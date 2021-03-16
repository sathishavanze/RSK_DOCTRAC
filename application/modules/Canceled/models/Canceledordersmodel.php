<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Canceledordersmodel extends MY_Model {
	function __construct()
	{
		parent::__construct();
        $this->loggedid = $this->session->userdata('UserUID');
    $this->UserName = $this->session->userdata('UserName');
    $this->RoleUID = $this->session->userdata('RoleUID');
	}
  function total_count()
  {
    $this->db->select("1");


      /*^^^^^ Get Canceled Orders Query ^^^^^*/
      $this->Common_Model->GetCanceledQueueOrders();
      

      $query = $this->db->count_all_results();
      return $query;
  }
		// MyOrders
	  function count_all()
	  {

      $this->db->select("1");


      /*^^^^^ Get Canceled Orders Query ^^^^^*/
      $this->Common_Model->GetCanceledQueueOrders();
$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);     

      $query = $this->db->count_all_results();
      return $query;
    }

	  function count_filtered($post)
	  {

      $this->Common_Model->DynamicColumnsCommonQuery($WorkflowModuleUID = '',TRUE);
      $this->db->select("1");


      /*^^^^^ Get Canceled Orders Query ^^^^^*/
      $this->Common_Model->GetCanceledQueueOrders();
    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

      // Advanced Search 
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }

      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

      $query = $this->db->get();
      return $query->num_rows();
	  }



  function CanceledOrders($post,$global='') {
      $this->Common_Model->DynamicColumnsCommonQuery($WorkflowModuleUID = '',TRUE);

    if (isset($post['IsDynamicColumns']) && $post['IsDynamicColumns'] == true) {
      $this->db->select("tOrders.OrderUID");
      $this->db->select($post['IsDynamicColumns_Select']);
    } else {
      $this->db->select("tOrders.*,  mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
      $this->db->select('tOrders.LastModifiedDateTime');
    }
		
    /*^^^^^ Get Canceled Orders Query ^^^^^*/
    $this->Common_Model->GetCanceledQueueOrders();
    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search 
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }

    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);

    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


    if ($post['length']!='') {
       $this->db->limit($post['length'], $post['start']);
    }
    $this->db->order_by('OrderEntryDatetime');
    $output = $this->db->get();

    return $output->result();
  
	}


    function GetCanceledOrdersExcelRecords($post)
    {
      $this->Common_Model->DynamicColumnsCommonQuery($WorkflowModuleUID = '',TRUE);
    
      if (isset($post['IsDynamicColumns']) && $post['IsDynamicColumns'] == true) {
        $this->db->select("tOrders.OrderUID, mStatus.StatusColor");
        $this->db->select($post['IsDynamicColumns_Select']);
      } else {
        $this->db->select("tOrders.*,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mMilestone.MilestoneName,mProjectCustomer.ProjectUID,mProducts.ProductName");
        $this->db->select("tOrders.LoanNumber,DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDateTime", FALSE);
      }

    /*^^^^^ Get Canceled Orders Query ^^^^^*/
    $this->Common_Model->GetCanceledQueueOrders();
$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

      // Advanced Search 
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }

    $this->db->order_by('tOrders.OrderNumber');
      $query = $this->db->get();
      return $query->result();  
    }

}
?>
