<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Settings_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GetSettingDetails()
	{
		$this->db->select("*");
		$this->db->from('mSettings');
		//$this->db->where('Active',STATUS_ONE);
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('CustomerUID',$this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->result();
	}

}
?>

