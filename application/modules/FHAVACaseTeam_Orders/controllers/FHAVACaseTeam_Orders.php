<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FHAVACaseTeam_Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('FHAVACaseTeam_Orders_Model');
		$this->WorkflowModuleUID = $this->config->item('Workflows')['FHAVACaseTeam'];
	}

	public function index()
	{
		$data['content']='index';
		$data['is_selfassign'] = 1;
		$data['IsGetNextOrder'] = $this->FHAVACaseTeam_Orders_Model->CheckAutoAssignEnabled($this->loggedid);
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$data['IsParking'] = $this->Common_Model->is_parking_enabledforworkflow($this->config->item('Workflows')['FHAVACaseTeam']);
		$data['IsKickBack'] = $this->Common_Model->is_kickback_enabledforworkflow($this->config->item('Workflows')['FHAVACaseTeam']);
		$data['IsExpiryOrders'] = $this->Common_Model->is_IsExpiryOrdersRequire_enabledforworkflow($this->config->item('Workflows')['FHAVACaseTeam']);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function neworders_ajax_list()
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
    	//get_post_input_data
    	//column order
        $post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

    //column search
        $post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

        /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }
        /* ****** Dynamic Queues Section Ends ****** */

        $list = $this->FHAVACaseTeam_Orders_Model->FHAVACaseTeamOrders($post,'');


        $no = $post['start'];
        $myorderslist = [];

        /* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "FHA_VA_CaseTeam/index/";
		$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$myorderslist 				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->CustomerName;
/*
		        $row[] = $myorders->ProductName;
		        $row[] = $myorders->ProjectName;
		        $row[] = $myorders->DocTypeName;
*/		        $row[] = $myorders->LoanNumber;
		        $row[] = $myorders->LoanType;
		        $row[] = $myorders->MilestoneName;
		        //$row[] = $myorders->LenderName;
		        $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
		        $row[] = $myorders->PropertyStateCode;
		        $row[] = site_datetimeaging($myorders->EntryDatetime);
		        $row[] = site_datetimeformat($myorders->DueDateTime);
		        $row[] = site_datetimeformat($myorders->LastModifiedDateTime);
		        // $row[] = $myorders->PropertyCityName;
		        // $row[] = $myorders->PropertyStateCode;
		        // $row[] = $myorders->PropertyZipCode;
				
