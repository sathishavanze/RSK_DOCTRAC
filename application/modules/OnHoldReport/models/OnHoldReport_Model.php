<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class OnHoldReport_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function OnHold_ajax_list($post){
		$this->db->select("*,tOrders.OrderNumber,tOrderPackage.PackageNumber,mUsers.UserName,mCustomer.CustomerName,mProducts.ProductName,mProjectCustomer.ProjectName,mLender.LenderName,mStatus.StatusName,mStatus.StatusColor,tOrders.PropertyAddress1,tOrders.PropertyAddress2,tOrders.PropertyCityName,tOrders.PropertyStateCode,tOrders.PropertyZipCode,tOrders.OrderEntryDateTime");
      
		$this->db->from('tOrderOnhold');
		$this->db->join('tOrders','tOrders.OrderUID=tOrderOnhold.OrderUID');
        $this->db->join('tOrderDocumentCheckIn','tOrders.OrderUID=tOrderDocumentCheckIn.OrderUID');
		$this->db->join('mUsers','mUsers.UserUID=tOrderOnhold.AssignedUserUID');
        $this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID');
        $this->db->join('mProducts','mProducts.ProductUID=tOrders.ProductUID');
        $this->db->join('mProjectCustomer','mProjectCustomer.ProjectUID=tOrders.ProjectUID');
        $this->db->join('mLender','mLender.LenderUID=tOrders.LenderUID','left');
        $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
        $this->db->join('mStatus','mStatus.StatusUID=tOrders.StatusUID');
        if ($this->RoleUID!=1) {
        $this->db->where('tOrderOnhold.AssignedUserUID',$this->loggedid);
        }
        $this->Common_Model->FilterOrderBasedOnRole();
       /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
       if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
           $filter = $this->Common_Model->OnHoldRpt_advanced_search($post);
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


    if ($post['length']!='') {
    	$this->db->limit($post['length'], $post['start']);
    }
    $query = $this->db->get();
    return $query->result();
}


function count_all()
{

	$this->db->select("*,tOrders.OrderNumber,tOrderPackage.PackageNumber,mUsers.UserName,mCustomer.CustomerName,mProducts.ProductName,mProjectCustomer.ProjectName,mLender.LenderName,mStatus.StatusName,mStatus.StatusColor,tOrders.PropertyAddress1,tOrders.PropertyAddress2,tOrders.PropertyCityName,tOrders.PropertyStateCode,tOrders.PropertyZipCode");
        $this->db->from('tOrderOnhold');
        $this->db->join('tOrders','tOrders.OrderUID=tOrderOnhold.OrderUID');
        $this->db->join('mUsers','mUsers.UserUID=tOrderOnhold.AssignedUserUID');
        $this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID');
        $this->db->join('mProducts','mProducts.ProductUID=tOrders.ProductUID');
        $this->db->join('mProjectCustomer','mProjectCustomer.ProjectUID=tOrders.ProjectUID');
        $this->db->join('mLender','mLender.LenderUID=tOrders.LenderUID');
        $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
        $this->db->join('mStatus','mStatus.StatusUID=tOrders.StatusUID');
        if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
           $filter = $this->Common_Model->OnHoldRpt_advanced_search($post);
       }
	$query = $this->db->count_all_results();
	return $query;
}

function count_filtered($post)
{

	$this->db->select("*,tOrders.OrderNumber,tOrderPackage.PackageNumber,mUsers.UserName,mCustomer.CustomerName,mProducts.ProductName,mProjectCustomer.ProjectName,mLender.LenderName,mStatus.StatusName,mStatus.StatusColor,tOrders.PropertyAddress1,tOrders.PropertyAddress2,tOrders.PropertyCityName,tOrders.PropertyStateCode,tOrders.PropertyZipCode");
        $this->db->from('tOrderOnhold');
        $this->db->join('tOrders','tOrders.OrderUID=tOrderOnhold.OrderUID');
        $this->db->join('mUsers','mUsers.UserUID=tOrderOnhold.AssignedUserUID');
        $this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID');
        $this->db->join('mProducts','mProducts.ProductUID=tOrders.ProductUID');
        $this->db->join('mProjectCustomer','mProjectCustomer.ProjectUID=tOrders.ProjectUID');
        $this->db->join('mLender','mLender.LenderUID=tOrders.LenderUID');
        $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
        $this->db->join('mStatus','mStatus.StatusUID=tOrders.StatusUID');
        if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
           $filter = $this->Common_Model->OnHoldRpt_advanced_search($post);
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
    $query = $this->db->get();
    return $query->num_rows();
}

function GetwaitingforOnHoldExcelRecords($post)
    {

     $this->db->select("*,tOrders.OrderNumber,tOrderPackage.PackageNumber,mUsers.UserName,mCustomer.CustomerName,mProducts.ProductName,mProjectCustomer.ProjectName,mLender.LenderName,mStatus.StatusName,mStatus.StatusColor,tOrders.PropertyAddress1,tOrders.PropertyAddress2,tOrders.PropertyCityName,tOrders.PropertyStateCode,tOrders.PropertyZipCode,
        CASE WHEN tOrderOnhold.OnHoldDateTime = '0000-00-00 00:00:00' THEN '' ELSE tOrderOnhold.OnHoldDateTime END AS OnHoldDateTime,
        CASE WHEN tOrderOnhold.ReleaseDateTime = '0000-00-00 00:00:00' THEN '' ELSE tOrderOnhold.ReleaseDateTime END AS ReleaseDateTime", FALSE);
     
        $this->db->from('tOrderOnhold');
        $this->db->join('tOrders','tOrders.OrderUID=tOrderOnhold.OrderUID');
        $this->db->join('mUsers','mUsers.UserUID=tOrderOnhold.AssignedUserUID');
        $this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID');
        $this->db->join('mProducts','mProducts.ProductUID=tOrders.ProductUID');
        $this->db->join('mProjectCustomer','mProjectCustomer.ProjectUID=tOrders.ProjectUID');
        $this->db->join('mLender','mLender.LenderUID=tOrders.LenderUID');
        $this->db->join('tOrderPackage','tOrders.PackageUID = tOrderPackage.PackageUID','left');
        $this->db->join('mStatus','mStatus.StatusUID=tOrders.StatusUID');
    
    /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
    $this->Common_Model->FilterOrderBasedOnRole();
    /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->Common_Model->OnHoldRpt_advanced_search($post);
    }

      $this->db->order_by('tOrders.OrderNumber');
      $query = $this->db->get();
      return $query->result();  
    }

}?>

