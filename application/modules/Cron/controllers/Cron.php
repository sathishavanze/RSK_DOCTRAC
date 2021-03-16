<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends MX_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('Cron_model');
		$this->load->model('Common_Model');
		$this->load->model('Orderentry/Orderentrymodel');
		$this->load->model('OrderComplete/OrderComplete_Model');

	}

	public function MoveCompletedFiles2S3()
	{
		$Days = 7;
		$CompletedOrders = $this->Cron_model->GetCompletedOrdersBefore($Days);

		$ordercount = 1;
		foreach ($CompletedOrders as $key => $value) {
			$src = FCPATH . 'uploads/OrderDocumentPath/' . $value->OrderNumber;
			$dest = FCPATH . 'S3_uploads/OrderDocumentPath/' . $value->OrderNumber;

			if (file_exists($src)) {

				shell_exec("sudo mv ". $src . " " . $dest);

				$this->Cron_model->UpdateMovedStatus($value->OrderUID);

				echo $value->OrderNumber ." Order Moved From ". $src . " To " . $dest ."<br/>";

				$this->CreateDirectoryToPath($src);

				echo $src . " Created";
			// $this->rcopy($src, $dest);
				echo $value->OrderNumber .' Order Moved </br>';

			}
			else{
				echo $value->OrderNumber ." Folder Not Available/Moved <br/>";
			}
			$ordercount++;
		}

	}


	public function DownloadPDF($OrderUID)
	{

		$ISTEMPFILECREATED = false;
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);


		$DocumentPath = $this->Common_Model->get_row('tDocuments', ['OrderUID'=>$OrderUID, 'IsStacking'=>1])->DocumentURL;
		if (!file_exists($DocumentPath)) {
			$Temp_DocumentPath = str_replace('uploads/', 'S3_uploads/', $DocumentPath);

			if (file_exists($Temp_DocumentPath)) {
				$create_temp_file = file_put_contents($DocumentPath, file_get_contents($Temp_DocumentPath));
				$ISTEMPFILECREATED = true;
			}
		}




		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

		$this->load->helper("file"); // load the helper

		$DEFAULT_URL = FCPATH . $DocumentPath;


		// $DestinationPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/Content/';


		// $file =  FCPATH . 'uploads/OrderDocumentPath/'.$tOrders->OrderNumber . '/' .$tOrders->OrderNumber . '_Stacked.pdf';

		// if (!file_exists($file)) {
		// 	$file = FCPATH . 'S3_uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' .$tOrders->OrderNumber . '_Stacked.pdf';
		// }

		// if (!file_exists($file)) {
		// 	return false; exit;
		// }


		$DocumentPages = $this->GetPageNos($OrderUID, $tOrders);
		$DocumentTypePDF_Array = $DocumentPages['DocumentTypePDF_Array'];
		$pages = $DocumentPages['pages'];
		
		$temp_OutputPath = FCPATH . 'uploads/OrderDocumentPath/'.$tOrders->OrderNumber . '/' .$tOrders->OrderNumber . '_temp_Stacked.pdf';

		$main = "pdftk " . $DEFAULT_URL . " cat " . $pages . " output " . $temp_OutputPath . "";


		$output = shell_exec("pdftk ".$DEFAULT_URL." cat ".$pages." output " .$temp_OutputPath. "");



		$catname = '';
		$bookmark_content = "";
		$bookmarks_sql = $this->db->query("SELECT DISTINCT SubCategoryName FROM tPage WHERE OrderUID = '".$OrderUID."' ORDER BY PagePosition")->result_array();
		foreach ($bookmarks_sql  as $key=>$bookmarks_fetch) :

			$SubCategoryName = $bookmarks_fetch['SubCategoryName'];

			$tPagesql = $this->db->query("SELECT * FROM tPage WHERE OrderUID = '" . $OrderUID . "' AND SubCategoryName = '$SubCategoryName' LIMIT 1")->result_array();

			foreach ($tPagesql as $key=> $fetch) :

				$mCategory = $this->Common_Model->get_row('mCategory', ['HashCode'=>$fetch['CategoryName']]);
				$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['HashCode'=>$fetch['SubCategoryName']]);
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
		file_put_contents($bookmark_txt_filename, $bookmark_content);

		$OutputPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/' . $tOrders->OrderNumber . '_Stacked.pdf';
		shell_exec("pdftk ".$temp_OutputPath." update_info ". $bookmark_txt_filename ." output " . $OutputPath . "");


		if (file_exists($temp_OutputPath)) {
			unlink($temp_OutputPath);
		}    
		if (file_exists($bookmark_txt_filename)) {
			unlink($bookmark_txt_filename);
		} 

		// Update in DB
		$this->Common_Model->UpdateExportedStatus($tOrders);
		$path = $OutputPath;
		$filename = ($tOrders->LoanNumber != '' ? $tOrders->LoanNumber .'_Stacked.pdf' : $tOrders->OrderNumber.'_Stacked.pdf');
		header('Content-Transfer-Encoding: binary');  
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
		header('Accept-Ranges: bytes');  
		header('Content-Length: ' . filesize($path));  
		header('Content-Encoding: none');
		header('Content-Type: application/pdf');  
		header('Content-Disposition: attachment; filename=' . $filename);
		readfile($path);

		// Delete Creted Single PDF
		if (file_exists($path)) {
			unlink($path);
		} 

		if ($ISTEMPFILECREATED && file_exists($DocumentPath)) {
			unlink($DocumentPath);
		}
		exit;

	}

	public function DownloadZip($OrderUID)
	{

		$this->load->library('zip');
		$this->load->helper("file"); // load the helper

		$ISTEMPFILECREATED = false;
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);


		$DocumentPath = $this->Common_Model->get_row('tDocuments', ['OrderUID'=>$OrderUID, 'IsStacking'=>1])->DocumentURL;
		if (!file_exists($DocumentPath)) {
			// $DocumentPath = str_replace('uploads/', 'S3_uploads/', $DocumentPath);
			$Temp_DocumentPath = str_replace('uploads/', 'S3_uploads/', $DocumentPath);

			if (file_exists($Temp_DocumentPath)) {
				$create_temp_file = file_put_contents($DocumentPath, file_get_contents($Temp_DocumentPath));
				$ISTEMPFILECREATED = true;
			}
		}


		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		

		$DEFAULT_URL = FCPATH . $DocumentPath;


		$DestinationPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/Content/';

		$DocumentPages = $this->GetPageNos($OrderUID, $tOrders);
		$DocumentTypePDF_Array = $DocumentPages['DocumentTypePDF_Array'];
		$pages = $DocumentPages['pages'];

		// Remove Directory
		if (file_exists($DestinationPath)) {
		// rmdir($DestinationPath);
		delete_files($DestinationPath, true); // delete all files/folders
	}
	
	
	foreach ($DocumentTypePDF_Array as $CategoryName => $DocumentType) {
		foreach ($DocumentType as $DocumentTypeName => $PageNos) {

			$Relative_Destination_Path = 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/Content/';
			$this->CreateDocumentTypePDF($DEFAULT_URL, $Relative_Destination_Path, $CategoryName, $DocumentTypeName, $PageNos, $tOrders->OrderNumber, $tOrders->LoanNumber);
		}
	}

	$this->Common_Model->UpdateExportedStatus($tOrders);
	$SourcePath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/Content/';
	if (!file_exists($SourcePath)) {
		$SourcePath = FCPATH . 'S3_uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/Content/';
	}
	$DestinationPath = FCPATH . 'uploads/OrderDocumentPath/' . $tOrders->OrderNumber . '/';

		$folder_in_zip = "/"; //root directory of the new zip file

		
		// $directories = scandir($SourcePath);
		// foreach ($directories as $key => $value) {
		// 	if (is_dir($SourcePath . $value)) {
		// 		// $this->zip->clear_data();
		// 		$this->zip->read_dir($SourcePath . $value, FALSE);
		// 	}
		// }
		$this->zip->read_dir($SourcePath, FALSE);

		delete_files($SourcePath, true); // delete all files/folders
		rmdir($SourcePath);


		$command = shell_exec("rm -rf " . $SourcePath);


		/* UNLINK TEMP CREATED FILE */
		if ($ISTEMPFILECREATED && file_exists($DocumentPath)) {
			unlink($DocumentPath);
		}

		$filename = ($tOrders->LoanNumber != '' ? $tOrders->LoanNumber .'_Stacked.zip' : $tOrders->OrderNumber.'_Stacked.zip');

		$this->zip->archive($DestinationPath . $filename);


		return $DestinationPath . $filename;
		// Remove Directory
		// if (file_exists($SourcePath)) {
		// 	// echo $SourcePath;
		// }


	}

	function CreateDocumentTypePDF($SourcePath, $DestinationPath, $CategoryName, $DocumentTypeName, $Pages, $OrderNumber, $LoanNumber)
	{

		// $DestinationPath = $DestinationPath;
		// $DestinationPath = $DestinationPath;
		// echo $DestinationPath;
		$prefix = $LoanNumber != '' ? $LoanNumber : $OrderNumber;
		if (!file_exists(FCPATH . $DestinationPath)) {
			if (!mkdir(FCPATH . $DestinationPath, 0777, true)) {
				die('Unable to Create Specifed Directory');
			}
		}
		$CategoryName = $this->clean($CategoryName);
		$DocumentTypeName = $this->clean($DocumentTypeName);

		if (!file_exists(FCPATH . $DestinationPath . $this->clean($prefix) . '_' . $this->clean($CategoryName))) {
			// if (!mkdir(FCPATH . $DestinationPath . $this->clean($prefix) . '_' . $this->clean($CategoryName), 0777, true)) {
			// 	die('Unable to Create Specifed Directory');
			// }
		}

		// if (!file_exists(FCPATH . $this->clean($DestinationPath . $CategoryName . '/' . $DocumentTypeName))) {
		// 	if (!mkdir(FCPATH . $this->clean($DestinationPath . $CategoryName . '/' . $DocumentTypeName), 0777, true)) {
		// 		die('Unable to Create Specifed Directory');
		// 	}
		// }
		
		$OutputPath = FCPATH . $DestinationPath;
		$OutputPath = $OutputPath . $this->clean($prefix) . '_' . $this->clean($DocumentTypeName) .'.pdf';

		$additional_commands = 'setlocal enabledelayedexpansion FOR /F %%A IN (\' dir / B / ON * . pdf \') DO (set command=!command! "%%A")';
		$output = shell_exec("pdftk " . $SourcePath . " cat " . $Pages . " output \"" . $OutputPath . "\"");

		$categorypdfs = "pdftk " . $SourcePath . " cat " . $Pages . " output \"" . $OutputPath . "\"";
		return true;
	}


	function clean($string)
	{
		$string = preg_replace('/[\/\\\\]/', '_OR_', $string); // Replaces all spaces with hyphens.
		
		return preg_replace('/[\/\\\\*|$":?]/', '_', $string); // Removes special chars.
	}

	function GetPageNos($OrderUID, $tOrders)
	{

		$mProjectCustomer = $this->Common_Model->get_row('mProjectCustomer', ['ProjectUID' => $tOrders->ProjectUID]);
		$pages = "";
		$DocumentTypePDF_Array=[];

		$categorysql = $this->db->query("SELECT * FROM tCategory WHERE OrderUID = '" . $OrderUID . "' Group BY CategoryName Order BY tCategoryUID")->result_array();
		foreach ($categorysql as $key => $category_fetch) :
			$CategoryName = $category_fetch['CategoryName'];
			$CategoryPosition = $category_fetch['CategoryPosition'];
			$CategoryPages = '';
			
			$subcategorysql = $this->db->query("SELECT * FROM tSubCategory WHERE ParentCategoryName = '$CategoryName' AND OrderUID = '" . $OrderUID . "' Group BY SubCategoryName Order BY tSubCategoryUID")->result_array();
			foreach ($subcategorysql as $key => $subcategory_fetch) :
				$SubCategoryName = $subcategory_fetch['SubCategoryName'];
				$SubCategoryPosition = $subcategory_fetch['SubCategoryPosition'];
				$SubCategoryPages = '';
				
				
				$pagesql = $this->db->query("SELECT * FROM tPage WHERE CategoryName = '$CategoryName' AND SubCategoryName = '$SubCategoryName' AND OrderUID = '" . $OrderUID . "'")->result_array();
				
				foreach ($pagesql as $key => $page_fetch) :
					$PageNo = $page_fetch['PageNo'];
					$PagePosition = $page_fetch['PagePosition'];
					
					$pages .= $PageNo . ' ';
					$SubCategoryPages .= $PageNo . ' ';
				endforeach;
				
				if ($mProjectCustomer->ExportLevel == 'SubCategory' && !empty($SubCategoryPages)) {
					
					$mCategory = $this->Common_Model->get_row('mCategory', ['HashCode' => $CategoryName]);
					$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['HashCode' => $SubCategoryName]);
					
					$DocumentTypePDF_Array[$mCategory->CategoryName][$mDocumentType->DocumentTypeName] = $SubCategoryPages;
					
				}
				
				$CategoryPages .= $SubCategoryPages . ' ';
			endforeach;
			

			if ($mProjectCustomer->ExportLevel == 'Category' && !empty($CategoryPages)) {

				$mCategory = $this->Common_Model->get_row('mCategory', ['HashCode' => $CategoryName]);

				$DocumentTypePDF_Array[$mCategory->CategoryName][$mCategory->CategoryName] = $CategoryPages;

			}
			
		endforeach;
		
		return ['pages'=>$pages, 'DocumentTypePDF_Array'=>$DocumentTypePDF_Array];
	}

	function SFTP_UPLOAD($srcFile)
	{
		//Send file via sftp to server

		$srcFile = 'C:\Xampp\htdocs\index.php';
		// echo "init";
		// $this->load->library('Sftp');

		// $config['hostname'] = '18.215.61.83';
		// $config['username'] = 'ubuntu';
		// $config['prikeyfile'] = 'E:\Downloads\putty\stacx.ppk';

		
		// echo "before";
		// $this->sftp->connect($config);

		// $this->sftp->upload('C:\Xampp\htdocs\index.php', '/var/www/html/stacx.stage/uploads/index.php', 'ascii', 0644);

		// $this->sftp->close();

		// echo "Moved"; exit;
		// echo '<pre>'; print_r($this->Sftp);exit();

		$mOrganizations = $this->Common_Model->get_row('mOrganizations', ['OrganizationUID'=>1]);
		if (file_exists($srcFile)) {
			

			// $strServer = $mOrganizations->SFTPSERVER;
			// $strServerPort = $mOrganizations->SFTPPort;
			// $strServerUsername = $mOrganizations->SFTPUserName;
			// $strServerPassword = $mOrganizations->SFTPPassword;
			// $SFTPPath = $mOrganizations->SFTPPath;

			$strServer = 'home571742109.1and1-data.host';
			$strServerPort = '';
			$strServerUsername = 'u80548530-boopathi';
			$strServerPassword = 'Boop@2124';
			$SFTPPath = '/temp';
			$csv_filename = "Test_File.csv";

			$filename = basename($srcFile);
		//connect to server
			$resConnection = ssh2_connect($strServer, $strServerPort);

			if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)){
			//Initialize SFTP subsystem

			// $resSFTP = ssh2_sftp($resConnection);

				ssh2_scp_send($resConnection, $srcFile, $SFTPPath . $filename, 0644);

			// $writtenBytes = stream_copy_to_stream($srcFile, $resFile);
				ssh2_exec($resConnection, 'exit');
			}else{
				echo "Unable to authenticate on server";
			}
		}
		else{
			echo "Inappropriate Data";
		}
	}



	function ftp_transfer()
	{
		//Send file via sftp to server

		$this->load->library('ftp');
		$this->load->library('email');
		$this->config->load('email', FALSE, TRUE);

		$mOrganization = $this->Common_Model->get_row('mOrganization', ['OrganizationUID'=>1]);
		$mEmailTemplate = $this->Common_Model->get_row('mEmailTemplate', ['EmailTemplateUID'=>1]);

		$mProjectCustomer = $this->Common_Model->get('mProjectCustomer', ['Active'=>1]);


		foreach ($mProjectCustomer as $key => $project) {
			
			if (empty($project)) {
				continue;
			}

			$mSFTP_EmailTemplate = $this->Cron_model->SFTP_Email($project->SFTPUID);


			if (empty($mSFTP_EmailTemplate)) {
				echo "No SFTP Available";
			}

			$config['hostname'] =  $mSFTP_EmailTemplate->SFTPHost;
			$config['username'] =  $mSFTP_EmailTemplate->SFTPUser;
			$config['password'] =  $mSFTP_EmailTemplate->SFTPPassword;
			$config['debug']    = TRUE;
			$SFTPPath = $mSFTP_EmailTemplate->SFTPPath;

			$data = [];

			if(!empty($SFTPPath) && $this->ftp->connect($config))
			{

				echo "FTP Connected <br/>";

				$data[] = array('Prop No','Client','Project Name','Loan No','Property Address','Property City','Property State','Zip Code','OrderEntryDateTime', 'FileName','FilePath');

				$ExportOrders = $this->Cron_model->GetExportOrders($project->ProjectUID);
				// echo "<pre>"; print_r($ExportOrders);
				foreach ($ExportOrders as $key => $value) {

					$srcFile = $this->DownloadZip($value->OrderUID);
					echo "Moving Order " . $value->OrderNumber . " and File " . $srcFile . "<br/>";
					$filename = basename($srcFile);

					if (file_exists($srcFile)) {				

						$UploadFile = $SFTPPath . DIRECTORY_SEPARATOR . $filename;
						$this->ftp->upload($srcFile, $UploadFile);

						$data[] = array($value->OrderNumber,$value->CustomerName,$value->ProjectName,$value->LoanNumber,$value->PropertyAddress1.$value->PropertyAddress2,$value->PropertyCityName,$value->PropertyStateCode,$value->PropertyZipCode,$value->OrderEntryDateTime, $filename, $UploadFile);


						$tOrders['IsAutoExport'] = 1;
						$tOrders['AutoExportDateTime'] = date('Y-m-d H:i:s');
						$this->Common_Model->save('tOrders', $tOrders, ['OrderUID'=>$value->OrderUID]);
						echo "File " . $srcFile . " Moved <br/>";

						unlink($srcFile);
					}
				}
				$this->ftp->close();


				$excelfile = $this->outputCSV($data);

				if (!empty($mOrganization) && !empty($mSFTP_EmailTemplate)) {

					$config = Array(
						'protocol' => 'smtp',
						'smtp_host' => $mOrganization->SMTPHost,
						'smtp_port' => $mOrganization->SMTPPort,
						'smtp_user' => $mOrganization->SMTPUserName,
						'smtp_pass' => $mOrganization->SMTPPassword,
						'mailtype'  => 'html', 
						'charset'   => 'iso-8859-1'
					);


					$this->email->initialize($config);

					$this->email->from($mOrganization->SMTPUserName);

					if (!empty($mSFTP_EmailTemplate->ToMailID)) {
						$toemails = explode(";", $mSFTP_EmailTemplate->ToMailID);
						foreach ($toemails as $key => $value) {
							$this->email->to($value);
						}
					}

					if (!empty($mSFTP_EmailTemplate->BCCMailID)) {
						$bcc_emails = explode(";", $mSFTP_EmailTemplate->BCCMailID);
						foreach ($bcc_emails as $key => $value) {
							$this->email->bcc($value);
						}
					}

					if (!empty($mSFTP_EmailTemplate->Subject)) {
						$this->email->subject($mSFTP_EmailTemplate->Subject);
					}

					if (!empty($mSFTP_EmailTemplate->Body)) {
						$this->email->message($mSFTP_EmailTemplate->Body);
					}

					if (!empty($excelfile)) {
						$this->email->attach($excelfile, 'attachment','ExportedList.csv','text/csv');
					}


					if (!$this->email->send()) {
						echo "Unable to Send Email <br/>";
						echo $this->email->print_debugger();
					}
					else{
						echo "Mail Send Successfully";
					}
				}

			}
			else{
				echo "Unable to connect to FTP";
			}
		}
	}


	function outputCSV($data) 
	{
		ob_start();

		$output = fopen("php://output", "w");
		foreach ($data as $row)
		{
	    	// here you can change delimiter/enclosure
			fputcsv($output, $row); 
		}
		fclose($output);
		$xlsData = ob_get_contents(); 
		ob_end_clean();

		return $xlsData;
	}


	function CreateDirectoryToPath($Path = '')
	{
		if (!file_exists($Path)) {
			if (!mkdir($Path, 0777, true)) die('Unable to create directory');
		}
		chmod($Path, 0777);
	}


	/**
	* send_parkingremainder
	* Email Remainder for the parked welcome call orders
	* .
	*
	* @added date 26.09.2019
	* No Params Required
	*/

	function send_parkingremainder()
	{
		$rows = $this->Cron_model->get_parkingqueue_notifications();

		if(!empty($rows)) {
			$this->load->library('email');
			$this->config->load('email', FALSE, TRUE);

			$mOrganization = $this->Common_Model->get_row('mOrganization', ['OrganizationUID'=>1]);

			if (!empty($mOrganization)) {


				$config = Array(
					'protocol' => 'smtp',
					'smtp_host' => $mOrganization->SMTPHost,
					'smtp_port' => $mOrganization->SMTPPort,
					'smtp_user' => $mOrganization->SMTPUserName,
					'smtp_pass' => $mOrganization->SMTPPassword,
					'mailtype'  => 'html', 
					'charset'   => 'iso-8859-1'
				);

				$this->email->initialize($config);

				foreach ($rows as $key => $row) {
					if($row->IsRemainderSend == 0 && $row) {

						$this->email->from($mOrganization->SMTPUserName);
						$this->email->to($row->EmailID);
						$this->email->subject('Welcome Call Reminder');
						$this->email->message('Welcome Call Reminder Notification for the Order'.$row->OrderNumber);
						if (!$this->email->send()) {
							echo "Unable to Send Email <br/>";
							echo $this->email->print_debugger();
							$this->Cron_model->update_remindedparking($row->ParkingUID);
						}
						else{
							echo "Mail Send Successfully";
						}

					}
				}

			} else {
				echo "unable to connect smtp";
			}

		} else {
			echo  "No Records";
		}

	}

	//Reset parking orders --assigned users will be unassigned
	function unassignusers_parkingqueue()
	{		
		$ParkingOrders = $this->Cron_model->get_parkingorderstype_uncleared();
		$currenttime = date('Y-m-d H:i:s');
		if(!empty($ParkingOrders)) {
			foreach ($ParkingOrders as $ParkingOrder) {
				
				if(!empty($ParkingOrder->OrderUID)) {
					//remainder expired
					if ($currenttime > $ParkingOrder->Remainder) {
						echo 'ParkingUID - '.$ParkingOrder->ParkingUID.' Processed <hr>';
						$data['IsCleared'] = 1;
						$data['ClearedByUserUID'] = $this->session->userdata('UserUID');
						$data['ClearedDateTime'] = date('Y-m-d H:i:s');
						$this->db->where('tOrderParking.ParkingUID',$ParkingOrder->ParkingUID);
						$this->db->update('tOrderParking',$data);

						//unassign users
						$res['AssignedToUserUID'] = null;
						$res['AssignedDatetime'] = null;
						$res['AssignedByUserUID'] = null;
						$this->db->where('tOrderAssignments.OrderUID',$ParkingOrder->OrderUID);
						$this->db->where('tOrderAssignments.WorkflowModuleUID',$ParkingOrder->WorkflowModuleUID);
						$this->db->update('tOrderAssignments',$res);

						//clear torderdocchase
						$docchasedata['AssignedToUserUID'] = null;
						$docchasedata['AssignedDateTime'] = null;
						$this->db->where('tOrderDocChase.OrderUID',$ParkingOrder->OrderUID);
						$this->db->where('tOrderDocChase.WorkflowModuleUID',$ParkingOrder->WorkflowModuleUID);
						$this->db->update('tOrderDocChase',$docchasedata);

						/*INSERT ORDER LOGS BEGIN*/
						$this->Common_Model->OrderLogsHistory($ParkingOrder->OrderUID,$ParkingOrder->WorkflowModuleName.' - Parking Cleared',Date('Y-m-d H:i:s'));
						/*INSERT ORDER LOGS END*/
					}
				}
			}
		}
		echo 'completed';
	}

	/**
	*Function CRON FUNCTION FOR PARKING ORDERS TO NORMAL Queue
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 30 March 2020
	*/
	function parkingtoqueue()
	{
		$ParkingOrders = $this->Cron_model->get_parkingorders_uncleared_crossedremainder();
		$currenttime = date('Y-m-d H:i:s');
		if(!empty($ParkingOrders)) {
			foreach ($ParkingOrders as $ParkingOrder) {

				if(!empty($ParkingOrder->OrderUID)) {
						//remainder expired
					if ($currenttime > $ParkingOrder->Remainder) {
						echo 'ParkingUID - '.$ParkingOrder->ParkingUID.'  '.$ParkingOrder->WorkflowModuleName.' Processed <hr>';
						$data['IsCleared'] = 1;
						$data['ClearedByUserUID'] = $this->session->userdata('UserUID');
						$data['ClearedDateTime'] = date('Y-m-d H:i:s');
						$this->db->where('tOrderParking.ParkingUID',$ParkingOrder->ParkingUID);
						$this->db->update('tOrderParking',$data);

						/*INSERT ORDER LOGS BEGIN*/
						$this->Common_Model->OrderLogsHistory($ParkingOrder->OrderUID,$ParkingOrder->WorkflowModuleName.' - Parking Cleared',Date('Y-m-d H:i:s'));
						/*INSERT ORDER LOGS END*/
					}
				}
			}
		}
		echo 'completed';
	}
	/**
	*function to get HOI Bot excel sheet header  
	*@param None
	*
	*@author Santhiya M <santhiya.m@avanzegroup.com>
	*@since June 17 2020
	*
	*@return Sheet header array
	*/
	function GetSheetList(){
		$head = array('Agency Name' => 'string',
			'Insurance Company' => 'string',
			'Enquiry Type' => 'string',
			'Request Type' => 'string',
			'Proof of Coverage' => 'string', 
			'Extended Replacement Cost Percentage' => 'string', 
			'Wind and Hail Deductible/Coverage' => 'string',
			'Mortgage Add, Change or Correction' => 'string', 
			'Mortgage Position' => 'string', 
			'Mortgage Option' => 'string', 
			'Mortgagee Name' => 'string', 
			'Mortgage Company Name Cont' => 'string', 
			'Mortgagee Address' => 'string', 
			'Mortgage Unit Number' => 'string', 
			'Mortgage City' => 'string', 
			'Mortgage State' => 'string', 
			'Mortgage Zip Code' => 'string', 
			'Effective Date' => 'string', 
			'Loan Number' => 'string', 
			'Loan Amount' => 'string', 
			'Escrowed' => 'string', 
			'Policy Number' => 'string', 
			'First Insured Name' => 'string', 
			'Second Insured Name' => 'string', 
			'Property Address' => 'string', 
			'Property City' => 'string', 
			'Property State' => 'string', 
			'Property Zip Code' => 'string', 
			'Property Unit Number' => 'string',
			'Property APT' => 'string',
			'Registered Owner' => 'string', 
			'Mortgage, Title or Attorney Name' => 'string', 
			'Mortgage, Title or Attorney Company Name' => 'string', 
			'Mortgage, Title or Attorney Company Phone Number' => 'string', 
			'Company Email' => 'string', 
			'Company Fax' => 'string', 
			'Date of Closing' => 'string', 
			'Notes' => 'string', 
			'Preferred Delivery Method' => 'string', 
			'Return Name' => 'string', 
			'Return Email' => 'string', 
			'Return Fax No' => 'string', 
			'Require an Invoice/Receipt' => 'string',
			'State Name Property' => 'string',
			'State Name Mortgage' => 'string',
			'Order Number' => 'string',
			'Request Number' => 'string',
			// 'Residence' => 'string',
			'Username' => 'string',
			'Password' => 'string',
			'Mortgage Code' => 'string',
			'Line Of Business' => 'string',
			'Who Pays' => 'string',
			);
		
		return $head;
	}
	/**
	*function to get array of sheet row  
	*@param OrderUID
	*
	*@author Santhiya M <santhiya.m@avanzegroup.com>
	*@since June 17 2020
	*
	*@return Excel sheet row array 
	*/
	function GetBotLists($OrderUID){
		$mort = array();
		/* Get required params from tOrders, tOrderImport & tOrderPropertyRole */
		$tOrder = $this->Common_Model->GetExportDetails($OrderUID); 
		/* Get Company Details using Company Name */
		//$getCompanyDetails = $this->Common_Model->getCompanyDetails($tOrder->InsuranceCompany); 
		/* Hoi company / Mortgage details */
		$mCustomer = $this->Common_Model->get_row('mCustomer', ['CustomerUID' => ($this->config->item('HOI_Customer'))]);
		/* Get State Name using Code */
		$MortageState = $this->Common_Model->get_row('mStates', ['StateCode' => ($mCustomer->StateName) ]);
		
		$getCompanyDetails = $this->Cron_model->get_mCompanyDetails($tOrder->InsuranceCompany); 
		// echo '<pre>';print_r($mCustomer);exit;
		
		$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['ContactNo'];
		$ContactNo = $this->Cron_model->getOrderChecklist($DocumentTypeUID, $tOrder->OrderUID);
		$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['Email'];
		$Email = $this->Cron_model->getOrderChecklist($DocumentTypeUID, $tOrder->OrderUID);
		$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['FaxNo'];
		$FaxNo = $this->Cron_model->getOrderChecklist($DocumentTypeUID, $tOrder->OrderUID);

		$mort[] =  $tOrder->AgencyName;
		$mort[] =  $tOrder->InsuranceCompany;
		$mort[] =  $tOrder->EnquireType;
		$mort[] =  $tOrder->RequestType;
		$mort[] =  $tOrder->ProofOfCoverage;
		$mort[] =  $tOrder->ExtendedReplacementCostPercentage;
		$mort[] =  $tOrder->WindHailDeductibleCoverage;
		$mort[] =  $tOrder->MortgageAddChangeCorrection;
		$mort[] =  $tOrder->MortgagePosition;
		$mort[] =  $tOrder->MortgageOption; // Mortgage Option / Select type in All state
		$mort[] =  $mCustomer->CustomerName;//mortgage name
		$mort[] =  $mCustomer->CustomerCode;//mortgage company name cont
		$mort[] =  $mCustomer->AddressLine1 .' '. $mCustomer->AddressLine2;//mortgage address
		$mort[] =  $mCustomer->MortgageUnit;//mortgage unit number
		$mort[] =  $mCustomer->CityName;//mortgage city
		$mort[] =  $mCustomer->StateName;//mortgage state
		$mort[] =  $mCustomer->ZipCode;//mortgage czip code	
		$mort[] =  date('m/d/yy');
		$mort[] =  $tOrder->LoanNumber;//NSMServicingLoanNumber
		$mort[] =  $tOrder->LoanAmount;
		$mort[] =  $tOrder->Escrowed;
		$mort[] =  $tOrder->PolicyNumber;
		
		$splitNames = explode(',', $tOrder->FirstInsuranceName);
		if(count($splitNames) > 1){
			$mort[] =  $splitNames[0]; //FirstInsuranceName
			$mort[] =  $splitNames[1]; //SecondInsuranceName
		}else{
			$mort[] =  $tOrder->FirstInsuranceName; //FirstInsuranceName;
			$mort[] =  ''; //SecondInsuranceName;
		}
		
		$mort[] =  $tOrder->PropertyAddress1;
		$mort[] =  $tOrder->PropertyCityName;
		$mort[] =  $tOrder->PropertyStateCode;
		$mort[] =  $tOrder->PropertyZipCode;
		$mort[] =  $tOrder->PropertyUnitNumber;
		$mort[] =  $tOrder->PropertyAPT;
		$mort[] =  $tOrder->BorrowerName; // Borrower Name 
		$mort[] =  $mCustomer->CustomerName;
		$mort[] =  $tOrder->InsuranceCompany;
		$mort[] =  $ContactNo->Comments;
		$mort[] =  $Email->Comments;
		$mort[] =  $FaxNo->Comments;
		$mort[] =  date('m/d/yy');
		$mort[] =  $tOrder->OrderComments;
		$PreferredDeliveryMethod = '';
		if($mCustomer->CustomerEmail != ''){
			$PreferredDeliveryMethod = 'By Email';
		} 
		if($mCustomer->FaxNo != ''){
			$PreferredDeliveryMethod = 'By Fax';
		} 

		$mort[] =  $PreferredDeliveryMethod;
		$mort[] =  $mCustomer->CustomerName;
		$mort[] =  $mCustomer->CustomerEmail;
		$mort[] =  $mCustomer->FaxNo;
		$mort[] =  $tOrder->RequireAnInvoiceReceipt;		
		$mort[] =  $tOrder->PropertyCountyName;
		$mort[] =  $MortageState->StateName;
		$mort[] =  $tOrder->OrderNumber;
		$mort[] =  $tOrder->RequestNumber;
		// $mort[] =  $tOrder->Residence;
		$mort[] =  $getCompanyDetails->Username; // Username
		$mort[] =  $getCompanyDetails->Password; // Password
		$mort[] =  $getCompanyDetails->MortgageCode; // Mortgage Code
		$mort[] =  $getCompanyDetails->LineOfBusiness; // Line of Business 
		$mort[] =  $getCompanyDetails->WhoPays; // Who Pays

		return $mort;
	}
	/**
	*Cron function to send email to HOI company  
	*@param None
	*
	*@author Santhiya M <santhiya.m@avanzegroup.com>
	*@since June 2020
	*
	*@return Success or Failure message
	*Modified at Tuesday 14 July 2020
	*Modification reason - Move HOI queues based on response 
	*Added MoveQueue() Common function
	*Modified at Friday 17 July 2020
	*Modification reason - Hoi WebBot updates 
	*/
	function NotifyCompanyHoi() {
		$this->load->library('email');
		$OrderDetails = $this->Common_Model->getEmailsOrders();        
		$HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];
		//echo '<pre>';print_r($OrderDetails);exit;
		if(!empty($OrderDetails)){
			$BotArray = [];
			$OrderUIDS = [];
			$Companies = [];
			foreach ($OrderDetails as $key => $order) { 
				//fetch company details by name
				$getCompanyDetails = $this->Cron_model->get_mCompanyDetails($order->InsuranceCompany); 
				if(empty($getCompanyDetails) && $order->InsuranceCompany == '') {
					/* Move HOI queue to exception */
					
					$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
					$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);
					
					/* Change HOI Order notification flag to notified*/
					$this->Common_Model->NotifiedOrder($order->OrderUID);

					$fieldArray = array('OrderUID' => $order->OrderUID, 'AutomationType'=> 'Email Sent', 'AutomationStatus' => 'Failure', 'EmailUID' => '0', 'CreatedDate' => date('Y-m-d H:i:s'));
					$this->Common_Model->insert_AutomationLog($fieldArray); 

					echo json_encode(array('Status' => 1,'message'=>'No Company found, Loan will be moved to exception Queue','type' => 'danger','OrderUID'=>$order->OrderUID));
				}else{
					/* Start -- Desc: D2TINT-172: Efax Integration, Send Efax  @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 2nd 2020 */
					if($order->Email && $order->Email != ''){
						$email = $order->Email;						
					}else{
						$email = $getCompanyDetails->Email;
					}
					if($order->InsuranceCompany){
						$CompanyName = $order->InsuranceCompany;						
					}else{
						$CompanyName = $getCompanyDetails->CompanyName;
					}
					if($order->Efax && $order->Efax != ''){
						// $FaxNo = $order->Efax;	
						$FaxNo = '13023972114';					
					}else{
						$FaxNo = $getCompanyDetails->FaxNo;
					}


					if($order->WebUrl && $order->WebUrl != ''){
						$web = $order->WebUrl;						
					}else{
						$web = $getCompanyDetails->Website;
					}
					$FolderName = $getCompanyDetails->FolderName;

					if(!empty($web) ){
						// echo '<pre>';print_r($order);exit;
						$OrderUIDS[] = $order->OrderUID;
						$Companies[] = $FolderName;
						$BotArray[] = $this->GetBotLists($order->OrderUID);
					}else if(!empty($FaxNo)){
						$this->load->model('Efax/Efax_model');
						$user = $this->Common_Model->pluckSettingValue('RMUser');
						$subject = 'Mortgagee Clause Change Request - Loan Number : '.$order->LoanNumber;
						/* @desc: Added Cover Message for Fax @author: Yagavi G <yagavi.g@avanzegroup.com> @since: July 22 2020 */
						$msg = $this->config->item('FaxCoverMessage');
						$message = str_replace("<<%%doctrac_order_number%%>>", $order->OrderNumber, $msg);
						$DocumentDetails = $this->Cron_model->get_tDocuments($order->OrderUID);
						if(!empty($DocumentDetails)){
							$OrgDetails = $this->Efax_model->GetEFaxCredentials();
							$fax_columns['destination'] = array('to_name' => $CompanyName, 'to_company' => $CompanyName, 'fax_number' => $FaxNo);
							$fax_columns['cover_page_options'] = array('from_name' => $user, 'subject' => $subject, 'message' => $message);
							$fax_columns['documents'] = $DocumentDetails;

							$fax_details['json_data'] = $fax_columns;
							$fax_details['EventCode'] = 'SendFax';
							$fax_details['OrderUID'] = $order->OrderUID;
							$fax_details['OrderNumber'] = $order->OrderNumber;
							$fax_details['ClientAuthKey'] = $OrgDetails['EFaxToken'];
							$fax_details['ClientUserID'] = $OrgDetails['EFaxUserID'];

							if(!empty($fax_details['ClientUserID'])){
								$fax_list = $this->Efax_model->Func_FaxDetails($fax_details);
								if($fax_list){

									
									$QueueUID = $this->config->item('HoiAutomationQueues')['HOIRequest'];
									$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);
									
									/* Desc: Store response details for Automation Log @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 6th 2020 */
									$fieldArray = array('OrderUID' => $order->OrderUID, 'AutomationType'=> 'eFax Sent', 'AutomationStatus' => 'Success', 'EFaxDataUID' => $fax_list, 'CreatedDate' => date('Y-m-d H:i:s'));
									$this->Common_Model->insert_AutomationLog($fieldArray);

									/* Change HOI Order notification flag to notified*/
									$this->Common_Model->NotifiedOrder($order->OrderUID);

									echo json_encode(array('Status' => 0,'message'=>'Fax Sent Successfully for '.$order->OrderNumber,'type' => 'success','OrderUID'=>$order->OrderUID));
								} else {
									echo json_encode(array('Status' => 1,'message'=>'Error!! Fax Not Sent for '.$order->OrderNumber,'type' => 'danger','OrderUID'=>$order->OrderUID));
								} 
							} else {
								echo json_encode(array('Status' => 1,'message'=>'Error!! Fax Credential is not Available','type' => 'danger','OrderUID'=>$order->OrderUID));
							}
						} else {
							echo json_encode(array('Status' => 1,'message'=>'Error!! No Documents Found in '.$order->OrderNumber,'type' => 'danger','OrderUID'=>$order->OrderUID));
						}
					} else{
						if(empty($email)) {
							
							$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
							$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);
							
							/* Change HOI Order notification flag to notified*/
							$this->Common_Model->NotifiedOrder($order->OrderUID);

							echo json_encode(array('Status' => 1,'message'=>'No email found, Loan will be moved to exception Queue','type' => 'danger','OrderUID'=>$order->OrderUID));

						}else{

							$user = $this->Common_Model->pluckSettingValue('RMUser');
							$from_email = $user; 
							$this->load->library('email'); 
							$this->email->from($from_email);					
							$to_email = $email;
							$subject = $this->Common_Model->pluckSettingValue('MortgageSubject').$order->LoanNumber;
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

							//Attach declaration file in mortagagee email if exist 
							$DocumentDetails = $this->Common_Model->get_row('tDocuments', ['OrderUID' => $order->OrderUID, 'TypeofDocument' => 'OCR_Declaration']);
							if(!empty($DocumentDetails) && file_exists(FCPATH.$DocumentDetails->DocumentURL) ){
								$DeclarationFile = FCPATH.$DocumentDetails->DocumentURL;
								$this->email->attach($DeclarationFile);
							}
							if($this->email->send()){
								$status =  "Success";	
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
								$EmailUID = $this->db->insert_id();	
								
								$QueueUID = $this->config->item('HoiAutomationQueues')['HOIRequest'];
								$this->Common_Model->MoveQueue($order->OrderUID, $QueueUID);
								

								/* Desc: Store response details for Automation Log @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 6th 2020 */
								$fieldArray = array('OrderUID' => $order->OrderUID, 'AutomationType'=> 'Email Sent', 'AutomationStatus' => 'Success', 'EmailUID' => $EmailUID, 'CreatedDate' => date('Y-m-d H:i:s'));
								$this->Common_Model->insert_AutomationLog($fieldArray);

								/* Change HOI Order notification flag to notified*/
								$this->Common_Model->NotifiedOrder($order->OrderUID);
								echo json_encode(array('Status' => 0,'message'=>'Email sent successfully ' . $to_email . '! And the loan will be moved to HOI Request queue.','type' => 'success','OrderUID'=>$order->OrderUID));
								
							}else{

								$fieldArray = array('OrderUID' => $order->OrderUID, 'AutomationType'=> 'Email Sent', 'AutomationStatus' => 'Failure', 'EmailUID' => '0', 'CreatedDate' => date('Y-m-d H:i:s'));
								$this->Common_Model->insert_AutomationLog($fieldArray); 

								$status =  "Failure";
								echo json_encode(array('Status' => 2,'message'=>'Unable to send mail!','type' => 'danger','OrderUID'=>$order->OrderUID));
								
							}
						}
					}
				}
			}
			//echo '<pre>';print_r($BotArray);exit;
			if(!empty($BotArray)){
				$i = 0;
				$key = $this->config->item('HoiBotFolder');
				$uploadCSV = $this->UploadHOIExcel($BotArray, $key);						
				// foreach ($BotArray as $value) {
				// 	// $orderUids = $OrderUIDS[$key];
				// 	if(!empty($value)){						
				// 	}
				// }

				$path = "uploads/BotFiles/" . $key . '/'. $uploadCSV;
				if(file_exists(FCPATH.$path)){

					echo '<pre>'.$uploadCSV.' - Bot file generated';

					$SFTP_upload = $this->UploadToSFTP($key, $uploadCSV);
					if($SFTP_upload == 'done'){								
						$Doc = array('DocumentName' => $uploadCSV, 
							'DocumentURL'=> ($path),
							'IsStacking'=> 8,
							'UploadedByUserUID' => $this->config->item('Cron_UserUID'),
							'TypeofDocument'=> 'BotFile',
							'UploadedDateTime'=> date('Y-m-d H:i:s'),
						);						
						$this->db->insert('tDocuments',$Doc);
						$BOTDocUID = $this->db->insert_id();
						$botlog['BotCompany'] = json_encode($Companies);
						$botlog['OrderUIDs'] = json_encode($OrderUIDS);
						$botlog['RequestSentAt'] = date('Y-m-d H:i:s');
						$botlog['DocumentUID'] = $BOTDocUID;
						$this->Common_Model->save('tBotLogs', $botlog);
						/* Update automaton log for this company OrderUIDs*/
						foreach ($OrderUIDS as $uid) {
							/* Move HOI queue to exception */
							
							$QueueUID = $this->config->item('HoiAutomationQueues')['HOIRequest'];
							$this->Common_Model->MoveQueue($uid, $QueueUID);									

							/* Automation Log */
							$fieldArray = array('OrderUID' => $uid, 'AutomationType'=> 'BOT Sent', 'AutomationStatus' => 'Success', 'CreatedDate' => date('Y-m-d H:i:s'));
							$this->Common_Model->insert_AutomationLog($fieldArray);

							/* Change HOI Order notification flag to notified*/
							$this->Common_Model->NotifiedOrder($uid);
						}
						echo '<pre> Bot created for companies '.json_encode($Companies).' & Order IDS - '.json_encode($OrderUIDS);
					}else{
						/* Update automaton log for this company OrderUIDs */
						foreach ($OrderUIDS as $uid) {
							/* Automation Log */
							$fieldArray = array('OrderUID' => $uid, 'AutomationType'=> 'BOT Sent', 'AutomationStatus' => 'Failure', 'CreatedDate' => date('Y-m-d H:i:s'));
							$this->Common_Model->insert_AutomationLog($fieldArray);
						}
						if($SFTP_upload == 'failed'){
							echo '<pre> Transfer BOT file failed for '.json_encode($Companies);
						}else if($SFTP_upload == 'auth_failed'){
							echo '<pre> Authentication Failed to transfer file to '.json_encode($Companies);
						}else if($SFTP_upload == 'connection_failed'){
							echo '<pre> Connection Failed to transfer file to '.json_encode($Companies);
						}else{

						}
					} 

				}else{
					/* @If Create excel sheet and store failed */
					echo '<pre> No File Created for '.$key;
				}
			}	
		}else{
			echo '<pre>No Orders Found';
		}
		
	}
	/**
	*Function to upload HOI Bot excel sheet 
	*
	*@param ComapnyArray, CompanyName
	*
	*@author Santhiya M <santhiya.m@avanzegroup.com>
	*@since Friday 17 th June 2020
	*
	*@return created filename
	*@since modified 20 June 2020
	*/
	function UploadHOIExcel($datas, $key){
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";

		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');
		$writer = new XLSXWriter();
		$HEADER = $this->GetSheetList();

		$writer->writeSheetHeader($key,$HEADER, $header_style);
		// echo "<pre>";print_r($datas);exit;
		foreach($datas as $data){
			$writer->writeSheetRow($key, array_values($data));
		}

		$filename = $key.'_'.date('Ymd_His').'.xlsx';
		$BotFilePath = FCPATH."uploads/BotFiles/" . $key;
		$this->Common_Model->CreateDirectoryToPath($BotFilePath);
		$writer->writeToFile($BotFilePath.'/'.$filename);



		if(file_exists($BotFilePath .'/'.$filename)){

			//unlink($filename);	  		
			return $filename;
		}else{
			return 0;
		}
		
	}
	function UploadHOICsv($data, $key) {
		ob_start();		
		$filename = $key.'_'.date('Ymd_His').'.csv';
		$output = fopen("php://output", "w");
		fputcsv($output, $this->GetSheetList());
		foreach ($data as $row)
		{
			fputcsv($output, $row); 
		}
		fclose($output);
		$xlsData = ob_get_contents(); 
		ob_end_clean();
		$BotFilePath = FCPATH."uploads/BotFiles/" . $key;
		$this->Common_Model->CreateDirectoryToPath($BotFilePath);
		if(file_put_contents($BotFilePath. '/' .$filename, $xlsData)){	  		
			return $filename;
		}else{
			return false;
		}		
	}
	/**
	*Function to upload Bot File to SFTP folder 
	*
	*@param CompanyName, FileName
	*
	*@author Santhiya M <santhiya.m@avanzegroup.com>
	*@since Saturday 18 June 2020
	*
	*@return Boolean
	*/
	public function UploadToSFTP($CompanyName,$file)
	{
		$localFile = FCPATH."uploads/BotFiles/" . $CompanyName . '/'.$file;
		/* Get SFTP / FTP details for BOT */
		$mSFTP = $this->Common_Model->get_row('mSFTP', ['SFTPUID'=>2]);
		$return = '';
		if($mSFTP->SFTPProtocol == 'FTP')
		{
			$this->load->library('ftp');
			$config['hostname'] =  $mSFTP->SFTPHost;
			$config['username'] =  $mSFTP->SFTPUser;
			$config['password'] =  $mSFTP->SFTPPassword;
			$config['debug']    = TRUE;
			$config['port']    = $mSFTP->SFTPPort;
			$SFTPPath = $mSFTP->SFTPPath.'/'.$CompanyName.'/'.$file;
			$config['debug'] = False;
			
			if($this->ftp->connect($config))
			{
				if($this->ftp->upload($localFile, $SFTPPath, 'auto', 0775)){
					$this->ftp->close();			
					$return = "done";		
				}else{
					$return = "failed";
					$this->ftp->close();
				}
			}else{
				/* @if ftp connection failed */
				$return = 'connection_failed';
			}
		}
		if($mSFTP->SFTPProtocol == 'SFTP')
		{
			$host 		=  $mSFTP->SFTPHost;
			$username 	=  $mSFTP->SFTPUser;
			$password 	=  $mSFTP->SFTPPassword;
			$debug    	= TRUE;
			$port    	= $mSFTP->SFTPPort;
			$SFTPPath = $mSFTP->SFTPPath.'/'.$CompanyName;
			$remoteFile = $mSFTP->SFTPPath.'/'.$CompanyName.'/'.$file;
			if($connection = ssh2_connect($host, 22)){
				ssh2_auth_password($connection, $username, $password);				
				$sftp = ssh2_sftp($connection);
				if( !file_exists ( "ssh2.sftp://".intval($sftp)."$SFTPPath" ) ) {
					mkdir("ssh2.sftp://".intval($sftp)."$SFTPPath");
					// ssh2_sftp_mkdir($sftp, $SFTPPath);
				}				
				$stream = fopen("ssh2.sftp://".intval($sftp)."$remoteFile", 'w');
				$file = file_get_contents($localFile);
				if(fwrite($stream, $file)){
					$return = "done";
				}else{
					$return = "failed";
				}
				fclose($stream);

			}else{
				/* @if sftp connection failed */
				$return = 'connection_failed';
			}
		}
		return $return;
	}
	/**
	*Cron function to gether reply content and attachments from email 
	*
	*@param None
	*
	*@author Santhiya M <santhiya.m@avanzegroup.com>
	*@since June 2020
	*
	*@return Success or Failure message
	*Modified at Tuesday 14 July 2020
	*Modified by Santhiya M <santhiya.m@avanzegroup.com>
	*Modification reason - Move HOI queues based on response 
	*Added MoveQueue() Common function
	*/
	function readMails(){
		require APPPATH . 'third_party/class.imap.php';

		$server = $this->Common_Model->pluckSettingValue('RMServer');
		$ssl = $this->Common_Model->pluckSettingValue('RMSsl');
		$port = $this->Common_Model->pluckSettingValue('RMPort');
		$user = $this->Common_Model->pluckSettingValue('RMUser');
		$pass = $this->Common_Model->pluckSettingValue('RMPass');
		$filter = $this->Common_Model->pluckSettingValue('RMFilter');
		// $conn = imap_open("{".$server.":".$port."/".$ssl."}INBOX", $user, $pass);

		echo "<pre> Server: "; print_r("{".$server.":".$port."/".$ssl."}INBOX");
		echo "<pre> User: "; print_r($user);
		echo "<pre> Password: "; print_r($pass);
		$attachments_dir = 'uploads/' . date('YmdHis') . '/';
		$config = [
			'attachments_dir' => $attachments_dir,
		];
		/* @Initialize Imap library */
		$imap = new Imap($config);
		if ($stream = $imap->connect("{".$server.":".$port."/".$ssl."}INBOX", $user, $pass)) {
			/* @If IMAP Connection Successful */			
			$tEmailImports = $this->Common_Model->GetUnreadEmailLogs(); /* Get email logs of unread email */
			$GetWaitingOrders = $this->Common_Model->GetWaitingOrders();
			// echo "<pre>  GetWaitingOrders";print_r($GetWaitingOrders);exit;
			if(!empty($GetWaitingOrders)){
				/* @ForLoop used to read email using IMAP Start */
				foreach ($GetWaitingOrders as $ks => $orderemail) {		

					$OrderUID = $orderemail->OrderUID;			
					//$tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
					$orderInfo = $this->Common_Model->getOrderDetails($OrderUID);
					// $uniqueid = str_replace('MailRef', 'Reply', $orderemail->RefID);
					$filter1 = 'Body "'.$orderInfo->OrderNumber.'"'; //filter inbox messages for a order
					$filter2 = 'Body "'.$orderInfo->LoanNumber.'"';
					/* Get response and mail receive interval from settings */
					$ResponseInterval = $this->Common_Model->pluckSettingValue('ResponseInterval');
					$ReceiveInterval = $this->Common_Model->pluckSettingValue('ReceiveInterval');		                

					$expire = strtotime($orderemail->MailReceivedDateTime.' + '.$ReceiveInterval);
					$today = time();

					if($today >= $expire) {
						/* Desc: DOC-617 Automation Log for Email receive @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
						$fieldArray = array('OrderUID' => $orderemail->OrderUID, 'AutomationType'=> 'Email Receive', 'AutomationStatus' => 'Failure', 'EmailUID' => $lastEUID, 'CreatedDate' => date('Y-m-d H:i:s'));									
						$this->Common_Model->insert_AutomationLog($fieldArray);
						/* Make mail as read in log */
						$this->Common_Model->ReadEmail($orderemail->EmailUID);
						/* Desc: Desc: Move HOI queue to exception queue if email sent time is more than 48 hours and till there is no response @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 16th 2020 */
						
						$QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
						$this->Common_Model->MoveQueue($OrderUID, $QueueUID);
						
						echo "<pre> Document Waiting Time Expired For ".$orderInfo->LoanNumber.". And Order Moved To Exception Queue";	
					}else{
						
						$inbox = $imap->getMessages('text', 'asc', $filter1);
						if(empty($inbox)){
							$inbox = $imap->getMessages('text', 'asc', $filter2);
						}
						echo "<pre>"; print_r($inbox);
						if(!empty($inbox)){

							/* Loop inbox message found for passed filter */
							foreach ($inbox as $key => $msg_content) {    			

								$msg = $msg_content['message'];
								echo $msg;
								//strripos($msg, $orderemail->RefID) !== false 
								if(strripos($msg, $orderInfo->OrderNumber) !== false || strripos($msg, $orderInfo->LoanNumber) !== false){             	

									/* Maintain Email Log for received inbox concent */
									$from = $msg_content['from'][0]['address'];
									$Elog = array(
										'SenderEmail'=>$from,
										// 'RefID'=>$uniqueid,
										'RecipientEmail'=>$user,
										'EmailSubject'=>$msg_content['subject'],
										'EmailBody'=>$msg,
										'IsReceived'=>'success',
										'OrderUID'=>$orderemail->OrderUID,
										'OrderNumber'=>$orderemail->OrderNumber,
										'AltOrderNumber' => '',
										'IsReply' => '1',
										'MailReceivedDateTime'=> date('Y-m-d H:i:s'),
									);
									$this->db->set($Elog);
									$this->db->insert('tEmailImport');	
									$lastEUID = $this->db->insert_id();		         			
								        //end

									/* @If Message not found */   	    
									echo "<pre> Message Found"; 
									/* Desc: DOC-617 Log for Email receive @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
									$fieldArray = array('OrderUID' => $orderemail->OrderUID, 'AutomationType'=> 'Email Receive', 'AutomationStatus' => 'Success', 'EmailUID' => $lastEUID, 'CreatedDate' => date('Y-m-d H:i:s'));
									$this->Common_Model->insert_AutomationLog($fieldArray);
									/* If message found in inbox change hoi queue to Document waiting */

									$QueueUID = $this->config->item('HoiAutomationQueues')['HOIDocumentWaiting'];
									$this->Common_Model->MoveQueue($OrderUID, $QueueUID);
									

									$OrderDocumentPath = "uploads/OrderDocumentPath/" . $orderInfo->OrderNumber . '/';
									$this->Common_Model->CreateDirectoryToPath($OrderDocumentPath);	                	

									$this->load->library('email');
									$this->config->load('email', FALSE, TRUE);


									$from_email = $user; 
									$to_mail = $orderInfo->CustomerEmail;

									$this->email->from($from_email);				
									$this->email->to($to_mail);

									$subject = $this->Common_Model->pluckSettingValue('DocumentSubject').$orderInfo->OrderNumber;

									$this->email->subject($subject); 
									$docs = array('LoanNumber'=> $orderInfo->LoanNumber,
										'OrderNumber' => $OrderUID,
										'BorrowerName'=> $orderInfo->CustomerName);
									$body = $this->load->view('common/document_mail.php',$docs,TRUE);

									$this->email->message($body); 
									$attachments = $msg_content['attachments'];
									print_r($msg_content);
									$order_attachments = [];
									/* Loop to gether mail attachments start */
									foreach ($attachments as $key => $file) {
										$file = $attachments_dir . $file;
										$filename = basename($file);
										/* Put mail attachment in our order folder */
										if(file_put_contents($OrderDocumentPath . $filename, file_get_contents($file)))
										{								
											$this->email->attach($OrderDocumentPath . $filename);
											
											$Doc = array('DocumentName' => $filename, 
												'DocumentURL'=> ($OrderDocumentPath . $filename),
												'OrderUID'=> $OrderUID,
												'IsStacking'=> 0,
												'TypeofDocument'=> 'Others',
												'UploadedDateTime'=> date('Y-m-d H:i:s'),
											);
											$this->db->insert('tDocuments',$Doc);
											$docUID = $this->db->insert_id();
											/* Change Hoi queue to document received if attachment found */
											
											$QueueUID = $this->config->item('HoiAutomationQueues')['HOIDocumentReceived'];
											$this->Common_Model->MoveQueue($OrderUID, $QueueUID);

											$this->ValidateFinalDoc($OrderDocumentPath . $filename, $OrderUID);
											/* Make mail as read in log */
											// $this->Common_Model->ReadEmail($orderemail->EmailUID);
										}							                
									} /* Loop to gether mail attachments end */

									$this->email->send();
									
									/* Desc: move the email to our saved folder @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 24th 2020 */
									$conn = imap_open("{".$server.":".$port."/".$ssl."}INBOX", $user, $pass);	
									if (imap_mail_move($conn,  $msg_content['uid'], 'Completed', CP_UID)) {
										imap_expunge($conn);
										echo "Mail :" .$msg_content['uid'] . " moved to completed folder <hr>";
									}									
								}
							}
						}else{
							/* @If Reply for sent email not found */
							echo "<pre>No Reply found in ".$user." for ".$filter1 .' '. $filter2. "<hr>";

							/* Desc: Move HOI queue to response waiting queue if email sent time is more than 4 hours  @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020*/
							$expire = strtotime($orderemail->MailReceivedDateTime.' + '.$ResponseInterval);

							if($today >= $expire) {	
								$QueueUID = $this->config->item('HoiAutomationQueues')['HOIResponseWaiting'];
								$this->Common_Model->MoveQueue($OrderUID, $QueueUID);
								echo $orderInfo->LoanNumber." - Order Moved To HOI Response Waiting Queue <hr>";		
							}
						}
						
					}						    

				}		
				
				/* @ForLoop used to read email using IMAP End */
			}else{
				/* @If unread email logs list empty */
				echo "<pre> No unread email";
			}

		}else{
			/* @If IMAP connection failed */
			echo "<pre> IMAP Connection Failed";
		}

	}
	/**
	  * Function Validate Final Loan document
	  * @param Document path, OrderUID
	  *
	  * @author Santhiya M <santhiya.m@avanzegroup.com>
	  * @since 27 July 2020 IST.
	 */
	function ValidateFinalDoc($path, $OrderUID){		
		$tOrder = $this->Common_Model->getOrderDetails($OrderUID);
		require_once (FCPATH  . 'OCR/DoctracCron.php');

		$Document = $this->Common_Model->get_row('tDocuments', ['DocumentURL' => $path]);
		$DocumentUID = $Document->DocumentUID;

		$FilePath = FCPATH . $path;
		chmod($FilePath, 0777); 
		$RequestParams = array(
			"sourceApp"=>"DOCTRAC",
			"orderNo"=>$tOrder->OrderNumber,
			"orderUID"=>$OrderUID, 
			"docType"=> "PDF",
			"source"=> $FilePath,
			"features"=> "DOCUMENT_TEXT_DETECTION",
			"engine"=> "Google Vision",
		);
		$response = run_OCR($RequestParams);

		$Fetch_Request_Details = json_decode($response,true);
		echo '<pre>Fetch_Request_Details ';print_r($Fetch_Request_Details);
		
		if (empty($Fetch_Request_Details)) {			

			$fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> 'Final Document', 'AutomationStatus' => 'Failure', 'DocumentUID' => $DocumentUID, 'CreatedDate' => date('Y-m-d H:i:s'));
			$this->Common_Model->insert_AutomationLog($fieldArray);

			$importStatus = 'Failure';
		} else {
			$importStatus = 'Failure';
				//parsing array
			if(isset($Fetch_Request_Details['data']) && is_array($Fetch_Request_Details['data'])) {

				if(isset($Fetch_Request_Detailsdata) && is_array($Fetch_Request_Detailsdata)) {
					foreach ($Fetch_Request_Detailsdata as $Fetch_Request_Detailsdatavalue) {
						if(isset($Fetch_Request_Detailsdatavalue['extractedData']) && is_array($Fetch_Request_Detailsdatavalue['extractedData'])) {
							foreach ($Fetch_Request_Detailsdatavalue['extractedData'] as $FetExKey => $FetExVal) {
								//$LoanNumber = (isset($FetExVal['Loan Number']) && $FetExVal['Loan Number'] != '') ? $FetExVal['Loan Number'] : $FetExVal['First Mortgagee Loan Number'];
								if((isset($FetExVal['Borrower Name']) && $FetExVal['Borrower Name'] == $tOrders->BorrowerName) && (isset($FetExVal['Dwelling Amount']) && $FetExVal['Dwelling Amount'] >= $tOrders->LoanAmount) && (isset($FetExVal['Loan Number']) && $FetExVal['Loan Number'] == $tOrders->LoanNumber)) {

									$fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> 'Final Document', 'AutomationStatus' => 'Success', 'DocumentUID' => $DocumentUID, 'CreatedDate' => date('Y-m-d H:i:s'));
									$this->Common_Model->insert_AutomationLog($fieldArray);

									$importStatus = 'Success';
								}
							}
						}
					}
				}			

			}
		}
		$loan = array('DocumentUID'=>$DocumentUID,
			'OrderUID'=>$OrderUID,
			'OCR'=>$response,
		);
		$this->db->insert('tLoanFiles',$loan);
		$fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> 'OCR', 'AutomationStatus' => $importStatus, 'DocumentUID' => $DocumentUID, 'CreatedDate' => date('Y-m-d H:i:s'));
		$this->Common_Model->insert_AutomationLog($fieldArray);
		/* Desc: DOC-617 Log for OCR failure @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
		// $this->db->insert('tLoanFiles',$loan);
		$this->Common_Model->save('tOrderImport',['DocumentStatus' => $importStatus],['OrderUID'=>$OrderUID]);
		echo '<pre>';print_r($importStatus);
	}

	/**
	*Function Initiate autofollowup cron 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Saturday 27 June 2020 IST.
	*/
	function execute_auto_followup()
	{
		// Dynamic Queue
		$tOrderQueues = $this->Cron_model->get_autofollowup();
		$currenttime = date('Y-m-d H:i:s');
		$NoOrders = false;
		foreach ($tOrderQueues as $Queue) {
			//autofollowup
			$LastFollowupDurationDateTime = !empty($Queue->LastFollowupDateTime) ? date("Y-m-d H:i:s", strtotime('+'.$Queue->FollowupDuration.' hours', strtotime($Queue->LastFollowupDateTime))) : NULL;
			$QueueRaisedDateTime = !empty($Queue->RaisedDateTime) ? date("Y-m-d H:i:s", strtotime('+'.$Queue->FollowupDuration.' hours', strtotime($Queue->RaisedDateTime))) : NULL;
			$autofollowup_data = [];
			if((!empty($LastFollowupDurationDateTime) && ($currenttime > $LastFollowupDurationDateTime)) || (empty($LastFollowupDurationDateTime) && !empty($QueueRaisedDateTime) && ($currenttime > $QueueRaisedDateTime))) {
				$autofollowup_data['OrderUID'] = $Queue->OrderUID;
				$autofollowup_data['WorkflowModuleUID'] = $Queue->WorkflowModuleUID;
				$autofollowup_data['QueueUID'] = $Queue->QueueUID;
				$autofollowup_data['ReasonUID'] = 3;
				$autofollowup_data['Remainder'] = isset($Queue->FollowupDuration) ? get_businesshourtat_duedate($Queue->FollowupDuration,$currenttime) : NULL;
				$autofollowup_data['Remarks'] = sprintf($this->lang->line('Initiate_Auto_Followup'), $Queue->WorkflowModuleName, $Queue->QueueName);
				$autofollowup_data['RaisedByUserUID'] = $this->config->item('Cron_UserUID');
				$autofollowup_data['RaisedDateTime'] = date('Y-m-d H:i:s');
				$this->Common_Model->save('tOrderFollowUp', $autofollowup_data);
				$this->Common_Model->OrderLogsHistory($Queue->OrderUID,$autofollowup_data['Remarks'],Date('Y-m-d H:i:s'));
				$NoOrders = true;
				echo 'OrderUID : '.$Queue->OrderUID.' - '.$autofollowup_data['Remarks'] ."<hr>";
			}
		}

		if(empty($NoOrders)) {
			echo "Dynamic Queue FollowUp No orders found<hr>"; // exit;
		}

		// Static Queue
		// Docsout Followup
		$DocsCheckedConditionPendingOrders = $this->Cron_model->GetDocsOut_DocsCheckedConditionPendingOrders();
		$currenttime = date('Y-m-d H:i:s');
		$NoOrders = false;
		foreach ($DocsCheckedConditionPendingOrders as $Queue) {
			// Multiple category is selected default set 2 hours
			if (count(explode(',', $Queue->CategoryUID)) > 1) {
				$Queue->FollowupDuration = 2;
			}

			//autofollowup
			$LastFollowupDurationDateTime = !empty($Queue->LastFollowupDateTime) ? date("Y-m-d H:i:s", strtotime('+'.$Queue->FollowupDuration.' hours', strtotime($Queue->LastFollowupDateTime))) : NULL;
			$QueueRaisedDateTime = !empty($Queue->RaisedDateTime) ? date("Y-m-d H:i:s", strtotime('+'.$Queue->FollowupDuration.' hours', strtotime($Queue->RaisedDateTime))) : NULL;
			$autofollowup_data = [];
			if((!empty($LastFollowupDurationDateTime) && ($currenttime > $LastFollowupDurationDateTime)) || (empty($LastFollowupDurationDateTime) && !empty($QueueRaisedDateTime) && ($currenttime > $QueueRaisedDateTime))) {
				$autofollowup_data['OrderUID'] = $Queue->OrderUID;
				$autofollowup_data['WorkflowModuleUID'] = $Queue->WorkflowModuleUID;
				$autofollowup_data['StaticQueueUID'] = $Queue->StaticQueueUID;
				$autofollowup_data['ReasonUID'] = 3;
				$autofollowup_data['Remainder'] = isset($Queue->FollowupDuration) ? get_businesshourtat_duedate($Queue->FollowupDuration,$currenttime) : NULL;
				$autofollowup_data['Remarks'] = sprintf($this->lang->line('Initiate_Auto_Followup'), $Queue->WorkflowModuleName, "Docs Checked Condition Pending");
				$autofollowup_data['RaisedByUserUID'] = $this->config->item('Cron_UserUID');
				$autofollowup_data['RaisedDateTime'] = date('Y-m-d H:i:s');
				$this->Common_Model->save('tOrderFollowUp', $autofollowup_data);
				$this->Common_Model->OrderLogsHistory($Queue->OrderUID,$autofollowup_data['Remarks'],Date('Y-m-d H:i:s'));
				$NoOrders = true;
				echo 'OrderUID : '.$Queue->OrderUID.' - '.$autofollowup_data['Remarks'] ."<hr>";
			}
		}

		if(empty($NoOrders)) {
			echo "Static Queue FollowUp No orders found<hr>"; // exit;
		}
		exit();
	}

	/**
	*Function run ocr with uploaded files 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 30 June 2020 IST.
	*/
	function cron_ocr()
	{	
		//fetch order hoi
		$this->db->select('tOrders.OrderUID,tOrders.OrderNumber,tDocuments.DocumentURL,tDocuments.DocumentStorage,tDocuments.DocumentUID,tOrders.LoanNumber,mCustomer.CustomerEmail,mCustomer.CustomerName');
		$this->db->from('tOrders');
		$this->db->join('tDocuments','tDocuments.OrderUID=tOrders.OrderUID');
		$this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID');
		$this->db->where(['tOrders.IsOCREnabled'=>1,' tDocuments.IsStacking'=>1]);
		$this->db->order_by('tOrders.OrderUID');
		$this->db->limit(1);
		$tOrders = $this->db->get()->row();
		//echo "<pre>";print_r($tOrders);exit;
		if(empty($tOrders)) {
			echo "No Orders Found";exit;
		}
		
		$this->ocr_core_fn($tOrders);
		echo '<hr>';
	}

	/**
	*Function run core function
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 30 June 2020 IST.
	*Modified at Tuesday 14 July 2020
	*Modified by Santhiya M <santhiya.m@avanzegroup.com>
	*Modification reason - Move HOI queues based on response 
	*Added MoveQueue() function
	*/
	function ocr_core_fn($tOrder)
	{	

		ini_set('display_errors', 1);
		error_reporting(E_All);
		
		include (FCPATH  . 'OCR/DoctracCron.php');
		$OrderUID = $tOrder->OrderUID;
		$DocumentUID = $tOrder->DocumentUID;

		$FilePath = FCPATH . $tOrder->DocumentURL;
		chmod($FilePath, 0777);
			// $this->pdf_split($tOrder);

		/*created JSON array values*/
		$RequestParams = array(
			"sourceApp"=>"DOCTRAC",
			"orderNo"=>$tOrder->OrderNumber,
			"orderUID"=>$OrderUID, 
			"docType"=> "PDF",
			"source"=> $FilePath,
			"features"=> "DOCUMENT_TEXT_DETECTION",
				// "featuresInput"=> "path of the image/icon",
				// "outputFormat"=> "JSON",
				// "outputLocation"=> "path of the output file",
			"engine"=> "Google Vision",
				//"docDef"=> $DocumentTypeObjects
		);
		// call OCR function
		$response = run_OCR($RequestParams); 
		$Fetch_Request_Details = json_decode($response,true);
		echo '<pre>';print_r($Fetch_Request_Details);
		echo '<br>';
		if (empty($Fetch_Request_Details)) {
			$Docarray = array(
				'IsStacking' => 4
			);
			$this->db->where('DocumentUID',$DocumentUID);
			$this->db->update('tDocuments',$Docarray);

			$loan = array('DocumentUID'=>$DocumentUID,
				'OrderUID'=>$OrderUID,
				'OCR'=>$response,
			);
			/* Desc: DOC-617 Log for OCR failure @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
			$this->db->insert('tLoanFiles',$loan);
			$fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> 'OCR', 'AutomationStatus' => 'Failure', 'DocumentUID' => $DocumentUID, 'CreatedDate' => date('Y-m-d H:i:s'));
			$this->Common_Model->insert_AutomationLog($fieldArray);
			/* Desc: DOC-617 Log for OCR failure @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
			echo json_encode(array('Status' => 2,'message'=>'Empty Response - Failed for OrderNumber : '.$tOrder->OrderNumber,'type' => 'danger'));exit();
		} else {
			//change to OCR Completed
			$this->db->where('OrderUID',$OrderUID);
			$this->db->update('tOrders',['IsOCREnabled'=>'2','IsHoiNotified' => '0']);
			$errors = 0;
			$valid = 0;
			//parsing array
			if(isset($Fetch_Request_Details['data']) && is_array($Fetch_Request_Details['data'])) {
				$FilePath = FCPATH . $tOrder->DocumentURL;	
				$pages = '';				
				$torderimportdata = [];
				$tpropdata = [];
				$tOrderUpdate = [];

				foreach ($Fetch_Request_Details['data'] as $Fetch_Request_Detailsdata) {
					if(isset($Fetch_Request_Detailsdata) && is_array($Fetch_Request_Detailsdata)) {
						foreach ($Fetch_Request_Detailsdata as $Fetch_Request_Detailsdatavalue) {

							if(isset($Fetch_Request_Detailsdatavalue['pageNo']) ){
								if($pages != ''){
									$pages = $pages.'-'.$Fetch_Request_Detailsdatavalue['pageNo'];
								}else{
									$pages = $pages.$Fetch_Request_Detailsdatavalue['pageNo'];
								}	
							}									

							if(isset($Fetch_Request_Detailsdatavalue['extractedData']) && is_array($Fetch_Request_Detailsdatavalue['extractedData'])) {
								foreach ($Fetch_Request_Detailsdatavalue['extractedData'] as $Fetch_Request_Detailsdatavaluekey => $Fetch_Request_DetailsdatavalueextractedData) {

									if(isset($Fetch_Request_DetailsdatavalueextractedData['InsuranceCompany'])) {
										$torderimportdata['InsuranceCompany'] = $Fetch_Request_DetailsdatavalueextractedData['InsuranceCompany'];

										if($torderimportdata['InsuranceCompany'] == 'Farmers' || $torderimportdata['InsuranceCompany'] == 'USAA'){
											$torderimportdata['RequestType'] = 'Policy Search';
										}
										if($torderimportdata['InsuranceCompany'] == 'All State'){
											$torderimportdata['EnquireType'] = 'BILL Mortgagee';						
										}
										if($torderimportdata['InsuranceCompany'] == 'Pemco Mutual' || $torderimportdata['InsuranceCompany'] == 'Pemco Mutual' || $torderimportdata['InsuranceCompany'] == 'Security First'){
											$torderimportdata['RequestType'] = 'Evidence of Insurance';
										}

										if($torderimportdata['InsuranceCompany'] == 'Western Mutual'){
											$torderimportdata['EnquireType'] = '(None)';
										}

									} else {
										$errors++;
									}
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Policy Number'])) {
										$torderimportdata['PolicyNumber'] = $Fetch_Request_DetailsdatavalueextractedData['Policy Number'];
									} else {
										$errors++;
									}
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Policy Type'])) {
										$torderimportdata['PolicyType'] = $Fetch_Request_DetailsdatavalueextractedData['Policy Type'];
									} else {
										// $errors++;
									}
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Insured Location'])) {
										$torderimportdata['InsuredLocation'] = $Fetch_Request_DetailsdatavalueextractedData['Insured Location'];

										$ExtractAddress = $this->Common_Model->ExtractAddress($torderimportdata['InsuredLocation']);
										/**
										* Desc - In order to avoid tOrder update saved in tOrderImports
										* 
										* @author : Santhiya M <santhiya.m@avanzegroup.com>
										* @since  : 24th Sept 2020 
										*/
									
										// $tOrderUpdate['PropertyAddress1'] = $ExtractAddress['Address1'];
										// $tOrderUpdate['PropertyCityName'] = $ExtractAddress['CityName'];
										// $tOrderUpdate['PropertyStateCode'] = $ExtractAddress['StateCode'];
										// $tOrderUpdate['PropertyCountyName'] = $ExtractAddress['CountyName'];
										// $tOrderUpdate['PropertyZipCode'] = $ExtractAddress['Zipcode'];

										$torderimportdata['PropertyAddress1'] = $ExtractAddress['Address1'];
										$torderimportdata['PropertyCityName'] = $ExtractAddress['CityName'];
										$torderimportdata['PropertyStateCode'] = $ExtractAddress['StateCode'];
										$torderimportdata['PropertyCountyName'] = $ExtractAddress['CountyName'];
										$torderimportdata['PropertyZipCode'] = $ExtractAddress['Zipcode'];
										
									} else {
										$errors++;
									}

									if(isset($Fetch_Request_DetailsdatavalueextractedData['Policy Start Date'])) {
										$torderimportdata['PolicyStartDate'] = $Fetch_Request_DetailsdatavalueextractedData['Policy Start Date'];
									} else {
										// $errors++;
									}
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Policy Exp Date'])) {
										$torderimportdata['PolicyExpDate'] = $Fetch_Request_DetailsdatavalueextractedData['Policy Exp Date'];
									} else {
										// $errors++;
									}
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Dwelling Amount'])) {
										$torderimportdata['DwellingAmount'] = $Fetch_Request_DetailsdatavalueextractedData['Dwelling Amount'];
									} else {
										$errors++;
									}

									//Borrower Name Saved
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Borrower Name'])) {

										$tpropdata['BorrowerFirstName'] = $Fetch_Request_DetailsdatavalueextractedData['Borrower Name'];
										$torderimportdata['FirstInsuranceName'] = $Fetch_Request_DetailsdatavalueextractedData['Borrower Name'];
									} else {
										$errors++;
									}	

									//first mortgagee
									if(isset($Fetch_Request_DetailsdatavalueextractedData['First Mortgagee'])) {
										$torderimportdata['FirstMortgagee'] = $Fetch_Request_DetailsdatavalueextractedData['First Mortgagee'];
									} else {
										// $errors++;
									}	

									//first mortgagee loan number
									if(isset($Fetch_Request_DetailsdatavalueextractedData['First Mortgagee Loan Number']) || isset($Fetch_Request_DetailsdatavalueextractedData['Loan Number'])) {
										$LoanNumberFirst = isset($Fetch_Request_DetailsdatavalueextractedData['First Mortgagee Loan Number']) ? $Fetch_Request_DetailsdatavalueextractedData['First Mortgagee Loan Number'] : $Fetch_Request_DetailsdatavalueextractedData['Loan Number'] ;
										$torderimportdata['FirstMortgageeLoanNumber'] = $LoanNumberFirst;
									} else {
										$errors++;
									}	

									//second mortgagee
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Second Mortgagee'])) {
										$torderimportdata['SecondMortgagee'] = $Fetch_Request_DetailsdatavalueextractedData['Second Mortgagee'];
									} else {
										$errors++;
									}	

									//second mortgagee loan number
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Second Mortgagee'])) {
										$torderimportdata['SecondMortgageeLoanNumber'] = $Fetch_Request_DetailsdatavalueextractedData['Second Mortgagee'];
									} else {
										$errors++;
									}

									if(isset($Fetch_Request_DetailsdatavalueextractedData['Residence'])) {
										$torderimportdata['Residence'] = $Fetch_Request_DetailsdatavalueextractedData['Residence'];
									} else {
										$errors++;
									}
									/* Loan company details update start */
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Website'])) {
										$torderimportdata['WebUrl'] = $Fetch_Request_DetailsdatavalueextractedData['Website'];
									} else {
										$errors++;
									}

									if(isset($Fetch_Request_DetailsdatavalueextractedData['Email'])) {
										$torderimportdata['Email'] = $Fetch_Request_DetailsdatavalueextractedData['Email'];
									} else {
										$errors++;
									}
									if(isset($Fetch_Request_DetailsdatavalueextractedData['Fax No'])) {
										$torderimportdata['Efax'] = $Fetch_Request_DetailsdatavalueextractedData['Fax No'];
									} else {
										$errors++;
									}
									//additional ocr details
									if(isset($Fetch_Request_DetailsdatavalueextractedData['InsuranceAgency'])) {
										$torderimportdata['InsuranceAgency'] = $Fetch_Request_DetailsdatavalueextractedData['InsuranceAgency'];
									}

									// if(isset($Fetch_Request_DetailsdatavalueextractedData['Amount of Payment'])) {
									// 	$torderimportdata['AmountofPayment'] = $Fetch_Request_DetailsdatavalueextractedData['Amount of Payment'];
									// }

									if(isset($Fetch_Request_DetailsdatavalueextractedData['Dwelling'])) {
										$torderimportdata['Dwelling'] = $Fetch_Request_DetailsdatavalueextractedData['Dwelling'];
									}

									if(isset($Fetch_Request_DetailsdatavalueextractedData['MORTGAGEE'])) {
										$torderimportdata['Mortgagee'] = $Fetch_Request_DetailsdatavalueextractedData['MORTGAGEE'];
									}

									if(isset($Fetch_Request_DetailsdatavalueextractedData['EXPIRATION DATE OF POLICY'])) {
										$torderimportdata['PolicyExpirationDate'] = $Fetch_Request_DetailsdatavalueextractedData['EXPIRATION DATE OF POLICY'];
									}
									/* Loan company details update end */
								}
							}

						}
					}
				}



				//update torder import
				if(!empty($torderimportdata)) {
					$this->db->where('OrderUID',$OrderUID);
					$this->db->update('tOrderImport',$torderimportdata);
				}

				// if(!empty($torderimportdata) && $errors == 0){
					$AutomationStatus = 'Success';
				// }else{
				// 	$AutomationStatus = 'Failure';
				// }

				//update torderpropertyrole
				if(!empty($tpropdata)) {
					//get Order property data
					$OrderPropertyData = $this->Cron_model->getOrderPropertyData($OrderUID,$tpropdata['BorrowerFirstName']);
					if(!empty($OrderPropertyData)) {
						//update
						$this->Common_Model->save('tOrderPropertyRole', $tpropdata, ['OrderUID'=>$OrderUID]);
					} else {
						//insert
						$tpropdata['OrderUID'] = $OrderUID;
						$this->Common_Model->save('tOrderPropertyRole', $tpropdata);
					}
				}

				//update torders
				if(!empty($tOrderUpdate)){							
					$this->Common_Model->save('tOrders', $tOrderUpdate, ['OrderUID'=>$OrderUID]);
				}

				$upload_path = FCPATH.'uploads/OrderDocumentPath/' .$tOrder->OrderNumber ;
				$this->Common_Model->CreateDirectoryToPath($upload_path);

				$filename = 'OrderDeclaration-'.time().'.pdf';
				$name = $upload_path.'/'.$filename;
				chmod($FilePath, 0777);

				if(file_exists($FilePath) && $pages != ''){
					/* Desc: Extract declaration page from ocr file and store @author Santhiya M<santhiya.m@avanzegroup.com> @since Thursday 8 July 2020 */
					$this->Common_Model->SplitPDF($FilePath, $pages, $name);
					$urlPath = 'uploads/OrderDocumentPath/' .$tOrder->OrderNumber.'/'.$filename ;
					$Doc = array(
						'DocumentName' => $filename, 
						'DocumentURL'=> $urlPath,
						'OrderUID'=> $tOrder->OrderUID,
						'IsStacking'=> 7, //For declaration IsStacking = 7;
						'TypeofDocument'=> 'OCR_Declaration',
						'UploadedDateTime'=> date('Y-m-d H:i:s'),
					);
					$this->Common_Model->save('tDocuments', $Doc);
				}
			}
			$OrderImportDetails = $this->Common_Model->get_row('tOrderImport', ['OrderUID'=>$OrderUID]);
			$HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];
				//update hoi company email in checklist
			if($OrderImportDetails->PolicyNumber){
				$DocumentPolicyNameUID = $this->config->item('HoiChecklistKeys')['PolicyNumber'];
				$checklistdata = array('Comments'=>$OrderImportDetails->PolicyNumber);
				$this->Cron_model->UpdateOrderChecklist($DocumentPolicyNameUID, $OrderUID, $checklistdata);
				
			}

			if($OrderImportDetails->PolicyExpDate){
				$DocumentTypeUIDExp = $this->config->item('HoiChecklistKeys')['PolicyExpireDate'];
				/* @Desc Update Order Checklist @author Santhiya M <santhiya.m@avanzegroup.com> @since July 30 2020 */
				$checklistdata = array('Comments'=>$OrderImportDetails->PolicyExpDate);
				$this->Cron_model->UpdateOrderChecklist($DocumentTypeUIDExp, $OrderUID, $checklistdata);

			}

			//fetch company details by name
			$getCompanyDetails = $this->Cron_model->get_mCompanyDetails($OrderImportDetails->InsuranceCompany); 
			 /**
			 Desc: Update Mortgage option in Loan Import 
			 Santhiya M <santhiya.m@avanzegroup.com>
			 July 24 2020 
			 **/
			 if(!empty($getCompanyDetails) && isset($getCompanyDetails->MortgageOption)){
			 	$MortgageOption = $getCompanyDetails->MortgageOption;
			 	$this->Common_Model->save('tOrderImport', ['MortgageOption' => $MortgageOption], ['OrderUID'=>$OrderUID]);
			 }
			 if(isset($OrderImportDetails->Email) && $OrderImportDetails->Email != ''){	
			 	$email = $OrderImportDetails->Email;
			 }else{				
			 	$email = $getCompanyDetails->Email;
			 }
			 if(isset($OrderImportDetails->InsuranceCompany) && $OrderImportDetails->InsuranceCompany != ''){	
			 	$CompanyName = $OrderImportDetails->InsuranceCompany;
			 }else{				
			 	$CompanyName = $getCompanyDetails->CompanyName;
			 }
			 if(isset($OrderImportDetails->Efax) && $OrderImportDetails->Efax != ''){	
			 	$Efax = $OrderImportDetails->Efax;
			 }else{				
			 	$Efax = $getCompanyDetails->FaxNo;
			 }		
			 if(isset($OrderImportDetails->WebUrl) && $OrderImportDetails->WebUrl != ''){	
			 	$WebUrl = $OrderImportDetails->WebUrl;
			 }else{				
			 	$WebUrl = $getCompanyDetails->Website;
			 }		
			 $ContactNo = $getCompanyDetails->ContactNo;

			 /* Update email checklist */
			 if(!empty($email)){			 				
			 	$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['Email'];

			 	/* @Desc Update Order Checklist Optimized @author Santhiya M <santhiya.m@avanzegroup.com> @since July 30 2020 */
			 	$checklistdata = array('Comments'=>$email);
			 	$this->Cron_model->UpdateOrderChecklist($DocumentTypeUID, $OrderUID, $checklistdata);

			 	
			 }
			 /* Update company name in checklist */
			 if($CompanyName){
			 	$CompanyDocumentTypeUID = $this->config->item('HoiChecklistKeys')['CompanyName'];

			 	/* @Desc Update Order Checklist Optimized @author Santhiya M <santhiya.m@avanzegroup.com> @since July 30 2020 */
			 	$checklistdata = array('Comments'=>$CompanyName,'SelectIn'=>$CompanyName);
			 	$this->Cron_model->UpdateOrderChecklist($CompanyDocumentTypeUID, $OrderUID, $checklistdata);

			 }
			 /* Update Efax in order checklist */
			 if($Efax){
			 	$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['FaxNo'];

			 	/* @Desc Update Order Checklist Optimized @author Santhiya M <santhiya.m@avanzegroup.com> @since July 30 2020 */
			 	$checklistdata = array('Comments'=>$Efax);
			 	$this->Cron_model->UpdateOrderChecklist($DocumentTypeUID, $OrderUID, $checklistdata);

			 }
			 /* Update Efax in order checklist */
			 if($ContactNo){
			 	$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['ContactNo'];

			 	/* @Desc Update Order Checklist Optimized @author Santhiya M <santhiya.m@avanzegroup.com> @since July 30 2020 */
			 	$checklistdata = array('Comments'=>$ContactNo);
			 	$this->Cron_model->UpdateOrderChecklist($DocumentTypeUID, $OrderUID, $checklistdata);

			 }
			 /* Update Efax in order checklist */
			 if($WebUrl){
			 	$DocumentTypeUID = $this->config->item('HoiChecklistKeys')['Website'];

			 	/* @Desc Update Order Checklist Optimized @author Santhiya M <santhiya.m@avanzegroup.com> @since July 30 2020 */
			 	$checklistdata = array('Comments'=>$WebUrl);
			 	$this->Cron_model->UpdateOrderChecklist($DocumentTypeUID, $OrderUID, $checklistdata);

			 }
			 $Docarray = array(
			 	'IsStacking' => 2
			 );
			 $this->db->where('DocumentUID',$DocumentUID);
			 $this->db->update('tDocuments',$Docarray);

			 $loan = array('DocumentUID'=>$DocumentUID,
			 	'OrderUID'=>$OrderUID,
			 	'OCR'=>$response,
			 );
			 $this->db->insert('tLoanFiles',$loan);

			 /* Desc: Store response details for Automation Log @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 6th 2020 */
			 $LoanFileUID = $this->db->insert_id();
			 $fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> 'OCR', 'AutomationStatus' => $AutomationStatus, 'DocumentUID' => $DocumentUID, 'CreatedDate' => date('Y-m-d H:i:s'));
			 $this->Common_Model->insert_AutomationLog($fieldArray);
			 echo json_encode(array('Status' => 0,'message'=>'Success for OrderNumber : '.$tOrder->OrderNumber,'type' => 'success'));exit();
			}

		}

	/**
	* Function to retrive the fax images from e-Fax Integration
	*
	* @param ClientUserID, ClientAuthKey, FaxID, OrderUID
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return Fax Image in PDF format
	* @since July 14th 2020
	* @version E-Fax Integration
	*
	*/

	function RetriveFaxImages()
	{
		$this->load->model('Efax/Efax_model');
		$this->load->model('EfaxOrders/Efaxordersmodel');
		$FaxDetails = $this->Cron_model->GetEFaxDetails();
		foreach ($FaxDetails as $key => $value) {
			$EFaxDataUID = $value['EFaxDataUID'];
			$data['EventCode'] = 'FaxImageRetrieve';
			$data['OrderUID'] = $value['OrderUID'];
			$data['OrderNumber'] = $value['TransactionID'];
			$data['FaxID'] = $value['FaxID'];
			$OrgDetails = $this->Efaxordersmodel->GetEFaxCredentials();
			$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
			$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
			$fax_list = $this->Efax_model->Func_FaxDetails($data);

			if(isset($fax_list['errors'])){
				$list = $this->Efaxordersmodel->array_to_list($fax_list);
				echo $res = array('status' => 0, 'fax_id' => $data['FaxID'], 'error' => $list);
			} else {
				$filename =  'FaxImage-'.$data['FaxID'].'.pdf';
				$basepath = FCPATH.'uploads/Efax_files/';
				$path = $basepath.$filename;
				$filepath = 'uploads/Efax_files/'.$filename;
				$this->Efaxordersmodel->CreateDirectoryToPath($basepath);
				$image = base64_decode($fax_list['image']); 
				file_put_contents($path, $image);
				$FaxID = $data['FaxID'];
				$update_tEFaxData = array(
					'IsFaxImageReceived' => 1,
					'DocumentURL' => $filepath,
				);
				$this->db->where('FaxID',$FaxID);
				$this->db->update('tEFaxData',$update_tEFaxData);
				unset($update_tEFaxData);
				$res = array('status' => 1,'fax_id' => $data['FaxID'], 'url' => $path, 'filename' => $filename, 'filepath' => $filepath);
				echo json_encode($res);
			}
		}
	}

	function CDandSchedulingQueueUpdatesCron() {
		$this->Cron_model->CDandSchedulingQueueUpdatesCron();
	}

	// Order Workflows updates cron
	function OrderWorkflowUpdatesCron() {
		$this->Cron_model->OrderWorkflowUpdatesCron();
	}

	/**
	*Function get unmapped efax orders
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 20 July 2020.
	*/

	function runEfaxOrders()
	{
		//fetch Efax unmapped data from table
		$EfaxOrders = $this->Cron_model->getUnMappedEfaxOrders(); 

		if(empty($EfaxOrders)){
			echo 'No Orders Found<hr>';
		}
		$BorrowerNamesArray = $this->Cron_model->getBorrowerNameOrders();
		$BorrowerNames = array();

		//generate borrower names
		if(!empty($BorrowerNamesArray)) {

			foreach ($BorrowerNamesArray as $key => $value){
				$BorrowerNames[$value->OrderNumber] = $value->BorrowerFirstName;
			}
		} 
		//validate efax orders is available
		if(!empty($EfaxOrders)){
			foreach ($EfaxOrders as $EfaxOrder) {

				echo 'EFaxDataUID : <b>'.$EfaxOrder->FaxID.'</b>	 Initiated <br>';

				if(!empty($EfaxOrder->DocumentURL)) {

					$FilePath = FCPATH.$EfaxOrder->DocumentURL;

					/*created JSON array values*/
					$RequestParams = array(
						"sourceApp"=>"DOCTRAC",
						"orderNo"=>'',
						"orderUID"=>'', 
						"docType"=> "PDF",
						"source"=> $FilePath,
						"IsFaxImage"=> TRUE,
						"BorrowerNames"=> $BorrowerNames,
						"engine"=> "Google Vision",
					);
					$ocr = $this->OcrDocumentProcess($RequestParams);
					if($ocr['status'] == 1){
						//update efax data to image received and map orderuid
						$update_tEFaxData = array(
							'IsFaxImageReceived' => 1,
							'OrderUID' => $OrderDetails->OrderUID,
						);
						$this->db->where('FaxID',$EfaxOrder->FaxID);
						$this->db->update('tEFaxData',$update_tEFaxData);

						echo $EfaxOrder->FaxID.' is mapped to Order Number : <b>' .$OrderDetails->OrderNumber.'</b> & OrderUID : <b>' .$OrderDetails->OrderUID.'</b><br>';
					}else{
						echo $ocr['msg'].'<br>';
					}
				} else {
					echo "<b>Empty Document URL</b>".'<br>';
				}

				echo 'EFaxDataUID : <b>'.$EfaxOrder->FaxID.'</b> closed <hr>';
			}

		}

	}
	function OcrDocumentProcess($RequestParams){
		//including ocr common file
		require_once (FCPATH  . 'OCR/DoctracCron.php');
		$FilePath = $RequestParams['source'];
		// call OCR function
		$response = run_OCR($RequestParams);
		$Fetch_Request_Details = json_decode($response,true);
		print_r($Fetch_Request_Details);
		echo '<br>';
					//check response is valid
		if(isset($Fetch_Request_Details['data']) && is_array($Fetch_Request_Details['data'])) {
						//matches with the order
			foreach ($Fetch_Request_Details['data'] as $Order => $OrderNumber) {
							//ordernumber available
				if(!empty($OrderNumber) && !is_array($OrderNumber)) {
								//check valid ordernumber
					$OrderDetails = $this->Cron_model->isvalidorder(trim($OrderNumber));
					if(!empty($OrderDetails)) {		
						//insert document to tDocuments
						$upload_path = FCPATH.'uploads/OrderDocumentPath/' .$OrderDetails->OrderNumber ;
						$this->Common_Model->CreateDirectoryToPath($upload_path);

						$cron_uid = $this->config->item('Cron_UserUID');
						$filename = basename($FilePath);
						$urlPath = 'uploads/OrderDocumentPath/' .$OrderDetails->OrderNumber.'/'.$filename ;

						//copy the document
						copy($FilePath, FCPATH.$urlPath);

						$Doc = array(
							'DocumentName' => $filename, 
							'DocumentURL'=> $urlPath,
							'OrderUID'=> $OrderDetails->OrderUID,
							'IsStacking'=> 2,
							'TypeofDocument'=> 'EFaxImage',
							'UploadedDateTime'=> date('Y-m-d H:i:s'),
						);
						$this->db->insert('tDocuments',$Doc);
						$res = array('status'=> 1, 'msg'=>'success', 'filepath' => $urlPath);

					} else {

						$res = array('status'=> 0, 'msg'=> ("<b>Invalid OrderNumber</b> : ".$OrderNumber) );
						//echo "<b>Invalid OrderNumber</b> : ".$OrderNumber.'<br>';
					}
				} else {

					$res = array('status'=> 0, 'msg'=>'<b>No OrderNumber</b>' );
					//echo "<b>No OrderNumber</b>".'<br>';
				}
			}
		} else {

			$res = array('status'=> 0, 'msg'=>'<b>Invalid Response</b>' );
			//echo "<b>Invalid Response</b>".'<br>';
		}
		return $res;
	}
	// Orderwise Enable available workflow
	function OrderwiseEnableAvailableWorkflow() {
		$this->Cron_model->OrderwiseEnableAvailableWorkflow();
	}


	/**
	*Function remove loan from doctrac url to be baseurl/Cron/deleteLoanNumberAllTables?key=key&loannumber=LoanNumber
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Friday 14 August 2020.
	*/

	function deleteLoanNumberAllTables()
	{
		$key = "Avanze@123";
		$PostKey = $this->input->get('key');
		$LoanNumber = $this->input->get('loannumber');

		if($key != $PostKey) {
			echo "Invalid Auth";exit;
		}

		if(empty($LoanNumber)) {
			echo "No Loan Number";exit;
		}
		echo $this->Cron_model->deleteLoanAllTables($LoanNumber);
	}

	/**
	*Function Complete Exception Queue and Workflow
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Thursday 22 October 2020.
	*/
	// Condition: (3, 4, 5 loans are automatically moved to completed folder from â€œBorrower Docs Pendingâ€?) 
	// URL : Cron/CompleteExceptionQueueandWorkflow?key=Avanze@123&ClientUID=29&WorkflowModuleUID=1&QueueUID=15&WorkflowCompleteMilestones=6,48,7,47,8,49,50
	public function CompleteExceptionQueueandWorkflow()
	{
		$key = "Avanze@123";
		$PostKey = $this->input->get('key');
		$ClientUID = $this->input->get('ClientUID');
		$WorkflowModuleUID = $this->input->get('WorkflowModuleUID');
		$QueueUID = $this->input->get('QueueUID');
		$WorkflowCompleteMilestones = $this->input->get('WorkflowCompleteMilestones');

		if($key != $PostKey) {
			echo "Invalid Auth";exit;
		}

		if(empty($ClientUID)) {
			echo "Invalid ClientUID";exit;
		}

		if(empty($WorkflowModuleUID)) {
			echo "Invalid WorkflowModuleUID";exit;
		}

		if(empty($QueueUID)) {
			echo "Invalid QueueUID";exit;
		}

		echo $this->Cron_model->CompleteExceptionQueueandWorkflow($ClientUID, $WorkflowModuleUID, $QueueUID, $WorkflowCompleteMilestones);
	}

	/**
	*Function Get next order lock expiration date - automatically change date based on current date 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Monday 09 November 2020.
	*/
	/*Workup - Rate lock If date is 2nd then date to be set for 4th
	URL : Cron/GetNextOrderLockExpirationUpdates?key=Avanze@123&ClientUID=29&WorkflowModuleUID=7&DaysAdd=2*/
	public function GetNextOrderLockExpirationUpdates()
	{
		$key = "Avanze@123";
		$PostKey = $this->input->get('key');
		$ClientUID = $this->input->get('ClientUID');
		$WorkflowModuleUID = $this->input->get('WorkflowModuleUID');
		$DaysAdd = $this->input->get('DaysAdd');

		if($key != $PostKey) {
			echo "Invalid Auth";exit;
		}

		if(empty($ClientUID)) {
			echo "Invalid ClientUID";exit;
		}

		if(empty($WorkflowModuleUID)) {
			echo "Invalid WorkflowModuleUID";exit;
		}

		echo $this->Cron_model->GetNextOrderLockExpirationUpdates($ClientUID, $WorkflowModuleUID, $DaysAdd);
	}

	public function WorkupReworkCron()
	{
		$this->Cron_model->WorkupReworkCron(['CronFunction'=>TRUE]);
	}

}?>
