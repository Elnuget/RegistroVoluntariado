<div class="form-question">
    <label for="tipo" class="question-label required">Tipo de Actividad</label>
    <input type="text" class="form-input" id="tipo" name="tipo" value="{{ old('tipo') }}" 
           placeholder="Ej: Transporte, Interpretación, Apoyo administrativo" required>
</div>

<div class="form-question">
    <label for="ubicacion_desde" class="question-label required">Ubicación de Origen</label>
    <input type="text" class="form-input" id="ubicacion_desde" name="ubicacion_desde" 
           value="{{ old('ubicacion_desde') }}" placeholder="Dirección de inicio" required>
</div>

<div class="form-question">
    <label for="ubicacion_hasta" class="question-label required">Ubicación de Destino</label>
    <input type="text" class="form-input" id="ubicacion_hasta" name="ubicacion_hasta" 
           value="{{ old('ubicacion_hasta') }}" placeholder="Dirección de destino" required>
</div>

<div class="form-question">
    <label for="millas" class="question-label required">Millas Recorridas</label>
    <input type="number" step="0.01" class="form-input" id="millas" name="millas" 
           value="{{ old('millas') }}" placeholder="0.00" required>
</div>
