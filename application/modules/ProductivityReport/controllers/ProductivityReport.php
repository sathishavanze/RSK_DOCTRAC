<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProductivityReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ProductivityReport_model');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['ProcessUsers'] = $this->ProductivityReport_model->getProcessUsers(array());
		$data['date'] = $this->monthFirstLastDay();
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		// $data['ProductivityReportCounts'] = $this->ProductivityReport_model->getProductivityReportCounts();
		// echo '<pre>';print_r($data);exit;
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
		// echo '<pre>';print_r($post);exit;
		$data['ProcessUsers'] = $this->ProductivityReport_model->getProcessUsers($post['Process']);
		$data['Target'] = $post['Target'];
		$data['ProductivityReportCounts'] = $this->ProductivityReport_model->getProductivityReportCounts($post);
		echo json_encode($this->load->view('tablepartialview',$data,'true'));
	}

	function WriteOrdersExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();
		$post = $this->input->post();
		$filename = 'ProcessInflow.xlsx';
		$ProcessUsers = $this->ProductivityReport_model->getProcessUsers($post['Process']);
		$ProductivityReportCounts = $this->ProductivityReport_model->getProductivityReportCounts($post);
		$rowTopHead = array();
		$rowTopHead[] = 'Dates';
		foreach ($ProcessUsers as $value) 
		{
			$rowTopHead[] = $value->UserName;
			$rowTopHead[] = '';
		}
		$writer->writeSheetRow($filename, array_values(($rowTopHead)));


		foreach ($ProductivityReportCounts as $ProductivityReportrow)
		{
			$row = array();
			foreach ($ProductivityReportrow as $ReportCounts) 
			{
				$row[] = $ReportCounts->date;
				foreach ($ProcessUsers as $value) 
				{ 	
					$row[] = $ReportCounts->{'process'.$value->UserUID};
					if($ReportCounts->{'total'.$value->UserUID} != 0)
					{
						$total = round( $ReportCounts->{'process'.$value->UserUID} * 100 / $ReportCounts->{'total'.$value->UserUID});
					}
					else
					{
						$total = 0;
					}
					$row[] = $total.'%';
				}
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
			$first_date = date('d-m-Y',strtotime('first day of this month'));
			$last_date = date('d-m-Y',strtotime('last day of this month'));
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

		$post['column_search'] = ['LoanNumber','BorrowerNames','MilestoneName','LoanType','PropertyStateCode','LoanProcessor'];
		$post['column_search'] = $post['column_search'];
		$List = $this->ProductivityReport_model->get_InflowReportList($post);

		$no = $post['start'];
		$returnlist = [];
		foreach ($List as $myorders)
		{
			$row = array();
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->BorrowerNames;
			$row[] = $myorders->MilestoneName;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->PropertyStateCode;
			$row[] = $myorders->LoanProcessor;
			$Action = '<a href="Ordersummary/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs OrderUID" data-orderuid = "'.$myorders->OrderUID.'" target="_new">'.$badge.'<i class="icon-pencil"></i></a>';

			$row[] = $Action;
			$returnlist[] = $row;
		}

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->ProductivityReport_model->count_all($post),
			"recordsFiltered" =>  $this->ProductivityReport_model->count_filtered($post),
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
		$list = $this->ProductivityReport_model->get_InflowReportList($post);

		$ExcelHeader = ['LoanNumber'=>'GENERAL','BorrowerNames'=>'GENERAL','MilestoneName'=>'GENERAL','LoanType'=>'GENERAL','PropertyStateCode'=>'GENERAL','LoanProcessor'=>'GENERAL'];
		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

		$writer->writeSheetHeader($filename, $ExcelHeader, $header_style);

		foreach ($list as $myorders)
		{
			$row = array();
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->BorrowerNames;
			$row[] = $myorders->MilestoneName;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->PropertyStateCode;
			$row[] = $myorders->LoanProcessor;
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
