# Resumen Ejecutivo - Estructura BD CrediBlamen

## 📊 TABLAS PRINCIPALES Y KPIs

### 1️⃣ CLIENTES ACTIVOS

**Tabla:** `tb_clientes`

| Campo | Descripción |
|-------|------------|
| `idcliente` | ID único |
| `estado` | 1=Activo, 0=Inactivo |
| `rechazado` | 1=Rechazado, 0=No |

**Query:**
```sql
SELECT COUNT(*) as clientes_activos
FROM tb_clientes
WHERE estado = 1 AND rechazado = 0;
```

---

### 2️⃣ INGRESOS (Pagos Recibidos)

**Tabla Principal:** `tb_prestamo_pagos`

| Campo | Descripción | Tipo |
|-------|------------|------|
| `monto_pagado` | Monto pagado en cuota | INGRESO |
| `fecha_pago` | Fecha del pago | - |
| `idprestamo` | Referencia al préstamo | - |

**Query Completa de Ingresos:**
```sql
SELECT 
  DATE(fecha_pago) as fecha,
  COUNT(*) as num_pagos,
  SUM(monto_pagado) as total_ingresos
FROM tb_prestamo_pagos
WHERE fecha_pago BETWEEN '2026-01-01' AND '2026-12-31'
GROUP BY DATE(fecha_pago)
ORDER BY fecha DESC;

-- Ingresos por mora (también es INGRESO)
SELECT 
  SUM(COALESCE(monto_mora, 0)) as total_mora
FROM tb_prestamo_pagos
WHERE fecha_pago BETWEEN '2026-01-01' AND '2026-12-31';
```

---

### 3️⃣ EGRESOS (Desembolsos + Costos)

**Tabla Principal:** `tb_prestamos`

| Campo | Descripción | Tipo |
|-------|------------|------|
| `monto_desembolsado` | Dinero entregado al cliente | EGRESO |
| `fecha_desembolso_real` | Fecha real del desembolso | - |
| `costos_legales` | Costos legales | EGRESO |
| `seguros` | Seguros | EGRESO |
| `comisiones` | Comisiones | EGRESO |

**Query Completa de Egresos:**
```sql
SELECT 
  DATE(fecha_desembolso_real) as fecha,
  COUNT(*) as num_desembolsos,
  SUM(monto_desembolsado) as total_desembolsos,
  SUM(costos_legales) as costos_legales,
  SUM(seguros) as seguros,
  SUM(comisiones) as comisiones,
  SUM(monto_desembolsado + costos_legales + seguros + comisiones) as total_egresos
FROM tb_prestamos
WHERE fecha_desembolso_real IS NOT NULL 
  AND desembolsado = 1
  AND DATE(fecha_desembolso_real) BETWEEN '2026-01-01' AND '2026-12-31'
GROUP BY DATE(fecha_desembolso_real)
ORDER BY fecha DESC;
```

**Egresos de Caja (movimientos que no son desembolsos):**
```sql
SELECT 
  DATE(fecha_movimiento) as fecha,
  SUM(monto_movimiento) as total_egresos_caja
FROM tb_caja_movimiento
WHERE tipo_movimiento = 0  -- 0 = Egreso
  AND DATE(fecha_movimiento) BETWEEN '2026-01-01' AND '2026-12-31'
GROUP BY DATE(fecha_movimiento);
```

---

### 4️⃣ CRÉDITOS POR ESTADO

**Tabla:** `tb_prestamos`

| Estado | Valor | Descripción |
|--------|-------|------------|
| Activos | `1` | En cobro, desembolsado |
| Pagados | `0` | Completamente pagado |
| Vigentes | - | Tiene saldo > 0 |
| Vencidos | - | Cuotas con mora |

**Query por Estado:**
```sql
-- Créditos Vigentes (saldo pendiente)
SELECT 
  COUNT(*) as num_creditos_vigentes,
  SUM(total_saldo) as saldo_pendiente
FROM tb_prestamos
WHERE estado = 1 
  AND desembolsado = 1
  AND total_saldo > 0;

-- Créditos Pagados
SELECT 
  COUNT(*) as num_creditos_pagados
FROM tb_prestamos
WHERE estado = 0;

-- Créditos en Mora (clasificados)
SELECT 
  rango_mora,
  COUNT(*) as cantidad,
  SUM(total_saldo) as saldo_en_mora
FROM tb_prestamos
WHERE estado = 1 
  AND rango_mora IS NOT NULL
  AND rango_mora != ''
GROUP BY rango_mora;

-- Ejemplos de rango_mora: "0-30", "31-60", "61-90", "91-180", "180+"
```

---

### 5️⃣ ANÁLISIS DE CUOTAS

**Tabla:** `tb_prestamo_cuotas`

**Query - Cuotas Pendientes vs Pagadas:**
```sql
-- Cuotas Pendientes (no pagadas)
SELECT 
  COUNT(*) as cuotas_pendientes,
  SUM(cuota) as monto_pendiente,
  SUM(monto_mora) as mora_pendiente
FROM tb_prestamo_cuotas
WHERE saldo > 0 
  AND fecha_vencimiento < CURDATE();

-- Cuotas Pagadas
SELECT 
  COUNT(*) as cuotas_pagadas,
  SUM(principal) as capital_pagado,
  SUM(interes) as interes_pagado
FROM tb_prestamo_cuotas
WHERE saldo = 0 OR saldo IS NULL;
```

