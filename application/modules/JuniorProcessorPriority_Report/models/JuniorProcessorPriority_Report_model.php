<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class JuniorProcessorPriority_Report_model extends MY_Model {

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

		$Except_JuniorPriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_JuniorPriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$JuniorPriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('JuniorPriorityReport_OnlyMilestones'), 'MilestoneUID'));

		$Milestones = $this->get_settingsmilestones('JuniorPriorityReport_MilestonesList');

		$Customer_Prioritys = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->JuniorProcessorPriority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		if(!empty($Milestones) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			$PipelineTotalWhere = [];

			foreach ($Milestones as $Milestonekey => $Milestone) {

				$MilestoneTotalWHERE = [];

				if(!empty($Except_JuniorPriorityReport_PipelineMilestones)) {
					$PipelineANDWhere = "mMilestone.MilestoneName = '".$Milestone->MilestoneName."'"." AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN (".$Except_JuniorPriorityReport_PipelineMilestones."))";
				} else {
					$PipelineANDWhere = "mMilestone.MilestoneName = '".$Milestone->MilestoneName."'";
				}

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $PipelineANDWhere . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$Milestone->MilestoneUID,FALSE);

				$PipelineTotalWhere[$Milestone->MilestoneUID] = $PipelineANDWhere;

				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];

					$WHERE = [];

					if(!empty($JuniorPriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$JuniorPriorityReport_OnlyMilestones.')';
					}

					if(!empty($PipelineANDWhere)) {

						$WHERE[] = $PipelineANDWhere;
					}	

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);

					if(!empty($PriorityFields)) {


						foreach ($PriorityFields as $PriorityFieldrow) {

							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].")";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].")";
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

		$last_key = end(array_keys($Customer_PriorityWorkflows));

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

		// Junior Processor Report Conditions
		$this->JuniorProcessorReportConditions();

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

		$Except_JuniorPriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_JuniorPriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$JuniorPriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('JuniorPriorityReport_OnlyMilestones'), 'MilestoneUID'));

		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}

		$AgingHeader = $this->config->item('PriorityAgingBucketHeader');

		$Customer_Prioritys = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->JuniorProcessorPriority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		$Pipelineexceptmilestone = '';

		if(!empty($Except_JuniorPriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_JuniorPriorityReport_PipelineMilestones.'))';
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
					$CASEAGEWHERE = "tOrderImport.Aging >= 0 AND tOrderImport.Aging <= 30 ";
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

						if(!empty($JuniorPriorityReport_OnlyMilestones)) {
							$WHERE[]  = 'tOrders.MilestoneUID IN ('.$JuniorPriorityReport_OnlyMilestones.')';
						}

						foreach ($PriorityFields as $PriorityFieldrow) {


							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";
								//$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
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

		$last_key = end(array_keys($Customer_PriorityWorkflows));

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

		// Junior Processor Report Conditions
		$this->JuniorProcessorReportConditions();

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
		$Customer_Prioritys = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->JuniorProcessorPriority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);


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

		$last_key = end(array_keys($Customer_PriorityWorkflows));

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
		$Except_JuniorPriorityReport_PipelineMilestones = implode(',', array_values($this->config->item('Except_JuniorPriorityReport_PipelineMilestones')));
		$JuniorPriorityReport_OnlyMilestones = implode(',', array_values($this->config->item('JuniorPriorityReport_OnlyMilestones')));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allteamleads();
		$Customer_Prioritys = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->JuniorProcessorPriority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);


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

		$last_key = end(array_keys($Customer_PriorityWorkflows));

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
		$Except_JuniorPriorityReport_PipelineMilestones = implode(',', array_values($this->config->item('Except_JuniorPriorityReport_PipelineMilestones')));
		$JuniorPriorityReport_OnlyMilestones = implode(',', array_values($this->config->item('JuniorPriorityReport_OnlyMilestones')));

		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$LoanTypes = $this->config->item('LoanTypes');
		$Customer_Prioritys = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->JuniorProcessorPriority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);


		$Pipelineexceptmilestone = '';

		if(!empty($Except_JuniorPriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_JuniorPriorityReport_PipelineMilestones.'))';
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

					if(!empty($JuniorPriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$JuniorPriorityReport_OnlyMilestones.')';
					}

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);
					
					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							//pending or Completed

							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

								//$WHERE[] = " tOrders.LoanType = '".$LoanType."' AND TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";

							} else {

								//$WHERE[] = " tOrders.LoanType = '".$LoanType."' AND TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TDC_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND TP_".$PriorityFieldrow->SystemName.".IsCleared IS NULL AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";
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

		$last_key = end(array_keys($Customer_PriorityWorkflows));

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

		// Junior Processor Report Conditions
		$this->JuniorProcessorReportConditions();

		return $this->db->get()->row();

	}

	public function JuniorProcessorReportConditions()
	{
		// check order exist for exception queue
		$ProcessorsQueueDetailsArr = $this->Common_Model->GetJuniorProcessorQueueDetails();
		// echo '<pre>';print_r($ProcessorsQueueDetailsArr);exit;
		if (!empty($ProcessorsQueueDetailsArr)) {

			$this->db->group_start();
			foreach ($ProcessorsQueueDetailsArr as $key => $value) {
				if(!empty($value->QueueUID)) {

					$this->db->or_where("EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = ".$value->WorkflowModuleUID." AND tOrderQueues.QueueUID = ".$value->QueueUID." AND mQueues.Active = ".STATUS_ONE.")", NULL, FALSE);
				} elseif($value->IsKickBack == 1) {
					
					$this->db->or_where("EXISTS (SELECT 1 FROM tOrderWorkflows LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrderWorkflows.OrderUID AND tOrderAssignments.WorkflowModuleUID = ".$value->WorkflowModuleUID." WHERE tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = ".$value->WorkflowModuleUID." AND tOrderWorkflows.IsPresent = ".STATUS_ONE." AND tOrderWorkflows.IsReversed = ".STATUS_ONE." AND (tOrderAssignments.WorkflowStatus != ".$this->config->item('WorkflowStatus')['Completed']." OR tOrderAssignments.WorkflowStatus IS NULL))", NULL, FALSE);
				}
				
			}
			$this->db->group_end();	
		}	

		// Get Processors
		$ProcessorsArr = $this->Common_Model->get_allonshorejuniorprocessors();

		// Check processors
		if (!empty($ProcessorsArr)) {
			$like = "";
			foreach ($ProcessorsArr as $key => $item) {
				if ($key === 0) {
					// first loop
					$like .= "( tOrderImport.LoanProcessor LIKE '%" . $item->UserName . "%' ";
				} else {
					$like .= " OR tOrderImport.LoanProcessor LIKE '%" . $item->UserName . "%' ";
				}
			}
			$like .= ") ";
			$this->db->where($like, null, false);
		} else {
			$this->db->like('tOrderImport.LoanProcessor',$this->UserName);
		}
	}

	function get_priorityonshoreprocessorcounts($post)
	{

		$CustomerUID = NULL;
		$Except_JuniorPriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_JuniorPriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$JuniorPriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('JuniorPriorityReport_OnlyMilestones'), 'MilestoneUID'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allonshorejuniorprocessors();
		$Customer_Prioritys = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->JuniorProcessorPriority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		$Pipelineexceptmilestone = '';

		if(!empty($Except_JuniorPriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_JuniorPriorityReport_PipelineMilestones.'))';
		}

		if(!empty($Users) && !empty($Customer_Prioritys)) {

			$TotalWHERE = [];
			$PipelineTotalWhere = [];
			$PriorityMilestoneWhereArray = [];
			foreach ($Users as $UserKey => $UserValue) {
				$UserHeaderTotal = [];

				$CASEWHERE = "tOrderImport.LoanProcessor LIKE '%".$UserValue->UserName."%'";

				$PipelineANDWhere = $CASEWHERE;

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $PipelineANDWhere . $Pipelineexceptmilestone. " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$UserValue->UserUID,FALSE);

				$PipelineTotalWhere[$UserValue->UserUID] = $PipelineANDWhere;

				foreach ($Customer_Prioritys as $Priorityrow) {	
					$PriorityLoopWHERE = [];


					$WHERE  = [];

					if(!empty($CASEWHERE)) {
						$WHERE[] =  $CASEWHERE;
					}

					if(!empty($JuniorPriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$JuniorPriorityReport_OnlyMilestones.')';
					}

					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);

					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							//pending or completed
							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].")";

							} else {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID."  AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].")";
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


		$last_key = end(array_keys($Customer_PriorityWorkflows));

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

		// Junior Processor Report Conditions
		$this->JuniorProcessorReportConditions();

		return $this->db->get()->row();

	}

	function get_priorityonshorejuniorprocessorcounts($post)
	{
		$CustomerUID = NULL;
		$Except_JuniorPriorityReport_PipelineMilestones = implode(',', array_column($this->get_settingsmilestones('Except_JuniorPriorityReport_PipelineMilestones'), 'MilestoneUID'));
		$JuniorPriorityReport_OnlyMilestones = implode(',', array_column($this->get_settingsmilestones('JuniorPriorityReport_OnlyMilestones'), 'MilestoneUID'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allonshorejuniorprocessorslead();
		$Customer_Prioritys = $this->JuniorProcessorPriority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->JuniorProcessorPriority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		$Pipelineexceptmilestone = '';

		if(!empty($Except_JuniorPriorityReport_PipelineMilestones)) {
			$Pipelineexceptmilestone = ' AND (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID NOT IN ('.$Except_JuniorPriorityReport_PipelineMilestones.'))';
		}

		if(!empty($Users) && !empty($Customer_Prioritys)) {

			$PipelineTotalWhere = [];
			$TotalWHERE = [];
			$PriorityMilestoneWhereArray = [];
			foreach ($Users as $UserKey => $UserValue) {
				$UserHeaderTotal = [];
					//fetch groupusers for team leads groups
				$UsersinGroupsArray = $this->Common_Model->get_ProcessorsbyjuniorProcessor($UserValue->UserUID);
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

					if(!empty($JuniorPriorityReport_OnlyMilestones)) {
						$WHERE[]  = 'tOrders.MilestoneUID IN ('.$JuniorPriorityReport_OnlyMilestones.')';
					}


					$PriorityFields = $this->get_priorityreportfields($Priorityrow->PriorityUID);
					
					if(!empty($PriorityFields)) {

						foreach ($PriorityFields as $PriorityFieldrow) {

							if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND ( TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") AND (" . implode(" OR ", $PipelineANDWhere) . " )";

							} else {

								$WHERE[] = "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") AND (" . implode(" OR ", $PipelineANDWhere) . " )";
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

			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS OverallTotal ",FALSE);

		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
		$last_key = end(array_keys($Customer_PriorityWorkflows));

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

		// Junior Processor Report Conditions
		$this->JuniorProcessorReportConditions();
		
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
		$this->db->update('tOrderImport', array('OrderJuniorProcessorComments' => $Comments));
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
		$row = $this->db->where(array('Active'=>1,'SettingField'=>$SettingField))->get('mSettings')->row();
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



}
?>
