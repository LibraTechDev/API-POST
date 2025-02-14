<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class Tags extends Model
{
    use SoftDeletes, HasUuids;
    protected $fillable = ['name', 'slug'];
    protected $guarded = ['id'];


    public function posts()
    {
        return $this->belongsToMany(Posts::class);
    }
}