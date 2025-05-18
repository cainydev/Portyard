<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repository_user', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->references('id')
                ->on('users');

            $table->foreignUuid('repository_id')
                ->constrained('repositories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->references('id')
                ->on('repositories');

            $table->enum('role', Roles::values());

            $table->unique(['user_id', 'repository_id'], 'user_repository_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repository_user');
    }
};
