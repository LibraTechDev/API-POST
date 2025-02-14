<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use PHPUnit\Framework\Constraint\IsEmpty;



class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index(): JsonResponse
    {
        $categories = Categories::latest()->get();

        return $categories->isEmpty()
            ? $this->sendError('No categories found', 404)
            : $this->sendResponse($categories, 'Categories retrieved successfully');
    }

    /**
     * Store a new category
     */
    public function store(Request $request): JsonResponse
    {
        return Categories::where('name', $request->name)->exists()
            ? $this->sendError('Post with this name already exists', 400)
            : $this->sendResponse(
                Categories::create([
                    'name' => $validated = $request->validate([
                        'name' => 'required|string|max:50|unique:categories,name',
                    ])['name'],
                    'slug' => Str::slug($validated),
                ]),
                'Category created successfully',
                201
            );
    }


    /**
     * Show a specific category
     */
    public function show(string $id): JsonResponse
    {
        return ($category = Categories::find($id))
            ? $this->sendResponse($category, 'Category retrieved successfully')
            : $this->sendError('Category not found', 404);
    }

    /**
     * Update a category
     */
    public function update(Request $request, $id)
    {
        $category = Categories::find($id);

        return $category
            ? ($category->update(['name' => $request->input('name')])
                ? $this->sendResponse($category, 'Categories Updated Successfully')
                : $this->sendError('Categories Failed To Update', 500))
            : $this->sendError('Categories Not Found', 404);
    }

    /**
     * Soft delete a category
     */
    public function destroy($id)
    {
        $category = Categories::find($id);

        return $category
            ? ($category->delete()
                ? $this->sendResponse($category, 'Categories Deleted Successfully')
                : $this->sendError('Failed to delete category', 500))
            : $this->sendError('Categories Not Found', 404);
    }

    /**
     * Restore a soft-deleted category
     */
    public function restore(string $id): JsonResponse
    {
        $category = Categories::withTrashed()->find($id);

        return !$category
            ? $this->sendError('Category not found', 404)
            : ($category->trashed()
                ? ($category->restore()
                    ? $this->sendResponse($category, 'Category restored successfully')
                    : $this->sendError('Failed to restore category', 500))
                : $this->sendError('Category is not deleted', 400));
    }

    /**
     * Permanently delete a category
     */
    public function forceDelete($id): JsonResponse
    {
        return !($category = Categories::onlyTrashed()->find($id))
            ? $this->sendError('Categories not found in trash', 404)
            : ($category->forceDelete()
                ? $this->sendResponse(null, 'Categories permanently deleted')
                : $this->sendError('Failed to permanently delete category', 500));
    }

    /**
     * Get all trashed categories
     */
    public function getTrash(): JsonResponse
    {
        $trashedCategories = Categories::onlyTrashed()->get();

        return $trashedCategories->isEmpty()
            ? $this->sendError('Category not found', 404)
            : $this->sendResponse($trashedCategories, 'Trashed categories retrieved successfully');
    }




    /**
     * Helper function for successful JSON response
     */
    private function sendResponse($data, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Helper function for error JSON response
     */
    private function sendError(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], $status);
    }
}