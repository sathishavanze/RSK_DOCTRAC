<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Report extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['HelpMenu'] = '';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}	

} 

?>