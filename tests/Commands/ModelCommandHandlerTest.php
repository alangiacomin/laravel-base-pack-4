<?php

namespace Tests\Commands;

use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\Models\Contracts\IModel;
use AlanGiacomin\LaravelBasePack\Repositories\IRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Mockery;
use ReflectionException;
use Tests\FakeClasses\ExampleCommand;
use Tests\FakeClasses\ExampleModel;
use Tests\FakeClasses\ExampleModelCommandHandler;

describe('ModelCommandHandler::setTypedModel', function () {
    it('sets the model when the repository returns a valid model instance',
        /**
         * @throws ReflectionException
         * @throws BasePackException
         */
        function () {
            $mockRepository = $this->SetMock(IRepository::class);
            $mockRepository->shouldReceive('findById')->with(1)->andReturn(new ExampleModel());

            $command = new ExampleCommand();
            $command->modelId = 1;

            $commandHandler = Mockery::mock(ExampleModelCommandHandler::class)->makePartial();
            $commandHandler->shouldAllowMockingProtectedMethods();
            $commandHandler->shouldReceive('makeRepository')->once()->andReturn($mockRepository);
            $commandHandler->command = $command;

            // Act
            $commandHandler->setTypedModel();

            // Assert
            expect($commandHandler->model)->toBeInstanceOf(IModel::class);
        });

    it('throws BasePackException when the repository returns null',
        /**
         * @throws ReflectionException
         * @throws BasePackException
         */
        function () {
            $mockRepository = $this->SetMock(IRepository::class);
            $mockRepository->shouldReceive('findById')->with(1)->andReturn(null);

            $command = new ExampleCommand();
            $command->modelId = 1;

            $commandHandler = Mockery::mock(ExampleModelCommandHandler::class)->makePartial();
            $commandHandler->shouldAllowMockingProtectedMethods();
            $commandHandler->shouldReceive('makeRepository')->once()->andReturn($mockRepository);
            $commandHandler->command = $command;

            $this->expectException(BasePackException::class);
            $this->expectExceptionMessage('model not found');
            $commandHandler->setTypedModel();
        });
});

describe('ModelCommandHandler::makeRepository', function () {
    it('returns the correct repository instance when given valid tokens and a model name', function () {
        $tokens = ['Namespace', 'To', 'Model'];
        $modelName = 'ExampleModel';
        $expectedClass = 'Namespace\\To\\Model\\Contracts\\IExampleModelRepository';

        $commandHandler = Mockery::mock(ExampleModelCommandHandler::class)->makePartial();
        $commandHandler->shouldAllowMockingProtectedMethods();
        app()->bind($expectedClass, fn () => $this->SetMock(IRepository::class));

        $repository = $commandHandler->makeRepository($tokens, $modelName);

        expect($repository)->toBeInstanceOf(IRepository::class);
    });

    it('throws an exception if the repository cannot be resolved', function () {
        $tokens = ['Namespace', 'Invalid'];
        $modelName = 'NonExistentModel';
        $commandHandler = Mockery::mock(ExampleModelCommandHandler::class)->makePartial();
        $commandHandler->shouldAllowMockingProtectedMethods();

        $this->expectException(BindingResolutionException::class);

        $commandHandler->makeRepository($tokens, $modelName);
    });
});
