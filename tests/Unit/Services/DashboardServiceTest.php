<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\DashboardService;
use App\Models\Book;
use App\Models\Author;
use App\Models\Subject;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Mockery\MockInterface;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\SubjectRepository;
use App\Services\LogService;
use Illuminate\Support\Facades\Schema;
use Exception;

class DashboardServiceTest extends TestCase
{
    private DashboardService $dashboardService;
    private BookRepository|MockInterface $bookRepository;
    private AuthorRepository|MockInterface $authorRepository;
    private SubjectRepository|MockInterface $subjectRepository;
    private LogService|MockInterface $logService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = Mockery::mock(BookRepository::class);
        $this->authorRepository = Mockery::mock(AuthorRepository::class);
        $this->subjectRepository = Mockery::mock(SubjectRepository::class);
        $this->logService = Mockery::mock(LogService::class);

        $this->dashboardService = new DashboardService(
            $this->bookRepository,
            $this->authorRepository,
            $this->subjectRepository,
            $this->logService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @dataProvider dashboardDataProvider
     */
    public function test_get_dashboard_data_returns_correct_data(
        int $expectedBooks,
        int $expectedAuthors,
        int $expectedSubjects,
        array $expectedLastBooks,
        array $expectedBooksBySubject
    ): void {
        // Arrange
        $this->bookRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn($expectedBooks);

        $this->bookRepository
            ->shouldReceive('getAllWithRelations')
            ->with(['authors'])
            ->once()
            ->andReturn(new EloquentCollection($expectedLastBooks));

        $this->authorRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn($expectedAuthors);

        $this->subjectRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn($expectedSubjects);

        $this->subjectRepository
            ->shouldReceive('getTopSubjectsByBookCount')
            ->with(10)
            ->once()
            ->andReturn(new EloquentCollection($expectedBooksBySubject));

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando dados do dashboard');

        // Act
        $result = $this->dashboardService->getDashboardData();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($expectedBooks, $result['books']);
        $this->assertEquals($expectedAuthors, $result['authors']);
        $this->assertEquals($expectedSubjects, $result['subjects']);
        $this->assertInstanceOf(EloquentCollection::class, $result['lastBooks']);
        $this->assertCount(count($expectedLastBooks), $result['lastBooks']);
        $this->assertInstanceOf(EloquentCollection::class, $result['booksBySubject']);
        $this->assertCount(count($expectedBooksBySubject), $result['booksBySubject']);
    }

    public function test_get_dashboard_data_handles_repository_exception(): void
    {
        // Arrange
        $this->bookRepository
            ->shouldReceive('count')
            ->once()
            ->andThrow(new Exception('Erro ao buscar livros'));

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando dados do dashboard');

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao buscar livros');

        // Act
        $this->dashboardService->getDashboardData();
    }

    public function test_get_dashboard_data_with_empty_collections(): void
    {
        // Arrange
        $this->bookRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn(0);

        $this->bookRepository
            ->shouldReceive('getAllWithRelations')
            ->with(['authors'])
            ->once()
            ->andReturn(new EloquentCollection([]));

        $this->authorRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn(0);

        $this->subjectRepository
            ->shouldReceive('count')
            ->once()
            ->andReturn(0);

        $this->subjectRepository
            ->shouldReceive('getTopSubjectsByBookCount')
            ->with(10)
            ->once()
            ->andReturn(new EloquentCollection([]));

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando dados do dashboard');

        // Act
        $result = $this->dashboardService->getDashboardData();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(0, $result['books']);
        $this->assertEquals(0, $result['authors']);
        $this->assertEquals(0, $result['subjects']);
        $this->assertInstanceOf(EloquentCollection::class, $result['lastBooks']);
        $this->assertCount(0, $result['lastBooks']);
        $this->assertInstanceOf(EloquentCollection::class, $result['booksBySubject']);
        $this->assertCount(0, $result['booksBySubject']);
    }

    public static function dashboardDataProvider(): array
    {
        return [
            'cenario_com_dados' => [
                'expectedBooks' => 10,
                'expectedAuthors' => 5,
                'expectedSubjects' => 8,
                'expectedLastBooks' => [
                    new Book(['Codl' => 1, 'Titulo' => 'Livro 1']),
                    new Book(['Codl' => 2, 'Titulo' => 'Livro 2']),
                ],
                'expectedBooksBySubject' => [
                    (new Subject(['Descricao' => 'Assunto 1']))->setRelation('books_count', 5),
                    (new Subject(['Descricao' => 'Assunto 2']))->setRelation('books_count', 3),
                ],
            ],
            'cenario_com_um_registro' => [
                'expectedBooks' => 1,
                'expectedAuthors' => 1,
                'expectedSubjects' => 1,
                'expectedLastBooks' => [
                    new Book(['Codl' => 1, 'Titulo' => 'Livro Único']),
                ],
                'expectedBooksBySubject' => [
                    (new Subject(['Descricao' => 'Assunto Único']))->setRelation('books_count', 1),
                ],
            ],
        ];
    }
}
