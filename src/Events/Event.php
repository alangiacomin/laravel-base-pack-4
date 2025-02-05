<?php

namespace AlanGiacomin\LaravelBasePack\Events;

use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObject;
use Illuminate\Notifications\Messages\BroadcastMessage;

abstract class Event extends QueueObject implements IEvent
{
    public int $userId = 0;

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return $this->fullName();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->props();
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    //    public function toBroadcast(object $notifiable): BroadcastMessage
    //    {
    //        return new BroadcastMessage($this->props());
    //    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        $caller = debug_backtrace()[1]['function'];
        if ($caller == 'queueNotification') {
            return [
                'broadcast' => 'notifications',
            ];
        }

        return [
            'mail' => 'mail-queue',
            'slack' => 'slack-queue',
            'broadcast' => 'default',
        ];
    }
}
