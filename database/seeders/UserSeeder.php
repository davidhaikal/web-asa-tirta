<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin','marketing','qc','produksi','gudang','keuangan','kasir','driver'];

        foreach ($roles as $role) {
            User::create([
                'name' => ucfirst($role) . ' User',
                'email' => $role . '@example.com',
                'password' => Hash::make('password'),
                'role' => $role,
            ]);
        }
    }
}
