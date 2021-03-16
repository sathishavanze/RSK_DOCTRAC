<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Search_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}


	function Search_IndexingOrders($post)
	{
		$projectfilter = $this->Common_Model->GetProjectCustomers();

		$status = $this->config->item('StackingEnabled');

		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", false);

      /*^^^^^ Get MyOrders Query ^^^^^*/
      $this->Common_Model->GetMyOrdersQueue();


      $this->db->group_start();
      
      $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
      $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
      $this->db->group_end();


      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }


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

		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}


	function Search_DocumentCheckInOrders($post)
	{


		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", false);

		$this->Common_Model->GetDocumentCheckInOrdersQueue();


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

		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get(); 
		return $query->result();
	}




	function Search_AuditOrders($post)
	{


		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", false);

		/*^^^^ Auditing Queue Orders Query ^^^^*/
		$this->Common_Model->GetAuditingQueueOrders();

		if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {

			$this->db->group_start();
			$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
			$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
			$this->db->group_end();
		}


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
		$this->Common_Model->FilterOrderBasedOnRole();
		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}


	function Search_ExceptionOrders($post, $Status)
	{
		if ($Status == 'Indexing Exception') {
			$status[0] = $this->config->item('keywords')['Indexing Exception'];
			$status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
		} else if ($Status == 'Fatal Exception') {
			$status[0] = $this->config->item('keywords')['Fatal Exception'];
			$status[1] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];

		} else if ($Status == 'Non Fatal Exception') {
			$status[0] = $this->config->item('keywords')['Non Fatal Exception'];
			$status[1] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
		}



		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);
		$this->db->from('tOrders');
		$this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');
		$this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
		$this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','left');
		$this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
		$this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
		$this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
		$this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
		$this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
		$this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
		$this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');

		$this->db->join("mCustomerWorkflowModules", "mCustomerWorkflowModules.CustomerUID = mCustomer.CustomerUID AND mCustomerWorkflowModules.ProductUID = tOrders.ProductUID AND mCustomerWorkflowModules.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");


		$this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

		$this->db->group_start();
		$this->db->where("CASE WHEN mCustomerWorkflowModules.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
			CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
			CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
			ELSE TRUE END
			ELSE TRUE END
			ELSE FALSE END");
		$this->db->group_end();

		$this->db->where("mCustomerWorkflowModules.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);

		/*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/


		if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {

			$this->db->group_start();
			$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
			$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
			$this->db->group_end();
		}
		$this->db->where_in('tOrders.StatusUID', $status);


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
		$this->Common_Model->FilterOrderBasedOnRole($query);
		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}

	function ReviewOrders($post)
	{

		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,mProducts.ProductName");
		
		$this->Common_Model->GetReviewOrdersQuery();


		if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
			$this->db->group_start();
			$this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
			$this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
			$this->db->group_end();
		}


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
		
		$query = $this->db->get();
		return $query->result();
	}


	function ExportOrders($post)
	{

		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", false);

		$this->Common_Model->GetExportOrdersQuery();

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
		
		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}

	function ShippingOrders($post)
	{
		$status[0] = $this->config->item('keywords')['Shipping'];


		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", false);
		$this->db->from('tOrders');
		$this->db->join('tOrderAssignment', 'tOrderAssignment.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('mStatus', 'tOrders.StatusUID = mStatus.StatusUID', 'left');
		$this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID', 'left');
		$this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID', 'left');
		$this->db->join('mLender', 'tOrders.LenderUID = mLender.LenderUID', 'left');
		$this->db->join('tOrderDocumentCheckIn', 'tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID', 'left');
		$this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
		$this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
		$this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
		// $this->db->where('mProjectCustomer.IsExport=', 1);
		$this->db->where_in('tOrders.StatusUID', $status);


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
		$this->Common_Model->FilterOrderBasedOnRole($query);
		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}



	function CompletedOrders($post)
	{

		$this->db->select("*,tOrders.LoanNumber,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName, tOrders.OrderUID AS OrderUID,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", false);

		$this->Common_Model->GetCompletedQueueOrders();


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
		
		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}


	function CancelledOrders($post)
	{

		$this->db->select("*,tOrders.LoanNumber,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,tOrderDocumentCheckIn.SettlementAgentName,tOrderDocumentCheckIn.FileNumber,tOrderDocumentCheckIn.IncomingTrackingNumber,tOrderPropertyRole.BorrowerFirstName,tOrderPackage.PackageNumber,mInputDocType.DocTypeName,mProducts.ProductName");
		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", false);

		$this->Common_Model->GetCancelledOrders();

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
		
		$this->db->order_by('tOrders.OrderNumber');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	*Function Dynamic search 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Thursday 20 August 2020.
	*/
	function getsearchorders($post) {
		$table = [];
		//check workflow available
		$Workflows = $this->Common_Model->GetWorkflowDetaiils();
		$WorkflowconfigArray = $this->config->item('WorkflowDetails');
		foreach ($Workflows as $Workflow) {
			foreach ($WorkflowconfigArray as $WorkflowModuleUID => $Workflowconfig) {
				if($WorkflowModuleUID == $Workflow->WorkflowModuleUID) {

					$this->db->select('tOrders.OrderNumber,mCustomer.CustomerName,tOrders.LoanNumber,tOrders.LoanType,mMilestone.MilestoneName,mStatus.StatusColor,mStatus.StatusName,tOrders.PropertyStateCode,tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime,tOrders.LastModifiedDateTime,tOrders.OrderUID,tOrderImport.LoanProcessor,tOrderPropertyRole.BorrowerFirstName,mUsers.UserName');
					$this->db->select('"'.$Workflowconfig['PageBaseLink'] . '" AS redirectionpage',false);
					$this->Common_Model->{$Workflowconfig['function_call']}(false);
					/*Datatable Search*/
					$this->Common_Model->WorkflowQueues_Datatable_Search($post);
					$table[$Workflow->WorkflowModuleName] =  $this->db->get()->result();
				}
			}
		}


		return $table;


	}

}
?>
