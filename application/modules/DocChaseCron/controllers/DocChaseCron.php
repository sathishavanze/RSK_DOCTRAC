<?php defined('BASEPATH') or exit('No direct script access allowed');
class DocChaseCron extends MY_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('DocChaseCron_model');
  }

  public function index(){
    /* Get all the order which are in WelcomeCall workflow*/
    $WelcomeDetails = $this->DocChaseCron_model->getWelcomeDetails();
    $WorkflowModuleUID =$this->config->item('Workflows')['WelcomeCall'];  
    $inserted_records = 0;
    $message = [];
    foreach ($WelcomeDetails as $w) {
      $ProblemIdentified = 0;
      $formdata = array();

      /* Checking WelcomeCall Workflow Complete or Not*/
      $DocList = $this->DocChaseCron_model->getDocWaitingList($w->OrderUID);

      /* Check Problem Identify in Welcome Call */	
      $CheckListAnswers = $this->DocChaseCron_model->getCheckListAnswers($w->OrderUID,$WorkflowModuleUID);

      /* Check - PreScreen Workflow Complete & Problem Identified */
      if (!empty($DocList) && $DocList !='-' && !empty($CheckListAnswers)) {
        $datetime2 = $DocList->CompleteDateTime;/* start time */
        $datetime1 = $w->AssignedDatetime;/* end time */

        /* Check datetime exceeds 48 hours*/
        $hours_difference = $this->Common_Model->differenceInHours($datetime2,$datetime1);
        if($hours_difference > 48){          
          $ProblemIdentified = $hours_difference; /*Difference in Hours*/
          $OrderUID = $w->OrderUID;
        }
      }else{
        $ProblemIdentified = 0;
        $OrderUID = 0;
      }

      $OrderUID = $w->OrderUID;
      $Reason = 3;
      $Remarks = 'System generated';
      $this->Email = $this->session->userdata('Email');
      $UserUID = $this->session->userdata('UserUID');
      $formdata=array('OrderUID'=>$OrderUID,
        'WorkflowModuleUID'=>$WorkflowModuleUID,
        'ReasonUID'=>$Reason,
        'Remarks'=>$Remarks,
        'RaisedByUserUID'=>$UserUID,
        'RaisedDateTime'=>date('Y-m-d H:i:s'));
      if ($ProblemIdentified != 0) {
        $checkDuplicateDocChase = $this->DocChaseCron_model->getDocChaseOrders($OrderUID);
        if (empty($checkDuplicateDocChase)) {
          $this->RaiseDocChase($formdata);
          $WorkflowArrays =$this->config->item('Workflows');
          $Module = array_search($WorkflowModuleUID, $WorkflowArrays);
          $data = array('OrderUID'=>$OrderUID,
            'Description'=>'System generated',
            'WorkflowUID'=>$WorkflowModuleUID,
            'Module'=>$Module,
            'CreatedByUserUID'=>$this->loggedid,
            'CreateDateTime'=>date("Y/m/d H:i:s"));
          $this->DocChaseCron_model->insertNotes($data);
        }
        $message[] = $OrderUID;
        $inserted_records = 1;
      }
    }

    if($inserted_records == 1){
      $implode = implode(', ', $message);
      //print_r("Doc Chase orders inserted successfully ".$implode);
    } else {
      //print_r("No Records Found");
    }
  }

  function RaiseDocChase($data){
    $Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$data['WorkflowModuleUID']]);
    $WorkflowModuleUID = $data['WorkflowModuleUID'];
    $OrderUID = $data['OrderUID'];

    $this->db->trans_begin();
    $is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID]);

    if (empty($is_assignment_row_available)) {				
      $res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Assigned'];
      $res['OrderUID'] = $data['OrderUID'];
      $res['WorkflowModuleUID'] = $WorkflowModuleUID;
      $res['AssignedToUserUID'] = $this->loggedid;
      $res['AssignedDatetime'] = date('Y-m-d H:i:s');
      $res['AssignedByUserUID'] = $this->loggedid;
      $this->db->insert('tOrderAssignments', $res);
    }

    $insert = $this->Common_Model->save('tOrderDocChase', $data);
    $this->DocChaseCron_model->UpdateCheckListAnswers($OrderUID,$WorkflowModuleUID);

    if ($this->db->trans_status()===false) {
      $this->db->trans_rollback();
      $Msg = $this->lang->line('DocChase_Raise_Failed');
      $this->output->set_content_type('application/json')
      ->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
      exit;
    }
    else{
      /*INSERT ORDER LOGS BEGIN*/
      $this->Common_Model->OrderLogsHistory($data['OrderUID'],$Workflow->WorkflowModuleName.' - Doc Chase Raised',Date('Y-m-d H:i:s'));
      /*INSERT ORDER LOGS END*/
      $this->db->trans_commit();
    }
  }
}?>
