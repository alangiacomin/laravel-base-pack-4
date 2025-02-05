<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User\User;
use App\Models\User\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::all()->where('email', 'admin@admin.com')->count() == 0) {
            $user = (new UserFactory())->create([
                'name' => 'Admin User',
                'email' => 'admin@admin.com',
                'password' => Hash::make('rew453!rew453!'),
            ]);

            $user->assignRole(RoleEnum::ADMIN);
            $user->assignRole(RoleEnum::MANAGER);
        }

        if (User::all()->where('email', 'test@example.com')->count() == 0) {
            $user = (new UserFactory())->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('rew453!rew453!'),
            ]);
            $user->assignRole(RoleEnum::MANAGER);
        }

        if (User::all()->where('email', 'test2@example.com')->count() == 0) {
            $user = (new UserFactory())->create([
                'name' => 'Test 2 User',
                'email' => 'test2@example.com',
                'password' => Hash::make('rew453!rew453!'),
            ]);
        }

        $users = (new UserFactory(10))->create();
        foreach ($users as $user) {
            $user->assignRole(RoleEnum::MANAGER);
        }
    }
}
