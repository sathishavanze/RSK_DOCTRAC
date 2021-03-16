<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProductivityReport_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}
	function getProcessUsers($UserUID)
	{
		$this->db->select('mUsers.UserName,mUsers.UserUID');
		$this->db->from('mUsers');
		$this->db->where('mUsers.Active',1);
		if(!empty($UserUID))
		{
			for ($i=0; $i < count($UserUID); $i++) { 
				$User .= $UserUID[$i].',';
			}
			$User = trim($User,',');
			$this->db->where('mUsers.UserUID in ('.$User.')');
		}
		$this->db->group_by('mUsers.UserUID');
		// $this->db->limit(3);
		return $this->db->get()->result();
	}
	function getProductivityReportCounts($post)
	{
		$CustomerUID = $this->parameters['DefaultClientUID'];
		$UserUID = $post['Process'];
		$ProcessUsers = $this->getProcessUsers($UserUID); // get user details with filter user
		//date report filter
		$Status = $post['Status'];
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);
		//echo '<pre>';print_r($datearray);exit;
		$Que_join="";
		$WorkflowStatus="AND tOrderAssignments.WorkflowStatus = 5";
		if($post['WorkflowModuleUID'] == 1)
		{
			$Que_join.=" LEFT JOIN tOrderQueues ON tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus= 'Pending' JOIN mQueues ON tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '15' and mQueues.WorkflowModuleUID='1'";
			$WorkflowStatus="";
		}
		//date wise loop
		$resultdata = [];
		foreach ($datearray as $key => $date)
		{
			$CountWhere = $this->CountWhere($post,$date);
			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			foreach ($ProcessUsers as $value) 
			{
				//select process
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
					WHERE
					 tOrderAssignments.AssignedToUserUID = ".$value->UserUID." ".$WorkflowStatus." ".$CountWhere."
					) as process".$value->UserUID);

				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
					WHERE
					 tOrderAssignments.AssignedToUserUID = ".$value->UserUID." ".$WorkflowStatus." ".$CountWhere."
					) as processOrderUID".$value->UserUID);
			}
			$resultdata[] = $this->db->get()->result();
			//print_r($this->db->last_query());
		}
			
			$CountTotalWhere = $this->CountTotalWhere($post,$datearray);
			$this->db->select("('Total') as date");
			foreach ($ProcessUsers as $value) 
			{
				// process
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
					WHERE
						tOrderAssignments.AssignedToUserUID = ".$value->UserUID." 
					".$CountTotalWhere." 
					) as process".$value->UserUID);

				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
					WHERE
						tOrderAssignments.AssignedToUserUID = ".$value->UserUID." 
					".$CountTotalWhere."
					) as processOrderUID".$value->UserUID);

			}
			
			
			$resultdata[] = $this->db->get()->result();
			//print_r($this->db->last_query());exit;
		// echo '<pre>';print_r($resultdata);exit;
		return $resultdata;
	}

	function getWeekDays($processdate)
	{
		$day_of_week = date('N', strtotime($processdate));
		$given_date = strtotime($processdate);
		$first_of_week =  date('Y-m-d', strtotime("- {$day_of_week} day", $given_date));
		$first_of_week = strtotime($first_of_week);
		for($i=0 ;$i<=7; $i++) {
			$week_array[] = date('Y-m-d', strtotime("+ {$i} day", $first_of_week));
		}
		return $week_array;
	}

	function getDates($date1, $date2) 
	{
		$format = 'Y-m-d';
		$dates = array();
		$current = strtotime($date1);
		$date2 = strtotime($date2);
		$stepVal = '+1 day';
		while( $current <= $date2 ) {
			$dates[] = date($format, $current);
			$current = strtotime($stepVal, $current);
		}
		return $dates;
	}

	function CountTotalWhere($post,$datearray)
	{
		$CustomerUID = $this->parameters['DefaultClientUID'];
		$totalwhere = ' AND tOrders.CustomerUID ='.$CustomerUID;
		if(!empty($post['WorkflowModuleUID']))
		{
			$totalwhere .= " AND tOrderAssignments.WorkflowModuleUID = ".$post['WorkflowModuleUID'];
		}
		if(!empty($datearray))
		{

			if($post['WorkflowModuleUID'] == 1)
			{
				$totalwhere .= " AND ((tOrderAssignments.WorkflowStatus = 5 AND DATE(tOrderAssignments.CompleteDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."')  OR (DATE(tOrderQueues.RaisedDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."'))";
		
			}
			else
			{
			$totalwhere .= " AND DATE(tOrderAssignments.CompleteDateTime) BETWEEN '".$datearray[0]."' AND 
			'".end($datearray)."'";
			}

		}
		return $totalwhere;
	}
	function CountWhere($post,$date)
	{
		$CustomerUID = $this->parameters['DefaultClientUID'];
		$CountWhere = ' AND tOrders.CustomerUID ='.$CustomerUID;
		if(!empty($date))
		{
			if($post['WorkflowModuleUID'] == 1)
			{
				$CountWhere .= " AND ((tOrderAssignments.WorkflowStatus = 5 AND DATE(tOrderAssignments.CompleteDateTime) = '".$date."')  OR (DATE(tOrderQueues.RaisedDateTime)='".$date."'))";
			}
			else
			{
			$CountWhere .= " AND DATE(tOrderAssignments.CompleteDateTime) = '".$date."'";	
			}
		}
		if(!empty($post['WorkflowModuleUID']))
		{
			$CountWhere .= " AND tOrderAssignments.WorkflowModuleUID = ".$post['WorkflowModuleUID'];
		}
		return $CountWhere;
	}


	function count_filtered($post)
	{
		$this->fetchorder_query($post);
		// Datatable Search
		$this->Common_Model->Datatable_Search_having($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_InflowReportList($post)
	{
		$this->fetchorder_query($post);
		// Datatable Search
		$this->Common_Model->Datatable_Search_having($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if (!empty($post['length'])) {
			$this->db->limit($post['length'], $post['start']);
		} else {

		}
		$query = $this->db->get();
		return $query->result();
	}

	function count_all($post)
	{
		$this->fetchorder_query($post);
		$query = $this->db->count_all_results();
		return $query;
	}

	function fetchorder_query($post)
	{
		$this->db->select("CONCAT_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName) as BorrowerNames");
		$this->db->select('LoanNumber,MilestoneName,LoanType,PropertyStateCode,LoanProcessor,tOrders.OrderUID');
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
		$this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','LEFT');
		$this->db->join('mMilestone','mMilestone.MilestoneUID = tOrders.MilestoneUID','LEFT');
		$this->db->where('tOrders.OrderUID in('.$post['OrderUID'].')');
		$this->db->group_by('tOrders.OrderUID');
	}
}
?>
