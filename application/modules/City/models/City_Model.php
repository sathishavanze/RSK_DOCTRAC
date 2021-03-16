 <?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class City_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function cityadd($post)
	{
		
		$city=array('CityName'=>$post['city_name'],'CountyUID'=>$post['CountyUID'],'StateUID'=>$post['StateUID'],'ZipCode'=>$post['zipcode']);
			$this->db->insert('mCities',$city);
			// print_r($city);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	function Updatecitysave($post)
	{
		
		$city=array('CityName'=>$post['Upcity_name'],'CountyUID'=>$post['UpCountyUID'],'StateUID'=>$post['UpStateUID'],'ZipCode'=>$post['Upzipcode']);
		    $Upid=$this->UpCityid=$post['UpCityUID'];
		    // echo $Upid;exit;
		    $this->db->where('CityUID',$Upid);
			$this->db->update('mCities',$city);
			

			// print_r($city);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	function GetCityDetails(){
		$this->db->limit(100);
		$this->db->select("mCities.*,mStates.StateName,mCounties.CountyName");
		$this->db->from('mCities');
		$this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
		$this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
		return $this->db->get()->result();
	}
     function GetState(){
	  	$this->db->select('*');
	  	$this->db->from('mStates');
	  	return $this->db->get()->result();


	  }

	  function citypagination($post)
	  {
	  	
		$this->db->select("mCities.*,mStates.StateName,mCounties.CountyName");
		$this->db->from('mCities');
		$this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
		$this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
		if (!empty($post['search_value'])) {
			$like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
            	$like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
            } else {
            	$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
            }
          }
          $like .= ") ";
          $this->db->where($like, null, false);
        }

        if (!empty($post['order']))
        {
      	// here order processing
        	if($post['column_order'][$post['order'][0]['column']]!='')
        	{
        		$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        	}
        } else if (isset($this->order)) {
        	$order = $this->order;
        	$this->db->order_by(key($order), $order[key($order)]);
        }
        else{
          $this->db->order_by('mCities.CityUID', 'ASC');
        }


	    if ($post['length']!='') {
	       $this->db->limit($post['length'], $post['start']);
	    }
	    $query = $this->db->get();
	    return $query->result();
	  }

	  function count_all()
	  {
	  
		$this->db->select("mCities.*,mStates.StateName,mCounties.CountyName");
		$this->db->from('mCities');
		$this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
		$this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
		$query = $this->db->count_all_results();
  	    return $query;
	  }

	  function count_filtered($post)
	  {
	  	
		$this->db->select("mCities.*,mStates.StateName,mCounties.CountyName");
		$this->db->from('mCities');
		$this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
		$this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
		if (!empty($post['search_value'])) {
			$like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
            	$like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
            } else {
            	$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
            }
          }
          $like .= ") ";
          $this->db->where($like, null, false);
        }

        if (!empty($post['order']))
        {
      	// here order processing
        	if($post['column_order'][$post['order'][0]['column']]!='')
        	{
        		$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
        	}
        } else if (isset($this->order)) {
        	$order = $this->order;
        	$this->db->order_by(key($order), $order[key($order)]);
        }
        else{
          $this->db->order_by('mCities.CityUID', 'ASC');
        }
        $query = $this->db->get();
  	     return $query->num_rows();

	  }

	  function Getcounty($stateid){
	  	
	  	$res = $this->db->query("SELECT * FROM mCounties WHERE StateUID=$stateid");
	  	return $res->result();
	  }
	  
	  function Updatecity($cityuid){
	  	$Updatecity = $this->db->query("SELECT *,B.CountyName FROM mCities A LEFT JOIN mCounties B ON A.CountyUID =B.CountyUID WHERE CityUID=$cityuid");
	  	$result = $Updatecity->result();
	  	return $result;
	  }

}
?>

