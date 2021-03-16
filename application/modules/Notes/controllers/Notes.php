<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Notes extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->lang->load('keywords');
		$this->load->model('Notes_Model');
		ini_set('display_errors', 1);

	}	

public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		$data['Notes'] = $this->Notes_Model->getNotes($OrderUID);
		$data['Workflows'] = $this->Common_Model->GetOrderWorkflows($OrderUID);
		$data['WorkflowArrays'] =$this->config->item('Workflows');
		$data['Status'] = $this->Common_Model->GetOrderWorkflowsWithStatus($OrderUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function AddNotes()
	{
		$result = $this->Notes_Model->insertNotes($this->input->post());
		echo json_encode($result);
	}

	
	
}?>
