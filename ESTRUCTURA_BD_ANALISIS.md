# Análisis de Estructura de BD - CrediBlamen

## Resumen Ejecutivo
La base de datos de CrediBlamen está organizada en un sistema de **gestión de créditos y tesorería** con dos modelos de préstamos (antiguo y nuevo), pagos, caja, contabilidad y análisis financiero.

---

## 1. TABLAS DE CLIENTES

### tb_clientes
**Propósito:** Información personal y comercial de clientes

**Campos Clave:**
- `idcliente` (PK) - Identificador único
- `apellidos`, `nombres` - Datos personales
- `numero_doc` - Número de documento
- `email`, `telefono` - Contacto
- `estado` - Estado del cliente (1=Activo, 0=Inactivo)
- `rechazado` - Flag si fue rechazado (1=Rechazado, 0=No)
- `fecha_nacimiento`, `edad`, `estado_civil`
- **INFORMACIÓN LABORAL:**
  - `nombre_empresa`, `direccion_empresa`, `cargo_puesto`
  - `ingreso_mensual_neto` - **Ingreso mensual neto (IMPORTANTE para cálculos)**
  - `tiempo_empleo_anios`, `tiempo_empleo_meses`
- **INFORMACIÓN DE NEGOCIO:**
  - `nombre_negocio`, `actividad_economica`
  - `ventas_buenos_amount`, `ventas_malos_amount`, `ventas_promedio_mensual`
  - `tiempo_operacion_anios`, `tiempo_operacion_meses`
- `condicion_vivienda`, `tiempo_residir_anios`

### tb_clientes_rechazados
**Propósito:** Historial de clientes rechazados (respaldo de tb_clientes con estado rechazado)

---

## 2. TABLAS DE SOLICITUDES

### tb_solicitudes
**Propósito:** Solicitudes de crédito con análisis financiero detallado

**Campos Clave:**
- `idsolicitud` (PK) - Identificador único
- `numero_doc` - Documento del solicitante
- `monto_solicitado` - **Monto solicitado de crédito**
- `plazo_meses` - Plazo en meses
- `tipo_credito`, `tipo_solicitud`
- `estado`, `estado_aprobacion` - Estado de aprobación (pendiente, aprobado, etc.)
- `es_nuevo`, `es_renovacion` - Tipo de solicitud
- **INGRESOS:**
  - `ingreso_promedio_alto`, `ingreso_promedio_bajo` - Ingresos del negocio
  - `otros_ingresos`, `otros_ingresos_1_amount`, `otros_ingresos_2_amount`, `otros_ingresos_3_amount`
  - `ventas_promedio_mensual`, `ventas_promedio_diarios`
  - `ventas_buenos_amount`, `ventas_malos_amount`
  - `salario_conyuge`
- **EGRESOS/GASTOS:**
  - `gastos_fijos` - **Gastos fijos mensuales (IMPORTANTE)**
  - `gastos_operativos` - **Gastos operativos mensuales (IMPORTANTE)**
  - `pago_alquiler` - Pago de alquiler
  - `pago_trabajadores` - Pago a trabajadores
  - `energia`, `agua`, `internet`
  - `energia_electrica`, `agua_potable`, `internet_telefonia`
- **ACTIVOS:**
  - `caja_amount` - Efectivo en caja
  - `banco_amount` - Saldo en banco
  - `cuentas_por_cobrar_amount` - Cuentas por cobrar
  - `inventario_disponible` - Disponibilidad de inventario
- `cuota_estim_estimada` - Cuota estimada
- `tasa_interes` - Tasa de interés propuesto
- `fecha_solicitud` - Fecha de solicitud
- `promotor` - Promotor asignado

---

## 3. TABLAS DE CRÉDITOS/PRÉSTAMOS (MODELO ANTIGUO)

### tb_creditos
**Propósito:** Créditos registrados (versión anterior/simple)