/*
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderDueDate));
*/
				$FollowUp = '';	
				if(!empty($myorders->FollowUpUID)) {
					$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
				}

		        if($this->loggedid == $myorders->AssignedToUserUID)
		        {
		        	$Action = '<a href="FHA_VA_CaseTeam/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
		        }else{

		       		$Action = '<button class="btn btn-info btn-sm FHAPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="fa fa-hand-o-up" aria-hidden="true"></i>'.$FollowUp.'</button>';
		        }
		        $row[] = $Action;
		        $myorderslist[] = $row;
        }



        $data =  array(
        	'myorderslist' => $myorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->FHAVACaseTeam_Orders_Model->count_all($post),
			"recordsFiltered" =>  $this->FHAVACaseTeam_Orders_Model->count_filtered($post,''),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function workinginprogress_ajax_list()
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
        $post['ReportType'] = $this->input->post('ReportType');
		$post['UserUID'] = $this->input->post('UserUID');
    	//get_post_input_data
    	//column order
        $post['column_order'] = array('tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.PropertyStateCode','mUsers.UserName','tOrderAssignments.AssignedDateTime','tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');
		//column search
        $post['column_search'] = array('tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.PropertyStateCode','mUsers.UserName','tOrderAssignments.AssignedDateTime','tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

        /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }
        /* ****** Dynamic Queues Section Ends ****** */

        //column order
        $list = $this->FHAVACaseTeam_Orders_Model->WorkInProgressOrders($post,'');


        $no = $post['start'];
        $workinginprogresslist = [];

        /* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "FHA_VA_CaseTeam/index/";
		$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$workinginprogresslist		= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->CustomerName;
/*
		        $row[] = $myorders->ProductName;
		        $row[] = $myorders->ProjectName;
		        $row[] = $myorders->DocTypeName;
*/		        $row[] = $myorders->LoanNumber;
		        $row[] = $myorders->LoanType;
		        $row[] = $myorders->MilestoneName;
		        //$row[] = $myorders->LenderName;
		        $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
		        $row[] = $myorders->PropertyStateCode;
		        $row[] = $myorders->AssignedUserName;
		        $row[] = site_datetimeformat($myorders->AssignedDateTime);
		        $row[] = site_datetimeaging($myorders->EntryDatetime);
		        $row[] = site_datetimeformat($myorders->DueDateTime);
		        $row[] = site_datetimeformat($myorders->LastModifiedDateTime);
		        // $row[] = $myorders->PropertyCityName;
		        // $row[] = $myorders->PropertyStateCode;
		        // $row[] = $myorders->PropertyZipCode;
				
/*
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderDueDate));
*/
				if(empty($post['ReportType'])){
				$FollowUp = '';	
				if(!empty($myorders->FollowUpUID)) {
					$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
				}

		        if($this->loggedid == $myorders->AssignedToUserUID)
		        {
		        	$Action = '<a href="FHA_VA_CaseTeam/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
		        }else{

		       		$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs FHAPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</button>';
		        }
		        $row[] = $Action;
		    	}
		        $workinginprogresslist[] = $row;
        }



         $data =  array(
        	'workinginprogresslist' => $workinginprogresslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->FHAVACaseTeam_Orders_Model->inprogress_count_all($post),
			"recordsFiltered" =>  $this->FHAVACaseTeam_Orders_Model->inprogress_count_filtered($post),
			"data" => $data['workinginprogresslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function myorders_ajax_list()
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
    	//get_post_input_data
    	//column order and search
        $post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');
   			 //column search
        $post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

        /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }
        /* ****** Dynamic Queues Section Ends ****** */

        $list = $this->FHAVACaseTeam_Orders_Model->MyOrders($post,'');


        $no = $post['start'];
        $myorderslist = [];

        /* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "FHA_VA_CaseTeam/index/";
		$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$myorderslist 				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->CustomerName;
/*
		        $row[] = $myorders->ProductName;
		        $row[] = $myorders->ProjectName;
		        $row[] = $myorders->DocTypeName;
*/		        $row[] = $myorders->LoanNumber;
		        $row[] = $myorders->LoanType;
		        $row[] = $myorders->MilestoneName;
		        //$row[] = $myorders->LenderName;
		        $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
		        $row[] = $myorders->PropertyStateCode;
		        $row[] = site_datetimeaging($myorders->EntryDatetime);
		        $row[] = site_datetimeformat($myorders->DueDateTime);
		        $row[] = site_datetimeformat($myorders->LastModifiedDateTime);
		        // $row[] = $myorders->AssignedUserName;
		        // $row[] = $myorders->PropertyCityName;
		        // $row[] = $myorders->PropertyStateCode;
		        // $row[] = $myorders->PropertyZipCode;
				
/*
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderDueDate));
*/
				$FollowUp = '';	
				if(!empty($myorders->FollowUpUID)) {
					$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
				}

		        if($this->loggedid == $myorders->AssignedToUserUID)
		        {
		        	$Action = '<a href="FHA_VA_CaseTeam/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
		        }else{

		       		$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs FHAPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</button>';
		        }
		        $row[] = $Action;
		        $myorderslist[] = $row;
        }



         $data =  array(
        	'wmyorderslist' => $myorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->FHAVACaseTeam_Orders_Model->myorders_count_all($post),
			"recordsFiltered" =>  $this->FHAVACaseTeam_Orders_Model->myorders_count_filtered($post),
			"data" => $data['wmyorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

function parkingorders_ajax_list()
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
    //get_post_input_data
   	//column order
		$post['column_order'] = array('tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrderParking.Remainder','b.UserName','tOrderParking.Remarks','tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');
    //column search
		$post['column_search'] = array('tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrderParking.Remainder','b.UserName','tOrderParking.Remarks','tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

		/* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }
        /* ****** Dynamic Queues Section Ends ****** */

		$list = $this->FHAVACaseTeam_Orders_Model->parkingorders($post,'');


		$no = $post['start'];
		$myorderslist = [];

		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "FHA_VA_CaseTeam/index/";
		$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['IsParkingQueue'] = true;
		$Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$myorderslist 				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

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
			$row[] = $myorders->RaisedBy;
			$row[] = $myorders->Remarks;
			$row[] = site_datetimeformat($myorders->Remainder);
			$row[] = site_datetimeaging($myorders->EntryDatetime);
			$row[] = site_datetimeformat($myorders->DueDateTime);
			$row[] = site_datetimeformat($myorders->LastModifiedDateTime);

			$FollowUp = '';	
			if(!empty($myorders->FollowUpUID)) {
				$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
			}
				
			if($this->loggedid == $myorders->AssignedToUserUID)
			{
				$Action = '<a href="FHA_VA_CaseTeam/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
			}else{

				$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs FHAPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</button>';
			}
			$row[] = $Action;
			$myorderslist[] = $row;
		}



		$data =  array(
			'myorderslist' => $myorderslist,
			'post' => $post
		);



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->FHAVACaseTeam_Orders_Model->parkingorders_count_all($post),
			"recordsFiltered" =>  $this->FHAVACaseTeam_Orders_Model->parkingorders_count_filtered($post),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}
	/*
	 * Function to Expired Order Ajax get expired orders
	 * @throws no exception
	 * @return Array
	 * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
	 * @since July 23 2020
	 */
	  
	function Expiredorders_ajax_list(){
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
  	//get_post_input_data
  	//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

    //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

		        /**
        *Function Description: Dynamic Columns Queues
        *@author Sathis Kannan <sathish.kannan@avanzegroup.com>
        *@since 23.7.2020
        */
         /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }



		$list = $this->FHAVACaseTeam_Orders_Model->ExpiredOrders($post,'');

		$no = $post['start'];
		$expiredlist = [];



		/**
        *Function Description: Dynamic Columns Queues
        *@author Sathis Kannan <sathish.kannan@avanzegroup.com>
        *@since 23.7.2020
        */
		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "FHA_VA_CaseTeam/index/";
		$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
		$Mischallenous['IsExpiryOrdersQueue'] = TRUE;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$expiredlist 				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

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

			$FollowUp = '';	
			if(!empty($myorders->FollowUpUID)) {
				$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
			}

			if($this->loggedid == $myorders->AssignedToUserUID)
			{
				$Action = '<a href="PreScreen/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
			}else{

				$Action = '<button class="btn btn-info btn-sm PreScreenPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="fa fa-hand-o-up" aria-hidden="true"></i>'.$FollowUp.'</button>';
			}
			$row[] = $Action;
			$expiredlist[] = $row;
		}



		$data =  array(
			'orderslist' => $expiredlist,
			'post' => $post
		);



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->FHAVACaseTeam_Orders_Model->Expiredcount_all($post),
			"recordsFiltered" =>  $this->FHAVACaseTeam_Orders_Model->Expiredcount_filtered($post,''),
			"data" => $data['orderslist'],
		);


		unset($post);
		unset($data);

		echo json_encode($output);
	}


	function GetNextOrder()
	{
		$ProjectUID = $this->input->post('ProjectUID');
		$Workflow = $this->input->post('Workflow');
		$this->load->library('form_validation');
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ProjectUID', '', 'required');
			$this->form_validation->set_rules('Workflow', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			if ($this->form_validation->run() == true)
			{

					$existingarray = [];
					$ExistingOrders = $this->FHAVACaseTeam_Orders_Model->CheckExistingOrders($this->loggedid,$ProjectUID,$Workflow);

					foreach ($ExistingOrders as $key => $value) {
						 if($value->StatusUID != 100)
						 {
						 	$existingarray[] = 'Not Completed';
						 }
					}
					if(sizeof($existingarray) > 0)
					{
						$result = array('validation_error' => 2,'message'=>'Existing Orders Not Completed','color'=>'danger');
						echo json_encode($result);

					}
					else
					{
						
						$data = $this->FHAVACaseTeam_Orders_Model->AssignOrders($this->loggedid,$ProjectUID,$Workflow);

						if($data == 0)
						{	
							$result = array('validation_error' => 2,'message'=>'No Orders found','color'=>'danger');
							echo json_encode($result);

						}else{
							$result = array('validation_error' => 2,'message'=>'Order Assigned','color'=>'success');
							echo json_encode($result);
						}

					}
			}else{

				$Msg = $this->lang->line('Empty_Validation');
				$formvalid = [];

				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'ProjectUID' => form_error('ProjectUID'),
					'Workflow' => form_error('Workflow'),
					'color' => 'danger',
					
				);
				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
	    }
	}

	function PickExistingOrderCheck()
	{
		$OrderUID = $this->input->post('OrderUID');
		$ProjectUID = $this->input->post('ProjectUID');
		$result = $this->FHAVACaseTeam_Orders_Model->PickExistingOrderCheck($OrderUID);
		if($result['Status'] == 1)
		{
			$res = array('validation_error' => 1,'message'=>'Order was picked by ' .$result['UserName'],'color'=>'danger');
			echo json_encode($res);exit();
		}
		else
		{
			$value = $this->FHAVACaseTeam_Orders_Model->OrderAssign($OrderUID,$ProjectUID);
			if($value)
			{
				$val = array('validation_error' => 2,'message'=>'Order Assigned','color'=>'success');
			    echo json_encode($val);exit();
			}
		}
	}


	function KickBackorders_ajax_list()
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
    	//get_post_input_data
    	//column order
        $post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

    //column search
        $post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

        /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }
        /* ****** Dynamic Queues Section Ends ****** */

        $list = $this->FHAVACaseTeam_Orders_Model->KickBackFHAVACaseTeamOrders($post,'');


        $no = $post['start'];
        $myorderslist = [];

        /* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "FHA_VA_CaseTeam/index/";
		$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['IsKickBackQueue'] = true;
		$Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$myorderslist 				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->CustomerName;
/*
		        $row[] = $myorders->ProductName;
		        $row[] = $myorders->ProjectName;
		        $row[] = $myorders->DocTypeName;
*/		        $row[] = $myorders->LoanNumber;
		        $row[] = $myorders->LoanType;
		        $row[] = $myorders->MilestoneName;
		        //$row[] = $myorders->LenderName;
		        $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
		        $row[] = $myorders->PropertyStateCode;
		        $row[] = site_datetimeaging($myorders->EntryDatetime);
		        $row[] = site_datetimeformat($myorders->DueDateTime);
		        $row[] = site_datetimeformat($myorders->LastModifiedDateTime);
		        // $row[] = $myorders->PropertyCityName;
		        // $row[] = $myorders->PropertyStateCode;
		        // $row[] = $myorders->PropertyZipCode;
				
