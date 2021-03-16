<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class EmailReport_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

 public function ReportUploadDocuments()
  {
    $this->db->select('*')->from('tOrders');
    $this->db->where('StatusUID', 1);
    $this->db->or_where('StatusUID', 9);   
    $query = $this->db->get()->result(); 
    if(count($query)>0)
    {
        return $query;
    }
    else
    {
      return 0;
    }
  }

}
?>
