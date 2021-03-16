<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class WorkUp_Orders_Model extends MY_Model {
  
  function __construct()
  { 
    parent::__construct();
    $this->loggedid = $this->session->userdata('UserUID');
    $this->UserName = $this->session->userdata('UserName');
    $this->RoleUID = $this->session->userdata('RoleUID');   
    $this->WorkflowModuleUID = $this->config->item('Workflows')['Workup']; 
    $this->CD_WorkflowModuleUID = $this->config->item('Workflows')['CD'];

  }

  function total_count()
  {
  	$this->db->select("1");


  	/*^^^^^ Get MyOrders Query ^^^^^*/
  	$this->Common_Model->GetWorkUpQueue(false);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'TOTALCOUNT');

  		$query = $this->db->count_all_results();
  		return $query;
  	}


  // MyOrders
    function count_all($post=[])
    {


      $this->db->select("1");


      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);




      //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'NEWCOUNT');
      if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $this->Common_Model->advanced_search($post);
      }


      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
      
      $query = $this->db->count_all_results();
      return $query;
    }


    function count_filtered($post,$module)
    {
      if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
         $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
       }
      $this->db->select("1");

      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

      

      //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'NEWCOUNT');

      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }
      // Advanced Search


      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

      $query = $this->db->get();
      return $query->num_rows();
    }



  function WorkUpOrders($post,$module,$global=''){
    $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
      $this->db->select('tOrders.LastModifiedDateTime');
      $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
      $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '.$this->WorkflowModuleUID.') AS Workupcount',false);

      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);


      //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'NEWCOUNT');

      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

      // Advanced Search
      if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
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


      $MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
      if (!empty($MetricsOrderBy)) {
        $this->db->_protect_identifiers=false;
        $this->db->order_by($MetricsOrderBy);
        $this->db->_protect_identifiers=true;
      }
      $this->db->order_by('OrderEntryDatetime');
      $output = $this->db->get();
      return $output->result();
   }

   function MyOrders($post)
  {
     $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID, mStatus.StatusName,mMilestone.MilestoneName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName AS AssignedUserName");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
      $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '.$this->WorkflowModuleUID.') AS Workupcount',false);

      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);


      if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
        $this->db->where('tOrderAssignments.AssignedToUserUID ='.$this->loggedid,NULL);
      }

