<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\SubjectRepository;
use App\Services\LogService;

class DashboardService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly AuthorRepository $authorRepository,
        private readonly SubjectRepository $subjectRepository,
        private readonly LogService $logService
    ) {
    }

    public function getDashboardData(): array
    {
        $this->logService->info('Buscando dados do dashboard');

        $data = [
            'books' => $this->bookRepository->count(),
            'authors' => $this->authorRepository->count(),
            'subjects' => $this->subjectRepository->count(),
            'lastBooks' => $this->getLastBooks(5),
            'booksBySubject' => $this->getBooksBySubject(10),
        ];

        return $data;
    }

    private function getLastBooks(int $limit = 5): Collection
    {
        return $this->bookRepository->getAllWithRelations(['authors'])
            ->sortByDesc('Codl')
            ->take($limit);
    }

    private function getBooksBySubject(int $limit = 10): Collection
    {
        return $this->subjectRepository->getTopSubjectsByBookCount($limit);
    }
}
