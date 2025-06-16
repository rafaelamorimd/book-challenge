<?php

namespace App\Providers;

use App\Services\SubjectService;
use App\Services\BookService;
use App\Services\AuthorService;
use App\Services\DashboardService;
use App\Services\LogService;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\SubjectRepository;
use App\Repository\ReportRepository;
use App\Models\Author;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SubjectService::class, function ($app) {
            return new SubjectService(
                $app->make(SubjectRepository::class),
                $app->make(LogService::class)
            );
        });

        $this->app->singleton(BookService::class, function ($app) {
            return new BookService(
                $app->make(BookRepository::class),
                $app->make(LogService::class)
            );
        });

        $this->app->singleton(AuthorService::class, function ($app) {
            return new AuthorService(
                $app->make(AuthorRepository::class),
                $app->make(LogService::class)
            );
        });

        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService(
                $app->make(BookRepository::class),
                $app->make(AuthorRepository::class),
                $app->make(SubjectRepository::class),
                $app->make(LogService::class)
            );
        });

        $this->app->singleton(LogService::class, function ($app) {
            return new LogService();
        });

        $this->app->bind(ReportRepository::class, function ($app) {
            return new ReportRepository($app->make(Author::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
