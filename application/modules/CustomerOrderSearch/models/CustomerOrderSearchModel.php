<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class CustomerOrderSearchModel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GetCustomerOrderSearchDetails($post)
	{
		$this->db->select();
		$this->db->from('tOrders');
		if(!empty($post['OrderNumber']))
		{
			$this->db->where('OrderNumber',$post['OrderNumber']);
		}
		if(!empty($post['LoanNumber']))
		{
			$this->db->where('LoanNumber',$post['LoanNumber']);
		}
		if(!empty($post['PropertyAddress']))
		{
			$this->db->or_like('PropertyAddress1',$post['PropertyAddress']);
			$this->db->or_like('PropertyAddress2',$post['PropertyAddress']);
			$this->db->or_like('PropertyZipCode',$post['PropertyAddress']);
			$this->db->or_like('PropertyCityName',$post['PropertyAddress']);
			$this->db->or_like('PropertyStateCode',$post['PropertyAddress']);
			$this->db->or_like('PropertyCountyName',$post['PropertyAddress']);
		}
		if(!empty($post['CustomerReferenceNumber']))
		{
			$this->db->where('CustomerReferenceNumber',$post['CustomerReferenceNumber']);
		}
		//$this->db->where('tOrders.StatusUID',100);
		$this->db->join('mCustomer','mCustomer.CustomerUID = tOrders.CustomerUID','left');
		$this->db->join('mProjectCustomer','mProjectCustomer.ProjectUID = tOrders.ProjectUID','left');
		$this->db->join('mLender','mLender.LenderUID = tOrders.LenderUID','left');
		return $this->db->get()->result();
	}

}
?>