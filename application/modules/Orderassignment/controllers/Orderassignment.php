<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderAssignment extends MY_Controller {
  
  
  function __construct()
  {
    parent::__construct();
    $this->load->model('Orderassignmentmodel');
    ini_set('display_errors', '0');
    
    
  } 
  
  public function index()
  {
    $data['content'] = 'index';
    $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
  }
  
  
  
  public function loadassignmentsummary()
  {
    $data['content'] = 'assignmentsummary';
    $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
  }
  
  public function loadorderassign()
  {
    $data['content'] = 'orderassign';
    $data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
    $data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID'=>'ASC'], ['CustomerUID']);
    
    $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
  }
  
  public function loadorderreassign()
  {
    $data['content'] = 'orderreassign';
    $data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
    
    $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
  }
  
  
  function assignment_ajax_list()
  {
    
    //get_post_input_data
    $post['length'] = $this->input->post('length');
    $post['start'] = $this->input->post('start');
    $search = $this->input->post('search');
    $post['search_value'] = $search['value'];
    $post['order'] = $this->input->post('order');
    $post['draw'] = $this->input->post('draw');
    $post['ProjectUID'] = $this->input->post('ProjectUID');
    $post['OrderUID'] = $this->input->post('OrderUID');
    if ($post['ProjectUID'] == 'all' && $post['OrderUID'] != '') {
      $tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$post['OrderUID']]);
      $post['ProjectUID'] = $tOrders->ProjectUID;
    }
    //get_post_input_data
    //column order
    $post['column_order'] = array('tOrders.OrderNumber','');
    $post['column_search'] = array('tOrders.OrderNumber');
    //column order
    $list = $this->Orderassignmentmodel->AssignmentOrders($post);
    
    $no = $post['start'];
    $assigmentorders = [];
    
    foreach ($list as $key=>$order)
    {
      $checked = '';
      $row = array();
      $row[] = '<a href="'.base_url('Ordersummary/index/'.$order->OrderUID).'" class="ajaxload">
      '.$order->OrderNumber.'</a>';
      $row[] = $order->CustomerName;
      
      $row[] = $order->ProjectName;
      $row[] = '<a href="javascript:void(0)" style=" background: '.$order->StatusColor.' !important;" class="btn  btn-round mt-10">'.$order->StatusName.'</a>';
      $row[] = $order->PropertyAddress1.' '.$order->PropertyAddress2;
      $row[] = $order->PropertyCityName;
      $row[] = $order->PropertyCountyName;
      $row[] = $order->PropertyStateCode;
      $row[] = $order->PropertyZipCode;
      $row[] = $order->OrderEntryDateTime;

      $AssignedUsers  = $this->Orderassignmentmodel->GetAssignedUsers($order->OrderUID);
      
      $row[] = $AssignedUsers->StackingAssigedToUserName;
      $row[] = $AssignedUsers->ReviewAssignedToUserName;

      if ($post['OrderUID'] == $order->OrderUID) {
        $checked = 'checked';
      }
      $Action ='<td><div class="form-check"> <label class="form-check-label" for="check'.$key.'"> <input class="form-check-input input_assigncheckbox" value="" id="check'.$key.'" name="input_assigncheckbox" type="checkbox" data-OrderUID="'.$order->OrderUID.'" '.$checked.'> <span class="form-check-sign"> <span class="check"></span> </span> </label> </div>
      </td></tr>';
      
      $row[] = $Action;
      $assigmentorders[] = $row;
    }
    
    
    $data =  array(
      'assigmentorders' => $assigmentorders,
      'post' => $post
    );
    
    
    
    $post = $data['post'];
    $output = array(
      "draw" => $post['draw'],
      "recordsTotal" => $this->Orderassignmentmodel->count_all(),
      "recordsFiltered" =>  $this->Orderassignmentmodel->count_filtered($post),
      "data" => $data['assigmentorders'],
    );
    
    unset($post);
    unset($data);
    
    echo json_encode($output);
  }
  
  public function GetProjectUsers()
  {
    $OrderUID = $this->input->post('OrderUID');
    
    $tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
    $tOrderAssignment = $this->Common_Model->get_row('tOrderAssignment', ['OrderUID'=> $OrderUID]);
    $mProjectUser = $this->Orderassignmentmodel->GetProjectUsers($tOrders->ProjectUID);
    
    $ProductionUserUID = $tOrderAssignment->AssignedToUserUID;
    $QcUserUID = $tOrderAssignment->QcAssignedToUserUID;
    
    $Productionhtml = '';
    foreach ($mProjectUser as $key => $value) {
      if ($ProductionUserUID == $value->UserUID) {
        $Productionhtml .= '<option value="'.$value->UserUID.'" selected>'.$value->UserName.'</option>';
      }
      else{
        $Productionhtml .= '<option value="'.$value->UserUID.'">'.$value->UserName.'</option>';
      }
    }
    
    $Reviewhtml = '';
    foreach ($mProjectUser as $key => $value) {
      if ($QcUserUID == $value->UserUID) {
        $Reviewhtml .= '<option value="'.$value->UserUID.'" selected>'.$value->UserName.'</option>';
      }
      else{
        $Reviewhtml .= '<option value="'.$value->UserUID.'">'.$value->UserName.'</option>';
      }
    }
    $response['validation_error']=0;
    $response['Productionhtml'] = $Productionhtml;
    $response['Reviewhtml'] = $Reviewhtml;
    $this->output->set_content_type('applicaton/json')
    ->set_output(json_encode($response));
    
    
  }
  
  public function assignorder()
  {
    $OrderUID = $this->input->post('OrderUID');
    $Production = $this->input->post('Stacking');
    $Qc = $this->input->post('Review');
    
    $insert_tOrderAssignment=[];
    $update_tOrderAssignment=[];
    $OrderNumbers = [];
    $response=[];
    
    foreach ($OrderUID as $key => $value) {
      $row_data=[];
      $count = count($OrderNumbers);
      $tOrderAssignment_row = $this->Common_Model->get_row('tOrderAssignment', ['OrderUID'=>$value]);
      $tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$value]);
      if ((empty($tOrderAssignment_row) || $tOrderAssignment_row->AssignedToUserUID=='') && $Production!='') {
        $row_data['OrderUID'] = $value;
        $row_data['AssignedToUserUID'] = $Production;
        $row_data['AssignedDateTime'] = date('Y-m-d H:i:s');
        $row_data['AssignedByUserUID'] = $this->loggedid;
        $row_data['ProjectUID'] = $tOrders->ProjectUID;
        $row_data['WorkflowStatus'] = 0;
      }
      
      if ((empty($tOrderAssignment_row) || $tOrderAssignment_row->QcAssignedToUserUID=='') && $Qc!='')
      {
        $row_data['OrderUID'] = $value;
        $row_data['QcAssignedToUserUID'] = $Qc;
        $row_data['QcAssignedDateTime'] = date('Y-m-d H:i:s');
        $row_data['QcAssignedByUserUID'] = $this->loggedid;
        $row_data['ProjectUID'] = $tOrders->ProjectUID;
      }
      
      if (empty($tOrderAssignment_row) && !empty($row_data)) {
        $insert_tOrderAssignment[] = $row_data;
        if ($count == count($OrderNumbers)) {
          $OrderNumbers[] = $tOrders->OrderNumber;          
        }
      }
      else if (($tOrderAssignment_row->AssignedToUserUID == '' || $tOrderAssignment_row->QcAssignedToUserUID == '') && !empty($row_data)) {
        $row_data['OrderAssignmentUID'] = $tOrderAssignment_row->OrderAssignmentUID;
        $update_tOrderAssignment[] = $row_data;
        if ($count == count($OrderNumbers)) {
          $OrderNumbers[] = $tOrders->OrderNumber;          
        }
        
      }
      
    }
    
    $this->db->trans_begin();
    if (!empty($insert_tOrderAssignment)) {
      $this->db->insert_batch('tOrderAssignment', $insert_tOrderAssignment);
    }
    
    if (!empty($update_tOrderAssignment)) {
      $this->db->update_batch('tOrderAssignment', $update_tOrderAssignment , 'OrderAssignmentUID'); 
    }
    
    if ($this->db->trans_status() === false) {
      $this->db->trans_rollback();
      $Msg = $this->lang->line('Assign_Failed');
      $response['validation_error'] = 1;
      $response['message'] = $Msg;
    }
    else{
      $this->db->trans_commit();      
      $Msg = $this->lang->line('Assign');
      $SuccessMsg='';
      foreach ($OrderNumbers as $key => $value) {
        $SuccessMsg .= str_replace('<<Order Number>>', $value, $Msg) . '<br>';
      }
      $response['validation_error'] = 0;
      $response['message'] = $SuccessMsg;
      
    }
    $this->output->set_content_type('applicaton/json')
    ->set_output(json_encode($response));
    
  }
  
  
  public function unassignorder()
  {
    $OrderUID = $this->input->post('OrderUID');
    $Production = $this->input->post('Stacking');
    $Qc = $this->input->post('Review');
    
    $insert_tOrderAssignment=[];
    $update_tOrderAssignment=[];
    $OrderNumbers = [];
    $response=[];
    
    foreach ($OrderUID as $key => $value) {
      $row_data=[];
      $count = count($OrderNumbers);
      $tOrderAssignment_row = $this->Common_Model->get_row('tOrderAssignment', ['OrderUID'=>$value]);
      $tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$value]);
      if ((empty($tOrderAssignment_row) || $tOrderAssignment_row->AssignedToUserUID=='') && $Production!='') {
        $row_data['OrderUID'] = $value;
        $row_data['AssignedToUserUID'] = $Production;
        $row_data['AssignedDateTime'] = date('Y-m-d H:i:s');
        $row_data['AssignedByUserUID'] = $this->loggedid;
        $row_data['ProjectUID'] = $tOrders->ProjectUID;
        $row_data['WorkflowStatus'] = 0;
      }
      
      if ((empty($tOrderAssignment_row) || $tOrderAssignment_row->QcAssignedToUserUID=='') && $Qc!='')
      {
        $row_data['OrderUID'] = $value;
        $row_data['QcAssignedToUserUID'] = $Qc;
        $row_data['QcAssignedDateTime'] = date('Y-m-d H:i:s');
        $row_data['QcAssignedByUserUID'] = $this->loggedid;
        $row_data['ProjectUID'] = $tOrders->ProjectUID;
      }
      
      if (empty($tOrderAssignment_row) && !empty($row_data)) {
        $insert_tOrderAssignment[] = $row_data;
        if ($count == count($OrderNumbers)) {
          $OrderNumbers[] = $tOrders->OrderNumber;          
        }
      }
      else if (($tOrderAssignment_row->AssignedToUserUID == '' || $tOrderAssignment_row->QcAssignedToUserUID == '') && !empty($row_data)) {
        $row_data['OrderAssignmentUID'] = $tOrderAssignment_row->OrderAssignmentUID;
        $update_tOrderAssignment[] = $row_data;
        if ($count == count($OrderNumbers)) {
          $OrderNumbers[] = $tOrders->OrderNumber;          
        }
        
      }
      
    }
    
    $this->db->trans_begin();
    if (!empty($insert_tOrderAssignment)) {
      $this->db->insert_batch('tOrderAssignment', $insert_tOrderAssignment);
    }
    
    if (!empty($update_tOrderAssignment)) {
      $this->db->update_batch('tOrderAssignment', $update_tOrderAssignment , 'OrderAssignmentUID'); 
    }
    
    if ($this->db->trans_status() === false) {
      $this->db->trans_rollback();
      $Msg = $this->lang->line('Assign_Failed');
      $response['validation_error'] = 1;
      $response['message'] = $Msg;
    }
    else{
      $this->db->trans_commit();      
      $Msg = $this->lang->line('Assign');
      $SuccessMsg='';
      foreach ($OrderNumbers as $key => $value) {
        $SuccessMsg .= str_replace('<<Order Number>>', $value, $Msg) . '<br>';
      }
      $response['validation_error'] = 0;
      $response['message'] = $SuccessMsg;
      
    }
    $this->output->set_content_type('applicaton/json')
    ->set_output(json_encode($response));
    
  }
  
} ?>

