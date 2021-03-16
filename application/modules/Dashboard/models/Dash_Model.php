<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dash_Model extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	function workflow_bind()
	{
		$query = $this->db->get("mWorkFlowModules");
		return $query->result();
	}

	function ind_bind($WorkFlowUID)
	{

		$sql="select AssignedToUserUID from tOrderAssignments where WorkflowModuleUID=$WorkFlowUID";
		$query = $this->db->query($sql);
		//print_r($query);
		//exit;
		$res = $query->result_array();
		//print_r($res);
		$WorkFlowStatus=array();

//		foreach ($res as $val)
//		{

			//$userID = $val["AssignedToUserUID"];

			//$qry1="select mUsers.UserName,sum(case when tOrderAssignments.WorkFlowStatus = 5 then 1 else 0 end) CompleteCount,sum(case when tOrderAssignments.WorkFlowStatus != 5  then 1 else //0 end) PendingCount, count(tOrderAssignments.AssignedToUserUID) as TotalCount
//from tOrderAssignments LEFT JOIN mUsers ON mUsers.UserUID = tOrderAssignments.AssignedByUserUID 
//WHERE tOrderAssignments.WorkflowModuleUID =$WorkFlowUID and tOrderAssignments.AssignedToUserUID=$userID GROUP BY mUsers.UserName";


		$sql="select mUsers.UserName,TotalInflow.Inflow,
		sum(case when tOrderAssignments.WorkFlowStatus = 5 then 1 else 0 end) CompleteCount,
		sum(case when tOrderAssignments.WorkFlowStatus != 5  then 1 else 0 end) PendingCount
		from tOrderAssignments 
		JOIN mUsers ON mUsers.UserUID = tOrderAssignments.AssignedByUserUID
LEFT JOIN (
SELECT count(*) AS Inflow, WorkflowModuleUID  from tOrderWorkflows WHERE 
WorkflowModuleUID=$WorkFlowUID ) as TotalInflow ON TotalInflow.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID 
WHERE tOrderAssignments.WorkflowModuleUID= $WorkFlowUID";


/*

	$qry1="select mUsers.UserName,
		sum(case when tOrderAssignments.WorkFlowStatus = 5 then 1 else 0 end) CompleteCount,
		sum(case when tOrderAssignments.WorkFlowStatus != 5  then 1 else 0 end) PendingCount,
		count(tOrderAssignments.AssignedToUserUID) as TotalCount
		from tOrderAssignments 
		JOIN mUsers ON mUsers.UserUID = tOrderAssignments.AssignedByUserUID
		WHERE tOrderAssignments.WorkflowModuleUID = $WorkFlowUID 
		GROUP BY mUsers.UserUID";

*/
		$query = $this->db->query($qry1,false,true,false)->result_array();	

		$res_arr = array();

		foreach ($query as $row)
		{

			$username = $row['UserName'];
			$complete = $row['CompleteCount'];
			$pending = $row['PendingCount'];
			$total = $row['Inflow'];

				if (($complete ==0) && ($total == 0))
				{
					$ratio = 0;	
				}
				else
				{
					$ratio = $complete / $total * 100;
				}

			$ratio = sprintf('%0.2f', $ratio);

			$res_arr[] = array("work_flow_id" => $WorkFlowUID, "total_flow" => $total, 
				"complete" => $complete, "pending" => $pending, "success" => $ratio, 
				"period" => $username, "repotype" => 'Individual' );
		}

		return $res_arr;


/*

		$username = $query[0]['UserName'];
		$complete = $query[0]['CompleteCount'];
		$pending = $query[0]['PendingCount'];
		$total = $query[0]['TotalCount'];

			if (($complete ==0) && ($total == 0))
			{
				$ratio = 0;	
			}
			else
			{
				$ratio = $complete / $total * 100;
			}

		$res_arr[] = array("work_flow_id" => $WorkFlowUID, "total_flow" => $total, 
			"complete" => $complete, "pending" => $pending, "success" => $ratio, 
			"period" => $username, "repotype" => 'Individual' );

	//}

	return $res_arr;
	//var_dump($res_arr);
	//exit;
*/

}


