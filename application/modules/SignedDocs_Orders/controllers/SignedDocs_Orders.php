<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SignedDocs_Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('SignedDocs_Orders_Model');
		$this->WorkflowModuleUID = $this->config->item('Workflows')['SignedDocs'];
		$this->WorkflowModuleName = 'SignedDocs';
	} 

	public function index()
	{

		$data['content']='index';
		$data['is_selfassign'] = 1;
		$data['IsGetNextOrder'] = $this->SignedDocs_Orders_Model->CheckAutoAssignEnabled($this->loggedid);
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$data['IsParking'] = $this->Common_Model->is_parking_enabledforworkflow($this->config->item('Workflows')['SignedDocs']);
		$data['IsKickBack'] = $this->Common_Model->is_kickback_enabledforworkflow($this->config->item('Workflows')['SignedDocs']);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function neworders_ajax_list(){
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
		$post['column_order'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

    //column search
		$post['column_search'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

		        /**
        *Function Description: Dynamic Columns Queues
        *@author Sathis Kannan <sathish.kannan@avanzegroup.com>
        *@since 14.7.2020
        */
         /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);

        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }



		$list = $this->SignedDocs_Orders_Model->SignedDocsOrders($post,'');

      

		$no = $post['start'];
		$SignedDocslist = [];
		/**
			*Function Description: Dynamic Columns Queues
			*@author Sathis Kannan <sathish.kannan@avanzegroup.com>
			*@since 15.7.2020
			*/
		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "SignedDocs/index/";
		$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$SignedDocslist 				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $myorders)
		{ 
			$row = array();
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->BorrowerFirstName;
			$row[] = $myorders->MilestoneName;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->LoanProcessor;
			// $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->PropertyStateCode;
			$row[] = site_datetimeaging($myorders->SigningTime);
			$row[] = site_datetimeaging($myorders->QueueDate);
			$row[] = $myorders->Funder;
			$row[] = site_datetimeaging($myorders->EarliestClosingDate);
			$row[] = site_datetimeaging($myorders->ClosingDisclosureSendDate);
			$row[] = $myorders->LockExpiration;
			$row[] = $myorders->OccupancyStatus;
			$row[] = $myorders->CashFromBorrower;

			// $row[] = site_datetimeaging($myorders->EntryDatetime);
			// $row[] = site_datetimeformat($myorders->DueDateTime);
			// $row[] = site_datetimeformat($myorders->LastModifiedDateTime);

			$FollowUp = '';	
			if(!empty($myorders->FollowUpUID)) {
				$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
			}

			if($this->loggedid == $myorders->AssignedToUserUID)
			{
				$Action = '<a href="SignedDocs/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
			}else{

				$Action = '<button class="btn btn-info btn-sm SignedDocsPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="fa fa-hand-o-up" aria-hidden="true"></i>'.$FollowUp.'</button>';
			}
			$row[] = $Action;
			$SignedDocslist[] = $row;
		}



		$data =  array(
			'orderslist' => $SignedDocslist,
			'post' => $post
		);



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->SignedDocs_Orders_Model->count_all($post),
			"recordsFiltered" =>  $this->SignedDocs_Orders_Model->count_filtered($post,''),
			"data" => $data['orderslist'],
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
  	//get_post_input_data
  	//column order
		$post['column_order'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

    //column search
		$post['column_search'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

		        /**
        *Function Description: Dynamic Columns Queues
        *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
        *@since 14.5.2020
        */
         /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }


        //column order
		$list = $this->SignedDocs_Orders_Model->WorkInProgressOrders($post,'');


		$no = $post['start'];
		$workinginprogresslist = [];


		/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/
		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "SignedDocs/index/";
		$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
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
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->BorrowerFirstName;
			$row[] = $myorders->MilestoneName;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->LoanProcessor;
			// $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->PropertyStateCode;
			$row[] = site_datetimeaging($myorders->SigningDate);
			$row[] = site_datetimeaging($myorders->SigningTime);
			$row[] = site_datetimeaging($myorders->QueueDate);
			$row[] = $myorders->Funder;
			$row[] = site_datetimeaging($myorders->EarliestClosingDate);
			$row[] = site_datetimeaging($myorders->ClosingDisclosureSendDate);
			$row[] = $myorders->LockExpiration;
			$row[] = $myorders->OccupancyStatus;
			$row[] = $myorders->CashFromBorrower;

			// $row[] = site_datetimeaging($myorders->EntryDatetime);
			// $row[] = site_datetimeformat($myorders->DueDateTime);
			// $row[] = site_datetimeformat($myorders->LastModifiedDateTime);

			$FollowUp = '';	
			if(!empty($myorders->FollowUpUID)) {
				$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
			}

			if($this->loggedid == $myorders->AssignedToUserUID)
			{
				$Action = '<a href="SignedDocs/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
			}else{

				$Action = '<button class="btn btn-info btn-sm SignedDocsPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</button>';
			}
			$row[] = $Action;
			$workinginprogresslist[] = $row;
		}



		$data =  array(
			'workinginprogresslist' => $workinginprogresslist,
			'post' => $post
		);



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->SignedDocs_Orders_Model->inprogress_count_all($post),
			"recordsFiltered" =>  $this->SignedDocs_Orders_Model->inprogress_count_filtered($post),
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
  //column order
		$post['column_order'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

    //column search
		$post['column_search'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

		        /**
        *Function Description: Dynamic Columns Queues
        *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
        *@since 14.5.2020
        */
         /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }


		$list = $this->SignedDocs_Orders_Model->MyOrders($post,'');

		$no = $post['start'];
		$myorderslist = [];


		/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/
		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "SignedDocs/index/";
		$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$myorderslist				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */



		foreach ($list as $myorders)
		{
			$row = array();
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->BorrowerFirstName;
			$row[] = $myorders->MilestoneName;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->LoanProcessor;
			// $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->PropertyStateCode;
			$row[] = site_datetimeaging($myorders->SigningDate);
			$row[] = site_datetimeaging($myorders->SigningTime);
			$row[] = site_datetimeaging($myorders->QueueDate);
			$row[] = $myorders->Funder;
			$row[] = site_datetimeaging($myorders->EarliestClosingDate);
			$row[] = site_datetimeaging($myorders->ClosingDisclosureSendDate);
			$row[] = $myorders->LockExpiration;
			$row[] = $myorders->OccupancyStatus;
			$row[] = $myorders->CashFromBorrower;

			// $row[] = site_datetimeaging($myorders->EntryDatetime);
			// $row[] = site_datetimeformat($myorders->DueDateTime);
			// $row[] = site_datetimeformat($myorders->LastModifiedDateTime);

			$FollowUp = '';	
			if(!empty($myorders->FollowUpUID)) {
				$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
			}

			if($this->loggedid == $myorders->AssignedToUserUID)
			{
				$Action = '<a href="SignedDocs/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
			}else{

				$Action = '<button class="btn btn-info btn-sm SignedDocsPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</button>';
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
			"recordsTotal" => $this->SignedDocs_Orders_Model->myorders_count_all($post),
			"recordsFiltered" =>  $this->SignedDocs_Orders_Model->myorders_count_filtered($post),
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
		$post['column_order'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

    //column search
		$post['column_search'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

		        /**
        *Function Description: Dynamic Columns Queues
        *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
        *@since 14.5.2020
        */
         /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }


		$list = $this->SignedDocs_Orders_Model->parkingorders($post,'');
		$no = $post['start'];
		$myorderslist = [];


		/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/
		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] = "SignedDocs/index/";
		$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
		$Mischallenous['IsParkingQueue'] = true;
		$Mischallenous['QueueColumns'] = $QueueColumns;
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
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->BorrowerFirstName;
			$row[] = $myorders->MilestoneName;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->LoanProcessor;
			// $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->PropertyStateCode;
			$row[] = site_datetimeaging($myorders->SigningDate);
			$row[] = site_datetimeaging($myorders->SigningTime);
			$row[] = site_datetimeaging($myorders->QueueDate);
			$row[] = $myorders->Funder;
			$row[] = site_datetimeaging($myorders->EarliestClosingDate);
			$row[] = site_datetimeaging($myorders->ClosingDisclosureSendDate);
			$row[] = $myorders->LockExpiration;
			$row[] = $myorders->OccupancyStatus;
			$row[] = $myorders->CashFromBorrower;

			// $row[] = site_datetimeaging($myorders->EntryDatetime);
			// $row[] = site_datetimeformat($myorders->DueDateTime);
			// $row[] = site_datetimeformat($myorders->LastModifiedDateTime);

			$FollowUp = '';	
			if(!empty($myorders->FollowUpUID)) {
				$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
			}

			if($this->loggedid == $myorders->AssignedToUserUID)
			{
				$Action = '<a href="SignedDocs/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
			}else{

				$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs SignedDocsPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</button>';
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
			"recordsTotal" => $this->SignedDocs_Orders_Model->parkingorders_count_all($post),
			"recordsFiltered" =>  $this->SignedDocs_Orders_Model->parkingorders_count_filtered($post),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}


	function KickBackorders_ajax_list(){
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
		$post['column_order'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

    //column search
		$post['column_search'] = array('tOrders.LoanNumber', 'tOrderPropertyRole.BorrowerFirstName','mMilestone.MilestoneName','tOrderImport.LoanProcessor','tOrders.LoanType','tOrders.PropertyStateCode', 'tOrderImport.SigningDate', 'tOrderImport.SigningTime','tOrderImport.QueueDate','tOrderImport.Funder','Earliest Closing Date','Closing Disclosure Send Date','tOrderImport.LockExpiration','tOrderImport.OccupancyStatus','tOrderImport.CashFromBorrower');

		        /**
        *Function Description: Dynamic Columns Queues
        *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
        *@since 14.5.2020
        */
         /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);

        }



		$list = $this->SignedDocs_Orders_Model->KickBackSignedDocsOrders($post,'');

		$no = $post['start'];
		$SignedDocslist = [];



		/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/
		/* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] 		= "SignedDocs/index/";
		$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$SignedDocslist 				= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $myorders)
		{
			$row = array();
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->BorrowerFirstName;
			$row[] = $myorders->MilestoneName;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->LoanProcessor;
			// $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->PropertyStateCode;
			$row[] = site_datetimeaging($myorders->SigningDate);
			$row[] = site_datetimeaging($myorders->SigningTime);
			$row[] = site_datetimeaging($myorders->QueueDate);
			$row[] = $myorders->Funder;
			$row[] = site_datetimeaging($myorders->EarliestClosingDate);
			$row[] = site_datetimeaging($myorders->ClosingDisclosureSendDate);
			$row[] = $myorders->LockExpiration;
			$row[] = $myorders->OccupancyStatus;
			$row[] = $myorders->CashFromBorrower;

			// $row[] = site_datetimeaging($myorders->EntryDatetime);
			// $row[] = site_datetimeformat($myorders->DueDateTime);
			// $row[] = site_datetimeformat($myorders->LastModifiedDateTime);

			$FollowUp = '';	
			if(!empty($myorders->FollowUpUID)) {
				$FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
			}

			if($this->loggedid == $myorders->AssignedToUserUID)
			{
				$Action = '<a href="SignedDocs/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
			}else{

				$Action = '<button class="btn btn-info btn-sm SignedDocsPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="fa fa-hand-o-up" aria-hidden="true"></i>'.$FollowUp.'</button>';
			}
			$row[] = $Action;
			$SignedDocslist[] = $row;
		}



		$data =  array(
			'orderslist' => $SignedDocslist,
			'post' => $post
		);



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->SignedDocs_Orders_Model->KickBackcount_all($post),
			"recordsFiltered" =>  $this->SignedDocs_Orders_Model->KickBackcount_filtered($post,''),
			"data" => $data['orderslist'],
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

		if($post['formData']['Status'] == 'orderslist')
		{
			$list = $this->SignedDocs_Orders_Model->SignedDocsOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Sathis Kannan <sathish.kannan@avanzegroup.com>
			*@since 15.07.2020
			*/
			
			$data = [];
			$data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','State','Aging','Due Date Time','Last Modified Date Time');

			// $data[] = array('Loan Number','Borrower Name','Milestone','processor Name','Loan Type','State','Signing Date','Signing Time','Queue','Funder','Earliest Closing Date','Closing Disclosure Send Date','Lock Expiration','Occupancy Status','Cash from borrower');


					/**
			*Function Description: Dynamic Columns Queues
			*@author Sathis Kannan <sathish.kannan@avanzegroup.com>
			*@since 15.07.2020
			*/

			/* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "SignedDocs/index/";
			$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list, $this->WorkflowModuleUID, $Mischallenous);
    

			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

			/*------ Automaticalled skipped when top if succeded --------*/

			for ($i=0; $i < sizeof($list); $i++) { 
			// $data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->PropertyStateCode,site_datetimeaging($list[$i]->EntryDatetime),site_datetimeformat($list[$i]->DueDateTime),site_datetimeformat($list[$i]->LastModifiedDateTime));				
				

		   // $data[] = array($list[$i]->LoanNumber,$list[$i]->BorrowerFirstName,$list[$i]->MilestoneName,$list[$i]->LoanType,$list[$i]->LoanProcessor,$list[$i]->PropertyStateCode,site_datetimeaging($list[$i]->SigningDate),site_datetimeformat($list[$i]->SigningTime),site_datetimeformat($list[$i]->QueueDate),$list[$i]->Funder,site_datetimeformat($list[$i]->EarliestClosingDate),site_datetimeformat($list[$i]->EarliestClosingDate),$list[$i]->LockExpiration,$list[$i]->OccupancyStatus,$list[$i]->CashFromBorrower);				
				

			}
	
		$this->outputCSV($data);
		
		}
		else if($post['formData']['Status'] == 'workinprogresslist')
		{
			$list1 = $this->SignedDocs_Orders_Model->WorkInProgressOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','State','Assigned To','Assigned Date Time','Aging','DueDateTime','LastModifiedDateTime');


					/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/

			/* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "SignedDocs/index/";
			$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list1, $this->WorkflowModuleUID, $Mischallenous);


			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list1 = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

			/*------ Automaticalled skipped when top if succeded --------*/
				for ($i=0; $i < sizeof($list1); $i++) { 
				$data[] = array($list1[$i]->OrderNumber,$list1[$i]->CustomerName, $list1[$i]->LoanNumber,$list1[$i]->LoanType,$list1[$i]->MilestoneName,$list1[$i]->StatusName,$list1[$i]->PropertyStateCode,$list1[$i]->AssignedTo,site_datetimeformat($list1[$i]->AssignedDateTime),site_datetimeaging($list1[$i]->EntryDatetime),site_datetimeformat($list1[$i]->DueDateTime),site_datetimeformat($list1[$i]->LastModifiedDateTime));				
				}

			$this->outputCSV($data);
		}
		else if($post['formData']['Status'] == 'myorderslist')
		{
			$list2 = $this->SignedDocs_Orders_Model->MyOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','State','Aging','Due Date Time','Last Modified Date Time');

					/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/

			/* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "SignedDocs/index/";
			$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list2, $this->WorkflowModuleUID, $Mischallenous);


			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list2 = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

			/*------ Automaticalled skipped when top if succeded --------*/

			for($i=0; $i < sizeof($list2); $i++){
				$data[]=array($list2[$i]->OrderNumber,$list2[$i]->CustomerName,$list2[$i]->LoanNumber,$list2[$i]->LoanType,$list2[$i]->MilestoneName,$list2[$i]->StatusName,$list2[$i]->PropertyStateCode,site_datetimeaging($list2[$i]->EntryDatetime),site_datetimeformat($list2[$i]->DueDateTime),site_datetimeformat($list2[$i]->LastModifiedDateTime));
			}
			$this->outputCSV($data);  
		}
		else if($post['formData']['Status'] == 'parkingorderslist')
		{
			$list3 = $this->SignedDocs_Orders_Model->parkingorders($post,'');

			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','State','Raised By','Remarks','Remainder On','Aging','DueDateTime','LastModifiedDateTime');

					/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/

			/* ****** Dynamic Queues Section Starts ****** */
			$Mischallenous['PageBaseLink'] = "SignedDocs/index/";
			$Mischallenous['AssignButtonClass'] = "SignedDocsPickNewOrder";
			$Mischallenous['IsParkingQueue'] = true;
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list3, $this->WorkflowModuleUID, $Mischallenous);


			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list3 = [];
			}

			/* ****** Dynamic Queues Section Ends ****** */

			/*------ Automaticalled skipped when top if succeded --------*/


				for ($i=0; $i < sizeof($list3); $i++) { 
				$data[] = array($list3[$i]->OrderNumber,$list3[$i]->CustomerName, $list3[$i]->LoanNumber,$list3[$i]->LoanType,$list3[$i]->MilestoneName,$list3[$i]->StatusName,$list3[$i]->PropertyStateCode,$list3[$i]->RaisedBy,$list3[$i]->Remarks,$list3[$i]->Remainder,site_datetimeaging($list3[$i]->EntryDatetime),site_datetimeformat($list3[$i]->DueDateTime),site_datetimeformat($list3[$i]->LastModifiedDateTime));				
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

