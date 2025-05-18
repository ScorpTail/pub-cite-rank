<?php

namespace App\Services\ImportServices\OpenAlex;

use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Publication;
use Illuminate\Support\Str;
use App\Exceptions\ImportException;
use Illuminate\Support\Facades\Http;
use App\Services\AuthorRankServices\AuthorRankService;

class OpenAlexService
{
    public function __construct(protected AuthorRankService $authorRankService) {}

    public function import(): void
    {
        $page = 1;
        $workCount = 0;
        while (true) {
            $response = $this->request('works', [
                'sort' => 'cited_by_count:desc',
                'per_page' => 50,
                'page' => $page,
            ]);

            if ($response->failed()) {
                throw new ImportException('Failed to import works.');
            }

            $works = $response->json('results');

            if (empty($works)) {
                break;
            }

            foreach ($works as $work) {
                $this->importWork($work);
            }

            $workCount += count($works);
            if ($workCount >= 1000) {
                break;
            }

            $page++;
        }
    }

    private function request(string $endpoint, array $params = []): \Illuminate\Http\Client\Response
    {
        return Http::get('https://api.openalex.org/' . $endpoint, $params);
    }

    private function importWork(array $work): void
    {
        $workId = Str::after($work['id'], 'https://openalex.org/W');

        // 1. Publisher (з правильного джерела)
        $publisher = null;
        $publisherUrl = $work['primary_location']['source']['host_organization'] ?? null;

        if ($publisherUrl) {
            $publisherOpenalexId = Str::after($publisherUrl, 'https://openalex.org/P');

            $publisher = Publisher::where('openalex_id', $publisherOpenalexId)->first();

            if (!$publisher) {
                $publisherResponse = $this->request("publishers/P{$publisherOpenalexId}");

                if ($publisherResponse->successful()) {
                    $publisherData = $publisherResponse->json();

                    $publisher = Publisher::updateOrCreate(
                        ['openalex_id' => $publisherOpenalexId],
                        [
                            'name' => $publisherData['display_name'],
                            'country' => $publisherData['country_code'] ?? null,
                            'website' => $publisherData['homepage_url'] ?? null,
                            'h_index' => $publisherData['summary_stats']['h_index'] ?? null,
                        ]
                    );
                }
            }
        }

        // 2. Publication
        $publication = Publication::updateOrCreate(
            ['openalex_id' => $workId],
            [
                'title' => $work['title'] ?? null,
                'published_at' => $work['publication_date'] ?? null,
                'publisher_id' => $publisher?->id,
                'citation_count' => $work['cited_by_count'] ?? 0,
                'doi' => $work['doi'] ?? null,
            ]
        );

        // 3. Authors
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

                $affiliation = $authorship['institutions'][0]['display_name'] ?? null;

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

                $publication->authors()->syncWithoutDetaching([
                    $author->id => ['author_position' => $position],
                ]);

                $position++;

                $this->authorRankService->calculate($author);
            }
        }

        // 4. Categories (Concepts)
        if (!empty($work['concepts'])) {
            foreach ($work['concepts'] as $concept) {
                $conceptId = Str::after($concept['id'], 'https://openalex.org/C');
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
