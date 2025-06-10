<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\Voluntario;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $registros = Registro::with('voluntario')->get();
        return view('registros.index', compact('registros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $voluntarios = Voluntario::all();
        return view('registros.create', compact('voluntarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'voluntario_id' => 'required|exists:voluntarios,id',
            'tipo_actividad' => 'required|string|in:Entrada,Salida,Extra',
            'fecha' => 'required|date',
            'hora' => 'required',
            'tipo' => 'required|string|max:255',
            'ubicacion_desde' => 'required|string|max:255',
            'ubicacion_hasta' => 'required|string|max:255',
            'millas' => 'required|numeric|min:0',
        ]);

        Registro::create($request->all());

        // Si viene del formulario público, redirigir de vuelta al formulario
        if ($request->has('from_public_form')) {
            return redirect()->route('registros.formulario')
                ->with('success', 'Registro creado exitosamente. ¡Gracias por su participación!');
        }

        return redirect()->route('registros.index')
            ->with('success', 'Registro creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $registro = Registro::with('voluntario')->findOrFail($id);
        return view('registros.show', compact('registro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $registro = Registro::findOrFail($id);
        $voluntarios = Voluntario::all();
        return view('registros.edit', compact('registro', 'voluntarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'voluntario_id' => 'required|exists:voluntarios,id',
            'tipo_actividad' => 'required|string|in:Entrada,Salida,Extra',
            'fecha' => 'required|date',
            'hora' => 'required',
            'tipo' => 'required|string|max:255',
            'ubicacion_desde' => 'required|string|max:255',
            'ubicacion_hasta' => 'required|string|max:255',
            'millas' => 'required|numeric|min:0',
        ]);

        $registro = Registro::findOrFail($id);
        $registro->update($request->all());

        return redirect()->route('registros.index')
            ->with('success', 'Registro actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $registro = Registro::findOrFail($id);
        $registro->delete();

        return redirect()->route('registros.index')
            ->with('success', 'Registro eliminado exitosamente.');
    }

    /**
     * Show the public form for creating a new resource (Google Forms style).
     */
    public function formulario()
    {
        $voluntarios = Voluntario::all();
        return view('registros.registro', compact('voluntarios'));
    }

    /**
     * Check if a volunteer has any records for a specific date.
     * Returns JSON response for AJAX requests.
     */
    public function checkVoluntarioRegistros(Request $request)
    {
        $voluntarioId = $request->query('voluntario_id');
        $fecha = $request->query('fecha');

        if (!$voluntarioId || !$fecha) {
            return response()->json(['error' => 'Parámetros requeridos: voluntario_id y fecha'], 400);
        }

        $registrosEnFecha = Registro::where('voluntario_id', $voluntarioId)
            ->whereDate('fecha', $fecha)
            ->get();

        $tieneRegistros = $registrosEnFecha->count() > 0;
        $tipoSugerido = $tieneRegistros ? 'Salida' : 'Entrada';

        return response()->json([
            'tiene_registros' => $tieneRegistros,
            'tipo_sugerido' => $tipoSugerido,
            'registros_count' => $registrosEnFecha->count(),
            'registros' => $registrosEnFecha->map(function($registro) {
                return [
                    'id' => $registro->id,
                    'tipo_actividad' => $registro->tipo_actividad,
                    'hora' => $registro->hora->format('H:i')
                ];
            })
        ]);
    }
}
