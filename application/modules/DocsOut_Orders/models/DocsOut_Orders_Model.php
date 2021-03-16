<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class DocsOut_Orders_Model extends MY_Model {
	
	function __construct()
	{ 
		parent::__construct();
		$this->loggedid = $this->session->userdata('UserUID');
		$this->UserName = $this->session->userdata('UserName');
		$this->RoleUID = $this->session->userdata('RoleUID');
		$this->WorkflowModuleUID = $this->config->item('Workflows')['DocsOut']; 
		$this->otherdb1 = $this->load->database('otherdb', TRUE);
		$this->otherdb2 = $this->load->database('otherdb', TRUE);
	}

	function total_count()
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();
		$this->db->select("1");


		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetDocsOrders(false);

		
		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'TOTALCOUNT');

		$this->db->group_start();

		$this->db->group_start();
		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->group_end();
		
		$this->db->or_group_start();
		// Docsout queue "Docs Checked Conditions Pending" Sub queue conditions
		$QueueTypes = $this->config->item("DocsCheckedConditionPending");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);
		$this->db->group_end();

		// $this->db->or_group_start();
		// Docsout queue "Queue cleared by Funding" Sub queue conditions
		// $this->db->group_start();
		// $this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		// $this->db->or_where('tOrderImport.Queue', '0');
		// $this->db->group_end();
		// $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderQueuUID = (SELECT MAX(OrderQueuUID) FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck").") AND QueueStatus = 'Completed')",NULL,FALSE);
		// $this->db->group_end();

		$this->db->or_group_start();
		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("PendingfromUW");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);
		$this->db->group_end();

		$this->db->or_group_start();
		// Docsout queue "Submitted for Doc Check" Sub queue conditions
		$QueueTypes = $this->config->item("SubmittedforDocCheckCond");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);
		$this->db->group_end();


		// $this->db->or_group_start();
		// Docsout queue "Pending Docs Release" Sub queue conditions
		// $QueueTypes = $this->config->item("PendingDocsRelease");
		// $this->db->where_in('tOrderImport.Queue',$QueueTypes);		
		// $this->db->group_end();

		$this->db->or_group_start();
		
		// Docsout queue "Pending Docs Release" Sub queue conditions
		$this->db->where(" EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);

		$this->db->group_end();

		$this->db->group_end();

		$query = $this->db->count_all_results();
		return $query;
	}

	// New Orders
	function count_all($post=[])
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'NEWCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}
		
		

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}


	function count_filtered($post,$module)
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'NEWCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}

	function DocsOutOrders($post,$module,$global='')
	{

		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'NEWCOUNT');
		
		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();

	}

	// Docs Check Orders Count all
	function Docs_count_all($post=[])
	{


		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Followup
		$this->db->join('tOrderFollowUp','`tOrderFollowUp`.`OrderUID` = `tOrders`.`OrderUID` AND `tOrderFollowUp`.`WorkflowModuleUID` = '.$this->WorkflowModuleUID.' AND `tOrderFollowUp`.`StaticQueueUID` = 13 AND tOrderFollowUp.IsCleared = 0','LEFT');

		// Docsout queue "Docs Checked Conditions Pending" Sub queue conditions
		$QueueTypes = $this->config->item("DocsCheckedConditionPending");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}
		
		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);



		$query = $this->db->count_all_results();
		return $query;
	}

	// Docs Check Orders Filter all

	function Docs_count_filtered($post,$module)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Followup
		$this->db->join('tOrderFollowUp','`tOrderFollowUp`.`OrderUID` = `tOrders`.`OrderUID` AND `tOrderFollowUp`.`WorkflowModuleUID` = '.$this->WorkflowModuleUID.' AND `tOrderFollowUp`.`StaticQueueUID` = 13 AND tOrderFollowUp.IsCleared = 0','LEFT');

		// Docsout queue "Docs Checked Conditions Pending" Sub queue conditions
		$QueueTypes = $this->config->item("DocsCheckedConditionPending");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}


	function DocsCheckOrders($post,$module,$global='')
	{

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID); 

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		$this->db->select('tOrderFollowUp.FollowUpUID, tOrderFollowUp.StaticQueueUID');
		/*^^^^^ Get MyOrders Query ^^^^^*/

		
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Followup
		$this->db->join('tOrderFollowUp','`tOrderFollowUp`.`OrderUID` = `tOrders`.`OrderUID` AND `tOrderFollowUp`.`WorkflowModuleUID` = '.$this->WorkflowModuleUID.' AND `tOrderFollowUp`.`StaticQueueUID` = 13 AND tOrderFollowUp.IsCleared = 0','LEFT');

		// Docsout queue "Docs Checked Conditions Pending" Sub queue conditions
		$QueueTypes = $this->config->item("DocsCheckedConditionPending");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// /*Milestone Is 2G */
		// // $this->db->where('tOrders.MilestoneUID',$this->config->item('CD_Orders_Milestones'));

		//  $this->db->where('tOrders.MilestoneUID','5');
		
		//        // ScheduledDate is blank
		// $this->db->where('tOrderImport.ScheduledDate IS NOT NULL');  

		//  // DocsOutClosingDisclosureSendDate is blank

		// $this->db->where('tOrderImport.DocsOutClosingDisclosureSendDate IS NOT NULL'); 

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();

	}

	// Queue Check Orders Count all
	function Queuecheck_count_all($post=[])
	{


		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Queue cleared by Funding" Sub queue conditions
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();
		// $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck")." AND QueueStatus = 'Completed')",NULL,FALSE);
		$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderQueuUID = (SELECT MAX(OrderQueuUID) FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck").") AND QueueStatus = 'Completed')",NULL,FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}

	// Queue Check Orders Filter all

	function Queuecheck_count_filtered($post,$module)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Queue cleared by Funding" Sub queue conditions
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();
		// $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck")." AND QueueStatus = 'Completed')",NULL,FALSE);
		$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderQueuUID = (SELECT MAX(OrderQueuUID) FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck").") AND QueueStatus = 'Completed')",NULL,FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}


	function QueuecheckOrders($post,$module,$global='')
	{

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID); 

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/

		
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(false, ['ExcludeOrderBy'=>TRUE]);
		} else {

			$this->Common_Model->GetDocsOrders(false);
			/*^^^^^ Get MyOrders Query ^^^^^*/
		}

		// Docsout queue "Queue cleared by Funding" Sub queue conditions
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();
		// $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck")." AND QueueStatus = 'Completed')",NULL,FALSE);

		$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderQueuUID = (SELECT MAX(OrderQueuUID) FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck").") AND QueueStatus = 'Completed')",NULL,FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();

	}


	// Queue Check Orders Count all


	function Pendingdocs_count_all($post=[])
	{


		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}
		// Docsout queue "Pending Docs Release" Sub queue conditions
		$QueueTypes = $this->config->item("PendingDocsRelease");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}

	// Queue Check Orders Filter all

	function Pendingdocs_count_filtered($post,$module)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Pending Docs Release" Sub queue conditions
		$QueueTypes = $this->config->item("PendingDocsRelease");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}


	function PendingdocsOrders($post,$module,$global='')
	{

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID); 

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/

		
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Pending Docs Release" Sub queue conditions
		$QueueTypes = $this->config->item("PendingDocsRelease");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();

	}

	function Pendinguw_count_all($post=[])
	{


		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("PendingfromUW");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}

	// Queue Check Orders Filter all

	function Pendinguw_count_filtered($post,$module)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("PendingfromUW");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}


	function PendinguwOrders($post,$module,$global='')
	{

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID); 

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/

		
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("PendingfromUW");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);


		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');
		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();

	}

	// Work In Progress
	function inprogress_count_all($post=[])
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		$this->db->select("1");


		/*^^^^^ Get MyOrders Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'ASSIGNEDCOUNT');
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}


		$query = $this->db->count_all_results();
		return $query;
	} 


	function inprogress_count_filtered($post)
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();
		
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}

		$this->db->select("1");
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);
		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'ASSIGNEDCOUNT');

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	} 




	function WorkInProgressOrders($post)
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName");
		$this->db->select('tOrders.LastModifiedDateTime,tOrderAssignments.AssignedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);

		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();
		
		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'ASSIGNEDCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$query = $this->db->get();
		return $query->result();  
	} 



	function DocsOutSubQueueConditions() {


		$this->GetDocsOrdersQuery();
		// Docsout queue "Docs Checked Conditions Pending" Sub queue conditions
		$QueueTypes = $this->config->item("DocsCheckedConditionPending");
		$this->otherdb1->where_in('tOrderImport.Queue',$QueueTypes);

		$DocsCheckedConditionPendingOrdersSQL = $this->otherdb1->get_compiled_select();
		
		// echo "<pre>"; print_r($DocsCheckedConditionPendingOrdersSQL); exit();

		/* $DocsCheckedConditionPendingOrdersArr = $this->db->get()->result_array();
		$DocsCheckedConditionPendingOrders = array_column($DocsCheckedConditionPendingOrdersArr, 'OrderUID'); */

		// $this->Common_Model->GetDocsOrdersQuery();
		// Docsout queue "Queue cleared by Funding" Sub queue conditions
		// $this->db->group_start();
		// $this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		// $this->db->or_where('tOrderImport.Queue', '0');
		// $this->db->group_end();
		// $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck")." AND QueueStatus = 'Completed')",NULL,FALSE);
		// $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderQueuUID = (SELECT MAX(OrderQueuUID) FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck").") AND QueueStatus = 'Completed')",NULL,FALSE);

		// $QueueClearedByFundingOrdersSQL = $this->db->get_compiled_select();

		/* $QueueClearedByFundingOrdersArr = $this->db->get()->result_array();
		$QueueClearedByFundingOrders = array_column($QueueClearedByFundingOrdersArr, 'OrderUID'); */

		// $this->Common_Model->GetDocsOrdersQuery();
		// Docsout queue "Pending Docs Release" Sub queue conditions
		// $QueueTypes = $this->config->item("PendingDocsRelease");
		// $this->db->where_in('tOrderImport.Queue',$QueueTypes);

		// $PendingDocsReleaseOrdersSQL = $this->db->get_compiled_select();

		/* $PendingDocsReleaseOrdersArr = $this->db->get()->result_array();
		$PendingDocsReleaseOrders = array_column($PendingDocsReleaseOrdersArr, 'OrderUID'); */

		$this->GetDocsOrdersQuery();
		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("PendingfromUW");
		$this->otherdb1->where_in('tOrderImport.Queue',$QueueTypes);

		$PendingfromUWOrdersSQL = $this->otherdb1->get_compiled_select();

		$this->GetDocsOrdersQuery();
		// Docsout queue "Submitted for Doc Check" Sub queue conditions
		$QueueTypes = $this->config->item("SubmittedforDocCheckCond");
		$this->otherdb1->where_in('tOrderImport.Queue',$QueueTypes);

		$SubmittedforDocCheckOrdersSQL = $this->otherdb1->get_compiled_select();

		return array(
			'DocsCheckedConditionPendingOrdersSQL'=>$DocsCheckedConditionPendingOrdersSQL,
			// 'QueueClearedByFundingOrdersSQL'=>$QueueClearedByFundingOrdersSQL,
			// 'PendingDocsReleaseOrdersSQL'=>$PendingDocsReleaseOrdersSQL,
			'PendingfromUWOrdersSQL'=>$PendingfromUWOrdersSQL,
			'SubmittedforDocCheckOrdersSQL'=>$SubmittedforDocCheckOrdersSQL
		);
		/************************/
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsCheckedConditionPendingOrdersSQL.')');
		// $this->db->where('tOrders.OrderUID NOT IN ('.$QueueClearedByFundingOrdersSQL.')');
		// $this->db->where('tOrders.OrderUID NOT IN ('.$PendingDocsReleaseOrdersSQL.')');
		// $this->db->where('tOrders.OrderUID NOT IN ('.$PendingfromUWOrdersSQL.')');

		/* $PendingfromUWOrdersArr = $this->db->get()->result_array();
		$PendingfromUWOrders = array_column($PendingfromUWOrdersArr, 'OrderUID'); */

		/* if (!empty($DocsCheckedConditionPendingOrders)) {
			$this->db->where_not_in('tOrders.OrderUID',$DocsCheckedConditionPendingOrders);
		}
		if (!empty($QueueClearedByFundingOrders)) {
			$this->db->where_not_in('tOrders.OrderUID',$QueueClearedByFundingOrders);
		}
		if (!empty($PendingDocsReleaseOrders)) {
			$this->db->where_not_in('tOrders.OrderUID',$PendingDocsReleaseOrders);
		}
		if (!empty($$PendingfromUWOrders)) {
			$this->db->where_not_in('tOrders.OrderUID',$$PendingfromUWOrders);
		} */

		/* $this->db->group_start();

		$this->db->or_group_start();		
		// Docsout queue "Docs Checked Conditions Pending" Sub queue conditions
		$QueueTypes = $this->config->item("DocsCheckedConditionPending");
		$this->db->where_not_in('tOrderImport.Queue',$QueueTypes);
		$this->db->or_where('tOrderImport.Queue IS NULL');
		$this->db->group_end();

		$this->db->or_group_start();		
		// Docsout queue "Queue cleared by Funding" Sub queue conditions
		$this->db->where('((tOrderImport.Queue IS NOT NULL) OR (tOrderImport.Queue <> ""))',NULL,FALSE);
		// $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck")." AND QueueStatus = 'Completed')",NULL,FALSE);
		$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE OrderQueuUID = (SELECT MAX(OrderQueuUID) FROM tOrderQueues WHERE OrderUID = tOrders.OrderUID AND QueueUID = ".$this->config->item("SubmittedforDocCheck").") AND QueueStatus = 'Completed')",NULL,FALSE);
		$this->db->group_end();

		$this->db->or_group_start();	
		// Docsout queue "Pending Docs Release" Sub queue conditions
		$QueueTypes = $this->config->item("PendingDocsRelease");
		$this->db->where_not_in('tOrderImport.Queue',$QueueTypes);
		$this->db->group_end();

		$this->db->or_group_start();	
		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("PendingfromUW");
		$this->db->where_not_in('tOrderImport.Queue',$QueueTypes);
		$this->db->group_end();

		$this->db->group_end(); */
	}

	function MyOrders($post)
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");

		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}
		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'MYCOUNT');



		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);



		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}		

		$query = $this->db->get();
		return $query->result();  
	}

	function myorders_count_all($post=[])
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		$this->db->select("1");


		/*^^^^^ Get MyOrders Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'MYCOUNT');


		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}



		$query = $this->db->count_all_results();
		return $query;
	}
	function myorders_count_filtered($post)
	{
		// skip the order in sub queue conditions
		$DocsOutSubQueueSQL = $this->DocsOutSubQueueConditions();

		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}

		$this->db->select("1");

		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}
		/*^^^^^ Get MyOrders Query ^^^^^*/

		// Queue blank
		$this->db->group_start();
		$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
		$this->db->or_where('tOrderImport.Queue', '0');
		$this->db->group_end();

		// skip the order in sub queue conditions
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["QueueClearedByFundingOrdersSQL"].')', NULL, FALSE);
		// $this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingDocsReleaseOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"].')', NULL, FALSE);
		$this->db->where('tOrders.OrderUID NOT IN ('.$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"].')', NULL, FALSE);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'MYCOUNT');



		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}
	// Work in Progress
	function CheckAutoAssignEnabled($id)
	{
		$this->db->select("AutoAssign");
		$this->db->from('mUsers');
		$this->db->where(array('UserUID'=>$id));
		return $this->db->get()->row()->AutoAssign;
	}

	function CheckExistingOrders($id,$ProjectUID,$Workflow)
	{
		$this->db->select("*");
		$this->db->from('tOrderAssignments');
		$this->db->join('tOrders','tOrders.OrderUID = tOrderAssignments.OrderUID','left');
		$this->db->where('tOrderAssignments.ProjectUID',$ProjectUID);
		if($Workflow == 1)
		{
			$this->db->where(array('AssignedToUserUID'=>$id));
		}else{
			$this->db->where(array('QcAssignedToUserUID'=>$id));
		}
		return $this->db->get()->result();
	}

	function GetQcUsers()
	{
		$this->db->select("*");
		$this->db->from('mUsers');
		return $this->db->get()->result();
	}

	function AssignOrders($id,$ProjectUID,$Workflow)
	{
		$this->db->select("OrderUID");
		$this->db->from('tOrderAssignments');
		if($Workflow == 1)
		{
			$this->db->where('AssignedToUserUID !=',NULL);
		}else{
			$this->db->where('QcAssignedToUserUID !=',NULL);
		}
		$this->db->where('tOrderAssignments.ProjectUID',$ProjectUID);
		$query = $this->db->get()->result_array();

		$OrderUID = [];
		foreach ($query as $key => $value) {
			$OrderUID[$key] = $value['OrderUID'];
		}
		if(sizeof($OrderUID) == 0)
		{
			$OrderUID = '0';
		}

		$status[0] = $this->config->item('keywords')['New Order'];
		$status[1] = $this->config->item('keywords')['Waiting For Images'];
		$status[2] = $this->config->item('keywords')['Image Received'];
		$status[21] = $this->config->item('keywords')['MyOrders'];


		$this->db->select("*");
		$this->db->from('tOrders');
		$this->db->where(array('ProjectUID'=>$ProjectUID));
		$this->db->where_in('tOrders.StatusUID', $status);
		$this->db->where_not_in('tOrders.OrderUID', $OrderUID);
		$this->db->limit(1);
		$query = $this->db->get();
		$queryassign = $query->row();
		if($query->num_rows() > 0)
		{
			if($this->CheckExistingAssignmentOrders($queryassign->OrderUID) == 0)
			{  
				$tOrderAssignmentsArray = array(
					'OrderUID' => $queryassign->OrderUID, 
					'ProjectUID' => $queryassign->ProjectUID,
					'AssignedToUserUID' => $this->loggedid,
					'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
					'AssignedByUserUID' => $this->loggedid,
					'QcAssignedDateTime' => Date('Y-m-d H:i:s', strtotime("now")),
					'QcAssignedToUserUID' => $this->loggedid,
					'QcAssignedByUserUID' => $this->loggedid
				);
				$query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);
				if($this->db->affected_rows() > 0)
				{
					return 1;
				}
			}
			else
			{
				$tOrderAssignmentsArray = array(
					'AssignedToUserUID' => $this->loggedid,
					'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
					'AssignedByUserUID' => $this->loggedid,
					'QcAssignedDateTime' => Date('Y-m-d H:i:s', strtotime("now")),
					'QcAssignedToUserUID' => $this->loggedid,
					'QcAssignedByUserUID' => $this->loggedid
				);
				$this->db->where(array('OrderUID' => $queryassign->OrderUID,'ProjectUID'=>$queryassign->ProjectUID));
				$query = $this->db->update('tOrderAssignment', $tOrderAssignmentArray);
				return 1;
			}
		}else{

			return 0;

		}
	}

	function CheckExistingAssignmentOrders($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tOrderAssignments');
		$this->db->where(array('OrderUID'=>$OrderUID));
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}

	}


	function PickExistingOrderCheck($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tOrderAssignments');
		$this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','left');
		$this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID, 'tOrderAssignments.WorkflowModuleUID'=>$this->config->item('Workflows')['Stacking']));
		$this->db->where('AssignedToUserUID !=',NULL);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return array('Status'=>1,'UserName'=>$query->row()->UserName);
		}else{
			return array('Status'=>0,'UserName'=>'');
		}
	}

	function OrderAssign($OrderUID,$ProjectUID)
	{

		$this->db->select('*');
		$this->db->from('tOrderAssignments');
		$this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID, 'tOrderAssignments.WorkflowModuleUID'=>$this->config->item('Workflows')['Stacking']));
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$tOrderAssignmentsArray = array(
				'WorkflowModuleUID' => $this->config->item('Workflows')['Stacking'],
				'AssignedToUserUID' => $this->loggedid,
				'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
				'AssignedByUserUID' => $this->loggedid,
				'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress'],
			);
			$this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID));
			$query = $this->db->update('tOrderAssignments', $tOrderAssignmentsArray);
			// Update Status to Stacking in Progress
			$this->Common_Model->save('tOrders',['StatusUID'=>$this->config->item('keywords')['Stacking In Progress']], ['OrderUID'=>$OrderUID]);
			return 1;
		}
		else
		{
			$tOrderAssignmentsArray = array(
				'OrderUID' => $OrderUID, 
				'WorkflowModuleUID' => $this->config->item('Workflows')['Stacking'],
				'AssignedToUserUID' => $this->loggedid,
				'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
				'AssignedByUserUID' => $this->loggedid,
				'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress']
			);
			$query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);
			// Update Status to Stacking in Progress
			$this->Common_Model->save('tOrders',['StatusUID'=>$this->config->item('keywords')['Stacking In Progress']], ['OrderUID'=>$OrderUID]);
			return 1;
		}

	}

	//parking orders

	function parkingorders_count_all($post=[])
	{


		$this->db->select("1");


		/*^^^^^ Get MyOrders Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(false, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(false);
		}
		$this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->WorkflowModuleUID."' AND tOrderParking.IsCleared = 0 ");
		$this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}
		

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'PARKINGCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}  

	function parkingorders($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName AS AssignedUserName, b.UserName AS RaisedBy,tOrderParking.Remarks");
		$this->db->select("tOrderParking.Remainder");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');

		/*^^^^^ Get MyOrders Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(false, ['ExcludeOrderBy'=>TRUE]);
		} else {

			$this->Common_Model->GetDocsOrders(false);
			/*^^^^^ Get MyOrders Query ^^^^^*/
		}
		// $this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);


		$this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->WorkflowModuleUID."' AND tOrderParking.IsCleared = 0 ");
		$this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'PARKINGCOUNT');

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$query = $this->db->get();
		return $query->result();  
	}

	function parkingorders_count_filtered($post)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");


		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetDocsOrders(false);
		$this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->WorkflowModuleUID."' AND tOrderParking.IsCleared = 0 ");
		$this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'PARKINGCOUNT');

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}


	// KickBack Orders
	function KickBackcount_all($post=[])
	{


		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}
		// $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->group_start();
		$this->db->where('tOrderWorkflows.IsReversed = 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);
		$this->db->group_end();


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}
		

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}


	function KickBackcount_filtered($post,$module)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");


		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}
		// $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->group_start();
		$this->db->where('tOrderWorkflows.IsReversed = 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);
		$this->db->group_end();


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search


		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}



	function KickBackDocsOutOrders($post,$module,$global='')
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->group_start();
		$this->db->where('tOrderWorkflows.IsReversed = 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);
		$this->db->group_end();


		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');
		
		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();

	}

	/**
	*Function Submitted for Doc Check Queue 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 30 October 2020.
	*/
	function SubmittedforDocCheck_CountAll($post=[])
	{

		$this->db->select("1");

		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("SubmittedforDocCheckCond");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}

	function SubmittedforDocCheck_CountFiltered($post,$module)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");

		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		}

		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("SubmittedforDocCheckCond");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');
		
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}

	function SubmittedforDocCheckOrders($post,$module,$global='')
	{

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID); 

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		//$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true);
		} else {
			
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->Common_Model->GetDocsOrders(true, ['ExcludeOrderBy'=>TRUE]);
		}

		// Docsout queue "Pending from UW" Sub queue conditions
		$QueueTypes = $this->config->item("SubmittedforDocCheckCond");
		$this->db->where_in('tOrderImport.Queue',$QueueTypes);

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');
		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}

		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}

		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);

		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();
		
	}

	/**
	*Function NonWorkable Queue 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 05 November 2020.
	*/
	function NonWorkable_CountAll($post=[])
	{

		$this->db->select("1");

		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->GetDocsOut_NonWorkableOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->GetDocsOut_NonWorkableOrders(true);
		}

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}

	function NonWorkable_CountFiltered($post,$module)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		}
		$this->db->select("1");

		/*^^^^^ Get DocsOut Query ^^^^^*/
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->GetDocsOut_NonWorkableOrders(true, ['ExcludeOrderBy'=>TRUE]);
		} else {

			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->GetDocsOut_NonWorkableOrders(true);
		}

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		// Advanced Search

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');
		
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}

	function NonWorkableOrders($post,$module,$global='')
	{

		$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID); 

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		//$this->GetDocsOut_NonWorkableOrders(true, ['ExcludeOrderBy'=>TRUE]);
		if (isset($post['order']) && !empty($post['order'])) {
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->GetDocsOut_NonWorkableOrders(true);
		} else {
			
			/*^^^^^ Get MyOrders Query ^^^^^*/
			$this->GetDocsOut_NonWorkableOrders(true, ['ExcludeOrderBy'=>TRUE]);
		}

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

		// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'EXCLUDEOTHERSASSIGNEDCOUNT');
		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}

		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}

		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);

		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			// $this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		// $this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();
		
	}



	/**
	*Function DocsOut Non Workable 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 05 November 2020.
	*/
	function GetDocsOrdersQuery($filterexception = true,$Conditions = [])
	{


		$WorkflowModuleUID = $this->config->item('Workflows')['DocsOut'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		$DependentWorkflowModuleUID = $this->Common_Model->getDependentworkflows($this->config->item('Workflows')['DocsOut']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb2->select('mWorkFlowModules.*');
			$this->otherdb2->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb2->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb2->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb2->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb2->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb2->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb2->group_start();
				$this->otherdb2->where($Case_Where, NULL, FALSE);
				$this->otherdb2->group_end();
			}

			$this->otherdb2->where_not_in('tOrders.StatusUID', $status);

			$previous_filtered_orders_sql = $this->otherdb2->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->otherdb1->select('tOrders.OrderUID');

		$this->otherdb1->from('tOrders');
		$this->otherdb1->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["DocsOut"] . '"', 'left');
		$this->otherdb1->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->otherdb1->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->otherdb1->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->otherdb1->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->otherdb1->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->otherdb1->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->otherdb1->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->otherdb1->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['DocsOut'] . '"');
		$this->otherdb1->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		// if (!isset($Conditions['OrderByexception']))
		// 	$this->Common_Model->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		// else
		// 	$this->Common_Model->	AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->otherdb1->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['DocsOut'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup

		//filter order when withdrawal enabled
		$this->otherdb1->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->otherdb1->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->otherdb1->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['DocsOut']);
		$this->otherdb1->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->otherdb1->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb1->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->otherdb1->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->otherdb1->group_start();
		$this->otherdb1->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['DocsOut'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['DocsOut'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->otherdb1->group_end();

		$this->otherdb1->where_in('tOrders.MilestoneUID', $this->config->item('CD_Orders_Milestones'));

		// DocsOutSigningDate is blank
		$this->otherdb1->where("(tOrderImport.DocsOutSigningDate IS NOT NULL AND tOrderImport.DocsOutSigningDate <> '') ", NULL, FALSE);

		// DocsOutClosingDisclosureSendDate is blank

		$this->otherdb1->where("(tOrderImport.DocsOutClosingDisclosureSendDate IS NOT NULL OR tOrderImport.DocsOutClosingDisclosureSendDate <> '')");
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->otherdb1->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '" . $this->config->item('Workflows')['DocsOut'] . "' AND IsCleared = 0)", NULL, FALSE);

		// remove reversed orders
		$this->otherdb1->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = ' . $this->config->item('Workflows')['DocsOut']);
	}

	/**
	*Function Docusout all orders 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Wednesday 18 November 2020.
	*/
	function docsoutallordersquery()
	{
		$this->DocsOut_Orders_Model->GetDocsOrdersQuery(true);
		return $this->otherdb1->get()->result_array();
	}


		/**
	*Function DocsOut Non Workable 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 05 November 2020.
	*/
	function GetDocsOut_NonWorkableOrders($filterexception = true,$Conditions = [])
	{
		$db = isset($Conditions['DBCONNECTION']) ? $this->{$Conditions['DBCONNECTION']} : $this->db;

		$WorkflowModuleUID = $this->config->item('Workflows')['DocsOut'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ###########*/

		/** 
		 * Dependent Workflows
		 * @author Sathis Kannan <sathish.kannan@avanzegroup.com> 
		 * @since Wednesday 15 July 2020 
		 */

		$DependentWorkflowModuleUID = $this->Common_Model->getDependentworkflows($this->config->item('Workflows')['DocsOut']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb2->select('mWorkFlowModules.*');
			$this->otherdb2->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb2->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb2->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb2->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb2->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb2->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb2->group_start();
				$this->otherdb2->where($Case_Where, NULL, FALSE);
				$this->otherdb2->group_end();
			}

			$this->otherdb2->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb2->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$db->select('tOrderWorkflows.WorkflowModuleUID');
		$db->from('tOrders');
		$db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["DocsOut"] . '"', 'left');
		$db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['DocsOut'] . '"');
		$db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$db->join('mCustomerWorkflowModules','mCustomerWorkflowModules.CustomerUID=tOrders.CustomerUID AND mCustomerWorkflowModules.WorkflowModuleUID = '.$WorkflowModuleUID.' AND (mCustomerWorkflowModules.OrderHighlightDuration IS NOT NULL OR mCustomerWorkflowModules.OrderHighlightDuration <> "") ','left');

		$db->where_in('tOrders.MilestoneUID', $this->config->item('DocsOutNonWorkable_Milestones'));

		/*Check Doc Case Enabled*/
		$db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['DocsOut'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup

		//filter order when withdrawal enabled
		$db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['DocsOut']);
		$db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$db->group_start();
			$db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['DocsOut'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['DocsOut'] . "' 
				THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$db->group_end();
		}

		// $db->where_in('tOrders.MilestoneUID', $this->config->item('CD_Orders_Milestones'));
		// DocsOutSigningDate is blank
		$db->where("(tOrderImport.DocsOutSigningDate IS NOT NULL AND tOrderImport.DocsOutSigningDate <> '') ", NULL, FALSE);

		// // DocsOutClosingDisclosureSendDate is blank

		$db->where("(tOrderImport.DocsOutClosingDisclosureSendDate IS NOT NULL AND tOrderImport.DocsOutClosingDisclosureSendDate <> '')");

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$db->group_by('tOrders.OrderUID');

		// Order By
		if (isset($Conditions['ExcludeOrderBy'])) {
			$db->order_by('STR_TO_DATE(tOrderImport.DocsOutSigningDate,"%m/%d/%Y"), STR_TO_DATE(tOrderImport.DocsOutSigningTime,"%h:%i %p")','ASC',FALSE);
		}
	}

	/**
	*Function Docusout Non Workable orders 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Wednesday 18 November 2020.
	*/
	function docsoutnonworkableordersquery()
	{
		$this->otherdb1->select('tOrders.OrderUID');
		$this->GetDocsOut_NonWorkableOrders(true,['DBCONNECTION'=>'otherdb1']);
		$DocsNonWorkableOrdersSQL = $this->otherdb1->get_compiled_select();
		return $this->otherdb1->query($DocsNonWorkableOrdersSQL)->result_array();
	}

}?>
