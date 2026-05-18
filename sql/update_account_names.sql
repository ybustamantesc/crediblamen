-- update_account_names.sql
-- Normalize account names to remove broken accents and standardize terms
-- Run with: mysql -u user -p your_db < update_account_names.sql

-- 1) Intereses y comisiones Creditos Vigentes (Prestamos Microcreditos)
UPDATE tb_account
SET name = 'Intereses y comisiones Creditos Vigentes (Prestamos Microcreditos)'
WHERE LOWER(name) LIKE '%intereses%comisiones%vigentes%microcr%';

-- 2) Interes y Comisiones Prestamos Microcreditos (Recargos cobrados por mora de clientes)
UPDATE tb_account
SET name = 'Interes y Comisiones Prestamos Microcreditos (Recargos cobrados por mora de clientes)'
WHERE LOWER(name) LIKE '%interes%comisiones%microcr%' AND LOWER(name) LIKE '%recargos%' ;

-- 3) Ingresos por Comisiones de Desembolsos (Prestamos Microcreditos)
UPDATE tb_account
SET name = 'Ingresos por Comisiones de Desembolsos (Prestamos Microcreditos)'
WHERE LOWER(name) LIKE '%ingresos%por%comisiones%desembolso%' AND LOWER(name) LIKE '%microcr%';

-- 4) Intereses y comisiones Creditos Vencidos (Prestamos Microcreditos)
UPDATE tb_account
SET name = 'Intereses y comisiones Creditos Vencidos (Prestamos Microcreditos)'
WHERE LOWER(name) LIKE '%intereses%comisiones%vencidos%microcr%';

-- 5) Constitucion de Provision por Cartera de Creditos
UPDATE tb_account
SET name = 'Constitucion de Provision por Cartera de Creditos'
WHERE LOWER(name) LIKE '%constituc%' AND LOWER(name) LIKE '%provision%' AND LOWER(name) LIKE '%cartera%';

-- 6) Servicios de Informacion
UPDATE tb_account
SET name = 'Servicios de Informacion'
WHERE LOWER(name) LIKE '%servicios%informaci%';

-- 7) Otros Gastos de Transporte y Comunicacion (Internet)
UPDATE tb_account
SET name = 'Otros Gastos de Transporte y Comunicacion (Internet)'
WHERE LOWER(name) LIKE '%otros%gastos%transporte%comunicaci%';

-- Preview changes (rows affected can be checked after running)
SELECT id, code, name FROM tb_account WHERE
LOWER(name) LIKE '%microcreditos%' OR LOWER(name) LIKE '%microcr%' OR LOWER(name) LIKE '%informaci%';

-- End
