<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class OrderLoanFile extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('OrderLoanFile_model');
		$this->load->model('Common_Model');
	}	

	public function index()
	{
		$OrderUID = $this->uri->segment(3);
		$data['content'] = 'index';
		$data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID'=>'ASC'], []);
		$data['OrderSummary'] = $this->OrderLoanFile_model->GettOrders($OrderUID);
		$data['Workflows'] = $this->Common_Model->GetOrderWorkflows($OrderUID);
		$data['Status'] = $this->Common_Model->GetOrderWorkflowsWithStatus($OrderUID);
		
		$this->OrderLoanFile_model->checkAssignDocumentStorage($data['OrderSummary']);
		
		$data['Documents'] = $this->Common_Model->GetLoanDocuments($OrderUID);

		$data['CompanyDetails'] = $this->Common_Model->get('mCompanyDetails');

		$HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];
		$data['hoi_company'] = $this->Common_Model->getHoiDocumenttype($OrderUID,$HoiWorkflowModuleUID, $this->config->item('HoiChecklistKeys')['CompanyName']); 
		$data['hoi_email'] = $this->Common_Model->getHoiDocumenttype($OrderUID,$HoiWorkflowModuleUID, $this->config->item('HoiChecklistKeys')['Email']); 
		$data['hoi_efax'] = $this->Common_Model->getHoiDocumenttype($OrderUID,$HoiWorkflowModuleUID, $this->config->item('HoiChecklistKeys')['FaxNo']);
		$data['hoi_web'] = $this->Common_Model->getHoiDocumenttype($OrderUID,$HoiWorkflowModuleUID, $this->config->item('HoiChecklistKeys')['Website']);
		$data['hoi_policyEx'] = $this->Common_Model->getHoiDocumenttype($OrderUID,$HoiWorkflowModuleUID, $this->config->item('HoiChecklistKeys')['PolicyExpireDate']);
		$data['hoi_policyNo'] = $this->Common_Model->getHoiDocumenttype($OrderUID,$HoiWorkflowModuleUID, $this->config->item('HoiChecklistKeys')['PolicyNumber']);
		$data['hoi_ContactNo'] = $this->Common_Model->getHoiDocumenttype($OrderUID,$HoiWorkflowModuleUID, $this->config->item('HoiChecklistKeys')['ContactNo']);
		// echo "<pre>";print_r($data);exit;
		$data['OrderDetails'] = $this->Common_Model->getOrderDetails($OrderUID);
		$data['ExceptionList'] = $this->Common_Model->GetOrderExceptions($OrderUID);
		$data['BorrowerName'] = $this->OrderLoanFile_model->GetBorrowerName($OrderUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	/**
	*Function to upload loan file and notify HOI company 
	*@author Santhiya M <santhiya.m@avanzegroup.com>
	*@since June 2020 
	*Modified at Tuesday 14 July 2020
	*Modified by Santhiya M <santhiya.m@avanzegroup.com>
	*Modification reason - Move HOI queues based on response 
	*Added MoveQueue() common function
	*/
	function UpdateLoanFile(){
		$cName = $this->input->post('name');
		$OrderUID = $this->input->post('OrderUID');
		$getCompanyDetails = $this->db->select('*')->from('mCompanyDetails')->like('CompanyName',$cName)->get();
		$order = $this->Common_Model->getOrderDetails($OrderUID);
		
		$status='exception';
		if($getCompanyDetails->num_rows() > 0){
			
			$email = $getCompanyDetails->row()->Email;
			$CompanyUID = $getCompanyDetails->row()->CompanyUID;
			
			if(!empty($email)){
				$OrderDocumentPath = "uploads/OrderDocumentPath/" . $order->OrderNumber ;
				$this->Common_Model->CreateDirectoryToPath($OrderDocumentPath);
				$config['allowed_types'] = 'pdf|docx|xls|xlsx';
				$config['upload_path'] = $OrderDocumentPath;
				$config['encrypt_name'] = true;
				$config['overwrite'] = true;
				$this->load->library('upload', $config);
				$upload = $this->upload->do_upload('file');
				if($upload){
					$data = $this->upload->data();
					
					$file_name = explode('.', $data['file_name']);
					$path = $OrderDocumentPath.'/'.$data['file_name'];

					$Doc = array('DocumentName' => $data['orig_name'], 
						'DocumentURL'=> ($path),
						'OrderUID'=> $OrderUID,
						'IsStacking'=> 1,
						'UploadedByUserUID' => $this->loggedid,
						'TypeofDocument'=> 'Others',
						'UploadedDateTime'=> date('Y-m-d H:i:s'),
					);
					
					$this->db->insert('tDocuments',$Doc);
					$DocumentUID = $this->db->insert_id();

					$this->db->where('OrderUID',$OrderUID);
					$this->db->delete('tLoanFiles');

					$loan = array('DocumentUID'=>$DocumentUID,
						'CompanyUID'=>$CompanyUID,
						'OrderUID'=>$OrderUID,
					);
					$this->db->insert('tLoanFiles',$loan);
					$msg = 'Updated';
				}else{
					$msg = 'Error Uploading file';
				}
				$from_email = $this->Common_Model->pluckSettingValue('RMUser');
            		// $from_email = "notifications@direct2title.com"; 
            		//Load email library 
				$this->load->library('email'); 
				$this->email->from($from_email);	

				$to_email = $email;
				$subject = 'Mortgagee Clause Change Request - Loan Number : '.$order->LoanNumber;
	        		// $content = str_replace('%LoanNumber%', $order->LoanNumber, $get_mail_content->Body);
	        		// $content = str_replace('%OrderNumber%', $order->OrderNumber, $content);
				$uniqueid = 'MailRef-'.rand();
				$this->email->set_header('ReferenceNumber', $uniqueid);
				$this->email->set_header('OrderUID', $order->OrderUID);
				$this->email->to($to_email);
				$this->email->subject($subject); 
				$mortgage = array('LoanNumber'=> $order->LoanNumber,
					'OrderNumber' => $order->OrderNumber,
					'BorrowerName'=> $order->CustomerName,
					'uniqueid' => $uniqueid);
				$body = $this->load->view('common/mortgage_mail.php',$mortgage,TRUE);

				$this->email->message($body); 

				if($this->email->send()){
					$Elog = array(
						'SenderEmail'=>$from_email,
						'RefID'=>$uniqueid,
						'RecipientEmail'=>$to_email,
						'EmailSubject'=>$subject,
						'EmailBody'=>$body,
						'IsReceived'=>$status,
						'OrderUID'=>$order->OrderUID,
						'OrderNumber'=>$order->OrderNumber,
						'AltOrderNumber' => '',
						'MailReceivedDateTime'=> date('Y-m-d H:i:s')
					);
					$this->db->set($Elog);
					$this->db->insert('tEmailImport');
	         			// $this->db->insert('tEmailImport',$Elog);
					$EmailUID = $this->db->insert_id();


					$QueueUID = $this->config->item('HoiAutomationQueues')['HOIRequest'];
					$update_queue = $this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);


					/* Change HOI Order notification flag to notified*/
					$this->Common_Model->NotifiedOrder($order->OrderUID);

					$status =  "Success";	         			
					$res = array('Status' => 0,'message'=>'Email sent successfully ' . $to_email . '! And the loan will be moved to HOI Request queue.','type' => 'success');
				}else{
					$status =  "Failure";
					$res = array('Status' => 2,'message'=>'Something went wrong in send email! Please try again. ','type' => 'danger');
				}         		

			}else{			


				$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
				$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);

				/* Change HOI Order notification flag to notified*/
				$this->Common_Model->NotifiedOrder($order->OrderUID);

				$res = array('Status' => 1,'message'=>'No email found, Loan will be moved to exception Queue','type' => 'danger');
			}

		}else{


			$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
			$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);

			/* Change HOI Order notification flag to notified*/
			$this->Common_Model->NotifiedOrder($order->OrderUID);

			$res = array('Status' => 1,'message'=>'No email found, Loan will be moved to exception Queue','type' => 'danger');
		}	

		echo json_encode($res);exit();
	}

	function RemoveDocument(){
		$DocUID = $this->input->post('DocUID');
		if(!empty($DocUID)){
			$this->db->where('DocumentUID',$DocUID);
			$this->db->delete('tDocuments');
			return true;
		}else{
			return false;
		}
		
	}
	function GetCompanyDetails(){
		$name = $this->input->post('name');
		$OrderUID = $this->input->post('OrderUID');
		$getCompanyDetails = $this->Common_Model->getCompanyDetails($name);
		$OrderImportDetails = $this->Common_Model->get_row('tOrderImport', ['InsuranceCompany'=>$name, 'OrderUID'=>$OrderUID]);
		if(!empty($getCompanyDetails) && $getCompanyDetails->Email){
			if($OrderImportDetails){
				$response = array('Status' => 0,'message'=>'yes','details'=>$getCompanyDetails,'OrderImportDetails'=>$OrderImportDetails,'type' => 'success');
			}else{
				$response = array('Status' => 0,'message'=>'yes','details'=>$getCompanyDetails,'OrderImportDetails'=>false,'type' => 'success');
			}
		}else{
			$response = array('Status' => 1,'message'=>'No email found','type' => 'danger');
		}
		echo json_encode($response);exit();
	}

	/**
	*Function run ocr with uploaded files 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 30 June 2020 IST.	
	*Modified at Tuesday 14 July 2020
	*Modified by Santhiya M <santhiya.m@avanzegroup.com>
	*Modification reason - Move HOI queues based on response 
	*Added MoveQueue() Common function
	*/
	function init_ocr()
	{
		//OCR INCLUDE FILE
		include (FCPATH  . 'OCR/DoctracCron.php');
		$OrderUID = $this->input->post('OrderUID');
		$order = $this->Common_Model->getOrderDetails($OrderUID);
		$Path=FCPATH . 'uploads/OrderDocumentPath/' .$order->OrderNumber . '/';
		$this->Common_Model->CreateDirectoryToPath($Path);

		$config['allowed_types'] = 'pdf|docx|xls|xlsx';
		$config['upload_path'] = 'uploads/OrderDocumentPath/' .$order->OrderNumber ;
		$config['encrypt_name'] = true;
		$config['overwrite'] = true;
		$this->load->library('upload', $config);
		$upload = $this->upload->do_upload('file');
		if($upload) {
			$data = $this->upload->data();


			$file_name = explode('.', $data['file_name']);
			$viewPath='uploads/OrderDocumentPath/' .$order->OrderNumber . '/'.$data['file_name'];

			$Doc = array(
				'DocumentName' => $data['orig_name'], 
				'DocumentURL'=> $viewPath,
				'OrderUID'=> $OrderUID,
				'IsStacking'=> 1,
				'UploadedByUserUID' => $this->loggedid,
				'TypeofDocument'=> 'Others',
				'UploadedDateTime'=> date('Y-m-d H:i:s'),
			);

			$this->db->insert('tDocuments',$Doc);
			$DocumentUID = $this->db->insert_id();

			$this->db->where('OrderUID',$OrderUID);
			$this->db->update('tOrders',['IsOCREnabled'=>1]);
			//sto here if want to use cron
			echo json_encode(array('Status' => 0,'message'=>'Waiting for OCR!','type' => 'success'));exit();

			$tOrders = $this->db->select('o.ProjectUID,o.OrderUID,o.OrderNumber,td.DocumentURL,td.DocumentStorage')->from('tOrders AS o')->join('tDocuments AS td','td.OrderUID=o.OrderUID')->where(['o.IsOCREnabled'=>1,' td.IsStacking'=>1,'o.OrderUID' => $OrderUID])->order_by('o.OrderUID')->limit(1)->get()->row();

			if(empty($tOrders)) {
				echo json_encode(array('Status' => 1,'message'=>'OCR not enabled!','type' => 'danger'));exit();
			}

			$FilePath = FCPATH . $tOrders->DocumentURL;
			/*created JSON array values*/
			$RequestParams = array(
				"sourceApp"=>"DOCTRAC",
				"orderNo"=>$tOrders->OrderNumber,
				"orderUID"=>$tOrders->OrderUID, 
				"docType"=> "PDF",
				"source"=> $FilePath,
				"features"=> "DOCUMENT_TEXT_DETECTION",
				// "featuresInput"=> "path of the image/icon",
				// "outputFormat"=> "JSON",
				// "outputLocation"=> "path of the output file",
				"engine"=> "TESSERACT",
				//"docDef"=> $DocumentTypeObjects
			);

			// call OCR function
			$response = run_OCR($RequestParams);

			if (empty($response)) {

				$Docarray = array(
					'IsStacking' => 4
				);
				$this->db->where('DocumentUID',$DocumentUID);
				$this->db->update('tDocuments',$Docarray);
				echo json_encode(array('Status' => 2,'message'=>'Response Failed','type' => 'danger'));exit();
				//echo "cURL Error #:" . $err;
			} else {
				//echo $response;

				//change to OCR Completed
				$this->db->where('OrderUID',$OrderUID);
				$this->db->update('tOrders',['IsOCREnabled'=>2]);

				$Fetch_Request_Details = json_decode($response,true);
				//parsing array
				if(isset($Fetch_Request_Details['data']) && is_array($Fetch_Request_Details['data'])) {
					foreach ($Fetch_Request_Details['data'] as $Fetch_Request_Detailsdata) {
						if(isset($Fetch_Request_Detailsdata) && is_array($Fetch_Request_Detailsdata)) {
							foreach ($Fetch_Request_Detailsdata as $Fetch_Request_Detailsdatavalue) {

								if(isset($Fetch_Request_Detailsdatavalue['extractedData']) && is_array($Fetch_Request_Detailsdatavalue['extractedData'])) {
									foreach ($Fetch_Request_Detailsdatavalue['extractedData'] as $Fetch_Request_Detailsdatavaluekey => $Fetch_Request_DetailsdatavalueextractedData) {
										$torderimportdata = [];
										if(isset($Fetch_Request_DetailsdatavalueextractedData['InsuranceCompany'])) {
											$torderimportdata['InsuranceCompany'] = $Fetch_Request_DetailsdatavalueextractedData['InsuranceCompany'];
										}
										if(isset($Fetch_Request_DetailsdatavalueextractedData['Policy Number'])) {
											$torderimportdata['PolicyNumber'] = $Fetch_Request_DetailsdatavalueextractedData['Policy Number'];
										}
										if(isset($Fetch_Request_DetailsdatavalueextractedData['Insured Location'])) {
											$torderimportdata['InsuredLocation'] = $Fetch_Request_DetailsdatavalueextractedData['Insured Location'];
										}
										if(isset($Fetch_Request_DetailsdatavalueextractedData['Policy Start Date'])) {
											$torderimportdata['PolicyStartDate'] = $Fetch_Request_DetailsdatavalueextractedData['Policy Start Date'];
										}
										if(isset($Fetch_Request_DetailsdatavalueextractedData['Policy Exp Date'])) {
											$torderimportdata['PolicyExpDate'] = $Fetch_Request_DetailsdatavalueextractedData['Policy Exp Date'];
										}
										if(isset($Fetch_Request_DetailsdatavalueextractedData['DwellingAmount'])) {
											$torderimportdata['DwellingAmount'] = $Fetch_Request_DetailsdatavalueextractedData['Dwelling Amount'];
										}

										// if(isset($Fetch_Request_DetailsdatavalueextractedData['Borrower Name'])) {
										// 	$torderimportdata['BorrowerName'] = $Fetch_Request_DetailsdatavalueextractedData['Borrower Name'];
										// }	
										if(isset($Fetch_Request_DetailsdatavalueextractedData['Residence'])) {
											$torderimportdata['Residence'] = $Fetch_Request_DetailsdatavalueextractedData['Residence'];
										}

										if(!empty($torderimportdata)) {
											$this->db->where('OrderUID',$OrderUID);
											$this->db->update('tOrderImport',$torderimportdata);

										}

									}
								}
							}
						}
					}
				}

				$OrderImportDetails = $this->Common_Model->get_row('tOrderImport', ['OrderUID'=>$OrderUID]);


				$getCompanyDetails = $this->db->select('*')->from('mCompanyDetails')->where('CompanyName',$OrderImportDetails->InsuranceCompany)->get()->row();

				//check company is present
				if(empty($getCompanyDetails)) {

					
					$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
					$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);

					/* Change HOI Order notification flag to notified*/
					$this->Common_Model->NotifiedOrder($order->OrderUID);
					echo json_encode(array('Status' => 1,'message'=>'No Company found, Loan will be moved to exception Queue','type' => 'danger'));exit();

				}
				
				$email = $getCompanyDetails->Email;
				$CompanyUID = $getCompanyDetails->CompanyUID;

				if(empty($email)) {

					
					$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
					$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);

					/* Change HOI Order notification flag to notified*/
					$this->Common_Model->NotifiedOrder($order->OrderUID);

					$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
					$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);


					echo json_encode(array('Status' => 1,'message'=>'No email found, Loan will be moved to exception Queue','type' => 'danger'));exit();

				}
				//update hoi company email in checklist
				$HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];
				$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['CompanyName'];
				$checklist = $this->db->select('DocumentTypeUID')->from('tDocumentCheckList')->where(array('DocumentTypeUID'=>$DocumentTypeUID,'WorkflowUID'=>$HoiWorkflowModuleUID,'OrderUID'=>$OrderUID))->get()->row();
				if(!empty($checklist)) {
					$checklistdata = [];
					$checklistdata['Comments'] = $email;
					$this->db->where(array('DocumentTypeUID'=>$DocumentTypeUID,'WorkflowUID'=>$HoiWorkflowModuleUID,'OrderUID'=>$OrderUID));
					$this->db->update('tDocumentCheckList',$checklistdata);

				} else {

					$Category = $this->db->select('CategoryUID')->from('mDocumentType')->where('DocumentTypeUID',$DocumentTypeUID)->get()->row();
					$checklistdata = [];
					$checklistdata['Comments'] = $email;
					$checklistdata['OrderUID'] = $OrderUID; 
					$checklistdata['CategoryUID'] = $Category->CategoryUID; 
					$checklistdata['DocumentTypeUID'] = $DocumentTypeUID; 
					$checklistdata['WorkflowUID'] = $HoiWorkflowModuleUID; 
					$checklistdata['ModifiedUserUID'] = $this->loggedid; 
					$checklistdata['ModifiedDateTime'] = date("d/m/Y H:i:s"); 
					$this->db->insert('tDocumentCheckList',$checklistdata);

				}

				// $this->db->where('OrderUID',$OrderUID);
				// $this->db->delete('tLoanFiles');

				$Docarray = array(
					'IsStacking' => 2
				);
				$this->db->where('DocumentUID',$DocumentUID);
				$this->db->update('tDocuments',$Docarray);


				$loan = array('DocumentUID'=>$DocumentUID,
					'CompanyUID'=>$CompanyUID,
					'OrderUID'=>$OrderUID,
					'OCR'=>$response,
				);

				$this->db->insert('tLoanFiles',$loan);

				$from_email = "notifications@direct2title.com"; 
				//Load email library 
				$this->load->library('email'); 				    
				$this->email->from($from_email);					
				$to_email = $email;
				$subject = 'Mortgagee Clause Change Request - Loan Number : '.$order->LoanNumber;
				$uniqueid = 'MailRef-'.rand();
				$this->email->set_header('ReferenceNumber', $uniqueid);
				$this->email->set_header('OrderUID', $order->OrderUID);
				$this->email->to($to_email);
				$this->email->subject($subject); 
				$mortgage = array('LoanNumber'=> $order->LoanNumber,
					'OrderNumber' => $order->OrderNumber,
					'BorrowerName'=> $order->CustomerName,
					'uniqueid' => $uniqueid);
				$body = $this->load->view('common/mortgage_mail.php',$mortgage,TRUE);

				$this->email->message($body); 

				if($this->email->send()){
					$Elog = array(
						'SenderEmail'=>$from_email,
						'RefID'=>$uniqueid,
						'RecipientEmail'=>$to_email,
						'EmailSubject'=>$subject,
						'EmailBody'=>$body,
						'IsReceived'=>$status,
						'OrderUID'=>$order->OrderUID,
						'OrderNumber'=>$order->OrderNumber,
						'AltOrderNumber' => '',
						'MailReceivedDateTime'=> date('Y-m-d H:i:s')
					);
					$this->db->set($Elog);
					$this->db->insert('tEmailImport');
					// $this->db->insert('tEmailImport',$Elog);
					$EmailUID = $this->db->insert_id();
					
					$QueueUID = $this->config->item('HoiAutomationQueues')['HOIRequest'];
					$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);


					$status =  "Success";	         			
					$res = array('Status' => 0,'message'=>'Email sent successfully ' . $to_email . '! And the loan will be moved to HOI Request queue.','type' => 'success');
					echo json_encode($res);exit();


				}else{
					$status =  "Failure";
					$res = array('Status' => 2,'message'=>'Unable to send mail!','type' => 'danger');
					echo json_encode($res);exit();

				}



			}
			

		} else {
			echo json_encode(array('Status' => 1,'message'=>'File Upload Failed','type' => 'danger'));exit();

		}

	}
	function GetLogResponse(){
		$type = $this->input->post('type');
		$OrderUID = $this->input->post('OrderUID');
		$response = $this->Common_Model->GetLogResponse($orderUID, $type);

	}
}?>
