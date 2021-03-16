<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Fields extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Fields_Model');
		$this->load->library('form_validation');
		$this->load->config('keywords');
	}

	public function index()
	{
		
		$data['content'] = 'index';
		$data['Fields']=$this->Fields_Model->get('mFields');
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function AddField()
	{
		$data['content'] = 'addField';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditField()
	{
		$data['FieldDetails'] = $this->db->select("*")->from("mFields")->where(array('FieldUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'updateField';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveField()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('FieldName', '', 'required');
			$this->form_validation->set_rules('FieldType', '', 'required');
			$this->form_validation->set_rules('FieldLabel', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();
				$addField = [];
				$addField = [
								"FieldName"=> $post['FieldName'],
								"FieldType"=> $post['FieldType'],
								"FieldLabel" => $post['FieldLabel'],
								"IsStacking"=> isset($post['FieldType']) ? 1 : 0,
								"IsIndexing"=> isset($post['IsIndexing']) ? 1 : 0,
							];
				if($this->Fields_Model->Save('mFields', $addField))
				{
					$res = array('validation_error' => 0,'message'=>'Field added Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Add Field.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'FieldName' => form_error('FieldName'),
					'FieldType' => form_error('FieldType'),
				);


				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function UpdateField()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('FieldName', '', 'required');
			$this->form_validation->set_rules('FieldType', '', 'required');
			$this->form_validation->set_rules('FieldLabel', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();

				$mFields = $this->Common_Model->get_row('mFields', ['FieldUID'=>$post['FieldUID']]);
				if (empty($mFields)) {
					$res = array('validation_error' => 1, 'message'=>"Invalid Field to Update.");
					echo json_encode($res);exit();
				}

				$addField = [];
				$addField = [
								"FieldName"=> $post['FieldName'],
								"FieldType"=> $post['FieldType'],
								"FieldLabel"=> $post['FieldLabel'],
								"IsStacking"=> isset($post['FieldType']) ? 1 : 0,
								"IsIndexing"=> isset($post['IsIndexing']) ? 1 : 0,
								"Active"=> isset($post['Active']) ? 1 : 0,
							];
				if($this->Fields_Model->Save('mFields', $addField, ['FieldUID'=>$post['FieldUID']]))
				{
					$res = array('validation_error' => 0,'message'=>'Field added Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Add Field.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'FieldName' => form_error('FieldName'),
					'FieldType' => form_error('FieldType'),
				);


				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
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