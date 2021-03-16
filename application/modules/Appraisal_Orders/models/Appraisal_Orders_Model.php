<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Appraisal_Orders_Model extends MY_Model {
	
	function __construct()
	{ 
		parent::__construct();
		$this->loggedid = $this->session->userdata('UserUID');
		$this->UserName = $this->session->userdata('UserName');
		$this->RoleUID = $this->session->userdata('RoleUID');    
	}

	function total_count()
	{
		$parkingenabled = $this->Common_Model->is_parking_enabledforworkflow($this->config->item('Workflows')['Appraisal']);

		$this->db->select("1");

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();

		//filter order when parking disabled
		if(empty($parkingenabled)) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE tOrderParking.OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID='".$this->config->item('Workflows')['Appraisal']."' AND tOrderParking.IsCleared = 0 )",NULL,FALSE);
		}
		
		if($this->RoleType!=1){
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {

				$this->db->group_start();

				$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
				$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$this->db->group_end();
			}
		}
		
		$query = $this->db->count_all_results();
		return $query;
	}

  	// New Orders
	function count_all()
	{
		$this->db->select("1");

		/*^^^^^ Get Appraisal Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if($this->RoleUID !=1) {
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
				$this->db->group_start();
				$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
				$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$this->db->group_end();
			}
		} else {
			$this->db->where('tOrderAssignments.AssignedToUserUID IS NULL');
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}

	function count_filtered($post,$module)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);
		$this->db->select("1");

		/*^^^^^ Get Appraisal Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if($this->RoleUID !=1){
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {

				$this->db->group_start();

				$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
				$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$this->db->group_end();
			}
		} else {
			$this->db->where('tOrderAssignments.AssignedToUserUID IS NULL');
		}

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

	function AppraisalOrders($post,$module,$global='')
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if(empty($global)) {        
			if($this->RoleUID !=1){
				if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
					$this->db->group_start();
					$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
					$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
					$this->db->group_end();
				}
			}else{
				$this->db->where('tOrderAssignments.AssignedToUserUID IS NULL');
			}
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->config->item('Workflows')['Appraisal']);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			$this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		$this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();
		return $output->result();

	}

	// Work In Progress
	function inprogress_count_all()
	{
		$this->db->select("1");

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
			$this->db->where('(tOrderAssignments.AssignedToUserUID IS NOT NULL AND tOrderAssignments.AssignedToUserUID != "" AND tOrderAssignments.AssignedToUserUID != "'.$this->loggedid.'" )',null,false);
		}
		$query = $this->db->count_all_results();
		return $query;
	}

	function inprogress_count_filtered($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);
		$this->db->select("1");

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
			$this->db->where('(tOrderAssignments.AssignedToUserUID IS NOT NULL AND tOrderAssignments.AssignedToUserUID != "" AND tOrderAssignments.AssignedToUserUID != "'.$this->loggedid.'" )',null,false);
		}

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
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName");
		$this->db->select('tOrders.LastModifiedDateTime,tOrderAssignments.AssignedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
			$this->db->where('(tOrderAssignments.AssignedToUserUID IS NOT NULL AND tOrderAssignments.AssignedToUserUID != "" AND tOrderAssignments.AssignedToUserUID != "'.$this->loggedid.'" )',null,false);
		}

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
	function MyOrders($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");

		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
			$this->db->where('tOrderAssignments.AssignedToUserUID ='.$this->loggedid,NULL);
		}

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

	function myorders_count_all()
	{
		$this->db->select("1");

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
			$this->db->where('tOrderAssignments.AssignedToUserUID ='.$this->loggedid,NULL);
		}

		$query = $this->db->count_all_results();
		return $query;
	}
	function myorders_count_filtered($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);
		$this->db->select("1");

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
			$this->db->where('tOrderAssignments.AssignedToUserUID ='.$this->loggedid,NULL);
		}

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

	function GetMyOrdersExcelRecords($post)
	{
		$this->db->select("*,tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['Appraisal']."' AND IsCleared = 0)",NULL,FALSE);

		if($post['advancedsearch']['Status'] == 'workinprogress' && in_array($this->RoleType, $this->config->item('Internal Roles'))){

			$this->db->where('tOrderAssignments.AssignedToUserUID IS NOT NULL');
		}
		else if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {

			$this->db->group_start();
			$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
			$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
			$this->db->group_end();
		}

		if ($post['advancedsearch']!='false' && sizeof(array_filter($post['advancedsearch']))!=0) 
		{
			$filter = $this->Common_Model->advanced_search($post); 
		}

		$this->db->order_by('OrderEntryDatetime');
		$query = $this->db->get();
		return $query->result();  
	}
 	
 	//parking orders
	function parkingorders_count_all()
	{
		$this->db->select("1");

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->config->item('Workflows')['Appraisal']."' AND tOrderParking.IsCleared = 0 ");
		$this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		$query = $this->db->count_all_results();
		return $query;
	}  

	function parkingorders($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);

		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName AS AssignedUserName, b.UserName AS RaisedBy,tOrderParking.Remarks");
		$this->db->select("tOrderParking.Remainder");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();

		$this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->config->item('Workflows')['Appraisal']."' AND tOrderParking.IsCleared = 0 ");
		$this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');

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

	function parkingorders_count_filtered($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($this->config->item("Workflows")["Appraisal"]);
		$this->db->select("1");

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetAppraisalOrders();
		$this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->config->item('Workflows')['Appraisal']."' AND tOrderParking.IsCleared = 0 ");
		$this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');

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
}?>
