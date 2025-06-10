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
        'fecha',
        'hora',
        'tipo',
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
        'hora' => 'datetime',
        'millas' => 'decimal:2',
    ];

    /**
     * Obtiene el voluntario asociado con este registro.
     */
    public function voluntario()
    {
        return $this->belongsTo(Voluntario::class);
    }
}
