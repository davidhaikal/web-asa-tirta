<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'admin',
            'qc',
            'produksi',
            'gudang',
            'keuangan',
            'kasir',
            'manajemen',
            'driver'
        ];

        foreach ($roles as $role) {

            User::updateOrCreate(

                [
                    'email' => $role . '@example.com'
                ],

                [
                    'name' => ucfirst($role) . ' User',
                    'password' => Hash::make('password'),
                    'role' => $role,
                ]

            );

        }
    }
}