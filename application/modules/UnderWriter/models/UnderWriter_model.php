<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class UnderWriter_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
		$this->WorkflowUID = $this->config->item('Workflows')['UnderWriter'];
	}

	
}
?>

