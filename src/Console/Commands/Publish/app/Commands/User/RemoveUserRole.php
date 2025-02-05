<?php

namespace App\Commands\User;

use AlanGiacomin\LaravelBasePack\Commands\ModelCommand;

class RemoveUserRole extends ModelCommand
{
    public string $role;
}
