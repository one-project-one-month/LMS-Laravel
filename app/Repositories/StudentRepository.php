<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentRepository implements StudentRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return Student::with('user')
            ->filter($filters)
            ->paginate($perPage);
    }

    public function find($id, array $relations = []): ?Student
    {
        return Student::with($relations)->find($id);
    }

    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function update(Student $student, array $data): bool
    {
        return $student->update($data);
    }

    public function delete(Student $student): ?bool
    {
        return $student->delete();
    }

    public function suspend(Student $student): bool
    {
        return $student->user->update(['is_available' => false]);
    }
}
