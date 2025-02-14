<?php

namespace App\Http\Controllers\API;

use App\Models\Posts;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        try {
            $posts = Posts::with([
                'category:id,name',
                'tags:id,name',
                'user:id,username,role',
            ])->get();

            return $this->sendResponse($posts, 'List of Posts');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve posts: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction(); // Memulai transaksi

        try {
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

            DB::commit(); // Simpan transaksi

            return $this->sendResponse($post->load(['tags', 'category']), 'Post created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error

            return $this->sendError('Failed to create post: ' . $e->getMessage(), 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $post = Posts::with([
                'user:id,username,role',
                'category:id,name',
                'tags:id,name'
            ])->find($id);

            return $post
                ? $this->sendResponse($post, 'Post found')
                : $this->sendError('Post not found', 404);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve post: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction(); // Memulai transaksi

        try {
            if (!($post = Posts::find($id))) {
                return $this->sendError('Post not found', 404);
            }

            $validated = $request->validate($this->rules($id));
            $validated['slug'] = Str::slug($validated['title']);

            if ($request->hasFile('thumbnail')) {
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

            DB::commit(); // Simpan transaksi

            return $this->sendResponse($post->load(['tags:id,name', 'category:id,name']), 'Post updated successfully');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error

            return $this->sendError('Failed to update post: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $post = Posts::find($id);

            return $post
                ? ($post->delete()
                    ? $this->sendResponse($post, 'Post deleted successfully')
                    : $this->sendError('Failed to delete post', 500))
                : $this->sendError('Post not found', 404);
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete post: ' . $e->getMessage(), 500);
        }
    }

    public function getTrash(): JsonResponse
    {
        try {
            return $this->sendResponse(Posts::onlyTrashed()->get(), 'Deleted posts retrieved');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve deleted posts: ' . $e->getMessage(), 500);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $post = Posts::withTrashed()->find($id);

            return !$post
                ? $this->sendError('Post not found', 404)
                : ($post->trashed()
                    ? ($post->restore()
                        ? $this->sendResponse($post, 'Post restored successfully')
                        : $this->sendError('Failed to restore post', 500))
                    : $this->sendError('Post is not deleted', 400));
        } catch (\Exception $e) {
            return $this->sendError('Failed to restore post: ' . $e->getMessage(), 500);
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            return !($post = Posts::onlyTrashed()->find($id))
                ? $this->sendError('Post not found in trash', 404)
                : ($post->forceDelete()
                    ? $this->sendResponse(null, 'Post permanently deleted')
                    : $this->sendError('Failed to permanently delete post', 500));
        } catch (\Exception $e) {
            return $this->sendError('Failed to permanently delete post: ' . $e->getMessage(), 500);
        }
    }

    private function rules($id = null): array
    {
        return [
            'title' => 'required|string|max:200|unique:posts,title,' . ($id ?? 'NULL'),
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'uuid|exists:tags,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
            'status' => 'required|in:publish,draft',
            'meta_title' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:150',
        ];
    }

    public function attachTags(Request $request, Posts $post): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tags' => 'required|array',
                'tags.*' => 'uuid|exists:tags,id',
            ]);

            $post->tags()->syncWithoutDetaching($validated['tags']);

            return $this->sendResponse($post->load('tags'), 'Tags attached successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to attach tags: ' . $e->getMessage(), 500);
        }
    }
}