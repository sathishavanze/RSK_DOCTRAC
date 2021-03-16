<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Automationlogmodel extends MY_Model {
	function __construct()
	{
		parent::__construct();
    $this->loggedid = $this->session->userdata('UserUID');
    $this->UserName = $this->session->userdata('UserName');
    $this->RoleUID = $this->session->userdata('RoleUID');
	}

  public function CreateDirectoryToPath($Path = '')
  {
    if (empty($Path)) 
    {
      die('No Path to create directory');
    }
    if (!file_exists($Path)) {
      if (!mkdir($Path, 0777, true)) die('Unable to create directory');
    }
    chmod($Path, 0777);
  }

  function FilterByProjectUser($RoleUID,$loggedid){
    /*if ($RoleUID != 1) {
     $this->db->join('mProjectUser','mProjectCustomer.ProjectUID = mProjectUser.ProjectUID','left');
     $this->db->where('mProjectUser.UserUID',$loggedid);
    }*/
  }

  function WorkflowQueues_Datatable_Search($post)
  {
    if (!empty($post['search_value'])) {
      $like = "";
      foreach ($post['column_search'] as $key => $item) { 
        if ($key === 0) { 
          $like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
        } else {
          $like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
        }
      }
      $like .= ") ";
      $this->db->where($like, null, false);
    }
  }

  function WorkflowQueues_Datatable_OrderBy($post)
  {
    if (!empty($post['order']))
    {
      if($post['column_order'][$post['order'][0]['column']]!='')
      {
        $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
      }
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->db->order_by(key($order), $order[key($order)]);
    }
  }

  function DateFilterToDate($post)
  {
    $ToDate = $post['advancedsearch']['ToDate'];
    $this->db->where('DATE(tAutomationLog.CreatedDate) <= "'.date('Y-m-d', strtotime($ToDate)).'"', NULL, false);
  }

  function DateFilterFromDate($post)
  {
    $FromDate = $post['advancedsearch']['FromDate'];
    $this->db->where('DATE(tAutomationLog.CreatedDate) >= "'.date('Y-m-d', strtotime($FromDate)).'"', NULL, false);
  }

  function log_advanced_search($post)
  {
    if(isset($post['advancedsearch']['OrderNumber']) && $post['advancedsearch']['OrderNumber'] != '' && $post['advancedsearch']['OrderNumber'] != 'All'){
      $this->db->like('tOrders.OrderNumber',$post['advancedsearch']['OrderNumber']);
    }

    if(isset($post['advancedsearch']['LoanNumber']) && $post['advancedsearch']['LoanNumber'] != '' && $post['advancedsearch']['LoanNumber'] != 'All'){
      $this->db->like('tOrders.LoanNumber',$post['advancedsearch']['LoanNumber']);
    }

    if(isset($post['advancedsearch']['AutomationType']) && $post['advancedsearch']['AutomationType'] != '' && $post['advancedsearch']['AutomationType'] != 'All'){
      $this->db->like('tAutomationLog.AutomationType',$post['advancedsearch']['AutomationType']);
    }

    if(isset($post['advancedsearch']['AutomationStatus']) && $post['advancedsearch']['AutomationStatus'] != '' && $post['advancedsearch']['AutomationStatus'] != 'All'){
      $this->db->like('tAutomationLog.AutomationStatus', $post['advancedsearch']['AutomationStatus']);
    }

    if(isset($post['advancedsearch']['FromDate']) && $post['advancedsearch']['FromDate'])
    {
      $this->DateFilterFromDate($post); 
    }

    if(isset($post['advancedsearch']['ToDate']) && $post['advancedsearch']['ToDate'])
    {
      $this->DateFilterToDate($post);
    }

    return true;
  }

  function total_count()
  {
    $this->db->select("1");
    $this->GetAutomationLog();
    $query = $this->db->count_all_results();
    return $query;
  }

  function count_all()
  {
    $this->db->select("1");
    $this->GetAutomationLog();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);     
    $query = $this->db->count_all_results();
    return $query;
  }

  function count_filtered($post)
  {
    $this->db->select("1");
    $this->GetAutomationLog();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->log_advanced_search($post);
    }
    $this->WorkflowQueues_Datatable_Search($post);
    $this->WorkflowQueues_Datatable_OrderBy($post);
    $query = $this->db->get();
    return $query->num_rows();
  }

  function Func_LogOrders($post,$global=''){
    $this->db->select("tAutomationLog.*");
		$this->db->select("tOrders.*,tOrders.OrderUID,tOrders.LoanNumber");
    $this->GetAutomationLog();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->log_advanced_search($post);
    }
    $this->WorkflowQueues_Datatable_Search($post);
    $this->WorkflowQueues_Datatable_OrderBy($post);
    if ($post['length']!='') {
       $this->db->limit($post['length'], $post['start']);
    }
    $this->db->order_by('tAutomationLog.CreatedDate', 'DESC');
    $output = $this->db->get();
    return $output->result();
	}

  function GetAutomationLogExcelRecords($post)
  {
    $this->db->select("tAutomationLog.*");
    $this->db->select("tOrders.*,tOrders.OrderUID,tOrders.LoanNumber");
    $this->GetAutomationLog();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->log_advanced_search($post);
    }
    $this->db->order_by('tAutomationLog.CreatedDate', 'DESC');
    $query = $this->db->get();
    return $query->result();  
  }

  function GetAutomationLog()
  {    
    $this->db->from('tAutomationLog');
    $this->db->join('tOrders','tOrders.OrderUID = tAutomationLog.OrderUID', 'LEFT');
    $this->db->group_by('tAutomationLog.AutomationLogUID');
  }

  function array_to_list(array $Array)
  {
    static $counter = 0;

    $Output = '<ul>';
    foreach($Array as $Key => $Value){
      $Output .= "<li><strong>{$Key}: </strong>";
      if(is_array($Value)){
        $Output .= $this->array_to_list($Value);
      }else{

        if($Value!='')
        {
        
          $btnid = str_replace(' ', '','btnCopy_'.$counter);
          
          $Output .= '<a id="'.$btnid.'">'.$Value.'</a></span>';
        }
        else
        {
          $Output .= $Value;
        } 
        $counter ++;
      }
      $Output .= '</li>';
    }
    $Output .= '</ul>';
    return $Output;
  }

  function GetOCRResponse($post){
    $DocumentUID = $post['DocumentUID'];
    $OrderUID = $post['OrderUID'];
    $this->db->select("*");
    $this->db->from('tLoanFiles');
    $this->db->where(array('DocumentUID' => $DocumentUID, 'OrderUID' => $OrderUID));
    $this->db->order_by('tLoanFiles.LoanFileUID', 'DESC');
    $query = $this->db->get();
    if($query->num_rows() > 0){
      return $query->row();
    }else{
      return false;
    }
  }
  /* Desc: DOC-617 Show email content for success mails in automation List @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
  function GetEmailResponse($post){
    $EmailUID = $post['EmailUID'];
    $OrderUID = $post['OrderUID'];
    $this->db->select("*");
    $this->db->from('tEmailImport');
    $this->db->where(array('EmailUID' => $EmailUID, 'OrderUID' => $OrderUID));
    $query = $this->db->get();
    if($query->num_rows() > 0){
      return $query->row();
    }else{
      return false;
    }
  }

}
?>
