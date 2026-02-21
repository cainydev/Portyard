<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Context
            $table->string('request_id')->nullable()->index();
            $table->foreignUuid('space_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();

            // Polymorphic Subject (Repository, User, Tag, Manifest)
            $table->nullableUuidMorphs('subject');

            // The Action
            $table->string('action');
            $table->string('description');

            // Metadata (IP, User Agent, JSON Diff, or Registry Report)
            $table->jsonb('metadata')->nullable();

            $table->timestamps();

            // Performance Indexes
            $table->index(['space_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
