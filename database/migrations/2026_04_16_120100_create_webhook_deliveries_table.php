<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('webhook_id')
                ->constrained()
                ->references('id')
                ->on('webhooks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('event');
            $table->string('status')->default('pending');
            $table->unsignedSmallInteger('attempt')->default(1);

            $table->jsonb('request_headers')->nullable();
            $table->longText('request_body')->nullable();

            $table->unsignedSmallInteger('response_status')->nullable();
            $table->jsonb('response_headers')->nullable();
            $table->text('response_body')->nullable();

            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error')->nullable();

            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['webhook_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
