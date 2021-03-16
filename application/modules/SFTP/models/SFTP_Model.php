<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SFTP_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GetSFTP($post)
	{
		$Users = array('SFTPName' => $post['SFTPName'],'SFTPProtocol' => $post['SFTPProtocol'],'SFTPHost' => $post['SFTPHost'],'SFTPPort' => $post['SFTPPort'],'SFTPUser' => $post['SFTPUser'],'SFTPPassword' => $post['SFTPPassword'],'SFTPKeyFile' => $post['SFTPKeyFile'],'SFTPPath' => $post['SFTPPath'],'EmailTemplateUID'=>$post['EmailTemplateUID'],'SFTPCreatedByDateTime' => Date('Y-m-d H:i:s', strtotime("now")),'SFTPCreatedByUserUID'=>$this->loggedid);
		
	  $this->db->insert('mSFTP',$Users);

		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}
	function UpdateSFTP($post){
		$Users = array('SFTPName' => $post['SFTPName'],'SFTPProtocol' => $post['SFTPProtocol'],'SFTPHost' => $post['SFTPHost'],'SFTPPort' => $post['SFTPPort'],'SFTPUser' => $post['SFTPUser'],'SFTPPassword' => $post['SFTPPassword'],'SFTPKeyFile' => $post['SFTPKeyFile'],'SFTPPath' => $post['SFTPPath'],'EmailTemplateUID'=>$post['EmailTemplateUID']);
			$this->db->where(array('SFTPUID' => $post['SFTPUID']));
		$this->db->update('mSFTP',$Users);
			if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}

	}

	function GetDetails()
	{
		$this->db->select("*");
		$this->db->from("mSFTP");
		return $this->db->get()->result();
	}
       
	

	
	function CheckExistUserName($UserUID, $LoginID)
	{																	
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows(); 
	}

}
?>

