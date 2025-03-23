<?php

namespace App\Providers;

use App\Interfaces\AdminDashboardInterface;
use App\Interfaces\LessonInterface;
use App\Models\Course;
use App\Policies\CoursePolicy;
use App\Repositories\course\CourseRepository;
use App\Repositories\course\CourseRepositoryInterface;
use App\Services\CourseService;
use Fruitcake\Cors\CorsService;
use App\Repositories\AdminDashboardReposity;
use App\Repositories\CategoryRepository;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\InstructorRepository;
use App\Repositories\InstructorRepositoryInterface;
use App\Repositories\LessonRepository;
use App\Repositories\SocialLinkRepository;
use App\Repositories\SocialLinkRepositoryInterface;
use App\Repositories\StudentRepository;
use App\Repositories\StudentRepositoryInterface;
use App\Repositories\UpdateProfilePhotoRepository;
use App\Repositories\UpdateProfilePhotoRepositoryInterface;
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
        // $this->app->bind(LessonInterface::class, LessonRepository::class);
        // $this->app->bind(AdminDashboardInterface::class,AdminDashboardReposity::class);
        $this->app->bind(InstructorRepositoryInterface::class, InstructorRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SocialLinkRepositoryInterface::class, SocialLinkRepository::class);
        $this->app->bind(UpdateProfilePhotoRepositoryInterface::class, UpdateProfilePhotoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
