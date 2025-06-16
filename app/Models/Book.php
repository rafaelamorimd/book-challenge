<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $table = 'Livro';
    protected $primaryKey = 'Codl';
    public $timestamps = false;
    protected $fillable = [
        'Codl',
        'Titulo',
        'Editora',
        'Edicao',
        'AnoPublicacao',
        'valor',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'Livro_Autor', 'Livro_Codl', 'Autor_CodAu');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'Livro_Assunto', 'Livro_Codl', 'Assunto_CodAs');
    }
}
