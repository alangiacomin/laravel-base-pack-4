<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Notifications;

use AlanGiacomin\LaravelBasePack\Notifications\Notification;
use ReflectionException;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->notification = $this->MockObject(Notification::class, true, false, $this->event);
    }

    public function test_should_create_a_new_notification_instance(): void
    {
        $this->assertInstanceOf(Notification::class, $this->notification);
    }

    /**
     * @throws ReflectionException
     */
    public function test_should_create_a_new_notification_instance_with_the_provided_event(): void
    {
        $this->assertSameProtectedProperty($this->event, $this->notification, 'event');
    }

    public function test_should_return_the_correct_broadcast_name_from_the_event(): void
    {
        $broadcastName = 'event.fullName';
        $this->event->shouldReceive('fullName')
            ->andReturn($broadcastName);

        $result = $this->notification->broadcastAs();

        $this->assertEquals($broadcastName, $result);
    }

    public function test_should_return_the_correct_delivery_channels(): void
    {
        $channels = ['broadcast'];

        $notifiable = new class() {};
        $result = $this->notification->via($notifiable);

        $this->assertEquals($channels, $result);
    }

    public function test_should_return_the_correct_array_representation_of_the_notification(): void
    {
        $eventProps = ['key' => 'value'];
        $this->event->shouldReceive('props')
            ->andReturn($eventProps);

        $notifiable = new class() {};
        $result = $this->notification->toArray($notifiable);

        $this->assertEquals($eventProps, $result);
    }
}
