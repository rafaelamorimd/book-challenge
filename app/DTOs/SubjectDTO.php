<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Subject;

class SubjectDTO
{
    public function __construct(
        public readonly ?int $codAs = null,
        public readonly string $Descricao = '',
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            codAs: $data['CodAs'] ?? null,
            Descricao: $data['Descricao'],
        );
    }

    public static function fromModel(Subject $subject): self
    {
        return new self(
            codAs: $subject->codAs,
            Descricao: $subject->Descricao,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'CodAs' => $this->codAs,
            'Descricao' => $this->Descricao,
        ]);
    }
}
