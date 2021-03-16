<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SubQueues_model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function SubQueuesDetails($QueueUID = FALSE){
		$this->db->select("mQueues.*, mWorkFlowModules.WorkflowModuleUID, mWorkFlowModules.WorkflowModuleName");
		$this->db->from('mQueues');
		$this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID=mQueues.WorkflowModuleUID','left');
		$this->db->where('mQueues.CustomerUID', $this->session->userdata('DefaultClientUID'));
		if ($QueueUID) {
			$this->db->where('mQueues.QueueUID', $QueueUID);
			return $this->db->get()->row_array();
		}
		return $this->db->get()->result();
	}

	function InActiveQueueDetails($QueueUID,$Active) {
		$this->db->where('QueueUID',$QueueUID);
		$this->db->update('mQueues', array('Active'=>$Active));
		return $this->db->affected_rows();
	}

	function SubQueuesActions($post)
	{
		$FormData = array(
			'QueueName' => $post['QueueName'],
			'WorkflowModuleUID ' => $post['WorkflowModuleUID'],
			'FollowupType' => $post['FollowupType'],
			'FollowupDuration' => $post['FollowupDuration'],
			'BusinessHourStartTime' => $post['BusinessHourStartTime'],
			'BusinessHourEndTime' => $post['BusinessHourEndTime'],
			'IsDocsReceived' => $post['IsDocsReceived'],
			'IsStatus' => $post['IsStatus']
		);

		$FormData['IsBorrowerDocs'] = isset($post['IsBorrowerDocs']) ? 1 : 0;
		$FormData['IsFollowup'] = isset($post['IsFollowup']) ? 1 : 0;
		$FormData['SkipWeekend'] = isset($post['SkipWeekend']) ? 1 : 0;
		$FormData['IsBusinessHours'] = isset($post['IsBusinessHours']) ? 1 : 0;
		$FormData['IsDocsReceived'] = isset($post['IsDocsReceived']) ? 1 : 0;
		$FormData['IsStatus'] = isset($post['IsStatus']) ? 1 : 0;

		if(isset($post['QueueUID']) && !empty($post['QueueUID'])) {
			$FormData['Active'] = isset($post['Active']) ? 1 : 0;
			$this->db->where(array("QueueUID"=>$post['QueueUID']));
			$this->db->update('mQueues',$FormData);
		} else {
			$FormData['Active'] = 1;
			$FormData['CustomerUID'] = $this->session->userdata('DefaultClientUID');

			$this->db->insert('mQueues',$FormData);
		}
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

}
?>

