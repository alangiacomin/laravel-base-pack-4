<?php

namespace AlanGiacomin\LaravelBasePack\QueueObject;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Auth;

class MessageBus implements IMessageBus
{
    public function __construct(
        protected Dispatcher $bus,
    ) {}

    public function dispatch(ICommand $command): mixed
    {
        return $this->bus->dispatch($command);
    }

    public function execute(ICommand $command): mixed
    {
        return $this->bus->dispatchSync($command);
    }

    public function publish(IEvent $event): mixed
    {
        if (Auth::check()) {
            $event->userId = Auth::user()->id;
        }

        return $this->bus->dispatchToQueue($event);
    }

    public function register(array $map): Dispatcher
    {
        return $this->bus->map($map);
    }
}
