<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class AgingReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('AgingReportmodel');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Modules'] = [];
		$data['AgingHeader'] = $this->config->item('AgingHeader');

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Modules'] = $this->AgingReportmodel->getCustomer_Milestones($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


	function fetch_agingcounts()
	{
		$post['MilestoneUID'] = $this->input->post('MilestoneUID');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->AgingReportmodel->get_agingcounts($post);
		echo json_encode(['success'=>1,'data'=>$result]);
	}

	
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

  	//get_post_input_data
  	//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

    //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

		$list = $this->AgingReportmodel->getOrders($post);

		$no = $post['start'];
		$returnlist = [];
		foreach ($list as $myorders)
		{
			$row = array();
			$row[] = $myorders->OrderNumber;
			$row[] = $myorders->CustomerName;
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->MilestoneName;
			$row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->PropertyStateCode;
			$row[] = site_datetimeaging($myorders->EntryDatetime);
			$row[] = site_datetimeformat($myorders->DueDateTime);
			$row[] = site_datetimeformat($myorders->LastModifiedDateTime);

			$Action = '<a href="Ordersummary/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'" target="_new"><i class="icon-pencil"></i></a>';

			$row[] = $Action;
			$returnlist[] = $row;
		}

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->AgingReportmodel->count_all($post),
			"recordsFiltered" =>  $this->AgingReportmodel->count_filtered($post),
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
		$filename = ($filename) ? $filename : 'Sheet.xlsx';
		$list = $this->AgingReportmodel->getOrders($post,'');
		$ExcelHeader = array('Order No'=>'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','Aging'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string');
		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

		$writer->writeSheetHeader($filename, $ExcelHeader, $header_style);

		foreach($list as $Order) {
			$Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
			$writer->writeSheetRow($filename, array_values($Exceldataset));
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
