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
     * Prepara los datos para la exportación por voluntario
     * 
     * @return array
     */
    public function sheets()
    {
        // Agrupar registros por voluntario
        $registrosPorVoluntario = $this->registrosAgrupados->groupBy(function($registro) {
            return $registro->voluntario->id;
        });
        
        $sheets = [];
        
        foreach ($registrosPorVoluntario as $voluntarioId => $registros) {
            // Nombre del voluntario para la hoja
            $nombreVoluntario = $registros->first()->voluntario->nombre_completo;
            
            // Limitar el nombre a 31 caracteres (límite de Excel para nombres de hojas)
            $nombreHoja = $this->formatSheetName($nombreVoluntario);
            
            // Crear colección de datos para esta hoja
            $datos = $this->prepararDatosVoluntario($registros);
            
            $sheets[$nombreHoja] = $datos;
        }
        
        return $sheets;
    }
    
    /**
     * Formatea el nombre de la hoja para que sea válido en Excel
     */
    private function formatSheetName($name)
    {
        // Reemplazar caracteres inválidos para nombres de hojas en Excel
        $invalidChars = ['/', '\\', '*', '[', ']', ':', '?'];
        $name = str_replace($invalidChars, '', $name);
        
        // Limitar a 31 caracteres (límite de Excel)
        $name = substr($name, 0, 31);
        
        // Asegurar que el nombre no está vacío
        if (empty($name)) {
            $name = 'Hoja';
        }
        
        return $name;
    }
    
    /**
     * Prepara los datos para un voluntario específico
     */
    private function prepararDatosVoluntario($registros)
    {
        $data = collect();
        
        foreach ($registros as $registro) {
            $row = [
                'DAY' => ucfirst($registro->dia_semana),
                'DATE' => $registro->fecha->format('m/d/Y'),
                'REGULAR HOURS' => $this->formatHorariosSimple($registro),
                'TOTAL HOURS' => number_format($registro->horas_totales, 2),
                'MILES' => number_format($registro->millas_totales, 2),
                'PURPOSE' => $this->formatActividades($registro),
                'LOCATIONS_FROM' => $this->formatUbicacionesDesde($registro),
                'LOCATIONS_TO' => $this->formatUbicacionesHasta($registro),
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
    
    /**
     * Formatea las ubicaciones desde para el JSON (donde inició el viaje)
     */
    private function formatUbicacionesDesde($registro)
    {
        $ubicaciones = [];
        
        if ($registro->entradas->count() > 0) {
            foreach ($registro->entradas as $entrada) {
                if (!empty($entrada->ubicacion_desde)) {
                    $ubicaciones[] = $entrada->ubicacion_desde;
                }
            }
        }
        
        if ($registro->salidas->count() > 0) {
            foreach ($registro->salidas as $salida) {
                if (!empty($salida->ubicacion_desde)) {
                    $ubicaciones[] = $salida->ubicacion_desde;
                }
            }
        }
        
        if ($registro->extras->count() > 0) {
            foreach ($registro->extras as $extra) {
                if (!empty($extra->ubicacion_desde)) {
                    $ubicaciones[] = $extra->ubicacion_desde;
                }
            }
        }
        
        // Eliminar duplicados y retornar
        $ubicaciones = array_unique($ubicaciones);
        return !empty($ubicaciones) ? implode(" | ", $ubicaciones) : 'N/A';
    }
    
    /**
     * Formatea las ubicaciones hasta para el JSON (donde terminó el viaje)
     */
    private function formatUbicacionesHasta($registro)
    {
        $ubicaciones = [];
        
        if ($registro->entradas->count() > 0) {
            foreach ($registro->entradas as $entrada) {
                if (!empty($entrada->ubicacion_hasta)) {
                    $ubicaciones[] = $entrada->ubicacion_hasta;
                }
            }
        }
        
        if ($registro->salidas->count() > 0) {
            foreach ($registro->salidas as $salida) {
                if (!empty($salida->ubicacion_hasta)) {
                    $ubicaciones[] = $salida->ubicacion_hasta;
                }
            }
        }
        
        if ($registro->extras->count() > 0) {
            foreach ($registro->extras as $extra) {
                if (!empty($extra->ubicacion_hasta)) {
                    $ubicaciones[] = $extra->ubicacion_hasta;
                }
            }
        }
        
        // Eliminar duplicados y retornar
        $ubicaciones = array_unique($ubicaciones);
        return !empty($ubicaciones) ? implode(" | ", $ubicaciones) : 'N/A';
    }
}
