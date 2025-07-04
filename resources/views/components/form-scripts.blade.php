@push('scripts')
<script>
// Manejar la fecha en formato MM/DD/YYYY con inputs personalizados
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    const fechaMesInput = document.getElementById('fecha_mes');
    const fechaDiaInput = document.getElementById('fecha_dia');
    const fechaAnioInput = document.getElementById('fecha_anio');
    const horaInput = document.getElementById('hora');
    
    // Función para actualizar el campo oculto con la fecha en formato YYYY-MM-DD
    function actualizarFechaOculta() {
        const mes = fechaMesInput.value.padStart(2, '0');
        const dia = fechaDiaInput.value.padStart(2, '0');
        const anio = fechaAnioInput.value;
        
        if (mes && dia && anio && anio.length === 4) {
            fechaInput.value = `${anio}-${mes}-${dia}`;
            
            // Actualizar el mensaje informativo
            const fechaInfo = fechaInput.parentElement.parentElement.querySelector('.timezone-info');
            if (fechaInfo) {
                fechaInfo.innerHTML = `Fecha seleccionada: ${mes}/${dia}/${anio} (Formato MM/DD/YYYY)`;
            }
            
            // Disparar el evento change en el campo oculto para que otros scripts lo detecten
            const event = new Event('change');
            fechaInput.dispatchEvent(event);
        }
    }
    
    // Agregar listeners a los campos de fecha
    fechaMesInput.addEventListener('input', function() {
        if (this.value.length === 2 && parseInt(this.value) > 0 && parseInt(this.value) <= 12) {
            fechaDiaInput.focus();
        }
        actualizarFechaOculta();
    });
    
    fechaDiaInput.addEventListener('input', function() {
        if (this.value.length === 2 && parseInt(this.value) > 0 && parseInt(this.value) <= 31) {
            fechaAnioInput.focus();
        }
        actualizarFechaOculta();
    });
    
    fechaAnioInput.addEventListener('input', function() {
        actualizarFechaOculta();
    });
    
    // Solo permitir números en los campos de fecha
    [fechaMesInput, fechaDiaInput, fechaAnioInput].forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
    
    // Establecer fecha actual de Minnesota si no hay valor
    if (!fechaInput.value || fechaInput.value === '') {
        // Crear fecha con zona horaria de Minnesota (Central Time)
        const now = new Date();
        // Usar Intl.DateTimeFormat para obtener la fecha en zona horaria de Minnesota
        const minnesotaFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: 'America/Chicago',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
        
        const minnesotaParts = minnesotaFormatter.formatToParts(now);
        const month = minnesotaParts.find(p => p.type === 'month').value;
        const day = minnesotaParts.find(p => p.type === 'day').value;
        const year = minnesotaParts.find(p => p.type === 'year').value;
        const hour = minnesotaParts.find(p => p.type === 'hour').value;
        const minute = minnesotaParts.find(p => p.type === 'minute').value;
        
        // Establecer los valores en los campos
        fechaMesInput.value = month;
        fechaDiaInput.value = day;
        fechaAnioInput.value = year;
        fechaInput.value = `${year}-${month}-${day}`;
        
        // Actualizar el mensaje informativo
        const fechaInfo = fechaInput.parentElement.parentElement.querySelector('.timezone-info');
        if (fechaInfo) {
            fechaInfo.innerHTML = `Fecha establecida: ${month}/${day}/${year} (Minnesota)`;
        }
        
        // Formato HH:MM para la hora
        if (!horaInput.value) {
            horaInput.value = `${hour}:${minute}`;
            
            // Actualizar el texto informativo con la hora establecida
            const horaInfo = horaInput.parentElement.querySelector('.timezone-info');
            if (horaInfo) {
                const hourNum = parseInt(hour);
                const ampm = hourNum >= 12 ? 'PM' : 'AM';
                const displayHour = hourNum % 12 || 12;
                horaInfo.innerHTML = `Hora establecida: ${displayHour}:${minute} ${ampm} (Central Time)`;
            }
        }
    } else {
        // Si ya hay una fecha, dividirla y colocarla en los campos
        const dateParts = fechaInput.value.split('-'); // Viene en formato YYYY-MM-DD
        if (dateParts.length === 3) {
            const year = dateParts[0];
            const month = dateParts[1];
            const day = dateParts[2];
            
            fechaAnioInput.value = year;
            fechaMesInput.value = month;
            fechaDiaInput.value = day;
            
            // Actualizar el mensaje informativo
            const fechaInfo = fechaInput.parentElement.parentElement.querySelector('.timezone-info');
            if (fechaInfo) {
                fechaInfo.innerHTML = `Fecha seleccionada: ${month}/${day}/${year} (Formato MM/DD/YYYY)`;
            }
        }
    }
});

