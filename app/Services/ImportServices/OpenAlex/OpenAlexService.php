<?php

namespace App\Services\ImportServices\OpenAlex;

use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\AuthorRank;
use App\Models\Publication;
use Illuminate\Support\Str;
use App\Exceptions\ImportException;
use Illuminate\Support\Facades\Http;

class OpenAlexService
{
    public function import(string $type = 'publishers')
    {
        return match ($type) {
            'publishers' => $this->importPublishers(),
            default => throw new ImportException(__('front.import.openalex.:type_not_supported', ['type' => $type])),
        };
    }

    private function request(string $type, array $params = [])
    {
        return Http::get('https://api.openalex.org/' . $type, [
            'page' => $params['page'] ?? 1,
            'per_page' => $params['per_page'] ?? 200,
        ]);
    }

    private function importPublishers()
    {
        $response = $this->request('publishers', [
            'page' => 1,
            'per_page' => 50,
        ]);

        if ($response->failed()) {
            throw new ImportException('Failed to import publishers.');
        }

        $publishers = $response->json('results');

        foreach ($publishers as $publisher) {
            $publisherId = Str::after($publisher['id'], 'https://openalex.org/P');

            $localPublisher = Publisher::updateOrCreate(
                ['openalex_id' => $publisherId],
                [
                    'name' => $publisher['display_name'] ?? null,
                    'country' => $publisher['country_codes'][0] ?? null,
                    'website' => $publisher['homepage_url'] ?? null,
                    'h_index' => $publisher['summary_stats']['h_index'] ?? null,
                ]
            );

            $this->importTopWorksForPublisher($publisherId, $localPublisher->id);
        }
    }

    private function importTopWorksForPublisher(string $openalexPublisherId, int $localPublisherId): void
    {
        $response = $this->request('works', [
            'filter' => 'host_venue.publisher.id:P' . $openalexPublisherId,
            'sort' => 'cited_by_count:desc',
            'per_page' => 20,
            'page' => 1,
        ]);

        if ($response->failed()) {
            return;
        }

        $works = $response->json('results');

        foreach ($works as $work) {
            $workId = Str::after($work['id'], 'https://openalex.org/W');

            $publication = Publication::updateOrCreate(
                ['openalex_id' => $workId],
                [
                    'title' => $work['title'] ?? null,
                    'published_at' => $work['publication_date'] ?? null,
                    'publisher_id' => $localPublisherId,
                    'citation_count' => $work['cited_by_count'] ?? 0,
                    'doi' => $work['doi'] ?? null,
                ]
            );

            // Автори
            if (!empty($work['authorships'])) {
                $position = 1;
                foreach ($work['authorships'] as $authorship) {
                    $authorData = $authorship['author'] ?? [];
                    if (!isset($authorData['id'])) {
                        continue;
                    }

                    $authorId = Str::after($authorData['id'], 'https://openalex.org/A');
                    $fullName = $authorData['display_name'] ?? null;
                    $orcid = $authorData['orcid'] ?? null;

                    $affiliation = null;
                    if (!empty($authorship['institutions'])) {
                        $affiliation = $authorship['institutions'][0]['display_name'] ?? null;
                    }

                    $nameParts = explode(' ', $fullName);
                    $firstName = $nameParts[0] ?? null;
                    $lastName = count($nameParts) > 1 ? array_pop($nameParts) : null;
                    $middleName = implode(' ', array_slice($nameParts, 1));

                    $author = Author::updateOrCreate(
                        ['openalex_id' => $authorId],
                        [
                            'first_name' => $firstName,
                            'middle_name' => $middleName,
                            'last_name' => $lastName,
                            'orcid' => $orcid,
                            'affiliation' => $affiliation,
                        ]
                    );

                    $stats = $this->request('authors/A' . $authorId)->json();

                    if ($stats) {
                        $publications = $stats['works_count'] ?? 0;
                        $citations = $stats['cited_by_count'] ?? 0;
                        $hIndex = $stats['summary_stats']['h_index'] ?? 0;

                        $rankScore = (0.2 * $publications) + (0.5 * sqrt($citations)) + (0.3 * $hIndex);

                        AuthorRank::updateOrCreate(
                            ['author_id' => $author->id],
                            [
                                'total_publications' => $publications,
                                'total_citations' => $citations,
                                'h_index' => $hIndex,
                                'rank_score' => $rankScore,
                            ]
                        );
                    }

                    $publication->authors()->syncWithoutDetaching([
                        $author->id => ['author_position' => $position]
                    ]);
                    $position++;
                }
            }

            // Категорії
            if (!empty($work['concepts'])) {
                foreach ($work['concepts'] as $concept) {
                    $conceptId = Str::after($concept['id'], 'https://openalex.org/C');
                    // $fullConcept = $this->request('concepts/C' . $conceptId);
                    // $name = $fullConcept->json('international.display_name.' . app()->getLocale(), $concept['display_name'] ?? null);
                    $name = $concept['display_name'] ?? null;
                    $level = $concept['level'] ?? null;

                    $parentId = null;
                    if (!empty($concept['ancestors'])) {
                        $parentOpenalexId = Str::after($concept['ancestors'][0]['id'], 'https://openalex.org/C');
                        $parent = Category::where('openalex_concept_id', $parentOpenalexId)->first();
                        $parentId = $parent?->id;
                    }

                    $category = Category::updateOrCreate(
                        ['openalex_concept_id' => $conceptId],
                        [
                            'name' => $name,
                            'level' => $level,
                            'parent_id' => $parentId,
                        ]
                    );

                    $publication->categories()->syncWithoutDetaching([$category->id]);
                }
            }
        }
    }
}
