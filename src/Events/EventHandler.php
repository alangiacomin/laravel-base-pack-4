<?php

namespace AlanGiacomin\LaravelBasePack\Events;

use AlanGiacomin\LaravelBasePack\Core\ClassUtility;
use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IQueueObject;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObjectHandler;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
        $this->queueObject = $event;
        $this->setTypedObject();

        $this->loginAsUser();
        $this->handleObject();

        $this->sendNotification();
    }

    final protected function failed(Throwable $exception): void
    {
        parent::failed($exception);
    }

    /**
     * Sets generic bus object as the specific typed object managed by the handler
     *
     * @throws BasePackException
     */
    private function setTypedObject(): void
    {
        if (!property_exists($this, 'event')) {
            throw new BasePackException($this->queueObject->fullName().": 'event' property must be defined");
        }

        $this->event = $this->queueObject;
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
            $fwe = ClassUtility::filenameWithoutExtension($this->event->fullName());
            $rel = Str::remove("App\Events", ClassUtility::relativeNamespace($this->event->fullName()));
            $notificationName = "App\\Notifications$rel\\{$fwe}Notification";

            //            if (!class_exists($notificationName)) {
            //                if (empty($rel)) {
            //                    Artisan::call('basepack:notification', ['name' => "$fwe"]);
            //                } else {
            //                    Artisan::call('basepack:notification', ['name' => "$rel\\$fwe"]);
            //                }
            //                spl_autoload($notificationName);
            //            }

            // Auth::user()->notify(new $notificationName($this->event));
            // $this->event->queue = 'notifications';
            $this->event->onQueue('notifications');
            /** @noinspection PhpUndefinedMethodInspection */
            Auth::user()->notify($this->event);
        }
    }
}
