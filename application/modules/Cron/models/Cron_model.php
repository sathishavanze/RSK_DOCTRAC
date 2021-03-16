<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Cron_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

  function GetCompletedOrdersBefore($Days)
  {
    $status = [];
    $status = $this->config->item('keywords')['Completed'];
    $this->db->Select('*');
    $this->db->from('tOrders');
    $this->db->where_in('StatusUID', $status);
    $this->db->where('IsMovedToS3', 0);
    $this->db->limit(50);
    //$this->db->where_in('OrderNumber', array('S19004071'));
    //$this->db->where('OrderCompleteDateTime < DATE_FORMAT(CURDATE() - INTERVAL '.$Days.' DAY,"%Y-%m-%d %H:%i:%s")', NULL, false);
    return $this->db->get()->result();
  }

  function UpdateMovedStatus($OrderUID)
  {
    $this->db->where('OrderUID', $OrderUID);
    $this->db->update('tOrders', ['IsMovedToS3' => 1]);
    return true;
  }

  function GetExportOrders($ProjectUID)
  {

    if (empty($ProjectUID)) {
      return [];
    }
    $status[] = $this->config->item('keywords')['Export'];
    $status[] = $this->config->item('keywords')['Review Completed'];

    $this->db->select('*');
    $this->db->from('tOrders');
    $this->db->join('mProjectCustomer', 'tOrders.ProjectUID = mProjectCustomer.ProjectUID');
    $this->db->join('mCustomer', 'tOrders.CustomerUID = mCustomer.CustomerUID');
    $this->db->where_in('tOrders.StatusUID', $status);
    $this->db->where('mProjectCustomer.IsAutoExport', 1);
    $this->db->where('tOrders.IsAutoExport', 0);
    $this->db->where('tOrders.ProjectUID', $ProjectUID);
    $this->db->where('mProjectCustomer.IsExport=',1);
    $this->db->order_by('tOrders.OrderUID', 'ASC');
    $this->db->limit(10);
    return $this->db->get()->result();
  }

  function SFTP_Email($SFTPUID)
  {
    $this->db->select('*');
    $this->db->from('mSFTP');
    $this->db->join('mEmailTemplate', 'mSFTP.EmailTEmplateUID=mEmailTemplate.EmailTemplateUID');
    $this->db->where('mSFTP.SFTPUID', $SFTPUID);
    return $this->db->get()->row();
  }

  function get_parkingqueue_notifications()
  {
    $this->db->select('ParkingUID,mUsers.EmailID,Remainder,IsRemainderSend,OrderNumber');
    $this->db->from('tOrderParking');
    $this->db->join('mUsers', 'mUsers.UserUID=tOrderParking.RaisedByUserUID');
    $this->db->join('tOrders', 'tOrders.OrderUID=tOrderParking.OrderUID');
    $this->db->where('tOrderParking.IsCleared', 0);
    $this->db->where('tOrderParking.IsRemainderSend', 0);
    return $this->db->get()->result();
  }

  function update_remindedparking($ParkingUID)
  {
    $this->db->where('ParkingUID', $ParkingUID);
    $this->db->update('tOrderParking', ['IsRemainderSend' => 1]);
    return true;
  }

  function get_parkingorderstype_uncleared()
  {
  	$this->db->select('ParkingUID,tOrderParking.OrderUID,tOrderParking.WorkflowModuleUID,Remainder,WorkflowModuleName',false);
  	$this->db->from('tOrderParking');
  	$this->db->join('mWorkFlowModules','tOrderParking.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID');
    $this->db->join('tOrders','tOrders.OrderUID = tOrderParking.OrderUID');
    $this->db->join('mCustomerWorkflowModules', 'tOrders.CustomerUID = mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID AND tOrderParking.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
    $this->db->where('mCustomerWorkflowModules.IsParkingCron', 1);
    $this->db->where('(tOrderParking.ParkingTypeUID IS NOT NULL OR tOrderParking.ParkingTypeUID <> "") ', null, false);
    $this->db->where('tOrderParking.IsCleared', 0);
    return $this->db->get()->result();
  }

  /**
	*Function CRON FUNCTION FOR PARKING ORDERS TO NORMAL Queue
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 30 March 2020
	*/
  function get_parkingorders_uncleared_crossedremainder()
  {
  	$this->db->select('ParkingUID,tOrderParking.OrderUID,tOrderParking.WorkflowModuleUID,Remainder,WorkflowModuleName',false);
  	$this->db->from('tOrderParking');
  	$this->db->join('mWorkFlowModules','tOrderParking.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID');
    $this->db->join('tOrders','tOrders.OrderUID = tOrderParking.OrderUID');
    $this->db->join('mCustomerWorkflowModules', 'tOrders.CustomerUID = mCustomerWorkflowModules.CustomerUID AND tOrders.ProductUID = mCustomerWorkflowModules.ProductUID AND tOrderParking.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID');
    $this->db->where('mCustomerWorkflowModules.IsParkingCron', 1);
  	$this->db->where('tOrderParking.IsCleared', 0);
  	$this->db->where('tOrderParking.Remainder < NOW()',NULL,FALSE);
  	return $this->db->get()->result();
  }

  /**
  *Function get autofollowup orders 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Saturday 27 June 2020 IST.
  */
  function get_autofollowup()
  {
    $this->db->select('tOrderQueues.OrderUID,tOrderQueues.QueueUID,mQueues.WorkflowModuleUID, QueueName,WorkflowModuleName,FollowupDuration,tOrderQueues.RaisedDateTime');
    $this->db->select('(SELECT ClearedDateTime FROM tOrderFollowUp WHERE tOrderFollowUp.OrderUID = tOrderQueues.OrderUID AND tOrderFollowUp.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID AND tOrderFollowUp.QueueUID = tOrderQueues.QueueUID AND tOrderFollowUp.IsCleared = 1 ORDER BY ClearedDateTime DESC LIMIT 1) AS LastFollowupDateTime',FALSE);
    $this->db->from('tOrderQueues');
    $this->db->join('mQueues','mQueues.QueueUID = tOrderQueues.QueueUID');
    $this->db->join('tOrderAssignments','tOrderAssignments.OrderUID = tOrderQueues.OrderUID AND mQueues.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID');
    $this->db->join('mWorkFlowModules','mQueues.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID');
    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderFollowUp WHERE tOrderFollowUp.OrderUID = tOrderQueues.OrderUID AND tOrderFollowUp.WorkflowModuleUID = tOrderAssignments.WorkflowModuleUID AND tOrderFollowUp.QueueUID = tOrderQueues.QueueUID AND IsCleared = 0)",NULL,FALSE);  
    $this->db->where(["mQueues.IsFollowup"=>1,"mQueues.FollowupType" => "Auto", "tOrderQueues.QueueStatus" => "Pending"]);
    $this->db->where('tOrderAssignments.WorkflowStatus <> 5',NULL,FALSE);
    return $this->db->get()->result();
  }

  /* Desc: D2TINT-172: Efax Integration-Get Documents to send in fax @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 2nd 2020 */
  function get_tDocuments($OrderUID)
  {
    $this->db->select('*');
    $this->db->from('tDocuments');
    $this->db->where('tDocuments.OrderUID', $OrderUID);
    /* Desc: Only take the documents that are in OCR Delaration @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 14th 2020 */
    $this->db->where('tDocuments.TypeofDocument', 'OCR_Declaration');
    return $this->db->get()->result();
  }

  /**
  * Function to get Efax data to retrive fax Image
  *
  * @param Nothing
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return Array
  * @since July 14th 2020
  * @version E-Fax Integration
  *
  */
  function GetEFaxDetails()
  {
    $this->db->Select('*');
    $this->db->from('tEFaxData');
    $this->db->where('FaxType', 'RECEIVE');
    $this->db->where('IsFaxImageReceived', 0);
    return $this->db->get()->result_array();
  }

  // Order Workflow Updates Cron
  function OrderWorkflowUpdatesCron() {

    // In URL Cron/OrderWorkflowUpdatesCron?CustomerUID=29
    $CustomerUID = $this->input->get('CustomerUID'); 
    $CustomOrderUID = $this->input->get('OrderUID'); 

    // Check Customer ID is passed or not
    if (!empty($CustomerUID)) {
      $status[0] = $this->config->item('keywords')['ClosedandBilled'];
      $status[1] = $this->config->item('keywords')['ClosingCompleted'];

      $this->db->select('tOrders.OrderUID, tOrders.OrderNumber, tOrders.CustomerUID, tOrders.MilestoneUID, tOrderImport.ClosingDisclosureSendDate, tOrderImport.SigningDate, tOrderImport.Queue');
      $this->db->from('tOrders');
      $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','left');
      $this->db->where('CustomerUID',$CustomerUID);
      $this->db->where_not_in('tOrders.StatusUID', $status);
      if (!empty($CustomOrderUID)) {
        $this->db->where('tOrders.OrderUID',$CustomOrderUID);
      }

      $this->db->where_in('tOrders.MilestoneUID', array(
        $this->config->item('Milestones')['2F'],
        $this->config->item('Milestones')['2G'],
        $this->config->item('Milestones')['3A'],
        $this->config->item('Milestones')['4A'],
        $this->config->item('Milestones')['5A']
      ));

      $tOrdersData = $this->db->get()->result();

      if (empty($tOrdersData)) {
        exit('No Orders Available');
      }

      // Declare variable
      $WorkflowCompleteDescriptionsArr = array();
      foreach ($tOrdersData as $key => $value) {

        $OrderUID = '';
        $CustomerUID = '';
        $MilestoneUID = '';
        $OrderUID = $value->OrderUID;
        $OrderNumber = $value->OrderNumber;
        $CustomerUID = $value->CustomerUID;
        $MilestoneUID = $value->MilestoneUID;
        $torderimport = array();
        $torderimport['ClosingDisclosureSendDate'] = $value->ClosingDisclosureSendDate;
        $torderimport['SigningDate'] = $value->SigningDate;
        $torderimport['Queue'] = $value->Queue;

        $WorkflowCompleteDescriptions = $this->Orderentrymodel->OrderWorkflowsUpdates($OrderUID, $CustomerUID, $MilestoneUID, $torderimport, TRUE);

        if (!empty($WorkflowCompleteDescriptions)) {
          /*INSERT ORDER LOGS BEGIN*/
          $this->Common_Model->OrderLogsHistory($OrderUID,'Order Update (Bulk)'.$WorkflowCompleteDescriptions,Date('Y-m-d H:i:s'));
          /*INSERT ORDER LOGS END*/
          $WorkflowCompleteDescriptionsArr[] = $WorkflowCompleteDescriptions;
          echo "<b><u>Order Number: ".$OrderNumber." </u></b>Following workflow was force completed".$WorkflowCompleteDescriptions."<br/>";
        }
      }

      if (empty($WorkflowCompleteDescriptionsArr)) {
        if (!empty($CustomOrderUID)) {
          echo "This Order Number: ".$OrderNumber." condition matched workflows already completed.";
        } else {
          echo "Required workflow was already completed for all orders";
        }
      }
    } else {
      echo "Please pass the CustomerUID in URL Like: Cron/OrderWorkflowUpdatesCron?CustomerUID=29&OrderUID=";
    }
  }

  // CD and Scheduling condition matched orders all workflows complete cron 
  function CDandSchedulingQueueUpdatesCron() {

    $status[0] = $this->config->item('keywords')['ClosedandBilled'];
    $status[1] = $this->config->item('keywords')['ClosingCompleted'];

    $this->db->select('tOrders.OrderUID, tOrders.CustomerUID, tOrders.MilestoneUID, tOrderImport.ClosingDisclosureSendDate, tOrderImport.SigningDate');
    $this->db->from('tOrders');
    $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','left');
    $this->db->where('tOrders.MilestoneUID', $this->config->item('Milestone')['2G']);
    $this->db->where('((tOrderImport.ClosingDisclosureSendDate IS NULL OR tOrderImport.ClosingDisclosureSendDate = "") OR (tOrderImport.ClosingDisclosureSendDate IS NOT NULL AND (tOrderImport.SigningDate IS NULL OR tOrderImport.SigningDate = "")))',NULL,FALSE);
    $this->db->where_not_in('tOrders.StatusUID', $status);
    $tOrdersData = $this->db->get()->result();

    if (empty($tOrdersData)) {
      exit('No Orders Available');
    }

    foreach ($tOrdersData as $key => $value) {
      $OrderUID = $value->OrderUID;
      $CustomerUID = $value->CustomerUID;
      $MilestoneUID = $value->MilestoneUID;
      $ClosingDisclosureSendDate = $value->ClosingDisclosureSendDate;
      $SigningDate = $value->SigningDate;

      // Check either CD and Scheduling workflow is enabled
      $OrderEnabledWorkflows = array();
      $OrderEnabledWorkflows[] = $this->config->item('Workflows')['CD'];
      $OrderEnabledWorkflows[] = $this->config->item('Workflows')['Scheduling'];

      // Check Scheduling condition matched.
      if (!empty($ClosingDisclosureSendDate) && empty($SigningDate)) {
        $OrderEnabledWorkflows = [];
        $OrderEnabledWorkflows[] = $this->config->item('Workflows')['Scheduling'];
      }

      $this->db->select('WorkflowModuleUID')->from('mCustomerWorkflowModules')->where(array('CustomerUID'=>$CustomerUID))->where_in('WorkflowModuleUID',$OrderEnabledWorkflows);

      // Check total records is greater than zero
      if($this->db->get()->num_rows()) {


        // Select need complete the workflow list
        $this->db->select('mCustomerWorkflowModules.WorkflowModuleUID, mWorkFlowModules.WorkflowModuleName');
        $this->db->from('mCustomerWorkflowModules');
        $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID','left');
        $this->db->where(array('mCustomerWorkflowModules.CustomerUID'=>$CustomerUID));
        $this->db->where_not_in('mCustomerWorkflowModules.WorkflowModuleUID',$OrderEnabledWorkflows);

        // Declare Log variable
        $OrderLogsDescriptions = '';

        $NeedtoCompleteWorkflowList = $this->db->get()->result_array();

        foreach ($NeedtoCompleteWorkflowList as $key => $value) {
          // Assign values
          $WorkflowModuleUID = '';
          $WorkflowModuleName = '';
          $WorkflowModuleUID = $value['WorkflowModuleUID'];
          $WorkflowModuleName = $value['WorkflowModuleName'];
          
          // Any issue checklist is there for this order (To complete that checklist)
          $this->db->where(array(
            'OrderUID' => $OrderUID,
            'WorkflowUID' => $WorkflowModuleUID,
            'Answer' => 'Problem Identified'
          ));

          $this->db->update('tDocumentCheckList', array('Answer' => 'Completed'));

          if($this->db->affected_rows()) {
            $OrderLogsDescriptions.= '<br/><b>'.$WorkflowModuleName.'</b> '.$this->db->affected_rows().' Problem Identified checklist is force completed.';
          }

          // Order in parking queue need to complete
          $tOrderParkingData = [];
          $tOrderParkingData['IsCleared'] = 1;
          $tOrderParkingData['ReasonUID'] = '';
          $tOrderParkingData['Remarks'] = ''; // Parking is force completed.
          $tOrderParkingData['ClearedByUserUID'] = $this->loggedid;
          $tOrderParkingData['ClearedDateTime'] = date('Y-m-d H:i:s');

          $this->db->where(array(
            'OrderUID'=>$OrderUID,
            'WorkflowModuleUID'=>$WorkflowModuleUID,
            'IsCleared'=>0
          ));
          $this->db->update('tOrderParking', $tOrderParkingData);

          if($this->db->affected_rows()) {
            $OrderLogsDescriptions.= '<br/><b>'.$WorkflowModuleName.'</b> parking queues is cleared.';
          }

          $Queues = '';
          $Queues = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID);

          foreach ($Queues as $key => $queue) {

            // Clear the exption if raised for this order
            $tOrderQueuesData = [];
            $tOrderQueuesData['OrderUID'] = $OrderUID;
            $tOrderQueuesData['QueueUID'] = $queue->QueueUID;
            $tOrderQueuesData['QueueStatus'] = "Completed";
            $tOrderQueuesData['CompletedReasonUID'] = '';
            $tOrderQueuesData['CompletedRemarks'] = ''; // Exception queue is force completed.
            $tOrderQueuesData['CompletedByUserUID'] = $this->loggedid;
            $tOrderQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

            $this->db->where(array(
              'OrderUID'=>$OrderUID,
              'QueueUID'=>$queue->QueueUID, 
              "QueueStatus" => "Pending"
            ));
            $this->db->update('tOrderQueues', $tOrderQueuesData);

            if($this->db->affected_rows()) {
              $OrderLogsDescriptions.= '<br/><b>'.$WorkflowModuleName.' - '.$queue->QueueName.'</b> sub queue was force completed.';
            }

            // Clear the followup if raised for this order
            $tOrderFollowUpData = [];
            $tOrderFollowUpData['IsCleared'] = 1;
            $tOrderFollowUpData['ClearedReasonUID'] = '';
            $tOrderFollowUpData['ClearedRemarks'] = ''; // FollowUp is cleared because workflow is force completed.
            $tOrderFollowUpData['ClearedByUserUID'] = $this->loggedid;
            $tOrderFollowUpData['ClearedDateTime'] = date('Y-m-d H:i:s');

            $this->db->where(array(
              'OrderUID'=>$OrderUID,
              'QueueUID'=>$queue->QueueUID,
              'WorkflowModuleUID'=>$WorkflowModuleUID,
              'IsCleared'=>0
            ));
            $this->db->update('tOrderFollowUp', $tOrderFollowUpData); 

            if($this->db->affected_rows()) {
              $OrderLogsDescriptions.= '<br/><b>'.$WorkflowModuleName.'</b> followup is cleared.';
            }
          } 

          // Clear the followup if raised for this order
          $tOrderFollowUpData = [];
          $tOrderFollowUpData['IsCleared'] = 1;
          $tOrderFollowUpData['ClearedReasonUID'] = '';
          $tOrderFollowUpData['ClearedRemarks'] = ''; // FollowUp is cleared because workflow is force completed.
          $tOrderFollowUpData['ClearedByUserUID'] = $this->loggedid;
          $tOrderFollowUpData['ClearedDateTime'] = date('Y-m-d H:i:s');

          $this->db->where(array(
            'OrderUID'=>$OrderUID,
            'WorkflowModuleUID'=>$WorkflowModuleUID,
            'IsCleared'=>0
          ));
          $this->db->update('tOrderFollowUp', $tOrderFollowUpData); 

          if($this->db->affected_rows()) {
            $OrderLogsDescriptions.= '<br/><b>'.$WorkflowModuleName.'</b> followup is cleared.';
          }         

          // Check is assignment row available
          $is_assignment_row_available = '';
          $is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID]);

          if (empty($is_assignment_row_available)) {  

            $tOrderAssignmentsData = [];
            $tOrderAssignmentsData['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
            $tOrderAssignmentsData['OrderUID'] = $OrderUID;
            $tOrderAssignmentsData['WorkflowModuleUID'] = $WorkflowModuleUID;
            $tOrderAssignmentsData['AssignedToUserUID'] = $this->loggedid;
            $tOrderAssignmentsData['AssignedDatetime'] = date('Y-m-d H:i:s');
            $tOrderAssignmentsData['AssignedByUserUID'] = $this->loggedid;
            $tOrderAssignmentsData['CompletedByUserUID'] = $this->loggedid;
            $tOrderAssignmentsData['CompleteDateTime'] = date('Y-m-d H:i:s');
            $this->db->insert('tOrderAssignments', $tOrderAssignmentsData);

            if($this->db->affected_rows()) {
              $OrderLogsDescriptions.= '<br/><b>'.$WorkflowModuleName.'</b> workflow was force completed.';
            }
          }
          elseif($is_assignment_row_available->WorkflowStatus != $this->config->item('WorkflowStatus')['Completed']) {

            $filter = [];
            $filter['OrderUID'] = $OrderUID;
            $filter['WorkflowModuleUID'] = $WorkflowModuleUID;
            
            $tOrderAssignmentsData = [];
            $tOrderAssignmentsData['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
            $tOrderAssignmentsData['CompletedByUserUID'] = $this->loggedid;
            $tOrderAssignmentsData['CompleteDateTime'] = date('Y-m-d H:i:s');
            $this->db->where($filter);
            $this->db->update('tOrderAssignments', $tOrderAssignmentsData);

            if($this->db->affected_rows()) {
              $OrderLogsDescriptions.= '<br/><b>'.$WorkflowModuleName.'</b> workflow was force completed.';
            }

          } 
        }

        /*INSERT ORDER LOGS BEGIN*/
        if(!empty($OrderLogsDescriptions)) {

          $this->Common_Model->OrderLogsHistory($OrderUID,'Order Update (Bulk)'.$OrderLogsDescriptions,Date('Y-m-d H:i:s'));
        }

        /*INSERT ORDER LOGS END*/         
        if ($NeedtoCompleteWorkflowList) {
          echo 'OrderUID : '.$OrderUID.' updated Successfully';
        } else {
          echo 'OrderUID : '.$OrderUID.' No workflow need to complete';
        }
      } else {
        echo 'OrderUID : '.$OrderUID.' CD and Scheduling workflow is not enabled for this customer';
      }
    }

  }

  /**
  *Function get company details based on company name 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Monday 20 July 2020.
  */

  function getOrderPropertyData($OrderUID,$BorrowerFirstName)
  {
    return $this->db->select('BorrowerFirstName')->from('tOrderPropertyRole')->where('OrderUID',$OrderUID)->like('BorrowerFirstName',$BorrowerFirstName)->get()->row();
  }

  /**
  *Function get company details based on company name 
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Monday 20 July 2020.
  */

  function get_mCompanyDetails($CompanyName)
  {
    return $this->db->select('*')->from('mCompanyDetails')->like('CompanyName',$CompanyName)->get()->row();
  }

  /**
  *Function get unmapped efax orders for cron
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Monday 20 July 2020.
  */

  function getUnMappedEfaxOrders()
  {
    return $this->db->select('*')->from('tEFaxData')->where(['FaxType'=>'RECEIVE','IsFaxImageReceived'=>1,'DocumentURL <>'=>'','OrderUID'=>0])->get()->result();
  }

  /**
  *Function get all borrower name for open orders
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Monday 20 July 2020.
  */

  function getBorrowerNameOrders()
  {
    $status[] = $this->config->item('keywords')['Cancelled'];
    $this->db->select('tOrderPropertyRole.BorrowerFirstName,tOrderPropertyRole.OrderUID,tOrders.OrderNumber');
    $this->db->from('tOrders');
    $this->db->join('tOrderPropertyRole', 'tOrderPropertyRole.OrderUID = tOrders.OrderUID');
    $this->db->join('tDocuments','tDocuments.OrderUID=tOrders.OrderUID');
    $this->db->where_not_in('tOrders.StatusUID', $status);
    $this->db->where('tOrderPropertyRole.BorrowerFirstName <> ""',NULL,FALSE);
    $this->db->order_by('tOrders.OrderUID');
    $this->db->group_by('tOrders.OrderUID,tOrderPropertyRole.BorrowerFirstName');
    return $this->db->get()->result();
  }

  /**
  *Function valid order number
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Monday 20 July 2020.
  */

  function isvalidorder($OrderNumber)
  {
    return $this->db->select('OrderUID,OrderNumber')->from('tOrders')->where(['OrderNumber'=>$OrderNumber])->get()->row();
  }

  // Orderwise enable available workflow
  function OrderwiseEnableAvailableWorkflow() {
    // In URL Cron/OrderwiseEnableAvailableWorkflow?CustomerUID=29
    $CustomerUID = $this->input->get('CustomerUID'); 
    $CustomOrderUID = $this->input->get('OrderUID'); 

    // Check Customer ID is passed or not
    if (!empty($CustomerUID)) {
      $status[0] = $this->config->item('keywords')['ClosedandBilled'];
      $status[1] = $this->config->item('keywords')['ClosingCompleted'];

      $this->db->select('OrderUID, OrderNumber');
      $this->db->from('tOrders');
      $this->db->where('CustomerUID',$CustomerUID);
      $this->db->where_not_in('tOrders.StatusUID', $status);
      if (!empty($CustomOrderUID)) {
        $this->db->where('OrderUID',$CustomOrderUID);
      }
      $tOrdersData = $this->db->get()->result();

      if (empty($tOrdersData)) {
        exit('No Orders Available');
      }

      // Declare variable
      $WorkflowEnabledDescriptionsArr = array();
      foreach ($tOrdersData as $key => $value) {
        $OrderUID = $value->OrderUID;
        $OrderNumber = $value->OrderNumber;
        $WorkflowEnabledDescriptions = $this->InsertOrderWorkflow_ALL($OrderUID, $OrderNumber);
        if (!empty($WorkflowEnabledDescriptions)) {
          $WorkflowEnabledDescriptionsArr[] = $WorkflowEnabledDescriptions;
          echo $WorkflowEnabledDescriptions;
        }
      }

      if (empty($WorkflowEnabledDescriptionsArr)) {
        if (!empty($CustomOrderUID)) {
          echo "This Order Number: ".$OrderNumber." already all workflows enabled.";
        } else {
          echo "All orders workflows already enabled.";
        }
      }
    } else {
      echo "Please pass the CustomerUID in URL Like: Cron/OrderwiseEnableAvailableWorkflow?CustomerUID=29&OrderUID=";
    }  
    
  }

  // Orderwise enable available workflow
  function InsertOrderWorkflow_ALL($OrderUID, $OrderNumber)
  {
    // Declare array variable for log
    $WorkflowModuleName = array();

    $OrderWorkflows = $this->Orderentrymodel->GetOrderWorkflowsentryWorkflows($OrderUID);

    foreach ($OrderWorkflows as $key => $value) {

      $tOrderWorkflows = [];

      $tOrderWorkflows['OrderUID'] = $OrderUID;
      $tOrderWorkflows['WorkflowModuleUID'] = $value->WorkflowModuleUID;

      $tOrderWorkflows['IsPresent'] = STATUS_ZERO;
      $tOrderWorkflows['EntryDateTime'] = NULL;
      $tOrderWorkflows['DueDateTime'] = NULL;
      $tOrderWorkflows['IsPresent'] = STATUS_ONE;

      if(!$this->Orderentrymodel->is_workflowdependentexists($OrderUID,$value->WorkflowModuleUID) && $value->WorkflowModuleUID != $this->config->item('Workflows')['Submissions']) {
        $tOrderWorkflows['EntryDateTime'] = date('Y-m-d H:i:s', strtotime("now"));
        $tOrderWorkflows['DueDateTime'] = calculate_workflowduedatetime($OrderUID,$value->WorkflowModuleUID);
        $tOrderWorkflows['IsAssign'] = STATUS_ONE;


        //check parking enabled
        $parking = $this->Common_Model->is_autoparking_enabledfor_orderworkflow($OrderUID,$value->WorkflowModuleUID);
        if(!empty($parking)) {
          $is_parking_row_available = $this->Common_Model->get_row('tOrderParking', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $value->WorkflowModuleUID,'IsCleared'=>0]);

          if (empty($is_parking_row_available)) {         
            $parkingdata['OrderUID'] = $OrderUID;
            $parkingdata['WorkflowModuleUID'] = $value->WorkflowModuleUID;
            $parkingdata['ReasonUID'] = '';
            $parkingdata['Remainder'] = isset($parking->ParkingDuration) && !empty($parking->ParkingDuration) ? date('Y-m-d H:i:s', strtotime('+' . $parking->ParkingDuration . ' Hours')) : NULL;
            $parkingdata['Remarks'] = sprintf($this->lang->line('autoparking_assigned'),$parking->WorkflowModuleName,$parking->ParkingDuration,site_datetimeformat(date('Y-m-d H:i:s', strtotime('+' . $parking->ParkingDuration . ' Hours'))));
            $parkingdata['RaisedByUserUID'] = !empty($this->loggedid) ? $this->loggedid: $this->config->item('Cron_UserUID');
            $parkingdata['RaisedDateTime'] = date('Y-m-d H:i:s');
            $this->Common_Model->save('tOrderParking', $parkingdata);

            $notesdata = array(
              'OrderUID'=>$OrderUID,
              'WorkflowUID'=> $value->WorkflowModuleUID,
              'Description'=> nl2br($parkingdata['Remarks'] ),
              'Module'=> $parking->SystemName,
              'CreatedByUserUID'=> !empty($this->loggedid) ? $this->loggedid: $this->config->item('Cron_UserUID'),
              'CreateDateTime'=> date('Y-m-d H:i:s'));
            $this->db->insert('tNotes',$notesdata);
          }
        }


      } else {
        $tOrderWorkflows['IsPresent'] = STATUS_ZERO;
      }

      //insert workflow here
      if(!empty($tOrderWorkflows)) {
        //Enable Workflow Name
        $WorkflowModuleName[] = $value->WorkflowModuleName;
        $this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);
      }

    }

    if (!empty($WorkflowModuleName)) {
      return "Order Number : ".$OrderNumber." Enabled Workflows :".implode(', ', $WorkflowModuleName).". <br/>";
    }

  }

  /**
  * Function 
  *
  * @param DocumentTypeUID, OrderUID
  *
  * @throws no exception
  * @author Santhiya M <santhiya.m@avanzegroup.com>
  * @return 
  * @since July 28th 2020
  *
  */
  function getOrderChecklist($DocumentTypeUID, $OrderUID){
    $HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];
    $query = $this->db->select('DocumentTypeUID')->from('tDocumentCheckList')->where(array('DocumentTypeUID'=>$DocumentTypeUID,'WorkflowUID'=>$HoiWorkflowModuleUID,'OrderUID'=>$OrderUID))->get();
    if($query->num_rows() > 0){
      return $query->row();
    }else{
      return false;
    }
  }
  /**
  * Function 
  *
  * @param DocumentTypeUID, OrderUID, array
  *
  * @throws no exception
  * @author Santhiya M <santhiya.m@avanzegroup.com>
  * @return boolean
  * @since July 30th 2020
  *
  */
  function UpdateOrderChecklist($DocumentTypeUID, $OrderUID, $checklistdata){
    $HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];
    $checklist = $this->getOrderChecklist($DocumentTypeUID, $OrderUID);

    if(!empty($checklist)) {          
      $this->db->where(array('DocumentTypeUID'=>$DocumentTypeUID,'WorkflowUID'=>$HoiWorkflowModuleUID,'OrderUID'=>$OrderUID));
      $this->db->update('tDocumentCheckList',$checklistdata);

    } else {

      $Category = $this->db->select('CategoryUID')->from('mDocumentType')->where('DocumentTypeUID',$DocumentTypeUID)->get()->row();
      $checklistdata['OrderUID'] = $OrderUID; 
      $checklistdata['CategoryUID'] = $Category->CategoryUID; 
      $checklistdata['DocumentTypeUID'] = $DocumentTypeUID; 
      $checklistdata['WorkflowUID'] = $HoiWorkflowModuleUID; 
      $checklistdata['ModifiedUserUID'] = $this->config->item('Cron_UserUID'); 
      $checklistdata['ModifiedDateTime'] = date("d/m/Y H:i:s"); 
      $this->db->insert('tDocumentCheckList',$checklistdata);
    }
    if($this->db->affected_rows() > 0) {
      return true;
    }else{
      return false;
    }
  }

  /**
  *Function log message
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Friday 14 August 2020.
  */
  function logmessage_deleted($message)
  {
    $logFilePath = FCPATH . 'uploads/Others/';
    $logFile = $logFilePath.'deleteorder-' . date('Y-m-d').'.log';
    $this->Common_Model->CreateDirectoryToPath($logFilePath);
    $cronLogFile = fopen($logFile, "a");
    fwrite($cronLogFile, date('Y-m-d H:i:s'). ' : ' .$message. PHP_EOL);
    fclose($cronLogFile);
  }

  /**
  *Function remove loan from doctrac
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Friday 14 August 2020.
  */
  function deleteLoanAllTables($LoanNumber)
  {

    $tables = ['taudittrail','tAutomationLog','tBinOrders','tCalcAuditLog','tCalculator','tCategory','tDocumentCheckList','tDocumentCheckListHistory','tDocuments','tDocumentTracking','tEmailImport','tExceptionMailReport','tLoanFiles','tLogs','tNotes','tOrderAssignment','tOrderAssignments','tOrderAssignmentsHistory','tOrderComments','tOrderDocChase','tOrderDocumentCheckIn','tOrderDurations','tOrderEsclation','tOrderException','tOrderFollowUp','tOrderImport','tOrderImport_bak','tOrderInstruction','tOrderMileStone','tOrderOnhold','tOrderPageDocument','tOrderParking','tOrderPropertyRole','tOrderQueues','tOrdersDataEntry','tOrderWithdrawal','tOrderWorkflows','tOrderWorkflowsHistory','tPage','tQuestionAnswer','tResponseTrack','tSpecialInstructions','tSubCategory','tTempDocuments','tOrderReWork','tOrderWorkflowsData','tSubQueueCategory','tOrderChecklistExpiryComplete','tOrderSubQueues','tOrderReverse','tOrderHighlights','tOrders'];

    $this->db->select('LoanNumber,OrderUID');
    $this->db->from('tOrders');
    $this->db->where('tOrders.LoanNumber', $LoanNumber);
    $Order =  $this->db->get()->row();

    if(empty($Order)) {
      return 'No Order Found';
    }

    if (!isset($Order->OrderUID) || empty($Order->OrderUID) || $Order->OrderUID == '') {
      return 'No OrderUID Found';
    }

    $message = '<b> Loan Number :'.$LoanNumber . '</b> <b> OrderUID : '.$Order->OrderUID . '</b> <br>'.PHP_EOL;
    //trans start
    $this->db->trans_begin();

    foreach ($tables as $table) {

      if (is_mysql_table_exists($table) ) {

        if ($table == 'tNotes') {
          $is_tNotes_row_available = $this->db->get_where('tNotes', array('OrderUID' => $Order->OrderUID))->result();
          // Delete tNotestUser record
          if (!empty($is_tNotes_row_available)) {
            $affected_rows_count = 0;
            foreach ($is_tNotes_row_available as $key => $value) {                
                $this->db->where_in('NotesUID', $value->NotesUID);
                $this->db->delete('tNotesUser');
                if($this->db->affected_rows() > 0) {
                  $affected_rows_count++;
                }
            }
            if (!empty($affected_rows_count)) {
              $message .= 'tNotesUser '.$affected_rows_count.' rows deleted <br>'.PHP_EOL;
            }
          }
        }
        $this->db->where_in('OrderUID', $Order->OrderUID);
        $this->db->delete($table);

        if($this->db->affected_rows() > 0) {
          $message .= $table . ' '.$this->db->affected_rows().' rows deleted <br>'.PHP_EOL;
        }
      }

    }

    if ($this->db->trans_status() === false) {
      $this->db->trans_rollback();
      $message = $LoanNumber . ' - deletion failed';
    } else {
      $this->db->trans_commit();
    }

    $this->logmessage_deleted($message);

    return $message;
  }

  /**
  *Function Complete Exception Queue and Workflow 
  *@author SathishKumar <sathish.kumar@avanzegroup.com>
  *@since Thursday 22 October 2020.
  */
  function CompleteExceptionQueueandWorkflow($ClientUID, $WorkflowModuleUID, $QueueUID, $WorkflowCompleteMilestones) {

    $ConnectWorkflowFuntion  = $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'];

    if (empty($ConnectWorkflowFuntion)) {
      
      return "Workflow Function Not Found!";
    }

    $this->db->select('tOrders.OrderUID, tOrders.LoanNumber');

    /*^^^^^ Get MyOrders Query ^^^^^*/
    $this->Common_Model->{$ConnectWorkflowFuntion}(false);

    $this->db->join("tOrderQueues","tOrderQueues.OrderUID=tOrders.OrderUID AND tOrderQueues.QueueStatus = 'Pending'");

    $this->db->join("mQueues","tOrderQueues.QueueUID=mQueues.QueueUID AND mQueues.QueueUID = '".$QueueUID."' and mQueues.WorkflowModuleUID='".$WorkflowModuleUID."'");

    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = '".$WorkflowModuleUID."' AND tOrderParking.IsCleared = 0)",NULL,FALSE);

    if (!empty($WorkflowCompleteMilestones)) {
      $this->db->where_in('tOrders.MilestoneUID', $WorkflowCompleteMilestones, FALSE);
    }    

    $this->db->where('tOrders.CustomerUID', $ClientUID);

    $Result = $this->db->get()->result();

    if (empty($Result)) {
      
      return "No Orders Found!";
    }

    // Get Workflow
    $NeedtoCompleteWorklfowList = $this->db->select('WorkflowModuleUID,WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID',$WorkflowModuleUID)->get()->result_array();

    $OrderLogsDescriptions = '
    <style>
    #OrderLogTable {
      font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #OrderLogTable td, #OrderLogTable th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #OrderLogTable tr:nth-child(even){background-color: #f2f2f2;}

    #OrderLogTable tr:hover {background-color: #ddd;}

    #OrderLogTable th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #4CAF50;
      color: white;
    }
    </style>
    <table id="OrderLogTable"><tr><th>S.No</th><th>Loan Number</th><th>Description</th></tr><body>';

    $SNo = 0;
    foreach ($Result as $key => $value) {
        
      $tmp_log = $this->Orderentrymodel->CompleteGivenWorkflows($value->OrderUID, $NeedtoCompleteWorklfowList);

      if (!empty($tmp_log)) {

        $OrderLogsDescriptions .= '<tr><td>'.++$SNo.'</td><td>'.$value->LoanNumber.'</td><td>'.$tmp_log.'</td></tr>';
        
        $tmp_log = '';
      }
    }  

    return $OrderLogsDescriptions .= '</body></table>';

  }

  /**
  *Function DocsOut DocsChecked Condition Pending Orders 
  *@author SathishKumar <sathish.kumar@avanzegroup.com>
  *@since Wednesday 04 November 2020.
  */
  function GetDocsOut_DocsCheckedConditionPendingOrders() {

    $this->db->select("tOrders.OrderUID, mStaticQueues.StaticQueueUID, mStaticQueues.StaticQueueTableName, mCategoriesTAT.TAT_Aging as FollowupDuration, mWorkFlowModules.WorkflowModuleName, tSubQueueCategory.LastModifiedDateTime as RaisedDateTime");

    $this->db->select('(SELECT ClearedDateTime FROM tOrderFollowUp WHERE tOrderFollowUp.OrderUID = tOrders.OrderUID AND tOrderFollowUp.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderFollowUp.StaticQueueUID = mStaticQueues.StaticQueueUID AND tOrderFollowUp.IsCleared = 1 ORDER BY ClearedDateTime DESC LIMIT 1) AS LastFollowupDateTime',FALSE);

    $this->db->select('tSubQueueCategory.CategoryUID');

    $this->Common_Model->GetDocsOrders(true);

    $this->db->join('mStaticQueues','mStaticQueues.StaticQueueUID = 13');

    $this->db->join('mQueueColumns','mQueueColumns.WorkflowUID = tOrderWorkflows.WorkflowModuleUID AND (mQueueColumns.SubQueueCategoryUID IS NOT NULL OR mQueueColumns.SubQueueCategoryUID <> "") ');

    $this->db->join('mCategoriesTAT','mCategoriesTAT.SubQueueCategoryUID = mQueueColumns.SubQueueCategoryUID');

    $this->db->join('mSubQueueCategory','mSubQueueCategory.SubQueueCategoryUID = mQueueColumns.SubQueueCategoryUID AND mSubQueueCategory.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND mSubQueueCategory.SubQueueSection = mStaticQueues.StaticQueueTableName');

    $this->db->join('mWorkFlowModules','mWorkFlowModules.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID');

    $this->db->join('tSubQueueCategory','tSubQueueCategory.OrderUID = tOrders.OrderUID AND tSubQueueCategory.SubQueueCategoryUID = mQueueColumns.SubQueueCategoryUID AND FIND_IN_SET(mCategoriesTAT.CategoryUID,tSubQueueCategory.CategoryUID)');

    $this->db->where('FIND_IN_SET(
                mCategoriesTAT.CategoryUID,
                mSubQueueCategory.CategoryUIDs
              )');

    // Docsout queue "Docs Checked Conditions Pending" Sub queue conditions
    $QueueTypes = $this->config->item("DocsCheckedConditionPending");
    $this->db->where_in('tOrderImport.Queue',$QueueTypes);

    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE OrderUID = tOrders.OrderUID AND WorkflowModuleUID = '".$this->config->item('Workflows')['DocsOut']."' AND IsCleared = 0)",NULL,FALSE);
    
    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderFollowUp WHERE tOrderFollowUp.OrderUID = tOrders.OrderUID AND tOrderFollowUp.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderFollowUp.StaticQueueUID = mStaticQueues.StaticQueueUID AND IsCleared = 0)",NULL,FALSE);

    // remove reversed orders
    $this->db->where('tOrderWorkflows.IsReversed != 1 AND tOrderWorkflows.WorkflowModuleUID = '.$this->config->item('Workflows')['DocsOut']);

    $this->db->where('tOrders.CustomerUID', 29);

    return $this->db->get()->result();

  }

  /**
  *Function Get next order lock expiration date - automatically change date based on current date 
  *@author SathishKumar <sathish.kumar@avanzegroup.com>
  *@since Monday 09 November 2020.
  */
  public function GetNextOrderLockExpirationUpdates($ClientUID, $WorkflowModuleUID, $DaysAdd)
  {
    // echo date('m/d/Y', strtotime(date('m/d/Y'). ' + '.(!empty($DaysAdd) ? $DaysAdd : 0).' days')); exit();
    $FHALockExpirationDate = date('m/d/Y', strtotime(date('m/d/Y'). ' + '.(!empty($DaysAdd) ? $DaysAdd : 0).' days'));
    $VALockExpirationDate = date('m/d/Y', strtotime(date('m/d/Y'). ' + '.(!empty($DaysAdd) ? $DaysAdd : 0).' days'));

    $this->db->where(array(
      'CustomerUID' => $ClientUID, 
      'WorkflowModuleUID' => $WorkflowModuleUID
    ));

    $data = array(
      'FHALockExpirationDate' => $FHALockExpirationDate, 
      'VALockExpirationDate' => $VALockExpirationDate
    );
    $this->db->update('mCustomerWorkflowModules', $data);

    if ($this->db->affected_rows() > 0) {
      $workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID', $WorkflowModuleUID)->get()->row();
      echo $workflow_names->WorkflowModuleName." Workflow <b>FHA Lock Expiration Date : ".$FHALockExpirationDate."</b> and <b>VA Lock Expiration Date : ".$VALockExpirationDate."</b> is updated";
    } else {
      echo "No records affected.";
    }
  }

  /**
  *Function  If closed date  is past or today date then check if the workup is in completed status (Workup Completed, ISSUED LE & STC ISSUE) and milestone to be in 2D,2E ,2H,2J,2K,2I  . when the loan matches the conditions to be moved to rework
  *@author SathishKumar <sathish.kumar@avanzegroup.com>
  *@since Saturday 21 November 2020.
  */
  function WorkupReworkCron($Conditions = [])
  {
    $this->db->select('tOrders.OrderUID, tOrders.LoanNumber');
    $this->Common_Model->GetWorkUpQueue(false, ['SkipCondition'=>TRUE]);

    // closed date  is past or today date 
    $this->db->where('STR_TO_DATE(tOrderImport.ClosedDate,"%m/%d/%Y") <= NOW()',NULL,FALSE);

    // check if the workup is in completed status (Workup Completed, ISSUED LE & STC ISSUE) 
    $WorkupSubQueueComplete = $this->config->item('WorkupSubQueueComplete');
    $this->db->group_start();
    foreach ($WorkupSubQueueComplete as $key => $QueueUID) {            
      if ($key === 0) {
        $this->db->where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
      } else {
        $this->db->or_where("EXISTS (SELECT 1 FROM tOrderQueues WHERE tOrderQueues.OrderUID = tOrders.OrderUID AND tOrderQueues.QueueUID = ".$QueueUID." AND tOrderQueues.QueueStatus = 'Pending')",NULL,FALSE);
      }
    }        
    $this->db->or_where('tOrderAssignments.WorkflowStatus = '.$this->config->item('WorkflowStatus')['Completed'].' ');    
    $this->db->group_end();

    // milestone to be in 2D,2E ,2H,2J,2K,2I  
    $WorkupReworkMilestones = $this->config->item('WorkupReworkMilestones');
    if (!empty($WorkupReworkMilestones)) {
      $this->db->where_in('tOrders.MilestoneUID', $WorkupReworkMilestones);
    }

    if (isset($Conditions['OrderUID']) && !empty($Conditions['OrderUID']) && is_numeric($Conditions['OrderUID'])) {
      $this->db->where('tOrders.OrderUID', $Conditions['OrderUID']);
    }

    // $this->db->limit(1);
    // Remove parking orders
    $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE tOrderParking.OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID = tOrderWorkflows.WorkflowModuleUID AND tOrderParking.IsCleared = 0)",NULL,FALSE);

    $result = $this->db->get()->result();

    // echo "<pre>"; print_r($result); exit();

    if (empty($result) && isset($Conditions['CronFunction'])) {
      echo "No orders found";
    }

    foreach ($result as $key => $value) {

      $LogDetails = '';

      // Get workflow name
      $WorkflowModuleName = $this->Common_Model->GetWorkflowModuleNameByWorkflowModuleUID($value->WorkflowModuleUID);

      $Queues = $this->Common_Model->getCustomerWorkflowQueues($value->WorkflowModuleUID);
      foreach ($Queues as $key => $queue) {

        // Clear the exception if raised for this order
        $tOrderQueuesData = [];
        $tOrderQueuesData['QueueStatus'] = "Completed";
        $tOrderQueuesData['CompletedReasonUID'] = '';
        $tOrderQueuesData['CompletedRemarks'] = '';
        $tOrderQueuesData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
        $tOrderQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

        $this->db->where(array(
          'OrderUID'=>$value->OrderUID,
          'QueueUID'=>$queue->QueueUID, 
          "QueueStatus" => "Pending"
        ));
        $this->db->update('tOrderQueues', $tOrderQueuesData);

        if($this->db->affected_rows()) {
          $LogDetails .= '<b>'.$WorkflowModuleName.' - '.$queue->QueueName.'</b> sub queue was force completed.<br/>';
        }

        // Clear the followup if raised for this queue
        $tOrderFollowUpData = [];
        $tOrderFollowUpData['IsCleared'] = 1;
        $tOrderFollowUpData['ClearedReasonUID'] = '';
        $tOrderFollowUpData['ClearedRemarks'] = '';
        $tOrderFollowUpData['ClearedByUserUID'] = $this->config->item('Cron_UserUID');
        $tOrderFollowUpData['ClearedDateTime'] = date('Y-m-d H:i:s');

        $this->db->where(array(
          'OrderUID'=>$value->OrderUID,
          'QueueUID'=>$queue->QueueUID,
          'WorkflowModuleUID'=>$value->WorkflowModuleUID,
          'IsCleared'=>0
        ));
        $this->db->update('tOrderFollowUp', $tOrderFollowUpData); 

        if($this->db->affected_rows()) {        
          $LogDetails .= '<b>'.$WorkflowModuleName.'</b> followup is cleared.<br/>';
        }
      }

      //update the workflow 
      $tOrderWorkflowsArray = array('IsRework' => STATUS_ONE);

      $this->db->where(array('OrderUID' => $value->OrderUID, 'WorkflowModuleUID' => $value->WorkflowModuleUID));
      $this->db->update('tOrderWorkflows', $tOrderWorkflowsArray);

      if($this->db->affected_rows()) {
          $LogDetails .= '<b>'.$WorkflowModuleName.'</b> loan is moved to Rework queue.<br/>';
      }

      //update the workflow 
      $tOrderAssignmentsArray = array('WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress']);

      $this->db->where(array('tOrderAssignments.OrderUID' => $value->OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $value->WorkflowModuleUID));
      $this->db->update('tOrderAssignments', $tOrderAssignmentsArray);

      /*INSERT ORDER LOGS BEGIN*/
      if (!empty($LogDetails) && !empty($LogDetails)) {
        $this->Common_Model->OrderLogsHistory($value->OrderUID,$LogDetails,Date('Y-m-d H:i:s'));
      }      
      /*INSERT ORDER LOGS END*/

      if (isset($Conditions['CronFunction']) && !empty($LogDetails)) {
        echo $value->LoanNumber.'<br/>'.$LogDetails;
        echo "<hr>";
      }      

    }
      
  }

}
?>
