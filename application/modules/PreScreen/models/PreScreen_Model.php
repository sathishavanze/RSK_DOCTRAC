<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class PreScreen_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
		$this->lang->load('keywords');
		$this->WorkflowUID = $this->config->item('Workflows')['PreScreen'];
	}

	public function getcategorylist($OrderUID,$WorkflowModuleUID)
	{
		$OrderDetails = $this->db->select('CustomerUID')->from('tOrders')->where('OrderUID',$OrderUID)->get()->row();
		return $this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType','mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID')->where(array('CustomerUID'=>$OrderDetails->CustomerUID,'WorkflowModuleUID'=>$WorkflowModuleUID))->get()->result();
	}

	/**
	*Function getprescreen checklist
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 05 May 2020
	*/
	function get_PrescreenWorkflowDetails($OrderUID)
	{
		//customer details
		$Customer = $this->Common_Model->getorder_customer_details($OrderUID);

		if(isset($Customer->PreScreenChecklist) && ($Customer->PreScreenChecklist == 'PreScreen')) {

			$ChecklistWorkflows = " AND mWorkFlowModules.WorkflowModuleUID = ".$this->config->item('ChecklistWorkflows')['PreScreen'];
		} else {
			$ChecklistWorkflows = " AND mWorkFlowModules.WorkflowModuleUID IN (".implode(',', array_values($this->config->item('ChecklistWorkflows'))).")"; 
		}

		return $this->db->query("SELECT mWorkFlowModules.WorkflowModuleUID,WorkflowModuleName FROM mWorkFlowModules 
			JOIN tOrderWorkflows ON tOrderWorkflows.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID 
			LEFT JOIN mCustomerWorkflowModules ON mCustomerWorkflowModules.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID 
			WHERE tOrderWorkflows.OrderUID = $OrderUID  {$ChecklistWorkflows} AND mCustomerWorkflowModules.CustomerUID = '".$this->parameters['DefaultClientUID']."' ")->result();
	}

	/* 
	* Function for get checklist alert message
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 28-07-2020
	*/
	function getChecklistAlert($DocumentTypeUID)
	{
		$this->db->select('DocumentTypeUID,ChecklistComment,AlertStartDate,AlertEndDate');
		$this->db->from('mDocumentTypeAlert');
		$this->db->where(array('DocumentTypeUID' => $DocumentTypeUID, 'CustomerUID' => $this->parameters['DefaultClientUID']));
		return $this->db->get()->result();
	}
}
?>
