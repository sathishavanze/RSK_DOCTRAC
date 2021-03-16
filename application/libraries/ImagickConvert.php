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

class ImagickConvert {

	protected $X_Resolution;
	protected $Y_Resolution;
	protected $image_format;
	protected $sourcefilepath;
	protected $image;


	function __construct()
	{
		
	}

	function init($config)
	{
		$X_Resolution = $config['Height'];
		$Y_Resolution = $config['Width'];
		$image_format = $config['Format'];
		$sourcefilepath = $config['SourceFile'];

		if (file_exists($sourcefilepath)) {
			$image = new Imagick($sourcefilepath);
			print_r($image);
			$image->setResolution( 300, 300 );
			$image->setImageFormat( "png" );

			return true;			
		}
		return false;
	}

	function WriteImage($FileLocation)
	{

		$image->writeImage($FileLocation);
	}


}




