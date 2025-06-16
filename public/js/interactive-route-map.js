/**
 * Mapa interactivo con rutas y cálculo de distancias
 * Integra con Google Maps API para mostrar rutas entre origen y destino
 */

class InteractiveRouteMap {
    constructor() {
        this.map = null;
        this.directionsService = null;
        this.directionsRenderer = null;
        this.originMarker = null;
        this.destinationMarker = null;
        this.geocoder = null;
        this.isInitialized = false;
        
        // Referencias a elementos del DOM
        this.mapElement = document.getElementById('map');
        this.loadingElement = document.getElementById('map-loading');
        this.routeInfoElement = document.getElementById('route-info');
        this.routeDistanceElement = document.getElementById('route-distance');
        this.routeDurationElement = document.getElementById('route-duration');
        this.millasInput = document.getElementById('millas');
        
        // Ubicaciones actuales
        this.currentOrigin = null;
        this.currentDestination = null;
    }    // Inicializar el mapa con mejor manejo de errores
    async initialize() {
        if (this.isInitialized) return;

        try {
            console.log('🗺️ Iniciando carga del mapa...');
            
            // Verificar que tenemos API key
            if (!window.GOOGLE_MAPS_API_KEY || window.GOOGLE_MAPS_API_KEY === 'TU_API_KEY_AQUI') {
                throw new Error('API key de Google Maps no configurada');
            }

            // Esperar a que Google Maps se cargue con timeout más largo
            await this.waitForGoogleMaps();
            
            console.log('✅ Google Maps API cargada');
            
            // Configurar el mapa centrado en Minnesota
            const minnesotaCenter = { lat: 46.7296, lng: -94.6859 };
            
            this.map = new google.maps.Map(this.mapElement, {
                zoom: 7,
                center: minnesotaCenter,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true,
                zoomControl: true,
                styles: this.getMapStyles()
            });

            console.log('✅ Mapa creado exitosamente');

            // Inicializar servicios de Google Maps
            this.directionsService = new google.maps.DirectionsService();
            this.directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: false, // Mostrar marcadores por defecto también
                polylineOptions: {
                    strokeColor: '#4285f4',
                    strokeWeight: 5,
                    strokeOpacity: 0.8
                }
            });
            
            this.directionsRenderer.setMap(this.map);
            this.geocoder = new google.maps.Geocoder();

            console.log('✅ Servicios de Google Maps inicializados');

            // Ocultar indicador de carga
            if (this.loadingElement) {
                this.loadingElement.style.display = 'none';
            }

            this.isInitialized = true;
            console.log('🎉 Mapa interactivo inicializado correctamente');
            
