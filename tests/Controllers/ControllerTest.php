<?php

namespace Tests\Controllers;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Controllers\Controller;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use Illuminate\Http\JsonResponse;

describe('Controller', function () {
    it('should return a JSON response when executing a command', function () {
        // Arrange
        $expectedResponse = ['status' => 'success'];

        $command = $this->SetMock(ICommand::class);

        $mockMessageBus = $this->SetMock(IMessageBus::class, true);
        $mockMessageBus->shouldReceive('execute')
            ->once()
            ->with($command)
            ->andReturn($expectedResponse);

        $controller = new class() extends Controller {};

        // Act
        $response = $controller->executeCommand($command);

        // Assert
        expect($response)->toBeInstanceOf(JsonResponse::class)
            ->and($response->getStatusCode())->toBe(200)
            ->and($response->getData(true))->toBe($expectedResponse);

        print_r($response->getData());
    });
});
