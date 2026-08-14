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
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'              => fake('id_ID')->name(),
            'email'             => fake('id_ID')->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'role'              => 'user',
            'company_id'        => null,
            'company_status'    => 'pending',
            'remember_token'    => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn() => [
            'role'           => 'admin',
            'company_status' => 'accept',
        ]);
    }

    public function dosen(): static
    {
        return $this->state(fn() => [
            'role'           => 'dosen',
            'company_status' => 'accept',
        ]);
    }

    public function withCompany(?Company $company = null): static
    {
        return $this->state(fn() => [
            'company_id'     => $company?->id ?? Company::factory(),
            'company_status' => 'accept',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn() => ['company_status' => 'accept']);
    }
}
