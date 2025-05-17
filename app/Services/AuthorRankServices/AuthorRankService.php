<?php

namespace App\Services\AuthorRankServices;

use App\Models\Author;
use App\Models\Weight;
use App\Models\AuthorRank;
use Illuminate\Support\Collection;

class AuthorRankService
{
    public function calculate(Author $author): void
    {
        $weights = cache()->remember('weight_all', 86400, fn() => Weight::pluck('value', 'key'));

        $publications = $author->publications()->with('publisher')->get();

        $totalPublications = $publications->count();
        $totalCitations    = $publications->sum('citation_count');
        $hIndex            = $this->calculateHIndex($publications);

        $publisherWeight = $publications->avg(fn($pub) => $pub->publisher?->h_index ?? 0) ?? 0;

        $rankScore =
            ($weights['publications'] ?? 1) * (0.2 * $totalPublications) +
            ($weights['citations'] ?? 1)    * (0.5 * sqrt($totalCitations)) +
            ($weights['h_index'] ?? 1)      * (0.3 * $hIndex) +
            ($weights['publisher'] ?? 1)    * $publisherWeight;
        $rankScore = round($rankScore, 3);

        AuthorRank::updateOrCreate(
            ['author_id' => $author->id],
            [
                'total_publications' => $totalPublications,
                'total_citations'    => $totalCitations,
                'h_index'            => $hIndex,
                'rank_score'         => $rankScore,
            ]
        );
    }

    private function calculateHIndex(Collection $publications): int
    {
        $citations = $publications->pluck('citation_count')->sortDesc()->values();
        foreach ($citations as $i => $count) {
            if ($count < $i + 1) return $i;
        }
        return $citations->count();
    }
}
