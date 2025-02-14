<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use SoftDeletes, HasUuids;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id',
        'category_id',
        'thumbnail',
        'published_at',
        'status',
        'meta_title',
        'meta_description'

    ];
    protected $guarded = ['id'];
    protected $casts = [
        'published_at' => 'date',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tags::class, 'posts_tags', 'posts_id', 'tags_id');
    }


    public function activities()
    {
        return $this->hasMany(Post_Activities::class, 'posts_id', 'id');
    }
}