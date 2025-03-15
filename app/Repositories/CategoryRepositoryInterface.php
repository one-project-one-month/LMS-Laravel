<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function all(): Collection;

    public function find($id): ?Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): bool;

    public function delete(Category $category): ?bool;
}
