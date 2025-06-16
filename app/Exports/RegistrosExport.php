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
                'DAY' => ucfirst($registro->dia_semana),
                'DATE' => $registro->fecha->format('m/d/Y'),
                'VOLUNTARIO' => $registro->voluntario->nombre_completo,
                'REGULAR HOURS' => $this->formatHorariosSimple($registro),
                'TOTAL HOURS' => number_format($registro->horas_totales, 2),
                'MILES' => number_format($registro->millas_totales, 2),
                'PURPOSE' => $this->formatActividades($registro),
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
                $actividades[] = ($entrada->actividad ?? 'N/A');
            }
        }
        
        if ($registro->salidas->count() > 0) {
            foreach ($registro->salidas as $salida) {
                $actividades[] = ($salida->actividad ?? 'N/A');
            }
        }
        
        if ($registro->extras->count() > 0) {
            foreach ($registro->extras as $extra) {
                $actividades[] = ($extra->actividad ?? 'N/A');
            }
        }
        
        return implode(" | ", $actividades);
    }
    
    /**
     * Formatea los horarios simplificados (solo las horas) para el Excel
     */
    private function formatHorariosSimple($registro)
    {
        $horarios = [];
        
        if ($registro->entradas->count() > 0) {
            foreach ($registro->entradas as $entrada) {
                $horarios[] = $entrada->hora_formateada;
            }
        }
        
        if ($registro->salidas->count() > 0) {
            foreach ($registro->salidas as $salida) {
                $horarios[] = $salida->hora_formateada;
            }
        }
        
        if ($registro->extras->count() > 0) {
            foreach ($registro->extras as $extra) {
                $horarios[] = $extra->hora_formateada;
            }
        }
        
        return implode(" - ", $horarios);
    }
}
