<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PollController;
use Illuminate\Support\Facades\Route;

// ========================================
// ROUTES PUBLIQUES (accessibles à tous)
// ========================================

// Page d'accueil avec la liste des articles
Route::get('/', [PostController::class, 'index'])->name('home');

// Redirection si quelqu'un accède directement à /articles
Route::get('/articles', function () {
    return redirect('/');
});

// Page à propos
Route::get('/a-propos', function () {
    return view('about');
})->name('about');

// Afficher le formulaire de création (AVANT /{id} pour éviter le conflit)
Route::get('/articles/create', [PostController::class, 'create'])->name('posts.create')->middleware('auth');

// Voir un article spécifique avec ses commentaires
Route::get('/articles/{slug}', [PostController::class, 'show'])->name('posts.show');

// Recherche utilisateurs pour le tag @
Route::get('/api/users/search', function(\Illuminate\Http\Request $request) {
    $query = $request->get('q', '');
    $users = \App\Models\User::where('name', 'like', $query . '%')
                ->where('id', '!=', auth()->id()) // Exclure l'utilisateur connecté
                ->select('id', 'name')
                ->take(5)
                ->get();
    return response()->json($users);
})->middleware('auth');

// Réactions (connecté uniquement)
Route::post('/articles/{slug}/reactions', [ReactionController::class, 'store'])->name('reactions.store')->middleware('auth');

// Ajouter un commentaire (connecté uniquement)
Route::post('/articles/{slug}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');

// Liker un commentaire
Route::post('/comments/{id}/like', [CommentLikeController::class, 'toggle'])->name('comments.like')->middleware('auth');

// Sondage
Route::post('/articles/{slug}/poll', [PollController::class, 'vote'])->name('poll.vote')->middleware('auth');

// Supprimer un commentaire (auteur ou admin)
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');

// Modifier un commentaire (auteur ou admin)
Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update')->middleware('auth');

// ========================================
// ROUTES PROTÉGÉES (nécessite connexion)
// ========================================

Route::middleware('auth')->group(function () {
    
    // Dashboard personnalisé
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $isAdmin = $user->email === env('ADMIN_EMAIL');

        if ($isAdmin) {
            $totalPosts     = \App\Models\Post::count();
            $totalComments  = \App\Models\Comment::count();
            $totalViews     = \App\Models\Post::sum('views');
            $totalUsers     = \App\Models\User::count();
            $recentPosts    = \App\Models\Post::latest()->take(5)->get();
            $recentComments = \App\Models\Comment::with('post')->latest()->take(5)->get();
            return view('dashboard', compact('isAdmin', 'totalPosts', 'totalComments', 'totalViews', 'totalUsers', 'recentPosts', 'recentComments'));
        } else {
            $myComments  = \App\Models\Comment::where('user_id', $user->id)->with('post')->latest()->take(5)->get();
            $myReactions = \App\Models\Reaction::where('user_id', $user->id)->with('post')->latest()->take(5)->get();
            return view('dashboard', compact('isAdmin', 'myComments', 'myReactions'));
        }
    })->middleware('verified')->name('dashboard');
    
    // Profil utilisateur
    Route::get('/profil', [UserProfileController::class, 'show'])->name('user.profile');
    Route::post('/profil', [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/profil/password', [UserProfileController::class, 'updatePassword'])->name('user.profile.password');

    // Notifications
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->paginate(15);
        auth()->user()->unreadNotifications->markAsRead();
        return view('notifications', compact('notifications'));
    })->name('notifications');

    Route::delete('/notifications/{id}', function ($id) {
        auth()->user()->notifications()->findOrFail($id)->delete();
        return back()->with('success', 'Notification supprimée.');
    })->name('notifications.destroy');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ========================================
    // GESTION DES ARTICLES (CRUD)
    // ========================================
    
    // Enregistrer un nouvel article
    Route::post('/articles', [PostController::class, 'store'])->name('posts.store');
    
    // Afficher le formulaire de modification
    Route::get('/articles/{slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    
    // Mettre à jour un article
    Route::put('/articles/{slug}', [PostController::class, 'update'])->name('posts.update');
    
    // Supprimer un article
    Route::delete('/articles/{slug}', [PostController::class, 'destroy'])->name('posts.destroy');
});

require __DIR__.'/auth.php';
