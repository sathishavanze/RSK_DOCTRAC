<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BulkWorkflow extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Common_Model');
    $this->load->model('BulkWorkflow_model');
	}

	public function index()
	{
		$data['content'] = 'bulkworkflowcomplete';
		$data['Users'] = $this->Common_Model->get('mUsers', ['Active'=>STATUS_ONE]);
 		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

  /* ---- EXCEL BULK ENTRY STARTS --- */

  function preview_bulkworkflow()
  {

    if(isset($_FILES['file'])) 
    {
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

        $FileUploadPreview = [];

        /*declare excel values*/
        $columnvariables = array(0=>'OrderNumber',1=>'LoanNumber',2=>'PreScreen',3=>'Welcome Call',4=>'Title',5=>'FHAVA Case',6=>'Third Party',7=>'Doc Chase',8=>'Workup',9=>'Underwriter',10=>'Scheduling',11=>'Closing');

        $objWorksheet = $objPHPExcel->getActiveSheet();
            //excel with first row header, use header as key
        $highestRow = $objWorksheet->getHighestDataRow();
        $highestColumn = $objWorksheet->getHighestDataColumn();


        $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false, true);
        $headingsArray = $headingsArray[1];

        $validationarray = array();

        $arrayCode = array();
        $r = -1;
        $headingArray = array();
        for ($row = 2; $row <= $highestRow; ++$row) 
        {
          $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false, true);
          if ($this->isEmptyRow(reset($dataRow))) {
            continue;
          } 
          // skip empty row
          ++$r;

          $i = 0;
          foreach ($headingsArray as $columnKey => $columnHeading) 
          {
            $cellformat = $objWorksheet->getCell($columnKey . $row);
            if(PHPExcel_Shared_Date::isDateTime($cellformat)) {
              $arrayCode[$r][$i] = $cellformat->getFormattedValue();
            }else{
              $arrayCode[$r][$i] = $cellformat->getValue();
            }
            $i++;
          }
          $validationarray[$r]  = array('OrderNumber'=>false);
        }

        $counts = 0; 
        $posts = [];  
        foreach ($arrayCode as $keys => $values) 
        {
          $OrderNumber = $values[0];
          $LoanNumber = $values[1];
          $OrderUID = $this->BulkWorkflow_model->ChkOrderisValid($OrderNumber);
          if(count($values) == count($columnvariables)) 
          {

            if(empty($OrderNumber) || empty($OrderUID)) 
            { 
              $values['ColorCode'] = "#fff";
              $values['BGColorCode'] = "#168998"; 
            } 
            else if(empty($LoanNumber)) 
            { 
              $values['ColorCode'] = "#fff";
              $values['BGColorCode'] = "#ff04ec";
            }

          } else {
            $values['ColorCode'] = "#fff";
            $values['BGColorCode'] = "#757575";
          }

          $arayhgen = [];
          foreach ($values as $key => $value) 
          { 
           $arayhgen[] = $value;
           $counts++;
          }
          $posts[] = $arayhgen;
        }

        $response['data'] = $posts;
        $PATH = $this->config->item('UploadPath') . $this->loggedid . '/';
        if($this->Common_Model->CreateDirectoryToPath($PATH)) {
          $fp = fopen($PATH . 'results.json', 'w');
          fwrite($fp, json_encode($response));
          fclose($fp);        
        }

        $data['arrayCode'] = $arrayCode;
        $data['headingsArray'] = $headingsArray;

        $preview = $this->load->view('BulkWorkflow_partialviews/bulk_preview', $data, true);
        $filelink = 'uploads/'.$this->loggedid.'/results.json';
        echo json_encode(array('error' => 0, 'html' => $preview, 'filehtml' => $filepreview, 'filelink' => $filelink)); exit;
      } else {
        echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
      }
    } else {
      echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
    }

  }

  function save_bulkworkflow()
  {
    if(isset($_FILES['file'])) 
    {
      $lib = $this->load->library('Excel'); 
      $inputFile = $_FILES['file']['tmp_name'];
      $filenames = $this->input->post('FILENAMES');
      $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
      $temp = explode(".", $_FILES["file"]["name"]);

      $allowedExts = array("xlsx", "xls","csv");

      $extension = end($temp);

      if (in_array($extension, $allowedExts)) 
      {

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

        $FileUploadPreview = [];

        /*declare excel values*/
        $columnvariables = array(0=>'OrderNumber',1=>'LoanNumber',2=>'PreScreen',3=>'Welcome Call',4=>'Title',5=>'FHAVA Case',6=>'Third Party',7=>'Doc Chase',8=>'Workup',9=>'Underwriter',10=>'Scheduling',11=>'Closing');

        $objWorksheet = $objPHPExcel->getActiveSheet();
            //excel with first row header, use header as key
        $highestRow = $objWorksheet->getHighestDataRow();
        $highestColumn = $objWorksheet->getHighestDataColumn();


        $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false, true);
        $headingsArray = $headingsArray[1];

        $validationarray = array();

        $arrayCode = array();
        $r = -1;
        $headingArray = array();
        for ($row = 2; $row <= $highestRow; ++$row) 
        {
          $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false, true);
          if ($this->isEmptyRow(reset($dataRow))) {
            continue;
          } 
          // skip empty row
          ++$r;

          $i = 0;
          foreach ($headingsArray as $columnKey => $columnHeading) 
          {
            $cellformat = $objWorksheet->getCell($columnKey . $row);
            if(PHPExcel_Shared_Date::isDateTime($cellformat)) {
              $arrayCode[$r][$i] = $cellformat->getFormattedValue();
            }else{
              $arrayCode[$r][$i] = $cellformat->getValue();
            }
            $i++;
          }
        }

        $counts = 0; 
        $ErrorData = [];
        $SuccessData = [];
        $ImportOrders = [];
        $Workflows = [];
        $posts = [];  
        foreach ($arrayCode as $keys => $values) 
        {
          $OrderNumber = $values[0];
          $LoanNumber = $values[1];
          $OrderUID = $this->BulkWorkflow_model->ChkOrderisValid($OrderNumber);
          $arayhgen = [];
          foreach ($values as $key => $value) 
          {

           if(!in_array($counts, [0,1])) // Workflow check only valid records
           { 
             if(!empty($OrderNumber) && !empty($OrderUID) && !empty($LoanNumber)) 
             {  
               if(in_array(strtolower($value), ['yes','no','n/a']))
               {
                 $WorkflowID = $this->BulkWorkflow_model->getWorkflowID($columnvariables[$key]); 
                 $Workflows[$WorkflowID] = strtolower($value); 
               }
             }
           }
           $arayhgen[] = $value;
           $counts++;
          }

          if(count($values) == count($columnvariables)) 
          {
            if(empty($OrderNumber) || empty($OrderUID)) 
            {  
              $values['Style'] = "color: #fff; background: #168998";
              $ErrorData[] = array_merge($arayhgen,$values); // merge cell style last array
            } 
            else if(empty($LoanNumber)) 
            {  
              $values['Style'] = "color: #fff; background: #ff04ec";
              $ErrorData[] = array_merge($arayhgen,$values);
            } else {
              $SuccessData[] = $arayhgen;
              $ImportOrders[] = $OrderUID;
            }
          } else {
            $values['Style'] = "color: #fff; background: #757575";
            $ErrorData[] = array_merge($arayhgen,$values);
          }
          $posts[] = $arayhgen;
        }

        if(count($SuccessData)>0) // Not error only allow to process
        {
          $this->BulkCompleteOrders($ImportOrders,$Workflows);
        }

        $data['arrayCode'] = $arrayCode;
        $data['headingsArray'] = $headingsArray;
        $data['Success'] = $SuccessData;
        $data['Error'] = $ErrorData;
        $preview = $this->load->view('BulkWorkflow_partialviews/bulk_imported',$data,true);
        echo json_encode(array('error' => 0, 'html' => $preview)); exit;
      } else {
        echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
      }
    } else {
      echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
    }
  }

  function isEmptyRow($row)
  {
    foreach ($row as $cell) {
      if (null !== $cell) return false;
    }
    return true;
  }

  function BulkCompleteOrders($Orders,$Workflows)
  {
    $WorkflowID = array_keys($Workflows); 
    foreach ($Orders as $key => $OrderUID) 
    {
     $Wrkflow = $this->BulkWorkflow_model->getOrderWorkflows($OrderUID,$WorkflowID);
     $OrderWorkflow = array_column($Wrkflow, 'WorkflowModuleUID');
     foreach ($Workflows as $key => $value) 
     {
       $data = [];
       $orderStatus = [];
       if(!in_array($key, $OrderWorkflow)) { continue; } // Not workflow this order skip
       if($value == 'yes') 
       {
         $data['WorkflowStatus'] = 5;
         $data['CompletedByUserUID'] = $this->loggedid;
         $data['CompleteDateTime'] = date('Y-m-d h:i:s');
         if($key == $this->config->item('Workflows')['DocChase']) // Doc Chase check
         {
           $this->BulkWorkflow_model->ClearDocChase($OrderUID,$key);
         } else {
           /*if($key == $this->config->item('Workflows')['Closing'])
           {
             $orderStatus['StatusUID'] = '';
           } else {
           }*/
           $orderStatus = $this->getOrderStatus($key);
         }
       } else {
         $data['WorkflowStatus'] = 3;
         $data['CompletedByUserUID'] = NULL;
         $data['CompleteDateTime'] = NULL;
       }      
       $OrderAssigned = $this->BulkWorkflow_model->CheckOrderAssigned($OrderUID,$key);
       if($OrderAssigned==100)
       {
         $OrderAssign['OrderUID'] = $OrderUID;
         $OrderAssign['WorkflowModuleUID'] = $key;
         $OrderAssign['AssignedToUserUID'] = $this->loggedid;
         $OrderAssign['AssignedDatetime'] = date('Y-m-d h:i:s');
         $OrderAssign['AssignedByUserUID'] = $this->loggedid;
         $OrderAssign['WorkflowStatus'] = $data['WorkflowStatus'];
         $OrderAssign['CompletedByUserUID'] = $this->loggedid;
         $OrderAssign['CompleteDateTime'] = date('Y-m-d h:i:s');
         $this->BulkWorkflow_model->assignOrderToComplete($OrderAssign);
       } else if($OrderAssigned<>5) {
         $this->BulkWorkflow_model->changeWorkflowStatus($OrderUID,$key,$data);
       }
       if(!empty($orderStatus))
       {
         $this->BulkWorkflow_model->updateOrderStatus($orderStatus,$OrderUID,$key,$OrderWorkflow);
       }
     } 
    } 
  }

  function getOrderStatus($WorkflowModuleUID)
  {
    if($WorkflowModuleUID == $this->config->item('Workflows')['PreScreen'])
    {
      $status['StatusUID'] = $this->config->item('keywords')['PrescreenCompleted'];
    } 
    else if($WorkflowModuleUID == $this->config->item('Workflows')['WelcomeCall']) 
    {
      $status['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
    } 
    else if($WorkflowModuleUID == $this->config->item('Workflows')['TitleTeam']) 
    {
      $status['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
    } 
    else if($WorkflowModuleUID == $this->config->item('Workflows')['FHAVACaseTeam']) 
    {
      $status['StatusUID'] = $this->config->item('keywords')['Pendingdocuments'];
    }
    else if($WorkflowModuleUID == $this->config->item('Workflows')['ThirdPartyTeam']) 
    {
      $status['StatusUID'] = $this->config->item('keywords')['Alldocuments received'];
    }
    else if($WorkflowModuleUID == $this->config->item('Workflows')['Workup']) 
    {
      $status['StatusUID'] = $this->config->item('keywords')['WorkupCompleted'];
    }
    else if($WorkflowModuleUID == $this->config->item('Workflows')['UnderWriter']) 
    {  
      $status['StatusUID'] = $this->config->item('keywords')['UnderwriterCompleted'];
    }
    else if($WorkflowModuleUID == $this->config->item('Workflows')['Scheduling']) 
    {
      $status['StatusUID'] = $this->config->item('keywords')['SchedulingCompleted'];
    }
    else if($WorkflowModuleUID == $this->config->item('Workflows')['Closing']) 
    {
      $status['StatusUID'] = $this->config->item('keywords')['ClosingCompleted'];
    }
    return $status;
  }

  function ExcelFormat()
  {
      $this->load->library('Excel');
      $objPHPExcel = new PHPExcel();
      $objPHPExcel->setActiveSheetIndex(0);
      $styleArray = array(
        'font'  => array(
          'bold'  => true,
          'color' => array('rgb' => 'ffffff'),
          'name'  => 'Calibri'
       ),
      'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => '1F497D')
      ));
      $ColumnArray = array('A1'=>'OrderNumber','B1'=>'LoanNumber','C1'=>'Pre-screen','D1'=>'Welcome Call','E1'=>'Title','F1'=>'FHAVA Case','G1'=>'Third Party','H1'=>'Doc Chase','I1'=>'Workup','J1'=>'Underwriter','K1'=>'Scheduling','L1'=>'Closing');
      $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleArray);
      foreach ($ColumnArray as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue($key, $value);
      }     
      $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Either Doctrac or Loan Number which can be considered as unique');
      $objPHPExcel->getActiveSheet()->setCellValue('B2', 'Yes');
      $objPHPExcel->getActiveSheet()->setCellValue('C2', 'No');
      $objPHPExcel->getActiveSheet()->setCellValue('D2', 'Yes');
      $objPHPExcel->getActiveSheet()->setCellValue('E2', 'No');
      $objPHPExcel->getActiveSheet()->setCellValue('F2', 'Yes');
      $objPHPExcel->getActiveSheet()->setCellValue('G2', 'No');
      $objPHPExcel->getActiveSheet()->setCellValue('H2', 'Yes');
      $objPHPExcel->getActiveSheet()->setCellValue('I2', 'No');
      $objPHPExcel->getActiveSheet()->setCellValue('J2', 'Yes'); 
      $objPHPExcel->getActiveSheet()->setCellValue('K2', 'No'); 
      $objPHPExcel->getActiveSheet()->setCellValue('L2', 'Yes'); 
      
      ob_end_clean();
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="BulkImport_Failed.xlsx"');
      header('Cache-Control: max-age=0');
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
  }

}?>

