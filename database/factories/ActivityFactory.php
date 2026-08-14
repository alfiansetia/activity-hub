<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'date'         => fake('id_ID')->dateTimeBetween('-30 days', 'now'),
            'title'        => fake('id_ID')->sentence(3),
            'descriptions' => fake('id_ID')->paragraph(),
            'rules'        => fake('id_ID')->optional(0.6)->sentence(),
            'tools'        => fake('id_ID')->optional(0.5)->words(3, true),
            'user_id'      => User::factory(),
            'company_id'   => Company::factory(),
            'status'       => 'pending',
            'accept_by'    => null,
            'reject_by'    => null,
            'reject_reason' => null,
            'reject_at'    => null,
            'accept_at'    => null,
            're_submit_at' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn() => [
            'status'    => 'accept',
            'accept_by' => User::factory(),
            'accept_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn() => [
            'status'        => 'reject',
            'reject_by'     => User::factory(),
            'reject_at'     => now(),
            'reject_reason' => fake('id_ID')->sentence(),
        ]);
    }
}
