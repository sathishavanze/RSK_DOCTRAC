<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends MY_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->model('Dashboard_Model');
		$this->load->model('Dash_Model');
	}
	
	function index()
	{
		$data['content'] = 'dashboard';		
		$data['WorkFlow'] = $this->Common_Model->get_customerroleworkflows();
		$data['ProcessUsers'] = $this->Dashboard_Model->getProcessUsers(array());
		//$data['workflow'] = $this->Common_Model->GetWorkflowDetaiils();	
		$users=$this->Common_Model->get('mUsers', NULL, ['UserName'=>'DESC'], ['RoleUID']);
		//$users=$this->Common_Model->get_row('mUsers', ['UserUID'=>1], ['UserName'=>'DESC'], ['RoleUID']);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


	/**
	*Function fetch queue progress date wise counts 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 22 October 2020.
	*/
	function queueProgress_periodcounts()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$daysCount=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		$data['ReportCounts'] = $this->Dashboard_Model->queue_progress_periodwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate,$daysCount);

		$res = $this->load->view('queueprogress/queueperiodview',$data,true);

		echo json_encode(array('Result' => $res));

	}


	/**
	*Function fetch queue progress period wise counts 
	*@author Mansoor Ali <mansoor.ali@avanzegroup.com>
	*@since Wednesday 02 December 2020.
	*/
	function queueProgress_periodcount_new()
	{

		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$daysCount=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		$data['ReportCounts'] = $this->Dashboard_Model->queue_progress_new_count($WorkFlowUID,$newadv_fromDate,$newadv_toDate,$daysCount);

		$res = $this->load->view('queueprogress/queueperiodview',$data,true);

		echo json_encode(array('Result' => $res));


	}



	/**
	*Function fetch queue progress date wise counts for Chart 
	*@author Mansoor Ali <mansoor.ali@avanzegroup.com>
	*@since Tuesday 03 November 2020.
	*/
	//queueProgress_periodcounts_Chart

	function queueProgress_periodcounts_Chart()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$daysCount=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		$data = $this->Dashboard_Model->queue_progress_periodwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate,$daysCount);


	$ChartData1['inflow'][]="inflow";
	$ChartData1['pending'][]="pending";
	$ChartData1['complete'][]="complete";
	$ChartData1['ratio'][]="ratio";

	foreach ($data as $row)
	{

		$ChartData1['Date'][] = $row['Date'];
        $ChartData['InflowCount'] = ($row['InflowOrders'] != '') ? count(explode(',', $row['InflowOrders'])) : NULL;
        $ChartData['PendingCount'] = ($row['PendingOrders'] != '') ? count(explode(',', $row['PendingOrders'])) : NULL;
        $ChartData['CompleteCount'] = ($row['CompletedOrders'] != '') ? count(explode(',', $row['CompletedOrders'])) : NULL;
	    
	    $ratio = $ChartData['CompleteCount'] / $ChartData['InflowCount'] * 100;

	        if (is_infinite($ratio))
	        {
	        	$ratio = 0;
	        }

			$ratio = sprintf('%0.2f', $ratio); 


        if (!empty($ChartData['InflowCount']))
        {
        	array_push($ChartData1['inflow'], count(explode(',', $row['InflowOrders'])));
        }
        else
        {
			array_push($ChartData1['inflow'], 0);

			//$ChartData1['inflow'][] = 0;        	
        }


        if (!empty($ChartData['PendingCount']))
        {
        	array_push($ChartData1['pending'], count(explode(',', $row['PendingOrders'])));
        }
        else
        {
			array_push($ChartData1['pending'], 0);
        }

        if (!empty($ChartData['CompleteCount']))
        {
        	array_push($ChartData1['complete'], count(explode(',', $row['CompletedOrders'])));
        }
        else
        {
			array_push($ChartData1['complete'], 0);
        }

        if (!empty($ratio))
        {
        	array_push($ChartData1['ratio'], $ratio);
        }
        else
        {
			array_push($ChartData1['ratio'], 0);
        }
	}

	echo json_encode($ChartData1);

	}


	/**
	*Function fetch queue progress individual counts 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 22 October 2020.
	*/
	function queueProgress_individualcounts()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->queue_progress_individualwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$res = $this->load->view('queueprogress/individualview',$data,true);

		echo json_encode(array('Result' => $res));

	}


	/**
	*Function fetch queue progress individual counts for Generation Chart 
	*@author Mansoor Ali <praveen.kumar@avanzegroup.com>
	*@since Wednesday 04 November 2020.
	*/

	//queueProgress_individualcounts_Chart
	function queueProgress_individualcounts_Chart()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data = $this->Dashboard_Model->queue_progress_individualwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$ChartData1['inflow'][]="inflow";
		$ChartData1['pending'][]="pending";
		$ChartData1['complete'][]="complete";
		$ChartData1['ratio'][]="ratio";

		foreach ($data as $row)
		{

			$ChartData1['UserName'][] = $row['UserName'];
	        $ChartData['InflowCount'] = ($row['InflowOrders'] != '') ? count(explode(',', $row['InflowOrders'])) : NULL;
	        $ChartData['PendingCount'] = ($row['PendingOrders'] != '') ? count(explode(',', $row['PendingOrders'])) : NULL;
	        $ChartData['CompleteCount'] = ($row['CompletedOrders'] != '') ? count(explode(',', $row['CompletedOrders'])) : NULL;
	   
	    	$ratio = $ChartData['CompleteCount'] / $ChartData['InflowCount'] * 100;

	        if (is_infinite($ratio))
	        {
	        	$ratio = 0;
	        }

			$ratio = sprintf('%0.2f', $ratio); 


	        if (!empty($ChartData['InflowCount']))
	        {
	        	array_push($ChartData1['inflow'], count(explode(',', $row['InflowOrders'])));
	        }
	        else
	        {
				array_push($ChartData1['inflow'], 0);

				//$ChartData1['inflow'][] = 0;        	
	        }


	        if (!empty($ChartData['PendingCount']))
	        {
	        	array_push($ChartData1['pending'], count(explode(',', $row['PendingOrders'])));
	        }
	        else
	        {
				array_push($ChartData1['pending'], 0);
	        }

	        if (!empty($ChartData['CompleteCount']))
	        {
	        	array_push($ChartData1['complete'], count(explode(',', $row['CompletedOrders'])));
	        }
	        else
	        {
				array_push($ChartData1['complete'], 0);
	        }

	        if (!empty($ratio))
	        {
	        	array_push($ChartData1['ratio'], $ratio);
	        }
	        else
	        {
				array_push($ChartData1['ratio'], 0);
	        }
		}

	echo json_encode($ChartData1);

	}


	/**
	* Function for getting date
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 19-10-2020
	**/
	function getFromToDate()
	{
		$period = $this->input->post('period');

		$data = array();
		switch ($period) 
		{
			case 'CYear':
			$data['fromDate'] = date("01/01/Y");
			$data['toDate'] = date("m/d/Y",strtotime(date('Y-12-31')));
			break;

			case 'CMonth':
			$data['fromDate'] = date("m/01/Y");
			$data['toDate'] = date("m/d/Y",strtotime(date('Y-m-d')));
			break;

			case 'LYear':
			$data['fromDate'] = date("m/d/Y",strtotime("-12 months"));
			$data['toDate'] = date("m/d/Y");
			break;

			case 'LMonth': 
			$data['fromDate'] = date("m/d/Y",strtotime("-29 days"));
			$data['toDate'] = date("m/d/Y");
			break;

			case '3Month':
			$data['fromDate'] = date("m/d/Y",strtotime("-3 months"));
			$data['toDate'] = date("m/d/Y");
			break;

			case '6Month':
			$data['fromDate'] = date("m/d/Y",strtotime("-6 months"));
			$data['toDate'] = date("m/d/Y");
			break;

			case '7days':
			$data['fromDate'] = date("m/d/Y",strtotime("7 days ago"));
			$data['toDate'] = date("m/d/Y");
			break;

			case 'week':
			$data['fromDate'] = date("m/d/Y",strtotime("7 days ago"));
			$data['toDate'] = date("m/d/Y");
			break;

			case 'month':
			$data['fromDate'] = date("m/01/Y");
			$data['toDate'] = date("m/d/Y",strtotime(date('Y-m-d')));
			break;

			case 'today':
			$data['fromDate'] = date("m/d/Y");
			$data['toDate'] = date("m/d/Y");
			break;

		} 
		echo json_encode($data);
	}
	/**
	* Function for get days count between two date
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 19-10-2020
	**/ 
	function dateDiffInDays($date1, $date2)  
	{ 
		if($date1!=$date2){
			// Calculating the difference in timestamps 
			$diff = strtotime($date2) - strtotime($date1); 

			// 1 day = 24 hours , 24 * 60 * 60 = 86400 seconds 
			return abs(round($diff / 86400));
		}else{
			return 1;
		}
		 
	} 

	/**
	* Function for getting inflow records
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 19-10-2020
	**/
	function getProductivityTable()
	{
		$post = $this->input->post();
		//$data['ProcessUsers'] = $this->dashboard_new_Model->getProcessUsers($post['Process']);
		$data['Target'] = $post['Target'];
		$data['Type'] = $post['Type'];
		//$data['ProductivityReportCounts'] = $this->Dashboard_Model->getProductivityReportCounts($post);
		$fdate = new DateTime($post['FromDate']);
		$newadv_fromDate = $fdate->format('Y-m-d');
		//print_r($data['ProductivityReportCounts']);exit;
		$edate = new DateTime($post['ToDate']);
		$newadv_toDate = $edate->format('Y-m-d');
		$data['ReportCounts'] = $this->Dashboard_Model->getProductivityPeriodicReport($post['WorkflowModuleUID'],$newadv_fromDate,$newadv_toDate);
		//echo '<pre>';print_r($data['ReportCounts']);exit;
		echo json_encode($this->load->view('productivity/tableinflow',$data,'true'));
	}

	/**
	* Function for getting inflow records by individual
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 19-10-2020
	**/
	function GetUsersByWorkflow()
	{
		ini_set('display_errors', 1);error_reporting(E_ALL);
		$WorkflowModule = $this->input->post('WorkflowModuleUID');	
		$data['WorkflowModule'] = $this->input->post('WorkflowModuleUID');	
		//$data['userslist']=$this->Dashboard_Model->GetUsersByWorkflow($WorkflowModule);
		//$data['GetcompletedOrder']=$this->Dashboard_Model->GetcompletedOrder($this->input->post());
		$data['Target']=$this->input->post('Target');

		$fdate = new DateTime($this->input->post('FromDate'));
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($this->input->post('ToDate'));
		$newadv_toDate = $edate->format('Y-m-d');

		$data['daycount']=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 
		$data['ReportCounts'] = $this->Dashboard_Model->getProductivityIndividualReport($WorkflowModule,$newadv_fromDate,$newadv_toDate);
		//echo '<pre>';print_r($data['ReportCounts']);exit;
		echo json_encode($this->load->view('productivity/getuserbyworkflow', $data,true));

	}

	/**
	* Function for getting inflow records by individual
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 19-10-2020
	**/
	function UpdateTarget()
	{
		$data = $this->input->post();	
		$dataUpdate=$this->Dashboard_Model->UpdateTarget($data);
		return $dataUpdate;
	}


	//agingReport_queuecounts
	function agingReport_queuecounts()
	{
		ini_set('display_errors', 1);error_reporting(E_ALL);
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->aging_report_queuewise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$res = $this->load->view('aged/queueaged',$data,true);

		echo json_encode(array('Result' => $res));

	}

	//agingReport_subqueuecounts
	function agingReport_subqueuecounts()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->aging_report_subqueuewise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$res = $this->load->view('aged/subqueueaged',$data,true);

		echo json_encode(array('Result' => $res));

	}

	/**
	* Function for getting aging records by individual
	* @author MANSOOR ALI.S<mansoor.ali@avanzegroup.com>
	* @since 27-10-2020
	**/

	//agingReport_individualcounts
	function agingReport_individualcounts()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');


		$data['ReportCounts'] = $this->Dashboard_Model->aging_report_individualwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$res = $this->load->view('aged/individualaged',$data,true);

		echo json_encode(array('Result' => $res));

	}


	/**
	* Function for getting aging records by individual chart Generation 
	* @author MANSOOR ALI<mansoor.ali@avanzegroup.com>
	* @since 05-November-2020
	**/

	//aging_individual_Chart
	function aging_individual_Chart()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data = $this->Dashboard_Model->aging_report_individualwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

		$finaltotalsum = [];
		$finalnoofvalues = [];
		foreach ($data as $row) 
		{ 
			$totalsum = 0;
			$noofvalues = 0;

				foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { 

					$row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; 

					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;


				} 

				$ChartData2[]=$row;
		}

		echo json_encode($ChartData2);

	}


	/**
	* Function for getting aging records by Periodic chart Generation 
	* @author MANSOOR ALI<mansoor.ali@avanzegroup.com>
	* @since 06-November-2020
	**/

	//aging_Periodic_Chart
	function aging_Periodic_Chart()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$daysCount=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		$data = $this->Dashboard_Model->aging_report_periodwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate,$daysCount);
		
		$DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

		$finaltotalsum = [];
		$finalnoofvalues = [];
		foreach ($data as $row) 
		{ 
			$totalsum = 0;
			$noofvalues = 0;

				foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { 

					$row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; 

					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;

				} 

				$ChartData2[]=$row;
		}
		echo json_encode($ChartData2);

	}


	/**
	* Function for getting aging records by Category-wise chart Generation 
	* @author MANSOOR ALI<mansoor.ali@avanzegroup.com>
	* @since 06-November-2020
	**/

	//aging_Category_Chart
	function aging_Category_Chart()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data = $this->Dashboard_Model->aging_report_categorywise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);
	
		$DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

		$finaltotalsum = [];
		$finalnoofvalues = [];
		foreach ($data as $row) 
		{ 
			$totalsum = 0;
			$noofvalues = 0;

				foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { 

					$row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; 

					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;

				} 

				$ChartData2[]=$row;
		}

		echo json_encode($ChartData2);

	}


	/**
	* Function for getting aging records by Queue-wise chart Generation 
	* @author MANSOOR ALI<mansoor.ali@avanzegroup.com>
	* @since 06-November-2020
	**/

	//aging_Queue_Chart
	function aging_Queue_Chart()
	{

		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');


		$data = $this->Dashboard_Model->aging_report_queuewise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);
		
		$DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

		$finaltotalsum = [];
		$finalnoofvalues = [];
		foreach ($data as $row) 
		{ 
			$totalsum = 0;
			$noofvalues = 0;

				foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { 

					$row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; 

					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;

				} 

				$ChartData2[]=$row;
		}

		echo json_encode($ChartData2);

	}

	/**
	* Function for getting aging records by SubQueue-wise chart Generation 
	* @author MANSOOR ALI<mansoor.ali@avanzegroup.com>
	* @since 07-November-2020
	**/

	//aging_SubQueue_Chart
	function aging_SubQueue_Chart()
	{

		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data = $this->Dashboard_Model->aging_report_subqueuewise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

		$finaltotalsum = [];
		$finalnoofvalues = [];
		foreach ($data as $row) 
		{ 
			$totalsum = 0;
			$noofvalues = 0;

				foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { 

					$row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; 

					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;

				} 

				$ChartData2[]=$row;
		}

		echo json_encode($ChartData2);

	}


	/**
	* Function for getting aging records by Periodic or Team-wise
	* @author MANSOOR ALI.S<mansoor.ali@avanzegroup.com>
	* @since 27-10-2020
	**/

	//agingReport_individualcounts
	function agingReport_periodcounts()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->aging_report_individualwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$res = $this->load->view('aged/agingtableindview',$data,true);

		echo json_encode(array('Result' => $res));

	}

	/**
	* Function for get Aging records by periodic
	* @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
	* @since 27-10-2020
	**/

	//agingReport_periodicResult
	function agingReport_periodicResult()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$daysCount=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		$data['ReportCounts'] = $this->Dashboard_Model->aging_report_periodwise($WorkFlowUID,$newadv_fromDate,$newadv_toDate,$daysCount);

		$res = $this->load->view('aged/periodaged',$data,true);

		echo json_encode(array('Result' => $res));

	}


	/**
	* Function for get Aging records by Category-wise
	* @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
	* @since 28-10-2020
	**/

	//agingReport_CategoryWise
	function agingReport_categoryWiseResult()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->aging_report_categorywise($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$res = $this->load->view('aged/categoryaged',$data,true);

		echo json_encode(array('Result' => $res));

	}


	/**
	* Function for GateKeeping Pipeline record Count by Review, Complete and Pendings
	* @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
	* @since 29-10-2020
	**/

	//Pipeline_reviewCountResult
	function Pipeline_reviewCountResult()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->Pipeline_ReviewCountReport($WorkFlowUID,$newadv_fromDate,$newadv_toDate);
		//print_r($data['ReportCounts']);exit;
		$res = $this->load->view('pipeline/pipeline_countresult',$data,true);

		echo json_encode(array('Result' => $res));

	}


	/**
	* Function for GateKeeping Pipeline Inflow SLA Report
	* @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
	* @since 29-10-2020
	**/

	//Pipeline_reviewCountResult
	function Pipeline_InflowSLAReport()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->Pipeline_InflowSLAResult($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$res = $this->load->view('pipeline/pipelineinflowsla',$data,true);

		echo json_encode(array('Result' => $res));

	}


	/**
	* Function for GateKeeping Pipeline Aging Files 
	* @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
	* @since 30-10-2020
	**/

	//Pipeline_AgingFileRepo
	function Pipeline_AgingFileRepo()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$data['ReportCounts'] = $this->Dashboard_Model->pipeline_agingFileResult($WorkFlowUID,$newadv_fromDate,$newadv_toDate);
		//print_r($data['ReportCounts']);exit;
		$res = $this->load->view('pipeline/pipelineagedfile',$data,true);

		echo json_encode(array('Result' => $res));

	}

	/**
	* Function for TAT Aging Report 
	* @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
	* @since 02-11-2020
	**/

	//TAT_AgingRepo
    function TAT_AgingRepo()
    {
        
        $WorkFlowUID = $this->input->post("WorkFlows");
        $adv_fromDate = $this->input->post("adv_fromDate");
        $adv_toDate = $this->input->post("adv_toDate");

        $fdate = new DateTime($adv_fromDate);
        $newadv_fromDate = $fdate->format('Y-m-d');
 
        $edate = new DateTime($adv_toDate);
        $newadv_toDate = $edate->format('Y-m-d');

        $data['ReportCounts'] = $this->Dashboard_Model->TAT_agingResult($WorkFlowUID,$newadv_fromDate,$newadv_toDate); 

        $res = $this->load->view('tat/tat_aging',$data,true);
        echo json_encode(array('Result' => $res));

    }

	/**
	* Function for TAT Aging Chart Generation 
	* @author Mansoor Ali <mansoor.ali@avanzegroup.com>
	* @since 11-10-2020
	**/

