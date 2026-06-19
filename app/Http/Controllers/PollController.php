<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PollVote;
use App\Models\Category;
use App\Models\User;
use App\Notifications\NewPollVoteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    public function vote(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($request->category_id);

        $existing = PollVote::where('post_id', $post->id)
                            ->where('user_id', Auth::id())
                            ->first();

        if ($existing) {
            $existing->update(['category_id' => $request->category_id]);
        } else {
            PollVote::create([
                'post_id'     => $post->id,
                'user_id'     => Auth::id(),
                'category_id' => $request->category_id,
            ]);

            // Notifier l'admin
            $admin = User::where('email', env('ADMIN_EMAIL'))->first();
            if ($admin && $admin->id !== Auth::id()) {
                $admin->notify(new NewPollVoteNotification($post, $category, Auth::user()->name));
            }
        }

        return redirect('/articles/' . $post->slug)->with('success', 'Vote enregistré !');
    }
}
