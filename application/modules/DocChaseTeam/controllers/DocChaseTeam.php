<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocChaseTeam extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocChaseTeam_model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

} 

?>