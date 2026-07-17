<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::insert(
            'INSERT INTO users (name, email, password, role, created_at, updated_at)
             SELECT ?, ?, ?, ?, ?, ?
             WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = ?)',
            [
                'Super Admin',
                'superadmin@example.com',
                Hash::make('test123'),
                'super_admin',
                $now,
                $now,
                'superadmin@example.com',
            ]
        );
    }
}