**Campos Clave:**
- `id` (PK) - Identificador único
- `idcliente` - Referencia a cliente
- `idasesor` - Asesor asignado
- `fecha_credito` - **Fecha de otorgamiento del crédito**
- `monto_credito` - **Monto total del crédito**
- `interes_credito` - Tasa de interés
- `numero_coutas` - Número de cuotas
- `monto_capital` - Monto capital
- `monto_interes` - Monto de interés
- `monto_couta` - Monto por cuota
- `total_interes` - **Total de intereses**
- `total_pagar` - **Total a pagar (capital + interés)**
- `total_saldo` - **Saldo pendiente (IMPORTANTE para calcular egresos)**
- `estado` - Estado (1=Activo, 0=Pagado/Cerrado)
- `forma_pago` - Forma de pago
- `descuento` - Descuento aplicado

### tb_credito_detalle
**Propósito:** Detalle de cuotas por crédito

**Campos Clave:**
- `id` (PK)
- `idcredito` - Referencia a crédito
- `fecha_couta` - **Fecha de vencimiento de cuota**
- `numero_couta` - Número de cuota
- `monto_capital` - Monto capital de cuota
- `monto_interes` - Monto interés de cuota
- `monto_couta` - Monto total de cuota
- `fecha_pago` - **Fecha de pago realizado**
- `monto_pagado` - **Monto pagado**
- `monto_pendiente` - **Monto pendiente de pago**
- `mora` - **Monto de mora**
- `estado_couta` - Estado (1=Pendiente, 0=Pagado)

---

## 4. TABLAS DE CRÉDITOS/PRÉSTAMOS (MODELO NUEVO)

### tb_prestamos
**Propósito:** Préstamos registrados (versión nueva con más campos)

**Campos Clave:**
- `idprestamo` (PK)
- `idsolicitud` - Referencia a solicitud
- `monto_credito` - **Monto de crédito otorgado**
- `monto_desembolsado` - **Monto realmente desembolsado (INGRESO)**
- `interes_credito` - Tasa de interés corriente anual
- `interes_moratorio` - Tasa de interés moratorio
- `comision_desembolso` - Comisión por desembolso
- `numero_coutas` - Número de cuotas
- `forma_pago` - Forma de pago
- `fecha_credito` - **Fecha de generación de crédito**
- `fecha_desembolso` - **Fecha de desembolso (IMPORTANTE - es un EGRESO)**
- `primer_dia_pago` - Primera fecha de pago
- `estado` - Estado del préstamo
- `idasesor` - Asesor asignado
- `desembolsado` - Flag si fue desembolsado (0/1)
- `obs_desembolso` - Observaciones de desembolso
- `usuario_desembolso` - Usuario que realizó desembolso
- `fecha_desembolso_real` - Fecha real de desembolso
- `emitido` - Flag si fue emitido
- `id_cheque` - ID de cheque usado para desembolso
- `costos_legales`, `seguros`, `comisiones` - **Costos asociados (EGRESOS)**
- `total_saldo` - **Saldo pendiente**
- `saldo_inicial` - Saldo inicial
- `rango_mora`, `nivel` - Clasificación de mora

### tb_prestamo_cuotas
**Propósito:** Cuotas del plan de pago de préstamos

**Campos Clave:**
- `idcuota` (PK)
- `idprestamo` - Referencia a préstamo
- `numero` - Número de cuota
- `fecha_vencimiento` - **Fecha de vencimiento de cuota**
- `dias` - Días de plazo
- `principal` - Monto principal de cuota
- `interes` - Monto de interés de cuota
- `cuota` - **Monto total de cuota (IMPORTANTE - INGRESO ESPERADO)**
- `saldo` - **Saldo después de cuota**
- `comision` - Comisión de cuota
- `dias_mora_raw`, `dias_mora_manual` - Días de mora
- `monto_mora` - **Monto de mora (INGRESO por mora)**

