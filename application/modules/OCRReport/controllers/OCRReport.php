<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class OCRReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('OCRReportmodel');
		
		include APPPATH . 'third_party/xlsxwriter.class.php';
	}

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function FetchReportsHeader() {		
		// Checklist header info
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$formData = $this->input->post('formData');
			
			$reportsinfo = $this->OCRReportmodel->Get_OCRReportsInfo($formData['ReportStatus']);

			//group by
			$reportsinfogroup = array();

			foreach ($reportsinfo as $key => $item) {
				$reportsinfogroup[$item['GroupName']][] = $item;
			}		
			echo json_encode($reportsinfogroup);
		}
	}

	function FetchReportsDetails() 
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');  
		
		$post['Reportrow'] = $this->OCRReportmodel->Get_OCRReportsInfo($post['advancedsearch']['ReportStatus']);		


		$post['column_order'] = array('tOrders.OrderNumber','tOrders.LoanNumber', 'mCustomer.CustomerName','tDocuments.IsStacking','tDocuments.DocumentName');

    //column search
		$post['column_search'] = array('tOrders.OrderNumber','tOrders.LoanNumber', 'mCustomer.CustomerName','tDocuments.IsStacking','tDocuments.DocumentName');

		$reportsdetails = $this->OCRReportmodel->Get_OCRReportsInfo($post['advancedsearch']['ReportStatus']);
		// $reportsdetails = $this->OCRReportmodel->Get_ReportsDetails($post);
		
		
		$OCRRportlist = [];
			
		foreach ($reportsdetails as $key => $value) {		
			$row_arr = array();		
			$row_arr[] = $value['OrderNumber'];
			$row_arr[] = $value['LoanNumber'];
			$row_arr[] = $value['CustomerName'];
			if($value['IsStacking'] == '4'){
				$row_arr[] = "Failed";
			}else if($value['IsStacking'] == '2'){ 
				$row_arr[] = "Success";
			}else if($value['IsStacking'] == '1'){
				$row_arr[] = '-';
			}
			$row_arr[] = $value['DocumentName'];
			$row_arr[] = '<a target="_blank" title="" href="'.base_url().$value['DocumentURL'].'" class="btn btn-sm btn-xs viewFile" style="background-color: #f2f2f2;color: #000;"><span class="mdi mdi-eye"></span>  View</a>';
			$OCRRportlist[] = $row_arr;
		}
		
		$data =  array(
			'reports' => $OCRRportlist,
			'post' => $post
		);

		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => count($reportsdetails),
			"recordsFiltered" => count($reportsdetails),
			"data" => $data['reports'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);

		/*$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->OCRReportmodel->count_all($post),
			"recordsFiltered" =>  $this->OCRReportmodel->count_filtered($post),
			"data" => $OCRRportlist,
		);

		unset($post);
		unset($data);
		echo json_encode($output);*/
	}

	function WriteXLS() {

		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');  
		
		$post['Reportrow'] = $this->OCRReportmodel->Get_OCRReportsInfo($post['advancedsearch']['ReportStatus']);		


		$post['column_order'] = array('OrderNumber','LoanNumber', 'CustomerName','OCR Status','DocumentName');

    //column search
		$post['column_search'] = array('OrderNumber','LoanNumber', 'CustomerName','OCR Status','DocumentName');

		$reportsdetails = $this->OCRReportmodel->Get_OCRReportsInfo($post['advancedsearch']['ReportStatus']);
		// $reportsdetails = $this->OCRReportmodel->Get_ReportsDetails($post);
		
		$writer = new XLSXWriter();     //new writer
		$sheet_name = 'sheets';   //sheetname
		$header = array("string");  // header-made for 19 columns

		//formating header cell		
		$row_options = array('wrap_text'=>true);

		$writer->writeSheetHeader($sheet_name, $header, $suppress_header_row = true);   //write header
		$arr = array();

		foreach ($post['column_order'] as $key => $item) {
			$arr[$item['GroupName']][] = $item;
		}

		$reportsrows = array();
		foreach ($arr as $key => $value) {
			$row1[] = $key;
			end($row1);
			$start_cols = key($row1);
			foreach ($value as $skey => $svalue) {		
				if($skey != count( $value ) -1) {
					$row1[] = "";
				}
				$row2[] = $svalue;
			}
			end($row1);
			$end_cols = key($row1);
			//$writer->markMergedCell($sheet_name, $start_row = 0, $start_col = $start_cols, $end_row = 0, $end_col = $end_cols);  //merge cells	
		}
		//main header
		//$writer->writeSheetRow($sheet_name, $row1, $row_options);   
		//write data

		//sub header(like group checklist)
		$writer->markMergedCell($sheet_name, $start_row = 1, $start_col = $single_cols, $end_row = 1, $end_col = $single_cols);
		$writer->writeSheetRow($sheet_name, $row2, $row_options);

		$OCRRportlist = [];
			
		foreach ($reportsdetails as $key => $value) {		
			$row_arr = array();		
			$row_arr[] = $value['OrderNumber'];
			$row_arr[] = $value['LoanNumber'];
			$row_arr[] = $value['CustomerName'];
			if($value['IsStacking'] == '4'){
				$row_arr[] = "Failed";
			}else if($value['IsStacking'] == '2'){ 
				$row_arr[] = "Success";
			}else if($value['IsStacking'] == '1'){
				$row_arr[] = '-';
			}
			$row_arr[] = $value['DocumentName'];
			
			$writer->writeSheetRow($sheet_name, $row_arr);
		}

		
		$fileLocation = 'ChecklistReport.xlsx';
		$writer->writeToFile($fileLocation);  //save file

		//force download
		header('Content-Description: File Transfer');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=".basename($fileLocation));
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Length: ' . filesize($fileLocation)); //Remove

		ob_clean();
		flush();

		readfile($fileLocation);
		unlink($fileLocation);
		exit(0);


	}
	
}?>
