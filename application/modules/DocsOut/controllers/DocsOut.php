<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocsOut extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocsOut_model');
		$this->load->model('Ordersummary/Ordersummarymodel');
		$this->WorkflowModuleUID = $this->config->item('Workflows')['DocsOut'];

	}	

	public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID);
		$data['OrderDetails'] = $OrderDetails;
		$groups = $OrderDetails->LoanType;
		$data['WorkflowModuleUID'] = $this->WorkflowModuleUID;
		$data['DocumentTypeNameDetails'] = $this->Common_Model->getcategorylist($OrderUID,$this->WorkflowModuleUID,$groups);
		$data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID'=>'ASC'], []);
		$data['OrderSummary'] = $this->Ordersummarymodel->GettOrders($OrderUID);
		$data['BorrowerName'] = $this->Ordersummarymodel->GetBorrowerName($OrderUID);
		$data['Workflows'] = $this->Common_Model->GetOrderWorkflows($OrderUID);
		$data['Status'] = $this->Common_Model->GetOrderWorkflowsWithStatus($OrderUID);
		$data['ChecklistFields'] = $this->Common_Model->get_dynamicchecklistfields($OrderUID,$this->WorkflowModuleUID);
		$data['CalculatorData'] = $this->Common_Model->GetCalculatorData($OrderUID,$this->WorkflowModuleUID);
		$data['loadexceldata'] = false;
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


} 

?>