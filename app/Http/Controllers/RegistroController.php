<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\Voluntario;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exports\RegistrosExport;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;
use Maatwebsite\Excel\Facades\Excel;

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
            
            // Separar registros por tipo (obtener TODOS los registros, no solo el primero)
            $entradas = $grupo->where('tipo_actividad', 'Entrada');
            $salidas = $grupo->where('tipo_actividad', 'Salida');
            $extras = $grupo->where('tipo_actividad', 'Extra');
            
            // Para compatibilidad con la vista actual, mantener entrada y salida como primer registro
            $entrada = $entradas->first();
            $salida = $salidas->first();
            
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
                // Nuevas colecciones con TODOS los registros de cada tipo
                'entradas' => $entradas,
                'salidas' => $salidas,
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
            'actividad' => 'required|string|max:255',
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
            'actividad' => 'required|string|max:255',
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
        $voluntarios = Voluntario::where('estado', 'activo')->get();
        $registros = Registro::with('voluntario')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->take(20) // Mostrar solo los últimos 20 registros
            ->get();
        return view('registros.registro-modular', compact('voluntarios', 'registros'));
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

        // Verificar si la fecha seleccionada es hoy
        $esHoy = Carbon::parse($fecha)->isToday();
        
        // Verificar si hay registros de entrada específicamente
        $tieneEntradaHoy = $registrosEnFecha->where('tipo_actividad', 'Entrada')->count() > 0;
        
        // Solo sugerir 'Entrada' automáticamente si es hoy y no hay entradas registradas
        $tipoSugerido = 'Extra'; // Valor por defecto
        if ($esHoy) {
            $tipoSugerido = $tieneEntradaHoy ? 'Salida' : 'Entrada';
        }

        return response()->json([
            'tiene_registros' => $registrosEnFecha->count() > 0,
            'es_hoy' => $esHoy,
            'tiene_entrada_hoy' => $tieneEntradaHoy,
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

    /**
     * API endpoint para obtener la dirección de un voluntario
     */
    public function getVoluntarioDireccion(Request $request)
    {
        $voluntarioId = $request->get('voluntario_id');
        
        if (!$voluntarioId) {
            return response()->json(['error' => 'ID de voluntario requerido'], 400);
        }

        $voluntario = Voluntario::find($voluntarioId);
        
        if (!$voluntario) {
            return response()->json(['error' => 'Voluntario no encontrado'], 404);
        }

        return response()->json([
            'direccion' => $voluntario->direccion,
            'nombre' => $voluntario->nombre_completo,
            'telefono' => $voluntario->telefono
        ]);
    }

    /**
     * Exportar los registros a Excel.
     */
    public function exportarExcel()
    {
        // Obtener todos los registros con información del voluntario
        $registros = Registro::with('voluntario')
            ->orderBy('fecha', 'desc')
            ->orderBy('voluntario_id')
            ->orderBy('hora')
            ->get();

        // Agrupar registros por fecha y voluntario (mismo código que en index)
        $registrosAgrupados = $registros->groupBy(function($registro) {
            return $registro->fecha->format('Y-m-d') . '_' . $registro->voluntario_id;
        })->map(function($grupo) {
            $primerRegistro = $grupo->first();
            
            // Separar registros por tipo
            $entradas = $grupo->where('tipo_actividad', 'Entrada');
            $salidas = $grupo->where('tipo_actividad', 'Salida');
            $extras = $grupo->where('tipo_actividad', 'Extra');
            
            // Para compatibilidad con la vista actual
            $entrada = $entradas->first();
            $salida = $salidas->first();
            
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
                // Nuevas colecciones con TODOS los registros de cada tipo
                'entradas' => $entradas,
                'salidas' => $salidas,
                'horas_totales' => $horasTotales,
                'ubicacion_entrada' => $entrada ? $entrada->ubicacion_desde : null,
                'ubicacion_salida' => $salida ? $salida->ubicacion_hasta : null,
                'millas_totales' => $grupo->sum('millas')
            ];
        })->sortByDesc('fecha');

        // Crear un objeto de exportación
        $exportObj = new RegistrosExport($registrosAgrupados);
        
        // Generar el nombre del archivo
        $fileName = 'Registros_Voluntariado_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Obtener las hojas para cada voluntario
        $sheetsData = $exportObj->sheets();
        
        // Crear una colección de hojas para FastExcel
        $sheetCollection = new SheetCollection($sheetsData);
        
        // Exportar usando fast-excel con múltiples hojas
        return (new FastExcel($sheetCollection))->download($fileName);
    }

    /**
     * Exportar los registros a JSON con el mismo formato del Excel.
     */
    public function exportarJson()
    {
        // Obtener todos los registros con información del voluntario
        $registros = Registro::with('voluntario')
            ->orderBy('fecha', 'desc')
            ->orderBy('voluntario_id')
            ->orderBy('hora')
            ->get();

        // Agrupar registros por fecha y voluntario (mismo código que en exportarExcel)
        $registrosAgrupados = $registros->groupBy(function($registro) {
            return $registro->fecha->format('Y-m-d') . '_' . $registro->voluntario_id;
        })->map(function($grupo) {
            $primerRegistro = $grupo->first();
            
            // Separar registros por tipo
            $entradas = $grupo->where('tipo_actividad', 'Entrada');
            $salidas = $grupo->where('tipo_actividad', 'Salida');
            $extras = $grupo->where('tipo_actividad', 'Extra');
            
            // Para compatibilidad con la vista actual
            $entrada = $entradas->first();
            $salida = $salidas->first();
            
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
                // Nuevas colecciones con TODOS los registros de cada tipo
                'entradas' => $entradas,
                'salidas' => $salidas,
                'horas_totales' => $horasTotales,
                'ubicacion_entrada' => $entrada ? $entrada->ubicacion_desde : null,
                'ubicacion_salida' => $salida ? $salida->ubicacion_hasta : null,
                'millas_totales' => $grupo->sum('millas')
            ];
        })->sortByDesc('fecha');

        // Crear un objeto de exportación
        $exportObj = new RegistrosExport($registrosAgrupados);
        
        // Obtener las hojas para cada voluntario (mismo formato que Excel)
        $sheetsData = $exportObj->sheets();
        
        // Convertir a formato JSON estructurado
        $jsonData = [];
        
        foreach ($sheetsData as $nombreVoluntario => $datos) {
            $jsonData[$nombreVoluntario] = $datos->toArray();
        }
        
        // Retornar como respuesta JSON
        return response()->json([
            'success' => true,
            'timestamp' => Carbon::now()->toISOString(),
            'total_voluntarios' => count($jsonData),
            'data' => $jsonData
        ], 200);
    }
}
