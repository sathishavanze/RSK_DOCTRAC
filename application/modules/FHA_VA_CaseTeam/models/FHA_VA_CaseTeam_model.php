<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class FHA_VA_CaseTeam_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
		$this->WorkflowUID = $this->config->item('Workflows')['FHAVACaseTeam'];
	}

	public function getcategorylist($OrderUID,$WorkflowModuleUID)
	{
		$OrderDetails = $this->db->select('CustomerUID')->from('tOrders')->where('OrderUID',$OrderUID)->get()->row();
		return $this->db->select('*,mDocumentType.CategoryUID')->from('mCustomerWorkflowModules')->join('mDocumentType','mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID')->where(array('CustomerUID'=>$OrderDetails->CustomerUID,'WorkflowModuleUID'=>$WorkflowModuleUID))->get()->result();
	}

	
}
?>

