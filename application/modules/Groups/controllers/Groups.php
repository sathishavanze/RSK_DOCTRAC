<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Groups extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Groups_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{

		$data['content'] = 'index';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function add_group()
	{
		$data['content'] = 'addgroup';
		$data['getcustomers'] = $this->Common_Model->GetCustomer();
		$data['getteamleaderusers'] = $this->Groups_Model->get_teamleaderusers();
		$data['getusers'] = $this->Groups_Model->get_users();
		$data['getstates'] = $this->Groups_Model->GetState();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}	

	function edit_group()
	{
		$GroupUID = $this->uri->segment(3);
		if(!empty($GroupUID)){
			if($this->Groups_Model->check_group($GroupUID)) { 
				$data['content'] = 'editgroup';
				$data['Group'] = $this->Groups_Model->get_group($GroupUID);
				$data['getcustomers'] = $this->Common_Model->GetCustomer();
				$data['getteamleaderusers'] = $this->Groups_Model->get_teamleaderusers();
				$data['getusers'] = $this->Groups_Model->get_users();
				$data['getstates'] = $this->Groups_Model->GetState();
				//fetch saved details for the group
				$data['getgroupcustomers'] = $this->Groups_Model->get_groupcustomer($GroupUID);
				$data['getgroupteamleadusers'] = $this->Groups_Model->get_groupteamleadusers($GroupUID);
				$data['getgroupusers'] = $this->Groups_Model->get_groupusers($GroupUID);
				$data['getgroupstates'] = $this->Groups_Model->get_groupstate($GroupUID);
				$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
			} else {
				redirect(base_url()."Groups");
			}
		}else{
			redirect(base_url()."Groups");
		}
	}	

	function Group_ajax_list()
	{
		$list = $this->Groups_Model->get_datatables();
		$grouplist = [];

		foreach ($list as $listkey => $group)
		{
			$Active = ($group->Active) ? "checked" : "";
			$row = array();
			$row[] = $listkey+1;
			$row[] = $group->GroupName;
			$row[] = '<div class="togglebutton">
			<label class="label-color"> 
			<input type="checkbox" id="Active" name="Active" class="Active" '.$Active.' disabled="">
			<span class="toggle"></span>
			</label>
			</div>';
			$row[]='<a href="'.base_url('Groups/edit_group/').$group->GroupUID.'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload" ><i class="icon-pencil"></i></a>';
			$grouplist[]= $row;
		}

		$output = array(
			"draw" => $this->input->post('draw'),
			"recordsTotal" => $this->Groups_Model->count_all(),
			"recordsFiltered" =>  $this->Groups_Model->count_filtered(),
			"data" => $grouplist,
		);

		unset($data);

		echo json_encode($output);
	}


	function Savegroup()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('GroupName', '', 'required');
			$this->form_validation->set_rules('GroupCustomerUID', '', 'required');
			// $this->form_validation->set_rules('GroupTeamUserUID[]', '', 'required');
			// $this->form_validation->set_rules('GroupUserUID[]', '', 'required');
			//$this->form_validation->set_rules('GroupStateUID[]', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');

			$post = $this->input->post();
			$post['GroupUID'] = false;
			if ($this->form_validation->run() == true) 
			{
				$result = $this->Groups_Model->savegroup($post);
				if( $result == true)
				{

					$res = array('Status' => 0,'message'=>'Group added successsfully');
					echo json_encode($res);exit();

				} else {

					$res = array('Status' => 0,'message'=>'Failed');
					echo json_encode($res);exit();
				}
			} else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'GroupName' => form_error('GroupName'),
					'GroupCustomerUID' => form_error('GroupCustomerUID'),
					//'GroupTeamUserUID' => form_error('GroupTeamUserUID[]'),
					//'GroupUserUID' => form_error('GroupUserUID[]'),
					//'GroupStateUID' => form_error('GroupStateUID[]'),
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

	function Updategroup()
	{

		$post = $this->input->post();
		$GroupUID = $post['GroupUID'];
		if ($this->input->server('REQUEST_METHOD') === 'POST' && !empty($GroupUID) ) 
		{
			if($this->Groups_Model->check_group($GroupUID)) { 
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('GroupName', '', 'required');
				$this->form_validation->set_rules('GroupCustomerUID', '', 'required');
				//$this->form_validation->set_rules('GroupTeamUserUID[]', '', 'required');
				//$this->form_validation->set_rules('GroupStateUID[]', '', 'required');
				//$this->form_validation->set_rules('GroupUserUID[]', '', 'required');

				$this->form_validation->set_message('required', 'This Field is required');

				if ($this->form_validation->run() == true) 
				{
					$result = $this->Groups_Model->savegroup($post);
					if( $result == true)
					{

						$res = array('Status' => 0,'message'=>'Group Updated successsfully');
						echo json_encode($res);exit();

					} else {

						$res = array('Status' => 0,'message'=>'Failed');
						echo json_encode($res);exit();
					}
				} else{


					$Msg = $this->lang->line('Empty_Validation');


					$data = array(
						'Status' => 1,
						'message' => $Msg,
						'GroupName' => form_error('GroupName'),
						'GroupCustomerUID' => form_error('GroupCustomerUID'),
						//'GroupTeamUserUID' => form_error('GroupTeamUserUID[]'),
						//'GroupUserUID' => form_error('GroupUserUID[]'),
						//'GroupStateUID' => form_error('GroupStateUID[]'),
						'type' => 'danger',
					);


					foreach ($data as $key => $value) {
						if (is_null($value) || $value == '')
							unset($data[$key]);
					}
					echo json_encode($data);exit;
				}

			}
		}

		echo json_encode(array('Status' => 1,'message' => 'Failed','type' => 'danger'));exit;
	}


} 

?>