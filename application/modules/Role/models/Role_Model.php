<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Role_Model extends MY_Model {

	
	function __construct()
	{ 
		parent::__construct();
	}

	function GetRoleTypeDetails()
	{ 
		$this->db->select("*");
		$this->db->from('mRoleType');
		$query = $this->db->get();
		return $query->result();
	}
	
	function GetRoleDetails(){
		$this->db->select('*');
		$query = $this->db->get('mRole');
		return $query->result();
	}

	function GetFieldSection(){
		$this->db->select('*');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", CustomerUID)",NULL, FALSE);
		}
		$this->db->group_by('FieldSection');
		$query = $this->db->get('mResources');
		return $query->result();
	}

	function GetResource($FieldSection){
		$this->db->select('*');
		$this->db->where('FieldSection',$FieldSection);
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", CustomerUID)",NULL, FALSE);
		}
		$this->db->where('mResources.Active',STATUS_ONE);
		$query = $this->db->get('mResources');
		return $query->result();
	}
	function GetRole(){
		$this->db->select('*');
		$query = $this->db->get('mRoleResources');
		return $query->result();
	}

	function GetRoleType(){
		$this->db->select('*');
		$this->db->from('mRoleType');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where_not_in('RoleTypeUID',$this->config->item('Super Admin'));
		}
		$query = $this->db->get();
		return $query->result();
	}

	function GetRoleDetailsById($RoleUID){

		$this->db->where(array("RoleUID"=>$RoleUID));
		$query = $this->db->get('mRole');
		return $query->row();
	}

	function GetRoleResourceByRoleUID_ResourceUID($RoleUID, $ResourceUID)
	{
		if (!empty($this->Common_Model->get_row('mRoleResources', ['RoleUID'=>$RoleUID, 'ResourceUID'=>$ResourceUID]))) {
			return 'checked';
		}
		return '';
	}


	function SaveRoleDetails($post)
	{


		$fieldArray = array(
			"RoleName"=>$post['RoleName'],
			"RoleTypeUID"=>$post['RoleTypeUID'],
			"DefaultScreen"=>$post['DefaultScreen'],
			"IsAssigned"=>$post['IsAssigned'],
			"OrderQueue"=>$post['OrderQueue'],
			"IsReverseEnabled"=>$post['IsReverseEnabled'],
			"IsSelfAssignEnabled"=>$post['IsSelfAssignEnabled'],
			"IsLockExpirationRestricted"=>$post['IsLockExpirationRestricted']
		); 
		if (isset($post['CustomerUID']) && !empty($post['CustomerUID'])) {
			$fieldArray['CustomerUID'] = $post['CustomerUID'];
		} else {
			$fieldArray['CustomerUID'] = $this->parameters['DefaultClientUID'];
		}
		$this->db->insert('mRole',$fieldArray);
		$RoleUID = $this->db->insert_id();
		if(isset($post['WorkflowPermission']) && $RoleUID>0)
		{
			if($post['WorkflowPermission'] != '')
			{
				foreach ($post['WorkflowPermission'] as $key => $value) 
				{
					$resource = array(
						"RoleUID"=>$RoleUID,
						"ResourceUID"=>$value,
					);
					//echo '<pre>';print_r($resource);exit;
					$this->db->insert('mRoleResources',$resource);
				}
			}


			foreach ($post['WorkflowPermission'] as $key => $value) 
			{
				$this->db->select('*');
				$this->db->from('mPermissions');
				$this->db->where('mPermissions.ResourceUID',$value);
				$mPermissions = $this->db->get()->result();
				foreach ($mPermissions as $key => $permissionrow) {
					$rolepermissions_array=['RoleUID'=>$RoleUID, 'PermissionUID'=>$permissionrow->PermissionUID];
					$this->db->insert('mRolePermissions', $rolepermissions_array);
				}

			}
			if($RoleUID>0)
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
	}


	function UpdateRoleDetails($post)
	{
		$Active=isset($post['Active']) ? 1 : 0;
		$fieldArray = array(
			"RoleName"=>$post['RoleName'],
			"DefaultScreen"=>$post['DefaultScreen'],
			"RoleTypeUID"=>$post['RoleTypeUID'],
			"Active"=>$Active,
			"IsAssigned"=>$post['IsAssigned'],
			"OrderQueue"=>$post['OrderQueue'],
			"IsReverseEnabled"=>$post['IsReverseEnabled'],
			"IsSelfAssignEnabled"=>$post['IsSelfAssignEnabled'],
			"IsLockExpirationRestricted"=>$post['IsLockExpirationRestricted'],
			"AssignGetNextOrder"=>$post['AssignGetNextOrder']
		); 
		if (isset($post['CustomerUID']) && !empty($post['CustomerUID'])) {
			$fieldArray['CustomerUID'] = $post['CustomerUID'];
		}
		$mResources = $this->Common_Model->get('mResources');


		/*^^^^ DB OPERATION STARTS ^^^^*/

		$this->db->trans_begin();
		$this->db->where(array("RoleUID"=>$post['RoleUID']));
		$this->db->update('mRole',$fieldArray);
		$RoleUID = $post['RoleUID'];

		$this->db->delete('mRoleResources', ['RoleUID'=>$RoleUID]);
		if(isset($post['WorkflowPermission']) && $RoleUID>0)
		{
			if(!empty($post['WorkflowPermission']))
			{
				foreach ($mResources as $key => $value) 
				{
					if (isset($post['WorkflowPermission'][$value->ResourceUID])) {

						$resource = array(
							"RoleUID"=>$RoleUID,
							"ResourceUID"=>$value->ResourceUID,
						);
						$this->db->insert('mRoleResources', $resource);
					}
				}
			}
			$this->db->query("DELETE FROM mRolePermissions WHERE RoleUID=".$RoleUID);
			foreach ($post['WorkflowPermission'] as $key => $value) 
			{
				$this->db->select('*');
				$this->db->from('mPermissions');
				$this->db->where('mPermissions.ResourceUID',$value);
				$mPermissions = $this->db->get()->result();
				foreach ($mPermissions as $key => $permissionrow) {
					$rolepermissions_array=['RoleUID'=>$RoleUID, 'PermissionUID'=>$permissionrow->PermissionUID];
					$this->db->insert('mRolePermissions', $rolepermissions_array);
				}
			}
		}
		if ($this->db->trans_status()===false) {
			$this->db->trans_rollback();
			return false;
		}
		else{
			$this->db->trans_commit(); 
			return true;
		}
	}

	function getpermissonsbysectionname($sectionname='')
	{
		return $this->db->get_where('mPermissions', ['SectionName'=>$sectionname])->result();
	}




	function GetRoles()
	{
		$this->db->select("mRole.*,mRoleType.RoleTypeName");
		$this->db->from('mRole');
		$this->db->join('mRoleType','mRole.RoleTypeUID=mRoleType.RoleTypeUID','left');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", CustomerUID)",NULL, FALSE);
			$this->db->where_not_in('mRole.RoleTypeUID',$this->config->item('Super Admin'));
		}
		return $this->db->get()->result(); 
	}

	function GetPermissions(){
		$GetPermissions=$this->db->query("SELECT * FROM mPermissions WHERE SectionName !='Common' GROUP BY SectionName ");
		return $GetPermissions->result();
	}

	function get_allresources(){
		$this->db->select('*');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where("FIND_IN_SET(".$this->parameters['DefaultClientUID'].", CustomerUID)",NULL, FALSE);
		}
		$this->db->where_in('FieldSection',['WORKFLOW','SUPERVISION']);
		$this->db->where('Active',STATUS_ONE);
		$query = $this->db->get('mResources');

		return $query->result();
	}

	function GetCustomerDetails()
	{
		$this->db->select("*");
		$this->db->from('mCustomer');
		$this->db->where('Active',STATUS_ONE);
		return $this->db->get()->result();
	}

}
?>
