/**
 * Script de diagnóstico para Google Maps API
 * Ayuda a identificar problemas de configuración y conectividad
 */

class GoogleMapsDebugger {
    constructor() {
        this.apiKey = window.GOOGLE_MAPS_API_KEY;
        this.tests = [];
    }

    async runDiagnostics() {
        console.log('🔍 INICIANDO DIAGNÓSTICOS DE GOOGLE MAPS API');
        console.log('================================================');
        
        this.checkAPIKey();
        this.checkInternetConnection();
        await this.checkAPIStatus();
        this.checkDOMElements();
        await this.testGeocodingService();
        
        this.showSummary();
    }

    checkAPIKey() {
        console.log('1️⃣ Verificando API Key...');
        
        if (!this.apiKey) {
            this.addTest('API Key', '❌ No configurada', 'Verifica el archivo .env');
            return;
        }
        
        if (this.apiKey === 'TU_API_KEY_AQUI') {
            this.addTest('API Key', '❌ No reemplazada', 'Reemplaza TU_API_KEY_AQUI con tu clave real');
            return;
        }
        
        if (this.apiKey.length < 30) {
            this.addTest('API Key', '⚠️ Parece incorrecta', 'Las API keys suelen ser más largas');
            return;
        }
        
        this.addTest('API Key', '✅ Configurada', `Longitud: ${this.apiKey.length} caracteres`);
    }

    checkInternetConnection() {
        console.log('2️⃣ Verificando conectividad...');
        
        if (navigator.onLine) {
            this.addTest('Conexión', '✅ Online', 'Navegador reporta conexión activa');
        } else {
            this.addTest('Conexión', '❌ Offline', 'Sin conexión a internet');
        }
    }

    async checkAPIStatus() {
        console.log('3️⃣ Verificando estado de la API...');
        
        try {
            // Intentar cargar un script de prueba desde Google
            const testUrl = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&libraries=places`;
            const response = await fetch(testUrl, { method: 'HEAD', mode: 'no-cors' });
            this.addTest('API Endpoint', '✅ Accesible', 'Servidor de Google Maps responde');
        } catch (error) {
            this.addTest('API Endpoint', '❌ No accesible', error.message);
        }
        
        // Verificar si Google Maps ya está cargado
        if (typeof google !== 'undefined' && google.maps) {
            this.addTest('Google Maps', '✅ Cargado', 'API disponible en el navegador');
        } else {
            this.addTest('Google Maps', '❌ No cargado', 'API no disponible');
        }
    }

    checkDOMElements() {
        console.log('4️⃣ Verificando elementos del DOM...');
        
        const mapElement = document.getElementById('map');
        if (mapElement) {
            const rect = mapElement.getBoundingClientRect();
            this.addTest('Elemento Mapa', '✅ Encontrado', `Tamaño: ${rect.width}x${rect.height}px`);
        } else {
            this.addTest('Elemento Mapa', '❌ No encontrado', 'Elemento #map no existe');
        }

        const origenInput = document.getElementById('ubicacion_desde');
        const destinoInput = document.getElementById('ubicacion_hasta');
        
        if (origenInput && destinoInput) {
            this.addTest('Campos Ubicación', '✅ Encontrados', 'Inputs de origen y destino existen');
        } else {
            this.addTest('Campos Ubicación', '❌ Faltantes', 'Algunos inputs no encontrados');
        }
    }

    async testGeocodingService() {
        console.log('5️⃣ Probando servicio de geocodificación...');
        
        if (typeof google === 'undefined' || !google.maps) {
            this.addTest('Geocodificación', '❌ No disponible', 'Google Maps no cargado');
            return;
        }

        try {
            const geocoder = new google.maps.Geocoder();
            const testAddress = '1233 Rose Vista Ct, Roseville, MN';
            
            const result = await new Promise((resolve, reject) => {
                geocoder.geocode({ address: testAddress }, (results, status) => {
                    if (status === 'OK') {
                        resolve(results[0]);
                    } else {
                        reject(new Error(status));
                    }
                });
            });

            this.addTest('Geocodificación', '✅ Funcional', `Probado con: ${testAddress}`);
            console.log('📍 Resultado:', result.formatted_address);
            
        } catch (error) {
            this.addTest('Geocodificación', '❌ Error', error.message);
        }
    }

    addTest(category, status, details) {
        this.tests.push({ category, status, details });
        console.log(`   ${status} ${category}: ${details}`);
    }

    showSummary() {
        console.log('\n📊 RESUMEN DE DIAGNÓSTICOS');
        console.log('========================');
        
        const passed = this.tests.filter(t => t.status.includes('✅')).length;
        const warnings = this.tests.filter(t => t.status.includes('⚠️')).length;
        const failed = this.tests.filter(t => t.status.includes('❌')).length;
        
        console.log(`✅ Exitosos: ${passed}`);
        console.log(`⚠️ Advertencias: ${warnings}`);
        console.log(`❌ Fallidos: ${failed}`);
        
        if (failed > 0) {
            console.log('\n🔧 ACCIONES RECOMENDADAS:');
            this.tests.filter(t => t.status.includes('❌')).forEach(test => {
                console.log(`• ${test.category}: ${test.details}`);
            });
        }
        
        if (failed === 0 && warnings === 0) {
            console.log('\n🎉 ¡Todo parece estar configurado correctamente!');
        }
    }

    // Método para probar una dirección específica
    async testAddress(address) {
        console.log(`🧪 Probando dirección: ${address}`);
        
        if (typeof google === 'undefined' || !google.maps) {
            console.error('❌ Google Maps no disponible');
            return null;
        }

        const geocoder = new google.maps.Geocoder();
        
        try {
            const result = await new Promise((resolve, reject) => {
                geocoder.geocode({ 
                    address: address,
                    componentRestrictions: { country: 'US', administrativeArea: 'MN' }
                }, (results, status) => {
                    if (status === 'OK') {
                        resolve(results[0]);
                    } else {
                        reject(new Error(status));
                    }
                });
            });

            console.log('✅ Dirección encontrada:', result.formatted_address);
            console.log('📍 Coordenadas:', result.geometry.location.lat(), result.geometry.location.lng());
            return result;
            
        } catch (error) {
            console.error('❌ Error:', error.message);
            return null;
        }
    }
}

// Crear instancia global para debugging
window.mapsDebugger = new GoogleMapsDebugger();

// Ejecutar diagnósticos automáticamente después de cargar
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para que todo se cargue
    setTimeout(() => {
        if (window.location.search.includes('debug=maps')) {
            window.mapsDebugger.runDiagnostics();
        }
    }, 2000);
});

console.log('🔧 Google Maps Debugger cargado');
console.log('💡 Usa: mapsDebugger.runDiagnostics() para ejecutar diagnósticos');
console.log('💡 Usa: mapsDebugger.testAddress("dirección") para probar una dirección');
console.log('💡 O agrega ?debug=maps a la URL para diagnósticos automáticos');
