<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class InflowReport_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	function getInflowReportCounts($post)
	{
		$CustomerUID = $this->parameters['DefaultClientUID'];
		//date report filter
		$Status = $post['Status'];
		$FromDate = $post['FromDate'];
		$ToDate = $post['ToDate'];
		$datearray = $this->getDates($FromDate, $ToDate);
		$TOrderImport_join="";
		$ReportDateSelection=$this->getCustomerReportDateSelection();
		if($ReportDateSelection == 2)
		{
			$TOrderImport_join.=" LEFT JOIN torderimport ON torderimport.OrderUID=tOrders.OrderUID";
			$WorkflowStatus="";
		}
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
		if($Status == 'Pending')
				{
					if($ReportDateSelection == 2)
					{
						$where = " AND STR_TO_DATE(torderimport.InflowDate,'%m/%d/%Y') BETWEEN '".$datearray[0]."' AND '".end($datearray)."' AND tOrders.CustomerUID =".$CustomerUID;
					}
					else
					{
					
						$where = " AND DATE(tOrders.OrderEntryDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."' AND tOrders.CustomerUID =".$CustomerUID;
					}
					if(!empty($Completedorders))
					{
						$where .= " AND tOrders.OrderUID NOT IN (".$Completedorders.")";
					}
				}
				else if($Status == 'Completed')
				{
					if($ReportDateSelection == 2)
					{
						$where = "AND STR_TO_DATE(torderimport.InflowDate,'%m/%d/%Y') BETWEEN '".$datearray[0]."' AND '".end($datearray)."'";
					}
					else
					{
					$where = "AND DATE(tOrders.OrderEntryDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."'";
					}
					if(!empty($Completedorders))
					{
						$where .= " AND tOrders.OrderUID IN (".$Completedorders.")";
					}
				}
				else
				{
					if($ReportDateSelection == 2)
					{
						$where = " AND STR_TO_DATE(torderimport.InflowDate,'%m/%d/%Y') BETWEEN '".$datearray[0]."'
					AND '".end($datearray)."' AND tOrders.CustomerUID =".$CustomerUID;
					}
					else
					{
						$where = " AND DATE(tOrders.OrderEntryDateTime) BETWEEN '".$datearray[0]."'
					AND '".end($datearray)."' AND tOrders.CustomerUID =".$CustomerUID;
					}
				}
				$this->db->select("('Total') as date");
				
				//fha total count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('FHA')
					 ".$where."
					) as FHA");

				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('FHA')
					 ".$where."
					) as FHAOrderUID");

					//va total count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('VA')
					 ".$where."
					) as VA");

				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('VA')
					 ".$where."
					) as VAOrderUID");

				//fha va total count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('VA','FHA')
					 ".$where."
				) as total");

				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('VA','FHA')
					 ".$where."
				) as totalOrderUID");
			
			$resultdata[] = $this->db->get()->result();
		foreach ($datearray as $key => $date)
		{
			
			if($ReportDateSelection == 2)
			{
				$InflowDate = date("m/d/yy", strtotime($date));
			}
			if($Status == 'Pending')
			{
				if($ReportDateSelection == 2)
				{
					$CountWhere = "AND torderimport.InflowDate = '".$InflowDate."' AND tOrders.CustomerUID =".$CustomerUID;
				}
				else
				{
					$CountWhere = " AND DATE(tOrders.OrderEntryDateTime) = '".$date."' AND tOrders.CustomerUID =".$CustomerUID;
				}
				if(!empty($Completedorders))
				{
					$CountWhere .=" AND tOrders.OrderUID NOT IN (".$Completedorders.")";
				}
			}
			else if($Status == 'Completed')
			{
				if($ReportDateSelection == 2)
				{
					$CountWhere = " AND torderimport.InflowDate = '".$InflowDate."' ";
				}
				else
				{
					$CountWhere = " AND DATE(tOrders.OrderEntryDateTime) = '".$date."' ";
				}
				if(!empty($Completedorders))
				{
					$CountWhere .=" AND tOrders.OrderUID IN (".$Completedorders.")";
				}
			}
			else
			{
				if($ReportDateSelection == 2)
				{
					$CountWhere = " AND torderimport.InflowDate = '".$InflowDate."' AND tOrders.CustomerUID =".$CustomerUID;
				}
				else
				{
					$CountWhere = " AND DATE(tOrders.OrderEntryDateTime) = '".$date."' AND tOrders.CustomerUID =".$CustomerUID;
				}
			}
			$this->db->select("('".date('d-M',strtotime($date))."') as date");

				//select fha count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('FHA')
					".$CountWhere."
					) as FHA");

				//select fha count
				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('FHA')
					".$CountWhere."
					) as FHAOrderUID");

				//select va count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					 tOrders.LoanType IN ('VA')
					".$CountWhere."
					) as VA");

				//select va count
				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					 tOrders.LoanType IN ('VA')
					".$CountWhere."
					) as VAOrderUID");

				//select fhs/va count
				$this->db->select("(SELECT count( DISTINCT
					tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('VA','FHA')
					".$CountWhere."
				) as total");


				$this->db->select("(SELECT group_concat('',tOrders.OrderUID)
					FROM
					tOrders ".$TOrderImport_join."
					WHERE
					tOrders.LoanType IN ('VA','FHA')
					".$CountWhere."
				) as totalOrderUID");

			$resultdata[] = $this->db->get()->result();
		}
		
				
			/*print_r($this->db->last_query());	
		exit;*/
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

	function getCustomerMTDGoals()
	{
		$CustomerUID = $this->parameters['DefaultClientUID'];
		return $this->db->select('MTDGoals')->from('mCustomer')->where('CustomerUID',$CustomerUID)->get()->row()->MTDGoals;
	}
	function getCustomerReportDateSelection()
	{
		$CustomerUID = $this->parameters['DefaultClientUID'];
		return $this->db->select('ReportDateSelection')->from('mCustomer')->where('CustomerUID',$CustomerUID)->get()->row()->ReportDateSelection;
	}

}
?>
