<?php

namespace AlanGiacomin\LaravelBasePack\QueueObject\Contracts;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use Illuminate\Contracts\Bus\Dispatcher;

interface IMessageBus
{
    /**
     * Dispatch command asynchronously over the bus
     */
    public function dispatch(ICommand $command): mixed;

    /**
     * Execute command synchronously over the bus
     */
    public function execute(ICommand $command): mixed;

    /**
     * Publish event over the bus
     */
    public function publish(IEvent $event): mixed;

    /**
     * Register handlers for commands and events
     */
    public function register(array $map): Dispatcher;
}
