<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Repository;
use App\Models\Tag;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $myself = User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
        ]);

        $collaborators = User::factory()->count(10)->create();

        $personalSpace = $myself->spaces()->first();
        $personalSpace->activities()->oldest()->first()->delete();

        Repository::factory()
            ->count(20)
            ->for($personalSpace)
            ->create()
            ->each(function ($repo) use ($myself) {
                Tag::factory()->count(rand(5, 15))->recycle($myself)->for($repo)->create();
                Webhook::factory()->count(rand(0, 2))->for($repo)->create();
            });

        $teamSpace = $myself->spaces()
            ->withPivotValue(['role' => Roles::Owner->value])
            ->create([
                'name' => 'Team Space',
                'namespace' => 'team',
            ]);

        $teamSpace->developers()->attach($collaborators->pluck('id'));
        $teamSpace->activities()->oldest()->first()->delete();

        $allMembers = $collaborators->merge([$myself]);

        Repository::factory()
            ->count(20)
            ->for($teamSpace)
            ->create()
            ->each(function ($repo) use ($allMembers) {
                Tag::factory()->count(rand(5, 15))->recycle($allMembers)->for($repo)->create();
                Webhook::factory()->count(rand(1, 3))->for($repo)->create();
            });

        $smallSpace = $myself->spaces()
            ->withPivotValue(['role' => Roles::Owner->value])
            ->create([
                'name' => 'Smol Space',
                'namespace' => 'smol',
            ]);

        $smallSpace->developers()->attach($collaborators->pluck('id')->first());
        $smallSpace->activities()->oldest()->first()->delete();

        Repository::factory()
            ->count(1)
            ->for($smallSpace)
            ->has(
                Tag::factory()
                    ->count(rand(4, 8))
                    ->recycle($collaborators->merge([$myself]))
            )
            ->has(Webhook::factory()->count(rand(1, 3)))
            ->create();

        $this->call(ActivitySeeder::class);
    }
}
