<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class CompletedReportmodel extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}
	function getProcessUsers($UserUID)
	{
		$this->db->select('UserName,UserUID');
		$this->db->from('mUsers');
		$this->db->where('Active',1);
		if(!empty($UserUID))
		{
			for ($i=0; $i < count($UserUID); $i++) { 
				$User .= $UserUID[$i].',';
			}
			$User = trim($User,',');
			$this->db->where('UserUID in ('.$User.')');
		}
		$this->db->group_by('UserUID');
		
		return $this->db->get()->result();
	}
	function WorkflowQueues_Datatable_Search($post)
	{
		if (!empty($post['search_value'])) {
			$like = "";
			foreach ($post['column_search'] as $key => $item) { 
				if ($key === 0) { 
					$like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
				}
			}
			$like .= ") ";
			$this->db->where($like, null, false);
		}
	}

	function WorkflowQueues_Datatable_OrderBy($post)
	{
		if (!empty($post['order']))
		{
			if($post['column_order'][$post['order'][0]['column']]!='')
			{
				$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
			}
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}
	function FilterByUserCommon($post, $table) {
		$Workflow = $post['workflow'];
		$Users = $post['Process'];

		if(!empty($Users)){
			$this->db->where_in($table.'.CompletedByUserUID', $Users);
		}
		
	}
	function FilterByDateCommon($post, $table){
		$FromDate = date('Y-m-d H:i:s', strtotime($post['FromDate']));
		$ToDate = date('Y-m-d 23:59:59', strtotime($post['ToDate']));
		if(isset($post['FromDate']) && isset($post['ToDate'])){
			$this->db->where($table.'.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
		}
	}
	function compainAssignAndHistoryOld($post, $count = false){

		$WorkflowModuleUID = $post['WorkflowModuleUID'];

		$this->db->select('tOrderAssignmentsHistory.AssignedDatetime,tOrderAssignmentsHistory.CompleteDateTime, completeduserhistory.UserName AS CompletedBy, tOrderAssignmentsHistory.WorkflowModuleUID');
		$this->db->from('tOrderAssignmentsHistory');
		$this->db->join('tOrderWorkflows as HistoryWorkflow', 'HistoryWorkflow.OrderUID = tOrderAssignmentsHistory.OrderUID AND tOrderAssignmentsHistory.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
		$this->db->join('mUsers as completeduserhistory','completeduserhistory.UserUID = tOrderAssignments.CompletedByUserUID','left');

		$this->FilterByUserCommon($post['advancedsearch'], 'tOrderAssignmentsHistory');
		$this->FilterByDateCommon($post['advancedsearch'], 'tOrderAssignmentsHistory');
        $this->db->group_by(array('tOrderAssignmentsHistory.CompletedByUserUID', 'tOrderAssignmentsHistory.CompleteDateTime'));
		$AssignmentsHistoryQuery = $this->db->get_compiled_select();

		$this->db->select('tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime, completeduser.UserName AS CompletedBy, tOrderAssignments.WorkflowModuleUID');
		$this->db->from('tOrderAssignments');
		$this->db->join('tOrderWorkflows as Workflow', 'Workflow.OrderUID = tOrderAssignments.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
		$this->db->join('mUsers as completeduser','completeduser.UserUID = tOrderAssignments.CompletedByUserUID','left');
		$this->FilterByUserCommon($post['advancedsearch'], 'tOrderAssignments');
		$this->FilterByDateCommon($post['advancedsearch'], 'tOrderAssignments');
		$this->db->group_by(array('tOrderAssignments.CompletedByUserUID', 'tOrderAssignments.CompleteDateTime'));

		$AssignmentsQuery = $this->db->get_compiled_select();
		// $this->Common_Model->DynamicColumnsCommonQuery($post['WorkflowModuleUID'], TRUE);		
		// $this->Common_Model->AllWorkflowQueue_CommonQuery($post['WorkflowModuleUID'], TRUE);	
		$this->db->select('tOrders.*');
		$this->db->from('tOrders');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		
        // Datatable Search
		$this->WorkflowQueues_Datatable_Search($post);
        // Datatable OrderBy  
		$this->WorkflowQueues_Datatable_OrderBy($post);
		$this->db->where('tOrderAssignments.CompletedByUserUID IS NOT NULL',NULL, FALSE);

		$common_query = $this->db->get_compiled_select();
		if(isset($post['length']) && $post['length'] != ''){
			$query = $this->db->query($common_query . ' UNION ALL ' . $AssignmentsHistoryQuery . ' UNION ALL ' . $AssignmentsQuery . ' limit ' . $post['start'] . ',' . $post['length']);
		}else{
			$query = $this->db->query($common_query . ' UNION ALL ' . $AssignmentsHistoryQuery . ' UNION ALL ' . $AssignmentsQuery);
		}
		// echo "<pre>";print_r($query->result());exit;
		$query = $this->db->get();
		if($count == true){
			return $query->num_rows();
		}
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}
	function compainAssignAndHistory($post, $count = false){

		$WorkflowModuleUID = $post['WorkflowModuleUID'];

		if($count != false){
			$this->db->select('1');
		}else{
			$this->db->select('tOrders.*, tOrderImport.*, mMilestone.*,tOrderAssignmentsHistory.AssignedDatetime,tOrderAssignmentsHistory.CompleteDateTime AS completeddatetime, mUsers.UserName AS completedby, tOrderAssignmentsHistory.WorkflowModuleUID');
		}
		
		$this->db->from('tOrderAssignmentsHistory');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrderAssignmentsHistory.OrderUID AND tOrderAssignmentsHistory.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
		$this->db->join('mUsers','mUsers.UserUID = tOrderAssignmentsHistory.CompletedByUserUID','left');
		$this->db->join('tOrders', 'tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		// $this->Common_Model->DynamicColumnsCommonQuery($post['WorkflowModuleUID']);	
		$this->Common_Model->AllWorkflowQueue_CommonQuery($post['WorkflowModuleUID']);
		if($count != 'all'){	
			$this->FilterByUserCommon($post['advancedsearch'], 'tOrderAssignmentsHistory');
			$this->FilterByDateCommon($post['advancedsearch'], 'tOrderAssignmentsHistory');
		}
		// $this->WorkflowQueues_Datatable_Search($post);
		// $this->WorkflowQueues_Datatable_OrderBy($post);
        $this->db->group_by(array('tOrderAssignmentsHistory.CompleteDateTime'));
		$AssignmentsHistoryQuery = $this->db->get_compiled_select();

		
		if($count != false){
			$this->db->select('1');
		}else{
			$this->db->select('tOrders.*, tOrderImport.*, 	mMilestone.*,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime AS completeddatetime, mUsers.UserName AS completedby, tOrderAssignments.WorkflowModuleUID');
		}
		$this->db->from('tOrderAssignments');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrderAssignments.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
		$this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.CompletedByUserUID','left');
		$this->db->join('tOrders', 'tOrderAssignments.OrderUID = tOrders.OrderUID');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		
		// $this->Common_Model->DynamicColumnsCommonQuery($post['WorkflowModuleUID']);
		$this->Common_Model->AllWorkflowQueue_CommonQuery($post['WorkflowModuleUID']);	
		if($count != 'all'){			
			$this->FilterByUserCommon($post['advancedsearch'], 'tOrderAssignments');
			$this->FilterByDateCommon($post['advancedsearch'], 'tOrderAssignments');
		}
		// $this->WorkflowQueues_Datatable_Search($post);
		// $this->WorkflowQueues_Datatable_OrderBy($post);
		// $this->db->where('tOrderAssignments.CompletedByUserUID IS NOT NULL',NULL, FALSE);
        $this->db->group_by(array('tOrderAssignments.CompleteDateTime'));

		$AssignmentsQuery = $this->db->get_compiled_select();
		if(isset($post['length']) && $post['length'] != ''){
			if($count != false){
				$query = $this->db->query($AssignmentsHistoryQuery . ' UNION ALL ' . $AssignmentsQuery . ' limit ' . $post['start'] . ',' . $post['length']);
			}else {
				$query = $this->db->query($AssignmentsHistoryQuery . ' UNION ALL ' . $AssignmentsQuery . ' ORDER BY `completeddatetime` DESC ' . ' limit ' . $post['start'] . ',' . $post['length']);
			}
		}else{
			if($count != false){
				$query = $this->db->query($AssignmentsHistoryQuery . ' UNION ALL ' . $AssignmentsQuery );
			}else {
				$query = $this->db->query($AssignmentsHistoryQuery . ' UNION ALL ' . $AssignmentsQuery . ' ORDER BY `completeddatetime` DESC');
			}
		}	
		if($count != false){
			return $query->num_rows();
		}
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}
	function selectCompletedFields(){
		$this->db->select('tOrders.*, tOrderAssignments.CompletedByUserUID AS CompletedBy,tOrderAssignmentsHistory.CompletedByUserUID AS ReWorkBy, mUsers.UserName, tOrderAssignments.WorkflowModuleUID as WorkflowModuleUID, tOrderAssignments.WorkflowModuleUID as ReWorkflowModuleUID, "No" AS IsRework');
		$this->db->select('DATE_FORMAT(tOrderAssignments.CompleteDateTime, "%m/%d/%Y %H:%i") As CompleteDateTime', false);
		$this->db->select('DATE_FORMAT(tOrderAssignmentsHistory.CompleteDateTime, "%m/%d/%Y %H:%i") As ReWorkDateTime', false);
		$this->db->select('DATE_FORMAT(tOrderAssignments.AssignedDatetime, "%m/%d/%Y %H:%i") As AssignedDatetime', false);
		$this->db->select('DATE_FORMAT(tOrderAssignmentsHistory.AssignedDatetime, "%m/%d/%Y %H:%i") As ReworkAssignedDatetime', false);
	}
	function getCompletedTotal($post) {

		$Workflow = $post['workflow'];
		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderAssignmentsHistory','tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$Workflow.'"', 'left');
		$this->db->join('mUsers', 'mUsers.UserUID = tOrderAssignments.CompletedByUserUID OR mUsers.UserUID = tOrderAssignmentsHistory.CompletedByUserUID');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->group_by(array('tOrderAssignments.CompletedByUserUID','tOrderAssignments.CompleteDateTime'));		
	}

	function FilterByUser($post) {
		$Workflow = $post['workflow'];
		$Users = $post['Process'];
		$this->db->group_start();
		$this->db->where_in('tOrderAssignments.CompletedByUserUID', $Users);
		$this->db->or_where_in('tOrderAssignmentsHistory.CompletedByUserUID', $Users);	
		$this->db->group_end(); // Close bracket	
	}
	function FilterByDate($post){
		$FromDate = date('Y-m-d H:i:s', strtotime($post['FromDate']));
		$ToDate = date('Y-m-d 23:59:59', strtotime($post['ToDate']));
		$this->db->group_start();
		$this->db->where('tOrderAssignments.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
		$this->db->or_where('tOrderAssignmentsHistory.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
		$this->db->group_end();
	}
	function count_all($post)
	{
		$post['length'] = '';
		$query = $this->compainAssignAndHistory($post, 'all');
		return $query;
	}
	function count_filtered($post)
	{
		$post['length'] = '';
		$query = $this->compainAssignAndHistory($post, 'filter');
		return $query;
	}
	function getCompletedReport($post){
		$advancedsearch = $post['advancedsearch'];
		$Workflow = $advancedsearch['workflow'];
		$Users = $advancedsearch['Process'];

		$query = $this->compainAssignAndHistory($post);
		return $query;

	}
	function getCompletedReportExcel($post){
		$post['length'] = '';
		$query = $this->compainAssignAndHistory($post);
		return $query;

	}
}
?>
