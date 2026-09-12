<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
    User::create([
    'name' => 'Admin Easy System',
    'email' => 'admin@easysystem.test',
    'password' => Hash::make('admin12345'),
    'role' => 'admin',
]);

User::create([
    'name' => 'Pegawai Testing',
    'email' => 'pegawai@easysystem.test',
    'password' => Hash::make('pegawai12345'),
    'role' => 'pegawai',
]);

User::create([
    'name' => 'PPNPN Testing',
    'email' => 'ppnpn@easysystem.test',
    'password' => Hash::make('ppnpn12345'),
    'role' => 'ppnpn',
]);
    }
}