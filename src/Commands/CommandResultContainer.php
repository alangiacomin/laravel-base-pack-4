<?php

namespace AlanGiacomin\LaravelBasePack\Commands;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;

/**
 * Represents a container for storing and retrieving command results.
 */
class CommandResultContainer
{
    private static array $results = [];

    /**
     * Retrieves the result for the specified command ID.
     *
     * @param  string  $commandId  The ID of the command whose result is to be retrieved.
     * @return CommandResult|null The result of the command, or null if no result is found.
     */
    public static function getResult(string $commandId): ?CommandResult
    {
        return self::$results[$commandId] ?? null;
    }

    /**
     * Sets the result for the given command.
     *
     * @param  ICommand  $command  The command whose result is to be set.
     */
    public static function setResult(ICommand $command): void
    {
        self::$results[$command->id] = $command->result;
    }
}
