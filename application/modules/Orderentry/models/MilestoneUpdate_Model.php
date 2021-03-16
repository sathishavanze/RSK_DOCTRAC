   <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MilestoneUpdate_Model extends MY_Model {
  
  function __construct()
  { 
    parent::__construct();   
  }

  function ChkOrderisValid($OrderNo='')
  {
    $DefaultClientUID = $this->session->userdata('DefaultClientUID');
    $OrderNumber = strtoupper($OrderNo);
    $this->db->select('OrderUID,LoanNumber');
    $this->db->where('OrderNumber',$OrderNumber);
    $this->db->where('tOrders.CustomerUID',$DefaultClientUID);
    $result = $this->db->get('tOrders')->row();
    if(is_object($result))
    {
      return $result;
    } else {
      return 0;
    }
  } 

  function IsMilestoneExsist($MilestoneName)
  {
    $MilestoneName = strtoupper($MilestoneName);
    $this->db->select('MilestoneUID');
    $this->db->where('MilestoneName',$MilestoneName);
    $result = $this->db->get('mMilestone')->row();
    if(is_object($result))
    {
      return $result;
    } else {
      return 0;
    }
  }

  function CheckMilestone($OrderUID,$MilestoneName)
  {
    if(!empty($OrderUID))
    {
      $DefaultClientUID = $this->session->userdata('DefaultClientUID');
      $data = [];
      $Workflowquery = $this->db->query('SELECT mCustomerWorkflowModules.ColorCode,GROUP_CONCAT(mWorkFlowModules.SystemName) as SystemName ,GROUP_CONCAT(mWorkFlowModules.WorkflowModuleUID) as WorkflowModuleUID FROM
        tOrders
        LEFT JOIN tOrderWorkflows ON tOrders.OrderUID = tOrderWorkflows.OrderUID
        LEFT JOIN mCustomerMilestones ON mCustomerMilestones.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND mCustomerMilestones.CustomerUID = tOrders.CustomerUID AND mCustomerMilestones.ProductUID = tOrders.ProductUID
        LEFT JOIN mCustomerWorkflowModules ON mCustomerWorkflowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND mCustomerWorkflowModules.CustomerUID = tOrders.CustomerUID AND mCustomerWorkflowModules.ProductUID = tOrders.ProductUID
        LEFT JOIN mWorkFlowModules ON mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID
        WHERE
        tOrderWorkflows.WorkflowModuleUID IN (
        SELECT
        mCustomerMilestones.WorkflowModuleUID
        FROM
        mCustomerMilestones
        WHERE
        mCustomerMilestones.MilestoneUID IN (
        SELECT
        mMilestone.MilestoneUID
        FROM
        mMilestone
        WHERE
        mMilestone.MilestoneName =  "'.$MilestoneName.'"
        ) and mCustomerMilestones.CustomerUID = '.$DefaultClientUID.'
        )
        AND tOrders.OrderUID = "'.$OrderUID.'"');
      $Workflow = $Workflowquery->row();
      $data['result'] = $Workflow;
      if(!empty($Workflow->WorkflowModuleUID))
      {
        $this->db->select('*');
        $this->db->from('tOrderWorkflows');
        $this->db->where(array('tOrderWorkflows.IsPresent'=>1,'tOrderWorkflows.OrderUID'=>$OrderUID));
        $this->db->where('tOrderWorkflows.WorkflowModuleUID IN ('.$Workflow->WorkflowModuleUID.')');
        $WorkflowPresent = $this->db->get()->result();
        if(empty($WorkflowPresent))
        {
          $data['Workflow'] = 'NA';
        }
        else
        {
          $WorkflowCompletedquery = $this->db->query('SELECT tOrderAssignments.OrderUID FROM tOrderAssignments WHERE tOrderAssignments.WorkflowModuleUID IN ( '.$Workflow->WorkflowModuleUID.') and tOrderAssignments.OrderUID = '.$OrderUID.' and tOrderAssignments.WorkflowStatus = 5');
          $WorkflowCompleted = $WorkflowCompletedquery->row();
          if(!empty($WorkflowCompleted))
          {
            return false;
          }
          else
          {
            $data['Workflow'] = 'NotCompleted';
          }
        }
        return $data;
      }
      else
      {
        return false;
      }
    }
  }

 function BulkMilestoneUpdate($OrderUID,$Milestone)
 {
  $data = array('MilestoneUID' => $Milestone);
  $this->db->where('tOrders.OrderUID',$OrderUID);
  $this->db->update('tOrders',$data);
 }

 function InsertOrderMileStone($OrderUID,$Milestone) {
  $data = array(
            'OrderUID'=>$OrderUID,
            'MilestoneUID'=>$Milestone,
            'CompletedByUserUID'=>$this->loggedid,
            'CompletedDateTime'=>date('Y-m-d H:i:s')
          );
  $this->db->insert('tOrderMileStone',$data);
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
  if($post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All'){
    $this->db->where('tOrders.CustomerUID',$post['advancedsearch']['CustomerUID']);
  }
  if($post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All'){
    $this->db->where('tOrders.ProjectUID',$post['advancedsearch']['ProjectUID']);
  }
  if($post['advancedsearch']['Milestone'] != '' && $post['advancedsearch']['Milestone'] != 'All'){
    $this->db->where('tOrders.MilestoneUID',$post['advancedsearch']['Milestone']);
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
  $this->db->select("tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,mMilestone.MilestoneName");
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