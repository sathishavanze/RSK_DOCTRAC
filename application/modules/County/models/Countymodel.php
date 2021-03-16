


<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Countymodel extends MY_Model { 
	function product_list(){
		$this->db->select("*");
		$this->db->from('mCounties');
		return $this->db->get()->result();
	}

	function SaveCounties($post){
		$Active=$data['Active']=isset($post['Active']) ? 1 : 0;
		$data = array(
			'CountyCode'=>$post['CountyCode'],'CountyName'=>$post['CountyName'],'StateUID'=>$post['StateUID'],'Active'=>$Active
			);
		$this->db->insert('mCounties',$data);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
		
	}

	function Update_County($post){
		$Active=$data['Active']=isset($post['Active']) ? 1 : 0;
		$user = array(
			'CountyCode'=> $post['CountyCode'],'CountyName'=> $post['CountyName'],'StateUID'  => $post['StateUID'],'Active'=>$Active);

		
		$this->db->where(array('CountyUID' => $post['CountyUID']));
		$result=$this->db->update('mCounties',$user);
		return $result;
	}
	 function GetState(){
     	$this->db->select("*");
     	$this->db->from("mStates");
     	return $this->db->get()->result();
     }
	function getdatabyCountyUID($CountyUID)
	{
		//echo '<pre>';print_r($StateUID);exit;
		$this->db->select('*');
		$this->db->from('mCounties');
		$this->db->where('CountyUID',$CountyUID);
		$result = $this->db->get()->row();
		return $result;
	}	
	function GetDocument()
	{
		$this->db->select("mCounties.*,mstates.StateName");
		$this->db->from('mCounties');
		$this->db->join('mstates','mCounties.StateUID=mstates.StateUID','left');
		return $this->db->get()->result();
	}



}
?>

