<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Emailtemplatemodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
		$this->load->library('session');
	}


	function getemailtemplate()
	{
		$this->db->select("EmailTemplateUID,EmailTemplateName,ToMailID,BCCMailID,Subject,Body");
		$this->db->from('mEmailTemplate');
		$this->db->where('CustomerUID',$this->parameters['DefaultClientUID']);
		$result=$this->db->get();
		return $result->result();
	}

	function UpdateEmailtemplate($post)
	{
		$data = array('EmailTemplateName'=>$post['EmailTemplateName'],'ToMailID'=>$post['ToMailID'],'BCCMailID'=>$post['BCCMailID'],'Subject'=>$post['Subject'],'Body'=>$post['Body']);
		$this->db->where(array('EmailTemplateUID'=>$post['EmailTemplateUID']));
		$this->db->update('mEmailTemplate',$data);
		if($this->db->affected_rows() >0)
		{
			return 1;
		}
		else
		{
			return 0;
		}

	}
	function SaveEmailtemplate($post)
	{
		//'ToMailID'=>$post['ToMailID'],'BCCMailID'=>$post['BCCMailID']
		$data=array('EmailTemplateName'=>$post['EmailTemplateName'],'Subject'=>$post['Subject'],'Body'=>$post['Body'],'CreatedByUserUID'=>$this->session->userdata('UserUID'),'CustomerUID'=>$this->parameters['DefaultClientUID']);
		$this->db->insert('mEmailTemplate',$data);
		if($this->db->affected_rows() >0)
		{
			return $this->db->insert_id(); 
		}
		else
		{
			return 0;
		}
	}
	function deleteTemplateEmail($Id){
		  $this->db->where('EmailTemplateUID',$Id);
	      $this->db->delete('mEmailTemplate');
	      if($this->db->affected_rows() > 0)
	      {
	        return 1;
	      } else {
	        return 0;
	      }	
	}
}
?>
