<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\Voluntario;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los registros con información del voluntario
        $registros = Registro::with('voluntario')
            ->orderBy('fecha', 'desc')
            ->orderBy('voluntario_id')
            ->orderBy('hora')
            ->get();

        // Agrupar registros por fecha y voluntario
        $registrosAgrupados = $registros->groupBy(function($registro) {
            return $registro->fecha->format('Y-m-d') . '_' . $registro->voluntario_id;
        })->map(function($grupo) {
            $primerRegistro = $grupo->first();
            
            // Separar registros por tipo
            $entrada = $grupo->where('tipo_actividad', 'Entrada')->first();
            $salida = $grupo->where('tipo_actividad', 'Salida')->first();
            $extras = $grupo->where('tipo_actividad', 'Extra');
            
            // Calcular horas trabajadas con manejo de errores
            $horasTotales = 0;
            if ($entrada && $salida) {
                try {
                    // Obtener solo las horas y minutos como string
                    $horaEntradaStr = is_string($entrada->hora) ? $entrada->hora : $entrada->hora->format('H:i:s');
                    $horaSalidaStr = is_string($salida->hora) ? $salida->hora : $salida->hora->format('H:i:s');
                    
                    // Crear objetos Carbon desde las horas en la misma fecha
                    $fechaBase = $primerRegistro->fecha;
                    $horaEntrada = Carbon::createFromFormat('Y-m-d H:i:s', $fechaBase->format('Y-m-d') . ' ' . $horaEntradaStr);
                    $horaSalida = Carbon::createFromFormat('Y-m-d H:i:s', $fechaBase->format('Y-m-d') . ' ' . $horaSalidaStr);
                    
                    // Si la hora de salida es menor que la de entrada, asumir que es del día siguiente
                    if ($horaSalida->lt($horaEntrada)) {
                        $horaSalida->addDay();
                    }
                    
                    // Calcular diferencia en horas (salida - entrada)
                    $horasTotales = $horaSalida->diffInMinutes($horaEntrada) / 60;
                    
                    // Asegurar que el resultado sea positivo
                    $horasTotales = abs($horasTotales);
                    
                } catch (\Exception $e) {
                    // Si hay error en el parsing, establecer horas como 0
                    $horasTotales = 0;
                    Log::error('Error calculando horas: ' . $e->getMessage());
                }
            }
            
            return (object) [
                'fecha' => $primerRegistro->fecha,
                'dia_semana' => $primerRegistro->fecha->locale('es')->dayName,
                'voluntario' => $primerRegistro->voluntario,
                'entrada' => $entrada,
                'salida' => $salida,
                'extras' => $extras,
                'horas_totales' => $horasTotales,
                'ubicacion_entrada' => $entrada ? $entrada->ubicacion_desde : null,
                'ubicacion_salida' => $salida ? $salida->ubicacion_hasta : null,
                'millas_totales' => $grupo->sum('millas')
            ];
        })->sortByDesc('fecha');

        return view('registros.index', compact('registrosAgrupados'));
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
            'ubicacion_desde' => 'required|string|max:255',
            'ubicacion_hasta' => 'required|string|max:255',
            'millas' => 'nullable|numeric|min:0',
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
            'ubicacion_desde' => 'required|string|max:255',
            'ubicacion_hasta' => 'required|string|max:255',
            'millas' => 'nullable|numeric|min:0',
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
        return view('registros.registro-modular', compact('voluntarios'));
    }

    /**
     * Get voluntario information by ID.
     * Returns JSON response for AJAX requests.
     */
    public function getVoluntarioInfo(string $id)
    {
        try {
            $voluntario = Voluntario::findOrFail($id);
            return response()->json([
                'success' => true,
                'voluntario' => [
                    'id' => $voluntario->id,
                    'nombre_completo' => $voluntario->nombre_completo,
                    'direccion' => $voluntario->direccion,
                    'email' => $voluntario->email,
                    'telefono' => $voluntario->telefono,
                    'ocupacion' => $voluntario->ocupacion
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'No se encontró el voluntario solicitado'
            ], 404);
        }
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
