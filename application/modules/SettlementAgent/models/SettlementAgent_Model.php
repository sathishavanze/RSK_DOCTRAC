<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SettlementAgent_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function SaveSettlementAgent($post)
	{
		$users=array('SettlementAgentNo'=>$post['SettlementAgentNo'],'SettlementAgentName'=>$post['SettlementAgentName'],'SettlementAgentPhone'=>$post['SettlementAgentPhone'],'SettlementAgentFax'=>$post['SettlementAgentFax'],'SettlementAgentEmail'=>$post['SettlementAgentEmail'],'AddressLine1'=>$post['AddressLine1'],'AddressLine2'=>$post['AddressLine2'],'CityName'=>$post['CityName'],'StateName'=>$post['StateName'],'ZipCode'=>$post['ZipCode'],'Active'=>1,'CreatedOn' => Date('Y-m-d H:i:s', strtotime("now")),'CreatedByUserUID'=>$this->loggedid);

		$this->db->insert('mSettlementAgent',$users);
		
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

	function GetSettlementAgents(){
		$this->db->select('*,mCities.CityName,mStates.StateName');
		$this->db->from('mSettlementAgent');
		$this->db->join('mCities','mCities.CityUID = mSettlementAgent.CityName','left');
		$this->db->join('mStates','mStates.StateUID = mSettlementAgent.StateName','left');
		return $this->db->get()->result();
	}


	function UpdateSettlementAgent($post){

	    $Active=$data['Active']=isset($post['Active']) ? 1 : 0;
		$users=array('SettlementAgentNo'=>$post['SettlementAgentNo'],'SettlementAgentName'=>$post['SettlementAgentName'],'SettlementAgentPhone'=>$post['SettlementAgentPhone'],'SettlementAgentFax'=>$post['SettlementAgentFax'],'SettlementAgentEmail'=>$post['SettlementAgentEmail'],'AddressLine1'=>$post['AddressLine1'],'AddressLine2'=>$post['AddressLine2'],'CityName'=>$post['CityName'],'StateName'=>$post['StateName'],'ZipCode'=>$post['ZipCode'],'Active'=>$Active,'CreatedOn' => Date('Y-m-d H:i:s', strtotime("now")),'CreatedByUserUID'=>$this->loggedid);
	
		   $this->db->where(array("SettlementAgentUID"=>$post['SettlementAgentUID']));	
		   $this->db->update('mSettlementAgent',$users);
		
		if($this->db->affected_rows() > 0)
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