<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'add-customer',
            'edit-customer',
            'delete-customer',
            'add-permission',
            'edit-permission',
            'delete-permission',
            'add-role',
            'edit-role',
            'delete-role',
            'customer-list',
            'role-list',
            'permissions-list',
            'user-list',
            'add-user',
            'edit-user',
            'delete-user',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
        ]);

        $role = Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Sub-admin']);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
