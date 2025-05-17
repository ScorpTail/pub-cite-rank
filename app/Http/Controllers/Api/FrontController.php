<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\SearchCollection;

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
}
