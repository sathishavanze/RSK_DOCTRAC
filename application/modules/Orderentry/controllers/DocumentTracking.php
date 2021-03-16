<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DocumentTracking extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocumentTracking_Model');
	}

	public function missingOrders()
	{
		$data['content']='missingOrders';
		$data['Users'] = $this->Common_Model->get('mUsers', ['Active'=>STATUS_ONE]);
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function TrackingDocuments_AjaxList()
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		//Advanced Search
		//get_post_input_data
    	$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = trim($search['value']);
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
    	//get_post_input_data

    	if (!isset($post['DocumentStatus'])) {
    		$post['DocumentStatus'] = 'Uploaded';
    	}
    	//column order
        $post['column_order'] = array('tDocumentTracking.DocumentName','mCustomer.CustomerName','mLender.LenderName','mUsers.UserName','tDocumentTracking.UploadedDateTime','tDocumentTracking.DocumentStatus');
        $post['column_search'] = array('tDocumentTracking.DocumentName','mCustomer.CustomerName','mLender.LenderName','mUsers.UserName','tDocumentTracking.UploadedDateTime','tDocumentTracking.DocumentStatus');
        //column order
        $list = $this->DocumentTracking_Model->TrackingOrders($post);


        $no = $post['start'];
        $tDocumentTrackinglist = [];
		foreach ($list as $tDocumentTracking)
        {
		        $row = array();
		        $link = '<a href="'.$tDocumentTracking->DocumentURL.'" class=""><i class="icon-eye"></i> '.$tDocumentTracking->DocumentName.'</a>';
		        $row[] = $link;
		        $row[] = $tDocumentTracking->CustomerName;
		        $row[] = $tDocumentTracking->LenderName;
		        $row[] = $tDocumentTracking->UserName;
		        $row[] = $tDocumentTracking->UploadedDateTime;
		        $row[] = $tDocumentTracking->DocumentStatus;


		        $Action = '<div class="form-check">
                          <label class="form-check-label">
                            <input class="form-check-input DocumentTrackingUID" type="checkbox" data-documenttrackinguid = "'.$tDocumentTracking->DocumentTrackingUID.'" value="">
                            <span class="form-check-sign">
                              <span class="check" style="top: 3px;"></span>
                            </span>
                          </label>
                        </div>
                        ';
		        $row[] = $Action;
		        $tDocumentTrackinglist[] = $row;
        }



        $data =  array(
        	'myorderslist' => $tDocumentTrackinglist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->DocumentTracking_Model->count_all($post),
			"recordsFiltered" =>  $this->DocumentTracking_Model->count_filtered($post),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function Orders_ajax_list()
	{
		// ini_set("display_errors", 1);
		// error_reporting(E_ALL);	
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		//Advanced Search
		//get_post_input_data
    	$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = trim($search['value']);
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
    	//get_post_input_data
    	//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','tOrders.LoanNumber','mLender.LenderName','mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode','tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','mProducts.ProductName','mLender.LenderName', 'tOrders.LoanNumber','mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode', 'mProjectCustomer.ProjectName', 'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
        //column order
        $list = $this->DocumentTracking_Model->OrdersList($post);

        $no = $post['start'];
        $allorderslist = [];
		foreach ($list as $orders)
        {
		        $row = array();
		        $row[] = '<a href="'.base_url('Ordersummary/index/'.$orders->OrderUID).'" class="ajaxload">'.$orders->OrderNumber.'</a>';
		        $row[] = $orders->CustomerName;
		        $row[] = $orders->ProductName;
		        $row[] = $orders->ProjectName;
		        $row[] = $orders->LoanNumber;
		        $row[] = $orders->LenderName;
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$orders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$orders->StatusName.'</a>';
		        $row[] = $orders->PropertyAddress1.' '.$orders->PropertyAddress2;
		        $row[] = $orders->PropertyCityName;
		        $row[] = $orders->PropertyStateCode;
		        $row[] = $orders->PropertyZipCode;
				$row[] = date('m/d/Y H:i:s', strtotime($orders->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($orders->OrderDueDate));

							
		        $Action = '<div class="form-check">
                          <label class="form-check-label">
                            <input class="form-check-input OrderUID" type="radio" name="OrderUID" data-OrderUID = "'.$orders->OrderUID.'" value="option2">
                            <span class="circle">
                              <span class="check"></span>
                            </span>
                          </label>
                        </div>';

		        $row[] = $Action;
		        $allorderslist[] = $row;
        }



        $data =  array(
        	'allorderslist' => $allorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->DocumentTracking_Model->OrdersList_countall(),
			"recordsFiltered" =>  $this->DocumentTracking_Model->OrdersList_count_filtered($post),
			"data" => $data['allorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function AttachDocumentToOrder()
	{
		// ini_set("display_errors", 1);
		// error_reporting(E_ALL);	

		$OrderUID = $this->input->post('OrderUID');
		$DocumentTrackingUIDs = $this->input->post('DocumentTrackingUID');
		$UploadType = $this->input->post('UploadType');
		$AttachmentStatus = [];


		$tOrders=$this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		if (empty($OrderUID) && empty($tOrders)) {
			echo json_encode(['validation_error'=>1, 'message'=>'Invalid Order Given']); exit;
		}

		if (empty($DocumentTrackingUIDs)) {
			echo json_encode(['validation_error'=>1, 'message'=>'No Documents Choosen.']); exit;	
		}

		if (empty($UploadType)) {
			$UploadType = 'Merge';
		}


		foreach ($DocumentTrackingUIDs as $key => $DocumentTrackingUID) {
			$tDocumentTracking= $this->Common_Model->get_row('tDocumentTracking', ['DocumentTrackingUID'=>$DocumentTrackingUID]);

			$status = $this->UploadFile($tDocumentTracking->DocumentURL, $tOrders, $UploadType);
			if ($status['status']==0) {
				$this->Common_Model->save('tDocumentTracking', ['DocumentStatus'=>'Closed'],['DocumentTrackingUID'=>$DocumentTrackingUID]);
			}


			$AttachmentStatus[] = $status['message'];

		}
		echo json_encode(['validation_error'=>0, 'message'=>implode(', ', $AttachmentStatus)]);
	}

	function DocumentExcel()
	{

		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
			$this->DocumentTracking_Model->GetMyOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}

        $list = $this->DocumentTracking_Model->TrackingOrders($post);

        
        $data = [];

        $data[] = array('Document Tracking','Customer','Lender','Uploaded UserName','Uploaded Date','Document Status');

			for ($i=0; $i < sizeof($list); $i++) { 

				$data[] = [$tDocumentTracking->DocumentName,$tDocumentTracking->CustomerName,$tDocumentTracking->LenderName,$tDocumentTracking->UserName,$tDocumentTracking->UploadedDateTime,$tDocumentTracking->DocumentStatus
				];
			}

		$this->outputCSV($data);
    

	}

	public function GetCustomerLenderEmail()
	{
		$DocumentTrackingUID = $this->input->post('DocumentTrackingUID');
		$CustomerEmails = [];
		$LenderEmails = [];

		if (empty($DocumentTrackingUID)) {
			echo json_encode(['validation_error'=>1, 'message'=>'No Documents Provided!']); exit;
		}

		foreach ($DocumentTrackingUID as $key => $docuid) {
			
			$tDocumentTracking = $this->Common_Model->get_row('tDocumentTracking', ['DocumentTrackingUID'=>$docuid]);

			if (!empty($tDocumentTracking) && !empty($tDocumentTracking->CustomerUID)) {
				
				$mCustomer = $this->Common_Model->get_row('mCustomer', ['CustomerUID'=>$tDocumentTracking->CustomerUID]);

				if (!empty($mCustomer) && filter_var($mCustomer->CustomerEmail, FILTER_VALIDATE_EMAIL)) {
					$CustomerEmails[] = $mCustomer->CustomerEmail;
				}

				$mLender = $this->Common_Model->get_row('mLender', ['LenderUID'=>$tDocumentTracking->LenderUID]);

				if (!empty($mLender) && filter_var($mLender->Email, FILTER_VALIDATE_EMAIL)) {
					$LenderEmails[] = $mLender->Email;
				}
			}
		}

		echo json_encode(['validation_error'=>0, 'customeremails'=>$CustomerEmails, 'lenderemails'=>$LenderEmails]);
	}

		function outputCSV($data) 
		{
		  ob_clean();
		  header("Content-Type: text/csv");
		  header("Content-Disposition: attachment; filename=file.csv");
		  $output = fopen("php://output", "w");
		  foreach ($data as $row)
		  {
		    fputcsv($output, $row); // here you can change delimiter/enclosure
		  }
		  fclose($output);
		  ob_flush();
		}

	function UploadFile($File, $tOrders, $UploadType='Merge')
	{

		$this->load->library('MergePDF');
		$this->load->model('Orderentry/Orderentrymodel');

		$Path=FCPATH . 'uploads/OrderDocumentPath/' .$tOrders->OrderNumber . '/';
		$viewPath='uploads/OrderDocumentPath/' .$tOrders->OrderNumber . '/';

		$OrderUID = $tOrders->OrderUID;

		if (!file_exists($File)) {
			return ['status'=>1, 'message'=>'File Not Exist'];
		}

		$ext = pathinfo($File, PATHINFO_EXTENSION);
		$filename = basename($File);

		/*############ Extract and attach files comming in zip format starts ###########*/
		$extracted_files = [];

		if ($ext == 'zip') {
		// Function Call
			$Zip_files = $this->ExtractFilesInFolder($File);

			foreach ($Zip_files as $key => $filename) {
				$extracted_files = $this->Extract_and_GetFiles($File . $filename, $Path);
			}
		}


		$UploaedFiles = [];
		if ($ext == 'pdf') {
			$UploaedFiles[] = $File;
		}



		/*############ Extract and attach files comming in zip format ends ###########*/


		if (count($UploaedFiles) || count($extracted_files)) {


			$files = [];
			$stacking_file_name = "";



					// Get Stacking document details and its name.
			$tDocuments_row = $this->Common_Model->get_row('tDocuments', ['OrderUID'=>$OrderUID, 'IsStacking'=>1]);

			if ($UploadType == 'Merge') {

				if (!empty($tDocuments_row)) {
					$stacking_file_name = $tDocuments_row->DocumentName;

					if (file_exists(FCPATH . $tDocuments_row->DocumentURL)) {
						$files[] = FCPATH . $tDocuments_row->DocumentURL;
					}
				}
				else{

					$stacking_file_name = empty($stacking_file_name) ? empty($tOrders->LoanNumber) ? $tOrders->OrderNumber  . '.pdf' : $tOrders->LoanNumber . '.pdf' : $stacking_file_name;
				}

			}
			foreach ($UploaedFiles as $key => $value) {
						// Collect Uploaded files
				$files[] = $value;
			}


					// Include extracted files.
			foreach ($extracted_files as $key => $file) {
				$files[] = $file;
			}


			$files = array_unique($files);
					// executes if files exists

			if (!empty($files)) {
				$tempfilename = date('YmdHis') . '.pdf';
						// Merge all uploaded files into one single stacking document.
				if($UploadType == 'Merge'){


					if ($this->mergepdf->merge($files, $Path . $tempfilename)) {

						$tDocuments_result = $this->Common_Model->get('tDocuments',['OrderUID'=>$OrderUID, 'IsStacking'=>1]);

							// Remove Previous 
						foreach ($tDocuments_result as $key => $doc) {
							$this->Common_Model->delete('tDocuments','DocumentUID', $doc->DocumentUID);

						}


						// foreach ($files as $key => $file) {
						// 	unlink($file);
						// }

						rename($Path.$tempfilename, $Path.$stacking_file_name);
						/*Save tDocuments*/
						$tDocuments['DocumentName'] = $stacking_file_name;
						$tDocuments['DocumentURL'] = $viewPath . $stacking_file_name;
						$tDocuments['OrderUID'] = $OrderUID;
						$tDocuments['IsStacking'] = 1;
						$tDocuments['TypeofDocument'] = 'Stacking';
						$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
						$tDocuments['UploadedByUserUID'] = $this->loggedid;
						if ($this->Common_Model->IsWorkflowAvailableForDocUpload($OrderUID, $this->config->item('Workflows')['Doc_Check_In'])) {
							$tDocuments['DocumentStorage'] = $this->Common_Model->getAvailableDocumentStorage();

						}


						$this->Orderentrymodel->save('tDocuments', $tDocuments);

						if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

							$status = $this->config->item('keywords')['Image Received'];
							$this->Orderentrymodel->UpdateStatus($OrderUID,$status);						
						}


					}

				}

				else{

					foreach ($files as $key => $file) {

						// Copy file to destination
						copy($file, $Path . basename($file));

						/*Save tDocuments*/
						$tDocuments['DocumentName'] = basename($file);
						$tDocuments['DocumentURL'] = $viewPath . basename($file);
						$tDocuments['OrderUID'] = $OrderUID;
						$tDocuments['IsStacking'] = 0;
						$tDocuments['TypeofDocument'] = 'Others';
						$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
						$tDocuments['UploadedByUserUID'] = $this->loggedid;
						if ($this->Common_Model->IsWorkflowAvailableForDocUpload($OrderUID, $this->config->item('Workflows')['Doc_Check_In'])) {
							$tDocuments['DocumentStorage'] = $this->Common_Model->getAvailableDocumentStorage();
							
						}

						$this->Orderentrymodel->save('tDocuments', $tDocuments);
					}

					if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

						$status = $this->config->item('keywords')['Image Received'];

						$this->Orderentrymodel->UpdateStatus($OrderUID,$status);
					}


				}

			}

		}	
		return ['status'=>0, 'message'=>$filename . ' File Attached'];

	}

	function DeleteDocuments()
	{
		$DocumentTrackingUIDs = $this->input->post('DocumentTrackingUID');

		if (empty($DocumentTrackingUIDs)) {
			echo json_encode(['validation_error'=>1, 'message'=>'No Document Given to Delete']); exit;
		}


		foreach ($DocumentTrackingUIDs as $key => $DocumentTrackingUID) {
			
			$tDocumentTracking = $this->Common_Model->get_row('tDocumentTracking', ['DocumentTrackingUID'=>$DocumentTrackingUID]);

			$this->db->where('DocumentTrackingUID', $DocumentTrackingUID);
			$is_deleted = $this->db->delete('tDocumentTracking');

			if ($is_deleted) {
				if (file_exists($tDocumentTracking->DocumentURL)) {
					unlink($tDocumentTracking->DocumentURL);
				}
			}
		}
		echo json_encode(['validation_error'=>0,'message'=>'Documents Deleted Successfully']);



	}

	function SendEmail()
	{
		// ini_set("display_errors", 1);
		// error_reporting(E_ALL);	

		$this->load->library('email');
		$EmailIDs = $this->input->post('Email');

		if (empty($EmailIDs)) {
			echo json_encode(['validation_error'=>1, 'message'=>'No Email(s) Given']); exit;
		}

		$EmailIDs = explode(";", $EmailIDs);
		$Body = $this->input->post('Body');
		$Subject = $this->input->post('Subject');
		$DocumentTrackingUID = $this->input->post('DocumentTrackingUID');

		$this->email->from($this->config->item('mail_from'));
		foreach ($EmailIDs as $key => $toemail) {
			
			$this->email->to(trim($toemail));
		}

		$this->email->subject($Subject);
		$this->email->message($Body);

		if(!$this->email->send())
		{
			echo json_encode(['validation_error'=>1, 'message'=>$this->email->print_debugger()]);
		}
		else{
			$tDocumentTrackingActivity['DocumentTrackingUID'] = $DocumentTrackingUID;
			$tDocumentTrackingActivity['Status'] = 'mailsend';
			$tDocumentTrackingActivity['ToEmail'] = implode("; ", $EmailIDs);
			$tDocumentTrackingActivity['Subject'] = $Subject;
			$tDocumentTrackingActivity['Body'] = $Body;
			$this->Common_Model->save('tDocumentTrackingActivity', $tDocumentTrackingActivity);
			echo json_encode(['validation_error'=>0, 'message'=>'Email Send Successfully']);
		}

	}

}?>

