<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class OrderComplete extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('OrderComplete_Model');
		$this->load->model('PriorityCustomization/PriorityCustomization_model');
		$this->load->model('Priority_Report/Priority_Report_model');
		$this->lang->load('keywords');
		$this->load->library('form_validation');
	}	

	function OrderCancellation()
	{
		$OrderUID = $this->input->post('OrderUID');


		// Cancel Order Followup Remove Begin
		if(!empty($OrderUID)){

			$this->db->delete('tOrderFollowUp',array('tOrderFollowUp.OrderUID' => $OrderUID,'FollowUpStatus' =>'Pending'));
		}
		// Cancel Order Followup Remove End

		// BinOrders Delete Begin
		if(!empty($OrderUID)){
			$this->db->where('tBinOrders.OrderUID',$OrderUID);
			$this->db->delete('tBinOrders');
		}
		// BinOrders Delete End
		$this->db->select('StatusUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$StatusUID = $this->db->get()->row()->StatusUID;

		$UpdateStatus = array('Cancel_Temp_StatusUID' => $StatusUID,'DocumentStorage'=>'', 'IsFollowUp'=>0);
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$this->db->update('tOrders',$UpdateStatus);

		/*INSERT ORDER LOGS BEGIN*/
		$this->Common_Model->OrderLogsHistory($OrderUID,'Order Cancelled',Date('Y-m-d H:i:s'));
		/*INSERT ORDER LOGS END*/

		$data['StatusUID'] = $this->config->item('keywords')['Cancelled'];
		$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);
		$response = [];
		$response['validation_error'] = 1;
		if ($update) {
			$response['message'] = $this->lang->line('Order_Cancelled');
			$response['validation_error'] = 0;
		}
		else{
			$response['message'] = $this->lang->line('Cancelled_Complete');
			$response['validation_error'] = 1;
		}

		$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
		

		$this->output->set_content_type('applicaton/json')
		->set_output(json_encode($response));
	}



	public function RaiseException()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$exceptiontype = $this->input->post('exceptiontype');
		$remarks = $this->input->post('remarks');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');
		$getUserDetailsEmail = $this->Common_Model->GetProjectUserDetails($OrderUID);

		$DeleteTBinOrders =$this->OrderComplete_Model->DeleteBinOrder($OrderUID);
		
		$getExceptionName = $this->Common_Model->get_row('mExceptions', ['ExceptionUID'=>$exceptiontype]);
		
		foreach ($getUserDetailsEmail as $key => $value) {
			$ExceptionUserMail = array(
				'ExceptionTypeUID' => $exceptiontype, 
				'ProjectUID' => $value->ProjectUID, 
				'UserUID' => $value->UserUID, 
				'CreatedBy' => $this->UserUID, 
				);
			$InsertEcxecptionMail = $this->Common_Model->save('tExceptionMailUser', $ExceptionUserMail);			
			$result = $this->email
			->from($this->Email)
			->to($value->EmailID)			
			->subject($value->OrderNumber.' has been raised an exception')
			->message('Here we have '.$getExceptionName->ExceptionName.'exeption raising in the <b>'.$value->OrderNumber.'</b> order for <b>'.$remarks.'</b>')
			->send();
		}

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('exceptiontype', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$data['OrderUID'] = $OrderUID;
			$data['ExceptionRemarks'] = $remarks;
			$data['ExceptionTypeUID'] = $exceptiontype;
			$data['ExceptionRaisedByUserUID'] = $this->loggedid;
			$data['ExceptionRaisedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();
			$insert = $this->Common_Model->save('tOrderException', $data);

			$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $OrderUID]);

			if ($tOrders->Temp_StatusUID == '' || in_array($tOrders->StatusUID, [$this->config->item('keywords')['Indexing Exception'],$this->config->item('keywords')['Fatal Exception'], $this->config->item('keywords')['Non Fatal Exception']])) {
				$this->Common_Model->save('tOrders', ['Temp_StatusUID' => $tOrders->StatusUID], 'OrderUID', $OrderUID);

			} else {
				$this->Common_Model->save('tOrders', ['Temp_StatusUID' => $tOrders->StatusUID], 'OrderUID', $OrderUID);
			}


			if ($exceptiontype == 1) {
				$StatusUID = $this->config->item('keywords')['Indexing Exception'];
				$this->Common_Model->save('tOrders', ['StatusUID'=>$StatusUID], 'OrderUID', $OrderUID);
			}
			elseif ($exceptiontype == 2) {
				$StatusUID = $this->config->item('keywords')['Fatal Exception'];
				$this->Common_Model->save('tOrders', ['StatusUID'=>$StatusUID], 'OrderUID', $OrderUID);
			}
			elseif ($exceptiontype == 3)
			{
				$StatusUID = $this->config->item('keywords')['Non Fatal Exception'];
				$this->Common_Model->save('tOrders', ['StatusUID'=>$StatusUID], 'OrderUID', $OrderUID);
			}

			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Exception_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'Exception Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Exception_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	public function ClearException()
	{
		$OrderUID = $this->input->post('OrderUID');
		// $ExceptionTypeUID = $this->input->post('ExceptionTypeUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		// $this->form_validation->set_rules('ExceptionTypeUID', '', 'required');
		$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$data['IsExceptionCleared'] = 1;
			$data['ExceptionRemarks'] = $remarks;
			$data['ExceptionClearedByUserUID'] = $this->loggedid;
			$data['ExceptionClearedDateTime'] = date('Y-m-d H:i:s');

			$filter['OrderUID'] = $OrderUID;
			$filter['IsExceptionCleared'] = 0;


			$this->db->trans_begin();


			$update = $this->Common_Model->save('tOrderException', $data, $filter);

			$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $OrderUID]);

			$DeleteOrderAssignments =$this->OrderComplete_Model->DeleteOrderAssignment($OrderUID);

			if ($tOrders->Temp_StatusUID != '' && $this->input->post('submit') == 'clearexceptionandcomplete') {


				// Clear Exception and Complete Workflow
				$Updated_Status = $this->config->item('keywords')['Stacking Completed'];
				if ($tOrders->Temp_StatusUID != '') {
					$clearexception_return_status = $this->Common_Model->GetNextWorkflowToComplete($tOrders, $tOrders->Temp_StatusUID);
					$Updated_Status = $clearexception_return_status['Status'];
					$Msg = $clearexception_return_status['Msg'];



					// Check Order is Stacked or nnot if Not Stacked throw Error.
					if (in_array($Updated_Status, [$this->config->item('keywords')['Stacking Completed'],$this->config->item('keywords')['Audit Completed']] )) {

						$ispage_inserted = $this->Common_Model->get('tPage', ['OrderUID'=>$OrderUID]);
						if (empty($ispage_inserted)) {
							$this->db->trans_rollback();
							$this->output->set_content_type('application/json')
							->set_output(json_encode(array('validation_error' => 2, 'message' => 'Order is Not Stacked. Please Stack Order and Clear Exception')))->_display();
							exit;

						}

					}

					$tOrderAssignment_insert = $clearexception_return_status['tOrderAssignment_insert'];

					if (!empty($tOrderAssignment_insert)) {
						$this->Common_Model->save('tOrderAssignment', $tOrderAssignment_insert, ['OrderUID'=>$tOrders->OrderUID]);
					}
				}

				$this->Common_Model->save('tOrders', ['StatusUID' => $Updated_Status], 'OrderUID', $OrderUID);


				$Msg = 'Exception Cleared and ' . $Msg;
			} elseif ($tOrders->Temp_StatusUID != '') {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'Exception Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				// Only Clear Exception
				$this->Common_Model->save('tOrders', ['StatusUID' => $tOrders->Temp_StatusUID], 'OrderUID', $OrderUID);
				$Msg = $this->lang->line('Exception_Cleared');

			} else {
				// Alternate setup if no value is present in Temp_StatusUID, Don't care about this part
				$this->Common_Model->save('tOrders', ['StatusUID' => $this->config->item('keywords')['Stacking In Progress']], 'OrderUID', $OrderUID);
				$Msg = $this->lang->line('Exception_Cleared_Failed');
			}


			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				$this->db->trans_commit();
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	function OrderOnHold(){
		$OrderUID = $this->input->post('OrderUID');
		$remarks=$this->input->post('comments');
		$CustomerNotification=$this->input->post('CustomerNotification'); 
		$UserEmails=$this->input->post('UserEmails'); 
		// $Onholdtype=$this->input->post('Onholdtype'); 

		$arr = array(
			'"' => '',
			'[' => '',
			']' => ''
		);

		$CustomerEmails = strtr($UserEmails,$arr); 

		$OrderOnHoldResult = $this->OrderComplete_Model->OrderOnHold($OrderUID,$remarks);

		if($CustomerNotification === 'on'){

	      /*MAIL FUNCTION START*/

	        $this->load->library('email');
	        $OrderDetails = $this->OrderComplete_Model->OrderDetails($OrderUID);

	        $subject = '';
	        $message = '';
	        $send_mail = 0;

	            $subject = $OrderDetails->OrderNumber.' - Source Point Response';

	            $message = '

	            <p>&nbsp;&nbsp;&nbsp; *** AUTOMATED EMAIL *** DO NOT REPLY ***</p>

	            <p>&nbsp;&nbsp;&nbsp;</p>

	            <p>&nbsp;&nbsp;&nbsp; Order Number ='.$OrderDetails->OrderNumber.'</p>

	            <p>&nbsp;&nbsp;&nbsp; Loan # = '.$OrderDetails->LoanNumber.'</p>

	            <p style="margin-top:3px;margin-right:0;margin-bottom:3px;margin-left:0"><span style="font-size:14px;font-family:Consolas;
	            color:black">&nbsp;&nbsp;'.$OrderDetails->PropertyAddress1.' '.$OrderDetails->PropertyAddress2.'</span></p>

	            <p style="margin-top:3px;margin-right:0;margin-bottom:3px;margin-left:0"><span style="font-size:14px;font-family:Consolas;
	            color:black">&nbsp;&nbsp;'.$OrderDetails->PropertyCityName.',&nbsp;'.$OrderDetails->PropertyStateCode.',&nbsp;'.$OrderDetails->PropertyZipCode.'</span></p>

	            <p style="margin-top:3px;margin-right:0;margin-bottom:
	            3px;margin-left:0"><span style="font-size:14px;font-family:Consolas;
	            color:black">&nbsp;&nbsp;'.$OrderDetails->PropertyCountyName.' County</span></p>

	            <p>&nbsp;&nbsp;&nbsp; SourcePoint Response: </p>

	            <p>&nbsp;&nbsp;&nbsp;&nbsp;'.$remarks.'</p>

	            <p>&nbsp;</p>

	            <p>&nbsp;If you would like to discuss further, please call the Source Point
	            Customer Service at 1-855-884-8001 or email to <a href="mailto:customerservice@isgnsolutions.com">customerservice@isgnsolutions.com</a>.</p>

	            <p>&nbsp;&nbsp;</p>

	            <p>&nbsp;&nbsp;&nbsp; *** AUTOMATED EMAIL *** DO NOT REPLY *** AUTOMATED EMAIL
	            *** DO NOT REPLY *** AUTOMATED EMAIL ***&nbsp;</p>';

	            $this->email->from('customerservice@isgnsolutions.com');



	            if(!empty($CustomerEmails))
	            {
	             $this->email->to($CustomerEmails);
	              $this->email->subject($subject)
	              ->message($message)
	           		->bcc('isgnreports@direct2title.com');

	              if(!$this->email->send()){
	                echo json_encode(array('validation_error'=>0,'data'=>$this->email->print_debugger(),'message'=>'Order Released but Error in sending mail'));exit;
	              }
	              else{

	                $data['ModuleName']='Order OnHold. '.$CustomerEmails.' '.' Mail Sent -  mail';
	                $data['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
	                $data['DateTime']=date('y-m-d H:i:s');
	                $data['TableName']='tOrderOnhold';
	                $data['OrderUID'] =$OrderUID;
	                $data['UserUID']=$this->loggedid;                
	                $this->Common_Model->Audittrail_insert($data);
	                echo json_encode(array('validation_error'=>1,'message'=>'Order OnHold Successfully'));exit;
	              }
	            }
	        // }

	      /*MAIL FUNCTION END*/
	    }

		if ($OrderOnHoldResult == 1) {
			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID,'Order On Hold',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
			$OrderOnHoldResultMsg = array('validation_error' => 1,'message'=>'Order OnHold Successfully');
			echo json_encode($OrderOnHoldResultMsg);exit();
		}
		else{
			$OrderOnHoldResultMsg = array('validation_error' => 0,'message'=>'Failed');
			echo json_encode($OrderOnHoldResultMsg);exit();
		}
	}
	function ReleaseOnHold(){
		$OrderUID = $this->input->post('OrderUID');
		$comments=$this->input->post('comments');
		$CustomerNotification=$this->input->post('CustomerNotification'); 
		$UserEmails=$this->input->post('UserEmails');
		$OnHoldUID=$this->input->post('OnHoldUID');

		$arr = array(
			'"' => '',
			'[' => '',
			']' => ''
		);

		$CustomerEmails = strtr($UserEmails,$arr); 

		$ReleaseOnHoldResult = $this->OrderComplete_Model->ReleaseOnHold($OrderUID,$comments,$OnHoldUID);


		if($CustomerNotification === 'on'){

	      /*MAIL FUNCTION START*/

	        $this->load->library('email');
	        $OrderDetails = $this->OrderComplete_Model->OrderDetails($OrderUID);

	        $subject = '';
	        $message = '';
	        $send_mail = 0;

	            $subject = $OrderDetails->OrderNumber.' - Source Point Response';

	            $message = '

	            <p>&nbsp;&nbsp;&nbsp; *** AUTOMATED EMAIL *** DO NOT REPLY ***</p>

	            <p>&nbsp;&nbsp;&nbsp;</p>

	            <p>&nbsp;&nbsp;&nbsp; Order Number ='.$OrderDetails->OrderNumber.'</p>

	            <p>&nbsp;&nbsp;&nbsp; Loan # = '.$OrderDetails->LoanNumber.'</p>

	            <p style="margin-top:3px;margin-right:0;margin-bottom:3px;margin-left:0"><span style="font-size:14px;font-family:Consolas;
	            color:black">&nbsp;&nbsp;'.$OrderDetails->PropertyAddress1.' '.$OrderDetails->PropertyAddress2.'</span></p>

	            <p style="margin-top:3px;margin-right:0;margin-bottom:3px;margin-left:0"><span style="font-size:14px;font-family:Consolas;
	            color:black">&nbsp;&nbsp;'.$OrderDetails->PropertyCityName.',&nbsp;'.$OrderDetails->PropertyStateCode.',&nbsp;'.$OrderDetails->PropertyZipCode.'</span></p>

	            <p style="margin-top:3px;margin-right:0;margin-bottom:
	            3px;margin-left:0"><span style="font-size:14px;font-family:Consolas;
	            color:black">&nbsp;&nbsp;'.$OrderDetails->PropertyCountyName.' County</span></p>

	            <p>&nbsp;&nbsp;&nbsp; Source Point Response: </p>

	            <p>&nbsp;&nbsp;&nbsp;&nbsp;'.$comments.'</p>

	            <p>&nbsp;</p>

	            <p>&nbsp;If you would like to discuss further, please call the Source Point
	            Customer Service at 1-855-884-8001 or email to <a href="mailto:customerservice@isgnsolutions.com">customerservice@isgnsolutions.com</a>.</p>

	            <p>&nbsp;&nbsp;</p>

	            <p>&nbsp;&nbsp;&nbsp; *** AUTOMATED EMAIL *** DO NOT REPLY *** AUTOMATED EMAIL
	            *** DO NOT REPLY *** AUTOMATED EMAIL ***&nbsp;</p>';

	            $this->email->from('customerservice@isgnsolutions.com');



	            if(!empty($CustomerEmails))
	            {
	             $this->email->to($CustomerEmails);

	              $this->email->subject($subject)
	              ->message($message)
	           		->bcc('isgnreports@direct2title.com');

	              if(!$this->email->send()){
	                echo json_encode(array('validation_error'=>0,'data'=>$this->email->print_debugger(),'message'=>'Order Released but Error in sending mail'));exit;
	              }
	              else{

	                $data['ModuleName']='Order Release. '.$CustomerEmails.' '.' Mail Sent -  mail';
	                $data['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
	                $data['DateTime']=date('y-m-d H:i:s');
	                $data['TableName']='tOrderOnhold';
	                $data['OrderUID'] =$OrderUID;
	                $data['UserUID']=$this->loggedid;                
	                $this->Common_Model->Audittrail_insert($data);
	                echo json_encode(array('validation_error'=>1,'message'=>'Order Release Successfully'));exit;
	              }
	            }
	        // }

	      /*MAIL FUNCTION END*/
	    }


		if ($ReleaseOnHoldResult == 1) {
			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID,'Order Release',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
			$ReleaseOnHoldResultMsg = array('validation_error' => 1,'message'=>'Order Release Successfully');
			echo json_encode($ReleaseOnHoldResultMsg);exit();
		}
		else{
			$ReleaseOnHoldResultMsg = array('validation_error' => 0,'message'=>'Failed');
			echo json_encode($ReleaseOnHoldResultMsg);exit();
		}
	}

	function PreScreenassign_complete()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Workflow = $this->input->post('Checked_Workflows');

		/*If PreScreen workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['PreScreen']))
		{

			$this->OrderComplete_Model->change_torderworkflowspresent($OrderUID,$Workflow);

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['PreScreen']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['PreScreen'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['PreScreen']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}
			$data['StatusUID'] = $this->config->item('keywords')['PrescreenCompleted'];
			if($this->Common_Model->get_workflow_completed($OrderUID)) {
				$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
			}

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'PreScreen Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('PreScreen_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	function welcomecall_complete()
	{
		$OrderUID = $this->input->post('OrderUID');
 
		
		/*If WelcomeCall workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['WelcomeCall']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['WelcomeCall']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['WelcomeCall'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['WelcomeCall']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}

			/*---- Check Order reverse happened ----*/
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$data['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
				if($this->Common_Model->get_workflow_completed($OrderUID)) {
					$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
				}
			}

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'WelcomeCall Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('WelcomeCall_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	function titleteamComplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If TitleTeam workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['TitleTeam']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['TitleTeam']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['TitleTeam'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['TitleTeam']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}


			/*---- Check Order reverse happened ----*/
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$data['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
				if($this->Common_Model->get_workflow_completed($OrderUID)) {
					$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
				}
			}

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'TitleTeam Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('TitleTeam_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	function fhavacaseteamComplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If FHAVACaseTeam workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['FHAVACaseTeam']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['FHAVACaseTeam']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['FHAVACaseTeam'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['FHAVACaseTeam']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}


			/*---- Check Order reverse happened ----*/
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$data['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
				if($this->Common_Model->get_workflow_completed($OrderUID)) {
					$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
				}
			}

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'FHAVACaseTeam Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('FHAVACaseTeam_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	function thirdpartyteamComplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If ThirdPartyTeam workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['ThirdPartyTeam']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['ThirdPartyTeam']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['ThirdPartyTeam'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['ThirdPartyTeam']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}

			/*---- Check Order reverse happened ----*/
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$data['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
				if($this->Common_Model->get_workflow_completed($OrderUID)) {
					$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
				}
			}
			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'ThirdPartyTeam Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('ThirdPartyTeam_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}
	
	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** Monday 09 March 2020 **/
	/** HOI PAYOFF WORKFLOW ADD **/
	function hoiComplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If HOI workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['HOI']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['HOI']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['HOI'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['HOI']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}

			/*---- Check Order reverse happened ----*/
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$data['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
				if($this->Common_Model->get_workflow_completed($OrderUID)) {
					$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
				}
			}
			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'HOI Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('HOI_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	/** Borrower Doc WORKFLOW ADD **/
	function BorrowerDocComplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If BorrowerDoc workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['BorrowerDoc']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['BorrowerDoc']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['BorrowerDoc'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['BorrowerDoc']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}

			/*---- Check Order reverse happened ----*/
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$data['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
				if($this->Common_Model->get_workflow_completed($OrderUID)) {
					$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
				}
			}
			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'BorrowerDoc Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('BorrowerDoc_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}
	
	/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
	/** Monday 09 March 2020 **/
	/** HOI PAYOFF WORKFLOW ADD **/
	function payoffComplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If PayOff workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['PayOff']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['PayOff']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['PayOff'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['PayOff']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}

			/*---- Check Order reverse happened ----*/
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$data['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
				if($this->Common_Model->get_workflow_completed($OrderUID)) {
					$data['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
				}
			}
			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'PayOff Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('HOI_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	function workupcomplete()
	{
		$OrderUID = $this->input->post('OrderUID');
		
		/*If Workup workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['Workup']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['Workup']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['Workup'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['Workup']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}
			$data['StatusUID'] = $this->config->item('keywords')['WorkupCompleted'];

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'Workup Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('Workup_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	function underwritercomplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If UnderWriter workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['UnderWriter']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['UnderWriter']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['UnderWriter'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['UnderWriter']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}
			$data['StatusUID'] = $this->config->item('keywords')['UnderwriterCompleted'];

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'UnderWriter Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('UnderWriter_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}




	function schedulingcomplete()
	{
		$OrderUID = $this->input->post('OrderUID');
		
		/*If Scheduling workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['Scheduling']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['Scheduling']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['Scheduling'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['Scheduling']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}
			$data['StatusUID'] = $this->config->item('keywords')['SchedulingCompleted'];

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'Scheduling Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('Scheduling_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	function closingcomplete()
	{
		$OrderUID = $this->input->post('OrderUID');


		
		/*If Closing workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $this->config->item('Workflows')['Closing']))
		{

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['Closing']]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $this->config->item('Workflows')['Closing'];
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['Closing']]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

			}
			$data['StatusUID'] = $this->config->item('keywords')['ClosedandBilled'];

			if ($update) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}
			if ($update) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'Closing Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$response['message'] = $this->lang->line('Closing_Complete');
				$response['validation_error'] = 0;
			} else {
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			}

		}  else {
			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('applicaton/json')->set_output(json_encode($response));
	}

	
	/**
		*@description Function to complete given workflow
		*
		* @param $post
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return JSON Status 
		* @since 13.3.2020 
		* @version Dynamic workflow 
		*
	*/ 
	function workflow_complete()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$Checked_Workflows = $this->input->post('Checked_Workflows');
		$modalcheckboxconfirmed = $this->input->post('skipdependent');
		$nbsmodalconfirmation = $this->input->post('nbsmodalconfirmation');
		$DependentWorkflows = [];
		$tOrderAssignments = [];
		
		if (empty($OrderUID) || empty($WorkflowModuleUID)) {
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Invalid Request"]))->_display();exit;
		}

		$IsWorkflowAvailable = $this->Common_Model->Is_given_workflow_available($OrderUID, $WorkflowModuleUID);

		if(!$IsWorkflowAvailable) {
			//enable workflow if not enabled
			$this->OrderComplete_Model->enable_torderworkflow($OrderUID,$WorkflowModuleUID);
		}

		$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		/*If workflow enable Begin*/
		if($this->Common_Model->Is_given_workflow_available($OrderUID, $WorkflowModuleUID))
		{

			// Calculator validation
			if (in_array($WorkflowModuleUID, $this->config->item('CalculatorEnabledWorkflows'))) {
				
				$ValidationStatus = $this->OrderComplete_Model->GetCalculatorData($OrderUID,$WorkflowModuleUID);

				if(!empty($ValidationStatus)) {
					$this->output->set_content_type('application/json');
					$this->output->set_output(json_encode(['validation_error'=>1,'message'=>$ValidationStatus]))->_display();exit;
				}
			}

			//NBS REQUIRED WORKFLOWS SELECTED
			if (empty($nbsmodalconfirmation)) {
				
				if(in_array($WorkflowModuleUID, $this->config->item('NBSREQUIRED_WORKFLOWS_TOCOMPLETE'))) {
					if($this->OrderComplete_Model->CheckNBSRequiredWorkflow($OrderUID)) {

						$this->output->set_content_type('application/json');
						$this->output->set_output(json_encode(['NBSRequiredConfirmation'=>1,'message'=>"NBS required Do you complete this workflow?"]))->_display();exit;
					}
				}
			}

			/**
			*Findings option mandatory 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Tuesday 01 September 2020.
			*/
			$checklistmandatoryfieldcheck = $this->OrderComplete_Model->checklistmandatoryfieldcheck($OrderUID,$WorkflowModuleUID, $OrderDetails);

			if(!empty($checklistmandatoryfieldcheck)) {
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Please select Findings for all checklist"]))->_display();exit;
			}

			//Checklist Issue -- check
			$issuevailable = $this->OrderComplete_Model->checklist_issue($OrderUID,$WorkflowModuleUID, $OrderDetails);

			if (isset($issuevailable->ProblemIdentifiedChecklists) && !empty($issuevailable->ProblemIdentifiedChecklists)) {
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Checklist Issue(s) Available <br><b>".$issuevailable->ProblemIdentifiedChecklists."</b>"]))->_display();exit;
			}

			/** @author Sathishkumar R <sathish.kumar@avanzegroup.com> **/
			/** @date Thursday 30 July 2020 **/
			/** @description Hardstop while completing workflow - mandatory to fill the fields **/
			$checklistmandatoryfield = $this->OrderComplete_Model->checklistmandatoryfield($OrderUID,$WorkflowModuleUID, $OrderDetails);

			foreach ($checklistmandatoryfield as $key => $value) {
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Please enter the document date for the checklist <br><span class='text-bold'>".$value->DocumentTypeName."</span>"]))->_display();exit;
			}
			
			//for modal confirm
			$dependentoptionalexistsarray = $this->OrderComplete_Model->is_dependentoptionalexists_result($OrderUID,$OrderDetails->CustomerUID,$WorkflowModuleUID);
			if(empty($modalcheckboxconfirmed) && !empty($dependentoptionalexistsarray)) {
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode(['validation_error'=>2,'message'=>"Optional Workflow Available",'DependentWorkflows'=>$dependentoptionalexistsarray]))->_display();exit;
			}

			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

			$res['CompleteDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
			$res['CompletedByUserUID'] = $this->loggedid;
			$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
			$res['WorkflowModuleUID'] = $WorkflowModuleUID;
			if (empty($is_assignment_row_available)) {				
				$res['OrderUID'] = $OrderUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
			}

			$tOrderAssignments[] = $res;

			$this->db->trans_begin();

			foreach ($tOrderAssignments as $key => $assignment) {

				if(!empty($this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$assignment['WorkflowModuleUID']]))){

					$update = $this->Common_Model->save('tOrderAssignments', $assignment, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);
				}	else {
					$update = $this->Common_Model->save('tOrderAssignments', $assignment);

				}

				/** @author Sathishkumar R <sathish.kumar@avanzegroup.com> **/
				/** @date Thursday 16 July 2020 **/
				/** @description Workflow completion details for generate the user activity log report and production summary report **/
				// Get Workflow Duration ID
				$WorkflowDurationDetails = $this->Common_Model->get_row('mWorkflowDurations', ['ClientUID'=>$this->parameters['DefaultClientUID'], 'WorkflowModuleUID'=> $WorkflowModuleUID, 'Active'=> STATUS_ONE]);

				if (!empty($WorkflowDurationDetails)) {
					//Insert Order Duration
					$OrderWorkflowDurationsData = [];
					$OrderWorkflowDurationsData['OrderUID'] = $OrderUID;
					$OrderWorkflowDurationsData['UserUID'] = $this->loggedid;
					$OrderWorkflowDurationsData['DurationUID'] = $WorkflowDurationDetails->DurationUID;
					$OrderWorkflowDurationsData['CompletedDateTime'] = Date('Y-m-d H:i:s', strtotime("now"));
					$this->OrderComplete_Model->InsertOrderWorkflowDurationsData($OrderWorkflowDurationsData);
				}				

			}

			// Cd inflow If Workup Associate is not an technical or support team Cd should be assigned to them
			if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {

				$tOrderAssignmentsData = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

				$CheckCDWorkflowIsEnabled = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['CD'], 'tOrderWorkflows.IsPresent' => STATUS_ONE]);
				
				if (!empty($tOrderAssignmentsData->AssignedToUserUID) && !in_array($tOrderAssignmentsData->AssignedToUserUID, $this->config->item('ReportSkippedUsers')) && !empty($CheckCDWorkflowIsEnabled)) {	

					$query = 0;

					$CD_tOrderAssignmentsData = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['CD']]);

					if (!empty($CD_tOrderAssignmentsData)) {
						
						if (empty($CD_tOrderAssignmentsData->AssignedToUserUID)) {
							
							$tOrderAssignmentsArray = array(
								'AssignedToUserUID' => $tOrderAssignmentsData->AssignedToUserUID,
								'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
								'AssignedByUserUID' => $this->loggedid,
								'WorkflowStatus' => $this->config->item('WorkflowStatus')['Assigned']
							);
							$query = $this->Common_Model->save('tOrderAssignments', $tOrderAssignmentsArray, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['CD']]);
						}
					} else {
					
						$tOrderAssignmentsArray = array(
							'OrderUID' => $OrderUID,
							'WorkflowModuleUID' => $this->config->item('Workflows')['CD'],
							'AssignedToUserUID' => $tOrderAssignmentsData->AssignedToUserUID,
							'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
							'AssignedByUserUID' => $this->loggedid,
							'WorkflowStatus' => $this->config->item('WorkflowStatus')['Assigned']
						);
						$query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);
					}		

					if ($query) {

						$workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID', $this->config->item('Workflows')['CD'])->get()->row();
						$assigneduser_row = $this->db->select('UserName')->from('mUsers')->where('UserUID', $tOrderAssignmentsData->AssignedToUserUID)->get()->row();
						
						$this->Common_Model->OrderLogsHistory($OrderUID, $workflow_names->WorkflowModuleName.' is assigned to '.$assigneduser_row->UserName, Date('Y-m-d H:i:s'));
					}
					
				}
			}

			//IF Workup completed GateKeeping to be enabled if not enabled
			$GateKeepingWorkflowModuleUID = $this->config->item('Workflows')['GateKeeping'];
			$GateKeepingavailable = $this->OrderComplete_Model->get_customer_workflows($this->parameters['DefaultClientUID'],[$GateKeepingWorkflowModuleUID]);
			
			if($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !empty($GateKeepingavailable)) {
				$tOrderWorkflowrowGateKeeping = $this->Common_Model->get_row('`tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$GateKeepingWorkflowModuleUID]);
				if(!empty($tOrderWorkflowrowGateKeeping)) {

					if( ($tOrderWorkflowrowGateKeeping->IsPresent == 0 || $tOrderWorkflowrowGateKeeping->IsAssign == 0)) {

						$tOrderWorkflows['IsPresent'] = STATUS_ONE;
						$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowGateKeeping->EntryDateTime) ? $tOrderWorkflowrowGateKeeping->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
						$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowGateKeeping->DueDateTime) ? $tOrderWorkflowrowGateKeeping->DueDateTime : calculate_workflowduedatetime($OrderUID,$GateKeepingWorkflowModuleUID);
						$tOrderWorkflows['IsAssign'] = STATUS_ONE;
						$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
						$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
						$tOrderWorkflows['ReversedByUserUID'] = NULL;
						$tOrderWorkflows['ReversedRemarks'] = NULL;
						$tOrderWorkflows['ReversedDateTime'] = NULL;
						$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $GateKeepingWorkflowModuleUID]);
						/*INSERT ORDER LOGS BEGIN*/
						$this->Common_Model->OrderLogsHistory($OrderUID,'GateKeeping - Queue Enabled',Date('Y-m-d H:i:s'), $this->config->item('Cron_UserUID'));
						/*INSERT ORDER LOGS END*/
					}

				}	else {

					$tOrderWorkflows['OrderUID'] = $OrderUID;
					$tOrderWorkflows['WorkflowModuleUID'] = $GateKeepingWorkflowModuleUID;
					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowGateKeeping->EntryDateTime) ? $tOrderWorkflowrowGateKeeping->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowGateKeeping->DueDateTime) ? $tOrderWorkflowrowGateKeeping->DueDateTime : calculate_workflowduedatetime($OrderUID,$GateKeepingWorkflowModuleUID);
					$tOrderWorkflows['IsAssign'] = STATUS_ONE;
					$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
					$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
					$tOrderWorkflows['ReversedByUserUID'] = NULL;
					$tOrderWorkflows['ReversedRemarks'] = NULL;
					$tOrderWorkflows['ReversedDateTime'] = NULL;
					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($OrderUID,'GateKeeping - Queue Enabled',Date('Y-m-d H:i:s'), $this->config->item('Cron_UserUID'));
					/*INSERT ORDER LOGS END*/

				}
			}

			$this->RaiseSubmissionsParkingWorkflow($OrderUID,$WorkflowModuleUID);

			//optional workflows
			$dependentexistsarray = $this->OrderComplete_Model->workflow_independent_workflow($OrderUID,$OrderDetails->CustomerUID,$WorkflowModuleUID);
			if(!empty($dependentexistsarray)) {

				$this->OrderComplete_Model->change_torderworkflowspresent($OrderUID,$OrderDetails->CustomerUID,$dependentexistsarray,$Checked_Workflows,$modalcheckboxconfirmed);
			}


			/*+++++++++++++++++To be Discussed+++++++++++*/
			/*To be Discussed*/
			/*---- Check Order reverse happened ----*/
			$Temp_StatusUID = $OrderDetails->Temp_StatusUID;
			$data['Temp_StatusUID'] = NULL;
			if (!empty($Temp_StatusUID)) {
				$data['StatusUID'] = $Temp_StatusUID;				
			}
			else{

				$mCustomerWorkflows = $this->Common_Model->get_row('mCustomerWorkflowModules', ['CustomerUID'=>$OrderDetails->CustomerUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);
				if (!empty($mCustomerWorkflows) && !empty($mCustomerWorkflows->StatusUID)) {
					$data['StatusUID'] = $mCustomerWorkflows->StatusUID;
				}
			}
			/*+++++++++++++++++To be Discussed+++++++++++*/

			//complete queues while completing workflow
			$tOrderQueues = $this->db->select('tOrderQueues.QueueUID,QueueName')->join('mQueues','mQueues.QueueUID = tOrderQueues.QueueUID')->where(['tOrderQueues.OrderUID'=>$OrderUID,'mQueues.WorkflowModuleUID'=>$WorkflowModuleUID, "tOrderQueues.QueueStatus" => "Pending"])->get('tOrderQueues')->result();
			if(!empty($tOrderQueues)) {
				foreach ($tOrderQueues as $tOrderQueue) {
					
					$queuedata = [];
					$queuedata['OrderUID'] = $OrderUID;
					$queuedata['QueueUID'] = $tOrderQueue->QueueUID;
					$queuedata['QueueStatus'] = "Completed";
					$queuedata['CompletedReasonUID'] = 3;
					$queuedata['CompletedRemarks'] = '';
					$queuedata['CompletedByUserUID'] = $this->loggedid;
					$queuedata['CompletedDateTime'] = date('Y-m-d H:i:s');
					$this->Common_Model->save('tOrderQueues', $queuedata,['OrderUID'=>$OrderUID,'QueueUID'=>$tOrderQueue->QueueUID, "QueueStatus" => "Pending"]);
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($OrderUID,$tOrderQueue->QueueName.' - Queue Completed',Date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/
				}
			}


			if ($update && !empty($data)) {

				$update = $this->Common_Model->save('tOrders', $data, 'OrderUID', $OrderUID);

				if ($data['StatusUID'] == $this->config->item('keywords')['ClosedandBilled']) {
					$this->Common_Model->save('tDocuments', ['DocumentStorage'=>''], ['OrderUID'=>$OrderUID]);
				}

			}

			/**
			*Complete Re-Work Queue is Enabled 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Friday 28 August 2020.
			*/
			$tOrderReWorkDetails = $this->Common_Model->get_row('tOrderReWork', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

			if (!empty($tOrderReWorkDetails)) {
				
				$tOrderReWorkData = [];
				$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ZERO;
				$tOrderReWorkData['CompletedByUserUID'] = $this->loggedid;
				$tOrderReWorkData['CompletedDateTime'] = date('Y-m-d H:i:s');

				$this->Common_Model->save('tOrderReWork', $tOrderReWorkData, ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

				// Add log
				$this->Common_Model->OrderLogsHistory($OrderUID, "ReWork Queue is Completed", Date('Y-m-d H:i:s'));
			}

			/**
			*Complete Pending Queue is Enabled 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Saturday 10 October 2020.
			*/
			$tOrderSubQueuesDetails = $this->Common_Model->get_row('tOrderSubQueues', ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'SubQueueStatus' => 'Pending']);

			if (!empty($tOrderSubQueuesDetails)) {
				
				$tOrderSubQueuesData = [];
				$tOrderSubQueuesData['OrderUID'] = $OrderUID;
				$tOrderSubQueuesData['WorkflowModuleUID'] = $WorkflowModuleUID;
				$tOrderSubQueuesData['SubQueueStatus'] = 'Completed';
				$tOrderSubQueuesData['CompletedByUserUID'] = $this->loggedid;
				$tOrderSubQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

				$this->Common_Model->save('tOrderSubQueues', $tOrderSubQueuesData, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'SubQueueStatus' => 'Pending']);

				// Add log
				$this->Common_Model->OrderLogsHistory($OrderUID, $this->lang->line('SubQueuePendingComplete_Success'), Date('Y-m-d H:i:s'));
			}

			$WorkflowDetailsConfig = $this->config->item('WorkflowDetails')[$WorkflowModuleUID];
			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID, $WorkflowDetailsConfig['logs'],Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$response['message'] = $this->lang->line('Complete_Failed');
				$response['validation_error'] = 1;
			} else {

				$this->db->trans_commit();
				$displayline = $this->lang->line($WorkflowDetailsConfig['line']);
				if($displayline == ""){
					$displayline = $this->lang->line('Completed');
				}
				$response['message'] = $displayline;
				$response['popup_message'] = $WorkflowDetailsConfig['message'];
				$response['redirect'] = $WorkflowDetailsConfig['screen'];
				$response['validation_error'] = 0;
			}


		}  else {

			$response['message'] = $this->lang->line('Invalid_Completion');
			$response['validation_error'] = 1;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}

	//doc chase

	public function RaiseDocchase()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');
		$WorkflowModuleUID = 0;
		$WorkflowModuleName = '';

		if(empty($Page)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		} else {
			$WorkflowModuleUID = isset($this->config->item('Order_WorkflowMenu')[$Page]) ? $this->config->item('Order_WorkflowMenu')[$Page]: NULL;
		}

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {
			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);		
			$data['OrderUID'] = $OrderUID;
			$data['WorkflowModuleUID'] = $WorkflowModuleUID;
			$data['ReasonUID'] = $Reason;
			$data['Remarks'] = $remarks;
			$data['RaisedByUserUID'] = $this->loggedid;
			$data['RaisedDateTime'] = date('Y-m-d H:i:s');
			$data['IsCleared'] = 0;
			//$data['AssignedToUserUID'] = $this->loggedid;
			//$data['AssignedDatetime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();
			/*$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID]);

			if (empty($is_assignment_row_available)) {				
				$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Assigned'];
				$res['OrderUID'] = $OrderUID;
				$res['WorkflowModuleUID'] = $WorkflowModuleUID;
				$res['AssignedToUserUID'] = $this->loggedid;
				$res['AssignedDatetime'] = date('Y-m-d H:i:s');
				$res['AssignedByUserUID'] = $this->loggedid;
				$this->db->insert('tOrderAssignments', $res);
			}*/

			$WorkflowArrays =$this->config->item('Workflows');
			$Module = array_search($WorkflowModuleUID, $WorkflowArrays);
			$insert = $this->Common_Model->save('tOrderDocChase', $data);
			if (!empty($remarks)) {			
				$data = array('OrderUID'=>$OrderUID,
					'Description'=>$remarks,
					'Module'=>$Module,
					'WorkflowUID'=>$WorkflowModuleUID,
					'CreatedByUserUID'=>$this->loggedid,
					'CreateDateTime'=>date("Y/m/d H:i:s")
				);
				$this->Common_Model->insertNotes($data);
			}

			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('DocChase_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Doc Chase Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('DocChase_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	public function ClearDocChase()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$Page = $this->input->post('Page');

		$WorkflowModuleUID = 0;
		$WorkflowModuleName = '';
		if(empty($Page)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		} else {
			$WorkflowModuleUID = isset($this->config->item('Order_WorkflowMenu')[$Page]) ? $this->config->item('Order_WorkflowMenu')[$Page]: NULL;
		}

				
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		// $this->form_validation->set_rules('ExceptionTypeUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {

			//check checklist not completed if need docchase
			$checklistneeddocchase = $this->Common_Model->checklistneeddocchase($OrderUID,$WorkflowModuleUID);
			if($checklistneeddocchase == 1) {

				$validation_data = array(
					'validation_error' => 1,
					'message' => $this->lang->line('Needdocchase_exists'),
					'html'=> ''
				);
				$this->output->set_content_type('application/json')->set_output(json_encode($validation_data))->_display();exit;
			}

			//check multiple docchase exists
				
			$multipledocchaseexists = $this->Common_Model->multipledocchaseexists($OrderUID);
			if(!empty($multipledocchaseexists)) {
				if(count($multipledocchaseexists) > 1) {

					$validation_data = array(
						'validation_error' => 2,
						'message' => $this->lang->line('Multiple_DocChase_Exists'),
						'html'=> $this->get_multiple_docchasedetails($multipledocchaseexists)
					);
					$this->output->set_content_type('application/json')
					->set_output(json_encode($validation_data))->_display();
					exit;
				}
			}

			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

			$data['IsCleared'] = 1;
			$data['ClearedReasonUID'] = $Reason;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');


			$this->db->trans_begin();

			$update = $this->Common_Model->save('tOrderDocChase', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsCleared'=>0]);
			$WorkflowArrays =$this->config->item('Workflows');
			$Module = array_search($WorkflowModuleUID, $WorkflowArrays);
			if (!empty($remarks)) {			

				$data = array('OrderUID'=>$OrderUID,
					'Description'=> "Doc Chase Cleared",
					'Module'=>$Module,
					'WorkflowUID'=>$WorkflowModuleUID,
					'CreatedByUserUID'=>$this->loggedid,
					'CreateDateTime'=>date("Y/m/d H:i:s")
				);
				$this->Common_Model->insertNotes($data);
			}
			$checklistdata = array(
				'IsChaseSend'=>'COMPLETED',
				'ModifiedUserUID'=>$this->loggedid,
				'ModifiedDateTime'=>date("Y-m-d H:i:s")
			);
			$this->db->set($checklistdata);
			$this->db->where(array('WorkflowUID'=>$WorkflowModuleUID,'OrderUID'=>$OrderUID,'IsChaseSend'=>'YES'));
			$this->db->update('tDocumentCheckList');

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('DocChase_Raise_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 1, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Doc Chase Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $Workflow->WorkflowModuleName.' '.$this->lang->line('DocChase_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	//multiple clear docchase
	public function ClearMultipleDocChase()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$Page = $this->input->post('Page');
		$PostWorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$WorkflowModuleUIDS = [];
		$WorkflowModuleNames = [];
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('', '');

		$this->form_validation->set_rules('OrderUID', '', 'required');
		// $this->form_validation->set_rules('ExceptionTypeUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');
		if(!empty($PostWorkflowModuleUID)) {
			$WorkflowModuleUIDS = explode(',', $PostWorkflowModuleUID);
		}

		$Msg = '';



		if ($this->form_validation->run() == true && !empty($WorkflowModuleUIDS)) {

			//check checklist not completed if need docchase
			$checklistneeddocchase = $this->Common_Model->checklistneeddocchase($OrderUID,$WorkflowModuleUIDS);
			if($checklistneeddocchase == 1) {

				$validation_data = array(
					'validation_error' => 1,
					'message' => $this->lang->line('Needdocchase_exists'),
					'html'=> ''
				);
				$this->output->set_content_type('application/json')->set_output(json_encode($validation_data))->_display();exit;
			}
			
			$this->db->trans_begin();

			foreach ($WorkflowModuleUIDS as $key => $WorkflowModuleUID) {

				$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);
				$data['IsCleared'] = 1;
				$data['ClearedReasonUID'] = $Reason;
				$data['ClearedByUserUID'] = $this->loggedid;
				$data['ClearedDateTime'] = date('Y-m-d H:i:s');

				$update = $this->Common_Model->save('tOrderDocChase', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsCleared'=>0]);
				$WorkflowArrays =$this->config->item('Workflows');
				$Module = array_search($WorkflowModuleUID, $WorkflowArrays);
				$notesdata = array('OrderUID'=>$OrderUID,
					'Description'=> "Doc Chase Cleared",
					'Module'=>$Module,
					'WorkflowUID'=>$WorkflowModuleUID,
					'CreatedByUserUID'=>$this->loggedid,
					'CreateDateTime'=>date("Y/m/d H:i:s"));
				$this->Common_Model->insertNotes($notesdata);

				$checklistdata = array(
					'IsChaseSend'=>'COMPLETED',
					'ModifiedUserUID'=>$this->loggedid,
					'ModifiedDateTime'=>date('Y-m-d H:i:s')
				);
				$this->db->set($checklistdata);
				$this->db->where(array('WorkflowUID'=>$WorkflowModuleUID,'OrderUID'=>$OrderUID,'IsChaseSend'=>'YES'));
				$this->db->update('tDocumentCheckList');

				$WorkflowModuleNames[] = $Workflow->WorkflowModuleName;
			}

			$imWorkflowModuleNames = implode(',', $WorkflowModuleNames);
			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('DocChase_Cleared_Failed');

				$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error' => 1, 'message' => $Msg)))->_display();exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$imWorkflowModuleNames.' - Doc Chase Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $imWorkflowModuleNames.' '.$this->lang->line('DocChase_Cleared');
				$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();exit;
			}
		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	//parking queue
	public function RaiseParking()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$Remainder = $this->input->post('Remainder');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');
		$WorkflowModuleUID = 0;

		if(empty($Page)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		} else {
			$WorkflowModuleUID = isset($this->config->item('Order_WorkflowMenu')[$Page]) ? $this->config->item('Order_WorkflowMenu')[$Page]: NULL;
		}

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {
			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

			$data['OrderUID'] = $OrderUID;
			$data['WorkflowModuleUID'] = $WorkflowModuleUID;
			$data['ReasonUID'] = $Reason;
			$data['Remainder'] = date('Y-m-d H:i:s',strtotime($Remainder));
			$data['Remarks'] = $remarks;
			$data['RaisedByUserUID'] = $this->loggedid;
			$data['RaisedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();


			if($WorkflowModuleUID != $this->config->item('Workflows')['DocChase']) {

				$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID]);

				if (empty($is_assignment_row_available)) {				
					$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Assigned'];
					$res['OrderUID'] = $OrderUID;
					$res['WorkflowModuleUID'] = $WorkflowModuleUID;
					$res['AssignedToUserUID'] = $this->loggedid;
					$res['AssignedDatetime'] = date('Y-m-d H:i:s');
					$res['AssignedByUserUID'] = $this->loggedid;
					$this->db->insert('tOrderAssignments', $res);
				}
			}


			$is_parking_row_available = $this->Common_Model->get_row('tOrderParking', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID,'IsCleared'=>0]);

			if (empty($is_parking_row_available)) {					
				$data['OrderUID'] = $OrderUID;
				$data['WorkflowModuleUID'] = $WorkflowModuleUID;
				$data['ReasonUID'] = '';
				$data['Remainder'] = date('Y-m-d H:i:s',strtotime($Remainder));
				$data['Remarks'] = '';
				$data['RaisedByUserUID'] = $this->loggedid;
				$data['RaisedDateTime'] = date('Y-m-d H:i:s');
				$this->Common_Model->save('tOrderParking', $data);
			}


			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Parking_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Parking Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Parking_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	public function ClearParking()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');

		$WorkflowModuleUID = 0;

		if(empty($Page)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		} else {
			$WorkflowModuleUID = isset($this->config->item('Order_WorkflowMenu')[$Page]) ? $this->config->item('Order_WorkflowMenu')[$Page]: NULL;
		}


				
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {
			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

			$data['IsCleared'] = 1;
			$data['ReasonUID'] = $Reason;
			$data['Remarks'] = $remarks;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');


			$this->db->trans_begin();

			$update = $this->Common_Model->save('tOrderParking', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsCleared'=>0]);

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Parking_Raise_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Parking Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Parking_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	//withdrawal queue
	public function RaiseWithdrawal()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$data['IsCleared'] = 0;
			$data['OrderUID'] = $OrderUID;
			$data['ReasonUID'] = $Reason;
			$data['Remarks'] = $remarks;
			$data['RaisedByUserUID'] = $this->loggedid;
			$data['RaisedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();

			//clear docchase for the workflow 
			$docchasedata['IsCleared'] = 1;
			$docchasedata['ReasonUID'] = 3;
			$docchasedata['Remarks'] = 'Cleared while approving withdrawal';
			$docchasedata['ClearedByUserUID'] = $this->loggedid;
			$docchasedata['ClearedDateTime'] = date('Y-m-d H:i:s');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('IsCleared',0);
			$this->db->update('tOrderDocChase', $docchasedata);

			//clear parking for the workflow 
			$parkingdata['IsCleared'] = 1;
			$parkingdata['ReasonUID'] = 3;
			$parkingdata['Remarks'] = 'Cleared while approving withdrawal';
			$parkingdata['ClearedByUserUID'] = $this->loggedid;
			$parkingdata['ClearedDateTime'] = date('Y-m-d H:i:s');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('IsCleared',0);
			$this->db->update('tOrderParking', $parkingdata);

			//clear escalation for the workflow 
			$escalationdata['IsCleared'] = 1;
			$escalationdata['ReasonUID'] = 3;
			$escalationdata['Remarks'] = 'Cleared while approving withdrawal';
			$escalationdata['ClearedByUserUID'] = $this->loggedid;
			$escalationdata['ClearedDateTime'] = date('Y-m-d H:i:s');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('IsCleared',0);
			$this->db->update('tOrderEsclation', $escalationdata);


			$insert = $this->Common_Model->save('tOrderWithdrawal', $data);

			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Withdrawal_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'Withdrawal Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Withdrawal_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	public function ClearWithdrawal()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');

		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$data['IsCleared'] = 1;
			$data['ClearedReasonUID'] = $Reason;
			$data['ClearedRemarks'] = $remarks;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');


			$this->db->trans_begin();
			$update = $this->Common_Model->save('tOrderWithdrawal', $data,array('OrderUID' => $OrderUID,'IsCleared'=>0));

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Withdrawal_Raise_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'Withdrawal Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Withdrawal_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}


//esclation queue
	public function RaiseEsclation()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');
		$RecipientEmail = $this->input->post('RecipientEmail');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');
		$WorkflowModuleUID = 0;
		$WorkflowModuleName = '';

		if(empty($Page)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		} else {
			$WorkflowModuleUID = isset($this->config->item('Order_WorkflowMenu')[$Page]) ? $this->config->item('Order_WorkflowMenu')[$Page]: NULL;
		}

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {
			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

			$data['OrderUID'] = $OrderUID;
			$data['WorkflowModuleUID'] = $WorkflowModuleUID;
			$data['ReasonUID'] = $Reason;
			$data['Remarks'] = $remarks;
			$data['RaisedByUserUID'] = $this->loggedid;
			$data['RaisedDateTime'] = date('Y-m-d H:i:s');

			/*$update['IsCleared'] = 1;
			// $update['ReasonUID'] = $Reason;
			// $update['Remarks'] = $remarks;
			$update['ClearedByUserUID'] = $this->loggedid;
			$update['ClearedDateTime'] = date('Y-m-d H:i:s');*/

			$this->db->trans_begin();
			
			//$update = $this->Common_Model->save('tOrderDocChase', $update,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID]);
			$insert = $this->Common_Model->save('tOrderEsclation', $data);
			$WorkflowArrays =$this->config->item('Workflows');
			$Module = array_search($WorkflowModuleUID, $WorkflowArrays);
			$data = array('OrderUID'=>$OrderUID,
				'Description'=>$remarks,
				'Module'=>$Module,
				'WorkflowUID'=>$WorkflowModuleUID,
				'CreatedByUserUID'=>$this->loggedid,
				'CreateDateTime'=>date("Y/m/d H:i:s")
			);
			$this->Common_Model->insertNotes($data);
			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Esclation_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Esclation Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/

				/*Raise Esclation Mail Begin*/
				if (!empty($RecipientEmail)) {					
					$this->RaiseEsclationMail($OrderUID, $RecipientEmail);
				}
				/*Raise Esclation Mail End*/

				$this->db->trans_commit();
				$Msg = $this->lang->line('Esclation_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	public function ClearEsclation()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');

		$WorkflowModuleUID = 0;

		$WorkflowModuleName = '';

		if(empty($Page)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		} else {
			$WorkflowModuleUID = isset($this->config->item('Order_WorkflowMenu')[$Page]) ? $this->config->item('Order_WorkflowMenu')[$Page]: NULL;
		}


				
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		// $this->form_validation->set_rules('ExceptionTypeUID', '', 'required');
		$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {
			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

			$data['IsCleared'] = 1;
			$data['ReasonUID'] = $Reason;
			$data['Remarks'] = $remarks;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');


			$this->db->trans_begin();

			$update = $this->Common_Model->save('tOrderEsclation', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID]);
			$WorkflowArrays =$this->config->item('Workflows');
			$Module = array_search($WorkflowModuleUID, $WorkflowArrays);
			$data = array('OrderUID'=>$OrderUID,
				'Description'=>$remarks,
				'Module'=>$Module,
				'WorkflowUID'=>$WorkflowModuleUID,
				'CreatedByUserUID'=>$this->loggedid,
				'CreateDateTime'=>date("Y/m/d H:i:s")
			);
			$this->Common_Model->insertNotes($data);

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Esclation_Clear_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Esclation_Clear Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Esclation_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	function get_multiple_docchasedetails($DocChase_Details)
	{
		$html = '';
		foreach ($DocChase_Details as $key => $value) {
			
			$redirect = 'Ordersummary';

			if (!empty($value->WorkflowModuleUID)) {
				$redirect = !empty(array_search($value->WorkflowModuleUID, $this->config->item('Order_WorkflowMenu'))) ? array_search($value->WorkflowModuleUID, $this->config->item('Order_WorkflowMenu')) : 'Ordersummary';
			}

			$RaisedDateTime = ($value->RaisedDateTime != '0000-00-00 00:00:00' && $value->RaisedDateTime != '') ? date('m/d/Y',strtotime($value->RaisedDateTime)) : '-';
			$html .= '<tr>
			<td><a class="btn btn-scondary btn-xs" href="'.base_url($redirect.'/index/'.$value->OrderUID).'" class="ajaxload" target="_blank">'.$value->WorkflowModuleName.'</a></td>
			<td><a style="background-color:#4E8657;font-size:11px" class="btn  btn-xs ajaxload" href="'.base_url($redirect.'/index/'.$value->OrderUID).'" target="_blank">'.$value->QuestionCount.'</a></td>
			<td>'.$value->RaisedReasonName.'</td>
			<td>'.$value->Remarks.'</td>
			<td>'.$value->RaisedUserName.'</td>                    
			<td>'.$RaisedDateTime.'</td>                    
			<td>
			<div class="form-check">
			<label class="form-check-label">
			<input class="form-check-input WorkflowModuleUIDClearChase_box" type="checkbox" id="WorkflowModuleUIDClearChase_box'.$value->WorkflowModuleUID.'" data-WorkflowModuleUID="'.$value->WorkflowModuleUID.'" checked> 
			<span class="form-check-sign">
			<span class="check"></span>
			</span>
			</label>
			</div>
			</td>                    
			</tr>';
		}
		return $html;
	}


	//parking queue
	public function RaiseExceptionQueue()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$QueueUID = $this->input->post('QueueUID');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');

		if(empty($QueueUID) || empty($OrderUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('Invalid Request'))))->_display();exit;
		}

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');		

		// Get Reason
		$mQueueReasons = $this->Common_Model->get_mqueuesmreasons($QueueUID);

		if (!empty($mQueueReasons)) {
			
			$this->form_validation->set_rules('Reason[]', '', 'required');	
		}

		if (!empty($Reason)) {
			
			// $mReasons = $this->Common_Model->get_row('mReasons', ['ReasonUID'=>$Reason]);
			$mReasons = $this->db->select('*')->from('mReasons')->where_in('ReasonUID',$Reason)->get()->result_array();

			$ReasonNameArr = array_column($mReasons, "ReasonName");

			if (in_array("Others", $ReasonNameArr)) {
				
				$this->form_validation->set_rules('remarks', '', 'required');
			}
		}		

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$mQueues->WorkflowModuleUID]);
			$WorkflowModuleUID = $Workflow->WorkflowModuleUID;


			$this->db->trans_begin();


			if($WorkflowModuleUID != $this->config->item('Workflows')['DocChase'] && !empty($WorkflowModuleUID)) {

				$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID]);

				if (empty($is_assignment_row_available)) {				
					$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Assigned'];
					$res['OrderUID'] = $OrderUID;
					$res['WorkflowModuleUID'] = $WorkflowModuleUID;
					$res['AssignedToUserUID'] = $this->loggedid;
					$res['AssignedDatetime'] = date('Y-m-d H:i:s');
					$res['AssignedByUserUID'] = $this->loggedid;
					$this->db->insert('tOrderAssignments', $res);
				}
			}	

			/**
			* 
			*@author SathishKumar <sathish.kumar@avanzegroup.com>
			*@since Friday 18 September 2020.
			*/
			if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && in_array($QueueUID, $this->config->item('WorkupSubQueueComplete'))) {
				
				$this->Common_Model->UpdateWorkupQueueCompleteLogic($OrderUID, $WorkflowModuleUID, $QueueUID);
			}		

			//Check other exception queue is already raised
			$PendingQueues = $this->Common_Model->getPendingQueueButtons($OrderUID, $WorkflowModuleUID);

			if (!empty($PendingQueues)) {
					
				// Calculator validation
				if (in_array($WorkflowModuleUID, $this->config->item('CalculatorEnabledWorkflows'))) {
					
					$ValidationStatus = $this->OrderComplete_Model->GetCalculatorData($OrderUID,$WorkflowModuleUID);

					if(!empty($ValidationStatus)) {
						$this->output->set_content_type('application/json');
						$this->output->set_output(json_encode(['validation_error'=>2,'message'=>$ValidationStatus]))->_display();exit;
					}
				}
			}

			foreach ($PendingQueues as $PendingQueue) {
			    
				$data['OrderUID'] = $OrderUID;
				$data['QueueUID'] = $PendingQueue->QueueUID;
				$data['QueueStatus'] = "Completed";
				$data['CompletedReasonUID'] = '';
				$data['CompletedRemarks'] = '';
				$data['CompletedByUserUID'] = $this->loggedid;
				$data['CompletedDateTime'] = date('Y-m-d H:i:s');

				$update = $this->Common_Model->save('tOrderQueues', $data,['OrderUID'=>$OrderUID,'QueueUID'=>$PendingQueue->QueueUID, "QueueStatus" => "Pending"]);

				if ($update) {
					
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($OrderUID,$PendingQueue->QueueName.' - Queue Completed',Date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/
				}				
					
			}


			$is_queue_row_available = $this->Common_Model->get_row('tOrderQueues', ['OrderUID'=>$OrderUID, 'QueueUID'=> $QueueUID,'QueueStatus'=>"Pending"]);

			if (empty($is_queue_row_available)) {
				$data['OrderUID'] = $OrderUID;
				$data['QueueUID'] = $QueueUID;
				$data['QueueStatus'] = "Pending";
				$data['RaisedReasonUID'] = implode(",", $Reason);
				$data['RaisedRemarks'] = $remarks;
				$data['RaisedByUserUID'] = $this->loggedid;
				$data['RaisedDateTime'] = date('Y-m-d H:i:s');
				$this->Common_Model->save('tOrderQueues', $data);
			}

			//clear followup
			$followup_row_available = $this->Common_Model->get('tOrderFollowUp', ['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsCleared'=>0]);
			if(!empty($followup_row_available)) {
				foreach ($followup_row_available as $followup_row) {
					$followpdata= [];
					$followpdata['IsCleared'] = 1;
					$followpdata['ClearedReasonUID'] = 3;
					$followpdata['ClearedRemarks'] = sprintf($this->lang->line('Clear_Followup_Init'), $Workflow->WorkflowModuleName, $mQueues->QueueName);
					$followpdata['ClearedByUserUID'] = $this->loggedid;
					$followpdata['ClearedDateTime'] = date('Y-m-d H:i:s');
					$this->db->where(['FollowUpUID'=>$followup_row->FollowUpUID]);
					$this->db->update('tOrderFollowUp', $followpdata);
					$this->Common_Model->OrderLogsHistory($OrderUID,$followpdata['ClearedRemarks'],Date('Y-m-d H:i:s'));
				}
			}



			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = sprintf($this->lang->line('Exception_Queue_Raise_Failed'), $mQueues->QueueName);
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$mQueues->QueueName.' - Queue Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/

				// Raise parking				
				$this->RaiseSubmissionsParkingWorkflow($OrderUID,$WorkflowModuleUID);

				$this->db->trans_commit();
				$Msg = sprintf($this->lang->line('Exception_Queue_Raised'), $mQueues->QueueName);

				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
				'ExceptionRaiseReason' => form_error('Reason[]'),
				'remarks' => form_error('remarks'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	public function ClearExceptionQueue()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$QueueUID = $this->input->post('QueueUID');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');
		$CompleteWorkflow = $this->input->post('CompleteWorkflow');
		$nbsmodalconfirmation = $this->input->post('nbsmodalconfirmation');

		if(empty($QueueUID) || empty($OrderUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('Invalid Request'))))->_display();exit;
		}



				
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');

		// Get Reason
		$mQueueReasons = $this->Common_Model->get_mqueuesmreasons($QueueUID);

		if (!empty($mQueueReasons)) {
			
			$this->form_validation->set_rules('Reason[]', '', 'required');	
		}

		if (!empty($Reason)) {
			
			// $mReasons = $this->Common_Model->get_row('mReasons', ['ReasonUID'=>$Reason]);
			$mReasons = $this->db->select('*')->from('mReasons')->where_in('ReasonUID',$Reason)->get()->result_array();

			$ReasonNameArr = array_column($mReasons, "ReasonName");

			if (in_array("Others", $ReasonNameArr)) {
				
				$this->form_validation->set_rules('remarks', '', 'required');
			}
		}

		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true ) {

			$mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$mQueues->WorkflowModuleUID]);
			$WorkflowModuleUID = $Workflow->WorkflowModuleUID;

			// Get Order Details
			$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

			// Calculator validation
			if (in_array($WorkflowModuleUID, $this->config->item('CalculatorEnabledWorkflows'))) {
				
				$ValidationStatus = $this->OrderComplete_Model->GetCalculatorData($OrderUID,$WorkflowModuleUID);

				if(!empty($ValidationStatus)) {
					$this->output->set_content_type('application/json');
					$this->output->set_output(json_encode(['validation_error'=>2,'message'=>$ValidationStatus]))->_display();exit;
				}
			}

			if($CompleteWorkflow == true && $WorkflowModuleUID != $this->config->item('Workflows')['DocChase'] && !empty($WorkflowModuleUID)) {

				//NBS REQUIRED WORKFLOWS SELECTED
				if (empty($nbsmodalconfirmation)) {
					
					if(in_array($WorkflowModuleUID, $this->config->item('NBSREQUIRED_WORKFLOWS_TOCOMPLETE'))) {
						if($this->OrderComplete_Model->CheckNBSRequiredWorkflow($OrderUID)) {

							$this->output->set_content_type('application/json');
							$this->output->set_output(json_encode(['NBSRequiredConfirmation'=>1,'message'=>"NBS required Do you complete this workflow?"]))->_display();exit;
						}
					}
				}

				/**
				*Findings option mandatory 
				*@author SathishKumar <sathish.kumar@avanzegroup.com>
				*@since Tuesday 01 September 2020.
				*/
				$checklistmandatoryfieldcheck = $this->OrderComplete_Model->checklistmandatoryfieldcheck($OrderUID,$WorkflowModuleUID, $OrderDetails);

				if(!empty($checklistmandatoryfieldcheck)) {
					$this->output->set_content_type('application/json');
					$this->output->set_output(json_encode(['validation_error'=>2,'message'=>"Please select Findings for all checklist"]))->_display();exit;
				}

				//Checklist Issue -- check
				$issuevailable = $this->OrderComplete_Model->checklist_issue($OrderUID,$WorkflowModuleUID, $OrderDetails);

				if (isset($issuevailable->ProblemIdentifiedChecklists) && !empty($issuevailable->ProblemIdentifiedChecklists)) {
					$this->output->set_content_type('application/json');
					$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Checklist Issue(s) Available <br><b>".$issuevailable->ProblemIdentifiedChecklists."</b>"]))->_display();exit;
				}

				/** @author Sathishkumar R <sathish.kumar@avanzegroup.com> **/
				/** @date Thursday 30 July 2020 **/
				/** @description Hardstop while completing workflow - mandatory to fill the fields **/
				$checklistmandatoryfield = $this->OrderComplete_Model->checklistmandatoryfield($OrderUID,$WorkflowModuleUID, $OrderDetails);

				foreach ($checklistmandatoryfield as $key => $value) {
					$this->output->set_content_type('application/json');
					$this->output->set_output(json_encode(['validation_error'=>2,'message'=>"Please enter the document date for the checklist <br><b>".$value->DocumentTypeName."</b>"]))->_display();exit;
				}

			}

			$this->db->trans_begin();

			if($CompleteWorkflow == true && $WorkflowModuleUID != $this->config->item('Workflows')['DocChase'] && !empty($WorkflowModuleUID)) {	

				$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID]);

				if (empty($is_assignment_row_available)) {				
					$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
					$res['OrderUID'] = $OrderUID;
					$res['WorkflowModuleUID'] = $WorkflowModuleUID;
					$res['AssignedToUserUID'] = $this->loggedid;
					$res['AssignedDatetime'] = date('Y-m-d H:i:s');
					$res['AssignedByUserUID'] = $this->loggedid;
					$res['CompletedByUserUID'] = $this->loggedid;
					$res['CompleteDateTime'] = date('Y-m-d H:i:s');
					$this->db->insert('tOrderAssignments', $res);
				}
				else{

					$filter['OrderUID'] = $OrderUID;
					$filter['WorkflowModuleUID'] = $WorkflowModuleUID;
					
					$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Completed'];
					$res['CompletedByUserUID'] = $this->loggedid;
					$res['CompleteDateTime'] = date('Y-m-d H:i:s');
					$this->db->where($filter);
					$this->db->update('tOrderAssignments', $res);


				}

				// Cd inflow If Workup Associate is not an technical or support team Cd should be assigned to them
				if ($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {

					$tOrderAssignmentsData = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

					$CheckCDWorkflowIsEnabled = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['CD'], 'tOrderWorkflows.IsPresent' => STATUS_ONE]);
					
					if (!empty($tOrderAssignmentsData->AssignedToUserUID) && !in_array($tOrderAssignmentsData->AssignedToUserUID, $this->config->item('ReportSkippedUsers')) && !empty($CheckCDWorkflowIsEnabled)) {	

						$query = 0;

						$CD_tOrderAssignmentsData = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('Workflows')['CD']]);

						if (!empty($CD_tOrderAssignmentsData)) {
							
							if (empty($CD_tOrderAssignmentsData->AssignedToUserUID)) {
								
								$tOrderAssignmentsArray = array(
									'AssignedToUserUID' => $tOrderAssignmentsData->AssignedToUserUID,
									'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
									'AssignedByUserUID' => $this->loggedid,
									'WorkflowStatus' => $this->config->item('WorkflowStatus')['Assigned']
								);
								$query = $this->Common_Model->save('tOrderAssignments', $tOrderAssignmentsArray, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $this->config->item('Workflows')['CD']]);
							}
						} else {
						
							$tOrderAssignmentsArray = array(
								'OrderUID' => $OrderUID,
								'WorkflowModuleUID' => $this->config->item('Workflows')['CD'],
								'AssignedToUserUID' => $tOrderAssignmentsData->AssignedToUserUID,
								'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
								'AssignedByUserUID' => $this->loggedid,
								'WorkflowStatus' => $this->config->item('WorkflowStatus')['Assigned']
							);
							$query = $this->db->insert('tOrderAssignments', $tOrderAssignmentsArray);
						}		

						if ($query) {

							$workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID', $this->config->item('Workflows')['CD'])->get()->row();
							$assigneduser_row = $this->db->select('UserName')->from('mUsers')->where('UserUID', $tOrderAssignmentsData->AssignedToUserUID)->get()->row();
							
							$this->Common_Model->OrderLogsHistory($OrderUID, $workflow_names->WorkflowModuleName.' is assigned to '.$assigneduser_row->UserName, Date('Y-m-d H:i:s'));
						}
						
					}
				}

				$this->RaiseSubmissionsParkingWorkflow($OrderUID,$WorkflowModuleUID);

				//optional workflows
				$dependentexistsarray = $this->OrderComplete_Model->workflow_independent_workflow($OrderUID,$this->parameters['DefaultClientUID'],$WorkflowModuleUID);
				if(!empty($dependentexistsarray)) {

					$modalcheckboxconfirmed = false;
					$Checked_Workflows = [];
					$this->OrderComplete_Model->change_torderworkflowspresent($OrderUID,$this->parameters['DefaultClientUID'],$dependentexistsarray,$Checked_Workflows,$modalcheckboxconfirmed);
				}

				/**
				*Complete Pending Queue is Enabled 
				*@author SathishKumar <sathish.kumar@avanzegroup.com>
				*@since Saturday 10 October 2020.
				*/
				$tOrderSubQueuesDetails = $this->Common_Model->get_row('tOrderSubQueues', ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'SubQueueStatus' => 'Pending']);

				if (!empty($tOrderSubQueuesDetails)) {
					
					$tOrderSubQueuesData = [];
					$tOrderSubQueuesData['OrderUID'] = $OrderUID;
					$tOrderSubQueuesData['WorkflowModuleUID'] = $WorkflowModuleUID;
					$tOrderSubQueuesData['SubQueueStatus'] = 'Completed';
					$tOrderSubQueuesData['CompletedByUserUID'] = $this->loggedid;
					$tOrderSubQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

					$this->Common_Model->save('tOrderSubQueues', $tOrderSubQueuesData, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'SubQueueStatus' => 'Pending']);

					// Add log
					$this->Common_Model->OrderLogsHistory($OrderUID, $this->lang->line('SubQueuePendingComplete_Success'), Date('Y-m-d H:i:s'));
				}

				$this->RaiseSubmissionsParkingQueue($OrderUID,$QueueUID);
				
				/**
				*Complete Re-Work Queue is Enabled 
				*@author SathishKumar <sathish.kumar@avanzegroup.com>
				*@since Friday 28 August 2020.
				*/
				$tOrderReWorkDetails = $this->Common_Model->get_row('tOrderReWork', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

				if (!empty($tOrderReWorkDetails)) {
					
					$tOrderReWorkData = [];
					$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ZERO;
					$tOrderReWorkData['CompletedByUserUID'] = $this->loggedid;
					$tOrderReWorkData['CompletedDateTime'] = date('Y-m-d H:i:s');

					$this->Common_Model->save('tOrderReWork', $tOrderReWorkData, ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

					// Add log
					$this->Common_Model->OrderLogsHistory($OrderUID, "ReWork Queue is Completed", Date('Y-m-d H:i:s'));
				}

			}


			$data['OrderUID'] = $OrderUID;
			$data['QueueUID'] = $QueueUID;
			$data['QueueStatus'] = "Completed";
			$data['CompletedReasonUID'] = implode(",", $Reason);
			$data['CompletedRemarks'] = $remarks;
			$data['CompletedByUserUID'] = $this->loggedid;
			$data['CompletedDateTime'] = date('Y-m-d H:i:s');
				$update = $this->Common_Model->save('tOrderQueues', $data,['OrderUID'=>$OrderUID,'QueueUID'=>$QueueUID, "QueueStatus" => "Pending"]);

			//clear followup
			$followupdata['IsCleared'] = 1;
			$followupdata['ClearedReasonUID'] = implode(",", $Reason);
			$followupdata['ClearedRemarks'] = $remarks;
			$followupdata['ClearedByUserUID'] = $this->loggedid;
			$followupdata['ClearedDateTime'] = date('Y-m-d H:i:s');
			$update = $this->Common_Model->save('tOrderFollowUp', $followupdata,['OrderUID'=>$OrderUID,'QueueUID'=>$QueueUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsCleared'=>0]);

			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = sprintf($this->lang->line('Exception_Queue_Clear_Failed'), $mQueues->QueueName);
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$mQueues->QueueName.' - Queue Completed',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				if ($CompleteWorkflow == true) 
				{
					$WorkflowCompleteName = "And " . $Workflow->WorkflowModuleName . " Completed";
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Completed',Date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/
				}
				else 
				{
					$WorkflowCompleteName = "";
				}
				$Msg = sprintf($this->lang->line('Exception_Queue_Cleared'), $mQueues->QueueName, $WorkflowCompleteName);

				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				// 'Reason' => form_error('Reason'),
				'ExcecptionQueueClearReason' => form_error('Reason[]'),
				'ExcecptionQueueClearRemarks' => form_error('remarks'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	/**
	*Function Raise DocChase Parking
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 28 April 2020
	*/
	public function RaiseDocChaseParking()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$Remainder = $this->input->post('Remainder');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$data['OrderUID'] = $OrderUID;
			$data['WorkflowModuleUID'] = 0;
			$data['ReasonUID'] = $Reason;
			$data['Remainder'] = date('Y-m-d H:i:s',strtotime($Remainder));
			$data['Remarks'] = $remarks;
			$data['RaisedByUserUID'] = $this->loggedid;
			$data['RaisedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();

			$is_parking_row_available = $this->Common_Model->get_row('tOrderParking', ['OrderUID'=>$OrderUID, 'IsDocChaseParking'=>1,'IsCleared'=>0]);

			$data['OrderUID'] = $OrderUID;
			$data['WorkflowModuleUID'] = 0;
			$data['IsDocChaseParking'] = 1;
			$data['ReasonUID'] = '';
			$data['Remainder'] = date('Y-m-d H:i:s',strtotime($Remainder));
			$data['Remarks'] = '';
			$data['RaisedByUserUID'] = $this->loggedid;
			$data['RaisedDateTime'] = date('Y-m-d H:i:s');

			if (empty($is_parking_row_available)) {					
				$this->Common_Model->save('tOrderParking', $data);
			} else {
				$this->db->where('tOrderParking.OrderUID',$OrderUID);
				$this->db->where('tOrderParking.IsDocChaseParking',1);
				$this->db->where('tOrderParking.IsCleared',0);
				$this->db->update('tOrderParking', $res);
			}


			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Parking_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'DocChase - Parking Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Parking_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	/**
	*Function Clear DocChase Parking
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 28 April 2020
	*/
	public function ClearDocChaseParking()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');

				
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$data['IsCleared'] = 1;
			$data['ReasonUID'] = $Reason;
			$data['Remarks'] = $remarks;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');


			$this->db->trans_begin();

			$update = $this->Common_Model->save('tOrderParking', $data,['OrderUID'=>$OrderUID,'IsDocChaseParking'=>1,'IsCleared'=>0]);

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Parking_Raise_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'DocChase - Parking Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Parking_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	/**
	*Function Raise DocChase Escalation
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 28 April 2020
	*/
	public function RaiseDocChaseEsclation()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');
		$RecipientEmail = $this->input->post('RecipientEmail');

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('Reason', '', 'required');
		$this->form_validation->set_rules('RecipientEmail', '', 'required|valid_email');
		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$escalationdata['OrderUID'] = $OrderUID;
			$escalationdata['WorkflowModuleUID'] = 0;
			$escalationdata['ReasonUID'] = $Reason;
			$escalationdata['Remarks'] = $remarks;
			$escalationdata['IsDocChaseEscalation'] = 1;
			$escalationdata['RaisedByUserUID'] = $this->loggedid;
			$escalationdata['RaisedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();
			
			$insert = $this->Common_Model->save('tOrderEsclation', $escalationdata);
			$WorkflowArrays =$this->config->item('Workflows');
			$data = array('OrderUID'=>$OrderUID,
				'Description'=>$remarks,
				'Module'=>'DocChase',
				'WorkflowUID'=>NULL,
				'CreatedByUserUID'=>$this->loggedid,
				'CreateDateTime'=>date("Y/m/d H:i:s")
			);
			$this->Common_Model->insertNotes($data);
			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Esclation_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'DocChase - Esclation Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/

				/*Raise Esclation Mail Begin*/
				if (!empty($RecipientEmail)) {					
					$this->RaiseEsclationMail($OrderUID, $RecipientEmail);
				}
				/*Raise Esclation Mail End*/
				
				$this->db->trans_commit();
				$Msg = $this->lang->line('Esclation_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
				'RecipientEmail' => form_error('RecipientEmail'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	public function RaiseEsclationMail($OrderUID, $RecipientEmail) {
    	$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
    	$mOrganization = $this->Common_Model->get_row('mOrganization', ['OrganizationUID'=>1]);
    	$memailtemplate = $this->Common_Model->get_row('mEmailTemplate', ['EmailTemplateUID'=>2]);

		$subject = str_replace('OrderNumber', $tOrders->OrderNumber, $memailtemplate->Subject);
		$subject = str_replace('LoanNumber', $tOrders->LoanNumber, $subject);
		$subject = str_replace('RaisedBy', $this->session->userdata('UserName'), $subject);

		$content = str_replace('OrderNumber', $tOrders->OrderNumber, $memailtemplate->Body);
		$content = str_replace('LoanNumber', $tOrders->LoanNumber, $content);
		$content = str_replace('RaisedBy', $this->session->userdata('UserName'), $content);

    	if(!empty($mOrganization) && !empty($memailtemplate) ){
    		$this->load->library('email');

			$config['protocol'] = "smtp";
		    // does not have to be gmail
		    $config['smtp_host'] = $mOrganization->SMTPHost; 
		    $config['smtp_port'] = $mOrganization->SMTPPort;
		    $config['smtp_user'] = $mOrganization->SMTPUserName;
		    $config['smtp_pass'] = $mOrganization->SMTPPassword;
		    $config['charset'] = 'utf-8';
		    $config['mailtype'] = 'html';
		    $config['newline'] = "\r\n";
		    $config['wordwrap'] = TRUE;
		    $this->email->initialize($config);

			$this->email->from($mOrganization->SMTPUserName);

     		if (!empty($memailtemplate->ToMailID)) {
				$this->email->to($RecipientEmail.','.$memailtemplate->ToMailID);
			} else {
     			$this->email->to($RecipientEmail);
			}

			if (!empty($memailtemplate->BCCMailID)) {
				$this->email->bcc($memailtemplate->BCCMailID);
			} 

     		$this->email->subject($subject); 
     		$this->email->message($content); 
     		//Send mail 
     		if($this->email->send()){
     			$status =  "Success";	         			
     		}else{
     			$status =  "Failure";
     		}
    	}else{
    		$status = 'Invalid';
    	}   
		$Elog = array('RecipientEmail'=>$RecipientEmail,
			'EmailSubject'=>$subject,
			'EmailBody'=>$content,
			'IsReceived'=>$status,
			'OrderUID'=>$tOrders->OrderUID,
			'OrderNumber'=>$tOrders->OrderNumber,
			'AltOrderNumber' => $tOrders->AltOrderNumber,
			'MailReceivedDateTime'=> date('Y-m-d H:i:s')
		);
		$this->db->insert('tEmailImport',$Elog);
	}

	/**
	*Function Clear DocChase Escalation
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 28 April 2020
	*/
	public function ClearDocChaseEsclation()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');

		$Msg = '';
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('Reason', '', 'required');
		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$data['IsCleared'] = 1;
			$data['ReasonUID'] = $Reason;
			$data['Remarks'] = $remarks;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');


			$this->db->trans_begin();

			$update = $this->Common_Model->save('tOrderEsclation', $data,['OrderUID'=>$OrderUID,'IsDocChaseEscalation'=>1,'IsCleared'=>0]);
			$data = array('OrderUID'=>$OrderUID,
				'Description'=>$remarks,
				'Module'=>'DocChase',
				'WorkflowUID'=>NULL,
				'CreatedByUserUID'=>$this->loggedid,
				'CreateDateTime'=>date("Y/m/d H:i:s")
			);
			$this->Common_Model->insertNotes($data);

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Esclation_Clear_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'DocChase - Esclation Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Esclation_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	/**
	*Function forceenable workflow
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Friday 29 May 2020
	*/
	public function forceenable_workflow()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$ClosingDate = $this->input->post('ClosingDate');
		$STC = $this->input->post('STC');
		$STCAmount = $this->input->post('STCAmount');

		$Msg = '';
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('WorkflowModuleUID', '', 'required');
		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

			$tOrderWorkflowrow = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);


			$this->db->trans_begin();

			if(!empty($tOrderWorkflowrow)){

				$tOrderWorkflows['IsPresent'] = STATUS_ONE;
				$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrow->EntryDateTime) ? $tOrderWorkflowrow->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
				$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrow->DueDateTime) ? $tOrderWorkflowrow->DueDateTime : calculate_workflowduedatetime($OrderUID,$WorkflowModuleUID);
				$tOrderWorkflows['IsAssign'] = STATUS_ONE;
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
				$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
				$tOrderWorkflows['ReversedByUserUID'] = NULL;
				$tOrderWorkflows['ReversedRemarks'] = NULL;
				$tOrderWorkflows['ReversedDateTime'] = NULL;
				$tOrderWorkflows['IsKickBack'] = STATUS_ZERO;
				$tOrderWorkflows['IsRework'] = STATUS_ZERO;

				$update = $this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);

			}	else {
				$tOrderWorkflows['OrderUID'] = $OrderUID;
				$tOrderWorkflows['WorkflowModuleUID'] = $WorkflowModuleUID;
				$tOrderWorkflows['IsPresent'] = STATUS_ONE;
				$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrow->EntryDateTime) ? $tOrderWorkflowrow->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
				$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrow->DueDateTime) ? $tOrderWorkflowrow->DueDateTime : calculate_workflowduedatetime($OrderUID,$WorkflowModuleUID);
				$tOrderWorkflows['IsAssign'] = STATUS_ONE;
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
				$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
				$tOrderWorkflows['ReversedByUserUID'] = NULL;
				$tOrderWorkflows['ReversedRemarks'] = NULL;
				$tOrderWorkflows['ReversedDateTime'] = NULL;
				$tOrderWorkflows['IsKickBack'] = STATUS_ZERO;
				$tOrderWorkflows['IsRework'] = STATUS_ZERO;

				$update = $this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);

			}


			//IF Workup enabled GateKeeping to be enabled start
			if($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
				$GateKeepingWorkflowModuleUID = $this->config->item('Workflows')['GateKeeping'];
				$tOrderWorkflowrowGateKeeping = $this->Common_Model->get_row('`tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$GateKeepingWorkflowModuleUID]);
				if(!empty($tOrderWorkflowrowGateKeeping)){

					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowGateKeeping->EntryDateTime) ? $tOrderWorkflowrowGateKeeping->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowGateKeeping->DueDateTime) ? $tOrderWorkflowrowGateKeeping->DueDateTime : calculate_workflowduedatetime($OrderUID,$GateKeepingWorkflowModuleUID);
					$tOrderWorkflows['IsAssign'] = STATUS_ONE;
					$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
					$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
					$tOrderWorkflows['ReversedByUserUID'] = NULL;
					$tOrderWorkflows['ReversedRemarks'] = NULL;
					$tOrderWorkflows['ReversedDateTime'] = NULL;
					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $GateKeepingWorkflowModuleUID]);

				}	else {

					$tOrderWorkflows['OrderUID'] = $OrderUID;
					$tOrderWorkflows['WorkflowModuleUID'] = $GateKeepingWorkflowModuleUID;
					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowGateKeeping->EntryDateTime) ? $tOrderWorkflowrowGateKeeping->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowGateKeeping->DueDateTime) ? $tOrderWorkflowrowGateKeeping->DueDateTime : calculate_workflowduedatetime($OrderUID,$GateKeepingWorkflowModuleUID);
					$tOrderWorkflows['IsAssign'] = STATUS_ONE;
					$tOrderWorkflows['IsForceEnabled'] = STATUS_ONE;
					$tOrderWorkflows['IsReversed'] = STATUS_ZERO;
					$tOrderWorkflows['ReversedByUserUID'] = NULL;
					$tOrderWorkflows['ReversedRemarks'] = NULL;
					$tOrderWorkflows['ReversedDateTime'] = NULL;
					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);

				}

				/**
				*Enable Re-Work Queue 
				*@author SathishKumar <sathish.kumar@avanzegroup.com>
				*@since Friday 28 August 2020.
				*/
				$tOrderAssignmentsDetails = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$GateKeepingWorkflowModuleUID, 'WorkflowStatus'=>$this->config->item('WorkflowStatus')['Completed']]);
				$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
				if (!empty($tOrderAssignmentsDetails) && !in_array($OrderDetails->MilestoneUID, $this->config->item('ReWorkQueueExcludeMilestones'))) {

					if (!in_array($tOrderAssignmentsDetails->CompletedByUserUID, $this->config->item('ReportSkippedUsers'))) {
					
						$tOrderReWorkData = [];
						$tOrderReWorkData['OrderUID'] = $OrderUID;
						$tOrderReWorkData['WorkflowModuleUID'] = $GateKeepingWorkflowModuleUID;
						$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ONE;
						$tOrderReWorkData['EnabledByUserUID'] = $this->loggedid;
						$tOrderReWorkData['EnabledDateTime'] = date('Y-m-d H:i:s');

						$this->Common_Model->save('tOrderReWork', $tOrderReWorkData);

						// Get workflow name
						$WorkflowModuleName = $this->Common_Model->GetWorkflowModuleNameByWorkflowModuleUID($WorkflowModuleUID);

						// Add log
		     			$this->Common_Model->OrderLogsHistory($OrderUID, $WorkflowModuleName." ReWork Queue is Enabled", Date('Y-m-d H:i:s'));

					} else {

						// Technical team rework users to be moved to new orders						

						//duplicate to tOrderAssignments History 
						$this->db->select('OrderUID,WorkflowModuleUID,AssignedToUserUID,AssignedDatetime,AssignedByUserUID,WorkflowStatus,CompletedByUserUID,CompleteDateTime,IsQCSkipped,UserProjectSkip,Remarks,OrderFlag,NOW() AS CreatedDateTime');

						$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$GateKeepingWorkflowModuleUID));
						$tOrderAssignments = $this->db->get('tOrderAssignments');

						if($tOrderAssignments->num_rows()) {

							$tOrderAssignments_History = $this->db->insert_batch('tOrderAssignmentsHistory', $tOrderAssignments->result_array());

							//delete tOrderAssignments
							$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$GateKeepingWorkflowModuleUID));
							$this->db->delete('tOrderAssignments');
						}

						// Add log
						$CompletedUserName = $this->db->select('UserName')->from('mUsers')->where('UserUID', $tOrderAssignmentsDetails->CompletedByUserUID)->get()->row()->UserName;
		     			$this->Common_Model->OrderLogsHistory($OrderUID, "The order was completed by the <b>".$CompletedUserName."</b>. So instead of Re-work the order was moved to the New Order.", Date('Y-m-d H:i:s'));

					}
			        
				} 

			}

			//Unassign user
			$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

			if(!empty($is_assignment_row_available)) {
				$tOrderAssignments = [];
				$tOrderAssignments['AssignedToUserUID'] = NULL;
				$tOrderAssignments['AssignedDatetime'] = NULL;
				$tOrderAssignments['AssignedByUserUID'] = NULL;

				$assigneduser_row = $this->db->select('UserName')->from('mUsers')->where('UserUID',$is_assignment_row_available->AssignedToUserUID)->get()->row();

				$this->Common_Model->save('tOrderAssignments', $tOrderAssignments, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);

				if(!empty($assigneduser_row)) {
					
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - '.$assigneduser_row->UserName.'  UnAssigned',date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/
				}

			}

			//IF Workup enabled GateKeeping to be enabled end

			//update closing date
			/*Update tOrderImport*/
			if(!empty($ClosingDate) || !empty($STC) || !empty($STCAmount)) {

				$tOrderImportrow = $this->db->select('1')->from('tOrderImport')->where('OrderUID',$OrderUID)->get()->row();
				$torderimport['OrderUID'] = $OrderUID;
				$torderimport['ProcessorChosenClosingDate'] = $ClosingDate;
				$torderimport['STC'] = $STC;
				$torderimport['STCAmount'] = ($STC == "Amount") ? $STCAmount : NULL;
				if(!empty($tOrderImportrow)) {
					$this->db->where('OrderUID', $OrderUID);
					$this->db->update('tOrderImport', $torderimport);
				} else {
					$this->db->insert('tOrderImport',$torderimport);
				}
			}

			//check parking if available clear that

			$tOrderParkingrow = $this->db->select('1')->from('tOrderParking')->where('OrderUID',$OrderUID)->where('WorkflowModuleUID',$WorkflowModuleUID)->where('IsCleared',0)->get()->row();

			if(!empty($tOrderParkingrow)) {
				$data['IsCleared'] = 1;
				$data['ReasonUID'] = $Reason;
				$data['Remarks'] = $remarks;
				$data['ClearedByUserUID'] = $this->loggedid;
				$data['ClearedDateTime'] = date('Y-m-d H:i:s');
				$update = $this->Common_Model->save('tOrderParking', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID]);

				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - Parking Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
			}

			//clear subqueues if force enabled
			$this->OrderComplete_Model->complete_exceptionqueues($OrderUID,$WorkflowModuleUID);
			

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = sprintf($this->lang->line('ForceEnable_Failed'), $Workflow->WorkflowModuleName);
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' Queue - Enabled',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = sprintf($this->lang->line('ForceEnable_Success'), $Workflow->WorkflowModuleName);
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	/**
	*Function Raise Followup
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 30 May 2020
	*/
	public function RaiseFollowup()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$Remainder = $this->input->post('FollowupRemainder');
		$remarks = $this->input->post('remarks');
		$this->Email = $this->session->userdata('Email');
		$this->UserUID = $this->session->userdata('UserUID');

		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$QueueUID = $this->input->post('QueueUID');

		if(empty($WorkflowModuleUID) || empty($QueueUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');
		$this->form_validation->set_rules('FollowupRemainder', '', 'required');
		$this->form_validation->set_rules('QueueUID', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {

			$Queue = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

			$data['OrderUID'] = $OrderUID;
			$data['WorkflowModuleUID'] = $WorkflowModuleUID;
			$data['QueueUID'] = $QueueUID;
			$data['ReasonUID'] = $Reason;
			$data['Remainder'] = date('Y-m-d H:i:s',strtotime($Remainder));
			$data['Remarks'] = $remarks;
			$data['RaisedByUserUID'] = $this->loggedid;
			$data['RaisedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();


			$is_Followup_row_available = $this->Common_Model->get_row('tOrderFollowUp', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID,'IsCleared'=>0,'QueueUID'=>$QueueUID]);

			if (empty($is_Followup_row_available)) {					
				$data['OrderUID'] = $OrderUID;
				$data['WorkflowModuleUID'] = $WorkflowModuleUID;
				$data['ReasonUID'] = $Reason;
				$data['QueueUID'] = $QueueUID;
				$data['Remainder'] = date('Y-m-d H:i:s',strtotime($Remainder));
				$data['Remarks'] = $remarks;
				$data['RaisedByUserUID'] = $this->loggedid;
				$data['RaisedDateTime'] = date('Y-m-d H:i:s');
				$this->Common_Model->save('tOrderFollowUp', $data);
			}


			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Followup_Raise_Failed');
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Queue->QueueName.' - Followup Raised',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Followup_Raised');
				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$Msg)))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'FollowupRemainder' => form_error('FollowupRemainder'),
				'exceptiontype' => form_error('exceptiontype'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	/**
	*Function Clear Followup
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 30 May 2020
	*/
	public function ClearFollowup()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');

		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$QueueUID = $this->input->post('QueueUID');

		if(empty($WorkflowModuleUID) || empty($QueueUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		}
				
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('QueueUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {

			$OrderDetails = $this->db->select('CustomerUID')->where('OrderUID',$OrderUID)->get('tOrders')->row();

			$Queue = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

			$data['IsCleared'] = 1;
			$data['ClearedReasonUID'] = $Reason;
			$data['ClearedRemarks'] = $remarks;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();

			$update = $this->Common_Model->save('tOrderFollowUp', $data,['OrderUID'=>$OrderUID,'QueueUID'=>$QueueUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsCleared'=>0]);


			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Followup_Raise_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Queue->QueueName.' - Followup Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Followup_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	/**
	*Function fetch Dependent Workflow 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 06 June 2020
	*/
	function get_dependentworkflow()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID =$this->input->post('WorkflowUID');
		$OrderDetails = $this->db->select('CustomerUID')->where('OrderUID',$OrderUID)->get('tOrders')->row();
		$result =  $this->OrderComplete_Model->workflow_dependent_workflow_completed($OrderUID,$OrderDetails->CustomerUID,$WorkflowModuleUID);
		$this->output->set_content_type('application/json');$this->output->set_output(json_encode($result))->_display();exit;

	}

	/**
	*Function Reverse Workflow 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 06 June 2020
	*/
	function WorkflowOrderReverse() {
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID =$this->input->post('WorkflowUID');
		$ReversedRemarks =$this->input->post('ReversedRemarks');
		$ClearChecklistData =$this->input->post('ClearChecklistData');
		$ReverseInitiatedWorkflowModuleUID =$this->input->post('ReverseInitiatedWorkflowModuleUID');
		//$workflowurl = array_search($WorkflowModuleUID, $this->config->item('Order_WorkflowMenu'));

		//$redirect = (!empty($workflowurl) ? $workflowurl : 'Ordersummary').'/index/'.$OrderUID;
		$redirect = '';

		$DependentWorkflowModuleUIDs = $this->input->post('DependentWorkflowModuleUIDs');
		$DependentWorkflowModuleUIDs = !empty($DependentWorkflowModuleUIDs) ?  $DependentWorkflowModuleUIDs : [];
		$OrderReverseMsg = array('success' => 0,'message'=>'Reverse Failed','RedirectURL' => '');

		if(empty($OrderUID) || empty($WorkflowModuleUID)) {

			$this->output->set_content_type('application/json');$this->output->set_output(json_encode($OrderReverseMsg))->_display();exit;

		}

		$OrderDetails = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

		$Temp_StatusUID = $OrderDetails->StatusUID;

		$customerworkflowrow = $this->OrderComplete_Model->get_customer_workflow_milestone($OrderDetails->CustomerUID,$WorkflowModuleUID);

		//check customer workflow row
		if(empty($customerworkflowrow)) {
			$this->output->set_content_type('application/json');$this->output->set_output(json_encode($OrderReverseMsg))->_display();exit;
		}

		//fetch dependent workflows
		$dependentworkflows = $this->OrderComplete_Model->get_customer_workflows($OrderDetails->CustomerUID,$DependentWorkflowModuleUIDs);

		$this->db->trans_begin();

		//duplicate to tOrderAssignments History 
		$this->db->select('OrderUID,WorkflowModuleUID,AssignedToUserUID,AssignedDatetime,AssignedByUserUID,WorkflowStatus,CompletedByUserUID,CompleteDateTime,IsQCSkipped,UserProjectSkip,Remarks,OrderFlag,NOW() AS CreatedDateTime');

		$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$customerworkflowrow->WorkflowModuleUID));
		$tOrderAssignments = $this->db->get('tOrderAssignments');

		if($tOrderAssignments->num_rows()) {
			$tOrderAssignments_History = $this->db->insert_batch('tOrderAssignmentsHistory', $tOrderAssignments->result_array());

			
		}	

		//check kickback orders enabled
		if(!empty($customerworkflowrow->IsKickBackRequire) && $customerworkflowrow->IsKickBackRequire == 1) {
			//update the workflow 
			$tOrderAssignmentsArray = array('WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress']);

			$this->db->where(array('tOrderAssignments.OrderUID' => $OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $customerworkflowrow->WorkflowModuleUID));
			$this->db->update('tOrderAssignments', $tOrderAssignmentsArray);

		} else {

			//delete tOrderAssignments
			$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$customerworkflowrow->WorkflowModuleUID));
			$this->db->delete('tOrderAssignments');

		}	

		//tOrderWorkflows Update 
		$this->db->select('OrderUID,WorkflowModuleUID,IsPresent,IsAssign,IsForceEnabled,EntryDateTime,DueDateTime,NOW() AS CreatedDateTime');
		$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$customerworkflowrow->WorkflowModuleUID));
		$tOrderWorkflowrow = $this->db->get('tOrderWorkflows')->result();
		
		if(!empty($tOrderWorkflowrow)) {
			$tOrderWorkflowrow_History = $this->db->insert_batch('tOrderWorkflowsHistory', $tOrderWorkflowrow);
		}

		$tOrderWorkflows['EntryDateTime'] = date('Y-m-d H:i:s', strtotime("now"));
		$tOrderWorkflows['DueDateTime'] = calculate_workflowduedatetime($OrderUID,$customerworkflowrow->WorkflowModuleUID);
		$tOrderWorkflows['IsAssign'] = STATUS_ONE;
		if($customerworkflowrow->WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
			$tOrderWorkflows['IsPresent'] = STATUS_ZERO;
		} else {
			$tOrderWorkflows['IsPresent'] = STATUS_ONE;
		}
		$tOrderWorkflows['IsForceEnabled'] = STATUS_ZERO;	
		$tOrderWorkflows['IsReversed'] = STATUS_ONE;
		$tOrderWorkflows['ReversedRemarks'] = $ReversedRemarks;
		$tOrderWorkflows['ReversedByUserUID'] = $this->loggedid;
		$tOrderWorkflows['ReversedDateTime'] = date('Y-m-d H:i:s', strtotime("now"));

		$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $customerworkflowrow->WorkflowModuleUID]);

		// Insert Reverse Data
		$tOrderReverseData['OrderUID'] = $OrderUID;
		$tOrderReverseData['WorkflowModuleUID'] = $WorkflowModuleUID;
		$tOrderReverseData['ReverseInitiatedWorkflowModuleUID'] = !empty($ReverseInitiatedWorkflowModuleUID)?$ReverseInitiatedWorkflowModuleUID: NULL;
		$tOrderReverseData['ReversedRemarks'] = $ReversedRemarks;
		$tOrderReverseData['ReversedByUserUID'] = $this->loggedid;
		$tOrderReverseData['ReversedDateTime'] = date('Y-m-d H:i:s', strtotime("now"));
		$this->Common_Model->InserttOrderReverse($tOrderReverseData);

		if(!empty($dependentworkflows)) {
	
			foreach ($dependentworkflows as $dependentworkflow) {

				//duplicate to tOrderAssignments History 
				$this->db->select('OrderUID,WorkflowModuleUID,AssignedToUserUID,AssignedDatetime,AssignedByUserUID,WorkflowStatus,CompletedByUserUID,CompleteDateTime,IsQCSkipped,UserProjectSkip,Remarks,OrderFlag,NOW() AS CreatedDateTime');

				$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$dependentworkflow->WorkflowModuleUID));
				$DependenttOrderAssignments = $this->db->get('tOrderAssignments');

				if($DependenttOrderAssignments->num_rows()) {
					$tOrderAssignments_History = $this->db->insert_batch('tOrderAssignmentsHistory', $DependenttOrderAssignments->result_array());
				}


				//check kickback orders enabled
				if(!empty($dependentworkflow->IsKickBackRequire) && $dependentworkflow->IsKickBackRequire == 1) {
					//update the workflow 
					$tOrderAssignmentsArray = array('WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress']);

					$this->db->where(array('tOrderAssignments.OrderUID' => $OrderUID, 'tOrderAssignments.WorkflowModuleUID' => $dependentworkflow->WorkflowModuleUID));
					$this->db->update('tOrderAssignments', $tOrderAssignmentsArray);

				} else {
					
					//delete tOrderAssignments
					$this->db->where(array('OrderUID' => $OrderUID,'WorkflowModuleUID'=>$dependentworkflow->WorkflowModuleUID));
					$this->db->delete('tOrderAssignments');		

				}



				$tOrderWorkflows['IsPresent'] = STATUS_ZERO;
				$tOrderWorkflows['EntryDateTime'] = NULL;
				$tOrderWorkflows['DueDateTime'] = NULL;
				$tOrderWorkflows['IsForceEnabled'] = STATUS_ZERO;
				$tOrderWorkflows['IsReversed'] = STATUS_ONE;
				$tOrderWorkflows['ReversedRemarks'] = $ReversedRemarks;
				$tOrderWorkflows['ReversedByUserUID'] = $this->loggedid;
				$tOrderWorkflows['ReversedDateTime'] = date('Y-m-d H:i:s', strtotime("now"));

				$update = $this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $dependentworkflow->WorkflowModuleUID]);

				//clear subqueues if force enabled
				$this->OrderComplete_Model->complete_exceptionqueues($OrderUID,$dependentworkflow->WorkflowModuleUID);
			}

		}

		$this->Common_Model->save('tOrders', ['StatusUID'=>(!empty($customerworkflowrow->StatusUID) ? $customerworkflowrow->StatusUID : $Temp_StatusUID) ,'Temp_StatusUID'=>$Temp_StatusUID,'LastModifiedDateTime'=>date('Y-m-d H:i:s'),'LastModifiedByUserUID'=>$this->loggedid], ['OrderUID'=>$OrderUID]);

		// Check clear checklist
		if ($ClearChecklistData == 1) {

			//duplicate to tDocumentCheckList History
			$this->db->select('OrderUID, CategoryUID, DocumentTypeUID, DocumentTypeName, Answer, Comments, IsChaseSend, WorkflowUID, FileUploaded, DocumentDate, DocumentType, DocumentExpiryDate, ModifiedUserUID, ModifiedDateTime, IsDelete, Position, NOW() AS CreatedDateTime');

			$this->db->where(array('OrderUID' => $OrderUID,'WorkflowUID'=>$WorkflowModuleUID));
			$tDocumentCheckList = $this->db->get('tDocumentCheckList');

			if($tDocumentCheckList->num_rows()) {
				$tDocumentCheckList_History = $this->db->insert_batch('tDocumentCheckListHistory', $tDocumentCheckList->result_array());

				//delete tDocumentCheckList
				$this->db->where(array('OrderUID' => $OrderUID,'WorkflowUID'=>$WorkflowModuleUID));
				$this->db->delete('tDocumentCheckList');

				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'The checklist answer is cleared when the '.$customerworkflowrow->WorkflowModuleName.' is reversed ',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
			}	
		}	

		//clear subqueues if force enabled
		$this->OrderComplete_Model->complete_exceptionqueues($OrderUID,$WorkflowModuleUID);

		/**
		*Complete Re-Work Queue is Enabled 
		*@author SathishKumar <sathish.kumar@avanzegroup.com>
		*@since Friday 28 August 2020.
		*/
		$tOrderReWorkDetails = $this->Common_Model->get_row('tOrderReWork', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

		// Rework - queue remove milestone 2G, 2F, all 3 series, 4 series, 5 series
		$OrderDetails = $this->db->select('MilestoneUID')->where('OrderUID',$OrderUID)->get('tOrders')->row();

		if (!empty($tOrderReWorkDetails) && !in_array($OrderDetails->MilestoneUID, $this->config->item('ReWorkQueueExcludeMilestones'))) {
			
			$tOrderReWorkData = [];
			$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ZERO;
			$tOrderReWorkData['CompletedByUserUID'] = $this->loggedid;
			$tOrderReWorkData['CompletedDateTime'] = date('Y-m-d H:i:s');

			$this->Common_Model->save('tOrderReWork', $tOrderReWorkData, ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

			// Add log
			$this->Common_Model->OrderLogsHistory($OrderUID, "ReWork Queue is Completed", Date('Y-m-d H:i:s'));
		}			

		/*INSERT ORDER LOGS BEGIN*/
		$this->Common_Model->OrderLogsHistory($OrderUID,$customerworkflowrow->WorkflowModuleName.' Reversed  '.$ReversedRemarks,Date('Y-m-d H:i:s'));
		/*INSERT ORDER LOGS END*/

		if ($this->db->trans_status() === false) {

			$this->db->trans_rollback();
			$OrderReverseMsg = array('success' => 0,'message'=>'Reverse Failed','RedirectURL' => '');

		} else {

			$this->db->trans_commit();
			$OrderReverseMsg = array('success' => 1,'message'=>'Reversed Successfully','RedirectURL' => $redirect);

		}


		$this->output->set_content_type('application/json');$this->output->set_output(json_encode($OrderReverseMsg))->_display();exit;
	}

	function RaiseSubmissionsParkingQueue($OrderUID,$CheckParking)
	{
		if(in_array($CheckParking, $this->config->item('RaiseSubmissionsParkingQueue')))
		{		
			$CheckParking = $this->Common_Model->is_workflow_in_parkingqueue($OrderUID,$this->config->item('Workflows')['Submissions']);
			$CheckWorkflowCompleted = $this->Common_Model->IsWorkflowCompleted($OrderUID, $this->config->item('Workflows')['GateKeeping']);
			$query = $this->db->query("SELECT EXISTS (SELECT
				tOrders.OrderUID
				FROM
				tOrders
				LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID
				AND tOrderAssignments.WorkflowModuleUID = ".$this->config->item('Workflows')['Workup']."
				LEFT JOIN tOrderQueues ON tOrderQueues.OrderUID = tOrders.OrderUID
				AND tOrderQueues.QueueUID IN (".$this->config->item('RaiseSubmissionsParkingQueue')['IssuedCD'].", ".$this->config->item('RaiseSubmissionsParkingQueue')['IssuedLE'].")
				WHERE
				(
				tOrderAssignments.WorkflowStatus = 5
				OR tOrderQueues.CompletedByUserUID IS NOT NULL
				OR tOrderQueues.CompletedByUserUID != ''
				)
				AND tOrders.OrderUID = ".$OrderUID.") AS available");
			$CheckWorkupReady = $query->row()->available;

			
			if(!empty($CheckWorkupReady) && !empty($CheckWorkflowCompleted))
			{
				$data['IsCleared'] = 1;
				$data['ClearedByUserUID'] = $this->loggedid;
				$data['ClearedDateTime'] = date('Y-m-d H:i:s');
				$update = $this->Common_Model->save('tOrderParking', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$this->config->item('Workflows')['Submissions'],'IsCleared'=>0]);
			}
			else if(!empty($CheckWorkupReady) || !empty($CheckWorkflowCompleted))
			{
				if(empty($CheckParking))
				{	
					$parkingdata['OrderUID'] = $OrderUID;
					$parkingdata['WorkflowModuleUID'] = $this->config->item('Workflows')['Submissions'];
					$parkingdata['ReasonUID'] = '';
					$parkingdata['RaisedByUserUID'] = $this->loggedid;
					$parkingdata['RaisedDateTime'] = date('Y-m-d H:i:s');
					$this->Common_Model->save('tOrderParking', $parkingdata);
				}
			}
			//IF Workup enabled Submissions to be enabled start
				$SubmissionsWorkflowModuleUID = $this->config->item('Workflows')['Submissions'];
				$tOrderWorkflowrowSubmissions = $this->Common_Model->get_row('`tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$SubmissionsWorkflowModuleUID]);
				if(!empty($tOrderWorkflowrowSubmissions)){

					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowSubmissions->EntryDateTime) ? $tOrderWorkflowrowSubmissions->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowSubmissions->DueDateTime) ? $tOrderWorkflowrowSubmissions->DueDateTime : calculate_workflowduedatetime($OrderUID,$SubmissionsWorkflowModuleUID);
					$tOrderWorkflows['IsAssign'] = STATUS_ONE;
					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $SubmissionsWorkflowModuleUID]);

				}	else {

					$tOrderWorkflows['OrderUID'] = $OrderUID;
					$tOrderWorkflows['WorkflowModuleUID'] = $SubmissionsWorkflowModuleUID;
					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowSubmissions->EntryDateTime) ? $tOrderWorkflowrowSubmissions->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowSubmissions->DueDateTime) ? $tOrderWorkflowrowSubmissions->DueDateTime : calculate_workflowduedatetime($OrderUID,$SubmissionsWorkflowModuleUID);
					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);

				}
			//IF Workup enabled Submissions to be enabled end
		}
	}


		function RaiseSubmissionsParkingWorkflow($OrderUID,$CheckParking)
	{
		if(in_array($CheckParking, $this->config->item('RaiseSubmissionsParkingWorkflow')))
		{		
			$CheckParking = $this->Common_Model->is_workflow_in_parkingqueue($OrderUID,$this->config->item('Workflows')['Submissions']);
			$CheckWorkflowCompleted = $this->Common_Model->IsWorkflowCompleted($OrderUID, $this->config->item('Workflows')['GateKeeping']);
			$query = $this->db->query("SELECT EXISTS (SELECT
				tOrders.OrderUID
				FROM
				tOrders
				LEFT JOIN tOrderAssignments ON tOrderAssignments.OrderUID = tOrders.OrderUID
				AND tOrderAssignments.WorkflowModuleUID = ".$this->config->item('Workflows')['Workup']."
				LEFT JOIN tOrderQueues ON tOrderQueues.OrderUID = tOrders.OrderUID
				AND tOrderQueues.QueueUID IN (".$this->config->item('RaiseSubmissionsParkingQueue')['IssuedCD'].", ".$this->config->item('RaiseSubmissionsParkingQueue')['IssuedLE'].")
				WHERE
				(
				tOrderAssignments.WorkflowStatus = 5
				OR tOrderQueues.QueueStatus = 'Pending'
				)
				AND tOrders.OrderUID = ".$OrderUID.") AS available");
			$CheckWorkupReady = $query->row()->available;

			
			if(!empty($CheckWorkupReady) && !empty($CheckWorkflowCompleted))
			{
				$data['IsCleared'] = 1;
				$data['ClearedByUserUID'] = $this->loggedid;
				$data['ClearedDateTime'] = date('Y-m-d H:i:s');
				$update = $this->Common_Model->save('tOrderParking', $data,['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$this->config->item('Workflows')['Submissions'],'IsCleared'=>0]);
			}
			else if(!empty($CheckWorkupReady) || !empty($CheckWorkflowCompleted))
			{
				if(empty($CheckParking))
				{	
					$parkingdata['OrderUID'] = $OrderUID;
					$parkingdata['WorkflowModuleUID'] = $this->config->item('Workflows')['Submissions'];
					$parkingdata['ReasonUID'] = '';
					$parkingdata['RaisedByUserUID'] = $this->loggedid;
					$parkingdata['RaisedDateTime'] = date('Y-m-d H:i:s');
					$this->Common_Model->save('tOrderParking', $parkingdata);
				}
			}


			//IF Workup enabled Submissions to be enabled start
				$SubmissionsWorkflowModuleUID = $this->config->item('Workflows')['Submissions'];
				$tOrderWorkflowrowSubmissions = $this->Common_Model->get_row('`tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$SubmissionsWorkflowModuleUID]);
				if(!empty($tOrderWorkflowrowSubmissions)){

					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowSubmissions->EntryDateTime) ? $tOrderWorkflowrowSubmissions->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowSubmissions->DueDateTime) ? $tOrderWorkflowrowSubmissions->DueDateTime : calculate_workflowduedatetime($OrderUID,$SubmissionsWorkflowModuleUID);
					$tOrderWorkflows['IsAssign'] = STATUS_ONE;
					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $SubmissionsWorkflowModuleUID]);

				}	else {

					$tOrderWorkflows['OrderUID'] = $OrderUID;
					$tOrderWorkflows['WorkflowModuleUID'] = $SubmissionsWorkflowModuleUID;
					$tOrderWorkflows['IsPresent'] = STATUS_ONE;
					$tOrderWorkflows['EntryDateTime'] = !empty($tOrderWorkflowrowSubmissions->EntryDateTime) ? $tOrderWorkflowrowSubmissions->EntryDateTime : date('Y-m-d H:i:s', strtotime("now"));
					$tOrderWorkflows['DueDateTime'] = !empty($tOrderWorkflowrowSubmissions->DueDateTime) ? $tOrderWorkflowrowSubmissions->DueDateTime : calculate_workflowduedatetime($OrderUID,$SubmissionsWorkflowModuleUID);
					$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflows);

				}
			//IF Workup enabled Submissions to be enabled end

		}
	}

	/**
	*Function Check is order priority 1 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 18 November 2020.
	*/
	public function CheckIsPriorityOneOrder($OrderUID)
	{

		$PriorityFields = $this->Priority_Report_model->get_priorityreportfields($this->config->item('Priority1'));

		$this->db->select('tOrders.OrderUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID',$OrderUID);
		
        if(!empty($PriorityFields)) {

			foreach ($PriorityFields as $PriorityFieldrow) {

				$this->db->join("tOrderWorkflows AS " .  "TW_" .$PriorityFieldrow->SystemName,   "TW_" .$PriorityFieldrow->SystemName.".OrderUID = tOrders.OrderUID AND ". "TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = '".$PriorityFieldrow->WorkflowModuleUID."'");

				$this->db->join("tOrderAssignments AS " . "TOA_" . $PriorityFieldrow->SystemName,  "TOA_" . $PriorityFieldrow->SystemName.".OrderUID = tOrders.OrderUID AND TOA_" . $PriorityFieldrow->SystemName.".WorkflowModuleUID = '".$PriorityFieldrow->WorkflowModuleUID."'", "LEFT");

				// tDocument Checklist Table Join
				$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
				$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

				if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

					foreach ($workflowchecklist as $checklistkey => $checklistuid) {

						if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {
							
							// $this->db->where("FIND_IN_SET(DATE_FORMAT(LAST_DAY(now() - INTERVAL 1 MONTH), '%b'),TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentDate)", NULL, FALSE);
							// $this->db->where("NOT FIND_IN_SET(DATE_FORMAT(CURDATE(), '%b'),TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentDate)", NULL, FALSE);
						} else {

							$this->db->join("tDocumentCheckList AS " .  "TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_" . $checklistuid,   "TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_" . $checklistuid.".OrderUID = tOrders.OrderUID AND ". "TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_" . $checklistuid.".WorkflowUID = ".$PriorityFieldrow->WorkflowModuleUID. " AND TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentTypeUID = '" . $checklistuid."' ","LEFT");
											
						}

					}
				}
				// Join Checklist Complete Table
				$this->db->join("tOrderChecklistExpiryComplete AS TOCEC_".$PriorityFieldrow->WorkflowModuleUID,"TOCEC_".$PriorityFieldrow->WorkflowModuleUID.".OrderUID = tOrders.OrderUID AND TOCEC_".$PriorityFieldrow->WorkflowModuleUID.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID,"LEFT");

				// Expiry Checklist
				$workflowchecklist = isset($this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_Checklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;
				$Expired_MonthOnlyChecklist = isset($this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID]) ? $this->config->item('Expired_MonthOnlyChecklist')[$PriorityFieldrow->WorkflowModuleUID] : NULL;

				$CHECKLISTEXPCOND = [];
				$CHECKLISTEXPCASE = "";

				if(is_array($workflowchecklist) && !empty($workflowchecklist)) {

					foreach ($workflowchecklist as $checklistkey => $checklistuid) {

						if(isset($Expired_MonthOnlyChecklist) && !empty($Expired_MonthOnlyChecklist) && in_array($checklistuid, $Expired_MonthOnlyChecklist)) {

						} else {

							if ($PriorityFieldrow->WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {

								$CHECKLISTEXPCOND[] = "(TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentDate IS NOT NULL AND TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentDate <> '' AND DATE(STR_TO_DATE(TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 65 DAY)) AND tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones'))."))";
							} else {

								$CHECKLISTEXPCOND[] = "(TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentExpiryDate IS NOT NULL AND TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentExpiryDate <> '' AND DATE(STR_TO_DATE(TDCEXP_". $PriorityFieldrow->WorkflowModuleUID."_". $checklistuid.".DocumentExpiryDate, '%m/%d/%Y')) <= DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND tOrders.MilestoneUID NOT IN (".implode(",", $this->config->item('ExpiryChecklistOrderRestrictedMilestones'))."))";
							}
											
						}

					}

				}

				//pending or completed
				$this->db->group_start();
				if(isset($PriorityFieldrow->WorkflowStatus) && $PriorityFieldrow->WorkflowStatus == 'Completed') {

					if (!empty($CHECKLISTEXPCOND)) {
									
						$CHECKLISTEXPCASE = " AND (CASE 
							WHEN ((TOCEC_".$PriorityFieldrow->WorkflowModuleUID.".CompletedByUserUID IS NULL OR TOCEC_".$PriorityFieldrow->WorkflowModuleUID.".CompletedByUserUID = '') AND ".implode("AND", $CHECKLISTEXPCOND).") THEN FALSE
							ELSE TRUE
						END)";
					}

					$this->db->where("TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].")".$CHECKLISTEXPCASE, NULL, FALSE);

				} else {

					if (!empty($CHECKLISTEXPCOND)) {
									
						$CHECKLISTEXPCASE = "(CASE 
							WHEN ((TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus = ".$this->config->item('WorkflowStatus')['Completed'].") AND (TOCEC_".$PriorityFieldrow->WorkflowModuleUID.".CompletedByUserUID IS NULL OR TOCEC_".$PriorityFieldrow->WorkflowModuleUID.".CompletedByUserUID = '') AND ".implode("AND", $CHECKLISTEXPCOND).") THEN TRUE
							ELSE FALSE
						END)";
					}

					$this->db->where("((TW_" .$PriorityFieldrow->SystemName.".WorkflowModuleUID = ".$PriorityFieldrow->WorkflowModuleUID." AND (TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus IS NULL OR TOA_" . $PriorityFieldrow->SystemName.".WorkflowStatus <> ".$this->config->item('WorkflowStatus')['Completed'].")) OR ( ".(!empty($CHECKLISTEXPCASE)? $CHECKLISTEXPCASE : "FALSE")."))", NULL, FALSE);
				}
				$this->db->group_end();
			}
			
		}

		return $this->db->get()->result();
	}

	/**
	*Function work-up is triggered following milestones should not be moved to work-up 2A, 2B, 2C, 2K, 2H, 2J, 2I 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 11 August 2020.
	*/
	public function WorkflowForceEnableHardstop()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$workupResult = $this->OrderComplete_Model->DueDateRestrict($OrderUID);
		if($workupResult==1)
		{
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Payment Due"]))->_display();exit;
		}

		if($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) {
			// Get Order Details
			$Workflow = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

			// Get Order Details
			$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID);

			// Only Priority 1 Loans to be triggered for work-up
			if (empty($this->CheckIsPriorityOneOrder($OrderUID))) {
				
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"<span style='font-size: 16px;'>Loan is not in Priority 1 to trigger for work-up</span>"]))->_display();exit;
			}

			// Check the order hardstop milestone is matched in the array
			if (in_array($Workflow->MilestoneUID, $this->config->item('WorkEnableHardstopMilestones'))) {
				
				if (isset($this->UserPermissions->IsLockExpirationRestricted) && $this->UserPermissions->IsLockExpirationRestricted == 1) {
					$this->output->set_content_type('application/json');
					$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"<span style='font-size: 16px;'>Loan Number(<span style='font-weight: bold;'>".$OrderDetails->LoanNumber."</span>) is in Milestone (<span style='font-weight: bold;'>".$OrderDetails->MilestoneName."</span>) Not ready for Workup yet.</span>"]))->_display();exit;
				}
			} 

			if (!empty($OrderDetails->LockExpiration)) {

				if (!$this->Common_Model->CheckLockExpiration($OrderDetails->LockExpiration, ['IsLockExpirationRestricted'=>true])) {

					$this->output->set_content_type('application/json');
					$this->output->set_output(json_encode(['validation_error'=>1,'message'=>"<span style='font-size: 16px;'><span style='font-weight: bold;'>".$OrderDetails->LoanNumber."</span> Rate lock is expiring, Please extend the rate.</span>"]))->_display();exit;
				}

			}

		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(['validation_error'=>0,'message'=>"Success"]))->_display();exit;
	}

	/**
	*Function Complete Re-Work Queue 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 28 August 2020.
	*/
	public function ReWork_Complete()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$this->db->trans_begin();

		$tOrderReWorkData = [];
		$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ZERO;
		$tOrderReWorkData['CompletedByUserUID'] = $this->loggedid;
		$tOrderReWorkData['CompletedDateTime'] = date('Y-m-d H:i:s');

		$this->Common_Model->save('tOrderReWork', $tOrderReWorkData, ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

		$WorkflowDetailsConfig = $this->config->item('WorkflowDetails')[$WorkflowModuleUID];

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$response['message'] = $this->lang->line('Complete_Failed');
			$response['validation_error'] = 1;
		} else {

			// Add log
			$this->Common_Model->OrderLogsHistory($OrderUID, "ReWork Queue is Completed", Date('Y-m-d H:i:s'));

			$this->db->trans_commit();
			$displayline = $this->lang->line($WorkflowDetailsConfig['line']);
			if($displayline == ""){
				$displayline = $this->lang->line('Completed');
			}
			$response['message'] = $displayline;
			$response['popup_message'] = $WorkflowDetailsConfig['message'];
			$response['redirect'] = $WorkflowDetailsConfig['screen'];
			$response['validation_error'] = 0;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));

	}

	/**
	*Function Enable HOI Rework Queue 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 28 September 2020.
	*/
	function EnableHOIReworkQueue()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$Remarks = $this->input->post('Remarks');

		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('', '');

		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('WorkflowModuleUID', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$this->db->trans_begin();

			$is_rework_enabled = $this->Common_Model->get_row('tOrderReWork', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID,'IsReWorkEnabled'=>STATUS_ONE]);

			if (empty($is_rework_enabled)) {
				
				$IsWorkflowEnabled = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsPresent'=>STATUS_ONE]);
				if (!empty($IsWorkflowEnabled)) {
					
					$tOrderReWorkData = [];
					$tOrderReWorkData['OrderUID'] = $OrderUID;
					$tOrderReWorkData['WorkflowModuleUID'] = $WorkflowModuleUID;
					$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ONE;
					$tOrderReWorkData['EnabledByUserUID'] = $this->loggedid;
					$tOrderReWorkData['EnabledDateTime'] = date('Y-m-d H:i:s');
					$tOrderReWorkData['EnabledRemarks'] = $Remarks;

					$this->Common_Model->save('tOrderReWork', $tOrderReWorkData);

				} else {
					$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 1, 'message' => 'Workflows not enabled')))->_display();
					exit;
				}
			} else {
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 1, 'message' => 'Rework queue is already enabled')))->_display();
				exit;
			}

			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 1, 'message' => 'Unable to enable Rework Queue')))->_display();
				exit;
			}
			else{
				// Get workflow name
				$WorkflowModuleName = $this->Common_Model->GetWorkflowModuleNameByWorkflowModuleUID($WorkflowModuleUID);

				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID, $WorkflowModuleName." ReWork Queue is Enabled", Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/

				$this->db->trans_commit();

				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>'Rework queue is enabled')))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'WorkflowModuleUID' => form_error('WorkflowModuleUID'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}
	}

	/**
	*Function Enable HOI Rework Queue 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 28 September 2020.
	*/
	function CompleteHOIReworkQueue()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$Remarks = $this->input->post('Remarks');

		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('', '');

		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('WorkflowModuleUID', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$this->db->trans_begin();

			$is_rework_enabled = $this->Common_Model->get_row('tOrderReWork', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID,'IsReWorkEnabled'=>STATUS_ONE]);

			if (!empty($is_rework_enabled)) {
				
				$IsWorkflowEnabled = $this->Common_Model->get_row('tOrderWorkflows', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsPresent'=>STATUS_ONE]);
				if (!empty($IsWorkflowEnabled)) {
					
					$tOrderReWorkData = [];
					$tOrderReWorkData['IsReWorkEnabled'] = STATUS_ZERO;
					$tOrderReWorkData['CompletedByUserUID'] = $this->loggedid;
					$tOrderReWorkData['CompletedDateTime'] = date('Y-m-d H:i:s');
					$tOrderReWorkData['CompletedRemarks'] = $Remarks;

					$this->Common_Model->save('tOrderReWork', $tOrderReWorkData, ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'IsReWorkEnabled'=>STATUS_ONE]);

				} else {
					$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 1, 'message' => 'Workflows not enabled')))->_display();
					exit;
				}
			} else {
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 1, 'message' => 'Rework queue is not enabled')))->_display();
				exit;
			}

			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 1, 'message' => 'Unable to enable Rework Queue')))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID, "ReWork Queue is completed", Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/

				$this->db->trans_commit();

				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>'Rework Queue is completed')))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'WorkflowModuleUID' => form_error('WorkflowModuleUID'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}
	}

	/**
	*Function Initiate Pending 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 09 October 2020
	*/
	public function InitiateSubQueuePending()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$this->db->trans_begin();

		$tOrderSubQueuesData = [];
		$tOrderSubQueuesData['OrderUID'] = $OrderUID;
		$tOrderSubQueuesData['WorkflowModuleUID'] = $WorkflowModuleUID;
		$tOrderSubQueuesData['SubQueueStatus'] = 'Pending';
		$tOrderSubQueuesData['RaisedByUserUID'] = $this->loggedid;
		$tOrderSubQueuesData['RaisedDateTime'] = date('Y-m-d H:i:s');

		$this->Common_Model->save('tOrderSubQueues', $tOrderSubQueuesData);

		$WorkflowDetailsConfig = $this->config->item('WorkflowDetails')[$WorkflowModuleUID];

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$response['message'] = $this->lang->line('SubQueuePendingInitiate_Failed');
			$response['validation_error'] = 1;
		} else {

			// Add log
			$this->Common_Model->OrderLogsHistory($OrderUID, $this->lang->line('SubQueuePendingInitiate_Success'), Date('Y-m-d H:i:s'));

			$this->db->trans_commit();

			$response['message'] = $this->lang->line('SubQueuePendingInitiate_Success');
			$response['popup_message'] = $WorkflowDetailsConfig['message'];
			$response['redirect'] = $WorkflowDetailsConfig['screen'];
			$response['validation_error'] = 0;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));

	}

	/**
	*Function Complete Pending 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 09 October 2020
	*/
	public function CompleteSubQueuePending()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$this->db->trans_begin();

		$tOrderSubQueuesData = [];
		$tOrderSubQueuesData['OrderUID'] = $OrderUID;
		$tOrderSubQueuesData['WorkflowModuleUID'] = $WorkflowModuleUID;
		$tOrderSubQueuesData['SubQueueStatus'] = 'Completed';
		$tOrderSubQueuesData['CompletedByUserUID'] = $this->loggedid;
		$tOrderSubQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

		$this->Common_Model->save('tOrderSubQueues', $tOrderSubQueuesData, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'SubQueueStatus' => 'Pending']);

		$WorkflowDetailsConfig = $this->config->item('WorkflowDetails')[$WorkflowModuleUID];

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$response['message'] = $this->lang->line('SubQueuePendingComplete_Failed');
			$response['validation_error'] = 1;
		} else {

			// Add log
			$this->Common_Model->OrderLogsHistory($OrderUID, $this->lang->line('SubQueuePendingComplete_Success'), Date('Y-m-d H:i:s'));

			$this->db->trans_commit();
			
			$response['message'] = $this->lang->line('SubQueuePendingComplete_Success');
			$response['popup_message'] = $WorkflowDetailsConfig['message'];
			$response['redirect'] = $WorkflowDetailsConfig['screen'];
			$response['validation_error'] = 0;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));

	}

	/**
	*Function Checklist Expiry Complete 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 09 October 2020
	*/
	public function ChecklistExpiryComplete()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$this->db->trans_begin();

		$tOrderChecklistExpiryCompleteData = [];
		$tOrderChecklistExpiryCompleteData['OrderUID'] = $OrderUID;
		$tOrderChecklistExpiryCompleteData['WorkflowModuleUID'] = $WorkflowModuleUID;
		$tOrderChecklistExpiryCompleteData['CompletedByUserUID'] = $this->loggedid;
		$tOrderChecklistExpiryCompleteData['CompletedDateTime'] = date('Y-m-d H:i:s');

		$this->Common_Model->save('tOrderChecklistExpiryComplete', $tOrderChecklistExpiryCompleteData);

		$WorkflowDetailsConfig = $this->config->item('WorkflowDetails')[$WorkflowModuleUID];

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$response['message'] = $this->lang->line('Complete_Failed');
			$response['validation_error'] = 1;
		} else {
			// Get workflow name
			$WorkflowModuleName = $this->Common_Model->GetWorkflowModuleNameByWorkflowModuleUID($WorkflowModuleUID);

			// Add log
			$this->Common_Model->OrderLogsHistory($OrderUID, $WorkflowModuleName." Checklist Expiry Completed", Date('Y-m-d H:i:s'));

			$this->db->trans_commit();
			$response['message'] = "Expiry Completed";
			$response['popup_message'] = $WorkflowDetailsConfig['message'];
			$response['redirect'] = $WorkflowDetailsConfig['screen'];
			$response['validation_error'] = 0;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));

	}

	/**
	*Function Update Excepiton Reason 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 16 October 2020.
	*/
	public function UpdateExceptionReason()
	{ 
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$QueueUID = $this->input->post('QueueUID');

		if(empty($QueueUID) || empty($OrderUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('Invalid Request'))))->_display();exit;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('', '');

		$this->form_validation->set_rules('OrderUID', '', 'required');		

		// Get Reason
		$mQueueReasons = $this->Common_Model->get_mqueuesmreasons($QueueUID);

		if (!empty($mQueueReasons)) {
			
			$this->form_validation->set_rules('Reason[]', '', 'required');	
		}

		if (!empty($Reason)) {
			
			// $mReasons = $this->Common_Model->get_row('mReasons', ['ReasonUID'=>$Reason]);
			$mReasons = $this->db->select('*')->from('mReasons')->where_in('ReasonUID',$Reason)->get()->result_array();

			$ReasonNameArr = array_column($mReasons, "ReasonName");

			if (in_array("Others", $ReasonNameArr)) {
				
				$this->form_validation->set_rules('remarks', '', 'required');
			}
		}		

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true) {

			$mQueues = $this->Common_Model->get_row('mQueues', ['QueueUID'=>$QueueUID]);

			$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$mQueues->WorkflowModuleUID]);
			$WorkflowModuleUID = $Workflow->WorkflowModuleUID;

			$this->db->trans_begin();

			$is_queue_row_available = $this->Common_Model->get_row('tOrderQueues', ['OrderUID'=>$OrderUID, 'QueueUID'=> $QueueUID,'QueueStatus'=>"Pending"]);

			if (empty($is_queue_row_available)) {

				$data['OrderUID'] = $OrderUID;
				$data['QueueUID'] = $QueueUID;
				$data['QueueStatus'] = "Pending";
				$data['RaisedReasonUID'] = implode(",", $Reason);
				$data['RaisedRemarks'] = $remarks;
				$data['RaisedByUserUID'] = $this->loggedid;
				$data['RaisedDateTime'] = date('Y-m-d H:i:s');
				
				$this->Common_Model->save('tOrderQueues', $data);

			} else {

				$data['RaisedReasonUID'] = implode(",", $Reason);
				$data['RaisedRemarks'] = $remarks;
				$data['RaisedByUserUID'] = $this->loggedid;
				$data['RaisedDateTime'] = date('Y-m-d H:i:s');
				
				$this->Common_Model->save('tOrderQueues', $data,['OrderUID'=>$OrderUID,'QueueUID'=>$QueueUID, "QueueStatus" => "Pending"]);
			}

			if ($this->db->trans_status()===false) {
				$this->db->trans_rollback();
				$this->output->set_content_type('application/json')
					->set_output(json_encode(array('validation_error' => 1, 'message' => 'Failed to update '.$mQueues->QueueName.' Reason')))->_display();
				exit;
			}
			else{
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$mQueues->QueueName.' - Reason is Updated',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/

				$this->db->trans_commit();

				$this->output->set_content_type('application/json')	
					->set_output(json_encode(array('validation_error'=>0, 'message'=>$mQueues->QueueName.' - Reason is Updated')))->_display();exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'exceptiontype' => form_error('exceptiontype'),
				'ExcecptionQueueClearReason' => form_error('Reason[]'),
				'ExcecptionQueueClearRemarks' => form_error('remarks'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode($validation_data))->_display(); exit;

		}

	}

	/**
	*Function Clear Static Queue Followup 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 05 November 2020.
	*/
	public function ClearStaticQueueFollowup()
	{
		$OrderUID = $this->input->post('OrderUID');
		$Reason = $this->input->post('Reason');
		$remarks = $this->input->post('remarks');
		$Page = $this->input->post('Page');

		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$StaticQueueUID = $this->input->post('StaticQueueUID');

		if(empty($WorkflowModuleUID) || empty($StaticQueueUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('validation_error'=>1, 'message'=>$this->lang->line('order_notassigned_page'))))->_display();exit;
		}
				
		$Msg = '';

		$this->load->library('form_validation');


		$this->form_validation->set_error_delimiters('', '');


		$this->form_validation->set_rules('OrderUID', '', 'required');
		$this->form_validation->set_rules('StaticQueueUID', '', 'required');
		//$this->form_validation->set_rules('Reason', '', 'required');

		$this->form_validation->set_message('required', 'This Field is required');

		if ($this->form_validation->run() == true && $WorkflowModuleUID) {

			$OrderDetails = $this->db->select('CustomerUID')->where('OrderUID',$OrderUID)->get('tOrders')->row();

			$Queue = $this->Common_Model->get_row('mStaticQueues', ['StaticQueueUID'=>$StaticQueueUID]);

			$data['IsCleared'] = 1;
			$data['ClearedReasonUID'] = $Reason;
			$data['ClearedRemarks'] = $remarks;
			$data['ClearedByUserUID'] = $this->loggedid;
			$data['ClearedDateTime'] = date('Y-m-d H:i:s');

			$this->db->trans_begin();

			$update = $this->Common_Model->save('tOrderFollowUp', $data,['OrderUID'=>$OrderUID,'StaticQueueUID'=>$StaticQueueUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsCleared'=>0]);


			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$Msg = $this->lang->line('Followup_Raise_Failed');

				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			} else {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,$Queue->StaticQueueName.' - Followup Cleared',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
				$this->db->trans_commit();
				$Msg = $this->lang->line('Followup_Cleared');
				$this->output->set_content_type('application/json')
				->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
				exit;
			}

		} else {

			$Msg = $this->lang->line('Empty_Validation');

			$formvalid = [];

			$validation_data = array(
				'validation_error' => 1,
				'message' => $Msg,
				'OrderUID' => form_error('OrderUID'),
				'Reason' => form_error('Reason'),
			);
			foreach ($validation_data as $key => $value) {
				if (is_null($value) || $value == '')
					unset($validation_data[$key]);
			}
			$this->output->set_content_type('application/json')
				->set_output(json_encode($validation_data))->_display();
			exit;

		}

	}

	public function InitiateOrderMovetoKickBack()
	{
		$OrderUID = $this->input->post('OrderUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$this->db->trans_begin();

		// Clear Followup
		$Queues = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID);
		foreach ($Queues as $key => $queue) {

			// Clear the exception if raised for this order
			$tOrderQueuesData = [];
			$tOrderQueuesData['OrderUID'] = $OrderUID;
			$tOrderQueuesData['QueueUID'] = $queue->QueueUID;
			$tOrderQueuesData['QueueStatus'] = "Completed";
			$tOrderQueuesData['CompletedReasonUID'] = '';
			$tOrderQueuesData['CompletedRemarks'] = ''; // Exception queue is force completed.
			$tOrderQueuesData['CompletedByUserUID'] = $this->config->item('Cron_UserUID');
			$tOrderQueuesData['CompletedDateTime'] = date('Y-m-d H:i:s');

			$this->db->where(array(
				'OrderUID'=>$OrderUID,
				'QueueUID'=>$queue->QueueUID, 
				"QueueStatus" => "Pending"
			));
			$this->db->update('tOrderQueues', $tOrderQueuesData);

			if($this->db->affected_rows()) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'<b>'.$WorkflowModuleName.' - '.$queue->QueueName.'</b> sub queue was force completed.',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
			}

			// Clear the followup if raised for this order
			$tOrderFollowUpData = [];
			$tOrderFollowUpData['IsCleared'] = 1;
			$tOrderFollowUpData['ClearedReasonUID'] = '';
			$tOrderFollowUpData['ClearedRemarks'] = ''; // FollowUp is cleared because workflow is force completed.
			$tOrderFollowUpData['ClearedByUserUID'] = $this->config->item('Cron_UserUID');
			$tOrderFollowUpData['ClearedDateTime'] = date('Y-m-d H:i:s');

			$this->db->where(array(
				'OrderUID'=>$OrderUID,
				'QueueUID'=>$queue->QueueUID,
				'WorkflowModuleUID'=>$WorkflowModuleUID,
				'IsCleared'=>0
			));
			$this->db->update('tOrderFollowUp', $tOrderFollowUpData);	

			if($this->db->affected_rows()) {
				/*INSERT ORDER LOGS BEGIN*/
				$this->Common_Model->OrderLogsHistory($OrderUID,'<b>'.$WorkflowModuleName.'</b> followup is cleared.',Date('Y-m-d H:i:s'));
				/*INSERT ORDER LOGS END*/
			}
		}

		// Order in parking queue need to complete
		$tOrderParkingData = [];
		$tOrderParkingData['IsCleared'] = 1;
		$tOrderParkingData['ReasonUID'] = '';
		$tOrderParkingData['Remarks'] = ''; // Parking is force completed.
		$tOrderParkingData['ClearedByUserUID'] = $this->config->item('Cron_UserUID');
		$tOrderParkingData['ClearedDateTime'] = date('Y-m-d H:i:s');

		$this->db->where(array(
			'OrderUID'=>$OrderUID,
			'WorkflowModuleUID'=>$WorkflowModuleUID,
			'IsCleared'=>0
		));
		$this->db->update('tOrderParking', $tOrderParkingData);

		if($this->db->affected_rows()) {
			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($OrderUID,'<b>'.$WorkflowModuleName.'</b> parking queues is cleared.',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
		}

		// Update kickback
		$tOrderWorkflowsData = [];
		$tOrderWorkflowsData['IsKickBack'] = STATUS_ONE;
		$tOrderWorkflowsData['KickBackUserUID'] = $this->loggedid;
		$tOrderWorkflowsData['KickBackDateTime'] = date('Y-m-d H:i:s');

		$this->Common_Model->save('tOrderWorkflows', $tOrderWorkflowsData, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);

		$WorkflowDetailsConfig = $this->config->item('WorkflowDetails')[$WorkflowModuleUID];

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$response['message'] = $this->lang->line('KickBackInitiate_Failed');
			$response['validation_error'] = 1;
		} else {

			// Get workflow name
			$WorkflowModuleName = $this->Common_Model->GetWorkflowModuleNameByWorkflowModuleUID($WorkflowModuleUID);

			// Add log
			$this->Common_Model->OrderLogsHistory($OrderUID, $WorkflowModuleName." order is moved to kickback queue", Date('Y-m-d H:i:s'));

			$this->db->trans_commit();

			$response['message'] = $this->lang->line('KickBackInitiate_Success');
			$response['popup_message'] = $WorkflowDetailsConfig['message'];
			$response['redirect'] = $WorkflowDetailsConfig['screen'];
			$response['validation_error'] = 0;
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($response));

	}
	
}?>
