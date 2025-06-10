# Documentación - Estructura Modular de Vistas

## Descripción
Este documento describe la estructura modular creada para el formulario de registro de voluntariado. La modularización se realizó para mejorar la mantenibilidad y reutilización del código.

## Estructura de Componentes

### Layout Principal
- `resources/views/components/layout.blade.php`: Define la estructura básica HTML del documento y contiene los estilos CSS globales.

### Componentes de Formulario
- `resources/views/components/form-header.blade.php`: Encabezado del formulario.
- `resources/views/components/alerts.blade.php`: Muestra alertas de éxito o error.
- `resources/views/components/voluntario-form.blade.php`: Campos para seleccionar voluntario y tipo de actividad.
- `resources/views/components/date-time-fields.blade.php`: Campos para fecha y hora con zona horaria.
- `resources/views/components/activity-fields.blade.php`: Campos específicos de la actividad (tipo, ubicaciones, millas).
- `resources/views/components/submit-button.blade.php`: Botón de envío del formulario.
- `resources/views/components/form-footer.blade.php`: Pie de página del formulario.
- `resources/views/components/form-scripts.blade.php`: Scripts JavaScript para la funcionalidad del formulario.

## Vista Principal
- `resources/views/registros/registro-modular.blade.php`: Vista principal que integra todos los componentes.

## Cómo Utilizar

### Extender la Funcionalidad
Para agregar nuevos campos o funcionalidades al formulario:

1. Cree un nuevo componente en `resources/views/components/` si es una funcionalidad separable.
2. Modifique los componentes existentes si es una modificación a la funcionalidad actual.
3. Incluya el nuevo componente en `registro-modular.blade.php` si es necesario.

### Modificar Estilos
Los estilos CSS están definidos en el archivo `layout.blade.php`. Para hacer cambios:

1. Modifique los estilos existentes en el archivo.
2. Agregue nuevos estilos en la sección `@section('styles')` en la vista principal.

### Agregar JavaScript
La funcionalidad JavaScript está en `form-scripts.blade.php`. Para agregar o modificar:

1. Edite el archivo para cambiar la funcionalidad existente.
2. Agregue nuevo JavaScript a través de `@push('scripts')` en la vista principal o en componentes individuales.

## Beneficios de la Estructura Modular

1. **Mantenibilidad**: Cada componente tiene una responsabilidad única y clara.
2. **Reutilización**: Los componentes pueden reutilizarse en diferentes vistas.
3. **Organización**: El código está organizado de manera lógica y fácil de entender.
4. **Escalabilidad**: Facilita la adición de nuevas funcionalidades sin modificar el código existente.

## Nota sobre Zonas Horarias
El formulario está configurado para usar la zona horaria de Minnesota (America/Chicago). Esta configuración se encuentra en el JavaScript y se aplica automáticamente al cargar la página.