$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
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
      $query = $this->db->get();
      return $query->result();  
  }

      function myorders_count_all($post=[])
    {
      $this->db->select("1");


      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);


      if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
        $this->db->where('tOrderAssignments.AssignedToUserUID ='.$this->loggedid,NULL);
      }
      if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $this->Common_Model->advanced_search($post);
      }
      
      
      $query = $this->db->count_all_results();
      return $query;
    }
     function myorders_count_filtered($post)
    {
      if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
         $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
       }
      $this->db->select("1");


      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

      
      if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
        $this->db->where('tOrderAssignments.AssignedToUserUID ='.$this->loggedid,NULL);
      }

      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }
      // Advanced Search


      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

      $query = $this->db->get();
      return $query->num_rows();
    }
    
    // Work In Progress
    function inprogress_count_all($post=[])
    {



      $this->db->select("1");


      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);
      if(isset($post['ReportType']))
         {
          $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'USERASSIGNEDCOUNT','',['UserUID'=>$post['UserUID']]);
         }
         else
         {
      //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'ASSIGNEDCOUNT');
        }
       if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }

      
      
      $query = $this->db->count_all_results();
      return $query;
    }


    function inprogress_count_filtered($post)
    {
      if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
         $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
       }
      $this->db->select("1");


      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);
        if(isset($post['ReportType']))
         {
          $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'USERASSIGNEDCOUNT','',['UserUID'=>$post['UserUID']]);
         }
         else
         {
      //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'ASSIGNEDCOUNT');
          }

      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }
      // Advanced Search


      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

      $query = $this->db->get();
      return $query->num_rows();
    }



  function WorkInProgressOrders($post)
  {

    $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

    $this->db->select('tOrderImport.*, tOrderPropertyRole.*,  tOrderAssignments.AssignedDatetime');

    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID, mStatus.StatusName,mMilestone.MilestoneName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName");
    $this->db->select('tOrders.LastModifiedDateTime,tOrderAssignments.AssignedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
    $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '.$this->WorkflowModuleUID.') AS Workupcount',false);

      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);
      if(isset($post['ReportType']))
         {
          $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'USERASSIGNEDCOUNT','',['UserUID'=>$post['UserUID']]);
         }
         else
         {
      //Order Queue Permission
      $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'ASSIGNEDCOUNT');
        }

      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
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
      $query = $this->db->get();
      return $query->result();  
  }
  // Work in Progress
  function CheckAutoAssignEnabled($id)
  {
      $this->db->select("AutoAssign");
      $this->db->from('mUsers');
      $this->db->where(array('UserUID'=>$id));
      return $this->db->get()->row()->AutoAssign;
  }

  function CheckExistingOrders($id,$ProjectUID,$Workflow)
  {
      $this->db->select("*");
      $this->db->from('tOrderAssignments');
      $this->db->join('tOrders','tOrders.OrderUID = tOrderAssignments.OrderUID','left');
      $this->db->where('tOrderAssignments.ProjectUID',$ProjectUID);
      if($Workflow == 1)
      {
        $this->db->where(array('AssignedToUserUID'=>$id));
      }else{
        $this->db->where(array('QcAssignedToUserUID'=>$id));
      }
      return $this->db->get()->result();
  }

  function GetQcUsers()
  {
      $this->db->select("*");
      $this->db->from('mUsers');
      return $this->db->get()->result();
  }

  function AssignOrders($id,$ProjectUID,$Workflow)
  {
      $this->db->select("OrderUID");
      $this->db->from('tOrderAssignments');
      if($Workflow == 1)
      {
        $this->db->where('AssignedToUserUID !=',NULL);
      }else{
        $this->db->where('QcAssignedToUserUID !=',NULL);
      }
      $this->db->where('tOrderAssignments.ProjectUID',$ProjectUID);
      $query = $this->db->get()->result_array();

      $OrderUID = [];
      foreach ($query as $key => $value) {
        $OrderUID[$key] = $value['OrderUID'];
      }
      if(sizeof($OrderUID) == 0)
      {
        $OrderUID = '0';
      }

      $status[0] = $this->config->item('keywords')['New Order'];
      $status[1] = $this->config->item('keywords')['Waiting For Images'];
      $status[2] = $this->config->item('keywords')['Image Received'];
      $status[21] = $this->config->item('keywords')['MyOrders'];

      
      $this->db->select("*");
      $this->db->from('tOrders');
      $this->db->where(array('ProjectUID'=>$ProjectUID));
      $this->db->where_in('tOrders.StatusUID', $status);
      $this->db->where_not_in('tOrders.OrderUID', $OrderUID);
      $this->db->limit(1);
      $query = $this->db->get();
      $queryassign = $query->row();
      if($query->num_rows() > 0)
      {
         if($this->CheckExistingAssignmentOrders($queryassign->OrderUID) == 0)
         {  
            $tOrderAssignmentsArray = array(
              'OrderUID' => $queryassign->OrderUID, 
              'ProjectUID' => $queryassign->ProjectUID,
              'AssignedToUserUID' => $this->loggedid,
              'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
              'AssignedByUserUID' => $this->loggedid,
              'QcAssignedDateTime' => Date('Y-m-d H:i:s', strtotime("now")),
              'QcAssignedToUserUID' => $this->loggedid,
              'QcAssignedByUserUID' => $this->loggedid
            );
            $query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);
            if($this->db->affected_rows() > 0)
            {
              return 1;
            }
         }
         else
         {
            $tOrderAssignmentsArray = array(
              'AssignedToUserUID' => $this->loggedid,
              'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
              'AssignedByUserUID' => $this->loggedid,
              'QcAssignedDateTime' => Date('Y-m-d H:i:s', strtotime("now")),
              'QcAssignedToUserUID' => $this->loggedid,
              'QcAssignedByUserUID' => $this->loggedid
            );
            $this->db->where(array('OrderUID' => $queryassign->OrderUID,'ProjectUID'=>$queryassign->ProjectUID));
            $query = $this->db->update('tOrderAssignment', $tOrderAssignmentArray);
              return 1;
         }
      }else{

          return 0;

      }
  }

    function CheckExistingAssignmentOrders($OrderUID)
    {
      $this->db->select('*');
      $this->db->from('tOrderAssignments');
      $this->db->where(array('OrderUID'=>$OrderUID));
      $query = $this->db->get();
      if($query->num_rows() > 0)
      {
        return 1;
      }else{
        return 0;
      }

    }


    function PickExistingOrderCheck($OrderUID)
    {
        $this->db->select('*');
        $this->db->from('tOrderAssignments');
        $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','left');
        $this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID, 'tOrderAssignments.WorkflowModuleUID'=>$this->config->item('Workflows')['Stacking']));
        $this->db->where('AssignedToUserUID !=',NULL);
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
          return array('Status'=>1,'UserName'=>$query->row()->UserName);
        }else{
          return array('Status'=>0,'UserName'=>'');
        }
    }

    function OrderAssign($OrderUID,$ProjectUID)
    {

        $this->db->select('*');
        $this->db->from('tOrderAssignments');
        $this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID, 'tOrderAssignments.WorkflowModuleUID'=>$this->config->item('Workflows')['Stacking']));
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
          $tOrderAssignmentsArray = array(
              'WorkflowModuleUID' => $this->config->item('Workflows')['Stacking'],
              'AssignedToUserUID' => $this->loggedid,
              'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
              'AssignedByUserUID' => $this->loggedid,
              'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress'],
            );
            $this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID));
            $query = $this->db->update('tOrderAssignments', $tOrderAssignmentsArray);
          // Update Status to Stacking in Progress
            $this->Common_Model->save('tOrders',['StatusUID'=>$this->config->item('keywords')['Stacking In Progress']], ['OrderUID'=>$OrderUID]);
            return 1;
          }
        else
        {
            $tOrderAssignmentsArray = array(
              'OrderUID' => $OrderUID, 
              'WorkflowModuleUID' => $this->config->item('Workflows')['Stacking'],
              'AssignedToUserUID' => $this->loggedid,
              'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
              'AssignedByUserUID' => $this->loggedid,
              'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress']
            );
            $query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);
          // Update Status to Stacking in Progress
            $this->Common_Model->save('tOrders',['StatusUID'=>$this->config->item('keywords')['Stacking In Progress']], ['OrderUID'=>$OrderUID]);
            return 1;
          }

    }

    function GetMyOrdersExcelRecords($post)
    {

      $this->db->select("*,tOrders.OrderUID,tOrders.LoanNumber,mStatus.StatusName,mStatus.StatusColor,mMilestone.MilestoneName,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
      $this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);


      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetWorkUpQueue();
      $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);


      if($post['advancedsearch']['Status'] == 'workinprogress' && in_array($this->RoleType, $this->config->item('Internal Roles'))){

       $this->db->where('tOrderAssignments.AssignedToUserUID IS NOT NULL');

     }
     else if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
      
      $this->db->group_start();
      $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
      $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
      $this->db->group_end();
    }
    

      if ($post['advancedsearch']!='false' && sizeof(array_filter($post['advancedsearch']))!=0) 
      {
          $filter = $this->Common_Model->advanced_search($post); 
      }

      $this->db->order_by('OrderEntryDatetime');
      $query = $this->db->get();
      return $query->result();  
    }
    //parking orders

  function parkingorders_count_all($post=[])
  {


    $this->db->select("1");


    /*^^^^^ Get MyOrders Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(false);
    $this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->WorkflowModuleUID."' AND tOrderParking.IsCleared = 0 ");
    $this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'PARKINGCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    $query = $this->db->count_all_results();
    return $query;
  }  

  function parkingorders($post)
  {
     $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName AS AssignedUserName, b.UserName AS RaisedBy,tOrderParking.Remarks");
    $this->db->select("tOrderParking.Remainder");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
    $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '.$this->WorkflowModuleUID.') AS Workupcount',false);

    /*^^^^^ Get MyOrders Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(false);

    $this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->WorkflowModuleUID."' AND tOrderParking.IsCleared = 0 ");
    $this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'PARKINGCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
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
    $query = $this->db->get();
    return $query->result();  
  }

  function parkingorders_count_filtered($post)
  {
    if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
       $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
     }
    $this->db->select("1");


    /*^^^^^ Get MyOrders Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(false);
    $this->db->join("tOrderParking","tOrderParking.OrderUID=tOrders.OrderUID and tOrderParking.WorkflowModuleUID='".$this->WorkflowModuleUID."' AND tOrderParking.IsCleared = 0 ");
    $this->db->join('mUsers b','tOrderParking.RaisedByUserUID = b.UserUID','left');

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'PARKINGCOUNT');
    
      // Advanced Search
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
      // Advanced Search


      // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    $query = $this->db->get();
    return $query->num_rows();
  }

  // CD Section
  function CDInflowOrders_CountAll($post=[])
  {
    $this->db->select("1");

    /*^^^^^ Get Query ^^^^^*/
    $this->Common_Model->GetCDOrders(FALSE);

    // Sub Queue Pending Not Exists
    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderSubQueues WHERE tOrderSubQueues.OrderUID = tOrders.OrderUID AND tOrderSubQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderSubQueues.SubQueueStatus = 'Pending')", NULL, FALSE);

       //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'EXCLUDEOTHERSASSIGNEDCOUNT');
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    $query = $this->db->count_all_results();
    return $query;
  }

  function CDInflowOrders_CountFiltered($post,$module)
  {
    if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
      $this->Common_Model->DynamicColumnsCommonQuery($this->CD_WorkflowModuleUID);
    }

    $this->db->select("1");

    /*^^^^^ Get Query ^^^^^*/
    $this->Common_Model->GetCDOrders(FALSE);

    // Sub Queue Pending Not Exists
    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderSubQueues WHERE tOrderSubQueues.OrderUID = tOrders.OrderUID AND tOrderSubQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderSubQueues.SubQueueStatus = 'Pending')", NULL, FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'EXCLUDEOTHERSASSIGNEDCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
    // Advanced Search

    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);

    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    $query = $this->db->get();
    return $query->num_rows();
  }

  function CDInflowOrders($post){
    $this->Common_Model->DynamicColumnsCommonQuery($this->CD_WorkflowModuleUID);
    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
    $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '.$this->CD_WorkflowModuleUID.') AS Workupcount',false);

    /*^^^^^ Get Query ^^^^^*/
    $this->Common_Model->GetCDOrders(FALSE);

    // Sub Queue Pending Not Exists
    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderSubQueues WHERE tOrderSubQueues.OrderUID = tOrders.OrderUID AND tOrderSubQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderSubQueues.SubQueueStatus = 'Pending')", NULL, FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'EXCLUDEOTHERSASSIGNEDCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
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

    $MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->config->item("Workflows")["CD"]);
    if (!empty($MetricsOrderBy)) {
      $this->db->_protect_identifiers=false;
      $this->db->order_by($MetricsOrderBy);
      $this->db->_protect_identifiers=true;
    }
    $this->db->order_by('OrderEntryDatetime');
    $output = $this->db->get();
    return $output->result();
  }

  function CDPendingOrders_CountAll($post=[])
  {
    $this->db->select("1");

    /*^^^^^ Get Query ^^^^^*/
    $this->Common_Model->GetCDOrders(FALSE);

    // Sub Queue Pending Exists
    $this->db->where("EXISTS (SELECT 1 FROM tOrderSubQueues WHERE tOrderSubQueues.OrderUID = tOrders.OrderUID AND tOrderSubQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderSubQueues.SubQueueStatus = 'Pending')", NULL, FALSE);

       //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'EXCLUDEOTHERSASSIGNEDCOUNT');
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    $query = $this->db->count_all_results();
    return $query;
  }


  function CDPendingOrders_CountFiltered($post,$module)
  {
    if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
      $this->Common_Model->DynamicColumnsCommonQuery($this->CD_WorkflowModuleUID);
    }

    $this->db->select("1");

    /*^^^^^ Get Query ^^^^^*/
    $this->Common_Model->GetCDOrders(FALSE);

    // Sub Queue Pending Exists
    $this->db->where("EXISTS (SELECT 1 FROM tOrderSubQueues WHERE tOrderSubQueues.OrderUID = tOrders.OrderUID AND tOrderSubQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderSubQueues.SubQueueStatus = 'Pending')", NULL, FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'EXCLUDEOTHERSASSIGNEDCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
    // Advanced Search

    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);

    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    $query = $this->db->get();
    return $query->num_rows();
  }

  function CDPendingOrders($post){
    $this->Common_Model->DynamicColumnsCommonQuery($this->CD_WorkflowModuleUID);
    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
    $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND tOrderAssignmentsHistory.WorkflowModuleUID = '.$this->CD_WorkflowModuleUID.') AS Workupcount',false);

    /*^^^^^ Get Query ^^^^^*/
    $this->Common_Model->GetCDOrders(FALSE);

    // Sub Queue Pending Exists
    $this->db->where("EXISTS (SELECT 1 FROM tOrderSubQueues WHERE tOrderSubQueues.OrderUID = tOrders.OrderUID AND tOrderSubQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderSubQueues.SubQueueStatus = 'Pending')", NULL, FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'EXCLUDEOTHERSASSIGNEDCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
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

    $MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->config->item("Workflows")["CD"]);
    if (!empty($MetricsOrderBy)) {
      $this->db->_protect_identifiers=false;
      $this->db->order_by($MetricsOrderBy);
      $this->db->_protect_identifiers=true;
    }
    $this->db->order_by('OrderEntryDatetime');
    $output = $this->db->get();
    return $output->result();
  }

  function CDCompletedOrders_CountAll($post=[])
  {
    $this->db->select("1");

    /*^^^^^ Get Query ^^^^^*/
    $this->CDCompletedOrdersConditions();

       //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'COMPLETEDCOUNT');
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    $query = $this->db->count_all_results();
    return $query;
  }


  function CDCompletedOrders_CountFiltered($post,$module)
  {
    if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
      $this->Common_Model->DynamicColumnsCommonQuery($this->CD_WorkflowModuleUID);
    }

    $this->db->select("1");

    /*^^^^^ Get Query ^^^^^*/
    $this->CDCompletedOrdersConditions();

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'COMPLETEDCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
    // Advanced Search

    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);

    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    $query = $this->db->get();
    return $query->num_rows();
  }

  function CDCompletedOrders($post){
    $this->Common_Model->DynamicColumnsCommonQuery($this->CD_WorkflowModuleUID, TRUE);
    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
    $this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND tOrderAssignmentsHistory.WorkflowModuleUID = '.$this->CD_WorkflowModuleUID.') AS Workupcount',false);
    $this->db->select('mUsers.UserName as completedby, tOrderAssignments.CompleteDateTime as completeddatetime');

    /*^^^^^ Get Query ^^^^^*/
    $this->CDCompletedOrdersConditions();

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->config->item("Workflows")["CD"],'COMPLETEDCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
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

    $MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->config->item("Workflows")["CD"]);
    if (!empty($MetricsOrderBy)) {
      $this->db->_protect_identifiers=false;
      $this->db->order_by($MetricsOrderBy);
      $this->db->_protect_identifiers=true;
    }
    $this->db->order_by('OrderEntryDatetime');
    $output = $this->db->get();
    return $output->result();
  }

  function CDCompletedOrdersConditions()
  {
    $this->db->from('tOrders');
    $this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->CD_WorkflowModuleUID . '"', 'left');
    $this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
    $this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
    $this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
    $this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
    $this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
    $this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->CD_WorkflowModuleUID . '"');
    $this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');
    $this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

    $this->Common_Model->AllWorkflowQueue_CommonQuery($this->config->item('Workflows')['CD'], TRUE);

    $this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

    $this->db->where('tOrderAssignments.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
    $this->db->where('tOrderAssignments.WorkflowModuleUID', $this->CD_WorkflowModuleUID);
    $this->db->where('tOrderWorkflows.WorkflowModuleUID', $this->CD_WorkflowModuleUID);

    if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
      $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
    }
  }
  // CD Section End

  // KickBack Orders
  function KickBackcount_all($post=[])
  {


    $this->db->select("1");


    /*^^^^^ Get Workup Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(true, ['IsKickBack'=>true]);

    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');

    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }
    

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    $query = $this->db->count_all_results();
    return $query;
  }


  function KickBackcount_filtered($post,$module)
  {
    if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
      $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
    }
    $this->db->select("1");


    /*^^^^^ Get Workup Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(true, ['IsKickBack'=>true]);

    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
    // Advanced Search


    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);

    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    $query = $this->db->get();
    return $query->num_rows();
  }



  function KickBackWorkupOrders($post,$module,$global='')
  {
    $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime,tOrderWorkflows.ReversedRemarks,tOrderWorkflows.ReversedDateTime');
    $this->db->select('tOrderWorkflows.KickBackDateTime AS ReversedDateTime, mUsersKickBack.UserName AS KickbackByUserName, "" AS ReversedRemarks');
    /*^^^^^ Get MyOrders Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(true, ['IsKickBack'=>true]);

    // KickBack User Name
    $this->db->join('mUsers AS mUsersKickBack','mUsersKickBack.UserUID = tOrderWorkflows.KickBackUserUID','left');

    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');
    
    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
    if ($post['length']!='') {
      $this->db->limit($post['length'], $post['start']);
    }
    $MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
    if (!empty($MetricsOrderBy)) {
      $this->db->_protect_identifiers=false;
      $this->db->order_by($MetricsOrderBy);
      $this->db->_protect_identifiers=true;
    }

    $this->db->order_by('OrderEntryDatetime');
    $output = $this->db->get();
    return $output->result();

  }

  // WorkupRework Orders
  function WorkupRework_CountAll($post=[])
  {

    $this->db->select("1");

    /*^^^^^ Get Workup Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(true, ['SkipCondition'=>true, 'SkipDependentWorkflowCompleteCond'=>true, 'IsRework'=>true]);

    // $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');

    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }    

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    $query = $this->db->count_all_results();
    return $query;
  }


  function WorkupReworkCount_Filtered($post,$module)
  {
    if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
      $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
    }

    $this->db->select("1");

    /*^^^^^ Get Workup Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(true, ['SkipCondition'=>true, 'SkipDependentWorkflowCompleteCond'=>true, 'IsRework'=>true]);

    // $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    // Advanced Search
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
    // Advanced Search

    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);

    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    $query = $this->db->get();
    return $query->num_rows();
  }

  function WorkupReworkOrders($post,$module,$global='')
  {
    $this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);

    $this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
    $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime,tOrderWorkflows.ReversedRemarks,tOrderWorkflows.ReversedDateTime');
    /*^^^^^ Get MyOrders Query ^^^^^*/
    $this->Common_Model->GetWorkUpQueue(true, ['SkipCondition'=>true, 'SkipDependentWorkflowCompleteCond'=>true, 'IsRework'=>true]);

    // $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

    //Order Queue Permission
    $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'KICKBACKCOUNT');
    
    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
    if ($post['length']!='') {
      $this->db->limit($post['length'], $post['start']);
    }
    $MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $this->WorkflowModuleUID);
    if (!empty($MetricsOrderBy)) {
      $this->db->_protect_identifiers=false;
      $this->db->order_by($MetricsOrderBy);
      $this->db->_protect_identifiers=true;
    }

    $this->db->order_by('OrderEntryDatetime');
    $output = $this->db->get();
    return $output->result();

  }
  
}?>
