<?php

namespace App\Services\GoogleBooks;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleBooksService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected int $maxResults;
    protected int $cacheTtl;
    protected int $timeout;

    public function __construct()
    {
        $config = config('services.google_books');

        $this->apiKey = $config['api_key'];
        $this->baseUrl = rtrim($config['api_url'], '/');
        $this->maxResults = 20;
        $this->cacheTtl = $config['cache_ttl'] ?? 3600;
        $this->timeout = $config['timeout'] ?? 15;
    }

    public function search(string $query): array
    {
        $query = trim($query);
        $cacheKey = 'google_books_' . md5($query);

        if (Cache::has($cacheKey)) {
            Log::info("GoogleBooksService: Returning cached results", ['query' => $query]);
            return [
                'status' => 'success',
                'data' => Cache::get($cacheKey)
            ];
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/volumes", [
                'q' => $query,
                'maxResults' => $this->maxResults,
                'key' => $this->apiKey,
            ]);

            /** @var \Illuminate\Http\Client\Response $response */
            $data = $response->json();

            if ($response->successful() && !empty($data['items'])) {
                $formattedData = $this->formatResults($data);

                Cache::put($cacheKey, $formattedData, now()->addHours(2));

                return [
                    'status' => 'success',
                    'data' => $formattedData
                ];
            }

            Log::info("GoogleBooksService: No items found", ['query' => $query]);

            return [
                'status' => 'not_found',
                'data' => [],
                'message' => 'Nenhum livro encontrado para esta busca.'
            ];
        } catch (\Exception $e) {
            Log::error("GoogleBooksService: Exception", ['query' => $query, 'error' => $e->getMessage()]);

            return [
                'status' => 'not_found',
                'data' => [],
                'message' => 'Nenhum livro encontrado para esta busca.'
            ];
        }
    }




    public function findById(string $googleId)
    {
        try {
            Log::info('GoogleBooksService: Finding book by ID', [
                'google_id' => $googleId
            ]);

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout($this->timeout)
                ->retry(3, 1000)
                ->withoutVerifying()
                ->get("{$this->baseUrl}/volumes/{$googleId}", [
                    'key' => $this->apiKey,
                ]);

            if ($response->failed()) {
                Log::warning('GoogleBooksService: Failed to find book', [
                    'google_id' => $googleId,
                    'status' => $response->status()
                ]);
                return null;
            }

            $bookData = $this->mapBookData($response->json());

            Log::info('GoogleBooksService: Book found successfully');

            return $bookData;
        } catch (\Exception $e) {
            Log::error('GoogleBooksService: Error finding book by ID', [
                'error' => $e->getMessage(),
                'google_id' => $googleId,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function extractIsbn(array $identifiers): ?string
    {
        if (empty($identifiers)) {
            return null;
        }

        foreach ($identifiers as $identifier) {
            if (isset($identifier['type']) && $identifier['type'] === 'ISBN_13') {
                return $identifier['identifier'] ?? null;
            }
        }

        foreach ($identifiers as $identifier) {
            if (isset($identifier['type']) && $identifier['type'] === 'ISBN_10') {
                return $identifier['identifier'] ?? null;
            }
        }

        return $identifiers[0]['identifier'] ?? null;
    }

    protected function formatResults(array $data): array
    {
        if (!isset($data['items']) || !is_array($data['items'])) {
            Log::warning('GoogleBooksService: No items in API response');
            return [];
        }

        return collect($data['items'])->map(function ($item) {
            try {
                $info = $item['volumeInfo'] ?? [];

                return [
                    'google_books_id' => $item['id'] ?? null,
                    'name'            => $info['title'] ?? 'Sem Título',
                    'authors_array'   => $info['authors'] ?? [],
                    'publisher_name'  => $info['publisher'] ?? 'Desconhecida',
                    'isbn'            => $this->extractIsbn($info['industryIdentifiers'] ?? []),
                    'bibliography'    => strip_tags($info['description'] ?? ''),
                    'cover_image'     => $info['imageLinks']['thumbnail'] ?? null,
                    'price'           => $item['saleInfo']['listPrice']['amount'] ?? null,
                ];
            } catch (\Exception $e) {
                Log::error('GoogleBooksService: Error formatting book item', [
                    'error' => $e->getMessage(),
                    'item_id' => $item['id'] ?? 'unknown'
                ]);
                return null;
            }
        })->filter()->values()->toArray();
    }

    protected function mapBookData(array $item): array
    {
        $info = $item['volumeInfo'] ?? [];
        $identifiers = $info['industryIdentifiers'] ?? [];

        return [
            'google_books_id'        => $item['id'] ?? null,
            'isbn'                   => $this->extractIsbn($identifiers),
            'name'                   => $info['title'] ?? 'Sem Título',
            'publisher_name'         => $info['publisher'] ?? 'Editora Desconhecida',
            'bibliography'           => strip_tags($info['description'] ?? ''),
            'cover_image'            => $info['imageLinks']['thumbnail'] ?? null,
            'price'                  => $item['saleInfo']['listPrice']['amount'] ?? 0.00,
            'google_books_synced_at' => now(),
            'authors_array'          => $info['authors'] ?? [],
        ];
    }
}
