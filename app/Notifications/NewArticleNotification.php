<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Notification;

class NewArticleNotification extends Notification
{
    public function __construct(public Post $post) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'new_article',
            'message' => '📝 Nouvel article publié : "' . $this->post->title . '"',
            'url'     => '/articles/' . $this->post->slug,
            'post_id' => $this->post->id,
        ];
    }
}
