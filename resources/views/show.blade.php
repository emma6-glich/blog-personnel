<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - Mon Blog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans overflow-x-hidden">

    <x-navbar />

    <x-flash-messages />

    <main class="container mx-auto my-6 md:my-10 px-4 max-w-3xl bg-white p-5 md:p-8 rounded-xl shadow-md border border-gray-200">
        <div class="mb-4">
            <a href="/" class="text-blue-600 hover:underline font-medium">&larr; Retour aux articles</a>
        </div>

        <article>
            <span class="text-sm text-blue-500 font-semibold uppercase tracking-wider">
                {{ $post->category ? $post->category->name : 'Non classé' }}
            </span>

            <h2 class="text-4xl font-extrabold text-gray-900 mt-2 mb-4">{{ $post->title }}</h2>

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

            @if($post->image)
                <img src="{{ Str::startsWith($post->image, 'http') ? $post->image : asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-72 object-cover rounded-xl mt-8 border border-gray-200">
            @endif

            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-4">
                @auth
                    @if(auth()->id() === $post->user_id)
                        <a href="/articles/{{ $post->slug }}/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 shadow transition text-sm flex items-center cursor-pointer">
                            Modifier l'article
                        </a>
                        <form action="/articles/{{ $post->slug }}" method="POST" onsubmit="return confirm('Es-tu sûr de vouloir supprimer cet article ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 shadow transition text-sm cursor-pointer">
                                Supprimer l'article
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </article>

        <div class="mt-12 pt-8 border-t border-gray-200">

            {{-- Sondage --}}
            <div class="mb-8 bg-blue-50 p-6 rounded-xl border border-blue-100">
                <h4 class="text-base font-bold text-blue-900 mb-1">Quel sujet souhaitez-vous pour le prochain article ?</h4>
                <p class="text-xs text-blue-600 mb-4">{{ $totalPollVotes }} vote{{ $totalPollVotes > 1 ? 's' : '' }} au total</p>

                @auth
                <form action="/articles/{{ $post->slug }}/poll" method="POST" class="space-y-3">
                    @csrf
                    @foreach($allCategories as $cat)
                        @php
                            $votes = $pollVotes[$cat->id] ?? 0;
                            $percent = $totalPollVotes > 0 ? round(($votes / $totalPollVotes) * 100) : 0;
                            $isVoted = $userVote == $cat->id;
                        @endphp
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="category_id" value="{{ $cat->id }}" {{ $isVoted ? 'checked' : '' }}
                                class="accent-blue-600 w-4 h-4 cursor-pointer">
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium {{ $isVoted ? 'text-blue-700' : 'text-gray-700' }}">{{ $cat->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $votes }} vote{{ $votes > 1 ? 's' : '' }} ({{ $percent }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                    <div class="pt-2">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition cursor-pointer">
                            {{ $userVote ? 'Changer mon vote' : 'Voter' }}
                        </button>
                    </div>
                </form>
                @else
                    <div class="space-y-3">
                        @foreach($allCategories as $cat)
                            @php
                                $votes = $pollVotes[$cat->id] ?? 0;
                                $percent = $totalPollVotes > 0 ? round(($votes / $totalPollVotes) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $cat->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $votes }} vote{{ $votes > 1 ? 's' : '' }} ({{ $percent }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-400 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-blue-700 mt-3">
                        <a href="/login" class="underline font-semibold">Connectez-vous</a> pour voter.
                    </p>
                @endauth
            </div>

            {{-- Réactions --}}
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
                                <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-full border text-sm font-medium transition cursor-pointer {{ $userReacted ? 'bg-blue-100 border-blue-400 text-blue-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                                    {{ $emoji }} {{ $count > 0 ? $count : '' }}
                                </button>
                            </form>
                        @else
                            <a href="/login" class="flex items-center gap-1 px-4 py-2 rounded-full border border-gray-300 bg-white text-gray-500 text-sm hover:bg-gray-50 transition">
                                {{ $emoji }} {{ $count > 0 ? $count : '' }}
                            </a>
                        @endauth
                    @endforeach
                </div>
            </div>

            {{-- Titre + tri --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Commentaires ({{ $post->comments->count() }})</h3>
                <div class="flex gap-2">
                    <a href="?sort=asc" class="text-xs px-3 py-1.5 rounded-full border {{ $sort === 'asc' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        Plus ancien
                    </a>
                    <a href="?sort=desc" class="text-xs px-3 py-1.5 rounded-full border {{ $sort === 'desc' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        Plus récent
                    </a>
                </div>
            </div>

            <div class="space-y-6 mb-10">
                @forelse($comments as $comment)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-100 shadow-sm">
                        {{-- Header commentaire --}}
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-gray-800">{{ $comment->pseudo }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400">Le {{ $comment->created_at->format('d/m/Y à H:i') }}</span>
                                @auth
                                    @if(auth()->id() === $comment->user_id || auth()->user()->email === env('ADMIN_EMAIL'))
                                        @if(auth()->id() === $comment->user_id && $comment->created_at->diffInMinutes(now()) <= 10)
                                            <button onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.toggle('hidden')"
                                                class="text-xs text-blue-500 hover:text-blue-700 font-medium cursor-pointer">Modifier</button>
                                        @endif
                                        <form action="/comments/{{ $comment->id }}" method="POST" onsubmit="return confirm('Supprimer ce commentaire ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium cursor-pointer">Supprimer</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        {{-- Contenu --}}
                        <p class="text-gray-600 text-sm whitespace-pre-line mb-3">{{ $comment->content }}</p>

                        {{-- Like --}}
                        <div class="flex items-center gap-4 mb-2">
                            @auth
                                @php $liked = $comment->likes->where('user_id', auth()->id())->count() > 0; @endphp
                                <form action="/comments/{{ $comment->id }}/like" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs flex items-center gap-1 {{ $liked ? 'text-blue-600 font-semibold' : 'text-gray-400 hover:text-blue-500' }} cursor-pointer transition">
                                        👍 {{ $comment->likes->count() > 0 ? $comment->likes->count() : '' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">👍 {{ $comment->likes->count() > 0 ? $comment->likes->count() : '' }}</span>
                            @endauth
                        </div>

                        {{-- Formulaire modifier --}}
                        @auth
                            @if(auth()->id() === $comment->user_id && auth()->user()->email !== env('ADMIN_EMAIL'))
                                <div id="edit-comment-{{ $comment->id }}" class="hidden mb-4">
                                    <form action="/comments/{{ $comment->id }}" method="POST">
                                        @csrf @method('PUT')
                                        <textarea name="content" rows="3" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ $comment->content }}</textarea>
                                        <div class="flex gap-2 mt-2 justify-end">
                                            <button type="button" onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.add('hidden')"
                                                class="text-xs text-gray-500 hover:text-gray-700 cursor-pointer">Annuler</button>
                                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-semibold hover:bg-blue-700 cursor-pointer">Sauvegarder</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        {{-- Répondre --}}
                        @auth
                        <details class="mt-2 text-sm text-gray-500">
                            <summary class="cursor-pointer text-blue-600 hover:underline font-medium focus:outline-none select-none">
                                Répondre à ce commentaire
                            </summary>
                            <form action="/articles/{{ $post->slug }}/comments" method="POST" class="mt-3 space-y-3 bg-white p-4 rounded-lg border border-gray-200 shadow-inner">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div class="relative">
                                    <textarea name="content" rows="2" id="reply-{{ $comment->id }}"
                                        placeholder="@{{ $comment->pseudo }} " required
                                        class="w-full px-3 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 text-xs focus:outline-none"
                                        oninput="handleMention(this, 'suggest-{{ $comment->id }}')"></textarea>
                                    <div id="suggest-{{ $comment->id }}" class="hidden absolute z-10 bg-white border border-gray-200 rounded-lg shadow-lg w-full mt-1"></div>
                                </div>
                                <p class="text-[10px] text-gray-400">Tape @nom pour tagger quelqu'un</p>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-semibold hover:bg-blue-700 transition cursor-pointer">Répondre</button>
                                </div>
                            </form>
                        </details>
                        @endauth

                        {{-- Réponses --}}
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
                                                    @if(auth()->id() === $reply->user_id || auth()->user()->email === env('ADMIN_EMAIL'))
                                                        <form action="/comments/{{ $reply->id }}" method="POST" onsubmit="return confirm('Supprimer cette réponse ?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-[10px] text-red-500 hover:text-red-700 font-medium cursor-pointer">Supprimer</button>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                        <p class="text-gray-700 text-xs whitespace-pre-line">{{ $reply->content }}</p>
                                        @auth
                                            @php $replyLiked = $reply->likes->where('user_id', auth()->id())->count() > 0; @endphp
                                            <form action="/comments/{{ $reply->id }}/like" method="POST" class="inline mt-1">
                                                @csrf
                                                <button type="submit" class="text-[10px] flex items-center gap-1 {{ $replyLiked ? 'text-blue-600 font-semibold' : 'text-gray-400 hover:text-blue-500' }} cursor-pointer">
                                                    👍 {{ $reply->likes->count() > 0 ? $reply->likes->count() : '' }}
                                                </button>
                                            </form>

                                            {{-- Répondre à une réponse --}}
                                            <button onclick="document.getElementById('reply-to-reply-{{ $reply->id }}').classList.toggle('hidden')"
                                                class="text-[10px] text-blue-500 hover:text-blue-700 cursor-pointer ml-2">
                                                Répondre
                                            </button>
                                            <div id="reply-to-reply-{{ $reply->id }}" class="hidden mt-2">
                                                <form action="/articles/{{ $post->slug }}/comments" method="POST" class="space-y-2 bg-white p-3 rounded-lg border border-gray-200">
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                    <div class="relative">
                                                        <textarea name="content" rows="2" id="rr-{{ $reply->id }}"
                                                            placeholder="@{{ $reply->pseudo }} " required
                                                            class="w-full px-3 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 text-[10px] focus:outline-none"
                                                            oninput="handleMention(this, 'suggest-rr-{{ $reply->id }}')"></textarea>
                                                        <div id="suggest-rr-{{ $reply->id }}" class="hidden absolute z-10 bg-white border border-gray-200 rounded-lg shadow-lg w-full mt-1"></div>
                                                    </div>
                                                    <div class="flex justify-end">
                                                        <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded text-[10px] font-semibold hover:bg-blue-700 cursor-pointer">Envoyer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 italic text-sm">Aucun commentaire pour le moment. Soyez le premier à donner votre avis !</p>
                @endforelse
            </div>

            {{-- Formulaire nouveau commentaire --}}
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
                    </div>
                @endauth
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-20 border-t border-gray-800">
        <p>&copy; 2026 Mon Blog Personnel - Tous droits réservés.</p>
    </footer>

    <script>
    function handleMention(textarea, suggestId) {
        const value = textarea.value;
        const cursorPos = textarea.selectionStart;
        const textBeforeCursor = value.substring(0, cursorPos);
        const mentionMatch = textBeforeCursor.match(/@(\w*)$/);
        const suggest = document.getElementById(suggestId);

        if (mentionMatch) {
            const query = mentionMatch[1];
            if (query.length === 0) { suggest.classList.add('hidden'); return; }

            fetch('/api/users/search?q=' + encodeURIComponent(query))
                .then(r => r.json())
                .then(users => {
                    if (users.length === 0) { suggest.classList.add('hidden'); return; }
                    suggest.innerHTML = users.map(u =>
                        `<div class="px-3 py-2 text-xs hover:bg-blue-50 cursor-pointer" onclick="insertMention('${suggestId}', '${u.name}', this)">${u.name}</div>`
                    ).join('');
                    suggest.classList.remove('hidden');
                });
        } else {
            suggest.classList.add('hidden');
        }
    }

    function insertMention(suggestId, name, el) {
        const suggest = document.getElementById(suggestId);
        const textarea = suggest.previousElementSibling;
        const value = textarea.value;
        const cursorPos = textarea.selectionStart;
        const textBeforeCursor = value.substring(0, cursorPos);
        const newText = textBeforeCursor.replace(/@\w*$/, '@' + name + ' ') + value.substring(cursorPos);
        textarea.value = newText;
        suggest.classList.add('hidden');
        textarea.focus();
    }
    </script>

</body>
</html>
