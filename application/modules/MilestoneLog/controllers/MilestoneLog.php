<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class MilestoneLog extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->lang->load('keywords');
		$this->load->model('MilestoneLog_Model');
		ini_set('display_errors', 1);

	}	

public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		$data['OrderUID'] = $OrderUID;
		$data['MilestoneLog_Details'] = $this->MilestoneLog_Model->getMilestoneLogDetails($OrderUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	
	
}?>
