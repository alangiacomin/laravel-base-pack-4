<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Commands;

use AlanGiacomin\LaravelBasePack\Commands\CommandResult;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use EmptyIterator;
use Exception;
use stdClass;
use Tests\TestCase;

class CommandHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->command->userId = 1;

        $this->commandHandler->shouldReceive('execute');
        $this->commandHandler->shouldReceive('handleObject');
        $this->commandHandler->command = $this->command;
    }

    public function test_handle_successfully_without_rules(): void
    {
        $result = $this->commandHandler->handle($this->command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertInstanceOf(stdClass::class, $result->result);
        $this->assertEmpty($result->errors);
    }

    public function test_handle_successfully_with_empty_rules(): void
    {
        $this->commandHandler
            ->shouldReceive('rules')
            ->once()
            ->andReturn(new EmptyIterator());

        $result = $this->commandHandler->handle($this->command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertInstanceOf(stdClass::class, $result->result);
        $this->assertEmpty($result->errors);
    }

    public function test_fails_command_due_to_rules_validation_errors(): void
    {
        $this->commandHandler
            ->shouldReceive('rules')
            ->once()
            ->andReturn(['Error 1', 'Error 2']);

        $result = $this->commandHandler->handle($this->command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertFalse($result->success);
        $this->assertInstanceOf(stdClass::class, $result->result);
        $this->assertEquals(['Error 1', 'Error 2'], $result->errors);
    }

    public function test_fails_command_due_to_exception_rules(): void
    {
        $this->commandHandler
            ->shouldReceive('rules')
            ->once()
            ->andThrow(new Exception('Error 1'));

        $result = $this->commandHandler->handle($this->command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertFalse($result->success);
        $this->assertInstanceOf(stdClass::class, $result->result);
        $this->assertEquals(['Error 1'], $result->errors);
    }

    public function test_handles_exception_during_typed_object_setup(): void
    {
        $this->commandHandler
            ->shouldReceive('setTypedModel')
            ->once()
            ->andThrow(new BasePackException('Typed object setup failed'));

        $result = $this->commandHandler->handle($this->command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertFalse($result->success);
        $this->assertInstanceOf(stdClass::class, $result->result);
        $this->assertEquals(['Typed object setup failed'], $result->errors);
    }

    public function test_successfully_sets_and_uses_default_response(): void
    {
        $this->commandHandler
            ->shouldReceive('getResponse')
            ->once()
            ->andReturn('Default response');

        $result = $this->commandHandler->handle($this->command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertEquals('Default response', $result->result);
        $this->assertEmpty($result->errors);
    }

    public function test_rules_returns_null(): void
    {
        $result = $this->commandHandler->rules();

        $this->assertNull($result);
    }

    public function test_failed_calls(): void
    {
        $exception = new Exception('Parent failed called');
        $this->commandHandler->result = $this->commandResult;
        $this->commandHandler->result
            ->shouldReceive('setFailure')
            ->once();

        $this->commandHandler->failed($exception);
    }

    public function test_should_throw_a_base_pack_exception_when_event_property_not_set(): void
    {
        $this->command->shouldReceive('fullName')->andReturn('My\Command\ClassName');

        unset($this->commandHandler->command);

        $result = $this->commandHandler->handle($this->command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertFalse($result->success);
        $this->assertInstanceOf(stdClass::class, $result->result);
        $this->assertEquals(['My\Command\ClassName: \'command\' property must be defined'], $result->errors);
    }
}
