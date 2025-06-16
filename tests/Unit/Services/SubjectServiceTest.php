<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Subject;
use App\DTOs\SubjectDTO;
use App\Services\SubjectService;
use App\Services\LogService;
use App\Repository\SubjectRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;

class SubjectServiceTest extends TestCase
{
    private SubjectService $subjectService;
    private SubjectRepository|MockInterface $subjectRepository;
    private LogService|MockInterface $logService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subjectRepository = Mockery::mock(SubjectRepository::class);
        $this->logService = Mockery::mock(LogService::class);
        $this->subjectService = new SubjectService($this->subjectRepository, $this->logService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_subjects_successfully()
    {
        $expectedSubjects = new Collection([
            new Subject(['CodAs' => 1, 'Descricao' => 'Assunto 1']),
            new Subject(['CodAs' => 2, 'Descricao' => 'Assunto 2'])
        ]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando todos os assuntos');

        $this->subjectRepository
            ->shouldReceive('getAllWithRelations')
            ->once()
            ->andReturn($expectedSubjects);

        $result = $this->subjectService->getAllSubjects();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedSubjects, $result);
    }

    public function test_get_paginated_subjects_successfully()
    {
        $filters = ['search' => 'test'];
        $perPage = 15;
        $expectedPaginator = new LengthAwarePaginator(
            [new Subject(['CodAs' => 1, 'Descricao' => 'Assunto 1'])],
            1,
            $perPage
        );

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando assuntos paginados', compact('filters', 'perPage'));

        $this->subjectRepository
            ->shouldReceive('getAllPaginate')
            ->once()
            ->with($filters, $perPage)
            ->andReturn($expectedPaginator);

        $result = $this->subjectService->getPaginatedSubjects($filters, $perPage);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals($expectedPaginator, $result);
    }

    public function test_create_subject_successfully()
    {
        $subjectData = [
            'Descricao' => 'Novo Assunto'
        ];

        $dto = new SubjectDTO(Descricao: $subjectData['Descricao']);

        $subject = Mockery::mock(Subject::class);
        $subject->shouldReceive('getAttribute')->with('CodAs')->andReturn(1);
        $subject->shouldReceive('getAttribute')->with('Descricao')->andReturn($subjectData['Descricao']);
        $subject->shouldReceive('load')->with('books')->andReturnSelf();

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Iniciando criação de assunto',
                    'Assunto criado com sucesso'
                ]);
            });

        $this->logService
            ->shouldReceive('error')
            ->never();

        $this->subjectRepository
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andReturn($subject);

        $result = $this->subjectService->createSubject($dto);

        $this->assertInstanceOf(Subject::class, $result);
        $this->assertEquals(1, $result->CodAs);
        $this->assertEquals($subjectData['Descricao'], $result->Descricao);
    }

    public function test_create_subject_throws_exception()
    {
        $subjectData = [
            'Descricao' => 'Novo Assunto'
        ];

        $dto = new SubjectDTO(Descricao: $subjectData['Descricao']);
        $exception = new \Exception('Erro ao criar assunto');

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Iniciando criação de assunto', ['dto' => $dto->toArray()]);

        $this->logService
            ->shouldReceive('error')
            ->once()
            ->with('Erro ao criar assunto', [
                'error' => $exception->getMessage(),
                'dto' => $dto->toArray()
            ]);

        $this->subjectRepository
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andThrow($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao criar assunto');

        $this->subjectService->createSubject($dto);
    }

    public function test_get_subject_successfully()
    {
        $subject = Mockery::mock(Subject::class);
        $subject->shouldReceive('getAttribute')->with('CodAs')->andReturn(1);
        $subject->shouldReceive('getAttribute')->with('Descricao')->andReturn('Assunto Teste');
        $subject->shouldReceive('load')->with('books')->andReturnSelf();

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Buscando assunto',
                    'Assunto encontrado com sucesso'
                ]);
            });

        $this->subjectRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn($subject);

        $result = $this->subjectService->getSubject($subject);

        $this->assertInstanceOf(Subject::class, $result);
        $this->assertEquals(1, $result->CodAs);
    }

    public function test_update_subject_successfully()
    {
        $subject = Mockery::mock(Subject::class);
        $subject->shouldReceive('getAttribute')->with('CodAs')->andReturn(1);
        $subject->shouldReceive('getAttribute')->with('Descricao')->andReturn('Assunto Antigo');
        $subject->shouldReceive('toArray')->andReturn(['CodAs' => 1, 'Descricao' => 'Assunto Antigo']);
        $subject->shouldReceive('load')->with('books')->andReturnSelf();

        $updateData = [
            'Descricao' => 'Assunto Atualizado'
        ];

        $dto = new SubjectDTO(Descricao: $updateData['Descricao']);

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Iniciando atualização de assunto',
                    'Assunto atualizado com sucesso'
                ]);
            });

        $this->app->instance('db', Mockery::mock('db', function ($mock) {
            $mock->shouldReceive('transaction')
                ->once()
                ->andReturnUsing(function ($callback) {
                    return $callback();
                });
        }));

        $subject->shouldReceive('update')
            ->once()
            ->with($dto->toArray())
            ->andReturn(true);

        $result = $this->subjectService->updateSubject($subject, $dto);

        $this->assertInstanceOf(Subject::class, $result);
        $this->assertEquals(1, $result->CodAs);
    }

    public function test_delete_subject_successfully()
    {
        $subject = Mockery::mock(Subject::class);
        $subject->shouldReceive('getAttribute')->with('CodAs')->andReturn(1);
        $subject->shouldReceive('getAttribute')->with('Descricao')->andReturn('Assunto Teste');
        $subject->shouldReceive('toArray')->andReturn(['CodAs' => 1, 'Descricao' => 'Assunto Teste']);

        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message, $context) {
                return in_array($message, [
                    'Iniciando exclusão de assunto',
                    'Assunto deletado com sucesso'
                ]);
            });

        $subject->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->subjectService->deleteSubject($subject);

        $this->assertTrue($result);
    }

    public function test_get_subject_throws_exception_when_not_found()
    {
        $subject = new Subject(['CodAs' => 999]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Buscando assunto', ['subject_id' => $subject->CodAs]);

        $this->logService
            ->shouldReceive('error')
            ->once();

        $this->subjectRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($subject->CodAs)
            ->andThrow(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        $this->subjectService->getSubject($subject);
    }
}
