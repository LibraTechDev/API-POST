<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes, HasUuids;
    protected $fillable = ['name', 'slug'];
    protected $guarded = ['id'];
    protected $dates = ['deleted_at']; 


    public function posts()
    {
        return $this->hasMany(Posts::class);
    }
}