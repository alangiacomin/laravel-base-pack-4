<?php

namespace AlanGiacomin\LaravelBasePack\Commands\Contracts;

use AlanGiacomin\LaravelBasePack\QueueObject\Contracts\IQueueObject;

/**
 * Represents a command interface that extends the functionality of IQueueObject.
 * This interface is intended to define the structure for command objects
 * that interact with or implement queue-related operations.
 */
interface ICommand extends IQueueObject {}
