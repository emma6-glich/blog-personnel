<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification
{
    public function __construct(public User $user) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'new_user',
            'message' => '👤 Nouvel utilisateur inscrit : ' . $this->user->name . ' (' . $this->user->email . ')',
            'url'     => '/dashboard',
            'user_id' => $this->user->id,
        ];
    }
}
