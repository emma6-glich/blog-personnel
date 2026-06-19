<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    protected $fillable = ['post_id', 'user_id', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
