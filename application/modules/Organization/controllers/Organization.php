<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Organization extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('Organizationmodel');
		$this->load->library('form_validation');

	}
	function index()
	{
		$data['content']='index';
		$data['organization']=$this->Organizationmodel->getorganization();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page',$data);

	}

	function getzip()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$zipcode = $this->input->post('zipcode');
			$details = $this->Organizationmodel->getzipcontents($zipcode);
			echo json_encode($details);

		}
	}

	function AddOrganization()
	{
		$data['content']='addorganization';
		$this->load->view($this->input->is_ajax_request()?$data['content'] : 'page',$data);
	}
	function EditOrganization()
	{
		$data['content']='updateorganization';
		$data['organization']=$this->db->select('*')->from('mOrganization')->where(array('OrganizationUID'=>$this->uri->segment(3)))->get()->row();
		$this->load->view($this->input->is_ajax_request()? $data['content'] : 'page',$data);
	}
	function UpdateOrganization()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{			
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('OrganizationName', '', 'required');
			$this->form_validation->set_rules('PropertyZipcode', '', 'required');
			$this->form_validation->set_rules('SMTPHost', '', 'required');
			$this->form_validation->set_rules('SMTPUserName', '', 'required');
			$this->form_validation->set_rules('SMTPPassword', '', 'required');
			$this->form_validation->set_rules('SMTPPort', '', 'required');
			$this->form_validation->set_rules('BinCount', '', 'required');
			$this->form_validation->set_rules('pageweight', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			if($this->form_validation->run() == true)
			{
				$OrgEditData = array('OrganizationName'=>$this->input->post('OrganizationName'),
					'OrganizationAddress1'=>$this->input->post('OrganizationAddress1'),
					'OrganizationAddress2'=>$this->input->post('OrganizationAddress2'),
					'OrganizationZib'=>$this->input->post('PropertyZipcode'),
					'OrganizationCity'=>$this->input->post('PropertyCityName'),
					'OrganizationCounty'=>$this->input->post('PropertyCountyName'),
					'OrganizationState'=>$this->input->post('PropertyStateCode'),
					'OrganizationPhoneNo'=>$this->input->post('OrganizationPhoneNo'),
					'SMTPHost'=>$this->input->post('SMTPHost'),
					'SMTPUserName'=>$this->input->post('SMTPUserName'),
					'SMTPPassword'=>$this->input->post('SMTPPassword'),
					'SMTPPort'=>$this->input->post('SMTPPort'),
					'BinCount'=>$this->input->post('BinCount'),
					'PageWeight'=>$this->input->post('pageweight')
					);

				if($this->input->post('OrgLogo') == 'No')
				{
					$filesCount = count($_FILES['organizationeditlogo']['name']); 
					for($i = 0; $i < $filesCount; $i++)
					{
						$filename = preg_replace('/[^a-zA-Z0-9_.]/', '', $_FILES['organizationeditlogo']['name'][$i]);						
						$_FILES['uploadfile']['name'] = $filename;
						$_FILES['uploadfile']['type'] = $_FILES['organizationeditlogo']['type'][$i];
						$_FILES['uploadfile']['tmp_name'] = $_FILES['organizationeditlogo']['tmp_name'][$i];
						$_FILES['uploadfile']['error'] = $_FILES['organizationeditlogo']['error'][$i];
						$_FILES['uploadfile']['size']  = $_FILES['organizationeditlogo']['size'][$i];
						$upload_url = "uploads/organization/";
						$config['upload_path'] = $upload_url;
						$config['allowed_types'] = 'jpeg|JPG|PNG|JPEG|gif|jpg|png';
						$config['remove_spaces'] = TRUE;
						$this->load->library('upload', $config);
						if(!is_dir($config['upload_path'])) 
						{
							mkdir($config['upload_path'], 0777, true);
						}
						$this->load->library('upload', $config);
						$this->upload->initialize($config);
						$filespath = $config['upload_path'];
						if($this->upload->do_upload('uploadfile')){
							$fileData = $this->upload->data();
							$uploadData[] = $fileData['file_name'];
						}
						$filenames = implode(',', $uploadData);
					}
					$OrgEditData['OrganizationLogo'] = $filespath.$filenames;
				}
			//echo '<pre>';print_r($OrgEditData);exit;
			$UpdateID = $this->input->post('OrganizationUID');

			$UpdateOrganization = $this->Organizationmodel->UpdateOrganization($OrgEditData,$UpdateID);

				if($UpdateOrganization == 1)
				{
				$res = array('validation_error' => 0,'message'=>'Organization UPDATED Successsfully');
					
					echo json_encode($res);exit();
				}
				else
				{
					$res = array('Status' => 0,'message'=>'Records Not Changed');
					echo json_encode($res);exit();
				}

			}
			else
			{
				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
				    'OrganizationName' => form_error('OrganizationName'),
					'PropertyZipcode' => form_error('PropertyZipcode'),
					'SMTPHost' => form_error('SMTPHost'),
					'CityUID' => form_error('CityUID'),
					'SMTPUserName' => form_error('SMTPUserName'),
					'SMTPPassword' => form_error('SMTPPassword'),
					'SMTPPort' => form_error('SMTPPort'),
					'BinCount' => form_error('BinCount'),
					'pageweight' => form_error('pageweight')
				);

				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);

			}
		}
	}

	function SaveOrganization()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('OrganizationName', '', 'required');
			$this->form_validation->set_rules('PropertyZipcode', '', 'required');
			
			$this->form_validation->set_rules('SMTPHost', '', 'required');
			$this->form_validation->set_rules('SMTPUserName', '', 'required');
			$this->form_validation->set_rules('SMTPPassword', '', 'required');
			$this->form_validation->set_rules('SMTPPort', '', 'required');
			$this->form_validation->set_rules('BinCount', '', 'required');
			$this->form_validation->set_rules('pageweight', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			if($this->form_validation->run() == true)
			{
				$filesCount = count($_FILES['organizationlogo']['name']); 
				for($i = 0; $i < $filesCount; $i++)
				{
					$filename = preg_replace('/[^a-zA-Z0-9_.]/', '', $_FILES['organizationlogo']['name'][$i]);						
					$_FILES['uploadfile']['name'] = $filename;
					$_FILES['uploadfile']['type'] = $_FILES['organizationlogo']['type'][$i];
					$_FILES['uploadfile']['tmp_name'] = $_FILES['organizationlogo']['tmp_name'][$i];
					$_FILES['uploadfile']['error'] = $_FILES['organizationlogo']['error'][$i];
					$_FILES['uploadfile']['size']  = $_FILES['organizationlogo']['size'][$i];
					$upload_url = "uploads/organization/";
					$config['upload_path'] = $upload_url;
					$config['allowed_types'] = 'jpeg|JPG|PNG|JPEG|gif|jpg|png';
					$config['remove_spaces'] = TRUE;
					$this->load->library('upload', $config);
					if(!is_dir($config['upload_path'])) 
					{
						mkdir($config['upload_path'], 0777, true);
					}
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					$filespath = $config['upload_path'];
					if($this->upload->do_upload('uploadfile')){
						$fileData = $this->upload->data();
						$uploadData[] = $fileData['file_name'];
					}
					$filenames = implode(',', $uploadData);
				}


			$OrgData = array(
				'OrganizationName'=>$this->input->post('OrganizationName'),
					'OrganizationAddress1'=>$this->input->post('OrganizationAddress1'),
					'OrganizationAddress2'=>$this->input->post('OrganizationAddress2'),
					'OrganizationZib'=>$this->input->post('PropertyZipcode'),
					'OrganizationCity'=>$this->input->post('PropertyCityName'),
					'OrganizationCounty'=>$this->input->post('PropertyCountyName'),
					'OrganizationState'=>$this->input->post('PropertyStateCode'),
					'OrganizationPhoneNo'=>$this->input->post('OrganizationPhoneNo'),
					'SMTPHost'=>$this->input->post('SMTPHost'),
					'SMTPUserName'=>$this->input->post('SMTPUserName'),
					'SMTPPassword'=>$this->input->post('SMTPPassword'),
					'SMTPPort'=>$this->input->post('SMTPPort'),
					'BinCount'=>$this->input->post('BinCount'),
					'PageWeight'=>$this->input->post('pageweight'),
				    'OrganizationLogo' => $filespath.$filenames,
			);	
			$InsertOrganization = $this->Organizationmodel->SaveOrganization($OrgData);


				if($InsertOrganization == 1)
				{
				$res = array('validation_error' => 0,'message'=>'Organization Inserted Successsfully');
					
					echo json_encode($res);exit();
				}
				else
				{
					$res = array('Status' => 0,'message'=>'Organization Not Inserted');
					echo json_encode($res);exit();
				}

			}
			else
			{
				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
				    'OrganizationName' => form_error('OrganizationName'),
					'PropertyZipcode' => form_error('PropertyZipcode'),
					'SMTPHost' => form_error('SMTPHost'),
					'CityUID' => form_error('CityUID'),
					'SMTPUserName' => form_error('SMTPUserName'),
					'SMTPPassword' => form_error('SMTPPassword'),
					'SMTPPort' => form_error('SMTPPort'),
					'BinCount' => form_error('BinCount'),
					'pageweight' => form_error('pageweight')
					
					
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