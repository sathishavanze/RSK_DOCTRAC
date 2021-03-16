<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SubProducts_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}


	function GetSubProducts()
	{
		$this->db->select('mSubProducts.*, mProducts.ProductUID, mProducts.ProductName, mProducts.Productcode');
		$this->db->from('mSubProducts');
		$this->db->join('mProducts', 'mSubProducts.ProductUID=mProducts.ProductUID');
		$this->db->where(['mSubProducts.Active'=>1]);
		return $this->db->get()->result();
	}
	
	function GetSubProduct_ByUID($SubProductUID)
	{
		$this->db->select('mSubProducts.*, mProducts.ProductUID, mProducts.ProductName, mProducts.Productcode');
		$this->db->from('mSubProducts');
		$this->db->join('mProducts', 'mSubProducts.ProductUID=mProducts.ProductUID');
		$this->db->where(['mSubProducts.Active'=>1, 'mSubProducts.SubProductUID'=>$SubProductUID]);
		return $this->db->get()->row();
	}
}
?>

