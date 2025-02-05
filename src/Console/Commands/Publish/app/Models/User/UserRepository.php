<?php

namespace App\Models\User;

use AlanGiacomin\LaravelBasePack\Repositories\Repository;
use App\Models\User\Contracts\IUserRepository;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository extends Repository implements IUserRepository
{
    public function findById(int $id): ?User
    {
        $user = User::find($id);

        return $user ?? null;
    }

    public function findByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        return $user ?? null;
    }

    public function getAll(): Collection
    {
        return User::all();
    }
}
