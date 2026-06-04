<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    // Une catégorie a plusieurs articles
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
