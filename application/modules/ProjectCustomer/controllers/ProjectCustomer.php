<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProjectCustomer extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ProjectCustomer_model');
		$this->load->model('Documenttype/Documenttype_model');
		$this->load->model('Category/Categorymodel');
		$this->loggedid = $this->session->userdata('UserUID');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		$data['content'] = 'index';
		$data['DocumentDetails'] = $this->ProjectCustomer_model->GetDocument();
		$data['Roles'] = $this->Common_Model->GetCustomer();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	
	function AddProject()
	{
		$data['content'] = 'addproject';
		$data['Product'] = $this->Common_Model->GetProduct();
		$data['addproject'] = $this->Common_Model->GetProject();
		$data['Roles'] = $this->Common_Model->GetCustomer();
		$data['SFTP'] = $this->db->select("*")->from('mSFTP')->get()->result();
		$data['importColumn'] = $this->db->select("ColumnID,ColumnName")->from('mImportColumn')->get()->result();
		$data['catagoryname']=$this->db->select("*")->from("mCategory")->where('Active',1)->get()->result();
		$this->db->select("mUsers.*, mRole.RoleName")->from("mUsers");
		$this->db->join('mRole', 'mUsers.RoleUID=mRole.RoleUID');
		$data['Username'] = $this->db->get()->result();
		$data['Lenders']=$this->db->select("*")->from("mLender")->where('Active',1)->get()->result();
		$data['Fields']=$this->db->select("*")->from("mFields")->get()->result();
		$data['GetUsersList'] = $this->ProjectCustomer_model->GetUsersList();
		$data['Investors'] = $this->Common_Model->get('mInvestors', ['Active', STATUS_ONE]);
		$data['Custodians'] = $this->Common_Model->get('mCustodians', ['Active', STATUS_ONE]);
		$data['TPOLenders'] = $this->Common_Model->get('mLender', ['Active', STATUS_ONE]);
		$data['QuestionType'] = $this->Common_Model->get('mQuestionType', ['Active', STATUS_ONE]);
		$data['WorkflowModules'] = $this->Common_Model->GetWorkflowDetaiils();
		//echo '<pre>';print_r($data['QuestionType']);exit;
		// $data['GetProjectUsers'] = $this->ProjectCustomer_model->GetProjectUsers();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditProject()
	{
		// ini_set("display_errors", newvalue);
		// error_reporting(E_ALL);
		$ProjectUID = $this->uri->segment(3);
		$data['DocumentDetails'] = $this->db->select("mProjectCustomer.*,mProjectCustomer.Active,mProducts.ProductUID,mProducts.ProductName")->from("mProjectCustomer")->join('mProducts','mProducts.ProductUID=mProjectCustomer.ProductUID', 'left')->where(array('ProjectUID'=>$this->uri->segment(3)))->get()->row();
		$CustomerUID = $data['DocumentDetails']->CustomerUID;
		// echo "<pre>"; print_r($data['DocumentDetails']); exit();
		$data['content'] = 'updateproject';
		$data['Product'] = $this->Common_Model->GetProduct();
		$data['project'] = $this->Common_Model->GetProject();
		$data['Category'] = $this->Common_Model->GetCustomer();
		$data['SFTP'] = $this->db->select("*")->from('mSFTP')->get()->result();
		$data['importColumn'] = $this->db->select("ColumnID,ColumnName")->from('mImportColumn')->get()->result();
		$data['catagoryname']=$this->db->select("*")->from("mCategory")->where(array('Active'=>1,'CustomerUID'=>$this->parameters['DefaultClientUID']))->get()->result();
		//$data['newcatagoryname']=$this->db->select("*")->from("mClientCategory")->where('Active',1)->get()->result();
		$this->db->select("mUsers.*, mRole.RoleName")->from("mUsers");
		$this->db->join('mRole', 'mUsers.RoleUID=mRole.RoleUID');
		$data['Username'] = $this->db->get()->result();
		$data['Fields']=$this->db->select("*")->from("mFields")->get()->result();
		$data['Lenders'] = $this->db->select("*")->from("mLender")->where('Active',1)->get()->result();
		$ProjectUID = $this->uri->segment(3);
		$data['GetUsersList'] = $this->ProjectCustomer_model->GetUsersListUpdate($ProjectUID);
		$data['GetProjectUsers'] = $this->ProjectCustomer_model->GetProjectUsers($ProjectUID);
		$data['ProjectInvestors'] = $this->ProjectCustomer_model->getProjectInvestor($ProjectUID);		
		$data['ProjectCustodians'] = $this->ProjectCustomer_model->getProjectCustodian($ProjectUID);
		$data['TPOLenders'] = $this->ProjectCustomer_model->getProjectLenders($ProjectUID);
		$data['ProjectQuestionType'] = $this->ProjectCustomer_model->getProjectQuestions($ProjectUID);
		$data['ProjectUID'] = $ProjectUID;
		//$ProjectDocarray=$this->ProjectCustomer_model->GetProjectCategoryDocTypeByID($ProjectUID);
		// echo '<pre>';print_r($data['catagoryname']);
		//echo '<pre>';print_r(count($ProjectDocarray));exit;
		$data['WorkflowModules'] = $this->Common_Model->GetWorkflowDetaiils($CustomerUID);
		$ProjectCustodian = [];
		foreach ($data['ProjectCustodians'] as $key => $value) {
			$ProjectCustodian[] = $value->CustodianUID;
		}//

		$ProjectInvestor = [];
		foreach ($data['ProjectInvestors'] as $key => $value) {
			$ProjectInvestor[] = $value->InvestorUID;
		}

		$ProjectLender = [];
		foreach ($data['TPOLenders'] as $key => $value) {
			$ProjectLender[] = $value->LenderUID;
		}

		$ProjectQuestionTypes = [];
		foreach ($data['ProjectQuestionType'] as $key => $value) {
			$ProjectQuestionTypes[] = $value->QuestionTypeUID;
		} 
		  $testarray=$this->Common_Model->projectcategory($ProjectUID); 
		  
		$data['Investors'] = $this->ProjectCustomer_model->getInvestors_not_in($ProjectInvestor);
		$data['Custodians'] = $this->ProjectCustomer_model->getCustodians_not_in($ProjectCustodian);
		$data['AllCustodians'] = $this->ProjectCustomer_model->GetAllCustodians();
		$data['Lenders'] = $this->ProjectCustomer_model->getLenders_not_in($ProjectLender);
		$data['QuestionType'] = $this->ProjectCustomer_model->getQuestions_not_in($ProjectQuestionTypes);

		$data['CustomerProducts'] = $this->Common_Model->GetCustomerProducts($CustomerUID);

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function SaveProject()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ProjectName', '', 'required');
			$this->form_validation->set_rules('ProjectCode', '', 'required');
			$this->form_validation->set_rules('CustomerUID', '', 'required');
			$this->form_validation->set_rules('Priority', '', 'required');
			$this->form_validation->set_rules('PriorityTime', '', 'required');
			$this->form_validation->set_rules('ExportLevel', '', 'required');
			$this->form_validation->set_rules('BulkImportFormat', '', 'required');
			$this->form_validation->set_rules('ProductUID', '', 'required');
			// $this->form_validation->set_rules('projectcategory[]', '', 'required');
			// $this->form_validation->set_rules('projectdocument[]', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');

		
			$post = $this->input->post();
			$data = [];

			// echo "<pre>"; print_r($_POST);exit();
			$data['ProjectName'] = $post['ProjectName'];
			$data['ProjectCode']=$post['ProjectCode'];
			$data['CustomerUID']=$post['CustomerUID'];
			$data['Priority']=$post['Priority'];
			$data['ProductUID']=$post['ProductUID'];
			$data['PriorityTime']=$post['PriorityTime'];
			$data['Active']=isset($post['Active']) ? 1 : 0;
			$data['BulkImportFormat']=$post['BulkImportFormat'];
			$data['ExportLevel']=$post['ExportLevel'];
			$data['SFTPUID']=$post['SFTPUID'];
			$data['SFTPExportUID'] = $post['SFTPExportUID'];
			$data['IsDocumentCheckIn'] = isset($post['IsDocumentCheckIn']) ? 1 : 0;
			$data['IsStacking'] = isset($post['IsStacking']) ? 1 : 0;
			$data['IsAuditing'] = isset($post['IsAuditing']) ? 1 : 0;
			$data['IsShipping'] = isset($post['IsShipping']) ? 1 : 0;
			$data['IsReview'] = isset($post['IsReview']) ? 1 : 0;
			$data['IsExport'] = isset($post['IsExport']) ? 1 : 0;
			$data['IsAutoExport'] = isset($post['IsAutoExport']) ? 1 : 0;
			$data['ZipImport'] = isset($post['ZipImport']) ? 1 : 0;
			$data['DataEntryDisplay']=$post['DataEntryDisplay'];
			$data['ExportType']=$post['ExportType'];
			$data['OCRWorkflowModuleUID']=$post['OCRWorkflowModuleUID'];
			$data['DocInstance']=$post['DocInstance'];
			$data['importColumn']=(isset($post['importColumn']) ? $post['importColumn'] : '');
			$data['IsFolderExport']=(isset($post['IsFolderExport']) ? $post['IsFolderExport'] : '0');
			if ($this->form_validation->run() == true) 
			{
				$result=$this->ProjectCustomer_model->GetDocumentType($data);

				
				if( $result !=0)
				{
					
					$res = array('Status' => 0,'message'=>'Project added Successsfully','url'=>$result);
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 1,'message'=>'Failed to add project');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'ProductUID' => form_error('ProductUID'),
					'ProjectName' => form_error('ProjectName'),
					'ProjectCode' => form_error('ProjectCode'),
					'CustomerUID' => form_error('CustomerUID'),
					'Priority' => form_error('Priority'),
					'PriorityTime' => form_error('PriorityTime'),
					'BulkImportFormat' => form_error('BulkImportFormat'),
					'ExportLevel' => form_error('ExportLevel'),
					//'StacxDocuments' => form_error('StacxDocuments'),
					'DataEntryDisplay' => form_error('DataEntryDisplay'),
					'ExportType' => form_error('ExportType'),
					'FieldsName' => form_error('FieldsName[]'),
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

	function SaveUpdate()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ProjectName', '', 'required');
			$this->form_validation->set_rules('ProjectCode', '', 'required');
			$this->form_validation->set_rules('CustomerUID', '', 'required');
			$this->form_validation->set_rules('Priority', '', 'required');
			$this->form_validation->set_rules('PriorityTime', '', 'required');
			$this->form_validation->set_rules('ExportLevel', '', 'required');
			$this->form_validation->set_rules('ProductUID', '', 'required');
			$this->form_validation->set_rules('BulkImportFormat', '', 'required');
			//$this->form_validation->set_rules('StacxDocuments', '', 'required');
			$this->form_validation->set_rules('DataEntryDisplay', '', 'required');
			$this->form_validation->set_rules('ExportType', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');
			
			$post = $this->input->post();
			// echo'<pre>';print_r($post);exit(); 
			$data = [];

			$data['ProductUID']=$post['ProductUID'];
			$data['OCRWorkflowModuleUID']=$post['OCRWorkflowModuleUID'];
			$data['ProjectName'] = $post['ProjectName'];
			$data['ProjectUID'] = $post['ProjectUID'];
			$data['ProjectCode']=$post['ProjectCode'];
			$data['CustomerUID']=$post['CustomerUID'];
			$data['Priority']=$post['Priority'];
			$data['PriorityTime']=$post['PriorityTime'];
			$data['Active']=isset($post['Active']) ? 1 : 0;
			$data['BulkImportFormat']=$post['BulkImportFormat'];
			$data['ExportLevel']=$post['ExportLevel'];
			$data['SFTPUID']=$post['SFTPUID'];
			$data['SFTPExportUID']=$post['SFTPExportUID'];
			$data['IsDocumentCheckIn'] = isset($post['IsDocumentCheckIn']) ? 1 : 0;
			$data['IsStacking'] = isset($post['IsStacking']) ? 1 : 0;
			$data['IsAuditing'] = isset($post['IsAuditing']) ? 1 : 0;
			$data['IsShipping'] = isset($post['IsShipping']) ? 1 : 0;
			$data['IsReview'] = isset($post['IsReview']) ? 1 : 0;
			$data['IsExport'] = isset($post['IsExport']) ? 1 : 0;
			$data['IsAutoExport'] = isset($post['IsAutoExport']) ? 1 : 0;
			$data['ZipImport'] = isset($post['ZipImport']) ? 1 : 0;
			$data['DataEntryDisplay']=$post['DataEntryDisplay'];
			$data['ExportType']=$post['ExportType'];
			$data['DocInstance']=$post['DocInstance'];
			$data['AutoImportColumn']=$post['importColumn'];
			$data['ExportAsFolder'] = isset($post['IsFolderExport']) ? 1 : 0;
			if ($this->form_validation->run() == true) 
			{
				$result=$this->ProjectCustomer_model->GetDocumentTypeUpdate($data);
				if( $result== 1)
				{
					$res = array('Status' => 2,'message'=>'Project Updated Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 2,'message'=>'Project Updated Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'ProductUID' => form_error('ProductUID'),
					'ProjectName' => form_error('ProjectName'),
					'ProjectCode' => form_error('ProjectCode'),
					'CustomerUID' => form_error('CustomerUID'),
					'Priority' => form_error('Priority'),
					'PriorityTime' => form_error('PriorityTime'),
					'BulkImportFormat' => form_error('BulkImportFormat'),
					'ExportLevel' => form_error('ExportLevel'),
					//'StacxDocuments' => form_error('StacxDocuments'),
					'DataEntryDisplay' => form_error('DataEntryDisplay'),
					'ExportType' => form_error('ExportType'),
					'FieldsName' => form_error('FieldsName[]'),
					'type' => 'danger',
				);


				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				//$res = array('Status' => 4,'detailes'=>$data);
				echo json_encode($data);
			}

		}

	}

	function GetDocumentTypeByCategory()
	{
		$CategoryUID = $this->input->post('CategoryUID'); 
		$documentID = []; 
	
		foreach ($CategoryUID as $key => $value) {
			$documentID[] = $this->ProjectCustomer_model->GetDocumentTypeByCategory($value);	
		}	
		if($documentID != 0)
		{
			echo json_encode($documentID);
		} else {
			echo json_encode('');
		}
	}


	function GetDocumentTypeByCategoryUID()
	{
		$CategoryUID = $this->input->post('CategoryUID'); 
		//$documentID = []; 
	
		
			$documentID= $this->ProjectCustomer_model->GetDocumentTypeByCategory($CategoryUID);	
			
		if($documentID != 0)
		{
			echo json_encode($documentID);
		} else {
			echo json_encode('');
		}
	}


	public function project()
	{
		$data['content'] = 'project';
		$data['DocumentDetails'] = $this->ProjectCustomer_model->GetDocument();
		$data['catagoryname']=$this->db->select("*")->from("mCategory")->where('Active',1)->get()->result();
		$data['Roles'] = $this->Common_Model->GetCustomer();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function externalScript(){
		// Get Unique Project ID's
		$documents=$this->db->query("SELECT ProjectUID FROM mProjectDocumentType GROUP BY ProjectUID")->result();
		
		$projectuid=[];
		
		foreach ($documents as $key => $value) {
			$projectuid[]=$value->ProjectUID;
			
		}
		// Project ID's comma separated
		$ProjectUID = implode (",", $projectuid);

		// Fetch all document types based on category id and project id's not present in mProjectDocumentType

		$query=$this->db->query("SELECT mProjectCategory.ProjectUID,mDocumentType.DocumentTypeUID FROM mDocumentType LEFT JOIN mProjectCategory on mProjectCategory.CategoryUID=mDocumentType.CategoryUID LEFT JOIN mCategory ON mCategory.CategoryUID=mDocumentType.CategoryUID WHERE mProjectCategory.ProjectUID NOT IN (".$ProjectUID.")   ORDER BY mProjectCategory.ProjectUID,mDocumentType.DocumentTypeUID")->result();
		
		$data=$query;
		
		// Insert into mProjectDocumentType 
		$i=1;
		if ($data) {		
			foreach ($data as $key => $value) {
				$query=$this->db->query("INSERT INTO mProjectDocumentType (ProjectUID,DocumentTypeUID,CreatedOn,CreatedBy,Active,DocPosition) Values (".$value->ProjectUID.",".$value->DocumentTypeUID.",CURDATE(),1,1,".$i.")");
				$i++;
			}
		}
		exit;		
	}


	function getCategoryByDocId()
	{
		$DocumentTypeUID = $this->input->post('DocumentTypeUID'); 
		
		$CategoryUID= $this->ProjectCustomer_model->getCategoryByDocId($DocumentTypeUID);	
			//echo "<pre>";print_r($CategoryUID);
		if(!empty($CategoryUID))
		{
			echo json_encode($CategoryUID);
		} else {
			echo json_encode('');
		}
	}

} 

?>
