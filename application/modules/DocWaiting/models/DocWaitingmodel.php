<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocWaitingmodel extends MY_Model {
	function __construct()
	{
		parent::__construct();
	}
 function count_all()
 {
  $this->db->select("1");
  $this->filterQuery();
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
      // Datatable Search
  $this->Common_Model->WorkflowQueues_Datatable_Search($post);
      // Datatable OrderBy
  $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
  $query = $this->db->get();
  return $query->num_rows();
}


function filterQuery()
{     
  $status[1] = $this->config->item('keywords')['PrescreenCompleted'];
  $status[2] = $this->config->item('keywords')['Pendingdocuments'];

  $this->db->from('tOrders');
  $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','LEFT');
  $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
  $this->db->where_in('`tOrders`.`StatusUID`',$status); 
  /** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
  /** @date Friday 13 March 2020 **/
  /** @description Header Client Selection **/
  if(isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
  	$this->db->where('tOrders.CustomerUID',$this->parameters['DefaultClientUID']);
  }
  $this->db->group_by('tOrders.OrderUID');
}

function advanced_search($post)
{
// echo '<pre>';print_r($post);exit;
  // if($post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All'){
  //   $this->db->where('tOrders.CustomerUID',$post['advancedsearch']['CustomerUID']);
  // }
  $this->db->where('tOrders.CustomerUID',$this->parameters['DefaultClientUID']);
  if($post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All'){
    $this->db->where('tOrders.ProjectUID',$post['advancedsearch']['ProjectUID']);
  }
  if(isset($post['advancedsearch']['WorkflowModuleUID'])){
    if($post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All'){

      foreach ($post['advancedsearch']['WorkflowModuleUID'] as $key => $WorkflowModuleUID) {
        $StatusUID = $this->get_orderworkflowstatus_bypage($WorkflowModuleUID);
      }

      $this->db->where_in('tOrders.StatusUID',$StatusUID);
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


  function get_orderworkflowstatus_bypage($WorkflowModuleUID)
  {
    $StatusUID = [];
    switch ($WorkflowModuleUID) {
      case $this->config->item('Workflows')['PreScreen']:
      $StatusUID[] = $this->config->item('keywords')['NewOrder'];
      break;
      case $this->config->item('Workflows')['WelcomeCall']:
      $StatusUID[] = $this->config->item('keywords')['PrescreenCompleted'];
      $StatusUID[] = $this->config->item('keywords')['Pendingdocuments'];
      break;
      case $this->config->item('Workflows')['TitleTeam']:
      $StatusUID[] = $this->config->item('keywords')['PrescreenCompleted'];
      $StatusUID[] = $this->config->item('keywords')['Pendingdocuments'];
      break;
      case $this->config->item('Workflows')['FHAVACaseTeam']:
      $StatusUID[] = $this->config->item('keywords')['PrescreenCompleted'];
      $StatusUID[] = $this->config->item('keywords')['Pendingdocuments'];
      break;
      case $this->config->item('Workflows')['ThirdPartyTeam']:
      $StatusUID[] = $this->config->item('keywords')['PrescreenCompleted'];
      $StatusUID[] = $this->config->item('keywords')['Pendingdocuments'];
      break;
      case $this->config->item('Workflows')['HOI']:
      $StatusUID[] = $this->config->item('keywords')['PrescreenCompleted'];
      $StatusUID[] = $this->config->item('keywords')['Pendingdocuments'];
      break;
      case $this->config->item('Workflows')['PayOff']:
      $StatusUID[] = $this->config->item('keywords')['PrescreenCompleted'];
      $StatusUID[] = $this->config->item('keywords')['Pendingdocuments'];
      break;
      case $this->config->item('Workflows')['Workup']:
      $StatusUID[] = $this->config->item('keywords')['Alldocuments received'];
      break;
      case $this->config->item('Workflows')['UnderWriter']:
      $StatusUID[] = $this->config->item('keywords')['WorkupCompleted'];
      break;
      case $this->config->item('Workflows')['Scheduling']:
      $StatusUID[] = $this->config->item('keywords')['UnderwriterCompleted'];
      break;
      case $this->config->item('Workflows')['Closing']:
      $StatusUID[] = $this->config->item('keywords')['SchedulingCompleted'];
      default:
      break;
    }
    return $StatusUID;
  }

function selecteOptionQuery()
{
  $this->db->select("tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,tOrders.LoanType,tOrders.StatusUID,
    mStatus.StatusColor,mStatus.StatusName,mCustomer.CustomerName");
  $this->db->select('DATE_FORMAT(tOrders.OrderDueDate, "%m/%d/%Y %H:%i:%s") As OrderDueDate', false);
}

function DocWaitingReportOrders($post,$global='') {

  $this->selecteOptionQuery();
  $this->filterQuery();

  /* Advanced Search  */
  if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  }

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


function GetDocWaitingOrdersExcelRecords($post)
{
  $this->selecteOptionQuery();    
  $this->filterQuery();
      // Advanced Search 
  if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  }
  $this->db->order_by('tOrders.OrderNumber','ASC');
  $query = $this->db->get();
  return $query->result();  
}

public function getworkflowPreScreenstatus($OrderUID)
{

  $WorkflowModuleUID[] = $this->config->item('Workflows')['TitleTeam'];
  $WorkflowModuleUID[] = $this->config->item('Workflows')['PreScreen'];
  $WorkflowModuleUID[] = $this->config->item('Workflows')['WelcomeCall'];
  $WorkflowModuleUID[] = $this->config->item('Workflows')['FHAVACaseTeam'];
  $WorkflowModuleUID[] = $this->config->item('Workflows')['ThirdPartyTeam'];
  $WorkflowModuleUID[] = $this->config->item('Workflows')['DocChase'];
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['PreScreen']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['PreScreen']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
      
          return 'Yes';exit();
        }
        else{
          return 'No';exit();
        }
 }
 else{
   return '-';exit();
 }

//   $this->db->select('mWorkFlowModules.WorkflowModuleName,tOrderAssignments.WorkflowStatus');
//   $this->db->from('tOrderAssignments');
//   $this->db->join('tOrders','tOrders.OrderUID = tOrderAssignments.OrderUID','LEFT');
//   $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID','LEFT');
//   $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
//   $this->db->order_by('tOrderAssignments.WorkflowModuleUID','asc');
//   $OrderWorkflowstatus = $this->db->get()->result();
//   $Workflow_array = array();
//   $viewarray = array();
//   foreach ($OrderWorkflowstatus as $key => $Workflow) {
//     if ($Workflow->WorkflowStatus == 5) {
//       $Workflow_array = $Workflow->WorkflowModuleName;
//     }
//     else{
//       $Workflow_array = '---';
//     }
//     array_push($viewarray, $Workflow_array);
//   }
//   return  implode(" / ",$viewarray);
}
public function getworkflowWelcomeCallstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['WelcomeCall']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['WelcomeCall']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {

      return 'Yes';exit();
    }
    else{
          return 'No';exit();
    }
 }
 else{
   return '-';exit();
 }
}
public function getworkflowTitleTeamstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['TitleTeam']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['TitleTeam']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
    
        return 'Yes';exit();
      }
      else{
        return 'No';exit();
      }
    
 }
 else{
   return '-';exit();
 }
}
public function getworkflowFHAVACaseTeamstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['FHAVACaseTeam']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['FHAVACaseTeam']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
     
          return 'Yes';exit();
        }
        else{
          return 'No';exit();
        }
  
 }
 else{
   return '-';exit();
 }
}
public function getworkflowThirdPartyTeamstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['ThirdPartyTeam']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['ThirdPartyTeam']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
      
          return 'Yes';exit();
        }
        else{
          return 'No';exit();
        }   
 }
 else{
   return '-';exit();
 }
}

 public function getworkflowHOI($OrderUID)
 {
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['HOI']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['HOI']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {

      return 'Yes';exit();
    }
    else{
      return 'No';exit();
    }   
  }
  else{
   return '-';exit();
 }
} 

