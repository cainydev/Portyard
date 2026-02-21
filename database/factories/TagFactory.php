<?php

namespace Database\Factories;

use App\Models\Manifest;
use App\Models\Repository;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'repository_id' => Repository::factory(),
            'user_id' => User::factory(),
            'name' => $this->faker->unique()->semver(), // Generates v1.a.z style or similar
            'last_pushed' => now(),
            'manifest_id' => fn () => (rand(1, 100) > 80)
                ? Manifest::factory()->image()
                : Manifest::factory()->list(),
        ];
    }

    /**
     * Force the tag to point to a Manifest List (Multi-arch).
     */
    public function multiArch(): static
    {
        return $this->state(fn (array $attributes) => [
            'manifest_id' => Manifest::factory()->list(),
        ]);
    }
}
