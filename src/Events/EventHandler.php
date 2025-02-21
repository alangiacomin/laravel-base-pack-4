<?php

namespace AlanGiacomin\LaravelBasePack\Events;

use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IQueueObject;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObjectHandler;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * @property IQueueObject $event
 */
abstract class EventHandler extends QueueObjectHandler
{
    /**
     * Execute the job.
     *
     * @throws BasePackException
     * @throws Throwable
     */
    final public function handle(IEvent $event): void
    {
        $this->setQueueObject($event);
        $this->setTypedObject();

        $this->loginAsUser();
        $this->handleObject();

        $this->sendNotification();
    }

    /**
     * Sets generic bus object as the specific typed object managed by the handler
     *
     * @throws BasePackException
     */
    private function setTypedObject(): void
    {
        if (!property_exists($this, 'event')) {
            throw new BasePackException($this->getQueueObject()->fullName().": 'event' property must be defined");
        }

        $this->event = $this->getQueueObject();
    }

    private function loginAsUser(): void
    {
        if ($this->event->userId > 0) {
            Auth::loginUsingId($this->event->userId);
        }
    }

    private function sendNotification(): void
    {
        if (Auth::check()) {
            $this->event->onQueue('notifications');
            /** @noinspection PhpUndefinedMethodInspection */
            Auth::user()->notify($this->event);
        }
    }
}
