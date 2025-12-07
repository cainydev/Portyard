<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $myself = User::factory()->create([
            'name' => 'John',
            'email' => 'wajo432@gmail.com',
            'password' => 'password',
        ]);

        $myself->spaces()->withPivotValue('role', Roles::Owner->value)->create([
            'name' => 'Work Space',
            'namespace' => 'workspace',
        ]);

        $myself->spaces()->withPivotValue('role', Roles::Owner->value)->create([
            'name' => 'Shared Space',
            'namespace' => 'shared',
        ]);

        User::factory()->count(10)->create();
    }
}
