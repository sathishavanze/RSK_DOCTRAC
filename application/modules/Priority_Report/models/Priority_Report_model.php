<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Priority_Report_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Wednesday Wednesday 08 April 2020 **/
	/** @description get customer workflows based on Prioritization **/
	function getCustomer_PrioritizationMilestones($CustomerUID,$MilestoneUID='')
	{
		if (!empty($CustomerUID)) {
			$this->db->select('mWorkFlowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName,mMilestone.MilestoneName,mMilestone.MilestoneUID');
			$this->db->from('mCustomerMilestones');
			$this->db->join('mCustomerWorkflowMetrics','mCustomerWorkflowMetrics.CustomerUID = mCustomerMilestones.CustomerUID  AND mCustomerWorkflowMetrics.WorkflowModuleUID = mCustomerMilestones.WorkflowModuleUID');
			$this->db->join('mWorkFlowModules','mCustomerMilestones.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID');
			$this->db->join('mMilestone','mCustomerMilestones.MilestoneUID = mMilestone.MilestoneUID');
			$this->db->where('mCustomerMilestones.CustomerUID', $CustomerUID);
			$this->db->where('mWorkFlowModules.Active', STATUS_ONE);
			if(!empty($MilestoneUID) && ($MilestoneUID != "All")) {
				$this->db->where('mCustomerMilestones.MilestoneUID', $MilestoneUID);
			}
			$this->db->order_by('mCustomerMilestones.WorkflowModuleUID');
			return $this->db->get()->result();
		}
		return [];
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Wednesday Wednesday 08 April 2020 **/
	/** @description get customer priority metrics **/
	function getCustomer_Prioritization_Metrics($CustomerUID)
	{
		if (!empty($CustomerUID)) {
			$this->db->select('mWorkFlowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName,mCustomerWorkflowMetrics.Priority');
			$this->db->from('mCustomerWorkflowMetrics');
			$this->db->join('mWorkFlowModules','mCustomerWorkflowMetrics.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID');
			$this->db->where('mCustomerWorkflowMetrics.CustomerUID', $CustomerUID);
			$this->db->where('mWorkFlowModules.Active', STATUS_ONE);
			$this->db->order_by('mCustomerWorkflowMetrics.Priority');
			return $this->db->get()->result();
		}
		return [];
	}


	/**
	*Function get previous metric dependent workflows
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 09 April 2020
	*/
	function get_CustomerMetricsDependentworkflows($CustomerUID,$WorkflowModuleUID)
	{
		$otherdb = $this->load->database('otherdb', TRUE);
		$otherdb->select('mCustomerWorkflowMetricsDependentWorkflows.*', false);
		$otherdb->from('mCustomerWorkflowMetrics');
		$otherdb->join('mCustomerWorkflowMetricsDependentWorkflows', 'mCustomerWorkflowMetricsDependentWorkflows.CustomerWorkflowMetricUID = mCustomerWorkflowMetrics.CustomerWorkflowMetricUID');
		$otherdb->join('mWorkFlowModules', 'mCustomerWorkflowMetricsDependentWorkflows.DependentWorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID');
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if(isset($CustomerUID) && !empty($CustomerUID)) {
			$otherdb->where('mCustomerWorkflowMetrics.CustomerUID',$CustomerUID);
		}
		$otherdb->where("mCustomerWorkflowMetrics.WorkflowModuleUID", $WorkflowModuleUID);
		$otherdb->order_by('mCustomerWorkflowMetrics.Priority', 'ASC');
		return $otherdb->get()->result();
	}

	/**
	*Function get report priority header
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 14 May 2020
	*/
	function get_priorityreportheader($ClientUID)
	{
		$otherdb = $this->load->database('otherdb', TRUE);
		$otherdb->select('mPriorityReport.*', false);
		$otherdb->from('mPriorityReport');
		if(isset($ClientUID) && !empty($ClientUID)) {
			$otherdb->where('mPriorityReport.ClientUID',$ClientUID);
		}
		$otherdb->where('mPriorityReport.Active',1);
		$otherdb->order_by('mPriorityReport.Position', 'ASC');
		return $otherdb->get()->result();
	}

	/**
	*Function get report priority header
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 14 May 2020
	*/
	function get_priorityreportfields($PriorityUID)
	{
		$otherdb = $this->load->database('otherdb', TRUE);
		$otherdb->select('mPriorityReportFields.*,mWorkFlowModules.SystemName', false);
		$otherdb->from('mPriorityReportFields');
		$otherdb->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mPriorityReportFields.WorkflowModuleUID');
		$otherdb->where('mPriorityReportFields.PriorityUID',$PriorityUID);
		return $otherdb->get()->result();
	}

	/**
	*Function get report priority header
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 14 May 2020
	*/
	function get_priorityreportworkflows($ClientUID)
	{
		$otherdb = $this->load->database('otherdb', TRUE);
		$otherdb->select('mPriorityReportFields.*,mWorkFlowModules.SystemName', false);
		$otherdb->from('mPriorityReportFields');
		$otherdb->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mPriorityReportFields.WorkflowModuleUID');
		if(isset($CustomerUID) && !empty($CustomerUID)) {
			$otherdb->where('mPriorityReport.ClientUID',$ClientUID);
		}
		$otherdb->group_by('mPriorityReportFields.WorkflowModuleUID');
		return $otherdb->get()->result();
	}


	function get_counts($post)
	{
		$CustomerUID = NULL;
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}

		$Except_PriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_PriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$PriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('PriorityReport_OnlyMilestones'), 'MilestoneUID'));

		$Milestones = $this->get_settingsmilestones('PriorityReport_MilestonesList');

		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		if(!empty($Milestones) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			$PipelineTotalWhere = [];

			foreach ($Milestones as $Milestonekey => $Milestone) {

				$MilestoneTotalWHERE = [];

				if(!empty($Except_PriorityReport_PipelineMilestones)) {
					$PipelineANDWhere = "mMilestone.MilestoneName = '".$Milestone->MilestoneName."'"." AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN (".$Except_PriorityReport_PipelineMilestones."))";
				} else {
					$PipelineANDWhere = "mMilestone.MilestoneName = '".$Milestone->MilestoneName."'";
				}

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $PipelineANDWhere . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$Milestone->MilestoneUID,FALSE);

				$PipelineTotalWhere[$Milestone->MilestoneUID] = $PipelineANDWhere;

				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];

					$WHERE = [];

					if(!empty($PriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$PriorityReport_OnlyMilestones.')';
					}

					if(!empty($PipelineANDWhere)) {

						$WHERE[] = $PipelineANDWhere;
					}	

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);

					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							// Expiry Checklist
							$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
							$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

							$CHECKLISTEXPCOND = [];
							$CHECKLISTEXPCASE = "";
							if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

								foreach ($workflowchecklist as $checklistkey => $checklistuid) {

									if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

									} else {

										if ($PriorityFieldrow->WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentDate IS NOT NULL AND tDocumentCheckList.DocumentDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										} else {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentExpiryDate IS NOT NULL AND tDocumentCheckList.DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										}
														
									}

								}

							}

							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = " AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN FALSE
										ELSE TRUE
									END)";
								}

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].")".$CHECKLISTEXPCASE;
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN TRUE
										ELSE FALSE
									END)";
								}

								$WHERE[] = "((TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].")) OR ( ".(!empty($CHECKLISTEXPCASE)? $CHECKLISTEXPCASE : "FALSE")."))";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
							}
						}
						
						if (!empty($WHERE)) {

							$PriorityLoopWHERE[] = implode(" AND ", $WHERE);
							$MilestoneTotalWHERE[] = implode(" AND ", $WHERE);
							$TotalWHERE[] = implode(" AND ", $WHERE);
							$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Milestone->MilestoneUID.'Priority'.$Priorityrow->PriorityUID);

						} else {

							$this->db->select("0 AS ".$Milestone->MilestoneUID.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
						}


					} else {
						$this->db->select("0 AS ".$Milestone->MilestoneUID.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
					}

					$PriorityMilestoneWhereArray[$Milestone->MilestoneUID][$Priorityrow->PriorityUID] = implode(" OR ", $PriorityLoopWHERE);


				}

				//Milestone Total
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $MilestoneTotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Milestone->MilestoneUID.'Total');


			}


			//Priority Total
			if(!empty($PriorityMilestoneWhereArray) && !empty($Customer_Prioritys)) {
				foreach ($Customer_Prioritys as $Priorityrow) {
					$PriorityWhere = [];
					foreach ($Milestones as $Milestonekey => $Milestone) {
						$PriorityWhere[] = array_key_exists($Priorityrow->PriorityUID, $PriorityMilestoneWhereArray[$Milestone->MilestoneUID]) ? $PriorityMilestoneWhereArray[$Milestone->MilestoneUID][$Priorityrow->PriorityUID] : [];

					}
					if(!empty($PriorityWhere)) {
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PriorityWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Priority".$Priorityrow->PriorityUID."Total");
					}
				}
			}

			//Pending Total
			if(!empty($PipelineTotalWhere)) {

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineTotalWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS PendingTotal",FALSE);

			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}

			//overall Total

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal ");


		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'LEFT');

		$this->db->join('mMilestone','mMilestone.MilestoneUID = tOrders.MilestoneUID');

		$Customer_PriorityWorkflowstmp = array_keys($Customer_PriorityWorkflows);
		$last_key = end($Customer_PriorityWorkflowstmp);

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrowkey => $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityWorkflowrow->SystemName,   "TW_" .$PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityWorkflowrow->SystemName,  "TOA_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $PriorityWorkflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$PriorityWorkflowrowkey,$last_key);
			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $PriorityWorkflowrow->SystemName,  "TDC_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TDC_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $PriorityWorkflowrow->SystemName,  "TP_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TP_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");*/

		}


		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);*/

		$this->db->where_not_in('tOrders.StatusUID', $status);


		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		if(isset($post['ProductUID']) && !empty($post['ProductUID']) && ($post['ProductUID'] != "All")) {
			$this->db->where('tOrders.ProductUID',$post['ProductUID']);
		}

		if(isset($post['ProjectUID']) && !empty($post['ProjectUID']) && ($post['ProjectUID'] != "All")) {
			$this->db->where('tOrders.ProjectUID',$post['ProjectUID']);
		}

		if(isset($post['FromDate']) && !empty($post['FromDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "'.date('Y-m-d', strtotime($post['FromDate'])).'"', NULL, false);
		}

		if(isset($post['ToDate']) && !empty($post['ToDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="'.date('Y-m-d', strtotime($post['ToDate'])).'"',NULL, false);
		}

		return $this->db->get()->row();

	}

	//Order fetch start

	function fetchorder_query($post,$global='')
	{
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
		$this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','LEFT');


		if(!empty($post['WorkflowModuleUID'])) {
			$this->db->select('tOrderWorkflows.WorkflowModuleUID,tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime,tOrderAssignments.AssignedToUserUID');

			$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = ' . $post['WorkflowModuleUID'] ,'LEFT');
			$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = ' . $post['WorkflowModuleUID'] ,'LEFT');
		} else {
			$this->db->select("'' AS WorkflowModuleUID,'' AS EntryDatetime,'' AS DueDateTime,'' AS AssignedToUserUID" ,NULL,FALSE);
		}

		$this->db->join('tOrderPropertyRole','tOrders.OrderUID = tOrderPropertyRole.OrderUID','left');

		$this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
		$this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','left');
		$this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
		
		if(!empty($post['OrderUID'])) {
			$this->db->where("tOrders.OrderUID IN (".$post['OrderUID'].")",NULL,FALSE);
		} else {
			$this->db->where("tOrders.OrderUID","");
		}

		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}

		$this->db->group_by('tOrders.OrderUID');
	}

	function count_all($post)
	{
		$this->fetchorder_query($post);
		$query = $this->db->count_all_results();
		return $query;
	}

	function count_filtered($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery(FALSE,TRUE,$post);

		$this->fetchorder_query($post);
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		$query = $this->db->get();
		return $query->num_rows();
	}

	function getOrders($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery(FALSE,TRUE,$post);

		$this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '.$this->config->item('Workflows')['Workup'].') AS Workupcount',false);

		$this->db->select("tOrders.*");
		$this->db->select("tOrderImport.*");
		$this->db->select("tOrderPropertyRole.*");

		$this->fetchorder_query($post);
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		if (isset($post['length']) && $post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		} 

		$query = $this->db->get();
		return $query->result();
	}

	function get_priorityagingcounts($post)
	{
		$CustomerUID = NULL;

		$Except_PriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_PriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$PriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('PriorityReport_OnlyMilestones'), 'MilestoneUID'));

		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}

		$AgingHeader = $this->config->item('PriorityAgingBucketHeader');

		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		$Pipelineexceptmilestone = '';

		if(!empty($Except_PriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_PriorityReport_PipelineMilestones.'))';
		}

		if(!empty($AgingHeader) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			$PipelineTotalWhere = [];
			foreach ($AgingHeader as $AgingHeaderkey => $AgingHeadervalue) {
				$AgingHeaderTotal = [];

				$CASEAGEWHERE = '';

				switch ($AgingHeaderkey) {
					case 'zerotothirtydays':
					$CASEAGEWHERE = "((tOrderImport.Aging IS NULL) OR (tOrderImport.Aging = '') OR (tOrderImport.Aging >= 0 AND tOrderImport.Aging <= 30)) ";
					break;
					case 'thirtyonetofortyfivedays':
					$CASEAGEWHERE = "tOrderImport.Aging >= 31 AND tOrderImport.Aging <= 45 ";
					break;
					case 'fortysixttosixtydays':
					$CASEAGEWHERE = "tOrderImport.Aging >= 46 AND tOrderImport.Aging <= 60 ";
					break;
					case 'sixtyonetoninetydays':
					$CASEAGEWHERE = "tOrderImport.Aging > 61 AND tOrderImport.Aging <= 90 ";
					break;
					case 'greaterthanninetydays':
					$CASEAGEWHERE = "tOrderImport.Aging > 90 ";
					break;

					default:
					break;
				}

				$PipelineANDWhere = $CASEAGEWHERE;

				if(!empty($PipelineANDWhere)) {

					$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $PipelineANDWhere . $Pipelineexceptmilestone. " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$AgingHeaderkey,FALSE);

					$PipelineTotalWhere[$AgingHeaderkey] = $PipelineANDWhere;
				}


				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);

					if(!empty($PriorityFields)) {

						$WHERE = [];

						if(!empty($CASEAGEWHERE)) {
							$WHERE[] = $CASEAGEWHERE;
						}

						if(!empty($PriorityReport_OnlyMilestones)) {
							$WHERE[]  = 'tOrders.MilestoneUID IN ('.$PriorityReport_OnlyMilestones.')';
						}

						foreach ($PriorityFields as $PriorityFieldrow) {

							// Expiry Checklist
							$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
							$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

							$CHECKLISTEXPCOND = [];
							$CHECKLISTEXPCASE = "";
							if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

								foreach ($workflowchecklist as $checklistkey => $checklistuid) {

									if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

									} else {

										if ($PriorityFieldrow->WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentDate IS NOT NULL AND tDocumentCheckList.DocumentDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										} else {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentExpiryDate IS NOT NULL AND tDocumentCheckList.DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										}
														
									}

								}

							}

							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = " AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN FALSE
										ELSE TRUE
									END)";
								}

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].")".$CHECKLISTEXPCASE;
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN TRUE
										ELSE FALSE
									END)";
								}

								$WHERE[] = "((TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].")) OR ( ".(!empty($CHECKLISTEXPCASE)? $CHECKLISTEXPCASE : "FALSE")."))";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
							}

						}

						if (!empty($WHERE)) {

							$PriorityLoopWHERE[] = implode(" AND ", $WHERE);
							$AgingHeaderTotal[] = implode(" AND ", $WHERE);
							$TotalWHERE[] = implode(" AND ", $WHERE);
							$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$AgingHeaderkey.'Priority'.$Priorityrow->PriorityUID);

						} else {

							$this->db->select("0 AS ".$AgingHeaderkey.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
						}

					} else {
						$this->db->select("0 AS ".$AgingHeaderkey.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
					}

					$PriorityMilestoneWhereArray[$Priorityrow->PriorityUID][$AgingHeaderkey] = implode(" OR ", $PriorityLoopWHERE);


				}

				//Milestone Total
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $AgingHeaderTotal) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$AgingHeaderkey.'Total');


			}

			//Priority Total
			if(!empty($PriorityMilestoneWhereArray) && !empty($Customer_Prioritys)) {
				foreach ($Customer_Prioritys as $Priorityrow) {
					$PriorityWhere = array_key_exists($Priorityrow->PriorityUID, $PriorityMilestoneWhereArray) ? $PriorityMilestoneWhereArray[$Priorityrow->PriorityUID] : [];
					if(!empty($PriorityWhere)) {
						
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PriorityWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Priority".$Priorityrow->PriorityUID."Total");
						
					}
				}
			}

			//Pending Total
			if(!empty($PipelineTotalWhere)) {

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineTotalWhere) . " ) ".$Pipelineexceptmilestone." THEN tOrders.OrderUID ELSE NULL END ) AS PendingTotal",FALSE);

			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}

			//overall Total

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal ");


		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$Customer_PriorityWorkflowstmp = array_keys($Customer_PriorityWorkflows);
		$last_key = end($Customer_PriorityWorkflowstmp);

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrowkey => $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityWorkflowrow->SystemName,   "TW_" .$PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityWorkflowrow->SystemName,  "TOA_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $PriorityWorkflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$PriorityWorkflowrowkey,$last_key);

			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $PriorityWorkflowrow->SystemName,  "TDC_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TDC_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $PriorityWorkflowrow->SystemName,  "TP_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TP_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");*/

		}


		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);


		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);*/


		$this->db->where_not_in('tOrders.StatusUID', $status);


		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		if(isset($post['ProductUID']) && !empty($post['ProductUID']) && ($post['ProductUID'] != "All")) {
			$this->db->where('tOrders.ProductUID',$post['ProductUID']);
		}

		if(isset($post['ProjectUID']) && !empty($post['ProjectUID']) && ($post['ProjectUID'] != "All")) {
			$this->db->where('tOrders.ProjectUID',$post['ProjectUID']);
		}

		if(isset($post['FromDate']) && !empty($post['FromDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "'.date('Y-m-d', strtotime($post['FromDate'])).'"', NULL, false);
		}

		if(isset($post['ToDate']) && !empty($post['ToDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="'.date('Y-m-d', strtotime($post['ToDate'])).'"',NULL, false);
		}

		return $this->db->get()->row();

	}

	function get_priorityprocessorcounts($post)
	{
		$CustomerUID = NULL;

		$Except_PriorityReport_Milestones = array_values($this->config->item('Except_PriorityReport_Milestones'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allprocessors();
		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);


		if(!empty($Users) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			foreach ($Users as $UserKey => $UserValue) {
				$UserHeaderTotal = [];


				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];


					$WHERE  = [];

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);

					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID = ".$UserValue->UserUID." AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID = ".$UserValue->UserUID." AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID = ".$UserValue->UserUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID = ".$UserValue->UserUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
							}
						}

						if (!empty($WHERE)) {

							$PriorityLoopWHERE[] = implode(" AND ", $WHERE);
							$UserHeaderTotal[] = implode(" AND ", $WHERE);
							$TotalWHERE[] = implode(" AND ", $WHERE);
							$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID);

						} else {

							$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
						}
					} else {
						$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
					}

					$PriorityMilestoneWhereArray[$Priorityrow->PriorityUID][$UserValue->UserUID] = implode(" OR ", $PriorityLoopWHERE);


				}

				//Milestone Total
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $UserHeaderTotal) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Total');


			}

			//Priority Total
			if(!empty($PriorityMilestoneWhereArray) && !empty($Customer_Prioritys)) {
				foreach ($Customer_Prioritys as $Priorityrow) {
					$PriorityWhere = array_key_exists($Priorityrow->PriorityUID, $PriorityMilestoneWhereArray) ? $PriorityMilestoneWhereArray[$Priorityrow->PriorityUID] : [];
					if(!empty($PriorityWhere)) {
						
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PriorityWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Priority".$Priorityrow->PriorityUID."Total");
						
					}
				}
			}

			//overall Total

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal ");


		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$Customer_PriorityWorkflowstmp = array_keys($Customer_PriorityWorkflows);
		$last_key = end($Customer_PriorityWorkflowstmp);

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrowkey => $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityWorkflowrow->SystemName,   "TW_" .$PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityWorkflowrow->SystemName,  "TOA_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $PriorityWorkflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$PriorityWorkflowrowkey,$last_key);

			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $PriorityWorkflowrow->SystemName,  "TDC_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TDC_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $PriorityWorkflowrow->SystemName,  "TP_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TP_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");*/
		}


		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);


		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);
		*/

		$this->db->where_not_in('tOrders.MilestoneUID', $Except_PriorityReport_Milestones);

		$this->db->where_not_in('tOrders.StatusUID', $status);

		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		if(isset($post['ProductUID']) && !empty($post['ProductUID']) && ($post['ProductUID'] != "All")) {
			$this->db->where('tOrders.ProductUID',$post['ProductUID']);
		}

		if(isset($post['ProjectUID']) && !empty($post['ProjectUID']) && ($post['ProjectUID'] != "All")) {
			$this->db->where('tOrders.ProjectUID',$post['ProjectUID']);
		}

		if(isset($post['FromDate']) && !empty($post['FromDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "'.date('Y-m-d', strtotime($post['FromDate'])).'"', NULL, false);
		}

		if(isset($post['ToDate']) && !empty($post['ToDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="'.date('Y-m-d', strtotime($post['ToDate'])).'"',NULL, false);
		}

		return $this->db->get()->row();

	}

	function get_priorityteamleadcounts($post)
	{
		$CustomerUID = NULL;
		$Except_PriorityReport_PipelineMilestones = implode(',', array_values($this->config->item('Except_PriorityReport_PipelineMilestones')));
		$PriorityReport_OnlyMilestones = implode(',', array_values($this->config->item('PriorityReport_OnlyMilestones')));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allteamleads();
		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);


		if(!empty($Users) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			foreach ($Users as $UserKey => $UserValue) {
				$UserHeaderTotal = [];

				//fetch groupusers for team leads groups
				$UsersinGroupsArray = $this->Common_Model->get_groupusersbyteamleads($UserValue->UserUID);
				$AssignedToUserUIDs = array_column($UsersinGroupsArray, 'UserUID');
				$AssignedToUserUID = implode(',', $AssignedToUserUIDs);
				if(empty($AssignedToUserUID)) {
					$AssignedToUserUID = 0;
				}

				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];
					$WHERE  = [];

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);
					
					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID IN (".$AssignedToUserUID.") AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID IN (".$AssignedToUserUID.") AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID IN (".$AssignedToUserUID.") AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TOA_" . $PriorityFieldrow->SystemName.".AssignedToUserUID IN (".$AssignedToUserUID.") AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
							}
						}

						if (!empty($WHERE)) {

							$PriorityLoopWHERE[] = implode(" AND ", $WHERE);
							$UserHeaderTotal[] = implode(" AND ", $WHERE);
							$TotalWHERE[] = implode(" AND ", $WHERE);
							$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID);

						} else {

							$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
						}
					} else {
						$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
					}


					$PriorityMilestoneWhereArray[$Priorityrow->PriorityUID][$UserValue->UserUID] = implode(" OR ", $PriorityLoopWHERE);


				}

				//Milestone Total
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $UserHeaderTotal) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Total');


			}

			//Priority Total
			if(!empty($PriorityMilestoneWhereArray) && !empty($Customer_Prioritys)) {
				foreach ($Customer_Prioritys as $Priorityrow) {
					$PriorityWhere = array_key_exists($Priorityrow->PriorityUID, $PriorityMilestoneWhereArray) ? $PriorityMilestoneWhereArray[$Priorityrow->PriorityUID] : [];
					if(!empty($PriorityWhere)) {

						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PriorityWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Priority".$Priorityrow->PriorityUID."Total");

					}
				}
			}

			//overall Total

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal ");

		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$Customer_PriorityWorkflowstmp = array_keys($Customer_PriorityWorkflows);
		$last_key = end($Customer_PriorityWorkflowstmp);

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrowkey => $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityWorkflowrow->SystemName,   "TW_" .$PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityWorkflowrow->SystemName,  "TOA_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $PriorityWorkflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$PriorityWorkflowrowkey,$last_key);
			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $PriorityWorkflowrow->SystemName,  "TDC_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TDC_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $PriorityWorkflowrow->SystemName,  "TP_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TP_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");*/
		}


		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);


		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);*/


		$this->db->where_not_in('tOrders.MilestoneUID', $Except_PriorityReport_Milestones);

		$this->db->where_not_in('tOrders.StatusUID', $status);


		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		if(isset($post['ProductUID']) && !empty($post['ProductUID']) && ($post['ProductUID'] != "All")) {
			$this->db->where('tOrders.ProductUID',$post['ProductUID']);
		}

		if(isset($post['ProjectUID']) && !empty($post['ProjectUID']) && ($post['ProjectUID'] != "All")) {
			$this->db->where('tOrders.ProjectUID',$post['ProjectUID']);
		}

		if(isset($post['FromDate']) && !empty($post['FromDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "'.date('Y-m-d', strtotime($post['FromDate'])).'"', NULL, false);
		}

		if(isset($post['ToDate']) && !empty($post['ToDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="'.date('Y-m-d', strtotime($post['ToDate'])).'"',NULL, false);
		}

		return $this->db->get()->row();

	}

	function get_priorityloantypecounts($post)
	{
		$CustomerUID = NULL;
		// $Except_PriorityReport_PipelineMilestones = implode(',', array_values($this->config->item('Except_PriorityReport_PipelineMilestones')));
		// $PriorityReport_OnlyMilestones = implode(',', array_values($this->config->item('PriorityReport_OnlyMilestones')));

		$Except_PriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_PriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$PriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('PriorityReport_OnlyMilestones'), 'MilestoneUID'));

		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$LoanTypes = $this->config->item('LoanTypes');
		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);


		$Pipelineexceptmilestone = '';

		if(!empty($Except_PriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_PriorityReport_PipelineMilestones.'))';
		}


		if(!empty($LoanTypes) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			$PipelineTotalWhere = [];
			foreach ($LoanTypes as $LoanTypeKey => $LoanType	) {
				$LoanTypeTotal = [];

				$PipelineANDWhere = "tOrders.LoanType = '".$LoanType."'";


				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $PipelineANDWhere . $Pipelineexceptmilestone. " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$LoanTypeKey,FALSE);

				$PipelineTotalWhere[$LoanTypeKey] = $PipelineANDWhere;

				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];

					$WHERE  = [];

					if(!empty($PipelineANDWhere)) {

						$WHERE[] = $PipelineANDWhere;
					}

					if(!empty($PriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$PriorityReport_OnlyMilestones.')';
					}

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);
					
					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							// Expiry Checklist
							$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
							$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

							$CHECKLISTEXPCOND = [];
							$CHECKLISTEXPCASE = "";
							if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

								foreach ($workflowchecklist as $checklistkey => $checklistuid) {

									if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

									} else {

										if ($PriorityFieldrow->WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentDate IS NOT NULL AND tDocumentCheckList.DocumentDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										} else {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentExpiryDate IS NOT NULL AND tDocumentCheckList.DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										}
														
									}

								}

							}

							//pending or Completed

							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = " AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN FALSE
										ELSE TRUE
									END)";
								}

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].")".$CHECKLISTEXPCASE;

								//$WHERE[] = " tOrders.LoanType = '".$LoanType."' AND TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN TRUE
										ELSE FALSE
									END)";
								}

								//$WHERE[] = " tOrders.LoanType = '".$LoanType."' AND TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";

								$WHERE[] = "((TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].")) OR ( ".(!empty($CHECKLISTEXPCASE)? $CHECKLISTEXPCASE : "FALSE")."))";
							}
						}


						if (!empty($WHERE)) {

							$PriorityLoopWHERE[] = implode(" AND ", $WHERE);
							$LoanTypeTotal[] = implode(" AND ", $WHERE);
							$TotalWHERE[] = implode(" AND ", $WHERE);
							$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$LoanTypeKey	.'Priority'.$Priorityrow->PriorityUID);

						} else {

							$this->db->select("0 AS ".$LoanTypeKey	.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
						}


					} else {
						$this->db->select("0 AS ".$LoanTypeKey	.'Priority'.$Priorityrow->PriorityUID,NULL,FALSE);
					}

					$PriorityMilestoneWhereArray[$Priorityrow->PriorityUID][$LoanTypeKey] = implode(" OR ", $PriorityLoopWHERE);


				}

				//Milestone Total
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $LoanTypeTotal) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$LoanTypeKey.'Total');


			}

			//Priority Total
			if(!empty($PriorityMilestoneWhereArray) && !empty($Customer_Prioritys)) {
				foreach ($Customer_Prioritys as $Priorityrow) {
					$PriorityWhere = array_key_exists($Priorityrow->PriorityUID, $PriorityMilestoneWhereArray) ? $PriorityMilestoneWhereArray[$Priorityrow->PriorityUID] : [];
					if(!empty($PriorityWhere)) {

						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PriorityWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Priority".$Priorityrow->PriorityUID."Total");

					}
				}
			}


			//Pending Total
			if(!empty($PipelineTotalWhere)) {

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineTotalWhere) . " ) ".$Pipelineexceptmilestone." THEN tOrders.OrderUID ELSE NULL END ) AS PendingTotal",FALSE);

			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}
			
			//overall Total

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal ");


		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$Customer_PriorityWorkflowstmp = array_keys($Customer_PriorityWorkflows);
		$last_key = end($Customer_PriorityWorkflowstmp);

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrowkey => $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityWorkflowrow->SystemName,   "TW_" .$PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityWorkflowrow->SystemName,  "TOA_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $PriorityWorkflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$PriorityWorkflowrowkey,$last_key);

			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $PriorityWorkflowrow->SystemName,  "TDC_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TDC_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $PriorityWorkflowrow->SystemName,  "TP_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TP_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");*/

		}


		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);


		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);*/


		$this->db->where_not_in('tOrders.StatusUID', $status);


		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		if(isset($post['ProductUID']) && !empty($post['ProductUID']) && ($post['ProductUID'] != "All")) {
			$this->db->where('tOrders.ProductUID',$post['ProductUID']);
		}

		if(isset($post['ProjectUID']) && !empty($post['ProjectUID']) && ($post['ProjectUID'] != "All")) {
			$this->db->where('tOrders.ProjectUID',$post['ProjectUID']);
		}

		if(isset($post['FromDate']) && !empty($post['FromDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "'.date('Y-m-d', strtotime($post['FromDate'])).'"', NULL, false);
		}

		if(isset($post['ToDate']) && !empty($post['ToDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="'.date('Y-m-d', strtotime($post['ToDate'])).'"',NULL, false);
		}

		return $this->db->get()->row();

	}

	function get_priorityonshoreprocessorcounts($post)
	{
		$CustomerUID = NULL;
		$Except_PriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_PriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$PriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('PriorityReport_OnlyMilestones'), 'MilestoneUID'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allonshoreprocessors();
		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		$Pipelineexceptmilestone = '';

		if(!empty($Except_PriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_PriorityReport_PipelineMilestones.'))';
		}

		if(!empty($Users) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PipelineTotalWhere = [];
			$PriorityMilestoneWhereArray = [];
			foreach ($Users as $UserKey => $UserValue) {
				$UserHeaderTotal = [];

				$CASEWHERE = "tOrderImport.LoanProcessor LIKE '%".$this->db->escape_str($UserValue->UserName,true)."%'";

				$PipelineANDWhere = $CASEWHERE;

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $PipelineANDWhere . $Pipelineexceptmilestone. " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$UserValue->UserUID,FALSE);

				$PipelineTotalWhere[$UserValue->UserUID] = $PipelineANDWhere;

				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];


					$WHERE  = [];

					if(!empty($CASEWHERE)) {
						$WHERE[] =  $CASEWHERE;
					}

					if(!empty($PriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$PriorityReport_OnlyMilestones.')';
					}

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);

					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							// Expiry Checklist
							$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
							$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

							$CHECKLISTEXPCOND = [];
							$CHECKLISTEXPCASE = "";
							if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

								foreach ($workflowchecklist as $checklistkey => $checklistuid) {

									if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

									} else {

										if ($PriorityFieldrow->WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentDate IS NOT NULL AND tDocumentCheckList.DocumentDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										} else {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentExpiryDate IS NOT NULL AND tDocumentCheckList.DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										}
														
									}

								}

							}

							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = " AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN FALSE
										ELSE TRUE
									END)";
								}

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].")".$CHECKLISTEXPCASE;

							} else {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN TRUE
										ELSE FALSE
									END)";
								}

								$WHERE[] = "((TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].")) OR ( ".(!empty($CHECKLISTEXPCASE)? $CHECKLISTEXPCASE : "FALSE")."))";
							}
						}

						if (!empty($WHERE)) {

							$PriorityLoopWHERE[] = implode(" AND ", $WHERE);
							$UserHeaderTotal[] = implode(" AND ", $WHERE);
							$TotalWHERE[] = implode(" AND ", $WHERE);
							$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,FALSE);

						} else {

							$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,FALSE);
						}
					} else {
						$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,FALSE);
					}

					$PriorityMilestoneWhereArray[$Priorityrow->PriorityUID][$UserValue->UserUID] = implode(" OR ", $PriorityLoopWHERE);


				}

				//Milestone Total
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $UserHeaderTotal) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Total',FALSE);


			}

			//Priority Total
			if(!empty($PriorityMilestoneWhereArray) && !empty($Customer_Prioritys)) {
				foreach ($Customer_Prioritys as $Priorityrow) {
					$PriorityWhere = array_key_exists($Priorityrow->PriorityUID, $PriorityMilestoneWhereArray) ? $PriorityMilestoneWhereArray[$Priorityrow->PriorityUID] : [];
					if(!empty($PriorityWhere)) {
						
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PriorityWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Priority".$Priorityrow->PriorityUID."Total",false);
						
					}
				}
			}

			//Pending Total
			if(!empty($PipelineTotalWhere)) {

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineTotalWhere) . " ) ".$Pipelineexceptmilestone." THEN tOrders.OrderUID ELSE NULL END ) AS PendingTotal",FALSE);

			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}
			//overall Total

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal ",FALSE);


		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');


		$Customer_PriorityWorkflowstmp = array_keys($Customer_PriorityWorkflows);
		$last_key = end($Customer_PriorityWorkflowstmp);

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrowkey => $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityWorkflowrow->SystemName,   "TW_" .$PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityWorkflowrow->SystemName,  "TOA_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $PriorityWorkflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$PriorityWorkflowrowkey,$last_key);

		}



		$this->db->where_not_in('tOrders.StatusUID', $status);


		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		if(isset($post['ProductUID']) && !empty($post['ProductUID']) && ($post['ProductUID'] != "All")) {
			$this->db->where('tOrders.ProductUID',$post['ProductUID']);
		}

		if(isset($post['ProjectUID']) && !empty($post['ProjectUID']) && ($post['ProjectUID'] != "All")) {
			$this->db->where('tOrders.ProjectUID',$post['ProjectUID']);
		}

		if(isset($post['FromDate']) && !empty($post['FromDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "'.date('Y-m-d', strtotime($post['FromDate'])).'"', NULL, false);
		}

		if(isset($post['ToDate']) && !empty($post['ToDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="'.date('Y-m-d', strtotime($post['ToDate'])).'"',NULL, false);
		}

		return $this->db->get()->row();

	}

	function get_priorityonshoreteamleadcounts($post)
	{
		$CustomerUID = NULL;
		$Except_PriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_PriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$PriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('PriorityReport_OnlyMilestones'), 'MilestoneUID'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);
		$Except_PriorityReport_Milestones = !empty($this->config->item('Except_PriorityReport_Milestones')) ? array_values($this->config->item('Except_PriorityReport_Milestones')) : FALSE;

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allonshoreteamleads();
		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		$Pipelineexceptmilestone = '';

		if(!empty($Except_PriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_PriorityReport_PipelineMilestones.'))';
		}

		if(!empty($Users) && !empty($Customer_Prioritys)) {

			$PipelineTotalWhere = [];
			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			foreach ($Users as $UserKey => $UserValue) {
				$UserHeaderTotal = [];
					//fetch groupusers for team leads groups
				$UsersinGroupsArray = $this->Common_Model->get_groupusersbyteamleads($UserValue->UserUID);
				$PipelineANDWhere = [];
				$PipelineANDWhere[] = 'tOrderImport.LoanProcessor LIKE "%'.$this->db->escape_str($UserValue->UserName,TRUE).'%"';
				foreach($UsersinGroupsArray as $GroupUser){

					$PipelineANDWhere[] = 'tOrderImport.LoanProcessor LIKE "%'.$this->db->escape_str($GroupUser->UserName,TRUE).'%"';

				}


				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineANDWhere) . " ) ".$Pipelineexceptmilestone." THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$UserValue->UserUID,FALSE);

				$PipelineTotalWhere[$UserValue->UserUID] = $PipelineANDWhere;

				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];
					$WHERE  = [];

					if(!empty($PriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$PriorityReport_OnlyMilestones.')';
					}


					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);
					
					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							// Expiry Checklist
							$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
							$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

							$CHECKLISTEXPCOND = [];
							$CHECKLISTEXPCASE = "";
							if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

								foreach ($workflowchecklist as $checklistkey => $checklistuid) {

									if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

									} else {

										if ($PriorityFieldrow->WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentDate IS NOT NULL AND tDocumentCheckList.DocumentDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										} else {

											$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentExpiryDate IS NOT NULL AND tDocumentCheckList.DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
										}
														
									}

								}

							}

							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = " AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN FALSE
										ELSE TRUE
									END)";
								}

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") AND (" . implode(" OR ", $PipelineANDWhere) . " )".$CHECKLISTEXPCASE;

							} else {

								if (!empty($CHECKLISTEXPCOND)) {

									$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
									
									$CHECKLISTEXPCASE = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." AND 
									(CASE 
										WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN TRUE
										ELSE FALSE
									END)";
								}

								$WHERE[] = "((TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") AND (" . implode(" OR ", $PipelineANDWhere) . " )) OR ((" . implode(" OR ", $PipelineANDWhere) . " ) AND ".(!empty($CHECKLISTEXPCASE)? $CHECKLISTEXPCASE : "FALSE")."))";
							}
						}

						if (!empty($WHERE)) {

							$PriorityLoopWHERE[] = implode(" AND ", $WHERE);
							$UserHeaderTotal[] = implode(" AND ", $WHERE);
							$TotalWHERE[] = implode(" AND ", $WHERE);
							$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,FALSE);

						} else {

							$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,FALSE);
						}
					} else {
						$this->db->select("0 AS ".$UserValue->UserUID.'Priority'.$Priorityrow->PriorityUID,FALSE);
					}


					$PriorityMilestoneWhereArray[$Priorityrow->PriorityUID][$UserValue->UserUID] = implode(" OR ", $PriorityLoopWHERE);


				}

				//Milestone Total
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $UserHeaderTotal) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$UserValue->UserUID.'Total',FALSE);


			}


			//Pending Total
			if(!empty($PipelineTotalWhere)) {

				$PipelineTotalWherearray = [];

				foreach ($PipelineTotalWhere as $PipelineTotalWhereValue) {

					if(is_array($PipelineTotalWhereValue)) {

						foreach ($PipelineTotalWhereValue as  $PipelineTotalWheresubValue) {
							$PipelineTotalWherearray[] = $PipelineTotalWheresubValue;
						}

					} else {
						$PipelineTotalWherearray[] = $PipelineTotalWhereValue;
					}

				}
				
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineTotalWherearray) . " )". $Pipelineexceptmilestone." THEN tOrders.OrderUID ELSE NULL END ) AS PendingTotal",FALSE);


			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}

			//Priority Total
			if(!empty($PriorityMilestoneWhereArray) && !empty($Customer_Prioritys)) {
				foreach ($Customer_Prioritys as $Priorityrow) {
					$PriorityWhere = array_key_exists($Priorityrow->PriorityUID, $PriorityMilestoneWhereArray) ? $PriorityMilestoneWhereArray[$Priorityrow->PriorityUID] : [];
					if(!empty($PriorityWhere)) {

						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PriorityWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Priority".$Priorityrow->PriorityUID."Total",FALSE);

					}
				}
			}

			//overall Total

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal",FALSE);

		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
		
		$Customer_PriorityWorkflowstmp = array_keys($Customer_PriorityWorkflows);
		$last_key = end($Customer_PriorityWorkflowstmp);

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrowkey => $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityWorkflowrow->SystemName,   "TW_" .$PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityWorkflowrow->SystemName,  "TOA_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $PriorityWorkflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$PriorityWorkflowrowkey,$last_key);

			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $PriorityWorkflowrow->SystemName,  "TDC_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TDC_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $PriorityWorkflowrow->SystemName,  "TP_" . $PriorityWorkflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $PriorityWorkflowrow->SystemName.".WorkflowModuleUID = '".$PriorityWorkflowrow->WorkflowModuleUID."' AND  TP_" . $PriorityWorkflowrow->SystemName.".IsCleared = 0", "LEFT");*/

		}


		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);


		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);*/


		$this->db->where_not_in('tOrders.MilestoneUID', $Except_PriorityReport_Milestones);

		$this->db->where_not_in('tOrders.StatusUID', $status);


		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		if(isset($post['ProductUID']) && !empty($post['ProductUID']) && ($post['ProductUID'] != "All")) {
			$this->db->where('tOrders.ProductUID',$post['ProductUID']);
		}

		if(isset($post['ProjectUID']) && !empty($post['ProjectUID']) && ($post['ProjectUID'] != "All")) {
			$this->db->where('tOrders.ProjectUID',$post['ProjectUID']);
		}

		if(isset($post['FromDate']) && !empty($post['FromDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "'.date('Y-m-d', strtotime($post['FromDate'])).'"', NULL, false);
		}

		if(isset($post['ToDate']) && !empty($post['ToDate'])) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="'.date('Y-m-d', strtotime($post['ToDate'])).'"',NULL, false);
		}

		return $this->db->get()->row();

	}

	function get_prioritytable_workflows()
	{
		$PriorityWorkflows = $this->config->item('PriorityWorkflows');
		$this->db->select('*');
		$this->db->from('mWorkFlowModules');
		$this->db->where('mWorkFlowModules.Active', STATUS_ONE);
		$this->db->where_in('mWorkFlowModules.WorkflowModuleUID', $PriorityWorkflows);
		$this->db->_protect_identifiers = FALSE;
		if(!empty($PriorityWorkflows)) {
			$this ->db->order_by("FIELD(mWorkFlowModules.WorkflowModuleUID, ".implode(",",$PriorityWorkflows).")");
		}
		$this->db->_protect_identifiers = TRUE;
		return $this->db->get()->result();
	}

	function updateprioritycomments($OrderUID, $Comments) {
		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('tOrderImport', array('OrderComments' => $Comments));
		return $this->db->affected_rows();
	}

	function UpdateDynamicColumn($OrderUID, $value, $ColumnName) {
		$this->load->dbforge();
		if (!$this->db->field_exists($ColumnName, 'tOrderImport')) {
			$this->dbforge->add_column('tOrderImport', $ColumnName);
			$this->db->query($sql);
		}
		$value = !empty($value) ? date('Y-m-d H:i:s',strtotime($value)) : NULL;
		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('tOrderImport',  array($ColumnName => $value));
		return TRUE;
	}

	function get_settingsmilestones($SettingField)
	{
		$CustomerUID = 0;

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}
		
		$row = $this->db->where(array('Active'=>1,'SettingField'=>$SettingField,'CustomerUID'=>$CustomerUID))->get('mSettings')->row();
		if(empty($row)) {
			return [];
		}
		$MilestoneUIDs = $row->SettingValue;
		if(empty($MilestoneUIDs)) {
			return [];
		}

		$this->db->select('*');
		$this->db->from('mMilestone');
		$this->db->where('mMilestone.Active', STATUS_ONE);
		$this->db->where_in('MilestoneUID',explode(',', $MilestoneUIDs));
		return $this->db->get()->result();
	}

	/**
	*Function function to update workflow comments 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Wednesday 22 July 2020.
	*/
	function updateworkflowcomments($OrderUID, $WorkflowModuleUID, $Comments) {

		$CommentsRow = $this->Common_Model->get_row('tOrderComments', ['OrderUID'=>$OrderUID,'WorkflowUID'=>$WorkflowModuleUID]);
		if(!empty($CommentsRow)) {
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('WorkflowUID', $WorkflowModuleUID);
			$this->db->update('tOrderComments', array('Description' => $Comments,'CreatedByUserUID'=>$this->loggedid,'CreateDateTime'=>date('Y-m-d H:i:s')));
		} else {
			$this->db->insert('tOrderComments', array('OrderUID'=> $OrderUID,'WorkflowUID'=> $WorkflowModuleUID,'Description' => $Comments,'CreatedByUserUID'=>$this->loggedid,'CreateDateTime'=>date('Y-m-d H:i:s')));
		}

		$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

		/*INSERT ORDER LOGS BEGIN*/
		$this->Common_Model->OrderLogsHistory($OrderUID, $Workflow->WorkflowModuleName . ' Comments - '.$Comments .' Updated', date('Y-m-d H:i:s'));
		/*INSERT ORDER LOGS END*/

		return $this->db->affected_rows();
	}

	/**
	*Function to fetch LoanNumber 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 01 September 2020.
	*/
	function get_loannumber($OrderUID)
	{
		return $this->db->select('LoanNumber')->from('tOrders')->where('OrderUID',$OrderUID)->get()->row();
	}

	/**
	*Function to fetch Workflowname 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 01 September 2020.
	*/
	function get_workflowname($WorkflowModuleUID)
	{
		return $this->db->select('WorkflowModuleUID')->from('mWorkFlowModules')->where('WorkflowModuleUID',$WorkflowModuleUID)->get()->row();
	}

	/**
	*Function to fetch unreadnotes 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 01 September 2020.
	*/
	function get_unreadnotes($OrderUID,$WorkflowModuleUID,$UserUID)
	{
		$this->db->select('*');
		$this->db->from('tNotes');
		$this->db->where("NOT EXISTS (SELECT 1 FROM tNotesUser WHERE tNotesUser.NotesUID = tNotes.NotesUID AND IsRead = 1 AND UserUID = ".$UserUID.")", NULL, FALSE);
		$this->db->where("(CreatedByUserUID IS NULL OR CreatedByUserUID <> ".$UserUID." )", NULL, FALSE);
		$this->db->where(['tNotes.OrderUID'=>$OrderUID,'tNotes.WorkflowUID'=>$WorkflowModuleUID]);
		return $this->db->count_all_results();
	}

	/**
	*Function function to update comments counts 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 03 September 2020.
	*/
	function update_notesread($OrderUID,$WorkflowModuleUID,$UserUID)
	{

		$this->db->select('*');
		$this->db->from('tNotes');
		$this->db->where("NOT EXISTS (SELECT 1 FROM tNotesUser WHERE tNotesUser.NotesUID = tNotes.NotesUID AND UserUID = ".$UserUID.")", NULL, FALSE);
		$this->db->where(['tNotes.OrderUID'=>$OrderUID,'tNotes.WorkflowUID'=>$WorkflowModuleUID]);
		$result =  $this->db->get()->result_array();

		if(!empty($result)) {

			$insertarray = [];
			foreach ($result as $value) {
				$insertarray[] = ['NotesUID'=>$value['NotesUID'],'UserUID'=>$UserUID];
			}

			if(!empty($insertarray)) {
				$this->db->insert_batch('tNotesUser', $insertarray);
			}

		}

		//update the read status 	
		$this->db->query('UPDATE `tNotesUser` JOIN `tNotes` ON `tNotesUser`.`NotesUID` = `tNotes`.`NotesUID` SET `IsRead` = 1, `ReadDateTime` = "'.date('Y-m-d H:i:s').'"  WHERE `tNotes`.`OrderUID` = '.$OrderUID.' AND `tNotes`.`WorkflowUID` = '.$WorkflowModuleUID.' AND `tNotesUser`.`UserUID` = '.$UserUID.' AND `tNotesUser`.`IsRead` = 0');
		return $this->db->affected_rows();
	}

	/**
	*Function Fetch Loan Info 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 01 October 2020.
	*/
	function GetLoanInfo($OrderUID) {
		$this->db->select('tOrders.LoanNumber, tOrderImport.MP, tOrderImport.BwrEmail, tOrderImport.ZipCode, tOrderImport.DOB, tOrderImport.Term, tOrderImport.NoteRate, tOrders.LoanType');
		$this->db->select("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName)) as BorrowerName");
		$PendingDocs_columnquery = "( SELECT 
			GROUP_CONCAT( DISTINCT
			CASE
			WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
			CASE 
			WHEN tDocumentCheckList.DocumentTypeUID = '' OR tDocumentCheckList.DocumentTypeUID IS NULL THEN
			tDocumentCheckList.DocumentTypeName
			ELSE						
			mDocumentType.DocumentTypeName
			END

			ELSE
			NULL
			END
			) FROM tDocumentCheckList LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` AND mDocumentType.Active = 1
			WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` 
			AND `tDocumentCheckList`.`WorkflowUID` = ".$this->config->item('Workflows')['PreScreen']."
		)";
		$this->db->select($PendingDocs_columnquery." AS PendingDocs");
		// Title - Issues in Title Processor Confirmation 
		$TitleSubQueueIssue = "( SELECT 
			GROUP_CONCAT( DISTINCT
			CASE
			WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
			CASE 
			WHEN tDocumentCheckList.DocumentTypeUID = '' OR tDocumentCheckList.DocumentTypeUID IS NULL THEN
			tDocumentCheckList.DocumentTypeName
			ELSE						
			mDocumentType.DocumentTypeName
			END

			ELSE
			NULL
			END
			) FROM tDocumentCheckList 
			LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` AND mDocumentType.Active = 1
			INNER JOIN `tOrderQueues` ON `tOrderQueues`.`QueueUID` = ".$this->config->item('TitleProcessorConfirmation')." AND `tOrderQueues`.`QueueStatus` = 'Pending'
			WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` 
			AND `tDocumentCheckList`.`WorkflowUID` = ".$this->config->item('Workflows')['TitleTeam']."
			AND `tOrderQueues`.`OrderUID` = `tOrders`.`OrderUID`
		)";
		$this->db->select($TitleSubQueueIssue." AS TitleSubQueueIssue");
		// HOI - Hoi Email Processor 
		$HOISubQueueIssue = "( SELECT 
			GROUP_CONCAT( DISTINCT
			CASE
			WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
			CASE 
			WHEN tDocumentCheckList.DocumentTypeUID = '' OR tDocumentCheckList.DocumentTypeUID IS NULL THEN
			tDocumentCheckList.DocumentTypeName
			ELSE						
			mDocumentType.DocumentTypeName
			END

			ELSE
			NULL
			END
			) FROM tDocumentCheckList 
			LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` AND mDocumentType.Active = 1
			INNER JOIN `tOrderQueues` ON `tOrderQueues`.`QueueUID` = ".$this->config->item('HoiEmailProcessor')." AND `tOrderQueues`.`QueueStatus` = 'Pending'
			WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` 
			AND `tDocumentCheckList`.`WorkflowUID` = ".$this->config->item('Workflows')['HOI']."
			AND `tOrderQueues`.`OrderUID` = `tOrders`.`OrderUID`
		)";
		$this->db->select($HOISubQueueIssue." AS HOISubQueueIssue");
		// FHA - FHA Processor Confimation 
		$FHASubQueueIssue = "( SELECT 
			GROUP_CONCAT( DISTINCT
			CASE
			WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
			CASE 
			WHEN tDocumentCheckList.DocumentTypeUID = '' OR tDocumentCheckList.DocumentTypeUID IS NULL THEN
			tDocumentCheckList.DocumentTypeName
			ELSE						
			mDocumentType.DocumentTypeName
			END

			ELSE
			NULL
			END
			) FROM tDocumentCheckList 
			LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` AND mDocumentType.Active = 1
			INNER JOIN `tOrderQueues` ON `tOrderQueues`.`QueueUID` = ".$this->config->item('FHAProcessorConfimation')." AND `tOrderQueues`.`QueueStatus` = 'Pending'
			WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` 
			AND `tDocumentCheckList`.`WorkflowUID` = ".$this->config->item('Workflows')['FHAVACaseTeam']."
			AND `tOrderQueues`.`OrderUID` = `tOrders`.`OrderUID`
		)";
		$this->db->select($FHASubQueueIssue." AS FHASubQueueIssue");
		// Processor Info
		$this->db->select('mProcessor.*');
		$this->db->from('tOrders');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mProcessor', 'LOCATE(mProcessor.FirstName, tOrderImport.LoanProcessor)', 'left');
		// $this->db->join('mProcessor', '(mProcessor.FirstName LIKE "%tOrderImport.LoanProcessor%" OR mProcessor.LastName LIKE "%tOrderImport.LoanProcessor%")', 'left');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('mProcessor.ClientUID', $this->parameters['DefaultClientUID']);
		$this->db->where('mProcessor.Active', STATUS_ONE);
		return $this->db->get()->row();
	}

}
?>
