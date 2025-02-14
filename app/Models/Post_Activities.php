<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Post_Activities extends Model
{
    use HasFactory,HasUuids;

    protected $table = 'post_activities';

    protected $fillable = [
        'posts_id',
        'ip',
        'userAgent',
    ];

    protected $casts = [
        'userAgent' => 'array',
    ];

    public function post()
    {
        return $this->belongsTo(Posts::class);
    }
}