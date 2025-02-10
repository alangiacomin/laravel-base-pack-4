<?php

namespace Tests\Events;

use Alangiacomin\PhpUtils\Guid;
use stdClass;
use Tests\FakeClasses\ExampleEvent;

beforeEach(function () {
    // Using an anonymous class to implement Event (since Event is abstract)
    $this->event = new ExampleEvent();
});

describe('Event', function () {
    dataset('viaQueuesEmpty', [
        'null' => null,
        'empty' => '',
        'spaces' => '  ',
    ]);
    dataset('viaQueuesSet', [
        'myQueue' => 'myQueue',
        'otherQueue' => 'otherQueue',
        'notifications' => 'notifications',
    ]);

    it('returns the correct broadcast name', function () {
        expect($this->event->broadcastAs())->toBe('Tests\FakeClasses\ExampleEvent');
    });

    it('returns the correct delivery channels', function () {
        $notifiable = new stdClass();
        expect($this->event->via($notifiable))->toBe(['broadcast']);
    });

    it('returns the correct array representation', function () {
        $notifiable = new stdClass();

        $result = $this->event->toArray($notifiable);
        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['id', 'userId'])
            ->and($result['userId'])->toBe(0)
            ->and(Guid::isValid($result['id']))->toBeTrue();
    });

    it('returns the correct queues for each channel', function () {
        $result = $this->event->viaQueues();

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys(['mail', 'slack', 'broadcast'])
            ->and($result['mail'])->toBe('mail-queue')
            ->and($result['slack'])->toBe('slack-queue')
            ->and($result['broadcast'])->toBe('default');
    });

    it('returns the default queue if not specified', function (string $queue) {
        $this->event->onQueue($queue);
        $result = $this->event->viaQueues();

        expect($result)->toBeArray()
            ->and($result)->toHaveKey('broadcast')
            ->and($result['broadcast'])->toBe($queue);
    })->with('viaQueuesSet');
});
