<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class DynamicColumn_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function GetDocumentType($post)
	{

		$numbers = 1; // Get Max of ID
		$hash = '';
		while (true) {
			$categoryhash = 'DocumentType' . $numbers;
			$hash = substr(md5($categoryhash), 0, 8);

			$this->db->where('HashCode', $hash);
			$count = $this->db->get('mDocumentType')->num_rows();
			if ($count == 0) {

				break;
			}
			$numbers++;
		}
		$Active = $data['Active'] = isset($post['Active']) ? 1 : 0;

		if ($post['Groups'] == 'empty') {
			$Groups = null;
		} else {
			$Groups = $post['Groups'];
		}
		$ParentDocumentTypeUID = isset($post['ParentDocumentTypeUID']) && !empty($post['ParentDocumentTypeUID']) ? $post['ParentDocumentTypeUID'] : NULL;

		$insert = array('DocumentTypeName' => $post['DocumentTypeName'], 'CategoryUID' => $post['CategoryUID'], 'NamingConventions' => $post['NamingConventions'], 'ScreenCode' => $post['ScreenCode'], 'Groups' => $Groups, 'CustomerUID' => $post['CustomerUID'], 'FieldType' => $post['FieldType'], 'ToMails' => $post['ToMails'], 'EmailTemplate' => $post['EmailTemplate'], 'Active' => 1, 'HashCode' => $hash, 'ParentDocumentTypeUID' => $ParentDocumentTypeUID, 'ChildLabel' => $post['ChildLabel']);

		$update = array('DocumentTypeName' => $post['DocumentTypeName'], 'CategoryUID' => $post['CategoryUID'], 'NamingConventions' => $post['NamingConventions'], 'ScreenCode' => $post['ScreenCode'], 'Groups' => $Groups, 'CustomerUID' => $post['CustomerUID'], 'FieldType' => $post['FieldType'], 'ToMails' => $post['ToMails'], 'EmailTemplate' => $post['EmailTemplate'], 'Active' => $Active, 'HashCode' => $hash, 'ParentDocumentTypeUID' => $ParentDocumentTypeUID, 'ChildLabel' => $post['ChildLabel']);

		if ($post['FieldType'] == 'select') {
			$insert['TableName'] = $post['TableName'];
			$insert['TableKey'] = $post['TableKey'];
			$update['TableName'] = $post['TableName'];
			$update['TableKey'] = $post['TableKey'];
		}

		if (isset($post['DocumentTypeUID'])) {
			$this->db->where(array("DocumentTypeUID" => $post['DocumentTypeUID']));
			$this->db->update('mDocumentType', $update);
			$this->addChildChecklist($post, $Groups, $hash, $Active);
		} else {
			$this->db->insert('mDocumentType', $insert);
			if ($post['childchecklist']) {
				$this->addChildChecklist($post, $Groups, $hash, $Active);
			}
		}
		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	/* 
	*Get Count of record for data table
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */
	function count_all($post)
	{
		$this->db->select("1");
		$this->dynamicQuery($post);
		$query = $this->db->count_all_results();
		return $query;
	}

	/* 
	*Get filtered count for data table
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */
	function count_filtered($post)
	{
		$this->db->select("1");
		$this->dynamicQuery($post);

		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();	
		return $query->num_rows();
	}

	/* 
	*Get Document checklist filtered data
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */
	function getWorkflowdetails($post)
	{

		$this->dynamicQuery($post);


		/* Datatable Search */
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		/* Datatable OrderBy */
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		if ($post['length'] != '') {
			$this->db->limit($post['length'], $post['start']);
		}
		$query = $this->db->get();
		return $query->result();
	}

	/* 
	*Get filtering query for document checklist data table
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */
	function dynamicQuery($post)
	{
		$this->db->select("m1.UserName as CreateUser, m2.UserName as ModifyUser, mQueueColumns.*,mDocumentType.DocumentTypeName,mCustomer.CustomerName,mWorkFlowModules.WorkflowModuleName");
		$this->db->select("CONCAT((SELECT GROUP_CONCAT(DISTINCT StaticQueueName) FROM mStaticQueues WHERE mStaticQueues.StaticQueueUID = mQueueColumns.StaticQueueUIDs LIMIT 1),',',(SELECT GROUP_CONCAT(DISTINCT QueueName) FROM mQueues WHERE mQueues.QueueUID = mQueueColumns.QueueUIDs LIMIT 1)) AS DisplayQueues",FALSE);
		$this->db->from("mQueueColumns");
		$this->db->join('mDocumentType', 'mQueueColumns.DocumentTypeUID=mDocumentType.DocumentTypeUID', 'LEFT');
		$this->db->join('mCustomer', 'mQueueColumns.CustomerUID=mCustomer.CustomerUID', 'LEFT');
		$this->db->join('mUsers m1', 'm1.UserUID = mQueueColumns.CreatedByUserUID', 'LEFT');
		$this->db->join('mUsers m2', 'm2.UserUID = mQueueColumns.ModifiedByUserUID', 'LEFT');
		$this->db->join('mWorkFlowModules', 'mQueueColumns.WorkflowUID=mWorkFlowModules.WorkflowModuleUID', 'LEFT');

		if(is_numeric ( $post['workflow'] ) ){

			$this->db->where('mQueueColumns.WorkflowUID',$post['workflow']);
		} else {

			$this->db->where('mQueueColumns.Section',$post['workflow']);
		}

		$this->db->where('mCustomer.CustomerUID', $this->parameters['DefaultClientUID']);

		$this->db->order_by('mQueueColumns.Position');
	

	}

	/* 
	*Function for advanced_search
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */
	function advanced_search($post)
	{
		// if ($post['advancedsearch']['Category'] != '' && $post['advancedsearch']['Category'] != 'All') {
		// 	$this->db->where('mDocumentType.CategoryUID', $post['advancedsearch']['Category']);
		// }
		if ($post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All') {
			$this->db->where('mQueueColumns.WorkflowUID', $post['advancedsearch']['WorkflowModuleUID']);
		}
		return true;
	}

	function CheckExistUserName($UserUID, $LoginID)
	{
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows();
	}

	function CheckHeaderNameIsExist($post)
	{
		if (isset($post['QueueColumnUID']) && !empty(trim($post['QueueColumnUID']))) {
			$this->db->select('*');
			$this->db->from('mQueueColumns');
			$this->db->where('HeaderName', trim($post['HeaderName']));
			$this->db->where('WorkflowUID', $post['WorkflowUID']);
			$this->db->where_not_in('QueueColumnUID', $post['QueueColumnUID']);
			$query = $this->db->get();
		} else {
			$this->db->where('WorkflowUID', $post['WorkflowUID']);
			$query = $this->db->get_where('mQueueColumns', array('HeaderName' => trim($post['HeaderName'])));
		}
		return $query->num_rows();
	}

	function getparent_checklists($ignoreDocumentTypeUID = false)
	{
		$this->db->select("mDocumentType.*");
		$this->db->from('mDocumentType');
		if ($ignoreDocumentTypeUID) {
			$this->db->where('DocumentTypeUID <>', $ignoreDocumentTypeUID);
		}
		$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		return $this->db->get()->result();
	}

	/* 
	*Get child data
	*changes by vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function getchild_checklists($ignoreDocumentTypeUID, $type)
	{

		$this->db->select("mDocumentType.ParentDocumentTypeUID");
		$this->db->from('mDocumentType');
		$this->db->where('((mDocumentType.ParentDocumentTypeUID IS NOT NULL) OR (mDocumentType.ParentDocumentTypeUID <> ""))', NULL, FALSE);
		$this->db->group_by('mDocumentType.ParentDocumentTypeUID');
		$parentChecklists = $this->db->get()->result_array();
		$parentChecklistsexits = array_column($parentChecklists, 'ParentDocumentTypeUID');

		$this->db->select("*");
		$this->db->from('mDocumentType');
		if ($ignoreDocumentTypeUID && $type == 'add') {
			$this->db->where('ParentDocumentTypeUID', $ignoreDocumentTypeUID);
		} else {
			$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		}
		if ($ignoreDocumentTypeUID && $type == 'edit') {
			$this->db->where_not_in('DocumentTypeUID', $ignoreDocumentTypeUID);
		}

		if (!empty($parentChecklistsexits)) {
			$this->db->where_not_in('mDocumentType.DocumentTypeUID', $parentChecklistsexits);
		}		

		return $this->db->get()->result();
	}

	/* 
	*add child checklist 
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function addChildChecklist($post, $Groups, $hash, $Active)
	{
		if ($post['DocumentTypeUID']) {
			$DocumentTypeUID = $post['DocumentTypeUID'];
			//$hash = null;
		} else {
			$this->db->select('DocumentTypeUID');
			$this->db->from('mDocumentType');
			$this->db->where('DocumentTypeName', $post['DocumentTypeName']);
			$query = $this->db->get()->row();
			//$hash = $hash;
			$DocumentTypeUID = $query->DocumentTypeUID;
		}

		foreach ($this->input->post('childchecklist') as $key => $value) {
			$this->db->where(array("DocumentTypeUID" => $value));
			$update = array('CategoryUID' => $post['CategoryUID'], 'NamingConventions' => $post['NamingConventions'], 'ScreenCode' => $post['ScreenCode'], 'Groups' => $Groups, 'CustomerUID' => $post['CustomerUID'], 'FieldType' => $post['FieldType'], 'ToMails' => $post['ToMails'], 'EmailTemplate' => $post['EmailTemplate'], 'Active' => 1, 'ParentDocumentTypeUID' => $DocumentTypeUID, 'ChildLabel' => $post['ChildLabel']);
			$this->db->update('mDocumentType', $update);
		}
	}
	/* get documenttypeUID of tdocumentchecklist 
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function DocumentCheckList()
	{
		$this->db->select("DocumentTypeUID");
		$this->db->from('tDocumentCheckList');
		$this->db->where('IsDelete', '0');
		$deleted = $this->db->get()->result_array();
		return $parentChecklistsexits = array_column($deleted, 'DocumentTypeUID');
	}


	function getDynamicColumnName($WorkflowUID)
	{

		if (($WorkflowUID!="") && (is_numeric($WorkflowUID)))
		{
			$this->db->select("WorkflowModuleName");
			$this->db->from('mWorkFlowModules');
			$this->db->where('WorkflowModuleUID', $WorkflowUID);
			$header_name = $this->db->get()->result_array();
		}
		return $header_name;
	}


	/* get Category
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function getCategory()
	{
		$this->db->select("mCategory.CategoryUID,mCategory.CategoryName");
		$this->db->from('mCategory');
		$this->db->join('mCustomerWorkflowModules', 'mCategory.CategoryUID=mCustomerWorkflowModules.CategoryUID', 'left');
		$this->db->where('mCustomerWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
		$this->db->where('mCustomerWorkflowModules.CategoryUID is NOT null');
		
		return $this->db->get()->result_array();
	}
	/* 
	*Function for getting SubQueue 
	*author Mansoor Ali(mansoor.ali@avanzegroup.com)
	*since Date:21.11.2020
	 */
	function GetSubQueue($WorkflowUID)
	{
		$actValue = 1;
		$this->db->select("QueueColumnUID,HeaderName,ColumnName");
		$this->db->from('mQueueColumns');
		$this->db->where('mQueueColumns.WorkflowUID', $WorkflowUID);
		return $this->db->get()->result();
	}
	/* 
	*Function for getting Queue IDs 
	*author Mansoor Ali(mansoor.ali@avanzegroup.com)
	*since Date:21.11.2020
	 */
	function GetQueueIDs($WorkflowUID)
	{
		$this->db->select("distinct(QueueName), QueueUID");
		$this->db->from('mQueues');
		$this->db->where('WorkflowModuleUID', $WorkflowUID);
		$this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);
		return $this->db->get()->result();
	}

	/* 
	*Function for getting Static Queue based on Client
	*author Mansoor Ali(mansoor.ali@avanzegroup.com)
	*since Date:21.11.2020
	 */

	function GetStaticQueue($WorkflowModuleUID = '')
	{
		$this->db->select("StaticQueueUID,StaticQueueName,StaticQueueTableName");
		$this->db->from('mStaticQueues');
		$this->db->where('mStaticQueues.ClientUID', $this->parameters['DefaultClientUID']);
		$this->db->where('mStaticQueues.Active', 1);
		if (!empty($WorkflowModuleUID)) {
			$WorkflowStaticQueues = $this->config->item('StaticQueus')[$WorkflowModuleUID];
			if (!empty($WorkflowStaticQueues)) {
				$this->db->where_in('mStaticQueues.StaticQueueUID',$WorkflowStaticQueues);
			}
		}
		return $this->db->get()->result();
	}

	/* 
	*Function for Workflow and WorkFlow ID
	*author Mansoor Ali(mansoor.ali@avanzegroup.com)
	*since Date:21.11.2020
	 */
	function getWorkFlowCategory()
	{
		$this->db->select("mCustomerWorkflowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName");
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID=mCustomerWorkflowModules.WorkflowModuleUID AND mWorkFlowModules.Active = 1', 'left');
		$this->db->where('mCustomerWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
		$this->db->group_by('mCustomerWorkflowModules.WorkflowModuleUID'); 
		$this->db->order_by('mCustomerWorkflowModules.Position','asc'); 
		return $this->db->get()->result();

	}


	/* 
	*Function for Workflow and Section Value Fetch
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */
	function getWorkflow(){

		$this->db->select("mQueueColumns.*,mWorkFlowModules.WorkflowModuleName");
		$this->db->from("mQueueColumns");
		$this->db->join('mWorkFlowModules', 'mQueueColumns.WorkflowUID=mWorkFlowModules.WorkflowModuleUID', 'left');

		$this->db->where("mQueueColumns.WorkflowUID Is NOT NULL OR mQueueColumns.WorkflowUID !='' ");
		$this->db->where("mQueueColumns.Section Is NOT NULL OR mQueueColumns.Section !='' ");

		$this->db->group_by('WorkflowUID'); 
		return $this->db->get()->result();


	}






    /* 
	*Function for Dynamic Queue Columns DocumentTypename
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:29.07.2020
	 */

    function GetDocumentTypeName()
	{
		$this->db->select('*');
		$this->db->from('mDocumentType');
		return $this->db->get()->result();
	}

	/* 
	*Function for Dynamic Queue Columns WorkflowModuleName
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:29.07.2020
	 */

    function GetWorkflowModuleName()
	{
		$this->db->select('*');
		$this->db->from('mCustomerWorkflowModules');
		return $this->db->get()->result();
	} 

   /* 
	*Function for Dynamic Queue Columns SectionName
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:29.07.2020
	 */

	 function GetSectionName()
	{
		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->where('FieldSection','SUPERVISION');
		return $this->db->get()->result();
	} 


	function get_checklistquestions($WorkflowModuleUID,$Conditions=[])
	{
		$this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType', 'mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID AND mDocumentType.CategoryUID IS NOT NULL AND mCustomerWorkflowModules.CategoryUID IS NOT NULL');
	

		if (!isset($Conditions['SkipCondition'])) {
			$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		}
		
		$this->db->where(array('mCustomerWorkflowModules.CustomerUID' => $this->parameters['DefaultClientUID'], 'WorkflowModuleUID' => $WorkflowModuleUID, 'mDocumentType.CustomerUID' => $this->parameters['DefaultClientUID'], 'mDocumentType.Active' => '1'));
		$this->db->order_by('mDocumentType.Position', 'ASC');
		return $this->db->get()->result();
	}

	/**
	*Function function to get column row details 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 08 December 2020.
	*/

	function getEditdetails($QueueColumnUID) {

		$this->db->select("mQueueColumns.*,mDocumentType.DocumentTypeName,mCustomer.CustomerName,mWorkFlowModules.WorkflowModuleName,SortColumnName,StaticQueueUIDs,QueueUIDs");
		$this->db->from("mQueueColumns");
		$this->db->join('mDocumentType', 'mQueueColumns.DocumentTypeUID=mDocumentType.DocumentTypeUID', 'LEFT');
		$this->db->join('mCustomer', 'mQueueColumns.CustomerUID=mCustomer.CustomerUID', 'LEFT');
		$this->db->join('mWorkFlowModules', 'mQueueColumns.WorkflowUID=mWorkFlowModules.WorkflowModuleUID', 'LEFT');
		$this->db->where('QueueColumnUID',$QueueColumnUID);
		$query = $this->db->get();
		return $query->row();
	}

	/**
	*Function for Update Dynamic Queue Columns
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 08 December 2020.
	*/
	function UpdateDynamicqueue($post)
	{
		$currDate = Date('Y-m-d H:i:s', strtotime("now"));
		$userID = $this->loggedid;
		$IsCheck = isset($post['IsCheck']) && $post['IsCheck'] == 1 ? 1 : 0;
		$post['DocumentTypeUID'] = $IsCheck == 1 ? $post['DocumentTypeUID']  : NULL;
		$NoSort = isset($post['IsNoSort']) && $post['IsNoSort'] == 1 ? 0 : 1;
		if($NoSort == 1) {
			$sortCol = NULL;
		} else {


			if(isset($post['SortColumnName']) && !empty($post['SortColumnName'])  ) {
				$NoSort = 1;
			}
			$sortCol = isset($post['SortColumnName']) && !empty($post['SortColumnName']) ? $post['SortColumnName'] : NULL;
			$sortCol = isset($post['SortColumnName']) && $post['SortColumnName'] == 'Custom' ? $post['SortCustomColumnName'] : $post['SortColumnName'];
		}

		$sortCol = !empty($post['SortColumnName']) && ($post['SortColumnName'] == 'Select') ? NULL : $sortCol;

		$QueueUIDsArray = [];
		$StaticQueueUIDsArray = [];

	

		$this->db->trans_begin();

		if(isset($post['PermissionQueueUIDS']) && !empty($post['PermissionQueueUIDS'])) {
			foreach ($post['PermissionQueueUIDS'] as $PermissionQueueUID) {
				$PermissionQueueUIDarray = explode('SubQueues-', $PermissionQueueUID);
				if(!empty($PermissionQueueUIDarray) && isset($PermissionQueueUIDarray[1]) && !empty($PermissionQueueUIDarray[1])) {
					$QueueUIDsArray[] = $PermissionQueueUIDarray[1];
				}

				$PermissionQueueUIDarray = explode('StaticQueues-', $PermissionQueueUID);
				if(!empty($PermissionQueueUIDarray) && isset($PermissionQueueUIDarray[1]) && !empty($PermissionQueueUIDarray[1])) {
					$StaticQueueUIDsArray[] = $PermissionQueueUIDarray[1];
				}

			}

		}

		$StaticQueueUIDs = !empty($StaticQueueUIDsArray) ? implode(',', $StaticQueueUIDsArray) : NULL;
		$QueueUIDs = !empty($QueueUIDsArray) ? implode(',', $QueueUIDsArray) : NULL;

		$Updatedata = array('HeaderName' => $post['HeaderName'], 'ColumnName' => $post['ColumnName'], 'IsChecklist' => $IsCheck, 'DocumentTypeUID' => $post['DocumentTypeUID'],  'NoSort' => $NoSort,'SortColumnName' => $sortCol, 'StaticQueueUIDs' => $StaticQueueUIDs, 'QueueUIDs' => $QueueUIDs, 'ModifiedByUserUID' => $userID, 'ModifiedDateTime' => $currDate );
		$this->db->where(array('mQueueColumns.QueueColumnUID'=>$post['QueueColumnUID']));
		$this->db->update('mQueueColumns', $Updatedata);


		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;

		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	/**
	*Function for Update Dynamic Queue Columns
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 08 December 2020.
	*/
	function AddDynamicqueue($post)
	{
		$currDate = Date('Y-m-d H:i:s', strtotime("now"));
		$userID = $this->loggedid;
		$IsCheck = isset($post['IsCheck']) && $post['IsCheck'] == 1 ? 1 : 0;
		$post['DocumentTypeUID'] = $IsCheck == 1 ? $post['DocumentTypeUID']  : NULL;

		$NoSort = isset($post['IsNoSort']) && $post['IsNoSort'] == 1 ? 0 : 1;
		if($NoSort == 1) {
			$sortCol = NULL;
		} else {


			if(isset($post['SortColumnName']) && !empty($post['SortColumnName'])  ) {
				$NoSort = 1;
			}
			$sortCol = isset($post['SortColumnName']) && !empty($post['SortColumnName']) ? $post['SortColumnName'] : NULL;
			$sortCol = isset($post['SortColumnName']) && $post['SortColumnName'] == 'Custom' ? $post['SortCustomColumnName'] : $post['SortColumnName'];
		}

		$sortCol = !empty($post['SortColumnName']) && ($post['SortColumnName'] == 'Select') ? NULL : $sortCol;

		$QueueUIDsArray = [];
		$StaticQueueUIDsArray = [];

			$this->db->select('MAX(Position) as Position');
		$this->db->from('mQueueColumns');
		if(is_numeric($post['WorkflowUID'])){

			$this->db->where('mQueueColumns.WorkflowUID',$post['WorkflowUID']);
		} else {
			$this->db->where('mQueueColumns.Section',$post['WorkflowUID']);

		}
		$Position = $this->db->get()->row()->Position;
		$this->db->trans_begin();

		if(isset($post['PermissionQueueUIDS']) && !empty($post['PermissionQueueUIDS'])) {
			foreach ($post['PermissionQueueUIDS'] as $PermissionQueueUID) {

				$PermissionQueueUIDarray = explode('SubQueues-', $PermissionQueueUID);
				if(!empty($PermissionQueueUIDarray) && isset($PermissionQueueUIDarray[1]) && !empty($PermissionQueueUIDarray[1])) {
					$QueueUIDsArray[] = $PermissionQueueUIDarray[1];
				}

				$PermissionQueueUIDarray = explode('StaticQueues-', $PermissionQueueUID);
				if(!empty($PermissionQueueUIDarray) && isset($PermissionQueueUIDarray[1]) && !empty($PermissionQueueUIDarray[1])) {
					$StaticQueueUIDsArray[] = $PermissionQueueUIDarray[1];
				}

			}

		}


		$StaticQueueUIDs = !empty($StaticQueueUIDsArray) ? implode(',', $StaticQueueUIDsArray) : NULL;
		$QueueUIDs = !empty($QueueUIDsArray) ? implode(',', $QueueUIDsArray) : NULL;

		$WorkflowUID = NULL;
		$Section = NULL;
		if(is_numeric($post['WorkflowUID'])){
			$WorkflowUID = $post['WorkflowUID'];
		} else {
			$Section = $post['WorkflowUID'];
		}

		$insertdata = array('HeaderName' => $post['HeaderName'], 'ColumnName' => $post['ColumnName'], 'WorkflowUID' => $WorkflowUID,'Section'=>$Section, 'NoSort' => $post['IsNoSort'], 'IsChecklist' => $IsCheck, 'DocumentTypeUID' => $post['DocumentTypeUID'], 'SortColumnName' => $sortCol, 'StaticQueueUIDs' => $StaticQueueUIDs, 'QueueUIDs' => $QueueUIDs, 'ModifiedByUserUID' => $userID, 'ModifiedDateTime' => $currDate,'CustomerUID'=>$this->parameters['DefaultClientUID'],'CreatedByUserUID'=>$userID,'CreatedDateTime'=>$currDate,'Position'=>($Position+1));

		$this->db->insert('mQueueColumns', $insertdata);


		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;

		} else {
			$this->db->trans_commit();
			return true;
		}
	}


}
