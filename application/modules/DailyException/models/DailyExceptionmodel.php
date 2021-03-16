<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DailyExceptionmodel extends MY_Model 
{
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
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');       
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProductUID','left');
      $this->db->join('mProducts','mProducts.ProductUID = tOrders.ProductUID','left');
      $this->db->join('tOrderException','tOrderException.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mUsers','mUsers.UserUID = tOrderException.ExceptionRaisedByUserUID','left');
      $this->db->join('mCustomer','mCustomer.CustomerUID = tOrders.CustomerUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','left');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mExceptions','mExceptions.ExceptionUID = tOrderException.ExceptionTypeUID','left');       
      $this->db->where('tOrderException.IsExceptionCleared', 0); 
      $this->db->where_not_in('tOrders.StatusUID', $status); 
      if ($this->RoleUID!=1) {
      $this->db->where('`tOrderException`.`ExceptionRaisedByUserUID`',$this->loggedid);
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
        $this->db->where('DATE(tOrderException.ExceptionRaisedDateTime ) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
      }
      if($post['advancedsearch']['ToDate']){
        $this->db->where('DATE(tOrderException.ExceptionRaisedDateTime ) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
      }
      return true;
    }

    function selecteOptionQuery()
    {

      $this->db->select("*, `tOrderException`.`ExceptionRaisedDateTime` AS RisedDate , `tOrderException`.`ExceptionClearedDateTime` AS ClearDate,`tOrderDocumentCheckIn`.`SettlementAgentName`,`tOrderDocumentCheckIn`.`AgentNo`,`tOrderPropertyRole`.`BorrowerFirstName`,`mUsers`.`UserName`,`tOrders`.`LoanNumber`,`tOrders`.`OrderNumber`,`mProjectCustomer`.`ProjectName`, tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
    }

    function FetchDataExceptionModel($post)
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
      $this->db->order_by('tOrderException.ExceptionRaisedDateTime','DESC');
      $query = $this->db->get();
      return $query->result();
    }


    function GetExceptionExcelRecords($post)
    {
      $this->selecteOptionQuery();    
      $this->filterQuery();          
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->advanced_search($post);
      }
      $this->db->order_by('tOrderException.ExceptionRaisedDateTime','DESC');
      $query = $this->db->get();
      return $query->result();  
    }

    }
    ?>
