<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class CycleTimeReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('CycleTimeReport_model');
	}


	function index()
	{
		$first_date = date('Y-m-d',strtotime('first day of this month'));
		$data['content'] = 'CycleTimeMTD';
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['cycletimeCount'] = $this->CycleTimeReport_model->getMTDCycleTime($first_date);
		// echo '<pre>';print_r($data['cycletimeCount']);exit;
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function CycleTime()
	{
		$data['content'] = 'index';
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['date'] = $this->monthFirstLastDay();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function agent()
	{
		$data['content'] = 'index';
		$data['ProcessUsers'] = $this->CycleTimeReport_model->getProcessUsers(array());
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['date'] = $this->monthFirstLastDay();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function substatus()
	{
		$data['content'] = 'index';
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['date'] = $this->monthFirstLastDay();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function substatusagent()
	{
		$data['content'] = 'index';
		$data['ProcessUsers'] = $this->CycleTimeReport_model->getProcessUsers(array());
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['date'] = $this->monthFirstLastDay();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	/** @author harini <harini.bangari@avanzegroup.com> **/
	/** @date  11 Aug 2020 **/
	/** @Getting month first and last date  **/
	function monthFirstLastDay()
	{
		$first_date = date('m/d/Y',strtotime('first day of this month'));
		$last_date = date('m/d/Y',strtotime('last day of this month'));
		return array('firstday'=>$first_date,'lastday'=>$last_date);
	}
	function getOverallTable()
	{
		$post = $this->input->post();
		$data['CycleTimeReportrows'] = $this->CycleTimeReport_model->get_CycleTimeReportCounts($post);
		echo json_encode($this->load->view('overalltableview',$data,'true'));
	}

	function getAgentTable()
	{
		$post = $this->input->post();
		$data['CycleTimeReportrows'] = $this->CycleTimeReport_model->get_AgentCycleTimeReportCounts($post);
		$data['ProcessUsers'] = $this->CycleTimeReport_model->getProcessUsers($post['Processor']);
		echo json_encode($this->load->view('agenttableview',$data,'true'));
	}

	function getQueueTable()
	{
		$post = $this->input->post();
		$data['Queues'] = $this->Common_Model->getCustomerWorkflowQueues($post['WorkflowModuleUID']);
		$data['WorkflowName'] = $this->db->select('SystemName')->from('mWorkFlowModules')->where('mWorkFlowModules.WorkflowModuleUID',$post['WorkflowModuleUID'])->get()->row()->SystemName;
		$post['Queues'] = $data['Queues'];
		$post['Modules'] = $data['Modules'];
		$data['CycleTimeReportrows'] = $this->CycleTimeReport_model->get_subqueuesCycleTimeReportCounts($post);
		echo json_encode($this->load->view('queuetableview',$data,'true'));
	}

	function getAgentQueueTable()
	{
		$post = $this->input->post();
		$data['Queues'] = $this->Common_Model->getCustomerWorkflowQueues($post['WorkflowModuleUID']);
		$data['ProcessUsers'] = $this->CycleTimeReport_model->getProcessUsers($post['Processor']);
		$data['WorkflowName'] = $this->db->select('SystemName')->from('mWorkFlowModules')->where('mWorkFlowModules.WorkflowModuleUID',$post['WorkflowModuleUID'])->get()->row()->SystemName;
		$post['Queues'] = $data['Queues'];
		$post['Modules'] = $data['Modules'];
		$data['CycleTimeReportrows'] = $this->CycleTimeReport_model->get_subqueuesAgentCycleTimeReportCounts($post);

		echo json_encode($this->load->view('queueagenttableview',$data,'true'));
	}


	function WriteOrdersExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();
		$post = $this->input->post();
		$filename = 'ProcessInflow.xlsx';
		$CycleTimeReportCounts = $this->CycleTimeReport_model->getCycleTimeReportCounts($post);

		$rowHead = array();
		$rowHead[] = 'Inflows';
		$rowHead[] = 'FHA';
		$rowHead[] = 'VA';
		$rowHead[] = 'Total';
		$rowHead[] = 'FHA';
		$rowHead[] = 'VA';
		$rowHead[] = 'Total';
		$writer->writeSheetRow($filename, array_values(($rowHead)));


		foreach ($CycleTimeReportCounts as $CycleTimeReportrow)
		{
			$row = array();
			foreach ($CycleTimeReportrow as $ReportCounts) 
			{	
					$row[] = $ReportCounts->date;
					$row[] = $ReportCounts->{'FHA'};
					$row[] = $ReportCounts->{'VA'};
					$row[] = $ReportCounts->{'total'};

					if($ReportCounts->{'total'} != 0)
					{
						$fhaPercent = round($ReportCounts->{'FHA'} * 100 / $ReportCounts->{'total'});
						$vaPercent = round($ReportCounts->{'VA'} * 100 / $ReportCounts->{'total'});
						$totalPercent = $fhaPercent+$vaPercent;
					}
					else
					{
						$fhaPercent = 0;
						$vaPercent = 0;
						$totalPercent = 0;
					}

					$row[] = $fhaPercent.'%';
					$row[] = $vaPercent.'%';
					$row[] = $totalPercent.'%';
			} 
			$writer->writeSheetRow($filename, array_values(($row)));
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


	function WriteGoalsExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();
		$post = $this->input->post();
		$filename = 'WriteGoalsExcel.xlsx';
		$ExcelHeader['Description'] = 'GENERAL';
		$ExcelHeader['Count'] = 'GENERAL';

		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');
		$writer->writeSheetHeader($filename, $ExcelHeader, $header_style);


		$post = $this->input->post();
		$first_date = date('d-m-Y',strtotime('first day of this month'));
		$Achive = $this->db->select("count( DISTINCT tOrders.OrderUID) as OrderUID")->FROM('tOrders')->WHERE("DATE(tOrders.OrderEntryDateTime) BETWEEN '".$first_date."' AND '".$post['FromDate']."'")->get()->row()->OrderUID;
		$Required = $post['Goals']-$Achive;

		$today = new DateTime();
		$lastDayOfThisMonth = new DateTime('last day of this month');
		$RemainDays =  $lastDayOfThisMonth->diff($today)->format('%a');

		$Inflow = round($Required/$RemainDays);
		$ResultData = array(
			date('M',strtotime($post['FromDate'])).' Goal' => $post['Goals'],
			date('M d',strtotime($post['FromDate'])).'th' => $Achive,
			'Required'=>$Required,
			'Working Days remaining'=>$RemainDays,
			'Daily Inflows'=>$Inflow);


		foreach ($ResultData as $key => $value) 
		{
			$row = [];
			$row[] = $key;
			$row[] = $value;
			$writer->writeSheetRow($filename, array_values(($row)));
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

	function getFromToDate()
	{
		$period = $this->input->post('period');
		if($period == 'week')
		{
			$date = date('Y-m-d'); // you can put any date you want
			$nbDay = date('N', strtotime($date));
			$monday = new DateTime($date);
			$sunday = new DateTime($date);
			$monday->modify('-'.($nbDay-1).' days');
			$sunday->modify('+'.(7-$nbDay).' days');
			echo  json_encode(array('fromDate'=>$monday->format('m/d/Y'),'toDate'=>$sunday->format('m/d/Y')));
		}
		else if($period == 'month')
		{
			$first_date = date('m/d/Y',strtotime('first day of this month'));
			$last_date = date('m/d/Y',strtotime('last day of this month'));
			echo  json_encode(array('fromDate'=>$first_date,'toDate'=>$last_date));
		}
	}

	function weekFirstLastDay()
	{
		$date = date('Y-m-d'); // you can put any date you want
		$nbDay = date('N', strtotime($date));
		$monday = new DateTime($date);
		$sunday = new DateTime($date);
		$monday->modify('-'.($nbDay-1).' days');
		$sunday->modify('+'.(7-$nbDay).' days');
		return array('firstday'=>$monday->format('m/d/Y'),'lastday'=>$sunday->format('m/d/Y'));
	}


	//count list orders 
		function fetchorders() 
	{
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['OrderUID'] = $this->input->post('OrderUID');
		$Modules = $this->Common_Model->GetCustomerBasedModules();
		$searchDate = [];
		$searchDate[] = 'LoanNumber';
		foreach ($Modules as $key => $workflow) 
		{
			$searchDate[] = $workflow->SystemName;
		}
		$post['column_search'] = $searchDate;
		$post['column_order'] = $post['column_search'];
		$List = $this->CycleTimeReport_model->get_CycleReportList($post);
		$no = $post['start'];
		$returnlist = [];
		foreach ($List as $myorders)
		{
			$row = array();
			$row[] = $myorders->LoanNumber;
			foreach ($Modules as $key => $Workflow) 
			{
				$row[] = !empty($myorders->{$Workflow->SystemName}) ? $myorders->{$Workflow->SystemName} : NULL;
			}
			$Action = '<a href="Ordersummary/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs OrderUID" data-orderuid = "'.$myorders->OrderUID.'" target="_new">'.$badge.'<i class="icon-pencil"></i></a>';

			$row[] = $Action;
			$returnlist[] = $row;
		}

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->CycleTimeReport_model->count_all($post),
			"recordsFiltered" =>  $this->CycleTimeReport_model->count_filtered($post),
			"data" => $returnlist,
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function WriteListExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();
		$post = [];
		$post['OrderUID'] = $this->input->post('OrderUID');
		$filename = $this->input->post('filename');
		$filename = ($filename) ? str_replace(' ', '', $writer->sanitize_filename($filename)) : 'Sheet.xlsx';
		$list = $this->CycleTimeReport_model->get_CycleReportList($post);

		$Modules = $this->Common_Model->GetCustomerBasedModules();
		$searchDate = [];
		$ExcelHeader['LoanNumber'] = 'GENERAL';
		foreach ($Modules as $key => $workflow) 
		{
			$ExcelHeader[$workflow->SystemName] ='GENERAL';
		}

		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

		$writer->writeSheetHeader($filename, $ExcelHeader, $header_style);

		foreach ($list as $myorders)
		{
			$row = array();
			$row[] = $myorders->LoanNumber;
			foreach ($Modules as $key => $Workflow) 
			{
				$row[] = !empty($myorders->{$Workflow->SystemName}) ? $myorders->{$Workflow->SystemName} : NULL;
			}
			$writer->writeSheetRow($filename, array_values(($row)));
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



	
	

}?>
