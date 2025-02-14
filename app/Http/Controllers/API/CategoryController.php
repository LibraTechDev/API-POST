<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Traits\ApiResponse;
use Exception;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Get all categories
     */
    public function index(): JsonResponse
    {
        try {
            $categories = Categories::latest()->get();
            return $categories->isEmpty()
                ? $this->sendError('No categories found', 404)
                : $this->sendResponse($categories, 'Categories retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Store a new category
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if (Categories::where('name', $request->name)->exists()) {
                return $this->sendError('Post with this name already exists', 400);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:categories,name',
            ]);

            $category = Categories::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
            ]);

            return $this->sendResponse($category, 'Category created successfully', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Show a specific category
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = Categories::find($id);
            return $category
                ? $this->sendResponse($category, 'Category retrieved successfully')
                : $this->sendError('Category not found', 404);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Update a category
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Categories::find($id);
            if (!$category) {
                return $this->sendError('Category Not Found', 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
            ]);

            $category->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']), // Buat slug otomatis dari name
            ]);

            return $this->sendResponse($category, 'Category Updated Successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Soft delete a category
     */
    public function destroy($id)
    {
        try {
            $category = Categories::find($id);
            if (!$category) {
                return $this->sendError('Category Not Found', 404);
            }

            $category->delete();
            return $this->sendResponse($category, 'Category Deleted Successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Restore a soft-deleted category
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $category = Categories::withTrashed()->find($id);
            if (!$category) {
                return $this->sendError('Category not found', 404);
            }

            if (!$category->trashed()) {
                return $this->sendError('Category is not deleted', 400);
            }

            $category->restore();
            return $this->sendResponse($category, 'Category restored successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Permanently delete a category
     */
    public function forceDelete($id): JsonResponse
    {
        try {
            $category = Categories::onlyTrashed()->find($id);
            if (!$category) {
                return $this->sendError('Category not found in trash', 404);
            }

            $category->forceDelete();
            return $this->sendResponse(null, 'Category permanently deleted');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    // /**
    //  * Get all trashed categories
    //  */
    // public function getTrash(): JsonResponse
    // {
    //     try {
    //         $trashedCategories = Categories::onlyTrashed()->get();
    //         return $trashedCategories->isEmpty()
    //             ? $this->sendError('No trashed categories found', 404)
    //             : $this->sendResponse($trashedCategories, 'Trashed categories retrieved successfully');
    //     } catch (Exception $e) {
    //         return $this->sendError($e->getMessage(), 500);
    //     }
    // }
}