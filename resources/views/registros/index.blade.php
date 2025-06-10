@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Lista de Registros</h3>
                        <a href="{{ route('registros.create') }}" class="btn btn-primary">Crear Nuevo Registro</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Voluntario</th>
                                <th>Tipo Actividad</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tipo</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Millas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $registro)
                                <tr>
                                    <td>{{ $registro->id }}</td>
                                    <td>{{ $registro->voluntario->nombre_completo }}</td>
                                    <td>
                                        <span class="badge bg-{{ $registro->tipo_actividad == 'Entrada' ? 'success' : ($registro->tipo_actividad == 'Salida' ? 'primary' : 'warning') }}">
                                            {{ $registro->tipo_actividad ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $registro->fecha->format('m/d/Y') }}</td>
                                    <td>{{ $registro->hora->format('h:i A') }}</td>
                                    <td>{{ $registro->tipo }}</td>
                                    <td>{{ $registro->ubicacion_desde }}</td>
                                    <td>{{ $registro->ubicacion_hasta }}</td>
                                    <td>{{ number_format($registro->millas, 2) }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('registros.show', $registro->id) }}" class="btn btn-info btn-sm">Ver</a>
                                            <a href="{{ route('registros.edit', $registro->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                            <form action="{{ route('registros.destroy', $registro->id) }}" method="POST" style="display: inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este registro?')">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay registros disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
