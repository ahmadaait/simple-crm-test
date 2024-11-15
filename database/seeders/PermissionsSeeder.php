<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //permission for roles
        Permission::create(['name' => 'roles.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'roles.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'roles.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'roles.delete', 'guard_name' => 'api']);

        //permission for permissions
        Permission::create(['name' => 'permissions.index', 'guard_name' => 'api']);

        //permission for users
        Permission::create(['name' => 'users.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'users.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'users.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'api']);

        //permission for companies
        Permission::create(['name' => 'companies.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'companies.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'companies.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'companies.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'companies.delete', 'guard_name' => 'api']);

        //assign permission to role
        $roleAdmin = Role::find(1);
        $permissionsAdmin = Permission::all();
        $roleAdmin->syncPermissions($permissionsAdmin);

        $roleManager = Role::find(2);
        $permissionsManager = Permission::where('name', 'LIKE', 'users.%')->get();
        $roleManager->syncPermissions($permissionsManager);

        $roleEmployee = Role::find(3);
        $permissionsEmployee = Permission::whereIN('name', ['users.index', 'users.show'])->get();
        $roleEmployee->syncPermissions($permissionsEmployee);

        //assign role with permission to user
        $user = User::find(1);
        $user->assignRole($roleAdmin->name);
    }
}
