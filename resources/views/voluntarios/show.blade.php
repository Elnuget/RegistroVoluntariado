@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Detalles del Voluntario</h3>
                        <div>
                            <a href="{{ route('voluntarios.edit', $voluntario->id) }}" class="btn btn-warning">Editar</a>
                            <a href="{{ route('voluntarios.index') }}" class="btn btn-secondary">Volver</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $voluntario->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre Completo:</th>
                            <td>{{ $voluntario->nombre_completo }}</td>
                        </tr>
                        <tr>
                            <th>Dirección:</th>
                            <td>{{ $voluntario->direccion }}</td>
                        </tr>
                        <tr>
                            <th>E-mail:</th>
                            <td>{{ $voluntario->email }}</td>
                        </tr>
                        <tr>
                            <th>Número de Teléfono:</th>
                            <td>{{ $voluntario->telefono }}</td>
                        </tr>
                        <tr>
                            <th>Ocupación:</th>
                            <td>{{ $voluntario->ocupacion }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $voluntario->estado == 'activo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($voluntario->estado) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $voluntario->created_at ? $voluntario->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $voluntario->updated_at ? $voluntario->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    </table>

                    <form action="{{ route('voluntarios.destroy', $voluntario->id) }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este voluntario?')">Eliminar Voluntario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
