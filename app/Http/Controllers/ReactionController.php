<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\Post;
use App\Notifications\NewReactionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function store(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $emoji = $request->emoji;
        $userId = Auth::id();

        $existing = Reaction::where('post_id', $post->id)
                             ->where('user_id', $userId)
                             ->first();

        if ($existing) {
            if ($existing->emoji === $emoji) {
                // Même réaction → on la retire ET on supprime la notification
                if ($post->user && $post->user_id !== $userId) {
                    $post->user->notifications()
                        ->where('type', 'App\Notifications\NewReactionNotification')
                        ->whereJsonContains('data->reaction_id', $existing->id)
                        ->delete();
                }
                $existing->delete();
            } else {
                $existing->update(['emoji' => $emoji]);
                // Notifier l'auteur de l'article
                if ($post->user && $post->user_id !== $userId) {
                    $existing->load('user', 'post');
                    $post->user->notify(new NewReactionNotification($existing));
                }
            }
        } else {
            $reaction = Reaction::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'emoji'   => $emoji,
            ]);
            // Notifier l'auteur de l'article
            if ($post->user && $post->user_id !== $userId) {
                $reaction->load('user', 'post');
                $post->user->notify(new NewReactionNotification($reaction));
            }
        }

        return redirect('/articles/' . $post->slug);
    }
}
