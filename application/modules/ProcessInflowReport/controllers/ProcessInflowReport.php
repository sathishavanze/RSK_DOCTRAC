<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProcessInflowReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ProcessInflowReport_model');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['ProcessUsers'] = $this->ProcessInflowReport_model->getProcessUsers(array());
		$data['date'] = $this->monthFirstLastDay();
		// $data['ProcessInflowReportCounts'] = $this->ProcessInflowReport_model->getProcessInflowReportCounts();
		// echo '<pre>';print_r($data);exit;
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function getProcessTable()
	{
		$post = $this->input->post();
		// echo '<pre>';print_r($post);exit;
		$data['ProcessUsers'] = $this->ProcessInflowReport_model->getProcessUsers($post['Process']);
		$data['ProcessInflowReportCounts'] = $this->ProcessInflowReport_model->getProcessInflowReportCounts($post);
		echo json_encode($this->load->view('tablepartialview',$data,'true'));
	}

	function WriteOrdersExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();
		$post = $this->input->post();
		$filename = 'ProcessInflow.xlsx';
		$ProcessUsers = $this->ProcessInflowReport_model->getProcessUsers($post['Process']);
		$ProcessInflowReportCounts = $this->ProcessInflowReport_model->getProcessInflowReportCounts($post);

		// $ExcelHeader[] = 'GENERAL';
		// foreach ($ProcessUsers as $value) 
		// { 		
		// 	$ExcelHeader[] = 'GENERAL';
		// 	$ExcelHeader[$value->UserName] = 'GENERAL';
		// 	$ExcelHeader[] = 'GENERAL';
		// }
		// echo '<pre>';print_r($ExcelHeader);exit;
		// $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');
		// $writer->writeSheetHeader($filename, $ExcelHeader, $header_style);

		$rowTopHead = array();
		$rowTopHead[] = '';
		foreach ($ProcessUsers as $value) 
		{
			$rowTopHead[] = '';
			$rowTopHead[] = $value->UserName;
			$rowTopHead[] = '';
		}
		$writer->writeSheetRow($filename, array_values(($rowTopHead)));


		$rowHead = array();
		$rowHead[] = '';
		foreach ($ProcessUsers as $value) 
		{
			$rowHead[] = 'FHA';
			$rowHead[] = 'VA';
			$rowHead[] = 'Total';
		}
		$writer->writeSheetRow($filename, array_values(($rowHead)));


		foreach ($ProcessInflowReportCounts as $ProcessInflowReportrow)
		{
			$row = array();
			foreach ($ProcessInflowReportrow as $ReportCounts) 
			{
				$row[] = $ReportCounts->date;
				foreach ($ProcessUsers as $value) 
				{ 	
					$row[] = $ReportCounts->{'FHA'.$value->UserUID};
					$row[] = $ReportCounts->{'VA'.$value->UserUID};
					$row[] = $ReportCounts->{'total'.$value->UserUID};
				}
			} 
			$writer->writeSheetRow($filename, array_values(($row)));
		}

		// $start = 1;
		// $end = 3;
		// foreach ($ProcessUsers as $value) 
		// {
		// 	$writer->markMergedCell($filename, $start_row = 0, $start_col = $start, $end_row = 0, $end_col = $end);
		// 	$start = $start+3;
		// 	$end = $end+3;
		// }

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
	/** @author harini <harini.bangari@avanzegroup.com> **/
	/** @date  11 Aug 2020 **/
	/** @Getting month first and last date  **/
	function monthFirstLastDay()
	{
		$first_date = date('m/d/Y',strtotime('first day of this month'));
		$last_date = date('m/d/Y',strtotime('last day of this month'));
		return array('firstday'=>$first_date,'lastday'=>$last_date);
	}

}?>
