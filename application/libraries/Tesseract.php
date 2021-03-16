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

class Tesseract {

	function __construct()
	{
		
	}

	function OCR($file)
	{
		if (file_exists($file) && is_file($file)) {
			return (new TesseractOCR($file))->run();
		}
		return false;
	}

}





