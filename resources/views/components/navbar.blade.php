<style>
    .nav-desktop { display: flex; }
    .nav-hamburger { display: none; }
    .nav-mobile { display: none; }
    @media (max-width: 767px) {
        .nav-desktop { display: none !important; }
        .nav-hamburger { display: block !important; }
    }
</style>

<nav style="background:white;border-bottom:1px solid #e5e7eb;position:sticky;top:0;z-index:50;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <div style="max-width:1152px;margin:0 auto;padding:16px;display:flex;justify-content:space-between;align-items:center;">
        
        <a href="/" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
            <div style="width:32px;height:32px;background:#2563eb;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="color:white;font-weight:bold;font-size:14px;">B</span>
            </div>
            <span style="font-size:20px;font-weight:bold;color:#111827;">Mon Blog</span>
        </a>

        {{-- Hamburger --}}
        <button class="nav-hamburger" onclick="var m=document.getElementById('nav-mob');m.style.display=m.style.display==='block'?'none':'block';" style="background:none;border:none;cursor:pointer;padding:4px;">
            <svg width="24" height="24" fill="none" stroke="#374151" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Menu desktop --}}
        <ul class="nav-desktop" style="list-style:none;margin:0;padding:0;align-items:center;gap:24px;font-size:14px;font-weight:500;">
            <li><a href="/" style="color:#4b5563;text-decoration:none;">Accueil</a></li>
            <li><a href="/a-propos" style="color:#4b5563;text-decoration:none;">À propos</a></li>
            @auth
                @if(auth()->user()->email === env('ADMIN_EMAIL'))
                    <li><a href="/articles/create" style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;text-decoration:none;font-weight:600;">Écrire</a></li>
                @endif
                <li><a href="/dashboard" style="color:#4b5563;text-decoration:none;">Dashboard</a></li>
                <li>
                    <a href="/notifications" style="position:relative;color:#4b5563;text-decoration:none;display:inline-block;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:white;font-size:10px;font-weight:bold;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="/profil" style="display:flex;align-items:center;gap:8px;color:#4b5563;text-decoration:none;font-weight:600;">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                        @else
                            <div style="width:28px;height:28px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;">
                                <span style="color:white;font-size:12px;font-weight:bold;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        {{ auth()->user()->name }}
                    </a>
                </li>
            @else
                <li><a href="/login" style="border:1px solid #2563eb;color:#2563eb;padding:8px 16px;border-radius:8px;text-decoration:none;font-weight:600;">Connexion</a></li>
            @endauth
        </ul>
    </div>

    {{-- Menu mobile --}}
    <div id="nav-mob" style="display:none;background:white;border-top:1px solid #f3f4f6;padding:8px 16px 16px;">
        <a href="/" style="display:block;padding:10px 0;color:#4b5563;text-decoration:none;border-bottom:1px solid #f9fafb;">Accueil</a>
        <a href="/a-propos" style="display:block;padding:10px 0;color:#4b5563;text-decoration:none;border-bottom:1px solid #f9fafb;">À propos</a>
        @auth
            @if(auth()->user()->email === env('ADMIN_EMAIL'))
                <a href="/articles/create" style="display:block;padding:10px 0;color:#2563eb;text-decoration:none;font-weight:600;border-bottom:1px solid #f9fafb;">Écrire un article</a>
            @endif
            <a href="/dashboard" style="display:block;padding:10px 0;color:#4b5563;text-decoration:none;border-bottom:1px solid #f9fafb;">Dashboard</a>
            <a href="/notifications" style="display:block;padding:10px 0;color:#4b5563;text-decoration:none;border-bottom:1px solid #f9fafb;">
                Notifications
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span style="background:#ef4444;color:white;font-size:10px;font-weight:bold;border-radius:9999px;padding:2px 6px;margin-left:6px;">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            <a href="/profil" style="display:block;padding:10px 0;color:#374151;text-decoration:none;font-weight:600;">{{ auth()->user()->name }}</a>
        @else
            <a href="/login" style="display:block;padding:10px 0;color:#2563eb;text-decoration:none;font-weight:600;">Connexion</a>
        @endauth
    </div>
</nav>
