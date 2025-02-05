<?php

namespace App\Models\User\Contracts;

use AlanGiacomin\LaravelBasePack\Repositories\IRepository;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;

interface IUserRepository extends IRepository
{
    public function findByEmail(string $email): ?User;

    public function getAll(): Collection;
}
