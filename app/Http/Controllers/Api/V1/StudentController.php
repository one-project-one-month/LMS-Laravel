<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Repositories\StudentRepositoryInterface;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;

class StudentController extends Controller
{
    use ResponseTraits;

    protected $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'is_available']);
        $students = $this->studentRepository->getAll($filters);

        return $this->successResponse(
            'Students retrieved successfully',
            StudentResource::collection($students),
            200
        );
    }

    public function show($id)
    {
        $student = $this->studentRepository->find($id, ['user', 'courses']);
        if (!$student) {
            return $this->errorResponse('Student not found', '', 404);
        }

        return $this->successResponse(
            'Student retrieved successfully',
            new StudentResource($student),
            200
        );
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->admin) {
            return $this->errorResponse('User is already registered as an admin', '', 422);
        }

        if ($user->instructor) {
            return $this->errorResponse('User is already registered as an instructor', '', 422);
        }

        if ($user->student) {
            return $this->errorResponse('User is already registered as a student', '', 422);
        }

        $student = $this->studentRepository->create([
            'user_id' => $user->id
        ]);

        return $this->successResponse(
            'Student created successfully',
            new StudentResource($student),
            201
        );
    }

    public function update(Request $request, $id)
    {
        // Adjust validation rules as needed
        $validated = $request->validate([]);

        $student = $this->studentRepository->find($id);
        if (!$student) {
            return $this->errorResponse('Student not found', '', 404);
        }

        $this->studentRepository->update($student, $validated);

        return $this->successResponse(
            'Student updated successfully',
            new StudentResource($student),
            200
        );
    }

    public function destroy($id)
    {
        $student = $this->studentRepository->find($id);
        if (!$student) {
            return $this->errorResponse('Student not found', '', 404);
        }

        $this->studentRepository->delete($student);

        return $this->successResponse('Student deleted successfully', null, 200);
    }

    public function suspend($id)
    {
        $student = $this->studentRepository->find($id, ['user']);
        if (!$student) {
            return $this->errorResponse('Student not found', '', 404);
        }

        $this->studentRepository->suspend($student);

        return $this->successResponse('Student suspended successfully', null, 200);
    }
}
