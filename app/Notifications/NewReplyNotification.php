<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;

class NewReplyNotification extends Notification
{
    public function __construct(public Comment $reply) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'     => 'new_reply',
            'message'  => '↩️ @' . $this->reply->pseudo . ' vous a répondu sur "' . $this->reply->post->title . '"',
            'url'      => '/articles/' . $this->reply->post->slug,
            'reply_id' => $this->reply->id,
        ];
    }
}
