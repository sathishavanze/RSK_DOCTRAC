<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_Model extends MY_Model
{


	function __construct()
	{
		parent::__construct();
		$this->otherdb = $this->load->database('otherdb', TRUE);
		$this->load->model('Priority_Report/Priority_Report_model');
		if($this->session->userdata('UserUID')){
			$this->UserPermissions = $this->MY_Model->get_userparams();
		}else{
			$this->UserPermissions = new stdClass();			
		}
	}
	function GetStatusByArray($StatusUID)
	{
		$this->db->select('*')->from('mStatus');
		$this->db->where_in('StatusUID', $StatusUID);
		return $this->db->get()->result();
	}

	public function GetOrganizationLogo($OrganizationUID = '')
	{
		$this->db->select('OrganizationLogo')->from('mOrganization');
		$this->db->where_in('OrganizationUID', 1);
		return $this->db->get()->row();
	}

	public function GetCustomerProjects($CustomerUID)
	{
		$this->db->select('*')->from('mProjectCustomer');
		$this->db->where('mProjectCustomer.CustomerUID', $CustomerUID);
		$this->db->join('mCustomer', 'mCustomer.CustomerUID = mProjectCustomer.CustomerUID', 'left');

		return $this->db->get()->result();
	}

	public function getProductDocType($ProductUID)
	{
		$this->db->select('mInputDocType.InputDocTypeUID, mInputDocType.DocTypeName, mProductDocType.ProductUID');
		$this->db->from('mProductDocType');
		$this->db->join('mInputDocType', 'mProductDocType.InputDocTypeUID = mInputDocType.InputDocTypeUID');
		$this->db->where('mProductDocType.ProductUID', $ProductUID);
		return $this->db->get()->result();
	}

	public function GetDocType()
	{
		$this->db->select('*');
		$this->db->from('mInputDocType');
		return $this->db->get()->result();
	}

	public function getProductDocTypeByName($DocTypeName, $ProductUID)
	{
		$this->db->select('mInputDocType.InputDocTypeUID, mInputDocType.DocTypeName');
		$this->db->select($ProductUID . ' AS ProductUID', false);
		$this->db->from('mInputDocType');
		$this->db->where('mInputDocType.DocTypeName', $DocTypeName);
		return $this->db->get()->row();
	}

	public function GetCustomerandProject_row($CustomerUID, $ProjectUID)
	{
		$this->db->select('mProjectCustomer.CustomerUID, mProjectCustomer.ProjectUID, mProjectCustomer.ProjectName, mCustomer.CustomerName, mProjectCustomer.Priority')->from('mProjectCustomer');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID = mProjectCustomer.CustomerUID', 'left');
		$this->db->where('mProjectCustomer.CustomerUID', $CustomerUID);
		$this->db->where('mProjectCustomer.ProjectUID', $ProjectUID);

		return $this->db->get()->row();
	}

	public function GetCustomerandProject_rowByName($CustomerUID, $ProjectName)
	{
		$this->db->select('mProjectCustomer.CustomerUID, mProjectCustomer.ProjectUID, mProjectCustomer.ProjectName, mCustomer.CustomerName, mProjectCustomer.Priority')->from('mProjectCustomer');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID = mProjectCustomer.CustomerUID', 'left');
		$this->db->where('mProjectCustomer.CustomerUID', $CustomerUID);
		$this->db->where('mProjectCustomer.ProjectName', $ProjectName);

		return $this->db->get()->row();
	}


	function getCityDetail($zipcode)
	{

		$zipcode = str_replace('-', '', $zipcode);
		$query = $this->db->get_where('mCities', array('ZipCode' => $zipcode, "mCities.Active" => 1));
		$result = $query->result();

		if (empty($result)) {
			$zipcode_new = substr("$zipcode", 0, 5);
			$query = $this->db->query("SELECT * FROM `mCities` WHERE mCities.ZipCode  LIKE '$zipcode_new%' AND `Active` = 1");
			return $query->result();
		} else {
			return $result;
		}
	}

	function getStateDetail($zipcode)
	{

		$zipcode = str_replace('-', '', $zipcode);
		$query = $this->db->query("SELECT DISTINCT a.StateUID, StateCode, StateName from 
			(select StateUID, StateCode,StateName, Active from mStates)a
			LEFT JOIN 
			(SELECT StateUID,ZipCode from mCities)b  
			ON a.`StateUID`=b.`StateUID` WHERE `ZipCode` = $zipcode AND a.`Active` = 1");
		$result = $query->result();

		if (empty($result)) {
			$zipcode_new = substr("$zipcode", 0, 5);
			$query = $this->db->query("SELECT DISTINCT a.StateUID, StateCode, StateName from 
				(select StateUID, StateCode,StateName, Active from mStates)a
				LEFT JOIN 
				(SELECT StateUID,ZipCode from mCities)b  
				ON a.`StateUID`=b.`StateUID` WHERE `ZipCode` LIKE '$zipcode_new%' AND a.`Active` = 1");
			return $query->result();
		} else {
			return $result;
		}
	}

	function getCountyDetail($zipcode)
	{
		$zipcode = str_replace('-', '', $zipcode);
		$query = $this->db->query("SELECT DISTINCT a.CountyUID, CountyName from 
			(select CountyUID,CountyName, Active from mCounties)a
			LEFT JOIN 
			(SELECT CountyUID,ZipCode from mCities)b  
			ON a.`CountyUID`=b.`CountyUID` WHERE `ZipCode` = $zipcode AND a.`Active` = 1");

		$result = $query->result();

		if (empty($result)) {
			$zipcode_new = substr("$zipcode", 0, 5);
			$query = $this->db->query("SELECT DISTINCT a.CountyUID, CountyName from 
				(select CountyUID,CountyName, Active from mCounties)a
				LEFT JOIN 
				(SELECT CountyUID,ZipCode from mCities)b  
				ON a.`CountyUID`=b.`CountyUID` WHERE `ZipCode` LIKE '$zipcode_new%' AND a.`Active` = 1");
			return $query->result();
		} else {
			return $result;
		}
	}
	function getOrderDetails($OrderUID)
	{
		$this->db->select('tOrders.*, mCustomer.*, mStatus.StatusName, mProjectCustomer.ProjectName,mProducts.ProductName, tOrders.PriorityUID, mOrderPriority.PriorityName, tOrderImport.NSMServicingLoanNumber, mMilestone.MilestoneName, tOrderImport.LockExpiration');
		$this->db->from('tOrders');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mStatus', 'mStatus.StatusUID = tOrders.StatusUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'mProducts.ProductUID = tOrders.ProductUID', 'left');
		$this->db->join('mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mMilestone', 'mMilestone.MilestoneUID = tOrders.MilestoneUID', 'left');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	function getOrderAssignDetails($OrderUID, $workflowUID)
	{
		$this->db->select('mUsers.UserName,CompleteDateTime,mWorkFlowModules.WorkflowModuleName');
		$this->db->from('tOrderAssignments');
		$this->db->join('mUsers', 'mUsers.UserUID = tOrderAssignments.CompletedByUserUID', 'left');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID', 'left');
		$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
		$this->db->where('tOrderAssignments.WorkflowModuleUID', $workflowUID);
		$this->db->where('tOrderAssignments.WorkflowStatus', 5);
		$query = $this->db->get();
		return $query->row();
	}

	public function GetOrderExceptions($OrderUID)
	{
		$this->db->select('*')->from('tOrderException');
		$this->db->select("Date_Format(tOrderException.ExceptionRaisedDateTime,'%m-%d-%Y %H:%i:%s') AS ExceptionRaisedDateTime", true);
		$this->db->select("Date_Format(tOrderException.ExceptionClearedDateTime,'%m-%d-%Y %H:%i:%s') AS ExceptionClearedDateTime", true);
		$this->db->join('mExceptions', 'mExceptions.ExceptionUID = tOrderException.ExceptionTypeUID', 'left');
		$this->db->where('tOrderException.OrderUID', $OrderUID);
		return $this->db->get()->result();
	}


	function Verify_Password($UserUID, $Password)
	{
		$query = $this->db->query("SELECT * FROM mUsers WHERE UserUID = '$UserUID' AND Password = '$Password'");
		if ($query->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function filesize_formatted($path)
	{
		$size = filesize($path);
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}


	function _getCounty_StateUID_ZipCode($ZipCode, $StateCode)
	{

		$this->db->select('*');
		$this->db->from('mCounties');
		$this->db->join('mCities', 'mCities.CountyUID = mCounties.CountyUID', 'INNER');
		$this->db->join('mStates', 'mStates.StateUID = mCities.StateUID', 'INNER');
		$this->db->where('mStates.StateCode', $StateCode);
		$this->db->where('mCities.ZipCode', $ZipCode);
		$query = $this->db->get();
		return $query->row();
	}

	function UpdateNullStatus($OrderUID)
	{
		$this->db->select('StatusUID');
		$this->db->from('tOrders');
		$this->db->join('tOrderAssignment', 'tOrders.OrderUID = tOrderAssignment.OrderUID', 'left');
		$this->db->where(array('tOrders.OrderUID' => $OrderUID));
		$result = $this->db->get()->row()->StatusUID;
		//Stacking Discard - assign NULL

		$myorders = $this->config->item('StackingEnabled');

		//Stacking Discard - assign NULL

		//Audit Discard - to be Null
		$Audit = $this->config->item('AuditEnabled');
		//Audit Discard - to be Null


		//Exception to be Null

		$exception = $this->config->item('ExceptionEnabled');

		//Exception to be Null

		//Review Discard Assign to Null

		$Review = $this->config->item('ReviewEnabled');

		//Review Discard Assign to Null


		if (in_array($result, $myorders)) {
			$tOrderAssignmentArray = array(

				'AssignedToUserUID' => NULL,
				'AssignedDateTime' => NULL,
				'AssignedByUserUID' => NULL,
			);
			$this->db->where(array('OrderUID' => $OrderUID));
			$query = $this->db->update('tOrderAssignment', $tOrderAssignmentArray);

			$response['URL'] = 'MyOrders';
			$response['status'] = 1;
			return $response;
		} else if (in_array($result, $Audit)) {
			$tOrderAssignmentArray = array(

				'AuditAssignedToUserUID' => NULL,
				'AuditAssignedDateTime' => NULL,
				'AuditAssignedByUserUID' => NULL,
			);
			$this->db->where(array('OrderUID' => $OrderUID));
			$query = $this->db->update('tOrderAssignment', $tOrderAssignmentArray);

			$response['URL'] = 'Auditing';
			$response['status'] = 1;
			return $response;
		} else if (in_array($result, $exception)) {
			$tOrderAssignmentArray = array(

				'ExceptionAssignedToUserUID' => NULL,
				'ExceptionAssignedDateTime' => NULL,
				'ExceptionAssignedByUserUID' => NULL,
			);
			$this->db->where(array('OrderUID' => $OrderUID));
			$query = $this->db->update('tOrderAssignment', $tOrderAssignmentArray);

			$response['URL'] = 'Exceptionorders';
			$response['status'] = 1;
			return $response;
		} else if (in_array($result, $Review)) {
			$tOrderAssignmentArray = array(

				'QcAssignedToUserUID' => NULL,
				'QcAssignedDateTime' => NULL,
				'QcAssignedByUserUID' => NULL,
			);
			$this->db->where(array('OrderUID' => $OrderUID));
			$query = $this->db->update('tOrderAssignment', $tOrderAssignmentArray);

			$response['URL'] = 'Revieworders';
			$response['status'] = 1;
			return $response;
		}

		return false;
	}

	function UpdateExportedStatus($tOrders)
	{

		if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['Export']])) {

			$OrderUID = $tOrders->OrderUID;
			$has_shipping = $this->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['Shipping']);

			if ($has_shipping) {

				$StatusUID = $this->config->item("keywords")["Shipping"];
			} else {
				$StatusUID = $this->config->item("keywords")["Completed"];
				$tOrderArray['OrderCompleteDateTime'] = date('Y-m-d H:i:s');
				$tOrderArray['DocumentStatus'] = '';
			}

			$tOA_row['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$tOA_row['CompletedByUserUID'] = $this->loggedid;
			$tOA_row['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			// check is workflow available 

			if (!$this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['Export'])) {
				$tOA_row['OrderUID'] = $OrderUID;
				$tOA_row['WorkflowModuleUID'] = $this->config->item('Workflows')['Export'];
				$tOA_row['AssignedToUserUID'] = $this->loggedid;
				$tOA_row['AssignedDatetime'] = date('Y-m-d H:i:s');
				$tOA_row['AssignedByUserUID'] = $this->loggedid;

				$this->Common_Model->save('tOrderAssignments', $tOA_row);
			} else {
				$this->Common_Model->save('tOrderAssignments', $tOA_row, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['Export']]);
			}

			$tOrderArray['StatusUID'] = $StatusUID;
			$tOrderArray['LastModifiedDateTime'] = date('Y-m-d H:i:s');
			$tOrderArray['LastModifiedByUserUID'] = $this->loggedid;

			$where = array('OrderUID' => $OrderUID);
			$q = $this->db->update('tOrders', $tOrderArray, $where);
			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID, 'Export Completed', Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
		}
	}

	function CheckShippingEnabled($OrderUID)
	{
		// $this->db->select('ProjectUID');
		// $this->db->from('tOrders');
		// $this->db->where('tOrders.OrderUID', $OrderUID);
		// $query = $this->db->get();
		// $ProjectUID = $query->row()->ProjectUID;
		// $this->db->select('IsShipping');
		// $this->db->from('mProjectCustomer');
		// $this->db->where('mProjectCustomer.ProjectUID', $ProjectUID);
		// $query = $this->db->get();
		// return $query->row()->IsShipping;
		$this->db->select('StatusUID');
		$this->db->from('tOrders');
		$WhereOrderUID = array('tOrders.OrderUID' => $OrderUID, 'tOrders.IsOnHold' => 0, 'tOrders.StatusUID' => $this->config->item('keywords')['Shipping']);
		$this->db->where($WhereOrderUID);
		$query = $this->db->get()->num_rows();
		return $query;
	}

	function CheckExportEnabled($OrderUID)
	{
		$this->db->select('ProjectUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$query = $this->db->get();
		$ProjectUID = $query->row()->ProjectUID;
		$this->db->select('IsExport');
		$this->db->from('mProjectCustomer');
		$this->db->where('mProjectCustomer.ProjectUID', $ProjectUID);
		$query = $this->db->get();
		return $query->row()->IsExport;
	}

	function GetShippingStatus($OrderUID)
	{
		$this->db->select('StatusUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$query = $this->db->get();
		return $query->row()->StatusUID;
	}
	function searchForId($id, $array, $slug)
	{
		foreach ($array as $key => $val) {
			if ($val->$slug == $id) {
				return $array[$key];
			}
		}
		return null;
	}

	public function GetProjectLender($ProjectUID)
	{
		$this->db->select('mLender.LenderName, mLender.LenderUID, mProjectLender.ProjectUID')->from('mProjectLender');
		$this->db->join('mLender', 'mLender.LenderUID = mProjectLender.LenderUID', 'left');
		$this->db->where('mProjectLender.ProjectUID', $ProjectUID);
		$this->db->where('mLender.Active', 1);
		return $this->db->get()->result();
	}

	public function GetProjectInvestor($ProjectUID)
	{
		$this->db->select('mInvestors.InvestorName, mInvestors.InvestorUID, mProjectInvestor.ProjectUID')->from('mProjectInvestor');
		$this->db->join('mInvestors', 'mInvestors.InvestorUID = mProjectInvestor.InvestorUID', 'left');
		$this->db->where('mProjectInvestor.ProjectUID', $ProjectUID);
		$this->db->where('mInvestors.Active', 1);
		return $this->db->get()->result();
	}

	public function GetMultipleProjectLender($ProjectUID)
	{
		if ($ProjectUID != 'all' && $ProjectUID != '') {
			$this->db->select('mLender.LenderName, mLender.LenderUID, mProjectLender.ProjectUID')->from('mProjectLender');
			$this->db->join('mLender', 'mLender.LenderUID = mProjectLender.LenderUID', 'left');
			$this->db->where_in('mProjectLender.ProjectUID', $ProjectUID);

			$this->db->group_by('LenderUID');
			return $this->db->get()->result();
		} else {
			return [];
		}
	}

	function GetProjectCustomers()
	{
		$this->db->select("*");
		$this->db->from('mProjectCustomer');
		return $this->db->get()->result();
	}

	function GetClients()
	{
		$this->db->select("*");
		$this->db->from('mCustomer');
		$this->db->where('Active', 1);
		$this->FilterClientsBasedOnRole();
		return $this->db->get()->result();
	}

	function GetModules()
	{
		$this->db->select("*");
		$this->db->from('mWorkFlowModules');
		$this->db->where('Active', 1);
		$this->FilterClientsBasedOnRole();
		return $this->db->get()->result();
	}

	function GetCustomerBasedModules()
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$this->db->select("*");
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID', 'left');
		$this->db->where('Active', 1);
		$this->db->where('mCustomerWorkflowModules.CustomerUID', $CustomerUID);
		$this->db->group_by('mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->order_by('mCustomerWorkflowModules.Position', 'ASC');
		return $this->db->get()->result();
	}

	function GetLenders()
	{
		$this->db->select("*");
		$this->db->from('mLender');
		$this->db->where('Active', 1);
		return $this->db->get()->result();
	}

	function GetStackingAssignedUsers($OrderUID)
	{
		$this->db->select('tOrderAssignment.*');
		$this->db->select('"Stacking" As WorkflowName', false);
		$this->db->select('DATE_FORMAT(AssignedDateTime, "%m/%d/%Y %H:%i:%s") As AssignedDateTime', false);
		$this->db->select('DATE_FORMAT(AssignedCompletedDateTime, "%m/%d/%Y %H:%i:%s") As CompletedDateTime', false);
		$this->db->select('AssignedToUser.UserName as AssignedUserName, AssignedCompletedUser.UserName as AssignedCompletedUserName');
		$this->db->from('tOrderAssignment');
		$this->db->join('mUsers AS AssignedToUser', 'AssignedToUser.UserUID = tOrderAssignment.AssignedToUserUID', 'left');
		$this->db->join('mUsers AS AssignedCompletedUser', 'AssignedCompletedUser.UserUID = tOrderAssignment.AssignedToUserUID', 'left');

		$this->db->where('tOrderAssignment.OrderUID', $OrderUID);
		return $this->db->get()->row();
	}

	function GetQcAssignedUsers($OrderUID)
	{
		$this->db->select('tOrderAssignment.*');
		$this->db->select('"Qc" As WorkflowName', false);
		$this->db->select('DATE_FORMAT(QcAssignedDateTime, "%m/%d/%Y %H:%i:%s") As AssignedDateTime', false);
		$this->db->select('DATE_FORMAT(QcCompletedDateTime, "%m/%d/%Y %H:%i:%s") As CompletedDateTime', false);

		$this->db->select('AssignedToUser.UserName as AssignedUserName, AssignedCompletedUser.UserName as AssignedCompletedUserName');
		$this->db->from('tOrderAssignment');
		$this->db->join('mUsers AS AssignedToUser', 'AssignedToUser.UserUID = tOrderAssignment.QcAssignedToUserUID', 'left');
		$this->db->join('mUsers AS AssignedCompletedUser', 'AssignedCompletedUser.UserUID = tOrderAssignment.QcAssignedToUserUID', 'left');

		$this->db->where('tOrderAssignment.OrderUID', $OrderUID);
		return $this->db->get()->row();
	}

	function GetExceptionAssignedUsers($OrderUID)
	{
		$this->db->select('tOrderAssignment.*');
		$this->db->select('"Exception" As WorkflowName', false);
		$this->db->select('DATE_FORMAT(QcAssignedDateTime, "%m/%d/%Y %H:%i:%s") As AssignedDateTime', false);
		$this->db->select('DATE_FORMAT(QcCompletedDateTime, "%m/%d/%Y %H:%i:%s") As CompletedDateTime', false);

		$this->db->select('AssignedToUser.UserName as AssignedUserName, AssignedCompletedUser.UserName as AssignedCompletedUserName');
		$this->db->from('tOrderAssignment');
		$this->db->join('mUsers AS AssignedToUser', 'AssignedToUser.UserUID = tOrderAssignment.ExceptionAssignedToUserUID', 'left');
		$this->db->join('mUsers AS AssignedCompletedUser', 'AssignedCompletedUser.UserUID = tOrderAssignment.ExceptionCompletedByUserUID', 'left');

		$this->db->where('tOrderAssignment.OrderUID', $OrderUID);
		return $this->db->get()->row();
	}

	function GetAuditAssignedUsers($OrderUID)
	{
		$this->db->select('tOrderAssignment.*');
		$this->db->select('"Audit" As WorkflowName', false);
		$this->db->select('DATE_FORMAT(AuditAssignedDateTime, "%m/%d/%Y %H:%i:%s") As AssignedDateTime', false);
		$this->db->select('DATE_FORMAT(AuditCompletedDateTime, "%m/%d/%Y %H:%i:%s") As CompletedDateTime', false);

		$this->db->select('AssignedToUser.UserName as AssignedUserName, AssignedCompletedUser.UserName as AssignedCompletedUserName');
		$this->db->from('tOrderAssignment');
		$this->db->join('mUsers AS AssignedToUser', 'AssignedToUser.UserUID = tOrderAssignment.AuditAssignedToUserUID', 'left');
		$this->db->join('mUsers AS AssignedCompletedUser', 'AssignedCompletedUser.UserUID = tOrderAssignment.AuditCompletedByUserUID', 'left');

		$this->db->where('tOrderAssignment.OrderUID', $OrderUID);
		return $this->db->get()->row();
	}

	function GetCustomerProducts($CustomerUID)
	{
		$this->db->select('mProducts.*, mCustomer.CustomerName, mCustomer.CustomerUID');
		$this->db->from('mCustomerProducts');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID=mCustomerProducts.CustomerUID');
		$this->db->join('mProducts', 'mProducts.ProductUID=mCustomerProducts.ProductUID');
		$this->db->where('mCustomerProducts.CustomerUID', $CustomerUID);
		$this->db->where('mProducts.Active', STATUS_ONE);
		$this->db->group_by('mProducts.ProductUID');
		$this->db->order_by('mProducts.ProductUID');
		return $this->db->get()->result();
	}

	function GetCustomerSubProducts($CustomerUID, $ProductUID)
	{
		$this->db->select('mSubProducts.*, mCustomer.CustomerName, mCustomer.CustomerUID');
		$this->db->from('mCustomerProducts');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID=mCustomerProducts.CustomerUID');
		$this->db->join('mSubProducts', 'mSubProducts.SubProductUID=mCustomerProducts.SubProductUID');
		$this->db->where('mCustomerProducts.CustomerUID', $CustomerUID);
		$this->db->where('mCustomerProducts.ProductUID', $ProductUID);
		$this->db->group_by('mSubProducts.SubProductUID');
		$this->db->order_by('mSubProducts.SubProductUID');
		return $this->db->get()->result();
	}

	function GetCustomerWorkflows($CustomerUID, $ProductUID, $IsQuantifiable = 0)
	{
		$this->db->select('*');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mWorkFlowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID');
		$this->db->where('mCustomerWorkflowModules.CustomerUID', $CustomerUID);
		$this->db->where('mCustomerWorkflowModules.ProductUID', $ProductUID);
		if (!empty($IsQuantifiable)) {
			$this->db->where('mWorkFlowModules.IsQuantifiable', $IsQuantifiable);
		}
		$this->db->group_by('mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->order_by('mCustomerWorkflowModules.WorkflowModuleUID');
		return $this->db->get()->result();
	}

	function projectcategory($ProjectUID)
	{
		$this->db->select('*,mCategory.CategoryName AS CateName');
		$this->db->from('mProjectCategory');
		$this->db->join('mCategory', 'mProjectCategory.CategoryUID = mCategory.CategoryUID', 'LEFT');
		$this->db->where('ProjectUID', $ProjectUID);
		$this->db->order_by('mProjectCategory.CatPosition');
		$ProjectCategory = $this->db->get()->result();
		$testarray = [];
		foreach ($ProjectCategory as $row) {

			$test = array($row->CategoryUID);
			$testarray = array_merge($testarray, $test);
		}
		return $testarray;
	}

	function ChangeToStacking($OrderUID)
	{

		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('IsStacking', 0);
		$tDocuments = $this->db->get('tDocuments')->row();

		if (!empty($tDocuments)) {
			$this->db->where('DocumentUID', $tDocuments->DocumentUID);
			$this->db->update('tDocuments', ['IsStacking' => 1, 'TypeofDocument' => 'Stacking']);
			return ['DocumentName' => $tDocuments->DocumentName];
		} else {
			return false;
		}
	}
	function GetRoles()
	{

		$this->db->select('*');
		$this->db->from('mRole');
		if (!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);
			$this->db->where_not_in('RoleTypeUID', $this->config->item('Super Admin'));
		}
		// $this->db->where_in('mRole.RoleTypeUID', $this->config->item('Internal Roles'));
		return $this->db->get()->result();
	}
	// function GetQuestion(){
	// 	$this->db->select("*");
	// 	$this->db->from("mQuestionType");
	// 	return $this->db->get()->result();
	// }
	function GetQuestion()
	{
		$this->db->select("*");
		$this->db->from("mQuestion");
		return $this->db->get()->result();
	}
	function GetCustomer()
	{
		$this->db->select("*");
		$this->db->from("mCustomer");
		return $this->db->get()->result();
	}
	function GetCategory()
	{
		$this->db->select("*");
		$this->db->from("mCategory");
		$this->db->where("Active", "1");
		$this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);
		return $this->db->get()->result();
	}
	function GetProject()
	{
		$this->db->select("*");
		$this->db->from("mProjectCustomer");
		return $this->db->get()->result();
	}
	function GetProduct()
	{
		$this->db->select("*");
		$this->db->from("mProducts");
		return $this->db->get()->result();
	}


	function GetHolidays($CurrentYear = FALSE)
	{
		$this->db->where('Active', 1);

		if ($CurrentYear) {
			$this->db->group_start();
			$this->db->where('DATE(HolidayDate) >= "' . date('Y-m-d', strtotime(date('Y')."-01-01")) . '"', NULL, false);
			$this->db->where('DATE(HolidayDate) <="' . date('Y-m-d', strtotime(date('Y')."-12-31")) . '"', NULL, false);
			$this->db->group_end();
			$this->db->order_by('HolidayDate');

		}		

		$query = $this->db->get('mHolidayList');
		return $query->result();
	}


	function getURLSegment($URL, $segment_no = 1)
	{
		$baseurl = base_url();

		$link = str_replace($baseurl, '', $URL);

		$segments = explode('/', $link);

		return $segment[$segment_no - 1];
	}

	function hasResourceAccess($Url)
	{
		if (in_array($this->RoleType, [$this->config->item('SuperAccess')])) {
			return true;
		}
		$resourcename = $this->getURLSegment($Url, 1);
		if (!empty($this->Common_Model->get_row('mRoleResources', ['RoleUID' => $this->RoleType, 'controller' => $resourcename]))) {
			return true;
		}
		return false;
	}
	function getDynamicLeftsubMenu($flow, $ResourceUID)
	{
		$this->db->select('FieldName');
		$this->db->from('mResources');
		$this->db->where('mResources.ResourceUID', $ResourceUID);

		$this->db->where('mResources.Active', 1);
		$parent = $this->db->get()->row();

		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID');
		$this->db->where('mRoleResources.RoleUID', $this->RoleUID);
		$this->db->where('mResources.ParentType', $parent->FieldName);
		$this->db->where('mResources.Active', 1);
		$this->db->order_by('mResources.Position', 'ASC');
		$output = $this->db->get()->result();


		return $output;
	}

	function getDynamicLeftMenu($FieldSection = 'Workflow')
	{
		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID');
		$this->db->where('mResources.FieldSection', $FieldSection);
		$this->db->where('mRoleResources.RoleUID', $this->RoleUID);
		$this->db->where('mResources.Active', 1);
		$this->db->order_by('mResources.Position', 'ASC');
		return $this->db->get()->result();
	}

	function FilterOrderBasedOnRole($querytype = "normal")
	{


		if (in_array($this->RoleType, $this->config->item('SuperAccess'))) {
			return false;
		}

		
		$this->otherdb->select('mUsers.*');
		$this->otherdb->from('mUsers');
		$this->otherdb->where('mUsers.UserUID', $this->loggedid);
		$mUsers = $this->otherdb->get()->row();

		if (in_array($this->RoleType, $this->config->item('Customer Roles'))) {

			if (!empty($mUsers)) {
				if ($querytype == "normal") {
					//$ProjectUIDs = implode(',', $Projects);
					return " AND tOrders.CustomerUID IN (" . $mUsers->CustomerUID . ")";
				} else {
					$this->db->where('tOrders.CustomerUID', $mUsers->CustomerUID);
				}
			}
			return false;
		} elseif (in_array($this->RoleType, $this->config->item('AgentAccess'))) {
			
			$this->otherdb->select('mProjectUser.ProjectUID');
			$this->otherdb->from('mProjectUser');
			$this->otherdb->where('mProjectUser.UserUID', $this->loggedid);
			$mProjectUser = $this->otherdb->get()->result();
			$Projects = [];

			$Projects[] = 0;
			foreach ($mProjectUser as $row) {
				$Projects[] = $row->ProjectUID;
			}



			if (!empty($Projects)) {
				if ($querytype == "normal") {
					$ProjectUIDs = implode(',', $Projects);
					return " AND tOrders.ProjectUID IN (" . $ProjectUIDs . ")";
				} else {
					$this->db->where_in('tOrders.ProjectUID', $Projects);
				}
			}
			return false;
		} elseif (in_array($this->RoleType, $this->config->item('TPO Roles'))) {
			if (!empty($mUsers) && !empty($mUsers->LenderUID)) {
				
				$this->otherdb->select('*');
				$this->otherdb->from('mLender');
				$this->otherdb->where('mLender.LenderUID', $mUsers->LenderUID);
				$mLender = $this->otherdb->get()->row();
				if (!empty($mLender)) {
					if ($querytype == "normal") {
						return " AND tOrderDocumentCheckIn.CorrespondentLenderName = '" . $mLender->LenderName . "'";
					} else {

						$this->db->like('tOrderDocumentCheckIn.CorrespondentLenderName', $mLender->LenderName);
					}
				}
			}
		} elseif (in_array($this->RoleType, $this->config->item('Settlement Agent Roles'))) {
			if (!empty($mUsers) && !empty($mUsers->SettlementAgentUID)) {
				
				$this->otherdb->select('*');
				$this->otherdb->from('mSettlementAgent');
				$this->otherdb->where('mSettlementAgent.SettlementAgentUID', $mUsers->SettlementAgentUID);
				$mSettlementAgent = $this->otherdb->get()->row();

				if (!empty($mSettlementAgent)) {
					if ($querytype == "normal") {
						return " AND tOrderDocumentCheckIn.SettlementAgentName = '" . $mSettlementAgent->SettlementAgentName . "'";
					} else {

						$this->db->where('tOrderDocumentCheckIn.SettlementAgentName = "' . $mSettlementAgent->SettlementAgentName . '"');
					}
				}
			}
		} elseif (in_array($this->RoleType, $this->config->item('Investor Roles'))) {
			if (!empty($mUsers) && !empty($mUsers->InvestorUID)) {

				
				$this->otherdb->select('*');
				$this->otherdb->from('mInvestors');
				$this->otherdb->where('mInvestors.InvestorUID', $mUsers->InvestorUID);
				$mInvestors = $this->otherdb->get()->row();

				if (!empty($mInvestors)) {
					if ($querytype == "normal") {
						return " AND tOrderDocumentCheckIn.InvestorName = '" . $mInvestors->InvestorName . "'";
					} else {

						$this->db->where('tOrderDocumentCheckIn.InvestorName', $mInvestors->InvestorName);
					}
				}
			}
		} elseif (in_array($this->RoleType, $this->config->item('Vendor Roles'))) {
		}
		return false;
	}

	function FilterClientsBasedOnRole()
	{
		if (in_array($this->RoleType, $this->config->item('AgentAccess'))) {

			$ProjectCustomers = [];
			$ProjectUsers = [];

			$mProjectUser = $this->GetAllProjectUsers();

			$ProjectUsers[] = 0;
			foreach ($mProjectUser as $row) {
				$ProjectUsers[] = $row->ProjectUID;
			}

			$mProjectCustomers = $this->GetAllProjectCustomers($ProjectUsers);

			foreach ($mProjectCustomers as $row) {
				$ProjectCustomers[] = $row->CustomerUID;
			}


			if (!empty($ProjectCustomers)) {
				$this->db->where_in('mCustomer.CustomerUID', $ProjectCustomers);
			}
			return true;
		} elseif (in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
			$mCustomerUser = $this->GetCustomerUsers($this->loggedid);
			if (!empty($mCustomerUser)) {
				$this->db->where_in('mCustomer.CustomerUID', $mCustomerUser->CustomerUID);
			}
		}
		return false;
	}

	function GetAllProjectUsers()
	{
		
		$this->otherdb->select('mProjectUser.ProjectUID');
		$this->otherdb->from('mProjectUser');
		$this->otherdb->where('mProjectUser.UserUID', $this->loggedid);
		return $this->otherdb->get()->result();
	}

	function GetAllProjectCustomers($ProjectUIDs)
	{
		
		$this->otherdb->select('mProjectCustomer.ProjectUID, mProjectCustomer.CustomerUID');
		$this->otherdb->from('mProjectCustomer');
		$this->otherdb->where_in('mProjectCustomer.ProjectUID', $ProjectUIDs);
		return $this->otherdb->get()->result();
	}

	function GetCustomerUsers($UserUID)
	{
		
		$this->otherdb->select('mUsers.CustomerUID');
		$this->otherdb->from('mUsers');
		$this->otherdb->where_in('mUsers.UserUID', $UserUID);
		return $this->otherdb->get()->row();
	}

	function GetmUsers($UserUID)
	{
		
		$this->otherdb->select('mUsers.Avatar, mUsers.UserName,mUsers.ProfileColor');
		$this->otherdb->from('mUsers');
		$this->otherdb->where_in('mUsers.UserUID', $UserUID);
		return $this->otherdb->get()->row();
	}

	function advanced_search($post)
	{

		if (isset($post['advancedsearch']['ProductUID']) && $post['advancedsearch']['ProductUID'] != '' && $post['advancedsearch']['ProductUID'] != 'All') {
			$this->db->where_in('tOrders.ProductUID', $post['advancedsearch']['ProductUID']);
		}
		/*if(isset($post['advancedsearch']['LenderUID']) && $post['advancedsearch']['LenderUID'] != '' && $post['advancedsearch']['LenderUID'] != 'All'){
				$this->db->where_in('tOrders.LenderUID',$post['advancedsearch']['LenderUID']);
			}*/
		if (isset($post['advancedsearch']['MilestoneUID']) && $post['advancedsearch']['MilestoneUID'] != '' && $post['advancedsearch']['MilestoneUID'] != 'All') {
			$this->db->where_in('tOrders.MilestoneUID', $post['advancedsearch']['MilestoneUID']);
		}
		if (isset($post['advancedsearch']['StateUID']) && $post['advancedsearch']['StateUID'] != '' && $post['advancedsearch']['StateUID'] != 'All') {
			$this->db->where_in('tOrders.PropertyStateCode', $post['advancedsearch']['StateUID']);
		}
		if (isset($post['advancedsearch']['LoanNo']) && $post['advancedsearch']['LoanNo'] != '' && $post['advancedsearch']['LoanNo'] != 'All') {
			$this->db->where_in('tOrders.LoanNumber', $post['advancedsearch']['LoanNo']);
		}
		if (isset($post['advancedsearch']['Processors']) && !empty($post['advancedsearch']['Processors'])) {
			$this->db->where_in('tOrderImport.LoanProcessor', $post['advancedsearch']['Processors']);
		}
		if (isset($post['advancedsearch']['CustomerUID']) && $post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All') {
			$this->db->where_in('tOrders.CustomerUID', $post['advancedsearch']['CustomerUID']);
		}
		if (isset($post['advancedsearch']['ProjectUID']) && $post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All') {
			$this->db->where_in('tOrders.ProjectUID', $post['advancedsearch']['ProjectUID']);
		}
		if (isset($post['advancedsearch']['ServiceType']) && $post['advancedsearch']['ServiceType'] != '' && $post['advancedsearch']['ServiceType'] != 'All') {
			$this->db->where_in('tOrders.ServiceType', $post['advancedsearch']['ServiceType']);
		}


		if (isset($post['advancedsearch']['FromDate']) && $post['advancedsearch']['FromDate']) {

			$this->DateFilterFromDate($post);
		}
		if (isset($post['advancedsearch']['ToDate']) && $post['advancedsearch']['ToDate']) {
			$this->DateFilterToDate($post);
		}


		if (isset($post['advancedsearch']['Followup']) && $post['advancedsearch']['Followup'] == 'true') {

			$this->db->where('(tOrderFollowUp.FollowUpUID <> "" OR tOrderFollowUp.FollowUpUID IS NOT NULL) ', NULL, false);
		}

		if (isset($post['advancedsearch']['Followupduetoday']) && $post['advancedsearch']['Followupduetoday'] == 'true') {

			$this->db->where('(tOrderFollowUp.FollowUpUID <> "" OR tOrderFollowUp.FollowUpUID IS NOT NULL) ', NULL, false);
			$this->db->where('DATE(tOrderFollowUp.Remainder) = "' . date("Y-m-d") . '"');
		}

		if (isset($post['advancedsearch']['Followupduepast']) && $post['advancedsearch']['Followupduepast'] == 'true') {

			$this->db->where('(tOrderFollowUp.FollowUpUID <> "" OR tOrderFollowUp.FollowUpUID IS NOT NULL) ', NULL, false);
			$this->db->where('DATE(tOrderFollowUp.Remainder) < "' . date("Y-m-d") . '"');
		}

		if (isset($post['advancedsearch']['SubQueues_DocsReceive_Enabled']) && $post['advancedsearch']['SubQueues_DocsReceive_Enabled'] == 'true') {

			$this->db->where('(tOrderQueues.QueueIsDocsReceived = 1) ', NULL, false);
		}

		if (isset($post['advancedsearch']['SubQueues_Status_Enabled']) && $post['advancedsearch']['SubQueues_Status_Enabled'] == 'true') {

			$this->db->where('tOrderQueues.QueueIsStatus', 'Approved');
		}

		///Common Search/////


		if (isset($post['advancedsearch']['CommonProductUID']) && $post['advancedsearch']['CommonProductUID'] != '' && $post['advancedsearch']['CommonProductUID'] != 'All') {
			$this->db->where_in('tOrders.ProductUID', $post['advancedsearch']['CommonProductUID']);
		}
		if (isset($post['advancedsearch']['CommonMilestoneUID']) && $post['advancedsearch']['CommonMilestoneUID'] != '' && $post['advancedsearch']['CommonMilestoneUID'] != 'All') {
			$this->db->where_in('tOrders.MilestoneUID', $post['advancedsearch']['CommonMilestoneUID']);
		}
		if (isset($post['advancedsearch']['CommonStateUID']) && $post['advancedsearch']['CommonStateUID'] != '' && $post['advancedsearch']['CommonStateUID'] != 'All') {
			$this->db->where_in('tOrders.PropertyStateCode', $post['advancedsearch']['CommonStateUID']);
		}

		if (isset($post['advancedsearch']['CommonLoanNo']) && $post['advancedsearch']['CommonLoanNo'] != '' && $post['advancedsearch']['CommonLoanNo'] != 'All') {


			$this->db->where('tOrders.LoanNumber', $post['advancedsearch']['CommonLoanNo']);
		}
		if (isset($post['advancedsearch']['CommonProcessors']) && !empty($post['advancedsearch']['CommonProcessors'])) {
			$this->db->where_in('tOrderImport.LoanProcessor', $post['advancedsearch']['CommonProcessors']);
		}
		if (isset($post['advancedsearch']['CommonCustomerUID']) && $post['advancedsearch']['CommonCustomerUID'] != '' && $post['advancedsearch']['CommonCustomerUID'] != 'All') {
			$this->db->where_in('tOrders.CustomerUID', $post['advancedsearch']['CommonCustomerUID']);
		}
		if (isset($post['advancedsearch']['CommonProjectUID']) && $post['advancedsearch']['CommonProjectUID'] != '' && $post['advancedsearch']['CommonProjectUID'] != 'All') {
			$this->db->where_in('tOrders.ProjectUID', $post['advancedsearch']['CommonProjectUID']);
		}
		if (isset($post['advancedsearch']['CommonFromDate']) && $post['advancedsearch']['CommonFromDate']) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "' . date('Y-m-d', strtotime($post['advancedsearch']['CommonFromDate'])) . '"', NULL, false);
		}
		if (isset($post['advancedsearch']['CommonToDate']) && $post['advancedsearch']['CommonToDate']) {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <="' . date('Y-m-d', strtotime($post['advancedsearch']['CommonToDate'])) . '"', NULL, false);
		}

		if (isset($post['advancedsearch']['IsPendingOrders']) && $post['advancedsearch']['IsPendingOrders']) {
			$WorkflowModuleUID = $post['advancedsearch']['WorkflowModuleUID'];

			$this->db->join('tOrderAssignments AS TOA_'.$WorkflowModuleUID, 'TOA_'.$WorkflowModuleUID.'.OrderUID = tOrders.OrderUID AND TOA_'.$WorkflowModuleUID.'.WorkflowModuleUID = "' .$WorkflowModuleUID. '"', 'left');

			$this->db->group_start();		
			$this->db->where('TOA_'.$WorkflowModuleUID.'.WorkflowStatus != '.$this->config->item('WorkflowStatus')['Completed'].' OR TOA_'.$WorkflowModuleUID.'.WorkflowStatus IS NULL ');
			$this->db->group_end();
		}

		if (isset($post['advancedsearch']['IsWidgetCompletedOrders']) && $post['advancedsearch']['IsWidgetCompletedOrders']) {
			$WorkflowModuleUID = $post['advancedsearch']['WorkflowModuleUID'];

			if ($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) {
				$FetchPendingCountWorkflows = $this->config->item('GatekeepingWidgetFetchPendingCount');
				// Remove key
				unset($FetchPendingCountWorkflows['Workup']);
			} elseif ($WorkflowModuleUID == $this->config->item('Workflows')['Submissions']) {
				$FetchPendingCountWorkflows = $this->config->item('SubmissionWidgetFetchPendingCount');
				unset($FetchPendingCountWorkflows['GateKeeping']);
			}
			
			if (!empty($FetchPendingCountWorkflows) && isset($FetchPendingCountWorkflows)) {
				
				foreach ($FetchPendingCountWorkflows as $WorkflowModuleName => $WorkflowModuleUID) {

					$this->db->join('tOrderAssignments AS TOAC_'.$WorkflowModuleUID, 'TOAC_'.$WorkflowModuleUID.'.OrderUID = tOrders.OrderUID AND TOAC_'.$WorkflowModuleUID.'.WorkflowModuleUID = "' .$WorkflowModuleUID. '"', 'left');

					$this->db->group_start();		
					$this->db->where('TOAC_'.$WorkflowModuleUID.'.WorkflowStatus = '.$this->config->item('WorkflowStatus')['Completed'].' ');
					$this->db->group_end();
				}
			}				
			
		}
		
		if (isset($post['advancedsearch']['MilestoneWidgetPendingFilter']) && $post['advancedsearch']['MilestoneWidgetPendingFilter']) {
			
			$this->db->where('tOrders.MilestoneUID',$post['advancedsearch']['WidgetMilestoneUID']);

			$WorkflowModuleUID = $post['advancedsearch']['WorkflowModuleUID'];
			/**
			*Scheduling Queue 2G, 3A (Pending Email & Checkbox Checkbox not checked) and total (2G,3A 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Saturday 05 September 2020.
			*/
			if ($WorkflowModuleUID == $this->config->item('Workflows')['Scheduling']) {
				
				$this->db->where('(tOrderWorkflowsData.IsEmailEnabled IS NULL OR tOrderWorkflowsData.IsEmailEnabled = "")', NULL, FALSE);
			}
		}
		
		if (isset($post['advancedsearch']['MilestoneWidgetTotalFilter']) && $post['advancedsearch']['MilestoneWidgetTotalFilter']) {			

			$WorkflowModuleUID = $post['advancedsearch']['WorkflowModuleUID'];
			
			if (!empty($this->config->item('MilestoneWidgetEnabledWorkflows')[$WorkflowModuleUID])) {

				$this->db->where_in('tOrders.MilestoneUID', $this->config->item('MilestoneWidgetEnabledWorkflows')[$WorkflowModuleUID]);
			}

			/**
			*Scheduling Queue 2G, 3A (Pending Email & Checkbox Checkbox not checked) and total (2G,3A 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Saturday 05 September 2020.
			*/
			if ($WorkflowModuleUID == $this->config->item('Workflows')['Scheduling']) {
				
				$this->db->where('(tOrderWorkflowsData.IsEmailEnabled IS NULL OR tOrderWorkflowsData.IsEmailEnabled = "")', NULL, FALSE);
			}
		}

		/**
		*TAT missed widgets more than 4 hours of new orders 
		*@author SathishKumar <sathish.kumar@avanzegroup.com>
		*@since Saturday 31 October 2020.
		*/
		if (isset($post['advancedsearch']['IsTATMissedNewOrdersFilter']) && $post['advancedsearch']['IsTATMissedNewOrdersFilter']) {
			
			$this->db->where('tOrderWorkflows.EntryDateTime < DATE_SUB(NOW(), INTERVAL 4 HOUR)',NULL,FALSE);
		}

		// Pending From UW 4 hours - queue date & time
		if (isset($post['advancedsearch']['IsTATMissedPendingFromUWFilter']) && $post['advancedsearch']['IsTATMissedPendingFromUWFilter']) {
			
			$this->db->where('STR_TO_DATE(tOrderImport.QueueDateTime,"%m/%d/%Y %h:%i %p") < DATE_SUB(NOW(), INTERVAL 4 HOUR)',NULL,FALSE);
		}

		// Followup
		if (isset($post['advancedsearch']['IsDocsCheckedConditionPendingFollowupPastDueFilter']) && $post['advancedsearch']['IsDocsCheckedConditionPendingFollowupPastDueFilter']) {
			
			$this->db->where('(tOrderFollowUp.FollowUpUID <> "" OR tOrderFollowUp.FollowUpUID IS NOT NULL) ', NULL, false);
			$this->db->where('DATE(tOrderFollowUp.Remainder) < "' . date('Y-m-d H:i:s') . '"');
		}

		// Followup
		if (isset($post['advancedsearch']['IsDocsCheckedConditionPendingFollowupYetToBeReviewedFilter']) && $post['advancedsearch']['IsDocsCheckedConditionPendingFollowupYetToBeReviewedFilter']) {

			$this->db->where("NOT EXISTS (
				SELECT
					1
				FROM
					tSubQueueCategory
				JOIN mStaticQueues ON mStaticQueues.StaticQueueUID = 13
				JOIN mQueueColumns ON mQueueColumns.WorkflowUID = ".$post['advancedsearch']['WorkflowModuleUID']."
				AND (
					mQueueColumns.SubQueueCategoryUID IS NOT NULL
					AND mQueueColumns.SubQueueCategoryUID <> ''
				)
				JOIN mSubQueueCategory ON mSubQueueCategory.SubQueueCategoryUID = mQueueColumns.SubQueueCategoryUID
				AND mSubQueueCategory.WorkflowModuleUID = ".$post['advancedsearch']['WorkflowModuleUID']."
				AND mSubQueueCategory.SubQueueSection = mStaticQueues.StaticQueueTableName
				WHERE
					tSubQueueCategory.OrderUID = tOrders.OrderUID
				AND tSubQueueCategory.SubQueueCategoryUID = mQueueColumns.SubQueueCategoryUID
				AND FIND_IN_SET(
					tSubQueueCategory.CategoryUID,
					mSubQueueCategory.CategoryUIDs
				)
			)",NULL,FALSE);
			
		}

		return true;
	}

	function OnHoldRpt_advanced_search($post)
	{ {
			if ($post['advancedsearch']['OnHoldStatus'] != '' && $post['advancedsearch']['OnHoldStatus'] != 'All') {
				$this->db->where('tOrderOnhold.OnHoldStatus', $post['advancedsearch']['OnHoldStatus']);
			}
			if ($post['advancedsearch']['OnHoldType'] != '' && $post['advancedsearch']['OnHoldType'] != 'All') {
				$this->db->where('tOrderOnhold.OnHoldType', $post['advancedsearch']['OnHoldType']);
			}
			if ($post['advancedsearch']['ProductUID'] != '' && $post['advancedsearch']['ProductUID'] != 'All') {
				$this->db->where('tOrders.ProductUID', $post['advancedsearch']['ProductUID']);
			}
			if ($post['advancedsearch']['PackageUID'] != '' && $post['advancedsearch']['PackageUID'] != 'All') {
				$this->db->where('tOrders.PackageUID', $post['advancedsearch']['PackageUID']);
			}

			/*if($post['advancedsearch']['LenderUID'] != '' && $post['advancedsearch']['LenderUID'] != 'All'){
				$this->db->where('tOrders.LenderUID',$post['advancedsearch']['LenderUID']);
			}*/
			if ($post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All') {
				$this->db->where('tOrders.CustomerUID', $post['advancedsearch']['CustomerUID']);
			}
			if ($post['advancedsearch']['ProjectUID'] != '' && $post['advancedsearch']['ProjectUID'] != 'All') {
				$this->db->where('tOrders.ProjectUID', $post['advancedsearch']['ProjectUID']);
			}
			if ($post['advancedsearch']['FromDate']) {
				$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "' . date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])) . '"', NULL, false);
			}
			if ($post['advancedsearch']['ToDate']) {
				$this->db->where('DATE(tOrders.OrderEntryDateTime) <="' . date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])) . '"', NULL, false);
			}
		}
		return true;
	}

	/*Self Assign DB Changes Starts*/
	function PickExistingOrderCheck($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*');
		$this->db->from('tOrderAssignments');
		$this->db->join('mUsers', 'mUsers.UserUID = tOrderAssignments.AssignedToUserUID', 'left');
		$this->db->where(array('tOrderAssignments.OrderUID' => $OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $WorkflowModuleUID));
		$this->db->where('AssignedToUserUID !=', NULL);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return array('Status' => 1, 'result' => $query->row());
		} else {
			return array('Status' => 0, 'UserName' => '');
		}
	}


	function OrderAssign($OrderUID, $WorkflowModuleUID, $AssignedToUserUID = '')
	{
		if ($AssignedToUserUID == '') {
			$AssignedToUserUID = $this->loggedid;
		}
		$workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID', $WorkflowModuleUID)->get()->row();

		$assigneduser_row = $this->db->select('UserName')->from('mUsers')->where('UserUID', $AssignedToUserUID)->get()->row();

		$Description = sprintf($this->lang->line('Log_Assigned_Reassigned'), (isset($workflow_names) && !empty($workflow_names->WorkflowModuleName) ? $workflow_names->WorkflowModuleName : ''), (isset($assigneduser_row) && !empty($assigneduser_row->UserName) ? $assigneduser_row->UserName : ''));


		// Get Order Assignment Details
		$query = $this->GettOrderAssignmentDetails($OrderUID, $WorkflowModuleUID);

		if ($query->num_rows() > 0) {

			//duplicate to tOrderAssignments History 
			$tOrderAssignmentsDetails = $query->row();
			if (!empty($tOrderAssignmentsDetails->AssignedToUserUID)) {
				$tOrderAssignments_History = $this->db->insert('tOrderAssignmentsHistory', $query->row());
			}			
			//duplicate to tOrderAssignments History end		

			$tOrderAssignmentsArray = array(
				'WorkflowModuleUID' => $WorkflowModuleUID,
				'AssignedToUserUID' => $AssignedToUserUID,
				'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
				'AssignedByUserUID' => $this->loggedid,
				'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress'],
			);
			$this->db->where(array('tOrderAssignments.OrderUID' => $OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $WorkflowModuleUID));
			$query = $this->db->update('tOrderAssignments', $tOrderAssignmentsArray);


			$this->Common_Model->OrderLogsHistory($OrderUID, $Description, Date('Y-m-d H:i:s'));
			return 1;
		} else {
			$tOrderAssignmentsArray = array(
				'OrderUID' => $OrderUID,
				'WorkflowModuleUID' => $WorkflowModuleUID,
				'AssignedToUserUID' => $AssignedToUserUID,
				'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
				'AssignedByUserUID' => $this->loggedid,
				'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress']
			);
			$query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);


			$this->Common_Model->OrderLogsHistory($OrderUID, $Description, Date('Y-m-d H:i:s'));
			return 1;
		}
	}
	/*Self Assign DB Changes Ends*/



	function Audittrail_insert($data)
	{
		// $this->set_lastactivitydatetime($this->session->userdata('UserUID'));

		$this->db->insert('taudittrail', $data);
		// echo '<pre>';print_r($data);exit;

	}

	function CalculateStatusBasedOnWorkflow($OrderUID, $WorkflowUID)
	{
		$tOrderAssignment = $this->Common_Model->get_row('tOrderAssignment', ['OrderUID' => $OrderUID]);

		$StatusUID = $this->config->item('keywords')['Image Received'];
		if (!empty($tOrderAssignment)) {

			switch ($WorkflowUID) {
				case $this->config->item('Workflows')['Document CheckIn']:
					$StatusUID = $this->config->item('keywords')['Image Received'];
					break;

				case $this->config->item('Workflows')['Stacking']:

					if ($tOrderAssignment->HasDocumentCheckIn == 1 && !empty($tOrderAssignment->DocumentCheckInCompletedByUserUID)) {
						$StatusUID = $this->config->item('keywords')['Document CheckIn Completed'];
					} else {
						$StatusUID = $this->config->item('keywords')['Image Received'];
					}

					break;

				case $this->config->item('Workflows')['Auditing']:

					if ($tOrderAssignment->HasDocumentCheckIn == 1 && !empty($tOrderAssignment->DocumentCheckInCompletedByUserUID)) {
						$StatusUID = $this->config->item('keywords')['Document CheckIn Completed'];
					}

					if ($tOrderAssignment->HasStacking == 1 && !empty($tOrderAssignment->AssignedCompletedByUserUID)) {
						$StatusUID = $this->config->item('keywords')['Stacking Completed'];
					}

					break;

				case $this->config->item('Workflows')['Review']:

					if ($tOrderAssignment->HasDocumentCheckIn == 1 && !empty($tOrderAssignment->DocumentCheckInCompletedByUserUID)) {
						$StatusUID = $this->config->item('keywords')['Document CheckIn Completed'];
					}

					if ($tOrderAssignment->HasStacking == 1 && !empty($tOrderAssignment->AssignedCompletedByUserUID)) {
						$StatusUID = $this->config->item('keywords')['Stacking Completed'];
					}

					if ($tOrderAssignment->HasAuditing == 1 && !empty($tOrderAssignment->AuditCompletedByUserUID)) {
						$StatusUID = $this->config->item('keywords')['Audit Completed'];
					}

					break;

				case $this->config->item('Workflows')['Shipping']:
					$StatusUID = $this->config->item('keywords')['Shipping'];
					break;

				case $this->config->item('Workflows')['Export']:
					$StatusUID = $this->config->item('keywords')['Export'];
					break;

				default:
					$StatusUID = $this->config->item('keywords')['Image Received'];
					break;
			}
		}
		return $StatusUID;
	}

	function ConnectSFTPByProjectUID($mProjectCustomer)
	{

		if (empty($mProjectCustomer)) {
			return false;
		}
		$mSFTP_EmailTemplate = $this->Cron_model->SFTP_Email($project->SFTPUID);


		if (empty($mSFTP_EmailTemplate)) {
			return false;
		}

		$config['hostname'] =  $mSFTP_EmailTemplate->SFTPHost;
		$config['username'] =  $mSFTP_EmailTemplate->SFTPUser;
		$config['password'] =  $mSFTP_EmailTemplate->SFTPPassword;
		$config['debug']    = TRUE;
		$SFTPPath = $mSFTP_EmailTemplate->SFTPPath;


		if (!empty($SFTPPath) && $this->ftp->connect($config)) {
			return true;
		}

		return false;
	}


	function WorkflowQueues_Datatable_Search($post)
	{

		if (!empty($post['search_value'])) {
			$like = "";
			foreach (array_values($post['column_search']) as $key => $item) { // loop column
				// if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
				} else {
					$like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
				}
			}
			$like .= ") ";
			$this->db->where($like, null, false);
		}
	}

	function WorkflowQueues_Datatable_OrderBy($post)
	{
		//sort remove identifiers
		$this->db->_protect_identifiers=false;

		if (!empty($post['order'])) {
			// here order processing
			if ($post['column_order'][$post['order'][0]['column']] != '') {
				$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
			}
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
		$this->db->_protect_identifiers=true;
		//sort readd identifiers

	}

	function ChecklistReport_Datatable_Search($post)
	{

		if (!empty($post['search_value'])) {
			$like = "";
			foreach ($post['column_search'] as $key => $item) { // loop column
				// if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
				} else {
					$like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
				}
			}
			$like .= ") ";
			$this->db->where($like, null, false);
		}
	}

	function ChecklistReport_Datatable_OrderBy($post)
	{

		if (!empty($post['order'])) {
			// here order processing
			if ($post['column_order'][$post['order'][0]['column']] != '') {
				$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
			}
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function GetAuditingQueueOrders()
	{
		$status[0] = $this->config->item('keywords')['New Order'];
		$status[1] = $this->config->item('keywords')['Waiting For Images'];
		$status[2] = $this->config->item('keywords')['Image Received'];
		$status[21] = $this->config->item('keywords')['Document CheckIn Completed'];
		$status[3] = $this->config->item('keywords')['Stacking In Progress'];
		$status[4] = $this->config->item('keywords')['Stacking Completed'];
		$status[17] = $this->config->item('keywords')['Audit In Progress'];


		/*########## GEt Workflow results using another db connection ############*/
		
		$this->otherdb->select('mWorkFlowModules.*');
		$this->otherdb->from('mWorkFlowModules');
		$this->otherdb->where('mWorkFlowModules.WorkflowModuleUID < ' . $this->config->item('Workflows')['Auditing']);
		$mWorkFlowModules = $this->otherdb->get()->result();


		/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
		$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
		foreach ($mWorkFlowModules as $key => $value) {

			$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

			$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
		}

		foreach ($mWorkFlowModules as $key => $value) {
			$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
			$this->otherdb->group_start();
			$this->otherdb->where($Case_Where, NULL, FALSE);
			$this->otherdb->group_end();
		}

		$this->otherdb->where_in('tOrders.StatusUID', $status);


		$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();



		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Auditing"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID');



		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');
		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Auditing'] . "'");

		$this->db->where_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

		$this->db->group_by('tOrders.OrderUID');


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Auditing'] . "' AND tOrderWorkflows.IsPresent = 1 THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Auditing'] . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Auditing']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/
	}

	function GetCompletedQueueOrders()
	{
		$status[0] = $this->config->item('keywords')['ClosedandBilled'];
		$status[1] = $this->config->item('keywords')['ClosingCompleted'];

		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->where_in('tOrders.StatusUID', $status);
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}

	function GetCompletedQueueOrdersByWorkflow($post, $Conditions = ['IgnoreQueues' => TRUE])
	{

		// Check WorkflowModuleUID is not empty
		$WorkflowModuleUID = $post['WorkflowModuleUID'];

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
		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID, $Conditions['IgnoreQueues']);

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}

	function GetDocsOutPendingQueueOrders($MileStoneUIDs)
	{
		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

		//filter order when withdrawal enabled
		// $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)",NULL,FALSE);

		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		if (!empty($MileStoneUIDs)) {
			$this->db->where_in('tOrders.MilestoneUID', $MileStoneUIDs);
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		$this->db->group_by('tOrders.OrderUID');
	}

	function GetCancelledOrders()
	{
		$status[0] = $this->config->item('keywords')['Cancelled'];
		$CancelledOrders_Milestones = $this->config->item('CancelledOrders_Milestones');
		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->where_in('tOrders.StatusUID', $status);
		$this->db->or_where_in('tOrders.MilestoneUID', $CancelledOrders_Milestones);


		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}

	function GetPreScreenOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['PreScreen'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['PreScreen']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["PreScreen"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['PreScreen'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['PreScreen'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['PreScreen']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}


		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['PreScreen'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['PreScreen'] . "' 
				THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}

		/*Milestone Is 2G */
		// $this->db->where('("tOrderImport.MilestoneUID",)'$this->config->item('CD_Orders_Milestones'));

		// ScheduledDate is blank
		// $this->db->where('(tOrderImport.ScheduledDate IS NULL OR tOrderImport.ScheduledDate = "")', NULL, FALSE);  


		// ClosingDisclosureSendDate is blank
		// $this->db->where('(tOrderImport.ClosingDisclosureSendDate IS NULL OR tOrderImport.ClosingDisclosureSendDate = "")', NULL, FALSE);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}



	function GetDocsOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['DocsOut'];



		$status[] = $this->config->item('keywords')['Cancelled'];



		/*########## GEt Workflow results using another db connection ###########*/

		/** 
		 * Dependent Workflows
		 * @author Sathis Kannan <sathish.kannan@avanzegroup.com> 
		 * @since Wednesday 15 July 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['DocsOut']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["DocsOut"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['DocsOut'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		if (isset($Conditions['ExcludeOrderBy'])) {
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');
		} else {

			if (!isset($Conditions['OrderByexception'])) {
				$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
			} else {
				$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');
			}
		}

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['DocsOut'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['DocsOut']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}


		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['DocsOut'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['DocsOut'] . "' 
				THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}


		// $this->db->where_in('tOrders.MilestoneUID', $this->config->item('CD_Orders_Milestones'));

		// DocsOutSigningDate is blank
		$this->db->where("(tOrderImport.DocsOutSigningDate IS NOT NULL AND tOrderImport.DocsOutSigningDate <> '') ", NULL, FALSE);


		// // DocsOutClosingDisclosureSendDate is blank

		$this->db->where("(tOrderImport.DocsOutClosingDisclosureSendDate IS NOT NULL AND tOrderImport.DocsOutClosingDisclosureSendDate <> '')");

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');

		// Order By
		if (isset($Conditions['ExcludeOrderBy'])) {
			$this->db->order_by('STR_TO_DATE(tOrderImport.DocsOutSigningDate,"%m/%d/%Y"), STR_TO_DATE(tOrderImport.DocsOutSigningTime,"%h:%i %p")','ASC',FALSE);
		}
	}



	function GetSignedDocsOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['SignedDocs'];



		$status[] = $this->config->item('keywords')['Cancelled'];



		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Sathis Kannan <sathish.kannan@avanzegroup.com> 
		 * @since Wednesday 15 July 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['SignedDocs']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["SignedDocs"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['SignedDocs'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['SignedDocs'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['SignedDocs']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}


		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['SignedDocs'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['SignedDocs'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();


		/*Milestone Is 2G */
		// $this->db->where('tOrders.MilestoneUID', $this->config->item('Milestones')['3A']);

		// ScheduledDate is Yesderday Or Before
		// $this->db->where("STR_TO_DATE(tOrderImport.SigningDate, '%m/%d/%Y') < DATE(NOW())", NULL, FALSE);


		// SigningDate is Yesderday Or Before
		$currentdate = date('Y-m-d');

		$this->db->where('tOrderImport.SigningDate <', $currentdate);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception'])){
		$query = $this->db->group_by('tOrders.OrderUID');
		return $query;
		}
	}
	function GetFundingConditionsOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['FundingConditions'];



		$status[] = $this->config->item('keywords')['Cancelled'];



		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Sathis Kannan <sathish.kannan@avanzegroup.com> 
		 * @since Wednesday 15 July 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['FundingConditions']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["FundingConditions"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['FundingConditions'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['FundingConditions'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['FundingConditions']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}


		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['FundingConditions'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['FundingConditions'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();


		// Funding Conditions queue conditions
		// $QueueMilestones = $this->config->item("FundingConditionsMilestones");
		// $this->db->where_in('tOrders.MilestoneUID', $QueueMilestones);

		// Funding Conditions queue conditions
		$ExceptQueue = $this->config->item("FundingConditionsExceptQueue");
		$this->db->group_start();
		$this->db->where_not_in('tOrderImport.Queue', $ExceptQueue);
		$this->db->or_where('tOrderImport.Queue IS NULL');
		$this->db->group_end();

		// ScheduledDate is blank
		// $this->db->where('tOrderImport.ScheduledDate IS NULL',NULL ,FALSE);  


		// ClosingDisclosureSendDate is blank

		// $this->db->where('tOrderImport.ClosingDisclosureSendDate IS NULL',NULL ,FALSE); 

		// $this->db->where('tOrderImport.ClosingDisclosureSendDate IS NULL OR tOrderImport.ClosingDisclosureSendDate = "")', NULL, FALSE);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception'])){
			$query = $this->db->group_by('tOrders.OrderUID');
			return $query;
		}
	}


	function GetOrderDetailsByOrderUID($OrderUID)
	{
		$this->db->select('tOrders.*, mStatus.StatusName, mCustomer.CustomerName, mProjectCustomer.ProjectName, mLender.LenderName');
		$this->db->from('tOrders');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');



		$this->db->where_in('OrderUID', $OrderUID);
		return $this->db->get()->row();
	}

	function GetOrderDetailsByOrderUIDs($OrderUIDs)
	{
		if (empty($OrderUIDs)) {
			return [];
		}
		$this->db->select('tOrders.*, mStatus.StatusName, mCustomer.CustomerName, mProjectCustomer.ProjectName, mLender.LenderName');
		$this->db->from('tOrders');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');



		$this->db->where_in('OrderUID', $OrderUIDs);
		return $this->db->get()->result();
	}

	function GetProjectUserDetails($OrderUID)
	{
		$this->db->select('tOrders.ProjectUID AS ProjectUID, mUsers.UserUID AS UserUID, mUsers.EmailID AS EmailID,tOrders.OrderNumber AS OrderNumber');
		$this->db->from('tOrders');
		$this->db->join('mProjectUser', 'tOrders.ProjectUID = mProjectUser.ProjectUID', 'LEFT');
		$this->db->join('mUsers', 'mProjectUser.UserUID = mUsers.UserUID', 'LEFT');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('mProjectUser.CanReceiveExceptionMails', 1);
		$query = $this->db->get()->result();
		return $query;
	}
	function CancelOrderRevoke($OrderUID)
	{
		$this->db->select('Cancel_Temp_StatusUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$CancelOrderRevoke = $this->db->get()->row()->Cancel_Temp_StatusUID;

		$UpdateStatus = array('StatusUID' => $CancelOrderRevoke, 'LastModifiedDateTime' => date('Y-m-d H:i:s'), 'LastModifiedByUserUID' => $this->loggedid);
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->update('tOrders', $UpdateStatus);
		return 1;
	}

	function GetOrderDetails_ByKey($key, $value)
	{

		$this->db->select('tOrders.*, mProjectCustomer.ProjectName, mCustomer.CustomerName');
		$this->db->from('tOrders');
		$this->db->join('mProjectCustomer', 'mProjectCustomer.ProjectUID=tOrders.ProjectUID');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID=tOrders.CustomerUID');
		$this->db->where($key, $value);
		return $this->db->get()->row();
	}

	function GetSubproductByProduct($ProductUID)
	{
		$this->db->select("*");
		$this->db->from('msubproducts');
		$this->db->where(array("ProductUID" => $ProductUID));
		$query = $this->db->get();
		return $query->result();
	}
	function GetWorkflowDetaiils($CustomerUID = FALSE)
	{

		// $this->db->where(array("mWorkFlowModules.WorkflowModuleUID !="=>'5'));
		/* $this->db->where(array('Active'=>'1'));
		$this->db->where_not_in('WorkflowModuleUID','6');
		$query = $this->db->get('mWorkFlowModules');
		return $query->result(); */
		$this->db->select('mWorkFlowModules.*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID', 'inner');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID=mResources.WorkflowModuleUID');
		$this->db->where(array(
			'mResources.FieldSection' => 'WORKFLOW',
			'mRoleResources.RoleUID' => $this->RoleUID
		));
		if ($CustomerUID == FALSE) {
			$this->db->like('mResources.CustomerUID', $this->parameters['DefaultClientUID']);
		} else {
			$this->db->like('mResources.CustomerUID', $CustomerUID);
		}
		$this->db->where_not_in('mWorkFlowModules.WorkflowModuleUID', '6');
		$this->db->where('mWorkFlowModules.Active',STATUS_ONE);
		return $this->db->get()->result();
	}

	function mStatusDetails()
	{
		$query = $this->db->get_where('mStatus', array('Active' => 1));
		return $query->result();
	}

	public function ProductsDetails()
	{
		$this->db->select('*');
		$this->db->from('mProducts');
		$this->db->where('mProducts.Active', 1);
		return $this->db->get()->result();
	}
	public function SubProductsDetails()
	{
		$this->db->select('*');
		$this->db->from('mSubProducts');
		$this->db->where('mSubProducts.Active', 1);
		return $this->db->get()->result();
	}
	public function ProjectDetails()
	{
		$this->db->select('*');
		$this->db->from('mProjectCustomer');
		$this->db->where('mProjectCustomer.Active', 1);
		return $this->db->get()->result();
	}

	public function GetWorkflowAssignmentDetails($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*')->from('tOrderAssignments');
		$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
		$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('tOrderAssignments.AssignedToUserUID', $this->loggedid);
		$this->db->where_not_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Onhold'], $this->config->item('WorkflowStatus')['Completed']]);
		return $this->db->get()->row();
	}

	public function IsWorkflowCompleted($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('OrderUID')->from('tOrderAssignments');
		$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
		$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Completed']]);
		return $this->db->get()->num_rows();
	}

	public function IsWorkflowAvailable($OrderUID, $WorkflowModuleUID)
	{

		// Check is not in followup
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $OrderUID]);

		if (!empty($tOrders)) {
			// if in followup return false
			if ($tOrders->IsFollowUp == STATUS_ONE) {
				return false;
			}
		}
		$is_workflowavailable = $this->Is_given_workflow_available($OrderUID, $WorkflowModuleUID);

		// Checki is workflow available.
		if (!$is_workflowavailable) {
			return false;
		}

		// Chekci is all previous workflows completed.
		$Workflows = $this->GetOrder_PreviousWorkflow($OrderUID, $WorkflowModuleUID);


		foreach ($Workflows as $key => $workflowuid) {

			$this->db->select('*')->from('tOrderAssignments');
			$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
			$this->db->where('tOrderAssignments.WorkflowModuleUID', $workflowuid->WorkflowModuleUID);
			$this->db->where_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Completed']]);
			$is_completed = $this->db->get()->num_rows();

			if (empty($is_completed)) {
				return false;
			}
		}

		// check this workflow assigned to user
		if (!in_array($this->RoleType, $this->config->item('SuperAccess'))) {
			$this->db->select('1')->from('tOrderAssignments');
			$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
			$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('tOrderAssignments.AssignedToUserUID', $this->loggedid);
			// $this->db->where_not_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Completed']]);

			$is_this_workflow_available = $this->db->get()->num_rows();

			if (!$is_this_workflow_available) {
				return false;
			}
		}
		return true;
	}

	public function IsWorkflowAvailableForDocUpload($OrderUID, $WorkflowModuleUID)
	{


		$is_workflowavailable = $this->Is_given_workflow_available($OrderUID, $WorkflowModuleUID);

		// Checki is workflow available.
		if (!$is_workflowavailable) {
			return false;
		}

		// Chekci is all previous workflows completed.
		$Workflows = $this->GetOrder_PreviousWorkflow($OrderUID, $WorkflowModuleUID);



		foreach ($Workflows as $key => $workflowuid) {

			$this->db->select('*')->from('tOrderAssignments');
			$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
			$this->db->where('tOrderAssignments.WorkflowModuleUID', $workflowuid->WorkflowModuleUID);
			$this->db->where_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Completed']]);
			$is_completed = $this->db->get()->num_rows();

			if (empty($is_completed)) {
				return false;
			}
		}

		// check this workflow assigned to user
		if (!in_array($this->RoleType, $this->config->item('SuperAccess'))) {
			$this->db->select('1')->from('tOrderAssignments');
			$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
			$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('tOrderAssignments.AssignedToUserUID', $this->loggedid);
			$this->db->where_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Completed']]);

			$is_this_workflow_available = $this->db->get()->num_rows();

			if ($is_this_workflow_available > 0) {
				return false;
			}
		} else {

			$this->db->select('1')->from('tOrderAssignments');
			$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
			$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);

			$is_this_workflow_assignment_available = $this->db->get()->num_rows();

			if ($is_this_workflow_assignment_available > 0) {

				$this->db->select('1')->from('tOrderAssignments');
				$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
				$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
				$this->db->where_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Completed']]);

				$is_this_workflow_available = $this->db->get()->num_rows();

				if ($is_this_workflow_available > 0) {
					return false;
				}
			} else {
				return false;
			}
		}
		return true;
	}


	public function GetOrderWorkflows($OrderUID)
	{
		$this->db->select('tOrders.*, mCustomerWorkflowModules.*, mWorkFlowModules.*');
		$this->db->from('tOrders');
		$this->db->join('mCustomerWorkflowModules', 'tOrders.CustomerUID=mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		return $this->db->get()->result();
	}


	public function GetOrderAvailableWorkflows($OrderUID)
	{
		$this->db->select('WorkflowModuleUID');
		$this->db->from('tOrderWorkflows');
		$this->db->where('OrderUID', $OrderUID);
		return $this->db->get()->result();
	}

	public function GetOrder_PreviousWorkflow($OrderUID, $WorkflowModuleUID)
	{

		$this->db->select('tOrderWorkflows.*, mWorkFlowModules.*');
		$this->db->from('tOrderWorkflows');
		$this->db->join('tOrders', 'tOrders.OrderUID = tOrderWorkflows.OrderUID');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');

		$this->db->where('tOrderWorkflows.OrderUID', $OrderUID);
		$this->db->where('tOrderWorkflows.WorkflowModuleUID < "' . $WorkflowModuleUID . '"');
		$this->db->where('tOrderWorkflows.IsPresent', STATUS_ONE);

		$this->db->order_by('mWorkFlowModules.WorkflowModuleUID', 'ASC');
		return $this->db->get()->result();

		// $this->db->select('mCustomerWorkflowModules.*, mWorkFlowModules.*');
		// $this->db->from('tOrders');
		// $this->db->join('mCustomerWorkflowModules', 'tOrders.CustomerUID=mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID');
		// $this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
		// $this->db->where('tOrders.OrderUID', $OrderUID);
		// $this->db->where('mCustomerWorkflowModules.WorkflowModuleUID < "' . $WorkflowModuleUID . '"');
		// $this->db->order_by('mWorkFlowModules.WorkflowModuleUID', 'ASC');
		// return $this->db->get()->result();

	}

	public function Is_given_workflow_available($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('1');
		$this->db->from('tOrderWorkflows');
		$this->db->where('tOrderWorkflows.OrderUID', $OrderUID);
		$this->db->where('tOrderWorkflows.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('tOrderWorkflows.IsPresent', STATUS_ONE);
		if ($this->db->get()->num_rows()) {
			return true;
		}

		return false;
	}

	public function Is_given_orderworkflow_available($OrderUID, $WorkflowModuleUID)
	{

		if ($this->Common_Model->Is_given_workflow_available($OrderUID, $WorkflowModuleUID)) {
			if (($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] || $WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) &&  $this->Common_Model->isworkflow_forceenabled($OrderUID, $WorkflowModuleUID)) {
				return true;
			}
			$DependentWorkflowModuleUID = $this->getDependentworkflows($WorkflowModuleUID);
			if (empty($DependentWorkflowModuleUID)) {
				return true;
			} else {
				//check dependent workflow available
				$PresentDependentWorkflowUID = [];
				$DependentWorkflowModuleUID = array_unique($DependentWorkflowModuleUID);
				foreach ($DependentWorkflowModuleUID as $key => $DependentWorkflowUID) {
					$available = $this->Common_Model->Is_given_workflow_available($OrderUID, $DependentWorkflowUID);
					if ($available) {
						$PresentDependentWorkflowUID[] = $DependentWorkflowUID;
					}
				}
				return !empty($PresentDependentWorkflowUID) ? $this->Common_Model->checkGivenWorkflowsCompleted($OrderUID, $PresentDependentWorkflowUID) : false;
			}
		}
		return false;
	}

	/**
	 *@description Function to getPageLessWorkflows
	 *
	 * @param $OrderUID
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.3.2020 
	 * @version dynamic workflow 
	 *
	 */
	function getPageLessWorkflows($OrderUID)
	{

		$torders = $this->get_row('tOrders', ['OrderUID' => $OrderUID]);

		$CustomerWorkflows = $this->Common_Model->getCustomer_Workflows($torders->CustomerUID);

		$WorkflowModuleUIDs = array_column($CustomerWorkflows, "WorkflowModuleUID");

		$Resources = $this->Common_Model->getResourcePageWorkflows();

		$ResourcePageWorkflows = array_column($Resources, "WorkflowModuleUID");

		$RemainingWorkflows = array_diff($WorkflowModuleUIDs, $ResourcePageWorkflows);

		return $RemainingWorkflows;
		if (empty($RemainingWorkflows)) {
			return [];
		}

		$this->db->select('*');
		$this->db->from('mWorkFlowModules');
		$this->db->where_in('mWorkFlowModules.WorkflowModuleUID', $RemainingWorkflows);
		return $this->db->get()->result();
	}

	/**
	 *@description Function to getResourcePageWorkflows
	 *
	 * @param 
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.3.2020 
	 * @version Dynamic workflow 
	 *
	 */
	function getResourcePageWorkflows()
	{

		$this->db->select('mResources.WorkflowModuleUID');
		$this->db->from('mResources');
		$this->db->where('FieldSection', "ORDERWORKFLOW");
		$this->db->where('WorkflowModuleUID IS NOT NULL');
		return $this->db->get()->result();
	}

	public function Is_orderworkflow_buttonavailable($OrderUID, $WorkflowModuleUID)
	{

		if ($this->Common_Model->Is_given_workflow_available($OrderUID, $WorkflowModuleUID)) {
			$DependentWorkflowModuleUID = $this->getDependentworkflows($WorkflowModuleUID);

			$PresentDependentWorkflowUID = [];

			if (!empty($DependentWorkflowModuleUID)) {
				//check dependent workflow available
				$DependentWorkflowModuleUID = array_unique($DependentWorkflowModuleUID);
				foreach ($DependentWorkflowModuleUID as $key => $DependentWorkflowUID) {
					$available = $this->Common_Model->Is_given_workflow_available($OrderUID, $DependentWorkflowUID);
					if ($available) {
						$PresentDependentWorkflowUID[] = $DependentWorkflowUID;
					}
				}
			}

			$StageStatus = false;
			if (empty($PresentDependentWorkflowUID)) {
				$StageStatus = true;
			} else if ($this->Common_Model->checkGivenWorkflowsCompleted($OrderUID, $PresentDependentWorkflowUID)) {
				$StageStatus = true;
			}
			if ($StageStatus) {
				return !$this->Common_Model->checkGivenWorkflowsCompleted($OrderUID, [$WorkflowModuleUID]);
			}
		}
		return false;
	}

	/**
	 *@description Function to checkGivenWorkflowsCompleted
	 *
	 * @param WorkflowModuleUID
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return bool 
	 * @since 13.3.2020 
	 * @version Vesion/Task 
	 *
	 */
	function checkGivenWorkflowsCompleted($OrderUID, $WorkflowModuleUIDs)
	{
		/*Code*/
		$WorkflowModuleUIDs = array_unique($WorkflowModuleUIDs);
		$this->db->select('tOrderAssignments.OrderUID');
		$this->db->from('tOrderAssignments');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
		$this->db->where_in('WorkflowModuleUID', $WorkflowModuleUIDs);
		$completedworkflows = $this->db->get()->num_rows();
		if (count($WorkflowModuleUIDs) == $completedworkflows) return true;
		else return false;
	}

	/**
	 *@description Function to getWorkflowByPage
	 *
	 * @param $Page
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return bool 
	 * @since 13.3.2020 
	 * @version DocTrack Dynamic Workflow 
	 *
	 */
	function getWorkflowByPage($Page)
	{
		/*Query*/
		$this->db->select('WorkflowModuleUID');
		$this->db->from('mResources');
		$this->db->where('FieldSection', "ORDERWORKFLOW");
		$this->db->where('controller', $Page);
		return $this->db->get()->row();
	}

	public function OrderReverseWorkflow($OrderUID)
	{
		$this->db->select('*,mWorkFlowModules.WorkflowModuleName,mUsers.UserName AS AssignedUserName,CompletedUser.UserName AS CompletedUserName');
		$this->db->from('tOrderAssignments');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID');
		$this->db->join('mUsers', 'mUsers.UserUID=tOrderAssignments.AssignedToUserUID');
		$this->db->join('mUsers AS CompletedUser', 'mUsers AS CompletedUser.UserUID=tOrderAssignments.CompletedByUserUID');
		$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
		$this->db->where_not_in('mWorkFlowModules.WorkflowModuleUID', $this->config->item('NonAssignableWorkflows'));
		$this->db->where_in('mWorkFlowModules.WorkflowModuleUID', $this->config->item('ReverseWorkflows'));
		return $this->db->get()->result();
	}
	public function OrderReverseWorkflowStatus($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$OrderDetails = $this->db->get()->row();

		$this->db->select('*');
		$this->db->from('tOrderWorkflows');
		$this->db->where(array('tOrderWorkflows.OrderUID' => $OrderUID));
		$CustomerProductWorkflows = $this->db->get()->result();

		$WorkflowsStatus = [];

		foreach ($CustomerProductWorkflows as $key => $value) {
			$this->db->select('tOrderAssignments.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName');
			$this->db->from('tOrderAssignments');
			$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID=tOrderAssignments.WorkflowModuleUID');
			$this->db->where(array('tOrderAssignments.OrderUID' => $OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $value->WorkflowModuleUID));
			$this->db->where('tOrderAssignments.WorkflowStatus', 5);
			$this->db->where_in('mWorkFlowModules.WorkflowModuleUID', $this->config->item('ReverseWorkflows'));
			$this->db->where_not_in('mWorkFlowModules.WorkflowModuleUID', $this->config->item('NonAssignableWorkflows'));
			$OrderWorkflows = $this->db->get()->row();
			array_push($WorkflowsStatus, $OrderWorkflows);
		}

		return array_filter($WorkflowsStatus);
	}


	/*Function to get product based on customer*/
	function get_customerproducts($CustomerUID)
	{
		$this->db->select("mCustomerProducts.ProductUID,ProductName,BulkImportFormat,BulkImportTemplateName,BulkImportTemplateXMLName, mCustomerProducts.BulkAssignFormat, mCustomerProducts.BulkAssignTemplateName, mCustomerProducts.PayOffBulkUpdateFormat, mCustomerProducts.PayOffBulkUpdateTemplateName, mCustomerProducts.BulkWorkflowEnableFormat, mCustomerProducts.BulkWorkflowEnableTemplateName, mCustomerProducts.DocsOutBulkUpdateFormat,mCustomerProducts.DocsOutBulkUpdateTemplateName");
		$this->db->from('mCustomerProducts');
		$this->db->join('mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID');
		$this->db->where("mCustomerProducts.CustomerUID", $CustomerUID);
		$this->db->where("mProducts.Active", 1);
		$this->db->group_by("mCustomerProducts.ProductUID");
		$this->db->order_by("ProductName");
		return $this->db->get()->result();
	}

	public function get_customer_product_project_row($CustomerUID, $ProductUID, $ProjectUID)
	{
		$this->db->select('mProjectCustomer.CustomerUID, mProjectCustomer.ProjectUID, mProjectCustomer.ProjectName, mCustomer.CustomerName, mProjectCustomer.Priority,ProductName,mProjectCustomer.ProductUID')->from('mProjectCustomer');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID = mProjectCustomer.CustomerUID', 'left');
		$this->db->join('mProducts', 'mProducts.ProductUID = mProjectCustomer.ProductUID', 'left');
		$this->db->where('mProjectCustomer.CustomerUID', $CustomerUID);
		$this->db->where('mProjectCustomer.ProductUID', $ProductUID);
		$this->db->where('mProjectCustomer.ProjectUID', $ProjectUID);

		return $this->db->get()->row();
	}

	public function get_customer_product_project_row_byname($CustomerUID, $ProductUID, $ProjectName)
	{
		$this->db->select('mProjectCustomer.CustomerUID, mProjectCustomer.ProjectUID, mProjectCustomer.ProjectName, mCustomer.CustomerName, mProjectCustomer.Priority,ProductName,mProjectCustomer.ProductUID')->from('mProjectCustomer');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID = mProjectCustomer.CustomerUID', 'left');
		$this->db->join('mProducts', 'mProducts.ProductUID = mProjectCustomer.ProductUID', 'left');
		$this->db->where('mProjectCustomer.CustomerUID', $CustomerUID);
		$this->db->where('mProjectCustomer.ProductUID', $ProductUID);
		$this->db->where('mProjectCustomer.ProjectName', $ProjectName);

		return $this->db->get()->row();
	}

	public function get_customer_product_row_byname($CustomerUID, $ProductName)
	{
		$this->db->select("mCustomerProducts.ProductUID,ProductName");
		$this->db->from('mCustomerProducts');
		$this->db->join('mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID');
		$this->db->where("mCustomerProducts.CustomerUID", $CustomerUID);
		$this->db->where("(`ProductName` = '" . $ProductName . "' OR `ProductCode` = '" . $ProductName . "')", null, false);
		$this->db->where("mProducts.Active", 1);
		$this->db->group_by("mCustomerProducts.ProductUID");
		return $this->db->get()->row();
	}

	public function GetUserEmailFromReleaseonhold($OrderUID)
	{
		$this->db->select('CustomerUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$OrderDetails = $this->db->get()->row();

		$this->db->select('CustomerEmail');
		$this->db->from('mCustomer');
		$this->db->where('mCustomer.CustomerUID', $OrderDetails->CustomerUID);
		return $this->db->get()->row();
	}

	public function getAvailableDocumentStorage()
	{
		$StorageLimit = 10000;

		for ($iter = 1; $iter <= $StorageLimit; $iter++) {

			if (!$this->Common_Model->isPresentInTable("DocumentStorage", $iter, "tDocuments")) {
				return $iter;
			}
		}
		return $StorageLimit;
	}

	public function getPendingOrders()
	{
		$status[] = $this->config->item('keywords')['ClosedandBilled'];
		$status[] = $this->config->item('keywords')['Cancelled'];

		$this->db->select('tOrders.OrderUID, tOrders.OrderNumber, tOrders.DocumentStorage');
		$this->db->from('tOrders');
		// $this->db->where_not_in('StatusUID', $status);
		return $this->db->get()->result();
	}
	//insert followup details
	function UpdateFollowup($Followup_Details, $OrderUID)
	{
		$this->db->insert('tOrderFollowUp', $Followup_Details);

		// BinOrders Delete Begin
		if (!empty($OrderUID)) {

			$this->db->where('OrderUID', $OrderUID);
			$GetBinUID = $this->db->get('tBinOrders')->row();

			$this->db->set('IsBinFull', '0', FALSE);
			$this->db->where('BinUID', $GetBinUID->BinUID);
			$this->db->update('tBin');

			$this->db->where('tBinOrders.OrderUID', $OrderUID);
			$this->db->delete('tBinOrders');
		}
		// BinOrders Delete End

		$this->db->set('IsFollowUp', '1', FALSE);
		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('tOrders');
		if ($this->db->affected_rows() > 0) {
			/*INSERT ORDER LOGS BEGIN*/
			$this->OrderLogsHistory($OrderUID, 'Order FollowUp Start', Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function UpdateInstruction($OrderUID, $InstructionUID, $UpdateFields)
	{
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('InstructionUID', $InstructionUID);
		$this->db->update('tOrderInstruction', $UpdateFields);
		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	//
	public function arrayDifference($NewArray, $OldArray)
	{
		//Check array differents between old array and new array
		$DiffNewArray = array();
		$DiffNewArray = array_diff_assoc($NewArray, $OldArray);
		// echo "<pre>";
		// print_r($DiffNewArray);

		//Log Description
		$Description = '';
		foreach ($DiffNewArray as $key => $value) {
			if (empty($OldArray[$key])) {
				$Description .= '<span class="log_dc_col">' . $key . ' :</span><span class="log_dc_val">"' . $value . '"</span> is newly added.<br/>';
			} else {
				if (empty($value)) {
					$Description .= '<span class="log_dc_col">' . $key . ' :</span><span class="log_dc_val">"' . $OldArray[$key] . '"</span> is deleted.<br/>';
				} else {
					$Description .= '<span class="log_dc_col">' . $key . ' :</span><span class="log_dc_val">"' . $OldArray[$key] . '"</span> has been changed to <span class="log_dc_val">"' . $value . '"</span>.<br/>';
				}
			}
		}
		return $Description;
	}

	public function OrderLogsHistory($OrderUID, $Description, $LogsDateTime, $UserUID = FALSE)
	{
		//update user activity in tOrders
		$Uid = isset($this->loggedid) ? $this->loggedid : $this->config->item('Cron_UserUID');
		$UpdateStatus = array('LastModifiedDateTime' => date('Y-m-d H:i:s'), 'LastModifiedByUserUID' => $Uid);
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->update('tOrders', $UpdateStatus);
		$LogsInsert = array('OrderUID' => $OrderUID, 'Description' => $Description, 'UserUID' => $Uid, 'LogsDateTime' => $LogsDateTime);
		if ($UserUID) {
			$LogsInsert['UserUID'] = $UserUID;
		}
		
		$this->db->insert('tLogs', $LogsInsert);
		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	public function GetOrderLogs($OrderUID)
	{
		$this->db->select('*,mUsers.UserName');
		$this->db->from('tLogs');
		$this->db->where('tLogs.OrderUID', $OrderUID);
		$this->db->join('mUsers', 'mUsers.UserUID=tLogs.UserUID');
		$this->db->order_by("LogsDateTime", "desc");
		return $this->db->get()->result();
		// echo '<pre>'; print_r($GetLogs); exit;

	}
	public function CheckOrderStatusandWorkflowShipping($OrderUID)
	{
		$this->db->where('tBinOrders.OrderUID', $OrderUID);
		$OrderDet = $this->db->get('tBinOrders')->row();

		if (!empty($OrderDet)) {
			$this->db->select('*');
			$this->db->from('tOrderWorkflows');
			$this->db->where('tOrderWorkflows.OrderUID', $OrderUID);
			$IsShipping = $this->db->get()->result();
			foreach ($IsShipping as $key => $value) {
				if ($value->WorkflowModuleUID == $this->config->item('Workflows')['Shipping']) {
					return 1;
				}
			}
		} else {
			return 0;
		}
	}
	//for Milestone

	function get_allMilestone()
	{
		$query = $this->db->query('SELECT MilestoneName FROM mMilestone');
		return $query->result();
	}

	// Madhuri 23/09/2019

	public function GetProjectProducts($ProjectUID)
	{

		$this->db->select("mProjectProducts.ProjectUID,mProducts.ProductName,mProducts.ProductUID");
		$this->db->from('mProjectProducts');
		$this->db->join('mProducts', 'mProducts.ProductUID = mProjectProducts.ProductUID');
		$this->db->where("mProjectProducts.ProjectUID", $ProjectUID);
		$this->db->where("mProducts.Active", 1);
		$this->db->group_by("mProjectProducts.ProductUID");
		$this->db->order_by("mProducts.ProductName");
		return $this->db->get()->result();
	}


	function FilterByProjectUser($RoleUID, $loggedid)
	{
		// if ($RoleUID != 1) {
		// 	$this->db->join('mProjectUser','mProjectCustomer.ProjectUID = mProjectUser.ProjectUID','left');
		// 	$this->db->where('mProjectUser.UserUID',$loggedid);
		// }
	}

	public function OrderAssignWorkflow($OrderUID)
	{
		$this->db->select('*,mWorkFlowModules.WorkflowModuleName,mUsers.UserName AS AssignedUserName,CompletedUser.UserName AS CompletedUserName');
		$this->db->from('tOrderAssignments');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID', 'left');
		$this->db->join('mUsers', 'mUsers.UserUID=tOrderAssignments.AssignedToUserUID');
		$this->db->join('mUsers AS CompletedUser', 'mUsers AS CompletedUser.UserUID=tOrderAssignments.CompletedByUserUID', 'left');
		$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
		return $this->db->get()->result();
	}

	function get_customerworkflowbyOrderUID($OrderUID)
	{
		$this->db->distinct();
		$this->db->select('mWorkFlowModules.WorkflowModuleUID,WorkflowModuleName');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('tOrders', 'tOrders.CustomerUID = mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where_in('mCustomerWorkflowModules.WorkflowModuleUID', $this->config->item('OrderAssignWorkflows'));
		$query = $this->db->get();
		return $query->result();
	}


	public function Is_given_workflow_availablecompleted($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('1');
		$this->db->from('tOrderAssignments');
		$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
		$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('tOrderAssignments.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
		if ($this->db->get()->num_rows() > 0) {
			return true;
		}

		return false;
	}

	public function Is_DisplayWorkflowAvailable($OrderUID, $WorkflowModuleUID)
	{

		// Check is not in followup
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $OrderUID]);

		if (!empty($tOrders)) {
			// if in followup return false
			if ($tOrders->IsFollowUp == STATUS_ONE) {
				return false;
			}
		}
		$is_workflowavailable = $this->Is_given_workflow_available($OrderUID, $WorkflowModuleUID);

		// Checki is workflow available.
		if (!$is_workflowavailable) {
			return false;
		}
		// Check is workflow available completed.
		$is_workflowavailablecompleted = $this->Is_given_workflow_availablecompleted($OrderUID, $WorkflowModuleUID);

		if ($is_workflowavailablecompleted) {
			return false;
		}



		// check this workflow assigned to user
		if (!in_array($this->RoleType, $this->config->item('SuperAccess'))) {
			$this->db->select('1')->from('tOrderAssignments');
			$this->db->where('tOrderAssignments.OrderUID', $OrderUID);
			$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('tOrderAssignments.AssignedToUserUID', $this->loggedid);
			// $this->db->where_not_in('tOrderAssignments.WorkflowStatus', [$this->config->item('WorkflowStatus')['Completed']]);

			$is_this_workflow_available = $this->db->get()->num_rows();

			if (!$is_this_workflow_available) {
				return false;
			}
		}
		return true;
	}


	/*LOP Orders Queues Query Starts*/

	/*Welcome Call Queue Common Query*/
	function GetWelcomeCallQueue($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['WelcomeCall'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['WelcomeCall']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
					CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
					CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
					TRUE 
					ELSE FALSE END
					ELSE FALSE END
					ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["WelcomeCall"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['WelcomeCall'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['WelcomeCall'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['WelcomeCall']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['WelcomeCall'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['WelcomeCall'] . "' 
				THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}

	/*TitleTeam Queue Common Query*/
	function GetTitleTeamQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['TitleTeam'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['TitleTeam']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			// $this->otherdb->where('mWorkFlowModules.WorkflowModuleUID <= ' .$this->config->item('Workflows')['PreScreen']);
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');



		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["TitleTeam"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['TitleTeam'] . "'");

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//join followup


		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['TitleTeam'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['TitleTeam'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['TitleTeam'] . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
				ELSE TRUE END
				ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['TitleTeam']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}

	/*FHAVACaseTeam Queue Common Query*/
	function GetFHAVACaseTeamQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['FHAVACaseTeam'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/
		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Thursday 12 March 2020
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['FHAVACaseTeam']);
		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}


		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["FHAVACaseTeam"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['FHAVACaseTeam'] . "'");

		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//join followup


		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['FHAVACaseTeam'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where_not_in('tOrders.StatusUID', $status);



		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}



		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['FHAVACaseTeam'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['FHAVACaseTeam'] . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
				ELSE TRUE END
				ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['FHAVACaseTeam']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}


	/*ThirdParty Queue Common Query*/
	function GetThirdPartyQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['ThirdPartyTeam'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/
		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Thursday 12 March 2020
		 */
		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['ThirdPartyTeam']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}


		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["ThirdPartyTeam"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');


		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['ThirdPartyTeam'] . "'");
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//join followup


		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['ThirdPartyTeam'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}



		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['ThirdPartyTeam'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['ThirdPartyTeam'] . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['ThirdPartyTeam']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}


	/*WorkUp Queue Common Query*/
	function GetWorkUpQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Workup'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/
		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Thursday 12 March 2020
		 */
		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Workup']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('TW_Workup.OrderUID')->from('tOrderWorkflows TW_Workup');
			
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = TW_Workup.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = TW_Workup.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "
				CASE WHEN TW_Workup.IsForceEnabled = 0 THEN
				CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND (TW_" . $value->SystemName . ".IsPresent = 1) THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where('TW_Workup.OrderUID = tOrderWorkflows.OrderUID AND TW_Workup.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID ', NULL,FALSE);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = ' . $this->config->item("Workflows")["Workup"], 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = " . $this->config->item('Workflows')['Workup']);

		/*Check Doc Case Enabled*/
		$this->db->join('tOrderDocChase', 'tOrders.OrderUID = tOrderDocChase.OrderUID AND tOrderDocChase.WorkflowModuleUID = ' . $this->config->item("Workflows")["Workup"], 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		//join followup


		$this->db->where('CASE WHEN tOrderDocChase.IsCleared = 0 AND tOrderDocChase.DocChaseUID IS NOT NULL THEN FALSE ELSE TRUE END', NULL, FALSE);

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where_not_in('tOrders.StatusUID', $status);
			
		if (!isset($Conditions['SkipDependentWorkflowCompleteCond']) && isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {

			$this->db->where('EXISTS (' . $previous_filtered_orders_sql . ')', NULL, FALSE);

		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['SkipCondition'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = " . $this->config->item('Workflows')['Workup'] . " AND (tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' OR tOrderWorkflows.IsForceEnabled = '" . STATUS_ONE . "') THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = " . $this->config->item('Workflows')['Workup'] . " THEN 
				CASE WHEN tOrderAssignments.WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN FALSE 
				ELSE TRUE END
				ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();	
		}

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Workup']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

		// kickback queue
		if (isset($Conditions['IsKickBack'])) {
			// KickBack orders filter
    		$this->db->where('tOrderWorkflows.IsKickBack', STATUS_ONE);
		} else {
			// Remove Kickback queue
			$this->db->where('(tOrderWorkflows.IsKickBack IS NULL OR tOrderWorkflows.IsKickBack = "" OR tOrderWorkflows.IsKickBack = 0)', NULL, FALSE);
		}

		// Rework queue
		if (isset($Conditions['IsRework'])) {
			// Rework orders filter
			$this->db->where('tOrderWorkflows.IsRework', STATUS_ONE);
		} else {
			// Remove Kickback queue
			$this->db->where('(tOrderWorkflows.IsRework IS NULL OR tOrderWorkflows.IsRework = "" OR tOrderWorkflows.IsRework = 0)', NULL, FALSE);
		}		

		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}



	/*UnderWriter Queue Common Query*/
	function GetUnderWriterQueue($Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['UnderWriter'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/
		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Thursday 12 March 2020
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['UnderWriter']);
		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			// $this->otherdb->where('mWorkFlowModules.WorkflowModuleUID <= ' .$this->config->item('Workflows')['PreScreen']);
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}
		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/



		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["UnderWriter"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['UnderWriter'] . "'");
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->where_not_in('tOrders.StatusUID', $status);


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

		$this->db->join('tOrderDocChase', 'tOrders.OrderUID = tOrderDocChase.OrderUID AND tOrderDocChase.WorkflowModuleUID = "' . $this->config->item("Workflows")["UnderWriter"] . '"', 'left');
		$this->db->where('CASE WHEN tOrderDocChase.IsCleared = 0 AND tOrderDocChase.DocChaseUID IS NOT NULL THEN FALSE ELSE TRUE END', NULL, FALSE);

		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['UnderWriter'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['UnderWriter'] . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['UnderWriter']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/




		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}




	/*UnderWriter Queue Common Query*/
	function GetSchedulingQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Scheduling'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/
		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Thursday 12 March 2020
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Scheduling']);
		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			// $this->otherdb->where('mWorkFlowModules.WorkflowModuleUID <= ' .$this->config->item('Workflows')['PreScreen']);
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}
		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Scheduling"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Scheduling'] . "'");
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');


		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID, FALSE, '', $Conditions);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->join('tOrderDocChase', 'tOrders.OrderUID = tOrderDocChase.OrderUID AND tOrderDocChase.WorkflowModuleUID = "' . $this->config->item("Workflows")["Scheduling"] . '"', 'left');
		$this->db->where('CASE WHEN tOrderDocChase.IsCleared = 0 AND tOrderDocChase.DocChaseUID IS NOT NULL THEN FALSE ELSE TRUE END', NULL, FALSE);


		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Scheduling'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Scheduling'] . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Scheduling']);

		if (isset($Conditions['ThreeAConfirmationSigningDateCond'])) {

			/*SigningDate is not blank*/
			$this->db->where('(tOrderImport.SigningDate IS NOT NULL OR tOrderImport.SigningDate <> "")', NULL, FALSE);

			$CurrentTimeStamp = time();
			$ToTimeStamp = strtotime("+1 day", $CurrentTimeStamp);
			$skipdays = array("Sunday");

			if (in_array(date("l", $ToTimeStamp), $skipdays)) {

				// If current day is saturday then saturday and monday to be considered
				$ToTimeStamp = strtotime("+1 day", $ToTimeStamp);

				$this->db->where('((STR_TO_DATE(tOrderImport.SigningDate, "%m/%d/%Y") = STR_TO_DATE("'.date('m/d/Y',$CurrentTimeStamp).'", "%m/%d/%Y") OR STR_TO_DATE(tOrderImport.SigningDate, "%m/%d/%Y") = STR_TO_DATE("'.date('m/d/Y',$ToTimeStamp).'", "%m/%d/%Y")) OR (STR_TO_DATE(tOrderImport.SigningDate, "%m-%d-%Y") = STR_TO_DATE("'.date('m-d-Y',$CurrentTimeStamp).'", "%m-%d-%Y") OR STR_TO_DATE(tOrderImport.SigningDate, "%m-%d-%Y") = STR_TO_DATE("'.date('m-d-Y',$ToTimeStamp).'", "%m-%d-%Y")))', NULL, FALSE);
				
			} elseif (in_array(date("l", $CurrentTimeStamp), $skipdays)) {

				// If current day is sunday then monday and tuesday to be considered
				$AddToTimeStamp = strtotime("+1 day", $ToTimeStamp);

				$this->db->where('((STR_TO_DATE(tOrderImport.SigningDate, "%m/%d/%Y") = STR_TO_DATE("'.date('m/d/Y',$ToTimeStamp).'", "%m/%d/%Y") OR STR_TO_DATE(tOrderImport.SigningDate, "%m/%d/%Y") = STR_TO_DATE("'.date('m/d/Y',$AddToTimeStamp).'", "%m/%d/%Y")) OR (STR_TO_DATE(tOrderImport.SigningDate, "%m-%d-%Y") = STR_TO_DATE("'.date('m-d-Y',$ToTimeStamp).'", "%m-%d-%Y") OR STR_TO_DATE(tOrderImport.SigningDate, "%m-%d-%Y") = STR_TO_DATE("'.date('m-d-Y',$AddToTimeStamp).'", "%m-%d-%Y")))', NULL, FALSE);
			} else {

				// 
				$this->db->where('((STR_TO_DATE(tOrderImport.SigningDate, "%m/%d/%Y") BETWEEN STR_TO_DATE("'.date('m/d/Y',$CurrentTimeStamp). '", "%m/%d/%Y") AND STR_TO_DATE("'.date('m/d/Y',$ToTimeStamp).'", "%m/%d/%Y")) OR (STR_TO_DATE(tOrderImport.SigningDate, "%m-%d-%Y") BETWEEN STR_TO_DATE("'.date('m-d-Y',$CurrentTimeStamp). '", "%m-%d-%Y") AND STR_TO_DATE("'.date('m-d-Y',$ToTimeStamp).'", "%m-%d-%Y")))', NULL, false);
			}
			
			
		} else {

			/*ClosingDisclosureSendDate is not blank*/
			$this->db->where('tOrderImport.ClosingDisclosureSendDate <> ""', NULL, FALSE);

			/*SigningDate is blank*/
			$this->db->where('(tOrderImport.SigningDate IS NULL OR tOrderImport.SigningDate = "")', NULL, FALSE);

		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}



	/*UnderWriter Queue Common Query*/
	function GetClosingQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Closing'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Closing']);
		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			// $this->otherdb->where('mWorkFlowModules.WorkflowModuleUID <= ' .$this->config->item('Workflows')['PreScreen']);
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}


		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Closing"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Closing'] . "'");

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->join('tOrderDocChase', 'tOrders.OrderUID = tOrderDocChase.OrderUID AND tOrderDocChase.WorkflowModuleUID = "' . $this->config->item("Workflows")["Closing"] . '"', 'left');
		$this->db->where('CASE WHEN tOrderDocChase.IsCleared = 0 AND tOrderDocChase.DocChaseUID IS NOT NULL THEN FALSE ELSE TRUE END', NULL, FALSE);

		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Closing'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Closing'] . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Closing']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}



	/*UnderWriter Queue Common Query*/
	function GetDocChaseQueue()
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['DocChase'];

		// $status[] = $this->config->item('keywords')['PrescreenCompleted'];
		// $status[] = $this->config->item('keywords')['Pendingdocuments'];
		// $status[] = $this->config->item('keywords')['Alldocuments received'];
		// $status[] = $this->config->item('keywords')['WorkupCompleted'];
		// $status[] = $this->config->item('keywords')['WaitingforConditional Approval'];
		// $status[] = $this->config->item('keywords')['OnHold'];
		// $status[] = $this->config->item('keywords')['UnderwriterCompleted'];



		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["DocChase"] . '"', 'left');
		//$this->db->join('tOrders','tOrderDocChase.OrderUID = tOrders.OrderUID','left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		// For Fileter Assignment based on workflow

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		//$this->db->where("tOrderDocChase.IsCleared", STATUS_ZERO);

		//$this->db->where_in('tOrders.StatusUID', $status);


		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}

	function GetEsclationQueue()
	{

		$this->db->from('tOrderEsclation');
		$this->db->join('tOrders', 'tOrderEsclation.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->where("tOrderEsclation.IsCleared", STATUS_ZERO);
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->group_by('tOrders.OrderUID');
	}

	function GetWithdrawalQueue()
	{

		$this->db->from('tOrderWithdrawal');
		$this->db->join('tOrders', 'tOrderWithdrawal.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where("tOrderWithdrawal.IsCleared", STATUS_ZERO);
	}




	/*LOP Orders Queues Query Ends*/

	/*ALL WORKFLOW COMPLETED Query*/
	function get_workflow_completed($OrderUID)
	{

		$this->db->select('*');
		/*########## GEt Workflow results using another db connection ############*/
		
		$this->otherdb->select('mWorkFlowModules.*');
		$this->otherdb->from('mWorkFlowModules');
		$this->otherdb->where('mWorkFlowModules.WorkflowModuleUID <= ' . $this->config->item('Workflows')['ThirdPartyTeam']);
		$this->otherdb->or_where('mWorkFlowModules.WorkflowModuleUID  = ' . $this->config->item('Workflows')['HOI']);
		$this->otherdb->or_where('mWorkFlowModules.WorkflowModuleUID  = ' . $this->config->item('Workflows')['BorrowerDoc']);
		$this->otherdb->or_where('mWorkFlowModules.WorkflowModuleUID  = ' . $this->config->item('Workflows')['PayOff']);

		$mWorkFlowModules = $this->otherdb->get()->result();



		/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
		$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
		foreach ($mWorkFlowModules as $key => $value) {

			$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

			$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
		}

		foreach ($mWorkFlowModules as $key => $value) {
			$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
			$this->otherdb->group_start();
			$this->otherdb->where($Case_Where, NULL, FALSE);
			$this->otherdb->group_end();
		}

		$this->otherdb->where('tOrders.OrderUID', $OrderUID);


		$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();



		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Workup"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');



		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Workup'] . "'");
		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Workup'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Workup'] . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Workup']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/
		$this->db->group_by('tOrders.OrderUID');
		return $this->db->get()->num_rows();
	}

	function check_raisedocchase_enabled($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*')->from('tOrderDocChase');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsCleared', 0);
		if ($this->db->get()->num_rows()) {
			return false;
		}
		return true;
	}

	function check_cleardocchase_enabled($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*')->from('tOrderDocChase');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsCleared', 0);
		return $this->db->get()->num_rows();
	}

	function check_atleastonecleardocchase_enabled($OrderUID)
	{
		$this->db->select('*')->from('tOrderDocChase');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('IsCleared', 0);
		return $this->db->get()->num_rows();
	}

	function check_clearesclation_enabled($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*')->from('tOrderEsclation');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsCleared', 0);
		return $this->db->get()->num_rows();
	}
	function getOrderWorkflowDetails($OrderUID)
	{
		$ChecklistWorkflows = " AND mWorkFlowModules.WorkflowModuleUID IN (" . implode(',', array_values($this->config->item('ChecklistWorkflows'))) . ")";
		return $this->db->query("SELECT mWorkFlowModules.WorkflowModuleUID,WorkflowModuleName FROM mWorkFlowModules 
			JOIN tOrderWorkflows ON tOrderWorkflows.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID 
			LEFT JOIN mCustomerWorkflowModules ON mCustomerWorkflowModules.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID 
			WHERE tOrderWorkflows.OrderUID = $OrderUID  {$ChecklistWorkflows} AND mCustomerWorkflowModules.CustomerUID = '" . $this->parameters['DefaultClientUID'] . "' ")->result();
	}
	function getCheckListAnswers($DocumentTypeUID, $OrderUID, $WorkflowModuleUID)
	{
		$result = '';
		$this->db->select('*')->from('tDocumentCheckList')->where(array('DocumentTypeUID' => $DocumentTypeUID, 'WorkflowUID' => $WorkflowModuleUID, 'OrderUID' => $OrderUID));
		$result =  $this->db->order_by('Position', 'ASC')->get()->row();
		return $result;
	}

	function getcategorylist($OrderUID, $WorkflowModuleUID, $groups, $Conditions = [])
	{
		$OrderDetails = $this->db->select('CustomerUID')->from('tOrders')->where('OrderUID', $OrderUID)->get()->row();
		$this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType', 'mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID AND mDocumentType.CategoryUID IS NOT NULL AND mCustomerWorkflowModules.CategoryUID IS NOT NULL');
		if ($groups != 'FHA/VA') {
			if ($groups) {
				$this->db->where('(mDocumentType.Groups IS NULL OR mDocumentType.Groups ="' . $groups . '")');
			}
		}

		if (!isset($Conditions['SkipCondition'])) {
			$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		}
		
		$this->db->where(array('mCustomerWorkflowModules.CustomerUID' => $OrderDetails->CustomerUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'mDocumentType.CustomerUID' => $OrderDetails->CustomerUID, 'mDocumentType.Active' => '1'));
		$this->db->order_by('mDocumentType.Position', 'ASC');
		return $this->db->get()->result();
	}

	function getfhacategorylist($OrderUID, $WorkflowModuleUID, $groups)
	{
		$OrderDetails = $this->db->select('CustomerUID')->from('tOrders')->where('OrderUID', $OrderUID)->get()->row();
		$this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType', 'mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID');
		$this->db->where('mDocumentType.Groups IS NOT NULL ');
		if ($groups) {
			if ($groups != 'FHA/VA') {
				$this->db->where('mDocumentType.Groups', $groups);
			}
		}
		$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);
		$this->db->where(array('mCustomerWorkflowModules.CustomerUID' => $OrderDetails->CustomerUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'mDocumentType.CustomerUID' => $OrderDetails->CustomerUID, 'mDocumentType.Active' => '1'));
		$this->db->order_by('mDocumentType.Position', 'ASC');
		return $this->db->get()->result();
	}

	function getOtherCheckList($OrderUID, $WorkflowUID)
	{

		return $this->db->query('SELECT * FROM `tDocumentCheckList` WHERE CategoryUID IS NULL and DocumentTypeUID IS NULL and WorkflowUID = ' . $WorkflowUID . ' and OrderUID = ' . $OrderUID . ' ORDER BY `DocumentTypeUID`, CASE `Answer` WHEN "Problem Identified" THEN 1  WHEN "Completed" THEN 2 WHEN "NA" THEN 3 WHEN "" THEN 4  END')->result();
	}

	function GetOrderWorkflowsWithStatus($OrderUID)
	{
		$query = $this->db->query("Select tOrderAssignments.*,mUsers.UserUID,mUsers.UserName from tOrderAssignments left join mUsers on mUsers.UserUID=tOrderAssignments.CompletedByUserUID where OrderUID=" . $OrderUID);
		return $query->result();
	}


	function GetOrderWorkflowsAssignedStatus($OrderUID, $WorkflowID)
	{
		$query = $this->db->query("Select tOrderAssignments.*,mUsers.UserUID,mUsers.UserName from tOrderAssignments left join mUsers on mUsers.UserUID=tOrderAssignments.AssignedToUserUID where OrderUID=" . $OrderUID . " and tOrderAssignments.WorkflowModuleUID=" . $WorkflowID);
		return $query->row();
	}
	function updateChecklist($post)
	{
		if (isset($post['checklist'])) {
			foreach ($post['checklist'] as $WorkflowUID => $checklist) {
				
				/*DOC-195 - DYNAMIC CHECKLIST FIELDS*/
				$ChecklistFields = $this->Common_Model->get_dynamicchecklistfields($post['OrderUID'], $WorkflowUID);
				
				/*DOC-195 - DYNAMIC CHECKLIST CLIENT*/
				if (!empty($ChecklistFields)) {
					$this->load->dbforge();

					foreach ($ChecklistFields as $ChecklistFieldkey => $ChecklistFieldsvalue) {
						if (!$this->db->field_exists($ChecklistFieldsvalue->FieldName, 'tDocumentCheckList')) {
							$this->dbforge->add_column('tDocumentCheckList', $ChecklistFieldsvalue->FieldName);
							$this->db->query($sql);
						}
					}
				}
				
				/*DYNAMIC CHECKLIST FIELDS*/
				if(!isset($post['autosave'])) {

					$this->db->query('DELETE FROM `tDocumentCheckList` WHERE CategoryUID IS NULL AND DocumentTypeUID IS NULL AND OrderUID =' . $post['OrderUID'] . ' AND WorkflowUID =' . $WorkflowUID );
				}

				foreach ($checklist as $DocumentTypeUID => $value) {
					$getDocumentTypeRecord = $this->getDocumentTypeRecord($post['OrderUID'], $DocumentTypeUID);

					if ($DocumentTypeUID == 'OtherChecklist') {
						//other checklist
						
						foreach ($value as $key => $value1) {

							if(isset($value1['question']) && $value1['question'] != "") {
								/* get Document type Name record */
								$resultUID = $this->db->select('*')->from('tDocumentChecklist')->where(array('DocumentTypeName' => $value1['question'], 'OrderUID' => $post['OrderUID'], 'WorkflowUID'=>$WorkflowUID))->get()->row();
								
								if(isset($post['autosave'])) {
									$this->db->query('DELETE FROM `tDocumentCheckList` WHERE CategoryUID IS NULL AND DocumentTypeUID IS NULL AND OrderUID =' . $post['OrderUID'] . ' AND WorkflowUID = ' . $WorkflowUID.' AND DocumentTypeName = "'.$value1['question'].'" ');
								}

								$data = array(
									'Answer' => $value1['Answer'],
									'Comments' => $value1['Comments'],
									'OrderUID' => $post['OrderUID'],
									'DocumentTypeName' => isset($value1['question']) ? $value1['question'] : '',
									'WorkflowUID' => $WorkflowUID,
									'ModifiedUserUID' => $this->loggedid,
									'ModifiedDateTime' => date("d/m/Y H:i:s")
								);

								/*DOC-195 - DYNAMIC CHECKLIST FIELDS*/
								if (!empty($ChecklistFields)) {
									foreach ($ChecklistFields as $ChecklistFieldkey => $ChecklistFieldsvalue) {
										if ($ChecklistFieldsvalue->FieldType == 'checkbox') {
											$data[$ChecklistFieldsvalue->FieldName] = isset($value1[$ChecklistFieldsvalue->FieldName]) ? 'Yes' : 'No';
										} else {
											$data[$ChecklistFieldsvalue->FieldName] = isset($value1[$ChecklistFieldsvalue->FieldName]) && ($value1[$ChecklistFieldsvalue->FieldName] != 'empty') ? $value1[$ChecklistFieldsvalue->FieldName] : '';
										}
									}
								}

								/*DYNAMIC CHECKLIST FIELDS*/
								$this->db->insert('tDocumentCheckList', $data);
								
								/*OTHER CHECKLIST AUDIT LOG*/
								$DocumentName = ($value1['question']) ? ' for ' . $value1['question'] : '';

								if ($value1['question']==$resultUID->DocumentTypeName) {
									/* comments */
									if ($resultUID->Comments != $value1['Comments']) {
										if ($resultUID->Comments) {
											$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change Comment ' . $resultUID->Comments . ' to ' . $value1['Comments'] . $DocumentName, Date('Y-m-d H:i:s'));
										} else {
											$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change Comment ' . $value1['Comments'] . $DocumentName, Date('Y-m-d H:i:s'));
										}
									}
									/* DocumentType */
									if ($resultUID->DocumentType != $value1['DocumentType']) {
										if ($resultUID->DocumentType) {
											$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentType ' . $resultUID->DocumentType . ' to ' . $value1['DocumentType'] . $DocumentName, Date('Y-m-d H:i:s'));
										} else if ($value['DocumentType'] != 'empty' && $value['DocumentType'] != '') {
											$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentType ' . $value1['DocumentType'] . $DocumentName, Date('Y-m-d H:i:s'));
										}
									}
									/* DocumentDate */
									if ($resultUID->DocumentDate != $value1['DocumentDate']) {
										if ($resultUID->DocumentDate) {
											$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentDate ' . $resultUID->DocumentDate . ' to ' . $value1['DocumentDate'] . $DocumentName, Date('Y-m-d H:i:s'));
										} else {
											$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentDate ' . $value1['DocumentDate'] . $DocumentName, Date('Y-m-d H:i:s'));
										}
									}
								}else{
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Added new checklist ' . $data['DocumentTypeName'], Date('Y-m-d H:i:s'));
								}			  
							}
						}
					} else {
						//defined checklist
						$data = array(
							'Answer' => $value['Answer'],
							'Comments' => $value['Comments']
						);

						/*DOC-195 - DYNAMIC CHECKLIST FIELDS*/
						if (!empty($ChecklistFields)) {
							foreach ($ChecklistFields as $ChecklistFieldkey => $ChecklistFieldsvalue) {
								if ($ChecklistFieldsvalue->FieldType == 'checkbox') {
									$data[$ChecklistFieldsvalue->FieldName] = isset($value[$ChecklistFieldsvalue->FieldName]) ? 'Yes' : 'No';
								} else {
									$data[$ChecklistFieldsvalue->FieldName] = isset($value[$ChecklistFieldsvalue->FieldName]) && ($value[$ChecklistFieldsvalue->FieldName] != 'empty') ? $value[$ChecklistFieldsvalue->FieldName] : '';
								}
							}
						}
						/*DYNAMIC CHECKLIST FIELDS*/
						if (isset($value['selectIn']) && !empty($value['selectIn'])) {
							$data['selectIn'] = $value['selectIn'];
						}

						if (isset($value['checkIn']) && !empty($value['checkIn'])) {
							$data['checkIn'] = isset($value['checkIn']) ? 'yes' : 'no';
						}

						if (isset($value['radioIn']) && !empty($value['radioIn'])) {
							$data['radioIn'] = $value['radioIn'];
						}


						$result = $this->db->select('DocumentTypeUID')->from('tDocumentCheckList')->where(array('DocumentTypeUID' => $DocumentTypeUID, 'WorkflowUID' => $WorkflowUID, 'OrderUID' => $post['OrderUID']))->get()->row();
						$checklistName = $this->db->select('DocumentTypeName')->from('mDocumentType')->where('DocumentTypeUID', $DocumentTypeUID)->get()->result();
						$DocumentName = ($checklistName[0]->DocumentTypeName) ? ' for ' . $checklistName[0]->DocumentTypeName : '';
						if ($result) {
							$this->db->where(array('DocumentTypeUID' => $DocumentTypeUID, 'WorkflowUID' => $WorkflowUID, 'OrderUID' => $post['OrderUID']));
							$this->db->update('tDocumentCheckList', $data);

							if ($getDocumentTypeRecord[0]->Comments != $value['Comments']) {
								if ($getDocumentTypeRecord[0]->Comments) {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change Comment ' . $getDocumentTypeRecord[0]->Comments . ' to ' . $value['Comments'] . $DocumentName, Date('Y-m-d H:i:s'));
								} else {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change Comment ' . $value['Comments'] . $DocumentName, Date('Y-m-d H:i:s'));
								}
							}
							if ($getDocumentTypeRecord[0]->DocumentType != $value['DocumentType']) {
								if ($getDocumentTypeRecord[0]->DocumentType) {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentType ' . $getDocumentTypeRecord[0]->DocumentType . ' to ' . $value['DocumentType'] . $DocumentName, Date('Y-m-d H:i:s'));
								} else if ($value['DocumentType'] != 'empty' && $value['DocumentType'] != '') {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentType ' . $value['DocumentType'] . $DocumentName, Date('Y-m-d H:i:s'));
								}
							}
							if ($getDocumentTypeRecord[0]->DocumentDate != $value['DocumentDate']) {
								if ($getDocumentTypeRecord[0]->DocumentDate) {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentDate ' . $getDocumentTypeRecord[0]->DocumentDate . ' to ' . $value['DocumentDate'] . $DocumentName, Date('Y-m-d H:i:s'));
								} else {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentDate ' . $value['DocumentDate'] . $DocumentName, Date('Y-m-d H:i:s'));
								}
							}
						} else {

							$Category = $this->db->select('CategoryUID')->from('mDocumentType')->where('DocumentTypeUID', $DocumentTypeUID)->get()->row();
							$data['OrderUID'] = $post['OrderUID'];
							$data['CategoryUID'] = $Category->CategoryUID;
							$data['DocumentTypeUID'] = $DocumentTypeUID;
							$data['WorkflowUID'] = $WorkflowUID;
							$data['ModifiedUserUID'] = $this->loggedid;
							$data['ModifiedDateTime'] = date("d/m/Y H:i:s");
							$this->db->insert('tDocumentCheckList', $data);

							if ($getDocumentTypeRecord[0]->Comments != $value['Comments']) {
								if ($getDocumentTypeRecord[0]->Comments) {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change Comment ' . $getDocumentTypeRecord[0]->Comments . ' to ' . $value['Comments'] . $DocumentName, Date('Y-m-d H:i:s'));
								} else {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change Comment ' . $value['Comments'] . $DocumentName, Date('Y-m-d H:i:s'));
								}
							}
							if ($getDocumentTypeRecord[0]->DocumentType != $value['DocumentType']) {
								if ($getDocumentTypeRecord[0]->DocumentType) {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentType ' . $getDocumentTypeRecord[0]->DocumentType . ' to ' . $value['DocumentType'] . $DocumentName, Date('Y-m-d H:i:s'));
								} else if ($value['DocumentType'] != 'empty' && $value['DocumentType'] != '') {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentType ' . $value['DocumentType'] . $DocumentName, Date('Y-m-d H:i:s'));
								}
							}
							if ($getDocumentTypeRecord[0]->DocumentDate != $value['DocumentDate']) {
								if ($getDocumentTypeRecord[0]->DocumentDate) {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentDate ' . $getDocumentTypeRecord[0]->DocumentDate . ' to ' . $value['DocumentDate'] . $DocumentName, Date('Y-m-d H:i:s'));
								} else {
									$this->Common_Model->OrderLogsHistory($post['OrderUID'], 'Change DocumentDate ' . $value['DocumentDate'] . $DocumentName, Date('Y-m-d H:i:s'));
								}
							}
						}

						// Update checklist data
						$this->UpdateRespectiveWorkflowChecklistData($post, $DocumentTypeUID, $value);
					}
				}

				//raise docchase if docchase send yes
				$DocChasesendyes = $this->db->query('SELECT EXISTS(SELECT * FROM `tDocumentCheckList` WHERE  OrderUID="' . $post['OrderUID'] . '" AND IsChaseSend="YES" AND WorkflowUID =' . $WorkflowUID . ') AS available')->row()->available;

				if ($DocChasesendyes) {
					$tOrderDocChaserow = $this->db->query('SELECT EXISTS(SELECT * FROM `tOrderDocChase` WHERE OrderUID="' . $post['OrderUID'] . '" AND WorkflowModuleUID =' . $WorkflowUID . ' AND IsCleared = 0) AS available')->row()->available;
					if (empty($tOrderDocChaserow)) {

						$docchasedata['OrderUID'] = $post['OrderUID'];
						$docchasedata['WorkflowModuleUID'] = $WorkflowUID;
						// $docchasedata['ReasonUID'] = $Reason;
						// $docchasedata['Remarks'] = $remarks;
						$docchasedata['RaisedByUserUID'] = $this->loggedid;
						$docchasedata['RaisedDateTime'] = date('Y-m-d H:i:s');
						$docchasedata['IsCleared'] = 0;
						$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID' => $WorkflowUID]);

						$insert = $this->Common_Model->save('tOrderDocChase', $docchasedata);
						$WorkflowArrays = $this->config->item('Workflows');
						$Module = array_search($WorkflowUID, $WorkflowArrays);
						/*$insertnotesdata = array('OrderUID'=>$post['OrderUID'],
							//'Description'=>$remarks,
						'WorkflowUID'=>$WorkflowUID,
						'Module'=>$Module,
						'CreatedByUserUID'=>$this->loggedid,
						'CreateDateTime'=>date("Y/m/d H:i:s"));
						$this->Common_Model->insertNotes($insertnotesdata);*/
						/*INSERT ORDER LOGS BEGIN*/
						$this->Common_Model->OrderLogsHistory($post['OrderUID'], $Module . ' - Doc Chase Raised', Date('Y-m-d H:i:s'));
						/*INSERT ORDER LOGS END*/
					}
				}
			}
		} else {
			if (!empty($post['WorkflowModuleUID'])) {
				$this->db->query('DELETE FROM `tDocumentCheckList` WHERE CategoryUID IS NULL and DocumentTypeUID IS NULL and OrderUID =' . $post['OrderUID'] . ' and WorkflowUID =' . $post['WorkflowModuleUID']);
			} else {
				$this->db->query('DELETE FROM `tDocumentCheckList` WHERE CategoryUID IS NULL and DocumentTypeUID IS NULL and OrderUID =' . $post['OrderUID']);
			}
		}
	}

	/**
	*Function Update Respective workflow checklist data 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 05 October 2020.
	*/
	function UpdateRespectiveWorkflowChecklistData($post, $DocumentTypeUID, $value) {
		if ($DocumentTypeUID == $this->config->item('Gatekeeping_TitleCommitment')) {

			$this->UpdateChecklistData($post, $this->config->item('Title_Commitment'), $this->config->item('Workflows')['TitleTeam'], $value);
		}

		if ($DocumentTypeUID == $this->config->item('Title_Commitment')) {

			$this->UpdateChecklistData($post, $this->config->item('Gatekeeping_TitleCommitment'), $this->config->item('Workflows')['GateKeeping'], $value);
		}

		if ($DocumentTypeUID == $this->config->item('Gatekeeping_UtilityBill')) {

			$this->UpdateChecklistData($post, $this->config->item('PreScreen_UtilityBill'), $this->config->item('Workflows')['PreScreen'], $value);
		}

		if ($DocumentTypeUID == $this->config->item('PreScreen_UtilityBill')) {
			
			$this->UpdateChecklistData($post, $this->config->item('Gatekeeping_UtilityBill'), $this->config->item('Workflows')['GateKeeping'], $value);
		}

		if ($DocumentTypeUID == $this->config->item('Gatekeeping_FHACaseAssignment')) {

			$this->UpdateChecklistData($post, $this->config->item('FHA_CaseAssignmentDate'), $this->config->item('Workflows')['FHAVACaseTeam'], $value);
		}

		if ($DocumentTypeUID == $this->config->item('FHA_CaseAssignmentDate')) {
			
			$this->UpdateChecklistData($post, $this->config->item('Gatekeeping_FHACaseAssignment'), $this->config->item('Workflows')['GateKeeping'], $value);
		}
	}

	public function UpdateChecklistData($post, $DocumentTypeUID, $WorkflowModuleUID, $value)
	{		

		$result = $this->db->select('DocumentTypeUID')->from('tDocumentCheckList')->where(array('DocumentTypeUID' => $DocumentTypeUID, 'WorkflowUID' => $WorkflowModuleUID, 'OrderUID' => $post['OrderUID']))->get()->row();

		// Enable workflow
		if(empty($result)){

			$Category = $this->db->select('CategoryUID')->from('mDocumentType')->where('DocumentTypeUID', $DocumentTypeUID)->get()->row();

			$data['OrderUID'] = $post['OrderUID'];
			$data['CategoryUID'] = $Category->CategoryUID;
			$data['DocumentTypeUID'] = $DocumentTypeUID;
			$data['WorkflowUID'] = $WorkflowModuleUID;
			$data['DocumentDate'] = $value['DocumentDate'];
			$data['DocumentExpiryDate'] = $value['DocumentExpiryDate'];
			$data['ModifiedUserUID'] = $this->loggedid;
			$data['ModifiedDateTime'] = date("d/m/Y H:i:s");
			$this->db->insert('tDocumentCheckList', $data);

		} else {

			$data['DocumentDate'] = $value['DocumentDate'];
			$data['DocumentExpiryDate'] = $value['DocumentExpiryDate'];
			$data['ModifiedUserUID'] = $this->loggedid;
			$data['ModifiedDateTime'] = date("d/m/Y H:i:s");
			
			$this->db->where(array('DocumentTypeUID' => $DocumentTypeUID, 'WorkflowUID' => $WorkflowModuleUID, 'OrderUID' => $post['OrderUID']));
			$this->db->update('tDocumentCheckList', $data);

		}
	}

	function is_workflow_in_parkingqueue($OrderUID, $WorkflowModuleUID)
	{
		$query = $this->db->query("SELECT EXISTS (SELECT * FROM tOrderParking WHERE OrderUID = '" . $OrderUID . "' AND WorkflowModuleUID = '" . $WorkflowModuleUID . "' AND IsCleared = 0) AS available");
		return $query->row()->available;
	}


	function check_raisewithdrawal_enabled($OrderUID)
	{
		$this->db->select('*')->from('tOrderWithdrawal');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('IsCleared', 0);
		if ($this->db->get()->num_rows()) {
			return false;
		}
		return true;
	}

	function check_clearwithdrawal_enabled($OrderUID)
	{
		$this->db->select('*')->from('tOrderWithdrawal');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('IsCleared', 0);
		return $this->db->get()->num_rows();
	}

	//menu

	function get_definedFieldSection($MenuBarType = ['common'])
	{
		$this->db->select('*');
		if (!empty($MenuBarType)) {
			$this->db->where_in('mResources.MenuBarType', $MenuBarType);
		}
		$this->db->where_in('mResources.FieldSection', ['WORKFLOW', 'SUPERVISION', 'CLIENT', 'ACCESS CONTROL', 'WORKFLOW SETUP', 'PRODUCT SETUP']);
		$this->db->group_by('FieldSection');
		$this->db->order_by("FIELD(FieldSection,'WORKFLOW','SUPERVISION','CLIENT','ACCESS CONTROL','WORKFLOW SETUP','PRODUCT SETUP')");
		$query = $this->db->get('mResources');
		return $query->result();
	}

	function get_definedleftDynamicMenu_options($MenuBarType = ['common', 'sidebar'])
	{
		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID');
		$this->db->join('mCustomerWorkflowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mResources.WorkflowModuleUID AND mCustomerWorkflowModules.CustomerUID = "' . $this->parameters['DefaultClientUID'] . '"', 'LEFT');
		if (!empty($MenuBarType)) {
			$this->db->where_in('mResources.MenuBarType', $MenuBarType);
		}
		$this->db->where('mRoleResources.RoleUID', $this->RoleUID);
		$this->db->where('mResources.Active', 1);

		if (!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->like('mResources.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->where_in('mResources.FieldSection', ['MISCHELLANEOUS', 'WORKFLOW', 'SUPERVISION']);

		$this->db->where('(mResources.WorkflowModuleUID IS NULL OR mCustomerWorkflowModules.WorkflowModuleUID IS NOT NULL)', NULL, FALSE);
		$this->db->order_by("FIELD(FieldSection,'MISCHELLANEOUS','WORKFLOW','SUPERVISION')");
		$this->db->order_by('mCustomerWorkflowModules.Position', 'ASC');
		$this->db->order_by('mResources.Position', 'ASC');
		return $this->db->get()->result();
	}

	function get_definedDynamicMenu_options($FieldSection = ['WORKFLOW'], $MenuBarType = ['common'])
	{
		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID');
		$this->db->join('mCustomerWorkflowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mResources.WorkflowModuleUID AND mCustomerWorkflowModules.CustomerUID = "' . $this->parameters['DefaultClientUID'] . '"', 'LEFT');
		$this->db->where_in('mResources.FieldSection', $FieldSection);
		if (!empty($MenuBarType)) {
			$this->db->where_in('mResources.MenuBarType', $MenuBarType);
		}
		$this->db->where('mRoleResources.RoleUID', $this->RoleUID);

		if (!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->like('mResources.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->order_by('mCustomerWorkflowModules.Position', 'ASC');

		$this->db->where('mResources.Active', 1);
		$this->db->order_by('mResources.Position', 'ASC');
		return $this->db->get()->result();
	}

	function get_definedWorkflowDynamicMenu_options($FieldSection = ['WORKFLOW'], $MenuBarType = ['common'])
	{
		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID');
		$this->db->join('mCustomerWorkflowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mResources.WorkflowModuleUID', 'LEFT');

		$this->db->where_in('mResources.FieldSection', $FieldSection);
		if (!empty($MenuBarType)) {
			$this->db->where_in('mResources.MenuBarType', $MenuBarType);
		}
		$this->db->where('(mResources.WorkflowModuleUID IS NULL OR mCustomerWorkflowModules.WorkflowModuleUID IS NOT NULL)', NULL, FALSE);

		$this->db->where_in('mResources.FieldSection', ['MISCHELLANEOUS', 'WORKFLOW', 'SUPERVISION']);

		$this->db->where('mRoleResources.RoleUID', $this->RoleUID);
		$this->db->where('mResources.Active', 1);

		if (!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->like('mResources.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->order_by('mCustomerWorkflowModules.Position', 'ASC');
		$this->db->order_by('mResources.Position', 'ASC');
		$this->db->group_by('mResources.ResourceUID');
		return $this->db->get()->result();
	}

	public function insertNotes($data)
	{
		$this->db->insert('tNotes', $data);
		return true;
	}

	function multipledocchaseexists($OrderUID)
	{
		$this->db->select('(SELECT count(*) from tDocumentCheckList LEFT JOIN mWorkFlowModules w on w.WorkflowModuleUID = tDocumentCheckList.WorkflowUID  WHERE OrderUID = ' . $OrderUID . ' and w.WorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID and Answer = "Problem Identified" and IsChaseSend = "YES") as QuestionCount,tOrderDocChase.*,a.ReasonName as RaisedReasonName,b.ReasonName as ClearedReasonName,c.UserName as RaisedUserName, d.UserName as ClearedUserName,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName');
		$this->db->from('tOrderDocChase');
		$this->db->join('mUsers c', 'c.UserUID = tOrderDocChase.RaisedByUserUID', 'LEFT');
		$this->db->join('mUsers d', 'd.UserUID = tOrderDocChase.ClearedByUserUID', 'LEFT');
		$this->db->join('mReasons a', 'a.ReasonUID = tOrderDocChase.ReasonUID', 'LEFT');
		$this->db->join('mReasons b', 'b.ReasonUID = tOrderDocChase.ClearedReasonUID', 'LEFT');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = tOrderDocChase.WorkflowModuleUID', 'LEFT');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('tOrderDocChase.IsCleared', 0);
		return $this->db->get()->result();
	}
	function GetCommandsDetails($OrderUID, $WorkflowUID)
	{
		$this->db->select('*,mUsers.UserName,tNotes.CreatedByUserUID');
		$this->db->select('(SELECT 1 FROM tNotesUser WHERE tNotesUser.NotesUID = tNotes.NotesUID AND IsRead = 1 AND UserUID = '.$this->loggedid.') AS IsRead',FALSE);
		$this->db->from('tNotes');
		$this->db->join('mUsers', 'mUsers.UserUID = tNotes.CreatedByUserUID', 'LEFT');
		$this->db->where(array('OrderUID' => $OrderUID, 'WorkflowUID' => $WorkflowUID));
		$this->db->order_by('CreateDateTime', 'DESC');
		return $this->db->get()->result();
	}

	function sendCommands($data)
	{
		if ($this->db->insert('tNotes', $data)) {
			$insert_id = $this->db->insert_id();
			$result = $this->db->select('*,mUsers.UserName')->from('tNotes')->where(array('NotesUID' => $insert_id))->join('mUsers', 'mUsers.UserUID=tNotes.CreatedByUserUID', 'left')->order_by('CreateDateTime', 'DESC')->get()->row();

			$Avatar = file_exists($result->Avatar) ? $result->Avatar : 'assets/img/profile.jpg';
			$data = '<div class="col-md-12 cmd_sec_div">
			<img class="cmd_img" src="'.$Avatar.'"/>
			<div class="cmd_sec_view">
			<p class="Uname">' . $result->UserName . '<span class="cm_date">' . date('m/d/Y H:i A', strtotime($result->CreateDateTime)) . '</span></p>
			<p class="Comments">' . $result->Description . '</p>
			</div>
			</div>';
			return $data;
		} else {
			return false;
		}
	}

	function checklistneeddocchase($OrderUID, $WorkflowUID)
	{
		if (is_array($WorkflowUID)) {
			$WorkflowUID = implode(',', $WorkflowUID);
		}
		return $this->db->query('SELECT EXISTS(SELECT * FROM `tDocumentCheckList` WHERE  OrderUID="' . $OrderUID . '" AND IsChaseSend="YES" AND WorkflowUID IN (' . $WorkflowUID . ') ) AS available')->row()->available;
	}

	function display_docchasemenu($OrderUID)
	{

		return $this->db->query('SELECT EXISTS(SELECT * FROM `tOrderDocChase` WHERE  OrderUID="' . $OrderUID . '" AND IsCleared=0) AS available')->row()->available;
	}

	function get_mParkingType()
	{
		$this->db->select('*')->from('mParkingType');
		return $this->db->get()->result();
	}

	function differenceInHours($startdate, $enddate)
	{
		$startdate = ($startdate == '0000-00-00 00:00:00') ? '' : $startdate;
		/* $enddate = ($enddate == '0000-00-00 00:00:00') ? '' : $enddate; */
		$enddate = Date('Y-m-d H:i:s');
		if ($startdate != '' && $enddate != '') {
			$starttimestamp = strtotime($startdate);
			$endtimestamp = strtotime($enddate);
			$difference = abs($endtimestamp - $starttimestamp) / 3600;
			return round($difference);
		} else {
			return 0;
		}
	}

	function getLastCompletedWorkflow($OrderUID)
	{
		$this->db->select('WorkflowModuleUID');
		$this->db->from('tOrderAssignments');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('WorkflowStatus', 5);
		$this->db->order_by('WorkflowModuleUID', 'DESC');
		$this->db->limit(1);
		return $this->db->get()->row();
	}

	function GetServiceType()
	{
		return $this->db->query('SELECT distinct(ServiceType) from tOrders where')->row()->available;
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** Monday 09 March 2020 **/
	/** HOI PAYOFF WORKFLOW ADD **/
	/** HOI Queue Common Query **/
	function GetHOIQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['HOI'];

		$status[] = $this->config->item('keywords')['Cancelled'];


		/*########## GEt Workflow results using another db connection ############*/
		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Thursday 12 March 2020
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['HOI']);
		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}


		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');



		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["HOI"] . '"', 'left');

		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['HOI'] . "'");

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');





		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['HOI'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);



		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['HOI'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['HOI'] . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
				ELSE TRUE END
				ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['HOI']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}

	/** Borrower Doc Queue Common Query **/
	function GetBorrowerDocQueue($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['BorrowerDoc'];

		$status[] = $this->config->item('keywords')['Cancelled'];


		/*########## GEt Workflow results using another db connection ############*/
		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Thursday 12 March 2020
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['BorrowerDoc']);
		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}


		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["BorrowerDoc"] . '"', 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['BorrowerDoc'] . "'");
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);




		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['BorrowerDoc'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}



		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['BorrowerDoc'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['BorrowerDoc'] . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['BorrowerDoc']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** Monday 09 March 2020 **/
	/** HOI PAYOFF WORKFLOW ADD **/
	/*PayOff Queue Common Query*/
	function GetPayOffQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['PayOff'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['PayOff']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["PayOff"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['PayOff'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['PayOff'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['PayOff']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['PayOff'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['PayOff'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/
		if (!isset($Conditions['OrderByexception']))
		$this->db->group_by('tOrders.OrderUID');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Wednesday 11 March 2020 **/
	/** @description get previous dependent workflows **/
	function getDependentworkflows($WorkflowModuleUID)
	{
  		$this->otherdb2 = $this->load->database('otherdb', TRUE);

		$this->otherdb2->select('DependentWorkflowModuleUID');
		$this->otherdb2->from('mCustomerDependentWorkflowModules');
		$this->otherdb2->join('mWorkFlowModules', 'mCustomerDependentWorkflowModules.DependentWorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID');
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb2->where('mCustomerDependentWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->otherdb2->where("mCustomerDependentWorkflowModules.WorkflowModuleUID", $WorkflowModuleUID);
		$result =  $this->otherdb2->get()->result();
		return array_column($result, 'DependentWorkflowModuleUID');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Wednesday Thursday 12 March 2020 **/
	/** @description get customer workflows **/
	function getCustomer_Workflows($CustomerUID)
	{
		if (!empty($CustomerUID)) {
			$this->db->select('mWorkFlowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName');
			$this->db->from('mCustomerWorkflowModules');
			$this->db->join('mWorkFlowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID');
			$this->db->where('mCustomerWorkflowModules.CustomerUID', $CustomerUID);
			$this->db->where('mWorkFlowModules.Active', STATUS_ONE);
			$this->db->group_by('mCustomerWorkflowModules.WorkflowModuleUID');
			$this->db->order_by('mCustomerWorkflowModules.WorkflowModuleUID');
			return $this->db->get()->result();
		}
		return [];
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Wednesday Thursday 12 March 2020 **/
	/** @description get active customers **/
	function getActivecustomer()
	{
		$this->db->select("*");
		$this->db->from("mCustomer");
		$this->db->where("mCustomer.Active", STATUS_ONE);
		$this->db->Order_by("mCustomer.CustomerUID");
		return $this->db->get()->row();
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Monday 16 March 2020 **/
	/** @description get workflow by systemname **/
	function getWorkflow_by_systemname($SystemName)
	{
		/*Query*/
		$this->db->select('WorkflowModuleUID,WorkflowModuleName');
		$this->db->from('mWorkFlowModules');
		$this->db->where('SystemName', $SystemName);
		return $this->db->get()->row();
	}


	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Monday 16 March 2020 **/
	/** @description get workflowuid **/
	function get_workflowprioritytime($OrderUID, $WorkflowModuleUID)
	{
		/*Query*/
		$this->db->select('SLA');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('tOrders', 'tOrders.CustomerUID = mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID');
		$this->db->where('mCustomerWorkflowModules.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$result = $this->db->get()->row();
		if (!empty($result)) {
			return $result->SLA;
		} else {
			return NULL;
		}
	}

	function Milestone()
	{
		$this->db->select('*');
		$this->db->from('mMilestone');
		$this->db->where('Active', 1);
		return $this->db->get()->result();
	}


	/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
	/** @date Wednesday 18 March 2020 **/
	/** @description get previous metric dependent workflows **/
	function getMetricsDependentworkflows($CustomerUID, $WorkflowModuleUID)
	{
		
		$this->otherdb->select('mCustomerWorkflowMetrics.*, GROUP_CONCAT(DISTINCT DependentWorkflowModuleUID) AS DependentWorkflowModuleUIDs', false);
		$this->otherdb->from('mCustomerWorkflowMetrics');
		$this->otherdb->join('mCustomerWorkflowMetricsDependentWorkflows', 'mCustomerWorkflowMetricsDependentWorkflows.CustomerWorkflowMetricUID = mCustomerWorkflowMetrics.CustomerWorkflowMetricUID');
		$this->otherdb->join('mWorkFlowModules', 'mCustomerWorkflowMetricsDependentWorkflows.DependentWorkflowModuleUID = mWorkFlowModules.WorkflowModuleUID');
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($CustomerUID) && !empty($CustomerUID)) {
			$this->otherdb->where('mCustomerWorkflowMetrics.CustomerUID', $CustomerUID);
		}
		$this->otherdb->where("mCustomerWorkflowMetrics.WorkflowModuleUID", $WorkflowModuleUID);
		$this->otherdb->group_by('mCustomerWorkflowMetrics.CustomerWorkflowMetricUID');
		$this->otherdb->order_by('mCustomerWorkflowMetrics.Priority', 'ASC');
		$Workflows =  $this->otherdb->get()->result();

		$CASE_WHEN = [];
		foreach ($Workflows as $key => $workflow) {
			$DependentWorkflows = explode(",", $workflow->DependentWorkflowModuleUIDs);
			$WHERE = [];
			foreach ($DependentWorkflows as $key => $dworkflow) {
				if (!empty($dworkflow) && is_numeric($dworkflow)) {
					$WHERE[] = "CASE WHEN (SELECT 1 FROM tOrderWorkflows t WHERE t.OrderUID = tOrders.OrderUID AND t.IsPresent = 1 AND t.WorkflowModuleUID = '" . $dworkflow . "') THEN CASE WHEN (SELECT 1 FROM tOrderAssignments ta WHERE ta.OrderUID = tOrders.OrderUID AND ta.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' AND ta.WorkflowModuleUID = '" . $dworkflow . "') THEN TRUE ELSE FALSE END ELSE TRUE END";
				}
			}

			if (!empty($WHERE)) {
				$CASE_WHEN[] = "( CASE WHEN (" . implode(" AND ", $WHERE) . " ) THEN 1 ELSE 2 END ) ASC";
			}
		}
		if (!empty($CASE_WHEN)) {
			return implode(", ", $CASE_WHEN);
		}
		return "";
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Thursday 19 March 2020 **/
	/** @description PE Orders **/
	function GetPEOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['PE'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['PE']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["PE"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['PE'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['PE'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['PE']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}
		

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['PE'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['PE'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
	   if (!isset($Conditions['OrderByexception']))
		$this->db->group_by('tOrders.OrderUID');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Thursday 19 March 2020 **/
	/** @description FinalApproval Orders **/
	function GetFinalApprovalOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['FinalApproval'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['FinalApproval']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["FinalApproval"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['FinalApproval'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');




		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['FinalApproval'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['FinalApproval']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['FinalApproval'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['FinalApproval'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		  if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Thursday 19 March 2020 **/
	/** @description CD Orders **/
	function GetCDOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['CD'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['CD']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["CD"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['CD'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');


		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['CD'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['CD']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['CD'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['CD'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*ClosingDisclosureSendDate is blank*/
		$this->db->where('(tOrderImport.ClosingDisclosureSendDate IS NULL OR tOrderImport.ClosingDisclosureSendDate = "")', NULL, FALSE);

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}

	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** @date Friday 20 March 2020 **/
	/** @description parking queue enabled based on workflow**/
	function is_parking_enabledforworkflow($WorkflowModuleUID)
	{
		
		$this->otherdb->select('*');
		$this->otherdb->from('mCustomerWorkflowModules');
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where('mCustomerWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->otherdb->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->otherdb->where('IsParkingRequire', STATUS_ONE);
		return $this->otherdb->get()->row();
	}

	function is_kickback_enabledforworkflow($WorkflowModuleUID)
	{
		$this->db->select('*');
		$this->db->from('mCustomerWorkflowModules');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mCustomerWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsKickBackRequire', STATUS_ONE);
		return $this->db->get()->row();
	}

	/**
	 *Expiry Orders queue enabled based on workflow
	 *@author SathishKumar <sathish.kumar@avanzegroup.com>
	 *@since Tuesday 28 July 2020
	 */
	function is_IsExpiryOrdersRequire_enabledforworkflow($WorkflowModuleUID)
	{
		$this->db->select('*');
		$this->db->from('mCustomerWorkflowModules');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mCustomerWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsExpiryOrdersRequire', STATUS_ONE);
		return $this->db->get()->row();
	}

	/**
	 *Parking queue enabled based on workflow
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Friday 20 March 2020
	 */
	function is_autoparking_enabledfor_orderworkflow($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('mCustomerWorkflowModules.WorkflowModuleUID,mCustomerWorkflowModules.ParkingDuration,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('tOrders', 'tOrders.CustomerUID = mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->where(['tOrders.OrderUID' => $OrderUID, 'mCustomerWorkflowModules.WorkflowModuleUID' => $WorkflowModuleUID, 'IsParkingRequire' => STATUS_ONE, 'ParkingType' => 'Auto']);
		return $this->db->get()->row();
	}

	function getStatus()
	{
		$this->db->select('*');
		$this->db->from('mStatus');
		$this->db->where('Active', 1);
		return $this->db->get()->result();
	}

	/**
	 *Function get workflow header 
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Friday 24 April 2020ate
	 */
	function get_definedWorkflowMenu_options($FieldSection = ['ORDERWORKFLOW'], $MenuBarType = [])
	{
		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID');
		$this->db->join('mCustomerWorkflowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mResources.WorkflowModuleUID AND mCustomerWorkflowModules.CustomerUID = "' . $this->parameters['DefaultClientUID'] . '"', "LEFT");
		if (!empty($MenuBarType)) {
			$this->db->where_in('mResources.MenuBarType', $MenuBarType);
		}
		$this->db->where('(mResources.WorkflowModuleUID IS NULL OR mCustomerWorkflowModules.WorkflowModuleUID IS NOT NULL)', NULL, FALSE);

		$this->db->where_in('mResources.FieldSection', $FieldSection);
		$this->db->where('mRoleResources.RoleUID', $this->RoleUID);
		$this->db->where('mResources.Active', 1);
		$this->db->order_by('ISNULL(mCustomerWorkflowModules.Position),mCustomerWorkflowModules.Position', 'ASC');
		$this->db->order_by('mResources.Position', 'ASC');
		return $this->db->get()->result();
	}


	/**
	 *Function get Queue Buttons 
	 *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 *@since Friday 25 April 2020
	 */
	public function getAvailableQueueButtons($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('mQueues.*, mWorkFlowModules.*, "' . $OrderUID . '" AS OrderUID', false);
		$this->db->from('mQueues');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID', 'left');
		$this->db->where('mQueues.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('mQueues.QueueUID NOT IN (SELECT DISTINCT QueueUID FROM tOrderQueues WHERE tOrderQueues.OrderUID = "' . $OrderUID . '" AND tOrderQueues.QueueStatus = "Pending" AND tOrderQueues.QueueUID IS NOT NULL)',NULL,FALSE);
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('mQueues.Active', 1);
		return $this->db->get()->result();
	}

	/**
	 *Function get Queue Buttons 
	 *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 *@since Friday 25 April 2020
	 */
	public function getPendingQueueButtons($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('mQueues.*, mWorkFlowModules.*, "' . $OrderUID . '" AS OrderUID', false);
		$this->db->from('mQueues');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID', 'left');
		$this->db->where('mQueues.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('mQueues.QueueUID IN (SELECT DISTINCT QueueUID FROM tOrderQueues WHERE OrderUID = "' . $OrderUID . '" AND QueueStatus = "Pending")');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('mQueues.Active', 1);
		return $this->db->get()->result();
	}

	/**
	 *Function get CustomerWorkflowQueues 
	 *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 *@since Friday 25 April 2020
	 */
	public function getCustomerWorkflowQueues($WorkflowModuleUID, $QueueUID = FALSE, $CustomerUID = FALSE)
	{
		$this->db->select('mQueues.*, mWorkFlowModules.*', false);
		$this->db->from('mQueues');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID', 'left');
		$this->db->where('mQueues.WorkflowModuleUID', $WorkflowModuleUID);
		if ($CustomerUID) {
			$this->db->where('mQueues.CustomerUID', $CustomerUID);
		} else {
			if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
				$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
			}
		}

		$this->db->where('mQueues.Active', 1);
		if ($QueueUID) {
			$this->db->where_in('mQueues.QueueUID', $QueueUID);
		}
		return $this->db->get()->result();
	}

	/**
	 *Function role by user
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Sunday 26 April 2020
	 */

	function get_rolepermissions()
	{

		$this->db->select('*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID');
		$this->db->where('mRoleResources.RoleUID', $this->RoleUID);
		$this->db->where('mResources.Active', 1);
		$output =  $this->db->get()->result();
		return array_column($output, 'controller');
	}


	/**
	 *Function check doc chase parking enabled
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Date
	 */

	function check_docchaseparking_enabled($OrderUID)
	{
		$this->db->select('*')->from('tOrderParking');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('IsCleared', 0);
		$this->db->where('IsDocChaseParking', 1);
		return $this->db->get()->num_rows();
	}

	/**
	 *Function check Doc Chase Escalation enabled
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Tuesday 28 April 2020
	 */
	function check_docchaseesclation_enabled($OrderUID)
	{
		$this->db->select('*')->from('tOrderEsclation');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('IsCleared', 0);
		$this->db->where('IsDocChaseEscalation', 1);
		return $this->db->get()->num_rows();
	}

	/**
	 *Function fetch dynamic checklist fields
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Wednesday 29 April 2020
	 */
	function get_dynamicchecklistfields($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('mCustomerField.*,mFields.*,mStates.StateCode')->from('mCustomerField');
		$this->db->join('mFields', 'mFields.FieldUID = mCustomerField.FieldUID');
		$this->db->join('mStates', 'mStates.StateUID = mCustomerField.StateUID', 'LEFT');
		$this->db->join('tOrders', 'tOrders.CustomerUID = mCustomerField.CustomerUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('mCustomerField.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->order_by('mCustomerField.Position');
		return $this->db->get()->result();
	}

	/**
	 *Function fetch dynamic checklist dropdown fields
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Wednesday 29 April 2020
	 */
	function get_dynamicchecklistdropdownfields($FieldUID)
	{
		$this->db->select('*')->from('mFieldDropDown');
		$this->db->where('mFieldDropDown.FieldUID', $FieldUID);
		return $this->db->get()->result();
	}

	/**
	 *Function validateCovertDateToFormat
	 *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 *@since Tuesday 1 May 2020
	 */
	function validateConvertDateToFormat($value, $format = "m/d/Y h:i A")
	{

		if ($value == '0000-00-00 00:00:00' ||  $value == '0000-00-00') {
			return '';
		}
	
		preg_match_all("/[\/|-]/", $value, $matches);
		if (!empty($matches[0]) && count($matches[0]) == 2 && date("Y-m-d H:i:s", strtotime($value)) > date("Y-m-d", strtotime("1970-01-02 01:00:00"))) {

			$parts = explode(" ", $value);
			list($month, $day,$year) = explode("/", $parts[0]);

			if(empty($year) || empty($month) || empty($day)) {
				list($month, $day,$year) = explode("-", $parts[0]);
			}

			$value = str_replace('-', '/', $value);


			if(!checkdate($month, $day, $year) && isTime($parts[1])){
				//time only
				return date($this->config->item('timeonly_format'), strtotime($value));

			} else if(checkdate($month, $day, $year) && !isTime($parts[1])) {
				//date only
				return date($this->config->item('dateonly_format'), strtotime($value));

			}else if(checkdate($month, $day, $year) && isTime($parts[1])) {
				//time only
				return date($this->config->item('date_format'), strtotime($value));
			}

			return date($format, strtotime($value));
		}

		return $value;
	}

	/**
	 *Function get customer details based on order
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Tuesday 05 May 2020
	 */
	function getorder_customer_details($OrderUID)
	{
		$this->db->select("mCustomer.*");
		$this->db->from("mCustomer");
		$this->db->join('tOrders', 'tOrders.CustomerUID = mCustomer.CustomerUID');

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->row();
	}

	/**
	 *Function fetch processors
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Wednesday 06 May 2020
	 */

	function get_allprocessors()
	{
		
		$this->otherdb->select('mUsers.UserName,mUsers.UserUID');
		$this->otherdb->from('mGroupUsers');
		$this->otherdb->join('mUsers',  'mUsers.UserUID = mGroupUsers.GroupUserUID');
		$this->otherdb->where('mUsers.Active', 1);
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where("EXISTS (SELECT 1 FROM mGroupCustomers WHERE mGroupCustomers.GroupUID = mGroupUsers.GroupUID AND mGroupCustomers.GroupCustomerUID = " . $this->parameters['DefaultClientUID'] . " )", NULL, FALSE);
		}
		$this->otherdb->group_by('mUsers.UserUID');
		return $this->otherdb->get()->result();
	}

	/**
	 *Function fetch all team leads
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Tuesday 12 May 2020
	 */

	function get_allteamleads()
	{
		
		$this->otherdb->select('mUsers.UserName,mUsers.UserUID');
		$this->otherdb->from('mGroupTeamLeaders');
		$this->otherdb->join('mUsers',  'mUsers.UserUID = mGroupTeamLeaders.GroupTeamUserUID');
		$this->otherdb->where('mUsers.Active', 1);
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where("EXISTS (SELECT 1 FROM mGroupCustomers WHERE mGroupCustomers.GroupUID = mGroupTeamLeaders.GroupUID AND mGroupCustomers.GroupCustomerUID = " . $this->parameters['DefaultClientUID'] . " )", NULL, FALSE);
		}

		$this->otherdb->group_by('mUsers.UserUID');
		return $this->otherdb->get()->result();
	}

	function get_groupusersbyteamleads($TeamLeadUserUID)
	{
		
		$this->otherdb->select('mUsers.UserName,UserUID');
		$this->otherdb->from('mGroupUsers');
		$this->otherdb->join('mUsers',  'mUsers.UserUID = mGroupUsers.GroupUserUID');
		$this->otherdb->where('mGroupUsers.GroupUID IN (SELECT GroupUID FROM mGroupTeamLeaders WHERE GroupTeamUserUID = ' . $TeamLeadUserUID . ' )', NULL, FALSE);
		$this->otherdb->where('mUsers.Active', 1);
		$this->otherdb->group_by('mUsers.UserUID');
		return $this->otherdb->get()->result();
	}

	/**
	*Function get processor by junior processor 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 27 August 2020.
	*/
	function get_ProcessorsbyjuniorProcessor($JuniorProcessorUserUID)
	{
		
		$this->otherdb->select('mUsers.UserName,mUsers.UserUID');
		$this->otherdb->from('mJuniorProcessorUsers');
		$this->otherdb->join('mJuniorProcessorGroup','mJuniorProcessorGroup.GroupUID = mJuniorProcessorUsers.GroupUID','left');
		$this->otherdb->join('mUsers','(mUsers.UserUID = mJuniorProcessorUsers.ProcessorUserUID) OR (mUsers.UserUID = mJuniorProcessorGroup.JuniorProcessorUserUID)','left');
		$this->otherdb->where('mJuniorProcessorGroup.JuniorProcessorUserUID',$JuniorProcessorUserUID);
		$this->otherdb->where('mJuniorProcessorGroup.Active',STATUS_ONE);
		$this->otherdb->group_by('mUsers.UserUID');
		return $this->otherdb->get()->result();
	}


	/**
	 *@description Function to getWorkflowQueuesColumns
	 *
	 * @param $WorkflowModuleUID
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.5.2020 
	 * @version Dynamic Queues 
	 *
	 */
	function getWorkflowQueuesColumns($WorkflowModuleUID, $nosort = false)
	{
		/*Query*/
		$this->db->select('mQueueColumns.*,mSubQueueCategory.*');
		$this->db->from('mQueueColumns');
		$this->db->join('mSubQueueCategory','mSubQueueCategory.SubQueueCategoryUID = mQueueColumns.SubQueueCategoryUID','left');
		$this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);
		$this->db->where('WorkflowUID', $WorkflowModuleUID);
		if ($nosort) {
			$this->db->where('NoSort <> 1', NULL, FALSE);
		}
		$this->db->group_by('HeaderName');
		$this->db->order_by('Position');
		return $this->db->get()->result();
	}


	function dynamicColumnNames($QueueColumns, $TableName = '', $QueueUID = '')
	{
		$DynamicColumns = [];
		foreach ($QueueColumns as $key => $value) {

			if (!empty($TableName) || !empty($QueueUID)) {
				if ($this->CheckQueueColumnIsEnabled($value->StaticQueueUIDs, $value->QueueUIDs, $TableName, $QueueUID)) {
					continue;
				}
			}

			$explodedarray = explode(".", $value->ColumnName);
			$column_name = end($explodedarray);

			if (!empty($value->IsChecklist)) {

				$columnquery = $this->get_dynamiccolumnquery_bycolumname($value);
				$DynamicColumns[] = !empty($columnquery) ? $columnquery : $value->SortColumnName;
			} else if ($value->NoSort == 1) {
				if (!empty($value->SortColumnName)) {
					$columnquery = $this->get_dynamiccolumnquery_bycolumname($value);
					$DynamicColumns[] = !empty($columnquery) ? $columnquery : $value->SortColumnName;
				} else {
					$DynamicColumns[] = '';
				}
			} else {
				$DynamicColumns[] = $value->ColumnName;
			}
		}

		return $DynamicColumns;
	}

	function dynamicColumnHeaderNames($QueueColumns, $Mischallenous = [])
	{
		$DynamicColumns = [];
		foreach ($QueueColumns as $key => $value) {

			if ($this->CheckQueueColumnIsEnabled($value->StaticQueueUIDs, $value->QueueUIDs, $Mischallenous['SubQueueSection'], $Mischallenous['SubQueueDetails']->QueueUID)) {
				continue;
			}

			if ($value->ColumnName == "SubQueueCategories") {
				
				if (empty($Mischallenous['SubQueueSection']) && empty($Mischallenous['SubQueueDetails']->QueueUID)) {

					continue;
				}

				if (!empty($Mischallenous['SubQueueSection']) && !empty($value->SubQueueSection)) {
					
					// Static Queue
					if ($value->SubQueueSection != $Mischallenous['SubQueueSection']) {

						continue;
					}
				} elseif (!empty($Mischallenous['SubQueueDetails']->QueueUID) && !empty($value->SubQueueUID)) {
					
					// Exception Queue
					if ($value->SubQueueUID != $Mischallenous['SubQueueDetails']->QueueUID) {

						continue;
					}
				} else {

					continue;
				}

			}			
			
			$DynamicColumns[] = $value->HeaderName;
		}

		return $DynamicColumns;
	}

	/**
	*Function Get Notification Enabled Queues 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 18 September 2020.
	*/
	public function GetNotificationEnabledQueues($WorkflowModuleUID)
	{
		$this->db->select('mQueues.QueueName');
		$this->db->from('mQueuesNotification');
		$this->db->join('mQueues','mQueues.QueueUID = mQueuesNotification.QueueUID AND mQueues.WorkflowModuleUID = mQueuesNotification.WorkflowModuleUID','LEFT');
		$this->db->where('mQueuesNotification.WorkflowModuleUID',$WorkflowModuleUID);
		$result = $this->db->get()->result();
		return array_column($result, 'QueueName');
	}

	/**
	*Function Get Categories 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 30 September 2020.
	*/
	function GetSubQueueCategories()
	{
		return $Categories = $this->db->select('*')->from('mCategories')->get()->result_array();
	}

	/**
	 *@description Function to getDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.5.2020 
	 * @version Dyamc Queues 
	 *
	 */
	function getDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{

		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		// Get Categories Details
		$SubQueueCategories = $this->GetSubQueueCategories();

		$LastNotesDays = $this->Common_Model->get_settings_value('LastNotesDays');

		if (!empty($QueueColumns)) {
			$orderslist = [];

			foreach ($ArrayList as $myorders) {
				$row = array();

				// Reset Arr
				$SubQueueCategory = [];

				foreach ($QueueColumns as $key => $column) {

					if ($this->CheckQueueColumnIsEnabled($column->StaticQueueUIDs, $column->QueueUIDs, $Mischallenous['SubQueueSection'])) {
						continue;
					}

					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);

					// Get notification enabled queues
					$NotificationEnabledQueues = $this->GetNotificationEnabledQueues($column->QueueWorkflowUID);

					if (strripos($column_name, "StatusName") !== false || strripos($column_name, "CurrentStatus") != false) {

						$row[] = '<a  href="javascript:void(0)" style=" background: ' . $myorders->StatusColor . ' !important;padding: 5px 10px;border-radius:0px;" class="btn">' . $myorders->StatusName . '</a>';

					} else if ($column_name == 'WorkflowQueue') {

						$daysleft = $this->get_expiredchecklistalertrow($myorders,$column->QueueWorkflowUID);
						if(isset($Mischallenous['Priority_Report']) && ((!empty($NotificationEnabledQueues) && in_array($myorders->{$column_name . $column->QueueWorkflowUID}, $NotificationEnabledQueues)) || ($column->QueueWorkflowUID == $this->config->item('Workflows')['Workup'] && $myorders->{$column_name . $column->QueueWorkflowUID} == "Kickback Order"))) {

							//$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID}.$daysleft : NULL;

							$row[] = !empty($column->QueueWorkflowUID) ? '<a href="javascript:;" class="viewnotes" data-orderuid="'.$myorders->OrderUID.'" data-workflowmoduleuid="'.$column->QueueWorkflowUID.'">'.$myorders->{$column_name . $column->QueueWorkflowUID}.$daysleft.'<span title="No Unread Message(s)" class="badgenotification-unreadnotes badgenotification-readnotes"></span></a>' : NULL;
						} else {

							$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID}.$daysleft : NULL;
						}


					} else if (strripos($column_name, "KickbackAssociate") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;

					} else if (strripos($column_name, "KickbackRemarks") !== false) {

						if (strlen($myorders->{$column_name . $column->QueueWorkflowUID}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name . $column->QueueWorkflowUID}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name . $column->QueueWorkflowUID}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name . $column->QueueWorkflowUID}, 0,25).'</div>';
						}

					} else if (strripos($column_name, "ReversedWorkflows") !== false) {

						if (strlen($myorders->{$column_name}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'</div>';
						}

					} else if (strripos($column_name, "ReversedWorkflowsComments") !== false) {

						if (strlen($myorders->{$column_name}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'</div>';
						}

					} else if (strripos($column_name, "KickbackDate") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {

						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};

					} else if ($column_name == 'WorkflowCompletedDate') {

						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});

					} else if (strripos($column_name, "ScheduledDate") !== false) {

						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = '<input type="text" data-orderuid="' . $myorders->OrderUID . '" id="schedule_date_' . $myorders->OrderUID . '" class="schedule_date ' . $select . '" value="' . $myorders->ScheduledDate . '">';

					} else if (strripos($column_name, "ProcessorChosenClosingDate") !== false) {
						
						if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && in_array($this->RoleType, $this->config->item('ProcssorChosenDateEditable_Roles'))) {
							
							$row[] = '<input type="text" data-orderuid="' . $myorders->OrderUID . '" id="ProcessorChosenClosingDate_' . $myorders->OrderUID . '" class="ProcessorChosenClosingDate" value="' . $myorders->ProcessorChosenClosingDate . '" readonly>';
						} else {

							$row[] = $myorders->ProcessorChosenClosingDate;
						}
						

					} else if ($column_name == 'ScheduledTime') {

						$row[] = '<input type="text" data-orderuid="' . $myorders->OrderUID . '" id="schedule_time_' . $myorders->OrderUID . '" class="schedule_time" value="' . $myorders->ScheduledTime . '">';

					} else if ($column_name == 'LOGIC-STC') {

						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;

					} else if ($column_name == 'LOGIC-LCREQUIRED') {

						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);

					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					}  else if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {

						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column->DocumentTypeUID . $column_name});
					
					} elseif(!empty($column->FieldUID)) {

						if($column->FieldType == 'date')
						{	
							if($myorders->{$column_name} == '0000-00-00 00:00:00' || empty($myorders->{$column_name}))
							{
								$date = '';
							}
							else
							{
								$date = date("m/d/Y",strtotime($myorders->{$column_name}));
							}

							$row[] = '
							<input type="text"  data-LastUpdated="'.$date.'" data-ColumnName = "'.$column_name.'" data-orderuid="'.$myorders->OrderUID.'" title="" name="date" class="form-control tabledatepicker" value="'.$date.'">
							';
						}
						else if($column->FieldType == 'label' && $column_name == 'Comments')
						{
							$row[] = '<span contenteditable="true"  data-orderuid="'.$myorders->OrderUID.'" class="form-control comments_editable" style="padding: 5px;">'.$myorders->Comments.'</span><input type="hidden" name="LastUpdateComments" id="LastUpdateComments" value="'.$myorders->Comments.'">';
						}
						else if($column->FieldType == 'label')
						{
							if($myorders->{$column_name} == '0000-00-00 00:00:00' || empty($myorders->{$column_name}))
							{
								$date = '';
							}
							else
							{
								$date = date("m/d/Y",strtotime($myorders->{$column_name}));
							}
							$row[] = '<span data-ColumnName = "'.$column_name.'" data-ExpirationDuration="'.$column->ExpirationDuration.'" data-orderuid="'.$myorders->OrderUID.'" class="bmd-form-group '.$column->FieldName.'" style="padding: 5px;">'.$date.'</span>';
						}
					
					} else if (strripos($column_name, "logic") !== false) {
					
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						if (!empty($myorders->ProblemIdentifiedChecklists)) {
							if (strlen($myorders->ProblemIdentifiedChecklists) > 25) {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">' . substr($myorders->ProblemIdentifiedChecklists, 0, 25) . '<span class="morecontent"><span style="display: none;">' . substr($myorders->ProblemIdentifiedChecklists, 25) . '</span>&nbsp;&nbsp;<a href="" class="morelinktoggle">...</a></span></div>';
							} else {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">' . substr($myorders->ProblemIdentifiedChecklists, 0, 25) . '</div>';
							}
						} else {
							$row[] = '';
						}
					} 
					else if($column->ColumnName == 'PriorityIssueChecklists')
					{

						if (!empty($myorders->PriorityIssueChecklists)) {
							if (strlen($myorders->PriorityIssueChecklists) > 25) {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->PriorityIssueChecklists, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->PriorityIssueChecklists, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
							} else {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->PriorityIssueChecklists, 0,25).'</div>';
							}
						} else {
							$row[] = '';
						}
					}
					else if($column->ColumnName == 'WorkflowIssueChecklists')
					{
						$WorkflowIssueChecklists = $this->getWorkflowIssueChecklists_variable($column);						

						if (!empty($myorders->{$WorkflowIssueChecklists})) {
							if (strlen($myorders->{$WorkflowIssueChecklists}) > 25) {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$WorkflowIssueChecklists}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$WorkflowIssueChecklists}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
							} else {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$WorkflowIssueChecklists}, 0,25).'</div>';
							}
						} else {
							$row[] = '';
						}
					} else if($column->ColumnName == 'OrderComments') {
						
						
						$row[] = '<span contenteditable="true" data-orderuid="'.$myorders->OrderUID.'" class="form-control comments_editable" style="padding: 5px;float: left;">'.$myorders->OrderComments.'</span><input type="hidden" name="LastUpdateComments" id="LastUpdateComments" value="'.$myorders->OrderComments.'">';

					} else if($column->ColumnName == 'OrderJuniorProcessorComments') {
						
						
						$row[] = '<span contenteditable="true" data-orderuid="'.$myorders->OrderUID.'" class="form-control comments_editable" style="padding: 5px; ">'.$myorders->OrderJuniorProcessorComments.'</span><input type="hidden" name="LastUpdated-JuniorProcessorComments" id="LastUpdated-JuniorProcessorComments" value="'.$myorders->OrderJuniorProcessorComments.'">';

					} else if (strripos($column_name, "Order-Priority") !== false) {

						$row[] = $myorders->Priority;

					} else if (strripos($column_name, "Order-Comments") !== false) {

						$row[] = '<span contenteditable="true" data-orderuid="' . $myorders->OrderUID . '" class="form-control comments_editable" style="padding: 5px; float: left;">' . $myorders->OrderComments . '</span><input type="hidden" name="LastUpdateComments" id="LastUpdateComments" value="' . $myorders->OrderComments . '">';

					} else if (strripos($column_name, "Workflow-Comments") !== false) {

						$row[] = '<span contenteditable="true" data-orderuid="' . $myorders->OrderUID . '" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" class="form-control workflowcomments_editable" style="padding: 5px; float: left;">' . $myorders->Description . '</span><input type="hidden" name="LastUpdateWorkflowComments" class="LastUpdateWorkflowComments" value="' . $myorders->Description . '">';

					} else if ($column_name == 'OrderEntryDateTime') {

						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);

					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {

						$row[] = site_dateformat($myorders->{$column_name});

					} else if ($column_name == 'ProcessorChosenClosingDate') {

						// Processor preferred closing date less than 5 days warning to be shown and can be assign highlight
						if ($this->CheckProcessorChosenClosingDate($myorders->ProcessorChosenClosingDate)) {
							$row[] = $myorders->{$column_name};
						} else {
							$row[] = '<span class="highlightProcessorChosenClosingDatecolumn" title="Processor Preferred Closing Date is Expiring.">'.$myorders->ProcessorChosenClosingDate.'</span>';
						}

					} else if (strripos($column_name, "date") !== false) {

						$row[] = site_dateformat(ltrim($myorders->{$column_name}));

					} else if ($column_name == 'LoanNumber') {

						$daysleft = $this->get_expiredchecklistalertrow($myorders,$WorkflowModuleUID);
						$row[] = isset($myorders->DueDateTime, $myorders->LoanNumber, $myorders->ExpiryOrderInDuration, $myorders->ExpiryOrders) ? $this->GetDueDateRow($myorders->DueDateTime, $myorders->LoanNumber, $myorders->ExpiryOrderInDuration, $myorders->ExpiryOrders). $daysleft : $myorders->LoanNumber;

					} else if($column_name == "LastNotesDays" && isset($myorders->{$column_name}) && isset($LastNotesDays->SettingValue) && ($myorders->{$column_name}) >= $LastNotesDays->SettingValue ) {

						$row[] = '<span class="text-danger" title="Greater than the default value">'.$myorders->{$column_name}.'</span>';

					}  else if ($column_name == 'LockExpiration') {

						// Color coding for expired, expiration of current day and one day before expiration
						// Rate Lock Expiration If weekend need to show upto next business day expiration dates
						if ($this->CheckLockExpiration($myorders->LockExpiration, ['HighlightLockExpiryOrdersColumn'=>true])) {
							$row[] = $myorders->{$column_name};
						} else {
							$row[] = '<span class="highlightlockexpirationcolumn" title="Rate Lock is Expiring.">'.$myorders->LockExpiration.'</span>';
						}

					} elseif ($column_name == 'SubQueueCategories') {
						if (isset($Mischallenous['SubQueueSection']) && !empty($Mischallenous['SubQueueSection']) && $Mischallenous['SubQueueSection'] == $column->SubQueueSection && !empty($column->SubQueueCategoryUID)) {

							$SubQueueCategoryAliasName = 'SubQueueCategories_'.$column->SubQueueSection;

							$SubQueueOptions = '<option value="">Select Category</option>';

							foreach ($SubQueueCategories as $value) {
							    if (in_array($value['CategoryUID'], explode(',', $column->CategoryUIDs)) && $column->WorkflowModuleUID == $myorders->WorkflowModuleUID) {

							    	// Category Details An Array
							    	if ($myorders->$SubQueueCategoryAliasName == $value['CategoryUID']) {
							    		
							    		$SubQueueCategory['SubQueueCategoryUID'] = $column->SubQueueCategoryUID;
							    		$SubQueueCategory['CategoryUID'] = $myorders->$SubQueueCategoryAliasName;
							    	}
							    	
							    	$selected = (in_array($value['CategoryUID'], explode(',', $myorders->$SubQueueCategoryAliasName))) ? 'selected': '';
							    	$SubQueueOptions.= '<option value="'.$value['CategoryUID'].'" '.$selected.'>'.$value['CategoryName'].'</option>';
							    }
							}

							// DocsOUt have Multi select
							$SubQueueCategoryAttr = '';
							if ($WorkflowModuleUID == $this->config->item('Workflows')['DocsOut']) {
								$SubQueueCategoryAttr = 'multiple="multiple"';
							}

							$row[] = '
							<div class="form-group bmd-form-group">
								<select class="select2picker select2table SubQueueCategory" name="SubQueueCategory" data-orderuid="'.$myorders->OrderUID.'" data-subqueuecategoryuid="'.$column->SubQueueCategoryUID.'" '.$SubQueueCategoryAttr.'>
									'.$SubQueueOptions.'
								</select>
							</div>
							';

						} else {
							continue;
						}
						
					} elseif ($column_name == 'SubQueueFirstInitiated') {

						$row[] = $myorders->{$column_name."_".$column->QueueWorkflowUID};

					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} elseif ($column_name == 'ChecklistIssueComments') {

						if (strlen($myorders->{$column_name}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'</div>';
						}

					} elseif ($column_name == 'KickBackSubQueueAging') {

						$row[] = $this->Get_DynamicSubQueueAging($myorders->ReversedDateTime, $column->SubQueueAging);

					} elseif ($column_name == 'ExpirySubQueueAging') {

						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID, $column->SubQueueAging);

					} elseif ($column_name == 'CategoryTAT') {

						$SubQueueCategory['OrderUID'] = $myorders->OrderUID;
						$SubQueueCategory['SubQueueAging'] = $column->SubQueueAging;

						$row[] = $this->CalculateCategoryTATAging($SubQueueCategory, ['UserView'=>TRUE]);
						// $row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} else if ($column_name == 'DocsOutAging') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$row[] = '<div style="width: 100%; text-align: center;">'.$this->DocsOutAging($dates, $column).'</div>';					

					} else if ($column_name == 'DocsOutAgingHours') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$hours = $this->DocsOutAging($dates, $column) * 24;
						$row[] = $hours != 0 ? '<div style="width: 100%; text-align: center;">'.$hours.'</div>':'';			

					} else if ($column->ColumnName ==  "AgingInHours") {
					
						$row[] = '<div style="width: 100%; text-align: center;">'.site_datetimeaginginhours($myorders->EntryDatetime).'</div>';
					
					} else {	

						$row[] = $myorders->{$column_name};

					}

				}

				if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
					$row[] = $myorders->KickbackByUserName;
					$row[] = site_dateformat($myorders->ReversedDateTime);
					$row[] = $myorders->ReversedRemarks;

					// KickBack SubQueue Aging Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {
						
						$row[] = site_datetimeaging($myorders->ReversedDateTime);
					}

				}

				if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

					$row[] = $myorders->RaisedBy;
					$row[] = $myorders->Remarks;
					$row[] = site_datetimeformat($myorders->Remainder);
				}

				// HOI Rework Queue
				if (isset($Mischallenous['IsHOIRework']) && $Mischallenous['IsHOIRework'] == true) {

					$row[] = $myorders->EnabledBy;
					$row[] = site_datetimeformat($myorders->EnabledDateTime);
					$row[] = $myorders->EnabledRemarks;
				}

				if (isset($Mischallenous['IsCompletedReport']) && $Mischallenous['IsCompletedReport'] == true) {

					$row[] = $myorders->IsRework;
				}
				if (isset($Mischallenous['IsCompleted']) && $Mischallenous['IsCompleted'] == true) {

					$row[] = $myorders->completedby;
					$row[] = site_datetimeformat($myorders->completeddatetime);
				}

				// Email and Phone 
				if (isset($Mischallenous['IsThreeAConfirmationQueue']) && $Mischallenous['IsThreeAConfirmationQueue'] == true) {

					$IsEmailEnabled = ($myorders->IsEmailEnabled == 1) ? 'checked' : '';

					$row[] = '<div class="form-check text-center"><label class="form-check-label " style="color: teal"><input class="form-check-input" id="IsEmailEnabled" type="checkbox" value="" name="IsEmailEnabled" '.$IsEmailEnabled.' data-orderuid="'.$myorders->OrderUID.'" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'"><span class="form-check-sign"><span class="check"></span></span></label></div>';

					$IsPhoneEnabled = ($myorders->IsPhoneEnabled == 1) ? 'checked' : '';

					$row[] = '<div class="form-check text-center"><label class="form-check-label " style="color: teal"><input class="form-check-input" id="IsPhoneEnabled" type="checkbox" value="" name="IsPhoneEnabled" '.$IsPhoneEnabled.' data-orderuid="'.$myorders->OrderUID.'" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'"><span class="form-check-sign"><span class="check"></span></span></label></div>';
				}

				if (isset($Mischallenous['IsExpiryOrdersQueue']) && $Mischallenous['IsExpiryOrdersQueue'] == TRUE) {
					
					// Expiry SubQueue Aging Column Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {

						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID);
					}
				}

				if ($Mischallenous['SubQueueSection'] == "ExpiredCompleteOrdersTable") {
					
					$row[] = site_datetimeformat($myorders->ExpirycompletedDateTime);
				}

				$badges = '';

				if ((isset($Mischallenous['JuniorProcessorPriority_Report']) && $Mischallenous['JuniorProcessorPriority_Report'] == true) || (isset($Mischallenous['Priority_Report']) && $Mischallenous['Priority_Report'])) {

					$badge= '';
					if($myorders->Workupcount > 0) {
						$badge= '<span class="badgenotification-workup">w'.$myorders->Workupcount.'</span>';
					}

					$Action = '<a href="Ordersummary/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs OrderUID" data-orderuid = "'.$myorders->OrderUID.'" target="_new">'.$badge.'<i class="icon-pencil"></i></a>';


					/*WORKUP FORCE ENABLE*/
					$is_enableworkup = $this->Common_Model->check_forcequeueenabled($myorders->OrderUID,$myorders->CustomerUID,$this->config->item('Workflows')['Workup']);

					if($is_enableworkup) 
					{
						$Action .= '&nbsp;<button data-href="Workup/index/'.$myorders->OrderUID.'" title="Enable Work-Up" class="btn btn-success btn-xs raiseworkupqueuepopup" data-workflowmoduleuid="'.$this->config->item('Workflows')['Workup'].'" data-orderuid="'.$myorders->OrderUID.'">W</button>';
					}

					// Order Reverse Enable
					if ( !in_array($myorders->StatusUID, $this->config->item('CancelledOrders_Milestones')) && !in_array($myorders->MilestoneUID, $this->config->item('Workflows_EliminatedMilestones')) && !empty($this->Common_Model->OrderReverseWorkflowStatus($myorders->OrderUID)) && (isset($this->UserPermissions->IsReverseEnabled)) && $this->UserPermissions->IsReverseEnabled == 1) {
						$Action .= '<button title="Order Reverse" type="button" class="btn btn-link btn-just-icon btn-xs btn-success btnOrderReverse" data-orderuid="'.$myorders->OrderUID.'"><i class="icon-tab pr-1"></i></button>';
					}



				}else if (isset($Mischallenous['IsCompleted']) && $Mischallenous['IsCompleted'] == true) {


					if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->IsWorkflowCompleted($myorders->OrderUID, $this->config->item('Workflows')['Workup']) && !empty($myorders->Workupcount)) {
						$badges .= '<span class="badgenotification-workup">w' . $myorders->Workupcount . '</span>';
					}

					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';
				} elseif(isset($Mischallenous['IsReworkOrders']) && $Mischallenous['IsReworkOrders'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';
				} elseif(isset($Mischallenous['IsHOIRework']) && $Mischallenous['IsHOIRework'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';
				} elseif (isset($Mischallenous['IsEsclation']) && $Mischallenous['IsEsclation'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i></a>';

					// Check if esclation is already raised or not
					if (!$myorders->EsclationRaised) {
						
						$Action .= '<button class="btn btn-danger btn-sm RaiseEsclationOrderModal EsclationOrderModal btn-RaiseEsclationOrder" data-orderuid="' . $myorders->OrderUID . '" title="Initiate Escalation">RE</button>';
					}
					
				} elseif($Mischallenous['SubQueueSection'] == "Expiredorderstable" || $Mischallenous['SubQueueSection'] == "ExpiredCompleteOrdersTable") {

					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';

					if (isset($myorders->WorkflowStatus) && $myorders->WorkflowStatus != $this->config->item('WorkflowStatus')['Completed']) {

						if (isset($this->UserPermissions->IsAssigned) && $this->UserPermissions->IsAssigned == 1) {
						
							$tOrderAssignment_row = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID' => $myorders->OrderUID, 'WorkflowModuleUID' => $myorders->WorkflowModuleUID]);

							if (empty($tOrderAssignment_row)) {
								$Action .= '<button class="btn btn-success btn-sm AssignUsers" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '" title="Assign/ReAssign User"><i class="icon-rotate-cw2" aria-hidden="true"></i></button>';
							} else {
								$Action .= '<button class="btn btn-success btn-sm ReAssignUsers" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '" title="Assign/ReAssign User"><i class="icon-rotate-cw2" aria-hidden="true"></i></button>';
							}
						}
					}

				} elseif(isset($Mischallenous['IsWorkupKickBackQueue']) && $Mischallenous['IsWorkupKickBackQueue'] == true) {
					
					if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->IsWorkflowCompleted($myorders->OrderUID, $this->config->item('Workflows')['Workup']) && !empty($myorders->Workupcount)) {
						$badges .= '<span class="badgenotification-workup">w' . $myorders->Workupcount . '</span>';
					}

					$Action = '<a target="_blank" href="Ordersummary/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';

					/*WORKUP FORCE ENABLE*/
					$is_enableworkup = $this->Common_Model->check_forcequeueenabled($myorders->OrderUID,$myorders->CustomerUID,$this->config->item('Workflows')['Workup']);

					if($is_enableworkup) {
						$Action .= '&nbsp;<button data-href="Workup/index/'.$myorders->OrderUID.'" title="Enable Work-Up" class="btn btn-success btn-xs raiseworkupqueuepopup" data-workflowmoduleuid="'.$this->config->item('Workflows')['Workup'].'" data-orderuid="'.$myorders->OrderUID.'">W</button>';
					}

				} elseif(isset($Mischallenous['IsWorkupReworkQueue']) && $Mischallenous['IsWorkupReworkQueue'] == true) {
					
					if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->IsWorkflowCompleted($myorders->OrderUID, $this->config->item('Workflows')['Workup']) && !empty($myorders->Workupcount)) {
						$badges .= '<span class="badgenotification-workup">w' . $myorders->Workupcount . '</span>';
					}

					$Action = '<a target="_blank" href="Ordersummary/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';

					/*WORKUP FORCE ENABLE*/
					$is_enableworkup = $this->Common_Model->check_forcequeueenabled($myorders->OrderUID,$myorders->CustomerUID,$this->config->item('Workflows')['Workup']);

					if($is_enableworkup) {
						$Action .= '&nbsp;<button data-href="Workup/index/'.$myorders->OrderUID.'" title="Enable Work-Up" class="btn btn-success btn-xs raiseworkupqueuepopup" data-workflowmoduleuid="'.$this->config->item('Workflows')['Workup'].'" data-orderuid="'.$myorders->OrderUID.'">W</button>';
					}

				} else {

					$Action = '';

					if ($this->loggedid == $myorders->AssignedToUserUID) {


						if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->IsWorkflowCompleted($myorders->OrderUID, $this->config->item('Workflows')['Workup']) && !empty($myorders->Workupcount)) {
							$badges .= '<span class="badgenotification-workup">w' . $myorders->Workupcount . '</span>';
						}

						$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a><a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs OrderWorkflowChecklist" data-orderuid = "' . $myorders->OrderUID . '" data-uri="'.$this->uri->segment(1).'"><i class="fa fa-eye"></i>' . $badges . '</a>';
					} else {

						if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->IsWorkflowCompleted($myorders->OrderUID, $this->config->item('Workflows')['Workup']) && !empty($myorders->Workupcount)) {
							$badges .= '<span class="buttonbadgenotification-workup">w' . $myorders->Workupcount . '</span>';
						}

						// Action button enabled workflows
						if (in_array($WorkflowModuleUID, $this->config->item('ActionBtnEnabledWorkflows'))) {

							$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';
						}

						// role level restrict self assign
						if (isset($this->UserPermissions->IsSelfAssignEnabled) && $this->UserPermissions->IsSelfAssignEnabled == 1) {
							$Action .= '<button class="btn btn-info btn-sm ' . $Mischallenous['AssignButtonClass'] . '" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '"><i class="fa fa-hand-o-up" aria-hidden="true"></i></button>' . $badges;
						}
						
					}
					if (!isset($Mischallenous['IsParkingQueue']) && !isset($Mischallenous['IsCanceled']) && !isset($Mischallenous['IsFunded'])) {
						$tOrderAssignment_row = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID' => $myorders->OrderUID, 'WorkflowModuleUID' => $myorders->WorkflowModuleUID]);
						if (isset($this->UserPermissions->IsAssigned) && $this->UserPermissions->IsAssigned == 1) {
							if (empty($tOrderAssignment_row)) {
								$Action .= '<button class="btn btn-success btn-sm AssignUsers" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '" title="Assign/ReAssign User"><i class="icon-rotate-cw2" aria-hidden="true"></i></button>';
							} else {
								$Action .= '<button class="btn btn-success btn-sm ReAssignUsers" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '" title="Assign/ReAssign User"><i class="icon-rotate-cw2" aria-hidden="true"></i></button>';
							}
						}
					}
				}



				if (isset($Mischallenous['IsFunded']) && $Mischallenous['IsFunded'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i></a>';
				} elseif (isset($Mischallenous['IsCanceled']) && $Mischallenous['IsCanceled'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i></a>';
				} elseif (isset($Mischallenous['IsPayOffOrders']) && $Mischallenous['IsPayOffOrders'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i></a>';
				} elseif (isset($Mischallenous['IsWorkflowQueueReport']) && $Mischallenous['IsWorkflowQueueReport'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i></a>';
				} elseif (isset($Mischallenous['Dashboard']) && $Mischallenous['Dashboard'] == true) {
					$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i></a>';
				}

				// Check if escalation is already raised or not
				if ($myorders->EsclationRaised == 1 && !empty($myorders->HighlightUID)) {
					
					$Action .= '<button class="btn btn-warning btn-sm ClearEsclationOrder btn-ClearEsclationOrder EsclationOrderModal" data-orderuid="' . $myorders->OrderUID . '" data-highlightuid="' . $myorders->HighlightUID . '" title="Clear Escalation">CE</button>';
					
					$Action .= '<input type="hidden" name="HighlightUID" class="HighlightEsclationOrder" val="'.$myorders->OrderUID.'">';
				}

				// Check if nbs is required
				if (isset($myorders->NBSRequired) && $myorders->NBSRequired == 1) {
					$Action .= '<input type="hidden" class="HighlightNBSOrder" value="'.$myorders->OrderUID.'">';
				}

				// highlight state based on workflows
				if(array_key_exists($WorkflowModuleUID, $this->config->item('HighlightStates_Workflows'))) {
					if(isset($this->config->item('HighlightStates_Workflows')[$WorkflowModuleUID]) && in_array($myorders->PropertyStateCode, $this->config->item('HighlightStates_Workflows')[$WorkflowModuleUID])) {
						$Action .= '<input type="hidden" class="HighlightState"  value="'.$myorders->PropertyStateCode.'" >';
					}
				}

				// Loan Info (Priority Report)
				if (isset($Mischallenous['Priority_Report']) && $Mischallenous['Priority_Report']) {
					
					$Action .= '<button title="Loan Info" type="button" class="btn btn-link btn-just-icon btn-xs btn-success btnLoanInfo" data-orderuid="'.$myorders->OrderUID.'"><i class="icon-info22" style="font-size: 15px !important;"></i></button>';
					
					// Calculator Info
					$DocsOutCalculatorShow = !empty($this->IsWorkflowCompleted($myorders->OrderUID, $this->config->item('Workflows')['DocsOut'])) ? 1 : '';
					$WorkUpCalculatorShow = !empty($this->CheckWorkUpIsCompletedStatus($myorders->OrderUID)) ? 1 : '';

					if (!empty($WorkUpCalculatorShow) || !empty($DocsOutCalculatorShow)) {
						
						$Action .= '<button title="Calculator Info" type="button" class="btn btn-link btn-just-icon btn-xs btn-success btnCalculatorInfo" data-orderuid="'.$myorders->OrderUID.'" data-workupcalculatorshow="'.$WorkUpCalculatorShow.'" data-docsoutcalculatorshow="'.$DocsOutCalculatorShow.'"><i class="icon-calculator" style="font-size: 15px !important;"></i></button>';
					}
					
				}

				if (!empty($myorders->FollowUpUID)) {
					$Action .= ' <button class="btn followupbutton btn-xs clearfollowupstaticqueuepopup HighlightFollowupOrders" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '" data-staticqueueuid="' . $myorders->StaticQueueUID . '" title="' . $this->lang->line('Clear_Followup') . '"><i class="fa fa-times" aria-hidden="true"></i> F</button>';
				}

				$row[] = $Action;
				$orderslist[] = $row;
			}


			$DynamicColumns = $this->Common_Model->dynamicColumnNames($QueueColumns, $Mischallenous['SubQueueSection']);

			$MischallenousColumns = ['tOrderWorkflows.EntryDatetime', 'tOrderWorkflows.DueDateTime'];
			$post['column_order'] = array_merge($DynamicColumns, $MischallenousColumns);
			$post['column_search'] = array_filter(array_merge($DynamicColumns, $MischallenousColumns));
			$post['orderslist'] = $orderslist;
			return $post;
		}

		return [];
	}

	function get_OrderPriority($OrderUID)
	{
		$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
		$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

		if (!empty($Customer_Prioritys)) {
			$PriorityWHERE = [];
			foreach ($Customer_Prioritys as $Priorityrow) {

				$PriorityFields = $this->Priority_Report_model->get_priorityreportfields($Priorityrow->PriorityUID);
				if (!empty($PriorityFields)) {
					$PriorityLOOPWHERE = [];
					foreach ($PriorityFields as $PriorityFieldrow) {
						if ($PriorityFieldrow->WorkflowStatus == 'Completed') {

							$PriorityLOOPWHERE[] = "(TWPR_" . $PriorityFieldrow->SystemName . ".WorkflowModuleUID = " . $PriorityFieldrow->WorkflowModuleUID . " AND TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " ) ";
						} else {

							$PriorityLOOPWHERE[] = "(TWPR_" . $PriorityFieldrow->SystemName . ".WorkflowModuleUID = " . $PriorityFieldrow->WorkflowModuleUID . " AND (TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus IS NULL OR TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus <> " . $this->config->item('WorkflowStatus')['Completed'] . ") ) ";
						}
					}

					$PriorityWHERE[$Priorityrow->PriorityUID] = "(" . implode(" AND ", $PriorityLOOPWHERE) . " )";
				}
			}

			if (!empty($PriorityWHERE)) {

				$PriorityWHERECASECONDITION = [];
				foreach ($PriorityWHERE as $PriorityWHEREkey => $PriorityWHEREvalue) {

					$PriorityWHERECASECONDITION[] = "WHEN " . $PriorityWHEREvalue . " THEN 'Priority " . $PriorityWHEREkey . "'";
				}

				$this->db->select("(CASE " . implode(" ", $PriorityWHERECASECONDITION) . "   ELSE '' END) AS Priority");
			} else {

				$this->db->select('"" AS Priority', FALSE);
			}
		} else {
			$this->db->select('"" AS Priority', FALSE);
		}

		$this->db->from('tOrders');

		foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrow) {

			$this->db->join("tOrderWorkflows AS " .  "TWPR_" . $PriorityWorkflowrow->SystemName,   "TWPR_" . $PriorityWorkflowrow->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TWPR_" . $PriorityWorkflowrow->SystemName . ".WorkflowModuleUID = '" . $PriorityWorkflowrow->WorkflowModuleUID . "'");

			$this->db->join("tOrderAssignments AS " . "TOAPR_" . $PriorityWorkflowrow->SystemName,  "TOAPR_" . $PriorityWorkflowrow->SystemName . ".OrderUID = tOrders.OrderUID AND TOAPR_" . $PriorityWorkflowrow->SystemName . ".WorkflowModuleUID = '" . $PriorityWorkflowrow->WorkflowModuleUID . "'", "LEFT");
		}

		$this->db->where('tOrders.OrderUID', $OrderUID);

		$result = $this->db->get()->row();

		return $result->Priority;
	}

	/**
	 *@description Function to getDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.5.2020 
	 * @version Dyamc Queues 
	 *
	 */
	function getExceptionDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{


		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		// Get Categories Details
		$SubQueueCategories = $this->GetSubQueueCategories();

		if (!empty($QueueColumns)) {
			$orderslist = [];
			foreach ($ArrayList as $myorders) {
				$row = array();

				// Reset Arr
				$SubQueueCategory = [];

				foreach ($QueueColumns as $key => $column) {

					if ($this->CheckQueueColumnIsEnabled($column->StaticQueueUIDs, $column->QueueUIDs, '', $myorders->QueueUID)) {
						continue;
					}

					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);

					if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {
						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column->DocumentTypeUID . $column_name});
					} else if (strripos($column_name, "StatusName") !== false || strripos($column_name, "CurrentStatus") != false) {
						$row[] = '<a  href="javascript:void(0)" style=" background: ' . $myorders->StatusColor . ' !important;padding: 5px 10px;border-radius:0px;" class="btn">' . $myorders->StatusName . '</a>';
					} else if ($column_name == 'WorkflowQueue') {
						$daysleft = $this->get_expiredchecklistalertrow($myorders,$column->QueueWorkflowUID);
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID}.$daysleft : NULL;
					} else if (strripos($column_name, "KickbackAssociate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackRemarks") !== false) {
						if (strlen($myorders->{$column_name . $column->QueueWorkflowUID}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name . $column->QueueWorkflowUID}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name . $column->QueueWorkflowUID}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name . $column->QueueWorkflowUID}, 0,25).'</div>';
						}
					} else if (strripos($column_name, "ReversedWorkflows") !== false) {

						if (strlen($myorders->{$column_name}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'</div>';
						}

					} else if (strripos($column_name, "ReversedWorkflowsComments") !== false) {

						if (strlen($myorders->{$column_name}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'</div>';
						}

					} else if (strripos($column_name, "KickbackDate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;
					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {
						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};
					} else if ($column_name == 'WorkflowCompletedDate') {
						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});
					} else if (strripos($column_name, "ScheduledDate") !== false) {
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = '<input type="text" data-orderuid="' . $myorders->OrderUID . '" id="schedule_date_' . $myorders->OrderUID . '" class="schedule_date ' . $select . '" value="' . $myorders->ScheduledDate . '">';
					} else if (strripos($column_name, "ProcessorChosenClosingDate") !== false) {
						
						if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && in_array($this->RoleType, $this->config->item('ProcssorChosenDateEditable_Roles'))) {
							
							$row[] = '<input type="text" data-orderuid="' . $myorders->OrderUID . '" id="ProcessorChosenClosingDate_' . $myorders->OrderUID . '" class="ProcessorChosenClosingDate" value="' . $myorders->ProcessorChosenClosingDate . '" readonly>';
						} else {

							$row[] = $myorders->ProcessorChosenClosingDate;
						}

					} else if ($column_name == 'ScheduledTime') {
						$row[] = '<input type="text" data-orderuid="' . $myorders->OrderUID . '" id="schedule_time_' . $myorders->OrderUID . '" class="schedule_time" value="' . $myorders->ScheduledTime . '">';
					} else if ($column_name == 'LOGIC-STC') {

						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;
					} else if ($column_name == 'LOGIC-LCREQUIRED') {
						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);
					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					} else if (strripos($column_name, "logic") !== false) {
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						if (!empty($myorders->ProblemIdentifiedChecklists)) {
							if (strlen($myorders->ProblemIdentifiedChecklists) > 25) {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">' . substr($myorders->ProblemIdentifiedChecklists, 0, 25) . '<span class="morecontent"><span style="display: none;">' . substr($myorders->ProblemIdentifiedChecklists, 25) . '</span>&nbsp;&nbsp;<a href="" class="morelinktoggle">...</a></span></div>';
							} else {
								$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">' . substr($myorders->ProblemIdentifiedChecklists, 0, 25) . '</div>';
							}
						} else {
							$row[] = '';
						}
					} else if (strripos($column_name, "Order-Priority") !== false) {
						$row[] = $myorders->Priority;
					} else if (strripos($column_name, "Order-Comments") !== false) {
						$row[] = '<span contenteditable="true" data-orderuid="' . $myorders->OrderUID . '" class="form-control comments_editable" style="padding: 5px; float: left;">' . $myorders->OrderComments . '</span><input type="hidden" name="LastUpdateComments" id="LastUpdateComments" value="' . $myorders->OrderComments . '">';
					} else if (strripos($column_name, "Workflow-Comments") !== false) {
						$row[] = '<span contenteditable="true" data-orderuid="' . $myorders->OrderUID . '" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" class="form-control workflowcomments_editable" style="padding: 5px; float: left;">' . $myorders->Description . '</span><input type="hidden" name="LastUpdateWorkflowComments" class="LastUpdateWorkflowComments" value="' . $myorders->Description . '">';
					} else if ($column_name == 'OrderEntryDateTime') {
						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);
					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {
						$row[] = site_dateformat($myorders->{$column_name});
					} else if ($column_name == 'ProcessorChosenClosingDate') {
						// Processor preferred closing date less than 5 days warning to be shown and can be assign highlight
						if ($this->CheckProcessorChosenClosingDate($myorders->ProcessorChosenClosingDate)) {
							$row[] = $myorders->{$column_name};
						} else {
							$row[] = '<span class="highlightProcessorChosenClosingDatecolumn" title="Processor Preferred Closing Date is Expiring.">'.$myorders->ProcessorChosenClosingDate.'</span>';
						}
					} else if (strripos($column_name, "date") !== false) {
						$row[] = site_dateformat(ltrim($myorders->{$column_name}));
					} else if ($column_name == 'LoanNumber') {
						$daysleft = $this->get_expiredchecklistalertrow($myorders,$WorkflowModuleUID);

						$row[] = $this->GetDueDateRow($myorders->DueDateTime, $myorders->LoanNumber, $myorders->ExpiryOrderInDuration, $myorders->ExpiryOrders).$daysleft;


					} else if ($column_name == 'LockExpiration') {
						// Color coding for expired, expiration of current day and one day before expiration
						// Rate Lock Expiration If weekend need to show upto next business day expiration dates
						if ($this->CheckLockExpiration($myorders->LockExpiration, ['HighlightLockExpiryOrdersColumn'=>true])) {
							$row[] = $myorders->{$column_name};
						} else {
							$row[] = '<span class="highlightlockexpirationcolumn" title="Rate Lock is Expiring.">'.$myorders->LockExpiration.'</span>';
						}
					} elseif ($column_name == 'SubQueueCategories') {

						if (!empty($column->SubQueueCategoryUID) && $myorders->QueueUID == $column->SubQueueUID) {

							$SubQueueCategoryAliasName = 'SubQueueCategories_'.$column->SubQueueUID;

							$SubQueueOptions = '<option value="">Select Category</option>';
							foreach ($SubQueueCategories as $value) {
							    if (in_array($value['CategoryUID'], explode(',', $column->CategoryUIDs)) && $column->WorkflowModuleUID == $myorders->WorkflowModuleUID) {
							    	
							    	$selected = (in_array($value['CategoryUID'], explode(',', $myorders->$SubQueueCategoryAliasName))) ? 'selected': '';

							    	// Category Details An Array
							    	if ($myorders->$SubQueueCategoryAliasName == $value['CategoryUID']) {
							    		
							    		$SubQueueCategory['SubQueueCategoryUID'] = $column->SubQueueCategoryUID;
							    		$SubQueueCategory['CategoryUID'] = $myorders->$SubQueueCategoryAliasName;
							    	}							    	

							    	$SubQueueOptions.= '<option value="'.$value['CategoryUID'].'" '.$selected.'>'.$value['CategoryName'].'</option>';
							    }
							}

							// DocsOUt have Multi select
							$SubQueueCategoryAttr = '';
							if ($WorkflowModuleUID == $this->config->item('Workflows')['DocsOut']) {
								$SubQueueCategoryAttr = 'multiple="multiple"';
							}

							$row[] = '
							<div class="form-group bmd-form-group">
								<select class="select2picker select2table SubQueueCategory" name="SubQueueCategory" data-orderuid="'.$myorders->OrderUID.'" data-subqueuecategoryuid="'.$column->SubQueueCategoryUID.'" '.$SubQueueCategoryAttr.'>
									'.$SubQueueOptions.'
								</select>
							</div>
							';

						} else {
							continue;
						}
						
					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} elseif ($column_name == 'ChecklistIssueComments') {

						if (strlen($myorders->{$column_name}) > 25) {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'<span class="morecontent"><span style="display: none;">'.substr($myorders->{$column_name}, 25).'</span>&nbsp;&nbsp;<a href="javascript:;" class="morelinktoggle">...</a></span></div>';
						} else {
							$row[] = '<div class="js-description_readmore" style="width: 100px; white-space: pre-wrap;">'.substr($myorders->{$column_name}, 0,25).'</div>';
						}

					} elseif ($column_name == 'ExceptionSubQueueAging') {

						$row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} elseif ($column_name == 'CategoryTAT') {

						$SubQueueCategory['OrderUID'] = $myorders->OrderUID;
						$SubQueueCategory['SubQueueAging'] = $column->SubQueueAging;

						$row[] = $this->CalculateCategoryTATAging($SubQueueCategory, ['UserView'=>TRUE]);
						// $row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} else if ($column_name == 'DocsOutAging') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$row[] = '<div style="width: 100%; text-align: center;">'.$this->DocsOutAging($dates, $column).'</div>';		

					} else if ($column_name == 'DocsOutAgingHours') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$hours = $this->DocsOutAging($dates, $column) * 24;
						$row[] = $hours != 0 ? '<div style="width: 100%; text-align: center;">'.$hours.'</div>':'';		

					} else {
						$row[] = $myorders->{$column_name};
					}
				}

				//SubQueue IsReceived 
				if (isset($myorders->QueueUID) && !empty($myorders->IsDocsReceived)) {
					$isdocreceivedchecked = (isset($myorders->QueueIsDocsReceived) && $myorders->QueueIsDocsReceived == 1 ? 'checked' : '');
					$row[] = '<div class="form-check"><label class="form-check-label td-checkbox" style="color: teal"><input class="form-check-input QueueIsDocsReceived" type="checkbox" name="QueueIsDocsReceived"  data-queueuid="' . $myorders->QueueUID . '" data-orderuid="' . $myorders->OrderUID . '" ' . $isdocreceivedchecked . '> <span class="form-check-sign"><span class="check"></span></span></label></div>';
				}

				if (isset($myorders->QueueUID) && !empty($myorders->IsStatus)) {
					$row[] = '<div class="form-group bmd-form-group"><select class="select2picker select2table QueueIsStatus" name="QueueIsStatus"  data-queueuid="' . $myorders->QueueUID . '" data-orderuid="' . $myorders->OrderUID . '"><option value="">Select Status</option><option value="Requested" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "Requested" ? 'selected' : '').'>Requested</option><option value="Approved" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "Approved" ? 'selected' : '').'>Approved</option><option value="Denied" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "Denied" ? 'selected' : '').'>Denied</option><option value="Sales Restructure" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "Sales Restructure" ? 'selected' : '').'>Sales Restructure</option><option value="Not Required" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "Not Required" ? 'selected' : '').'>Not Required</option><option value="NTB fail" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "NTB fail" ? 'selected' : '').'>NTB fail</option><option value="CREO issue" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "CREO issue" ? 'selected' : '').'>CREO issue</option><option value="open exceptions" '.(isset($myorders->QueueIsStatus) && $myorders->QueueIsStatus == "open exceptions" ? 'selected' : '').'>open exceptions</option> </select></div> ';
				}

				// $row[] = !empty($myorders->RaisedDateTime) ? site_datetimeaging($myorders->RaisedDateTime) : NULL;
				$row[] = $myorders->RaisedBy;
				$row[] = $myorders->ReasonName;
				$row[] = site_datetimeformat($myorders->RaisedDateTime);



				$badges = '';

				if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !empty($myorders->Workupcount)) {
					$badges .= '<span class="badgenotification-workup">w' . $myorders->Workupcount . '</span>';
				}

				$Action = '<a target="_blank" href="' . $Mischallenous['PageBaseLink'] . $myorders->OrderUID . '" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "' . $myorders->OrderUID . '"><i class="icon-pencil"></i>' . $badges . '</a>';

				if (!empty($myorders->FollowUpUID)) {
					$Action .= ' <button class="btn followupbutton btn-xs clearfollowupqueuepopup" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '" data-queueuid="' . $myorders->QueueUID . '" title="' . $this->lang->line('Clear_Followup') . '"><i class="fa fa-times" aria-hidden="true"></i> F</button>';
				} else if ($myorders->IsFollowup == 1) {
					//$Action .= ' <button class="btn followupbutton btn-success btn-xs followupqueuepopup" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-followupdurationdate="'. date('m/d/Y g:i A', strtotime(' + '.$myorders->FollowupDuration.' hours')).'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"  data-queueuid="'.$myorders->QueueUID.'" title="'.$this->lang->line('Initiate_Followup').'"><i class="fa fa-clock-o" aria-hidden="true"></i> F</button>';

				}

				/*WORKUP FORCE ENABLE*/
				/*	$is_enableworkup = $this->Common_Model->check_forcequeueenabled($myorders->OrderUID,$myorders->CustomerUID,$this->config->item('Workflows')['Workup']);

				if($is_enableworkup) {
					$Action .= '&nbsp;<button data-href="Workup/index/'.$myorders->OrderUID.'" title="Enable Work-Up" class="btn btn-sm btn-tumblr forceenable_workflow" data-workflowmoduleuid="'.$this->config->item('Workflows')['Workup'].'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'">Workup</button>';
				}*/

				$tOrderAssignment_row = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID' => $myorders->OrderUID, 'WorkflowModuleUID' => $myorders->WorkflowModuleUID]);

				if (isset($this->UserPermissions->IsAssigned) && $this->UserPermissions->IsAssigned == 1) {

					$Action .= '<button class="btn btn-success btn-sm ReAssignUsers" data-workflowmoduleuid="' . $myorders->WorkflowModuleUID . '" data-orderuid="' . $myorders->OrderUID . '" data-projectuid="' . $myorders->ProjectUID . '" title="Assign/ReAssign User"><i class="icon-rotate-cw2" aria-hidden="true"></i></button>';

				}

				// Check if esclation is already raised or not
				if ($myorders->EsclationRaised == 1 && !empty($myorders->HighlightUID)) {
					
					$Action .= '<button class="btn btn-warning btn-sm ClearEsclationOrder btn-ClearEsclationOrder EsclationOrderModal" data-orderuid="' . $myorders->OrderUID . '" data-highlightuid="' . $myorders->HighlightUID . '" title="Clear Esclation">CE</button>';
					
					$Action .= '<input type="hidden" name="HighlightUID" class="HighlightEsclationOrder" val="'.$myorders->OrderUID.'">';
				}

				// Check if nbs is required
				if (isset($myorders->NBSRequired) && $myorders->NBSRequired == 1) {
					$Action .= '<input type="hidden" class="HighlightNBSOrder" value="'.$myorders->OrderUID.'">';
				}

				// highlight state based on workflows
				if(array_key_exists($WorkflowModuleUID, $this->config->item('HighlightStates_Workflows'))) {
					if(isset($this->config->item('HighlightStates_Workflows')[$WorkflowModuleUID]) && in_array($myorders->PropertyStateCode, $this->config->item('HighlightStates_Workflows')[$WorkflowModuleUID])) {
						$Action .= '<input type="hidden" class="HighlightState"  value="'.$myorders->PropertyStateCode.'" >';
					}
				}

				$row[] = $Action;
				$orderslist[] = $row;
			}


			$DynamicColumns = $this->Common_Model->dynamicColumnNames($QueueColumns, '', $myorders->QueueUID);

			$MischallenousColumns = ['tOrderWorkflows.EntryDatetime', 'tOrderWorkflows.DueDateTime'];
			$post['column_order'] = array_merge($DynamicColumns, $MischallenousColumns);
			$post['column_search'] = array_filter(array_merge($DynamicColumns, $MischallenousColumns));
			$post['orderslist'] = $orderslist;

			return $post;
		}

		return [];
	}


	/**
	 *@description Function to getExcelDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.5.2020 
	 * @version Dyamc Queues 
	 *
	 */
	function getExcelDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{
		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		if (!empty($QueueColumns)) {
			$orderslist = [];
			foreach ($ArrayList as $myorders) {
				$row = array();

				foreach ($QueueColumns as $key => $column) {
					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);
					if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {

						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column_name});
					} else if ($column_name == 'WorkflowQueue') {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackAssociate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackRemarks") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackDate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;
					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {
						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};
					} else if ($column_name == 'WorkflowCompletedDate') {
						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});
					}else if (strripos($column_name, "ScheduledDate") !== false) {
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = $myorders->ScheduledDate;
					} else if ($column_name == 'ScheduledTime') {
						$row[] = $myorders->ScheduledTime;
					} else if ($column_name == 'LOGIC-STC') {

						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;
					} else if ($column_name == 'LOGIC-LCREQUIRED') {
						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);
					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					} else if (strripos($column_name, "logic") !== false) {
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						$row[] = $myorders->ProblemIdentifiedChecklists;
					} else if (strripos($column_name, "Order-Priority") !== false) {
						$row[] = $myorders->Priority;
					} else if (strripos($column_name, "Order-Comments") !== false) {
						$row[] = $myorders->OrderComments;
					} else if (strripos($column_name, "Workflow-Comments") !== false) {
						$row[] = $myorders->Description;
					} else if ($column_name == 'OrderEntryDateTime') {
						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);
					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {
						$row[] = site_dateformat($myorders->{$column_name});
					} else if (strripos($column_name, "date") !== false) {
						$row[] = site_dateformat(ltrim($myorders->{$column_name}));
					} elseif ($column_name == 'SubQueueFirstInitiated') {

						$row[] = $myorders->{$column_name."_".$column->QueueWorkflowUID};

					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} else {
						$row[] = $myorders->{$column_name};
					}
				}

				if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
					$row[] = $myorders->KickbackByUserName;
					$row[] = site_dateformat($myorders->ReversedDateTime);
					$row[] = $myorders->ReversedRemarks;

					// KickBack SubQueue Aging Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

						$row[] = site_datetimeaging($myorders->ReversedDateTime);
					}
				}

				if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

					$row[] = $myorders->RaisedBy;
					$row[] = $myorders->Remarks;
					$row[] = site_datetimeformat($myorders->Remainder);
				}


				if (isset($Mischallenous['IsCompleted']) && $Mischallenous['IsCompleted'] == true) {

					$row[] = $myorders->completedby;
					$row[] = site_datetimeformat($myorders->completeddatetime);
				}

				// Email and Phone 
				if (isset($Mischallenous['IsThreeAConfirmationQueue']) && $Mischallenous['IsThreeAConfirmationQueue'] == true) {

					$row[] = ($myorders->IsEmailEnabled == 1) ? 'YES' : '';
					$row[] = ($myorders->IsPhoneEnabled == 1) ? 'YES' : '';
				}

				// HOI Rework Queue
				if (isset($Mischallenous['IsHOIRework']) && $Mischallenous['IsHOIRework'] == true) {

					$row[] = $myorders->EnabledBy;
					$row[] = site_datetimeformat($myorders->EnabledDateTime);
					$row[] = $myorders->EnabledRemarks;
				}

				if (isset($Mischallenous['IsExpiryOrdersQueue']) && $Mischallenous['IsExpiryOrdersQueue'] == TRUE) {
					
					// Expiry SubQueue Aging Column Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {
						
						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID);
					}
				}

				$orderslist[] = $row;
			}


			$DynamicHeaders = $this->Common_Model->dynamicColumnHeaderNames($QueueColumns);

			if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
				$DynamicHeaders[] = 'Kickback Associate';
				$DynamicHeaders[] = 'Kickback Date';
				$DynamicHeaders[] = 'Remarks';

				// KickBack SubQueue Aging Enabled Workflows
				if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

					$DynamicHeaders[] = 'KickBack SubQueue Aging';
				}
			}

			if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

				$DynamicHeaders[] = 'Parking RaisedBy';
				$DynamicHeaders[] = 'Parking Remarks';
				$DynamicHeaders[] = 'Parking ReminderOn';
			}


			if (isset($Mischallenous['IsCompleted']) && $Mischallenous['IsCompleted'] == true) {

				$DynamicHeaders[] = 'Completed By';
				$DynamicHeaders[] = 'Completed Date and Time';
			}

			if (isset($Mischallenous['IsHOIRework']) && $Mischallenous['IsHOIRework'] == true) {

				$DynamicHeaders[] = 'Raised By';
				$DynamicHeaders[] = 'Raised Date and Time';
				$DynamicHeaders[] = 'Raised Remarks';
			}

			
			if (isset($Mischallenous['IsThreeAConfirmationQueue']) && $Mischallenous['IsThreeAConfirmationQueue'] == true) {

				$DynamicHeaders[] = 'Email';
				$DynamicHeaders[] = 'Phone';
			}

			if (isset($Mischallenous['IsExpiryOrdersQueue']) && $Mischallenous['IsExpiryOrdersQueue'] == TRUE) {

				// Expiry SubQueue Aging Column Enabled Workflows
				if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {

					$DynamicHeaders[] = 'Expiry SubQueue Aging';
				}
			}

			$MischallenousColumns = [];
			$DynamicHeaders = array_merge($DynamicHeaders, $MischallenousColumns);

			$post['header'] = $DynamicHeaders;
			$post['orderslist'] = array_merge([$DynamicHeaders], $orderslist);

			return $post;
		}

		return [];
	}

	/**
	 *@description Function to getExceptionExcelDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.5.2020 
	 * @version Dyamc Queues 
	 *
	 */
	function getExceptionExcelDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{
		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		if (!empty($QueueColumns)) {
			$orderslist = [];
			foreach ($ArrayList as $myorders) {
				$row = array();

				foreach ($QueueColumns as $key => $column) {
					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);
					if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {

						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column->DocumentTypeUID . $column_name});
					} else if ($column_name == 'WorkflowQueue') {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackAssociate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackRemarks") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackDate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;
					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {
						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};
					} else if ($column_name == '  ') {
						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});
					} else if (strripos($column_name, "ScheduledDate") !== false) {
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = $myorders->ScheduledDate;
					} else if ($column_name == 'ScheduledTime') {
						$row[] = $myorders->ScheduledTime;
					} else if ($column_name == 'LOGIC-STC') {

						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;
					} else if ($column_name == 'LOGIC-LCREQUIRED') {
						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);
					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					} else if (strripos($column_name, "logic") !== false) {
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						$row[] = $myorders->ProblemIdentifiedChecklists;
					} else if (strripos($column_name, "Order-Priority") !== false) {
						$row[] = $myorders->Priority;
					} else if (strripos($column_name, "Order-Comments") !== false) {
						$row[] = $myorders->OrderComments;
					} else if (strripos($column_name, "Workflow-Comments") !== false) {
						$row[] = $myorders->Description;
					} else if ($column_name == 'OrderEntryDateTime') {
						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);
					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {
						$row[] = site_dateformat($myorders->{$column_name});
					} else if (strripos($column_name, "date") !== false) {
						$row[] = site_dateformat(ltrim($myorders->{$column_name}));
					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} else {
						$row[] = $myorders->{$column_name};
					}
				}


				// $row[] = !empty($myorders->RaisedDateTime) ? site_datetimeaging($myorders->RaisedDateTime) : NULL;
				$row[] = $myorders->RaisedBy;
				$row[] = $myorders->ReasonName;
				$row[] = site_datetimeformat($myorders->RaisedDateTime);

				$orderslist[] = $row;
			}


			$DynamicHeaders = $this->Common_Model->dynamicColumnHeaderNames($QueueColumns);

			/* ******* Exception Queue fields ***** */
			// $DynamicHeaders[] = 'SubQueue Aging';
			$DynamicHeaders[] = 'Initiated By';
			
			if ($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) {
				
				$DynamicHeaders[] = 'Kickback';

			} else {

				$DynamicHeaders[] = 'Reason';

			}
			
			$DynamicHeaders[] = 'Initiated DateTime';



			$MischallenousColumns = [];
			$DynamicHeaders = array_merge($DynamicHeaders, $MischallenousColumns);

			$post['header'] = $DynamicHeaders;
			$post['orderslist'] = array_merge([$DynamicHeaders], $orderslist);

			return $post;
		}

		return [];
	}

	// Completed Orders Based On Workflow
	function CompletedOrdersBasedOnWorkflow($post, $global = '')
	{
		$this->Common_Model->DynamicColumnsCommonQuery($post['WorkflowModuleUID'], TRUE);

		if ($post['WorkflowModuleUID'] == $this->config->item('Workflows')['Workup']) {
			$this->db->select('(SELECT COUNT(*) FROM tOrderAssignmentsHistory WHERE tOrderAssignmentsHistory.OrderUID = tOrders.OrderUID AND WorkflowModuleUID = ' . $this->config->item('Workflows')['Workup'] . ') AS Workupcount', false);
		}
		$this->db->select('tOrderWorkflows.WorkflowModuleUID');
		$this->db->select('tOrderImport.*');
		$this->db->select('tOrderPropertyRole.*');
		$this->db->select("tOrders.*,  mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('Completed.UserName as completedby, tOrderAssignments.CompleteDateTime as completeddatetime, tOrderWorkflows.EntryDatetime, mUsers.UserName AS AssignedUserName');

		/*^^^^^ Get Completed Orders Query ^^^^^*/
		$this->Common_Model->GetCompletedQueueOrdersByWorkflow($post);
		$this->Common_Model->FilterByProjectUser($this->RoleUID, $this->loggedid);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($post['WorkflowModuleUID'],'COMPLETEDCOUNT');

		// Advanced Search 
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}

		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


		if ($post['length'] != '') {
			$this->db->limit($post['length'], $post['start']);
		}


		$this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();

		return $output->result();
	}

	// MyOrders
	function completedordersBasedOnWorkflow_count_all($post = false)
	{
		if ($post == FALSE) {
			//Get WorkflowModuleUID
			$controller = $this->uri->segment(1);
			if (isset($this->config->item('workflowcontroller')[$controller])) {
				$WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
				$post['WorkflowModuleUID'] = $WorkflowModuleUID;
			} else {
				return;
			}
		}

		$this->db->select("1");


		/*^^^^^ Get Completed Orders Query ^^^^^*/
		$this->Common_Model->GetCompletedQueueOrdersByWorkflow($post);
		$this->Common_Model->FilterByProjectUser($this->RoleUID, $this->loggedid);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($post['WorkflowModuleUID'],'COMPLETEDCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->Common_Model->advanced_search($post);
		}
		/*if ($post['WorkflowModuleUID'] == 41) {
			$MileStoneUIDs = array('6', '7', '8');
			$this->db->where_in('tOrders.MilestoneUID', $MileStoneUIDs);
		}*/

		$query = $this->db->count_all_results();
		return $query;
	}

	function completedordersBasedOnWorkflow_count_filtered($post)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->Common_Model->DynamicColumnsCommonQuery($post['WorkflowModuleUID'], TRUE);
		}

		$this->db->select("1");

		/*^^^^^ Get Completed Orders Query ^^^^^*/
		$this->Common_Model->GetCompletedQueueOrdersByWorkflow($post);
		$this->Common_Model->FilterByProjectUser($this->RoleUID, $this->loggedid);

		//Order Queue Permission
		$this->Common_Model->OrdersPermission($post['WorkflowModuleUID'],'COMPLETEDCOUNT');

		// Advanced Search 
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}

		// Datatable Search
		$this->Common_Model->WorkflowQueues_Datatable_Search($post);

		// Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

		/*if ($post['WorkflowModuleUID'] == 41) {
			$MileStoneUIDs = array('6', '7', '8');
			$this->db->where_in('tOrders.MilestoneUID', $MileStoneUIDs);
		}*/

		$query = $this->db->get();
		return $query->num_rows();
	}

	function GetCompletedOrdersBasedOnWorkflowExcelRecords($post)
	{
		$this->Common_Model->DynamicColumnsCommonQuery($post['WorkflowModuleUID']);
		$this->db->select('tOrderWorkflows.WorkflowModuleUID');
		$this->db->select('tOrderImport.*');
		$this->db->select('tOrderPropertyRole.*');
		$this->db->select("tOrders.*,  mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('mUsers.UserName as completedby, tOrderAssignments.CompleteDateTime as completeddatetime, tOrderWorkflows.EntryDatetime, mUsers.UserName AS AssignedUserName');

		/*^^^^^ Get Completed Orders Query ^^^^^*/
		$this->Common_Model->GetCompletedQueueOrdersByWorkflow($post);
		$this->Common_Model->FilterByProjectUser($this->RoleUID, $this->loggedid);

		// Advanced Search 
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->Common_Model->advanced_search($post);
		}
		//Order Queue Permission
		$this->Common_Model->OrdersPermission($post['WorkflowModuleUID'],'COMPLETEDCOUNT');

		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}
	/**
	 *@description Function to getExcelDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Santhiya <santhiya.m@avanzegroup.com>
	 * @return Array 
	 * @since 15.8.2020 
	 * @version Dynamic key value  
	 *
	 */
	function getSelectiveExcelDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{
		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		// Get Categories Details
		$SubQueueCategories = $this->GetSubQueueCategories();

		if (!empty($QueueColumns)) {
			$orderslist = [];
			foreach ($ArrayList as $myorders) {
				$row = array();

				// Reset Arr
				$SubQueueCategory = [];

				foreach ($QueueColumns as $key => $column) {
					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);
					if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {

						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column->DocumentTypeUID . $column_name});
					} else if ($column_name == 'WorkflowQueue') {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackAssociate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackRemarks") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackDate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;
					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {
						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};
					} else if ($column_name == 'WorkflowCompletedDate') {
						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});
					} else if (strripos($column_name, "ScheduledDate") !== false) {
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = $myorders->ScheduledDate;
					} else if ($column_name == 'ScheduledTime') {
						$row[] = $myorders->ScheduledTime;
					} else if ($column_name == 'LOGIC-STC') {

						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;
					} else if ($column_name == 'LOGIC-LCREQUIRED') {
						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);
					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					} else if (strripos($column_name, "logic") !== false) {
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						$row[] = $myorders->ProblemIdentifiedChecklists;
					} else if (strripos($column_name, "Order-Priority") !== false) {
						$row[] = $myorders->Priority;
					} else if (strripos($column_name, "Order-Comments") !== false) {
						$row[] = $myorders->OrderComments;
					} else if (strripos($column_name, "Workflow-Comments") !== false) {
						$row[] = $myorders->Description;
					} else if ($column_name == 'OrderEntryDateTime') {
						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);
					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {
						$row[] = site_dateformat($myorders->{$column_name});
					} else if (strripos($column_name, "date") !== false) {
						$row[] = site_dateformat(ltrim($myorders->{$column_name}));
					} elseif ($column_name == 'SubQueueCategories') {

						$SubQueueCategoryTemp = [];

						if (isset($Mischallenous['SubQueueSection']) && !empty($Mischallenous['SubQueueSection']) && $Mischallenous['SubQueueSection'] == $column->SubQueueSection && !empty($column->SubQueueCategoryUID)) 
						{
							$SubQueueCategoryAliasName = 'SubQueueCategories_'.$column->SubQueueSection;

							foreach ($SubQueueCategories as $value) {

							    if (in_array($value['CategoryUID'], explode(',', $myorders->$SubQueueCategoryAliasName)) && in_array($value['CategoryUID'], explode(',', $column->CategoryUIDs)) && $column->WorkflowModuleUID == $myorders->WorkflowModuleUID) {							    	
							    		
						    		$SubQueueCategory['SubQueueCategoryUID'] = $column->SubQueueCategoryUID;
						    		$SubQueueCategory['CategoryUID'] = $myorders->$SubQueueCategoryAliasName;
							    	
							    	$SubQueueCategoryTemp[] = $value['CategoryName'];
							    }
							}

						}

						$row[] = implode(', ', $SubQueueCategoryTemp);
						
					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} elseif ($column_name == 'KickBackSubQueueAging') {

						$row[] = $this->Get_DynamicSubQueueAging($myorders->ReversedDateTime, $column->SubQueueAging);

					} elseif ($column_name == 'ExpirySubQueueAging') {
						
						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID, $column->SubQueueAging);

					} elseif ($column_name == 'CategoryTAT') {

						$SubQueueCategory['OrderUID'] = $myorders->OrderUID;
						$SubQueueCategory['SubQueueAging'] = $column->SubQueueAging;

						$row[] = $this->CalculateCategoryTATAging($SubQueueCategory);
						// $row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} else if ($column_name == 'DocsOutAging') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$row[] = $this->DocsOutAging($dates, $column);						

					} else if ($column->ColumnName ==  "AgingInHours") {
					
						$row[] = site_datetimeaginginhours($myorders->EntryDatetime);
					
					} else if ($column_name == 'DocsOutAgingHours') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$hours = $this->DocsOutAging($dates, $column) * 24;
						$row[] = $hours != 0 ? $hours:'';

					} else {
						$row[] = $myorders->{$column_name};
					}
				}
				if (isset($Mischallenous['SubQueueDetails']) && $Mischallenous['SubQueueDetails']->IsStatus == '1') {
					$row[] = $myorders->QueueIsStatus;
				}else{
					$row[] = '';
				}
				if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
					$row[] = $myorders->KickbackByUserName;
					$row[] = site_dateformat($myorders->ReversedDateTime);
					$row[] = $myorders->ReversedRemarks;

					// KickBack SubQueue Aging Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

						$row[] = site_datetimeaging($myorders->ReversedDateTime);
					}
				}else {
					$row[] = '';
					$row[] = '';
					$row[] = '';

					// KickBack SubQueue Aging Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

						$row[] = '';
					}
				}

				if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

					$row[] = $myorders->RaisedBy;
					$row[] = $myorders->Remarks;
					$row[] = site_datetimeformat($myorders->Remainder);
				}else {
					$row[] = '';
					$row[] = '';
					$row[] = '';

				}

				// $row[] = !empty($myorders->RaisedDateTime) ? site_datetimeaging($myorders->RaisedDateTime) : NULL; //initiated by datetime aging
				$row[] = isset($myorders->RaisedBy) ? $myorders->RaisedBy : ''; //initiated by
				$row[] = isset($myorders->ReasonName) ? $myorders->ReasonName : ''; //initiated reason 
				$row[] = isset($myorders->RaisedDateTime) ? $myorders->RaisedDateTime : ''; //initiated date time
				if (isset($Mischallenous['IsCompleted']) && $Mischallenous['IsCompleted'] == true) {

					$row[] = $myorders->completedby;
					$row[] = site_datetimeformat($myorders->completeddatetime);
				}else {
					$row[] = '';
					$row[] = '';
				}

				// Email and Phone 
				if (isset($Mischallenous['IsThreeAConfirmationQueue']) && $Mischallenous['IsThreeAConfirmationQueue'] == true) {

					$row[] = ($myorders->IsEmailEnabled == 1) ? 'YES' : '';
					$row[] = ($myorders->IsPhoneEnabled == 1) ? 'YES' : '';
				} else {
					$row[] = '';
					$row[] = '';
				}

				// HOI Rework Queue
				if (isset($Mischallenous['IsHOIRework']) && $Mischallenous['IsHOIRework'] == true) {

					$row[] = $myorders->EnabledBy;
					$row[] = site_datetimeformat($myorders->EnabledDateTime);
					$row[] = $myorders->EnabledRemarks;
				} else {
					$row[] = '';
					$row[] = '';
					$row[] = '';
				}

				if (isset($Mischallenous['IsExpiryOrdersQueue']) && $Mischallenous['IsExpiryOrdersQueue'] == TRUE) {
					
					// Expiry SubQueue Aging Column Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {
						
						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID);
					}
				} else {

					if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {
						
						$row[] = '';
					}
				}

				// Expiry Complete Orders
				if ($Mischallenous['SubQueueSection'] == "ExpiredCompleteOrdersTable") {
					
					$row[] = site_datetimeformat($myorders->ExpirycompletedDateTime);
				} else {

					$row[] = '';
				}

				$row[] = $Mischallenous['SubQueueName'];
				$orderslist[] = $row;
			}


			$DynamicHeaders = $this->Common_Model->dynamicColumnHeaderNames_selectiveexcelexport($QueueColumns, $Mischallenous);
				$DynamicHeaders[] = 'Status';
				
				$DynamicHeaders[] = 'Kickback Associate';
				$DynamicHeaders[] = 'Kickback Date';
				$DynamicHeaders[] = 'Remarks';

				// KickBack SubQueue Aging Enabled Workflows
				if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

					$DynamicHeaders[] = 'KickBack SubQueue Aging';
				}
			
				$DynamicHeaders[] = 'Parking RaisedBy';
				$DynamicHeaders[] = 'Parking Remarks';
				$DynamicHeaders[] = 'Parking ReminderOn';
			
				// $DynamicHeaders[] = 'SubQueue Aging';
				$DynamicHeaders[] = 'Initiated By';

				if ($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) {

					$DynamicHeaders[] = 'Kickback';

				} else {

					$DynamicHeaders[] = 'Reason';

				}

				$DynamicHeaders[] = 'Raised DateTime';
				$DynamicHeaders[] = 'Completed By';
				$DynamicHeaders[] = 'Completed Date and Time';

				$DynamicHeaders[] = 'Email';
				$DynamicHeaders[] = 'Phone';

				$DynamicHeaders[] = 'Raised By';
				$DynamicHeaders[] = 'Raised Date and Time';
				$DynamicHeaders[] = 'Raised Remarks';

				// Expiry SubQueue Aging Column Enabled Workflows
				if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {

					$DynamicHeaders[] = 'Expiry SubQueue Aging';
				}

				// Expiry Complete Orders
				$DynamicHeaders[] = 'Expiry completed DateTime';
			
				$DynamicHeaders[] = 'Sub Queue';
			$MischallenousColumns = [];
			$DynamicHeaders = array_merge($DynamicHeaders, $MischallenousColumns);

			$post['header'] = $DynamicHeaders;
			$post['orderslist'] = $orderslist;

			return $post;
		}

		return [];
	}

	function dynamicColumnHeaderNames_selectiveexcelexport($QueueColumns)
	{
		$DynamicColumns = [];
		foreach ($QueueColumns as $key => $value) {
			$DynamicColumns[] = $value->HeaderName;
		}

		return $DynamicColumns;
	}

	/**
	 *@description Function to getExcelDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.5.2020 
	 * @version Dyamc Queues 
	 *
	 */
	function getGlobalExcelDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{
		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		// Get Categories Details
		$SubQueueCategories = $this->GetSubQueueCategories();

		if (!empty($QueueColumns)) {

			$orderslist = [];

			foreach ($ArrayList as $myorders) {
				$row = array();

				// Reset Arr
				$SubQueueCategory = [];

				foreach ($QueueColumns as $key => $column) {

					if ($this->CheckQueueColumnIsEnabled($column->StaticQueueUIDs, $column->QueueUIDs, $Mischallenous['SubQueueSection'])) {
						continue;
					}

					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);
					if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {

						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column->DocumentTypeUID . $column_name});
					} else if ($column_name == 'WorkflowQueue') {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackAssociate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackRemarks") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackDate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;
					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {
						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};
					} else if ($column_name == 'WorkflowCompletedDate') {
						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});
					} else if (strripos($column_name, "ScheduledDate") !== false) {
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = $myorders->ScheduledDate;
					} else if ($column_name == 'ScheduledTime') {
						$row[] = $myorders->ScheduledTime;
					} else if ($column_name == 'LOGIC-STC') {

						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;
					} else if ($column_name == 'LOGIC-LCREQUIRED') {
						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);
					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					} else if (strripos($column_name, "logic") !== false) {
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						$row[] = $myorders->ProblemIdentifiedChecklists;
					} else if (strripos($column_name, "Order-Priority") !== false) {
						$row[] = $myorders->Priority;
					} else if (strripos($column_name, "Order-Comments") !== false) {
						$row[] = $myorders->OrderComments;
					} else if (strripos($column_name, "Workflow-Comments") !== false) {
						$row[] = $myorders->Description;
					} else if ($column_name == 'OrderEntryDateTime') {
						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);
					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {
						$row[] = site_dateformat($myorders->{$column_name});
					} else if (strripos($column_name, "date") !== false) {
						$row[] = site_dateformat(ltrim($myorders->{$column_name}));
					} elseif ($column_name == 'SubQueueCategories') {

						if (isset($Mischallenous['SubQueueSection']) && !empty($Mischallenous['SubQueueSection']) && $Mischallenous['SubQueueSection'] == $column->SubQueueSection && !empty($column->SubQueueCategoryUID)) 
						{
							$SubQueueCategoryAliasName = 'SubQueueCategories_'.$column->SubQueueSection;

							$SubQueueCategoryTemp = [];

							foreach ($SubQueueCategories as $value) {								

							    if (in_array($value['CategoryUID'], explode(',', $myorders->$SubQueueCategoryAliasName)) && in_array($value['CategoryUID'], explode(',', $column->CategoryUIDs)) && $column->WorkflowModuleUID == $myorders->WorkflowModuleUID) {

						    		$SubQueueCategory['SubQueueCategoryUID'] = $column->SubQueueCategoryUID;
						    		$SubQueueCategory['CategoryUID'] = $myorders->$SubQueueCategoryAliasName;
							    	
							    	$SubQueueCategoryTemp[] = $value['CategoryName'];
							    }
							}

							$row[] = implode(', ', $SubQueueCategoryTemp);

						} else {

							continue;
						}
						
					} else if($column_name == 'WorkflowIssueChecklists') {
						$WorkflowIssueChecklists = $this->getWorkflowIssueChecklists_variable($column);		

						$row[] = $myorders->{$WorkflowIssueChecklists};
						
					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} elseif ($column_name == 'KickBackSubQueueAging') {

						$row[] = $this->Get_DynamicSubQueueAging($myorders->ReversedDateTime, $column->SubQueueAging);

					} elseif ($column_name == 'ExpirySubQueueAging') {
						
						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID, $column->SubQueueAging);

					} elseif ($column_name == 'CategoryTAT') {

						$SubQueueCategory['OrderUID'] = $myorders->OrderUID;
						$SubQueueCategory['SubQueueAging'] = $column->SubQueueAging;

						$row[] = $this->CalculateCategoryTATAging($SubQueueCategory);
						// $row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} else if ($column_name == 'DocsOutAging') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$row[] = $this->DocsOutAging($dates, $column);						

					} else if ($column->ColumnName ==  "AgingInHours") {
					
						$row[] = site_datetimeaginginhours($myorders->EntryDatetime);
					
					} else if ($column_name == 'DocsOutAgingHours') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$hours = $this->DocsOutAging($dates, $column) * 24;
						$row[] = $hours != 0 ? $hours:'';						

					} else {
						$row[] = $myorders->{$column_name};
					}
				}

				if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
					$row[] = $myorders->KickbackByUserName;
					$row[] = site_dateformat($myorders->ReversedDateTime);
					$row[] = $myorders->ReversedRemarks;

					// KickBack SubQueue Aging Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

						$row[] = site_datetimeaging($myorders->ReversedDateTime);
					}
				}

				if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

					$row[] = $myorders->RaisedBy;
					$row[] = $myorders->Remarks;
					$row[] = site_datetimeformat($myorders->Remainder);
				}


				if (isset($Mischallenous['IsCompleted']) && $Mischallenous['IsCompleted'] == true) {

					$row[] = $myorders->completedby;
					$row[] = site_datetimeformat($myorders->completeddatetime);
				}
	
				// Email and Phone 
				if (isset($Mischallenous['IsThreeAConfirmationQueue']) && $Mischallenous['IsThreeAConfirmationQueue'] == true) {

					$row[] = ($myorders->IsEmailEnabled == 1) ? 'YES' : '';
					$row[] = ($myorders->IsPhoneEnabled == 1) ? 'YES' : '';
				}

				// HOI Rework Queue
				if (isset($Mischallenous['IsHOIRework']) && $Mischallenous['IsHOIRework'] == true) {

					$row[] = $myorders->EnabledBy;
					$row[] = site_datetimeformat($myorders->EnabledDateTime);
					$row[] = $myorders->EnabledRemarks;
				}

				if (isset($Mischallenous['IsExpiryOrdersQueue']) && $Mischallenous['IsExpiryOrdersQueue'] == TRUE) {
					
					// Expiry SubQueue Aging Column Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {
						
						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID);
					}
				}

				if ($Mischallenous['SubQueueSection'] == "ExpiredCompleteOrdersTable") {
					
					$row[] = site_datetimeformat($myorders->ExpirycompletedDateTime);
				}

				$orderslist[] = $row;
			}


			$DynamicHeaders = $this->Common_Model->dynamicColumnHeaderNames($QueueColumns, $Mischallenous);

			if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
				$DynamicHeaders[] = 'Kickback Associate';
				$DynamicHeaders[] = 'Kickback Date';
				$DynamicHeaders[] = 'Remarks';

				// KickBack SubQueue Aging Enabled Workflows
				if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

					$DynamicHeaders[] = 'KickBack SubQueue Aging';
				}
			}

			if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

				$DynamicHeaders[] = 'Parking RaisedBy';
				$DynamicHeaders[] = 'Parking Remarks';
				$DynamicHeaders[] = 'Parking ReminderOn';
			}


			if (isset($Mischallenous['IsCompleted']) && $Mischallenous['IsCompleted'] == true) {

				$DynamicHeaders[] = 'Completed By';
				$DynamicHeaders[] = 'Completed Date and Time';
			}

			if (isset($Mischallenous['IsThreeAConfirmationQueue']) && $Mischallenous['IsThreeAConfirmationQueue'] == true) {

				$DynamicHeaders[] = 'Email';
				$DynamicHeaders[] = 'Phone';
			}

			if (isset($Mischallenous['IsHOIRework']) && $Mischallenous['IsHOIRework'] == true) {

				$DynamicHeaders[] = 'Raised By';
				$DynamicHeaders[] = 'Raised Date and Time';
				$DynamicHeaders[] = 'Raised Remarks';
			}

			if (isset($Mischallenous['IsExpiryOrdersQueue']) && $Mischallenous['IsExpiryOrdersQueue'] == TRUE) {

				// Expiry SubQueue Aging Column Enabled Workflows
				if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {

					$DynamicHeaders[] = 'Expiry SubQueue Aging';
				}
			}

			if ($Mischallenous['SubQueueSection'] == "ExpiredCompleteOrdersTable") {

				$DynamicHeaders[] = 'Expiry completed DateTime';
			}

			$MischallenousColumns = [];
			$DynamicHeaders = array_merge($DynamicHeaders, $MischallenousColumns);

			$post['header'] = $DynamicHeaders;
			$post['orderslist'] = $orderslist;

			return $post;
		}

		return [];
	}



	/**
	 *@description Function to getGlobalExceptionExcelDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	 * @return Array 
	 * @since 14.5.2020 
	 * @version Dyamc Queues 
	 *
	 */
	function getGlobalExceptionExcelDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{
		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		// Get Categories Details
		$SubQueueCategories = $this->GetSubQueueCategories();

		if (!empty($QueueColumns)) {
			$orderslist = [];
			foreach ($ArrayList as $myorders) {
				$row = array();

				// Reset Arr
				$SubQueueCategory = [];

				foreach ($QueueColumns as $key => $column) {

					if ($this->CheckQueueColumnIsEnabled($column->StaticQueueUIDs, $column->QueueUIDs, '', $myorders->QueueUID)) {
						continue;
					}

					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);
					if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {
						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column->DocumentTypeUID . $column_name});
					} else if ($column_name == 'WorkflowQueue') {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackAssociate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackRemarks") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackDate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;
					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {
						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};
					} else if ($column_name == 'WorkflowCompletedDate') {
						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});
					} else if (strripos($column_name, "ScheduledDate") !== false) {
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = $myorders->ScheduledDate;
					} else if ($column_name == 'ScheduledTime') {
						$row[] = $myorders->ScheduledTime;
					} else if ($column_name == 'LOGIC-STC') {
						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;
					} else if ($column_name == 'LOGIC-LCREQUIRED') {
						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);
					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					}else if (strripos($column_name, "logic") !== false) {
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						$row[] = $myorders->ProblemIdentifiedChecklists;
					} else if (strripos($column_name, "Order-Priority") !== false) {
						$row[] = $myorders->Priority;
					} else if (strripos($column_name, "Order-Comments") !== false) {
						$row[] = $myorders->OrderComments;
					} else if (strripos($column_name, "Workflow-Comments") !== false) {
						$row[] = $myorders->Description;
					} else if ($column_name == 'OrderEntryDateTime') {
						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);
					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {
						$row[] = site_dateformat($myorders->{$column_name});
					} else if (strripos($column_name, "date") !== false) {
						$row[] = site_dateformat(ltrim($myorders->{$column_name}));
					} elseif ($column_name == 'SubQueueCategories') {

						if (!empty($column->SubQueueCategoryUID) && $myorders->QueueUID == $column->SubQueueUID) {

							$SubQueueCategoryAliasName = 'SubQueueCategories_'.$column->SubQueueUID;

							$SubQueueCategoryTemp = [];

							foreach ($SubQueueCategories as $value) {

							    if (in_array($value['CategoryUID'], explode(',', $myorders->$SubQueueCategoryAliasName)) && in_array($value['CategoryUID'], explode(',', $column->CategoryUIDs)) && $column->WorkflowModuleUID == $myorders->WorkflowModuleUID) {
							    		
						    		$SubQueueCategory['SubQueueCategoryUID'] = $column->SubQueueCategoryUID;
						    		$SubQueueCategory['CategoryUID'] = $myorders->$SubQueueCategoryAliasName;
							    	
							    	$SubQueueCategoryTemp[] = $value['CategoryName'];
							    }
							}

							$row[] = implode(', ', $SubQueueCategoryTemp);

						} else {
							continue;
						}
						
					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} elseif ($column_name == 'ExceptionSubQueueAging') {

						$row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} elseif ($column_name == 'CategoryTAT') {

						$SubQueueCategory['OrderUID'] = $myorders->OrderUID;
						$SubQueueCategory['SubQueueAging'] = $column->SubQueueAging;

						$row[] = $this->CalculateCategoryTATAging($SubQueueCategory);
						// $row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} else if ($column_name == 'DocsOutAging') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$row[] = $this->DocsOutAging($dates, $column);						

					} else if ($column_name == 'DocsOutAgingHours') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$hours = $this->DocsOutAging($dates, $column) * 24;
						$row[] = $hours != 0 ? $hours:'';						

					} else {
						$row[] = $myorders->{$column_name};
					}
				}

				if ($Mischallenous['SubQueueDetails']->IsStatus == '1') {
					$row[] = $myorders->QueueIsStatus;
				}

				// $row[] = !empty($myorders->RaisedDateTime) ? site_datetimeaging($myorders->RaisedDateTime) : NULL;;
				$row[] = $myorders->RaisedBy;
				if(!isset($Mischallenous['SubQueueName'])){
						$row[] = $myorders->ReasonName;
				}
				$row[] = site_datetimeformat($myorders->RaisedDateTime);

				$orderslist[] = $row;
			}

			$DynamicHeaders = $this->Common_Model->dynamicColumnHeaderNames($QueueColumns, $Mischallenous);

			// Get Queue Details
			if ($Mischallenous['SubQueueDetails']->IsStatus == '1') {
				$DynamicHeaders[] = 'Status';
			}

			/* ******* Exception Queue fields ***** */
			// $DynamicHeaders[] = 'SubQueue Aging';
			$DynamicHeaders[] = 'Initiated By';
			if(!isset($Mischallenous['SubQueueName'])){

					if ($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) {

						$DynamicHeaders[] = 'Kickback';

					} else {

						$DynamicHeaders[] = 'Reason';

					}

			}
			$DynamicHeaders[] = 'Initiated DateTime';

			$MischallenousColumns = [];
			$DynamicHeaders = array_merge($DynamicHeaders, $MischallenousColumns);

			$post['header'] = $DynamicHeaders;
			$post['orderslist'] = $orderslist;

			return $post;
		}

		return [];
	}

	/**
	 *@description Function to getSelectiveExceptionExcelDynamicQueueColumns
	 *
	 * @param $ArrayList
	 * 
	 * @throws no exception
	 * @author Santhiya <santhiya.m@avanzegroup.com>
	 * @return Array 
	 * @since 22 Augest 2020
	 * @version Dyamc Queues 
	 *
	 */
	function getSelectiveExceptionExcelDynamicQueueColumns($ArrayList, $WorkflowModuleUID, $Mischallenous = [])
	{
		if (isset($Mischallenous['QueueColumns']) && !empty($Mischallenous['QueueColumns'])) {
			$QueueColumns = $Mischallenous['QueueColumns'];
		} else {
			$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
		}

		// Get Categories Details
		$SubQueueCategories = $this->GetSubQueueCategories();

		if (!empty($QueueColumns)) {
			$orderslist = [];
			foreach ($ArrayList as $myorders) {
				$row = array();

				// Reset Arr
				$SubQueueCategory = [];

				foreach ($QueueColumns as $key => $column) {
					$ExtractColumnNameArray = explode(".", $column->ColumnName);
					$column_name = end($ExtractColumnNameArray);
					if ($column->IsChecklist == 1 && !empty($column->WorkflowUID) && !empty($column->DocumentTypeUID)) {
						$row[] = $this->validateConvertDateToFormat($myorders->{'checklist_' . $column->DocumentTypeUID . $column_name});
					} else if ($column_name == 'WorkflowQueue') {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackAssociate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackRemarks") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? $myorders->{$column_name . $column->QueueWorkflowUID} : NULL;
					} else if (strripos($column_name, "KickbackDate") !== false) {
						$row[] = !empty($column->QueueWorkflowUID) ? site_dateformat($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;
					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if ($column_name == 'WorkflowCompletedAssociate') {
						$row[] =  $myorders->{'WorkflowCompletedAssociate'.$column->QueueWorkflowUID};
					} else if ($column_name == 'WorkflowCompletedDate') {
						$row[] = site_dateformat($myorders->{'WorkflowCompletedDate'.$column->QueueWorkflowUID});
					} else if (strripos($column_name, "ScheduledDate") !== false) {
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						if ($myorders->ScheduledDate && $myorders->ScheduledTime && $myorders->EarliestClosingDate) {
							$schedule = $myorders->ScheduledDate . '' . $myorders->ScheduledTime;
							$EarliestClosingDate = $myorders->EarliestClosingDate;
							$closing  = date('Y-m-d H:i:s', strtotime($EarliestClosingDate));
							$combinedDT = date('Y-m-d H:i:s', strtotime($schedule));
							if ($closing > $combinedDT) {
								$select = 'selectPrior';
							} else {
								$select = '';
							}
						} else {
							$select = '';
						}
						/*Desc: Check Prior schedule for the loan @author: Santhiya M <santhiya.m@avanzegroup.com> @since: Friday 10 July 2020*/
						$row[] = $myorders->ScheduledDate;
					} else if ($column_name == 'ScheduledTime') {
						$row[] = $myorders->ScheduledTime;
					} else if ($column_name == 'LOGIC-STC') {
						$row[] = ($myorders->STC == 'Amount') ? $myorders->STCAmount : $myorders->STC;
					} else if ($column_name == 'LOGIC-LCREQUIRED') {
						$row[] = $this->get_lcrequired($myorders->CashFromBorrower, $myorders->ProposedTotalHousingExpense, $myorders->Assets);
					} else if ($column_name == 'LCREQUIRED') {

						$row[] = '$'.$myorders->LCREQUIRED;

					} else if (strripos($column_name, "KICKBACKAGING") !== false) {

						$row[] = !empty($column->QueueWorkflowUID) ? site_datetimeaging($myorders->{$column_name . $column->QueueWorkflowUID}) : NULL;

					} else if (strripos($column_name, "logic") !== false) {
						$row[] = site_datetimeaging($myorders->EntryDatetime);
					} else if (strripos($column_name, "ProblemIdentified") !== false) {
						$row[] = $myorders->ProblemIdentifiedChecklists;
					} else if (strripos($column_name, "Order-Priority") !== false) {
						$row[] = $myorders->Priority;
					} else if (strripos($column_name, "Order-Comments") !== false) {
						$row[] = $myorders->OrderComments;
					} else if (strripos($column_name, "Workflow-Comments") !== false) {
						$row[] = $myorders->Description;
					} else if ($column_name == 'OrderEntryDateTime') {
						$row[] = site_datetimeformat($myorders->OrderEntryDateTime);
					} else if ($column_name == 'ReWorkCompletedDateTime') {

						$row[] = site_datetimeformat($myorders->ReWorkCompletedDateTime);

					} else if (strripos($column_name, "EarliestClosingDate") !== false) {
						$row[] = site_dateformat($myorders->{$column_name});
					} else if (strripos($column_name, "date") !== false) {
						$row[] = site_dateformat(ltrim($myorders->{$column_name}));
					} elseif ($column_name == 'SubQueueCategories') {

						$SubQueueCategoryTemp = [];

						if (!empty($column->SubQueueCategoryUID) && $myorders->QueueUID == $column->SubQueueUID) {

							$SubQueueCategoryAliasName = 'SubQueueCategories_'.$column->SubQueueUID;

							foreach ($SubQueueCategories as $value) {

							    if (in_array($value['CategoryUID'], explode(',', $myorders->$SubQueueCategoryAliasName)) && in_array($value['CategoryUID'], explode(',', $column->CategoryUIDs)) && $column->WorkflowModuleUID == $myorders->WorkflowModuleUID) {
							    		
						    		$SubQueueCategory['SubQueueCategoryUID'] = $column->SubQueueCategoryUID;
						    		$SubQueueCategory['CategoryUID'] = $myorders->$SubQueueCategoryAliasName;
							    	
							    	$SubQueueCategoryTemp[] = $value['CategoryName'];
							    }
							}

						} 

						$row[] = implode(', ', $SubQueueCategoryTemp);
						
					} elseif ($column_name == 'ChecklistGroupIssueCount') {

						$row[] = $myorders->{$column_name."_".$column->QueueColumnUID};

					} elseif ($column_name == 'ExceptionSubQueueAging') {

						$row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} elseif ($column_name == 'CategoryTAT') {

						$SubQueueCategory['OrderUID'] = $myorders->OrderUID;
						$SubQueueCategory['SubQueueAging'] = $column->SubQueueAging;

						$row[] = $this->CalculateCategoryTATAging($SubQueueCategory);
						// $row[] = $this->Get_DynamicSubQueueAging($myorders->RaisedDateTime, $column->SubQueueAging);

					} else if ($column_name == 'DocsOutAging') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$row[] = $this->DocsOutAging($dates, $column);						

					} else if ($column_name == 'DocsOutAgingHours') {

						$dates = [$myorders->QueueDateTime, $myorders->ApprovedMilestoneDate];
						$hours = $this->DocsOutAging($dates, $column) * 24;
						$row[] = $hours != 0 ? $hours:'';						

					} else {
						$row[] = $myorders->{$column_name};
					}
				}

				if ($Mischallenous['SubQueueDetails']->IsStatus == '1') {
					$row[] = $myorders->QueueIsStatus;
				}else{
					$row[] = '';
				}
				if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
					$row[] = $myorders->KickbackByUserName;
					$row[] = site_dateformat($myorders->ReversedDateTime);
					$row[] = $myorders->ReversedRemarks;

					// KickBack SubQueue Aging Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

						$row[] = site_datetimeaging($myorders->ReversedDateTime);
					}

				}else {
					$row[] = '';
					$row[] = '';
					$row[] = '';

					// KickBack SubQueue Aging Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

						$row[] = '';
					}
				}

				if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

					$row[] = $myorders->RaisedBy;
					$row[] = $myorders->Remarks;
					$row[] = site_datetimeformat($myorders->Remainder);
				}else {
					$row[] = '';
					$row[] = '';
					$row[] = '';

				}

				// $row[] = !empty($myorders->RaisedDateTime) ? site_datetimeaging($myorders->RaisedDateTime) : NULL;
				$row[] = $myorders->RaisedBy;
				$row[] = $myorders->ReasonName;
				$row[] = site_datetimeformat($myorders->RaisedDateTime);
				$row[] = ''; //Completed By
				$row[] = ''; //Completed Date time

				//3A Confirmation Queue
				$row[] = '';
				$row[] = '';

				//HOI Rework Qeueue
				$row[] = '';
				$row[] = '';
				$row[] = '';

				if (isset($Mischallenous['IsExpiryOrdersQueue']) && $Mischallenous['IsExpiryOrdersQueue'] == TRUE) {
					
					// Expiry SubQueue Aging Column Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {
						
						$row[] = $this->get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID);
					}
				} else {

					// Expiry SubQueue Aging Column Enabled Workflows
					if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {
						
						$row[] = '';
					}
				}

				// Expiry Complete Orders
				$row[] = '';

				$orderslist[] = $row;
			}

			$DynamicHeaders = $this->Common_Model->dynamicColumnHeaderNames_selectiveexcelexport($QueueColumns, $Mischallenous);

			
				$DynamicHeaders[] = 'Status';
			
			// if (isset($Mischallenous['IsKickBackQueue']) && $Mischallenous['IsKickBackQueue'] == true) {
				$DynamicHeaders[] = 'Kickback Associate';
				$DynamicHeaders[] = 'Kickback Date';
				$DynamicHeaders[] = 'Remarks';

				// KickBack SubQueue Aging Enabled Workflows
				if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) {

					$DynamicHeaders[] = 'KickBack SubQueue Aging';
				}
			// }

			// if (isset($Mischallenous['IsParkingQueue']) && $Mischallenous['IsParkingQueue'] == true) {

				$DynamicHeaders[] = 'Parking RaisedBy';
				$DynamicHeaders[] = 'Parking Remarks';
				$DynamicHeaders[] = 'Parking ReminderOn';

			// }
			/* ******* Exception Queue fields ***** */
			// $DynamicHeaders[] = 'SubQueue Aging';
			$DynamicHeaders[] = 'Initiated By';

			if ($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) {
				
				$DynamicHeaders[] = 'Kickback';

			} else {

				$DynamicHeaders[] = 'Reason';

			}
			
			$DynamicHeaders[] = 'Initiated DateTime';
			$DynamicHeaders[] = 'Completed By';
			$DynamicHeaders[] = 'Completed DateTime';


			// 3A Confirmation Queue
			$DynamicHeaders[] = 'Email';
			$DynamicHeaders[] = 'Phone';

			// HOI Rework Orders
			$DynamicHeaders[] = 'Raised By';
			$DynamicHeaders[] = 'Raised Date and Time';
			$DynamicHeaders[] = 'Raised Remarks';

			// Expiry SubQueue Aging Column Enabled Workflows
			if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) {

				$DynamicHeaders[] = 'Expiry SubQueue Aging';
			}

			// Expiry Complete Orders
			$DynamicHeaders[] = 'Expiry completed DateTime';
				
			$MischallenousColumns = [];
			$DynamicHeaders = array_merge($DynamicHeaders, $MischallenousColumns);

			$post['header'] = $DynamicHeaders;
			$post['orderslist'] = $orderslist;

			return $post;
		}

		return [];
	}

	/**
	 *Function fetch checklist column value
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Monday 18 May 2020
	 */

	function get_checklist_columns($ColumnName, $OrderUID, $WorkflowUID, $DocumentTypeUID)
	{
		if ($this->db->field_exists($ColumnName, 'tDocumentCheckList')) {

			$result =  $this->db->query("(SELECT tDocumentCheckList." . $ColumnName . " FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = '" . $OrderUID . "' AND tDocumentCheckList.DocumentTypeUID = '" . $DocumentTypeUID . "' AND tDocumentCheckList.WorkflowUID = '" . $WorkflowUID . "') ")->row();
			if (!empty($result)) {
				return $result->{$ColumnName};
			}
		}

		return '';
	}

	/**
	 *Function fetch onshore processors
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Wednesday 06 May 2020
	 */

	function get_allonshoreprocessors()
	{
		

		$this->otherdb->select('tOrderImport.LoanProcessor');
		$this->otherdb->from('tOrderImport');
		$this->otherdb->join('tOrders', 'tOrders.OrderUID = tOrderImport.OrderUID', 'left');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->otherdb->group_by('tOrderImport.LoanProcessor');
		$result = $this->otherdb->get()->result();

		$result = array_filter($result);

		$this->otherdb->select('mUsers.UserName,mUsers.UserUID');
		$this->otherdb->from('mGroupUsers');
		$this->otherdb->join('mUsers',  'mUsers.UserUID = mGroupUsers.GroupUserUID');
		$this->otherdb->where('mUsers.Active', 1);
		$this->otherdb->where('mUsers.UserLocation', 'Onshore');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where("EXISTS (SELECT 1 FROM mGroupCustomers WHERE mGroupCustomers.GroupUID = mGroupUsers.GroupUID AND mGroupCustomers.GroupCustomerUID = " . $this->parameters['DefaultClientUID'] . " )", NULL, FALSE);
		}

		//Order Queue Permission
		$this->Common_Model->reportOrderPermission('mUsers.UserUID','USERCOUNT',FALSE,FALSE,TRUE);


		if (!empty($result)) {
			$like = "";
			foreach ($result as $key => $item) {
				if ($key === 0) {
					// first loop
					$like .= "( mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,true) . "%' ";
				} else {
					$like .= " OR mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,true) . "%' ";
				}
			}
			$like .= ") ";
			$this->otherdb->where($like, null, false);
		}
		$this->otherdb->group_by('mUsers.UserUID');
		return $this->otherdb->get()->result();
	}

	/**
	*Function Fetch all onshore processors 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 21 August 2020.
	*/
	public function get_allonshorejuniorprocessors($value='')
	{
		// Get processor Data in tOrderImport table
		$this->otherdb->select('tOrderImport.LoanProcessor');
		$this->otherdb->from('tOrderImport');
		$this->otherdb->join('tOrders', 'tOrders.OrderUID = tOrderImport.OrderUID', 'left');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->otherdb->where('tOrderImport.LoanProcessor IS NOT NULL', NULL, FALSE);
		$this->otherdb->group_by('tOrderImport.LoanProcessor');
		$tOrderImportProcessorDetails = $this->otherdb->get()->result();

		$tOrderImportProcessorDetails = array_filter($tOrderImportProcessorDetails);		


		$this->otherdb->select('mUsers.UserName,mUsers.UserUID');
		$this->otherdb->from('mJuniorProcessorUsers');
		$this->otherdb->join('mJuniorProcessorGroup','mJuniorProcessorGroup.GroupUID = mJuniorProcessorUsers.GroupUID','left');
		$this->otherdb->join('mUsers','(mUsers.UserUID = mJuniorProcessorUsers.ProcessorUserUID) OR (mUsers.UserUID = mJuniorProcessorGroup.JuniorProcessorUserUID)','left');
		$this->otherdb->where('mJuniorProcessorGroup.Active',STATUS_ONE);
		// check if role type is super admin, supervisor and admin able to access all the details
		if (!in_array($this->RoleType, $this->config->item('SuperAccess'))) 
		{
			$this->otherdb->where('mJuniorProcessorGroup.JuniorProcessorUserUID',$this->loggedid);
		}

		if (!empty($tOrderImportProcessorDetails)) {
			$like = "";
			foreach ($tOrderImportProcessorDetails as $key => $item) {
				if ($key === 0) {
					// first loop
					$like .= "( mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,true) . "%' ";
				} else {
					$like .= " OR mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,true) . "%' ";
				}
			}
			$like .= ") ";
			$this->otherdb->where($like, null, false);
		}

		$this->otherdb->group_by('mUsers.UserUID');

		return $this->otherdb->get()->result();
	}

	/**
	*Function Get Junior Processor Queue Details 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Saturday 22 August 2020.
	*/
	public function GetJuniorProcessorQueueDetails()
	{

		$this->otherdb->select('mJuniorProcessorWorkflows.WorkflowModuleUID,mJuniorProcessorWorkflows.QueueUID,mJuniorProcessorWorkflows.IsKickBack');
		$this->otherdb->from('mJuniorProcessorGroup');
		$this->otherdb->join('mJuniorProcessorWorkflows','mJuniorProcessorWorkflows.GroupUID=mJuniorProcessorGroup.GroupUID','left');
		$this->otherdb->where('mJuniorProcessorGroup.Active',STATUS_ONE);
		$this->otherdb->where('mJuniorProcessorWorkflows.WorkflowModuleUID IS NOT NULL',NULL,FALSE);
		// check if role type is super admin, supervisor and admin able to access all the details
		if (!in_array($this->RoleType, $this->config->item('SuperAccess'))) 
		{
			$this->otherdb->where('mJuniorProcessorGroup.JuniorProcessorUserUID',$this->loggedid);	
		}
		
		return $this->otherdb->get()->result();
	}

	/**
	 *Function fetch all onshore team leads
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Tuesday 12 May 2020
	 */

	function get_allonshoreteamleads()
	{
		

		$userresult = [];

		// $this->otherdb->select('tOrderImport.LoanProcessor');
		// $this->otherdb->from('tOrderImport');
		// $this->otherdb->join('tOrders', 'tOrders.OrderUID = tOrderImport.OrderUID', 'left');
		// if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
		// 	$this->otherdb->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		// }
		// $this->otherdb->group_by('tOrderImport.LoanProcessor');

		// $result = $this->otherdb->get()->result();
		// $result = array_filter($result);

		// //get user query


		// if (!empty($result)) {
		// 	$like = "";
		// 	foreach ($result as $key => $item) {
		// 		if ($key === 0) {
		// 			// first loop
		// 			$like .= "( mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,TRUE) . "%' ";
		// 		} else {
		// 			$like .= " OR mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,TRUE) . "%' ";
		// 		}
		// 	}
		// 	$like .= ") ";
		// 	$this->otherdb->select("GroupUID");
		// 	$this->otherdb->from('mGroupUsers');
		// 	$this->otherdb->join('mUsers',  'mUsers.UserUID = mGroupUsers.GroupUserUID');
		// 	$this->otherdb->where($like, NULL, FALSE);
		// 	$userresult = $this->otherdb->get_compiled_select();
		// }

		$this->otherdb->select('mUsers.UserName,mUsers.UserUID');
		$this->otherdb->from('mGroupTeamLeaders');
		$this->otherdb->join('mUsers',  'mUsers.UserUID = mGroupTeamLeaders.GroupTeamUserUID');
		$this->otherdb->where('mUsers.Active', 1);
		$this->otherdb->where('mUsers.UserLocation', 'Onshore');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where("EXISTS (SELECT 1 FROM mGroupCustomers WHERE mGroupCustomers.GroupUID = mGroupTeamLeaders.GroupUID AND mGroupCustomers.GroupCustomerUID = " . $this->parameters['DefaultClientUID'] . " )", NULL, FALSE);
		}

		//Order Queue Permission
		$this->Common_Model->reportOrderPermission('mUsers.UserUID','USERCOUNT',FALSE,FALSE,TRUE);

		// if (!empty($userresult)) {
		// 	$this->otherdb->where_in('mGroupTeamLeaders.GroupUID', $userresult, FALSE);
		// }

		$this->otherdb->group_by('mUsers.UserUID');
		return $this->otherdb->get()->result();
	}

	/**
	*Function fetch all onshore Junior Processor count
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 12 May 2020
	*/

	function get_allonshorejuniorprocessorslead()
	{
		

	// Get processor Data in tOrderImport table
	/*	$this->otherdb->select('tOrderImport.LoanProcessor');
		$this->otherdb->from('tOrderImport');
		$this->otherdb->join('tOrders', 'tOrders.OrderUID = tOrderImport.OrderUID', 'left');
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->otherdb->where('tOrderImport.LoanProcessor IS NOT NULL', NULL, FALSE);
		$this->otherdb->group_by('tOrderImport.LoanProcessor');
		$tOrderImportProcessorDetails = $this->otherdb->get()->result();

		$tOrderImportProcessorDetails = array_filter($tOrderImportProcessorDetails);	*/	


		$this->otherdb->select('mUsers.UserName,mUsers.UserUID');
		$this->otherdb->from('mJuniorProcessorUsers');
		$this->otherdb->join('mJuniorProcessorGroup','mJuniorProcessorGroup.GroupUID = mJuniorProcessorUsers.GroupUID','left');
		$this->otherdb->join('mUsers','mUsers.UserUID = mJuniorProcessorGroup.JuniorProcessorUserUID','left');
		$this->otherdb->where('mJuniorProcessorGroup.Active',STATUS_ONE);
		// check if role type is super admin, supervisor and admin able to access all the details
		if (!in_array($this->RoleType, $this->config->item('SuperAccess'))) 
		{
			$this->otherdb->where('mJuniorProcessorGroup.JuniorProcessorUserUID',$this->loggedid);
		}

		/*if (!empty($tOrderImportProcessorDetails)) {
			$like = "";
			foreach ($tOrderImportProcessorDetails as $key => $item) {
				if ($key === 0) {
					// first loop
					$like .= "( mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,true) . "%' ";
				} else {
					$like .= " OR mUsers.UserName LIKE '%" . $this->db->escape_str($item->LoanProcessor,true) . "%' ";
				}
			}
			$like .= ") ";
			$this->otherdb->where($like, null, false);
		}*/

		$this->otherdb->group_by('mUsers.UserUID');

		return $this->otherdb->get()->result();
	}

	function Datatable_Search_having($post)
	{

		if (!empty($post['search_value'])) {
			$like = "";
			foreach ($post['column_search'] as $key => $item) { // loop column
				// if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( " . $item . " LIKE '%" . $post['search_value'] . "%' ";
				} else {
					$like .= " OR " . $item . " LIKE '%" . $post['search_value'] . "%' ";
				}
			}
			$like .= ") ";
			$this->db->having($like, null, false);
		}
	}


	/**
	 *Function get CustomerWorkflowQueues 
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Monday 25 May 2020
	 */
	public function get_borrowerdynamicworkflow_queues($QueueUID = FALSE)
	{
		$this->db->select('mQueues.*, mWorkFlowModules.*', false);
		$this->db->from('mQueues');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mQueues.WorkflowModuleUID', 'left');
		$this->db->where('mQueues.IsBorrowerDocs', STATUS_ONE);
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('mQueues.Active', 1);
		if ($QueueUID) {
			$this->db->where('mQueues.QueueUID', $QueueUID);
		}
		return $this->db->get()->result();
	}


	function get_clientrow()
	{
		
		$this->otherdb->select('*');
		$this->otherdb->from('mCustomer');
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->otherdb->where('mCustomer.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		return $this->otherdb->get()->row();
	}

	function Is_ClientWorkflowavailable($CustomerUID, $WorkflowModuleUID)
	{
		if (!empty($CustomerUID) && !empty($WorkflowModuleUID)) {
			$this->db->select('mWorkFlowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName,mWorkFlowModules.SystemName');
			$this->db->from('mCustomerWorkflowModules');
			$this->db->join('mWorkFlowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID');
			$this->db->where('mCustomerWorkflowModules.CustomerUID', $CustomerUID);
			$this->db->where('mCustomerWorkflowModules.WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('mWorkFlowModules.Active', STATUS_ONE);
			return $this->db->get()->row();
		}
		return [];
	}

	function get_orderclientrow($ClientUID)
	{
		
		$this->otherdb->select('*');
		$this->otherdb->from('mCustomer');
		$this->otherdb->where('mCustomer.CustomerUID', $ClientUID);
		return $this->otherdb->get()->row();
	}

	/**
	 *Function check workup enabled 
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Friday 29 May 2020
	 */
	function check_forcequeueenabled($OrderUID, $CustomerUID, $WorkflowModuleUID)
	{
		$client = $this->get_orderclientrow($CustomerUID);

		if (empty($client)) {
			return false;
		}


		if (!isset($client->EnableWorkupOption)) {
			return false;
		}

		if ($client->EnableWorkupOption == 0) {
			return false;
		}

		$torderworkflowrow = $this->db->where(array('OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID))->get('tOrderWorkflows')->row();

		if (isset($torderworkflowrow->IsKickBack) && $torderworkflowrow->IsKickBack == 1) {
			return true;
		}

		if (isset($torderworkflowrow->IsRework) && $torderworkflowrow->IsRework == 1) {
			return true;
		}

		if ($this->IsWorkflowCompleted($OrderUID, $WorkflowModuleUID)) {
			return false;
		}

		// chek is parking order start
		if (!empty($this->is_workflow_in_parkingqueue($OrderUID, $WorkflowModuleUID))) {
			return true;
		}
		// chek is parking order end


		if (empty($torderworkflowrow)) {
			return false;
		}

		if (isset($torderworkflowrow->IsForceEnabled) && $torderworkflowrow->IsForceEnabled == 1) {
			return false;
		}

		if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && isset($torderworkflowrow->IsReversed) && $torderworkflowrow->IsReversed == 1 && isset($torderworkflowrow->IsPresent)) {
			return true;
		}


		if (empty($this->Is_ClientWorkflowavailable($CustomerUID, $WorkflowModuleUID))) {
			return false;
		}



		if (isset($torderworkflowrow->IsPresent)  && isset($torderworkflowrow->EntryDateTime) && $torderworkflowrow->IsPresent == 1 && !empty($torderworkflowrow->EntryDateTime)) {
			return false;
		}

		return true;
	}

	function check_raisefollowup_enabled($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*')->from('tOrderFollowUp');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsCleared', 0);
		if ($this->db->get()->num_rows()) {
			return false;
		}
		return true;
	}

	function check_clearfollowup_enabled($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*')->from('tOrderFollowUp');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsCleared', 0);
		return $this->db->get()->num_rows();
	}

	/**
	 *Function check doc chase parking enabled
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Saturday 30 May 2020
	 */

	function check_docchasefollowup_enabled($OrderUID)
	{
		$this->db->select('*')->from('tOrderFollowUp');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('IsCleared', 0);
		$this->db->where('IsDocChaseFollowup', 1);
		return $this->db->get()->num_rows();
	}

	function get_dynamiccolumnquery_bycolumname($Columnrow)
	{
		$returnval = '';
		if (!isset($Columnrow->IsChecklist) || empty($Columnrow->IsChecklist)) {

			switch ($Columnrow->ColumnName) {

				case ($Columnrow->ColumnName == 'WorkflowQueue' || $Columnrow->ColumnName == 'Queue'):
					if (!empty($Columnrow->QueueWorkflowUID)) {

						$DependentWorkflowModuleUIDs = $this->getDependentworkflows($Columnrow->QueueWorkflowUID);
						$DependentWorkflowUIDQuery = FALSE;
						if (!empty($DependentWorkflowModuleUIDs)) {

							foreach ($DependentWorkflowModuleUIDs as $DependentWorkflowModuleUIDKey => $DependentWorkflowModuleUID) {

								$DependentWorkflowUIDQuery .= " AND (tOrderWorkflows.IsForceEnabled = 1 OR EXISTS (SELECT TOA_".$DependentWorkflowModuleUIDKey.".OrderUID FROM tOrderAssignments TOA_".$DependentWorkflowModuleUIDKey." where TOA_".$DependentWorkflowModuleUIDKey.".OrderUID = suborder.OrderUID AND TOA_".$DependentWorkflowModuleUIDKey.".WorkflowModuleUID = ".$DependentWorkflowModuleUID." AND TOA_".$DependentWorkflowModuleUIDKey.".WorkflowStatus = 5))";					

							}
						}

						if($Columnrow->QueueWorkflowUID == $this->config->item('Workflows')['Submissions']) {
							$queueconditions = submissionqueueconditions();
							$DependentWorkflowUIDQuery .= !empty($queueconditions) ? $queueconditions : NULL;
						}
						$DocsOutCase = '';
						if($Columnrow->QueueWorkflowUID == $this->config->item('Workflows')['DocsOut']) {
							$DocsOutSubQueueSQL = $this->DocsOut_Orders_Model->DocsOutSubQueueConditions();
							//docs checked condition pending
							$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"] = $this->db->query($DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"])->result_array();
							$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrderUIDs"] = ($DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"] != '') ? implode("," , array_unique(array_filter(array_column($DocsOutSubQueueSQL["DocsCheckedConditionPendingOrdersSQL"], 'OrderUID')))) : NULL;
							$DocsOutCase .= !empty($DocsOutSubQueueSQL["DocsCheckedConditionPendingOrderUIDs"]) ? " WHEN tOrders.OrderUID IN  (".$DocsOutSubQueueSQL["DocsCheckedConditionPendingOrderUIDs"].") THEN 'Docs Checked Conditions Pending' " : NULL;

							//PendingfromUWOrders
							$DocsOutSubQueueSQL["PendingfromUWOrdersSQL"] = $this->db->query($DocsOutSubQueueSQL["PendingfromUWOrdersSQL"])->result_array();
							$DocsOutSubQueueSQL["PendingfromUWOrdersSQLOrderUIDs"] = ($DocsOutSubQueueSQL["PendingfromUWOrdersSQL"] != '') ? implode("," , array_unique(array_filter(array_column($DocsOutSubQueueSQL["PendingfromUWOrdersSQL"], 'OrderUID')))) : NULL;
							$DocsOutCase .= !empty($DocsOutSubQueueSQL["PendingfromUWOrdersSQLOrderUIDs"]) ? " WHEN tOrders.OrderUID IN  (".$DocsOutSubQueueSQL["PendingfromUWOrdersSQLOrderUIDs"].") THEN 'Pending From UW' " : NULL;

							//PendingfromUWOrders
							$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"] = $this->db->query($DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"])->result_array();
							$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQLOrderUIDs"] = ($DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"] != '') ? implode("," , array_unique(array_filter(array_column($DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQL"], 'OrderUID')))) : NULL;
							$DocsOutCase .= !empty($DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQLOrderUIDs"]) ? " WHEN tOrders.OrderUID IN  (".$DocsOutSubQueueSQL["SubmittedforDocCheckOrdersSQLOrderUIDs"].") THEN 'Submitted For Doc Check' " : NULL;

							//Non Workable Orders
							$DocsOutSubQueueSQL["NonWorkableOrders"] = $this->DocsOut_Orders_Model->docsoutnonworkableordersquery();
							$DocsOutSubQueueSQL["NonWorkableOrdersOrderUIDs"] = ($DocsOutSubQueueSQL["NonWorkableOrders"] != '') ? implode("," , array_unique(array_filter(array_column($DocsOutSubQueueSQL["NonWorkableOrders"], 'OrderUID')))) : NULL;
							$DocsOutCase .= !empty($DocsOutSubQueueSQL["NonWorkableOrdersOrderUIDs"]) ? " WHEN tOrders.OrderUID IN  (".$DocsOutSubQueueSQL["NonWorkableOrdersOrderUIDs"].") THEN 'Non Workable' " : NULL;
							
							//new orders
							
							$DocsOutSubQueueSQL['AllOrdersSQL'] = $this->DocsOut_Orders_Model->docsoutallordersquery();
							$DocsOutSubQueueSQL["AllOrdersUIDs"] = ($DocsOutSubQueueSQL["AllOrdersSQL"] != '') ? implode("," , array_unique(array_filter(array_column($DocsOutSubQueueSQL["AllOrdersSQL"], 'OrderUID')))) : NULL;

							$DependentWorkflowUIDQuery .= !empty($DocsOutSubQueueSQL["AllOrdersUIDs"]) ? " AND tOrders.OrderUID IN  (".$DocsOutSubQueueSQL["AllOrdersUIDs"].") " : NULL;
							unset($DocsOutSubQueueSQL);
						}

						$returnval = "(SELECT (CASE 

						WHEN (tOrderWorkflows.IsKickBack = 1 AND tOrderWorkflows.WorkflowModuleUID = ".$this->config->item('Workflows')['Workup'].") THEN 'Kickback Order'

						WHEN (tOrderWorkflows.IsRework = 1 AND tOrderWorkflows.WorkflowModuleUID = ".$this->config->item('Workflows')['Workup'].") THEN 'Rework Order'

						WHEN (tOrderWorkflows.WorkflowModuleUID = ".$this->config->item('Workflows')['GateKeeping']." AND tOrderReWork.IsReWorkEnabled = ".STATUS_ONE." AND tOrderAssignments.WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." AND tOrderWorkflows.IsForceEnabled = ".STATUS_ONE.") THEN 'Re-Work'

						WHEN (tOrderAssignments.WorkflowStatus = 5 AND tOrderWorkflows.IsReversed = 1 AND tOrderWorkflows.WorkflowModuleUID IN (".$this->config->item('Workflows')['PreScreen'].",".$this->config->item('Workflows')['TitleTeam'].",".$this->config->item('Workflows')['FHAVACaseTeam'].",".$this->config->item('Workflows')['ThirdPartyTeam'].",".$this->config->item('Workflows')['HOI'].") AND (tOrderWorkflows.IsPresent = 1 OR tOrderWorkflows.IsForceEnabled = 1)) THEN 'Resolved'

						WHEN (tOrderAssignments.WorkflowStatus = 5) THEN 'Completed'

						WHEN (tOrderParking.IsCleared = 0 AND (tOrderWorkflows.IsPresent = 1 OR tOrderWorkflows.IsForceEnabled = 1)) THEN 'Parking Order'

						WHEN (OrderQueues.QueueName <> '' AND (tOrderWorkflows.IsPresent = 1 OR tOrderWorkflows.IsForceEnabled = 1)) THEN OrderQueues.QueueName

						WHEN (tOrderWorkflows.IsReversed = 1 AND tOrderWorkflows.WorkflowModuleUID IN (".$this->config->item('Workflows')['PreScreen'].",".$this->config->item('Workflows')['TitleTeam'].",".$this->config->item('Workflows')['FHAVACaseTeam'].",".$this->config->item('Workflows')['ThirdPartyTeam'].",".$this->config->item('Workflows')['HOI'].") AND (tOrderWorkflows.IsPresent = 1 OR tOrderWorkflows.IsForceEnabled = 1)) THEN 'Kickback Order'

						".$DocsOutCase."

						WHEN tOrderWorkflows.WorkflowModuleUID = ".$this->config->item('Workflows')['Workup']." AND (tOrderWorkflows.IsPresent = ". STATUS_ZERO ." OR tOrderWorkflows.IsForceEnabled = ". STATUS_ZERO .") AND (tOrderAssignments.AssignedToUserUID = '' OR tOrderAssignments.AssignedToUserUID IS NULL) ".$DependentWorkflowUIDQuery." THEN ''

						WHEN (tOrderAssignments.AssignedToUserUID = " . $this->loggedid . " AND (tOrderWorkflows.IsPresent = 1 OR tOrderWorkflows.IsForceEnabled = 1)) THEN 'My Order'


						WHEN (tOrderAssignments.AssignedToUserUID <> " . $this->loggedid . " AND tOrderAssignments.AssignedToUserUID IS NOT NULL AND (tOrderWorkflows.IsPresent = 1 OR tOrderWorkflows.IsForceEnabled = 1) ".$DependentWorkflowUIDQuery." ) THEN 'Assigned Order'


						WHEN (tOrderAssignments.AssignedToUserUID = '' OR tOrderAssignments.AssignedToUserUID IS NULL AND (tOrderWorkflows.IsPresent = 1 OR tOrderWorkflows.IsForceEnabled = 1) ".$DependentWorkflowUIDQuery." ) THEN 'New Order'

						ELSE '' 

						END
						)  FROM `tOrders` suborder 
						LEFT JOIN  tOrderAssignments ON `tOrderAssignments`.`OrderUID` = `suborder`.`OrderUID` AND `tOrderAssignments`.`WorkflowModuleUID` = ".$Columnrow->QueueWorkflowUID."
						LEFT JOIN (SELECT mQueues.QueueUID,mQueues.QueueName,tOrderQueues.OrderUID FROM tOrderQueues JOIN mQueues ON mQueues.QueueUID = tOrderQueues.QueueUID AND mQueues.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID." WHERE QueueStatus = 'Pending') OrderQueues ON `OrderQueues`.`OrderUID` = `suborder`.`OrderUID`
						LEFT JOIN `tOrderParking` ON `tOrderParking`.`OrderUID` = `suborder`.`OrderUID` AND `tOrderParking`.`WorkflowModuleUID` = ".$Columnrow->QueueWorkflowUID." AND `tOrderParking`.`IsCleared` = 0
						LEFT JOIN `tOrderWorkflows` ON `tOrderWorkflows`.`OrderUID` = `suborder`.`OrderUID` AND `tOrderWorkflows`.`WorkflowModuleUID` = ".$Columnrow->QueueWorkflowUID."
						LEFT JOIN `tOrderReWork` ON `tOrderReWork`.`OrderUID` = `suborder`.`OrderUID` AND `tOrderReWork`.`WorkflowModuleUID` = `tOrderWorkflows`.`WorkflowModuleUID` AND `tOrderReWork`.`IsReWorkEnabled` = ".STATUS_ONE." WHERE suborder.OrderUID = tOrders.OrderUID LIMIT 0,1)";

					} else {
						$returnval = "";
					}

					break;

				case 'ProblemIdentified-Checklists':
					$returnval = "( SELECT 
				GROUP_CONCAT( DISTINCT
				CASE
				WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
				CASE 
				WHEN tDocumentCheckList.DocumentTypeUID = '' OR tDocumentCheckList.DocumentTypeUID IS NULL THEN
				tDocumentCheckList.DocumentTypeName
				ELSE						
				CASE 
				WHEN mDocumentType.ScreenCode = '' OR mDocumentType.ScreenCode IS NULL THEN
				mDocumentType.DocumentTypeName
				ELSE
				mDocumentType.ScreenCode
				END
				END

				ELSE
				NULL
				END
				) FROM tDocumentCheckList LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` AND mDocumentType.Active = 1
				WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` AND `tDocumentCheckList`.`WorkflowUID` = " . $Columnrow->WorkflowUID . "
			)";
					break;
				case 'PriorityIssueChecklists':
					$returnval = "( SELECT 
			GROUP_CONCAT( DISTINCT
			CASE
			WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
			CASE 
			WHEN tDocumentCheckList.DocumentTypeUID = '' OR tDocumentCheckList.DocumentTypeUID IS NULL THEN
			tDocumentCheckList.DocumentTypeName
			ELSE						
			mDocumentType.DocumentTypeName
			END

			ELSE
			NULL
			END
			) FROM tDocumentCheckList LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` AND mDocumentType.Active = 1
			WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` 
			AND `tDocumentCheckList`.`WorkflowUID` = ".$this->config->item('Workflows')['PreScreen']."
		)";
					break;

				// Workflow and subqueue IssueChecklists
				case 'WorkflowIssueChecklists':	

					$IssueWorkflowQueue_Join = '';
					$IssueWorkflowQueue_Where = '';
					if (!empty($Columnrow->ChecklistIssueWorkflowUID)) {

						$IssueWorkflowQueue_Where .= "
							 AND `tDocumentCheckList`.`WorkflowUID` = ".$Columnrow->ChecklistIssueWorkflowUID."
						";

						if (!empty($Columnrow->ChecklistIssueSubQueueUID)) {
							
							$IssueWorkflowQueue_Join .= "
								INNER JOIN `tOrderQueues` ON `tOrderQueues`.`QueueUID` = ".$Columnrow->ChecklistIssueSubQueueUID." 
								AND `tOrderQueues`.`QueueStatus` = 'Pending'
							";

							$IssueWorkflowQueue_Where .= "
								 AND `tOrderQueues`.`OrderUID` = `tOrders`.`OrderUID` 
							";
						}
					}			

					$returnval = "( SELECT 
									GROUP_CONCAT( DISTINCT
									CASE WHEN 
										tDocumentCheckList.Answer = 'Problem Identified' 
									THEN
										CASE WHEN 
											tDocumentCheckList.DocumentTypeUID = '' OR tDocumentCheckList.DocumentTypeUID IS NULL 
										THEN
											tDocumentCheckList.DocumentTypeName
										ELSE						
											mDocumentType.DocumentTypeName
										END
									ELSE
										NULL
									END
									) FROM tDocumentCheckList 
									LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` AND mDocumentType.Active = 1						
									".$IssueWorkflowQueue_Join."
									WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` 
									AND (`tDocumentCheckList`.`WorkflowUID` <> '' OR `tDocumentCheckList`.`WorkflowUID` IS NOT NULL) 
									".$IssueWorkflowQueue_Where."
								)";
					break;

				case 'Order-Priority':

					$Customer_Prioritys = $this->Priority_Report_model->get_priorityreportheader($this->parameters['DefaultClientUID']);
					if (!empty($Customer_Prioritys)) {
						$PriorityWHERE = [];
						foreach ($Customer_Prioritys as $Priorityrow) {

							$PriorityFields = $this->Priority_Report_model->get_priorityreportfields($Priorityrow->PriorityUID);
							if (!empty($PriorityFields)) {
								$PriorityLOOPWHERE = [];
								foreach ($PriorityFields as $PriorityFieldrow) {

									// Expiry Checklist
									$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
									$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

									$CHECKLISTEXPCOND = [];
									$CHECKLISTEXPCASE = "";
									if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

										foreach ($workflowchecklist as $checklistkey => $checklistuid) {

											if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

											} else {

												if ($PriorityFieldrow->WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {

													$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentDate IS NOT NULL AND tDocumentCheckList.DocumentDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
												} else {

													$CHECKLISTEXPCOND[] = "(tDocumentCheckList.DocumentTypeUID = ".$checklistuid." AND tDocumentCheckList.DocumentExpiryDate IS NOT NULL AND tDocumentCheckList.DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(tDocumentCheckList.DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND (tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones')).") OR (tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = '')))";
												}
																
											}

										}

									}

									if ($PriorityFieldrow->WorkflowStatus == 'Completed') {

										if (!empty($CHECKLISTEXPCOND)) {

											$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
											
											$CHECKLISTEXPCASE = " AND 
											(CASE 
												WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN FALSE
												ELSE TRUE
											END)";
										}

										$PriorityLOOPWHERE[] = "(TWPR_" . $PriorityFieldrow->SystemName . ".WorkflowModuleUID = " . $PriorityFieldrow->WorkflowModuleUID . " AND TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " ) ".$CHECKLISTEXPCASE;
									} else {

										if (!empty($CHECKLISTEXPCOND)) {

											$TOCECTMP = "NOT EXISTS(SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID.")";
											
											$CHECKLISTEXPCASE = "TWPR_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOAPR_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." AND 
											(CASE 
												WHEN EXISTS (SELECT 1 FROM tDocumentCheckList WHERE tDocumentCheckList.OrderUID = tOrders.OrderUID AND tDocumentCheckList.WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (".implode(" AND ", $CHECKLISTEXPCOND).") AND ".$TOCECTMP.") THEN TRUE
												ELSE FALSE
											END)";
										}

										$PriorityLOOPWHERE[] = "((TWPR_" . $PriorityFieldrow->SystemName . ".WorkflowModuleUID = " . $PriorityFieldrow->WorkflowModuleUID . " AND (TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus IS NULL OR TOAPR_" . $PriorityFieldrow->SystemName . ".WorkflowStatus <> " . $this->config->item('WorkflowStatus')['Completed'] . ") )  OR ( ".(!empty($CHECKLISTEXPCASE)? $CHECKLISTEXPCASE : "FALSE")."))";
									}
								}

								$PriorityWHERE[$Priorityrow->PriorityName] = "(" . implode(" AND ", $PriorityLOOPWHERE) . " )";
							}
						}

						if (!empty($PriorityWHERE)) {

							$PriorityWHERECASECONDITION = [];
							foreach ($PriorityWHERE as $PriorityWHEREkey => $PriorityWHEREvalue) {
								$PriorityWHERECASECONDITION[] = "WHEN " . $PriorityWHEREvalue . " THEN '" . $PriorityWHEREkey . "'";
							}

							$returnval = "(CASE " . implode(" ", $PriorityWHERECASECONDITION) . "   ELSE '' END)";
						}
					}
					break;


					case 'WorkflowCompletedDate':

					if(!empty($Columnrow->QueueWorkflowUID) && $Columnrow->QueueWorkflowUID == $this->config->item('Workflows')['Workup']) {

						$WorkupSubQueueComplete = implode(',', $this->config->item('WorkupSubQueueComplete'));

						$returnval = "(SELECT (CASE 

						WHEN (tOrderAssignments.WorkflowStatus = 5) THEN tOrderAssignments.CompleteDateTime

						WHEN (tOrderQueues.RaisedDateTime IS NOT NULL OR tOrderQueues.RaisedDateTime <> '') THEN MAX(tOrderQueues.RaisedDateTime)

						ELSE '' 

						END
						)  FROM `tOrders` suborder 
						LEFT JOIN  tOrderAssignments ON `tOrderAssignments`.`OrderUID` = `suborder`.`OrderUID` AND `tOrderAssignments`.`WorkflowModuleUID` = ".$Columnrow->QueueWorkflowUID."
						LEFT JOIN `tOrderQueues` ON `tOrderQueues`.`OrderUID` = `suborder`.`OrderUID` AND `tOrderQueues`.`QueueUID` IN (".$WorkupSubQueueComplete.")
						WHERE suborder.OrderUID = tOrders.OrderUID 
						LIMIT 0,1)";

						// echo '<pre>';print_r($WorkupSubQueueComplete);exit;

					} elseif (!empty($Columnrow->QueueWorkflowUID)) {
						
						$returnval = "(SELECT CompleteDateTime FROM tOrderAssignments WHERE tOrderAssignments.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID." AND tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowStatus = 5 LIMIT 1)";
					}
					break;

					case 'WorkflowCompletedAssociate':
					if(!empty($Columnrow->QueueWorkflowUID)) {

						$returnval = "(SELECT UserName FROM tOrderAssignments JOIN mUsers ON mUsers.UserUID = tOrderAssignments.CompletedByUserUID WHERE tOrderAssignments.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID." AND tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowStatus = 5 LIMIT 1)";
					}
					break;


					case 'KickbackAssociate':
					if(!empty($Columnrow->QueueWorkflowUID)) {
						$returnval = "(SELECT KickbackAssociate.UserName FROM tOrderWorkflows JOIN mUsers KickbackAssociate ON KickbackAssociate.UserUID = tOrderWorkflows.ReversedByUserUID WHERE tOrderWorkflows.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID." AND tOrderWorkflows.OrderUID = tOrders.OrderUID LIMIT 1)";
					}
					break;

					case 'KickbackRemarks':
					if(!empty($Columnrow->QueueWorkflowUID)) {
						$returnval = "(SELECT ReversedRemarks FROM tOrderWorkflows WHERE tOrderWorkflows.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID." AND tOrderWorkflows.OrderUID = tOrders.OrderUID LIMIT 1)";
					}
					break;

					case 'KickbackDate':
					if(!empty($Columnrow->QueueWorkflowUID)) {
						$returnval = "(SELECT ReversedDateTime FROM tOrderWorkflows WHERE tOrderWorkflows.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID." AND tOrderWorkflows.OrderUID = tOrders.OrderUID LIMIT 1)";
					}
					break;

					case 'KICKBACKAGING':
					if(!empty($Columnrow->QueueWorkflowUID)) {
						$returnval = "(SELECT ReversedDateTime FROM tOrderWorkflows WHERE tOrderWorkflows.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID." AND tOrderWorkflows.OrderUID = tOrders.OrderUID LIMIT 1)";
					}
					break;

					case 'BorrowerNames':
					$returnval = "(SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName)) FROM tOrderPropertyRole WHERE tOrderPropertyRole.OrderUID = tOrders.OrderUID LIMIT 1)";
					break;

					case 'OrderJuniorProcessorComments ':
					$returnval = "(tOrderImport.OrderJuniorProcessorComments)";
					break;

					case 'ReWorkCompletedDateTime':
					$returnval = "(SELECT CompletedDateTime FROM tOrderReWork WHERE OrderReWorkUID = (SELECT MAX(OrderReWorkUID) FROM tOrderReWork WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND IsReWorkEnabled = ".STATUS_ZERO." LIMIT 1))";

					break;

					case 'ReWorkCompletedBy':
					$returnval = "(SELECT ReWorkAssociate.UserName FROM tOrderReWork LEFT JOIN mUsers ReWorkAssociate ON ReWorkAssociate.UserUID = tOrderReWork.CompletedByUserUID WHERE OrderReWorkUID = (SELECT MAX(OrderReWorkUID) FROM tOrderReWork WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND IsReWorkEnabled = ".STATUS_ZERO." LIMIT 1))";

					break;

					case 'LCREQUIRED':

					$returnval = "(
								SELECT
									GREATEST(
										ROUND(
											IFNULL(
												(
													tOrderImport.CashFromBorrower - (
														tOrderImport.ProposedTotalHousingExpense + tOrderImport.Assets
													)
												),
												0
											),
											2
										),
										0
									)
								)";

					 	// return '$' . max(number_format((float)$CashFromBorrower - ((float)$ProposedTotalHousingExpense + (float)$Assets), 2), 0);

					 break;

					// SubQueueCategories
					case 'SubQueueCategories':

						$subquery_where = '';
						if (!empty($Columnrow->SubQueueUID)) {

							$subquery_where = "AND mSubQueueCategory.SubQueueUID = '".$Columnrow->SubQueueUID."'";
						} elseif(!empty($Columnrow->SubQueueSection)) {

							$subquery_where = "AND mSubQueueCategory.SubQueueSection = '".$Columnrow->SubQueueSection."'";
						}

						$returnval = "(
							SELECT
								tSubQueueCategory.CategoryUID
							FROM
								tSubQueueCategory
							INNER JOIN mSubQueueCategory ON mSubQueueCategory.SubQueueCategoryUID = tSubQueueCategory.SubQueueCategoryUID
							AND mSubQueueCategory.WorkflowModuleUID = ".$Columnrow->WorkflowUID."
							WHERE
								tSubQueueCategory.OrderUID = tOrders.OrderUID
								".$subquery_where."
							LIMIT 1
						)";
					break;

					case 'SubQueueFirstInitiated':
						
						$returnval = "(
							SELECT
								mQueues.QueueName
							FROM
								mQueues
							JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID
							WHERE
								mQueues.WorkflowModuleUID = ".$Columnrow->QueueWorkflowUID."
							AND tOrderQueues.OrderUID = tOrders.OrderUID
							AND mQueues.CustomerUID = tOrders.CustomerUID
							ORDER BY tOrderQueues.RaisedDateTime ASC
							LIMIT 1
						)";

						break;

					case 'ReversedWorkflows':
							
							$returnval = "(
								SELECT
									GROUP_CONCAT(
										DISTINCT mWorkFlowModules.WorkflowModuleName SEPARATOR ', '
									)
								FROM
									tOrderReverse
								JOIN mWorkFlowModules ON mWorkFlowModules.WorkflowModuleUID = tOrderReverse.WorkflowModuleUID
								WHERE
									tOrderReverse.OrderUID = tOrders.OrderUID
								AND tOrderReverse.ReverseInitiatedWorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID
							)";

						break;

					case 'ReversedWorkflowsComments':
							
							$returnval = "(
								SELECT
									GROUP_CONCAT(
										CASE
										WHEN tOrderReverse.ReversedRemarks IS NOT NULL
										AND tOrderReverse.ReversedRemarks <> '' THEN
											CONCAT(
												mWorkFlowModules.WorkflowModuleName,
												' - ',
												tOrderReverse.ReversedRemarks
											)
										ELSE
											NULL
										END SEPARATOR ', '
									)
								FROM
									tOrderReverse
								JOIN mWorkFlowModules ON mWorkFlowModules.WorkflowModuleUID = tOrderReverse.WorkflowModuleUID
								WHERE
									tOrderReverse.OrderUID = tOrders.OrderUID
								AND tOrderReverse.ReverseInitiatedWorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID
							)";

						break;

					case 'ChecklistGroupIssueCount':

						if (empty($Columnrow->DocumentTypeUIDs)) {
							
							$returnval = NULL;
						}
							
						$returnval = "(
							SELECT
								(CASE WHEN COUNT(1) THEN COUNT(1) ELSE NULL END)
							FROM
								tDocumentCheckList
							LEFT JOIN mDocumentType ON mDocumentType.DocumentTypeUID = tDocumentCheckList.DocumentTypeUID
							WHERE
								tDocumentCheckList.OrderUID = tOrders.OrderUID
							AND tDocumentCheckList.WorkflowUID = tOrderWorkflows.WorkflowModuleUID
							AND (
								(
									tOrders.LoanType IS NULL
									OR tOrders.LoanType = ''
								)
								OR (mDocumentType.Groups = tOrders.LoanType)
							)
							AND tDocumentCheckList.DocumentTypeUID IN (".$Columnrow->DocumentTypeUIDs.")
							AND tDocumentCheckList.Answer = 'Problem Identified'
						)";

						break;

					case 'ChecklistIssueComments':

						$returnval = "( 
										SELECT
											GROUP_CONCAT(
												DISTINCT CASE
												WHEN tDocumentCheckList.Answer = 'Problem Identified' THEN
													tDocumentCheckList.Comments
												ELSE
													NULL
												END SEPARATOR ', '
											)
										FROM
											tOrders suborder
										JOIN tDocumentCheckList ON tDocumentCheckList.OrderUID = suborder.OrderUID
										JOIN mCustomerWorkflowModules ON mCustomerWorkflowModules.CustomerUID = suborder.CustomerUID
										AND mCustomerWorkflowModules.WorkflowModuleUID = tDocumentCheckList.WorkflowUID
										LEFT JOIN mDocumentType ON mDocumentType.DocumentTypeUID = tDocumentCheckList.DocumentTypeUID
										AND mDocumentType.Active = 1
										AND (
											suborder.LoanType IS NULL
											OR mDocumentType.Groups = suborder.LoanType
										)
										WHERE
											suborder.OrderUID = tOrders.OrderUID
										AND tDocumentCheckList.WorkflowUID = ".$Columnrow->WorkflowUID."
										AND (
											tDocumentCheckList.Comments IS NOT NULL
											AND tDocumentCheckList.Comments <> ''
										)
										AND (
											tDocumentCheckList.CategoryUID IS NULL
											OR mDocumentType.CategoryUID = mCustomerWorkflowModules.CategoryUID
										)
									)";

						break;

					default:
					break;
				}
			} else {

				$returnval = "( SELECT " . $Columnrow->ColumnName . " FROM tDocumentCheckList LEFT JOIN `mDocumentType` ON `mDocumentType`.`DocumentTypeUID` = `tDocumentCheckList`.`DocumentTypeUID` 
				WHERE `tDocumentCheckList`.`OrderUID` = `tOrders`.`OrderUID` AND `tDocumentCheckList`.`WorkflowUID` = " . $Columnrow->WorkflowUID . " AND `tDocumentCheckList`.`DocumentTypeUID` = " . $Columnrow->DocumentTypeUID . " LIMIT 1
			)";
		}

		return $returnval;
	}

	function DynamicColumnsCommonQuery($WorkflowModuleUID, $IgnoreQueues = FALSE,$post = [])
	{
		$Expired_Checklist_Enabled_Workflows = $this->config->item('Expired_Checklist_Enabled_Workflows');
		$Expired_Checklist = $this->config->item('Expired_Checklist');

		if ($WorkflowModuleUID != '') {
			$QueueColumns = $this->getWorkflowQueuesColumns($WorkflowModuleUID);
		} else if(isset($post['Section']) && !empty($post['Section'])) {
			$QueueColumns = $this->Common_Model->getSectionQueuesColumns($post['Section']);
		} else {
			$QueueColumns = $this->Common_Model->getSectionQueuesColumns('Funded');
		}

		foreach ($QueueColumns as $key => $value) {

			$columnquery = $this->get_dynamiccolumnquery_bycolumname($value);
			$explodedarray = explode(".", $value->ColumnName);
			$column_name = end($explodedarray);

			if (!empty($value->IsChecklist)) {
				if (!empty($columnquery)) {
					$this->db->select($columnquery . " AS checklist_" . $value->DocumentTypeUID . $column_name);
				} else {
					$this->db->select('"" AS checklist_' . $value->DocumentTypeUID . $column_name, FALSE);
				}
			} else {

				switch ($value->ColumnName) {

					case 'ProblemIdentified-Checklists':

						if (!empty($columnquery)) {
							$this->db->select($columnquery . " AS ProblemIdentifiedChecklists");
						} else {
							$this->db->select('"" AS ProblemIdentifiedChecklists', FALSE);
						}

						break;

					case 'LCREQUIRED':

						if (!empty($columnquery)) {
							$this->db->select($columnquery . " AS LCREQUIRED");
						} else {
							$this->db->select('"" AS LCREQUIRED', FALSE);
						}

						break;

					case 'WorkflowQueue':



						if (!empty($value->QueueWorkflowUID)) {
							$this->db->select($columnquery . " AS WorkflowQueue" . $value->QueueWorkflowUID,false);

							$workflowchecklist = isset($this->config->item('Expired_Checklist')[$value->QueueWorkflowUID]) ? $this->config->item('Expired_Checklist')[$value->QueueWorkflowUID] : NULL;

							//checklist other workflow joins to check document expiry date
							if( $workflowchecklist && !empty($value->QueueWorkflowUID)) {
								
								if(is_array($workflowchecklist) && $value->QueueWorkflowUID != $WorkflowModuleUID && in_array($value->QueueWorkflowUID, $Expired_Checklist_Enabled_Workflows)) {

									foreach ($workflowchecklist as $checklistkey => $checklistuid) {

										$this->db->select("(SELECT TDC_". $checklistuid.".DocumentExpiryDate FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$value->QueueWorkflowUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentExpiryDate IS NOT NULL AND TDC_". $checklistuid.".DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL ".(($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) ? '65' : '10')." DAY))) AS Expired_Checklist_DocumentExpiryDate_". $checklistuid,false);

										if ($value->QueueWorkflowUID == $this->config->item('Workflows')['HOI']) {
											
											$this->db->select("(SELECT TDC_". $checklistuid.".DocumentDate FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$value->QueueWorkflowUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentDate IS NOT NULL AND TDC_". $checklistuid.".DocumentDate <> '' AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY))) AS Expired_Checklist_DocumentDate_". $checklistuid,false);
										} else {

											$this->db->select("(SELECT TDC_". $checklistuid.".DocumentDate FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$value->QueueWorkflowUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentExpiryDate IS NOT NULL AND TDC_". $checklistuid.".DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY))) AS Expired_Checklist_DocumentDate_". $checklistuid,false);
										}

										$this->db->select("(SELECT MDOC_". $checklistuid.".DocumentTypeName FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$value->QueueWorkflowUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentExpiryDate IS NOT NULL AND TDC_". $checklistuid.".DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL ".(($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) ? '65' : '10')." DAY))) AS Expired_Checklist_DocumentType". $checklistuid,false);


									}
								}

							}

						} else {
							$this->db->select('"" AS WorkflowQueue' . $value->QueueWorkflowUID, FALSE);
						}

						break;

					case 'Order-Priority':

						//get_priorityreportfields
				
						$columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);
						$Customer_PriorityWorkflows = $this->Priority_Report_model->get_priorityreportworkflows($this->parameters['DefaultClientUID']);

						if(!empty($columnquery)) {

							$this->db->select($columnquery." AS Priority",FALSE);
						} else {

							$this->db->select('"" AS Priority',FALSE);
						}

						foreach ($Customer_PriorityWorkflows as $PriorityWorkflowrow) {


							$this->db->join("tOrderWorkflows AS " .  "TWPR_" . $PriorityWorkflowrow->SystemName,   "TWPR_" . $PriorityWorkflowrow->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TWPR_" . $PriorityWorkflowrow->SystemName . ".WorkflowModuleUID = " . $PriorityWorkflowrow->WorkflowModuleUID, "LEFT");

							$this->db->join("tOrderAssignments AS " . "TOAPR_" . $PriorityWorkflowrow->SystemName,  "TOAPR_" . $PriorityWorkflowrow->SystemName . ".OrderUID = tOrders.OrderUID AND TOAPR_" . $PriorityWorkflowrow->SystemName . ".WorkflowModuleUID = " . $PriorityWorkflowrow->WorkflowModuleUID, "LEFT");
						}


		
						break;

						case 'Logic-Aging':
						break;

						case 'KICKBACKAGING':

						if (!empty($value->QueueWorkflowUID)) {

							$this->db->select("(SELECT ReversedDateTime FROM tOrderWorkflows WHERE tOrderWorkflows.WorkflowModuleUID = ".$value->QueueWorkflowUID." AND tOrderWorkflows.OrderUID = tOrders.OrderUID LIMIT 1) AS KICKBACKAGING".$value->QueueWorkflowUID,false);

						} else {
							$this->db->select('"" AS KICKBACKAGING' . $value->QueueWorkflowUID, FALSE);

						}

						break;

					case 'Order-Comments':
						break;

					case 'Workflow-Comments':
						$this->db->select("tOrderComments.Description AS Description",FALSE);
						$this->db->join('tOrderComments', 'tOrderComments.OrderUID = tOrders.OrderUID AND tOrderComments.WorkflowUID = ' . $WorkflowModuleUID, 'LEFT');
						break;

					case 'LOGIC-LCREQUIRED':
						break;

					case 'LOGIC-STC':
					break;

					case 'WorkflowCompletedDate':


					$columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);

					if (!empty($columnquery)) {
						$this->db->select($columnquery . ' AS WorkflowCompletedDate'.$value->QueueWorkflowUID);
					} else {
						$this->db->select('"" AS WorkflowCompletedDate' . $value->QueueWorkflowUID, FALSE);
					}

					break;

					case 'WorkflowCompletedAssociate':

					$columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);

					if (!empty($columnquery)) {
						$this->db->select($columnquery . ' AS WorkflowCompletedAssociate'.$value->QueueWorkflowUID);
					} else {
						$this->db->select('"" AS WorkflowCompletedAssociate' . $value->QueueWorkflowUID, FALSE);
					}


					break;


					case ($value->ColumnName == 'KickbackAssociate' && !empty($value->QueueWorkflowUID)):

					$columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);

					if (!empty($columnquery)) {
						$this->db->select($columnquery . ' AS KickbackAssociate'.$value->QueueWorkflowUID);
					} else {
						$this->db->select('"" AS KickbackAssociate' . $value->QueueWorkflowUID, FALSE);
					}

					break;

					case ($value->ColumnName == 'KickbackRemarks' && !empty($value->QueueWorkflowUID)):

					$columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);

					if (!empty($columnquery)) {
						$this->db->select($columnquery . ' AS KickbackRemarks'.$value->QueueWorkflowUID);
					} else {
						$this->db->select('"" AS KickbackRemarks' . $value->QueueWorkflowUID, FALSE);
					}

					break;

					case ($value->ColumnName == 'KickbackDate' && !empty($value->QueueWorkflowUID)):

					$columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);

					if (!empty($columnquery)) {
						$this->db->select($columnquery . ' AS KickbackDate'.$value->QueueWorkflowUID);
					} else {
						$this->db->select('"" AS KickbackDate' . $value->QueueWorkflowUID, FALSE);
					}

					break;


					case ($value->ColumnName == 'KICKBACKAGING' && !empty($value->QueueWorkflowUID)):


					$columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);

					if (!empty($columnquery)) {
						$this->db->select($columnquery . ' AS KICKBACKAGING'.$value->QueueWorkflowUID);
					} else {
						$this->db->select('"" AS KICKBACKAGING' . $value->QueueWorkflowUID, FALSE);
					}

					break;

					case $value->ColumnName == 'BorrowerNames':
					 //$this->db->select("GROUP_CONCAT(DISTINCT CONCAT_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName)) AS BorrowerNames",FALSE);
					 $this->db->select("(SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(' ',tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.BorrowerLastName)) FROM tOrderPropertyRole WHERE tOrderPropertyRole.OrderUID = tOrders.OrderUID LIMIT 1) AS BorrowerNames",FALSE);

					 break;

					 case ($value->ColumnName == 'PriorityIssueChecklists'):

					 $columnquery = $this->Common_Model->get_dynamiccolumnquery_bycolumname($value);

					 $this->db->select($columnquery." AS ".$value->ColumnName);
					 break;

					 // Workflow and queue Issue Checklists
					 case ($value->ColumnName == 'WorkflowIssueChecklists'):

					 	$WorkflowIssueChecklists = $this->getWorkflowIssueChecklists_variable($value);

					 	$this->db->select($columnquery." AS ".$WorkflowIssueChecklists);					 	

					 break;

					case $value->ColumnName == 'ReWorkCompletedDateTime':
					 	$this->db->select("(SELECT CompletedDateTime FROM tOrderReWork WHERE OrderReWorkUID = (SELECT MAX(OrderReWorkUID) FROM tOrderReWork WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND IsReWorkEnabled = ".STATUS_ZERO." LIMIT 1)) AS ReWorkCompletedDateTime",FALSE);

					 break;

					case $value->ColumnName == 'ReWorkCompletedBy':
					 	$this->db->select("(SELECT ReWorkAssociate.UserName FROM tOrderReWork LEFT JOIN mUsers ReWorkAssociate ON ReWorkAssociate.UserUID = tOrderReWork.CompletedByUserUID WHERE OrderReWorkUID = (SELECT MAX(OrderReWorkUID) FROM tOrderReWork WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND IsReWorkEnabled = ".STATUS_ZERO." LIMIT 1)) AS ReWorkCompletedBy",FALSE);

					 break;

					 case 'SubQueueCategories':

						 if (!empty($columnquery)) {
						 	
						 	if (!empty($value->SubQueueUID)) {

								$this->db->select($columnquery . ' AS SubQueueCategories_'.$value->SubQueueUID);
							} elseif (!empty($value->SubQueueSection)) {

								$this->db->select($columnquery . ' AS SubQueueCategories_'.$value->SubQueueSection);
							} else {

								$this->db->select($columnquery . ' AS SubQueueCategories');
							}
						 } else {
						 	$this->db->select('"" AS SubQueueCategories', FALSE);
						 }

					 break;

					case 'SubQueueFirstInitiated':

						$this->db->select($columnquery." AS ".$value->ColumnName."_".$value->QueueWorkflowUID);

						break;

					case 'ReversedWorkflows':

						$this->db->select($columnquery." AS ".$value->ColumnName);

						break;

					case 'ReversedWorkflowsComments':

						$this->db->select($columnquery." AS ".$value->ColumnName);

						break;

					case 'ChecklistGroupIssueCount':

						$this->db->select($columnquery." AS ".$value->ColumnName."_".$value->QueueColumnUID);

						break;

					case 'ChecklistIssueComments':

						$this->db->select($columnquery." AS ".$value->ColumnName);

						break;

					case 'ExceptionSubQueueAging':

						break;

					case 'KickBackSubQueueAging':

						break;

					case 'ExpirySubQueueAging':

						break;

					case 'CategoryTAT':

						break;

					case 'DocsOutAging':

						break;

					case 'DocsOutAgingHours':

						break;

					case 'AgingInHours':

						break;
			
					default:
						$this->db->select($value->ColumnName . " AS " . $column_name,FALSE);
						break;
				}

			}
		}

		//checklist workflow joins to check document expiry date

		$workflowchecklist = isset($this->config->item('Expired_Checklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$WorkflowModuleUID] : NULL;
		if(isset($workflowchecklist) && $workflowchecklist && !empty($WorkflowModuleUID) && in_array($value->QueueWorkflowUID, $Expired_Checklist_Enabled_Workflows)) {

			if(is_array($workflowchecklist)) {

				foreach ($workflowchecklist as $checklistkey => $checklistuid) {

					$this->db->select("(SELECT TDC_". $checklistuid.".DocumentExpiryDate FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$WorkflowModuleUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentExpiryDate IS NOT NULL AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL ".(($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) ? '65' : '10')." DAY))) AS Expired_Checklist_DocumentExpiryDate_". $checklistuid,false);

					if ($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {
						
						$this->db->select("(SELECT TDC_". $checklistuid.".DocumentDate FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$WorkflowModuleUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentDate IS NOT NULL AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY))) AS Expired_Checklist_DocumentDate_". $checklistuid,false);
					} else {

						$this->db->select("(SELECT TDC_". $checklistuid.".DocumentDate FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$WorkflowModuleUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentExpiryDate IS NOT NULL AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY))) AS Expired_Checklist_DocumentDate_". $checklistuid,false);
					}

					$this->db->select("(SELECT MDOC_". $checklistuid.".DocumentTypeName FROM tDocumentCheckList AS " .  "TDC_" . $checklistuid ." JOIN mDocumentType AS " .  "MDOC_" . $checklistuid.   " ON MDOC_" . $checklistuid.".DocumentTypeUID = TDC_". $checklistuid.".DocumentTypeUID WHERE TDC_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDC_" . $checklistuid.".WorkflowUID = ".$WorkflowModuleUID. " AND TDC_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' AND TDC_". $checklistuid.".DocumentExpiryDate IS NOT NULL AND DATE(STR_TO_DATE(TDC_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL ".(($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) ? '65' : '10')." DAY))) AS Expired_Checklist_DocumentType". $checklistuid,false);



				}
			}

		}

		// Highlight expiry orders based on duration setup in client setup
		if ($IgnoreQueues == FALSE) {
			$this->db->select('(
				CASE WHEN 
					(
					mCustomerWorkflowModules.OrderHighlightDuration != ""
					OR mCustomerWorkflowModules.OrderHighlightDuration IS NOT NULL
					)
					AND (tOrderAssignments.WorkflowStatus != 5 OR tOrderAssignments.WorkflowStatus IS NULL)
					AND (NOW() < tOrderWorkflows.DueDateTime)
				THEN
					CASE WHEN
						TIMESTAMPDIFF(hour ,NOW(), tOrderWorkflows.DueDateTime) < mCustomerWorkflowModules.OrderHighlightDuration
					THEN
						1
					ELSE
						0
					END
				ELSE
					0
				END
			) as ExpiryOrderInDuration');

			$this->db->select('(
				CASE 
				WHEN 
					mCustomer.HighlightExpiryOrders = 1
					AND (tOrderAssignments.WorkflowStatus != 5 OR tOrderAssignments.WorkflowStatus IS NULL)
				THEN
					CASE
					WHEN
					DATEDIFF(now(), tOrderWorkflows.DueDateTime) > 0
					THEN
					1
					ELSE
					0
					END
				ELSE
				0
				END
			) as ExpiryOrders');
		}

		// Order Esclation table select
		$this->db->select("
	      (CASE WHEN
	              EXISTS(SELECT * FROM tOrderHighlights WHERE tOrderHighlights.OrderUID = tOrders.OrderUID AND tOrderHighlights.IsCleared = 0)
	            THEN
	              1
	            ELSE
	              0
	            END) AS EsclationRaised, tOrderHighlights.HighlightUID
	    ");

		// Order Esclation table select, join
		$this->db->join('tOrderHighlights', 'tOrders.OrderUID = tOrderHighlights.OrderUID AND tOrderHighlights.IsCleared = 0', 'left');


		//NBS REQUIRED WORKFLOWS SELECTED
		if(in_array($WorkflowModuleUID, $this->config->item('NBSREQUIRED_WORKFLOWS'))) {

			$this->db->select("(SELECT mStateMatrix.NBSRequired FROM mStateMatrix JOIN mStates ON mStates.StateUID = mStateMatrix.StateUID WHERE tOrders.PropertyStateCode = mStates.StateCode) AS NBSRequired",NULL,FALSE);
		}	

	}

	function isworkflow_forceenabled($OrderUID, $WorkflowModuleUID)
	{
		$torderworkflowrow = $this->db->where(array('OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID))->get('tOrderWorkflows')->row();

		if (isset($torderworkflowrow->IsForceEnabled) && $torderworkflowrow->IsForceEnabled == 1) {
			return true;
		}

		return false;
	}

	function GetFundedQueueOrders()
	{
		$FundedOrders_Milestones = $this->config->item('FundedOrders_Milestones');

		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderImport', 'tOrders.OrderUID=tOrderImport.OrderUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrders.OrderUID = tOrderWorkflows.OrderUID', 'left');
		$this->db->where_in('tOrders.MilestoneUID', $FundedOrders_Milestones);
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->group_by('tOrders.OrderUID');
	}
	function GetExpiredQueueOrders()
	{


		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderImport', 'tOrders.OrderUID=tOrderImport.OrderUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrders.OrderUID = tOrderWorkflows.OrderUID', 'left');

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->where('tOrderWorkflows.DueDateTime', NULL, FALSE);

		$this->db->group_by('tOrders.OrderUID');
	}

	function getSectionQueuesColumns($controller, $nosort = false)
	{
		/*Query*/
		$this->db->select('*');
		$this->db->from('mQueueColumns');
		$this->db->join('mFields', 'mFields.FieldUID = mQueueColumns.FieldUID', 'left');
		$this->db->where('(WorkflowUID IS NULL OR WorkflowUID = "")', NULL, FALSE);
		$this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);
		$this->db->where('Section', $controller);
		if ($nosort) {
			$this->db->where('NoSort <> 1', NULL, FALSE);
		}
		$this->db->order_by('Position');
		return $this->db->get()->result();
	}

	function GetCanceledQueueOrders()
	{
		$status[0] = $this->config->item('keywords')['Cancelled'];
		$CancelledOrders_Milestones = $this->config->item('CancelledOrders_Milestones');

		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderImport', 'tOrders.OrderUID=tOrderImport.OrderUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrders.OrderUID = tOrderWorkflows.OrderUID', 'left');

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->group_start();
		$this->db->where_in('tOrders.StatusUID', $status);
		$this->db->or_where_in('tOrders.MilestoneUID', $CancelledOrders_Milestones);
		$this->db->group_end();

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
				//$this->db->order_by('tOrderWorkflows.DueDateTime', 'ASC');
			} 
		}
	}


	function is_followup_enabledforworkflow($WorkflowModuleUID)
	{
		$this->db->select('*');
		$this->db->from('mCustomerWorkflowModules');
		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mCustomerWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('IsFollowUp', STATUS_ONE);
		return $this->db->get()->num_rows();
	}

	function get_followupcounts($QueueUID)
	{
		if (!empty($QueueUID)) {

			$Queue = $this->Common_Model->get_row('mQueues', ['QueueUID' => $QueueUID]);

			$followupcount = $followupduetodaycount = $followupduepastcount = 0;

			if (!empty($Queue)) {
				$post['advancedsearch']['Followup'] = 'true';
				$post['QueueUID'] = $QueueUID;

				if (!empty($QueueUID)) {
					$followupcount = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'count_filter');
				}

				$post['advancedsearch']['Followup'] = 'false';
				$post['advancedsearch']['Followupduetoday'] = 'true';

				if (!empty($QueueUID)) {
					$followupduetodaycount = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'count_filter');
				}

				$post['advancedsearch']['Followupduetoday'] = 'false';
				$post['advancedsearch']['Followupduepast'] = 'true';
				if (!empty($QueueUID)) {
					$followupduepastcount = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, 'count_filter');
				}

				$data = array('followupcount' => $followupcount, 'followupduetodaycount' => $followupduetodaycount, 'followupduepastcount' => $followupduepastcount);
				return $data;
			}
		}

		$data = array('followupcount' => 0, 'followupduetodaycount' => 0, 'followupduepastcount' => 0);
		return $data;
	}

	// ICD Workflow Orders Begin
	function GetICDOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['ICD'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['ICD']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["ICD"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['ICD'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['ICD'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['ICD']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['ICD'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['ICD'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}

	// ICD Workflow Orders End
	function GetAssignUserOption()
	{
		
		$this->otherdb->select('mUsers.UserUID, mUsers.UserName, mUsers.LoginID');
		$this->otherdb->from('mUsers');
		$this->otherdb->where('CustomerUID', $this->parameters['DefaultClientUID']);
		$this->otherdb->where('mUsers.Active',STATUS_ONE);

		$list = $this->otherdb->get()->result();

		$option = '<select class="select2picker form-control AssignedToUserUID" name="AssignedToUserUID" required><option value="">Select User</option>';
		foreach ($list as $key => $value) {
			$option .= '<option value="' . $value->UserUID . '">' . $value->UserName . ' ('.$value->LoginID.')</option>';
		}
		$option .= '</select>';

		return $option;
	}

	// Disclosures Workflow Orders Begin
	function GetDisclosuresOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Disclosures'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Disclosures']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Disclosures"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['Disclosures'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['Disclosures'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Disclosures']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Disclosures'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Disclosures'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// Disclosures Workflow Orders End

	// NTB Workflow Orders Begin
	function GetNTBOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['NTB'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['NTB']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["NTB"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['NTB'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['NTB'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['NTB']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['NTB'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['NTB'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// NTB Workflow Orders End

	// FloodCert Workflow Orders Begin
	function GetFloodCertOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['FloodCert'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['FloodCert']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["FloodCert"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['FloodCert'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['FloodCert'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['FloodCert']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['FloodCert'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['FloodCert'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// FloodCert Workflow Orders End

	// Appraisal Workflow Orders Begin
	function GetAppraisalOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Appraisal'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Appraisal']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Appraisal"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['Appraisal'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['Appraisal'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Appraisal']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Appraisal'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Appraisal'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// Appraisal Workflow Orders End

	// Escrows Workflow Orders Begin
	function GetEscrowsOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Escrows'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Escrows']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Escrows"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['Escrows'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['Escrows'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Escrows']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Escrows'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Escrows'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// Escrows Workflow Orders End

	// TwelveDayLetter Workflow Orders Begin
	function GetTwelveDayLetterOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['TwelveDayLetter'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['TwelveDayLetter']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["TwelveDayLetter"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['TwelveDayLetter'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['TwelveDayLetter'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['TwelveDayLetter']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['TwelveDayLetter'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['TwelveDayLetter'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// TwelveDayLetter Workflow Orders End

	// MaxLoan Workflow Orders Begin
	function GetMaxLoanOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['MaxLoan'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['MaxLoan']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["MaxLoan"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['MaxLoan'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['MaxLoan'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['MaxLoan']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['MaxLoan'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['MaxLoan'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// MaxLoan Workflow Orders End

	// POO Workflow Orders Begin
	function GetPOOOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['POO'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['POO']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["POO"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['POO'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['POO'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['POO']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['POO'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['POO'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// POO Workflow Orders End

	// CondoQR Workflow Orders Begin
	function GetCondoQROrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['CondoQR'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['CondoQR']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["CondoQR"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['CondoQR'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['CondoQR'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['CondoQR']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['CondoQR'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['CondoQR'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// CondoQR Workflow Orders End

	// FHACaseAssignment Workflow Orders Begin
	function GetFHACaseAssignmentOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['FHACaseAssignment'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['FHACaseAssignment']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["FHACaseAssignment"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['FHACaseAssignment'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['FHACaseAssignment'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['FHACaseAssignment']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['FHACaseAssignment'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['FHACaseAssignment'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// FHACaseAssignment Workflow Orders End
	function DbTables()
	{
		$dbname = $this->db->database;
		$tables = $this->db->query("SHOW TABLES FROM `" . $dbname . "`")->result_array();

		return $tables;
	}
	function TableKeys($table)
	{
		$dbname = $this->db->database;
		$tKeys = $this->db->list_fields($table);
		// echo '<pre>';print_r($tKeys);exit;

		if ($tKeys) {
			return $tKeys;
		} else {
			return false;
		}
	}

	// VACaseAssignment Workflow Orders Begin
	function GetVACaseAssignmentOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['VACaseAssignment'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['VACaseAssignment']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["VACaseAssignment"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['VACaseAssignment'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['VACaseAssignment'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['VACaseAssignment']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['VACaseAssignment'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['VACaseAssignment'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// VACaseAssignment Workflow Orders End

	// VVOE Workflow Orders Begin
	function GetVVOEOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['VVOE'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['VVOE']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["VVOE"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['VVOE'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['VVOE'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['VVOE']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['VVOE'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['VVOE'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// VVOE Workflow Orders End

	// CEMA Workflow Orders Begin
	function GetCEMAOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['CEMA'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['CEMA']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["CEMA"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['CEMA'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['CEMA'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['CEMA']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['CEMA'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['CEMA'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// CEMA Workflow Orders End

	// SCAP Workflow Orders Begin
	function GetSCAPOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['SCAP'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['SCAP']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
			CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
			TRUE 
			ELSE FALSE END
			ELSE FALSE END
			ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["SCAP"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['SCAP'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['SCAP'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['SCAP']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['SCAP'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
		CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['SCAP'] . "' 
		THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
		ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// SCAP Workflow Orders End
	/**
	 *Function child checklists 
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Friday 12 June 2020.
	 */

	function get_childchecklists($ClientUID, $WorkflowModuleUID, $ParentDocumentTypeUID, $groups)
	{
		$this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType', 'mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID AND mDocumentType.CategoryUID IS NOT NULL AND mCustomerWorkflowModules.CategoryUID IS NOT NULL');
		if ($groups && $groups != 'FHA/VA') {
			$this->db->where('(mDocumentType.Groups IS NULL OR mDocumentType.Groups ="' . $groups . '")');
		}
		$this->db->where(array('mCustomerWorkflowModules.CustomerUID' => $ClientUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'mDocumentType.CustomerUID' => $ClientUID, 'mDocumentType.Active' => '1', 'mDocumentType.ParentDocumentTypeUID' => $ParentDocumentTypeUID));
		$this->db->order_by('mDocumentType.Position', 'ASC');
		return $this->db->get()->result();
	}

	function get_fhachildchecklist($ClientUID, $WorkflowModuleUID, $ParentDocumentTypeUID, $groups)
	{
		$this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType', 'mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID');
		$this->db->where('mDocumentType.Groups IS NOT NULL ');
		if ($groups && $groups != 'FHA/VA') {
			$this->db->where('mDocumentType.Groups', $groups);
		}
		$this->db->where(array('mCustomerWorkflowModules.CustomerUID' => $ClientUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'mDocumentType.CustomerUID' => $ClientUID, 'mDocumentType.Active' => '1', 'mDocumentType.ParentDocumentTypeUID' => $ParentDocumentTypeUID));
		$this->db->order_by('mDocumentType.Position', 'ASC');
		return $this->db->get()->result();
	}

	// NLR Workflow Orders Begin
	function GetNLROrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['NLR'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['NLR']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["NLR"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['NLR'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['NLR'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['NLR']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['NLR'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['NLR'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// NLR Workflow Orders End

	function getHtmlSource($table_name, $table_key)
	{
		$this->db->select($table_key)->from($table_name);
		$keys = $this->db->get();
		if ($keys->num_rows() > 0) {
			return $keys->result_array();
		} else {
			return false;
		}
	}

	// CTCFlipQC Workflow Orders Begin
	function GetCTCFlipQCOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['CTCFlipQC'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['CTCFlipQC']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["CTCFlipQC"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['CTCFlipQC'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['CTCFlipQC'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['CTCFlipQC']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['CTCFlipQC'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['CTCFlipQC'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// CTCFlipQC Workflow Orders End

	// PrefundAuditCorrection Workflow Orders Begin
	function GetPrefundAuditCorrectionOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['PrefundAuditCorrection'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['PrefundAuditCorrection']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["PrefundAuditCorrection"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['PrefundAuditCorrection'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['PrefundAuditCorrection'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['PrefundAuditCorrection']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['PrefundAuditCorrection'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['PrefundAuditCorrection'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// PrefundAuditCorrection Workflow Orders End

	// AdhocTasks Workflow Orders Begin
	function GetAdhocTasksOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['AdhocTasks'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['AdhocTasks']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["AdhocTasks"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['AdhocTasks'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');


		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['AdhocTasks'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['AdhocTasks']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['AdhocTasks'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['AdhocTasks'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// AdhocTasks Workflow Orders End

	// UWClear Workflow Orders Begin
	function GetUWClearOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['UWClear'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['UWClear']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["UWClear"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['UWClear'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['UWClear'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['UWClear']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['UWClear'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['UWClear'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// UWClear Workflow Orders End

	// TitleReview Workflow Orders Begin
	function GetTitleReviewOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['TitleReview'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['TitleReview']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["TitleReview"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['TitleReview'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['TitleReview'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['TitleReview']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['TitleReview'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['TitleReview'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// TitleReview Workflow Orders End

	//getPriorityReportColumn start
	function getPriorityReportColumn()
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$this->db->select('*');
		$this->db->from('mPriorityReportColumns');
		$this->db->join('mFields', 'mFields.FieldUID = mPriorityReportColumns.FieldUID', 'left');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mPriorityReportColumns.WorkflowUID', 'left');
		$this->db->where('mPriorityReportColumns.CustomerUID', $CustomerUID);
		$this->db->order_by('Position', 'ASC');
		return $this->db->get()->result();
	}
	//getPriorityReportColumn end

	function getAllOrderDetails($Nstatus = false)
	{
		$this->db->select('tOrders.*, mCustomer.*, mStatus.StatusName, mProjectCustomer.ProjectName,mProducts.ProductName, tOrders.PriorityUID, mOrderPriority.PriorityName, tOrderImport.NSMServicingLoanNumber');
		$this->db->from('tOrders');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mStatus', 'mStatus.StatusUID = tOrders.StatusUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'mProducts.ProductUID = tOrders.ProductUID', 'left');
		$this->db->join('mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		if ($Nstatus) {
			$this->db->where_not_in('tOrders.StatusUID', $Nstatus);
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	function getHoiDocumenttype($OrderUID, $WorkflowModuleUID, $checklist = false)
	{

		$this->db->select('*')->from('tDocumentCheckList')->join('mDocumentType', 'mDocumentType.DocumentTypeUID=tDocumentCheckList.DocumentTypeUID');
		$this->db->where('tDocumentCheckList.OrderUID', $OrderUID);
		if ($checklist) {
			$this->db->where('mDocumentType.DocumentTypeUID', $checklist);
		}
		$this->db->where('tDocumentCheckList.WorkflowUID', $WorkflowModuleUID);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}
	}
	function getMailContent($eid)
	{
		$query = $this->db->select('*')->from('mEmailTemplate')->where('EmailTemplateUID', $eid)->get();
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			$query = $this->db->select('*')->from('mEmailTemplate')->get();
			return $query->row();
		}
	}

	function GetStateDetails()
	{
		$this->db->select('StateUID,StateCode');
		$this->db->from('mStates');
		$this->db->where('Active', 1);
		return $this->db->get()->result();
	}

	function GetLoanTypeDetails($CustomerUID = FALSE)
	{
		$this->db->select('LoanTypeName,LoanTypeUID,LockExpiration');
		$this->db->from('mLoanType');
		if ($CustomerUID) {
			$this->db->where(array('Active' => 1, 'ClientUID' => $CustomerUID));
		} else {
			$this->db->where(array('Active' => 1, 'ClientUID' => $this->session->userdata('DefaultClientUID')));
		}
		return $this->db->get()->result();
	}

	function get_mreasons()
	{
		$this->db->select('*');
		$this->db->from('mReasons');
		$this->db->where('Active', 1);
		$this->db->where('(QueueUID IS NULL OR QueueUID = "")', NULL, FALSE);
		$this->db->order_by('ReasonName', 'ASC');
		return $this->db->get()->result();
	}

	function get_mqueuesmreasons($QueueUID = FALSE)
	{
		$this->db->select('*');
		$this->db->from('mReasons');
		$this->db->where('Active', 1);
		$this->db->where('( QueueUID <> "" OR StaticQueueUID <> "")', NULL, FALSE);
		if ($QueueUID) {
			$this->db->where('QueueUID', $QueueUID);
		}
		$this->db->order_by('ReasonName', 'ASC');
		return $this->db->get()->result();
	}

	function get_settings_value($FieldName)
	{
		$this->db->select('SettingValue');
		$this->db->from('mSettings');
		$this->db->where('SettingField', $FieldName);
		$this->db->where('Active', 1);
		return $this->db->get()->row();
	}
	function GetLoanDocuments($OrderUID)
	{
		$this->db->select('*,mUsers.UserName as UploadedUser');
		$this->db->from('tDocuments');
		$this->db->join('mUsers', 'tDocuments.UploadedByUserUID = mUsers.UserUID', 'left');
		$this->db->where(array('tDocuments.OrderUID' => $OrderUID));
		$this->db->group_by('tDocuments.DocumentUID');
		$query = $this->db->get();
		return $query->result();
	}

	function GetcustomerWorkflowDetails($CustomerUID = FALSE)
	{
		$this->db->select('mWorkFlowModules.*');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID', 'left');
		if ($CustomerUID) {
			$this->db->where('CustomerUID', $CustomerUID);
		} else {
			$this->db->where('CustomerUID', $this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->result();
	}
	function pluckSettingValue($FieldName)
	{
		$this->db->select('SettingValue');
		$this->db->from('mSettings');
		$this->db->where('SettingField', $FieldName);
		// $this->db->where('Active',1);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row()->SettingValue;
		} else {
			return '';
		}
	}
	// BorrowerDocs Workflow Orders Begin
	function GetBorrowerDocsOrders($filterexception = true)
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['BorrowerDocs'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['BorrowerDocs']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["BorrowerDocs"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['BorrowerDocs'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['BorrowerDocs'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['BorrowerDocs']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['BorrowerDocs'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['BorrowerDocs'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		$this->db->group_by('tOrders.OrderUID');
	}
	// BorrowerDocs Workflow Orders End
	function getCompanyDetails($name)
	{
		// $this->db->select('*')->from('mCompanyDetails')->like('CompanyName',$name)->get()
		$query = $this->db->query("SELECT * FROM `mCompanyDetails` WHERE mCompanyDetails.CompanyName  LIKE '$name%'");
		return $query->row();
	}

	public function CreateDirectoryToPath($Path = '')
	{
		if (empty($Path)) {
			die('No Path to create directory');
		}
		if (!file_exists($Path)) {
			if (!mkdir($Path, 0777, true)) die('Unable to create directory');
		}
		chmod($Path, 0777);
		return true;
	}


	// date filter for queue tab level start
	function DateFilterFromDate($post)
	{
		$FromDate = $post['advancedsearch']['FromDate'];
		$DateFilter = $post['advancedsearch']['DateFilter'];
		if ($DateFilter == 'Assigned') {
			$this->db->where('DATE(tOrderAssignments.AssignedDatetime) >= "' . date('Y-m-d', strtotime($FromDate)) . '"', NULL, false);
		} else if ($DateFilter == 'Completed') {
			$this->db->where('DATE(tOrderAssignments.CompleteDateTime) >= "' . date('Y-m-d', strtotime($FromDate)) . '"', NULL, false);
		} else if (!empty($post['advancedsearch']['QueueUID'])) {
			$this->db->where('DATE(tOrderQueues.RaisedDateTime) >= "' . date('Y-m-d', strtotime($FromDate)) . '"', NULL, false);
		} else {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) >= "' . date('Y-m-d', strtotime($FromDate)) . '"', NULL, false);
		}
	}

	function DateFilterToDate($post)
	{
		$ToDate = $post['advancedsearch']['ToDate'];
		$DateFilter = $post['advancedsearch']['DateFilter'];

		if ($DateFilter == 'Assigned') {
			$this->db->where('DATE(tOrderAssignments.AssignedDatetime) <= "' . date('Y-m-d', strtotime($ToDate)) . '"', NULL, false);
		} else if ($DateFilter == 'Completed') {
			$this->db->where('DATE(tOrderAssignments.CompleteDateTime) <= "' . date('Y-m-d', strtotime($ToDate)) . '"', NULL, false);
		} else if (!empty($post['advancedsearch']['QueueUID'])) {
			$this->db->where('DATE(tOrderQueues.RaisedDateTime) <= "' . date('Y-m-d', strtotime($ToDate)) . '"', NULL, false);
		} else {
			$this->db->where('DATE(tOrders.OrderEntryDateTime) <= "' . date('Y-m-d', strtotime($ToDate)) . '"', NULL, false);
		}
	}
	// date filter for queue tab level end

	function GetMilestoneDetails($CustomerUID = FALSE)
	{
		$this->db->select('*');
		$this->db->from('mMilestone');
		$this->db->where('Active', 1);
		if (!empty($CustomerUID)) {
			$this->db->where('CustomerUID', $CustomerUID);
		} else {
			$this->db->where('CustomerUID', $this->session->userdata('DefaultClientUID'));
		}
		return $this->db->get()->result();
	}

	// GateKeeping Workflow Orders Begin
	function GetGateKeepingOrders($filterexception = true, $Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['GateKeeping'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/
		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['GateKeeping']);

		if (!empty($DependentWorkflowModuleUID)) {
			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('TW_GateKeeping.OrderUID')->from('tOrderWorkflows TW_GateKeeping');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = TW_GateKeeping.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = TW_GateKeeping.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "
				CASE WHEN TW_GateKeeping.IsForceEnabled = 0 THEN
				CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND (TW_" . $value->SystemName . ".IsPresent = 1) THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where('TW_GateKeeping.OrderUID = tOrderWorkflows.OrderUID AND TW_GateKeeping.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID ', NULL,FALSE);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');


		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = ' . $this->config->item("Workflows")["GateKeeping"], 'left');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');

		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');

		// For Fileter Assignment based on workflow
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		$this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = " . $this->config->item('Workflows')['GateKeeping']);

		/*Check Doc Case Enabled*/
		$this->db->join('tOrderDocChase', 'tOrders.OrderUID = tOrderDocChase.OrderUID AND tOrderDocChase.WorkflowModuleUID = ' . $this->config->item("Workflows")["GateKeeping"], 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');

		//join followup


		$this->db->where('CASE WHEN tOrderDocChase.IsCleared = 0 AND tOrderDocChase.DocChaseUID IS NOT NULL THEN FALSE ELSE TRUE END', NULL, FALSE);


		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($Conditions['DependentWorkflowPending'])) {
			if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
				$this->db->where('NOT EXISTS (' . $previous_filtered_orders_sql . ')', NULL, FALSE);
			}
		} else {
			if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
				$this->db->where('EXISTS (' . $previous_filtered_orders_sql . ')', NULL, FALSE);
			}
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}



		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

		if (!isset($Conditions['SkipCondition'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = " . $this->config->item('Workflows')['GateKeeping'] . " AND (tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' OR tOrderWorkflows.IsForceEnabled = '" . STATUS_ONE . "') THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = " . $this->config->item('Workflows')['GateKeeping'] . " THEN 
				CASE WHEN tOrderAssignments.WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN FALSE 
				ELSE TRUE END
				ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}		

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['GateKeeping']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/

		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}
	// GateKeeping Workflow Orders End

	// Submissions Workflow Orders Begin
	function GetSubmissionsOrders($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Submissions'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Submissions']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();

			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Submissions"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['Submissions'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['Submissions'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Submissions']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		$this->db->group_start();
		$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Submissions'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Submissions'] . "' 
			THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
			ELSE FALSE END", NULL, FALSE);
		$this->db->group_end();
		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}
	// Submissions Workflow Orders End

	/* Desc:  get hoi non mail orders @author: Santhiya M <santhiya.m@avanzegroup.com> */
	function getEmailsOrders()
	{
		$HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];

		$this->db->select('tOrders.OrderUID,tOrders.IsOCREnabled,tOrders.IsHoiNotified,tDocuments.IsStacking,tOrders.OrderNumber,tOrders.LoanNumber,mCustomer.CustomerEmail,mCustomer.CustomerName,tOrderImport.InsuranceCompany,tOrderImport.PolicyNumber,tDocuments.DocumentUID,tOrderImport.Email,tOrderImport.Efax,tOrderImport.WebUrl');
		$this->db->from('tOrders');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID=tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID="' . $HoiWorkflowModuleUID . '"');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID=tOrders.CustomerUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tDocuments', 'tDocuments.OrderUID=tOrders.OrderUID');
		$this->db->where(['tOrders.IsOCREnabled' => '2', 'tDocuments.IsStacking' => '2', 'tOrders.IsHoiNotified' => '0']);
		// $this->db->or_where('tOrders.IsHoiNotified', NULL, TRUE);
		$this->db->group_by('tOrders.OrderUID');

		$tOrders = $this->db->get();


		if ($tOrders->num_rows() > 0) {
			return $tOrders->result();
		} else {
			return false;
		}
	}
	//get hoi non mail orders

	/*SFTP*/
	public function getNormalAutoImport()
	{
		$query = $this->db->where(array('IsActive'	=>	'1', 'mProjectCustomer.ZipImport'	=>	'0'))
			->select('mAutoImport.*,mProjectCustomer.SFTPUID,mSFTP.*,mImportColumn.ColumnName')
			->from('mAutoImport')
			->join('mProjectCustomer', 'mAutoImport.ProjectUID = mProjectCustomer.ProjectUID')
			->join('mSFTP', 'mProjectCustomer.SFTPUID = mSFTP.SFTPUID')
			->join('mImportColumn', 'mProjectCustomer.AutoImportColumn = mImportColumn.ColumnID')
			->get();
		return $query->result();
	}

	public function getProjectWaitingLoans($ProjectUID)
	{
		$statusUID = $this->config->item('keywords')['New Order'];
		$query = $this->db->query("SELECT * FROM `tOrders` WHERE `StatusUID` = '" . $statusUID . "' AND `ProjectUID` = '" . $ProjectUID . "' and AutoExportStatus != '6'");
		return $query->result();
	}

	public function updateOrderStatus($OrderUID, $data)
	{
		$this->db->where(array('OrderUID' => $OrderUID));
		$query = $this->db->update('tOrders', $data);
	}
	/*Purpose: Convert the array to Key/Value format Author: Yagavi G <yagavi.g@avanzegroup.com> Since July 6th 2020 */
	function array_to_list(array $Array)
	{
		static $counter = 0;
		$Output = '<ul>';
		foreach ($Array as $Key => $Value) {
			$Output .= "<li><strong>{$Key}: </strong>";
			if (is_array($Value)) {
				$Output .= $this->array_to_list($Value);
			} else {
				if ($Value != '') {
					$btnid = str_replace(' ', '', 'btnCopy_' . $counter);
					$Output .= '<a id="' . $btnid . '">' . $Value . '</a></span>';
				} else {
					$Output .= $Value;
				}
				$counter++;
			}
			$Output .= '</li>';
		}
		$Output .= '</ul>';
		return $Output;
	}
	/* Desc: Store response details for Automation Log @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 6th 2020 */
	function insert_AutomationLog($fieldArray)
	{
		$this->db->set($fieldArray);
		$this->db->insert('tAutomationLog');
		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	/**
	 *Function to get last automation log of an order 
	 *
	 * @param OrderUID
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @since 
	 *
	 * @return array
	 */
	public function CheckAutoStatus($OrderUID)
	{
		$this->db->select('tOrders.OrderNumber,tOrders.LoanNumber,tOrders.IsOCREnabled,IsHoiNotified,AutoExportStatus,tAutomationLog.*');
		$this->db->from('tOrders');
		$this->db->join('tAutomationLog', 'tAutomationLog.OrderUID = tOrders.OrderUID', 'left');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->order_by('AutomationLogUID', 'desc');
		$getLog = $this->db->get();
		if ($getLog->num_rows() > 0) {
			return $getLog->row();
		} else {
			return false;
		}
	}
	/**
	 *Function to get automation logs of an order 
	 *
	 * @param OrderUID
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @since Thursday 6 Augest 2020
	 *
	 * @return array
	 */
	public function GetAutoLogs($OrderUID)
	{
		$this->db->select('tOrders.OrderNumber,tOrders.LoanNumber,tOrders.IsOCREnabled,IsHoiNotified,tAutomationLog.*');
		$this->db->from('tOrders');
		$this->db->join('tAutomationLog', 'tAutomationLog.OrderUID = tOrders.OrderUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->order_by('AutomationLogUID', 'desc');
		$getLog = $this->db->get();
		if ($getLog->num_rows() > 0) {
			return $getLog->result();
		} else {
			return false;
		}
	}

	function GetCalculatorData($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('*')->from('tCalculator');
		$this->db->where('tCalculator.OrderUID', $OrderUID);
		$this->db->where('tCalculator.WorkflowModuleUID', $WorkflowModuleUID);
		return $this->db->get()->row();
	}
	function ScheduleUpdate($post)
	{
		$this->db->where(array('OrderUID' => $post['OrderUID']));
		$query = $this->db->update('tOrderImport', array('ScheduledDate' => $post['schedule_date'], 'ScheduledTime' => $post['schedule_time']));
		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	//get color based on due date
	function GetDueDateRow($DueDateTime, $LoanNumber, $ExpiryOrderInDuration, $ExpiryOrders)
	{

		if ($ExpiryOrders == 1) {
			$row = '<span style="background-color:#ff0000;color:white;font-weight: bold;">' . $LoanNumber. '</span>';
		} elseif ($ExpiryOrderInDuration == 1) {
			$row = '<span style="background-color:orange;color:white;font-weight: bold;">' . $LoanNumber. '</span>';
		} else {
			$row = $LoanNumber;
		}
		return $row;
	}
	/**
	 *Function get Queue UID using Queue Name
	 *@author Santhiya M <santhiya.m@avanzegroup.com>
	 *@since Tuesday 14 July 2020
	 */
	function GetQueueUID($QueueName)
	{
		$this->db->select('QueueUID');
		$this->db->from('mQueues');
		$this->db->where('QueueName', $QueueName);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}
	}
	/**
	 *Function Update & Move Queue on Hoi Automation process 
	 *@author Santhiya M <santhiya.m@avanzegroup.com>
	 *@since Tuesday 14 July 2020
	 */
	function MoveQueue($OrderUID, $QueueUID, $reason = 33)
	{

		$WorkflowModuleUID = $this->config->item('Workflows')['HOI'];

		$tOrderQueues = $this->db->select('tOrderQueues.QueueUID,QueueName')->join('mQueues', 'mQueues.QueueUID = tOrderQueues.QueueUID')->where(['tOrderQueues.OrderUID' => $OrderUID, 'mQueues.WorkflowModuleUID' => $WorkflowModuleUID, "tOrderQueues.QueueStatus" => "Pending"])->get('tOrderQueues')->result();

		if ($this->loggedid) {
			$UserID = $this->loggedid;
		} else {
			$UserID = $this->config->item('Cron_UserUID');
		}
		if (!empty($tOrderQueues)) {
			foreach ($tOrderQueues as $tOrderQueue) {

				$queueUpdate = [];
				$queueUpdate['OrderUID'] = $OrderUID;
				$queueUpdate['QueueUID'] = $tOrderQueue->QueueUID;
				$queueUpdate['QueueStatus'] = "Completed";
				$queueUpdate['CompletedReasonUID'] = $reason;
				$queueUpdate['CompletedRemarks'] = '';
				$queueUpdate['CompletedByUserUID'] = $UserID;
				$queueUpdate['CompletedDateTime'] = date('Y-m-d H:i:s');
				$update = $this->Common_Model->save('tOrderQueues', $queueUpdate, ['OrderUID' => $OrderUID, 'QueueUID' => $tOrderQueue->QueueUID, "QueueStatus" => "Pending"]);
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID, $tOrderQueue->QueueName . ' - Queue Completed', Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
			}
		}

		$queuesave = [];
		$queuesave['OrderUID'] = $OrderUID;
		$queuesave['QueueUID'] = $QueueUID;
		$queuesave['QueueStatus'] = "Pending";
		$queuesave['RaisedReasonUID'] = $reason;
		$queuesave['RaisedRemarks'] = '';
		$queuesave['RaisedDateTime'] = date('Y-m-d H:i:s');
		$queuesave['RaisedByUserUID'] = $UserID;
		$insert = $this->Common_Model->save('tOrderQueues', $queuesave);

		return true;
	}

	function GetSftpFiles($OrderUID)
	{
		$this->db->select('tDocuments.*, tOrders.LoanNumber, tOrders.OrderNumber');
		$this->db->from('tDocuments');
		$this->db->join('tOrders', 'tOrders.OrderUID = tDocuments.OrderUID');
		$this->db->where(array('tDocuments.OrderUID' => $OrderUID, 'TypeofDocument' => 'SftpFile'));
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	/**
	 *Function to complete orders if find sftp file 
	 *
	 * @param OrderUID, WorkflowModuleUID
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @since Thursday 16 July 2020
	 *
	 * @return boolean
	 */
	function CompleteHoi($OrderUID, $WorkflowModuleUID)
	{
		$Cron_uid = $this->config->item('Cron_UserUID');
		$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);

		if (empty($is_assignment_row_available)) {
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['OrderUID'] = $OrderUID;
			$res['WorkflowModuleUID'] = $WorkflowModuleUID;
			$res['AssignedToUserUID'] = $Cron_uid;
			$res['AssignedDatetime'] = date('Y-m-d H:i:s');
			$res['AssignedByUserUID'] = $Cron_uid;
			$res['CompletedByUserUID'] = $Cron_uid;
			$res['CompleteDateTime'] = date('Y-m-d H:i:s');
			$this->db->insert('tOrderAssignments', $res);
		} else {
			$filter['OrderUID'] = $OrderUID;
			$filter['WorkflowModuleUID'] = $WorkflowModuleUID;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['CompletedByUserUID'] = $Cron_uid;
			$res['CompleteDateTime'] = date('Y-m-d H:i:s');
			$this->db->where($filter);
			$this->db->update('tOrderAssignments', $res);
		}
		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_lcrequired($CashFromBorrower, $ProposedTotalHousingExpense, $Assets)
	{
		return '$' . max(number_format((float)$CashFromBorrower - ((float)$ProposedTotalHousingExpense + (float)$Assets), 2), 0);
	}
	/**
	 *Function to change email log status to read 
	 *
	 * @param EmailUID
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @since Thursday 17th July 2020
	 *
	 * @return boolean
	 */
	function ReadEmail($EmailUID)
	{
		$this->db->where('EmailUID', ($EmailUID))->update('tEmailImport', array('IsRead' => '1'));

		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	/**
	 *Function to change email log status to read 
	 *
	 * @param None
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @since Thursday 17th July 2020
	 *
	 * @return result array
	 */
	function GetUnreadEmailLogs()
	{
		$this->db->select('*');
		$this->db->from('tEmailImport');
		$this->db->like('RefID', 'MailRef');
		$this->db->where('IsRead', '0');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	/**
	 *Function to change order notification flag 
	 *
	 * @param OrderUID
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @since Friday 17th July 2020
	 *
	 * @return boolean
	 */
	function NotifiedOrder($OrderUID)
	{
		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('tOrders', ['IsHoiNotified' => '1']);
		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	/* 
	*get documenttypeUID of tdocumentchecklist 
	*@author Vishnupriya <vishnupriya.a@avanzegroup.com>
	*@since Date : 16-07-2020
	 */
	function getDocumentCheckList($OrderUID)
	{
		$this->db->select("DocumentTypeUID");
		$this->db->from('tDocumentCheckList');
		$this->db->where(array('IsDelete' => '0', 'OrderUID' => $OrderUID));
		$deleted = $this->db->get()->result_array();
		return $parentChecklistsexits = array_column($deleted, 'DocumentTypeUID');
	}
	/* *
	*get documenttypeUID of tdocumentchecklist 
	*@author Vishnupriya <vishnupriya.a@avanzegroup.com>
	*@since Date : 17-07-2020
	 */
	function getDocumentlistOrder($OrderUID, $CategoryUID)
	{
		$this->db->select("DocumentTypeUID");
		$this->db->from('tDocumentCheckList');
		$this->db->where(array('IsDelete' => '0', 'OrderUID' => $OrderUID, 'CategoryUID' => $CategoryUID,));
		$this->db->order_by('Position');
		$getDocumentTypeUID = $this->db->get()->result_array();
		return array_column($getDocumentTypeUID, 'DocumentTypeUID');
	}
	/* *
	*get documenttypeUID of tdocumentchecklist for set position 
	*@author Vishnupriya <vishnupriya.a@avanzegroup.com>
	*@since Date : 17-07-2020
	 */
	function getDocumentCategoryList($OrderUID, $WorkflowModuleUID, $groups, $Documentlist)
	{
		$OrderDetails = $this->db->select('CustomerUID')->from('tOrders')->where('OrderUID', $OrderUID)->get()->row();
		$this->db->select('*')->from('mCustomerWorkflowModules')->join('mDocumentType', 'mDocumentType.CategoryUID=mCustomerWorkflowModules.CategoryUID AND mDocumentType.CategoryUID IS NOT NULL AND mCustomerWorkflowModules.CategoryUID IS NOT NULL');
		if ($groups != 'FHA/VA') {
			if ($groups) {
				$this->db->where('(mDocumentType.Groups IS NULL OR mDocumentType.Groups ="' . $groups . '")');
			}
		}
		$this->db->where('(mDocumentType.ParentDocumentTypeUID IS NULL OR mDocumentType.ParentDocumentTypeUID = "")', NULL, FALSE);

		$this->db->where(array('mCustomerWorkflowModules.CustomerUID' => $OrderDetails->CustomerUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'mDocumentType.CustomerUID' => $OrderDetails->CustomerUID, 'mDocumentType.Active' => '1'));
		//$this->db->where_in('mDocumentType.DocumentTypeUI', 1995, 1994, 1996, 1997);
		if ($Documentlist) {
			//$this->db->order_by('FIELD ( mDocumentType.DocumentTypeUID, ' . $Documentlist . ' )');
		}

		return $this->db->get()->result();
	}

	/* *
	*get documenttype data of documenttypeUID from tdocumentchecklist
	*@author Vishnupriya <vishnupriya.a@avanzegroup.com>
	*@since Date : 20-07-2020
	 */
	function getDocumentTypeRecord($OrderUID, $DocumentTypeUID)
	{
		$this->db->select("Comments,DocumentDate,DocumentType,DocumentExpiryDate");
		$this->db->from('tDocumentCheckList');
		$this->db->where(array('OrderUID' => $OrderUID, 'DocumentTypeUID' => $DocumentTypeUID,));
		$getDocumentTypeUID = $this->db->get()->result();
		return $getDocumentTypeUID;
	}

	/**
	 * Function 
	 *
	 * @param OrderUID
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @return Bot log array
	 * @since July 18 2020
	 *
	 */
	function GetBotLog($OrderUID)
	{
		$this->db->select('*');
		$this->db->like('OrderUID', $OrderUID);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}
	}
	/**
	 * Function to get BOT SFTP details 
	 *
	 * @param None
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @return SFTP details
	 * @since 20 July 2020
	 *
	 */
	public function GetHOISftp()
	{
		$query = $this->db->select('mSFTP.*,mProjectCustomer.BOTSFTPUID')
			->from('mSFTP')
			->join('mProjectCustomer', 'mSFTP.SFTPUID = mProjectCustomer.BOTSFTPUID')
			->get();
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}
	}

	/**
	 * Function to Extrtact Address from string
	 *
	 * @param Address (String)
	 *
	 * @throws no exception
	 * @author Yagavi G <yagavi.g@avanzegroup.com>
	 * @return Array
	 * @since July 20th 2020
	 *
	 */

	function ExtractAddress($address = '')
	{
		$address = strtoupper($address);
		$split_address = explode(' ', $address); /* Split the address string */
		$zip =  end($split_address); /* Get last array */

		$details = $this->GetZipCode($zip); /* Get the Zipcode address */
		if ($details) {
			$details = json_encode($details);
			$details = json_decode($details, true); /* Convert to Array */
			$CityName = $details['City'][0]['CityName'];
			$StateCode = $details['State'][0]['StateCode'];
			$CountyName = $details['County'][0]['CountyName'];

			$find = [$zip, $CityName, $StateCode, $CountyName, ","];
			$replace   = ["", "", "", "", ""];
			$addressline1 = trim(str_replace($find, $replace, $address)); /* Find replace the string */

			$PropertyAddress = array(
				'Address1' => $addressline1,
				'CityName' => $CityName,
				'StateCode' => $StateCode,
				'CountyName' => $CountyName,
				'Zipcode' => $zip
			);
			return $PropertyAddress;
		}
	}

	/**
	 * Function to Zipcode details
	 *
	 * @param Zipcode (String)
	 *
	 * @throws no exception
	 * @author Yagavi G <yagavi.g@avanzegroup.com>
	 * @return Array
	 * @since July 20th 2020
	 *
	 */

	function GetZipCode($Zipcode)
	{
		$Zipcode =  preg_replace('/[^A-Za-z0-9\-]/', '-', $Zipcode);
		$zips = explode('-', preg_replace('/-+/', '-', $Zipcode) );
		$zip = $zips[0];
		if ($zip) {
			$City = $this->Common_Model->getCityDetail($zip);
			$State = $this->Common_Model->getStateDetail($zip);
			$County = $this->Common_Model->getCountyDetail($zip);
			if (!empty($State) && !empty($County) && !empty($City)) {
				return array('City' => $City, 'success' => 1, 'State' => $State, 'County' => $County);
			} else {
				return array('details' => '', 'success' => 0);
			}
		} else {
			return array('details' => '', 'success' => 0);
		}
	}
	/**
	 * Function to Get Order details used in Bot fields
	 *
	 * @param OrderUID
	 *
	 * @throws no exception
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @return Array
	 * @since July 21th 2020
	 *
	 */
	function GetExportDetails($OrderUID)
	{
		$this->db->select('tOrderImport.*, tOrders.LoanNumber, tOrders.LoanAmount, tOrders.OrderNumber');
		$this->db->from('tOrders');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'LEFT');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}
	}

	/**
	 * Function to split PDF paged
	 *
	 * @param original_filepath (string), pages (string), temp_filepath (string)
	 * @author Yagavi G <yagavi.g@avanzegroup.com>
	 * @return noting
	 * @since July 21th 2020
	 * @version E-Fax Integration
	 *
	 */

	function SplitPDF($original_filepath, $pages, $temp_filepath)
	{
		$main = "pdftk \"" . $original_filepath . "\" cat " . $pages . " output \"" . $temp_filepath . "\""; /* pdftk used to split PDF pages */
		shell_exec($main);
	}

	/* *
	* Function for get mAuditlog data
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com>
	* @since Date : 24-07-2020
	 */
	function getmAuditlog()
	{
		$this->db->select("*");
		$this->db->from('mAuditLog');
		$this->db->join('mUsers', 'mUsers.UserUID=mAuditLog.UserID');
		$this->db->where('mAuditLog.Module', $this->uri->segment(1));
		$this->db->order_by("LogDateTime", "desc");
		$auditlog = $this->db->get()->result();
		return $auditlog;
	}


	/**
	*Function OrdersQueue - Role Level Permissions 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Friday 24 July 2020.
	*/
	/**
	*Added Conditions by harini for user assigned count
	*@author harini bangari <harini.bangari@avanzegroup.com>
	*@since tuesday 25 Aug 2020.
	*/
	function OrdersPermission($WorkflowModuleUID=false,$RequestType=false,$otherdatabase=false,$Conditions = [])
	{	
		$db = ($otherdatabase === true) ? $this->otherdb : $this->db;

		//check permissions for orders
		if(isset($this->UserPermissions->OrderQueue) && $this->UserPermissions->OrderQueue == 2 && in_array($this->RoleType, $this->config->item('ProcessorAccess'))) {
			//PROCESSOR QUEUE PERMISSION

			//TOTAL ORDERS

			if($RequestType == 'TOTALCOUNT') {

				//for all counts

				if(!empty($WorkflowModuleUID)) {

					//filter exception orders when myorders

					//$db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND (tOrderQueues.RaisedByUserUID <> ".$this->loggedid." OR mQueues.Active = 0) )", NULL, FALSE);

					//filter order when parking

					$db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE tOrderParking.OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID='".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);

				}



				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();


				
			}

			//Expiration
			elseif($RequestType == 'ESCLATIONCOUNT') {

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();

			}

			//NEW COUNT - NEW ORDERS
			else if($RequestType == 'NEWCOUNT') {
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();
			}

			//ASSIGNED COUNT - ASSIGNED ORDERS
			else if($RequestType == 'ASSIGNEDCOUNT') {
				//for tab counts and orders
				//nothing returns
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->where('tOrderAssignments.AssignedToUserUID <> ',$this->loggedid);
				$db->where('tOrderAssignments.AssignedToUserUID IS NOT NULL',NULL,FALSE);
				//$db->where('tOrderAssignments.AssignedToUserUID <> ','');
				
			}

			//MYCOUNT - MYORDERS
			else if($RequestType == 'MYCOUNT') {
				//for tab counts and orders
				//Order Queue Permission
				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
			}

			//PARKINGCOUNT - PARKING ORDERS
			else if($RequestType == 'PARKINGCOUNT') {

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->where('tOrderParking.RaisedByUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();


			}

			//KICKBACKCOUNT - KICKBACK ORDERS -- LIKE NEW ORDERS ON REVERSED AVAILABLE
			else if($RequestType == 'KICKBACKCOUNT') {



				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
				

			}

			//REWORKCOUNT - REWORK ORDERS -- LIKE NEW ORDERS ON REVERSED AVAILABLE
			else if($RequestType == 'REWORKCOUNT') {

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();

			}

			//COMPLETEDCOUNT - COMPLETED ORDERS
			else if($RequestType == 'COMPLETEDCOUNT') {
				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->where('tOrderAssignments.CompletedByUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
			}

			//SUBQUEUECOUNT - SUBQUEUE ORDERS
			else if($RequestType == 'SUBQUEUECOUNT') {

				$db->group_start();
				$db->group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->or_group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				// $db->where('tOrderQueues.RaisedByUserUID',$this->loggedid);
				$db->group_end();

			}
			

			//EXPIREDCOUNT - EXPIRED ORDERS
			else if($RequestType == 'EXPIREDCOUNT') {

				$db->like('tOrderImport.LoanProcessor',$this->UserName);

				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
			}


			//PRIORITYCOUNT - COMPLETED ORDERS
			else if($RequestType == 'PRIORITYCOUNT') {

				$db->like('tOrderImport.LoanProcessor',$this->UserName);
			}

			//EXCLUDEOTHERSASSIGNEDCOUNT - EXCLUDE OTHER ASSIGNED ORDERS
			else if($RequestType == 'EXCLUDEOTHERSASSIGNEDCOUNT') {

				$db->like('tOrderImport.LoanProcessor',$this->UserName);

				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
			}

		}else if(isset($this->UserPermissions->OrderQueue) && $this->UserPermissions->OrderQueue == 2) {
			//MY ORDERS QUEUE PERMISSION

			//TOTAL ORDERS


			if($RequestType == 'TOTALCOUNT') {

				//for all counts

				if(!empty($WorkflowModuleUID)) {

					//filter order when parking
					$db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE tOrderParking.OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID='".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);
				}

				if(!empty($WorkflowModuleUID)) {
					//filter exception orders when myorders
					$db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID LEFT JOIN tOrderAssignments OA ON OA.OrderUID = tOrderQueues.OrderUID AND OA.WorkflowModuleUID = '".$WorkflowModuleUID."' WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = '".$WorkflowModuleUID."' AND (((tOrderQueues.RaisedByUserUID <> ".$this->loggedid." OR tOrderQueues.RaisedByUserUID IS NULL) OR mQueues.Active = 0 ) AND (OA.AssignedToUserUID <> ".$this->loggedid." OR OA.AssignedToUserUID IS NULL) ) ) ", NULL, FALSE);

				}

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();

				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
				
			}

			//Expiration
			elseif($RequestType == 'ESCLATIONCOUNT') {

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();

				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
								
			}

			//NEW COUNT - NEW ORDERS
			else if($RequestType == 'NEWCOUNT') {
				$db->group_start();
				
				$db->where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();
			}

			//ASSIGNED COUNT - ASSIGNED ORDERS
			else if($RequestType == 'ASSIGNEDCOUNT') {
				//for tab counts and orders
				//nothing returns
				$db->where('(tOrderAssignments.AssignedToUserUID = "'.$this->loggedid.'" AND tOrderAssignments.AssignedToUserUID != "'.$this->loggedid.'" )',null,false);
				
			}

			//MYCOUNT - MYORDERS
			else if($RequestType == 'MYCOUNT') {
				//for tab counts and orders
				//Order Queue Permission
				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
			}

			//PARKINGCOUNT - PARKING ORDERS
			else if($RequestType == 'PARKINGCOUNT') {

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();
				$db->or_group_start();
				$db->where('tOrderParking.RaisedByUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();


			}

			//KICKBACKCOUNT - KICKBACK ORDERS -- LIKE NEW ORDERS ON REVERSED AVAILABLE
			else if($RequestType == 'KICKBACKCOUNT') {
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();
			
			}

			//REWORKCOUNT - REWORK ORDERS -- LIKE NEW ORDERS ON REVERSED AVAILABLE
			else if($RequestType == 'REWORKCOUNT') {
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();
			
			}

			//COMPLETEDCOUNT - COMPLETED ORDERS
			else if($RequestType == 'COMPLETEDCOUNT') {
				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->where('tOrderAssignments.CompletedByUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
			}

			//SUBQUEUECOUNT - SUBQUEUE ORDERS
			else if($RequestType == 'SUBQUEUECOUNT') {
				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				// $db->or_group_start();
				// $db->where('tOrderQueues.RaisedByUserUID',$this->loggedid);
				// $db->group_end();
				$db->group_end();
			}			

			//EXPIREDCOUNT - EXPIRED ORDERS
			else if($RequestType == 'EXPIREDCOUNT') {

				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();
			}


			//PRIORITYCOUNT - COMPLETED ORDERS
			else if($RequestType == 'PRIORITYCOUNT') {

				$db->like('tOrderImport.LoanProcessor',$this->UserName);
			}

			//EXCLUDEOTHERSASSIGNEDCOUNT - EXCLUDE OTHER ASSIGNED ORDERS
			else if($RequestType == 'EXCLUDEOTHERSASSIGNEDCOUNT') {

				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();
			}


		}	 else {
			//ALL ORDERS PERMISSION
			if($RequestType == 'TOTALCOUNT') {

				if(!empty($WorkflowModuleUID)) {
					//filter order when parking

					$db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE tOrderParking.OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID='".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);
				}

				//for all counts

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID <>','');
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');				
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
				
			}

			//Expiration
			elseif($RequestType == 'ESCLATIONCOUNT') {

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID <>','');
				$db->or_where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');				
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
								
			}

			//NEW COUNT - NEW ORDERS
			else if($RequestType == 'NEWCOUNT') {
				//for tab counts and orders
				//Order Queue Permission
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID','');
				$db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
				$db->group_end();


			}

			//ASSIGNED COUNT - ASSIGNED ORDERS
			else if($RequestType == 'USERASSIGNEDCOUNT') {
				$db->where('(tOrderAssignments.AssignedToUserUID = "'.$Conditions['UserUID'].'" )',null,false);
				//print_r($this->db);
				//print_r($db->last_query());
				//exit;
			}
			//ASSIGNED COUNT - ASSIGNED ORDERS
			else if($RequestType == 'ASSIGNEDCOUNT') {
				//for tab counts and orders
				$db->where('(tOrderAssignments.AssignedToUserUID IS NOT NULL AND tOrderAssignments.AssignedToUserUID <> "" AND tOrderAssignments.AssignedToUserUID <> "'.$this->loggedid.'" )',null,false);
			}

			//MYCOUNT - MYORDERS
			else if($RequestType == 'MYCOUNT') {
				//for tab counts and orders
				//Order Queue Permission
				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();
			}

			//KICKBACKCOUNT - KICKBACK ORDERS
			else if($RequestType == 'KICKBACKCOUNT') {
				
			}

			//REWORKCOUNT - REWORK ORDERS
			else if($RequestType == 'REWORKCOUNT') {
				
			}

			//PARKINGCOUNT - PARKING ORDERS
			else if($RequestType == 'PARKINGCOUNT') {


			}

			//COMPLETEDCOUNT - COMPLETED ORDERS
			else if($RequestType == 'COMPLETEDCOUNT') {

			}

			//EXPIREDCOUNT - EXPIREDCOUNT ORDERS
			else if($RequestType == 'EXPIREDCOUNT') {

			}

			//SUBQUEUECOUNT - SUBQUEUECOUNT ORDERS
			else if($RequestType == 'SUBQUEUECOUNT') {

			}

			//EXCLUDEOTHERSASSIGNEDCOUNT - EXCLUDE OTHER ASSIGNED ORDERS
			else if($RequestType == 'EXCLUDEOTHERSASSIGNEDCOUNT') {

			}

		}
	}

	//Order Queue Permission
	function reportOrderPermission($where,$RequestType,$index=false,$lastindex=false,$otherdatabase=false)
	{
		$db = ($otherdatabase === true) ? $this->otherdb : $this->db;
		//check permissions for orders
		if(isset($this->UserPermissions->OrderQueue) && $this->UserPermissions->OrderQueue == 2) {
			//PRIORITYCOUNT - COMPLETED ORDERS
			if($RequestType == 'PRIORITYCOUNT') {

				if($index == 0) {
					$db->group_start();

					$db->group_start();
					$db->like('tOrderImport.LoanProcessor',$this->UserName);
					$db->group_end();

				} else if($index == $lastindex) {
					$db->group_end();
				} else {
					$db->or_group_start();
					$db->where($where,$this->loggedid);
					$db->group_end();
				}

			}elseif($RequestType == 'PRIORITYUSERCOUNT') {

				$db->group_start();
				$db->where('mUsers.UserUID',$this->loggedid);
				$db->group_end();

			}elseif($RequestType == 'USERCOUNT') {

				$db->group_start();
				$db->where($where,$this->loggedid);
				$db->group_end();

			}elseif($RequestType == 'PIPELINECOUNT') {

				$db->group_start();
				$db->group_start();
				$db->where('tOrderAssignments.AssignedToUserUID',$this->loggedid);
				$db->group_end();
				$db->or_group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();
				$db->group_end();

			}elseif($RequestType == 'LOANPROCESSORCOUNT') {

				$db->group_start();
				$db->like('tOrderImport.LoanProcessor',$this->UserName);
				$db->group_end();

			}
		}


	}

	/**
	 * Function to get email received content
	 * 
	 * @param OrderUid
	 *
	 * @author Santhiya M <santhiya.m@avanzegroup.com>
	 * @return Array
	 * @since July 24 2020
	 * 
	**/
	function GetReceivedEmail($OrderUID){
		$this->db->select('tEmailImport.*, tAutomationLog.*');
		$this->db->from('tAutomationLog');
		$this->db->join('tEmailImport', '(tEmailImport.EmailUID = tAutomationLog.EmailUID AND tAutomationLog.AutomationType = "Email Receive")', 'left');
		$this->db->where('tAutomationLog.OrderUID',$OrderUID);
		$this->db->where('tAutomationLog.AutomationStatus', 'Success');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return false;
		}
	}
	function GetBotFile($OrderUID){
		$this->db->select('*');
		$this->db->from('tDocuments');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->order_by('DocumentUID', 'DESC');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return false;
		}
	}

	/**
	 * Function to filter Checklist Expiry orders
	 * 
	 * @param WorkflowModuleUID
	 *
	 * @author Sathishkumar <sathish.kumar@avanzegroup.com>
	 * @return Query
	 * @since July 28 2020
	 * 
	**/
	function checklistExpiryOrdersConditions($WorkflowModuleUID) {

		$workflowchecklist = isset($this->config->item('Expired_Checklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$WorkflowModuleUID] : NULL;

		$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$WorkflowModuleUID] : NULL;

		if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

			$this->db->group_start();
			foreach ($workflowchecklist as $checklistkey => $checklistuid) {

				$this->db->select('TDCEXP_'. $checklistuid.'.DocumentExpiryDate AS Expired_Checklist_DocumentExpiryDate_'. $checklistuid,false);
				$this->db->select('TDCEXP_'. $checklistuid.'.DocumentDate AS Expired_Checklist_DocumentDate_'. $checklistuid,false);
				$this->db->select('MDOCEXP_'. $checklistuid.'.DocumentTypeName AS Expired_Checklist_DocumentType'. $checklistuid,false);

				$this->db->join("tDocumentCheckList AS " .  "TDCEXP_" . $checklistuid,   "TDCEXP_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDCEXP_" . $checklistuid.".WorkflowUID = ".$WorkflowModuleUID. " AND TDCEXP_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' ","LEFT");
				$this->db->join("mDocumentType AS " .  "MDOCEXP_" . $checklistuid,   "MDOCEXP_" . $checklistuid.".DocumentTypeUID = TDCEXP_". $checklistuid.".DocumentTypeUID","LEFT");				

				if($checklistkey == 0) {
					$this->db->group_start();
				} else {
					$this->db->or_group_start();
				}

				if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {
					
					// $this->db->where("FIND_IN_SET(DATE_FORMAT(LAST_DAY(now() - INTERVAL 1 MONTH), '%b'),TDCEXP_". $checklistuid.".DocumentDate)", NULL, FALSE);
					// $this->db->where("NOT FIND_IN_SET(DATE_FORMAT(CURDATE(), '%b'),TDCEXP_". $checklistuid.".DocumentDate)", NULL, FALSE);
				} else {

					if ($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {
						
						$this->db->where('TDCEXP_'. $checklistuid.'.DocumentDate IS NOT NULL AND TDCEXP_'. $checklistuid.'.DocumentDate <> ""');					
						$this->db->where("DATE(STR_TO_DATE(TDCEXP_". $checklistuid.".DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY))", NULL, FALSE);	
					} else {

						$this->db->where('TDCEXP_'. $checklistuid.'.DocumentExpiryDate IS NOT NULL AND TDCEXP_'. $checklistuid.'.DocumentExpiryDate <> ""');					
						$this->db->where("DATE(STR_TO_DATE(TDCEXP_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY))", NULL, FALSE);	
					}
									
				}

				$this->db->group_end();

				break;

			}
			$this->db->group_end();
		}
		// Once Expiry Orders complete order moved to Expiry Complete
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);

		//3,4,5 series should be not part of expiry orders		
		$this->db->group_start();
		$this->db->where('(tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = "")', NULL, FALSE);
		$this->db->or_where_not_in('tOrders.MilestoneUID', $this->config->item('ExpiryChecklistOrderRestrictedMilestones'));
		$this->db->group_end();

	}

	/**
	*Function Checklist Expiry Complete 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 09 October 2020.
	*/
	function checklistExpiryCompleteOrdersConditions($WorkflowModuleUID) {

		// Once Expiry Orders complete order moved to Expiry Complete
		$this->db->where("EXISTS (SELECT 1 FROM tOrderChecklistExpiryComplete WHERE tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);

		//3,4,5 series should be not part of expiry orders		
		$this->db->group_start();
		$this->db->where('(tOrders.MilestoneUID IS NULL OR tOrders.MilestoneUID = "")', NULL, FALSE);
		$this->db->or_where_not_in('tOrders.MilestoneUID', $this->config->item('ExpiryChecklistOrderRestrictedMilestones'));
		$this->db->group_end();

	}

	/**
	*Function to filter Checklist Expiry orders 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 09 October 2020.
	*/
	public function IsChecklistExpiryOrders($OrderUID, $WorkflowModuleUID)
	{

		$workflowchecklist = isset($this->config->item('Expired_Checklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$WorkflowModuleUID] : NULL;
		if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

			$this->db->select('*');
			$this->db->from('tOrders');
			$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "'.$WorkflowModuleUID.'" ');
			$this->db->where('tOrders.OrderUID', $OrderUID);

			// filter Checklist Expiry orders
			$this->checklistExpiryOrdersConditions($WorkflowModuleUID);

			$count_all = $this->db->count_all_results();
			if($count_all > 0){
				return true;
			}	
		}
		return false;
	}

	/**
	*Function Expired checklist badge 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Wednesday 29 July 2020.
	*/
	function get_expiredchecklistalertrow($myorders,$WorkflowModuleUID)
	{
		$daysleft = '';
		$workflowchecklist = isset($this->config->item('Expired_Checklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$WorkflowModuleUID] : NULL;
		$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$WorkflowModuleUID] : NULL;

		if(isset($workflowchecklist) && $workflowchecklist && !empty($WorkflowModuleUID)) {

			if(is_array($workflowchecklist)) {

				foreach ($workflowchecklist as $checklistkey => $checklistuid) {
					$colorclass = '';
					if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {
						
						// $checklistrow = isset($myorders->{'Expired_Checklist_DocumentDate_'.$checklistuid}) && !empty($myorders->{'Expired_Checklist_DocumentDate_'. $checklistuid}) ? get_weekremainingdaysleft($myorders->{'Expired_Checklist_DocumentDate_'. $checklistuid}) : '';
						// if(!empty($checklistrow) && $checklistrow == 'E') { $colorclass = 'yellow'; }
						// $daysleft .= !empty($checklistrow) ? '<span class="badgenotification-queueloanexpired'.$colorclass.'" title="'.$myorders->{'Expired_Checklist_DocumentType'.$checklistuid}.'">'.$checklistrow.'</span>' : '';

					} else {

						if ($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {
							
							$checklistrow = isset($myorders->{'Expired_Checklist_DocumentDate_'.$checklistuid}) && !empty($myorders->{'Expired_Checklist_DocumentDate_'. $checklistuid}) ? get_remainingdaysleft($myorders->{'Expired_Checklist_DocumentDate_'. $checklistuid}, ['ExpiryOrdersAging' => 65]) : '';
						} else {

							$checklistrow = isset($myorders->{'Expired_Checklist_DocumentExpiryDate_'.$checklistuid}) && !empty($myorders->{'Expired_Checklist_DocumentExpiryDate_'. $checklistuid}) ? get_remainingdaysleft($myorders->{'Expired_Checklist_DocumentExpiryDate_'. $checklistuid},['ExpiryOrdersAging' => 10]) : '';
						}						

						if(!empty($checklistrow) && $checklistrow == 'E') { $colorclass = 'red'; } else if (!empty($checklistrow)) { $colorclass = 'orange';}
						$daysleft .= !empty($checklistrow) ? '<span class="badgenotification-queueloanexpired'.$colorclass.'" title="'.$myorders->{'Expired_Checklist_DocumentType'.$checklistuid}.'">'.$checklistrow.'</span>' : '';
					}
					
				}
			}
		}
		return $daysleft;
	}
	/**
	* Function to get waiting orders in HOI automation 
	*
	* @param 
	*
	* @throws no exception
	* @author Santhiya M <santhiya.m@avanzegroup.com>
	* @return 
	* @since July 31st 2020
	*
	*/
	function GetWaitingOrders(){
		$this->db->select('tOrders.*, tOrderQueues.QueueUID, tOrderQueues.RaisedDateTime as MailReceivedDateTime');
		$this->db->from('tOrders'); 
		$hoimailprocess = $this->config->item('HoiAutomationQueues')['HOIMailProcessQueues'];
		$this->db->join('tOrderQueues', 'tOrderQueues.OrderUID = tOrders.OrderUID', 'left');
		$this->db->where('tOrderQueues.QueueStatus', 'Pending');
		$this->db->where_in('tOrderQueues.QueueUID', $hoimailprocess);
		$query = $this->db->get();

		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}

	/** @author Sathishkumar R <sathish.kumar@avanzegroup.com> **/
	/** @date Friday 31 July 2020 **/
	/** @description Day difference **/
	/** @return number of days from two dates **/
	function TwoDatesDiffence($start, $end, $IncludeWeekends, $Conditions = []) {
		$start = new DateTime($start);
		$end = new DateTime($end);
		// otherwise the  end date is excluded (bug?)

		if (!isset($Conditions['Ignore'])) {
			
			$end->modify('+1 day');
		}		

		$interval = $end->diff($start);

		// total days
		$days = $interval->days;

		if ($days == 0) {
			return $days;
		}

		if ($IncludeWeekends == 1) {

			if (isset($Conditions['IsCheckistExpirySubQueueAging'])) {

				if ($interval->format("%r%a") < 0)
				{
					$days = 'E ('.abs($days).' Days)';
				}

			}

			return $days; // return days

		} else {

			// create an iterateable period of date (P1D equates to 1 day)
			$period = new DatePeriod($start, new DateInterval('P1D'), $end);

			// best stored as array, so you can add more than one
			$holidays = array();

			foreach($period as $dt) {
			    $curr = $dt->format('D');

			    // substract if Saturday or Sunday
			    if ($curr == 'Sat' || $curr == 'Sun') {
			        $days--;
			    }

			    // (optional) for the updated question
			    elseif (in_array($dt->format('Y-m-d'), $holidays)) {
			        $days--;
			    }
			}			

			if (isset($Conditions['IsCheckistExpirySubQueueAging'])) {

				if ($interval->format("%r%a") < 0)
				{
					$days = 'E ('.abs($days).' Days)';
				}

			}

			return $days; // return days
		}
	}

	/**
	*Function * Lock expiration of current day and one day before expiration 
			  * Rate Lock Expiration If weekend need to show upto next business day expiration dates
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Date.
	*@return True OR False
	*/
	public function CheckLockExpiration($LockExpiration='', $Conditions = [])
	{
		if (isset($Conditions['IsLockExpirationRestricted'])) {
			// lock expiration logic based on role
			if (!(isset($this->UserPermissions->IsLockExpirationRestricted) && $this->UserPermissions->IsLockExpirationRestricted == 1)) {
				return TRUE;
			}		

			$CurrentDate = date('m/d/Y');

			$NextBusinessDay1 = date('m/d/Y', strtotime($CurrentDate.' +2 Weekday'));

			if (!empty($LockExpiration) && ((strtotime($CurrentDate) <= strtotime($LockExpiration)) && (strtotime($NextBusinessDay1) >= strtotime($LockExpiration)))) {
				return FALSE;
			}
		}

		if (isset($Conditions['HighlightLockExpiryOrdersColumn'])) {
			// Highlight lock expiration column
			if (!($this->db->select('HighlightLockExpiryOrdersColumn')->from('mCustomer')->where('CustomerUID',$this->parameters['DefaultClientUID'])->get()->row()->HighlightLockExpiryOrdersColumn)) {
				return TRUE;
			}		

			$CurrentDate = date('m/d/Y');

			$NextBusinessDay1 = date('m/d/Y', strtotime($CurrentDate.' +2 Weekday'));

			if (strtotime($NextBusinessDay1) >= strtotime($LockExpiration)) {
				return FALSE;
			}	
		}

		return TRUE;
	}

	/**
	*Function Processor preferred closing date less than 5 days warning to be shown and can be assign highlight 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 17 August 2020.
	*/
	public function CheckProcessorChosenClosingDate($ProcessorChosenClosingDate='')
	{
		$CurrentDate = date('m/d/Y');

		if (!empty($ProcessorChosenClosingDate) && strtotime($ProcessorChosenClosingDate) >= strtotime($CurrentDate)) {

			$ToDate = date('m/d/Y', strtotime($CurrentDate.' +5 day'));

			if (strtotime($ToDate) > strtotime($ProcessorChosenClosingDate)) {
				return FALSE;
			}					

		}
		return TRUE;
	}

	/**
	*Function Get tOrderAssignments Details 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 17 August 2020.
	*/
	public function GettOrderAssignmentDetails($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('OrderUID,WorkflowModuleUID,AssignedToUserUID,AssignedDatetime,AssignedByUserUID,WorkflowStatus,CompletedByUserUID,CompleteDateTime,IsQCSkipped,UserProjectSkip,Remarks,OrderFlag,NOW() AS CreatedDateTime');
		$this->db->from('tOrderAssignments');
		$this->db->where(array('tOrderAssignments.OrderUID' => $OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $WorkflowModuleUID));
		return $this->db->get();
	}

	/**
	*Function Get tOrderAssignmentsHistory Details 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 17 August 2020.
	*/
	public function GettOrderAssignmentsHistory($OrderUID, $WorkflowModuleUID)
	{
		$this->db->select('tOrderAssignmentsHistory.AssignedDatetime, AssignedByUser.UserName AS AssignedByUserName, AssignedToUser.UserName AS AssignedToUserName');
		$this->db->from('tOrderAssignmentsHistory');
		$this->db->join('mUsers AS AssignedByUser','AssignedByUser.UserUID = tOrderAssignmentsHistory.AssignedByUserUID','left');
		$this->db->join('mUsers AS AssignedToUser','AssignedToUser.UserUID = tOrderAssignmentsHistory.AssignedToUserUID','left');
		$this->db->where(array('tOrderAssignmentsHistory.OrderUID' => $OrderUID, 'tOrderAssignmentsHistory.WorkflowModuleUID' => $WorkflowModuleUID));
		$AssignmentsHistoryQuery = $this->db->get_compiled_select();

		$this->db->select('tOrderAssignments.AssignedDatetime, AssignedByUser.UserName AS AssignedByUserName, AssignedToUser.UserName AS AssignedToUserName');
		$this->db->from('tOrderAssignments');
		$this->db->join('mUsers AS AssignedByUser','AssignedByUser.UserUID = tOrderAssignments.AssignedByUserUID','left');
		$this->db->join('mUsers AS AssignedToUser','AssignedToUser.UserUID = tOrderAssignments.AssignedToUserUID','left');
		$this->db->where(array('tOrderAssignments.OrderUID' => $OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $WorkflowModuleUID));
		$this->db->where('tOrderAssignments.AssignedToUserUID IS NOT NULL',NULL, FALSE);
		$AssignmentsQuery = $this->db->get_compiled_select();

		$query = $this->db->query($AssignmentsHistoryQuery . ' UNION ALL ' . $AssignmentsQuery);

		return $query->result();
	}


	/**
	*Function customer role workflows
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Friday 21 August 2020.
	*/
	function get_customerroleworkflows()
	{
		$this->db->select('mWorkFlowModules.*');
		$this->db->from('mResources');
		$this->db->join('mRoleResources', 'mRoleResources.ResourceUID=mResources.ResourceUID', 'inner');
		
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID=mResources.WorkflowModuleUID');
		$this->db->join('mCustomerWorkflowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID');
		$this->db->where(array(
			'mResources.FieldSection' => 'WORKFLOW',
			'mRoleResources.RoleUID' => $this->RoleUID
		));
		
		$this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", mCustomerWorkflowModules.CustomerUID)",NULL, FALSE);
		$this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", mResources.CustomerUID)",NULL, FALSE);
		
		$this->db->where_not_in('mWorkFlowModules.WorkflowModuleUID', '6');
		$this->db->where('mWorkFlowModules.Active',STATUS_ONE);
		$this->db->group_by('mWorkFlowModules.WorkflowModuleUID');
		return $this->db->get()->result();
	}

	//getPriorityReportColumn start
	function getJuniorPriorityReportColumn()
	{
		$CustomerUID = $this->session->userdata('DefaultClientUID');
		$this->db->select('*');
		$this->db->from('mJuniorPriorityReportColumns');
		$this->db->join('mFields', 'mFields.FieldUID = mJuniorPriorityReportColumns.FieldUID', 'left');
		$this->db->join('mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mJuniorPriorityReportColumns.WorkflowUID', 'left');
		$this->db->where('mJuniorPriorityReportColumns.CustomerUID', $CustomerUID);
		$this->db->order_by('Position', 'ASC');
		return $this->db->get()->result();
	}
	//getPriorityReportColumn end

	/**
	*Function getting total Assigned Count of user
	*@author Harini bangari <harini.bangari@avanzegroup.com>
	*@since tuesday 25 August 2020.
	*/
	function totAssignedCntByWorkflowModuleUID($WorkflowModuleUID,$UserUID, $FromDate, $ToDate)
	{
		// echo $FromDate;exit;
		$function_call	= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
		$this->db->select("1");
		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->$function_call(true);
		if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
		{
				$this->SubmissionCondition();
		}
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$WorkflowModuleUID."' AND IsCleared = 0)",NULL,FALSE);
					// remove reversed orders
		$this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$WorkflowModuleUID);
		//Order Queue Permission
		$this->OrdersPermission($WorkflowModuleUID,'USERASSIGNEDCOUNT','',['UserUID'=>$UserUID]);

		$this->db->where('tOrderAssignments.AssignedDatetime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);

		$query = $this->db->count_all_results();
		return $query;
	}
	/**
	*Function getting total Queue users for Que Report in each module
	*@author Harini bangari <harini.bangari@avanzegroup.com>
	*@since Wednesday 26 August 2020.
	*/
	function QueUsersByWorkflowModule($WorkflowModuleUID)
	{
		$function_call    = $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
		if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
		{
		     $this->Submissions_Orders_Model->SubmissionCondition();
		}
  		$this->db->select("1,`mUsers`.`UserName`,`mUsers`.`UserUID`");
  		$this->$function_call(false,['OrderByexception'=>true]);
  		$this->db->where("(tOrderAssignments.AssignedToUserUID IS NOT NULL OR tOrderAssignments.AssignedToUserUID != '' )",NULL,FALSE);
  		$this->db->where("(tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
  		
  		//Skip Users
		$SkippedUsers = $this->config->item('ReportSkippedUsers');
		if (!empty($SkippedUsers)) {

			$this->db->where_not_in('tOrderAssignments.AssignedToUserUID',$SkippedUsers);
		}

  		$this->db->group_by("mUsers.UserUID");
  		$result=$this->db->get()->result();
  		//print_r($this->db->last_query());exit;
  		return $result;
	}

	/**
	*Function Esclation 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 26 August 2020.
	*/
	function GetEsclationOrders()
	{
		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderImport', 'tOrders.OrderUID=tOrderImport.OrderUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrders.OrderUID = tOrderWorkflows.OrderUID', 'left');
		$this->db->join('tOrderHighlights', 'tOrders.OrderUID = tOrderHighlights.OrderUID AND tOrderHighlights.IsCleared = 0', 'left');

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->group_by('tOrders.OrderUID');
	}

	/**
    *Function Get workflow sub queue details 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Thursday 27 August 2020.
    */
	public function FetchWorkflowSubQueues($WorkflowModuleUID)
	{
		$this->db->select('mQueues.QueueUID, mQueues.QueueName');
		$this->db->from('mQueues');
		$this->db->where('mQueues.WorkflowModuleUID', $WorkflowModuleUID);
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('mQueues.Active', 1);
		return $this->db->get()->result();
	}

	/**
	*Function getting Assigned Status of users for Que Report in each module
	*@author Harini bangari <harini.bangari@avanzegroup.com>
	*@since Wednesday 27 August 2020.
	*/
	function AssignedStatusByWorkflowModule($WorkflowModuleUID, $FromDate = false, $ToDate = false)
	{
		if($FromDate == false){
			$FromDate = date('Y-m-d 00:00:00');		
			$ToDate = date('Y-m-d 23:59:59');			
		}else{
			$FromDate = date('Y-m-d H:i:s', strtotime($FromDate));
			$ToDate = date('Y-m-d 23:59:59', strtotime($ToDate));		
		}

		$users=$this->QueUsersByWorkflowModule($WorkflowModuleUID);
		$function_call    = $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];

		if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
		{
		     $this->Submissions_Orders_Model->SubmissionCondition();
		}
		if(!empty($users)){
		$i=1;
        foreach ($users as $ukey => $user) { 
        	//$user->UserUID=5;
        	$this->db->select("1");
  			$this->$function_call(true);

  			$this->db->where("(tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
  			$this->db->where("tOrderAssignments.AssignedToUserUID ",$user->UserUID);
  			// $this->db->where("tOrderAssignments.AssignedByUserUID ",$user->UserUID);

  			$this->db->where("tOrderAssignments.WorkflowModuleUID ",$WorkflowModuleUID);
  			$this->db->where(array('tOrders.CustomerUID'=>$this->parameters['DefaultClientUID'],'tOrderAssignments.WorkflowModuleUID'=>$WorkflowModuleUID));
			$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);
			$this->db->where('tOrderAssignments.AssignedDatetime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);

  			$result[$i]['self-assigned']=$this->db->count_all_results();
  			$result[$i]['UserName']=$user->UserName;

  			$this->db->select("1");
  			$this->$function_call(true);
  			$this->db->where("tOrderAssignments.WorkflowModuleUID ",$WorkflowModuleUID);
  			$this->db->where("tOrderAssignments.AssignedToUserUID ",$user->UserUID);

  			$this->db->where("(tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
			$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);  			

  			$this->db->where(array('tOrders.CustomerUID'=>$this->parameters['DefaultClientUID'],'tOrderAssignments.WorkflowModuleUID'=>$WorkflowModuleUID));
			$this->db->where('tOrderAssignments.AssignedDatetime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);

  			$result[$i]['not_reviewed']=$this->db->count_all_results();
  			$i++;
        }
       
       }
       return $result;
	}
	/**
	*Function getting mQueues By Workflow
	*@author Harini bangari <harini.bangari@avanzegroup.com>
	*@since Wednesday 27 August 2020.
	*/
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
	/**
	*Function getting Assigned Status of users for Que Report in each module
	*@author Harini bangari <harini.bangari@avanzegroup.com>
	*@since Wednesday 27 August 2020.
	*/
	function QueStatusByWorkflowModule($WorkflowModuleUID, $FromDate = false, $ToDate = false)
	{
		if($FromDate == false){
			$FromDate = date('Y-m-d 00:00:00');		
			$ToDate = date('Y-m-d 23:59:59');			
		}else{
			$FromDate = date('Y-m-d H:i:s', strtotime($FromDate));
			$ToDate = date('Y-m-d 23:59:59', strtotime($ToDate));		
		}

		$this->load->model('Reports/Reports_Model');
		$mQueues =$this->GetmQueuesByWorkFlow($WorkflowModuleUID);
		$users=$this->QueUsersByWorkflowModule($WorkflowModuleUID);
		$function_call    = $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
		if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
		{
		     $this->Submissions_Orders_Model->SubmissionCondition();
		}

		

		if(!empty($users)){
			$assigned_cnt=0;
			$i=1;
		foreach ($users as $ukey => $user) {
			if($this->RoleType != $this->config->item('Internal Roles')['Agent']){
	  			$assigned_cnt=$this->Common_Model->totAssignedCntByWorkflowModuleUID($WorkflowModuleUID,$user->UserUID, $FromDate, $ToDate);
			}
			//print_r($user);exit;
        	if(!empty($mQueues)){
        		foreach($mQueues as $key => $queue){
        			$this->Common_Model->$function_call(false);
					$this->db->select('1');
        			 $this->db->join("tOrderQueues","tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus = 'Pending'");
    				$this->db->join("mQueues","tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '".$queue['QueueUID']."' and mQueues.WorkflowModuleUID='".$WorkflowModuleUID."'");
					$this->db->where(array('tOrderQueues.QueueUID'=>$queue['QueueUID'], ' tOrders.CustomerUID'=>$this->parameters['DefaultClientUID'],'tOrderAssignments.WorkflowModuleUID'=>$WorkflowModuleUID,'tOrderAssignments.AssignedToUserUID'=>$user->UserUID,'tOrderQueues.QueueStatus'=>'Pending'));
					$this->db->where("(tOrderAssignments.WorkflowStatus NOT IN (".$this->config->item('WorkflowStatus')['Onhold'].",".$this->config->item('WorkflowStatus')['Completed']."))",NULL,FALSE);
					$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);
					$this->db->where('tOrderAssignments.AssignedDatetime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
		 			$row_res= $this->db->get()->num_rows();;
        			//echo $row_res->QueCount;
        		   $result[$i][$queue['QueueUID']]=$row_res;

        		}

        	}

        	$result[$i]['UserName']=$user->UserName;
        	if($this->RoleType != $this->config->item('Internal Roles')['Agent']){
	 			if($assigned_cnt != 0){
		 			$result[$i]['UserUID']=$row->UserUID;
		 			$result[$i]['assigned_cnt']=$assigned_cnt;
		 	    }
	 		}
        	$i++;
         }

		}
		return $result;

	}

	/**
	*Function Check if Re-Work Enabled 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 28 August 2020.
	*/
	function IsReWorkEnabled($OrderUID, $WorkflowModuleUID)
	{
		$torderworkflowrow = $this->db->select('IsReWorkEnabled')->where(array('OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE))->get('tOrderReWork')->row();

		if (isset($torderworkflowrow->IsReWorkEnabled) && $torderworkflowrow->IsReWorkEnabled == 1) {
			return true;
		}

		return false;
	}

	/**
	*Function NBS Required 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 31 August 2020.
	*/
	function get_NBSRequired($StateCode)
	{
		if(empty($StateCode)) {
			return FALSE;
		}

		$this->db->select('*')->from('mStateMatrix');
		$this->db->join('mStates','mStates.StateUID = mStateMatrix.StateUID');
		$this->db->where(array('mStates.StateCode' => $StateCode, 'mStateMatrix.Active'=>STATUS_ONE,'mStateMatrix.CustomerUID'=>$this->parameters['DefaultClientUID']));
		return $this->db->get()->row();

	}	

	/**
	*Function today's completed orders count of intivitual/all  
	*@author Santhiya <santhiya.m@avanzegroup.com>
	*@since Thursday 2 September 2020.
	*/
	public function OrdersCompletedCountToday($WorkflowModuleUID, $by = 'all') {

		$FromDate = date('Y-m-d 00:00:00');
		$ToDate = date('Y-m-d 23:59:59');

		$this->db->select('1');
		$this->db->from('tOrderAssignments');

		$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);

		//condition for workup
		if($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] ) {

			//Join tOrderWorkflows
			$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrderAssignments.OrderUID AND tOrderWorkflows.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID');
			$this->db->where('(tOrderWorkflows.IsCountDisabled <> 1 OR tOrderWorkflows.IsCountDisabled IS NULL)',NULL, FALSE);

			$this->db->group_start();
			$this->db->group_start();

			$WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');
			foreach ($WorkupSubQueueComplete as $key => $QueueUID) {
				//self completed count
				$SELFWHERE = '';
				if($by == 'self') {
					$SELFWHERE = ' AND (tOrderQueues.RaisedByUserUID = '.$this->loggedid.')';
				}		

				if ($key === 0) {
					$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrderAssignments.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending' AND tOrderQueues.RaisedDateTime BETWEEN '".$FromDate. "' AND '".$ToDate."' ".$SELFWHERE.")",NULL,FALSE);
				} else {
					$this->db->or_where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrderAssignments.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending' AND tOrderQueues.RaisedDateTime BETWEEN '".$FromDate. "' AND '".$ToDate."' ".$SELFWHERE.")",NULL,FALSE);
				}
			}
			$this->db->group_end();
			$this->db->or_group_start();
			$this->db->where('tOrderAssignments.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
			$this->db->where('tOrderAssignments.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
			//self completed count
			if($by == 'self') {
				$this->db->where('tOrderAssignments.CompletedByUserUID', $this->loggedid);
			}

			$this->db->group_end();
			$this->db->group_end();
		}	else {

			//self completed count
			if($by == 'self') {
				$this->db->where('tOrderAssignments.CompletedByUserUID', $this->loggedid);
			}

			$this->db->where('tOrderAssignments.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
			$this->db->where('tOrderAssignments.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);

		}

		//Skip Users
		$SkippedUsers = $this->config->item('ReportSkippedUsers');
		if (!empty($SkippedUsers)) {

			$this->db->group_start();
			$this->db->where_not_in('tOrderAssignments.CompletedByUserUID',$SkippedUsers);
			$this->db->or_where('tOrderAssignments.CompletedByUserUID IS NULL',NULL,FALSE);
			$this->db->group_end();
		}		

		$this->db->group_by('tOrderAssignments.OrderUID');
		return $this->db->count_all_results();


		/*$this->db->select('1');
		$this->db->from('tOrderAssignmentsHistory');
		$this->db->where('tOrderAssignmentsHistory.WorkflowModuleUID', $WorkflowModuleUID);
		if($by == 'self'){
			$this->db->where('tOrderAssignmentsHistory.CompletedByUserUID', $UserUID);
		}
		if($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] ){
			$this->db->group_start();
			$WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');
			foreach ($WorkupSubQueueComplete as $key => $QueueUID) {						
				if ($key === 0) {
					$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrderAssignmentsHistory.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
				} else {
					$this->db->or_where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrderAssignmentsHistory.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
				}
			}
			$this->db->group_end();
		}
		$FromDate = date('Y-m-d 00:00:00');
		$ToDate = date('Y-m-d 23:59:59');
		$this->db->where('tOrderAssignmentsHistory.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
		$this->db->group_by('tOrderAssignmentsHistory.OrderUID');
		$HistoryQuery = $this->db->get_compiled_select();

		$query = $this->db->query($HistoryQuery . ' UNION ALL ' . $AssignmentsQuery);
		return $query->num_rows();*/
	}
	/**
	*Function today's reviewed orders count   
	*@author Santhiya <santhiya.m@avanzegroup.com>
	*@since Thursday 8 September 2020.
	*/
	public function TotalViewedCount($WorkflowModuleUID){

		//	Workup workflow following sub queue is completed. Order is taken as completed
		$WorkupCondition = "";
		if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
			
			$WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');
			$WorkupCondition = "AND tOrderQueues.QueueUID NOT IN (" . implode(',', $WorkupSubQueueComplete) . ")";
		}			

		$FromDate = date('Y-m-d 00:00:00');
		$ToDate = date('Y-m-d 23:59:59');
		$this->db->select('1');
		$this->db->from('tOrderAssignments');
		$this->db->where('tOrderAssignments.WorkflowModuleUID', $WorkflowModuleUID);
		$this->db->where('tOrderAssignments.AssignedDatetime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
		$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrderAssignments.OrderUID AND tOrderQueues.RaisedDateTime BETWEEN '".$FromDate. "' AND '".$ToDate."' ".$WorkupCondition." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);

		return $this->db->count_all_results();

	}
	/* *
	* Function for get Alter DocumenttypeUID
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com>
	* @since Date : 28-08-2020
	 */
	function getAlertData()
	{
		$currentDate=date('Y-m-d').' 00:00:00';
		$query = $this->db->query("SELECT DocumenttypeUID from mDocumentTypeAlert WHERE `AlertStartDate` <= '$currentDate' AND `AlertEndDate` >= '$currentDate'");
		$result=$query->result();
		foreach($result as $key => $value){
			$documentTypeUID[]=$value->DocumenttypeUID;
		}
		return array_unique($documentTypeUID);
	}
		
	/* *
	* Function for get Scrolling data
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com>
	* @since Date : 25-07-2020
	 */
	function getScrollingData()
	{
		$this->db->select("Description,Active");
		$this->db->from('mSettings');
		$this->db->where(array('SettingField' => 'Scrolling_Text', 'CustomerUID' => $this->parameters['DefaultClientUID']));
		return $this->db->get()->result();
	}

	/**
	*Function get users based on workflow in completed report 
	*@author Santhiya <santhiya.m@avanzegroup.com>
	*@since Thursday 9 September 2020.
	*/
	function CompletedUsersByWorkflowModule($WorkflowModuleUID)
	{
		$function_call    = $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];
		if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
		{
		     $this->Submissions_Orders_Model->SubmissionCondition();
		}
  		$this->db->select("1,`mUsers`.`UserName`,`mUsers`.`UserUID`");
  		$this->$function_call(false,['OrderByexception'=>true]);
  		$this->db->where("(tOrderAssignments.AssignedToUserUID IS NOT NULL OR tOrderAssignments.AssignedToUserUID != '' )",NULL,FALSE);
  		// $this->db->where("(tOrderAssignments.WorkflowStatus IS NULL OR tOrderAssignments.WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed']." )",NULL,FALSE);
  		$this->db->group_by("mUsers.UserUID");
  		$result=$this->db->get()->result();
  		return $result;
	}

	/**
	*Function getting Assigned Status of users for Que Report in each module
	*@author Harini bangari <harini.bangari@avanzegroup.com>
	*@since Wednesday 27 August 2020.
	*/
	function CompletedAndResolvedStatusByWorkflowModule($WorkflowModuleUID, $FromDate = false, $ToDate = false)
	{
		if($FromDate == false){
			$FromDate = date('Y-m-d 00:00:00');		
			$ToDate = date('Y-m-d 23:59:59');			
		}else{
			$FromDate = date('Y-m-d H:i:s', strtotime($FromDate));
			$ToDate = date('Y-m-d 23:59:59', strtotime($ToDate));		
		}

		$users=$this->QueUsersByWorkflowModule($WorkflowModuleUID);
		$function_call = $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];

		if($WorkflowModuleUID == $this->config->item('Workflows')['Submissions'])
		{
		     $this->Submissions_Orders_Model->SubmissionCondition();
		}
		if(!empty($users)){
		$i=1;
			
		$Workflows_EliminatedMilestones = $this->config->item('Workflows_EliminatedMilestones');
        foreach ($users as $ukey => $user) { 

        	$this->db->select("1");
  			$this->db->from('tOrders');
  			$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
  			$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
  			$this->db->where('tOrderAssignments.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
  			$this->db->where("tOrderAssignments.AssignedToUserUID ",$user->UserUID);
  			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
  			$this->db->where('tOrderWorkflows.IsReversed', STATUS_ZERO);
			$this->db->where('tOrderAssignments.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
			
			$this->db->group_start();
			$this->db->where('tOrders.MilestoneUID IS NULL', NULL, FALSE);
			$this->db->or_where_not_in('tOrders.MilestoneUID', $Workflows_EliminatedMilestones);
			$this->db->group_end();

  			$result[$i]['Completed']=$this->db->count_all_results();
  			$result[$i]['UserName']=$user->UserName;

        	$this->db->select("1");
  			$this->db->from('tOrders');
  			$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
  			$this->db->join('tOrderWorkflows','tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '.$WorkflowModuleUID,'LEFT');
  			$this->db->where('tOrderAssignments.WorkflowStatus', $this->config->item('WorkflowStatus')['Completed']);
  			$this->db->where("tOrderAssignments.AssignedToUserUID ",$user->UserUID);
  			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
  			$this->db->where('tOrderWorkflows.IsReversed', STATUS_ONE);
			$this->db->where('tOrderAssignments.CompleteDateTime BETWEEN "'.$FromDate. '" AND "'.$ToDate.'"', NULL, false);
			
			$this->db->group_start();
			$this->db->where('tOrders.MilestoneUID IS NULL', NULL, FALSE);
			$this->db->or_where_not_in('tOrders.MilestoneUID', $Workflows_EliminatedMilestones);
			$this->db->group_end();

  			$result[$i]['Resolved']=$this->db->count_all_results();
  			$i++;
        }
       
       }
       return $result;
	}

	/**
	*Function PayOff Update Orders 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 11 September 2020.
	*/
	function GetPayOffUpdateOrders()
	{
		$Workflows_EliminatedMilestones = $this->config->item('Workflows_EliminatedMilestones');

		// Get PayOff Date
		$PayOff_Date = $this->GetPayOffDate($this->parameters['DefaultClientUID']);

		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderImport', 'tOrders.OrderUID=tOrderImport.OrderUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrders.OrderUID = tOrderWorkflows.OrderUID', 'left');

		if (!empty($PayOff_Date)) {
			
			$this->db->where("DATE(STR_TO_DATE(tOrderImport.LastPaymentReceivedDate, '%m/%d/%Y')) = DATE(STR_TO_DATE('".$PayOff_Date."', '%m/%d/%Y'))", NULL, FALSE);
		} else {

			$this->db->where("DATE(STR_TO_DATE(tOrderImport.LastPaymentReceivedDate, '%m/%d/%Y')) = DATE(DATE_ADD(NOW(), INTERVAL -1 DAY))", NULL, FALSE);
		}		
		
		$this->db->group_start();
		$this->db->where('tOrders.MilestoneUID IS NULL', NULL, FALSE);
		$this->db->or_where_not_in('tOrders.MilestoneUID', $Workflows_EliminatedMilestones);
		$this->db->group_end();

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		$this->db->group_by('tOrders.OrderUID');
	}

	/**
	*Function Get Workflow Queue Details
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 18 September 2020.
	*/
	public function UpdateWorkupQueueCompleteLogic($OrderUID, $WorkflowModuleUID, $QueueUID)
	{
		$WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');
		$WorkupCondition = "tOrderQueues.QueueUID IN (" . implode(',', $WorkupSubQueueComplete) . ")";

		// Get Last Order Reverse UID
		$OrderReverseUID = $this->db->select('MAX(OrderReverseUID) AS OrderReverseUID')->from('tOrderReverse')->where(array('OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID))->get()->row()->OrderReverseUID;
		
		$OrderReverseUID_Where = "";
		if (!empty($OrderReverseUID)) {
			
			$OrderReverseUID_Where = "AND tOrderReverse.OrderReverseUID = ".$OrderReverseUID;
		}

		$this->db->select('1 AS IsCountDisabled');		
		$this->db->from('tOrderWorkflows');
		$this->db->join('tOrderQueues','tOrderQueues.OrderUID = tOrderWorkflows.OrderUID');
		$this->db->join('tOrderReverse','tOrderReverse.OrderUID = tOrderWorkflows.OrderUID AND tOrderReverse.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID '.$OrderReverseUID_Where,'left');
		$this->db->where('tOrderWorkflows.OrderUID',$OrderUID);
		$this->db->where('tOrderWorkflows.WorkflowModuleUID',$WorkflowModuleUID);
		$this->db->where_in('tOrderQueues.QueueUID',$this->config->item('WorkupSubQueueComplete'));

		$this->db->group_start();

		$this->db->group_start();

		$this->db->where("tOrderWorkflows.IsReversed = 0 AND DATE_FORMAT(tOrderQueues.RaisedDateTime, '%Y-%m-%d') > DATE(DATE_ADD(NOW(), INTERVAL -7 DAY)) AND DATE_FORMAT(tOrderQueues.RaisedDateTime, '%Y-%m-%d') <> DATE(NOW())");

		$this->db->group_end();

		$this->db->or_group_start();

		$this->db->where("tOrderWorkflows.IsReversed = 1 AND DATE_FORMAT(tOrderReverse.ReversedDateTime, '%Y-%m-%d') <= DATE_FORMAT(tOrderQueues.RaisedDateTime, '%Y-%m-%d') AND DATE_FORMAT(tOrderQueues.RaisedDateTime, '%Y-%m-%d') > DATE(DATE_ADD(NOW(), INTERVAL -7 DAY)) AND DATE_FORMAT(tOrderQueues.RaisedDateTime, '%Y-%m-%d') <> DATE(NOW())");

		$this->db->group_end();

		$this->db->group_end();

		$this->db->group_by('tOrderQueues.OrderUID');

		$result = $this->db->get()->row();

		$IsCountDisabled = ($result->IsCountDisabled == 1) ? 1 : 0;
		
		$this->Common_Model->save('tOrderWorkflows', array('IsCountDisabled'=>$IsCountDisabled), ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);
	}

	/**
	*Function Insert Order Reverse Data 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 21 September 2020.
	*/
	public function InserttOrderReverse($tOrderReverseData)
	{
		$this->db->insert('tOrderReverse', $tOrderReverseData);
		return true;
	}

	/**
	*Function Get Workflow Documents 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 25 September 2020.
	*/
	function GetWorkflowDocuments($WorkflowModuleUID, $DocumentUID = FALSE)
	{
		$this->db->select('mWorkflowDocuments.DocumentUID, mWorkflowDocuments.DocumentName, mWorkflowDocuments.DocumentURL, mWorkflowDocuments.UploadedDateTime, mUsers.UserName as UploadedUser');
		$this->db->from('mWorkflowDocuments');
		$this->db->join('mUsers', 'mWorkflowDocuments.UploadedByUserUID = mUsers.UserUID');
		$this->db->where(array('mWorkflowDocuments.WorkflowModuleUID' => $WorkflowModuleUID));
		if (!empty($DocumentUID)) {
			$this->db->where(array('mWorkflowDocuments.DocumentUID' => $DocumentUID));			
		}	
		$query = $this->db->get();
		return $query->result();
	}

	/**
	*Function Get PayOff Date 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 25 September 2020.
	*/
	public function GetPayOffDate($CustomerUID)
	{
		return $this->otherdb->select('PayOff_Date')->from('mCustomer')->where('CustomerUID',$CustomerUID)->get()->row()->PayOff_Date;
	}

	/**
	*Function Fetch Sub Queue Category Details 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 29 September 2020.
	*/
	function GetSubQueueCategory($WorkflowModuleUID) {
		return $this->db->select('*')->from('mSubQueueCategory')->where('WorkflowModuleUID',$WorkflowModuleUID)->get()->result_array();
	}


	/**
	*Function InitialUnderWriting Workflow Orders Begin
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 01 October 2020.
	*/
	function GetInitialUnderWritingQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['InitialUnderWriting'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['InitialUnderWriting']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["InitialUnderWriting"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['InitialUnderWriting'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['InitialUnderWriting'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['InitialUnderWriting']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}


		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['InitialUnderWriting'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['InitialUnderWriting'] . "' 
				THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}
	// InitialUnderWriting Workflow Orders End

	
	/**
	*Function ConditionwithApproval Workflow Orders Begin
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 01 October 2020.
	*/
	function GetConditionwithApprovalQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['ConditionwithApproval'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['ConditionwithApproval']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["ConditionwithApproval"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['ConditionwithApproval'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['ConditionwithApproval'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['ConditionwithApproval']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}


		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['ConditionwithApproval'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['ConditionwithApproval'] . "' 
				THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}
	// ConditionwithApproval Workflow Orders End


	/** 
	* Underwriting Workflow Orders Begin
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
	* @since Wednesday 11 March 2020 
	*/
	function GetUnderwritingQueue($filterexception = true,$Conditions = [])
	{
		$WorkflowModuleUID = $this->config->item('Workflows')['Underwriting'];

		$status[] = $this->config->item('keywords')['Cancelled'];

		/*########## GEt Workflow results using another db connection ############*/

		/** 
		 * Dependent Workflows
		 * @author Praveen Kumar <praveen.kumar@avanzegroup.com> 
		 * @since Wednesday 11 March 2020 
		 */

		$DependentWorkflowModuleUID = $this->getDependentworkflows($this->config->item('Workflows')['Underwriting']);

		if (!empty($DependentWorkflowModuleUID)) {

			
			$this->otherdb->select('mWorkFlowModules.*');
			$this->otherdb->from('mWorkFlowModules');
			if (!empty($DependentWorkflowModuleUID)) {
				$this->otherdb->where_in('mWorkFlowModules.WorkflowModuleUID', $DependentWorkflowModuleUID);
			}
			$mWorkFlowModules = $this->otherdb->get()->result();



			/*############ Get SubQuery for Previous workflow completed or not available Starts ############*/
			$this->otherdb->select('tOrders.OrderUID')->from('tOrders');
			foreach ($mWorkFlowModules as $key => $value) {

				$this->otherdb->join("tOrderWorkflows AS " .  "TW_" . $value->SystemName,   "TW_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND " . "TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1", "LEFT");

				$this->otherdb->join("tOrderAssignments AS " . "TOA_" . $value->SystemName,  "TOA_" . $value->SystemName . ".OrderUID = tOrders.OrderUID AND TOA_" . $value->SystemName . ".WorkflowModuleUID = '" . $value->WorkflowModuleUID . "'", "LEFT");
			}

			foreach ($mWorkFlowModules as $key => $value) {
				$Case_Where = "CASE WHEN TW_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " AND TW_" . $value->SystemName . ".IsPresent = 1 THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowModuleUID = " . $value->WorkflowModuleUID . " THEN 
				CASE WHEN TOA_" . $value->SystemName . ".WorkflowStatus = " . $this->config->item('WorkflowStatus')['Completed'] . " THEN 
				TRUE 
				ELSE FALSE END
				ELSE FALSE END
				ELSE TRUE END";
				$this->otherdb->group_start();
				$this->otherdb->where($Case_Where, NULL, FALSE);
				$this->otherdb->group_end();
			}

			$this->otherdb->where_not_in('tOrders.StatusUID', $status);


			$previous_filtered_orders_sql = $this->otherdb->get_compiled_select();
		}

		/*############ Get SubQuery for Previous workflow completed or not available Ends ############*/

		$this->db->select('tOrderWorkflows.WorkflowModuleUID');

		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Underwriting"] . '"', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = "' . $this->config->item('Workflows')['Underwriting'] . '"');
		$this->db->join('mUsers', 'tOrderAssignments.AssignedToUserUID = mUsers.UserUID', 'left');

		if (!isset($Conditions['OrderByexception']))
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID);
		else
			$this->AllWorkflowQueue_CommonQuery($WorkflowModuleUID,'','exclude');

		/*Check Doc Case Enabled*/
		$this->db->where("NOT  EXISTS (SELECT 1 FROM tOrderDocChase WHERE `tOrders`.`OrderUID` = `tOrderDocChase`.`OrderUID` AND `tOrderDocChase`.`WorkflowModuleUID` = " . $this->config->item('Workflows')['Underwriting'] . "  AND tOrderDocChase.IsCleared = 0)", NULL, FALSE);

		//join followup


		//filter order when withdrawal enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderWithdrawal WHERE tOrderWithdrawal.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);
		//filter order when escalation enabled
		$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderEsclation WHERE tOrderEsclation.OrderUID = tOrders.OrderUID AND IsCleared = 0)", NULL, FALSE);

		$this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Underwriting']);
		$this->db->where_not_in('tOrders.StatusUID', $status);

		if (isset($previous_filtered_orders_sql) && !empty($previous_filtered_orders_sql)) {
			$this->db->where('tOrders.OrderUID IN (' . $previous_filtered_orders_sql . ')');
		}

		/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
		/** @date Friday 13 March 2020 **/
		/** @description Header Client Selection **/
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}


		/** @author Parthasarathy <parthasarathy.m@avanzegroup.com> **/
		/** @date Friday 15 May 2020 **/
		/** @description Filter orders having queue exception **/
		if ($filterexception == true) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = mQueues.QueueUID AND tOrderQueues.QueueStatus = 'Pending' AND mQueues.CustomerUID = tOrders.CustomerUID AND mQueues.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID)", NULL, FALSE);
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/
		if (!isset($Conditions['filtercompletedorders'])) {
			$this->db->group_start();
			$this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '" . $this->config->item('Workflows')['Underwriting'] . "' AND tOrderWorkflows.IsPresent = '" . STATUS_ONE . "' THEN 
				CASE WHEN tOrderAssignments.WorkflowModuleUID = '" . $this->config->item('Workflows')['Underwriting'] . "' 
				THEN CASE WHEN  tOrderAssignments.WorkflowStatus = '" . $this->config->item('WorkflowStatus')['Completed'] . "' THEN FALSE ELSE TRUE END ELSE TRUE END
				ELSE FALSE END", NULL, FALSE);
			$this->db->group_end();
		}


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

		/*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
		/*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
		if (!isset($Conditions['OrderByexception']))
			$this->db->group_by('tOrders.OrderUID');
	}

	/**
	*Function Removes special chars and spaces 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 05 October 2020.
	*/
	function RemoveSpecialCharsAndSpaces($string) {
	   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

	/**
	*Function fetch state matrix 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 05 October 2020.
	*/
	public function get_orderinfo($OrderUID) {
		$this->db->select('PropertyStateCode,TitleInsuranceCompanyName,PropertyCountyName');
		$this->db->from('tOrders');
		$this->db->join('tOrderImport', 'tOrderImport.OrderUID = tOrders.OrderUID', 'left');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	/**
	*Function fetch state matrix 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 05 October 2020.
	*/

	public function get_statematrix($PropertyStateCode)
	{

		if(!empty($PropertyStateCode)) {

			$this->db->select('*')->from('mStateMatrix');
			$this->db->join('mStates','mStates.StateUID = mStateMatrix.StateUID');
			$this->db->where(array('mStates.StateCode' => $PropertyStateCode, 'mStateMatrix.Active'=>STATUS_ONE,'mStateMatrix.CustomerUID'=>$this->parameters['DefaultClientUID']));
			return $this->db->get()->row();
		}
	}

	/**
	*Function Get Workflow Issue Checklist Variable Name 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 06 October 2020.
	*/
	function getWorkflowIssueChecklists_variable($column) {
		if (!empty($column->ChecklistIssueWorkflowUID)) {

			if (!empty($column->ChecklistIssueSubQueueUID)) {
				
				$WorkflowIssueChecklists = $column->ColumnName."_".$column->ChecklistIssueWorkflowUID."_".$column->ChecklistIssueSubQueueUID;

			} else {

				$WorkflowIssueChecklists = $column->ColumnName."_".$column->ChecklistIssueWorkflowUID;

			}					 		

		} else {

			$WorkflowIssueChecklists = $column->ColumnName."_".$column->QueueColumnUID;

		}
		return $WorkflowIssueChecklists;
	}

	/**
	*Function Worklfow Queue Orders 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 08 October 2020.
	*/
	function GetWorkflowQueueReportQueueOrders()
	{

		$this->db->from('tOrders');

		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mMilestone', 'tOrders.MilestoneUID = mMilestone.MilestoneUID', 'left');
		$this->db->join('mProducts', 'tOrders.ProductUID=mProducts.ProductUID', 'left');
		$this->db->join('tOrderImport', 'tOrders.OrderUID=tOrderImport.OrderUID', 'left');
		$this->db->join('tOrderWorkflows', 'tOrders.OrderUID = tOrderWorkflows.OrderUID', 'left');

		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('tOrders.CustomerUID', $this->parameters['DefaultClientUID']);
		}

		// Order exist for subqueues
		$this->db->where("EXISTS (SELECT 1 FROM mQueues JOIN tOrderQueues ON mQueues.QueueUID = tOrderQueues.QueueUID WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND mQueues.CustomerUID = tOrders.CustomerUID)", NULL, FALSE);

		$this->db->group_by('tOrders.OrderUID');
	}

	/**
	*Function Check Order in Sub Queues 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Saturday 10 October 2020.
	*/
	public function CheckOrderExistInSubQueues($OrderUID, $WorkflowModuleUID)
	{
		$query = $this->db->get_where('tOrderSubQueues', array('OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'SubQueueStatus' => 'Pending'));
		if ($query->num_rows() === 0) {
			return true;
		} else {
			return false;
		}
	}

	// Expired Complete Orders
	function ExpiredCompletecount_all($post = FALSE)
	{
		if ($post == FALSE) {
			//Get WorkflowModuleUID
			$controller = $this->uri->segment(1);
			if (isset($this->config->item('workflowcontroller')[$controller])) {
				$WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
				$post['WorkflowModuleUID'] = $WorkflowModuleUID;
			} else {
				return;
			}
		}

		$this->db->select("1");

		/*^^^^^ Get New Orders Query ^^^^^*/
		$function_call	= $this->config->item('WorkflowDetails')[$post['WorkflowModuleUID']]['function_call'];
		$this->$function_call(false,['filtercompletedorders'=>true]);
		
		// Checklist Expiry orders common function
		$this->checklistExpiryCompleteOrdersConditions($post['WorkflowModuleUID']);

		//Order Queue Permission
		$this->OrdersPermission($post['WorkflowModuleUID'],'EXPIREDCOUNT');

		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$this->advanced_search($post);
		}

		$this->FilterByProjectUser($this->RoleUID,$this->loggedid);	

		$query = $this->db->count_all_results();
		return $query;
	}

	// Expired Complete Orders Filter Function
	function ExpiredCompletecount_filtered($post)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->DynamicColumnsCommonQuery($post['WorkflowModuleUID']);
		}
		$this->db->select("1");

		/*^^^^^ Get New Orders Query ^^^^^*/
		$function_call	= $this->config->item('WorkflowDetails')[$post['WorkflowModuleUID']]['function_call'];
		$this->$function_call(false,['filtercompletedorders'=>true]);
		
		// Checklist Expiry orders common function
		$this->checklistExpiryCompleteOrdersConditions($post['WorkflowModuleUID']);

		//Order Queue Permission
		$this->OrdersPermission($post['WorkflowModuleUID'],'EXPIREDCOUNT');

		$this->FilterByProjectUser($this->RoleUID,$this->loggedid);

	  	// Advanced Search
		if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		}
	  	// Advanced Search

	 	// Datatable Search
		$this->WorkflowQueues_Datatable_Search($post);

	  	// Datatable OrderBy
		$this->WorkflowQueues_Datatable_OrderBy($post);		

		$query = $this->db->get();
		return $query->num_rows();
	}


	function ExpiredCompleteOrders($post)
	{
		$this->DynamicColumnsCommonQuery($post['WorkflowModuleUID']);
		$this->db->select('tOrderImport.*, tOrderPropertyRole.*, mUsers.UserName AS AssignedUserName, tOrderAssignments.AssignedDatetime');

		$this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName,tOrders.LoanNumber, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID");
		$this->db->select('tOrders.LastModifiedDateTime');
		$this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime, tOrderAssignments.WorkflowStatus');

		// Expiry completed  DateTime under expiry order complete
		$this->db->select('MAX(tOrderChecklistExpiryComplete.CompletedDateTime) AS ExpirycompletedDateTime');

		/*^^^^^ Get MyOrders Query ^^^^^*/
		$function_call	= $this->config->item('WorkflowDetails')[$post['WorkflowModuleUID']]['function_call'];
		$this->$function_call(false,['filtercompletedorders'=>true]);
		
		// Checklist Expiry orders common function
		$this->checklistExpiryCompleteOrdersConditions($post['WorkflowModuleUID']);

		// tOrderChecklistExpiryComplete table join
		$this->db->join('tOrderChecklistExpiryComplete','tOrderChecklistExpiryComplete.OrderUID = tOrders.OrderUID AND tOrderChecklistExpiryComplete.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID','left');

		//Order Queue Permission
		$this->OrdersPermission($post['WorkflowModuleUID'],'EXPIREDCOUNT');

		$this->FilterByProjectUser($this->RoleUID,$this->loggedid);

	  	// Advanced Search
		if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
			$filter = $this->advanced_search($post);
		}
	  	// Advanced Search

	  	// Datatable Search
		$this->WorkflowQueues_Datatable_Search($post);

	  	// Datatable OrderBy
		$this->WorkflowQueues_Datatable_OrderBy($post);


		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$MetricsOrderBy = $this->getMetricsDependentworkflows($this->parameters['DefaultClientUID'], $post['WorkflowModuleUID']);
		if (!empty($MetricsOrderBy)) {
			$this->db->_protect_identifiers=false;
			$this->db->order_by($MetricsOrderBy);
			$this->db->_protect_identifiers=true;
		}

		$this->db->order_by('OrderEntryDatetime');
		$output = $this->db->get();

		return $output->result();
	}

	/**
	*Function Expired checklist badge 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Wednesday 29 July 2020.
	*/
	function get_ExpiredChecklistSubQueueAging($myorders,$WorkflowModuleUID, $SubQueueAging = '')
	{
		$daysleft = [];
		$workflowchecklist = isset($this->config->item('Expired_Checklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$WorkflowModuleUID] : NULL;

		$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$WorkflowModuleUID] : NULL;

		if(isset($workflowchecklist) && $workflowchecklist && !empty($WorkflowModuleUID)) {

			if(is_array($workflowchecklist)) {

				foreach ($workflowchecklist as $checklistkey => $checklistuid) {

					if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

					} else {

						if ($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {
							
							$daysleft[] = isset($myorders->{'Expired_Checklist_DocumentDate_'.$checklistuid}) && !empty($myorders->{'Expired_Checklist_DocumentDate_'. $checklistuid}) ? $this->Get_DynamicSubQueueAging($myorders->{'Expired_Checklist_DocumentDate_'. $checklistuid}, $SubQueueAging, ['IsCheckistExpirySubQueueAging'=>TRUE]) : '';
						} else {

							$daysleft[] = isset($myorders->{'Expired_Checklist_DocumentExpiryDate_'.$checklistuid}) && !empty($myorders->{'Expired_Checklist_DocumentExpiryDate_'. $checklistuid}) ? $this->Get_DynamicSubQueueAging($myorders->{'Expired_Checklist_DocumentExpiryDate_'. $checklistuid}, $SubQueueAging, ['IsCheckistExpirySubQueueAging'=>TRUE]) : '';
						}
						
					}
					
				}
			}
		}

		// Remove Empty Array
		foreach ($daysleft as $key => $value) {
			if (is_null($value) || $value == '')
				unset($daysleft[$key]);
		}

		return !empty($daysleft) ? array_values($daysleft)[0] : '';
	}

	/**
	*Function Get Dynamic Sub Queue Aging 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 27 October 2020.
	*/
	function Get_DynamicSubQueueAging($RaisedDateTime, $SubQueueAging, $Conditions = []) {

		if (isset($Conditions['IsCheckistExpirySubQueueAging'])) {

			if (($SubQueueAging == 'Calendar Days' || empty($SubQueueAging)) && !empty($RaisedDateTime)) {
				
				return $this->TwoDatesDiffence($RaisedDateTime, date("Y-m-d", time()), 1, ['Ignore'=>TRUE, 'IsCheckistExpirySubQueueAging'=>TRUE]);

			} elseif ($SubQueueAging == 'Business Days' && !empty($RaisedDateTime)) {

				return $this->TwoDatesDiffence($RaisedDateTime, date("Y-m-d", time()), 0, ['Ignore'=>TRUE, 'IsCheckistExpirySubQueueAging'=>TRUE]);
			}
		}

		if (($SubQueueAging == 'Calendar Days' || empty($SubQueueAging)) && !empty($RaisedDateTime)) {
				
			return site_datetimeaging($RaisedDateTime);

		} elseif ($SubQueueAging == 'Business Days' && !empty($RaisedDateTime)) {

			return $this->TwoDatesDiffence($RaisedDateTime, date("Y-m-d H:i:s", time()), 0, ['Ignore'=>TRUE]);
		}

		return '';
	}

	/**
	*Function Get Static Queues 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 27 October 2020.
	*/
	function Get_StaticQueues($StaticQueueUIDs) {

		return $this->db->select('StaticQueueTableName')->from('mStaticQueues')->where('ClientUID', $this->parameters['DefaultClientUID'])->where_in('StaticQueueUID', $StaticQueueUIDs, FALSE)->get()->result_array();
	}

	function CheckQueueColumnIsEnabled($StaticQueueUIDs, $QueueUIDs, $TableName = '', $QueueUID = '') {
		if (!empty($StaticQueueUIDs) || !empty($QueueUIDs)) {
			if (!empty($StaticQueueUIDs) && !empty($TableName)) {
				$StaticQueueTableNames = array_column($this->Get_StaticQueues($StaticQueueUIDs), "StaticQueueTableName");
				if (!in_array($TableName, $StaticQueueTableNames)) {
					return TRUE;
				}										
			} elseif (!empty($QueueUIDs) && !empty($QueueUID)) {
				if (!in_array($QueueUID, explode(',', $QueueUIDs))) {
					return TRUE;
				}
			} else {
				return TRUE;
			}										
		}
		return FALSE;
	}

	public function CalculateCategoryTATAging($data, $Conditions = [])
	{
		$this->db->select('tSubQueueCategory.LastModifiedDateTime, mCategoriesTAT.TAT_Aging');
		$this->db->from('tSubQueueCategory');
		$this->db->join('mCategoriesTAT','mCategoriesTAT.SubQueueCategoryUID = tSubQueueCategory.SubQueueCategoryUID AND mCategoriesTAT.CategoryUID = tSubQueueCategory.CategoryUID','left');
		$this->db->where(array(
			'tSubQueueCategory.OrderUID'=>$data['OrderUID'],
			'tSubQueueCategory.SubQueueCategoryUID'=>$data['SubQueueCategoryUID'],
			'tSubQueueCategory.CategoryUID'=>$data['CategoryUID'],
		));
		$result = $this->db->get()->row();

		if (!empty($result) && !empty($result->LastModifiedDateTime)) {
			
			if (($data['SubQueueAging'] == 'Calendar Days' || empty($data['SubQueueAging'])) && !empty($result->LastModifiedDateTime)) {
				
				$daysleft = site_datetimeaging($result->LastModifiedDateTime);

			} elseif ($data['SubQueueAging'] == 'Business Days' && !empty($result->LastModifiedDateTime)) {

				$daysleft = $this->TwoDatesDiffence($result->LastModifiedDateTime, date("Y-m-d", time()), 0);
			}

			if (!empty($result->TAT_Aging) && $daysleft > $result->TAT_Aging && isset($Conditions['UserView'])) {
				
				return $daysleft.'<span class="badgenotification-TATSLAExpired" title="Expired">E</span>';
			}

			return $daysleft;

		}
		return '';
	}

	/**
	*Function DocsOut Aging 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 04 November 2020.
	*/
	function DocsOutAging($dates, $column) {
		$mostRecent= 0;
		foreach($dates as $date){
			$curDate = strtotime($date);
			if ($curDate > $mostRecent) {
				$mostRecent = $curDate;
			}
		}

		if ($mostRecent != 0) {
			$DocsOutAging = $this->Get_DynamicSubQueueAging(date('m/d/Y', $mostRecent), $column->SubQueueAging);
			if ($DocsOutAging < 0) {
				return 0;
			} else {
				return $DocsOutAging;
			}
		} else {
			return NULL;
		}
	}

	/**
	*Function Description 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 18 November 2020.
	*/
	function CheckWorkUpIsCompletedStatus($OrderUID) {
		$WorkupModuleUID = $this->config->item('Workflows')['Workup'];
		$this->db->select('tOrders.OrderUID');
		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments', 'tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' .$WorkupModuleUID. '"', 'left');

		$WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');

		$this->db->group_start();
		foreach ($WorkupSubQueueComplete as $key => $QueueUID) {						
			if ($key === 0) {
				$this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
			} else {
				$this->db->or_where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
			}
		}
				
		$this->db->or_where('tOrderAssignments.WorkflowStatus = '.$this->config->item('WorkflowStatus')['Completed'].' ');		
		$this->db->group_end();

		$this->db->where('tOrders.OrderUID', $OrderUID);

		return $this->db->get()->result();
	}

	/**
	*Function Check KickBack Is Enabled 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Sunday 22 November 2020.
	*/
	function CheckIsKickBackOrder($OrderUID, $WorkflowModuleUID)
	{
		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrderWorkflows WHERE OrderUID = ".$OrderUID." AND WorkflowModuleUID = ".$WorkflowModuleUID." AND (IsKickBack = ".STATUS_ONE." OR IsRework = ".STATUS_ONE.")) as avail");
		return $query->row()->avail;
	}

	/**
	*Function Get workflow name
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Sunday 22 November 2020.
	*/
	public function GetWorkflowModuleNameByWorkflowModuleUID($WorkflowModuleUID)
	{
		$this->db->select('WorkflowModuleName');
		$this->db->from('mWorkFlowModules');
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		return $this->db->get()->row()->WorkflowModuleName;
	}

}