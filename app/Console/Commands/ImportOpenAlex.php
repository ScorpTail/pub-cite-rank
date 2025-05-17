<?php

namespace App\Console\Commands;

use App\Services\ImportServices\OpenAlex\OpenAlexService;
use Illuminate\Console\Command;

class ImportOpenAlex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-openalex {type=publishers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import publication from external source';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // dd(hash('sha256', 123));
        $start = microtime(true);
        $this->info('Syncing publications...');

        $type = $this->argument('type', 'publishers');
        app(OpenAlexService::class)->import($type);
        $end = microtime(true);
        $executionTime = $end - $start;
        $this->info('Execution time: ' . $executionTime . ' seconds');
        $this->info('Publications synced successfully.');
    }
}
