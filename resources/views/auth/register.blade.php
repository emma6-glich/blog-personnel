<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-6">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-lg">B</span>
                </div>
                <span class="text-2xl font-bold text-gray-900">Mon Blog</span>
            </a>
            <p class="text-gray-500 mt-2 text-sm">Crée ton compte pour rejoindre la communauté</p>
        </div>

        {{-- Carte --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            <h2 class="text-xl font-bold text-gray-900 mb-6">Créer un compte</h2>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Nom --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nom complet</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe</label>
                    <div class="relative mt-1">
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm !pr-10">
                        <button type="button"
                            onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password'; this.textContent = p.type === 'password' ? '👁' : '🙈';"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 cursor-pointer">
                            👁
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmer mot de passe --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmer le mot de passe</label>
                    <div class="relative mt-1">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm !pr-10">
                        <button type="button"
                            onclick="const p = document.getElementById('password_confirmation'); p.type = p.type === 'password' ? 'text' : 'password'; this.textContent = p.type === 'password' ? '👁' : '🙈';"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 cursor-pointer">
                            👁
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bouton inscription --}}
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition shadow-sm">
                    Créer mon compte
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Déjà un compte ?
                <a href="/login" class="text-blue-600 font-semibold hover:underline">Se connecter</a>
            </p>
        </div>

        <p class="text-center mt-6">
            <a href="/" class="text-sm text-gray-400 hover:text-blue-600 transition">&larr; Retour à l'accueil</a>
        </p>
    </div>

</body>
</html>
