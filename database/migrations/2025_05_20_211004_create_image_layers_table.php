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
        Schema::create('image_layers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('manifest_id')
                ->constrained()
                ->references('id')
                ->on('manifests')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('digest');
            $table->integer('sort_order');
            $table->unsignedBigInteger('size_bytes');
            $table->string('media_type');

            $table->text('command')->nullable();

            $table->unique(['manifest_id', 'sort_order']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_layers');
    }
};
