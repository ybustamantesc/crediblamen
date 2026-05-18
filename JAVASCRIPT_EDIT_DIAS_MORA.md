# Documentación - Edición de Días de Mora

## Descripción General
La funcionalidad permite editar manualmente los días de mora de cada cuota en el estado de cuenta. Cuando se modifica el valor, se recalcula automáticamente el monto en mora basado en la fórmula de interés diario.

## Flujo Técnico

### 1. Usuario Interactúa con el Botón "Editar"
```html
<button class="btn btn-sm btn-warning no-print" 
        onclick="editDiasMora(idcuota, dias_mora)"
        style="padding:2px 6px; font-size:11px; margin-left:4px;">
    Editar
</button>
```

**Ejemplo:** `onclick="editDiasMora(42, 5)"`
- `42` = ID de la cuota
- `5` = Días de mora actuales

### 2. Función JavaScript `editDiasMora()`

#### 2.1 - Solicitar Nuevo Valor
```javascript
let newDays = prompt('Ingrese los nuevos días de mora:', currentDays);
```

El usuario ve una ventana con el valor actual pre-cargado.

**Ejemplos de entrada válida:**
- `0` - Sin mora
- `10` - 10 días de mora
- `30` - Un mes de mora

#### 2.2 - Validaciones Locales
```javascript
if (newDays === null) return;  // Usuario cancela
newDays = parseInt(newDays, 10);
if (isNaN(newDays) || newDays < 0) {
    alert('Por favor ingrese un número válido (mayor o igual a 0)');
    return;
}
```

#### 2.3 - Petición AJAX al Servidor
```javascript
$.ajax({
    url: '/planescredito/update_dias_mora',
    type: 'POST',
    dataType: 'json',
    data: {
        idcuota: idcuota,
        nuevos_dias: newDays
    },
    success: function(response) { ... },
    error: function(xhr, status, error) { ... }
});
```

### 3. Procesamiento en el Servidor (PHP)

#### 3.1 - Método del Controlador
**Ubicación:** `application/controllers/Planescredito.php::update_dias_mora()`

**Validaciones:**
- Usuario debe estar autenticado
- `idcuota` debe ser > 0
- `nuevos_dias` debe ser >= 0

#### 3.2 - Obtener Datos de la Cuota
```php
$cuota = $this->db->get_where('tb_prestamo_cuotas', 
    ['idcuota' => $idcuota])->row();
```

Recupera el principal de la cuota.

#### 3.3 - Cálculo del Monto Mora
```php
$principal = floatval($cuota->principal);
$nuevo_monto_mora = $principal * (0.18 / 360) * $nuevos_dias;
$nuevo_monto_mora = round($nuevo_monto_mora, 2);
```

**Fórmula Explicada:**
- `0.18` = 18% anual (tasa de mora)
- `360` = Días comerciales del año
- `$nuevos_dias` = Días de mora ingresados
- Resultado: Dinero adeudado por interés en mora

**Ejemplo Numérico:**
- Principal: $1,000
- Días de mora: 10
- Cálculo: 1000 * (0.18/360) * 10 = 1000 * 0.0005 * 10 = $5
- Monto en mora: $5

#### 3.4 - Actualizar Base de Datos
```php
$update_data = [
    'dias_mora_manual' => $nuevos_dias,
    'monto_mora' => $nuevo_monto_mora
];
$this->db->update('tb_prestamo_cuotas', $update_data);
```

**Campos actualizados en `tb_prestamo_cuotas`:**
- `dias_mora_manual` (INT): Nuevo valor ingresado manualmente
- `monto_mora` (DECIMAL 14,2): Resultado del cálculo

#### 3.5 - Respuesta JSON
```json
{
    "success": true,
    "message": "Actualizado correctamente",
    "nuevo_monto_mora": 5.00
}
```

### 4. Actualización de la Tabla (Lado Cliente)

#### 4.1 - Actualizar Días Mora Mostrados
```javascript
$('#dias-mora-display-' + idcuota).text(newDays);
```

