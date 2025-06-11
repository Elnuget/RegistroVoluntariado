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
                                <th>Día</th>
                                <th>Fecha</th>
                                <th>Voluntario</th>
                                <th>Entrada/Salida/Extra</th>
                                <th>Ubicaciones y Millas</th>
                                <th>Acciones</th>
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
                                        <!-- Entrada -->
                                        @if($registro->entrada)
                                            <div class="mb-1">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-sign-in-alt"></i> Entrada: {{ $registro->entrada->hora_formateada }}
                                                </span>
                                            </div>
                                        @endif
                                        
                                        <!-- Salida -->
                                        @if($registro->salida)
                                            <div class="mb-1">
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-sign-out-alt"></i> Salida: {{ $registro->salida->hora_formateada }}
                                                </span>
                                            </div>
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
                                        @if(!$registro->entrada && !$registro->salida && $registro->extras->count() == 0)
                                            <span class="text-muted">Sin registros</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($registro->entrada)
                                            <small class="text-success d-block">
                                                <i class="fas fa-arrow-right"></i> <strong>Origen:</strong> {{ Str::limit($registro->ubicacion_entrada, 30) }}
                                                @if($registro->entrada->millas > 0)
                                                    <span class="badge bg-light text-dark ms-1">{{ number_format($registro->entrada->millas, 2) }} mi</span>
                                                @endif
                                            </small>
                                        @endif
                                        @if($registro->salida)
                                            <small class="text-primary d-block">
                                                <i class="fas fa-arrow-left"></i> <strong>Destino:</strong> {{ Str::limit($registro->ubicacion_salida, 30) }}
                                                @if($registro->salida->millas > 0)
                                                    <span class="badge bg-light text-dark ms-1">{{ number_format($registro->salida->millas, 2) }} mi</span>
                                                @endif
                                            </small>
                                        @endif
                                        @if($registro->extras->count() > 0)
                                            @foreach($registro->extras as $extra)
                                                <small class="text-warning d-block">
                                                    <i class="fas fa-plus"></i> <strong>Extra:</strong> {{ Str::limit($extra->ubicacion_desde, 15) }} → {{ Str::limit($extra->ubicacion_hasta, 15) }}
                                                    @if($extra->millas > 0)
                                                        <span class="badge bg-light text-dark ms-1">{{ number_format($extra->millas, 2) }} mi</span>
                                                    @endif
                                                </small>
                                            @endforeach
                                        @endif
                                        
                                        <!-- Total de millas del día -->
                                        @if($registro->millas_totales > 0)
                                            <div class="mt-2 pt-2 border-top">
                                                <small class="text-dark fw-bold d-block">
                                                    <i class="fas fa-road"></i> <strong>Total del día:</strong> 
                                                    <span class="badge bg-secondary">{{ number_format($registro->millas_totales, 2) }} millas</span>
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            @if($registro->entrada)
                                                <a href="{{ route('registros.show', $registro->entrada->id) }}" class="btn btn-info btn-sm mb-1" title="Ver Entrada">
                                                    <i class="fas fa-eye"></i> Entrada
                                                </a>
                                                <a href="{{ route('registros.edit', $registro->entrada->id) }}" class="btn btn-warning btn-sm mb-1" title="Editar Entrada">
                                                    <i class="fas fa-edit"></i> Entrada
                                                </a>
                                            @endif
                                            @if($registro->salida)
                                                <a href="{{ route('registros.show', $registro->salida->id) }}" class="btn btn-info btn-sm mb-1" title="Ver Salida">
                                                    <i class="fas fa-eye"></i> Salida
                                                </a>
                                                <a href="{{ route('registros.edit', $registro->salida->id) }}" class="btn btn-warning btn-sm mb-1" title="Editar Salida">
                                                    <i class="fas fa-edit"></i> Salida
                                                </a>
                                            @endif
                                            @if($registro->extras->count() > 0)
                                                @foreach($registro->extras as $extra)
                                                    <a href="{{ route('registros.show', $extra->id) }}" class="btn btn-secondary btn-sm mb-1" title="Ver Extra">
                                                        <i class="fas fa-eye"></i> Extra
                                                    </a>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay registros disponibles.</td>
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
