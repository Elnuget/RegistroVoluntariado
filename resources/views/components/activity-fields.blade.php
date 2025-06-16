<!-- Contenedor principal con dos columnas -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
    <!-- Columna izquierda: Inputs de ubicación -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="form-question" style="margin-bottom: 0;">
            <label for="ubicacion_desde" class="question-label required">Ubicación de Origen</label>
            <input type="text" class="form-input" id="ubicacion_desde" name="ubicacion_desde" 
                   value="{{ old('ubicacion_desde') }}" placeholder="Dirección de inicio" required>
            <div class="direccion-info" id="direccion_origen_info" style="font-size: 12px; color: #5f6368; margin-top: 4px;"></div>
        </div>

        <div class="form-question" style="margin-bottom: 0;">
            <label for="ubicacion_hasta" class="question-label required">Ubicación de Destino</label>
            <input type="text" class="form-input" id="ubicacion_hasta" name="ubicacion_hasta" 
                   value="{{ old('ubicacion_hasta') }}" placeholder="Dirección de destino" required>
            <div class="direccion-info" id="direccion_destino_info" style="font-size: 12px; color: #5f6368; margin-top: 4px;"></div>
        </div>
    </div>

    <!-- Columna derecha: Mapa -->
    <div style="height: 300px; border: 2px solid #dadce0; border-radius: 8px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
        <div id="map" style="width: 100%; height: 100%; border-radius: 6px;">
            <!-- Aquí se mostrará el mapa -->
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #5f6368; font-size: 14px;">
                <div style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 8px;">🗺️</div>
                    <div>Mapa se cargará aquí</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-question">
    <label for="millas" class="question-label">Millas Recorridas</label>
    <input type="number" step="0.01" class="form-input" id="millas" name="millas" 
           value="{{ old('millas') }}" placeholder="0.00">
</div>
