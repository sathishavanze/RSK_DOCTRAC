<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ExceptionQueue_Orders_model extends MY_Model {

	function __construct()
	{
		parent::__construct();
    $this->load->library('session');
  }


  function advanced_search($post)
  {
    if($post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All'){
      $this->db->where('tOrders.CustomerUID',$post['advancedsearch']['CustomerUID']);
    }
    if($post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All'){
      $this->db->where('tOrders.ProjectUID',$post['advancedsearch']['ProjectUID']);
    }
    if(isset($post['advancedsearch']['WorkflowModuleUID'])){
      if($post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All')
      {
        if($post['advancedsearch']['WorkflowModuleUID'] == $this->config->item('Workflows')['DocChase'])
        {
          if($post['advancedsearch']['Status'] != '' && $post['advancedsearch']['Status'] != 'All')
          {
            if($post['advancedsearch']['Status'] == 'NA')
            {
              $this->db->where('tOrders.OrderUID NOT IN(select distinct tOrderDocChase.OrderUID from tOrderDocChase )');
            }
            else if($post['advancedsearch']['Status'] == 'Completed')
            {
             $this->db->where('tOrders.OrderUID in(select distinct tOrderDocChase.OrderUID from tOrderDocChase where tOrderDocChase.IsCleared = 1)');
           } 
           else if($post['advancedsearch']['Status'] == 'ProblemIdentified')
           {
            $this->db->where('tOrders.OrderUID in (SELECT tDocumentCheckList.OrderUID FROM `tDocumentCheckList` WHERE `WorkflowUID` = '.$post['advancedsearch']['WorkflowModuleUID'].' AND `Answer` = "Problem Identified" AND (`tDocumentCheckList`.`DocumentTypeName` IS NOT NULL or `tDocumentCheckList`.`DocumentTypeUID` in (select DocumentTypeUID from mDocumentType where Active = 1)))');
          }
          else if($post['advancedsearch']['Status'] == 'Pending')
          {
            $this->db->where('tOrders.OrderUID in(select tOrderDocChase.OrderUID from tOrderDocChase where IsCleared = 0)');
          }
          else if($post['advancedsearch']['Status'] == 'NotReady')
          {
            $this->db->where('tOrders.OrderUID NOT IN(select distinct tOrderDocChase.OrderUID from tOrderDocChase )');
          }
        }
      }
      else
      {
        if($post['advancedsearch']['Status'] != '' && $post['advancedsearch']['Status'] != 'All')
        { 
         if($post['advancedsearch']['Status'] == 'NA')
         {
          $this->db->where('tOrders.OrderUID in(select tOrderWorkflows.OrderUID from tOrderWorkflows  where tOrderWorkflows.WorkflowModuleUID = '.$post['advancedsearch']['WorkflowModuleUID'].' and tOrderWorkflows.IsPresent = 0)');
        }
        else if($post['advancedsearch']['Status'] == 'Completed')
        {
         $this->db->where('tOrders.OrderUID in (select tOrderAssignments.OrderUID from tOrderAssignments where WorkflowModuleUID = '.$post['advancedsearch']['WorkflowModuleUID'].' and WorkflowStatus = 5)');
       } 
       else if($post['advancedsearch']['Status'] == 'ProblemIdentified')
       {
        $this->db->where('tOrders.OrderUID in (SELECT tDocumentCheckList.OrderUID FROM `tDocumentCheckList` WHERE `WorkflowUID` = '.$post['advancedsearch']['WorkflowModuleUID'].' AND `Answer` = "Problem Identified" AND (`tDocumentCheckList`.`DocumentTypeName` IS NOT NULL or `tDocumentCheckList`.`DocumentTypeUID` in (select DocumentTypeUID from mDocumentType where Active = 1)))');
      }
      else if($post['advancedsearch']['Status'] == 'Pending')
      {
        $filterOrders = $this->getPendingFilterOrders($post['advancedsearch']['WorkflowModuleUID']);
        $this->db->where('tOrders.OrderUID in ('.$filterOrders.') and tOrders.OrderUID NOT in(select tOrderAssignments.OrderUID from tOrderAssignments where WorkflowModuleUID = '.$post['advancedsearch']['WorkflowModuleUID'].' and WorkflowStatus = 5)');
        $this->db->where('tOrders.OrderUID NOT in (SELECT tDocumentCheckList.OrderUID FROM `tDocumentCheckList` WHERE `WorkflowUID` = '.$post['advancedsearch']['WorkflowModuleUID'].' AND `Answer` = "Problem Identified" AND (`tDocumentCheckList`.`DocumentTypeName` IS NOT NULL or `tDocumentCheckList`.`DocumentTypeUID` in (select DocumentTypeUID from mDocumentType where Active = 1)))');
      }
      else if($post['advancedsearch']['Status'] == 'NotReady')
      {
        $filterOrders = $this->getPendingFilterOrders($post['advancedsearch']['WorkflowModuleUID']);
        $this->db->where('tOrders.OrderUID in (SELECT tOrderWorkflows.OrderUID FROM `tOrderWorkflows` WHERE tOrderWorkflows.WorkflowModuleUID = '.$post['advancedsearch']['WorkflowModuleUID'].' and tOrderWorkflows.IsPresent = 1) and tOrders.OrderUID NOT IN('.$filterOrders.')');
        $this->db->where('tOrders.OrderUID NOT in (SELECT tDocumentCheckList.OrderUID FROM `tDocumentCheckList` WHERE `WorkflowUID` = '.$post['advancedsearch']['WorkflowModuleUID'].' AND `Answer` = "Problem Identified" AND (`tDocumentCheckList`.`DocumentTypeName` IS NOT NULL or `tDocumentCheckList`.`DocumentTypeUID` in (select DocumentTypeUID from mDocumentType where Active = 1)))');
        $this->db->where('tOrders.OrderUID NOT in (select tOrderAssignments.OrderUID from tOrderAssignments where WorkflowModuleUID = '.$post['advancedsearch']['WorkflowModuleUID'].' and WorkflowStatus = 5)');
      }
    }
  }
}
}
if($post['advancedsearch']['FromDate']){
  $this->db->where('DATE(`tOrders`.`OrderEntryDateTime` ) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
}
if($post['advancedsearch']['ToDate']){
  $this->db->where('DATE(`tOrders`.`OrderEntryDateTime` ) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
}
return true;
}




  /**
    *@description Function to fetch Fee Variance Data
    *
    * @param $Post (Array)
    * 
    * @throws no exception
    * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    * @return Mixed(Array) 
    * @since 27.2.2020 
    * @version Open Action Report 
    *
  */ 
  function _getExeceptionQueueOrders($post, $returntype='data'){
    /*Query*/
    if (empty($post['QueueUID'])) {
      if ($returntype == 'data') {
        return [];
      }
      else{
       return 0; 
      }
    }


    $WorkflowModuleFunctions = [
      $this->config->item("Workflows")["PreScreen"] => "GetPreScreenOrders",
      $this->config->item("Workflows")["TitleTeam"] => "GetTitleTeamQueue",
      $this->config->item("Workflows")["FHAVACaseTeam"] => "GetFHAVACaseTeamQueue",
      $this->config->item("Workflows")["HOI"] => "GetHOIQueue",
      $this->config->item("Workflows")["ThirdPartyTeam"] => "GetThirdPartyQueue",
      $this->config->item("Workflows")["Workup"] => "GetWorkUpQueue",
      $this->config->item("Workflows")["BorrowerDoc"] => "GetBorrowerDocQueue",
      $this->config->item("Workflows")["ICD"] => "GetICDOrders",
      $this->config->item("Workflows")["Disclosures"] => "GetDisclosuresOrders",
      $this->config->item("Workflows")["NTB"] => "GetNTBOrders",
      $this->config->item("Workflows")["FloodCert"] => "GetFloodCertOrders",
      $this->config->item("Workflows")["Appraisal"] => "GetAppraisalOrders",
      $this->config->item("Workflows")["Escrows"] => "GetEscrowsOrders",
      $this->config->item("Workflows")["TwelveDayLetter"] => "GetTwelveDayLetterOrders",
      $this->config->item("Workflows")["MaxLoan"] => "GetMaxLoanOrders",
      $this->config->item("Workflows")["POO"] => "GetPOOOrders",
      $this->config->item("Workflows")["CondoQR"] => "GetCondoQROrders",
      $this->config->item("Workflows")["FHACaseAssignment"] => "GetFHACaseAssignmentOrders",
      $this->config->item("Workflows")["VACaseAssignment"] => "GetVACaseAssignmentOrders",
      $this->config->item("Workflows")["VVOE"] => "GetVVOEOrders",
      $this->config->item("Workflows")["CEMA"] => "GetCEMAOrders",
      $this->config->item("Workflows")["SCAP"] => "GetSCAPOrders",
      $this->config->item("Workflows")["NLR"] => "GetNLROrders",
      $this->config->item("Workflows")["CTCFlipQC"] => "GetCTCFlipQCOrders",
      $this->config->item("Workflows")["PrefundAuditCorrection"] => "GetPrefundAuditCorrectionOrders",
      $this->config->item("Workflows")["AdhocTasks"] => "GetAdhocTasksOrders",
      $this->config->item("Workflows")["UWClear"] => "GetUWClearOrders",
      $this->config->item("Workflows")["TitleReview"] => "GetTitleReviewOrders",
      $this->config->item("Workflows")["WelcomeCall"] => "GetWelcomeCallQueue",
      $this->config->item("Workflows")["PayOff"] => "GetPayOffQueue",
      $this->config->item("Workflows")["BorrowerDocs"] => "GetBorrowerDocsOrders",
      $this->config->item("Workflows")["GateKeeping"] => "GetGateKeepingOrders",
      $this->config->item("Workflows")["Submissions"] => "GetSubmissionsOrders",
      $this->config->item("Workflows")["CD"] => "GetCDOrders",
      $this->config->item("Workflows")["Scheduling"] => "GetSchedulingQueue",
      $this->config->item("Workflows")["DocsOut"] => "GetDocsOrders",
      $this->config->item("Workflows")["FundingConditions"] => "GetFundingConditionsOrders",
      $this->config->item("Workflows")["SignedDocs"] => "GetSignedDocsOrders",
      $this->config->item("Workflows")["InitialUnderWriting"] => "GetInitialUnderWritingQueue",
      $this->config->item("Workflows")["ConditionwithApproval"] => "GetConditionwithApprovalQueue",
      $this->config->item("Workflows")["Underwriting"] => "GetUnderwritingQueue"
    ];

    $mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$post['QueueUID']]);
    $WorkflowModuleUID = $mQueues->WorkflowModuleUID;
    $ConnectWorkflowFuntion = $WorkflowModuleFunctions[$WorkflowModuleUID];

    if (empty($ConnectWorkflowFuntion)) {
      $ConnectWorkflowFuntion = "GetTitleTeamQueue";
    }

    if (in_array($returntype, ['data', 'count_filter'])) {
      $this->Common_Model->DynamicColumnsCommonQuery($WorkflowModuleUID);
    }

    if ($returntype == 'data') {

      if($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] ) {

        $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '.$this->config->item('Workflows')['Workup'].') AS Workupcount',false);
        
      } else {

        $this->db->select('"" AS Workupcount', false);

      }

      $this->db->select('tOrderImport.*, tOrders.OrderUID, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

      $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName AS AssignedUserName, b.UserName AS RaisedBy,tOrderQueues.RaisedRemarks,tOrderQueues.RaisedDateTime,GROUP_CONCAT(DISTINCT mReasons.ReasonName SEPARATOR ', ') AS ReasonName");
      $this->db->select('tOrders.LastModifiedDateTime');
      $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
      $this->db->select('mQueues.QueueUID,mQueues.IsFollowup,mQueues.FollowupDuration,mQueues.WorkflowModuleUID,tOrderFollowUp.FollowUpUID,mQueues.IsDocsReceived,tOrderQueues.QueueIsDocsReceived,mQueues.IsStatus,tOrderQueues.QueueIsStatus');

    }
    else{
      $this->db->select("1", false);
    }

    /*^^^^^ Get MyOrders Query ^^^^^*/
    $this->Common_Model->{$ConnectWorkflowFuntion}(false);

    $this->db->join("tOrderQueues","tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus = 'Pending'");
    $this->db->join("mQueues","tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '".$post["QueueUID"]."' and mQueues.WorkflowModuleUID='".$WorkflowModuleUID."'");
    $this->db->join('mUsers b','tOrderQueues.RaisedByUserUID = b.UserUID','left');
    $this->db->join('mReasons','FIND_IN_SET(mReasons.ReasonUID, tOrderQueues.RaisedReasonUID)','LEFT');
    
    $this->db->join('tOrderFollowUp','`tOrderFollowUp`.`OrderUID` = `tOrders`.`OrderUID` AND `tOrderFollowUp`.`WorkflowModuleUID` = tOrderFollowUp.WorkflowModuleUID AND `tOrderFollowUp`.`QueueUID` = mQueues.QueueUID  AND tOrderFollowUp.IsCleared = 0','LEFT');

    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);


    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
    
    //Order Queue Permission
    $this->Common_Model->OrdersPermission($WorkflowModuleUID,'SUBQUEUECOUNT');
    
    //advanced search
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    } 


    if (!in_array($returntype, ['count_all'])) {
      // Advanced Search
      // if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      //   $filter = $this->Common_Model->advanced_search($post);
      // }
      // Advanced Search
    
      /*Datatable Search*/
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);
      
      /*Datatable OrderBy*/
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
      
    }
   
    if(isset($post['ReportType']))
    {
      $this->db->where("tOrders.OrderUID IN (".$post['OrderUID'].")",NULL,FALSE);
    }
    if ($returntype == 'data') {

      if (isset($post['length']) && $post['length']!='') {
        $this->db->limit($post['length'], $post['start']);
      }
      // $this->db->where('sf');
     return  $this->db->get()->result();
      //print_r($this->db->last_query());exit;

    }

    if (in_array($returntype, ['count_all', 'count_filter'])) {
      return $this->db->get()->num_rows();
    }



  }

}


?>
