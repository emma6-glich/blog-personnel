<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CommentDeletedNotification extends Notification
{
    public function __construct(public string $postTitle, public string $postSlug) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'comment_deleted',
            'message' => '🗑️ Votre commentaire sur "' . $this->postTitle . '" a été supprimé.',
            'url'     => '/articles/' . $this->postSlug,
        ];
    }
}
