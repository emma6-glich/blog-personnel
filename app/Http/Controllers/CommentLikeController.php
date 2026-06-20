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
        $userId  = Auth::id();

        $existing = CommentLike::where('comment_id', $id)->where('user_id', $userId)->first();

        if ($existing) {
            if ($comment->user_id && $comment->user_id !== $userId) {
                $comment->user->notifications()
                    ->where('type', 'App\Notifications\CommentLikedNotification')
                    ->whereJsonContains('data->comment_id', $comment->id)
                    ->delete();
            }
            $existing->delete();
            $liked = false;
        } else {
            CommentLike::create(['comment_id' => $id, 'user_id' => $userId]);
            if ($comment->user_id && $comment->user_id !== $userId) {
                $comment->load('user');
                $comment->user->notify(new CommentLikedNotification($comment, Auth::user()->name));
            }
            $liked = true;
        }

        $likesCount = CommentLike::where('comment_id', $id)->count();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'liked' => $liked, 'likes_count' => $likesCount]);
        }

        return redirect('/articles/' . $comment->post->slug);
    }
}
