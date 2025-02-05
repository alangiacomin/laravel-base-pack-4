<?php

namespace AlanGiacomin\LaravelBasePack\Notifications;

use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected IEvent $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(IEvent $event)
    {
        $this->event = $event;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return $this->event->fullName();
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
        return $this->event->props();
    }
}
