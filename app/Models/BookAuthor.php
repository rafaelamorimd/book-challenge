<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookAuthor extends Pivot
{
    protected $table = 'Livro_Autor';
    public $timestamps = false;
    protected $fillable = [
        'Livro_Codl',
        'Autor_CodAu',
    ];
}
