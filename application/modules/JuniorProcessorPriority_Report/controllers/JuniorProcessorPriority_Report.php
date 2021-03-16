<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class JuniorProcessorPriority_Report extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('JuniorProcessorPriority_Report_model');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Modules'] = [];
		$data['Customer_Prioritys'] = [];
		$data['PriorityWorkflows'] = $this->JuniorProcessorPriority_Report_model->get_prioritytable_workflows();
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Modules'] = $this->JuniorProcessorPriority_Report_model->get_settingsmilestones('JuniorPriorityReport_MilestonesList');;
			$data['Customer_Prioritys'] = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function agingbucket()
	{
		$data['content'] = 'agingbucket';
		$data['Modules'] = [];
		$data['Customer_Prioritys'] = [];
		$data['AgingHeader'] = $this->config->item('PriorityAgingBucketHeader');
		$data['PriorityWorkflows'] = $this->JuniorProcessorPriority_Report_model->get_prioritytable_workflows();

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Customer_Prioritys'] = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function processor()
	{
		$data['content'] = 'processor';
		$data['Modules'] = [];
		$data['Customer_Prioritys'] = [];
		$data['Processors'] = $this->Common_Model->get_allprocessors();	
		$data['PriorityWorkflows'] = $this->JuniorProcessorPriority_Report_model->get_prioritytable_workflows();

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Customer_Prioritys'] = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


	public function teamleader()
	{
		$data['content'] = 'teamleader';
		$data['Modules'] = [];
		$data['Customer_Prioritys'] = [];
		$data['Processors'] = $this->Common_Model->get_allteamleads();
		$data['PriorityWorkflows'] = $this->JuniorProcessorPriority_Report_model->get_prioritytable_workflows();

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Customer_Prioritys'] = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function onshoreprocessor()
	{
		$data['content'] = 'onshoreprocessor';
		$data['Modules'] = [];
		$data['Workflows'] = [];
		$data['Processors'] = $this->Common_Model->get_allonshorejuniorprocessors();
		$data['PriorityWorkflows'] = $this->JuniorProcessorPriority_Report_model->get_prioritytable_workflows();


		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Customer_Prioritys'] = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


	public function onshorejuniorprocessor()
	{
		$data['content'] = 'onshorejuniorprocessor';
		$data['Modules'] = [];
		$data['Workflows'] = [];
		$data['Processors'] = $this->Common_Model->get_allonshorejuniorprocessorslead();
		$data['PriorityWorkflows'] = $this->JuniorProcessorPriority_Report_model->get_prioritytable_workflows();

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Customer_Prioritys'] = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function loantype()
	{
		$data['content'] = 'loantype';
		$data['Modules'] = [];
		$data['Customer_Prioritys'] = [];
		$data['LoanTypes'] = $this->config->item('LoanTypes');
		$data['PriorityWorkflows'] = $this->JuniorProcessorPriority_Report_model->get_prioritytable_workflows();

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$data['Customer_Prioritys'] = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		}
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


	function fetch_counts()
	{
		$post['FilterStatus'] = $this->input->post('FilterStatus');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->JuniorProcessorPriority_Report_model->get_counts($post);
		echo json_encode(['success'=>1,'data'=>$result]);
	}

	function fetch_agingcounts()
	{
		$post['FilterStatus'] = $this->input->post('FilterStatus');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->JuniorProcessorPriority_Report_model->get_priorityagingcounts($post);
		echo json_encode(['success'=>1,'data'=>$result]);
	}


	function fetch_processorcounts()
	{
		$post['FilterStatus'] = $this->input->post('FilterStatus');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->JuniorProcessorPriority_Report_model->get_priorityprocessorcounts($post);
		echo json_encode(['success'=>1,'data'=>$result]);
	}

	function fetch_teamleadcounts()
	{
		$post['FilterStatus'] = $this->input->post('FilterStatus');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->JuniorProcessorPriority_Report_model->get_priorityteamleadcounts($post);
		echo json_encode(['success'=>1,'data'=>$result]);
	}

	function fetch_onshoreprocessorcounts()
	{
		$post['FilterStatus'] = $this->input->post('FilterStatus');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->JuniorProcessorPriority_Report_model->get_priorityonshoreprocessorcounts($post);
		echo json_encode(['success'=>1,'data'=>$result]);
	}

	function fetch_onshorejuniorprocessorcounts()
	{
		$post['FilterStatus'] = $this->input->post('FilterStatus');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->JuniorProcessorPriority_Report_model->get_priorityonshorejuniorprocessorcounts($post);
		echo json_encode(['success'=>1,'data'=>$result]);
	}

	function fetch_loantypecounts()
	{
		$post['FilterStatus'] = $this->input->post('FilterStatus');
		$post['ProjectUID'] = $this->input->post('ProjectUID');
		$post['ProductUID'] = $this->input->post('ProductUID');
		$post['FromDate'] = $this->input->post('FromDate');
		$post['ToDate'] = $this->input->post('ToDate');
		$result = $this->JuniorProcessorPriority_Report_model->get_priorityloantypecounts($post);
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
		$post['Priority'] = $this->input->post('Priority');
		$post['Section'] = 'JuniorProcessorPriority_Report';

  	//column order
  	$QueueColumns = $this->Common_Model->getSectionQueuesColumns($post['Section']);
  	if (!empty($QueueColumns)) 
  	{
  		$columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
  		$post['column_order'] = $columndetails;
  		$post['column_search'] = array_filter($columndetails);
  		$post['IsDynamicColumns'] = true;
  		$post['IsDynamicColumns_Select'] = $columndetails;
  	}

		$post['column_search'] = $post['column_order'];

		$list = $this->JuniorProcessorPriority_Report_model->getOrders($post);
		$no = $post['start'];
		$returnlist = [];

		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] = "Ordersummary/index/";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous[$post['Section']] = TRUE;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $WorkflowModuleUID = '', $Mischallenous);
		if (!empty($DynamicColumns)) 
		{
			$returnlist = $DynamicColumns['orderslist'];
		}

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->JuniorProcessorPriority_Report_model->count_all($post),
			"recordsFiltered" =>  $this->JuniorProcessorPriority_Report_model->count_filtered($post),
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
		$filename = ($filename) ? str_replace(' ', '', $writer->sanitize_filename($filename)) : 'Sheet.xlsx';
		$post['Section'] = 'JuniorProcessorPriority_Report';

  	//column order
  	$QueueColumns = $this->Common_Model->getSectionQueuesColumns($post['Section']);
  	if (!empty($QueueColumns)) 
  	{
  		$columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
  		$post['column_order'] = $columndetails;
  		$post['column_search'] = array_filter($columndetails);
  		$post['IsDynamicColumns'] = true;
  		$post['IsDynamicColumns_Select'] = $columndetails;
  	}

  	$post['column_search'] = $post['column_order']  ='';

  	$list = $this->JuniorProcessorPriority_Report_model->getOrders($post);


		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');


		$Mischallenous = array();
  	$Mischallenous[$post['Section']] = TRUE;
		$Mischallenous['PageBaseLink'] = "";
		$Mischallenous['AssignButtonClass'] = "";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$excelcolumnsdata = $this->Common_Model->getGlobalExcelDynamicQueueColumns($list, $WorkflowModuleUID, $Mischallenous);
		if ( !empty($excelcolumnsdata) ) 
		{
			$header = $excelcolumnsdata['header'];
			$data = $excelcolumnsdata['orderslist'];		

			$HEADER = [];
			foreach ($header as $hkey => $head) {
				$HEADER[$head] = "string";
			}
			$writer->writeSheetHeader($filename,$HEADER, $header_style);
			foreach($data as $Order) {                     
				$writer->writeSheetRow($filename, $Order);
			}
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

	function updateprioritycomments() {
		$OrderUID = $this->input->post('OrderUID');
		$Comments = $this->input->post('Comments');
		$result = $this->JuniorProcessorPriority_Report_model->updateprioritycomments($OrderUID, $Comments);
		if ($result) {
			echo json_encode(array('error' => 0, 'msg' => 'Comments updated successfully', 'type' => 'success'));
		} else {
			echo json_encode(array('error' => 1, 'msg' => 'Failed', 'type' => 'danger'));
		}
	}

	function Update_Dynamic_Column() {
		$OrderUID = $this->input->post('OrderUID');
		$value = $this->input->post('value');
		$ColumnName = $this->input->post('ColumnName');
		$ExpirationDuration = $this->input->post('ExpirationDuration');

		$result = $this->JuniorProcessorPriority_Report_model->UpdateDynamicColumn($OrderUID, $value, $ColumnName);
		if ($result) {
			echo json_encode(array('error' => 0, 'msg' => 'Field updated successfully', 'type' => 'success'));
		} else {
			echo json_encode(array('error' => 1, 'msg' => 'Failed', 'type' => 'danger'));
		}
	}

	function fetch_reversepopup()
	{
		$OrderUID = $this->input->post('OrderUID');
		if(!empty($OrderUID)) {
			$data['Reverseform_ID'] = "frm_priorityorderreverse";
			$data['OrderDetails'] = $this->db->select('*')->from('tOrders')->where('OrderUID',$OrderUID)->get()->row();
			$data = $this->load->view('orderinfoheader/reverseworkflow',$data,true);
			echo json_encode(array('success' => 1,'data'=>$data));exit;
		}
		echo json_encode(array('success' => 0,'data'=>''));exit;

	}

	


}?>
