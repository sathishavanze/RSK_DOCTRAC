<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class PipelineReportmodel extends MY_Model {

	function __construct()
	{
		parent::__construct();
    $this->load->library('session');
	}

 function count_all()
 {
  $this->db->select("1");
  $this->filterQuery();
  $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));
  $query = $this->db->count_all_results();
  return $query;
}

function count_filtered($post)
{
  $this->db->select("1");
  $this->filterQuery();
  if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  }
  $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));
      // Datatable Search
  $this->Common_Model->WorkflowQueues_Datatable_Search($post);
      // Datatable OrderBy
  $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
  $query = $this->db->get();
  return $query->num_rows();
}


function filterQuery()
{     
  $this->db->from('tOrders');
  $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
  $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
  $this->db->join('tOrderPropertyRole','tOrders.OrderUID = tOrderPropertyRole.OrderUID','left');
  $this->db->join('tOrderComments','tOrders.OrderUID = tOrderComments.OrderUID','left');
  $this->db->join('mWorkFlowModules','tOrderComments.WorkflowUID = mWorkFlowModules.WorkflowModuleUID','left');
  $this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','left');
  //Order Queue Permission
  $this->Common_Model->reportOrderPermission('','LOANPROCESSORCOUNT');
  
  $this->db->group_by('tOrders.OrderUID');
}

function advanced_search($post)
{
  // if($post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All'){
  //   $this->db->where('tOrders.CustomerUID',$post['advancedsearch']['CustomerUID']);
  // }
  $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));
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

