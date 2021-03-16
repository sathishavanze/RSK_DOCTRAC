<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class JuniorProcessorGroups_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function get_processorusers()
	{
		$this->db->select('mUsers.UserUID, mUsers.UserName, mUsers.LoginID');
		$this->db->from('mUsers');
		$this->db->join('mRole','mRole.RoleUID = mUsers.RoleUID','LEFT');
		$this->db->where('mRole.RoleTypeUID', $this->config->item('RoleType')['Processor']);
		$this->db->where('mUsers.Active',STATUS_ONE);
		$this->db->where('mRole.Active',STATUS_ONE);
		$this->db->order_by('mUsers.UserName','ASC');
		return $this->db->get()->result();
	}

	function SaveJuniorProcessorGroup($post)
	{
		$GroupUID = $post['GroupUID'];

		$this->db->trans_begin();

		if(empty($post['GroupUID'])) {
			$JuniorGroupData = array('GroupCustomerUID'=>$this->parameters['DefaultClientUID'],'JuniorProcessorUserUID'=>$post['JuniorProcessorUserUID'],'Active'=>isset($post['Active']) ? 1 : 0, 'CreatedByUserUID'=>$this->loggedid);
			$this->db->insert('mJuniorProcessorGroup',$JuniorGroupData);
			$GroupUID = $this->db->insert_id();
		} else {
			$JuniorGroupData = array('JuniorProcessorUserUID'=>$post['JuniorProcessorUserUID'],'Active'=>isset($post['Active']) ? 1 : 0,'ModifiedByUserUID'=>$this->loggedid,'ModifiedOn'=>date('Y-m-d H:i:s'));
			$this->db->where('GroupUID',$GroupUID);
			$this->db->update('mJuniorProcessorGroup',$JuniorGroupData);

			// Delete Processor Data
			$this->db->where('GroupUID',$GroupUID);
			$this->db->delete('mJuniorProcessorUsers');

			// Delete workflow data
			$this->db->where('GroupUID',$GroupUID);
			$this->db->delete('mJuniorProcessorWorkflows');
		}

		// insert junior processor data
		if (!empty($post['ProcessorUserUID'])) {
			foreach ($post['ProcessorUserUID'] as $key => $value) {
				$insertdata = array('GroupUID'=>$GroupUID,'ProcessorUserUID'=>$value);
				$this->db->insert('mJuniorProcessorUsers',$insertdata);		
			}
		}

		// insert junior processor data
		if (!empty($post['WorkflowModuleUID'])) {
			foreach ($post['WorkflowModuleUID'] as $workflowkey => $WorkflowModuleUID) {
				if (!empty($post['QueueUID'][$WorkflowModuleUID])) {
					foreach ($post['QueueUID'][$WorkflowModuleUID] as $queuekey => $QueueUID) {
						$this->db->insert('mJuniorProcessorWorkflows',array('GroupUID'=>$GroupUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'QueueUID'=>$QueueUID));
					}					

				}

				// Insert IsKickback Data
				if (!empty($post['IsKickBack'][$WorkflowModuleUID])) {					
					$this->db->insert('mJuniorProcessorWorkflows',array('GroupUID'=>$GroupUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsKickBack'=>isset($post['IsKickBack'][$WorkflowModuleUID]) ? 1 : NULL));
				}
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

	function GetJuniorProcessorGroupDetails($GroupUID, $ReturnType = '')
	{
		$this->db->select('mJuniorProcessorGroup.GroupUID, mJuniorProcessorGroup.JuniorProcessorUserUID, mJuniorProcessorGroup.Active, GROUP_CONCAT(DISTINCT mJuniorProcessorUsers.ProcessorUserUID) AS ProcessorUserUIDs, GROUP_CONCAT(DISTINCT mJuniorProcessorWorkflows.WorkflowModuleUID) AS WorkflowModuleUIDs');
		$this->db->from('mJuniorProcessorGroup');
		$this->db->join('mJuniorProcessorUsers','mJuniorProcessorUsers.GroupUID = mJuniorProcessorGroup.GroupUID','LEFT');
		$this->db->join('mJuniorProcessorWorkflows','mJuniorProcessorWorkflows.GroupUID = mJuniorProcessorGroup.GroupUID','LEFT');
		$this->db->where('mJuniorProcessorGroup.GroupUID',$GroupUID);
		$this->db->where('GroupCustomerUID',$this->parameters['DefaultClientUID']);
		if (!empty($ReturnType) && $ReturnType == 'count') {
			return $this->db->get()->num_rows();
		} elseif (!empty($ReturnType) && $ReturnType == 'row') {
			return $this->db->get()->row();
		}
		return $this->db->get()->result();
	}
	
	private function _get_datatables_query($countall=false)
	{
		$column_order = array('mUsers.UserName');
		$column_search = array('mUsers.UserName');

		$this->db->select('mUsers.UserName, mJuniorProcessorGroup.Active, mJuniorProcessorGroup.GroupUID');
		$this->db->from('mJuniorProcessorGroup');
		$this->db->join('mUsers','mUsers.UserUID = mJuniorProcessorGroup.JuniorProcessorUserUID','LEFT');
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

	// Check junior processor group is there
	public function CheckJuniorProcessorGroupExist($post)
	{
		$this->db->select('1');
		$this->db->from('mJuniorProcessorGroup');
		$this->db->where('JuniorProcessorUserUID',$post['JuniorProcessorUserUID']);
		$this->db->where('GroupCustomerUID',$this->parameters['DefaultClientUID']);
		if (!empty($post['GroupUID'])) {
			$this->db->where_not_in('GroupUID',$post['GroupUID']);
		}
		if ($this->db->get()->num_rows() > 0) {
			return true;
		}
		return false;
	}

	// update group status
	function UpdateJuniorProcessorGroupStatus($GroupUID,$Active) {
		$this->db->where('GroupUID',$GroupUID);
		$this->db->update('mJuniorProcessorGroup', array('Active'=>$Active));
		if($this->db->affected_rows()){
			$data=array('validation_error' => 0,'message' => 'Group Status Updated Successfully.','type'=>'success');
		}
		else{
			$data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
		}
		return $data;
	}

	/**
	 *Function get CustomerWorkflowQueues 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Saturday 22 August 2020.
	*/
	public function getCustomerWorkflowQueues($WorkflowModuleUID)
	{
		$this->db->select('mQueues.QueueUID, mQueues.QueueName');
		$this->db->from('mQueues');
		$this->db->where('mQueues.WorkflowModuleUID', $WorkflowModuleUID);
		if (isset($this->parameters['DefaultClientUID']) && !empty($this->parameters['DefaultClientUID'])) {
			$this->db->where('mQueues.CustomerUID', $this->parameters['DefaultClientUID']);
		}
		$this->db->where('mQueues.Active', 1);
		return $this->db->get()->result();
	}

	/**
	*Function Get Workflow and queue details 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Saturday 22 August 2020.
	*/
	public function GetJuniorWorkflowQueueDetails($GroupUID,$WorkflowModuleUID)
	{
		$this->db->select('QueueUID,IsKickBack');
		$this->db->from('mJuniorProcessorWorkflows');
		$this->db->where('GroupUID',$GroupUID);
		$this->db->where('WorkflowModuleUID',$WorkflowModuleUID);
		$result = $this->db->get()->result_array();
		return $result;
	}

}
?>

