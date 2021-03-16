<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Emailtemplate extends MY_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Emailtemplatemodel');
		$this->load->library('form_validation');
		
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['emailtemplate'] = $this->Emailtemplatemodel->getemailtemplate();
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function AddEmailtemplate()
	{
		$data['content']='addemailtemplate';
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function EditEmailtemplate()
	{
		$data['templatedetails']=$this->db->select("*")->from("mEmailTemplate")->where(array('EmailTemplateUID'=>$this->uri->segment(3)))->get()->row();
		$data['content']="updateemailtemplate";
		$this->load->view($this->input->is_ajax_request()?$data['content'] : 'page',$data);
	}

	function UpdateEmailtemplate()
	{
		

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			// $this->form_validation->set_rules('ToMailID','','trim|required');
			$this->form_validation->set_rules('EmailTemplateName', 'Email Template Name', 'required');
			$this->form_validation->set_rules('Subject', 'Subject', 'required');
			$this->form_validation->set_rules('Body', 'Email Content', 'required');

			if($this->form_validation->run() == true)
			{
				if($this->Emailtemplatemodel->UpdateEmailtemplate($this->input->post()) == 1)
				{
					$res = array('Status' => 2,'message'=>'Updated Successsfully','type' => 'success');
					echo json_encode($res);exit();
				}
				else
				{
					$res = array('Status' => 0,'message'=>'Updated Successsfully','type' => 'success');
					echo json_encode($res);exit();
				}
				
			}
			else
			{
				$msg = 'Please fill all fields';
				$data = array(
					'Status' => 1,
					'message' => $msg
					
					
				);

				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
			
		}	
		
	}
	function SaveEmailtemplate()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->form_validation->set_error_delimiters('','');
			// $this->form_validation->set_rules('ToMailID','To Mail ID','trim|required');
			$this->form_validation->set_rules('EmailTemplateName', 'Email Template Name', 'required');
			$this->form_validation->set_rules('Subject', 'Subject', 'required');
			$this->form_validation->set_rules('Body', 'Email Content', 'required');
			// $this->form_validation->set_message('required','fill this field');
			if($this->form_validation->run()==true)
			{				
				$Inserttemplate = $this->Emailtemplatemodel->SaveEmailtemplate($this->input->post());
				if($Inserttemplate != 0)
				{					
					$res = array('validation_error' => 0,'message'=>'Added Successsfully','templateuid'=>$Inserttemplate);					
					echo json_encode($res);exit();
				}
				else
				{
					$res = array('Status' => 0);
					echo json_encode($res);exit();
				}
			}
			else
			{
				$msg = 'Please fill all fields';
				$data = array(
				'validation_error' => 1,
				'message' => $msg);
			}
			foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
		
		echo json_encode($data);
		
	     }

    }

    public function DeleteEmailtemplate()
	{
		$Id = $this->input->post('dlt-id');
		$result = $this->Emailtemplatemodel->deleteTemplateEmail($Id);
		if($result == 1)
		{
			$Msg = $this->lang->line('Delete');
			$res = array("validation_error" => 1,'message' => $Msg);
		}
		else
		{
			$Msg = $this->lang->line('Error');
			$res = array("validation_error" => 0,'message' =>$Msg);
		}
		echo json_encode($res);
	}
}
?>
