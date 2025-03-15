<?php

namespace App\Repositories;

use App\Models\Instructor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InstructorRepository implements InstructorRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        // Assuming you have a "filter" scope defined on your model.
        return Instructor::with('user')
            ->filter($filters)
            ->paginate($perPage);
    }

    public function find(int $id, array $relations = []): ?Instructor
    {
        return Instructor::with($relations)->find($id);
    }

    public function create(array $data): Instructor
    {
        return Instructor::create($data);
    }

    public function update(Instructor $instructor, array $data): bool
    {
        return $instructor->update($data);
    }

    public function delete(Instructor $instructor): ?bool
    {
        return $instructor->delete();
    }

    public function suspend(Instructor $instructor): bool
    {
        return $instructor->user->update(['is_available' => false]);
    }
}
