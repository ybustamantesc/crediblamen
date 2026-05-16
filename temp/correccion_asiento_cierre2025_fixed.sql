-- Fixed adjustment journal to set accounts to Saldo Correcto
START TRANSACTION;
-- Ensure adjust account 9999 exists
INSERT INTO tb_account (`code`,`name`,`postable`,`type`,`naturaleza`,`created_at`) SELECT '9999','AJUSTE IMPORTACION',1,'MISC','acreedora',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tb_account WHERE code = '9999');

INSERT INTO tb_journal (`date`,`description`,`posted`,`posted_at`,`created_at`,`period_month`,`period_year`) VALUES ('2025-12-31','Ajuste cierre importacion 2025 - fix',1,NOW(),NOW(),12,2025);
SET @journal_id = LAST_INSERT_ID();

-- Account 14010101301
SELECT id INTO @a_id_0 FROM tb_account WHERE code = '14010101301' LIMIT 1;
SELECT IFNULL(SUM(e.debit - e.credit),0) INTO @curr_raw_0 FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = @a_id_0 AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= '2025-12-31';
SELECT CASE WHEN (SELECT type FROM tb_account WHERE id = @a_id_0) IN ('pasivo','patrimonio','ingreso') THEN -1 ELSE 1 END INTO @factor_0;
SET @curr_display_0 = ROUND(@curr_raw_0 * @factor_0,2);
SET @desired_display_0 = ROUND(7362299.58,2);
SET @diff_display_0 = ROUND(@desired_display_0 - @curr_display_0,2);
SET @raw_diff_0 = ROUND((@diff_display_0 / @factor_0),2);
-- Insert line if needed
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, @a_id_0, CASE WHEN @raw_diff_0 > 0 THEN @raw_diff_0 ELSE 0 END, CASE WHEN @raw_diff_0 < 0 THEN ABS(@raw_diff_0) ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed', 1 FROM DUAL WHERE @a_id_0 IS NOT NULL AND @raw_diff_0 != 0;

-- Account 54030901201
SELECT id INTO @a_id_1 FROM tb_account WHERE code = '54030901201' LIMIT 1;
SELECT IFNULL(SUM(e.debit - e.credit),0) INTO @curr_raw_1 FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = @a_id_1 AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= '2025-12-31';
SELECT CASE WHEN (SELECT type FROM tb_account WHERE id = @a_id_1) IN ('pasivo','patrimonio','ingreso') THEN -1 ELSE 1 END INTO @factor_1;
SET @curr_display_1 = ROUND(@curr_raw_1 * @factor_1,2);
SET @desired_display_1 = ROUND(52411.9345,2);
SET @diff_display_1 = ROUND(@desired_display_1 - @curr_display_1,2);
SET @raw_diff_1 = ROUND((@diff_display_1 / @factor_1),2);
-- Insert line if needed
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, @a_id_1, CASE WHEN @raw_diff_1 > 0 THEN @raw_diff_1 ELSE 0 END, CASE WHEN @raw_diff_1 < 0 THEN ABS(@raw_diff_1) ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed', 1 FROM DUAL WHERE @a_id_1 IS NOT NULL AND @raw_diff_1 != 0;

-- Account 54050801201
SELECT id INTO @a_id_2 FROM tb_account WHERE code = '54050801201' LIMIT 1;
SELECT IFNULL(SUM(e.debit - e.credit),0) INTO @curr_raw_2 FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = @a_id_2 AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= '2025-12-31';
SELECT CASE WHEN (SELECT type FROM tb_account WHERE id = @a_id_2) IN ('pasivo','patrimonio','ingreso') THEN -1 ELSE 1 END INTO @factor_2;
SET @curr_display_2 = ROUND(@curr_raw_2 * @factor_2,2);
SET @desired_display_2 = ROUND(88713.46,2);
SET @diff_display_2 = ROUND(@desired_display_2 - @curr_display_2,2);
SET @raw_diff_2 = ROUND((@diff_display_2 / @factor_2),2);
-- Insert line if needed
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, @a_id_2, CASE WHEN @raw_diff_2 > 0 THEN @raw_diff_2 ELSE 0 END, CASE WHEN @raw_diff_2 < 0 THEN ABS(@raw_diff_2) ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed', 1 FROM DUAL WHERE @a_id_2 IS NOT NULL AND @raw_diff_2 != 0;

