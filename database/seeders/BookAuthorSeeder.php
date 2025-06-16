<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BookAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookAuthors = [
            ['book_id' => 1, 'author_id' => 1],
            ['book_id' => 1, 'author_id' => 2],
            ['book_id' => 2, 'author_id' => 3],
            ['book_id' => 2, 'author_id' => 4],
            ['book_id' => 3, 'author_id' => 5],
            ['book_id' => 4, 'author_id' => 6],
            ['book_id' => 5, 'author_id' => 8],
            ['book_id' => 6, 'author_id' => 9],
            ['book_id' => 7, 'author_id' => 7],
            ['book_id' => 8, 'author_id' => 7],
        ];

        foreach ($bookAuthors as $bookAuthor) {
            $book = Book::find($bookAuthor['book_id']);
            $author = Author::find($bookAuthor['author_id']);

            if ($book && $author) {
                $book->authors()->attach($author->CodAu);
            }
        }

        $faker = Faker::create('pt_BR');
        $books = Book::where('Codl', '>', 8)->get();
        $authors = Author::where('CodAu', '>', 10)->get();

        foreach ($books as $book) {
            $numAuthors = $faker->numberBetween(1, 3);
            $selectedAuthors = $authors->random($numAuthors);

            foreach ($selectedAuthors as $author) {
                $book->authors()->attach($author->CodAu);
            }
        }
    }
}
