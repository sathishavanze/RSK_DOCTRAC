<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Notes_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}
	function insertNotes($post)
	{
		$WorkflowModuleUID = $this->input->post('Module');
		$noteArray = array('Parking','Others');
		if(in_array($WorkflowModuleUID, $noteArray ) == TRUE){
			$WorkflowModuleUID = 0;
			$Module = $this->input->post('Module');
		} else {			
			$WorkflowArrays =$this->config->item('Workflows');
			$Module = array_search($WorkflowModuleUID, $WorkflowArrays);
		}

		$data = array('OrderUID'=>$this->input->post('OrderUID'),
			'Description'=>$this->input->post('Description'),
			'Module'=>$Module,
			'WorkflowUID'=>$WorkflowModuleUID,
			'CreatedByUserUID'=>$this->loggedid,
			'CreateDateTime'=>date("Y/m/d H:i:s"));
		$this->db->insert('tNotes',$data);
	}
	function getNotes($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tNotes');
		$this->db->join('mUsers','mUsers.UserUID = tNotes.CreatedByUserUID');
		$this->db->where('OrderUID',$OrderUID);
		return $this->db->get()->result();
	}

	
}
?>
