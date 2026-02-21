<?php

namespace Database\Factories;

use App\Models\Manifest;
use App\Models\ManifestListEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ManifestListEntry>
 */
class ManifestListEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'list_manifest_id' => Manifest::factory(),
            'child_manifest_id' => Manifest::factory(),
            'platform_os' => 'linux',
            'platform_architecture' => 'amd64',
            'platform_variant' => null,
            'platform_features' => [],
            'platform_os_features' => [],
        ];
    }

    public function amd64(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_architecture' => 'amd64',
            'platform_os' => 'linux',
        ]);
    }

    public function arm64(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_architecture' => 'arm64',
            'platform_os' => 'linux',
        ]);
    }
}
