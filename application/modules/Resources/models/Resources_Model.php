<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Resources_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function SaveResources($post)
	{
		
		$Resource=array('controller'=>$post['controller'],'FieldName'=>$post['FieldName'],'FieldSection'=>$post['FieldSection'],'NotificationElement'=>$post['NotificationEle'],'MenuBarType'=>$post['MenuBarType'],'IconClass'=>$post['IconClass'],'Position'=>$post['Position'],'Active'=>1,'ParentType'=>$post['ParentType'],'WorkflowModuleUID'=>$post['WorkflowModuleUID'],'CustomerUID'=>$post['CustomerUID']);
			$this->db->insert('mResources',$Resource);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function UpdateResourceSave($post)
	{
		
		$Resource=array('controller'=>$post['controller'],'FieldName'=>$post['FieldName'],'FieldSection'=>$post['FieldSection'],'NotificationElement'=>$post['NotificationEle'],'MenuBarType'=>$post['MenuBarType'],'IconClass'=>$post['IconClass'],'Position'=>$post['Position'],'Active'=>$post['Active'],'ParentType'=>$post['ParentType'],'WorkflowModuleUID'=>$post['WorkflowModuleUID'],'CustomerUID'=>$post['CustomerUID']);
		
		$this->db->where(array('ResourceUID' => $post['ResourceUID']));
		$this->db->update('mResources',$Resource);  
		
		return 1;
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}




	function GetResourcesDetails(){
		$Resources = $this->db->query("SELECT * FROM mResources");
		return $Resources->result();
	}

	function GetWorkflows()
	{
		$this->db->select("*");
		$this->db->from('mWorkFlowModules');
		$this->db->where('Active', 1);
		return $this->db->get()->result();
	}

}
?>

