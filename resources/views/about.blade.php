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

    <main class="container mx-auto my-10 px-4 max-w-2xl">

        {{-- Carte profil --}}
        <div class="bg-white p-6 md:p-8 rounded-xl shadow-md border border-gray-200 mb-6 flex items-center gap-6">
            <div style="width:80px;height:80px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="color:white;font-size:28px;font-weight:bold;">G</span>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">GBAGUIDI Emmanuella</h1>
                <p class="text-blue-600 font-medium text-sm mt-1">Développeuse Web · Créatrice de contenu</p>
                <p class="text-gray-500 text-xs mt-1">Bénin 🇧🇯</p>
            </div>
        </div>

        <div class="bg-white p-6 md:p-8 rounded-xl shadow-md border border-gray-200">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-6 border-b-2 border-blue-500 pb-2">
                À propos de moi
            </h2>

            <p class="text-gray-700 leading-relaxed mb-4">
                Bienvenue sur mon espace personnel ! Je suis <strong>GBAGUIDI Emmanuella</strong>, passionnée par la technologie et le développement web. J'ai créé ce blog pour partager mes projets, mes découvertes et mes réflexions du quotidien.
            </p>

            <p class="text-gray-700 leading-relaxed mb-4">
                Mes trois univers de prédilection sont la <strong>technologie</strong>, le <strong>voyage</strong> et le <strong>personnel</strong>. Ils ont tous un point commun : la curiosité. La curiosité d'explorer de nouveaux outils, de nouveaux endroits, et de mieux se comprendre soi-même.
            </p>

            <p class="text-gray-700 leading-relaxed mb-6">
                Ce site est entièrement propulsé par <strong>Laravel</strong> pour le back-end et stylisé avec <strong>Tailwind CSS</strong>. C'est mon terrain d'expérimentation pour concevoir des interfaces modernes et fluides.
            </p>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-blue-700 font-medium">
                    N'hésitez pas à commenter mes articles et à voter pour le prochain sujet qui vous intéresse !
                </p>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 GBAGUIDI Emmanuella - Tous droits réservés.</p>
    </footer>

</body>
</html>
