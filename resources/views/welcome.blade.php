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
    <x-navbar />

    <x-flash-messages />

    {{-- HERO SECTION --}}
    @if(!request('search') && !request('category'))
    <section class="hero-gradient text-white py-14 md:py-20 px-4">
        <div class="container mx-auto max-w-4xl text-center">
            <span class="inline-block bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4 uppercase tracking-widest">Blog Personnel</span>
            <h1 class="text-3xl md:text-5xl font-extrabold mb-4 leading-tight">Bienvenue sur mon espace</h1>
            <p class="text-blue-100 text-base md:text-lg max-w-xl mx-auto mb-8">
                Découvrez mes articles sur la technologie, mes projets et mes réflexions du quotidien.
            </p>
            <a href="#articles" class="bg-white text-blue-600 font-bold px-6 py-3 rounded-full hover:bg-blue-50 transition shadow-lg inline-block">
                Lire les articles
            </a>
        </div>
    </section>
    @endif

    <main class="container mx-auto px-4 md:px-6 py-8 md:py-12 max-w-6xl" id="articles">

        {{-- BARRE DE RECHERCHE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-6 mb-8">
            <form method="GET" action="/" class="flex flex-col gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher un article..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                <div class="flex gap-3">
                    <select name="category" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-white">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition text-sm">
                        Rechercher
                    </button>
                    @if(request('search') || request('category'))
                        <a href="/" class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition text-sm flex items-center">
                            Effacer
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TITRE --}}
        <div class="mb-6">
            <h2 class="text-xl md:text-2xl font-extrabold text-gray-900">
                @if(request('search') || request('category')) Résultats @else Derniers articles @endif
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ $posts->total() }} article(s)</p>
        </div>

        {{-- GRILLE D'ARTICLES --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @forelse($posts as $post)
                <article class="bg-white rounded-2xl shadow-sm border border-gray-200 card-hover overflow-hidden flex flex-col">
                    @if($post->image)
                        <img src="{{ Str::startsWith($post->image, 'http') ? $post->image : asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover">
                    @else
                        <div class="w-full h-44 bg-gradient-to-br from-blue-400 to-blue-600"></div>
                    @endif
                    <div class="p-4 md:p-5 flex flex-col flex-1">
                        <span class="inline-block bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3 w-fit">
                            {{ $post->category ? $post->category->name : 'Non classé' }}
                        </span>
                        <h3 class="text-base md:text-lg font-bold text-gray-900 mb-2 leading-snug hover:text-blue-600 transition">
                            <a href="/articles/{{ $post->slug }}">{{ $post->title }}</a>
                        </h3>
                        <p class="text-gray-500 text-sm leading-relaxed flex-1">{{ Str::limit($post->content, 100) }}</p>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <div class="text-xs text-gray-400">
                                <span class="block">{{ $post->created_at->format('d M Y') }}</span>
                                <span class="block mt-0.5">{{ $post->views }} vue{{ $post->views > 1 ? 's' : '' }}</span>
                            </div>
                            <a href="/articles/{{ $post->slug }}" class="bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Lire
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center py-16 bg-white rounded-2xl border border-gray-200">
                    <p class="text-gray-500 text-lg font-medium">
                        @if(request('search') || request('category')) Aucun article ne correspond. @else Aucun article publié pour le moment. @endif
                    </p>
                    @if(request('search') || request('category'))
                        <a href="/" class="mt-4 inline-block bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700">Voir tous les articles</a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($posts->hasPages())
            <div class="mt-10 flex justify-center">{{ $posts->appends(request()->query())->links() }}</div>
        @endif

    </main>

    <footer class="bg-gray-900 text-gray-400 mt-16">
        <div class="container mx-auto px-4 md:px-6 py-8 max-w-6xl">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
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

    <script>
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notif-dropdown-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('notif-dropdown')?.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
