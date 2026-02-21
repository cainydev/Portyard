<?php

namespace Database\Factories;

use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Repository>
 */
class RepositoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ver = $this->faker->semver();
        $repo = $this->faker->domainWord();
        $ucRepo = ucfirst($repo);
        $overview = collect([
            '# Quick reference',

            "- **Maintained by**: [The {$this->faker->company()} Community]({$this->faker->url()})",
            "- **Where to get help**: [Stack {$this->faker->word()}]({$this->faker->url()})",

            '# Supported tags',

            collect([
                "[`{$ver}.0-{$this->faker->word()}`, `{$ver}`, `latest`]({$this->faker->url()})",
                "[`{$ver}-slim`, `slim`]({$this->faker->url()})",
            ])->map(fn ($tag) => "- $tag")->implode("\n\n"),

            "# What is $ucRepo?",

            '> '.$this->faker->sentence(),

            "**$ucRepo** combines tiny versions of {$this->faker->words(asText: true)} into a single executable. ".$this->faker->sentence(),

            "![logo](https://picsum.photos/seed/$repo/400/200)",

            '# How to use this image',

            "```console\n$ docker run -it --rm $repo\n```",

            "This will drop you into an `sh` shell to allow you to do what you want inside a **$ucRepo** system.",

        ])->filter()->implode("\n\n");

        return [
            'name' => $repo,
            'description' => $this->faker->sentence(),
            'overview' => $overview,
            'public' => $this->faker->boolean(),
        ];
    }
}
