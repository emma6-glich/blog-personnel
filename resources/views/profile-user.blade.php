<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center max-w-6xl">
            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">B</span>
                </div>
                <span class="text-xl font-bold text-gray-900">Mon Blog</span>
            </a>
            <ul class="flex items-center gap-6 font-medium text-sm">
                <li><a href="/" class="text-gray-600 hover:text-blue-600 transition">Accueil</a></li>
                <li><a href="/dashboard" class="text-gray-600 hover:text-blue-600 transition">Dashboard</a></li>
            </ul>
        </div>
    </nav>

    <x-flash-messages />

    <main class="container mx-auto px-6 py-10 max-w-4xl">

        {{-- En-tête profil --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    {{-- Avatar --}}
                    <div class="relative">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                class="w-24 h-24 rounded-full object-cover border-4 border-blue-100">
                        @else
                            <div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center border-4 border-blue-100">
                                <span class="text-white text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                        @if($user->bio)
                            <p class="text-gray-600 text-sm mt-2 italic">{{ $user->bio }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Membre depuis {{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Boutons déconnexion et changer de compte --}}
                <div class="flex flex-col gap-3">
                    {{-- Déconnexion --}}
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-xl font-semibold text-sm hover:bg-red-100 transition cursor-pointer w-full justify-center">
                            🚪 Déconnexion
                        </button>
                    </form>

                    {{-- Changer de compte --}}
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 bg-gray-50 text-gray-600 border border-gray-200 px-4 py-2 rounded-xl font-semibold text-sm hover:bg-gray-100 transition cursor-pointer w-full justify-center">
                            🔄 Changer de compte
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Modifier le profil --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Modifier mon profil</h2>
                <form action="/profil" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Photo de profil</label>
                        <input type="file" name="avatar" accept="image/*"
                            class="w-full p-2 border border-gray-300 rounded-xl text-sm focus:outline-none">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WEBP — max 2 Mo</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nom complet</label>
                        <input type="text" name="name" value="{{ $user->name }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bio <span class="text-gray-400 font-normal">(optionnel)</span></label>
                        <textarea name="bio" rows="3" maxlength="300" placeholder="Dis quelques mots sur toi..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">{{ $user->bio }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition">
                        Sauvegarder
                    </button>
                </form>
            </div>

            {{-- Changer le mot de passe --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Changer le mot de passe</h2>
                    <form action="/profil/password" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe actuel</label>
                            <div class="relative mt-1">
                                <input type="password" id="current_password" name="current_password" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm pr-10">
                                <button type="button"
                                    onclick="const p=document.getElementById('current_password');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'👁':'🙈';"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 cursor-pointer">👁</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nouveau mot de passe</label>
                            <div class="relative mt-1">
                                <input type="password" id="new_password" name="password" required minlength="8"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm pr-10">
                                <button type="button"
                                    onclick="const p=document.getElementById('new_password');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'👁':'🙈';"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 cursor-pointer">👁</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                            <div class="relative mt-1">
                                <input type="password" id="confirm_password" name="password_confirmation" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm pr-10">
                                <button type="button"
                                    onclick="const p=document.getElementById('confirm_password');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'👁':'🙈';"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 cursor-pointer">👁</button>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-gray-800 text-white py-2.5 rounded-xl font-semibold hover:bg-gray-900 transition">
                            Changer le mot de passe
                        </button>
                    </form>
                </div>

                {{-- Mes commentaires récents --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Mes commentaires récents</h2>
                    <div class="space-y-3">
                        @forelse($comments as $comment)
                            <div class="py-2 border-b border-gray-100 last:border-0">
                                <p class="text-xs text-gray-400">Sur "{{ Str::limit($comment->post->title ?? '...', 30) }}"</p>
                                <p class="text-sm text-gray-700 mt-0.5">{{ Str::limit($comment->content, 60) }}</p>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm">Aucun commentaire.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20">
        <p>&copy; 2026 Mon Blog Personnel — Tous droits réservés.</p>
    </footer>

</body>
</html>
