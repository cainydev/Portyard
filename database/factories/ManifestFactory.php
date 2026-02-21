<?php

namespace Database\Factories;

use App\Models\ImageConfig;
use App\Models\ImageLayer;
use App\Models\Manifest;
use App\Models\ManifestListEntry;
use Cainy\Dockhand\Enums\MediaType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends Factory<Manifest>
 */
class ManifestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'digest' => 'sha256:'.$this->faker->sha256(),
            'size_bytes' => $this->faker->numberBetween(1000, 50000000),
            'media_type' => MediaType::IMAGE_MANIFEST_V2->value,
            'content' => [],
        ];
    }

    /**
     * A multi-arch Manifest List (indexes other images).
     */
    public function list(): static
    {
        return $this->state(fn (array $attributes) => [
            'media_type' => MediaType::IMAGE_MANIFEST_V2_LIST,
        ])->afterCreating(function (Manifest $manifest) {
            // Create a child manifest for AMD64
            $amd64 = Manifest::factory()->image()->create();
            ManifestListEntry::factory()
                ->for($manifest, 'listManifest')
                ->for($amd64, 'childManifest')
                ->amd64()
                ->create();

            // Create a child manifest for ARM64
            $arm64 = Manifest::factory()->image()->create();
            ManifestListEntry::factory()
                ->for($manifest, 'listManifest')
                ->for($arm64, 'childManifest')
                ->arm64()
                ->create();
        });
    }

    /**
     * A standard single-arch Docker image (has config + layers).
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'media_type' => MediaType::IMAGE_MANIFEST_V2,
        ])->afterCreating(function (Manifest $manifest) {
            ImageConfig::factory()
                ->for($manifest)
                ->create();

            ImageLayer::factory()
                ->count(rand(3, 5))
                ->for($manifest)
                ->sequence(fn (Sequence $sequence) => [
                    'sort_order' => $sequence->index,
                ])
                ->create();
        });
    }
}
