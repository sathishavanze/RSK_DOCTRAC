<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Master setup for Company details (List / Add / Edit )
*
* @author Santhiya M <santhiya.m@avanzegroup.com>
* @since July 29th 2020
*
*/
class HoiCompanies_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	public function SaveDetails($Details)
	{

		$this->db->trans_begin();
		$CompanyUID = $this->HoiCompanies_Model->Save('mCompanyDetails', $Details);

		if ($this->db->trans_status() == true) {
			$this->db->trans_commit();
			return true;
		}
		else{
			return false;
		}
	}

	public function UpdateDetails($Details, $CompanyUID)
	{
		$this->db->trans_begin();
		$this->HoiCompanies_Model->Save('mCompanyDetails', $Details, ['CompanyUID'=>$CompanyUID]);

		if ($this->db->trans_status() == true) {
			$this->db->trans_commit();
			return true;
		}
		else{
			return false;
		}
	}

	public function GetDetails() {
		$this->db->select('*');
		$this->db->from('mCompanyDetails');
		return $this->db->get()->result();
	}

}?>

