<?php

namespace App\Jobs;

use App\Mail\CourseCreated;
use App\Models\Course;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class RequestCreateCourse implements ShouldQueue
{
    use Queueable;
    protected $user;
    /**
     * Create a new job instance.
     */
    public function __construct(public Course $course)
    {
        //
        $this->user =  JWTAuth::parseToken()->authenticate();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to("admin@gmail.com")->queue(new CourseCreated($this->course, $this->user));
    }
}
