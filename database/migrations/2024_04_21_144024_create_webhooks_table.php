<?php

use App\Enums\WebhookTrigger;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('repository_id')
                ->constrained()
                ->references('id')
                ->on('repositories')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('trigger', WebhookTrigger::values());
            $table->string('name');
            $table->string('url');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
