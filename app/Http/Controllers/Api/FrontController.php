<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Author;
use App\Models\Category;
use App\Enums\StatusEnum;
use App\Models\Publisher;
use App\Models\AuthorRank;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\SearchCollection;
use App\Http\Resources\Api\Admin\StatisticCollection;
use App\Http\Resources\Api\Front\TopAuthorCollection;

class FrontController extends Controller
{
    public function search(Request $request)
    {
        $query = Author::where('first_name', 'like', '%' . $request->input('name') . '%')
            ->orWhere('middle_name', 'like', '%' . $request->input('name') . '%')
            ->orWhere('last_name', 'like', '%' . $request->input('name') . '%');

        $searchResults = $query->get();

        return SearchCollection::make($searchResults);
    }

    public function statistic(Request $request)
    {
        $statistic = [
            'total_users' => User::where('status', StatusEnum::ACTIVE)->count(),
            'total_publications' => Publication::count(),
            'total_publishers' => Publisher::count(),
            'total_authors' => Author::count(),
            'total_categories' => Category::count(),
            'average_h_index' => AuthorRank::where('h_index', '!=', null)->avg('h_index'),
            'max_h_index' => AuthorRank::where('h_index', '!=', null)->max('h_index'),
        ];

        return StatisticCollection::make($statistic);
    }

    public function topAuthors(Request $request)
    {
        $topAuthors = Author::select('authors.*')
            ->join('author_ranks as ranks', 'ranks.author_id', 'authors.id')
            ->orderBy('ranks.total_citations', 'desc')
            ->orderBy('ranks.total_publications', 'desc')
            ->with('rank')
            ->take(8)
            ->get();

        return TopAuthorCollection::make($topAuthors);
    }
}
