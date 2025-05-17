<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Admin\StatisticCollection;
use App\Models\Author;
use App\Models\AuthorRank;
use App\Models\Category;
use App\Models\Publication;
use App\Models\Publisher;

class AdminController extends Controller
{
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
}
