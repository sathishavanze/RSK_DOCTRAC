<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Exceptionordersmodel extends MY_Model {
	function __construct()
	{
		parent::__construct();
    $this->loggedid = $this->session->userdata('UserUID');
    $this->UserName = $this->session->userdata('UserName');
    $this->RoleUID = $this->session->userdata('RoleUID');    
	}

		// MyOrders
	  function count_all($Status = "")
	  {
	  	if($Status == 'indexing')
      {
        $status[0] = $this->config->item('keywords')['Indexing Exception'];
        $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
      }
      else if($Status == 'fatal')
      {
         $status[0] = $this->config->item('keywords')['Fatal Exception'];
         $status[1] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];

      }
      else if($Status == 'nonfatal')
      {
        $status[0] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[1] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
      }
  
      if(empty($Status))
      {
        $status[] = $this->config->item('keywords')['Indexing Exception'];
        $status[] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
        $status[] = $this->config->item('keywords')['Fatal Exception'];
        $status[] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];
        $status[] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
      }

  	  $this->db->select("*,tOrders.OrderUID,mInputDocType.DocTypeName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
  		$this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
  		$this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
  		$this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
  		$this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      //$this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');


      $this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");

      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');

      $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

      $this->db->group_start();
      $this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
        CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
        CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
        ELSE TRUE END
        ELSE TRUE END
        ELSE FALSE END", NULL, FALSE);
      $this->db->group_end();

      $this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);

      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

     if($this->RoleUID !=1){
      if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        
        $this->db->group_start();
        $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
        $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
        $this->db->group_end();
      }}else{
           $this->db->where('tOrderAssignments.AssignedToUserUID IS NULL');
      }
      $this->db->where_in('tOrders.StatusUID', $status);
      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);
      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
      

  	  $query = $this->db->count_all_results();
  	  return $query;
	  }

	  function count_filtered($post,$Status = "")
	  {
	  	if($Status == 'indexing')
      {
        $status[0] = $this->config->item('keywords')['Indexing Exception'];
        $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
      }
      else if($Status == 'fatal')
      {
         $status[0] = $this->config->item('keywords')['Fatal Exception'];
         $status[1] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];

      }
      else if($Status == 'nonfatal')
      {
        $status[0] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[1] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
      }

      if (empty($Status)) {
        $status[] = $this->config->item('keywords')['Indexing Exception'];
        $status[] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];

        $status[] = $this->config->item('keywords')['Fatal Exception'];
        $status[] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];

        $status[] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
        
      }

  		$this->db->select("*,tOrders.OrderUID,mInputDocType.DocTypeName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
    	$this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
  		$this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
  		$this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
  		$this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
     // $this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');



      $this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");

      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');

      $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

      $this->db->group_start();
      $this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
        CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
        CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
        ELSE TRUE END
        ELSE TRUE END
        ELSE FALSE END", NULL, FALSE);
      $this->db->group_end();

      $this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);
      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

      if($this->RoleUID !=1){
      if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        
        $this->db->group_start();
        $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
        $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
        $this->db->group_end();
      }}else{
           $this->db->where('tOrderAssignments.AssignedToUserUID IS NULL');
      }