### tb_prestamo_pagos
**Propósito:** Registro de pagos realizados de préstamos

**Campos Clave:**
- `id` (PK)
- `idprestamo` - Referencia a préstamo
- `idcuota` - Referencia a cuota
- `idcliente` - Cliente que pagó
- `monto_pagado` - **Monto pagado (INGRESO)**
- `fecha_pago` - **Fecha de pago (IMPORTANTE)**
- `metodo_pago` - Método de pago
- `referencia` - Referencia del pago
- `dato_adicional` - Datos adicionales
- `rango_mora`, `nivel` - Clasificación de mora en pago

---

## 5. TABLAS DE PAGOS GENERALES

### tb_pagos
**Propósito:** Registro genérico de pagos (versión anterior, menos usada)

**Campos Clave:**
- `idpago` (PK)
- `fecha_pago` - **Fecha de pago**
- `idcliente` - Cliente que pagó
- `idcredito` - Crédito asociado
- `idcuota` - Cuota asociada
- `monto_pago` - **Monto de pago (INGRESO)**
- `descuento_pago` - Descuento aplicado
- `forma_pago` - Forma de pago
- `idusuario` - Usuario que registró el pago

### tb_pagos_detalle
**Propósito:** Detalle de pagos por cuota

**Campos Clave:**
- `pdid` (PK)
- `idpago` - Referencia a pago
- `idcuota` - Referencia a cuota
- `monto_pagado` - Monto pagado en esta cuota

---

## 6. TABLAS DE CAJA Y TESORERÍA

### tb_caja
**Propósito:** Control de cajas registradas

**Campos Clave:**
- `idcaja` (PK)
- `fecha_apertura` - **Fecha de apertura de caja**
- `fecha_cierre` - **Fecha de cierre de caja**
- `monto_apertura` - Monto inicial de caja
- `estado` - Estado de caja (1=Abierta, 0=Cerrada)
- `monto_movimiento` - Total de movimientos

### tb_caja_movimiento
**Propósito:** Movimientos de caja (ingresos y egresos)

**Campos Clave:**
- `idcm` (PK)
- `idcaja` - Referencia a caja
- `tipo_movimiento` - Tipo (1=Ingreso, 0=Egreso)
- `monto_movimiento` - **Monto del movimiento (IMPORTANTE)**
- `descripcion_movimiento` - Descripción
- `fecha_movimiento` - **Fecha del movimiento**
- `forma_pago` - Forma de pago
- `tipo_doc` - Tipo de documento
- `numero_doc` - Número de documento

---

## 7. TABLAS CONTABLES

### tb_journal
**Propósito:** Asientos contables del diario

**Campos Clave:**
- `id` (PK)
- `date` - **Fecha del asiento**
- `description` - Descripción del asiento
- `total_debit` - **Total débito (INGRESO contable)**
- `total_credit` - **Total crédito (EGRESO contable)**
- `source_type` - Tipo de fuente (pago, desembolso, etc.)
- `source_id` - ID de fuente
- `entry_type` - Tipo de entrada
- `centro_costo_id` - Centro de costo asociado
- `period_month`, `period_year` - Período del asiento
- `posted` - Flag si fue contabilizado
- `voided` - Flag si fue anulado

### tb_journal_entry
**Propósito:** Detalle de movimientos contables por cuenta

**Campos Clave:**
- `id` (PK)
- `journal_id` - Referencia a asiento
- `account_id` - Referencia a cuenta contable
- `debit` - **Débito (INGRESO contable)**
- `credit` - **Crédito (EGRESO contable)**
- `description` - Descripción
- `centro_costo_id` - Centro de costo

### tb_account
**Propósito:** Catálogo de cuentas contables

**Campos Clave:**
- `id` (PK)
- `code` - Código de cuenta
- `name` - Nombre de cuenta
- `type` - Tipo de cuenta
- `status` - Estado

