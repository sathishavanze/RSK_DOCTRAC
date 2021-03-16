<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard_Model extends MY_Model {

	function __construct()
	{
		parent::__construct();
    $this->loggedid = $this->session->userdata('UserUID');
    $this->UserName = $this->session->userdata('UserName');
    $this->RoleUID = $this->session->userdata('RoleUID');
    $this->otherdb = $this->load->database('otherdb', TRUE);

  }


  function workflow_bind()
  {
    $query = $this->db->get("mWorkFlowModules");
    return $query->result();
  }



  /**
  * Function for count 
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 19-10-2020
  **/
  function CountWhere($post,$date)
  {
    $CustomerUID = $this->parameters['DefaultClientUID'];
    $CountWhere = 'tOrders.CustomerUID ='.$CustomerUID;
    if(!empty($date))
    {
      if($post['WorkflowModuleUID'] == 1)
      {
        $CountWhere .= " AND ((tOrderAssignments.WorkflowStatus = 5 AND DATE(tOrderAssignments.CompleteDateTime) = '".$date."')  OR (DATE(tOrderQueues.RaisedDateTime)='".$date."'))";
      }
      else
      {
        $CountWhere .= " AND DATE(tOrderAssignments.CompleteDateTime) = '".$date."'"; 
      }
    }
    if(!empty($post['WorkflowModuleUID']))
    {
      $CountWhere .= " AND tOrderAssignments.WorkflowModuleUID = ".$post['WorkflowModuleUID'];
    }
    return $CountWhere;
  }


  /**
  * Function for Get report 
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 19-10-2020
  **/
  function getProductivityReportCounts($post)
  {
    $CustomerUID = $this->parameters['DefaultClientUID'];

    $Type = $post['Type'];
    $userid = ($post['Type']=='Individual') ? "AND tOrderAssignments.CompletedByUserUID=".$post['userid']." " : '' ;
    $FromDate = $post['FromDate'];
    $ToDate = $post['ToDate'];
    $datearray = $this->getDates($FromDate, $ToDate);
    
    $Que_join="";
    $WorkflowStatus="tOrderAssignments.WorkflowStatus = 5 AND";
    if($post['WorkflowModuleUID'] == 1)
    {
      $Que_join.=" LEFT JOIN tOrderQueues ON tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus= 'Pending' JOIN mQueues ON tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '15' and mQueues.WorkflowModuleUID='1'";
      $WorkflowStatus="";
    }
    //date wise loop
    $resultdata = [];
    foreach ($datearray as $key => $date)
    {
      $CountWhere = $this->CountWhere($post,$date);
      $this->db->select("('".date('d-M',strtotime($date))."') as date");
      
      $this->db->select("(SELECT count( DISTINCT
        tOrders.OrderUID)
        FROM
        tOrders
        LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
        WHERE ".$WorkflowStatus."  ".$CountWhere." ".$userid."
      ) as process");

      $this->db->select("(SELECT group_concat('',tOrders.OrderUID)
        FROM
        tOrders
        LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
        WHERE ".$WorkflowStatus." ".$CountWhere." ".$userid."
      ) as processOrderUID");
      
      $resultdata[] = $this->db->get()->result();
      
    }

    $CountTotalWhere = $this->CountTotalWhere($post,$datearray);
    $this->db->select("('Total') as date");
    $this->db->select("(SELECT count( DISTINCT
      tOrders.OrderUID)
      FROM
      tOrders
      LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
      WHERE 
      ".$CountTotalWhere."  ".$userid."
    ) as process");

    $this->db->select("(SELECT group_concat('',tOrders.OrderUID)
      FROM
      tOrders
      LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID ".$Que_join."
      WHERE
      ".$CountTotalWhere."  ".$userid."
    ) as processOrderUID");


    $resultdata[] = $this->db->get()->result();
    

    return $resultdata;
  }


  /**
  * Function for Get date 
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 19-10-2020
  **/
  function getDates($date1, $date2) 
  {
    $format = 'Y-m-d';
    $dates = array();
    $current = strtotime($date1);
    $date2 = strtotime($date2);
    $stepVal = '+1 day';
    while( $current <= $date2 ) {
      $dates[] = date($format, $current);
      $current = strtotime($stepVal, $current);
    }
    return $dates;
  }


  /**
  * Function for get where
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 19-10-2020
  **/
  function CountTotalWhere($post,$datearray)
  {
    $CustomerUID = $this->parameters['DefaultClientUID'];
    $totalwhere = ' tOrders.CustomerUID ='.$CustomerUID;
    if(!empty($post['WorkflowModuleUID']))
    {
      $totalwhere .= " AND tOrderAssignments.WorkflowModuleUID = ".$post['WorkflowModuleUID'];
    }
    if(!empty($datearray))
    {

      if($post['WorkflowModuleUID'] == 1)
      {
        $totalwhere .= " AND ((tOrderAssignments.WorkflowStatus = 5 AND DATE(tOrderAssignments.CompleteDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."')  OR (DATE(tOrderQueues.RaisedDateTime) BETWEEN '".$datearray[0]."' AND '".end($datearray)."'))";

      }
      else
      {
        $totalwhere .= " AND DATE(tOrderAssignments.CompleteDateTime) BETWEEN '".$datearray[0]."' AND 
        '".end($datearray)."'";
      }

    }
    return $totalwhere;
  }

  /**
  * Function for get where
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 19-10-2020
  **/
  function GetUsersByWorkflow($WorkflowModuleUID){
    $this->db->select("mUsers.`UserUID`,mUsers.`UserName`,mUsers.`RoleUID`,mResources.WorkflowModuleUID,mRoleResources.ResourceUID,mResources.CustomerUID,mResources.FieldName,mGetNextOrderUserPermissions.PermissionUserUID,mGetNextOrderUserPermissions.LoanTypeUIDs");
    $this->db->from('mUsers');
    $this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID','inner');
    $this->db->join('mRoleResources','mUsers.RoleUID=mRoleResources.RoleUID','left');
    $this->db->join('mResources','mResources.ResourceUID=mRoleResources.ResourceUID','left');
    $this->db->join('mGetNextOrderUserPermissions','mGetNextOrderUserPermissions.PermissionUserUID = mUsers.UserUID','left');
    $this->db->where('mUsers.CustomerUID',$this->parameters['DefaultClientUID']);

    $this->db->where('mResources.WorkflowModuleUID',$WorkflowModuleUID);
    $this->db->where('mResources.FieldSection','ORDERWORKFLOW');
    $this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", mResources.CustomerUID)",NULL, FALSE);
    
    $this->db->where_in('mRole.RoleTypeUID',array_merge($this->config->item('SuperAccess'),$this->config->item('AgentAccess')));

    //$this->db->where('mUsers.UserUID !=',$this->session->userdata('UserUID'));
    $this->db->where('mUsers.Active ',1);
    $this->db->group_by("mUsers.UserUID");
    return $this->db->get()->result();
    //print_r($this->db->last_query());exit;
  }

  /**
  * Function for get individual completed order
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 21-10-2020
  **/
  function GetcompletedOrder($post){
    $getindividual=$this->GetUsersByWorkflow($post['WorkflowModuleUID']);
    foreach($getindividual as $row){
      $post['userid']=$row->UserUID;
      $getData=$this->getProductivityReportCounts($post);
      $count=0;
      foreach ($getData as $key => $value) {
        foreach ($value as $keyCount => $valueCount) {
          $count +=($valueCount->process);
        }
      }
      $data[$row->UserUID]=$count;
    }
    return ($data);
  }

  /**
  * Function for get Targer
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 21-10-2020
  **/
  function getTarget($WorkflowModuleUID){
    $this->db->select("Target");
    $this->db->from('mProductivityTarget');
    $this->db->where(array('CustomerUID'=>$this->parameters['DefaultClientUID'],'WorkflowUID'=>$WorkflowModuleUID));
    return $this->db->get()->row();
  }

  /**
  * Function for get Process
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 21-10-2020
  **/
  function getProcessUsers($UserUID)
  {
    $this->db->select('mUsers.UserName,mUsers.UserUID, mUsers.LoginID');
    $this->db->from('mUsers');
    $this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);//Added BY harini to get the users based on client 
    $this->db->where('mUsers.Active',1);
    if(!empty($UserUID))
    {
      for ($i=0; $i < count($UserUID); $i++) { 
        $User .= $UserUID[$i].',';
      }
      $User = trim($User,',');
      $this->db->where('mUsers.UserUID in ('.$User.')');
    }
    $this->db->group_by('mUsers.UserUID');
    // $this->db->limit(3);
    return $this->db->get()->result();
  }


  /**
  *Function get Queue Progress counts 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Thursday 22 October 2020.
  */
  function workflow_completedcondition_filtersql($WorkflowModuleUID,$FromDate,$ToDate)
  {
    $condition_orders_sql = false;
    
    //condition for workup
    if($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] ) {
      $WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');
      $this->db->select('to.OrderUID');
      $this->db->from('tOrders to');
      $this->db->join('tOrderWorkflows tw','tw.OrderUID = to.OrderUID AND tw.WorkflowModuleUID = '.$WorkflowModuleUID.'');
      $this->db->join('tOrderAssignments ta','ta.OrderUID = to.OrderUID AND ta.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
      $this->db->where('(tw.IsCountDisabled <> 1 OR tw.IsCountDisabled IS NULL)',NULL, FALSE);

      $this->db->group_start();
      $this->db->group_start();

      foreach ($WorkupSubQueueComplete as $key => $QueueUID) {
        //self completed count
        $SELFWHERE = '';
        /*if($by == 'self') {
          $SELFWHERE = ' AND (tOrderQueues.RaisedByUserUID = '.$this->loggedid.')';
        }*/   

        if ($key === 0) {
          $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = ta.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending' AND tOrderQueues.RaisedDateTime BETWEEN '".$FromDate. "' AND '".$ToDate."' ".$SELFWHERE.")",NULL,FALSE);
        } else {
          $this->db->or_where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = ta.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending' AND tOrderQueues.RaisedDateTime BETWEEN '".$FromDate. "' AND '".$ToDate."' ".$SELFWHERE.")",NULL,FALSE);
        }
      }
      $this->db->group_end();
      $this->db->or_group_start();
      $this->db->where('ta.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
      //$this->db->where('ta.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
      // //self completed count
      /*if($by == 'self') {
        $this->db->where('ta.CompletedByUserUID', $this->loggedid);
      }*/

      $this->db->group_end();
      $this->db->group_end();

      //Skip Users
      $SkippedUsers = $this->config->item('ReportSkippedUsers');
      if (!empty($SkippedUsers)) {

        $this->db->group_start();
        $this->db->where_not_in('ta.CompletedByUserUID',$SkippedUsers);
        $this->db->or_where('ta.CompletedByUserUID IS NULL',NULL,FALSE);
        $this->db->group_end();
      }
      $condition_orders_sql = $this->db->get_compiled_select();

    }

    if($WorkflowModuleUID == $this->config->item('Workflows')['PreScreen'] ) {
      $PreScreenSubQueueComplete = $this->config->item('PreScreenSubQueueComplete');
      $this->db->select('to.OrderUID');
      $this->db->from('tOrders to');
      $this->db->join('tOrderWorkflows tw','tw.OrderUID = to.OrderUID AND tw.WorkflowModuleUID = '.$WorkflowModuleUID.'');
      $this->db->join('tOrderAssignments ta','ta.OrderUID = to.OrderUID AND ta.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
      $this->db->where('(tw.IsCountDisabled <> 1 OR tw.IsCountDisabled IS NULL)',NULL, FALSE);

      $this->db->group_start();
      $this->db->group_start();

      foreach ($PreScreenSubQueueComplete as $key => $QueueUID) {
        //self completed count
        $SELFWHERE = '';
        /*if($by == 'self') {
          $SELFWHERE = ' AND (tOrderQueues.RaisedByUserUID = '.$this->loggedid.')';
        }*/   

        if ($key === 0) {
          $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = ta.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending' AND tOrderQueues.RaisedDateTime BETWEEN '".$FromDate. "' AND '".$ToDate."' ".$SELFWHERE.")",NULL,FALSE);
        } else {
          $this->db->or_where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = ta.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending' AND tOrderQueues.RaisedDateTime BETWEEN '".$FromDate. "' AND '".$ToDate."' ".$SELFWHERE.")",NULL,FALSE);
        }
      }
      $this->db->group_end();
      $this->db->or_group_start();
      $this->db->where('ta.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
      //$this->db->where('ta.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
      // //self completed count
      /*if($by == 'self') {
        $this->db->where('ta.CompletedByUserUID', $this->loggedid);
      }*/

      $this->db->group_end();
      $this->db->group_end();

      //Skip Users
      $SkippedUsers = $this->config->item('ReportSkippedUsers');
      if (!empty($SkippedUsers)) {

        $this->db->group_start();
        $this->db->where_not_in('ta.CompletedByUserUID',$SkippedUsers);
        $this->db->or_where('ta.CompletedByUserUID IS NULL',NULL,FALSE);
        $this->db->group_end();
      }
      $condition_orders_sql = $this->db->get_compiled_select();

    }

    $condition_orders_sql = str_ireplace('SELECT *', '', $condition_orders_sql);
    $condition_orders_sql = str_ireplace('WHERE', ' ', $condition_orders_sql);

    return $condition_orders_sql ? $condition_orders_sql : false;
  }

  /**
  *Function get Queue Progress Count 
  *@author Mansoor Ali <mansoor.ali@avanzegroup.com>
  *@since Wednesday 22 October 2020.
  **/

  function queue_progress_new_count($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate,$dayCount)
  {

    if ($dayCount > 31)
    {
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%b-%Y") AS Date',FALSE);
    }
    else
    {
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d-%b") AS Date',FALSE);  
    }

    //$this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime, "%d-%b") AS Date',FALSE);
    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrderWorkflows.WorkflowModuleUID THEN tOrders.OrderUID ELSE NULL END) AS InflowOrders',FALSE);

    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrderAssignments.WorkflowStatus=5 
AND NOT EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrderAssignments.OrderUID AND tOrderQueues.QueueUID IN (SELECT QueueUID FROM mQueues WHERE mQueues.WorkflowModuleUID='.$WorkflowModuleUID.' AND mQueues.CustomerUID ='.$this->parameters['DefaultClientUID']. ' AND mQueues.Active = 1) AND DATE(tOrderWorkflows.EntryDateTime)=DATE(tOrderQueues.RaisedDateTime)) THEN tOrders.OrderUID ELSE NULL END) AS CompletedOrders',FALSE);

    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrderAssignments.WorkflowStatus IS NULL or tOrderAssignments.WorkflowStatus<>5 OR EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrderAssignments.OrderUID AND tOrderQueues.QueueUID IN (SELECT QueueUID FROM mQueues WHERE mQueues.WorkflowModuleUID='.$WorkflowModuleUID.' AND mQueues.CustomerUID ='.$this->parameters['DefaultClientUID']. ' AND mQueues.Active = 1) AND DATE(tOrderWorkflows.EntryDateTime)=DATE(tOrderQueues.RaisedDateTime)) THEN tOrders.OrderUID ELSE NULL END) AS PendingOrders',FALSE);

    $this->db->from('tOrders');

    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID and tOrderWorkflows.WorkflowModuleUID="'.$WorkflowModuleUID.'"'); 

    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID ="'.$WorkflowModuleUID.'"','LEFT'); 

    $this->db->join('tOrderSubQueues','tOrderSubQueues.OrderUID = tOrders.OrderUID AND tOrderSubQueues.WorkflowModuleUID = '.$WorkflowModuleUID.'', 'LEFT');

    $this->db->where('tOrderWorkflows.WorkflowModuleUID', $WorkflowModuleUID);
    //to get the users based on client 
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."'",NULL,FALSE);

    $this->db->group_by('DATE_FORMAT(tOrderWorkflows.EntryDateTime, "%d-%b")');
    return $this->db->get()->result_array();

  }

  /**
  *Function get Queue Progress counts - loans to be completed without moved subqueues
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Thursday 22 October 2020.
  */
  function queue_progress_periodwise($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate,$dayCount)
  {


    $condition_orders_sql = false;

    $this->otherdb->select('QueueUID');
    $this->otherdb->from('mQueues');
    $this->otherdb->where('mQueues.WorkflowModuleUID', $WorkflowModuleUID);
    $this->otherdb->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->otherdb->where('mQueues.Active', 1);
    $previous_filtered_orders_sql = $this->otherdb->get_compiled_select();

    if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
      $previous_filtered_orders_sql = ' AND tOrderQueues.QueueUID IN (' . $previous_filtered_orders_sql . ') AND DATE(t.EntryDateTime)=DATE(tOrderQueues.RaisedDateTime)';
    } else {
      $previous_filtered_orders_sql = '';
    }

    // #1 SubQueries no.1 -------------------------------------------

    if ($dayCount > 31)
    {
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%b-%Y") AS Date',FALSE);
    }
    else
    {
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d-%b") AS Date',FALSE);  
    }

