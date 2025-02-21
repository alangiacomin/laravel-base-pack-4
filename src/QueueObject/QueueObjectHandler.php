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

    protected function manageFailed(Throwable $exception): void {}

    /**
     * @throws Throwable
     */
    protected function handleObject(): void
    {
        $this->setMessageBus(app(IMessageBus::class));

        $isJob = isset($this->job);
        $queue = $isJob ? $this->job->getQueue() : null;
        $isSync = !$isJob || $queue == 'sync';

        if ($isSync) {
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

    protected function setMessageBus(IMessageBus $messageBus): void
    {
        $this->messageBus = $messageBus;
    }

    protected function setQueueObject(IQueueObject $queueObject): void
    {
        $this->queueObject = $queueObject;
    }

    protected function getQueueObject(): IQueueObject
    {
        return $this->queueObject;
    }

    /**
     * Safe execution within a transaction
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

    protected function failed(Throwable $exception): void
    {
        $this->manageFailed($exception);
    }

    protected function publish(IEvent $event): void
    {
        $event->assignUser($this->queueObject->userId);
        $this->messageBus->publish($event);
    }

    protected function send(ICommand $command): void
    {
        $command->assignUser($this->queueObject->userId);
        $this->messageBus->dispatch($command);
    }
}
