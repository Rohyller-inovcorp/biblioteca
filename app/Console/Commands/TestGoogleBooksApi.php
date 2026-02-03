<?php

namespace App\Console\Commands;

use App\Services\GoogleBooks\GoogleBooksService;
use Illuminate\Console\Command;

class TestGoogleBooksApi extends Command
{
    protected $signature = 'google-books:test {query?}';
    protected $description = 'Test Google Books API connection and search';

    public function handle(GoogleBooksService $service)
    {
        $this->info('=== Google Books API Test ===');
        $this->newLine();

        $this->info('Checking configuration...');
        $apiKey = config('services.google_books.api_key');
        $apiUrl = config('services.google_books.api_url');
        
        if (empty($apiKey)) {
            $this->error('❌ API Key not configured! Please set GOOGLE_BOOKS_API_KEY in .env');
            return 1;
        }

        $this->info('✓ API Key: ' . substr($apiKey, 0, 10) . '...');
        $this->info('✓ API URL: ' . $apiUrl);
        $this->newLine();

        $query = $this->argument('query') ?? $this->ask('Enter search query', 'Laravel');
        
        $this->info("Searching for: {$query}");
        $this->newLine();

        $startTime = microtime(true);
        
        try {
            $results = $service->search($query);
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            if (empty($results)) {
                $this->warn('⚠ No results found');
                return 0;
            }

            $this->info("✓ Found {count($results)} results in {$duration}ms");
            $this->newLine();

            $this->info('First 3 results:');
            $this->newLine();

            foreach (array_slice($results, 0, 3) as $index => $book) {
                $this->line("Book #" . ($index + 1));
                $this->line("  Title: {$book['name']}");
                $this->line("  Authors: " . implode(', ', $book['authors_array']));
                $this->line("  ISBN: {$book['isbn']}");
                $this->line("  Publisher: {$book['publisher_name']}");
                $this->line("  Google ID: {$book['google_books_id']}");
                $this->newLine();
            }

            if (!empty($results[0]['google_books_id'])) {
                $googleId = $results[0]['google_books_id'];
                $this->info("Testing findById with ID: {$googleId}");
                
                $startTime = microtime(true);
                $bookData = $service->findById($googleId);
                $endTime = microtime(true);
                $duration = round(($endTime - $startTime) * 1000, 2);

                if ($bookData) {
                    $this->info("✓ Book data retrieved in {$duration}ms");
                    $this->line("  Title: {$bookData['name']}");
                    $this->line("  Has cover: " . ($bookData['cover_image'] ? 'Yes' : 'No'));
                } else {
                    $this->error('❌ Failed to retrieve book data');
                }
            }

            $this->newLine();
            $this->info('✓ All tests completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace:');
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}