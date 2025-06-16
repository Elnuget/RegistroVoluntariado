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
            types: ['street_address', 'route', 'intersection', 'political'], // Enfocado en direcciones específicas
            componentRestrictions: { 
                country: 'us',
                administrativeArea: 'MN' // Restringir a Minnesota
            },
            // Bias adicional hacia Minnesota con mayor precisión
            locationBias: {
                center: { lat: 46.7296, lng: -94.6859 }, // Centro de Minnesota
                radius: 200000 // Radio más específico (200km)
            },
            // Configuración adicional para más detalles
            fields: ['formatted_address', 'geometry', 'name', 'place_id', 'types', 'address_components'],
            sessionToken: new google.maps.places.AutocompleteSessionToken(),
            // Configuración adicional para direcciones específicas
            strictbounds: false, // No restringir estrictamente, pero dar preferencia
            region: 'us' // Región de Estados Unidos
        };this.service.getPlacePredictions(request, (predictions, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                const results = predictions.map(prediction => {
                    // Extraer información más detallada
                    const mainText = prediction.structured_formatting.main_text;
                    const secondaryText = prediction.structured_formatting.secondary_text;
                    
                    // Crear descripción más completa
                    let fullDescription = prediction.description;
                    
                    // Si es un negocio, incluir el tipo
                    let businessType = '';
                    if (prediction.types && prediction.types.length > 0) {
                        const relevantTypes = prediction.types.filter(type => 
                            !['establishment', 'point_of_interest', 'geocode'].includes(type)
                        );
                        if (relevantTypes.length > 0) {
                            businessType = this.formatBusinessType(relevantTypes[0]);
                        }
                    }
                    
                    return {
                        main: mainText,
                        secondary: secondaryText,
                        businessType: businessType,
                        placeId: prediction.place_id,
                        fullDescription: fullDescription,
                        types: prediction.types
                    };
                });
                callback(results);
            } else {
                console.warn('Error en Places API:', status);
                this.searchLocationsFallback(query, callback);
            }
        });
    }    // Fallback con resultados simulados basados en direcciones reales de Minnesota
    searchLocationsFallback(query, callback) {
        // Crear direcciones más realistas basadas en las que tienes en la DB
        const ciudadesMinnesota = [
            { ciudad: 'Minneapolis', zip: '55401', condado: 'Hennepin County' },
            { ciudad: 'Saint Paul', zip: '55102', condado: 'Ramsey County' },
            { ciudad: 'Roseville', zip: '55113', condado: 'Ramsey County' },
            { ciudad: 'Oakdale', zip: '55128', condado: 'Washington County' },
            { ciudad: 'Eagan', zip: '55123', condado: 'Dakota County' },
            { ciudad: 'Burnsville', zip: '55337', condado: 'Dakota County' },
            { ciudad: 'Andover', zip: '55304', condado: 'Anoka County' },
            { ciudad: 'Le Center', zip: '56057', condado: 'Le Sueur County' }
        ];
        
        const tiposVia = ['Avenue', 'Street', 'Road', 'Drive', 'Court', 'Lane', 'Boulevard'];
        
        const mockResults = ciudadesMinnesota.slice(0, 4).map((lugar, index) => {
            const numeroVivienda = Math.floor(Math.random() * 9000) + 1000; // 1000-9999
            const tipoVia = tiposVia[index % tiposVia.length];
            const direccionCompleta = `${numeroVivienda} ${query} ${tipoVia}, ${lugar.ciudad}, MN ${lugar.zip}, USA`;
            
            return {
                main: `${numeroVivienda} ${query} ${tipoVia}`,
                secondary: `${lugar.ciudad}, MN ${lugar.zip}, USA`,
                businessType: '',
                placeId: `mock${index + 1}`,
                fullDescription: direccionCompleta,
                types: ['street_address']
            };
        });
        
        // Simular delay de red
        setTimeout(() => {
            callback(mockResults);
        }, 200);
    }// Obtener detalles de un lugar específico con información completa
    async getPlaceDetails(placeId, callback) {
        if (!this.initialized || !this.service) {
            callback(null);
            return;
        }

        const service = new google.maps.places.PlacesService(document.createElement('div'));
        
        service.getDetails({
            placeId: placeId,
            fields: [
                'formatted_address', 
                'geometry', 
                'name', 
                'vicinity',
                'address_components',
                'types',
                'business_status'
            ]
        }, (place, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                // Crear dirección más detallada
                let detailedAddress = place.formatted_address;
                
                // Si es un negocio con nombre, incluirlo
                if (place.name && place.name !== detailedAddress) {
                    // Verificar si el nombre ya está en la dirección
                    if (!detailedAddress.toLowerCase().includes(place.name.toLowerCase())) {
                        detailedAddress = `${place.name}, ${detailedAddress}`;
                    }
                }
                
                callback({
                    address: detailedAddress,
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                    name: place.name,
                    vicinity: place.vicinity,
                    types: place.types,
                    businessStatus: place.business_status
                });
            } else {
                console.warn('Error obteniendo detalles del lugar:', status);
                callback(null);
            }
        });
    }

    // Formatear tipos de negocios para mejor legibilidad
    formatBusinessType(type) {
        const typeMap = {
            'restaurant': '🍴 Restaurante',
            'gas_station': '⛽ Gasolinera',
            'hospital': '🏥 Hospital',
            'school': '🏫 Escuela',
            'university': '🎓 Universidad',
            'shopping_mall': '🛍️ Centro Comercial',
            'store': '🏪 Tienda',
            'bank': '🏦 Banco',
            'pharmacy': '💊 Farmacia',
            'gym': '💪 Gimnasio',
            'park': '🌳 Parque',
            'church': '⛪ Iglesia',
            'library': '📚 Biblioteca',
            'post_office': '📮 Oficina Postal',
            'police': '👮 Policía',
            'fire_station': '🚒 Bomberos',
            'airport': '✈️ Aeropuerto',
            'subway_station': '🚊 Estación Metro',
            'bus_station': '🚌 Estación Autobús',
            'lodging': '🏨 Hotel'
        };
        
        return typeMap[type] || '';
    }

    // Normalizar dirección para mejor búsqueda
    normalizeAddress(address) {
        if (!address) return address;
        
        let normalized = address;
        
        // Correcciones comunes para direcciones de Minnesota
        const corrections = {
            // Abreviaciones comunes
            'Apt': 'Apartment',
            'apt': 'Apartment', 
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
            
            // Correcciones de ciudades de Minnesota
            'Mn': 'MN',
            'mn': 'MN',
            'Minnesota': 'MN',
            'Saint Paul': 'St Paul',
            'St. Paul': 'St Paul',
            
            // Direcciones específicas problemáticas
            'lower 57th': 'Lower 57th Street',
            'Rose Vista Ct': 'Rose Vista Court',
            'Gentry Ave. N': 'Gentry Avenue North',
            'Magnolia Ave E.': 'Magnolia Avenue East',
            'Chicago Avenue,': 'Chicago Avenue',
            'Clinton Ave S': 'Clinton Avenue South',
            'Powers Avenue': 'Powers Avenue',
            'Montgomery Ave': 'Montgomery Avenue'
        };
        
        // Aplicar correcciones
        Object.keys(corrections).forEach(search => {
            const replace = corrections[search];
            normalized = normalized.replace(new RegExp(search, 'gi'), replace);
        });
        
        // Limpiar espacios extra y comas mal ubicadas
        normalized = normalized.replace(/\s+/g, ' ').trim();
        normalized = normalized.replace(/,\s*,/g, ',');
normalized = normalized.replace(/,\s*$/, '');
        
        return normalized;
    }

    // ...existing code...
}
