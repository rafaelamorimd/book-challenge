<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Book;

class BookDTO
{
    public function __construct(
        public readonly ?int $codl = null,
        public readonly string $titulo = '',
        public readonly string $editora = '',
        public readonly int $edicao = 1,
        public readonly string $anoPublicacao = '',
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
            codl: $data['codl'] ?? null,
            titulo: $data['titulo'],
            editora: $data['editora'],
            edicao: (int) $data['edicao'],
            anoPublicacao: (string)$data['anoPublicacao'],
            valor: $valor,
            authors: $data['authors'] ?? null,
            subjects: $data['subjects'] ?? null,
        );
    }

    public static function fromModel(Book $book): self
    {
        $authors = [];
        $subjects = [];

        // Verifica se o relacionamento authors está carregado e é uma collection
        if ($book->relationLoaded('authors') && $book->authors instanceof \Illuminate\Support\Collection) {
            $authors = $book->authors->map(fn ($author) => [
                'CodAu' => $author->CodAu,
                'Nome' => $author->Nome,
            ])->toArray();
        }

        // Verifica se o relacionamento subjects está carregado e é uma collection
        if ($book->relationLoaded('subjects') && $book->subjects instanceof \Illuminate\Support\Collection) {
            $subjects = $book->subjects->map(fn ($subject) => [
                'CodAs' => $subject->CodAs,
                'Descricao' => $subject->Descricao,
            ])->toArray();
        }

        return new self(
            codl: $book->Codl,
            titulo: $book->Titulo,
            editora: $book->Editora,
            edicao: $book->Edicao,
            anoPublicacao: $book->AnoPublicacao,
            valor: number_format((float) $book->valor, 2, '.', ''),
            authors: $authors,
            subjects: $subjects,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'Codl' => $this->codl,
            'Titulo' => $this->titulo,
            'Editora' => $this->editora,
            'Edicao' => $this->edicao,
            'AnoPublicacao' => $this->anoPublicacao,
            'valor' => $this->valor,
            'authors' => $this->authors,
            'subjects' => $this->subjects,
        ]);
    }
}
