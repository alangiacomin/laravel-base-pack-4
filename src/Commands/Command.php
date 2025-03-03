<?php

namespace AlanGiacomin\LaravelBasePack\Commands;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObject;

/**
 * Represents an abstract command within the system, serving as a base class
 * for specific command implementations. Manages initialization and result handling.
 */
abstract class Command extends QueueObject implements ICommand
{
    public CommandResult $result;

    /**
     * Constructor method for initializing the class.
     * It sets up the base structure and initializes the CommandResult instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->result = new CommandResult();
    }
}
