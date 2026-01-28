<?php

namespace App\Console\Commands;

use App\Services\Seed\StoreDataImporter;
use Illuminate\Console\Command;
use RuntimeException;

class ImportStoreData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-data:import {--file= : Relative path to JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import categories, brands, products, and reviews from JSON';

    /**
     * Execute the console command.
     */
    public function handle(StoreDataImporter $importer): int
    {
        $relativePath = $this->option('file') ?: 'database/seed-data/store-export.json';
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

        $this->info('Store data imported successfully.');

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
