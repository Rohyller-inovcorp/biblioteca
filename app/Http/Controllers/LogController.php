<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    protected $logs;

    public function __construct(LogService $logs)
    {
        $this->logs = $logs;
    }

    public function index()
    {
        return response()->json($this->logs->getAll());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'accion' => 'required|string',
            'usuario' => 'required|string',
        ]);

        return response()->json($this->logs->create($data));
    }
}
