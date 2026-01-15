<?php

namespace App\Services\Search;

class SearchQueryService
{
    public function normalize(string $query): string
    {
        $query = trim($query);
        $query = preg_replace('/\s+/', ' ', $query) ?? $query;

        return $query;
    }

    /**
     * @return string[]
     */
    public function tokenize(string $query, int $minLength = 1, int $maxTokens = 5): array
    {
        $parts = preg_split('/\s+/', trim($query)) ?: [];
        $parts = array_values(array_filter($parts, fn ($part) => mb_strlen($part) >= $minLength));

        return array_slice($parts, 0, $maxTokens);
    }

    public function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
