<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocChaseReportmodel extends MY_Model {

	function __construct()
	{
		parent::__construct();
    $this->load->library('session');
	}

  /**
  *get all docchase report count 
  *@author alwin <alwin.l@avanzegroup.com>
  *@since Friday 31 March 2020
  */
 function count_all()
 {
  $this->db->select("1");
  $this->filterQuery();
  $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));
  $query = $this->db->count_all_results();
  return $query;
}

  /**
  *get filter docchase report count 
  *@author alwin <alwin.l@avanzegroup.com>
  *@since Friday 31 March 2020
  */
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

  /**
  * docchase fiter join query 
  *@author alwin <alwin.l@avanzegroup.com>
  *@since Friday 31 March 2020
  */
function filterQuery()
{     
   $this->Common_Model->GetDocChaseQueue();
    $this->db->where("tOrders.OrderUID IN (SELECT OrderUID FROM tOrderDocChase WHERE tOrderDocChase.IsCleared = 0 )", NULL , FALSE);
}

  /**
  *docchase fiter advance search 
  *@author alwin <alwin.l@avanzegroup.com>
  *@since Friday 31 March 2020
  */
function advanced_search($post)
{
  if($post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All'){
    $this->db->where('tOrders.ProjectUID',$post['advancedsearch']['ProjectUID']);
  }
  if($post['advancedsearch']['Status'] != '' && $post['advancedsearch']['Status'] != 'All'){
    $this->db->where('tOrders.StatusUID',$post['advancedsearch']['Status']);
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
  *docchase SELECT QUERY FUNCTION 
  *@author alwin <alwin.l@avanzegroup.com>
  *@since Friday 31 March 2020
  */
function selecteOptionQuery($workflows)
{
  $this->db->select("tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,mStatus.StatusName,mStatus.StatusColor");
  foreach ($workflows as $key => $value) 
  {
    if($this->config->item('Workflows')['DocChase'] != $value->WorkflowModuleUID)
    {
   
     $this->db->select("(SELECT COUNT(tOrderDocChase.WorkflowModuleUID) FROM tOrderDocChase WHERE tOrderDocChase.OrderUID = tOrders.OrderUID and tOrderDocChase.WorkflowModuleUID = ".$value->WorkflowModuleUID." AND IsCleared = 0) as ".$value->SystemName);
   }
 }
}

/**
  *docchase MAIN QUERY 
  *@author alwin <alwin.l@avanzegroup.com>
  *@since Friday 31 March 2020
  */
function DocChaseReportReportOrders($post,$global='') {
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

/**
  *docchase MAIN QUERY FOR EXCEL EXPORT 
  *@author alwin <alwin.l@avanzegroup.com>
  *@since Friday 31 March 2020
  */
function GetDocChaseReportOrdersExcelRecords($post)
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

}
?>
