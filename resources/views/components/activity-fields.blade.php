<!-- Contenedor principal con dos columnas -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
    <!-- Columna izquierda: Inputs de ubicación -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="form-question" style="margin-bottom: 0; position: relative;">
            <label for="ubicacion_desde" class="question-label required">Ubicación de Origen</label>
            <input type="text" class="form-input autocomplete-input" id="ubicacion_desde" name="ubicacion_desde" 
                   value="{{ old('ubicacion_desde') }}" placeholder="Buscar dirección de inicio..." required
                   autocomplete="off">
            <div class="autocomplete-dropdown" id="dropdown_origen"></div>
            <div class="direccion-info" id="direccion_origen_info" style="font-size: 12px; color: #5f6368; margin-top: 4px;"></div>
        </div>

        <div class="form-question" style="margin-bottom: 0; position: relative;">
            <label for="ubicacion_hasta" class="question-label required">Ubicación de Destino</label>
            <input type="text" class="form-input autocomplete-input" id="ubicacion_hasta" name="ubicacion_hasta" 
                   value="{{ old('ubicacion_hasta') }}" placeholder="Buscar dirección de destino..." required
                   autocomplete="off">
            <div class="autocomplete-dropdown" id="dropdown_destino"></div>
            <div class="direccion-info" id="direccion_destino_info" style="font-size: 12px; color: #5f6368; margin-top: 4px;"></div>
        </div>
    </div>

    <!-- Columna derecha: Mapa interactivo -->
    <div style="height: 300px; border: 2px solid #dadce0; border-radius: 8px; background-color: #f8f9fa; position: relative;">
        <div id="map" style="width: 100%; height: 100%; border-radius: 6px;">
            <!-- El mapa se cargará aquí -->
        </div>
        
        <!-- Indicador de carga del mapa -->
        <div id="map-loading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; align-items: center; justify-content: center; flex-direction: column; color: #5f6368; font-size: 14px;">
            <div style="font-size: 48px; margin-bottom: 8px;">🗺️</div>
            <div>Cargando mapa...</div>
        </div>
        
        <!-- Información de la ruta -->
        <div id="route-info" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.95); padding: 8px 12px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); font-size: 12px; color: #202124; display: none;">
            <div id="route-distance" style="font-weight: 500;"></div>
            <div id="route-duration" style="color: #5f6368;"></div>
        </div>
    </div>
</div>

<div class="form-question">
    <label for="millas" class="question-label">Millas Recorridas</label>
    <input type="number" step="0.01" class="form-input" id="millas" name="millas" 
           value="{{ old('millas') }}" placeholder="0.00">
</div>

<!-- Estilos CSS para el autocompletado -->
<style>
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dadce0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: none;
}

.autocomplete-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f4;
    transition: background-color 0.2s;
}

.autocomplete-item:hover, .autocomplete-item.selected {
    background-color: #f8f9fa;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-main {
    font-weight: 500;
    color: #202124;
    margin-bottom: 2px;
}

.autocomplete-secondary {
    font-size: 12px;
    color: #5f6368;
}

.autocomplete-loading {
    padding: 12px 16px;
    text-align: center;
    color: #5f6368;
    font-size: 14px;
}
</style>

<!-- JavaScript básico para compatibilidad -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Si no se carga el script principal, usar funcionalidad básica
    if (typeof locationAutocomplete === 'undefined') {
        console.log('Usando funcionalidad de autocompletado básica');
        setupBasicLocationAutocomplete('ubicacion_desde', 'dropdown_origen');
        setupBasicLocationAutocomplete('ubicacion_hasta', 'dropdown_destino');
    }
    
    function setupBasicLocationAutocomplete(inputId, dropdownId) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        let searchTimeout;
        
        input.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 3) {
                dropdown.style.display = 'none';
                return;
            }
            
            dropdown.innerHTML = '<div class="autocomplete-loading">🔍 Buscando ubicaciones...</div>';
            dropdown.style.display = 'block';
            
            searchTimeout = setTimeout(() => {
                // Generar direcciones más realistas para Minnesota
                const ciudades = [
                    { nombre: 'Minneapolis', zip: '55401' },
                    { nombre: 'Saint Paul', zip: '55102' },
                    { nombre: 'Roseville', zip: '55113' },
                    { nombre: 'Oakdale', zip: '55128' }
                ];
                
                const mockResults = ciudades.map((ciudad, index) => {
                    const numero = Math.floor(Math.random() * 9000) + 1000;
                    const tipos = ['Avenue', 'Street', 'Court', 'Drive'];
                    const tipo = tipos[index];
                    
                    return {
                        main: `${numero} ${query} ${tipo}`,
                        secondary: `${ciudad.nombre}, MN ${ciudad.zip}, USA`
                    };
                });
                
                const html = mockResults.map(result => `
                    <div class="autocomplete-item" onclick="selectBasicLocation('${result.main}', '${result.secondary}', '${inputId}', '${dropdownId}')">
                        <div class="autocomplete-main">📍 ${result.main}</div>
                        <div class="autocomplete-secondary">${result.secondary}</div>
                    </div>
                `).join('');
                
                dropdown.innerHTML = html;
            }, 300);
        });
        
        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }
    
    // Función global para selección básica
    window.selectBasicLocation = function(main, secondary, inputId, dropdownId) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        
        input.value = main + ', ' + secondary;
        dropdown.style.display = 'none';
        input.dispatchEvent(new Event('change'));
        
        // Integrar con el mapa si está disponible
        if (typeof window.routeMap !== 'undefined' && window.routeMap) {
            if (inputId === 'ubicacion_desde') {
                window.routeMap.updateOrigin(input.value);
            } else if (inputId === 'ubicacion_hasta') {
                window.routeMap.updateDestination(input.value);
            }
        }
    };
});
</script>
