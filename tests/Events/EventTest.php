<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Events;

use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\TestCase;

class EventTest extends TestCase
{
    public function test_broadcast_name_is_correct(): void
    {
        $this->event->shouldReceive('fullName')->andReturn('My\Event\ClassName');

        $result = $this->event->broadcastAs();

        $this->assertEquals('My\Event\ClassName', $result);
    }

    public function test_delivery_channels_are_correct(): void
    {
        $notifiable = new stdClass();

        $result = $this->event->via($notifiable);

        $this->assertEquals(['broadcast'], $result);
    }

    public function test_array_representation_is_correct(): void
    {
        $eventProps = ['key' => 'value'];
        $this->event->shouldReceive('props')
            ->andReturn($eventProps);

        $notifiable = new stdClass();
        $result = $this->event->toArray($notifiable);

        $this->assertEquals($eventProps, $result);
    }

    public function test_queues_for_each_channel_are_correct(): void
    {
        $result = $this->event->viaQueues();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('mail', $result);
        $this->assertEquals('mail-queue', $result['mail']);
        $this->assertArrayHasKey('slack', $result);
        $this->assertEquals('slack-queue', $result['slack']);
        $this->assertArrayHasKey('broadcast', $result);
        $this->assertEquals('default', $result['broadcast']);
    }

    public static function viaQueuesSetProvider(): array
    {
        return [
            ['myQueue'],
            ['otherQueue'],
            ['notifications'],
        ];
    }

    #[DataProvider('viaQueuesSetProvider')]
    public function test_default_queue_is_correct(string $queue): void
    {
        $this->event->onQueue($queue);

        $result = $this->event->viaQueues();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('broadcast', $result);
        $this->assertEquals($queue, $result['broadcast']);
    }
}
