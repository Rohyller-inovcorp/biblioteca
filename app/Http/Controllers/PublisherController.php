<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publisher;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PublisherController extends Controller
{
    // 🔹 Listado con búsqueda, orden y paginación
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'name');
        $sortDir = $request->input('sortDir', 'asc');

        $publishers = Publisher::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate(3)
            ->withQueryString();

        return Inertia::render('Publishers/Index', [
            'publishers' => $publishers,
            'filters' => [
                'search'  => $search,
                'sortBy'  => $sortBy,
                'sortDir' => $sortDir,
            ],
        ]);
    }

    // 🔹 Página de creación
    public function create()
    {
        return Inertia::render('Publishers/Create');
    }

    // Salvar novo registro
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048', // 2MB
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('publishers', 'public');
        }

        Publisher::create($validated);

        return redirect()->route('publishers.index')->with('success', 'Editora criada com sucesso.');
    }

    // 🔹 Página de edición
    public function edit(Publisher $publisher)
    {
        return Inertia::render('Publishers/Edit', [
            'publisher' => $publisher,
        ]);
    }

    // Atualizar registro
    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('logo')) {
            if ($publisher->logo && Storage::disk('public')->exists($publisher->logo)) {
                Storage::disk('public')->delete($publisher->logo);
            }

            $validated['logo'] = $request->file('logo')->store('publishers', 'public');
        } else {
            unset($validated['logo']);
        }

        $publisher->update($validated);

        return redirect()->route('publishers.index')->with('success', 'Editora atualizada com sucesso.');
    }

    // Apagar registro
    public function destroy(Publisher $publisher)
    {
        if ($publisher->books()->exists()) {
            return back()->withErrors([
                'message' => 'Não é possível eliminar esta editora porque existem livros associados a ela.'
            ]);
        }
        if ($publisher->logo) {
            Storage::disk('public')->delete($publisher->logo);
        }
        $publisher->delete();

        return back();
    }
}
