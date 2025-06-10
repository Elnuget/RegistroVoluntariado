<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Voluntariado</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Google Sans', 'Roboto', Arial, sans-serif;
            background-color: #f1f3f4;
            color: #202124;
            line-height: 1.6;
        }

        .form-container {
            max-width: 640px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 24px;
            color: white;
            text-align: center;
        }

        .form-title {
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .form-description {
            font-size: 14px;
            opacity: 0.9;
        }

        .form-body {
            padding: 24px;
        }

        .form-question {
            margin-bottom: 32px;
        }

        .question-label {
            display: block;
            font-size: 16px;
            font-weight: 400;
            color: #202124;
            margin-bottom: 8px;
            position: relative;
        }

        .required::after {
            content: " *";
            color: #d93025;
        }

        .form-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #dadce0;
            padding: 8px 0;
            font-size: 16px;
            font-family: inherit;
            background: transparent;
            transition: border-color 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-bottom: 2px solid #1a73e8;
        }

        .form-select {
            width: 100%;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 12px;
            font-size: 16px;
            font-family: inherit;
            background: white;
            outline: none;
            transition: border-color 0.2s;
        }        .form-select:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 1px #1a73e8;
        }

        .select-container {
            position: relative;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dadce0;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .dropdown-item {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item.selected {
            background-color: #e8f0fe;
            color: #1a73e8;
        }        .dropdown-item:last-child {
            border-bottom: none;
        }        .timezone-info {
            font-size: 11px;
            color: #5f6368;
            margin-top: 4px;
            font-style: italic;
            padding: 4px 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #34a853;
        }

        .submit-section {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #e8eaed;
        }

        .submit-btn {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 120px;
        }

        .submit-btn:hover {
            background: #1557b0;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
        }

        .submit-btn:active {
            background: #1557b0;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 4px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fce8e6;
            border: 1px solid #fad2cf;
            color: #d93025;
        }

        .alert-success {
            background-color: #e6f4ea;
            border: 1px solid #ceead6;
            color: #137333;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .form-footer {
            padding: 16px 24px;
            background: #f8f9fa;
            font-size: 12px;
            color: #5f6368;
            text-align: center;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 20px 16px;
            }
            
            .form-header {
                padding: 20px 16px;
            }
            
            .form-body {
                padding: 20px 16px;
            }
            
            .form-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h1 class="form-title">Registro de Voluntariado</h1>
            <p class="form-description">Complete el formulario para registrar su actividad de voluntariado</p>
        </div>

        <div class="form-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif            <form method="POST" action="{{ route('registros.store') }}">
                @csrf
                <input type="hidden" name="from_public_form" value="1">                <div class="form-question">
                    <label for="voluntario_id" class="question-label required">Voluntario</label>
                    <div class="select-container">
                        <input type="text" class="form-input" id="voluntario_search" 
                               placeholder="Escriba para buscar o seleccione de la lista" autocomplete="off">
                        <input type="hidden" id="voluntario_id" name="voluntario_id" required>
                        <div class="dropdown-list" id="voluntario_dropdown">
                            @foreach ($voluntarios as $voluntario)
                                <div class="dropdown-item" data-value="{{ $voluntario->id }}">
                                    {{ $voluntario->nombre_completo }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-question">
                    <label for="tipo_actividad" class="question-label required">Tipo de Actividad</label>
                    <select class="form-select" id="tipo_actividad" name="tipo_actividad" required>
                        <option value="">Seleccione el tipo de actividad</option>
                        <option value="Entrada" {{ old('tipo_actividad') == 'Entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="Salida" {{ old('tipo_actividad') == 'Salida' ? 'selected' : '' }}>Salida</option>
                        <option value="Extra" {{ old('tipo_actividad') == 'Extra' ? 'selected' : '' }}>Extra</option>
                    </select>
                </div>                <div class="form-question">
                    <label for="fecha" class="question-label required">Fecha</label>
                    <small style="color: #5f6368; font-size: 12px; display: block; margin-bottom: 8px;">
                        Formato: MM/DD/YYYY (Mes/Día/Año)
                    </small>
                    <input type="date" class="form-input" id="fecha" name="fecha" value="{{ old('fecha') }}" required>
                    <div class="timezone-info">Se establecerá automáticamente la fecha actual de Minnesota</div>                </div><div class="form-question">
                    <label for="hora" class="question-label required">Hora (Zona Horaria Central - Minnesota)</label>
                    <input type="time" class="form-input" id="hora" name="hora" value="{{ old('hora') }}" required>
                    <div class="timezone-info">Se establecerá automáticamente la hora actual de Minnesota</div>
                </div>

                <div class="form-question">
                    <label for="ubicacion_desde" class="question-label required">Ubicación de Origen</label>
                    <input type="text" class="form-input" id="ubicacion_desde" name="ubicacion_desde" 
                           value="{{ old('ubicacion_desde') }}" placeholder="Dirección de inicio" required>
                </div>

                <div class="form-question">
                    <label for="ubicacion_hasta" class="question-label required">Ubicación de Destino</label>
                    <input type="text" class="form-input" id="ubicacion_hasta" name="ubicacion_hasta" 
                           value="{{ old('ubicacion_hasta') }}" placeholder="Dirección de destino" required>
                </div>

                <div class="form-question">
                    <label for="millas" class="question-label required">Millas Recorridas</label>
                    <input type="number" step="0.01" class="form-input" id="millas" name="millas" 
                           value="{{ old('millas') }}" placeholder="0.00" required>
                </div>

                <div class="submit-section">
                    <button type="submit" class="submit-btn">Enviar Registro</button>
                </div>
            </form>
        </div>

        <div class="form-footer">
            Sistema de Registro de Voluntariado - {{ date('Y') }}
        </div>
    </div>    <script>        // Agregar fecha y hora actual por defecto usando zona horaria de Minnesota (Central Time)
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInput = document.getElementById('fecha');
            const horaInput = document.getElementById('hora');
            
            if (!fechaInput.value || !horaInput.value) {
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
                
                // Formato YYYY-MM-DD para la fecha (requerido por HTML date input)
                if (!fechaInput.value) {
                    fechaInput.value = `${year}-${month}-${day}`;
                    
                    // Actualizar el texto informativo con la fecha formateada MM/DD/YYYY
                    const fechaInfo = fechaInput.parentElement.querySelector('.timezone-info');
                    if (fechaInfo) {
                        fechaInfo.innerHTML = `Fecha establecida: ${month}/${day}/${year} (Minnesota)`;
                    }
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
        }        // Manejar selección de voluntario
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
            });
        });

        // Función para verificar registros existentes y configurar tipo de actividad
        async function checkAndSetTipoActividad(voluntarioId) {
            const fechaInput = document.getElementById('fecha');
            const tipoActividadSelect = document.getElementById('tipo_actividad');
            
            if (!voluntarioId || !fechaInput.value) {
                return;
            }

            try {
                const response = await fetch(`/api/voluntario-registros?voluntario_id=${voluntarioId}&fecha=${fechaInput.value}`);
                const data = await response.json();
                
                if (response.ok) {
                    // Seleccionar automáticamente el tipo de actividad sugerido
                    tipoActividadSelect.value = data.tipo_sugerido;
                    
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
                        infoMessage.innerHTML = `<span style="color: #1a73e8;">ℹ️ El voluntario ya tiene ${data.registros_count} registro(s) hoy. Se sugiere "Salida".</span>`;
                    } else {
                        infoMessage.innerHTML = `<span style="color: #137333;">✓ Primer registro del día. Se sugiere "Entrada".</span>`;
                    }
                    
                    tipoActividadContainer.appendChild(infoMessage);
                } else {
                    console.error('Error al verificar registros:', data.error);
                }
            } catch (error) {
                console.error('Error en la solicitud:', error);
            }
        }        // También verificar cuando cambie la fecha
        document.getElementById('fecha').addEventListener('change', function() {
            const selectedVoluntarioId = voluntarioId.value;
            if (selectedVoluntarioId) {
                checkAndSetTipoActividad(selectedVoluntarioId);
            }
            
            // Actualizar el mensaje informativo con el formato correcto MM/DD/YYYY
            const fechaInfo = this.parentElement.querySelector('.timezone-info');
            if (fechaInfo && this.value) {
                const dateParts = this.value.split('-'); // Viene en formato YYYY-MM-DD
                const year = dateParts[0];
                const month = dateParts[1];
                const day = dateParts[2];
                fechaInfo.innerHTML = `Fecha seleccionada: ${month}/${day}/${year} (Formato MM/DD/YYYY)`;
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
        @if(old('voluntario_id'))
            const oldVoluntarioId = '{{ old('voluntario_id') }}';
            const oldVoluntarioItem = document.querySelector(`[data-value="${oldVoluntarioId}"]`);
            if (oldVoluntarioItem) {
                voluntarioSearch.value = oldVoluntarioItem.textContent.trim();
                voluntarioId.value = oldVoluntarioId;
                oldVoluntarioItem.classList.add('selected');
            }
        @endif
    </script>
</body>
</html>
