<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Milestonemodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	/*
	* Fetching the Milestone list from Database to the table
	*
	*/
	
	function milestone_list(){
		$hasil=$this->db->get('mMilestone');
		return $hasil->result();
	}


	/* 
	*Saving data of milestone to the database 
	*
	*/

	function CheckMilestoneExist($post) {
		if (isset($post['MilestoneUID'])) {
			$this->db->select('*');
			$this->db->from('mMilestone');
			$this->db->where('MilestoneName',$post['MilestoneName']);
			$this->db->where_not_in('MilestoneUID',$post['MilestoneUID']);
			$query = $this->db->get();
		} else {
			$query = $this->db->get_where('mMilestone', array('MilestoneName' => $this->input->post('MilestoneName')));
		}
		return $query->num_rows();
	}
	
	function SaveMilestone($post)
	{
		$Active = isset($post['Active']) ? 1 : 0;
			$mileData = array(
			     
				'MilestoneName'  => $this->input->post('MilestoneName'), 
				'Active' => $Active,
			    'CustomerUID' => $this->session->userdata('DefaultClientUID')
			   );
		      $result=$this->db->insert('mMilestone',$mileData);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}

	/* 
	*Updating the data of milestone to the database 
	*
	*/
	function UpdateMilestone($post){
		$Active = isset($post['Active']) ? 1 : 0;

		$Users = array('MilestoneName' => $post['MilestoneName'],'Active' => $Active);
		$this->db->where(array('MilestoneUID' => $post['MilestoneUID']));
		$this->db->update('mMilestone',$Users);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}

	}

	function InActiveQueueDetails($MilestoneUID,$Active) {
		$this->db->where('MilestoneUID',$MilestoneUID);
		$this->db->update('mMilestone', array('Active'=>$Active));
		return $this->db->affected_rows();
	}

}
?>

