<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class FloodCert extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('FloodCert_model');
		$this->load->model('Ordersummary/Ordersummarymodel');
	}	

	public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID);
		$data['OrderDetails'] = $OrderDetails;
		$groups = $OrderDetails->LoanType;
		$WorkflowModuleUID = $this->config->item('Workflows')['FloodCert'];
		$data['DocumentTypeNameDetails'] = $this->Common_Model->getcategorylist($OrderUID,$WorkflowModuleUID,$groups);
		$data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID'=>'ASC'], []);
		$data['OrderSummary'] = $this->Ordersummarymodel->GettOrders($OrderUID);
		$data['BorrowerName'] = $this->Ordersummarymodel->GetBorrowerName($OrderUID);
		$data['Workflows'] = $this->Common_Model->GetOrderWorkflows($OrderUID);
		$data['Status'] = $this->Common_Model->GetOrderWorkflowsWithStatus($OrderUID);
		$data['ChecklistFields'] = $this->Common_Model->get_dynamicchecklistfields($OrderUID,$WorkflowModuleUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
} 
?>