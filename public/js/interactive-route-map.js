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
    }

    // Inicializar el mapa
    async initialize() {
        if (this.isInitialized) return;

        try {
            // Esperar a que Google Maps se cargue
            await this.waitForGoogleMaps();
            
            // Configurar el mapa centrado en Minnesota
            const minnesotaCenter = { lat: 46.7296, lng: -94.6859 };
            
            this.map = new google.maps.Map(this.mapElement, {
                zoom: 7,
                center: minnesotaCenter,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                styles: this.getMapStyles()
            });

            // Inicializar servicios de Google Maps
            this.directionsService = new google.maps.DirectionsService();
            this.directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true, // Usaremos marcadores personalizados
                polylineOptions: {
                    strokeColor: '#4285f4',
                    strokeWeight: 4,
                    strokeOpacity: 0.8
                }
            });
            this.directionsRenderer.setMap(this.map);
            
            this.geocoder = new google.maps.Geocoder();

            // Ocultar indicador de carga
            if (this.loadingElement) {
                this.loadingElement.style.display = 'none';
            }

            this.isInitialized = true;
            console.log('Mapa interactivo inicializado correctamente');
            
            // Procesar ubicaciones existentes si las hay
            this.checkExistingLocations();
            
        } catch (error) {
            console.error('Error al inicializar el mapa:', error);
            this.showMapError();
        }
    }

    // Esperar a que Google Maps esté disponible
    waitForGoogleMaps() {
        return new Promise((resolve, reject) => {
            const checkGoogle = () => {
                if (typeof google !== 'undefined' && google.maps) {
                    resolve();
                } else {
                    setTimeout(checkGoogle, 100);
                }
            };
            checkGoogle();
            
            // Timeout después de 10 segundos
            setTimeout(() => reject(new Error('Timeout esperando Google Maps')), 10000);
        });
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
    }

    // Geocodificar dirección
    geocodeAddress(address) {
        return new Promise((resolve, reject) => {
            this.geocoder.geocode(
                { 
                    address: address,
                    componentRestrictions: { 
                        country: 'US',
                        administrativeArea: 'MN'
                    }
                },
                (results, status) => {
                    if (status === 'OK' && results[0]) {
                        resolve(results[0].geometry.location);
                    } else {
                        console.warn('Geocoding falló:', status);
                        resolve(null);
                    }
                }
            );
        });
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
    }

    // Mostrar error del mapa
    showMapError() {
        if (this.loadingElement) {
            this.loadingElement.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 8px;">⚠️</div>
                    <div>Error cargando mapa</div>
                    <div style="font-size: 12px; color: #666; margin-top: 4px;">Verifica tu conexión</div>
                </div>
            `;
        }
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
});

// Hacer disponible globalmente para debugging
window.routeMap = routeMap;
