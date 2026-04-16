<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('repositories')
            ->select('space_id', 'name')
            ->groupBy('space_id', 'name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $rows = DB::table('repositories')
                ->where('space_id', $duplicate->space_id)
                ->where('name', $duplicate->name)
                ->orderBy('created_at')
                ->get();

            foreach ($rows->skip(1) as $index => $row) {
                DB::table('repositories')
                    ->where('id', $row->id)
                    ->update(['name' => $row->name.'-dup-'.($index + 1)]);
            }
        }

        Schema::table('repositories', function (Blueprint $table) {
            $table->unique(['space_id', 'name'], 'repositories_space_id_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->dropUnique('repositories_space_id_name_unique');
        });
    }
};
