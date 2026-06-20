<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

            <div class="text-gray-700 leading-relaxed space-y-6 text-lg whitespace-pre-line" id="article-content">
                {{ $post->content }}
            </div>

            {{-- Lecteur audio --}}
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">Écouter cet article :</span>
                <button id="btn-play" onclick="startReading()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition cursor-pointer">
                    ▶ Lire
                </button>
                <button id="btn-pause" onclick="pauseReading()" style="display:none;"
                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-yellow-600 transition cursor-pointer">
                    ⏸ Pause
                </button>
                <button id="btn-resume" onclick="resumeReading()" style="display:none;"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition cursor-pointer">
                    ▶ Reprendre
                </button>
                <button id="btn-stop" onclick="stopReading()" style="display:none;"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-600 transition cursor-pointer">
                    ■ Arrêter
                </button>
                <div class="flex items-center gap-2 ml-auto">
                    <label class="text-xs text-gray-500">Vitesse :</label>
                    <select id="speed-select" class="text-xs border border-gray-300 rounded px-2 py-1">
                        <option value="0.8">Lente</option>
                        <option value="1" selected>Normale</option>
                        <option value="1.3">Rapide</option>
                        <option value="1.6">Très rapide</option>
                    </select>
                </div>
            </div>

            <script>
            var utterance = null;

            function startReading() {
                if (!window.speechSynthesis) {
                    alert("Votre navigateur ne supporte pas la synthèse vocale.");
                    return;
                }
                window.speechSynthesis.cancel();
                var text = document.getElementById('article-content').innerText;
                utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'fr-FR';
                utterance.rate = parseFloat(document.getElementById('speed-select').value);
                utterance.onend = function() { resetButtons(); };
                window.speechSynthesis.speak(utterance);
                document.getElementById('btn-play').style.display = 'none';
                document.getElementById('btn-pause').style.display = 'inline-block';
                document.getElementById('btn-stop').style.display = 'inline-block';
            }

            function pauseReading() {
                window.speechSynthesis.pause();
                document.getElementById('btn-pause').style.display = 'none';
                document.getElementById('btn-resume').style.display = 'inline-block';
            }

            function resumeReading() {
                window.speechSynthesis.resume();
                document.getElementById('btn-resume').style.display = 'none';
                document.getElementById('btn-pause').style.display = 'inline-block';
            }

            function stopReading() {
                window.speechSynthesis.cancel();
                resetButtons();
            }

            function resetButtons() {
                document.getElementById('btn-play').style.display = 'inline-block';
                document.getElementById('btn-pause').style.display = 'none';
                document.getElementById('btn-resume').style.display = 'none';
                document.getElementById('btn-stop').style.display = 'none';
            }
            </script>

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
                <p class="text-xs text-blue-600 mb-4" id="poll-total">{{ $totalPollVotes }} vote{{ $totalPollVotes > 1 ? 's' : '' }} au total</p>

                @auth
                <form action="/articles/{{ $post->slug }}/poll" method="POST" class="space-y-3" data-ajax-poll>
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
                                    <span class="text-xs text-gray-500" id="poll-count-{{ $cat->id }}">{{ $votes }} vote{{ $votes > 1 ? 's' : '' }} ({{ $percent }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all" id="poll-bar-{{ $cat->id }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                    <div class="pt-2">
                        <button type="submit" id="poll-submit-btn" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition cursor-pointer">
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
                            <form action="/articles/{{ $post->slug }}/reactions" method="POST" data-ajax-reaction>
                                @csrf
                                <input type="hidden" name="emoji" value="{{ $emoji }}">
                                <button type="submit"
                                    data-reaction-emoji="{{ $emoji }}"
                                    class="flex items-center gap-1 px-4 py-2 rounded-full border text-sm font-medium transition cursor-pointer {{ $userReacted ? 'bg-blue-100 border-blue-400 text-blue-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                                    {{ $emoji }} <span class="reaction-count">{{ $count > 0 ? $count : '' }}</span>
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
                <h3 class="text-2xl font-bold text-gray-900">Commentaires (<span id="comments-count">{{ $post->comments->count() }}</span>)</h3>
                <div class="flex gap-2">
                    <a href="?sort=asc" class="text-xs px-3 py-1.5 rounded-full border {{ $sort === 'asc' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        Plus ancien
                    </a>
                    <a href="?sort=desc" class="text-xs px-3 py-1.5 rounded-full border {{ $sort === 'desc' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        Plus récent
                    </a>
                </div>
            </div>

            <div class="space-y-6 mb-10" id="comments-container">
                @forelse($comments as $comment)
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-100 shadow-sm" id="comment-{{ $comment->id }}">
                        <div class="flex gap-3">
                            {{-- Avatar --}}
                            <div style="flex-shrink:0;">
                                @if($comment->user && $comment->user->avatar)
                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
                                @else
                                    <div style="width:38px;height:38px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;">
                                        <span style="color:#6b7280;font-weight:bold;font-size:16px;">{{ strtoupper(substr($comment->pseudo, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-gray-800 text-sm">{{ $comment->pseudo }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-400">{{ $comment->created_at->format('d/m/Y à H:i') }}</span>
                                        @auth
                                            @if(auth()->id() === $comment->user_id || auth()->user()->email === env('ADMIN_EMAIL'))
                                                @if(auth()->id() === $comment->user_id && $comment->created_at->diffInMinutes(now()) <= 10)
                                                    <button onclick="document.getElementById('edit-comment-{{ $comment->id }}').classList.toggle('hidden')"
                                                        class="text-xs text-blue-500 hover:text-blue-700 font-medium cursor-pointer">Modifier</button>
                                                @endif
                                                <form action="/comments/{{ $comment->id }}" method="POST" data-ajax-delete data-comment-id="{{ $comment->id }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium cursor-pointer">Supprimer</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>

                                <p class="text-gray-600 text-sm whitespace-pre-line mb-3">{{ $comment->content }}</p>

                                {{-- Like --}}
                                <div class="flex items-center gap-4 mb-2">
                                    @auth
                                        @php $liked = $comment->likes->where('user_id', auth()->id())->count() > 0; @endphp
                                        <button type="button"
                                            data-like-comment="{{ $comment->id }}"
                                            class="text-xs flex items-center gap-1 {{ $liked ? 'text-blue-600 font-semibold' : 'text-gray-400 hover:text-blue-500' }} cursor-pointer transition">
                                            👍 <span class="like-count">{{ $comment->likes->count() > 0 ? $comment->likes->count() : '' }}</span>
                                        </button>
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
                                    <form action="/articles/{{ $post->slug }}/comments" method="POST" class="mt-3 space-y-3 bg-white p-4 rounded-lg border border-gray-200 shadow-inner" data-ajax-comment>
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
                                    <div class="mt-4 pl-6 border-l-2 border-blue-200 space-y-3" id="replies-{{ $comment->id }}">
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
                                                    <button type="button"
                                                        data-like-comment="{{ $reply->id }}"
                                                        class="text-[10px] flex items-center gap-1 {{ $replyLiked ? 'text-blue-600 font-semibold' : 'text-gray-400 hover:text-blue-500' }} cursor-pointer">
                                                        👍 <span class="like-count">{{ $reply->likes->count() > 0 ? $reply->likes->count() : '' }}</span>
                                                    </button>
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
                            </div>{{-- fin flex-1 --}}
                        </div>{{-- fin flex gap-3 --}}
                    </div>
                @empty
                    <p class="text-gray-500 italic text-sm">Aucun commentaire pour le moment. Soyez le premier à donner votre avis !</p>
                @endforelse
            </div>

            {{-- Formulaire nouveau commentaire --}}
            <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 shadow-inner">
                @auth
                    <h4 class="text-lg font-semibold text-blue-900 mb-4">Laisser un commentaire</h4>
                    <form action="/articles/{{ $post->slug }}/comments" method="POST" class="space-y-4" data-ajax-comment>
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
    // ============================================================
    // AJAX - Toutes les actions sans recharger la page
    // ============================================================

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content
              || document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]
              || '';

    function ajaxPost(url, data, callback) {
        const formData = new FormData();
        for (const [k, v] of Object.entries(data)) formData.append(k, v);
        formData.append('_token', CSRF);

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(callback)
        .catch(e => console.error(e));
    }

    // --- NOUVEAU COMMENTAIRE ---
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.dataset.ajaxComment) return;
        e.preventDefault();

        const content = form.querySelector('[name="content"]').value.trim();
        const parentId = form.querySelector('[name="parent_id"]')?.value || '';

        ajaxPost(form.action, { content, parent_id: parentId }, function(res) {
            if (!res.success) { alert(res.message); return; }

            const c = res.comment;
            const initial = c.pseudo ? c.pseudo[0].toUpperCase() : '?';
            const avatarHtml = c.avatar
                ? `<img src="${c.avatar}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">`
                : `<div style="width:38px;height:38px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;"><span style="color:#6b7280;font-weight:bold;font-size:16px;">${initial}</span></div>`;

            const html = `
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-100 shadow-sm" id="comment-${c.id}">
                <div class="flex gap-3">
                    <div style="flex-shrink:0;">${avatarHtml}</div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-gray-800 text-sm">${c.pseudo}</span>
                            <span class="text-xs text-gray-400">${c.created_at}</span>
                        </div>
                        <p class="text-gray-600 text-sm whitespace-pre-line mb-3">${c.content}</p>
                        <div class="flex items-center gap-4 mb-2">
                            <span class="text-xs text-gray-400">👍</span>
                        </div>
                    </div>
                </div>
            </div>`;

            if (parentId) {
                // Réponse — l'ajouter dans la section réponses du parent
                const parentEl = document.getElementById('replies-' + parentId);
                if (parentEl) {
                    parentEl.insertAdjacentHTML('beforeend', html.replace('bg-gray-50', 'bg-blue-50/50').replace('border-gray-100', 'border-blue-50'));
                }
                // Fermer le details
                const details = form.closest('details');
                if (details) details.removeAttribute('open');
            } else {
                // Nouveau commentaire principal
                const container = document.getElementById('comments-container');
                if (container) container.insertAdjacentHTML('beforeend', html);
            }

            form.reset();

            // Mettre à jour le compteur
            const counter = document.getElementById('comments-count');
            if (counter) counter.textContent = parseInt(counter.textContent) + 1;

            showFlash('Commentaire publié !', 'success');
        });
    });

    // --- LIKE COMMENTAIRE ---
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-like-comment]');
        if (!btn) return;
        e.preventDefault();

        const commentId = btn.dataset.likeComment;
        ajaxPost(`/comments/${commentId}/like`, {}, function(res) {
            if (!res.success) return;
            btn.querySelector('.like-count').textContent = res.likes_count > 0 ? res.likes_count : '';
            if (res.liked) {
                btn.classList.add('text-blue-600', 'font-semibold');
                btn.classList.remove('text-gray-400');
            } else {
                btn.classList.remove('text-blue-600', 'font-semibold');
                btn.classList.add('text-gray-400');
            }
        });
    });

    // --- REACTIONS ARTICLE ---
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.dataset.ajaxReaction) return;
        e.preventDefault();

        const emoji = form.querySelector('[name="emoji"]').value;
        ajaxPost(form.action, { emoji }, function(res) {
            if (!res.success) return;
            // Mettre à jour tous les boutons de réaction
            for (const [em, data] of Object.entries(res.reactions)) {
                const btn = document.querySelector(`[data-reaction-emoji="${em}"]`);
                if (!btn) continue;
                btn.querySelector('.reaction-count').textContent = data.count > 0 ? data.count : '';
                if (data.userReacted) {
                    btn.classList.add('bg-blue-100', 'border-blue-400', 'text-blue-700');
                    btn.classList.remove('bg-white', 'border-gray-300', 'text-gray-600');
                } else {
                    btn.classList.remove('bg-blue-100', 'border-blue-400', 'text-blue-700');
                    btn.classList.add('bg-white', 'border-gray-300', 'text-gray-600');
                }
            }
        });
    });

    // --- SUPPRIMER COMMENTAIRE ---
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.dataset.ajaxDelete) return;
        e.preventDefault();

        if (!confirm('Supprimer ce commentaire ?')) return;

        const commentId = form.dataset.commentId;
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ _token: CSRF, _method: 'DELETE' })
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) { alert(res.message); return; }
            const el = document.getElementById('comment-' + commentId);
            if (el) el.remove();
            const counter = document.getElementById('comments-count');
            if (counter) counter.textContent = Math.max(0, parseInt(counter.textContent) - 1);
            showFlash('Commentaire supprimé.', 'success');
        });
    });

    // --- SONDAGE ---
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form.dataset.ajaxPoll) return;
        e.preventDefault();

        const selected = form.querySelector('input[name="category_id"]:checked');
        if (!selected) return;

        ajaxPost(form.action, { category_id: selected.value }, function(res) {
            if (!res.success) return;
            // Mettre à jour les barres
            for (const [catId, votes] of Object.entries(res.pollVotes)) {
                const bar = document.getElementById('poll-bar-' + catId);
                const count = document.getElementById('poll-count-' + catId);
                const percent = res.totalPollVotes > 0 ? Math.round((votes / res.totalPollVotes) * 100) : 0;
                if (bar) bar.style.width = percent + '%';
                if (count) count.textContent = votes + ' vote' + (votes > 1 ? 's' : '') + ' (' + percent + '%)';
            }
            document.getElementById('poll-total').textContent = res.totalPollVotes + ' vote' + (res.totalPollVotes > 1 ? 's' : '') + ' au total';
            document.getElementById('poll-submit-btn').textContent = 'Changer mon vote';
            showFlash('Vote enregistré !', 'success');
        });
    });

    // --- FLASH MESSAGE ---
    function showFlash(msg, type) {
        const existing = document.getElementById('ajax-flash');
        if (existing) existing.remove();
        const color = type === 'success' ? '#d1fae5' : '#fee2e2';
        const border = type === 'success' ? '#34d399' : '#f87171';
        const text   = type === 'success' ? '#065f46' : '#991b1b';
        const div = document.createElement('div');
        div.id = 'ajax-flash';
        div.style.cssText = `position:fixed;top:80px;right:20px;z-index:9999;background:${color};border:1px solid ${border};color:${text};padding:12px 20px;border-radius:12px;font-size:14px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.1);`;
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    }

    // ============================================================
    // Lecteur audio
    // ============================================================
    var utterance = null;

    function startReading() {
        if (!window.speechSynthesis) { alert("Votre navigateur ne supporte pas la synthèse vocale."); return; }
        window.speechSynthesis.cancel();
        var text = document.getElementById('article-content').innerText;
        utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'fr-FR';
        utterance.rate = parseFloat(document.getElementById('speed-select').value);
        utterance.onend = function() { resetButtons(); };
        window.speechSynthesis.speak(utterance);
        document.getElementById('btn-play').style.display = 'none';
        document.getElementById('btn-pause').style.display = 'inline-block';
        document.getElementById('btn-stop').style.display = 'inline-block';
    }

    function pauseReading() {
        window.speechSynthesis.pause();
        document.getElementById('btn-pause').style.display = 'none';
        document.getElementById('btn-resume').style.display = 'inline-block';
    }

    function resumeReading() {
        window.speechSynthesis.resume();
        document.getElementById('btn-resume').style.display = 'none';
        document.getElementById('btn-pause').style.display = 'inline-block';
    }

    function stopReading() {
        window.speechSynthesis.cancel();
        resetButtons();
    }

    function resetButtons() {
        document.getElementById('btn-play').style.display = 'inline-block';
        document.getElementById('btn-pause').style.display = 'none';
        document.getElementById('btn-resume').style.display = 'none';
        document.getElementById('btn-stop').style.display = 'none';
    }

    // ============================================================
    // Autocomplétion @mention
    // ============================================================
    function handleMention(textarea, suggestId) {
        const value = textarea.value;
        const cursorPos = textarea.selectionStart;
        const textBeforeCursor = value.substring(0, cursorPos);
        const mentionMatch = textBeforeCursor.match(/@(\w*)$/);
        const suggest = document.getElementById(suggestId);
        if (!suggest) return;

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
