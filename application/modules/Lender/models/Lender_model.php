<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Lender_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GetDocumentType($post)
	{
		// $Active=$data['Active']=isset($post['Active']) ? 1 : 0;
		$users=array('LenderName'=>$post['LenderName'],'TPOCode'=>$post['TPOCode'],'AddressLine1'=>$post['AddressLine1'],'AddressLine2'=>$post['AddressLine2'],'CityUID'=>$post['CityUID'],'StateUID'=>$post['StateUID'],'ZipCode'=>$post['ZipCode'],'OfficeNo'=>$post['OfficeNo'],'Active'=>1,'FaxNo'=>$post['FaxNo'],'CreatedOn' => Date('Y-m-d H:i:s', strtotime("now")),'CreatedByUID'=>$this->loggedid);
		    
			$this->db->insert('mLender',$users);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function UpdateDocument($post)
	{
		$Active=$data['Active']=isset($post['Active']) ? 1 : 0;
		$users=array('LenderName'=>$post['LenderName'],'TPOCode'=>$post['TPOCode'],'AddressLine1'=>$post['AddressLine1'],'AddressLine2'=>$post['AddressLine2'],'CityUID'=>$post['CityUID'],'StateUID'=>$post['StateUID'],'ZipCode'=>$post['ZipCode'],'OfficeNo'=>$post['OfficeNo'],'Active'=>$Active,'FaxNo'=>$post['FaxNo'],'CreatedOn' => Date('Y-m-d H:i:s', strtotime("now")),'CreatedByUID'=>$this->loggedid);
	
		   $this->db->where(array("LenderUID"=>$post['LenderUID']));

			$this->db->update('mLender',$users);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function getzipcontents($CustomerZipCode = '')
	{
		$query = $this->db->query("SELECT * FROM `mCities` 
			LEFT JOIN mStates ON mCities.StateUID = mStates.StateUID 
			LEFT JOIN mCounties ON mCities.StateUID = mCounties.StateUID 
			AND mCities.CountyUID = mCounties.CountyUID
			WHERE mCities.ZipCode = '$CustomerZipCode'");
		return $query->result();
	}


	function GetDocument(){
		$this->db->select('*,mCities.CityName,mStates.StateName');
		$this->db->from('mLender');
		$this->db->join('mCities','mCities.CityUID = mLender.CityUID','left');
		$this->db->join('mStates','mStates.StateUID = mLender.StateUID','left');
		return $this->db->get()->result();
	}

	function CheckExistUserName($UserUID, $LoginID)
	{																	
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows(); 
	}

}
?>

