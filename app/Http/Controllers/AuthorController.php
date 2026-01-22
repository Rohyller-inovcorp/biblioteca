<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
    $search = $request->input('search');
    $sortBy = $request->input('sortBy', 'name');
    $sortDir = $request->input('sortDir', 'asc');

    $authors = Author::query()
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy($sortBy, $sortDir)
        ->paginate(3) 
        ->withQueryString();

    return Inertia::render('Authors/Index', [
        'authors' => $authors,
        'filters' => [
            'search'  => $search,
            'sortBy'  => $sortBy,
            'sortDir' => $sortDir,
        ],
    ]);
    }
    public function create()
    {
        return Inertia::render('Authors/Create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'photo' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('authors', 'public');
        }

        Author::create($validated);

        return redirect()
            ->route('authors.index')
            ->with('success', 'Autor criado com sucesso.');
    }
    public function edit(Author $author)
    {
        return Inertia::render('Authors/Edit', [
            'author' => $author,
        ]);
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($author->photo && Storage::disk('public')->exists($author->photo)) {
                Storage::disk('public')->delete($author->photo);
            }

            $validated['photo'] = $request->file('photo')->store('authors', 'public');
        } else {
            unset($validated['photo']);
        }

        $author->update($validated);

        return redirect()->route('authors.index')->with('success', 'Autor atualizado com sucesso.');
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return redirect()->route('authors.index')->with('success', 'Autor eliminado com sucesso.');
    }
}
