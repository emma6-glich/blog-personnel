<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wide">Mon Blog Personnel</h1>
            <ul class="flex space-x-6 font-medium items-center">
                <li><a href="/" class="hover:underline">Accueil</a></li>
                @auth
                    <li><a href="/articles/create" class="hover:underline">Écrire un article</a></li>
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
                @endauth
            </ul>
        </div>
    </nav>

    <main class="container mx-auto my-20 px-4 max-w-2xl text-center">
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-12">

            <p class="text-9xl font-extrabold text-blue-500 mb-4">404</p>

            <h2 class="text-3xl font-bold text-gray-900 mb-3">Page introuvable</h2>

            <p class="text-gray-500 text-lg mb-8">
                Oups ! La page que tu cherches n'existe pas ou a été déplacée.
            </p>

            <a href="/" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 shadow transition inline-block">
                &larr; Retour à l'accueil
            </a>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 Mon Blog Personnel - Tous droits réservés.</p>
    </footer>

</body>
</html>
