<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProjectCustomer_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GetDocumentType($post)
	{
		

		$users=array('ProjectName'=>$post['ProjectName'],'CustomerUID'=>$post['CustomerUID'],'ProjectCode'=>$post['ProjectCode'],'Priority'=>$post['Priority'],'ProductUID'=>$post['ProductUID'],'PriorityTime'=>$post['PriorityTime'],'Active'=>1,'BulkImportFormat'=>$post['BulkImportFormat'],'IsAutoExport'=>$post['IsAutoExport'],'ZipImport'=>$post['ZipImport'],'ExportLevel'=>$post['ExportLevel'],'SFTPUID'=>$post['SFTPUID'],'SFTPExportUID'=>$post['SFTPExportUID'],'DataEntryDisplay'=>$post['DataEntryDisplay'],'ExportType'=>$post['ExportType'],'OCRWorkflowModuleUID'=>$post['OCRWorkflowModuleUID'],'DocInstance'=>$post['DocInstance'],'AutoImportColumn'=>$post['importColumn'],'ExportAsFolder'=>$post['IsFolderExport']);


		 $this->db->insert('mProjectCustomer',$users);
		 $ProjectUID = $this->db->insert_id();

		 if ($ProjectUID) {
		 	return $ProjectUID;
		 }else{
		 	return 0;
		 }
		 
	}
	function GetDocumentTypeUpdate($post)
	{
		$users=array('ProjectName'=>$post['ProjectName'],'ProjectCode'=>$post['ProjectCode'], 'CustomerUID'=>$post['CustomerUID'], 'Priority'=>$post['Priority'], 'ProductUID'=>$post['ProductUID'],'PriorityTime'=>$post['PriorityTime'],'Active'=>$post['Active'],'BulkImportFormat'=>$post['BulkImportFormat'],'IsAutoExport'=>$post['IsAutoExport'],'ZipImport'=>$post['ZipImport'],'ExportLevel'=>$post['ExportLevel'],'SFTPUID'=>$post['SFTPUID'],'SFTPExportUID'=>$post['SFTPExportUID'],'DataEntryDisplay'=>$post['DataEntryDisplay'],'ExportType'=>$post['ExportType'],'OCRWorkflowModuleUID'=>$post['OCRWorkflowModuleUID'],'DocInstance'=>$post['DocInstance'],'AutoImportColumn'=>$post['AutoImportColumn'],'ExportAsFolder'=>$post['ExportAsFolder']);
		 
		 $CustodianUID=$this->input->post('Custodians');
		 $Custodian_Array = [];
		 
		 $InvestorUID=$this->input->post('Investors');
		 $Investor_Array = [];
		 
		 $TPO=$this->input->post('TPO');
		 $TPO_Array = [];

		 $Question=$this->input->post('Questions');
		 $Question_Array = [];

		 $this->db->trans_begin();
		
		$this->db->where(array('ProjectUID' => $post['ProjectUID']));
		$this->db->update('mProjectCustomer',$users);

		$ProjectUID = $post['ProjectUID'];
		$data=$this->input->post('projectcategory');
		$prodoc=$this->input->post('projectdocument');
		$fieldname=$this->input->post('FieldsName');
		// $Userdata=$this->input->post('Users');
		$ProjectUserUID=$this->input->post('ProjectUserUID');
		$RecevieEmailChecked=$this->input->post('RecevieEmailChecked');
		$IsAccessible=$this->input->post('IsAccessible');
		

		// echo '<pre>';print_r($data);
		// echo '<pre>';print_r($prodoc);exit;

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectCategory');

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectUser'); 
		
		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectDocumentType');

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectFields');

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectCustodian');

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectInvestor');

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectLender');

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectQuestion');
		// print_r($ProjectUserUID);exit;
		$i=1;
		foreach($data as $value){
			$projectcategory=array('ProjectUID'=>$ProjectUID,'CategoryUID'=> $value,'CreatedOn'=> Date('Y-m-d H:i:s', strtotime("now")),'CreatedBy'=> $this->loggedid,'Active'=>1,'CatPosition'=>$i);
			$this->db->insert('mProjectCategory', $projectcategory);
			$i++;
		}
		$i=1;
		foreach($prodoc as $value){
			$projectdocument=array('ProjectUID'=>$ProjectUID,'DocumentTypeUID'=> $value,'CreatedOn'=> Date('Y-m-d H:i:s', strtotime("now")),'CreatedBy'=> $this->loggedid,'Active'=>1,'DocPosition'=>$i);
			$this->db->insert('mProjectDocumentType', $projectdocument);
			$i++;
		}
		
		// foreach($Userdata as $value){
		// 	$SelectUsers=array('ProjectUID'=>$ProjectUID,'UserUID'=> $value);
		// 	$this->db->insert('mProjectUser', $SelectUsers);
		// }

		foreach($fieldname as $value){
			$SelectFields=array('ProjectUID'=>$ProjectUID,'FieldUID'=> $value);
			$this->db->insert('mProjectFields', $SelectFields);
		}

		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->delete('mProjectLender'); 
		// $Lenderdata =$this->input->post('Lenders');
		// foreach ($Lenderdata as $value) {
		// 	$SelectLenders = array('ProjectUID' => $ProjectUID, 'LenderUID' => $value);
		// 	$this->db->insert('mProjectLender', $SelectLenders);
		// }
		// if (!empty($ProjectUserUID) && $post['IsAutoExport'] == 1) {
		// 	if (isset($AccessUsersID) && $AccessUsersID!='null'  && in_array($AccessUsersID,$ProjectUserUID)) {
		// 		$ProjectUserUIDInsert=array('ProjectUID'=>$ProjectUID,'UserUID'=> $AccessUsersID,'CanReceiveExceptionMails'=>1,'IsAccessible'=>1);
		// 		$this->db->insert('mProjectUser', $ProjectUserUIDInsert);
		// 	}
		// }
			foreach($ProjectUserUID as $key => $value){
				$RecevieEmailChecked = $this->input->post('RecevieEmailChecked');
				$IsAccessible = $this->input->post('IsAccessible');
				$CheckBox = $RecevieEmailChecked[$key];
				$Access = $IsAccessible[$key];
				if($CheckBox == 'true' || $CheckBox == 'on'){
				// echo '<pre>'; print_r('c'); exit;
					 $Receive = 1;
				}
				else{
					 $Receive = 0;
				}

				if ($post['IsAutoExport'] == 1) {
				
					if($Access == 'true' || $Access == 'on'){
					
						 $Status = 1;
					}
					else{
						 $Status = 0;
					}
				}else{
					$Status = 0;
				}
				$ProjectUserUIDInsert=array('ProjectUID'=>$ProjectUID,'UserUID'=> $value,'CanReceiveExceptionMails'=>$Receive,'IsAccessible'=>$Status,'ModuleUID'=>$ProjectModuleUID[$value],'ModuleName'=>$ProjectModuleName[$value]);
				
					$this->db->insert('mProjectUser', $ProjectUserUIDInsert);
				
			}

		// foreach ($CustodianUID as $key => $value) {
		// 	$ProjectCustodian_row['ProjectUID'] = $ProjectUID;
		// 	$ProjectCustodian_row['CustodianUID'] = $value;
		// 	$Custodian_Array[] = $ProjectCustodian_row;
		// }

		// if (!empty($Custodian_Array)) {
		// 	$this->db->insert_batch('mProjectCustodian', $Custodian_Array);
		// }


		foreach ($InvestorUID as $key => $value) {
			$ProjectInvestor_row['ProjectUID'] = $ProjectUID;		
	        $arr  = explode (",", $value);
			$ProjectInvestor_row['InvestorUID'] = $arr[0];
			$ProjectInvestor_row['CustodianUID'] = $arr[1];	
			$Investor_Array[] = $ProjectInvestor_row;

		}


		if (!empty($Investor_Array)) {
			$this->db->insert_batch('mProjectInvestor', $Investor_Array);
		}

		foreach ($TPO as $key => $value) {
			$TPO_row['ProjectUID'] = $ProjectUID;
			$TPO_row['LenderUID'] = $value;
			$TPO_Array[] = $TPO_row;

		}


		if (!empty($TPO_Array)) {
			$this->db->insert_batch('mProjectLender', $TPO_Array);
		}


		foreach ($Question as $key => $value) {
			$Question_row['ProjectUID'] = $ProjectUID;
			$Question_row['QuestionTypeUID'] = $value;
			$Question_Array[] = $Question_row;
		}


		if (!empty($Question_Array)) {
			$this->db->insert_batch('mProjectQuestion', $Question_Array);
		}



		if($this->db->trans_status() === true)
		{
			$this->db->trans_commit();
			return 1;
		}
		else
		{
			return 0;
		}
	}

	function GetDocument(){
		$this->db->select("mProjectCustomer.*, mCustomer.CustomerName, mCustomer.CustomerUID");
		$this->db->from('mProjectCustomer');
		$this->db->join('mCustomer','mProjectCustomer.CustomerUID=mCustomer.CustomerUID','left');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('mProjectCustomer.CustomerUID',$this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->result();
	}

	function CheckExistUserName($UserUID, $LoginID)
	{																	
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows(); 
	}

	function getProjectCustodian($ProjectUID = '')
	{
		$this->db->select('*');
		$this->db->from('mProjectCustodian');
		$this->db->join('mCustodians', 'mCustodians.CustodianUID = mProjectCustodian.CustodianUID');

		if (!empty($ProjectUID)) {
			$this->db->where('mProjectCustodian.ProjectUID', $ProjectUID);
		}

		$this->db->order_by('mCustodians.CustodianUID', 'ASC');
		return $this->db->get()->result();
	}

	function getProjectLenders($ProjectUID = '')
	{
		$this->db->select('*');
		$this->db->from('mProjectLender');
		$this->db->join('mLender', 'mLender.LenderUID = mProjectLender.LenderUID');

		if (!empty($ProjectUID)) {
			$this->db->where('mProjectLender.ProjectUID', $ProjectUID);
		}

		$this->db->order_by('mLender.LenderUID', 'ASC');
		return $this->db->get()->result();
	}

	function getProjectInvestor($ProjectUID = '')
	{
		$this->db->select('*');
		$this->db->from('mProjectInvestor');
		$this->db->join('mInvestors', 'mInvestors.InvestorUID = mProjectInvestor.InvestorUID');
		$this->db->join('mCustodians', 'mCustodians.CustodianUID = mProjectInvestor.CustodianUID');

		if (!empty($ProjectUID)) {
			$this->db->where('mProjectInvestor.ProjectUID', $ProjectUID);
		}

		$this->db->order_by('mInvestors.InvestorUID', 'ASC');
		return $this->db->get()->result();
	}


	function getInvestors_not_in($Investors = [])
	{
		$this->db->select('*');
		$this->db->from('mInvestors');

		if (!empty($Investors)) {
			$this->db->where_not_in('mInvestors.InvestorUID', $Investors);
		}

		$this->db->where('mInvestors.Active', STATUS_ONE);

		$this->db->order_by('mInvestors.InvestorUID', 'ASC');
		return $this->db->get()->result();
		
	}

	function getCustodians_not_in($Custodians = [])
	{
		$this->db->select('*');
		$this->db->from('mCustodians');

		if (!empty($Custodians)) {
			$this->db->where_not_in('mCustodians.CustodianUID', $Custodians);
		}
		$this->db->where('mCustodians.Active', STATUS_ONE);

		$this->db->order_by('mCustodians.CustodianUID', 'ASC');
		return $this->db->get()->result();
		
	}


	function getProjectQuestions($ProjectUID = ''){
		$this->db->select('*');
		$this->db->from('mProjectQuestion');
		$this->db->join('mQuestionType', 'mQuestionType.QuestionTypeUID = mProjectQuestion.QuestionTypeUID');

		if (!empty($ProjectUID)) {
			$this->db->where('mProjectQuestion.ProjectUID', $ProjectUID);
		}

		$this->db->order_by('mQuestionType.QuestionTypeUID', 'ASC');
		return $this->db->get()->result();

	}

	function getQuestions_not_in($QuestionTypes = [])
	{
		$this->db->select('*');
		$this->db->from('mQuestionType');

		if (!empty($QuestionTypes)) {
			$this->db->where_not_in('mQuestionType.QuestionTypeUID', $QuestionTypes);
		}

		$this->db->where('mQuestionType.Active', STATUS_ONE);
	

		$this->db->order_by('mQuestionType.QuestionTypeUID', 'ASC');
		return $this->db->get()->result();

		
	}







	function getLenders_not_in($Lenders = [])
	{
		$this->db->select('*');
		$this->db->from('mLender');

		if (!empty($Lenders)) {
			$this->db->where_not_in('mLender.LenderUID', $Lenders);
		}
		$this->db->where('mLender.Active', STATUS_ONE);

		$this->db->order_by('mLender.LenderUID', 'ASC');
		return $this->db->get()->result();
		
	}
	function SelectUsers($ProjectUID)
	{
		$this->db->select('*,mUsers.UserName AS CateName');
		$this->db->from('mProjectUser');
		$this->db->join('mUsers', 'mProjectUser.UserUID = mUsers.UserUID', 'LEFT');
		$this->db->where('ProjectUID', $ProjectUID);
		$SelectUsers = $this->db->get()->result();
		$userarray = [];
		foreach ($SelectUsers as $row) {

			$Users = array($row->UserUID);
			$userarray = array_merge($userarray, $Users);
		}
		return $userarray;

	}

	function SelectFields($ProjectUID)
	{
		$this->db->select('*');
		$this->db->from('mProjectFields');
		//$this->db->join('mFields', 'mProjectFields.FieldUID = mFields.FieldUID', 'LEFT');
		$this->db->where('ProjectUID', $ProjectUID);
		$SelectFields = $this->db->get()->result();
		$fieldarray = [];
		foreach ($SelectFields as $row) {

			$Fields = array($row->FieldUID);
			$fieldarray = array_merge($fieldarray, $Fields);
		}
		return $fieldarray;

	}

	function SelectLenders($ProjectUID)
	{
		$this->db->select('*');
		$this->db->from('mProjectLender');
		$this->db->join('mLender', 'mProjectLender.LenderUID = mLender.LenderUID', 'LEFT');
		$this->db->where('ProjectUID', $ProjectUID);
		$SelectUsers = $this->db->get()->result();
		$userarray = [];
		foreach ($SelectUsers as $row) {

			$Users = array($row->LenderUID);
			$userarray = array_merge($userarray, $Users);
		}
		return $userarray;

	}

	function GetDocumentTypeByCategory($CategoryUID)
	{
		$this->db->select('*');
		$this->db->where_in('CategoryUID',$CategoryUID);
		$this->db->where('Active',1);
		$data = $this->db->get('mDocumentType')->result();
		if(count($data)>0)
		{
			return $data;
		} else {
			return 0;
		}
	}
	function ProjectDocumentType($ProjectUID)
	{
		$this->db->select('*');
		$this->db->from('mProjectDocumentType');
		$this->db->where('ProjectUID',$ProjectUID);
		$this->db->where('Active',1);
		$data = $this->db->get()->result();
		if(count($data) > 0 )
		{
			return $data;
		} else {
			return 0;
		}
	}
	function GetUsersList(){

		$this->db->select('*');
		$this->db->from('mUsers');
		$this->db->where_not_in('');
		return $this->db->get()->result();
	}
	function GetProjectUsers($ProjectUID){
		$this->db->select('*,mUsers.UserName,mRole.RoleUID,mRole.RoleName');
		$this->db->from('mProjectUser');
		$this->db->join('mUsers','mProjectUser.UserUID=mUsers.UserUID');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID');
		$this->db->where('mProjectUser.ProjectUID',$ProjectUID);
		return $this->db->get()->result();
	}
	function GetUsersListUpdate($ProjectUID){
		
		$this->db->select('*');
		$this->db->from('mUsers');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID');
		$this->db->where('mUsers.UserUID NOT IN (SELECT UserUID FROM mProjectUser WHERE ProjectUID ='.$ProjectUID.')',NULL,FALSE);
		$this->db->where('mUsers.CustomerUID',$this->parameters['DefaultClientUID']);
		return $this->db->get()->result();
	}


	function GetAllCustodians(){		
		$this->db->select('*');
		$this->db->from('mCustodians');	
		$this->db->where('Active',1);
		return $this->db->get()->result();
	}


	function getProjectDocumentType($ProjectUID)
	{
		$this->db->select('*');
		$this->db->from('mProjectDocumentType');
		$this->db->join('mDocumentType','mProjectDocumentType.DocumentTypeUID = mDocumentType.DocumentTypeUID','left');
		$this->db->join('mCategory','mDocumentType.CategoryUID = mCategory.CategoryUID','left');
		$this->db->where('mProjectDocumentType.ProjectUID',$ProjectUID);
		$this->db->where('mProjectDocumentType.Active',1);
		$this->db->order_by('mDocumentType.DocumentTypeName');
		$data = $this->db->get()->result();
		if(count($data) > 0 )
		{
			return $data;
		} else {
			return 0;
		}
	}


		  function projectcategory($ProjectUID)
	  {
		$this->db->select('mProjectCategory.*,mCategory.CategoryName AS CateName');
		$this->db->from('mProjectCategory');
		$this->db->join('mCategory', 'mProjectCategory.CategoryUID = mCategory.CategoryUID', 'LEFT');
		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->order_by('mCategory.CategoryName');
		$ProjectCategory = $this->db->get()->result();
		
		return $ProjectCategory;
		
	}


	function getProjectDocumentTypeWithCategory($ProjectUID){
		$this->db->select('*');
		$this->db->from('mProjectDocumentType');
		$this->db->join('mDocumentType','mProjectDocumentType.DocumentTypeUID = mDocumentType.DocumentTypeUID','left');
		$this->db->join('mCategory','mDocumentType.CategoryUID = mCategory.CategoryUID','left');
		$this->db->join('mProjectCategory','mProjectCategory.CategoryUID = mCategory.CategoryUID','left');
		$this->db->where('mProjectDocumentType.ProjectUID',$ProjectUID);
		$this->db->where('mProjectDocumentType.Active',1);
		$this->db->order_by('mCategory.CategoryName');
		//$this->db->order_by('mProjectDocumentType.DocPosition');
		$this->db->group_by('mProjectDocumentType.DocumentTypeUID');
		$data = $this->db->get()->result();
		if(count($data) > 0 )
		{
			return $data;
		} else {
			return 0;
		}

		// $query=$this->db->query("SELECT * FROM `mProjectDocumentType` Left JOIN mDocumentType on mDocumentType.DocumentTypeUID=mProjectDocumentType.DocumentTypeUID WHERE mProjectDocumentType.ProjectUID='".$OrderDetails->ProjectUID."'");
		// return $query->result();
	}


	function getCategoryByDocId($DocumentTypeUID){
		$query=$this->db->query("SELECT mCategory.* FROM `mCategory` Left JOIN mDocumentType on mDocumentType.CategoryUID=mCategory.CategoryUID WHERE mDocumentType.DocumentTypeUID=".$DocumentTypeUID);
		return $query->row_array();
	}

	public function GetAccessibleUserList($ProjectUID){
	$this->db->select('UserUID');
		$this->db->from('mProjectUser');
		$this->db->where('mProjectUser.ProjectUID',$ProjectUID);
		$this->db->where('mProjectUser.IsAccessible',1);
		$data = $this->db->get()->row();
		return $data;
	}
}
?>

