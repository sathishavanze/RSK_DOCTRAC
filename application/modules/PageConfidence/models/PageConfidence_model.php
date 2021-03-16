<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class PageConfidence_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

  function SavePage($PageConfidenceDetails)
  {
      $this->db->insert('tPage',$PageConfidenceDetails);
      if($this->db->affected_rows()>0)
      {
        return 1;
      } else {
        return 0;
      }
  }
}
?>