function selecteOptionQuery($workflows)
{
  $this->db->select("tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,tOrders.LoanType,mCustomer.CustomerName,tOrders.PropertyStateCode, mMilestone.MilestoneName");
  $this->db->select("GROUP_CONCAT(distinct concat_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName)) as Borrower");

  // Workflow Comments
  $this->db->select("GROUP_CONCAT(distinct concat_WS(' - ',mWorkFlowModules.WorkflowModuleName,tOrderComments.Description)) as WorkflowComments");

  $DefaultClientUID = $this->session->userdata('DefaultClientUID');
  foreach ($workflows as $key => $value) 
  {
    if($this->config->item('Workflows')['DocChase'] == $value->WorkflowModuleUID)
    {
     $this->db->select("CASE WHEN NOT EXISTS (SELECT tOrderDocChase.orderUID from tOrderDocChase where tOrderDocChase.OrderUID = tOrders.OrderUID) THEN 'N/A'
      WHEN EXISTS(SELECT Answer FROM tDocumentCheckList WHERE WorkflowUID = ".$value->WorkflowModuleUID." AND tDocumentCheckList.OrderUID = tOrders.OrderUID AND Answer = 'Problem Identified' AND (tDocumentCheckList.DocumentTypeName IS NOT NULL or tDocumentCheckList.DocumentTypeUID in (select DocumentTypeUID from mDocumentType where Active = 1))) THEN 'Problem Identified'
      WHEN EXISTS(SELECT tOrderDocChase.OrderUID from tOrderDocChase where tOrderDocChase.IsCleared = 1 and tOrderDocChase.OrderUID = tOrders.OrderUID) THEN 'completed'
      WHEN EXISTS (SELECT tOrderDocChase.OrderUID from tOrderDocChase where tOrderDocChase.IsCleared = 0 and tOrderDocChase.OrderUID = tOrders.OrderUID) THEN 'Pending'
      ELSE 'Not Ready'
      END AS ".$value->SystemName." ");
   }
   else
   {   
     $this->db->select("CASE WHEN EXISTS (select tOrderWorkflows.OrderUID FROM tOrderWorkflows WHERE tOrderWorkflows.WorkflowModuleUID = ".$value->WorkflowModuleUID." AND tOrderWorkflows.IsPresent = 0 and tOrderWorkflows.OrderUID = tOrders.OrderUID ) THEN 'N/A'
      WHEN EXISTS(SELECT Answer FROM tDocumentCheckList WHERE WorkflowUID = ".$value->WorkflowModuleUID." AND tDocumentCheckList.OrderUID = tOrders.OrderUID AND Answer = 'Problem Identified' AND (tDocumentCheckList.DocumentTypeName IS NOT NULL or tDocumentCheckList.DocumentTypeUID in (select DocumentTypeUID from mDocumentType where Active = 1))) THEN 'Problem Identified'
      WHEN EXISTS(SELECT tOrders.OrderUID FROM tOrderAssignments WHERE tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = ".$value->WorkflowModuleUID." AND tOrderAssignments.WorkflowStatus = 5) THEN 'completed'
      WHEN NOT EXISTS (SELECT DependentWorkflowModuleUID FROM mCustomerDependentWorkflowModules JOIN mWorkFlowModules ON mCustomerDependentWorkflowModules.DependentWorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID WHERE mCustomerDependentWorkflowModules.WorkflowModuleUID = ".$value->WorkflowModuleUID." and mCustomerDependentWorkflowModules.CustomerUID = ".$DefaultClientUID.") THEN 'Pending'
      WHEN EXISTS(SELECT tOrders.OrderUID FROM tOrderAssignments WHERE tOrderAssignments.WorkflowStatus = 5 AND tOrderAssignments.OrderUID = tOrders.OrderUID and tOrderAssignments.WorkflowModuleUID in (SELECT DependentWorkflowModuleUID FROM mCustomerDependentWorkflowModules JOIN mWorkFlowModules ON mCustomerDependentWorkflowModules.DependentWorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID WHERE mCustomerDependentWorkflowModules.WorkflowModuleUID = ".$value->WorkflowModuleUID." and mCustomerDependentWorkflowModules.CustomerUID = ".$DefaultClientUID.")) THEN 'Pending'
      ELSE 'Not Ready'
      END AS ".$value->SystemName." ");
    }
 }

 foreach ($workflows as $key => $value) 
  {
    if($this->config->item('Workflows')['DocChase'] == $value->WorkflowModuleUID)
    {
      $this->db->select('(SELECT DATE_FORMAT(tOrderDocChase.ClearedDateTime, "%c/%d/%Y %T") FROM tOrderDocChase WHERE tOrderDocChase.orderUID = tOrders.orderUID and tOrderDocChase.IsCleared = 1 ORDER BY tOrderDocChase.DocChaseUID DESC LIMIT 1)as '.$value->SystemName.'DateTime');

      $this->db->select('(SELECT mUsers.UserName FROM tOrderDocChase LEFT JOIN mUsers ON tOrderDocChase.RaisedByUserUID = mUsers.UserUID WHERE tOrderDocChase.orderUID = tOrders.orderUID and tOrderDocChase.IsCleared = 1 ORDER BY tOrderDocChase.DocChaseUID DESC LIMIT 1)as '.$value->SystemName.'CompletedBy');  
    } 
    else
    {
     $this->db->select('(SELECT DATE_FORMAT(tOrderAssignments.CompleteDateTime, "%c/%d/%Y %T") FROM tOrderAssignments WHERE tOrderAssignments.WorkflowModuleUID = "'.$value->WorkflowModuleUID.'" and tOrderAssignments.orderUID = tOrders.orderUID and tOrderAssignments.Workflowstatus = 5 LIMIT 1) as '.$value->SystemName.'DateTime');

      $this->db->select('(SELECT mUsers.UserName FROM tOrderAssignments LEFT JOIN mUsers ON tOrderAssignments.CompletedByUserUID = mUsers.UserUID WHERE tOrderAssignments.WorkflowModuleUID = "'.$value->WorkflowModuleUID.'" and tOrderAssignments.orderUID = tOrders.orderUID and tOrderAssignments.Workflowstatus = 5 LIMIT 1)as '.$value->SystemName.'CompletedBy'); 
    } 
  }

}

function PipelineReportReportOrders($post,$global='') {
 $workflows = $this->Common_Model->GetCustomerBasedModules();
  $this->selecteOptionQuery($workflows);
  $this->filterQuery();

  

  /* Advanced Search  */
  if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  }
  $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));

  /* Datatable Search */
  $this->Common_Model->WorkflowQueues_Datatable_Search($post);

  /* Datatable OrderBy */
  $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


  if ($post['length']!='') {
    $this->db->limit($post['length'], $post['start']);
  }
  $this->db->order_by('tOrders.OrderNumber','DESC');
  $query = $this->db->get();
  return $query->result();
}


function GetPipelineReportOrdersExcelRecords($post)
{
  $workflows = $this->Common_Model->GetCustomerBasedModules();
  $this->selecteOptionQuery($workflows);
  $this->filterQuery();
      // Advanced Search 
  if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  }
  $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));
  $this->db->order_by('tOrders.OrderNumber','ASC');
  $query = $this->db->get();
  return $query->result();  
}

