<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
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

// Ajouter un commentaire (connecté uniquement)
Route::post('/articles/{slug}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');

// Réactions (connecté uniquement)
Route::post('/articles/{slug}/reactions', [ReactionController::class, 'store'])->name('reactions.store')->middleware('auth');

// Supprimer un commentaire (auteur ou admin)
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');

// Modifier un commentaire (auteur ou admin)
Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update')->middleware('auth');

// ========================================
// ROUTES PROTÉGÉES (nécessite connexion)
// ========================================

Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');
    
    // Gestion du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
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
