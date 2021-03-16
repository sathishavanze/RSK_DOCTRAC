<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Status_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function AddingStatus($post)
	{
		
		$AddingStatus=array('StatusName'=>$post['StatusName'],'StatusColor'=>$post['StatusColor'],'ModuleName'=>$post['ModuleName'],'Active'=>$post['Active']);
			$this->db->insert('mStatus',$AddingStatus);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function UpdateStatus($post)
	{
		$UpdateStatus=array('StatusName'=>$post['StatusName'],'StatusColor'=>$post['StatusColor'],'ModuleName'=>$post['ModuleName'],'Active'=>$post['Active']);
		
		$this->db->where(array('StatusUID' => $post['StatusUID']));
		$this->db->update('mStatus',$UpdateStatus);  
		
		return 1;
	
	}

	function GetStatusDetails(){
		$this->db->select("*");
		$this->db->from('mStatus');
		return $this->db->get()->result();
	}

}
?>

