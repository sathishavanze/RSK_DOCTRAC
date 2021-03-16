<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class FollowUpReport_model extends MY_Model {

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
	function getSubQueues($WorkflowModuleUID){
		$this->db->select('QueueUID,QueueName');
		$this->db->from('mQueues');
		$this->db->where('WorkflowModuleUID',$WorkflowModuleUID);
		
		return $this->db->get()->result();	
	}
	function getFollowup_workflow($post){
		$getSubQueues = $this->getSubQueues($post['workflow']); 

		$Status = $post['Status'];
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);	

		$resultdata = [];
		foreach ($datearray as $key => $date)
		{
			
			$CountWhere = "AND ((DATE(tOrders.OrderEntryDateTime) = '".$date."') )";
			$CountWhere .= $this->getOrderStatus($Status);
			
			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			foreach ($getSubQueues as $value) 
			{
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderFollowUp ON tOrderFollowUp.OrderUID = tOrders.OrderUID
					WHERE (tOrderFollowUp.QueueUID = '".$value->QueueUID."')
					AND (DATE(tOrderFollowUp.RaisedDateTime) = '".$date."')
					". $CountWhere."
					) as countIn".$value->QueueUID);
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderFollowUp ON tOrderFollowUp.OrderUID = tOrders.OrderUID
					WHERE (tOrderFollowUp.QueueUID = '".$value->QueueUID."')
					AND (DATE(tOrderFollowUp.ClearedDateTime) = '".$date."')
					". $CountWhere."
					) as countOut".$value->QueueUID);
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderFollowUp ON tOrderFollowUp.OrderUID = tOrders.OrderUID
					LEFT JOIN mQueues ON tOrderFollowUp.QueueUID = mQueues.QueueUID
					WHERE (tOrderFollowUp.QueueUID = '".$value->QueueUID."')
					AND (DATEDIFF(tOrderFollowUp.ClearedDateTime, tOrderFollowUp.RaisedDateTime) >= mQueues.FollowupDuration)
					". $CountWhere."
					) as missedTat".$value->QueueUID);
				//$this->db->where('DATEDIFF(end_date, start_date) >=',  365);
			}
			$resultdata[] = $this->db->get()->result();
		}
		
		return $resultdata;
		
	}
	function getFollowup_agent($post){
		$UserUID = $post['Process'];
		$ProcessUsers = $this->getProcessUsers($UserUID); 

		$Status = $post['Status'];
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);

		$resultdata = [];
		foreach ($datearray as $key => $date)
		{
			$CountWhere = "AND ((DATE(tOrders.OrderEntryDateTime) = '".$date."') )";
			$CountWhere .= $this->getOrderStatus($Status);

			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			foreach ($ProcessUsers as $value) 
			{
				$CountWherein = "AND ((DATE(tOrderFollowUp.RaisedDateTime) = '".$date."') )";
				$CountWhereOut = "AND ((DATE(tOrderFollowUp.ClearedDateTime) = '".$date."') )";

				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderFollowUp ON tOrderFollowUp.OrderUID = tOrders.OrderUID
					WHERE (tOrderFollowUp.RaisedByUserUID = '".$value->UserUID."')
					" .$CountWherein."
					" .$CountWhere."
					) as countIn".$value->UserUID);
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderFollowUp ON tOrderFollowUp.OrderUID = tOrders.OrderUID
					WHERE (tOrderFollowUp.RaisedByUserUID = '".$value->UserUID."')
					" .$CountWherein."
					" .$CountWhere."
					) as countOut".$value->UserUID);
			}
			$resultdata[] = $this->db->get()->result();
		}

		return $resultdata;
	}


	function getOrderStatus($status)
	{
		$this->db->select('GROUP_CONCAT(tOrders.OrderUID)');
		$this->Common_Model->GetCompletedQueueOrders();
		$Completedorders = $this->db->get()->row()->OrderUID;
		$where = '';
		if(!empty($Completedorders))
		{
			if($status == 'Pending'){
				$where =" AND tOrders.OrderUID NOT IN (".$Completedorders.")";
			}else{
				$where =" AND tOrders.OrderUID IN (".$Completedorders.")";
			}			
		}
		return $where;
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

}
?>
