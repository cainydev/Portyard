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
        Schema::create('image_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('manifest_id')
                ->unique()
                ->constrained()
                ->references('id')
                ->on('manifests')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('digest');
            $table->string('architecture');
            $table->string('os');
            $table->string('variant')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_configs');
    }
};
