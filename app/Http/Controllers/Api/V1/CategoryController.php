<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CategoryRepositoryInterface;
use App\Traits\ResponseTraits;

class CategoryController extends Controller
{
    use ResponseTraits;

    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $categories = $this->categoryRepository->all();
        return $this->successResponse('Categories retrieved successfully', $categories, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);

        $category = $this->categoryRepository->create($data);

        return $this->successResponse('Category created successfully', $category, 201);
    }

    public function show($id)
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return $this->errorResponse('Category not found', '', 404);
        }

        return $this->successResponse('Category retrieved successfully', $category, 200);
    }

    public function update(Request $request, $id)
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return $this->errorResponse('Category not found', '', 404);
        }

        $data = $request->validate([
            'name' => 'required|string',
        ]);

        $this->categoryRepository->update($category, $data);

        return $this->successResponse('Category updated successfully', $category, 200);
    }

    public function destroy($id)
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return $this->errorResponse('Category not found', '', 404);
        }

        $this->categoryRepository->delete($category);

        return $this->successResponse('Category deleted successfully', null, 200);
    }
}
