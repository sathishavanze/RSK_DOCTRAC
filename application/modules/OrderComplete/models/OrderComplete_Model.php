<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class OrderComplete_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}


	/** @author Mansoor Ali <mansoor.ali@avanzegroup.com> **/
	/** @date Wednesday 25 November 2020 **/
	/** @description Workflow Due Date Restriction  **/

	function DueDateRestrict($OrderUID)
	{

		$CustomerUID = $this->parameters['DefaultClientUID'];

		$this->db->select('count(*) as Result');
		$this->db->from('mCustomer');
		$this->db->join('tOrderImport','tOrderImport.OrderUID ='.$OrderUID,'LEFT');
		$this->db->group_start();
		$this->db->where("FIND_IN_SET(DATE_FORMAT(STR_TO_DATE(tOrderImport.NextPaymentDue, '%m/%d/%Y'), '%m/%d/%Y'), mCustomer.NextPaymentDueRestriction)<>0");
		$this->db->or_where("FIND_IN_SET(DATE_FORMAT(STR_TO_DATE(tOrderImport.NextPaymentDue, '%m-%d-%Y'), '%m/%d/%Y'), mCustomer.NextPaymentDueRestriction)<>0");
		$this->db->group_end();

		$this->db->where('mCustomer.CustomerUID',$CustomerUID);
		return $this->db->get()->row()->Result;
	}

	function OrderOnHold($OrderUID,$remarks){
		// $OrderUID = $this->input->post('OrderUID');
		// $remarks=$this->input->post('remarks');
		// $Onholdtype=$this->input->post('Onholdtype');
		
		// BinOrders Delete Begin
		if(!empty($OrderUID)){
			$this->db->where('tBinOrders.OrderUID',$OrderUID);
			$this->db->delete('tBinOrders');
		}
	    // BinOrders Delete End 

		$this->db->select('StatusUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$OrderDetails = $this->db->get()->row();

		$IsOnHoldUpdate=array('tOrders.IsOnHold' => 1);
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$this->db->update('tOrders',$IsOnHoldUpdate);

		$InsertOrderOnHoldDet = ['OrderUID' => $OrderUID,'StatusUID'=>$OrderDetails->StatusUID,'OnHoldStatus'=>'OnHold','Remarks'=>$remarks,'AssignedUserUID'=>$this->loggedid,'OnholdDateTime'=>date('Y-m-d H:i:s')];

		$this->db->insert('tOrderOnhold',$InsertOrderOnHoldDet);
		$OnHoldUID = $this->db->insert_id();

		$OnHoldUIDdUpdate=array('tOrders.OnHoldUID' => $OnHoldUID);
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$this->db->update('tOrders',$OnHoldUIDdUpdate);

		if ($this->db->affected_rows() > 0) {
			return 1;
		}
		else{
			return 0;
		}
	}
	function ReleaseOnHold($OrderUID,$comments,$OnHoldUID){
		$this->db->select('StatusUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$OrderDetails = $this->db->get()->row();

		$IsOnHoldUpdate=array('tOrders.IsOnHold' => 0);
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$this->db->update('tOrders',$IsOnHoldUpdate);


		$ReleaseUpdate=array('tOrderOnhold.OnHoldStatus' =>'Release','tOrderOnhold.Comments'=>$comments,'tOrderOnhold.ReleaseDateTime'=>date('Y-m-d H:i:s'));
		$this->db->where('tOrderOnhold.OnHoldUID',$OnHoldUID);
		$this->db->update('tOrderOnhold',$ReleaseUpdate);

		if ($this->db->affected_rows() > 0) {
			return 1;
		}
		else{
			return 0;
		}
	}

	function OrderDetails($OrderUID){
		$this->db->select('*');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$OrderDetails = $this->db->get()->row();
		return $OrderDetails;
	}

	function DeleteOrderAssignment($OrderUID){
		$DeleteOrderWorkflow = array('OrderUID' => $OrderUID,'WorkflowModuleUID' =>$this->config->item('Workflows')['Exception']);
		$this->db->where($DeleteOrderWorkflow);
		$this->db->delete('tOrderAssignments');

	}

		// BinOrders Delete Begin
	function DeleteBinOrder($OrderUID){
		if(!empty($OrderUID)){
			$this->db->where('tBinOrders.OrderUID',$OrderUID);
			$this->db->delete('tBinOrders');
		}
	}
		// BinOrders Delete End

	function change_torderworkflowspresent($OrderUID,$CustomerUID,$dependentworkflows,$CheckedWorkflows,$modalcheckboxconfirmed) {
		//$dependentworkflowuids = array_column($dependentexistsarray, 'WorkflowModuleUID');
		if(!empty($dependentworkflows)) {
			foreach ($dependentworkflows as $key => $dependentworkflow) {



				$tOrderworkflowarray = [];
				$WorkflowModuleUID = $dependentworkflow->WorkflowModuleUID;
				//get workflow 
				$customerworkflowrow = $this->get_customer_workflow($CustomerUID,$WorkflowModuleUID);

				$tOrderWorkflowrow = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);
				
				//if force enabled skip torderworkflow changes
				if($tOrderWorkflowrow->IsForceEnabled == 1) {
					continue;
				}

				$tOrderworkflowarray['OrderUID'] = $OrderUID;
				$tOrderworkflowarray['IsAssign'] = STATUS_ONE;
				$tOrderworkflowarray['IsPresent'] = STATUS_ONE;
				$tOrderworkflowarray['WorkflowModuleUID'] = $WorkflowModuleUID;


				if(!empty($customerworkflowrow) && $customerworkflowrow->Optional == 1 && !in_array($WorkflowModuleUID, $CheckedWorkflows) && !empty($modalcheckboxconfirmed)) {
					$tOrderworkflowarray['IsPresent'] = STATUS_ZERO;
				}	


				if($this->check_alldependentworkflow_completed($OrderUID,$CustomerUID,$WorkflowModuleUID)) {

					/*update entrydatetime duedatetime for workflow*/
					$tOrderworkflowarray['EntryDateTime'] = !empty($tOrderWorkflowrow->EntryDateTime) && $tOrderWorkflowrow->EntryDateTime != '0000-00-00 00:00:00' ?  $tOrderWorkflowrow->EntryDateTime :date('Y-m-d H:i:s', strtotime("now"));

					$tOrderworkflowarray['DueDateTime'] = !empty($tOrderWorkflowrow->DueDateTime) && $tOrderWorkflowrow->DueDateTime != '0000-00-00 00:00:00' ?  $tOrderWorkflowrow->DueDateTime : calculate_workflowduedatetime($OrderUID,$WorkflowModuleUID);

					//check parking enabled
					$parking = $this->Common_Model->is_autoparking_enabledfor_orderworkflow($OrderUID,$WorkflowModuleUID);
					if(!empty($parking)) {
						$is_parking_row_available = $this->Common_Model->get_row('tOrderParking', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID,'IsCleared'=>0]);

						if (empty($is_parking_row_available)) {					
							$parkingdata['OrderUID'] = $OrderUID;
							$parkingdata['WorkflowModuleUID'] = $WorkflowModuleUID;
							$parkingdata['ReasonUID'] = '';
							$parkingdata['Remainder'] = isset($parking->ParkingDuration) ? date('Y-m-d H:i:s', strtotime('+' . $parking->ParkingDuration . ' Hours')) : NULL;
							$parkingdata['Remarks'] = sprintf($this->lang->line('autoparking_assigned'),$parking->WorkflowModuleName,$parking->ParkingDuration,site_datetimeformat(date('Y-m-d H:i:s', strtotime('+' . $parking->ParkingDuration . ' Hours'))));
							$parkingdata['RaisedByUserUID'] = $this->loggedid;
							$parkingdata['RaisedDateTime'] = date('Y-m-d H:i:s');
							$this->Common_Model->save('tOrderParking', $parkingdata);

							$notesdata = array(
								'OrderUID'=>$OrderUID,
								'WorkflowUID'=> $WorkflowModuleUID,
								'Description'=> nl2br($parkingdata['Remarks'] ),
								'Module'=> $parking->SystemName,
								'CreatedByUserUID'=> $this->loggedid,
								'CreateDateTime'=> date('Y-m-d H:i:s'));
							$this->db->insert('tNotes',$notesdata);
						}
					}
				}




				if(!empty($tOrderWorkflowrow)) {

					$this->db->where('OrderUID',$OrderUID);
					$this->db->where('WorkflowModuleUID',$WorkflowModuleUID);

					$this->db->update('tOrderWorkflows',$tOrderworkflowarray);	

				}	else {
				
					$this->Common_Model->save('tOrderWorkflows', $tOrderworkflowarray);

				}

			}

		}

		return true;
	}

	function dependent_check_query($OrderUID,$CustomerUID,$WorkflowModuleUID)
	{
		$this->db->select('mCustomerDependentWorkflowModules.WorkflowModuleUID,WorkflowModuleName,mCustomerWorkflowModules.Optional');
		$this->db->from('mCustomerDependentWorkflowModules');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerDependentWorkflowModules.WorkflowModuleUID');
		$this->db->join('mCustomerWorkflowModules', 'mCustomerWorkflowModules.CustomerUID = mCustomerDependentWorkflowModules.CustomerUID AND mCustomerWorkflowModules.WorkflowModuleUID = mCustomerDependentWorkflowModules.WorkflowModuleUID');
		$this->db->where('mCustomerDependentWorkflowModules.CustomerUID',$CustomerUID);
		$this->db->where_in('mCustomerDependentWorkflowModules.DependentWorkflowModuleUID', $WorkflowModuleUID);
	}


	function is_dependentoptionalexists_result($OrderUID,$CustomerUID,$WorkflowModuleUID) {
		$this->dependent_check_query($OrderUID,$CustomerUID,$WorkflowModuleUID);
		$this->db->where("mWorkFlowModules.WorkflowModuleUID IN ( SELECT WorkflowModuleUID FROM tOrderWorkflows WHERE tOrderWorkflows.OrderUID = {$OrderUID} AND IsAssign = ".STATUS_ZERO.")",NULL,FALSE);
		$this->db->where('mCustomerWorkflowModules.Optional',STATUS_ONE);
		$query = $this->db->get();
		return $query->result();
	}


		/*checked*/
	function workflow_independent_workflow($OrderUID,$CustomerUID,$WorkflowModuleUID) {
		$this->dependent_check_query($OrderUID,$CustomerUID,$WorkflowModuleUID);
		//$this->db->where("mWorkFlowModules.WorkflowModuleUID IN ( SELECT WorkflowModuleUID FROM tOrderWorkflows WHERE tOrderWorkflows.OrderUID = {$OrderUID} AND IsPresent = ".STATUS_ONE.")",NULL,FALSE);
		$query = $this->db->get();
		return $query->result();
	}



	function get_customer_workflow($CustomerUID,$WorkflowModuleUID) {
		return $this->db->where(['CustomerUID'=>$CustomerUID,'WorkflowModuleUID'=>$WorkflowModuleUID])->get('mCustomerWorkflowModules')->row();
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Wednesday 18 March 2020 **/
	/** @description get previous dependent workflows **/
	function getCustomerWorkflow_Dependentworkflows($OrderUID,$CustomerUID,$WorkflowModuleUID)
	{
		$this->db->select('DependentWorkflowModuleUID,mWorkFlowModules.SystemName');
		$this->db->from('mCustomerDependentWorkflowModules');
		$this->db->join('mWorkFlowModules', 'mCustomerDependentWorkflowModules.DependentWorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID');
		$this->db->where('mCustomerDependentWorkflowModules.CustomerUID',$CustomerUID);
		$this->db->where("mCustomerDependentWorkflowModules.WorkflowModuleUID", $WorkflowModuleUID);
		$this->db->where("mWorkFlowModules.WorkflowModuleUID IN ( SELECT WorkflowModuleUID FROM tOrderWorkflows WHERE tOrderWorkflows.OrderUID = {$OrderUID} AND IsPresent = ".STATUS_ONE.")",NULL,FALSE);
		return  $this->db->get()->result();
	}

	function check_alldependentworkflow_completed($OrderUID,$CustomerUID,$WorkflowModuleUID, $Conditions = [])
	{

		if(empty($WorkflowModuleUID)) {
			return false;
		}

		$DependentWorkflowModuleUIDs = $this->getCustomerWorkflow_Dependentworkflows($OrderUID,$CustomerUID,$WorkflowModuleUID);
		if(!empty($DependentWorkflowModuleUIDs)) {
			$otherdb = $this->load->database('otherdb', TRUE);

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/		
			$otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($DependentWorkflowModuleUIDs as $key => $value) {

				$otherdb->join("tOrderWorkflows AS " .  "TW_" .$value->SystemName,   "TW_" .$value->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$value->SystemName.".WorkflowModuleUID = '".$value->DependentWorkflowModuleUID."'");

				$otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName.".WorkflowModuleUID = '".$value->DependentWorkflowModuleUID."'", "LEFT");
			}

			foreach ($DependentWorkflowModuleUIDs as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->DependentWorkflowModuleUID . " AND TW_".$value->SystemName.".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->DependentWorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$otherdb->group_start();
				$otherdb->where($Case_Where, NULL, FALSE);
				$otherdb->group_end();
			}
			$otherdb->where('tOrders.OrderUID',$OrderUID);

			$previous_filtered_orders_sql = $otherdb->get_compiled_select();


			if(!empty($previous_filtered_orders_sql)) {
				$previous_filtered_orders_sql = "SELECT EXISTS(".$previous_filtered_orders_sql.") AS completed";
				return $this->db->query($previous_filtered_orders_sql)->row()->completed;
			}
	
		}

		if (isset($Conditions['OrderEntry']) && empty($DependentWorkflowModuleUIDs)) {
			return TRUE;
		}

		return false;

	}

	function get_customer_workflow_milestone($CustomerUID,$WorkflowModuleUID) {
		 
		$this->db->select('mCustomerWorkflowModules.WorkflowModuleUID,mCustomerMilestones.MilestoneUID,mCustomerWorkflowModules.StatusUID,SLA,mWorkFlowModules.WorkflowModuleName,mCustomerWorkflowModules.IsKickBackRequire');
		$this->db->join('mCustomerMilestones','mCustomerMilestones.CustomerUID = mCustomerWorkflowModules.CustomerUID AND mCustomerMilestones.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID','LEFT');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID','LEFT');
		$this->db->where(['mCustomerWorkflowModules.CustomerUID'=>$CustomerUID,'mCustomerWorkflowModules.WorkflowModuleUID'=>$WorkflowModuleUID]);
		return $this->db->get('mCustomerWorkflowModules')->row();

	}


	function get_customer_workflows($CustomerUID,$WorkflowModuleUIDs) {
		if(empty($WorkflowModuleUIDs)) {
			return [];
		}
		return $this->db->where('CustomerUID',$CustomerUID)->where_in('WorkflowModuleUID',$WorkflowModuleUIDs)->get('mCustomerWorkflowModules')->result();
	}


	/*Dependent workflow completed*/
	function workflow_dependent_workflow_completed($OrderUID,$CustomerUID,$WorkflowModuleUID) {
		$this->dependent_check_query($OrderUID,$CustomerUID,$WorkflowModuleUID);
		$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = '.$OrderUID.' AND tOrderAssignments.WorkflowModuleUID = mCustomerDependentWorkflowModules.WorkflowModuleUID','LEFT');
		$this->db->where('tOrderAssignments.WorkflowStatus',$this->config->item('WorkflowStatus')['Completed']);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	*Function checklist issue details 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 08 June 2020.
	*/
	function checklist_issue($OrderUID,$WorkflowModuleUID,$OrderDetails) {

		// Get checklist details
		$DocumentTypeNameDetails = $this->Common_Model->getcategorylist($OrderUID, $WorkflowModuleUID, $OrderDetails->LoanType, ['SkipCondition'=>true]);

		$DocumentTypeUID = array_column($DocumentTypeNameDetails, 'DocumentTypeUID');

		if(!empty($DocumentTypeUID)) {

			/* if($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping'])
			{
				$ChecklistSequence = $this->config->item('ChecklistSequence');
				$ChecklistSequenceStart = explode("-",$ChecklistSequence)[0];
				$ChecklistSequenceEnd = explode("-",$ChecklistSequence)[1];
			} */
			$this->db->select("( GROUP_CONCAT(
				DISTINCT
				CASE

				WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
				CASE

				WHEN tDocumentCheckList.DocumentTypeUID = '' 
				OR tDocumentCheckList.DocumentTypeUID IS NULL THEN
				tDocumentCheckList.DocumentTypeName ELSE
				CASE

				WHEN mDocumentType.ScreenCode = '' 
				OR mDocumentType.ScreenCode IS NULL THEN
				mDocumentType.DocumentTypeName ELSE mDocumentType.ScreenCode 
				END 
				END ELSE NULL 
				END 
			)) AS ProblemIdentifiedChecklists");
			$this->db->from('tDocumentCheckList');
			$this->db->join('mDocumentType','mDocumentType.DocumentTypeUID = tDocumentCheckList.DocumentTypeUID','LEFT');
			$this->db->where('tDocumentCheckList.OrderUID',$OrderUID);
			$this->db->where('tDocumentCheckList.WorkflowUID',$WorkflowModuleUID);
			$this->db->where('mDocumentType.Active',1);
			// $this->db->group_start();
			$this->db->where_in('tDocumentCheckList.DocumentTypeUID',$DocumentTypeUID);
			// $this->db->or_where('tDocumentCheckList.DocumentTypeUID IS NULL');
			// $this->db->group_end();
			/* if($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping'] && !empty($ChecklistSequence))
			{
				$this->db->where('mDocumentType.SequenceNo BETWEEN '.$ChecklistSequenceStart.' AND '.$ChecklistSequenceEnd);
			} */
			$query = $this->db->get();
			return $query->row();
		}
	}

	/** @author Sathishkumar R <sathish.kumar@avanzegroup.com> **/
	/** @date Thursday 16 July 2020 **/
	/** @description Workflow completion details for generate the user activity log report and production summary report **/
	function InsertOrderWorkflowDurationsData($OrderWorkflowDurationsData) {
		$this->db->insert('tOrderDurations',$OrderWorkflowDurationsData);
	}

	/** @author Sathishkumar R <sathish.kumar@avanzegroup.com> **/
	/** @date Thursday 30 July 2020 **/
	/** @description check mandatory field is filled in checklist **/
	function getChecklistDetails($OrderUID, $WorkflowModuleUID, $DocumentTypeUID) {
		$this->db->select('1');
		$this->db->from('tDocumentCheckList');
		$this->db->where(array('OrderUID' => $OrderUID, 'WorkflowUID' => $WorkflowModuleUID, 'DocumentTypeUID' => $DocumentTypeUID));
		$this->db->where('(DocumentDate IS NOT NULL AND DocumentDate <> "")');
		if ($this->db->get()->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	*Function Check checklist details 
	*@author sathishkumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 04 August 2020.
	*/
	function checklistmandatoryfield($OrderUID,$WorkflowModuleUID, $OrderDetails) {
		$workflowchecklist = $this->config->item('Expired_Checklist')[$WorkflowModuleUID];
		
		// Get checklist details
		$DocumentTypeNameDetails = $this->Common_Model->getcategorylist($OrderUID, $WorkflowModuleUID, $OrderDetails->LoanType, ['SkipCondition'=>true]);

		foreach ($DocumentTypeNameDetails as $key => $value) {
			if (in_array($value->DocumentTypeUID, $workflowchecklist)) {
				// Check checklist row is available
				$IsChecklistRowAvailable = $this->Common_Model->get_row('tDocumentCheckList', ['OrderUID'=>$OrderUID, 'WorkflowUID'=>$WorkflowModuleUID,'DocumentTypeUID'=>$value->DocumentTypeUID]);
				if (empty($IsChecklistRowAvailable)) {
					return array($value);
				}
			}
		}

		if(!empty($workflowchecklist)) {

			$this->db->select('tDocumentCheckList.DocumentTypeUID, mDocumentType.DocumentTypeName');
			$this->db->from('tDocumentCheckList');
			$this->db->join('mDocumentType','mDocumentType.DocumentTypeUID = tDocumentCheckList.DocumentTypeUID','LEFT');
					
			$this->db->where_in('tDocumentCheckList.DocumentTypeUID', $workflowchecklist);

			// $this->db->where('((tDocumentCheckList.Answer IS NULL OR tDocumentCheckList.Answer = "") OR (tDocumentCheckList.Answer = "Problem Identified"))', NULL, FALSE);
			$this->db->where('(tDocumentCheckList.DocumentDate IS NULL OR tDocumentCheckList.DocumentDate = "")', NULL, FALSE);
			if ($OrderDetails->LoanType != 'FHA/VA') {
				if ($OrderDetails->LoanType) {
					$this->db->where('(mDocumentType.Groups IS NULL OR mDocumentType.Groups ="' . $OrderDetails->LoanType . '")');
				}
			}

			$this->db->where('tDocumentCheckList.OrderUID',$OrderUID);
			$this->db->where('tDocumentCheckList.WorkflowUID',$WorkflowModuleUID);
			$this->db->where('mDocumentType.Active',1);
			$query = $this->db->get();
			return $query->result();
		}
		return [];
		
	}

	/**
	*Function Subqueues complete 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 22 August 2020.
	*/
	function complete_exceptionqueues($OrderUID,$WorkflowModuleUID)
	{

		// Check if exception row available
		$this->db->select('tOrderQueues.OrderQueuUID,mQueues.QueueUID,mQueues.QueueName, mWorkFlowModules.WorkflowModuleName,mQueues.WorkflowModuleUID');
		$this->db->from('tOrderQueues');
		$this->db->join('mQueues','mQueues.QueueUID = tOrderQueues.QueueUID','left');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID','left');
		$this->db->where(array(
			'tOrderQueues.OrderUID'=>$OrderUID,
			'mQueues.WorkflowModuleUID'=>$WorkflowModuleUID,
			"tOrderQueues.QueueStatus" => "Pending"
		));
		$is_exception_row_available = $this->db->get()->result();

		if (!empty($is_exception_row_available)) {	

			foreach ($is_exception_row_available as $queue) {

				// Clear the subqueues option if raised for this order
				$tOrderQueuesData = [];
				$tOrderQueuesData['OrderUID'] = $OrderUID;
				$tOrderQueuesData['QueueUID'] = $queue->QueueUID;
				$tOrderQueuesData['QueueStatus'] = "Completed";
				$tOrderQueuesData['CompletedReasonUID'] = '';
				$tOrderQueuesData['CompletedRemarks'] = ''; // Exception queue is force completed.
				$tOrderQueuesData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
				$tOrderQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

				$this->db->where(array(
					'OrderQueuUID'=>$queue->OrderQueuUID, 
					"QueueStatus" => "Pending"
				));
				$this->db->update('tOrderQueues', $tOrderQueuesData);

				if($this->db->affected_rows()) {
					$OrderLogsDescriptions = '<br/><b>'.$queue->WorkflowModuleName.' - '.$queue->QueueName.'</b> sub queue was force completed.';
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($OrderUID,$OrderLogsDescriptions,Date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/
				}
			}						
		}
	}

	/**
	*Function Enable tOrderworkflow if not enabled
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 31 August 2020.
	*/
	function enable_torderworkflow($OrderUID,$WorkflowModuleUID)
	{
		$tOrderWorkflowrow = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

		$tOrderworkflowarray = [];
		$tOrderworkflowarray['IsAssign'] = STATUS_ONE;
		$tOrderworkflowarray['IsPresent'] = STATUS_ONE;


		if(!empty($tOrderWorkflowrow)) {

			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('WorkflowModuleUID',$WorkflowModuleUID);
			$this->db->update('tOrderWorkflows',$tOrderworkflowarray);	

		}	else {

			$tOrderworkflowarray['OrderUID'] = $OrderUID;
			$tOrderworkflowarray['WorkflowModuleUID'] = $WorkflowModuleUID;
			/*update entrydatetime duedatetime for workflow*/
			$tOrderworkflowarray['EntryDateTime'] = !empty($tOrderWorkflowrow->EntryDateTime) && $tOrderWorkflowrow->EntryDateTime != '0000-00-00 00:00:00' ?  $tOrderWorkflowrow->EntryDateTime :date('Y-m-d H:i:s', strtotime("now"));

			$tOrderworkflowarray['DueDateTime'] = !empty($tOrderWorkflowrow->DueDateTime) && $tOrderWorkflowrow->DueDateTime != '0000-00-00 00:00:00' ?  $tOrderWorkflowrow->DueDateTime : calculate_workflowduedatetime($OrderUID,$WorkflowModuleUID);	
			$this->Common_Model->save('tOrderWorkflows', $tOrderworkflowarray);

		}
	}

	/**
	*Function Check checklist details 
	*@author sathishkumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 04 August 2020.
	*/
	function checklistmandatoryfieldcheck($OrderUID,$WorkflowModuleUID, $OrderDetails) {

		// Is checklist mandatory field enabled workflow
		if (in_array($WorkflowModuleUID, $this->config->item('ChecklistMandatoryFieldWorkflows'))) {
		
			// Get checklist details
			$DocumentTypeNameDetails = $this->Common_Model->getcategorylist($OrderUID, $WorkflowModuleUID, $OrderDetails->LoanType, ['SkipCondition'=>true]);

			foreach ($DocumentTypeNameDetails as $key => $value) {
				// Check checklist row is available
				$IsChecklistRowAvailable = $this->Common_Model->get_row('tDocumentCheckList', ['OrderUID'=>$OrderUID, 'WorkflowUID'=>$WorkflowModuleUID,'DocumentTypeUID'=>$value->DocumentTypeUID]);
				if (empty($IsChecklistRowAvailable)) {
					return array($value);
				}
			}

			$DocumentTypeUIDArr = array_column($DocumentTypeNameDetails, 'DocumentTypeUID');

			if(!empty($DocumentTypeUIDArr)) {

				$this->db->select('tDocumentCheckList.DocumentTypeUID, mDocumentType.DocumentTypeName');
				$this->db->from('tDocumentCheckList');
				$this->db->join('mDocumentType','mDocumentType.DocumentTypeUID = tDocumentCheckList.DocumentTypeUID','LEFT');		
				$this->db->where_in('tDocumentCheckList.DocumentTypeUID', $DocumentTypeUIDArr);
				$this->db->where('tDocumentCheckList.Answer IS NULL OR tDocumentCheckList.Answer = ""', NULL, FALSE);
				$this->db->where('tDocumentCheckList.OrderUID',$OrderUID);
				$this->db->where('tDocumentCheckList.WorkflowUID',$WorkflowModuleUID);
				if ($OrderDetails->LoanType != 'FHA/VA') {
					if ($OrderDetails->LoanType) {
						$this->db->where('(mDocumentType.Groups IS NULL OR mDocumentType.Groups ="' . $OrderDetails->LoanType . '")');
					}
				}
				$this->db->where('mDocumentType.Active',1);
				$query = $this->db->get();
				return $query->result();
			}

		}
		return [];
		
	}

	/**
	*Function Check NBS Required Workflow 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 09 September 2020.
	*/
	function CheckNBSRequiredWorkflow($OrderUID)
	{
		$this->db->select('mStateMatrix.NBSRequired');
		$this->db->from('tOrders');
		$this->db->join('mStates','mStates.StateCode = tOrders.PropertyStateCode','LEFT');
		$this->db->join('mStateMatrix','mStateMatrix.StateUID = mStates.StateUID','LEFT');
		$this->db->where('tOrders.OrderUID',$OrderUID);		
		return $this->db->get()->row()->NBSRequired;

	}

	/**
	*Function Calculator data 
	*@author sathishkumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 14 October 2020.
	*/
	function GetCalculatorData($OrderUID, $WorkflowModuleUID) {

		$this->db->select('tCalculator.*');
		$this->db->from('tCalculator');
		$this->db->where(array(
			'OrderUID' => $OrderUID,
			'WorkflowModuleUID' => $WorkflowModuleUID
		));
		$query = $this->db->get();
		$CalculatorData = $query->row();	

		// Validation
		if(empty($CalculatorData->InsuranceNextDueDate)) {

			return "Insurance Calculator <b>Next Due Date (Aggregate escrow account screen)</b> Column is Mandatory";
		}
		if(empty($CalculatorData->FinalOutPut)) {

			return "Insurance Calculator <b>Final Output</b> Column is Mandatory";
		}	

		// Validation
		if(empty($CalculatorData->EscrowNextDueDate1)) {

			return "Escrow Calculator <b>Next Due Date (Aggregate escrow account screen)</b> Column is Mandatory";
		}
		if(empty($CalculatorData->EscrowFinalOutPut)) {

			return "Escrow Calculator <b>Final Output</b> Column is Mandatory";
		}	

		// Validation
		if(empty($CalculatorData->TotalAmountPayoff)) {

			return "Payoff Calculator <b>Total Amount on the payoff statement</b> Column is Mandatory";
		}	
		if(empty($CalculatorData->UseThisPayoff)) {

			return "Payoff Calculator <b>Use This Payoff</b> Column is Mandatory";
		}
		return '';
	}

}
?>