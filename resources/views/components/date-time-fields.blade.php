<div class="form-question">
    <label for="fecha_visual" class="question-label required">Fecha</label>
    <small style="color: #5f6368; font-size: 12px; display: block; margin-bottom: 8px;">
        Formato: MM/DD/YYYY (Mes/Día/Año)
    </small>
    <div class="date-input-container" style="display: flex; gap: 5px;">
        <input type="text" class="form-input" id="fecha_mes" placeholder="MM" maxlength="2" style="width: 60px;" required>
        <span style="align-self: center;">/</span>
        <input type="text" class="form-input" id="fecha_dia" placeholder="DD" maxlength="2" style="width: 60px;" required>
        <span style="align-self: center;">/</span>
        <input type="text" class="form-input" id="fecha_anio" placeholder="YYYY" maxlength="4" style="width: 80px;" required>
        <!-- Campo oculto que almacena el valor en formato YYYY-MM-DD para enviar al servidor -->
        <input type="hidden" id="fecha" name="fecha" value="{{ old('fecha') }}" required>
    </div>
    <div class="timezone-info">Se establecerá automáticamente la fecha actual de Minnesota</div>
</div>

<div class="form-question">
    <label for="hora" class="question-label required">Hora (Zona Horaria Central - Minnesota)</label>
    <input type="time" class="form-input" id="hora" name="hora" value="{{ old('hora') }}" required>
    <div class="timezone-info">Se establecerá automáticamente la hora actual de Minnesota</div>
</div>
