<div class="form-question">
    <label for="voluntario_id" class="question-label required">Voluntario</label>
    <div class="select-container">
        <input type="text" class="form-input" id="voluntario_search" 
               placeholder="Escriba para buscar o seleccione de la lista" autocomplete="off">
        <input type="hidden" id="voluntario_id" name="voluntario_id" required>
        <div class="dropdown-list" id="voluntario_dropdown">
            @foreach ($voluntarios as $voluntario)
                <div class="dropdown-item" data-value="{{ $voluntario->id }}">
                    {{ $voluntario->nombre_completo }}
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="form-question">
    <label for="tipo_actividad" class="question-label required">Tipo de Actividad</label>
    <select class="form-select" id="tipo_actividad" name="tipo_actividad" required>
        <option value="">Seleccione el tipo de actividad</option>
        <option value="Entrada" {{ old('tipo_actividad') == 'Entrada' ? 'selected' : '' }}>Entrada</option>
        <option value="Salida" {{ old('tipo_actividad') == 'Salida' ? 'selected' : '' }}>Salida</option>
        <option value="Extra" {{ old('tipo_actividad') == 'Extra' ? 'selected' : '' }}>Extra</option>
    </select>
</div>

<div class="form-question">
    <label for="actividad" class="question-label required">Actividad</label>
    <input type="text" class="form-input" id="actividad" name="actividad" 
           value="{{ old('actividad') }}" placeholder="Descripción de la actividad" required>
</div>
