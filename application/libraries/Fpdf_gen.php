<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . 'third_party/fpdf/fpdf.php';
class Fpdf_gen
{
	public function __construct()
	{
		require 'fpdf/Pdf.php';
		$pdf = new FPDF();
		$pdf->AddPage();
		$CI = &get_instance();
		$CI->Pdf = $pdf;
	}
}
