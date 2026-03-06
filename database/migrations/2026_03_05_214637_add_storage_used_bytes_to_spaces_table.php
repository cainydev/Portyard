<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->unsignedBigInteger('storage_used_bytes')->default(0)->after('description');
        });

        // Backfill existing data
        DB::statement('
            UPDATE spaces
            SET storage_used_bytes = COALESCE((
                SELECT SUM(il.size_bytes)
                FROM repositories r
                JOIN tags t ON t.repository_id = r.id
                JOIN manifests m ON m.id = t.manifest_id
                JOIN image_layers il ON il.manifest_id = m.id
                WHERE r.space_id = spaces.id
            ), 0)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn('storage_used_bytes');
        });
    }
};
