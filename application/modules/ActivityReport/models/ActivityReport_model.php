<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ActivityReport_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}
	function getActivityUsers($UserUID)
	{
		$this->db->select('UserName,UserUID');
		$this->db->from('mUsers');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID','left');
		$this->db->where('mUsers.Active',1);
		if(!empty($UserUID))
		{
			for ($i=0; $i < count($UserUID); $i++) { 
				$User .= $UserUID[$i].',';
			}
			$User = trim($User,',');
			$this->db->where('UserUID in ('.$User.')');
		}
		$this->db->where('mUsers.CustomerUID',$this->parameters['DefaultClientUID']);
		$this->db->where('mRole.CustomerUID',$this->parameters['DefaultClientUID']);
		$this->db->where_not_in('mRole.RoleTypeUID',$this->config->item('Super Admin'));
		$this->db->group_by('UserUID');
		
		return $this->db->get()->result();
	}

	function getActivityLogs($post){
		$where = '';
		$users = $post['Process'];
		$FromDate = date('Y-m-d', strtotime($post['FromDate']));
		$ToDate = date('Y-m-d', strtotime($post['ToDate']));
		$Workflow = $post['workflow'];
		
		$where = "WHERE mWorkflowDurations.ClientUID = ".$this->parameters['DefaultClientUID']." AND mWorkflowDurations.Active = ".STATUS_ONE." AND ( DATE(tOrderDurations.CompletedDateTime) BETWEEN '".$FromDate."' AND '".$ToDate."' ) ";
		if(!empty($Workflow)){
			$WorkflowsFilter = implode(', ', array_filter($Workflow));
			$where .= "AND (mWorkflowDurations.WorkflowModuleUID IN (".$WorkflowsFilter.") )";	
		}		
		
		if(!empty($users)){
			$UsersFilter = implode(', ', array_filter($users));
			$where .= "AND (tOrderDurations.UserUID IN (".$UsersFilter.") )";
		}	
		$workActivities = $this->db->query("SELECT mWorkflowDurations.Hours, mUsers.UserName, count( DISTINCT
					tOrderDurations.OrderUID ) as WorkCount, mWorkFlowModules.WorkflowModuleName, DATE(tOrderDurations.CompletedDateTime) as completedDate
					FROM
					tOrderDurations
					LEFT JOIN mWorkflowDurations ON mWorkflowDurations.DurationUID = tOrderDurations.DurationUID
					LEFT JOIN mWorkFlowModules ON mWorkFlowModules.WorkflowModuleUID = mWorkflowDurations.WorkflowModuleUID
					LEFT JOIN mUsers ON mUsers.UserUID = tOrderDurations.UserUID
					".$where."
					GROUP BY tOrderDurations.DurationUID, tOrderDurations.UserUID
					ORDER BY tOrderDurations.UserUID ASC");
		
		if($workActivities->num_rows() > 0){
			return $workActivities->result();
		}else{
			return false;
		}
	}

}
?>
