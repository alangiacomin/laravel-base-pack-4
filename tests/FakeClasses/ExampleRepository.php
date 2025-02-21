<?php

namespace Tests\FakeClasses;

use AlanGiacomin\LaravelBasePack\Models\Contracts\IModel;
use AlanGiacomin\LaravelBasePack\Repositories\Repository;

class ExampleRepository extends Repository
{
    //
    public function findById(int $id): ?IModel
    {
        // TODO: Implement findById() method.
    }
}
