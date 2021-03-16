<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ACL
{
    protected $CI;

    protected $userId = null;

    protected $userRoleId = null;

    protected $guestPages = [
        'Login/Index',
        'Login/Logout',
        'Dashboard/Index'
    ];

    protected $_config = [
        'acl_table_users' => 'mUsers',
        'acl_users_fields' => [
            'id' => 'UserUID',
            'role_id' => 'RoleUID'
        ],
        'acl_table_resources' => 'mResources',
        'acl_resources_fields' => [
            'id' => 'ResourceUID',
            'controller' => 'controller'
        ],
        'acl_table_permissions' => 'mPermissions',
        'acl_permissions_fields' => [
            'id' => 'PermissionUID',
            'resource_id' => 'ResourceUID',
            'action' => 'PermissionName'
        ],
        'acl_table_role_permissions' => 'mRolePermissions',
        'acl_role_permissions_fields' => [
            'id' => '',
            'role_id' => 'RoleUID',
            'permission_id' => 'PermissionUID'
        ],
        'acl_user_session_key' => 'UserUID'
    ];

    /**
     * Constructor
     *
     * @param array $config            
     */
    public function __construct($config = array())
    {
        $this->CI = &get_instance();
        
        // Load Session library
        $this->CI->load->library('session');
        
        // Load ACL model
        $this->CI->load->model('ACL_Model');
    }

    public function getAclConfig($key = null)
    {
        if ($key) {
            return $this->_config[$key];
        }

        return $this->_config;
    }
    
    // --------------------------------------------------------------------

    /**
     * Check is controller/method has access for role
     *
     * @access public
     * 
     * @return bool
     */
    public function hasAccess()
    {
        if ($this->getUserId()) {
            if (in_array($this->CI->session->userdata('RoleType'), $this->CI->config->item('SuperAccess'))) {
                return true;
            }
            $permissions = $this->CI->ACL_Model->getRolePermissions($this->getUserRoleId());
            // echo '<pre>'; print_r($permissions); exit;
            if (count($permissions) > 0) {
                $currentPermission = strtolower($this->CI->uri->rsegment(1) . '/' . $this->CI->uri->rsegment(2));
                if (in_array($currentPermission, $permissions)) {
                    return true;
                }
            }
        }

        return false;
    }
    
    // --------------------------------------------------------------------

    /**
     * Return the value of user id from the session.
     * Returns 0 if not logged in
     *
     * @access private
     * @return int
     */
    private function getUserId()
    {
        if ($this->userId == null) {
            $this->userId = $this->CI->session->userdata($this->_config['acl_user_session_key']);

            if ($this->userId === false) {
                $this->userId = null;
            }
        }

        return $this->userId;
    }
    
    // --------------------------------------------------------------------

    /**
     * Return user role
     *
     * @return int
     */
    private function getUserRoleId()
    {
        if ($this->userRoleId == null) {
            // Set the role
            $this->userRoleId = $this->CI->ACL_Model->getUserRoleId($this->getUserId());

            if (!$this->userRoleId) {
                $this->userRoleId = 0;
            }
        }

        return $this->userRoleId;
    }

    public function getGuestPages()
    {
        return $this->guestPages;
    }
}