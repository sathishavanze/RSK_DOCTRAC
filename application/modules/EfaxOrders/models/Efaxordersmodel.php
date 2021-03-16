<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Efaxordersmodel extends MY_Model {
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
    $this->db->where('DATE(tEFaxData.ModifiedDate) <= "'.date('Y-m-d', strtotime($ToDate)).'"', NULL, false);
  }

  function DateFilterFromDate($post)
  {
    $FromDate = $post['advancedsearch']['FromDate'];
    $this->db->where('DATE(tEFaxData.ModifiedDate) >= "'.date('Y-m-d', strtotime($FromDate)).'"', NULL, false);
  }

  function advanced_search($post)
  {
    if(isset($post['advancedsearch']['OrderNumber']) && $post['advancedsearch']['OrderNumber'] != '' && $post['advancedsearch']['OrderNumber'] != 'All'){
      $this->db->like('tEFaxData.TransactionID',$post['advancedsearch']['OrderNumber']);
    }

    if(isset($post['advancedsearch']['FaxID']) && $post['advancedsearch']['FaxID'] != '' && $post['advancedsearch']['FaxID'] != 'All'){
      $this->db->like('tEFaxData.FaxID',$post['advancedsearch']['FaxID']);
    }

    if(isset($post['advancedsearch']['FromFaxNumber']) && $post['advancedsearch']['FromFaxNumber'] != '' && $post['advancedsearch']['FromFaxNumber'] != 'All'){
      $this->db->like('tEFaxData.FromFaxNumber',$post['advancedsearch']['FromFaxNumber']);
    }

    if(isset($post['advancedsearch']['ToFaxNumber']) && $post['advancedsearch']['ToFaxNumber'] != '' && $post['advancedsearch']['ToFaxNumber'] != 'All'){
      $this->db->like('tEFaxData.ToFaxNumber', $post['advancedsearch']['ToFaxNumber']);
    }

    if(isset($post['advancedsearch']['TransmissionStatus']) && $post['advancedsearch']['TransmissionStatus'] != '' && $post['advancedsearch']['TransmissionStatus'] != 'All'){
      $this->db->like('tEFaxData.TransmissionStatus',$post['advancedsearch']['TransmissionStatus']);
    }

    if(isset($post['advancedsearch']['FaxStatus']) && $post['advancedsearch']['FaxStatus'] != '' && $post['advancedsearch']['FaxStatus'] != 'All'){
      $this->db->like('tEFaxData.FaxStatus',$post['advancedsearch']['FaxStatus']);
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
    $this->GetEFaxOrders();
    $query = $this->db->count_all_results();
    return $query;
  }

  function count_all()
  {
    $this->db->select("1");
    $this->GetEFaxOrders();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);     
    $query = $this->db->count_all_results();
    return $query;
  }

  function count_filtered($post)
  {
    $this->db->select("1");
    $this->GetEFaxOrders();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->advanced_search($post);
    }
    $this->WorkflowQueues_Datatable_Search($post);
    $this->WorkflowQueues_Datatable_OrderBy($post);
    $query = $this->db->get();
    return $query->num_rows();
  }

  function Func_EFaxOrders($post,$global=''){
    $this->db->select("tEFaxData.*");
		$this->db->select("tOrders.*,  mProjectCustomer.ProjectName, tOrders.OrderUID,tOrders.LoanNumber,mMilestone.MilestoneName,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
    $this->db->select('tOrders.LastModifiedDateTime');
    $this->GetEFaxOrders();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->advanced_search($post);
    }
    $this->WorkflowQueues_Datatable_Search($post);
    $this->WorkflowQueues_Datatable_OrderBy($post);
    if ($post['length']!='') {
       $this->db->limit($post['length'], $post['start']);
    }
    $this->db->order_by('OrderEntryDatetime');
    $output = $this->db->get();
    return $output->result();
	}

  function GetEfaxOrdersExcelRecords($post)
  {
    $this->db->select("tEFaxData.*");
    $this->db->select("tOrders.*, mStatus.StatusName, mStatus.StatusColor, mCustomer.CustomerName, mMilestone.MilestoneName, mProjectCustomer.ProjectUID, mProducts.ProductName");
    $this->db->select("tOrders.LoanNumber,DATE_FORMAT(tOrders.OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDateTime", FALSE);
    $this->GetEFaxOrders();
    $this->FilterByProjectUser($this->RoleUID,$this->loggedid);
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->advanced_search($post);
    }
    $this->db->order_by('tOrders.OrderNumber');
    $query = $this->db->get();
    return $query->result();  
  }

  function GetEFaxOrders()
  {    
    $this->db->from('tEFaxData');
    $this->db->join('tOrders','tOrders.OrderUID = tEFaxData.OrderUID');
    $this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','left');
    $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
    $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
    $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
    $this->db->join('mMilestone','tOrders.MilestoneUID = mMilestone.MilestoneUID','left');
    $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
    $this->db->group_by('tEFaxData.EFaxDataUID');
  }

  function GetEFaxOrdersByFaxID($EFaxDataUID)
  {  
    $this->db->select("*");  
    $this->db->from('tEFaxData');
    $this->db->where('tEFaxData.EFaxDataUID', $EFaxDataUID);
    $output = $this->db->get();
    return $output->row();
  }

  function GetEFaxCredentials()
  {  
    $this->db->select("*");  
    $this->db->from('mSettings');
    $output = $this->db->get();
    $mSettings = $output->result_array();
    $arr = [];
    foreach ($mSettings as $key => $value) {
      if($value['SettingField'] == 'EFaxToken'){
        $arr['EFaxToken'] = $value['SettingValue'];
      }
      if($value['SettingField'] == 'EFaxAuthKey'){
        $arr['EFaxAuthKey'] = $value['SettingValue'];
      }
      if($value['SettingField'] == 'EFaxURL'){
        $arr['EFaxURL'] = $value['SettingValue'];
      }
      if($value['SettingField'] == 'EFaxUserID'){
        $arr['EFaxUserID'] = $value['SettingValue'];
      }
    }
    return $arr;
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

  /**
  * Function to retrive the fax list from e-Fax Integration
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return Array
  * @since July 20th 2020
  * @version E-Fax Intergartion
  *
  */

  function Func_ReceiveEFaxOrders($post,$global=''){
    $this->db->select("tEFaxData.*");
    $this->GetReceiveEFaxOrders();
    if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->advanced_search($post);
    }
    $this->WorkflowQueues_Datatable_Search($post);
    $this->WorkflowQueues_Datatable_OrderBy($post);
    if ($post['length']!='') {
       $this->db->limit($post['length'], $post['start']);
    }
    $this->db->order_by('tEFaxData.CreatedDate');
    $output = $this->db->get();
    return $output->result();
  }

  /**
  * Function to select orders from Efaxtable
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return Array
  * @since July 20th 2020
  * @version E-Fax Intergartion
  *
  */

  function GetReceiveEFaxOrders()
  {    
    $this->db->from('tEFaxData');
    $this->db->where('tEFaxData.OrderUID', 0);
    $this->db->where('FaxType', 'RECEIVE');
  }

  /**
  * Function to get count from Efaxtable
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return Array
  * @since July 20th 2020
  * @version E-Fax Intergartion
  *
  */

  function receive_fax_count_all()
  {
    $this->db->select("1");
    $this->GetReceiveEFaxOrders();
    $query = $this->db->count_all_results();
    return $query;
  }

  /**
  * Function to get count for filtered orders from Efaxtable
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return Array
  * @since July 20th 2020
  * @version E-Fax Intergartion
  *
  */

  function receive_count_filtered($post)
  {
    $this->db->select("1");
    $this->GetReceiveEFaxOrders();
    if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
      $filter = $this->advanced_search($post);
    }
    $this->WorkflowQueues_Datatable_Search($post);
    $this->WorkflowQueues_Datatable_OrderBy($post);
    $query = $this->db->get();
    return $query->num_rows();
  }

  /**
  * Function to get Efax data
  *
  * @param FaxID (String)
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return Array
  * @since July 21th 2020
  * @version E-Fax Integration
  *
  */
  function GetEFaxDetailsByFaxID($FaxID)
  {
    $this->db->Select('*');
    $this->db->from('tEFaxData');
    $this->db->where('FaxType', 'RECEIVE');
    $this->db->where('IsFaxImageReceived', 1);
    $this->db->where('FaxID', $FaxID);
    return $this->db->get()->row();
  }

}
?>