---

## 📈 DASHBOARD KPI RECOMENDADO

```sql
-- RESUMEN FINANCIERO DEL PERÍODO
SELECT 
  -- INGRESOS
  (SELECT COALESCE(SUM(monto_pagado), 0) 
   FROM tb_prestamo_pagos 
   WHERE DATE(fecha_pago) BETWEEN '2026-01-01' AND '2026-12-31') as total_ingresos_pagos,
  
  -- EGRESOS DESEMBOLSOS
  (SELECT COALESCE(SUM(monto_desembolsado), 0) 
   FROM tb_prestamos 
   WHERE DATE(fecha_desembolso_real) BETWEEN '2026-01-01' AND '2026-12-31'
   AND desembolsado = 1) as total_desembolsos,
  
  -- EGRESOS COSTOS
  (SELECT COALESCE(SUM(costos_legales + seguros + comisiones), 0) 
   FROM tb_prestamos 
   WHERE DATE(fecha_desembolso_real) BETWEEN '2026-01-01' AND '2026-12-31'
   AND desembolsado = 1) as total_costos,
  
  -- CLIENTES ACTIVOS
  (SELECT COUNT(*) FROM tb_clientes 
   WHERE estado = 1 AND rechazado = 0) as clientes_activos,
  
  -- CRÉDITOS ACTIVOS
  (SELECT COUNT(*) FROM tb_prestamos 
   WHERE estado = 1 AND desembolsado = 1) as creditos_vigentes,
  
  -- SALDO PENDIENTE
  (SELECT COALESCE(SUM(total_saldo), 0) FROM tb_prestamos 
   WHERE estado = 1) as saldo_total_pendiente,
  
  -- MORA
  (SELECT COALESCE(SUM(monto_mora), 0) FROM tb_prestamo_cuotas 
   WHERE fecha_vencimiento < CURDATE() AND saldo > 0) as monto_en_mora;
```

---

## 🔍 RELACIONES CLAVE

```
tb_clientes (idcliente)
    ↓
    └─→ tb_prestamos (monto_desembolsado = EGRESO)
            ↓
            ├─→ tb_prestamo_cuotas (cuota = INGRESO ESPERADO)
            │       ↓
            │       └─→ tb_prestamo_pagos (monto_pagado = INGRESO REAL)
            │
            └─→ tb_journal_entry (contabilización)
                    ↓
                    └─→ tb_account (clasificación contable)
```

---

## 🎯 GASTOS NO REGISTRADOS EN CRÉDITOS

**NOTA IMPORTANTE:** Los gastos operativos del negocio de CrediBlamen NO están en tabla de egresos específica.

Están en:
1. **tb_caja_movimiento** - Movimientos de caja diversos (tipo_movimiento=0 para egresos)
2. **tb_solicitudes** - Datos históricos (gastos_fijos, gastos_operativos) 
3. **tb_analisis_financiero_comerciante** - Análisis consolidado

**Query de Gastos Operativos (si se registran en caja):**
```sql
SELECT 
  DATE(fecha_movimiento) as fecha,
  descripcion_movimiento,
  SUM(monto_movimiento) as monto
FROM tb_caja_movimiento
WHERE tipo_movimiento = 0  -- Egresos
  AND DATE(fecha_movimiento) BETWEEN '2026-01-01' AND '2026-12-31'
GROUP BY DATE(fecha_movimiento), descripcion_movimiento
ORDER BY fecha DESC;
```

---

## 📋 LISTADO TABLAS RELEVANTES

| Tabla | Propósito | Campos Clave |
|-------|----------|-------------|
| `tb_clientes` | Datos clientes | idcliente, estado, rechazado |
| `tb_prestamos` | Créditos otorgados | monto_desembolsado, total_saldo, estado |
| `tb_prestamo_cuotas` | Plan de cuotas | cuota, saldo, monto_mora, fecha_vencimiento |
| `tb_prestamo_pagos` | Pagos realizados | monto_pagado, fecha_pago, monto_mora |
| `tb_caja_movimiento` | Movimientos caja | monto_movimiento, tipo_movimiento, fecha_movimiento |
| `tb_journal` | Asientos contables | total_debit, total_credit, date |
| `tb_journal_entry` | Detalle contable | debit, credit, account_id |
| `tb_solicitudes` | Solicitudes crédito | monto_solicitado, gastos_fijos, gastos_operativos |

---

## ✅ VALIDACIONES

- **Desembolso válido:** `desembolsado = 1` Y `fecha_desembolso_real IS NOT NULL`
- **Pago válido:** `monto_pagado > 0` Y `fecha_pago IS NOT NULL`
- **Cuota pendiente:** `saldo > 0` Y `fecha_vencimiento < CURDATE()`
- **Cliente activo:** `estado = 1` Y `rechazado = 0`
- **Crédito activo:** `estado = 1` Y `desembolsado = 1` Y `total_saldo > 0`
