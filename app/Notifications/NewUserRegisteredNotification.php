<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $newUser,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New User Registered',
            'body' => "{$this->newUser->name} ({$this->newUser->role->value}) has joined.",
            'icon' => 'user-plus',
            'url' => route('admin.users.show', $this->newUser),

        ];
    }
}
