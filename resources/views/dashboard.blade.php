<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">

    {{-- NAVBAR --}}
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
                <li><a href="/a-propos" class="text-gray-600 hover:text-blue-600 transition">À propos</a></li>
                <li><a href="/profil" class="text-gray-600 hover:text-blue-600 transition font-semibold">Mon Profil</a></li>
                <li>
                    <a href="/notifications" class="relative text-gray-600 hover:text-blue-600 transition">
                        🔔
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                </li>
                @if($isAdmin)
                    <li>
                        <a href="/articles/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            ✏️ Écrire
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>

    <x-flash-messages />

    <main class="container mx-auto px-6 py-10 max-w-6xl">

        {{-- En-tête --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Bonjour, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-gray-500 mt-1 text-sm">
                    {{ $isAdmin ? 'Tableau de bord administrateur' : 'Tableau de bord utilisateur' }}
                </p>
            </div>
        </div>

        @if($isAdmin)
        {{-- ===== VUE ADMIN ===== --}}

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-blue-600">{{ $totalPosts }}</p>
                <p class="text-sm text-gray-500 mt-1">Articles</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-green-600">{{ $totalComments }}</p>
                <p class="text-sm text-gray-500 mt-1">Commentaires</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-purple-600">{{ number_format($totalViews) }}</p>
                <p class="text-sm text-gray-500 mt-1">Vues totales</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm text-center">
                <p class="text-3xl font-extrabold text-orange-500">{{ $totalUsers }}</p>
                <p class="text-sm text-gray-500 mt-1">Utilisateurs</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Derniers articles --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Derniers articles</h2>
                    <a href="/articles/create" class="text-sm text-blue-600 hover:underline font-medium">+ Nouvel article</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentPosts as $post)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                            <div>
                                <a href="/articles/{{ $post->slug }}" class="text-sm font-medium text-gray-800 hover:text-blue-600">
                                    {{ Str::limit($post->title, 40) }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $post->created_at->format('d/m/Y') }} · 👁 {{ $post->views }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="/articles/{{ $post->slug }}/edit" class="text-xs text-blue-500 hover:underline">Modifier</a>
                                <form action="/articles/{{ $post->slug }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline cursor-pointer">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">Aucun article.</p>
                    @endforelse
                </div>
            </div>

            {{-- Derniers commentaires --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Derniers commentaires</h2>
                <div class="space-y-3">
                    @forelse($recentComments as $comment)
                        <div class="py-2 border-b border-gray-100 last:border-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-sm font-medium text-gray-800">{{ $comment->pseudo }}</span>
                                    <span class="text-xs text-gray-400 ml-2">sur "{{ Str::limit($comment->post->title ?? '...', 25) }}"</span>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($comment->content, 60) }}</p>
                                </div>
                                <form action="/comments/{{ $comment->id }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline cursor-pointer">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">Aucun commentaire.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @else
        {{-- ===== VUE UTILISATEUR SIMPLE ===== --}}

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Mes commentaires --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Mes commentaires</h2>
                <div class="space-y-3">
                    @forelse($myComments as $comment)
                        <div class="py-2 border-b border-gray-100 last:border-0">
                            <p class="text-xs text-gray-400">Sur "{{ Str::limit($comment->post->title ?? '...', 30) }}"</p>
                            <p class="text-sm text-gray-700 mt-0.5">{{ Str::limit($comment->content, 80) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at->format('d/m/Y') }}</p>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">Tu n'as pas encore commenté.</p>
                    @endforelse
                </div>
            </div>

            {{-- Mes réactions --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Mes réactions</h2>
                <div class="space-y-3">
                    @forelse($myReactions as $reaction)
                        <div class="py-2 border-b border-gray-100 last:border-0 flex items-center gap-3">
                            <span class="text-2xl">{{ $reaction->emoji }}</span>
                            <div>
                                <a href="/articles/{{ $reaction->post->slug ?? '#' }}" class="text-sm font-medium text-gray-800 hover:text-blue-600">
                                    {{ Str::limit($reaction->post->title ?? '...', 40) }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $reaction->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">Tu n'as pas encore réagi à un article.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition shadow-sm inline-block">
                Voir les articles →
            </a>
        </div>

        @endif

    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 Mon Blog Personnel — Tous droits réservés.</p>
    </footer>

</body>
</html>
