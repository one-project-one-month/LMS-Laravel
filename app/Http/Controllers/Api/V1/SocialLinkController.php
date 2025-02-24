<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialLinkRequest;
use App\Http\Resources\SocialLinkResource;
use App\Models\Course;
use App\Models\SocialLink;
use Illuminate\Http\JsonResponse;

class SocialLinkController extends Controller
{
    public function show(Course $course): JsonResponse
    {
        $socialLink = $course->socialLink;

        if (!$socialLink) {
            return response()->json(['message' => 'Social link not found'], 404);
        }

        return response()->json([
            'data' => new SocialLinkResource($socialLink)
        ], 200);
    }

    public function store(SocialLinkRequest $request, Course $course): JsonResponse
    {
        if ($course->socialLink) {
            return response()->json(['message' => 'Social link already exists'], 409);
        }

        $data = $request->validated();

        $socialLink = new SocialLink();
        $socialLink->fill($data);
        $socialLink->course()->associate($course);
        $socialLink->save();

        return response()->json(
            [
                'message' => 'Social link created successfully',
                'data' => new SocialLinkResource($socialLink)
            ],
            201
        );
    }

    public function update(SocialLinkRequest $request, Course $course): JsonResponse
    {
        $socialLink = $course->socialLink;

        if (!$socialLink) {
            return response()->json(['message' => 'Social link not found'], 404);
        }

        $data = $request->validated();
        $socialLink->fill($data);
        $socialLink->save();

        return response()->json(
            [
                'message' => 'Social link updated successfully',
                'data' => new SocialLinkResource($socialLink)
            ],
            200
        );
    }
}
