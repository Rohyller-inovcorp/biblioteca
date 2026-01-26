<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Inertia\Inertia;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => [
                'search' => $search
            ],
        ]);
    }
    public function show(Book $book)
    {

        $book->load([
            'loans' => function ($query) {
                $query->with('user')->latest();
            },
            'authors',  
        ]);

        return inertia('Books/Show', [
            'book' => $book,
            'isAdmin' => Auth::user()->role === 'admin'
        ]);
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
        // Validación
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
}
