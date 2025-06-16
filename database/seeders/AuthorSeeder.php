<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            ['Nome' => 'Kathy Sierra'],
            ['Nome' => 'Bert Bates'],
            ['Nome' => 'Paul Deitel'],
            ['Nome' => 'Harvey Deitel'],
            ['Nome' => 'Cay S. Horstmann'],
            ['Nome' => 'Joshua Bloch'],
            ['Nome' => 'Martin Fowler'],
            ['Nome' => 'Robert C. Martin'],
            ['Nome' => 'Eric Evans'],
            ['Nome' => 'Kent Beck'],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }

        // Criar mais 20 autores usando Faker
        $faker = Faker::create('pt_BR');
        for ($i = 0; $i < 20; $i++) {
            Author::create([
                'Nome' => $faker->name(),
            ]);
        }
    }
}
