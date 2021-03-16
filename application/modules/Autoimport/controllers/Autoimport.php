<?php
class Autoimport extends MX_Controller
{
	public $pfilename;
	private $_lock;

	function __construct()
	{
		$this->pfilename = '/tmp/import.lock';
		$this->load->library('zip');
		$this->load->helper('file');
		$this->load->helper("url");
		$this->load->model("Common_Model");
		//$this->load->library('S3');
	}


	function HOI_import_Orders($CustomerUID,$ProjectUID)
	{
		$parkingenabled = $this->Common_Model->is_parking_enabledforworkflow($this->config->item('Workflows')['HOI']);

		$this->db->select("tOrders.*");


		/*^^^^^ Get MyOrders Query ^^^^^*/
		$this->Common_Model->GetHOIQueue();

		//filter order when parking disabled
		if(empty($parkingenabled)) {
			$this->db->where("NOT EXISTS (SELECT 1 FROM tOrderParking WHERE tOrderParking.OrderUID = tOrders.OrderUID AND tOrderParking.WorkflowModuleUID='".$this->config->item('Workflows')['HOI']."' AND tOrderParking.IsCleared = 0 )",NULL,FALSE);
		}

		$this->db->where('tOrders.CustomerUID',$CustomerUID);
		$this->db->where('tOrders.ProjectUID',$ProjectUID);
		$this->db->where('(tOrders.AutoExportStatus <> 2 OR tOrders.AutoExportStatus IS NULL)',NULL,FALSE);
		$this->db->limit(10);
		$query = $this->db->get()->result();
		return $query;
	}


	public function importPDFLoans()
	{
		include(APPPATH.'modules/SFTP/controllers/ConnectSFTP.php'); 
		$exp =  new ConnectSFTP();
		$this->load->library('ftp');
		echo "<pre>";
		$mAutoImport = $this->Common_Model->getNormalAutoImport(); 
		if(empty($mAutoImport)){
			echo '<pre> No Project Customers';
		}
		foreach ($mAutoImport as $key => $value) 
		{

			$fileList = array();
			$config['hostname'] =  $value->SFTPHost;
			$config['username'] =  $value->SFTPUser;
			$config['password'] =  $value->SFTPPassword;
			$config['debug']    = FALSE;
			$SFTPPath = $value->SFTPPath;
			$port = $value->SFTPPort;
			$protocol = $value->SFTPProtocol;
			$list = array();
			if($protocol == 'FTP')
			{
				if(empty($SFTPPath) || !$this->ftp->connect($config))
				{
					continue;
				}
				else
				{
					$list = $this->ftp->list_files($SFTPPath);

				}
			}
			if($protocol == 'SFTP')
			{
				if(!$exp->connect($config))
				{
					continue;
				}
				else
				{
					$list = $exp->list_files($SFTPPath);
				}
			}
			if(empty($list)) {
				exit('No Files in SFTP');
			}
			$waitingLoans = $this->HOI_import_Orders($value->CustomerUID,$value->ProjectUID);
			$selectedColumnName = $value->ColumnName;
			$count = 0;
			$limit = $value->ImportLimit;
			
			foreach ($waitingLoans as $loankey => $loanvalue) {
				echo "pickup OrderUID : ".$loanvalue->OrderUID." OrderNumber : ".$loanvalue->OrderNumber."  LoanNumber - ".$loanvalue->LoanNumber."<hr>";
				foreach ($list as $listkey => $listvalue) {
					if($listvalue == $loanvalue->{$selectedColumnName}.'.pdf')
					{
						$count++;
						$data = array(
							'AutoExportStatus'	=>	1
						);
						$update = $this->Common_Model->updateOrderStatus($loanvalue->OrderUID,$data);
						$RemoteFilePath = $SFTPPath.$listvalue;
						$DocumentPath = FCPATH . 'uploads/OrderDocumentPath/'.$loanvalue->OrderNumber . '/';
						$completePath = 'uploads/OrderDocumentPath/'.$loanvalue->OrderNumber . '/'.$loanvalue->LoanNumber.'.pdf';

						$this->Common_Model->CreateDirectoryToPath($DocumentPath);
						if($protocol == 'FTP')
						{
							if($this->ftp->download($RemoteFilePath, $DocumentPath))
								$download = true;
						}
						if($protocol == 'SFTP')
						{
							$this->uploadPDF($value->SFTPHost,$value->SFTPUser,$value->SFTPPassword,$SFTPPath,$listvalue,$DocumentPath,$loanvalue->LoanNumber);
						}
						$data = array(
							'DocumentName'		=>	$loanvalue->LoanNumber.'.pdf',
							'DocumentURL'		=>	$completePath,
							'OrderUID'			=>	$loanvalue->OrderUID,
							'IsStacking'		=>	'1',
							'TypeofDocument'	=>	'Others',
							'UploadedDateTime'	=>	date('Y-m-d H:i:s')
						);
						$this->Common_Model->save('tDocuments',$data);
						$data = array(
							'AutoExportStatus'	=>	2,
							'IsOCREnabled'	=>	1,
						);

						$update = $this->Common_Model->updateOrderStatus($loanvalue->OrderUID,$data);
						echo $loanvalue->LoanNumber.".pdf attached to OrderUID : ".$loanvalue->OrderUID." OrderNumber : ".$loanvalue->OrderNumber."  LoanNumber - ".$loanvalue->LoanNumber."<hr>";

					} else {
						$data = array(
							'AutoExportStatus'	=>	2
						);
						$update = $this->Common_Model->updateOrderStatus($loanvalue->OrderUID,$data);
					}
					if($count >= $limit)
					{
						exit();
					}
				}

				if($count >= $limit)
				{
					exit();
				}
				echo 'Import Loan files success';
			}
		}    
	}
	public function uploadLoanToFTP()
	{
		require_once(APPPATH.'modules/Softworks/controllers/Softworks.php'); 
		$softworks =  new Softworks();
		$waitingLoans = $this->Common_Model->getWaitingLoansForUpload();
		if($waitingLoans)
		{
			$completePath = 'uploads/OrderDocumentPath/'.$waitingLoans->OrderNumber . '/'.$waitingLoans->LoanNumber.'.pdf';
			$ftpData = array(
				'OrderUID'		=>	$waitingLoans->OrderUID,
				'DocumentName'	=>	$waitingLoans->DocumentName,
				'DocumentURL'	=>	$completePath
			);
			$this->UploadToFtp($ftpData);
			$this->Common_Model->updateNextWorkflow($waitingLoans->OrderUID);
			$data = array(
				'AutoExportStatus'	=>	'0'
			);
			$update = $this->Common_Model->updateOrderStatus($loanvalue->OrderUID,$data);
			$softworks->addNewBatch();							
		}
	}
	public function uploadPDF($SFTPHost,$SFTPUser,$SFTPPassword,$SFTPPath,$RemoteFileName,$viewPath,$LoanNumber)
	{
		$host = $SFTPHost;
		$port = 22;
		$username = $SFTPUser;
		$password = $SFTPPassword;
		$remoteDir = $SFTPPath;
		$localDir = $viewPath;
		$file = $RemoteFileName;

		if (!function_exists("ssh2_connect"))
			die('Function ssh2_connect does not exist.');

		if (!$connection = ssh2_connect($host, $port))
			die('Failed to connect.');

		if (!ssh2_auth_password($connection, $username, $password))
			die('Failed to authenticate.');

		if (!$sftp_conn = ssh2_sftp($connection))
			die('Failed to create a sftp connection.');

		if (!$dir = opendir("ssh2.sftp://".intval($sftp_conn)."{$remoteDir}"))
			die('Failed to open the directory.');
		$remote = fopen("ssh2.sftp://".intval($sftp_conn)."$remoteDir$file", 'r');
		$local = fopen($localDir . $LoanNumber.'.pdf', 'w');
		$read = 0;
		$fileconn="ssh2.sftp://".intval($sftp_conn)."$remoteDir".$file;
		$statinfo = ssh2_sftp_stat($sftp_conn,$remoteDir.$file );
		$filesize = $statinfo['size']; 
		if($filesize >0)
		{
			while ( ($read < $filesize) && ($buffer = fread($remote, $filesize - $read)) )
			{
				$read += strlen($buffer);
				if (fwrite($local, $buffer) === FALSE)
				{
					echo "Failed to write to local file: $file\n";
					break;
				}else{
					$path_from = $remoteDir.$file;
					$path_to = $remoteDir.'/completed/'.$file;
					ssh2_sftp_rename($sftp_conn, $path_from, $path_to);
				}
			}
		}
		else
		{
			return false;
		}

	}


	/**
	* Function To get the Lock the Transaction
	* @param None
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return NULL
	* @since  20th April 2020
	*
	*/
	public function lock() {
		if(file_exists($this->pfilename))
		{
			$this->_lock = fopen($this->pfilename,'a+');
			if(!flock($this->_lock, LOCK_EX))
			{
				echo "string";
				exit();
			}
		}
		else
		{
			touch($this->pfilename);
			$this->_lock = fopen($this->pfilename, 'r');
			flock($this->_lock, LOCK_EX);
		}
	}

