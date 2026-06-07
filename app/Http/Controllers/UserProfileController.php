<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    // Afficher le profil
    public function show()
    {
        $user = Auth::user();
        $comments = $user->comments()->with('post')->latest()->take(5)->get();
        $reactions = $user->reactions()->with('post')->latest()->take(5)->get();
        return view('profile-user', compact('user', 'comments', 'reactions'));
    }

    // Mettre à jour le profil
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'   => 'required|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'bio'    => 'nullable|max:300',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Gestion de l'avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->bio   = $request->bio;
        $user->save();

        return redirect('/profil')->with('success', 'Profil mis à jour !');
    }

    // Changer le mot de passe
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed|different:current_password',
        ], [
            'password.different' => 'Le nouveau mot de passe doit être différent de l\'ancien.',
            'password.confirmed' => 'Les deux nouveaux mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mot de passe actuel incorrect.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect('/profil')->with('success', 'Mot de passe modifié !');
    }
}
