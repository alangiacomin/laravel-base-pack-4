<?php

namespace Tests;

use AlanGiacomin\LaravelBasePack\Commands\CommandHandler;
use AlanGiacomin\LaravelBasePack\Commands\CommandResult;
use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Controllers\Controller;
use AlanGiacomin\LaravelBasePack\Events\Event;
use AlanGiacomin\LaravelBasePack\Events\EventHandler;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use Alangiacomin\PhpUtils\Guid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use ReflectionClass;
use ReflectionException;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    public MockInterface $repository;

    public MockInterface $event;

    public MockInterface $messageBus;

    public MockInterface $controller;

    public MockInterface $command;

    public MockInterface $commandHandler;

    public MockInterface $commandResult;

    public MockInterface $eventHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->MockObject(Repository::class);

        $this->controller = $this->MockObject(Controller::class);

        $this->messageBus = $this->MockObject(IMessageBus::class, registerInstance: true);

        $this->command = $this->MockObject(ICommand::class);
        $this->command->id = Guid::newGuid();
        $this->commandHandler = $this->MockObject(CommandHandler::class);
        $this->commandResult = $this->MockObject(CommandResult::class);

        $this->event = $this->MockObject(Event::class);
        $this->eventHandler = $this->MockObject(EventHandler::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function SetMock(...$params): MockInterface|string
    {
        return $this->MockObject(...$params);
    }

    public function MockObject(string $name, bool $partial = true, bool $registerInstance = false, ...$params): MockInterface
    {
        $mock = $params ? Mockery::mock($name, $params) : Mockery::mock($name);

        if ($partial) {
            $mock->makePartial();
        }

        $mock->shouldAllowMockingProtectedMethods();

        if ($registerInstance) {
            app()->instance($name, $mock);
        }

        return $mock;
    }

    /**
     * @throws ReflectionException
     */
    public function assertSameProtectedProperty($expected, $object, $propertyName): void
    {
        $this->assertEquals($expected, $this->getProperty($object, $propertyName));
    }

    /**
     * @throws ReflectionException
     */
    public function getProperty($object, $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);

        // $property->setAccessible(true);
        return $property->getValue($object);
    }
}
