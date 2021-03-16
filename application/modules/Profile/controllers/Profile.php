<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();

		if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
			redirect(base_url().'Profile');
		}
		else{
			$this->load->library(array('form_validation','upload'));
			$this->load->helper(array('form', 'url'));
			$this->load->model('Common_Model');
			$this->load->model('Profile_Model');
			//$this->load->model('users/Mlogin');
			$this->lang->load('keywords');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->RoleType = $this->session->userdata('RoleType');

		}
	}	

	public function index()
	{
		$UserID = $this->session->userdata('UserUID');
		$data['content'] = 'edit';
		$data['RoleDetails']= $this->Profile_Model->GetRoleDetails();
		$data['UserDetails']= $this->Profile_Model->GetUserDetailsById($UserID);
		$data['RoleName']= $this->Profile_Model->GetRoleTypeDetails($this->RoleUID);
		$data['Name'] = $this->UserName;
		$data['RoleType'] = $this->RoleType;
		
		if($this->RoleType == '15'){

			$data['abstractor_login'] = $this->Profile_Model->get_abstractor_login();
			$AbstractorUID = $data['abstractor_login']->AbstractorUID;
			$data['AbstractorDetails']= $this->Profile_Model->GetAbstractorDetails($AbstractorUID);
			$data['AbstractorDocumentDetails']= $this->Profile_Model->GetAbstractorDocument($AbstractorUID);
			$data['document'] = $this->Profile_Model->Getdocumenttypes(); 
			$data['subdocument'] = $this->Profile_Model->GetSubdocumenttypes();
			$data['States'] = $this->Common_Model->GetStateDetails();
			$data['Cities'] = $this->Common_Model->GetCityDetails();
			$data['Counties'] = $this->Common_Model->GetCountyDetails();

		}
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		//$this->load->view('page', $data);
	}
	
	function update_user(){
		$data['content'] = 'index';
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('UserName', '', 'required');
			//$this->form_validation->set_rules('EmployeeUID', '', 'required');
			$this->form_validation->set_rules('EmailID', '', 'required');
			// $this->form_validation->set_rules('ContactNo', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');

			if ($this->form_validation->run() == TRUE) 
			{
				// echo '<pre>';print_r($_POST);exit;
				$edit_userid = $this->input->post('UserUID');
				$login = $this->input->post('LoginID');
				$filepath = $this->profilepictureUpload($_FILES);
				$ImageURL = base_url().'/'.$filepath;
				$UserID = $this->Profile_Model->UpdateUserDetails($this->input->post(),$filepath);
				$result = array("validation_error" => 0,"UserID" => $UserID,'message'=>'Updated Successfully','ImageURL'=>$ImageURL);

				echo json_encode($result);
			}
			else
			{
				$data = array(
					'validation_error' => 1,
					'message' =>'Please Fill The Required Fields',
					'UserName' => form_error('UserName'),
					//'EmployeeUID' => form_error('EmployeeUID'),
					// 'Password' => form_error('Password'),
					'UserEmailID' => form_error('UserEmailID'),
					'UserContactNo' => form_error('UserContactNo'),
				);
				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}
	}


	function profilepictureUpload()
	{	

		error_reporting(E_ALL);
		$upload_url = FCPATH."uploads/avatar/";
		//Check if the directory already exists.
		if(!is_dir($upload_url)){
			//Directory does not exist, so lets create it.
			mkdir($upload_url, 0755, true);
		}
		if (isset($_FILES['image_upload']))
		{  
			$extension = pathinfo($_FILES['image_upload']['name'][0],PATHINFO_EXTENSION);


			$extensionArray = array('png','jpg','jpeg','gif');
			if(in_array($extension, $extensionArray))
			{

				if($_FILES['image_upload']['size'][0] < 10485760)
				{
					if(is_uploaded_file($_FILES['image_upload']['tmp_name'][0]))
					{
						$file_name = $_FILES['image_upload']['name'][0];
						$sourcePath = $_FILES['image_upload']['tmp_name'][0];

						$temp = explode(".", $_FILES["image_upload"]["name"][0]);
						$newfilename = $this->input->post('UserUID').'-'.date('m_d_Y_H_i_s');
						$destination_path = $upload_url.$newfilename.'.'.$extension;          
						try
						{
							if(move_uploaded_file($sourcePath, $destination_path))
							{
								return "uploads/avatar/".$newfilename.'.'.$extension;
							}                
						}
						catch (Exception $e)
						{
							return false;
						}            
					}  
				}else{
					return false;
				}
			}else{
				return false;
			}

		}
		else
		{
			return false;
		}
	}

	


	function SaveAbstractor()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('FirstName', '', 'required');
			$this->form_validation->set_rules('ContactNumber', '', 'required');
			$this->form_validation->set_rules('AbstractorEmail', '', 'required');
			$this->form_validation->set_rules('AbstractorAddress1', '', 'required');
			$this->form_validation->set_rules('AbstractorZipCode', '', 'required');
			$this->form_validation->set_rules('AbstractorCityUID', '', 'required');
			$this->form_validation->set_rules('AbstractorCountyUID', '', 'required');
			$this->form_validation->set_rules('AbstractorStateUID', '', 'required');
			$this->form_validation->set_rules('AbstractorUID', '', 'required');
			$this->form_validation->set_rules('EOStatus', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			if ($this->form_validation->run() == TRUE) 
			{
				$LastInsertAbstractorUID = $this->input->post('AbstractorUID');
				$ApprovalUID = $this->Profile_Model->SaveAbstractor_basicdetails($this->input->post());
				if($ApprovalUID){
					$res = '';

					//upload_file					

					if(isset($_FILES['upload_file']))
					{

						$ApprovalUID = $this->Profile_Model->SaveAbstractor_expirydetails($this->input->post(),true);

						$directoryName = 'uploads/abstractordocuments/';

						//Check if the directory already exists.
						if(!is_dir($directoryName)){
							//Directory does not exist, so lets create it.
							mkdir($directoryName, 0755, true);
						}


						$Count = count($_FILES['upload_file']);
						for ($i=0; $i < $Count; $i++) 
						{ 
							$OrderDocs_Path = 'uploads/abstractordocuments/';

							if (!is_dir($OrderDocs_Path)) {
								mkdir($OrderDocs_Path, 0777, true);
							} 	

							if(is_uploaded_file($_FILES['upload_file']['tmp_name'][$i]))
							{
								$SourcePath = $_FILES['upload_file']['tmp_name'][$i];
								$DocumentFileName = $_FILES['upload_file']['name'][$i];
								$DocumentName = $this->input->post('DocumentTypeSelect');
								$Path = "uploads/abstractordocuments/".$DocumentFileName;
								//$this->Profile_Model->DeleteDocument($LastInsertAbstractorUID, $this->input->post('DocumentTypeSelect'),$Path);
								move_uploaded_file($SourcePath, $Path);	
								if(isset($_POST['ContractPackage'])){
									$IsContractorPackage=$_POST['ContractPackage'];
								}else{
									$IsContractorPackage=0;
								}
								$res .= $this->Profile_Model->SaveAbstractordoc($LastInsertAbstractorUID,$DocumentName,$Path,$IsContractorPackage,$ApprovalUID);
							}
						}
					}else{

						$Approvalrequired = $this->Approvalrequired($this->input->post());

						$ApprovalUID = $this->Profile_Model->SaveAbstractor_expirydetails($this->input->post(),$Approvalrequired);

					}



					if(isset($_FILES['upload_file']))
					{
						if($res)
						{
							$data = array("validation_error" => 1,'message'=>'Success', 'LastInsertAbstractorUID' =>$LastInsertAbstractorUID);
							echo json_encode($data);exit;
						}
					}else{
						$data = array("validation_error" => 1,'message'=>'Success', 'LastInsertAbstractorUID' =>$LastInsertAbstractorUID);
						echo json_encode($data);exit;
					}
				}
				$data = array("validation_error" => 0,'message'=>'Error');
				echo json_encode($data);exit;

			}else{

				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'validation_error' => 0,
					'message' => $Msg,
					'EOStatus' => form_error('EOStatus'),
					'FirstName' => form_error('FirstName'),
					'ContactNumber' => form_error('ContactNumber'),
					'AbstractorEmail' => form_error('AbstractorEmail'),
					'AbstractorAddress1' => form_error('AbstractorAddress1'),
					'AbstractorZipCode' => form_error('AbstractorZipCode'),
					'AbstractorCityUID' => form_error('AbstractorCityUID'),
					'AbstractorCountyUID' => form_error('AbstractorCountyUID'),
					'AbstractorStateUID' => form_error('AbstractorStateUID'),
				);
				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}

		}
	}

	function Approvalrequired($postAbstractordetails){

		$Existdetails = $this->Profile_Model->get_abstractor($postAbstractordetails['AbstractorUID']);

		if(($Existdetails['LicenseExpiryDate'] == '0000-00-00')) {
			$Existdetails['LicenseExpiryDate'] = NULL;
		}


		$ExistLicenseExpiryDate = ($Existdetails['LicenseExpiryDate'] == NULL) ? $Existdetails['LicenseExpiryDate'] : Date('Y-m-d',strtotime($Existdetails['LicenseExpiryDate']));
		$postLicenseExpiryDate = ($postAbstractordetails['LicenseExpiryDate'] == NULL) ? $postAbstractordetails['LicenseExpiryDate'] : Date('Y-m-d',strtotime($postAbstractordetails['LicenseExpiryDate']));



		if(($Existdetails['EAndOExpiryDate'] == '0000-00-00 00:00:00')) {
			$Existdetails['EAndOExpiryDate'] = NULL;
		}
		$ExistExpiryDate = ($Existdetails['EAndOExpiryDate'] == NULL) ? $Existdetails['EAndOExpiryDate'] : Date('Y-m-d H:i:s',strtotime($Existdetails['EAndOExpiryDate']));
		$PostExpiryDate = ($postAbstractordetails['ExpiryDate'] == NULL) ? $postAbstractordetails['ExpiryDate'] : Date('Y-m-d H:i:s',strtotime($postAbstractordetails['ExpiryDate']));

		if($Existdetails['LicenseNo'] != $postAbstractordetails['LicenseNo'] || $ExistLicenseExpiryDate != $postLicenseExpiryDate || 

			$Existdetails['EAndOStatus'] != $postAbstractordetails['EOStatus'] || $Existdetails['EAndORecoveryAmt'] != $postAbstractordetails['EORecoveryAmount'] 

			|| $ExistExpiryDate != $PostExpiryDate ||$Existdetails['EAndOPolicyLimit'] != $postAbstractordetails['Policy'] 

			|| $Existdetails['EAndOInsurance'] != $postAbstractordetails['Insurance'] || $Existdetails['FaxNo'] != $postAbstractordetails['FaxNo']){
			return true;

	} else{

		return false;
	}

}

