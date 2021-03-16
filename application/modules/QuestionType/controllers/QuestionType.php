<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class QuestionType extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('QuestionTypemodel');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['UserDetails'] = $this->db->select("*")->from("mQuestionType")->get()->result();
		//$data['Roles'] = $this->Common_Model->GetRoles();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function AddQuestionType()
	{
		$data['content'] = 'addQuestionType';
		//$data['Roles'] = $this->Common_Model->GetRoles();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditQuestionType()
	{
		$data['UserDetails'] = $this->db->select("*")->from("mQuestionType")->where(array('QuestionTypeUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'updateQuestionType';
		//$data['Roles'] = $this->Common_Model->GetRoles();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveQuestionType()
	{


		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('QuestionTypeName', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
            $data['Active']=isset($post['Active']) ? 1 : 0;
			if ($this->form_validation->run() == true) 
			{

				if($this->QuestionTypemodel->SaveQuestionType($this->input->post()) == 1)
				{
					$res = array('Status' => 1,'message'=>'QuestionType added Successsfully');
					echo json_encode($res);exit();
				}else{
					print_r("expression");exit();
					$res = array('Status' => 0);
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'QuestionTypeName' => form_error('QuestionTypeName')
					
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function UpdateQuestionType()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('QuestionTypeName', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
             $data['Active']=isset($post['Active']) ? 1 : 0;
			if ($this->form_validation->run() == true) 
			{
				
				if($this->QuestionTypemodel->UpdateQuestionType($this->input->post()) == 1)
				{
					$res = array('Status' => 2,'message'=>'Updated Successsfully','type' => 'success');
					echo json_encode($res);exit();
				}else{
					$res = array('Status' => 0,'message'=>'Updated Successsfully');
					echo json_encode($res);exit();
				}

				
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
		'QuestionTypeName' => form_error('QuestionTypeName')
					
				);

				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function CheckLoginUser()
	{
		$loginid = $this->input->post('loginid');
		if($this->Usermodel->CheckLoginUser($loginid) == 1)
		{
			$res = array('Status' => 1);
			echo json_encode($res);exit();
		}else{
			$res = array('Status' => 2);
			echo json_encode($res);exit();
		}
	}


} 

?>