<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions for users
        $permissions = [
            'Tambah Pengguna',
            'Lihat Pengguna',
            'Ubah Pengguna',
            'Hapus Pengguna',
            'Tambah Data',
            'Lihat Data',
            'Ubah Data',
            'Hapus Data',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'Administrator' => ['Tambah Pengguna', 'Lihat Pengguna', 'Ubah Pengguna', 'Hapus Pengguna', 'Tambah Data', 'Lihat Data', 'Ubah Data', 'Hapus Data'],
            'User' => ['Tambah Data', 'Lihat Data', 'Ubah Data', 'Hapus Data'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->givePermissionTo($rolePermissions);
        }

        // Create users and assign roles
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@starter.com',
                'password' => Hash::make('12345678'),
                'role' => ['Administrator', 'User'],
            ],
            [
                'name' => 'User',
                'email' => 'user@starter.com',
                'password' => Hash::make('12345678'),
                'role' => 'User',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            $user->assignRole($userData['role']);
        }

        $this->command->info('Roles, Permissions, dan Users Telah berhasil dibuat!');
    }
}
