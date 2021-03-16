<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Settings extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Settings_Model');
		$this->load->library('form_validation');
	}

	public function index()
	{
		
		$data['content'] = 'index';
		$data['lists']=$this->Settings_Model->GetSettingDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function edit()
	{
		$SettingUID = $this->uri->segment(3);
		$data['setting'] = $this->db->select("*")->from("mSettings")->where(array('SettingUID'=>$SettingUID))->get()->row();
		$data['Milestones'] = $this->db->select("*")->from("mMilestone")->get()->result();
		$data['content'] = 'edit';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function Update()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$post = $this->input->post();
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('DisplayName', '', 'required');
			if ($post['SettingField'] != 'Scrolling_Text') {
				$this->form_validation->set_rules('SettingValue[]', '', 'required');
			}
			$this->form_validation->set_message('required', 'This Field is required');
			if ($this->form_validation->run() == true) 
			{	
				if(is_array($post['SettingValue'])) {
					$SettingValue = !empty($post['SettingValue']) ? implode(',', $post['SettingValue']) : NULL;
				} else {
					$SettingValue = !empty($post['SettingValue']) ? $post['SettingValue'] : NULL;
				}
				$updatearray = [
					"DisplayName"=> $post['DisplayName'],
					"SettingValue"=> $SettingValue,
					"Description"=> $post['Description'],
					"Active"=> isset($post['Active']) ? 1 : 0,
				];

				if($this->Settings_Model->Save('mSettings', $updatearray, ['SettingUID'=>$post['SettingUID']]))
				{
					$res = array('success' => 1,'message'=>$this->lang->line('Success'));
					echo json_encode($res);exit();
				}else{
					$res = array('success' => 0, 'message'=>$this->lang->line('Failed'));
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'success' => 0,
					'message' => $Msg,
					'DisplayName' => form_error('DisplayName'),
					//'SettingValue' => form_error('SettingValue'),
				);

				echo json_encode($data);
			}
		}


	}

	function PdfText()
	{
		ini_set("display_errors", 1);
		error_reporting(E_ALL);
		$this->load->library("Pdfparser");
		$file = "D:\Separator.pdf";

		$text = $this->pdfparser->getPageText($file);

		echo "<pre>";
		print_r($text);
	}


} 

?>