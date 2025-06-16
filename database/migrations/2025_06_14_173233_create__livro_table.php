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
        Schema::create('Livro', function (Blueprint $table) {
            $table->increments('Codl');
            $table->string('Titulo', 40);
            $table->string('Editora', 40);
            $table->integer('Edicao');
            $table->string('AnoPublicacao', 4);
            $table->decimal('valor', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Livro');
    }
};
