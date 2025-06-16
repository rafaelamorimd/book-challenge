<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('books', BookController::class);
Route::resource('authors', AuthorController::class);
Route::resource('subjects', SubjectController::class);

Route::prefix('reports')->group(function () {
    Route::get('/authors', [ReportController::class, 'index'])->name('reports.authors');
    Route::get('/authors/download', [ReportController::class, 'download'])->name('reports.authors.download');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
