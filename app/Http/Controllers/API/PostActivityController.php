<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post_Activities;
use App\Models\Posts;
use Illuminate\Http\Request;

class PostActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Posts $post)
    {
        $activities = Post_Activities::where('posts_id', $post->id)->get();

        if ($activities->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No activities found for this post',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}