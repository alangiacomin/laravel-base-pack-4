<?php

namespace AlanGiacomin\LaravelBasePack\QueueObject;

use AlanGiacomin\LaravelBasePack\Commands\CommandResult;
use AlanGiacomin\LaravelBasePack\Commands\CommandResultContainer;
use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Auth;

/**
 * Class MessageBus
 *
 * Provides functionality for dispatching and processing commands and events
 * using Laravel's Dispatcher, including support for synchronous execution,
 * asynchronous dispatch, and event publishing to a queue.
 */
class MessageBus implements IMessageBus
{
    /**
     * @var Dispatcher The Laravel Dispatcher instance used for dispatching commands and events.
     */
    protected Dispatcher $bus;

    /**
     * MessageBus constructor.
     *
     * @param  Dispatcher  $bus  The Laravel Dispatcher for command and event dispatching.
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * Dispatches a command asynchronously.
     *
     * @param  ICommand  $command  The command to be dispatched.
     * @return mixed The result of the command dispatch.
     */
    public function dispatch(ICommand $command): mixed
    {
        return $this->bus->dispatch($command);
    }

    /**
     * Executes a command synchronously and returns the result.
     *
     * A new CommandResult instance is attached to the command, and the result
     * is processed synchronously.
     *
     * @param  ICommand  $command  The command to be executed.
     * @return CommandResult The result of the executed command.
     */
    public function execute(ICommand $command): CommandResult
    {
        $command->result = new CommandResult();
        CommandResultContainer::setResult($command);
        $this->bus->dispatchSync($command);

        return $command->result;
    }

    /**
     * Publishes an event to the message queue.
     *
     * If the `userId` on the event is `0` and there's an authenticated user,
     * the authenticated user's ID is automatically set on the event before dispatch.
     *
     * @param  IEvent  $event  The event to be published to the queue.
     * @return mixed The result of publishing the event.
     */
    public function publish(IEvent $event): mixed
    {
        if ($event->userId == 0 && Auth::check()) {
            /** @noinspection PhpUndefinedFieldInspection */
            $event->userId = Auth::user()->id;
        }

        return $this->bus->dispatchToQueue($event);
    }

    /**
     * Registers a mapping of commands/events to their corresponding handlers.
     *
     * @param  array  $map  An associative array where keys are commands/events and
     *                      their values are the corresponding handlers.
     * @return Dispatcher The dispatcher instance after applying the mappings.
     */
    public function register(array $map): Dispatcher
    {
        return $this->bus->map($map);
    }
}
