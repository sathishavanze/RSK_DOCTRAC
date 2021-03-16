<?php if(!defined('BASEPATH')) exit('No direct secipt access allowed');
/**
 * 
 */
class Organizationmodel extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();

	}

	function getorganization()
	{
	$this->db->select("OrganizationUID,OrganizationName,concat_ws('  ',OrganizationAddress1,' ',OrganizationAddress2)as Address,OrganizationCity,OrganizationCounty,OrganizationState,OrganizationPhoneNo");
	$this->db->from('mOrganization');
	$result=$this->db->get();
		return $result->result();

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

	function UpdateOrganization($post,$id)
	{
		// $data=array('OrganizationName'=>$post['OrganizationName'],
		// 			'OrganizationAddress1'=>$post['OrganizationAddress1'],
		// 			'OrganizationAddress2'=>$post['OrganizationAddress2'],
		// 			'OrganizationZib'=>$post['OrganizationZib'],
		// 			'OrganizationCity'=>$post['cityuid'],
		// 			'OrganizationCounty'=>$post['CountyUID'],
		// 			'OrganizationState'=>$post['stateuid'],
		// 			'OrganizationPhoneNo'=>$post['OrganizationPhoneNo'],
		// 			'SMTPHost'=>$post['SMTPHost'],
		// 			'SMTPUserName'=>$post['SMTPUserName'],
		// 			'SMTPPassword'=>$post['SMTPPassword'],
		// 			'SMTPPort'=>$post['SMTPPort'],
		// 			'BinCount'=>$post['BinCount']);
		$this->db->where(array('OrganizationUID'=>$id));
		$this->db->update('mOrganization',$post);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}

	}
	function SaveOrganization($post)
	{  
		$this->db->insert('mOrganization',$post);
		if($this->db->affected_rows()>0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}
?>