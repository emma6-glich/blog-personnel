<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ArticleDeletedNotification extends Notification
{
    public function __construct(public string $title) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'article_deleted',
            'message' => '🗑️ L\'article "' . $this->title . '" a été supprimé.',
            'url'     => '/',
        ];
    }
}
