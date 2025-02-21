<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\QueueObject;

use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IQueueObject;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObjectHandler;
use Exception;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;
use Throwable;

class QueueObjectHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->queueObjectHandler = $this->MockObject(QueueObjectHandler::class);
    }

    public function test_execute_sync_job_with_transaction_commits_successfully(): void
    {
        $this->queueObjectHandler
            ->shouldReceive('execute')
            ->once()
            ->andReturnTrue();

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        try {
            $this->queueObjectHandler->handleObject();
        } catch (Throwable) {
            $this->fail('Exception should not have been thrown.');
        }

        $this->assertTrue(true, 'Transaction and execution were handled correctly.');
    }

    public function test_execute_async_job_with_transaction_commits_successfully(): void
    {
        $this->queueObjectHandler
            ->shouldReceive('execute')
            ->once()
            ->andReturnTrue();

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $this->queueObjectHandler->job = $this->MockObject(Job::class);
        $this->queueObjectHandler->job
            ->shouldReceive('getQueue')
            ->andReturn('default');

        try {
            $this->queueObjectHandler->handleObject();
        } catch (Throwable) {
            $this->fail('Exception should not have been thrown.');
        }

        $this->assertTrue(true, 'Transaction and execution were handled correctly.');
    }

    public function test_execute_with_transaction_rolls_back_on_failure(): void
    {
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $exception = new Exception('Execution Failure');

        $this->queueObjectHandler
            ->shouldReceive('execute')
            ->once()
            ->andThrow($exception);

        $this->queueObjectHandler
            ->shouldReceive('manageFailed')
            ->once()
            ->with($exception);

        $this->queueObjectHandler->handleObject();
    }

    public function test_publish_event(): void
    {
        $this->event->shouldReceive('assignUser')->once(); // ->with(1);
        $this->messageBus
            ->shouldReceive('publish')
            ->once()
            ->with($this->event);

        $qo = Mockery::mock(IQueueObject::class);
        $qo->userId = 1;
        $this->queueObjectHandler->setQueueObject($qo);

        try {
            $this->queueObjectHandler->setMessageBus($this->messageBus);
            $this->queueObjectHandler->publish($this->event);
            $this->assertTrue(true);
        } catch (Throwable $exception) {
            $this->fail('Protected publish method threw an exception: '.$exception->getMessage());
        }
    }

    public function test_send_command(): void
    {
        $this->command->shouldReceive('assignUser')->once()->with(10);
        $this->messageBus
            ->shouldReceive('dispatch')
            ->once()
            ->with($this->command);

        $qo = Mockery::mock(IQueueObject::class);
        $qo->userId = 10;
        $this->queueObjectHandler->setQueueObject($qo);

        try {
            $this->queueObjectHandler->setMessageBus($this->messageBus);
            $this->queueObjectHandler->send($this->command);
            $this->assertTrue(true);
        } catch (Throwable $exception) {
            $this->fail('Protected send method threw an exception: '.$exception->getMessage());
        }
    }

    public function test_manage_failed_method_is_called_when_throws_exception(): void
    {
        $exception = new Exception('Test Exception');

        $this->queueObjectHandler
            ->shouldReceive('manageFailed')
            ->once()
            ->with($exception);

        try {
            $this->queueObjectHandler->failed($exception);
        } catch (Throwable $e) {
            $this->fail('manageFailed should handle the exception without propagating: '.$e->getMessage());
        }
    }

    public function test_backoff_configuration(): void
    {
        $config = [1, 1, 5, 1, 1, 5, 1, 1];

        $result = $this->queueObjectHandler->backoff();

        $this->assertSame($config, $result);
    }
}
