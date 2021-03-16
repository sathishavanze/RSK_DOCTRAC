<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class JuniorProcessorGroups extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('JuniorProcessorGroups_Model');
		$this->load->model('Customer/Customer_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{

		$data['content'] = 'index';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function addjuniorprocessorgroup()
	{
		$data['content'] = 'addjuniorprocessorgroup';
		$data['Processors'] = $this->JuniorProcessorGroups_Model->get_processorusers();
		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}	

	function editjuniorprocessorgroup()
	{
		$GroupUID = $this->uri->segment(3);
		if(!empty($GroupUID)){
			if($this->JuniorProcessorGroups_Model->GetJuniorProcessorGroupDetails($GroupUID, $ReturnType = 'count')) { 
				$data['content'] = 'editjuniorprocessorgroup';
				$data['Processors'] = $this->JuniorProcessorGroups_Model->get_processorusers();
				$data['JuniorProcessorGroupDetails'] = $this->JuniorProcessorGroups_Model->GetJuniorProcessorGroupDetails($GroupUID, $ReturnType = 'row');
				$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details();
				// $data['WorkflowsAndQueuesDetails'] = $this->JuniorProcessorGroups_Model->GetWorkflowQueueDetails($GroupUID);
				// echo '<pre>';print_r($data['WorkflowsAndQueuesDetails']);exit;

				$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
			} else {
				redirect(base_url()."JuniorProcessorGroups");
			}
		}else{
			redirect(base_url()."JuniorProcessorGroups");
		}
	}	

	function JuniorProcessorGroup_ajax_list()
	{
		$list = $this->JuniorProcessorGroups_Model->get_datatables();

		$grouplist = [];

		foreach ($list as $listkey => $group)
		{
			$Active = ($group->Active) ? "checked" : "";
			$row = array();
			$row[] = $listkey+1;
			$row[] = $group->UserName;
			$row[] = '<div class="togglebutton">
			<label class="label-color"> 
			<input type="checkbox" id="Active" name="Active" class="Active" '.$Active.' data-groupuid="'.$group->GroupUID.'">
			<span class="toggle"></span>
			</label>
			</div>';
			$row[]='<a href="'.base_url('JuniorProcessorGroups/editjuniorprocessorgroup/').$group->GroupUID.'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload" ><i class="icon-pencil"></i></a>';
			$grouplist[]= $row;
		}

		$output = array(
			"draw" => $this->input->post('draw'),
			"recordsTotal" => $this->JuniorProcessorGroups_Model->count_all(),
			"recordsFiltered" =>  $this->JuniorProcessorGroups_Model->count_filtered(),
			"data" => $grouplist,
		);

		unset($data);

		echo json_encode($output);
	}


	function SaveJuniorProcessorGroup()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('JuniorProcessorUserUID[]', '', 'required');
			$this->form_validation->set_rules('ProcessorUserUID[]', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');

			$post = $this->input->post();
			$post['GroupUID'] = isset($post['GroupUID'])? $post['GroupUID']: '';

			if ($this->form_validation->run() == true) 
			{
				// Check if junior processor is already added
				if ($this->JuniorProcessorGroups_Model->CheckJuniorProcessorGroupExist($post)) {

					$res = array('Status' => 1,'message'=>'The Junior Processor Group is already exists');
					echo json_encode($res);exit();
				}

				$result = $this->JuniorProcessorGroups_Model->SaveJuniorProcessorGroup($post);
				if( $result == true)
				{
					if (empty($post['GroupUID'])) {
						$res = array('Status' => 0,'message'=>'Junior Processor Group Added Successsfully');
					} else {
						$res = array('Status' => 0,'message'=>'Junior Processor Group Updated Successsfully');	
					}					
					echo json_encode($res);exit();

				} else {

					$res = array('Status' => 1,'message'=>'Failed');
					echo json_encode($res);exit();
				}
					
			} else{

				$Msg = $this->lang->line('Empty_Validation');

				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'JuniorProcessorUserUID' => form_error('JuniorProcessorUserUID[]'),
					'ProcessorUserUID' => form_error('ProcessorUserUID[]'),
					'type' => 'danger',
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);exit;
			}


		}
		echo json_encode(array('Status' => 1,'message' => 'Failed','type' => 'danger'));exit();
	}

	// update group status
	public function UpdateJuniorProcessorGroupStatus()
	{
		$GroupUID = $this->input->post('GroupUID');
		$Active = $this->input->post('Active');
		$data = $this->JuniorProcessorGroups_Model->UpdateJuniorProcessorGroupStatus($GroupUID,$Active);
		echo json_encode($data);
	}

	// Fetch Workflow SubQueues
	public function FetchWorkflowSubQueues($value='')
	{
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$WorkflowQueueDetails = $this->JuniorProcessorGroups_Model->getCustomerWorkflowQueues($WorkflowModuleUID);

		echo json_encode($WorkflowQueueDetails);
	}

} 

?>