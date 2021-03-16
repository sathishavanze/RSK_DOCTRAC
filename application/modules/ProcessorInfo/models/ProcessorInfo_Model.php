<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProcessorInfo_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GetProcessorsDetails($ProcessorUID = false){
		$this->db->select("*");
		$this->db->from('mProcessor');
		$this->db->where('ClientUID',$this->parameters['DefaultClientUID']);
		if ($ProcessorUID) {
			$this->db->where('ProcessorUID', $ProcessorUID);
			return $this->db->get()->row();
		}
		return $this->db->get()->result();
	}

	function SaveProcessor($post)
	{	

		$TableData = [
			'ClientUID' => $this->parameters['DefaultClientUID'],
			'FirstName' => $post['FirstName'],
			'LastName' => $post['LastName'],
			'TeamLeader' => $post['TeamLeader'],
			'Manager' => $post['Manager'],
			'VP' => $post['VP'],
			'PhoneNumber' => $post['PhoneNumber'],
			'HoursofOperation' => $post['HoursofOperation'],
			'TimeZone' => $post['TimeZone']
		];

		if (isset($post['ProcessorUID']) && !empty($post['ProcessorUID'])) {

			$TableData['ModifiedOn'] = date('Y-m-d H:i:s');
			$TableData['ModifiedByUserUID'] = $this->loggedid;
			$TableData['Active'] = isset($post['Active']) ? 1 : 0;

			$this->db->where('ProcessorUID', $post['ProcessorUID']);
			$this->db->update('mProcessor',$TableData);

		} else {

			$TableData['CreatedOn'] = date('Y-m-d H:i:s');
			$TableData['CreatedByUserUID'] = $this->loggedid;

			$this->db->insert('mProcessor',$TableData);
		}	

		return ($this->db->affected_rows()) ? true : false;

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