### tb_ledger
**Propósito:** Mayor general de cuentas

**Campos Clave:**
- `id` (PK)
- `account_id` - Referencia a cuenta
- `period` - Período
- `debit` - **Débito acumulado**
- `credit` - **Crédito acumulado**
- `balance` - **Saldo final (IMPORTANTE)**

---

## 8. TABLAS DE CONFIGURACIÓN Y ANÁLISIS

### tb_centro_costo
**Propósito:** Centros de costo para asignación contable

**Campos Clave:**
- `id` (PK)
- `codigo` - Código de centro
- `nombre` - Nombre (Gerencia, Administración, Finanzas, Crédito, Cobranza)
- `activo` - Estado

### tb_analisis_financiero_asalariado
**Propósito:** Análisis financiero para clientes asalariados

**Campos Clave:**
- `ingreso_neto` - **Ingreso neto (IMPORTANTE)**
- `total_obligaciones` - Total de obligaciones (EGRESO)
- `total_gastos` - **Total de gastos (EGRESO)**
- `capacidad_pago` - Capacidad de pago

### tb_analisis_financiero_comerciante
**Propósito:** Análisis financiero para clientes comerciantes/negociantes

**Campos Clave:**
- `ventas_promedio` - **Ventas promedio (INGRESO)**
- `costo_ventas` - Costo de ventas
- `gastos_operativos` - **Gastos operativos (EGRESO)**
- `utilidad_neta` - Utilidad neta
- `capacidad_pago` - Capacidad de pago

---

## CÁLCULOS PARA KPIs

### ✅ TOTAL INGRESOS
```sql
-- Ingresos por pagos de cuotas/préstamos (ACTUAL)
SELECT SUM(monto_pagado) FROM tb_prestamo_pagos 
WHERE fecha_pago BETWEEN ? AND ?

-- O desde tb_pagos (sistema anterior)
SELECT SUM(monto_pago) FROM tb_pagos 
WHERE fecha_pago BETWEEN ? AND ?

-- Ingresos por mora
SELECT SUM(monto_mora) FROM tb_prestamo_pagos 
WHERE fecha_pago BETWEEN ? AND ?

-- Total Ingresos = Pagos de Capital + Pagos de Interés + Mora
```

### ✅ TOTAL EGRESOS
```sql
-- Desembolsos de préstamos (EGRESO)
SELECT SUM(monto_desembolsado) FROM tb_prestamos 
WHERE fecha_desembolso BETWEEN ? AND ?

-- Costos asociados a préstamos (EGRESO)
SELECT SUM(costos_legales + seguros + comisiones) FROM tb_prestamos 
WHERE fecha_desembolso BETWEEN ? AND ?

-- Movimientos de caja tipo egreso (tipo_movimiento = 0)
SELECT SUM(monto_movimiento) FROM tb_caja_movimiento 
WHERE tipo_movimiento = 0 AND fecha_movimiento BETWEEN ? AND ?

-- Total Egresos = Desembolsos + Costos + Otros Egresos de Caja
```

### ✅ CLIENTES ACTIVOS
```sql
-- Clientes con créditos activos
SELECT COUNT(DISTINCT idcliente) FROM tb_prestamos 
WHERE estado != 0 AND fecha_desembolso <= NOW()

-- O combinando ambos sistemas
SELECT COUNT(DISTINCT idcliente) FROM tb_clientes c
WHERE c.estado = 1 AND (
    SELECT COUNT(*) FROM tb_prestamos p 
    WHERE p.idsolicitud IN (
        SELECT idsolicitud FROM tb_solicitudes 
        WHERE numero_doc = c.numero_doc AND estado_aprobacion = 'aprobado'
    )
) > 0
```

