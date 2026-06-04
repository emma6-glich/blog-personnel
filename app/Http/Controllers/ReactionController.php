<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\Post;
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
                // Même réaction → on la retire (toggle)
                $existing->delete();
            } else {
                // Réaction différente → on la change
                $existing->update(['emoji' => $emoji]);
            }
        } else {
            // Nouvelle réaction
            Reaction::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'emoji'   => $emoji,
            ]);
        }

        return redirect('/articles/' . $post->slug);
    }
}
