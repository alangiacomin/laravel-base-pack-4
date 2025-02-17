<?php

namespace Tests\FakeClasses;

use AlanGiacomin\LaravelBasePack\Models\Contracts\IModel;
use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use Tests\FakeClasses\Contracts\IExampleModelRepository;

class ExampleModelRepository extends Repository implements IExampleModelRepository
{
    //
    public function findById(int $id): ?IModel
    {
        // TODO: Implement findById() method.
    }
}
