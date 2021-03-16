 <?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocumentCheckList extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Ordersummary/Ordersummarymodel');
		$this->lang->load('keywords');
		$this->load->model('DocumentCheckList_Model');
		ini_set('display_errors', 1);

	}	

	public function index()
	{
		$OrderUID = $this->uri->segment(3);
		$data['content'] = 'index';
		$Active  = array('Active' => 1);
		$data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID'=>'ASC'], []);


		$data['Investors'] = $this->Common_Model->get('mInvestors', $Active, ['InvestorUID'=>'ASC'], []);	
		$data['Custodians'] = $this->Common_Model->get('mCustodians', $Active, ['CustodianUID'=>'ASC'], []);
		$data['TPOName'] = $this->db->select('CorrespondentLenderName')->from('tOrderDocumentCheckIn')->where('OrderUID',$OrderUID)->get()->row();
		$data['SettlementAgent'] = $this->Common_Model->get('mSettlementAgent', $Active, ['SettlementAgentUID'=>'ASC'], []);
		$data['BusinessChannel'] = $this->Common_Model->get('mBusinessChannel', $Active, ['BusinessChannelUID'=>'ASC'], []);
		//echo '<pre>';print_r($data['BusinessChannel']);exit;

		$data['OrderSummary'] = $this->Ordersummarymodel->GettOrders($OrderUID);
		$data['BorrowerName'] = $this->DocumentCheckList_Model->GetBorrowerName($OrderUID);
		$data['BorrowerDocumentDetails'] = $this->DocumentCheckList_Model->BorrowerDocumentDetails($OrderUID);
		$data['Documents'] = $this->Ordersummarymodel->GetDocuments($OrderUID);
		$data['OrderDetails'] = $this->Common_Model->getOrderDetails($OrderUID);
		$data['ExceptionList'] = $this->Common_Model->GetOrderExceptions($OrderUID);

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}



	function insert()
	{

		$this->load->library('form_validation');
		// echo '<pre>'; print_r($_POST); exit;
		$data['content'] = 'index';
		if ($this->input->server('REQUEST_METHOD') === 'POST') {

			$this->form_validation->set_error_delimiters('', '');


			$this->form_validation->set_rules('Customer', '', 'required');
			$this->form_validation->set_rules('PropertyAddress1', '', 'required');
			$this->form_validation->set_rules('PropertyCityName', '', 'required');
			$this->form_validation->set_rules('PropertyStateCode', '', 'required');
			$this->form_validation->set_rules('PropertyCountyName', '', 'required');
			$this->form_validation->set_rules('PropertyZipcode', '', 'required');
			$this->form_validation->set_rules('ProjectUID', '', 'required');
			$this->form_validation->set_rules('PriorityUID', '', 'required');

			/*LOOP VALIDATION*/


			$this->form_validation->set_message('required', 'This Field is required');

			if ($this->form_validation->run() == true) {

				$OrderDetails = $this->input->post();
				$UPDATE = [];

			
				
				$result = $this->Ordersummarymodel->insert_order($OrderDetails);


				// foreach ($OrderDetails['DocumentName'] as $key => $value) {
				// 	$tDocument_row['DocumentUID'] = $value;
				// 	if (isset($OrderDetails['IsStacking'][$key])) {
				// 		$tDocument_row['TypeofDocument'] = 'Stacking';
				// 	}
				// 	else{
				// 		$tDocument_row['TypeofDocument'] = $OrderDetails['TypeofDocument'][$key];
				// 	}
				// 	$tDocument_row['IsStacking'] = isset($OrderDetails['IsStacking'][$key]) ? 1 : 0;
				// 	$UPDATE[] = $tDocument_row;
				// }
				if (!empty($UPDATE)) {
					$this->db->update_batch('tDocuments', $UPDATE, 'DocumentUID');
				}
				$Path=FCPATH . 'uploads/OrderDocumentPath/' .$result['OrderNumber'] . '/';
				$viewPath= 'uploads/OrderDocumentPath/' .$result['OrderNumber'] . '/';

				// Executes entire block when file is uploaded.
				if (isset($_FILES['DocumentFiles']) && count($_FILES['DocumentFiles'])) {
					$this->Ordersummarymodel->CreateDirectoryToPath($Path);
					$UploaedFiles = $this->UploadFileToPath($_FILES['DocumentFiles'], $Path);					
					
					$OrderUID=$result['OrderUID'];
	
					foreach ($UploaedFiles as $key => $File) {
						if (isset($File['file_name'])) {						
							/*Save tDocuments*/
							$tDocuments['DocumentName'] = $File['file_name'];
							$tDocuments['DocumentURL'] = $viewPath . $File['file_name'];
							$tDocuments['OrderUID'] = $OrderUID;
							$tDocuments['IsStacking'] = isset($OrderDetails['Stacking'][$key]) ? 1 : 0;
							$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
							$tDocuments['UploadedByUserUID'] = $this->loggedid;
							$this->Ordersummarymodel->save('tDocuments', $tDocuments);
						}
					}
					$status = $this->config->item('keywords')['Image Received'];
					$this->Ordersummarymodel->UpdateStatus($OrderUID,$status);
				}





				$result = array("validation_error" => 0, "id" => '', 'message' => $result['message']);
				echo json_encode($result);

			} else {

				$Msg = $this->lang->line('Empty_Validation');

				$formvalid = [];

				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'Customer' => form_error('Customer'),
					'PropertyAddress1' => form_error('PropertyAddress1'),
					'PropertyCityName' => form_error('PropertyCityName'),
					'PropertyStateCode' => form_error('PropertyStateCode'),
					'PropertyCountyName' => form_error('PropertyCountyName'),
					'PropertyZipcode' => form_error('PropertyZipcode'),
					'LoanNumber' => form_error('LoanNumber'),
					'ProjectUID' => form_error('ProjectUID'),
					'PriorityUID' => form_error('PriorityUID'),
				);
				// $datas = array_merge($datas1, $datas2, $formvalid);
				// $Merged = array_merge($datas, $data);
				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}
	}
