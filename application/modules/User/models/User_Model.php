<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class User_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function Useradding($post)
	{
		
		$users=array('UserName'=>$post['username'],'LoginID'=>$post['loginid'],'Password'=>md5($post['password']),'EmailID'=>$post['emailid'],'ContactNo'=>$post['contactno'],'FaxNo'=>$post['faxno'],'PasscodeVerify'=>$post['PasscodeVerify'],'FirstLogin'=>'1','CreatedOn'=>date('Y-m-d H:i:s'),'PasswordUpdatedDate'=>date('Y-m-d H:i:s'),'RoleUID'=>$post['RoleUID'],'CreatedByUserUID'=>$this->loggedid,'Active'=>$post['Active'],'OrderQCSkipPercentage'=>$post['OrderQCSkipPercentage'],'UserLocation'=>$post['UserLocation']);

		if (isset($post['CustomerUID']) && !empty($post['CustomerUID']) && is_numeric($post['CustomerUID'])) {
			$users['CustomerUID'] = $post['CustomerUID'];
		} else {
			$users['CustomerUID'] = $this->parameters['DefaultClientUID'];
		}

		$this->db->insert('mUsers',$users);
		
		if($this->db->affected_rows() > 0)
		{
			return $this->db->insert_id();
		}
		else
		{
			return 0;
		}
	}
	function UserDetailsUpdate($post)
	{
		
		$users=array('UserName'=>$post['username'],'LoginID'=>$post['loginid'],'EmailID'=>$post['emailid'],'ContactNo'=>$post['contactno'],'FaxNo'=>$post['faxno'],'PasscodeVerify'=>$post['PasscodeVerify'],'CreatedOn'=>date('Y-m-d H:i:s'),'PasswordUpdatedDate'=>date('Y-m-d H:i:s'),'RoleUID'=>$post['RoleUID'],'Active'=>$post['Active'],'CreatedByUserUID'=>$this->loggedid,'OrderQCSkipPercentage'=>$post['OrderQCSkipPercentage'],'UserLocation'=>$post['UserLocation']);

		// Check password field is not empty
		if (isset($post['SetPassword']) && !empty($post['SetPassword'])) {
			$users['Password'] = md5($post['SetPassword']);
		}

		if (isset($post['CustomerUID']) && !empty($post['CustomerUID']) && is_numeric($post['CustomerUID'])) {
			$users['CustomerUID'] = $post['CustomerUID'];
		} else {
			$users['CustomerUID'] = $this->parameters['DefaultClientUID'];
		}
		$this->db->where(array('UserUID' => $post['UserUID']));
		$this->db->update('mUsers',$users);  		

		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}




	function GetUsersDetails(){
		$this->db->select("mUsers.*,mRole.RoleName");
		$this->db->from('mUsers');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID','left');
		// $this->db->where('mUsers.CustomerUID IS NULL AND mUsers.LenderUID IS NULL AND mUsers.SettlementAgentUID IS NULL AND mUsers.InvestorUID IS NULL AND mUsers.VendorUID IS NULL OR mUsers.IsInternal = 1');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('mUsers.CustomerUID',$this->parameters['DefaultClientUID']);
			$this->db->where('mRole.CustomerUID',$this->parameters['DefaultClientUID']);
			$this->db->where_not_in('mRole.RoleTypeUID',$this->config->item('Super Admin'));
		}
		return $this->db->get()->result();
	}

	function CheckExistUserName($UserUID, $LoginID)
	{																	
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows(); 
	}
	function SideBarColorChanges($post){
		$new_color = $this->input->post('new_color');
		$Type = $this->input->post('Type');
		$new_image = $this->input->post('new_image');
		$LoginUser = $this->loggedid;
		if ($Type == 'Sidebar_Filter') {
			$UpdateColor = array('SideBar_NavColor' => $new_color);
			$this->db->where('UserUID',$LoginUser);
			$this->db->update('mUsers',$UpdateColor);
			return 1;
		}
		else if ($Type == 'Sidebar_BackGround') {
			$UpdateColor = array('SideBar_BGColor' => $new_color);
			$this->db->where('UserUID',$LoginUser);
			$this->db->update('mUsers',$UpdateColor);
			return 1;
		}
		else if ($Type == 'Sidebar_Image') {
			$ImagePath=parse_url($new_image,PHP_URL_PATH);
			$FinalPath =substr($ImagePath, -24);
			$UpdateColor = array('SideBar_Image' => $FinalPath);
			$this->db->where('UserUID',$LoginUser);
			$this->db->update('mUsers',$UpdateColor);
			return 1;
		}


	}



	public function verifyEmailAddress($verificationcode){  
  		$sql = "update mUsers set VerificationStatus=1 WHERE UserUID=?";
	  	$this->db->query($sql, array('UserUID'=>($verificationcode)));
	  	return $this->db->affected_rows(); 
 }


   function PasswordStore($UserUID,$Password)
	{
	  $data['UserUID'] = $UserUID;
	  $data['Password'] = $Password;
	  $data['CreatedOn'] = date('Y-m-d H:i:s');
	  if($this->db->insert('mUserPasswordVerification',$data))
	  {
	  	return 1;
	  } else {
	  	return 0;
	  }
	}


}
?>

