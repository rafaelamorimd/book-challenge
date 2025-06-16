<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Livro_Autor', function (Blueprint $table) {
            $table->integer('Livro_Codl');
            $table->integer('Autor_CodAu');
            $table->foreign('Livro_Codl')->references('Codl')->on('Livro')->onDelete('cascade');
            $table->foreign('Autor_CodAu')->references('CodAu')->on('Autor')->onDelete('cascade');
            $table->primary(['Livro_Codl', 'Autor_CodAu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Livro_Autor');
    }
};
