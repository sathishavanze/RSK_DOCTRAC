<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class WelcomeCall_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
		$this->WorkflowUID = $this->config->item('Workflows')['WelcomeCall'];
	}
	public function getcategorylist($OrderUID,$WorkflowModuleUID)
	{
		$OrderDetails = $this->db->select('CustomerUID')->from('tOrders')->where('OrderUID',$OrderUID)->get()->row();
		return $this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType','mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID')->where(array('CustomerUID'=>$OrderDetails->CustomerUID,'WorkflowModuleUID'=>$WorkflowModuleUID))->get()->result();
	}

	public function get_parking_callbacks($OrderUID)
	{
		$this->db->select('*');
		$this->db->select('b.UserName AS RaisedBy',false);
		$this->db->select("Date_Format(tOrderParking.Remainder,'%m/%d/%Y %H:%i:%s') AS Remainder", null,false);
		$this->db->select("Date_Format(tOrderParking.RaisedDateTime,'%m/%d/%Y %H:%i:%s') AS RaisedDateTime", null,false);
		$this->db->from('tOrderParking');
		$this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('tOrderParking.WorkflowModuleUID',$this->WorkflowUID);
		$query = $this->db->get();
		return $query->result();
	}
	
}
?>

