<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class History extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->lang->load('keywords');
		$this->load->model('History_Model');
		ini_set('display_errors', 1);

	}	

public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		$data['OrderUID'] = $OrderUID;
		$data['Withdraw_Details'] = $this->History_Model->getWithdrawelDetails($OrderUID);
		$data['DocChase_Details'] = $this->History_Model->getDocChaseDetails($OrderUID);
		$data['FollowUp_Details'] = $this->History_Model->getFollowUpDetails($OrderUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	
	
}?>
