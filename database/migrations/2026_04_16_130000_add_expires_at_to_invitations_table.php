<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('declined_at');
        });

        DB::table('invitations')
            ->whereNull('expires_at')
            ->update(['expires_at' => now()->addDays(14)]);
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
