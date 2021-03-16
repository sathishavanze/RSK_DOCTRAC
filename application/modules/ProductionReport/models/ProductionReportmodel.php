<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProductionReportmodel extends MY_Model {
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
      // Advanced Search 
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
       $status[0] = $this->config->item('keywords')['Completed'];   
       $status[1] = $this->config->item('keywords')['Cancelled'];         
       $this->db->from('tOrders');
       $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID','left');
       $this->db->join('mStatus','mStatus.StatusUID = tOrders.StatusUID','left');
       $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
       $this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','left');
       $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
       $this->db->join('mProjectCustomer','mProjectCustomer.ProductUID = tOrders.ProjectUID','left');
       $this->db->join('mProducts','mProducts.ProductUID=tOrders.ProductUID','left');
       $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','left');
       $this->db->where_not_in('tOrders.StatusUID', $status);
       if ($this->RoleUID!=1) {
        $this->db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
        }          
       $this->db->group_by('tOrders.OrderUID');
    }

    function advanced_search($post)
    {
      if($post['advancedsearch']['ProductUID'] != '' && $post['advancedsearch']['ProductUID'] != 'All'){
        $this->db->where('tOrders.ProductUID',$post['advancedsearch']['ProductUID']);
      }
      if($post['advancedsearch']['LenderUID'] != '' && $post['advancedsearch']['LenderUID'] != 'All'){
        $this->db->where('tOrders.LenderUID',$post['advancedsearch']['LenderUID']);
      }
      if($post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All'){
        $this->db->where('tOrders.CustomerUID',$post['advancedsearch']['CustomerUID']);
      }
      if($post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All'){
        $this->db->where('tOrders.ProjectUID',$post['advancedsearch']['ProjectUID']);
      }
      if($post['advancedsearch']['FromDate']){
        $this->db->where('DATE(`tOrderAssignments`.`CompleteDateTime` ) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
      }
      if($post['advancedsearch']['ToDate']){
        $this->db->where('DATE(`tOrderAssignments`.`CompleteDateTime` ) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
      }

      return true;
    }

function selecteOptionQuery()
{
  $this->db->select("`tOrders`.`OrderNumber`,`tOrderDocumentCheckIn`.`SettlementAgentName`,`tOrderDocumentCheckIn`.`AgentNo`,`tOrderPropertyRole`.`BorrowerFirstName`,`mUsers`.`UserName`,`tOrders`.`LoanNumber`,`mProjectCustomer`.`ProjectName`, `tOrderAssignments`.`CompleteDateTime` AS Date, tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName,tOrderAssignments.AssignedToUserUID");
}

  function ProductionReportDaily($post)
	{ 
      $this->selecteOptionQuery();
      $this->filterQuery();
      // Advanced Search 
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->advanced_search($post);
      }
      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

	    if ($post['length']!='') {
	       $this->db->limit($post['length'], $post['start']);
	    }
      $this->db->order_by('tOrders.OrderNumber','DESC');
	    $query = $this->db->get();
	    return $query->result();
	}


    function GetProductionReportExcel($post)
    {
      $this->selecteOptionQuery();    
      $this->filterQuery();
      // Advanced Search 
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->advanced_search($post);
      }
      $this->db->order_by('tOrders.OrderNumber','DESC');
        $query = $this->db->get();
        return $query->result();  
    }

}
?>
