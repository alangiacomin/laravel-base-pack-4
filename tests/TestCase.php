<?php

namespace Tests;

use Mockery;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function SetMock(string $name, bool $register = false): MockInterface|string
    {
        $mock = Mockery::mock($name);
        if ($register) {
            app()->instance($name, $mock);
        }

        return $mock;
    }
}
