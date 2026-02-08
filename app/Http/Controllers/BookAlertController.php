<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookAlert;
use Illuminate\Support\Facades\Auth;

class BookAlertController extends Controller
{
    public function subscribe(Request $request)
    {
        $userId = Auth::id();
        $bookId = $request->book_id;

        // Tenta encontrar um alerta ativo. Se não existir, cria um.
        $alert = BookAlert::firstOrCreate(
            [
                'user_id' => $userId,
                'book_id' => $bookId,
                'is_notified' => false
            ]
        );

        // Se o alerta já existia (não foi criado agora)
        if (!$alert->wasRecentlyCreated) {
            return back()->withErrors([
                'message' => 'Já estás na lista de espera para este livro!'
            ]);
        }

        return back()->with('success', 'Alerta criado com sucesso!');
    }
}
