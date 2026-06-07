<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Notifications\CommentLikedNotification;
use Illuminate\Support\Facades\Auth;

class CommentLikeController extends Controller
{
    public function toggle($id)
    {
        $comment = Comment::with('post')->findOrFail($id);
        $userId = Auth::id();

        $existing = CommentLike::where('comment_id', $id)
                                ->where('user_id', $userId)
                                ->first();

        if ($existing) {
            // Déjà liké → on retire le like et on supprime la notification
            if ($comment->user_id && $comment->user_id !== $userId) {
                $comment->user->notifications()
                    ->where('type', 'App\Notifications\CommentLikedNotification')
                    ->whereJsonContains('data->comment_id', $comment->id)
                    ->delete();
            }
            $existing->delete();
        } else {
            // Nouveau like → notifier l'auteur du commentaire
            CommentLike::create([
                'comment_id' => $id,
                'user_id'    => $userId,
            ]);

            if ($comment->user_id && $comment->user_id !== $userId) {
                $comment->load('user');
                $comment->user->notify(new CommentLikedNotification($comment, Auth::user()->name));
            }
        }

        return redirect('/articles/' . $comment->post->slug);
    }
}
