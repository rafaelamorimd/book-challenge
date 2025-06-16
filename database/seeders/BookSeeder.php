<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'Titulo' => 'Use a Cabeça! Java AAA',
                'Editora' => 'Alta Books',
                'Edicao' => 2,
                'AnoPublicacao' => '2010',
                'valor' => '119.00',
            ],
            [
                'Titulo' => 'Java, como Programar',
                'Editora' => 'Pearson',
                'Edicao' => 10,
                'AnoPublicacao' => '2011',
                'valor' => '180.00',
            ],
            [
                'Titulo' => 'Core Java for the Impatient',
                'Editora' => 'Addison-Wesley Professional',
                'Edicao' => 1,
                'AnoPublicacao' => '2012',
                'valor' => '89.90',
            ],
            [
                'Titulo' => 'Effective Java',
                'Editora' => 'Addison-Wesley Professional',
                'Edicao' => 3,
                'AnoPublicacao' => '2017',
                'valor' => '99.90',
            ],
            [
                'Titulo' => 'Clean Code',
                'Editora' => 'Alta Books',
                'Edicao' => 1,
                'AnoPublicacao' => '2009',
                'valor' => '129.90',
            ],
            [
                'Titulo' => 'Domain-Driven Design',
                'Editora' => 'Addison-Wesley Professional',
                'Edicao' => 1,
                'AnoPublicacao' => '2004',
                'valor' => '149.90',
            ],
            [
                'Titulo' => 'Refactoring',
                'Editora' => 'Addison-Wesley Professional',
                'Edicao' => 2,
                'AnoPublicacao' => '2018',
                'valor' => '159.90',
            ],
            [
                'Titulo' => 'Patterns of EA Architecture',
                'Editora' => 'Addison-Wesley Professional',
                'Edicao' => 1,
                'AnoPublicacao' => '2003',
                'valor' => '139.90',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        $faker = Faker::create('pt_BR');
        $editoras = ['Alta Books', 'Pearson', 'Addison-Wesley Professional', 'Novatec', 'Casa do Código', 'Bookman', 'O\'Reilly'];

        for ($i = 0; $i < 20; $i++) {
            Book::create([
                'Titulo' => substr($faker->sentence(3), 0, 40),
                'Editora' => $faker->randomElement($editoras),
                'Edicao' => $faker->numberBetween(1, 5),
                'AnoPublicacao' => (string) $faker->year('now'),
                'valor' => (string) $faker->randomFloat(2, 49.90, 299.90),
            ]);
        }
    }
}
