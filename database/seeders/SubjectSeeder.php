<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['Descricao' => 'Java'],
            ['Descricao' => 'Programação Orientada a Objetos'],
            ['Descricao' => 'Desenvolvimento Web'],
            ['Descricao' => 'Banco de Dados'],
            ['Descricao' => 'Python'],
            ['Descricao' => 'JavaScript'],
            ['Descricao' => 'PHP'],
            ['Descricao' => 'Laravel'],
            ['Descricao' => 'React'],
            ['Descricao' => 'Vue.js'],
            ['Descricao' => 'Node.js'],
            ['Descricao' => 'TypeScript'],
            ['Descricao' => 'Docker'],
            ['Descricao' => 'Kubernetes'],
            ['Descricao' => 'DevOps'],
            ['Descricao' => 'Testes Automatizados'],
            ['Descricao' => 'Arquitetura de Software'],
            ['Descricao' => 'Design Patterns'],
            ['Descricao' => 'Clean Code'],
            ['Descricao' => 'Segurança da Informação'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
