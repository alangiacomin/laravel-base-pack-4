<?php

namespace AlanGiacomin\LaravelBasePack\Controllers;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IMessageBus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

/**
 * Abstract class that serves as a base controller for handling requests.
 *
 * This controller utilizes middleware for authorization and validation. It
 * provides a method to execute commands through a message bus and return
 * results in a JSON response format.
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Executes the given command using the message bus and returns a JSON response.
     *
     * @param  ICommand  $command  The command to be executed.
     * @return JsonResponse The JSON response containing the result of the command execution.
     *
     * @noinspection PhpUnused
     */
    final public function executeCommand(ICommand $command): JsonResponse
    {
        $result = app(IMessageBus::class)->execute($command);

        return response()->json($result);
    }
}
