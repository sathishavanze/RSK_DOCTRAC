<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Followup_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

  function Pending_count_all()
  {
     $this->db->select('*,tOrders.OrderNumber','tOrders.LoanNumber','tOrderPackage.PackageNumber');
     $this->db->from('tOrderFollowUp');
     $this->db->join('tOrders','tOrderFollowUp.OrderUID=tOrders.OrderUID','left');
     $this->db->join('tOrderPackage','tOrders.PackageUID=tOrderPackage.PackageUID','left');
    //filter the pending and completed orders
        $this->db->where('FollowUpStatus','Pending');
    $query = $this->db->count_all_results();
    return $query;
  }

  function Completed_count_all()
  {
     $this->db->select('*,tOrders.OrderNumber','tOrders.LoanNumber','tOrderPackage.PackageNumber');
     $this->db->from('tOrderFollowUp');
     $this->db->join('tOrders','tOrderFollowUp.OrderUID=tOrders.OrderUID','left');
     $this->db->join('tOrderPackage','tOrders.PackageUID=tOrderPackage.PackageUID','left');
    //filter the pending and completed orders
     $this->db->where('FollowUpStatus','completed');
    $query = $this->db->count_all_results();
    return $query;
  }

     function count_filtered($post)
    {

     $this->db->select('*,tOrders.OrderNumber','tOrders.LoanNumber','tOrderPackage.PackageNumber');
     $this->db->from('tOrderFollowUp');
     $this->db->join('tOrders','tOrderFollowUp.OrderUID=tOrders.OrderUID','left');
     $this->db->join('tOrderPackage','tOrders.PackageUID=tOrderPackage.PackageUID','left');

      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }
      // Advanced Search


      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
       //filter the pending and completed orders
      $this->db->where('FollowUpStatus',$post['filter']);
      $this->db->order_by('OrderEntryDatetime');
      $query = $this->db->get();
      return $query->num_rows();
    }
	 function GetFollowupOrders($post)
  {
     $this->db->select('*,tOrders.OrderNumber','tOrders.LoanNumber','tOrderPackage.PackageNumber');
     $this->db->from('tOrderFollowUp');
     $this->db->join('tOrders','tOrderFollowUp.OrderUID=tOrders.OrderUID','left');
     $this->db->join('tOrderPackage','tOrders.PackageUID=tOrderPackage.PackageUID','left');

      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }
      // Advanced Search


      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
      //filter the pending and completed orders
        $this->db->where('FollowUpStatus',$post['filter']);

      $this->db->order_by('FollowUpUID',"DESC");
      $query = $this->db->get();
      return $query->result();  
  }

	  function GetMyOrdersExcelRecords($post)
	  {
	   
     $this->db->select('*,tOrders.OrderNumber','tOrders.LoanNumber','tOrderPackage.PackageNumber');
     $this->db->from('tOrderFollowUp');
     $this->db->join('tOrders','tOrderFollowUp.OrderUID=tOrders.OrderUID','left');
     $this->db->join('tOrderPackage','tOrders.PackageUID=tOrderPackage.PackageUID','left');
	  

      if ($post['advancedsearch']!='false' && sizeof(array_filter($post['advancedsearch']))!=0) 
      {
          $filter = $this->Common_Model->advanced_search($post); 
      }
       $this->db->where('FollowUpStatus',$post['advancedsearch']['filterlist']);
      $this->db->order_by('tOrders.OrderNumber');
      $query = $this->db->get();
      return $query->result();  
    }
    function Update_Complete_Followup($OrderUID,$Followup_Details)
    {
    
      //$this->db->where('FollowUpUID',$FollowUpUID);
      $where_data=array('OrderUID'=>$OrderUID,'FollowUpStatus'=>'Pending');
      $result=$this->db->select('max(FollowUpUID) as FollowUpUID')->from('tOrderFollowUp')->where($where_data)->get()->row();
      $this->db->update('tOrderFollowUp',$Followup_Details,'FollowUpUID='.$result->FollowUpUID);
      $this->db->update('tOrders',array('IsFollowUp'=>0),'OrderUID='.$OrderUID);
      if ($this->db->affected_rows() > 0)
      {
        /*INSERT ORDER LOGS BEGIN*/
        $this->Common_Model->OrderLogsHistory($OrderUID,'Order Followup Completed',Date('Y-m-d H:i:s'));
        /*INSERT ORDER LOGS END*/
        return TRUE;
      }
      else
      {
        return FALSE;
      }
     
    }
    function getFollowupUser($UserUID)
    {
      return $this->db->select('UserName')->from('mUsers')->where('UserUID',$UserUID)->get()->row();
    }
}
?>