@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Editar Registro</h3>
                        <a href="{{ route('registros.index') }}" class="btn btn-secondary">Volver</a>
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

                    <form method="POST" action="{{ route('registros.update', $registro->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="voluntario_id">Voluntario</label>
                            <select class="form-control" id="voluntario_id" name="voluntario_id" required>
                                <option value="">Seleccione un voluntario</option>
                                @foreach ($voluntarios as $voluntario)
                                    <option value="{{ $voluntario->id }}" {{ old('voluntario_id', $registro->voluntario_id) == $voluntario->id ? 'selected' : '' }}>
                                        {{ $voluntario->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tipo_actividad">Tipo de Actividad</label>
                            <select class="form-control" id="tipo_actividad" name="tipo_actividad" required>
                                <option value="">Seleccione el tipo de actividad</option>
                                <option value="Entrada" {{ old('tipo_actividad', $registro->tipo_actividad) == 'Entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="Salida" {{ old('tipo_actividad', $registro->tipo_actividad) == 'Salida' ? 'selected' : '' }}>Salida</option>
                                <option value="Extra" {{ old('tipo_actividad', $registro->tipo_actividad) == 'Extra' ? 'selected' : '' }}>Extra</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="fecha">Fecha (MM/DD/YYYY)</label>
                            <input type="text" class="form-control" id="fecha" name="fecha" 
                                   value="{{ old('fecha', $registro->fecha ? $registro->fecha->format('m/d/Y') : '') }}" 
                                   placeholder="MM/DD/YYYY" 
                                   pattern="^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/\d{4}$"
                                   title="Formato: MM/DD/YYYY"
                                   required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="hora">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora" 
                                   value="{{ old('hora', $registro->hora_input) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="ubicacion_desde">Ubicación Desde</label>
                            <input type="text" class="form-control" id="ubicacion_desde" name="ubicacion_desde" value="{{ old('ubicacion_desde', $registro->ubicacion_desde) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="ubicacion_hasta">Ubicación Hasta</label>
                            <input type="text" class="form-control" id="ubicacion_hasta" name="ubicacion_hasta" value="{{ old('ubicacion_hasta', $registro->ubicacion_hasta) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="millas">Millas</label>
                            <input type="number" step="0.01" class="form-control" id="millas" name="millas" value="{{ old('millas', $registro->millas) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Actualizar Registro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    
    fechaInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Solo números
        
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2);
        }
        if (value.length >= 5) {
            value = value.substring(0, 5) + '/' + value.substring(5, 9);
        }
        
        e.target.value = value;
    });
    
    fechaInput.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[0-9\/]/.test(char)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
