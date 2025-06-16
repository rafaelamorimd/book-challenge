<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BookSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookSubjects = [
            ['book_id' => 1, 'subject_id' => 1],
            ['book_id' => 2, 'subject_id' => 1],
            ['book_id' => 2, 'subject_id' => 2],
            ['book_id' => 3, 'subject_id' => 1],
            ['book_id' => 3, 'subject_id' => 2],
            ['book_id' => 4, 'subject_id' => 1],
            ['book_id' => 4, 'subject_id' => 2],
            ['book_id' => 5, 'subject_id' => 19],
            ['book_id' => 6, 'subject_id' => 17],
            ['book_id' => 7, 'subject_id' => 19],
            ['book_id' => 8, 'subject_id' => 18],
        ];

        foreach ($bookSubjects as $bookSubject) {
            $book = Book::find($bookSubject['book_id']);
            $subject = Subject::find($bookSubject['subject_id']);

            if ($book && $subject) {
                $book->subjects()->attach($subject->CodAs);
            }
        }

        $faker = Faker::create('pt_BR');
        $books = Book::where('Codl', '>', 8)->get();
        $subjects = Subject::all();

        foreach ($books as $book) {
            $numSubjects = $faker->numberBetween(2, 4);
            $selectedSubjects = $subjects->random($numSubjects);

            foreach ($selectedSubjects as $subject) {
                $book->subjects()->attach($subject->CodAs);
            }
        }
    }
}
