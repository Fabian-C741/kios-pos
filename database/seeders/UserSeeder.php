<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@kiosco.com',
            'password' => Hash::make('password'),
            'telefono' => '5512345678',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        $cajero1 = User::create([
            'name' => 'Cajero Principal',
            'username' => 'cajero',
            'email' => 'cajero@kiosco.com',
            'password' => Hash::make('password'),
            'telefono' => '5587654321',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        $cajero2 = User::create([
            'name' => 'Segundo Turno',
            'username' => 'cajero2',
            'email' => 'cajero2@kiosco.com',
            'password' => Hash::make('password'),
            'telefono' => '5598765432',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $cajeroRole = Role::where('name', 'cajero')->first();

        $admin->roles()->attach($adminRole);
        $cajero1->roles()->attach($cajeroRole);
        $cajero2->roles()->attach($cajeroRole);
    }
}
