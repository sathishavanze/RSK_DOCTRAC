<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Reports_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function Get_ReportsInfo($ClientUID, $ReportUID) {
		$this->db->select('*');
		$this->db->from('mReports');
		$this->db->join('mReportFields', 'mReportFields.ReportUID = mReports.ReportUID');
		$this->db->join('mReportsGroups', 'mReportsGroups.GroupUID = mReportFields.GroupUID');
		$this->db->join('mDocumentType', 'mDocumentType.DocumentTypeUID = mReportFields.DocumentTypeUID');
		$this->db->where(array('mReports.ClientUID'=>$ClientUID, 'mReports.ReportUID'=>$ReportUID));
		return $this->db->get()->result_array();
		// echo "<pre>";
		// print_r($query);exit();
	}

	function Get_ReportsDetails($ClientUID, $ReportUID) {
		$WorkflowDetails = $this->Get_WorkflowDetails($ClientUID, $ReportUID);
		$this->db->select('tOrders.OrderNumber');		
		$this->db->select('tOrders.LoanType');	
		$this->db->select("GROUP_CONCAT(distinct concat_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName)) as Borrower");
		$this->db->select('"" as Processor');	
		$this->db->select('mProducts.ProductName');	
		$this->db->select('"" as Status_For_Filtr');	
		$this->db->select('mStates.StateName');			
		$this->db->select('"" as Processor_Offshore');
		$this->db->select('"" as Reviewed_times');	
		$this->db->select('"" as Initial_Review_Date');
		$this->db->select('"" as Review_completed_1');	
		$this->db->select('"" as Review_completed_2');	
		$this->db->select('"" as Review_completed_3');	
		foreach ($WorkflowDetails as $key => $value)
		 {
			$this->db->select("(SELECT
	tDocumentCheckList.Answer
FROM
	tDocumentCheckList
WHERE
	tDocumentCheckList.DocumentTypeUID = ".$value['DocumentTypeUID']."
AND tDocumentCheckList.WorkflowUID = ".$value['WorkflowUID']."
AND tDocumentCheckList.OrderUID = tOrders.OrderUID) as ".$value['SystemName']." ");
		}
		$this->db->from('tOrders');
		$this->db->join('mReports', 'mReports.ClientUID = tOrders.CustomerUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mReportFields', 'mReportFields.ReportUID = mReports.ReportUID', 'left');
		$this->db->join('tDocumentCheckList', 'tDocumentCheckList.DocumentTypeUID = mReportFields.DocumentTypeUID', 'left');
		$this->db->join('mProducts', 'mProducts.ProductUID = tOrders.ProductUID', 'left');
		$this->db->join('mStates', 'mStates.StateCode = tOrders.PropertyStateCode', 'left');
		$this->db->where('mReports.ClientUID',28);
		$this->db->group_by("tOrders.OrderUID");
		return $this->db->get()->result_array();	
	}

	function Get_WorkflowDetails($ClientUID, $ReportUID) {
		$this->db->select('mReportFields.WorkflowUID,mReportFields.DocumentTypeUID, mDocumentType.DocumentTypeName,mWorkFlowModules.SystemName,mReportsGroups.GroupName');	
		$this->db->from('mReports');
		$this->db->join('mReportFields', 'mReportFields.ReportUID = mReports.ReportUID', 'left');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mReportFields.WorkflowUID', 'left');

		$this->db->join('mReportsGroups', 'mReportsGroups.GroupUID = mReportFields.GroupUID', 'left');
		$this->db->join('mDocumentType', 'mDocumentType.DocumentTypeUID = mReportFields.DocumentTypeUID', 'left');
		$this->db->where(array('mReports.ClientUID'=>$ClientUID, 'mReports.ReportUID'=>$ReportUID));
		$this->db->order_by("mReportsGroups.GroupName", "asc");
		return $this->db->get()->result_array();
		// echo "<pre>";
		// print_r($query);exit();	
	}
	function GetmQueuesByWorkFlow($WorkflowUID,$QueueUID ="")
	{
		$this->db->select('QueueUID,QueueName');
		$this->db->from('mQueues');
		$this->db->where(array('WorkflowModuleUID'=>$WorkflowUID, 'Active'=>1,'CustomerUID'=>$this->parameters['DefaultClientUID']));
		if(!empty($QueueUID))
		{
			 $this->db->where_in('QueueUID', $QueueUID);  //this is condition  
		}
		return $this->db->get()->result_array();
	}
	public function GetUserQuecount($WorkflowModuleUID,$mQueues,$userslist)
	{
		$function_call		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
		if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
		{
		     $this->Submissions_Orders_Model->SubmissionCondition();
		}
		$result=array();
		$user_cnt=array();
		if(!empty($mQueues)){
			$i=1;
			if($this->RoleType == $this->config->item('Internal Roles')['Agent']){
					$userslist=$this->GetUsersByWorkflow($WorkflowModuleUID,array($this->loggedid));
			}
			else
			{
				$userslist=$this->GetUsersByWorkflow($WorkflowModuleUID,$userslist);
			}
		foreach($userslist as $row){
			$assigned_cnt=0;
			if($this->RoleType != $this->config->item('Internal Roles')['Agent']){
			$assigned_cnt=$this->Common_Model->totAssignedCntByWorkflowModuleUID($WorkflowModuleUID,$row->UserUID);
			}
			foreach($mQueues as $key => $queue){
				$this->Common_Model->$function_call(false);
						$this->db->select('tOrderQueues.OrderUID');

		  $this->db->join("tOrderQueues","tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus = 'Pending'");
    	$this->db->join("mQueues","tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '".$queue."' and mQueues.WorkflowModuleUID='".$WorkflowModuleUID."'");
		/*$this->db->from('tOrderQueues');
		$this->db->join('tOrders','tOrderQueues.OrderUID = tOrders.OrderUID','left');
		$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID=tOrders.OrderUID','left');*/
		$this->db->where(array('tOrderQueues.QueueUID'=>$queue, ' tOrders.CustomerUID'=>$this->parameters['DefaultClientUID'],'tOrderAssignments.WorkflowModuleUID'=>$WorkflowModuleUID,'tOrderAssignments.AssignedToUserUID'=>$row->UserUID,'tOrderQueues.QueueStatus'=>'Pending'));
		$this->db->where("(tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);

		 	$row_res= $this->db->get()->result();
		 	//print_r($this->db->last_query());
		 	//print_r($row_res);exit;
		 	/*echo $row_res->QueCount;exit;*/
			 		if(!empty($row_res))
			 		{
					 	$result[$i][$queue]=count($row_res);
					 	$result[$i]['Orders'][$queue]=implode(',',array_column($row_res,'OrderUID'));
				 	}
				 	else
				 	{
				 		array_push($user_cnt, $row->UserUID);
				 	}
				 
		 		}
		 			$counts =array_count_values($user_cnt);
		 			if($this->RoleType != $this->config->item('Internal Roles')['Agent']){
		 			if($assigned_cnt != 0){
				 			$result[$i]['UserName']=$row->UserName;
				 			$result[$i]['UserUID']=$row->UserUID;
				 			$result[$i]['assigned_cnt']=$assigned_cnt;
			 	    }
			 		}
			 	    if(empty($counts))
		 			{
		 				$result[$i]['UserName']=$row->UserName;
				 			$result[$i]['UserUID']=$row->UserUID;
				 			$result[$i]['assigned_cnt']=$assigned_cnt;
		 			}
			 	   else{ 
			 	   	/*echo $row->UserUID;
			 	   	print_r($counts);*/
			 	   	if(count($mQueues) != isset($counts[$row->UserUID])){
				 			$result[$i]['UserName']=$row->UserName;
				 			$result[$i]['UserUID']=$row->UserUID;
				 			$result[$i]['assigned_cnt']=$assigned_cnt;
				 	}
				 }
			 		//print_r($result);exit;
		 	$i++;
			}
		
		}
		return $result;
	}
	public function GetUserAgingQuecount($WorkflowModuleUID,$aging,$mQueues,$userslist)
	{
			
		$function_call		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
		$result=array();
		$user_cnt=array();
		if(!empty($mQueues)){
			$i=1;
			
			if($this->RoleType == $this->config->item('Internal Roles')['Agent']){
					$userslist=$this->GetUsersByWorkflow($WorkflowModuleUID,array($this->loggedid));
			}
			else
			{
				$userslist=$this->GetUsersByWorkflow($WorkflowModuleUID,$userslist);
			}
		foreach($userslist as $row){
			$assigned_cnt=0;
		 if($this->RoleType != $this->config->item('Internal Roles')['Agent']){
			$assigned_cnt=$this->Common_Model->totAssignedCntByWorkflowModuleUID($WorkflowModuleUID,$row->UserUID);
			}
		   foreach ($aging as $value) 
             {

              	foreach($mQueues as $key => $queue){
              		$this->Common_Model->$function_call(false);
                   $this->db->select('TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())  as aging');
                    $this->db->select("tOrders.OrderUID");
				   $this->db->join("tOrderQueues","tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus = 'Pending'");
    			  $this->db->join("mQueues","tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '".$queue."' and mQueues.WorkflowModuleUID='".$WorkflowModuleUID."'");
				/*	$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID=tOrders.OrderUID','inner');*/
					$this->db->where(array('tOrderQueues.QueueUID'=>$queue, ' tOrders.CustomerUID'=>$this->parameters['DefaultClientUID'],'tOrderAssignments.WorkflowModuleUID'=>$WorkflowModuleUID,'tOrderAssignments.AssignedToUserUID'=>$row->UserUID,'tOrderQueues.QueueStatus'=>'Pending','`tOrderWorkflows`.WorkflowModuleUID'=>$WorkflowModuleUID));
					$this->db->where("(tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
					$this->db->where("`EntryDateTime` != '' AND `EntryDateTime` IS NOT NULL",NULL,FALSE);
					$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);
						switch ($value) {
							case 'zerodays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=0",NULL,FALSE);
							break;
							case 'oneday':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=1",NULL,FALSE);
							break;
							case 'twodays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=2",NULL,FALSE);
							break;
							case 'threedays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=3",NULL,FALSE);
							break;
							case 'fourdays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=4",NULL,FALSE);
							break;
							case 'fivedays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=5",NULL,FALSE);
							break;
							case 'sixdays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=6",NULL,FALSE);
							break;
							case 'sevendays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())=7",NULL,FALSE);
							 break;
							 case 'eleventtofifteendays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 11  AND  TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) <= 15",NULL,FALSE);
							 break;
							 case 'sixteentotwentydays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 16  AND  TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) <= 20",NULL,FALSE);
							 break;
							 case 'sixteentotwentydays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 16  AND  TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) <= 20",NULL,FALSE);
							 break;
							 case 'twentyonetothirtydays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 21  AND  TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) <= 30",NULL,FALSE);
							 break;
							 case 'thirtyonetofortyfivedays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 31  AND  TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) <= 45",NULL,FALSE);
							 break;
							 case 'fortysixttosixtydays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 46  AND  TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) <= 60",NULL,FALSE);
							 break;
							  case 'sixtyonetoninetydays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 61  AND  TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) <= 90",NULL,FALSE);
								break;
							 case 'greaterthanninetydays':
								$this->db->where("TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW()) >= 90",NULL,FALSE);
							  break;
							  default:
								break;

							}//End of switch case
		 					$row_res= $this->db->get()->result();
		 					if(!empty($row_res)){
	                          	$result[$i][$value][$queue]=count($row_res);
	                          	$result[$i][$value]['Orders'][$queue]=implode(',',array_column($row_res,'OrderUID'));
                            }
                          	else
						 	{
						 		array_push($user_cnt, $row->UserUID);
						 	}
                       // print_r($result);exit;
                     }//End of mQueues
				}//End of Aging

				$counts =array_count_values($user_cnt);
				$agingQueCount=count($aging)*count($mQueues);
					if($this->RoleType != $this->config->item('Internal Roles')['Agent']){
		 			if($assigned_cnt != 0){
				 			$result[$i]['UserName']=$row->UserName;
				 			$result[$i]['UserUID']=$row->UserUID;
				 			$result[$i]['assigned_cnt']=$assigned_cnt;
			 	    }
			 		}
					if(empty($counts))
		 			{
		 				$result[$i]['UserName']=$row->UserName;
		 				$result[$i]['UserUID']=$row->UserUID;
				 		$result[$i]['assigned_cnt']=$assigned_cnt;
		 			}
			 		else{ 
			 			if($agingQueCount != isset($counts[$row->UserUID])){
			 			$result[$i]['UserName']=$row->UserName;
			 			$result[$i]['UserUID']=$row->UserUID;
				 		$result[$i]['assigned_cnt']=$assigned_cnt;
			 			}
			 		}
				
			 $i++;
		   }//End of users list
		}//Empty Queues Checking
		return $result;
	}
	function GetUsersByWorkflow($WorkflowModuleUID,$UID=""){
		$this->db->select("`UserUID`,`UserName`,LoginID,mUsers.`RoleUID`,mResources.WorkflowModuleUID,mRoleResources.ResourceUID,mResources.CustomerUID,mResources.FieldName");
		$this->db->from('mUsers');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID','inner');
		$this->db->join('mRoleResources','mUsers.RoleUID=mRoleResources.RoleUID','left');
		$this->db->join('mResources','mResources.ResourceUID=mRoleResources.ResourceUID','left');
		// $this->db->where('mUsers.CustomerUID IS NULL AND mUsers.LenderUID IS NULL AND mUsers.SettlementAgentUID IS NULL AND mUsers.InvestorUID IS NULL AND mUsers.VendorUID IS NULL OR mUsers.IsInternal = 1');
		//if($this->session->userdata('RoleType') == 9 ) {
		//}
		//$this->db->where('mRole.CustomerUID',$this->parameters['DefaultClientUID']);
		//$this->db->where('mRole.RoleTypeUID',$this->config->item('RoleTypeUID'));
		$this->db->where('mUsers.CustomerUID',$this->parameters['DefaultClientUID']);
		$this->db->where('mResources.WorkflowModuleUID',$WorkflowModuleUID);
		$this->db->where('mResources.FieldSection','ORDERWORKFLOW');
		$this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", mResources.CustomerUID)",NULL, FALSE);
		//$this->db->where('mUsers.UserUID !=',$this->session->userdata('UserUID'));
		$this->db->where('mUsers.Active ',1);
		$this->db->where_in('mRole.RoleTypeUID',array_merge($this->config->item('SuperAccess'),$this->config->item('AgentAccess')));
		//echo $UID;exit;
		if(isset($UID) && !empty($UID))
		{
			//$this->db->where('mUsers.UserUID',$UID);
			 $this->db->where_in('mUsers.UserUID', $UID);  //this is condition  	
		}
		else
		{
		$this->db->group_by("mUsers.UserUID");
		}
		 return $this->db->get()->result();
		 //print_r($this->db->last_query());exit;
	}
	function fetchorder_query($post)
	{
		$this->db->select('tOrderQueues.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,tOrders.LoanType,tOrders.OrderEntryDateTime,TIMESTAMPDIFF(DAY,`EntryDateTime`,NOW())  as Queue_aging');
		   $this->db->from('tOrderWorkflows');
		   $this->db->join('tOrderQueues','tOrderQueues on tOrderWorkflows.OrderUID=tOrderQueues.OrderUID','inner');
			$this->db->join('tOrders','tOrderQueues.OrderUID = tOrders.OrderUID','inner');
			$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID=tOrders.OrderUID','inner');
			$this->db->where(array('tOrderQueues.QueueUID'=>$post['mQueue'], ' tOrders.CustomerUID'=>$this->parameters['DefaultClientUID'],'tOrderAssignments.WorkflowModuleUID'=>$post['WorkflowModuleUID'],'tOrderQueues.QueueStatus'=>'Pending','`tOrderWorkflows`.WorkflowModuleUID'=>$post['WorkflowModuleUID']));
			$this->db->where("(tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
			$this->db->where("`EntryDateTime` != '' AND `EntryDateTime` IS NOT NULL",NULL,FALSE);
			$status[] = $this->config->item('keywords')['Cancelled'];
		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $post['WorkflowModuleUID'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);
		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where_not_in('tOrders.StatusUID', $status);
		$Workflows_EliminatedMilestones = $this->config->item('Workflows_EliminatedMilestones');
		$this->db->where_not_in('tOrders.MilestoneUID', $Workflows_EliminatedMilestones);
			if(!empty($post['OrderUID'])) {
			$this->db->where("tOrders.OrderUID IN (".$post['OrderUID'].")",NULL,FALSE);
		} else {
			$this->db->where("tOrders.OrderUID","");
		}		
 		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}
		$this->db->group_by('tOrders.OrderUID');
		$this->db->order_by('OrderEntryDatetime');
	}
	function getOrders($post, $returntype='data')
	{
		$this->fetchorder_query($post);
		if (!in_array($returntype, ['count_all'])) {
		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);
		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
			}
		if (isset($post['length']) && $post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		/*$MetricsOrderBy = $this->Common_Model->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $post['WorkflowModuleUID']);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			$this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}*/
		$this->db->order_by('OrderEntryDatetime');
		
		if ($returntype == 'data') {
			$query = $this->db->get();
		return $query->result();
			}
		 if (in_array($returntype, ['count_all', 'count_filter'])) {
	       return $this->db->get()->num_rows();
	    }
	}
	function WorkInProgressOrders($post)
	{
		$UserUID=$post['UserUID'];
		//$this->Common_Model->DynamicColumnsCommonQuery($this->WorkflowModuleUID);
		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');
		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName");
		$this->db->select('tOrders.LastModifiedDateTime,tOrderAssignments.AssignedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetPreScreenOrders();
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);

				// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->WorkflowModuleUID);
		
		//Order Queue Permission
          $this->Common_Model->OrdersPermission($this->WorkflowModuleUID,'USERASSIGNEDCOUNT',false,['UserUID'=>$UserUID]);
		$this->db->get();
		print_r($this->db->last_query());
		exit;
		$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

     


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
	
}