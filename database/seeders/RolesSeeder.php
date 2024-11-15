<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(
            [
                'name' => 'superadmin',
                'guard_name' => 'api'
            ],
        );

        Role::create(
            [
                'name' => 'manager',
                'guard_name' => 'api'
            ],
        );

        Role::create(
            [
                'name' => 'employee',
                'guard_name' => 'api'
            ],
        );
    }
}