$this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

      $this->db->where_in('tOrders.StatusUID', $status);
      
      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole($query);
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/


      // Advanced Search
      if ($post['advancedsearch'] != '' && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }

		if (!empty($post['search_value'])) {
			$like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
            	$like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
            } else {
            	$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
            }
          }
          $like .= ") ";
          $this->db->where($like, null, false);
        }

        if (!empty($post['order']))
        {
      	// here order processing
        	if($post['column_order'][$post['order'][0]['column']]!='')
        	{
        		$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        	}
        } else if (isset($this->order)) {
        	$order = $this->order;
        	$this->db->order_by(key($order), $order[key($order)]);
        }
      
      $this->db->order_by('OrderEntryDatetime');
	  	$query = $this->db->get();
	  	return $query->num_rows();
	  }

  function ExceptionOrders($post,$Status = "")
	{
      if($Status == 'indexing')
      {
  		  $status[0] = $this->config->item('keywords')['Indexing Exception'];
        $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
      }
      else if($Status == 'fatal')
      {
         $status[0] = $this->config->item('keywords')['Fatal Exception'];
         $status[1] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];

      }
      else if($Status == 'nonfatal')
      {
        $status[0] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[1] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
      }

  
      if(empty($Status))
      {
        $status[] = $this->config->item('keywords')['Indexing Exception'];
        $status[] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
        $status[] = $this->config->item('keywords')['Fatal Exception'];
        $status[] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];
        $status[] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
      }

  		$this->db->select("*,tOrders.OrderUID,tOrders.LoanNumber,mInputDocType.DocTypeName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName,tOrderAssignments.AssignedToUserUID");
  		$this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
  		$this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
  		$this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
  		$this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      //$this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');


      $this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");

      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');

      $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

      $this->db->group_start();
      $this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
        CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
        CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
        ELSE TRUE END
        ELSE TRUE END
        ELSE FALSE END", NULL, FALSE);
      $this->db->group_end();

      $this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);
      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/


    if($this->RoleUID !=1){
      if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        
        $this->db->group_start();
        $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
        $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
        $this->db->group_end();
      }}else{
           $this->db->where('tOrderAssignments.AssignedToUserUID IS NULL');
      }
  		$this->db->where_in('tOrders.StatusUID', $status);
      
      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);


      // Advanced Search
    if ($post['advancedsearch'] != '' && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }

		if (!empty($post['search_value'])) {
			$like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
            	$like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
            } else {
            	$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
            }
          }
          $like .= ") ";
          $this->db->where($like, null, false);
        }

        if (!empty($post['order']))
        {
      	// here order processing
        	if($post['column_order'][$post['order'][0]['column']]!='')
        	{
        		$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        	}
        } else if (isset($this->order)) {
        	$order = $this->order;
        	$this->db->order_by(key($order), $order[key($order)]);
        }
        else{
          $this->db->order_by('tOrders.OrderNumber', 'ASC');
        }


	    if ($post['length']!='') {
	       $this->db->limit($post['length'], $post['start']);
	    }

      $this->db->order_by('OrderEntryDatetime');
	   $output= $this->db->get();
     if($output->num_rows()>1000)
     {
       return $output->num_rows();
     }else{
       return $output->result();
     }
  
     
    
	}
	// MyOrders

    function advanced_search($post)
    {
      $fields = array_filter($post['advancedsearch']);
      $like = [];
      $inlike = [];
      foreach ($fields as $key => $value) {

        if($value == 'All')
        {
          $inlike[] = "tOrders.".$key;
        }
        else
        {
            if($key == 'FromDate')
            {
               $like[] = "DATE(OrderEntryDatetime) BETWEEN '".date('Y-m-d',strtotime($value))."'"; 

            }else if ($key == 'ToDate'){
              $like[] = "'".date('Y-m-d',strtotime($value))."'"; 

            }else if($key == 'ProjectUID' || $key == 'CustomerUID' || $key == 'PackageUID'){
              
                $like[] = "tOrders.".$key."='".$value."'";
            }

        }

      }
      $filter = implode(' AND ', $like);
      return array('where' => $filter,'where_in' => $inlike);
    }
  // Work In Progress
      function in_progress_count_all()
    {
      $status[0] = $this->config->item('keywords')['Indexing Exception'];
      $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
      $status[2] = $this->config->item('keywords')['Fatal Exception'];
      $status[3] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];
      $status[4] = $this->config->item('keywords')['Non Fatal Exception'];
      $status[5] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];

      $this->db->select("*,tOrders.OrderUID,mInputDocType.DocTypeName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mUsers.UserName AS AssignedUserName,mProducts.ProductName");
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      //$this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');



      $this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");

      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');

      $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

      $this->db->group_start();
      $this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
        CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
        CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
        ELSE TRUE END
        ELSE TRUE END
        ELSE FALSE END", NULL, FALSE);
      $this->db->group_end();

      $this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);
      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/



      if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        $this->db->where('tOrderAssignments.AssignedToUserUID !=',NULL);
      }
      $this->db->where_in('tOrders.StatusUID', $status);
      
      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
      

      $query = $this->db->count_all_results();
      return $query;
    }



    function in_progress_count_filtered($post)
    {
      $status[0] = $this->config->item('keywords')['Indexing Exception'];
      $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
      $status[2] = $this->config->item('keywords')['Fatal Exception'];
      $status[3] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];
      $status[4] = $this->config->item('keywords')['Non Fatal Exception'];
      $status[5] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];

      $this->db->select("*,tOrders.OrderUID,mInputDocType.DocTypeName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mUsers.UserName AS AssignedUserName,mProducts.ProductName");
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      //$this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');


      $this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");

      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');

      $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

      $this->db->group_start();
      $this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
        CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
        CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
        ELSE TRUE END
        ELSE TRUE END
        ELSE FALSE END", NULL, FALSE);
      $this->db->group_end();

      $this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);
      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

      if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        
        $this->db->where('tOrderAssignments.AssignedToUserUID !=',NULL);
      }
      $this->db->where_in('tOrders.StatusUID', $status);
      
      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/


      // Advanced Search
    if ($post['advancedsearch'] != '' && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->advanced_search($post);
    }

    if (!empty($post['search_value'])) {
      $like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
              $like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
            } else {
              $like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
            }
          }
          $like .= ") ";
          $this->db->where($like, null, false);
        }

        if (!empty($post['order']))
        {
        // here order processing
          if($post['column_order'][$post['order'][0]['column']]!='')
          {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
          }
        } else if (isset($this->order)) {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);
        }
        else{
          $this->db->order_by('tOrders.OrderNumber', 'ASC');
        }

      $query = $this->db->get();
      return $query->num_rows();
    }



  function WorkinprogressExceptionOrders($post)
  {
      $status[0] = $this->config->item('keywords')['Indexing Exception'];
      $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
      $status[2] = $this->config->item('keywords')['Fatal Exception'];
      $status[3] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];
      $status[4] = $this->config->item('keywords')['Non Fatal Exception'];
      $status[5] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];


      $this->db->select("tOrders.OrderNumber,tOrders.LoanNumber,tOrders.OrderUID,mInputDocType.DocTypeName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mUsers.UserName AS AssignedUserName,mProducts.ProductName,mProjectCustomer.ProjectName,tOrders.PropertyAddress1,tOrders.PropertyCityName,tOrders.PropertyStateCode,tOrders.PropertyZipCode,tOrderPackage.PackageNumber");
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      //$this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');


      if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        
        $this->db->where('tOrderAssignments.AssignedToUserUID !=',NULL);
      }
      $this->db->where_in('tOrders.StatusUID', $status);
      

      $this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");

      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');

      $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

      $this->db->group_start();
      $this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
        CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
        CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
        ELSE TRUE END
        ELSE TRUE END
        ELSE FALSE END", NULL, FALSE);
      $this->db->group_end();

      $this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);
      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/

      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
      $this->Common_Model->FilterByProjectUser($this->RoleUID,$this->loggedid);

      // Advanced Search
      if ($post['advancedsearch'] != '' && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }

    if (!empty($post['search_value'])) {
      $like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
              $like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
            } else {
              $like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
            }
          }
          $like .= ") ";
          $this->db->where($like, null, false);
        }

        if (!empty($post['order']))
        {
        // here order processing
          if($post['column_order'][$post['order'][0]['column']]!='')
          {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
          }
        } else if (isset($this->order)) {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);
        }
        else{
          $this->db->order_by('tOrders.OrderNumber', 'ASC');
        }


      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
      $query = $this->db->get();
      return $query->result();
  }
  // Work In Progress 

    function PickExistingOrderCheck($OrderUID)
    {
        $this->db->select('*');
        $this->db->from('tOrderAssignments');
        $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','left');
        // $this->db->where(array());
        $this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID,'tOrderAssignments.WorkflowModuleUID'=>$this->config->item('Workflows')['Exception']));
        $this->db->where('tOrderAssignments.AssignedToUserUID !=',NULL);
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
        $tOrderAssignmentArray = array(
          
          'AssignedToUserUID' => $this->loggedid,
          'AssignedDateTime' => Date('Y-m-d H:i:s', strtotime("now")),
          'AssignedByUserUID' => $this->loggedid,
          'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress'],
        );

        // Check is exceoption available
        $this->db->select('*');
        $this->db->from('tOrderAssignments');
        $this->db->join('mUsers','mUsers.UserUID = tOrderAssignments.AssignedToUserUID','left');
        $this->db->where(array('tOrderAssignments.OrderUID'=>$OrderUID));
        $this->db->where(array('tOrderAssignments.WorkflowModuleUID'=>$this->config->item('Workflows')['Exception']));
        $this->db->where('tOrderAssignments.AssignedToUserUID !=',NULL);
        $query = $this->db->get();
        if($query->num_rows() < 1){
          $tOrderAssignmentArray['OrderUID'] = $OrderUID;
          $tOrderAssignmentArray['WorkflowModuleUID'] = $this->config->item('Workflows')['Exception'];
          $this->Common_Model->save('tOrderAssignments', $tOrderAssignmentArray);
        }
        else{
          $this->db->where(array('OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['Exception']));
          $query = $this->db->update('tOrderAssignments', $tOrderAssignmentArray);

        }
        return 1;
    }

    function GetExceptionOrdersExcelRecords($post)
    {
      
      $projectfilter = $this->Common_Model->GetProjectCustomers(); 
      $projectuidlike = [];
      foreach ($projectfilter as $key => $value) {
        $projectuidlike[] = $key;
      }

      $customerfilter = $this->Common_Model->GetClients(); 
      $customeruidlike = [];
      foreach ($customerfilter as $key => $value) {
        $customeruidlike[] = $key;
      }

      $lenderfilter = $this->Common_Model->GetLenders(); 
      $lenderuidlike = [];
      foreach ($lenderfilter as $key => $value) {
        $lenderuidlike[] = $key;
      }

      if($post['advancedsearch']['Status'] == 'indexing')
      {
        
        $status[0] = $this->config->item('keywords')['Indexing Exception'];
        $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
      }
      else if($post['advancedsearch']['Status'] == 'fatal')
      {
       
         $status[0] = $this->config->item('keywords')['Fatal Exception'];
         $status[1] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];

      }
      else if($post['advancedsearch']['Status'] == 'nonfatal')
      {
        
        $status[0] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[1] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];
      }
        else if($post['advancedsearch']['Status'] == 'workinprogress')
      {
        $status[0] = $this->config->item('keywords')['Indexing Exception'];
        $status[1] = $this->config->item('keywords')['Indexing Exception Fix In Progress'];
        $status[2] = $this->config->item('keywords')['Fatal Exception'];
        $status[3] = $this->config->item('keywords')['Fatal Exception Fix In Progress'];
        $status[4] = $this->config->item('keywords')['Non Fatal Exception'];
        $status[5] = $this->config->item('keywords')['Non Fatal Exception Fix In Progress'];

      }
      
      

      $this->db->select("tOrders.OrderNumber,tOrders.LoanNumber,tOrders.OrderDueDate,tOrders.PropertyAddress1,tOrders.PropertyAddress2,tOrders.PropertyCityName,tOrders.PropertyStateCode, tOrders.PropertyZipCode,tOrders.OrderUID,mInputDocType.DocTypeName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mUsers.UserName,mProducts.ProductName,tOrderPackage.PackageNumber,mProjectCustomer.ProjectName");
      $this->db->select("DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDateTime", FALSE);
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
     // $this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
      $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = tOrders.InputDocTypeUID','left');
      $this->db->join('mProducts','tOrders.ProductUID = mProducts.ProductUID','left');



      $this->db->join("tOrderWorkflows", "tOrderWorkflows.OrderUID = tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."'");

      $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrders.OrderUID AND tOrderAssignments.WorkflowModuleUID = "' . $this->config->item("Workflows")["Exception"] . '"','left');

      $this->db->join('mUsers','tOrderAssignments.AssignedToUserUID = mUsers.UserUID','left');


      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED STARTS^^^^^*/

      $this->db->group_start();
      $this->db->where("CASE WHEN tOrderWorkflows.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' AND tOrderWorkflows.IsPresent = '". STATUS_ONE ."' THEN 
        CASE WHEN tOrderAssignments.WorkflowModuleUID = '".$this->config->item('Workflows')['Exception']."' THEN 
        CASE WHEN tOrderAssignments.WorkflowStatus = '".$this->config->item('WorkflowStatus')['Completed']."' THEN FALSE 
        ELSE TRUE END
        ELSE TRUE END
        ELSE FALSE END", NULL, FALSE);
      $this->db->group_end();

      $this->db->where("tOrderWorkflows.WorkflowModuleUID", $this->config->item('Workflows')['Exception']);
      /*^^^^^ WORKFLOW CHECK FOR PREVIOUS ARE COMPLETED ENDS ^^^^^*/


      if($post['advancedsearch']['Status'] == 'workinprogress'){

       $this->db->where('tOrderAssignments.AssignedToUserUID !=',NULL);
   
     }
     else if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
        
       $this->db->group_start();
       $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
       $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
       $this->db->group_end();
      }
      $this->db->where_in('tOrders.StatusUID', $status);
      
      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
      


      if ($post['advancedsearch']!='false' && sizeof(array_filter($post['advancedsearch']))!=0) 
      {
          $filter = $this->Common_Model->advanced_search($post); 
          
      }
      $this->db->order_by('tOrders.OrderNumber');
      $query = $this->db->get();
      if($query->num_rows()>1000)
      {
        return $query->num_rows(); 
      }else{
        return $query->result();  
      }
    }


}
?>
