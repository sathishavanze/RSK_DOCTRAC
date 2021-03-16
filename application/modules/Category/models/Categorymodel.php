<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Categorymodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function SaveCategory($post)
	{
        // $Active = isset($post['Active']) ? 1 : 0;
		$numbers = 1; // Get Max of ID
		$hash = '';
		while (true) {
			$categoryhash = 'Category' . $numbers;
			$hash = substr(md5($categoryhash), 0, 8);
			
			$this->db->where('HashCode', $hash);
			$count = $this->db->get('mCategory')->num_rows();
			if($count==0) 
			{
				
				break;
			}
			$numbers++;
		}
		
		$Users = array('CategoryName' => $post['CategoryName'],'CreatedOn' =>  Date('Y-m-d H:i:s', strtotime("now")), 'HashCode'=>$hash,'Active' => 1);

		if (isset($post['CustomerUID']) && !empty($post['CustomerUID'])) {
			$Users['CustomerUID'] = $post['CustomerUID'];
		} else {
			$Users['CustomerUID'] = $this->parameters['DefaultClientUID'];
		}
		
		$this->db->insert('mCategory',$Users);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}
	function UpdateCategory($post){
		$Active = isset($post['Active']) ? 1 : 0;

		$Users = array('CategoryName' => $post['CategoryName'],'CreatedOn' =>  Date('Y-m-d H:i:s', strtotime("now")),'Active' => $Active);
		$this->db->where(array('CategoryUID' => $post['CategoryUID']));
		$this->db->update('mCategory',$Users);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}


	}

	function CheckLoginUser($loginid)
	{
		$this->db->select("*");
		$this->db->from("mUsers");
		$this->db->where(array('LoginID' => $loginid));
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}

	function CheckExistUserName($UserUID, $LoginID)
	{																	
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows(); 
	}

	public function GetGategoryDetails() {
		$this->db->select('*');
		$this->db->from('mCategory');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('CustomerUID',$this->parameters['DefaultClientUID']);
		}
		return $this->db->get()->result();
	}
}
?>

