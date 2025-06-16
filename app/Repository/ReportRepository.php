<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Author;
use Illuminate\Support\Collection;

class ReportRepository
{
    public function __construct(
        private readonly Author $author
    ) {
    }

    public function getAuthorsWithBooksAndSubjects(): Collection
    {
        return $this->author
            ->with(['books.subjects'])
            ->get()
            ->map(function (Author $author) {
                return [
                    'authorId' => $author->CodAu,
                    'authorName' => $author->Nome,
                    'books' => $author->books->map(function ($book) {
                        return [
                            'bookId' => $book->Codl,
                            'bookTitle' => $book->Titulo,
                            'publisher' => $book->Editora,
                            'edition' => $book->Edicao,
                            'publicationYear' => $book->AnoPublicacao,
                            'amount' => $book->valor ? number_format((float) $book->valor, 2, ',', '.') : '0,00',
                            'subjects' => $book->subjects->pluck('Descricao'),
                        ];
                    }),
                ];
            });
    }
}