            // Procesar ubicaciones existentes si las hay
            setTimeout(() => {
                this.checkExistingLocations();
            }, 1000);
            
        } catch (error) {
            console.error('❌ Error al inicializar el mapa:', error);
            this.showMapError(error.message);
        }
    }    // Esperar a que Google Maps esté disponible con mejor manejo
    waitForGoogleMaps() {
        return new Promise((resolve, reject) => {
            let attempts = 0;
            const maxAttempts = 100; // 10 segundos máximo
            
            const checkGoogle = () => {
                attempts++;
                console.log(`🔍 Verificando Google Maps API... (intento ${attempts}/${maxAttempts})`);
                
                if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
                    console.log('✅ Google Maps API disponible');
                    resolve();
                } else if (attempts >= maxAttempts) {
                    console.error('❌ Timeout esperando Google Maps API');
                    reject(new Error('Timeout esperando Google Maps API. Verifica tu conexión a internet y la API key.'));
                } else {
                    setTimeout(checkGoogle, 100);
                }
            };
            
            // Intentar cargar la API si no está disponible
            if (typeof google === 'undefined') {
                console.log('📡 Cargando Google Maps API...');
                this.loadGoogleMapsAPIManually();
            }
            
            checkGoogle();
        });
    }

    // Cargar Google Maps API manualmente si no está disponible
    loadGoogleMapsAPIManually() {
        // Verificar si ya hay un script cargándose
        if (document.querySelector('script[src*="maps.googleapis.com"]')) {
            console.log('📡 Google Maps API ya se está cargando...');
            return;
        }

        const apiKey = window.GOOGLE_MAPS_API_KEY;
        if (!apiKey || apiKey === 'TU_API_KEY_AQUI') {
            console.error('❌ API key no configurada');
            return;
        }

        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places,geometry&callback=initGoogleMapsCallback`;
        script.async = true;
        script.defer = true;
        
        script.onload = () => {
            console.log('✅ Google Maps API cargada exitosamente');
        };
        
        script.onerror = (error) => {
            console.error('❌ Error cargando Google Maps API:', error);
        };
        
        document.head.appendChild(script);
        
        // Callback global para cuando se carga la API
        window.initGoogleMapsCallback = () => {
            console.log('🎉 Google Maps API inicializada via callback');
        };
    }

    // Estilos personalizados del mapa
    getMapStyles() {
        return [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            },
            {
                featureType: 'transit',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            }
        ];
    }

    // Actualizar ubicación de origen
    async updateOrigin(address) {
        if (!address || !this.isInitialized) return;
        
        try {
            const location = await this.geocodeAddress(address);
            if (location) {
                this.currentOrigin = { address, location };
                this.updateOriginMarker(location);
                this.updateRoute();
            }
        } catch (error) {
            console.error('Error al actualizar origen:', error);
        }
    }

    // Actualizar ubicación de destino
    async updateDestination(address) {
        if (!address || !this.isInitialized) return;
        
        try {
            const location = await this.geocodeAddress(address);
            if (location) {
                this.currentDestination = { address, location };
                this.updateDestinationMarker(location);
                this.updateRoute();
            }
        } catch (error) {
            console.error('Error al actualizar destino:', error);
        }
    }    // Geocodificar dirección con múltiples intentos y estrategias
    geocodeAddress(address) {
        return new Promise((resolve) => {
            console.log(`🔍 Geocodificando: ${address}`);
            
            // Lista de estrategias de búsqueda
            const searchStrategies = [
                // 1. Dirección original
                address,
                // 2. Dirección normalizada
                this.normalizeAddress(address),
                // 3. Solo dirección sin apartamento
                address.replace(/,?\s*(apt|apartment|unit|#)\s*\d+/gi, ''),
                // 4. Agregar Minnesota si no está
                address.includes('MN') ? address : `${address}, Minnesota`,
                // 5. Simplificada (solo número, calle y ciudad)
                this.simplifyAddress(address)
            ];
            
            this.tryGeocodeStrategies(searchStrategies, 0, resolve);
        });
    }

    // Intentar múltiples estrategias de geocodificación
    tryGeocodeStrategies(strategies, index, resolve) {
        if (index >= strategies.length) {
            console.error(`❌ Todas las estrategias de geocodificación fallaron`);
            resolve(null);
            return;
        }

        const currentStrategy = strategies[index];
        console.log(`🎯 Probando estrategia ${index + 1}/${strategies.length}: ${currentStrategy}`);

        this.geocoder.geocode(
            { 
                address: currentStrategy,
                componentRestrictions: { 
                    country: 'US',
                    administrativeArea: 'MN'
                },
                region: 'us'
            },
            (results, status) => {
                if (status === 'OK' && results && results[0]) {
                    console.log(`✅ Geocodificación exitosa (estrategia ${index + 1}): ${currentStrategy} → ${results[0].formatted_address}`);
                    resolve(results[0].geometry.location);
                } else {
                    console.warn(`⚠️ Estrategia ${index + 1} falló: ${status}`);
                    // Intentar siguiente estrategia
                    setTimeout(() => {
                        this.tryGeocodeStrategies(strategies, index + 1, resolve);
                    }, 200); // Pequeña pausa entre intentos
                }
            }
        );
    }

    // Simplificar dirección para búsqueda básica
    simplifyAddress(address) {
        // Extraer componentes básicos: número, calle, ciudad
        const parts = address.split(',');
        if (parts.length >= 2) {
            const streetPart = parts[0].trim();
            const cityPart = parts[1].trim();
            return `${streetPart}, ${cityPart}, MN`;
        }
        return address;
    }

    // Normalizar dirección para mejor geocodificación
    normalizeAddress(address) {
        if (!address) return address;
        
        let normalized = address;
        
        // Correcciones específicas para direcciones de voluntarios
        const corrections = {
            // Abreviaciones comunes
            'Apt ': 'Apartment ',
            'apt ': 'Apartment ',
            'St ': 'Street ',
            'St.': 'Street',
            'Ave ': 'Avenue ',
            'Ave.': 'Avenue',
            'Ct ': 'Court ',
            'Ct.': 'Court',
            'Dr ': 'Drive ',
            'Dr.': 'Drive',
            'Rd ': 'Road ',
            'Rd.': 'Road',
            'Blvd ': 'Boulevard ',
            'Blvd.': 'Boulevard',
            'Ln ': 'Lane ',
            'Ln.': 'Lane',
            
            // Correcciones de estado y ciudades
            'Mn': 'MN',
            'mn': 'MN',
            'Minnesota': 'MN',
            'Saint Paul': 'St Paul',
            'St. Paul': 'St Paul',
            
            // Direcciones específicas problemáticas de la DB
            'lower 57th': 'Lower 57th Street',
            'Rose Vista Ct': 'Rose Vista Court',
            'Gentry Ave. N': 'Gentry Avenue North',
            'Magnolia Ave E.': 'Magnolia Avenue East',
            'Chicago Avenue,': 'Chicago Avenue',
            'Clinton Ave S': 'Clinton Avenue South',
            'Powers Avenue': 'Powers Avenue',
            'Montgomery Ave': 'Montgomery Avenue',
            'Hamilton St': 'Hamilton Street',
            '73rd Ave N': '73rd Avenue North'
        };
        
        // Aplicar correcciones
        Object.keys(corrections).forEach(search => {
            const replace = corrections[search];
            normalized = normalized.replace(new RegExp(search, 'gi'), replace);
        });
        
        // Limpiar espacios extra y comas
        normalized = normalized.replace(/\s+/g, ' ').trim();
        normalized = normalized.replace(/,\s*,/g, ',');
        normalized = normalized.replace(/,\s*$/, '');
        
        return normalized;
    }

    // Actualizar marcador de origen
    updateOriginMarker(location) {
        if (this.originMarker) {
            this.originMarker.setMap(null);
        }

        this.originMarker = new google.maps.Marker({
            position: location,
            map: this.map,
            title: 'Origen',
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="12" fill="#34a853" stroke="white" stroke-width="3"/>
                        <text x="16" y="21" text-anchor="middle" fill="white" font-family="Arial" font-size="14" font-weight="bold">A</text>
                    </svg>
                `),
                scaledSize: new google.maps.Size(32, 32)
            }
        });
    }

    // Actualizar marcador de destino
    updateDestinationMarker(location) {
        if (this.destinationMarker) {
            this.destinationMarker.setMap(null);
        }

        this.destinationMarker = new google.maps.Marker({
            position: location,
            map: this.map,
            title: 'Destino',
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="12" fill="#ea4335" stroke="white" stroke-width="3"/>
                        <text x="16" y="21" text-anchor="middle" fill="white" font-family="Arial" font-size="14" font-weight="bold">B</text>
                    </svg>
                `),
                scaledSize: new google.maps.Size(32, 32)
            }
        });
    }

    // Actualizar ruta entre origen y destino
    async updateRoute() {
        if (!this.currentOrigin || !this.currentDestination || !this.isInitialized) {
            this.hideRouteInfo();
            return;
        }

        try {
            const request = {
                origin: this.currentOrigin.location,
                destination: this.currentDestination.location,
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.IMPERIAL // Usar millas
            };

            this.directionsService.route(request, (result, status) => {
                if (status === 'OK') {
                    this.directionsRenderer.setDirections(result);
                    this.displayRouteInfo(result);
                    this.updateMilesInput(result);
                    this.fitMapToBounds(result);
                } else {
                    console.error('Error calculando ruta:', status);
                    this.hideRouteInfo();
                }
            });
        } catch (error) {
            console.error('Error al calcular ruta:', error);
        }
    }

    // Mostrar información de la ruta
    displayRouteInfo(directionsResult) {
        const route = directionsResult.routes[0];
        if (!route) return;

        const leg = route.legs[0];
        const distance = leg.distance.text;
        const duration = leg.duration.text;

        if (this.routeDistanceElement) {
            this.routeDistanceElement.textContent = `📏 ${distance}`;
        }
        
        if (this.routeDurationElement) {
            this.routeDurationElement.textContent = `⏱️ ${duration}`;
        }
        
        if (this.routeInfoElement) {
            this.routeInfoElement.style.display = 'block';
        }
    }

    // Actualizar el campo de millas automáticamente
    updateMilesInput(directionsResult) {
        const route = directionsResult.routes[0];
        if (!route || !this.millasInput) return;

        const leg = route.legs[0];
        const distanceInMiles = leg.distance.value * 0.000621371; // Convertir metros a millas
        
        this.millasInput.value = distanceInMiles.toFixed(2);
        
        // Trigger change event para validaciones
        this.millasInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Ajustar el mapa para mostrar toda la ruta
    fitMapToBounds(directionsResult) {
        const bounds = new google.maps.LatLngBounds();
        const route = directionsResult.routes[0];
        
        if (route) {
            bounds.extend(route.legs[0].start_location);
            bounds.extend(route.legs[0].end_location);
            this.map.fitBounds(bounds, { padding: 50 });
        }
    }

    // Ocultar información de ruta
    hideRouteInfo() {
        if (this.routeInfoElement) {
            this.routeInfoElement.style.display = 'none';
        }
    }

    // Verificar ubicaciones existentes en los inputs
    checkExistingLocations() {
        const origenInput = document.getElementById('ubicacion_desde');
        const destinoInput = document.getElementById('ubicacion_hasta');
        
        if (origenInput && origenInput.value.trim()) {
            this.updateOrigin(origenInput.value.trim());
        }
        
        if (destinoInput && destinoInput.value.trim()) {
            this.updateDestination(destinoInput.value.trim());
        }
    }    // Mostrar error del mapa con información detallada
    showMapError(errorMessage = 'Error desconocido') {
        if (this.loadingElement) {
            let errorHtml = `
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 48px; margin-bottom: 8px;">⚠️</div>
                    <div style="color: #d93025; font-weight: 500; margin-bottom: 8px;">Error cargando mapa</div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 12px;">${errorMessage}</div>
            `;
            
            // Agregar botón de retry
            errorHtml += `
                    <button onclick="window.routeMap.retryInitialization()" 
                            style="background: #1a73e8; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        🔄 Reintentar
                    </button>
                </div>
            `;
            
            this.loadingElement.innerHTML = errorHtml;
        }
    }

    // Método para reintentar inicialización
    async retryInitialization() {
        console.log('🔄 Reintentando inicialización del mapa...');
        
        if (this.loadingElement) {
            this.loadingElement.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 8px;">🗺️</div>
                    <div>Reintentando carga del mapa...</div>
                </div>
            `;
        }
        
        // Reset estado
        this.isInitialized = false;
        this.map = null;
        
        // Reintentar inicialización
        setTimeout(() => {
            this.initialize();
        }, 1000);
    }

    // Limpiar mapa
    clearMap() {
        if (this.originMarker) {
            this.originMarker.setMap(null);
            this.originMarker = null;
        }
        
        if (this.destinationMarker) {
            this.destinationMarker.setMap(null);
            this.destinationMarker = null;
        }
        
        if (this.directionsRenderer) {
            this.directionsRenderer.setDirections({ routes: [] });
        }
        
        this.currentOrigin = null;
        this.currentDestination = null;
        this.hideRouteInfo();
        
        if (this.millasInput) {
            this.millasInput.value = '';
        }
    }
}

// Instancia global del mapa
let routeMap = null;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia del mapa
    routeMap = new InteractiveRouteMap();
    
    // Inicializar después de un pequeño delay para asegurar que todo esté cargado
    setTimeout(() => {
        routeMap.initialize();
    }, 500);
    
    // Escuchar cambios en los inputs de ubicación
    const origenInput = document.getElementById('ubicacion_desde');
    const destinoInput = document.getElementById('ubicacion_hasta');
    
    if (origenInput) {
        origenInput.addEventListener('change', function() {
            if (routeMap && this.value.trim()) {
                routeMap.updateOrigin(this.value.trim());
            }
        });
    }
    
    if (destinoInput) {
        destinoInput.addEventListener('change', function() {
            if (routeMap && this.value.trim()) {
                routeMap.updateDestination(this.value.trim());
            }
        });
    }
    
    console.log('Sistema de mapas y rutas inicializado');
    // Exponer mapa a nivel global para permitir actualizaciones automáticas
    window.routeMap = routeMap;
});

// Hacer disponible globalmente para debugging
window.routeMap = routeMap;
