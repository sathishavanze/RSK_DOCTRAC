<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class PipelineReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('PipelineReportmodel');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function FetchPipelineReportReport() 
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');    	
		$post['column_order'] = array('tOrders.OrderNumber','tOrders.LoanNumber','tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrders.LoanType','','','','','','','','','','');
		$post['column_search'] = array('tOrders.OrderNumber','tOrders.LoanNumber','tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrders.LoanType','tOrders.PropertyStateCode');

		$list = $this->PipelineReportmodel->PipelineReportReportOrders($post); 
		$workflows = $this->Common_Model->GetCustomerBasedModules();        
		$no = $post['start'];
		$PipelineReportorderslist = [];
		foreach ($list as $key => $revieworders)
		{
				$row = array();
				$row[] = $revieworders->OrderNumber;
				$row[] = $revieworders->LoanNumber;
				$row[] = $revieworders->Borrower;
				$row[] = $revieworders->MilestoneName;
				$row[] = $revieworders->PropertyStateCode;
				$row[] = $revieworders->LoanType;
				foreach ($workflows as $key => $value) 
				{
				 $row[] = $revieworders->{$value->SystemName};
				}

				//Workflow completed date time and completed by
				foreach ($workflows as $key => $value) 
				{
				 $row[] = site_datetimeformat($revieworders->{$value->SystemName.'DateTime'});
				 $row[] = $revieworders->{$value->SystemName.'CompletedBy'};
				}

				$PendingWorkflows = [];
				foreach ($workflows as $key => $value) 
				{
					if ($revieworders->{$value->SystemName} == "Pending") {
						$PendingWorkflows[] = $value->WorkflowModuleName;
					}
				}
				$PendingWorkflowsList = implode(', ', $PendingWorkflows);

				if (!empty($PendingWorkflowsList)) {
					if (strlen($PendingWorkflowsList) > 25) {
						$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($PendingWorkflowsList, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($PendingWorkflowsList, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
					} else {
						$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($PendingWorkflowsList, 0,25).'</div>';
					}
				} else {
					$row[] = '';
				}

				// $row[] = $PendingWorkflowsList;

				$row[] = count($PendingWorkflows);

				// Workflow Comments
				$WorkflowComments = $revieworders->WorkflowComments;
				if (!empty($WorkflowComments)) {
					if (strlen($WorkflowComments) > 25) {
						$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($WorkflowComments, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($WorkflowComments, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
					} else {
						$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($WorkflowComments, 0,25).'</div>';
					}
				} else {
					$row[] = '';
				}

				$Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
				<i class="icon-pencil"></i></a>';
				$row[] = $Action;
			$PipelineReportorderslist[] = $row;
		}

		$data =  array(
			'PipelineReportorderslist' => ($PipelineReportorderslist),
			'post' => $post
		);

		$post = $data['post'];

		$count_all = $this->PipelineReportmodel->count_filtered($post);

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->PipelineReportmodel->count_all(),
			"recordsFiltered" =>  $count_all,
			"data" => $data['PipelineReportorderslist'],
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
		}
		else
		{
			$post['advancedsearch'] = $this->input->post('formData');
		}
		$list = $this->PipelineReportmodel->GetPipelineReportOrdersExcelRecords($post);
		$workflows = $this->Common_Model->GetCustomerBasedModules(); 
		$data = [];
		$HeaderData = [];

			$HeaderData[] = 'Order Number';
			$HeaderData[] = 'Loan Number';
			$HeaderData[] = 'Borrower';
			$HeaderData[] = 'Milestone';
			$HeaderData[] = 'State';
			$HeaderData[] = 'Loan Type';

		foreach ($workflows as $key => $value) 
		{
		  $HeaderData[] = $value->SystemName;
		}

		//Workflow completed date time and completed by
		foreach ($workflows as $key => $value) 
		{
		  $HeaderData[] = $value->SystemName.' Completed Date & Time';
		  $HeaderData[] = $value->SystemName.' Completed By';
		}

		$HeaderData[] = "Pending Workflows";

		$HeaderData[] = "Total Pending Count";

		$HeaderData[] = "Comments";

		$data[] = $HeaderData;

		for ($i=0; $i < sizeof($list); $i++){ 
 			$WorkflowData = [];
				$WorkflowData[] = $list[$i]->OrderNumber;
				$WorkflowData[] = $list[$i]->LoanNumber;
				$WorkflowData[] = $list[$i]->Borrower;
				$WorkflowData[] = $list[$i]->MilestoneName;
				$WorkflowData[] = $list[$i]->PropertyStateCode;
				$WorkflowData[] = $list[$i]->LoanType;

				foreach ($workflows as $key => $value) 
				{
					$WorkflowData[] = $list[$i]->{$value->SystemName};
				}

				//Workflow completed date time and completed by
				foreach ($workflows as $key => $value) 
				{
					$WorkflowData[] = site_datetimeformat($list[$i]->{$value->SystemName.'DateTime'});
					$WorkflowData[] = $list[$i]->{$value->SystemName.'CompletedBy'};
				}

				$PendingWorkflows = [];
				foreach ($workflows as $key => $value) 
				{
					if ($list[$i]->{$value->SystemName} == "Pending") {
						$PendingWorkflows[] = $value->WorkflowModuleName;
					}
				}
				$PendingWorkflowsList = implode(', ', $PendingWorkflows);

				$WorkflowData[] = $PendingWorkflowsList;

				$WorkflowData[] = count($PendingWorkflows);

				$WorkflowData[] = $list[$i]->WorkflowComments;
				
		$data[] = $WorkflowData;
		}
		$this->outputCSV($data);
	}

	function outputCSV($data) 
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
  		require_once APPPATH."third_party/xlsxwriter.class.php";
  		$writer = new XLSXWriter();

  		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

  		$header = true;
  		$ExcelHeader = array();
  		foreach ($data as $key => $value) {
  			if ($header) {
  				$header = false;
  				foreach ($value as $k => $v) {
  					$ExcelHeader[$v] = 'string';
  				}
  				$writer->writeSheetHeader('Pipeline_Report',$ExcelHeader, $header_style);
  			} else {
  				$writer->writeSheetRow('Pipeline_Report', array_values($value));
  			}
  		}

  		$filename = 'file.xlsx';

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

		/*ob_clean();
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=file.csv");
		$output = fopen("php://output", "w");
		foreach ($data as $row)
		{
		    fputcsv($output, $row); // here you can change delimiter/enclosure
		}
		fclose($output);
		ob_flush();*/
	}

}?>
