<?php

namespace App\Observers;

use App\Models\Posts;
use App\Models\Post_Activities;

class PostObserver
{
    public function created(Posts $post)
    {
        Post_Activities::create([
            'posts_id' => $post->id,
            'ip' => request()->ip(),
            'userAgent' => request()->header('User-Agent'),
        ]);
    }

    public function updated(Posts $post)
    {
        Post_Activities::create([
            'posts_id' => $post->id,
            'ip' => request()->ip(),
            'userAgent' => request()->header('User-Agent'),
        ]);
    }

    public function deleted(Posts $post)
    {
        Post_Activities::create([
            'posts_id' => $post->id,
            'ip' => request()->ip(),
            'userAgent' => request()->header('User-Agent'),
        ]);
    }
}