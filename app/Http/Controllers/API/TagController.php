<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Tags;
use Illuminate\Support\Str;
use App\traits\ApiResponse;
use Exception;

class TagController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        try {
            $tag = Tags::latest()->get();
            return $tag->isEmpty()
                ? $this->sendError('No Tags Found', 404)
                : $this->sendResponse($tag, 'Tags retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:50|unique:tags,name',
            ]);
            
            $tag = Tags::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
            return $this->sendResponse($tag, 'Tag created successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $tag = Tags::find($id);
            return !$tag
                ? $this->sendError('Tag not found', 404)
                : $this->sendResponse($tag, 'Tag retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $tag = Tags::find($id);
            if (!$tag) {
                return $this->sendError('Tag not found', 404);
            }
            
            $request->validate([
                'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
            ]);
            
            $tag->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
            
            return $this->sendResponse($tag, 'Tag updated successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $tag = Tags::find($id);
            if (!$tag) {
                return $this->sendError('Tag not found', 404);
            }
            
            return $tag->delete()
                ? $this->sendResponse($tag, 'Tag Deleted Successfully')
                : $this->sendError('Failed to delete tag', 500);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function restore(string $id): JsonResponse
    {
        try {
            $tag = Tags::withTrashed()->find($id);
            if (!$tag) {
                return $this->sendError('Tag not found', 404);
            }
            
            return $tag->trashed() && $tag->restore()
                ? $this->sendResponse($tag, 'Tag restored successfully')
                : $this->sendError('Failed to restore tag', 500);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function forceDelete(string $id): JsonResponse
    {
        try {
            $tag = Tags::onlyTrashed()->find($id);
            if (!$tag) {
                return $this->sendError('Tag not found in trash', 404);
            }
            
            return $tag->forceDelete()
                ? $this->sendResponse(null, 'Tag permanently deleted')
                : $this->sendError('Failed to permanently delete tag', 500);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}