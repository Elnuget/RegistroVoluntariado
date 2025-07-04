@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Lista de Registros</h3>
                        <div>
                            <a href="{{ route('registros.export.excel') }}" class="btn btn-success me-2">
                                <i class="fas fa-file-excel"></i> Exportar a Excel
                            </a>
                            <a href="{{ route('registros.create') }}" class="btn btn-primary">Crear Nuevo Registro</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th style="min-width: 80px;">Día</th>
                                    <th style="min-width: 100px;">Fecha</th>
                                    <th style="min-width: 150px;">Voluntario</th>
                                    <th style="min-width: 200px;">Actividades por Tipo</th>
                                    <th style="min-width: 180px;">Horarios</th>
                                    <th style="min-width: 300px;">Ubicaciones y Millas</th>
                                    <th style="min-width: 120px;">Acciones</th>
                                </tr>
                            </thead>
                        <tbody>
                            @forelse ($registrosAgrupados as $registro)
                                <tr>
                                    <td>
                                        <strong>{{ ucfirst($registro->dia_semana) }}</strong>
                                    </td>
                                    <td>{{ $registro->fecha->format('m/d/Y') }}</td>
                                    <td>{{ $registro->voluntario->nombre_completo }}</td>
                                    <td>
                                        <!-- Actividades de Entradas -->
                                        @if($registro->entradas->count() > 0)
                                            @foreach($registro->entradas as $entrada)
                                                <div class="mb-1">
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                        <i class="fas fa-sign-in-alt"></i> Entrada
                                                    </span>
                                                    <small class="d-block text-muted ms-2">{{ $entrada->actividad ?? 'N/A' }}</small>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        <!-- Actividades de Salidas -->
                                        @if($registro->salidas->count() > 0)
                                            @foreach($registro->salidas as $salida)
                                                <div class="mb-1">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                                        <i class="fas fa-sign-out-alt"></i> Salida
                                                    </span>
                                                    <small class="d-block text-muted ms-2">{{ $salida->actividad ?? 'N/A' }}</small>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        <!-- Actividades de Extras -->
                                        @if($registro->extras->count() > 0)
                                            @foreach($registro->extras as $extra)
                                                <div class="mb-1">
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                        <i class="fas fa-plus"></i> Extra
                                                    </span>
                                                    <small class="d-block text-muted ms-2">{{ $extra->actividad ?? 'N/A' }}</small>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($registro->entradas->count() == 0 && $registro->salidas->count() == 0 && $registro->extras->count() == 0)
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Entradas -->
                                        @if($registro->entradas->count() > 0)
                                            @foreach($registro->entradas as $entrada)
                                                <div class="mb-1">
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-sign-in-alt"></i> Entrada: {{ $entrada->hora_formateada }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        <!-- Salidas -->
                                        @if($registro->salidas->count() > 0)
                                            @foreach($registro->salidas as $salida)
                                                <div class="mb-1">
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-sign-out-alt"></i> Salida: {{ $salida->hora_formateada }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        <!-- Extras -->
                                        @if($registro->extras->count() > 0)
                                            @foreach($registro->extras as $extra)
                                                <div class="mb-1">
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-plus"></i> Extra: {{ $extra->hora_formateada }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        <!-- Horas Trabajadas -->
                                        @if($registro->horas_totales > 0)
                                            <div class="mt-2">
                                                <small class="text-info fw-bold">
                                                    <i class="fas fa-clock"></i> {{ number_format($registro->horas_totales, 2) }} horas trabajadas
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <!-- Si no hay registros -->
                                        @if($registro->entradas->count() == 0 && $registro->salidas->count() == 0 && $registro->extras->count() == 0)
                                            <span class="text-muted">Sin registros</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($registro->entradas->count() > 0)
                                            @foreach($registro->entradas as $entrada)
                                                <div class="mb-2 border-bottom pb-1">
                                                    <small class="text-success d-block">
                                                        <i class="fas fa-arrow-right"></i> <strong>Entrada:</strong>
                                                    </small>
                                                    <small class="text-muted ms-3">
                                                        <strong>Desde:</strong> {{ $entrada->ubicacion_desde }}
                                                    </small>
                                                    <small class="text-muted ms-3 d-block">
                                                        <strong>Hasta:</strong> {{ $entrada->ubicacion_hasta }}
                                                    </small>
                                                    @if($entrada->millas > 0)
                                                        <span class="badge bg-light text-dark ms-3">{{ number_format($entrada->millas, 2) }} millas</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($registro->salidas->count() > 0)
                                            @foreach($registro->salidas as $salida)
                                                <div class="mb-2 border-bottom pb-1">
                                                    <small class="text-primary d-block">
                                                        <i class="fas fa-arrow-left"></i> <strong>Salida:</strong>
                                                    </small>
                                                    <small class="text-muted ms-3">
                                                        <strong>Desde:</strong> {{ $salida->ubicacion_desde }}
                                                    </small>
                                                    <small class="text-muted ms-3 d-block">
                                                        <strong>Hasta:</strong> {{ $salida->ubicacion_hasta }}
                                                    </small>
                                                    @if($salida->millas > 0)
                                                        <span class="badge bg-light text-dark ms-3">{{ number_format($salida->millas, 2) }} millas</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($registro->extras->count() > 0)
                                            @foreach($registro->extras as $extra)
                                                <div class="mb-2 border-bottom pb-1">
                                                    <small class="text-warning d-block">
                                                        <i class="fas fa-plus"></i> <strong>Extra:</strong>
                                                    </small>
                                                    <small class="text-muted ms-3">
                                                        <strong>Desde:</strong> {{ $extra->ubicacion_desde }}
                                                    </small>
                                                    <small class="text-muted ms-3 d-block">
                                                        <strong>Hasta:</strong> {{ $extra->ubicacion_hasta }}
                                                    </small>
                                                    @if($extra->millas > 0)
                                                        <span class="badge bg-light text-dark ms-3">{{ number_format($extra->millas, 2) }} millas</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        <!-- Total de millas del día -->
                                        @if($registro->millas_totales > 0)
                                            <div class="mt-3 pt-2 border-top">
                                                <small class="text-dark fw-bold d-block">
                                                    <i class="fas fa-road"></i> <strong>Total del día:</strong> 
                                                    <span class="badge bg-secondary">{{ number_format($registro->millas_totales, 2) }} millas</span>
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalRegistros{{ $loop->index }}" title="Ver registros del día">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                        
                                        <!-- Modal para mostrar registros del día -->
                                        <div class="modal fade" id="modalRegistros{{ $loop->index }}" tabindex="-1" aria-labelledby="modalRegistrosLabel{{ $loop->index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalRegistrosLabel{{ $loop->index }}">
                                                            Registros del {{ ucfirst($registro->dia_semana) }} {{ $registro->fecha->format('m/d/Y') }} - {{ $registro->voluntario->nombre_completo }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Tipo</th>
                                                                    <th>Hora</th>
                                                                    <th>Desde</th>
                                                                    <th>Hasta</th>
                                                                    <th>Millas</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- Mostrar TODAS las entradas -->
                                                                @foreach($registro->entradas as $entrada)
                                                                    <tr>
                                                                        <td>
                                                                            <span class="badge bg-success">
                                                                                <i class="fas fa-sign-in-alt"></i> Entrada
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $entrada->hora_formateada }}</td>
                                                                        <td>{{ $entrada->ubicacion_desde }}</td>
                                                                        <td>{{ $entrada->ubicacion_hasta }}</td>
                                                                        <td>{{ number_format($entrada->millas, 2) }}</td>
                                                                        <td>
                                                                            <div class="btn-group" role="group">
                                                                                <a href="{{ route('registros.edit', $entrada->id) }}" class="btn btn-warning btn-sm">
                                                                                    <i class="fas fa-edit"></i> Editar
                                                                                </a>
                                                                                <form action="{{ route('registros.destroy', $entrada->id) }}" method="POST" style="display: inline" onsubmit="return confirmDelete('entrada')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                                                        <i class="fas fa-trash"></i> Eliminar
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                
                                                                <!-- Mostrar TODAS las salidas -->
                                                                @foreach($registro->salidas as $salida)
                                                                    <tr>
                                                                        <td>
                                                                            <span class="badge bg-primary">
                                                                                <i class="fas fa-sign-out-alt"></i> Salida
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $salida->hora_formateada }}</td>
                                                                        <td>{{ $salida->ubicacion_desde }}</td>
                                                                        <td>{{ $salida->ubicacion_hasta }}</td>
                                                                        <td>{{ number_format($salida->millas, 2) }}</td>
                                                                        <td>
                                                                            <div class="btn-group" role="group">
                                                                                <a href="{{ route('registros.edit', $salida->id) }}" class="btn btn-warning btn-sm">
                                                                                    <i class="fas fa-edit"></i> Editar
                                                                                </a>
                                                                                <form action="{{ route('registros.destroy', $salida->id) }}" method="POST" style="display: inline" onsubmit="return confirmDelete('salida')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                                                        <i class="fas fa-trash"></i> Eliminar
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                
                                                                <!-- Mostrar TODOS los extras -->
                                                                @foreach($registro->extras as $extra)
                                                                    <tr>
                                                                        <td>
                                                                            <span class="badge bg-warning">
                                                                                <i class="fas fa-plus"></i> Extra
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $extra->hora_formateada }}</td>
                                                                        <td>{{ $extra->ubicacion_desde }}</td>
                                                                        <td>{{ $extra->ubicacion_hasta }}</td>
                                                                        <td>{{ number_format($extra->millas, 2) }}</td>
                                                                        <td>
                                                                            <div class="btn-group" role="group">
                                                                                <a href="{{ route('registros.edit', $extra->id) }}" class="btn btn-warning btn-sm">
                                                                                    <i class="fas fa-edit"></i> Editar
                                                                                </a>
                                                                                <form action="{{ route('registros.destroy', $extra->id) }}" method="POST" style="display: inline" onsubmit="return confirmDelete('extra')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                                                        <i class="fas fa-trash"></i> Eliminar
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                
                                                                @if($registro->entradas->count() == 0 && $registro->salidas->count() == 0 && $registro->extras->count() == 0)
                                                                    <tr>
                                                                        <td colspan="6" class="text-center text-muted">
                                                                            <i class="fas fa-info-circle"></i> No hay registros para este día
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                            <tfoot class="table-light">
                                                                <tr>
                                                                    <td colspan="4" class="text-end fw-bold">Totales:</td>
                                                                    <td class="fw-bold">{{ number_format($registro->millas_totales, 2) }} millas</td>
                                                                    <td>
                                                                        @if($registro->horas_totales > 0)
                                                                            <small class="text-info fw-bold">
                                                                                {{ number_format($registro->horas_totales, 2) }} hrs
                                                                            </small>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay registros disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(tipo) {
    const messages = {
        'entrada': '¿Estás seguro de eliminar este registro de entrada?',
        'salida': '¿Estás seguro de eliminar este registro de salida?',
        'extra': '¿Estás seguro de eliminar este registro extra?'
    };
    
    return confirm(messages[tipo] || '¿Estás seguro de eliminar este registro?');
}
</script>
@endsection
