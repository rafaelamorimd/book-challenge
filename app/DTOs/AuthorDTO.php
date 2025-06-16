<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Author;

class AuthorDTO
{
    public function __construct(
        public readonly ?int $codAu = null,
        public readonly ?string $nome = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            nome: $data['nome'],
        );
    }

    public static function fromModel(Author $author): self
    {
        return new self(
            codAu: $author->CodAu,
            nome: $author->Nome,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'CodAu' => $this->codAu,
            'Nome' => $this->nome,
        ]);
    }
}