public function getworkflowPayOff($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['PayOff']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['PayOff']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {

      return 'Yes';exit();
    }
    else{
      return 'No';exit();
    }   
  }
  else{
   return '-';exit();
 }
}

public function getworkflowDocChasestatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['DocChase']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderDocChase');
    $this->db->where('tOrderDocChase.OrderUID',$OrderUID);
    $this->db->where('tOrderDocChase.IsCleared', 0);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
      
          return 'Yes';exit();
        }
        else{
          return 'No';exit();
        }
 }
 else{
   return '-';exit();
 }
}
public function getworkflowWorkupstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['Workup']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['Workup']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
      return 'Yes';exit();
    }
    else{
     return 'No';exit();
   }

 }
 else{
   return '-';exit();
 }
}
public function getworkflowUnderWriterstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['UnderWriter']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['UnderWriter']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
      return 'Yes';exit();
    }
    else{
     return 'No';exit();
   }

 }
 else{
   return '-';exit();
 }
}
public function getworkflowSchedulingstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['Scheduling']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['Scheduling']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
      return 'Yes';exit();
    }
    else{
     return 'No';exit();
   }

 }
 else{
   return '-';exit();
 }
}
public function getworkflowClosingstatus($OrderUID)
{
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['Closing']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$this->config->item('Workflows')['Closing']);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    if ($Workflowstatus) {
      return 'Yes';exit();
    }
    else{
     return 'No';exit();
   }

 }
 else{
   return '-';exit();
 }
}


