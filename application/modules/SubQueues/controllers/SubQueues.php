<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SubQueues extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('SubQueues_model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['SubQueuesDetails'] = $this->SubQueues_model->SubQueuesDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function InActiveQueueDetails() {
		$QueueUID = $this->input->post('QueueUID');
		$Active = $this->input->post('Active');
		$response = $this->SubQueues_model->InActiveQueueDetails($QueueUID,$Active);		
		if($response){
			$data=array('validation_error' => 0,'message' => 'Sub Queue Status Updated Successfully.','type'=>'success');
		}
		else{
			$data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
		}
		echo json_encode($data);
	}	

	function SubQueue()
	{
		$QueueUID = $this->uri->segment(3);
		if (!empty($QueueUID)) {
			$data['SubQueuesDetails'] = $this->SubQueues_model->SubQueuesDetails($QueueUID);
			// echo '<pre>'; print_r($data['SubQueuesDetails']); exit;
		}
		$data['content'] = 'SubQueue';
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$data['WorkflowDetails'] = $this->Common_Model->GetcustomerWorkflowDetails($CustomerUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	
	function SaveSubQueue()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('QueueName', '', 'required');
			$this->form_validation->set_rules('WorkflowModuleUID', '', 'required');

			if ($this->form_validation->run() == true) 
			{
				if($this->SubQueues_model->SubQueuesActions($this->input->post()) == 1)
				{
					if(!empty($this->input->post('QueueUID')))
					{
						$res = array('Status' => 0,'message'=>'Updated Successsfully');
						echo json_encode($res);exit();
					}
					else
					{
						$res = array('Status' => 0,'message'=>'Added Successsfully');
						echo json_encode($res);exit();
					}
				}else{
					$res = array('Status' => 0,'message'=>'Updated Successsfully');
					echo json_encode($res);exit();
				}
			} else{

				$Msg = $this->lang->line('Empty_Validation');

				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'QueueName' => form_error('QueueName'),
					'WorkflowModuleUID' => form_error('WorkflowModuleUID'),
					'type' => 'danger',
				);

				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('QueueName', '', 'required');
				$this->form_validation->set_rules('WorkflowModuleUID', '', 'required');

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
