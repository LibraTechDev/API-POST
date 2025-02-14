<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Tags;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tag = Tags::latest()->get();

        return $tag->isEmpty()
            ? $this->sendError('No Tags Found', 404)
            : $this->sendResponse($tag, 'Tags retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
        ]);

        return $this->sendResponse(
            Tags::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]),
            'Tag created successfully'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $tag = Tags::find($id);

        return !$tag
            ? $this->sendError('Tag not found', 404)
            : $this->sendResponse($tag, 'Tag retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tag = Tags::find($id);

        return !$tag
            ? $this->sendError('Tag not found', 404)
            : tap($tag, function ($t) use ($request) {
                $request->validate([
                    'name' => 'required|string|max:50|unique:tags,name,' . $t->id,
                ]);
                $t->update([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name),
                ]);
            }) && $this->sendResponse($tag, 'Tag updated successfully');
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $tag = Tags::find($id);

        return $tag
            ? ($tag->delete()
                ? $this->sendResponse($tag, 'Tag Deleted Successfully')
                : $this->sendError('Failed to delete tag', 500))
            : $this->sendError('Tag Not Found', 404);
    }


    /**
     * Restore a soft-deleted tag.
     */
    public function restore(string $id): JsonResponse
    {
        $tag = Tags::withTrashed()->find($id);

        return !$tag
            ? $this->sendError('Tag not found', 404)
            : ($tag->trashed()
                ? ($tag->restore()
                    ? $this->sendResponse($tag, 'Tag restored successfully')
                    : $this->sendError('Failed to restore tag', 500))
                : $this->sendError('Tag is not deleted', 400));
    }


    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id): JsonResponse
    {
        return !($tag = Tags::onlyTrashed()->find($id))
            ? $this->sendError('Tag not found in trash', 404)
            : ($tag->forceDelete()
                ? $this->sendResponse(null, 'Tag permanently deleted')
                : $this->sendError('Failed to permanently delete tag', 500));
    }

    

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