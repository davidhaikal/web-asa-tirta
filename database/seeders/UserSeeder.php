<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 5 role sesuai use case skripsi: Admin Gudang, Kasir, Admin Keuangan, QC, Driver
        $roles = [
            'qc',
            'gudang',
            'keuangan',
            'kasir',
            'driver',
        ];

        foreach ($roles as $role) {

            User::updateOrCreate(

                [
                    'email' => $role.'@example.com',
                ],

                [
                    'name' => ucfirst($role).' User',
                    'password' => Hash::make('password'),
                    'role' => $role,
                ]

            );

        }
    }
}
