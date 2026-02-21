<?php

namespace Database\Factories;

use App\Models\ImageConfig;
use App\Models\Manifest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImageConfig>
 */
class ImageConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'manifest_id' => Manifest::factory(),
            'digest' => 'sha256:'.$this->faker->sha256(),
            'architecture' => 'amd64',
            'os' => 'linux',
            'variant' => null,
        ];
    }
}
