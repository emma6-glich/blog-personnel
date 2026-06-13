<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">

    <x-navbar />

    <x-flash-messages />

    <main class="container mx-auto px-6 py-10 max-w-3xl">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-extrabold text-gray-900">🔔 Notifications</h1>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="/notifications/read-all" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:underline cursor-pointer">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                <div class="bg-white rounded-xl border {{ $notification->read_at ? 'border-gray-200' : 'border-blue-300 bg-blue-50' }} shadow-sm p-4 flex items-start justify-between gap-4">
                    <a href="{{ $notification->data['url'] ?? '/' }}" class="flex-1">
                        <p class="text-sm {{ $notification->read_at ? 'text-gray-600' : 'text-blue-800 font-semibold' }}">
                            {{ $notification->data['message'] }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                    <form action="/notifications/{{ $notification->id }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-300 hover:text-red-500 text-lg cursor-pointer">&times;</button>
                    </form>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-200">
                    <p class="text-4xl mb-3">🔔</p>
                    <p class="text-gray-500 font-medium">Aucune notification pour le moment.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif

    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20">
        <p>&copy; 2026 Mon Blog Personnel — Tous droits réservés.</p>
    </footer>

</body>
</html>
