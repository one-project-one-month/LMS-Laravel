<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialLinkRequest;
use App\Http\Resources\SocialLinkResource;
use App\Models\Course;
use App\Repositories\SocialLinkRepositoryInterface;
use App\Traits\ResponseTraits;
use Illuminate\Http\JsonResponse;

class SocialLinkController extends Controller
{
    use ResponseTraits;

    protected $socialLinkRepository;

    public function __construct(SocialLinkRepositoryInterface $socialLinkRepository)
    {
        $this->socialLinkRepository = $socialLinkRepository;
    }

    public function show(Course $course): JsonResponse
    {
        $socialLink = $this->socialLinkRepository->findByCourse($course);

        if (!$socialLink) {
            return $this->errorResponse('Social link not found', '', 404);
        }

        return $this->successResponse(
            'Social link retrieved successfully',
            new SocialLinkResource($socialLink),
            200
        );
    }

    public function store(SocialLinkRequest $request, Course $course): JsonResponse
    {
        if ($this->socialLinkRepository->findByCourse($course)) {
            return $this->errorResponse('Social link already exists', '', 409);
        }

        $data = $request->validated();
        $socialLink = $this->socialLinkRepository->createForCourse($course, $data);

        return $this->successResponse(
            'Social link created successfully',
            new SocialLinkResource($socialLink),
            201
        );
    }

    public function update(SocialLinkRequest $request, Course $course): JsonResponse
    {
        $socialLink = $this->socialLinkRepository->findByCourse($course);

        if (!$socialLink) {
            return $this->errorResponse('Social link not found', '', 404);
        }

        $data = $request->validated();
        $this->socialLinkRepository->update($socialLink, $data);

        return $this->successResponse(
            'Social link updated successfully',
            new SocialLinkResource($socialLink),
            200
        );
    }
}
