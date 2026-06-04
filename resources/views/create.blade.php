<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Article - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <!-- Barre de navigation -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wide">Mon Blog Personnel</h1>
            <ul class="flex space-x-6 font-medium">
                <li><a href="/" class="hover:underline">Accueil</a></li>
                <li><a href="#" class="hover:underline">Articles</a></li>
                <li><a href="/a-propos" class="hover:underline">À propos</a></li>
            </ul>
        </div>
    </nav>

    <x-flash-messages />

    <!-- Formulaire de création -->
    <main class="container mx-auto my-10 px-4 max-w-2xl bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">
            Nouvel Article
        </h2>

       <form action="/articles" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
            <!-- Champ Catégorie -->
            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Catégorie</label>
                <select id="category_id" name="category_id"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Aucune catégorie --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Champ Titre -->
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Titre de l'article</label>
                <input type="text" id="title" name="title" placeholder="Ex: Mon premier article sur Laravel" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Champ Contenu -->
            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-1">Contenu</label>
                <textarea id="content" name="content" rows="6" placeholder="Écrivez votre texte ici..." 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <!-- Champ Image -->
            <div>
                <label for="image" class="block text-sm font-semibold text-gray-700 mb-1">Image à la une <span class="text-gray-400 font-normal">(optionnel)</span></label>
                <input type="file" id="image" name="image" accept="image/*"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WEBP — max 2 Mo</p>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-4">
                <a href="/" class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg font-medium hover:bg-gray-300 transition">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-blue-700 shadow transition">
                    Publier l'article
                </button>
            </div>
        </form>
    </main>

    <!-- Pied de page -->
    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 Mon Blog Personnel - Tous droits réservés.</p>
    </footer>

</body>
</html>