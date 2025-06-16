<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Book;
use App\DTOs\BookDTO;
use App\Services\BookService;
use App\Services\LogService;
use App\Repository\BookRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;

class BookServiceTest extends TestCase
{
    private BookService $bookService;
    private BookRepository|MockInterface $bookRepository;
    private LogService|MockInterface $logService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = Mockery::mock(BookRepository::class);
        $this->logService = Mockery::mock(LogService::class);
        $this->bookService = new BookService($this->bookRepository, $this->logService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createBookMock(array $attributes = []): Book|MockInterface
    {
        $defaultAttributes = [
            'Codl' => 1,
            'Titulo' => 'Livro Teste',
            'Editora' => 'Editora Teste',
            'Edicao' => 1,
            'AnoPublicacao' => '2024',
            'valor' => '10.00'
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        $bookMock = Mockery::mock(Book::class)->makePartial();

        $authorsCollection = collect([
            (object)['CodAu' => 1, 'Nome' => 'Autor 1'],
            (object)['CodAu' => 2, 'Nome' => 'Autor 2']
        ]);
        $subjectsCollection = collect([
            (object)['CodAs' => 1, 'Descricao' => 'Assunto 1'],
            (object)['CodAs' => 2, 'Descricao' => 'Assunto 2']
        ]);

        $bookMock->shouldReceive('getAttribute')
            ->andReturnUsing(function ($key) use ($bookMock, $authorsCollection, $subjectsCollection) {
                if ($key === 'authors') {
                    return $authorsCollection;
                }
                if ($key === 'subjects') {
                    return $subjectsCollection;
                }
                return $bookMock->$key ?? null;
            });

        $bookMock->shouldReceive('setAttribute')
            ->andReturnUsing(function ($key, $value) use ($bookMock) {
                $bookMock->$key = $value;
                return $bookMock;
            });

        foreach ($attributes as $key => $value) {
            $bookMock->$key = $value;
        }

        $bookMock->shouldReceive('getRelation')
            ->with('authors')
            ->andReturn($authorsCollection);

        $bookMock->shouldReceive('getRelation')
            ->with('subjects')
            ->andReturn($subjectsCollection);

        $bookMock->shouldReceive('authors')
            ->andReturn($authorsCollection);

        $bookMock->shouldReceive('subjects')
            ->andReturn($subjectsCollection);

        $bookMock->shouldReceive('relationLoaded')
            ->with('authors')
            ->andReturn(true);

        $bookMock->shouldReceive('relationLoaded')
            ->with('subjects')
            ->andReturn(true);

        $bookMock->authors = $authorsCollection;
        $bookMock->subjects = $subjectsCollection;

        return $bookMock;
    }

    private function createBookData(array $attributes = []): array
    {
        $defaultData = [
            'codl' => 1,
            'titulo' => 'Novo Livro',
            'editora' => 'Editora Teste',
            'edicao' => 1,
            'anoPublicacao' => (string)2024,
            'valor' => '10,00',
            'authors' => [1, 2],
            'subjects' => [1, 2]
        ];

        return array_merge($defaultData, $attributes);
    }

    private function mockDbTransaction(callable $callback): void
    {
        $this->app->instance('db', Mockery::mock('db', function ($mock) use ($callback) {
            $mock->shouldReceive('transaction')
                ->once()
                ->andReturnUsing($callback);
        }));
    }

    public function test_get_all_books_successfully(): void
    {
        $expectedBooks = new Collection([
            $this->createBookMock(['Codl' => 1, 'Titulo' => 'Livro 1']),
            $this->createBookMock(['Codl' => 2, 'Titulo' => 'Livro 2'])
        ]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando todos os livros');

        $this->bookRepository
            ->shouldReceive('getAllWithRelations')
            ->once()
            ->with(['authors', 'subjects'])
            ->andReturn($expectedBooks);

        $result = $this->bookService->getAllBooks();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedBooks, $result);
    }

    public function test_get_paginated_books_successfully(): void
    {
        $filters = ['search' => 'test'];
        $perPage = 15;
        $expectedPaginator = new LengthAwarePaginator(
            [$this->createBookMock(['Codl' => 1, 'Titulo' => 'Livro 1'])],
            1,
            $perPage
        );

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando livros paginados', compact('filters', 'perPage'));

        $this->bookRepository
            ->shouldReceive('getAllPaginate')
            ->once()
            ->with($filters, $perPage)
            ->andReturn($expectedPaginator);

        $result = $this->bookService->getPaginatedBooks($filters, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($expectedPaginator, $result);
    }

    public function test_create_book_successfully(): void
    {
        $bookData = $this->createBookData();
        $dto = BookDTO::fromRequest($bookData);
        $bookMock = $this->createBookMock();

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message) {
                return in_array($message, ['Iniciando criação de livro', 'Livro criado com sucesso']);
            });

        $this->logService
            ->shouldReceive('error')
            ->never();

        $this->bookRepository
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andReturn($bookMock);

        $this->bookRepository
            ->shouldReceive('syncAuthors')
            ->once()
            ->with(Mockery::type(Book::class), $bookData['authors']);

        $this->bookRepository
            ->shouldReceive('syncSubjects')
            ->once()
            ->with(Mockery::type(Book::class), $bookData['subjects']);

        $this->app->instance('db', Mockery::mock('db', function ($mock) use ($bookMock) {
            $mock->shouldReceive('transaction')
                ->once()
                ->andReturnUsing(function ($callback) use ($bookMock) {
                    return $callback();
                });
        }));

        $bookMock->shouldReceive('load')
            ->once()
            ->with(['authors', 'subjects'])
            ->andReturnSelf();

        $result = $this->bookService->createBook($dto);

        $this->assertInstanceOf(Book::class, $result);
    }

    public function test_get_book_successfully(): void
    {
        $bookMock = $this->createBookMock();

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message) {
                return in_array($message, ['Buscando livro', 'Livro encontrado com sucesso']);
            });

