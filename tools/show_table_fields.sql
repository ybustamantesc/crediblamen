-- tools/show_table_fields.sql
-- Usage: replace `your_database` and `your_table` or run with -D database
-- Example: & 'C:\xampp\mysql\bin\mysql.exe' -u root -D "crediblamen.db" -e "SOURCE tools/show_table_fields.sql;" 

-- Replace these two identifiers as needed
USE `your_database`; -- e.g. `crediblamen.db`

-- Show detailed column information
SHOW FULL COLUMNS FROM `your_table`;

-- Show full CREATE TABLE statement
SHOW CREATE TABLE `your_table`;
