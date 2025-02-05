<?php

namespace AlanGiacomin\LaravelBasePack\Commands;

use AlanGiacomin\LaravelBasePack\Commands\Contracts\ICommand;
use AlanGiacomin\LaravelBasePack\QueueObject\QueueObject;

abstract class Command extends QueueObject implements ICommand {}
