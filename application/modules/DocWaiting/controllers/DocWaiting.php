<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocWaiting extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocWaitingmodel');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$data['Modules'] = $this->Common_Model->GetModules();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function FetchDocWaitingReport()
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		// echo '<pre>';print_r($post['advancedsearch']);exit;
		//Advanced Search
		$Hours = 0;
		if (isset($post['advancedsearch']['Hours'])) {
			$Hours = $post['advancedsearch']['Hours'];
		}else{
			$Hours =0;
		}
		
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');    	
		$post['column_order'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','tOrders.LoanNumber','tOrders.LoanType','','','','','','','','');
		$post['column_search'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','tOrders.LoanNumber','tOrders.LoanType','mStatus.StatusName');

		$list = $this->DocWaitingmodel->DocWaitingReportOrders($post);         
		$no = $post['start'];
		$DocWaitingorderslist = [];
		foreach ($list as $revieworders)
		{
			$ProblemIdentified = '';
			$WorkflowModuleUID =$this->config->item('Workflows')['WelcomeCall'];
			
			$CheckListAnswers = $this->DocWaitingmodel->getCheckListAnswers($revieworders->OrderUID,$WorkflowModuleUID);

			$WelcomeCall = $this->DocWaitingmodel->getWelcomeDetails($revieworders->OrderUID);

			$DocList = $this->DocWaitingmodel->getDocWaitingList($revieworders->OrderUID);
			$OrderUID = 0;
			if (!empty($WelcomeCall) && !empty($DocList) && $DocList !='-' && !empty($CheckListAnswers)) {
				$datetime2 = $DocList->CompleteDateTime;//start time
				$datetime1 = Date('Y-m-d H:i:s');//end time
				$starttimestamp = strtotime($datetime2);
				$endtimestamp = strtotime($datetime1);
				$seconds_diff = $starttimestamp - $endtimestamp;                            
				$time = ($seconds_diff/60);
				/*$time = $this->dateDiff($datetime1,$datetime2);
				$ProblemIdentified = $time;//00 years 0 months 0 days 08 hours 0 minutes 0 seconds
				$OrderUID = $revieworders->OrderUID;*/

				/* Check datetime exceeds 48 hours*/
				$hours_difference = $this->Common_Model->differenceInHours($datetime2,$datetime1);
				$ProblemIdentified = $hours_difference.' Hrs'; /*Difference in Hours*/
				$OrderUID = $revieworders->OrderUID;

			}else{
				$ProblemIdentified = '-';
				$OrderUID = 0;
			}

			if ($Hours ==1) {
				$row = array();
				if ($ProblemIdentified!='-' && $OrderUID !=0) {
								
				$row[] = $revieworders->OrderNumber;
				$row[] = $revieworders->LoanNumber;
				$row[] = $revieworders->LoanType;
				$row[] = '<a  href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 7px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
				$row[] = $this->DocWaitingmodel->getworkflowPreScreenstatus($revieworders->OrderUID);
				$row[] = $ProblemIdentified;
				$row[] = $this->DocWaitingmodel->getworkflowWelcomeCallstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowTitleTeamstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowFHAVACaseTeamstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowThirdPartyTeamstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowHOI($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowPayOff($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowDocChasestatus($revieworders->OrderUID);
				$row[] = $revieworders->OrderDueDate;
				
				$Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
				<i class="icon-pencil"></i></a>';
				$row[] = $Action;
				
				}
				if (!empty($row)) {
					$DocWaitingorderslist[] = $row;
				}					
			}else{
				
				$row = array();
				$row[] = $revieworders->OrderNumber;
				$row[] = $revieworders->LoanNumber;
				$row[] = $revieworders->LoanType;
				$row[] = '<a  href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
				$row[] = $this->DocWaitingmodel->getworkflowPreScreenstatus($revieworders->OrderUID);
				$row[] = $ProblemIdentified;
				$row[] = $this->DocWaitingmodel->getworkflowWelcomeCallstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowTitleTeamstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowFHAVACaseTeamstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowThirdPartyTeamstatus($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowHOI($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowPayOff($revieworders->OrderUID);
				$row[] = $this->DocWaitingmodel->getworkflowDocChasestatus($revieworders->OrderUID);
				$row[] = $revieworders->OrderDueDate;
				
				$Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
				<i class="icon-pencil"></i></a>';
				$row[] = $Action;
			$DocWaitingorderslist[] = $row;
			}
		}

		$data =  array(
			'DocWaitingorderslist' => ($DocWaitingorderslist),
			'post' => $post
		);

		$post = $data['post'];

		if ($Hours == 1) {
			$count_all = count($DocWaitingorderslist);
		}else{
			$count_all = $this->DocWaitingmodel->count_filtered($post);
		}
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->DocWaitingmodel->count_all(),
			"recordsFiltered" =>  $count_all,
			"data" => $data['DocWaitingorderslist'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	function WriteExcel()
	{

		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
			$this->DocWaitingmodel->GetDocWaitingOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
		$list = $this->DocWaitingmodel->GetDocWaitingOrdersExcelRecords($post);
		$data = [];

		$data[] = array(
			'Order Number',
			'Loan Number',
			'Loan Type',
			'Status',
			'PreScreen',
			'48 Hours Waiting',
			'WelcomeCall',
			'TitleTeam',
			'FHAVACaseTeam',
			'ThirdPartyTeam',
			'HOI',
			'PayOff',
			'DocChase',
			// 'Workup',
			// 'UnderWriter',
			// 'Scheduling',
			// 'Closing',
			'Due DateTime'
		);
		for ($i=0; $i < sizeof($list); $i++){ 

			$PreScreen = $this->DocWaitingmodel->getworkflowPreScreenstatus($list[$i]->OrderUID);
			$WelcomeCall = $this->DocWaitingmodel->getworkflowWelcomeCallstatus($list[$i]->OrderUID);
			$TitleTeam = $this->DocWaitingmodel->getworkflowTitleTeamstatus($list[$i]->OrderUID);
			$FHAVACaseTeam = $this->DocWaitingmodel->getworkflowFHAVACaseTeamstatus($list[$i]->OrderUID);
			$ThirdPartyTeam = $this->DocWaitingmodel->getworkflowThirdPartyTeamstatus($list[$i]->OrderUID);
			$HOI = $this->DocWaitingmodel->getworkflowHOI($list[$i]->OrderUID);
			$PayOff = $this->DocWaitingmodel->getworkflowPayOff($list[$i]->OrderUID);
			$DocChase = $this->DocWaitingmodel->getworkflowDocChasestatus($list[$i]->OrderUID);
			//$Workup = $this->DocWaitingmodel->getworkflowWorkupstatus($list[$i]->OrderUID);
			//$UnderWriter = $this->DocWaitingmodel->getworkflowUnderWriterstatus($list[$i]->OrderUID);
			//$Scheduling = $this->DocWaitingmodel->getworkflowSchedulingstatus($list[$i]->OrderUID);
			//$Closing = $this->DocWaitingmodel->getworkflowClosingstatus($list[$i]->OrderUID);

			$data[] = array(
				$list[$i]->OrderNumber,
				$list[$i]->LoanNumber,
				$list[$i]->StatusName,
				$PreScreen,
				$WelcomeCall,
				$TitleTeam,
				$FHAVACaseTeam,
				$ThirdPartyTeam,
				$HOI,
				$PayOff,
				$DocChase,
				// $Workup,
				// $UnderWriter,
				// $Scheduling,
				// $Closing,
				$list[$i]->OrderDueDate
			);
		}

		$this->outputCSV($data);
	}

	function outputCSV($data) 
	{
		ob_clean();
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=file.csv");
		$output = fopen("php://output", "w");
		foreach ($data as $row)
		{
		    fputcsv($output, $row); // here you can change delimiter/enclosure
		}
		fclose($output);
		ob_flush();
	}


  function dateDiff($time1, $time2, $precision = 6) {
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }

    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }

    // Set up intervals and diffs arrays
    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();

    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Create temp time from time1 and interval
      $ttime = strtotime('+1 ' . $interval, $time1);
      // Set initial values
      $add = 1;
      $looped = 0;
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
        // Create new temp time from time1 and interval
        $add++;
        $ttime = strtotime("+" . $add . " " . $interval, $time1);
        $looped++;
      }
 
      $time1 = strtotime("+" . $looped . " " . $interval, $time1);
      $diffs[$interval] = $looped;
    }
    
    $count = 0;

    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
        break;
      }
      
      // Add value and interval 
      //if value is bigger than 0
      // if ($value > 0) {
      //   // Add s if value is not 1
      //   if ($value != 1) {
      //     $interval .= "s";
      //   }
      //   // Add value and interval to times array
      //   $times[] = $value;
      //   $count++;
      // }

      if ($interval == 'hour' && $value > 1) {
      	$times[] = $value." hr";
      	$count++;
      }

    }

	if (!empty($times)) {
		$times = $times;
	}else{
		$times[]= 0;
	}

    // Return string with times
    return implode(", ", $times);
  }

}?>
