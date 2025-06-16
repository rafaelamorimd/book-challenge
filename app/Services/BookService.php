<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\DTOs\BookDTO;
use App\Repository\BookRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly LogService $logService
    ) {
    }

    public function getAllBooks(): Collection
    {
        try {
            $this->logService->info('Buscando todos os livros');
            return $this->bookRepository->getAllWithRelations(['authors', 'subjects']);
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar todos os livros', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getPaginatedBooks(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        try {
            $this->logService->info('Buscando livros paginados', compact('filters', 'perPage'));
            return $this->bookRepository->getAllPaginate($filters, $perPage);
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar livros paginados', [
                'error' => $e->getMessage(),
                'filters' => $filters,
                'perPage' => $perPage
            ]);
            throw $e;
        }
    }

    public function createBook(BookDTO $dto): Model
    {
        try {
            $this->logService->info('Iniciando criação de livro', ['dto' => $dto->toArray()]);

            return DB::transaction(function () use ($dto) {
                $book = $this->bookRepository->create($dto->toArray());

                if ($dto->authors) {
                    $this->bookRepository->syncAuthors($book, $dto->authors);
                }

                if ($dto->subjects) {
                    $this->bookRepository->syncSubjects($book, $dto->subjects);
                }

                $this->logService->info('Livro criado com sucesso', [
                    'book_id' => $book->Codl,
                    'title' => $book->Titulo
                ]);

                return $book->load(['authors', 'subjects']);
            });
        } catch (\Exception $e) {
            $this->logService->error('Erro ao criar livro', [
                'error' => $e->getMessage(),
                'dto' => $dto->toArray()
            ]);
            throw $e;
        }
    }

    public function getBook(Book $book): Model
    {
        try {
            $this->logService->info('Buscando livro', ['book_id' => $book->Codl]);
            $book = $this->bookRepository->findOrFail($book->Codl);
            $this->logService->info('Livro encontrado com sucesso', ['book_id' => $book->Codl]);
            return $book->load(['authors', 'subjects']);
        } catch (\Exception $e) {
            $this->logService->error('Erro ao buscar livro', [
                'book_id' => $book->Codl,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateBook(Book $book, BookDTO $dto): Model
    {
        try {
            $this->logService->info('Iniciando atualização de livro', [
                'book_id' => $book->Codl,
                'dto' => $dto->toArray()
            ]);

            return DB::transaction(function () use ($book, $dto) {
                $oldData = $book->toArray();
                $book->update($dto->toArray());

                if ($dto->authors) {
                    $this->bookRepository->syncAuthors($book, $dto->authors);
                }

                if ($dto->subjects) {
                    $this->bookRepository->syncSubjects($book, $dto->subjects);
                }

                $this->logService->info('Livro atualizado com sucesso', [
                    'book_id' => $book->Codl,
                    'old_data' => $oldData,
                    'new_data' => $book->toArray()
                ]);

                return $book->load(['authors', 'subjects']);
            });
        } catch (\Exception $e) {
            $this->logService->error('Erro ao atualizar livro', [
                'book_id' => $book->Codl,
                'error' => $e->getMessage(),
                'dto' => $dto->toArray()
            ]);
            throw $e;
        }
    }

    public function deleteBook(Book $book): bool
    {
        try {
            $this->logService->info('Iniciando exclusão de livro', ['book_id' => $book->Codl]);
            $bookData = $book->toArray();
            $result = $book->delete();

            $this->logService->info('Livro deletado com sucesso', [
                'book_id' => $book->Codl,
                'book_data' => $bookData
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logService->error('Erro ao deletar livro', [
                'book_id' => $book->Codl,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
