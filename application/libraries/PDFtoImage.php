<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class PDFtoImage
{

    public function __construct()
    {

    }

	public function convert_pdf_to_image($sourcefile, $prefix_filename, $destination_path){
		$directory = FCPATH;
		$source_path = escapeshellarg($sourcefile);
		$destination_path = escapeshellarg($destination_path);

		// check current OS
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

			$command = $directory."assets/gs/win/bin/gs.exe -sDEVICE=pngalpha -o " .$destination_path . $prefix_filename . "-%01d.png -r300 " .$source_path;
		}else{

			$command = "gs -sDEVICE=pngalpha -o " .$destination_path . $prefix_filename . "-%01d.png -r300 " .$source_path;
		}

		exec($command, $retArr, $retVal);

		if(empty($retVal)){
			return true;
		}
		else{
			return 'Error occured while converting the file using below command. <br>'.$command;
		}
	}

	function get_pdf_pages($filepath)
	{

		if (!file_exists($filepath)) {
			return 0;
		}
		// $filepath = escapeshellarg($filepath);

		// check current OS
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

			$command = FCPATH.'assets/gs/win/bin/gs.exe -q -dNODISPLAY -c "('.$filepath.') (r) file runpdfbegin pdfpagecount = quit"';
		}else{

			$command = 'gs -q -dNODISPLAY -c "('.$filepath.') (r) file runpdfbegin pdfpagecount = quit"';
		}

		return exec($command);


	}
}

?>