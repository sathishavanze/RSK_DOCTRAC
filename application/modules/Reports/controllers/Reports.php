<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Reports extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Reports_Model');

	}

	/**
	*Displaying all modules and based on module displaying Queues
	*@author harini <harini.bnagari@avanzegroup.com>
	*@since Tuesday 20 August 2020.
	*/
	public function Queues_report()
	{
		$data['content'] = 'index';
		$data['WorkflowModules'] =$this->Common_Model->get_customerroleworkflows($this->parameters['DefaultClientUID']);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	/**
	*Displaying all Queues and based on module 
	*@author harini <harini.bnagari@avanzegroup.com>
	*@since Tuesday 24 August 2020.
	*/
	public function GetQueuesByWorkflow()
	{
		//$data['content'] = 'GetQueuesByWorkflow';
		$data['WorkflowModule'] = $this->input->post('WorkflowModule');
		$data['screen_name']=$this->config->item('WorkflowDetails')[$data['WorkflowModule']]['screen'];
		$data['mQueues'] =$this->Reports_Model->GetmQueuesByWorkFlow($data['WorkflowModule']);
		if($this->RoleType == $this->config->item('Internal Roles')['Agent']){
			$data['users'] =$this->Reports_Model->GetUsersByWorkflow($data['WorkflowModule'],array($this->loggedid));
		}
		else
		{
		$data['users'] =$this->Reports_Model->GetUsersByWorkflow($data['WorkflowModule']);
		}
		$options="";
		if(!empty($data['mQueues'])){
		 foreach($data['mQueues'] as $queue){ 
                $options .="<option value=" .$queue['QueueUID'].">".$queue['QueueName']."</option>";
                 }
		}
		$usr_options="";
		if(!empty($data['users'])){
		 foreach($data['users'] as $user){ 
		 		if($this->RoleType == $this->config->item('Internal Roles')['Agent']){

		 			$usr_options .="<option selected='selected' value=" .$user->UserUID.">".$user->UserName."</option>";
		 		}
		 		else
		 		{
                $usr_options .="<option value=" .$user->UserUID.">".$user->UserName."</option>";
            	}
              }
		}
        $result = array("options" => $options,'usr_options'=>$usr_options,'screen_name'=>$data['screen_name']);
         echo json_encode($result);
		 exit;
	}
	/**
	*Get the users queue count based on workflow 
	*@author harini <harini.bangari@avanzegroup.com>
	*@since Tuesday 20 August 2020.
	*/
	public function GetUsersByWorkflow()
	{
		$data['WorkflowModule'] = $this->input->post('WorkflowModule');	
		$data['mQueue'] = $this->input->post('mQueue');
		$data['UsrList'] = $this->input->post('UsrList');
		//print_r($data['UsrList']);exit;
		//print_r($data['mQueue']);
		$data['QueueNames'] =$this->Reports_Model->GetmQueuesByWorkFlow($data['WorkflowModule'],array_values($data['mQueue']));
		$data['list']=$this->Reports_Model->GetUserQuecount($data['WorkflowModule'],$data['mQueue'],array_values($data['UsrList']));
		/*print_r($data['QueueNames']);
		print_r($data['list']);
		exit;*/
		echo json_encode($this->load->view('getusersbyworkflow', $data,true));

	}
	/**
	*Displaying all modules and based on module and aging 
	*@author harini <harini.bnagari@avanzegroup.com>
	*@since Tuesday 20 August 2020.
	*/
	public function QueuesAgingReport()
	{
		$data['content'] = 'QueuesAgingReport';
		$data['WorkflowModules'] =$this->Common_Model->get_customerroleworkflows();
		$data['AgingHeader'] = $this->config->item('AgingHeader');
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	/**
	*Get the users queue count based on workflow 
	*@author harini <harini.bangari@avanzegroup.com>
	*@since Tuesday 20 August 2020.
	*/
	public function GetQueuesAgingUsersByWorkflow()
	{
		$data['content'] = 'GetQueuesAgingUsersByWorkflow';
		$data['WorkflowModule'] = $this->input->post('WorkflowModule');
		$data['Aging'] = $this->input->post('Aging');
		$data['mQueue'] = $this->input->post('mQueue');
		$data['UsrList'] = $this->input->post('UsrList');
		$data['QueueNames'] =$this->Reports_Model->GetmQueuesByWorkFlow($data['WorkflowModule'],array_values($data['mQueue']));
		$data['list']=$this->Reports_Model->GetUserAgingQuecount($data['WorkflowModule'],$data['Aging'],$data['mQueue'],array_values($data['UsrList']));
		echo json_encode($this->load->view('GetQueuesAgingUsersByWorkflow', $data,true));
	}
	
	function fetch_queuereports()
	{
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$FromDate = $this->input->post('FromDate');
		$ToDate = $this->input->post('ToDate');
		if($WorkflowModuleUID) {
			
			$content = $this->load->view('DynamicColumn/Queue_report', ['WorkflowModuleUID'=>$WorkflowModuleUID, 'FromDate' => $FromDate, 'ToDate' => $ToDate ],TRUE);
			echo json_encode(array('success' => 1,'content'=>$content));exit;

		}
		echo json_encode(array('success' => 0,'content'=>''));exit;
	}
}