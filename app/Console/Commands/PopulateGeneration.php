<?php

namespace App\Console\Commands;

use App\Models\KtbMember;
use Illuminate\Console\Command;

class PopulateGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ktb:populate-generation
                            {--force : Force update all generations}
                            {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate or recalculate generation field for all KTB members based on mentor relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting generation population...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Step 1: Find all root members (no mentors)
        $this->info('Step 1: Finding root members (Generation 1)...');
        $rootMembers = KtbMember::whereDoesntHave('mentors')->get();

        $this->info("Found {$rootMembers->count()} root members");
        $this->newLine();

        $updated = 0;
        $bar = $this->output->createProgressBar($rootMembers->count());
        $bar->start();

        foreach ($rootMembers as $root) {
            if ($dryRun) {
                $this->line("  - {$root->name}: would set generation = 1");
            } else {
                if ($force || $root->generation === null) {
                    $root->update(['generation' => 1]);
                    $updated++;
                }
            }

            // Update descendants recursively
            $updated += $this->updateDescendants($root, 1, $dryRun, $force, 1);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Step 2: Find orphaned members (have mentors but generation is null)
        if (!$dryRun) {
            $this->info('Step 2: Checking for orphaned members...');
            $orphaned = KtbMember::whereNull('generation')->get();

            if ($orphaned->count() > 0) {
                $this->warn("Found {$orphaned->count()} orphaned members. Calculating their generation...");

                foreach ($orphaned as $member) {
                    $member->calculateAndUpdateGeneration();
                    $updated++;
                }
            }
            $this->newLine();
        }

        // Summary
        $this->newLine();
        if ($dryRun) {
            $this->info("✅ Dry run completed. {$updated} members would be updated.");
        } else {
            $this->info("✅ Generation population completed!");
            $this->info("   Updated: {$updated} members");
        }

        // Show statistics
        $this->newLine();
        $this->showStatistics();

        return Command::SUCCESS;
    }

    /**
     * Update descendants recursively
     */
    private function updateDescendants(KtbMember $member, int $generation, bool $dryRun, bool $force, int $level = 1): int
    {
        $updated = 0;
        $mentees = $member->mentees()->get();

        $indent = str_repeat('  ', $level);

        foreach ($mentees as $mentee) {
            $newGeneration = $generation + 1;

            if ($dryRun) {
                $current = $mentee->generation ?? 'null';
                $this->line("{$indent}- {$mentee->name}: {$current} → {$newGeneration}");
            } else {
                if ($force || $mentee->generation === null || $mentee->generation !== $newGeneration) {
                    $mentee->update(['generation' => $newGeneration]);
                    $updated++;
                }
            }

            // Recursive update
            $updated += $this->updateDescendants($mentee, $newGeneration, $dryRun, $force, $level + 1);
        }

        return $updated;
    }

    /**
     * Show generation statistics
     */
    private function showStatistics()
    {
        $this->info('📊 Generation Statistics:');
        $this->table(
            ['Generation', 'Count', 'Percentage'],
            KtbMember::selectRaw('generation, COUNT(*) as count')
                ->whereNotNull('generation')
                ->groupBy('generation')
                ->orderBy('generation')
                ->get()
                ->map(function ($stat) {
                    $total = KtbMember::whereNotNull('generation')->count();
                    $percentage = $total > 0 ? round(($stat->count / $total) * 100, 1) : 0;
                    return [
                        "Gen {$stat->generation}",
                        $stat->count,
                        "{$percentage}%"
                    ];
                })
        );

        $nullCount = KtbMember::whereNull('generation')->count();
        if ($nullCount > 0) {
            $this->warn("⚠️  {$nullCount} members still have NULL generation");
        }
    }
}
