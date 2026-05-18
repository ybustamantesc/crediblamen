# 💡 MEJORES PRÁCTICAS Y TIPS - Importación de Balanza

## 🎯 RESUMEN EJECUTIVO

**¿Qué acabamos de hacer?**
✅ Sistema completo de importación de cuentas contables desde Excel/CSV
✅ Clasificación automática por código de cuenta (1=Activo, 2=Pasivo, etc.)
✅ Generación automática de asiento de apertura con saldos iniciales
✅ Validación de cuadre contable (Debe = Haber)
✅ Vista previa antes de confirmar

**Acceso rápido:**
http://localhost/Servicredit/contabilidad/importar_balanza

---

## 📋 PREPARACIÓN DEL ARCHIVO

### ✅ CHECKLIST Antes de Importar

- [ ] El archivo tiene columnas: Código, Denominación, Saldo Actual
- [ ] Los códigos son únicos (no hay duplicados)
- [ ] Los códigos siguen el formato estándar (1XXXX, 2XXXX, etc.)
- [ ] Los saldos son números (sin letras, solo puntos/comas)
- [ ] La suma de Activos = Pasivos + Patrimonio
- [ ] Tienes un backup de la base de datos actual

### 📊 Formato Recomendado

**Opción A: CSV Simple (Sin dependencias)**
```csv
Código,Denominación,Saldo Actual
11010101201,CAJA PRINCIPAL C$,115107.61
21010101201,Proveedores,50000.00
31010101201,Capital Social,65107.61
```

**Opción B: Excel Completo (Requiere PhpSpreadsheet)**
```
| Código      | Denominación | Saldo Anterior | Cargos      | Abonos      | Saldo Actual |
|-------------|--------------|----------------|-------------|-------------|--------------|
| 11010101201 | CAJA         | 88074.85       | 111085.16   | 84052.40    | 115107.61    |
```

### 🔢 Códigos de Cuenta - Sistema Estándar

| Rango | Tipo | Ejemplo |
|-------|------|---------|
| 1000-1999 | **ACTIVO** | 1101 = Caja, 1102 = Bancos |
| 2000-2999 | **PASIVO** | 2101 = Proveedores, 2201 = Préstamos |
| 3000-3999 | **PATRIMONIO** | 3101 = Capital Social, 3201 = Utilidades |
| 4000-4999 | **INGRESOS** | 4101 = Intereses, 4201 = Comisiones |
| 5000-5999 | **GASTOS** | 5101 = Salarios, 5201 = Alquiler |
| 6000-6999 | **GASTOS** (alt) | 6101 = Provisiones |

---

## 🎓 ESCENARIOS COMUNES

### Escenario 1: Primera Vez - Sistema Nuevo

**Situación:** Empezar desde cero con una balanza inicial

**Pasos:**
1. Exportar balanza desde sistema anterior (Excel/CSV)
2. Verificar que cuadre: Σ Activos = Σ (Pasivos + Patrimonio)
3. Importar usando la fecha de inicio del sistema nuevo
4. Verificar en el catálogo que todas las cuentas aparecen
5. Revisar el asiento de apertura en Libro Diario

**Fecha recomendada:** 01/01/YYYY (inicio del año fiscal)

### Escenario 2: Migración de Sistema

**Situación:** Ya tienes movimientos en otro sistema y quieres migrar

**Pasos:**
1. Definir fecha de corte (ej: 31/12/2025)
2. Extraer balanza de comprobación a esa fecha
3. Importar con fecha del día siguiente (01/01/2026)
4. Continuar registrando movimientos nuevos desde esa fecha

**⚠️ Nota:** Los movimientos anteriores a la fecha de corte NO estarán detallados

### Escenario 3: Ajuste de Saldos

**Situación:** Ya importaste pero los saldos cambiaron

**Opción A - Reimportar:**
1. Eliminar asiento de apertura anterior (Libro Diario → Anular)
2. Reimportar con saldos actualizados
3. Se crearán nuevas cuentas y actualizarán existentes

**Opción B - Asiento de Ajuste:**
1. Crear asiento manual con las diferencias
2. Descripción: "Ajuste de Saldos Iniciales"

### Escenario 4: Balanza No Cuadra

**Problema:** Total Activos ≠ (Pasivos + Patrimonio)

**Causas comunes:**
- Falta una cuenta por registrar
- Error de suma en el archivo original
- Diferencia de cambio no considerada
- Cuenta transitoria no cerrada

