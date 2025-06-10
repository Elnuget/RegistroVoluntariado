@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Detalles del Registro</h3>
                        <div>
                            <a href="{{ route('registros.edit', $registro->id) }}" class="btn btn-warning">Editar</a>
                            <a href="{{ route('registros.index') }}" class="btn btn-secondary">Volver</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $registro->id }}</td>
                        </tr>
                        <tr>
                            <th>Voluntario:</th>
                            <td>
                                <a href="{{ route('voluntarios.show', $registro->voluntario_id) }}">
                                    {{ $registro->voluntario->nombre_completo }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Tipo de Actividad:</th>
                            <td>
                                <span class="badge bg-{{ $registro->tipo_actividad == 'Entrada' ? 'success' : ($registro->tipo_actividad == 'Salida' ? 'primary' : 'warning') }}">
                                    {{ $registro->tipo_actividad ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha:</th>
                            <td>{{ $registro->fecha ? $registro->fecha->format('m/d/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Hora:</th>
                            <td>{{ $registro->hora ? $registro->hora->format('h:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Ubicación Desde:</th>
                            <td>{{ $registro->ubicacion_desde }}</td>
                        </tr>
                        <tr>
                            <th>Ubicación Hasta:</th>
                            <td>{{ $registro->ubicacion_hasta }}</td>
                        </tr>
                        <tr>
                            <th>Millas:</th>
                            <td>{{ number_format($registro->millas, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $registro->created_at ? $registro->created_at->format('m/d/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $registro->updated_at ? $registro->updated_at->format('m/d/Y H:i') : 'N/A' }}</td>
                        </tr>
                    </table>

                    <form action="{{ route('registros.destroy', $registro->id) }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este registro?')">Eliminar Registro</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
