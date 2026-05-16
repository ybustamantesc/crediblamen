# Importación de Balanza de Comprobación

## 📋 Descripción

Este módulo permite importar cuentas contables desde una **Balanza de Comprobación** en formato Excel o CSV, clasificarlas automáticamente y crear un **asiento de apertura** con los saldos iniciales.

## 🚀 Acceso

URL: `http://localhost/Servicredit/contabilidad/importar_balanza`

## 📊 Formato del Archivo

### Columnas Requeridas:
- **Código**: Código de la cuenta contable (ej: 11010101201)
- **Denominación**: Nombre de la cuenta
- **Saldo Actual**: Saldo final de la cuenta

### Columnas Opcionales:
- **Saldo Anterior**: Saldo inicial del período
- **Cargos**: Total de cargos en el período
- **Abonos**: Total de abonos en el período

### Ejemplo de Archivo CSV:

```csv
Código,Denominación,Saldo Anterior,Cargos,Abonos,Saldo Actual
11010101201,CAJA PRINCIPAL C$,88074.85,111085.16,84052.40,115107.61
11010101301,CAJA PRINCIPAL US,0.00,2406944.39,2406944.23,0.01
11020101301,Depósitos en Cuenta Corriente con intereses ME (LA FISE),1729674.80,2386317.58,1751407.44,2364584.94
11040101201,Caja Chica,7630.06,21481.68,21481.68,7630.06
```

Un archivo de ejemplo está disponible en: `temp/ejemplo_balanza.csv`

## 🔢 Clasificación Automática de Cuentas

Las cuentas se clasifican según el **primer dígito** de su código:

| Primer Dígito | Tipo de Cuenta |
|---------------|----------------|
| 1XXXX | **Activo** |
| 2XXXX | **Pasivo** |
| 3XXXX | **Patrimonio** |
| 4XXXX | **Ingresos** |
| 5XXXX | **Gastos** |
| 6XXXX | **Gastos** (alternativo) |

## 📝 Funcionamiento del Asiento de Apertura

El sistema crea automáticamente un asiento contable con los saldos iniciales:

### Reglas de Debe y Haber:

1. **ACTIVOS y GASTOS** con saldo positivo → van al **DEBE**
2. **PASIVOS, PATRIMONIO e INGRESOS** con saldo positivo → van al **HABER**

Ejemplo:
```
Fecha: 2026-01-09
Descripción: Asiento de Apertura - Saldos Iniciales

CUENTA                          | DEBE        | HABER
--------------------------------|-------------|------------
11010101201 - CAJA PRINCIPAL C$ | 115,107.61  |
11020101301 - Depósitos LA FISE | 2,364,584.94|
21010101201 - Proveedores       |             | 500,000.00
31010101201 - Capital Social    |             | 1,979,692.55
--------------------------------|-------------|------------
TOTALES:                        | 2,479,692.55| 2,479,692.55
```

## ⚙️ Proceso de Importación

### Paso 1: Subir Archivo
1. Seleccione su archivo Excel (.xlsx) o CSV
2. Configure la fecha del asiento de apertura
3. Personalice la descripción si lo desea
4. Click en "Subir y Analizar"

### Paso 2: Vista Previa
- Revise las cuentas detectadas
- Verifique la clasificación automática (Activo, Pasivo, etc.)
- Confirme que el asiento **CUADRA** (Debe = Haber)
- Las cuentas existentes se actualizarán (no duplicarán)

### Paso 3: Confirmación
- El sistema creará las cuentas nuevas
- Actualizará nombres de cuentas existentes
- Generará el asiento de apertura
- Mostrará el resumen de la importación

## 🔧 Instalación de Dependencias (Opcional)

Para importar archivos **Excel** (.xlsx), necesita instalar PhpSpreadsheet:

### Opción 1: Con Composer (Recomendado)
```bash
cd C:\xampp\htdocs\Servicredit
composer require phpoffice/phpspreadsheet
```

### Opción 2: Sin PhpSpreadsheet
Si no tiene Composer, puede usar archivos **CSV** que funcionan sin dependencias adicionales.

### Verificar composer.json:
```json
{
    "require": {
        "phpoffice/phpspreadsheet": "^1.29"
    }
}
```

## 📌 Notas Importantes

### ✅ Ventajas:
- Importación masiva de cuentas en segundos
- Clasificación automática inteligente
- Asiento de apertura se genera automáticamente
- Vista previa antes de confirmar
- No duplica cuentas existentes

### ⚠️ Advertencias:
- **Asegúrese que los saldos sean correctos** antes de importar
- El asiento **DEBE CUADRAR** (Debe = Haber)
- Si hay diferencia, revise los saldos en el archivo original
- Las cuentas existentes se **actualizarán**, no se eliminarán

### 🐛 Troubleshooting:

**Error: "PhpSpreadsheet no está instalado"**
- Solución: Use formato CSV o instale PhpSpreadsheet con Composer

**Error: "El asiento no cuadra"**
- Causa: Suma de Activos ≠ Suma de Pasivos + Patrimonio
- Solución: Revise los saldos en su balanza original

**Cuentas no se clasifican correctamente**
- Causa: Código de cuenta no sigue el formato estándar
- Solución: Ajuste manualmente después de importar desde el Catálogo

## 🔗 Enlaces Útiles

- Ver Catálogo: `/contabilidad/catalogo`
- Ver Libro Diario: `/contabilidad/diario`
- Ver Asientos: `/contabilidad/index`

## 💡 Ejemplo Práctico

Suponga que tiene estas cuentas en su balanza:

```
Código       | Nombre                    | Saldo
-------------|---------------------------|------------
11010101201  | CAJA PRINCIPAL           | 115,107.61
21010101201  | Proveedores              | 50,000.00
31010101201  | Capital Social           | 65,107.61
```

El sistema generará:

**Cuentas Creadas:**
- 3 cuentas nuevas clasificadas automáticamente

**Asiento de Apertura:**
```
DEBE:
  11010101201 - CAJA PRINCIPAL: 115,107.61

HABER:
  21010101201 - Proveedores: 50,000.00
  31010101201 - Capital Social: 65,107.61

Total Debe: 115,107.61
Total Haber: 115,107.61
✅ Cuadra correctamente
```

## 📞 Soporte

Si tiene problemas con la importación, verifique:
1. Formato del archivo (CSV o XLSX)
2. Columnas correctas (Código y Denominación son obligatorias)
3. Saldos numéricos (sin letras o símbolos especiales)
4. Que el asiento cuadre matemáticamente
