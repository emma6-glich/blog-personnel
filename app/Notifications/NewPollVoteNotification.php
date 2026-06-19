<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Notifications\Notification;

class NewPollVoteNotification extends Notification
{
    public function __construct(
        public Post $post,
        public Category $category,
        public string $voterName
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'poll_vote',
            'message' => '🗳️ ' . $this->voterName . ' a voté pour "' . $this->category->name . '" comme prochain sujet sur "' . $this->post->title . '"',
            'url'     => '/articles/' . $this->post->slug,
            'post_id' => $this->post->id,
        ];
    }
}
