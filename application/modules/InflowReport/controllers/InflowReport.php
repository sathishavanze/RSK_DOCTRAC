<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class InflowReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('InflowReport_model');
	}

	public function index()
	{
		$data['content'] = 'index';
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
	function getProcessTable()
	{
		$post = $this->input->post();
		$data['InflowReportCounts'] = $this->InflowReport_model->getInflowReportCounts($post);
		echo json_encode($this->load->view('tablepartialview',$data,'true'));
	}

	function Goals()
	{
		$data['content'] = 'goals';
		$data['date'] = $this->weekFirstLastDay();
		$data['MTDGoals'] = $this->InflowReport_model->getCustomerMTDGoals();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function getGoalsTable()
	{
		$CustomerUID = $this->parameters['DefaultClientUID'];
		$post = $this->input->post();
		$first_date = date('Y-m-d',strtotime('first day of this month'));
		$Achive = $this->db->select("count( DISTINCT tOrders.OrderUID) as OrderUID")->FROM('tOrders')->WHERE("DATE(tOrders.OrderEntryDateTime) BETWEEN '".$first_date."' AND '".date('Y-m-d')."' AND tOrders.CustomerUID =".$CustomerUID)->get()->row()->OrderUID;
		$Required = $post['Goals']-$Achive;

		/*$today = new DateTime();
		$lastDayOfThisMonth = new DateTime('last day of this month');
		$RemainDays =  $lastDayOfThisMonth->diff($today)->format('%a');*/
		$beginday=date('Y-m-d');
		$lastday=date('Y-m-t');
		$RemainDays = $this->getWorkingDays($beginday,$lastday);
		$Inflow = round($Required/$RemainDays);
		$data['ResultData'] = array(
			date('M').' Goal' => $post['Goals'],
			date('M').' MTD' => $Achive,
			'Total Inflows required'=>$Required,
			'Working Days remaining'=>$RemainDays,
			'Daily inflows required'=>$Inflow);
		echo json_encode($this->load->view('Goalstable',$data,'true'));
	}
	/** @author harini <harini.bangari@avanzegroup.com> **/
	/** @date  13 Aug 2020 **/
	/** @Getting working days in month  **/
	function getWorkingDays($startDate, $endDate){
		$begin=strtotime($startDate);
		 $end=strtotime($endDate);
		   $no_days=0;
		   $weekends=0;
  			while($begin<=$end){
		    $no_days++; // no of days in the given interval
		    $what_day=date('N',$begin);
		     if($what_day>5) { // 6 and 7 are weekend days
		          $weekends++;
		     }
		    $begin+=86400; // +1 day
		  }
		  $working_days=$no_days-$weekends;
		  return $working_days;	
		
	}
	function WriteOrdersExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();
		$post = $this->input->post();
		$filename = 'ProcessInflow.xlsx';
		$InflowReportCounts = $this->InflowReport_model->getInflowReportCounts($post);

		$rowHead = array();
		$rowHead[] = 'Inflows';
		$rowHead[] = 'FHA';
		$rowHead[] = 'VA';
		$rowHead[] = 'Total';
		$rowHead[] = 'FHA';
		$rowHead[] = 'VA';
		$rowHead[] = 'Total';
		$writer->writeSheetRow($filename, array_values(($rowHead)));


		foreach ($InflowReportCounts as $InflowReportrow)
		{
			$row = array();
			foreach ($InflowReportrow as $ReportCounts) 
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
		$CustomerUID = $this->parameters['DefaultClientUID'];
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
		$first_date = date('Y-m-d',strtotime('first day of this month'));
		$Achive = $this->db->select("count( DISTINCT tOrders.OrderUID) as OrderUID")->FROM('tOrders')->WHERE("DATE(tOrders.OrderEntryDateTime) BETWEEN '".$first_date."' AND '".date('Y-m-d')."' AND tOrders.CustomerUID =".$CustomerUID)->get()->row()->OrderUID;
		$Required = $post['Goals']-$Achive;

		$today = new DateTime();
		$lastDayOfThisMonth = new DateTime('last day of this month');
		$RemainDays =  $lastDayOfThisMonth->diff($today)->format('%a');

		$Inflow = round($Required/$RemainDays);
		$ResultData = array(
			date('M').' Goal' => $post['Goals'],
			date('M').' MTD' => $Achive,
			'Total Inflows required'=>$Required,
			'Working Days remaining'=>$RemainDays,
			'Daily inflows required'=>$Inflow);


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

	function ChangeMTDGoals()
	{
		$MTDGoals = $this->input->post('MTDGoals');
		$CustomerUID = $this->parameters['DefaultClientUID'];
		$this->db->set('MTDGoals', $MTDGoals);
		$this->db->where('CustomerUID', $CustomerUID);
		$this->db->update('mCustomer'); 
		if($this->db->affected_rows() > 0)
		{
			echo json_encode(1);
		}
	}

}?>