**Solución:**
1. Identificar la diferencia: `Activos - (Pasivos + Patrimonio)`
2. Crear cuenta de ajuste:
   ```
   Código: 39999
   Nombre: Ajuste de Apertura
   Tipo: Patrimonio
   Saldo: [diferencia para cuadrar]
   ```
3. Agregar al archivo y reimportar
4. Investigar la causa de la diferencia

---

## 🔍 VALIDACIONES QUE HACE EL SISTEMA

### Durante la Carga:
- ✅ Verifica que existen columnas: Código y Denominación
- ✅ Limpia caracteres especiales de los montos
- ✅ Detecta cuentas duplicadas por código
- ✅ Clasifica automáticamente por primer dígito

### En Vista Previa:
- ✅ Muestra resumen por tipo de cuenta
- ✅ Calcula Total Debe y Total Haber
- ✅ **Alerta si no cuadra** (diferencia > 0.01)
- ✅ Indica qué cuentas ya existen

### Al Confirmar:
- ✅ Usa transacción de BD (todo o nada)
- ✅ No duplica cuentas (actualiza por código)
- ✅ Crea asiento con referencia a cada cuenta
- ✅ Verifica integridad referencial

---

## ⚠️ ERRORES COMUNES Y SOLUCIONES

### Error 1: "PhpSpreadsheet no está instalado"

**Causa:** Intentas subir Excel (.xlsx) sin la librería

**Solución:**
```bash
cd C:\xampp\htdocs\Servicredit
composer install
```

O usa formato CSV que no requiere librerías.

### Error 2: "No se encontraron columnas requeridas"

**Causa:** El archivo no tiene las columnas correctas

**Solución:**
- Verifica que la primera fila sea el encabezado
- Debe tener al menos: "Código" y "Denominación"
- Acepta variaciones: "codigo", "Código", "Code"

### Error 3: "El asiento no cuadra"

**Causa:** Suma de Activos ≠ Suma de Pasivos + Patrimonio

**Solución:**
1. Verifica en Excel: `=SUMA(Activos) - (SUMA(Pasivos) + SUMA(Patrimonio))`
2. Si hay diferencia, busca:
   - Cuentas faltantes
   - Errores de tipeo en los montos
   - Clasificación incorrecta de cuentas

### Error 4: "Error al crear el asiento de apertura"

**Causa:** Problema en la base de datos

**Solución:**
1. Verifica que existan las tablas: `tb_account`, `tb_journal`, `tb_journal_entry`
2. Ejecuta: `sql/create_tb_contabilidad.sql`
3. Verifica permisos de usuario MySQL

### Error 5: Caracteres especiales raros (�)

**Causa:** Encoding del archivo CSV

**Solución:**
- Guarda el CSV con UTF-8 en Excel:
  1. Archivo → Guardar como
  2. Tipo: CSV UTF-8 (delimitado por comas)

---

## 🚀 MEJORES PRÁCTICAS

### 1. ANTES de Importar

```sql
-- Hacer backup
CREATE TABLE tb_account_backup_YYYYMMDD AS SELECT * FROM tb_account;
CREATE TABLE tb_journal_backup_YYYYMMDD AS SELECT * FROM tb_journal;
CREATE TABLE tb_journal_entry_backup_YYYYMMDD AS SELECT * FROM tb_journal_entry;
```

### 2. DESPUÉS de Importar

Verificar:
```sql
-- 1. Contar cuentas creadas
SELECT COUNT(*) FROM tb_account;

-- 2. Ver el asiento
SELECT * FROM tb_journal ORDER BY id DESC LIMIT 1;

-- 3. Verificar cuadre
SELECT 
    SUM(debit) AS debe, 
    SUM(credit) AS haber,
    SUM(debit) - SUM(credit) AS diferencia
FROM tb_journal_entry
WHERE journal_id = (SELECT MAX(id) FROM tb_journal);
```

### 3. Nomenclatura de Cuentas

**Recomendado:**
```
Estructura: [Tipo][Grupo][Subgrupo][Cuenta]
Ejemplo: 1 1 01 01 01 201
         │ │ │  │  │  └─── Nivel detalle (Moneda C$)
         │ │ │  │  └────── Nivel 4 (Tipo de caja)
         │ │ │  └───────── Nivel 3 (Caja/Banco)
         │ │ └──────────── Nivel 2 (Efectivo)
         │ └─────────────── Nivel 1 (Activo Corriente)
         └───────────────── Tipo (1=Activo)
```

