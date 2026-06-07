<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Notification;

class ArticleUpdatedNotification extends Notification
{
    public function __construct(public Post $post) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'article_updated',
            'message' => '✏️ L\'article "' . $this->post->title . '" a été mis à jour.',
            'url'     => '/articles/' . $this->post->slug,
            'post_id' => $this->post->id,
        ];
    }
}
