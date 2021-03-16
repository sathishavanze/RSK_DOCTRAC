<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Resources extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Resources_Model');
		$this->load->model('Role/Role_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['ResourcesDetails'] = $this->Resources_Model->GetResourcesDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function AddResources()
	{
		$data['content'] = 'AddResources';
		$data['workflows'] = $this->Resources_Model->GetWorkflows();
		$data['CustomerDetails']= $this->Role_Model->GetCustomerDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function UpdateResources()
	{
		$data['UpdateResources'] = $this->db->select("*")->from("mResources")->where(array('ResourceUID'=>$this->uri->segment(3)))->get()->row();
		$data['workflows'] = $this->Resources_Model->GetWorkflows();
		$data['content'] = 'UpdateResources';
		$data['CustomerDetails']= $this->Role_Model->GetCustomerDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	

	function SaveResources()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('controller', '', 'required');
			$this->form_validation->set_rules('FieldName', '', 'required');
			$this->form_validation->set_rules('FieldSection', '', 'required');
			$this->form_validation->set_rules('MenuBarType', '', 'required');
			// $this->form_validation->set_rules('CustomerUID[]', '', 'required');
			// $this->form_validation->set_rules('IconClass', '', 'required');
			// $this->form_validation->set_rules('Position', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');

			
			$post = $this->input->post();
			/*echo'<pre>';print_r($post);exit(); */
			$data = [];
			
			$data['controller'] = $post['controller'];
			$data['FieldName']=$post['FieldName'];
			$data['FieldSection']=$post['FieldSection'];
			$data['NotificationEle']=$post['NotificationEle'];
			$data['IconClass']=$post['IconClass'];
			$data['Position']=isset($post['Position']) ? $post['Position'] : NULL;
			$data['MenuBarType']=$post['MenuBarType'];
			$data['WorkflowModuleUID']=isset($post['WorkflowModuleUID']) && !empty($post['WorkflowModuleUID']) && ($post['WorkflowModuleUID'] !="NA")  ? $post['WorkflowModuleUID'] : NULL;
			$data['ParentType']=$post['ParentType'];
			$data['CustomerUID']=implode(",",$post['CustomerUID']);

			if ($this->form_validation->run() == true) 
			{
				$result=$this->Resources_Model->SaveResources($data);
				if( $result== 1)
				{
					
					$res = array('Status' => 0,'message'=>'Resources added Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'Resources added Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'controller' => form_error('controller'),
					'FieldName' => form_error('FieldName'),
					'FieldSection' => form_error('FieldSection'),
					'MenuBarType' => form_error('MenuBarType'),
					'CustomerUID' => form_error('CustomerUID[]'),
					// 'IconClass' => form_error('IconClass'),
					// 'Position' => form_error('Position'),
					'type' => 'danger',
				);

			/*	echo '<pre>';print_r($data);exit;*/

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				//$res = array('Status' => 4,'detailes'=>$data);
				echo json_encode($data);
			}
			

		}

	}

	function UpdateResourceSave()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('controller', '', 'required');
			$this->form_validation->set_rules('FieldName', '', 'required');
			$this->form_validation->set_rules('FieldSection', '', 'required');
			$this->form_validation->set_rules('MenuBarType', '', 'required');
			// $this->form_validation->set_rules('CustomerUID[]', '', 'required');
			// $this->form_validation->set_rules('IconClass', '', 'required');
			// $this->form_validation->set_rules('Position', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');

			
			$post = $this->input->post();
			/*echo'<pre>';print_r($post);exit(); */
			$data = [];
			$data['ResourceUID'] = $post['ResourceUID'];
			$data['controller'] = $post['controller'];
			$data['FieldName']=$post['FieldName'];
			$data['FieldSection']=$post['FieldSection'];
			$data['WorkflowModuleUID']=isset($post['WorkflowModuleUID']) && !empty($post['WorkflowModuleUID']) && ($post['WorkflowModuleUID'] !="NA")  ? $post['WorkflowModuleUID'] : NULL;
			$data['NotificationEle']=$post['NotificationEle'];
			$data['IconClass']=$post['IconClass'];
			$data['Position']=isset($post['Position']) ? $post['Position'] : NULL;
			$data['MenuBarType']=$post['MenuBarType'];
			$data['Active']=isset($post['Active']) ? 1 : 0;
			$data['ParentType']=$post['ParentType'];
			$data['CustomerUID']=implode(",",$post['CustomerUID']);
			if ($this->form_validation->run() == true) 
			{
				$result=$this->Resources_Model->UpdateResourceSave($data);
				if( $result== 1)
				{
					
					$res = array('Status' => 2,'message'=>'Resources Update Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 1,'message'=>'No Updatation Resources');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'controller' => form_error('controller'),
					'FieldName' => form_error('FieldName'),
					'FieldSection' => form_error('FieldSection'),
					'MenuBarType' => form_error('MenuBarType'),
					'CustomerUID' => form_error('CustomerUID[]'),
					// 'IconClass' => form_error('IconClass'),
					// 'Position' => form_error('Position'),
					'type' => 'danger',
				);

			/*	echo '<pre>';print_r($data);exit;*/

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				//$res = array('Status' => 4,'detailes'=>$data);
				echo json_encode($data);
			}
			

		}

	}

} 

?>
