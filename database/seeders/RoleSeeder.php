<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('template-starter.default-roles');

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role]);
        }

        $permissions = [
            "menu role",
            "index role",
            "show role",
            "create role",
            "update role",
            "delete role",
        ];

        foreach ($permissions as $permit) {
            Permission::updateOrCreate([
                'group' => "role",
                'name' => $permit,
            ]);
        }
    }
}
