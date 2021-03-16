<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class History_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}
	
	function getWithdrawelDetails($OrderUID)
	{
		$this->db->select('tOrderWithdrawal.*,a.ReasonName as RaisedReasonName,b.ReasonName as ClearedReasonName,c.UserName as RaisedUserName, d.UserName as ClearedUserName');
		$this->db->from('tOrderWithdrawal');
		$this->db->join('mUsers c','c.UserUID = tOrderWithdrawal.RaisedByUserUID','LEFT');
		$this->db->join('mUsers d','d.UserUID = tOrderWithdrawal.ClearedByUserUID','LEFT');
		$this->db->join('mReasons a','a.ReasonUID = tOrderWithdrawal.ReasonUID','LEFT');
		$this->db->join('mReasons b','b.ReasonUID = tOrderWithdrawal.ClearedReasonUID','LEFT');
		$this->db->where('OrderUID',$OrderUID);
		return $this->db->get()->result();
	}
	function getDocChaseDetails($OrderUID)
	{
		$this->db->select('(SELECT count(*) from tDocumentCheckList LEFT JOIN mWorkFlowModules w on w.WorkflowModuleUID = tDocumentCheckList.WorkflowUID  WHERE OrderUID = '.$OrderUID.' and w.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID and Answer = "Problem Identified" and IsChaseSend = "YES") as QuestionCount,tOrderDocChase.*,a.ReasonName as RaisedReasonName,b.ReasonName as ClearedReasonName,c.UserName as RaisedUserName, d.UserName as ClearedUserName,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName');
		$this->db->from('tOrderDocChase');
		$this->db->join('mUsers c','c.UserUID = tOrderDocChase.RaisedByUserUID','LEFT');
		$this->db->join('mUsers d','d.UserUID = tOrderDocChase.ClearedByUserUID','LEFT');
		$this->db->join('mReasons a','a.ReasonUID = tOrderDocChase.ReasonUID','LEFT');
		$this->db->join('mReasons b','b.ReasonUID = tOrderDocChase.ClearedReasonUID','LEFT');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderDocChase.WorkflowModuleUID','LEFT');
		$this->db->where('OrderUID',$OrderUID);
		return $this->db->get()->result();
	}
	function getFollowUpDetails($OrderUID)
	{
		$this->db->select('tOrderFollowUp.*,a.ReasonName as RaisedReasonName,b.ReasonName as ClearedReasonName,c.UserName as RaisedUserName, d.UserName as ClearedUserName,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName,mQueues.QueueName');
		$this->db->from('tOrderFollowUp');
		$this->db->join('mQueues','mQueues.QueueUID = tOrderFollowUp.QueueUID');
		$this->db->join('mUsers c','c.UserUID = tOrderFollowUp.RaisedByUserUID','LEFT');
		$this->db->join('mUsers d','d.UserUID = tOrderFollowUp.ClearedByUserUID','LEFT');
		$this->db->join('mReasons a','a.ReasonUID = tOrderFollowUp.ReasonUID','LEFT');
		$this->db->join('mReasons b','b.ReasonUID = tOrderFollowUp.ClearedReasonUID','LEFT');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderFollowUp.WorkflowModuleUID','LEFT');
		$this->db->where('OrderUID',$OrderUID);
		return $this->db->get()->result();
	}
}
?>
