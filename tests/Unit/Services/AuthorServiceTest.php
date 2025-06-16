<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Author;
use App\DTOs\AuthorDTO;
use App\Services\AuthorService;
use App\Services\LogService;
use App\Repository\AuthorRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;

class AuthorServiceTest extends TestCase
{
    private AuthorService $authorService;
    private AuthorRepository|MockInterface $authorRepository;
    private LogService|MockInterface $logService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorRepository = Mockery::mock(AuthorRepository::class);
        $this->logService = Mockery::mock(LogService::class);
        $this->authorService = new AuthorService($this->authorRepository, $this->logService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_authors_successfully(): void
    {
        // Arrange
        $expectedAuthors = new Collection([
            new Author(['CodAu' => 1, 'Nome' => 'Autor 1']),
            new Author(['CodAu' => 2, 'Nome' => 'Autor 2'])
        ]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando todos os autores');

        $this->authorRepository
            ->shouldReceive('getAllWithRelations')
            ->once()
            ->andReturn($expectedAuthors);

        // Act
        $result = $this->authorService->getAllAuthors();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedAuthors, $result);
    }

    public function test_get_paginated_authors_successfully(): void
    {
        // Arrange
        $filters = ['search' => 'test'];
        $perPage = 15;
        $expectedPaginator = new LengthAwarePaginator(
            [new Author(['CodAu' => 1, 'Nome' => 'Autor 1'])],
            1,
            $perPage
        );

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando autores paginados', compact('filters', 'perPage'));

        $this->authorRepository
            ->shouldReceive('getAllPaginate')
            ->once()
            ->with($filters, $perPage)
            ->andReturn($expectedPaginator);

        // Act
        $result = $this->authorService->getPaginatedAuthors($filters, $perPage);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($expectedPaginator, $result);
    }

    public function test_create_author_successfully(): void
    {
        // Arrange
        $authorData = [
            'Nome' => 'Novo Autor'
        ];

        $dto = new AuthorDTO(Nome: $authorData['Nome']);

        $author = Mockery::mock(Author::class);
        $author->shouldReceive('getAttribute')->with('CodAu')->andReturn(1);
        $author->shouldReceive('getAttribute')->with('Nome')->andReturn($authorData['Nome']);
        $author->shouldReceive('load')->with('books')->andReturnSelf();

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Iniciando criação de autor',
                    'Autor criado com sucesso'
                ]);
            });

        $this->logService
            ->shouldReceive('error')
            ->never();

        $this->authorRepository
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andReturn($author);

        // Act
        $result = $this->authorService->createAuthor($dto);

        // Assert
        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals(1, $result->CodAu);
        $this->assertEquals($authorData['Nome'], $result->Nome);
    }

    public function test_create_author_throws_exception(): void
    {
        // Arrange
        $authorData = [
            'Nome' => 'Novo Autor'
        ];

        $dto = new AuthorDTO(Nome: $authorData['Nome']);
        $exception = new \Exception('Erro ao criar autor');

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Iniciando criação de autor', ['dto' => $dto->toArray()]);

        $this->logService
            ->shouldReceive('error')
            ->once()
            ->with('Erro ao criar autor', [
                'error' => $exception->getMessage(),
                'dto' => $dto->toArray()
            ]);

        $this->authorRepository
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andThrow($exception);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao criar autor');

        // Act
        $this->authorService->createAuthor($dto);
    }

    public function test_get_author_successfully()
    {
        $author = Mockery::mock(Author::class);
        $author->shouldReceive('getAttribute')->with('CodAu')->andReturn(1);
        $author->shouldReceive('getAttribute')->with('Nome')->andReturn('Autor Teste');
        $author->shouldReceive('load')->with('books')->andReturnSelf();

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Buscando autor',
                    'Autor encontrado com sucesso'
                ]);
            });

        $this->authorRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn($author);

        $result = $this->authorService->getAuthor($author);

        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals(1, $result->CodAu);
    }

    public function test_update_author_successfully()
    {
        $author = Mockery::mock(Author::class);
        $author->shouldReceive('getAttribute')->with('CodAu')->andReturn(1);
        $author->shouldReceive('getAttribute')->with('Nome')->andReturn('Autor Antigo');
        $author->shouldReceive('toArray')->andReturn(['CodAu' => 1, 'Nome' => 'Autor Antigo']);
        $author->shouldReceive('load')->with('books')->andReturnSelf();

        $updateData = [
            'Nome' => 'Autor Atualizado'
        ];

        $dto = new AuthorDTO(Nome: $updateData['Nome']);

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Iniciando atualização de autor',
                    'Autor atualizado com sucesso'
                ]);
            });

        $this->app->instance('db', Mockery::mock('db', function ($mock) {
            $mock->shouldReceive('transaction')
                ->once()
                ->andReturnUsing(function ($callback) {
                    return $callback();
                });
        }));

        $author->shouldReceive('update')
            ->once()
            ->with($dto->toArray())
            ->andReturn(true);

        $result = $this->authorService->updateAuthor($author, $dto);

        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals(1, $result->CodAu);
    }

    public function test_delete_author_successfully()
    {
        $author = Mockery::mock(Author::class);
        $author->shouldReceive('getAttribute')->with('CodAu')->andReturn(1);
        $author->shouldReceive('getAttribute')->with('Nome')->andReturn('Autor Teste');
        $author->shouldReceive('toArray')->andReturn(['CodAu' => 1, 'Nome' => 'Autor Teste']);

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Iniciando exclusão de autor',
                    'Autor deletado com sucesso'
                ]);
            });

        $author->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->authorService->deleteAuthor($author);

        $this->assertTrue($result);
    }

    public function test_get_author_throws_exception_when_not_found(): void
    {
        // Arrange
        $author = new Author(['CodAu' => 999]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando autor', ['author_id' => $author->CodAu]);

        $this->logService
            ->shouldReceive('error')
            ->once();

        $this->authorRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($author->CodAu)
            ->andThrow(new ModelNotFoundException());

        // Assert
        $this->expectException(ModelNotFoundException::class);

        // Act
        $this->authorService->getAuthor($author);
    }
}
