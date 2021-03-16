<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class CycleTimeReport_model extends MY_Model {

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

	function get_CycleTimeReportCounts($post)
	{
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);
		$resultdata = [];
		foreach ($datearray as $key => $date)
		{
			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			$where = ' AND DATE(tOrderAssignments.CompleteDateTime) = "'.date('Y-m-d',strtotime($date)).'"';
			$this->CycleTimeQuery($where);
			$resultdata[] = $this->db->get()->result();
		}
		return $resultdata;
	}
	function get_AgentCycleTimeReportCounts($post)
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);
		$Processor = $this->getProcessUsers($post['Processor']);
		$resultdata = [];

		foreach ($datearray as $key => $date)
		{
				$this->db->query('SET SESSION group_concat_max_len = 1000000000;');
			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			$where = ' AND DATE(tOrderAssignments.CompleteDateTime) = "'.date('Y-m-d',strtotime($date)).'"';
			foreach ($Processor as $key => $value) 
			{	
				$where .= ' AND tOrderAssignments.AssignedToUserUID ='.$value->UserUID;
				$this->db->select('(SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.') as FundedOrderCount'.$value->UserUID);
				$this->db->select('(SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.')  as FundedOrderUID'.$value->UserUID);
				$this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime)) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.') as FundedAvg'.$value->UserUID);	
			}
			$resultdata[] = $this->db->get()->row();
		}
		return $resultdata;
	}
	
	function get_subqueuesCycleTimeReportCounts($post)
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);
		$resultdata = [];

		foreach ($datearray as $key => $date)
		{
			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			$where = ' AND DATE(tOrderQueues.CompletedDateTime) = "'.date('Y-m-d',strtotime($date)).'"';
			$whereWorkflow = ' AND DATE(tOrderAssignments.CompleteDateTime) = "'.date('Y-m-d',strtotime($date)).'"';

			$this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime)) FROM tOrderAssignments left join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowModuleUID = '.$post['WorkflowModuleUID'].' AND tOrderAssignments.WorkflowStatus = 5'.$whereWorkflow.') as FundedAvg');
			$this->db->select('(SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments left join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowModuleUID = '.$post['WorkflowModuleUID'].' AND tOrderAssignments.WorkflowStatus = 5'.$whereWorkflow.') as FundedOrderCount');
			
			foreach ($post['Queues'] as $Queue) 
			{
				$where .= ' AND tOrderQueues.QueueUID ='.$Queue->QueueUID;
				// $this->db->select('(SELECT GROUP_CONCAT(DISTINCT tOrderQueues.OrderUID) FROM tOrderQueues left join tOrders on tOrders.OrderUID = tOrderQueues.OrderUID left join tOrderAssignments on tOrderAssignments.OrderUID = tOrderQueues.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.')  as FundedOrderUID'.$Queue->QueueUID);
				$this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime)) FROM tOrderQueues left join tOrders on tOrders.OrderUID = tOrderQueues.OrderUID left join tOrderAssignments on tOrderAssignments.OrderUID = tOrderQueues.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.')  as FundedAvg'.$Queue->QueueUID);
				$this->db->select('(SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderQueues left join tOrders on tOrders.OrderUID = tOrderQueues.OrderUID left join tOrderAssignments on tOrderAssignments.OrderUID = tOrderQueues.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.')  as FundedOrderCount'.$Queue->QueueUID);
			}
			$resultdata[] = $this->db->get()->row();
		}
			// echo '<pre>';print_r($resultdata);exit;
		return $resultdata;
	}

	function get_subqueuesAgentCycleTimeReportCounts($post)
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);
		$Processor = $this->getProcessUsers($post['Processor']);
		$resultdata = [];

		foreach ($datearray as $key => $date)
		{
			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			$where = ' AND DATE(tOrderQueues.CompletedDateTime) = "'.date('Y-m-d',strtotime($date)).'"';
			$whereWorkflow = ' AND DATE(tOrderAssignments.CompleteDateTime) = "'.date('Y-m-d',strtotime($date)).'"';

			foreach ($Processor as $key => $value)
			{
			$where .= ' AND tOrderQueues.RaisedByUserUID = '.$value->UserUID;
			$whereWorkflow .= ' AND tOrderAssignments.AssignedToUserUID = '.$value->UserUID;
				$this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime)) FROM tOrderAssignments left join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowModuleUID = '.$post['WorkflowModuleUID'].' AND tOrderAssignments.WorkflowStatus = 5'.$whereWorkflow.') as FundedAvg'.$value->UserUID);
				$this->db->select('(SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments left join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowModuleUID = '.$post['WorkflowModuleUID'].' AND tOrderAssignments.WorkflowStatus = 5'.$whereWorkflow.') as FundedOrderCount'.$value->UserUID);

				foreach ($post['Queues'] as $Queue) 
				{
					$where .= ' AND tOrderQueues.QueueUID ='.$Queue->QueueUID;
					$this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime)) FROM tOrderQueues left join tOrders on tOrders.OrderUID = tOrderQueues.OrderUID left join tOrderAssignments on tOrderAssignments.OrderUID = tOrderQueues.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.')  as FundedAvg'.$Queue->QueueUID.$value->UserUID);
					$this->db->select('(SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderQueues left join tOrders on tOrders.OrderUID = tOrderQueues.OrderUID left join tOrderAssignments on tOrderAssignments.OrderUID = tOrderQueues.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.')  as FundedOrderCount'.$Queue->QueueUID.$value->UserUID);
				}
			}
				$resultdata[] = $this->db->get()->row();

		}
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


	//get MTDCycleTime
	function getMTDCycleTime($first_date)
	{
		$where = ' AND DATE(tOrderAssignments.CompleteDateTime) BETWEEN "'.$first_date.'" and "'.date('Y-m-d').'"';
		$this->CycleTimeQuery($where);
		return $this->db->get()->row();
	}

	function CycleTimeQuery($where)
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$this->db->query('SET SESSION group_concat_max_len = 1000000;');
		// total loans
		// $this->db->select(' (SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.$where.') as TotalOrderCount ');
		$this->db->select('(SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.$where.')  as TotalOrderUID');
		// $this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime)) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.$where.') as TotalAvg');

		// Pending loand
		$this->db->select('(SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus != 5'.$where.') as PendingOrderCount ');
		$this->db->select('(SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus != 5'.$where.')  as PendingOrderUID');
		$this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,'.date('Y-m-d').')) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.'  AND tOrderAssignments.WorkflowStatus != 5'.$where.') as PendingAvg');

		// funded loans
		$this->db->select('(SELECT COUNT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.') as FundedOrderCount ');
		$this->db->select('(SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.')  as FundedOrderUID');
		$this->db->select('(SELECT SUM(TIMESTAMPDIFF(DAY,tOrderAssignments.AssignedDatetime,tOrderAssignments.CompleteDateTime)) FROM tOrderAssignments join tOrders on tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrders.CustomerUID = '.$CustomerUID.' AND tOrderAssignments.WorkflowStatus = 5'.$where.') as FundedAvg');
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

	function get_CycleReportList($post)
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
		$Modules = $this->Common_Model->GetCustomerBasedModules();
		$this->db->select("tOrders.OrderUID,tOrders.LoanNumber");
		$this->db->from('tOrders');
		foreach ($Modules as $workflow) {

			$this->db->select("timestampdiff(DAY, TOA_".$workflow->SystemName.".AssignedDatetime, TOA_".$workflow->SystemName.".CompleteDateTime) AS ".$workflow->SystemName,FALSE);

			$this->db->join('tOrderAssignments AS TOA_'.$workflow->SystemName,'TOA_'.$workflow->SystemName.'.OrderUID = tOrders.OrderUID AND TOA_'.$workflow->SystemName.'.WorkflowModuleUID = ' . $workflow->WorkflowModuleUID,'LEFT');
		}
		$this->db->where('tOrders.OrderUID in ('.$post['OrderUID'].')');
		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
		$this->db->group_by('tOrders.OrderUID');
	}

}
?>