function setDefaultclient()
{
	if ($this->input->server('REQUEST_METHOD') === 'POST')
	{
		$DefaultClientUID = $this->input->post('adv_CustomerUID');
		if(!empty($DefaultClientUID)) {
			$this->session->set_userdata( array('DefaultClientUID'=>$DefaultClientUID));
			$mRole = $this->Common_Model->get_row('mRole', ['RoleUID'=>$this->session->userdata('RoleType')]);
			echo json_encode(array('DefaultScreen'=>$mRole->DefaultScreen));exit;
		}
	}
	echo json_encode(array('DefaultScreen'=>base_url()));exit;
}

function getuserDetails(){
	$UserID = $this->session->userdata('UserUID');
	$getdetails = $this->Profile_Model->GetUserProfile($UserID); 	
	echo json_encode($getdetails);
}

function ProfileSettingsUpdate(){

	$UserID = $this->session->userdata('UserUID');
	$postdata  = $this->input->post();
	$updateProfileSettings  = $this->Profile_Model->updateProfileSettings($postdata , $UserID); 
	$msg  = '';			
	if($updateProfileSettings){			
		$msg = array("Status"=>1 , "Message"=>"Profile Settings Updated Successfully");
	}else{
		$msg = array("Status"=>0 , "Message"=>"Failed");
	}		
	echo json_encode($msg);

}


}?>
