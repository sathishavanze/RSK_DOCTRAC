<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class PageConfidence extends MX_Controller {

	/*Construct function*/
	function __construct()
	{
		parent::__construct();
		$this->load->model('PageConfidence_model');
	}

	/*function for Input details JSON format*/
	function Send_OCR_Details($OrderNumber="")
	{
		// foreach for order
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderNumber'=>$OrderNumber]);
		/*validation for order with particular ID*/
		if(!empty($tOrders))
		{			
			$DocTypes = [];
			$mProjectCategory = $this->Common_Model->get('mProjectCategory', ['ProjectUID'=>$tOrders->ProjectUID]);
			$tDocuments = $this->Common_Model->get_row('tDocuments', ['OrderUID'=>$tOrders->OrderUID, 'IsStacking'=>1]);
			$FilePath = "";
			/*get document path for an order*/
			if (!empty($tDocuments) && file_exists(FCPATH . $tDocuments->DocumentURL)) {
				$FilePath = FCPATH . $tDocuments->DocumentURL;
				$OutputPath = '/var/www/html/HyperVision/vision/';
				$this->Common_Model->CreateDirectoryToPath($OutputPath);
				file_put_contents($OutputPath . basename($FilePath), file_get_contents($FilePath));
			}
			foreach ($mProjectCategory as $key => $value) {
				$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['CategoryUID'=>$value->CategoryUID]);
				if(!empty($mDocumentType))
				{
					$DocTypes[$mDocumentType->DocumentTypeName] = $mDocumentType->DocumentTypeUID;
				}
			}

			$DocumentTypeObjects = new stdClass();
			$countDocs = count($DocTypes);
			/*^^^^ Made Static two Documenttypes only as asked by Ramashankar*/
			// $DocumentTypeObjects->Count = $countDocs;
			$DocumentTypeObjects->Count = 2;
			foreach ($DocTypes as $key => $value) {
				$ConfObj = new stdClass();
				/*^^^^ Made Static two Documenttypes only as asked by Ramashankar*/
				/*^^^^ Remove if check once Demo is over ^^^^^*/
				if ($key=="1003 Residential loan Application") {
					
					$ConfObj->docuemtTypeUID = $value;
					$ConfObj->Confidence = 0.2;
					$ConfObj->KeywordsCount = 15;
					$ConfObj->MandatoryKeywordsCount = 1;
					$ConfObj->MandatoryKeywords = ["loan"];
					$ConfObj->Keywords = ["Taxes","Contract","Residential","loan","application","Sales","Person","Servicer","Property","mac","form","65","HUD/VA","Addendum","Fannie"];
					$DocumentTypeObjects->$key = $ConfObj;
				}
				elseif($key=="Appraisal Report"){
					$ConfObj->docuemtTypeUID = $value;
					$ConfObj->Confidence = 0.2;
					$ConfObj->KeywordsCount = 12;
					$ConfObj->MandatoryKeywordsCount = 1;
					$ConfObj->MandatoryKeywords = ["Uniform"];
					$ConfObj->Keywords = ["Front","Real","Street","photograph","addendum","living","shed","kitchen","dining","bath","bedroom","furnace"];
					$DocumentTypeObjects->$key = $ConfObj;

				}
			}
			/*created JSON array values*/
			$PageConfidence = array(
				"sourceApp"=>"Doctrac",
				"orderNo"=>$tOrders->OrderNumber,
				"orderUID"=>$tOrders->OrderUID, 
				"docType"=> "PDF",
				"source"=> $FilePath,
				"features"=> "DOCUMENT_TEXT_DETECTION",
				"featuresInput"=> "path of the image/icon",
				"outputFormat"=> "JSON",
				"outputLocation"=> "path of the output file",
				"engine"=> "TESSERACT",
				"docDef"=> $DocumentTypeObjects
				);


			// CURL OCR Operations comes here

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "http://insig.direct2title.com/vision/controller.php",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($PageConfidence),
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json",
					"cache-control: no-cache"
					),
				));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				echo $response;
			}


			// CURL OCR Operations comes here

			 // echo json_encode($PageConfidence);
		}
		else{
			echo json_encode(['Status'=>"Failed", "message"=>"Invalid Order Number"]);
		}
	}

	function Receive_OCR_Details()
	{
		$Json_request = file_get_contents('php://input');
		echo '<pre>';print_r($Json_request);
		/*sample input details*/
		// $Json_request = '{"sourceApp":"StacX","orderNo":"S19000052","orderUID":"52","data":[{"2":{"pageConfidence":0.9,"pageNo":1},"Deeds":{"pageConfidence":0.9,"pageNo":1}},{"2":{"pageConfidence":0,"pageNo":11},"4":{"pageConfidence":0.8,"pageNo":11}},{"2":{"pageConfidence":0,"pageNo":10},"4":{"pageConfidence":0.6,"pageNo":10}},{"2":{"pageConfidence":0.75,"pageNo":12},"4":{"pageConfidence":0.7,"pageNo":12}}]}';
		// $Json_request = '{ "sourceApp":"StacX","orderNo":"ST0000000123", "data":[{"1":[{"docName":"Mortgages","docTypeUID":"123","pageConfidence":0,"pageNo":1},{"docName":"Deed","docTypeUID":"125","pageConfidence":0.4,"pageNo":1}],"2":[{"docName":"Deeds","docTypeUID":"124","pageConfidence":0.4,"pageNo":2},{"docName":"Mortgages", "docTypeUID":"123", "pageConfidence":0,"pageNo":10}, {"docName":"Mortgages", "docTypeUID":"123", "pageConfidence":0.6, "pageNo":10}]}] }';
		$Fetch_Request_Details = json_decode($Json_request);
		$data['OrderUID'] = $Fetch_Request_Details->orderUID;
		$data['orderNo'] = $Fetch_Request_Details->orderNo;
		$data['data'] = $Fetch_Request_Details->data;		
		$tOrders = $this->Common_Model->get_row('tOrders', ['OrderNumber'=>$data['orderNo']]);
		/*validation for order with particular ID*/
		if(empty($tOrders))
		{
			echo json_encode(["message"=>"Invalid Request", "Status"=>"Failed" ]); exit();
		}
			$mProjectCategory = $this->Common_Model->get('mProjectCategory', ['ProjectUID'=>$tOrders->ProjectUID]);
			/*fetch category details*/
			foreach ($mProjectCategory as $key => $value) {
				$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['CategoryUID'=>$value->CategoryUID]);
				if(!empty($mDocumentType))
				{
					$DocTypes[$mDocumentType->DocumentTypeName] = $mDocumentType->DocumentTypeUID;
				}
			}
			$DocumentTypeObjects = new stdClass();
			$i=0;
			$this->db->query("DELETE FROM tCategory WHERE OrderUID = " . $tOrders->OrderUID);
			$this->db->query("DELETE FROM tSubCategory WHERE OrderUID = " . $tOrders->OrderUID);
			$this->db->query("DELETE FROM tPage WHERE OrderUID = " . $tOrders->OrderUID);

			/*document type details*/
			foreach ($DocTypes as $key => $value) {
				$i++;
				$ConfObj = new stdClass();
				$ConfObj->Confidence = 0.2;
				$DocumentTypeObjects->$key = $ConfObj;

				foreach ($data['data'] as $key => $fetchValue) {
					$object = (array)$fetchValue;
					$DocumentTypeName = key($object);

					$confidence = '';
					echo "DocumentTypeName: ";
					print_r($object);
					/*comparation of the orders details*/
					foreach ($object as $objkey => $doctypearray) {
						if($value == $doctypearray->docTypeUID)
						{
							if($doctypearray->pageConfidence >= $ConfObj->Confidence)
							{
								$mDocumentType = $this->Common_Model->get_row('mDocumentType', ['DocumentTypeUID'=>$value]);

								if(!empty($mDocumentType))
								{
									$SubCategoryName = $mDocumentType->HashCode;
									$mCategory = $this->Common_Model->get_row('mCategory', ['CategoryUID'=>$mDocumentType->CategoryUID]);
									if(!empty($mCategory))
									{
										$CategoryName = $mCategory->HashCode;
									}
								}

								$tCategory_row = ['OrderUID'=>$data['OrderUID'], 'CategoryName'=>$CategoryName, 'CategoryPosition'=>$i];
								$tSubCategory_row = ['OrderUID'=>$data['OrderUID'], 'SubCategoryName' => $SubCategoryName, 'ParentCategoryName' => $CategoryName, 'SubCategoryPosition' => $i];

								$this->Common_Model->save("tCategory", $tCategory_row);
								$this->Common_Model->save("tSubCategory", $tSubCategory_row);

								/*Insert details in tPage Table*/
								$PageConfidenceDetails = array(														
									'OrderUID' => $data['OrderUID'],
									'DocumentUID' => $value,
									'CategoryName' => $CategoryName,
									'SubCategoryName' => $SubCategoryName,
									'PagePosition' => $i,								
									'LogicPageNumber' => $i,
									'PageConfidence' => $doctypearray->pageConfidence,					
									'PageNo' => $objkey,
									);								
								$InsertPage = $this->PageConfidence_model->SavePage($PageConfidenceDetails);
							}
						}
						continue 2;
					}

				}
			}

			echo json_encode(["message"=>"Finished","status"=>"Done"]); exit();

	}

}
?>