/*
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderDueDate));
*/
				$FollowUp = '';	
				if(!empty($myorders->FollowUpUID)) {
					$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
				}

		        if($this->loggedid == $myorders->AssignedToUserUID)
		        {
		        	$Action = '<a href="FHA_VA_CaseTeam/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
		        }else{

		       		$Action = '<button class="btn btn-info btn-sm FHAPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="fa fa-hand-o-up" aria-hidden="true"></i>'.$FollowUp.'</button>';
		        }
		        $row[] = $Action;
		        $myorderslist[] = $row;
        }



        $data =  array(
        	'myorderslist' => $myorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->FHAVACaseTeam_Orders_Model->KickBackcount_all($post),
			"recordsFiltered" =>  $this->FHAVACaseTeam_Orders_Model->KickBackcount_filtered($post,''),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function WriteExcel()
	{

		/*@Author Jainulabdeen <jainulabdeeen.b@avanzegroup.com> @Updated Mar 4 2020*/
		$post = $this->input->post();
		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}

		/* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
        }
    	/* ****** Dynamic Queues Section Ends ****** */

		if($post['formData']['Status'] == 'orderslist')
		{
			$list = $this->FHAVACaseTeam_Orders_Model->FHAVACaseTeamOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','State','Aging','Due Date Time','Last Modified Date Time');

			/* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "FHA_VA_CaseTeam/index/";
			$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);


			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

			for ($i=0; $i < sizeof($list); $i++) { 
			$data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->PropertyStateCode,site_datetimeaging($list[$i]->EntryDatetime),site_datetimeformat($list[$i]->DueDateTime),site_datetimeformat($list[$i]->LastModifiedDateTime));				
			}
			$this->outputCSV($data);
		}
		else if($post['formData']['Status'] == 'workinprogresslist')
		{
			 $list1 = $this->FHAVACaseTeam_Orders_Model->WorkInProgressOrders($post,'');
			 /**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			 $data = [];
			 $data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','State','Assigned To','Assigned Date Time','Aging','DueDateTime','LastModifiedDateTime');

			 /* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "FHA_VA_CaseTeam/index/";
			$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list1, $this->WorkflowModuleUID, $Mischallenous);


			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list1 = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

				 for ($i=0; $i < sizeof($list1); $i++) { 
				 $data[] = array($list1[$i]->OrderNumber,$list1[$i]->CustomerName, $list1[$i]->LoanNumber,$list1[$i]->LoanType,$list1[$i]->MilestoneName,$list1[$i]->StatusName,$list1[$i]->PropertyStateCode,$list1[$i]->AssignedUserName,site_datetimeformat($list1[$i]->AssignedDateTime),site_datetimeaging($list1[$i]->EntryDatetime),site_datetimeformat($list1[$i]->DueDateTime),site_datetimeformat($list1[$i]->LastModifiedDateTime));				
				 }
 
			 $this->outputCSV($data);
		}
		else if($post['formData']['Status'] == 'myorderslist')
		{
			$list2 = $this->FHAVACaseTeam_Orders_Model->MyOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','State','Aging','Due Date Time','Last Modified Date Time');

			/* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "FHA_VA_CaseTeam/index/";
			$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list2, $this->WorkflowModuleUID, $Mischallenous);


			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list2 = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

			     for($i=0; $i < sizeof($list2); $i++){
				     $data[]=array($list2[$i]->OrderNumber,$list2[$i]->CustomerName,$list2[$i]->LoanNumber,$list2[$i]->LoanType,$list2[$i]->MilestoneName,$list2[$i]->StatusName,$list2[$i]->PropertyStateCode,site_datetimeaging($list2[$i]->EntryDatetime),site_datetimeformat($list2[$i]->DueDateTime),site_datetimeformat($list2[$i]->LastModifiedDateTime));
			     }
			$this->outputCSV($data); 
		}
		else if($post['formData']['Status'] == 'parkingorderslist')
		{
			$list3 = $this->FHAVACaseTeam_Orders_Model->parkingorders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','State','Raised By','Remarks','Remainder On','Aging','DueDateTime','LastModifiedDateTime');

			/* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "FHA_VA_CaseTeam/index/";
			$Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
			$Mischallenous['IsParkingQueue'] = true;
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list3, $this->WorkflowModuleUID, $Mischallenous);


			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list3 = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

				for ($i=0; $i < sizeof($list3); $i++) { 
				$data[] = array($list3[$i]->OrderNumber,$list3[$i]->CustomerName, $list3[$i]->LoanNumber,$list3[$i]->LoanType,$list3[$i]->MilestoneName,$list3[$i]->StatusName,$list3[$i]->PropertyStateCode,$list3[$i]->RaisedBy,$list3[$i]->Remarks,$list3[$i]->Remainder,$list3[$i]->Aging,site_datetimeformat($list3[$i]->DueDateTime),site_datetimeformat($list3[$i]->LastModifiedDateTime));				
				}

			$this->outputCSV($data);
		}
		/*End*/

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

}?>

