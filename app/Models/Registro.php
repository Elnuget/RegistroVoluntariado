<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'registros';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'voluntario_id',
        'tipo_actividad',
        'fecha',
        'hora',
        'ubicacion_desde',
        'ubicacion_hasta',
        'millas',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array
     */
    protected $casts = [
        'fecha' => 'date',
        'millas' => 'decimal:2',
    ];

    /**
     * Obtiene el voluntario asociado con este registro.
     */
    public function voluntario()
    {
        return $this->belongsTo(Voluntario::class);
    }

    /**
     * Accessor para formatear la hora
     */
    public function getHoraFormateadaAttribute()
    {
        try {
            // Si la hora es null, devolver string vacío
            if (!$this->hora) {
                return '-';
            }
            
            // Si la hora es string en formato H:i:s
            if (is_string($this->hora)) {
                // Intentar diferentes formatos
                if (strlen($this->hora) == 8) { // H:i:s
                    return \Carbon\Carbon::createFromFormat('H:i:s', $this->hora)->format('h:i A');
                } elseif (strlen($this->hora) == 5) { // H:i
                    return \Carbon\Carbon::createFromFormat('H:i', $this->hora)->format('h:i A');
                }
                return $this->hora; // Si no coincide con formatos esperados, devolver tal como está
            }
            
            // Si es objeto Carbon/datetime
            if ($this->hora instanceof \Carbon\Carbon || $this->hora instanceof \DateTime) {
                return $this->hora->format('h:i A');
            }
            
            // Fallback: intentar parsear como string
            return \Carbon\Carbon::parse($this->hora)->format('h:i A');
            
        } catch (\Exception $e) {
            // Si todo falla, devolver el valor original
            return $this->hora ?? '-';
        }
    }
}
