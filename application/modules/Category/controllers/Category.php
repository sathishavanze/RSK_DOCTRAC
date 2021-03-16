	<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
	class Category extends MY_Controller {

		function __construct()
		{
			parent::__construct();
			$this->load->model('Categorymodel');
			$this->load->model('Role/Role_Model');
			$this->load->library('form_validation');
		}	

		public function index()
		{
			
			$data['content'] = 'index';
			$data['UserDetails'] = $this->Categorymodel->GetGategoryDetails();
			//$data['Roles'] = $this->Common_Model->GetRoles('ADMINSTRATOR');
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

		function AddCategory()
		{
			$data['content'] = 'addcategory';
			$data['CustomerDetails']= $this->Role_Model->GetCustomerDetails();
			//$data['Roles'] = $this->Common_Model->GetRoles();
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

		function EditCategory()
		{
			$data['UserDetails'] = $this->db->select("*")->from("mCategory")->where(array('CategoryUID'=>$this->uri->segment(3)))->get()->row();
			$data['content'] = 'updatecategory';
			//$data['Roles'] = $this->Common_Model->GetRoles();
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}

		function SaveCategory()
		{


			if ($this->input->server('REQUEST_METHOD') === 'POST') 
			{
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('CategoryName', '', 'required');
				$this->form_validation->set_message('required', 'This Field is required');
                $data['Active']=isset($post['Active']) ? 1 : 0;

				if ($this->form_validation->run() == true) 
				{

					if($this->Categorymodel->SaveCategory($this->input->post()) == 1)
					{
						$res = array('Status' => 1,'message'=>'Category added Successsfully');
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
						'CategoryName' => form_error('CategoryName'),
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

		function UpdateCategory()
		{
			if ($this->input->server('REQUEST_METHOD') === 'POST') 
			{
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('CategoryName', '', 'required');
				$this->form_validation->set_message('required', 'This Field is required');
                $data['Active']=isset($post['Active']) ? 1 : 0;
				if ($this->form_validation->run() == true) 
				{
					
					if($this->Categorymodel->UpdateCategory($this->input->post()) == 1)
					{
						$res = array('Status' => 2,'message'=>'Updated Successsfully','type' => 'success');
						echo json_encode($res);exit();
					}else{
						$res = array('Status' => 0);
						echo json_encode($res);exit();
					}

					
				}else{


					$Msg = $this->lang->line('Empty_Validation');


					$data = array(
						'Status' => 1,
						'message' => $Msg,
						'CategoryName' => form_error('CategoryName'),
						
					);

					

					foreach ($data as $key => $value) {
						if (is_null($value) || $value == '')
							unset($data[$key]);
					}
					echo json_encode($data);
				}
			}

		}

		function CheckLoginUser()
		{
			$loginid = $this->input->post('loginid');
			if($this->Usermodel->CheckLoginUser($loginid) == 1)
			{
				$res = array('Status' => 1);
				echo json_encode($res);exit();
			}else{
				$res = array('Status' => 2);
				echo json_encode($res);exit();
			}
		}


	} 

	?>