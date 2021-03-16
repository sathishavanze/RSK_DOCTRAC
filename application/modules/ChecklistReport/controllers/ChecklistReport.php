<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class ChecklistReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ChecklistReportmodel');
		//load xlsx library
		include APPPATH . 'third_party/xlsxwriter.class.php';
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Clients'] = $this->Common_Model->GetClients();

		// Checklist header info
		$data['ClientReports'] = $this->ChecklistReportmodel->GetReports();

		// $data['reportsinfogroup'] = $this->GetHeaderDynamic();

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function FetchReportsHeader() {		
		// Checklist header info
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$formData = $this->input->post('formData');
			$ReportUID = isset($formData['ReportUID']) && !empty($formData['ReportUID']) ? $formData['ReportUID'] : FALSE;

			if (empty($ReportUID)) {
				$result = array("validation_error" => 1,'message'=>"No reports are mapped to this client.");
				$this->session->unset_userdata('ReportUID');
				echo json_encode($result); exit();
			}

			$this->session->set_userdata('ReportUID', $ReportUID);

			$reportsinfo = $this->ChecklistReportmodel->Get_ReportsInfo($ReportUID);

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
		if (empty($post['advancedsearch']['ReportUID'])) {
			$ClientReports = $this->ChecklistReportmodel->GetReports();
			$post['advancedsearch']['ReportUID'] = $ClientReports[0]['ReportUID'];
		}

		$post['Reportrow'] = $this->ChecklistReportmodel->getreport_row($post['advancedsearch']['ReportUID']);

		$post['WorkflowDetails'] = $this->ChecklistReportmodel->Get_WorkflowDetails($post);

		$post['Checklists'] = [];

		if($post['Reportrow']->DisplayChecklist == 1 && !empty($post['Reportrow']->QueueUIDs)) {
			$post['Checklists'] = $this->ChecklistReportmodel->get_checklistcategory($post['Reportrow']->WorkflowModuleUID);
		}

		$datatable_searchcolumns = array_merge($post['WorkflowDetails'],$post['Checklists']);
		$post['column_order'] = [];
		foreach ($datatable_searchcolumns as $key => $datatable_searchcolumnvalue) {

			$columnname = '';
			if($datatable_searchcolumnvalue['ColumnType'] == 'Columns' && $datatable_searchcolumnvalue['IsChecklist'] && $datatable_searchcolumnvalue['IsChecklist'] == 1 && !empty($datatable_searchcolumnvalue['WorkflowUID'])) {
				$columnname = 'Column_'.alphanumericonly($datatable_searchcolumnvalue['ReportFieldUID'].$datatable_searchcolumnvalue['ChecklistOption']);
			} else if($datatable_searchcolumnvalue['ColumnType'] == 'Checklist') {
				$columnname = 'Checklist_'.alphanumericonly($datatable_searchcolumnvalue['DocumentTypeUID']);
			} else {
				$columnname = 'Column_'.alphanumericonly($datatable_searchcolumnvalue['ReportFieldUID'].$datatable_searchcolumnvalue['ColumnName']);

			}
			$post['column_order'][] = $columnname;
		}

		$post['column_search'] = $post['column_order'] ;

		// echo "<pre>";print_r($post);exit;

		$reportsdetails = $this->ChecklistReportmodel->Get_ReportsDetails($post);
// echo "<pre>";print_r($reportsdetails);exit;
		$ChecklistReportlist = [];
		//print all rows		
		foreach ($reportsdetails as $key => $value) {
			$row_arr = array();	
			foreach ($value as $skey => $svalue) {
				
				if($skey == 'Aging') {

					$row_arr[] = site_datetimeaging($svalue);

				} else if($skey == 'LOGIC-LCREQUIRED'){
					$row_arr[] = '$'.max(number_format((float)$svalue, 2), 0);
				} else {
					$row_arr[] = validateDate($svalue) ? site_datetimeformat($svalue) : $svalue;
				}
			}
			$ChecklistReportlist[] = $row_arr;
		}


		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->ChecklistReportmodel->count_all($post),
			"recordsFiltered" =>  $this->ChecklistReportmodel->count_filtered($post),
			"data" => $ChecklistReportlist,
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	function WriteXLS() {
		$post['advancedsearch'] = $this->input->post('formData');
		$ReportUID = $post['advancedsearch']['ReportUID'];

		$writer = new XLSXWriter();     //new writer
		$sheet_name = 'sheets';   //sheetname
		$header = array("string");  // header-made for 19 columns

		//formating header cell		
		$row_options = array('wrap_text'=>true);

		$writer->writeSheetHeader($sheet_name, $header, $suppress_header_row = true);   //write header

		if(!empty($ReportUID)) {

			$post['Reportrow'] = $this->ChecklistReportmodel->getreport_row($post['advancedsearch']['ReportUID']);

			$post['WorkflowDetails'] = $this->ChecklistReportmodel->Get_WorkflowDetails($post);

			$post['Checklists'] = [];

			if($post['Reportrow']->DisplayChecklist == 1 && !empty($post['Reportrow']->QueueUIDs)) {
				$post['Checklists'] = $this->ChecklistReportmodel->get_checklistcategory($post['Reportrow']->WorkflowModuleUID);
			}

			$reportsinfo = $this->ChecklistReportmodel->Get_ReportsInfo($ReportUID);

		//group by
			$arr = array();

			foreach ($reportsinfo as $key => $item) {
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
					$row2[] = $svalue['HeaderName'];
				}
				end($row1);
				$end_cols = key($row1);
				$writer->markMergedCell($sheet_name, $start_row = 0, $start_col = $start_cols, $end_row = 0, $end_col = $end_cols);  //merge cells	
			}

			//main header
			$writer->writeSheetRow($sheet_name, $row1, $row_options);   
			//write data

			//sub header(like group checklist)
			$writer->markMergedCell($sheet_name, $start_row = 1, $start_col = $single_cols, $end_row = 1, $end_col = $single_cols);
			$writer->writeSheetRow($sheet_name, $row2, $row_options);


			$reportsdetails = $this->ChecklistReportmodel->Get_ReportsDetails($post);
			//print all rows		
			foreach ($reportsdetails as $key => $value) {
				$row_arr = array();	
				foreach ($value as $skey => $svalue) {
					if($skey == 'Aging') {

						$row_arr[] = site_datetimeaging($svalue);

					} else {
						$row_arr[] = validateDate($svalue) ? site_datetimeformat($svalue) : $svalue;
					}
				}
				$writer->writeSheetRow($sheet_name, $row_arr);

			}
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
