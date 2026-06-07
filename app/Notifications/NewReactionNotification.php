<?php

namespace App\Notifications;

use App\Models\Reaction;
use Illuminate\Notifications\Notification;

class NewReactionNotification extends Notification
{
    public function __construct(public Reaction $reaction) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'        => 'new_reaction',
            'message'     => $this->reaction->emoji . ' ' . $this->reaction->user->name . ' a réagi à votre article "' . $this->reaction->post->title . '"',
            'url'         => '/articles/' . $this->reaction->post->slug,
            'reaction_id' => $this->reaction->id,
        ];
    }
}
