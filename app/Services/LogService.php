<?php 

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public function getAll() { return Log::orderBy('id', 'desc')->get(); } 
    public function create(array $data) { return Log::create($data); }
    public static function registrar($modulo, $objetoId, $alteracao)
    {
        Log::create([
            'data' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'user_id' => Auth::id(),
            'modulo' => $modulo,
            'objeto_id' => $objetoId,
            'alteracao' => $alteracao,
            'ip' => request()->ip(),
            'browser' => request()->header('User-Agent'),
        ]);
    }
}
