-- =====================================================
-- SCRIPT DE VERIFICACIÓN Y LIMPIEZA
-- Importación de Balanza de Comprobación
-- =====================================================

-- 1. VER CUENTAS CREADAS
-- =====================================================
SELECT 
    id,
    code AS 'Código',
    name AS 'Nombre',
    type AS 'Tipo',
    parent_id AS 'Padre',
    created_at AS 'Creada'
FROM tb_account
ORDER BY code;

-- 2. VER ASIENTO DE APERTURA
-- =====================================================
SELECT 
    id,
    date AS 'Fecha',
    description AS 'Descripción',
    total_debit AS 'Total Debe',
    total_credit AS 'Total Haber',
    (total_debit - total_credit) AS 'Diferencia'
FROM tb_journal
WHERE description LIKE '%Apertura%'
   OR description LIKE '%Saldos Iniciales%'
ORDER BY date DESC;

-- 3. VER DETALLE DEL ASIENTO DE APERTURA
-- =====================================================
SELECT 
    je.id,
    a.code AS 'Código',
    a.name AS 'Cuenta',
    a.type AS 'Tipo',
    je.debit AS 'Debe',
    je.credit AS 'Haber',
    je.description AS 'Descripción'
FROM tb_journal_entry je
JOIN tb_account a ON a.id = je.account_id
WHERE je.journal_id = (
    SELECT id 
    FROM tb_journal 
    WHERE description LIKE '%Apertura%'
       OR description LIKE '%Saldos Iniciales%'
    ORDER BY date DESC 
    LIMIT 1
)
ORDER BY je.debit DESC, je.credit DESC;

-- 4. VERIFICAR CUADRE DEL ASIENTO
-- =====================================================
SELECT 
    SUM(debit) AS 'Total Debe',
    SUM(credit) AS 'Total Haber',
    (SUM(debit) - SUM(credit)) AS 'Diferencia'
FROM tb_journal_entry
WHERE journal_id = (
    SELECT id 
    FROM tb_journal 
    WHERE description LIKE '%Apertura%'
       OR description LIKE '%Saldos Iniciales%'
    ORDER BY date DESC 
    LIMIT 1
);

-- 5. RESUMEN POR TIPO DE CUENTA
-- =====================================================
SELECT 
    a.type AS 'Tipo de Cuenta',
    COUNT(*) AS 'Cantidad',
    SUM(IFNULL(je.debit, 0)) AS 'Total Debe',
    SUM(IFNULL(je.credit, 0)) AS 'Total Haber',
    SUM(IFNULL(je.debit, 0) - IFNULL(je.credit, 0)) AS 'Saldo Neto'
FROM tb_account a
LEFT JOIN tb_journal_entry je ON je.account_id = a.id
    AND je.journal_id = (
        SELECT id 
        FROM tb_journal 
        WHERE description LIKE '%Apertura%'
           OR description LIKE '%Saldos Iniciales%'
        ORDER BY date DESC 
        LIMIT 1
    )
GROUP BY a.type
ORDER BY 
    CASE a.type
        WHEN 'activo' THEN 1
        WHEN 'pasivo' THEN 2
        WHEN 'patrimonio' THEN 3
        WHEN 'ingreso' THEN 4
        WHEN 'gasto' THEN 5
        ELSE 6
    END;

