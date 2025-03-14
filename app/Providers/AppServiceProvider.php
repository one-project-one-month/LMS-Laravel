<?php

namespace App\Providers;

use App\Models\Course;
use App\Policies\CoursePolicy;
use App\Repositories\course\CourseRepository;
use App\Repositories\course\CourseRepositoryInterface;
use App\Services\CourseService;
use Fruitcake\Cors\CorsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);

        $this->app->bind(CourseService::class, function ($app) {
            return new CourseService(
                $app->make(CourseRepositoryInterface::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
