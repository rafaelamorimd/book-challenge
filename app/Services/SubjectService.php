<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Subject;
use App\DTOs\SubjectDTO;
use App\Repository\SubjectRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubjectService
{
    public function __construct(
        private readonly SubjectRepository $subjectRepository,
        private readonly LogService $logService
    ) {
    }

    public function getAllSubjects(): Collection
    {
        try {
            $this->logService->info('Buscando todos os assuntos');
            return $this->subjectRepository->getAllWithRelations();
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar assuntos', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getPaginatedSubjects(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            $this->logService->info('Buscando assuntos paginados', compact('filters', 'perPage'));
            return $this->subjectRepository->getAllPaginate($filters, $perPage);
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar assuntos paginados', [
                'error' => $e->getMessage(),
                'filters' => $filters,
                'perPage' => $perPage
            ]);
            throw $e;
        }
    }

    public function createSubject(SubjectDTO $dto): Model
    {
        try {
            $this->logService->info('Iniciando criação de assunto', ['dto' => $dto->toArray()]);

            return DB::transaction(function () use ($dto) {
                $subject = $this->subjectRepository->create($dto->toArray());

                $this->logService->info('Assunto criado com sucesso', [
                    'subject_id' => $subject->CodAs,
                    'description' => $subject->Descricao
                ]);

                return $subject->load('books');
            });
        } catch (\Exception $e) {
            $this->logService->error('Erro ao criar assunto', [
                'error' => $e->getMessage(),
                'dto' => $dto->toArray()
            ]);
            throw $e;
        }
    }

    public function getSubject(Subject $subject): Model
    {
        try {
            $this->logService->info('Buscando assunto', ['subject_id' => $subject->CodAs]);
            $subject = $this->subjectRepository->findOrFail($subject->CodAs);
            $this->logService->info('Assunto encontrado com sucesso', ['subject_id' => $subject->CodAs]);
            return $subject->load('books');
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar assunto', [
                'subject_id' => $subject->CodAs,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateSubject(Subject $subject, SubjectDTO $dto): Model
    {
        try {
            $this->logService->info('Iniciando atualização de assunto', [
                'subject_id' => $subject->CodAs,
                'dto' => $dto->toArray()
            ]);

            return DB::transaction(function () use ($subject, $dto) {
                $oldData = $subject->toArray();
                $subject->update($dto->toArray());

                $this->logService->info('Assunto atualizado com sucesso', [
                    'subject_id' => $subject->CodAs,
                    'old_data' => $oldData,
                    'new_data' => $subject->toArray()
                ]);

                return $subject->load('books');
            });
        } catch (\Exception $e) {
            $this->logService->error('Erro ao atualizar assunto', [
                'subject_id' => $subject->CodAs,
                'error' => $e->getMessage(),
                'dto' => $dto->toArray()
            ]);
            throw $e;
        }
    }

    public function deleteSubject(Subject $subject): bool
    {
        try {
            $this->logService->info('Iniciando exclusão de assunto', ['subject_id' => $subject->CodAs]);
            $subjectData = $subject->toArray();
            $result = $subject->delete();

            $this->logService->info('Assunto deletado com sucesso', [
                'subject_id' => $subject->CodAs,
                'subject_data' => $subjectData
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logService->error('Erro ao deletar assunto', [
                'subject_id' => $subject->CodAs,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
