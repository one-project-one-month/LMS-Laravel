<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InstructorResource;
use App\Repositories\InstructorRepositoryInterface;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;

class InstructorController extends Controller
{
    use ResponseTraits;

    protected $instructorRepository;

    public function __construct(InstructorRepositoryInterface $instructorRepository)
    {
        $this->instructorRepository = $instructorRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'edu', 'is_available']);
        $instructors = $this->instructorRepository->getAll($filters);

        return $this->successResponse(
            'Instructors retrieved successfully',
            InstructorResource::collection($instructors),
            200
        );
    }

    public function show($id)
    {
        $instructor = $this->instructorRepository->find($id, ['user', 'courses']);
        if (!$instructor) {
            return $this->errorResponse('Instructor not found', '', 404);
        }

        return $this->successResponse(
            'Instructor retrieved successfully',
            new InstructorResource($instructor),
            200
        );
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->admin) {
            return $this->errorResponse('User is already registered as an admin', '', 422);
        }

        if ($user->student) {
            return $this->errorResponse('User is already registered as a student', '', 422);
        }

        if ($user->instructor) {
            return $this->errorResponse('User is already registered as an instructor', '', 422);
        }

        $validated = $request->validate([
            'nrc'            => ['required', 'string'],
            'edu_background' => ['required', 'string'],
        ]);

        $instructor = $this->instructorRepository->create([
            'user_id'        => $user->id,
            'nrc'            => $validated['nrc'],
            'edu_background' => $validated['edu_background'],
        ]);

        return $this->successResponse(
            'Instructor created successfully',
            new InstructorResource($instructor),
            201
        );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nrc'            => 'sometimes|required|string',
            'edu_background' => 'sometimes|required|string',
        ]);

        $instructor = $this->instructorRepository->find($id);
        if (!$instructor) {
            return $this->errorResponse('Instructor not found', '', 404);
        }

        $this->instructorRepository->update($instructor, $validated);

        return $this->successResponse(
            'Instructor updated successfully',
            new InstructorResource($instructor),
            200
        );
    }

    public function destroy($id)
    {
        $instructor = $this->instructorRepository->find($id);
        if (!$instructor) {
            return $this->errorResponse('Instructor not found', '', 404);
        }

        $this->instructorRepository->delete($instructor);

        return $this->successResponse('Instructor deleted successfully', null, 200);
    }

    public function suspend($id)
    {
        $instructor = $this->instructorRepository->find($id, ['user']);
        if (!$instructor) {
            return $this->errorResponse('Instructor not found', '', 404);
        }

        $this->instructorRepository->suspend($instructor);

        return $this->successResponse('Instructor suspended successfully', null, 200);
    }
}
