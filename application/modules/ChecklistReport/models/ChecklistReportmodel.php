<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ChecklistReportmodel extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	function GetReports() {
		$this->db->select('*');
		$this->db->from('mReports');
		$this->db->join('mReportFields','mReportFields.ReportUID = mReports.ReportUID', 'left');
		$this->db->where(array('mReports.ClientUID'=>$this->session->userdata('DefaultClientUID'),'mReportFields.Active'=>'1','mReports.Active'=>'1'));
		$this->db->group_by('mReports.ReportUID');
		$query = $this->db->get()->result_array();
		return $query;
	}

	function Get_ReportsInfo($ReportUID) {

		//fetch report row
		$Reportrow =  $this->getreport_row($ReportUID);  
		if(empty($Reportrow)) {
			return [];
		}  

		$this->db->select('HeaderName,GroupName');
		$this->db->from('mReportFields');
		$this->db->join('mReports', 'mReports.ReportUID = mReportFields.ReportUID');
		$this->db->join('mReportsGroups', 'mReportsGroups.GroupUID = mReportFields.GroupUID');
		$this->db->join('mDocumentType', 'mDocumentType.DocumentTypeUID = mReportFields.DocumentTypeUID','LEFT');
		$this->db->where(array('mReports.ClientUID'=>$this->session->userdata('DefaultClientUID'), 'mReports.ReportUID'=>$ReportUID, 'mReportFields.Active'=>'1'));
		$this->db->order_by('mReportFields.Position','ASC');
		$report_headerlist =  $this->db->get()->result_array();  

		$report_headerchecklist = [];
		if($Reportrow->DisplayChecklist == 1  && !empty($Reportrow->QueueUIDs)) {
			$GroupName = 'Checklist';
			$report_headerchecklist = $this->get_checklistcategory($Reportrow->WorkflowModuleUID,$GroupName);
		} 

		return array_merge($report_headerlist,$report_headerchecklist);

	}

	function Get_ReportsDetails($post) {
		$this->selecteOptionQuery($post);
		$this->filterQuery($post);
		/* */
		/* Advanced Search  */
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		}
		$this->db->where('mReports.ClientUID',$this->session->userdata('DefaultClientUID'));

		/* Datatable Search */
		$this->Common_Model->Datatable_Search_having($post);

		/* Datatable OrderBy */
		$this->Common_Model->ChecklistReport_Datatable_OrderBy($post);


		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$this->db->order_by('tOrders.OrderNumber','ASC');
		/* */
		return $this->db->get()->result_array();  
    // echo "<pre>";
    // print_r($query);exit();
	}

	function Get_WorkflowDetails($post) {
		$this->db->select('mReportFields.ReportFieldUID,mReportFields.WorkflowUID,mReportFields.DocumentTypeUID, mDocumentType.DocumentTypeName,mWorkFlowModules.SystemName,mReportsGroups.GroupName,mReportFields.IsChecklist,mReportFields.ColumnName,mReportFields.ChecklistOption,mReportFields.HeaderName');  
		$this->db->select('"Columns" AS ColumnType',FALSE);
		$this->db->from('mReports');
		$this->db->join('mReportFields', 'mReportFields.ReportUID = mReports.ReportUID', 'left');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mReportFields.WorkflowUID', 'left');

		$this->db->join('mReportsGroups', 'mReportsGroups.GroupUID = mReportFields.GroupUID', 'left');
		$this->db->join('mDocumentType', 'mDocumentType.DocumentTypeUID = mReportFields.DocumentTypeUID', 'left');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		}
		$this->db->where('mReports.ClientUID',$this->session->userdata('DefaultClientUID'));
		$this->db->where('mReportFields.Active','1');

		$this->db->order_by("mReportsGroups.GroupName", "ASC");
		$this->db->order_by('mReportFields.Position','ASC');
		return $this->db->get()->result_array();
    // echo "<pre>";
    // print_r($query);exit();  
	}

	function selecteOptionQuery($post)
	{

		$Reportrow = $post['Reportrow'];
		$WorkflowDetails = $post['WorkflowDetails'];

		//$this->db->select('"" AS ChecklistColumn');
		foreach ($WorkflowDetails as $key => $value)
		{
			if($value['IsChecklist'] == 1 && !empty($value['WorkflowUID'])) {
				if ($this->db->field_exists($value['ChecklistOption'], 'tDocumentCheckList')) {
					$this->db->select("(SELECT tDocumentCheckList.".$value['ChecklistOption']." FROM tDocumentCheckList WHERE tDocumentCheckList.DocumentTypeUID = ".$value['DocumentTypeUID']." AND tDocumentCheckList.WorkflowUID = ".$value['WorkflowUID']." AND tDocumentCheckList.OrderUID = tOrders.OrderUID) AS Column_".alphanumericonly($value['ReportFieldUID'].$value['ChecklistOption']),FALSE);

				} else {
					$this->db->select('"" AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE);
				}

			} else if(!empty($value['ColumnName'])) {

				if(($value['ColumnName'] == 'Aging' || $value['ColumnName'] == 'Logic-Aging') && isset($Reportrow->WorkflowModuleUID) && !empty($Reportrow->WorkflowModuleUID)) {

					$this->db->select('tOrderWorkflows.EntryDatetime AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE);

				}else if($value['ColumnName'] == 'LOGIC-LCREQUIRED') {

					$this->db->select('SUM(tOrderImport.CashFromBorrower - ( tOrderImport.ProposedTotalHousingExpense +  tOrderImport.Assets)) AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE);

				} else if($value['ColumnName'] == 'CurrentQueue' && isset($Reportrow->WorkflowModuleUID) && !empty($Reportrow->WorkflowModuleUID)) {
					$this->db->select('(CASE 
					WHEN (tOrderAssignments.WorkflowStatus = 5) THEN "Completed"
					WHEN (tOrderParking.IsCleared = 0) THEN "Parking Orders"
					WHEN (GROUP_CONCAT(DISTINCT mQueues.QueueName) <> "" AND tOrderQueues.OrderUID <> "") THEN GROUP_CONCAT(DISTINCT mQueues.QueueName)
					WHEN (tOrderAssignments.AssignedToUserUID = '.$this->loggedid.') THEN "My Orders"
					WHEN (tOrderAssignments.AssignedToUserUID <> '.$this->loggedid.' AND tOrderAssignments.AssignedToUserUID IS NOT NULL) THEN "Assigned Orders"
					WHEN (tOrderAssignments.AssignedToUserUID = "" OR tOrderAssignments.AssignedToUserUID IS NULL) THEN "New Orders"
					ELSE ""
					END) AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE);
				} else if($value['ColumnName'] == 'Associate' && isset($Reportrow->WorkflowModuleUID) && !empty($Reportrow->WorkflowModuleUID)) {
					$this->db->select('mUsers.UserName AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE);
				} else if($value['ColumnName'] == 'Order-Priority') {
					$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);	
					if (!empty($Customer_Prioritys)) {
						$PriorityWHERE = [];
						foreach ($Customer_Prioritys as $Priorityrow) {
							$PriorityFields = $this->Priority_Report_model->get_priorityreportfields($Priorityrow->PriorityUID);
							if (!empty($PriorityFields)) {
								$PriorityLOOPWHERE = [];
								foreach ($PriorityFields as $PriorityFieldrow) {
									if ($PriorityFieldrow->WorkflowStatus == 'Completed') {
										$PriorityLOOPWHERE[] = "(TWPR_" . $PriorityFieldrow->SystemName . ".WorkflowModuleUID = " . $PriorityFieldrow->WorkflowModuleUID . " AND TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " ) ";
									} else {
										$PriorityLOOPWHERE[] = "(TWPR_" . $PriorityFieldrow->SystemName . ".WorkflowModuleUID = " . $PriorityFieldrow->WorkflowModuleUID . " AND (TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus IS NULL OR TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus <> " . $this->config->item('WorkflowStatus')['Completed'] . ") ) ";
									}
								}
								$PriorityWHERE[$Priorityrow->PriorityUID] = "(" . implode(" AND ", $PriorityLOOPWHERE) . " )";
							}
						}

						if (!empty($PriorityWHERE)) {

							$PriorityWHERECASECONDITION = [];
							foreach ($PriorityWHERE as $PriorityWHEREkey => $PriorityWHEREvalue) {

								$PriorityWHERECASECONDITION[] = "WHEN " . $PriorityWHEREvalue . " THEN 'Priority " . $PriorityWHEREkey . "'";
							}

							$this->db->select("(CASE " . implode(" ", $PriorityWHERECASECONDITION) . "   ELSE '' END) AS Column_".alphanumericonly($value['ReportFieldUID'].$value['ColumnName']));
						} else {

							$this->db->select('"" AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']), FALSE);
						}
					} else {
						$this->db->select('"" AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']), FALSE);
					}

				} else if((!isset($Reportrow->WorkflowModuleUID) || empty($Reportrow->WorkflowModuleUID)) && ($value['ColumnName'] == 'Aging' || $value['ColumnName'] == 'Logic-Aging' || $value['ColumnName'] == 'Associate'  || $value['ColumnName'] == 'CurrentQueue')) {
					
					$this->db->select('"" AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE);

				} else {
					$this->db->select(trim($value['ColumnName'] .' AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE));
				}


			} else {
				$this->db->select('"" AS Column_'.alphanumericonly($value['ReportFieldUID'].$value['ColumnName']),FALSE);
			}


		}




		if(!empty($Reportrow)) {
			if($Reportrow->DisplayChecklist == 1 && !empty($Reportrow->QueueUIDs)) {
				foreach ($post['Checklists'] as $Checklistskey => $Checklist) {
					$this->db->select('(SELECT CASE WHEN tDocumentCheckList.Comments <> "" THEN tDocumentCheckList.Comments ELSE tDocumentCheckList.Answer END FROM tDocumentCheckList  WHERE mQueues.QueueUID IN ( '.$Reportrow->QueueUIDs.') AND tDocumentCheckList.DocumentTypeUID = '.$Checklist['DocumentTypeUID'].' AND tDocumentCheckList.WorkflowUID = '.$Checklist["WorkflowModuleUID"].' AND tDocumentCheckList.OrderUID = tOrders.OrderUID AND tOrderQueues.OrderUID = tOrders.OrderUID) AS Checklist_'.alphanumericonly($value['ReportFieldUID'].$Checklist['DocumentTypeUID']),FALSE);

				}
			}
			$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);
			foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrow) {

				$this->db->join("tOrderWorkflows AS " .  "TWPR_" . $PriorityWorkflowrow->SystemName,   "TWPR_" . $PriorityWorkflowrow->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TWPR_" . $PriorityWorkflowrow->SystemName . ".WorkflowModuleUID = '" . $PriorityWorkflowrow->WorkflowModuleUID . "'", "INNER");

				$this->db->join("tOrderAssignments AS " . "TOAPR_" . $PriorityWorkflowrow->SystemName,  "TOAPR_" . $PriorityWorkflowrow->SystemName . ".OrderUID = tOrders.OrderUID AND TOAPR_" . $PriorityWorkflowrow->SystemName . ".WorkflowModuleUID = '" . $PriorityWorkflowrow->WorkflowModuleUID . "'", "INNER");
			}
		}

	}


	function filterQuery($post,$requesttype=false)
	{
		$this->db->from('tOrders');

		if($requesttype === false) {

			$this->db->join('mReports', 'mReports.ClientUID = tOrders.CustomerUID', 'left');
			$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
			$this->db->join('mReportFields', 'mReportFields.ReportUID = mReports.ReportUID', 'left');
			// $this->db->join('tDocumentCheckList', 'tDocumentCheckList.DocumentTypeUID = mReportFields.DocumentTypeUID and tDocumentCheckList.OrderUID = tOrders.OrderUID', 'left');
			$this->db->join('mProducts', 'mProducts.ProductUID = tOrders.ProductUID', 'left');
			$this->db->join('mStates', 'mStates.StateCode = tOrders.PropertyStateCode', 'left');
			$this->db->join('mMilestone','mMilestone.MilestoneUID = tOrders.MilestoneUID','left');
			$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','left');
		}

		if(isset($post['advancedsearch']['ReportUID']) && !empty($post['advancedsearch']['ReportUID']))
		{	
			$Reportrow = $this->getreport_row($post['advancedsearch']['ReportUID']);
			if(!empty($Reportrow)) {
				if(!empty($Reportrow->WorkflowModuleUID)) {
    			//workflowuid present - workflowstatus
					$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$Reportrow->WorkflowModuleUID,'LEFT');

					$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$Reportrow->WorkflowModuleUID, 'LEFT');

					if($requesttype === false) {

						$this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');

						$this->db->join('tOrderParking','tOrderParking.OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '.$Reportrow->WorkflowModuleUID,'left');

						$this->db->join('mQueues','mQueues.WorkflowModuleUID = '.$Reportrow->WorkflowModuleUID.' AND mQueues.CustomerUID = '.$this->parameters['DefaultClientUID'],'LEFT');

						$this->db->join('tOrderQueues','tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND QueueStatus = "Pending"','LEFT');
					}

					if(isset($post['advancedsearch']['ReportStatus']) && $post['advancedsearch']['ReportStatus'] == 'All') {
						$this->db->group_start();
						$this->db->group_start();
						$this->pending_queueworkflowquery($Reportrow->WorkflowModuleUID);
						$this->db->group_end();

						$this->db->or_group_start();
						$this->db->where('tOrderAssignments.WorkflowStatus',$this->config->item('WorkflowStatus')['Completed']);
						//filter order when withdrawal enabled
						$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

						//filter order when escalation enabled
						$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

						$this->db->group_end();
						$this->db->group_end();

					} else if(isset($post['advancedsearch']['ReportStatus']) && $post['advancedsearch']['ReportStatus'] == 'Pending') {

						$this->pending_queueworkflowquery($Reportrow->WorkflowModuleUID);

					} else if(isset($post['advancedsearch']['ReportStatus']) && $post['advancedsearch']['ReportStatus'] == 'Completed') {

						//filter order when withdrawal enabled
						$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

						//filter order when escalation enabled
						$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

						$this->db->where('tOrderAssignments.WorkflowStatus',$this->config->item('WorkflowStatus')['Completed']);
					}


				} else {
    			//workflow empty - order status
					$status = [];
					$status[0] = $this->config->item('keywords')['ClosedandBilled'];
					$status[1] = $this->config->item('keywords')['ClosingCompleted'];

					if(isset($post['advancedsearch']['ReportStatus']) && $post['advancedsearch']['ReportStatus'] == 'All') {
						$this->db->group_start();
						$this->db->or_where_not_in('tOrders.StatusUID', $status);
						$this->db->or_where_in('tOrders.StatusUID', $status);
						$this->db->group_end();
					}else if(isset($post['advancedsearch']['ReportStatus']) && $post['advancedsearch']['ReportStatus'] == 'Pending') {
						$this->db->where_not_in('tOrders.StatusUID', $status);
					}else if(isset($post['advancedsearch']['ReportStatus']) && $post['advancedsearch']['ReportStatus'] == 'Completed') {
						$this->db->where_in('tOrders.StatusUID', $status);
					}
				}
			}
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID',$this->parameters['DefaultClientUID']);

		}


		$this->db->group_by("tOrders.OrderUID");
	}

	function count_filtered($post)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->selecteOptionQuery($post);
		}
		$this->filterQuery($post);
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		} 
    // Datatable Search
		$this->Common_Model->Datatable_Search_having($post);
    // Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		$query = $this->db->get();
		return $query->num_rows();
	}

	function advanced_search($post)
	{
		if($post['advancedsearch']['ReportUID'] != ''){
			$this->db->where('mReports.ReportUID',$post['advancedsearch']['ReportUID']);
		}
	}

	function count_all($post)
	{
		$this->db->select("1");
		$this->filterQuery($post,'count_all');
		$query = $this->db->count_all_results();
		return $query;
	}

	/**
  *Function fetch report row
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Saturday 25 April 2020
  */
	function getreport_row($ReportUID)
	{
		$otherdb = $this->load->database('otherdb', TRUE);
		$otherdb->select('mReports.*');
		$otherdb->from('mReports');
		$otherdb->where('mReports.ReportUID',$ReportUID);
		return $otherdb->get()->row();
	}


	/**
  *Function fetch standard columns
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Saturday 25 April 2020
  */
	function get_standardreportrows($ReportUID)
	{
		$otherdb = $this->load->database('otherdb', TRUE);
		$otherdb->select('mReportFields.*');
		$otherdb->from('mReportFields');
		$otherdb->where('mReportFields.ReportUID',$ReportUID);
		$otherdb->where('(mReportFields.IsChecklist IS NULL OR mReportFields.IsChecklist = "")',NULL,FALSE);
		$otherdb->order_by('mReportFields.Position','ASC');
		return $otherdb->get()->result();
	}

	function pending_queueworkflowquery($WorkflowModuleUID)
	{
		//pending check validations
		$status = [];
		$status[] = $this->config->item('keywords')['Cancelled'];

		/** 
		* Dependent Workflows
		* @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		* @since Wednesday 11 March 2020 
		*/

		$DependentWorkflowModuleUID = $this->Common_Model->getDependentworkflows($WorkflowModuleUID);

		if(!empty($DependentWorkflowModuleUID)) {

			$otherdb = $this->load->database('otherdb', TRUE);
			$otherdb->select('mWorkFlowModules.*');
			$otherdb->from('mWorkFlowModules');
			if(!empty($DependentWorkflowModuleUID))
			{
				$otherdb->where_in('mWorkFlowModules.WorkflowModuleUID',$DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $otherdb->get()->result();


			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/		
			$otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$otherdb->join("tOrderWorkflows AS " .  "TW_" .$value->SystemName,   "TW_" .$value->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$value->SystemName.".WorkflowModuleUID = '".$value->WorkflowModuleUID."'");

				$otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName.".WorkflowModuleUID = '".$value->WorkflowModuleUID."'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_".$value->SystemName.".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$otherdb->group_start();
				$otherdb->where($Case_Where, NULL, FALSE);
				$otherdb->group_end();
			}

			$otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $otherdb->get_compiled_select();
		}

		/*Check Doc Case Enabled*/
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = ".$WorkflowModuleUID."  AND tOrderDocChase.IsCleared = 0)",NULL,FALSE);

		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

		if(isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = ".$WorkflowModuleUID." AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = ".$WorkflowModuleUID."
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/


	}

	function get_checklistcategory($WorkflowModuleUID)
	{
		$this->db->select('mDocumentType.DocumentTypeUID,mDocumentType.CategoryUID,mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->select('DocumentTypeName AS HeaderName',FALSE);
		$this->db->select('"Checklist" AS GroupName',FALSE);
		$this->db->select('"Checklist" AS ColumnType',FALSE);
		$this->db->from('mCustomerWorkflowModules')->join('mDocumentType','mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID AND mDocumentType.CategoryUID IS NOT NULL AND mCustomerWorkflowModules.CategoryUID IS NOT NULL');

		/**
		*Function  Header Client Selection 
		*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
		*@since Friday 13 March 2020
		*/
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mCustomerWorkflowModules.CustomerUID',$this->parameters['DefaultClientUID']);
			$this->db->where('mDocumentType.CustomerUID',$this->parameters['DefaultClientUID']);

		}
		$this->db->where(array('WorkflowModuleUID'=>$WorkflowModuleUID,'mDocumentType.Active'=>'1'));
		return $this->db->get()->result_array();
	}

}
?>
