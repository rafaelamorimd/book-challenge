<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Author;
use App\DTOs\AuthorDTO;
use App\Repository\AuthorRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AuthorService
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly LogService $logService
    ) {
    }

    public function getAllAuthors(): Collection
    {
        try {
            $this->logService->info('Buscando todos os autores');
            return $this->authorRepository->getAllWithRelations();
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar autores', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getPaginatedAuthors(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            $this->logService->info('Buscando autores paginados', compact('filters', 'perPage'));
            return $this->authorRepository->getAllPaginate($filters, $perPage);
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar autores paginados', [
                'error' => $e->getMessage(),
                'filters' => $filters,
                'perPage' => $perPage
            ]);
            throw $e;
        }
    }

    public function createAuthor(AuthorDTO $dto): Model
    {
        try {
            $this->logService->info('Iniciando criação de autor', ['dto' => $dto->toArray()]);

            return DB::transaction(function () use ($dto) {
                $author = $this->authorRepository->create($dto->toArray());

                $this->logService->info('Autor criado com sucesso', [
                    'author_id' => $author->CodAu,
                    'name' => $author->Nome
                ]);

                return $author->load('books');
            });
        } catch (\Exception $e) {
            $this->logService->error('Erro ao criar autor', [
                'error' => $e->getMessage(),
                'dto' => $dto->toArray()
            ]);
            throw $e;
        }
    }

    public function getAuthor(Author $author): Model
    {
        try {
            $this->logService->info('Buscando autor', ['author_id' => $author->CodAu]);
            $author = $this->authorRepository->findOrFail($author->CodAu);
            $this->logService->info('Autor encontrado com sucesso', ['author_id' => $author->CodAu]);
            return $author;
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar autor', [
                'author_id' => $author->CodAu,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateAuthor(Author $author, AuthorDTO $dto): Model
    {
        try {
            $this->logService->info('Iniciando atualização de autor', [
                'author_id' => $author->CodAu,
                'dto' => $dto->toArray()
            ]);

            return DB::transaction(function () use ($author, $dto) {
                $oldData = $author->toArray();
                $author->update($dto->toArray());

                $this->logService->info('Autor atualizado com sucesso', [
                    'author_id' => $author->CodAu,
                    'old_data' => $oldData,
                    'new_data' => $author->toArray()
                ]);

                return $author->load('books');
            });
        } catch (\Exception $e) {
            $this->logService->error('Erro ao atualizar autor', [
                'author_id' => $author->CodAu,
                'error' => $e->getMessage(),
                'dto' => $dto->toArray()
            ]);
            throw $e;
        }
    }

    public function deleteAuthor(Author $author): bool
    {
        try {
            $this->logService->info('Iniciando exclusão de autor', ['author_id' => $author->CodAu]);
            $authorData = $author->toArray();
            $result = $author->delete();

            $this->logService->info('Autor deletado com sucesso', [
                'author_id' => $author->CodAu,
                'author_data' => $authorData
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logService->error('Erro ao deletar autor', [
                'author_id' => $author->CodAu,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
