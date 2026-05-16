<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contabilidad extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // cargar modelo y helpers basicos
        $this->load->model('Contabilidad_model');
        $this->load->helper(array('url','form'));
        $this->load->library('session');
    }

    public function index()
    {
        $data = array(
            'titulo' => 'Contabilidad',
            'subtitulo' => 'Módulo de asientos y reportes',
            'icono' => 'fas fa-calculator',
            'scripts' => array('js/contabilidad.js')
        );

        // Render within the global layout so styles/scripts match Home
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/home', $data);
        $this->load->view('layout/footer');
    }

    // Modal to view a journal (header + lines)
    public function modal_view()
    {
        $id = $this->input->get('id');
        if (!$id) { echo 'ID inválido'; return; }
        $data = $this->Contabilidad_model->get_journal($id);
        if (!$data) { echo 'Asiento no encontrado'; return; }
        $this->load->view('contabilidad/modal_view', $data);
    }

    // Printable single-asiento view (full page) used for printing the journal
    public function diario_print()
    {
        $id = $this->input->get('id');
        if (!$id) { show_error('ID inválido', 400); return; }
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $data = $this->Contabilidad_model->get_journal($id);
        if (!$data) { show_error('Asiento no encontrado', 404); return; }
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $viewData = array_merge($data, ['empresa' => $empresa]);
        $this->load->view('contabilidad/diario_print', $viewData);
    }

    // View for Auxiliares report
    public function auxiliares()
    {
        $data = ['titulo' => 'Auxiliares', 'scripts' => ['js/contabilidad_auxiliares.js']];
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/auxiliares', $data);
        $this->load->view('layout/footer');
    }

    // UI for FX revaluation
    public function revaluacion()
    {
        $data = ['titulo' => 'Revaluación por Tipo de Cambio', 'scripts' => ['js/contabilidad_revaluacion.js']];
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/revaluacion', $data);
        $this->load->view('layout/footer');
    }

    // UI for monthly period close management
    public function cierre_mensual()
    {
        $data = ['titulo' => 'Cierre Mensual', 'scripts' => ['js/contabilidad_cierre.js']];
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/cierre_mensual', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: list closed periods
    public function cierre_mensual_list()
    {
        header('Content-Type: application/json');
        $list = $this->Contabilidad_model->get_closed_periods();
        echo json_encode(['status'=>'success','data'=>$list]);
    }

    // AJAX: close a period
    public function cierre_mensual_close()
    {
        header('Content-Type: application/json');
        $year = intval($this->input->post('year'));
        $month = intval($this->input->post('month'));
        $notes = $this->input->post('notes');
        $user_id = $this->ion_auth->logged_in() ? $this->ion_auth->get_user_id() : null;
        if (!$year || !$month) { echo json_encode(['status'=>'error','message'=>'Año o mes inválido']); return; }
        $ok = $this->Contabilidad_model->close_period($year,$month,$user_id,$notes);
        if ($ok) echo json_encode(['status'=>'success','message'=>'Periodo cerrado']); else echo json_encode(['status'=>'error','message'=>'No fue posible cerrar el periodo']);
    }

    // AJAX: open (unlock) a period
    public function cierre_mensual_open()
    {
        header('Content-Type: application/json');
        $year = intval($this->input->post('year'));
        $month = intval($this->input->post('month'));
        if (!$year || !$month) { echo json_encode(['status'=>'error','message'=>'Año o mes inválido']); return; }
        $ok = $this->Contabilidad_model->open_period($year,$month);
        if ($ok) echo json_encode(['status'=>'success','message'=>'Periodo desbloqueado']); else echo json_encode(['status'=>'error','message'=>'No fue posible desbloquear el periodo']);
    }

    // AJAX: check whether a given date falls in a closed period
    public function is_period_closed()
    {
        header('Content-Type: application/json');
        $date = $this->input->get('date') ? $this->input->get('date') : null;
        if (!$date) { echo json_encode(['status'=>'error','message'=>'date requerido']); return; }
        $closed = $this->Contabilidad_model->is_period_closed($date);
        $isAdmin = (method_exists($this->ion_auth, 'is_admin') && $this->ion_auth->logged_in() && $this->ion_auth->is_admin());
        echo json_encode(['status'=>'success','closed' => $closed ? true : false, 'is_admin' => $isAdmin]);
    }

    // AJAX: search accounts for Select2 (code or name)
    public function search_accounts()
    {
        header('Content-Type: application/json');
        $q = $this->input->get('q');
        if (!$q) $q = $this->input->get('term');
        $limit = intval($this->input->get('limit') ?: 200);
        $q = trim((string)$q);
        if ($q === '') { echo json_encode(['status'=>'success','data'=>[]]); return; }
        $rows = $this->Contabilidad_model->search_accounts($q, $limit);
        $out = [];
        if ($rows) {
            foreach ($rows as $r) {
                $out[] = [
                    'id' => isset($r->id) ? $r->id : null,
                    'code' => isset($r->code) ? $r->code : '',
                    'name' => isset($r->name) ? $r->name : ''
                ];
            }
        }
        echo json_encode(['status'=>'success','data'=>$out]);
    }

    // Execute revaluation (AJAX POST)
    public function revaluacion_execute()
    {
        header('Content-Type: application/json');
        $post = $this->input->post();
        $new_rate = isset($post['new_rate']) ? floatval($post['new_rate']) : 0;
        $fecha = isset($post['fecha']) ? $post['fecha'] : date('Y-m-d');
        $execute = isset($post['execute']) && ($post['execute'] == '1' || $post['execute'] == 1);
        $notes = isset($post['notes']) ? trim($post['notes']) : null;

        if ($new_rate <= 0) {
            echo json_encode(['status'=>'error','message'=>'Tipo de cambio nuevo inválido']); return;
        }

        // optional P&L account ids
        $fx_gain_account = isset($post['fx_gain_account']) ? intval($post['fx_gain_account']) : null;
        $fx_loss_account = isset($post['fx_loss_account']) ? intval($post['fx_loss_account']) : null;
        // optional contra-gastos account to be used for pasivos
        $fx_contra_gastos_account = isset($post['fx_contra_gastos_account']) ? intval($post['fx_contra_gastos_account']) : null;

        // create run record (ensure revaluation tables exist)
        if (! $this->db->table_exists('tb_revaluation_run')) {
            $sql_run = "CREATE TABLE IF NOT EXISTS `tb_revaluation_run` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `fecha` date NOT NULL,
                `currency` varchar(10) DEFAULT 'USD',
                `tasa_nueva` decimal(18,6) NOT NULL,
                `created_by` int(11) DEFAULT NULL,
                `notes` text DEFAULT NULL,
                `executed` tinyint(1) DEFAULT 0,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $this->db->query($sql_run);
        }
        if (! $this->db->table_exists('tb_revaluation_entry')) {
            $sql_entry = "CREATE TABLE IF NOT EXISTS `tb_revaluation_entry` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `run_id` int(11) NOT NULL,
                `account_id` int(11) NOT NULL,
                `opening_local` decimal(20,4) DEFAULT 0,
                `revalued_local` decimal(20,4) DEFAULT 0,
                `difference` decimal(20,4) DEFAULT 0,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `run_id` (`run_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $this->db->query($sql_entry);
        }
        $run = [
            'fecha' => $fecha,
            'currency' => 'USD',
            'tasa_nueva' => $new_rate,
            'created_by' => $this->ion_auth->logged_in() ? $this->ion_auth->get_user_id() : null,
            'notes' => $notes,
            'executed' => $execute ? 1 : 0
        ];
        $this->db->insert('tb_revaluation_run', $run);
        $run_id = $this->db->insert_id();

        // fetch monetary USD accounts
        $monetary = $this->Contabilidad_model->get_monetary_accounts_for_revaluation();
        $entries = [];
        $total_diff = 0.0;
        foreach ($monetary as $m) {
            $calc = $this->Contabilidad_model->calculate_revaluation_for_account($m['id'], $new_rate, $fecha);
            if (abs($calc['difference']) < 0.005) continue; // skip tiny differences

            // build a normalized entry record for response and optional DB insert
            $entry_row = [
                'account_id' => $m['id'],
                'account_code' => isset($m['code']) ? $m['code'] : null,
                'account_name' => isset($m['name']) ? $m['name'] : null,
                'opening_local' => $calc['opening_local'],
                'balance_foreign' => $calc['balance_foreign'],
                'revalued_local' => $calc['revalued_local'],
                'difference' => $calc['difference']
            ];

            // only persist per-account entries when executing (preview must NOT write)
            if ($execute) {
                $this->db->insert('tb_revaluation_entry', [
                    'run_id' => $run_id,
                    'account_id' => $entry_row['account_id'],
                    'opening_local' => $entry_row['opening_local'],
                    'revalued_local' => $entry_row['revalued_local'],
                    'difference' => $entry_row['difference']
                ]);
            }

            $entries[] = $entry_row;
            $total_diff += $entry_row['difference'];
        }

        // If execute requested, create automatic journal entries
        if ($execute && !empty($entries)) {
            // require fx accounts configured: at minimum a gain account and one of loss/contra-gastos
            if (!$fx_gain_account || (!$fx_loss_account && !$fx_contra_gastos_account)) {
                echo json_encode(['status'=>'error','message'=>'Cuentas de ganancia/perdida cambiaria no configuradas. Enviar fx_gain_account y fx_loss_account o fx_contra_gastos_account.']); return;
            }

            // build journal
            $journal = [
                'date' => $fecha,
                'description' => 'Revaluación tipo de cambio USD - run #' . $run_id,
                'entry_type' => 'REVAL',
                'created_by' => $this->ion_auth->logged_in() ? $this->ion_auth->get_user_id() : null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tb_journal', $journal);
            $journal_id = $this->db->insert_id();

            $total_debit = 0.0; $total_credit = 0.0;
            foreach ($entries as $e) {
                $acc = $this->Contabilidad_model->get_account($e['account_id']);
                $diff = round($e['difference'],2);
                if (abs($diff) < 0.005) continue;
                // Determine sign and account side depending on account type
                $atype = isset($acc->type) ? strtolower($acc->type) : '';
                if ($atype === 'activo') {
                    if ($diff > 0) {
                        // debit asset, credit fx_gain
                        $debit = $diff; $credit = 0;
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$e['account_id'],'debit'=>$debit,'credit'=>0,'description'=>'Ajuste revaluacion']);
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$fx_gain_account,'debit'=>0,'credit'=>$debit,'description'=>'Contrapartida revaluacion']);
                        $total_debit += $debit; $total_credit += $debit;
                    } else {
                        $amt = abs($diff);
                        // credit asset, debit fx_loss
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$e['account_id'],'debit'=>0,'credit'=>$amt,'description'=>'Ajuste revaluacion']);
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$fx_loss_account,'debit'=>$amt,'credit'=>0,'description'=>'Contrapartida revaluacion']);
                        $total_debit += $amt; $total_credit += $amt;
                    }
                } else {
                    // pasivo/patrimonio: reverse signs
                    if ($diff > 0) {
                        // credit liability, debit loss/contra-gastos
                        $contra_account = $fx_contra_gastos_account ? $fx_contra_gastos_account : $fx_loss_account;
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$e['account_id'],'debit'=>0,'credit'=>$diff,'description'=>'Ajuste revaluacion']);
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$contra_account,'debit'=>$diff,'credit'=>0,'description'=>'Contrapartida revaluacion']);
                        $total_debit += $diff; $total_credit += $diff;
                    } else {
                        $amt = abs($diff);
                        // debit liability (decrease), credit gain
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$e['account_id'],'debit'=>$amt,'credit'=>0,'description'=>'Ajuste revaluacion']);
                        $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$fx_gain_account,'debit'=>0,'credit'=>$amt,'description'=>'Contrapartida revaluacion']);
                        $total_debit += $amt; $total_credit += $amt;
                    }
                }
            }

            // update journal totals if columns exist
            if ($this->db->field_exists('total_debit','tb_journal') || $this->db->field_exists('total_credit','tb_journal')) {
                $upd = [];
                if ($this->db->field_exists('total_debit','tb_journal')) $upd['total_debit'] = $total_debit;
                if ($this->db->field_exists('total_credit','tb_journal')) $upd['total_credit'] = $total_credit;
                $this->db->where('id',$journal_id)->update('tb_journal',$upd);
            }

            // mark run executed
            $this->db->where('id',$run_id)->update('tb_revaluation_run',['executed'=>1]);
            echo json_encode(['status'=>'success','message'=>'Revaluación ejecutada','run_id'=>$run_id,'journal_id'=>$journal_id]);
            return;
        }

        $resp = ['status'=>'success','message'=>'Revaluación calculada','run_id'=>$run_id,'entries_count'=>count($entries)];
        // include detail list when not executed (preview)
        if (!$execute) $resp['entries'] = $entries;
        echo json_encode($resp);
    }

    // AJAX: return auxiliares data for selected accounts and date range
    public function auxiliares_data()
    {
        $post = $this->input->post();
        $start = isset($post['start']) && $post['start'] ? $post['start'] : null;
        $end = isset($post['end']) && $post['end'] ? $post['end'] : null;
        $accounts = [];
        if (isset($post['all']) && $post['all']) {
            $accounts = [];
        } else if (isset($post['accounts']) && is_array($post['accounts'])) {
            $accounts = array_map('intval', $post['accounts']);
        }

        $data = $this->Contabilidad_model->get_auxiliares($accounts, $start, $end);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    // Export auxiliares to CSV (simple)
    public function auxiliares_export()
    {
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $format = $this->input->get('format');
        $accounts = $this->input->get('accounts[]') ?: $this->input->get('accounts');
        $all = $this->input->get('all');
        if ($all) $accounts = [];
        if (!is_array($accounts) && $accounts) $accounts = is_string($accounts) ? [$accounts] : (array)$accounts;
        $accounts = array_map('intval', $accounts ?: []);
        $rows = $this->Contabilidad_model->get_auxiliares($accounts, $start, $end);
        // currency selection: 'local' (default) or 'usd'
        $currency = strtolower(trim($this->input->get('currency') ?: 'local'));
        // obtain latest exchange rate (tasa_cambio) for USD conversion
        $this->db->select('tasa_cambio');
        $this->db->order_by('fecha', 'DESC');
        $this->db->limit(1);
        $tasa = $this->db->get('tb_tasa_cambio')->row();
        $exchange_rate = ($tasa && isset($tasa->tasa_cambio)) ? floatval($tasa->tasa_cambio) : 36.50;
        // If PDF requested, generate PDF using Dompdf
        if ($format && strtolower($format) === 'pdf') {
            // build simple HTML
            $this->load->model('Core_model');
            $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
            $title = 'REPORTE AUXILIARES';
            $range = '';
            if ($start || $end) {
                $range = 'Desde: ' . ($start ?: '') . ' &nbsp; Hasta: ' . ($end ?: '');
            }
            // indicate currency in title
            $curr_label = ($currency === 'usd') ? ' (USD)' : ' (Moneda local)';
            $html = '<html><head><meta charset="utf-8"><style>@page{margin:12mm 8mm;}body{font-family: DejaVu Sans, Arial, sans-serif; font-size:10px;color:#222;} .h1{font-size:14px;font-weight:700;margin-bottom:6px;} .meta{font-size:10px;margin-bottom:8px;} table{width:100%;border-collapse:collapse;margin-bottom:8px;table-layout:fixed;border:1px solid #bbb;} th,td{border:none;padding:4px;font-size:9px;vertical-align:top;word-wrap:break-word;overflow:hidden;} th{background:#f0f0f0;font-weight:700;} .acc-title{font-weight:700;margin:8px 0;font-size:11px;} .right{text-align:right;} .small{font-size:9px;color:#444;}</style></head><body>';
            $html .= '<div style="text-align:center;"><div class="h1">' . htmlspecialchars($title) . $curr_label . '</div><div class="meta">' . $range . '</div></div>';
            foreach ($rows as $acc) {
                $html .= '<div class="acc-title">' . htmlspecialchars(($acc['code'] ?: '') . ' - ' . ($acc['name'] ?: '')) . '</div>';
                $html .= '<table><thead><tr><th style="width:10%">fecha</th><th style="width:10%">Tipo Documento</th><th style="width:8%">No Documento</th><th style="width:10%">Centro costo</th><th style="width:32%">Descripcion</th><th style="width:10%" class="right">Debito' . $curr_label . '</th><th style="width:10%" class="right">Credito' . $curr_label . '</th><th style="width:10%" class="right">Balance Final' . $curr_label . '</th></tr></thead><tbody>';
                    // opening balance (convert if needed)
                    $opening_val = ($currency === 'usd') ? ($acc['opening'] / $exchange_rate) : $acc['opening'];
                    $html .= '<tr><td colspan="7" style="font-style:italic;">Saldo anterior</td><td style="text-align:right;">' . number_format($opening_val,2,'.',',') . '</td></tr>';
                foreach ($acc['lines'] as $l) {
                    $html .= '<tr>';
                        $html .= '<td>' . htmlspecialchars($l['date'] ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($l['doc_type'] ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($l['document_no'] ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($l['centro_costo'] ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($l['descripcion'] ?? '') . '</td>';
                        $debit_val = ($currency === 'usd') ? (($l['debit'] ?? 0) / $exchange_rate) : ($l['debit'] ?? 0);
                        $credit_val = ($currency === 'usd') ? (($l['credit'] ?? 0) / $exchange_rate) : ($l['credit'] ?? 0);
                        $bal_val = ($currency === 'usd') ? (($l['balance'] ?? 0) / $exchange_rate) : ($l['balance'] ?? 0);
                        $html .= '<td style="text-align:right;">' . number_format($debit_val,2,'.',',') . '</td>';
                        $html .= '<td style="text-align:right;">' . number_format($credit_val,2,'.',',') . '</td>';
                        $html .= '<td style="text-align:right;">' . number_format($bal_val,2,'.',',') . '</td>';
                    $html .= '</tr>';
                }
                    $final_val = ($currency === 'usd') ? ($acc['final_balance'] / $exchange_rate) : $acc['final_balance'];
                    $html .= '<tr style="font-weight:700;"><td colspan="7" style="text-align:right;">Balance Final</td><td style="text-align:right;">' . number_format($final_val,2,'.',',') . '</td></tr>';
                $html .= '</tbody></table>';
            }
            $html .= '</body></html>';

            // load Dompdf
            if (!class_exists('\Dompdf\Dompdf')) {
                if (file_exists(FCPATH . 'dompdf/autoload.inc.php')) {
                    require_once FCPATH . 'dompdf/autoload.inc.php';
                } elseif (file_exists(FCPATH . 'vendor/autoload.php')) {
                    require_once FCPATH . 'vendor/autoload.php';
                }
            }
            if (!class_exists('\Dompdf\Dompdf')) {
                show_error('Dompdf no disponible en el servidor', 500);
                return;
            }
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->loadHtml($html);
            $dompdf->render();
            $filename = 'auxiliares_' . ($start ?: 'start') . '_' . ($end ?: 'end') . '.pdf';
            $dompdf->stream($filename, ['Attachment' => 0]);
            return;
        }

        // If XLSX requested, generate formatted Excel using PhpSpreadsheet
        // ensure company data available for headers
        $this->load->model('Core_model');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        if ($format && in_array(strtolower($format), ['xlsx','excel'])) {
            // ensure autoload
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                if (file_exists(FCPATH . 'vendor/autoload.php')) {
                    require_once FCPATH . 'vendor/autoload.php';
                }
            }
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                show_error('PhpSpreadsheet no disponible en el servidor', 500);
                return;
            }
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Auxiliares');

            $row = 1;
            $company = isset($empresa->razon_social) ? $empresa->razon_social : 'Empresa';
            $title = 'Reporte: Auxiliares - Lista de movimientos por cuenta';
            $curr_label = ($currency === 'usd') ? ' (USD)' : ' (Moneda local)';
            $rangeText = '';
            if ($start || $end) $rangeText = 'Desde: ' . ($start ?: '') . '   Hasta: ' . ($end ?: '');

            // Header block
            $sheet->mergeCells('A'.$row.':H'.$row);
            $sheet->setCellValue('A'.$row, $company);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row++;
            $sheet->mergeCells('A'.$row.':H'.$row);
            $sheet->setCellValue('A'.$row, $title);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row++;
            $sheet->mergeCells('A'.$row.':H'.$row);
            $sheet->setCellValue('A'.$row, $rangeText);
            $sheet->getStyle('A'.$row)->getFont()->setSize(9);
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row += 2;

            // Column headers for each account table
            foreach ($rows as $acc) {
                // Account title
                $sheet->setCellValue('A'.$row, ($acc['code'] . ' - ' . $acc['name']));
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                $row++;

                $headers = ['fecha','Tipo Documento','No Documento','Centro costo','Descripcion','Debito' . $curr_label,'Credito' . $curr_label,'Balance Final' . $curr_label];
                $col = 'A';
                foreach ($headers as $h) {
                    $sheet->setCellValue($col.$row, $h);
                    $sheet->getStyle($col.$row)->getFont()->setBold(true);
                    $sheet->getStyle($col.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $col++;
                }
                $headerRow = $row;
                $row++;

                // Opening balance row (convert if needed)
                $opening_val = ($currency === 'usd') ? ($acc['opening'] / $exchange_rate) : $acc['opening'];
                $sheet->setCellValue('A'.$row, 'Saldo anterior');
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, $opening_val);
                $row++;

                // Lines
                foreach ($acc['lines'] as $l) {
                    $col = 'A';
                    $sheet->setCellValue($col.$row, $l['date'] ?? ''); $col++;
                    $sheet->setCellValue($col.$row, $l['doc_type'] ?? ''); $col++;
                    $sheet->setCellValue($col.$row, $l['document_no'] ?? ''); $col++;
                    $sheet->setCellValue($col.$row, $l['centro_costo'] ?? ''); $col++;
                    $sheet->setCellValue($col.$row, $l['descripcion'] ?? ''); $col++;
                    $debit_val = ($currency === 'usd') ? (($l['debit'] ?? 0) / $exchange_rate) : ($l['debit'] ?? 0);
                    $credit_val = ($currency === 'usd') ? (($l['credit'] ?? 0) / $exchange_rate) : ($l['credit'] ?? 0);
                    $bal_val = ($currency === 'usd') ? (($l['balance'] ?? 0) / $exchange_rate) : ($l['balance'] ?? 0);
                    $sheet->setCellValue($col.$row, $debit_val); $col++;
                    $sheet->setCellValue($col.$row, $credit_val); $col++;
                    $sheet->setCellValue($col.$row, $bal_val);
                    $row++;
                }

                // Balance final row
                $final_val = ($currency === 'usd') ? ($acc['final_balance'] / $exchange_rate) : $acc['final_balance'];
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('A'.$row, 'Balance Final');
                $sheet->setCellValue('H'.$row, $final_val);
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                $sheet->getStyle('H'.$row)->getFont()->setBold(true);
                $row += 2;

                // Apply outer border to the table range for this account (from headerRow to last data row)
                $startRow = $headerRow;
                $endRow = $row - 3; // adjust to include balance final
                if ($endRow >= $startRow) {
                    $range = 'A'.$startRow.':H'.$endRow;
                    $sheet->getStyle($range)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('BBBBBB');
                }
            }

            // Column widths
            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(12);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(12);
            $sheet->getColumnDimension('E')->setWidth(40);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(12);
            $sheet->getColumnDimension('H')->setWidth(14);

            // Number formats for columns F,G,H
            $sheet->getStyle('F:'.'H')->getNumberFormat()->setFormatCode('#,##0.00');

            // Page setup landscape
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

            // Output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'auxiliares_' . ($start ?: 'start') . '_' . ($end ?: 'end') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $writer->save('php://output');
            return;
        }

        // default: CSV export
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="auxiliares_export.csv"');
        $out = fopen('php://output', 'w');
        $curr_label = ($currency === 'usd') ? ' (USD)' : '';
        fputcsv($out, ['Cuenta','Código','fecha','Tipo Documento','No Documento','Centro costo','Descripcion','Debito' . $curr_label,'Credito' . $curr_label,'Balance Final' . $curr_label]);
        foreach ($rows as $acc) {
            // opening row for account (convert if needed)
            $opening_val = ($currency === 'usd') ? ($acc['opening'] / $exchange_rate) : $acc['opening'];
            fputcsv($out, [$acc['code'] . ' ' . $acc['name'], $acc['code'], ($acc['opening'] ? 'Saldo anterior' : ''), '', '', '', '', number_format($opening_val,2,'.','')]);
            foreach ($acc['lines'] as $l) {
                $debit_val = ($currency === 'usd') ? (($l['debit'] ?? 0) / $exchange_rate) : ($l['debit'] ?? 0);
                $credit_val = ($currency === 'usd') ? (($l['credit'] ?? 0) / $exchange_rate) : ($l['credit'] ?? 0);
                $bal_val = ($currency === 'usd') ? (($l['balance'] ?? 0) / $exchange_rate) : ($l['balance'] ?? 0);
                fputcsv($out, [$acc['code'] . ' ' . $acc['name'], $acc['code'], $l['date'] ?? '', $l['doc_type'] ?? '', $l['document_no'] ?? '', $l['centro_costo'] ?? '', $l['descripcion'] ?? '', number_format($debit_val,2,'.',''), number_format($credit_val,2,'.',''), number_format($bal_val,2,'.','')]);
            }
            $final_val = ($currency === 'usd') ? ($acc['final_balance'] / $exchange_rate) : $acc['final_balance'];
            fputcsv($out, ['','', 'Balance Final','', '', '', '', number_format($final_val,2,'.','')]);
        }
        fclose($out);
    }

    // Modal para seleccionar cuentas en el reporte Auxiliares
    public function modal_auxiliares()
    {
        $data['accounts'] = $this->Contabilidad_model->get_accounts();
        $this->load->view('contabilidad/modal_auxiliares', $data);
    }

    // AJAX: get journal as JSON
    public function journal_detail()
    {
        $id = $this->input->get('id');
        header('Content-Type: application/json');
        if (!$id) { echo json_encode(['status'=>'error','error'=>'id requerido']); return; }
        $data = $this->Contabilidad_model->get_journal($id);
        if (!$data) { echo json_encode(['status'=>'error','error'=>'no encontrado']); return; }
        echo json_encode(['status'=>'success','data'=>$data]);
    }

    // AJAX: return modal HTML for creating a new journal (partial view only)
    public function modal_add()
    {
        // provide centros_costo for the modal selects
        $this->load->model('Centro_costo_model');
        $data = [];
        if (method_exists($this->Centro_costo_model, 'get_all_active')) {
            $data['centros_costo'] = $this->Centro_costo_model->get_all_active();
        } else {
            $data['centros_costo'] = [];
        }
        // return only the modal fragment (no header/footer)
        $this->load->view('contabilidad/modal_add', $data);
    }

    // AJAX: return accounts list as JSON for selects
    public function accounts()
    {
        header('Content-Type: application/json');
        $accounts = $this->Contabilidad_model->get_accounts();
        echo json_encode(['status' => 'success', 'data' => $accounts]);
    }

    // AJAX: list entries (journals) for Diario page
    public function list_entries()
    {
        header('Content-Type: application/json');
        $entries = $this->Contabilidad_model->get_journals();
        echo json_encode(['status' => 'success', 'data' => $entries]);
    }

    // AJAX: update an existing journal (edit)
    public function update_entry()
    {
        $post = $this->input->post();
        header('Content-Type: application/json');
        $id = isset($post['id']) ? intval($post['id']) : null;
        if (!$id) { echo json_encode(['status'=>'error','error'=>'id requerido']); return; }
        // Prevent edits when the target period is closed (admins may override)
        $date = isset($post['date']) ? $post['date'] : null;
        if ($date && $this->Contabilidad_model->is_period_closed($date)) {
            $isAdmin = (method_exists($this->ion_auth, 'is_admin') && $this->ion_auth->logged_in() && $this->ion_auth->is_admin());
            if (! $isAdmin) {
                echo json_encode(['status'=>'error','error'=>'Periodo cerrado: no puede editar asientos en este mes']); return;
            }
        }
        // normalize lines array
        $lines = isset($post['lines']) ? $post['lines'] : [];
        // prepare payload
        $payload = [
            'date' => isset($post['date']) ? $post['date'] : date('Y-m-d'),
            'description' => isset($post['description']) ? $post['description'] : '',
            'lines' => $lines
        ];
        $ok = $this->Contabilidad_model->update_journal($id, $payload);
        if ($ok) echo json_encode(['status'=>'success']); else echo json_encode(['status'=>'error','error'=>'falló al actualizar']);
    }

    // AJAX: reverse (reversar) a journal by creating an opposite journal
    public function reverse_entry()
    {
        // Mark a journal as voided (anulado) instead of creating a reversing journal
        $id = $this->input->post('id');
        header('Content-Type: application/json');
        if (!$id) { echo json_encode(['status'=>'error','error'=>'id requerido']); return; }
        $j = $this->Contabilidad_model->get_journal($id);
        if (!$j) { echo json_encode(['status'=>'error','error'=>'no encontrado']); return; }
        
        // Check if entry is posted (mayorizado)
        if (isset($j['header']->posted) && $j['header']->posted == 1) {
            echo json_encode(['status'=>'error','error'=>'No se puede anular un asiento mayorizado. Primero debe desmayorizarlo.']);
            return;
        }
        
        // update tb_journal set voided=1, voided_by=user, voided_at=now()
        $user = $this->ion_auth->logged_in() ? $this->ion_auth->get_user_id() : null;
        // Ensure the 'voided' columns exist; if not, add them (safe idempotent checks)
        if (!$this->db->field_exists('voided', 'tb_journal')) {
            // add missing columns
            $this->db->query("ALTER TABLE `tb_journal` ADD COLUMN `voided` TINYINT(1) NOT NULL DEFAULT 0");
        }
        if (!$this->db->field_exists('voided_by', 'tb_journal')) {
            $this->db->query("ALTER TABLE `tb_journal` ADD COLUMN `voided_by` INT NULL");
        }
        if (!$this->db->field_exists('voided_at', 'tb_journal')) {
            $this->db->query("ALTER TABLE `tb_journal` ADD COLUMN `voided_at` DATETIME NULL");
        }

        $ok = $this->db->where('id', intval($id))->update('tb_journal', ['voided' => 1, 'voided_by' => $user, 'voided_at' => date('Y-m-d H:i:s')]);
        if ($ok) echo json_encode(['status'=>'success']); else echo json_encode(['status'=>'error','error'=>'falló al anular']);
    }

    // Export journals to Excel with formatting
    public function export_csv()
    {
        require_once APPPATH . '../vendor/autoload.php';
        
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $entries = $this->Contabilidad_model->get_journals();
        if ($start || $end) {
            $filtered = [];
            foreach ($entries as $e) {
                $d = date('Y-m-d', strtotime($e->date));
                if ($start && $d < $start) continue;
                if ($end && $d > $end) continue;
                $filtered[] = $e;
            }
            $entries = $filtered;
        }

        // Exclude voided (anulados) entries from export
        $entries = array_values(array_filter($entries, function($e){
            return !(isset($e->voided) && intval($e->voided) === 1);
        }));
        
        // Get exchange rate for USD conversion
        $this->db->select('tasa_cambio');
        $this->db->order_by('fecha', 'DESC');
        $this->db->limit(1);
        $tasa = $this->db->get('tb_tasa_cambio')->row();
        $exchange_rate = $tasa && isset($tasa->tasa_cambio) ? floatval($tasa->tasa_cambio) : 36.50;
        
        // Calculate totals
        $total_debit_nio = 0;
        $total_credit_nio = 0;
        $total_debit_usd = 0;
        $total_credit_usd = 0;
        
        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(12);
        
        // Título en A1 (combinado hasta I1) - estilo más elegante
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'REPORTE DE ASIENTOS CONTABLES');
        $sheet->getStyle('A1')->getFont()->setName('Calibri')->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0B5394');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getRowDimension(1)->setRowHeight(36);
        
        // Headers en fila 2
        $headers = [
            'ID Asiento',
            'Fecha',
            'Descripcion del Asiento',
            'Total Debito NIO',
            'Total Debito USD',
            'Total Credito NIO',
            'Total Credito USD',
            'Usuario Creador',
            'Mayorizado'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '2', $header);
            $col++;
        }
        
        // Estilo de headers (más legible y profesional)
        $headerStyle = [
            'font' => [
                'name' => 'Calibri',
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0B5394']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9E1F2']
                ]
            ]
        ];
        $sheet->getStyle('A2:I2')->applyFromArray($headerStyle);
        $sheet->getRowDimension(2)->setRowHeight(26);
        // Habilitar autofiltro y congelar la fila de headers
        $sheet->setAutoFilter('A2:I' . (count($entries) + 2));
        $sheet->freezePane('A3');
        
        // Datos
        $row = 3;
        foreach ($entries as $e) {
            // Get user name
            $username = '';
            if (isset($e->created_by) && $e->created_by) {
                $this->db->select('username, first_name, last_name');
                $this->db->where('id', $e->created_by);
                $user = $this->db->get('users')->row();
                if ($user) {
                    $username = trim(($user->first_name ? $user->first_name . ' ' : '') . ($user->last_name ? $user->last_name : ''));
                    if (!$username) $username = $user->username;
                }
            }
            
            // Calculate USD amounts
            $debit_usd = $e->total_debit / $exchange_rate;
            $credit_usd = $e->total_credit / $exchange_rate;
            
            // Accumulate totals
            $total_debit_nio += $e->total_debit;
            $total_credit_nio += $e->total_credit;
            $total_debit_usd += $debit_usd;
            $total_credit_usd += $credit_usd;
            
            // Posted status
            $posted_status = (isset($e->posted) && $e->posted == 1) ? 'Si' : 'No';
            
            $sheet->setCellValue('A' . $row, $e->id);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($e->date)));
            $sheet->setCellValue('C' . $row, $e->description);
            $sheet->setCellValue('D' . $row, $e->total_debit);
            $sheet->setCellValue('E' . $row, $debit_usd);
            $sheet->setCellValue('F' . $row, $e->total_credit);
            $sheet->setCellValue('G' . $row, $credit_usd);
            $sheet->setCellValue('H' . $row, $username);
            $sheet->setCellValue('I' . $row, $posted_status);
            
            // Formato de números con separadores de miles
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            
            // Alineación
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row . ':G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Bordes
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('CCCCCC');
            
            $row++;
        }
        
        // Fila de SUBTOTALES
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, 'SUBTOTALES');
        $sheet->setCellValue('D' . $row, $total_debit_nio);
        $sheet->setCellValue('E' . $row, $total_debit_usd);
        $sheet->setCellValue('F' . $row, $total_credit_nio);
        $sheet->setCellValue('G' . $row, $total_credit_usd);
        $sheet->setCellValue('H' . $row, '');
        $sheet->setCellValue('I' . $row, '');
        
        // Formato de números en totales
        $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Estilo de subtotales (igual que headers)
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($headerStyle);
        $sheet->getStyle('D' . $row . ':G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // Generate file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="libro_diario.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    // Modal para crear/editar cuenta (catalogo)
    public function modal_account()
    {
        $id = $this->input->get('id');
        $data = [];
        if ($id) {
            $data['account'] = $this->Contabilidad_model->get_account($id);
        }
        // Load report lines config for ER/BS dropdowns (load without sections)
        $this->config->load('report_lines', FALSE, TRUE);
        $rl = $this->config->item('report_lines');
        $data['er_lines'] = isset($rl['er']) ? $rl['er'] : [];
        $data['bs_lines'] = isset($rl['bs']) ? $rl['bs'] : [];
        $this->load->view('contabilidad/modal_account', $data);
    }

    // Página: catálogo de cuentas
    public function catalogo()
    {
        $page = max(1, intval($this->input->get('page')));
        $page_size = intval($this->input->get('page_size')) ?: 25;
        $offset = ($page - 1) * $page_size;

        $data = array(
            'titulo' => 'Catálogo de Cuentas',
            'subtitulo' => 'Gestión del Plan de Cuentas',
            'icono' => 'fas fa-calculator',
            'scripts' => array('js/contabilidad_catalogo.js')
        );

        // Paginated server-side snapshot of accounts; JS may still fetch full tree later
        $pag = $this->Contabilidad_model->get_accounts_with_balance_paginated($page_size, $offset);
        $data['accounts'] = $pag['rows'];
        $data['total_accounts'] = $pag['total'];
        $data['page'] = $page;
        $data['page_size'] = $page_size;
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/catalogo', $data);
        $this->load->view('layout/footer');
    }

    // API: guardar cuenta (crear/editar)
    public function account_save()
    {
        $post = $this->input->post();
        $id = isset($post['id']) && $post['id'] ? intval($post['id']) : null;
        $code = isset($post['code']) ? trim($post['code']) : '';
        $name = isset($post['name']) ? trim($post['name']) : '';
        $type = isset($post['type']) ? trim($post['type']) : '';
        $naturaleza = isset($post['naturaleza']) && $post['naturaleza'] !== '' ? trim($post['naturaleza']) : null;
        $parent_id = (isset($post['parent_id']) && $post['parent_id'] !== '') ? intval($post['parent_id']) : null;
        $muc_class = isset($post['muc_class']) && $post['muc_class'] !== '' ? intval($post['muc_class']) : null;
        $muc_group = isset($post['muc_group']) ? trim($post['muc_group']) : null;
        $muc_subgroup = isset($post['muc_subgroup']) ? trim($post['muc_subgroup']) : null;
        $level = isset($post['level']) && $post['level'] !== '' ? intval($post['level']) : 4;
        $is_mayor = isset($post['is_mayor']) && ($post['is_mayor'] == '1' || $post['is_mayor'] == 1) ? 1 : 0;
        $statement = isset($post['statement']) ? trim($post['statement']) : 'BS';
        $regulatory_code = isset($post['regulatory_code']) ? trim($post['regulatory_code']) : null;
        $must_report = isset($post['must_report']) && ($post['must_report'] == '1' || $post['must_report'] == 1) ? 1 : 0;
        $postable = isset($post['postable']) && ($post['postable'] == '1' || $post['postable'] == 1) ? 1 : 0;
        $report_is = isset($post['report_is']) && $post['report_is'] !== '' ? trim($post['report_is']) : null;
        $report_bs = isset($post['report_bs']) && $post['report_bs'] !== '' ? trim($post['report_bs']) : null;

        $errors = [];
        // Validaciones básicas
        if ($code === '') $errors[] = 'El código es obligatorio.';
        if ($name === '') $errors[] = 'El nombre es obligatorio.';
        $allowed = ['activo','pasivo','patrimonio','ingreso','gasto'];
        if (!in_array($type, $allowed)) $errors[] = 'Tipo de cuenta inválido.';
        if ($naturaleza && !in_array($naturaleza, ['deudora', 'acreedora'])) $errors[] = 'Naturaleza de cuenta inválida.';
        // report_is and report_bs are free-form keys chosen from dropdowns (validation deferred)
        if ($parent_id && $id && $parent_id == $id) $errors[] = 'La cuenta padre no puede ser la misma cuenta.';

        // Código con formato: números y puntos (ej. 1.100)
        if ($code !== '' && !preg_match('/^[0-9]+(\.[0-9]+)*$/', $code)) {
            $errors[] = 'El código debe contener sólo números y puntos, p. ej. 1.100.';
        }
        // Código único
        if ($code !== '' && $this->Contabilidad_model->code_exists($code, $id)) {
            $errors[] = 'El código ya existe en otra cuenta.';
        }

        header('Content-Type: application/json');
        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        $payload = [
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'naturaleza' => $naturaleza,
            'parent_id' => $parent_id,
            'muc_class' => $muc_class,
            'muc_group' => $muc_group,
            'muc_subgroup' => $muc_subgroup,
            'level' => $level,
            'statement' => $statement,
            'regulatory_code' => $regulatory_code,
            'must_report' => $must_report,
            'postable' => $postable,
        ];
        // include is_mayor flag
        $payload['is_mayor'] = $is_mayor;
        // include report mappings in payload (DB columns may not exist; model will handle)
        $payload['report_is'] = $report_is;
        $payload['report_bs'] = $report_bs;

        if ($id) {
            $ok = $this->Contabilidad_model->update_account($id, $payload);
        } else {
            $ok = $this->Contabilidad_model->create_account($payload);
        }

        if ($ok) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error', 'errors' => ['Error al guardar en base de datos.']]);
    }

    // API: eliminar cuenta
    public function account_delete()
    {
        $id = $this->input->post('id');
        header('Content-Type: application/json');
        if (!$id) { echo json_encode(['status' => 'error', 'errors' => ['ID inválido']]); return; }
        // no permitir eliminar si tiene asientos
        if ($this->Contabilidad_model->account_has_entries(intval($id))) {
            echo json_encode(['status' => 'error', 'errors' => ['La cuenta tiene asientos asociados y no puede eliminarse.']]);
            return;
        }
        $ok = $this->Contabilidad_model->delete_account(intval($id));
        echo json_encode(['status' => $ok ? 'success' : 'error']);
    }

    // Export catalogo to Excel with filters
    public function catalogo_export()
    {
        $search = trim($this->input->get('search') ?? '');
        $type = strtolower(trim($this->input->get('type') ?? ''));
        $report_type = strtolower(trim($this->input->get('report_type') ?? 'basic'));
        
        // Get all accounts with balance (returns objects)
        $accounts_obj = $this->Contabilidad_model->get_accounts_with_balance();
        
        // Convert objects to arrays
        $accounts = array_map(function($obj) {
            return (array) $obj;
        }, $accounts_obj);
        
        // Apply filters
        $filtered = $accounts;
        
        // Search filter (3+ characters)
        if ($search && strlen($search) >= 3) {
            $filtered = array_filter($filtered, function($a) use ($search) {
                $code = strtolower($a['code'] ?? '');
                $name = strtolower($a['name'] ?? '');
                $searchLower = strtolower($search);
                return (strpos($code, $searchLower) !== false) || (strpos($name, $searchLower) !== false);
            });
        }
        
        // Type filter
        if ($type && in_array($type, ['activo','pasivo','patrimonio','ingreso','gasto'])) {
            $filtered = array_filter($filtered, function($a) use ($type) {
                return strtolower($a['type'] ?? '') === $type;
            });
        }
        
        require_once APPPATH . '../vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        if ($report_type === 'bimoneda') {
            // Balance Bimoneda Report
            // Get exchange rate
            $this->db->select('tasa_cambio');
            $this->db->order_by('fecha', 'DESC');
            $this->db->limit(1);
            $tasa = $this->db->get('tb_tasa_cambio')->row();
            $tasaCambio = $tasa && isset($tasa->tasa_cambio) ? floatval($tasa->tasa_cambio) : 36.50;
            
            // Title row
            $sheet->mergeCells('A1:E1');
            $sheet->setCellValue('A1', 'BALANCE BIMONEDA');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E78');
            $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');
            
            // Header row
            $headers = ['Código', 'Nombre', 'Tipo', 'Balance C$', 'Balance USD'];
            $sheet->fromArray($headers, null, 'A2');
            $headerStyle = $sheet->getStyle('A2:E2');
            $headerStyle->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E78');
            $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Data rows
            $row = 3;
            foreach ($filtered as $acc) {
                $balance = isset($acc['balance']) ? floatval($acc['balance']) : 0;
                $balanceUSD = $tasaCambio > 0 ? ($balance / $tasaCambio) : 0;
                
                $sheet->setCellValue('A' . $row, $acc['code'] ?? '');
                $sheet->setCellValue('B' . $row, $acc['name'] ?? '');
                $sheet->setCellValue('C' . $row, $acc['type'] ?? '');
                $sheet->setCellValue('D' . $row, $balance);
                $sheet->setCellValue('E' . $row, $balanceUSD);
                
                $row++;
            }
            
            // Format balance columns
            $lastRow = $row - 1;
            if ($lastRow >= 3) {
                $sheet->getStyle('D3:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('D3:E' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
            
            // Auto-size columns
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $filename = 'Balance_Bimoneda_' . date('Ymd_His') . '.xlsx';
        } else {
            // Basic Catalog Report
            // Title row
            $sheet->mergeCells('A1:D1');
            $sheet->setCellValue('A1', 'CATÁLOGO DE CUENTAS');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E78');
            $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');
            
            // Header row
            $headers = ['Código', 'Nombre', 'Tipo', 'Agrupación', 'Balance'];
            $sheet->fromArray($headers, null, 'A2');
            $headerStyle = $sheet->getStyle('A2:E2');
            $headerStyle->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E78');
            $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Data rows
            $row = 3;
            foreach ($filtered as $acc) {
                $balance = isset($acc['balance']) ? floatval($acc['balance']) : 0;
                
                $sheet->setCellValue('A' . $row, $acc['code'] ?? '');
                $sheet->setCellValue('B' . $row, $acc['name'] ?? '');
                $sheet->setCellValue('C' . $row, $acc['type'] ?? '');
                $sheet->setCellValue('D' . $row, $acc['report_bs'] ?? '');
                $sheet->setCellValue('E' . $row, $balance);
                
                $row++;
            }
            
            // Format balance column
            $lastRow = $row - 1;
            if ($lastRow >= 3) {
                $sheet->getStyle('E3:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('E3:E' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
            
            // Auto-size columns
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $filename = 'Catalogo_Cuentas_' . date('Ymd_His') . '.xlsx';
        }
        
        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // Página: Libro Diario
    public function diario()
    {
        $data = array(
            'titulo' => 'Libro Diario',
            'subtitulo' => 'Registro de asientos contables',
            'icono' => 'fas fa-book-open',
            // load both diario script and the generic contabilidad modal helper
            'scripts' => array('js/contabilidad_diario.js','js/contabilidad.js','js/contabilidad_modal_enhanced.js')
        );
        // server-side entries for immediate rendering (fallback when JS is cached/blocked)
        $data['entries'] = $this->Contabilidad_model->get_journals();
        
        // Include company info for PDF export
        $this->load->model('Core_model');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $data['empresa'] = $empresa;
        
        // Load centros de costo for modal
        $this->load->model('Centro_costo_model');
        $data['centros_costo'] = $this->Centro_costo_model->get_all_active();
        
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/diario', $data);
        $this->load->view('layout/footer');
    }
    
    // AJAX: Get detailed lines for multiple entries (for PDF export)
    public function get_entries_details()
    {
        header('Content-Type: application/json');
        
        // Get JSON input
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);
        
        if (!isset($input['entry_ids']) || !is_array($input['entry_ids'])) {
            echo json_encode(['success' => false, 'message' => 'entry_ids requerido']);
            return;
        }
        
        $entry_ids = array_map('intval', $input['entry_ids']);
        
        if (empty($entry_ids)) {
            echo json_encode(['success' => false, 'message' => 'No hay IDs de asientos']);
            return;
        }
        
        // Get all lines for these entries with account info
        $this->db->select('l.*, a.code as account_code, a.name as account_name, a.type as account_type, j.date, j.entry_type, j.description as entry_description');
        $this->db->from('tb_journal_lines l');
        $this->db->join('tb_accounts a', 'l.account_id = a.id', 'left');
        $this->db->join('tb_journals j', 'l.journal_id = j.id', 'left');
        $this->db->where_in('l.journal_id', $entry_ids);
        $this->db->order_by('j.date', 'ASC');
        $this->db->order_by('j.id', 'ASC');
        $this->db->order_by('l.id', 'ASC');
        
        $query = $this->db->get();
        $lines = $query->result_array();
        
        // Format the lines
        $formatted_lines = array();
        foreach ($lines as $line) {
            $formatted_lines[] = array(
                'entry_id' => $line['journal_id'],
                'entry_type' => $line['entry_type'],
                'date' => date('d/m/Y', strtotime($line['date'])),
                'account_code' => $line['account_code'],
                'account_name' => $line['account_name'],
                'account_type' => $line['account_type'],
                'description' => $line['description'] ?: $line['entry_description'],
                'debit' => $line['debit'],
                'credit' => $line['credit']
            );
        }
        
        echo json_encode(['success' => true, 'lines' => $formatted_lines]);
    }

    // Página: Libro Mayor
    public function mayor()
    {
        $data = array(
            'titulo' => 'Libro Mayor',
            'subtitulo' => 'Reporte de saldos por cuenta',
            'icono' => 'fas fa-layer-group',
            'scripts' => array('js/contabilidad_mayor.js')
        );
        // include system/company info for printing header
        $this->load->model('Core_model');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $data['empresa'] = $empresa;
        // Provide published (mayorizado) journals from Libro Diario so this page
        // simply lists only mayorizados (posted = 1) as requested.
        $all = $this->Contabilidad_model->get_journals();
        $posted = [];
        if ($all) {
            foreach ($all as $j) {
                if (isset($j->posted) && intval($j->posted) === 1) $posted[] = $j;
            }
        }
        $data['entries'] = $posted;

        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/mayor', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: obtener ledger (movimientos por cuenta) con balance acumulado
    public function mayor_data()
    {
        $account = $this->input->get('account_id');
        $start = $this->input->get('start_date');
        $page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
        $per_page = $this->input->get('per_page') ? intval($this->input->get('per_page')) : 500;
        $end = $this->input->get('end_date');
        header('Content-Type: application/json');
        if (!$account) { echo json_encode(['status'=>'error','error'=>'account_id requerido']); return; }
        $data = $this->Contabilidad_model->get_ledger(intval($account), $start, $end, $page, $per_page);
        echo json_encode(['status'=>'success','data'=>$data]);
    }

    // Export mayor as CSV
    public function mayor_export()
    {
        // Clean any output buffer
        if (ob_get_length()) ob_end_clean();
        
        require_once APPPATH . '../vendor/autoload.php';
        
        $account = $this->input->get('account_id');
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        if (!$account) { show_error('account_id requerido', 400); return; }
        $page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
        $per_page = $this->input->get('per_page') ? intval($this->input->get('per_page')) : 500;
        
        try {
            $data = $this->Contabilidad_model->get_ledger(intval($account), $start, $end, $page, $per_page);
        } catch (Exception $e) {
            log_message('error', 'Mayor export error: ' . $e->getMessage());
            show_error('Error al obtener datos: ' . $e->getMessage(), 500);
            return;
        }
        
        // Get account info
        $account_info = $this->Contabilidad_model->get_account(intval($account));
        $account_name = $account_info ? $account_info->code . ' - ' . $account_info->name : 'Cuenta ' . $account;
        
        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        
        // Title row - "Libro Mayor" centered
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Libro Mayor');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('002060');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // Headers row
        $headers = ['Tipo Cuenta', 'Fecha', 'Asiento ID', 'Descripcion', 'Debito', 'Credito', 'Balance Actual'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
            $sheet->getStyle($col . '2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('002060');
            $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        
        $row = 3;
        
        // Opening balance row
        if (isset($data['opening_balance']) && $data['opening_balance'] != 0) {
            $sheet->setCellValue('A' . $row, $data['account_type']);
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, '');
            $sheet->setCellValue('D' . $row, 'Saldo Inicial');
            $sheet->setCellValue('E' . $row, '-');
            $sheet->setCellValue('F' . $row, '-');
            $sheet->setCellValue('G' . $row, $data['opening_balance']);
            
            // Format opening balance row
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('D' . $row)->getFont()->setBold(true);
            $row++;
        }
        
        // Data rows
        foreach ($data['entries'] as $e) {
            $sheet->setCellValue('A' . $row, $data['account_type']);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($e['date'])));
            $sheet->setCellValue('C' . $row, $e['journal_id']);
            $sheet->setCellValue('D' . $row, $e['description']);
            
            // Debit and Credit - show "-" if zero, otherwise show formatted number
            if ($e['debit'] > 0) {
                $sheet->setCellValue('E' . $row, $e['debit']);
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            } else {
                $sheet->setCellValue('E' . $row, '-');
            }
            
            if ($e['credit'] > 0) {
                $sheet->setCellValue('F' . $row, $e['credit']);
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            } else {
                $sheet->setCellValue('F' . $row, '-');
            }
            
            $sheet->setCellValue('G' . $row, $e['running_balance']);
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            
            $row++;
        }
        
        // Apply borders to all data
        $lastRow = $row - 1;
        $sheet->getStyle('A1:G' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
            ->getColor()->setARGB('000000');
        
        // Align columns
        $sheet->getStyle('A3:C' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E3:G' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        
        // Freeze header rows
        $sheet->freezePane('A3');
        
        // Generate and download
        $filename = 'Libro_Mayor_' . $account . '_' . date('Ymd') . '.xlsx';
        
        // Clear any previous output
        if (ob_get_contents()) ob_end_clean();
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        
        // Clean up
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    // Export all mayor (ignore pagination) — returns CSV of all records for account/range
    public function mayor_export_all()
    {
        $account = $this->input->get('account_id');
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        if (!$account) { show_error('account_id requerido', 400); return; }
        // request with very large per_page to obtain all rows
        $data = $this->Contabilidad_model->get_ledger(intval($account), $start, $end, 1, 100000000);
        header('Content-Type: text/csv');
        $fname = 'libro_mayor_account_' . $account . '_all.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['account_id' , 'account_type', 'date', 'journal_id', 'entry_id', 'description', 'debit', 'credit', 'running_balance', 'side']);
        if (isset($data['opening_balance'])) {
            fputcsv($out, ['', $data['account_type'], '', '', '', 'Saldo inicial', '', '', number_format($data['opening_balance'],2,'.',''), '']);
        }
        foreach ($data['entries'] as $e) {
            fputcsv($out, [ $data['account_id'], $data['account_type'], $e['date'], $e['journal_id'], $e['entry_id'], $e['description'], number_format($e['debit'],2,'.',''), number_format($e['credit'],2,'.',''), number_format($e['running_balance'],2,'.',''), $e['side'] ]);
        }
        fclose($out);
    }

    // Export posted journals (filtered) as Excel
    public function mayor_export_posted()
    {
        if (ob_get_length()) ob_end_clean();
        require_once APPPATH . '../vendor/autoload.php';

        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $asiento = $this->input->get('asiento');
        $desc = $this->input->get('description');

        // Get all journals and filter for mayorizados
        $all = $this->Contabilidad_model->get_journals();
        $filtered = array();
        if ($all) {
            foreach ($all as $j) {
                if (!isset($j->posted) || intval($j->posted) !== 1) continue;
                // date filtering
                if ($start && isset($j->date)) {
                    if (strtotime($j->date) < strtotime($start)) continue;
                }
                if ($end && isset($j->date)) {
                    if (strtotime($j->date) > strtotime($end . ' 23:59:59')) continue;
                }
                if ($asiento && strpos((string)$j->id, $asiento) === false) continue;
                if ($desc && stripos((string)($j->description ?? ''), $desc) === false) continue;
                $filtered[] = $j;
            }
        }

        // Build spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Default font
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(60);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(14);

        // Title
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Libro Mayor - Asientos Mayorizados');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0B5394');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Headers
        $headers = ['Fecha', 'Asiento ID', 'Descripción', 'Total Debe', 'Total Haber', 'Estado'];
        $r = 2; $c = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($c . $r, $h);
            $sheet->getStyle($c . $r)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($c . $r)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E78');
            $sheet->getStyle($c . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $c++;
        }

        $row = 3;
        foreach ($filtered as $f) {
            $fecha = isset($f->date) ? date('d/m/Y', strtotime($f->date)) : '';
            $sheet->setCellValue('A' . $row, $fecha);
            $sheet->setCellValue('B' . $row, intval($f->id));
            $sheet->setCellValue('C' . $row, $f->description ?? '');
            $debe = isset($f->total_debit) ? floatval($f->total_debit) : 0;
            $haber = isset($f->total_credit) ? floatval($f->total_credit) : 0;
            $sheet->setCellValue('D' . $row, $debe);
            $sheet->setCellValue('E' . $row, $haber);
            $sheet->setCellValue('F' . $row, (isset($f->posted) && intval($f->posted) === 1) ? 'Mayorizado' : 'Pendiente');

            // number formats
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        // Alignments
        $last = max(3, $row - 1);
        $sheet->getStyle('A3:A' . $last)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B3:B' . $last)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D3:E' . $last)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Remove gridlines and adjust page setup to portrait fit-to-width
        $sheet->setShowGridlines(false);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4);
        $sheet->getPageMargins()->setBottom(0.4);
        $sheet->getPageMargins()->setLeft(0.4);
        $sheet->getPageMargins()->setRight(0.4);

        // Signature block: add spacing then three signature lines
        $sigStart = $row + 2;
        // Merge ranges for three sigs across A-F (pairs)
        $sheet->mergeCells('A' . $sigStart . ':B' . $sigStart);
        $sheet->mergeCells('C' . $sigStart . ':D' . $sigStart);
        $sheet->mergeCells('E' . $sigStart . ':F' . $sigStart);
        // apply top border to create signature line
        $lineStyle = [
            'borders' => [
                'top' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb'=>'000000'] ]
            ]
        ];
        $sheet->getStyle('A' . $sigStart . ':B' . $sigStart)->applyFromArray($lineStyle);
        $sheet->getStyle('C' . $sigStart . ':D' . $sigStart)->applyFromArray($lineStyle);
        $sheet->getStyle('E' . $sigStart . ':F' . $sigStart)->applyFromArray($lineStyle);
        // labels in next row
        $sheet->setCellValue('A' . ($sigStart + 1), 'Contador General');
        $sheet->setCellValue('C' . ($sigStart + 1), 'Gerente General');
        $sheet->setCellValue('E' . ($sigStart + 1), 'Administrador');
        $sheet->getStyle('A' . ($sigStart + 1) . ':F' . ($sigStart + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Output
        $filename = 'Mayorizados_' . date('Ymd_His') . '.xlsx';
        if (ob_get_contents()) ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }
    
    // Export all accounts with their ledgers as JSON for PDF generation
    public function mayor_export_all_pdf()
    {
        header('Content-Type: application/json');
        
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        
        // Get all accounts
        $accounts = $this->Contabilidad_model->get_accounts();
        
        $result = array();
        foreach ($accounts as $account) {
            // Get ledger for each account with all entries
            $ledger = $this->Contabilidad_model->get_ledger($account->id, $start, $end, 1, 100000000);
            
            // Only include accounts with movements
            if (!empty($ledger['entries']) || (isset($ledger['opening_balance']) && $ledger['opening_balance'] != 0)) {
                $result[] = array(
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'opening_balance' => isset($ledger['opening_balance']) ? $ledger['opening_balance'] : 0,
                    'entries' => $ledger['entries']
                );
            }
        }
        
        echo json_encode(array('success' => true, 'accounts' => $result));
    }

    // Export single journal as CSV
    public function journal_export()
    {
        // Clean any output buffer
        if (ob_get_length()) ob_end_clean();
        
        require_once APPPATH . '../vendor/autoload.php';
        
        $id = $this->input->get('id');
        if (!$id) { show_error('id requerido', 400); return; }
        $j = $this->Contabilidad_model->get_journal($id);
        if (!$j) { show_error('Asiento no encontrado', 404); return; }
        
        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(35);
        
        // Title row
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Detalle de Asiento Contable');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('002060');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // Journal header info
        $row = 2;
        $sheet->setCellValue('A' . $row, 'Asiento No:');
        $sheet->setCellValue('B' . $row, $id);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Fecha:');
        $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($j['header']->date)));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Descripción:');
        $sheet->setCellValue('B' . $row, $j['header']->description);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->mergeCells('B' . $row . ':F' . $row);
        $row++;
        
        // Empty row
        $row++;
        
        // Headers for lines
        $headers = ['Cuenta', 'Código', 'Nombre de Cuenta', 'Debe', 'Haber', 'Descripción'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('002060');
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }
        $headerRow = $row;
        $row++;
        
        // Data rows
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($j['lines'] as $l) {
            $sheet->setCellValue('A' . $row, $l->id);
            $sheet->setCellValue('B' . $row, $l->code);
            $sheet->setCellValue('C' . $row, $l->name);
            
            if ($l->debit > 0) {
                $sheet->setCellValue('D' . $row, $l->debit);
                $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $totalDebit += $l->debit;
            } else {
                $sheet->setCellValue('D' . $row, '-');
            }
            
            if ($l->credit > 0) {
                $sheet->setCellValue('E' . $row, $l->credit);
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $totalCredit += $l->credit;
            } else {
                $sheet->setCellValue('E' . $row, '-');
            }
            
            $sheet->setCellValue('F' . $row, $l->line_description);
            $row++;
        }
        
        // Totals row
        $sheet->setCellValue('C' . $row, 'TOTALES:');
        $sheet->setCellValue('D' . $row, $totalDebit);
        $sheet->setCellValue('E' . $row, $totalCredit);
        $sheet->getStyle('C' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('D' . $row . ':E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        
        $lastRow = $row;
        
        // Apply borders
        $sheet->getStyle('A1:F' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
            ->getColor()->setARGB('000000');
        
        // Align columns
        $sheet->getStyle('A' . ($headerRow + 1) . ':B' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . ($headerRow + 1) . ':E' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        
        // Generate and download
        $filename = 'Asiento_' . $id . '_' . date('Ymd') . '.xlsx';
        
        if (ob_get_contents()) ob_end_clean();
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    // Printable HTML for single journal (used by per-row print)
    public function journal_print()
    {
        $id = $this->input->get('id');
        if (!$id) { echo 'ID requerido'; return; }
        $j = $this->Contabilidad_model->get_journal($id);
        if (!$j) { echo 'Asiento no encontrado'; return; }
        // include empresa header
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $data = ['journal' => $j, 'empresa' => $empresa];
        $this->load->view('contabilidad/journal_print', $data);
    }

    // Página: Balanza de Comprobación
    public function balanza()
    {
        $data = array(
            'titulo' => 'Balanza de Comprobación',
            'subtitulo' => 'Saldos y verificación',
            'icono' => 'fas fa-balance-scale',
            'scripts' => array('js/contabilidad_balanza.js')
        );
        // include empresa info for print header
        $this->load->model('Core_model');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $data['empresa'] = $empresa;
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/balanza', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: obtener balanza (trial balance) agrupada por cuenta
    public function balanza_data()
    {
        $account = $this->input->get('account_id'); // optional
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $include_zero = $this->input->get('include_zero') !== null ? ($this->input->get('include_zero') == '1' ? true : false) : true;
        $group = $this->input->get('group');
        $group_mode = $this->input->get('group_mode') ? $this->input->get('group_mode') : 'prefix';
        $group_param = null;
        if ($group === 'mayor') {
            if ($group_mode === 'level') {
                $group_param = $this->input->get('level') ? intval($this->input->get('level')) : 1;
            } else {
                $group_param = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;
            }
        }
        $only_mayor = $this->input->get('only_mayor') !== null ? ($this->input->get('only_mayor') == '1' ? true : false) : false;
        header('Content-Type: application/json');
        $data = $this->Contabilidad_model->get_trial_balance($start, $end, $account, $include_zero, $group_param, $group_mode, $only_mayor);
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    // Export balanza as CSV (all rows by filters)
    public function balanza_export()
    {
        $account = $this->input->get('account_id'); // optional
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $include_zero = $this->input->get('include_zero') !== null ? ($this->input->get('include_zero') == '1' ? true : false) : true;
        
        // Si se solicita Excel, redirigir a la función de Excel
        $format = $this->input->get('format');
        if ($format === 'excel') {
            return $this->balanza_export_excel();
        }
        
        // CSV export (mantener compatibilidad) — formatear números igual que la vista
        $group = $this->input->get('group');
        $group_mode = $this->input->get('group_mode') ? $this->input->get('group_mode') : 'prefix';
        $group_param = null;
        if ($group === 'mayor') {
            if ($group_mode === 'level') {
                $group_param = $this->input->get('level') ? intval($this->input->get('level')) : 1;
            } else {
                $group_param = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;
            }
        }
        $only_mayor = $this->input->get('only_mayor') !== null ? ($this->input->get('only_mayor') == '1' ? true : false) : false;
        $data = $this->Contabilidad_model->get_trial_balance($start, $end, $account, $include_zero, $group_param, $group_mode, $only_mayor);
        header('Content-Type: text/csv; charset=UTF-8');
        $fname = 'balanza_' . ($start ? $start : 'all') . '_' . ($end ? $end : 'all') . '.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');
        $out = fopen('php://output', 'w');
        // Emit UTF-8 BOM so Excel detects UTF-8
        echo "\xEF\xBB\xBF";
        // Use semicolon as delimiter because numbers use comma as decimal separator
        fputcsv($out, ['account_code','account_name','opening_deudor','opening_acreedor','debits','credits','closing_deudor','closing_acreedor','balance_final'], ',');
        foreach ($data['rows'] as $r) {
            $balance_final = ($r['closing_deudor'] - $r['closing_acreedor']);
            fputcsv($out, [$r['code'],$r['name'], number_format($r['opening_deudor'],2,',','.'), number_format($r['opening_acreedor'],2,',','.'), number_format($r['debits'],2,',','.'), number_format($r['credits'],2,',','.'), number_format($r['closing_deudor'],2,',','.'), number_format($r['closing_acreedor'],2,',','.'), number_format($balance_final,2,',','.')], ';');
        }
        // totals
        fputcsv($out, [] , ';');
        $tot_balance = $data['totals']['closing_deudor'] - $data['totals']['closing_acreedor'];
        fputcsv($out, ['TOTALES', '', number_format($data['totals']['opening_deudor'],2,',','.'), number_format($data['totals']['opening_acreedor'],2,',','.'), number_format($data['totals']['debits'],2,',','.'), number_format($data['totals']['credits'],2,',','.'), number_format($data['totals']['closing_deudor'],2,',','.'), number_format($data['totals']['closing_acreedor'],2,',','.'), number_format($tot_balance,2,',','.')], ';');
        fclose($out);
    }
    
    // Export balanza to Excel with professional formatting
    public function balanza_export_excel()
    {
        require_once APPPATH . '../vendor/autoload.php';
        
        $account = $this->input->get('account_id');
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $include_zero = $this->input->get('include_zero') !== null ? ($this->input->get('include_zero') == '1' ? true : false) : true;
        $group = $this->input->get('group');
        $group_mode = $this->input->get('group_mode') ? $this->input->get('group_mode') : 'prefix';
        $group_param = null;
        if ($group === 'mayor') {
            if ($group_mode === 'level') {
                $group_param = $this->input->get('level') ? intval($this->input->get('level')) : 1;
            } else {
                $group_param = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;
            }
        }
        $data = $this->Contabilidad_model->get_trial_balance($start, $end, $account, $include_zero, $group_param, $group_mode);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        
        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column widths (added Saldo Final columns G/H)
        $sheet->getColumnDimension('A')->setWidth(15); // Código
        $sheet->getColumnDimension('B')->setWidth(45); // Denominación
        $sheet->getColumnDimension('C')->setWidth(15); // Mayor
        $sheet->getColumnDimension('D')->setWidth(15); // Cargos
        $sheet->getColumnDimension('E')->setWidth(15); // Abonos
        $sheet->getColumnDimension('F')->setWidth(15); // Saldo Actual
        $sheet->getColumnDimension('G')->setWidth(18); // Saldo Final (Deudor)
        $sheet->getColumnDimension('H')->setWidth(18); // Saldo Final (Acreedor)
        $sheet->getColumnDimension('I')->setWidth(18); // Balance Final
        
        $row = 1;
        
        // Company name (prominent)
        if ($empresa && isset($empresa->razon_social)) {
            $sheet->mergeCells('A' . $row . ':F' . $row);
            $sheet->setCellValue('A' . $row, mb_strtoupper($empresa->razon_social));
            $sheet->getStyle('A' . $row)->getFont()->setName('Calibri')->setBold(true)->setSize(16);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('0B5394');
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // Report title (clear and visible)
        $sheet->mergeCells('A' . $row . ':I' . $row);
        $sheet->setCellValue('A' . $row, 'BALANZA DE COMPROBACIÓN');
        $sheet->getStyle('A' . $row)->getFont()->setName('Calibri')->setBold(true)->setSize(13);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;

        // Period
        if ($start || $end) {
            $sheet->mergeCells('A' . $row . ':I' . $row);
            $periodText = 'Período: ' . ($start ? date('d/m/Y', strtotime($start)) : 'Inicio');
            $periodText .= ' al ' . ($end ? date('d/m/Y', strtotime($end)) : 'Final');
            $sheet->setCellValue('A' . $row, $periodText);
            $sheet->getStyle('A' . $row)->getFont()->setName('Calibri')->setSize(10);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $row++; // Línea en blanco
        
        // Column headers
        $headers = ['Código', 'Denominación', 'Mayor', 'Cargos', 'Abonos', 'Saldo Actual', 'Saldo Final (Deudor)', 'Saldo Final (Acreedor)', 'Balance Final'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Header style - modern blue
        $headerStyle = [
            'font' => [
                'name' => 'Calibri',
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0B5394'] // Darker blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9E1F2']
                ]
            ]
        ];
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($headerStyle);
        $sheet->getRowDimension($row)->setRowHeight(22);
        // Add autofilter and freeze headers
        $lastHeaderRow = $row;
        $sheet->setAutoFilter('A' . $lastHeaderRow . ':I' . $lastHeaderRow);
        $sheet->freezePane('A' . ($lastHeaderRow + 1));
        $row++;
        
        // Data rows
        if (!empty($data['rows'])) {
            foreach ($data['rows'] as $r) {
                // Calcular el saldo actual (Mayor + Cargos - Abonos)
                $saldo_actual = ($r['opening_deudor'] - $r['opening_acreedor']) + ($r['debits'] - $r['credits']);
                // Saldo final dividido en Deudor / Acreedor
                $saldo_final_deudor = $saldo_actual >= 0 ? $saldo_actual : 0.0;
                $saldo_final_acreedor = $saldo_actual < 0 ? abs($saldo_actual) : 0.0;

                $sheet->setCellValue('A' . $row, $r['code']);
                $sheet->setCellValue('B' . $row, $r['name']);
                $sheet->setCellValue('C' . $row, $r['opening_deudor'] - $r['opening_acreedor']); // Mayor
                $sheet->setCellValue('D' . $row, $r['debits']); // Cargos
                $sheet->setCellValue('E' . $row, $r['credits']); // Abonos
                $sheet->setCellValue('F' . $row, $saldo_actual); // Saldo Actual (raw)
                $sheet->setCellValue('G' . $row, $saldo_final_deudor); // Saldo Final (Deudor)
                $sheet->setCellValue('H' . $row, $saldo_final_acreedor); // Saldo Final (Acreedor)
                $sheet->setCellValue('I' . $row, $saldo_final_deudor - $saldo_final_acreedor); // Balance Final

                // Number format
                $sheet->getStyle('C' . $row . ':I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

                // Alignment
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C' . $row . ':I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                // Borders
                $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('CCCCCC');

                $row++;
            }
        }
        
        // Totals row
        $total_mayor = ($data['totals']['opening_deudor'] - $data['totals']['opening_acreedor']);
        $total_cargos = $data['totals']['debits'];
        $total_abonos = $data['totals']['credits'];
        $total_final_deudor = $data['totals']['closing_deudor'];
        $total_final_acreedor = $data['totals']['closing_acreedor'];

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'TOTALES');
        $sheet->setCellValue('C' . $row, $total_mayor);
        $sheet->setCellValue('D' . $row, $total_cargos);
        $sheet->setCellValue('E' . $row, $total_abonos);
        $sheet->setCellValue('F' . $row, $total_mayor + $total_cargos - $total_abonos);
        $sheet->setCellValue('G' . $row, $total_final_deudor);
        $sheet->setCellValue('H' . $row, $total_final_acreedor);
        $sheet->setCellValue('I' . $row, $total_final_deudor - $total_final_acreedor);

        // Number format for totals
        $sheet->getStyle('C' . $row . ':I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        // Totals style - Bold with blue background
        $totalsStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0070C0']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($totalsStyle);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($row)->setRowHeight(20);
        
        // Add signature placeholders (three sign lines and titles) after totals
        $row++; // spacing row
        // merge cells for three signature areas: A-C, D-F, G-I
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->mergeCells('D' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':I' . $row);
        $topBorderStyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($topBorderStyle);
        $sheet->getStyle('D' . $row . ':F' . $row)->applyFromArray($topBorderStyle);
        $sheet->getStyle('G' . $row . ':I' . $row)->applyFromArray($topBorderStyle);
        $sheet->getRowDimension($row)->setRowHeight(18);

        $row++;
        $sheet->setCellValue('A' . $row, 'Contador General');
        $sheet->setCellValue('D' . $row, 'Gerente Financiero');
        $sheet->setCellValue('G' . $row, 'Gerente General');
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row . ':F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . $row . ':I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(18);
        
        // Generate file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'balanza_comprobacion_' . ($start ? $start : 'all') . '_' . ($end ? $end : 'all') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    // Upload Excel to mark accounts as 'Cuenta de Mayor' based on a column 'Tipo' (Mayor/Detalle)
    public function import_mayor_excel()
    {
        // simple permission check (you may adapt to your auth)
        if (!isset($_FILES['file'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded (field "file").']);
            return;
        }
        $f = $_FILES['file'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Upload error code: ' . $f['error']]);
            return;
        }
        $tmp = $f['tmp_name'];
        // load php spreadsheet
        require_once APPPATH . 'third_party/autoload.php';
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmp);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error reading spreadsheet: ' . $e->getMessage()]);
            return;
        }
        $sheet = $reader->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) < 2) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Spreadsheet appears empty or has no data rows.']);
            return;
        }
        // ensure is_mayor column exists
        $this->load->model('Contabilidad_model');
        $ok = $this->Contabilidad_model->ensure_is_mayor_column();
        if (!$ok) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Could not ensure is_mayor column exists on account table.']);
            return;
        }
        // find header columns: try to detect 'Codigo' and 'Tipo'
        $header = $rows[1];
        $codeCol = null; $typeCol = null;
        foreach ($header as $col => $val) {
            $h = strtolower(trim((string)$val));
            if (in_array($h, ['codigo','code','cod'])) $codeCol = $col;
            if (in_array($h, ['tipo','type','tipo de cuenta'])) $typeCol = $col;
        }
        if (!$codeCol || !$typeCol) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Could not detect header columns. Expected headers: Codigo, Tipo']);
            return;
        }
        $updated = 0; $notfound = 0; $errors = 0; $total = 0;
        // iterate from row 2
        for ($r = 2; $r <= count($rows); $r++) {
            $total++;
            $row = $rows[$r];
            $code = isset($row[$codeCol]) ? trim((string)$row[$codeCol]) : '';
            $tipo = isset($row[$typeCol]) ? trim((string)$row[$typeCol]) : '';
            if ($code === '') { $notfound++; continue; }
            $is_mayor = 0;
            if (stripos($tipo, 'mayor') !== false) $is_mayor = 1;
            // update via model
            $res = $this->Contabilidad_model->set_account_is_mayor_by_code($code, $is_mayor);
            if ($res === false) $errors++; else if ($res == 0) $notfound++; else $updated += intval($res);
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'total_rows' => $total, 'updated_rows' => $updated, 'not_found' => $notfound, 'errors' => $errors]);
    }

    // Export all balanza as CSV in streaming mode (iterate accounts to avoid large memory spikes)
    public function balanza_export_all()
    {
        $account = $this->input->get('account_id'); // optional
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');

        $group = $this->input->get('group');
        $group_mode = $this->input->get('group_mode') ? $this->input->get('group_mode') : 'prefix';
        $prefix_len = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;
        $group_param = null;
        if ($group === 'mayor') {
            if ($group_mode === 'level') {
                $group_param = $this->input->get('level') ? intval($this->input->get('level')) : 1;
            } else {
                $group_param = $prefix_len;
            }
        }

        // headers for CSV download
        header('Content-Type: text/csv');
        $fname = 'balanza_all_' . ($start ? $start : 'all') . '_' . ($end ? $end : 'all') . '.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');

        $out = fopen('php://output', 'w');
        // CSV header
        fputcsv($out, ['account_code','account_name','opening_deudor','opening_acreedor','debits','credits','closing_deudor','closing_acreedor','balance_final']);
        // iterate accounts in batches to avoid loading everything in memory
        $include_zero = $this->input->get('include_zero') !== null ? ($this->input->get('include_zero') == '1' ? true : false) : true;
        $this->load->model('Contabilidad_model');
        $this->load->database();

        $this->db->select('id');
        $this->db->from('tb_account');
        if ($account) $this->db->where('id', intval($account));
        $this->db->order_by('code','asc');
        $q = $this->db->get();
        foreach ($q->result() as $arow) {
            $aid = intval($arow->id);
            // reuse model function but filtered to one account (model returns rows array)
            if (isset($group) && $group === 'mayor') {
                $d = $this->Contabilidad_model->get_trial_balance($start, $end, $aid, $include_zero, $group_param, $group_mode);
            } else {
                $d = $this->Contabilidad_model->get_trial_balance($start, $end, $aid, $include_zero);
            }
            if (!isset($d['rows']) || count($d['rows']) == 0) continue;
            foreach ($d['rows'] as $r) {
                $balance = round(floatval($r['closing_deudor']) - floatval($r['closing_acreedor']),2);
                fputcsv($out, [$r['code'],$r['name'], number_format($r['opening_deudor'],2,'.',''), number_format($r['opening_acreedor'],2,'.',''), number_format($r['debits'],2,'.',''), number_format($r['credits'],2,'.',''), number_format($r['closing_deudor'],2,'.',''), number_format($r['closing_acreedor'],2,'.',''), number_format($balance,2,'.','')]);
                // flush so the response is streamed progressively
                if (function_exists('ob_flush')) { @ob_flush(); }
                if (function_exists('flush')) { @flush(); }
            }
        }
        // totals: compute once via model for filters
        $totalData = $this->Contabilidad_model->get_trial_balance($start, $end, null, $include_zero, $group_param, $group_mode);
        fputcsv($out, []);
        $totbal = round(floatval($totalData['totals']['closing_deudor']) - floatval($totalData['totals']['closing_acreedor']),2);
        fputcsv($out, ['TOTALES', '', number_format($totalData['totals']['opening_deudor'],2,'.',''), number_format($totalData['totals']['opening_acreedor'],2,'.',''), number_format($totalData['totals']['debits'],2,'.',''), number_format($totalData['totals']['credits'],2,'.',''), number_format($totalData['totals']['closing_deudor'],2,'.',''), number_format($totalData['totals']['closing_acreedor'],2,'.',''), number_format($totbal,2,'.','')]);
        fclose($out);
    }

    // NEW: Stable CSV export that outputs rounded raw numeric values (dot decimal)
    public function balanza_export_new()
    {
        $account = $this->input->get('account_id');
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');

        $this->load->model('Contabilidad_model');
        $include_zero = $this->input->get('include_zero') == '1' ? true : false;
        $group = $this->input->get('group');
        $group_mode = $this->input->get('group_mode') ? $this->input->get('group_mode') : 'prefix';
        $group_param = null;
        if ($group === 'mayor') {
            if ($group_mode === 'level') {
                $group_param = $this->input->get('level') ? intval($this->input->get('level')) : 1;
            } else {
                $group_param = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;
            }
        }
        $data = $this->Contabilidad_model->get_trial_balance($start, $end, $account, $include_zero, $group_param, $group_mode);

        header('Content-Type: text/csv; charset=UTF-8');
        $fname = 'balanza_fixed_' . ($start ? $start : 'all') . '_' . ($end ? $end : 'all') . '.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');

        $out = fopen('php://output','w');
        // Emit BOM
        echo "\xEF\xBB\xBF";

        // Use comma delimiter per user request
        fputcsv($out, ['account_code','account_name','opening_deudor','opening_acreedor','debits','credits','closing_deudor','closing_acreedor','balance_final'], ',');

        foreach ($data['rows'] as $r) {
            // Round numeric values to 2 decimals to match displayed sums
            $od = round(floatval($r['opening_deudor']),2);
            $oa = round(floatval($r['opening_acreedor']),2);
            $db = round(floatval($r['debits']),2);
            $cr = round(floatval($r['credits']),2);
            $cd = round(floatval($r['closing_deudor']),2);
            $ca = round(floatval($r['closing_acreedor']),2);
            // Output raw numeric with dot as decimal separator (no thousands)
            $bal = number_format(round($cd - $ca,2),2,'.','');
            fputcsv($out, [$r['code'],$r['name'], number_format($od,2,'.',''), number_format($oa,2,'.',''), number_format($db,2,'.',''), number_format($cr,2,'.',''), number_format($cd,2,'.',''), number_format($ca,2,'.',''), $bal], ',');
        }

        fputcsv($out, [] , ',');
        $tot_bal = number_format(round($data['totals']['closing_deudor'] - $data['totals']['closing_acreedor'],2),2,'.','');
        fputcsv($out, ['TOTALES','', number_format(round($data['totals']['opening_deudor'],2),2,'.',''), number_format(round($data['totals']['opening_acreedor'],2),2,'.',''), number_format(round($data['totals']['debits'],2),2,'.',''), number_format(round($data['totals']['credits'],2),2,'.',''), number_format(round($data['totals']['closing_deudor'],2),2,'.',''), number_format(round($data['totals']['closing_acreedor'],2),2,'.',''), $tot_bal], ',');
        fclose($out);
    }

    // NEW: streaming version for all accounts with same stable numeric format
    public function balanza_export_all_new()
    {
        $account = $this->input->get('account_id');
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');

        $group = $this->input->get('group');
        $prefix_len = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;

        header('Content-Type: text/csv; charset=UTF-8');
        $fname = 'balanza_all_fixed_' . ($start ? $start : 'all') . '_' . ($end ? $end : 'all') . '.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');
        $out = fopen('php://output','w');
        echo "\xEF\xBB\xBF";
        fputcsv($out, ['account_code','account_name','opening_deudor','opening_acreedor','debits','credits','closing_deudor','closing_acreedor','balance_final'], ';');

        $include_zero = $this->input->get('include_zero') == '1' ? true : false;
        $this->load->model('Contabilidad_model');
        $this->load->database();

        $this->db->select('id');
        $this->db->from('tb_account');
        if ($account) $this->db->where('id', intval($account));
        $this->db->order_by('code','asc');
        $q = $this->db->get();
        foreach ($q->result() as $arow) {
            $aid = intval($arow->id);
            $d = $this->Contabilidad_model->get_trial_balance($start, $end, $aid, $include_zero, ($group === 'mayor' ? $prefix_len : null));
            if (!isset($d['rows']) || count($d['rows']) == 0) continue;
            foreach ($d['rows'] as $r) {
                $od = round(floatval($r['opening_deudor']),2);
                $oa = round(floatval($r['opening_acreedor']),2);
                $db = round(floatval($r['debits']),2);
                $cr = round(floatval($r['credits']),2);
                $cd = round(floatval($r['closing_deudor']),2);
                $ca = round(floatval($r['closing_acreedor']),2);
                $bal = number_format(round($cd - $ca,2),2,'.','');
                fputcsv($out, [$r['code'],$r['name'], number_format($od,2,'.',''), number_format($oa,2,'.',''), number_format($db,2,'.',''), number_format($cr,2,'.',''), number_format($cd,2,'.',''), number_format($ca,2,'.',''), $bal], ',');
            }
        }
        fclose($out);
    }

    // DEBUG: compare model values and CSV serialization for a single account code
    public function balanza_debug_account()
    {
        $code = $this->input->get('code');
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        header('Content-Type: application/json');
        if (!$code) { echo json_encode(['status'=>'error','message'=>'code param required']); return; }
        $this->load->database();
        $this->db->where('code', $code);
        $acct = $this->db->get('tb_account')->row();
        if (!$acct) { echo json_encode(['status'=>'error','message'=>'account not found']); return; }
        $this->load->model('Contabilidad_model');
        $d = $this->Contabilidad_model->get_trial_balance($start, $end, $acct->id, false);
        $row = isset($d['rows'][0]) ? $d['rows'][0] : null;
        if (!$row) { echo json_encode(['status'=>'ok','account'=> $code, 'note'=>'no rows (all zeros)']); return; }

        // view-like formatted strings (thousands '.' decimal ',')
        $fmt_view = function($v){ return number_format(round(floatval($v),2),2,',','.'); };
        $view = [
            'opening_deudor' => $fmt_view($row['opening_deudor']),
            'opening_acreedor' => $fmt_view($row['opening_acreedor']),
            'debits' => $fmt_view($row['debits']),
            'credits' => $fmt_view($row['credits']),
            'closing_deudor' => $fmt_view($row['closing_deudor']),
            'closing_acreedor' => $fmt_view($row['closing_acreedor'])
        ];

        // CSV old serialization (localized with comma decimal + dot thousand, semicolon separator may be used elsewhere)
        $old_csv_row = implode(',', [$row['code'], $row['name'], $view['opening_deudor'], $view['opening_acreedor'], $view['debits'], $view['credits'], $view['closing_deudor'], $view['closing_acreedor'], number_format(round(floatval($row['closing_deudor'])-floatval($row['closing_acreedor']),2),2,',','.')]);

        // CSV new fixed serialization (raw numeric, dot decimal)
        $raw = function($v){ return number_format(round(floatval($v),2),2,'.',''); };
        $new_csv_row = implode(',', [$row['code'], $row['name'], $raw($row['opening_deudor']), $raw($row['opening_acreedor']), $raw($row['debits']), $raw($row['credits']), $raw($row['closing_deudor']), $raw($row['closing_acreedor']), number_format(round(floatval($row['closing_deudor'])-floatval($row['closing_acreedor']),2),2,'.','')]);

        echo json_encode(['status'=>'ok','account'=>$code,'model_row'=>$row,'view_strings'=>$view,'old_csv_row'=>$old_csv_row,'new_csv_row'=>$new_csv_row]);
    }

    // Printable HTML for trial balance (Balanza)
    public function balanza_print()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $account = $this->input->get('account_id');
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $include_zero = $this->input->get('include_zero') == '1' ? true : false;
        $group = $this->input->get('group');
        $prefix_len = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;
        $data = $this->Contabilidad_model->get_trial_balance($start, $end, $account, $include_zero, ($group === 'mayor' ? $prefix_len : null));
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $viewData = ['data' => $data, 'empresa' => $empresa, 'start' => $start, 'end' => $end];
        $this->load->view('contabilidad/balanza_print', $viewData);
    }

    // Generate a PDF of the trial balance using Dompdf and return as download
    public function balanza_pdf()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $account = $this->input->get('account_id');
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');

        $include_zero = $this->input->get('include_zero') == '1' ? true : false;
        $group = $this->input->get('group');
        $prefix_len = $this->input->get('prefix_len') ? intval($this->input->get('prefix_len')) : null;
        $data = $this->Contabilidad_model->get_trial_balance($start, $end, $account, $include_zero, ($group === 'mayor' ? $prefix_len : null));
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));

        // exporter info
        $exported_by = null;
        if ($this->load->is_loaded('ion_auth') || isset($this->ion_auth)) {
            try { $u = $this->ion_auth->user()->row(); $exported_by = isset($u->username) ? $u->username : null; } catch(Exception $e) { $exported_by = null; }
        }
        $exported_at = date('Y-m-d H:i:s');

        // render HTML using a dedicated view
        $html = $this->load->view('contabilidad/balanza_pdf', ['data' => $data, 'empresa' => $empresa, 'start' => $start, 'end' => $end, 'exported_by' => $exported_by, 'exported_at' => $exported_at], true);

        // include dompdf (project contains dompdf/ at FCPATH)
        if (!defined('FCPATH')) define('FCPATH', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
        $dompfPath = FCPATH . 'dompdf' . DIRECTORY_SEPARATOR . 'autoload.inc.php';
        if (!file_exists($dompfPath)) {
            show_error('Dompdf no encontrado en ' . $dompfPath . '. Coloca la librería dompdf en la raíz del proyecto.', 500);
            return;
        }
        require_once $dompfPath;

        // instantiate and render using double-pass to embed document hash in content
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);

        // first pass
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $firstPdf = $dompdf->output();
        $hash = md5($firstPdf);

        // inject hash into HTML placeholder and re-render
        $htmlWithHash = str_replace('{hash}', $hash, $html);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($htmlWithHash);
        $dompdf->render();
        $finalPdf = $dompdf->output();

        $filename = 'balanza_' . ($start ? $start : 'all') . '_' . ($end ? $end : 'all') . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($finalPdf));
        header('X-Report-Hash: ' . $hash);
        echo $finalPdf;
    }

    // POST: file upload + periodoMes + periodoAnio + offset_account_code + auto_post (optional)
    // Endpoint to force-import balanza CSV using CI DB connection (wraps temp/importar_balanza_force.php logic)
    public function importar_balanza_force_ci()
    {
        header('Content-Type: application/json');
        // ensure file present
        if (empty($_FILES) || !isset($_FILES['balanzaFile'])) {
            echo json_encode(['status'=>'error','message'=>'No file uploaded (balanzaFile)']); return;
        }
        $mes = $this->input->post('periodoMes');
        $anio = $this->input->post('periodoAnio');
        $offset_code = trim($this->input->post('offset_account_code'));
        $auto_post = $this->input->post('auto_post') !== null ? intval($this->input->post('auto_post')) : 1;
        if (!$mes || !$anio) { echo json_encode(['status'=>'error','message'=>'Missing periodoMes or periodoAnio']); return; }
        if (!$offset_code) { echo json_encode(['status'=>'error','message'=>'offset_account_code required']); return; }

        // move uploaded to temp location
        $tmp = $_FILES['balanzaFile']['tmp_name'];
        if (!is_uploaded_file($tmp)) { echo json_encode(['status'=>'error','message'=>'Upload failed']); return; }

        $fecha = date('Y-m-t', strtotime($anio . '-' . $mes . '-01'));

        // helper parser
        $parse_number = function($s) {
            $s = trim($s);
            if ($s === '') return 0.0;
            $s = trim($s, "\"' ");
            $s = str_replace("'", '', $s);
            if (strpos($s, ',') !== false && strpos($s, '.') !== false) { $s = str_replace(',', '', $s); }
            elseif (strpos($s, ',') !== false && strpos($s, '.') === false) { $s = str_replace(',', '.', $s); }
            $s = str_replace(' ', '', $s);
            $s = preg_replace('/[^0-9\.\-]/', '', $s);
            return floatval($s);
        };

        // parse CSV
        $h = fopen($tmp, 'r');
        if (!$h) { echo json_encode(['status'=>'error','message'=>'Cannot open uploaded file']); return; }
        $hdr = fgetcsv($h);
        $rows = [];
        while (($r = fgetcsv($h)) !== false) {
            if (count($r) < 2) continue;
            $cod = trim($r[0]); if ($cod === '') continue;
            $name = isset($r[1]) ? trim($r[1]) : '';
            if (count($r) >= 5) {
                $cargos = $parse_number($r[2] ?? '0');
                $abonos = $parse_number($r[3] ?? '0');
                $saldo  = $parse_number($r[4] ?? '0');
            } else if (count($r) >= 6) {
                $cargos = $parse_number($r[3] ?? '0');
                $abonos = $parse_number($r[4] ?? '0');
                $saldo  = $parse_number($r[5] ?? '0');
            } else {
                $cargos = $parse_number($r[count($r)-3] ?? '0');
                $abonos = $parse_number($r[count($r)-2] ?? '0');
                $saldo  = $parse_number($r[count($r)-1] ?? '0');
            }
            $rows[] = ['code'=>$cod,'name'=>$name,'cargos'=>$cargos,'abonos'=>$abonos,'saldo'=>$saldo];
        }
        fclose($h);
        if (count($rows) == 0) { echo json_encode(['status'=>'error','message'=>'No rows parsed from CSV']); return; }

        // lookup offset account id
        $acct = $this->db->get_where('tb_account', ['code' => $offset_code], 1)->row_array();
        if (!$acct) { echo json_encode(['status'=>'error','message'=>'Offset account not found: ' . $offset_code]); return; }
        $offset_id = intval($acct['id']);

        // build adjustments
        $adjustments = [];
        foreach ($rows as $rr) {
            $code = $rr['code'];
            $a = $this->db->get_where('tb_account', ['code' => $code], 1)->row_array();
            if (!$a) { $adjustments[] = ['code'=>$code,'found'=>false,'note'=>'account not found']; continue; }
            $aid = intval($a['id']); $atype = strtolower($a['type']);
            $factor = 1; if (in_array($atype, ['pasivo','patrimonio','ingreso'])) $factor = -1;
            $sql = "SELECT IFNULL(SUM(e.debit - e.credit),0) as raw FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = ? AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= ?";
            $q = $this->db->query($sql, [$aid, $fecha]);
            $curr_raw = floatval($q->row()->raw);
            $curr_display = $curr_raw * $factor;
            $desired_display = floatval($rr['saldo']);
            $diff_display = $desired_display - $curr_display;
            $raw_diff = $diff_display / ($factor == 0 ? 1 : $factor);
            if (abs($raw_diff) < 0.005) {
                $adjustments[] = ['code'=>$code,'found'=>true,'account_id'=>$aid,'name'=>$a['name'],'current_display'=>round($curr_display,2),'desired_display'=>round($desired_display,2),'raw_diff'=>0.0,'note'=>'no adjustment'];
                continue;
            }
            $adjustments[] = ['code'=>$code,'found'=>true,'account_id'=>$aid,'name'=>$a['name'],'current_display'=>round($curr_display,2),'desired_display'=>round($desired_display,2),'raw_diff'=>round($raw_diff,2)];
        }

        // prepare entries
        $entries = [];
        $total_debit = 0.0; $total_credit = 0.0;
        foreach ($adjustments as $a) {
            if (!isset($a['found']) || !$a['found']) continue;
            $v = floatval($a['raw_diff']);
            if (abs($v) < 0.005) continue;
            if ($v > 0) { $entries[] = ['account_id'=>$a['account_id'],'debit'=>round($v,2),'credit'=>0.0,'description'=>'Ajuste import']; $total_debit += round($v,2); }
            else { $entries[] = ['account_id'=>$a['account_id'],'debit'=>0.0,'credit'=>round(abs($v),2),'description'=>'Ajuste import']; $total_credit += round(abs($v),2); }
        }

        if ($total_debit > $total_credit) { $bal = round($total_debit - $total_credit,2); $entries[] = ['account_id'=>$offset_id,'debit'=>0.0,'credit'=>$bal,'description'=>'Ajuste import (compensacion)']; $total_credit += $bal; }
        elseif ($total_credit > $total_debit) { $bal = round($total_credit - $total_debit,2); $entries[] = ['account_id'=>$offset_id,'debit'=>$bal,'credit'=>0.0,'description'=>'Ajuste import (compensacion)']; $total_debit += $bal; }

        $total_debit = round($total_debit,2); $total_credit = round($total_credit,2);
        if (abs($total_debit - $total_credit) > 0.01) { echo json_encode(['status'=>'error','message'=>'Cannot build balanced journal','total_debit'=>$total_debit,'total_credit'=>$total_credit]); return; }

        // insert journal via CI
        $description = 'Import forced balances ' . $mes . '/' . $anio . ' - CI import';
        $posted = $auto_post ? 1 : 0;
        $now = date('Y-m-d H:i:s');
        $this->db->trans_start();
        $this->db->insert('tb_journal', ['date'=>$fecha,'description'=>$description,'total_debit'=>$total_debit,'total_credit'=>$total_credit,'posted'=>$posted,'created_at'=>$now]);
        $journal_id = $this->db->insert_id();
        foreach ($entries as $e) {
            $this->db->insert('tb_journal_entry', ['journal_id'=>$journal_id,'account_id'=>$e['account_id'],'debit'=>$e['debit'],'credit'=>$e['credit'],'description'=>$e['description']]);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) { echo json_encode(['status'=>'error','message'=>'DB transaction failed']); return; }

        echo json_encode(['status'=>'ok','message'=>'Adjusting journal created','journal_id'=>$journal_id,'posted'=>$posted,'total_debit'=>$total_debit,'total_credit'=>$total_credit,'entries_count'=>count($entries),'adjustments'=>$adjustments]);
        return;
    }

    // Create a background job that will generate the PDF and return a job status URL
    public function balanza_pdf_job()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $account = $this->input->get('account_id');

        // ensure reports table exists
        $this->ensure_reports_table_exists();

        // build printable URL
        $query = [];
        if ($start) $query['start_date'] = $start;
        if ($end) $query['end_date'] = $end;
        if ($account) $query['account_id'] = $account;
        $printUrl = site_url('contabilidad/balanza_print') . '?' . http_build_query($query);

        // create DB job
        $jobId = uniqid('balanza_', true);
        $reportsDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
        if (!is_dir($reportsDir)) mkdir($reportsDir, 0755, true);
        $outFile = 'uploads/reports/' . $jobId . '.pdf';

        $created_by = null;
        if (isset($this->ion_auth) && $this->ion_auth->logged_in()) {
            $u = $this->ion_auth->user()->row();
            $created_by = isset($u->username) ? $u->username : null;
        }

        $insert = [
            'job_id' => $jobId,
            'type' => 'balanza_pdf',
            'print_url' => $printUrl,
            'file_path' => $outFile,
            'status' => 'pending',
            'created_by' => $created_by,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tb_reports', $insert);

        // spawn worker CLI in background to process job (worker will pick pending jobs too)
        $phpCli = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
        $workerScript = FCPATH . 'scripts' . DIRECTORY_SEPARATOR . 'pdf_worker.php';
        // pass job id, printUrl and outFile to worker so it doesn't need DB credentials
        $cmd = '"' . $phpCli . '" "' . $workerScript . '" ' . escapeshellarg($jobId) . ' ' . escapeshellarg($printUrl) . ' ' . escapeshellarg(FCPATH . $outFile);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen('start /B "" ' . $cmd, 'r'));
        } else {
            exec($cmd . ' > /dev/null 2>&1 &');
        }

        $statusUrl = site_url('contabilidad/balanza_pdf_status_db') . '?job=' . urlencode($jobId);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'accepted', 'job_id' => $jobId, 'status_url' => $statusUrl, 'download_url' => base_url($outFile)]);
    }

    // Endpoint used by worker to report job completion/error
    public function balanza_pdf_worker_report()
    {
        $post = $this->input->post();
        header('Content-Type: application/json');
        $job = isset($post['job']) ? $post['job'] : null;
        if (!$job) { echo json_encode(['status' => 'error','error'=>'job required']); return; }
        $status = isset($post['status']) ? $post['status'] : 'error';
        $file_hash = isset($post['file_hash']) ? $post['file_hash'] : null;
        $error_text = isset($post['error_text']) ? $post['error_text'] : null;
        $now = date('Y-m-d H:i:s');
        $this->ensure_reports_table_exists();
        $update = ['status' => $status, 'finished_at' => $now];
        if ($file_hash) $update['file_hash'] = $file_hash;
        if ($error_text) $update['error_text'] = $error_text;
        if ($status === 'processing') $update['started_at'] = $now;
        $this->db->where('job_id', $job)->update('tb_reports', $update);
        echo json_encode(['status'=>'ok']);
    }

    // Upload a signature image and store path in tb_sistema.firma (id=1)
    public function upload_signature()
    {
        header('Content-Type: application/json');
        if (empty($_FILES) || !isset($_FILES['firma'])) {
            echo json_encode(['status'=>'error','error'=>'No file uploaded']);
            return;
        }
        $file = $_FILES['firma'];
        if ($file['error'] !== 0) {
            echo json_encode(['status'=>'error','error'=>'Upload error code: '.$file['error']]);
            return;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['png','jpg','jpeg','gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            echo json_encode(['status'=>'error','error'=>'Tipo de archivo no permitido']);
            return;
        }
        $uploads = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploads)) mkdir($uploads, 0755, true);
        $target = 'firma_' . time() . '.' . $ext;
        $dest = $uploads . $target;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            echo json_encode(['status'=>'error','error'=>'No se pudo mover el archivo']);
            return;
        }

        // ensure column exists
        if (!$this->db->field_exists('firma', 'tb_sistema')) {
            $this->db->query("ALTER TABLE `tb_sistema` ADD COLUMN `firma` VARCHAR(255) NULL");
        }
        // update record id=1
        $ok = $this->db->where('id', 1)->update('tb_sistema', ['firma' => $target]);
        if ($ok) echo json_encode(['status'=>'success','path' => base_url('uploads/'.$target)]); else echo json_encode(['status'=>'error','error'=>'DB update failed']);
    }

    // Poll endpoint to check whether background PDF is ready
    public function balanza_pdf_status()
    {
        $job = $this->input->get('job');
        header('Content-Type: application/json');
        if (!$job) { echo json_encode(['status' => 'error', 'error' => 'job required']); return; }
        $reportsDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
        $outFile = $reportsDir . $job . '.pdf';
        $doneFile = $outFile . '.done';
        $errFile = $outFile . '.error.txt';
        if (file_exists($outFile) && file_exists($doneFile)) {
            echo json_encode(['status' => 'done', 'download_url' => base_url('uploads/reports/' . $job . '.pdf')]);
            return;
        }
        if (file_exists($errFile)) {
            $err = file_get_contents($errFile);
            echo json_encode(['status' => 'error', 'error' => $err]);
            return;
        }
        echo json_encode(['status' => 'pending']);
    }

    // DB-backed status endpoint
    public function balanza_pdf_status_db()
    {
        $job = $this->input->get('job');
        header('Content-Type: application/json');
        if (!$job) { echo json_encode(['status' => 'error', 'error' => 'job required']); return; }
        // ensure table exists
        $this->ensure_reports_table_exists();
        $q = $this->db->get_where('tb_reports', ['job_id' => $job]);
        $r = $q->row_array();
        if (!$r) { echo json_encode(['status' => 'error', 'error' => 'job not found']); return; }
        if ($r['status'] === 'done') {
            echo json_encode(['status' => 'done', 'download_url' => base_url($r['file_path']), 'file_hash' => $r['file_hash']]);
            return;
        }
        if ($r['status'] === 'error') {
            echo json_encode(['status' => 'error', 'error' => $r['error_text']]);
            return;
        }
        echo json_encode(['status' => $r['status']]);
    }

    // Ensure the reports table exists (simple schema apply if missing)
    protected function ensure_reports_table_exists()
    {
        if ($this->db->table_exists('tb_reports')) return;
        $sql = "CREATE TABLE IF NOT EXISTS `tb_reports` (
          `job_id` VARCHAR(64) NOT NULL,
          `type` VARCHAR(50) NOT NULL,
          `print_url` TEXT,
          `file_path` TEXT,
          `status` ENUM('pending','processing','done','error') NOT NULL DEFAULT 'pending',
          `created_by` VARCHAR(100) DEFAULT NULL,
          `created_at` DATETIME NOT NULL,
          `started_at` DATETIME DEFAULT NULL,
          `finished_at` DATETIME DEFAULT NULL,
          `error_text` TEXT DEFAULT NULL,
          `file_hash` VARCHAR(128) DEFAULT NULL,
          PRIMARY KEY (`job_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->query($sql);
    }

    // Estados Financieros: Balance General
    public function balance()
    {
        $data = array(
            'titulo' => 'Balance General',
            'subtitulo' => 'Reporte de situación financiera',
            'icono' => 'fas fa-balance-scale',
            'scripts' => array('js/contabilidad_balance.js')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/balance', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: obtener Balance General (as of date)
    public function balance_data()
    {
        $as_of = $this->input->get('as_of'); // YYYY-MM-DD
        header('Content-Type: application/json');
        $this->load->model('Contabilidad_model');
        $data = $this->Contabilidad_model->get_balance_sheet($as_of);
        echo json_encode(['status' => 'success', 'data' => $data, 'as_of' => $as_of]);
    }

    // Debug endpoint: return counts and sample accounts per group + raw sums for inspection
    public function balance_debug()
    {
        $as_of = $this->input->get('as_of');
        header('Content-Type: application/json');
        $this->load->model('Contabilidad_model');
        $sheet = $this->Contabilidad_model->get_balance_sheet($as_of);

        // prepare samples
        $samples = [];
        foreach (['activo','pasivo','patrimonio','ingreso','gasto'] as $g) {
            $list = isset($sheet['groups'][$g]) ? $sheet['groups'][$g] : [];
            $samples[$g] = [
                'count' => count($list),
                'first' => count($list) ? $list[0] : null,
                'sum_raw' => array_sum(array_map(function($r){ return isset($r['raw']) ? floatval($r['raw']) : 0; }, $list)),
                'sum_display' => array_sum(array_map(function($r){ return isset($r['display']) ? floatval($r['display']) : 0; }, $list))
            ];
        }

        echo json_encode(['status' => 'ok', 'as_of' => $as_of, 'totals' => $sheet['totals'], 'samples' => $samples]);
    }

    // Export Balance General as CSV (as_of optional)
    public function balance_export()
    {
        $as_of = $this->input->get('as_of');
        $this->load->model('Contabilidad_model');
        $sheet = $this->Contabilidad_model->get_balance_sheet($as_of);

        header('Content-Type: text/csv');
        $fname = 'balance_' . ($as_of ? $as_of : 'all') . '.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');
        $out = fopen('php://output', 'w');
        // header
        fputcsv($out, ['Grupo','Código','Cuenta','Saldo']);
        // activos
        if (isset($sheet['groups']['activo'])) {
            fputcsv($out, ['ACTIVO']);
            foreach ($sheet['groups']['activo'] as $r) {
                fputcsv($out, ['Activo', $r['code'], $r['name'], number_format($r['display'],2,'.','')]);
            }
            fputcsv($out, ['Total Activo', '', '', number_format($sheet['totals']['activo'],2,'.','')]);
            fputcsv($out, []);
        }
        // pasivo
        if (isset($sheet['groups']['pasivo'])) {
            fputcsv($out, ['PASIVO']);
            foreach ($sheet['groups']['pasivo'] as $r) {
                fputcsv($out, ['Pasivo', $r['code'], $r['name'], number_format(abs($r['display']),2,'.','')]);
            }
            fputcsv($out, ['Total Pasivo', '', '', number_format($sheet['totals']['pasivo'],2,'.','')]);
            fputcsv($out, []);
        }
        // patrimonio
        if (isset($sheet['groups']['patrimonio'])) {
            fputcsv($out, ['PATRIMONIO']);
            foreach ($sheet['groups']['patrimonio'] as $r) {
                fputcsv($out, ['Patrimonio', $r['code'], $r['name'], number_format(abs($r['display']),2,'.','')]);
            }
            fputcsv($out, ['Total Patrimonio', '', '', number_format($sheet['totals']['patrimonio'],2,'.','')]);
            fputcsv($out, []);
        }

        // summary check
        fputcsv($out, ['TOTAL ACTIVO', '', '', number_format($sheet['totals']['activo'],2,'.','')]);
        fputcsv($out, ['TOTAL PASIVO + PATRIMONIO', '', '', number_format($sheet['totals']['pasivo_patrimonio'],2,'.','')]);

        fclose($out);
    }

    // Printable balance view
    public function balance_print()
    {
        $as_of = $this->input->get('as_of');
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $sheet = $this->Contabilidad_model->get_balance_sheet($as_of);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $viewData = ['data' => $sheet, 'empresa' => $empresa, 'as_of' => $as_of];
        $this->load->view('contabilidad/balance_print', $viewData);
    }

    // Generate PDF for Balance General using Dompdf (double-pass hash embed)
    public function balance_pdf()
    {
        $as_of = $this->input->get('as_of');
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $data = $this->Contabilidad_model->get_balance_sheet($as_of);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));

        $exported_by = null;
        if ($this->load->is_loaded('ion_auth') || isset($this->ion_auth)) {
            try { $u = $this->ion_auth->user()->row(); $exported_by = isset($u->username) ? $u->username : null; } catch(Exception $e) { $exported_by = null; }
        }
        $exported_at = date('Y-m-d H:i:s');

        $html = $this->load->view('contabilidad/balance_pdf', ['data' => $data, 'empresa' => $empresa, 'as_of' => $as_of, 'exported_by' => $exported_by, 'exported_at' => $exported_at], true);

        if (!defined('FCPATH')) define('FCPATH', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
        $dompfPath = FCPATH . 'dompdf' . DIRECTORY_SEPARATOR . 'autoload.inc.php';
        if (!file_exists($dompfPath)) {
            show_error('Dompdf no encontrado en ' . $dompfPath . '. Coloca la librería dompdf en la raíz del proyecto.', 500);
            return;
        }
        require_once $dompfPath;

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);

        // first pass
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $firstPdf = $dompdf->output();
        $hash = md5($firstPdf);

        $htmlWithHash = str_replace('{hash}', $hash, $html);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($htmlWithHash);
        $dompdf->render();
        $finalPdf = $dompdf->output();

        $filename = 'balance_' . ($as_of ? $as_of : 'all') . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($finalPdf));
        header('X-Report-Hash: ' . $hash);
        echo $finalPdf;
    }

    // ========== CENTROS DE COSTO ==========
    
    // Página: Centros de Costo
    public function centros_costo()
    {
        $data = array(
            'titulo' => 'Centros de Costo',
            'subtitulo' => 'Gestión de centros de costo',
            'icono' => 'fas fa-building',
            'scripts' => array('js/contabilidad_centros_costo.js')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/centros_costo', $data);
        $this->load->view('layout/footer');
    }
    
    // AJAX: Listar centros de costo
    public function centros_costo_list()
    {
        header('Content-Type: application/json');
        $this->load->model('Centro_costo_model');
        
        $this->db->order_by('codigo', 'ASC');
        $centros = $this->db->get('tb_centro_costo')->result();
        
        echo json_encode(['status' => 'success', 'data' => $centros]);
    }
    
    // AJAX: Modal para agregar/editar
    public function centros_costo_modal()
    {
        $this->load->view('contabilidad/modal_centro_costo');
    }
    
    // AJAX: Guardar centro de costo
    public function centros_costo_save()
    {
        header('Content-Type: application/json');
        $post = $this->input->post();
        
        // Validar campos requeridos
        if (empty($post['codigo']) || empty($post['nombre'])) {
            echo json_encode(['status' => 'error', 'message' => 'Código y nombre son obligatorios']);
            return;
        }
        
        $id = isset($post['id']) && $post['id'] ? intval($post['id']) : null;
        
        // Verificar que el código no exista (excepto si es edición del mismo registro)
        $this->db->where('codigo', $post['codigo']);
        if ($id) {
            $this->db->where('id !=', $id);
        }
        $exists = $this->db->get('tb_centro_costo')->row();
        
        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'El código ya está en uso']);
            return;
        }
        
        $data = [
            'codigo' => trim($post['codigo']),
            'nombre' => trim($post['nombre']),
            'descripcion' => trim($post['descripcion'] ?? ''),
            'activo' => isset($post['activo']) ? 1 : 0
        ];
        
        if ($id) {
            // Actualizar
            $this->db->where('id', $id);
            $result = $this->db->update('tb_centro_costo', $data);
            $message = 'Centro de costo actualizado correctamente';
        } else {
            // Crear
            $result = $this->db->insert('tb_centro_costo', $data);
            $message = 'Centro de costo creado correctamente';
        }
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => $message]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar']);
        }
    }
    
    // AJAX: Eliminar centro de costo
    public function centros_costo_delete()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id');
        
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID requerido']);
            return;
        }
        
        // Verificar que no esté en uso
        $this->db->where('centro_costo_id', $id);
        $inUse = $this->db->get('tb_journal')->num_rows();
        
        if ($inUse > 0) {
            echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar. El centro de costo está en uso en ' . $inUse . ' asiento(s).']);
            return;
        }
        
        // Eliminar
        $this->db->where('id', $id);
        $result = $this->db->delete('tb_centro_costo');
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Centro de costo eliminado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']);
        }
    }

    // Estado de Situación Financiera (nuevo formato mejorado)
    public function situacion_financiera()
    {
        $this->load->model('Core_model');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        
        $data = array(
            'titulo' => 'Estado de Situación Financiera',
            'subtitulo' => 'Balance General',
            'icono' => 'fas fa-balance-scale',
            'scripts' => array('js/contabilidad_situacion_financiera.js'),
            'empresa' => $empresa
        );
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/situacion_financiera', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: Obtener datos mensuales
    public function situacion_financiera_mensual()
    {
        $mes = $this->input->get('mes'); // format: YYYY-MM
        if (!$mes) {
            echo json_encode(['status' => 'error', 'message' => 'Mes requerido']);
            return;
        }
        
        // Calcular último día del mes
        $ultimo_dia = date('Y-m-t', strtotime($mes . '-01'));
        
        $this->load->model('Contabilidad_model');
        $data = $this->Contabilidad_model->get_situacion_financiera_mensual($ultimo_dia);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $data, 'mes' => $mes, 'fecha' => $ultimo_dia]);
    }

    // AJAX: Obtener datos anuales (consolidado de todos los meses)
    public function situacion_financiera_anual()
    {
        $anio = $this->input->get('anio'); // format: YYYY
        if (!$anio) {
            echo json_encode(['status' => 'error', 'message' => 'Año requerido']);
            return;
        }
        
        $this->load->model('Contabilidad_model');
        $data = $this->Contabilidad_model->get_situacion_financiera_anual($anio);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $data, 'anio' => $anio]);
    }

    // Export mensual a Excel
    public function situacion_financiera_export_mensual()
    {
        if (ob_get_length()) ob_end_clean();
        require_once APPPATH . '../vendor/autoload.php';
        
        $mes = $this->input->get('mes');
        if (!$mes) {
            show_error('Mes requerido', 400);
            return;
        }
        
        $ultimo_dia = date('Y-m-t', strtotime($mes . '-01'));
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $data = $this->Contabilidad_model->get_situacion_financiera_mensual($ultimo_dia);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Page setup for single page
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
        
        // Set margins
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(18);
        
        $row = 1;
        
        // Header - Company Name (centered, bold, larger)
        if ($empresa && !empty($empresa->razon_social)) {
            $sheet->setCellValue('A' . $row, strtoupper($empresa->razon_social));
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $row++;
        }
        
        // Title (centered, bold)
        $sheet->setCellValue('A' . $row, 'Estado de Situación Financiera');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $row++;
        
        // Date (centered)
        $fecha_texto = 'Al ' . date('d', strtotime($ultimo_dia)) . ' de ' . 
                       $this->_get_mes_espanol(date('n', strtotime($ultimo_dia))) . ' de ' . 
                       date('Y', strtotime($ultimo_dia));
        $sheet->setCellValue('A' . $row, $fecha_texto);
        $sheet->getStyle('A' . $row)->getFont()->setSize(9);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $row++;
        $row++; // empty row
        
        // Column headers
        $sheet->setCellValue('A' . $row, 'Cuenta');
        $sheet->setCellValue('B' . $row, 'Monto');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getBottom()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $row++;
        
        $dataStartRow = $row;
        
        // ACTIVO
        $this->_add_situacion_section_compact($sheet, $row, 'ACTIVO', $data['activo']);
        
        // Total Activo
        $sheet->setCellValue('A' . $row, 'Total Activo');
        $sheet->setCellValue('B' . $row, $data['total_activo']);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getTop()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $row++;
        $row++; // empty row
        
        // PASIVO Y PATRIMONIO
        $sheet->setCellValue('A' . $row, 'PASIVO Y PATRIMONIO');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(9);
        $row++;
        
        // PASIVO
        $this->_add_situacion_section_compact($sheet, $row, 'PASIVO', $data['pasivo']);
        
        // Total Pasivo
        $sheet->setCellValue('A' . $row, 'Total Pasivo');
        $sheet->setCellValue('B' . $row, $data['total_pasivo']);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getTop()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $row++;
        $row++; // empty row
        
        // PATRIMONIO
        $this->_add_situacion_section_compact($sheet, $row, 'PATRIMONIO', $data['patrimonio']);
        
        // Total Patrimonio
        $sheet->setCellValue('A' . $row, 'Total Patrimonio');
        $sheet->setCellValue('B' . $row, $data['total_patrimonio']);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getTop()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $row++;
        
        // Total Pasivo y Patrimonio
        $sheet->setCellValue('A' . $row, 'Total Pasivo y Patrimonio');
        $sheet->setCellValue('B' . $row, $data['total_pasivo'] + $data['total_patrimonio']);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getTop()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getBottom()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
        
        $lastDataRow = $row;
        $row++;
        $row++;
        $row++;
        
        // Signature lines
        $sheet->setCellValue('A' . $row, '_________________________');
        $sheet->setCellValue('B' . $row, '_________________________');
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Contador General');
        $sheet->setCellValue('B' . $row, 'Gerente General');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Apply font size to all data cells
        $sheet->getStyle('A' . $dataStartRow . ':B' . $lastDataRow)->getFont()->setSize(8);
        
        // Generate
        $filename = 'Estado_Situacion_Financiera_' . date('d_m_Y', strtotime($ultimo_dia)) . '.xlsx';
        if (ob_get_contents()) ob_end_clean();
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    // Helper para agregar sección compacta
    private function _add_situacion_section_compact(&$sheet, &$row, $title, $items)
    {
        $sheet->setCellValue('A' . $row, $title);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(9);
        $row++;
        
        // Detect if $items is grouped (has 'label' and 'total') or flat (list of cuentas)
        if (!empty($items) && isset($items[0]) && is_array($items[0]) && array_key_exists('label', $items[0]) && array_key_exists('total', $items[0])) {
            // grouped: write only group label and total (one line per agrupación)
            foreach ($items as $group) {
                $sheet->setCellValue('A' . $row, $group['label']);
                $sheet->setCellValue('B' . $row, $group['total']);
                $sheet->getStyle('A' . $row)->getFont()->setSize(9);
                $sheet->getStyle('A' . $row)->getFont()->setBold(false);
                $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                // dotted bottom border to mimic printed layout
                $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED);
                $row++;
            }
        } else {
            // flat: fallback to previous behavior (list of accounts)
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $item['nombre']);
                $sheet->setCellValue('B' . $row, $item['saldo']);
                $sheet->getStyle('A' . $row)->getFont()->setSize(8);
                $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $row++;
            }
        }
    }

    // Helper para obtener nombre de mes en español
    private function _get_mes_espanol($num_mes)
    {
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        return isset($meses[$num_mes]) ? $meses[$num_mes] : '';
    }

    // Export anual a Excel (consolidado)
    public function situacion_financiera_export_anual()
    {
        if (ob_get_length()) ob_end_clean();
        require_once APPPATH . '../vendor/autoload.php';
        
        $anio = $this->input->get('anio');
        if (!$anio) {
            show_error('Año requerido', 400);
            return;
        }
        
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $data = $this->Contabilidad_model->get_situacion_financiera_anual($anio);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Similar structure but with 12 months columns
        $row = 1;
        if ($empresa && !empty($empresa->razon_social)) {
            $sheet->setCellValue('A' . $row, $empresa->razon_social);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $row++;
        }
        
        $sheet->setCellValue('A' . $row, 'Estado de Situación Financiera - Consolidado Anual');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Año ' . $anio);
        $row++;
        $row++; // empty row
        
        // Headers with months
        $sheet->setCellValue('A' . $row, 'Cuenta');
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $col = 'B';
        foreach ($meses as $mes) {
            $sheet->setCellValue($col . $row, $mes);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($col)->setWidth(12);
            $col++;
        }
        $sheet->getColumnDimension('A')->setWidth(35);
        $headerRow = $row;
        $row++;
        
        // Data per account across months
        foreach ($data['cuentas'] as $cuenta) {
            $sheet->setCellValue('A' . $row, $cuenta['nombre']);
            $col = 'B';
            for ($m = 1; $m <= 12; $m++) {
                $valor = isset($cuenta['meses'][$m]) ? $cuenta['meses'][$m] : 0;
                $sheet->setCellValue($col . $row, $valor);
                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $col++;
            }
            $row++;
        }
        
        // Borders
        $lastRow = $row - 1;
        $sheet->getStyle('A' . $headerRow . ':M' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Generate
        $filename = 'Estado_Situacion_Financiera_Anual_' . $anio . '.xlsx';
        if (ob_get_contents()) ob_end_clean();
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    // Estados Financieros: Estado de Resultados
    public function resultados()
    {
        $data = array(
            'titulo' => 'Estado de Resultados',
            'subtitulo' => 'Ingresos y gastos',
            'icono' => 'fas fa-chart-line',
            'scripts' => array('js/contabilidad_resultados.js')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/resultados', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: obtener estado de resultados para el periodo
    public function resultados_data()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        header('Content-Type: application/json');
        $this->load->model('Contabilidad_model');
        // Return the structured estado de resultados so the view can render by agrupaciones
        $data = $this->Contabilidad_model->get_estado_resultados_estructurado($start, $end);

        // Ensure expected grouping lines exist (even when monto == 0) and preserve order like the Excel/PDF template
        $expected = [
            'ingresos_financieros' => [
                'Disponibilidades',
                'Inversiones negociables y a vencimiento',
                'Utilidad en venta de inversiones en valores',
                'Cartera de créditos',
                'Diferencia Cambiaria',
                'Otros Ingresos Financieros'
            ],
            'gastos_financieros' => [
                'Obligaciones financieras',
                'Obligaciones con instituciones financieras y otros financiamientos',
                'Pérdida en venta de inversiones en valores',
                'Deuda subordinada y obligaciones convertibles en acciones',
                'Diferencia Cambiaria',
                'Otros gastos financieros'
            ],
            'provisiones' => [
                'Gasto por provisión por incobrabilidad',
                'DISMINUCION DE PROVISION PARA CARTERA DE CREDITOS',
                'Ingresos por recuperación de la cartera de creditos directa saneada',
                'Gastos por deterioro de inversiones',
                'Gasto por saneamiento de ingresos financieros'
            ],
            'ingresos_operativos' => [ 'Ingresos operativos diversos' ],
            'gastos_operativos' => [ 'Gastos operativos diversos' ],
            'gastos_administracion' => [ 'Gastos de administración y otros', 'Gastos con personas vinculadas' ],
            'impuesto_renta' => [ 'Impuesto a la renta' ],
            'participacion_asociadas' => [ 'Participación en resultados de asociadas' ]
        ];

        foreach ($expected as $section => $names) {
            if (!isset($data[$section]) || !is_array($data[$section])) $data[$section] = [];

            // Map existing by lowercase nombre => item
            $map = [];
            foreach ($data[$section] as $it) {
                $k = mb_strtolower(trim($it['nombre'] ?? ''));
                $map[$k] = $it;
            }

            $ordered = [];
            foreach ($names as $n) {
                $k = mb_strtolower(trim($n));
                if (isset($map[$k])) {
                    $ordered[] = $map[$k];
                    unset($map[$k]);
                } else {
                    $ordered[] = ['nombre' => $n, 'monto' => 0];
                }
            }

            // Append any remaining (unexpected) items after the known ones
            foreach ($map as $rem) $ordered[] = $rem;

            $data[$section] = $ordered;

            // Recompute totals for this section if key exists in data
            $totalKey = 'total_' . $section;
            if (array_key_exists($totalKey, $data)) {
                $sum = 0;
                foreach ($data[$section] as $it) { $sum += floatval($it['monto'] ?? 0); }
                $data[$totalKey] = $sum;
            }
        }

        echo json_encode(['status' => 'success', 'data' => $data, 'start' => $start, 'end' => $end]);
    }

    // Export resultados as CSV
    public function resultados_export()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $ac = $this->input->get('acumulado');
        $this->load->model('Contabilidad_model');

        header('Content-Type: text/csv; charset=UTF-8');
        $fname = 'estado_resultados_' . ($start ?: 'all') . '_' . ($end ?: 'all') . '.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');
        $out = fopen('php://output', 'w');
        echo "\xEF\xBB\xBF";

        $fmt = function($n){ return number_format($n,2,',','.'); };

        // If acumulado requested, generate dual-column CSV (Mes / Acumulado)
        if ($ac) {
            $start_ac = date('Y', strtotime($end)) . '-01-01';
            $d1 = $this->Contabilidad_model->get_estado_resultados_estructurado($start, $end);
            $d2 = $this->Contabilidad_model->get_estado_resultados_estructurado($start_ac, $end);

            fputcsv($out, ['Seccion','Cuenta','Mes','Acumulado']);

            $sections = ['ingresos_financieros' => 'Ingresos financieros por:',
                         'gastos_financieros' => 'Gastos financieros por:',
                         'provisiones' => 'Gasto por provisión e incobrabilidad de la cartera de créditos directa',
                         'ingresos_operativos' => 'Ingresos operativos diversos',
                         'gastos_operativos' => 'Gastos operativos diversos',
                         'gastos_administracion' => 'Gastos de administracion',
            ];

            foreach ($sections as $key => $title) {
                fputcsv($out, [$title,'','','']);
                $items1 = isset($d1[$key]) ? $d1[$key] : [];
                $items2 = isset($d2[$key]) ? $d2[$key] : [];
                $map2 = [];
                foreach ($items2 as $it) { $map2[mb_strtolower(trim($it['nombre'] ?? ''))] = $it; }
                foreach ($items1 as $it) {
                    $name = $it['nombre'];
                    $m1 = floatval($it['monto'] ?? 0);
                    $m2 = isset($map2[mb_strtolower(trim($name))]) ? floatval($map2[mb_strtolower(trim($name))]['monto']) : 0;
                    fputcsv($out, ['', $name, $fmt($m1), $fmt($m2)]);
                }
                $total1 = isset($d1['total_' . $key]) ? $d1['total_' . $key] : 0;
                $total2 = isset($d2['total_' . $key]) ? $d2['total_' . $key] : 0;
                fputcsv($out, ['Total ' . ucfirst(str_replace('_',' ',$key)), '', $fmt($total1), $fmt($total2)]);
                fputcsv($out, []);
            }

            // Resultado operativo bruto
            $res1 = floatval($d1['resultado_operativo_bruto'] ?? 0);
            $res2 = floatval($d2['resultado_operativo_bruto'] ?? 0);
            fputcsv($out, ['Resultado operativo bruto','','' , '']);
            fputcsv($out, ['', 'Resultado operativo bruto', $fmt($res1), $fmt($res2)]);
            fputcsv($out, []);

            // Gastos administracion already included above; then Resultado antes impuesto
            $rat1 = floatval($d1['resultado_antes_impuesto'] ?? 0);
            $rat2 = floatval($d2['resultado_antes_impuesto'] ?? 0);
            fputcsv($out, ['Resultado antes del impuesto a la renta', '', $fmt($rat1), $fmt($rat2)]);

            if (!empty($d1['impuesto_renta']) || !empty($d2['impuesto_renta'])) {
                // align impuesto rows by name from d1
                $items1 = $d1['impuesto_renta'] ?? [];
                $items2 = $d2['impuesto_renta'] ?? [];
                $map2 = [];
                foreach ($items2 as $it) $map2[mb_strtolower(trim($it['nombre'] ?? ''))] = $it;
                foreach ($items1 as $it) {
                    $name = $it['nombre'];
                    $m1 = floatval($it['monto'] ?? 0);
                    $m2 = isset($map2[mb_strtolower(trim($name))]) ? floatval($map2[mb_strtolower(trim($name))]['monto']) : 0;
                    fputcsv($out, ['', $name, $fmt($m1), $fmt($m2)]);
                }
                $tot1 = floatval($d1['total_impuesto'] ?? 0);
                $tot2 = floatval($d2['total_impuesto'] ?? 0);
                fputcsv($out, ['Total Impuesto a la Renta', '', $fmt($tot1), $fmt($tot2)]);
            }

            $re1 = floatval($d1['resultado_ejercicio'] ?? 0);
            $re2 = floatval($d2['resultado_ejercicio'] ?? 0);
            fputcsv($out, ['Resultado del ejercicio', '', $fmt($re1), $fmt($re2)]);

            fclose($out);
            return;
        }

        // default single-month export (existing behavior)
        $d = $this->Contabilidad_model->get_estado_resultados_estructurado($start, $end);
        fputcsv($out, ['Seccion', 'Cuenta', 'Monto']);

        // Ingresos financieros
        fputcsv($out, ['Ingresos financieros por:', '', '']);
        foreach ($d['ingresos_financieros'] as $it) {
            fputcsv($out, ['', $it['nombre'], $fmt($it['monto'])]);
        }
        fputcsv($out, ['Total Ingresos Financieros', '', $fmt($d['total_ingresos_financieros'])]);
        fputcsv($out, []);

        // Gastos financieros
        fputcsv($out, ['Gastos financieros por:', '', '']);
        foreach ($d['gastos_financieros'] as $it) {
            fputcsv($out, ['', $it['nombre'], $fmt($it['monto'])]);
        }
        fputcsv($out, ['Total Gastos Financieros', '', $fmt($d['total_gastos_financieros'])]);
        fputcsv($out, []);

        // Margen financiero bruto
        fputcsv($out, ['Margen Financiero Bruto', '', $fmt($d['margen_financiero_bruto'])]);
        fputcsv($out, []);

        // Provisiones
        fputcsv($out, ['Gasto por provisión e incobrabilidad de la cartera de créditos directa', '', '']);
        foreach ($d['provisiones'] as $it) {
            fputcsv($out, ['', $it['nombre'], $fmt($it['monto'])]);
        }
        fputcsv($out, ['Total Provisiones', '', $fmt($d['total_provisiones'])]);
        fputcsv($out, []);

        // Ingresos/Gastos operativos
        fputcsv($out, ['Ingresos operativos diversos', '', '']);
        foreach ($d['ingresos_operativos'] as $it) fputcsv($out, ['', $it['nombre'], $fmt($it['monto'])]);
        fputcsv($out, []);
        fputcsv($out, ['Gastos operativos diversos', '', '']);
        foreach ($d['gastos_operativos'] as $it) fputcsv($out, ['', $it['nombre'], $fmt($it['monto'])]);
        fputcsv($out, []);

        // Resultado operativo bruto
        fputcsv($out, ['Resultado operativo bruto', '', $fmt($d['resultado_operativo_bruto'])]);
        fputcsv($out, []);

        // Gastos administracion
        fputcsv($out, ['Gastos de administracion', '', '']);
        foreach ($d['gastos_administracion'] as $it) fputcsv($out, ['', $it['nombre'], $fmt($it['monto'])]);
        fputcsv($out, ['Total Gastos Administracion', '', $fmt($d['total_gastos_administracion'])]);
        fputcsv($out, []);

        // Resultado antes impuesto / impuesto / resultado final
        fputcsv($out, ['Resultado antes del impuesto a la renta', '', $fmt($d['resultado_antes_impuesto'])]);
        if (!empty($d['impuesto_renta'])) {
            foreach ($d['impuesto_renta'] as $it) fputcsv($out, ['', $it['nombre'], $fmt($it['monto'])]);
            fputcsv($out, ['Total Impuesto a la Renta', '', $fmt($d['total_impuesto'])]);
        }
        fputcsv($out, ['Resultado del ejercicio', '', $fmt($d['resultado_ejercicio'])]);

        fclose($out);
    }

    // Printable view for resultados
    public function resultados_print()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $data = $this->Contabilidad_model->get_income_statement($start, $end);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $viewData = ['data' => $data, 'empresa' => $empresa, 'start' => $start, 'end' => $end];
        $this->load->view('contabilidad/resultados_print', $viewData);
    }

    // PDF for resultados (simple render via Dompdf)
    public function resultados_pdf()
    {
        if (ob_get_length()) ob_end_clean();
        require_once APPPATH . '../vendor/autoload.php';
        
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $ac = $this->input->get('acumulado');
        
        if (!$start || !$end) {
            show_error('Fechas de inicio y fin requeridas', 400);
            return;
        }
        
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        // If acumulado requested, fetch both month and acumulado ranges
        if ($ac) {
            $start_ac = date('Y', strtotime($end)) . '-01-01';
            $data_cur = $this->Contabilidad_model->get_estado_resultados_estructurado($start, $end);
            $data_acu = $this->Contabilidad_model->get_estado_resultados_estructurado($start_ac, $end);
        } else {
            $data = $this->Contabilidad_model->get_estado_resultados_estructurado($start, $end);
        }
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        // Create Excel with professional format
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // If acumulado -> 3 columns (A desc, B Mes, C Acumulado), else 2 columns
        if ($ac) {
            $sheet->getColumnDimension('A')->setWidth(60);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
        } else {
            $sheet->getColumnDimension('A')->setWidth(60);
            $sheet->getColumnDimension('B')->setWidth(20);
        }

        $row = 1;

        // Header empresa
        if ($empresa && !empty($empresa->razon_social)) {
            $sheet->setCellValue('A' . $row, strtoupper($empresa->razon_social));
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            if ($ac) $sheet->mergeCells('A' . $row . ':C' . $row); else $sheet->mergeCells('A' . $row . ':B' . $row);
            $row++;
        }

        // Title
        $sheet->setCellValue('A' . $row, 'Estado de Situacion Financiera');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        if ($ac) $sheet->mergeCells('A' . $row . ':C' . $row); else $sheet->mergeCells('A' . $row . ':B' . $row);
        $row++;

        // Date range
        $periodo = 'Del ' . date('d/m/Y', strtotime($start)) . ' al ' . date('d/m/Y', strtotime($end));
        $sheet->setCellValue('A' . $row, $periodo);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        if ($ac) $sheet->mergeCells('A' . $row . ':C' . $row); else $sheet->mergeCells('A' . $row . ':B' . $row);
        $row++;
        $row++; // empty row

        // Year column header(s)
        if ($ac) {
            $sheet->setCellValue('B' . $row, 'Mes');
            $sheet->setCellValue('C' . $row, 'Acumulado ' . date('Y', strtotime($end)));
            $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setCellValue('B' . $row, date('Y', strtotime($end)));
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
        $row++;

        // Render sections
        if ($ac) {
            // helper: render dual column section
            $this->_add_seccion_resultados_dual($sheet, $row, 'Ingresos financieros por:', $data_cur['ingresos_financieros'], $data_acu['ingresos_financieros'], true);
            $this->_add_seccion_resultados_dual($sheet, $row, 'Gastos financieros por:', $data_cur['gastos_financieros'], $data_acu['gastos_financieros'], true);

            // Totals and other sections handled inside helper or below
            $sheet->setCellValue('A' . $row, 'Total Ingresos Financieros');
            $sheet->setCellValue('B' . $row, $data_cur['total_ingresos_financieros']);
            $sheet->setCellValue('C' . $row, $data_acu['total_ingresos_financieros']);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            $sheet->setCellValue('A' . $row, 'Total Gastos Financieros');
            $sheet->setCellValue('B' . $row, $data_cur['total_gastos_financieros']);
            $sheet->setCellValue('C' . $row, $data_acu['total_gastos_financieros']);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            // Margen financiero bruto
            $sheet->setCellValue('A' . $row, 'Margen financiero Bruto');
            $sheet->setCellValue('B' . $row, $data_cur['margen_financiero_bruto']);
            $sheet->setCellValue('C' . $row, $data_acu['margen_financiero_bruto']);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;

            $this->_add_seccion_resultados_dual($sheet, $row, 'Gasto por provisión e incobrabilidad de la cartera de créditos directa', $data_cur['provisiones'], $data_acu['provisiones'], false);

            $sheet->setCellValue('A' . $row, 'Margen Financiero Bruto');
            $sheet->setCellValue('B' . $row, $data_cur['margen_financiero_neto']);
            $sheet->setCellValue('C' . $row, $data_acu['margen_financiero_neto']);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            $this->_add_seccion_resultados_dual($sheet, $row, 'Ingresos operativos diversos', $data_cur['ingresos_operativos'], $data_acu['ingresos_operativos'], false);
            $this->_add_seccion_resultados_dual($sheet, $row, 'Gastos operativos diversos', $data_cur['gastos_operativos'], $data_acu['gastos_operativos'], false);

            $sheet->setCellValue('A' . $row, 'Resultado operativo bruto');
            $sheet->setCellValue('B' . $row, $data_cur['resultado_operativo_bruto']);
            $sheet->setCellValue('C' . $row, $data_acu['resultado_operativo_bruto']);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;

            if (!empty($data_cur['participacion_asociadas'])) {
                $this->_add_seccion_resultados_dual($sheet, $row, 'Participación en resultados de asociadas', $data_cur['participacion_asociadas'], $data_acu['participacion_asociadas'] ?? [], false);
            }

            $this->_add_seccion_resultados_dual($sheet, $row, 'Gastos de administración', $data_cur['gastos_administracion'], $data_acu['gastos_administracion'], false);

            $sheet->setCellValue('A' . $row, 'Resultado antes del impuesto a la renta');
            $sheet->setCellValue('B' . $row, $data_cur['resultado_antes_impuesto']);
            $sheet->setCellValue('C' . $row, $data_acu['resultado_antes_impuesto']);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;

            if (!empty($data_cur['impuesto_renta']) || !empty($data_acu['impuesto_renta'])) {
                $this->_add_seccion_resultados_dual($sheet, $row, 'Impuesto a la renta', $data_cur['impuesto_renta'] ?? [], $data_acu['impuesto_renta'] ?? [], false);
            }

            $sheet->setCellValue('A' . $row, 'Resultado del ejercicio');
            $sheet->setCellValue('B' . $row, $data_cur['resultado_ejercicio']);
            $sheet->setCellValue('C' . $row, $data_acu['resultado_ejercicio']);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('D1D5DB');
            $sheet->getStyle('B' . $row . ':C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // No borders per request; align amounts right
            $lastRow = $row;
            $sheet->getStyle('B5:C' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Add signature lines: three columns with top border and labels underneath
            $row += 2; // gap before signatures
            // draw top border for signature line
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, '');
            $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;
            // labels under signature lines
            $sheet->setCellValue('A' . $row, 'Contador General');
            $sheet->setCellValue('B' . $row, 'Gerente General');
            $sheet->setCellValue('C' . $row, 'Administrador');
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        } else {
            // single-month (existing behavior)
            $sheet->getColumnDimension('A')->setWidth(60);
            $sheet->getColumnDimension('B')->setWidth(20);

            $row = 1;

            if ($empresa && !empty($empresa->razon_social)) {
                $sheet->setCellValue('A' . $row, strtoupper($empresa->razon_social));
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('A' . $row . ':B' . $row);
                $row++;
            }

            $sheet->setCellValue('A' . $row, 'Estado de Situacion Financiera');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $row++;

            $periodo = 'Del ' . date('d/m/Y', strtotime($start)) . ' al ' . date('d/m/Y', strtotime($end));
            $sheet->setCellValue('A' . $row, $periodo);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $row++;
            $row++; // empty

            $sheet->setCellValue('B' . $row, date('Y', strtotime($end)));
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row++;

            $this->_add_seccion_resultados($sheet, $row, 'Ingresos financieros por:', $data['ingresos_financieros'], true);
            $this->_add_seccion_resultados($sheet, $row, 'Gastos financieros por:', $data['gastos_financieros'], true);

            // Total Ingresos Financieros
            $sheet->setCellValue('A' . $row, 'Total Ingresos Financieros');
            $sheet->setCellValue('B' . $row, $data['total_ingresos_financieros']);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            // Total Gastos Financieros
            $sheet->setCellValue('A' . $row, 'Total Gastos Financieros');
            $sheet->setCellValue('B' . $row, $data['total_gastos_financieros']);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            // MARGEN FINANCIERO BRUTO
            $sheet->setCellValue('A' . $row, 'Margen financiero Bruto');
            $sheet->setCellValue('B' . $row, $data['margen_financiero_bruto']);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;

            $this->_add_seccion_resultados($sheet, $row, 'Gasto por provisión e incobrabilidad de la cartera de créditos directa', $data['provisiones'], false);

            $sheet->setCellValue('A' . $row, 'Margen Financiero Bruto');
            $sheet->setCellValue('B' . $row, $data['margen_financiero_neto']);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            $this->_add_seccion_resultados($sheet, $row, 'Ingresos operativos diversos', $data['ingresos_operativos'], false);
            $this->_add_seccion_resultados($sheet, $row, 'Gastos operativos diversos', $data['gastos_operativos'], false);

            $sheet->setCellValue('A' . $row, 'Resultado operativo bruto');
            $sheet->setCellValue('B' . $row, $data['resultado_operativo_bruto']);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            if (!empty($data['participacion_asociadas'])) {
                $this->_add_seccion_resultados($sheet, $row, 'Participación en resultados de asociadas', $data['participacion_asociadas'], false);
            }

            $this->_add_seccion_resultados($sheet, $row, 'Gastos de administración', $data['gastos_administracion'], false);

            $sheet->setCellValue('A' . $row, 'Resultado antes del impuesto a la renta');
            $sheet->setCellValue('B' . $row, $data['resultado_antes_impuesto']);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E9ECEF');
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
            $row++;

            if (!empty($data['impuesto_renta'])) {
                $this->_add_seccion_resultados($sheet, $row, 'Impuesto a la renta', $data['impuesto_renta'], false);
            }

            $sheet->setCellValue('A' . $row, 'Resultado del ejercicio');
            $sheet->setCellValue('B' . $row, $data['resultado_ejercicio']);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('D1D5DB');
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            // No borders per request; align amounts right
            $lastRow = $row;
            $sheet->getStyle('B5:B' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Ensure a third column exists for signatures
            $sheet->getColumnDimension('C')->setWidth(20);
            // Add signature lines
            $row += 2;
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, '');
            $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;
            $sheet->setCellValue('A' . $row, 'Contador General');
            $sheet->setCellValue('B' . $row, 'Gerente General');
            $sheet->setCellValue('C' . $row, 'Administrador');
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Generate file
        // Ensure sheet prints in portrait and fits width to one page where possible
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4);
        $sheet->getPageMargins()->setBottom(0.4);
        $sheet->getPageMargins()->setLeft(0.4);
        $sheet->getPageMargins()->setRight(0.4);

        $filename = 'Estado_Resultados_' . date('Ymd', strtotime($start)) . '_' . date('Ymd', strtotime($end)) . '.xlsx';
        if (ob_get_contents()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    // Generate real PDF (Dompdf) for Estado de Resultados, matching Excel layout
    public function resultados_pdf_real()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $ac = $this->input->get('acumulado');

        if (!$start || !$end) {
            show_error('Fechas de inicio y fin requeridas', 400);
            return;
        }

        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));

        if ($ac) {
            $start_ac = date('Y', strtotime($end)) . '-01-01';
            $d_cur = $this->Contabilidad_model->get_estado_resultados_estructurado($start, $end);
            $d_acu = $this->Contabilidad_model->get_estado_resultados_estructurado($start_ac, $end);
        } else {
            $d_cur = $this->Contabilidad_model->get_estado_resultados_estructurado($start, $end);
            $d_acu = null;
        }

        if (!defined('FCPATH')) define('FCPATH', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
        $dompfPath = FCPATH . 'dompdf' . DIRECTORY_SEPARATOR . 'autoload.inc.php';
        if (!file_exists($dompfPath)) { show_error('Dompdf no encontrado', 500); return; }
        require_once $dompfPath;

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);

        // Build HTML similar to Excel layout, minimal borders, small font to fit one page
        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
        $html .= '<style>
            @page { size: A4 portrait; margin: 8mm 6mm; }
            body{ font-family: Arial, Helvetica, sans-serif; font-size:10px; color:#000 }
            .title{ text-align:center; font-weight:700; font-size:14px; }
            .company{ text-align:center; font-weight:700; font-size:16px; }
            .period{ text-align:center; font-size:12px; margin-bottom:6px }
            table{ width:100%; border-collapse:collapse; }
            td, th{ padding:3px 6px; vertical-align:top; }
            .desc{ width:60%; }
            .amt{ width:20%; text-align:right; }
            .amt2{ width:20%; text-align:right; }
            .section{ font-weight:700; }
            .total{ font-weight:700; border-top:1px solid #000; }
            .sigline{ border-top:1px solid #000; height:30px; }
        </style>';
        $html .= '</head><body>';

        $html .= '<div class="company">' . htmlspecialchars(strtoupper($empresa->razon_social ?? '')) . '</div>';
        $html .= '<div class="title">Estado de Situacion Financiera</div>';
        $html .= '<div class="period">Del ' . date('d/m/Y', strtotime($start)) . ' al ' . date('d/m/Y', strtotime($end)) . '</div>';

        $html .= '<table>';

        // Header for amounts
        if ($ac) {
            $html .= '<tr><th class="desc"></th><th class="amt">Mes</th><th class="amt2">Acumulado ' . date('Y', strtotime($end)) . '</th></tr>';
        } else {
            $html .= '<tr><th class="desc"></th><th class="amt">' . date('Y', strtotime($end)) . '</th></tr>';
        }

        // Helper to render arrays
        $renderItems = function($title, $items1, $items2 = null) use (&$html, $ac) {
            $html .= '<tr><td class="section">' . htmlspecialchars($title) . '</td>';
            if ($ac) $html .= '<td class="amt"></td><td class="amt2"></td>'; else $html .= '<td class="amt"></td>';
            $html .= '</tr>';
            if (!empty($items1)) {
                $map2 = [];
                if ($ac && !empty($items2)) {
                    foreach ($items2 as $it) $map2[mb_strtolower(trim($it['nombre'] ?? ''))] = $it;
                }
                foreach ($items1 as $it) {
                    $name = $it['nombre'] ?? '';
                    $m1 = number_format(floatval($it['monto'] ?? 0),2,',','.');
                    $m2 = '';
                    if ($ac) {
                        $k = mb_strtolower(trim($name));
                        $m2 = isset($map2[$k]) ? number_format(floatval($map2[$k]['monto'] ?? 0),2,',','.') : '0,00';
                    }
                    $html .= '<tr><td class="desc">' . htmlspecialchars($name) . '</td><td class="amt">' . $m1 . '</td>';
                    if ($ac) $html .= '<td class="amt2">' . $m2 . '</td>';
                    $html .= '</tr>';
                }
            }
            return;
        };

        // Ingresos financieros
        $renderItems('Ingresos financieros por:', $d_cur['ingresos_financieros'] ?? [], $d_acu['ingresos_financieros'] ?? []);
        // Total Ingresos Financieros
        $t1 = number_format(floatval($d_cur['total_ingresos_financieros'] ?? 0),2,',','.');
        $t2 = $ac ? number_format(floatval($d_acu['total_ingresos_financieros'] ?? 0),2,',','.') : '';
        $html .= '<tr class="total"><td>Total Ingresos Financieros</td><td class="amt">' . $t1 . '</td>' . ($ac ? '<td class="amt2">' . $t2 . '</td>' : '') . '</tr>';

        // Gastos financieros
        $renderItems('Gastos financieros por:', $d_cur['gastos_financieros'] ?? [], $d_acu['gastos_financieros'] ?? []);
        $tg1 = number_format(floatval($d_cur['total_gastos_financieros'] ?? 0),2,',','.');
        $tg2 = $ac ? number_format(floatval($d_acu['total_gastos_financieros'] ?? 0),2,',','.') : '';
        $html .= '<tr class="total"><td>Total Gastos Financieros</td><td class="amt">' . $tg1 . '</td>' . ($ac ? '<td class="amt2">' . $tg2 . '</td>' : '') . '</tr>';

        // Margen
        $m1 = number_format(floatval($d_cur['margen_financiero_bruto'] ?? 0),2,',','.');
        $m2 = $ac ? number_format(floatval($d_acu['margen_financiero_bruto'] ?? 0),2,',','.') : '';
        $html .= '<tr class="total"><td>Margen Financiero Bruto</td><td class="amt">' . $m1 . '</td>' . ($ac ? '<td class="amt2">' . $m2 . '</td>' : '') . '</tr>';

        // Provisiones
        $renderItems('Gasto por provisión e incobrabilidad de la cartera de créditos directa', $d_cur['provisiones'] ?? [], $d_acu['provisiones'] ?? []);
        $p1 = number_format(floatval($d_cur['total_provisiones'] ?? 0),2,',','.');
        $p2 = $ac ? number_format(floatval($d_acu['total_provisiones'] ?? 0),2,',','.') : '';
        $html .= '<tr class="total"><td>Total Provisiones</td><td class="amt">' . $p1 . '</td>' . ($ac ? '<td class="amt2">' . $p2 . '</td>' : '') . '</tr>';

        // Operativos
        $renderItems('Ingresos operativos diversos', $d_cur['ingresos_operativos'] ?? [], $d_acu['ingresos_operativos'] ?? []);
        $renderItems('Gastos operativos diversos', $d_cur['gastos_operativos'] ?? [], $d_acu['gastos_operativos'] ?? []);

        // Resultado operativo bruto
        $ro1 = number_format(floatval($d_cur['resultado_operativo_bruto'] ?? 0),2,',','.');
        $ro2 = $ac ? number_format(floatval($d_acu['resultado_operativo_bruto'] ?? 0),2,',','.') : '';
        $html .= '<tr class="total"><td>Resultado operativo bruto</td><td class="amt">' . $ro1 . '</td>' . ($ac ? '<td class="amt2">' . $ro2 . '</td>' : '') . '</tr>';

        // Gastos administracion
        $renderItems('Gastos de administracion', $d_cur['gastos_administracion'] ?? [], $d_acu['gastos_administracion'] ?? []);

        // Resultado antes impuesto
        $rat1 = number_format(floatval($d_cur['resultado_antes_impuesto'] ?? 0),2,',','.');
        $rat2 = $ac ? number_format(floatval($d_acu['resultado_antes_impuesto'] ?? 0),2,',','.') : '';
        $html .= '<tr class="total"><td>Resultado antes del impuesto a la renta</td><td class="amt">' . $rat1 . '</td>' . ($ac ? '<td class="amt2">' . $rat2 . '</td>' : '') . '</tr>';

        // Impuesto a la renta
        if (!empty($d_cur['impuesto_renta']) || !empty($d_acu['impuesto_renta'])) {
            $renderItems('Impuesto a la renta', $d_cur['impuesto_renta'] ?? [], $d_acu['impuesto_renta'] ?? []);
            $ti1 = number_format(floatval($d_cur['total_impuesto'] ?? 0),2,',','.');
            $ti2 = $ac ? number_format(floatval($d_acu['total_impuesto'] ?? 0),2,',','.') : '';
            $html .= '<tr class="total"><td>Total Impuesto a la Renta</td><td class="amt">' . $ti1 . '</td>' . ($ac ? '<td class="amt2">' . $ti2 . '</td>' : '') . '</tr>';
        }

        // Resultado del ejercicio
        $re1 = number_format(floatval($d_cur['resultado_ejercicio'] ?? 0),2,',','.');
        $re2 = $ac ? number_format(floatval($d_acu['resultado_ejercicio'] ?? 0),2,',','.') : '';
        $html .= '<tr class="total"><td>Resultado del ejercicio</td><td class="amt">' . $re1 . '</td>' . ($ac ? '<td class="amt2">' . $re2 . '</td>' : '') . '</tr>';

        $html .= '</table>';

        // Signatures: three columns centered
        $html .= '<br/><br/>';
        $html .= '<table style="width:100%; margin-top:12px;"><tr><td style="width:33%; text-align:center;">&nbsp;</td><td style="width:33%; text-align:center;">&nbsp;</td><td style="width:33%; text-align:center;">&nbsp;</td></tr>';
        $html .= '<tr><td style="text-align:center; padding-top:20px; border-top:1px solid #000;">Contador General</td><td style="text-align:center; padding-top:20px; border-top:1px solid #000;">Gerente General</td><td style="text-align:center; padding-top:20px; border-top:1px solid #000;">Administrador</td></tr></table>';

        $html .= '</body></html>';

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdf = $dompdf->output();

        $filename = 'Estado_Resultados_' . date('Ymd', strtotime($start)) . '_' . date('Ymd', strtotime($end)) . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $pdf;
        exit;
    }

    // Helper para agregar sección al Estado de Resultados
    private function _add_seccion_resultados(&$sheet, &$row, $titulo, $items, $es_titulo_principal = false)
    {
        if ($es_titulo_principal) {
            $sheet->setCellValue('A' . $row, $titulo);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getFont()->setUnderline(true);
            $row++;
        } else {
            $sheet->setCellValue('A' . $row, $titulo);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }
        
        if (!empty($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $sheet->setCellValue('A' . $row, '    ' . $item['nombre']);
                    // write formatted string to enforce thousands '.' and decimal ','
                    $fmt = number_format($item['monto'],2,',','.');
                    $sheet->setCellValue('B' . $row, $fmt);
                    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $row++;
                }
            }
        }
        $row++; // empty row after section
    }

    // Helper para sección dual (Mes / Acumulado)
    private function _add_seccion_resultados_dual(&$sheet, &$row, $titulo, $items_cur, $items_acu, $es_titulo_principal = false)
    {
        if ($es_titulo_principal) {
            $sheet->setCellValue('A' . $row, $titulo);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getFont()->setUnderline(true);
            $row++;
        } else {
            $sheet->setCellValue('A' . $row, $titulo);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }

        $mapA = [];
        if (!empty($items_acu)) {
            foreach ($items_acu as $it) {
                $mapA[mb_strtolower(trim($it['nombre'] ?? ''))] = $it;
            }
        }

        if (!empty($items_cur)) {
            foreach ($items_cur as $item) {
                if (is_array($item)) {
                    $name = $item['nombre'];
                    $mcur = number_format(floatval($item['monto'] ?? 0),2,',','.');
                    $maux = 0;
                    $key = mb_strtolower(trim($name));
                    if (isset($mapA[$key])) $maux = number_format(floatval($mapA[$key]['monto'] ?? 0),2,',','.');

                    $sheet->setCellValue('A' . $row, '    ' . $name);
                    $sheet->setCellValue('B' . $row, $mcur);
                    $sheet->setCellValue('C' . $row, $maux);
                    $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $row++;
                }
            }
        }

        $row++; // gap after section
    }

    // Estados Financieros: Flujo de Efectivo
    public function flujo()
    {
        $data = array(
            'titulo' => 'Flujo de Efectivo',
            'subtitulo' => 'Flujos de caja',
            'icono' => 'fas fa-chart-area',
            'scripts' => array('js/contabilidad_flujo.js')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/flujo', $data);
        $this->load->view('layout/footer');
    }

    // AJAX: obtener flujo de efectivo
    public function flujo_data()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        header('Content-Type: application/json');
        $this->load->model('Contabilidad_model');
        $data = $this->Contabilidad_model->get_cash_flow($start, $end);
        echo json_encode(['status' => 'success', 'data' => $data, 'start' => $start, 'end' => $end]);
    }

    // Export flujo as CSV
    public function flujo_export()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $this->load->model('Contabilidad_model');
        $d = $this->Contabilidad_model->get_cash_flow($start, $end);
        header('Content-Type: text/csv');
        $fname = 'flujo_efectivo_' . ($start ?: 'all') . '_' . ($end ?: 'all') . '.csv';
        header('Content-Disposition: attachment; filename="' . $fname . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['date','journal_id','description','category','amount']);
        foreach ($d['rows'] as $r) {
            fputcsv($out, [$r['date'], $r['journal_id'], $r['description'], $r['category'], number_format($r['amount'],2,'.','')]);
        }
        fputcsv($out, []);
        fputcsv($out, ['TOTALS','', '', 'colecciones_creditos', number_format($d['totals']['colecciones_creditos'] ?? 0,2,'.','')]);
        fputcsv($out, ['TOTALS','', '', 'intereses_comisiones', number_format($d['totals']['intereses_comisiones'] ?? 0,2,'.','')]);
        fputcsv($out, ['TOTALS','', '', 'desembolsos_creditos', number_format($d['totals']['desembolsos_creditos'] ?? 0,2,'.','')]);
        fputcsv($out, ['TOTALS','', '', 'pagos_operativos', number_format($d['totals']['pagos_operativos'] ?? 0,2,'.','')]);
        fputcsv($out, ['TOTALS','', '', 'financiacion', number_format($d['totals']['financiacion'] ?? 0,2,'.','')]);
        fputcsv($out, ['TOTALS','', '', 'neto', number_format($d['totals']['neto'] ?? 0,2,'.','')]);
        fclose($out);
    }

    // Printable view for flujo (used by worker/pdf)
    public function flujo_print()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $data = $this->Contabilidad_model->get_cash_flow($start, $end);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));
        $viewData = ['data' => $data, 'empresa' => $empresa, 'start' => $start, 'end' => $end];
        $this->load->view('contabilidad/flujo_print', $viewData);
    }

    // Generate PDF for flujo using Dompdf with double-pass hash embed
    public function flujo_pdf()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $this->load->model('Contabilidad_model');
        $this->load->model('Core_model');
        $data = $this->Contabilidad_model->get_cash_flow($start, $end);
        $empresa = $this->core_model->get_by_id('tb_sistema', array('id' => 1));

        $exported_by = null;
        if ($this->load->is_loaded('ion_auth') || isset($this->ion_auth)) {
            try { $u = $this->ion_auth->user()->row(); $exported_by = isset($u->username) ? $u->username : null; } catch(Exception $e) { $exported_by = null; }
        }
        $exported_at = date('Y-m-d H:i:s');

        $html = $this->load->view('contabilidad/flujo_pdf', ['data' => $data, 'empresa' => $empresa, 'start' => $start, 'end' => $end, 'exported_by' => $exported_by, 'exported_at' => $exported_at], true);

        if (!defined('FCPATH')) define('FCPATH', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
        $dompfPath = FCPATH . 'dompdf' . DIRECTORY_SEPARATOR . 'autoload.inc.php';
        if (!file_exists($dompfPath)) { show_error('Dompdf no encontrado', 500); return; }
        require_once $dompfPath;

        $options = new \Dompdf\Options(); $options->set('isHtml5ParserEnabled', true);
        // first pass
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4','portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $firstPdf = $dompdf->output();
        $hash = md5($firstPdf);

        $htmlWithHash = str_replace('{hash}', $hash, $html);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4','portrait');
        $dompdf->loadHtml($htmlWithHash);
        $dompdf->render();
        $finalPdf = $dompdf->output();

        $filename = 'flujo_efectivo_' . ($start ?: 'all') . '_' . ($end ?: 'all') . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($finalPdf));
        header('X-Report-Hash: ' . $hash);
        echo $finalPdf;
    }

    // Create a background job for flujo PDF (returns job/status url)
    public function flujo_pdf_job()
    {
        $start = $this->input->get('start_date');
        $end = $this->input->get('end_date');
        $this->ensure_reports_table_exists();

        $query = [];
        if ($start) $query['start_date'] = $start;
        if ($end) $query['end_date'] = $end;
        $printUrl = site_url('contabilidad/flujo_print') . '?' . http_build_query($query);

        $jobId = uniqid('flujo_', true);
        $reportsDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
        if (!is_dir($reportsDir)) mkdir($reportsDir, 0755, true);
        $outFile = 'uploads/reports/' . $jobId . '.pdf';

        $created_by = null;
        if (isset($this->ion_auth) && $this->ion_auth->logged_in()) { $u = $this->ion_auth->user()->row(); $created_by = isset($u->username) ? $u->username : null; }

        $insert = [
            'job_id' => $jobId,
            'type' => 'flujo_pdf',
            'print_url' => $printUrl,
            'file_path' => $outFile,
            'status' => 'pending',
            'created_by' => $created_by,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tb_reports', $insert);

        $phpCli = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
        $workerScript = FCPATH . 'scripts' . DIRECTORY_SEPARATOR . 'pdf_worker.php';
        $cmd = '"' . $phpCli . '" "' . $workerScript . '" ' . escapeshellarg($jobId) . ' ' . escapeshellarg($printUrl) . ' ' . escapeshellarg(FCPATH . $outFile);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen('start /B "" ' . $cmd, 'r'));
        } else {
            exec($cmd . ' > /dev/null 2>&1 &');
        }

        $statusUrl = site_url('contabilidad/balanza_pdf_status_db') . '?job=' . urlencode($jobId);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'accepted', 'job_id' => $jobId, 'status_url' => $statusUrl, 'download_url' => base_url($outFile)]);
    }

    // AJAX: crear asiento contable (simplificado)
    public function add_entry()
    {
        $post = $this->input->post();
        // Normalizar líneas: CI normalmente agrupa lines as array when named lines[0][...]
        $lines = isset($post['lines']) ? $post['lines'] : [];
        // calcular totales
        $total_debit = 0.0; $total_credit = 0.0;
        $errors = [];
        if (!is_array($lines) || count($lines) == 0) {
            $errors[] = 'Debe agregar al menos una línea al asiento.';
        } else {
            foreach ($lines as $i => $ln) {
                $acc = isset($ln['account_id']) ? intval($ln['account_id']) : 0;
                $debit = isset($ln['debit']) && $ln['debit'] !== '' ? floatval(str_replace(',','.', $ln['debit'])) : 0.0;
                $credit = isset($ln['credit']) && $ln['credit'] !== '' ? floatval(str_replace(',','.', $ln['credit'])) : 0.0;
                if ($acc <= 0) $errors[] = "Línea " . ($i+1) . ": cuenta inválida.";
                if ($debit < 0 || $credit < 0) $errors[] = "Línea " . ($i+1) . ": valores negativos no permitidos.";
                $total_debit += $debit;
                $total_credit += $credit;
            }
        }

        header('Content-Type: application/json');
        // Prevent creating entries in a closed period (admins may override)
        $date = isset($post['date']) ? $post['date'] : null;
        if ($date && $this->Contabilidad_model->is_period_closed($date)) {
            $isAdmin = (method_exists($this->ion_auth, 'is_admin') && $this->ion_auth->logged_in() && $this->ion_auth->is_admin());
            if (! $isAdmin) {
                echo json_encode(['status'=>'error','errors'=>['Periodo cerrado: no puede crear asientos en este mes']]);
                return;
            }
        }
        if (!empty($errors)) {
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // obligar que cuadre
        if (round($total_debit,2) !== round($total_credit,2)) {
            echo json_encode(['status' => 'error', 'errors' => ['El asiento no cuadra: total debe (' . number_format($total_debit,2) . ') <> total haber (' . number_format($total_credit,2) . ')']]);
            return;
        }

        // validar que las cuentas existen
        foreach ($lines as $i => $ln) {
            $acc = isset($ln['account_id']) ? intval($ln['account_id']) : 0;
            if ($acc > 0) {
                $a = $this->Contabilidad_model->get_account($acc);
                if (!$a) {
                    echo json_encode(['status' => 'error', 'errors' => ["Línea " . ($i+1) . ": la cuenta no existe."]]);
                    return;
                }
            }
        }

        // preparar payload y guardar
        $payload = [
            'date' => isset($post['date']) ? $post['date'] : date('Y-m-d'),
            'description' => isset($post['description']) ? $post['description'] : '',
            'lines' => $lines,
            'source_type' => isset($post['source_type']) ? $post['source_type'] : null,
            'source_id' => isset($post['source_id']) ? intval($post['source_id']) : null,
        ];

        $result = $this->Contabilidad_model->create_journal($payload);
        if ($result) {
            // Si es asiento de tesorería, marcar movimiento como contabilizado
            if (isset($payload['source_type']) && $payload['source_type'] === 'teso_movimiento' && isset($payload['source_id'])) {
                $this->db->where('id', intval($payload['source_id']))->update('teso_movimientos', ['contabilizado' => 1]);
            }
            echo json_encode(['status' => 'success', 'journal_id' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'errors' => ['Error al guardar el asiento.']]);
        }
    }

    // ============================================
    // IMPORTACIÓN DE BALANZA DE COMPROBACIÓN
    // ============================================
    
    /**
     * Página de importación de balanza de comprobación
     */
    public function importar_balanza()
    {
        $data = array(
            'titulo' => 'Importar Balanza de Comprobación',
            'subtitulo' => 'Carga inicial de cuentas y saldos',
            'icono' => 'fas fa-file-upload',
            'scripts' => array()
        );
        
        $this->load->view('layout/header', $data);
        $this->load->view('contabilidad/importar_balanza', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Procesar archivo de balanza y mostrar vista previa
     */
    public function procesar_balanza()
    {
        // Limpiar buffers y forzar JSON
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        try {
            // Log de debug
            log_message('debug', 'procesar_balanza - Inicio');
            log_message('debug', 'FILES: ' . print_r($_FILES, true));
            log_message('debug', 'POST: ' . print_r($_POST, true));
            
            // Verificar si se subió archivo
            if (!isset($_FILES['balanzaFile'])) {
                echo json_encode(['status' => 'error', 'message' => 'No se recibió el campo balanzaFile']);
                exit();
            }
        
            $upload_error = $_FILES['balanzaFile']['error'];
            
            // Mensajes de error detallados
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede upload_max_filesize en php.ini',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede MAX_FILE_SIZE del formulario HTML',
                UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
                    UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
                UPLOAD_ERR_CANT_WRITE => 'Fallo al escribir el archivo al disco',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida'
            ];
            
            if ($upload_error !== UPLOAD_ERR_OK) {
                $error_msg = isset($upload_errors[$upload_error]) ? 
                    $upload_errors[$upload_error] : 
                    'Error desconocido al subir archivo (código: ' . $upload_error . ')';
                
                log_message('error', 'Error de upload: ' . $error_msg);
                echo json_encode(['status' => 'error', 'message' => $error_msg]);
                exit();
            }

            $file = $_FILES['balanzaFile'];
            
            // Validar que el archivo existe y es legible
            if (!file_exists($file['tmp_name']) || !is_readable($file['tmp_name'])) {
                echo json_encode(['status' => 'error', 'message' => 'El archivo temporal no es accesible']);
                exit();
            }
            
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            log_message('debug', 'Extensión detectada: ' . $extension);
            
            // Capturar información del período
            $periodo_mes = $this->input->post('periodoMes');
            $periodo_anio = $this->input->post('periodoAnio');
            $tipo_importacion = $this->input->post('tipoImportacion');
            
            log_message('debug', 'Período: ' . $periodo_mes . '/' . $periodo_anio);
            
            // Validar período
            if (!$periodo_mes || !$periodo_anio) {
                echo json_encode(['status' => 'error', 'message' => 'Debe especificar el período (mes y año)']);
                exit();
            }
            
            // Cargar librería PhpSpreadsheet si está disponible, sino parsear CSV básico
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Intentar cargar con PhpSpreadsheet
                if (file_exists(APPPATH . '../vendor/autoload.php')) {
                    require_once APPPATH . '../vendor/autoload.php';
                    $cuentas = $this->_parsear_excel($file['tmp_name']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'PhpSpreadsheet no está instalado. Use formato CSV.']);
                    exit();
                }
            } else if ($extension === 'csv') {
                $cuentas = $this->_parsear_csv($file['tmp_name']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Formato de archivo no soportado']);
                exit();
            }

            if (empty($cuentas)) {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos en el archivo']);
                exit();
            }

            // Procesar y clasificar cuentas
            $resultado = $this->_procesar_cuentas_balanza($cuentas);
            
            // Agregar información del período
            $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                      '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
            $tipos = ['apertura'=>'Asiento de Apertura','cierre'=>'Cierre Mensual','ajuste'=>'Ajuste de Saldos'];
            
            $resultado['periodo'] = [
                'mes' => $periodo_mes,
                'anio' => $periodo_anio,
                'mes_nombre' => $meses[$periodo_mes] ?? 'N/A',
                'tipo' => $tipo_importacion,
                'tipo_nombre' => $tipos[$tipo_importacion] ?? 'N/A'
            ];
            
            echo json_encode([
                'status' => 'success',
                'data' => $resultado
            ]);
            exit();
            
        } catch (Exception $e) {
            log_message('error', 'Exception en procesar_balanza: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error al procesar: ' . $e->getMessage()]);
            exit();
        }
    }

    /**
     * Confirmar e importar la balanza (crear cuentas y asiento de apertura)
     */
    public function importar_balanza_confirmar()
    {
        header('Content-Type: application/json');
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data || !isset($data['cuentas']) || !isset($data['asiento'])) {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
            return;
        }
        
        $cuentas = $data['cuentas'];
        $asiento_info = $data['asiento'];
        
        // Información del período
        $periodo = isset($data['periodo']) ? $data['periodo'] : [];
        $periodo_mes = isset($periodo['mes']) ? $periodo['mes'] : date('m');
        $periodo_anio = isset($periodo['anio']) ? $periodo['anio'] : date('Y');
        
        $this->db->trans_start();
        
        try {
            $cuentas_creadas = 0;
            $cuentas_actualizadas = 0;
            $errores = [];
            $lineas_asiento = [];
            
            // 1. Crear/actualizar cuentas
            foreach ($cuentas as $cuenta) {
                $code = trim($cuenta['code']);
                $name = trim($cuenta['name']);
                $type = strtolower(trim($cuenta['type']));
                $saldo = floatval($cuenta['saldo_actual']);
                
                // Verificar si la cuenta ya existe
                $existe = $this->Contabilidad_model->get_account_by_code($code);
                
                if ($existe) {
                    // Actualizar nombre si es diferente
                    if ($existe->name !== $name) {
                        $this->Contabilidad_model->update_account($existe->id, [
                            'name' => $name,
                            'type' => $type
                        ]);
                    }
                    $account_id = $existe->id;
                    $cuentas_actualizadas++;
                } else {
                    // Crear nueva cuenta
                    $account_id = $this->Contabilidad_model->create_account([
                        'code' => $code,
                        'name' => $name,
                        'type' => $type,
                        'parent_id' => null
                    ]);
                    
                    if (!$account_id) {
                        $errores[] = "Error al crear cuenta: $code - $name";
                        continue;
                    }
                    $cuentas_creadas++;
                }
                
                // 2. Preparar líneas del asiento (solo si tiene saldo)
                if (abs($saldo) > 0.001) {
                    // Determinar si va al debe o al haber según el tipo de cuenta
                    $debe = 0;
                    $haber = 0;
                    
                    if (in_array($type, ['activo', 'gasto'])) {
                        // Activos y gastos van al DEBE si son positivos
                        if ($saldo > 0) {
                            $debe = abs($saldo);
                        } else {
                            $haber = abs($saldo);
                        }
                    } else {
                        // Pasivo, patrimonio e ingresos van al HABER si son positivos
                        if ($saldo > 0) {
                            $haber = abs($saldo);
                        } else {
                            $debe = abs($saldo);
                        }
                    }
                    
                    $lineas_asiento[] = [
                        'account_id' => $account_id,
                        'debit' => $debe,
                        'credit' => $haber,
                        'description' => 'Saldo ' . ucfirst($periodo_mes) . '/' . $periodo_anio . ' - ' . $name
                    ];
                }
            }
            
            // 3. Crear asiento (SIEMPRE se crea uno nuevo, aunque existan otros)
            $asiento_id = null;
            if (!empty($lineas_asiento)) {
                $payload_asiento = [
                    'date' => $asiento_info['fecha'],
                    'description' => $asiento_info['descripcion'],
                    'lines' => $lineas_asiento,
                    'periodo' => $periodo_mes . '/' . $periodo_anio
                ];
                
                $asiento_id = $this->Contabilidad_model->create_journal($payload_asiento);
                
                if (!$asiento_id) {
                    throw new Exception('Error al crear el asiento');
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción de base de datos');
            }
            
            echo json_encode([
                'status' => 'success',
                'cuentas_creadas' => $cuentas_creadas,
                'cuentas_actualizadas' => $cuentas_actualizadas,
                'asiento_id' => $asiento_id,
                'total_cuentas' => count($cuentas),
                'periodo' => $periodo_mes . '/' . $periodo_anio,
                'errores' => $errores
            ]);
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error al importar: ' . $e->getMessage()]);
        }
    }

    /**
     * Parsear archivo Excel (XLSX/XLS)
     */
    private function _parsear_excel($filepath)
    {
        $cuentas = [];
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filepath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Buscar la fila de encabezados
            $header_row = -1;
            foreach ($rows as $idx => $row) {
                // Buscar columnas clave (Código, Denominación, etc.)
                $row_text = implode('|', array_map('strtolower', $row));
                if (strpos($row_text, 'código') !== false || strpos($row_text, 'codigo') !== false) {
                    $header_row = $idx;
                    break;
                }
            }
            
            if ($header_row === -1) {
                throw new Exception('No se encontró fila de encabezados');
            }
            
            $headers = array_map('trim', array_map('strtolower', $rows[$header_row]));
            
            // Mapear columnas
            $col_codigo = $this->_encontrar_columna($headers, ['código', 'codigo', 'code']);
            $col_nombre = $this->_encontrar_columna($headers, ['denominación', 'denominacion', 'nombre', 'name', 'descripción', 'descripcion']);
            $col_saldo_anterior = $this->_encontrar_columna($headers, ['saldo anterior', 'saldo_anterior', 'anterior']);
            $col_cargos = $this->_encontrar_columna($headers, ['cargos', 'debe', 'debito']);
            $col_abonos = $this->_encontrar_columna($headers, ['abonos', 'haber', 'credito']);
            $col_saldo_actual = $this->_encontrar_columna($headers, ['saldo actual', 'saldo_actual', 'saldo', 'balance']);
            
            if ($col_codigo === -1 || $col_nombre === -1) {
                throw new Exception('No se encontraron las columnas requeridas (Código y Denominación)');
            }
            
            // Procesar filas de datos
            for ($i = $header_row + 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                $codigo = isset($row[$col_codigo]) ? trim($row[$col_codigo]) : '';
                $nombre = isset($row[$col_nombre]) ? trim($row[$col_nombre]) : '';
                
                // Saltar filas vacías
                if (empty($codigo) || empty($nombre)) continue;
                
                // Extraer saldos
                $saldo_anterior = $col_saldo_anterior !== -1 && isset($row[$col_saldo_anterior]) ? 
                    $this->_limpiar_numero($row[$col_saldo_anterior]) : 0;
                $cargos = $col_cargos !== -1 && isset($row[$col_cargos]) ? 
                    $this->_limpiar_numero($row[$col_cargos]) : 0;
                $abonos = $col_abonos !== -1 && isset($row[$col_abonos]) ? 
                    $this->_limpiar_numero($row[$col_abonos]) : 0;
                $saldo_actual = $col_saldo_actual !== -1 && isset($row[$col_saldo_actual]) ? 
                    $this->_limpiar_numero($row[$col_saldo_actual]) : ($saldo_anterior + $cargos - $abonos);
                
                $cuentas[] = [
                    'code' => $codigo,
                    'name' => $nombre,
                    'saldo_anterior' => $saldo_anterior,
                    'cargos' => $cargos,
                    'abonos' => $abonos,
                    'saldo_actual' => $saldo_actual
                ];
            }
            
        } catch (Exception $e) {
            throw new Exception('Error al leer Excel: ' . $e->getMessage());
        }
        
        return $cuentas;
    }

    /**
     * Parsear archivo CSV
     */
    private function _parsear_csv($filepath)
    {
        $cuentas = [];
        
        if (($handle = fopen($filepath, 'r')) !== FALSE) {
            // Leer encabezados
            $headers = fgetcsv($handle, 10000, ',');
            if (!$headers) {
                fclose($handle);
                throw new Exception('No se pudo leer el archivo CSV');
            }
            
            $headers = array_map('trim', array_map('strtolower', $headers));
            
            // Mapear columnas
            $col_codigo = $this->_encontrar_columna($headers, ['código', 'codigo', 'code']);
            $col_nombre = $this->_encontrar_columna($headers, ['denominación', 'denominacion', 'nombre', 'name']);
            $col_saldo_anterior = $this->_encontrar_columna($headers, ['saldo anterior', 'saldo_anterior', 'anterior']);
            $col_cargos = $this->_encontrar_columna($headers, ['cargos', 'debe']);
            $col_abonos = $this->_encontrar_columna($headers, ['abonos', 'haber']);
            $col_saldo_actual = $this->_encontrar_columna($headers, ['saldo actual', 'saldo_actual', 'saldo']);
            
            if ($col_codigo === -1 || $col_nombre === -1) {
                fclose($handle);
                throw new Exception('No se encontraron las columnas requeridas');
            }
            
            // Leer datos
            while (($row = fgetcsv($handle, 10000, ',')) !== FALSE) {
                $codigo = isset($row[$col_codigo]) ? trim($row[$col_codigo]) : '';
                $nombre = isset($row[$col_nombre]) ? trim($row[$col_nombre]) : '';
                
                if (empty($codigo) || empty($nombre)) continue;
                
                $saldo_anterior = $col_saldo_anterior !== -1 && isset($row[$col_saldo_anterior]) ? 
                    $this->_limpiar_numero($row[$col_saldo_anterior]) : 0;
                $cargos = $col_cargos !== -1 && isset($row[$col_cargos]) ? 
                    $this->_limpiar_numero($row[$col_cargos]) : 0;
                $abonos = $col_abonos !== -1 && isset($row[$col_abonos]) ? 
                    $this->_limpiar_numero($row[$col_abonos]) : 0;
                $saldo_actual = $col_saldo_actual !== -1 && isset($row[$col_saldo_actual]) ? 
                    $this->_limpiar_numero($row[$col_saldo_actual]) : ($saldo_anterior + $cargos - $abonos);
                
                $cuentas[] = [
                    'code' => $codigo,
                    'name' => $nombre,
                    'saldo_anterior' => $saldo_anterior,
                    'cargos' => $cargos,
                    'abonos' => $abonos,
                    'saldo_actual' => $saldo_actual
                ];
            }
            
            fclose($handle);
        }
        
        return $cuentas;
    }

    /**
     * Procesar y clasificar cuentas según su código
     */
    private function _procesar_cuentas_balanza($cuentas_raw)
    {
        $cuentas_procesadas = [];
        $resumen = [
            'activo' => ['cantidad' => 0, 'total' => 0],
            'pasivo' => ['cantidad' => 0, 'total' => 0],
            'patrimonio' => ['cantidad' => 0, 'total' => 0],
            'ingreso' => ['cantidad' => 0, 'total' => 0],
            'gasto' => ['cantidad' => 0, 'total' => 0]
        ];
        
        $total_debe = 0;
        $total_haber = 0;
        
        foreach ($cuentas_raw as $cuenta) {
            $code = $cuenta['code'];
            $saldo = floatval($cuenta['saldo_actual']);
            
            // Clasificar según primer dígito del código
            $primer_digito = substr($code, 0, 1);
            $type = 'activo'; // default
            
            switch ($primer_digito) {
                case '1':
                    $type = 'activo';
                    break;
                case '2':
                    $type = 'pasivo';
                    break;
                case '3':
                    $type = 'patrimonio';
                    break;
                case '4':
                    $type = 'ingreso';
                    break;
                case '5':
                case '6':
                    $type = 'gasto';
                    break;
            }
            
            // Verificar si la cuenta ya existe
            $existe = $this->Contabilidad_model->get_account_by_code($code);
            
            $cuenta_procesada = [
                'code' => $code,
                'name' => $cuenta['name'],
                'type' => $type,
                'saldo_anterior' => $cuenta['saldo_anterior'],
                'cargos' => $cuenta['cargos'],
                'abonos' => $cuenta['abonos'],
                'saldo_actual' => $saldo,
                'existe' => $existe ? true : false
            ];
            
            $cuentas_procesadas[] = $cuenta_procesada;
            
            // Actualizar resumen
            $resumen[$type]['cantidad']++;
            $resumen[$type]['total'] += abs($saldo);
            
            // Calcular debe/haber para el asiento
            if (abs($saldo) > 0.001) {
                if (in_array($type, ['activo', 'gasto'])) {
                    $total_debe += ($saldo > 0 ? abs($saldo) : 0);
                    $total_haber += ($saldo < 0 ? abs($saldo) : 0);
                } else {
                    $total_haber += ($saldo > 0 ? abs($saldo) : 0);
                    $total_debe += ($saldo < 0 ? abs($saldo) : 0);
                }
            }
        }
        
        return [
            'cuentas' => $cuentas_procesadas,
            'resumen' => $resumen,
            'cuadre' => [
                'total_debe' => $total_debe,
                'total_haber' => $total_haber,
                'diferencia' => abs($total_debe - $total_haber)
            ]
        ];
    }

    /**
     * Encontrar columna en headers (case-insensitive, múltiples nombres posibles)
     */
    private function _encontrar_columna($headers, $nombres_posibles)
    {
        foreach ($nombres_posibles as $nombre) {
            foreach ($headers as $idx => $header) {
                if (stripos($header, $nombre) !== false) {
                    return $idx;
                }
            }
        }
        return -1;
    }

    /**
     * Limpiar número (remover formato de moneda, comas, etc.)
     */
    private function _limpiar_numero($valor)
    {
        if (is_numeric($valor)) return floatval($valor);
        
        // Remover símbolos de moneda, espacios, comas
        $valor = preg_replace('/[^\d\.\-]/', '', str_replace(',', '', $valor));
        
        return floatval($valor);
    }

    /**
     * Mayorizar (post) un asiento contable
     * La mayorización marca el asiento como permanente y bloquea su edición/anulación
     */
    public function post_entry()
    {
        $id = $this->input->post('id');
        header('Content-Type: application/json');
        
        if (!$id) {
            echo json_encode(['status' => 'error', 'error' => 'ID requerido']);
            return;
        }

        // Verificar que el asiento existe y no está anulado
        $this->db->where('id', $id);
        $this->db->where('voided', 0);
        $entry = $this->db->get('tb_journal')->row();

        if (!$entry) {
            echo json_encode(['status' => 'error', 'error' => 'Asiento no encontrado o está anulado']);
            return;
        }

        // Verificar que no esté ya mayorizado
        if ($entry->posted == 1) {
            echo json_encode(['status' => 'error', 'error' => 'El asiento ya está mayorizado']);
            return;
        }

        // Mayorizar el asiento - AQUÍ es cuando impacta tb_ledger
        $user_id = $this->ion_auth->logged_in() ? $this->ion_auth->get_user_id() : null;
        
        // Obtener las líneas del asiento para actualizar tb_ledger
        $journal_data = $this->Contabilidad_model->get_journal($id);
        if (!$journal_data || !isset($journal_data['lines'])) {
            echo json_encode(['status' => 'error', 'error' => 'Error al obtener líneas del asiento']);
            return;
        }
        
        $this->db->trans_start();
        
        // Actualizar estado de mayorización
        $this->db->where('id', $id);
        $this->db->update('tb_journal', [
            'posted' => 1,
            'posted_by' => $user_id,
            'posted_at' => date('Y-m-d H:i:s')
        ]);
        
        // AHORA SÍ actualizar tb_ledger con las líneas del asiento
        $period = date('Y-m', strtotime($journal_data['header']->date));
        foreach ($journal_data['lines'] as $line) {
            $this->Contabilidad_model->update_ledger_line(
                intval($line->account_id),
                $period,
                floatval($line->debit),
                floatval($line->credit)
            );
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'error' => 'Error al mayorizar el asiento']);
            return;
        }

        echo json_encode(['status' => 'success', 'message' => 'Asiento mayorizado correctamente']);
    }

    /**
     * Mayorizar múltiples asientos contables de forma masiva
     * Recibe un array de IDs y mayoriza todos los que sean válidos
     */
    public function mass_post()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        header('Content-Type: application/json');
        
        if (!isset($data['entry_ids']) || !is_array($data['entry_ids']) || empty($data['entry_ids'])) {
            echo json_encode(['success' => false, 'message' => 'No se recibieron IDs de asientos']);
            return;
        }

        $entry_ids = array_map('intval', $data['entry_ids']);
        $user_id = $this->ion_auth->logged_in() ? $this->ion_auth->get_user_id() : null;
        
        $posted_count = 0;
        $failed_count = 0;
        $errors = [];

        foreach ($entry_ids as $id) {
            // Verificar que el asiento existe y no está anulado
            $this->db->where('id', $id);
            $this->db->where('voided', 0);
            $entry = $this->db->get('tb_journal')->row();

            if (!$entry) {
                $failed_count++;
                $errors[] = "ID $id: Asiento no encontrado o está anulado";
                continue;
            }

            // Verificar que no esté ya mayorizado
            if ($entry->posted == 1) {
                $failed_count++;
                $errors[] = "ID $id: Ya está mayorizado";
                continue;
            }

            // Obtener las líneas del asiento
            $journal_data = $this->Contabilidad_model->get_journal($id);
            if (!$journal_data || !isset($journal_data['lines'])) {
                $failed_count++;
                $errors[] = "ID $id: Error al obtener líneas del asiento";
                continue;
            }

            // Mayorizar el asiento
            $this->db->trans_start();
            
            // Actualizar estado de mayorización
            $this->db->where('id', $id);
            $this->db->update('tb_journal', [
                'posted' => 1,
                'posted_by' => $user_id,
                'posted_at' => date('Y-m-d H:i:s')
            ]);
            
            // Actualizar tb_ledger con las líneas del asiento
            $period = date('Y-m', strtotime($journal_data['header']->date));
            foreach ($journal_data['lines'] as $line) {
                $this->Contabilidad_model->update_ledger_line(
                    intval($line->account_id),
                    $period,
                    floatval($line->debit),
                    floatval($line->credit)
                );
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                $failed_count++;
                $errors[] = "ID $id: Error en transacción de base de datos";
            } else {
                $posted_count++;
            }
        }

        echo json_encode([
            'success' => true,
            'posted' => $posted_count,
            'failed' => $failed_count,
            'errors' => $errors,
            'message' => "Mayorización completada: $posted_count exitosos, $failed_count fallidos"
        ]);
    }

    /**
     * Desmayorizar (unpost) un asiento contable
     * Permite volver a editar o anular un asiento mayorizado
     */
    public function unpost_entry()
    {
        $id = $this->input->post('id');
        header('Content-Type: application/json');
        
        if (!$id) {
            echo json_encode(['status' => 'error', 'error' => 'ID requerido']);
            return;
        }

        // Verificar que el asiento existe
        $this->db->where('id', $id);
        $entry = $this->db->get('tb_journal')->row();

        if (!$entry) {
            echo json_encode(['status' => 'error', 'error' => 'Asiento no encontrado']);
            return;
        }

        // Verificar que esté mayorizado
        if ($entry->posted == 0) {
            echo json_encode(['status' => 'error', 'error' => 'El asiento no está mayorizado']);
            return;
        }

        // Validar contraseña de administrador
        $admin_pass = $this->input->post('admin_pass');
        // contraseña requerida: 12345678 (se puede cambiar a validación contra Ion Auth si se desea)
        if (!$admin_pass || trim($admin_pass) !== '12345678') {
            echo json_encode(['status' => 'error', 'error' => 'Contraseña de administrador inválida']);
            return;
        }

        // Desmayorizar el asiento - REMOVER impacto de tb_ledger
        
        // Obtener las líneas del asiento para revertir en tb_ledger
        $journal_data = $this->Contabilidad_model->get_journal($id);
        if (!$journal_data || !isset($journal_data['lines'])) {
            echo json_encode(['status' => 'error', 'error' => 'Error al obtener líneas del asiento']);
            return;
        }
        
        $this->db->trans_start();
        
        // REVERTIR impacto en tb_ledger (restar los valores)
        $period = date('Y-m', strtotime($journal_data['header']->date));
        foreach ($journal_data['lines'] as $line) {
            $this->Contabilidad_model->update_ledger_line(
                intval($line->account_id),
                $period,
                -floatval($line->debit),  // Negativo para revertir
                -floatval($line->credit)  // Negativo para revertir
            );
        }
        
        // Actualizar estado de desmayorización
        $this->db->where('id', $id);
        $this->db->update('tb_journal', [
            'posted' => 0,
            'posted_by' => null,
            'posted_at' => null
        ]);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'error' => 'Error al desmayorizar el asiento']);
            return;
        }

        echo json_encode(['status' => 'success', 'message' => 'Asiento desmayorizado correctamente']);
    }

    /**
     * Limpiar todos los asientos contables
     * Deja el sistema en 0 para una nueva instalación
     */
    public function limpiar_asientos()
    {
        // Solo administradores pueden ejecutar esto
        if (!$this->ion_auth->is_admin()) {
            show_error('Acceso denegado. Solo administradores pueden ejecutar esta acción.', 403);
            return;
        }

        // Ejecutar limpieza de tablas relevantes de forma segura
        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        $ok1 = $this->db->query("TRUNCATE TABLE tb_journal_entry");
        $ok2 = $this->db->query("TRUNCATE TABLE tb_journal");
        $this->db->query("ALTER TABLE tb_journal_entry AUTO_INCREMENT = 1");
        $this->db->query("ALTER TABLE tb_journal AUTO_INCREMENT = 1");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            show_error('Error al limpiar asientos. Revisa los logs del servidor.', 500);
            return;
        }

        // Redirigir al diario con mensaje flash
        $this->session->set_flashdata('message', 'Limpieza de asientos completada correctamente');
        redirect('contabilidad/diario');
        return;
    }
}