-- 6. VERIFICAR ECUACIÓN CONTABLE
-- =====================================================
-- Debe cumplirse: ACTIVOS = PASIVOS + PATRIMONIO
SELECT 
    'Verificación de Ecuación Contable' AS 'Concepto',
    SUM(CASE WHEN a.type = 'activo' THEN (IFNULL(je.debit, 0) - IFNULL(je.credit, 0)) ELSE 0 END) AS 'Total Activos',
    SUM(CASE WHEN a.type = 'pasivo' THEN (IFNULL(je.credit, 0) - IFNULL(je.debit, 0)) ELSE 0 END) AS 'Total Pasivos',
    SUM(CASE WHEN a.type = 'patrimonio' THEN (IFNULL(je.credit, 0) - IFNULL(je.debit, 0)) ELSE 0 END) AS 'Total Patrimonio',
    (
        SUM(CASE WHEN a.type = 'pasivo' THEN (IFNULL(je.credit, 0) - IFNULL(je.debit, 0)) ELSE 0 END) +
        SUM(CASE WHEN a.type = 'patrimonio' THEN (IFNULL(je.credit, 0) - IFNULL(je.debit, 0)) ELSE 0 END)
    ) AS 'Pasivo + Patrimonio',
    (
        SUM(CASE WHEN a.type = 'activo' THEN (IFNULL(je.debit, 0) - IFNULL(je.credit, 0)) ELSE 0 END) -
        (
            SUM(CASE WHEN a.type = 'pasivo' THEN (IFNULL(je.credit, 0) - IFNULL(je.debit, 0)) ELSE 0 END) +
            SUM(CASE WHEN a.type = 'patrimonio' THEN (IFNULL(je.credit, 0) - IFNULL(je.debit, 0)) ELSE 0 END)
        )
    ) AS 'Diferencia (debe ser 0)'
FROM tb_account a
LEFT JOIN tb_journal_entry je ON je.account_id = a.id
    AND je.journal_id = (
        SELECT id 
        FROM tb_journal 
        WHERE description LIKE '%Apertura%'
           OR description LIKE '%Saldos Iniciales%'
        ORDER BY date DESC 
        LIMIT 1
    );

-- =====================================================
-- SCRIPTS DE LIMPIEZA (USAR CON PRECAUCIÓN)
-- =====================================================

-- 7. ELIMINAR EL ÚLTIMO ASIENTO DE APERTURA
-- =====================================================
-- ⚠️ ADVERTENCIA: Esto eliminará el asiento y sus líneas
-- Descomenta las siguientes líneas solo si estás seguro:

/*
DELETE FROM tb_journal 
WHERE id = (
    SELECT id FROM (
        SELECT id 
        FROM tb_journal 
        WHERE description LIKE '%Apertura%'
           OR description LIKE '%Saldos Iniciales%'
        ORDER BY date DESC 
        LIMIT 1
    ) AS tmp
);
*/

-- 8. ELIMINAR TODAS LAS CUENTAS SIN MOVIMIENTOS
-- =====================================================
-- ⚠️ ADVERTENCIA: Elimina cuentas que no tienen asientos
-- Útil si quieres limpiar antes de reimportar
-- Descomenta solo si estás seguro:

/*
DELETE FROM tb_account 
WHERE id NOT IN (
    SELECT DISTINCT account_id 
    FROM tb_journal_entry
);
*/

-- 9. LIMPIAR TODO Y EMPEZAR DE CERO
-- =====================================================
-- ⚠️⚠️⚠️ EXTREMA PRECAUCIÓN ⚠️⚠️⚠️
-- Esto eliminará TODOS los datos contables
-- SOLO usar en ambiente de prueba o si estás completamente seguro
-- Descomenta solo si estás seguro:

/*
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE tb_journal_entry;
TRUNCATE TABLE tb_journal;
TRUNCATE TABLE tb_account;
SET FOREIGN_KEY_CHECKS = 1;
*/

-- 10. BACKUP ANTES DE LIMPIAR
-- =====================================================
-- Siempre haz backup antes de eliminar datos
-- Ejemplo de backup:

/*
CREATE TABLE tb_account_backup AS SELECT * FROM tb_account;
CREATE TABLE tb_journal_backup AS SELECT * FROM tb_journal;
CREATE TABLE tb_journal_entry_backup AS SELECT * FROM tb_journal_entry;
*/

-- 11. RESTAURAR DESDE BACKUP
-- =====================================================
-- Si necesitas restaurar:

/*
INSERT INTO tb_account SELECT * FROM tb_account_backup;
INSERT INTO tb_journal SELECT * FROM tb_journal_backup;
INSERT INTO tb_journal_entry SELECT * FROM tb_journal_entry_backup;
*/

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
