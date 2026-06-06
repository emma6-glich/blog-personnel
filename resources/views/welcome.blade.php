<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Blog Personnel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .hero-gradient { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 50%, #60a5fa 100%); }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

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
                <li><a href="/" class="text-blue-600 font-semibold">Accueil</a></li>
                <li><a href="/a-propos" class="text-gray-600 hover:text-blue-600 transition">À propos</a></li>
                @auth
                    @if(auth()->user()->email === env('ADMIN_EMAIL'))
                        <li>
                            <a href="/articles/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                                ✏️ Écrire
                            </a>
                        </li>
                    @endif
                    <li>
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-500 transition cursor-pointer">
                                Déconnexion
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="/login" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold">
                            Connexion
                        </a>
                    </li>
                    <li>
                        <a href="/register" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            S'inscrire
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    <x-flash-messages />

    {{-- HERO SECTION --}}
    @if(!request('search') && !request('category'))
    <section class="hero-gradient text-white py-20 px-6">
        <div class="container mx-auto max-w-4xl text-center">
            <span class="inline-block bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4 uppercase tracking-widest">
                Blog Personnel
            </span>
            <h1 class="text-5xl font-extrabold mb-4 leading-tight">
                Bienvenue sur mon espace
            </h1>
            <p class="text-blue-100 text-lg max-w-xl mx-auto mb-8">
                Découvrez mes articles sur la technologie, mes projets et mes réflexions du quotidien.
            </p>
            <a href="#articles" class="bg-white text-blue-600 font-bold px-6 py-3 rounded-full hover:bg-blue-50 transition shadow-lg inline-block">
                Lire les articles ↓
            </a>
        </div>
    </section>
    @endif

    <main class="container mx-auto px-6 py-12 max-w-6xl" id="articles">

        {{-- BARRE DE RECHERCHE + FILTRES --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-10">
            <form method="GET" action="/" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher un article..."
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <select name="category" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-white">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition text-sm">
                    Rechercher
                </button>
                @if(request('search') || request('category'))
                    <a href="/" class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition text-sm flex items-center justify-center">
                        ✕ Effacer
                    </a>
                @endif
            </form>
        </div>

        {{-- TITRE SECTION --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">
                    @if(request('search') || request('category'))
                        Résultats de recherche
                    @else
                        Derniers articles
                    @endif
                </h2>
                @if(request('search') || request('category'))
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $posts->total() }} article(s) trouvé(s)
                        @if(request('search')) pour "<strong>{{ request('search') }}</strong>"@endif
                    </p>
                @else
                    <p class="text-sm text-gray-500 mt-1">{{ $posts->total() }} article(s) publié(s)</p>
                @endif
            </div>
        </div>

        {{-- GRILLE D'ARTICLES --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <article class="bg-white rounded-2xl shadow-sm border border-gray-200 card-hover overflow-hidden flex flex-col">

                    {{-- Image ou placeholder coloré --}}
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}"
                            class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <span class="text-white text-5xl opacity-30">✍️</span>
                        </div>
                    @endif

                    <div class="p-5 flex flex-col flex-1">
                        {{-- Catégorie --}}
                        <span class="inline-block bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3 w-fit">
                            {{ $post->category ? $post->category->name : 'Non classé' }}
                        </span>

                        {{-- Titre --}}
                        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug hover:text-blue-600 transition">
                            <a href="/articles/{{ $post->slug }}">{{ $post->title }}</a>
                        </h3>

                        {{-- Extrait --}}
                        <p class="text-gray-500 text-sm leading-relaxed flex-1">
                            {{ Str::limit($post->content, 100) }}
                        </p>

                        {{-- Footer carte --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <div class="text-xs text-gray-400">
                                <span class="block">{{ $post->created_at->format('d M Y') }}</span>
                                <span class="flex items-center gap-1 mt-0.5">👁 {{ $post->views }} vue{{ $post->views > 1 ? 's' : '' }}</span>
                            </div>
                            <a href="/articles/{{ $post->slug }}"
                                class="bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Lire →
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center py-16 bg-white rounded-2xl border border-gray-200">
                    @if(request('search') || request('category'))
                        <p class="text-4xl mb-4">🔍</p>
                        <p class="text-gray-500 text-lg font-medium">Aucun article ne correspond à ta recherche.</p>
                        <a href="/" class="mt-4 inline-block bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700">
                            Voir tous les articles
                        </a>
                    @else
                        <p class="text-4xl mb-4">✍️</p>
                        <p class="text-gray-500 text-lg font-medium">Aucun article publié pour le moment.</p>
                        @auth
                            @if(auth()->user()->email === env('ADMIN_EMAIL'))
                                <a href="/articles/create" class="mt-4 inline-block bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700">
                                    Écrire le premier article
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($posts->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $posts->appends(request()->query())->links() }}
            </div>
        @endif

    </main>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-gray-400 mt-20">
        <div class="container mx-auto px-6 py-10 max-w-6xl">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xs">B</span>
                    </div>
                    <span class="text-white font-semibold">Mon Blog Personnel</span>
                </div>
                <div class="flex gap-6 text-sm">
                    <a href="/" class="hover:text-white transition">Accueil</a>
                    <a href="/a-propos" class="hover:text-white transition">À propos</a>
                </div>
                <p class="text-sm">&copy; 2026 — Tous droits réservés.</p>
            </div>
        </div>
    </footer>

</body>
</html>
