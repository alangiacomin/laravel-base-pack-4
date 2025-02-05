<?php

namespace AlanGiacomin\LaravelBasePack\QueueObject\Contracts;

use Illuminate\Contracts\Queue\ShouldQueue;

interface IQueueObject extends ShouldQueue
{
    public function handlerClassName(): string;

    public function fullName(): string;

    public function props(): array;
}
