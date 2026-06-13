<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-4 md:px-6 py-4 flex justify-between items-center max-w-6xl">
        <a href="/" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">B</span>
            </div>
            <span class="text-xl font-bold text-gray-900">Mon Blog</span>
        </a>

        {{-- Hamburger --}}
        <button class="md:hidden text-gray-600" onclick="document.getElementById('navbar-mobile').classList.toggle('hidden')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Desktop --}}
        <ul class="hidden md:flex items-center gap-6 font-medium text-sm">
            <li><a href="/" class="text-gray-600 hover:text-blue-600 transition">Accueil</a></li>
            <li><a href="/a-propos" class="text-gray-600 hover:text-blue-600 transition">À propos</a></li>
            @auth
                @if(auth()->user()->email === env('ADMIN_EMAIL'))
                    <li><a href="/articles/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Écrire</a></li>
                @endif
                <li><a href="/dashboard" class="text-gray-600 hover:text-blue-600 transition">Dashboard</a></li>
                <li>
                    <a href="/notifications" class="relative text-gray-600 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                </li>
                <li><a href="/profil" class="text-gray-600 hover:text-blue-600 transition font-semibold">{{ auth()->user()->name }}</a></li>
            @else
                <li><a href="/login" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition font-semibold">Connexion</a></li>
            @endauth
        </ul>
    </div>

    {{-- Mobile --}}
    <div id="navbar-mobile" class="hidden md:hidden bg-white border-t border-gray-100 px-4 py-3 space-y-2 text-sm font-medium">
        <a href="/" class="block py-2 text-gray-600" onclick="document.getElementById('navbar-mobile').classList.add('hidden')">Accueil</a>
        <a href="/a-propos" class="block py-2 text-gray-600" onclick="document.getElementById('navbar-mobile').classList.add('hidden')">À propos</a>
        @auth
            @if(auth()->user()->email === env('ADMIN_EMAIL'))
                <a href="/articles/create" class="block py-2 text-blue-600" onclick="document.getElementById('navbar-mobile').classList.add('hidden')">Écrire un article</a>
            @endif
            <a href="/dashboard" class="block py-2 text-gray-600" onclick="document.getElementById('navbar-mobile').classList.add('hidden')">Dashboard</a>
            <a href="/notifications" class="block py-2 text-gray-600" onclick="document.getElementById('navbar-mobile').classList.add('hidden')">Notifications
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="inline-block bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 ml-1">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            <a href="/profil" class="block py-2 font-semibold text-gray-700" onclick="document.getElementById('navbar-mobile').classList.add('hidden')">{{ auth()->user()->name }}</a>
        @else
            <a href="/login" class="block py-2 text-blue-600 font-semibold" onclick="document.getElementById('navbar-mobile').classList.add('hidden')">Connexion</a>
        @endauth
    </div>
</nav>
