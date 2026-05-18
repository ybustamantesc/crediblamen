<?php

defined('BASEPATH') or exit('Acción no permitida');
require_once('./dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Caja extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('login');
        }
        $this->load->model('caja_model');
    }

    public function index()
    {
        $data = array(
            'titulo' => 'Administrar Caja',
            'subtitulo' => 'Abrir, Cerrar, Buscar.',
            'icono' => 'fas fa-box',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js',
                'js/caja/caja.js'
            ),
            'cajas' => $this->core_model->get_all('tb_caja'),
            'datos_caja' => $this->caja_model->getDatosCaja()
        );
        $this->load->view('layout/header', $data);
        $this->load->view('caja/index');
        $this->load->view('layout/footer');
    }

    public function core($caja_id = NULL)
    {
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('info', 'No tienes permiso para editar o registrar.');
            redirect($this->router->fetch_class());
        }
        if (!$caja_id) {
            #Registrar
            $this->form_validation->set_rules('monto_apertura', 'Monto Apertura', 'trim|required');
            if ($this->form_validation->run()) {
                $data = elements(
                    array(
                        'monto_apertura'
                    ),
                    $this->input->post()
                );
                $data["fecha_apertura"] = date("Y-m-d H:i:s");
                $data = html_escape($data);
                $this->core_model->insert('tb_caja', $data);
                redirect($this->router->fetch_class());
            } else {
                #Error de validacion.
                $data = array(
                    'titulo' => 'Abrir Caja',
                    'subtitulo' => 'Ingrese los datos solicitados',
                    'icono_view' => 'fas fa-university ',
                    'scripts' => array(
                        'plugins/jQueryMask/jquery.mask.min.js'
                    ),
                );
                $this->load->view('layout/header', $data);
                $this->load->view('caja/core');
                $this->load->view('layout/footer');
            }
        } else {
            #Editar
            if (!$this->core_model->get_by_id('tb_caja', array('idcaja' => $caja_id))) {
                $this->session->set_flashdata('error', 'Registro no encontrado.');
                redirect($this->router->fetch_class());
            } else {
                $this->form_validation->set_rules('monto_cierre', 'Monto Cierre', 'trim|required');
                if ($this->form_validation->run()) {
                    $data = elements(
                        array(
                            'fecha_cierre',
                            'monto_cierre',
                            'estado'
                        ),
                        $this->input->post()
                    );
                    $data["fecha_cierre"] = date("Y-m-d H:i:s");
                    $data["estado"] = 0;
                    $data = html_escape($data);
                    $this->core_model->update('tb_caja', $data, array('idcaja' => $caja_id));
                    redirect($this->router->fetch_class());
                } else {
                    #Error de validacion.
                    $data = array(
                        'titulo' => 'Cerrar Caja',
                        'subtitulo' => 'Ingrese los datos solicitados',
                        'icono_view' => 'fas fa-university ',
                        'scripts' => array(
                            'plugins/mask/jquery.mask.min.js'
                        ),
                        'caja' => $this->core_model->get_by_id('tb_caja', array('idcaja' => $caja_id)),
                        'caja_detalle' => $this->core_model->get_by_id_all('tb_caja_movimiento', array('idcaja' => $caja_id)),
                    );
                    $this->load->view('layout/header', $data);
                    $this->load->view('caja/core');
                    $this->load->view('layout/footer');
                }
            }
        }
    }

    public function registrar()
    {
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('info', 'No tienes permiso para editar o registrar.');
            redirect($this->router->fetch_class());
        }

        $this->form_validation->set_rules('tipo_movimiento', 'Tipo Movimiento', 'trim|required');
        $this->form_validation->set_rules('descripcion_movimiento', 'Motivo Movimiento', 'trim|required');
        $this->form_validation->set_rules('monto_movimiento', 'Monto Movimiento', 'trim|required');
        $this->form_validation->set_rules('forma_pago', 'Forma de Pago', 'trim|required');
        if ($this->form_validation->run()) {
            $data = elements(
                array(
                    'tipo_movimiento',
                    'descripcion_movimiento',
                    'monto_movimiento',
                    'forma_pago',
                    'tipo_doc',
                    'numero_doc'
                ),
                $this->input->post()
            );
            $fechActual = date("Y-m-d");
            $caja = $this->core_model->get_by_id("tb_caja", array("DATE(fecha_apertura)" => $fechActual));
            $idcaja = $caja->idcaja;
            $data["idcaja"] = $idcaja;
            $data["fecha_movimiento"] = date("Y-m-d H:i:s");
            $data = html_escape($data);
            $this->core_model->insert('tb_caja_movimiento', $data);
            redirect($this->router->fetch_class());
        } else {
            #Error de validacion.
            $data = array(
                'titulo' => 'Abrir Caja',
                'subtitulo' => 'Ingrese los datos solicitados',
                'icono_view' => 'fas fa-university ',
                'scripts' => array(
                    'plugins/mask/jquery.mask.min.js'
                ),
            );
            $this->load->view('layout/header', $data);
            $this->load->view('caja/core');
            $this->load->view('layout/footer');
        }
    }

    public function movimiento()
    {
        $data = array(
            'titulo' => 'Movimiento de Caja',
            'subtitulo' => 'Registrar',
            'icono' => 'fas fa-university',
            'styles' => array(
                'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'plugins/datatables.net/js/jquery.dataTables.min.js',
                'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
                'plugins/datatables.net/js/activaDatatable.js',
                'js/caja/caja.js'
            ),
            'cajas' => $this->core_model->get_all('tb_caja')
        );
        $this->load->view('layout/header', $data);
        $this->load->view('caja/movimiento');
        $this->load->view('layout/footer');
    }

    public function check_banco($nombre)
    {
        $id = $this->input->post('id');
        if ($this->core_model->get_by_id('tb_bancos', array('nombre' => $nombre, 'id!=' => $id))) {
            $this->form_validation->set_message('check_banco', 'Este Nombre de Banco ya existe');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function del($id = NULL)
    {
        if (!$this->ion_auth->is_admin()) {
            $this->session->set_flashdata('info', 'No tienes permiso para eliminar.');
            redirect($this->router->fetch_class());
        }
        if (!$id || !$this->core_model->get_by_id('tb_bancos', array('id' => $id))) {
            $this->session->set_flashdata('error', 'Banco no encontrado.');
            redirect($this->router->fetch_class());
        }
        if ($this->core_model->get_by_id('tb_bancos', array('id' => $id, 'estado' => 1))) {
            $this->session->set_flashdata('error', 'Este Banco tiene como Estado Activo no puede se eliminado.');
            redirect($this->router->fetch_class());
        }
        $this->core_model->delete('tb_bancos', array('id' => $id));
        redirect($this->router->fetch_class());
    }

    public function validarCaja()
    {
        // Si la configuración global indica que no usamos caja, omitir validación.
        $usar_caja = $this->config->item('usar_caja');
        if ($usar_caja === FALSE) {
            echo json_encode(1);
            return;
        }

        $data = $this->caja_model->validar_caja();
        if (count($data) > 0) {
            $contar = 1; //La caja está abierta
        } else {
            $contar = 0; //La caja está cerrada
        }
        echo json_encode($contar);
    }

    public function pdfcortefecha($idcaja)
    {
        $caja = $this->core_model->get_by_id("tb_caja", array("idcaja" => $idcaja));
        $ingresos = $this->core_model->get_by_id_all("tb_caja_movimiento", array("idcaja" => $idcaja, 'tipo_movimiento' => 1));
        $gastos = $this->core_model->get_by_id_all("tb_caja_movimiento", array("idcaja" => $idcaja, 'tipo_movimiento' => 0));
        $file_name = "CORTE DE CAJA DE DÍA ";
        $data = array(
            'ingresos' => $ingresos,
            'caja' => $caja,
            'gastos' => $gastos,
            'titulo' => $file_name
        );
        $dompdf = new Dompdf();
        $html = $this->load->view('caja/pdf_corte_caja_dia', $data, TRUE);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=documento.pdf");
        echo $dompdf->output();
    }
}
