<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Book;

class BookDTO
{
    public function __construct(
        public readonly ?int $Codl = null,
        public readonly string $Titulo = '',
        public readonly string $Editora = '',
        public readonly int $Edicao = 1,
        public readonly string $AnoPublicacao = '',
        public readonly string $valor = '0.00',
        public readonly ?array $authors = null,
        public readonly ?array $subjects = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        $valor = $data['valor'];
        if (is_string($valor)) {
            $valor = number_format((float) str_replace(',', '.', $valor), 2, '.', '');
        } else {
            $valor = number_format((float) $valor, 2, '.', '');
        }

        return new self(
            Codl: $data['Codl'] ?? null,
            Titulo: $data['Titulo'],
            Editora: $data['Editora'],
            Edicao: (int) $data['Edicao'],
            AnoPublicacao: (string)$data['AnoPublicacao'],
            valor: $valor,
            authors: $data['authors'] ?? null,
            subjects: $data['subjects'] ?? null,
        );
    }

    public static function fromModel(Book $book): self
    {
        return new self(
            Codl: $book->Codl,
            Titulo: $book->Titulo,
            Editora: $book->Editora,
            Edicao: $book->Edicao,
            AnoPublicacao: $book->AnoPublicacao,
            valor: number_format((float) $book->valor, 2, '.', ''),
            authors: $book->authors->map(fn ($author) => [
                'CodAu' => $author->CodAu,
                'Nome' => $author->Nome,
            ])->toArray(),
            subjects: $book->subjects->map(fn ($subject) => [
                'CodAs' => $subject->CodAs,
                'Descricao' => $subject->Descricao,
            ])->toArray(),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'Codl' => $this->Codl,
            'Titulo' => $this->Titulo,
            'Editora' => $this->Editora,
            'Edicao' => $this->Edicao,
            'AnoPublicacao' => $this->AnoPublicacao,
            'valor' => $this->valor,
            'authors' => $this->authors,
            'subjects' => $this->subjects,
        ]);
    }
}
