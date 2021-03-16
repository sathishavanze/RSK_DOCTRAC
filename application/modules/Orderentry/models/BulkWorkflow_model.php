<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class BulkWorkflow_model extends MY_Model {
  
  function __construct()
  { 
    parent::__construct();   
  }

  function ChkOrderisValid($OrderNo='')
  {
    $OrderNumber = strtoupper($OrderNo);
    $this->db->select('OrderUID');
    $this->db->where('OrderNumber',$OrderNumber);
    $result = $this->db->get('tOrders')->row();
    if(is_object($result))
    {
      return $result->OrderUID;
    } else {
      return 0;
    }
  }

  function getWorkflowID($WorkflowName)
  {
    $this->db->select('WorkflowModuleUID');
    $this->db->like('WorkflowModuleName', $WorkflowName);
    $result = $this->db->get('mWorkFlowModules')->row(); 
    if(is_object($result))
    {
      return $result->WorkflowModuleUID;
    } else {
      return 0;
    }
  }  

  function ClearDocChase($OrderUID,$WorkflowID)
  {
    $data['IsCleared'] = 1;
    $data['ClearedDateTime'] = date('Y-m-d H:i:s');
    $data['CompletedDateTime'] = date('Y-m-d H:i:s');
    $data['ClearedByUserUID'] = $this->loggedid;
    $this->db->where('OrderUID', $OrderUID);
    $this->db->where('WorkflowModuleUID', $WorkflowID);
    $this->db->update('tOrderDocChase', $data);
  }

  function updateOrderStatus($Status,$OrderUID,$Workflow,$Workflows)
  {
    if($Workflow == $this->config->item('Workflows')['Closing'])
    {
      $this->db->select('COUNT(DISTINCT WorkflowModuleUID) AS Complete');
      $this->db->where('OrderUID', $OrderUID);
      $this->db->where('WorkflowStatus', 5);
      $this->db->where_in('WorkflowModuleUID', $Workflows);
      $this->db->from('tOrderAssignments');
      $result = $this->db->get()->row();
      if($result->Complete == count($Workflows))
      {
        $Status['StatusUID'] = $this->config->item('keywords')['ClosedandBilled'];
      } 
    }
    $this->db->where('OrderUID', $OrderUID);
    $this->db->update('tOrders', $Status);
  }

  function getOrderWorkflows($OrderUID,$Workflow)
  {
    $this->db->select('WorkflowModuleUID');
    $this->db->from('tOrderWorkflows');
    $this->db->where('IsPresent',1);
    $this->db->where_in('OrderUID',$OrderUID,FALSE);
    $this->db->where_in('WorkflowModuleUID',$Workflow,FALSE);
    $this->db->group_by('WorkflowModuleUID');
    return $this->db->get('')->result();
  }

  function CheckOrderAssigned($OrderUID,$WorkflowUID)
  {
    $this->db->select('WorkflowStatus');
    $this->db->where('OrderUID',$OrderUID);
    $this->db->where('WorkflowModuleUID',$WorkflowUID);
    $result = $this->db->get('tOrderAssignments')->row();
    if(is_object($result))
    {
      return $result->WorkflowStatus;
    } else {
      return 100;
    }
  }

  function assignOrderToComplete($data)
  {
    if($this->db->insert('tOrderAssignments',$data) == true)
    {
      return true;
    } else {
      return false;
    }
  }

  function changeWorkflowStatus($OrderUID,$Workflow,$data)
  {
    $this->db->where('OrderUID', $OrderUID);
    $this->db->where('WorkflowModuleUID', $Workflow);
    if($this->db->update('tOrderAssignments',$data) == true)
    {
      return true;
    } else {
      return false;
    }
  }

}
?>