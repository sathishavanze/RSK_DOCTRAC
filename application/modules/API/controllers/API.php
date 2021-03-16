<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class API extends MX_Controller {

  /**
  * API for Receive Bot Image
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @since Date July 17th 2020
  */

  function __construct()
  {
    parent::__construct();
    $this->load->model('API_model');
    $this->load->model('Common_Model');
    //error_reporting(E_ALL);
  }

  function index(){

  }

  /**
  * Function to receive Bot Image
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @since July 17th 2020
  * @version Bot Image API
  *
  */

  function ReceiveBotImage(){
    header('Content-Type: application/json');
    $Receive_Response = file_get_contents("php://input"); /* Get Response from API */
    $headers = apache_request_headers(); /* Get headers */
    if($Receive_Response){
      $Auth = $headers['Authorization'];
      if($Auth) {
        $Credrentials = $this->API_model->GetBotCredrentials($Auth); /* Function to get BOT Credentials*/
        $BotAuthKey = $Credrentials['BotAuthkey'];
        if($BotAuthKey) {
          $json_to_array = json_decode($Receive_Response, true); /* Converting from JSON to Array */
          $OrderNumber = $json_to_array['OrderNumber'];
          $ReferenceNo = $json_to_array['ReferenceNo'];
          $OrderDetails = $this->API_model->GetOrderDetails($OrderNumber); /* Get OrderDetails from tOrders table by OrderNumber */
          $OrderUID = $OrderDetails->OrderUID;
          if($OrderNumber){ 
            /* @If File Content are in Base64 */
            if(isset($json_to_array['File'])) {
              $File = $json_to_array['File'];
              $FileContent = base64_decode($File);

              $f = finfo_open();
              $mime_type = finfo_buffer($f, $FileContent, FILEINFO_MIME_TYPE);
              $OrderDetails = $this->API_model->GetOrderDetails($OrderNumber); /* Get OrderDetails from tOrders table by OrderNumber */
              if(!empty($OrderDetails)){
                
                /* Start - PDF file Storing */
                $current_date=date("Y-m-d-h-i-s");
                $randomnumber=str_replace("-","",$current_date);
                $pdf_filename = $OrderNumber.'-BotImage-'.$randomnumber.'.pdf';
                $PDFDocs_Path = 'uploads/BotImage/'.$OrderNumber."/";
                $this->API_model->CreateDirectoryToPath($PDFDocs_Path); /* Create Path for store Bot Image */
                $pdffile = FCPATH.$PDFDocs_Path.$pdf_filename;

                if($mime_type == 'application/pdf'){
                  file_put_contents($pdffile, $FileContent);
                }else {
                  $OrderUID = $OrderDetails->OrderUID;
                  $filename = 'JPGBotImage-'.time().'.jpg';
                  $filename1 = 'JPGBotImage1-'.time().'.jpg';
                  $basepath = FCPATH.'uploads/BotImage/'.$OrderNumber.'/';
                  $this->API_model->CreateDirectoryToPath($basepath); /* Create Path for store Bot Image */
                  $path = $basepath.$filename;
                  $filepath = 'uploads/BotImage/'.$OrderNumber.'/'.$filename;
                  $filepath1 = 'uploads/BotImage/'.$OrderNumber.'/'.$filename1;
                  $saveFile = file_put_contents($path, $FileContent);

                  $image = $filepath;
                  $this->load->library('Mypdf');                
                  $pdf = new mypdf();
                  $pdf->AddPage();
                  $pdf->Image($image,10,10,190);
                  $pdf->Output($pdffile, 'F');
                }
                  /* End - PDF file Storing */
                  $Doc = array(
                    'DocumentName' => $pdf_filename, 
                    'DocumentURL'=> $PDFDocs_Path.$pdf_filename,
                    'OrderUID'=> $OrderUID,
                    'ReferenceNo'=> $ReferenceNo,
                    'IsStacking'=> 0,
                    'TypeofDocument'=> 'BotImage',
                    'DocumentStorage'=> $PDFDocs_Path,
                    'UploadedDateTime'=> date('Y-m-d H:i:s'),
                  );
                  $this->db->insert('tDocuments',$Doc);
                  $DocumentUID = $this->db->insert_id(); /* Insert the PDF file in tOrderDocuments table */
                  unset($Doc);

                  /* Move HOI queue to exception */
                  
                  $QueueUID = $this->config->item('HoiAutomationQueues')['HOIDocumentReceived'];
                  $this->Common_Model->MoveQueue($OrderUID, $QueueUID);
                  
                  /* Automation Log */
                  $fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> 'BOT Receive', 'AutomationStatus' => 'Success', 'CreatedDate' => date('Y-m-d H:i:s'));
                  $this->Common_Model->insert_AutomationLog($fieldArray);
                  unset($fieldArray);

                  $res = array('status' => 1, 'message' => 'BotImage is received');

             } else {
              $res = array('status' => 0, 'message' => "OrderNumber doesn't match");       
            }
          }
        } else {
          $res = array('status' => 0, 'message' => 'Missing of OrderNumber/FileContent');       
        }
      } else {
        $res = array('status' => 0, 'message' => "Invalid Credrentials");
      }
    } else {
      $res = array('status' => 0, 'message' => "Auth Key didn't receive");
    } 
  }else {
    $res = array('status' => 0, 'message' => 'Unable to receive response');
  }

  echo json_encode($res);
}

  /**
  * Function to handle error Bot Image
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @since July 18th 2020
  * @version Bot Image API
  *
  */
  function ErrorBotImage(){
    $Receive_Response = file_get_contents("php://input"); /* Get Response from API */
    $headers = apache_request_headers(); /* Get headers */
    if($Receive_Response){
      $Auth = $headers['Authorization'];
      $json_to_array = json_decode($Receive_Response, true); /* Converting from JSON to Array */
      $OrderNumber = $json_to_array['OrderNumber'];
      $Status = $json_to_array['Status'];
      if($Auth) {
        $Credrentials = $this->API_model->GetBotCredrentials($Auth); /* Function to get BOT Credentials*/
        $BotAuthKey = $Credrentials['BotAuthkey'];
        if($BotAuthKey) {
          if($OrderNumber){
            $OrderDetails = $this->API_model->GetOrderDetails($OrderNumber); /* Get OrderDetails from tOrders table by OrderNumber */
            $OrderUID = $OrderDetails->OrderUID;
            /* Automation Log for Bot receive */
            $fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> 'Bot Receive', 'AutomationStatus' => 'Failure', 'EmailUID' => $lastEUID, 'CreatedDate' => date('Y-m-d H:i:s'));                 
            $this->Common_Model->insert_AutomationLog($fieldArray);
            unset($fieldArray);
            /* Move HOI queue to exception queue if email sent time is more than 48 hours and till there is no response */
            
            $QueueUID = $this->config->item('HoiAutomationQueues')['HOIException'];
            $this->Common_Model->MoveQueue($OrderUID, $QueueUID);
            
            $res = array('status' => 1, 'message' => 'Error Received');
          } else {
            $res = array('status' => 0, 'message' => 'Missing of OrderNumber');
          }
        } else {
          $res = array('status' => 0, 'message' => "Invalid Credrentials");
        }
      } else {
        $res = array('status' => 0, 'message' => "Auth Key didn't receive");
      } 
    } else {
      $res = array('status' => 0, 'message' => 'Unable to receive response');
    }
    echo json_encode($res);
  }
}?>
