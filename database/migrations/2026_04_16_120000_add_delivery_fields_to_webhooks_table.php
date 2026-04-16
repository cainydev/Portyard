<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webhooks', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->text('secret')->nullable()->after('url');
            $table->boolean('enabled')->default(true)->after('secret');
            $table->jsonb('events')->nullable()->after('enabled');
            $table->string('tag_filter', 100)->nullable()->after('events');
            $table->string('template')->default('generic')->after('tag_filter');
        });

        DB::table('webhooks')->orderBy('id')->each(function (object $webhook) {
            DB::table('webhooks')
                ->where('id', $webhook->id)
                ->update([
                    'events' => json_encode([$webhook->trigger]),
                ]);
        });

        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropColumn('trigger');
        });
    }

    public function down(): void
    {
        Schema::table('webhooks', function (Blueprint $table) {
            $table->string('trigger')->default('tag_pushed')->after('repository_id');
        });

        DB::table('webhooks')->orderBy('id')->each(function (object $webhook) {
            $events = json_decode($webhook->events, true) ?: ['tag_pushed'];
            DB::table('webhooks')
                ->where('id', $webhook->id)
                ->update(['trigger' => $events[0] ?? 'tag_pushed']);
        });

        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropColumn(['description', 'secret', 'enabled', 'events', 'tag_filter', 'template']);
        });
    }
};
