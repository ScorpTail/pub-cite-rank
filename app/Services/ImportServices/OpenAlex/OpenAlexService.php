<?php

namespace App\Services\ImportServices\OpenAlex;

use Illuminate\Support\Str;
use App\Exceptions\ImportException;
use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
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

        $create = [];
        foreach ($publishers as $publisher) {

            $publisherId = Str::after($publisher['id'], 'https://openalex.org/P');

            if (!isset($create[$publisherId])) {
                $create[$publisherId] = [
                    'name' => $publisher['display_name'] ?? null,
                    'country' => $publisher['country_codes'][0] ?? null,
                    'website' => $publisher['homepage_url'] ?? null,
                    'h_index' => $publisher['summary_stats']['h_index'] ?? null,
                    'openalex_id' => $publisherId,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        Publisher::insert($create);
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
