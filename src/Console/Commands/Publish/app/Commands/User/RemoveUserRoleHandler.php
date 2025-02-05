<?php

namespace App\Commands\User;

use AlanGiacomin\LaravelBasePack\Commands\ModelCommandHandler;
use App\Models\User\User;

class RemoveUserRoleHandler extends ModelCommandHandler
{
    public RemoveUserRole $command;

    public User $model;

    public function execute(): void
    {
        $this->model->removeRole($this->command->role);
    }

    public function getResponse(): object|string|null
    {
        return $this->model;
    }
}
