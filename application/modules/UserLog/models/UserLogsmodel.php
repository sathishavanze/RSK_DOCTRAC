<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class UserLogsmodel extends MY_Model {
	function __construct()
	{
		parent::__construct();
	}

		
    function GetUserDetails($login_id,$post)
    {
      if($this->session->userdata('RoleUID') > 6){

        $this->db->select("taudittrail.*,mUsers.UserName");
        $this->db->select("DATE_FORMAT(taudittrail.DateTime, '%m-%d-%Y %h:%i:%s') as Userlogtime",false);
        $this->db->from("taudittrail");
        $this->db->join('mUsers','mUsers.UserUID=taudittrail.UserUID','inner');
        $this->db->where('taudittrail.ModuleName','user-Login');
        $this->db->where('taudittrail.UserUID',$login_id);
        $this->db->group_by('taudittrail.AuditUID');
      }else{

        $this->db->select("taudittrail.*,mUsers.UserName");
        $this->db->select("DATE_FORMAT(taudittrail.DateTime, '%m-%d-%Y %h:%i:%s') as Userlogtime",false);
        $this->db->from("taudittrail");
        $this->db->join('mUsers','mUsers.UserUID=taudittrail.UserUID','inner');
        $this->db->where('taudittrail.ModuleName','user-Login');
        $this->db->group_by('taudittrail.AuditUID');

      }

      if ($post['length']!='') {
        $this->db->limit($post['length'], $post['start']);
      }
      if ($post['formData']!='All' && sizeof(array_filter($post['formData']))!=0)
      {
        $filter = $this->Generate_Advance_search_filter_keywords_User($post);
        $this->db->where($filter, null, false);
      }
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

        if (!empty($post['order'])) {
          // here order processing
          if ($post['column_order'][$post['order'][0]['column']] != '') {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
          }
        }

        return $this->db->get('')->result();
      }
      function count_GetUserDetails($login_id,$post){

  if($this->session->userdata('RoleUID') > 6){

    $this->db->select("taudittrail.*,mUsers.UserName");
    $this->db->select("DATE_FORMAT(taudittrail.DateTime, '%m-%d-%Y %h:%i:%s') as Userlogtime",false);
    $this->db->from("taudittrail");
    $this->db->join('mUsers','mUsers.UserUID=taudittrail.UserUID','inner');
    $this->db->where('taudittrail.ModuleName','user-Login');
    $this->db->where('taudittrail.UserUID',$login_id);
    $this->db->group_by('taudittrail.AuditUID');
    $this->db->order_by('taudittrail.DateTime');

  }else{

    $this->db->select("taudittrail.*,mUsers.UserName");
    $this->db->select("DATE_FORMAT(taudittrail.DateTime, '%m-%d-%Y %h:%i:%s') as Userlogtime",false);
    $this->db->from("taudittrail");
    $this->db->join('mUsers','mUsers.UserUID=taudittrail.UserUID','inner');
    $this->db->where('taudittrail.ModuleName','user-Login');
    $this->db->group_by('taudittrail.AuditUID');
    $this->db->order_by('taudittrail.DateTime');

  }

  if ($post['formData']!='All' && sizeof(array_filter($post['formData']))!=0)
  {
    $filter = $this->Generate_Advance_search_filter_keywords_User($post);
    $this->db->where($filter, null, false);
  }
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

    return $this->db->get('')->num_rows();

}
function filter_GetUserDetails($login_id,$post){

  if($this->session->userdata('RoleUID') > 6){

    $this->db->select("taudittrail.*,mUsers.UserName");
    $this->db->select("DATE_FORMAT(taudittrail.DateTime, '%m-%d-%Y %h:%i:%s') as Userlogtime",false);
    $this->db->from("taudittrail");
    $this->db->join('mUsers','mUsers.UserUID=taudittrail.UserUID','inner');
    $this->db->where('taudittrail.ModuleName','user-Login');
    $this->db->where('taudittrail.UserUID',$login_id);
    $this->db->group_by('taudittrail.AuditUID');
    $this->db->order_by('taudittrail.DateTime');
  }else{

    $this->db->select("taudittrail.*,mUsers.UserName");
    $this->db->select("DATE_FORMAT(taudittrail.DateTime, '%m-%d-%Y %h:%i:%s') as Userlogtime",false);
    $this->db->from("taudittrail");
    $this->db->join('mUsers','mUsers.UserUID=taudittrail.UserUID','inner');
    $this->db->where('taudittrail.ModuleName','user-Login');
    $this->db->group_by('taudittrail.AuditUID');
    $this->db->order_by('taudittrail.DateTime');

  }


  if ($post['formData']!='All' && sizeof(array_filter($post['formData']))!=0)
  {
    $filter = $this->Generate_Advance_search_filter_keywords_User($post);
    $this->db->where($filter, null, false);
  }

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
    return $this->db->get('')->num_rows();

}
function Generate_Advance_search_filter_keywords_User($post)
  {
    $keywords =  array_filter($post['formData']);
    $like = array();

    foreach ($keywords as $key => $item)
    {
        // if datatable send POST for search
      if ($item != '')
      {
        if($key=='UserName')
        {
          $like[] = "UserName='".$item."'";
        }
        else if($key=='IpAddreess')
        {
          $like[] = "IpAddreess='".$item."'";
        }

        else  if($key=='FromDate' && !array_key_exists('ToDate',$keywords))
        {
          $like[] = "DATE(DateTime) = '".date('Y-m-d',strtotime($item))."'";
        }
        else  if($key=='ToDate' && !array_key_exists('FromDate',$keywords))
        {
          $like[] = "DATE(DateTime) = '".date('Y-m-d',strtotime($item))."'";
        }
      }
    }

    if(array_key_exists('FromDate',$keywords) && array_key_exists('ToDate',$keywords))
    {
      $like[] = "DATE(DateTime) BETWEEN '".date('Y-m-d',strtotime($keywords['FromDate']))."' AND '".date('Y-m-d',strtotime($keywords['ToDate']))."'";
    }
    $keyword_where = implode(' AND ', $like);
    return $keyword_where;

  }
}
?>