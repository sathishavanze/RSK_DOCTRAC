<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class ExpirationReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ExpirationReportmodel');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Modules'] = $this->GetCustomerBasedModules();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function FetchExpirationReport() 
	{

		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');    	
		$post['column_order'] = array('tOrders.LoanNumber','tOrders.LoanType','tOrders.PropertyStateCode','mWorkFlowModules.WorkflowModuleName','mDocumentType.DocumentTypeName','tDocumentCheckList.DocumentDate','tDocumentCheckList.DocumentExpiryDate');
		$post['column_search'] = array('tOrders.LoanNumber','tOrders.LoanType','tOrders.PropertyStateCode','mWorkFlowModules.WorkflowModuleName','mDocumentType.DocumentTypeName','tDocumentCheckList.DocumentDate','tDocumentCheckList.DocumentExpiryDate');

		// Get Expired checklist ID's
		$DocumentTypeUIDArr = array();

		if (((!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0)) && $post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All') {
			if ($post['advancedsearch']['WorkflowModuleUID'] == "FHA_MONTH") {
				
				$DocumentTypeUIDArr[] = 2036;
			} else {

				$Expired_Checklist = $this->config->item('Expired_Checklist')[$post['advancedsearch']['WorkflowModuleUID']];
				foreach ($Expired_Checklist as $checklistkey => $DocumentTypeUID) {
					$DocumentTypeUIDArr[] = $DocumentTypeUID;
				}	
			}
			
		} else { 
			$Expired_Checklist = $this->config->item('Expired_Checklist');
			foreach ($Expired_Checklist as $Expired_Checklistkey => $Expired_Checklistvalue) {
				foreach ($Expired_Checklistvalue as $checklistkey => $DocumentTypeUID) {
				  $DocumentTypeUIDArr[] = $DocumentTypeUID;
				}
			}
		}

		$post['DocumentTypeUIDArr'] = $DocumentTypeUIDArr;

		$list = $this->ExpirationReportmodel->ExpirationReportOrders($post);
		
		$no = $post['start'];
		$ExpirationReportorderslist = [];
		foreach ($list as $key => $revieworders)
		{
			// Loan No,Borrower Name ,Milestone,State,Workflow,Associate ,Processor, Document Expiry Date , Days To Expire
			$row = array();
			$row[] = $revieworders->LoanNumber;
			$row[] = $revieworders->LoanType;
			$row[] = $revieworders->Borrower;
			$row[] = $revieworders->MilestoneName;
			$row[] = $revieworders->PropertyStateCode;
			$row[] = $revieworders->WorkflowModuleName;
			$row[] = $revieworders->UserName;
			$row[] = $revieworders->LoanProcessor;	
			$row[] = $revieworders->DocumentTypeName;		
			$row[] = $revieworders->DocumentDate;
			$row[] = $revieworders->DocumentExpiryDate;
			$row[] = $this->ExpiryDaysCalculate($revieworders->DocumentExpiryDate, date("Y-m-d"));
			$Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
			<i class="icon-pencil"></i></a>';
			$row[] = $Action;
			$ExpirationReportorderslist[] = $row;
		}

		$data =  array(
			'ExpirationReportorderslist' => ($ExpirationReportorderslist),
			'post' => $post
		);

		$post = $data['post'];

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->ExpirationReportmodel->count_all($post),
			"recordsFiltered" => $this->ExpirationReportmodel->count_filtered($post),
			"data" => $data['ExpirationReportorderslist']
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	function ExpiryDaysCalculate($startdate,$enddate) {
		$start = new DateTime($startdate);
		$end = new DateTime($enddate);
		// otherwise the  end date is excluded (bug?)
		// $end->modify('+1 day');

		$interval = $end->diff($start);

		// total days
		// return $days = $interval->days;
		return $days = (int)$interval->format("%r%a");
	}

	function WriteExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();

		$filename = 'ExpirationReport';

		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

		$ExcelHeader = array('Loan Number'=>'GENERAL','Loan Type'=>'GENERAL','Borrower Name'=>'GENERAL','Milestone'=>'GENERAL','State'=>'GENERAL','Workflow'=>'GENERAL','Associate'=>'GENERAL','Processor'=>'GENERAL','Checklist'=>'GENERAL','Document Date'=>'GENERAL','Document Expiry Date'=>'GENERAL','Days To Expire'=>'GENERAL');

		$writer->writeSheetHeader($filename, $ExcelHeader, $header_style);

		$post['advancedsearch'] = $this->input->post('formData');

		if (((!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0)) && $post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All') {

			if ($post['advancedsearch']['WorkflowModuleUID'] == "FHA_MONTH") {
				
				$DocumentTypeUIDArr[] = 2036;
			} else {

				$Expired_Checklist = $this->config->item('Expired_Checklist')[$post['advancedsearch']['WorkflowModuleUID']];
				foreach ($Expired_Checklist as $checklistkey => $DocumentTypeUID) {
					$DocumentTypeUIDArr[] = $DocumentTypeUID;
				}	
			}
		} else { 
			$Expired_Checklist = $this->config->item('Expired_Checklist');
			foreach ($Expired_Checklist as $Expired_Checklistkey => $Expired_Checklistvalue) {
				foreach ($Expired_Checklistvalue as $checklistkey => $DocumentTypeUID) {
				  $DocumentTypeUIDArr[] = $DocumentTypeUID;
				}
			}
		}

		$post['DocumentTypeUIDArr'] = $DocumentTypeUIDArr;

		$list = $this->ExpirationReportmodel->ExpirationReportOrders($post);

		for ($i=0; $i < sizeof($list); $i++){ 
 			$WorkflowData = [];
			$WorkflowData[] = $list[$i]->LoanNumber;
			$WorkflowData[] = $list[$i]->LoanType;
			$WorkflowData[] = $list[$i]->Borrower;
			$WorkflowData[] = $list[$i]->MilestoneName;
			$WorkflowData[] = $list[$i]->PropertyStateCode;
			if ($post['advancedsearch']['WorkflowModuleUID'] == "FHA_MONTH") {
				$WorkflowData[] = "FHA MONTH";
			} else {
				$WorkflowData[] = $list[$i]->WorkflowModuleName;
			}
			
			$WorkflowData[] = $list[$i]->UserName;
			$WorkflowData[] = $list[$i]->LoanProcessor;
			$WorkflowData[] = $list[$i]->DocumentTypeName;
			$WorkflowData[] = $list[$i]->DocumentDate;
			$WorkflowData[] = $list[$i]->DocumentExpiryDate;
			$WorkflowData[] = $this->ExpiryDaysCalculate($list[$i]->DocumentExpiryDate, date("Y-m-d"));
				
			$writer->writeSheetRow($filename, array_values(($WorkflowData)));
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

	function GetCustomerBasedModules()
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$this->db->select("*");
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID', 'left');
		$this->db->where('Active', 1);
		$this->db->where('mCustomerWorkflowModules.CustomerUID', $CustomerUID);
		if (!empty($this->config->item('Expired_Checklist_Enabled_Workflows'))) {
			$this->db->where_in('mCustomerWorkflowModules.WorkflowModuleUID', $this->config->item('Expired_Checklist_Enabled_Workflows'));
		}		
		$this->db->group_by('mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->order_by('mCustomerWorkflowModules.Position', 'ASC');
		return $this->db->get()->result();
	}

}?>
