<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class ExceptionQueue_Orders extends MY_Controller {

  protected $WorkflowModuleUID = NULL;
	function __construct()
	{
		parent::__construct();
		$this->load->model('ExceptionQueue_Orders_model');
	}


    /**
            *Function Description: ExceptionQueue_Orders
            *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
            *@since Date APR 25 2020
            */

    public function ExceptionQueue_Orders_ajax()
    {
        
    // ini_set("display_errors", 1);
    // error_reporting(E_ALL);
        $post = $this->input->post();
        if(isset($post['ReportType']))
        {
         
          $post['OrderUID']=$this->input->post('formData')['OrderUID'];
        }
        $formData = $this->input->post('formData'); 
        $post['advancedsearch'] = $this->input->post('formData');
        $search = $this->input->post('search');
        $post['search_value'] = trim($search['value']);

        $WorkflowModulePages = [
          $this->config->item("Workflows")["PreScreen"] => "PreScreen",
          $this->config->item("Workflows")["TitleTeam"] => "TitleTeam",
          $this->config->item("Workflows")["FHAVACaseTeam"] => "FHA_VA_CaseTeam",
          $this->config->item("Workflows")["HOI"] => "HOI",
          $this->config->item("Workflows")["ThirdPartyTeam"] => "ThirdPartyTeam",
          $this->config->item("Workflows")["Workup"] => "Workup",
          $this->config->item("Workflows")["BorrowerDoc"] => "BorrowerDoc",
          $this->config->item("Workflows")["ICD"] => "ICD",
          $this->config->item("Workflows")["Disclosures"] => "Disclosures",
          $this->config->item("Workflows")["NTB"] => "NTB",
          $this->config->item("Workflows")["FloodCert"] => "FloodCert",
          $this->config->item("Workflows")["Appraisal"] => "Appraisal",
          $this->config->item("Workflows")["Escrows"] => "Escrows",
          $this->config->item("Workflows")["TwelveDayLetter"] => "TwelveDayLetter",
          $this->config->item("Workflows")["MaxLoan"] => "MaxLoan",
          $this->config->item("Workflows")["POO"] => "POO",
          $this->config->item("Workflows")["CondoQR"] => "CondoQR",
          $this->config->item("Workflows")["FHACaseAssignment"] => "FHACaseAssignment",
          $this->config->item("Workflows")["VACaseAssignment"] => "VACaseAssignment",
          $this->config->item("Workflows")["VVOE"] => "VVOE",
          $this->config->item("Workflows")["CEMA"] => "CEMA",
          $this->config->item("Workflows")["SCAP"] => "SCAP",
          $this->config->item("Workflows")["NLR"] => "NLR",
          $this->config->item("Workflows")["CTCFlipQC"] => "CTCFlipQC",
          $this->config->item("Workflows")["PrefundAuditCorrection"] => "PrefundAuditCorrection",
          $this->config->item("Workflows")["AdhocTasks"] => "AdhocTasks",
          $this->config->item("Workflows")["UWClear"] => "UWClear",
          $this->config->item("Workflows")["TitleReview"] => "TitleReview",
          $this->config->item("Workflows")["WelcomeCall"] => "WelcomeCall",
          $this->config->item("Workflows")["PayOff"] => "PayOff",
          $this->config->item("Workflows")["BorrowerDocs"] => "BorrowerDocs",
          $this->config->item("Workflows")["GateKeeping"] => "GateKeeping",
          $this->config->item("Workflows")["Submissions"] => "Submissions",
          $this->config->item("Workflows")["CD"] => "CD",
          $this->config->item("Workflows")["Scheduling"] => "Scheduling",
          $this->config->item("Workflows")["DocsOut"] => "DocsOut",
          $this->config->item("Workflows")["FundingConditions"] => "FundingConditions",
          $this->config->item("Workflows")["SignedDocs"] => "SignedDocs",
          $this->config->item("Workflows")["InitialUnderWriting"] => "InitialUnderWriting",
          $this->config->item("Workflows")["ConditionwithApproval"] => "ConditionwithApproval",
          $this->config->item("Workflows")["Underwriting"] => "Underwriting",
        ];

        $mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$post['QueueUID']]);
        $this->WorkflowModuleUID = $mQueues->WorkflowModuleUID;

        $WorkflowPage = $WorkflowModulePages[$this->WorkflowModuleUID];

        if (empty($WorkflowPage)) 
        {
          $WorkflowPage = "TitleTeam";
        }


   	//column order
		$post['column_order'] = array('tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','b.UserName','tOrderQueues.RaisedRemarks','tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');
    //column search
		$post['column_search'] = array('tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','b.UserName','tOrderQueues.RaisedRemarks','tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

        /**
        *Function Description: Dynamic Columns Queues
        *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
        *@since 14.5.2020
        */
         /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, '', $post['QueueUID']);
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }

       

        $rows = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'data');
         $totalorders = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post,'count_all');

        $myorderslist = [];



      /**
        *Function Description: Dynamic Columns Queues
        *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
        *@since 14.5.2020
        */
      /* ****** Dynamic Queues Section Starts ****** */
        $Mischallenous['PageBaseLink'] = $WorkflowPage . '/index/';
        $Mischallenous['AssignButtonClass'] = "";
        $Mischallenous['QueueColumns'] = $QueueColumns;
        $Mischallenous['AssignButtonClass'] = $this->config->item('WorkflowDetails')[$this->WorkflowModuleUID]['AssignButtonClass'];
        $DynamicColumns = $this->Common_Model->getExceptionDynamicQueueColumns($rows, $this->WorkflowModuleUID, $Mischallenous);

        if (!empty($DynamicColumns)) 
        {
          $myorderslist            =   $DynamicColumns['orderslist'];
          $post['column_order']    =   $DynamicColumns['column_order'];
          $post['column_search']   =   $DynamicColumns['column_search'];
          $rows = [];
        }
      /* ****** Dynamic Queues Section Ends ****** */


        foreach ($rows as $myorders)
        {


          $row = array();
          $row[] = $myorders->OrderNumber;
          $row[] = $myorders->CustomerName;
          $row[] = $myorders->LoanNumber;
          $row[] = $myorders->LoanType;
          $row[] = $myorders->MilestoneName;
          $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
          $row[] = site_datetimeaging($myorders->EntryDatetime);
          $row[] = site_datetimeformat($myorders->DueDateTime);
          $row[] = site_datetimeformat($myorders->LastModifiedDateTime);
          $row[] = $myorders->RaisedBy;
          $row[] = $myorders->RaisedRemarks;
          $row[] = site_datetimeformat($myorders->RaisedDateTime);

          $FollowUp = ''; 
          if(!empty($myorders->FollowUpUID)) {
            $FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
          }

          $Action = '<a href="'.$WorkflowPage.'/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';

          $row[] = $Action;
          $myorderslist[] = $row;
        }


        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $totalorders,
            "recordsFiltered" => $totalorders,
            "data" => $myorderslist,
        );
        if (isset($post['search']['value']) &&  $post['search']['value'] !='') {
          $output['recordsFiltered']=$this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'count_filter');
        }
      unset($post);
      unset($data);
      echo json_encode($output);        

    }

    public function ExceptionQueue_table_rows($data)
    {

        $WorkflowModulePages = [
          $this->config->item("Workflows")["PreScreen"] => "PreScreen",
          $this->config->item("Workflows")["TitleTeam"] => "TitleTeam",
          $this->config->item("Workflows")["FHAVACaseTeam"] => "FHA_VA_CaseTeam",
          $this->config->item("Workflows")["HOI"] => "HOI",
          $this->config->item("Workflows")["ThirdPartyTeam"] => "ThirdPartyTeam",
          $this->config->item("Workflows")["Workup"] => "Workup",
          $this->config->item("Workflows")["BorrowerDoc"] => "BorrowerDoc",
      ];


    	$myorderslist = [];
    	foreach ($data as $myorders)
    	{
          $WorkflowPage = $WorkflowModulePages[$myorders->WorkflowModuleUID];

          if (empty($WorkflowPage)) {
              $WorkflowPage = "TitleTeam";
          }

          $row = array();
    		$row[] = $myorders->OrderNumber;
    		$row[] = $myorders->CustomerName;
    		$row[] = $myorders->LoanNumber;
    		$row[] = $myorders->LoanType;
    		$row[] = $myorders->MilestoneName;
    		$row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
    		$row[] = site_datetimeaging($myorders->EntryDatetime);
    		$row[] = site_datetimeformat($myorders->DueDateTime);
    		$row[] = site_datetimeformat($myorders->LastModifiedDateTime);
    		$row[] = $myorders->RaisedBy;
    		$row[] = $myorders->RaisedRemarks;
        $row[] = site_datetimeformat($myorders->RaisedDateTime);

         $Action = '<a href="'.$WorkflowPage.'/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i></a>';
         /*if($this->loggedid == $myorders->AssignedToUserUID)
    		{
    		}else{

    			$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs TitleTeamPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i></button>';
    		}*/
    		$row[] = $Action;
    		$myorderslist[] = $row;
    	}
    	return $myorderslist;

    }


	function outputCSV($data) 
	{
		ob_clean();
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=file.csv");
		$output = fopen("php://output", "w");
		foreach ($data as $row)
		{
			fputcsv($output, $row); // here you can change delimiter/enclosure
		}
		fclose($output);
		ob_flush();
	}


	function WriteExcel()
	{

		/*@Author Parthasarathy <parthasarathy.m@avanzegroup.com> @Updated APR 25 2020*/
		$post = $this->input->post();
		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}

    $mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$post['QueueUID']]);
    $this->WorkflowModuleUID = $mQueues->WorkflowModuleUID;

		$orders = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'data');

		$data = [];
		$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','Raised By','Remarks','Aging','DueDateTime','LastModifiedDateTime');

    /**
    *Function Description: Dynamic Columns Queues
    *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
    *@since 14.5.2020
    */

    /* ****** Dynamic Queues Section Starts ****** */
    $Mischallenous['PageBaseLink'] = "TitleTeam/index/";
    $Mischallenous['AssignButtonClass'] = "TitleTeamPickNewOrder";
    $QueueColumns = $this->Common_Model->getExceptionExcelDynamicQueueColumns($orders, $this->WorkflowModuleUID, $Mischallenous);


    if ( !empty($QueueColumns) ) 
    {
      $data = $QueueColumns['orderslist'];
      $orders = [];
    }

    /* ****** Dynamic Queues Section Ends ****** */

    /*------ Automaticalled skipped when top if succeded --------*/
		for ($i=0; $i < sizeof($orders); $i++) { 
			$data[] = array($orders[$i]->OrderNumber,$orders[$i]->CustomerName, $orders[$i]->LoanNumber,$orders[$i]->LoanType,$orders[$i]->MilestoneName,$orders[$i]->StatusName,$orders[$i]->RaisedBy,$orders[$i]->RaisedRemarks,$orders[$i]->Aging,site_datetimeformat($orders[$i]->DueDateTime),site_datetimeformat($orders[$i]->LastModifiedDateTime));				
		}

		$this->outputCSV($data);
		/*End*/

	}

  /**
  *Function function to  update IsQueueReceived columns in subqueues enabled 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Wednesday 15 July 2020.
  */

  function update_IsQueueReceived()
  {
    $OrderUID = $this->input->post('OrderUID');
    $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
    $QueueUID = $this->input->post('QueueUID');
    $ischecked = $this->input->post('ischecked');
    $response = array('success' => 2,'message'=>$this->lang->line('SubQueues_IsReceived_Failed'));

    if($OrderUID && $WorkflowModuleUID && $QueueUID) {

      $Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);    
      $mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

      $this->db->where(array('OrderUID'=>$OrderUID,'QueueUID'=>$QueueUID));
      $this->db->update('tOrderQueues', array('QueueIsDocsReceived'=>$ischecked));

      if ($this->db->affected_rows() > 0) {

        $Description = sprintf($this->lang->line('SubQueues_IsReceived_Success'), $Workflow->WorkflowModuleName, $mQueues->QueueName);
        //Insert Log
        $this->Common_Model->OrderLogsHistory($OrderUID,$Description,date('Y-m-d H:i:s'));

        $response = array('success' => 1,'message'=>$Description);
      } else {
        $response = array('success' => 2,'message'=>$this->lang->line('SubQueues_IsReceived_Failed'));
      }

    }

    $this->output->set_content_type('application/json');$this->output->set_output(json_encode($response))->_display();exit;

  }

  /**
  *Function function to  update IsQueueReceived columns in subqueues enabled 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Wednesday 15 July 2020.
  */

  function update_IsQueueStatus()
  {
    $OrderUID = $this->input->post('OrderUID');
    $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
    $QueueUID = $this->input->post('QueueUID');
    $QueueIsStatus = $this->input->post('QueueIsStatus');
    $response = array('success' => 2,'message'=>$this->lang->line('SubQueues_IsReceived_Failed'));

    if($OrderUID && $WorkflowModuleUID && $QueueUID) {

      $Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);    
      $mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

      $this->db->where(array('OrderUID'=>$OrderUID,'QueueUID'=>$QueueUID));
      $this->db->update('tOrderQueues', array('QueueIsStatus'=>$QueueIsStatus));

      if ($this->db->affected_rows() > 0) {

        $Description = sprintf($this->lang->line('SubQueues_IsStatus_Success'), $Workflow->WorkflowModuleName, $mQueues->QueueName);
        //Insert Log
        $this->Common_Model->OrderLogsHistory($OrderUID,$Description,date('Y-m-d H:i:s'));

        $response = array('success' => 1,'message'=>$Description);
      } else {
        $response = array('success' => 2,'message'=>$this->lang->line('SubQueues_IsStatus_Failed'));
      }

    }

    $this->output->set_content_type('application/json');$this->output->set_output(json_encode($response))->_display();exit;
  }

  /**
  *Function function to fetch IsQueueReceived columns in subqueues enabled 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Wednesday 15 July 2020.
  */
  function fetch_docreceivedcounts(){
    $QueueUID = $this->input->post('QueueUID');
    if(!empty($QueueUID)) {

      $Queue = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

      $SubQueues_IsDocsReceivedcount = 0;

      if(!empty($Queue)) {
        $post['advancedsearch']['SubQueues_DocsReceive_Enabled'] = 'true';
        $post['QueueUID'] = $QueueUID;

        if(!empty($QueueUID)) {
          $SubQueues_IsDocsReceivedcount = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'count_filter');
        }

        $this->output->set_content_type('application/json');$this->output->set_output(json_encode(array('SubQueues_IsDocsReceivedcount' => $SubQueues_IsDocsReceivedcount)))->_display();exit;

      }
    }

    $this->output->set_content_type('application/json');$this->output->set_output(json_encode(array('SubQueues_IsDocsReceivedcount' => 0)))->_display();exit;
  }

  /**
  *Function function to fetch IsQueueReceived columns in subqueues enabled 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Wednesday 15 July 2020.
  */
  function fetch_statuscounts(){
    $QueueUID = $this->input->post('QueueUID');
    if(!empty($QueueUID)) {

      $Queue = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

      $SubQueues_IsStatuscount = 0;

      if(!empty($Queue)) {
        $post['advancedsearch']['SubQueues_Status_Enabled'] = 'true';
        $post['QueueUID'] = $QueueUID;

        if(!empty($QueueUID)) {
          $SubQueues_IsStatuscount = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'count_filter');
        }

        $this->output->set_content_type('application/json');$this->output->set_output(json_encode(array('SubQueues_IsStatuscount' => $SubQueues_IsStatuscount)))->_display();exit;

      }
    }

    $this->output->set_content_type('application/json');$this->output->set_output(json_encode(array('SubQueues_IsStatuscount' => 0)))->_display();exit;
  }


}?>
