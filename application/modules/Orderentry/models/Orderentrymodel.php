<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Orderentrymodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}


	function insert_order($data)
	{

		$date = date('Ymd');

		$this->db->trans_begin();

		$insertdata = new stdClass();
		


		$insertdata->CustomerUID = $data['Customer'];
		$insertdata->ProductUID = $data['ProductUID'];
		$insertdata->ProjectUID = $data['ProjectUID'];
		$insertdata->PriorityUID = $data['PriorityUID'];
		$insertdata->LoanNumber = $data['LoanNumber'];
		$insertdata->LoanAmount = $data['LoanAmount'];
		$insertdata->LoanType = $data['LoanType'];
		$insertdata->CustomerReferenceNumber = $data['CustomerReferenceNumber'];
		$insertdata->APN = $data['APN'];
		$insertdata->PropertyAddress1 = $data['PropertyAddress1'];
		$insertdata->PropertyAddress2 = $data['PropertyAddress2'];
		$insertdata->PropertyZipcode = $data['PropertyZipcode'];
		$insertdata->PropertyCityName = $data['PropertyCityName'];
		$insertdata->PropertyCountyName = $data['PropertyCountyName'];
		$insertdata->PropertyStateCode = $data['PropertyStateCode'];
		$insertdata->StatusUID = $this->config->item('keywords')['NewOrder'];
	
		
		//insert ocr enabled field in torders
		$IsOCREnabled = $this->db->select('IsOCREnabled')->from('mProducts')->where('ProductUID',$insertdata->ProductUID)->get()->row();
		$insertdata->IsOCREnabled = $IsOCREnabled->IsOCREnabled;

		// Decide order status

		$insertdata->OrderEntryDatetime = Date('Y-m-d H:i:s', strtotime("now"));
		

		$insertdata->OrderNumber = $this->Order_Number();


		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $data['ProjectUID']]);
		if (!empty($mProjectCustomer) && is_object($mProjectCustomer) && $mProjectCustomer->PriorityTime != 0) {
			$insertdata->OrderDueDate = date('Y-m-d H:i:s', strtotime('+' . $mProjectCustomer->PriorityTime . ' Hours'));
		}
		else{
			$insertdata->OrderDueDate = date('Y-m-d H:i:s', strtotime('+1 Day'));
		}
		$insertdata->OrderEntryByUserUID = $this->loggedid;

		$query = $this->db->insert('tOrders', $insertdata);
		$insert_id = $this->db->insert_id();

		$torderimport['OrderUID'] = $insert_id;
		$this->Common_Model->save('tOrderImport', $torderimport);			
		
		if ($insert_id) {
			$this->db->where('tOrderPropertyRole.OrderUID',$insert_id);
			$this->db->delete('tOrderPropertyRole');
			
			$entry_array = array();
			$count = count($data['BorrowerFirstName']);
			$BorrowerFirstName = $data['BorrowerFirstName']; 
			//$BorrowerLastName = $data['BorrowerLastName']; 
			$BorrowerMailAddress = $data['BorrowerMailAddress']; 
			$BorrowerContactNumber = $data['BorrowerContactNumber']; 
			$BorrowerSSN = $data['BorrowerSSN'];

			for($i=0; $i<$count; $i++)  
			{
				$entry_single_array = array(
					"OrderUID"=>$insert_id,
					'BorrowerFirstName' => $BorrowerFirstName[$i],
					// 'BorrowerLastName' => $BorrowerLastName[$i],
					'BorrowerMailingAddress1' => $BorrowerMailAddress[$i],
					'BorrowerContactNumber' => $BorrowerContactNumber[$i],
					'BorrowerSSN' => $BorrowerSSN[$i],
				);
				$entry_array[] = $entry_single_array;

			} 

			if(!empty($entry_array)) {
				$this->db->insert_batch('tOrderPropertyRole', $entry_array);
			}
		}

		/*INSERT ORDER LOGS BEGIN*/
		$this->Common_Model->OrderLogsHistory($insert_id,'Order Entry',Date('Y-m-d H:i:s'));
		/*INSERT ORDER LOGS END*/
		$this->InsertOrderworkflow_ALL($insert_id, $data['Customer']);



		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
		}

		$OrderNumbers = $insertdata->OrderNumber;
		$Msg = $this->lang->line('Order_Save');
		$Rep_msg = str_replace("<<Order Number>>", $OrderNumbers, $Msg);
		return ['message'=>$Rep_msg, 'OrderUID'=>$insert_id, 'OrderNumber'=>$insertdata->OrderNumber];

	}

	function savebulkentry_order($data,$tpropdata, $torderimport = [])
	{

		$date = date('Ymd');

		$this->db->trans_begin();

		$insertdata = new stdClass();
		$tdoccheckindata = new stdClass();
		// $tpropdata = new stdClass();
		$tspecialdata = new stdClass();

		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID'=>$data['ProjectUID']]);

		/*tOrders table*/
		$insertdata->CustomerUID = $data['CustomerUID'];
		$insertdata->ProductUID = $data['ProductUID'];
		$insertdata->ProjectUID = $data['ProjectUID'];
		$insertdata->LoanNumber = $data['LoanNumber'];
		$insertdata->CustomerReferenceNumber = $data['CustomerReferenceNumber'];
		$insertdata->LoanAmount = $data['LoanAmount'];
		$insertdata->LoanType = $data['LoanType'];
		$insertdata->PropertyAddress1 = $data['PropertyAddress1'];
		$insertdata->PropertyCityName = $data['PropertyCityName'];
		$insertdata->PropertyStateCode = $data['PropertyStateCode'];
		$insertdata->PropertyZipCode = $data['PropertyZipCode'];
		$insertdata->PropertyCountyName = $data['PropertyCountyName'];

		if (!empty($data['MilestoneUID'])) {
			$insertdata->MilestoneUID = $data['MilestoneUID'];
		}


    //insert ocr enabled field in torders
		$IsOCREnabled = $this->db->select('IsOCREnabled')->from('mProducts')->where('ProductUID',$insertdata->ProductUID)->get()->row();
		$insertdata->IsOCREnabled = $IsOCREnabled->IsOCREnabled;
		

		$insertdata->StatusUID = $this->config->item('keywords')['NewOrder'];
		$insertdata->OrderEntryDatetime = Date('Y-m-d H:i:s', strtotime("now"));

		$insertdata->OrderNumber = $this->Order_Number();

		$OrderNos = [];
		if (!empty($mProjectCustomer) && is_object($mProjectCustomer) && $mProjectCustomer->PriorityTime != 0) {
			$insertdata->OrderDueDate = date('Y-m-d H:i:s', strtotime('+' . $mProjectCustomer->PriorityTime . ' Hours'));
		} else {
			$insertdata->OrderDueDate = date('Y-m-d H:i:s', strtotime('+1 Day'));
		}

		$insertdata->OrderEntryByUserUID = $this->loggedid;
		$query = $this->db->insert('tOrders', $insertdata);
		$insert_id = $this->db->insert_id();

		/*tOrderPropertyroles data*/
		foreach ($tpropdata as $tpropkey => $tpropvalue) {
			$tpropdata[$tpropkey]['OrderUID'] = $insert_id;
			$this->db->insert('tOrderPropertyRole', $tpropdata[$tpropkey]);
		}		

		if (!empty($torderimport)) 
		{
			$torderimport['OrderUID'] = $insert_id;
			$this->Common_Model->save('tOrderImport', $torderimport);			
		}


		if (!empty($data['MilestoneUID'])) {
			$this->Common_Model->save('tOrderMileStone', ['OrderUID'=>$insert_id, 'MilestoneUID'=>$data['MilestoneUID'], 'CompletedDateTime'=>date('Y-m-d H:i:s'), 'CompletedByUserUID'=>$this->loggedid]);
		}

		// workflows insert
		$this->InsertOrderworkflow_ALL($insert_id, $data['CustomerUID']);


		$this->Orderentrymodel->InsertOrderWorkflow($mProjectCustomer, $insert_id);

		// Order Workflows updates
		$OrderUID = $insert_id;
		$MilestoneUID = $data['MilestoneUID'];
		$CustomerUID = $data['CustomerUID'];
		$this->OrderWorkflowsUpdates($OrderUID, $CustomerUID, $MilestoneUID, $torderimport);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			$OrderNos[] = $insertdata->OrderNumber;

			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($insert_id,'Order Entry (Bulk)',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
		}

		$OrderNumbers = implode(",", $OrderNos);
		$Msg = $this->lang->line('Order_Save');
		$Rep_msg = str_replace("<<Order Number>>", $OrderNumbers, $Msg);
		return ['message'=>$Rep_msg, 'OrderUID'=>$insert_id, 'OrderNumber'=>$insertdata->OrderNumber];

	}

	function updatebulkentry_order($updatedata,$tpropdata, $torderimport = [])
	{

		$date = date('Ymd');

		$this->db->trans_begin();


		$OrderUID = $updatedata['OrderUID'];

		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('tOrders', $updatedata);

		/*tOrderPropertyroles data*/
		foreach ($tpropdata as $tpropkey => $tpropvalue) {
			$tpropdata[$tpropkey]['OrderUID'] = $OrderUID;

			$this->db->where('OrderUID', $OrderUID);
			$this->db->limit(1);
			$this->db->update('tOrderPropertyRole', $tpropdata[$tpropkey]);
		}		

		/*Update tOrderImport*/
		if(!empty($torderimport)) {
			
			$tOrderImportrow = $this->db->select('1')->from('tOrderImport')->where('OrderUID',$OrderUID)->get()->row();
			if(!empty($tOrderImportrow)) {
				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('tOrderImport', $torderimport);
			} else {
				$this->db->insert('tOrderImport',$torderimport);
			}
		}		

		//Order Workflows updates
		$MilestoneUID = $updatedata['MilestoneUID'];
		$CustomerUID = $updatedata['CustomerUID'];
		$this->OrderWorkflowsUpdates($OrderUID, $CustomerUID, $MilestoneUID, $torderimport);

		// Update Order Workflow
		$this->UpdateOrderworkflow_ALL($OrderUID, $updatedata['CustomerUID']);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;
		} else {

			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID,'Order Update (Bulk)',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/

			$this->db->trans_commit();
			return true;
		}


	}

	// Order Workflows updates
	function OrderWorkflowsUpdates($OrderUID, $CustomerUID, $MilestoneUID, $torderimport, $cronupdate = FALSE) {
		if (!empty($MilestoneUID)) {
			if ($cronupdate == FALSE) {
				$tOrderMileStoneData = array(
					'OrderUID'=>$OrderUID, 
					'MilestoneUID'=>$MilestoneUID, 
					'CompletedDateTime'=>date('Y-m-d H:i:s'), 
					'CompletedByUserUID'=>$this->loggedid
				);
				$this->db->insert('tOrderMileStone', $tOrderMileStoneData);
			}			

			// Get order details
			$this->db->select('tOrders.MilestoneUID, tOrderImport.SigningDate, tOrderImport.ClosingDisclosureSendDate, tOrderImport.DocsOutSigningDate, tOrderImport.DocsOutClosingDisclosureSendDate');
			$this->db->from('tOrders');
			$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','left');
			$this->db->where('tOrders.OrderUID',$OrderUID);
			$OrderDetails = $this->db->get()->row();

			// Declare the variables			
			$OrderLogsDescriptions = ''; // Declare Log variable
			$CheckIsOrderEnabledWorkflows = array();
			$IsRemainingWorkflowForceCompleted = '';
			$IsWorkflowForceCompleted = '';
			$IsForceCompletedWorkflows = array();

			// Check either CD or Scheduling condition matched.
			$CheckMilestoneUID = $this->config->item('Milestones')['2G'];

			// CD and Scheduling Queue Condition
			if (($MilestoneUID == $CheckMilestoneUID && empty($torderimport['ClosingDisclosureSendDate'])) || ($MilestoneUID == $CheckMilestoneUID && !empty($torderimport['ClosingDisclosureSendDate']) && empty($torderimport['SigningDate']))) {

				// Check either CD and Scheduling workflow is enabled
				$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['CD'];
				$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['Scheduling'];

				// CD Queue Conditions
				// Logic: 2G&Closing Disclosure sent date –Blank
				if ($MilestoneUID == $CheckMilestoneUID && empty($torderimport['ClosingDisclosureSendDate'])) 
				{
					# code...
				}

				// Scheduling Queue Conditions
				// Logic: 2G&Closing Disclosure sent dateis not blank&Signing date is blank
				if ($MilestoneUID == $CheckMilestoneUID && !empty($torderimport['ClosingDisclosureSendDate']) && empty($torderimport['SigningDate'])) {
					$CheckIsOrderEnabledWorkflows = [];
					$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['Scheduling'];
				}

				//Order force complete skipped workflows				
				$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['DocsOut'];	
				$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['FundingConditions'];
				$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['SignedDocs'];
				$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['HOI'];
				$CheckIsOrderEnabledWorkflows[] = $this->config->item('Workflows')['TitleTeam'];

				//Order is force completed 
				$IsRemainingWorkflowForceCompleted = "YES";

			}

			// DocsOut Queue Conditions
			// Logic: 3, 4, 5- Milestones should be moved to Completed
			$CheckWorkflowCompleteMilestones = array(
				$this->config->item('Milestones')['3A'],
				$this->config->item('Milestones')['4A'],
				$this->config->item('Milestones')['5A']
			);

			// DocsOut queue condition
			// 2G, scheduled date, Closing disclosure sent date
			// This condition matched DocsOut workflow automatically completed.
			if (in_array($MilestoneUID, $CheckWorkflowCompleteMilestones)) 
			{
				//Workflow is force completed
				$IsWorkflowForceCompleted = "YES";

				$IsForceCompletedWorkflows[] = $this->config->item('Workflows')['DocsOut'];

			}

			// 2G, 2F, all 3 series, 4 series, 5 series milestone to be moved to completed in Gatekeeping and Submission Queue
			$GatekeepingSubmissionCompleteMilestones = array(
				$this->config->item('Milestones')['2F'],
				$this->config->item('Milestones')['2G'],
				$this->config->item('Milestones')['3A'],
				$this->config->item('Milestones')['4A'],
				$this->config->item('Milestones')['5A']
			);
			if (in_array($MilestoneUID, $GatekeepingSubmissionCompleteMilestones)) {
				//Workflow is force completed
				$IsWorkflowForceCompleted = "YES";

				$IsForceCompletedWorkflows[] = $this->config->item('Workflows')['GateKeeping'];
				$IsForceCompletedWorkflows[] = $this->config->item('Workflows')['Submissions'];
			}

			// DocsOut Queue Complete the Static Sub Queue "Submitted for Doc Check"
			if ($CheckMilestoneUID == $OrderDetails->MilestoneUID && !empty($OrderDetails->DocsOutClosingDisclosureSendDate) && !empty($OrderDetails->DocsOutSigningDate) && empty($torderimport['Queue'])) 
			{
				// Check if exception row available
				$this->db->select('mQueues.QueueName, mWorkFlowModules.WorkflowModuleName');
				$this->db->from('tOrderQueues');
				$this->db->join('mQueues','mQueues.QueueUID = tOrderQueues.QueueUID','left');
				$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID','left');
				$this->db->where(array(
					'tOrderQueues.OrderUID'=>$OrderUID,
					'tOrderQueues.QueueUID'=>$this->config->item('SubmittedforDocCheck'), 
					"tOrderQueues.QueueStatus" => "Pending"
				));
				$is_exception_row_available = $this->db->get()->row();

				if (!empty($is_exception_row_available)) {							
					// Clear the "Submitted for Doc Check" exption if raised for this order
					$tOrderQueuesData = [];
					$tOrderQueuesData['OrderUID'] = $OrderUID;
					$tOrderQueuesData['QueueUID'] = $this->config->item('SubmittedforDocCheck');
					$tOrderQueuesData['QueueStatus'] = "Completed";
					$tOrderQueuesData['CompletedReasonUID'] = '';
					$tOrderQueuesData['CompletedRemarks'] = ''; // Exception queue is force completed.
					$tOrderQueuesData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

					$this->db->where(array(
						'OrderUID'=>$OrderUID,
						'QueueUID'=>$this->config->item('SubmittedforDocCheck'), 
						"QueueStatus" => "Pending"
					));
					$this->db->update('tOrderQueues', $tOrderQueuesData);

					if($this->db->affected_rows()) {
						$OrderLogsDescriptions.= '<br/><b>'.$is_exception_row_available->WorkflowModuleName.' - '.$is_exception_row_available->QueueName.'</b> sub queue was force completed.';
					}
				}
			}

			if ($IsRemainingWorkflowForceCompleted == "YES") {

				// Check order is enabled workflows
				$this->db->select('WorkflowModuleUID');
				$this->db->from('mCustomerWorkflowModules');
				$this->db->where(array('CustomerUID'=>$CustomerUID));
				$this->db->where_in('WorkflowModuleUID',$CheckIsOrderEnabledWorkflows);

				// Check total records is greater than zero
				if($this->db->get()->num_rows() > 0) {										

					// Select need complete the workflow list
					$this->db->select('mCustomerWorkflowModules.WorkflowModuleUID, mWorkFlowModules.WorkflowModuleName');
					$this->db->from('mCustomerWorkflowModules');
					$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID','left');
					$this->db->where(array('mCustomerWorkflowModules.CustomerUID'=>$CustomerUID));
					$this->db->where_not_in('mCustomerWorkflowModules.WorkflowModuleUID',$CheckIsOrderEnabledWorkflows);

					$NeedtoCompleteWorklfowList = $this->db->get()->result_array();

					if (!empty($NeedtoCompleteWorklfowList)) {
						$OrderLogsDescriptions.= $this->CompleteGivenWorkflows($OrderUID, $NeedtoCompleteWorklfowList);
					}					
										
				}
			}

			if ($IsWorkflowForceCompleted == "YES") {
				// Select need complete the workflow list
				$this->db->select('mCustomerWorkflowModules.WorkflowModuleUID, mWorkFlowModules.WorkflowModuleName');
				$this->db->from('mCustomerWorkflowModules');
				$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID','left');
				$this->db->where(array('mCustomerWorkflowModules.CustomerUID'=>$CustomerUID));
				$this->db->where_in('mCustomerWorkflowModules.WorkflowModuleUID',$IsForceCompletedWorkflows);

				$NeedtoCompleteWorklfowList = $this->db->get()->result_array();

				if (!empty($NeedtoCompleteWorklfowList)) {
					$OrderLogsDescriptions.= $this->CompleteGivenWorkflows($OrderUID, $NeedtoCompleteWorklfowList);
				}				
				
			}

			// Enable workflow
			$OrderLogsDescriptions.= $this->SubmissionCondition($OrderUID);

			// Expiry Orders Complete
			if (in_array($MilestoneUID, $this->config->item('ExpiryOrdersCompleteMilestones'))) {
				
				$this->ExpiryOrdersComplete($CustomerUID, $OrderUID);
			}

			// DocsOut(When completed Milestone is in 2G while uploading should move back to new orders)
			// Check docsout workflow is completed
			$IsDocsOutWorkflowCompleted = $this->Common_Model->IsWorkflowCompleted($OrderUID, $this->config->item('Workflows')['DocsOut']);

			if (!empty($IsDocsOutWorkflowCompleted)) {
				
				// Check is Docsout logic match
				if ($this->Is_Workflow_Enable($OrderUID, $this->config->item('Workflows')['DocsOut'], $CustomerUID)) {
										
					if ($this->Common_Model->save('tOrderAssignments', ['WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress']], ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['DocsOut']])) {

						$workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID', $this->config->item('Workflows')['DocsOut'])->get()->row();

						// Add log
						$OrderLogsDescriptions.= '<br/>The <b>'.$workflow_names->WorkflowModuleName.'</b> workflow is moved back to the working queue';
					}
				}
			}

			// If loan is in Review Completed - When loan matches for submiited for doc checked then review complete to be completed and loan to be in submitted for doc check. - for all static queues.
			// $test = $this->Is_Workflow_Enable($OrderUID, $WorkflowModuleUID, $CustomerUID, ['Exclude'=>TRUE]); var_dump($test); exit();
			if ($this->Is_Workflow_Enable($OrderUID, $this->config->item('Workflows')['DocsOut'], $CustomerUID, ['Exclude'=>TRUE])) {
				$Queues = '';
				$Queues = $this->Common_Model->getCustomerWorkflowQueues($this->config->item('Workflows')['DocsOut'], FALSE, $CustomerUID);

				foreach ($Queues as $key => $queue) {

					// Clear the exception if raised for this order
					$tOrderQueuesData = [];
					$tOrderQueuesData['OrderUID'] = $OrderUID;
					$tOrderQueuesData['QueueUID'] = $queue->QueueUID;
					$tOrderQueuesData['QueueStatus'] = "Completed";
					$tOrderQueuesData['CompletedReasonUID'] = '';
					$tOrderQueuesData['CompletedRemarks'] = ''; // Exception queue is force completed.
					$tOrderQueuesData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

					$this->db->where(array(
						'OrderUID'=>$OrderUID,
						'QueueUID'=>$queue->QueueUID, 
						"QueueStatus" => "Pending"
					));
					$this->db->update('tOrderQueues', $tOrderQueuesData);

					if($this->db->affected_rows()) {

						$workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID', $this->config->item('Workflows')['DocsOut'])->get()->row();

						$OrderLogsDescriptions.= '<br/><b>'.$workflow_names->WorkflowModuleName.' - '.$queue->QueueName.'</b> sub queue was force completed.';
					}

				}
			}

			if ($cronupdate == TRUE) {
				return $OrderLogsDescriptions;
			}

			/*INSERT ORDER LOGS BEGIN*/
			if (!empty($OrderLogsDescriptions)) {
				$this->Common_Model->OrderLogsHistory($OrderUID,'Order Update (Bulk)'.$OrderLogsDescriptions,Date('Y-m-d H:i:s'), $this->config->item('Cron_UserUID'));
			}			
			/*INSERT ORDER LOGS END*/

			// Workup rework condition
			$this->Cron_model->WorkupReworkCron(['OrderUID'=>$OrderUID]);
		}
	}

	/**
	*Function 3,4,5 series should be not part of expiry orders if loan is 2 series if changes to 3 series then it should be autocompleted.  
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Date.
	*/
	public function ExpiryOrdersComplete($CustomerUID, $OrderUID)
	{
		// Select need complete the workflow list
		$this->db->select('mCustomerWorkflowModules.WorkflowModuleUID, mWorkFlowModules.WorkflowModuleName');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID','left');
		$this->db->where(array('mCustomerWorkflowModules.CustomerUID'=>$CustomerUID));
		$this->db->where('IsExpiryOrdersRequire', STATUS_ONE);
		$WorkflowLists = $this->db->get()->result_array();

		foreach ($WorkflowLists as $value) {
		    
		    if($this->Common_Model->IsChecklistExpiryOrders($OrderUID, $value['WorkflowModuleUID'])) {

		    	$tOrderChecklistExpiryCompleteData = [];
				$tOrderChecklistExpiryCompleteData['OrderUID'] = $OrderUID;
				$tOrderChecklistExpiryCompleteData['WorkflowModuleUID'] = $value['WorkflowModuleUID'];
				$tOrderChecklistExpiryCompleteData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
				$tOrderChecklistExpiryCompleteData['CompletedDateTime'] = date('Y-m-d H:i:s');

				$this->Common_Model->save('tOrderChecklistExpiryComplete', $tOrderChecklistExpiryCompleteData);

				// Add log
				$this->Common_Model->OrderLogsHistory($OrderUID, $value['WorkflowModuleName']." Expiry Completed", Date('Y-m-d H:i:s'));
		    }
		}
	}

	/**
	*Function In Workup issue cd,issue LE and workup completed and Gatekeeping to be completed if these loan present in these queues then loan should available in submission queue 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 14 August 2020.
	*/
	public function SubmissionCondition($OrderUID)
	{
		$IsWorkflowEnableDescription = '';
		$SubmissionsWorkflowModuleUID = $this->config->item('Workflows')['Submissions'];

		$this->db->select('1');
		$this->db->from('tOrders');

		$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$SubmissionsWorkflowModuleUID,'left');
		
		$WorkupModuleUID = $this->config->item('Workflows')['Workup'];
		$this->db->join('tOrderAssignments AS STOAC_'.$WorkupModuleUID, 'STOAC_'.$WorkupModuleUID.'.OrderUID = tOrders.OrderUID AND STOAC_'.$WorkupModuleUID.'.WorkflowModuleUID = "' .$WorkupModuleUID. '"', 'left');
		
		$GateKeepingModuleUID = $this->config->item('Workflows')['GateKeeping'];
		$this->db->join('tOrderAssignments AS STOAC_'.$GateKeepingModuleUID, 'STOAC_'.$GateKeepingModuleUID.'.OrderUID = tOrders.OrderUID AND STOAC_'.$GateKeepingModuleUID.'.WorkflowModuleUID = "' .$GateKeepingModuleUID. '"', 'left');

		$WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');

		$this->db->group_start();
			$this->db->group_start();
				foreach ($WorkupSubQueueComplete as $key => $QueueUID) {						
					if ($key === 0) {
						$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
					} else {
						$this->db->or_where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
					}
				}
						
				$this->db->or_where('STOAC_'.$WorkupModuleUID.'.WorkflowStatus = '.$this->config->item('WorkflowStatus')['Completed'].' ');		
			$this->db->group_end();
			$this->db->or_group_start();
				$this->db->where('STOAC_'.$GateKeepingModuleUID.'.WorkflowStatus = '.$this->config->item('WorkflowStatus')['Completed'].' ');	
			$this->db->group_end();				

		$this->db->group_end();
		$this->db->group_start();
			$this->db->where('tOrderAssignments.WorkflowStatus != '.$this->config->item('WorkflowStatus')['Completed'].' OR tOrderAssignments.WorkflowStatus IS NULL',NULL, FALSE);
		$this->db->group_end();

		$this->db->where('tOrders.OrderUID',$OrderUID);		

		$CheckIsWorkflowEnabledOrders = $this->db->get()->result();

		if (!empty($CheckIsWorkflowEnabledOrders)) {

			$WorkflowModuleUID = $this->Common_Model->is_workflow_in_parkingqueue($OrderUID,$this->config->item('Workflows')['Submissions']);
			$CheckWorkflowCompleted = $this->Common_Model->IsWorkflowCompleted($OrderUID, $this->config->item('Workflows')['GateKeeping']);
			$query = $this->db->query("SELECT EXISTS (SELECT
				tOrders.OrderUID
				FROM
				tOrders
				LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID
				AND tOrderAssignments.WorkflowModuleUID = ".$this->config->item('Workflows')['Workup']."
				LEFT JOIN tOrderQueues ON tOrderQueues.OrderUID = tOrders.OrderUID
				AND tOrderQueues.QueueUID IN (".$this->config->item('RaiseSubmissionsParkingQueue')['IssuedCD'].", ".$this->config->item('RaiseSubmissionsParkingQueue')['IssuedLE'].")
				WHERE
				(
				tOrderAssignments.WorkflowStatus = 5
				OR tOrderQueues.QueueStatus = 'Pending'
				)
				AND tOrders.OrderUID = ".$OrderUID.") AS available");
			$CheckWorkupReady = $query->row()->available;

			// Fetch workflow name			
			$this->db->select('mWorkFlowModules.WorkflowModuleName');
			$query = $this->db->get_where('mWorkFlowModules', array('WorkflowModuleUID' => $SubmissionsWorkflowModuleUID));
    		$WorkflowModuleName = $query->row()->WorkflowModuleName;
			
			if(!empty($CheckWorkupReady) && !empty($CheckWorkflowCompleted))
			{
				$data['IsCleared'] = 1;
				$data['ClearedByUserUID'] = $this->loggedid;
				$data['ClearedDateTime'] = date('Y-m-d H:i:s');
				$update = $this->Common_Model->save('tOrderParking', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$this->config->item('Workflows')['Submissions'],'IsCleared'=>0]);

				// parking cleared log
				$IsWorkflowEnableDescription.= '<br/><b>'.$WorkflowModuleName.'</b> Parking is force cleared.';
			}
			else if(!empty($CheckWorkupReady) || !empty($CheckWorkflowCompleted))
			{
				if(empty($WorkflowModuleUID))
				{	
					$parkingdata['OrderUID'] = $OrderUID;
					$parkingdata['WorkflowModuleUID'] = $this->config->item('Workflows')['Submissions'];
					$parkingdata['ReasonUID'] = '';
					$parkingdata['RaisedByUserUID'] = $this->loggedid;
					$parkingdata['RaisedDateTime'] = date('Y-m-d H:i:s');
					$this->Common_Model->save('tOrderParking', $parkingdata);

					// parking cleared log
					$IsWorkflowEnableDescription.= '<br/><b>'.$WorkflowModuleName.'</b> Parking is force raised.';
				}
			}


			//IF Workup enabled Submissions to be enabled start
			$tOrderWorkflowrowSubmissions = $this->Common_Model->get_row('`tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$SubmissionsWorkflowModuleUID]);

			if(!empty($tOrderWorkflowrowSubmissions)){

				$tOrderWorkflows['IsPresent'] = STATUS_ONE;
				$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowSubmissions->EntryDateTime) ? $tOrderWorkflowrowSubmissions->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
				$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowSubmissions->DueDateTime) ? $tOrderWorkflowrowSubmissions->DueDateTime : calculate_workflowduedatetime($OrderUID,$SubmissionsWorkflowModuleUID);
				$tOrderWorkflows['IsAssign'] = STATUS_ONE;
				$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $SubmissionsWorkflowModuleUID]);

				// workflow enable log
				$IsWorkflowEnableDescription.= '<br/><b>'.$WorkflowModuleName.'</b> workflow is force enabled.';

			}	else {

				$tOrderWorkflows['OrderUID'] = $OrderUID;
				$tOrderWorkflows['WorkflowModuleUID'] = $SubmissionsWorkflowModuleUID;
				$tOrderWorkflows['IsPresent'] = STATUS_ONE;
				$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowSubmissions->EntryDateTime) ? $tOrderWorkflowrowSubmissions->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
				$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowSubmissions->DueDateTime) ? $tOrderWorkflowrowSubmissions->DueDateTime : calculate_workflowduedatetime($OrderUID,$SubmissionsWorkflowModuleUID);
				$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);

				// workflow enable log
				$IsWorkflowEnableDescription.= '<br/><b>'.$WorkflowModuleName.'</b> workflow is force enabled.';

			}
			//IF Workup enabled Submissions to be enabled end

		}	

		return $IsWorkflowEnableDescription;	

	}

	// Complete the given workflows
	function CompleteGivenWorkflows($OrderUID, $NeedtoCompleteWorklfowList) {
		$CompleteGivenWorkflowsLog = '';
		foreach ($NeedtoCompleteWorklfowList as $key => $value) {
			// Check this workflow is present for this order
			$this->db->select('1');
			$this->db->from('tOrderWorkflows');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('WorkflowModuleUID',$value['WorkflowModuleUID']);
			$this->db->group_start();
			$this->db->where('IsPresent',1);
			$this->db->or_where('IsAssign',1);
			$this->db->group_end();

			$IsAvailableWorkflow = $this->db->get()->row_array();

			if (!empty($IsAvailableWorkflow)) {

				// Assign values
				$WorkflowModuleUID = '';
				$WorkflowModuleName = '';
				$WorkflowModuleUID = $value['WorkflowModuleUID'];
				$WorkflowModuleName = $value['WorkflowModuleName'];
				
				// Any issue checklist is there for this order (To complete that checklist)
				$this->db->where(array(
					'OrderUID' => $OrderUID,
					'WorkflowUID' => $WorkflowModuleUID,
					'Answer' => 'Problem Identified'
				));

				$this->db->update('tDocumentCheckList', array('Answer' => 'Completed'));

				if($this->db->affected_rows()) {
					$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> '.$this->db->affected_rows().' Problem Identified checklist is force completed.';
				}

				// Order in parking queue need to complete
				$tOrderParkingData = [];
				$tOrderParkingData['IsCleared'] = 1;
				$tOrderParkingData['ReasonUID'] = '';
				$tOrderParkingData['Remarks'] = ''; // Parking is force completed.
				$tOrderParkingData['ClearedByUserUID'] = $this->config->item('Cron_UserUID');
				$tOrderParkingData['ClearedDateTime'] = date('Y-m-d H:i:s');

				$this->db->where(array(
					'OrderUID'=>$OrderUID,
					'WorkflowModuleUID'=>$WorkflowModuleUID,
					'IsCleared'=>0
				));
				$this->db->update('tOrderParking', $tOrderParkingData);

				if($this->db->affected_rows()) {
					$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> parking queues is cleared.';
				}

				$Queues = '';
				$Queues = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID);

				foreach ($Queues as $key => $queue) {

					// Clear the exception if raised for this order
					$tOrderQueuesData = [];
					$tOrderQueuesData['OrderUID'] = $OrderUID;
					$tOrderQueuesData['QueueUID'] = $queue->QueueUID;
					$tOrderQueuesData['QueueStatus'] = "Completed";
					$tOrderQueuesData['CompletedReasonUID'] = '';
					$tOrderQueuesData['CompletedRemarks'] = ''; // Exception queue is force completed.
					$tOrderQueuesData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

					$this->db->where(array(
						'OrderUID'=>$OrderUID,
						'QueueUID'=>$queue->QueueUID, 
						"QueueStatus" => "Pending"
					));
					$this->db->update('tOrderQueues', $tOrderQueuesData);

					if($this->db->affected_rows()) {
						$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.' - '.$queue->QueueName.'</b> sub queue was force completed.';
					}

					// Clear the followup if raised for this order
					$tOrderFollowUpData = [];
					$tOrderFollowUpData['IsCleared'] = 1;
					$tOrderFollowUpData['ClearedReasonUID'] = '';
					$tOrderFollowUpData['ClearedRemarks'] = ''; // FollowUp is cleared because workflow is force completed.
					$tOrderFollowUpData['ClearedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderFollowUpData['ClearedDateTime'] = date('Y-m-d H:i:s');

					$this->db->where(array(
						'OrderUID'=>$OrderUID,
						'QueueUID'=>$queue->QueueUID,
						'WorkflowModuleUID'=>$WorkflowModuleUID,
						'IsCleared'=>0
					));
					$this->db->update('tOrderFollowUp', $tOrderFollowUpData);	

					if($this->db->affected_rows()) {
						$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> followup is cleared.';
					}
				}	

				// Clear the followup if raised for this order
				$tOrderFollowUpData = [];
				$tOrderFollowUpData['IsCleared'] = 1;
				$tOrderFollowUpData['ClearedReasonUID'] = '';
				$tOrderFollowUpData['ClearedRemarks'] = ''; // FollowUp is cleared because workflow is force completed.
				$tOrderFollowUpData['ClearedByUserUID'] = $this->config->item('Cron_UserUID');
				$tOrderFollowUpData['ClearedDateTime'] = date('Y-m-d H:i:s');

				$this->db->where(array(
					'OrderUID'=>$OrderUID,
					'WorkflowModuleUID'=>$WorkflowModuleUID,
					'IsCleared'=>0
				));
				$this->db->update('tOrderFollowUp', $tOrderFollowUpData);	

				if($this->db->affected_rows()) {
					$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> followup is cleared.';
				}					

				// Check is assignment row available
				$is_assignment_row_available = '';
				$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID]);

				if (empty($is_assignment_row_available)) {	

					$tOrderAssignmentsData = [];
					$tOrderAssignmentsData['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
					$tOrderAssignmentsData['OrderUID'] = $OrderUID;
					$tOrderAssignmentsData['WorkflowModuleUID'] = $WorkflowModuleUID;
					$tOrderAssignmentsData['AssignedToUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderAssignmentsData['AssignedDatetime'] = date('Y-m-d H:i:s');
					$tOrderAssignmentsData['AssignedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderAssignmentsData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderAssignmentsData['CompleteDateTime'] = date('Y-m-d H:i:s');
					$this->db->insert('tOrderAssignments', $tOrderAssignmentsData);

					if($this->db->affected_rows()) {
						$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> workflow was force completed.';
					}
				}
				elseif($is_assignment_row_available->WorkflowStatus != $this->config->item('WorkflowStatus')['Completed']) {

					$filter = [];
					$filter['OrderUID'] = $OrderUID;
					$filter['WorkflowModuleUID'] = $WorkflowModuleUID;
					
					$tOrderAssignmentsData = [];
					$tOrderAssignmentsData['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
					$tOrderAssignmentsData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderAssignmentsData['CompleteDateTime'] = date('Y-m-d H:i:s');
					$this->db->where($filter);
					$this->db->update('tOrderAssignments', $tOrderAssignmentsData);

					if($this->db->affected_rows()) {
						$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> workflow was force completed.';
					}

				}	

				if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {

					// Cd inflow If Workup Associate is not an technical or support team Cd should be assigned to them
					$tOrderAssignmentsData = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

					$CheckCDWorkflowIsEnabled = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['CD'], 'tOrderWorkflows.IsPresent' => STATUS_ONE]);
					
					if (!empty($tOrderAssignmentsData->AssignedToUserUID) && !in_array($tOrderAssignmentsData->AssignedToUserUID, $this->config->item('ReportSkippedUsers')) && !empty($CheckCDWorkflowIsEnabled)) {	

						$query = 0;

						$CD_tOrderAssignmentsData = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['CD']]);

						if (!empty($CD_tOrderAssignmentsData)) {
							
							if (empty($CD_tOrderAssignmentsData->AssignedToUserUID)) {
								
								$tOrderAssignmentsArray = array(
									'AssignedToUserUID' => $tOrderAssignmentsData->AssignedToUserUID,
									'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
									'AssignedByUserUID' => $this->config->item('Cron_UserUID'),
									'WorkflowStatus' => $this->config->item('WorkflowStatus')['Assigned']
								);
								$query = $this->Common_Model->save('tOrderAssignments', $tOrderAssignmentsArray, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['CD']]);
							}
						} else {
						
							$tOrderAssignmentsArray = array(
								'OrderUID' => $OrderUID,
								'WorkflowModuleUID' => $this->config->item('Workflows')['CD'],
								'AssignedToUserUID' => $tOrderAssignmentsData->AssignedToUserUID,
								'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
								'AssignedByUserUID' => $this->config->item('Cron_UserUID'),
								'WorkflowStatus' => $this->config->item('WorkflowStatus')['Assigned']
							);
							$query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);
						}		

						if ($query) {

							$workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID', $this->config->item('Workflows')['CD'])->get()->row();
							$assigneduser_row = $this->db->select('UserName')->from('mUsers')->where('UserUID', $tOrderAssignmentsData->AssignedToUserUID)->get()->row();

							// Add log
							$CompleteGivenWorkflowsLog.= '<br/><b>'.$workflow_names->WorkflowModuleName.'</b>  is assigned to '.$assigneduser_row->UserName;
						}
						
					}
				}

				/**
				*Complete Re-Work Queue is Enabled 
				*@author SathishKumar <sathish.kumar@avanzegroup.com>
				*@since Friday 28 August 2020.
				*/
				$tOrderReWorkDetails = $this->Common_Model->get_row('tOrderReWork', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

				if (!empty($tOrderReWorkDetails)) {
					
					$tOrderReWorkData = [];
					$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ZERO;
					$tOrderReWorkData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
					$tOrderReWorkData['CompletedDateTime'] = date('Y-m-d H:i:s');

					$this->Common_Model->save('tOrderReWork', $tOrderReWorkData, ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

					// Add log
					$CompleteGivenWorkflowsLog.= '<br/><b>Re-Work</b> queue is force completed.';
				}

			}				
		}

		return $CompleteGivenWorkflowsLog;
	}

	function Order_Number()
	{

		$date = date("y");

		$id = sprintf("%06d", 0);
		$code="S";
		$lastOrderNo = $code . $date . $id;

		$last_row = $this->db->select('*')->order_by('OrderUID', "DESC")->limit(1)->get('tOrders')->row();
		if (!empty($last_row)) {

			$lastOrderNo = $last_row->OrderNumber;

		}

		$db_2digitdate = substr($lastOrderNo, strlen($code), strlen($date));


		if ($date == $db_2digitdate) {

			$lastOrderNosliced = substr($lastOrderNo, (strlen($code) + strlen($date)));
			$id = sprintf("%06d", $lastOrderNosliced + 1);
			$OrderNumber = $code . $date . $id;
		} else {
			$id = sprintf("%06d", 1);
			$OrderNumber = $code . $date . $id;

		}	
		return $OrderNumber;
	}

	function Package_Number($ProductUID)
	{

		$mProducts = $this->Common_Model->get_row('mProducts', ['ProductUID'=>$ProductUID]);


		$date = date("y") % 20;

		$id = sprintf("%06d", 0);

		$code = "P";
		
		if (!empty($mProducts)) {
			
			$code=$mProducts->ProductCode;
		}

		$code = substr($code, 0, 1);

		$lastOrderNo = $code . $date . $id;

		$last_row = $this->db->select('*')->order_by('PackageUID', "DESC")->limit(1)->get('tOrderPackage')->row();
		if (!empty($last_row)) {

			$lastOrderNo = $last_row->PackageNumber;

		}


		$year = substr($date, strpos($lastOrderNo, $date));

		$db_2digitdate = substr($lastOrderNo, strlen($code), strlen($date));


		if ($year == $db_2digitdate) {

			$lastOrderNosliced = substr($lastOrderNo, (strlen($code) + strlen($date)));
			$id = sprintf("%06d", $lastOrderNosliced + 1);
			$PackageNumber = $code . $date . $id;
		} else {
			$id = sprintf("%06d", 1);
			$PackageNumber = $code . $date . $id;

		}

		return $PackageNumber;

	}

	
	function UpdateStatus($OrderUID,$status)
	{
		$this->db->set('StatusUID',$status);
		$this->db->where(array('OrderUID'=>$OrderUID));
		$this->db->update('tOrders');
	}

	function GetLenderByProjectUIDAndLenderName($ProjectUID, $LenderName)
	{
		$this->db->select('*')->from('mProjectLender');
		$this->db->join('mLender', 'mProjectLender.LenderUID = mLender.LenderUID');
		
		$this->db->where('mProjectLender.ProjectUID', $ProjectUID);
		$this->db->where('mLender.LenderName', $LenderName);
		return $this->db->get()->row();
	}

	/*^^^^   Insert Date in tOrderAssignment Table  ^^^^*/
	function InsertOrderWorkflow($OrderWorkflows, $OrderUID)
	{

		$mWorkFlowModules = $this->Common_Model->get('mWorkFlowModules', ['Active'=>1]);

		foreach ($mWorkFlowModules as $key => $value) {
			
		}
		if (!empty($mProjectCustomer) && !empty($OrderUID)) {
			$tOrderAssignment = new stdClass();

			$tOrderAssignment->HasStacking = $mProjectCustomer->IsStacking;
			$tOrderAssignment->HasAuditing = $mProjectCustomer->IsAuditing;
			$tOrderAssignment->HasReview = $mProjectCustomer->IsReview;
			$tOrderAssignment->HasDocumentCheckIn = $mProjectCustomer->IsDocumentCheckIn;
			$tOrderAssignment->HasShipping = $mProjectCustomer->IsShipping;
			$tOrderAssignment->HasExport = $mProjectCustomer->IsExport;
			$tOrderAssignment->OrderUID = $OrderUID;
			$tOrderAssignment->ProjectUID = $mProjectCustomer->ProjectUID;

			$this->db->insert('tOrderAssignment', $tOrderAssignment);
		}
	}

	/**
	*Function get customer workflows to be inserted 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 25 July 2020.
	*/
	public function GetOrderWorkflowsentryWorkflows($OrderUID)
	{
    $this->db->select('mCustomerWorkflowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName');
		$this->db->from('tOrders');
		$this->db->join('mCustomerWorkflowModules', 'tOrders.CustomerUID=mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWorkflows WHERE tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID)", NULL, FALSE);
		$this->db->where('tOrders.OrderUID', $OrderUID);
		return $this->db->get()->result();
	}

	function InsertOrderworkflow_ALL($OrderUID, $CustomerUID)
	{
		$OrderWorkflows = $this->GetOrderWorkflowsentryWorkflows($OrderUID);

		foreach ($OrderWorkflows as $key => $value) {

			$tOrderWorkflows = [];

			$tOrderWorkflows['OrderUID'] = $OrderUID;
			$tOrderWorkflows['WorkflowModuleUID'] = $value->WorkflowModuleUID;

			$tOrderWorkflows['IsPresent'] = STATUS_ZERO;
			$tOrderWorkflows['EntryDateTime'] = NULL;
			$tOrderWorkflows['DueDateTime'] = NULL;
			$tOrderWorkflows['IsPresent'] = STATUS_ONE;

			if(!$this->is_workflowdependentexists($OrderUID,$value->WorkflowModuleUID) && $value->WorkflowModuleUID != $this->config->item('Workflows')['Submissions'] && $this->Is_Workflow_Enable($OrderUID, $value->WorkflowModuleUID, $CustomerUID)) {
				$tOrderWorkflows['EntryDateTime'] = date('Y-m-d H:i:s', strtotime("now"));
				$tOrderWorkflows['DueDateTime'] = calculate_workflowduedatetime($OrderUID,$value->WorkflowModuleUID);
				$tOrderWorkflows['IsAssign'] = STATUS_ONE;


				//check parking enabled
				$parking = $this->Common_Model->is_autoparking_enabledfor_orderworkflow($OrderUID,$value->WorkflowModuleUID);
				if(!empty($parking)) {
					$is_parking_row_available = $this->Common_Model->get_row('tOrderParking', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $value->WorkflowModuleUID,'IsCleared'=>0]);

					if (empty($is_parking_row_available)) {					
						$parkingdata['OrderUID'] = $OrderUID;
						$parkingdata['WorkflowModuleUID'] = $value->WorkflowModuleUID;
						$parkingdata['ReasonUID'] = '';
						$parkingdata['Remainder'] = isset($parking->ParkingDuration) && !empty($parking->ParkingDuration) ? date('Y-m-d H:i:s', strtotime('+' . $parking->ParkingDuration . ' Hours')) : NULL;
						$parkingdata['Remarks'] = sprintf($this->lang->line('autoparking_assigned'),$parking->WorkflowModuleName,$parking->ParkingDuration,site_datetimeformat(date('Y-m-d H:i:s', strtotime('+' . $parking->ParkingDuration . ' Hours'))));
						$parkingdata['RaisedByUserUID'] = $this->loggedid;
						$parkingdata['RaisedDateTime'] = date('Y-m-d H:i:s');
						$this->Common_Model->save('tOrderParking', $parkingdata);

						$notesdata = array(
							'OrderUID'=>$OrderUID,
							'WorkflowUID'=> $value->WorkflowModuleUID,
							'Description'=> nl2br($parkingdata['Remarks'] ),
							'Module'=> $parking->SystemName,
							'CreatedByUserUID'=> $this->loggedid,
							'CreateDateTime'=> date('Y-m-d H:i:s'));
						$this->db->insert('tNotes',$notesdata);
					}
				}


			} else {
				$tOrderWorkflows['IsPresent'] = STATUS_ZERO;
			}

			//insert workflow here
			if(!empty($tOrderWorkflows)) {
				$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);
			}

		}

	}

	function InsertOrder_OrderTypes($OrderUID, $ProductUID)
	{
		$mProductOrderTypes = $this->Common_Model->get('mProductOrderTypes', ['ProductUID']);
		$tOrderOrderTypes_Array = [];
		foreach ($mProductOrderTypes as $key => $value) {
			$tOrderOrderTypes['OrderUID'] = $OrderUID;
			$tOrderOrderTypes['OrderTypeUID'] = $value->OrderTypeUID;
			$tOrderOrderTypes_Array[] = $tOrderOrderTypes;
		}

		if ($this->db->insert_batch('tOrderOrderTypes_Array', $tOrderOrderTypes_Array)) {
			return true;
		}
		else return false;


	}
	/*^^^^^ Get Waiting for Image Orders ^^^^^ */

	function GetWaitingForImageOrders()
	{
		$status[] = $this->config->item('keywords')['NewOrder'];
		$status[] = $this->config->item('keywords')['Waiting For Images'];
		$this->db->select('*')->from('tOrders');
		$this->db->where_in('tOrders.StatusUID', $status);
		$this->db->order_by('tOrders.ProjectUID', 'ASC');
		return $this->db->get()->result();
	}

	/*^^^^^ Get Waiting for Image Orders ^^^^^ */

	function GetOrderByOrderUID($OrderUIDs)
	{
		$this->db->select('*')->from('tOrders');
		$this->db->where_in('tOrders.OrderUID', $OrderUIDs);
		$this->db->order_by('tOrders.OrderUID', 'ASC');
		return $this->db->get()->result();
	}

	/*^^^^^ Get Waiting for Image Orders ^^^^^ */

	function GetOrdersByOrderUIDs_GroupByProjectUID($OrderUIDs)
	{
		$this->db->select('ProjectUID, Group_Concat(OrderUID) AS OrderUIDs', false)->from('tOrders');
		$this->db->where_in('tOrders.OrderUID', $OrderUIDs);
		$this->db->order_by('tOrders.OrderUID', 'ASC');
		$this->db->group_by('tOrders.ProjectUID');
		return $this->db->get()->result();
	}

	/*^^^^^ Get Waiting for Image Orders Group By ProjectUID^^^^^ */

	function GetWaitingForImageOrders_GroupByProjectUID()
	{
		$status[] = $this->config->item('keywords')['NewOrder'];
		$status[] = $this->config->item('keywords')['Waiting For Images'];
		$this->db->select('*, Group_Concat(tOrders.OrderUID) AS OrderUIDs', false);
		$this->db->from('tOrders');
		$this->db->where_in('tOrders.StatusUID', $status);
		$this->db->group_by('tOrders.ProjectUID');
		$this->db->order_by('tOrders.ProjectUID', 'ASC');
		return $this->db->get()->result();
	}

	/*Function to get product based on customer*/
	function get_customerprojects($CustomerUID,$ProductUID)
	{
		$this->db->select("ProjectUID,ProjectName,ProjectCode");
		$this->db->from('mProjectCustomer');
		$this->db->where(array("CustomerUID"=>$CustomerUID,"ProductUID"=>$ProductUID,'Active'=>1));
		$this->db->order_by("ProjectName");
		return $this->db->get()->result();
	}

		function get_productbyuid($ProductUID){
		$this->db->select("ProductUID,ProductName");
		$this->db->from('mProducts');
		$this->db->where(array("Active"=>1,"ProductUID"=>$ProductUID));
		return $this->db->get()->row();
	}

	function get_customerbyuid($CustomerUID){
		$this->db->select("CustomerCode,CustomerName,CustomerUID,LoanNumberValidation");
		$this->db->from('mCustomer');
		$this->db->where(array("Active"=>1,"CustomerUID"=>$CustomerUID));
		return $this->db->get()->row();
	}

	function get_projectbyuid($ProjectUID){
		$this->db->select("ProjectUID,ProjectName,ProjectCode");
		$this->db->from('mProjectCustomer');
		$this->db->where("ProjectUID",$ProjectUID);
		return $this->db->get()->row();
	}

	function get_lenderbyuid($LenderUID){
		$this->db->select("LenderUID,LenderName");
		$this->db->from('mLender');
		$this->db->where(array("Active"=>1,"LenderUID"=>$LenderUID));
		return $this->db->get()->row();
	}

	function get_projectbyname($ProjectName){

		$this->db->select("ProjectUID,ProjectName,ProjectCode");
		$this->db->from('mProjectCustomer');
		$this->db->where("ProjectName",$ProjectName);
		return $this->db->get()->row();
	}

	function get_productbyname($ProductName){

		$query = $this->db->query("SELECT ProductUID,ProductName FROM (`mProducts`) WHERE `Active` = 1 AND (`ProductName` = '".$ProductName."' OR `ProductCode` = '".$ProductName."')");
		return $query->row();
	}


	function getorder_successfailed_data($OrderUID){
		$this->db->select("OrderUID,CustomerName,ProjectName,LenderName,OrderNumber,AltOrderNumber,PropertyAddress1,PropertyZipCode,PropertyCityName,PropertyStateCode,PropertyCountyName");
		$this->db->from('tOrders');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID = tOrders.CustomerUID');
		$this->db->join('mProjectCustomer', '(mProjectCustomer.CustomerUID = tOrders.CustomerUID AND mProjectCustomer.ProductUID = tOrders.ProductUID)','left');
		$this->db->join('mLender', 'mLender.LenderUID = tOrders.LenderUID','left');
		$this->db->where('OrderUID',$OrderUID);
		return $this->db->get()->row();
	}

	function get_clientname_byname($CustomerName){
		$this->db->select("CustomerCode,CustomerName,CustomerUID");
		$this->db->from('mCustomer');
		$this->db->where(array("Active"=>1,"CustomerName"=>$CustomerName));
		return $this->db->get()->row();
	}

	function get_clientcode_bycode($CustomerCode){
		$this->db->select("CustomerUID,CustomerCode,CustomerName");
		$this->db->from('mCustomer');
		$this->db->where(array("Active"=>1,"CustomerCode"=>$CustomerCode));
		return $this->db->get()->row();
	}

	function get_projectby_customer_product($CustomerUID,$ProductUID,$ProjectName){
		$this->db->select("ProjectUID,ProjectName,ProjectCode");
		$this->db->from('mProjectCustomer');
		$this->db->where(array("ProjectName"=>$ProjectName,"CustomerUID"=>$CustomerUID,"ProductUID"=>$ProductUID));
		return $this->db->get()->row();
	}


	function is_loanno_exists($LoanNumber){
		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrders WHERE LoanNumber = '".$LoanNumber."') as avail");
		return $query->row()->avail;
	}

	function check_validdoc($DoCTypeName){
		$this->db->select ( '*' );
		$this->db->from ( 'mInputDocType' );
		$this->db->where('DoCTypeName',$DoCTypeName);
		$query = $this->db->get();
		return $query->row();
	}

	function get_docforproduct($ProductUID,$restricttitlepolicy){
		$this->db->select ( '*' );
		$this->db->from ( 'mProductDocType' );
		$this->db->join('mInputDocType', 'mInputDocType.InputDocTypeUID = mProductDocType.InputDocTypeUID');
		if($restricttitlepolicy == true){

			$this->db->where('mInputDocType.InputDocTypeUID !=',1);
		}
		$this->db->where('ProductUID',$ProductUID);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_docforproductbyName($DoCTypeName,$ProductName){
		$this->db->select ( '*' );
		$this->db->from ( 'mProductDocType' );
		$this->db->join('mInputDocType', 'mInputDocType.InputDocTypeUID = mProductDocType.InputDocTypeUID');
		$this->db->join('mProducts', 'mProducts.ProductUID = mProductDocType.ProductUID');
		$this->db->where('ProductName',$ProductName);
		$this->db->where('DoCTypeName',$DoCTypeName);
		$query = $this->db->get();
		return $query->row();
	}

	function get_docforproductbyuid($DoCTypeName,$ProductUID){
		$this->db->select ( '*' );
		$this->db->from ( 'mProductDocType' );
		$this->db->join('mInputDocType', 'mInputDocType.InputDocTypeUID = mProductDocType.InputDocTypeUID');
		$this->db->join('mProducts', 'mProducts.ProductUID = mProductDocType.ProductUID');
		$this->db->where('mProductDocType.ProductUID',$ProductUID);
		$this->db->where('DoCTypeName',$DoCTypeName);
		$query = $this->db->get();
		return $query->row();
	}

	function GetAllOrders($OrderUIDs)
	{
		$this->db->select('OrderUID');
		$this->db->from('tOrders');
		$this->db->where_in('tOrders.OrderUID', $OrderUIDs);
		return $this->db->get()->result();
	}


	function getUnMergedDocuments()
	{
		$this->db->select('OrderUID, GROUP_CONCAT(DocumentName) AS DocumentName,GROUP_CONCAT(DocumentURL) AS DocumentURL', false);
		$this->db->from('tTempDocuments');
		$this->db->where('ToBeMerged', STATUS_ONE);
		$this->db->group_by('OrderUID');
		return $this->db->get()->result();
	}

	
	function is_loannodoctype_exists($LoanNumber,$InputDocTypeName){
		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrders JOIN mInputDocType ON mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID  WHERE LoanNumber = '".$LoanNumber."' AND DocTypeName = '".$InputDocTypeName."' ) as avail");
		return $query->row()->avail;
	}

	function isloan_doctypevalidation(){
		$query = $this->db->query("SELECT ImportValidation FROM mOrganization LIMIT 1");
		return $query->row();
	}

	function get_docimportrules($ProductUID){
		$query = $this->db->query("SELECT * FROM mProductRules JOIN mImportRules ON mImportRules.RuleUID = mProductRules.RuleUID WHERE mProductRules.ProductUID = '".$ProductUID."' ");
		return $query->result_array();	
	}	




	function is_loannodoctype_matchorder($LoanNumber,$InputDocTypeName){
		$query = $this->db->query("SELECT tOrders.OrderUID,OrderNumber FROM tOrders JOIN mInputDocType ON mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID  WHERE LoanNumber = '".$LoanNumber."' AND DocTypeName = '".$InputDocTypeName."' ");
		return $query->result();
	}

	function get_packagenumber_orderuid($OrderUID){
		$this->db->select('PackageNumber')->from('tOrders');
		$this->db->join('tOrderPackage', 'tOrderPackage.PackageUID = tOrders.PackageUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		return $this->db->get()->row();
	}

	function check_customer_product($CustomerUID,$ProductUID){

		$query = $this->db->query("SELECT EXISTS(SELECT * FROM mCustomerProducts WHERE CustomerUID = '".$CustomerUID."' AND ProductUID = '".$ProductUID."') as avail;
			");
		return $query->row()->avail;

	}

	function checkorderexists($LoanNumber,$InputDocTypeName,$CustomerUID,$ProductUID,$ProjectUID){

		$query = $this->db->query("SELECT tOrders.OrderUID,OrderNumber,CustomerUID,ProjectUID,ProductUID,tOrders.InputDocTypeUID,DocTypeName FROM tOrders JOIN mInputDocType ON mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID  WHERE LoanNumber = '".$LoanNumber."' AND DocTypeName = '".$InputDocTypeName."' AND CustomerUID = '".$CustomerUID."' AND ProductUID = '".$ProductUID."' AND ProjectUID = '".$ProjectUID."' ");
		return $query->row();
	}

	function checkorderexistsbyloan($LoanNumber,$CustomerUID,$ProductUID){

		$query = $this->db->query("SELECT tOrders.OrderUID,OrderNumber,CustomerUID,ProjectUID,ProductUID,tOrders.InputDocTypeUID,DocTypeName FROM tOrders WHERE LoanNumber = '".$LoanNumber."'  AND CustomerUID = '".$CustomerUID."' AND ProductUID = '".$ProductUID."'");
		return $query->result();
	}
	function checkorderexistsbyloannumber($LoanNumber,$CustomerUID,$ProductUID){

		$query = $this->db->query("SELECT tOrders.OrderUID,OrderNumber,CustomerUID,ProjectUID,ProductUID,tOrders.InputDocTypeUID FROM tOrders WHERE LoanNumber = '".$LoanNumber."'  AND CustomerUID = '".$CustomerUID."' AND ProductUID = '".$ProductUID."'");
		return $query->result();
	}
 
	function is_workflowdependentexists($OrderUID,$WorkflowModuleUID) {

		$query = $this->db->query("SELECT EXISTS(SELECT * FROM mCustomerDependentWorkflowModules JOIN tOrders ON  tOrders.CustomerUID = mCustomerDependentWorkflowModules.CustomerUID WHERE tOrders.OrderUID = {$OrderUID} AND mCustomerDependentWorkflowModules.WorkflowModuleUID = {$WorkflowModuleUID}) AS Passed ");
		return $query->row()->Passed;

	}

	function isWorklflowExistsForCustomer($CustomerUID, $WorkflowModuleName) {

		$this->db->select('mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID','left');
		$this->db->where('mCustomerWorkflowModules.CustomerUID',$CustomerUID);
		$this->db->like('mWorkFlowModules.WorkflowModuleName', $WorkflowModuleName);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	function isAssociateExistsForCustoemr($CustomerUID, $UserName) {

		$this->db->select('UserUID');
		$this->db->from('mUsers');
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->like('UserName', $UserName);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	function orderWorkflowUnAssign($OrderUID, $WorkflowModuleUID) {
		//Unassign user
        $Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);


        $is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

        if(!empty($is_assignment_row_available)) {  
            $tOrderAssignments = [];
            $tOrderAssignments['AssignedToUserUID'] = NULL;
            $tOrderAssignments['AssignedDatetime'] = NULL;
            $tOrderAssignments['AssignedByUserUID'] = NULL;

            $assigneduser_row = $this->db->select('UserName')->from('mUsers')->where('UserUID',$is_assignment_row_available->AssignedToUserUID)->get()->row();

            $this->Common_Model->save('tOrderAssignments', $tOrderAssignments, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);

            if(!empty($assigneduser_row)) {

                /*INSERT ORDER LOGS BEGIN*/
                $this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - '.$assigneduser_row->UserName.'  UnAssigned',date('Y-m-d H:i:s'));
                /*INSERT ORDER LOGS END*/
            }
        }

        return true;
	}

	function CheckOrderExistsforCustomer($LoanNumber,$CustomerUID){

		$query = $this->db->query("SELECT tOrders.OrderUID,OrderNumber,CustomerUID,ProjectUID,ProductUID,tOrders.InputDocTypeUID FROM tOrders WHERE LoanNumber = '".$LoanNumber."'  AND CustomerUID = '".$CustomerUID."'");
		return $query->result();
	}

	/**
	*Function Update PayOff Bulk Data
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 11 September 2020.
	*/
	function UpdatePayOffBulkOrders($torderimport)
	{

		$date = date('Ymd');

		$this->db->trans_begin();

		$OrderUID = $torderimport['OrderUID'];	

		/*Update tOrderImport*/
		if(!empty($torderimport)) {
			
			$tOrderImportrow = $this->db->select('1')->from('tOrderImport')->where('OrderUID',$OrderUID)->get()->row();
			if(!empty($tOrderImportrow)) {
				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('tOrderImport', $torderimport);
			} else {
				$this->db->insert('tOrderImport',$torderimport);
			}
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;
		} else {

			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID,'PayOff Update (Bulk)',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/

			$this->db->trans_commit();
			return true;
		}


	}

	/**
	*Function Enable Workflow
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 22 September 2020.
	*/
	function EnableWorkflow($data, $tOrderImport)
	{

		$CustomerUID = $data['CustomerUID'];
		$OrderUID = $data['OrderUID'];
		$WorkflowModuleUID = $data['WorkflowModuleUID'];

		$this->db->trans_begin();

		if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
			
			$tOrderImportrow = $this->db->select('1')->from('tOrderImport')->where('OrderUID',$OrderUID)->get()->row();
			if(!empty($tOrderImportrow)) {
				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('tOrderImport', $tOrderImport);
			} else {
				$this->db->insert('tOrderImport',$tOrderImport);
			}
		}

		// Fetch workflow name			
		$this->db->select('mWorkFlowModules.WorkflowModuleName');
		$WorkflowModuleName = $this->db->get_where('mWorkFlowModules', array('WorkflowModuleUID' => $data['WorkflowModuleUID']))->row()->WorkflowModuleName;

		##################################Parking, Subqueue ,Kickback orders complete########################################
		$CompleteGivenWorkflowsLog = '';

		// Order in parking queue need to delete
		$this->db->where(array(
			'OrderUID'=>$OrderUID,
			'WorkflowModuleUID'=>$WorkflowModuleUID,
			'IsCleared'=>0
		));
		$this->db->delete('tOrderParking');

		if($this->db->affected_rows()) {
			$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> parking queues is deleteed.';
		}

		$Queues = '';
		$Queues = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID, FALSE, $CustomerUID);

		foreach ($Queues as $key => $queue) {

			$this->db->where(array(
				'OrderUID'=>$OrderUID,
				'QueueUID'=>$queue->QueueUID, 
				"QueueStatus" => "Pending"
			));
			$this->db->delete('tOrderQueues');

			if($this->db->affected_rows()) {
				$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.' - '.$queue->QueueName.'</b> sub queue is deleted.';
			}

			// delete the followup if raised for this order
			$this->db->where(array(
				'OrderUID'=>$OrderUID,
				'QueueUID'=>$queue->QueueUID,
				'WorkflowModuleUID'=>$WorkflowModuleUID,
				'IsCleared'=>0
			));
			$this->db->delete('tOrderFollowUp');	

			if($this->db->affected_rows()) {
				$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.' - '.$queue->QueueName.'</b> followup is deleted.';
			}
		}	

		// delete the followup if raised for this order
		$this->db->where(array(
			'OrderUID'=>$OrderUID,
			'WorkflowModuleUID'=>$WorkflowModuleUID,
			'IsCleared'=>0
		));
		$this->db->delete('tOrderFollowUp');	

		if($this->db->affected_rows()) {
			$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> followup is deleted.';
		}	

		// delete if Re-Work Queue is Enabled
		$this->db->where(array(
			'OrderUID'=>$OrderUID,
			'WorkflowModuleUID'=>$WorkflowModuleUID,
			'IsReWorkEnabled'=>STATUS_ONE
		));
		$this->db->delete('tOrderReWork');	

		if($this->db->affected_rows()) {
			$CompleteGivenWorkflowsLog.= '<br/><b>'.$WorkflowModuleName.'</b> - <b>Re-Work</b> queue is deleted.';
		}

		//duplicate to tOrderAssignments History 
		$this->db->select('OrderUID,WorkflowModuleUID,AssignedToUserUID,AssignedDatetime,AssignedByUserUID,WorkflowStatus,CompletedByUserUID,CompleteDateTime,IsQCSkipped,UserProjectSkip,Remarks,OrderFlag,NOW() AS CreatedDateTime');

		$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID));
		$tOrderAssignments = $this->db->get('tOrderAssignments');

		if($tOrderAssignments->num_rows()) {
			$tOrderAssignments_History = $this->db->insert_batch('tOrderAssignmentsHistory', $tOrderAssignments->result_array());

			//delete tOrderAssignments
			$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID));
			$this->db->delete('tOrderAssignments');			
		}
		##################################Parking, Subqueue ,Kickback orders complete End####################################

		$tOrderWorkflowrow = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$data['WorkflowModuleUID']]);

		// Enable workflow
		if(!empty($tOrderWorkflowrow)){

			$tOrderWorkflows['IsPresent'] = STATUS_ONE;
			$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrow->EntryDateTime) ? $tOrderWorkflowrow->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
			$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrow->DueDateTime) ? $tOrderWorkflowrow->DueDateTime : calculate_workflowduedatetime($OrderUID,$data['WorkflowModuleUID']);
			$tOrderWorkflows['IsAssign'] = STATUS_ONE;
			$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
			$tOrderWorkflows['ReversedByUserUID'] = NULL;
			$tOrderWorkflows['ReversedRemarks'] = NULL;
			$tOrderWorkflows['ReversedDateTime'] = NULL;

			if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
			} else {
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ZERO;
			}

			$update = $this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $data['WorkflowModuleUID']]);

		} else {
			$tOrderWorkflows['OrderUID'] = $OrderUID;
			$tOrderWorkflows['WorkflowModuleUID'] = $data['WorkflowModuleUID'];
			$tOrderWorkflows['IsPresent'] = STATUS_ONE;
			$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrow->EntryDateTime) ? $tOrderWorkflowrow->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
			$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrow->DueDateTime) ? $tOrderWorkflowrow->DueDateTime : calculate_workflowduedatetime($OrderUID,$data['WorkflowModuleUID']);
			$tOrderWorkflows['IsAssign'] = STATUS_ONE;
			$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
			$tOrderWorkflows['ReversedByUserUID'] = NULL;
			$tOrderWorkflows['ReversedRemarks'] = NULL;
			$tOrderWorkflows['ReversedDateTime'] = NULL;

			if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
			} else {
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ZERO;
			}

			$update = $this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);

		}

		//IF Workup enabled GateKeeping to be enabled start
		if($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
			$GateKeepingWorkflowModuleUID = $this->config->item('Workflows')['GateKeeping'];
			$tOrderWorkflowrowGateKeeping = $this->Common_Model->get_row('`tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$GateKeepingWorkflowModuleUID]);
			if(!empty($tOrderWorkflowrowGateKeeping)){

				$tOrderWorkflows['IsPresent'] = STATUS_ONE;
				$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowGateKeeping->EntryDateTime) ? $tOrderWorkflowrowGateKeeping->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
				$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowGateKeeping->DueDateTime) ? $tOrderWorkflowrowGateKeeping->DueDateTime : calculate_workflowduedatetime($OrderUID,$GateKeepingWorkflowModuleUID);
				$tOrderWorkflows['IsAssign'] = STATUS_ONE;
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
				$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
				$tOrderWorkflows['ReversedByUserUID'] = NULL;
				$tOrderWorkflows['ReversedRemarks'] = NULL;
				$tOrderWorkflows['ReversedDateTime'] = NULL;
				$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $GateKeepingWorkflowModuleUID]);

			}	else {

				$tOrderWorkflows['OrderUID'] = $OrderUID;
				$tOrderWorkflows['WorkflowModuleUID'] = $GateKeepingWorkflowModuleUID;
				$tOrderWorkflows['IsPresent'] = STATUS_ONE;
				$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowGateKeeping->EntryDateTime) ? $tOrderWorkflowrowGateKeeping->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
				$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowGateKeeping->DueDateTime) ? $tOrderWorkflowrowGateKeeping->DueDateTime : calculate_workflowduedatetime($OrderUID,$GateKeepingWorkflowModuleUID);
				$tOrderWorkflows['IsAssign'] = STATUS_ONE;
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
				$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
				$tOrderWorkflows['ReversedByUserUID'] = NULL;
				$tOrderWorkflows['ReversedRemarks'] = NULL;
				$tOrderWorkflows['ReversedDateTime'] = NULL;
				$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);

			}					

		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;
		} else {

			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID,'Order Force Enable (Bulk)',Date('Y-m-d H:i:s'), $this->loggedid);
			/*INSERT ORDER LOGS END*/

			/*INSERT ORDER LOGS BEGIN*/
			if (!empty($CompleteGivenWorkflowsLog)) {
				
				$this->Common_Model->OrderLogsHistory($OrderUID,'Order Force Enable (Bulk)'.$CompleteGivenWorkflowsLog,Date('Y-m-d H:i:s'), $this->config->item('Cron_UserUID'));
			}			
			/*INSERT ORDER LOGS END*/

			$this->db->trans_commit();
			return true;
		}


	}

	/**
	*Function Check Borrower Exist for the order 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 23 September 2020.
	*/
	public function CheckBorrowerExistOrder($OrderUID,$BorrowerName)
	{
		$this->db->select('1');
		$this->db->from('tOrderPropertyRole');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->like('BorrowerFirstName',$BorrowerName);
		if ($this->db->get()->num_rows() > 0) {
			return false;
		}
		return true;
		
	}

	/**
	*Function Check Milestone Exist for the order 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 23 September 2020.
	*/
	public function CheckMilestoneExistOrder($OrderUID,$Milestone)
	{
		$this->db->select('1');
		$this->db->from('tOrders');
		$this->db->join('mMilestone','mMilestone.MilestoneUID = tOrders.MilestoneUID');
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$this->db->where('mMilestone.MilestoneName',$Milestone);
		if ($this->db->get()->num_rows() > 0) {
			return false;
		}
		return true;
		
	}

	/**
	*Function Check Milestone Exist for the order 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 23 September 2020.
	*/
	public function CheckLoantypeExistOrder($OrderUID,$Milestone)
	{
		$this->db->select('1');
		$this->db->from('tOrders');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('LoanType',$Milestone);
		if ($this->db->get()->num_rows() > 0) {
			return false;
		}
		return true;
		
	}

	/**
	*Function DocsOut Update 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 30 October 2020.
	*/
	function updatedocsout_order($updatedata,$tpropdata, $torderimport = [])
	{

		$date = date('Ymd');

		$this->db->trans_begin();

		$OrderUID = $updatedata['OrderUID'];

		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('tOrders', $updatedata);

		/*tOrderPropertyroles data*/
		foreach ($tpropdata as $tpropkey => $tpropvalue) {
			$tpropdata[$tpropkey]['OrderUID'] = $OrderUID;

			$tOrderPropertyRolerow = $this->db->select('1')->from('tOrderPropertyRole')->where('OrderUID',$OrderUID)->get()->row();
			if(!empty($tOrderPropertyRolerow)) {
				$this->db->where('OrderUID', $OrderUID);
				$this->db->limit(1);
				$this->db->update('tOrderPropertyRole', $tpropdata[$tpropkey]);
			} else {
				$this->db->insert('tOrderPropertyRole', $tpropdata[$tpropkey]);
			}
		}		

		/*Update tOrderImport*/
		if(!empty($torderimport)) {
			
			$tOrderImportrow = $this->db->select('1')->from('tOrderImport')->where('OrderUID',$OrderUID)->get()->row();
			if(!empty($tOrderImportrow)) {
				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('tOrderImport', $torderimport);
			} else {
				$this->db->insert('tOrderImport',$torderimport);
			}
		}

		//Order Workflows updates
		$MilestoneUID = $updatedata['MilestoneUID'];
		$CustomerUID = $updatedata['CustomerUID'];
		$this->OrderWorkflowsUpdates($OrderUID, $CustomerUID, $MilestoneUID, $torderimport);

		// Update Order Workflow
		$this->UpdateOrderworkflow_ALL($OrderUID, $updatedata['CustomerUID']);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;
		} else {

			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID,'Order Update (Bulk)',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/

			$this->db->trans_commit();
			return true;
		}


	}

	/**
	*Function Check is workflow enable
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Saturday 31 October 2020.
	*/
	function Is_Workflow_Enable($OrderUID, $WorkflowModuleUID, $CustomerUID, $Conditions = []) {

		// DocsOut Conditions
		if ($WorkflowModuleUID == $this->config->item('Workflows')['DocsOut']) {
			
			// Workflow metrics setup
			$this->db->select('State, LoanTypeName, PropertyType, MilestoneUID');
			$this->db->from('mCustomerWorkflowModules');
			$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowModuleUID));
			$WorkflowMetrics = $this->db->get()->row_array();		
			// Workflow metrics setup end

			$this->db->select('1');
			$this->db->from('tOrders');
			$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','left');
			$this->db->where('tOrders.OrderUID', $OrderUID);

			// Workflow metrics
			if (!empty($WorkflowMetrics['State'])) {
				$WorkflowState = explode(',', $WorkflowMetrics['State']);
				$this->db->where_in('tOrders.PropertyStateCode', $WorkflowState);
			}

			if (!empty($WorkflowMetrics['LoanTypeName'])) {
				$WorkflowLoanTypeName = explode(',', $WorkflowMetrics['LoanTypeName']);
				$this->db->where_in('tOrders.LoanType', $WorkflowLoanTypeName);
			}

			if (!empty($WorkflowMetrics['PropertyType'])) {
				$this->db->like('tOrderImport.PropertyType', $WorkflowMetrics['PropertyType']);
			}

			if (!empty($WorkflowMetrics['MilestoneUID'])) {
				$WorkflowMilestoneUID = explode(',', $WorkflowMetrics['MilestoneUID']);
				$this->db->where_in('tOrders.MilestoneUID', $WorkflowMilestoneUID);
			}

			// All workflow eliminated milestones
			$Workflows_EliminatedMilestones = $this->config->item('Workflows_EliminatedMilestones');
			$this->db->group_start();
			$this->db->where('tOrders.MilestoneUID IS NULL', NULL, FALSE);
			$this->db->or_where_not_in('tOrders.MilestoneUID', $Workflows_EliminatedMilestones);
			$this->db->group_end();
				
			// Docs Out queue condition

			// DocsOutSigningDate is not blank
			$this->db->where("(tOrderImport.DocsOutSigningDate IS NOT NULL AND tOrderImport.DocsOutSigningDate <> '') ", NULL, FALSE);

			// DocsOutClosingDisclosureSendDate is not blank
			$this->db->where("(tOrderImport.DocsOutClosingDisclosureSendDate IS NOT NULL AND tOrderImport.DocsOutClosingDisclosureSendDate <> '')");

			$this->db->group_start();

			// New Order Queue blank
			if (!isset($Conditions['Exclude'])) {
				$this->db->group_start();
				$this->db->where('(tOrderImport.Queue IS NULL OR tOrderImport.Queue = "")',NULL,FALSE);
				$this->db->or_where('tOrderImport.Queue', '0');
				$this->db->group_end();
			}			

			$this->db->or_group_start();

			// "Docs Checked Conditions Pending" Sub queue conditions
			$QueueTypes = $this->config->item("DocsCheckedConditionPending");
			$this->db->where_in('tOrderImport.Queue',$QueueTypes);
			$this->db->group_end();

			$this->db->or_group_start();

			// "Pending from UW" Sub queue conditions
			$QueueTypes = $this->config->item("PendingfromUW");
			$this->db->where_in('tOrderImport.Queue',$QueueTypes);
			$this->db->group_end();

			$this->db->or_group_start();

			// "Submitted for Doc Check" Sub queue conditions
			$QueueTypes = $this->config->item("SubmittedforDocCheckCond");
			$this->db->where_in('tOrderImport.Queue',$QueueTypes);
			$this->db->group_end();

			$this->db->group_end();

			if($this->db->count_all_results() > 0) {
				return TRUE;
			} else {
				return FALSE;
			}
		}			

		return TRUE;
	}

	/**
	*Function Update Order Workflow for All 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 02 November 2020.
	*/
	function UpdateOrderworkflow_ALL($OrderUID, $CustomerUID)
	{
		$UpdateWorkflows = [
			$this->config->item('Workflows')['DocsOut']
		];
		$OrderWorkflows = $this->GetOrderWorkflows($OrderUID, $UpdateWorkflows);

		foreach ($OrderWorkflows as $key => $value) {

			if($this->OrderComplete_Model->check_alldependentworkflow_completed($OrderUID,$CustomerUID,$value->WorkflowModuleUID, ['OrderEntry'=>TRUE]) && $this->Is_Workflow_Enable($OrderUID, $value->WorkflowModuleUID, $CustomerUID)) {

				$tOrderWorkflowrow = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$value->WorkflowModuleUID]);

				if(!empty($tOrderWorkflowrow)){

					$tOrderWorkflows = [];

					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrow->EntryDateTime) && $tOrderWorkflowrow->EntryDateTime != '0000-00-00 00:00:00' ?  $tOrderWorkflowrow->EntryDateTime :date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrow->DueDateTime) && $tOrderWorkflowrow->DueDateTime != '0000-00-00 00:00:00' ?  $tOrderWorkflowrow->DueDateTime : calculate_workflowduedatetime($OrderUID,$value->WorkflowModuleUID);
					$tOrderWorkflows['IsAssign'] = STATUS_ONE;

					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $value->WorkflowModuleUID]);

				}


			}

		}

	}	

	/**
	*Function get customer workflows to be inserted 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 25 July 2020.
	*/
	public function GetOrderWorkflows($OrderUID, $UpdateWorkflows)
	{
    	$this->db->select('mCustomerWorkflowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName');
		$this->db->from('tOrders');
		$this->db->join('mCustomerWorkflowModules', 'tOrders.CustomerUID=mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->where("EXISTS (SELECT 1 FROM tOrderWorkflows WHERE tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID)", NULL, FALSE);
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where_in('mCustomerWorkflowModules.WorkflowModuleUID',$UpdateWorkflows);
		return $this->db->get()->result();
	}
	
}
?>
