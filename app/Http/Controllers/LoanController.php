<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Events\LoanCreated;
use Illuminate\Support\Facades\Log;
class LoanController extends Controller
{
    // Listar requisições (Admin vê tudo, Cidadão só as dele)
    public function index()
    {
        $user = Auth::user();
    $query = Loan::with(['user', 'book']);

    if ($user->role !== 'admin') {
        $query->where('user_id', $user->id);
    }

    return Inertia::render('Loans/Index', [
        'loans' => $query->latest()->paginate(15), 
        'stats' => [
            'active' => Loan::whereNull('actual_return_date')->count(),
            'last_30_days' => Loan::where('loan_date', '>=', now()->subDays(30))->count(),
            'delivered_today' => Loan::whereDate('actual_return_date', today())->count(),
        ]
    ]);
    }

    // Processar a requisição
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return back()->withErrors(['auth' => 'Precisa estar logado para requisitar.']);
        }
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $user = Auth::user();

        $activeLoansCount = \App\Models\Loan::where('user_id', $user->id)
            ->whereNull('actual_return_date')
            ->count();

        if ($activeLoansCount >= 3) {
            return back()->withErrors([
                'message' => 'Limite atingido! Já tem 3 livros requisitados.'
            ]);
        }

        try {
            $lastLoan = \App\Models\Loan::latest('id')->first();
            $nextNumber = $lastLoan ? $lastLoan->id + 1 : 1;
            $sequential = 'REQ-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $loan = \App\Models\Loan::create([
                'sequential_number'    => $sequential,
                'user_id'              => $user->id,
                'book_id'              => $request->book_id,
                'loan_date'            => now(),
                'expected_return_date' => now()->addDays(5),
            ]);
            event(new LoanCreated($loan, $user));
            return back()->with('success', 'Livro requisitado com sucesso!');
         
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao processar: ' . $e->getMessage()]);
        }
    }
    public function update(Request $request, Loan $loan)
    {
        if (Auth::user()->role !== 'admin') {
            return back()->withErrors(['message' => 'Apenas admins podem confirmar a receção.']);
        }

        $loan->actual_return_date = now();

        $loan->days_elapsed = $loan->loan_date->diffInDays($loan->actual_return_date);

        $loan->save();

        return back()->with('success', 'Livro recebido! O cidadão já pode requisitar outro.');
    }
}
