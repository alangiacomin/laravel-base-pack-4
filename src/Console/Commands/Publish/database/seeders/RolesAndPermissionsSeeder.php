<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionEnum::cases() as $case) {
            Permission::create(['name' => $case]);
        }

        // create roles and assign created permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // or may be done by chaining
        Role::create(['name' => RoleEnum::ADMIN])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => RoleEnum::MANAGER])
            ->givePermissionTo([PermissionEnum::ADMIN_VIEW, PermissionEnum::ADMIN_USERS]);
    }
}
