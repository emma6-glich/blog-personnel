<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'article - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wide">Mon Blog Personnel</h1>
            <ul class="flex space-x-6 font-medium items-center">
                <li><a href="/" class="hover:underline">Accueil</a></li>
                @auth
                    @if(auth()->user()->email === env('ADMIN_EMAIL'))
                        <li><a href="/articles/create" class="hover:underline">Écrire un article</a></li>
                    @endif
                @endauth
                <li><a href="/a-propos" class="hover:underline">À propos</a></li>
                @auth
                    <li>
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="hover:underline cursor-pointer">Déconnexion</button>
                        </form>
                    </li>
                @else
                    <li><a href="/login" class="hover:underline">Connexion</a></li>
                    <li><a href="/register" class="hover:underline">S'inscrire</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <x-flash-messages />

    <main class="container mx-auto my-10 px-4 max-w-2xl bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <div class="mb-6">
            <a href="/articles/{{ $post->slug }}" class="text-blue-600 hover:underline font-medium">&larr; Retour à l'article</a>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-900 mb-6 pb-2 border-b border-gray-200">
            Modifier l'article
        </h2>

        <form action="/articles/{{ $post->slug }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Catégorie</label>
                <select id="category_id" name="category_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucune catégorie --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Titre de l'article</label>
                <input type="text" id="title" name="title" value="{{ $post->title }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-lg">
            </div>

            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">Contenu de l'article</label>
                <textarea id="content" name="content" rows="8" required 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-base leading-relaxed">{{ $post->content }}</textarea>
            </div>

            <div>
                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Image à la une <span class="text-gray-400 font-normal">(optionnel)</span></label>
                @if($post->image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $post->image) }}" alt="Image actuelle" class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">Image actuelle — uploader une nouvelle pour la remplacer</p>
                        <label class="flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_image" value="1" class="rounded">
                            Supprimer cette image
                        </label>
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/*"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WEBP — max 2 Mo</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 shadow transition cursor-pointer">
                    Sauvegarder les modifications
                </button>
            </div>
        </form>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 Mon Blog Personnel - Tous droits réservés.</p>
    </footer>

</body>
</html>