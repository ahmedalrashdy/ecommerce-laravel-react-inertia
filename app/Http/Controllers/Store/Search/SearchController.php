<?php

namespace App\Http\Controllers\Store\Search;

use App\Data\Search\SearchSuggestionData;
use App\Http\Controllers\Controller;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private SearchService $searchService
    ) {}

    /**
     * Get search suggestions for autocomplete.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
        ]);

        $query = (string) ($validated['q'] ?? '');

        $suggestions = $this->searchService->getSuggestions($query);

        return response()->json([
            'suggestions' => SearchSuggestionData::collect($suggestions),
        ]);
    }
}