**Consistencia:**
- Usa siempre la misma longitud de código
- Define jerarquía clara (padres e hijos)
- Documenta el catálogo de cuentas

### 4. Documentación

Mantén un documento con:
- Estructura del plan de cuentas
- Códigos reservados (ej: 39999 = Ajustes)
- Reglas de clasificación
- Fecha de última actualización

---

## 📊 REPORTES POST-IMPORTACIÓN

### Balance General Inicial

```sql
SELECT 
    a.type AS 'Tipo',
    a.code AS 'Código',
    a.name AS 'Cuenta',
    IFNULL(SUM(je.debit - je.credit), 0) AS 'Saldo'
FROM tb_account a
LEFT JOIN tb_journal_entry je ON je.account_id = a.id
WHERE a.type IN ('activo', 'pasivo', 'patrimonio')
GROUP BY a.id
ORDER BY a.code;
```

### Libro Mayor por Cuenta

```sql
SELECT 
    j.date AS 'Fecha',
    j.description AS 'Descripción',
    je.debit AS 'Debe',
    je.credit AS 'Haber',
    je.description AS 'Detalle'
FROM tb_journal_entry je
JOIN tb_journal j ON j.id = je.journal_id
WHERE je.account_id = [ID_CUENTA]
ORDER BY j.date, je.id;
```

---

## 🎯 SIGUIENTES PASOS

### Inmediatos:
1. ✅ Importar la balanza de comprobación
2. ✅ Verificar que las cuentas aparecen en el catálogo
3. ✅ Revisar el asiento de apertura en Libro Diario
4. ✅ Generar Balance General para validar

### Corto Plazo:
- Configurar jerarquía de cuentas (padre-hijo)
- Crear plantillas de asientos comunes
- Configurar cierre mensual
- Definir proceso de conciliación

### Mediano Plazo:
- Integrar con módulos de préstamos/cobros
- Automatizar asientos repetitivos
- Configurar alertas de descuadres
- Implementar reportes personalizados

---

## 🔐 SEGURIDAD Y AUDITORÍA

### Permisos Recomendados

```php
// Solo permitir a usuarios autorizados
if (!$this->ion_auth->in_group(['admin', 'contador'])) {
    show_error('No autorizado');
}
```

### Log de Importaciones

Considera agregar:
```sql
CREATE TABLE tb_import_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    fecha DATETIME,
    tipo VARCHAR(50),
    registros_importados INT,
    archivo_nombre VARCHAR(255),
    asiento_id INT
);
```

---

## 📞 TROUBLESHOOTING RÁPIDO

| Síntoma | Causa Probable | Solución Rápida |
|---------|----------------|-----------------|
| No se ven las cuentas | Importación falló | Revisa logs: `application/logs/` |
| Asiento no cuadra | Balanza desbalanceada | Usa cuenta de ajuste 39999 |
| Error de permisos | Usuario sin acceso | Verifica `ion_auth` groups |
| Archivo no se sube | Límite PHP | Aumenta `upload_max_filesize` en php.ini |
| Duplicados | Código ya existe | El sistema actualiza, no duplica |

---

## ✅ CHECKLIST FINAL

Antes de considerar completada la importación:

- [ ] Todas las cuentas aparecen en el Catálogo
- [ ] El asiento de apertura está en el Libro Diario
- [ ] El asiento cuadra (Debe = Haber)
- [ ] Balance General muestra correctamente
- [ ] Ecuación contable se cumple: A = P + C
- [ ] Se hizo backup antes de importar
- [ ] Se documentó la fecha de corte
- [ ] Se verificaron al menos 5 cuentas manualmente

---

## 📚 RECURSOS ADICIONALES

### Archivos de Referencia:
- `GUIA_IMPORTACION_COMPLETA.md` - Guía paso a paso
- `IMPORTACION_BALANZA_README.md` - Documentación técnica
- `sql/verificar_importacion_balanza.sql` - Scripts de verificación
- `temp/ejemplo_balanza.csv` - Archivo de ejemplo

### URLs Útiles:
- Importar: `/contabilidad/importar_balanza`
- Catálogo: `/contabilidad/catalogo`
- Libro Diario: `/contabilidad/diario`
- Balance: `/contabilidad/balance`

---

**¿Listo para comenzar? 🚀**

Ve a: http://localhost/Servicredit/contabilidad/importar_balanza

¡Éxito en tu importación! 🎉
