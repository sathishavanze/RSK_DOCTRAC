<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Escalation_Orders_model extends MY_Model {
	function __construct()
	{
		parent::__construct();
        $this->loggedid = $this->session->userdata('UserUID');
    $this->UserName = $this->session->userdata('UserName');
    $this->RoleUID = $this->session->userdata('RoleUID');
	}

		// MyOrders
	  function count_all()
	  {
      $this->db->select("1");

     $this->Common_Model->GetEsclationQueue();

     $query = $this->db->count_all_results();
     return $query;
   }

	  function count_filtered($post)
	  {
      $this->db->select("1");
      $this->Common_Model->GetEsclationQueue();
      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }

      $this->Common_Model->WorkflowQueues_Datatable_Search($post);


      $query = $this->db->get();
      return $query->num_rows();
	  }



    function GetEsclationQueue($post,$global='') {

      $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName,tOrderEsclation.WorkflowModuleUID");
      $this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);
      $this->db->select('DATE_FORMAT(tOrders.LastModifiedDateTime, "%m/%d/%Y %H:%i:%s") As LastModifiedDateTime', false);
      $this->db->select('DATE_FORMAT(tOrders.OrderDueDate, "%m/%d/%Y %H:%i:%s") As OrderDueDate', false);

      /*^^^^^ Cancelled Orders Query ^^^^*/

      $this->Common_Model->GetEsclationQueue();

      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
      /*+++++ Advanced Search +++++*/
      if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }

      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

      if ($post['length']!='') {
        $this->db->limit($post['length'], $post['start']);
      }
      $this->db->order_by('OrderEntryDatetime');
      $output = $this->db->get();
      return $output->result();
    }

    function GetGetEsclationQueueExcelRecords($post)
    {


      /*^^^^^ Cancelled Orders Query ^^^^*/

      $this->Common_Model->GetEsclationQueue();

$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }

      $this->db->order_by('tOrders.OrderNumber');
      $query = $this->db->get();
      return $query->result();
    }

}
?>
