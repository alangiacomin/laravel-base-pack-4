<?php

use AlanGiacomin\LaravelBasePack\Commands\CommandHandler;
use AlanGiacomin\LaravelBasePack\Commands\CommandResult;
use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use Tests\FakeClasses\ExampleCommand;

beforeEach(function () {
    $this->command = new ExampleCommand();
    $this->command->userId = 1;

    $this->handlerMock = Mockery::mock(CommandHandler::class)->makePartial();
    $this->handlerMock->shouldAllowMockingProtectedMethods();
    $this->handlerMock->command = $this->command;

    $this->SetMock(IMessageBus::class, true);
});

afterEach(function () {
    Mockery::close();
});

describe('CommandHandler::handle', function () {
    it('handles the command successfully without any rules',
        /**
         * @throws Throwable
         */ function () {
            $mockCommand = $this->SetMock(ICommand::class);
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                protected function handleObject(): void {}

                protected function execute() {}
            };
            $commandHandler->command = $mockCommand;

            $result = $commandHandler->handle($mockCommand);

            expect($result)
                ->toBeInstanceOf(CommandResult::class)
                ->and($result->success)->toBeTrue()
                ->and($result->result)->toBeInstanceOf(stdClass::class)
                ->and($result->errors)->toBeEmpty();
        });

    it('fails the command due to rules validation errors',
        /**
         * @throws Throwable
         */ function () {
            $mockCommand = $this->SetMock(ICommand::class);
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                public function rules(): ?iterable
                {
                    return ['Error 1', 'Error 2'];
                }

                protected function execute() {}

                protected function handleObject(): void {}
            };
            $commandHandler->command = $mockCommand;

            $result = $commandHandler->handle($mockCommand);

            expect($result)
                ->toBeInstanceOf(CommandResult::class)
                ->and($result->success)->toBeFalse()
                ->and($result->result)->toBeInstanceOf(stdClass::class)
                ->and($result->errors)->toMatchArray(['Error 1', 'Error 2']);
        });

    it('handles exception during typed object setup',
        /**
         * @throws Throwable
         */ function () {
            $mockCommand = $this->SetMock(ICommand::class);
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                protected function handleObject(): void {}

                protected function setTypedModel(): void
                {
                    throw new BasePackException('Typed object setup failed');
                }

                protected function execute() {}
            };
            $commandHandler->command = $mockCommand;

            $result = $commandHandler->handle($mockCommand);

            expect($result)
                ->toBeInstanceOf(CommandResult::class)
                ->and($result->success)->toBeFalse()
                ->and($result->result)->toBeInstanceOf(stdClass::class)
                ->and($result->errors)->toMatchArray(['Typed object setup failed']);
        });

    it('successfully sets and uses default response',
        /**
         * @throws Throwable
         */ function () {
            $mockCommand = $this->SetMock(ICommand::class);
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                protected function handleObject(): void
                {
                    $this->result->setSuccess();
                }

                protected function getResponse(): string
                {
                    return 'Default response';
                }

                protected function execute() {}
            };
            $commandHandler->command = $mockCommand;

            $result = $commandHandler->handle($mockCommand);

            expect($result)
                ->toBeInstanceOf(CommandResult::class)
                ->and($result->success)->toBeTrue()
                ->and($result->result)->toBe('Default response')
                ->and($result->errors)->toBeEmpty();
        });
});

describe('CommandHandler::rules', function () {
    it('returns null for rules method', function () {
        $commandHandler = new class() extends CommandHandler
        {
            protected function execute() {}
        };

        $result = $commandHandler->rules();

        expect($result)->toBeNull();
    });
});

