<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class EmailReport extends MX_Controller {

	/*Construct function*/
	function __construct()
	{
		parent::__construct();
		$this->load->model('EmailReport_model');
		$this->load->library('form_validation');
		//require_once APPPATH."/third_party/PHPExcel/Classes/PHPExcel.php"; 
        $this->load->library('Excel');
        $this->load->library('email');
        $this->config->load('email', FALSE, TRUE);
		$this->UserUID = $this->session->userdata('UserUID');
      /* ob_clean();
       ob_start();*/ 
	}	

	function index()
	{      
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
		$getReportUploadDocs = $this->EmailReport_model->ReportUploadDocuments();
        $this->excel();         
        foreach ($getReportUploadDocs as $value) {  
        $OrderUID = $value->OrderUID;          
			$ExceptionUserMail = array(
				'OrderUID' => $OrderUID, 
				'LoanNumber' => $value->LoanNumber, 
				'Subject' => "Exception Report", 
				'Message' => "Here we have attached a file, that's an list of the exception. Order ID is <b>".$OrderUID."</b> order", 
				'CreatedBy' => $this->UserUID, 
                'From' => 'bhuvaneswari.a@avanzegroup.com', 
				'To' => 'bhuvaneswari.a@avanzegroup.com', 
				);
			$InsertEcxecptionMail = $this->Common_Model->save('tExceptionMailReport', $ExceptionUserMail);		            
			$result = $this->email
			->from('bhuvaneswari.a@avanzegroup.com')
			->to('bhuvaneswari.a@avanzegroup.com')			
			->subject($OrderUID.' has been raised an exception')
			->message("Here we have attached a file, that's an list of the exception. Order ID is <b>".$OrderUID."</b> order")
            ->attach(FCPATH.'uploads/EmailReport/ExceptionReport.xlsx')
			->send();
		}

        //echo '<pre> hi';print_r($this->email->print_debugger());exit;
        echo json_encode(["Message"=>"Finished","Status"=>"Success"]); exit();
	}

	public function excel()
    {          
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $ColumnArray = array('A','B','C','D','E','F','G');
        foreach ($ColumnArray as  $value) {
           $objPHPExcel->getActiveSheet()->getStyle($value.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
           ->getStartColor()->setRGB('003366');
        }
        $styleArray = array('font'  => array('bold'  => true,'color' => array('rgb' => 'ffffff')));
        $ColumnArray = array('A','B','C','D','E','F','G');
        $objPHPExcel->getActiveSheet()->setCellValue('A1','Exception Report');
        
        $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');

        foreach ($ColumnArray as  $value) {
          $objPHPExcel->getActiveSheet()->getStyle($value.'1')->applyFromArray($styleArray);
        }
        
        $styleArray = array('font'  => array('bold'  => true,'color' => array('rgb' => 'C3C3C3')));
        $ColumnArray = array('A3'=>'S.No','B3'=>'Order ID','C3'=>'Customer Name','D3'=>'Lender ID','E3'=>'Loan Number','F3'=>'Status','G3'=>'Customer Ref Number'); 
        foreach ($ColumnArray as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue($key, $value);
        }
        $n=4;
        $rs = $this->EmailReport_model->ReportUploadDocuments();
       
        $i=0;
        foreach($rs as $row)
        {
            $i++;            
            $CusName = $this->Common_Model->get_row('mCustomer', ['CustomerUID'=>$row->CustomerUID]);
            $Statusid = $this->Common_Model->get_row('mStatus', ['StatusUID'=>$row->StatusUID]); 
            // echo '<pre>';print_r($Statusid->StatusName);exit;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$n, $i);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$n, $row->OrderUID);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$n, $CusName->CustomerName);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$n, $row->LenderUID);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$n, $row->LoanNumber);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$n, $Statusid->StatusName);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$n, $row->CustomerReferenceNumber);
            $n++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // sleep(10);
        // If connected
        if (!file_exists('uploads/EmailReport/')) {
            if (!mkdir('uploads/EmailReport',0777,true)) 
            {
                die('Unable to creat directory'); 
                exit;
            }
        }
        // File creation
        $FileName ='ExceptionReport_'; 
        $ReportPath='uploads/EmailReport/ExceptionReport.xlsx';
        $objWriter->save($ReportPath);       
        //return $FileName;        
    }


}
?>
