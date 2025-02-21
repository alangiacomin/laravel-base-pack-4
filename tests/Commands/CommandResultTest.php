<?php

/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Commands;

use AlanGiacomin\LaravelBasePack\Commands\CommandResult;
use stdClass;
use Tests\TestCase;

class CommandResultTest extends TestCase
{
    public function test_should_initialize_success_as_true(): void
    {
        $commandResult = new CommandResult();
        $this->assertTrue($commandResult->success);
    }

    public function test_should_initialize_result_with_a_default_empty_object(): void
    {
        $commandResult = new CommandResult();
        $this->assertInstanceOf(stdClass::class, $commandResult->result);
    }

    public function test_should_initialize_errors_as_an_empty_array(): void
    {
        $commandResult = new CommandResult();
        $this->assertIsArray($commandResult->errors);
        $this->assertEmpty($commandResult->errors);
    }

    public function test_should_set_success_to_true(): void
    {
        $this->commandResult->setSuccess();
        $this->assertTrue($this->commandResult->success);
    }

    public function test_should_set_result_to_the_given_value(): void
    {
        $this->commandResult->setSuccess('custom result');
        $this->assertEquals('custom result', $this->commandResult->result);
    }

    public function test_should_reset_result_to_default_if_no_value_is_passed(): void
    {
        $this->commandResult->setSuccess();
        $this->assertInstanceOf(stdClass::class, $this->commandResult->result);
    }

    public function test_should_reset_errors_to_an_empty_array(): void
    {
        $this->commandResult->errors = ['error1', 'error2'];
        $this->commandResult->setSuccess();
        $this->assertIsArray($this->commandResult->errors);
        $this->assertEmpty($this->commandResult->errors);
    }

    public function test_should_set_success_to_false(): void
    {
        $this->commandResult->setFailure();
        $this->assertFalse($this->commandResult->success);
    }

    public function test_should_set_errors_to_the_given_array(): void
    {
        $errors = ['error1', 'error2'];
        $this->commandResult->setFailure($errors);
        $this->assertEquals($errors, $this->commandResult->errors);
    }

    public function test_should_convert_a_string_error_to_an_array(): void
    {
        $this->commandResult->setFailure('single error');
        $this->assertEquals(['single error'], $this->commandResult->errors);
    }

    public function test_should_reset_result_to_default_on_failure(): void
    {
        $this->commandResult->result = 'custom result';
        $this->commandResult->setFailure();
        $this->assertInstanceOf(stdClass::class, $this->commandResult->result);
    }
}
