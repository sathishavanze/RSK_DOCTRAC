<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Documenttype_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();
	}

	function GetDocumentType($post)
	{

		$hash = NULL;
		$numbers = 1; // Get Max of ID
		/*while (true) {
			$categoryhash = 'DocumentType' . $numbers;
			$hash = substr(md5($categoryhash), 0, 8);

			$this->db->where('HashCode', $hash);
			$count = $this->db->get('mDocumentType')->num_rows();
			if ($count == 0) {

				break;
			}
			$numbers++;
		}*/
		$Active = $data['Active'] = isset($post['Active']) ? 1 : 0;

		if ($post['Groups'] == 'empty') {
			$Groups = null;
		} else {
			$Groups = $post['Groups'];
		}
		$ParentDocumentTypeUID = isset($post['ParentDocumentTypeUID']) && !empty($post['ParentDocumentTypeUID']) ? $post['ParentDocumentTypeUID'] : NULL;

		$insert = array('DocumentTypeName' => $post['DocumentTypeName'], 'CategoryUID' => $post['CategoryUID'], 'NamingConventions' => $post['NamingConventions'], 'ScreenCode' => $post['ScreenCode'], 'Groups' => $Groups, 'CustomerUID' => $post['CustomerUID'], 'FieldType' => $post['FieldType'], 'ToMails' => $post['ToMails'], 'EmailTemplate' => $post['EmailTemplate'], 'Active' => 1, 'HashCode' => $hash, 'ParentDocumentTypeUID' => $ParentDocumentTypeUID, 'ChildLabel' => $post['ChildLabel'],'HeadingName' => $post['HeadingName'],'GroupHeadingName' => $post['GroupHeadingName'],'CalculateDays' => $post['CalculateDays']);

		$update = array('DocumentTypeName' => $post['DocumentTypeName'], 'CategoryUID' => $post['CategoryUID'], 'NamingConventions' => $post['NamingConventions'], 'ScreenCode' => $post['ScreenCode'], 'Groups' => $Groups, 'CustomerUID' => $post['CustomerUID'], 'FieldType' => $post['FieldType'], 'ToMails' => $post['ToMails'], 'EmailTemplate' => $post['EmailTemplate'], 'Active' => $Active, 'HashCode' => $hash, 'ParentDocumentTypeUID' => $ParentDocumentTypeUID, 'ChildLabel' => $post['ChildLabel'],'HeadingName' => $post['HeadingName'],'GroupHeadingName' => $post['GroupHeadingName'],'CalculateDays' => $post['CalculateDays']);

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
			$post['DocumentTypeUID'] = $this->db->insert_id();

			if ($post['childchecklist']) {
				$this->addChildChecklist($post, $Groups, $hash, $Active);
			}
		}
		if ($this->db->affected_rows() > 0) {
			if ($post['Alert'][0]['Comments']) {
				$this->addChecklistAlert($post);
			}
			return 1;
		} else {
			if ($post['Alert'][0]['Comments']) {
				$this->addChecklistAlert($post);
			}
			return 0;
		}
	}
	/* 
	* function for change date format 
	*/
	function changeDateformat($date)
	{
		return $your_date = date("Y-m-d", strtotime($date));
	}

	/* 
	* Function for Add checklist alert
	* @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:28-07-2020
	*/
	function addChecklistAlert($post)
	{
		foreach ($post['Alert'] as $key => $value) {
			//Insert and update based on count
			$insert = array('DocumentTypeUID' => $post['DocumentTypeUID'], 'CustomerUID' => $this->parameters['DefaultClientUID'], 'ChecklistComment' => $value['Comments'], 'AlertStartDate' => $this->changeDateformat($value['startDate']), 'AlertEndDate' => $this->changeDateformat($value['endDate']), 'CreatedUserUID' => 5, 'CreatedDateTime' => date('Y-m-d H:i:s'));
			$this->db->insert('mDocumentTypeAlert', $insert);
		}
	}

	/* 
	* function for get alert message to list out 
	* @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:28-07-2020
	*/
	function getAlertMessage($DocumentTypeUID)
	{
		$this->db->select('*');
		$this->db->from('mDocumentTypeAlert');
		$this->db->where(array('CustomerUID' => $this->parameters['DefaultClientUID'], 'DocumentTypeUID' => $DocumentTypeUID));
		return $this->db->get()->result();
	}
	/* 
	*Get Count of record for data table
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function count_all()
	{
		$this->db->select("1");
		$this->filterQuery();
		//$this->db->where('tOrders.CustomerUID', $this->session->userdata('DefaultClientUID'));
		$query = $this->db->count_all_results();
		return $query;
	}
	/* 
	*Get filtered count for data table
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function count_filtered($post)
	{
		$this->db->select("1");
		$this->filterQuery();

		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		}
		// Datatable Search
		if (!empty($post['search_value'])) {
			$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		}else{
			$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		}
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		$query = $this->db->get();
		return $query->num_rows();
	}
	/* 
	*Get Document checklist filtered data
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function getDocumentTypeChecklist($post)
	{
		$this->filterQuery();

		/* Advanced Search  */
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		}

		/* Datatable Search */
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		/* Datatable OrderBy */
		if (!empty($post['order'])) {
			$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		}else{
			$this->db->order_by('(mDocumentType.Position * -1)','DESC');
		}

		if ($post['length'] != '') {
			$this->db->limit($post['length'], $post['start']);
		}

		$query = $this->db->get();
		return $query->result();
	}
	/* 
	*Get Document checklist filtered data
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function getDocumentTypeChecklists($post)
	{
		$this->filterQuery();

		/* Advanced Search  */
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		}

		/* Datatable Search */
		if (!empty($post['search_value'])) {
			$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		}else{
			$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		}

		/* Datatable OrderBy */
		if (!empty($post['order'])) {
			$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		}else{
			$this->db->order_by('(mDocumentType.Position * -1)','DESC');
		}

		if ($post['length'] != '') {
			$this->db->limit($post['length'], $post['start']);
		}

		$query = $this->db->get();
		return $query->result();
	}
	/* 
	*/
	function getDocumentPositionChecklist($Category,$LoanType)
	{
		$this->db->select("mDocumentType.*,mCategory.CategoryName,mCustomer.CustomerName");
		$this->db->from('mDocumentType');
		$this->db->join('mCategory', 'mDocumentType.CategoryUID=mCategory.CategoryUID', 'left');
		$this->db->join('mCustomer', 'mDocumentType.CustomerUID=mCustomer.CustomerUID', 'left');
		$this->db->where('mDocumentType.CustomerUID', $this->parameters['DefaultClientUID']);
		$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		if($LoanType!='All' && $LoanType!=''){
			$this->db->where(array('mDocumentType.CategoryUID' => $Category,'mDocumentType.Groups'=>$LoanType));
		}else{
			$this->db->where('mDocumentType.CategoryUID',$Category);
		}
		$this->db->where('mDocumentType.Active',1);
		$this->db->order_by('(mDocumentType.Position * -1)','DESC');
		$query = $this->db->get();
		return $query->result();
	}
	/* 
	*/
	function getDocumentPositionChildChecklist($Category,$ParentDocumentTypeUID)
	{
		$this->db->select("mDocumentType.*,mCategory.CategoryName,mCustomer.CustomerName");
		$this->db->from('mDocumentType');
		$this->db->join('mCategory', 'mDocumentType.CategoryUID=mCategory.CategoryUID', 'left');
		$this->db->join('mCustomer', 'mDocumentType.CustomerUID=mCustomer.CustomerUID', 'left');
		$this->db->where(array('mDocumentType.CustomerUID'=> $this->parameters['DefaultClientUID'],'mDocumentType.ParentDocumentTypeUID' => $ParentDocumentTypeUID));
		$this->db->where('mDocumentType.CategoryUID', $Category);
		$this->db->where('mDocumentType.Active',1);
		$this->db->order_by('(mDocumentType.Position * -1)','DESC');
		$query = $this->db->get();
		return $query->result();
	}
	/* 
	*/
	function getDocumentPositionChecklist1($Category)
	{
		$this->db->select("mDocumentType.*,mCategory.CategoryName,mCustomer.CustomerName");
		$this->db->from('mDocumentType');
		$this->db->join('mCategory', 'mDocumentType.CategoryUID=mCategory.CategoryUID', 'left');
		$this->db->join('mCustomer', 'mDocumentType.CustomerUID=mCustomer.CustomerUID', 'left');
		$this->db->where('mDocumentType.CustomerUID', $this->parameters['DefaultClientUID']);
		//$this->db->where('mDocumentType.CategoryUID', $Category);
		//$this->db->order_by('(mDocumentType.Position * -1)','DESC');
		/* $query = $this->db->get();
		return $query->result(); */
	}
	/* 
	*/
	function getDocumentPositionChildChecklist1($Category,$ParentDocumentTypeUID)
	{
		$this->db->select("mDocumentType.*,mCategory.CategoryName,mCustomer.CustomerName");
		$this->db->from('mDocumentType');
		$this->db->join('mCategory', 'mDocumentType.CategoryUID=mCategory.CategoryUID', 'left');
		$this->db->join('mCustomer', 'mDocumentType.CustomerUID=mCustomer.CustomerUID', 'left');
		$this->db->where(array('mDocumentType.CustomerUID'=> $this->parameters['DefaultClientUID'],'mDocumentType.ParentDocumentTypeUID' => $ParentDocumentTypeUID));
		$this->db->where('mDocumentType.CategoryUID', $Category);
		$this->db->order_by('(mDocumentType.Position * -1)','DESC');
		$query = $this->db->get();
		return $query->result();
	}
	/* 
	*Get filtering query for document checklist data table
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function filterQuery()
	{
		$this->db->select("mDocumentType.*,mCategory.CategoryName,mCustomer.CustomerName");
		$this->db->from('mDocumentType');
		$this->db->join('mCategory', 'mDocumentType.CategoryUID=mCategory.CategoryUID', 'left');
		$this->db->join('mCustomer', 'mDocumentType.CustomerUID=mCustomer.CustomerUID', 'left');
		$this->db->where('mDocumentType.CustomerUID', $this->parameters['DefaultClientUID']);
	}
	/* 
	*Function for advanced_search
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
	function advanced_search($post)
	{
		if ($post['advancedsearch']['Category'] != '' && $post['advancedsearch']['Category'] != 'All') {
			$this->db->where('mDocumentType.CategoryUID', $post['advancedsearch']['Category']);
		}
		if ($post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All') {
			$this->db->where('mDocumentType.CategoryUID', $post['advancedsearch']['WorkflowModuleUID']);
		}
		if ($post['advancedsearch']['Groups'] != '' && $post['advancedsearch']['Groups'] != 'All') {
			$this->db->where('mDocumentType.Groups', $post['advancedsearch']['Groups']);
		}
		return true;
	}

	function CheckExistUserName($UserUID, $LoginID)
	{
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows();
	}

	function CheckDocumentTypeExist($post)
	{
		if (isset($post['DocumentTypeUID'])) {
			$this->db->select('*');
			$this->db->from('mDocumentType');
			$this->db->where_in('CategoryUID', $post['CategoryUID']);
			$this->db->where('DocumentTypeName', $post['DocumentTypeName']);
			$this->db->where_not_in('DocumentTypeUID', $post['DocumentTypeUID']);
			$query = $this->db->get();
		} else {
			$this->db->where_in('CategoryUID', $post['CategoryUID']);
			$query = $this->db->get_where('mDocumentType', array('DocumentTypeName' => $this->input->post('DocumentTypeName')));
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
	* function for get child checklist 
	* @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:27-07-2020
	*/
	function getChildChecklist($ParentDocumentTypeUID)
	{
		$this->db->select("DocumentTypeUID,DocumentTypeName");
		$this->db->from('mDocumentType');
		$this->db->where('ParentDocumentTypeUID', $ParentDocumentTypeUID);
		return $this->db->get()->result();
	}

	/* 
	* function for remove child checklist 
	* @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:27-07-2020
	*/
	function removeParent($DocumentTypeUID)
	{
		$update = array('ParentDocumentTypeUID' => null);
		$this->db->where('DocumentTypeUID', $DocumentTypeUID);
		$this->db->update('mDocumentType', $update);
		/* check affected row */
		if ($this->db->affected_rows() > 0) {
			return '1';
		} else {
			return '0';
		}
	}
	/* 
	* function for remove Alert message
	* @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:27-07-2020
	*/
	function removeChecklistMessage($AlertUID)
	{
		$this->db->query('DELETE FROM `mDocumentTypeAlert` WHERE AlertID =' . $AlertUID . ' and CustomerUID =' . $this->parameters['DefaultClientUID']);
		/* check affected row */
		if ($this->db->affected_rows() > 0) {
			return '1';
		} else {
			return '0';
		}
	}

	/* 
	* Function for update alert message
	* @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:28-07-2020
	*/
	function updateAlert($post)
	{
		$dataField = ($post['field'] != 'ChecklistComment') ? $this->changeDateformat($post['value']) : $post['value'];
		$update = array('' . $post['field'] . '' => $dataField, 'ModifiedUserUID' => 5, 'ModifiedDateTime' => date('Y-m-d H:i:s'));
		$this->db->where(array('AlertID' => $post['AlertUID'], 'CustomerUID' => $this->parameters['DefaultClientUID']));
		$this->db->update('mDocumentTypeAlert', $update);
		if ($this->db->affected_rows() > 0) {
			return '1';
		} else {
			return '0';
		}
	}
	/* 
	* Function for update Active and inactive status
	* @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:28-07-2020
	*/
	function UpdateActiveStatus($post)
	{
		$Active=isset($post['Active']) ? 1 : 0;
		$update = array('Active' => $Active);
		$this->db->where(array('DocumenttypeUID' => $post['DocumenttypeUID'], 'CustomerUID' => $this->parameters['DefaultClientUID']));
		$this->db->update('mDocumentType', $update);
		if ($this->db->affected_rows() > 0) {
			return '1';
		} else {
			return '0';
		}
	}
}
