<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post_Activities;
use App\Models\Posts;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class PostActivityController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Posts $post)
    {
        try {
            $activities = Post_Activities::where('posts_id', $post->id)->get();

            if ($activities->isEmpty()) {
                return $this->sendError('No activities found for this post', 404);
            }

            return $this->sendResponse($activities, 'Post activities retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve post activities: ' . $e->getMessage(), 500);
        }
    }
}