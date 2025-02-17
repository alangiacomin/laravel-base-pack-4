<?php

namespace Tests\FakeClasses;

use AlanGiacomin\LaravelBasePack\Commands\ModelCommandHandler;

class ExampleModelCommandHandler extends ModelCommandHandler
{
    public $command;

    public ExampleModel $model;

    protected function handleObject(): void {}

    protected function execute() {}
}
