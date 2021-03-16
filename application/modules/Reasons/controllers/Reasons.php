<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Reasons extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Reasons_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['ReasonsDetails'] = $this->Reasons_Model->GetReasonsDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function AddReasons()
	{
		$data['content'] = 'AddReasons';
		$data['QueueDetail'] = $this->Reasons_Model->GetQueueName();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditReasons()
	{
		$data['QueueDetail'] = $this->Reasons_Model->GetQueueName();
		$data['UpdateReasonsDetails'] = $this->db->select('mReasons.*' )
		->from('mReasons')
		->where(array('mReasons.ReasonUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'UpdateReasons';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	

	function SaveReasons()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ReasonsName', '', 'required');
			$this->form_validation->set_rules('QueueName', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			
			$post = $this->input->post();
			
			$data = [];
			
			$data['ReasonsName'] = $post['ReasonsName'];
			$data['QueueName']=$post['QueueName'];
			$data['Active']=1;
			if ($this->form_validation->run() == true) 
			{
				$result=$this->Reasons_Model->AddingReasons($data);
				if( $result== 1)
				{
					
					$res = array('Reasons' => 1,'message'=>'Reasons added Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Reasons' => 0,'message'=>'Failed');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Reasons' => 1,
					'message' => $Msg,
					'ReasonsName' => form_error('ReasonsName'),
					'ReasonsColor' => form_error('ReasonsColor'),
					'ModuleName' => form_error('ModuleName'),
					'type' => 'danger',
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
			

		}

	}

	function UpdateReasons()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ReasonName', '', 'required');
			$this->form_validation->set_rules('QueueUID', '', 'required');


			$this->form_validation->set_message('required', 'This Field is required');
			
			$post = $this->input->post();
			/*echo'<pre>';print_r($post);exit();*/
			$data = [];
			
			$data['ReasonUID'] = $post['ReasonUID'];
			$data['ReasonName'] = $post['ReasonName'];
			$data['QueueUID']=$post['QueueUID'];
			$data['Active']=isset($post['Active']) ? 1 : 0;
			

			if ($this->form_validation->run() == true) 
			{
				$result=$this->Reasons_Model->UpdateReasons($data);
				if( $result== 1)
				{
					$res = array('Reasons' => 1,'message'=>'Reasons updated Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Reasons' => 0,'message'=>'Failed');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Reasons' => 1,
					'message' => $Msg,
					'ReasonsName' => form_error('ReasonsName'),
					'type' => 'danger',
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				
				echo json_encode($data);
			}
			

			

		}

	}

} 

?>