<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class WorkFlow extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('WorkFlow_Model');
		$this->load->library('form_validation');
		$this->load->config('keywords');
	}

	public function index()
	{

		$data['content'] = 'index';
		$data['WorkFlowList']=$this->WorkFlow_Model->get('mWorkFlowModules');
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function AddWorkFlow()
	{
		$data['content'] = 'AddWorkFlow';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditWorkFlow()
	{
		$data['WorkFlowDetails'] = $this->db->select("*")->from("mWorkFlowModules")->where(array('WorkflowModuleUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'EditWorkFlow';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveWorkFlow()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('WorkflowModuleName', '', 'required');
			$this->form_validation->set_rules('WorkflowIcon', '', 'required');
			$this->form_validation->set_message('required', 'This Product is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();
				$SaveWorkFlow = [];
				$SaveWorkFlow = [
								"WorkflowModuleName"=> $post['WorkflowModuleName'],
								"WorkflowIcon"=> $post['WorkflowIcon'],
								"Active"=> 1,
							];
				if($this->WorkFlow_Model->Save('mWorkFlowModules', $SaveWorkFlow))
				{
					$res = array('validation_error' => 0,'message'=>'WorkFlow added Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Add WorkFlow.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'WorkflowModuleName' => form_error('WorkflowModuleName'),
					'WorkflowIcon' => form_error('WorkflowIcon'),
				);


				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function UpdateWorkFlow()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('WorkflowModuleName', '', 'required');
			$this->form_validation->set_rules('WorkflowIcon', '', 'required');
			$this->form_validation->set_message('required', 'This Product is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();

				$mWorkFlowModules = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$post['WorkflowModuleUID']]);
				if (empty($mWorkFlowModules)) {
					$res = array('validation_error' => 1, 'message'=>"Invalid Product to Update.");
					echo json_encode($res);exit();
				}

				$SaveWorkFlow = [];
				$SaveWorkFlow = [
								"WorkflowModuleName"=> $post['WorkflowModuleName'],
								"WorkflowIcon"=> $post['WorkflowIcon'],
								"Active"=> isset($post['Active']) ? 1 : 0,
							];
				if($this->WorkFlow_Model->Save('mWorkFlowModules', $SaveWorkFlow, ['WorkflowModuleUID'=>$post['WorkflowModuleUID']]))
				{
					$res = array('validation_error' => 0,'message'=>'WorkFlow Updated Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Update WorkFlow.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'WorkflowModuleName' => form_error('WorkflowModuleName'),
					'WorkflowIcon' => form_error('WorkflowIcon'),
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