<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookSubject extends Pivot
{
    protected $table = 'Livro_Assunto';
    public $timestamps = false;
    protected $fillable = [
        'Livro_Codl',
        'Assunto_CodAs',
    ];
}
