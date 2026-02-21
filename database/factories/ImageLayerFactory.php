<?php

namespace Database\Factories;

use App\Models\ImageLayer;
use App\Models\Manifest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImageLayer>
 */
class ImageLayerFactory extends Factory
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
            'sort_order' => $this->faker->numberBetween(0, 100),
            'size_bytes' => $this->faker->numberBetween(1024, 10485760),
            'media_type' => 'application/vnd.docker.image.rootfs.diff.tar.gzip',
        ];
    }
}
