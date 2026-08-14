<?php

namespace Database\Factories;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'caption'   => fake()->sentence(2),
            'image_url' => 'uploads/' . fake()->uuid() . '.jpg',
        ];
    }
}
