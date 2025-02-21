<?php

namespace AlanGiacomin\LaravelBasePack\Events;

use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObject;
use Illuminate\Support\Str;

abstract class Event extends QueueObject implements IEvent
{
    /**
     * The event's broadcast name.
     *
     * @noinspection PhpUnused
     */
    public function broadcastAs(): string
    {
        return $this->fullName();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function toArray(object $notifiable): array
    {
        return $this->props();
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array|string
    {
        return [
            'mail' => 'mail-queue',
            'slack' => 'slack-queue',
            'broadcast' => Str::of($this->queue)->isEmpty() ? 'default' : $this->queue,
        ];
    }
}
