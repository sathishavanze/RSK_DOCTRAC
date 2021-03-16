<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class PDFJS extends MY_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$this->load->view('index');
	}

	
		function userlog_ajax_list()
		{
			$login_id = $this->loggedid; 
			$formData = $this->input->post('formData'); 

			$data = $this->userlog_process_get_data($formData);
			$post = $data['post'];
			$post['formData'] = $formData;
			$output = array(
				"draw" => $post['draw'],
				"recordsTotal" => $this->UserLogsmodel->count_GetUserDetails($login_id,$post),
				"recordsFiltered" =>  $this->UserLogsmodel->filter_GetUserDetails($login_id,$post),
				"data" => $data['data'],
			);
			unset($post);
			unset($data);
			echo json_encode($output);        
		}
		function userlog_process_get_data($formData)
		{
			$login_id = $this->loggedid; 
			$post = $this->userlog_get_post_input_data(); 
			$post['column_order'] = array('DateTime','UserName','IpAddreess','Browser','BrowserVersion','Platform','PlatformVersion');
        // $post['column_search'] = array('UserName');
			$post['column_search'] = array('DateTime','UserName','IpAddreess','Browser','BrowserVersion','Platform','PlatformVersion');
			$post['formData'] = $formData;
        // echo '<pre>';print_r($post);exit;
        //$list = $this->Status_Report_Model->GetCityDetails($post);
			$list = $this->UserLogsmodel->GetUserDetails($login_id,$post); 
        //echo '<pre>';print_r($list);exit;
			$data = array();
        //$no = $post['start'];

			foreach ($list as $cities) {
            //$no++;
				$row =  $this->userlog_cities_table_data($cities);
				$data[] = $row;
			}

			return array(
				'data' => $data,
				'post' => $post
			);
		}
		function userlog_get_post_input_data(){
			$post['length'] = $this->input->post('length');
			$post['start'] = $this->input->post('start');
			$search = $this->input->post('search');
			$post['search_value'] = $search['value'];
			$post['order'] = $this->input->post('order');
			$post['draw'] = $this->input->post('draw');
			return $post;
		}
		function userlog_cities_table_data($cities)
		{

			$row = array();
        //$row[] = $no;
			$PlatformVersion = '';
			if($cities->Platform){
				$PlatformVersion = $cities->PlatformVersion ? '64bit' : '32bit';
			}

			$row[] = $cities->Userlogtime;
			$row[] = $cities->UserName .' '.'is'.' '.'Logged';
			$row[] = $cities->IpAddreess;
			$row[] = $cities->Browser;
			$row[] = $cities->BrowserVersion;
			$row[] = $cities->Platform;
			$row[] = $PlatformVersion;
			return $row;
		}

}?>
