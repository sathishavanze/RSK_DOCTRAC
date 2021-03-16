<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocChase extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocChase_Model');
		$this->load->model('Ordersummary/Ordersummarymodel');
	}	

	public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		$WorkflowModuleUID = $this->config->item('Workflows')['DocChase'];
		$data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID'=>'ASC'], []);
		$data['OrderSummary'] = $this->Ordersummarymodel->GettOrders($OrderUID);
		$data['OrderDetails'] = $this->Common_Model->getOrderDetails($OrderUID);
		$data['BorrowerName'] = $this->Ordersummarymodel->GetBorrowerName($OrderUID);
		$data['Workflows'] = $this->DocChase_Model->get_docchase_workflows($OrderUID);
		$data['Status'] = $this->Common_Model->GetOrderWorkflowsWithStatus($OrderUID);
		$data['Callbacks'] = $this->DocChase_Model->get_parking_callbacks($OrderUID);
		$data['mTimeZones'] = $this->Common_Model->get('mTimeZones', ['Active'=>1]);
		$data['tOrderMeeting'] = $this->Common_Model->get_row('tOrderMeeting', ['MeetingOrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);
		$data['WorkflowModuleUID'] = $WorkflowModuleUID;
		$data['ChecklistFields'] = $this->Common_Model->get_dynamicchecklistfields($OrderUID,$WorkflowModuleUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	
}?>
