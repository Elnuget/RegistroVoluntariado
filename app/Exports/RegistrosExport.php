<?php

namespace App\Exports;

use App\Models\Registro;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RegistrosExport
{
    protected $registrosAgrupados;

    public function __construct($registrosAgrupados)
    {
        $this->registrosAgrupados = $registrosAgrupados;
    }

    /**
     * Prepara los datos para la exportación
     * 
     * @return Collection
     */
    public function collection()
    {
        $data = collect();
        
        foreach ($this->registrosAgrupados as $registro) {
            $row = [
                'Día' => ucfirst($registro->dia_semana),
                'Fecha' => $registro->fecha->format('m/d/Y'),
                'Voluntario' => $registro->voluntario->nombre_completo,
                'Actividades' => $this->formatActividades($registro),
                'Horarios' => $this->formatHorarios($registro),
                'Millas' => number_format($registro->millas_totales, 2),
                'Horas Trabajadas' => number_format($registro->horas_totales, 2),
                'Ubicación Desde' => $this->getUbicacionDesde($registro),
                'Ubicación Hasta' => $this->getUbicacionHasta($registro)
            ];
            
            $data->push($row);
        }
        
        return $data;
    }
    
    /**
     * Formatea las actividades para el Excel
     */
    private function formatActividades($registro)
    {
        $actividades = [];
        
        if ($registro->entradas->count() > 0) {
            foreach ($registro->entradas as $entrada) {
                $actividades[] = "Entrada: " . ($entrada->actividad ?? 'N/A');
            }
        }
        
        if ($registro->salidas->count() > 0) {
            foreach ($registro->salidas as $salida) {
                $actividades[] = "Salida: " . ($salida->actividad ?? 'N/A');
            }
        }
        
        if ($registro->extras->count() > 0) {
            foreach ($registro->extras as $extra) {
                $actividades[] = "Extra: " . ($extra->actividad ?? 'N/A');
            }
        }
        
        return implode("\n", $actividades);
    }
    
    /**
     * Formatea los horarios para el Excel
     */
    private function formatHorarios($registro)
    {
        $horarios = [];
        
        if ($registro->entradas->count() > 0) {
            foreach ($registro->entradas as $entrada) {
                $horarios[] = "Entrada: " . $entrada->hora_formateada;
            }
        }
        
        if ($registro->salidas->count() > 0) {
            foreach ($registro->salidas as $salida) {
                $horarios[] = "Salida: " . $salida->hora_formateada;
            }
        }
        
        if ($registro->extras->count() > 0) {
            foreach ($registro->extras as $extra) {
                $horarios[] = "Extra: " . $extra->hora_formateada;
            }
        }
        
        return implode("\n", $horarios);
    }
    
    /**
     * Obtiene la ubicación de origen
     */
    private function getUbicacionDesde($registro)
    {
        $ubicaciones = [];
        
        if ($registro->entradas->count() > 0) {
            foreach ($registro->entradas as $entrada) {
                $ubicaciones[] = $entrada->ubicacion_desde;
            }
        }
        
        return implode("\n", $ubicaciones);
    }
    
    /**
     * Obtiene la ubicación de destino
     */
    private function getUbicacionHasta($registro)
    {
        $ubicaciones = [];
        
        if ($registro->salidas->count() > 0) {
            foreach ($registro->salidas as $salida) {
                $ubicaciones[] = $salida->ubicacion_hasta;
            }
        }
        
        return implode("\n", $ubicaciones);
    }
}
