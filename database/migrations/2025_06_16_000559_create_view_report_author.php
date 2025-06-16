<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            CREATE OR REPLACE VIEW view_report_author AS
            SELECT
                DISTINCT
                ROW_NUMBER() OVER () AS id,
                a."CodAu" as author_id,
                a."Nome" as author_name,
                l."Codl" as book_id,
                l."Titulo" as book_title,
                l."Editora" as publisher,
                l."Edicao" as edition,
                l."AnoPublicacao" as publication_year,
                l."valor" as amount,
                STRING_AGG(a2."Descricao", \', \') as subjects
            FROM "Autor" a
            LEFT JOIN "Livro_Autor" la ON la."Autor_CodAu" = a."CodAu"
            LEFT JOIN "Livro" l ON l."Codl" = la."Livro_Codl"
            LEFT JOIN "Livro_Assunto" la2 ON la2."Livro_Codl" = l."Codl"
            LEFT JOIN "Assunto" a2 ON a2."CodAs" = la2."Assunto_CodAs"
            GROUP BY a."CodAu", a."Nome", l."Codl", l."Titulo", l."Edicao", l."Editora", l."AnoPublicacao", l."valor"
            ORDER BY a."Nome" ASC
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS view_report_author');
    }
};
