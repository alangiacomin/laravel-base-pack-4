<?php

namespace Tests\Notifications;

use AlanGiacomin\LaravelBasePack\Events\Contracts\IEvent;
use AlanGiacomin\LaravelBasePack\Notifications\Notification;

describe('Notification', function () {
    it('creates a new Notification instance with the provided IEvent', function () {
        $mockEvent = $this->SetMock(IEvent::class);
        $notification = new Notification($mockEvent);

        expect($notification)
            ->toBeInstanceOf(Notification::class)
            ->and($notification)->toHaveProtectedProperty('event', $mockEvent);
    });

    it('returns the correct broadcast name from the event', function () {
        $expectedResult = 'event.fullName';
        $mockEvent = $this->SetMock(IEvent::class);
        $mockEvent->shouldReceive('fullName')
            ->once()
            ->andReturn($expectedResult);

        $notification = new Notification($mockEvent);

        expect($notification->broadcastAs())->toBe($expectedResult);
    });

    it('returns the correct delivery channels', function () {
        $mockEvent = $this->SetMock(IEvent::class);
        $mockNotifiable = new class() {};

        $notification = new Notification($mockEvent);

        expect($notification->via($mockNotifiable))->toBe(['broadcast']);
    });

    it('returns the correct array representation of the notification', function () {
        $mockEvent = $this->SetMock(IEvent::class);
        $expectedProps = ['key' => 'value'];
        $mockEvent->shouldReceive('props')
            ->once()
            ->andReturn($expectedProps);

        $mockNotifiable = new class() {};
        $notification = new Notification($mockEvent);

        expect($notification->toArray($mockNotifiable))->toBe($expectedProps);
    });
});
