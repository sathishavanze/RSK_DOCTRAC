<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_new extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view('page', $data);
	}

}
