<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\PostView;
use App\Models\PollVote;
use App\Models\User;
use App\Notifications\NewArticleNotification;
use App\Notifications\ArticleDeletedNotification;
use App\Notifications\ArticleUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Afficher la page d'accueil avec tous les articles
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Post::with(['user', 'category'])->latest();

        // Filtre par mot clé
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $posts = $query->paginate(6);

        return view('welcome', compact('posts', 'categories'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        if (Auth::user()->email !== env('ADMIN_EMAIL')) {
            return redirect('/')->with('error', 'Accès réservé à l\'administrateur.');
        }

        $categories = Category::all();
        return view('create', compact('categories'));
    }

    // Enregistrer le nouvel article dans la base de données
    public function store(Request $request)
    {
        if (Auth::user()->email !== env('ADMIN_EMAIL')) {
            return redirect('/')->with('error', 'Accès réservé à l\'administrateur.');
        }
        $request->validate([
            'title'       => 'required|max:255',
            'content'     => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Gestion de l'upload de l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // Générer un slug unique depuis le titre
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        Post::create([
            'title'       => $request->title,
            'slug'        => $slug,
            'content'     => $request->content,
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'image'       => $imagePath,
        ]);

        // Notifier tous les utilisateurs sauf l'admin
        $post = Post::where('slug', $slug)->first();
        $users = User::where('id', '!=', Auth::id())->get();
        foreach ($users as $user) {
            $user->notify(new NewArticleNotification($post));
        }

        return redirect('/')->with('success', 'Article publié avec succès !');
    }

    // Afficher un article spécifique
    public function show(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        $ip = request()->ip();

        // Pour les utilisateurs connectés, on utilise leur ID. Pour les visiteurs, l'IP
        $alreadyViewed = PostView::where('post_id', $post->id)
                                  ->where(function($q) use ($ip) {
                                      if (auth()->check()) {
                                          $q->where('ip_address', 'user_' . auth()->id());
                                      } else {
                                          $q->where('ip_address', $ip);
                                      }
                                  })
                                  ->exists();

        if (!$alreadyViewed) {
            $identifier = auth()->check() ? 'user_' . auth()->id() : $ip;
            PostView::create(['post_id' => $post->id, 'ip_address' => $identifier]);
            $post->increment('views');
        }

        // Tri des commentaires
        $sort = $request->get('sort', 'asc');
        $comments = $post->comments()
                         ->whereNull('parent_id')
                         ->with(['replies.likes', 'likes', 'user'])
                         ->orderBy('created_at', $sort)
                         ->get();

        // Données du sondage
        $allCategories = Category::all();
        $pollVotes = PollVote::where('post_id', $post->id)
                             ->selectRaw('category_id, count(*) as total')
                             ->groupBy('category_id')
                             ->pluck('total', 'category_id');
        $userVote = auth()->check()
            ? PollVote::where('post_id', $post->id)->where('user_id', auth()->id())->value('category_id')
            : null;
        $totalPollVotes = $pollVotes->sum();

        return view('show', compact('post', 'comments', 'sort', 'allCategories', 'pollVotes', 'userVote', 'totalPollVotes'));
    }

    // Afficher le formulaire de modification
    public function edit($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $categories = Category::all();

        if (Auth::id() !== $post->user_id) {
            return redirect('/')->with('error', 'Vous ne pouvez pas modifier cet article.');
        }

        return view('edit', compact('post', 'categories'));
    }

    // Enregistrer les modifications dans la base de données
    public function update(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            return redirect('/')->with('error', 'Action non autorisée.');
        }

        $request->validate([
            'title'       => 'required|max:255',
            'content'     => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Régénérer le slug si le titre a changé
        if ($request->title !== $post->title) {
            $newSlug = Str::slug($request->title);
            $originalSlug = $newSlug;
            $count = 1;
            while (Post::where('slug', $newSlug)->where('id', '!=', $post->id)->exists()) {
                $newSlug = $originalSlug . '-' . $count++;
            }
            $post->slug = $newSlug;
        }

        // Gestion de l'image
        $imagePath = $post->image;
        if ($request->hasFile('image')) {
            if ($post->image) {
                \Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // Supprimer l'image si la case est cochée
        if ($request->has('remove_image') && $post->image) {
            \Storage::disk('public')->delete($post->image);
            $imagePath = null;
        }

        $post->update([
            'title'       => $request->title,
            'content'     => $request->content,
            'category_id' => $request->category_id,
            'image'       => $imagePath,
        ]);

        // Notifier tous les utilisateurs sauf l'admin
        $users = User::where('id', '!=', Auth::id())->get();
        foreach ($users as $user) {
            $user->notify(new ArticleUpdatedNotification($post));
        }

        return redirect('/articles/' . $post->slug)->with('success', 'Article modifié avec succès !');
    }

    // Supprimer un article
    public function destroy($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            return redirect('/')->with('error', 'Action non autorisée.');
        }

        $title = $post->title;

        // Supprimer l'image associée
        if ($post->image) {
            \Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        // Notifier tous les utilisateurs
        $users = User::where('id', '!=', Auth::id())->get();
        foreach ($users as $user) {
            $user->notify(new ArticleDeletedNotification($title));
        }

        return redirect('/')->with('success', 'Article supprimé.');
    }
}
