<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Controllers;

use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    public function test_should_return_a_json_response_when_executing_a_command(): void
    {
        $expectedResponse = ['status' => 'success'];
        $this->messageBus
            ->shouldReceive('execute')
            ->once()
            ->with($this->command)
            ->andReturn($expectedResponse);

        $response = $this->controller->executeCommand($this->command);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_should_return_successful_response_when_executing_a_command(): void
    {
        $expectedResponse = ['status' => 'success'];
        $this->messageBus
            ->shouldReceive('execute')
            ->once()
            ->with($this->command)
            ->andReturn($expectedResponse);

        $response = $this->controller->executeCommand($this->command);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_should_return_a_json_data_when_executing_a_command(): void
    {
        $expectedResponse = ['status' => 'success'];
        $this->messageBus
            ->shouldReceive('execute')
            ->once()
            ->with($this->command)
            ->andReturn($expectedResponse);

        $response = $this->controller->executeCommand($this->command);

        $this->assertEquals($expectedResponse, $response->getData(true));
    }
}
