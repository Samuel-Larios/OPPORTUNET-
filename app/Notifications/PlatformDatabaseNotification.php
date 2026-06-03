<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlatformDatabaseNotification extends Notification
{
    use Queueable;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public array $payload,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => (string) ($this->payload['title'] ?? ''),
            'message' => (string) ($this->payload['message'] ?? ''),
            'action_url' => $this->payload['action_url'] ?? null,
            'action_label' => $this->payload['action_label'] ?? null,
            'category' => (string) ($this->payload['category'] ?? 'general'),
            'level' => (string) ($this->payload['level'] ?? 'info'),
            'resource_type' => $this->payload['resource_type'] ?? null,
            'resource_id' => $this->payload['resource_id'] ?? null,
        ];
    }
}
