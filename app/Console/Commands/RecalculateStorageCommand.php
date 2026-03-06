<?php

namespace App\Console\Commands;

use App\Models\Space;
use Illuminate\Console\Command;

class RecalculateStorageCommand extends Command
{
    protected $signature = 'storage:recalculate {--space= : Recalculate only the given space namespace}';

    protected $description = 'Recalculate storage_used_bytes for all spaces (or a specific one)';

    public function handle(): int
    {
        $query = Space::query();

        if ($namespace = $this->option('space')) {
            $query->where('namespace', $namespace);
        }

        $spaces = $query->get();

        if ($spaces->isEmpty()) {
            $this->error('No spaces found.');

            return self::FAILURE;
        }

        $this->info("Recalculating storage for {$spaces->count()} space(s)...");

        foreach ($spaces as $space) {
            $before = $space->storage_used_bytes;
            $space->recalculateStorage();
            $after = $space->fresh()->storage_used_bytes;
            $drift = $after - $before;

            $driftLabel = $drift === 0 ? 'no drift' : ($drift > 0 ? "+{$drift} bytes" : "{$drift} bytes");

            $this->line("  {$space->namespace}: {$before} → {$after} ({$driftLabel})");
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
