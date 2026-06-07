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

        // Notifier l'auteur de l'article (si ce n'est pas lui qui commente)
        if ($post->user && $post->user_id !== Auth::id()) {
            $post->user->notify(new NewCommentNotification($comment));
        }

        // Si c'est une réponse, notifier l'auteur du commentaire parent
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if ($parentComment && $parentComment->user_id && $parentComment->user_id !== Auth::id()) {
                $parentComment->user->notify(new NewReplyNotification($comment));
            }
        }

        return redirect('/articles/' . $post->slug)->with('success', 'Commentaire publié !');
    }

    // Modifier un commentaire (auteur ou admin)
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        // Seul l'auteur ou l'admin peut modifier
        if (Auth::id() !== $comment->user_id && Auth::user()->email !== env('ADMIN_EMAIL')) {
            return redirect('/')->with('error', 'Action non autorisée.');
        }

        // Limite de 10 minutes pour modifier (sauf admin)
        if (Auth::user()->email !== env('ADMIN_EMAIL')) {
            if ($comment->created_at->diffInMinutes(now()) > 10) {
                return back()->with('error', 'Vous ne pouvez plus modifier ce commentaire (délai de 10 minutes dépassé).');
            }
        }

        $request->validate([
            'content' => 'required|min:3|max:1000',
        ]);

        $comment->update(['content' => $request->content]);

        return redirect('/articles/' . $comment->post->slug)->with('success', 'Commentaire modifié !');
    }

    // Supprimer un commentaire (auteur ou admin)
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Seul l'auteur du commentaire ou l'admin peut supprimer
        if (Auth::id() !== $comment->user_id && Auth::user()->email !== env('ADMIN_EMAIL')) {
            return redirect('/')->with('error', 'Action non autorisée.');
        }

        $postSlug = $comment->post->slug;
        $postTitle = $comment->post->title;
        $commentUserId = $comment->user_id;

        $comment->delete();

        // Notifier l'auteur du commentaire si c'est l'admin qui supprime
        if ($commentUserId && $commentUserId !== Auth::id()) {
            $commentUser = \App\Models\User::find($commentUserId);
            if ($commentUser) {
                $commentUser->notify(new CommentDeletedNotification($postTitle, $postSlug));
            }
        }

        return redirect('/articles/' . $postSlug)->with('success', 'Commentaire supprimé.');
    }
}
