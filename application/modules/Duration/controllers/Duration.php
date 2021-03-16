	<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
	class Duration extends MY_Controller {

		function __construct()
		{
			parent::__construct();
			$this->load->model('Durationmodel');
			$this->load->library('form_validation');
		}	

		
	/* 
	* loading the Milestone list from the database
	*
	*/
		public function index()
		{
			
			$data['content'] = 'index';
			$data['GetWorkflowUIDName']=$this->Durationmodel->GetWorkflowUIDName();
			$this->db->select ( '*' );
			$this->db->from ( 'mWorkflowDurations' );
			$this->db->where('ClientUID' , $this->parameters['DefaultClientUID']);
			$query = $this->db->get();
			$data['UserDetails'] = $query->result();
			
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

	/* 
	*Adding Data of Milestone here 
	*
	*/	
	
		
		function AddDuration()
		{
			$data['content'] = 'addduration';
			$data['GetWorkflowList']=$this->Durationmodel->GetWorkflowList($this->parameters['DefaultClientUID']);
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

	/* 
	*Editing the Data of Milestone with Editmilestone function
	*
	*/
		function Editduration()
		{
			$data['UserDetails'] = $this->db->select("*")->from("mWorkflowDurations")->where(array('WorkflowModuleUID'=>$this->uri->segment(3),'ClientUID' => $this->parameters['DefaultClientUID']))->get()->row();
			$data['content'] = 'updateduration';
			$data['GetWorkflowUIDName']=$this->Durationmodel->GetWorkflowUIDName();
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

	/* 
	*Saving Data here with SaveMilestone function 
	*
	*/
		function SaveMilestone()
		{
			if ($this->input->server('REQUEST_METHOD') === 'POST') 
			{
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('WorkFlow', '', 'required');
				$this->form_validation->set_rules('Duration', '', 'required');
				$this->form_validation->set_message('required', 'This Field is required');
                $data['Active']=isset($post['Active']) ? 1 : 0;

				if ($this->form_validation->run() == true) 
				{
					// Check Duration already exist or not
					$result = $this->Durationmodel->CheckWorkflowDurationExist($this->input->post());
					if ($result) {
						$res = array('validation_error' => 1,'message'=>'Workflow Duration already exist!.', 'type'=>'danger');
						echo json_encode($res);exit();
					}

					if($this->Durationmodel->SaveMilestone($this->input->post()) == 1)
					{
						$res = array('Status' => 1,'message'=>'Duration added Successsfully');
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
						'WorkFlowUID' => form_error('WorkFlow'),
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

	/* 
	*Updating Milestone here with UpdateMilestone function
	*
	*/
		function Updateduration()
		{
			if ($this->input->server('REQUEST_METHOD') === 'POST') 
			{
				
				$this->form_validation->set_rules('WorkFlow', '', 'required');
				$this->form_validation->set_rules('Duration', '', 'required');
				$this->form_validation->set_message('required', 'This Field is required');
                		$data['Active']=isset($post['Active']) ? 1 : 0;
				if ($this->form_validation->run() == true) 
				{
					// Check milestone already exist or not
					$result = $this->Durationmodel->CheckWorkflowDurationExist($this->input->post());
					if ($result) {
						$res = array('validation_error' => 1,'message'=>'Workflow Duration already exist!.', 'type'=>'danger');
						echo json_encode($res);exit();
					}
					
				 	if($this->Durationmodel->UpdateDuration($this->input->post()) == 1)
					{
						$res = array('Status' => 2,'message'=>'Updated Successfully','type' => 'success');
						echo json_encode($res);exit();
					} else{
						$res = array('Status' => 0,'message'=>'No changes.');
						echo json_encode($res);exit();
					}

					
				}else{


					$Msg = $this->lang->line('Empty_Validation');


					$data = array(
						'Status' => 1,
						'message' => $Msg,
						'MilestoneName' => form_error('MilestoneName'),
						
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