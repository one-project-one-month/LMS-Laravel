<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StudentRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function find($id, array $relations = []): ?Student;

    public function create(array $data): Student;

    public function update(Student $student, array $data): bool;

    public function delete(Student $student): ?bool;

    public function suspend(Student $student): bool;
}
