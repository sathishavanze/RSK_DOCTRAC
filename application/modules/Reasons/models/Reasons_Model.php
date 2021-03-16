<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Reasons_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function AddingReasons($post)
	{
		
		$AddingReasons=array('ReasonName'=>$post['ReasonsName'],'QueueUID'=>$post['QueueName'],'Active'=>$post['Active']);
		$this->db->insert('mReasons',$AddingReasons);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function UpdateReasons($post)
	{
		$UpdateReasons=array('ReasonName'=>$post['ReasonName'],'QueueUID'=>$post['QueueUID'],'Active'=>$post['Active']);
		
		$this->db->where(array('ReasonUID' => $post['ReasonUID']));
		$this->db->update('mReasons',$UpdateReasons);  
		
		return 1;
		
	}

	function GetReasonsDetails(){
		$this->db->select('mQueues.QueueName, mReasons.ReasonName, mReasons.Active, mReasons.ReasonUID,mWorkFlowModules.WorkflowModuleName' )
		->from('mQueues')
		->join('mReasons', 'mQueues.QueueUID = mReasons.QueueUID');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID', 'left');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->result();
	}

	function GetQueueName(){
		$this->db->select('mQueues.QueueUID, mQueues.QueueName,mWorkFlowModules.WorkflowModuleName' )->from('mQueues');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID', 'left');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->result();
	}
}
?>

