<?php

namespace App\Services\ImportServices\OpenAlex;

use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Publication;
use Illuminate\Support\Str;
use App\Exceptions\ImportException;
use Illuminate\Support\Facades\Http;

class OpenAlexService
{
    public function import(string $type = 'works')
    {
        return match ($type) {
            'works' => $this->importWorks(),
            'authors' => $this->importAuthors(),
            'publishers' => $this->importPublishers(),
            'concepts' => $this->importCategories(),
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

    private function importWorks()
    {
        $response = $this->request('works', [
            'page' => 1,
            'per_page' => 200,
        ]);

        if ($response->failed()) {
            throw new ImportException(__('front.import.openalex.works_failed'));
        }

        $works = $response->json();

        dd($works);
    }

    private function importAuthors()
    {
        $response = $this->request('authors', [
            'page' => 1,
            'per_page' => 50,
        ]);

        if ($response->failed()) {
            throw new ImportException(__('front.import.openalex.authors_failed'));
        }

        $authors = $response->json('results');

        foreach ($authors as $author) {
            $nameParts = explode(' ', $author['display_name']);
            $firstName = $nameParts[0] ?? null;
            $lastName = count($nameParts) > 1 ? array_pop($nameParts) : null;
            $middleName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1, -1)) : null;

            $insertData[] = [
                'user_id' => null,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'birth_date' => null,
                'about' => null,
                'orcid' => $author['orcid'] ?? null,
                'affiliation' => data_get($author, 'affiliations.0.institution.display_name'),
                'openalex_id' => Str::after($author['id'], 'https://openalex.org/A'),
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];
        }

        Author::insert($insertData);
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

                    // Спрощений поділ імені
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

                    $publication->authors()->syncWithoutDetaching([$author->id]);
                }
            }

            // Категорії
            if (!empty($work['concepts'])) {
                foreach ($work['concepts'] as $concept) {
                    $conceptId = Str::after($concept['id'], 'https://openalex.org/C');
                    $name = $concept['display_name'] ?? null;
                    $level = $concept['level'] ?? null;

                    // Пошук parent_id через перший ancestor
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

    private function importCategories(): void
    {
        $response = $this->request('concepts', [
            'filter' => 'level:0',
            'per_page' => 20,
            'page' => 1,
        ]);

        if ($response->failed()) {
            throw new ImportException(__('front.import.openalex.concepts_failed'));
        }

        $topConcepts = $response->json('results');
        $create = [];

        foreach ($topConcepts as $concept) {
            $this->traverseConcept($concept, null, $create);
        }
        dd($create);
        Category::insert($create);
    }

    private function traverseConcept(array $concept, ?string $parentId, array &$create): void
    {
        $openalexId = Str::after($concept['id'], 'https://openalex.org/');

        $create[] = [
            'openalex_concept_id' => $openalexId,
            'name' => Str::ucfirst(data_get($concept, 'display_name')),
            'level' => $concept['level'],
            'parent_id' => $parentId,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->request('concepts', [
            'filter' => 'ancestors.id:' . $openalexId,
            'per_page' => 20,
            'page' => 1,
        ]);

        if ($response->failed()) {
            return;
        }

        $children = $response->json('results');

        foreach ($children as $child) {
            dd($child);
            if (Str::after($concept['id'], 'https://openalex.org/') === $openalexId || empty($child['ancestors'])) {
                continue;
            }

            $this->traverseConcept($child, $openalexId, $create);
        }
    }
}
