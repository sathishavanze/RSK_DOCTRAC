<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class QuestionTypemodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function SaveQuestionType($post)
	{

		// $Active = isset($post['Active']) ? 1 : 0;
		$Users = array('QuestionTypeName' => $post['QuestionTypeName'],'Active' => 1);
		
		$this->db->insert('mQuestionType',$Users);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}
	function UpdateQuestionType($post){
		
    $Active = isset($post['Active']) ? 1 : 0;

		$Users = array('QuestionTypeName' => $post['QuestionTypeName'],'Active' => $Active);
		$this->db->where(array('QuestionTypeUID' => $post['QuestionTypeUID']));
		$this->db->update('mQuestionType',$Users);
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

}
?>

