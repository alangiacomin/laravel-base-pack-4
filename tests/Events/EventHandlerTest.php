<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Events;

use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use ReflectionException;
use Tests\TestCase;

class EventHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->event->userId = 1;
        $this->event->shouldReceive('onQueue');

        $this->eventHandler->event = $this->event;
    }

    public function test_should_execute_the_handle_method_without_errors(): void
    {
        $userMock = $this->MockObject(User::class);
        $userMock->shouldReceive('setAttribute')->once();
        $userMock->shouldReceive('notify')->once();
        $userMock->id = 1;

        Auth::shouldReceive('user')->once()->andReturn($userMock);
        Auth::shouldReceive('loginUsingId')->once()->with(1);
        Auth::shouldReceive('check')->andReturn(true);

        $this->eventHandler
            ->shouldReceive('handleObject')
            ->once()
            ->andReturn();

        $this->eventHandler->handle($this->event);
    }

    /**
     * @throws ReflectionException
     */
    public function test_should_save_the_event_in_the_queue_object(): void
    {
        $userMock = $this->MockObject(User::class);
        $userMock->shouldReceive('setAttribute')->once();
        $userMock->shouldReceive('notify')->once();
        $userMock->id = 1;

        Auth::shouldReceive('user')->once()->andReturn($userMock);
        Auth::shouldReceive('loginUsingId')->once()->with(1);
        Auth::shouldReceive('check')->andReturn(true);

        $this->eventHandler->handle($this->event);

        $this->assertSame($this->event, $this->eventHandler->getQueueObject());
    }

    public function test_should_throw_a_base_pack_exception_when_event_property_not_set(): void
    {
        $this->event->shouldReceive('fullName')->andReturn('My\Event\ClassName');

        unset($this->eventHandler->event);

        $this->expectException(BasePackException::class);
        $this->expectExceptionMessage("My\Event\ClassName: 'event' property must be defined");

        $this->eventHandler->handle($this->event);
    }

    public function test_should_not_throw_any_exception_when_event_property_is_set(): void
    {
        $userMock = $this->MockObject(User::class);
        $userMock->shouldReceive('setAttribute')->once();
        $userMock->shouldReceive('notify')->once();
        $userMock->id = 1;

        Auth::shouldReceive('user')->once()->andReturn($userMock);
        Auth::shouldReceive('loginUsingId')->once()->with(1);
        Auth::shouldReceive('check')->andReturn(true);

        $this->eventHandler->event = $this->event; // Ensure the event property is set

        $this->eventHandler->handle($this->event);
    }

    public function test_should_log_in_using_correct_user_id_when_user_id_greater_than_zero(): void
    {
        $this->event->userId = 10; // Positive user ID
        $userMock = $this->MockObject(User::class);
        $userMock->shouldReceive('setAttribute')->once();
        $userMock->shouldReceive('notify')->once();
        $userMock->id = 10;

        Auth::shouldReceive('user')->once()->andReturn($userMock);
        Auth::shouldReceive('loginUsingId')->once()->with(10); // Use correct user ID
        Auth::shouldReceive('check')->andReturn(true);

        $this->eventHandler->handle($this->event);
    }
}
