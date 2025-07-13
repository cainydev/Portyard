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
        Schema::create('manifest_list_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('list_manifest_id')
                ->constrained()
                ->references('id')
                ->on('manifests')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUuid('child_manifest_id')
                ->constrained()
                ->references('id')
                ->on('manifests')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('platform_os')->nullable();
            $table->string('platform_architecture')->nullable();
            $table->string('platform_variant')->nullable();
            $table->string('platform_os_version')->nullable();
            $table->json('platform_os_features')->nullable();
            $table->json('platform_features')->nullable();

            $table->unique(['list_manifest_id', 'child_manifest_id'], 'list_child_manifest_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifest_list_entries');
    }
};
