<?php

namespace App\Providers;

use App\Interfaces\LessonInterface;
use App\Models\Course;
use App\Policies\CoursePolicy;
use App\Repositories\LessonRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LessonInterface::class, LessonRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

    }
}
