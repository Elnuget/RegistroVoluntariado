<table>
    <thead>
        <tr>
            <th>Día</th>
            <th>Fecha</th>
            <th>Voluntario</th>
            <th>Actividades</th>
            <th>Horarios</th>
            <th>Millas</th>
            <th>Horas Trabajadas</th>
            <th>Ubicación Desde</th>
            <th>Ubicación Hasta</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($registrosAgrupados as $registro)
            <tr>
                <td>{{ ucfirst($registro->dia_semana) }}</td>
                <td>{{ $registro->fecha->format('m/d/Y') }}</td>
                <td>{{ $registro->voluntario->nombre_completo }}</td>
                <td>
                    @if($registro->entradas->count() > 0)
                        @foreach($registro->entradas as $entrada)
                            Entrada: {{ $entrada->actividad ?? 'N/A' }}
                        @endforeach
                    @endif
                    
                    @if($registro->salidas->count() > 0)
                        @foreach($registro->salidas as $salida)
                            Salida: {{ $salida->actividad ?? 'N/A' }}
                        @endforeach
                    @endif
                    
                    @if($registro->extras->count() > 0)
                        @foreach($registro->extras as $extra)
                            Extra: {{ $extra->actividad ?? 'N/A' }}
                        @endforeach
                    @endif
                </td>
                <td>
                    @if($registro->entradas->count() > 0)
                        @foreach($registro->entradas as $entrada)
                            Entrada: {{ $entrada->hora_formateada }}
                        @endforeach
                    @endif
                    
                    @if($registro->salidas->count() > 0)
                        @foreach($registro->salidas as $salida)
                            Salida: {{ $salida->hora_formateada }}
                        @endforeach
                    @endif
                    
                    @if($registro->extras->count() > 0)
                        @foreach($registro->extras as $extra)
                            Extra: {{ $extra->hora_formateada }}
                        @endforeach
                    @endif
                </td>
                <td>{{ number_format($registro->millas_totales, 2) }}</td>
                <td>{{ number_format($registro->horas_totales, 2) }}</td>
                <td>
                    @if($registro->entradas->count() > 0)
                        @foreach($registro->entradas as $entrada)
                            {{ $entrada->ubicacion_desde }}
                        @endforeach
                    @endif
                </td>
                <td>
                    @if($registro->salidas->count() > 0)
                        @foreach($registro->salidas as $salida)
                            {{ $salida->ubicacion_hasta }}
                        @endforeach
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">No hay registros disponibles.</td>
            </tr>
        @endforelse
    </tbody>
</table>
