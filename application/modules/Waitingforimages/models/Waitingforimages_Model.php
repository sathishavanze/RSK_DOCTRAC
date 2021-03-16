<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Waitingforimages_Model extends MY_Model {
  
  function __construct()
  { 
    parent::__construct();
   
  }


  // Waiting for Images
    function count_all()
    {


      $status[0] = $this->config->item('keywords')['Waiting For Images'];

      $this->db->select("*,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID");
      $this->db->from('tOrderAssignment');
      $this->db->join('tOrders','tOrders.OrderUID = tOrderAssignment.OrderUID','left');
      $this->db->join('mUsers', 'tOrderAssignment.AssignedToUserUID = mUsers.UserUID' , 'left' );
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      $this->db->where_in('tOrders.StatusUID', $status);
      if($this->RoleUID == 8)
      {
        $this->db->join('mCustomerUser','tOrders.CustomerUID = mCustomerUser.CustomerUID','left');
        $this->db->where(array('mCustomerUser.UserUID'=>$this->loggedid));
      }
      if(in_array($this->RoleUID,$this->config->item('Agent')))
      {
        $this->db->where(array('tOrderAssignment.AssignedToUserUID'=>$this->loggedid));
      }
        $query = $this->db->count_all_results();
        return $query;
    }


    function count_filtered($post)
    {
     
      $status[0] = $this->config->item('keywords')['Waiting For Images'];





      $this->db->select("*,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID");
      $this->db->from('tOrderAssignment');
      $this->db->join('tOrders','tOrders.OrderUID = tOrderAssignment.OrderUID','left');
      $this->db->join('mUsers', 'tOrderAssignment.AssignedToUserUID = mUsers.UserUID' , 'left' );
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      $this->db->where_in('tOrders.StatusUID', $status);
      if($this->RoleUID == 8)
      {
        $this->db->join('mCustomerUser','tOrders.CustomerUID = mCustomerUser.CustomerUID','left');
        $this->db->where(array('mCustomerUser.UserUID'=>$this->loggedid));
      }
      if(in_array($this->RoleUID,$this->config->item('Agent')))
      {
        $this->db->where(array('tOrderAssignment.AssignedToUserUID'=>$this->loggedid));
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
        $query = $this->db->get();
        return $query->num_rows();
    }



  function MyOrders($post)
  {
    

      $status[0] = $this->config->item('keywords')['Waiting For Images'];

      $this->db->select("*,mStatus.StatusName,mStatus.StatusColor,mCustomer.CustomerName,mProjectCustomer.ProjectUID");
      $this->db->from('tOrderAssignment');
      $this->db->join('tOrders','tOrders.OrderUID = tOrderAssignment.OrderUID','left');
      $this->db->join('mUsers', 'tOrderAssignment.AssignedToUserUID = mUsers.UserUID' , 'left' );
      $this->db->join('mStatus','tOrders.StatusUID = mStatus.StatusUID','left');
      $this->db->join('mCustomer','tOrders.CustomerUID = mCustomer.CustomerUID','left');
      $this->db->join('mProjectCustomer','tOrders.ProjectUID = mProjectCustomer.ProjectUID','left');
      $this->db->where_in('tOrders.StatusUID', $status);
      if($this->RoleUID == 8)
      {
        $this->db->join('mCustomerUser','tOrders.CustomerUID = mCustomerUser.CustomerUID','left');
        $this->db->where(array('mCustomerUser.UserUID'=>$this->loggedid));
      }
      if(in_array($this->RoleUID,$this->config->item('Agent')))
      {
        $this->db->where(array('tOrderAssignment.AssignedToUserUID'=>$this->loggedid));
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


      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
      $query = $this->db->get();
      return $query->result();  
  }
  // Waiting for Images








  




}?>