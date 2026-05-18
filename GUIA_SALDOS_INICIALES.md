# Guía para Importar Saldos Iniciales

## 📋 Descripción General

Este sistema permite importar los **saldos iniciales** de las cuentas contables desde un archivo CSV, creando automáticamente un **asiento de apertura** que registra los saldos de todas las cuentas al inicio del ejercicio contable.

## 🎯 ¿Por qué es importante?

Los saldos iniciales son fundamentales porque:
- Establecen el punto de partida de tu contabilidad
- Permiten que los reportes y balances sean correctos desde el inicio
- Evitan problemas al montar otros cargadores de datos
- Garantizan que el balance general cuadre correctamente

## 📁 Archivos del Sistema

- **`importar_saldos_iniciales.php`** - Script PHP que procesa el archivo CSV
- **`importar_saldos_iniciales.html`** - Interfaz web para cargar el archivo
- **`temp/ejemplo_balanza_marzo_2025.csv`** - Archivo de ejemplo

## 🚀 Cómo Usar

### Paso 1: Preparar el Archivo CSV

Tu archivo CSV debe tener **tres columnas mínimas**:

```csv
Código,Denominación,Saldo Anterior
11010101201,CAJA PRINCIPAL C$,88074.85
11010101301,CAJA PRINCIPAL U$,-0.15
26010401201,Aportaciones Laborales Retenidas,-14952.29
```

**Columnas requeridas:**
- **Código**: Código de la cuenta contable
- **Denominación/Nombre**: Nombre descriptivo de la cuenta
- **Saldo Anterior/Saldo Inicial**: Saldo inicial de la cuenta

**Notas importantes:**
- Los números pueden tener comas (,) como separador de miles
- Los valores negativos deben llevar el signo menos (-)
- Si una celda tiene un guión (-) o está vacía, se interpreta como 0

### Paso 2: Acceder a la Interfaz

1. Abra su navegador y vaya a:
   ```
   http://localhost/Servicredit/importar_saldos_iniciales.html
   ```

2. Verá una pantalla con:
   - Campo para la **Fecha de Apertura** (por defecto: primer día del mes actual)
   - Campo para la **Descripción del Asiento**
   - Área para arrastrar o seleccionar el archivo CSV

### Paso 3: Cargar el Archivo

1. **Seleccionar fecha**: Elija la fecha del asiento de apertura (usualmente el 1 de enero del año contable)

2. **Cargar archivo**: Puede hacerlo de dos formas:
   - **Arrastrar y soltar**: Arrastre el archivo CSV sobre el área azul
   - **Hacer clic**: Haga clic en el área azul y seleccione el archivo

3. **Importar**: Haga clic en el botón "Importar Saldos Iniciales"

### Paso 4: Revisar el Resultado

Después de la importación, verá un resumen con:

✅ **Si todo salió bien:**
- ID del asiento creado
- Total de cuentas procesadas
- Cuentas creadas vs. existentes
- Total Debe y Total Haber
- Confirmación de que el asiento cuadra

❌ **Si hubo errores:**
- Descripción del error
- Sugerencias para corregir el problema

## 🔍 Lógica del Asiento de Apertura

El sistema crea un asiento contable siguiendo estas reglas:

### Cuentas de ACTIVO (código inicia con 1)
- **Saldo positivo** → Se registra en el **DEBE**
- **Saldo negativo** → Se registra en el **HABER**

### Cuentas de PASIVO (código inicia con 2)
- **Saldo positivo** → Se registra en el **HABER**
- **Saldo negativo** → Se registra en el **DEBE**

### Cuentas de PATRIMONIO (código inicia con 3)
- **Saldo positivo** → Se registra en el **HABER**
- **Saldo negativo** → Se registra en el **DEBE**

### Cuentas de INGRESO (código inicia con 4)
- **Saldo positivo** → Se registra en el **HABER**
- **Saldo negativo** → Se registra en el **DEBE**

### Cuentas de GASTO (código inicia con 5, 6 o 7)
- **Saldo positivo** → Se registra en el **DEBE**
- **Saldo negativo** → Se registra en el **HABER**

## 📊 Ejemplo Práctico

Supongamos este archivo CSV:

```csv
Código,Denominación,Saldo Anterior
11010101201,CAJA PRINCIPAL C$,88074.85
26010401201,Aportaciones Laborales,-14952.29
31010101201,Capital Social,-4500000.00
```

