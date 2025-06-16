<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Author;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AuthorRepository
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

    public function create(array $data): Model
    {
        return Author::create($data);
    }

    public function count(): int
    {
        return $this->getBaseQuery()->count();
    }

    private function getBaseQuery(): Builder
    {
        return Author::query()->orderBy('CodAu');
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (empty($filters)) {
            return;
        }

        $searchableFields = ['Nome'];
        $exactMatchFields = ['CodAu'];

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

        if (isset($filters['nome'])) {
            $query->where('Nome', 'like', '%' . $filters['nome'] . '%');
        }
    }
}
