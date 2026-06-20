<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewCommentNotification;
use App\Notifications\NewReplyNotification;
use App\Notifications\CommentDeletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|min:3|max:1000',
        ]);

        $post = Post::where('slug', $slug)->firstOrFail();

        $comment = Comment::create([
            'post_id'   => $post->id,
            'parent_id' => $request->parent_id,
            'user_id'   => Auth::id(),
            'pseudo'    => Auth::user()->name,
            'content'   => $request->content,
        ]);

        $comment->load('user', 'likes', 'replies');

        if ($post->user && $post->user_id !== Auth::id()) {
            $post->user->notify(new NewCommentNotification($comment));
        }

        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if ($parentComment && $parentComment->user_id && $parentComment->user_id !== Auth::id()) {
                $parentComment->user->notify(new NewReplyNotification($comment));
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Commentaire publié !',
                'comment' => [
                    'id'         => $comment->id,
                    'pseudo'     => $comment->pseudo,
                    'content'    => $comment->content,
                    'created_at' => $comment->created_at->format('d/m/Y à H:i'),
                    'user_id'    => $comment->user_id,
                    'parent_id'  => $comment->parent_id,
                    'avatar'     => $comment->user && $comment->user->avatar
                                    ? asset('storage/' . $comment->user->avatar)
                                    : null,
                    'likes_count' => 0,
                ]
            ]);
        }

        return redirect('/articles/' . $post->slug)->with('success', 'Commentaire publié !');
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if (Auth::id() !== $comment->user_id) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
            return redirect('/')->with('error', 'Action non autorisée.');
        }

        if ($comment->created_at->diffInMinutes(now()) > 10) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Délai de 10 minutes dépassé.'], 403);
            return back()->with('error', 'Délai de 10 minutes dépassé.');
        }

        $request->validate(['content' => 'required|min:3|max:1000']);
        $comment->update(['content' => $request->content]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Commentaire modifié !', 'content' => $comment->content]);
        }

        return redirect('/articles/' . $comment->post->slug)->with('success', 'Commentaire modifié !');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if (Auth::id() !== $comment->user_id && Auth::user()->email !== env('ADMIN_EMAIL')) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
            return redirect('/')->with('error', 'Action non autorisée.');
        }

        $postSlug  = $comment->post->slug;
        $postTitle = $comment->post->title;
        $commentUserId = $comment->user_id;

        $comment->delete();

        if ($commentUserId && $commentUserId !== Auth::id()) {
            $commentUser = \App\Models\User::find($commentUserId);
            if ($commentUser) {
                $commentUser->notify(new CommentDeletedNotification($postTitle, $postSlug));
            }
        }

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Commentaire supprimé.']);
        }

        return redirect('/articles/' . $postSlug)->with('success', 'Commentaire supprimé.');
    }
}
