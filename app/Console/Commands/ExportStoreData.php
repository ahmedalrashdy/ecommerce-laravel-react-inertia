<?php

namespace App\Console\Commands;

use App\Services\Seed\StoreDataExporter;
use Illuminate\Console\Command;
use RuntimeException;

class ExportStoreData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-data:export {--file= : Relative path to JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export categories, brands, products, and reviews to JSON';

    /**
     * Execute the console command.
     */
    public function handle(StoreDataExporter $exporter): int
    {
        $relativePath = $this->option('file') ?: 'database/seed-data/store-export.json';
        $path = base_path($relativePath);

        try {
            $payload = $exporter->export();
            $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if ($json === false) {
                throw new RuntimeException('Failed to encode JSON.');
            }

            file_put_contents($path, $json);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Store data exported successfully.');

        return self::SUCCESS;
    }
}
