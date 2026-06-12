<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans overflow-x-hidden">

    <x-navbar />

    <main class="container mx-auto my-10 px-4 max-w-2xl bg-white p-6 md:p-8 rounded-xl shadow-md border border-gray-200">
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