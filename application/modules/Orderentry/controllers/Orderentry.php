<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class OrderEntry extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Orderentrymodel');
		$this->load->model('Common_Model');
		$this->load->model('Submissions_Orders_Model');
		$this->load->model('Customer/Customer_Model');
		$this->load->model('OrderComplete/OrderComplete_Model');
		$this->load->model('Cron/Cron_model');

	}	

	public function index()
	{
		
		$data['content'] = 'index';

		$data['Customers'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$data['ProductsDetails'] =$this->Common_Model->ProductsDetails();
		$data['ProjectDetails'] =$this->Common_Model->ProjectDetails();
		$data['OrderPriority'] =$this->Common_Model->get('mOrderPriority',['Active'=>1]);
		$data['SettlementAgent'] =$this->Common_Model->get('mSettlementAgent',['Active'=>1]);
		//Get State
		$data['GetLoanTypeDetails'] = $this->Common_Model->GetLoanTypeDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function GetLoanTypeDetails() {
		$CustomerUID = $this->input->post('CustomerUID');
		$LoanTypeDetails = $this->Common_Model->GetLoanTypeDetails($CustomerUID);
		echo json_encode($LoanTypeDetails);
	}

	public function bulkentry()
	{
		
		$data['content'] = 'bulkentry';

		$data['Customers'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$data['ProductsDetails'] =$this->Common_Model->ProductsDetails();
		$data['ProjectDetails'] =$this->Common_Model->ProjectDetails();
		$data['OrderPriority'] =$this->Common_Model->get('mOrderPriority',['Active'=>1]);
		$data['SettlementAgent'] =$this->Common_Model->get('mSettlementAgent',['Active'=>1]);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}



	function insert()
	{


		$this->load->library('form_validation');
		$this->form_validation->CI =& $this;

		$this->load->library('MergePDF');

		$data['content'] = 'index';

		if ($this->input->server('REQUEST_METHOD') === 'POST') {

			$matching_files = [];
			$Products = $this->input->post('ProductUID');
			$PropertyRoleUID = $this->input->post('PropertyRoleUID');
			$this->form_validation->set_error_delimiters('', '');


			$this->form_validation->set_rules('Customer', '', 'required');
			/*$this->form_validation->set_rules('PropertyAddress1', '', 'required');
			$this->form_validation->set_rules('PropertyCityName', '', 'required');*/
			/*$this->form_validation->set_rules('PropertyStateCode', '', 'required');*/
			/*$this->form_validation->set_rules('PropertyCountyName', '', 'required');
			$this->form_validation->set_rules('PropertyZipcode', '', 'required');*/
			if(!in_array($this->input->post('Customer'),$this->config->item('Loantypeexcludedclients'))) {
				$this->form_validation->set_rules('LoanType', '', 'required');
			}
			
			$this->form_validation->set_rules('ProductUID', '', 'required');
			$this->form_validation->set_rules('ProjectUID', '', 'required');

			$this->form_validation->set_rules('LoanNumber','LoanNumber','callback_check_loannumber');

			$this->form_validation->set_message('required', 'This Field is required');


			if ($this->form_validation->run() == true) {

				$OrderDetails = $this->input->post();

				$result = $this->Orderentrymodel->insert_order($OrderDetails);

				$Msg = $this->lang->line('Order_Save');
				$Rep_msg = str_replace("<<Order Number>>", $result['OrderNumber'], $Msg);

				$result = array("validation_error" => 0, "id" =>  $result['OrderUID'], 'message' => $Rep_msg, 'matching_files'=>$matching_files);
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
					/*'PropertyStateCode' => form_error('PropertyStateCode'),*/
					'PropertyCountyName' => form_error('PropertyCountyName'),
					'PropertyZipcode' => form_error('PropertyZipcode'),
					'LoanNumber' => form_error('LoanNumber'),
					'Single-ProductUID' => form_error('ProductUID'),
					'Single-ProjectUID' => form_error('ProjectUID'),
					'LoanType' => form_error('LoanType'),


				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}
	}



		/**
	* Multi-array search
	*
	* @param array $array
	* @param array $search
	* @return array
	*/
	function multi_array_search($array, $search, $ignorekey)
	{

    // Create the result array
		$result = array();

    // Iterate over each array element
		foreach ($array as $key => $value)
		{

			if($key == $ignorekey){
				continue;
			}

      // Iterate over each search condition
			foreach ($search as $k => $v)
			{

        // If the array element does not meet the search condition then continue to the next element
        $smallcasevalue = strtolower($value->$k);
				if ((!isset($smallcasevalue)) || (strtolower($smallcasevalue) != strtolower($v)))
				{
					continue 2;
				}

			}

      // Add the array element's key to the result array
			if(!empty($search)){

				$result[] = $key;
			}

		}

    // Return the result array
		return $result;

	}

	/* ---- EXCEL BULK ENTRY STARTS --- */
	function preview_bulkentry()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames = $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");

			$extension = end($temp);

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				$LenderUID = $this->input->post('LenderUID');


				$FileUploadPreview = [];


				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the Required Fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkImportFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
					$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->column_variables($BulkImportFormat);


				$objWorksheet = $objPHPExcel->getActiveSheet();
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$validationarray = array();

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} 
					// skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}

				}

				array_unshift($headingsArray, 'Rejection Details');

				$posts = [];

				$loannumbercolumns = []; 

				foreach ($arrayCode as $keys => $a) {

					if (count($a) == $columnvariables['TotalCount']) {

						$additional['RejectionType'] = '';
						$a['ColorCode'] = '';
						$a['BGColorCode'] = '';

						/*LOAN DUPLICATE VALIDATION*/
						$loanvalidation = $this->Orderentrymodel->is_loanno_exists($a[$columnvariables['Loan Number']]);

						if(!empty($a[$columnvariables['Loan Number']]) && !empty($Customerrow->LoanNumberValidation) && strlen($a[$columnvariables['Loan Number']]) != $Customerrow->LoanNumberValidation) {
							$additional['RejectionType'] = 'Loan Number should be '.$Customerrow->LoanNumberValidation.' characters';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff04ec';
						} else if(in_array($a[$columnvariables['Loan Number']], $loannumbercolumns) || $loanvalidation) {
							$additional['RejectionType'] = 'Duplicate Loan Number';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff04ec';
						}
						

					} else {
						$additional['RejectionType'] = 'Invalid Format';
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
					}

					$a = array_merge($additional,$a);
					$arayhgen = [];
					foreach ($a as $key => $value) {
						$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$posts[] = $arayhgen;
					$loannumbercolumns[] = $a[$columnvariables['Loan Number']];
				}


				$response['data'] = $posts;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'results.json', 'w');
					fwrite($fp, json_encode($response));
					fclose($fp);				
				}
				$data['headingsArray'] = $headingsArray;

				$preview = $this->load->view('standard_bulk_partialviews/bulk_preview', $data, true);

				$filelink = 'uploads/'.$this->loggedid.'/results.json';

				echo json_encode(array('error' => 0, 'html' => $preview, 'filelink' => $filelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
		}

	}

	//save bulkentry function
	function save_bulkentry()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames= $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");
			$extension = end($temp);

		// files checking & handling varaiables
			$matching_files = []; $followup_orders = [];

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				



				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the required fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkImportFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
					$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->column_variables($BulkImportFormat);

				$objWorksheet = $objPHPExcel->getActiveSheet();
						//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];


				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}



				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
				} // skip empty row
				++$r;


				$i = 0;
				foreach ($headingsArray as $columnKey => $columnHeading) {
					$cellformat = $objWorksheet->getCell($columnKey . $row);
					if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

						$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

					}else{
						$arrayCode[$r][$i] = trim($cellformat->getValue());
					}
					$i++;
				}
			}





			$html = '';
			$FailedData = [];
			$SuccessData = [];
			$InsertedOrderUID = [];
			$InsertedOrderUIDs = '';

			$loannumbercolumns = []; 
			$propertyroledata = []; 

			array_unshift($headingsArray, 'Order Number');
			array_unshift($headingsArray, 'Rejection Details');

			foreach ($arrayCode as $i => $a) {
				
				$data = []; 

				/*LOAN DUPLICATE VALIDATION*/

				//for missing fields
				$data['OrderNumber'] = '';
				$data['CustomerUID'] = !empty($CustomerUID) ? $CustomerUID : null;
				$data['ProductUID'] = !empty($ProductUID) ? $ProductUID : null;
				$data['ProjectUID'] =  !empty($ProjectUID) ? $ProjectUID : null;

				$torderimport = [];
				if ($BulkImportFormat == "LOP-Assignment") {

					$mMilestone = $this->Common_Model->get_row('mMilestone', ['MilestoneName'=>trim(ltrim($a[$columnvariables['Milestone']], "0"))]);
					$data['LoanNumber'] = $a[$columnvariables['Loan Number']];
					$data['LoanAmount'] = $a[$columnvariables['Loan Amount']];

					if (!empty($mMilestone)) {
						$data['MilestoneUID'] = $mMilestone->MilestoneUID;
					}

					$data['LoanType'] = $a[$columnvariables['LoanType']];
					$data['PropertyCountyName'] = $a[$columnvariables['County']];
					$data['PropertyStateCode'] = $a[$columnvariables['State']];
					$data['PropertyZipCode'] = $a[$columnvariables['Property Zip Code']];

					$propertyroledata = [];
					/*for ($i = 1; $i <= 5; $i++){*/
						if($a[$columnvariables['Borrower Name']] ) 
						{		
							$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['Borrower Name']];
							$propertyroledata[$i]['BorrowerMailingAddress1'] = "";
							$propertyroledata[$i]['HomeNumber'] = "";
							$propertyroledata[$i]['WorkNumber'] = "";
							$propertyroledata[$i]['CellNumber'] = $a[$columnvariables['Borrower Cell Number']];
							$propertyroledata[$i]['Social'] = "";

						}
					/*}*/


					$torderimport['LoanProcessor'] = $a[$columnvariables["Processor"]];
					$torderimport['SubStatusLastChangedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SubStatus last changed date"]]);
					$torderimport['LockExpiration'] = $a[$columnvariables["Lock Expiration"]];
					$torderimport['EarliestClosingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Earliest Closing date"]]);
					$torderimport['ClosedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closed date"]]);
					$torderimport['LEDisclosureDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LE disclosure date"]]);
					$torderimport['ClosingDisclosureSendDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closing Disclosure sent date"]]);
					$torderimport['DocsOutDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["DocsOutDate"]]);
					$torderimport['SigningDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDate"]]);
					$torderimport['SigningTime'] = $a[$columnvariables["SigningTime"]];
					$torderimport['SigningDateTime'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDateTime"]]);
					$torderimport['QueueDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["QueueDate"]]);
					$torderimport['NextPaymentDue'] = $a[$columnvariables["Next Payment Due"]];
					$torderimport['NSMServicingLoanNumber'] = $a[$columnvariables["NSM Serviving Loan number"]];
					$torderimport['DaysinStatus'] = $a[$columnvariables["DaysinStatus"]];
					$torderimport['ProcAssignDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ProcAssignDate"]]);
					$torderimport['FinalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["FinalApprovalDate"]]);
					$torderimport['PropertyType'] = $a[$columnvariables["Property Type"]];
					$torderimport['OccupancyStatus'] = $a[$columnvariables["Occupancy Status"]];
					$torderimport['ResubmittalCount'] = $a[$columnvariables["Resubmittal Count"]];
					$torderimport['TitleInsuranceCompanyName'] = $a[$columnvariables["TitleCompany"]];
					$torderimport['WelcomeCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["WelcomeCallDate"]]);
					$torderimport['ApprovalCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ApprovalCallDate"]]);
					$torderimport['ConditionalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ConditionalApprovalDate"]]);
					$torderimport['LastNotesDays'] = $a[$columnvariables["LastNotesDays"]];
					$torderimport['LastNotesDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LastNotesDate"]]);
					$torderimport['NewLoanDays'] = $a[$columnvariables["NewLoanDays"]];
					$torderimport['Aging'] = $a[$columnvariables["Aging"]];
					$torderimport['NewLoanDaysBucket'] = $a[$columnvariables["NewLoanDaysBucket"]];
					$torderimport['MP'] = $a[$columnvariables["MP"]];
					$torderimport['MPManager'] = $a[$columnvariables["MPManager"]];
					$torderimport['Funder'] = $a[$columnvariables["Funder"]];
					$torderimport['BwrEmail'] = $a[$columnvariables["BwrEmail"]];
					$torderimport['PriorLoanNumber'] = $a[$columnvariables["PriorLoanNumber"]];
					$torderimport['BorrowerCellNumber'] = $a[$columnvariables["Borrower Cell Number"]];
					$torderimport['BranchID'] = $a[$columnvariables["Branch ID"]];
					$torderimport['FundingMilestoneDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Milestone Date"]]);
					$torderimport['ProductDescription'] = $a[$columnvariables["Product Description"]];
					$torderimport['FundingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Date"]]);
					$torderimport['FirstPaymentDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["First Payment Date"]]);
					$torderimport['TitleDocs'] = $a[$columnvariables["Title Docs"]];
					$torderimport['CashFromBorrower'] = $a[$columnvariables["CashFromBorrower"]];
					$torderimport['ProposedTotalHousingExpense'] = $a[$columnvariables["ProposedTotalHousingExpense"]];
					$torderimport['Assets'] = $a[$columnvariables["Assets"]];
					$torderimport['Queue'] = $a[$columnvariables["Queue"]];
					$torderimport['MaritalStatus'] = $a[$columnvariables["MaritalStatus"]];
					$torderimport['ZipCode'] = $a[$columnvariables["ZipCode"]];
					$torderimport['DOB'] = $a[$columnvariables["DOB"]];
					$torderimport['Term'] = $a[$columnvariables["Term"]];
					$torderimport['NoteRate'] = $a[$columnvariables["NoteRate"]];

				} elseif ($BulkImportFormat == "NRZ-Bulk_Upload") {

					$mMilestone = $this->Common_Model->get_row('mMilestone', ['MilestoneName'=>trim(ltrim($a[$columnvariables['Milestone']], "0"))]);
					$data['LoanNumber'] = $a[$columnvariables['Loan Number']];
					$data['LoanAmount'] = $a[$columnvariables['Loan Amount']];

					if (!empty($mMilestone)) {
						$data['MilestoneUID'] = $mMilestone->MilestoneUID;
					}

					$data['LoanType'] = $a[$columnvariables['LoanType']];
					$data['PropertyCountyName'] = $a[$columnvariables['County']];
					$data['PropertyStateCode'] = $a[$columnvariables['State']];
					$data['PropertyZipCode'] = $a[$columnvariables['Property Zip Code']];

					$propertyroledata = [];
					/*for ($i = 1; $i <= 5; $i++){*/
						if($a[$columnvariables['Borrower Name']] ) 
						{		
							$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['Borrower Name']];
							$propertyroledata[$i]['BorrowerMailingAddress1'] = "";
							$propertyroledata[$i]['HomeNumber'] = "";
							$propertyroledata[$i]['WorkNumber'] = "";
							$propertyroledata[$i]['CellNumber'] = $a[$columnvariables['Borrower Cell Number']];
							$propertyroledata[$i]['Social'] = "";

						}
					/*}*/


					$torderimport['LoanProcessor'] = $a[$columnvariables["Processor"]];
					$torderimport['SubStatusLastChangedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SubStatus last changed date"]]);
					$torderimport['LockExpiration'] = $a[$columnvariables["Lock Expiration"]];
					$torderimport['EarliestClosingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Earliest Closing date"]]);
					$torderimport['ClosedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closed date"]]);
					$torderimport['LEDisclosureDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LE disclosure date"]]);
					$torderimport['ClosingDisclosureSendDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closing Disclosure sent date"]]);
					$torderimport['DocsOutDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["DocsOutDate"]]);
					$torderimport['SigningDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDate"]]);
					$torderimport['SigningTime'] = $a[$columnvariables["SigningTime"]];
					$torderimport['SigningDateTime'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDateTime"]]);
					$torderimport['QueueDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["QueueDate"]]);
					$torderimport['NextPaymentDue'] = $a[$columnvariables["Next Payment Due"]];
					$torderimport['NSMServicingLoanNumber'] = $a[$columnvariables["NSM Serviving Loan number"]];
					$torderimport['DaysinStatus'] = $a[$columnvariables["DaysinStatus"]];
					$torderimport['ProcAssignDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ProcAssignDate"]]);
					$torderimport['FinalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["FinalApprovalDate"]]);
					$torderimport['PropertyType'] = $a[$columnvariables["Property Type"]];
					$torderimport['OccupancyStatus'] = $a[$columnvariables["Occupancy Status"]];
					$torderimport['ResubmittalCount'] = $a[$columnvariables["Resubmittal Count"]];
					$torderimport['TitleInsuranceCompanyName'] = $a[$columnvariables["TitleCompany"]];
					$torderimport['WelcomeCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["WelcomeCallDate"]]);
					$torderimport['ApprovalCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ApprovalCallDate"]]);
					$torderimport['ConditionalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ConditionalApprovalDate"]]);
					$torderimport['LastNotesDays'] = $a[$columnvariables["LastNotesDays"]];
					$torderimport['LastNotesDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LastNotesDate"]]);
					$torderimport['NewLoanDays'] = $a[$columnvariables["NewLoanDays"]];
					$torderimport['Aging'] = $a[$columnvariables["Aging"]];
					$torderimport['NewLoanDaysBucket'] = $a[$columnvariables["NewLoanDaysBucket"]];
					$torderimport['MP'] = $a[$columnvariables["MP"]];
					$torderimport['MPManager'] = $a[$columnvariables["MPManager"]];
					$torderimport['Funder'] = $a[$columnvariables["Funder"]];
					$torderimport['BwrEmail'] = $a[$columnvariables["BwrEmail"]];
					$torderimport['PriorLoanNumber'] = $a[$columnvariables["PriorLoanNumber"]];
					$torderimport['BorrowerCellNumber'] = $a[$columnvariables["Borrower Cell Number"]];
					$torderimport['BranchID'] = $a[$columnvariables["Branch ID"]];
					$torderimport['FundingMilestoneDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Milestone Date"]]);
					$torderimport['ProductDescription'] = $a[$columnvariables["Product Description"]];
					$torderimport['FundingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Date"]]);
					$torderimport['FirstPaymentDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["First Payment Date"]]);
					$torderimport['TitleDocs'] = $a[$columnvariables["Title Docs"]];
					$torderimport['CashFromBorrower'] = $a[$columnvariables["CashFromBorrower"]];
					$torderimport['ProposedTotalHousingExpense'] = $a[$columnvariables["ProposedTotalHousingExpense"]];
					$torderimport['Assets'] = $a[$columnvariables["Assets"]];
					$torderimport['Queue'] = $a[$columnvariables["Queue"]];
					$torderimport['MinApprovedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["MinApprovedDate"]]);

				}
				else{

					$data['LoanNumber'] = $a[$columnvariables['Loan Number']];
					$data['LoanAmount'] = $a[$columnvariables['Loan Amount']];
					$data['LoanType'] = $a[$columnvariables['Loan Type']];
					$data['CustomerReferenceNumber'] = $a[$columnvariables['Customer Reference Number']];
					$data['PropertyAddress1'] = $a[$columnvariables['Property Address']];
					$data['PropertyCityName'] = $a[$columnvariables['Property City']];
					$data['PropertyCountyName'] = $a[$columnvariables['Property County']];
					$data['PropertyStateCode'] = $a[$columnvariables['Property State']];
					$data['PropertyZipCode'] = $a[$columnvariables['Property Zip Code']];
					$data['APN'] = $a[$columnvariables['APN']];


					for ($i = 1; $i <= 5; $i++){
						if($a[$columnvariables['Borrower Name '.$i]] || $a[$columnvariables['Email '.$i]] || $a[$columnvariables['Home Number '.$i]] || $a[$columnvariables['Work Number '.$i]] || $a[$columnvariables['Cell Number '.$i]] || $a[$columnvariables['Social '.$i]] ) 
						{		
							$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['Borrower Name '.$i]];
							$propertyroledata[$i]['BorrowerMailingAddress1'] = $a[$columnvariables['Email '.$i]];
							$propertyroledata[$i]['HomeNumber'] = $a[$columnvariables['Home Number '.$i]];
							$propertyroledata[$i]['WorkNumber'] = $a[$columnvariables['Work Number '.$i]];
							$propertyroledata[$i]['CellNumber'] = $a[$columnvariables['Cell Number '.$i]];
							$propertyroledata[$i]['Social'] = $a[$columnvariables['Social '.$i]];
						}
					}

				}


				if (count($a) == $columnvariables['TotalCount']) {

					$additional['RejectionType'] = '';
					$a['ColorCode'] = '';
					$a['BGColorCode'] = '';

					$loanvalidation = $this->Orderentrymodel->is_loanno_exists($a[$columnvariables['Loan Number']]);

					if(!empty($a[$columnvariables['Loan Number']]) && !empty($Customerrow->LoanNumberValidation)  && strlen($a[$columnvariables['Loan Number']]) != $Customerrow->LoanNumberValidation) {
						$additional['RejectionType'] = 'Loan Number should be '.$Customerrow->LoanNumberValidation.' characters';
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#ff04ec';
						$a = array_merge($additional,$a);
						array_push($FailedData, $a);

					} else if(in_array($a[$columnvariables['Loan Number']], $loannumbercolumns) || $loanvalidation) {

						$additional['RejectionType'] = 'Duplicate Loan Number';
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#ff04ec';
						$a = array_merge($additional,$a);
						array_push($FailedData, $a);


					}  else {

						foreach ($torderimport as $key => $value) {
							// removes whitespace or other predefined characters from the left side of a string
							$torderimport[$key] = (ltrim($value) == '') ? NULL : ltrim($value);
						}

						$result = $this->Orderentrymodel->savebulkentry_order($data,$propertyroledata, $torderimport);

						$data['OrderNumber'] = $result['OrderNumber'];

						$a['result'] = $result;


						if (is_array($result) && !empty($result['OrderUID'])) {

							$InsertedOrderUID[] = $result['OrderUID'];
							$additional['RejectionType'] = '';
							$additional['OrderNumber'] = $result['OrderNumber'];
							$a['ColorCode'] = '';
							$a['BGColorCode'] = '';
							$a = array_merge($additional,$a);
							$SuccessData[] = $a;

							$Path = FCPATH . 'uploads/OrderDocumentPath/' . $result['OrderNumber'] . '/';
							$viewPath = 'uploads/OrderDocumentPath/' . $result['OrderNumber'] . '/';
							$this->Orderentrymodel->CreateDirectoryToPath($Path);

						} else {

							$additional['RejectionType'] = 'Failed to place order';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#757575';
							$a = array_merge($additional,$a);
							array_push($FailedData, $a);

						}



					}
				} else {

					$additional['RejectionType'] = 'Invalid Format';
					$a['ColorCode'] = '#fff';
					$a['BGColorCode'] = '#757575';
					$a = array_merge($additional,$a);
					array_push($FailedData, $a);


				}

			}


			$previewdata['headingsArray'] = $headingsArray;
			$previewdata['InsertedOrderUID'] = implode(',', $InsertedOrderUID);

			$posts = [];
			foreach ($SuccessData as $SuccessDatakey => $SuccessDatavalue) {
				$arayhgen = [];

				foreach ($SuccessDatavalue as $SuccessDatavaluekey => $value) {
					$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
				}
				$posts[] = $arayhgen;
			}

			$SuccessJSON = $posts;

			//$SuccessJSON = $this->GenerateSuccessRows($SuccessData, $CustomerUID, $ProductUID);


			$successresponse['data'] = $SuccessJSON;
			$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
			if($this->Common_Model->CreateDirectoryToPath($PATH)){
				$fp = fopen($PATH . 'successresults.json', 'w');
				fwrite($fp, json_encode($successresponse));
				fclose($fp);				
			}


			$posts = [];
			foreach ($FailedData as $FailedDatakey => $FailedDatavalue) {
				$arayhgen = [];

				foreach ($FailedDatavalue as $FailedDatavaluekey => $value) {
					$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
				}
				$posts[] = $arayhgen;
			}

			$FailedJSON = $posts;

			//$FailedJSON = $this->GenerateFailedRows($FailedData, $CustomerUID, $ProductUID);

			$failedresponse['data'] = $FailedJSON;
			$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
			if($this->Common_Model->CreateDirectoryToPath($PATH)){
				$fp = fopen($PATH . 'failedresults.json', 'w');
				fwrite($fp, json_encode($failedresponse));
				fclose($fp);				
			}


			$html = $this->load->view('standard_bulk_partialviews/bulk_imported', $previewdata, true);

			$successfilelink = 'uploads/'.$this->loggedid.'/successresults.json';
			$failedfilelink = 'uploads/'.$this->loggedid.'/failedresults.json';


			echo json_encode(array('error' => 0, 'html' => $html, 'matching_files'=>$matching_files, 'message'=>'Upload Success', 'successfilelink' => $successfilelink, 'failedfilelink' => $failedfilelink)); exit;

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please choose an valid file'));
		}

	} else {
		echo json_encode(array('error' => '1', 'message' => 'Please upload file'));
	}

}



	//function to get bulkentry format files
public function bulkentrypreviewfile($filename)
{
	$file = FCPATH.'assets/previewfile/'.$filename;
	if (file_exists($file)) {   
		if (ob_get_contents()) ob_end_clean();
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Type: application/force-download");
		header("Content-Type: application/download");
		header("Content-Length: ".filesize($file));
		readfile($file);
		ob_clean();
		exit;
	}
	header('HTTP/1.1 404 Not Found');

}


/* ---- EXCEL BULK ENTRY ENDS --- */


function PreviewOrderUploadFiles()
{
	$FileNames = $this->input->post("Files");
	$UploadType = $this->input->post("UploadType");

	if ($UploadType == "LocalUpload") {

			// Get Waiting Orders.
		$WaitingForImages = $this->Orderentrymodel->GetWaitingForImageOrders();

		$result = $this->FileUploadGenerate_Array($WaitingForImages, $FileNames);


		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($result))->_display();exit();
	}
	elseif($UploadType == "FTPUpload"){
		$this->load->library('ftp');
		$FileCollection = [];
		$FilePathCollection = [];

			// Get Waiting Orders.
		$ProjectOrders = $this->Orderentrymodel->GetWaitingForImageOrders_GroupByProjectUID();

		$OrderUIDs = "";
		foreach ($ProjectOrders as $key => $value) {
			$ProjectUID = $value->ProjectUID;
			$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID'=>$ProjectUID]);

			$mSFTP = $this->Common_Model->get_row('mSFTP', ['mSFTP.SFTPUID'=>$mProjectCustomer->SFTPUID]);

			$config['hostname'] =  $mSFTP->SFTPHost;
			$config['username'] =  $mSFTP->SFTPUser;
			$config['password'] =  $mSFTP->SFTPPassword;
			$config['debug']    = TRUE;
			$SFTPPath = $mSFTP->SFTPPath;

			if(empty($SFTPPath) || !$this->ftp->connect($config))
			{
				continue;
			}

			$SFTPPath = $mSFTP->SFTPPath;
			$list = $this->ftp->list_files($SFTPPath);

				// print_r($list);

			foreach ($list as $key => $file) {
				$FileCollection[] = basename($file);
				$FilePathCollection[] = $file;
			}
			$OrderUIDs .= $value->OrderUIDs;
			$this->ftp->close();

		}

		$FileCollection = array_unique($FileCollection);
		$FilePathCollection = array_unique($FilePathCollection);

		$OrderUID_Array = array_filter(explode(",", $OrderUIDs));

		$WaitingForImages = $this->Common_Model->GetOrderDetailsByOrderUIDs($OrderUID_Array);

		$result = $this->FileUploadGenerate_Array($WaitingForImages, $FileCollection, $FilePathCollection);


		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($result))->_display();exit();


	}
	elseif ($UploadType == "BulkImageUpload") {

		if (!isset($_FILES['BulkPDF'])) {
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(['validation_error'=>1, 'message'=>'Files not available.']))->_display();exit();

		}

		$this->load->library('Tesseract');
		$this->load->library('PDFtoImage');


		$timestamp = date('YmdHis');
		$DestinationPath = FCPATH . "uploads/UploadFileProcessing/" . $timestamp . "/";
		$this->Common_Model->CreateDirectoryToPath($DestinationPath);

		$UploadedFiles = $this->UploadFileToPath($_FILES['BulkPDF'], $DestinationPath, 0);
		$order_pages = $this->BulkImportOrders_SeperatorSheet($DestinationPath, $UploadedFiles); $end=array_pop($order_pages); 

		if (empty($order_pages)) {
			$result['validation_error']=0;
			// $result['html']=$html;
			$result['message']="Nothing matches.";

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($result))->_display();exit();				
		}

		$Orders = [];

		foreach ($order_pages as $key => $value) {

			if (!empty($value)) {


				$tOrders = $this->Common_Model->GetOrderDetails_ByKey('tOrders.OrderNumber', trim($value["Order"]));

				if (!empty($tOrders)) {


					$tOrders->StartPage = $value["page_start"];
					$tOrders->EndPage = $order_pages[$key+1]["page_start"]-1;
					if($tOrders->EndPage=='-1')
					{
						$tOrders->EndPage=$end;
					}
					
					// $tOrders->Pages = implode(" ", $value);

					$Orders[] = $tOrders;

				}
			}
		}

		
		$data['view']=['Orders'=>$Orders,'FilePath'=>$timestamp . '/' .$UploadedFiles[0]['file_name']];
		$html = $this->load->view('FileUpload_partialview/bulkimport_ocr_partialview', $data['view'], true);
		

		$result['validation_error']=0;
		$result['html']=$html;
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($result))->_display();exit();
	}

}

function FileUploadGenerate_Array($WaitingForImages, $FileNames, $FilePathCollection = [])
{

	if (empty($FileNames)) {
		return ['validation_error'=>1, 'message'=>"Files Not Available."];
	}

	$TableArray = [];

	foreach ($WaitingForImages as $key => $value) {
		$OrderNumber = $value->OrderNumber;
		$LoanNumber = $value->LoanNumber;

		$is_files_matched = false;
		$obj = new stdClass();
		$obj->OrderUID = $value->OrderUID;
		$obj->Files = [];

			// Exclusively for FTP Upload...
		if (!empty($FilePathCollection)) {
			$obj->FilesPath = [];
		}

		foreach ($FileNames as $key => $name) {

			$extension = strtolower(substr($name, (strripos($name, ".") + 1)));
			if (!empty($LoanNumber) && !empty($OrderNumber)) {

				if ((stripos($name, $OrderNumber) !== false || stripos($name, $LoanNumber) !== false) && $extension == 'pdf') {
					$obj->Files[] = $name;

						// Exclusively for FTP Upload...
					if (isset($FilePathCollection[$key])) {
						$obj->FilesPath[] = $FilePathCollection[$key];
					}
					$is_files_matched = true;
				}

			}
			else{
				if (stripos($name, $OrderNumber) !== false && $extension == 'pdf') {
					$obj->Files[] = $name;
					$is_files_matched = true;
				}
			}
		}
		if ($is_files_matched) {

			$OrderDetails = $this->Common_Model->GetOrderDetailsByOrderUID($obj->OrderUID);

			foreach ($obj->Files as $key => $FileName) {
				$result_obj = new stdClass();
				$result_obj->OrderUID = $OrderDetails->OrderUID;
				$result_obj->OrderNumber = $OrderDetails->OrderNumber;
				$result_obj->LoanNumber = $OrderDetails->LoanNumber;
				$result_obj->CustomerName = $OrderDetails->CustomerName;
				$result_obj->ProjectName = $OrderDetails->ProjectName;
				$result_obj->LenderName = $OrderDetails->LenderName;
				$result_obj->PropertyAddress1 = $OrderDetails->PropertyAddress1;
				$result_obj->PropertyCityName = $OrderDetails->PropertyCityName;
				$result_obj->PropertyStateCode = $OrderDetails->PropertyStateCode;
				$result_obj->PropertyZipCode = $OrderDetails->PropertyZipCode;
				$result_obj->OrderEntryDateTime = $OrderDetails->OrderEntryDateTime;
				$result_obj->FileName = $FileName;

					// Exclusively for FTP Upload...
				if (isset($obj->FilesPath[$key])) {
					$result_obj->FilesPath = $obj->FilesPath[$key];
				}


				$TableArray[] = $result_obj;
			}
		}
	}
	$html = $this->load->view('FileUpload_partialview/fileupload_partialview', ['WaitingForImage'=>$TableArray], true);
	return ['validation_error'=>0, 'html'=>$html];

}

function UploadOrderFiles()
{

	$Orders = $this->input->post('Orders');
	$UploadType = $this->input->post("UploadType");

	$this->load->library('MergePDF');

	if ( empty($Orders) ) {
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(['validation_error'=>1, 'message'=>"Nothing to Upload"]))->_display();exit();
	}

	if ($UploadType == "LocalUpload") {
			// Get Uploaded files.
		$OrderFiles = $_FILES['Orders'];

		foreach ($Orders['OrderUID'] as $key => $value) {
			$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$value]);

			$files = [];

			if (!empty($tOrders)) {
				$DocumentPath = FCPATH . 'uploads/OrderDocumentPath/'.$tOrders->OrderNumber . '/';
				$this->Common_Model->CreateDirectoryToPath($DocumentPath);
				$Path = $DocumentPath;

				$viewPath = 'uploads/OrderDocumentPath/'.$tOrders->OrderNumber . '/';


				$tDocuments_row = $this->Common_Model->get_row('tDocuments',['OrderUID'=>$tOrders->OrderUID, 'IsStacking'=>1]);

				if (!empty($tDocuments_row) && file_exists(FCPATH. $tDocuments_row->DocumentURL)) {

					$files[] = FCPATH. $tDocuments_row->DocumentURL;

					$stacking_file_name = $tDocuments_row->DocumentName;
				}

				if (empty($stacking_file_name)) {
					$stacking_file_name = basename($OrderFiles['name']['File'][$key]);
				}



				$files[] = $DocumentPath . basename($OrderFiles['name']['File'][$key]);


				if (move_uploaded_file($OrderFiles['tmp_name']['File'][$key], $DocumentPath . basename($OrderFiles['name']['File'][$key]))) {

					$tempfilename = date('YmdHis') . '.pdf';
						// Merge all uploaded files into one single stacking document.
					if($this->mergepdf->merge($files, $Path . $tempfilename)){

						$tDocuments_result = $this->Common_Model->get('tDocuments',['OrderUID'=>$tOrders->OrderUID, 'IsStacking'=>1]);

									// Remove Previous 
						foreach ($tDocuments_result as $key => $doc) {
							$this->Common_Model->delete('tDocuments','DocumentUID', $doc->DocumentUID);

						}

						foreach ($files as $key => $file) {
							unlink($file);
						}

						rename($Path.$tempfilename, $Path.$stacking_file_name);
						/*Save tDocuments*/
						$tDocuments['DocumentName'] = $stacking_file_name;
						$tDocuments['DocumentURL'] = $viewPath . $stacking_file_name;
						$tDocuments['OrderUID'] = $value;
						$tDocuments['IsStacking'] = 1;
						$tDocument['TypeofDocument'] = 'Stacking';
						$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
						$tDocuments['UploadedByUserUID'] = $this->loggedid;
						$this->Orderentrymodel->save('tDocuments', $tDocuments);

						if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

							$status = $this->config->item('keywords')['Image Received'];
							$this->Orderentrymodel->UpdateStatus($tOrders->OrderUID,$status);						
						}

					}
				}
			}
		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(['validation_error'=>0, 'message'=>"File Upload Success"]));
	}
	elseif($UploadType == "FTPUpload"){

		$this->load->library('ftp');


		$ProjectOrderGroup = $this->Orderentrymodel->GetOrdersByOrderUIDs_GroupByProjectUID($Orders['OrderUID']);

		foreach ($ProjectOrderGroup as $key => $projectorder) {

			$ProjectUID = $projectorder->ProjectUID;
			$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID'=>$ProjectUID]);

			$mSFTP = $this->Common_Model->get_row('mSFTP', ['mSFTP.SFTPUID'=>$mProjectCustomer->SFTPUID]);

			$config['hostname'] =  $mSFTP->SFTPHost;
			$config['username'] =  $mSFTP->SFTPUser;
			$config['password'] =  $mSFTP->SFTPPassword;
			$config['debug']    = FALSE;
			$SFTPPath = $mSFTP->SFTPPath;

			if(empty($SFTPPath) || !$this->ftp->connect($config))
			{
				continue;
			}

			$OrderUIDs = explode(",", $projectorder->OrderUIDs);

			foreach ($OrderUIDs as $key => $order) {
				$post_array_key = $this->Common_Model->return_array_key($order, $Orders['OrderUID']);
				$RemoteFilePath = $Orders['FilePath'][$post_array_key];
				$RemoteFileName = $Orders['FileName'][$post_array_key];

				$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$order]);

				$files = [];
				if (!empty($tOrders)) {
					$DocumentPath = FCPATH . 'uploads/OrderDocumentPath/'.$tOrders->OrderNumber . '/';
					$Path = $DocumentPath;
					$this->Common_Model->CreateDirectoryToPath($DocumentPath);
					$DocumentPath .= $RemoteFileName;


					$viewPath = 'uploads/OrderDocumentPath/'.$tOrders->OrderNumber . '/';



					$tDocuments_row = $this->Common_Model->get_row('tDocuments',['OrderUID'=>$tOrders->OrderUID, 'IsStacking'=>1]);

					if (!empty($tDocuments_row) && file_exists(FCPATH. $tDocuments_row->DocumentURL)) {

						$files[] = FCPATH. $tDocuments_row->DocumentURL;

						$stacking_file_name = $tDocuments_row->DocumentName;
					}

					if (empty($stacking_file_name)) {
						$stacking_file_name = basename($OrderFiles['name']['File'][$key]);
					}

					if ($this->ftp->download($RemoteFilePath, $DocumentPath)) {



						$files[] = $DocumentPath;

					// executes if files exists
						if (file_exists($DocumentPath)) {


							$tempfilename = date('YmdHis') . '.pdf';
						// Merge all uploaded files into one single stacking document.
							if($this->mergepdf->merge($files, $Path . $tempfilename)){

								$tDocuments_result = $this->Common_Model->get('tDocuments',['OrderUID'=>$tOrders->OrderUID, 'IsStacking'=>1]);

									// Remove Previous 
								foreach ($tDocuments_result as $key => $doc) {
									$this->Common_Model->delete('tDocuments','DocumentUID', $doc->DocumentUID);

								}

								foreach ($files as $key => $file) {
									unlink($file);
								}

								rename($Path.$tempfilename, $Path.$stacking_file_name);
								/*Save tDocuments*/
								$tDocuments['DocumentName'] = $stacking_file_name;
								$tDocuments['DocumentURL'] = $viewPath . $stacking_file_name;
								$tDocuments['OrderUID'] = $result['OrderUID'];
								$tDocuments['IsStacking'] = 1;
								$tDocuments['TypeofDocument'] = 'Stacking';
								$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
								$tDocuments['UploadedByUserUID'] = $this->loggedid;
								$this->Orderentrymodel->save('tDocuments', $tDocuments);

								if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

									$status = $this->config->item('keywords')['Image Received'];
									$this->Orderentrymodel->UpdateStatus($tOrders->OrderUID,$status);						
								}
							}
						}

					}
					else{
						echo "Unable to Download";
					}

				}

			}

			$this->ftp->close();
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(['validation_error'=>0, 'message'=>"File Upload Success"]));

	}
	else if($UploadType == "BulkImageUpload"){

		$FilePath = $this->input->post('FilePath');

		$SourceFilePath = FCPATH . 'uploads/UploadFileProcessing/' . $FilePath;

		foreach ($Orders['OrderUID'] as $key => $orderuid) {

			 // var_dump($Orders);

			// if (empty($Orders['Pages'][$key])) {die('lion2');
			// 	continue;

			// }				

			$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$orderuid]);
			$files = []; $stacking_file_name = "";

			$Path = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/';
			$this->Orderentrymodel->CreateDirectoryToPath($Path);

			$tDocuments_row = $this->Common_Model->get_row('tDocuments',['OrderUID'=>$tOrders->OrderUID, 'IsStacking'=>1]);

			if (!empty($tDocuments_row) && file_exists(FCPATH. $tDocuments_row->DocumentURL)) {

				$files[] = FCPATH. $tDocuments_row->DocumentURL;

				$stacking_file_name = $tDocuments_row->DocumentName;
			}

			if (empty($stacking_file_name)) {
				$stacking_file_name = empty($tOrders->LoanNumber) ? $tOrders->OrderNumber . ".pdf" : $tOrders->LoanNumber . ".pdf";
			}

			$viewPath = 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . $stacking_file_name;

			$temp_OutputPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . date('YmdHis') . ".pdf";
			$output = shell_exec("pdftk \"".$SourceFilePath."\" cat ".$Orders['start'][$key]."-".$Orders['end'][$key]." output \"" .$temp_OutputPath. "\"");

			// echo "pdftk \"".$SourceFilePath."\" cat ".$Orders['Pages'][$key]." output \"" .$temp_OutputPath. "\"";
			$files[] = $temp_OutputPath;

			if (!empty($files)) {

				sleep(2);
				$tempfilename = date('YmdHis') . '.pdf';
						// Merge all uploaded files into one single stacking document.
				// var_dump($this->mergepdf->merge($files, $Path . $tempfilename));
				if($this->mergepdf->merge($files, $Path . $tempfilename)){

					// unlink($temp_OutputPath);


					$tDocuments_result = $this->Common_Model->get('tDocuments',['OrderUID'=>$tOrders->OrderUID, 'IsStacking'=>1]);

									// Remove Previous 
					foreach ($tDocuments_result as $key => $doc) {
						$this->Common_Model->delete('tDocuments','DocumentUID', $doc->DocumentUID);
						unlink($doc->DocumentURL);

					}

					rename($Path.$tempfilename, $Path.$stacking_file_name);


					/*Save tDocuments*/
					$tDocuments['DocumentName'] = $stacking_file_name;
					$tDocuments['DocumentURL'] = $viewPath;
					$tDocuments['OrderUID'] = $tOrders->OrderUID;
					$tDocuments['IsStacking'] = 1;
					$tDocument['TypeofDocument'] = 'Stacking';
					$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
					$tDocuments['UploadedByUserUID'] = $this->loggedid;
					if ($this->Common_Model->IsWorkflowAvailableForDocUpload($tOrders->OrderUID, $this->config->item('Workflows')['Doc_Check_In'])) {
						$tDocuments['DocumentStorage'] = $this->Common_Model->getAvailableDocumentStorage();

					}

					$this->Orderentrymodel->save('tDocuments', $tDocuments);


					if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

						$status = $this->config->item('keywords')['Image Received'];
						$this->Orderentrymodel->UpdateStatus($tOrders->OrderUID,$status);						
					}					}
				}

			}

			$DeletePath = substr($SourceFilePath, 0, stripos($SourceFilePath, basename($SourceFilePath)) - 1);
			if (file_exists($DeletePath) && is_dir($DeletePath) && !in_array($DeletePath, [FCPATH . 'uploads/UploadFileProcessing/'])) {
				$this->Common_Model->rrmdir($DeletePath);
			}

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(['validation_error'=>0, 'message'=>"File Upload Success"]));

		}

	}

	function BulkImportOrders_SeperatorSheet($DestinationPath, $UploadedFiles)
	{	
		$order_pages = [];

		foreach ($UploadedFiles as $key => $File) {
			if (isset($File['file_name'])) {				

				$SourceFile = $DestinationPath . $File['file_name'];

				$prefix_name = str_replace(['.pdf', '.PDF'], "", basename($SourceFile));

				$Seperator_Sheet_Text = "Reference Loan Number:";

				$loan_length = 100;


				$current_loannumber = null;


				// if($this->pdftoimage->convert_pdf_to_image($SourceFile, $prefix_name, $DestinationPath)){

				$SearchCriteria = ['Reference Loan Number:'];/*,'LoanNumber : ', 'LoanNumber:', 'Loan Number:','LoanNumber', 'Loan Number', 'LN#', 'Loan#'*/
					// $pagecount = $this->pdftoimage->get_pdf_pages($SourceFile);

					//$this->load->library("pdfparser.php");
					// $this->load->library("pdf2text");
					// $pdfstring= $this->pdf2text->pdf2text($SourceFile);
					//$this->load->library("pdf-to-text");
				require_once(APPPATH.'libraries/pdf-to-text/PdfToText.phpclass');
					// $SourceFile1=file_get_contents($SourceFile , FILE_BINARY);
					// $SourceFile1=preg_replace( '/[^[:print:]]/', '',$SourceFile1);

				

				$pdf = new PdfToText( $SourceFile ); 
				
				$Orders=[];
				
				foreach( $pdf -> Pages as $page_number => $page_contents)
						 { //echo "Contents of page #$page_number :\n$page_contents\n";
						if(strpos($page_contents,$Seperator_Sheet_Text)>0)
						{
							$pagedata =  preg_replace( "/\r|\n|\t/", "^",$page_contents);

							$length=strlen($Seperator_Sheet_Text)+1;
							$order_pages['page_start']=$page_number;


						// $order_pages['page_end']=$page_number-1;
							$start=strpos($pagedata,":")+1;

							$order_pages['Order']=substr($page_contents,$length,10);

							array_push($Orders,$order_pages);

						}

					}
					ob_end_clean();
					$order_pages['end']=$page_number;	
					array_push($Orders,$order_pages['end']);
					return $Orders;

					//$apages_text = $this->pdfparser->getPageText($SourceFile);

					if(!$apages_text)
					{
						return $order_pages;
					}

					foreach ($apages_text as $pagekey => $pagedata) {

					// }
					// for ($i=1; $i <= $pagecount; $i++) { 

						// $ocrimage = $DestinationPath . $prefix_name . "-" . $i . ".png";

						// if (file_exists($ocrimage)) {

						// 	$pagedata = $this->tesseract->OCR($ocrimage);

						// 	unlink($ocrimage);

						// echo $pagedata . "<br/>";
						// exit;
						$pagedata =  preg_replace( "/\r|\n|\t/", "", $pagedata );

						// echo $pagedata; exit;

						if (stripos($pagedata, $Seperator_Sheet_Text) !== false) {
								// echo $pagedata; exit;
							foreach ($SearchCriteria as $key => $value) {

								if (($loan_postion = strrpos($pagedata, $value)) !== false ) {
									$loan_num_pos = strrpos($pagedata, "Package No");
									$loan_length = $loan_num_pos - ($loan_postion + strlen($value));
									$predicted_loan_number = substr($pagedata, $loan_postion + strlen($value), $loan_length);

									$loan_numbers = explode(" ", $predicted_loan_number);

									foreach ($loan_numbers as $l_key => $number) {


										if (!empty($number)) {

											$current_loannumber = str_replace(" ", "", $number);
											continue 3;
										}
									}

								}
							}
						}
						else{

							if (!empty($current_loannumber)) {
								$order_pages[$current_loannumber][] = $pagekey + 1;
							}
						}
						// }
					}
				// }
				// else{
				// 	return false;
				// }
				}
			}

			return $order_pages;

		}

		function BulkImport_SeperatorSheet()
		{
			 // Convert this document
		// Each page to single image
			$img = new imagick("/var/www/html/stacx.stage/uploads/S19000004/1436792129_Doc%20Check-in.pdf.pdf");

		// Set background color and flatten
		// Prevents black background on objects with transparency
			$img->setImageBackgroundColor('white');
			$img = $img->flattenImages();

		// Set image resolution
		// Determine num of pages
			$img->setResolution(300,300);
			$num_pages = $img->getNumberImages();

			echo $num_pages;
		}


		/* ----- SUPPORTING FUNCTIONS STARTS ---- */
		function UploadFileToPath($files, $Path, $name)
		{
			if (!file_exists($Path)) {
				if (!mkdir($Path, 0777, true)) die('Unable to create directory');
			}
			chown($Path,'www-data');
			$config['upload_path'] = $Path;
			$config['allowed_types'] = 'pdf';
			$config['max_size'] = 0;
			$config['overwrite'] = true;
			
			$this->load->library('upload', $config);
			
			$DocumentFiles = [];
			$Errors = [];
			foreach ($files['name'] as $key => $image) {
				$_FILES[$name]['name'] = $files['name'][$key];
				$_FILES[$name]['type'] = $files['type'][$key];
				$_FILES[$name]['tmp_name'] = $files['tmp_name'][$key];
				$_FILES[$name]['error'] = $files['error'][$key];
				$_FILES[$name]['size'] = $files['size'][$key];
				
				$fileName = $files['name'][$key];
				
						// $DocumentFiles[] = $fileName;
				
				$config['file_name'] = $fileName;
				
				$this->upload->initialize($config);
				
				if ($this->upload->do_upload($name)) {
					chmod($Path.'/'.$fileName,0777);
					chown($Path.'/'.$fileName,'www-data');
					$DocumentFiles[] = $this->upload->data();
				} else {
					$this->upload->display_errors();
				}
			}
			return $DocumentFiles;
		}

		public function NormalFileUpload($File, $PATH, $OrderUID)
		{
			if (is_uploaded_file($File)) {
				if (move_uploaded_file($File, $PATH)) {
					return true;
				}
				
			}
			return false;
		}

		function isEmptyRow($row)
		{
			foreach ($row as $cell) {
				if (null !== $cell) return false;
			}
			return true;
		}


		/* ----- SUPPORTING FUNCTIONS ENDS ---- */
		
		function Get_CustomerProjects()
		{   
			$CustomerUID = $this->input->post('CustomerUID');
			$ProductUID = $this->input->post('ProductUID');
			$CustomerProjects = [];
			if(!empty($CustomerUID) && !empty($ProductUID)){
				$CustomerProjects =  $this->Orderentrymodel->Get_CustomerProjects($CustomerUID,$ProductUID);
			}
			$this->output->set_content_type('application/json')
			->set_output(json_encode(['CustomerProjects'=>$CustomerProjects]));
		}



		function cleanData(&$str) 
		{ 
			$str = preg_replace("/\t/", "\\t", $str); 
			$str = preg_replace("/\r?\n/", "\\n", $str); 
			if(strstr($str, '"')) 
				$str = '"' . str_replace('"','""', $str).'"'; 
		}



		public function uploadfile()
		{

			$this->load->library('MergePDF');

			$up_files = $this->input->post('Files');
			$OrderUIDs = $this->input->post('OrderUID');
			$UploadType = $this->input->post('UploadType');


			$files_available = 0;

			$filecount = count($up_files);

			if (!empty($OrderUIDs) ) {

				$OrderNumbers = [];
				foreach ($OrderUIDs as $key => $OrderUID) {


					$tOrders = $this->Common_Model->get_row('tOrders',['OrderUID'=>$OrderUID]);

					if(empty($tOrders))
					{
						if($this->saveInDocumentTracking($_FILES)){
							echo json_encode(['validation_error'=>STATUS_ZERO, 'msg'=>'Files Uploaded Successfully']); exit;
						}
					}
					$Path=FCPATH . 'uploads/OrderDocumentPath/' .$tOrders->OrderNumber . '/';
					$viewPath='uploads/OrderDocumentPath/' .$tOrders->OrderNumber . '/';

					$OrderNumbers[] = $tOrders->OrderNumber;


					/*############ Extract and attach files comming in zip format starts ###########*/
					$extracted_files = [];
					if (isset($_FILES['ZipFiles']) && count($_FILES['ZipFiles'])) {
						$Dynamic_Temp_Path = FCPATH . 'uploads/Temp_DocumentPath/' . date('YmdHis') . '/';
						$this->Orderentrymodel->CreateDirectoryToPath($Dynamic_Temp_Path);
						foreach ($_FILES['ZipFiles']['error'] as $key => $error) {
							if ($error == UPLOAD_ERR_OK) {
								$tmp_name = $_FILES["ZipFiles"]["tmp_name"][$key];
								$name = basename($_FILES["ZipFiles"]["name"][$key]);
								move_uploaded_file($tmp_name, "$Dynamic_Temp_Path/$name");
							}

						}

					// Function Call
						$Zip_files = $this->ExtractFilesInFolder($Dynamic_Temp_Path);

						foreach ($Zip_files as $key => $filename) {
							$extracted_files = $this->Extract_and_GetFiles($Dynamic_Temp_Path . $filename, $Path);
						}

					}


					/*############ Extract and attach files comming in zip format ends ###########*/

				// Executes entire block when file is uploaded.
					$UploaedFiles = [];
					if (isset($_FILES['DocumentFiles']) && count($_FILES['DocumentFiles'])) {
						$this->Orderentrymodel->CreateDirectoryToPath($Path);
						$UploaedFiles = $this->UploadFileToPath($_FILES['DocumentFiles'], $Path, 'DocumentFiles[]');					


					}


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
							$files[] = $Path . $value['file_name'];
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


									foreach ($files as $key => $file) {
										unlink($file);
									}

									rename($Path.$tempfilename, $Path.$stacking_file_name);
									/*Save tDocuments*/
									$tDocuments['DocumentName'] = $stacking_file_name;
									$tDocuments['DocumentURL'] = $viewPath . $stacking_file_name;
									$tDocuments['OrderUID'] = $OrderUID;
									$tDocuments['IsStacking'] = STATUS_ONE;
									$tDocuments['TypeofDocument'] = 'Stacking';
									$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
									$tDocuments['UploadedByUserUID'] = $this->loggedid;
									if ($this->Common_Model->IsWorkflowAvailableForDocUpload($OrderUID, $this->config->item('Workflows')['Doc_Check_In'])) {
										$tDocuments['DocumentStorage'] = $this->Common_Model->getAvailableDocumentStorage();

									}
									$this->Orderentrymodel->save('tDocuments', $tDocuments);
									/*INSERT ORDER LOGS BEGIN*/
									$this->Common_Model->OrderLogsHistory($OrderUID,''.$tDocuments['DocumentName'].' '.'File Uploaded',Date('Y-m-d H:i:s'));
									/*INSERT ORDER LOGS END*/

									if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

										$status = $this->config->item('keywords')['Image Received'];
										$this->Orderentrymodel->UpdateStatus($OrderUID,$status);						
									}


								}

							}

							else{

								$is_merge_later = $this->input->post('MergeLater');

								if ($is_merge_later == "Yes") {
									foreach ($files as $key => $file) {

										/*Save tTempDocuments*/
										$tTempDocuments['DocumentName'] = basename($file);
										$tTempDocuments['DocumentURL'] = $viewPath . basename($file);
										$tTempDocuments['OrderUID'] = $OrderUID;
										$tTempDocuments['IsStacking'] = STATUS_ZERO;
										$tTempDocuments['TypeofDocument'] = 'Others';
										$tTempDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
										$tTempDocuments['UploadedByUserUID'] = $this->loggedid;
										$tTempDocuments['ToBeMerged'] = STATUS_ONE;
										$this->Orderentrymodel->save('tTempDocuments', $tTempDocuments);
									}

								}
								else{

									foreach ($files as $key => $file) {

										/*Save tDocuments*/
										$tDocuments['DocumentName'] = basename($file);
										$tDocuments['DocumentURL'] = $viewPath . basename($file);
										$tDocuments['OrderUID'] = $OrderUID;
										$tDocuments['IsStacking'] = STATUS_ZERO;
										$tDocuments['TypeofDocument'] = 'Others';
										$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
										$tDocuments['UploadedByUserUID'] = $this->loggedid;
										if ($this->Common_Model->IsWorkflowAvailableForDocUpload($OrderUID, $this->config->item('Workflows')['Doc_Check_In'])) {
											$tDocuments['DocumentStorage'] = $this->Common_Model->getAvailableDocumentStorage();

										}
										$this->Orderentrymodel->save('tDocuments', $tDocuments);
										/*INSERT ORDER LOGS BEGIN*/
										$this->Common_Model->OrderLogsHistory($OrderUID,''.$tDocuments['DocumentName'].' '.'File Uploaded',Date('Y-m-d H:i:s'));
										/*INSERT ORDER LOGS END*/
									}

									if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

										$status = $this->config->item('keywords')['Image Received'];

										$this->Orderentrymodel->UpdateStatus($OrderUID,$status);						
									}

								}

							}

						}

					}
				}	
				echo json_encode(['validation_error'=>STATUS_ZERO, 'msg'=>'Files Uploaded Successfully', 'OrderNumber'=>implode(", ", $OrderNumbers)]);
			}
			else{
				$CustomerUID = $this->input->post('CustomerUID');
				$LenderUID = $this->input->post('LenderUID');
				if($this->saveInDocumentTracking($_FILES, $CustomerUID, $LenderUID)){

					echo json_encode(['validation_error'=>STATUS_ZERO, 'msg'=>'Files Uploaded Successfully']);

				}

			}
		}


		function saveInDocumentTracking($files_to_upload, $CustomerUID, $LenderUID)
		{
			$Path = FCPATH . 'uploads/DocumentTracking/' .date('Ymd') . '/';
			$ViewPath = 'uploads/DocumentTracking/' .date('Ymd') . '/';


			/*############ Extract and attach files comming in zip format starts ###########*/
			$extracted_files = [];
			if (isset($files_to_upload['ZipFiles']) && count($files_to_upload['ZipFiles'])) {
				$Dynamic_Temp_Path = FCPATH . 'uploads/Temp_DocumentPath/' . date('YmdHis') . '/';
				$this->Orderentrymodel->CreateDirectoryToPath($Dynamic_Temp_Path);
				foreach ($files_to_upload['ZipFiles']['error'] as $key => $error) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $files_to_upload["ZipFiles"]["tmp_name"][$key];
						$name = basename($files_to_upload["ZipFiles"]["name"][$key]);
						move_uploaded_file($tmp_name, "$Dynamic_Temp_Path/$name");
					}

				}

					// Function Call
				$Zip_files = $this->ExtractFilesInFolder($Dynamic_Temp_Path);

				foreach ($Zip_files as $key => $filename) {
					$extracted_files = $this->Extract_and_GetFiles($Dynamic_Temp_Path . $filename, $Path);
				}

			}


			/*############ Extract and attach files comming in zip format ends ###########*/

				// Executes entire block when file is uploaded.
			$UploaedFiles = [];
			if (isset($files_to_upload['DocumentFiles']) && count($files_to_upload['DocumentFiles'])) {
				$this->Orderentrymodel->CreateDirectoryToPath($Path);
				$UploaedFiles = $this->UploadFileToPath($files_to_upload['DocumentFiles'], $Path, 'DocumentFiles[]');					

			}


			if (count($UploaedFiles) || count($extracted_files)) {

				foreach ($UploaedFiles as $key => $value) {
						// Collect Uploaded files
					$files[] = $Path . $value['file_name'];
				}


					// Include extracted files.
				foreach ($extracted_files as $key => $file) {
					$files[] = $file;
				}


				foreach ($files as $key => $value) {

					$tDocumentTracking['DocumentName'] = basename($value);
					$tDocumentTracking["DocumentURL"] = $ViewPath . basename($value);
					$tDocumentTracking["UploadedByUserUID"] = $this->loggedid;
					$tDocumentTracking["UploadedDateTime"] = date('Y-m-d H:i:s');
					$tDocumentTracking["ReceivedByUserUID"] = $this->loggedid;
					$tDocumentTracking["DocumentStatus"] = "Uploaded";
					$tDocumentTracking["CustomerUID"] = $CustomerUID;
					$tDocumentTracking["LenderUID"] = $LenderUID;

					$this->Common_Model->save('tDocumentTracking', $tDocumentTracking);
				}

			}

			return true;
		}


		public function RaiseFollowUp()
		{
			$Orders = $this->input->post('Orders');
			$realOrders = $this->Orderentrymodel->GetAllOrders($OrderUIDs);

			$response = [];		
			$this->db->trans_begin();

			foreach ($realOrders as $key => $value) {
				$this->Common_Model->save('tOrders', ['IsFollowUp'=>STATUS_ONE], ['OrderUID'=>$value->OrderUID]);
			}

			if ($this->db->trans_status() == true) {
				$this->db->trans_commit();
				$response['validation_error'] = 0;
				$response['message'] = "Follow Up Raised Successfully";
			}
			else{
				$response['validation_error'] = 1;
				$response['message'] = "Unable to raise followup";

			}

			echo json_encode($response);

		}


		public function MergeFilesLater()
		{
		// ini_set("display_errors", 1);
		// error_reporting(E_ALL);

			$this->load->library('MergePDF');

			$tTempDocuments = $this->Orderentrymodel->getUnMergedDocuments();
			$response = [];

			$this->db->trans_begin();

			foreach ($tTempDocuments as $key => $value) {

				$files = [];
				$stacking_file_name = "";
				$OrderUID = $value->OrderUID;

				$tOrders = $this->Common_Model->get_row('tOrders',['OrderUID'=>$OrderUID]);

				$Path=FCPATH . 'uploads/OrderDocumentPath/' .$tOrders->OrderNumber . '/';
				$viewPath='uploads/OrderDocumentPath/' .$tOrders->OrderNumber . '/';

				$DocumentURL = explode(",", $value->DocumentURL);

				$tDocuments_row = $this->Common_Model->get_row('tDocuments', ['OrderUID'=>$OrderUID, 'IsStacking'=>1]);

				if (!empty($tDocuments_row) && file_exists($tDocuments_row->DocumentURL)) {
					$files[] = $tDocuments_row->DocumentURL;
					$stacking_file_name = $tDocuments_row->DocumentName;
				}
				else{

					$stacking_file_name = empty($stacking_file_name) ? empty($tOrders->LoanNumber) ? $tOrders->OrderNumber  . '.pdf' : $tOrders->LoanNumber . '.pdf' : $stacking_file_name;
				}

				foreach ($DocumentURL as $key => $document) {

					$files[] = $document;
				}

				$tempfilename = date('YmdHis') . '.pdf';



				if ($this->mergepdf->merge($files, $Path . $tempfilename)) {

					$tDocuments_result = $this->Common_Model->get('tDocuments',['OrderUID'=>$OrderUID, 'IsStacking'=>1]);

							// Remove Previous 
					foreach ($tDocuments_result as $key => $doc) {
						$this->Common_Model->delete('tDocuments','DocumentUID', $doc->DocumentUID);

					}


					foreach ($files as $key => $file) {
						if (file_exists($file)) {
							unlink($file);

						}
					}

					rename($Path.$tempfilename, $Path.$stacking_file_name);
					/*Save tDocuments*/
					$tDocuments['DocumentName'] = $stacking_file_name;
					$tDocuments['DocumentURL'] = $viewPath . $stacking_file_name;
					$tDocuments['OrderUID'] = $OrderUID;
					$tDocuments['IsStacking'] = STATUS_ONE;
					$tDocuments['TypeofDocument'] = 'Stacking';
					$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
					$tDocuments['UploadedByUserUID'] = $this->loggedid;
					if ($this->Common_Model->IsWorkflowAvailableForDocUpload($OrderUID, $this->config->item('Workflows')['Doc_Check_In'])) {
						$tDocuments['DocumentStorage'] = $this->Common_Model->getAvailableDocumentStorage();

					}
					$this->Orderentrymodel->save('tDocuments', $tDocuments);
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($OrderUID,''.$tDocuments['DocumentName'].' '.'File Uploaded',Date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/

					if (in_array($tOrders->StatusUID, [$this->config->item('keywords')['New Order'], $this->config->item('keywords')['Waiting For Images']])) {

						$status = $this->config->item('keywords')['Image Received'];
						$this->Orderentrymodel->UpdateStatus($OrderUID,$status);						
					}

					$this->Common_Model->save('tTempDocuments', ['ToBeMerged'=> STATUS_ZERO], ['OrderUID'=>$OrderUID]);
				}
			}

			if ($this->db->trans_status() == true) {
				$this->db->trans_commit();
				$response['status'] = STATUS_ZERO;
				$response['message'] = 'Merge Success';
			}
			else{
				$response['status'] = STATUS_ONE;
				$response['message'] = 'Merge Fail';

			}

			echo json_encode($response);
		}
		function ExtractFilesInFolder($Path)
		{
			$FILE_ARRAY = [];	
			if (is_dir($Path) && $handle = opendir($Path)) {

				while (false !== ($entry = readdir($handle))) {

					if ($entry != "." && $entry != "..") {

						$FILE_ARRAY[] =$entry;
					}
				}

				closedir($handle);
			}
			return $FILE_ARRAY;
		}



		function Extract_and_GetFiles($arch_filename, $dest_dir)
		{
		// $dest_dir = './dest';
			$Files = [];
			if (!is_dir($dest_dir)) {
				if (!mkdir($dest_dir, 0755, true))
					die("failed to make directory $dest_dir\n");
			}

			$zip = new ZipArchive;

			if (!$zip->open($arch_filename))
				die("failed to open $arch_filename");

			for ($i = 0; $i < $zip->numFiles; ++$i) {
				$path = $zip->getNameIndex($i);
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				if (!preg_match('/(?:pdf|PDF)/i', $ext))
					continue;
				$dest_basename = pathinfo($path, PATHINFO_BASENAME);
				copy("zip://{$arch_filename}#{$path}", "$dest_dir/{$dest_basename}");
				$Files[] = $dest_dir . $dest_basename;
			}

			$zip->close();
			return $Files;
		}

		function column_variables($BulkImportFormat = "LOP-Standard")
		{

			if ($BulkImportFormat == "LOP-Assignment") {
				$column["Loan Number"] = 0; /* tOrders -> LoanNumber*/
				$column["Borrower Name"] = 1; /* tOrderPropertyRole -> BorrowerFirstName*/
				$column["Processor"] = 2; /* tOrderImport -> LoanProcessor*/
				$column["Milestone"] = 3; /* tOrders, tOrderMileStone -> MilestoneUID*/
				$column["SubStatus last changed date"] = 4; /* tOrderImport -> SubStatusLastChangedDate*/
				$column["LoanType"] = 5; /* tOrders -> LoanType*/
				$column["State"] = 6; /* tOrders -> PropertyStateCode*/
				$column["County"] = 7; /* tOrders -> PropertyCountyName*/
				$column["Lock Expiration"] = 8; /* tOrderImport -> LockExpiration*/
				$column["Earliest Closing date"] = 9; /* tOrderImport -> EarliestClosingDate*/
				$column["Closed date"] = 10; /* tOrderImport -> ClosedDate*/
				$column["LE disclosure date"] = 11; /* tOrderImport -> LEDisclosureDate*/
				$column["Closing Disclosure sent date"] = 12; /* tOrderImport -> ClosingDisclosureSendDate*/
				$column["DocsOutDate"] = 13; /* tOrderImport -> DocsOutDate*/
				$column["SigningDate"] = 14; /* tOrderImport -> SigningDate*/
				$column["SigningTime"] = 15; /* tOrderImport -> SigningTime*/
				$column["SigningDateTime"] = 16; /* tOrderImport -> SigningDateTime*/
				$column["QueueDate"] = 17; /* tOrderImport -> QueueDate*/
				$column["Next Payment Due"] = 18; /* tOrderImport -> NextPaymentDue*/
				$column["NSM Serviving Loan number"] = 19; /* tOrderImport -> NSMServicingLoanNumber*/
				$column["Loan Amount"] = 20; /* LoanAmount -> tOrders*/
				$column["DaysinStatus"] = 21; /* tOrderImport -> DaysinStatus*/
				$column["ProcAssignDate"] = 22; /* tOrderImport -> ProcAssignDate*/
				$column["FinalApprovalDate"] = 23; /* tOrderImport -> FinalApprovalDate*/
				$column["Property Type"] = 24; /* tOrderImport -> PropertyType*/
				$column["Occupancy Status"] = 25; /* tOrderImport -> OccupancyStatus*/
				$column["Resubmittal Count"] = 26; /* tOrderImport -> ResubmittalCount*/
				$column["TitleCompany"] = 27; /* tOrderImport -> TitleInsuranceCompanyName*/
				$column["WelcomeCallDate"] = 28; /* tOrderImport -> WelcomeCallDate*/
				$column["ApprovalCallDate"] = 29; /* tOrderImport -> ApprovalCallDate*/
				$column["ConditionalApprovalDate"] = 30; /* tOrderImport -> ConditionalApprovalDate*/
				$column["LastNotesDays"] = 31; /* tOrderImport -> LastNotesDays*/
				$column["LastNotesDate"] = 32; /* tOrderImport -> LastNotesDate*/
				$column["NewLoanDays"] = 33; /* tOrderImport -> NewLoanDays*/
				$column["Aging"] = 34; /* tOrderImport -> Aging*/
				$column["NewLoanDaysBucket"] = 35; /* tOrderImport -> NewLoanDaysBucket*/
				$column["MP"] = 36; /* tOrderImport -> MP*/
				$column["MPManager"] = 37; /* tOrderImport -> MPManager*/
				$column["Funder"] = 38; /* tOrderImport -> Funder*/
				$column["BwrEmail"] = 39; /* tOrderImport -> BwrEmail*/
				$column["PriorLoanNumber"] = 40; /* tOrderImport -> PriorLoanNumber*/
				$column["Borrower Cell Number"] = 41; /* tOrderImport -> BorrowerCellNumber*/
				$column["Branch ID"] = 42; /* tOrderImport -> BranchID*/
				$column["Funding Milestone Date"] = 43; /* tOrderImport -> FundingMilestoneDate*/
				$column["Product Description"] = 44; /* tOrderImport -> ProductDescription*/
				$column["Funding Date"] = 45; /* tOrderImport -> FundingDate*/
				$column["First Payment Date"] = 46; /* tOrderImport -> FirstPaymentDate*/
				$column["Title Docs"] = 47; /* tOrderImport -> TitleDocs*/
				$column["CashFromBorrower"] = 48; /* tOrderImport -> CashFromBorrower*/
				$column["ProposedTotalHousingExpense"] = 49; /* tOrderImport -> ProposedTotalHousingExpense*/
				$column["Assets"] = 50; /* tOrderImport -> Assets*/
				$column["Queue"] = 51; /* tOrderImport -> Queue*/
				$column["MaritalStatus"] = 52; /* tOrderImport -> MaritalStatus*/
				$column["ZipCode"] = 53; /* tOrderImport -> ZipCode*/
				$column["DOB"] = 54; /* tOrderImport -> DOB*/
				$column["Term"] = 55; /* tOrderImport -> Term*/
				$column["NoteRate"] = 56; /* tOrderImport -> NoteRate*/
				$column["TotalCount"] = 57; /*  */
				return $column;
			} elseif ($BulkImportFormat == "NRZ-Bulk_Upload") {
				$column["Loan Number"] = 0; /* tOrders -> LoanNumber*/
				$column["Borrower Name"] = 1; /* tOrderPropertyRole -> BorrowerFirstName*/
				$column["Processor"] = 2; /* tOrderImport -> LoanProcessor*/
				$column["Milestone"] = 3; /* tOrders, tOrderMileStone -> MilestoneUID*/
				$column["SubStatus last changed date"] = 4; /* tOrderImport -> SubStatusLastChangedDate*/
				$column["LoanType"] = 5; /* tOrders -> LoanType*/
				$column["State"] = 6; /* tOrders -> PropertyStateCode*/
				$column["County"] = 7; /* tOrders -> PropertyCountyName*/
				$column["Lock Expiration"] = 8; /* tOrderImport -> LockExpiration*/
				$column["Earliest Closing date"] = 9; /* tOrderImport -> EarliestClosingDate*/
				$column["Closed date"] = 10; /* tOrderImport -> ClosedDate*/
				$column["LE disclosure date"] = 11; /* tOrderImport -> LEDisclosureDate*/
				$column["Closing Disclosure sent date"] = 12; /* tOrderImport -> ClosingDisclosureSendDate*/
				$column["DocsOutDate"] = 13; /* tOrderImport -> DocsOutDate*/
				$column["SigningDate"] = 14; /* tOrderImport -> SigningDate*/
				$column["SigningTime"] = 15; /* tOrderImport -> SigningTime*/
				$column["SigningDateTime"] = 16; /* tOrderImport -> SigningDateTime*/
				$column["QueueDate"] = 17; /* tOrderImport -> QueueDate*/
				$column["Next Payment Due"] = 18; /* tOrderImport -> NextPaymentDue*/
				$column["NSM Serviving Loan number"] = 19; /* tOrderImport -> NSMServicingLoanNumber*/
				$column["Loan Amount"] = 20; /* LoanAmount -> tOrders*/
				$column["DaysinStatus"] = 21; /* tOrderImport -> DaysinStatus*/
				$column["ProcAssignDate"] = 22; /* tOrderImport -> ProcAssignDate*/
				$column["FinalApprovalDate"] = 23; /* tOrderImport -> FinalApprovalDate*/
				$column["Property Type"] = 24; /* tOrderImport -> PropertyType*/
				$column["Occupancy Status"] = 25; /* tOrderImport -> OccupancyStatus*/
				$column["Resubmittal Count"] = 26; /* tOrderImport -> ResubmittalCount*/
				$column["TitleCompany"] = 27; /* tOrderImport -> TitleInsuranceCompanyName*/
				$column["WelcomeCallDate"] = 28; /* tOrderImport -> WelcomeCallDate*/
				$column["ApprovalCallDate"] = 29; /* tOrderImport -> ApprovalCallDate*/
				$column["ConditionalApprovalDate"] = 30; /* tOrderImport -> ConditionalApprovalDate*/
				$column["LastNotesDays"] = 31; /* tOrderImport -> LastNotesDays*/
				$column["LastNotesDate"] = 32; /* tOrderImport -> LastNotesDate*/
				$column["NewLoanDays"] = 33; /* tOrderImport -> NewLoanDays*/
				$column["Aging"] = 34; /* tOrderImport -> Aging*/
				$column["NewLoanDaysBucket"] = 35; /* tOrderImport -> NewLoanDaysBucket*/
				$column["MP"] = 36; /* tOrderImport -> MP*/
				$column["MPManager"] = 37; /* tOrderImport -> MPManager*/
				$column["Funder"] = 38; /* tOrderImport -> Funder*/
				$column["BwrEmail"] = 39; /* tOrderImport -> BwrEmail*/
				$column["PriorLoanNumber"] = 40; /* tOrderImport -> PriorLoanNumber*/
				$column["Borrower Cell Number"] = 41; /* tOrderImport -> BorrowerCellNumber*/
				$column["Branch ID"] = 42; /* tOrderImport -> BranchID*/
				$column["Funding Milestone Date"] = 43; /* tOrderImport -> FundingMilestoneDate*/
				$column["Product Description"] = 44; /* tOrderImport -> ProductDescription*/
				$column["Funding Date"] = 45; /* tOrderImport -> FundingDate*/
				$column["First Payment Date"] = 46; /* tOrderImport -> FirstPaymentDate*/
				$column["Title Docs"] = 47; /* tOrderImport -> TitleDocs*/
				$column["CashFromBorrower"] = 48; /* tOrderImport -> CashFromBorrower*/
				$column["ProposedTotalHousingExpense"] = 49; /* tOrderImport -> ProposedTotalHousingExpense*/
				$column["Assets"] = 50; /* tOrderImport -> Assets*/
				$column["Queue"] = 51; /* tOrderImport -> Queue*/
				$column["MinApprovedDate"] = 52; /* tOrderImport -> MinApprovedDate*/
				$column["TotalCount"] = 53; /*  */
				return $column;
			} else {
				return array ('Loan Number'=>0,'Loan Amount'=>1,'Loan Type'=>2,'Customer Reference Number'=>3,'Property Address'=>4,'Property City'=>5,'Property County'=>6,'Property State'=>7,'Property Zip Code'=>8,'APN'=>9,'Borrower Name 1'=>10,'Email 1'=>11,'Home Number 1'=>12,'Work Number 1'=>13,'Cell Number 1'=>14,'Social 1'=>15,'Borrower Name 2'=>16,'Email 2'=>17,'Home Number 2'=>18,'Work Number 2'=>19,'Cell Number 2'=>20,'Social 2'=>21,'Borrower Name 3'=>22,'Email 3'=>23,'Home Number 3'=>24,'Work Number 3'=>25,'Cell Number 3'=>26,'Social 3'=>27,'Borrower Name 4'=>28,'Email 4'=>29,'Home Number 4'=>30,'Work Number 4'=>31,'Cell Number 4'=>32,'Social 4'=>33,'Borrower Name 5'=>34,'Email 5'=>35,'Home Number 5'=>36,'Work Number 5'=>37,'Cell Number 5'=>38,'Social 5'=>39,'TotalCount'=>40);
				
			}
		}

		function excel_header_columns()
		{
			$sessiondata = $this->getImportSessionDetails();
			$CustomerUID = $sessiondata['CustomerUID'];
			$ProductUID = $sessiondata['ProductUID'];
			$ProjectUID = $sessiondata['ProjectUID'];

			$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
			$BulkImportFormat = "";
			if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
				$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
			}

			if ($BulkImportFormat == "LOP-Assignment") {
				$column['A'] = "Rejection Details";
				$column['B'] = "Loan Number";
				$column['C'] = "Borrower Name";
				$column['D'] = "Processor";
				$column['E'] = "Milestone";
				$column['F'] = "SubStatus last changed date";
				$column['G'] = "LoanType";
				$column['H'] = "State";
				$column['I'] = "County";
				$column['J'] = "Lock Expiration";
				$column['K'] = "Earliest Closing date";
				$column['L'] = "Closed date";
				$column['M'] = "LE disclosure date";
				$column['N'] = "Closing Disclosure sent date";
				$column['O'] = "DocsOutDate";
				$column['P'] = "SigningDate";
				$column['Q'] = "SigningTime";
				$column['R'] = "SigningDateTime";
				$column['S'] = "QueueDate";
				$column['T'] = "Next Payment Due";
				$column['U'] = "NSM Serviving Loan number";
				$column['V'] = "Loan Amount";
				$column['W'] = "DaysinStatus";
				$column['X'] = "ProcAssignDate";
				$column['Y'] = "FinalApprovalDate";
				$column['Z'] = "Property Type";
				$column['AA'] = "Occupancy Status";
				$column['AB'] = "Resubmittal Count";
				$column['AC'] = "TitleCompany";
				$column['AD'] = "WelcomeCallDate";
				$column['AE'] = "ApprovalCallDate";
				$column['AF'] = "ConditionalApprovalDate";
				$column['AG'] = "LastNotesDays";
				$column['AH'] = "LastNotesDate";
				$column['AI'] = "NewLoanDays";
				$column['AJ'] = "Aging";
				$column['AK'] = "NewLoanDaysBucket";
				$column['AL'] = "MP";
				$column['AM'] = "MPManager";
				$column['AN'] = "Funder";
				$column['AO'] = "BwrEmail";
				$column['AP'] = "PriorLoanNumber";
				$column['AQ'] = "Borrower Cell Number";
				$column['AR'] = "Branch ID";
				$column['AS'] = "Funding Milestone Date";
				$column['AT'] = "Product Description";
				$column['AU'] = "Funding Date";
				$column['AV'] = "First Payment Date";
				$column['AW'] = "Title Docs";
				$column['AX'] = "CashFromBorrower";
				$column['AY'] = "ProposedTotalHousingExpense";
				$column['AZ'] = "Assets";
				$column['BA'] = "Queue";
				$column['BB'] = "MaritalStatus";
				$column['BC'] = "ZipCode";
				$column['BD'] = "DOB";
				$column['BE'] = "Term";
				$column['BF'] = "Note Rate";
				return $column;
			} elseif ($BulkImportFormat == "NRZ-Bulk_Upload") {
				$column['A'] = "Rejection Details";
				$column['B'] = "Loan Number";
				$column['C'] = "Borrower Name";
				$column['D'] = "Processor";
				$column['E'] = "Milestone";
				$column['F'] = "SubStatus last changed date";
				$column['G'] = "LoanType";
				$column['H'] = "State";
				$column['I'] = "County";
				$column['J'] = "Lock Expiration";
				$column['K'] = "Earliest Closing date";
				$column['L'] = "Closed date";
				$column['M'] = "LE disclosure date";
				$column['N'] = "Closing Disclosure sent date";
				$column['O'] = "DocsOutDate";
				$column['P'] = "SigningDate";
				$column['Q'] = "SigningTime";
				$column['R'] = "SigningDateTime";
				$column['S'] = "QueueDate";
				$column['T'] = "Next Payment Due";
				$column['U'] = "NSM Serviving Loan number";
				$column['V'] = "Loan Amount";
				$column['W'] = "DaysinStatus";
				$column['X'] = "ProcAssignDate";
				$column['Y'] = "FinalApprovalDate";
				$column['Z'] = "Property Type";
				$column['AA'] = "Occupancy Status";
				$column['AB'] = "Resubmittal Count";
				$column['AC'] = "TitleCompany";
				$column['AD'] = "WelcomeCallDate";
				$column['AE'] = "ApprovalCallDate";
				$column['AF'] = "ConditionalApprovalDate";
				$column['AG'] = "LastNotesDays";
				$column['AH'] = "LastNotesDate";
				$column['AI'] = "NewLoanDays";
				$column['AJ'] = "Aging";
				$column['AK'] = "NewLoanDaysBucket";
				$column['AL'] = "MP";
				$column['AM'] = "MPManager";
				$column['AN'] = "Funder";
				$column['AO'] = "BwrEmail";
				$column['AP'] = "PriorLoanNumber";
				$column['AQ'] = "Borrower Cell Number";
				$column['AR'] = "Branch ID";
				$column['AS'] = "Funding Milestone Date";
				$column['AT'] = "Product Description";
				$column['AU'] = "Funding Date";
				$column['AV'] = "First Payment Date";
				$column['AW'] = "Title Docs";
				$column['AX'] = "CashFromBorrower";
				$column['AY'] = "ProposedTotalHousingExpense";
				$column['AZ'] = "Assets";
				$column['BA'] = "Queue";
				$column['BB'] = "MinApprovedDate";
				return $column;
			} else{

				return array (
					'A'=>'Loan Number','B'=>'Loan Amount','C'=>'Loan Type','D'=>'Customer Reference Number','E'=>'Property Address','F'=>'Property City','G'=>'Property County','H'=>'Property State','I'=>'Property Zip Code','J'=>'APN','K'=>'Borrower Name 1','L'=>'Email 1','M'=>'Home Number 1','N'=>'Work Number 1','O'=>'Cell Number 1','P'=>'Social 1','Q'=>'Borrower Name 2','R'=>'Email 2','S'=>'Home Number 2','T'=>'Work Number 2','U'=>'Cell Number 2','V'=>'Social 2','W'=>'Borrower Name 3','X'=>'Email 3','Y'=>'Home Number 3','Z'=>'Work Number 3','AA'=>'Cell Number 3','AB'=>'Social 3','AC'=>'Borrower Name 4','AD'=>'Email 4','AE'=>'Home Number 4','AF'=>'Work Number 4','AG'=>'Cell Number 4','AH'=>'Social 4','AI'=>'Borrower Name 5','AJ'=>'Email 5','AK'=>'Home Number 5','AL'=>'Work Number 5','AM'=>'Cell Number 5','AN'=>'Social 5'
				);
			}
		}

		function save_excel_header_columns()
		{
			$sessiondata = $this->getImportSessionDetails();
			$CustomerUID = $sessiondata['CustomerUID'];
			$ProductUID = $sessiondata['ProductUID'];
			$ProjectUID = $sessiondata['ProjectUID'];

			$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
			$BulkImportFormat = "";
			if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
				$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
			}

			if ($BulkImportFormat == "LOP-Assignment") {
				$column['A'] = "Rejection Details";
				$column['B'] = "Order Number";
				$column['C'] = "Loan Number";
				$column['D'] = "Borrower Name";
				$column['E'] = "Processor";
				$column['F'] = "Milestone";
				$column['G'] = "SubStatus last changed date";
				$column['H'] = "LoanType";
				$column['I'] = "State";
				$column['J'] = "County";
				$column['K'] = "Lock Expiration";
				$column['L'] = "Earliest Closing date";
				$column['M'] = "Closed date";
				$column['N'] = "LE disclosure date";
				$column['O'] = "Closing Disclosure sent date";
				$column['P'] = "DocsOutDate";
				$column['Q'] = "SigningDate";
				$column['R'] = "SigningTime";
				$column['S'] = "SigningDateTime";
				$column['T'] = "QueueDate";
				$column['U'] = "Next Payment Due";
				$column['V'] = "NSM Serviving Loan number";
				$column['W'] = "Loan Amount";
				$column['X'] = "DaysinStatus";
				$column['Y'] = "ProcAssignDate";
				$column['Z'] = "FinalApprovalDate";
				$column['AA'] = "Property Type";
				$column['AB'] = "Occupancy Status";
				$column['AC'] = "Resubmittal Count";
				$column['AD'] = "TitleCompany";
				$column['AE'] = "WelcomeCallDate";
				$column['AF'] = "ApprovalCallDate";
				$column['AG'] = "ConditionalApprovalDate";
				$column['AH'] = "LastNotesDays";
				$column['AI'] = "LastNotesDate";
				$column['AJ'] = "NewLoanDays";
				$column['AK'] = "Aging";
				$column['AL'] = "NewLoanDaysBucket";
				$column['AM'] = "MP";
				$column['AN'] = "MPManager";
				$column['AO'] = "Funder";
				$column['AP'] = "BwrEmail";
				$column['AQ'] = "PriorLoanNumber";
				$column['AR'] = "Borrower Cell Number";
				$column['AS'] = "Branch ID";
				$column['AT'] = "Funding Milestone Date";
				$column['AU'] = "Product Description";
				$column['AV'] = "Funding Date";
				$column['AW'] = "First Payment Date";
				$column['AX'] = "Title Docs";
				$column['AY'] = "CashFromBorrower";
				$column['AZ'] = "ProposedTotalHousingExpense";
				$column['BA'] = "Assets";
				$column['BB'] = "Queue";
				$column['BC'] = "MaritalStatus";
				$column['BD'] = "ZipCode";
				$column['BE'] = "DOB";
				$column['BF'] = "Term";
				$column['BG'] = "Note Rate";
				return $column;
			} elseif ($BulkImportFormat == "NRZ-Bulk_Upload") {
				$column['A'] = "Rejection Details";
				$column['B'] = "Order Number";
				$column['C'] = "Loan Number";
				$column['D'] = "Borrower Name";
				$column['E'] = "Processor";
				$column['F'] = "Milestone";
				$column['G'] = "SubStatus last changed date";
				$column['H'] = "LoanType";
				$column['I'] = "State";
				$column['J'] = "County";
				$column['K'] = "Lock Expiration";
				$column['L'] = "Earliest Closing date";
				$column['M'] = "Closed date";
				$column['N'] = "LE disclosure date";
				$column['O'] = "Closing Disclosure sent date";
				$column['P'] = "DocsOutDate";
				$column['Q'] = "SigningDate";
				$column['R'] = "SigningTime";
				$column['S'] = "SigningDateTime";
				$column['T'] = "QueueDate";
				$column['U'] = "Next Payment Due";
				$column['V'] = "NSM Serviving Loan number";
				$column['W'] = "Loan Amount";
				$column['X'] = "DaysinStatus";
				$column['Y'] = "ProcAssignDate";
				$column['Z'] = "FinalApprovalDate";
				$column['AA'] = "Property Type";
				$column['AB'] = "Occupancy Status";
				$column['AC'] = "Resubmittal Count";
				$column['AD'] = "TitleCompany";
				$column['AE'] = "WelcomeCallDate";
				$column['AF'] = "ApprovalCallDate";
				$column['AG'] = "ConditionalApprovalDate";
				$column['AH'] = "LastNotesDays";
				$column['AI'] = "LastNotesDate";
				$column['AJ'] = "NewLoanDays";
				$column['AK'] = "Aging";
				$column['AL'] = "NewLoanDaysBucket";
				$column['AM'] = "MP";
				$column['AN'] = "MPManager";
				$column['AO'] = "Funder";
				$column['AP'] = "BwrEmail";
				$column['AQ'] = "PriorLoanNumber";
				$column['AR'] = "Borrower Cell Number";
				$column['AS'] = "Branch ID";
				$column['AT'] = "Funding Milestone Date";
				$column['AU'] = "Product Description";
				$column['AV'] = "Funding Date";
				$column['AW'] = "First Payment Date";
				$column['AX'] = "Title Docs";
				$column['AY'] = "CashFromBorrower";
				$column['AZ'] = "ProposedTotalHousingExpense";
				$column['BA'] = "Assets";
				$column['BB'] = "Queue";
				$column['BC'] = "MinApprovedDate";
				return $column;
			}
			else{

				return array (
					'A'=>'Order Number',
					'B'=>'Loan Number',
					'C'=>'Loan Amount',
					'D'=>'Loan Type',
					'E'=>'Customer Reference Number',
					'F'=>'Property Address',
					'G'=>'Property City',
					'H'=>'Property County',
					'I'=>'Property State',
					'J'=>'Property Zip Code',
					'K'=>'APN',
					'L'=>'Borrower Name 1',
					'M'=>'Email 1',
					'N'=>'Home Number 1',
					'O'=>'Work Number 1',
					'P'=>'Cell Number 1',
					'Q'=>'Social 1',
					'R'=>'Borrower Name 2',
					'S'=>'Email 2',
					'T'=>'Home Number 2',
					'U'=>'Work Number 2',
					'V'=>'Cell Number 2',
					'W'=>'Social 2',
					'X'=>'Borrower Name 3',
					'Y'=>'Email 3',
					'Z'=>'Home Number 3',
					'AA'=>'Work Number 3',
					'AB'=>'Cell Number 3',
					'AC'=>'Social 3',
					'AD'=>'Borrower Name 4',
					'AE'=>'Email 4',
					'AF'=>'Home Number 4',
					'AG'=>'Work Number 4',
					'AH'=>'Cell Number 4',
					'AI'=>'Social 4',
					'AJ'=>'Borrower Name 5',
					'AK'=>'Email 5',
					'AL'=>'Home Number 5',
					'AM'=>'Work Number 5',
					'AN'=>'Cell Number 5',
					'AO'=>'Social 5'
				);
			}
		}

		function common_excelrows($objPHPExcel,$no,$value)
		{

			$excelalphas = [0 => 'A',1 => 'B',2 => 'C',3 => 'D',4 => 'E',5 => 'F',6 => 'G',7 => 'H',8 => 'I',9 => 'J',10 => 'K',11 => 'L',12 => 'M',13 => 'N',14 => 'O',15 => 'P',16 => 'Q',17 => 'R',18 => 'S',19 => 'T',20 => 'U',21 => 'V',22 => 'W',23 => 'X',24 => 'Y',25 => 'Z',26 => 'AA',27 => 'AB',28 => 'AC',29 => 'AD',30 => 'AE',31 => 'AF',32 => 'AG',33 => 'AH',34 => 'AI',35 => 'AJ',36 => 'AK',37 => 'AL',38 => 'AM',39 => 'AN',40=>'AO',41=>'AP',42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',50=>'AY',51=>'AZ',52=>'BA',53=>'BB'];

			for ($i=0; $i < count($value)-2; $i++) { 
				$objPHPExcel->getActiveSheet()->setCellValue($excelalphas[$i].$no, $value[$i]);
			}
			return $objPHPExcel;
		}


		function outputPreviewExcel()
		{

			$data = [];
			$filelink = $this->config->item('UploadPath') . $this->loggedid . '/results.json';
			if (file_exists($filelink)) {
				$filecontents = file_get_contents($filelink);
				$data = json_decode($filecontents, true);
				$data = $data['data'];
			}


			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);

			$ColumnArray = $this->excel_header_columns();

			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {
				$objPHPExcel = $this->common_excelrows($objPHPExcel,$no,$value);
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Followup.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}

		function outputupdatePreviewExcel()
		{
			$data = [];
			$filelink = $this->config->item('UploadPath') . $this->loggedid . '/updateresults.json';
			if (file_exists($filelink)) {
				$filecontents = file_get_contents($filelink);
				$data = json_decode($filecontents, true);
				$data = $data['data'];
			}


			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);

			$ColumnArray = $this->excel_header_columns();
			$ColumnArray['AT'] = 'Rejection Details';
			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {
				$objPHPExcel = $this->common_excelrows($objPHPExcel,$no,$value);
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Followup.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}

		function outputSaveExcel()
		{


			$data = [];
			$filelink = $this->config->item('UploadPath') . $this->loggedid . '/successresults.json';
			if (file_exists($filelink)) {
				$filecontents = file_get_contents($filelink);
				$data = json_decode($filecontents, true);
				$data = $data['data'];
			}


			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$ColumnArray = $this->save_excel_header_columns();

			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {
				$objPHPExcel = $this->common_excelrows($objPHPExcel,$no,$value);
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="BulkImport_Success.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}


		function outputFailedExcel()
		{
			$data = [];
			$filelink = $this->config->item('UploadPath') . $this->loggedid . '/failedresults.json';
			if (file_exists($filelink)) {
				$filecontents = file_get_contents($filelink);
				$data = json_decode($filecontents, true);
				$data = $data['data'];
			}


			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$ColumnArray = $this->excel_header_columns();
			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {
				$objPHPExcel = $this->common_excelrows($objPHPExcel,$no,$value);	
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="BulkImport_Failed.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}

		function outputupdateSuccessExcel()
		{
			$data = [];
			$filelink = $this->config->item('UploadPath') . $this->loggedid . '/updatesuccessresults.json';
			if (file_exists($filelink)) {
				$filecontents = file_get_contents($filelink);
				$data = json_decode($filecontents, true);
				$data = $data['data'];
			}


			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$ColumnArray = $this->excel_header_columns();

			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {
				$objPHPExcel = $this->common_excelrows($objPHPExcel,$no,$value);
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="BulkImport_Success.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}


		function outputUpdateFailedExcel()
		{
			$data = [];
			$filelink = $this->config->item('UploadPath') . $this->loggedid . '/updatefailedresults.json';
			if (file_exists($filelink)) {
				$filecontents = file_get_contents($filelink);
				$data = json_decode($filecontents, true);
				$data = $data['data'];
			}


			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$ColumnArray = $this->excel_header_columns();

			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {
				$objPHPExcel = $this->common_excelrows($objPHPExcel,$no,$value);	
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="BulkImport_Failed.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}

		function GenerateSuccessRows($SuccessData, $CustomerUID, $ProductUID)
		{

			$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
			$BulkImportFormat = "";
			if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
				$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
			}

			/*declare excel values*/
			$columnvariables = $this->column_variables($BulkImportFormat);

			$Rows = [];
			foreach ($SuccessData as $key => $a) {

				$Columns = [];
				$Columns[] = $a['result']['OrderNumber']; 

				foreach ($columnvariables as $key => $var) {
					if ($key != "TotalCount") {
						$Columns[] = $a[$var];
					}
				}
/*				$Columns[] = $a[$columnvariables['Loan Amount']]; 
				$Columns[] = $a[$columnvariables['Loan Type']]; 
				$Columns[] = $a[$columnvariables['Customer Reference Number']]; 
				$Columns[] = $a[$columnvariables['Property Address']]; 
				$Columns[] = $a[$columnvariables['Property City']]; 
				$Columns[] = $a[$columnvariables['Property County']]; 
				$Columns[] = $a[$columnvariables['Property State']]; 
				$Columns[] = $a[$columnvariables['Property Zip Code']]; 
				$Columns[] = $a[$columnvariables['APN']]; 
				$Columns[] = $a[$columnvariables['Borrower Name 1']]; 
				$Columns[] = $a[$columnvariables['Email 1']]; 
				$Columns[] = $a[$columnvariables['Home Number 1']]; 
				$Columns[] = $a[$columnvariables['Work Number 1']]; 
				$Columns[] = $a[$columnvariables['Cell Number 1']]; 
				$Columns[] = $a[$columnvariables['Social 1']]; 
				$Columns[] = $a[$columnvariables['Borrower Name 2']]; 
				$Columns[] = $a[$columnvariables['Email 2']]; 
				$Columns[] = $a[$columnvariables['Home Number 2']]; 
				$Columns[] = $a[$columnvariables['Work Number 1']]; 
				$Columns[] = $a[$columnvariables['Cell Number 2']]; 
				$Columns[] = $a[$columnvariables['Social 2']]; 
				$Columns[] = $a[$columnvariables['Borrower Name 3']]; 
				$Columns[] = $a[$columnvariables['Email 3']]; 
				$Columns[] = $a[$columnvariables['Home Number 3']]; 
				$Columns[] = $a[$columnvariables['Work Number 3']]; 
				$Columns[] = $a[$columnvariables['Cell Number 3']]; 
				$Columns[] = $a[$columnvariables['Social 3']]; 
				$Columns[] = $a[$columnvariables['Borrower Name 4']]; 
				$Columns[] = $a[$columnvariables['Email 4']]; 
				$Columns[] = $a[$columnvariables['Home Number 4']]; 
				$Columns[] = $a[$columnvariables['Work Number 4']]; 
				$Columns[] = $a[$columnvariables['Cell Number 4']]; 
				$Columns[] = $a[$columnvariables['Social 4']]; 
				$Columns[] = $a[$columnvariables['Borrower Name 5']]; 
				$Columns[] = $a[$columnvariables['Email 5']]; 
				$Columns[] = $a[$columnvariables['Home Number 5']]; 
				$Columns[] = $a[$columnvariables['Work Number 5']]; 
				$Columns[] = $a[$columnvariables['Cell Number 5']]; 
				$Columns[] = $a[$columnvariables['Social 5']]; 
*/
				$Rows[] = $Columns;
			}

			return $Rows;

		}

		function GenerateFailedRows($FailedData, $CustomerUID, $ProductUID)
		{

			$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
			$BulkImportFormat = "";
			if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
				$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
			}

			/*declare excel values*/
			$columnvariables = $this->column_variables($BulkImportFormat);

			$Rows = [];
			$loannumbercolumns = [];
			foreach ($FailedData as $i => $a) {
				//for missing fields
				if (count($a) == $columnvariables['TotalCount']) {

					$a['ColorCode'] = '';
					$a['BGColorCode'] = '';

					$loanvalidation = $this->Orderentrymodel->is_loanno_exists($a[$columnvariables['Loan Number']]);			

					if(in_array($a[$columnvariables['Loan Number']], $loannumbercolumns) || $loanvalidation) {

						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#ff04ec';


					} else {

						$a['ColorCode'] = '';
						$a['BGColorCode'] = '';
					}


				} else {

					$a['ColorCode'] = '#fff';
					$a['BGColorCode'] = '#757575';

				}


				$Columns = [];
				foreach ($a as $key => $value) {
					$Columns[] = $value;
				}

				$Rows[] = $Columns;


			}

			return $Rows;


		}


			/**
	* preview_updatebulkentry
	* 
	* For previewing the bulk imported orders update functionality
	*
	* @added 13-6-2019
	*
	*/

	function preview_updatebulkentry()
	{


		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames = $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");

			$extension = end($temp);

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				$LenderUID = $this->input->post('LenderUID');

				$FileUploadPreview = [];


				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the Required Fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);


				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkImportFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
					$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->column_variables($BulkImportFormat);



				$objWorksheet = $objPHPExcel->getActiveSheet();
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$validationarray = array();

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} 
					// skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}

				}


				$posts = [];

				$loannumbercolumns = []; 

				foreach ($arrayCode as $keys => $a) {

					if (count($a) == $columnvariables['TotalCount']) {

						$checkorderexists_byloan = $this->Orderentrymodel->checkorderexistsbyloannumber($a[$columnvariables['Loan Number']],$CustomerUID,$ProductUID);

						if (!empty($checkorderexists_byloan)) {

							/*LOAN DUPLICATE VALIDATION*/
							if(in_array($a[$columnvariables['Loan Number']], $loannumbercolumns) ) {
								$a['ColorCode'] = '#fff';
								$a['BGColorCode'] = '#ff04ec';

							}
							else{
								$loannumbercolumns[] = $a[$columnvariables['Loan Number']];
								$a['ColorCode'] = '';
								$a['BGColorCode'] = '';

							}
						}
						else{
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff0013';
						}


					} else {
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
					}


					$arayhgen = [];
					foreach ($a as $key => $value) {
						$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$posts[] = $arayhgen;
				}


				$response['data'] = $posts;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updateresults.json', 'w');
					fwrite($fp, json_encode($response));
					fclose($fp);				
				}

				$data['headingsArray'] = $headingsArray;

				$preview = $this->load->view('standard_bulk_partialviews/bulk_update_preview', $data, true);

				$filelink = 'uploads/'.$this->loggedid.'/updateresults.json';

				echo json_encode(array('error' => 0, 'html' => $preview, 'filelink' => $filelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
		}

	}



	//save bulkentry function
	function update_bulkentry()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames= $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");
			$extension = end($temp);

		// files checking & handling varaiables
			$matching_files = []; $followup_orders = [];

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				



				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the required fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);


				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkImportFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkImportFormat)) {
					$BulkImportFormat = $mcustomerproducts->BulkImportFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->column_variables($BulkImportFormat);


				$objWorksheet = $objPHPExcel->getActiveSheet();
						//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];


				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}



				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
				} // skip empty row
				++$r;


				$i = 0;
				foreach ($headingsArray as $columnKey => $columnHeading) {
					$cellformat = $objWorksheet->getCell($columnKey . $row);
					if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

						$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

					}else{
						$arrayCode[$r][$i] = trim($cellformat->getValue());
					}
					$i++;
				}
			}





			$html = '';
			$FailedData = [];
			$SuccessData = [];
			$InsertedOrderUID = [];
			$InsertedOrderUIDs = '';

			$loannumbercolumns = []; 
			$propertyroledata = []; 

			foreach ($arrayCode as $i => $a) {
				
				$data = []; 

				/*LOAN DUPLICATE VALIDATION*/

				//for missing fields

				$data['CustomerUID'] = !empty($CustomerUID) ? $CustomerUID : null;
				$data['ProductUID'] = !empty($ProductUID) ? $ProductUID : null;
				$data['ProjectUID'] =  !empty($ProjectUID) ? $ProjectUID : null;

				if ($BulkImportFormat == "LOP-Assignment") {

					$mMilestone = $this->Common_Model->get_row('mMilestone', ['MilestoneName'=>trim(ltrim($a[$columnvariables['Milestone']], "0"))]);

					$data['LoanNumber'] = $a[$columnvariables['Loan Number']];
					$data['LoanAmount'] = $a[$columnvariables['Loan Amount']];

					if (!empty($mMilestone)) {
						$data['MilestoneUID'] = $mMilestone->MilestoneUID;
					}

					$data['LoanType'] = $a[$columnvariables['LoanType']];
					$data['PropertyCountyName'] = $a[$columnvariables['County']];
					$data['PropertyStateCode'] = $a[$columnvariables['State']];
					$data['PropertyZipCode'] = $a[$columnvariables['Property Zip Code']];

					$propertyroledata = [];
					if($a[$columnvariables['Borrower Name']] ) 
					{		
						$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['Borrower Name']];

					}


					$torderimport['LoanProcessor'] = $a[$columnvariables["Processor"]];
					$torderimport['SubStatusLastChangedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SubStatus last changed date"]]);
					$torderimport['LockExpiration'] = $a[$columnvariables["Lock Expiration"]];
					$torderimport['EarliestClosingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Earliest Closing date"]]);
					$torderimport['ClosedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closed date"]]);
					$torderimport['LEDisclosureDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LE disclosure date"]]);
					$torderimport['ClosingDisclosureSendDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closing Disclosure sent date"]]);
					$torderimport['DocsOutDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["DocsOutDate"]]);
					$torderimport['SigningDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDate"]]);
					$torderimport['SigningTime'] = $a[$columnvariables["SigningTime"]];
					$torderimport['SigningDateTime'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDateTime"]]);
					$torderimport['QueueDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["QueueDate"]]);
					$torderimport['NextPaymentDue'] = $a[$columnvariables["Next Payment Due"]];
					$torderimport['NSMServicingLoanNumber'] = $a[$columnvariables["NSM Serviving Loan number"]];
					$torderimport['DaysinStatus'] = $a[$columnvariables["DaysinStatus"]];
					$torderimport['ProcAssignDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ProcAssignDate"]]);
					$torderimport['FinalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["FinalApprovalDate"]]);
					$torderimport['PropertyType'] = $a[$columnvariables["Property Type"]];
					$torderimport['OccupancyStatus'] = $a[$columnvariables["Occupancy Status"]];
					$torderimport['ResubmittalCount'] = $a[$columnvariables["Resubmittal Count"]];
					$torderimport['TitleInsuranceCompanyName'] = $a[$columnvariables["TitleCompany"]];
					$torderimport['WelcomeCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["WelcomeCallDate"]]);
					$torderimport['ApprovalCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ApprovalCallDate"]]);
					$torderimport['ConditionalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ConditionalApprovalDate"]]);
					$torderimport['LastNotesDays'] = $a[$columnvariables["LastNotesDays"]];
					$torderimport['LastNotesDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LastNotesDate"]]);
					$torderimport['NewLoanDays'] = $a[$columnvariables["NewLoanDays"]];
					$torderimport['Aging'] = $a[$columnvariables["Aging"]];
					$torderimport['NewLoanDaysBucket'] = $a[$columnvariables["NewLoanDaysBucket"]];
					$torderimport['MP'] = $a[$columnvariables["MP"]];
					$torderimport['MPManager'] = $a[$columnvariables["MPManager"]];
					$torderimport['Funder'] = $a[$columnvariables["Funder"]];
					$torderimport['BwrEmail'] = $a[$columnvariables["BwrEmail"]];
					$torderimport['PriorLoanNumber'] = $a[$columnvariables["PriorLoanNumber"]];
					$torderimport['BorrowerCellNumber'] = $a[$columnvariables["Borrower Cell Number"]];
					$torderimport['BranchID'] = $a[$columnvariables["Branch ID"]];
					$torderimport['FundingMilestoneDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Milestone Date"]]);
					$torderimport['ProductDescription'] = $a[$columnvariables["Product Description"]];
					$torderimport['FundingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Date"]]);
					$torderimport['FirstPaymentDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["First Payment Date"]]);
					$torderimport['TitleDocs'] = $a[$columnvariables["Title Docs"]];
					$torderimport['CashFromBorrower'] = $a[$columnvariables["CashFromBorrower"]];
					$torderimport['ProposedTotalHousingExpense'] = $a[$columnvariables["ProposedTotalHousingExpense"]];
					$torderimport['Assets'] = $a[$columnvariables["Assets"]];
					$torderimport['Queue'] = $a[$columnvariables["Queue"]];
					$torderimport['MaritalStatus'] = $a[$columnvariables["MaritalStatus"]];
					$torderimport['ZipCode'] = $a[$columnvariables["ZipCode"]];
					$torderimport['DOB'] = $a[$columnvariables["DOB"]];
					$torderimport['Term'] = $a[$columnvariables["Term"]];
					$torderimport['NoteRate'] = $a[$columnvariables["NoteRate"]];


				} elseif ($BulkImportFormat == "NRZ-Bulk_Upload") {

					$mMilestone = $this->Common_Model->get_row('mMilestone', ['MilestoneName'=>trim(ltrim($a[$columnvariables['Milestone']], "0"))]);

					$data['LoanNumber'] = $a[$columnvariables['Loan Number']];
					$data['LoanAmount'] = $a[$columnvariables['Loan Amount']];

					if (!empty($mMilestone)) {
						$data['MilestoneUID'] = $mMilestone->MilestoneUID;
					}

					$data['LoanType'] = $a[$columnvariables['LoanType']];
					$data['PropertyCountyName'] = $a[$columnvariables['County']];
					$data['PropertyStateCode'] = $a[$columnvariables['State']];
					$data['PropertyZipCode'] = $a[$columnvariables['Property Zip Code']];

					$propertyroledata = [];
					if($a[$columnvariables['Borrower Name']] ) 
					{		
						$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['Borrower Name']];

					}


					$torderimport['LoanProcessor'] = $a[$columnvariables["Processor"]];
					$torderimport['SubStatusLastChangedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SubStatus last changed date"]]);
					$torderimport['LockExpiration'] = $a[$columnvariables["Lock Expiration"]];
					$torderimport['EarliestClosingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Earliest Closing date"]]);
					$torderimport['ClosedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closed date"]]);
					$torderimport['LEDisclosureDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LE disclosure date"]]);
					$torderimport['ClosingDisclosureSendDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Closing Disclosure sent date"]]);
					$torderimport['DocsOutDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["DocsOutDate"]]);
					$torderimport['SigningDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDate"]]);
					$torderimport['SigningTime'] = $a[$columnvariables["SigningTime"]];
					$torderimport['SigningDateTime'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningDateTime"]]);
					$torderimport['QueueDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["QueueDate"]]);
					$torderimport['NextPaymentDue'] = $a[$columnvariables["Next Payment Due"]];
					$torderimport['NSMServicingLoanNumber'] = $a[$columnvariables["NSM Serviving Loan number"]];
					$torderimport['DaysinStatus'] = $a[$columnvariables["DaysinStatus"]];
					$torderimport['ProcAssignDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ProcAssignDate"]]);
					$torderimport['FinalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["FinalApprovalDate"]]);
					$torderimport['PropertyType'] = $a[$columnvariables["Property Type"]];
					$torderimport['OccupancyStatus'] = $a[$columnvariables["Occupancy Status"]];
					$torderimport['ResubmittalCount'] = $a[$columnvariables["Resubmittal Count"]];
					$torderimport['TitleInsuranceCompanyName'] = $a[$columnvariables["TitleCompany"]];
					$torderimport['WelcomeCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["WelcomeCallDate"]]);
					$torderimport['ApprovalCallDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ApprovalCallDate"]]);
					$torderimport['ConditionalApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ConditionalApprovalDate"]]);
					$torderimport['LastNotesDays'] = $a[$columnvariables["LastNotesDays"]];
					$torderimport['LastNotesDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LastNotesDate"]]);
					$torderimport['NewLoanDays'] = $a[$columnvariables["NewLoanDays"]];
					$torderimport['Aging'] = $a[$columnvariables["Aging"]];
					$torderimport['NewLoanDaysBucket'] = $a[$columnvariables["NewLoanDaysBucket"]];
					$torderimport['MP'] = $a[$columnvariables["MP"]];
					$torderimport['MPManager'] = $a[$columnvariables["MPManager"]];
					$torderimport['Funder'] = $a[$columnvariables["Funder"]];
					$torderimport['BwrEmail'] = $a[$columnvariables["BwrEmail"]];
					$torderimport['PriorLoanNumber'] = $a[$columnvariables["PriorLoanNumber"]];
					$torderimport['BorrowerCellNumber'] = $a[$columnvariables["Borrower Cell Number"]];
					$torderimport['BranchID'] = $a[$columnvariables["Branch ID"]];
					$torderimport['FundingMilestoneDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Milestone Date"]]);
					$torderimport['ProductDescription'] = $a[$columnvariables["Product Description"]];
					$torderimport['FundingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["Funding Date"]]);
					$torderimport['FirstPaymentDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["First Payment Date"]]);
					$torderimport['TitleDocs'] = $a[$columnvariables["Title Docs"]];
					$torderimport['CashFromBorrower'] = $a[$columnvariables["CashFromBorrower"]];
					$torderimport['ProposedTotalHousingExpense'] = $a[$columnvariables["ProposedTotalHousingExpense"]];
					$torderimport['Assets'] = $a[$columnvariables["Assets"]];
					$torderimport['Queue'] = $a[$columnvariables["Queue"]];
					$torderimport['MinApprovedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["MinApprovedDate"]]);

				}
				else{


					$data['LoanNumber'] = $a[$columnvariables['Loan Number']];
					$data['LoanAmount'] = $a[$columnvariables['Loan Amount']];
					$data['LoanType'] = $a[$columnvariables['Loan Type']];
					$data['CustomerReferenceNumber'] = $a[$columnvariables['Customer Reference Number']];
					$data['PropertyAddress1'] = $a[$columnvariables['Property Address']];
					$data['PropertyCityName'] = $a[$columnvariables['Property City']];
					$data['PropertyCountyName'] = $a[$columnvariables['Property County']];
					$data['PropertyStateCode'] = $a[$columnvariables['Property State']];
					$data['PropertyZipCode'] = $a[$columnvariables['Property Zip Code']];
					$data['APN'] = $a[$columnvariables['APN']];


					for ($i = 1; $i <= 5; $i++){
						if($a[$columnvariables['Borrower Name '.$i]] || $a[$columnvariables['Email '.$i]] || $a[$columnvariables['Home Number '.$i]] || $a[$columnvariables['Work Number '.$i]] || $a[$columnvariables['Cell Number '.$i]] || $a[$columnvariables['Social '.$i]] ) 
						{		
							$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['Borrower Name '.$i]];
							$propertyroledata[$i]['BorrowerMailingAddress1'] = $a[$columnvariables['Email '.$i]];
							$propertyroledata[$i]['HomeNumber'] = $a[$columnvariables['Home Number '.$i]];
							$propertyroledata[$i]['WorkNumber'] = $a[$columnvariables['Work Number '.$i]];
							$propertyroledata[$i]['CellNumber'] = $a[$columnvariables['Cell Number '.$i]];
							$propertyroledata[$i]['Social'] = $a[$columnvariables['Social '.$i]];
						}
					}


				}


				$a['ColorCode'] = '';
				$a['BGColorCode'] = '';

				if (count($a) == ($columnvariables['TotalCount'] + 2) ) {

					$checkorderexists_byloan = $this->Orderentrymodel->checkorderexistsbyloannumber($a[$columnvariables['Loan Number']],$CustomerUID,$ProductUID);


					if (!empty($checkorderexists_byloan)) {
						
						foreach ($checkorderexists_byloan as $key => $order) {
							
							if( in_array($a[$columnvariables['Loan Number']], $loannumbercolumns) ) {

								$a['ColorCode'] = '#fff';
								$a['BGColorCode'] = '#ff04ec';
								array_push($FailedData, $a);


							}  else {

								$loannumbercolumns[] = $a[$columnvariables['Loan Number']];

								$data['OrderUID'] = $order->OrderUID;
								$data['OrderNumber'] = $order->OrderNumber;

								foreach ($torderimport as $key => $value) {
									// removes whitespace or other predefined characters from the left side of a string
									$torderimport[$key] = (ltrim($value) == '') ? NULL : ltrim($value);
								}

								$torderimport['OrderUID'] = $order->OrderUID;
								$result = $this->Orderentrymodel->updatebulkentry_order($data,$propertyroledata, $torderimport);


								$a['result'] = $result;


								if ($result == true) {

									$a['OrderNumber'] = $order->OrderNumber;;
									$InsertedOrderUID[] = $data['OrderUID'];
									$SuccessData[] = $a;


								} else {
									array_push($FailedData, $arrayCode[$i]);

								}



							}
						}
					}
					else{
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#ff0013';
						array_push($FailedData, $a);
					}
				} else {

					$a['ColorCode'] = '#fff';
					$a['BGColorCode'] = '#757575';
					array_push($FailedData, $a);


				}

			}


			$previewdata['headingsArray'] = $headingsArray;
			$previewdata['InsertedOrderUID'] = implode(',', $InsertedOrderUID);


			$SuccessRows = [];
			foreach ($SuccessData as $key => $sa) {

				$Columns = [];
				$Columns[] = $sa['OrderNumber']; 

				foreach ($columnvariables as $key => $var) {
					if ($key != "TotalCount") {
						$Columns[] = $this->Common_Model->validateConvertDateToFormat($sa[$var], "m/d/Y h:i A");
					}
				}

				$SuccessRows[] = $Columns;
			}


			$SuccessJSON = $SuccessRows;


			$successresponse['data'] = $SuccessJSON;
			$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
			if($this->Common_Model->CreateDirectoryToPath($PATH)){
				$fp = fopen($PATH . 'updatesuccessresults.json', 'w');
				fwrite($fp, json_encode($successresponse));
				fclose($fp);				
			}

			$FailedRows = [];
			foreach ($FailedData as $i => $fa) {

				$Columns = [];
				foreach ($fa as $key => $value) {
					$Columns[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
				}

				$FailedRows[] = $Columns;
			}

			$FailedJSON = $FailedRows;

			$failedresponse['data'] = $FailedJSON;
			$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
			if($this->Common_Model->CreateDirectoryToPath($PATH)){
				$fp = fopen($PATH . 'updatefailedresults.json', 'w');
				fwrite($fp, json_encode($failedresponse));
				fclose($fp);				
			}


			$html = $this->load->view('standard_bulk_partialviews/bulk_updated', $previewdata, true);

			$successfilelink = 'uploads/'.$this->loggedid.'/updatesuccessresults.json';
			$failedfilelink = 'uploads/'.$this->loggedid.'/updatefailedresults.json';


			echo json_encode(array('error' => 0, 'html' => $html, 'matching_files'=>$matching_files, 'message'=>'Upload Success', 'successfilelink' => $successfilelink, 'failedfilelink' => $failedfilelink)); exit;

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please choose an valid file'));
		}

	} else {
		echo json_encode(array('error' => '1', 'message' => 'Please upload file'));
	}

}

function setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID)
{
	$this->session->set_userdata(['ImportCustomerUID'=>$CustomerUID, 'ImportProductUID'=>$ProductUID, 'ImportProjectUID'=>$ProjectUID]);
}

function getImportSessionDetails()
{
	$sessiondata = $this->session->userdata();
	return ['CustomerUID'=>$sessiondata['ImportCustomerUID'], 'ProductUID'=>$sessiondata['ImportProductUID'], 'ProjectUID'=>$sessiondata['ImportProjectUID']];
}


function test()
{
	echo $this->Common_Model->validateConvertDateToFormat("269-271-0710", "m/d/Y h:i A");
}

	/**
	*Function Duplicate Loan Number with respect to client
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 18 May 2020
	*/
	public function check_loannumber() {

		$ClientUID = $this->input->post('Customer');// get client
		$LoanNumber = $this->input->post('LoanNumber');// get Loan cNumber
		
		$this->form_validation->set_message('check_loannumber', 'Duplicate {field}');

		if(empty($LoanNumber)) {
			return TRUE;
		}

		$Client = $this->Orderentrymodel->get_customerbyuid($ClientUID);

		if(empty($Client)) {
			return TRUE;
		}

		$LoanNumberValidation = $Client->LoanNumberValidation;

		if(empty($LoanNumberValidation)) {
			return TRUE;
		}

		if(strlen($LoanNumber) == $LoanNumberValidation) {

			$this->db->select('tOrders.OrderNumber');
			$this->db->from('tOrders');
			$this->db->where('CustomerUID', $ClientUID);
			$this->db->where('LoanNumber', $LoanNumber);
			$query = $this->db->get();
			$num = $query->num_rows();
			if ($num > 0) {
				return FALSE;
			} else {
				return TRUE;
			}
		}

		$this->form_validation->set_message('check_loannumber', 'Loan Number should be exactly '.$LoanNumberValidation.' characters');

		return FALSE;
	}

	public function bulkAssign()
	{
		
		$data['content'] = 'bulkAssign';

		$data['Customers'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$data['ProductsDetails'] =$this->Common_Model->ProductsDetails();
		$data['ProjectDetails'] =$this->Common_Model->ProjectDetails();
		$data['OrderPriority'] =$this->Common_Model->get('mOrderPriority',['Active'=>1]);
		$data['SettlementAgent'] =$this->Common_Model->get('mSettlementAgent',['Active'=>1]);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/**
	*Function Bulk Assign Preview 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Saturday 01 August 2020.
	*/
	//save bulkentry function
	function updateBulkAssign()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames= $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");
			$extension = end($temp);

			// files checking & handling varaiables
			$matching_files = []; $followup_orders = [];

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				



				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the required fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);


				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkAssignFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkAssignFormat)) {
					$BulkAssignFormat = $mcustomerproducts->BulkAssignFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->bulkAssignColumnVariables($BulkAssignFormat);


				$objWorksheet = $objPHPExcel->getActiveSheet();
						//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];


				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} // skip empty row
					++$r;


					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}
				}

				$html = '';
				$FailedData = [];
				$SuccessData = [];
				$InsertedOrderUID = [];
				$InsertedOrderUIDs = '';

				$loannumbercolumns = []; 
				$propertyroledata = []; 
				$data = []; 

				// echo '<pre>';print_r($arrayCode);exit;

				foreach ($arrayCode as $i => $a) {

					$a['ColorCode'] = '';
					$a['BGColorCode'] = '';

					if (count($a) == ($columnvariables['TotalCount'] + 2) ) {

						$checkorderexists_byloan = $this->Orderentrymodel->CheckOrderExistsforCustomer($a[$columnvariables['LoanNumber']],$CustomerUID);

						/*LOAN NUMBER VALIDATION*/
						if (!empty($checkorderexists_byloan)) {
							
							foreach ($checkorderexists_byloan as $key => $order) {

								/*WORKFLOW VALIDATION*/
								$WorkflowValidation = $this->Orderentrymodel->isWorklflowExistsForCustomer($CustomerUID,$a[$columnvariables['Workflow']]);

								/*ASSOCIATE VALIDATION*/
								$AssociateValidation = $this->Orderentrymodel->isAssociateExistsForCustoemr($CustomerUID,$a[$columnvariables['Associate']]);

								if(empty($WorkflowValidation) || empty($a[$columnvariables['Workflow']])) {

									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#e73f3b';
									array_push($FailedData, $a);

								} else if($a[$columnvariables['UnAssign']] == "YES") {

									// Un Assign
									$result = $this->Orderentrymodel->orderWorkflowUnAssign($order->OrderUID, $WorkflowValidation->WorkflowModuleUID);

									$a['result'] = $result;

									if ($result == true) {

										$a['OrderNumber'] = $order->OrderNumber;
										$InsertedOrderUID[] = $data['OrderUID'];
										$SuccessData[] = $a;

									} else {
										array_push($FailedData, $arrayCode[$i]);

									}
								} else if(empty($AssociateValidation) || empty($a[$columnvariables['Associate']])) {

									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#4b669e';
									array_push($FailedData, $a);

								} else {

									// Assign and ReAssign
									$result = $this->Common_Model->OrderAssign($order->OrderUID, $WorkflowValidation->WorkflowModuleUID, $AssociateValidation->UserUID);

									$a['result'] = $result;

									if ($result == true) {

										$a['OrderNumber'] = $order->OrderNumber;
										$InsertedOrderUID[] = $data['OrderUID'];
										$SuccessData[] = $a;

									} else {
										array_push($FailedData, $arrayCode[$i]);

									}									

								}
							}
						}
						else{
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff04ec';
							array_push($FailedData, $a);
						}
					} else {

						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
						array_push($FailedData, $a);


					}

				}


				$previewdata['headingsArray'] = $headingsArray;
				$previewdata['InsertedOrderUID'] = implode(',', $InsertedOrderUID);


				$SuccessRows = [];
				foreach ($SuccessData as $key => $sa) {

					$Columns = [];
					$Columns[] = $sa['OrderNumber']; 

					foreach ($columnvariables as $key => $var) {
						if ($key != "TotalCount") {
							$Columns[] = $this->Common_Model->validateConvertDateToFormat($sa[$var], "m/d/Y h:i A");
						}
					}

					$SuccessRows[] = $Columns;
				}


				$SuccessJSON = $SuccessRows;


				$successresponse['data'] = $SuccessJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatesuccessresults.json', 'w');
					fwrite($fp, json_encode($successresponse));
					fclose($fp);				
				}

				$FailedRows = [];
				foreach ($FailedData as $i => $fa) {

					$Columns = [];
					foreach ($fa as $key => $value) {
						$Columns[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$FailedRows[] = $Columns;
				}

				$FailedJSON = $FailedRows;

				$failedresponse['data'] = $FailedJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatefailedresults.json', 'w');
					fwrite($fp, json_encode($failedresponse));
					fclose($fp);				
				}


				$html = $this->load->view('standard_bulkassign_partialviews/bulk_updated', $previewdata, true);

				$successfilelink = 'uploads/'.$this->loggedid.'/updatesuccessresults.json';
				$failedfilelink = 'uploads/'.$this->loggedid.'/updatefailedresults.json';


				echo json_encode(array('error' => 0, 'html' => $html, 'matching_files'=>$matching_files, 'message'=>'Upload Success', 'successfilelink' => $successfilelink, 'failedfilelink' => $failedfilelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please choose an valid file'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload file'));
		}

	}

	/**
	*Function Bulk Assign Preview 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Saturday 01 August 2020.
	*/
	function previewBulkAssign()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames = $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");

			$extension = end($temp);

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				$LenderUID = $this->input->post('LenderUID');


				$FileUploadPreview = [];


				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the Required Fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkAssignFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkAssignFormat)) {
					$BulkAssignFormat = $mcustomerproducts->BulkAssignFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->bulkAssignColumnVariables($BulkAssignFormat);


				$objWorksheet = $objPHPExcel->getActiveSheet();
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$validationarray = array();

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} 
					// skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}

				}

				array_unshift($headingsArray, 'Rejection Details');

				$posts = [];

				$loannumbercolumns = []; 

				foreach ($arrayCode as $keys => $a) {

					if (count($a) == $columnvariables['TotalCount']) {

						$additional['RejectionType'] = '';
						$a['ColorCode'] = '';
						$a['BGColorCode'] = '';

						/*LOAN NUMBER VALIDATION*/
						$loanvalidation = $this->Orderentrymodel->CheckOrderExistsforCustomer($a[$columnvariables['LoanNumber']],$CustomerUID);

						if(empty($loanvalidation) || empty($a[$columnvariables['LoanNumber']])) {
							$additional['RejectionType'] = 'Invalid Loan Number';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff04ec';
						}

						/*WORKFLOW VALIDATION*/
						$WorkflowValidation = $this->Orderentrymodel->isWorklflowExistsForCustomer($CustomerUID,$a[$columnvariables['Workflow']]);

						if(empty($WorkflowValidation) || empty($a[$columnvariables['Workflow']])) {
							$additional['RejectionType'] = 'Invalid Workflow';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#e73f3b';
						}

						// IF UnAssign YES
						if (empty($a[$columnvariables['UnAssign']]) || $a[$columnvariables['UnAssign']] != "YES") {

							/*ASSOCIATE VALIDATION*/
							$AssociateValidation = $this->Orderentrymodel->isAssociateExistsForCustoemr($CustomerUID,$a[$columnvariables['Associate']]);

							if(empty($AssociateValidation) || empty($a[$columnvariables['Associate']])) {
								$additional['RejectionType'] = 'Invalid Associate';
								$a['ColorCode'] = '#fff';
								$a['BGColorCode'] = '#4b669e';
							}

						}

					} else {

						$additional['RejectionType'] = 'Invalid Format';
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
					}

					$a = array_merge($additional,$a);
					$arayhgen = [];
					foreach ($a as $key => $value) {
						$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$posts[] = $arayhgen;
					$loannumbercolumns[] = $a[$columnvariables['Loan Number']];
				}

				$response['data'] = $posts;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'results.json', 'w');
					fwrite($fp, json_encode($response));
					fclose($fp);				
				}
				$data['headingsArray'] = $headingsArray;

				$preview = $this->load->view('standard_bulkassign_partialviews/bulk_preview', $data, true);

				$filelink = 'uploads/'.$this->loggedid.'/results.json';

				echo json_encode(array('error' => 0, 'html' => $preview, 'filelink' => $filelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
		}

	}

	function bulkAssignColumnVariables($BulkAssignFormat)
	{

		if ($BulkAssignFormat == "NRZ-Assign") {
			$column["LoanNumber"] = 0; /* tOrders -> LoanNumber*/
			$column["Workflow"] = 1; /* tOrderPropertyRole -> Workflow*/
			$column["Associate"] = 2; /* tOrderImport -> Associate*/
			$column["UnAssign"] = 3; /* tOrders, tOrderMileStone -> UnAssign*/
			$column["TotalCount"] = 4; /*  */
			return $column;
		} elseif ($BulkAssignFormat == "Cooper-Assign") {
			$column["LoanNumber"] = 0; /* tOrders -> LoanNumber*/
			$column["Workflow"] = 1; /* tOrderPropertyRole -> Workflow*/
			$column["Associate"] = 2; /* tOrderImport -> Associate*/
			$column["UnAssign"] = 3; /* tOrders, tOrderMileStone -> UnAssign*/
			$column["TotalCount"] = 4; /*  */
			return $column;
		}
	}

	/**
	*Function PayOff Update 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 11 September 2020.
	*/
	public function PayOffBulkUpdate()
	{
		
		$data['content'] = 'PayOffBulkUpdate';

		$data['Customers'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$data['ProductsDetails'] =$this->Common_Model->ProductsDetails();
		$data['ProjectDetails'] =$this->Common_Model->ProjectDetails();
		$data['OrderPriority'] =$this->Common_Model->get('mOrderPriority',['Active'=>1]);
		$data['SettlementAgent'] =$this->Common_Model->get('mSettlementAgent',['Active'=>1]);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function PayOffBulkUpdateColumnVariables($BulkAssignFormat)
	{

		if ($BulkAssignFormat == "Cooper-PayOffBulkUpdate") {
			$column["LoanNumber"] = 0;
			$column["LoanProcessor"] = 1;
			$column["FundDt"] = 2;
			$column["Column1"] = 3;
			$column["ProcName"] = 4;
			$column["ProcTeam"] = 5;
			$column["SalesTeam"] = 6;
			$column["LOName"] = 7;
			$column["PropertyStateCode"] = 8;
			$column["BorrowerName"] = 9;
			$column["orig_loan_amt"] = 10;
			$column["curr_UPB"] = 11;
			$column["LastPaymentReceivedDate"] = 12;
			$column["SVCD_BORR_NAME"] = 13;
			$column["NXT_DUE_DATE"] = 14;
			$column["MBA_DELQ_STATUS"] = 15;
			$column["Site"] = 16;
			$column["Product Type"] = 17;
			$column["BorrowerEmail"] = 18;
			$column["TotalCount"] = 19;
			return $column;
		} 
	}

	function UpdateBulkPayOff()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames= $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");
			$extension = end($temp);

			// files checking & handling varaiables
			$matching_files = []; $followup_orders = [];

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');

				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the required fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$PayOffBulkUpdateFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->PayOffBulkUpdateFormat)) {
					$PayOffBulkUpdateFormat = $mcustomerproducts->PayOffBulkUpdateFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->PayOffBulkUpdateColumnVariables($PayOffBulkUpdateFormat);


				$objWorksheet = $objPHPExcel->getActiveSheet();
						//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];


				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} // skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}
				}

				$html = '';
				$FailedData = [];
				$SuccessData = [];
				$InsertedOrderUID = [];
				$InsertedOrderUIDs = '';

				$loannumbercolumns = [];

				// Get PayOff Date
				$PayOff_Date = $this->Common_Model->GetPayOffDate($CustomerUID);

				foreach ($arrayCode as $i => $a) {

					if ($PayOffBulkUpdateFormat == "Cooper-PayOffBulkUpdate") {

						$torderimport['LastPaymentReceivedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LastPaymentReceivedDate"]]);
					}

					$a['ColorCode'] = '';
					$a['BGColorCode'] = '';

					if (count($a) == ($columnvariables['TotalCount'] + 2) ) {

						$checkorderexists_byloan = $this->Orderentrymodel->checkorderexistsbyloannumber($a[$columnvariables['LoanNumber']],$CustomerUID,$ProductUID);

						if (!empty($checkorderexists_byloan)) {
							
							foreach ($checkorderexists_byloan as $key => $order) {
								
								if( in_array($a[$columnvariables['LoanNumber']], $loannumbercolumns) ) {

									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#ff04ec';
									array_push($FailedData, $a);

								}  elseif($a[$columnvariables['Site']] != "ISGN") {

									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#ff9800';
									array_push($FailedData, $a);
								}  elseif($this->LastPaymentReceivedDateConditions($a[$columnvariables['LastPaymentReceivedDate']], $PayOff_Date)) {
									// Last Payment Received date should be YesterDay
									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#9575cd';
									array_push($FailedData, $a);
								} else {

									$loannumbercolumns[] = $a[$columnvariables['LoanNumber']];

									foreach ($torderimport as $key => $value) {
										// removes whitespace or other predefined characters from the left side of a string
										$torderimport[$key] = (ltrim($value) == '') ? NULL : ltrim($value);
									}

									$torderimport['OrderUID'] = $order->OrderUID;
									$result = $this->Orderentrymodel->UpdatePayOffBulkOrders($torderimport);

									$a['result'] = $result;

									if ($result == true) {

										$a['OrderNumber'] = $order->OrderNumber;;
										$InsertedOrderUID[] = $order->OrderUID;
										$SuccessData[] = $a;

									} else {
										array_push($FailedData, $arrayCode[$i]);

									}

								}
							}
						}
						else{
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff0013';
							array_push($FailedData, $a);
						}
					} else {

						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
						array_push($FailedData, $a);

					}

				}

				$previewdata['headingsArray'] = $headingsArray;
				$previewdata['InsertedOrderUID'] = implode(',', $InsertedOrderUID);

				$SuccessRows = [];
				foreach ($SuccessData as $key => $sa) {

					$Columns = [];
					$Columns[] = $sa['OrderNumber']; 

					foreach ($columnvariables as $key => $var) {
						if ($key != "TotalCount") {
							$Columns[] = $this->Common_Model->validateConvertDateToFormat($sa[$var], "m/d/Y h:i A");
						}
					}

					$SuccessRows[] = $Columns;
				}

				$SuccessJSON = $SuccessRows;

				$successresponse['data'] = $SuccessJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatesuccessresults.json', 'w');
					fwrite($fp, json_encode($successresponse));
					fclose($fp);				
				}

				$FailedRows = [];
				foreach ($FailedData as $i => $fa) {

					$Columns = [];
					foreach ($fa as $key => $value) {
						$Columns[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$FailedRows[] = $Columns;
				}

				$FailedJSON = $FailedRows;

				$failedresponse['data'] = $FailedJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatefailedresults.json', 'w');
					fwrite($fp, json_encode($failedresponse));
					fclose($fp);				
				}

				$html = $this->load->view('PayOffBulkUpdate_PartialViews/bulk_updated', $previewdata, true);

				$successfilelink = 'uploads/'.$this->loggedid.'/updatesuccessresults.json';
				$failedfilelink = 'uploads/'.$this->loggedid.'/updatefailedresults.json';

				echo json_encode(array('error' => 0, 'html' => $html, 'matching_files'=>$matching_files, 'message'=>'Upload Success', 'successfilelink' => $successfilelink, 'failedfilelink' => $failedfilelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please choose an valid file'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload file'));
		}

	}

	function preview_PayOffBulkUpdate()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames = $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");

			$extension = end($temp);

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				$LenderUID = $this->input->post('LenderUID');

				$FileUploadPreview = [];

				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the Required Fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$PayOffBulkUpdateFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->PayOffBulkUpdateFormat)) {
					$PayOffBulkUpdateFormat = $mcustomerproducts->PayOffBulkUpdateFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->PayOffBulkUpdateColumnVariables($PayOffBulkUpdateFormat);

				$objWorksheet = $objPHPExcel->getActiveSheet();
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();

				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$validationarray = array();

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} 
					// skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}

				}

				array_unshift($headingsArray, 'Rejection Details');

				$posts = [];

				$loannumbercolumns = []; 

				// Get PayOff Date
				$PayOff_Date = $this->Common_Model->GetPayOffDate($CustomerUID);

				foreach ($arrayCode as $keys => $a) {

					if (count($a) == $columnvariables['TotalCount']) {

						$additional['RejectionType'] = '';
						$a['ColorCode'] = '';
						$a['BGColorCode'] = '';

						/*LOAN DUPLICATE VALIDATION*/
						$loanvalidation = $this->Orderentrymodel->is_loanno_exists($a[$columnvariables['LoanNumber']]);

						if(empty($a[$columnvariables['LoanNumber']]) || !$loanvalidation) {

							$additional['RejectionType'] = 'Invalid LoanNumber';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff0013';
						} elseif(in_array($a[$columnvariables['LoanNumber']], $loannumbercolumns)) {

							$additional['RejectionType'] = 'Duplicate LoanNumber';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff04ec';
						} elseif($a[$columnvariables['Site']] != "ISGN") {

							$additional['RejectionType'] = 'Only the ISGN site is allowed';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff9800';
						}  elseif($this->LastPaymentReceivedDateConditions($a[$columnvariables['LastPaymentReceivedDate']], $PayOff_Date)) {
							
							// Last Payment Received date should be YesterDay
							$PayOff_Date_Validation = !empty($PayOff_Date) ? $PayOff_Date : 'YesterDay date';
							$additional['RejectionType'] = 'Last Payment Received date should be '.$PayOff_Date_Validation;
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#9575cd';
						}
						

					} else {
						$additional['RejectionType'] = 'Invalid Format';
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
					}

					$a = array_merge($additional,$a);
					$arayhgen = [];
					foreach ($a as $key => $value) {
						$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$posts[] = $arayhgen;
					$loannumbercolumns[] = $a[$columnvariables['LoanNumber']];
				}

				$response['data'] = $posts;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'results.json', 'w');
					fwrite($fp, json_encode($response));
					fclose($fp);				
				}
				$data['headingsArray'] = $headingsArray;

				$preview = $this->load->view('PayOffBulkUpdate_PartialViews/bulk_preview', $data, true);

				$filelink = 'uploads/'.$this->loggedid.'/results.json';

				echo json_encode(array('error' => 0, 'html' => $preview, 'filelink' => $filelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
		}

	}

	public function LastPaymentReceivedDateConditions($LastPaymentReceivedDate, $PayOff_Date)
	{
		if (!empty($LastPaymentReceivedDate)) {

			if (!empty($PayOff_Date)) {
				
				if (strtotime($PayOff_Date) == strtotime($LastPaymentReceivedDate)) {
					
					return false;
				}
			} else {

				$CurrentTimeStamp = strtotime(date("Y-m-d",time()));

				if (date("l", $CurrentTimeStamp) == "Monday") {
					
					// If today is monday take fri, sat, sun
					$FromTimeStamp = strtotime("-3 day", $CurrentTimeStamp);

					if ($FromTimeStamp <= strtotime($LastPaymentReceivedDate) && $CurrentTimeStamp > strtotime($LastPaymentReceivedDate)) {
						
						return false;
					}

				} elseif (date("l", $CurrentTimeStamp) == "Sunday") {
					
					// If today is Sunday take fri, sat
					$FromTimeStamp = strtotime("-2 day", $CurrentTimeStamp);

					if ($FromTimeStamp <= strtotime($LastPaymentReceivedDate) && $CurrentTimeStamp > strtotime($LastPaymentReceivedDate)) {
						
						return false;
					}
				} elseif (strtotime("-1 day", $CurrentTimeStamp) == strtotime($LastPaymentReceivedDate)) {
					
					// If last payment received date is yester to continue
					return false;
				}
			}

		}
		return true;
		
	}

	/**
	*Function Bulk Workflow Enable 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Tuesday 22 September 2020.
	*/
	public function BulkWorkflowEnable()
	{
		
		$data['content'] = 'BulkWorkflowEnable';

		$data['Customers'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$data['ProductsDetails'] =$this->Common_Model->ProductsDetails();
		$data['ProjectDetails'] =$this->Common_Model->ProjectDetails();
		$data['OrderPriority'] =$this->Common_Model->get('mOrderPriority',['Active'=>1]);
		$data['SettlementAgent'] =$this->Common_Model->get('mSettlementAgent',['Active'=>1]);
		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function BulkWorkflowEnableColumnVariables($BulkWorkflowEnableFormat)
	{

		if ($BulkWorkflowEnableFormat == "Cooper-BulkWorkflowEnable") {
			$column["LoanNumber"] = 0;
			$column["BorrowerName"] = 1;
			$column["Loantype"] = 2;
			$column["PreferredClosingDate"] = 3;
			$column["OnemonthPayment"] = 4;
			$column["ZeroSTC"] = 5;
			$column["Amount"] = 6;
			$column["TotalCount"] = 7;
			return $column;
		} 
	}

	function UpdateBulkEnableWorkflow()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames= $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");
			$extension = end($temp);

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '' || $WorkflowModuleUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the required fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkWorkflowEnableFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkWorkflowEnableFormat)) {
					$BulkWorkflowEnableFormat = $mcustomerproducts->BulkWorkflowEnableFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->BulkWorkflowEnableColumnVariables($BulkWorkflowEnableFormat);


				$objWorksheet = $objPHPExcel->getActiveSheet();
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} // skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}
				}

				$html = '';
				$FailedData = [];
				$SuccessData = [];
				$InsertedOrderUID = [];
				$InsertedOrderUIDs = '';

				$loannumbercolumns = [];

				foreach ($arrayCode as $i => $a) {

					if ($BulkWorkflowEnableFormat == "Cooper-BulkWorkflowEnable") {

						$tOrderImport['ProcessorChosenClosingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["PreferredClosingDate"]]);
						$tOrderImport['STCAmount'] = NULL;

						if (strtolower($a[$columnvariables["OnemonthPayment"]]) == 'yes' && !empty($a[$columnvariables["PreferredClosingDate"]])) {
							
							$tOrderImport['STC'] = 'OneMonthPayment';
						} elseif (strtolower($a[$columnvariables["ZeroSTC"]]) == 'yes' && !empty($a[$columnvariables["PreferredClosingDate"]])) {
							
							$tOrderImport['STC'] = 'ZeroSTC';
						} elseif (!empty($a[$columnvariables["Amount"]]) && !empty($a[$columnvariables["PreferredClosingDate"]])) {
							
							$tOrderImport['STC'] = 'Amount';
							$tOrderImport['STCAmount'] = $a[$columnvariables["Amount"]];
						}
						// echo '<pre>';print_r($tOrderImport);exit;


					}

					$a['ColorCode'] = '';
					$a['BGColorCode'] = '';

					if (count($a) == ($columnvariables['TotalCount'] + 2) ) {

						$checkorderexists_byloan = $this->Orderentrymodel->checkorderexistsbyloannumber($a[$columnvariables['LoanNumber']],$CustomerUID,$ProductUID);

						if (!empty($checkorderexists_byloan)) {
							
							foreach ($checkorderexists_byloan as $key => $order) {
								
								if( in_array($a[$columnvariables['LoanNumber']], $loannumbercolumns) ) {

									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#ff04ec';
									array_push($FailedData, $a);

								} elseif (empty($this->Common_Model->Is_ClientWorkflowavailable($CustomerUID, $WorkflowModuleUID))) {
									
									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#795548';
									array_push($FailedData, $a);
								} /* elseif ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->check_forcequeueenabled($order->OrderUID, $CustomerUID, $WorkflowModuleUID)) {
									
									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#ff9800';
									array_push($FailedData, $a);
								} */ elseif ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->WorkupEnableValidation($tOrderImport)) {

									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#607d8b';
									array_push($FailedData, $a);
								} elseif (!empty($a[$columnvariables['BorrowerName']]) && $this->Orderentrymodel->CheckBorrowerExistOrder($order->OrderUID,$a[$columnvariables['BorrowerName']])) {
									
									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#9575cd';
									array_push($FailedData, $a);
								} elseif (!empty($a[$columnvariables['Loantype']]) && $this->Orderentrymodel->CheckLoantypeExistOrder($order->OrderUID,$a[$columnvariables['Loantype']])) {
									
									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#2196f3';
									array_push($FailedData, $a);
								} else {

									$data['$CustomerUID'] = $order->$CustomerUID;
									$data['OrderUID'] = $order->OrderUID;
									$data['WorkflowModuleUID'] = $WorkflowModuleUID;
									$tOrderImport['OrderUID'] = $order->OrderUID;
									$result = $this->Orderentrymodel->EnableWorkflow($data, $tOrderImport);

									$a['result'] = $result;

									if ($result == true) {

										$a['OrderNumber'] = $order->OrderNumber;;
										$InsertedOrderUID[] = $order->OrderUID;
										$SuccessData[] = $a;

									} else {
										array_push($FailedData, $arrayCode[$i]);

									}

								}
							}
						}
						else{
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff0013';
							array_push($FailedData, $a);
						}
					} else {

						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
						array_push($FailedData, $a);

					}					

					$loannumbercolumns[] = $a[$columnvariables['LoanNumber']];
				}

				$previewdata['headingsArray'] = $headingsArray;
				$previewdata['InsertedOrderUID'] = implode(',', $InsertedOrderUID);

				$SuccessRows = [];
				foreach ($SuccessData as $key => $sa) {

					$Columns = [];
					$Columns[] = $sa['OrderNumber']; 

					foreach ($columnvariables as $key => $var) {
						if ($key != "TotalCount") {
							$Columns[] = $this->Common_Model->validateConvertDateToFormat($sa[$var], "m/d/Y h:i A");
						}
					}

					$SuccessRows[] = $Columns;
				}

				$SuccessJSON = $SuccessRows;

				$successresponse['data'] = $SuccessJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatesuccessresults.json', 'w');
					fwrite($fp, json_encode($successresponse));
					fclose($fp);				
				}

				$FailedRows = [];
				foreach ($FailedData as $i => $fa) {

					$Columns = [];
					foreach ($fa as $key => $value) {
						$Columns[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$FailedRows[] = $Columns;
				}

				$FailedJSON = $FailedRows;

				$failedresponse['data'] = $FailedJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatefailedresults.json', 'w');
					fwrite($fp, json_encode($failedresponse));
					fclose($fp);				
				}

				$html = $this->load->view('BulkWorkflowEnable_PartialViews/bulk_updated', $previewdata, true);

				$successfilelink = 'uploads/'.$this->loggedid.'/updatesuccessresults.json';
				$failedfilelink = 'uploads/'.$this->loggedid.'/updatefailedresults.json';

				echo json_encode(array('error' => 0, 'html' => $html, 'message'=>'Upload Success', 'successfilelink' => $successfilelink, 'failedfilelink' => $failedfilelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please choose an valid file'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload file'));
		}

	}

	/**
	*Function Workup enable validaiton 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 05 October 2020.
	*/
	function WorkupEnableValidation($tOrderImport) {

		if (empty($tOrderImport['ProcessorChosenClosingDate']) || empty($tOrderImport['STC'])) {
			return false;
		}

		if ($tOrderImport['STC'] == 'Amount' && empty($tOrderImport['STCAmount'])) {
			return false;
		}

		return true;
	}

	function preview_BulkWorkflowEnable()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames = $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");

			$extension = end($temp);

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				$LenderUID = $this->input->post('LenderUID');
				$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

				$FileUploadPreview = [];

				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '' || $WorkflowModuleUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the Required Fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$BulkWorkflowEnableFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->BulkWorkflowEnableFormat)) {
					$BulkWorkflowEnableFormat = $mcustomerproducts->BulkWorkflowEnableFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->BulkWorkflowEnableColumnVariables($BulkWorkflowEnableFormat);

				$objWorksheet = $objPHPExcel->getActiveSheet();
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();

				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/

				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$validationarray = array();

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} 
					// skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}

				}

				array_unshift($headingsArray, 'Rejection Details');

				$posts = [];

				$loannumbercolumns = []; 

				foreach ($arrayCode as $keys => $a) {

					if ($BulkWorkflowEnableFormat == "Cooper-BulkWorkflowEnable") {

						$tOrderImport['ProcessorChosenClosingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["PreferredClosingDate"]]);
						$tOrderImport['STCAmount'] = NULL;

						if (strtolower($a[$columnvariables["OnemonthPayment"]]) == 'yes' && !empty($a[$columnvariables["PreferredClosingDate"]])) {
							
							$tOrderImport['STC'] = 'OneMonthPayment';
						} elseif (strtolower($a[$columnvariables["ZeroSTC"]]) == 'yes' && !empty($a[$columnvariables["PreferredClosingDate"]])) {
							
							$tOrderImport['STC'] = 'ZeroSTC';
						} elseif (!empty($a[$columnvariables["Amount"]]) && !empty($a[$columnvariables["PreferredClosingDate"]])) {
							
							$tOrderImport['STC'] = 'Amount';
							$tOrderImport['STCAmount'] = $a[$columnvariables["Amount"]];
						}
						// echo '<pre>';print_r($tOrderImport);exit;

					}

					if (count($a) == $columnvariables['TotalCount']) {

						$additional['RejectionType'] = '';
						$a['ColorCode'] = '';
						$a['BGColorCode'] = '';

						/*LOAN DUPLICATE VALIDATION*/
						$loanvalidation = $this->Orderentrymodel->is_loanno_exists($a[$columnvariables['LoanNumber']]);

						$order = $this->Common_Model->get_row('tOrders', ['LoanNumber'=>$a[$columnvariables['LoanNumber']]]);

						if(empty($a[$columnvariables['LoanNumber']]) || !$loanvalidation) {

							$additional['RejectionType'] = 'Invalid LoanNumber';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff0013';
						} elseif(in_array($a[$columnvariables['LoanNumber']], $loannumbercolumns)) {

							$additional['RejectionType'] = 'Duplicate LoanNumber';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff04ec';

						} elseif (empty($this->Common_Model->Is_ClientWorkflowavailable($CustomerUID, $WorkflowModuleUID))) {

							$additional['RejectionType'] = 'Workflow is not available';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#795548';
						} /* elseif ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->check_forcequeueenabled($order->OrderUID, $CustomerUID, $WorkflowModuleUID)) {

							$additional['RejectionType'] = 'Workflow is already enabled';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff9800';
						} */ elseif (!empty($a[$columnvariables['BorrowerName']]) && $this->Orderentrymodel->CheckBorrowerExistOrder($order->OrderUID,$a[$columnvariables['BorrowerName']])) {

							$additional['RejectionType'] = 'Borrower is not matched';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#9575cd';
						} elseif (!empty($a[$columnvariables['Loantype']]) && $this->Orderentrymodel->CheckLoantypeExistOrder($order->OrderUID,$a[$columnvariables['Loantype']])) {

							$additional['RejectionType'] = 'LoanType is not matched';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#2196f3';
						}  elseif ($WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->WorkupEnableValidation($tOrderImport)) {

							$additional['RejectionType'] = 'Workup Enable Error';
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#607d8b';
						}						

					} else {
						$additional['RejectionType'] = 'Invalid Format';
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
					}

					$a = array_merge($additional,$a);
					$arayhgen = [];
					foreach ($a as $key => $value) {
						$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$posts[] = $arayhgen;
					$loannumbercolumns[] = $a[$columnvariables['LoanNumber']];
				}

				$response['data'] = $posts;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'results.json', 'w');
					fwrite($fp, json_encode($response));
					fclose($fp);				
				}
				$data['headingsArray'] = $headingsArray;

				$preview = $this->load->view('BulkWorkflowEnable_PartialViews/bulk_preview', $data, true);

				$filelink = 'uploads/'.$this->loggedid.'/results.json';

				echo json_encode(array('error' => 0, 'html' => $preview, 'filelink' => $filelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
		}

	}

	/**
	*Function DocsOut 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 29 October 2020.
	*/
	function DocsOut()
	{
		$data['content'] = 'DocsOut';

		$data['Customers'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$data['ProductsDetails'] =$this->Common_Model->ProductsDetails();
		$data['ProjectDetails'] =$this->Common_Model->ProjectDetails();
		$data['OrderPriority'] =$this->Common_Model->get('mOrderPriority',['Active'=>1]);
		$data['SettlementAgent'] =$this->Common_Model->get('mSettlementAgent',['Active'=>1]);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/**
	*Function DocsOut Bulk Update Preview 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 30 October 2020.
	*/
	function Preview_DocsOutUpdate()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames = $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");

			$extension = end($temp);

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');
				$LenderUID = $this->input->post('LenderUID');

				$FileUploadPreview = [];


				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the Required Fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$DocsOutBulkUpdateFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->DocsOutBulkUpdateFormat)) {
					$DocsOutBulkUpdateFormat = $mcustomerproducts->DocsOutBulkUpdateFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->docsout_column_variables($DocsOutBulkUpdateFormat);

				$objWorksheet = $objPHPExcel->getActiveSheet();
				
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();

				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/
				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$validationarray = array();

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} 
					
					// skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}
				}

				$posts = [];

				$loannumbercolumns = []; 

				foreach ($arrayCode as $keys => $a) {

					if (count($a) == $columnvariables['TotalCount']) {

						$checkorderexists_byloan = $this->Orderentrymodel->checkorderexistsbyloannumber($a[$columnvariables['LoanNumber']],$CustomerUID,$ProductUID);

						if (!empty($checkorderexists_byloan)) {

							/*LOAN DUPLICATE VALIDATION*/
							if(in_array($a[$columnvariables['LoanNumber']], $loannumbercolumns) ) {
								$a['ColorCode'] = '#fff';
								$a['BGColorCode'] = '#ff04ec';

							}
							else{
								$loannumbercolumns[] = $a[$columnvariables['LoanNumber']];
								$a['ColorCode'] = '';
								$a['BGColorCode'] = '';

							}
						}
						else{
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff0013';
						}

					} else {
						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
					}

					$arayhgen = [];
					foreach ($a as $key => $value) {
						$arayhgen[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$posts[] = $arayhgen;
				}

				$response['data'] = $posts;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updateresults.json', 'w');
					fwrite($fp, json_encode($response));
					fclose($fp);				
				}

				$data['headingsArray'] = $headingsArray;

				$preview = $this->load->view('docsoutupdate_bulk_partialviews/bulk_update_preview', $data, true);

				$filelink = 'uploads/'.$this->loggedid.'/updateresults.json';

				echo json_encode(array('error' => 0, 'html' => $preview, 'filelink' => $filelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
		}

	}

	/**
	*Function Docsout bulk update 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 30 October 2020.
	*/
	function docsoutupdate_bulk()
	{

		if (isset($_FILES['file'])) {
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];
			$filenames= $this->input->post('FILENAMES');
			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx", "xls","csv");
			$extension = end($temp);

			// files checking & handling varaiables
			$matching_files = []; $followup_orders = [];

			if (in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(false);
					$objPHPExcel = $objReader->load($inputFile);

				} catch (Exception $e) {

					$msg = 'Error Uploading file';
					echo json_encode(array('error' => '1', 'message' => $msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$ProjectUID = $this->input->post('ProjectUID');

				if ($CustomerUID == '' || $ProjectUID == '' || $ProductUID == '') {
					echo json_encode(array('error' => '1', 'message' => 'Select the required fields'));
					exit;
				}

				$this->setImportSessionDetails($CustomerUID, $ProductUID, $ProjectUID);

				$mcustomerproducts = $this->Common_Model->get_row('mCustomerProducts', ["CustomerUID"=>$CustomerUID, "ProductUID"=>$ProductUID]);
				$DocsOutBulkUpdateFormat = "";
				if (!empty($mcustomerproducts) && !empty($mcustomerproducts->DocsOutBulkUpdateFormat)) {
					$DocsOutBulkUpdateFormat = $mcustomerproducts->DocsOutBulkUpdateFormat;
				}

				/*declare excel values*/
				$columnvariables = $this->docsout_column_variables($DocsOutBulkUpdateFormat);

				$objWorksheet = $objPHPExcel->getActiveSheet();
				
				//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();

				$headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, false, false, true);
				$headingsArray = $headingsArray[1];

				/*get Customer name and product name*/
				$LenderName = $ProductName = $CustomerName  = $CustomerCode = $ProjectName = $ProjectCode = false;

				if($CustomerUID != ''){
					$Customerrow = $this->Orderentrymodel->get_customerbyuid($CustomerUID);
					if(!empty($Customerrow)){
						$CustomerName =  $Customerrow->CustomerName;
						$CustomerCode =  $Customerrow->CustomerCode;
					}
				}

				if($ProductUID != ''){
					$Productrow = $this->Orderentrymodel->get_productbyuid($ProductUID);
					if(!empty($Productrow)){
						$ProductName =  $Productrow->ProductName;
					}
				}

				if($ProjectUID != ''){
					$Projectrow = $this->Orderentrymodel->get_projectbyuid($ProjectUID);
					if(!empty($Projectrow)){
						$ProjectName =  $Projectrow->ProjectName;
						$ProjectCode =  $Projectrow->ProjectCode;
					}
				}

				$arrayCode = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
					if ($this->isEmptyRow(reset($dataRow))) {
						continue;
					} // skip empty row
					++$r;

					$i = 0;
					foreach ($headingsArray as $columnKey => $columnHeading) {
						$cellformat = $objWorksheet->getCell($columnKey . $row);
						if(PHPExcel_Shared_Date::isDateTime($cellformat)) {

							$arrayCode[$r][$i] = trim($cellformat->getFormattedValue());

						}else{
							$arrayCode[$r][$i] = trim($cellformat->getValue());
						}
						$i++;
					}
				}

				$html = '';
				$FailedData = [];
				$SuccessData = [];
				$InsertedOrderUID = [];
				$InsertedOrderUIDs = '';

				$loannumbercolumns = []; 
				$propertyroledata = []; 

				foreach ($arrayCode as $i => $a) {
					
					$data = []; 

					/*LOAN DUPLICATE VALIDATION*/

					//for missing fields
					$data['CustomerUID'] = !empty($CustomerUID) ? $CustomerUID : null;
					$data['ProductUID'] = !empty($ProductUID) ? $ProductUID : null;
					$data['ProjectUID'] =  !empty($ProjectUID) ? $ProjectUID : null;

					if ($DocsOutBulkUpdateFormat == "Cooper-DocsOut-Update") {

						$mMilestone = $this->Common_Model->get_row('mMilestone', ['MilestoneName'=>trim(ltrim($a[$columnvariables['Milestone']], "0"))]);

						if (!empty($mMilestone)) {
							$data['MilestoneUID'] = $mMilestone->MilestoneUID;
						}

						$data['LoanNumber'] = $a[$columnvariables['LoanNumber']];
						$data['LoanType'] = $a[$columnvariables['LoanType']];
						$data['PropertyStateCode'] = $a[$columnvariables['PropertyStateCode']];

						$propertyroledata = [];
						if($a[$columnvariables['BorrowerFirstName']] ) 
						{		
							$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['BorrowerFirstName']];

						}

						$torderimport['LoanProcessor'] = $a[$columnvariables["LoanProcessor"]];
						$torderimport['DocsOutSigningDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["DocsOutSigningDate"]]);
						$torderimport['DocsOutSigningTime'] = $a[$columnvariables["DocsOutSigningTime"]];
						$torderimport['Queue'] = $a[$columnvariables["Queue"]];
						$torderimport['QueueDateTime'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["QueueDateTime"]]);
						$torderimport['LockExpiration'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LockExpiration"]]);
						$torderimport['ClosedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ClosedDate"]]);
						$torderimport['SigningStatusDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SigningStatusDate"]]);
						$torderimport['EarliestClosingDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["EarliestClosingDate"]]);
						$torderimport['DocsOutDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["DocsOutDate"]]);
						$torderimport['FundingFunderName'] = $a[$columnvariables["FundingFunderName"]];
						$torderimport['SubStatusLastChangedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["SubStatusLastChangedDate"]]);
						$torderimport['SigningLocation'] = $a[$columnvariables["SigningLocation"]];
						$torderimport['ProposedTotalHousingExpense'] = $a[$columnvariables["ProposedTotalHousingExpense"]];
						$torderimport['CashFromBorrower'] = $a[$columnvariables["CashFromBorrower"]];
						$torderimport['Assets'] = $a[$columnvariables["Assets"]];
						$torderimport['Credit3Amt'] = $a[$columnvariables["Credit3Amt"]];
						$torderimport['Credit4Amt'] = $a[$columnvariables["Credit4Amt"]];
						$torderimport['ExceptionAmount'] = $a[$columnvariables["ExceptionAmount"]];
						$torderimport['Status'] = $a[$columnvariables["Status"]];
						$torderimport['SubStatus'] = $a[$columnvariables["SubStatus"]];
						$torderimport['OccupancyPSI'] = $a[$columnvariables["Occupancy"]];
						$torderimport['TitleInsuranceCompanyName'] = $a[$columnvariables["TitleInsuranceCompanyName"]];
						$torderimport['DocsOutClosingDisclosureSendDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["DocsOutClosingDisclosureSendDate"]]);
						$torderimport['UnderwritingApprovalDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["UnderwritingApprovalDate"]]);
						$torderimport['AlertRedisclosureRequired'] = $a[$columnvariables["AlertRedisclosureRequired"]];
						$torderimport['CountSubmittedforDocCheckQueueresubmissions'] = $a[$columnvariables["CountSubmittedforDocCheckQueueresubmissions"]];
						$torderimport['CDCallOutDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["CDCallOutDate"]]);
						$torderimport['ApprovalMilestoneCount'] = $a[$columnvariables["ApprovalMilestoneCount"]];
						$torderimport['NSMServicingLoanNumber'] = $a[$columnvariables["NSMServicingLoanNumber"]];
						$torderimport['ApprovedMilestoneDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["ApprovedMilestoneDate"]]);
						$torderimport['LastFinishedMilestone'] = $a[$columnvariables["LastFinishedMilestone"]];
						$torderimport['NextPaymentDue'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["NextPaymentDue"]]);
						$torderimport['LastPaymentReceivedDate'] = $this->Common_Model->validateConvertDateToFormat($a[$columnvariables["LastPaymentReceivedDate"]]);

					} else {

						$data['LoanNumber'] = $a[$columnvariables['Loan Number']];
						$data['LoanAmount'] = $a[$columnvariables['Loan Amount']];
						$data['LoanType'] = $a[$columnvariables['Loan Type']];
						$data['CustomerReferenceNumber'] = $a[$columnvariables['Customer Reference Number']];
						$data['PropertyAddress1'] = $a[$columnvariables['Property Address']];
						$data['PropertyCityName'] = $a[$columnvariables['Property City']];
						$data['PropertyCountyName'] = $a[$columnvariables['Property County']];
						$data['PropertyStateCode'] = $a[$columnvariables['Property State']];
						$data['PropertyZipCode'] = $a[$columnvariables['Property Zip Code']];
						$data['APN'] = $a[$columnvariables['APN']];

						for ($i = 1; $i <= 5; $i++){
							if($a[$columnvariables['Borrower Name '.$i]] || $a[$columnvariables['Email '.$i]] || $a[$columnvariables['Home Number '.$i]] || $a[$columnvariables['Work Number '.$i]] || $a[$columnvariables['Cell Number '.$i]] || $a[$columnvariables['Social '.$i]] ) 
							{		
								$propertyroledata[$i]['BorrowerFirstName'] = $a[$columnvariables['Borrower Name '.$i]];
								$propertyroledata[$i]['BorrowerMailingAddress1'] = $a[$columnvariables['Email '.$i]];
								$propertyroledata[$i]['HomeNumber'] = $a[$columnvariables['Home Number '.$i]];
								$propertyroledata[$i]['WorkNumber'] = $a[$columnvariables['Work Number '.$i]];
								$propertyroledata[$i]['CellNumber'] = $a[$columnvariables['Cell Number '.$i]];
								$propertyroledata[$i]['Social'] = $a[$columnvariables['Social '.$i]];
							}
						}

					}

					$a['ColorCode'] = '';
					$a['BGColorCode'] = '';

					if (count($a) == ($columnvariables['TotalCount'] + 2) ) {

						$checkorderexists_byloan = $this->Orderentrymodel->checkorderexistsbyloannumber($a[$columnvariables['LoanNumber']],$CustomerUID,$ProductUID);

						if (!empty($checkorderexists_byloan)) {
							
							foreach ($checkorderexists_byloan as $key => $order) {
								
								if( in_array($a[$columnvariables['LoanNumber']], $loannumbercolumns) ) {

									$a['ColorCode'] = '#fff';
									$a['BGColorCode'] = '#ff04ec';
									array_push($FailedData, $a);

								}  else {

									$loannumbercolumns[] = $a[$columnvariables['LoanNumber']];

									$data['OrderUID'] = $order->OrderUID;
									$data['OrderNumber'] = $order->OrderNumber;

									foreach ($torderimport as $key => $value) {
										// removes whitespace or other predefined characters from the left side of a string
										$torderimport[$key] = (ltrim($value) == '') ? NULL : ltrim($value);
									}

									$torderimport['OrderUID'] = $order->OrderUID;
									$result = $this->Orderentrymodel->updatedocsout_order($data,$propertyroledata, $torderimport);

									$a['result'] = $result;

									if ($result == true) {

										$a['OrderNumber'] = $order->OrderNumber;;
										$InsertedOrderUID[] = $data['OrderUID'];
										$SuccessData[] = $a;

									} else {
										array_push($FailedData, $arrayCode[$i]);

									}

								}
							}
						}
						else{
							$a['ColorCode'] = '#fff';
							$a['BGColorCode'] = '#ff0013';
							array_push($FailedData, $a);
						}
					} else {

						$a['ColorCode'] = '#fff';
						$a['BGColorCode'] = '#757575';
						array_push($FailedData, $a);

					}

				}

				$previewdata['headingsArray'] = $headingsArray;
				$previewdata['InsertedOrderUID'] = implode(',', $InsertedOrderUID);

				$SuccessRows = [];
				foreach ($SuccessData as $key => $sa) {

					$Columns = [];
					$Columns[] = $sa['OrderNumber']; 

					foreach ($columnvariables as $key => $var) {
						if ($key != "TotalCount") {
							$Columns[] = $this->Common_Model->validateConvertDateToFormat($sa[$var], "m/d/Y h:i A");
						}
					}

					$SuccessRows[] = $Columns;
				}

				$SuccessJSON = $SuccessRows;

				$successresponse['data'] = $SuccessJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatesuccessresults.json', 'w');
					fwrite($fp, json_encode($successresponse));
					fclose($fp);				
				}

				$FailedRows = [];
				foreach ($FailedData as $i => $fa) {

					$Columns = [];
					foreach ($fa as $key => $value) {
						$Columns[] = $this->Common_Model->validateConvertDateToFormat($value, "m/d/Y h:i A");
					}

					$FailedRows[] = $Columns;
				}

				$FailedJSON = $FailedRows;

				$failedresponse['data'] = $FailedJSON;
				$PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
				if($this->Common_Model->CreateDirectoryToPath($PATH)){
					$fp = fopen($PATH . 'updatefailedresults.json', 'w');
					fwrite($fp, json_encode($failedresponse));
					fclose($fp);				
				}

				$html = $this->load->view('docsoutupdate_bulk_partialviews/bulk_updated', $previewdata, true);

				$successfilelink = 'uploads/'.$this->loggedid.'/updatesuccessresults.json';
				$failedfilelink = 'uploads/'.$this->loggedid.'/updatefailedresults.json';

				echo json_encode(array('error' => 0, 'html' => $html, 'matching_files'=>$matching_files, 'message'=>'Upload Success', 'successfilelink' => $successfilelink, 'failedfilelink' => $failedfilelink)); exit;

			} else {
				echo json_encode(array('error' => '1', 'message' => 'Please choose an valid file'));
			}

		} else {
			echo json_encode(array('error' => '1', 'message' => 'Please upload file'));
		}

	}

	/**
	*Function Docsout column varaiables 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Friday 30 October 2020.
	*/
	function docsout_column_variables($DocsOutBulkUpdateFormat = "")
	{

		if ($DocsOutBulkUpdateFormat == "Cooper-DocsOut-Update") {
			$column["LoanNumber"] = 0; //tOrders->LoanNumber
			$column["BorrowerFirstName"] = 1; //tOrderPropertyRole->BorrowerFirstName
			$column["LoanProcessor"] = 2; //tOrderImport->LoanProcessor
			$column["Milestone"] = 3; //tOrders->Milestone
			$column["PropertyStateCode"] = 4; //tOrders->PropertyStateCode
			$column["LoanType"] = 5; //tOrders->LoanType
			$column["DocsOutSigningDate"] = 6; //tOrderImport->DocsOutSigningDate
			$column["DocsOutSigningTime"] = 7; //tOrderImport->DocsOutSigningTime
			$column["Queue"] = 8; //tOrderImport->Queue
			$column["QueueDateTime"] = 9; //tOrderImport->QueueDateTime
			$column["LockExpiration"] = 10; //tOrderImport->LockExpiration
			$column["ClosedDate"] = 11; //tOrderImport->ClosedDate
			$column["SigningStatusDate"] = 12; //tOrderImport->SigningStatusDate
			$column["EarliestClosingDate"] = 13; //tOrderImport->EarliestClosingDate
			$column["DocsOutDate"] = 14; //tOrderImport->DocsOutDate
			$column["FundingFunderName"] = 15; //tOrderImport->FundingFunderName
			$column["SubStatusLastChangedDate"] = 16; //tOrderImport->SubStatusLastChangedDate
			$column["SigningLocation"] = 17; //tOrderImport->SigningLocation
			$column["ProposedTotalHousingExpense"] = 18; //tOrderImport->ProposedTotalHousingExpense
			$column["CashFromBorrower"] = 19; //tOrderImport->CashFromBorrower
			$column["Assets"] = 20; //tOrderImport->Assets
			$column["Credit3Amt"] = 21; //tOrderImport->Credit3Amt
			$column["Credit4Amt"] = 22; //tOrderImport->Credit4Amt
			$column["ExceptionAmount"] = 23; //tOrderImport->ExceptionAmount
			$column["Status"] = 24; //tOrderImport->Status
			$column["SubStatus"] = 25; //tOrderImport->SubStatus
			$column["OccupancyPSI"] = 26; //tOrderImport->Occupancy
			$column["TitleInsuranceCompanyName"] = 27; //tOrderImport->TitleInsuranceCompanyName
			$column["DocsOutClosingDisclosureSendDate"] = 28; //tOrderImport->DocsOutClosingDisclosureSendDate
			$column["UnderwritingApprovalDate"] = 29; //tOrderImport->UnderwritingApprovalDate
			$column["AlertRedisclosureRequired"] = 30; //tOrderImport->AlertRedisclosureRequired
			$column["CountSubmittedforDocCheckQueueresubmissions"] = 31; //tOrderImport->CountSubmittedforDocCheckQueueresubmissions
			$column["CDCallOutDate"] = 32; //tOrderImport->CDCallOutDate
			$column["ApprovalMilestoneCount"] = 33; //tOrderImport->ApprovalMilestoneCount
			$column["NSMServicingLoanNumber"] = 34; //tOrderImport->NSMServicingLoanNumber
			$column["ApprovedMilestoneDate"] = 35; //tOrderImport->ApprovedMilestoneDate
			$column["LastFinishedMilestone"] = 36; //tOrderImport->LastFinishedMilestone
			$column["NextPaymentDue"] = 37; //tOrderImport->NextPaymentDue
			$column["LastPaymentReceivedDate"] = 38; //tOrderImport->LastPaymentReceivedDate
			$column["TotalCount"] = 39; /*  */
			return $column;
		} else {
			return array ('Loan Number'=>0,'Loan Amount'=>1,'Loan Type'=>2,'Customer Reference Number'=>3,'Property Address'=>4,'Property City'=>5,'Property County'=>6,'Property State'=>7,'Property Zip Code'=>8,'APN'=>9,'Borrower Name 1'=>10,'Email 1'=>11,'Home Number 1'=>12,'Work Number 1'=>13,'Cell Number 1'=>14,'Social 1'=>15,'Borrower Name 2'=>16,'Email 2'=>17,'Home Number 2'=>18,'Work Number 2'=>19,'Cell Number 2'=>20,'Social 2'=>21,'Borrower Name 3'=>22,'Email 3'=>23,'Home Number 3'=>24,'Work Number 3'=>25,'Cell Number 3'=>26,'Social 3'=>27,'Borrower Name 4'=>28,'Email 4'=>29,'Home Number 4'=>30,'Work Number 4'=>31,'Cell Number 4'=>32,'Social 4'=>33,'Borrower Name 5'=>34,'Email 5'=>35,'Home Number 5'=>36,'Work Number 5'=>37,'Cell Number 5'=>38,'Social 5'=>39,'TotalCount'=>40);

		}
	}

}
?>
