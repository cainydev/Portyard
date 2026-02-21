<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            if (! Schema::hasColumn('repositories', 'space_id')) {
                $table->foreignUuid('space_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('spaces')
                    ->cascadeOnDelete();
            }
        });

        DB::transaction(function () {
            DB::table('users')->orderBy('id')->cursor()->each(function ($user) {
                $existingLink = DB::table('space_user')
                    ->where('user_id', $user->id)
                    ->first();

                if ($existingLink) {
                    $spaceId = $existingLink->space_id;
                } else {
                    $spaceId = (string) Str::uuid();
                    $now = now();

                    $namespace = Str::slug($user->name);

                    DB::table('spaces')->insert([
                        'id' => $spaceId,
                        'name' => $user->name."'s Space",
                        'namespace' => $namespace,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('space_user')->insert([
                        'id' => Str::uuid7(),
                        'user_id' => $user->id,
                        'space_id' => $spaceId,
                        'role' => Roles::Owner->value,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                DB::table('repositories')
                    ->where('user_id', $user->id)
                    ->whereNull('space_id')
                    ->update(['space_id' => $spaceId]);
            });
        });

        $existingIndexes = collect(DB::select('SHOW INDEXES FROM repositories'))
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->all();

        Schema::table('repositories', function (Blueprint $table) use ($existingIndexes) {
            if (DB::table('repositories')->exists()) {
                $table->uuid('space_id')->nullable(false)->change();
            }

            if (in_array('unique_repository_path', $existingIndexes)) {
                $table->dropUnique('unique_repository_path');
            }
            if (in_array('index_repository_namespace_name', $existingIndexes)) {
                $table->dropIndex('index_repository_namespace_name');
            }
            if (in_array('index_repository_path', $existingIndexes)) {
                $table->dropIndex('index_repository_path');
            }

            if (Schema::hasColumn('repositories', 'path')) {
                $table->dropColumn('path');
            }
        });

        Schema::table('repositories', function (Blueprint $table) {
            if (Schema::hasColumn('repositories', 'namespace')) {
                $table->dropColumn('namespace');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            if (! Schema::hasColumn('repositories', 'namespace')) {
                $table->string('namespace')->nullable();
            }
        });

        Schema::table('repositories', function (Blueprint $table) {
            if (! Schema::hasColumn('repositories', 'path')) {
                $table->string('path')->virtualAs("CONCAT(`namespace`, '/', `name`)");
            }

            try {
                $table->unique(['namespace', 'name'], 'unique_repository_path');
                $table->index(['namespace', 'name'], 'index_repository_namespace_name');
                $table->index(['path'], 'index_repository_path');
            } catch (\Exception $e) { /* Ignore */
            }
        });

        DB::transaction(function () {
            $repos = DB::table('repositories')
                ->join('spaces', 'repositories.space_id', '=', 'spaces.id')
                ->select('repositories.id', 'spaces.namespace as space_slug')
                ->get();

            foreach ($repos as $repo) {
                DB::table('repositories')
                    ->where('id', $repo->id)
                    ->update(['namespace' => $repo->space_slug]);
            }
        });

        Schema::table('repositories', function (Blueprint $table) {
            $table->string('namespace')->nullable(false)->change();
            $table->dropForeign(['space_id']);
            $table->dropColumn('space_id');
        });
    }
};
