<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Status extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Status_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['StatusDetails'] = $this->Status_Model->GetStatusDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function AddStatus()
	{
		$data['content'] = 'AddStatus';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditStatus()
	{
		$data['UpdateStatusDetails'] = $this->db->select("*")->from("mStatus")->where(array('StatusUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'UpdateStatus';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	

	function SaveStatus()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('StatusName', '', 'required');
			$this->form_validation->set_rules('StatusColor', '', 'required');
			$this->form_validation->set_rules('ModuleName', '', 'required');
			
			$this->form_validation->set_message('required', 'This Field is required');
			
			$post = $this->input->post();
			
			$data = [];
			
			$data['StatusName'] = $post['StatusName'];
			$data['StatusColor']=$post['StatusColor'];
			$data['ModuleName']=$post['ModuleName'];
			$data['Active']=1;
			if ($this->form_validation->run() == true) 
			{
				$result=$this->Status_Model->AddingStatus($data);
				if( $result== 1)
				{
					
					$res = array('Status' => 1,'message'=>'Status added Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'Failed');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'StatusName' => form_error('StatusName'),
					'StatusColor' => form_error('StatusColor'),
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

	function UpdateStatus()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->CI =& $this;
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('StatusName', '', 'required');
			$this->form_validation->set_rules('StatusColor', '', 'required');
			$this->form_validation->set_rules('ModuleName', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');
			
			$post = $this->input->post();
			/*echo'<pre>';print_r($post);exit(); */
			$data = [];
			
			$data['StatusName'] = $post['StatusName'];
			$data['StatusColor']=$post['StatusColor'];
			$data['ModuleName']=$post['ModuleName'];
			$data['Active']=isset($post['Active']) ? 1 : 0;
			$data['StatusUID'] = $post['StatusUID'];

			if ($this->form_validation->run() == true) 
			{
				$result=$this->Status_Model->UpdateStatus($data);
				if( $result== 1)
				{
					$res = array('Status' => 1,'message'=>'Status updated Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'Failed');
					echo json_encode($res);exit();
				}
			}
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'StatusName' => form_error('StatusName'),
					'StatusColor' => form_error('StatusColor'),
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

} 

?>