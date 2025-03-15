<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\SocialLink;

interface SocialLinkRepositoryInterface
{
    public function findByCourse(Course $course): ?SocialLink;

    public function createForCourse(Course $course, array $data): SocialLink;

    public function update(SocialLink $socialLink, array $data): bool;
}
