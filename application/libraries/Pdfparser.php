<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter PDF Library
 *
 * Generate PDF's in your CodeIgniter applications.
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Chris Harvey
 * @license			MIT License
 */

require_once APPPATH . "vendor/autoload.php";

class Pdfparser {

	function __construct()
	{
	}

	function getallPages($file)
	{
		$parser = new \Smalot\PdfParser\Parser();
		if (file_exists($file) && is_file($file)) {
			$pdf    = $parser->parseFile($file);			
			return $pdf->getText();
		}
		return false;
	}


	function getPageText($file, $pageno = 1)
	{
		$pages_text = [];
		$parser = new \Smalot\PdfParser\Parser();

		if (file_exists($file) && is_file($file)) {
			$pdf    = $parser->parseFile($file); 
			if($pdf!='')
			{$pages  = $pdf->getPages();
			// Loop over each page to extract text.
			foreach ($pages as $page) {
				$pages_text[] =  $page->getText();
			}
			return $pages_text;}else return false;
		}
		return false;
	}

	

}

