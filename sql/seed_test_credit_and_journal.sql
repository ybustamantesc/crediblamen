-- Seed script: create test accounts/mappings, a test credit and a disbursement journal
-- Run after your main schema is created.

-- 1) Ensure accounts exist (codes: 1000 = Caja/Banco, 1100 = Cartera préstamos, 4000 = Ingresos por interés)
INSERT INTO tb_account (`code`,`name`,`type`,`parent_id`,`created_at`)
SELECT '1000','Caja y Bancos','activo',NULL,NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tb_account WHERE code='1000');

INSERT INTO tb_account (`code`,`name`,`type`,`parent_id`,`created_at`)
SELECT '1100','Cartera de Créditos','activo',NULL,NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tb_account WHERE code='1100');

INSERT INTO tb_account (`code`,`name`,`type`,`parent_id`,`created_at`)
SELECT '4000','Ingresos por Intereses','ingreso',NULL,NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tb_account WHERE code='4000');

-- 2) Load account ids into variables
SELECT id INTO @acc_1000 FROM tb_account WHERE code='1000' LIMIT 1;
SELECT id INTO @acc_1100 FROM tb_account WHERE code='1100' LIMIT 1;
SELECT id INTO @acc_4000 FROM tb_account WHERE code='4000' LIMIT 1;

-- 3) Insert mapping rows if missing
INSERT IGNORE INTO tb_account_mapping (mapping_key, description, debit_account_id, credit_account_id, created_at)
SELECT 'loan_disbursement','Desembolso de crédito', @acc_1100, @acc_1000, NOW()
WHERE NOT EXISTS (SELECT 1 FROM tb_account_mapping WHERE mapping_key='loan_disbursement');

INSERT IGNORE INTO tb_account_mapping (mapping_key, description, debit_account_id, credit_account_id, created_at)
SELECT 'loan_payment_principal','Pago principal de crédito', @acc_1000, @acc_1100, NOW()
WHERE NOT EXISTS (SELECT 1 FROM tb_account_mapping WHERE mapping_key='loan_payment_principal');

INSERT IGNORE INTO tb_account_mapping (mapping_key, description, debit_account_id, credit_account_id, created_at)
SELECT 'loan_payment_interest','Pago interés de crédito', @acc_1000, @acc_4000, NOW()
WHERE NOT EXISTS (SELECT 1 FROM tb_account_mapping WHERE mapping_key='loan_payment_interest');

-- 4) Insert a test credit
INSERT INTO tb_creditos
(idusuario, idcliente, idasesor, fecha_credito, monto_credito, interes_credito, numero_coutas, monto_capital, monto_interes, monto_couta, total_interes, descuento, total_pagar, forma_pago, total_saldo, estado, comentarios)
VALUES
(1, 691, 1, CURDATE(), 1000.00, 10.00, 4, 250.00, 10.00, 260.00, 40.00, 0.00, 1040.00, '3', 1040.00, 1, 'Credito de prueba (seed)');

-- get the inserted credit id
SELECT LAST_INSERT_ID() INTO @test_credit_id;

-- 5) Insert credit detail (4 cuotas)
INSERT INTO tb_credito_detalle (idcredito, fecha_couta, numero_couta, monto_capital, monto_interes, monto_couta, monto_pendiente, estado_couta)
VALUES
(@test_credit_id, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 250.00, 10.00, 260.00, 260.00, 1),
(@test_credit_id, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 2, 250.00, 10.00, 260.00, 260.00, 1),
(@test_credit_id, DATE_ADD(CURDATE(), INTERVAL 90 DAY), 3, 250.00, 10.00, 260.00, 260.00, 1),
(@test_credit_id, DATE_ADD(CURDATE(), INTERVAL 120 DAY),4, 250.00, 10.00, 260.00, 260.00, 1);

-- 6) Create the disbursement journal using mapping (simulate apply_accounting_rule)
-- Get mapping
SELECT debit_account_id, credit_account_id INTO @m_debit, @m_credit FROM tb_account_mapping WHERE mapping_key='loan_disbursement' LIMIT 1;

-- Insert journal header
INSERT INTO tb_journal (`date`,`description`,`total_debit`,`total_credit`,`created_at`,`created_by`,`source_type`,`source_id`)
VALUES (CURDATE(), CONCAT('Desembolso crédito #', @test_credit_id), 1000.00, 1000.00, NOW(), 1, 'credito', @test_credit_id);
SELECT LAST_INSERT_ID() INTO @journal_id;

-- Insert entries
INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description)
VALUES
(@journal_id, @m_debit, 1000.00, 0.00, 'Desembolso - Debe (cartera)'),
(@journal_id, @m_credit, 0.00, 1000.00, 'Desembolso - Haber (caja/banco)');

-- 7) Update ledger (period YYYY-MM)
-- Ensure the @period variable uses a consistent utf8mb4 collation to avoid mixing collations
SELECT CONVERT(DATE_FORMAT(CURDATE(), '%Y-%m') USING utf8mb4) COLLATE utf8mb4_unicode_ci INTO @period;
-- debit side
INSERT INTO tb_ledger (account_id, period, debit, credit, balance)
VALUES (@m_debit, @period, 1000.00, 0.00, 1000.00)
ON DUPLICATE KEY UPDATE debit = debit + VALUES(debit), credit = credit + VALUES(credit), balance = (debit + VALUES(debit)) - (credit + VALUES(credit));
-- credit side
INSERT INTO tb_ledger (account_id, period, debit, credit, balance)
VALUES (@m_credit, @period, 0.00, 1000.00, -1000.00)
ON DUPLICATE KEY UPDATE debit = debit + VALUES(debit), credit = credit + VALUES(credit), balance = (debit + VALUES(debit)) - (credit + VALUES(credit));

-- 8) Verification selects (results will be shown by your client)
SELECT '=== JOURNAL HEADER ===' AS info;
SELECT * FROM tb_journal WHERE id = @journal_id;
SELECT '=== JOURNAL LINES ===' AS info;
SELECT * FROM tb_journal_entry WHERE journal_id = @journal_id;
SELECT '=== LEDGER ===' AS info;
-- compare using an explicit collation to avoid #1267 (mixed collations)
SELECT * FROM tb_ledger WHERE period COLLATE utf8mb4_unicode_ci = @period COLLATE utf8mb4_unicode_ci AND account_id IN (@m_debit,@m_credit);

-- End of seed