// Madhuri
public function getDocWaitingList($OrderUID)
{
  $status = $this->config->item('Workflows')['WelcomeCall'];
  $this->db->select('*');
  $this->db->from('tOrderWorkflows');
  $this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
  $this->db->where('tOrderWorkflows.WorkflowModuleUID',$this->config->item('Workflows')['WelcomeCall']);
  $CheckWorkflow = $this->db->get()->row();
  if ($CheckWorkflow) {
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$status);
    $this->db->where('tOrderAssignments.WorkflowStatus',5);
    $Workflowstatus = $this->db->get()->row();
    return $Workflowstatus;

  }
  else{
     return '-';exit();
  }

}


  function getCheckListAnswers($OrderUID,$WorkflowModuleUID)
  {
    $result = '';
    $result =  $this->db->select('Answer,IsChaseSend,FileUploaded,Comments')->from('tDocumentCheckList')->where(array('WorkflowUID'=>$WorkflowModuleUID,'OrderUID'=>$OrderUID,'Answer'=>'Problem Identified'))->get()->row();
    return $result;
  }


  public function getWelcomeDetails($OrderUID)
{
  $status = $this->config->item('Workflows')['WelcomeCall'];
    $this->db->select('*');
    $this->db->from('tOrderAssignments');
    $this->db->where('tOrderAssignments.OrderUID',$OrderUID);
    $this->db->where('tOrderAssignments.WorkflowModuleUID',$status);
    $Workflowstatus = $this->db->get()->row();
    return $Workflowstatus;

}


function getCheckListAnswersForModules($OrderUID,$WorkflowModuleUID)
  {
    $result = '';
    // $result =  $this->db->select('Answer,IsChaseSend,FileUploaded,Comments')->from('tDocumentCheckList')->where(array('WorkflowUID'=>$WorkflowModuleUID,'OrderUID'=>$OrderUID,'Answer'=>'Problem Identified'))->get()->row();

    $status = $this->config->item('Workflows')['WelcomeCall'];
    $this->db->select('Answer,IsChaseSend,FileUploaded,Comments');
    $this->db->from('tDocumentCheckList');
    $this->db->where('tDocumentCheckList.OrderUID',$OrderUID);
    $this->db->where('tDocumentCheckList.Answer','Problem Identified');
    $this->db->where_in('tDocumentCheckList.WorkflowUID',$WorkflowModuleUID);
    $result = $this->db->get()->row();
    
    return $result;
  }

}
?>