**Ejemplo:** Si `idcuota = 42` y `newDays = 8`:
- Busca elemento con ID: `dias-mora-display-42`
- Actualiza su contenido a: `8`

#### 4.2 - Recalcular Monto Mora Visualmente
```javascript
let $row = $('#dias-mora-display-' + idcuota).closest('tr');
let principal = parseFloat($row.find('td:eq(3)').text().replace(/[\$,]/g, ''));
let montoMora = principal * (0.18 / 360) * newDays;
let montoMoraFormatted = '$' + montoMora.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
$row.find('td:eq(8)').text(montoMoraFormatted);
```

**Desglose:**
1. `closest('tr')` - Obtiene la fila de la tabla
2. `find('td:eq(3)')` - Columna 4 (0-indexed) = Capital/Principal
3. `.replace(/[\$,]/g, '')` - Remueve símbolos de moneda y separadores de miles
4. `parseFloat()` - Convierte a número
5. Calcula `montoMora` con la fórmula
6. Formatea a 2 decimales y agrega separadores de miles
7. `find('td:eq(8)')` - Columna 9 (0-indexed) = Monto Mora
8. Actualiza el texto en la tabla

#### 4.3 - Mostrar Confirmación
```javascript
alert('Días de mora actualizados correctamente');
```

## Estructura de Columnas en la Tabla

```
0  - No Cuota
1  - Fecha
2  - Cuota
3  - Capital         ← Principal (usado para calcular)
4  - Interés
5  - Pagado
6  - Saldo
7  - Días Mora       ← Botón "Editar" está aquí
8  - Monto Mora      ← Se actualiza aquí
9  - Fecha Pagada
10 - No Serie Recibo
11 - Estado de Pago
```

## Comportamiento de Errores

### Si el usuario cancela el prompt
- Función retorna sin hacer nada
- La tabla no cambia

### Si ingresa un valor inválido (no-numérico)
```javascript
alert('Por favor ingrese un número válido (mayor o igual a 0)');
```

### Si hay error en el servidor
```javascript
alert('Error al comunicarse con el servidor');
// Ver consola para más detalles con console.error()
```

### Si la cuota no existe
**Respuesta del servidor:**
```json
{
    "success": false,
    "message": "Cuota no encontrada"
}
```

## Persistencia de Datos

Los cambios se guardan permanentemente en la base de datos:
- Campo `tb_prestamo_cuotas.dias_mora_manual`
- Campo `tb_prestamo_cuotas.monto_mora`

Al recargar la página, los valores editados se muestran automáticamente porque el controlador `estado_cuenta()` calcula:
```php
if ($diasMoraManual !== null) {
    // Usar valor manual
} else {
    // Calcular automáticamente
}
```

## Consideraciones de Seguridad

1. **Autenticación:** Solo usuarios autenticados pueden editar
   ```php
   if (!$this->ion_auth->logged_in()) { ... }
   ```

2. **Validación de entrada:** Se validan los parámetros en el servidor
   ```php
   if ($idcuota <= 0 || $nuevos_dias < 0) { ... }
   ```

3. **Escapado HTML:** Los valores se sanitizan con `htmlspecialchars()` en la vista

4. **Auditoría:** Los cambios quedan registrados en la BD con timestamps implícitos

## Troubleshooting

### "Función editDiasMora no está definida"
- Verificar que el JavaScript está incluido en `estado_cuenta.php`
- Asegurarse que jQuery está cargado

### AJAX retorna 404
- Verificar URL: `/planescredito/update_dias_mora`
- Confirmar que el método existe en el controlador

### Los cambios no persisten
- Verificar que `dias_mora_manual` y `monto_mora` existen en la tabla
- Revisar permisos de escritura en la BD

### El monto mora no se actualiza en la tabla
- Abrir consola del navegador (F12)
- Buscar errores JavaScript
- Verificar que las columnas están en el índice correcto (eq(3) para capital, eq(8) para monto mora)
