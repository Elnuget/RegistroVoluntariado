@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Lista de Voluntarios</h3>
                        <a href="{{ route('voluntarios.create') }}" class="btn btn-primary">Registrar Nuevo Voluntario</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-striped">                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Dirección</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Ocupación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($voluntarios as $voluntario)                                <tr>
                                    <td>{{ $voluntario->id }}</td>
                                    <td>{{ $voluntario->nombre_completo }}</td>
                                    <td>{{ $voluntario->direccion }}</td>
                                    <td>{{ $voluntario->email }}</td>
                                    <td>{{ $voluntario->telefono }}</td>
                                    <td>{{ $voluntario->ocupacion }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('voluntarios.show', $voluntario->id) }}" class="btn btn-info btn-sm">Ver</a>
                                            <a href="{{ route('voluntarios.edit', $voluntario->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                            <form action="{{ route('voluntarios.destroy', $voluntario->id) }}" method="POST" style="display: inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este voluntario?')">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay voluntarios registrados.</td>
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
