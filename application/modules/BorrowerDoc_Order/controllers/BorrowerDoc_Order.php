<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BorrowerDoc_Order extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('BorrowerDoc_Order_Model');
	}

	public function index()
	{
		$data['content']='index';
		$data['is_selfassign'] = 1;
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

}?>

