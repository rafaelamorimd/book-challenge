<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Author;

class AuthorDTO
{
    public function __construct(
        public readonly ?int $CodAu = null,
        public readonly ?string $Nome = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            Nome: $data['Nome'],
        );
    }

    public static function fromModel(Author $author): self
    {
        return new self(
            CodAu: $author->CodAu,
            Nome: $author->Nome,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'CodAu' => $this->CodAu,
            'Nome' => $this->Nome,
        ]);
    }
}
