<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'member',
            'company_id' => Company::factory(),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => 'super_admin',
            'company_id' => null,
        ]);
    }

    public function admin(?Company $company = null): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'company_id' => $company instanceof Company ? $company->id : Company::factory(),
        ]);
    }

    public function member(?Company $company = null): static
    {
        return $this->state(fn () => [
            'role' => 'member',
            'company_id' => $company instanceof Company ? $company->id : Company::factory(),
        ]);
    }
}