### ✅ CRÉDITOS POR ESTADO
```sql
-- Créditos vigentes (activos y desembolsados)
SELECT COUNT(*), SUM(total_saldo) FROM tb_prestamos 
WHERE estado != 0 AND desembolsado = 1

-- Créditos en mora
SELECT COUNT(*), SUM(total_saldo) FROM tb_prestamos 
WHERE rango_mora IS NOT NULL AND rango_mora != ''

-- Créditos pagados/cerrados
SELECT COUNT(*) FROM tb_prestamos 
WHERE estado = 0

-- Cuotas pendientes
SELECT COUNT(*) FROM tb_prestamo_cuotas 
WHERE saldo > 0 AND fecha_vencimiento <= NOW()

-- Cuotas en mora
SELECT COUNT(*), SUM(monto_mora) FROM tb_prestamo_cuotas 
WHERE fecha_vencimiento < CURDATE() AND saldo > 0
```

### ✅ ESTADOS DE CRÉDITOS
En `tb_prestamos.estado`:
- `0` = Pagado/Cerrado
- `1` = Vigente/Activo (en cobro)
- Otros valores posibles según implementación

En `tb_prestamo_cuotas.saldo`:
- Si `saldo > 0` = Cuota pendiente
- Si `saldo = 0 o NULL` = Cuota pagada

En `tb_prestamos.rango_mora` y `nivel`:
- Valores como "0-30", "31-60", "61-90", "91-180", "180+" días

---

## TABLA DE CORRELACIÓN PARA REPORTES

| KPI | Tabla Principal | Campo Crítico | Filtro | Fórmula |
|-----|-----------------|---------------|--------|---------|
| Total Ingresos | tb_prestamo_pagos | monto_pagado | fecha_pago | SUM(monto_pagado) |
| Ingresos por Mora | tb_prestamo_pagos | monto_mora | fecha_pago | SUM(monto_mora) |
| Total Desembolsos | tb_prestamos | monto_desembolsado | fecha_desembolso | SUM(monto_desembolsado) |
| Costos Operativos | tb_prestamos | costos_legales + seguros + comisiones | fecha_desembolso | SUM(costos_legales + seguros + comisiones) |
| Clientes Activos | tb_prestamos | estado | estado != 0 | COUNT(DISTINCT idcliente) |
| Saldo Pendiente | tb_prestamos | total_saldo | estado != 0 | SUM(total_saldo) |
| Cuotas Vencidas | tb_prestamo_cuotas | saldo | fecha_vencimiento < CURDATE() AND saldo > 0 | COUNT(*) |
| Monto en Mora | tb_prestamo_cuotas | monto_mora | fecha_vencimiento < CURDATE() | SUM(monto_mora) |

---

## NOTAS IMPORTANTES

1. **Sistema Híbrido:** CrediBlamen usa DOS sistemas de créditos:
   - `tb_creditos` (antiguo/simple) - Menos usado
   - `tb_prestamos` (nuevo) - Sistema principal actual

2. **Desembolsos = Egresos:** Un desembolso en `tb_prestamos` es un EGRESO de dinero
   - Usa `fecha_desembolso_real` para fecha exacta
   - Verifica `desembolsado = 1` para confirmar que se realizó

3. **Pagos = Ingresos:** Los pagos en `tb_prestamo_pagos` son INGRESOS de dinero
   - Incluye capital, interés y mora
   - Usa `tb_prestamo_pagos` para precisión

4. **Análisis Financiero:**
   - `tb_solicitudes` contiene gastos estimados (gastos_fijos, gastos_operativos)
   - `tb_analisis_financiero_asalariado` y `_comerciante` contienen cálculos de capacidad

5. **Contabilidad:**
   - `tb_journal` es el punto de partida para reportes contables
   - `tb_journal_entry` tiene el detalle por cuenta
   - `tb_ledger` tiene saldos acumulados por período

6. **Centros de Costo:**
   - 001=Gerencia, 002=Administración, 003=Finanzas, 004=Crédito, 005=Cobranza
   - Permiten segregar egresos por departamento
