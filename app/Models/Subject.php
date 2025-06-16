<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $table = 'Assunto';
    protected $primaryKey = 'CodAs';
    public $timestamps = false;
    protected $fillable = [
        'CodAs',
        'Descricao',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'Livro_Assunto', 'Assunto_CodAs', 'Livro_Codl');
    }
}
