# 📅 GUÍA: Importación de Balanzas con Períodos Mensuales

## 🎯 ACTUALIZACIÓN IMPORTANTE

El sistema ahora soporta **importaciones múltiples por período**, permitiendo:

✅ Cargar balanzas de **diferentes meses**  
✅ Cada mes genera su **propio asiento contable**  
✅ Los **filtros por fecha** funcionan correctamente  
✅ Puedes hacer **cierres mensuales** automáticos  

---

## 📋 NUEVO FLUJO DE TRABAJO

### Paso 1: Preparar Balanzas por Mes

Organiza tus archivos por período:

```
📁 Balanzas_2026/
  ├── balanza_enero_2026.csv
  ├── balanza_febrero_2026.csv
  ├── balanza_marzo_2026.csv
  └── ...
```

### Paso 2: Importar Mes por Mes

Para cada mes, sigue este proceso:

#### 🗓️ **ENERO 2026 - Apertura Inicial**

1. Ve a: http://localhost/Servicredit/contabilidad/importar_balanza
2. Configura:
   - **Período:** Enero 2026
   - **Fecha del Asiento:** 31/01/2026 (último día del mes)
   - **Tipo:** Asiento de Apertura
   - **Archivo:** balanza_enero_2026.csv
3. La descripción se genera automáticamente: _"Asiento de Apertura - Saldos Iniciales Enero 2026"_

**Resultado:**
- ✅ Crea todas las cuentas
- ✅ Genera Asiento #1 con fecha 31/01/2026

#### 🗓️ **FEBRERO 2026 - Cierre Mensual**

1. Configura:
   - **Período:** Febrero 2026
   - **Fecha del Asiento:** 28/02/2026
   - **Tipo:** Cierre Mensual
   - **Archivo:** balanza_febrero_2026.csv
2. Descripción automática: _"Cierre Mensual - Febrero 2026"_

**Resultado:**
- ✅ Actualiza nombres de cuentas (si cambiaron)
- ✅ Genera Asiento #2 con fecha 28/02/2026
- ✅ **NO duplica cuentas** existentes

#### 🗓️ **MARZO 2026 y siguientes...**

Repite el proceso para cada mes subsiguiente.

---

## 🎨 TIPOS DE IMPORTACIÓN

El sistema ofrece 3 tipos de importación:

### 1. **Asiento de Apertura**
- **Cuándo usar:** Primera importación del sistema
- **Descripción:** "Asiento de Apertura - Saldos Iniciales [Mes] [Año]"
- **Propósito:** Establecer saldos iniciales

### 2. **Cierre Mensual**
- **Cuándo usar:** Importaciones mensuales subsecuentes
- **Descripción:** "Cierre Mensual - [Mes] [Año]"
- **Propósito:** Registrar saldos al final del mes

### 3. **Ajuste de Saldos**
- **Cuándo usar:** Correcciones o ajustes puntuales
- **Descripción:** "Ajuste de Saldos - [Mes] [Año]"
- **Propósito:** Corregir errores o hacer ajustes especiales

---

## 📊 FORMATO DEL ARCHIVO (Sin cambios)

El CSV sigue el mismo formato:

```csv
Código,Denominación,Saldo Anterior,Cargos,Abonos,Saldo Actual
11010101201,CAJA PRINCIPAL C$,100000.00,50000.00,30000.00,120000.00
```

**Nota:** Las columnas de Saldo Anterior, Cargos y Abonos son **opcionales** pero recomendadas para auditoría.

---

## 🔍 VENTAJAS DEL SISTEMA CON PERÍODOS

### ✅ Reportes por Fecha

Los reportes ahora funcionan correctamente con filtros de fecha:

**Balanza de Comprobación:**
```
Filtro: Del 01/01/2026 al 31/01/2026
Resultado: Solo muestra movimientos de enero
```

**Libro Mayor:**
```
Filtro: Del 01/02/2026 al 28/02/2026
Resultado: Muestra movimientos de febrero
```

**Estado de Resultados:**
```
Filtro: Del 01/01/2026 al 31/03/2026
Resultado: Ingresos y gastos del trimestre
```

### ✅ Asientos Independientes

Cada mes tiene su propio asiento:

