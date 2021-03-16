  <?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Permissions_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function SavePermissions($post)
	{
		
		$Permissions=array('ResourceUID'=>$post['ResourceUID'],'PermissionName'=>$post['PermissionName'],'SectionName'=>$post['SectionName'],'PermissionFieldName'=>$post['PermissionFieldName'],'CreatedBy'=>$this->loggedid,'CreatedOn'=>date('Y-m-d H:i:s'),'Active'=>1);
			$this->db->insert('mPermissions',$Permissions);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function UpdatePermissionsSave($post)
	{
		
		$Permissions=array('ResourceUID'=>$post['ResourceUID'],'PermissionName'=>$post['PermissionName'],'SectionName'=>$post['SectionName'],'PermissionFieldName'=>$post['PermissionFieldName'],'ModifiedBy'=>$this->loggedid,'ModifiedOn'=>date('Y-m-d H:i:s'),'Active'=>$post['Active']);

		$this->db->where(array('PermissionUID' => $post['PermissionUID']));
		$this->db->update('mPermissions',$Permissions);  
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}




	function GetResourcesDet(){
		$Resources = $this->db->query("SELECT * FROM mResources");
		return $Resources->result();
	}

	function GetPermissionsDetails(){
		$Permissions = $this->db->query("SELECT *,B.FieldName FROM mPermissions A LEFT JOIN mResources B ON A.ResourceUID=B.ResourceUID");
		return $Permissions->result();
	}

	function paginationpermission($post)
	{
		$this->db->select("*,mResources.FieldName");
		$this->db->from('mPermissions');
		$this->db->join('mResources','mPermissions.ResourceUID=mResources.ResourceUID','left'); 
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
          $this->db->order_by('mPermissions.PermissionUID', 'ASC');
        }


	    if ($post['length']!='') {	
	       $this->db->limit($post['length'], $post['start']);
	    }
		$result=$this->db->get();
		return $result->result();

	}
	function count_all()
	{
		$this->db->select("*,mResources.FieldName");
		$this->db->from('mPermissions');
		$this->db->join('mResources','mPermissions.ResourceUID=mResources.ResourceUID','left'); 
				$query = $this->db->count_all_results();
  	    return $query;

	}

	function count_filtered($post)
	{
		$this->db->select("*,mResources.FieldName");
		$this->db->from('mPermissions');
		$this->db->join('mResources','mPermissions.ResourceUID=mResources.ResourceUID','left'); 
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

       
       $query = $this->db->get();
  	     return $query->num_rows();
	}

}
?>

