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
        Schema::create('repositories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('namespace');
            $table->string('name');
            $table->string('path')->virtualAs("CONCAT(`namespace`, '/', `name`)");
            $table->text('description')->nullable();
            $table->longText('overview')->nullable();
            $table->boolean('public')->default(false);

            $table->unique(['namespace', 'name'], 'unique_repository_path');
            $table->index(['namespace', 'name'], 'index_repository_namespace_name');
            $table->index(['path'], 'index_repository_path');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};
