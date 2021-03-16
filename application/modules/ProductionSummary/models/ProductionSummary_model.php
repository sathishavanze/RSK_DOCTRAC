<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProductionSummary_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}
	function getActivityUsers($UserUID)
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

	function getActivityLogs($post){
		$where = '';
		$users = $post['Process'];
		$FromDate = date('Y-m-d', strtotime($post['FromDate']));
		$ToDate = date('Y-m-d', strtotime($post['ToDate']));
		
		$where = "mWorkflowDurations.ClientUID = ".$this->parameters['DefaultClientUID']." AND mWorkflowDurations.Active = ".STATUS_ONE." AND ( DATE(tOrderDurations.CompletedDateTime) BETWEEN '".$FromDate."' AND '".$ToDate."' ) ";
		
		if(!empty($users)){
			$UsersFilter = implode(', ', array_filter($users));
			$where .= "AND (tOrderDurations.UserUID IN (".$UsersFilter.") )";
		}

		// Get Workflow Details
		$WorkflowDetails = $this->getWorkflowDetailsActivityReport($where);

		/* $workActivities = $this->db->query("SELECT SUM(mWorkflowDurations.Hours) as Hours , mUsers.UserName, count( DISTINCT tOrderDurations.OrderUID ) as WorkCount, DATE(tOrderDurations.CompletedDateTime) as completedDate
					FROM
					tOrderDurations
					LEFT JOIN mWorkflowDurations ON mWorkflowDurations.DurationUID = tOrderDurations.DurationUID
					LEFT JOIN mUsers ON mUsers.UserUID = tOrderDurations.UserUID
					".$where."
					GROUP BY tOrderDurations.UserUID
					ORDER BY tOrderDurations.UserUID ASC"); */
		$this->db->select('SUM(mWorkflowDurations.Hours) as Hours , mUsers.UserName, count( DISTINCT tOrderDurations.OrderUID ) as WorkCount, DATE(tOrderDurations.CompletedDateTime) as completedDate');

		$this->db->from('tOrderDurations');
		$this->db->join('mWorkflowDurations','mWorkflowDurations.DurationUID = tOrderDurations.DurationUID','LEFT');
		$this->db->join('mUsers','mUsers.UserUID = tOrderDurations.UserUID','LEFT');

		foreach ($WorkflowDetails as $key => $value) {
			$this->db->select('COUNT(MWD_'.$value->WorkflowModuleUID.'.WorkflowModuleUID) AS '.$value->SystemName);
			$this->db->join('mWorkflowDurations AS MWD_'.$value->WorkflowModuleUID,'MWD_'.$value->WorkflowModuleUID.'.DurationUID = mWorkflowDurations.DurationUID AND mWorkflowDurations.WorkflowModuleUID = '.$value->WorkflowModuleUID,'LEFT');
		}

		$this->db->where($where, NULL, FALSE);
		$this->db->group_by('tOrderDurations.UserUID');
		$this->db->order_by('tOrderDurations.UserUID','ASC');
		$workActivities = $this->db->get()->result();
		// echo '<pre>';print_r($workActivities);exit;
		if(count($workActivities) > 0){
			return array('workActivities'=>$workActivities, 'WorkflowDetails'=>$WorkflowDetails);
		}else{
			return false;
		}
	}

	// Get workflow details based on advanced filter
	function getWorkflowDetailsActivityReport($where) {
		$this->db->select('mWorkFlowModules.WorkflowModuleUID, mWorkFlowModules.WorkflowModuleName, mWorkFlowModules.SystemName');

		$this->db->from('tOrderDurations');
		$this->db->join('mWorkflowDurations','mWorkflowDurations.DurationUID = tOrderDurations.DurationUID','LEFT');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mWorkflowDurations.WorkflowModuleUID','LEFT');
		$this->db->where($where, NULL, FALSE);
		$this->db->group_by('mWorkflowDurations.WorkflowModuleUID');
		$this->db->order_by('mWorkflowDurations.WorkflowModuleUID','ASC');
		return $workActivities = $this->db->get()->result();

	}

}
?>
