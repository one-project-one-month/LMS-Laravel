<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'Categories retrived successfully',
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category  = Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ]);
    }

    /**
     * Display the specified resource.
     */

    public function show(Category $category, Request $request)
    {
        $category = Category::find($request->id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found']
                , 404);
        }

        return response()->json(
            ['data' => $category]
             ,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Category $category)
    {
        $category = Category::find($category->id);

        if(!$category){
            return response()->json([
            'message' => 'Category not found',
            ], 404);
        }
        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, Request $request)
    {
        $category = Category::find($request->id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found']
                , 404);
        }

        $category->delete();

        return response()->json(
            ['message' => 'Category deleted successfully']
            , 200);
        }
}
