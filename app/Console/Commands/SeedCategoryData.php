<?php

namespace App\Console\Commands;

use App\Services\Seed\SeedDataImporter;
use Illuminate\Console\Command;
use RuntimeException;

class SeedCategoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:category-data {--file= : Relative path to JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed categories, brands, products, variants, and images from JSON seed data';

    /**
     * Execute the console command.
     */
    public function handle(SeedDataImporter $importer): int
    {
        $relativePath = $this->option('file') ?: 'database/seed-data/categories/electronics.json';
        $path = base_path($relativePath);

        try {
            $importer->importFromPath(
                $path,
                function (string $message, array $context): void {
                    $this->line($this->formatProgressMessage($message, $context));
                },
                function (string $message, array $context): void {
                    $this->error($this->formatProgressMessage($message, $context));
                }
            );
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Seed data imported successfully.');

        return self::SUCCESS;
    }

    private function formatProgressMessage(string $message, array $context): string
    {
        if ($context === []) {
            return $message;
        }

        $pairs = collect($context)
            ->map(fn ($value, $key) => $key.': '.$value)
            ->implode(' | ');

        return $message.' - '.$pairs;
    }
}
