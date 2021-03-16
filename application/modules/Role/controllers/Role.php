<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends MY_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->model('Role_Model');
	}

	/*Listing All Roles */
	public function index()
	{	
		// echo "<pre>"; print_r($this->session->userdata); exit();
		$data['content'] = 'index';
		$data['RoleDetails']= $this->Role_Model->GetRoles();
		$data['RoleTypeDetails']= $this->Role_Model->GetRoleType();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	//Add Role
	public function AddRole()
	{
		$data['content'] = 'addrole';
		$data['Action']="ADD";
		$data['RoleTypeDetails']= $this->Role_Model->GetRoleType();
		$data['Resources']= $this->Role_Model->get_allresources();
		$data['GetPermissions']=$this->Role_Model->GetPermissions();
		$data['CustomerDetails']= $this->Role_Model->GetCustomerDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	} 

	public function EditRole($RoleUID)
	{
		// echo "<pre>"; print_r($this->session->userdata); exit();
		$data['RoleDetails']=$this->db->select("*")->from("mRole")->where(array('RoleUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'editrole';
		$data['RoleTypeDetails']= $this->Role_Model->GetRoleType();
		$data['Resources']= $this->Role_Model->get_allresources();
		$data['CustomerDetails']= $this->Role_Model->GetCustomerDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function SaveRole()
	{
		$this->load->library('form_validation');  
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('RoleName', '', 'required');
		$this->form_validation->set_rules('RoleTypeUID', '', 'required'); 
		if(in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->form_validation->set_rules('CustomerUID', '', 'required'); 
		}
		
		if ($this->form_validation->run() == true) 
		{
			$result=$this->Role_Model->SaveRoleDetails($this->input->post());
			if( $result== 1)
			{

				$res = array('Status' => 0,'message'=>'Role added Successfully');
				echo json_encode($res);exit();

			}
			else{

				$res = array('Status' => 2,'message'=>'Role added failed');
				echo json_encode($res);exit();
			}
		}
		else{


			$Msg = $this->lang->line('Empty_Validation');


			$data = array(
				'Status' => 1,
				'message' => $Msg,
				'RoleName' => form_error('RoleName'),
				'RoleTypeUID' => form_error('RoleTypeUID'),
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
	public function UpdateRole()
	{
		/*echo '<pre>';print_r($_POST);exit;*/

		$this->load->library('form_validation');  
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('RoleName', '', 'required');
		$this->form_validation->set_rules('RoleTypeUID', '', 'required'); 
		if(in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->form_validation->set_rules('CustomerUID', '', 'required'); 
		}
		$post = $this->input->post(); 
		$data = [];

		
		if ($this->form_validation->run() == true) 
		{

			$RoleUID = $this->input->post('RoleUID');
			$result=$this->Role_Model->UpdateRoleDetails($_POST,$data);
			if( $result== 1)
			{

				$res = array('Status' => 0,'message'=>'Role Updated Successfully');
				echo json_encode($res);exit();

			}
			else{

				$res = array('Status' => 2,'message'=>'Role Update Failed');
				echo json_encode($res);exit();
			}
		}
		else{


			$Msg = $this->lang->line('Empty_Validation');


			$data = array(
				'Status' => 1,
				'message' => $Msg,
				'RoleName' => form_error('RoleName'),
				'RoleTypeUID' => form_error('RoleTypeUID'),
				'CustomerUID' => form_error('CustomerUID'),
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


	

	function AjaxChangeStatus()
	{
		$RoleUID = $this->input->post('RoleUID');
		$data['Active'] = $this->input->post('Status');
		$this->db->where('RoleUID',$RoleUID);
		if($this->db->update('mRole',$data))
		{
			$Msg = $this->lang->line('Search_Status');
			$res = array('error' =>1,'message'=> $Msg,'type' => 'success');
		} else {
			$Msg = $this->lang->line('Search_Status_Validation');
			$res = array('error' =>2,'message'=> $Msg,'type' => 'success');
		}
		echo json_encode($res);
	}

	function SaveEdit()
	{
		$this->Role_Model->SaveRoleEditDetails($this->input->post()); 
	}
}
?>

