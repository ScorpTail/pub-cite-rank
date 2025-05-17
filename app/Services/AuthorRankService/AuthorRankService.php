<?php

namespace App\Services\AuthorRankService;

use App\Models\Author;
use App\Models\Weight;
use App\Models\AuthorRank;

class AuthorRankService
{
    public function calculate(Author $author): void
    {
        $weights = cache()->remember('weight_all', fn() => Weight::pluck('value', 'key'));

        $totalPublications = $author->publications()->count();
        $totalCitations    = $author->publications()->sum('citation_count');
        $hIndex            = $this->calculateHIndex($author);
        $publisherWeight   = $author->publications()
            ->with('publisher')
            ->get()
            ->sum(fn($pub) => $pub->publisher?->h_index ?? 0);

        $rankScore =
            (($weights['publications'] ?? 1) * (0.2 * $totalPublications))
            + (($weights['citations'] ?? 1) * (0.5 * sqrt($totalCitations)))
            + (($weights['h_index'] ?? 1) * (0.3 * $hIndex))
            + (($weights['publisher'] ?? 1) * $publisherWeight);

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

    private function calculateHIndex(Author $author): int
    {
        $citations = $author->publications()->pluck('citation_count')->sortDesc()->values();
        foreach ($citations as $i => $count) {
            if ($count < $i + 1) return $i;
        }
        return $citations->count();
    }
}
