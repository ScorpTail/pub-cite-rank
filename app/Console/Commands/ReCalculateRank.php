<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Services\AuthorRankServices\AuthorRankService;
use Illuminate\Console\Command;

class ReCalculateRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:re-calculate-rank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'recalculation of rank for all authors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Author::with(['publications'])->get() as $author) {
            $this->info("Recalculating rank for author: {$author->full_name}");
            app(AuthorRankService::class)->calculate($author);
        }

        $this->info('Rank recalculation completed.');
    }
}
