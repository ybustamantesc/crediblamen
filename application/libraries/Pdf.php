<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once('./dompdf/autoload.inc.php');

/*Esse trecho de código é preciso quando for hospedar*/

use Dompdf\Adapter\CPDF;
use Dompdf\Dompdf;
use Dompdf\Exception;

class Pdf
{

	function createPDF($html, $filename = '', $download = TRUE, $paper = 'A4', $orientation = 'portrait')
	{
		//        $dompdf = new dompdf\DOMPDF(); //Para localhost
		$dompdf = new Dompdf(); //Para hospedado
		// Configure options for better HTML5/CSS support and remote resources
		$options = $dompdf->getOptions();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isRemoteEnabled', true);
		$options->set('defaultFont', 'DejaVu Sans');
		$dompdf->setOptions($options);
		// Use loadHtml (newer API) to ensure proper parsing
		$dompdf->loadHtml($html);
		$dompdf->set_paper($paper, $orientation);
		try {
			$dompdf->render();
		} catch (Exception $e) {
			if (function_exists('log_message')) log_message('error', 'Dompdf render error: ' . $e->getMessage());
		}

		// Clean any previously sent output to avoid corrupting PDF stream
		while (ob_get_level() > 0) { ob_end_clean(); }

		// Stream the generated PDF
		$streamOpts = array('Attachment' => ($download ? 1 : 0));
		$dompdf->stream($filename . '.pdf', $streamOpts);
	}

	/**
	 * Save generated PDF to disk.
	 * @param string $html
	 * @param string $fullpath Absolute path where the PDF will be saved (including .pdf)
	 * @param string $paper
	 * @param string $orientation
	 * @return bool True on success
	 */
	public function savePDF($html, $fullpath, $paper = 'A4', $orientation = 'portrait')
	{
		$dompdf = new Dompdf();
		$options = $dompdf->getOptions();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isRemoteEnabled', true);
		$options->set('defaultFont', 'DejaVu Sans');
		$dompdf->setOptions($options);
		$dompdf->loadHtml($html);
		$dompdf->set_paper($paper, $orientation);
		try {
			$dompdf->render();
		} catch (Exception $e) {
			if (function_exists('log_message')) log_message('error', 'Dompdf save render error: ' . $e->getMessage());
		}
		$pdfOutput = $dompdf->output();
		// Ensure directory exists
		$dir = dirname($fullpath);
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		return (bool) file_put_contents($fullpath, $pdfOutput);
	}
}