function period_ind_bind($WorkFlowUID,$newadv_fromDate,$newadv_toDate)
{

		$sql="select mUsers.UserName,TotalInflow.Inflow,
		sum(case when tOrderAssignments.WorkFlowStatus = 5 then 1 else 0 end) CompleteCount,
		sum(case when tOrderAssignments.WorkFlowStatus != 5  then 1 else 0 end) PendingCount
		from tOrderAssignments 
		JOIN mUsers ON mUsers.UserUID = tOrderAssignments.AssignedByUserUID
LEFT JOIN (
SELECT count(*) AS Inflow, WorkflowModuleUID  from tOrderWorkflows WHERE 
WorkflowModuleUID=$WorkFlowUID AND 
DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '$newadv_fromDate' AND '$newadv_toDate' 
) as TotalInflow ON TotalInflow.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID 
WHERE tOrderAssignments.WorkflowModuleUID= 1 AND
		DATE_FORMAT(tOrderAssignments.AssignedDatetime, '%Y-%m-%d') BETWEEN '$newadv_fromDate' AND '$newadv_toDate'";

	/*
		$sql="select mUsers.UserName,
		sum(case when tOrderAssignments.WorkFlowStatus = 5 then 1 else 0 end) CompleteCount,
		sum(case when tOrderAssignments.WorkFlowStatus != 5  then 1 else 0 end) PendingCount,
		count(tOrderAssignments.AssignedToUserUID) as TotalCount
		from tOrderAssignments 
		JOIN mUsers ON mUsers.UserUID = tOrderAssignments.AssignedByUserUID
		WHERE tOrderAssignments.WorkflowModuleUID = $WorkFlowUID AND
		DATE_FORMAT(tOrderAssignments.AssignedDateTime, '%Y-%m-%d') BETWEEN '$newadv_fromDate' AND '$newadv_toDate' GROUP BY mUsers.UserUID";
	*/

		//print_r("<pre>");
		//print_r($sql);
		//exit;

		$query = $this->db->query($sql,false,true,false)->result_array();	

		$res_arr = array();

		foreach ($query as $row)
		{

			$username = $row['UserName'];
			$complete = $row['CompleteCount'];
			$pending = $row['PendingCount'];
			$total = $row['Inflow'];

				if (($complete ==0) && ($total == 0))
				{
					$ratio = 0;	
				}
				else
				{
					$ratio = $complete / $total * 100;
				}

			$ratio = sprintf('%0.2f', $ratio);

			$res_arr[] = array("work_flow_id" => $WorkFlowUID, "total_flow" => $total, 
				"complete" => $complete, "pending" => $pending, "success" => $ratio, 
				"period" => $username, "repotype" => 'Individual' );
		}

		return $res_arr;

	}


	function Workflow_Success_Bind($WorkFlowUID,$newadv_fromDate,$newadv_toDate)
	{

//		$sql="select DATE_FORMAT(tOrderAssignments.AssignedDateTime,'%d-%b') as Date,
//				count(tOrderAssignments.AssignedToUserUID) as TotalCount,
//				sum(case when tOrderAssignments.WorkFlowStatus = 5 then 1 else 0 end) CompleteCount,
//				sum(case when tOrderAssignments.WorkFlowStatus != 5  then 1 else 0 end) PendingCount
//				from tOrderAssignments where WorkflowModuleUID=$WorkFlowUID  and 
//				DATE(tOrderAssignments.AssignedDateTime) BETWEEN '$newadv_fromDate' AND '$newadv_toDate'";

		$sql="select DATE_FORMAT(tOrderAssignments.AssignedDateTime,'%d-%b') as Date,
				count(tOrderAssignments.AssignedToUserUID) as TotalCount,
				sum(case when tOrderAssignments.WorkFlowStatus = 5 then 1 else 0 end) CompleteCount,
				sum(case when tOrderAssignments.WorkFlowStatus != 5  then 1 else 0 end) PendingCount
			from tOrderAssignments where WorkflowModuleUID=$WorkFlowUID  
			and DATE_FORMAT(tOrderAssignments.AssignedDateTime, '%Y-%m-%d') BETWEEN '$newadv_fromDate' AND '$newadv_toDate' GROUP BY DATE(tOrderAssignments.AssignedDateTime)";

		$query = $this->db->query($sql,false,true,false)->result_array();	

		$res_arr = array();

		foreach ($query as $row)
		{

			$username = $row['Date'];
			$complete = $row['CompleteCount'];
			$pending = $row['PendingCount'];
			$total = $row['TotalCount'];

				if (($complete ==0) && ($total == 0))
				{
					$ratio = 0;	
				}
				else
				{
					$ratio = $complete / $total * 100;
				}

			$ratio = sprintf('%0.2f', $ratio);

			$res_arr[] = array("work_flow_id" => $WorkFlowUID, "total_flow" => $total, 
				"complete" => $complete, "pending" => $pending, "success" => $ratio, 
				"Date" => $username );
		}

		//print_r("<pre></pre>");
		//print_r($res_arr);
		//exit;
		return $res_arr;

	}



}
?>