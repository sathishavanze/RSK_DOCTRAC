<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class DocumentTracking_Model extends MY_Model {
  
  function __construct()
  { 
    parent::__construct();
   
  }


  // DocumentTracking
    function count_all($post)
    {


      $this->db->select("1");


      /*^^^^^ Get DocumentTracking Query ^^^^^*/
      $this->db->from('tDocumentTracking');
      $this->db->join('tOrders', 'tOrders.OrderUID = tDocumentTracking.OrderUID', 'LEFT');
      $this->db->join('mInputDocType', 'mInputDocType.InputDocTypeUID = tDocumentTracking.InputDocTypeUID', 'LEFT');
      $this->db->join('mCustomer', 'mCustomer.CustomerUID=tDocumentTracking.CustomerUID', 'LEFT');
      $this->db->join('mLender', 'mLender.LenderUID=tDocumentTracking.LenderUID', 'LEFT');
      $this->db->join('mUsers', 'mUsers.UserUID=tDocumentTracking.UploadedByUserUID', 'LEFT');

      $this->db->where('tDocumentTracking.DocumentStatus', $post['DocumentStatus']);


      
      $query = $this->db->count_all_results();
      return $query;
    }


    function count_filtered($post)
    {

      $this->db->select("1");

      /*^^^^^ Get DocumentTracking Query ^^^^^*/
      $this->db->from('tDocumentTracking');
      $this->db->join('tOrders', 'tOrders.OrderUID = tDocumentTracking.OrderUID', 'LEFT');
      $this->db->join('mInputDocType', 'mInputDocType.InputDocTypeUID = tDocumentTracking.InputDocTypeUID', 'LEFT');
      $this->db->join('mCustomer', 'mCustomer.CustomerUID=tDocumentTracking.CustomerUID', 'LEFT');
      $this->db->join('mLender', 'mLender.LenderUID=tDocumentTracking.LenderUID', 'LEFT');
      $this->db->join('mUsers', 'mUsers.UserUID=tDocumentTracking.UploadedByUserUID', 'LEFT');

      $this->db->where('tDocumentTracking.DocumentStatus', $post['DocumentStatus']);


      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->DocumentTracking_Model->advanced_search($post);
      }
      // Advanced Search

      // Datatable Search
      $this->DocumentTracking_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->DocumentTracking_Model->WorkflowQueues_Datatable_OrderBy($post);

      $query = $this->db->get();
      return $query->num_rows();
    }



  function TrackingOrders($post)
  {

    
      $this->db->select("*");
      $this->db->select("DATE_FORMAT(tDocumentTracking.UploadedDateTime, '%m-%d-%Y %H:%i:%s') as UploadedDateTime", FALSE);

      $this->db->from('tDocumentTracking');
      $this->db->join('tOrders', 'tOrders.OrderUID = tDocumentTracking.OrderUID', 'LEFT');
      $this->db->join('mInputDocType', 'mInputDocType.InputDocTypeUID = tDocumentTracking.InputDocTypeUID', 'LEFT');
      $this->db->join('mCustomer', 'mCustomer.CustomerUID=tDocumentTracking.CustomerUID', 'LEFT');
      $this->db->join('mLender', 'mLender.LenderUID=tDocumentTracking.LenderUID', 'LEFT');
      $this->db->join('mUsers', 'mUsers.UserUID=tDocumentTracking.UploadedByUserUID', 'LEFT');

      $this->db->where('tDocumentTracking.DocumentStatus', $post['DocumentStatus']);

      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->DocumentTracking_Model->advanced_search($post);
      }
      // Advanced Search


      // Datatable Search
      $this->DocumentTracking_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->DocumentTracking_Model->WorkflowQueues_Datatable_OrderBy($post);


      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
      $query = $this->db->get();
      return $query->result();  
  }



  function OrdersList($post)
  {

    $status[] = $this->config->item('keywords')['Completed'];
    $status[] = $this->config->item('keywords')['Cancelled'];
    $this->db->select("*,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
    $this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);

      /*^^^^^ Get Completed Orders Query ^^^^^*/
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      $this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
      $this->db->where_not_in('tOrders.StatusUID', $status);

      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/

      $this->db->group_by('tOrders.OrderUID');

      // Advanced Search 
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }

      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);


      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
      $this->db->order_by('OrderEntryDatetime');
      $query = $this->db->get();
      return $query->result();
  }


  function OrdersList_countall()
  {

    $status[] = $this->config->item('keywords')['Completed'];
    $status[] = $this->config->item('keywords')['Cancelled'];
    $this->db->select("1");

      /*^^^^^ Get Completed Orders Query ^^^^^*/
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      $this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
      $this->db->where_not_in('tOrders.StatusUID', $status);

      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/
      $query = $this->db->count_all_results();

      return $query;
  }


  function OrdersList_count_filtered($post)
  {

      $status[] = $this->config->item('keywords')['Completed'];
      $status[] = $this->config->item('keywords')['Cancelled'];
      $this->db->select("1");

      /*^^^^^ Get Completed Orders Query ^^^^^*/
      $this->db->from('tOrders');
      $this->db->join('tOrderDocumentCheckIn','tOrderDocumentCheckIn.OrderUID = tOrders.OrderUID','left');
      $this->db->join('tOrderPropertyRole','tOrderPropertyRole.OrderUID = tOrders.OrderUID','left');
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      $this->db->join('mLender','tOrders.LenderUID = mLender.LenderUID','left');
      $this->db->join('mProducts','tOrders.ProductUID=mProducts.ProductUID','left');
      $this->db->where_not_in('tOrders.StatusUID', $status);

      /*^^^^^ ROLE BASED FILTER STARTS^^^^^*/
      $this->Common_Model->FilterOrderBasedOnRole();
      /*^^^^^ ROLE BASED FILTER ENDS^^^^^*/


      // Advanced Search
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
        $filter = $this->Common_Model->advanced_search($post);
      }
      // Advanced Search

      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      // Datatable OrderBy
      $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);

      $query = $this->db->get();
      return $query->num_rows();
  }


  function advanced_search($post)
  {
    if($post['advancedsearch']['UploadedByUserUID'] != '' && $post['advancedsearch']['UploadedByUserUID'] != 'All'){
      $this->db->where('tDocumentTracking.UploadedByUserUID',$post['advancedsearch']['UploadedByUserUID']);
    }
    if($post['advancedsearch']['CustomerUID'] != '' && $post['advancedsearch']['CustomerUID'] != 'All'){
      $this->db->where('tDocumentTracking.CustomerUID',$post['advancedsearch']['CustomerUID']);
    }
    if($post['advancedsearch']['LenderUID'] != '' && $post['advancedsearch']['LenderUID'] != 'All'){
      $this->db->where('tDocumentTracking.LenderUID',$post['advancedsearch']['LenderUID']);
    }

    if($post['advancedsearch']['FromDate']){
      $this->db->where('DATE(tDocumentTracking.UploadedDateTime) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
    }
    if($post['advancedsearch']['ToDate']){
      $this->db->where('DATE(tDocumentTracking.UploadedDateTime) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
    }
    return true;
  }


  function WorkflowQueues_Datatable_Search($post)
  {

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
  }

  function WorkflowQueues_Datatable_OrderBy($post)
  {

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
          $this->db->order_by('tDocumentTracking.DocumentTrackingUID', 'ASC');
        }

  }


    function GetDocumentTrackingExcelRecords($post)
    {

      $this->db->select("*,tOrders.OrderUID,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID,mProducts.ProductName");
      $this->db->select("DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);


      /*^^^^^ Get DocumentTracking Query ^^^^^*/
      $this->Common_Model->GetDocumentTrackingQueue();


      if($post['advancedsearch']['Status'] == 'workinprogress'){

       $this->db->where('tOrderAssignments.AssignedToUserUID IS NOT NULL');

     }
     else if (!in_array($this->RoleType, $this->config->item('CustomerAccess'))) {
      
      $this->db->group_start();
      $this->db->where(array('tOrderAssignments.AssignedToUserUID'=>$this->loggedid));
      $this->db->or_where('tOrderAssignments.AssignedToUserUID IS NULL');
      $this->db->group_end();
    }
    

      if ($post['advancedsearch']!='false' && sizeof(array_filter($post['advancedsearch']))!=0) 
      {
          $filter = $this->Common_Model->advanced_search($post); 
      }

      $this->db->order_by('OrderEntryDatetime');
      $query = $this->db->get();
      return $query->result();  
    }
}?>