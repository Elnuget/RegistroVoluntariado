<div class="form-question">
    <label for="ubicacion_desde" class="question-label required">Ubicación de Origen</label>
    <input type="text" class="form-input" id="ubicacion_desde" name="ubicacion_desde" 
           value="{{ old('ubicacion_desde') }}" placeholder="Dirección de inicio" required>
    <div class="direccion-info" id="direccion_origen_info" style="font-size: 12px; color: #5f6368; margin-top: 4px;"></div>
</div>

<div class="form-question">
    <label for="ubicacion_hasta" class="question-label required">Ubicación de Destino</label>
    <input type="text" class="form-input" id="ubicacion_hasta" name="ubicacion_hasta" 
           value="{{ old('ubicacion_hasta') }}" placeholder="Dirección de destino" required>
    <div class="direccion-info" id="direccion_destino_info" style="font-size: 12px; color: #5f6368; margin-top: 4px;"></div>
</div>

<div class="form-question">
    <label for="millas" class="question-label">Millas Recorridas</label>
    <input type="number" step="0.01" class="form-input" id="millas" name="millas" 
           value="{{ old('millas') }}" placeholder="0.00">
</div>
