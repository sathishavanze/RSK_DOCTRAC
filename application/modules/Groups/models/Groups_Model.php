<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Groups_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function get_teamleaderusers()
	{
		$query = $this->db->query("SELECT * FROM mUsers LEFT JOIN mRole ON mRole.RoleUID = mUsers.RoleUID WHERE mUsers.Active = ".STATUS_ONE." AND mUsers.CustomerUID = ".$this->parameters['DefaultClientUID']." AND mUsers.Active = ".STATUS_ONE." ");
		return $query->result();
	}

	function get_users()
	{
		//$query = $this->db->query("SELECT * FROM mUsers LEFT JOIN mRole ON mRole.RoleUID = mUsers.RoleUID WHERE mRole.RoleTypeUID IN (".$this->config->item('Internal Roles')['Agent'].") AND mUsers.CustomerUID = ".$this->parameters['DefaultClientUID']." AND mUsers.Active = ".STATUS_ONE." ");
		$query = $this->db->query("SELECT * FROM mUsers LEFT JOIN mRole ON mRole.RoleUID = mUsers.RoleUID WHERE  mUsers.CustomerUID = ".$this->parameters['DefaultClientUID']." AND mUsers.Active = ".STATUS_ONE." ");
		return $query->result();
	}

	function savegroup($post)
	{
		$GroupUID = $post['GroupUID'];

		$this->db->trans_begin();

		if(empty($post['GroupUID'])) {
			$groupdata = array('GroupName'=>$post['GroupName'],'CreatedByUserUID'=>$this->loggedid,'CreatedOn'=>Date('Y-m-d H:i:s', strtotime("now")),'ModifiedByUserUID'=>Date('Y-m-d H:i:s', strtotime("now")));
			$this->db->insert('mGroups',$groupdata);
			$GroupUID = $this->db->insert_id();
		}

		$this->db->where('GroupUID',$GroupUID);
		$this->db->update('mGroups',array('GroupName'=>$post['GroupName'],'Active'=>(isset($post['Active']) ? 1 : 0),'ModifiedByUserUID'=>date('Y-m-d H:i:s', strtotime("now"))));

		//DELETE CUSTOMER
		$this->db->where('GroupUID',$GroupUID);
		$this->db->delete('mGroupCustomers');
		//INSERT CUSTOMER
		/* if(!empty($post['GroupCustomerUID'])) {
			foreach ($post['GroupCustomerUID'] as $Customerkey => $GroupCustomerUID) {
				$insertdata = array('GroupUID'=>$GroupUID,'GroupCustomerUID'=>$GroupCustomerUID);
				$this->db->insert('mGroupCustomers',$insertdata);
			}
		} */
		$insertdata = array('GroupUID'=>$GroupUID,'GroupCustomerUID'=>$post['GroupCustomerUID']);
		$this->db->insert('mGroupCustomers',$insertdata);

		//DELETE TEAM LEADERS
		$this->db->where('GroupUID',$GroupUID);
		$this->db->delete('mGroupTeamLeaders');
		//INSERT USER
		if(!empty($post['GroupTeamUserUID'])) {
			foreach ($post['GroupTeamUserUID'] as $Userkey => $GroupTeamUserUID) {
				$insertdata = array('GroupUID'=>$GroupUID,'GroupTeamUserUID'=>$GroupTeamUserUID);
				$this->db->insert('mGroupTeamLeaders',$insertdata);
			}
		}

		//DELETE CUSTOMER
		$this->db->where('GroupUID',$GroupUID);
		$this->db->delete('mGroupUsers');
		//INSERT USER
		if(!empty($post['GroupUserUID'])) {
			foreach ($post['GroupUserUID'] as $Userkey => $GroupUserUID) {
				$insertdata = array('GroupUID'=>$GroupUID,'GroupUserUID'=>$GroupUserUID);
				$this->db->insert('mGroupUsers',$insertdata);
			}
		}
		//DELETE STATE
		$this->db->where('GroupUID',$GroupUID);
		$this->db->delete('mGroupStates');
		//INSERT STATE
		if(!empty($post['GroupStateUID'])) {
			foreach ($post['GroupStateUID'] as $Statekey => $GroupStateUID) {
				$insertdata = array('GroupUID'=>$GroupUID,'GroupStateUID'=>$GroupStateUID);
				$this->db->insert('mGroupStates',$insertdata);
			}
		}
		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	function get_group($GroupUID)
	{
		$this->db->select('*');
		$this->db->from('mGroups');
		$this->db->order_by('GroupName','asc');
		$this->db->where('GroupUID',$GroupUID);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	function check_group($GroupUID)
	{
		$this->db->select('*');
		$this->db->from('mGroups');
		$this->db->where('GroupUID',$GroupUID);
		$query = $this->db->get();
		$row = $query->num_rows();
		return $row;
	}

	function GetState(){
		$this->db->select('*');
		$this->db->from('mStates');
		return $this->db->get()->result();
	}

	function get_groupcustomer($GroupUID){
		// $this->db->select('GROUP_CONCAT(mCustomer.CustomerUID) AS CustomerUID',false);
		$this->db->select('mGroupCustomers.GroupCustomerUID AS CustomerUID');
		$this->db->from('mGroupCustomers');
		$this->db->join('mCustomer', 'mCustomer.CustomerUID = mGroupCustomers.GroupCustomerUID');
		$this->db->where('GroupUID',$GroupUID);
		return $this->db->get()->row()->CustomerUID;
	}

	function get_groupteamleadusers($GroupUID){
		$this->db->select('GROUP_CONCAT(mUsers.UserUID) AS UserUID',false);
		$this->db->from('mGroupTeamLeaders');
		$this->db->join('mUsers', 'mUsers.UserUID = mGroupTeamLeaders.GroupTeamUserUID');
		$this->db->where('GroupUID',$GroupUID);
		return $this->db->get()->row()->UserUID;
	}

	function get_groupusers($GroupUID){
		$this->db->select('GROUP_CONCAT(mUsers.UserUID) AS UserUID',false);
		$this->db->from('mGroupUsers');
		$this->db->join('mUsers', 'mUsers.UserUID = mGroupUsers.GroupUserUID');
		$this->db->where('GroupUID',$GroupUID);
		return $this->db->get()->row()->UserUID;
	}

	function get_groupstate($GroupUID){
		$this->db->select('GROUP_CONCAT(mStates.StateUID) AS StateUID',false);
		$this->db->from('mGroupStates');
		$this->db->join('mStates', 'mStates.StateUID = mGroupStates.GroupStateUID');
		$this->db->where('GroupUID',$GroupUID);
		return $this->db->get()->row()->StateUID;
	}

	private function _get_datatables_query($countall=false)
	{
		$order = array('mGroups.GroupName');
		$column_order = array('mGroups.GroupName');
		$column_search = array('mGroups.GroupName');

		$this->db->select("*");
		//$this->db->select("@rownum := @rownum + 1 AS rank",false);
		$this->db->from('mGroups');
		//$this->db->from('(SELECT @rownum := 0) r');
		$this->db->join('mGroupCustomers','mGroupCustomers.GroupUID=mGroups.GroupUID','inner');
		$this->db->where('GroupCustomerUID',$this->parameters['DefaultClientUID']);

		if($countall == false){
			$i = 0;
			foreach ($column_search as $item) 
			{
				if($_POST['search']['value'] != ''){
					($i===0) ? $this->db->like($item, $_POST['search']['value']) : $this->db->or_like($item, $_POST['search']['value']);
				}
				$column_search[$i] = $item;
				$i++;
			}
			if (!empty($_POST['order'])) 
			{ 
			// here order processing 
				if($column_order[$_POST['order']['0']['column']] != '')
				{
					$this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
				}   
			} else if (isset($order)) {
				$this->db->order_by(key($order), $order[key($order)]);  
			}	
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}
	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}
	public function count_all()
	{
		$this->_get_datatables_query(true);
		$query = $this->db->get();
		return $query->num_rows();
	}

}
?>