//TAT_Aging_Chart
  function TAT_Aging_Chart()
    {
        
        $WorkFlowUID = $this->input->post("WorkFlows");
        $adv_fromDate = $this->input->post("adv_fromDate");
        $adv_toDate = $this->input->post("adv_toDate");

        $fdate = new DateTime($adv_fromDate);
        $newadv_fromDate = $fdate->format('Y-m-d');
 
        $edate = new DateTime($adv_toDate);
        $newadv_toDate = $edate->format('Y-m-d');


        $data = $this->Dashboard_Model->TAT_agingResult($WorkFlowUID,$newadv_fromDate,$newadv_toDate); 

		$DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

		$finaltotalsum = [];
		$finalnoofvalues = [];
		foreach ($data as $row) 
		{ 
			$totalsum = 0;
			$noofvalues = 0;

				foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { 

					$row[$AgingHeaderkey.'count'] = ($row[$AgingHeaderkey] != '') ? count(explode(',', $row[$AgingHeaderkey])) : NULL; 

					$sum = isset($row[$AgingHeaderkey.'count']) ? $row[$AgingHeaderkey.'count'] : 0; 
					$totalsum = $sum + $totalsum; 
					$noofvalues = $noofvalues + 1;
				}
				$ChartData3[]=$row;
		}
		foreach ($ChartData3 as $row) 
		{
			$ChartData4['label'][] = $row['CategoryName'];
			$fiveten = !empty($row['fivetotendayscount']) ? $row['fivetotendayscount'] : 0;
			$tenfif = !empty($row['tentofifteendayscount']) ? $row['tentofifteendayscount'] : 0;
			$fiftwen = !empty($row['fifteentotwentydayscount']) ? $row['fifteentotwentydayscount'] : 0;
			$twenthirty = !empty($row['twentyfivetothirtydayscount']) ? $row['twentyfivetothirtydayscount'] : 0;
			//$ChartData4['series'][] =  $fiveten . "," . $tenfif . "," . $fiftwen . "," . $twenthirty;
			$ChartData4['fivetotendays'][] =  $fiveten;
			$ChartData4['tentofifteendays'][] =  $tenfif;
			$ChartData4['fifteentotwentydays'][] =  $fiftwen;
			$ChartData4['twentyfivetothirty'][] =  $twenthirty;
		}
		echo json_encode($ChartData4);

    }



	/**
	* Function for get inflow records by periodic
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 19-10-2020
	**/
	function getInflowProcessTable()
	{
		$post = $this->input->post();
		$data['inflowType'] = $this->input->post('Type');
		// $processinflowreport=$this->Dashboard_Model->processinflowreport($post);
		// echo '<pre>';print_r($processinflowreport);exit;
		//$data['ProcessUsers'] = $this->Dashboard_Model->getProcessUsers($post['Process']);
		if($data['inflowType']=='Individual'){
			$data['ProcessInflowReportCounts'] = $this->Dashboard_Model->getProcessInflowIndividualReportCounts($post);
		}else{
			$data['ProcessInflowReportCounts'] = $this->Dashboard_Model->getProcessInflowPeriodicReportCounts($post);
		}	
		echo json_encode($this->load->view('tableprocessinginflow',$data,'true'));
	}

	/**
	* Function for get FollowUp records by periodic
	* @author Sathishkumar R<sathish.kumar@avanzegroup.com>
	* @since Monday 09 November 2020
	**/
	function getFollowUpProcessTable()
	{
		$post = $this->input->post();
		$data['FollowUpType'] = $this->input->post('Type');
		// $processFollowUpreport=$this->Dashboard_Model->processFollowUpreport($post);
		// echo '<pre>';print_r($processFollowUpreport);exit;
		//$data['ProcessUsers'] = $this->Dashboard_Model->getProcessUsers($post['Process']);
		if($data['FollowUpType']=='Individual'){
			$data['ProcessFollowUpReportCounts'] = $this->Dashboard_Model->getProcessFollowUpIndividualReportCounts($post);
		}else{
			$data['ProcessFollowUpReportCounts'] = $this->Dashboard_Model->getProcessFollowUpPeriodicReportCounts($post);
		}	
		
		echo json_encode($this->load->view('tableprocessingFollowUp',$data,'true'));
	}

	/**
  * Function for get Targer
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 21-10-2020
  **/
	function getTarget(){
		$result=$this->Dashboard_Model->getTarget($this->input->post('WorkflowModuleUID'));
		if(!empty($result->Target)){
			echo ($result->Target);
		}else{
			echo (10);
		}
	}
	/**
	*Function fetch loans based on orderuid
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 22 October 2020.
	*/
	function fetchorders() 
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		//Advanced Search
		//get_post_input_data
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['OrderUID'] = $this->input->post('OrderUID');
		$post['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
		$post['Section'] = 'Dashboard';

		//column order
		$QueueColumns = $this->Common_Model->getSectionQueuesColumns($post['Section']);
		if (!empty($QueueColumns)) 
		{
			$columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns);
			$post['column_order'] = $columndetails;
			$post['column_search'] = array_filter($columndetails);
			$post['IsDynamicColumns'] = true;
			$post['IsDynamicColumns_Select'] = $columndetails;
		}

		$post['column_search'] = $post['column_order'];

		$list = $this->Dashboard_Model->getOrders($post);

		$no = $post['start'];
		$returnlist = [];

		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] = "Ordersummary/index/";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous[$post['Section']] = TRUE;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $WorkflowModuleUID, $Mischallenous);
		if (!empty($DynamicColumns)) 
		{
			$returnlist = $DynamicColumns['orderslist'];
		}

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Dashboard_Model->count_all($post),
			"recordsFiltered" =>  $this->Dashboard_Model->count_filtered($post),
			"data" => $returnlist,
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function WriteOrdersExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();
		$post = [];
		$post['OrderUID'] = $this->input->post('OrderUID');
		$post['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
		$filename = $this->input->post('filename');
		$filename = ($filename) ? str_replace(' ', '', $writer->sanitize_filename($filename)) : 'Sheet.xlsx';
		$post['Section'] = 'Dashboard';

		//column order
		$QueueColumns = $this->Common_Model->getSectionQueuesColumns($post['Section']);
		if (!empty($QueueColumns)) 
		{
			$columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
			$post['column_order'] = $columndetails;
			$post['column_search'] = array_filter($columndetails);
			$post['IsDynamicColumns'] = true;
			$post['IsDynamicColumns_Select'] = $columndetails;
		}

		$post['column_search'] = $post['column_order']  ='';

		$list = $this->Dashboard_Model->getOrders($post);


		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');


		$Mischallenous = array();
		$Mischallenous[$post['Section']] = TRUE;
		$Mischallenous['PageBaseLink'] = "";
		$Mischallenous['AssignButtonClass'] = "";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$excelcolumnsdata = $this->Common_Model->getGlobalExcelDynamicQueueColumns($list, $WorkflowModuleUID, $Mischallenous);
		if ( !empty($excelcolumnsdata) ) 
		{
			$header = $excelcolumnsdata['header'];
			$data = $excelcolumnsdata['orderslist'];		

			$HEADER = [];
			foreach ($header as $hkey => $head) {
				$HEADER[$head] = "string";
			}
			$writer->writeSheetHeader($filename,$HEADER, $header_style);
			foreach($data as $Order) {                     
				$writer->writeSheetRow($filename, $Order);
			}
		}
		ob_clean();
		$writer->writeToFile($filename);
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename= '.$filename);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Transfer-Encoding: binary');
		header('Set-Cookie: fileDownload=true; path=/');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		readfile($filename);
		unlink($filename);
		exit(0);
	}

	/**
	* Function for get quality report details
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 29-10-2020
	**/
	function GetQualityReport(){
		$WorkFlowUID = $this->input->post("WorkflowModuleUID");
		$data['Type'] = $this->input->post("Type");
		$adv_fromDate = $this->input->post("FromDate");
		$adv_toDate = $this->input->post("ToDate");

		$fdate = new DateTime($adv_fromDate);
		$FromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$ToDate = $edate->format('Y-m-d');

		if($this->input->post("Type")=='Individual'){
			$data['QualityReportIndividual']=$this->Dashboard_Model->getQualityIndividualReport($WorkFlowUID,$FromDate,$ToDate);
		}else{
			$data['QualityReportPeriodic']=$this->Dashboard_Model->getQualityPeriodicReport($WorkFlowUID,$FromDate,$ToDate);			
		}
		
		//print_r($data);exit;
		echo json_encode($this->load->view('tableqcreport',$data,'true'));
	}
	

	/**
	*Function Fetch Pending Workflow Count 
	*@author Vishnupriya.A <vishnupriya.a@avanzegroup.com>
	*@since 30-10-2020.
	*@return Pending Counts
	*/
	function widgetGetPendingOrdersCount() {
		$FilterCounts = [];
		// Gatekeeping Filter List
		$filterlist = $this->input->post('filterlist');
		$QueueUID = $this->input->post('QueueUID');
    	$post['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
    	$adv_fromDate = $this->input->post("FromDate");
		$adv_toDate = $this->input->post("ToDate");

		$fdate = new DateTime($adv_fromDate);
		$post['fromDate'] = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$post['Todate'] = $edate->format('Y-m-d');

		if ($filterlist == '#orderslist') {
			$FilterCounts['ModelName'] = 'GateKeeping_Orders_Model';
			$FilterCounts['FunctionName'] = 'count_all';
		} elseif ($filterlist == '#workinprogresslist') {
			$FilterCounts['ModelName'] = 'GateKeeping_Orders_Model';
			$FilterCounts['FunctionName'] = 'inprogress_count_all';
		} elseif ($filterlist == '#myorderslist') {
			$FilterCounts['ModelName'] = 'GateKeeping_Orders_Model';
			$FilterCounts['FunctionName'] = 'myorders_count_all';
		} elseif ($filterlist == '#parkingorderslist') {
			$FilterCounts['ModelName'] = 'GateKeeping_Orders_Model';
			$FilterCounts['FunctionName'] = 'parkingorders_count_all';
		} elseif ($filterlist == '#completedorderslist') {
			$FilterCounts['ModelName'] = 'Common_Model';
			$FilterCounts['FunctionName'] = 'completedordersBasedOnWorkflow_count_all';
		} elseif ($filterlist == '#ReWorkOrdersList') {
			$FilterCounts['ModelName'] = 'GateKeeping_Orders_Model';
			$FilterCounts['FunctionName'] = 'WorkUpReWorkGateKeepingOrders_CountAll';
		} elseif ($filterlist == '#ReWorkPendingOrdersList') {
			$FilterCounts['ModelName'] = 'GateKeeping_Orders_Model';
			$FilterCounts['FunctionName'] = 'ReWorkPendingOrders_CountAll';
		} elseif (!empty($QueueUID)) {
			$FilterCounts['ModelName'] = 'ExceptionQueue_Orders_model';
			$FilterCounts['FunctionName'] = '_getExeceptionQueueOrders';
			$post['QueueUID'] = $QueueUID;
		}

		if (!empty($FilterCounts)) {
			$FetchPendingCountWorkflows = $this->config->item('GatekeepingWidgetFetchPendingCount');
			$post['advancedsearch']['IsPendingOrders'] = 'true';
			$PendingWorklfowsCount = [];
			foreach ($FetchPendingCountWorkflows as $WorkflowModuleName => $WorkflowModuleUID) {
				$post['advancedsearch']['WorkflowModuleUID'] = $WorkflowModuleUID;
				$PendingWorklfowsCount[$WorkflowModuleName] = $this->Dashboard_Model->{$FilterCounts['FunctionName']}($post,'count_all');
			}
			$post = [];
			$post['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
    		$post['advancedsearch']['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
			$post['advancedsearch']['IsWidgetCompletedOrders'] = 'true';
			$post['QueueUID'] = $QueueUID;
			$PendingWorklfowsCount['CompletedOrdersCount'] = $this->Dashboard_Model->{$FilterCounts['FunctionName']}($post,'count_all');
		}
		$data['PendingWorklfowsCount']=$PendingWorklfowsCount;
		echo json_encode($this->load->view('tablependingcount',$data,'true'));
		//echo json_encode($PendingWorklfowsCount);	
		
	}


	/**
	* Function for get inflow records for chart
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 03-11-2020
	**/
	function getInflowProcessChart()
	{
		$post = $this->input->post();
		$data['inflowType'] = $this->input->post('Type');
		$FromDate = date('Y-m-d', strtotime($post['FromDate']));
    	$ToDate = date('Y-m-d', strtotime($post['ToDate']));

		$fdate = new DateTime($this->input->post('FromDate'));
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($this->input->post('ToDate'));
		$newadv_toDate = $edate->format('Y-m-d');

		$post['daysCount']=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		if($data['inflowType']=='Individual'){
			$dataInflow = $this->Dashboard_Model->getProcessInflowIndividualReportCounts($post);
				
			foreach($dataInflow as $keyInflow => $value){
				$ChartData['Label'][] = $value['UserName'];
				if(!empty($value['FHAOrders'])){
					$ChartData['FHA'][] = count(explode(',', $value['FHAOrders']));
				}else{
					$ChartData['FHA'][] = 0;
				}
				if(!empty($value['VAOrders'])){
					$ChartData['VA'][] = count(explode(',', $value['VAOrders']));
				}else{
					$ChartData['VA'][] = 0;
				}				
			}
		}else{
			$dataInflow = $this->Dashboard_Model->getProcessInflowPeriodicReportCounts($post);

			foreach($dataInflow as $keyInflow => $value){
				$ChartData['Label'][] = $value['Date'];
				if(!empty($value['FHAOrders'])){
					$ChartData['FHA'][] = count(explode(',', $value['FHAOrders']));
				}else{
					$ChartData['FHA'][] = 0;
				}
				if(!empty($value['VAOrders'])){
					$ChartData['VA'][] = count(explode(',', $value['VAOrders']));
				}else{
					$ChartData['VA'][] = 0;
				}
				
			}
		}
	
		echo json_encode($ChartData);	
		//echo json_encode($this->load->view('tableprocessinginflow',$data,'true'));
	}

	/**
	*Function for get FollowUp records for chart 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 09 November 2020.
	*/
	function getFollowUpProcessChart()
	{
		$post = $this->input->post();
		$data['FollowUpType'] = $this->input->post('Type');

		$fdate = new DateTime($post['FromDate']);
		$newadv_fromDate = $fdate->format('Y-m-d');
		
		$edate = new DateTime($post['ToDate']);
		$newadv_toDate = $edate->format('Y-m-d');

		$post['daycount']=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		if($data['FollowUpType']=='Individual'){
		  $dataFollowUp = $this->Dashboard_Model->getProcessFollowUpIndividualReportCounts($post);
		    
		  foreach($dataFollowUp as $keyFollowUp => $value){
		    $ChartData['Label'] = $value['UserName'];
		    if(!empty($value['TotalCleared'])){
		      $ChartData['Total'] = count(explode(',', $value['TotalCleared']));
		    }else{
		      $ChartData['Total'] = 0;
		    }
		    $FollowupChartDate[]=$ChartData;  
		  }
		}else{
		  $dataFollowUp = $this->Dashboard_Model->getProcessFollowUpPeriodicReportCounts($post);

		  foreach($dataFollowUp as $keyFollowUp => $value){
		    $ChartData['Label'] = $value['Date'];
		    if(!empty($value['TotalCleared'])){
		      $ChartData['Total'] = count(explode(',', $value['TotalCleared']));
		    }else{
		      $ChartData['Total'] = 0;
		    }
		    $FollowupChartDate[]=$ChartData; 
		  }
		}

		echo json_encode($FollowupChartDate); 
		//echo json_encode($this->load->view('tableprocessingFollowUp',$data,'true'));
	}

	/**
	* Function for get inflow records for chart
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 03-11-2020
	**/
	function getProductivityChart()
	{
		$post = $this->input->post();

		$fdate = new DateTime($post['FromDate']);
		$newadv_fromDate = $fdate->format('Y-m-d');
		
		$edate = new DateTime($post['ToDate']);
		$newadv_toDate = $edate->format('Y-m-d');

		$daycount=$this->dateDiffInDays($newadv_fromDate, $newadv_toDate); 

		if($post['Type']=='Team'){
			//Get productivity periodic reports
			$dataProductivity = $this->Dashboard_Model->getProductivityPeriodicReport($post['WorkflowModuleUID'],$newadv_fromDate,$newadv_toDate,$daycount);
		
			foreach($dataProductivity as $keyProductivity => $value){
				if(!empty($value['CompletedOrders'])){
					$ChartData['Label'][] = $value['Date'];
					$totalCount=count(explode(',', $value['CompletedOrders']));
					$ChartData['CompletedOrders'][] = $totalCount;//echo $totalCount.'<br>'.$value['UserCount'].'<br>'.$post['Target'].'<br>';
					$productivity=round((($totalCount/($value['UserCount']*$post['Target']))*100), 2);
					
					if(!empty($productivity)){
						$ChartData['Productivity'][] = $productivity;
					}else{
						$ChartData['Productivity'][] = 0;
					}
				}
			}
		}else{
			$dataInflow = $this->Dashboard_Model->getProductivityIndividualReport($post['WorkflowModuleUID'],$newadv_fromDate,$newadv_toDate);
		
			foreach($dataInflow as $keyInflow => $value){
					
				if(!empty($value['CompletedOrders'])){
					$ChartData['Label'][] = $value['UserName'];
					$totalCount=count(explode(',', $value['CompletedOrders']));
					$ChartData['CompletedOrders'][] = $totalCount;
					$productivity=round((($totalCount/($daycount*$post['Target']))*100), 2);
					
					if(!empty($productivity)){
						$ChartData['Productivity'][] = $productivity;
					}else{
						$ChartData['Productivity'][] = 0;
					}
				}			
			}
		}
	
		echo json_encode($ChartData);exit;	
		//echo json_encode($this->load->view('tableprocessinginflow',$data,'true'));
	}

	/**
	* Function for get Pending files records for chart
	* @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 06-11-2020
	**/
	function getPendingChart()
	{
		$FilterCounts = [];
		// Gatekeeping Filter List
		$filterlist = $this->input->post('filterlist');
		$QueueUID = $this->input->post('QueueUID');
    	$post['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
    	$adv_fromDate = $this->input->post("FromDate");
		$adv_toDate = $this->input->post("ToDate");

		$fdate = new DateTime($adv_fromDate);
		$post['fromDate'] = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$post['Todate'] = $edate->format('Y-m-d');

		if ($filterlist == '#completedorderslist') {
			$FilterCounts['ModelName'] = 'Common_Model';
			$FilterCounts['FunctionName'] = 'completedordersBasedOnWorkflow_count_all';
		}

		if (!empty($FilterCounts)) {
			$FetchPendingCountWorkflows = $this->config->item('GatekeepingWidgetFetchPendingCount');
			$post['advancedsearch']['IsPendingOrders'] = 'true';
			$PendingWorklfowsCount = [];
			foreach ($FetchPendingCountWorkflows as $WorkflowModuleName => $WorkflowModuleUID) {
				$post['advancedsearch']['WorkflowModuleUID'] = $WorkflowModuleUID;
				$PendingWorklfowsCount[$WorkflowModuleName] = $this->Dashboard_Model->{$FilterCounts['FunctionName']}($post,'count_all');
			}
			$post = [];
			$post['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
    		$post['advancedsearch']['WorkflowModuleUID'] = $this->input->post('WorkflowModuleUID');
			$post['advancedsearch']['IsWidgetCompletedOrders'] = 'true';
			$post['QueueUID'] = $QueueUID;
			$PendingWorklfowsCount['CompletedOrdersCount'] = $this->Dashboard_Model->{$FilterCounts['FunctionName']}($post,'count_all');
		}
		//$PendingWorklfowsCount=$PendingWorklfowsCount;

		
		foreach($PendingWorklfowsCount as $key => $value){
	        if($key!='CompletedOrdersCount'){
	         $data['name']=$key;
	         if(!empty($value)){
	         	$$WorkflowCount=0;
		         foreach($value as $PendingKey =>$PendingValue){
		         	$WorkflowCount = count(explode(',', $PendingValue['Count']));
			     }
			         $data['value']=$WorkflowCount;
			 }else{
			 	$data['value']=0;
			 }
	         $PendingData[]=$data;
	        } 
	    }

		echo trim(json_encode($PendingData));exit;
		//echo json_encode($this->load->view('tableprocessinginflow',$data,'true'));
	}

	/**
	* Function for Pipeline_reviewCountResult 
	* @author Vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 10-11-2020
	*/
	function Pipeline_reviewCountChart()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_period = $this->input->post("adv_period");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');

		$ReportCounts = $this->Dashboard_Model->Pipeline_ReviewCountReport($WorkFlowUID,$newadv_fromDate,$newadv_toDate);
		$data['label']=array('Completed','Pending');
		foreach($ReportCounts as $key => $value){
			//echo $value['ReviewCount'].'hi<br>';
			$completed=(!empty($value["CompleteCount"])) ? count(explode(',', $value["CompleteCount"])) : 0;
			$Pending=(!empty($value['PendingCount'])) ? count(explode(',', $value['PendingCount'])) : 0;
			$data['value']=array($completed,$Pending);
		}
		
		echo json_encode($data);

	}

	/**
	* Function for get pipeline aging report
	* @author Vishnupriya.A<vishnupriya.a@avanzegroup.com>
	* @since 10-11-2020
	**/
	function PipelineAgingChart()
	{
		$WorkFlowUID = $this->input->post("WorkFlows");
		$adv_fromDate = $this->input->post("adv_fromDate");
		$adv_toDate = $this->input->post("adv_toDate");

		$fdate = new DateTime($adv_fromDate);
		$newadv_fromDate = $fdate->format('Y-m-d');

		$edate = new DateTime($adv_toDate);
		$newadv_toDate = $edate->format('Y-m-d');
		
		$pipelineAgingData = $this->Dashboard_Model->pipeline_agingFileResult($WorkFlowUID,$newadv_fromDate,$newadv_toDate);

		$DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader1');
		//print_r($DashboardAgingBucketHeader['fivetotendays']);exit;
		foreach($pipelineAgingData as $key => $value){
			$data['category']=($value['WorkflowModuleName']);
			$data[$DashboardAgingBucketHeader['fivetotendays']]=(($value['fivetotendays']) != '') ? count(explode(',', ($value['fivetotendays']))) : 0;
			$data[$DashboardAgingBucketHeader['tentofifteendays']]=(($value['tentofifteendays']) != '') ? count(explode(',', ($value['tentofifteendays']))) : 0;
			$data[$DashboardAgingBucketHeader['fifteentotwentydays']]=(($value['fifteentotwentydays']) != '') ? count(explode(',', ($value['fifteentotwentydays']))) : 0;
			$data[$DashboardAgingBucketHeader['twentyfivetothirtydays']]=(($value['twentyfivetothirtydays']) != '') ? count(explode(',', ($value['twentyfivetothirtydays']))) : 0;
			$data[$DashboardAgingBucketHeader['pastdue']]=(($value['pastdue']) != '') ? count(explode(',', ($value['pastdue']))) : 0;
			$data[$DashboardAgingBucketHeader['duetoday']]=(($value['duetoday']) != '') ? count(explode(',', ($value['duetoday']))) : 0;
			$AgingDate[]=$data;
		}
		//print_r(json_encode($AgingDate));exit;
		echo json_encode($AgingDate);
	}

}?>