function SaveDocument(){	
$post = $this->input->post();
$BorrowerName =  $this->input->post('BorrowerName');
$BorrowerNameArray = explode(',',$BorrowerName);
//echo '<pre>';print_r($BorrowerNameArray);exit;
//$data=array('BorrowerName'=> ($post['BorrowerName']));
//$OrderUID = $this->uri->segment(3);
// echo '<pre>';print_r($post);exit;
$result=$this->DocumentCheckList_Model->GetDocumentCheck($post,$BorrowerNameArray);
$this->DocumentCheckListPDF($post['OrderUID']);
echo json_encode($result);exit();
}

	function text_preview_bulkentry()
	{
		// echo '<pre>';print_r('test');exit;
		if ($this->input->post('bulk_order_details') != '') {
			
			$inputdata = $this->input->post('bulk_order_details');
			$returnvalue = false;
			$orders_yts = 0;
			$duplicate_order_id = 0;
			$duplicate_row = 0;
			$column_empty = 0;
			$element_empty = 0;
			
			
			$CustomerUID = $this->input->post('CustomerUID');
			$ProjectUID = $this->input->post('ProjectUID');
			
			
			
			
			if ($CustomerUID == '' ) {
				echo json_encode(array('error' => '1', 'message' => 'Select the Required Fields'));
				exit;
			}
			
			
			
			
			
			
			$arrayCode = array();
			$rows = explode("\n", $inputdata);
			$rows = array_filter($rows);
			
			
			foreach ($rows as $idx => $row) {
				$row = explode("\t", $row);
				
				//to get rid of first item (the number)
				//comment it if you don't need.
				//array_shift ( $row );
				
				foreach ($row as $field) {
					//to clean up $ sign
					$arrayCode[$idx][0] = '';
					$arrayCode[$idx][1] = '';
					
					$field = trim($field, "$ ");
					
					$arrayCode[$idx][] = $field;
				}
			}
			
			
			
			$ProjectCheck = [];
			
			foreach ($arrayCode as $i => $v) {

				$ProjectCheck[$i] = false;

				$msubproducts = array();
				
				
				if ($CustomerUID!='' && $ProjectUID!='') {
					$CustomerProject=$this->Common_Model->GetCustomerandProject_row($CustomerUID, $ProjectUID);
					$arrayCode[$i][0] = $CustomerProject->CustomerName;
					$arrayCode[$i][1] = $CustomerProject->ProjectName;
					$ProjectCheck[$i] = true;					
				}
				elseif ($CustomerUID!='' && $v[2]!='') {
					$CustomerProject = $this->Common_Model->GetCustomerandProject_rowByName($CustomerUID, $v[2]);
					$arrayCode[$i][0] = $CustomerProject->CustomerName;
					$arrayCode[$i][1] = $CustomerProject->ProjectName;
					$ProjectCheck[$i] = true;
				}
				else{
					$ProjectCheck[$i] = false;
				}


			}
			
			$headingcount = 0;
			$counts = 0;
			foreach ($arrayCode as $key => $value) {
				
				
				if (count($value) > $counts) {
					$counts = count($value);
					$headingcount = count(array_chunk($value, 6));
				}
			}
			
			$tableheadcount = $headingcount - 3;
			?>

			<?php
			
		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please Fill the Required Field'));
		}
		
	}



	function verify_password()
	{
		$password = md5($this->input->post('Password'));
		$result = $this->Common_Model->Verify_Password($this->loggedid, $password);
		if ($result) {
			$res = array('validation_error' => 1, 'message' => 'Template Changed');
		} else {
			$Msg = $this->lang->line('Error');
			$res = array("validation_error" => 0, 'message' => $Msg);
		}
		echo json_encode($res);
	}





	/* ----- SUPPORTING FUNCTIONS STARTS ---- */
	function UploadFileToPath($files, $Path)
	{
		if (!file_exists($Path)) {
			if (!mkdir($Path, 0777, true)) die('Unable to create directory');
		}
		
		$config['upload_path'] = $Path;
		$config['allowed_types'] = 'pdf';
		$config['max_size'] = 0;
		$config['overwrite'] = true;
		
		$this->load->library('upload', $config);
		
		$DocumentFiles = [];
		$Errors = [];
		foreach ($files['name'] as $key => $image) {
			$_FILES['DocumentFiles[]']['name'] = $files['name'][$key];
			$_FILES['DocumentFiles[]']['type'] = $files['type'][$key];
			$_FILES['DocumentFiles[]']['tmp_name'] = $files['tmp_name'][$key];
			$_FILES['DocumentFiles[]']['error'] = $files['error'][$key];
			$_FILES['DocumentFiles[]']['size'] = $files['size'][$key];
			
			$fileName = $files['name'][$key];
			
            // $DocumentFiles[] = $fileName;
			
			$config['file_name'] = $fileName;
			
			$this->upload->initialize($config);
			
			if ($this->upload->do_upload('DocumentFiles[]')) {
				$DocumentFiles[] = $this->upload->data();
			} else {
			$DocumentFiles[] = $this->upload->display_errors();
			}
		}
		return $DocumentFiles;
	}
	/* ----- SUPPORTING FUNCTIONS ENDS ---- */
	function DeleteExistingDocument()
	{
		$documentuid = $this->input->post('documentuid');
		$result = $this->Ordersummarymodel->DeleteExistingDocument($documentuid);
		if ($result) {
			$res = array('validation_error' => 1, 'message' => 'Document Deleted','color' => 'success');
		} else {
			$Msg = $this->lang->line('Error');
			$res = array("validation_error" => 0, 'message' => $Msg,'color' => 'danger');
		}
		echo json_encode($res);
		
	}
	
	public function switchdocumentstatus()
	{
		$DocumentUID = $this->input->post('DocumentUID');
		$OrderUID = $this->input->post('OrderUID');
		$switch = $this->input->post('Switch');
		$response = [];
		if ($DocumentUID == '') {
			$this->output->set_content_type('application/json')
						->set_output(json_encode(['validation_error'=>1, 'message'=>'Invalid Document','color'=>'danger']))->_display(); exit;
			
		}

		switch ($switch) {
			case 'on':
				$this->Common_Model->save('tDocuments', ['IsStacking' => 0], ['OrderUID' => $OrderUID]);
				$this->Common_Model->save('tDocuments', ['IsStacking' => 1, 'TypeofDocument'=>'Stacking'], ['DocumentUID' => $DocumentUID]);
				$this->RemoveStackingProperty($OrderUID);
				$response['validation_error'] = 0;
				$response['message'] = "Status Changed";
				$response['color'] = "success";
				break;
			case 'off':
				$this->Common_Model->save('tDocuments', ['IsStacking' => 0], ['DocumentUID' =>$DocumentUID]);
				$response['validation_error'] = 0;
				$response['message'] = "Status Changed";
				$response['color'] = "success";
				break;
			default:
				$response['validation_error'] = 1;
				$response['message'] = "Something went wrong";
				$response['color'] = "danger";
				break;
		}

		$this->output->set_content_type('application/json')
					->set_output(json_encode($response));

	}
	
	public function changedocumenttype()
	{
		$DocumentUID = $this->input->post('DocumentUID');
		$value = $this->input->post('value');
		$OrderUID = $this->input->post('OrderUID');
		$response = [];
		if ($DocumentUID == '') {
			$this->output->set_content_type('application/json')
						->set_output(json_encode(['validation_error'=>1, 'message'=>'Invalid Document','color'=>'danger']))->_display(); exit;
			
		}
		if($value == 'Stacking'){
		$this->Common_Model->save('tDocuments', ['IsStacking' => 0], ['OrderUID' => $OrderUID]);
		$this->Common_Model->save('tDocuments', ['TypeofDocument' => 'Others'], ['OrderUID' => $OrderUID, 'TypeofDocument'=>'Stacking']);
		$this->Common_Model->save('tDocuments', ['IsStacking' => 1, 'TypeofDocument' => 'Stacking'], ['DocumentUID' => $DocumentUID]);
		$this->RemoveStackingProperty($OrderUID);
		}
		$result = $this->Common_Model->save('tDocuments', ['TypeofDocument' => $value], ['DocumentUID' => $DocumentUID]);
		if($result){
			$response['validation_error'] = 0;
			$response['message'] = "Document Type Changed";
			$response['color'] = "success";			
		}
		else{
			$response['validation_error'] = 1;
			$response['message'] = "Something went wrong";
			$response['color'] = "danger";
		}


		$this->output->set_content_type('application/json')
					->set_output(json_encode($response));

	}

	function RemoveStackingProperty($OrderUID)
	{
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

		if (file_exists(FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber .'/'. $tOrders->OrderNumber . '_Stacked.pdf')) {
			unlink(FCPATH. 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber .'/'. $tOrders->OrderNumber . '_Stacked.pdf');
		}

		
		$this->Common_Model->delete('tCategory', 'OrderUID', $OrderUID);
		$this->Common_Model->delete('tSubCategory', 'OrderUID', $OrderUID);
		$this->Common_Model->delete('tPage', 'OrderUID', $OrderUID);
		$this->Common_Model->delete('tDocuments', 'DocumentName', $tOrders->OrderNumber . '_Stacked.pdf');
		return true;
	}

	function isnullcheck($input)
	{
		return $input!=''?$input:'-';
	}

	function DocumentCheckListPDF($OrderUID)
	{

		$img =$this->BarcodeGenerator($OrderUID, 'OrderNumber');
		$LN_Barcode_img =$this->BarcodeGenerator($OrderUID, 'LoanNumber');
		$PackageNo_Barcode_img =$this->BarcodeGenerator($OrderUID, 'PackageNumber');
		if (!empty($LN_Barcode_img)) {
			$LN_Barcode_img = "<img src='".$LN_Barcode_img."'>";
		}
		$OrderNumber= $this->db->select('OrderNumber')->from('tOrders')->where('OrderUID',$OrderUID)->get()->row('OrderNumber');
		$OrderEnteryDate= $this->db->select('OrderEntryDateTime')->from('tOrders')->where('OrderUID',$OrderUID)->get()->row('OrderEntryDateTime');
		 $AssignedUser=$this->db->select('UserName')->from('mUsers')->where('UserUID',$this->loggedid)->get()->row('UserName');
		$ReferenceDocumentID= $this->db->select('DocumentName')->from('tDocuments')->where('OrderUID',$OrderUID)->where('IsStacking',1)->get()->row('DocumentName');
		$this->db->select('mInputDocType.*')->from('tOrders');
		$this->db->join('mInputDocType', 'tOrders.InputDocTypeUID=mInputDocType.InputDocTypeUID', 'left');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$DocTypeName = $this->db->get()->row('DocTypeName');

		$data['content'] = 'index';
		$Customers = $this->Common_Model->get('mCustomer', [], ['CustomerUID'=>'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID'=>'ASC'], []);
		$OrderSummary = $this->Ordersummarymodel->GettOrders($OrderUID);
		$ClientCode = "";
		$ClientName = "";
		foreach ($Customers as $row) {
			if($row->CustomerUID == $OrderSummary->CustomerUID)
			{
				$ClientName = $row->CustomerName;
				$ClientCode = $row->CustomerCode;
			}
		}
		
		$BorrowerName = $this->DocumentCheckList_Model->GetBorrowerName($OrderUID);
		//echo '<pre>';print_r($BorrowerName);exit;
		$BorrowerNameList = '';
		$no = 0;
		foreach ($BorrowerName as $row) {
			$row     = get_object_vars($row);
			$BorrowerNameList .= $row['BorrowerFirstName'].',';
			$no++;
		}
		$BorrowerNameList = rtrim($BorrowerNameList,',');
		
		//echo '<pre>';print_r($data['BorrowerName']);exit;
		$data['Documents'] = $this->Ordersummarymodel->GetDocuments($OrderUID);
		$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID);
		$data['ExceptionList'] = $this->Common_Model->GetOrderExceptions($OrderUID);
		$ProjectCode = $this->db->select('ProjectCode')->from('mProjectCustomer')->where('ProjectUID',$OrderDetails->ProjectUID)->get()->row('ProjectCode');
		$BorrowerDocumentDetails = $this->DocumentCheckList_Model->BorrowerDocumentDetails($OrderUID);
		$FolderName = $this->DocumentCheckList_Model->getFolderIDs($OrderUID);
		foreach ($BorrowerDocumentDetails as $DocumentDetails) {
			if (!empty($DocumentDetails->ClosingDateTime)) {
				$ClosingDateTime = date('m-d-Y',strtotime($DocumentDetails->ClosingDateTime));
			}
			else{
				$ClosingDateTime = "";
			}

			if (!empty($DocumentDetails->DisbursementDate)) {
				$DisbursementDate = date('m-d-Y',strtotime($DocumentDetails->DisbursementDate));
			}
			else{
				$DisbursementDate = "";
			}
			$SettlementAgentName = $DocumentDetails->SettlementAgentName;
			$FileNumber = $DocumentDetails->FileNumber;
			$LoanAmount = $DocumentDetails->LoanAmount;
			$RecordingFeesDeed = $DocumentDetails->RecordingFeesDeed;
			$RecordingFeesMortgage = $DocumentDetails->RecordingFeesMortgage;
			$LoanPurpose = $DocumentDetails->LoanPurpose;
			$LoanType = $DocumentDetails->LoanType;
			$IncomingTrackingNumber = $DocumentDetails->IncomingTrackingNumber;
		}    
        $this->load->library('pdf');

        $doc = '';
        $doc .= '
        <style type="text/css">
        	td{
        		font-family: "Arial", "serif";
        		font-size: 11px;
        	}

        
        </style>
        <div>
        <div style="float:left; width:50%">
        <div><img src="'.$this->isnullcheck($PackageNo_Barcode_img) .'"></div>
        </div>
        <div style="float:right;width:50%">
        </div>
        </div>
        <table width="70%;">
        <thead>
        	<tr>
        		<td>Reference Loan Number</td>
        			<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderNumber).'</td>
        		
        	</tr>
        	<tr>
        		<td>Package No</td>
        			<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderDetails->PackageNumber).'</td>
        		
        	</tr>
        	<tr>
        		<td>Loan No</td>
        			<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderDetails->LoanNumber).'</td>
        		
        	</tr>
        	<tr>
        		<td>Reference Document ID</td>
        			<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($DocTypeName).'</td>
        	</tr>
        	<tr>
        		<td>Client Name</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($ClientName).'</td>

        	</tr>
        	<tr>
        		<td>Client Code</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($ClientCode).'</td>
        	</tr>
        	<tr>
        		<td>Channel Identifier</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderDetails->BussinessChannel).'</td>
        	</tr>
        	<tr>
        		<td>Folder name</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($FolderName).'</td>
        	</tr>
        	<tr>
        		<td>Project Name</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderDetails->ProjectName).'</td>
        	</tr>
        	<tr>
        		<td>Project Code</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($ProjectCode).'</td>
        	</tr>
        	<tr>
        		<td>Loan Number</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderSummary->LoanNumber).'</td>
        	</tr>
        	<tr>
        		<td>Borrower Name</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($BorrowerNameList) .'</td>
        	</tr>
        	<tr>
        		<td>Closing Date</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($ClosingDateTime).'</td>
        	</tr>
        	<tr>
        		<td>Disbursement Date</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($DisbursementDate).'</td>
        	</tr>
        	<tr>
        		<td>Settlement Agent Name</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($SettlementAgentName).'</td>
        	</tr>
        	<tr>
        		<td>TPO Company Name</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderDetails->CorrespondentLenderName).'</td>
        	</tr>
        	<tr>
        		<td>File Number</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($FileNumber).'</td>
        	</tr>
        	<tr>
        		<td>Property Address</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderSummary->PropertyAddress1).', '.$this->isnullcheck($OrderSummary->PropertyAddress2).','.$this->isnullcheck($OrderSummary->PropertyCityName).', '.$this->isnullcheck($OrderSummary->PropertyZipCode).'</td>
        	</tr>
        	<tr>
        		<td>Property County</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderSummary->PropertyCountyName).'</td>
        	</tr>
        	<tr>
        		<td>Lender Name</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderDetails->LenderName).'</td>
        	</tr>
        	<tr>
        		<td>Loan Amount</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderSummary->LoanAmount).'</td>
        	</tr>
        	<tr>
        		<td>Funding Date</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($OrderDetails->FundingDate).'</td>
        	</tr>
        	<tr>
        		<td>User Name- Check in</td>
        		<td style="font-size:15px">:</td>
        		<td>'.$this->isnullcheck($AssignedUser).'</td>
        	</tr>
        	<tr>
        		<td>Received Date</td>
        		<td style="font-size:15px">:</td>
        		<td>'.date('m/d/Y H:i:s', strtotime($this->isnullcheck($OrderEnteryDate))).'</td>
        	</tr>

        </table>
        <div>
        <div style="float:left; width:50%">
        <div><img src="'.$img .'"></div>
        </div>
        <div style="float:right;width:50%">
        <div style="text-align:right;">'.$LN_Barcode_img.'</div>
        </div>
        </div>';

        $SavePreviewOrderNumber = ($OrderSummary->LoanNumber != '' ? $OrderSummary->LoanNumber .'_Doc Check-in.pdf' : $OrderSummary->OrderNumber.'_Doc Check-in.pdf');
        //echo '<pre>';print_r($SavePreviewOrderNumber);exit;
        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        unset($pdf);
        $pdf = $this->pdf->load($param);
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 0;
        // $pdf->list_indent_first_level = 0;
        $html = mb_convert_encoding($doc, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber;
        $folderpath = 'uploads/OrderDocumentPath/'.$OrderSummary->OrderNumber.'/';
        if ($OrderSummary->IsMovedToS3 == 1) {
			$folderpath = 'S3_uploads/OrderDocumentPath/'.$OrderSummary->OrderNumber.'/';
        }
        if (!file_exists(FCPATH. $folderpath)) {
            mkdir(FCPATH. $folderpath, 0777, true); 
        }
        $date = new DateTime();
        $dir = FCPATH. $folderpath ;

        $path = $folderpath . $SavePreviewOrderNumber;
        // echo $dir;
        // echo $path;
        // echo $doc_save;

        $pdf->Output($dir.$doc_save, '');

        // file_put_contents($dir.$SavePreviewOrderNumber,file_get_contents($doc_save));
        // unlink($doc_save);
        $PDF_Data['DocumentName']= $SavePreviewOrderNumber;
        $PDF_Data['DocumentURL']= $path;
        $PDF_Data['OrderUID']= $OrderUID;
        $PDF_Data['IsStacking']= '0';
        $PDF_Data['TypeofDocument']= 'Others';
        $PDF_Data['UploadedByUserUID']= $this->loggedid;
        $PDF_Data['UploadedDateTime']= $date->format('Y-m-d H:i:sP');

        if ($this->Common_Model->IsWorkflowAvailableForDocUpload($OrderUID, $this->config->item('Workflows')['Doc_Check_In'])) {
        	$PDF_Data['DocumentStorage'] = $this->Common_Model->getAvailableDocumentStorage();
        }



       	$DocumentCheckListPDF = $this->DocumentCheckList_Model->insertDocumentCheckListPDF($PDF_Data);
       	if($DocumentCheckListPDF == 1)
       	{
       		return 1;
       	}
       	else
       	{
       		return 0;
       	}

	}
	public function BarcodeGenerator($OrderUID, $Field)
	{
		$this->db->select($Field);
		$this->db->from('tOrders');
		$this->db->join('tOrderPackage', 'tOrderPackage.PackageUID=tOrders.PackageUID', 'left');
		$this->db->where('OrderUID',$OrderUID);
		$OrderNumber = $this->db->get()->row($Field);
		//$mProjects = $this->Printing_Orders_Model->GetmProjectByProjectUID($torders->ProjectUID);
		if (empty($OrderNumber)) {
			return false;
		}
		$code = $OrderNumber;
		//load library
		// echo 'Entired';exit;
		$this->load->library('Zend');

		//load in folder Zend
		$this->zend->load('Zend/Barcode');

		//generate barcode
		$barcode = Zend_Barcode::draw('code128', 'image', array('text' => $code), array());

		ob_start();
		imagepng($barcode);
		imagedestroy($barcode);
         // Capture the output
		$imagedata = ob_get_contents();
         // Clear the output buffer
		ob_end_clean();
		$imageData = base64_encode($imagedata);

		return $src = 'data:image/png;base64,' . $imageData;


	}

	public function BarcodePreview()
	{
		$code = "Ln0001";
		//load library
		// echo 'Entired';exit;
		$this->load->library('Zend');

		//load in folder Zend
		$this->zend->load('Zend/Barcode');

		//generate barcode
		$barcode = Zend_Barcode::draw('code128', 'image', array('text' => $code), array());

		ob_start();
		imagepng($barcode);
		imagedestroy($barcode);
         // Capture the output
		$imagedata = ob_get_contents();
         // Clear the output buffer
		ob_end_clean();
		$imageData = base64_encode($imagedata);

		$src = 'data:image/png;base64,' . $imageData;

		echo '<img src="'.$src.'" >';


	}

	function Document_List()
	{
		$OrderUID=$this->input->post('OrderUID');
		$DocumentDetails=$this->DocumentCheckList_Model->getDocument($OrderUID);
		//$list.="<div class='dropdown-menu dropdown-menu-right DocumentList' aria-labelledby='navbarDropdownMenuLink'>";
		foreach ($DocumentDetails as $key => $value) {
			$list.="<a class='dropdown-item' href='".base_url().$value->DocumentURL."' target='_blank'>".$value->DocumentName."<span style='font-size:10px;'>&nbsp;(".$value->DocTypeName.")</span></a>";
		}
		//$list.="</div>";
		echo $list;
	}
}?>
