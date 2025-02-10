<?php

namespace Tests\Events;

use AlanGiacomin\LaravelBasePack\Events\EventHandler;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\FakeClasses\ExampleEvent;
use Throwable;

beforeEach(function () {
    $this->event = new ExampleEvent();
    $this->event->userId = 1;

    $this->handlerMock = Mockery::mock(EventHandler::class)->makePartial();
    $this->handlerMock->shouldAllowMockingProtectedMethods();
    $this->handlerMock->event = $this->event;

    $this->SetMock(IMessageBus::class, true);
});

afterEach(function () {
    Mockery::close();
});

describe('EventHandler', function () {
    it('executes the handle method without errors',
        /**
         * @throws BasePackException
         * @throws Throwable
         */
        function () {
            // Arrange

            $userMock = Mockery::mock(User::class);
            $userMock->shouldReceive('setAttribute')->once();
            $userMock->shouldReceive('notify')->once();
            $userMock->id = 1;
            Auth::shouldReceive('user')->once()->andReturn($userMock);
            Auth::shouldReceive('loginUsingId')->once()->with(1);
            Auth::shouldReceive('check')->andReturn(true);

            /** @noinspection PhpMockeryInvalidMockingMethodInspection */
            $this->handlerMock
                ->shouldReceive('handleObject')
                ->once()
                ->andReturn();

            // Act & Assert
            $this->handlerMock->handle($this->event);
        });

    it('should save the event in the queue object',
        /**
         * @throws BasePackException
         * @throws Throwable
         */
        function () {
            // Arrange

            $userMock = Mockery::mock(User::class);
            $userMock->shouldReceive('setAttribute')->once();
            $userMock->shouldReceive('notify')->once();
            $userMock->id = 1;
            Auth::shouldReceive('user')->once()->andReturn($userMock);
            Auth::shouldReceive('loginUsingId')->once()->with(1);
            Auth::shouldReceive('check')->andReturn(true);

            // Act & Assert

            $this->handlerMock->handle($this->event);

            expect($this->handlerMock)->toHaveProtectedProperty('queueObject', $this->event);
        });

    it('throws a BasePackException when the event property is not set',
        /**
         * @throws BasePackException
         * @throws Throwable
         */
        function () {
            // Arrange

            // Ensure the event property is not set
            unset($this->handlerMock->event);

            // Act & Assert
            $this->expectException(BasePackException::class);
            $this->expectExceptionMessage("Tests\FakeClasses\ExampleEvent: 'event' property must be defined");

            $this->handlerMock->handle($this->event);
        });

    it('does not throw any exception when the event property is set',
        /**
         * @throws Throwable
         */
        function () {
            // Arrange
            // Ensure the event property is set
            $this->handlerMock->event = $this->event;

            // Act & Assert
            // No exception should be thrown
            expect(fn () => $this->handlerMock->handle($this->event))->not->toThrow(BasePackException::class);
        });

    it('logs in using the correct user ID when userId is greater than zero',
        /**
         * @throws Throwable
         */
        function () {
            // Arrange
            $this->event->userId = 10; // Positive user ID
            $userMock = Mockery::mock(User::class);
            $userMock->shouldReceive('setAttribute')->once();
            $userMock->shouldReceive('notify')->once();
            $userMock->id = 10;
            Auth::shouldReceive('loginUsingId')->once()->with(10);
            Auth::shouldReceive('check')->andReturn(true);
            Auth::shouldReceive('user')->once()->andReturn($userMock);

            // Act
            $this->handlerMock->handle($this->event);
        });

    it('does not log in if userId is zero or less',
        /**
         * @throws Throwable
         */
        function () {
            // Arrange
            $this->event->userId = 0; // Non-positive user ID
            Auth::shouldReceive('loginUsingId')->never();
            Auth::shouldReceive('check')->andReturn(false);

            // Act
            $this->handlerMock->handle($this->event);
        });

    it('sends a notification if the user is logged in',
        /**
         * @throws BasePackException
         * @throws Throwable
         */ function () {
            // Arrange
            $userMock = Mockery::mock(User::class);
            $userMock->shouldReceive('notify')->once();
            $userMock->shouldReceive('setAttribute')->once();
            $userMock->id = 1;
            Auth::shouldReceive('loginUsingId')->once()->with(1);

            Auth::shouldReceive('check')->once()->andReturn(true);
            Auth::shouldReceive('user')->once()->andReturn($userMock);

            // Act
            $this->handlerMock->handle($this->event);
        });

    it('does not send a notification if no user is logged in',
        /**
         * @throws BasePackException
         * @throws Throwable
         */ function () {
            // Arrange
            $userMock = Mockery::mock(User::class);
            $userMock->shouldReceive('setAttribute')->once();
            $userMock->id = 1;
            Auth::shouldReceive('check')->once()->andReturn(false);
            Auth::shouldReceive('user')->never();
            Auth::shouldReceive('loginUsingId')->once()->with(1);

            // Act
            $this->handlerMock->handle($this->event);

            // Assert
            expect($this->event->queue)->toBeEmpty();
        });

    it('sets the event queue to notifications before sending a notification',
        /**
         * @throws BasePackException
         * @throws Throwable
         */ function () {
            // Arrange
            $userMock = Mockery::mock(User::class);
            $userMock->shouldReceive('notify')->once();
            $userMock->shouldReceive('setAttribute')->once();
            Auth::shouldReceive('loginUsingId')->once()->with(1);
            $userMock->id = 1;

            Auth::shouldReceive('check')->once()->andReturn(true);
            Auth::shouldReceive('user')->once()->andReturn($userMock);

            // Act
            $this->handlerMock->handle($this->event);

            // Assert
            expect($this->event->queue)->toBe('notifications');
        });
});
