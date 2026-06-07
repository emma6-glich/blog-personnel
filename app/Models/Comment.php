<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['post_id', 'parent_id', 'user_id', 'pseudo', 'content'];

    // Un commentaire appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un commentaire peut avoir plusieurs réponses (enfants)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Un commentaire appartient à un article
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Un commentaire peut avoir plusieurs likes
    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }
}