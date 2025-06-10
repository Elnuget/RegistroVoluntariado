<?php

namespace App\Http\Controllers;

use App\Models\Voluntario;
use Illuminate\Http\Request;

class VoluntarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voluntarios = Voluntario::all();
        return view('voluntarios.index', compact('voluntarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('voluntarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:voluntarios',
            'telefono' => 'required|string|max:20',
            'ocupacion' => 'required|string|max:255',
        ]);

        Voluntario::create($request->all());

        return redirect()->route('voluntarios.index')
            ->with('success', 'Voluntario registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voluntario = Voluntario::findOrFail($id);
        return view('voluntarios.show', compact('voluntario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $voluntario = Voluntario::findOrFail($id);
        return view('voluntarios.edit', compact('voluntario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:voluntarios,email,'.$id,
            'telefono' => 'required|string|max:20',
            'ocupacion' => 'required|string|max:255',
        ]);

        $voluntario = Voluntario::findOrFail($id);
        $voluntario->update($request->all());

        return redirect()->route('voluntarios.index')
            ->with('success', 'Información del voluntario actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voluntario = Voluntario::findOrFail($id);
        $voluntario->delete();

        return redirect()->route('voluntarios.index')
            ->with('success', 'Voluntario eliminado exitosamente.');
    }
}
