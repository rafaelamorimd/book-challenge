<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    protected $table = 'Autor';
    protected $primaryKey = 'CodAu';
    public $timestamps = false;
    protected $fillable = [
        'CodAu',
        'Nome',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'Livro_Autor', 'Autor_CodAu', 'Livro_Codl');
    }
}
