	<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
	class Milestone extends MY_Controller {

		function __construct()
		{
			parent::__construct();
			$this->load->model('Milestonemodel');
			$this->load->library('form_validation');
		}	

		
	/* 
	* loading the Milestone list from the database
	*
	*/
		public function index()
		{
			
			$data['content'] = 'index';
			//Get Milestone details
			$data['UserDetails'] = $this->Common_Model->GetMilestoneDetails();
			
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

	/* 
	*Adding Data of Milestone here 
	*
	*/		
		function AddMilestone()
		{
			$data['content'] = 'addmilestone';
			
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

	/* 
	*Editing the Data of Milestone with Editmilestone function
	*
	*/
		function Editmilestone()
		{
			$data['UserDetails'] = $this->db->select("*")->from("mMilestone")->where(array('MilestoneUID'=>$this->uri->segment(3)))->get()->row();
			$data['content'] = 'updatemilestone';
			
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
				$this->form_validation->set_rules('MilestoneName', '', 'required');
				$this->form_validation->set_message('required', 'This Field is required');
                $data['Active']=isset($post['Active']) ? 1 : 0;

				if ($this->form_validation->run() == true) 
				{
					// Check milestone already exist or not
					$result = $this->Milestonemodel->CheckMilestoneExist($this->input->post());
					if ($result) {
						$res = array('validation_error' => 1,'message'=>'Milestone name already exist!.', 'type'=>'danger');
						echo json_encode($res);exit();
					}

					if($this->Milestonemodel->SaveMilestone($this->input->post()) == 1)
					{
						$res = array('Status' => 1,'message'=>'Milestone added Successsfully');
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
						'MilestoneName' => form_error('MilestoneName'),
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
		function UpdateMilestone()
		{
			if ($this->input->server('REQUEST_METHOD') === 'POST') 
			{
				
				$this->form_validation->set_rules('MilestoneName', '', 'required');
				$this->form_validation->set_message('required', 'This Field is required');
                		$data['Active']=isset($post['Active']) ? 1 : 0;
				if ($this->form_validation->run() == true) 
				{
					// Check milestone already exist or not
					$result = $this->Milestonemodel->CheckMilestoneExist($this->input->post());
					if ($result) {
						$res = array('validation_error' => 1,'message'=>'Milestone name already exist!.', 'type'=>'danger');
						echo json_encode($res);exit();
					}
					
					if($this->Milestonemodel->UpdateMilestone($this->input->post()) == 1)
					{
						$res = array('Status' => 2,'message'=>'Updated Successfully','type' => 'success');
						echo json_encode($res);exit();
					}else{
						$res = array('Status' => 0,'message'=>'No changes');
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

		function InActiveQueueDetails() {
			$MilestoneUID = $this->input->post('MilestoneUID');
			$Active = $this->input->post('Active');
			$response = $this->Milestonemodel->InActiveQueueDetails($MilestoneUID,$Active);		
			if($response){
				$data=array('validation_error' => 0,'message' => 'Milestone Status Updated Successfully.','type'=>'success');
			}
			else{
				$data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
			}
			echo json_encode($data);
		}

	} 

	?>