**Resultado del asiento:**

| Cuenta | Descripción | Debe | Haber |
|--------|-------------|------|-------|
| 11010101201 | Saldo Inicial - CAJA PRINCIPAL C$ | 88,074.85 | - |
| 26010401201 | Saldo Inicial - Aportaciones Laborales | 14,952.29 | - |
| 31010101201 | Saldo Inicial - Capital Social | - | 4,500,000.00 |
| **TOTALES** | | **103,027.14** | **4,500,000.00** |

⚠️ **Nota**: En este ejemplo el asiento NO cuadraría porque faltan cuentas para completar la ecuación contable. Un asiento de apertura completo debe tener **DEBE = HABER**.

## ✅ Verificaciones Automáticas

El sistema realiza las siguientes verificaciones:

1. ✔️ **Formato del archivo**: Verifica que sea un CSV válido
2. ✔️ **Columnas requeridas**: Confirma que existan las columnas necesarias
3. ✔️ **Creación de cuentas**: Crea automáticamente las cuentas que no existen
4. ✔️ **Balance del asiento**: Verifica que DEBE = HABER (con tolerancia de 0.01)
5. ✔️ **Integridad transaccional**: Si hay algún error, revierte toda la operación

## 🔧 Solución de Problemas

### Error: "El archivo CSV no tiene las columnas requeridas"
**Solución**: Verifique que su archivo CSV tenga las columnas: Código, Denominación y Saldo Anterior (o variaciones como "Saldo Inicial", "Nombre", etc.)

### Error: "El asiento no cuadra"
**Solución**: 
1. Revise que todas las cuentas estén incluidas en el CSV
2. Verifique que los saldos estén correctos
3. Asegúrese de que la ecuación contable esté balanceada: Activo = Pasivo + Patrimonio

### Advertencia: "Error al crear cuenta XXXXX"
**Solución**: Puede haber un problema con el código de la cuenta (caracteres especiales, duplicados, etc.). Revise el código en su CSV.

## 📝 Recomendaciones

1. **Respaldo**: Haga una copia de seguridad de su base de datos antes de importar
2. **Prueba**: Pruebe primero con un archivo pequeño para familiarizarse
3. **Verificación**: Después de importar, consulte el asiento en el módulo de contabilidad
4. **Fecha correcta**: Use la fecha de inicio del ejercicio contable
5. **Orden**: Importe los saldos iniciales ANTES de cargar otros movimientos

## 🗄️ Estructura de la Base de Datos

El sistema utiliza las siguientes tablas:

- **`tb_account`**: Catálogo de cuentas contables
  - `id`: ID de la cuenta
  - `code`: Código de la cuenta
  - `name`: Nombre de la cuenta
  - `type`: Tipo (activo, pasivo, patrimonio, ingreso, gasto)

- **`tb_journal`**: Encabezados de asientos contables
  - `id`: ID del asiento
  - `date`: Fecha del asiento
  - `description`: Descripción
  - `total_debit`: Total debe
  - `total_credit`: Total haber

- **`tb_journal_entry`**: Líneas de los asientos
  - `journal_id`: ID del asiento
  - `account_id`: ID de la cuenta
  - `debit`: Monto al debe
  - `credit`: Monto al haber
  - `description`: Descripción de la línea

## 🔗 Próximos Pasos

Después de importar los saldos iniciales:

1. ✅ Verificar el asiento en el módulo de contabilidad
2. ✅ Generar un balance de comprobación inicial
3. ✅ Proceder con la importación de otros movimientos (balanza mensual, etc.)
4. ✅ Realizar conciliaciones y ajustes si es necesario

## 💡 Ejemplo de Uso Completo

```bash
# 1. Prepare su archivo CSV con los saldos iniciales
# 2. Acceda a la interfaz web
# 3. Seleccione la fecha: 2025-01-01
# 4. Cargue el archivo: saldos_iniciales_2025.csv
# 5. Haga clic en "Importar"
# 6. Revise el resultado
# 7. Verifique el asiento creado en el sistema
```

## 📞 Soporte

Si tiene problemas o preguntas:
1. Revise esta guía completa
2. Verifique la sección de solución de problemas
3. Consulte los logs de error del sistema
4. Contacte al administrador del sistema

---

**Versión**: 1.0  
**Fecha**: Enero 2025  
**Sistema**: Servicredit - Módulo de Contabilidad
