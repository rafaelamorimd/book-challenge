<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportAuthor extends Model
{

    public $table = 'view_report_author';

    protected $fillable = [
        'author_id',
        'author_name',
        'book_id',
        'book_title',
        'publisher',
        'edition',
        'publication_year',
        'amount',
        'subjects',
    ];

    protected $casts = [
        'author_id' => 'integer',
        'book_id' => 'integer',
        'edition' => 'integer',
        'publication_year' => 'string',
        'amount' => 'decimal:2',
    ];
}
