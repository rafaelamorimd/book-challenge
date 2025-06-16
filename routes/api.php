<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::apiResource('books', BookController::class);
    Route::apiResource('authors', AuthorController::class);
    Route::apiResource('subjects', SubjectController::class);

    Route::prefix('reports')->group(function () {
        Route::get('/authors', [ReportController::class, 'index'])->name('reports.authors');
        Route::get('/authors/download', [ReportController::class, 'download'])->name('reports.authors.download');
    });
});


