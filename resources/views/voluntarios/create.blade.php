@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Registrar Nuevo Voluntario</h3>
                        <a href="{{ route('voluntarios.index') }}" class="btn btn-secondary">Volver</a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('voluntarios.store') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="nombre_completo">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="telefono">Número de Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="ocupacion">Ocupación</label>
                            <input type="text" class="form-control" id="ocupacion" name="ocupacion" value="{{ old('ocupacion') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Registrar Voluntario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
