<?php

namespace AlanGiacomin\LaravelBasePack\Repositories;

abstract class Repository implements IRepository
{
    protected string $model;

    /**
     * Returns default element from repository
     */
    protected function default(bool $allowNull = true): ?static
    {
        return $allowNull ? null : new static();
    }
}
