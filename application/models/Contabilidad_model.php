<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contabilidad_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ensure tb_account (or current account table) has `is_mayor` column.
     * Returns true if column exists or was created, false on error.
     */
    public function ensure_is_mayor_column()
    {
        $acct_fq = $this->account_table_fq();
        // if column exists already, nothing to do
        if ($this->safe_field_exists('is_mayor', $acct_fq)) return true;
        // try to add column to the physical table name
        $tbl = $this->account_table_name();
        $sql = "ALTER TABLE `" . $this->db->escape_str($tbl) . "` ADD COLUMN `is_mayor` TINYINT(1) NOT NULL DEFAULT 0";
        $prev = isset($this->db->db_debug) ? $this->db->db_debug : TRUE;
        $this->db->db_debug = FALSE;
        try {
            $this->db->query($sql);
            $this->db->db_debug = $prev;
            return true;
        } catch (Exception $e) {
            $this->db->db_debug = $prev;
            return false;
        }
    }

    /**
     * Update a single account identified by code setting its is_mayor flag (0/1).
     * Returns number of affected rows or false on error.
     */
    public function set_account_is_mayor_by_code($code, $is_mayor)
    {
        $tbl = $this->account_table_name();
        $data = ['is_mayor' => $is_mayor ? 1 : 0];
        $this->db->where('code', $code);
        $res = $this->db->update($tbl, $data);
        if ($res) return $this->db->affected_rows();
        return false;
    }

    /**
     * Return the account table name to use. Prefer `tb_account`, fallback to `b_account`.
     */
    protected function account_table_name()
    {
        if ($this->db->table_exists('tb_account')) {
            if ($this->safe_field_exists('name', 'tb_account') && $this->safe_field_exists('type', 'tb_account')) {
                return 'tb_account';
            }
        }
        if ($this->db->table_exists('b_account')) return 'b_account';
        if ($this->db->table_exists('teso_accounts')) return 'teso_accounts';
        return 'tb_account';
    }

    /**
     * Return a fully-qualified account table name suitable for SQL FROM clauses.
     * If the account table exists in the current DB return the simple name,
     * otherwise search `information_schema` for a schema that contains b_account
     * and return `schema.table` so queries can read from the other database
     * without changing the default connection.
     */
    protected function account_table_fq()
    {
        // prefer healthy tb_account in current DB if it appears to have rows
        if ($this->db->table_exists('tb_account') && $this->safe_field_exists('name', 'tb_account') && $this->safe_field_exists('type', 'tb_account')) {
            $cnt = $this->safe_row_count('tb_account');
            if ($cnt > 0) return 'tb_account';
            // otherwise prefer b_account if available elsewhere
        }

        // try to find b_account across schemas
        $sql = "SELECT table_schema FROM information_schema.tables WHERE table_name = 'b_account' LIMIT 1";
        $q = $this->db->query($sql);
        if ($q && $q->row()) {
            $schema = $q->row()->table_schema;
            // if it's the current database, just return table name
            $curr = $this->db->database;
            if ($schema == $curr) return 'b_account';
            return "`" . $schema . "`.b_account";
        }

        // fallback to tb_account name
        return 'tb_account';
    }

    /**
     * Return a safe row count for a table (supports schema-qualified names).
     * Returns 0 on error.
     */
    protected function safe_row_count($table)
    {
        $prev = isset($this->db->db_debug) ? $this->db->db_debug : TRUE;
        $this->db->db_debug = FALSE;
        try {
            if (strpos($table, '.') !== false) {
                list($schema, $tbl) = explode('.', $table, 2);
                // remove surrounding backticks if present
                $schema = trim($schema, "` ");
                $tbl = trim($tbl, "` ");
                $sql = "SELECT COUNT(*) as cnt FROM `" . $this->db->escape_str($schema) . "`.`" . $this->db->escape_str($tbl) . "`";
            } else {
                $tbl = trim($table, "` ");
                $sql = "SELECT COUNT(*) as cnt FROM `" . $this->db->escape_str($tbl) . "`";
            }
            $q = $this->db->query($sql);
            if ($q && $q->row()) {
                $cnt = intval($q->row()->cnt);
                $this->db->db_debug = $prev;
                return $cnt;
            }
        } catch (Exception $e) {
            // ignore
        }
        $this->db->db_debug = $prev;
        return 0;
    }

    /**
     * Check whether a table has a given column but avoid throwing DB debug errors
     * if the table is corrupted/missing. Returns boolean.
     */
    protected function safe_field_exists($field, $table)
    {
        $prev = isset($this->db->db_debug) ? $this->db->db_debug : TRUE;
        $this->db->db_debug = FALSE;
        try {
            // Support schema-qualified table names: schema.table
            if (strpos($table, '.') !== false) {
                list($schema, $tbl) = explode('.', $table, 2);
                // strip backticks/spaces if the table name was returned quoted
                $schema = trim($schema, "` ");
                $tbl = trim($tbl, "` ");
                $sql = "SHOW COLUMNS FROM `" . $this->db->escape_str($schema) . "`.`" . $this->db->escape_str($tbl) . "`";
            } else {
                $tbl = trim($table, "` ");
                $sql = "SHOW COLUMNS FROM `" . $this->db->escape_str($tbl) . "`";
            }
            $q = $this->db->query($sql);
            if (!$q) {
                $this->db->db_debug = $prev;
                return FALSE;
            }
            $cols = $q->result_array();
            foreach ($cols as $c) {
                if (isset($c['Field']) && $c['Field'] == $field) {
                    $this->db->db_debug = $prev;
                    return TRUE;
                }
            }
            $this->db->db_debug = $prev;
            return FALSE;
        } catch (Exception $e) {
            $this->db->db_debug = $prev;
            return FALSE;
        }
    }

    /**
     * Return a SELECT fragment that normalizes potentially-misnamed
     * columns (some legacy/corrupted tables have `ame`/`ype` instead
     * of `name`/`type`). Use with table alias (e.g. 'a').
     */
    protected function account_select_cols($alias = 'a')
    {
        $a = $alias ? ($alias . '.') : '';
        // determine which physical account table to inspect (fully-qualified when needed)
        $acct_fq = $this->account_table_fq();
        // decide which physical column names exist (supports schema-qualified names)
        $nameCol = $this->safe_field_exists('name', $acct_fq) ? 'name' : 'ame';
        // support several possible type column names (legacy/corrupted)
        if ($this->safe_field_exists('type', $acct_fq)) {
            $typeCol = 'type';
        } elseif ($this->safe_field_exists('account_type', $acct_fq)) {
            $typeCol = 'account_type';
        } else {
            $typeCol = 'ype';
        }
        
        // Include naturaleza if exists
        $naturalezaCol = '';
        if ($this->safe_field_exists('naturaleza', $acct_fq)) {
            $naturalezaCol = ', ' . $a . 'naturaleza';
        }
        // Include explicit level/is_mayor if present
        $levelCol = '';
        if ($this->safe_field_exists('level', $acct_fq)) {
            $levelCol = ', ' . $a . 'level';
        }
        $isMayorCol = '';
        if ($this->safe_field_exists('is_mayor', $acct_fq)) {
            $isMayorCol = ', ' . $a . 'is_mayor';
        }
        // Include report_type/report_is/report_bs if exists
        $reportCol = '';
        if ($this->safe_field_exists('report_type', $acct_fq)) {
            $reportCol .= ', ' . $a . 'report_type';
        }
        if ($this->safe_field_exists('report_is', $acct_fq)) {
            $reportCol .= ', ' . $a . 'report_is';
        }
        if ($this->safe_field_exists('report_bs', $acct_fq)) {
            $reportCol .= ', ' . $a . 'report_bs';
        }
        
        return $a . "id, " . $a . "code, " . $a . $nameCol . " as name, " . $a . $typeCol . " as type" . $naturalezaCol . $reportCol . $levelCol . $isMayorCol . ", " . $a . "parent_id";
    }

    /**
     * Same as account_select_cols but without alias prefix (for simple FROM selects).
     */
    protected function account_select_cols_noalias()
    {
        // determine which physical account table to inspect (fully-qualified when needed)
        $acct_fq = $this->account_table_fq();
        $nameCol = $this->safe_field_exists('name', $acct_fq) ? 'name' : 'ame';
        if ($this->safe_field_exists('type', $acct_fq)) {
            $typeCol = 'type';
        } elseif ($this->safe_field_exists('account_type', $acct_fq)) {
            $typeCol = 'account_type';
        } else {
            $typeCol = 'ype';
        }
        
        // Include naturaleza if exists
        $naturalezaCol = '';
        if ($this->safe_field_exists('naturaleza', $acct_fq)) {
            $naturalezaCol = ', naturaleza';
        }
        $levelCol = '';
        if ($this->safe_field_exists('level', $acct_fq)) {
            $levelCol = ', level';
        }
        $isMayorCol = '';
        if ($this->safe_field_exists('is_mayor', $acct_fq)) {
            $isMayorCol = ', is_mayor';
        }
        // Include report_type/report_is/report_bs if exists
        $reportCol = '';
        if ($this->safe_field_exists('report_type', $acct_fq)) {
            $reportCol .= ', report_type';
        }
        if ($this->safe_field_exists('report_is', $acct_fq)) {
            $reportCol .= ', report_is';
        }
        if ($this->safe_field_exists('report_bs', $acct_fq)) {
            $reportCol .= ', report_bs';
        }
        
        return "id, code, " . $nameCol . " as name, " . $typeCol . " as type" . $naturalezaCol . $reportCol . $levelCol . $isMayorCol . ", parent_id";
    }

    // Obtener journals (simplificado)
    public function get_journals($start_date = null, $end_date = null)
    {
        // Build select dynamically based on existing columns
        $select = 'j.id, j.date, j.description, j.total_debit, j.total_credit';
        
        // Add voided columns if they exist
        if ($this->safe_field_exists('voided', 'tb_journal')) {
            $select .= ', j.voided, j.voided_by, j.voided_at';
        } else {
            $select .= ', 0 as voided, NULL as voided_by, NULL as voided_at';
        }
        
        // Add document_type or entry_type if exists, and keep compatibility
        if ($this->safe_field_exists('document_type', 'tb_journal')) {
            $select .= ', j.document_type as document_type';
            if ($this->safe_field_exists('entry_type', 'tb_journal')) {
                $select .= ', j.entry_type';
            } else {
                $select .= ', j.document_type as entry_type';
            }
        } elseif ($this->safe_field_exists('entry_type', 'tb_journal')) {
            $select .= ', j.entry_type, j.entry_type as document_type';
        } else {
            $select .= ', "CD" as entry_type, "CD" as document_type';
        }
        
        // Add posted (mayorizado) columns if they exist
        if ($this->safe_field_exists('posted', 'tb_journal')) {
            $select .= ', j.posted, j.posted_by, j.posted_at';
        } else {
            $select .= ', 0 as posted, NULL as posted_by, NULL as posted_at';
        }
        
        // Get unique centro_costo from journal entry lines
        $select .= ', GROUP_CONCAT(DISTINCT CONCAT(cc.codigo, " - ", cc.nombre) SEPARATOR ", ") as centro_costo_nombres';
        $select .= ', GROUP_CONCAT(DISTINCT je.centro_costo_id) as centro_costo_ids';
        
        $this->db->select($select);
        $this->db->from('tb_journal as j');
        
        // Join journal entries to get centro_costo from lines
        $this->db->join('tb_journal_entry as je', 'je.journal_id = j.id', 'left');
        
        // Join centro_costo if field exists in journal_entry
        if ($this->db->field_exists('centro_costo_id', 'tb_journal_entry')) {
            $this->db->join('tb_centro_costo as cc', 'cc.id = je.centro_costo_id', 'left');
        }

        if ($start_date) {
            $this->db->where('DATE(j.date) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(j.date) <=', $end_date);
        }
        
        $this->db->group_by('j.id');
        $this->db->order_by('j.date', 'desc');
        $this->db->order_by('j.id', 'desc');
        $q = $this->db->get();
        return $q->result();
    }

    /**
     * Return journal lines for the given journal IDs, including account metadata.
     *
     * @param array $entry_ids
     * @return array
     */
    public function get_journal_lines_by_entry_ids(array $entry_ids)
    {
        $entry_ids = array_filter(array_map('intval', $entry_ids));
        if (empty($entry_ids)) {
            return [];
        }

        $acct = $this->account_table_name();
        $nameCol = $this->db->field_exists('name', $acct) ? 'name' : 'ame';
        $accountTypeCol = $this->db->field_exists('type', $acct) ? 'a.type as account_type,' : '';

        $select = 'j.id as journal_id, j.date, j.description as journal_description, ';
        if ($this->db->field_exists('entry_type', 'tb_journal')) {
            $select .= 'j.entry_type, ';
        } elseif ($this->db->field_exists('document_type', 'tb_journal')) {
            $select .= 'j.document_type as entry_type, ';
        } else {
            $select .= '"CD" as entry_type, ';
        }
        $select .= 'e.id as line_id, e.debit, e.credit, ';
        if ($this->db->field_exists('debit_usd', 'tb_journal_entry') && $this->db->field_exists('credit_usd', 'tb_journal_entry')) {
            $select .= 'e.debit_usd, e.credit_usd, ';
        }
        $select .= 'COALESCE(NULLIF(e.description, ""), j.description) as line_description, a.code as account_code, a.' . $nameCol . ' as account_name, ' . $accountTypeCol;
        if ($this->db->field_exists('centro_costo_id', 'tb_journal_entry')) {
            $select .= 'e.centro_costo_id, cc.codigo as centro_costo_codigo, cc.nombre as centro_costo_nombre, ';
        }
        $select = rtrim($select, ', ');

        $this->db->select($select);
        $this->db->from('tb_journal_entry e');
        $this->db->join('tb_journal j', 'j.id = e.journal_id', 'left');
        $this->db->join($acct . ' a', 'a.id = e.account_id', 'left');
        if ($this->db->field_exists('centro_costo_id', 'tb_journal_entry')) {
            $this->db->join('tb_centro_costo cc', 'cc.id = e.centro_costo_id', 'left');
        }
        $this->db->where_in('e.journal_id', $entry_ids);
        $this->db->order_by('j.date', 'asc');
        $this->db->order_by('j.id', 'asc');
        $this->db->order_by('e.id', 'asc');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Return list of monetary accounts that should be considered for FX revaluation.
     * This looks for accounts that are marked as monetary (by name/code pattern or report flags),
     * and that are denominated in a foreign currency (we assume USD by default).
     *
     * Returns array of accounts with keys: id, code, name, currency (e.g. 'USD'), current_balance_local, balance_foreign
     */
    public function get_monetary_accounts_for_revaluation()
    {
        // Heuristic: tb_account may have a column `currency` or `moneda`. If present use it.
        $acct = $this->account_table_fq();
        $select = $this->account_select_cols('a');
        // add currency column if exists
        if ($this->safe_field_exists('currency', $acct)) {
            $select .= ', a.currency';
        } elseif ($this->safe_field_exists('moneda', $acct)) {
            $select .= ', a.moneda as currency';
        } else {
            // no explicit currency column; we'll try to infer by account code or name containing "USD" or "dólar"
            $select .= ', NULL as currency';
        }

        // attempt to get balances using tb_ledger or tb_journal_entry aggregation
        $sql = "SELECT " . $select . " FROM " . $acct . " as a WHERE 1=1";
        $rows = $this->db->query($sql)->result_array();

        $out = [];
        foreach ($rows as $r) {
            // Only consider asset ('activo') and liability ('pasivo') accounts for revaluation
            $acct_type = isset($r['type']) ? strtolower(trim($r['type'])) : '';
            if (!in_array($acct_type, ['activo', 'pasivo'])) continue;
            $currency = isset($r['currency']) && $r['currency'] ? strtoupper($r['currency']) : null;
            // infer if null: code or name contains USD or DOLAR
            if (!$currency) {
                $code = isset($r['code']) ? $r['code'] : '';
                $name = isset($r['name']) ? strtolower($r['name']) : '';
                if (stripos($name, 'dólar') !== false || stripos($name, 'dolar') !== false || stripos($name, 'usd') !== false || stripos($code, 'USD') !== false) {
                    $currency = 'USD';
                }
            }
            if ($currency !== 'USD') continue; // only USD for now

            // Compute balances: prefer tb_ledger if exists
            $balance_local = 0.0;
            $balance_foreign = 0.0;
            if ($this->db->table_exists('tb_ledger')) {
                $q = $this->db->select('SUM(debit) as deb, SUM(credit) as cre')->where('account_id', intval($r['id']))->get('tb_ledger');
                if ($q && $q->row()) {
                    $balance_local = floatval($q->row()->deb) - floatval($q->row()->cre);
                }
            } else {
                // fallback: aggregate journal_entry amounts
                $q = $this->db->select('SUM(debit) as deb, SUM(credit) as cre')->where('account_id', intval($r['id']))->get('tb_journal_entry');
                if ($q && $q->row()) {
                    $balance_local = floatval($q->row()->deb) - floatval($q->row()->cre);
                }
            }

            // If there's a stored foreign amount column (e.g., amount_foreign) try to use it (best-effort)
            if ($this->safe_field_exists('amount_foreign', 'tb_ledger')) {
                $q2 = $this->db->select('SUM(amount_foreign) as amt')->where('account_id', intval($r['id']))->get('tb_ledger');
                if ($q2 && $q2->row()) $balance_foreign = floatval($q2->row()->amt);
            }

            $out[] = [
                'id' => intval($r['id']),
                'code' => $r['code'] ?? null,
                'name' => $r['name'] ?? null,
                'currency' => $currency,
                'current_balance_local' => $balance_local,
                'balance_foreign' => $balance_foreign
            ];
        }
        return $out;
    }

    /**
     * Calculate revaluation for a given USD account given a new exchange rate.
     * Params: $account_id, $new_rate (local per 1 USD), optional $as_of_date
     * Returns array: opening_local, revalued_local, difference (revalued - opening)
     * Note: this function does not write anything to DB; it only computes values.
     */
    public function calculate_revaluation_for_account($account_id, $new_rate, $as_of_date = null)
    {
        $account_id = intval($account_id);
        // Determine foreign balance if available
        $balance_foreign = 0.0;
        if ($this->db->table_exists('tb_ledger') && $this->safe_field_exists('amount_foreign', 'tb_ledger')) {
            $q = $this->db->select('SUM(amount_foreign) as amt')->where('account_id', $account_id)->get('tb_ledger');
            if ($q && $q->row()) $balance_foreign = floatval($q->row()->amt);
        }
        // If no explicit foreign amount, try to infer from local amounts divided by original exchange rate
        if (abs($balance_foreign) < 0.000001) {
            // try to use tb_journal_entry.amount_foreign
            if ($this->safe_field_exists('amount_foreign', 'tb_journal_entry')) {
                $q = $this->db->select('SUM(amount_foreign) as amt')->where('account_id', $account_id)->get('tb_journal_entry');
                if ($q && $q->row()) $balance_foreign = floatval($q->row()->amt);
            }
        }

        // opening local balance (current local recorded balance)
        $balance_local = 0.0;
        if ($this->db->table_exists('tb_ledger')) {
            $q = $this->db->select('SUM(debit) as deb, SUM(credit) as cre')->where('account_id', $account_id)->get('tb_ledger');
            if ($q && $q->row()) $balance_local = floatval($q->row()->deb) - floatval($q->row()->cre);
        } else {
            $q = $this->db->select('SUM(debit) as deb, SUM(credit) as cre')->where('account_id', $account_id)->get('tb_journal_entry');
            if ($q && $q->row()) $balance_local = floatval($q->row()->deb) - floatval($q->row()->cre);
        }

        // If we don't have balance_foreign but we have local and original rate, best-effort: try to find last stored rate in tb_tasa_cambio
        if (abs($balance_foreign) < 0.000001) {
            // try to retrieve an implied original rate if entries recorded with an `amount_foreign` field at insert time are not present
            // fallback: assume foreign balance = local / latest_rate
            $this->db->select('tasa_cambio');
            $this->db->order_by('fecha', 'DESC');
            $this->db->limit(1);
            $t = $this->db->get('tb_tasa_cambio')->row();
            $latest_rate = ($t && isset($t->tasa_cambio)) ? floatval($t->tasa_cambio) : 36.50;
            if ($latest_rate > 0) $balance_foreign = $balance_local / $latest_rate;
        }

        // Re-valued local amount using new_rate
        $revalued_local = $balance_foreign * floatval($new_rate);
        $difference = $revalued_local - $balance_local;

        return [
            'account_id' => $account_id,
            'opening_local' => $balance_local,
            'balance_foreign' => $balance_foreign,
            'revalued_local' => $revalued_local,
            'difference' => $difference
        ];
    }

    /**
     * Ensure the period lock table exists. Creates `tb_period_lock` if missing.
     */
    public function ensure_period_lock_table()
    {
        if ($this->db->table_exists('tb_period_lock')) return true;
        $sql = "CREATE TABLE IF NOT EXISTS `tb_period_lock` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `year` int(4) NOT NULL,
            `month` int(2) NOT NULL,
            `closed_by` int(11) DEFAULT NULL,
            `closed_at` datetime DEFAULT NULL,
            `notes` text DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `year_month` (`year`,`month`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        return $this->db->query($sql);
    }

    /**
     * Return true if the given year/month is closed.
     * Params: either pass ($year, $month) or a date string as $year.
     */
    public function is_period_closed($year_or_date, $month = null)
    {
        $this->ensure_period_lock_table();
        if ($month === null) {
            // interpret first param as date string
            $ts = strtotime($year_or_date);
            if ($ts === false) return false;
            $y = intval(date('Y', $ts));
            $m = intval(date('n', $ts));
        } else {
            $y = intval($year_or_date);
            $m = intval($month);
        }
        $q = $this->db->where('year', $y)->where('month', $m)->get('tb_period_lock');
        return ($q && $q->row()) ? true : false;
    }

    /**
     * Close a period (insert or update record).
     */
    public function close_period($year, $month, $user_id = null, $notes = null)
    {
        $this->ensure_period_lock_table();
        $y = intval($year); $m = intval($month);
        $exists = $this->db->where('year', $y)->where('month', $m)->get('tb_period_lock')->row();
        $data = ['year'=>$y,'month'=>$m,'closed_by'=>$user_id,'closed_at'=>date('Y-m-d H:i:s'),'notes'=>$notes];
        if ($exists) {
            return $this->db->where('id', $exists->id)->update('tb_period_lock', $data);
        }
        return $this->db->insert('tb_period_lock', $data);
    }

    /**
     * Open (unlock) a closed period.
     */
    public function open_period($year, $month)
    {
        $this->ensure_period_lock_table();
        $y = intval($year); $m = intval($month);
        return $this->db->where('year', $y)->where('month', $m)->delete('tb_period_lock');
    }

    /**
     * Return list of closed periods.
     */
    public function get_closed_periods()
    {
        $this->ensure_period_lock_table();
        $q = $this->db->order_by('year','desc')->order_by('month','desc')->get('tb_period_lock');
        return $q ? $q->result_array() : [];
    }

    // Obtener lista simple de cuentas para selects (id, code, name, type)
    public function get_accounts()
    {
        // devolver también parent_id y parent_name para facilitar renderizado
        $acct_fq = $this->account_table_fq();
        $nameCol = $this->safe_field_exists('name', $acct_fq) ? 'name' : 'ame';
        $this->db->select($this->account_select_cols('a') . ', p.' . $nameCol . ' as parent_name');
        $this->db->from($acct_fq . ' as a');
        $this->db->join($acct_fq . ' as p', 'p.id = a.parent_id', 'left');
        $this->db->order_by('a.code', 'asc');
        $q = $this->db->get();
        return $q->result();
    }

    // Obtener cuentas con saldo (balance = sum(debit - credit))
    public function get_accounts_with_balance()
    {
        $acct_fq = $this->account_table_fq();
        $nameCol = $this->safe_field_exists('name', $acct_fq) ? 'name' : 'ame';
        if ($this->safe_field_exists('type', $acct_fq)) {
            $typeCol = 'type';
        } elseif ($this->safe_field_exists('account_type', $acct_fq)) {
            $typeCol = 'account_type';
        } else {
            $typeCol = 'ype';
        }

        // if journal entries are available in the current DB, compute balances using only posted entries
        // but always return the account row even if it only has unposted entries (balance will be 0)
        if ($this->db->table_exists('tb_journal_entry') && $this->safe_field_exists('debit', 'tb_journal_entry')) {
            $selectCols = $this->account_select_cols('a');
            // sum only includes amounts from posted, non-voided journals; other entries contribute 0
            $sql = "SELECT " . $selectCols . ", IFNULL(SUM(CASE WHEN j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) THEN (e.debit - e.credit) ELSE 0 END),0) as balance
                FROM " . $acct_fq . " a
                LEFT JOIN tb_journal_entry e ON e.account_id = a.id
                LEFT JOIN tb_journal j ON j.id = e.journal_id
                GROUP BY a.id
                ORDER BY a.code ASC";
            $q = $this->db->query($sql);
            return $q->result();
        }

        // journal entries not available or corrupted: return accounts with zero balance.
        $acct = $this->account_table_name();
        // use a safe SELECT that references proper name/type column names
        $selectCols = str_replace('a.', 'a.', $this->account_select_cols('a')) . ', 0 as balance';
        $sql2 = "SELECT " . $selectCols . " FROM " . $acct . " as a ORDER BY a.code ASC";
        $q2 = $this->db->query($sql2);
        return $q2->result();
    }

    /**
     * Same as get_accounts_with_balance but returns a paginated slice and total count.
     * Returns array with keys: 'rows' => array, 'total' => int
     */
    public function get_accounts_with_balance_paginated($limit = 25, $offset = 0)
    {
        $acct = $this->account_table_name();
        $acct_fq = $this->account_table_fq();
        $nameCol = $this->safe_field_exists('name', $acct_fq) ? 'name' : 'ame';
        if ($this->safe_field_exists('type', $acct_fq)) {
            $typeCol = 'type';
        } elseif ($this->safe_field_exists('account_type', $acct_fq)) {
            $typeCol = 'account_type';
        } else {
            $typeCol = 'ype';
        }

        // total count
        $total = $this->safe_row_count($acct_fq);

        // if journal entries exist, compute balances with grouping and pagination - ONLY POSTED ENTRIES
        if ($this->db->table_exists('tb_journal_entry') && $this->safe_field_exists('debit', 'tb_journal_entry')) {
            $selectCols = $this->account_select_cols('a');
            $sql = "SELECT " . $selectCols . ", IFNULL(SUM(CASE WHEN j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) THEN (e.debit - e.credit) ELSE 0 END),0) as balance
                FROM " . $acct_fq . " a
                LEFT JOIN tb_journal_entry e ON e.account_id = a.id
                LEFT JOIN tb_journal j ON j.id = e.journal_id
                GROUP BY a.id
                ORDER BY a.code ASC
                LIMIT ? OFFSET ?";
            $q = $this->db->query($sql, array(intval($limit), intval($offset)));
            $rows = $q->result();
            return ['rows' => $rows, 'total' => intval($total)];
        }

        // no journal entries: return zero balances but apply limit/offset
        $selectCols = $this->account_select_cols('a') . ", 0 as balance";
        $sql2 = "SELECT " . $selectCols . " FROM " . $acct . " as a ORDER BY a.code ASC LIMIT ? OFFSET ?";
        $q2 = $this->db->query($sql2, array(intval($limit), intval($offset)));
        $rows2 = $q2->result();
        return ['rows' => $rows2, 'total' => intval($total)];
    }

    /**
     * Search accounts by code or name (case-insensitive LIKE). Returns array of rows.
     */
    public function search_accounts($q, $limit = 200)
    {
        $q = trim($q);
        if ($q === '') return [];
        $acct_fq = $this->account_table_fq();
        $nameCol = $this->safe_field_exists('name', $acct_fq) ? 'name' : 'ame';
        // choose type column
        if ($this->safe_field_exists('type', $acct_fq)) {
            $typeCol = 'type';
        } elseif ($this->safe_field_exists('account_type', $acct_fq)) {
            $typeCol = 'account_type';
        } else {
            $typeCol = 'ype';
        }

        // Determine whether query looks like a code (digits, dots, dashes)
        $is_code = preg_match('/^[0-9\.\-]+$/', $q);
        if ($is_code) {
            // match code prefix for incremental typing (e.g. '14' matches '1408...')
            $codeParam = $this->db->escape_like_str($q) . '%';
            $nameParam = '%' . $this->db->escape_like_str($q) . '%';
            $sql = "SELECT " . $this->account_select_cols('a') . ", 0 as balance FROM " . $acct_fq . " as a WHERE a.code LIKE ? OR a." . $nameCol . " LIKE ? ORDER BY a.code ASC LIMIT ?";
            $qobj = $this->db->query($sql, array($codeParam, $nameParam, intval($limit)));
        } else {
            // general text search: match anywhere in code or name
            $param = '%' . $this->db->escape_like_str($q) . '%';
            $sql = "SELECT " . $this->account_select_cols('a') . ", 0 as balance FROM " . $acct_fq . " as a WHERE a.code LIKE ? OR a." . $nameCol . " LIKE ? ORDER BY a.code ASC LIMIT ?";
            $qobj = $this->db->query($sql, array($param, $param, intval($limit)));
        }
        if (!$qobj) return [];
        return $qobj->result();
    }

    // Obtener balanza de comprobación (trial balance) por cuenta.
    // $start/$end opcionales: YYYY-MM-DD. $account_id opcional para filtrar una sola cuenta.
    public function get_trial_balance($start = null, $end = null, $account_id = null, $include_zero = false, $group_prefix_len = null, $group_mode = 'prefix', $only_mayor = false)
    {
        // get accounts (if account_id provided, filter)
        $acct = $this->account_table_name();
        $acct_fq = $this->account_table_fq();
        $this->db->select($this->account_select_cols_noalias());
        // alias table as 'a' so we can reference it for parent/child checks
        $this->db->from($acct . ' as a');
        if ($account_id) $this->db->where('a.id', intval($account_id));
        // if caller requests only 'mayor' accounts, prefer using explicit is_mayor column if present
        if (!empty($only_mayor)) {
            if ($this->safe_field_exists('is_mayor', $acct_fq) || $this->safe_field_exists('is_mayor', $acct)) {
                $this->db->where('a.is_mayor', 1);
            } else {
                // fallback: accounts that have children
                $this->db->where("EXISTS (SELECT 1 FROM " . $acct . " c WHERE c.parent_id = a.id)", null, false);
            }
        }
        $this->db->order_by('a.code', 'asc');
        $accounts = $this->db->get()->result();

        $rows = [];
        $totals = [
            'opening_deudor' => 0, 'opening_acreedor' => 0,
            'debits' => 0, 'credits' => 0,
            'closing_deudor' => 0, 'closing_acreedor' => 0
        ];

        foreach ($accounts as $a) {
            $aid = intval($a->id);
            // Exclude internal adjustment account 9999 from trial balance reports
            if (isset($a->code) && trim($a->code) === '9999') {
                continue;
            }
            // opening: sum before start - ONLY POSTED ENTRIES
            if ($start) {
                $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as opening_raw FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.date < ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0)";
                $q = $this->db->query($sql, array($aid, $start));
                $opening_raw = floatval($q->row()->opening_raw);
            } else {
                $opening_raw = 0.0;
            }
            // period debits/credits - ONLY POSTED ENTRIES
            $sql2 = "SELECT IFNULL(SUM(e.debit),0) as debits, IFNULL(SUM(e.credit),0) as credits FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0)";
            $params = array($aid);
            if ($start) { $sql2 .= " AND j.date >= ?"; $params[] = $start; }
            if ($end) { $sql2 .= " AND j.date <= ?"; $params[] = $end; }
            $q2 = $this->db->query($sql2, $params);
            $debits = floatval($q2->row()->debits);
            $credits = floatval($q2->row()->credits);

            // closing raw = opening_raw + debits - credits
            $closing_raw = $opening_raw + $debits - $credits;

            // normalize by account type (activo/gasto = debit increases; pasivo/patrimonio/ingreso invert)
            $acct_type = strtolower($a->type);
            $factor = 1;
            if (in_array($acct_type, ['pasivo','patrimonio','ingreso'])) $factor = -1;

            $opening_display = $opening_raw * $factor;
            $closing_display = $closing_raw * $factor;

            // split into Deudor/Acreedor columns
            $opening_deudor = $opening_display >= 0 ? $opening_display : 0.0;
            $opening_acreedor = $opening_display < 0 ? abs($opening_display) : 0.0;
            $closing_deudor = $closing_display >= 0 ? $closing_display : 0.0;
            $closing_acreedor = $closing_display < 0 ? abs($closing_display) : 0.0;

            // Filtrar cuentas sin movimiento: omitir si todos los valores son cero (a menos que se pida incluirlas)
            if (!$include_zero) {
                if ($opening_deudor == 0 && $opening_acreedor == 0 && $debits == 0 && $credits == 0 && $closing_deudor == 0 && $closing_acreedor == 0) {
                    continue; // Saltar esta cuenta, no tiene movimientos
                }
            }

            $rows[] = [
                'id' => $aid,
                'code' => $a->code,
                'name' => $a->name,
                'type' => $a->type,
                'opening_raw' => $opening_raw,
                'opening_deudor' => $opening_deudor,
                'opening_acreedor' => $opening_acreedor,
                'debits' => $debits,
                'credits' => $credits,
                'closing_raw' => $closing_raw,
                'closing_deudor' => $closing_deudor,
                'closing_acreedor' => $closing_acreedor
            ];

            // accumulate totals
            $totals['opening_deudor'] += $opening_deudor;
            $totals['opening_acreedor'] += $opening_acreedor;
            $totals['debits'] += $debits;
            $totals['credits'] += $credits;
            $totals['closing_deudor'] += $closing_deudor;
            $totals['closing_acreedor'] += $closing_acreedor;
        }

        // If grouping requested, support two modes: 'prefix' (code prefix) and 'level' (ancestor level)
        if ($group_prefix_len && intval($group_prefix_len) > 0) {
            $gparam = intval($group_prefix_len);
            if ($group_mode === 'level') {
                // Build accounts map by id for parent traversal
                $accounts_map = [];
                foreach ($accounts as $ac) {
                    $accounts_map[intval($ac->id)] = [
                        'parent_id' => isset($ac->parent_id) ? intval($ac->parent_id) : null,
                        'code' => isset($ac->code) ? $ac->code : '',
                        'name' => isset($ac->name) ? $ac->name : ''
                    ];
                }

                // depth cache
                $depth_cache = [];
                $get_depth = function($id) use (&$get_depth, &$accounts_map, &$depth_cache) {
                    if (!isset($accounts_map[$id])) return 1;
                    if (isset($depth_cache[$id])) return $depth_cache[$id];
                    $p = $accounts_map[$id]['parent_id'];
                    if (!$p || !isset($accounts_map[$p])) { $depth_cache[$id] = 1; return 1; }
                    $d = $get_depth($p) + 1;
                    $depth_cache[$id] = $d;
                    return $d;
                };

                $level = max(1, $gparam);
                $agg = [];
                $agg_totals = ['opening_deudor' => 0, 'opening_acreedor' => 0, 'debits' => 0, 'credits' => 0, 'closing_deudor' => 0, 'closing_acreedor' => 0];

                foreach ($rows as $r) {
                    $aid = isset($r['id']) ? intval($r['id']) : null;
                    $group_key = null; $group_name = null;
                    if ($aid && isset($accounts_map[$aid])) {
                        $d = $get_depth($aid);
                        $cur = $aid;
                        while ($d > $level && isset($accounts_map[$cur]) && $accounts_map[$cur]['parent_id']) {
                            $cur = $accounts_map[$cur]['parent_id'];
                            $d--;
                        }
                        // now $cur is ancestor at requested level (or root)
                        if (isset($accounts_map[$cur])) {
                            $group_key = $accounts_map[$cur]['code'] ?: (string)$cur;
                            $group_name = $accounts_map[$cur]['name'] ?: ('MAYOR ' . $group_key);
                        }
                    }
                    // fallback: use code prefix of length gparam if ancestor not found
                    if (!$group_key) {
                        $code = isset($r['code']) ? (string)$r['code'] : '';
                        $group_key = strlen($code) > $gparam ? substr($code, 0, $gparam) : $code;
                        $group_name = 'AGRUPADO ' . $group_key;
                    }

                    if (!isset($agg[$group_key])) {
                        $agg[$group_key] = [
                            'id' => null,
                            'code' => $group_key,
                            'name' => $group_name,
                            'type' => null,
                            'opening_raw' => 0,
                            'opening_deudor' => 0,
                            'opening_acreedor' => 0,
                            'debits' => 0,
                            'credits' => 0,
                            'closing_raw' => 0,
                            'closing_deudor' => 0,
                            'closing_acreedor' => 0
                        ];
                    }
                    $agg[$group_key]['opening_raw'] += floatval($r['opening_raw'] ?? 0);
                    $agg[$group_key]['opening_deudor'] += floatval($r['opening_deudor'] ?? 0);
                    $agg[$group_key]['opening_acreedor'] += floatval($r['opening_acreedor'] ?? 0);
                    $agg[$group_key]['debits'] += floatval($r['debits'] ?? 0);
                    $agg[$group_key]['credits'] += floatval($r['credits'] ?? 0);
                    $agg[$group_key]['closing_raw'] += floatval($r['closing_raw'] ?? 0);
                    $agg[$group_key]['closing_deudor'] += floatval($r['closing_deudor'] ?? 0);
                    $agg[$group_key]['closing_acreedor'] += floatval($r['closing_acreedor'] ?? 0);

                    $agg_totals['opening_deudor'] += floatval($r['opening_deudor'] ?? 0);
                    $agg_totals['opening_acreedor'] += floatval($r['opening_acreedor'] ?? 0);
                    $agg_totals['debits'] += floatval($r['debits'] ?? 0);
                    $agg_totals['credits'] += floatval($r['credits'] ?? 0);
                    $agg_totals['closing_deudor'] += floatval($r['closing_deudor'] ?? 0);
                    $agg_totals['closing_acreedor'] += floatval($r['closing_acreedor'] ?? 0);
                }

                ksort($agg);
                $newRows = [];
                foreach ($agg as $p => $entry) { $newRows[] = $entry; }
                return ['rows' => $newRows, 'totals' => $agg_totals];
            } else {
                // prefix mode (backwards compatible)
                $plen = $gparam;
                // build map of account code -> account name for title lookup
                $codeNameMap = [];
                foreach ($accounts as $ac) { $codeNameMap[isset($ac->code) ? $ac->code : ''] = (isset($ac->name) ? $ac->name : ''); }
                $agg = [];
                $agg_totals = ['opening_deudor' => 0, 'opening_acreedor' => 0, 'debits' => 0, 'credits' => 0, 'closing_deudor' => 0, 'closing_acreedor' => 0];
                foreach ($rows as $r) {
                    $code = isset($r['code']) ? (string)$r['code'] : '';
                    $prefix = strlen($code) > $plen ? substr($code, 0, $plen) : $code;
                    if (!isset($agg[$prefix])) {
                        $agg[$prefix] = [
                            'id' => null,
                            'code' => $prefix,
                            'name' => (isset($codeNameMap[$prefix]) && $codeNameMap[$prefix]) ? $codeNameMap[$prefix] : ('AGRUPADO ' . $prefix),
                            'type' => null,
                            'opening_raw' => 0,
                            'opening_deudor' => 0,
                            'opening_acreedor' => 0,
                            'debits' => 0,
                            'credits' => 0,
                            'closing_raw' => 0,
                            'closing_deudor' => 0,
                            'closing_acreedor' => 0
                        ];
                    }
                    $agg[$prefix]['opening_raw'] += floatval($r['opening_raw'] ?? 0);
                    $agg[$prefix]['opening_deudor'] += floatval($r['opening_deudor'] ?? 0);
                    $agg[$prefix]['opening_acreedor'] += floatval($r['opening_acreedor'] ?? 0);
                    $agg[$prefix]['debits'] += floatval($r['debits'] ?? 0);
                    $agg[$prefix]['credits'] += floatval($r['credits'] ?? 0);
                    $agg[$prefix]['closing_raw'] += floatval($r['closing_raw'] ?? 0);
                    $agg[$prefix]['closing_deudor'] += floatval($r['closing_deudor'] ?? 0);
                    $agg[$prefix]['closing_acreedor'] += floatval($r['closing_acreedor'] ?? 0);

                    $agg_totals['opening_deudor'] += floatval($r['opening_deudor'] ?? 0);
                    $agg_totals['opening_acreedor'] += floatval($r['opening_acreedor'] ?? 0);
                    $agg_totals['debits'] += floatval($r['debits'] ?? 0);
                    $agg_totals['credits'] += floatval($r['credits'] ?? 0);
                    $agg_totals['closing_deudor'] += floatval($r['closing_deudor'] ?? 0);
                    $agg_totals['closing_acreedor'] += floatval($r['closing_acreedor'] ?? 0);
                }
                // sort by code
                ksort($agg);
                $newRows = [];
                foreach ($agg as $p => $entry) { $newRows[] = $entry; }
                return ['rows' => $newRows, 'totals' => $agg_totals];
            }
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Obtener reporte Auxiliares: lista de movimientos por cuenta entre fechas
     * $account_ids: array empty => all accounts
     * returns array of accounts: [ ['id', 'code','name','opening', 'lines' => [ {date,serie,document_no,descripcion,debit,credit,balance} ], 'final_balance'] ]
     */
    public function get_auxiliares($account_ids = [], $start = null, $end = null)
    {
        // fetch accounts to report (filter if provided)
        $acct_fq = $this->account_table_fq();
        $this->db->select($this->account_select_cols_noalias());
        $this->db->from($acct_fq . ' as a');
        if (!empty($account_ids)) {
            $this->db->where_in('a.id', $account_ids);
        }
        $this->db->order_by('a.code', 'asc');
        $accounts = $this->db->get()->result();

        // load centros de costo map
        $centros_map = [];
        $qc = $this->db->select('id,nombre,codigo')->from('tb_centro_costo')->where('activo',1)->get();
        if ($qc) {
            foreach ($qc->result() as $c) { $centros_map[intval($c->id)] = trim(($c->codigo ? $c->codigo . ' - ' : '') . $c->nombre); }
        }

        $result = [];
        foreach ($accounts as $a) {
            $aid = intval($a->id);
            // compute opening balance (sum before start) - only posted
            $opening = 0.0;
            if ($start) {
                $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as opening_raw FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date < ?";
                $q = $this->db->query($sql, array($aid, $start));
                if ($q && $q->row()) $opening = floatval($q->row()->opening_raw);
            }

            // get lines within the period
            $sql2 = "SELECT j.date as date, IFNULL(j.entry_type, '') as serie, j.id as document_no, e.description as descripcion, e.debit as debit, e.credit as credit, e.centro_costo_id as centro_id FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND (j.date IS NOT NULL AND j.date <> '' AND j.date <> '0000-00-00')";
            $params = array($aid);
            if ($start) { $sql2 .= " AND j.date >= ?"; $params[] = $start; }
            if ($end) { $sql2 .= " AND j.date <= ?"; $params[] = $end; }
            $sql2 .= " ORDER BY j.date, j.id";
            $q2 = $this->db->query($sql2, $params);
            $lines = $q2 ? $q2->result() : [];

            $running = $opening;
            $outLines = [];
            foreach ($lines as $ln) {
                $running += floatval($ln->debit) - floatval($ln->credit);
                $centro_name = '';
                if (isset($ln->centro_id) && $ln->centro_id) {
                    $cid = intval($ln->centro_id);
                    $centro_name = isset($centros_map[$cid]) ? $centros_map[$cid] : ('ID ' . $cid);
                }
                $outLines[] = [
                    'date' => $ln->date,
                    'doc_type' => $ln->serie,
                    'document_no' => $ln->document_no,
                    'descripcion' => $ln->descripcion,
                    'centro_costo' => $centro_name,
                    'debit' => floatval($ln->debit),
                    'credit' => floatval($ln->credit),
                    'balance' => $running
                ];
            }

            $result[] = [
                'id' => $aid,
                'code' => $a->code,
                'name' => $a->name,
                'opening' => $opening,
                'lines' => $outLines,
                'final_balance' => $running
            ];
        }

        return $result;
    }

    /**
     * Obtener Balance General a una fecha determinada (as_of_date: YYYY-MM-DD)
     * Agrupa por tipo de cuenta (activo, pasivo, patrimonio) y devuelve totales por cuenta.
     * Se normaliza la presentación para que Activo muestre montos positivos y
     * Pasivo/Patrimonio también como valores positivos en la columna correspondiente.
     */
    public function get_balance_sheet($as_of_date = null)
    {
        // fetch accounts
        $this->db->select($this->account_select_cols_noalias());
        $acct = $this->account_table_name();
        $this->db->from($acct);
        $this->db->order_by('code', 'asc');
        $accounts = $this->db->get()->result();

        $groups = [
            'activo' => [],
            'pasivo' => [],
            'patrimonio' => [],
            'ingreso' => [],
            'gasto' => []
        ];

        $totals = ['activo' => 0.0, 'pasivo' => 0.0, 'patrimonio' => 0.0];

        // map of accounts by id for lookups
        $accounts_map = [];
        foreach ($accounts as $ac) { $accounts_map[intval($ac->id)] = $ac; }

        foreach ($accounts as $a) {
            $aid = intval($a->id);
            // compute raw balance up to as_of_date (inclusive) - ONLY POSTED ENTRIES
            $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as bal FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0)";
            $params = array($aid);
            if ($as_of_date) { $sql .= " AND j.date <= ?"; $params[] = $as_of_date; }
            $q = $this->db->query($sql, $params);
            $raw = floatval($q->row()->bal);

            // normalize display: for activo/gasto -> debit increases (positive raw = asset), for pasivo/patrimonio/ingreso invert sign
            $type = strtolower($a->type);
            $factor = 1;
            if (in_array($type, ['pasivo','patrimonio','ingreso'])) $factor = -1;
            $display = $raw * $factor;

            // for balance sheet presentation show positive amounts only, negative as credit (we'll keep sign)
            // determine whether the account is corriente (current) using heuristics:
            // 1) parent account name contains 'corrient' (case-insensitive)
            // 2) account or parent name contains 'corrient'
            // 3) fallback: for activos, codes starting with '1.' and second segment <= 3 considered corriente
            $is_current = false;
            $p = isset($accounts_map[$a->parent_id]) ? $accounts_map[$a->parent_id] : null;
            $name_check = strtolower($a->name . ' ' . ($p ? $p->name : ''));
            if (strpos($name_check, 'corrient') !== false) {
                $is_current = true;
            } else {
                // fallback by code pattern
                if ($type === 'activo' && preg_match('/^(\d+)(?:\.(\d+))?/', $a->code, $m)) {
                    $first = isset($m[1]) ? intval($m[1]) : 0;
                    $second = isset($m[2]) ? intval($m[2]) : 0;
                    if ($first === 1 && $second > 0 && $second <= 3) $is_current = true;
                }
            }

            $rows = [
                'id' => $aid,
                'code' => $a->code,
                'name' => $a->name,
                'type' => $type,
                'raw' => $raw,
                'display' => $display,
                'current' => $is_current
            ];

            // compute comparative (same date previous year) if as_of_date provided - ONLY POSTED ENTRIES
            if ($as_of_date) {
                $prev = date('Y-m-d', strtotime($as_of_date . ' -1 year'));
                $sqlc = "SELECT IFNULL(SUM(e.debit - e.credit),0) as bal FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.date <= ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0)";
                $qc = $this->db->query($sqlc, array($aid, $prev));
                $raw_prev = floatval($qc->row()->bal);
                $display_prev = $raw_prev * $factor;
                $rows['compare_raw'] = $raw_prev;
                $rows['compare_display'] = $display_prev;
            } else {
                $rows['compare_raw'] = 0.0;
                $rows['compare_display'] = 0.0;
            }

            if (!isset($groups[$type])) $groups[$type] = [];
            $groups[$type][] = $rows;
        }
        // compute totals per group as the sum of absolute displayed amounts (presentation amounts)
        $computed = ['activo' => 0.0, 'pasivo' => 0.0, 'patrimonio' => 0.0];
        foreach (['activo','pasivo','patrimonio'] as $g) {
            if (!empty($groups[$g])) {
                foreach ($groups[$g] as $r) {
                    $computed[$g] += abs(floatval(isset($r['display']) ? $r['display'] : 0));
                }
            }
        }

        return [
            'groups' => $groups,
            'totals' => [
                'activo' => $computed['activo'],
                'pasivo' => $computed['pasivo'],
                'patrimonio' => $computed['patrimonio'],
                'pasivo_patrimonio' => ($computed['pasivo'] + $computed['patrimonio'])
            ]
        ];
    }

    /**
     * Obtener Estado de Situación Financiera Mensual (formato mejorado)
     * Al último día del mes especificado
     */
    public function get_situacion_financiera_mensual($fecha_fin)
    {
        // Use grouping by 'report_bs' (Estado de Situación Financiera) when available
        $activo = $this->_get_cuentas_por_tipo_grouped('activo', $fecha_fin);
        $pasivo = $this->_get_cuentas_por_tipo_grouped('pasivo', $fecha_fin);
        $patrimonio = $this->_get_cuentas_por_tipo_grouped('patrimonio', $fecha_fin);

        $total_activo = 0; $total_pasivo = 0; $total_patrimonio = 0;
        foreach ($activo as $g) { $total_activo += floatval($g['total']); }
        foreach ($pasivo as $g) { $total_pasivo += floatval($g['total']); }
        foreach ($patrimonio as $g) { $total_patrimonio += floatval($g['total']); }

        return [
            'activo' => $activo,
            'pasivo' => $pasivo,
            'patrimonio' => $patrimonio,
            'total_activo' => $total_activo,
            'total_pasivo' => $total_pasivo,
            'total_patrimonio' => $total_patrimonio,
            'fecha' => $fecha_fin
        ];
    }

    /**
     * Obtener Estado de Situación Financiera Anual (consolidado de 12 meses)
     */
    public function get_situacion_financiera_anual($anio)
    {
        $cuentas = [];
        
        // Get all accounts of types activo, pasivo, patrimonio
        $this->db->select($this->account_select_cols_noalias());
        $acct = $this->account_table_name();
        $this->db->from($acct);
        $this->db->where_in('type', ['activo', 'pasivo', 'patrimonio']);
        $this->db->order_by('code', 'asc');
        $accounts = $this->db->get()->result();
        
        foreach ($accounts as $account) {
            $meses = [];
            
            // Calculate balance for each month
            for ($mes = 1; $mes <= 12; $mes++) {
                $fecha_fin = date('Y-m-t', strtotime($anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01'));
                
                $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as bal 
                        FROM tb_journal_entry e 
                        JOIN tb_journal j ON j.id = e.journal_id 
                        WHERE e.account_id = ? 
                        AND j.posted = 1 
                        AND (j.voided IS NULL OR j.voided = 0)
                        AND j.date <= ?";
                
                $q = $this->db->query($sql, array($account->id, $fecha_fin));
                $raw = floatval($q->row()->bal);
                
                // Normalize sign
                $type = strtolower($account->type);
                $factor = in_array($type, ['pasivo', 'patrimonio']) ? -1 : 1;
                $meses[$mes] = abs($raw * $factor);
            }
            
            $cuentas[] = [
                'nombre' => $account->code . ' ' . $account->name,
                'tipo' => $account->type,
                'meses' => $meses
            ];
        }
        
        return [
            'cuentas' => $cuentas,
            'anio' => $anio
        ];
    }

    /**
     * Helper: Obtener cuentas por tipo con sus saldos
     */
    private function _get_cuentas_por_tipo($tipo, $fecha_fin)
    {
        $this->db->select($this->account_select_cols_noalias());
        $acct = $this->account_table_name();
        $this->db->from($acct);
        $this->db->where('type', $tipo);
        $this->db->order_by('code', 'asc');
        $accounts = $this->db->get()->result();
        
        $resultado = [];
        
        foreach ($accounts as $account) {
            $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as bal 
                    FROM tb_journal_entry e 
                    JOIN tb_journal j ON j.id = e.journal_id 
                    WHERE e.account_id = ? 
                    AND j.posted = 1 
                    AND (j.voided IS NULL OR j.voided = 0)
                    AND j.date <= ?";
            
            $q = $this->db->query($sql, array($account->id, $fecha_fin));
            $raw = floatval($q->row()->bal);
            
            // Normalize sign for display
            $factor = in_array(strtolower($tipo), ['pasivo', 'patrimonio']) ? -1 : 1;
            $saldo = abs($raw * $factor);
            
            // Only include accounts with non-zero balance
            if ($saldo > 0.01) {
                $resultado[] = [
                    'nombre' => $account->code . ' - ' . $account->name,
                    'saldo' => $saldo,
                    'codigo' => $account->code,
                    'level' => 0,
                    'bold' => false
                ];
            }
        }
        
        return $resultado;
    }

    /**
     * Obtener cuentas por tipo agrupadas por la clave `report_bs` (Estado de Situación Financiera)
     * Retorna un arreglo de grupos: [ ['label'=>..., 'items'=>[...], 'total'=>...], ... ]
     */
    private function _get_cuentas_por_tipo_grouped($tipo, $fecha_fin)
    {
        $this->db->select($this->account_select_cols_noalias() . ', report_bs');
        $acct = $this->account_table_name();
        $this->db->from($acct);
        $this->db->where('type', $tipo);
        $this->db->order_by('code', 'asc');
        $accounts = $this->db->get()->result();

        // Load configured group order
        $this->load->config('report_lines');
        $orderList = isset($this->config->item('report_lines')['bs']) ? $this->config->item('report_lines')['bs'] : [];

        $groups = [];
        // Initialize groups from config to ensure they always appear (even with zero total)
        foreach ($orderList as $label) {
            $groups[$label] = ['label' => $label, 'items' => [], 'total' => 0.0];
        }
        // Ensure 'Otros' exists for unmapped accounts
        if (!isset($groups['Otros'])) {
            $groups['Otros'] = ['label' => 'Otros', 'items' => [], 'total' => 0.0];
        }

        foreach ($accounts as $account) {
            $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as bal 
                    FROM tb_journal_entry e 
                    JOIN tb_journal j ON j.id = e.journal_id 
                    WHERE e.account_id = ? 
                    AND j.posted = 1 
                    AND (j.voided IS NULL OR j.voided = 0)
                    AND j.date <= ?";

            $q = $this->db->query($sql, array($account->id, $fecha_fin));
            $raw = floatval($q->row()->bal);

            // Normalize sign for display
            $factor = in_array(strtolower($tipo), ['pasivo', 'patrimonio']) ? -1 : 1;
            $saldo = abs($raw * $factor);

            // Skip near-zero balances but still keep group entries (we don't add items with zero)
            if ($saldo <= 0.01) continue;

            $groupKey = trim($account->report_bs);
            if ($groupKey === '' || $groupKey === null) $groupKey = 'Otros';

            if (!isset($groups[$groupKey])) {
                // create dynamic group if not in config
                $groups[$groupKey] = ['label' => $groupKey, 'items' => [], 'total' => 0.0];
            }

            $groups[$groupKey]['items'][] = [
                'nombre' => $account->code . ' - ' . $account->name,
                'saldo' => $saldo,
                'codigo' => $account->code
            ];
            $groups[$groupKey]['total'] += $saldo;
        }

        // Build ordered list: respect config order, then any dynamic groups (including 'Otros' last)
        $ordered = [];
        foreach ($orderList as $label) {
            if (isset($groups[$label])) {
                $ordered[] = $groups[$label];
                unset($groups[$label]);
            }
        }
        // Append any remaining groups (sorted by key except keep 'Otros' last)
        if (!empty($groups)) {
            if (isset($groups['Otros'])) {
                $otros = $groups['Otros'];
                unset($groups['Otros']);
            } else {
                $otros = null;
            }
            ksort($groups);
            foreach ($groups as $g) $ordered[] = $g;
            if ($otros !== null) $ordered[] = $otros;
        }

        return $ordered;
    }

    /**
     * Obtener Estado de Resultados entre dos fechas (YYYY-MM-DD).
     * Para una microfinanciera, intenta agrupar por ingresos financieros (intereses),
     * ingresos operativos (comisiones), provisiones y gastos operativos.
     * Retorna filas por cuenta y totales por sección.
     */
    public function get_income_statement($start = null, $end = null)
    {
        // fetch income and expense accounts and build a map for parent lookups
        $acct = $this->account_table_name();
        $this->db->select($this->account_select_cols_noalias());
        $this->db->from($acct);
        $this->db->where_in('type', array('ingreso','gasto'));
        $this->db->order_by('code','asc');
        $accounts = $this->db->get()->result();
        // load all accounts map (including parents) to allow grouping by parent properties
        $this->db->select($this->account_select_cols_noalias());
        $all_acc = $this->db->get($acct)->result();
        $acc_map = [];
        foreach ($all_acc as $aa) { $acc_map[intval($aa->id)] = $aa; }

        $rows = [];
        $totals = [
            'ingresos_financieros' => 0.0,
            'ingresos_operativos' => 0.0,
            'provisiones' => 0.0,
            'gastos_operativos' => 0.0,
            'total_ingresos' => 0.0,
            'total_gastos' => 0.0,
            'resultado_operativo' => 0.0,
            'resultado_neto' => 0.0
        ];

        foreach ($accounts as $a) {
            $aid = intval($a->id);
            $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as bal_raw FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0)";
            $params = array($aid);
            if ($start) { $sql .= " AND j.date >= ?"; $params[] = $start; }
            if ($end) { $sql .= " AND j.date <= ?"; $params[] = $end; }
            $q = $this->db->query($sql, $params);
            $raw = floatval($q->row()->bal_raw);

            $type = strtolower($a->type);
            $factor = 1;
            if (in_array($type, array('pasivo','patrimonio','ingreso'))) $factor = -1;
            // for ingresos, make positive when credit>debit
            if ($type === 'ingreso') $factor = -1;
            $display = $raw * $factor;

            // heuristics for grouping (microfinance): prefer parent_id or code prefixes, fallback to name matching
            $section = 'otros';
            // parent name/code hints
            $parent = isset($acc_map[intval($a->parent_id)]) ? $acc_map[intval($a->parent_id)] : null;
            $pname = $parent ? strtolower($parent->name) : '';
            $lname = strtolower($a->name);

            // prefer explicit parent mapping: if parent name suggests interest/comision/provision
            if (strpos($pname, 'interes') !== false || strpos($pname, 'interés') !== false) {
                $section = 'ingresos_financieros';
            } elseif (strpos($pname, 'comisi') !== false || strpos($pname, 'cargo') !== false) {
                $section = 'ingresos_operativos';
            } elseif (strpos($pname, 'provision') !== false || strpos($pname, 'provisión') !== false || strpos($pname, 'deterioro') !== false) {
                $section = 'provisiones';
            } else {
                // code-based hints: common chart of accounts: 4xxx = ingresos, 5xxx = gastos
                if (preg_match('/^\s*4(\D|$)/', $a->code)) {
                    // income account; further classify by name
                    if (strpos($lname, 'interes') !== false || strpos($lname, 'interés') !== false) $section = 'ingresos_financieros';
                    elseif (strpos($lname, 'comisi') !== false || strpos($lname, 'cargo') !== false || strpos($lname,'servicio') !== false) $section = 'ingresos_operativos';
                    else $section = 'ingresos_operativos';
                } elseif (preg_match('/^\s*5(\D|$)/', $a->code)) {
                    // expense
                    if (strpos($lname, 'provision') !== false || strpos($lname, 'provisión') !== false || strpos($lname,'deterioro') !== false) $section = 'provisiones';
                    else $section = 'gastos_operativos';
                } else {
                    // fallback to name-based
                    if (strpos($lname, 'interes') !== false || strpos($lname, 'interés') !== false) {
                        $section = 'ingresos_financieros';
                    } elseif (strpos($lname, 'comisi') !== false || strpos($lname, 'cargo') !== false || strpos($lname,'servicio') !== false) {
                        $section = 'ingresos_operativos';
                    } elseif (strpos($lname, 'provision') !== false || strpos($lname, 'provisión') !== false || strpos($lname,'deterioro') !== false) {
                        $section = 'provisiones';
                    } else {
                        if ($type === 'ingreso') $section = 'ingresos_operativos';
                        if ($type === 'gasto') $section = 'gastos_operativos';
                    }
                }
            }

            $rows[] = [
                'id' => $aid,
                'code' => $a->code,
                'name' => $a->name,
                'type' => $type,
                'section' => $section,
                'raw' => $raw,
                'display' => $display
            ];

            // accumulate
            if (isset($totals[$section])) $totals[$section] += $display;
            if ($type === 'ingreso') $totals['total_ingresos'] += $display;
            if ($type === 'gasto') $totals['total_gastos'] += $display;
        }

        // compute results
        $totals['resultado_operativo'] = $totals['total_ingresos'] - $totals['total_gastos'];
        $totals['resultado_neto'] = $totals['resultado_operativo']; // tax not considered here

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Obtener Estado de Resultados estructurado para microfinancieras
     * Con formato: Ingresos/Gastos Financieros, Provisiones, Operativos, etc.
     */
    public function get_estado_resultados_estructurado($start, $end)
    {
        $acct = $this->account_table_name();
        $this->db->select($this->account_select_cols_noalias());
        $this->db->from($acct);
        $this->db->where_in('type', ['ingreso', 'gasto']);
        $this->db->order_by('code', 'asc');
        $accounts = $this->db->get()->result();
        
        $resultado = [
            'ingresos_financieros' => [],
            'gastos_financieros' => [],
            'provisiones' => [],
            'ingresos_operativos' => [],
            'gastos_operativos' => [],
            'gastos_administracion' => [],
            'impuesto_renta' => [],
            'participacion_asociadas' => [],
            'total_ingresos_financieros' => 0,
            'total_gastos_financieros' => 0,
            'margen_financiero_bruto' => 0,
            'total_provisiones' => 0,
            'margen_financiero_neto' => 0,
            'total_ingresos_operativos' => 0,
            'total_gastos_operativos' => 0,
            'resultado_operativo_bruto' => 0,
            'total_gastos_administracion' => 0,
            'resultado_antes_impuesto' => 0,
            'total_impuesto' => 0,
            'resultado_ejercicio' => 0
        ];
        
        foreach ($accounts as $account) {
            $sql = "SELECT IFNULL(SUM(e.debit - e.credit), 0) as bal 
                    FROM tb_journal_entry e 
                    JOIN tb_journal j ON j.id = e.journal_id 
                    WHERE e.account_id = ? 
                    AND j.posted = 1 
                    AND (j.voided IS NULL OR j.voided = 0)
                    AND j.date >= ? 
                    AND j.date <= ?";
            
            $q = $this->db->query($sql, [$account->id, $start, $end]);
            $raw = floatval($q->row()->bal);
            
            // Skip if zero
            if (abs($raw) < 0.01) continue;
            
            $type = strtolower($account->type);
            $code = $account->code;
            $nombre = $account->name;
            $nombre_lower = strtolower($nombre);
            
            // Normalize amount for display
            $monto = ($type === 'ingreso') ? abs($raw) * -1 : abs($raw);
            
            // Clasificar por nombre, código o por la clave asignada en `report_is`
            $clasificado = false;

            // If the account has an explicit `report_is` mapping, respect it (map to our section keys)
            if (!$clasificado && !empty($account->report_is)) {
                $this->config->load('report_lines', FALSE, TRUE);
                $rl = $this->config->item('report_lines');
                $er = isset($rl['er']) ? $rl['er'] : [];

                // Define groups according to the report_lines order used in templates
                $g_ingresos = array_slice($er, 0, 6);
                $g_gastos_fin = array_slice($er, 6, 6);
                $g_provisiones = array_slice($er, 12, 5);
                // next two are operativos
                $g_ing_oper = [ 'Ingresos operativos diversos' ];
                $g_gas_oper = [ 'Gastos operativos diversos' ];
                $g_part = [ 'Participación en resultados de asociadas', 'Utilidades en asociadas', 'Pérdidas en asociadas' ];
                $g_admin = [ 'Gastos de administración y otros', 'Gastos con personas vinculadas' ];
                $g_imp = [ 'Impuesto a la renta' ];

                $ri = trim($account->report_is);
                if (in_array($ri, $g_ingresos, true)) {
                    $resultado['ingresos_financieros'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $resultado['total_ingresos_financieros'] += abs($monto);
                    $clasificado = true;
                } elseif (in_array($ri, $g_gastos_fin, true)) {
                    $resultado['gastos_financieros'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $resultado['total_gastos_financieros'] += abs($monto);
                    $clasificado = true;
                } elseif (in_array($ri, $g_provisiones, true)) {
                    $resultado['provisiones'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $resultado['total_provisiones'] += abs($monto);
                    $clasificado = true;
                } elseif (in_array($ri, $g_ing_oper, true)) {
                    $resultado['ingresos_operativos'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $resultado['total_ingresos_operativos'] += abs($monto);
                    $clasificado = true;
                } elseif (in_array($ri, $g_gas_oper, true)) {
                    $resultado['gastos_operativos'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $resultado['total_gastos_operativos'] += abs($monto);
                    $clasificado = true;
                } elseif (in_array($ri, $g_part, true)) {
                    $resultado['participacion_asociadas'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $clasificado = true;
                } elseif (in_array($ri, $g_admin, true)) {
                    $resultado['gastos_administracion'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $resultado['total_gastos_administracion'] += abs($monto);
                    $clasificado = true;
                } elseif (in_array($ri, $g_imp, true)) {
                    $resultado['impuesto_renta'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                    $resultado['total_impuesto'] += abs($monto);
                    $clasificado = true;
                }
            }
            
            // INGRESOS FINANCIEROS (intereses, rendimientos)
            if ($type === 'ingreso' && (
                strpos($nombre_lower, 'interes') !== false ||
                strpos($nombre_lower, 'interés') !== false ||
                strpos($nombre_lower, 'rendimiento') !== false ||
                strpos($nombre_lower, 'financiero') !== false ||
                strpos($code, '41') === 0 || strpos($code, '4.1') === 0
            )) {
                $resultado['ingresos_financieros'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                $resultado['total_ingresos_financieros'] += abs($monto);
                $clasificado = true;
            }
            
            // GASTOS FINANCIEROS (intereses pagados, obligaciones)
            if (!$clasificado && $type === 'gasto' && (
                strpos($nombre_lower, 'interes') !== false ||
                strpos($nombre_lower, 'interés') !== false ||
                strpos($nombre_lower, 'obligacion') !== false ||
                strpos($nombre_lower, 'financiero') !== false ||
                strpos($code, '51') === 0 || strpos($code, '5.1') === 0
            )) {
                $resultado['gastos_financieros'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                $resultado['total_gastos_financieros'] += abs($monto);
                $clasificado = true;
            }
            
            // PROVISIONES (incobrabilidad, deterioro)
            if (!$clasificado && $type === 'gasto' && (
                strpos($nombre_lower, 'provision') !== false ||
                strpos($nombre_lower, 'provisión') !== false ||
                strpos($nombre_lower, 'incobrabilidad') !== false ||
                strpos($nombre_lower, 'deterioro') !== false ||
                strpos($nombre_lower, 'incobrable') !== false ||
                strpos($code, '52') === 0 || strpos($code, '5.2') === 0
            )) {
                $resultado['provisiones'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                $resultado['total_provisiones'] += abs($monto);
                $clasificado = true;
            }
            
            // GASTOS DE ADMINISTRACIÓN
            if (!$clasificado && $type === 'gasto' && (
                strpos($nombre_lower, 'administra') !== false ||
                strpos($nombre_lower, 'personal') !== false ||
                strpos($nombre_lower, 'salario') !== false ||
                strpos($nombre_lower, 'sueldo') !== false ||
                strpos($nombre_lower, 'planilla') !== false ||
                strpos($nombre_lower, 'alquiler') !== false ||
                strpos($nombre_lower, 'arrendamiento') !== false ||
                strpos($nombre_lower, 'depreciaci') !== false ||
                strpos($nombre_lower, 'amortizaci') !== false ||
                strpos($code, '53') === 0 || strpos($code, '5.3') === 0
            )) {
                $resultado['gastos_administracion'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                $resultado['total_gastos_administracion'] += abs($monto);
                $clasificado = true;
            }
            
            // IMPUESTO A LA RENTA
            if (!$clasificado && $type === 'gasto' && (
                strpos($nombre_lower, 'impuesto') !== false ||
                strpos($nombre_lower, 'renta') !== false ||
                strpos($nombre_lower, 'ir') !== false
            )) {
                $resultado['impuesto_renta'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                $resultado['total_impuesto'] += abs($monto);
                $clasificado = true;
            }
            
            // INGRESOS OPERATIVOS (comisiones, servicios, otros)
            if (!$clasificado && $type === 'ingreso') {
                $resultado['ingresos_operativos'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                $resultado['total_ingresos_operativos'] += abs($monto);
                $clasificado = true;
            }
            
            // GASTOS OPERATIVOS (otros gastos no clasificados)
            if (!$clasificado && $type === 'gasto') {
                $resultado['gastos_operativos'][] = ['nombre' => $nombre, 'monto' => abs($monto)];
                $resultado['total_gastos_operativos'] += abs($monto);
            }
        }
        
        // Calcular márgenes y resultados
        $resultado['margen_financiero_bruto'] = $resultado['total_ingresos_financieros'] - $resultado['total_gastos_financieros'];
        $resultado['margen_financiero_neto'] = $resultado['margen_financiero_bruto'] - $resultado['total_provisiones'];
        $resultado['resultado_operativo_bruto'] = $resultado['margen_financiero_neto'] + $resultado['total_ingresos_operativos'] - $resultado['total_gastos_operativos'];
        $resultado['resultado_antes_impuesto'] = $resultado['resultado_operativo_bruto'] - $resultado['total_gastos_administracion'];
        $resultado['resultado_ejercicio'] = $resultado['resultado_antes_impuesto'] - $resultado['total_impuesto'];
        
        return $resultado;
    }

    /**
     * Obtener Flujo de Efectivo entre dos fechas (YYYY-MM-DD)
     * Heurística para microfinanciera:
     * - Define cuentas de caja/banco (codes que comienzan por '1' o nombres que contienen 'caja'/'banc')
     * - Para cada asiento que toque una cuenta de caja, calcula el efecto neto (debit-credit)
     * - Clasifica la fuente/destino por las cuentas contrapartida del mismo asiento:
     *   * colecciones_creditos: si contrapartida tiene código que comienza con '11' (cartera)
     *   * intereses_y_comisiones: si hay cuentas de ingreso (4xxx) o nombre con 'interes'
     *   * desembolsos_creditos: si el efecto es negativo y contrapartida cartera
     *   * pagos_operativos: si contrapartida es gasto (5xxx) o type='gasto'
     *   * financiacion: si contrapartida es patrimonio/pasivo relevante
     *   * otros: resto
     */
    public function get_cash_flow($start = null, $end = null)
    {
        // find cash accounts: prefer explicit common codes (1000,1100) then code starts with '1' OR name includes 'caja' or 'banc'
        $acct = $this->account_table_name();
        $this->db->select($this->account_select_cols_noalias());
        $this->db->from($acct);
        $this->db->group_start();
        // explicit common cash/bank codes used in seeds
        $this->db->or_where('code', '1000');
        $this->db->or_where('code', '1100');
        $this->db->or_like('code', '1', 'after');
        $nameCol = $this->safe_field_exists('name', $acct) ? 'name' : 'ame';
        $this->db->or_like($nameCol, 'caja');
        $this->db->or_like($nameCol, 'banc');
        $this->db->group_end();
        $cashAccsQ = $this->db->get();
        $cashAccs = $cashAccsQ->result();
        $cashIds = array();
        foreach ($cashAccs as $c) $cashIds[] = intval($c->id);

        if (empty($cashIds)) {
            return ['rows' => [], 'totals' => []];
        }

        // get cash entries in date range
        $sql = "SELECT e.journal_id, j.date as date, j.description as journal_description, e.account_id, e.debit, e.credit
            FROM tb_journal_entry e
            JOIN tb_journal j ON j.id = e.journal_id
            WHERE e.account_id IN (" . implode(',', $cashIds) . ")";
        $params = array();
        if ($start) { $sql .= " AND j.date >= ?"; $params[] = $start; }
        if ($end) { $sql .= " AND j.date <= ?"; $params[] = $end; }
        $sql .= " ORDER BY j.date ASC";
        $q = $this->db->query($sql, $params);
        $entries = $q->result();

        $rows = array();
        $totals = [
            'colecciones_creditos' => 0.0,
            'intereses_comisiones' => 0.0,
            'desembolsos_creditos' => 0.0,
            'pagos_operativos' => 0.0,
            'financiacion' => 0.0,
            'otros' => 0.0,
            'neto' => 0.0
        ];

        foreach ($entries as $e) {
            $cash_effect = floatval($e->debit) - floatval($e->credit); // positive = inflow, negative = outflow
            // fetch contrapartida lines for the same journal (non-cash lines)
                $acct = $this->account_table_name();
                $nameCol = $this->db->field_exists('name', $acct) ? 'name' : 'ame';
                $typeCol = $this->db->field_exists('type', $acct) ? 'type' : 'ype';
                $cq = $this->db->query("SELECT a.id,a.code, a." . $nameCol . " as name, a." . $typeCol . " as type, e.debit,e.credit FROM tb_journal_entry e JOIN " . $acct . " a ON a.id = e.account_id WHERE e.journal_id = ? AND e.account_id NOT IN (" . implode(',', $cashIds) . ")", array($e->journal_id));
            $contrapartes = $cq->result();

            // default category
            $category = 'otros';
            // rules
            $found = false;
            foreach ($contrapartes as $c) {
                $ccode = isset($c->code) ? trim($c->code) : '';
                $cname = strtolower(isset($c->name) ? $c->name : '');
                $ctype = strtolower(isset($c->type) ? $c->type : '');
                // loan portfolio (cartera) detection: prefer name hints (cartera/prestamo) then codes starting with '11'
                if (strpos($cname, 'cartera') !== false || strpos($cname, 'prestamo') !== false || preg_match('/^\s*11/', $ccode)) {
                    if ($cash_effect > 0) {
                        $category = 'colecciones_creditos';
                    } else {
                        $category = 'desembolsos_creditos';
                    }
                    $found = true; break;
                }
                // interest or fees income
                if ($ctype === 'ingreso' || preg_match('/^\s*4/', $ccode) || strpos($cname, 'interes') !== false || strpos($cname, 'comisi') !== false) {
                    $category = 'intereses_comisiones'; $found = true; break;
                }
                // expense payments
                if ($ctype === 'gasto' || preg_match('/^\s*5/', $ccode) || strpos($cname, 'gasto') !== false || strpos($cname, 'sueld') !== false) {
                    $category = 'pagos_operativos'; $found = true; break;
                }
                // financing (capital injections / loans from banks) - patrimonio / pasivo
                if ($ctype === 'patrimonio' || $ctype === 'pasivo' || preg_match('/^\s*2/', $ccode) || preg_match('/^\s*3/', $ccode)) {
                    $category = 'financiacion'; $found = true; break;
                }
            }

            // accumulate totals
            if (!isset($totals[$category])) $totals[$category] = 0.0;
            $totals[$category] += $cash_effect;
            $totals['neto'] += $cash_effect;

            $rows[] = [
                'journal_id' => $e->journal_id,
                'date' => $e->date,
                'description' => $e->journal_description,
                'cash_account_id' => $e->account_id,
                'amount' => $cash_effect,
                'category' => $category,
                'counterparties' => $contrapartes
            ];
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    // Verificar si existe un código (excluir id opcional)
    public function code_exists($code, $exclude_id = null)
    {
        $acct = $this->account_table_name();
        $this->db->from($acct);
        $this->db->where('code', $code);
        if ($exclude_id) $this->db->where('id !=', intval($exclude_id));
        return $this->db->count_all_results() > 0;
    }

    // Get single account by id
    public function get_account($id)
    {
        $acct_fq = $this->account_table_fq();
        $sql = "SELECT " . $this->account_select_cols_noalias() . " FROM " . $acct_fq . " WHERE id = ?";
        $q = $this->db->query($sql, array(intval($id)));
        return $q->row();
    }

    // Get single account by code
    public function get_account_by_code($code)
    {
        $acct_fq = $this->account_table_fq();
        $sql = "SELECT " . $this->account_select_cols_noalias() . " FROM " . $acct_fq . " WHERE code = ?";
        $q = $this->db->query($sql, array(trim($code)));
        return $q->row();
    }

    // Create account
    public function create_account($data)
    {
        $insert = [
            'code' => isset($data['code']) ? $data['code'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'type' => isset($data['type']) ? $data['type'] : null,
            'parent_id' => isset($data['parent_id']) ? $data['parent_id'] : null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $acct = $this->account_table_name();
        // Add optional report_type if column exists and provided
        if ($this->safe_field_exists('report_type', $acct) && isset($data['report_type'])) {
            $insert['report_type'] = $data['report_type'];
        }
        if ($this->safe_field_exists('report_is', $acct) && isset($data['report_is'])) {
            $insert['report_is'] = $data['report_is'];
        }
        if ($this->safe_field_exists('report_bs', $acct) && isset($data['report_bs'])) {
            $insert['report_bs'] = $data['report_bs'];
        }
        // optional level/is_mayor
        if ($this->safe_field_exists('level', $acct) && array_key_exists('level', $data)) {
            $insert['level'] = intval($data['level']);
        }
        if ($this->safe_field_exists('is_mayor', $acct) && array_key_exists('is_mayor', $data)) {
            $insert['is_mayor'] = ($data['is_mayor'] ? 1 : 0);
        }
        // Guardar naturaleza y agrupador_estado si existen
        if ($this->safe_field_exists('naturaleza', $acct) && isset($data['naturaleza'])) {
            $insert['naturaleza'] = $data['naturaleza'];
        }
        if ($this->safe_field_exists('agrupador_estado', $acct) && isset($data['agrupador_estado'])) {
            $insert['agrupador_estado'] = $data['agrupador_estado'];
        }
        $result = $this->db->insert($acct, $insert);
        return $result ? $this->db->insert_id() : false;
    }
    // Update account
    public function update_account($id, $data)
    {
        $update = [
            'code' => isset($data['code']) ? $data['code'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'type' => isset($data['type']) ? $data['type'] : null,
            'parent_id' => isset($data['parent_id']) ? $data['parent_id'] : null,
        ];
        // Include optional report_type/report_is/report_bs if columns exist
        $acct = $this->account_table_name();
        if ($this->safe_field_exists('report_type', $acct) && array_key_exists('report_type', $data)) {
            $update['report_type'] = $data['report_type'];
        }
        if ($this->safe_field_exists('report_is', $acct) && array_key_exists('report_is', $data)) {
            $update['report_is'] = $data['report_is'];
        }
        if ($this->safe_field_exists('report_bs', $acct) && array_key_exists('report_bs', $data)) {
            $update['report_bs'] = $data['report_bs'];
        }
        // optional level/is_mayor
        if ($this->safe_field_exists('level', $acct) && array_key_exists('level', $data)) {
            $update['level'] = intval($data['level']);
        }
        if ($this->safe_field_exists('is_mayor', $acct) && array_key_exists('is_mayor', $data)) {
            $update['is_mayor'] = ($data['is_mayor'] ? 1 : 0);
        }
        // Guardar naturaleza y agrupador_estado si existen
        if ($this->safe_field_exists('naturaleza', $acct) && isset($data['naturaleza'])) {
            $update['naturaleza'] = $data['naturaleza'];
        }
        if ($this->safe_field_exists('agrupador_estado', $acct) && isset($data['agrupador_estado'])) {
            $update['agrupador_estado'] = $data['agrupador_estado'];
        }
        $this->db->where('id', intval($id));
        return $this->db->update($acct, $update);
    }
    // Delete account (soft delete is possible; for now hard delete)
    public function delete_account($id)
    {
        $this->db->where('id', intval($id));
        $acct = $this->account_table_name();
        return $this->db->delete($acct);
    }
    // Verificar si una cuenta tiene asientos asociados
    public function account_has_entries($id)
    {
        $this->db->from('tb_journal_entry');
        $this->db->where('account_id', intval($id));
        return $this->db->count_all_results() > 0;
    }

    // Crear journal y entries (espera array con 'date','description','created_by','source_type','source_id', 'lines' => [[account_id, debit, credit, description], ...])
    public function create_journal($data)
    {
        if (empty($data)) return false;
        $this->db->trans_start();

        $journal = [
            'date' => isset($data['date']) ? $data['date'] : date('Y-m-d'),
            'description' => isset($data['description']) ? $data['description'] : '',
            'total_debit' => 0,
            'total_credit' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => isset($data['created_by']) ? intval($data['created_by']) : null,
            'source_type' => isset($data['source_type']) ? $data['source_type'] : null,
            'source_id' => isset($data['source_id']) ? intval($data['source_id']) : null,
        ];
        if ($this->db->field_exists('document_type', 'tb_journal')) {
            $journal['document_type'] = isset($data['document_type']) ? $data['document_type'] : (isset($data['entry_type']) ? $data['entry_type'] : null);
        }
        if ($this->db->field_exists('entry_type', 'tb_journal')) {
            $journal['entry_type'] = isset($data['entry_type']) ? $data['entry_type'] : (isset($data['document_type']) ? $data['document_type'] : null);
        }

        $this->db->insert('tb_journal', $journal);
        $journal_id = $this->db->insert_id();

        $total_debit = 0; $total_credit = 0;
        if (isset($data['lines']) && is_array($data['lines'])) {
            foreach ($data['lines'] as $line) {
                $account_id = isset($line['account_id']) ? intval($line['account_id']) : 0;
                $debit_raw = isset($line['debit']) ? $line['debit'] : '';
                $credit_raw = isset($line['credit']) ? $line['credit'] : '';
                $debit = ($debit_raw === '' || $debit_raw === null) ? 0.0 : floatval(str_replace(',', '.', $debit_raw));
                $credit = ($credit_raw === '' || $credit_raw === null) ? 0.0 : floatval(str_replace(',', '.', $credit_raw));

                $entry = [
                    'journal_id' => $journal_id,
                    'account_id' => $account_id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => isset($line['description']) ? $line['description'] : '',
                ];

                // Add centro_costo_id if provided and field exists
                if (isset($line['centro_costo_id']) && $this->db->field_exists('centro_costo_id', 'tb_journal_entry')) {
                    $entry['centro_costo_id'] = intval($line['centro_costo_id']);
                }

                $this->db->insert('tb_journal_entry', $entry);
                $total_debit += floatval($entry['debit']);
                $total_credit += floatval($entry['credit']);
            }
        }

        // actualizar totales
        $this->db->where('id', $journal_id)->update('tb_journal', ['total_debit' => $total_debit, 'total_credit' => $total_credit]);

        // NO actualizar tb_ledger aquí - solo se actualiza cuando se mayoriza (posted=1)
        // La mayorización se hace manualmente con post_entry()

        $status = $this->db->trans_status();
        $this->db->trans_complete();
        if ($status) {
            return $journal_id;
        }
        return false;
    }

    // Update or insert a ledger row for a given account and period.
    // period format: YYYY-MM
    public function update_ledger_line($account_id, $period, $debit, $credit)
    {
        if (!$account_id) return false;
        // Use insert on duplicate key (we created unique key account_id+period)
        $sql = "INSERT INTO tb_ledger (account_id, period, debit, credit, balance)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                  debit = debit + VALUES(debit),
                  credit = credit + VALUES(credit),
                  balance = (debit + VALUES(debit)) - (credit + VALUES(credit))";
        $balance = $debit - $credit;
        return $this->db->query($sql, array($account_id, $period, $debit, $credit, $balance));
    }

    /**
     * Apply an accounting mapping rule by key. The mapping must exist
     * in `tb_account_mapping` and provide debit/credit account ids.
     * $params: ['amount' => float, 'date'=> 'YYYY-MM-DD', 'description' => '...']
     */
    public function apply_accounting_rule($mapping_key, $params = [])
    {
        // buscar mapping por clave
        $map = $this->db->where('mapping_key', $mapping_key)->get('tb_account_mapping')->row();

        if (!$map) return false;
        $amount = isset($params['amount']) ? floatval($params['amount']) : 0.0;
        if ($amount <= 0) return false;
        $date = isset($params['date']) ? $params['date'] : date('Y-m-d');
        $description = isset($params['description']) ? $params['description'] : $map->description;

        $lines = [];
        // debit
        $lines[] = ['account_id' => intval($map->debit_account_id), 'debit' => $amount, 'credit' => 0, 'description' => $description];
        // credit
        $lines[] = ['account_id' => intval($map->credit_account_id), 'debit' => 0, 'credit' => $amount, 'description' => $description];

        $payload = ['date' => $date, 'description' => $description, 'lines' => $lines];
        return $this->create_journal($payload);
    }

    // Get mapping row by key
    public function get_mapping_by_key($key)
    {
        $q = $this->db->get_where('tb_account_mapping', ['mapping_key' => $key]);
        return $q->row();
    }

    // Get journal header and lines by journal id
    public function get_journal($journal_id)
    {
        $jid = intval($journal_id);
        if (!$jid) return null;
        
        // Select journal header
        $this->db->select('j.*');
        $this->db->from('tb_journal j');
        $this->db->where('j.id', $jid);
        $qh = $this->db->get();
        $header = $qh->row();
        if (!$header) return null;
        
        // Get lines with account and centro_costo info
        $acct = $this->account_table_name();
        $nameCol = $this->db->field_exists('name', $acct) ? 'name' : 'ame';
        
        $select = 'e.id, e.journal_id, e.account_id, e.debit, e.credit, e.description as line_description, e.description as description, a.code, a.' . $nameCol . ' as name';
        
        // Add centro_costo fields if column exists
        if ($this->db->field_exists('centro_costo_id', 'tb_journal_entry')) {
            $select .= ', e.centro_costo_id, cc.codigo as centro_costo_codigo, cc.nombre as centro_costo_nombre';
        }
        
        $this->db->select($select);
        $this->db->from('tb_journal_entry e');
        $this->db->join($acct . ' a', 'a.id = e.account_id', 'left');
        
        // Join centro_costo if column exists
        if ($this->db->field_exists('centro_costo_id', 'tb_journal_entry')) {
            $this->db->join('tb_centro_costo cc', 'cc.id = e.centro_costo_id', 'left');
        }
        
        $this->db->where('e.journal_id', $jid);
        $this->db->order_by('e.id', 'asc');
        $ql = $this->db->get();
        $lines = $ql->result();
        return ['header' => $header, 'lines' => $lines];
    }

    /**
     * Obtener movimientos del libro mayor para una cuenta y rango de fechas.
     * Retorna un arreglo con entradas y balance acumulado.
     * $start/$end en formato YYYY-MM-DD (opcionales).
     */
    public function get_ledger($account_id, $start = null, $end = null, $page = 1, $per_page = 500)
    {
        $aid = intval($account_id);
        if (!$aid) return [];
        $params = [];
        $acct = $this->account_table_name();
        $nameCol = $this->db->field_exists('name', $acct) ? 'name' : 'ame';
        $sql = "SELECT j.id as journal_id, j.date as date, j.description as journal_description,
               e.id as entry_id, e.debit, e.credit, e.description as line_description,
               a.code as account_code, a." . $nameCol . " as account_name
            FROM tb_journal_entry e
            JOIN tb_journal j ON j.id = e.journal_id
            LEFT JOIN " . $acct . " a ON a.id = e.account_id
            WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0)";
        $params[] = $aid;
        if ($start) {
            $sql .= " AND j.date >= ?";
            $params[] = $start;
        }
        if ($end) {
            $sql .= " AND j.date <= ?";
            $params[] = $end;
        }
        $sql .= " ORDER BY j.date ASC, j.id ASC, e.id ASC";

        // total count for pagination
        $countSql = "SELECT COUNT(*) as cnt FROM (" . $sql . ") t";
        $countQ = $this->db->query($countSql, $params);
        $total = $countQ->row() ? intval($countQ->row()->cnt) : 0;

        // apply limit/offset
        $page = max(1, intval($page));
        $per_page = max(1, intval($per_page));
        $offset = ($page - 1) * $per_page;
        $sql .= " LIMIT " . intval($offset) . "," . intval($per_page);
        $q = $this->db->query($sql, $params);
        $rows = $q->result();

        // get account type to normalize balance sign
        $acct = $this->get_account($aid);
        $acct_type = $acct ? strtolower($acct->type) : '';
        // factor: for activo/gasto -> 1 (debit increases), for pasivo/patrimonio/ingreso -> -1 (credit increases)
        $factor = 1;
        if (in_array($acct_type, ['pasivo','patrimonio','ingreso'])) $factor = -1;

        // compute running balance (raw = debit - credit), but provide display balance normalized by factor
        $running_raw = 0.0;
        $out = [];
        foreach ($rows as $r) {
            $debit = floatval($r->debit);
            $credit = floatval($r->credit);
            $amt = $debit - $credit; // raw effect
            $running_raw += $amt;
            $display_running = $running_raw * $factor;
            $side = $display_running >= 0 ? 'Deudor' : 'Acreedor';
            $running_abs = abs($display_running);
            $out[] = [
                'date' => $r->date,
                'journal_id' => $r->journal_id,
                'entry_id' => $r->entry_id,
                'description' => $r->line_description ? $r->line_description : $r->journal_description,
                'debit' => $debit,
                'credit' => $credit,
                'amount' => $amt,
                'running_balance_raw' => $running_raw,
                'running_balance' => $display_running,
                'running_abs' => $running_abs,
                'side' => $side,
                'account_code' => $r->account_code,
                'account_name' => $r->account_name,
            ];
        }

        // include opening balance (before start) for context
        if ($start) {
            $sql2 = "SELECT IFNULL(SUM(e.debit - e.credit),0) as opening FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.date < ?";
            $q2 = $this->db->query($sql2, array($aid, $start));
            $opening = $q2->row();
            $opening_balance_raw = $opening ? floatval($opening->opening) : 0.0;
            $opening_balance = $opening_balance_raw * $factor;
        } else {
            // when no start date, but offset > 0 (pagination), compute opening as sum of previous rows up to offset
            if (isset($offset) && $offset > 0) {
                // sum of raw amounts for rows before offset
                $sumSql = "SELECT IFNULL(SUM(t.amt),0) as opening FROM (SELECT (e.debit - e.credit) as amt FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ?";
                $sumParams = array($aid);
                if ($end) { $sumSql .= " AND j.date <= ?"; $sumParams[] = $end; }
                $sumSql .= " ORDER BY j.date ASC, j.id ASC, e.id ASC LIMIT " . intval($offset) . ") t";
                $q2 = $this->db->query($sumSql, $sumParams);
                $opening_row = $q2->row();
                $opening_balance_raw = $opening_row ? floatval($opening_row->opening) : 0.0;
                $opening_balance = $opening_balance_raw * $factor;
            } else {
                $opening_balance_raw = 0.0;
                $opening_balance = 0.0;
            }
        }

        return ['account_id' => $aid, 'account_type' => $acct_type, 'opening_balance' => $opening_balance, 'opening_balance_raw' => $opening_balance_raw, 'entries' => $out, 'total' => $total, 'page' => $page, 'per_page' => $per_page];
    }

    // Update an existing journal: reverse its ledger impact and apply new lines
    public function update_journal($journal_id, $data)
    {
        $jid = intval($journal_id);
        if (!$jid || empty($data)) return false;
        $this->db->trans_start();

        // fetch existing header and lines
        $old = $this->get_journal($jid);
        if ($old && isset($old['lines']) && is_array($old['lines'])) {
            // Solo remover entradas anteriores, NO tocar tb_ledger
            // El ledger solo se actualiza cuando está mayorizado
            $this->db->where('journal_id', $jid)->delete('tb_journal_entry');
        }

        // update header
        $headerUpdate = [
            'date' => isset($data['date']) ? $data['date'] : date('Y-m-d'),
            'description' => isset($data['description']) ? $data['description'] : '',
        ];
        
        // Add document_type if provided and keep entry_type compatibility
        if (isset($data['document_type']) && $this->db->field_exists('document_type', 'tb_journal')) {
            $headerUpdate['document_type'] = $data['document_type'];
        }
        if (isset($data['entry_type']) && $this->db->field_exists('entry_type', 'tb_journal')) {
            $headerUpdate['entry_type'] = $data['entry_type'];
        }
        
        $this->db->where('id', $jid)->update('tb_journal', $headerUpdate);

        // insert new entries and apply ledger
        $total_debit = 0; $total_credit = 0;
        if (isset($data['lines']) && is_array($data['lines'])) {
            foreach ($data['lines'] as $line) {
                $entry = [
                    'journal_id' => $jid,
                    'account_id' => $line['account_id'],
                    'debit' => floatval($line['debit']),
                    'credit' => floatval($line['credit']),
                    'description' => isset($line['description']) ? $line['description'] : '',
                ];
                
                // Add centro_costo_id if provided and field exists
                if (isset($line['centro_costo_id']) && $this->db->field_exists('centro_costo_id', 'tb_journal_entry')) {
                    $entry['centro_costo_id'] = intval($line['centro_costo_id']);
                }
                
                $this->db->insert('tb_journal_entry', $entry);
                $total_debit += $entry['debit'];
                $total_credit += $entry['credit'];
                // NO actualizar tb_ledger aquí - solo cuando se mayoriza
            }
        }

        // actualizar totales
        $this->db->where('id', $jid)->update('tb_journal', ['total_debit' => $total_debit, 'total_credit' => $total_credit]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
