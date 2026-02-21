<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('repository_user');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('repository_user', function ($table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained()
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUuid('repository_id')
                ->constrained()
                ->references('id')
                ->on('repositories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->enum('role', Roles::values());

            $table->unique(['user_id', 'repository_id'], 'user_repository_unique');

            $table->timestamps();
        });
    }
};
