<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Pipeline_model extends MY_Model {

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
	function get_PipelineReport_Workflows($CustomerUID)
	{
		if (!empty($CustomerUID)) {
			$this->db->select('mWorkFlowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName');
			$this->db->from('mWorkFlowModules');
			$this->db->where_in('mWorkFlowModules.WorkflowModuleUID', $this->config->item('PriorityReport_Workflows'));
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

	function get_counts($post)
	{
		$CustomerUID = NULL;
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		$orderstatuscompleted[0] = $this->config->item('keywords')['ClosedandBilled'];
		$orderstatuscompleted[1] = $this->config->item('keywords')['ClosingCompleted'];

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}

		$MilestoneNames = $this->config->item('PriorityReport_Milestones');
		$Report_Workflows = $this->Pipeline_model->get_PipelineReport_Workflows($this->parameters['DefaultClientUID']);


		if(!empty($MilestoneNames)) {

			$TotalWHERE = [];
			$PipelineMilestoneWhereArray = [];
			$TotalPipelineWhereArray = [];

			foreach ($MilestoneNames as $Milestonekey => $MilestoneName) {

				// Total Pipeline
				$WHERE_TOTALPIPELINE = [];
				$TotalPipelineLoopWHERE = [];

				$WHERE_TOTALPIPELINE[] = "mMilestone.MilestoneName = '".$Milestonekey."' AND tOrders.StatusUID NOT IN( '" . implode( "', '" , $orderstatuscompleted ) . "' )";

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE_TOTALPIPELINE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Milestonekey.'totalpipeline');	

				$TotalPipelineLoopWHERE[] = implode(" AND ", $WHERE_TOTALPIPELINE);

				$TotalPipelineWhereArray[$Milestonekey] = implode(" OR ", $TotalPipelineLoopWHERE);			

				// $MilestoneTotalWHERE = [];
				foreach ($Report_Workflows as $Workflowrow) {	
					$PipelineLoopWHERE = [];

					$WHERE = [];
					$WHERE[]= "mMilestone.MilestoneName = '".$Milestonekey."'";

					$WHERE[] = "(SELECT tOrders.OrderUID FROM tOrderWorkflows t WHERE t.OrderUID = tOrders.OrderUID AND t.IsPresent = 1 AND t.WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."') AND (SELECT tOrders.OrderUID FROM tOrderAssignments ta WHERE ta.OrderUID = tOrders.OrderUID AND ta.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' AND ta.WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." LIMIT 1)";

					if (!empty($WHERE)) {

						$PipelineLoopWHERE[] = implode(" AND ", $WHERE);
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Milestonekey.$Workflowrow->SystemName);

					} else {

						$this->db->select("0 AS ".$Milestonekey.$Workflowrow->SystemName,NULL,FALSE);
					}

					$PipelineMilestoneWhereArray[$Milestonekey][$Workflowrow->WorkflowModuleUID] = implode(" OR ", $PipelineLoopWHERE);


				}

			}

			//Total Pipeline total
			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalPipelineWhereArray) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS TotalPipelineTotal");


			//Priority Total
			if(!empty($PipelineMilestoneWhereArray) && !empty($Report_Workflows)) {
				foreach ($Report_Workflows as $Workflowrow) {
					$PipelineWhere = [];
					foreach ($MilestoneNames as $Milestonekey => $MilestoneName) {
						$PipelineWhere[] = array_key_exists($Workflowrow->WorkflowModuleUID, $PipelineMilestoneWhereArray[$Milestonekey]) ? $PipelineMilestoneWhereArray[$Milestonekey][$Workflowrow->WorkflowModuleUID] : [];

					}
					if(!empty($PipelineWhere)) {
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName."Total");
					}
				}
			}

		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$this->db->join('mMilestone','mMilestone.MilestoneUID = tOrders.MilestoneUID');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID");

		$this->db->join("tOrderAssignments", "tOrderAssignments.OrderUID = tOrders.OrderUID", "LEFT");

		$this->db->where_not_in('tOrders.StatusUID', $status);

		//Order Queue Permission
		$this->Common_Model->reportOrderPermission('tOrderAssignments.AssignedToUserUID','PIPELINECOUNT');

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

		$result = $this->db->get()->row();
		return $result;
		// echo "<pre>";
		// print_r($result);
		// exit();

	}

	//Order fetch start

	function fetchorder_query($post,$global='')
	{
		$this->db->select("tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanType,tOrders.PropertyStateCode,mProjectCustomer.ProjectName,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName,tOrderImport.Aging,tOrderImport.NextPaymentDue");
		$this->db->select('tOrders.LastModifiedDateTime');


		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
		$this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID');

		/* if(!empty($post['WorkflowModuleUID'])) {
			$this->db->select('tOrderWorkflows.WorkflowModuleUID,tOrderWorkflows.EntryDateTime,tOrderWorkflows.DueDateTime,tOrderAssignments.AssignedToUserUID');
			$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $post['WorkflowModuleUID'] . '"');
			$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $post['WorkflowModuleUID'] . '"','left');
		} else {
			$this->db->select("'' AS WorkflowModuleUID,'' AS EntryDateTime,'' AS DueDateTime,'' AS AssignedToUserUID" ,NULL,FALSE);
		} */

		$this->db->select('tOrderWorkflows.EntryDateTime,tOrderWorkflows.DueDateTime,tOrderAssignments.AssignedToUserUID');
		$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID');
		$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID','left');

		$this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');		

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
		$this->db->order_by('OrderEntryDatetime','DESC');
	}

	function count_all($post)
	{
		$this->fetchorder_query($post);
		$query = $this->db->count_all_results();
		return $query;
	}

	function count_filtered($post)
	{
		$this->fetchorder_query($post);
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		/*$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $post['WorkflowModuleUID']);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			//$this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}*/
		$this->db->order_by('OrderEntryDatetime');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function getOrders($post)
	{
		$this->fetchorder_query($post);
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		if (isset($post['length']) && $post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		/*$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $post['WorkflowModuleUID']);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			$this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}*/
		$this->db->order_by('OrderEntryDatetime');
		$query = $this->db->get();
		return $query->result();
	}

	function get_pipelineagingcounts($post)
	{
		$CustomerUID = NULL;
		$Milestones = array_keys($this->config->item('PriorityReport_Milestones'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		$orderstatuscompleted[0] = $this->config->item('keywords')['ClosedandBilled'];
		$orderstatuscompleted[1] = $this->config->item('keywords')['ClosingCompleted'];

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}

		$AgingHeader = $this->config->item('PriorityAgingBucketHeader');
		$Report_Workflows = $this->Pipeline_model->get_PipelineReport_Workflows($this->parameters['DefaultClientUID']);


		if(!empty($AgingHeader) && !empty($Report_Workflows)) {

			$TotalWHERE = [];
			$PipelineMilestoneWhereArray = [];

			foreach ($AgingHeader as $AgingHeaderkey => $AgingHeadervalue) {

				// Total Pipeline
				$WHERE_TOTALPIPELINE = [];
				$TotalPipelineLoopWHERE = [];

				$WHERE_TOTALPIPELINE[] = "tOrders.StatusUID NOT IN( '" . implode( "', '" , $orderstatuscompleted ) . "' )";

				switch ($AgingHeaderkey) {
					case 'zerotothirtydays':
					$WHERE_TOTALPIPELINE[] = "tOrderImport.Aging >= 0 AND tOrderImport.Aging <= 30 ";
					break;
					case 'thirtyonetofortyfivedays':
					$WHERE_TOTALPIPELINE[] = "tOrderImport.Aging >= 31 AND tOrderImport.Aging <= 45 ";
					break;
					case 'fortysixttosixtydays':
					$WHERE_TOTALPIPELINE[] = "tOrderImport.Aging >= 46 AND tOrderImport.Aging <= 60 ";
					break;
					case 'sixtyonetoninetydays':
					$WHERE_TOTALPIPELINE[] = "tOrderImport.Aging > 61 AND tOrderImport.Aging <= 90 ";
					break;
					case 'greaterthanninetydays':
					$WHERE_TOTALPIPELINE[] = "tOrderImport.Aging >= 90 ";
					break;

					default:
					break;
				}

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE_TOTALPIPELINE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$AgingHeaderkey.'totalpipeline');

				$TotalPipelineLoopWHERE[] = implode(" AND ", $WHERE_TOTALPIPELINE);	

				$TotalPipelineWhereArray[$AgingHeaderkey] = implode(" OR ", $TotalPipelineLoopWHERE);

				// Total Pipeline

				foreach ($Report_Workflows as $Workflowrow) {	
					$PipelineLoopWHERE = [];

					$WHERE = [];

					switch ($AgingHeaderkey) {
						case 'zerotothirtydays':
						$WHERE[] = "tOrderImport.Aging >= 0 AND tOrderImport.Aging <= 30 ";
						break;
						case 'thirtyonetofortyfivedays':
						$WHERE[] = "tOrderImport.Aging >= 31 AND tOrderImport.Aging <= 45 ";
						break;
						case 'fortysixttosixtydays':
						$WHERE[] = "tOrderImport.Aging >= 46 AND tOrderImport.Aging <= 60 ";
						break;
						case 'sixtyonetoninetydays':
						$WHERE[] = "tOrderImport.Aging > 61 AND tOrderImport.Aging <= 90 ";
						break;
						case 'greaterthanninetydays':
						$WHERE[] = "tOrderImport.Aging >= 90 ";
						break;

						default:
						break;
					}

					$WHERE[] = "(SELECT tOrders.OrderUID FROM tOrderWorkflows t WHERE t.OrderUID = tOrders.OrderUID AND t.IsPresent = 1 AND t.WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."') AND (SELECT tOrders.OrderUID FROM tOrderAssignments ta WHERE ta.OrderUID = tOrders.OrderUID AND ta.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' AND ta.WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." LIMIT 1)";

					if (!empty($WHERE)) {

						$PipelineLoopWHERE[] = implode(" AND ", $WHERE);
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$AgingHeaderkey.$Workflowrow->SystemName);

					} else {

						$this->db->select("0 AS ".$AgingHeaderkey.$Workflowrow->SystemName,NULL,FALSE);
					}

					$PipelineMilestoneWhereArray[$AgingHeaderkey][$Workflowrow->WorkflowModuleUID] = implode(" OR ", $PipelineLoopWHERE);


				}

			}

			//Total Pipeline total
			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalPipelineWhereArray) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS TotalPipelineTotal");

			//Priority Total
			if(!empty($PipelineMilestoneWhereArray) && !empty($Report_Workflows)) {
				foreach ($Report_Workflows as $Workflowrow) {
					$PipelineWhere = [];
					foreach ($AgingHeader as $AgingHeaderkey => $AgingHeadervalue) {
						$PipelineWhere[] = array_key_exists($Workflowrow->WorkflowModuleUID, $PipelineMilestoneWhereArray[$AgingHeaderkey]) ? $PipelineMilestoneWhereArray[$AgingHeaderkey][$Workflowrow->WorkflowModuleUID] : [];

					}
					if(!empty($PipelineWhere)) {
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName."Total");
					}
				}
			}

		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrderImport');
		$this->db->join('tOrders','tOrders.OrderUID = tOrderImport.OrderUID','LEFT');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID");

		$this->db->join("tOrderAssignments", "tOrderAssignments.OrderUID = tOrders.OrderUID", "LEFT");

		//Order Queue Permission
		$this->Common_Model->reportOrderPermission('tOrderAssignments','PIPELINECOUNT');

		$this->db->where_not_in('tOrders.StatusUID', $status);

		$this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','LEFT');

		$this->db->where_in('mMilestone.MilestoneName', $Milestones);

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

		$result = $this->db->get()->row();
		return $result;
		// echo "<pre>";
		// print_r($result);
		// exit();

	}

	function get_priorityprocessorcounts($post)
	{
		$CustomerUID = NULL;
		$Milestones = array_keys($this->config->item('PriorityReport_Milestones'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allprocessors();
		$Report_Workflows = $this->Pipeline_model->get_PipelineReport_Workflows($this->parameters['DefaultClientUID']);
		if(!empty($Users) && !empty($Report_Workflows)) {

			$PipelineTotalWhere = [];
			$WorkflowTotal = [];
			foreach ($Users as $UserKey => $UserValue) {

				$PipelineANDWhere = "tOrderAssignments.AssignedToUserUID = ".$UserValue->UserUID;

				$PipelineWhere = "(SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments WHERE (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") AND ".$PipelineANDWhere." )";

				$this->db->select($PipelineWhere ." AS Pending".$UserValue->UserUID,FALSE);

				$PipelineTotalWhere[$UserValue->UserUID] = $PipelineANDWhere;

				foreach ($Report_Workflows as $Workflowrow) {	

						$CASECONDITION = "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." AND TOA_" . $Workflowrow->SystemName.".AssignedToUserUID = ".$UserValue->UserUID." AND ( TOA_" . $Workflowrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";
						//$CASECONDITION = "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." AND TDC_".$Workflowrow->SystemName.".IsCleared IS NULL AND TP_".$Workflowrow->SystemName.".IsCleared IS NULL AND TOA_" . $Workflowrow->SystemName.".AssignedToUserUID = ".$UserValue->UserUID." AND ( TOA_" . $Workflowrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";


					$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $CASECONDITION . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName.$UserValue->UserUID);

					$WorkflowTotal[$Workflowrow->WorkflowModuleUID][] = $CASECONDITION;

				}

			}	


			//Pending Total
			if(!empty($PipelineTotalWhere)) {

				$PipelineWhere = "SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments WHERE (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") AND (".implode(' OR ', $PipelineTotalWhere).")";

				$this->db->select('('.$PipelineWhere.') AS PendingTotal',FALSE);

			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}
			//Workflow Total
			if(!empty($WorkflowTotal) && !empty($Report_Workflows)) {
				foreach ($Report_Workflows as $Workflowrow) {	
					$TotalWorkflowWhere = array_key_exists($Workflowrow->WorkflowModuleUID, $WorkflowTotal) ? $WorkflowTotal[$Workflowrow->WorkflowModuleUID] : [];

					if(!empty($TotalWorkflowWhere)) {

						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWorkflowWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName."Total");
						
					} else {
						$this->db->select('"" AS '.$Workflowrow->SystemName.'Total',FALSE);

					}
				}
			}
		}

		$this->db->select('"1" AS passed',FALSE);
		$this->db->from('tOrders');


		foreach ($Report_Workflows as $Workflowrow) {	
			$this->db->join("tOrderWorkflows AS " .  "TW_" .$Workflowrow->SystemName,   "TW_" .$Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'","LEFT");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $Workflowrow->SystemName,  "TOA_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'", "LEFT");

			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $Workflowrow->SystemName,  "TDC_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."' AND  TDC_" . $Workflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $Workflowrow->SystemName,  "TP_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."' AND  TP_" . $Workflowrow->SystemName.".IsCleared = 0", "LEFT");*/
		}


		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);


		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);*/

		//Order Queue Permission
		$this->Common_Model->reportOrderPermission('tOrderAssignments','PIPELINECOUNT');

		$this->db->where_not_in('tOrders.StatusUID', $status);

		$this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','LEFT');

		$this->db->where_in('mMilestone.MilestoneName', $Milestones);

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
		$Milestones = array_keys($this->config->item('PriorityReport_Milestones'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allteamleads();
		$Report_Workflows = $this->Pipeline_model->get_PipelineReport_Workflows($this->parameters['DefaultClientUID']);


		if(!empty($Users) && !empty($Report_Workflows)) {

			$PipelineTotalWhere = [];
			$WorkflowTotal = [];
			foreach ($Users as $UserKey => $UserValue) {

								//fetch groupusers for team leads groups
				$UsersinGroupsArray = $this->Common_Model->get_groupusersbyteamleads($UserValue->UserUID);
				$AssignedToUserUIDs = array_column($UsersinGroupsArray, 'UserUID');
				$AssignedToUserUID = implode(',', $AssignedToUserUIDs);
				if(empty($AssignedToUserUID)) {
					$AssignedToUserUID = 0;
				}

				$PipelineANDWhere = "tOrderAssignments.AssignedToUserUID IN  (".$AssignedToUserUID.") ";

				$PipelineWhere = "(SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments WHERE (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") AND ".$PipelineANDWhere." )";


				$this->db->select($PipelineWhere ." AS Pending".$UserValue->UserUID,FALSE);

				$PipelineTotalWhere[$UserValue->UserUID] = $PipelineANDWhere;

				foreach ($Report_Workflows as $Workflowrow) {	

						$CASECONDITION = "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." AND TOA_" . $Workflowrow->SystemName.".AssignedToUserUID IN (".$AssignedToUserUID.") AND ( TOA_" . $Workflowrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";
						//$CASECONDITION = "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." AND TDC_".$Workflowrow->SystemName.".IsCleared IS NULL AND TP_".$Workflowrow->SystemName.".IsCleared IS NULL AND TOA_" . $Workflowrow->SystemName.".AssignedToUserUID IN (".$AssignedToUserUID.") AND ( TOA_" . $Workflowrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") ";


					$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $CASECONDITION . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName.$UserValue->UserUID);

					$WorkflowTotal[$Workflowrow->WorkflowModuleUID][] = $CASECONDITION;

				}

			}	


			//Pending Total
			if(!empty($PipelineTotalWhere)) {
				
				$PipelineWhere = "SELECT GROUP_CONCAT(DISTINCT tOrderAssignments.OrderUID) FROM tOrderAssignments WHERE (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") AND (".implode(' OR ', $PipelineTotalWhere).")";
				$this->db->select('('.$PipelineWhere.') AS PendingTotal',FALSE);


			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}
			//Workflow Total
			if(!empty($WorkflowTotal) && !empty($Report_Workflows)) {
				foreach ($Report_Workflows as $Workflowrow) {	
					$TotalWorkflowWhere = array_key_exists($Workflowrow->WorkflowModuleUID, $WorkflowTotal) ? $WorkflowTotal[$Workflowrow->WorkflowModuleUID] : [];

					if(!empty($TotalWorkflowWhere)) {

						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWorkflowWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName."Total");
						
					} else {
						$this->db->select('"" AS '.$Workflowrow->SystemName.'Total',FALSE);

					}
				}
			}
		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$last_key = end(array_keys($Report_Workflows));
		foreach ($Report_Workflows as $Workflowrowkey => $Workflowrow) {	
			$this->db->join("tOrderWorkflows AS " .  "TW_" .$Workflowrow->SystemName,   "TW_" .$Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'","LEFT");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $Workflowrow->SystemName,  "TOA_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $Workflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$Workflowrowkey,$last_key);		

			/*$this->db->join("tOrderDocChase AS " . "TDC_" . $Workflowrow->SystemName,  "TDC_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."' AND  TDC_" . $Workflowrow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $Workflowrow->SystemName,  "TP_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."' AND  TP_" . $Workflowrow->SystemName.".IsCleared = 0", "LEFT");*/
		}



		/*//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);


		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);*/


		$this->db->where_not_in('tOrders.StatusUID', $status);

		$this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','LEFT');

		$this->db->where_in('mMilestone.MilestoneName', $Milestones);
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

	function get_pipelineloantypecounts($post)
	{
		$CustomerUID = NULL;
		$Milestones = array_keys($this->config->item('PriorityReport_Milestones'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		$orderstatuscompleted[0] = $this->config->item('keywords')['ClosedandBilled'];
		$orderstatuscompleted[1] = $this->config->item('keywords')['ClosingCompleted'];

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}

		$LoanTypes = $this->config->item('LoanTypes');
		$Report_Workflows = $this->Pipeline_model->get_PipelineReport_Workflows($this->parameters['DefaultClientUID']);


		if(!empty($LoanTypes) && !empty($Report_Workflows)) {

			$TotalWHERE = [];
			$PipelineMilestoneWhereArray = [];
			$TotalPipelineWhereArray = [];

			foreach ($LoanTypes as $LoanTypeKey => $LoanType) {

				// Total Pipeline
				$WHERE_TOTALPIPELINE = [];
				$TotalPipelineLoopWHERE = [];

				$WHERE_TOTALPIPELINE[] = "tOrders.LoanType = '".$LoanType."' AND tOrders.StatusUID NOT IN( '" . implode( "', '" , $orderstatuscompleted ) . "' )";

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE_TOTALPIPELINE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$LoanTypeKey.'totalpipeline');	

				$TotalPipelineLoopWHERE[] = implode(" AND ", $WHERE_TOTALPIPELINE);

				$TotalPipelineWhereArray[$LoanTypeKey] = implode(" OR ", $TotalPipelineLoopWHERE);			

				// $MilestoneTotalWHERE = [];
				foreach ($Report_Workflows as $Workflowrow) {	
					$PipelineLoopWHERE = [];

					$WHERE = [];
					$WHERE[]= "tOrders.LoanType = '".$LoanType."'";

					$WHERE[] = "(SELECT tOrders.OrderUID FROM tOrderWorkflows t WHERE t.OrderUID = tOrders.OrderUID AND t.IsPresent = 1 AND t.WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."' LIMIT 1) AND (SELECT tOrders.OrderUID FROM tOrderAssignments ta WHERE ta.OrderUID = tOrders.OrderUID AND ta.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' AND ta.WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." LIMIT 1)";

					if (!empty($WHERE)) {

						$PipelineLoopWHERE[] = implode(" AND ", $WHERE);
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$LoanTypeKey.$Workflowrow->SystemName);

					} else {

						$this->db->select("0 AS ".$LoanTypeKey.$Workflowrow->SystemName,NULL,FALSE);
					}

					$PipelineMilestoneWhereArray[$LoanTypeKey][$Workflowrow->WorkflowModuleUID] = implode(" OR ", $PipelineLoopWHERE);


				}

			}

			//Total Pipeline total
			$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalPipelineWhereArray) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS TotalPipelineTotal");


			//Priority Total
			if(!empty($PipelineMilestoneWhereArray) && !empty($Report_Workflows)) {
				foreach ($Report_Workflows as $Workflowrow) {
					$PipelineWhere = [];
					foreach ($LoanTypes as $LoanTypeKey => $LoanType) {
						$PipelineWhere[] = array_key_exists($Workflowrow->WorkflowModuleUID, $PipelineMilestoneWhereArray[$LoanTypeKey]) ? $PipelineMilestoneWhereArray[$LoanTypeKey][$Workflowrow->WorkflowModuleUID] : [];

					}
					if(!empty($PipelineWhere)) {
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName."Total");
					}
				}
			}

		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID");

		$this->db->join("tOrderAssignments", "tOrderAssignments.OrderUID = tOrders.OrderUID", "LEFT");

		//Order Queue Permission
		$this->Common_Model->reportOrderPermission('tOrderAssignments.AssignedToUserUID','PIPELINECOUNT');

		$this->db->where_not_in('tOrders.StatusUID', $status);

		$this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','LEFT');

		$this->db->where_in('mMilestone.MilestoneName', $Milestones);

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

		$result = $this->db->get()->row();
		return $result;
		// echo "<pre>";
		// print_r($result);
		// exit();

	}

	/**
	*Function fetch onshore counts
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 19 May 2020
	*/
	function get_priorityonshoreprocessorcounts($post)
	{
		$CustomerUID = NULL;
		$Milestones = array_keys($this->config->item('PriorityReport_Milestones'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allonshoreprocessors();
		$Report_Workflows = $this->Pipeline_model->get_PipelineReport_Workflows($this->parameters['DefaultClientUID']);
		if(!empty($Users) && !empty($Report_Workflows)) {

			$PipelineTotalWhere = [];
			$WorkflowTotal = [];
			foreach ($Users as $UserKey => $UserValue) {

				$PipelineANDWhere = "tOrderImport.LoanProcessor LIKE '%".$this->db->escape_str($UserValue->UserName,TRUE)."%'";

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $PipelineANDWhere . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$UserValue->UserUID,FALSE);

				$PipelineTotalWhere[$UserValue->UserUID] = $PipelineANDWhere;

				foreach ($Report_Workflows as $Workflowrow) {	

						$CASECONDITION = "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." AND ( TOA_" . $Workflowrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") AND ".$PipelineANDWhere;

					$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $CASECONDITION . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName.$UserValue->UserUID,FALSE);

					$WorkflowTotal[$Workflowrow->WorkflowModuleUID][] = $CASECONDITION;

				}

			}	


			//Pending Total
			if(!empty($PipelineTotalWhere)) {

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineTotalWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS PendingTotal",FALSE);

			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}
			//Workflow Total
			if(!empty($WorkflowTotal) && !empty($Report_Workflows)) {
				foreach ($Report_Workflows as $Workflowrow) {	
					$TotalWorkflowWhere = array_key_exists($Workflowrow->WorkflowModuleUID, $WorkflowTotal) ? $WorkflowTotal[$Workflowrow->WorkflowModuleUID] : [];

					if(!empty($TotalWorkflowWhere)) {

						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWorkflowWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName."Total",FALSE);
						
					} else {
						$this->db->select('"" AS '.$Workflowrow->SystemName.'Total',FALSE);

					}
				}
			}
		}

		$this->db->select('"1" AS passed',FALSE);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$last_key = end(array_keys($Report_Workflows));
		foreach ($Report_Workflows as $Workflowrowkey => $Workflowrow) {	

			$this->db->join("tOrderWorkflows AS " .  "TW_" .$Workflowrow->SystemName,   "TW_" .$Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'","LEFT");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $Workflowrow->SystemName,  "TOA_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $Workflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$Workflowrowkey,$last_key);		
		}

		$this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','LEFT');

		$this->db->where_in('mMilestone.MilestoneName', $Milestones);

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
		$Milestones = array_keys($this->config->item('PriorityReport_Milestones'));
		$status[] = $this->config->item('keywords')['Cancelled'];
		$otherdb = $this->load->database('otherdb', TRUE);

		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}


		$Users = $this->Common_Model->get_allonshoreteamleads();
		$Report_Workflows = $this->Pipeline_model->get_PipelineReport_Workflows($this->parameters['DefaultClientUID']);


		if(!empty($Users) && !empty($Report_Workflows)) {

			$PipelineTotalWhere = [];
			$WorkflowTotal = [];
			foreach ($Users as $UserKey => $UserValue) {

				//fetch groupusers for team leads groups
				$UsersinGroupsArray = $this->Common_Model->get_groupusersbyteamleads($UserValue->UserUID);
				$PipelineANDWhere = [];
				$PipelineANDWhere[] = 'tOrderImport.LoanProcessor LIKE "%'.$this->db->escape_str($UserValue->UserName,TRUE).'%"';
				foreach($UsersinGroupsArray as $GroupUser){

					$PipelineANDWhere[] = 'tOrderImport.LoanProcessor LIKE "%'.$this->db->escape_str($GroupUser->UserName,TRUE).'%"';

				}

				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineANDWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS Pending".$UserValue->UserUID,FALSE);

				$PipelineTotalWhere[$UserValue->UserUID] = $PipelineANDWhere;

				foreach ($Report_Workflows as $Workflowrow) {	

						$CASECONDITION = "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = ".$Workflowrow->WorkflowModuleUID." AND ( TOA_" . $Workflowrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") AND (" . implode(" OR ", $PipelineANDWhere) . " )";

					$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . $CASECONDITION . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName.$UserValue->UserUID,FALSE);

					$WorkflowTotal[$Workflowrow->WorkflowModuleUID][] = $CASECONDITION;

				}

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
				
				$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $PipelineTotalWherearray) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS PendingTotal",FALSE);


			} else {
				$this->db->select('"" AS PendingTotal',FALSE);

			}
			//Workflow Total
			if(!empty($WorkflowTotal) && !empty($Report_Workflows)) {
				foreach ($Report_Workflows as $Workflowrow) {	
					$TotalWorkflowWhere = array_key_exists($Workflowrow->WorkflowModuleUID, $WorkflowTotal) ? $WorkflowTotal[$Workflowrow->WorkflowModuleUID] : [];

					if(!empty($TotalWorkflowWhere)) {

						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN (" . implode(" OR ", $TotalWorkflowWhere) . " ) THEN tOrders.OrderUID ELSE NULL END ) AS ".$Workflowrow->SystemName."Total",FALSE);
						
					} else {
						$this->db->select('"" AS '.$Workflowrow->SystemName.'Total',FALSE);

					}
				}
			}
		}

		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

		$last_key = end(array_keys($Report_Workflows));

		foreach ($Report_Workflows as $Workflowrowkey => $Workflowrow) {	
			$this->db->join("tOrderWorkflows AS " .  "TW_" .$Workflowrow->SystemName,   "TW_" .$Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'","LEFT");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $Workflowrow->SystemName,  "TOA_" . $Workflowrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $Workflowrow->SystemName.".WorkflowModuleUID = '".$Workflowrow->WorkflowModuleUID."'", "LEFT");

			//Order Queue Permission
			$this->Common_Model->reportOrderPermission("TOA_" . $Workflowrow->SystemName.".AssignedToUserUID",'PRIORITYCOUNT',$Workflowrowkey,$last_key);		
		}

		$this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','LEFT');

		$this->db->where_in('mMilestone.MilestoneName', $Milestones);

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

}
?>
