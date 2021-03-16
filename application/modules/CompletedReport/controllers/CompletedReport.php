<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class CompletedReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('CompletedReportmodel');
		$this->load->model('Common_Model');
		$this->load->model('Customer/Customer_Model');
		//load xlsx library
		include APPPATH . 'third_party/xlsxwriter.class.php';
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['ProcessUsers'] = $this->CompletedReportmodel->getProcessUsers(array());
		$data['date'] = $this->monthFirstLastDay();
		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details();

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
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
	function getCompletedReportHead(){
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');

		$workflows = $this->config->item('Workflows'); 
		$WorkflowModuleUID = $post['advancedsearch']['workflow'];

		$post['WorkflowModuleUID'] = $WorkflowModuleUID;
		$module = array_search($WorkflowModuleUID,$workflows,true);
		if(empty($module)){
			$module = $workflows[0];
		}
		$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
// echo "<pre>";print_r($QueueColumns);exit;
		if (!empty($QueueColumns)) 
		{
			$data['status'] = 1; 

			$data['html'] = $QueueColumns;
		}else{
			$data['status'] = 0;
		}
		echo json_encode($data);	
	}
	function getCompletedReport()
	{
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');

		$workflows = $this->config->item('Workflows'); 
		$WorkflowModuleUID = $post['advancedsearch']['workflow'];

		$post['WorkflowModuleUID'] = $WorkflowModuleUID;
		$module = array_search($WorkflowModuleUID,$workflows,true);
		if(empty($module)){
			$module = $workflows[0];
		}
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime','mUsers.UserName','tOrderAssignments.CompleteDateTime');

            //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime','mUsers.UserName','tOrderAssignments.CompleteDateTime');
		/* ****** Dynamic Queues Section Starts ****** */
		$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);

		if (!empty($QueueColumns)) 
		{
			$columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
			$post['column_order'] = $columndetails;
			$post['column_search'] = array_filter($columndetails);

		}

		$data['ProcessUsers'] = $this->CompletedReportmodel->getProcessUsers($this->input->post('Process'));
		$CompletedReports = $this->CompletedReportmodel->getCompletedReport($post);

		$no = $post['start'];
		$completedrecord = [];

		/* ****** Dynamic Queues Section ****** */
			$Mischallenous['PageBaseLink'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['PageBaseLink'];
			$Mischallenous['AssignButtonClass'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['AssignButtonClass'];
			$Mischallenous['QueueColumns'] = $QueueColumns;
			$Mischallenous['IsCompleted'] = true;
			$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($CompletedReports, $WorkflowModuleUID, $Mischallenous);

			if (!empty($DynamicColumns)) 	
			{
				$completedrecord			= 	$DynamicColumns['orderslist'];
				$post['column_order']		=	$DynamicColumns['column_order'];
				$post['column_search']		=	$DynamicColumns['column_search'];
				$CompletedReports = [];
			}
			/* ****** Dynamic Queues Section Ends ****** */

			foreach ($CompletedReports as $records)
			{
				$Actiondata = '<a href="'.base_url($module.'/index/'.$records->OrderUID).'" target="_blank" class="ajaxload"><i class="icon-pencil"></i></a>';

				if($records->ReWorkBy != '' && $records->CompletedBy != '' && $records->ReWorkBy != $records->CompletedBy){
					$completedDate = $records->CompleteDateTime;
					$AssignedDatetime = $records->AssignedDatetime;

					$records->CompletedBy = '';
					$records->IsRework = 'Yes';
					// $CompletedReports[] = $records;

				}else if(isset($records->CompletedBy) && $records->CompletedBy != ''){
					$completedDate = $records->CompleteDateTime;
					$AssignedDatetime = $records->AssignedDatetime;
				}else{		
					$records->IsRework = 'Yes';		
					$completedDate = $records->ReWorkDateTime;
					$AssignedDatetime = $records->ReworkAssignedDatetime;
				}
				$row = array();

				$row[] = $records->OrderNumber;	
				$row[] = $records->IsRework;  	        
				$row[] = $records->LoanNumber;		        
				$row[] = $records->LoanType;		        
				$row[] = site_datetimeformat($records->OrderEntryDateTime);
				$row[] = $AssignedDatetime;		        
				$row[] = $records->UserName;
				$row[] = $completedDate;       
				$Action = '<div style="display: inline-flex;">'.$Actiondata.'</div>';
				$row[] = $Action;
				$completedrecord[] = $row;
			}
			$data =  array(
				'completedrecord' => $completedrecord,
				'post' => $post
			);
			$post = $data['post'];
			$output = array(
				"draw" => $post['draw'],
				"column" => $post['column_order'],
				"recordsTotal" => $this->CompletedReportmodel->count_all($post),
				"recordsFiltered" =>  $this->CompletedReportmodel->count_filtered($post),
				"data" => $data['completedrecord'],
			);

			unset($post);
			unset($data);
			echo json_encode($output);
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
		}else if($period == 'year')
		{
			$first_date = date('m/d/Y',strtotime('1/1'));
			$last_date = date('m/d/Y',strtotime('12/31'));
			echo  json_encode(array('fromDate'=>$first_date,'toDate'=>$last_date));
		}
	}
	function WriteExcel()
	{
		$post = $this->input->post();

		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
		}
		else{
			$post['advancedsearch'] = $this->input->post('formData');
		}
		$post['length'] = '';

		$workflows = $this->config->item('Workflows'); 
		$WorkflowModuleUID = $post['advancedsearch']['workflow'];

		$post['WorkflowModuleUID'] = $WorkflowModuleUID;
		$module = array_search($WorkflowModuleUID,$workflows,true);
		if(empty($module)){
			$module = $workflows[0];
		}
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime','mUsers.UserName','tOrderAssignments.CompleteDateTime');

            //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime','mUsers.UserName','tOrderAssignments.CompleteDateTime');
		
		$CompletedReports = $this->CompletedReportmodel->getCompletedReport($post);


		$data = [];
		$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','State','Aging','Due Date Time','Last Modified Date Time');

		$Mischallenous['PageBaseLink'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['PageBaseLink'];
		$Mischallenous['AssignButtonClass'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['AssignButtonClass'];
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['IsCompleted'] = true;
		$Mischallenous['IsCompletedReport'] = true;
		
		$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($CompletedReports, $WorkflowModuleUID, $Mischallenous);


		if ( !empty($QueueColumns) ) 
		{
			$data = $QueueColumns['orderslist'];
			$CompletedReports = [];
		}


		for ($i=0; $i < sizeof($CompletedReports); $i++) { 
				$data[] = array($CompletedReports[$i]->OrderNumber,$CompletedReports[$i]->CustomerName,$CompletedReports[$i]->LoanNumber,$CompletedReports[$i]->LoanType,$CompletedReports[$i]->MilestoneName,$CompletedReports[$i]->StatusName,$CompletedReports[$i]->PropertyStateCode,site_datetimeaging($CompletedReports[$i]->EntryDatetime),site_datetimeformat($CompletedReports[$i]->DueDateTime),site_datetimeformat($CompletedReports[$i]->LastModifiedDateTime));				
		}

		$this->outputCSV($data);

	}

	function outputCSV($data) 
	{
		ob_clean();
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=file.csv");
		$output = fopen("php://output", "w");
		foreach ($data as $row)
		{
			fputcsv($output, $row);
		}
		fclose($output);
		ob_flush();
	}
	/**
	*Function return users based on workflow in completed report
	*@author Santhiya <santhiya.m@avanzegroup.com>
	*@since Thursday 9 September 2020.
	*/
	function AppendUsers(){
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$WorkflowUsers = $this->Common_Model->CompletedUsersByWorkflowModule($WorkflowModuleUID);
		$select = '<select class="processUser form-control mdb-select" id="adv_Process"  name="Process" multiple="true" placeholder="Select User(s)">';
		foreach ($WorkflowUsers as $key => $value) {
			$select .= '<option value="'.$value->UserUID.'" >'.$value->UserName.'</option>';
		}
		$select .= '</select>';

		echo $select;
	}
}?>
