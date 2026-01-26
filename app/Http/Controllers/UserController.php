<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        return Inertia::render('Users/Index', [
            'users' => $query->get(),
            'filters' => $request->only(['search', 'role'])
        ]);
    }
    public function show(User $user)
    {
        $currentUser = Auth::user();

        // Usuario normal: solo puede verse a sí mismo
        if ($currentUser->role !== 'admin' && $currentUser->id !== $user->id) {
            abort(403, 'Você só pode visualizar o seu próprio histórico.');
        }

        $user->load([
            'loans' => function ($query) {
                $query->with('book')->latest();
            }
        ]);

        return inertia('Users/Show', [
            'user' => $user,
            'isAdmin' => $currentUser->role === 'admin',
        ]);
    }
    public function create()
    {
        return Inertia::render('Users/Create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,cidadao',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Utilizador criado com sucesso!');
    }
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', ['user' => $user]);
    }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,cidadao',
            'photo' => 'nullable|image|max:2048', // 2MB max
        ]);

        $user->update($request->only('name', 'email', 'role'));

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');

            $user->update(['profile_photo_path' => $path]);
        }

        return redirect()->route('users.index')->with('success', 'Utilizador e foto atualizados com sucesso!');
    }
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilizador excluído com sucesso!');
    }
}
