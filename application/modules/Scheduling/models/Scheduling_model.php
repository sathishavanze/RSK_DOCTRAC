<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Scheduling_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
		$this->WorkflowUID = $this->config->item('Workflows')['Scheduling'];
	}

	
}
?>