-- Account 54059901201
SELECT id INTO @a_id_3 FROM tb_account WHERE code = '54059901201' LIMIT 1;
SELECT IFNULL(SUM(e.debit - e.credit),0) INTO @curr_raw_3 FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = @a_id_3 AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= '2025-12-31';
SELECT CASE WHEN (SELECT type FROM tb_account WHERE id = @a_id_3) IN ('pasivo','patrimonio','ingreso') THEN -1 ELSE 1 END INTO @factor_3;
SET @curr_display_3 = ROUND(@curr_raw_3 * @factor_3,2);
SET @desired_display_3 = ROUND(130929.8895,2);
SET @diff_display_3 = ROUND(@desired_display_3 - @curr_display_3,2);
SET @raw_diff_3 = ROUND((@diff_display_3 / @factor_3),2);
-- Insert line if needed
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, @a_id_3, CASE WHEN @raw_diff_3 > 0 THEN @raw_diff_3 ELSE 0 END, CASE WHEN @raw_diff_3 < 0 THEN ABS(@raw_diff_3) ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed', 1 FROM DUAL WHERE @a_id_3 IS NOT NULL AND @raw_diff_3 != 0;

-- Account 54059901301
SELECT id INTO @a_id_4 FROM tb_account WHERE code = '54059901301' LIMIT 1;
SELECT IFNULL(SUM(e.debit - e.credit),0) INTO @curr_raw_4 FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = @a_id_4 AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= '2025-12-31';
SELECT CASE WHEN (SELECT type FROM tb_account WHERE id = @a_id_4) IN ('pasivo','patrimonio','ingreso') THEN -1 ELSE 1 END INTO @factor_4;
SET @curr_display_4 = ROUND(@curr_raw_4 * @factor_4,2);
SET @desired_display_4 = ROUND(56855.58,2);
SET @diff_display_4 = ROUND(@desired_display_4 - @curr_display_4,2);
SET @raw_diff_4 = ROUND((@diff_display_4 / @factor_4),2);
-- Insert line if needed
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, @a_id_4, CASE WHEN @raw_diff_4 > 0 THEN @raw_diff_4 ELSE 0 END, CASE WHEN @raw_diff_4 < 0 THEN ABS(@raw_diff_4) ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed', 1 FROM DUAL WHERE @a_id_4 IS NOT NULL AND @raw_diff_4 != 0;

-- Account 63010201201
SELECT id INTO @a_id_5 FROM tb_account WHERE code = '63010201201' LIMIT 1;
SELECT IFNULL(SUM(e.debit - e.credit),0) INTO @curr_raw_5 FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = @a_id_5 AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= '2025-12-31';
SELECT CASE WHEN (SELECT type FROM tb_account WHERE id = @a_id_5) IN ('pasivo','patrimonio','ingreso') THEN -1 ELSE 1 END INTO @factor_5;
SET @curr_display_5 = ROUND(@curr_raw_5 * @factor_5,2);
SET @desired_display_5 = ROUND(1054380.75,2);
SET @diff_display_5 = ROUND(@desired_display_5 - @curr_display_5,2);
SET @raw_diff_5 = ROUND((@diff_display_5 / @factor_5),2);
-- Insert line if needed
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, @a_id_5, CASE WHEN @raw_diff_5 > 0 THEN @raw_diff_5 ELSE 0 END, CASE WHEN @raw_diff_5 < 0 THEN ABS(@raw_diff_5) ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed', 1 FROM DUAL WHERE @a_id_5 IS NOT NULL AND @raw_diff_5 != 0;

-- Compute totals and insert balancing entry to 9999
SET @totdeb = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = @journal_id);
SET @totcre = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = @journal_id);
SET @diff = ROUND(@totdeb - @totcre,2);
SELECT @totdeb AS total_debit, @totcre AS total_credit, @diff AS diff;
SELECT id INTO @adjust_id FROM tb_account WHERE code = '9999' LIMIT 1;
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id) VALUES (@journal_id, @adjust_id, CASE WHEN @diff < 0 THEN -@diff ELSE 0 END, CASE WHEN @diff > 0 THEN @diff ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed',1);

UPDATE tb_journal SET total_debit = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = @journal_id), total_credit = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = @journal_id) WHERE id = @journal_id;

COMMIT;
