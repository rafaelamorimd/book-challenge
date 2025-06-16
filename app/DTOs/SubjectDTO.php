<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Subject;

class SubjectDTO
{
    public function __construct(
        public readonly ?int $codAs = null,
        public readonly string $descricao = '',
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            codAs: $data['codAs'] ?? null,
            descricao: $data['descricao'],
        );
    }

    public static function fromModel(Subject $subject): self
    {
        return new self(
            codAs: $subject->CodAs,
            descricao: $subject->Descricao,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'CodAs' => $this->codAs,
            'Descricao' => $this->descricao,
        ]);
    }
}
