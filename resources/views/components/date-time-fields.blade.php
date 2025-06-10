<div class="form-question">
    <label for="fecha" class="question-label required">Fecha</label>
    <small style="color: #5f6368; font-size: 12px; display: block; margin-bottom: 8px;">
        Formato: MM/DD/YYYY (Mes/Día/Año)
    </small>
    <input type="date" class="form-input" id="fecha" name="fecha" value="{{ old('fecha') }}" data-format="mm/dd/yyyy" required>
    <div class="timezone-info">Se establecerá automáticamente la fecha actual de Minnesota</div>
</div>

<div class="form-question">
    <label for="hora" class="question-label required">Hora (Zona Horaria Central - Minnesota)</label>
    <input type="time" class="form-input" id="hora" name="hora" value="{{ old('hora') }}" required>
    <div class="timezone-info">Se establecerá automáticamente la hora actual de Minnesota</div>
</div>
