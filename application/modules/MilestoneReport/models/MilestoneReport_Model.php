<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MilestoneReport_Model extends MY_Model {
  
  function __construct()
  { 
    parent::__construct();   
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
  $this->db->join('mMilestone','mMilestone.MilestoneUID = tOrders.MilestoneUID','left');
  $this->db->group_by('tOrders.OrderUID');
}

function advanced_search($post)
{
  if($post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All'){
    $this->db->where('tOrders.ProjectUID',$post['advancedsearch']['ProjectUID']);
  }
  if($post['advancedsearch']['Milestone'] != '' && $post['advancedsearch']['Milestone'] != 'All')
  {
   if($post['advancedsearch']['Status'] != '' && $post['advancedsearch']['Status'] != 'All')
   { 
     $DefaultClientUID = $this->session->userdata('DefaultClientUID');
     if($post['advancedsearch']['Status'] == 'NA')
     {
      $this->db->where('tOrders.OrderUID in(select tOrderWorkflows.OrderUID from tOrderWorkflows  where tOrderWorkflows.WorkflowModuleUID in(SELECT
        mCustomerMilestones.WorkflowModuleUID
      FROM
        mCustomerMilestones
      WHERE mCustomerMilestones.CustomerUID = '.$DefaultClientUID.' and
        mCustomerMilestones.MilestoneUID IN ('.$post['advancedsearch']['Milestone'].'))  and tOrderWorkflows.IsPresent = 0)');
    }
    else if($post['advancedsearch']['Status'] == 'Completed')
    { 
     $this->db->where('(tOrders.OrderUID in (SELECT
      tOrders.OrderUID
      FROM
      tOrders
      LEFT JOIN tOrderAssignments on tOrderAssignments.OrderUID = tOrders.OrderUID
      WHERE
      tOrderAssignments.WorkflowModuleUID IN (
      SELECT
      mCustomerMilestones.WorkflowModuleUID
      FROM
      mCustomerMilestones
      WHERE mCustomerMilestones.CustomerUID = '.$DefaultClientUID.' and
      mCustomerMilestones.WorkflowModuleUID IN (
      SELECT
      DependentWorkflowModuleUID
      FROM
      mCustomerDependentWorkflowModules
      WHERE
      mCustomerDependentWorkflowModules.CustomerUID = '.$DefaultClientUID.'
      AND mCustomerDependentWorkflowModules.WorkflowModuleUID IN (
      SELECT
      mCustomerMilestones.WorkflowModuleUID
      FROM
      mCustomerMilestones
      WHERE
      mCustomerMilestones.MilestoneUID = '.$post['advancedsearch']['Milestone'].' and mCustomerMilestones.CustomerUID = '.$DefaultClientUID.'
      )
      ) and mCustomerMilestones.WorkflowModuleUID IN (SELECT
      mCustomerMilestones.WorkflowModuleUID
      FROM
      mCustomerMilestones
      WHERE mCustomerMilestones.CustomerUID = '.$DefaultClientUID.' and 
      mCustomerMilestones.MilestoneUID = '.$post['advancedsearch']['Milestone'].')
      )
      AND tOrderAssignments.WorkflowStatus = 5) or tOrders.OrderUID in(select tOrders.OrderUID from tOrders where tOrders.MilestoneUID ='.$post['advancedsearch']['Milestone'].'))');
   } 
   else if($post['advancedsearch']['Status'] == 'Pending')
   {
    $this->db->where('tOrders.OrderUID NOT in(select tOrderWorkflows.OrderUID from tOrderWorkflows  where tOrderWorkflows.WorkflowModuleUID in(SELECT
      mCustomerMilestones.WorkflowModuleUID
      FROM
      mCustomerMilestones
      WHERE mCustomerMilestones.CustomerUID = '.$DefaultClientUID.' and 
      mCustomerMilestones.MilestoneUID IN ('.$post['advancedsearch']['Milestone'].'))  and tOrderWorkflows.IsPresent = 0)');
     $this->db->where('tOrders.OrderUID NOT in(select tOrders.OrderUID from tOrders where tOrders.MilestoneUID ='.$post['advancedsearch']['Milestone'].')');
    $this->db->where('tOrders.OrderUID  NOT in (SELECT
      tOrders.OrderUID
      FROM
      tOrders
      LEFT JOIN tOrderAssignments on tOrderAssignments.OrderUID = tOrders.OrderUID
      WHERE
      tOrderAssignments.WorkflowModuleUID IN (
      SELECT
      mCustomerMilestones.WorkflowModuleUID
      FROM
      mCustomerMilestones
      WHERE mCustomerMilestones.CustomerUID = '.$DefaultClientUID.' and 
      mCustomerMilestones.WorkflowModuleUID IN (
      SELECT
      DependentWorkflowModuleUID
      FROM
      mCustomerDependentWorkflowModules
      WHERE
      mCustomerDependentWorkflowModules.CustomerUID = '.$DefaultClientUID.'
      AND mCustomerDependentWorkflowModules.WorkflowModuleUID IN (
      SELECT
      mCustomerMilestones.WorkflowModuleUID
      FROM
      mCustomerMilestones
      WHERE
      mCustomerMilestones.MilestoneUID = '.$post['advancedsearch']['Milestone'].' and mCustomerMilestones.CustomerUID = '.$DefaultClientUID.'
      )
      ) and mCustomerMilestones.WorkflowModuleUID IN (SELECT
      mCustomerMilestones.WorkflowModuleUID
      FROM
      mCustomerMilestones
      WHERE mCustomerMilestones.CustomerUID = '.$DefaultClientUID.' and 
      mCustomerMilestones.MilestoneUID = '.$post['advancedsearch']['Milestone'].')
      )
      AND tOrderAssignments.WorkflowStatus = 5)');
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

function selecteOptionQuery()
{
  $Milestone = $this->Common_Model->Milestone();
  $DefaultClientUID = $this->session->userdata('DefaultClientUID');
  $this->db->select("tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,mMilestone.MilestoneName");
  foreach ($Milestone as $key => $value) 
  {  
     $this->db->select("CASE
WHEN EXISTS (
  SELECT
    tOrderWorkflows.OrderUID
  FROM
    tOrderWorkflows
  WHERE
    tOrderWorkflows.WorkflowModuleUID IN (
      SELECT
        mCustomerMilestones.WorkflowModuleUID
      FROM
        mCustomerMilestones
      WHERE
        mCustomerMilestones.MilestoneUID IN (".$value->MilestoneUID.") and mCustomerMilestones.CustomerUID = ".$DefaultClientUID."
    )
  AND tOrderWorkflows.IsPresent = 0
  AND tOrderWorkflows.OrderUID = tOrders.OrderUID
) THEN
  'N/A'
WHEN tOrders.MilestoneUID = ".$value->MilestoneUID."
 THEN
  'completed'
WHEN tOrders.OrderUID in(SELECT
  tOrderAssignments.OrderUID
FROM
  tOrderAssignments
WHERE
  tOrderAssignments.WorkflowModuleUID IN (
    SELECT
      mCustomerMilestones.WorkflowModuleUID
    FROM
      mCustomerMilestones
    WHERE mCustomerMilestones.CustomerUID = ".$DefaultClientUID." and 
      mCustomerMilestones.WorkflowModuleUID IN (
        SELECT
          DependentWorkflowModuleUID
        FROM
          mCustomerDependentWorkflowModules
        WHERE
          mCustomerDependentWorkflowModules.CustomerUID = ".$DefaultClientUID."
        AND mCustomerDependentWorkflowModules.WorkflowModuleUID IN (
          SELECT
            mCustomerMilestones.WorkflowModuleUID
          FROM
            mCustomerMilestones
          WHERE
            mCustomerMilestones.MilestoneUID = tOrders.MilestoneUID and mCustomerMilestones.CustomerUID = ".$DefaultClientUID."
        )
      ) and mCustomerMilestones.WorkflowModuleUID IN (SELECT
            mCustomerMilestones.WorkflowModuleUID
          FROM
            mCustomerMilestones
          WHERE
            mCustomerMilestones.MilestoneUID = ".$value->MilestoneUID." and mCustomerMilestones.CustomerUID = ".$DefaultClientUID.")
  )
AND tOrderAssignments.WorkflowStatus = 5 and tOrderAssignments.OrderUID = tOrders.OrderUID)
 THEN
  'completed'
ELSE
  'Pending'
END ".$value->MilestoneName." ");
 }
}

function MilestoneReportOrders($post,$global='') {
  $this->selecteOptionQuery();
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
  $this->db->order_by('tOrders.OrderNumber','ASC');
  $query = $this->db->get();
  return $query->result();
}


function MilestoneExcelRecords($post)
{
  $this->selecteOptionQuery();
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

}
?>