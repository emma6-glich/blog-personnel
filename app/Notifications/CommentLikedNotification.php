<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;

class CommentLikedNotification extends Notification
{
    public function __construct(public Comment $comment, public string $likerName) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'comment_liked',
            'message'    => '👍 ' . $this->likerName . ' a aimé votre commentaire sur "' . $this->comment->post->title . '"',
            'url'        => '/articles/' . $this->comment->post->slug,
            'comment_id' => $this->comment->id,
        ];
    }
}
