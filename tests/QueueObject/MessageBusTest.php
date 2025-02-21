<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\QueueObject;

use AlanGiacomin\LaravelBasePack\Commands\Command;
use AlanGiacomin\LaravelBasePack\QueueObject\MessageBus;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class MessageBusTest extends TestCase
{
    public function test_bus_should_dispatch_command(): void
    {
        $command = new class() extends Command {};
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->with($command);
        $messageBus = new MessageBus($dispatcher);

        $messageBus->dispatch($command);

        $this->assertTrue(true, 'Command dispatched');
    }

    public function test_bus_should_execute_command(): void
    {
        $command = new class() extends Command {};
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatchSync')
            ->once()
            ->with($command);
        $messageBus = new MessageBus($dispatcher);

        $messageBus->execute($command);

        $this->assertTrue(true, 'Command dispatched');
    }

    public function test_publish_should_dispatch_to_queue_with_user_id_set_when_authenticated(): void
    {
        $this->event->userId = 0;
        $this->event->shouldReceive('userId')
            ->andSet(42);

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatchToQueue')
            ->once()
            ->with($this->event)
            ->andReturn('queued');

        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn((object) ['id' => 42]);

        $messageBus = new MessageBus($dispatcher);

        /** @noinspection PhpParamsInspection */
        $result = $messageBus->publish($this->event);

        $this->assertSame(42, $this->event->userId);
        $this->assertSame('queued', $result, 'Event should be dispatched to queue with updated userId');
    }

    public function test_publish_should_dispatch_to_queue_with_user_id_not_set_when_authenticated(): void
    {
        $this->event->userId = 53;
        $this->event->shouldReceive('userId')
            ->andSet(42);

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatchToQueue')
            ->once()
            ->with($this->event)
            ->andReturn('queued');

        Auth::shouldReceive('check')
            ->never();

        Auth::shouldReceive('user')
            ->never();

        $messageBus = new MessageBus($dispatcher);

        /** @noinspection PhpParamsInspection */
        $result = $messageBus->publish($this->event);

        $this->assertSame(53, $this->event->userId);
        $this->assertSame('queued', $result, 'Event should be dispatched to queue with updated userId');
    }

    public function test_publish_should_dispatch_to_queue_when_not_authenticated(): void
    {
        $this->event->userId = 0;
        $this->event->shouldReceive('userId')
            ->andSet(42);

        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatchToQueue')
            ->once()
            ->with($this->event)
            ->andReturn('queued');

        Auth::shouldReceive('check')
            ->once()
            ->andReturn(false);

        Auth::shouldReceive('user')
            ->never();

        $messageBus = new MessageBus($dispatcher);

        /** @noinspection PhpParamsInspection */
        $result = $messageBus->publish($this->event);

        $this->assertSame(0, $this->event->userId);
        $this->assertSame('queued', $result, 'Event should be dispatched to queue with updated userId');
    }

    public function test_bus_should_register_mappings(): void
    {
        $mappings = ['key' => 'value'];
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('map')
            ->once()
            ->with($mappings)
            ->andReturnSelf();
        $messageBus = new MessageBus($dispatcher);

        $messageBus->register($mappings);

        $this->assertTrue(true, 'Command dispatched');
    }
}