// Mejorar la experiencia del usuario
const inputs = document.querySelectorAll('.form-input');
inputs.forEach(input => {
    input.addEventListener('focus', function() {
        this.style.borderColor = '#1a73e8';
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.style.borderColor = '#dadce0';
        }
    });
});

// Funcionalidad del dropdown de voluntarios
const voluntarioSearch = document.getElementById('voluntario_search');
const voluntarioId = document.getElementById('voluntario_id');
const dropdown = document.getElementById('voluntario_dropdown');
const dropdownItems = document.querySelectorAll('.dropdown-item');

// Mostrar dropdown al hacer focus
voluntarioSearch.addEventListener('focus', function() {
    dropdown.style.display = 'block';
    filterVoluntarios(''); // Mostrar todos
});

// Ocultar dropdown al hacer click fuera
document.addEventListener('click', function(e) {
    if (!voluntarioSearch.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

// Filtrar voluntarios mientras escribe
voluntarioSearch.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    filterVoluntarios(searchTerm);
    
    // Limpiar selección si el texto no coincide exactamente
    const exactMatch = Array.from(dropdownItems).find(item => 
        item.textContent.trim().toLowerCase() === searchTerm
    );
    if (!exactMatch) {
        voluntarioId.value = '';
    }
});

// Función para filtrar voluntarios
function filterVoluntarios(searchTerm) {
    let hasVisibleItems = false;
    dropdownItems.forEach(item => {
        const text = item.textContent.trim().toLowerCase();
        if (text.includes(searchTerm)) {
            item.style.display = 'block';
            hasVisibleItems = true;
        } else {
            item.style.display = 'none';
        }
    });

    // Mostrar u ocultar dropdown basado en si hay elementos visibles
    if (hasVisibleItems) {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

// Manejar selección de voluntario
dropdownItems.forEach(item => {
    item.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        const text = this.textContent.trim();
        
        voluntarioSearch.value = text;
        voluntarioId.value = value;
        dropdown.style.display = 'none';
        
        // Remover selección anterior
        dropdownItems.forEach(i => i.classList.remove('selected'));
        // Agregar selección actual
        this.classList.add('selected');

        // Verificar registros y configurar tipo de actividad automáticamente
        checkAndSetTipoActividad(value);
        
        // Cargar dirección del voluntario automáticamente
        loadVolunteerAddress(value);
    });
});

// Función para verificar registros existentes y configurar tipo de actividad
async function checkAndSetTipoActividad(voluntarioId) {
    const fechaInput = document.getElementById('fecha'); // Este es ahora el campo oculto
    const tipoActividadSelect = document.getElementById('tipo_actividad');
    
    if (!voluntarioId || !fechaInput.value) {
        return;
    }

    try {
        const response = await fetch(`/api/voluntario-registros?voluntario_id=${voluntarioId}&fecha=${fechaInput.value}`);
        const data = await response.json();
        
        if (response.ok) {
            // Solo seleccionar automáticamente si no hay un tipo ya seleccionado por el usuario
            const currentTipoActividad = tipoActividadSelect.value;
            if (!currentTipoActividad || currentTipoActividad === '') {
                tipoActividadSelect.value = data.tipo_sugerido;
            }
            
            // Mostrar información visual (opcional)
            const tipoActividadContainer = tipoActividadSelect.parentElement;
            
            // Remover mensajes anteriores
            const existingMessage = tipoActividadContainer.querySelector('.activity-info');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Agregar mensaje informativo
            const infoMessage = document.createElement('div');
            infoMessage.className = 'activity-info';
            infoMessage.style.cssText = 'font-size: 12px; color: #5f6368; margin-top: 4px;';
            
            if (data.tiene_registros) {
                if (data.es_hoy && data.tiene_entrada_hoy) {
                    infoMessage.innerHTML = `<span style="color: #1a73e8;">ℹ️ El voluntario ya tiene entrada registrada hoy. Se sugiere "Salida".</span>`;
                } else {
                    infoMessage.innerHTML = `<span style="color: #1a73e8;">ℹ️ El voluntario ya tiene ${data.registros_count} registro(s) en esta fecha.</span>`;
                }
            } else if (data.es_hoy) {
                infoMessage.innerHTML = `<span style="color: #137333;">✓ Primer registro del día. Se sugiere "Entrada".</span>`;
            } else {
                infoMessage.innerHTML = `<span style="color: #5f6368;">Seleccione el tipo de actividad para esta fecha.</span>`;
            }
              tipoActividadContainer.appendChild(infoMessage);
              
              // Solo configurar ubicaciones automáticamente si es el día actual
              if (data.es_hoy) {
                // Usar el tipo de actividad actualmente seleccionado en lugar del sugerido
                const tipoActividadActual = tipoActividadSelect.value;
                
                // Configurar ubicaciones basado en el tipo de actividad actual
                if (tipoActividadActual === 'Entrada') {
                    // Para "Entrada": Voluntario -> Oficina
                    obtenerDireccionVoluntarioComoOrigen(voluntarioId);
                    
                    // También establecer la ubicación de destino como la oficina
                    const ubicacionHastaInput = document.getElementById('ubicacion_hasta');
                    const direccionDestinoInfo = document.getElementById('direccion_destino_info');
                    
                    // Dirección fija de la oficina
                    const direccionOficina = "2042 wooddale drive, suite 250 Avenue, Minneapolis, MN 55401, USA";
                    ubicacionHastaInput.value = direccionOficina;
                    
                    // Mostrar mensaje informativo
                    if (direccionDestinoInfo) {
                        direccionDestinoInfo.innerHTML = `<span style="color: #137333;">✓ Dirección de la oficina establecida automáticamente</span>`;
                    }
                    
                    // Actualizar el mapa con la dirección de la oficina como destino
                    updateMapSafely('destination', direccionOficina);
                } else if (tipoActividadActual === 'Salida') {
                    // Para "Salida": Oficina -> Voluntario
                    const ubicacionDesdeInput = document.getElementById('ubicacion_desde');
                    const direccionOrigenInfo = document.getElementById('direccion_origen_info');
                    
                    // Dirección fija de la oficina como origen
                    const direccionOficina = "2042 wooddale drive, suite 250 Avenue, Minneapolis, MN 55401, USA";
                    ubicacionDesdeInput.value = direccionOficina;
                    
                    // Mostrar mensaje informativo para origen
                    if (direccionOrigenInfo) {
                        direccionOrigenInfo.innerHTML = `<span style="color: #137333;">✓ Dirección de la oficina establecida automáticamente</span>`;
                    }
                    
                    // Actualizar el mapa con la dirección de la oficina como origen
                    updateMapSafely('origin', direccionOficina);
                    
                    // Establecer la dirección del voluntario como destino
                    obtenerDireccionVoluntarioComoDestino(voluntarioId);
                }
              }
        } else {
            console.error('Error al verificar registros:', data.error);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
    }
}

// También verificar cuando cambie la fecha
document.getElementById('fecha').addEventListener('change', function() {
    const selectedVoluntarioId = document.getElementById('voluntario_id').value;
    if (selectedVoluntarioId) {
        checkAndSetTipoActividad(selectedVoluntarioId);
    }
});

// Navegación con teclado
voluntarioSearch.addEventListener('keydown', function(e) {
    const visibleItems = Array.from(dropdownItems).filter(item => 
        item.style.display !== 'none'
    );
    
    if (visibleItems.length === 0) return;
    
    const currentSelected = visibleItems.find(item => 
        item.classList.contains('selected')
    );
    let selectedIndex = currentSelected ? visibleItems.indexOf(currentSelected) : -1;
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex = (selectedIndex + 1) % visibleItems.length;
        updateSelection(visibleItems, selectedIndex);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex = selectedIndex <= 0 ? visibleItems.length - 1 : selectedIndex - 1;
        updateSelection(visibleItems, selectedIndex);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (currentSelected) {
            currentSelected.click();
        }
    } else if (e.key === 'Escape') {
        dropdown.style.display = 'none';
    }
});

function updateSelection(visibleItems, selectedIndex) {
    // Remover selección anterior
    dropdownItems.forEach(item => item.classList.remove('selected'));
    
    // Agregar nueva selección
    if (selectedIndex >= 0 && selectedIndex < visibleItems.length) {
        visibleItems[selectedIndex].classList.add('selected');
        visibleItems[selectedIndex].scrollIntoView({ block: 'nearest' });
    }
}

// Restaurar valor seleccionado si existe (para cuando hay errores de validación)
document.addEventListener('DOMContentLoaded', function() {
    const oldVoluntarioId = '{{ old('voluntario_id') }}';
    if (oldVoluntarioId) {
        const oldVoluntarioItem = document.querySelector(`[data-value="${oldVoluntarioId}"]`);
        if (oldVoluntarioItem) {
            voluntarioSearch.value = oldVoluntarioItem.textContent.trim();
            voluntarioId.value = oldVoluntarioId;
            oldVoluntarioItem.classList.add('selected');
            
            // Restaurar configuración de ubicaciones basado en el tipo de actividad guardado
            const oldTipoActividad = '{{ old('tipo_actividad') }}';
            if (oldTipoActividad === 'Entrada') {
                const direccionOrigenInfoElem = document.getElementById('direccion_origen_info');
                const direccionDestinoInfoElem = document.getElementById('direccion_destino_info');
                
                if (direccionOrigenInfoElem) {
                    direccionOrigenInfoElem.innerHTML = `<span style="color: #137333;">✓ Dirección del voluntario cargada automáticamente como origen</span>`;
                }
                
                if (direccionDestinoInfoElem) {
                    direccionDestinoInfoElem.innerHTML = `<span style="color: #137333;">✓ Dirección de la oficina establecida automáticamente</span>`;
                }
            } else if (oldTipoActividad === 'Salida') {
                const direccionOrigenInfoElem = document.getElementById('direccion_origen_info');
                const direccionDestinoInfoElem = document.getElementById('direccion_destino_info');
                
                if (direccionOrigenInfoElem) {
                    direccionOrigenInfoElem.innerHTML = `<span style="color: #137333;">✓ Dirección de la oficina establecida automáticamente</span>`;
                }
                
                if (direccionDestinoInfoElem) {
                    direccionDestinoInfoElem.innerHTML = `<span style="color: #137333;">✓ Dirección del voluntario cargada automáticamente como destino</span>`;
                }
            }
        }
    }
});

// Función para obtener y establecer la dirección del voluntario como ubicación de origen
async function obtenerDireccionVoluntarioComoOrigen(voluntarioId) {
    try {
        const response = await fetch(`/api/voluntario/${voluntarioId}`);
        const data = await response.json();
        
        if (data.success) {
            const voluntario = data.voluntario;
            const ubicacionDesdeInput = document.getElementById('ubicacion_desde');
            const direccionInfo = document.getElementById('direccion_origen_info');
            
            // Establecer la dirección como ubicación de origen
            ubicacionDesdeInput.value = voluntario.direccion;
            
            // Mostrar mensaje informativo
            if (direccionInfo) {
                direccionInfo.innerHTML = `<span style="color: #137333;">✓ Dirección del voluntario cargada automáticamente como origen</span>`;
            }
            
            // Actualizar el mapa con la dirección del voluntario como origen
            updateMapSafely('origin', voluntario.direccion);
        } else {
            console.error('Error al obtener información del voluntario:', data.error);
        }
    } catch (error) {
        console.error('Error al obtener la dirección del voluntario:', error);
    }
}

// Función para obtener y establecer la dirección del voluntario como ubicación de destino
async function obtenerDireccionVoluntarioComoDestino(voluntarioId) {
    try {
        const response = await fetch(`/api/voluntario/${voluntarioId}`);
        const data = await response.json();
        
        if (data.success) {
            const voluntario = data.voluntario;
            const ubicacionHastaInput = document.getElementById('ubicacion_hasta');
            const direccionInfo = document.getElementById('direccion_destino_info');
            
            // Establecer la dirección como ubicación de destino
            ubicacionHastaInput.value = voluntario.direccion;
            
            // Mostrar mensaje informativo
            if (direccionInfo) {
                direccionInfo.innerHTML = `<span style="color: #137333;">✓ Dirección del voluntario cargada automáticamente como destino</span>`;
            }
            
            // Actualizar el mapa con la dirección del voluntario como destino
            updateMapSafely('destination', voluntario.direccion);
        } else {
            console.error('Error al obtener información del voluntario:', data.error);
        }
    } catch (error) {
        console.error('Error al obtener la dirección del voluntario:', error);
    }
}

// Función para cargar la dirección del voluntario y establecerla como origen
async function loadVolunteerAddress(voluntarioId) {
    if (!voluntarioId) return;
    
    try {
        const response = await fetch(`/api/voluntario-direccion?voluntario_id=${voluntarioId}`);
        const data = await response.json();
        
        if (response.ok && data.direccion) {
            // Determinar tipo de actividad actual
            const tipoActividadSelect = document.getElementById('tipo_actividad');
            const tipo = tipoActividadSelect ? tipoActividadSelect.value : null;
            
            if (tipo === 'Salida') {
                // Para salida, establecer dirección como destino
                const ubicacionHastaInput = document.getElementById('ubicacion_hasta');
                if (ubicacionHastaInput) {
                    ubicacionHastaInput.value = data.direccion;
                    // Mostrar mensaje informativo
                    const direccionInfo = document.getElementById('direccion_destino_info');
                    if (direccionInfo) {
                        direccionInfo.innerHTML = `<span style=\"color: #137333;\">✓ Dirección del voluntario cargada automáticamente como destino</span>`;
                    }
                    // Disparar evento change para actualizar el mapa
                    ubicacionHastaInput.dispatchEvent(new Event('change', { bubbles: true }));
                    updateMapSafely('destination', data.direccion);
                }
            } else {
                // Para entrada u otros, establecer dirección como origen
                const ubicacionOrigenInput = document.getElementById('ubicacion_desde');
                if (ubicacionOrigenInput) {
                    ubicacionOrigenInput.value = data.direccion;
                    // Mostrar mensaje informativo
                    const direccionInfo = document.getElementById('direccion_origen_info');
                    if (direccionInfo) {
                        direccionInfo.innerHTML = `<span style=\"color: #137333;\">✓ Dirección del voluntario cargada automáticamente como origen</span>`;
                    }
                    // Disparar evento change para actualizar el mapa
                    ubicacionOrigenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    updateMapSafely('origin', data.direccion);
                }
            }
        }
    } catch (error) {
        console.error('Error al cargar dirección del voluntario:', error);
    }
}

// Escuchar cambios en el tipo de actividad para actualizar las ubicaciones
document.getElementById('tipo_actividad').addEventListener('change', function() {
    const selectedVoluntarioId = document.getElementById('voluntario_id').value;
    const ubicacionDesdeInput = document.getElementById('ubicacion_desde');
    const ubicacionHastaInput = document.getElementById('ubicacion_hasta');
    const direccionOrigenInfo = document.getElementById('direccion_origen_info');
    const direccionDestinoInfo = document.getElementById('direccion_destino_info');
    
    if (this.value === 'Entrada' && selectedVoluntarioId) {
        // Para "Entrada": Voluntario -> Oficina
        obtenerDireccionVoluntarioComoOrigen(selectedVoluntarioId);
        
        // Establecer la ubicación de destino como la oficina
        const direccionOficina = "2042 wooddale drive, suite 250 Avenue, Minneapolis, MN 55401, USA";
        ubicacionHastaInput.value = direccionOficina;
        
        // Mostrar mensaje informativo
        if (direccionDestinoInfo) {
            direccionDestinoInfo.innerHTML = `<span style="color: #137333;">✓ Dirección de la oficina establecida automáticamente</span>`;
        }
        
        // Actualizar el mapa con la dirección de la oficina como destino
        updateMapSafely('destination', direccionOficina);
    } else if (this.value === 'Salida' && selectedVoluntarioId) {
        // Para "Salida": Oficina -> Voluntario
        const direccionOficina = "2042 wooddale drive, suite 250 Avenue, Minneapolis, MN 55401, USA";
        ubicacionDesdeInput.value = direccionOficina;
        
        // Mostrar mensaje informativo para origen
        if (direccionOrigenInfo) {
            direccionOrigenInfo.innerHTML = `<span style="color: #137333;">✓ Dirección de la oficina establecida automáticamente</span>`;
        }
        
        // Actualizar el mapa con la dirección de la oficina como origen
        updateMapSafely('origin', direccionOficina);
        
        // Establecer la dirección del voluntario como destino
        obtenerDireccionVoluntarioComoDestino(selectedVoluntarioId);
    } else {
        // Limpiar campos y mensajes informativos para otros tipos o cuando no hay voluntario seleccionado
        if (this.value !== 'Entrada' && this.value !== 'Salida') {
            ubicacionDesdeInput.value = '';
            ubicacionHastaInput.value = '';
        }
        
        if (direccionOrigenInfo) {
            direccionOrigenInfo.innerHTML = '';
        }
        
        if (direccionDestinoInfo) {
            direccionDestinoInfo.innerHTML = '';
        }
    }
});

// Función auxiliar para actualizar el mapa de manera segura
function updateMapSafely(action, address) {
    // Verificar que el mapa esté disponible y inicializado
    if (typeof window.routeMap !== 'undefined' && window.routeMap && window.routeMap.isInitialized) {
        try {
            if (action === 'origin') {
                window.routeMap.updateOrigin(address);
            } else if (action === 'destination') {
                window.routeMap.updateDestination(address);
            }
        } catch (error) {
            console.warn('Error al actualizar el mapa:', error);
        }
    } else {
        // Si el mapa no está listo, intentar después de un breve delay
        setTimeout(() => {
            if (typeof window.routeMap !== 'undefined' && window.routeMap && window.routeMap.isInitialized) {
                try {
                    if (action === 'origin') {
                        window.routeMap.updateOrigin(address);
                    } else if (action === 'destination') {
                        window.routeMap.updateDestination(address);
                    }
                } catch (error) {
                    console.warn('Error al actualizar el mapa (segundo intento):', error);
                }
            }
        }, 1000);
    }
}
</script>
@endpush
