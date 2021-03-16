<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Durationmodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	//check cout of row
	function CheckWorkflowDurationExist($post) {
		if (isset($post['DurationUID'])) {
			$this->db->select('*');
			$this->db->from('mWorkflowDurations');
			$this->db->where((array('DurationUID'=>$post['DurationUID'],'ClientUID' => $this->parameters['DefaultClientUID'])));
			$this->db->where_not_in('DurationUID',$post['DurationUID']);
			$query = $this->db->get();
		} else {
			$query = $this->db->get_where('mWorkflowDurations', array('ClientUID' => $this->parameters['DefaultClientUID'],'WorkflowModuleUID'=>$this->input->post('WorkFlow')));
		}
		return $query->num_rows();
	}
	//insert duration in db
	function SaveMilestone($post)
	{
		$Active = isset($post['Active']) ? 1 : 0;
			$mileData = array(
			     
				'WorkflowModuleUID'  => $this->input->post('WorkFlow'), 
				'Hours'  => $this->input->post('Duration'), 
				'ClientUID' => $this->parameters['DefaultClientUID'],
				'Active' => $Active
			     
			   );
		      $result=$this->db->insert('mWorkflowDurations',$mileData);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}

	/* 
	*Updating the data of duration to the database 
	*
	*/
	function UpdateDuration($post){
		$Active = isset($post['Active']) ? 1 : 0;

		$Users = array('WorkflowModuleUID' => $post['WorkflowModuleUID'],'Hours' => $post['Duration'],'Active' => $Active);
		$this->db->where(array('DurationUID' => $post['DurationUID'],'ClientUID' => $this->parameters['DefaultClientUID']));
		$this->db->update('mWorkflowDurations',$Users);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	
/* 		$Users = array('WorkflowModuleUID' => $post['WorkflowModuleUID'],'DurationUID' => $post['Duration'],'Active' => $Active);
		$this->db->where(array('DurationUID' => $post['DurationUID'],'ClientUID' => $this->parameters['DefaultClientUID']));
		$this->db->update('mWorkflowDurations',$Users);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		} */

	}
	//get workfolw list
	function GetWorkflowList($CustomerUID){
			
		$this->db->select ( '*' );
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->join ( 'mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID=mCustomerWorkflowModules.WorkflowModuleUID', 'inner' );
		$this->db->where('mCustomerWorkflowModules.CustomerUID' , $CustomerUID);
		$query = $this->db->get();
		return $query->result_array();
	}

	//get workflow uid and name
	function GetWorkflowUIDName(){
		$fetch=$this->GetWorkflowList($this->parameters['DefaultClientUID']);
		foreach($fetch as $key_fetch =>$value_fetch){
			$array[$value_fetch['WorkflowModuleUID']]=$value_fetch['WorkflowModuleName'];
		}
		return $array;
	}


}
?>

