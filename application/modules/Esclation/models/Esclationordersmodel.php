<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Esclationordersmodel extends MY_Model {
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


      /*^^^^^ Get Esclation Orders Query ^^^^^*/
      $this->Common_Model->GetEsclationOrders();
      

      $query = $this->db->count_all_results();
      return $query;
  }
		// MyOrders
	  function count_all($post)
	  {

      $this->db->select("1");

      // Esclation orders conditiona
      $this->EsclationOrdersConditions($post);

      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);     

      $query = $this->db->count_all_results();
      return $query;
    }

	  function count_filtered($post)
	  {

      $this->db->select("1");      

      // Esclation orders conditiona
      $this->EsclationOrdersConditions($post);

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



  function EsclationOrders($post,$global='') {

    // Workflow Query
    $WorkflowModuleUID = $post['advancedsearch']['WorkflowModuleUID'];
    
    // dynamic column common query
    $post['Section'] = $this->uri->segment(1);
    $this->Common_Model->DynamicColumnsCommonQuery('', FALSE, $post);

    if (isset($post['IsDynamicColumns']) && $post['IsDynamicColumns'] == true) {
      $this->db->select("tOrders.OrderUID");
      $this->db->select($post['IsDynamicColumns_Select']);
    } else {
      $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
      $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
      $this->db->select('tOrders.LastModifiedDateTime');
      $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
    }

    // Esclation orders conditiona
    $this->EsclationOrdersConditions($post);
		
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

  public function EsclationOrdersConditions($post)
  {      
    // Workflow Query
    $WorkflowModuleUID = $post['advancedsearch']['WorkflowModuleUID'];
    $function_call = $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
    $this->Common_Model->{$function_call}(false);

    if (isset($post['advancedsearch']['QueueUID']) && !empty($post['advancedsearch']['QueueUID'])) {
      $QueueUID = $post['advancedsearch']['QueueUID'];
      $this->db->join("tOrderQueues","tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus = 'Pending'");
      $this->db->join("mQueues","tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '".$QueueUID."' and mQueues.WorkflowModuleUID='".$WorkflowModuleUID."'");
      $this->db->join('mUsers b','tOrderQueues.RaisedByUserUID = b.UserUID','left');
      $this->db->join('mReasons','mReasons.ReasonUID = tOrderQueues.RaisedReasonUID','LEFT');

      $this->db->join('tOrderFollowUp','`tOrderFollowUp`.`OrderUID` = `tOrders`.`OrderUID` AND `tOrderFollowUp`.`WorkflowModuleUID` = tOrderFollowUp.WorkflowModuleUID AND `tOrderFollowUp`.`QueueUID` = mQueues.QueueUID  AND tOrderFollowUp.IsCleared = 0','LEFT');

      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);
    }

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($WorkflowModuleUID,'ESCLATIONCOUNT');
  }


    function GetEsclationOrdersExcelRecords($post)
    {
    
      if (isset($post['IsDynamicColumns']) && $post['IsDynamicColumns'] == true) {
        $this->db->select("tOrders.OrderUID, mStatus.StatusColor");
        $this->db->select($post['IsDynamicColumns_Select']);
      } else {
        $this->db->select("*,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mMilestone.MilestoneName,mProjectCustomer.ProjectUID,mProducts.ProductName");
        $this->db->select("tOrders.LoanNumber,DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDateTime", FALSE);
      }

    /*^^^^^ Get Esclation Orders Query ^^^^^*/
    $this->Common_Model->GetEsclationOrders();
    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

      // Advanced Search 
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }

    $this->db->order_by('tOrders.OrderNumber');
      $query = $this->db->get();
      return $query->result();  
    }

    public function RaiseEsclation($post)
    {
      $Type = "Esclation";
      $HighlightUID = $post['HighlightUID'];

      $this->db->trans_begin();

      if(empty($post['HighlightUID'])) {
        $tOrderHighlightsData = array('OrderUID'=>$post['OrderUID'],'Type'=>$Type,'RaisedRemarks'=>$post['RaisedRemarks'], 'RaisedByUserUID'=>$this->loggedid, 'RaisedDateTime'=>date('Y-m-d H:i:s'), 'IsCleared'=>0);
        $this->db->insert('tOrderHighlights',$tOrderHighlightsData);

        if ($this->db->affected_rows() > 0) {
          $Description = "Escalation is Initiated";
          $this->Common_Model->OrderLogsHistory($post['OrderUID'], $Description, Date('Y-m-d H:i:s'));
        }
      } else {
        $tOrderHighlightsData = array('OrderUID'=>$post['OrderUID'],'Type'=>$Type,'ClearedRemarks'=>$post['RaisedRemarks'], 'ClearedByUserUID'=>$this->loggedid, 'ClearedDateTime'=>date('Y-m-d H:i:s'), 'IsCleared'=>1);
        $this->db->where('HighlightUID',$HighlightUID);
        $this->db->update('tOrderHighlights',$tOrderHighlightsData);

        if ($this->db->affected_rows() > 0) {
          $Description = "Escalation is cleared";
          $this->Common_Model->OrderLogsHistory($post['OrderUID'], $Description, Date('Y-m-d H:i:s'));
        }
      }

      if ($this->db->trans_status() === false) {
        $this->db->trans_rollback();
        return false;
      } else {
        $this->db->trans_commit();
        return true;
      }
    }

    // Check Esclation Raise
    public function CheckRaiseEsclation($post)
    {
      $this->db->select('1');
      $this->db->from('tOrderHighlights');
      $this->db->where('OrderUID',$post['OrderUID']);
      $this->db->where('IsCleared',0);
      if ($post['EsclationType'] == "RaiseEsclation") {
        if ($this->db->get()->num_rows() > 0) {
          return "EXISTS";
        }
      } elseif ($post['EsclationType'] == "ClearEsclation") {
        if (isset($post['HighlightUID']) && !empty($post['HighlightUID'])) {
          $this->db->where_in('HighlightUID',$post['HighlightUID']);
          if ($this->db->get()->num_rows() > 0) {
            return FALSE;
          }
        }
        return "NOT EXISTS";
      }
    }

}
?>
