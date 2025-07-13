<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('repository_id')
                ->constrained()
                ->references('id')
                ->on('repositories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUuid('manifest_id')
                ->constrained()
                ->references('id')
                ->on('manifests')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUuid('user_id')
                ->constrained()
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('name');
            $table->dateTime('last_pushed');

            $table->unique(['repository_id', 'name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