//    $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d-%b") AS Date',FALSE);
    $this->db->select('tOrderWorkflows.IsReversed,tOrderWorkflows.ReversedDateTime',FALSE);
    $this->db->select('tOrderWorkflows.WorkflowModuleUID,tOrderWorkflows.EntryDateTime,tOrders.OrderUID');
    $this->db->select('tOrderAssignments.WorkflowStatus,tOrderAssignments.CompleteDateTime');

    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkflowModuleUID.'');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tOrderWorkflows.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where('tOrderWorkflows.IsReversed', 0);
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $subQuery1 = $this->db->get_compiled_select();

    // #2 SubQueries no.2 -------------------------------------------
   
if ($dayCount > 31)
    {
      $this->db->select('DATE_FORMAT(tOrderWorkflowsHistory.EntryDateTime,"%b-%Y") AS Date',FALSE);
      //$this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%b-%Y") AS Date',FALSE);
    }
    else
    {
      $this->db->select('DATE_FORMAT(tOrderWorkflowsHistory.EntryDateTime,"%d-%b") AS Date',FALSE);
      //$this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d-%b") AS Date',FALSE);  
    }
//    $this->db->select('DATE_FORMAT(tOrderWorkflowsHistory.EntryDateTime,"%d-%b") AS Date',FALSE);
    
    $this->db->select('1 AS IsReversed',FALSE);
    $this->db->select('tOrderWorkflowsHistory.EntryDateTime AS ReversedDateTime',FALSE);
    $this->db->select('tOrderWorkflowsHistory.WorkflowModuleUID,tOrderWorkflowsHistory.EntryDateTime,tOrders.OrderUID');
    $this->db->select('tOrderAssignmentsHistory.WorkflowStatus,tOrderAssignmentsHistory.CompleteDateTime');


    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflowsHistory','tOrderWorkflowsHistory.OrderUID = tOrders.OrderUID AND tOrderWorkflowsHistory.WorkflowModuleUID = '.$WorkflowModuleUID.'');
    $this->db->join('tOrderAssignmentsHistory','tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND tOrderAssignmentsHistory.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tOrderWorkflowsHistory.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where("DATE_FORMAT(tOrderWorkflowsHistory.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $subQuery2 = $this->db->get_compiled_select();

    // #3 Union with Simple Manual Queries --------------------------

    $query = $this->db->query("SELECT `Date`, 
      GROUP_CONCAT(DISTINCT CASE WHEN t.WorkflowModuleUID THEN t.OrderUID ELSE NULL END) AS InflowOrders,
      GROUP_CONCAT(DISTINCT CASE WHEN ((t.IsReversed = 1 AND DATE(t.EntryDateTime) = DATE(t.ReversedDateTime)) AND (t.WorkflowStatus IS NULL OR t.WorkflowStatus<> 5)) OR (t.WorkflowStatus = 5 AND DATE(t.EntryDateTime) <> DATE(t.CompleteDateTime)) OR EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = t.OrderUID ".$previous_filtered_orders_sql.") THEN t.OrderUID ELSE NULL END ) AS PendingOrders,
      GROUP_CONCAT(DISTINCT CASE WHEN t.WorkflowStatus=5 AND DATE(t.CompleteDateTime)=DATE(t.EntryDateTime) AND NOT EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = t.OrderUID ".$previous_filtered_orders_sql.") THEN t.OrderUID ELSE NULL END ) AS CompletedOrders
      FROM (
      $subQuery1 
      UNION ALL 
      $subQuery2
      ) AS t GROUP BY `Date` ORDER BY t.EntryDateTime ");

      //GROUP BY `Date` ORDER BY t.EntryDateTime ");

      return $query->result_array();
  }

  /**
  *Function get Queue Progress counts - Individual wise - loans to be completed without moved subqueues
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Thursday 22 October 2020.
  */
  function queue_progress_individualwise($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {
    $condition_orders_sql = false;

    $this->otherdb->select('QueueUID');
    $this->otherdb->from('mQueues');
    $this->otherdb->where('mQueues.WorkflowModuleUID', $WorkflowModuleUID);
    $this->otherdb->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->otherdb->where('mQueues.Active', 1);
    $previous_filtered_orders_sql = $this->otherdb->get_compiled_select();

    if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
      $previous_filtered_orders_sql = ' AND tOrderQueues.QueueUID IN (' . $previous_filtered_orders_sql . ') AND DATE(t.CompleteDateTime) = DATE(tOrderQueues.RaisedDateTime)';
    } else {
      $previous_filtered_orders_sql = '';
    }
    // #1 SubQueries no.1 -------------------------------------------

    $this->db->select('mUsers.UserName',FALSE);
    $this->db->select('tOrderWorkflows.IsReversed,tOrderWorkflows.ReversedDateTime',FALSE);
    $this->db->select('tOrderWorkflows.WorkflowModuleUID,tOrderWorkflows.EntryDateTime,tOrders.OrderUID');
    $this->db->select('tOrderAssignments.WorkflowStatus,tOrderAssignments.CompleteDateTime');


    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkflowModuleUID);
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkflowModuleUID);
    $this->db->join('mUsers','mUsers ON mUsers.UserUID = tOrderAssignments.AssignedToUserUID');

    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tOrderWorkflows.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where('tOrderWorkflows.IsReversed', 0);
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $subQuery1 = $this->db->get_compiled_select();

    // #2 SubQueries no.2 -------------------------------------------
    $this->db->select('mUsers.UserName',FALSE);
    $this->db->select('1 AS IsReversed',FALSE);
    $this->db->select('tOrderWorkflowsHistory.EntryDateTime AS ReversedDateTime',FALSE);
    $this->db->select('tOrderWorkflowsHistory.WorkflowModuleUID,tOrderWorkflowsHistory.EntryDateTime,tOrders.OrderUID');
    $this->db->select('tOrderAssignmentsHistory.WorkflowStatus,tOrderAssignmentsHistory.CompleteDateTime');


    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflowsHistory','tOrderWorkflowsHistory.OrderUID = tOrders.OrderUID AND tOrderWorkflowsHistory.WorkflowModuleUID = '.$WorkflowModuleUID);
    $this->db->join('tOrderAssignmentsHistory','tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND tOrderAssignmentsHistory.WorkflowModuleUID = '.$WorkflowModuleUID);
    $this->db->join('mUsers','mUsers ON mUsers.UserUID = tOrderAssignmentsHistory.AssignedToUserUID');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tOrderWorkflowsHistory.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where("DATE_FORMAT(tOrderWorkflowsHistory.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $subQuery2 = $this->db->get_compiled_select();

    // #3 Union with Simple Manual Queries --------------------------

    $query = $this->db->query("SELECT `UserName`, 
      GROUP_CONCAT(DISTINCT CASE WHEN t.WorkflowModuleUID THEN t.OrderUID ELSE NULL END) AS InflowOrders,
      GROUP_CONCAT(DISTINCT CASE WHEN ((t.IsReversed = 1 AND DATE(t.EntryDateTime) = DATE(t.ReversedDateTime)) AND (t.WorkflowStatus IS NULL OR t.WorkflowStatus<> 5)) OR (t.WorkflowStatus = 5 AND DATE(t.EntryDateTime) <> DATE(t.CompleteDateTime)) OR EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = t.OrderUID ".$previous_filtered_orders_sql.") THEN t.OrderUID ELSE NULL END ) AS PendingOrders,
      GROUP_CONCAT(DISTINCT CASE WHEN t.WorkflowStatus=5 AND DATE(t.CompleteDateTime)=DATE(t.EntryDateTime) AND NOT EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = t.OrderUID ".$previous_filtered_orders_sql.") THEN t.OrderUID ELSE NULL END ) AS CompletedOrders
      FROM (
      $subQuery1 
      UNION ALL 
      $subQuery2
      ) AS t
      GROUP BY `UserName` ORDER BY t.EntryDateTime ");
    return $query->result_array();

  }

  function aging_casequery($AgingHeaderkey,$tablecolumn,$CASEWHERE = FALSE)
  {
    switch ($AgingHeaderkey) {
      case 'fivetotendays':

      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 5 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 10 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);

      break;
      case 'tentofifteendays':
      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 10 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 15 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
      break;
      case 'fifteentotwentydays':
      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 15 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 20 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
      break;
      case 'twentyfivetothirtydays':
      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 25 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 30 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
      break;

      default:
      break;
    }
  }


  function aging_casequery1($AgingHeaderkey,$tablecolumn,$CASEWHERE = FALSE)
  {
    switch ($AgingHeaderkey) {
      case 'fivetotendays':

      $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 5 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 10 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);

      break;
      case 'tentofifteendays':
      $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 10 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 15 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
      break;
      case 'fifteentotwentydays':
      $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 15 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 20 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
      break;
      case 'twentyfivetothirtydays':
      $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) >= 25 AND TIMESTAMPDIFF(DAY, ('.$tablecolumn.'),NOW()) <= 30 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
      break;

      default:
      break;
    }
  }






  /**
  *Function get Aged counts - Aged wise
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Thursday 22 October 2020.
  */
  function aging_report_queuewise($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

    $CASEWHERE = " AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";



    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      $this->aging_casequery($AgingHeaderkey,'tOrderWorkflows.EntryDatetime',$CASEWHERE);

    }

    $this->db->select('mWorkFlowModules.WorkflowModuleName',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');

    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $this->db->group_by('tOrderWorkflows.WorkflowModuleUID');

    return $this->db->get()->result_array();

  }



  function aging_report_queuewise_count($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

    $CASEWHERE = " AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";



    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      $this->aging_casequery1($AgingHeaderkey,'tOrderWorkflows.EntryDatetime',$CASEWHERE);

    }

    $this->db->select('mWorkFlowModules.WorkflowModuleName',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');

    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');
   // $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $this->db->group_by('tOrderWorkflows.WorkflowModuleUID');

    return $this->db->get()->result_array();

  }




  /**
  *Function get Aging Report Individual
  *@author Mansoor Ali <mansoor.ali@avanzegroup.com>
  *@since Thursday 22 October 2020.
  */

  function aging_report_individualwise($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {


    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

    $CASEWHERE = " AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";

    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      $this->aging_casequery($AgingHeaderkey,'tOrderAssignments.AssignedDateTime',$CASEWHERE);

    }

    $this->db->select('mUsers.UserName',FALSE);
    $this->db->from('tOrderAssignments');
    $this->db->join('tOrders','tOrders.OrderUID = tOrderAssignments.OrderUID');
    $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID');
    $this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
//    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where("DATE_FORMAT(tOrderAssignments.AssignedDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $this->db->group_by('mUsers.UserName');
   
    return $this->db->get()->result_array();


  }



  /**
  * Function for Aged Periodic Report
  * @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
  * @since 27-10-2020
  **/ 
  function aging_report_periodwise($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate,$daysCount)
  {

    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

    $CASEWHERE = " AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";

    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      $this->aging_casequery($AgingHeaderkey,'tOrderWorkflows.EntryDateTime',$CASEWHERE);

    }

    if($daysCount>31){
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime, "%b-%Y") AS Date',FALSE);
      //$this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%b %Y") AS Date',FALSE);
    }
    else
    {
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d %b") AS Date',FALSE);
    }

    //$this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime, "%d-%b") AS Date',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');

    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$WorkflowModuleUID.'"', 'left');
    $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');
    $this->db->where('tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    
    //$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);

    if($daysCount>31){
      $this->db->group_by('DATE_FORMAT(tOrderWorkflows.EntryDateTime, "%b-%Y")');
      //$this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%b %Y") AS Date',FALSE);
    }
    else
    {
      $this->db->group_by('DATE(tOrderWorkflows.EntryDateTime)');
    }

    return $this->db->get()->result_array();
  }


  /**
  * Function for Aging Category-wise Report
  * @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
  * @since 28-10-2020
  **/ 
  function aging_report_categorywise($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {
    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');
    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {
      $this->aging_casequery($AgingHeaderkey,'tOrderWorkflows.EntryDateTime',$CASEWHERE);
    }
    $this->db->select('mCategories.CategoryName as CateName,',FALSE);
    $this->db->from('mSubQueueCategory');
    $this->db->join("mCategories","find_in_set(mCategories.CategoryUID,mSubQueueCategory.CategoryUIDs)<>0","inner",false);
    $this->db->join('tSubQueueCategory', 'tSubQueueCategory.CategoryUID  = mCategories.CategoryUID');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tSubQueueCategory.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->join('tOrders','tOrders.OrderUID = tOrderWorkflows.OrderUID');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('mSubQueueCategory.WorkflowModuleUID', $WorkflowModuleUID);//to get the users based on client 
    $this->db->group_by('mCategories.CategoryName');


    return $this->db->get()->result_array();
  }


  /**
  * Function for GateKeeping Pipeline Aging File Report
  * @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
  * @since 30-10-2020
  **/ 

  function pipeline_agingFileResult($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader1');

    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      switch ($AgingHeaderkey) {
        case 'fivetotendays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDateTime),NOW()) >= 5 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDateTime),NOW()) <= 10 THEN tOrderWorkflows.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'tentofifteendays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 10 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 15 THEN tOrderWorkflows.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'fifteentotwentydays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 15 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 20 THEN tOrderWorkflows.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'twentyfivetothirtydays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 25 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 30 THEN tOrderWorkflows.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'duetoday':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderFollowUp.ClearedDateTime),NOW()) = 0 THEN tOrderWorkflows.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'pastdue':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderFollowUp.ClearedDateTime),NOW()) >= 0 THEN tOrderWorkflows.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;

        default:
        break;
      }

    }

    $this->db->select('mWorkFlowModules.WorkflowModuleName',FALSE);
    $this->db->from('tOrderWorkflows');
    $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');
    $this->db->join('tOrderFollowUp','tOrderFollowUp.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID','left',false);
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $this->db->group_by('mWorkFlowModules.WorkflowModuleName');

    return $this->db->get()->result_array();

  }



  /**
  * Function for GateKeeping Pipeline Review, Complete and Pending Count Report
  * @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
  * @since 29-10-2020
  **/ 

  //Pipeline_ReviewCountReport
  function Pipeline_ReviewCountReport($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $this->db->select('count(tOrderAssignments.AssignedDatetime) as ReviewCount',FALSE);

    $this->db->select("GROUP_CONCAT(DISTINCT case when tOrderAssignments.WorkFlowStatus = 5 then tOrders.OrderUID else NULL end) CompleteCount",FALSE);

    $this->db->select("GROUP_CONCAT(DISTINCT case when tOrderAssignments.WorkFlowStatus != 5  then tOrders.OrderUID else NULL end) PendingCount",FALSE);

    $this->db->from('tOrderAssignments');
    $this->db->join('tOrders','tOrders.OrderUID = tOrderAssignments.OrderUID');
    $this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where("DATE_FORMAT(tOrderAssignments.AssignedDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);

    return  $this->db->get()->result_array();

  }


    /**
    * Function for TAT Aging Report 
    * @author Mansoor Ali.S<mansoor.ali@avanzegroup.com>
    * @since 02-11-2020
    **/

  function TAT_agingResult($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      switch ($AgingHeaderkey) {
        case 'fivetotendays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) >= 5 AND TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) <= 10 THEN tOrders.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'tentofifteendays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) >= 10 AND TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) <= 15 THEN tOrders.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'fifteentotwentydays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) >= 15 AND TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) <= 20 THEN tOrders.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;
        case 'twentyfivetothirtydays':
        $this->db->select("GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) >= 25 AND TIMESTAMPDIFF(DAY, (tSubQueueCategory.LastModifiedDateTime),NOW()) <= 30 THEN tOrders.OrderUID ELSE NULL END) AS ".$AgingHeaderkey,FALSE);
        break;

         default:
        break;
      }

    }
    $this->db->select('mCategories.CategoryName',FALSE);
    $this->db->from('mSubQueueCategory');
    $this->db->join("mCategories","find_in_set(mCategories.CategoryUID,mSubQueueCategory.CategoryUIDs)<>0","inner",false);
    $this->db->join('tSubQueueCategory', 'tSubQueueCategory.CategoryUID  = mCategories.CategoryUID');
    $this->db->join('tOrders','tOrders.OrderUID = tSubQueueCategory.OrderUID');
