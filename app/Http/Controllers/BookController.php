<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Inertia\Inertia;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleBooks\GoogleBooksService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');

        $books = Book::with(['publisher', 'authors', 'loans' => function ($query) {
            $query->whereNull('actual_return_date');
        }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(3)
            ->withQueryString();

        $userAlerts = [];
        $userActiveLoans = [];

        if (Auth::check()) {
            $userId = Auth::id();

            $userAlerts = \App\Models\BookAlert::where('user_id', $userId)
                ->where('is_notified', false)
                ->pluck('book_id')
                ->toArray();

            $userActiveLoans = \App\Models\Loan::where('user_id', $userId)
                ->whereNull('actual_return_date')
                ->pluck('book_id')
                ->toArray();
        }

        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => [
                'search' => $search
            ],
            'userAlerts' => $userAlerts,
            'userActiveLoans' => $userActiveLoans,
        ]);
    }
    public function show(Book $book)
    {
        $book->load([
            'authors',
            'loans.user',
        ]);

        $approvedReviews = $book->approvedReviews()->with('user')->paginate(5);

        $relatedBooks = collect();

        if ($book->bibliography) {

            $keywords = $this->extractKeywords($book->bibliography);

            if (!empty($keywords)) {
                $searchTerm = implode(' ', array_slice($keywords, 0, 5));

                $relatedBooks = Book::search($searchTerm)
                    ->where('id', '!=', $book->id)
                    ->take(10)
                    ->get();

            }
        }

        if ($relatedBooks->count() < 5) {
            $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $book->name);
            $titleWords = collect(explode(' ', mb_strtolower($cleanName)))
                ->filter(fn($word) => strlen($word) > 3)
                ->values();

            $descKeywords = collect($this->extractKeywords($book->bibliography ?? ''));

            $commonWords = $titleWords->intersect($descKeywords);

            if ($commonWords->isNotEmpty()) {
                $searchTerm = $commonWords->take(3)->implode(' ');

                $commonBooks = Book::search($searchTerm)
                    ->where('id', '!=', $book->id)
                    ->take(5)
                    ->get();

                $relatedBooks = $relatedBooks->merge($commonBooks)->unique('id');
            }
        }
        if ($relatedBooks->count() < 5) {
            $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $book->name);
            $titleKeywords = collect(explode(' ', $cleanName))
                ->filter(fn($word) => strlen($word) > 3)
                ->take(2)
                ->implode(' ');


            $titleBooks = Book::search($titleKeywords)
                ->where('id', '!=', $book->id)
                ->take(5)
                ->get();

            $relatedBooks = $relatedBooks->merge($titleBooks)->unique('id');
        }

        if ($relatedBooks->count() < 5 && $book->authors->isNotEmpty()) {
            $authorName = $book->authors->first()->name;

            $authorBooks = Book::search($authorName)
                ->where('id', '!=', $book->id)
                ->take(5)
                ->get();

            $relatedBooks = $relatedBooks->merge($authorBooks)->unique('id');
        }
        if ($relatedBooks->isEmpty()) {

            $relatedBooks = Book::where('id', '!=', $book->id)
                ->inRandomOrder()
                ->take(5)
                ->get();

        }
        $relatedBooks = $relatedBooks->take(5);
        $relatedBooks->load('authors');

        $relatedBooksFormatted = $relatedBooks->map(function ($relatedBook) {
            return [
                'id' => $relatedBook->id,
                'name' => $relatedBook->name,
                'cover_image' => $relatedBook->cover_image,
                'authors_array' => $relatedBook->authors->pluck('name')->toArray(),
            ];
        });

        return Inertia::render('Books/Show', [
            'book' => array_merge($book->toArray(), [
                'approved_reviews' => $approvedReviews
            ]),
            'relatedBooks' => $relatedBooksFormatted,
        ]);
    }


    private function extractKeywords(string $text, int $minLength = 4): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        $stopwords = [
            'o',
            'a',
            'os',
            'as',
            'um',
            'uma',
            'de',
            'do',
            'da',
            'dos',
            'das',
            'em',
            'no',
            'na',
            'nos',
            'nas',
            'por',
            'para',
            'com',
            'sem',
            'que',
            'este',
            'esta',
            'esse',
            'essa',
            'aquele',
            'aquela',
            'ser',
            'estar',
            'ter',
            'haver',
            'fazer',
            'ir',
            'vir',
            'the',
            'and',
            'or',
            'but',
            'in',
            'on',
            'at',
            'to',
            'for'
        ];

        $words = explode(' ', $text);

        $wordCount = [];
        foreach ($words as $word) {
            $word = trim($word);

            if (strlen($word) < $minLength || in_array($word, $stopwords)) {
                continue;
            }

            if (!isset($wordCount[$word])) {
                $wordCount[$word] = 0;
            }
            $wordCount[$word]++;
        }

        arsort($wordCount);

        return array_keys($wordCount);
    }

    public function create()
    {
        return Inertia::render('Books/Create', [
            'authors' => Author::select('id', 'name')->get(),
            'publishers' => Publisher::select('id', 'name')->get(),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'isbn' => 'required|string|unique:books,isbn',
            'name' => 'required|string',
            'publisher_id' => 'required|exists:publishers,id',
            'bibliography' => 'nullable|string',
            'price' => 'required|numeric',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,id',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book = Book::create([
            'isbn' => $data['isbn'],
            'name' => $data['name'],
            'publisher_id' => $data['publisher_id'],
            'bibliography' => $data['bibliography'] ?? null,
            'price' => $data['price'],
            'cover_image' => $data['cover_image'] ?? null,
        ]);


        $book->authors()->sync($data['authors']);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }



    public function edit(Book $book)
    {
        $book->load('authors');

        return Inertia::render('Books/Edit', [
            'book' => $book,
            'authors' => Author::select('id', 'name')->get(),
            'publishers' => Publisher::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'name' => 'required|string',
            'publisher_id' => 'required|exists:publishers,id',
            'bibliography' => 'nullable|string',
            'price' => 'required|numeric',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($data);
        $book->authors()->sync($data['authors']);

        return redirect()->route('books.index');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->authors()->detach();
        $book->delete();

        return redirect()->route('books.index');
    }

    public function googleSearch(Request $request, GoogleBooksService $service)
    {
        try {
            $query = trim($request->input('q'));

            Log::info('Google Books Search', [
                'query' => $query,
                'user_id' => Auth::id()
            ]);

            if (empty($query)) {
                return Inertia::render('Books/GoogleSearch', [
                    'books' => [],
                    'filters' => ['q' => '']
                ]);
            }

            $response = $service->search($query);

            Log::info('Google Books Search Results', [
                'query' => $query,
                'status' => $response['status'] ?? 'unknown',
                'count' => isset($response['data']) ? count($response['data']) : 0
            ]);

            return Inertia::render('Books/GoogleSearch', [
                'books'   => $response['data'] ?? [],
                'filters' => $request->only(['q']),
                'flash'   => [
                    'info'  => $response['status'] === 'not_found' ? $response['message'] : null,
                    'error' => in_array($response['status'], ['error_limit', 'error_api']) ? $response['message'] : null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in googleSearch', ['error' => $e->getMessage()]);

            return Inertia::render('Books/GoogleSearch', [
                'books' => [],
                'filters' => $request->only(['q'])
            ])->with('error', 'Erro inesperado. Tente novamente.');
        }
    }
    public function googleImport(Request $request, GoogleBooksService $service)
    {
        $validated = $request->validate([
            'google_books_id' => 'required|string'
        ]);

        try {
            Log::info('Starting book import', [
                'google_books_id' => $validated['google_books_id']
            ]);

            $data = $service->findById($validated['google_books_id']);

            if (!$data) {
                Log::warning('Book data not found', [
                    'google_books_id' => $validated['google_books_id']
                ]);
                return back()->with('error', 'Falha ao obter dados da API.');
            }

            return DB::transaction(function () use ($data) {
                $publisher = Publisher::firstOrCreate(
                    ['name' => $data['publisher_name']],
                    ['notes' => 'Importada via Google API']
                );

                Log::info('Publisher created/found', ['id' => $publisher->id]);


                $localCoverPath = $this->downloadCoverImage(
                    $data['cover_image'],
                    $data['isbn']
                );


                $book = Book::updateOrCreate(
                    ['google_books_id' => $data['google_books_id']],
                    [
                        'isbn' => $data['isbn'],
                        'name' => $data['name'],
                        'publisher_id' => $publisher->id,
                        'bibliography' => $data['bibliography'],
                        'cover_image' => $localCoverPath,
                        'price' => $data['price'] ?? 0.00,
                        'google_books_synced_at' => now(),
                    ]
                );

                Log::info('Book created/updated', ['id' => $book->id]);

                $authorIds = collect($data['authors_array'])->map(function ($name) {
                    $author = Author::firstOrCreate(['name' => $name]);
                    Log::info('Author created/found', [
                        'id' => $author->id,
                        'name' => $name
                    ]);
                    return $author->id;
                });


                $book->authors()->sync($authorIds);

                Log::info('Book import completed successfully', [
                    'book_id' => $book->id
                ]);

                return redirect()
                    ->route('books.index')
                    ->with('success', 'Livro e capa importados localmente!');
            });
        } catch (\Exception $e) {
            Log::error('Error importing book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'google_books_id' => $request->google_books_id
            ]);

            return back()->with('error', 'Erro ao importar livro: ' . $e->getMessage());
        }
    }

    private function downloadCoverImage(?string $url, ?string $isbn): ?string
    {
        if (!$url) {
            Log::info('No cover image URL provided');
            return null;
        }

        try {
            Log::info('Downloading cover image', ['url' => $url]);


            $imageContent = @file_get_contents($url);

            if ($imageContent === false) {
                Log::warning('Failed to download cover image', ['url' => $url]);
                return null;
            }

            $fileName = 'covers/' . ($isbn ?? Str::random(10)) . '.jpg';

            Storage::disk('public')->put($fileName, $imageContent);

            Log::info('Cover image downloaded successfully', ['path' => $fileName]);

            return $fileName;
        } catch (\Exception $e) {
            Log::error('Error downloading cover image', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            return null;
        }
    }
}
