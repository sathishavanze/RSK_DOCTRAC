<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class MilestoneLog_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}
	
	function getMilestoneLogDetails($OrderUID)
	{
		$this->db->select('tOrders.OrderNumber,mMilestone.MilestoneName,tOrderMileStone.CompletedDateTime,mUsers.UserName');
		$this->db->from('tOrderMileStone');
		$this->db->join('tOrders','tOrderMileStone.OrderUID = tOrders.OrderUID','left');
		$this->db->join('mMilestone','tOrderMileStone.MilestoneUID = mMilestone.MilestoneUID','left');
		$this->db->join('mUsers','tOrderMileStone.CompletedByUserUID = mUsers.UserUID','left');
		$this->db->where('tOrderMileStone.OrderUID',$OrderUID);
		$this->db->order_by('tOrderMileStone.tOrderMileStoneUID','DESC');
		return $this->db->get()->result();
	}
}
?>
