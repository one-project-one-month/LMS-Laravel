<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\SocialLink;

class SocialLinkRepository implements SocialLinkRepositoryInterface
{
    public function findByCourse(Course $course): ?SocialLink
    {
        return $course->socialLink;
    }

    public function createForCourse(Course $course, array $data): SocialLink
    {
        $socialLink = new SocialLink();
        $socialLink->fill($data);
        $socialLink->course()->associate($course);
        $socialLink->save();

        return $socialLink;
    }

    public function update(SocialLink $socialLink, array $data): bool
    {
        $socialLink->fill($data);
        return $socialLink->save();
    }
}
