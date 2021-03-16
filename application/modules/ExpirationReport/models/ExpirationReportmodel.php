<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ExpirationReportmodel extends MY_Model {

	function __construct()
	{
		parent::__construct();
    $this->load->library('session');
	}

 function count_all($post)
 {
  $this->db->select("1");
  $this->tableJoinQuery();
  $this->filterQuery($post);

  /* Advanced Search  */
  if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  } else {
    if (!empty($post['DocumentTypeUIDArr'])) {
      $this->db->where_in('tDocumentCheckList.DocumentTypeUID', $post['DocumentTypeUIDArr']);
      $this->db->where_in('tDocumentCheckList.WorkflowUID', $this->config->item('Expired_Checklist_Enabled_Workflows'));
    }
  }

  $query = $this->db->count_all_results();
  return $query;
}

function count_filtered($post)
{
  $this->db->select("1");
  $this->tableJoinQuery();
  $this->filterQuery($post);

  /* Advanced Search  */
  if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  } else {
    if (!empty($post['DocumentTypeUIDArr'])) {
      $this->db->where_in('tDocumentCheckList.DocumentTypeUID', $post['DocumentTypeUIDArr']);
      $this->db->where_in('tDocumentCheckList.WorkflowUID', $this->config->item('Expired_Checklist_Enabled_Workflows'));
    }
  }

  /* Datatable Search */
  $this->Common_Model->WorkflowQueues_Datatable_Search($post);

  $query = $this->db->get();
  return $query->num_rows();
}


function tableJoinQuery()
{     
  $this->db->from('tOrders');
  $this->db->join('tDocumentCheckList','tOrders.OrderUID = tDocumentCheckList.OrderUID','left');
  $this->db->join('mWorkFlowModules','tDocumentCheckList.WorkflowUID = mWorkFlowModules.WorkflowModuleUID','left');
  $this->db->join('mDocumentType','tDocumentCheckList.DocumentTypeUID = mDocumentType.DocumentTypeUID','left');
  $this->db->join('tOrderPropertyRole','tOrders.OrderUID = tOrderPropertyRole.OrderUID','left');
  $this->db->join('mMilestone','mMilestone.MilestoneUID = tOrders.MilestoneUID','left');
  $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','left');
  $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = tOrderAssignments.WorkflowModuleUID','left');
  $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','left');
}

function advanced_search($post)
{
  if($post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All'){
    if ($post['advancedsearch']['WorkflowModuleUID'] == "FHA_MONTH") {

      $this->db->where_in('tDocumentCheckList.DocumentTypeUID', $post['DocumentTypeUIDArr']);
      $this->db->where_in('tDocumentCheckList.WorkflowUID', $this->config->item('Workflows')['FHAVACaseTeam']);
    } elseif (!empty($post['DocumentTypeUIDArr'])) {

      $this->db->where_in('tDocumentCheckList.DocumentTypeUID', $post['DocumentTypeUIDArr']);
      $this->db->where_in('tDocumentCheckList.WorkflowUID', $post['advancedsearch']['WorkflowModuleUID']);
    }
  } else {
    if (!empty($post['DocumentTypeUIDArr'])) {
      $this->db->where_in('tDocumentCheckList.DocumentTypeUID', $post['DocumentTypeUIDArr']);
      $this->db->where_in('tDocumentCheckList.WorkflowUID', $this->config->item('Expired_Checklist_Enabled_Workflows'));
    }
  }

  if ($post['advancedsearch']['WorkflowModuleUID'] != "FHA_MONTH") {
    if($post['advancedsearch']['filterexpiredays'] != '' && $post['advancedsearch']['filterexpiredays'] != 'All'){
      if($post['advancedsearch']['FilterStatus'] != '' && $post['advancedsearch']['FilterStatus'] == 'ExpiredOrders'){
        $this->db->where("STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y') <",Date('Y/m/d'));
        $this->db->where("STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y') >=",Date('Y/m/d', strtotime('-'.$post['advancedsearch']['filterexpiredays'].' days')), NULL, FALSE);
      }
      if($post['advancedsearch']['FilterStatus'] != '' && $post['advancedsearch']['FilterStatus'] == 'ExpiryOrders'){
        $this->db->where("STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y') >=",Date('Y/m/d'));
        $this->db->where("STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y') <=",Date('Y/m/d', strtotime('+'.$post['advancedsearch']['filterexpiredays'].' days')), NULL, FALSE);
      }
    } else {
      if($post['advancedsearch']['FilterStatus'] != '' && $post['advancedsearch']['FilterStatus'] == 'ExpiredOrders'){
        $this->db->where("STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y') < DATE(NOW())", NULL, FALSE);
      }
      if($post['advancedsearch']['FilterStatus'] != '' && $post['advancedsearch']['FilterStatus'] == 'ExpiryOrders'){
        $this->db->where("STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y') >= DATE(NOW())", NULL, FALSE);
      }
    }
  }
  return true;
}

function selecteOptionQuery()
{
  $this->db->select("tOrders.OrderUID,tOrders.LoanNumber,tOrders.LoanType,mMilestone.MilestoneName,tOrders.PropertyStateCode,mWorkFlowModules.WorkflowModuleName,mUsers.UserName,tOrderImport.LoanProcessor,mDocumentType.DocumentTypeName,tDocumentCheckList.DocumentExpiryDate,tDocumentCheckList.DocumentDate");
  $this->db->select("CONCAT_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName) as Borrower");
 }

 function filterQuery($post) {
  if ($post['advancedsearch']['WorkflowModuleUID'] == "FHA_MONTH") {
    $this->db->where('(tDocumentCheckList.DocumentDate IS NOT NULL AND tDocumentCheckList.DocumentDate != "")', NULL, false);
  } else {
    $this->db->where('(tDocumentCheckList.DocumentExpiryDate IS NOT NULL AND tDocumentCheckList.DocumentExpiryDate != "")', NULL, false);
  }
  $this->db->where('(tDocumentCheckList.WorkflowUID IS NOT NULL AND tDocumentCheckList.WorkflowUID != "")', NULL, false);
  $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));

  // Expiration report only 2series milestone  
  $this->db->where_in('tOrders.MilestoneUID',$this->config->item('ExpirationReportMilestones'));
 }

function ExpirationReportOrders($post,$global='') {
  $this->selecteOptionQuery();
  $this->tableJoinQuery();
  $this->filterQuery($post);

  /* Advanced Search  */
  if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
    $filter = $this->advanced_search($post);
  } else { 
    if (!empty($post['DocumentTypeUIDArr'])) {
      $this->db->where_in('tDocumentCheckList.DocumentTypeUID', $post['DocumentTypeUIDArr']);
      $this->db->where_in('tDocumentCheckList.WorkflowUID', $this->config->item('Expired_Checklist_Enabled_Workflows'));
    }
  }

  /* Datatable Search */
  $this->Common_Model->WorkflowQueues_Datatable_Search($post);

  /* Datatable OrderBy */
  $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


  if ($post['length']!='') {
    $this->db->limit($post['length'], $post['start']);
  }
  $this->db->order_by('tDocumentCheckList.DocumentExpiryDate','ASC');
  $query = $this->db->get();
  return $query->result();
}

}
?>
