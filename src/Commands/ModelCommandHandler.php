<?php

namespace AlanGiacomin\LaravelBasePack\Commands;

use AlanGiacomin\LaravelBasePack\Exceptions\BasePackException;
use AlanGiacomin\LaravelBasePack\Repositories\IRepository;
use AllowDynamicProperties;
use ReflectionException;
use ReflectionProperty;

/**
 * @property ModelCommand $command
 */
#[AllowDynamicProperties] abstract class ModelCommandHandler extends CommandHandler
{
    protected IRepository $modelRepository;

    /**
     * @throws BasePackException
     * @throws ReflectionException
     */
    protected function setTypedModel(): void
    {
        $rp = new ReflectionProperty(get_class($this), 'model');
        $tokens = explode('\\', $rp->getType()->getName());
        $modelName = array_pop($tokens);
        $this->modelRepository = $this->makeRepository($tokens, $modelName);

        $model = $this->modelRepository->findById($this->command->modelId);
        if ($model != null) {
            $this->model = $model;
        } else {
            throw new BasePackException('model not found');
        }
    }

    /**
     * Creates and returns a repository instance for the specified model.
     *
     * @param  array  $tokens  An array of namespace parts leading to the model's directory.
     * @param  string  $modelName  The name of the model for which the repository should be created.
     * @return IRepository The repository instance corresponding to the provided model name.
     */
    protected function makeRepository(array $tokens, string $modelName): IRepository
    {
        return app(implode('\\', $tokens).'\\Contracts\\I'.$modelName.'Repository');
    }
}
