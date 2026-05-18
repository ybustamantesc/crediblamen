<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pdf
{

	public function __construct()
	{

		// include_once APPPATH.'third_party/Fpdf/Pdf.php'; 

		$pdf = new FPDF();
		$pdf->AddPage();

		$CI = &get_instance();
		$CI->Pdf = $pdf;
	}
}
