# Configuración de Google Places API para Autocompletado de Ubicaciones

## 📋 Pasos para configurar la API

### 1. Obtener API Key de Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la **Places API** y **Maps JavaScript API**
4. Ve a "Credenciales" → "Crear credenciales" → "Clave de API"
5. Copia la API key generada

### 2. Configurar restricciones de la API Key (Recomendado)

Por seguridad, configura restricciones:
- **Restricciones de aplicación**: Referentes HTTP
- **Agregar**: `localhost:*`, `127.0.0.1:*`, y tu dominio de producción
- **Restricciones de API**: Selecciona solo Places API y Maps JavaScript API

### 3. Configurar la aplicación

#### Opción A: Usar el archivo JavaScript mejorado

1. Abre `public/js/location-autocomplete.js`
2. Reemplaza `'TU_API_KEY_AQUI'` con tu API key real
3. Cambia `componentRestrictions: { country: 'us' }` por tu país (ej: 'mx' para México)

#### Opción B: Usar variable de entorno (Más seguro)

1. Agrega tu API key al archivo `.env`:
```env
GOOGLE_MAPS_API_KEY=tu_api_key_aqui
```

2. Modifica el archivo blade para usar la variable:
```blade
<script>
    const GOOGLE_MAPS_API_KEY = '{{ env('GOOGLE_MAPS_API_KEY') }}';
</script>
<script src="{{ asset('js/location-autocomplete.js') }}"></script>
```

### 4. Incluir el script en tu template

Agrega al final de tu archivo blade (antes de `</body>`):

```blade
<!-- Incluir el script de autocompletado -->
<script src="{{ asset('js/location-autocomplete.js') }}"></script>
```

## 🎯 Características implementadas

### ✅ Autocompletado inteligente
- Busca ubicaciones reales mientras escribes
- Mínimo 3 caracteres para activar la búsqueda
- Debounce de 300ms para optimizar las consultas
- Navegación con teclado (↑↓ Enter Escape)

### ✅ Interfaz como Google Maps
- Texto principal en negrita
- Texto secundario en gris
- Iconos visuales (📍, 🔍, ❌)
- Hover y selección visual

### ✅ Fallback automático
- Si falla la API, muestra resultados simulados
- No interrumpe la experiencia del usuario
- Logs de errores para debugging

### ✅ Optimización de rendimiento
- Carga asíncrona de Google Maps API
- Debounce en las búsquedas
- Caché automático de resultados

## 🚀 Uso

Una vez configurado, los usuarios pueden:

1. **Escribir en el campo**: Empezar a escribir "Vento..." 
2. **Ver sugerencias**: Aparecerán opciones como "Vento Centro, Centro de la ciudad"
3. **Seleccionar**: Click o Enter para seleccionar
4. **Autocompletado**: El campo se llena automáticamente

## 🔧 Personalización

### Cambiar país de búsqueda
```javascript
componentRestrictions: { country: 'mx' } // México
componentRestrictions: { country: 'es' } // España
```

### Cambiar tipos de lugares
```javascript
types: ['geocode'] // Solo direcciones
types: ['establishment'] // Solo negocios
types: ['(cities)'] // Solo ciudades
```

### Modificar número de resultados
```javascript
// En la función displayResults, cambia:
callback(mockResults.slice(0, 4)); // Mostrar 4 resultados
```

## 💡 Próximos pasos sugeridos

1. **Integrar con el mapa**: Mostrar ubicaciones seleccionadas en el mapa
2. **Calcular distancia**: Usar las coordenadas para calcular millas automáticamente
3. **Geocodificación reversa**: Permitir seleccionar ubicaciones desde el mapa
4. **Historial**: Guardar ubicaciones frecuentes del usuario

## 🐛 Troubleshooting

### Error: "google is not defined"
- Verifica que la API key esté correcta
- Asegúrate de que Places API esté habilitada
- Revisa las restricciones de dominio

### No aparecen resultados
- Verifica tu conexión a internet
- Revisa la consola del navegador para errores
- Asegúrate de que la API key tenga permisos

### Límites de cuota excedidos
- Revisa tu uso en Google Cloud Console
- Considera implementar caché del lado del servidor
- Aumenta el debounce delay para reducir consultas
