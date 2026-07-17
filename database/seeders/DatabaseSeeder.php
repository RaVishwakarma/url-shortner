<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);

        $company = Company::firstOrCreate([
            'name' => 'Example Company',
        ]);

        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_id' => $company->id,
        ]);

        User::updateOrCreate(['email' => 'member@example.com'], [
            'name' => 'Member User',
            'password' => Hash::make('password'),
            'role' => 'member',
            'company_id' => $company->id,
        ]);
    }
}
