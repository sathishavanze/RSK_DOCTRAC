


<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Statemodel extends MY_Model { 
	function product_list(){
		$this->db->select("*");
		$this->db->from('mStates');
		return $this->db->get()->result();
	}

	function Savestates($post){
			$Active=$data['Active']=isset($post['Active']) ? 1 : 0;
		$data = array(
			'StateCode'=>$post['StateCode'],'StateName'=>$post['StateName'],'FIPSCode'=>$post['FIPSCode'],'StateEmail'=>$post['State'],'StateWebsite'=>$post['StateWebsite'],'StatePhoneNumber'=>$post['StatePhoneNumber'],'Active'=>$Active);
		$this->db->insert('mStates',$data);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
		
	}

	function update_states($post){
		$Active=$data['Active']=isset($post['Active']) ? 1 : 0;
		$user = array(
			'StateCode'=> $post['StateCode'],'StateName'=> $post['StateName'],'FIPSCode'  => $post['FIPSCode'],'StateEmail'=> $post['StateEmail'],'StateWebsite'=> $post['StateWebsite'],'StatePhoneNumber'=> $post['StatePhoneNumber'],'Active'=>$Active);
		$this->db->where(array('StateUID' => $post['StateUID']));
		$result=$this->db->update('mStates',$user);
		return $result;
	}
	function getdatabyStateUID($StateUID)
	{
		//echo '<pre>';print_r($StateUID);exit;
		$this->db->select('*');
		$this->db->from('mStates');
		$this->db->where('StateUID',$StateUID);
		$result = $this->db->get()->row();
		return $result;
	}
	function CheckIfExitStateCode($StateCode)
	{
		$this->db->select('*');
		$this->db->from('mStates');
		$this->db->where('StateCode',$StateCode);
		$result=$this->db->get()->num_rows();
		if($result == 0)
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

