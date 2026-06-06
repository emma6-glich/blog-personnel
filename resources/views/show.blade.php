<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wide">Mon Blog Personnel</h1>
            <ul class="flex space-x-6 font-medium items-center">
                <li><a href="/" class="hover:underline">Accueil</a></li>
                @auth
                    @if(auth()->user()->email === env('ADMIN_EMAIL'))
                        <li><a href="/articles/create" class="hover:underline">Écrire un article</a></li>
                    @endif
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

    <x-flash-messages />

    <main class="container mx-auto my-10 px-4 max-w-3xl bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <div class="mb-4">
            <a href="/" class="text-blue-600 hover:underline font-medium">&larr; Retour aux articles</a>
        </div>

        <article>
            <span class="text-sm text-blue-500 font-semibold uppercase tracking-wider">
                {{ $post->category ? $post->category->name : 'Non classé' }}
            </span>
            
            <h2 class="text-4xl font-extrabold text-gray-900 mt-2 mb-4">
                {{ $post->title }}
            </h2>
            
            <div class="text-sm text-gray-500 mb-6">
                <p>Publié le {{ $post->created_at->format('d/m/Y à H:i') }}</p>
                @if($post->user)
                    <p class="text-xs">Par <span class="font-semibold text-gray-700">{{ $post->user->name }}</span></p>
                @endif
                <p class="text-xs mt-1 text-gray-400">👁 {{ $post->views }} vue{{ $post->views > 1 ? 's' : '' }}</p>
            </div>

            <div class="text-gray-700 leading-relaxed space-y-6 text-lg whitespace-pre-line">
                {{ $post->content }}
            </div>

            {{-- Image à la une --}}
            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-72 object-cover rounded-xl mt-8 border border-gray-200">
            @endif

            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-4">
                @auth
                    @if(auth()->id() === $post->user_id)
                        <a href="/articles/{{ $post->slug }}/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 shadow transition text-sm flex items-center cursor-pointer">
                            Modifier l'article
                        </a>

                        <form action="/articles/{{ $post->slug }}" method="POST" onsubmit="return confirm('Es-tu sûr de vouloir supprimer cet article ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 shadow transition text-sm cursor-pointer">
                                Supprimer l'article
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </article>

        <div class="mt-12 pt-8 border-t border-gray-200">

            {{-- Bloc réactions --}}
            <div class="mb-8">
                <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Réagir à cet article</h4>
                <div class="flex flex-wrap gap-3">
                    @foreach(['👍' => 'J\'aime', '❤️' => 'Amour', '😂' => 'Drôle', '😮' => 'Surpris', '😢' => 'Triste'] as $emoji => $label)
                        @php
                            $count = $post->reactions->where('emoji', $emoji)->count();
                            $userReacted = auth()->check() && $post->reactions->where('emoji', $emoji)->where('user_id', auth()->id())->count() > 0;
                        @endphp
                        @auth
                            <form action="/articles/{{ $post->slug }}/reactions" method="POST">
                                @csrf
                                <input type="hidden" name="emoji" value="{{ $emoji }}">
                                <button type="submit"
                                    class="flex items-center gap-1 px-4 py-2 rounded-full border text-sm font-medium transition cursor-pointer
                                    {{ $userReacted ? 'bg-blue-100 border-blue-400 text-blue-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                                    {{ $emoji }} {{ $count > 0 ? $count : '' }}
                                </button>
                            </form>
                        @else
                            <a href="/login"
                                class="flex items-center gap-1 px-4 py-2 rounded-full border border-gray-300 bg-white text-gray-500 text-sm hover:bg-gray-50 transition">
                                {{ $emoji }} {{ $count > 0 ? $count : '' }}
                            </a>
                        @endauth
                    @endforeach
                </div>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 mb-6">
                Commentaires ({{ $post->comments->count() }})
            </h3>

            <div class="space-y-6 mb-10">
                @forelse($post->comments->where('parent_id', null) as $comment)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-gray-800">{{ $comment->pseudo }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400">Le {{ $comment->created_at->format('d/m/Y à H:i') }}</span>
                                @auth
                                    @if(auth()->id() === $comment->user_id || auth()->user()->email === env('ADMIN_EMAIL'))
                                        {{-- Bouton modifier --}}
                                        <button onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.toggle('hidden')"
                                            class="text-xs text-blue-500 hover:text-blue-700 font-medium cursor-pointer">
                                            Modifier
                                        </button>
                                        {{-- Bouton supprimer --}}
                                        <form action="/comments/{{ $comment->id }}" method="POST" onsubmit="return confirm('Supprimer ce commentaire ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium cursor-pointer">
                                                Supprimer
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm whitespace-pre-line mb-4">{{ $comment->content }}</p>

                        {{-- Formulaire de modification (caché par défaut) --}}
                        @auth
                            @if(auth()->id() === $comment->user_id || auth()->user()->email === env('ADMIN_EMAIL'))
                                <div id="edit-comment-{{ $comment->id }}" class="hidden mb-4">
                                    <form action="/comments/{{ $comment->id }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="content" rows="3" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ $comment->content }}</textarea>
                                        <div class="flex gap-2 mt-2 justify-end">
                                            <button type="button"
                                                onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.add('hidden')"
                                                class="text-xs text-gray-500 hover:text-gray-700 cursor-pointer">
                                                Annuler
                                            </button>
                                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-semibold hover:bg-blue-700 cursor-pointer">
                                                Sauvegarder
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        <details class="mt-2 text-sm text-gray-500">
                            <summary class="cursor-pointer text-blue-600 hover:underline font-medium focus:outline-none select-none">
                                Répondre à ce commentaire
                            </summary>
                            
                            <form action="/articles/{{ $post->slug }}/comments" method="POST" class="mt-3 space-y-3 bg-white p-4 rounded-lg border border-gray-200 shadow-inner">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div>
                                    <textarea name="content" rows="2" placeholder="Votre réponse..." required 
                                              class="w-full px-3 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 text-xs focus:outline-none"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-semibold hover:bg-blue-700 transition cursor-pointer">
                                        Répondre
                                    </button>
                                </div>
                            </form>
                        </details>

                        @if($comment->replies->count() > 0)
                            <div class="mt-4 pl-6 border-l-2 border-blue-200 space-y-3">
                                @foreach($comment->replies as $reply)
                                    <div class="bg-blue-50/50 p-3 rounded-lg border border-blue-50">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-blue-900 text-xs">
                                                {{ $reply->pseudo }}
                                                <span class="bg-blue-200 text-blue-800 text-[10px] px-1.5 py-0.5 rounded ml-1 font-semibold">Réponse</span>
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] text-gray-400">Le {{ $reply->created_at->format('d/m/Y à H:i') }}</span>
                                                @auth
                                                    @if(auth()->user()->email === env('ADMIN_EMAIL'))
                                                    <form action="/comments/{{ $reply->id }}" method="POST" onsubmit="return confirm('Supprimer cette réponse ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-[10px] text-red-500 hover:text-red-700 font-medium cursor-pointer">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                        <p class="text-gray-700 text-xs whitespace-pre-line">{{ $reply->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 italic text-sm">Aucun commentaire pour le moment. Soyez le premier à donner votre avis !</p>
                @endforelse
            </div>

            <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 shadow-inner">
                @auth
                    <h4 class="text-lg font-semibold text-blue-900 mb-4">Laisser un commentaire</h4>
                    <form action="/articles/{{ $post->slug }}/comments" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="content" class="block text-xs font-bold text-blue-900 uppercase mb-1">Votre Commentaire</label>
                            <textarea id="content" name="content" rows="4" placeholder="Donnez votre avis sur cet article..." required
                                      class="w-full px-3 py-2 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow cursor-pointer">
                                Publier le commentaire
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <p class="text-blue-800 font-medium mb-3">Tu dois être connecté pour laisser un commentaire.</p>
                        <a href="/login" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow inline-block">
                            Se connecter
                        </a>
                        <span class="text-gray-400 text-sm mx-2">ou</span>
                        <a href="/register" class="bg-white text-blue-600 border border-blue-300 px-5 py-2 rounded-lg font-semibold text-sm hover:bg-blue-50 transition inline-block">
                            Créer un compte
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 Mon Blog Personnel - Tous droits réservés.</p>
    </footer>

</body>
</html>