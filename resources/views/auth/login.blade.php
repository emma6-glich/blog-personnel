<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mon Blog</title>
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
            <p class="text-gray-500 mt-2 text-sm">Connecte-toi pour accéder à ton espace</p>
        </div>

        {{-- Carte --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            <h2 class="text-xl font-bold text-gray-900 mb-6">Connexion</h2>

            {{-- Status --}}
            @if (session('status'))
                <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
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

                {{-- Remember me + Forgot password --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600">
                        Se souvenir de moi
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                {{-- Bouton connexion --}}
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition shadow-sm">
                    Se connecter
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Pas encore de compte ?
                <a href="/register" class="text-blue-600 font-semibold hover:underline">S'inscrire</a>
            </p>
        </div>

        <p class="text-center mt-6">
            <a href="/" class="text-sm text-gray-400 hover:text-blue-600 transition">&larr; Retour à l'accueil</a>
        </p>
    </div>

</body>
</html>
