<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MilestoneUpdate extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Common_Model');
    $this->load->model('MilestoneUpdate_Model');
	}

	public function index()
	{
		$data['content'] = 'milestoneupdate';
    $data['Projects'] = $this->Common_Model->GetProjectCustomers();
    $data['Clients'] = $this->Common_Model->GetClients();
    $data['Milestone'] = $this->Common_Model->Milestone();
		$data['Users'] = $this->Common_Model->get('mUsers', ['Active'=>STATUS_ONE]);
 		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


  function preview_MilestoneUpdate()
  {
    if(isset($_FILES['file'])) 
    {
      $lib = $this->load->library('Excel'); 
      $inputFile = $_FILES['file']['tmp_name'];
      $filenames = $this->input->post('FILENAMES');
      $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
      $temp = explode(".", $_FILES["file"]["name"]);

      $allowedExts = array("xlsx");

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
        $columnvariables = array(0=>'OrderNumber',1=>'LoanNumber',2=>'MileStone');

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
          $MilestoneName = $values[2];
          $OrderDetails = $this->MilestoneUpdate_Model->ChkOrderisValid($OrderNumber);
          $IsMilestoneExsist = $this->MilestoneUpdate_Model->IsMilestoneExsist($MilestoneName);
          $CheckMilestone = $this->MilestoneUpdate_Model->CheckMilestone($OrderDetails->OrderUID,$MilestoneName);
          if(count($values) == count($columnvariables)) 
          {
            //add regection type
            $Regection =[] ;
            if(empty($OrderDetails->OrderUID)) 
            { 
              $Regection[] = 'Order number is invalid'; 
            } 
            if($LoanNumber != $OrderDetails->LoanNumber) 
            { 
              $Regection[] = 'Loan number is invalid';
            }
            if(empty($IsMilestoneExsist)) 
            {  
              $Regection[] = 'Milestone name is invalid';
            } 
            if(!empty($CheckMilestone))
            {
              $Regection[] = $CheckMilestone['result']->SystemName.' not completed';
            }
            if(!empty($Regection))
            {  
             $values[3] = implode(',', $Regection);
            }
            else
            {
              $values[3] = '-';
            }
            //regection type end
            if( empty($OrderDetails->OrderUID)) 
            { 
              $values['ColorCode'] = "#fff";
              $values['BGColorCode'] = "#756af1"; 
            } 
            else if($LoanNumber != $OrderDetails->LoanNumber) 
            { 
              $values['ColorCode'] = "#fff";
              $values['BGColorCode'] = "#ff04ec";
            }
             else if(empty($IsMilestoneExsist)) 
            {  
              $values['ColorCode'] = "#fff";
              $values['BGColorCode'] = "#32CD32";
            } 
            else if(!empty($CheckMilestone))
            {
              $values['ColorCode'] = "#fff";
              $values['BGColorCode'] = $CheckMilestone['result']->ColorCode;
            }

          } 
          else 
          {
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
        $headingsArray[3] = 'Rejection Message';
        $data['headingsArray'] = $headingsArray;

        $preview = $this->load->view('MileStone_Update/bulk_preview', $data, true);
        $filelink = 'uploads/'.$this->loggedid.'/results.json';
        echo json_encode(array('error' => 0, 'html' => $preview, 'filehtml' => $filepreview, 'filelink' => $filelink)); exit;
      } else {
        echo json_encode(array('error' => '1', 'message' => 'Please Upload Valid File'));
      }
    } else {
      echo json_encode(array('error' => '1', 'message' => 'Please upload File'));
    }

  }

  function save_MilestoneUpdate()
  {
    if(isset($_FILES['file'])) 
    {
      $lib = $this->load->library('Excel'); 
      $inputFile = $_FILES['file']['tmp_name'];
      $filenames = $this->input->post('FILENAMES');
      $extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
      $temp = explode(".", $_FILES["file"]["name"]);

      $allowedExts = array("xlsx");

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
        $columnvariables = array(0=>'OrderNumber',1=>'LoanNumber',2=>'MileStone');

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
        $posts = [];  
        foreach ($arrayCode as $keys => $values) 
        {
          $OrderNumber = $values[0];
          $LoanNumber = $values[1];
          $MilestoneName = $values[2];
          $OrderDetails = $this->MilestoneUpdate_Model->ChkOrderisValid($OrderNumber);
          $IsMilestoneExsist = $this->MilestoneUpdate_Model->IsMilestoneExsist($MilestoneName);
          $CheckMilestone = $this->MilestoneUpdate_Model->CheckMilestone($OrderDetails->OrderUID,$MilestoneName);
    	
          if(count($values) == count($columnvariables)) 
          {
            //add regection type
            $Regection =[] ;
            if(empty($OrderDetails->OrderUID)) 
            { 
              $Regection[] = 'Order Nnumber is invalid'; 
            } 
            if($LoanNumber != $OrderDetails->LoanNumber) 
            { 
              $Regection[] = 'Loan Nnumber is invalid';
            }
            if(empty($IsMilestoneExsist)) 
            {  
              $Regection[] = 'Milestone name is invalid';
            } 
            if(!empty($CheckMilestone))
            {
              $Regection[] = $CheckMilestone['result']->SystemName.' not completed';
            }
            if(!empty($Regection))
            {  
             $values[3] = implode(',', $Regection);
            }
            else
            {
              $values[3] = '-';
            }
            //regection type end

            if(empty($OrderDetails->OrderUID)) 
            {  
              $values['Style'] = "color: #fff; background: #756af1";
              $ErrorData[] = $values; // merge cell style last array
            } 
            else if($LoanNumber != $OrderDetails->LoanNumber) 
            {  
              $values['Style'] = "color: #fff; background: #ff04ec";
              $ErrorData[] = $values;
            }
            else if(empty($IsMilestoneExsist)) 
            {  
              $values['Style'] = "color: #fff; background: #32CD32";
              $ErrorData[] = $values;
            }  
            else if(!empty($CheckMilestone))
            {
               $values['Style'] = "color: #fff; background:".$CheckMilestone['result']->ColorCode;
               $ErrorData[] = $values;
            }
            else 
            {
              $this->MilestoneUpdate_Model->BulkMilestoneUpdate($OrderDetails->OrderUID,$IsMilestoneExsist->MilestoneUID);
              //insert tOrderMileStone
              $this->MilestoneUpdate_Model->InsertOrderMileStone($OrderDetails->OrderUID,$IsMilestoneExsist->MilestoneUID);
              $SuccessData[] = $values;
            }

            
          } 
          else 
          {
            $values['Style'] = "color: #fff; background: #757575";
            $ErrorData[] = $values;
          }
          $posts[] = $values;
        }

        $data['arrayCode'] = $arrayCode;
        $headingsArray[3] = 'Rejection Message';
        $data['headingsArray'] = $headingsArray;
        $data['Success'] = $SuccessData;
        $data['Error'] = $ErrorData;
        $preview = $this->load->view('MileStone_Update/bulk_imported',$data,true);
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
      $ColumnArray = array('A1' => 'OrderNumber','B1'=>'LoanNumber','C1'=>'MileStone');
      $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
      foreach ($ColumnArray as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue($key, $value);
      }     
      $objPHPExcel->getActiveSheet()->setCellValue('A2', '');
      $objPHPExcel->getActiveSheet()->setCellValue('B2', '');
      $objPHPExcel->getActiveSheet()->setCellValue('C2', '');

      ob_end_clean();
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="BulkImport_Failed.xlsx"');
      header('Cache-Control: max-age=0');
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
  }


  function MilestoneExport() 
  {
    //Advanced Search
    $post['advancedsearch'] = $this->input->post('formData');
    $post['length'] = $this->input->post('length');
    $post['start'] = $this->input->post('start');
    $search = $this->input->post('search');
    $post['search_value'] = trim($search['value']);
    $post['order'] = $this->input->post('order');
    $post['draw'] = $this->input->post('draw');     
    $post['column_order'] = array('tOrders.OrderNumber','tOrders.LoanNumber','mMilestone.MilestoneName');
    $post['column_search'] = array('tOrders.OrderNumber','tOrders.LoanNumber','mMilestone.MilestoneName');

    $list = $this->MilestoneUpdate_Model->MilestoneReportOrders($post);   
    $no = $post['start'];
    $MilestoneReportorderslist = [];
    foreach ($list as $key => $revieworders)
    {
        $row = array();
        $row[] = $revieworders->OrderNumber;
        $row[] = $revieworders->LoanNumber;
        $row[] = $revieworders->MilestoneName;
        $Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'"  target="_new" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
        <i class="icon-pencil"></i></a>';
        $row[] = $Action;
      $MilestoneReportorderslist[] = $row;
    }

    $data =  array(
      'MilestoneReportorderslist' => ($MilestoneReportorderslist),
      'post' => $post
    );

    $post = $data['post'];

    $count_all = $this->MilestoneUpdate_Model->count_filtered($post);

    $output = array(
      "draw" => $post['draw'],
      "recordsTotal" => $this->MilestoneUpdate_Model->count_all(),
      "recordsFiltered" =>  $count_all,
      "data" => $data['MilestoneReportorderslist'],
    );

    unset($post);
    unset($data);
    echo json_encode($output);
  }

  function MilestoneExcelExport()
  {
    if($this->input->post('formData') == 'All')
    {
      $post['advancedsearch'] = 'false';
    }
    else
    {
      $post['advancedsearch'] = $this->input->post('formData');
    }
    $list = $this->MilestoneUpdate_Model->MilestoneExcelRecords($post);
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
      $ColumnArray = array('A1' => 'OrderNumber','B1'=>'LoanNumber','C1'=>'MileStone');
      $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
      foreach ($ColumnArray as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue($key, $value);
      }     
     $excelIteration =1;
      for ($i=0; $i < sizeof($list); $i++){
       $excelIteration++; 
       $objPHPExcel->getActiveSheet()->setCellValue('A'.$excelIteration, $list[$i]->OrderNumber);
       $objPHPExcel->getActiveSheet()->setCellValue('B'.$excelIteration, $list[$i]->LoanNumber);
       $objPHPExcel->getActiveSheet()->setCellValue('C'.$excelIteration,  $list[$i]->MilestoneName);
     }

     ob_clean();
     header("Content-Type: text/csv");
     header("Content-Disposition: attachment; filename=file.csv");
     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
     $objWriter->save('php://output');
     ob_flush();
  }
}?>