	/**
	* Function To get the Unlock the Transaction
	* @param None
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return NULL
	* @since  20th April 2020
	*
	*/
	public function unlock() {
		flock($this->_lock, LOCK_UN);
	}

	/**
	* Scheduler to import the loans from the source
	* sftp folder
	* @param None
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return NULL
	* @since  30th April 2020
	*
	*/
	function autoImport()
	{

		require_once(APPPATH.'modules/SFTP/controllers/ConnectSFTP.php'); 
		$exp =  new ConnectSFTP();
		require_once(APPPATH.'modules/Softworks/controllers/Softworks.php'); 
		$softworks =  new Softworks();
		$this->load->library('ftp');
		$this->lock();
		$mAutoImport = $this->Common_Model->getAutoImport(); 
		foreach ($mAutoImport as $key => $value) 
		{
			$fileList = array();
			$config['hostname'] =  $value->SFTPHost;
			$config['username'] =  $value->SFTPUser;
			$config['password'] =  $value->SFTPPassword;
			$config['debug']    = FALSE;
			$SFTPPath = $value->SFTPPath;
			$port = $value->SFTPPort;
			$list = array();
			if($value->SFTPProtocol == 'FTP')
			{
				if(empty($SFTPPath) || !$this->ftp->connect($config))
				{
					continue;
				}
				else
				{
					$list = $this->ftp->list_files($SFTPPath);

				}
			}
			if($value->SFTPProtocol == 'SFTP')
			{
				if(!$exp->connect($config))
				{
					continue;
				}
				else
				{
					$list = $exp->list_files($SFTPPath);

				}
			}
			foreach ($list as $listkey => $listvalue) {
				if(substr($listvalue, -4)==".zip")
				{
					$Loan = explode('_', $listvalue);
					$RefNumberArray = explode('.', $listvalue);
					$RefNumber = $RefNumberArray[0];
					$LoanNumber = $Loan[1];
					$checkAvailable  = $this->Common_Model->checkLoanNumber($LoanNumber,$value->ProjectUID);
					$importStatus = (isset($checkAvailable['AutoImportStatus']) ? $checkAvailable['AutoImportStatus'] : '');
					$missingDocsStatus = (isset($checkAvailable['IsMissingDocs']) ? $checkAvailable['IsMissingDocs'] : '');
					if(empty($checkAvailable) || $checkAvailable == 0 || $importStatus == '2' || $missingDocsStatus == '1')
					{
						$postData = array(
							'Customer'              =>  $value->CustomerUID,
							'ProductUID'            =>  $value->ProductUID,
							'ProjectUID'            =>  $value->ProjectUID,
							'LoanNumber'            =>  $LoanNumber, 
							'DataExtractionEnable'  =>  '1',
							'DocType'               =>  '8',
							'CustomerRefNum'		=>	$RefNumber
						);
						echo "Creating New Order <br>";
						if($importStatus == '2')
						{
							$OrderStatus = $checkAvailable;
						}
						else
						{
							$OrderStatus = $this->insertOrderDetail($postData);
						}
						if($OrderStatus)
						{
							$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderStatus['OrderUID']]);
							$statusData = array(
								'AutoImportStatus'  =>  '1'
							);
							$this->Common_Model->updateOrderData($statusData,$tOrders->OrderUID);
							$Orders = array(
								'OrderUID'          =>  $tOrders->OrderUID,
								'FileName'          =>  $listvalue,
								'FilePath'          =>  $listvalue
							);
							print_r($Orders);
							$type = 'FTPUpload';
							$uploadStatus = $this->UploadOrderFiles($Orders,$type);
							if($tOrders->IsOCREnabled == '1')
							{
								$softworks->addNewBatch();

							}
						}
					}
				}
			}
		}    
		$this->unlock();
	}

	/**
	* Verify the loan number after picking from the
	* source sftp folder and insert into the database
	* @param Orderdetails Array
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Bool
	* @since  12th April 2020
	*
	*/
	function insertOrderDetail($OrderDetails)
	{
		$this->load->model('Orderentry/Orderentrymodel','Orderentrymodel');

		$ProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID'=>$OrderDetails['ProjectUID']]);
		$Inserted_OrderNumbers = [];
		$Inserted_OrderUID = [];

		$PropertyRoles = [];

		$is_productdocuments_not_available = false;
		$unavailable_productdocuments = [];
		$OrderDetails['tOrders_ProductUID'] = $OrderDetails['ProductUID'];
		$OrderDetails['tOrders_ProjectUID'] = $OrderDetails['ProjectUID'];
		$OrderDetails['tOrders_LenderUID'] = '';

		// Generate and insert Package Number
		$PackageNumber = $this->Orderentrymodel->Package_Number($OrderDetails['ProductUID']);
		$PackageUID = $this->Common_Model->save('tOrderPackage', ['PackageNumber'=>$PackageNumber, 'CreatedByUserUID'=>$this->loggedid]);
		$OrderDetails['PackageUID'] = $PackageUID;
		
		$OrderDetails['InputDocTypeUID'] = $OrderDetails['DocType'];
		$OrderDetails['tOrders_PriorityUID'] = '';
		$OrderDetails['AltORderNumber'] = '';

		$OrderDetails['LoanPurpose'] = '';
		$OrderDetails['MOMFlag'] = '';
		$OrderDetails['GAPMortgageAmount'] = '';
		$OrderDetails['SecondLienFlag'] = '';
		$OrderDetails['DataExtractionEnable'] = $ProjectCustomer->DataExtraction;
		$result = $this->Orderentrymodel->insert_order($OrderDetails);
		if($result)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}

	/**
	* Once the loan processing completed
	* move the loan to destination sftp folder 
	* @param Orders and Uploadtype
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Bool
	* @since  24th April 2020
	*
	*/
	function UploadOrderFiles($Orders, $UploadType)
	{
		$this->load->model('Orderentry/Orderentrymodel','Orderentrymodel');

		require_once(APPPATH.'modules/SFTP/controllers/ConnectSFTP.php'); 
		$exp =  new ConnectSFTP();
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
			if($mSFTP->SFTPProtocol == 'FTP')
			{
				if(empty($SFTPPath) || !$this->ftp->connect($config))
				{
					continue;
				}
			}
			if($mSFTP->SFTPProtocol == 'SFTP')
			{
				if(!$exp->connect($config))
				{
					continue;
				}
			}
			$OrderUIDs = explode(",", $projectorder->OrderUIDs);

			foreach ($OrderUIDs as $key => $order) {
				
				$RemoteFilePath = $SFTPPath.$Orders['FilePath'];
				$RemoteFileName = $Orders['FileName'];

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
					}
					$download = false;
					if($mSFTP->SFTPProtocol == 'FTP')
					{
						if($this->ftp->download($RemoteFilePath, $DocumentPath))
							$download = true;
					}
					if($mSFTP->SFTPProtocol == 'SFTP')
					{
						if($this->downloadFromSFTP($mSFTP->SFTPHost,$mSFTP->SFTPUser,$mSFTP->SFTPPassword,$SFTPPath,$RemoteFileName,$viewPath))
						{

							$download = true;
						}
						else
						{
							$this->output->set_content_type('application/json');
							$this->output->set_output(json_encode(['validation_error'=>1, 'message'=>"Fail To get file from SFTP"]));
							exit();
						}
						
					}
					if ($download == true) {
						$files[] = $DocumentPath;

						// executes if files exists
						if (file_exists($DocumentPath)) {
							if($mProjectCustomer->ZipImport == '1')
							{
								$zip = new ZipArchive;
								$path = $viewPath;
								$fileName = $RemoteFileName;
								$res = $zip->open($path.$fileName);
								if($res==TRUE)
								{  
									$this->Common_Model->CreateDirectoryToPath($path.'contents/');
									$zip->extractTo($path.'contents/');
									$zip->close();
									if(!file_exists($path.'contents/Manifest.xml'))
									{
										$this->sendCorruptArchiveMail($tOrders,$path,$fileName);
										exit();
									}
									$xmlFile = simplexml_load_file($path.'contents/Manifest.xml');
									$file = json_encode($xmlFile);
									$file = str_replace('@','', $file);
									$file = json_decode($file);
									$loanDetail = $file->Loan->attributes;
									$fileDetail = $file->Loan->Files;
									$mergeFile = '';
									if($loanDetail->MAXLoanNumber != $tOrders->LoanNumber)
									{
										$this->sendCorruptArchiveMail($tOrders,$path,$fileName);
										exit();
									}

									foreach ($fileDetail as $key => $value) {
										foreach ($value as $k => $val) {
											$name = $val->attributes->Filename;
											$extension = strtolower(substr($name, (strripos($name, ".") + 1)));
											if($extension == 'pdf')
											{
												$mergeFile.=' '.$path.'contents/'.$name;

											}
										}
									}
									$cmd = 'gs -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dAutoRotatePages=/None -sOutputFile='.$path.$loanDetail->MAXLoanNumber.'.pdf'.$mergeFile;
									shell_exec($cmd);
									$tDocuments['DocumentName'] = $loanDetail->MAXLoanNumber.'.pdf';
									$tDocuments['DocumentURL'] = $path.$loanDetail->MAXLoanNumber.'.pdf';
									$tDocuments['OrderUID'] = $order;
									$tDocuments['IsStacking'] = 1;
									$tDocuments['TypeofDocument'] = 'Stacking';
									$tDocuments['UploadedDateTime'] = date('Y-m-d H:i:s');
									$tDocuments['UploadedByUserUID'] = $this->loggedid;
									$this->Orderentrymodel->save('tDocuments', $tDocuments);
									$data = array(
										'Guid'		=>	$loanDetail->Guid,
										'RegistrationStatus'	=>	$loanDetail->RegistrationStatus
									);
									$this->Orderentrymodel->UpdateOrderDetail($tOrders->OrderUID,$data);
									if($tOrders->IsOCREnabled == '1')
									{
										$ftpData = array(
											'OrderUID'		=>	$tOrders->OrderUID,
											'DocumentName'	=>	$loanDetail->MAXLoanNumber.'.pdf',
											'DocumentURL'	=>	$path.$loanDetail->MAXLoanNumber.'.pdf'
										);
										$this->UploadToFtp($ftpData);
										$nextWorkFlow = $this->Common_Model->updateNextWorkflow($tOrders->OrderUID);										
									}
									$SimpleXML = new SimpleXMLElement('<CdeLoan xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></CdeLoan>');
									$adfitech = $SimpleXML->addChild('adfitech');
									$adfitech->addAttribute('processDate',date('Y-m-d'));
									$adfitech->addAttribute('loanid',$tOrders->LoanNumber);
									$adfitech->addAttribute('transactionType','Pre Closing Review');
									$adfitech->addAttribute('event','Receipt');
									$adfitech->addAttribute('GUID',$loanDetail->Guid);
									$receipt = $adfitech->addChild('Receipt');
									$receipt->addAttribute('Filename',$fileName);
									$receipt->addAttribute('timestamp',date('Y-m-d').'_'.date('h-i-s'));
									$xmlFileName = $tOrders->LoanNumber.'_results_'.date('Y-m-d').'_'.date('h-i-s').'.xml';
									$SimpleXML->asXML($path.$xmlFileName);
									$resultFile = file_get_contents($path.$xmlFileName);
									$result = $this->Common_Model->formatXmlString($resultFile);
									$myfile = fopen($path.$xmlFileName, "w");
									fwrite($myfile, $result);
									fclose($myfile);
									if($this->uploadXMLtoSFTP($tOrders->OrderUID,$xmlFileName,$path))
									{

										$type=$this->config->item('emailType')['Receipt'];
										$recipients = $this->Common_Model->getEmailUsers($tOrders->ProjectUID,$type);
										$recipient = '';
										foreach ($recipients as $key => $value) {
											$recipient .= $value['EmailID'].',';
										} 
										$recipient = rtrim($recipient, ",");
										$subject = 'Maxex-Sourcepoint Loan Receipt / Import Confirmation';
										$config=$this->config->item('sendmail');
										$this->load->library('myemail',$config);
										$this->myemail->clear(true);							            
										$this->myemail->from($this->config->item('sendmail')['smtp_user']);
										$this->myemail->to($recipient);
										$this->myemail->set_newline("\r\n");
										$this->myemail->subject($subject);
										$this->myemail->message('');
										$this->myemail->attach($path.$xmlFileName);
										$this->myemail->send();
										rmdir($path.'contents/');
										unlink($path.$RemoteFileName);
									}
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

	}

	/**
	* Notifications (ACK / NACK) once the loan received
	* is corrupt
	* @param torders,path and filename
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  15th April 2020
	*
	*/
	public function sendCorruptArchiveMail($tOrders,$path,$fileName)
	{
		$statusData = array(
			'AutoImportStatus'  =>  '2'
		);
		$this->Common_Model->updateOrderData($statusData,$tOrders->OrderUID);
		$SimpleXML = new SimpleXMLElement('<CdeLoan xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></CdeLoan>');
		$adfitech = $SimpleXML->addChild('adfitech');
		$adfitech->addAttribute('processDate',date('Y-m-d'));
		$adfitech->addAttribute('loanid',$tOrders->LoanNumber);
		$adfitech->addAttribute('transactionType','Pre Closing Review');
		$adfitech->addAttribute('event','Receipt');
		$adfitech->addAttribute('GUID','');
		//echo $SimpleXML;
		$receipt = $adfitech->addChild('Receipt');
		$receipt->addAttribute('name',$fileName);
		$receipt->addAttribute('error','Corrupt Archive');
		$receipt->addAttribute('errorcode','5');
		$xmlFileName = $tOrders->LoanNumber.'_results_'.date('Y-m-d').'_'.date('h-i-s').'.xml';
		$SimpleXML->asXML($path.$xmlFileName);
		$resultFile = file_get_contents($path.$xmlFileName);
		$result = $this->Common_Model->formatXmlString($resultFile);
		$myfile = fopen($path.$xmlFileName, "w");
		fwrite($myfile, $result);
		fclose($myfile);
		if($this->uploadXMLtoSFTP($tOrders->OrderUID,$xmlFileName,$path))
		{
			$type=$this->config->item('emailType')['Corrupt'];
			$recipients = $this->Common_Model->getEmailUsers($tOrders->ProjectUID,$type);
			$recipient = '';
			foreach ($recipients as $key => $value) {
				$recipient .= $value['EmailID'].',';
			} 
			$recipient = rtrim($recipient, ",");
			$subject = 'Maxex-Sourcepoint Corrupt Import Transmission Notification';
			$config=$this->config->item('sendmail');
			$this->load->library('myemail',$config);
			$this->myemail->clear(true);							            
			$this->myemail->from($this->config->item('sendmail')['smtp_user']);
			$this->myemail->to($recipient);
			$this->myemail->set_newline("\r\n");
			$this->myemail->subject($subject);
			$this->myemail->message('');
			$this->myemail->attach($path.$xmlFileName);

			if(!$this->myemail->send())
			{
				echo json_encode(array('validation_error'=>1,'data'=>$this->email->print_debugger(),'message'=>'Zip Curupted Error in sending mail'));
			}
			else{
				echo json_encode(array('validation_error'=>0,'message'=>'Email sent Successfully'));

			}
		}
	}

	/**
	* Once the loan is imported
	* move te loan to the OCR FTP location
	* @param data Array
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  15th April 2020
	*
	*/
	function UploadToFtp($data)
	{
		$this->load->library('ftp');
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $data['OrderUID']]);
		$config['hostname'] =  FTP_HOST;
		$config['username'] =  FTP_USER;
		$config['password'] =  FTP_PWD;
		$config['debug']    = TRUE;
		$config['port']    = FTP_PORT;
		$SFTPPath =  FTP_PATH.'/'. $tOrders->OrderNumber.'_'.$data['DocumentName'];

		$config['debug'] = False;

		$locpath=FCPATH . $data['DocumentURL'];

		if($this->ftp->connect($config))
		{
			if($this->ftp->upload($locpath, $SFTPPath, 'auto', 0775)){
				echo "success";
			}else{
				echo "failed";
			}

			$this->ftp->close();
		}
		else
		{
			echo "failed";
		}	
	}

	/**
	* Function to downlaod the loan from
	* the source sftp path
	* @param host,username, password,path,RemoteFilename and view path
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  15th April 2020
	*
	*/
	public function downloadFromSFTP($SFTPHost,$SFTPUser,$SFTPPassword,$SFTPPath,$RemoteFileName,$viewPath)
	{
		$host = $SFTPHost;
		$port = 22;
		$username = $SFTPUser;
		$password = $SFTPPassword;
		$remoteDir = $SFTPPath;
		$localDir = FCPATH.$viewPath;

		if (!function_exists("ssh2_connect"))
			die('Function ssh2_connect does not exist.');

		if (!$connection = ssh2_connect($host, $port))
			die('Failed to connect.');

		if (!ssh2_auth_password($connection, $username, $password))
			die('Failed to authenticate.');

		if (!$sftp_conn = ssh2_sftp($connection))
			die('Failed to create a sftp connection.');

		if (!$dir = opendir("ssh2.sftp://".intval($sftp_conn)."{$remoteDir}"))
			die('Failed to open the directory.');

		$files = array();
		while ( ($file = readdir($dir)) !== false)
		{
			if(substr($file, -4)==".zip")
			{
				$files[]=$file;
			}
		}
		closedir($dir);

		foreach ($files as $file)
		{
			if($file == $RemoteFileName)
			{
				if (!$remote = fopen("ssh2.sftp://".intval($sftp_conn)."$remoteDir$file", 'r'))
				{
					echo "Failed to open remote file: $file\n";
					continue;
				}

				if (!$local = fopen($localDir . $file, 'w'))
				{
					echo "Failed to create local file: $file\n";
					continue;
				}

				$read = 0;
				$fileconn="ssh2.sftp://".intval($sftp_conn)."$remoteDir".$file;
				$statinfo = ssh2_sftp_stat($sftp_conn,$remoteDir.$file );
				$filesize = $statinfo['size']; 
				if($filesize >0)
				{
					while ( ($read < $filesize) && ($buffer = fread($remote, $filesize - $read)) )
					{
						$read += strlen($buffer);
						if (fwrite($local, $buffer) === FALSE)
						{
							echo "Failed to write to local file: $file\n";
							break;
						}
					}
				}
				else
				{
					return false;
				}
				fclose($local);
				fclose($remote);
				return true;

			}
		}
	}
	public function uploadXMLtoSFTP($OrderUID,$file,$localFilePath)
	{
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $tOrders->ProjectUID]);
		$localFile = FCPATH.$localFilePath.$file;
		$mSFTP = $this->Common_Model->get_row('mSFTP', ['mSFTP.SFTPUID'=>$mProjectCustomer->SFTPExportUID]);
		if($mSFTP->SFTPProtocol == 'FTP')
		{
			$this->load->library('ftp');
			$config['hostname'] =  $mSFTP->SFTPHost;
			$config['username'] =  $mSFTP->SFTPUser;
			$config['password'] =  $mSFTP->SFTPPassword;
			$config['debug']    = TRUE;
			$config['port']    = $mSFTP->SFTPPort;
			$SFTPPath = $mSFTP->SFTPPath.'/'.$file;
			$config['debug'] = False;

			$locpath=FCPATH . $data['DocumentURL'];
			
			if($this->ftp->connect($config))
			{
				if($this->ftp->upload($localFile, $SFTPPath, 'auto', 0775)){
					$this->ftp->close();
					return true;
					
				}else{
					$this->ftp->close();
					return false;
				}
			}

		}
		if($mSFTP->SFTPProtocol == 'SFTP')
		{
			$host 		=  $mSFTP->SFTPHost;
			$username 	=  $mSFTP->SFTPUser;
			$password 	=  $mSFTP->SFTPPassword;
			$debug    	= TRUE;
			$port    	= $mSFTP->SFTPPort;
			$SFTPPath 	= $mSFTP->SFTPPath;
			$remoteFile = $mSFTP->SFTPPath.'/'.$file;
			$connection = ssh2_connect($host, $port);
			ssh2_auth_password($connection, $username, $password);
			$sftp = ssh2_sftp($connection);
			$stream = fopen("ssh2.sftp://".intval($sftp)."$remoteFile", 'w');
			$file = file_get_contents($localFile);
			fwrite($stream, $file);
			fclose($stream);
			return true;
		}
	}

	/**
	* Scheduler to push the loan to the 
	* destination sftp location, runs every 15 mins 
	* @param OrderUID
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  30th April 2020
	*
	*/
	public function AutoExportOrder($OrderUID)
	{
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $tOrders->ProjectUID]);
		$file = $tOrders->LoanNumber.'.zip';
		$localFilePath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/'.$tOrders->LoanNumber .'/';
		$localFile = $localFilePath.$file;
		$mSFTP = $this->Common_Model->get_row('mSFTP', ['mSFTP.SFTPUID'=>$mProjectCustomer->SFTPExportUID]);
		if($mSFTP->SFTPProtocol == 'FTP')
		{
			echo "FTP initialize <br>";
			$this->load->library('ftp');
			$config['hostname'] =  $mSFTP->SFTPHost;
			$config['username'] =  $mSFTP->SFTPUser;
			$config['password'] =  $mSFTP->SFTPPassword;
			$config['debug']    = TRUE;
			$config['port']    = $mSFTP->SFTPPort;
			$SFTPPath = $mSFTP->SFTPPath.'/'.$file;

			$config['debug'] = False;

			$locpath=FCPATH . $data['DocumentURL'];
			
			if($this->ftp->connect($config))
			{
				echo "Upload Start <br>";
				if($this->ftp->upload($localFile, $SFTPPath, 'auto', 0775)){
					
				}else{
					
				}
				
				$this->ftp->close();
			}

		}
		if($mSFTP->SFTPProtocol == 'SFTP')
		{

			echo "SFTP initialize <br>";
			$host 		=  $mSFTP->SFTPHost;
			$username 	=  $mSFTP->SFTPUser;
			$password 	=  $mSFTP->SFTPPassword;
			$debug    	= TRUE;
			$port    	= $mSFTP->SFTPPort;
			$SFTPPath 	= $mSFTP->SFTPPath;
			$remoteFile = $mSFTP->SFTPPath.'/'.$file;
			$connection = ssh2_connect($host, $port);
			ssh2_auth_password($connection, $username, $password);
			$sftp = ssh2_sftp($connection);
			$stream = fopen("ssh2.sftp://".intval($sftp)."$remoteFile", 'w');
			echo "Upload Start <br>";
			$file = file_get_contents($localFile);
			fwrite($stream, $file);
			fclose($stream);
		}
		$data = array(
			'StatusUID'					=>	$this->config->item('keywords')['Completed'],
			'IsAutoExport'				=>	'1',
			'AutoExportStatus'			=>	'1',
			'OrderCompleteDateTime'		=>	date('Y-m-d H:i:s')
		);
		$this->Common_Model->updateOrderData($data,$tOrders->OrderUID);
		$type = '4';
		$recipients = $this->Common_Model->getEmailUsers($tOrders->ProjectUID,$type);
		$recipient = '';
		foreach ($recipients as $key => $value) {
			$recipient .= $value['EmailID'].',';
		} 
		echo "Email process start <br>";
		$recipient = rtrim($recipient, ",");
		$subject = 'Maxex-Sourcepoint Loan Export Notification';
		$message = 'Hello All,<br><br> Loan # '.$tOrders->LoanNumber.'.zip is exported to the folder "'.$mSFTP->SFTPPath. '". Please confirm the same';

		$config=$this->config->item('sendmail');
		$this->load->library('myemail',$config);
		$this->myemail->clear(true);							            
		$this->myemail->from($this->config->item('sendmail')['smtp_user']);
		$this->myemail->to($recipient);
		$this->myemail->set_newline("\r\n");
		$this->myemail->subject($subject);
		$this->myemail->message($message);
		if($this->myemail->send())
		{
			echo "Email Sent Successfully <br>";
		}
		else
		{
			echo "Error in Sending Email<br>";
		}

	}

	/**
	* Function to get the list of laons to be exported
	* 
	* @param Null
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  30th April 2020
	*
	*/
	public function ExportOrder()
	{
		$this->pfilename = '/tmp/export.lock';
		$this->lock();
		echo "process lock <br>";
		$OrderDetail = $this->Common_Model->get_row('tOrders', ['IsAutoExport'=>'3']);
		if($OrderDetail)
		{
			$OrderUID = $OrderDetail->OrderUID;
			echo "zip creation start for orderId".$OrderUID.'<br>';
			$this->DownloadZip($OrderUID);
			echo "zip progress complete";
			$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
			$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $tOrders->ProjectUID]);
			if($mProjectCustomer->IsAutoExport == '1' && $tOrders->AutoExportStatus != '1')
			{
				echo "Auto Export start <br>";
				$this->AutoExportOrder($OrderUID);
			}
		}
		$this->unlock();
		
	}

	/**
	* Function to download the zip archive from the sftp folder
	* 
	* @param OrderUID
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  18th April 2020
	*
	*/
	public function DownloadZip($OrderUID = null)
	{
		if($OrderUID == null)
		{
			$OrderDetail = $this->Common_Model->get_row('tOrders', ['IsAutoExport'=>'3']);
			$OrderUID = $OrderDetail->OrderUID;
		}
		$ISTEMPFILECREATED = false;
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $OrderUID]);
		$tDocuments = $this->Common_Model->get_row('tDocuments', ['OrderUID' => $OrderUID, 'IsStacking' => 1]);
		$DocumentPath = $tDocuments->DocumentURL;
		if (!file_exists($DocumentPath)) {
			$Temp_DocumentPath = str_replace('uploads/', 'S3_uploads/', $DocumentPath);
			if (file_exists($Temp_DocumentPath)) {
				// $create_temp_file = file_put_contents($DocumentPath, file_get_contents($Temp_DocumentPath));
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
				$DocumentPath = $Temp_DocumentPath;
			}
		} else {
			if (strpos($DocumentPath, "S3_uploads/") !== false) {
				$DocumentPath = str_replace('S3_uploads/', 'uploads/', $DocumentPath);
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
				$DocumentPath = $Temp_DocumentPath;
			}
		}
		$DEFAULT_URL = FCPATH . $DocumentPath;
		$DestinationPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' .$tOrders->LoanNumber .'/';
		$DocumentPages = $this->GetPageNos($OrderUID, $tOrders);
		$DocumentTypePDF_Array = $DocumentPages['DocumentTypePDF_Array'];
		echo "get all pdf page number array <br>";
		$pages = $DocumentPages['pages'];
		// Remove Directory
		if (file_exists($DestinationPath)) 
		{
			echo "deleting previous zip <br>";
			$dir_handle = opendir($DestinationPath);
			while($file = readdir($dir_handle)) 
			{
				if ($file != "." && $file != "..") 
				{
					if (!is_dir($DestinationPath."/".$file)){
						unlink($DestinationPath."/".$file);
					}
					else
						delete_directory($DestinationPath.'/'.$file);
				}
			}
			closedir($dir_handle);
			rmdir($DestinationPath);
		}

		foreach ($DocumentTypePDF_Array as $CategoryName => $DocumentType) {
			foreach ($DocumentType as $DocumentTypeName => $PageNos) {
				$Relative_Destination_Path = 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/'.$tOrders->LoanNumber .'/';
				if(strpos($DocumentTypeName,'.pdf') == false)
					$DocumentTypeName =$DocumentTypeName.'.pdf';
				$this->CreateDocumentTypePDF($DEFAULT_URL, $Relative_Destination_Path, $CategoryName, $DocumentTypeName, $PageNos, $tOrders->OrderNumber, $tOrders->LoanNumber);
			}
		}
		$this->Common_Model->UpdateExportedStatus($tOrders);
		$SourcePath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/'.$tOrders->LoanNumber .'/';
		if (!file_exists($SourcePath)) {
			$SourcePath = FCPATH . 'S3_uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/'.$tOrders->LoanNumber .'/';
		}
		$DestinationPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/'.$tOrders->LoanNumber .'/';
		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $tOrders->ProjectUID]);
		if($mProjectCustomer->DocInstance == 1)
		{
			echo "xml download start <br>";
			$this->XMLDownload($tOrders->OrderUID,$tOrders->OrderNumber);
			echo "zip scaning start <br>";
			$zipFiles = scandir($SourcePath);
			foreach ($zipFiles as $key => $value) {
				if($value == '.' || $value == '..')
				{
				}
				else
				{
					$this->zip->read_file($SourcePath.$value);
				}
			}
			$file = ($tOrders->LoanNumber != '' ? $tOrders->LoanNumber . '.zip' : $tOrders->OrderNumber . '.zip');
		}
		echo "zip is ready <br>";
		$this->zip->archive($DestinationPath . $file);
		if($tOrders->IsAutoExport == '3')
		{
			$data = array(
				'StatusUID'		=>	$this->config->item('keywords')['Export'],
				'IsAutoExport'	=>	'0'
			);
			echo "updating order status <br>";
			$this->Common_Model->updateOrderData($data,$tOrders->OrderUID);

		}
	}

	/**
	* Function to retrieve the page numbers
	* 
	* @param OrderUID,tOders array
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  11th April 2020
	*
	*/
	function GetPageNos($OrderUID, $tOrders)
	{
		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $tOrders->ProjectUID]);
		$pages = "";
		$DocumentTypePDF_Array=[];

		$this->db->select('tCategory.CategoryName,tCategory.CategoryPosition');
		$this->db->from('tCategory');
		$this->db->join('mCategory', 'mCategory.HashCode = tCategory.CategoryName');
		$this->db->join('mProjectCategory', 'mCategory.CategoryUID = mProjectCategory.CategoryUID AND mProjectCategory.ProjectUID = '.$mProjectCustomer->ProjectUID);

		$this->db->where(array('OrderUID'=>$OrderUID));
		$this->db->where('mProjectCategory.IsExport=1');


		$this->db->group_by('tCategory.CategoryName');
		$this->db->order_by('tCategory.tCategoryUID', 'ASC');

		$categorysql = $this->db->get()->result_array();
		if($mProjectCustomer->DocInstance == 1)
		{
			$this->db->select('tCategory.CategoryName,tCategory.CategoryPosition');
			$this->db->from('tCategory');
			$this->db->join('mClientCategory', 'mClientCategory.HashCode = tCategory.CategoryName');
			$this->db->join('mProjectCategory', 'mClientCategory.ClientCategoryUID = mProjectCategory.ClientCategoryUID AND mProjectCategory.ProjectUID = '.$mProjectCustomer->ProjectUID);

			$this->db->where(array('OrderUID'=>$OrderUID));
			$this->db->where('mProjectCategory.IsExport=1');


			$this->db->group_by('tCategory.CategoryName');
			$this->db->order_by('tCategory.tCategoryUID', 'ASC');
			$categorysql = $this->db->get()->result_array();

		}
		foreach ($categorysql as $key => $category_fetch) :
			$CategoryName = $category_fetch['CategoryName'];
			$CategoryPosition = $category_fetch['CategoryPosition'];
			$CategoryPages = '';

			if($mProjectCustomer->DocInstance == 1)
			{
				$this->db->select('tSubCategory.SubCategoryName,tSubCategory.SubCategoryPosition,tPage.InstanceUID');
				$this->db->from('tSubCategory');
				$this->db->where(array('tSubCategory.ParentCategoryName'=>$CategoryName, 'tPage.OrderUID'=>$OrderUID));
				$this->db->join('tPage','tSubCategory.ParentCategoryName = tPage.SubCategoryName');	
				$this->db->group_by('tPage.InstanceUID');
				$this->db->order_by('tSubCategory.tSubCategoryUID', 'ASC');
				$this->db->order_by('tPage.InstanceUID', 'ASC');
				$subcategorysql = $this->db->get()->result_array();	
			}
			else
			{
				$this->db->select('tSubCategory.SubCategoryName,tSubCategory.SubCategoryPosition');
				$this->db->from('tSubCategory');
				$this->db->where(array('ParentCategoryName'=>$CategoryName, 'OrderUID'=>$OrderUID));
				$this->db->group_by('tSubCategory.SubCategoryName');
				$this->db->order_by('tSubCategory.tSubCategoryUID', 'ASC');

				$subcategorysql = $this->db->get()->result_array();

			}
			foreach ($subcategorysql as $key => $subcategory_fetch) :
				$SubCategoryName = $subcategory_fetch['SubCategoryName'];
				$SubCategoryPosition = $subcategory_fetch['SubCategoryPosition'];
				$instanse = $subcategory_fetch['InstanceUID'];
				$SubCategoryPages = '';
				$pagesql = '';
				if($mProjectCustomer->DocInstance == 0)
				{
					$pagesql = $this->db->query("SELECT PageNo,PagePosition FROM tPage WHERE CategoryName = '$CategoryName' AND SubCategoryName = '$SubCategoryName' AND OrderUID = '" . $OrderUID . "'")->result_array();

				}
				else
				{
					$pagesql = $this->db->query("SELECT PageNo,PagePosition,InstanceUID FROM tPage WHERE CategoryName = '$CategoryName' AND SubCategoryName = '$SubCategoryName' AND InstanceUID = '$instanse' AND OrderUID = '" . $OrderUID . "'")->result_array();
					
				}
				foreach ($pagesql as $key => $page_fetch) :
					$PageNo = $page_fetch['PageNo'];
					$PagePosition = $page_fetch['PagePosition'];

					$pages .= $PageNo . ' ';
					$SubCategoryPages .= $PageNo . ' ';
				endforeach;

				if ($mProjectCustomer->ExportLevel == 'DocumentType' && !empty($SubCategoryPages)) {

					$mCategory = $this->Common_Model->get_row('mCategory', ['HashCode' => $CategoryName]);
					$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['HashCode' => $SubCategoryName]);
					if($mProjectCustomer->DocInstance == 1)
					{
						$mClientDocumentType = $this->Common_Model->get_row('mClientDocumentType', ['HashCode' => $SubCategoryName]);

					}
					else
					{
						$mClientDocumentType = $this->db->query('Select ClientCustomUID,ClientCustomUID from mProjectDocumentType left join mClientDocumentType on mProjectDocumentType.ClientDocumentTypeUID = mClientDocumentType.ClientDocumentTypeUID where mProjectDocumentType.ProjectUID='.$mProjectCustomer->ProjectUID.' and mProjectDocumentType.DocumentTypeUID='.$mDocumentType->DocumentTypeUID)->row();

					}
					if($mClientDocumentType->ClientCustomUID != 0 || $mClientDocumentType->ClientCustomUID != ''){
						$DocumentTypePDF_Array[$mCategory->CategoryName][$mClientDocumentType->ClientCustomUID.'-'.$page_fetch['InstanceUID'].'.pdf'] = $SubCategoryPages;
					}
					else if (($mDocumentType->SchemaUID != 0 || $mDocumentType->SchemaUID != '') || ($mDocumentType->SequenceUID != 0 || $mDocumentType->SequenceUID !='')) {
						$DocumentTypePDF_Array[$mCategory->CategoryName][$mDocumentType->SchemaUID.'_'.$mDocumentType->SequenceUID] = $SubCategoryPages;

					}else {

						$DocumentTypePDF_Array[$mCategory->CategoryName][$mDocumentType->DocumentTypeName] = $SubCategoryPages;
					}

				}

				$CategoryPages .= $SubCategoryPages . ' ';
			endforeach;


			if ($mProjectCustomer->ExportLevel == 'Category' && !empty($CategoryPages)) {

				$mCategory = $this->Common_Model->get_row('mCategory', ['HashCode' => $CategoryName]);
				$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['HashCode' => $SubCategoryName]);
				$mClientDocumentType = $this->db->query('Select * from mProjectDocumentType left join mClientDocumentType on mProjectDocumentType.ClientDocumentTypeUID = mClientDocumentType.ClientDocumentTypeUID where mProjectDocumentType.ProjectUID='.$mProjectCustomer->ProjectUID.' and mProjectDocumentType.DocumentTypeUID='.$mDocumentType->DocumentTypeUID)->row();
				if($mClientDocumentType->ClientCustomUID != 0 || $mClientDocumentType->ClientCustomUID != ''){
					$DocumentTypePDF_Array[$mCategory->CategoryName][$mClientDocumentType->ClientCustomUID.'-'.$page_fetch['InstanceUID'].'.pdf'] = $SubCategoryPages;
				}
				else if(($mCategory->SchemaUID != 0 || $mCategory->SchemaUID != '') || ($mCategory->SequenceUID != 0 || $mCategory->SequenceUID != '')){
					$DocumentTypePDF_Array[$mCategory->CategoryName][$mCategory->SchemaUID.'_'.$mCategory->SequenceUID] = $CategoryPages;
				}else {
					$DocumentTypePDF_Array[$mCategory->CategoryName][$mCategory->CategoryName] = $CategoryPages;
				}

			}

		endforeach;

		return ['pages'=>$pages, 'DocumentTypePDF_Array'=>$DocumentTypePDF_Array];
	}

	/**
	* Function to create the stacked pdf
	* 
	* @param sourcepath,destinationpath,categoryname,documenttypename,page,Ordernumber and Loan number
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  8th April 2020
	*
	*/
	function CreateDocumentTypePDF($SourcePath, $DestinationPath, $CategoryName, $DocumentTypeName, $Pages, $OrderNumber, $LoanNumber)
	{
		$prefix = $LoanNumber != '' ? $LoanNumber : $OrderNumber;
		if (!file_exists(FCPATH . $DestinationPath)) {
			if (!mkdir(FCPATH . $DestinationPath, 0777, true)) {
				die('Unable to Create Specifed Directory');
			}
		}
		$CategoryName = $this->clean($CategoryName);
		$DocumentTypeName = $this->clean($DocumentTypeName);			
		$OutputPath = FCPATH . $DestinationPath;
		$OutputPath = $OutputPath.$this->clean($DocumentTypeName);
		$filename = $this->clean($prefix) . '_' . $this->clean($DocumentTypeName) .'.pdf';
		$additional_commands = 'setlocal enabledelayedexpansion FOR /F %%A IN (\' dir / B / ON * . pdf \') DO (set command=!command! "%%A")';
		$output = shell_exec("pdftk \"" . $SourcePath . "\" cat " . $Pages . " output \"" . $OutputPath . "\"");

		$categorypdfs = "pdftk " . $SourcePath . " cat " . $Pages . " output \"" . $OutputPath . "\"";
		return $filename;	
	}

	/**
	* Function to downlad the XML after data entry
	* 
	* @param OrderUID
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return BOOL
	* @since  8th April 2020
	*
	*/
	function XMLDownload($OrderUID)
	{
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $OrderUID]);
		$DocumentTypes = $this->Common_Model->GetDocumentTypesByID($tOrders->ProjectUID,$OrderUID);
		$tDocuments = $this->Common_Model->get_row('tDocuments', ['OrderUID' => $OrderUID, 'IsStacking' => 1]);
		$fileSize=filesize(FCPATH.$tDocuments->DocumentURL);

		$SimpleXML = new SimpleXMLElement('<CdeLoan xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></CdeLoan>');
		$Header = $SimpleXML->addChild('adfitech');
		$Header->addAttribute('vendor','D1FF6133-67CD-4E48-85D0-BDB4DC12FC62');
		$Header->addAttribute('Guid', $tOrders->Guid);
		$Header->addAttribute('loanid', $tOrders->LoanNumber);
		$Header->addAttribute('processDate', date('Y-m-d'));
		$Header->addAttribute('transactionType', ($tOrders->RegistrationStatus ? $tOrders->RegistrationStatus : "Pre Closing Review"));
		$Documents = $Header->addChild('Documents');		
		$DocumentTypePDF_Array = $this->GetDocumentPagesMapped($OrderUID,$tOrders);	
		if ($DocumentTypes) 
		{
			foreach ($DocumentTypes as $key => $value) 
			{
				$AttributesVersion = $this->Common_Model->GetAttributesVersion($OrderUID,$value->HashCode);
				foreach ($AttributesVersion as $av => $att) 
				{
					$MappedAttributes = $this->Common_Model->GetAttributesByDocumentTypes($tOrders->OrderUID,$value->ClientDocumentTypeUID,$att->InstanceUID);
					$Pages = $this->db->query('Select count(*) as totalPage from tPage where SubCategoryName="'.$value->HashCode.'" and OrderUID='.$tOrders->OrderUID.' and InstanceUID = '.$att->InstanceUID)->row();
					$PageType = 'singlepage';
					$totalPage = 0;
					if ($Pages) {

						$totalPage = $Pages->totalPage;
						if ($totalPage > 1) {
							$PageType = 'multipage';
						}else{
							$PageType = 'singlepage';
						}
					}
					$PageSize = round($fileSize/$totalPage);
					$hashcode=substr(md5($key.$av), 0, 32);
					$MappedDocuments = $Documents->addChild('Document');
					$filename = $value->ClientCustomUID.'-'.$att->InstanceUID.'.pdf';
					$MappedDocuments->addAttribute('filename',$filename);
					$MappedDocuments->addAttribute('type',$value->DocumentTypeName);
					$MappedDocuments->addAttribute('pages',$totalPage);
					$MappedDocuments->addAttribute('version',$att->InstanceUID);
					$MappedDocuments->addAttribute('fileType','pdf');
					$MappedDocuments->addAttribute('fileSize',$PageSize);
					$MappedDocuments->addAttribute('format',$PageType);
					$MappedDocuments->addAttribute('hash',$hashcode);

					if (!empty($MappedAttributes)) 
					{
						foreach ($MappedAttributes as $a => $attr) 
						{
							$Attributes = $MappedDocuments->addChild('attribute');
							$attributeValue= $attr->attributeValue;
							if ($attr->AttributeType == 'Yes/No') {
								if ($attributeValue == '1') {
									$attributeValue = 'Y';
								}else if($attributeValue == '0'){
									$attributeValue = 'N';
								}
								else
								{
									$attributeValue = '';
								}
							}
							if ($attr->AttributeType == 'Date' && $attributeValue != '') {
								$attributeValue = date('m/d/Y',strtotime($attr->attributeValue));
							}
							$Attributes->addAttribute('name',$attr->AttributeName);
							$Attributes->addAttribute('value',$attributeValue);
							if(empty($attributeValue) && !is_numeric($attributeValue))
							{
								$pageNo = '';
							}
							else
							{
								$pageNo = $attr->MappedPageNo;
							}
							$Attributes->addAttribute('page',$pageNo);
						}
					}
					$FeeSectionArray = array();
					$CheckIfFeeExists = $this->Common_Model->GetFeeDataByOrderID($OrderUID,$value->ClientDocumentTypeUID,$att->InstanceUID);
					
					if (!empty($CheckIfFeeExists)) 
					{
						
						$Fees = $MappedDocuments->addChild('Fees');

						$MainFeeTypes = $this->Common_Model->GetFeeSections();
						foreach ($MainFeeTypes as $mt => $mft) 
						{
							foreach ($CheckIfFeeExists as $ck => $cf) 
							{

								$FeeSection = $this->db->query("SELECT * FROM `mFeeSection` where SectionId=".$cf->FeeSectionId)->row();
								$FeeData = $this->Common_Model->GetFeeDataByFeeTypes($mft->SectionId,$OrderUID,$value->ClientDocumentTypeUID,$att->InstanceUID,$cf->LineNumber);
								if (!empty($FeeData)) 
								{
									$FeeName = $this->db->query("SELECT * FROM `tFeeEnumeration` where FeeId=".$cf->FeeId)->row();
									
									
									$FeeAttributes = $this->Common_Model->GetFeeAttributes();
									$FeeDataAttributes = $this->Common_Model->GetFeeDataByFeeAttributes($mft->SectionId,$OrderUID,$value->ClientDocumentTypeUID,$att->InstanceUID,$cf->LineNumber,$cf->FeeId);
									
									if ($FeeDataAttributes) 
									{
										$FeeList = $Fees->addChild('Fee');
										foreach ($FeeDataAttributes as $fa => $ftr) 
										{
											$FeeValue ='';
											if ($ftr->AttributeName == 'FromDate' || $ftr->AttributeName == 'ToDate') {
												$FeeValue = date('m/d/Y',strtotime($ftr->AttributeValue));
												if ($FeeValue == '01/01/1970' || $FeeValue == '12/31/1969') {
													$FeeValue = '';
												}
											}else{
												$FeeValue = $ftr->AttributeValue;
											}
											$FeeAttributeData=$FeeList->addChild($ftr->AttributeName);
											$FeeAttributeData->addAttribute('value',$FeeValue);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$SimpleXML->asXML('xml/'.$tOrders->LoanNumber.'.xml');
		$resultFile = file_get_contents(FCPATH.'xml/'.$tOrders->LoanNumber.'.xml');
		$result = $this->Common_Model->formatXmlString($resultFile);
		$myfile = fopen(FCPATH.'uploads/OrderDocumentPath/'.$tOrders->OrderNumber.'/'.$tOrders->LoanNumber.'/'.$tOrders->LoanNumber.'_results.xml', "w");
		fwrite($myfile, $result);
		fclose($myfile);
		return true;


	}

	/**
	* Function to get the mapped page number
	* 
	* @param OrderUID,tOrders Array
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  4th April 2020
	*
	*/
	function GetDocumentPagesMapped($OrderUID,$tOrders)
	{
		$data =array();
		$tDocuments = $this->Common_Model->get_row('tDocuments', ['OrderUID' => $OrderUID, 'IsStacking' => 1]);
		$DocumentPath = $tDocuments->DocumentURL;
		if (!file_exists($DocumentPath)) {
			$Temp_DocumentPath = str_replace('uploads/', 'S3_uploads/', $DocumentPath);
			if (file_exists($Temp_DocumentPath)) {
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
			}
		} else {
			if (strpos($DocumentPath, "S3_uploads/") !== false) {
				$DocumentPath = str_replace('S3_uploads/', 'uploads/', $DocumentPath);
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
			}
		}

		$DEFAULT_URL = FCPATH . $DocumentPath;
		$DestinationPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/Content/';
		$DocumentPages = $this->GetPageNos($tOrders->OrderUID, $tOrders);
		$DocumentTypePDF_Array = $DocumentPages['DocumentTypePDF_Array'];

		foreach ($DocumentTypePDF_Array as $CategoryName => $DocType) {

			foreach ($DocType as $DocumentTypeName => $PageNos) {

				$Relative_Destination_Path = 'uploads/SplitPDF/' . $tOrders->OrderNumber . '/Content/';
				$PageSize =0;
				$data['DocumentTypes'][] = array('DocumentTypeName'=>$DocumentTypeName,'PageSize'=>$PageSize);
			}
		}
		return $data;
	}

	/**
	* Function to clean the psot paramenters
	* 
	* @param String
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  4th April 2020
	*
	*/
	function clean($string)
	{
		$string = preg_replace('/[\/\\\\]/', '_OR_', $string); // Replaces all spaces with hyphens.
		
		return preg_replace('/[\/\\\\*|$":?]/', '_', $string); // Removes special chars.
	}
	/**
	* Function to create stacked pdf in background
	* 
	* @param String
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  6th May 2020
	*
	*/
	public function DownloadPDF($OrderUID)
	{
		$ISTEMPFILECREATED = false;
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

		$ISTEMPFILECREATED = false;
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID' => $OrderUID]);
		$tDocuments = $this->Common_Model->get_row('tDocuments', ['OrderUID' => $OrderUID, 'IsStacking' => 1]);
		$DocumentPath = $tDocuments->DocumentURL;
		$S3_status = false;
		if (!file_exists($DocumentPath)) {
			$Temp_DocumentPath = str_replace('uploads/', 'S3_uploads/', $DocumentPath);

			if (file_exists($Temp_DocumentPath)) {
				// $create_temp_file = file_put_contents($DocumentPath, file_get_contents($Temp_DocumentPath));
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
				$DocumentPath = $Temp_DocumentPath;
				$S3_status = true;
			}
		} else {

			if (strpos($DocumentPath, "S3_uploads/") !== false) {
				$DocumentPath = str_replace('S3_uploads/', 'uploads/', $DocumentPath);
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
			}
		}
		if (!file_exists($DocumentPath)) {
			return 0;
		}
		$DEFAULT_URL = FCPATH . $DocumentPath;
		$DocumentPages = $this->GetPageNos($OrderUID, $tOrders);
		$DocumentTypePDF_Array = $DocumentPages['DocumentTypePDF_Array'];
		$pages = $DocumentPages['pages'];
		$temp_OutputPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . $tOrders->OrderNumber . '_temp_Stacked.pdf';
		if($S3_status == true)
		{
			$temp_OutputPath = FCPATH . 'S3_uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . $tOrders->OrderNumber . '_temp_Stacked.pdf';
		}
		$main = "pdftk " . $DEFAULT_URL . " cat " . $pages . " output " . $temp_OutputPath . "";
		$output = shell_exec("pdftk " . $DEFAULT_URL . " cat " . $pages . " output " . $temp_OutputPath . "");
		$catname = '';
		$bookmark_content = "";
		$StackingDocument = $this->Common_Model->get_row('mProjectMappedDocuments', ['ProjectUID'=>$tOrders->ProjectUID, 'WorkflowModuleUID'=>2]);
		if($StackingDocument->StacxDocuments == 2){
			$bookmarks_sql = $this->db->query("SELECT DISTINCT SubCategoryName FROM tPage JOIN mClientCategory ON tPage.CategoryName = mClientCategory.HashCode	JOIN mProjectCategory ON mProjectCategory.ClientCategoryUID = mClientCategory.ClientCategoryUID AND mProjectCategory.ProjectUID = '" . $tOrders->ProjectUID . "' WHERE OrderUID = '" . $OrderUID . "' AND mProjectCategory.IsExport = 1	 ORDER BY PagePosition")->result_array();

		}
		else
		{
			$bookmarks_sql = $this->db->query("SELECT DISTINCT SubCategoryName FROM tPage 
				JOIN mCategory ON tPage.CategoryName = mCategory.HashCode
				JOIN mProjectCategory ON mProjectCategory.CategoryUID = mCategory.CategoryUID AND mProjectCategory.ProjectUID = '" . $tOrders->ProjectUID . "'
				WHERE OrderUID = '" . $OrderUID . "' AND mProjectCategory.IsExport = 1
				ORDER BY PagePosition")->result_array();
		}
		foreach ($bookmarks_sql  as $key => $bookmarks_fetch) :

			$SubCategoryName = $bookmarks_fetch['SubCategoryName'];

			$tPagesql = $this->db->query("SELECT * FROM tPage WHERE OrderUID = '" . $OrderUID . "' AND SubCategoryName = '$SubCategoryName' LIMIT 1")->result_array();

			foreach ($tPagesql as $key => $fetch) :
				if($StackingDocument->StacxDocuments == 2){
					$mCategory = $this->Common_Model->get_row('mClientCategory', ['HashCode' => $fetch['CategoryName']]);
					$mDocumentType = $this->Common_Model->get_row('mClientDocumentType', ['HashCode' => $fetch['SubCategoryName']]);
				}
				else
				{
					$mCategory = $this->Common_Model->get_row('mCategory', ['HashCode' => $fetch['CategoryName']]);
					$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['HashCode' => $fetch['SubCategoryName']]);
				}
				//echo $fetch['CategoryName'].' - '.$SubCategoryName.' - '.$fetch['LogicPageNumber'].'<br>';
				if ($catname != $fetch['CategoryName']) {
					$catname = $fetch['CategoryName'];
					$bookmark_content .= 'BookmarkBegin' . PHP_EOL;
					$bookmark_content .= 'BookmarkTitle:' . $tOrders->LoanNumber . '_' . $mCategory->CategoryName . PHP_EOL;
					$bookmark_content .= 'BookmarkLevel: 1' . PHP_EOL;
					$bookmark_content .= 'BookmarkPageNumber: ' . $fetch['LogicPageNumber'] . PHP_EOL;

					$bookmark_content .= 'BookmarkBegin' . PHP_EOL;
					$bookmark_content .= 'BookmarkTitle:' . $tOrders->LoanNumber . '_' . $mDocumentType->DocumentTypeName  . PHP_EOL;
					$bookmark_content .= 'BookmarkLevel: 2' . PHP_EOL;
					$bookmark_content .= 'BookmarkPageNumber: ' . $fetch['LogicPageNumber'] . PHP_EOL;
				} else {
					$bookmark_content .= 'BookmarkBegin' . PHP_EOL;
					$bookmark_content .= 'BookmarkTitle:' . $tOrders->LoanNumber . '_' . $mDocumentType->DocumentTypeName . PHP_EOL;
					$bookmark_content .= 'BookmarkLevel: 2' . PHP_EOL;
					$bookmark_content .= 'BookmarkPageNumber: ' . $fetch['LogicPageNumber'] . PHP_EOL;
				}

			endforeach;

		endforeach;

		$bookmark_txt_filename = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . date('YmdHis') . '.txt';
		if($S3_status == true)
		{
			$bookmark_txt_filename = FCPATH . 'S3_uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . date('YmdHis') . '.txt';
		}
		file_put_contents($bookmark_txt_filename, $bookmark_content);

		$OutputPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . $tOrders->OrderNumber . '_Stacked.pdf';
		if($S3_status == true)
		{
			$OutputPath = FCPATH . 'S3_uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . $tOrders->OrderNumber . '_Stacked.pdf';
		}
		shell_exec("pdftk " . $temp_OutputPath . " update_info " . $bookmark_txt_filename . " output " . $OutputPath . "");
		if (file_exists($temp_OutputPath)) {
			unlink($temp_OutputPath);
		}
		if (file_exists($bookmark_txt_filename)) {
			unlink($bookmark_txt_filename);
		}
	}
	/**
	* Function to generate staced pdf for old loans in project
	* 
	* @param String
	* 
	* @author Rajat <rajat.goswami@avanzegroup.com>
	* @return Null
	* @since  6th May 2020
	*
	*/
	public function generatepdf($projectId)
	{
		$query = $this->db->query('select OrderUID,OrderNumber,LoanNumber from tOrders where ProjectUID = "'.$projectId.'"');
		$orders = $query->result();
		echo "<pre>";
		foreach ($orders as $key => $value) {
			$uploadPath = FCPATH . 'uploads/OrderDocumentPath/' . $value->OrderNumber . '/' . $value->OrderNumber . '_Stacked.pdf';
			$s3Path = FCPATH . 'S3_uploads/OrderDocumentPath/' . $value->OrderNumber . '/' . $value->OrderNumber . '_Stacked.pdf';
			if(!(file_exists($uploadPath) || file_exists($s3Path)))
			{
				print_r($value);
				$this->DownloadPDF($value->OrderUID);
			}
		}
		
	}
	public function UploadFolderToClient()
	{
		$tOrders = $this->Common_Model->get_row('tOrders', ['AutoExportStatus'=>'4']);
		$OrderUID = $tOrders->OrderUID;
		$ISTEMPFILECREATED = false;
		$tDocuments = $this->Common_Model->get_row('tDocuments', ['OrderUID' => $OrderUID, 'IsStacking' => 1]);
		$DocumentPath = $tDocuments->DocumentURL;
		if (!file_exists($DocumentPath)) {
			$Temp_DocumentPath = str_replace('uploads/', 'S3_uploads/', $DocumentPath);
			if (file_exists($Temp_DocumentPath)) {
				// $create_temp_file = file_put_contents($DocumentPath, file_get_contents($Temp_DocumentPath));
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
				$DocumentPath = $Temp_DocumentPath;
			}
		} else {
			if (strpos($DocumentPath, "S3_uploads/") !== false) {
				$DocumentPath = str_replace('S3_uploads/', 'uploads/', $DocumentPath);
				$fileupload = $this->s3->getObject($this->config->item('BucketName'), "OrderDocumentPath/" . $tOrders->OrderNumber . "/" . $tDocuments->DocumentName, $DocumentPath);
				$ISTEMPFILECREATED = true;
				$DocumentPath = $Temp_DocumentPath;
			}
		}
		$statusdata = array(
			'AutoExportStatus'	=>	'7'
		);
		$update = $this->Common_Model->updateOrderStatus($tOrders->OrderUID,$statusdata);
		$DEFAULT_URL = FCPATH . $DocumentPath;
		$DestinationPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/files/';
		$DocumentPages = $this->GetPageNos($OrderUID, $tOrders);
		$DocumentTypePDF_Array = $DocumentPages['DocumentTypePDF_Array'];
		$files_to_upload = array();
		echo "get all pdf page number array <br>";
		$pages = $DocumentPages['pages'];
		// Remove Directory
		if (file_exists($DestinationPath)) 
		{
			echo "deleting previous zip <br>";
			$dir_handle = opendir($DestinationPath);
			while($file = readdir($dir_handle)) 
			{
				if ($file != "." && $file != "..") 
				{
					if (!is_dir($DestinationPath."/".$file)){
						unlink($DestinationPath."/".$file);
					}
					else
						delete_directory($DestinationPath.'/'.$file);
				}
			}
			closedir($dir_handle);
			rmdir($DestinationPath);
		}
		$this->Common_Model->CreateDirectoryToPath($DestinationPath);
		foreach ($DocumentTypePDF_Array as $CategoryName => $DocumentType) {
			foreach ($DocumentType as $DocumentTypeName => $PageNos) {
				$Relative_Destination_Path = 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/files/';
				if(strpos($DocumentTypeName,'.pdf') == false)
					$DocumentTypeName =$DocumentTypeName.'.pdf';
				$this->CreateDocumentTypePDF($DEFAULT_URL, $Relative_Destination_Path, $CategoryName, $DocumentTypeName, $PageNos, $tOrders->OrderNumber, $tOrders->LoanNumber);
			}
		}
		$this->Common_Model->UpdateExportedStatus($tOrders);
		$SourcePath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/'.$tOrders->LoanNumber .'/';
		if (file_exists($DestinationPath)) 
		{
			$dir_handle = opendir($DestinationPath);
			while($file = readdir($dir_handle)) 
			{
				if ($file != "." && $file != "..") 
				{
					if (!is_dir($DestinationPath."/".$file)){
						rename($DestinationPath."/".$file,$DestinationPath."/".$tOrders->LoanNumber.'_'.$file);
						$files_to_upload[] = $tOrders->LoanNumber.'_'.$file;
					}
				}
			}
			closedir($dir_handle);
		}
		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $tOrders->ProjectUID]);
		$mSFTP = $this->Common_Model->get_row('mSFTP', ['mSFTP.SFTPUID'=>$mProjectCustomer->SFTPExportUID]);
		if($mSFTP->SFTPProtocol == 'FTP')
		{
			$this->load->library('ftp');
			$config['hostname'] =  $mSFTP->SFTPHost;
			$config['username'] =  $mSFTP->SFTPUser;
			$config['password'] =  $mSFTP->SFTPPassword;
			$config['debug']    = TRUE;
			$config['port']    = $mSFTP->SFTPPort;
			$SFTPPath = $mSFTP->SFTPPath.'/'.$file;
			$config['debug'] = False;

			$locpath=FCPATH . $data['DocumentURL'];
			
			if($this->ftp->connect($config))
			{
				if($this->ftp->upload($localFile, $SFTPPath, 'auto', 0775)){
					$this->ftp->close();
					
				}else{
					$this->ftp->close();
				}
			}

		}
		if($mSFTP->SFTPProtocol == 'SFTP')
		{
			$host 		=  $mSFTP->SFTPHost;
			$username 	=  $mSFTP->SFTPUser;
			$password 	=  $mSFTP->SFTPPassword;
			$debug    	= TRUE;
			$port    	= $mSFTP->SFTPPort;
			$SFTPPath 	= $mSFTP->SFTPPath.'/'.$tOrders->LoanNumber;
			$connection = ssh2_connect($host, $port);
			ssh2_auth_password($connection, $username, $password);
			$sftp = ssh2_sftp($connection);
			ssh2_sftp_mkdir($sftp, $SFTPPath);
			if(!empty($files_to_upload))
			{
				foreach($files_to_upload as $file)
				{
					$remoteFile = $SFTPPath.'/'.$file;
					$localFile = $DestinationPath.'/'.$file;
					$stream = fopen("ssh2.sftp://".intval($sftp)."$remoteFile", 'w');
					$uploadFile = file_get_contents($localFile);
					fwrite($stream, $uploadFile);
					fclose($stream);
				}
			}
		}
		
		$data = array(
			'StatusUID'					=>	$this->config->item('keywords')['Completed'],
			'AutoExportStatus'			=>	'0',
			'OrderCompleteDateTime'		=>	date('Y-m-d H:i:s')
		);
		echo "updating order status <br>";
		$this->Common_Model->updateOrderData($data,$tOrders->OrderUID);
		if (file_exists($DestinationPath)) 
		{
			echo "deleting previous zip <br>";
			$dir_handle = opendir($DestinationPath);
			while($file = readdir($dir_handle)) 
			{
				if ($file != "." && $file != "..") 
				{
					if (!is_dir($DestinationPath."/".$file)){
						unlink($DestinationPath."/".$file);
					}
					else
						delete_directory($DestinationPath.'/'.$file);
				}
			}
			closedir($dir_handle);
			rmdir($DestinationPath);
		}
	}


}
?>
