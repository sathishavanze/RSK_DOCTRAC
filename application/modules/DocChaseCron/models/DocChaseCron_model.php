<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocChaseCron_model extends MY_Model { 
	function __construct() { 
		parent::__construct();
	}

	public function getWelcomeDetails(){
	  	$status = $this->config->item('Workflows')['WelcomeCall'];
	    $this->db->select('*');
	    $this->db->from('tOrderAssignments');
	    $this->db->where('tOrderAssignments.WorkflowModuleUID',$status);
		$this->db->where('tOrderAssignments.WorkflowStatus',5);
	    $this->db->group_by('tOrderAssignments.OrderUID');
	    $this->db->order_by('tOrderAssignments.OrderUID');
	    $Workflowstatus = $this->db->get()->result();
	    return $Workflowstatus;
	}

	public function getDocWaitingList($OrderUID){
		$status = $this->config->item('Workflows')['WelcomeCall'];
		$this->db->select('*');
		$this->db->from('tOrderWorkflows');
		$this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
		$this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['WelcomeCall']);
		$CheckWorkflow = $this->db->get()->row();
		if ($CheckWorkflow) {
			$this->db->select('*');
			$this->db->from('tOrderAssignments');
			$this->db->where('tOrderAssignments.OrderUID',$OrderUID);
			$this->db->where('tOrderAssignments.WorkflowModuleUID',$status);
			$this->db->where('tOrderAssignments.WorkflowStatus',5);
			$this->db->order_by('tOrderAssignments.OrderUID');
			$Workflowstatus = $this->db->get()->row();
			return $Workflowstatus;
		} else{
			return '-';exit();
		}
	}

	public function getCheckListAnswers($OrderUID,$WorkflowModuleUID){
		$result = '';
		$result =  $this->db->select('Answer,IsChaseSend,FileUploaded,Comments,OrderUID')->from('tDocumentCheckList')->where(array('WorkflowUID'=>$WorkflowModuleUID,'OrderUID'=>$OrderUID,'Answer'=>'Problem Identified'))->get()->row();
		return $result;
	}

	public function UpdateCheckListAnswers($OrderUID,$WorkflowModuleUID){
		$fieldArray = array("IsChaseSend"=> 'YES');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('WorkflowUID',$WorkflowModuleUID);
		$this->db->where('Answer','Problem Identified');
		$res = $this->db->update('tDocumentCheckList',$fieldArray);
		return $res;
	}

	public function insertNotes($data) {
		$this->db->insert('tNotes',$data);
		return true;
	}

	public function getDocChaseOrders($OrderUID){
		$result = '';
		$result =  $this->db->query('select * from tOrderDocChase where OrderUID='.$OrderUID.' and ClearedByUserUID=""');
		return $result->row();
	}	
}
?>

