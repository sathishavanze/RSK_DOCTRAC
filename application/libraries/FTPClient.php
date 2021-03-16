<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

require_once(APPPATH . 'vendor/autoload.php');
use \phpseclib\Net\SFTP;

class FTPClient
{
	$sftp = new SFTP('domain');
}