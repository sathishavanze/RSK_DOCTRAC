<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Products_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	public function SaveProductDetails($ProductDetails, $ProductDocTypes, $ProductRules)
	{


		$this->db->trans_begin();
		$ProductUID = $this->Products_Model->Save('mProducts', $ProductDetails);

		$ProductDocTypes_Array = [];
		foreach ($ProductDocTypes as $key => $value) {
			$ProductDocTypes_Field['ProductUID'] = $ProductUID;
			$ProductDocTypes_Field['InputDocTypeUID'] = $value;

			$ProductDocTypes_Array[] = $ProductDocTypes_Field;
		}

		// Insert Product Rules in batch array
		$ProductRules_Array = [];
		foreach ($ProductRules as $key => $value) {
			$ProducRules_Field['ProductUID'] = $ProductUID;
			$ProducRules_Field['RuleUID'] = $value;

			$ProductRules_Array[] = $ProducRules_Field;
		}


		// Insert BATCH
		if (!empty($ProductDocTypes_Array)) {
			$this->db->insert_batch('mProductDocType', $ProductDocTypes_Array);
		}
		if (!empty($ProductRules_Array)) {
			$this->db->insert_batch('mProductRules', $ProductRules_Array);
		}
		if ($this->db->trans_status() == true) {
			$this->db->trans_commit();
			return true;
		}
		else{
			return false;
		}
	}

	public function UpdateProductDetails($ProductDetails, $ProductDocTypes, $ProductRules, $ProductUID)
	{


		$this->db->trans_begin();

		$this->db->where(['ProductUID'=>$ProductUID]);
		$this->db->delete('mProductDocType');

		$this->db->where(['ProductUID'=>$ProductUID]);
		$this->db->delete('mProductRules');
		$this->Products_Model->Save('mProducts', $ProductDetails, ['ProductUID'=>$ProductUID]);

		// Insert Product DocTypes in batch array
		$ProductDocTypes_Array = [];
		foreach ($ProductDocTypes as $key => $value) {
			$ProductDocTypes_Field['ProductUID'] = $ProductUID;
			$ProductDocTypes_Field['InputDocTypeUID'] = $value;

			$ProductDocTypes_Array[] = $ProductDocTypes_Field;
		}

		// Insert Product Rules in batch array
		$ProductRules_Array = [];
		foreach ($ProductRules as $key => $value) {
			$ProducRules_Field['ProductUID'] = $ProductUID;
			$ProducRules_Field['RuleUID'] = $value;

			$ProductRules_Array[] = $ProducRules_Field;
		}


		// Insert BATCH
		if (!empty($ProductDocTypes_Array)) {
			$this->db->insert_batch('mProductDocType', $ProductDocTypes_Array);
		}
		if (!empty($ProductRules_Array)) {
			$this->db->insert_batch('mProductRules', $ProductRules_Array);
		}

		if ($this->db->trans_status() == true) {
			$this->db->trans_commit();
			return true;
		}
		else{
			return false;
		}
	}

	public function getProductRules($ProductUID)
	{
		$this->db->select('mProductRules.*, mImportRules.*, mProducts.ProductName, mProducts.ProductCode');
		$this->db->from('mProductRules');
		$this->db->join('mImportRules', 'mImportRules.RuleUID = mProductRules.RuleUID');
		$this->db->join('mProducts', 'mProducts.ProductUID = mProductRules.ProductUID');
		$this->db->where('mProductRules.ProductUID', $ProductUID);
		return $this->db->get()->result();
	}

	public function getRules($Rules = [])
	{
		$this->db->select('mImportRules.*');
		$this->db->from('mImportRules');
		if(!empty($Rules)) {
			$this->db->where_not_in('mImportRules.RuleUID', $Rules);
		}
		return $this->db->get()->result();
	}

	public function GetProductDetails() {
		$this->db->select('*');
		$this->db->from('mProducts');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('CustomerUID',$this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->result();
	}
}
?>

