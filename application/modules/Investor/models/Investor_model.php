<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Investor_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GetInvestors()
	{
		$this->db->select('mInvestors.*');
		$this->db->from('mInvestors');
		//$this->db->where('Active', STATUS_ONE);
		return $this->db->get()->result();
	}

	function InsertInvestor($post)
	{
		$Investor = [];
		$Active=$post['Active']=isset($post['Active']) ? 1 : 0;
		$Investor['InvestorNo'] = $post['InvestorNo'];
		$Investor['InvestorName'] = $post['InvestorName'];
		$Investor['AddressLine1'] = $post['AddressLine1'];
		$Investor['AddressLine2'] = $post['AddressLine2'];
		$Investor['ZipCode'] = $post['ZipCode'];
		$Investor['CityName'] = $post['CityName'];
		$Investor['StateName'] = $post['StateName'];
		$Investor['CreatedByUserUID'] = $this->loggedid;
		$Investor['CreatedOn'] = date('Y-m-d H:i:s');
		$Investor['Active'] = $Active;

		if($this->Common_Model->save('mInvestors', $Investor)){
			return true;
		}
		return false;
	}

	function UpdateInvestor($post)
	{
		$Investor = [];
		$Active=$post['Active']=isset($post['Active']) ? 1 : 0;
		$InvestorUID = $post['InvestorUID'];
		$Investor['InvestorNo'] = $post['InvestorNo'];
		$Investor['InvestorName'] = $post['InvestorName'];
		$Investor['AddressLine1'] = $post['AddressLine1'];
		$Investor['AddressLine2'] = $post['AddressLine2'];
		$Investor['ZipCode'] = $post['ZipCode'];
		$Investor['CityName'] = $post['CityName'];
		$Investor['StateName'] = $post['StateName'];
		$Investor['Active'] = $Active;

		if($this->Common_Model->save('mInvestors', $Investor, ['InvestorUID'=>$InvestorUID])){
			return true;
		}
		return false;
	}

}
?>

