<?php

namespace App\Services\Seed;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PexelsImageService
{
    public function getImagePath(string $query, string $folder = 'seed-images'): string
    {
        $normalized = Str::of($query)->lower()->squish()->toString();
        $cacheKey = 'pexels:query:'.sha1($normalized);

        $cachedPath = Cache::get($cacheKey);
        if (is_string($cachedPath) && Storage::disk('public')->exists($cachedPath)) {
            return $cachedPath;
        }

        Cache::forget($cacheKey);

        return Cache::rememberForever($cacheKey, function () use ($normalized, $folder) {
            $photo = $this->searchPhoto($normalized);
            $url = $photo['src']['large2x'] ?? $photo['src']['original'] ?? null;

            if (! is_string($url) || $url === '') {
                throw new RuntimeException('Pexels response missing image URL.');
            }

            $extension = $this->guessExtension($url);
            $filename = sha1($normalized).'.'.$extension;
            $path = trim($folder, '/').'/'.$filename;

            $disk = Storage::disk('public');
            if (! $disk->exists($path)) {
                $this->downloadImage($url, $path);
            }

            return $path;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function searchPhoto(string $query): array
    {
        $response = $this->httpClient()
            ->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'per_page' => 1,
            ])
            ->throw()
            ->json();

        $photo = $response['photos'][0] ?? null;

        if (! is_array($photo)) {
            throw new RuntimeException('No Pexels image found for query: '.$query);
        }

        return $photo;
    }

    private function downloadImage(string $url, string $path): void
    {
        $response = $this->httpClient()->get($url)->throw();

        Storage::disk('public')->put($path, $response->body());
    }

    private function httpClient(): PendingRequest
    {
        $key = config('services.pexels.key');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException('Missing PEXELS_API_KEY configuration.');
        }

        return Http::withHeaders([
            'Authorization' => $key,
        ]);
    }

    private function guessExtension(string $url): string
    {
        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);

        if ($extension !== '') {
            return strtolower($extension);
        }

        return 'jpg';
    }
}
