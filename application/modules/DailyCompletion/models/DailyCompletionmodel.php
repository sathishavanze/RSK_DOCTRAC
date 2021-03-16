<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DailyCompletionmodel extends MY_Model {
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
     
      $status = $this->config->item('keywords')['Completed'];   
      
       $this->db->from('tOrders');
       $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID','left');
       $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
       $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
       $this->db->join('tOrderPropertyRole','tOrders.OrderUID = tOrderPropertyRole.OrderUID','left');
       $this->db->join('tOrderDocumentCheckIn','tOrders.OrderUID = tOrderDocumentCheckIn.OrderUID','left');
       $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProductUID','left');
       $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
       $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');
       $this->db->where('tOrders.StatusUID', $status);   
       if ($this->RoleUID!=1) {
          $this->db->where('`tOrders`.`OrderEntryByUserUID`',$this->loggedid); 
        }   
        
       $this->db->where('`tOrderAssignments`.`CompleteDateTime` != ', NULL);       
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
  $this->db->select("(SELECT MAX(`tOrderAssignments`.`CompleteDateTime`) FROM tOrderAssignments) AS MaxCompleteDate, `tOrderDocumentCheckIn`.`SettlementAgentName`,`tOrderDocumentCheckIn`.`AgentNo`,`tOrderPropertyRole`.`BorrowerFirstName`,`mUsers`.`UserName`,`tOrders`.`LoanNumber`,`tOrders`.`OrderNumber`,`mProjectCustomer`.`ProjectName`, `tOrderAssignments`.`CompleteDateTime` AS Date, tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName,tOrderAssignments.AssignedToUserUID");

}

  function CompletedOrders($post)
	{

    
     $this->selecteOptionQuery();
     //$this->db->select('max(`tOrderAssignments`.`CompleteDateTime`) AS Cdate');
      $this->filterQuery();
      if ($this->RoleType!=1) {

            if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {

              $this->db->group_start();
              $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
              $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
              $this->db->group_end();
            }
              # code...
      }

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


    function GetCompletedOrdersExcelRecords($post)
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
