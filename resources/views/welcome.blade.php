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
        <div class="container mx-auto px-4 md:px-6 py-4 flex justify-between items-center max-w-6xl">
            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">B</span>
                </div>
                <span class="text-xl font-bold text-gray-900">Mon Blog</span>
            </a>

            {{-- Menu hamburger mobile --}}
            <button id="menu-toggle" class="md:hidden text-gray-600 focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Menu desktop --}}
            <ul class="hidden md:flex items-center gap-6 font-medium text-sm">
                <li><a href="/" class="text-blue-600 font-semibold">Accueil</a></li>
                <li><a href="/a-propos" class="text-gray-600 hover:text-blue-600 transition">À propos</a></li>
                @auth
                    @if(auth()->user()->email === env('ADMIN_EMAIL'))
                        <li><a href="/articles/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Écrire</a></li>
                    @endif
                    <li><a href="/dashboard" class="text-gray-600 hover:text-blue-600 transition">Dashboard</a></li>
                    <li>
                        <div class="relative" id="notif-dropdown-wrapper">
                            <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')"
                                class="relative text-gray-600 hover:text-blue-600 transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                                        {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            <div id="notif-dropdown" class="hidden absolute right-0 top-8 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                                <div class="p-3 border-b border-gray-100 flex justify-between items-center">
                                    <span class="font-semibold text-sm text-gray-800">Notifications</span>
                                    <a href="/notifications" class="text-xs text-blue-600 hover:underline">Voir tout</a>
                                </div>
                                <div class="max-h-72 overflow-y-auto">
                                    @forelse(auth()->user()->notifications()->take(5)->get() as $notif)
                                        <a href="{{ $notif->data['url'] ?? '/' }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 {{ $notif->read_at ? '' : 'bg-blue-50' }}">
                                            <p class="text-xs {{ $notif->read_at ? 'text-gray-600' : 'text-blue-800 font-semibold' }}">{{ $notif->data['message'] }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <p class="text-center text-sm text-gray-400 py-6">Aucune notification</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="/profil" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-7 h-7 rounded-full object-cover">
                            @else
                                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <span class="hidden lg:block">{{ auth()->user()->name }}</span>
                        </a>
                    </li>
                @else
                    <li><a href="/login" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold">Connexion</a></li>
                @endauth
            </ul>
        </div>

        {{-- Menu mobile --}}
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-4 py-3 space-y-2 text-sm font-medium">
            <a href="/" class="block py-2 text-blue-600 font-semibold">Accueil</a>
            <a href="/a-propos" class="block py-2 text-gray-600">À propos</a>
            @auth
                @if(auth()->user()->email === env('ADMIN_EMAIL'))
                    <a href="/articles/create" class="block py-2 text-blue-600">Écrire un article</a>
                @endif
                <a href="/dashboard" class="block py-2 text-gray-600">Dashboard</a>
                <a href="/notifications" class="block py-2 text-gray-600">
                    Notifications
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="inline-block bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 ml-1">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
                <a href="/profil" class="block py-2 text-gray-600">Mon Profil ({{ auth()->user()->name }})</a>
            @else
                <a href="/login" class="block py-2 text-blue-600 font-semibold">Connexion</a>
            @endauth
        </div>
    </nav>

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
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover">
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
