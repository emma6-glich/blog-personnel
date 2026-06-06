<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
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

        Comment::create([
            'post_id'   => $post->id,
            'parent_id' => $request->parent_id,
            'user_id'   => Auth::id(),
            'pseudo'    => Auth::user()->name, // Nom automatique
            'content'   => $request->content,
        ]);

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
        $comment->delete();

        return redirect('/articles/' . $postSlug)->with('success', 'Commentaire supprimé.');
    }
}