public function getworkflowstatus($OrderUID,$WorkflowModuleUID)
{
  if($WorkflowModuleUID == $this->config->item('Workflows')['DocChase'])
  {
        $this->db->select('*');
        $this->db->from('tOrderDocChase');
        $this->db->where('tOrderDocChase.OrderUID',$OrderUID);
        $Workflowstatus = $this->db->get()->row();
        $ProblemIdentfiedResult = $this->getCheckListAnswers($OrderUID,$WorkflowModuleUID);
        if(!empty($ProblemIdentfiedResult))
        {
         return 'Issue';
        }
        else if (!empty($Workflowstatus)) 
        {
        $this->db->select('*');
        $this->db->from('tOrderDocChase');
        $this->db->where('tOrderDocChase.OrderUID',$OrderUID);
        $this->db->where('tOrderDocChase.IsCleared',1);
        $DocchaseCleared = $this->db->get()->row();
        if(!empty($DocchaseCleared))
        {
          return 'Completed';
        }
        else
        {
          $workflowpending = $this->getPendingDocChaseOrders($OrderUID,$WorkflowModuleUID);
          if(!empty($workflowpending))
          {
            return 'Pending';
          }
          else
          {
            return 'Not Ready';
          }
        }
      }
      else
      {
       return 'N/A';
     }
  }
  else
  {
    $this->db->select('*');
    $this->db->from('tOrderWorkflows');
    $this->db->where('tOrderWorkflows.WorkflowModuleUID',$WorkflowModuleUID);
    $this->db->where('tOrderWorkflows.IsPresent',1);
    $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
    $CheckWorkflow = $this->db->get()->row();
        if (!empty($CheckWorkflow)) 
        {
          $this->db->select('*');
          $this->db->from('tOrderAssignments');
          $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
          $this->db->where('tOrderAssignments.WorkflowModuleUID',$WorkflowModuleUID);
          $this->db->where('tOrderAssignments.WorkflowStatus',5);
          $Workflowstatus = $this->db->get()->row();

          $ProblemIdentfiedResult = $this->getCheckListAnswers($OrderUID,$WorkflowModuleUID);
          if(!empty($ProblemIdentfiedResult))
          {
           return 'Problem Identified';
          }
          else if (!empty($Workflowstatus)) 
          {
          return 'Completed';
          }
         else
         {
          $workflowpending = $this->getPendingOrders($OrderUID,$WorkflowModuleUID);
          if(!empty($workflowpending))
          {
            return 'Pending';
          }
          else
          {
            return 'Not Ready';
          }
         }
        }
        else
        {
         return 'N/A';
        }
  }
}

function getCheckListAnswers($OrderUID,$WorkflowModuleUID)
{
  $result = '';
  $this->db->select('Answer');
  $this->db->from('tDocumentCheckList');
  $this->db->where(array('WorkflowUID'=>$WorkflowModuleUID,'tDocumentCheckList.OrderUID'=>$OrderUID,'Answer'=>'Problem Identified'));
  $this->db->where('(tDocumentCheckList.DocumentTypeName IS NOT NULL or tDocumentCheckList.DocumentTypeUID in (select DocumentTypeUID from mDocumentType where Active = 1))');
  $result = $this->db->get()->row();
  return $result;
}


function getPendingOrders($OrderUID,$Workflow)
{
  $result = $this->Common_Model->getDependentworkflows($Workflow);
  if(empty($result))
  {
    return 'pending';
  }
  $this->db->select('OrderUID');
  $this->db->from('tOrderAssignments');
  $this->db->where_in('tOrderAssignments.WorkflowModuleUID',$result);
  $this->db->where('tOrderAssignments.WorkflowStatus',5);
  $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
  return $this->db->get()->result();
}

function getPendingDocChaseOrders($OrderUID,$WorkflowModuleUID)
{
  $this->db->select('tOrders.OrderUID');
  $this->Common_Model->GetDocChaseQueue();
  $this->db->where('tOrders.OrderUID',$OrderUID);
  return $this->db->get()->result();
}

function getPendingFilterOrders($Workflow)
{
  $Dependentworkflow = $this->Common_Model->getDependentworkflows($Workflow);
  if(empty($Dependentworkflow))
  {
    return 'SELECT tOrderWorkflows.OrderUID FROM `tOrderWorkflows` WHERE tOrderWorkflows.WorkflowModuleUID = '.$Workflow.' and tOrderWorkflows.IsPresent = 1';
  }
  else
  {
   $Dependentworkflow =  implode(',', $Dependentworkflow);
    return 'SELECT tOrderAssignments.OrderUID FROM `tOrderAssignments` WHERE tOrderAssignments.WorkflowModuleUID in('.$Dependentworkflow.') and tOrderAssignments.WorkflowStatus = 5';
  }
}

}
?>