//    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('mSubQueueCategory.WorkflowModuleUID', $WorkflowModuleUID);//to get the users based on client 
    $this->db->group_by('mCategories.CategoryName');

    return $this->db->get()->result_array();
 
  }


  /**
  * Function for get Processinf inflow reports
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 23-10-2020
  **/ 
  function getProcessInflowPeriodicReportCounts($post){
    $FromDate = date('Y-m-d', strtotime($post['FromDate']));
    $ToDate = date('Y-m-d', strtotime($post['ToDate']));
    $WorkFlowUID = $post['WorkflowModuleUID'];


    if($post['daysCount']>31){
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%b %Y") AS Date',FALSE);
    }else{
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d %b") AS Date',FALSE);
    }
    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrders.LoanType="FHA" THEN tOrders.OrderUID ELSE NULL END) AS FHAOrders',FALSE);
    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrders.LoanType="VA" THEN tOrders.OrderUID ELSE NULL END) AS VAOrders',FALSE);
    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN (tOrders.LoanType="VA" OR tOrders.LoanType="FHA") THEN tOrders.OrderUID ELSE NULL END) AS TotalOrders',FALSE);

    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkFlowUID.'', 'LEFT');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkFlowUID.'','LEFT');
    $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');

    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    //$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkFlowUID);
    $this->db->where("DATE(tOrderWorkflows.EntryDateTime) BETWEEN '".$FromDate."' AND '".$ToDate."' ",NULL,FALSE);

    if($post['daysCount']>31){
      $this->db->group_by('MONTH(tOrderWorkflows.EntryDateTime)');
    }else{
      $this->db->group_by('DATE(tOrderWorkflows.EntryDateTime)');
    }

    return $this->db->get()->result_array();

  }

  /**
  *Function for get Processinf FollowUp reports 
  *@author SathishKumar <sathish.kumar@avanzegroup.com>
  *@since Monday 09 November 2020.
  */
  function getProcessFollowUpPeriodicReportCounts($post){
    $FromDate = date('Y-m-d', strtotime($post['FromDate']));
    $ToDate = date('Y-m-d', strtotime($post['ToDate']));
    $WorkFlowUID = $post['WorkflowModuleUID'];


    if($post['daycount']>31){
      $this->db->select('DATE_FORMAT(tOrderFollowUp.ClearedDateTime,"%b %Y") AS Date',FALSE);
    }else{
      $this->db->select('DATE_FORMAT(tOrderFollowUp.ClearedDateTime,"%d %b") AS Date',FALSE);
    }
    //$this->db->select('DATE_FORMAT(tOrderFollowUp.ClearedDateTime,"%d-%b") AS Date',FALSE);
    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN (tOrderFollowUp.ClearedByUserUID IS NOT NULL AND tOrderFollowUp.ClearedByUserUID <> "") THEN tOrders.OrderUID ELSE NULL END) AS TotalCleared',FALSE);

    $this->db->from('tOrders');
    $this->db->join('tOrderFollowUp','tOrderFollowUp.OrderUID = tOrders.OrderUID AND tOrderFollowUp.WorkflowModuleUID = '.$WorkFlowUID.'');

    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    
    $this->db->where("DATE(tOrderFollowUp.ClearedDateTime) BETWEEN '".$FromDate."' AND '".$ToDate."' ",NULL,FALSE);

    $this->db->where('tOrderFollowUp.IsCleared', STATUS_ONE);
    
    if($post['daycount']>31){
      $this->db->group_by('MONTH(tOrderFollowUp.ClearedDateTime)');
    }else{
      $this->db->group_by('DATE(tOrderFollowUp.ClearedDateTime)');
    }

    //$this->db->group_by('DATE(tOrderFollowUp.ClearedDateTime)');

    return $this->db->get()->result_array();

  }

  /**
  * Function for get Processinf inflow individual reports
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 22-10-2020
  **/ 
  function getProcessInflowIndividualReportCounts($post){
    $FromDate = date('Y-m-d', strtotime($post['FromDate']));
    $ToDate = date('Y-m-d', strtotime($post['ToDate']));
    $WorkFlowUID = $post['WorkflowModuleUID'];
    //get completed order 
    /*$data = [];
    $this->db->select("tOrders.OrderUID");
    $this->Common_Model->GetCompletedQueueOrders();
    $Completedorders = $this->db->get()->result();
    foreach ($Completedorders as $key => $value) 
    {
      $data[] = $value->OrderUID;
    }
    $Completedorders = implode(",",$data);*/

    $this->db->select('mUsers.UserName',FALSE);

    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrders.LoanType="FHA" THEN tOrders.OrderUID ELSE NULL END) AS FHAOrders',FALSE);
    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrders.LoanType="VA" THEN tOrders.OrderUID ELSE NULL END) AS VAOrders',FALSE);
    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN (tOrders.LoanType="VA" OR tOrders.LoanType="FHA") THEN tOrders.OrderUID ELSE NULL END) AS TotalOrders',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkFlowUID.'','LEFT');
    $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','LEFT');
    //$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
     $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkFlowUID.'', 'LEFT');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    //$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkFlowUID);
    $this->db->where("DATE(tOrderAssignments.AssignedDatetime) BETWEEN '".$FromDate."' AND '".$ToDate."' ",NULL,FALSE);

    $this->db->group_by('mUsers.UserName');

    return $this->db->get()->result_array();

  }

  /**
  *Function for get Processinf FollowUp individual reports 
  *@author SathishKumar <sathish.kumar@avanzegroup.com>
  *@since Monday 09 November 2020.
  */
  function getProcessFollowUpIndividualReportCounts($post){
    $FromDate = date('Y-m-d', strtotime($post['FromDate']));
    $ToDate = date('Y-m-d', strtotime($post['ToDate']));
    $WorkFlowUID = $post['WorkflowModuleUID'];
    //get completed order 
    /*$data = [];
    $this->db->select("tOrders.OrderUID");
    $this->Common_Model->GetCompletedQueueOrders();
    $Completedorders = $this->db->get()->result();
    foreach ($Completedorders as $key => $value) 
    {
      $data[] = $value->OrderUID;
    }
    $Completedorders = implode(",",$data);*/

    $this->db->select('mUsers.UserName',FALSE);

    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN (tOrderFollowUp.ClearedByUserUID IS NOT NULL AND tOrderFollowUp.ClearedByUserUID <> "") THEN tOrders.OrderUID ELSE NULL END) AS TotalCleared',FALSE);

    $this->db->from('tOrders');
    $this->db->join('tOrderFollowUp','tOrderFollowUp.OrderUID = tOrders.OrderUID AND tOrderFollowUp.WorkflowModuleUID = '.$WorkFlowUID.'');
    $this->db->join('mUsers','mUsers.UserUID = tOrderFollowUp.ClearedByUserUID','LEFT');
    //$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
     $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkFlowUID.'', 'LEFT');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where("DATE(tOrderFollowUp.ClearedDateTime) BETWEEN '".$FromDate."' AND '".$ToDate."' ",NULL,FALSE);

    $this->db->where('tOrderFollowUp.IsCleared', STATUS_ONE);

    $this->db->group_by('mUsers.UserName');

    return $this->db->get()->result_array();

  }

  /**
  * Function for update target based on workflow
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 22-10-2020
  **/ 
  function UpdateTarget($data){

    $this->db->select('*');//echo $data['WorkflowModuleUID'];
    $this->db->from('mProductivityTarget');
    $this->db->where(array('CustomerUID'=>$this->parameters['DefaultClientUID'],'WorkflowUID'=>$data['WorkflowModuleUID']));
    $count=$this->db->get()->num_rows();
    if($count<1){
      $insert=array('CustomerUID'=>$this->parameters['DefaultClientUID'],'WorkflowUID'=>$data['WorkflowModuleUID'],'Target'=>$data['Target'],'CreatedDate'=>date('Y-m-d H:i:s'));
      $insert = $this->db->insert('mProductivityTarget',$insert);
      if($this->db->affected_rows() > 0)
      {
        return 1;
      }else{
        return 0;
      }
    }else{
      $update=array('Target'=>$data['Target'],'UpdatedDate'=>date('Y-m-d H:i:s'));
      $this->db->where(array('CustomerUID'=>$this->parameters['DefaultClientUID'],'WorkflowUID'=>$data['WorkflowModuleUID']));
      $this->db->update('mProductivityTarget',$update);
      if($this->db->affected_rows() > 0)
      {
        return 1;
      }else{
        return 0;
      }
    }
  }

  /**
  * Function for get Process Productivity reports
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 22-10-2020
  **/  

  function getProductivityPeriodicReport($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate,$DaysCount=30)
  {
    $condition_orders_sql = false;


    if($DaysCount>31){
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%b %Y") AS Date,COUNT(DISTINCT mUsers.UserName) as UserCount',FALSE);
    }else{
      $this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d-%b") AS Date,COUNT(DISTINCT mUsers.UserName) as UserCount',FALSE);
    }

    //$this->db->select('DATE_FORMAT(tOrderWorkflows.EntryDateTime,"%d-%b") AS Date,COUNT(DISTINCT mUsers.UserName) as UserCount',FALSE);

    if(!empty($condition_orders_sql)) {

      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrders.OrderUID IN ('.$condition_orders_sql.') THEN tOrders.OrderUID ELSE NULL END) AS CompletedOrders',FALSE);

    } else {
      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrderAssignments.WorkflowStatus = 5 THEN tOrders.OrderUID ELSE NULL END) AS CompletedOrders',FALSE);
    }

    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkflowModuleUID.'');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
    $this->db->join('mUsers','mUsers ON mUsers.UserUID = tOrderAssignments.AssignedToUserUID','LEFT');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tOrderWorkflows.WorkflowModuleUID', $WorkflowModuleUID);

    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    
    if($DaysCount>31){
      $this->db->group_by('MONTH(tOrderWorkflows.EntryDateTime)');
    }else{
      $this->db->group_by('DATE(tOrderWorkflows.EntryDateTime)');
    }

    //$this->db->group_by('DATE(tOrderWorkflows.EntryDateTime)');
    
    return  $this->db->get()->result_array();
    
  }


  /**
  * Function for get productivity individual reports
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 22-10-2020
  **/  
  function getProductivityIndividualReport($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $this->db->select('mUsers.UserName,COUNT(DISTINCT mUsers.UserName) as UserCount',FALSE);

    if(!empty($condition_orders_sql)) {

      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrders.OrderUID IN ('.$condition_orders_sql.') THEN tOrders.OrderUID ELSE NULL END) AS CompletedOrders',FALSE);

    } else {
      $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrderAssignments.WorkflowStatus = 5 THEN tOrders.OrderUID ELSE NULL END) AS CompletedOrders',FALSE);
    }

    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkflowModuleUID.'');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkflowModuleUID);
    $this->db->join('mUsers','mUsers ON mUsers.UserUID = tOrderAssignments.AssignedToUserUID','LEFT');

    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tOrderWorkflows.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where("DATE_FORMAT(tOrderAssignments.AssignedDatetime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $this->db->group_by('mUsers.UserName');

    return $this->db->get()->result_array();
    

  }

  /**Function get Aged counts - Subqueue wise
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Tuesday 27 October 2020.
  */
  function aging_report_subqueuewise($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

    $CASEWHERE = " AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";


    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      switch ($AgingHeaderkey) {
        case 'fivetotendays':
        $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 5 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 10 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;
        case 'tentofifteendays':
        $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 10 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 15 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;
        case 'fifteentotwentydays':
        $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 15 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 20 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;
        case 'twentyfivetothirtydays':
        $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 25 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 30 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;

        default:
        break;
      }

    }

    $this->db->select('mQueues.QueueName',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->join('tOrderQueues','tOrderQueues.OrderUID = tOrders.OrderUID');
    $this->db->join('mQueues','mQueues.QueueUID = tOrderQueues.QueueUID AND mQueues.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $this->db->group_by('mQueues.QueueUID');
   
    return $this->db->get()->result_array();

  }


function aging_report_subqueuewise_count($WorkflowModuleUID,$newadv_fromDate,$newadv_toDate)
  {

    $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader');

    $CASEWHERE = " AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].") ";


    foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) {

      switch ($AgingHeaderkey) {
        case 'fivetotendays':
        $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 5 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 10 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;
        case 'tentofifteendays':
        $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 10 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 15 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;
        case 'fifteentotwentydays':
        $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 15 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 20 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;
        case 'twentyfivetothirtydays':
        $this->db->select('SUM(DISTINCT CASE WHEN TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) >= 25 AND TIMESTAMPDIFF(DAY, (tOrderWorkflows.EntryDatetime),NOW()) <= 30 '.$CASEWHERE.' THEN tOrders.OrderUID ELSE NULL END) AS '.$AgingHeaderkey,FALSE);
        break;

        default:
        break;
      }

    }

    $this->db->select('mQueues.QueueName',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->join('tOrderQueues','tOrderQueues.OrderUID = tOrders.OrderUID');
    $this->db->join('mQueues','mQueues.QueueUID = tOrderQueues.QueueUID AND mQueues.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "'.$WorkflowModuleUID.'"');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$newadv_fromDate."' AND '".$newadv_toDate."' ",NULL,FALSE);
    $this->db->group_by('mQueues.QueueUID');

   return $this->db->get()->result_array();

  } 



  /**
  *Function fetch loans based on orderuid
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Thursday 22 October 2020.
  */

  function fetchorder_query($post,$global='')
  {
    $this->db->from('tOrders');
    $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
    $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','LEFT');


    if(!empty($post['WorkflowModuleUID'])) {
      $this->db->select('tOrderWorkflows.WorkflowModuleUID,tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime,tOrderAssignments.AssignedToUserUID');

      $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = ' . $post['WorkflowModuleUID'] ,'LEFT');
      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = ' . $post['WorkflowModuleUID'] ,'LEFT');
      $this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

    } else {
      $this->db->select("'' AS WorkflowModuleUID,'' AS EntryDatetime,'' AS DueDateTime,'' AS AssignedToUserUID" ,NULL,FALSE);
    }

    $this->db->join('tOrderPropertyRole','tOrders.OrderUID = tOrderPropertyRole.OrderUID','left');

    $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
    $this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','left');
    $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
    
    if(!empty($post['OrderUID'])) {
      $this->db->where("tOrders.OrderUID IN (".$post['OrderUID'].")",NULL,FALSE);
    } else {
      $this->db->where("tOrders.OrderUID","");
    }

    $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $this->Common_Model->advanced_search($post);
    }

    $this->db->group_by('tOrders.OrderUID');
  }

  function count_all($post)
  {
    $this->fetchorder_query($post);
    $query = $this->db->count_all_results();
    return $query;
  }

  function count_filtered($post)
  {
    $this->Common_Model->DynamicColumnsCommonQuery(FALSE,TRUE,$post);

    $this->fetchorder_query($post);
    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);
    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    $query = $this->db->get();
    return $query->num_rows();
  }

  function getOrders($post)
  {
    $this->Common_Model->DynamicColumnsCommonQuery(FALSE,TRUE,$post);
    
    $this->db->select("tOrders.*");
    $this->db->select("tOrderImport.*");
    $this->db->select("tOrderPropertyRole.*");

    $this->fetchorder_query($post);
    // Datatable Search
    $this->Common_Model->WorkflowQueues_Datatable_Search($post);
    // Datatable OrderBy
    $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

    if (isset($post['length']) && $post['length']!='') {
      $this->db->limit($post['length'], $post['start']);
    } 

    $query = $this->db->get();
    return $query->result();
  } 
  /*Fetch Loans End*/ 


  /**
  * Function for get Quality periodic report
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 29-10-2020
  **/ 
  function getQualityPeriodicReport($WorkFlowUID,$FromDate,$ToDate){

    $this->db->select('
     DATE_FORMAT(tOrders.OrderEntryDateTime, "%d-%b") AS Date,
     SUM(CASE WHEN tQCOrders.IsError="Yes" THEN 1 ELSE 0 END) AS QCFail,
     SUM(CASE WHEN tQCOrders.IsError="No" THEN 1 ELSE 0 END) AS QCPass',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tQCOrders','tQCOrders.OrderUID = tOrders.OrderUID','LEFT');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkFlowUID,'LEFT');

    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tQCOrders.WorkflowModuleUID', $WorkFlowUID);
    $this->db->where("DATE_FORMAT(tOrders.OrderEntryDateTime, '%Y-%m-%d') BETWEEN '".$FromDate."' AND '".$ToDate."' ",NULL,FALSE);

    $this->db->group_by('DATE(tOrders.OrderEntryDateTime)');
    return $this->db->get()->result_array();
  }

  /**
  * Function for get Quality Individual report
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 29-10-2020
  **/ 
  function getQualityIndividualReport($WorkFlowUID,$FromDate,$ToDate){

    $this->db->select('
     mUsers.UserName as UserName,
     SUM(CASE WHEN tQCOrders.IsError="Yes" THEN 1 ELSE 0 END) AS QCFail,
     SUM(CASE WHEN tQCOrders.IsError="No" THEN 1 ELSE 0 END) AS QCPass',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tQCOrders','tQCOrders.OrderUID = tOrders.OrderUID','LEFT');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkFlowUID,'LEFT');
    $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','LEFT');

    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where('tQCOrders.WorkflowModuleUID', $WorkFlowUID);
    $this->db->where("DATE_FORMAT(tOrders.OrderEntryDateTime, '%Y-%m-%d') BETWEEN '".$FromDate."' AND '".$ToDate."' ",NULL,FALSE);

    $this->db->group_by('mUsers.UserName');
    return $this->db->get()->result_array();
  }


  /**
  * Function for get Completed count report
  * @author vishnupriya.A<vishnupriya.a@avanzegroup.com>
  * @since 31-10-2020
  **/ 
  function completedordersBasedOnWorkflow_count_all($post = false)
  {


    $this->db->select('GROUP_CONCAT(DISTINCT CASE WHEN tOrders.OrderUID THEN tOrders.OrderUID ELSE NULL END) AS Count',FALSE);
    $this->db->from('tOrders');
    $this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','LEFT');
    $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','LEFT');
    $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','LEFT');
    $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','LEFT');
    $this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','LEFT');
    $this->db->join('mProducts','tOrders.ProductUID = mProducts.ProductUID','LEFT');
    $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','LEFT');
    $this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID','LEFT');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID','LEFT');
    $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','LEFT');
    $this->db->join('mUsers Completed','Completed.UserUID = tOrderAssignments.CompletedByUserUID','LEFT');
    $this->db->join('mUsers b','tOrderAssignments.AssignedToUserUID = b.UserUID','LEFT');
    $this->db->join('tOrderAssignments TOA_1','TOA_1.OrderUID = tOrders.OrderUID AND TOA_1.WorkflowModuleUID='.$post['advancedsearch']['WorkflowModuleUID'],'LEFT');
    $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);//to get the users based on client 
    $this->db->where(array('tOrderAssignments.WorkflowStatus'=> 5, 'tOrderAssignments.WorkflowModuleUID' => $post['WorkflowModuleUID'],'tOrderWorkflows.WorkflowModuleUID'=>$post['WorkflowModuleUID']));
    $this->db->where("DATE_FORMAT(tOrders.OrderEntryDateTime, '%Y-%m-%d') BETWEEN '".$post['fromDate']."' AND '".$post['Todate']."' ",NULL,FALSE);
    $this->db->where('( `TOA_1`.`WorkflowStatus` != 5 OR `TOA_1`.`WorkflowStatus` IS NULL )');
    $this->db->group_by('DATE(tOrderWorkflows.EntryDateTime)');
     return $this->db->get()->result_array();
    
  }


  function GetCompletedQueueOrdersByWorkflow($post, $Conditions = ['IgnoreQueues' => TRUE])
  {

    // Check WorkflowModuleUID is not empty
    $WorkflowModuleUID = $post['WorkflowModuleUID'];
    $fromDate = $post['fromDate'];
    $Todate = $post['Todate'];

    $this->db->from('tOrders');

    $this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
    $this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
    $this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
    $this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
    $this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
    $this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
    $this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

    $this->db->join('tOrderWorkflows', "tOrderWorkflows.OrderUID = tOrders.OrderUID", 'left');
    //join followup


    if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
      $this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
    }

    $this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID', 'left');
    $this->db->join('mUsers', 'mUsers.UserUID = tOrderAssignments.AssignedToUserUID', 'left');
    $this->db->join('mUsers Completed', 'Completed.UserUID = tOrderAssignments.CompletedByUserUID', 'left');
    $this->db->join('mUsers b', 'tOrderAssignments.AssignedToUserUID = b.UserUID', 'left');
    $this->db->where('tOrderAssignments.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
    $this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where('tOrderWorkflows.WorkflowModuleUID', $WorkflowModuleUID);
    $this->db->where("DATE_FORMAT(tOrderWorkflows.EntryDateTime, '%Y-%m-%d') BETWEEN '".$fromDate."' AND '".$Todate."'",NULL,FALSE);
    $this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID, $Conditions['IgnoreQueues']);

    /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

    /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

    $this->db->group_by('tOrders.OrderUID');
  }

  function AllWorkflowQueue_CommonQuery($WorkflowModuleUID = FALSE, $IgnoreQueues = FALSE,$orderbyException="",$Conditions = [])
  {
    if ($IgnoreQueues == FALSE) {
      $Workflows_EliminatedMilestones = $this->config->item('Workflows_EliminatedMilestones');
      $this->db->group_start();
      $this->db->where('tOrders.MilestoneUID IS NULL', NULL, FALSE);
      $this->db->or_where_not_in('tOrders.MilestoneUID', $Workflows_EliminatedMilestones);
      $this->db->group_end();

      if (!empty($WorkflowModuleUID)) {

        // ignore this conditions
        if (!isset($Conditions['ThreeAConfirmationSigningDateCond'])) {

          $CustomerUID = $this->session->userdata("DefaultClientUID");
          
          $this->otherdb->select('State, LoanTypeName, PropertyType, MilestoneUID');
          $this->otherdb->from('mCustomerWorkflowModules');
          $this->otherdb->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowModuleUID));
          $WorkflowMetrics = $this->otherdb->get()->row_array();

          if (!empty($WorkflowMetrics['State'])) {
            $WorkflowState = explode(',', $WorkflowMetrics['State']);
            $this->db->where_in('tOrders.PropertyStateCode', $WorkflowState);
          }

          if (!empty($WorkflowMetrics['LoanTypeName'])) {
            $WorkflowLoanTypeName = explode(',', $WorkflowMetrics['LoanTypeName']);
            $this->db->where_in('tOrders.LoanType', $WorkflowLoanTypeName);
          }

          if (!empty($WorkflowMetrics['PropertyType'])) {
            $this->db->like('tOrderImport.PropertyType', $WorkflowMetrics['PropertyType']);
          }

          if (!empty($WorkflowMetrics['MilestoneUID'])) {
            $WorkflowMilestoneUID = explode(',', $WorkflowMetrics['MilestoneUID']);
            $this->db->where_in('tOrders.MilestoneUID', $WorkflowMilestoneUID);
          }
        }
      }

      // Highlight expiry orders based on duration setup in client setup
      $this->db->join('mCustomerWorkflowModules','mCustomerWorkflowModules.CustomerUID=tOrders.CustomerUID AND mCustomerWorkflowModules.WorkflowModuleUID = '.$WorkflowModuleUID.' AND (mCustomerWorkflowModules.OrderHighlightDuration IS NOT NULL OR mCustomerWorkflowModules.OrderHighlightDuration <> "") ','left');     
      if(empty($orderbyException)) {
        $this->db->order_by('tOrderWorkflows.DueDateTime', 'ASC');
      } 
    }
  }

}?>
