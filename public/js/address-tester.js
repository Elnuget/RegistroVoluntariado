/**
 * Script de prueba para verificar geocodificación de direcciones de voluntarios
 * Este archivo ayuda a verificar que las direcciones reales se encuentren correctamente
 */

// Direcciones reales de voluntarios para pruebas
const direccionesVoluntarios = [
    '1233 Rose Vista Ct Apt 6 Roseville, MN, 55113',
    '5817 73rd Ave N apt32 Minneapolis, MN, 55429',
    '2226 Gentry Ave. N Oakdale, Minnesota, 55128',
    '1499 Magnolia Ave E. Apt 09 St. Paul, MN, 55106',
    '6489 lower 57th N Oakdale, Mn, 55103',
    '3851 Hamilton St Burnsville, MN, 55337-6007',
    '4153 Chicago Avenue, Apt 1 Minneapolis, MN, 55407',
    '3126 Clinton Ave S Minneapolis, Minnesota, 55408',
    '2212 Powers Avenue Saint Paul, Minnesota, 55119',
    '37 S Montgomery Ave Le Center, MN, 56057'
];

class DireccionTester {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.geocoder = null;
        this.results = [];
    }

    async initialize() {
        // Cargar Google Maps API
        if (typeof google === 'undefined') {
            await this.loadGoogleMapsAPI();
        }
        this.geocoder = new google.maps.Geocoder();
    }

    loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&libraries=places`;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    async testDireccion(direccion) {
        return new Promise((resolve) => {
            this.geocoder.geocode({ address: direccion }, (results, status) => {
                const resultado = {
                    direccionOriginal: direccion,
                    status: status,
                    encontrada: status === 'OK',
                    direccionFormateada: null,
                    coordenadas: null,
                    componentes: null
                };

                if (status === 'OK' && results[0]) {
                    resultado.direccionFormateada = results[0].formatted_address;
                    resultado.coordenadas = {
                        lat: results[0].geometry.location.lat(),
                        lng: results[0].geometry.location.lng()
                    };
                    resultado.componentes = results[0].address_components;
                }

                resolve(resultado);
            });
        });
    }

    async testTodasLasDirecciones() {
        console.log('🔍 Iniciando prueba de geocodificación...');
        
        for (let i = 0; i < direccionesVoluntarios.length; i++) {
            const direccion = direccionesVoluntarios[i];
            console.log(`📍 Probando ${i + 1}/${direccionesVoluntarios.length}: ${direccion}`);
            
            const resultado = await this.testDireccion(direccion);
            this.results.push(resultado);
            
            if (resultado.encontrada) {
                console.log(`✅ Encontrada: ${resultado.direccionFormateada}`);
                console.log(`📊 Coordenadas: ${resultado.coordenadas.lat}, ${resultado.coordenadas.lng}`);
            } else {
                console.log(`❌ No encontrada: ${resultado.status}`);
            }
            
            // Pequeña pausa para evitar límites de API
            await new Promise(resolve => setTimeout(resolve, 200));
        }

        this.mostrarResumen();
    }

    mostrarResumen() {
        const exitosas = this.results.filter(r => r.encontrada).length;
        const fallidas = this.results.length - exitosas;
        
        console.log('\n📊 RESUMEN DE PRUEBAS:');
        console.log(`✅ Direcciones encontradas: ${exitosas}/${this.results.length}`);
        console.log(`❌ Direcciones no encontradas: ${fallidas}/${this.results.length}`);
        console.log(`📈 Tasa de éxito: ${((exitosas / this.results.length) * 100).toFixed(1)}%`);
        
        if (fallidas > 0) {
            console.log('\n❌ Direcciones problemáticas:');
            this.results.filter(r => !r.encontrada).forEach(r => {
                console.log(`- ${r.direccionOriginal} (Status: ${r.status})`);
            });
        }

        // Mostrar todas las direcciones formateadas
        console.log('\n📝 DIRECCIONES FORMATEADAS:');
        this.results.filter(r => r.encontrada).forEach((r, index) => {
            console.log(`${index + 1}. Original: ${r.direccionOriginal}`);
            console.log(`   Formateada: ${r.direccionFormateada}`);
            console.log('');
        });
    }

    // Método para probar una dirección específica desde la consola
    async probarDireccion(direccion) {
        console.log(`🔍 Probando: ${direccion}`);
        const resultado = await this.testDireccion(direccion);
        
        if (resultado.encontrada) {
            console.log(`✅ Encontrada: ${resultado.direccionFormateada}`);
            console.log(`📊 Coordenadas: ${resultado.coordenadas.lat}, ${resultado.coordenadas.lng}`);
        } else {
            console.log(`❌ No encontrada: ${resultado.status}`);
        }
        
        return resultado;
    }
}

// Uso del tester
// const tester = new DireccionTester('TU_API_KEY_AQUI');
// tester.initialize().then(() => {
//     tester.testTodasLasDirecciones();
// });

// Para probar una dirección específica:
// tester.probarDireccion('1233 Rose Vista Ct Apt 6 Roseville, MN, 55113');

console.log('📋 Tester de direcciones cargado. Usa:');
console.log('const tester = new DireccionTester("TU_API_KEY");');
console.log('tester.initialize().then(() => tester.testTodasLasDirecciones());');
