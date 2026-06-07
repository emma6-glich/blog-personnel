<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    public function __construct(public Comment $comment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'new_comment',
            'message'    => '💬 ' . $this->comment->pseudo . ' a commenté votre article "' . $this->comment->post->title . '"',
            'url'        => '/articles/' . $this->comment->post->slug,
            'comment_id' => $this->comment->id,
        ];
    }
}
