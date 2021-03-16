<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class AgingReportmodel extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Wednesday Thursday 12 March 2020 **/
	/** @description get customer workflows **/
	function getCustomer_Milestones($CustomerUID,$MilestoneUID='')
	{
		if (!empty($CustomerUID)) {
			$this->db->select('mWorkFlowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName,mMilestone.MilestoneName,mMilestone.MilestoneUID,mCustomerWorkflowModules.SLA,mCustomerWorkflowModules.IsParkingRequire');
			$this->db->from('mCustomerMilestones');
			$this->db->join('mCustomerWorkflowModules','mCustomerWorkflowModules.CustomerUID = mCustomerMilestones.CustomerUID AND mCustomerWorkflowModules.ProductUID = mCustomerMilestones.ProductUID AND mCustomerWorkflowModules.WorkflowModuleUID = mCustomerMilestones.WorkflowModuleUID','LEFT');
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

	function get_agingcounts($post)
	{
		$CustomerUID = NULL;
		//get customer workflows
		if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$CustomerUID = $this->parameters['DefaultClientUID'];
		}

		$AgingHeader = $this->config->item('AgingHeader');

		$Customerworkflows = $this->getCustomer_Milestones($CustomerUID,$post['MilestoneUID']);

		if(!empty($Customerworkflows)) {

			foreach ($Customerworkflows as $Customerworkflow) {

				$CASEWHERE = " AND TW_" .$Customerworkflow->SystemName.".WorkflowModuleUID = ".$Customerworkflow->WorkflowModuleUID." AND TDC_".$Customerworkflow->SystemName.".IsCleared IS NULL AND TP_".$Customerworkflow->SystemName.".IsCleared IS NULL AND (TOA_" . $Customerworkflow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $Customerworkflow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";

				foreach ($AgingHeader as $AgingHeaderkey => $AgingHeadervalue) {


					switch ($AgingHeaderkey) {
						case 'zerodays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 0  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'oneday':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 1  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'twodays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 2  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'threedays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 3  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'fourdays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 4  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'fivedays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 5  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'sixdays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 6  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'sevendays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 7  ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'eleventtofifteendays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 11 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 15 ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'sixteentotwentydays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 16 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 20 ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'twentyonetothirtydays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 21 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 30 ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'thirtyonetofortyfivedays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 31 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 45 ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'fortysixttosixtydays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 46 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 60 ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'sixtyonetoninetydays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) > 61 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 90 ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						case 'greaterthanninetydays':
						$this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 90 ".$CASEWHERE." THEN  TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS ".$AgingHeaderkey.$Customerworkflow->SystemName."");
						break;
						
						default:
						break;
					}

				}

				//TOTAL COUNT
				$this->db->select("GROUP_CONCAT(DISTINCT 
					CASE WHEN (
					(TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 0)
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 1) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 2) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 3) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 4) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 5) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 6) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) = 7) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 11 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 15) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 16 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 20) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 21 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 30) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 31 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 45) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 46 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 60) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 61 AND TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) <= 90) 
					OR (TIMESTAMPDIFF(DAY, (TW_" .$Customerworkflow->SystemName.".EntryDatetime),NOW()) >= 90) 
					)  ".$CASEWHERE."
					THEN TW_" .$Customerworkflow->SystemName.".OrderUID ELSE NULL END) AS total".$Customerworkflow->SystemName."");

			}
		}
		
		$this->db->select('"1" AS passed',false);
		$this->db->from('tOrders');

		foreach ($Customerworkflows as $Customerworkflow) {
			$this->db->join("tOrderWorkflows AS " .  "TW_" .$Customerworkflow->SystemName,   "TW_" .$Customerworkflow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$Customerworkflow->SystemName.".WorkflowModuleUID = '".$Customerworkflow->WorkflowModuleUID."'","LEFT");

			$this->db->join("tOrderAssignments AS " . "TOA_" . $Customerworkflow->SystemName,  "TOA_" . $Customerworkflow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $Customerworkflow->SystemName.".WorkflowModuleUID = '".$Customerworkflow->WorkflowModuleUID."'", "LEFT");

			$this->db->join("tOrderDocChase AS " . "TDC_" . $Customerworkflow->SystemName,  "TDC_" . $Customerworkflow->SystemName.".OrderUID = tOrders.OrderUID AND TDC_" . $Customerworkflow->SystemName.".WorkflowModuleUID = '".$Customerworkflow->WorkflowModuleUID."' AND  TDC_" . $Customerworkflow->SystemName.".IsCleared = 0", "LEFT");

			$this->db->join("tOrderParking AS " . "TP_" . $Customerworkflow->SystemName,  "TP_" . $Customerworkflow->SystemName.".OrderUID = tOrders.OrderUID AND TP_" . $Customerworkflow->SystemName.".WorkflowModuleUID = '".$Customerworkflow->WorkflowModuleUID."' AND  TP_" . $Customerworkflow->SystemName.".IsCleared = 0", "LEFT");

		}

		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

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
		$this->db->select("tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanType,tOrders.PropertyStateCode,mProjectCustomer.ProjectName,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID,tOrderWorkflows.WorkflowModuleUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');

		$this->db->from('tOrders');
		$this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID');

		$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $post['WorkflowModuleUID'] . '"');

		$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $post['WorkflowModuleUID'] . '"','left');

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
		$this->db->order_by('OrderEntryDatetime');
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




}
?>
