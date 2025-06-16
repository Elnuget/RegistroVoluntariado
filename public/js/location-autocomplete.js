/**
 * Location Autocomplete usando Google Places API
 * 
 * Para usar este archivo:
 * 1. Obtén una API key de Google Cloud Console
 * 2. Habilita la Places API
 * 3. Reemplaza 'TU_API_KEY_AQUI' con tu API key real
 * 4. Incluye este script en tu blade template
 */

class LocationAutocomplete {
    constructor() {
        this.apiKey = window.GOOGLE_MAPS_API_KEY || 'TU_API_KEY_AQUI'; // Usar la variable global
        this.service = null;
        this.initialized = false;
    }

    // Inicializar el servicio de Google Places
    async initialize() {
        if (this.initialized) return;
        
        try {
            // Cargar la librería de Google Maps si no está cargada
            if (typeof google === 'undefined') {
                await this.loadGoogleMapsAPI();
            }
            
            this.service = new google.maps.places.AutocompleteService();
            this.initialized = true;
            console.log('Google Places API inicializada correctamente');
        } catch (error) {
            console.error('Error al inicializar Google Places API:', error);
            // Fallback a modo simulado
            this.initialized = false;
        }
    }

    // Cargar la API de Google Maps dinámicamente
    loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (typeof google !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&libraries=places`;
            script.async = true;
            script.defer = true;
            
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Error al cargar Google Maps API'));
            
            document.head.appendChild(script);
        });
    }

    // Buscar ubicaciones usando Google Places API
    async searchLocations(query, callback) {
        if (!this.initialized) {
            await this.initialize();
        }

        if (!this.initialized || !this.service) {
            // Fallback a resultados simulados
            this.searchLocationsFallback(query, callback);
            return;
        }        const request = {
            input: query,
            types: ['geocode'], // Solo direcciones
            componentRestrictions: { 
                country: 'us',
                administrativeArea: 'MN' // Restringir a Minnesota
            },
            // Bias adicional hacia Minnesota
            locationBias: {
                center: { lat: 46.7296, lng: -94.6859 }, // Centro de Minnesota
                radius: 500000 // Radio en metros (500km)
            }
        };

        this.service.getPlacePredictions(request, (predictions, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                const results = predictions.map(prediction => ({
                    main: prediction.structured_formatting.main_text,
                    secondary: prediction.structured_formatting.secondary_text,
                    placeId: prediction.place_id,
                    fullDescription: prediction.description
                }));
                callback(results);
            } else {
                console.warn('Error en Places API:', status);
                this.searchLocationsFallback(query, callback);
            }
        });
    }    // Fallback con resultados simulados específicos de Minnesota
    searchLocationsFallback(query, callback) {
        const mockResults = [
            { main: query + ' Ave', secondary: 'Minneapolis, MN', placeId: 'mock1' },
            { main: query + ' St', secondary: 'Saint Paul, MN', placeId: 'mock2' },
            { main: query + ' Blvd', secondary: 'Duluth, MN', placeId: 'mock3' },
            { main: query + ' Dr', secondary: 'Rochester, MN', placeId: 'mock4' },
            { main: query + ' Rd', secondary: 'Bloomington, MN', placeId: 'mock5' }
        ];
        
        // Simular delay de red
        setTimeout(() => {
            callback(mockResults.slice(0, 4));
        }, 200);
    }

    // Obtener detalles de un lugar específico
    async getPlaceDetails(placeId, callback) {
        if (!this.initialized || !this.service) {
            callback(null);
            return;
        }

        const service = new google.maps.places.PlacesService(document.createElement('div'));
        
        service.getDetails({
            placeId: placeId,
            fields: ['formatted_address', 'geometry', 'name']
        }, (place, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                callback({
                    address: place.formatted_address,
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                    name: place.name
                });
            } else {
                callback(null);
            }
        });
    }
}

// Instancia global
const locationAutocomplete = new LocationAutocomplete();

// Función para configurar autocompletado en inputs
function setupGoogleLocationAutocomplete(inputId, dropdownId) {
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    let searchTimeout;
    let selectedIndex = -1;
    
    input.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 3) {
            hideDropdown();
            return;
        }
        
        showLoading();
        
        searchTimeout = setTimeout(() => {
            locationAutocomplete.searchLocations(query, (results) => {
                displayResults(results, dropdown);
            });
        }, 300);
    });
    
    input.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && items[selectedIndex]) {
                selectLocationItem(items[selectedIndex], input, dropdown);
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });
    
    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            hideDropdown();
        }
    });
    
    function showLoading() {
        dropdown.innerHTML = '<div class="autocomplete-loading">🔍 Buscando ubicaciones...</div>';
        dropdown.style.display = 'block';
    }
    
    function hideDropdown() {
        dropdown.style.display = 'none';
        selectedIndex = -1;
    }
    
    function updateSelection(items) {
        items.forEach((item, index) => {
            item.classList.toggle('selected', index === selectedIndex);
        });
    }
    
    function displayResults(results, dropdown) {
        if (results.length === 0) {
            dropdown.innerHTML = '<div class="autocomplete-loading">❌ No se encontraron ubicaciones</div>';
            return;
        }
        
        const html = results.map((result, index) => `
            <div class="autocomplete-item" data-index="${index}" data-place-id="${result.placeId || ''}">
                <div class="autocomplete-main">📍 ${result.main}</div>
                <div class="autocomplete-secondary">${result.secondary || ''}</div>
            </div>
        `).join('');
        
        dropdown.innerHTML = html;
        dropdown.style.display = 'block';
        
        // Agregar event listeners a los items
        dropdown.querySelectorAll('.autocomplete-item').forEach(item => {
            item.addEventListener('click', () => {
                selectLocationItem(item, input, dropdown);
            });
        });
    }
    
    function selectLocationItem(item, input, dropdown) {
        const mainText = item.querySelector('.autocomplete-main').textContent.replace('📍 ', '');
        const secondaryText = item.querySelector('.autocomplete-secondary').textContent;
        const placeId = item.dataset.placeId;
        
        // Mostrar la dirección completa
        if (secondaryText) {
            input.value = mainText + ', ' + secondaryText;
        } else {
            input.value = mainText;
        }
        
        hideDropdown();
        
        // Obtener detalles adicionales si hay placeId
        if (placeId && placeId !== '') {
            locationAutocomplete.getPlaceDetails(placeId, (details) => {
                if (details) {
                    console.log('Detalles del lugar:', details);
                    // Aquí puedes actualizar coordenadas, calcular distancia, etc.
                // Integración con el mapa
                if (typeof window.routeMap !== 'undefined' && window.routeMap) {
                    const inputElement = document.querySelector(`[data-place-id="${placeId}"]`).closest('.form-question').querySelector('input');
                    if (inputElement) {
                        const inputId = inputElement.id;
                        if (inputId === 'ubicacion_desde') {
                            window.routeMap.updateOrigin(details.address);
                        } else if (inputId === 'ubicacion_hasta') {
                            window.routeMap.updateDestination(details.address);
                        }
                    }
                }
                }
            });
        }
          // Trigger change event
        input.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Notificar al mapa si está disponible
        if (typeof window.routeMap !== 'undefined' && window.routeMap) {
            if (inputId === 'ubicacion_desde') {
                window.routeMap.updateOrigin(input.value);
            } else if (inputId === 'ubicacion_hasta') {
                window.routeMap.updateDestination(input.value);
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Configurar autocompletado para ambos inputs
    setupGoogleLocationAutocomplete('ubicacion_desde', 'dropdown_origen');
    setupGoogleLocationAutocomplete('ubicacion_hasta', 'dropdown_destino');
    
    console.log('Location Autocomplete inicializado');
});