        $this->bookRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($bookMock->Codl)
            ->andReturn($bookMock);

        $bookMock->shouldReceive('load')
            ->once()
            ->with(['authors', 'subjects'])
            ->andReturnSelf();

        $result = $this->bookService->getBook($bookMock);

        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals($bookMock->Codl, $result->Codl);
    }

    public function test_update_book_successfully(): void
    {
        $bookMock = $this->createBookMock();
        $updateData = $this->createBookData([
            'titulo' => 'Livro Atualizado',
            'editora' => 'Editora Atualizada',
            'edicao' => 2
        ]);

        $dto = BookDTO::fromRequest($updateData);

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message) {
                return in_array($message, ['Iniciando atualização de livro', 'Livro atualizado com sucesso']);
            });

        $this->bookRepository
            ->shouldReceive('syncAuthors')
            ->once()
            ->with($bookMock, $updateData['authors']);

        $this->bookRepository
            ->shouldReceive('syncSubjects')
            ->once()
            ->with($bookMock, $updateData['subjects']);

        $this->app->instance('db', Mockery::mock('db', function ($mock) use ($bookMock) {
            $mock->shouldReceive('transaction')
                ->once()
                ->andReturnUsing(function ($callback) use ($bookMock) {
                    return $callback();
                });
        }));

        $bookMock->shouldReceive('update')
            ->once()
            ->with($dto->toArray())
            ->andReturnUsing(function ($data) use ($bookMock) {
                foreach ($data as $key => $value) {
                    $bookMock->setAttribute($key, $value);
                }
                return $bookMock;
            });

        $bookMock->shouldReceive('load')
            ->once()
            ->with(['authors', 'subjects'])
            ->andReturnSelf();

        $result = $this->bookService->updateBook($bookMock, $dto);

        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals($updateData['titulo'], $result->Titulo);
        $this->assertEquals($updateData['editora'], $result->Editora);
        $this->assertEquals($updateData['edicao'], $result->Edicao);
    }

    public function test_delete_book_successfully(): void
    {
        $bookMock = $this->createBookMock();
        $bookData = $bookMock->toArray();

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Iniciando exclusão de livro', ['book_id' => $bookMock->Codl]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Livro deletado com sucesso', [
                'book_id' => $bookMock->Codl,
                'book_data' => $bookData
            ]);

        $this->logService
            ->shouldReceive('error')
            ->never();

        $bookMock->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->bookService->deleteBook($bookMock);

        $this->assertTrue($result);
    }

    public function test_get_book_throws_exception_when_not_found(): void
    {
        $book = new Book(['Codl' => 999]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando livro', ['book_id' => $book->Codl]);

        $this->logService
            ->shouldReceive('error')
            ->once();

        $this->bookRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($book->Codl)
            ->andThrow(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        $this->bookService->getBook($book);
    }

    public function test_create_book_throws_exception_during_transaction(): void
    {
        $bookData = $this->createBookData();
        $dto = BookDTO::fromRequest($bookData);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context = []) {
                return $message === 'Iniciando criação de livro';
            });

        $this->logService
            ->shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Erro ao criar livro' && is_array($context);
            });

        $this->app->instance('db', Mockery::mock('db', function ($mock) {
            $mock->shouldReceive('transaction')
                ->once()
                ->andThrow(new \Exception('Erro na transação'));
        }));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro na transação');

        $this->bookService->createBook($dto);
    }

    public function test_update_book_throws_exception_during_transaction(): void
    {
        $bookMock = $this->createBookMock();
        $updateData = $this->createBookData([
            'Titulo' => 'Livro Atualizado'
        ]);
        $dto = BookDTO::fromRequest($updateData);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context = []) {
                return $message === 'Iniciando atualização de livro';
            });

        $this->logService
            ->shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Erro ao atualizar livro' && is_array($context);
            });

        $this->app->instance('db', Mockery::mock('db', function ($mock) {
            $mock->shouldReceive('transaction')
                ->once()
                ->andThrow(new \Exception('Erro na transação'));
        }));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro na transação');

        $this->bookService->updateBook($bookMock, $dto);
    }

    public function test_delete_book_returns_false_when_fails(): void
    {
        $bookMock = $this->createBookMock();

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context = []) {
                return in_array($message, [
                    'Iniciando exclusão de livro',
                    'Livro deletado com sucesso'
                ]);
            });

        $this->logService
            ->shouldReceive('error')
            ->never();

        $bookMock->shouldReceive('delete')
            ->once()
            ->andReturn(false);

        $bookMock->shouldReceive('toArray')
            ->once()
            ->andReturn(['Codl' => $bookMock->Codl, 'Titulo' => $bookMock->Titulo]);

        $result = $this->bookService->deleteBook($bookMock);

        $this->assertFalse($result);
    }

    public function test_get_paginated_books_with_different_filters(): void
    {
        $testCases = [
            [
                'filters' => ['search' => 'test', 'editora' => 'Editora A'],
                'perPage' => 10,
                'expectedCount' => 1
            ],
            [
                'filters' => ['ano' => '2024'],
                'perPage' => 20,
                'expectedCount' => 2
            ],
            [
                'filters' => ['valor_min' => '10.00', 'valor_max' => '50.00'],
                'perPage' => 15,
                'expectedCount' => 3
            ]
        ];

        foreach ($testCases as $case) {
            $expectedBooks = new Collection(
                array_fill(0, $case['expectedCount'], $this->createBookMock())
            );

            $expectedPaginator = new LengthAwarePaginator(
                $expectedBooks,
                $case['expectedCount'],
                $case['perPage']
            );

            $this->logService
                ->shouldReceive('info')
                ->once()
                ->with('Buscando livros paginados', [
                    'filters' => $case['filters'],
                    'perPage' => $case['perPage']
                ]);

            $this->bookRepository
                ->shouldReceive('getAllPaginate')
                ->once()
                ->with($case['filters'], $case['perPage'])
                ->andReturn($expectedPaginator);

            $result = $this->bookService->getPaginatedBooks($case['filters'], $case['perPage']);

            $this->assertInstanceOf(LengthAwarePaginator::class, $result);
            $this->assertEquals($case['expectedCount'], $result->count());
            $this->assertEquals($case['perPage'], $result->perPage());
        }
    }

    public function test_get_book_with_specific_relations(): void
    {
        $bookMock = $this->createBookMock();
        $relations = ['authors', 'subjects'];

        $bookMock->shouldReceive('authors')
            ->andReturn(Mockery::mock('authors'));
        $bookMock->shouldReceive('subjects')
            ->andReturn(Mockery::mock('subjects'));

        $bookMock->shouldReceive('getRelation')
            ->andReturnUsing(function ($relation) use ($relations) {
                if (!in_array($relation, $relations)) {
                    throw new \Illuminate\Database\Eloquent\RelationNotFoundException();
                }
                return Mockery::mock($relation);
            });

        $bookMock->shouldReceive('relationLoaded')
            ->andReturnUsing(function ($relation) use ($relations) {
                return in_array($relation, $relations);
            });

        $bookMock->shouldReceive('getRelations')
            ->andReturnUsing(function () use ($relations) {
                $relationsArray = [];
                foreach ($relations as $relation) {
                    $relationsArray[$relation] = Mockery::mock($relation);
                }
                return $relationsArray;
            });

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context = []) {
                return in_array($message, ['Buscando livro', 'Livro encontrado com sucesso']);
            });

        $this->logService
            ->shouldReceive('error')
            ->zeroOrMoreTimes()
            ->withArgs(function ($message, $context) {
                return $message === 'Erro ao buscar livro' && is_array($context);
            });

        $this->bookRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($bookMock->Codl)
            ->andReturn($bookMock);

        $bookMock->shouldReceive('load')
            ->once()
            ->with($relations)
            ->andReturnUsing(function ($relations) use ($bookMock) {
                foreach ($relations as $relation) {
                    $bookMock->shouldReceive('getRelation')
                        ->with($relation)
                        ->andReturn(Mockery::mock($relation));
                }
                return $bookMock;
            });

        $result = $this->bookService->getBook($bookMock, $relations);

        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals($bookMock->Codl, $result->Codl);
        $this->assertTrue($result->relationLoaded('authors'));
        $this->assertTrue($result->relationLoaded('subjects'));
    }

    public function test_create_book_with_dto_from_model(): void
    {
        $bookMock = $this->createBookMock();
        $dto = BookDTO::fromModel($bookMock);

        $this->assertEquals($bookMock->Codl, $dto->codl);
        $this->assertEquals($bookMock->Titulo, $dto->titulo);
        $this->assertEquals($bookMock->Editora, $dto->editora);
        $this->assertEquals($bookMock->Edicao, $dto->edicao);
        $this->assertEquals($bookMock->AnoPublicacao, $dto->anoPublicacao);
        $this->assertEquals(number_format((float) $bookMock->valor, 2, '.', ''), $dto->valor);

        $this->assertIsArray($dto->authors);
        $this->assertCount(2, $dto->authors);
        $this->assertEquals(1, $dto->authors[0]['CodAu']);
        $this->assertEquals('Autor 1', $dto->authors[0]['Nome']);
        $this->assertEquals(2, $dto->authors[1]['CodAu']);
        $this->assertEquals('Autor 2', $dto->authors[1]['Nome']);

        $this->assertIsArray($dto->subjects);
        $this->assertCount(2, $dto->subjects);
        $this->assertEquals(1, $dto->subjects[0]['CodAs']);
        $this->assertEquals('Assunto 1', $dto->subjects[0]['Descricao']);
        $this->assertEquals(2, $dto->subjects[1]['CodAs']);
        $this->assertEquals('Assunto 2', $dto->subjects[1]['Descricao']);
    }

    public function test_create_book_with_dto_from_request(): void
    {
        $bookData = $this->createBookData([
            'valor' => '15,50'
        ]);
        $dto = BookDTO::fromRequest($bookData);

        $this->assertEquals($bookData['codl'], $dto->codl);
        $this->assertEquals($bookData['titulo'], $dto->titulo);
        $this->assertEquals($bookData['editora'], $dto->editora);
        $this->assertEquals($bookData['edicao'], $dto->edicao);
        $this->assertEquals($bookData['anoPublicacao'], $dto->anoPublicacao);
        $this->assertEquals('15.50', $dto->valor);
        $this->assertEquals($bookData['authors'], $dto->authors);
        $this->assertEquals($bookData['subjects'], $dto->subjects);
    }

    public function test_create_book_with_dto_to_array(): void
    {
        $bookData = $this->createBookData();
        $dto = BookDTO::fromRequest($bookData);
        $array = $dto->toArray();

        $this->assertArrayHasKey('Codl', $array);
        $this->assertArrayHasKey('Titulo', $array);
        $this->assertArrayHasKey('Editora', $array);
        $this->assertArrayHasKey('Edicao', $array);
        $this->assertArrayHasKey('AnoPublicacao', $array);
        $this->assertArrayHasKey('valor', $array);
        $this->assertArrayHasKey('authors', $array);
        $this->assertArrayHasKey('subjects', $array);
        $this->assertEquals($bookData['codl'], $array['Codl']);
        $this->assertEquals($bookData['titulo'], $array['Titulo']);
        $this->assertEquals($bookData['editora'], $array['Editora']);
        $this->assertEquals($bookData['edicao'], $array['Edicao']);
        $this->assertEquals($bookData['anoPublicacao'], $array['AnoPublicacao']);
        $this->assertEquals('10.00', $array['valor']);
    }

    public function test_create_book_with_invalid_data(): void
    {
        $invalidData = [
            'titulo' => '',
            'editora' => 'Editora Teste',
            'edicao' => -1,
            'anoPublicacao' => '2025',
            'valor' => '-10,00',
            'authors' => [],
            'subjects' => []
        ];

        $dto = BookDTO::fromRequest($invalidData);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context = []) {
                return $message === 'Iniciando criação de livro';
            });

        $this->logService
            ->shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Erro ao criar livro' && is_array($context);
            });

        $this->bookRepository
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \InvalidArgumentException('Dados inválidos'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dados inválidos');

        $this->bookService->createBook($dto);
    }
}
