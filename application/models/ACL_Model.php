<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ACL_Model extends MY_Model {

	// --------------------------------------------------------------------

	/**
	 * Get current user by session info
	 * @param   int $userId
	 * @return  array
	 */
	public function getUserRoleId($userId = 0)
	{
	    $query = $this->db->select("u.{$this->acl->getAclConfig('acl_users_fields')['role_id']} as role_id")
			->from($this->acl->getAclConfig('acl_table_users').' u')
			->where("u.{$this->acl->getAclConfig('acl_users_fields')['id']}", $userId)
			->get();
        
		// User was found
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();
		    
			return $row['role_id'];
		}

		// No role
		return 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Get permissions from database for  particular role
	 *
	 * @param   int $roleId
	 * @return  array
	 */
	public function getRolePermissions($roleId = 0)
	{
	    $query = $this->db->select([
    	        "p.{$this->acl->getAclConfig('acl_permissions_fields')['action']} as action",
    	        "r.{$this->acl->getAclConfig('acl_resources_fields')['controller']} as controller"
            ])
			->from($this->acl->getAclConfig('acl_table_permissions').' p')
			->join($this->acl->getAclConfig('acl_table_resources').' r', "p.{$this->acl->getAclConfig('acl_permissions_fields')['resource_id']} = r.{$this->acl->getAclConfig('acl_resources_fields')['id']}")
			->join($this->acl->getAclConfig('acl_table_role_permissions').' rp', "rp.{$this->acl->getAclConfig('acl_role_permissions_fields')['permission_id']} = p.{$this->acl->getAclConfig('acl_permissions_fields')['id']}")
			->where("rp.{$this->acl->getAclConfig('acl_role_permissions_fields')['role_id']}", $roleId)
			->get();

		$permissions = array();

		// Add to the list of permissions
		foreach ($query->result_array() as $row)
		{		    
			$permissions[] = strtolower($row['controller'] . '/' . $row['action']);
		}

		return $permissions;
	}

}