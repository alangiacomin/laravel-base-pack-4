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
        $this->modelRepository = app(implode('\\', $tokens).'\\Contracts\\I'.$modelName.'Repository');

        $model = $this->modelRepository->findById($this->command->modelId);
        if ($model != null) {
            $this->model = $model;
        } else {
            throw new BasePackException('model not found');
        }
    }
}