describe('CommandHandler::failed', function () {
    it('calls the parent failed method', function () {
        $mockCommand = $this->SetMock(ICommand::class);
        $exception = new Exception('Parent failed called');

        $commandHandler = new class() extends CommandHandler
        {
            public $command;

            public CommandResult $result;

            public bool $parentFailedCalled = false;

            public function testCallFailed(Throwable $exception): void
            {
                $this->failed($exception);
            }

            protected function manageFailed(Throwable $exception): void
            {
                $this->parentFailedCalled = true;
            }

            protected function execute() {}
        };
        $commandHandler->result = new CommandResult();
        $commandHandler->command = $mockCommand;

        // Trigger the failed method
        $commandHandler->testCallFailed($exception);

        expect($commandHandler->parentFailedCalled)->toBeTrue();
    });

    it('sets the result to failure with exception message', function () {
        $mockCommand = $this->SetMock(ICommand::class);
        $exception = new Exception('Test failure');

        $commandHandler = new class() extends CommandHandler
        {
            public $command;

            public CommandResult $result;

            public function testCallFailed(Throwable $exception): void
            {
                $this->failed($exception);
            }

            protected function execute() {}
        };
        $commandHandler->result = new CommandResult();
        $commandHandler->command = $mockCommand;

        // Trigger the failed method
        $commandHandler->testCallFailed($exception);

        expect($commandHandler->result->success)->toBeFalse()
            ->and($commandHandler->result->errors)->toMatchArray(['Test failure']);
    });
});

describe('CommandHandler::checkRules', function () {
    it('returns no errors when rules are null',
        /**
         * @throws Throwable
         */
        function () {
            // Arrange: Create a handler without failing rules
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                protected function handleObject(): void {}

                protected function execute() {}
            };
            $command = new ExampleCommand();

            // Act: Call handle, which indirectly tests checkRules
            $result = $commandHandler->handle($command);

            // Assert: No errors and success should be true
            expect($result->success)->toBeTrue()
                ->and($result->errors)->toBeEmpty();
        });

    it('returns no errors when rules are empty',
        /**
         * @throws Throwable
         */
        function () {
            // Arrange: Create a handler without failing rules
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                public function rules(): ?iterable
                {
                    if (1 == 2) {
                        return yield 'error';
                    }
                }

                protected function handleObject(): void {}

                protected function execute() {}
            };
            $command = new ExampleCommand();

            // Act: Call handle, which indirectly tests checkRules
            $result = $commandHandler->handle($command);

            // Assert: No errors and success should be true
            expect($result->success)->toBeTrue()
                ->and($result->errors)->toBeEmpty();
        });

    it('returns errors when rules have errors',
        /**
         * @throws Throwable
         */
        function () {
            // Arrange: Create a handler with failing rules
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                public function rules(): ?iterable
                {
                    return new ArrayIterator(['Error 1', 'Error 2']); // Simulate rule errors
                }

                protected function handleObject(): void {}

                protected function execute() {}
            };
            $command = new ExampleCommand();

            // Act: Call handle, which indirectly tests checkRules
            $result = $commandHandler->handle($command);

            // Assert: Success should be false, and errors should match
            expect($result->success)->toBeFalse()
                ->and($result->errors)->toBe(['Error 1', 'Error 2']);
        });

    it('returns exception message when rules throw a Throwable',
        /**
         * @throws Throwable
         */
        function () {
            // Arrange: Create a handler that throws an exception in rules
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                public function rules(): ?iterable
                {
                    throw new Exception('An exception occurred'); // Simulate exception
                }

                protected function handleObject(): void {}

                protected function execute() {}
            };
            $command = new ExampleCommand();

            // Act: Call handle, which indirectly tests checkRules
            $result = $commandHandler->handle($command);

            // Assert: Success should be false, and the error should contain exception message
            expect($result->success)->toBeFalse()
                ->and($result->errors)->toBe(['An exception occurred']);
        });
});

describe('CommandHandler::setTypedObject', function () {
    it('throws a BasePackException when the command property is not set',
        /**
         * @throws BasePackException
         * @throws Throwable
         */
        function () {
            // Arrange
            $commandHandler = new class() extends CommandHandler
            {
                protected function execute() {}
            };
            $command = new ExampleCommand();

            // Act
            $result = $commandHandler->handle($command);

            // Assert:
            expect($result->success)->toBeFalse()
                ->and($result->errors)->toBe(["Tests\FakeClasses\ExampleCommand: 'command' property must be defined"]);
        });

    it('does not throw any exception when the command property is set',
        /**
         * @throws BasePackException
         * @throws Throwable
         */
        function () {
            // Arrange
            $commandHandler = new class() extends CommandHandler
            {
                public $command;

                protected function execute() {}
            };
            $command = new ExampleCommand();

            // Act
            $result = $commandHandler->handle($command);

            // Assert:
            expect($result->success)->toBeTrue()
                ->and($result->errors)->toBeEmpty();
        });
});
