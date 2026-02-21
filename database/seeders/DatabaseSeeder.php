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
            'email' => 'wajo432@gmail.com',
            'password' => 'password',
        ]);

        $collaborators = User::factory()->count(10)->create();

        $personalSpace = $myself->spaces()->first();
        $personalSpace->activities()->oldest()->first()->delete();

        Repository::factory()
            ->count(20)
            ->for($personalSpace)
            ->has(
                Tag::factory()
                    ->count(rand(5, 15))
                    ->recycle($myself)
            )
            ->has(Webhook::factory()->count(rand(0, 2)))
            ->create();

        $teamSpace = $myself->spaces()
            ->withPivotValue(['role' => Roles::Owner->value])
            ->create([
                'name' => 'Team Space',
                'namespace' => 'team',
            ]);

        $teamSpace->developers()->attach($collaborators->pluck('id'));
        $teamSpace->activities()->oldest()->first()->delete();

        Repository::factory()
            ->count(20)
            ->for($teamSpace)
            ->has(
                Tag::factory()
                    ->count(rand(5, 15))
                    ->recycle($collaborators->merge([$myself]))
            )
            ->has(Webhook::factory()->count(rand(1, 3)))
            ->create();

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
                    ->count(3)
                    ->recycle($collaborators->merge([$myself]))
            )
            ->has(Webhook::factory()->count(rand(1, 3)))
            ->create();

        $this->call(ActivitySeeder::class);
    }
}
