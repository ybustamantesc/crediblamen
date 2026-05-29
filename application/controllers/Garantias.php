<?php
// ...existing code...

class Garantias extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Garantia_model');
        $this->load->model('Garantia_verificacion_model');
        $this->load->model('core_model');
        $this->load->model('TasaCambio_model');
    }

    private function _infer_aprob_status_from_comment($comment)
    {
        $txt = strtolower((string)$comment);
        if (strpos($txt, 'anul') !== false) return 'annulled';
        if (strpos($txt, 'rechaz') !== false) return 'rejected';
        if (strpos($txt, 'aprob') !== false) return 'approved';
        return 'pending';
    }
    // ...existing code...

    /**
     * Descargar solo hoja de fotos (ahora genera PDF de avalúo)
     */
    public function pdf_fotos($solicitud_id)
    {
        if (! $solicitud_id) show_404();
        // Obtener las garantías asociadas a la solicitud
        $garantias = $this->Garantia_model->get_all_by_solicitud($solicitud_id);

        // Construir HTML de la tabla de avalúo
        $html = '<!doctype html><html><head><meta charset="utf-8"><title>Avalúo de Garantías</title>';
        $html .= '<style>@page { size: letter portrait; margin: 18mm 12mm; } body { font-family: Arial, sans-serif; font-size: 12px; background: #fff; } table { border-collapse: collapse; width: 100%; margin-top: 16px; } th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; font-size: 11px; } th { background: #eaeaea; } h2 { margin-bottom: 0; } .total { font-weight: bold; background: #f5f5f5; }</style>';
        $html .= '</head><body>';
        $html .= '<h2>Avalúo de Garantías</h2>';
        $html .= '<p><strong>ID Solicitud:</strong> '.htmlspecialchars($solicitud_id).'</p>';
        $html .= '<table><thead><tr>';
        $html .= '<th>Cant.</th><th>Descripción</th><th>Modelo</th><th>Marca/Color</th><th>Nº Serie</th><th>Avalúo C$</th><th>Avalúo US$</th><th>Estado</th>';
        $html .= '</tr></thead><tbody>';
        $total_c = 0; $total_usd = 0;
        if (!empty($garantias)) {
            foreach ($garantias as $g) {
                $costo = isset($g->costo) ? floatval($g->costo) : 0;
                $costo_usd = isset($g->costo_usd) ? floatval($g->costo_usd) : '';
                $total_c += $costo;
                $total_usd += is_numeric($costo_usd) ? $costo_usd : 0;
                $html .= '<tr>';
                $html .= '<td>'.htmlspecialchars($g->cantidad).'</td>';
                $html .= '<td>'.htmlspecialchars($g->nombre).'</td>';
                $html .= '<td>'.htmlspecialchars($g->modelo).'</td>';
                $html .= '<td>'.htmlspecialchars($g->marca).'</td>';
                $html .= '<td>'.htmlspecialchars($g->n_serie).'</td>';
                $html .= '<td>'.number_format($costo,2).'</td>';
                $html .= '<td>'.(is_numeric($costo_usd)?number_format($costo_usd,2):'').'</td>';
                $html .= '<td>'.htmlspecialchars($g->tiempo_vida).'</td>';
                $html .= '</tr>';
            }
            $html .= '<tr class="total"><td colspan="5">TOTAL</td><td>'.number_format($total_c,2).'</td><td>'.number_format($total_usd,2).'</td><td></td></tr>';
        } else {
            $html .= '<tr><td colspan="8">No hay garantías registradas para esta solicitud.</td></tr>';
        }
        $html .= '</tbody></table>';
        $html .= '</body></html>';

        // Preparar directorio temporal para Dompdf
        $tempDir = FCPATH . 'tmp';
        if (! is_dir($tempDir)) @mkdir($tempDir, 0755, true);
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('tempDir', $tempDir);
        try {
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->load_html($html);
            $dompdf->set_paper('letter', 'portrait');
            $dompdf->render();
            $dompdf->stream('avaluo_garantias_'.$solicitud_id.'.pdf', ['Attachment' => 1]);
        } catch (\Exception $e) {
            // Si hay error, mostrar PDF simple con mensaje de error
            $html_error = '<html><body><h2>Error al generar PDF</h2><p>'.$e->getMessage().'</p></body></html>';
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->load_html($html_error);
            $dompdf->set_paper('letter', 'portrait');
            $dompdf->render();
            $dompdf->stream('error_avaluo_garantias_'.$solicitud_id.'.pdf', ['Attachment' => 1]);
        }
    }

    /**
     * Test endpoint to verify controller is working
     */
    public function test()
    {
        echo json_encode([
            'status' => 'OK',
            'message' => 'Garantias controller is working',
            'models_loaded' => [
                'Garantia_model' => isset($this->Garantia_model),
                'TasaCambio_model' => isset($this->TasaCambio_model)
            ]
        ]);
    }

    public function index()
    {
        // Lista de garantías con layout completo similar a solicitudes
        $data = array(
            'titulo' => 'Formato de Garantía',
            'subtitulo' => 'Listado de formatos de garantía',
            'icono' => 'fas fa-shield-alt',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
                'plugins/select2/dist/css/select2.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/select2/dist/js/select2.min.js',
                'plugins/datatables.net/js/activaDatatable.js'
            ),
            'garantias' => $this->Garantia_model->get_grouped_by_solicitud()
        );

        // Enriquecer listado con datos de solicitud (cliente, destino conami y asesor/ruta)
        $sol_map = array();
        if (!empty($data['garantias']) && is_array($data['garantias'])) {
            $solicitud_ids = array();
            foreach ($data['garantias'] as $g0) {
                if (isset($g0->solicitud_id)) $solicitud_ids[] = (int)$g0->solicitud_id;
            }
            $solicitud_ids = array_values(array_unique(array_filter($solicitud_ids)));

            if (!empty($solicitud_ids)) {
                $this->db->select('tb_solicitudes.*, CONCAT(IFNULL(tb_asesores.nombres, ""), "") as nombre_asesor');
                $this->db->from('tb_solicitudes');
                $this->db->join('tb_asesores', 'tb_solicitudes.idasesor = tb_asesores.idasesor', 'left');
                $this->db->where_in('tb_solicitudes.idsolicitud', $solicitud_ids);
                $sols = $this->db->get()->result();

                foreach ($sols as $s) {
                    $sol_map[(int)$s->idsolicitud] = $s;
                }

                foreach ($data['garantias'] as &$g1) {
                    $sid = isset($g1->solicitud_id) ? (int)$g1->solicitud_id : 0;
                    if ($sid && isset($sol_map[$sid])) {
                        $s = $sol_map[$sid];
                        $g1->cliente_nombre = trim((string)($s->nombres ?? '') . ' ' . (string)($s->apellidos ?? ''));
                        if ($g1->cliente_nombre === '') {
                            $g1->cliente_nombre = (string)($s->nombre_completo ?? '');
                        }
                        $g1->rubro_credito = (string)($s->rubro_credito ?? '');
                        $g1->nombre_asesor = (string)($s->nombre_asesor ?? '');
                        if ($g1->nombre_asesor === '') {
                            $g1->nombre_asesor = (string)($s->nombre_promotor ?? '');
                        }
                    }
                }
                unset($g1);
            }
        }

        // mark grouped rows: whether any verification exists for the solicitud, and approval status per solicitud
        if (!empty($data['garantias']) && is_array($data['garantias'])) {
            foreach ($data['garantias'] as $g) {
                // verificado si existe alguna verificación para la solicitud
                $verifs = $this->Garantia_verificacion_model->get_by_solicitud($g->solicitud_id);
                $g->verified = !empty($verifs);
                // If there is a verification record for this solicitud, record the garantia_id
                if (!empty($verifs) && is_array($verifs)) {
                    $first = $verifs[0];
                    $g->ver_garantia_id = isset($first->garantia_id) ? intval($first->garantia_id) : null;
                } else {
                    $g->ver_garantia_id = null;
                }

                // approval status derived from committee approvals for the solicitud
                $g->aprob_status = 'pending';
                $sid = isset($g->solicitud_id) ? intval($g->solicitud_id) : 0;
                if ($sid) {
                    $estado_sol = (isset($sol_map[$sid]) && isset($sol_map[$sid]->estado_aprobacion))
                        ? strtolower((string)$sol_map[$sid]->estado_aprobacion)
                        : '';
                    if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                        $g->aprob_status = 'annulled';
                    }

                    if (method_exists($this->core_model, 'get_by_id_all')) {
                        $aprs = $this->core_model->get_by_id_all('tb_solicitud_aprobaciones', array('idsolicitud' => $sid));
                    } else {
                        $aprs = $this->db->where('idsolicitud', $sid)->order_by('created_at', 'DESC')->get('tb_solicitud_aprobaciones')->result();
                    }
                    if (!empty($aprs) && is_array($aprs)) {
                        usort($aprs, function($a,$b){ $ta = isset($a->created_at)?strtotime($a->created_at):0; $tb = isset($b->created_at)?strtotime($b->created_at):0; return $tb - $ta; });
                        $latest = $aprs[0];
                        $g->aprob_status = $this->_infer_aprob_status_from_comment(isset($latest->comment) ? $latest->comment : '');
                    }

                    if ($estado_sol === 'anulado' || $estado_sol === 'annulled') {
                        $g->aprob_status = 'annulled';
                    }

                    $plan = $this->core_model->get_by_id('tb_prestamos', array('idsolicitud' => $sid));
                    if ($plan && isset($plan->estado) && intval($plan->estado) === 2) {
                        $g->aprob_status = 'annulled';
                    }
                }
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('garantias/index', $data);
        $this->load->view('layout/footer');
    }

    public function create($solicitud_id = null)
    {
        if (! $solicitud_id) {
            $this->session->set_flashdata('error', 'Falta el ID de la solicitud.');
            redirect('solicitudes');
        }

        // validar que la solicitud existe
        $sol = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $solicitud_id));
        if (! $sol) {
            $this->session->set_flashdata('error', 'Solicitud no encontrada.');
            redirect('solicitudes');
        }

        $garantias = $this->Garantia_model->get_all_by_solicitud($solicitud_id);

        $photos_map = [];
        if ($this->db->table_exists('tb_garantias_fotos')) {
            $rows = $this->db->select('garantia_id, filename')
                ->from('tb_garantias_fotos')
                ->where('solicitud_id', $solicitud_id)
                ->order_by('row_index, created_at')
                ->get()
                ->result();
            if (! empty($rows)) {
                foreach ($rows as $row) {
                    if (empty($row->garantia_id)) continue;
                    $photos_map[intval($row->garantia_id)][] = $row->filename;
                }
            }
        }

        $data = [
            'titulo' => 'Formato de Garantía',
            'solicitud_id' => $solicitud_id,
            'garantias' => $garantias,
            'photos_map' => $photos_map
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('garantias/formato_garantia', $data);
        $this->load->view('layout/footer');
    }

    public function save()
    {
        try {
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            
            $post = $this->input->post();
            
            // Handle photos marked for deletion
            $fotos_eliminar = $this->input->post('fotos_eliminar');
            if (!empty($fotos_eliminar)) {
                if (!is_array($fotos_eliminar)) {
                    $fotos_eliminar = [$fotos_eliminar];
                }
                foreach ($fotos_eliminar as $foto_path) {
                    $foto_path = trim($foto_path);
                    if (empty($foto_path)) continue;
                    
                    try {
                        // Delete file from filesystem
                        $file_path = FCPATH . $foto_path;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                            log_message('debug', '[GARANTIAS] Deleted photo file: ' . $foto_path);
                        }
                        
                        // Delete from database - both table storage and column storage
                        if ($this->db->table_exists('tb_garantias_fotos')) {
                            $this->db->delete('tb_garantias_fotos', ['filename' => $foto_path]);
                        }
                        
                        // Also check and clear from foto columns if needed
                        $this->db->set('foto1', NULL)->where('foto1', $foto_path)->update('tb_garantias');
                        $this->db->set('foto2', NULL)->where('foto2', $foto_path)->update('tb_garantias');
                        $this->db->set('foto3', NULL)->where('foto3', $foto_path)->update('tb_garantias');
                        $this->db->set('foto4', NULL)->where('foto4', $foto_path)->update('tb_garantias');
                        $this->db->set('foto5', NULL)->where('foto5', $foto_path)->update('tb_garantias');
                        
                    } catch (Exception $e) {
                        log_message('error', '[GARANTIAS] Error deleting photo ' . $foto_path . ': ' . $e->getMessage());
                    }
                }
            }
            // Temporary debug: dump POST and FILES for AJAX requests to project logs
            if ($this->input->is_ajax_request()) {
                try {
                    $dbg = "=== GARANTIAS SAVE DEBUG " . date('c') . " ===\n";
                    $dbg .= "POST: " . print_r($post, true) . "\n";
                    $dbg .= "_FILES: " . print_r(isset($_FILES) ? $_FILES : array(), true) . "\n";
                    $raw = '';
                    try { $raw = $this->input->raw_input_stream; } catch (Exception $e) { $raw = ''; }
                    $dbg .= "RAW: " . $raw . "\n\n";
                    @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', $dbg, FILE_APPEND);
                } catch (Exception $e) { 
                    log_message('error', '[GARANTIAS] Debug logging error: ' . $e->getMessage());
                }
                // If no files were sent, log and continue — saving should still work without photos
                if (empty($_FILES)) {
                    log_message('debug', '[GARANTIAS] AJAX save received with empty $_FILES. Proceeding without file uploads. POST=' . print_r($post, true));
                }
            }
            
            $solicitud_id = isset($post['solicitud_id']) ? intval($post['solicitud_id']) : 0;
            if (! $solicitud_id) {
                if ($this->input->is_ajax_request()) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['success' => false, 'message' => 'Falta el ID de la solicitud']);
                    return;
                }
                $this->session->set_flashdata('error', 'Falta el ID de la solicitud.');
                redirect($_SERVER['HTTP_REFERER'] ?? base_url('solicitudes'));
                return;
            }

            // Prepare upload dir
            $upload_dir = FCPATH . 'uploads/garantias/solicitud_' . $solicitud_id . '/';
            if (! is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            // upload dir prepared

            // Instead of deleting existing guarantees and photos, update existing rows where possible
            $existing_rows = $this->Garantia_model->get_all_by_solicitud($solicitud_id);
            $existing_rows = is_array($existing_rows) ? array_values($existing_rows) : array();
            
            @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "CHECKPOINT 1: Loaded existing rows\n", FILE_APPEND);

            $nombres = $this->input->post('nombre');
            $cantidades = $this->input->post('cantidad');
            $marcas = $this->input->post('marca');
            $modelos = $this->input->post('modelo');
            $n_series = $this->input->post('n_serie');
            $costos = $this->input->post('costo');
            $tiempos = $this->input->post('tiempo_vida');
            // Posted garantia ids (one per row) - used to determine updates vs inserts
            $post_garantia_ids = $this->input->post('garantia_id');
            $post_garantia_ids = is_array($post_garantia_ids) ? $post_garantia_ids : array();
            
            @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "CHECKPOINT 2: Got POST data\n", FILE_APPEND);

            // ensure arrays
            $nombres = is_array($nombres) ? $nombres : array();
            $cantidades = is_array($cantidades) ? $cantidades : array();
            $marcas = is_array($marcas) ? $marcas : array();
            $modelos = is_array($modelos) ? $modelos : array();
            $n_series = is_array($n_series) ? $n_series : array();
            $costos = is_array($costos) ? $costos : array();
            $tiempos = is_array($tiempos) ? $tiempos : array();

            $rowCount = max( count($nombres), count($cantidades), count($marcas), count($modelos), count($n_series), count($costos), count($tiempos) );
            // computed row count
            @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "CHECKPOINT 3: rowCount=$rowCount\n", FILE_APPEND);
            $upload_errors = array();
            // Handle each posted row: update existing garantia (by index) or insert new one
            for ($i = 0; $i < $rowCount; $i++) {
                @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "CHECKPOINT 4: Processing row $i\n", FILE_APPEND);
                try {
                    $nombre = isset($nombres[$i]) ? trim($nombres[$i]) : '';
                    $cantidad = isset($cantidades[$i]) ? intval($cantidades[$i]) : 0;
                    $marca = isset($marcas[$i]) ? trim($marcas[$i]) : '';
                    $modelo = isset($modelos[$i]) ? trim($modelos[$i]) : '';
                    $n_serie = isset($n_series[$i]) ? trim($n_series[$i]) : '';
                    $costo = isset($costos[$i]) && $costos[$i] !== '' ? $costos[$i] : null;
                    $tiempo = isset($tiempos[$i]) ? trim($tiempos[$i]) : '';

                    // Only process if there is at least one meaningful value
                    if ($nombre === '' && $cantidad <= 0 && $marca === '' && $modelo === '' && $n_serie === '' && $costo === null && $tiempo === '') {
                        continue;
                    }

                    $row = [
                        'solicitud_id' => $solicitud_id,
                        'nombre' => $nombre,
                        'cantidad' => $cantidad > 0 ? $cantidad : null,
                        'marca' => $marca,
                        'modelo' => $modelo,
                        'n_serie' => $n_serie,
                        'costo' => $costo,
                        'tiempo_vida' => $tiempo,
                    ];
                        // row data prepared

                    // Decide update vs insert based on posted garantia_id for this row
                    $posted_id = isset($post_garantia_ids[$i]) && $post_garantia_ids[$i] !== '' ? intval($post_garantia_ids[$i]) : null;
                    if ($posted_id) {
                        $garantia_id = $posted_id;
                        $this->Garantia_model->update($garantia_id, $row);
                        @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "CHECKPOINT 5: Updated garantia_id=$garantia_id\n", FILE_APPEND);
                    } else {
                        // Prevent accidental duplicate inserts: try to find an existing similar row
                        try {
                            $possible = $this->db->from('tb_garantias')
                                ->where('solicitud_id', $solicitud_id)
                                ->where('nombre', $row['nombre'])
                                ->where('n_serie', $row['n_serie'])
                                ->where('marca', $row['marca'])
                                ->where('modelo', $row['modelo'])
                                ->where('tiempo_vida', $row['tiempo_vida'])
                                ->limit(1)
                                ->get()
                                ->row();
                        } catch (Exception $e) { $possible = null; }

                        if ($possible && isset($possible->id)) {
                            // Found similar existing record — update it instead of inserting to avoid duplicates
                            $garantia_id = intval($possible->id);
                            $this->Garantia_model->update($garantia_id, $row);
                            @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "CHECKPOINT 6: Found similar garantia_id=$garantia_id, updated instead of insert\n", FILE_APPEND);
                        } else {
                            $garantia_id = $this->Garantia_model->insert($row);
                            @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "CHECKPOINT 6: Inserted garantia_id=$garantia_id\n", FILE_APPEND);
                        }
                    }

                    // process uploaded files for this row: structure from FormData is $_FILES['fotos'] with nested arrays
                    $saved_files = [];
                    $upload_errors = [];
                    try {
                        if (! empty($_FILES['fotos']) && isset($_FILES['fotos']['name'][$i])) {
                            $names = $_FILES['fotos']['name'][$i];
                            $tmp_names = $_FILES['fotos']['tmp_name'][$i];
                            $types = $_FILES['fotos']['type'][$i];
                            $errors = $_FILES['fotos']['error'][$i];
                            $sizes = $_FILES['fotos']['size'][$i];

                            // if single file, names may be string not array
                            if (! is_array($names)) {
                                $names = [$names];
                                $tmp_names = [$tmp_names];
                                $types = [$types];
                                $errors = [$errors];
                                $sizes = [$sizes];
                            }

                            // take up to 5 files
                            for ($f = 0; $f < min(count($names), 5); $f++) {
                                if (!isset($errors[$f]) || $errors[$f] === UPLOAD_ERR_NO_FILE || $errors[$f] !== UPLOAD_ERR_OK) continue;

                                // build a temporary $_FILES entry for upload library
                                $_FILES['upload_file'] = [
                                    'name' => $names[$f],
                                    'type' => $types[$f],
                                    'tmp_name' => $tmp_names[$f],
                                    'error' => $errors[$f],
                                    'size' => $sizes[$f]
                                ];

                                $config = [];
                                $config['upload_path'] = $upload_dir;
                                // allow common web image types including webp
                                $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
                                $config['max_size'] = 8192; // 8MB
                                $config['encrypt_name'] = TRUE;
                                $this->upload->initialize($config);

                                if ($this->upload->do_upload('upload_file')) {
                                    $info = $this->upload->data();
                                    $foto_path = 'uploads/garantias/solicitud_' . $solicitud_id . '/' . $info['file_name'];
                                    $saved_files[] = $foto_path;
                                } else {
                                    $err = $this->upload->display_errors('', '');
                                    log_message('error', '[GARANTIAS] upload error row ' . $i . ': ' . strip_tags($err));
                                    $upload_errors[] = 'row ' . $i . ': ' . strip_tags($err);
                                }
                            }
                        }
                    } catch (Exception $upload_ex) {
                        log_message('error', '[GARANTIAS] File upload exception for row ' . $i . ': ' . $upload_ex->getMessage());
                        $upload_errors[] = 'row ' . $i . ': ' . $upload_ex->getMessage();
                    }

                    // if there are saved files, persist them (append — do not delete existing ones)
                    if (! empty($saved_files)) {
                        if ($this->db->table_exists('tb_garantias_fotos')) {
                            foreach ($saved_files as $idx => $path) {
                                try {
                                    $this->db->insert('tb_garantias_fotos', [
                                        'garantia_id' => $garantia_id,
                                        'solicitud_id' => $solicitud_id,
                                        'row_index' => $i,
                                        'filename' => $path,
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);
                                } catch (Exception $e) {
                                    log_message('error', '[GARANTIAS] DB insert foto error: ' . $e->getMessage());
                                }
                            }
                        } else {
                            // fallback: append into foto columns only if empty slots exist
                            $current = $this->Garantia_model->get($garantia_id);
                            $update = [];
                            if ($current) {
                                for ($k = 0; $k < min(5, count($saved_files)); $k++) {
                                    $col = 'foto' . ($k+1);
                                    if (empty($current->$col)) {
                                        $update[$col] = $saved_files[$k];
                                    }
                                }
                            } else {
                                for ($k = 0; $k < min(5, count($saved_files)); $k++) {
                                    $col = 'foto' . ($k+1);
                                    $update[$col] = $saved_files[$k];
                                }
                            }
                            if (! empty($update)) {
                                $this->Garantia_model->update($garantia_id, $update);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Log and continue to next row
                    log_message('error', '[GARANTIAS] error processing row ' . $i . ': ' . $e->getMessage());
                    if ($this->input->is_ajax_request()) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(['success' => false, 'message' => 'Error procesando fila ' . $i . ': ' . $e->getMessage()]);
                        return;
                    }
                }
            }

            // If this was an AJAX request and we encountered upload errors, return them explicitly
            if ($this->input->is_ajax_request() && ! empty($upload_errors)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array('success' => false, 'message' => 'Errores en subida de archivos', 'errors' => $upload_errors));
                return;
            }

            // Delete any existing garantías that were not included in the posted garantia_id[] list
            try {
                $existing_ids_in_db = array();
                foreach ($existing_rows as $er) {
                    if (isset($er->id)) $existing_ids_in_db[] = intval($er->id);
                }
                $posted_nonempty = array();
                foreach ($post_garantia_ids as $pid) {
                    if ($pid !== '' && $pid !== null) $posted_nonempty[] = intval($pid);
                }
                $to_delete = array_diff($existing_ids_in_db, $posted_nonempty);
                if (! empty($to_delete)) {
                    foreach ($to_delete as $del_id) {
                        // remove associated photos from disk and table
                        if ($this->db->table_exists('tb_garantias_fotos')) {
                            $rows = $this->db->select('filename')->from('tb_garantias_fotos')->where('garantia_id', $del_id)->get()->result();
                            if (! empty($rows)) {
                                foreach ($rows as $r) {
                                    $fname = trim((string)$r->filename);
                                    if ($fname !== '') {
                                        $fpath = FCPATH . ltrim($fname, '/\\');
                                        if (is_file($fpath)) @unlink($fpath);
                                    }
                                }
                            }
                            $this->db->delete('tb_garantias_fotos', array('garantia_id' => $del_id));
                        }

                        // finally delete garantia row
                        $this->db->delete('tb_garantias', array('id' => $del_id));
                        @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "DELETED garantia_id=$del_id because it was not present in posted data\n", FILE_APPEND);
                    }
                }
            } catch (Exception $e) {
                log_message('error', '[GARANTIAS] error deleting missing rows: ' . $e->getMessage());
            }

            $this->session->set_flashdata('message', 'Formato de garantía guardado.');
            @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', "SAVE completed for solicitud_id=" . $solicitud_id . "\n", FILE_APPEND);

            // If request was AJAX, return JSON so JS can open the PDF directly
            if ($this->input->is_ajax_request()) {
                $pdf_url = base_url('garantias/pdf_by_solicitud/' . $solicitud_id);
                $redirect = base_url('garantias/index?solicitud=' . $solicitud_id);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array('success' => true, 'solicitud_id' => $solicitud_id, 'pdf_url' => $pdf_url, 'redirect' => $redirect));
                return;
            }

            redirect(base_url('garantias/index?solicitud=' . $solicitud_id));
        } catch (\Exception $e) {
            // Top-level error handler for this save action
            $msg = '[GARANTIAS] save exception: ' . $e->getMessage();
            log_message('error', $msg);
            // Also append full message and trace to the dedicated debug log for visibility
            try {
                $dbg = "=== GARANTIAS SAVE EXCEPTION " . date('c') . " ===\n";
                $dbg .= "Message: " . $e->getMessage() . "\n";
                $dbg .= "File: " . $e->getFile() . "\n";
                $dbg .= "Line: " . $e->getLine() . "\n";
                $dbg .= "Trace:\n" . $e->getTraceAsString() . "\n\n";
                @file_put_contents(APPPATH . 'logs/garantias_save_debug.log', $dbg, FILE_APPEND);
            } catch (\Exception $inner) { /* ignore debug write errors */ }

            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Error guardando: ' . $e->getMessage()]);
                return;
            }
            $this->session->set_flashdata('error', 'Error al guardar formato: ' . $e->getMessage());
            redirect($_SERVER['HTTP_REFERER'] ?? base_url('garantias'));
        }
    }

    public function view($id)
    {
        $g = $this->Garantia_model->get($id);
        if (! $g) show_404();
        $data = ['g' => $g, 'titulo' => 'Ver Formato de Garantía'];
        $this->load->view('layout/header', $data);
        $this->load->view('garantias/view', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Show all garantias for a solicitud (aggregate view)
     */
    public function view_by_solicitud($solicitud_id = null)
    {
        if (! $solicitud_id) show_404();
        $garantias = $this->Garantia_model->get_all_by_solicitud($solicitud_id);
        if (empty($garantias)) {
            $this->session->set_flashdata('error', 'No se encontraron garantías para la solicitud.');
            redirect('garantias');
        }

        // Use the first garantia as representative for header/title
        $g = $garantias[0];
        // collect photos by garantia_id if related table exists
        $photos_map = [];
        if ($this->db->table_exists('tb_garantias_fotos')) {
            $rows = $this->db->select('garantia_id, filename')->from('tb_garantias_fotos')->where('solicitud_id', $solicitud_id)->order_by('row_index, created_at')->get()->result();
            if (! empty($rows)) {
                foreach ($rows as $r) {
                    if (empty($r->garantia_id)) continue;
                    $photos_map[intval($r->garantia_id)][] = $r->filename;
                }
            }
        }

        $data = ['g' => $g, 'garantias' => $garantias, 'photos_map' => $photos_map, 'titulo' => 'Formato de Garantía - Solicitud '.$solicitud_id];
        $this->load->view('layout/header', $data);
        $this->load->view('garantias/view_solicitud', $data);
        $this->load->view('layout/footer');
    }

    /**
     * AJAX: list all photos for a solicitud (garantias)
     * GET: /garantias/list_photos_ajax/{solicitud_id}
     */
    public function list_photos_ajax($solicitud_id = null)
    {
        if (! $solicitud_id) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => FALSE, 'message' => 'Falta id de solicitud', 'photos' => array())));
            return;
        }
        $photos = array();
        if ($this->db->table_exists('tb_garantias_fotos')) {
            try {
                $photos = $this->db->select('*')->from('tb_garantias_fotos')->where('solicitud_id', $solicitud_id)->order_by('row_index, created_at')->get()->result();
                if (! is_array($photos)) $photos = array();
            } catch (Exception $e) { $photos = array(); }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => TRUE, 'photos' => $photos)));
    }

    /**
     * Web: show gallery page for garantia photos by solicitud
     */
    public function photos($solicitud_id = null)
    {
        if (! $solicitud_id || ! $this->db->get_where('tb_solicitudes', array('idsolicitud' => $solicitud_id))->row()) {
            $this->session->set_flashdata('error', 'Solicitud no encontrada');
            redirect('garantias');
            return;
        }
        $photos = array();
        if ($this->db->table_exists('tb_garantias_fotos')) {
            try { $photos = $this->db->select('*')->from('tb_garantias_fotos')->where('solicitud_id', $solicitud_id)->order_by('created_at')->get()->result(); } catch (Exception $e) { $photos = array(); }
        }
        $garantias = array();
        try { $garantias = $this->Garantia_model->get_all_by_solicitud($solicitud_id); } catch (Exception $e) { $garantias = array(); }
        $data = array(
            'titulo' => 'Fotos de Garantías',
            'subtitulo' => 'Galería de imágenes para la solicitud #' . intval($solicitud_id),
            'icono' => 'fas fa-image',
            'photos' => $photos,
            'garantias' => $garantias,
            'solicitud_id' => intval($solicitud_id)
        );
        $this->load->view('layout/header', $data);
        $this->load->view('garantias/photos', $data);
        $this->load->view('layout/footer');
    }

    /**
     * AJAX: upload single photo for a garantia
     * POST: solicitud_id, garantia_id, file 'photo'
     */
    public function upload_garantia_photo_ajax()
    {
        // Set JSON header early
        header('Content-Type: application/json; charset=utf-8');
        
        $ids = intval($this->input->post('solicitud_id'));
        $garantia_id = intval($this->input->post('garantia_id')) ?: null;
        
        // Validate required parameters
        if (! $ids) {
            echo json_encode(array('status' => FALSE, 'message' => 'Falta el ID de solicitud'));
            return;
        }
        
        if (empty($_FILES['photo'])) {
            echo json_encode(array('status' => FALSE, 'message' => 'No se recibió ningún archivo'));
            return;
        }
        
        // Ensure solicitud exists
        if (! $this->db->get_where('tb_solicitudes', array('idsolicitud' => $ids))->row()) {
            echo json_encode(array('status' => FALSE, 'message' => 'Solicitud no encontrada'));
            return;
        }
        
        $file = $_FILES['photo'];
        
        // Validate file upload
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            echo json_encode(array('status' => FALSE, 'message' => 'Archivo no subido correctamente'));
            return;
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_msg = 'Error al subir archivo: ';
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_msg .= 'El archivo excede el tamaño máximo permitido';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_msg .= 'El archivo se subió parcialmente';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_msg .= 'No se subió ningún archivo';
                    break;
                default:
                    $error_msg .= 'Error desconocido (' . $file['error'] . ')';
            }
            echo json_encode(array('status' => FALSE, 'message' => $error_msg));
            return;
        }
        
        // Validate file type
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed_types)) {
            echo json_encode(array('status' => FALSE, 'message' => 'Tipo de archivo no permitido. Solo imágenes JPG, PNG, GIF, WEBP'));
            return;
        }
        
        // Generate safe filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($ext));
        $name = time() . '_' . substr(md5(uniqid('', true)), 0, 8) . ($safeExt ? '.' . $safeExt : '.jpg');
        
        // Prepare destination directory
        $destDir = FCPATH . 'uploads/garantias/solicitud_' . $ids . '/';
        if (!is_dir($destDir)) {
            if (!@mkdir($destDir, 0755, true)) {
                echo json_encode(array('status' => FALSE, 'message' => 'No se pudo crear el directorio de uploads'));
                return;
            }
        }
        
        // Check if directory is writable
        if (!is_writable($destDir)) {
            echo json_encode(array('status' => FALSE, 'message' => 'El directorio de uploads no tiene permisos de escritura'));
            return;
        }
        
        $target = $destDir . $name;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            echo json_encode(array('status' => FALSE, 'message' => 'No se pudo mover el archivo al directorio de destino'));
            return;
        }
        
        $relPath = 'solicitud_' . $ids . '/' . $name;
        $insert_id = null;
        
        // Save to database
        if ($this->db->table_exists('tb_garantias_fotos')) {
            try {
                $this->db->insert('tb_garantias_fotos', array(
                    'garantia_id' => $garantia_id,
                    'solicitud_id' => $ids,
                    'filename' => 'uploads/garantias/' . $relPath,
                    'created_at' => date('Y-m-d H:i:s')
                ));
                $insert_id = $this->db->insert_id();
            } catch (Exception $e) {
                log_message('error', '[GARANTIAS] upload_garantia_photo_ajax DB insert error: ' . $e->getMessage());
                // Continue even if DB insert fails, file is uploaded
            }
        }
        
        $url = base_url('uploads/garantias/' . $relPath);
        echo json_encode(array('status' => TRUE, 'file' => $name, 'url' => $url, 'id' => $insert_id));
    }

    /**
     * AJAX: delete garantia photo
     * POST: id OR filename
     */
    public function delete_garantia_photo_ajax()
    {
        $id = $this->input->post('id');
        $filename = $this->input->post('filename');
        $deleted = false;
        if ($this->db->table_exists('tb_garantias_fotos')) {
            if ($id) {
                // try delete by id (try common id column names)
                $deleted = (bool)$this->db->delete('tb_garantias_fotos', array('id' => $id));
                if (!$deleted) $deleted = (bool)$this->db->delete('tb_garantias_fotos', array('idfoto' => $id));
            }
            if (!$deleted && $filename) {
                $deleted = (bool)$this->db->delete('tb_garantias_fotos', array('filename' => $filename));
            }
        }
        // attempt to delete file on disk if filename provided or found by id
        if ($filename) {
            $fpath = FCPATH . ltrim($filename, '/');
            if (is_file($fpath)) { @unlink($fpath); }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('status' => (bool)$deleted)));
    }

    public function pdf($id)
    {
        $g = $this->Garantia_model->get($id);
        if (! $g) show_404();

        // Load Dompdf: prefer Composer autoload, but if composer autoload exists and Dompdf
        // is not available there, also try the bundled dompdf/autoload.inc.php.
        if (file_exists(FCPATH . 'vendor/autoload.php')) {
            require_once FCPATH . 'vendor/autoload.php';
            // If Dompdf class is still missing, try the bundled autoload
            if (! class_exists('\\Dompdf\\Dompdf')) {
                if (file_exists(FCPATH . 'dompdf/autoload.inc.php')) {
                    require_once FCPATH . 'dompdf/autoload.inc.php';
                } elseif (file_exists(APPPATH . 'third_party/dompdf/autoload.inc.php')) {
                    require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
                }
            }
        } else {
            if (file_exists(FCPATH . 'dompdf/autoload.inc.php')) {
                require_once FCPATH . 'dompdf/autoload.inc.php';
            } elseif (file_exists(APPPATH . 'third_party/dompdf/autoload.inc.php')) {
                require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
            }
        }

        if (! class_exists('\\Dompdf\\Dompdf')) {
            show_error('Dompdf no está disponible. Instale la librería (composer require dompdf/dompdf) o coloque la carpeta /dompdf en el proyecto.');
        }

        // prepare all items for the solicitud so the first page contains the full table
        $garantias = $this->Garantia_model->get_all_by_solicitud($g->solicitud_id);

        // collect photos for the solicitud: prefer related table if exists, else use foto1..foto5
        // Prefer embedding local files as data-URIs so Dompdf doesn't need remote fetch.
        $photos = [];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($this->db->table_exists('tb_garantias_fotos')) {
            $rows = $this->db->select('filename')->from('tb_garantias_fotos')->where('solicitud_id', $g->solicitud_id)->order_by('created_at')->get()->result();
            foreach ($rows as $r) {
                if (empty($r->filename)) continue;
                $rel = $r->filename;
                $abs = FCPATH . ltrim($rel, '/\\');
                if (file_exists($abs)) {
                    $mime = finfo_file($finfo, $abs) ?: 'image/jpeg';
                    $data = base64_encode(file_get_contents($abs));
                    $photos[] = 'data:' . $mime . ';base64,' . $data;
                } else {
                    // fallback to web URL if file not found locally
                    $photos[] = base_url($rel);
                }
            }
        } else {
            foreach ($garantias as $row) {
                for ($i = 1; $i <= 5; $i++) {
                    $col = 'foto' . $i;
                    if (empty($row->$col)) continue;
                    $rel = $row->$col;
                    $abs = FCPATH . ltrim($rel, '/\\');
                    if (file_exists($abs)) {
                        $mime = finfo_file($finfo, $abs) ?: 'image/jpeg';
                        $data = base64_encode(file_get_contents($abs));
                        $photos[] = 'data:' . $mime . ';base64,' . $data;
                    } else {
                        $photos[] = base_url($rel);
                    }
                }
            }
        }
        finfo_close($finfo);

        // Fallback: if no photos found in DB/columns, try scanning uploads/garantias/solicitud_{id}
        if (empty($photos)) {
            $upload_dir = FCPATH . 'uploads/garantias/solicitud_' . $g->solicitud_id . '/';
            if (is_dir($upload_dir)) {
                $files = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                foreach ($files as $fpath) {
                    if (! file_exists($fpath)) continue;
                    $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fpath) ?: 'image/jpeg';
                    $data = base64_encode(file_get_contents($fpath));
                    $photos[] = 'data:' . $mime . ';base64,' . $data;
                }
            }
        }

        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $g->solicitud_id));
        $html = $this->load->view('garantias/pdf_view', ['g' => $g, 'garantias' => $garantias, 'photos' => $photos, 'solicitud' => $solicitud], true);

        // Debug mode: if ?debug=1 is present, return the HTML for inspection (no PDF render)
        if (isset($_GET['debug']) && $_GET['debug'] == '1') {
            // send as HTML so developer can inspect image src, etc.
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            exit;
        }

        // Prepare a writable temp directory for Dompdf
        $tempDir = FCPATH . 'tmp';
        if (! is_dir($tempDir)) @mkdir($tempDir, 0755, true);

        // Configure Dompdf options
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('tempDir', $tempDir);

            $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->load_html($html);
            // Allow optional paper size via query string: ?size=letter|legal|A4
            $reqSize = isset($_GET['size']) ? strtolower(trim($_GET['size'])) : 'letter';
            $allowed = array('letter', 'legal', 'a4');
            if (! in_array($reqSize, $allowed)) {
                $reqSize = 'letter';
            }
            $paper = ($reqSize === 'a4') ? 'A4' : $reqSize;
            $dompdf->set_paper($paper, 'portrait');
        $dompdf->render();
        $dompdf->stream('formato_garantia_'.$g->solicitud_id.'.pdf', ['Attachment' => 1]);
    }

    /**
     * Generate PDF (or debug HTML) by solicitud_id instead of garantia id.
     * Useful to quickly debug all photos for a solicitud.
     */
    public function pdf_by_solicitud($solicitud_id)
    {
        if (! $solicitud_id) show_404();
        $garantias = $this->Garantia_model->get_all_by_solicitud($solicitud_id);
        if (empty($garantias)) show_404();

        // take first garantia as representative for header
        $g = $garantias[0];

        // collect photos similarly to pdf()
        $photos = [];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($this->db->table_exists('tb_garantias_fotos')) {
            $rows = $this->db->select('filename')->from('tb_garantias_fotos')->where('solicitud_id', $solicitud_id)->order_by('created_at')->get()->result();
            foreach ($rows as $r) {
                if (empty($r->filename)) continue;
                $rel = $r->filename;
                $abs = FCPATH . ltrim($rel, '/\\');
                if (file_exists($abs)) {
                    $mime = finfo_file($finfo, $abs) ?: 'image/jpeg';
                    $data = base64_encode(file_get_contents($abs));
                    $photos[] = 'data:' . $mime . ';base64,' . $data;
                } else {
                    $photos[] = base_url($rel);
                }
            }
        } else {
            foreach ($garantias as $row) {
                for ($i = 1; $i <= 5; $i++) {
                    $col = 'foto' . $i;
                    if (empty($row->$col)) continue;
                    $rel = $row->$col;
                    $abs = FCPATH . ltrim($rel, '/\\');
                    if (file_exists($abs)) {
                        $mime = finfo_file($finfo, $abs) ?: 'image/jpeg';
                        $data = base64_encode(file_get_contents($abs));
                        $photos[] = 'data:' . $mime . ';base64,' . $data;
                    } else {
                        $photos[] = base_url($rel);
                    }
                }
            }
        }
        finfo_close($finfo);

        // Obtener la tasa de cambio actual
        $tasa_cambio = $this->TasaCambio_model->get_tasa_actual();

        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $solicitud_id));
        $html = $this->load->view('garantias/pdf_view', ['g' => $g, 'garantias' => $garantias, 'photos' => $photos, 'tasa_cambio' => $tasa_cambio, 'solicitud' => $solicitud], true);

        // extended debug: include upload dir listing when requested
        if (isset($_GET['debug_json']) && $_GET['debug_json'] == '1') {
            $upload_dir = FCPATH . 'uploads/garantias/solicitud_' . $solicitud_id . '/';
            $scan = [];
            if (is_dir($upload_dir)) {
                $files = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                foreach ($files as $f) $scan[] = basename($f);
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['photos' => $photos, 'upload_dir' => $upload_dir, 'upload_dir_exists' => is_dir($upload_dir), 'files' => $scan]);
            exit;
        }
        if (isset($_GET['debug']) && $_GET['debug'] == '1') {
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            exit;
        }

        // Ensure Dompdf is available. Prefer the bundled autoload first (more deterministic in this env),
        // then fall back to composer/vendor and third_party locations.
        if (file_exists(FCPATH . 'dompdf/autoload.inc.php')) {
            require_once FCPATH . 'dompdf/autoload.inc.php';
        } elseif (file_exists(APPPATH . 'third_party/dompdf/autoload.inc.php')) {
            require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
        }
        if (file_exists(FCPATH . 'vendor/autoload.php')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
        if (! class_exists('\Dompdf\Dompdf')) {
            show_error('Dompdf no está disponible. Instale la librería (composer install) o coloque la carpeta /dompdf en el proyecto.');
        }

        // Prepare temp dir and options for Dompdf
        $tempDir = FCPATH . 'tmp';
        if (! is_dir($tempDir)) @mkdir($tempDir, 0755, true);
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('tempDir', $tempDir);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->load_html($html);
        // Allow optional paper size via query string: ?size=letter|legal|A4
        $reqSize = isset($_GET['size']) ? strtolower(trim($_GET['size'])) : 'letter';
        $allowed = array('letter', 'legal', 'a4');
        if (! in_array($reqSize, $allowed)) {
            $reqSize = 'letter';
        }
        $paper = ($reqSize === 'a4') ? 'A4' : $reqSize;
        $dompdf->set_paper($paper, 'landscape');
        $dompdf->render();
        $dompdf->stream('formato_garantia_'.$solicitud_id.'.pdf', ['Attachment' => 1]);
    }

    /**
     * Guarda una verificación (comentario + usuario + hasta 5 fotos) para una garantía
     * Espera: POST {garantia_id, solicitud_id, verificador_usuario, comentario} y archivos ver_foto1..5
     * Responde JSON {success:bool, message:string}
     */
    public function save_verificacion()
    {
        // Always return JSON from this endpoint
        header('Content-Type: application/json; charset=utf-8');

        // Prevent CodeIgniter from rendering DB debug HTML on DB errors
        if (isset($this->db) && is_object($this->db)) {
            $this->db->db_debug = FALSE;
        }

        $post = $this->input->post();
        $garantia_id = isset($post['garantia_id']) ? intval($post['garantia_id']) : null;
        $solicitud_id = isset($post['solicitud_id']) ? intval($post['solicitud_id']) : null;
        // Use the logged-in user as verificador (remove free-text Usuario field)
        $usuario = null;
        if (isset($this->ion_auth) && $this->ion_auth->logged_in()) {
            try { $u = $this->ion_auth->user()->row(); $usuario = isset($u->username) ? $u->username : (isset($u->first_name) ? $u->first_name : null); } catch (Exception $e) { $usuario = null; }
        }

        $comentario_input = isset($post['comentario']) ? $post['comentario'] : null;
        $estado_input = isset($post['estado_aprobacion']) ? $post['estado_aprobacion'] : null;
        $nombre_input = isset($post['nombre_garantia']) ? $post['nombre_garantia'] : null;
        
        $main_garantia_id = $garantia_id;
        $comentarios = array();
        $estados = array();
        $nombres = array();
        
        if (is_array($comentario_input)) {
            foreach ($comentario_input as $key => $value) {
                $key = intval($key);
                if ($key) {
                    $comentarios[$key] = trim((string)$value);
                }
            }
        } else {
            if ($main_garantia_id) {
                $comentarios[$main_garantia_id] = trim((string)$comentario_input);
            }
        }
        
        if (is_array($estado_input)) {
            foreach ($estado_input as $key => $value) {
                $key = intval($key);
                if ($key) {
                    $estados[$key] = trim((string)$value);
                }
            }
        } else {
            if ($main_garantia_id) {
                $estados[$main_garantia_id] = trim((string)$estado_input);
            }
        }
        
        if (is_array($nombre_input)) {
            foreach ($nombre_input as $key => $value) {
                $key = intval($key);
                if ($key) {
                    $nombres[$key] = trim((string)$value);
                }
            }
        } else {
            if ($main_garantia_id) {
                $nombres[$main_garantia_id] = trim((string)$nombre_input);
            }
        }

        // prevent multiple verifications for the same garantia when no valid target is present
        if (! $main_garantia_id && empty($comentarios)) {
            echo json_encode(['success' => false, 'message' => 'Falta el ID de la garantía.']);
            return;
        }

        // prepare upload directory for possible photo uploads
        $base_dir = FCPATH . 'uploads/garantias/';
        if ($solicitud_id) {
            $base_dir .= 'solicitud_' . $solicitud_id . '/verificaciones/';
        } else {
            $base_dir .= 'verificaciones/';
        }
        if (! is_dir($base_dir)) {
            mkdir($base_dir, 0755, true);
        }

        $uploadedPhotos = array();
        for ($i = 1; $i <= 5; $i++) {
            $field = 'ver_foto' . $i;
            if (! empty($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
                $_FILES['upload_file'] = $_FILES[$field];
                $config = [];
                $config['upload_path'] = $base_dir;
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 4096;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if ($this->upload->do_upload('upload_file')) {
                    $info = $this->upload->data();
                    $uploadedPhotos['foto' . $i] = 'uploads/garantias/' . ($solicitud_id ? 'solicitud_' . $solicitud_id . '/verificaciones/' : 'verificaciones/') . $info['file_name'];
                } else {
                    $error = $this->upload->display_errors('', '');
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Error al subir archivo: ' . strip_tags($error)]);
                    return;
                }
            }
        }

        $errors = array();
        $lastId = null;
        $baseVerificationId = null;

        // PASO 1: Crear o actualizar registro base (sin garantia_id) para gestionar fotos
        if (! empty($uploadedPhotos) || ! empty($comentarios)) {
            try {
                // Buscar registro base existente (garantia_id = NULL)
                $baseRecords = $this->db->get_where('tb_garantias_verificaciones', [
                    'garantia_id' => null,
                    'solicitud_id' => $solicitud_id
                ])->result();
                $baseRecord = !empty($baseRecords) ? $baseRecords[0] : null;

                $baseData = [
                    'solicitud_id' => $solicitud_id,
                    'verificador_usuario' => $usuario,
                ];

                // Agregar fotos al registro base
                if (! empty($uploadedPhotos)) {
                    $baseData = array_merge($baseData, $uploadedPhotos);
                }

                // Si existe registro base, actualizar; si no, crear
                if ($baseRecord) {
                    // Actualizar: mantener fotos antiguas si no se proporcionan nuevas
                    for ($i = 1; $i <= 5; $i++) {
                        $field = 'foto' . $i;
                        if (empty($uploadedPhotos[$field]) && ! empty($baseRecord->$field)) {
                            $baseData[$field] = $baseRecord->$field;
                        }
                    }
                    $this->Garantia_verificacion_model->update($baseRecord->id, $baseData);
                    $baseVerificationId = $baseRecord->id;
                } else {
                    // Crear nuevo registro base
                    $baseVerificationId = $this->Garantia_verificacion_model->insert($baseData);
                }

                if (! $baseVerificationId) {
                    $errors[] = 'No se pudo crear/actualizar el registro base de verificación.';
                }
            } catch (Exception $e) {
                $errors[] = 'Error al crear registro base: ' . $e->getMessage();
            }
        }

        // PASO 2: Crear o actualizar registros por garantía (sin fotos)
        foreach ($comentarios as $target_garantia_id => $comment) {
            try {
                $existing = $this->Garantia_verificacion_model->get_by_garantia($target_garantia_id);
                $existing_record = !empty($existing) ? $existing[0] : null;
            } catch (Exception $e) {
                $errors[] = 'Error al comprobar verificación previa para la garantía ' . $target_garantia_id . '. ' . $e->getMessage();
                continue;
            }

            $record = [
                'garantia_id' => $target_garantia_id,
                'solicitud_id' => $solicitud_id,
                'verificador_usuario' => $usuario,
                'comentario' => $comment,
            ];
            
            // Agregar nombre de garantía (solo si la columna existe)
            if (isset($nombres[$target_garantia_id]) && $this->db->field_exists('nombre_garantia', 'tb_garantias_verificaciones')) {
                $record['nombre_garantia'] = $nombres[$target_garantia_id];
            }

            // Agregar estado de aprobación (solo si la columna existe)
            if ($this->db->field_exists('estado_aprobacion', 'tb_garantias_verificaciones')) {
                if (isset($estados[$target_garantia_id])) {
                    $record['estado_aprobacion'] = $estados[$target_garantia_id];
                } else {
                    $record['estado_aprobacion'] = 'No aprobado';
                }
            }

            try {
                // Filtrar campos del record para evitar insertar columnas inexistentes en la tabla
                $tableFields = array();
                try {
                    $tableFields = $this->db->list_fields('tb_garantias_verificaciones');
                } catch (Exception $e) {
                    $tableFields = array();
                }
                if (!empty($tableFields)) {
                    $record = array_intersect_key($record, array_flip($tableFields));
                }
                
                if ($existing_record) {
                    // Actualizar: no incluir fotos en la actualización
                    $success = $this->Garantia_verificacion_model->update($existing_record->id, $record);
                    if ($success) {
                        $lastId = $existing_record->id;
                    } else {
                        $errors[] = 'No se pudo actualizar la verificación de la garantía ' . $target_garantia_id . '.';
                    }
                } else {
                    // Crear nuevo registro de garantía (sin fotos)
                    $insert_id = $this->Garantia_verificacion_model->insert($record);
                    if ($insert_id) {
                        $lastId = $insert_id;
                    } else {
                        $errors[] = 'No se pudo guardar la verificación de la garantía ' . $target_garantia_id . '.';
                    }
                }
            } catch (Exception $e) {
                $errors[] = 'Error interno para la garantía ' . $target_garantia_id . ': ' . $e->getMessage();
            }
        }

        if (! empty($errors)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Verificación guardada.', 'id' => $lastId ?: $baseVerificationId]);
    }

    /**
     * Devuelve datos de verificación existentes para una garantía
     * Retorna el registro de la garantía (comentario, estado, etc.) y las fotos desde el registro base
     */
    public function get_verificacion_ajax($garantia_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (! $garantia_id) {
            echo json_encode(['success' => false, 'message' => 'Falta el ID de la garantía.']);
            return;
        }
        try {
            // Obtener registro específico de la garantía (con comentario, estado, etc.)
            $records = $this->Garantia_verificacion_model->get_by_garantia($garantia_id);
            if (empty($records)) {
                echo json_encode(['success' => false, 'message' => 'No existe verificación para esta garantía.']);
                return;
            }
            $record = $records[0];

            // Obtener fotos desde el registro base (garantia_id = NULL) de la misma solicitud
            $photos = array();
            if (isset($record->solicitud_id)) {
                $baseRecords = $this->db->get_where('tb_garantias_verificaciones', [
                    'garantia_id' => null,
                    'solicitud_id' => $record->solicitud_id
                ])->result();
                
                if (!empty($baseRecords)) {
                    $baseRecord = $baseRecords[0];
                    for ($i = 1; $i <= 5; $i++) {
                        $field = 'foto' . $i;
                        if (! empty($baseRecord->$field)) {
                            $photos[] = array('field' => $field, 'url' => base_url($baseRecord->$field));
                        }
                    }
                }
            } else {
                // Fallback: buscar fotos en el mismo registro (para compatibilidad retroactiva)
                for ($i = 1; $i <= 5; $i++) {
                    $field = 'foto' . $i;
                    if (! empty($record->$field)) {
                        $photos[] = array('field' => $field, 'url' => base_url($record->$field));
                    }
                }
            }

            echo json_encode(['success' => true, 'verification' => $record, 'photos' => $photos]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al cargar verificación: ' . $e->getMessage()]);
        }
    }

    /**
     * Devuelve las garantías completas asociadas a una solicitud
     */
    public function get_garantias_by_solicitud_ajax($solicitud_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (! $solicitud_id) {
            echo json_encode(['success' => false, 'message' => 'Falta el ID de la solicitud.']);
            return;
        }
        try {
            $records = $this->Garantia_model->get_all_by_solicitud($solicitud_id);
            $garantias = array();
            foreach ($records as $record) {
                $garantias[] = array(
                    'id' => isset($record->id) ? intval($record->id) : null,
                    'nombre' => trim((string)($record->nombre ?? '')),
                );
            }
            echo json_encode(['success' => true, 'garantias' => $garantias]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al cargar garantías: ' . $e->getMessage()]);
        }
    }

    /**
     * Devuelve todas las verificaciones asociadas a una solicitud
     */
    public function get_verificaciones_by_solicitud_ajax($solicitud_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (! $solicitud_id) {
            echo json_encode(['success' => false, 'message' => 'Falta el ID de la solicitud.']);
            return;
        }
        try {
            $records = $this->Garantia_verificacion_model->get_by_solicitud($solicitud_id);
            $verifs = array();
            foreach ($records as $r) {
                $verifs[] = array(
                    'id' => isset($r->id) ? intval($r->id) : null,
                    'garantia_id' => isset($r->garantia_id) ? intval($r->garantia_id) : null,
                    'comentario' => isset($r->comentario) ? $r->comentario : null,
                    'estado_aprobacion' => isset($r->estado_aprobacion) ? $r->estado_aprobacion : null,
                    'nombre_garantia' => isset($r->nombre_garantia) ? $r->nombre_garantia : null,
                    'verificador_usuario' => isset($r->verificador_usuario) ? $r->verificador_usuario : null,
                    'created_at' => isset($r->created_at) ? $r->created_at : null,
                );
            }
            echo json_encode(['success' => true, 'verifications' => $verifs]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al cargar verificaciones: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: eliminar foto de verificación de una garantía
     */
    public function delete_verificacion_photo_ajax()
    {
        header('Content-Type: application/json; charset=utf-8');
        $garantia_id = intval($this->input->post('garantia_id'));
        $field = trim((string)$this->input->post('field'));
        $allowed_fields = array('foto1', 'foto2', 'foto3', 'foto4', 'foto5');

        if (! $garantia_id) {
            echo json_encode(['success' => false, 'message' => 'Falta el ID de la garantía.']);
            return;
        }
        if (! in_array($field, $allowed_fields, true)) {
            echo json_encode(['success' => false, 'message' => 'Campo de foto inválido.']);
            return;
        }

        try {
            // Obtener el registro de la garantía para acceder al solicitud_id
            $records = $this->Garantia_verificacion_model->get_by_garantia($garantia_id);
            if (empty($records)) {
                echo json_encode(['success' => false, 'message' => 'No existe verificación para esta garantía.']);
                return;
            }
            $record = $records[0];

            // Buscar el registro base (garantia_id = NULL) de la misma solicitud
            $baseRecords = $this->db->get_where('tb_garantias_verificaciones', [
                'garantia_id' => null,
                'solicitud_id' => $record->solicitud_id
            ])->result();

            if (empty($baseRecords)) {
                echo json_encode(['success' => false, 'message' => 'No se encontró el registro base de verificación.']);
                return;
            }

            $baseRecord = $baseRecords[0];
            if (empty($baseRecord->$field)) {
                echo json_encode(['success' => false, 'message' => 'No se encontró la foto en el registro.']);
                return;
            }

            $file_rel = $baseRecord->$field;
            $filepath = FCPATH . ltrim($file_rel, '/\\');
            $updated = $this->Garantia_verificacion_model->update($baseRecord->id, array($field => null));
            if (! $updated) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la foto de la verificación.']);
                return;
            }

            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            echo json_encode(['success' => true, 'message' => 'Foto eliminada.']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la foto: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: obtener información de la solicitud (código y cliente)
     */
    public function get_solicitud_info_ajax($solicitud_id = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (! $solicitud_id) {
            echo json_encode(['success' => false, 'message' => 'Falta el ID de la solicitud.']);
            return;
        }

        try {
            $solicitud_id = intval($solicitud_id);
            
            // Buscar la solicitud (tabla tb_solicitudes o según la estructura del proyecto)
            $query = $this->db->select('id, nombre_cliente, apellido_cliente, cedula, codigo, fecha_solicitud')
                ->from('tb_solicitudes')
                ->where('id', $solicitud_id)
                ->limit(1)
                ->get();
            
            if ($query->num_rows() == 0) {
                echo json_encode(['success' => false, 'message' => 'No se encontró la solicitud.']);
                return;
            }

            $solicitud = $query->row();
            
            // Construir el código de solicitud
            $codigo_solicitud = '';
            if (isset($solicitud->codigo) && !empty($solicitud->codigo)) {
                $codigo_solicitud = $solicitud->codigo;
            } else {
                // Formato alternativo si no existe el campo codigo
                $codigo_solicitud = 'SOL-' . str_pad($solicitud->id, 4, '0', STR_PAD_LEFT);
            }
            
            // Construir el nombre del cliente
            $nombre_cliente = '';
            if (isset($solicitud->nombre_cliente) && !empty($solicitud->nombre_cliente)) {
                $nombre_cliente = trim($solicitud->nombre_cliente);
                if (isset($solicitud->apellido_cliente) && !empty($solicitud->apellido_cliente)) {
                    $nombre_cliente .= ' ' . trim($solicitud->apellido_cliente);
                }
            }

            echo json_encode([
                'success' => true,
                'codigo_solicitud' => $codigo_solicitud,
                'nombre_cliente' => $nombre_cliente,
                'cedula' => isset($solicitud->cedula) ? $solicitud->cedula : null
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al cargar información de la solicitud: ' . $e->getMessage()]);
        }
    }

    /**
     * Descargar la verificación de una garantía en PDF
     */
    public function download_verificacion($garantia_id = null)
    {
        if (! $garantia_id) show_404();
        $verificaciones = $this->Garantia_verificacion_model->get_by_garantia($garantia_id);
        if (empty($verificaciones)) {
            $this->session->set_flashdata('error', 'No se encontró verificación para esta garantía.');
            redirect('garantias');
        }

        // take the first (there should be only one)
        $v = $verificaciones[0];

        // collect image data URIs from the base verification record for this solicitud (foto1..foto5)
        $imgs = [];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (! empty($v->solicitud_id)) {
            $baseRecords = $this->db->get_where('tb_garantias_verificaciones', [
                'garantia_id' => null,
                'solicitud_id' => $v->solicitud_id
            ])->result();
            if (! empty($baseRecords)) {
                $base = $baseRecords[0];
                for ($i = 1; $i <= 5; $i++) {
                    $col = 'foto' . $i;
                    if (empty($base->$col)) continue;
                    $rel = $base->$col;
                    $abs = FCPATH . ltrim($rel, '/\\');
                    if (file_exists($abs)) {
                        $mime = finfo_file($finfo, $abs) ?: 'image/jpeg';
                        $data = base64_encode(file_get_contents($abs));
                        $imgs[] = 'data:' . $mime . ';base64,' . $data;
                    } else {
                        $imgs[] = base_url($rel);
                    }
                }
            }
        }
        finfo_close($finfo);

        $solicitud = null;
        $verificaciones = array();
        if (! empty($v->solicitud_id)) {
            $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $v->solicitud_id));
            $all_verificaciones = $this->Garantia_verificacion_model->get_by_solicitud($v->solicitud_id);
            foreach ($all_verificaciones as $record) {
                if (! empty($record->garantia_id)) {
                    $verificaciones[] = $record;
                }
            }
        }

        $html = $this->load->view('garantias/verificacion_pdf', [
            'v' => $v,
            'imgs' => $imgs,
            'solicitud' => $solicitud,
            'verificaciones' => $verificaciones,
        ], true);

        // Ensure Dompdf available
        if (file_exists(FCPATH . 'dompdf/autoload.inc.php')) {
            require_once FCPATH . 'dompdf/autoload.inc.php';
        } elseif (file_exists(APPPATH . 'third_party/dompdf/autoload.inc.php')) {
            require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
        } elseif (file_exists(FCPATH . 'vendor/autoload.php')) {
            require_once FCPATH . 'vendor/autoload.php';
        }
        if (! class_exists('\Dompdf\Dompdf')) {
            show_error('Dompdf no está disponible.');
        }

        $tempDir = FCPATH . 'tmp'; if (! is_dir($tempDir)) @mkdir($tempDir, 0755, true);
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('tempDir', $tempDir);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->load_html($html);
        // Allow optional paper size via query string: ?size=letter|legal|A4
        $reqSize = isset($_GET['size']) ? strtolower(trim($_GET['size'])) : 'letter';
        $allowed = array('letter', 'legal', 'a4');
        if (! in_array($reqSize, $allowed)) {
            $reqSize = 'letter';
        }
        $paper = ($reqSize === 'a4') ? 'A4' : $reqSize;
        $dompdf->set_paper($paper, 'portrait');
        $dompdf->render();
        $dompdf->stream('verificacion_garantia_'.$garantia_id.'.pdf', ['Attachment' => 1]);
    }

    /**
     * Vista moderna de Avalúo de Garantías
     */
    public function avaluo($solicitud_id = null)
    {
        if (! $solicitud_id) show_404();

        // Obtener datos de la solicitud
        $solicitud = $this->core_model->get_by_id('tb_solicitudes', array('idsolicitud' => $solicitud_id));
        if (! $solicitud) show_404();

        // Obtener garantías asociadas
        $garantias = $this->Garantia_model->get_all_by_solicitud($solicitud_id);

        // Ajusta estos campos según tu modelo de solicitud
        $data = [
            'solicitud_id'    => $solicitud_id,
            'cliente_nombre'  => isset($solicitud->cliente_nombre) ? $solicitud->cliente_nombre : (isset($solicitud->cliente) ? $solicitud->cliente : ''),
            'fecha_solicitud' => isset($solicitud->fecha_solicitud) ? $solicitud->fecha_solicitud : (isset($solicitud->fecha) ? $solicitud->fecha : ''),
            'garantias'       => $garantias,
            'error_pdf'       => $this->session->flashdata('error_pdf')
        ];

        $this->load->view('garantias/avaluo', $data);
    }
}
