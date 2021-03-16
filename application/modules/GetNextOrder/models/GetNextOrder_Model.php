<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class GetNextOrder_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function get_allgroups()
	{
		$this->db->select('*');
		$this->db->from('mGroups');
		$this->db->where('Active', 1);
		return $this->db->get()->result();
	}

	function get_groups()
	{
		$this->db->select('mGroups.GroupUID,mGroups.GroupName');
		$this->db->from('mGroupUsers');
		$this->db->join('mGroups', 'mGroups.GroupUID = mGroupUsers.GroupUID');
		$this->db->where('mGroupUsers.GroupUserUID', $this->loggedid);
		$this->db->where('mGroups.Active', 1);
		$this->db->group_by('mGroups.GroupUID');
		return $this->db->get()->result();
	}

	function get_groupstates($GroupUID){
		$this->db->select('mStates.StateUID,mStates.StateName');
		$this->db->from('mGroupStates');
		$this->db->join('mStates', 'mStates.StateUID = mGroupStates.GroupStateUID');
		$this->db->where('GroupUID',$GroupUID);
		return $this->db->get()->result();
	}

	function get_statedetails_byStateUID($StateUID)
	{
		$this->db->select('*');
		$this->db->from('mStates');
		$this->db->where('mStates.StateUID', $StateUID);
		return $this->db->get()->row();
	}

	function check_pendingworkflow()
	{
		$query = $this->db->query("SELECT EXISTS (SELECT 1 FROM tOrderAssignments JOIN tOrders ON tOrders.OrderUID = tOrderAssignments.OrderUID WHERE tOrderAssignments.AssignedToUserUID = '".$this->loggedid."' AND  (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed'].")) AND (StatusUID IS NULL OR StatusUID NOT IN (".$this->config->item('keywords')['ClosedandBilled'].",".$this->config->item('keywords')['Cancelled']."))) AS pending ");
		return  $query->row()->pending;	
	}
	function get_assign_nextorder($assigned_UID,$WorkflowModuleUID,$loan_types=null,$conditions=[])
	{	

		// Get Workflow Model Name and New Order Function Name
		$workflowcontroller = array_search($WorkflowModuleUID, $this->config->item('workflowcontroller'));
		$NewOrdersFunctionName = $this->config->item('controllerarray')[$workflowcontroller]['NewOrders'];
		$workflowcontrollerModel = $this->config->item('controllerarray')[$workflowcontroller]['Model'];

		// Conver Array Object to Associative Array
		$NewOrders_ArrObj = $this->{$workflowcontrollerModel}->{$NewOrdersFunctionName}([],'');
		$NewOrders_Arr = json_decode(json_encode($NewOrders_ArrObj), true);
		$NewOrders_OrderUIDs = array_column($NewOrders_Arr, 'OrderUID');

			//$conditions=[]
		//print_r($Conditions);exit;
		$order_ids='';	
		//$CurrentDate = date('08/17/2021');
		$CurrentDate = date('m/d/Y');
		//echo $CurrentDate;
		//echo strtotime($CurrentDate.' +1 Weekday');exit;
		$NextBusinessDay = date('m/d/Y', strtotime($CurrentDate.' +2 Weekday'));
		//echo $NextBusinessDay;exit;
		$ProcessorClosingDay = date('m/d/Y', strtotime($CurrentDate.' +2 Weekday'));
		$function_call		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
		foreach ($assigned_UID as $key => $UID) {

			$row = $this->get_getnextorder_permission($WorkflowModuleUID,$UID);
			$LoanTypeNames = '';
			if(!empty($row)) {
				$ExistingLoanTypeUIDs = explode(',', $row->LoanTypeUIDs);
				$LoanTypeNames = $this->db->select('GROUP_CONCAT(LoanTypeName) AS LoanTypeNames',FALSE)->where_in("LoanTypeUID",$ExistingLoanTypeUIDs)->get('mLoanType')->row()->LoanTypeNames;
			}
			
			if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
				
				$this->GetNextOrderQueryByLockexpiration($LoanTypeNames,$function_call,$conditions);

				if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
				{
					$this->Submissions_Orders_Model->SubmissionCondition();
				}

				// Filter only given orders
				if (!empty($NewOrders_OrderUIDs)) {
					$this->db->where_in('tOrders.OrderUID', $NewOrders_OrderUIDs);
				} else {
					$this->db->where('tOrders.OrderUID IS NULL', NULL, FALSE);
				}

				$this->db->limit(1);
				$result=$this->db->get()->row();
				//print_r($result);exit;

			} else {
				$result = '';
			}

			if(!empty($result)){
				if($result->import_count > 1)
				{ // Based on lockexpiration and closing date
					 $expiration_date = $result->LockExpiration;
						$this->db->select("1,`tOrderWorkflows`.`WorkflowModuleUID` ,`LoanNumber`,tOrderImport.LockExpiration,tOrders.OrderUID,`OrderNumber`,tOrderImport.ProcessorChosenClosingDate");
						$this->Common_Model->$function_call(false,['OrderByexception'=>true]);
						$this->db->where("STR_TO_DATE(tOrderImport.ProcessorChosenClosingDate,'%m/%d/%Y') > STR_TO_DATE('".$ProcessorClosingDay."','%m/%d/%Y')",NULL,FALSE);
						// $this->db->where("STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') = STR_TO_DATE('".$expiration_date."','%m/%d/%Y')",NULL,FALSE);
						$this->db->where("
							CASE 
								WHEN
									tOrders.LoanType = 'FHA' AND (mCustomerWorkflowModules.FHALockExpirationDate IS NOT NULL AND mCustomerWorkflowModules.FHALockExpirationDate <> '')
								THEN
									STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') >= STR_TO_DATE(mCustomerWorkflowModules.FHALockExpirationDate,'%m/%d/%Y')
								WHEN
									tOrders.LoanType = 'VA' AND (mCustomerWorkflowModules.VALockExpirationDate IS NOT NULL AND mCustomerWorkflowModules.VALockExpirationDate <> '')
								THEN
									STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') >= STR_TO_DATE(mCustomerWorkflowModules.VALockExpirationDate,'%m/%d/%Y')
								ELSE
									STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') > STR_TO_DATE('".$expiration_date."','%m/%d/%Y')		
								END
						",NULL,FALSE);

						if (!empty($LoanTypeNames)) 
						{
							$this->db->where("FIND_IN_SET(tOrders.LoanType,'".$LoanTypeNames."') <> 0",NULL, FALSE);
						}
						else
						{
							$this->db->where("(tOrders.LoanType IS NULL OR tOrders.LoanType = '')",NULL,FALSE);
						}
						
						$this->db->where("(tOrderAssignments.AssignedToUserUID IS NULL OR tOrderAssignments.AssignedToUserUID = '') AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
						$this->db->where("tOrders.CustomerUID ",$this->parameters['DefaultClientUID']);
						if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
						{
							$this->Submissions_Orders_Model->SubmissionCondition();
						}

						// Filter only given orders
						if (!empty($NewOrders_OrderUIDs)) {
							$this->db->where_in('tOrders.OrderUID', $NewOrders_OrderUIDs);
						} else {
							$this->db->where('tOrders.OrderUID IS NULL', NULL, FALSE);
						}

						$this->db->group_by("STR_TO_DATE(tOrderImport.ProcessorChosenClosingDate,'%m/%d/%Y')");
						$this->db->order_by("STR_TO_DATE(tOrderImport.ProcessorChosenClosingDate,'%m/%d/%Y')",FALSE, 'ASC');
						$this->db->limit(1);
						$closing_result=$this->db->get()->row();
						//print_r($closing_result);exit;
						if(!empty($closing_result))
						{
							//print_r($closing_result);exit;
							$Description="Loan Number:" .$closing_result->LoanNumber." has assigned";
							$Des="Order Number:" .$closing_result->OrderNumber." has assigned";
							$resut=$this->Common_Model->OrderAssign($closing_result->OrderUID, $WorkflowModuleUID,$UID);
							if($resut){
							$LogsInsert = array('Module' => 'GetNextOrder', 'Description' => $Description, 'UserID' => $this->loggedid, 'LogDateTime' =>  Date('Y-m-d H:i:s'));
        					$this->db->insert('mAuditLog', $LogsInsert);
        					$tLogsInsert = array('OrderUID' => $closing_result->OrderUID, 'Description' => $Des, 'UserUID' => $UID, 'LogsDateTime' => Date('Y-m-d H:i:s'));
							$this->db->insert('tLogs', $tLogsInsert);
								$order_ids= $closing_result->LoanNumber;
							}
							else
								$order_ids= '';
						}
						else
						{ 
							$this->GetNextOrderQueryByLockexpiration($LoanTypeNames,$function_call,$conditions);
							if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
							{
								$this->Submissions_Orders_Model->SubmissionCondition();
							}

							// Filter only given orders
							if (!empty($NewOrders_OrderUIDs)) {
								$this->db->where_in('tOrders.OrderUID', $NewOrders_OrderUIDs);
							} else {
								$this->db->where('tOrders.OrderUID IS NULL', NULL, FALSE);
							}

							$this->db->order_by('tOrders.OrderEntryDateTime', 'ASC');
							$this->db->limit(1);
							$exp_result=$this->db->get()->row();
							$resut=$this->Common_Model->OrderAssign($exp_result->OrderUID, $WorkflowModuleUID,$UID);
							if($resut){
							$Description="Loan Number:" .$exp_result->LoanNumber." has assigned";
							$Des="Order Number:" .$exp_result->OrderNumber." has assigned";
							$LogsInsert = array('Module' => 'GetNextOrder', 'Description' => $Description, 'UserID' => $this->loggedid, 'LogDateTime' =>  Date('Y-m-d H:i:s'));
        					$this->db->insert('mAuditLog', $LogsInsert);
        					$tLogsInsert = array('OrderUID' => $exp_result->OrderUID, 'Description' => $Des, 'UserUID' => $UID, 'LogsDateTime' => Date('Y-m-d H:i:s'));
							$this->db->insert('tLogs', $tLogsInsert);
								$order_ids= $exp_result->LoanNumber;
							}
							else
							{
								$order_ids= '';
							}

						}
						//print_r($this->db->last_query());exit;
					
				}
				else
				{ //Only Based on lockexpiration 
					$resut=$this->Common_Model->OrderAssign($result->OrderUID, $WorkflowModuleUID,$UID);
					$Description="Loan Number:" .$result->LoanNumber." has assigned";
					$Des="Order Number:" .$result->OrderNumber." has assigned";
						if($resut){
							$LogsInsert = array('Module' => 'GetNextOrder', 'Description' => $Description, 'UserID' => $this->loggedid, 'LogDateTime' =>  Date('Y-m-d H:i:s'));
		        			$this->db->insert('mAuditLog', $LogsInsert);
		        			$tLogsInsert = array('OrderUID' => $result->OrderUID, 'Description' => $Des, 'UserUID' => $UID, 'LogsDateTime' => Date('Y-m-d H:i:s'));
							$this->db->insert('tLogs', $tLogsInsert);
							$order_ids= $result->LoanNumber;
						}
						else
							$order_ids= '';
				}
			}
			else // if lockexpiration is empty
			{
				$this->db->select("tOrders.OrderUID,`OrderNumber`,`LoanNumber`");
				$this->Common_Model->$function_call(false,['OrderByexception'=>true]);
				if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
				{
					$this->Submissions_Orders_Model->SubmissionCondition();
				}
				$this->db->where("(tOrderAssignments.AssignedToUserUID IS NULL OR tOrderAssignments.AssignedToUserUID = '') AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
				if (!empty($LoanTypeNames)) 
				{
					$this->db->where("FIND_IN_SET(tOrders.LoanType,'".$LoanTypeNames."') <> 0", NULL, FALSE);
				}
				else
				{
					$this->db->where("(tOrders.LoanType IS NULL OR tOrders.LoanType = '')",NULL,FALSE);
				}

				// Filter only given orders
				if (!empty($NewOrders_OrderUIDs)) {
					$this->db->where_in('tOrders.OrderUID', $NewOrders_OrderUIDs);
				} else {
					$this->db->where('tOrders.OrderUID IS NULL', NULL, FALSE);
				}

				// DocsOut Get next order FIFO based on signing date & Time
				if ($WorkflowModuleUID == $this->config->item('Workflows')['DocsOut']) {
					
					$this->db->order_by('tOrderImport.DocsOutSigningDate, tOrderImport.DocsOutSigningTime','ASC');
				} else {

					// $this->db->where("(tOrderImport.LockExpiration IS NULL OR tOrderImport.LockExpiration = '')",NULL,FALSE);
					$this->db->order_by('tOrders.OrderEntryDateTime', 'ASC');					
				}

				$this->db->limit(1);
				$order_res=$this->db->get()->row();
				if(!empty($order_res))
				{
					$resut=$this->Common_Model->OrderAssign($order_res->OrderUID, $WorkflowModuleUID,$UID);
					$Description="Loan Number:" .$order_res->LoanNumber." has assigned";
				if($resut){
					$LogsInsert = array('Module' => 'GetNextOrder', 'Description' => $Description, 'UserID' => $this->loggedid, 'LogDateTime' =>  Date('Y-m-d H:i:s'));
					$Des="Order Number:" .$order_res->OrderNumber." has assigned";
    				$this->db->insert('mAuditLog', $LogsInsert);
    				$tLogsInsert = array('OrderUID' => $order_res->OrderUID, 'Description' => $Des, 'UserUID' => $UID, 'LogsDateTime' => Date('Y-m-d H:i:s'));
						$this->db->insert('tLogs', $tLogsInsert);
					$order_ids= $order_res->LoanNumber;
				}
				else
					$order_ids= '';
				}
				else
				{
					$order_ids= '';
				}
			}
		
			
			
		}
		return $order_ids;
	}



	function GetNextOrderQueryByLockexpiration($LoanTypeNames,$function_call,$Conditions)
	{
		
		//$CurrentDate = date('08/17/2021');
		$CurrentDate = date('m/d/Y');
		$NextBusinessDay = date('m/d/Y', strtotime($CurrentDate.' +2 Weekday'));
		$ProcessorClosingDay = date('m/d/Y', strtotime($CurrentDate.' +2 Weekday'));
		$this->db->select("1,`tOrderWorkflows`.`WorkflowModuleUID` ,tOrderImport.LockExpiration,tOrders.OrderUID,`OrderNumber`,`LoanNumber`,tOrderImport.ProcessorChosenClosingDate,COUNT(tOrderImport.LockExpiration) AS import_count ");
			$this->Common_Model->$function_call(false,['OrderByexception'=>true]);
			// $this->db->where("STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') > STR_TO_DATE('".$NextBusinessDay."','%m/%d/%Y')",NULL,FALSE);
			$this->db->where("
				CASE 
					WHEN
						tOrders.LoanType = 'FHA' AND (mCustomerWorkflowModules.FHALockExpirationDate IS NOT NULL AND mCustomerWorkflowModules.FHALockExpirationDate <> '')
					THEN
						STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') >= STR_TO_DATE(mCustomerWorkflowModules.FHALockExpirationDate,'%m/%d/%Y')
					WHEN
						tOrders.LoanType = 'VA' AND (mCustomerWorkflowModules.VALockExpirationDate IS NOT NULL AND mCustomerWorkflowModules.VALockExpirationDate <> '')
					THEN
						STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') >= STR_TO_DATE(mCustomerWorkflowModules.VALockExpirationDate,'%m/%d/%Y')
					ELSE
						STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y') > STR_TO_DATE('".$NextBusinessDay."','%m/%d/%Y')		
					END
			",NULL,FALSE);
			if (!empty($LoanTypeNames)) 
			{
				$this->db->where("FIND_IN_SET(tOrders.LoanType,'".$LoanTypeNames."') <> 0",NULL, FALSE);
			}
			else
			{
				$this->db->where("(tOrders.LoanType IS NULL OR tOrders.LoanType = '')",NULL,FALSE);
			}

			$this->db->where("(tOrderAssignments.AssignedToUserUID IS NULL OR tOrderAssignments.AssignedToUserUID = '') AND (tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
			$this->db->where("tOrders.CustomerUID ",$this->parameters['DefaultClientUID']);
			// $this->db->group_by("STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y')");
			$this->db->group_by("tOrders.LoanType, tOrderImport.LockExpiration");
			// $this->db->order_by("STR_TO_DATE(tOrderImport.LockExpiration,'%m/%d/%Y')",FALSE,'ASC');
			$this->db->order_by("FIELD(tOrders.LoanType,'VA','FHA')");
			$this->db->order_by("tOrderImport.LockExpiration, tOrderWorkflows.DueDateTime", "ASC");
			
	}
	function GetUsersByWorkflow($WorkflowModuleUID){
		$this->db->select("`UserUID`,`UserName`,mUsers.`RoleUID`,mResources.WorkflowModuleUID,mRoleResources.ResourceUID,mResources.CustomerUID,mResources.FieldName,mGetNextOrderUserPermissions.PermissionUserUID,mGetNextOrderUserPermissions.LoanTypeUIDs");
		$this->db->from('mUsers');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID','inner');
		$this->db->join('mRoleResources','mUsers.RoleUID=mRoleResources.RoleUID','left');
		$this->db->join('mResources','mResources.ResourceUID=mRoleResources.ResourceUID','left');
		$this->db->join('mGetNextOrderUserPermissions','mGetNextOrderUserPermissions.PermissionUserUID = mUsers.UserUID AND mGetNextOrderUserPermissions.WorkflowModuleUID = '.$WorkflowModuleUID,'left');
		// $this->db->where('mUsers.CustomerUID IS NULL AND mUsers.LenderUID IS NULL AND mUsers.SettlementAgentUID IS NULL AND mUsers.InvestorUID IS NULL AND mUsers.VendorUID IS NULL OR mUsers.IsInternal = 1');
		/*if($this->session->userdata('RoleType') == 9 ) {
			$this->db->where_not_in('mRole.RoleTypeUID',$this->config->item('Super Admin'));
		}*/
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
	function assign_next_order($GroupUID,$WorkflowModuleUID,$StateUID,$functioncall) 
	{

		if(!empty($StateUID) && $StateUID != 'All') {
			$statedetails = $this->get_statedetails_byStateUID($StateUID);
		}	


		//call function
		$this->Common_Model->{$functioncall}();


		$this->db->select('tOrders.OrderUID,OrderNumber');

		$this->db->join("mCustomerProducts", "mCustomerProducts.CustomerUID = tOrders.OrderUID AND mCustomerProducts.ProductUID = tOrders.ProductUID");
		$this->db->join("mGroupCustomers", "mGroupCustomers.GroupCustomerUID = tOrders.CustomerUID AND mGroupCustomers.GroupUID = ".$GroupUID." ");

		$this->db->join("tOrderAssignments a", " a.OrderUID = tOrders.OrderUID AND a.WorkflowModuleUID = '".$WorkflowModuleUID."' ","LEFT");

		$this->db->where("(a.AssignedToUserUID IS NULL OR a.AssignedToUserUID = '') AND (a.WorkflowStatus IS NULL OR a.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);

		if(isset($statedetails) && !empty($statedetails)) {
			$this->db->where('tOrders.PropertyStateCode',$statedetails->StateCode);
		}

		$Order = $this->db->get()->row();

		if(!empty($Order)) {

			$OrderNumber = $Order->OrderNumber;
			$assignment['OrderUID'] = $Order->OrderUID;
			$assignment['WorkflowModuleUID'] = $WorkflowModuleUID;
			$assignment['AssignedToUserUID'] = $this->loggedid;
			$assignment['AssignedDatetime'] = date('Y-m-d H:i:s');
			$assignment['AssignedByUserUID'] = $this->loggedid;
			$assignment['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Assigned'];
			if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$Order->OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){
				$this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $Order->OrderUID, 'WorkflowModuleUID' => $assignment['WorkflowModuleUID']]);
			}	else {
				$this->Common_Model->save('tOrderAssignments', $assignment);
			}
			return array('OrderNumber'=>$Order->OrderNumber,'OrderUID'=>$Order->OrderUID);
		}

		return array('OrderNumber','OrderUID');
	}

	/**
	*Function workflow user fetch
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 08 September 2020.
	*/
	function get_getnextorder_permission($WorkflowModuleUID,$UserUID)
	{
		$this->db->select('LoanTypeUIDs');
		$this->db->where(array('WorkflowModuleUID' => $WorkflowModuleUID,'PermissionUserUID'=>$UserUID));
		return $this->db->get('mGetNextOrderUserPermissions')->row();
	}

	/**
	*Function workflow user loantype update
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 08 September 2020.
	*/
	function Updateworkflowloantype($CustomerUID, $WorkflowModuleUID, $UserUID, $postloantype) {

		$loantype = !empty($postloantype) ? implode(',', $postloantype) : NULL;

		$ExistingLoanTypeUIDs = FALSE;

	
		$row = $this->get_getnextorder_permission($WorkflowModuleUID,$UserUID);

		if(!empty($row)) {

			$ExistingLoanTypeUIDs = explode(',', $row->LoanTypeUIDs);
			$removedloantype = array_diff($ExistingLoanTypeUIDs, $postloantype);

			$this->db->where(array('WorkflowModuleUID' => $WorkflowModuleUID,'PermissionUserUID'=>$UserUID));
			$this->db->update('mGetNextOrderUserPermissions', array('LoanTypeUIDs' => $loantype));	

		} else {


			$permissioninsert = array('LoanTypeUIDs' => $loantype, 'WorkflowModuleUID' => $WorkflowModuleUID, 'PermissionUserUID' => $UserUID,'ModifiedbyUserUID' => $this->loggedid, 'ModifiedDateTime' =>  Date('Y-m-d H:i:s'));
			$this->db->insert('mGetNextOrderUserPermissions', $permissioninsert);

		}


		$loantypenames = !empty($postloantype) ? $this->db->select('GROUP_CONCAT(LoanTypeName) AS LoanTypeNames',FALSE)->where_in("LoanTypeUID",$postloantype)->get('mLoanType')->row()->LoanTypeNames : '';

		$removedloantypenames = !empty($removedloantype) ? $this->db->select('GROUP_CONCAT(LoanTypeName) AS LoanTypeNames',FALSE)->where_in("LoanTypeUID",$removedloantype)->get('mLoanType')->row()->LoanTypeNames : '';

		$userrow = $this->db->select('UserName')->where(array('UserUID' => $UserUID))->get('mUsers')->row();

		$Description = (!empty($loantypenames)) ? $userrow->UserName . ' - Loan Type '.$loantypenames.' Selected' : '';

		$Description .= (!empty($removedloantypenames)) ?  ' '.$userrow->UserName .' - '.$removedloantypenames.' Loan Types Unselected' : '';

		$LogsInsert = array('Module' => 'GetNextOrder', 'Description' => $Description, 'UserID' => $this->loggedid, 'LogDateTime' =>  Date('Y-m-d H:i:s'));
		$this->db->insert('mAuditLog', $LogsInsert);
		return TRUE;
	}

	/**
	*Function Update lock expiration report 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 23 September 2020.
	*/
	public function Update_FHA_LockExpirationDate($WorkflowModuleUID, $FHA_LockExpirationDate)
	{
		$this->db->where(array(
			'CustomerUID' => $this->parameters['DefaultClientUID'], 
			'WorkflowModuleUID' => $WorkflowModuleUID
		));
		return $this->db->update('mCustomerWorkflowModules', array(
			'FHALockExpirationDate' => $FHA_LockExpirationDate
		));
	}

	/**
	*Function Update lock expiration report 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 23 September 2020.
	*/
	public function Update_VA_LockExpirationDate($WorkflowModuleUID, $VA_LockExpirationDate)
	{
		$this->db->where(array(
			'CustomerUID' => $this->parameters['DefaultClientUID'], 
			'WorkflowModuleUID' => $WorkflowModuleUID
		));
		return $this->db->update('mCustomerWorkflowModules', array(
			'VALockExpirationDate' => $VA_LockExpirationDate
		));
	}

	/**
	*Function Get Lock Expiration Details 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 09 November 2020.
	*/
	public function GetLockExpirationDetails($WorkflowModuleUID)
	{
		return $this->db->select('FHALockExpirationDate, VALockExpirationDate')->from('mCustomerWorkflowModules')->where(array(
			'CustomerUID' => $this->parameters['DefaultClientUID'], 
			'WorkflowModuleUID' => $WorkflowModuleUID
		))->get()->row_array();
	}

}
?>