<?php
defined('BASEPATH') or exit('Acción no permitida');

class Tools extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    protected function local_only_or_cli()
    {
        if (php_sapi_name() === 'cli') return TRUE;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return in_array($ip, array('127.0.0.1','::1'));
    }

    public function clear_contabilidad()
    {
        if (!$this->local_only_or_cli()) {
            header('HTTP/1.1 403 Forbidden');
            echo "Access denied. This tool is restricted to localhost or CLI.";
            return;
        }

        $tables = array('tb_journal_entry','tb_journal','tb_account','b_account','teso_accounts','tb_centro_costo');
        $exists = array();
        foreach ($tables as $t) {
            if ($this->db->table_exists($t)) {
                $exists[] = $t;
            }
        }

        // If no matching tables
        if (empty($exists)) {
            echo "No contabilidad tables found to process.\n";
            return;
        }

        $confirm = $this->input->get('confirm');
        $action = $this->input->get('action'); // if 'truncate' proceed

        if (!$confirm || $confirm !== '1' || $action !== 'truncate') {
            // Show report and a safe link to perform the truncate
            echo "<h2>Contabilidad - Reporte</h2>";
            echo "<p>Estas tablas serán procesadas en la base de datos actual: <strong>" . htmlspecialchars($this->db->database) . "</strong></p>";
            echo "<ul>";
            foreach ($exists as $t) {
                $count = $this->db->count_all($t);
                echo "<li>" . htmlspecialchars($t) . " - filas: " . intval($count) . "</li>";
            }
            echo "</ul>";
            $url = base_url('tools/clear_contabilidad') . '?confirm=1&action=truncate';
            echo "<p>Si estás seguro, abre este enlace para crear respaldos y truncar las tablas: <a href=\"" . $url . "\">Ejecutar borrado seguro</a></p>";
            echo "<p>El proceso creará tablas de respaldo con sufijo <code>_backup_YYYYmmdd_HHis</code> antes de truncar.</p>";
            return;
        }

        // Proceed: create backups then truncate
        $timestamp = date('Ymd_His');
        $report = array();

        // disable FK checks for truncation
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($exists as $t) {
            try {
                $cnt = $this->db->count_all($t);
                $bk = $t . '_backup_' . $timestamp;
                // CREATE backup LIKE original
                $this->db->query("CREATE TABLE `" . $this->db->escape_str($bk) . "` LIKE `" . $this->db->escape_str($t) . "`");
                // INSERT data
                $this->db->query("INSERT INTO `" . $this->db->escape_str($bk) . "` SELECT * FROM `" . $this->db->escape_str($t) . "`");
                // TRUNCATE original
                $this->db->query("TRUNCATE TABLE `" . $this->db->escape_str($t) . "`");
                $report[] = array('table' => $t, 'rows_before' => $cnt, 'backup' => $bk, 'status' => 'ok');
            } catch (Exception $e) {
                $report[] = array('table' => $t, 'rows_before' => isset($cnt) ? $cnt : 'n/a', 'backup' => isset($bk) ? $bk : 'n/a', 'status' => 'error', 'error' => $e->getMessage());
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        // Output report
        echo "<h2>Contabilidad - Resultado</h2>";
        echo "<p>Base de datos: <strong>" . htmlspecialchars($this->db->database) . "</strong></p>";
        echo "<table border=1 cellpadding=6 cellspacing=0>
                <tr><th>Tabla</th><th>Filas antes</th><th>Backup creado</th><th>Estado</th><th>Error (si aplica)</th></tr>";
        foreach ($report as $r) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($r['table']) . "</td>";
            echo "<td>" . htmlspecialchars($r['rows_before']) . "</td>";
            echo "<td>" . htmlspecialchars($r['backup']) . "</td>";
            echo "<td>" . htmlspecialchars($r['status']) . "</td>";
            echo "<td>" . (isset($r['error']) ? htmlspecialchars($r['error']) : '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Operación completada. Revise las tablas de respaldo y confirme que todo está bien.</p>";
    }
}

