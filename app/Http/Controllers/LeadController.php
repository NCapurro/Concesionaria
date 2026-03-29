<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validamos los datos (Laravel rechaza automáticamente si falla)
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'interes' => 'required|string|max:100',
            'mensaje' => 'nullable|string|max:1000',
        ]);

        // 2. Guardamos en la base de datos
        Lead::create($validated);

        // 3. Volvemos a la página principal (Inertia detecta esto y no recarga la web)
        return back();
    }
}