<?php

namespace App\Http\Controllers\API;

use App\Models\Posts;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Posts::with([
            'category:id,name',
            'tags:id,name',
            'user:id,username,role',
        ])->get();

        return $this->sendResponse($posts, 'List of Posts');
    }


    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validate($this->rules());

        $validated['id'] = (string) Str::uuid();
        $validated['user_id'] = $user->id;
        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            $thumbnailFile = $request->file('thumbnail');
            $thumbnailName = time() . '_' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();
            $thumbnailPath = $thumbnailFile->storeAs('article_thumbnails', $thumbnailName, 'public');

            $validated['thumbnail'] = $thumbnailPath;
        }

        $post = Posts::create($validated);

        if (!empty($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return $this->sendResponse($post->load(['tags', 'category']), 'Post created successfully', 201);
    }



    public function show($id): JsonResponse
    {
        $post = Posts::with([
            'user:id,username,role',
            'category:id,name',
            'tags:id,name'
        ])->find($id);

        return $post
            ? $this->sendResponse($post, 'Post found')
            : $this->sendError('Post not found', 404);
    }


    public function update(Request $request, $id): JsonResponse
    {

        if (!($post = Posts::find($id))) {
            return $this->sendError('Post not found', 404);
        }

        $validated = $request->validate($this->rules($id));
        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }

            $thumbnailFile = $request->file('thumbnail');
            $thumbnailName = time() . '_' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();
            $thumbnailPath = $thumbnailFile->storeAs('article_thumbnails', $thumbnailName, 'public');

            $validated['thumbnail'] = $thumbnailPath;
        }

        $post->update($validated);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return $this->sendResponse($post->load(['tags:id,name', 'category:id,name']), 'Post updated successfully');

    }



    public function destroy($id): JsonResponse
    {
        $post = Posts::find($id);

        return $post
            ? ($post->delete()
                ? $this->sendResponse($post, 'Posts Deleted Successfully')
                : $this->sendError('Failed to delete post', 500))
            : $this->sendError('Posts Not Found', 404);
    }

    public function getTrash(): JsonResponse
    {
        return $this->sendResponse(Posts::onlyTrashed()->get(), 'Deleted posts retrieved');
    }

    public function restore($id): JsonResponse
    {
        $post = Posts::withTrashed()->find($id);

        return !$post
            ? $this->sendError('Post not found', 404)
            : ($post->trashed()
                ? ($post->restore()
                    ? $this->sendResponse($post, 'Post restored successfully')
                    : $this->sendError('Failed to restore post', 500))
                : $this->sendError('Post is not deleted', 400));
    }

    public function forceDelete($id): JsonResponse
    {
        return !($post = Posts::onlyTrashed()->find($id))
            ? $this->sendError('Post not found in trash', 404)
            : ($post->forceDelete()
                ? $this->sendResponse(null, 'Post permanently deleted')
                : $this->sendError('Failed to permanently delete post', 500));
    }

    private function rules($id = null): array
    {
        return [
            'title' => 'required|string|max:200|unique:posts,title,' . ($id ?? 'NULL'),
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'uuid|exists:tags,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
            'published_at' => 'nullable|date',
            'status' => 'required|in:publish,draft',
            'meta_title' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:150',
        ];
    }

    public function attachTags(Request $request, Posts $post): JsonResponse
    {
        $validated = $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'uuid|exists:tags,id',
        ]);

        // Attach tags ke post
        $post->tags()->syncWithoutDetaching($validated['tags']);

        return $this->sendResponse($post->load('tags'), 'Tags attached successfully', 200);
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