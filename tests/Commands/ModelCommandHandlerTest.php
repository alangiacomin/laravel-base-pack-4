<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Commands;

use AlanGiacomin\LaravelBasePack\Commands\ModelCommandHandler;
use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\Models\Contracts\IModel;
use AlanGiacomin\LaravelBasePack\Repositories\IRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionException;
use Tests\TestCase;

class ModelCommandHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $commandHandler = new class() extends ModelCommandHandler
        {
            public IModel $model;

            protected function execute() {}
        };
        $this->commandHandler = $this->MockObject(get_class($commandHandler));
    }

    public function test_set_typed_model_sets_model_when_repository_returns_valid_model()
    {
        $model = $this->MockObject(IModel::class);
        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->andReturn($model);

        $this->command->modelId = 1;

        $this->commandHandler
            ->shouldReceive('makeRepository')
            ->once()
            ->andReturn($this->repository);
        $this->commandHandler->command = $this->command;

        $this->commandHandler->setTypedModel();

        $this->assertInstanceOf(IModel::class, $this->commandHandler->model);
    }

    /**
     * @throws ReflectionException
     * @throws BasePackException
     */
    public function test_set_typed_model_throws_exception_when_repository_returns_null()
    {
        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->andReturn(null);

        $this->command->modelId = 1;

        $this->commandHandler
            ->shouldReceive('makeRepository')
            ->once()
            ->andReturn($this->repository);
        $this->commandHandler->command = $this->command;

        $this->expectException(BasePackException::class);
        $this->expectExceptionMessage('model not found');

        $this->commandHandler->setTypedModel();
    }

    public function test_make_repository_returns_correct_instance()
    {
        $tokens = ['Namespace', 'To', 'Model'];
        $modelName = 'ExampleModel';
        $expectedClass = 'Namespace\\To\\Model\\Contracts\\IExampleModelRepository';

        app()->bind($expectedClass, fn () => $this->repository);

        $repository = $this->commandHandler->makeRepository($tokens, $modelName);

        $this->assertInstanceOf(IRepository::class, $repository);
    }

    public function test_make_repository_throws_exception_when_repository_cannot_be_resolved()
    {
        $tokens = ['Namespace', 'Invalid'];
        $modelName = 'NonExistentModel';

        $this->expectException(BindingResolutionException::class);

        $this->commandHandler->makeRepository($tokens, $modelName);
    }
}