```sql
SELECT * FROM tb_journal ORDER BY date;

| ID | Fecha      | Descripción                                    |
|----|------------|------------------------------------------------|
| 1  | 2026-01-31 | Asiento de Apertura - Saldos Iniciales Enero  |
| 2  | 2026-02-28 | Cierre Mensual - Febrero 2026                  |
| 3  | 2026-03-31 | Cierre Mensual - Marzo 2026                    |
```

### ✅ Trazabilidad

Puedes identificar fácilmente de qué período viene cada movimiento:

```sql
-- Ver asientos por período
SELECT 
    j.date,
    j.description,
    COUNT(je.id) as lineas,
    SUM(je.debit) as total_debe,
    SUM(je.credit) as total_haber
FROM tb_journal j
LEFT JOIN tb_journal_entry je ON je.journal_id = j.id
WHERE j.description LIKE '%2026%'
GROUP BY j.id
ORDER BY j.date;
```

---

## 📝 EJEMPLO PRÁCTICO COMPLETO

### Escenario: Importar 3 meses

#### **MES 1 - ENERO 2026 (Apertura)**

**Archivo:** `ejemplo_balanza_enero_2026.csv`
```csv
Código,Denominación,Saldo Actual
11010101201,CAJA PRINCIPAL C$,100000.00
21010101201,Proveedores,200000.00
31010101201,Capital Social,598000.00
```

**Configuración:**
- Período: Enero 2026
- Fecha Asiento: 31/01/2026
- Tipo: Asiento de Apertura

**Genera:**
```
ASIENTO #1 - 31/01/2026
Descripción: Asiento de Apertura - Saldos Iniciales Enero 2026

DEBE:
  11010101201 - CAJA: 100,000.00

HABER:
  21010101201 - Proveedores: 200,000.00
  31010101201 - Capital: 598,000.00
```

#### **MES 2 - FEBRERO 2026 (Cierre)**

**Archivo:** `ejemplo_balanza_febrero_2026.csv`
```csv
Código,Denominación,Saldo Actual
11010101201,CAJA PRINCIPAL C$,150000.00
21010101201,Proveedores,250000.00
31010101201,Capital Social,598000.00
41010101201,Ingresos por Intereses,162000.00
```

**Configuración:**
- Período: Febrero 2026
- Fecha Asiento: 28/02/2026
- Tipo: Cierre Mensual

**Genera:**
```
ASIENTO #2 - 28/02/2026
Descripción: Cierre Mensual - Febrero 2026

DEBE:
  11010101201 - CAJA: 150,000.00

HABER:
  21010101201 - Proveedores: 250,000.00
  31010101201 - Capital: 598,000.00
  41010101201 - Ingresos: 162,000.00
```

**Nota:** La cuenta de Ingresos se crea automáticamente.

#### **MES 3 - MARZO 2026**

Similar proceso para marzo...

---

## ⚙️ CONFIGURACIÓN AUTOMÁTICA DE FECHAS

El sistema sugiere automáticamente:

- **Fecha del asiento:** Último día del mes seleccionado
- **Descripción:** Generada según tipo y período

Ejemplo al seleccionar "Febrero 2026":
- Fecha sugerida: 28/02/2026 (o 29 si es bisiesto)
- Descripción: "Cierre Mensual - Febrero 2026"

Puedes modificar manualmente si lo necesitas.

---

## 🔄 PROCESO RECOMENDADO

### Para Migración Inicial:

1. **Mes 1:** Importar como "Asiento de Apertura"
2. **Meses siguientes:** Importar como "Cierre Mensual"

### Para Operación Normal:

1. Al final de cada mes, exporta la balanza de tu sistema actual
2. Importa usando tipo "Cierre Mensual"
3. Verifica el asiento generado
4. Los reportes se actualizan automáticamente

---

## 🛠️ SCRIPTS DE CONSULTA ÚTILES

### Ver todos los asientos por mes:

```sql
SELECT 
    YEAR(date) as anio,
    MONTH(date) as mes,
    description,
    total_debit,
    total_credit
FROM tb_journal
WHERE description LIKE '%Cierre%' OR description LIKE '%Apertura%'
ORDER BY date;
```

### Verificar saldos mensuales:

```sql
SELECT 
    a.code,
    a.name,
    SUM(je.debit - je.credit) as saldo
FROM tb_account a
LEFT JOIN tb_journal_entry je ON je.account_id = a.id
LEFT JOIN tb_journal j ON j.id = je.journal_id
WHERE j.date <= '2026-02-28'  -- Fecha de corte
GROUP BY a.id
ORDER BY a.code;
```

### Listar asientos de un mes específico:

```sql
SELECT * FROM tb_journal
WHERE YEAR(date) = 2026 AND MONTH(date) = 2
ORDER BY date;
```

---

## ⚠️ CONSIDERACIONES IMPORTANTES

### ✅ Cosas que SÍ hace el sistema:

- ✅ Genera un asiento NUEVO para cada importación
- ✅ No duplica cuentas (actualiza por código)
- ✅ Respeta las fechas para filtros en reportes
- ✅ Mantiene trazabilidad por período
- ✅ Permite múltiples importaciones sin conflicto

### ❌ Cosas que NO hace:

- ❌ NO elimina asientos anteriores automáticamente
- ❌ NO valida si ya existe un asiento del mismo mes (permite duplicados si reimportas)
- ❌ NO genera movimientos intermedios (solo registra saldos finales)

### 🔧 Si necesitas reimportar un mes:

1. Elimina el asiento anterior manualmente:
   - Libro Diario → Buscar asiento → Anular/Eliminar
2. Importa nuevamente con los datos correctos

---

## 📈 EJEMPLO DE FLUJO ANUAL

```
Enero 2026   →  Asiento #1  (Apertura)     →  31/01/2026
Febrero 2026 →  Asiento #2  (Cierre)       →  28/02/2026
Marzo 2026   →  Asiento #3  (Cierre)       →  31/03/2026
Abril 2026   →  Asiento #4  (Cierre)       →  30/04/2026
...
Diciembre    →  Asiento #12 (Cierre Anual) →  31/12/2026
```

Cada asiento es independiente y permite reportes por rango de fechas.

---

## 🎯 BENEFICIOS

### Para el Contador:
- ✅ Cierre mensual automatizado
- ✅ Trazabilidad completa por período
- ✅ Reportes precisos por fecha
- ✅ Auditoría facilitada

### Para la Empresa:
- ✅ Estados financieros mensuales
- ✅ Análisis de tendencias
- ✅ Comparativos período a período
- ✅ Cumplimiento normativo

---

## 📞 PREGUNTAS FRECUENTES

### ¿Puedo importar meses en desorden?

**Sí.** El sistema no valida el orden cronológico. Puedes importar Marzo antes que Febrero si lo necesitas.

### ¿Qué pasa si importo el mismo mes dos veces?

Se generarán **dos asientos diferentes** con la misma fecha. Debes eliminar el incorrecto manualmente.

### ¿Puedo cambiar la fecha del asiento después?

No directamente desde la importación, pero puedes editarlo en el Libro Diario.

### ¿Los reportes suman correctamente con múltiples asientos?

**Sí.** El sistema suma todos los movimientos dentro del rango de fechas seleccionado.

---

## 📁 ARCHIVOS DE EJEMPLO INCLUIDOS

- `temp/ejemplo_balanza.csv` - Balanza general
- `temp/ejemplo_balanza_enero_2026.csv` - Enero (apertura)
- `temp/ejemplo_balanza_febrero_2026.csv` - Febrero (con ingresos)

Usa estos como plantilla para tus archivos.

---

## ✅ CHECKLIST DE IMPORTACIÓN MENSUAL

- [ ] Exportar balanza del mes desde sistema actual
- [ ] Verificar que cuadra (Activos = Pasivos + Patrimonio)
- [ ] Configurar período correcto (mes y año)
- [ ] Seleccionar fecha (último día del mes recomendado)
- [ ] Elegir tipo de importación (Cierre Mensual)
- [ ] Revisar vista previa
- [ ] Confirmar importación
- [ ] Verificar asiento generado en Libro Diario
- [ ] Generar reportes para validar

---

## 🚀 ¡LISTO PARA USAR!

El sistema ahora está preparado para manejar **cierres mensuales automáticos** y **múltiples períodos**.

**URL:** http://localhost/Servicredit/contabilidad/importar_balanza

¡Empieza a importar tus balanzas mes por mes! 🎉
