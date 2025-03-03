<?php

namespace AlanGiacomin\LaravelBasePack\QueueObject;

use AlanGiacomin\LaravelBasePack\Commands\Command;
use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\Events\Event;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IQueueObject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Abstract class representing a handler that processes queued commands or events.
 */
abstract class QueueObjectHandler implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * The number of times the queued listener may be attempted.
     */
    public int $tries = 3;

    /**
     * Message bus where {@see Command} and {@see Event} are dispatched
     */
    protected IMessageBus $messageBus;

    /**
     * {@see Command} or {@see Event} to handle
     */
    private IQueueObject $queueObject;

    /**
     * Execute the command or event body
     */
    abstract protected function execute();

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array|int
    {
        return [1, 1, 5, 1, 1, 5, 1, 1];
    }

    /**
     * Handles the failed scenario provided by a Throwable exception.
     *
     * @param  Throwable  $exception  The exception instance that contains the failure details.
     */
    protected function manageFailed(Throwable $exception): void {}

    /**
     * @throws Throwable
     */
    protected function handleObject(): void
    {
        $this->setMessageBus(app(IMessageBus::class));

        if ($this->isSyncJob()) {
            $this->tries = 1;
            try {
                $this->executeWithinTransaction();
            } catch (Throwable $e) {
                $this->failed($e);
            }
        } else {
            $this->executeWithinTransaction();
        }
    }

    /**
     * Determines if the current job is a synchronous job.
     *
     * @return bool True if the job is either not set or is part of the 'sync' queue; otherwise, false.
     */
    protected function isSyncJob(): bool
    {
        $isJob = isset($this->job);
        $queue = $isJob ? $this->job->getQueue() : null;

        return !$isJob || $queue == 'sync';
    }

    /**
     * Sets the message bus instance to be used.
     *
     * @param  IMessageBus  $messageBus  The message bus instance to assign.
     */
    protected function setMessageBus(IMessageBus $messageBus): void
    {
        $this->messageBus = $messageBus;
    }

    /**
     * Sets the queue object instance.
     *
     * @param  IQueueObject  $queueObject  The queue object instance to be assigned.
     */
    protected function setQueueObject(IQueueObject $queueObject): void
    {
        $this->queueObject = $queueObject;
    }

    /**
     * Retrieves the queue object instance.
     *
     * @return IQueueObject The queue object associated with the instance.
     */
    protected function getQueueObject(): IQueueObject
    {
        return $this->queueObject;
    }

    /**
     * Safe execution within a transaction
     *
     * @throws Throwable
     */
    final protected function executeWithinTransaction(): void
    {
        try {
            DB::beginTransaction();
            $this->execute();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Processes a failure event by passing the exception to the handling method.
     *
     * @param  Throwable  $exception  The exception instance that describes the failure.
     */
    protected function failed(Throwable $exception): void
    {
        $this->manageFailed($exception);
    }

    /**
     * Publishes the given event after assigning a user to it.
     *
     * @param  IEvent  $event  The event instance to be published.
     */
    protected function publish(IEvent $event): void
    {
        $event->assignUser($this->queueObject->userId);
        $this->messageBus->publish($event);
    }

    /**
     * Sends a command by assigning a user to it and dispatching it through the message bus.
     *
     * @param  ICommand  $command  The command to be processed and dispatched.
     */
    protected function send(ICommand $command): void
    {
        $command->assignUser($this->queueObject->userId);
        $this->messageBus->dispatch($command);
    }
}
