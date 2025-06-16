<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Subject;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository
{
    public function getAllPaginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->getBaseQuery();
        $this->applyFilters($query, $filters);
        return $query->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->getBaseQuery()->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->getBaseQuery()->findOrFail($id);
    }

    public function getAllWithRelations(array $relations = []): Collection
    {
        return $this->getBaseQuery()->get();
    }

    public function count(): int
    {
        return $this->getBaseQuery()->count();
    }

    public function getTopSubjectsByBookCount(int $limit = 10): Collection
    {
        return $this->getBaseQuery()
            ->withCount('books')
            ->orderByDesc('books_count')
            ->take($limit)
            ->get(['Descricao', 'books_count']);
    }

    public function create(array $data): Model
    {
        return Subject::create($data);
    }

    private function getBaseQuery(): Builder
    {
        return Subject::query();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (empty($filters)) {
            return;
        }

        $searchableFields = ['Descricao'];
        $exactMatchFields = ['CodAs'];

        if (isset($filters['search'])) {
            $query->where(function ($query) use ($filters, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $query->orWhere($field, 'like', '%' . $filters['search'] . '%');
                }
            });
        }

        foreach ($exactMatchFields as $field) {
            if (isset($filters[strtolower($field)])) {
                $query->where($field, $filters[strtolower($field)]);
            }
        }

        if (isset($filters['descricao'])) {
            $query->where('Descricao', 'like', '%' . $filters['descricao'] . '%');
        }
    }
}
