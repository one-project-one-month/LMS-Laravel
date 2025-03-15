<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Instructor;

interface InstructorRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function find(int $id, array $relations = []): ?Instructor;

    public function create(array $data): Instructor;

    public function update(Instructor $instructor, array $data): bool;

    public function delete(Instructor $instructor): ?bool;

    public function suspend(Instructor $instructor): bool;
}
