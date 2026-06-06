<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Mon Blog</title>
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
                    <li><a href="/register" class="hover:underline">S'inscrire</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <main class="container mx-auto my-10 px-4 max-w-2xl bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">
            À propos de moi
        </h2>
        
        <p class="text-gray-700 leading-relaxed mb-4">
            Bienvenue sur mon espace personnel ! Passionnée par la technologie et le développement, j'ai créé ce blog pour partager mes projets, mes sessions de code et mes découvertes au quotidien.
        </p>

        <p class="text-gray-700 leading-relaxed mb-6">
            Ce site est entièrement propulsé par <strong>Laravel</strong> pour le Back-end et stylisé avec <strong>Tailwind CSS</strong>. C'est mon terrain d'expérimentation pour concevoir des interfaces modernes et fluides.
        </p>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <p class="text-sm text-blue-700 font-medium">
                💡 Prochaine étape visuelle : Créer une page contenant un formulaire pour ajouter de nouveaux articles !
            </p>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 Mon Blog Personnel - Tous droits réservés.</p>
    </footer>

</body>
</html>