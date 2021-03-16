<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProcessInflowReport_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}
	function getProcessUsers($UserUID)
	{
		$this->db->select('mUsers.UserName,mUsers.UserUID, mUsers.LoginID');
		$this->db->from('mUsers');
		$this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);//Added BY harini to get the users based on client 
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

	function getProcessInflowReportCounts($post)
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

		//get completed order 
		$data = [];
		$this->db->select("tOrders.OrderUID");
		$this->Common_Model->GetCompletedQueueOrders();
		$Completedorders = $this->db->get()->result();
		foreach ($Completedorders as $key => $value) 
		{
			$data[] = $value->OrderUID;
		}
		$Completedorders = implode(",",$data);

		//date wise loop
		$resultdata = [];
		foreach ($datearray as $key => $date)
		{
			if($Status == 'Pending')
			{
				$CountWhere = " AND DATE(tOrders.OrderEntryDateTime) = '".$date."' AND tOrders.CustomerUID =".$CustomerUID;
				if(!empty($Completedorders))
				{
					$CountWhere .=" AND tOrders.OrderUID NOT IN (".$Completedorders.")";
				}
			}
			else if($Status == 'Completed')
			{
				$CountWhere = " AND DATE(tOrders.OrderEntryDateTime) = '".$date."' AND tOrders.CustomerUID =".$CustomerUID;
				if(!empty($Completedorders))
				{
					$CountWhere .=" AND tOrders.OrderUID IN (".$Completedorders.")";
				}
			}
			else
			{
				$CountWhere = " AND DATE(tOrders.OrderEntryDateTime) = '".$date."' AND tOrders.CustomerUID =".$CustomerUID;
			}
			$this->db->select("('".date('d-M',strtotime($date))."') as date");
			foreach ($ProcessUsers as $value) 
			{
				//select fha count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('FHA')
					".$CountWhere."
					) as FHA".$value->UserUID);

				//select fha orderuid
				$this->db->select("(SELECT GROUP_CONCAT('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('FHA')
					".$CountWhere."
					) as FHAOrderUID".$value->UserUID);

				//select va count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA')
					".$CountWhere."
					) as VA".$value->UserUID);

				//select va orderuid
					$this->db->select("(SELECT GROUP_CONCAT('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA')
					".$CountWhere."
					) as VAOrderUID".$value->UserUID);

				//select fhs/va count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA','FHA')
					".$CountWhere."
				) as total".$value->UserUID);
			

			//select fhs/va orderuid
				$this->db->select("(SELECT GROUP_CONCAT('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA','FHA')
					".$CountWhere."
					) as totalOrderUID".$value->UserUID);
			}
			$resultdata[] = $this->db->get()->result();
		}
			
			foreach ($ProcessUsers as $value) 
			{

				if($Status == 'Pending')
				{
					$where = " AND DATE(tOrders.OrderEntryDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."' AND tOrders.CustomerUID =".$CustomerUID;
					if(!empty($Completedorders))
					{
						$where .= " AND tOrders.OrderUID NOT IN (".$Completedorders.")";
					}
				}
				else if($Status == 'Completed')
				{
					$where = "AND DATE(tOrders.OrderEntryDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."' AND tOrders.CustomerUID =".$CustomerUID;
					if(!empty($Completedorders))
					{
						$where .= " AND tOrders.OrderUID IN (".$Completedorders.")";
					}
				}
				else
				{
					$where = " AND DATE(tOrders.OrderEntryDateTime) BETWEEN '".$datearray[0]."'
					AND '".end($datearray)."' AND tOrders.CustomerUID =".$CustomerUID;
				}
				$this->db->select("('Total') as date");

				//fha total count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('FHA')
					 ".$where."
					) as FHA".$value->UserUID);

				//fha total orderuid
				$this->db->select("(SELECT GROUP_CONCAT('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('FHA')
					 ".$where."
					) as FHAOrderUID".$value->UserUID);

					//va total count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA')
					 ".$where."
					) as VA".$value->UserUID);

				//fha total orderuid
				$this->db->select("(SELECT GROUP_CONCAT('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA')
					 ".$where."
					) as VAOrderUID".$value->UserUID);

				//fha va total count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA','FHA')
					 ".$where."
				) as total".$value->UserUID);

				//fha total orderuid
				$this->db->select("(SELECT GROUP_CONCAT('',tOrders.OrderUID)
					FROM
					tOrders
					LEFT JOIN tOrderImport ON tOrderImport.OrderUID = tOrders.OrderUID
					WHERE
					(
					(tOrderImport.LoanProcessor LIKE '%".$value->UserName."%')
					OR (tOrders.OrderEntryByUserUID = ".$value->UserUID.")
					)
					AND tOrders.LoanType IN ('VA','FHA')
					 ".$where."
					) as totalOrderUID".$value->UserUID);
			}
			$resultdata[] = $this->db->get()->result();

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

}
?>
