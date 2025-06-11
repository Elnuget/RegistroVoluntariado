@extends('components.layout')

@section('styles')
    <!-- Estilos adicionales específicos para esta página pueden ir aquí -->
@endsection

@section('content')
    <x-form-header />

    <div class="form-body">
        <x-alerts />

        <form method="POST" action="{{ route('registros.store') }}">
            @csrf
            <input type="hidden" name="from_public_form" value="1">
            
            <x-voluntario-form :voluntarios="$voluntarios" />
            <x-date-time-fields />
            <x-activity-fields />
            <x-submit-button />
        </form>
    </div>

    <!-- Tabla de registros recientes -->
    <div class="form-body" style="margin-top: 40px;">
        <div style="background: white; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
            <h2 style="font-size: 24px; font-weight: 500; color: #202124; margin-bottom: 20px; text-align: center;">
                Registros Recientes
            </h2>
            
            @if($registros->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 12px; text-align: left; font-weight: 500; color: #5f6368;">Fecha</th>
                                <th style="padding: 12px; text-align: left; font-weight: 500; color: #5f6368;">Hora</th>
                                <th style="padding: 12px; text-align: left; font-weight: 500; color: #5f6368;">Voluntario</th>
                                <th style="padding: 12px; text-align: left; font-weight: 500; color: #5f6368;">Tipo</th>
                                <th style="padding: 12px; text-align: left; font-weight: 500; color: #5f6368;">Desde</th>
                                <th style="padding: 12px; text-align: left; font-weight: 500; color: #5f6368;">Hasta</th>
                                <th style="padding: 12px; text-align: right; font-weight: 500; color: #5f6368;">Millas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registros as $registro)
                                <tr style="border-bottom: 1px solid #e8eaed;">
                                    <td style="padding: 12px; color: #202124;">
                                        {{ $registro->fecha->format('m/d/Y') }}
                                    </td>
                                    <td style="padding: 12px; color: #202124;">
                                        {{ $registro->hora_formateada }}
                                    </td>
                                    <td style="padding: 12px; color: #202124;">
                                        {{ $registro->voluntario->nombre_completo }}
                                    </td>
                                    <td style="padding: 12px;">
                                        @if($registro->tipo_actividad == 'Entrada')
                                            <span style="background-color: #e8f5e8; color: #137333; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                                Entrada
                                            </span>
                                        @elseif($registro->tipo_actividad == 'Salida')
                                            <span style="background-color: #e3f2fd; color: #1565c0; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                                Salida
                                            </span>
                                        @else
                                            <span style="background-color: #fff3e0; color: #ef6c00; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                                Extra
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; color: #5f6368; font-size: 13px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $registro->ubicacion_desde }}
                                    </td>
                                    <td style="padding: 12px; color: #5f6368; font-size: 13px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $registro->ubicacion_hasta }}
                                    </td>
                                    <td style="padding: 12px; text-align: right; color: #202124; font-weight: 500;">
                                        {{ number_format($registro->millas, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #5f6368;">
                    <p style="font-size: 16px; margin: 0;">No hay registros disponibles</p>
                </div>
            @endif
        </div>
    </div>

    <x-form-footer />
@endsection

@push('scripts')
    <x-form-scripts />
@endpush
