<?php

namespace AlanGiacomin\LaravelBasePack\Controllers;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    final public function executeCommand(ICommand $command): JsonResponse
    {
        return response()->json(app(IMessageBus::class)->execute($command));
    }
}
