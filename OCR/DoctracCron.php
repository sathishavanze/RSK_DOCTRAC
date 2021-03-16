<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);

/**********************************************************************/
/* CONFIG STUFF */
/**********************************************************************/
define('IMAGESPLIT',1);
define('OCR',1);
define('CLASSIFICATION',1);
define('EXTRACTION',1);
define('IS_LIVE',1);
define('DEBUG',0);
define('APP_MODE',"DB");
define('DB_SERVER',"localhost");
define('DB_USER',"root");
define('DB_PASSWORD',"");
define('DB_NAME',"doctrac");
define('LOG_FILE', "/var/www/html/logs/doctrac/DOCTRAC".date("Y-m-d").".log");
define('FILEPATH',"/var/www/html/doctrac.stage/uploads/OrderDocumentPath/");
define('EFAXFILEPATH',"/var/www/html/doctrac.stage/uploads/Efax_files/");
define('PATH',"/var/www/html/doctrac.stage/");

// define('LOG_FILE', "C://xampp/htdocs/doctrac/OCR/cronNew".date("Y-m-d").".log");
// define('FILEPATH',"C://xampp/htdocs/doctrac/uploads/OrderDocumentPath/");
// define('EFAXFILEPATH',"C://xampp/htdocs/doctrac/uploads/Efax_files/");
// define('PATH',"C://xampp/htdocs/doctrac/");

define('keyFile', [
			  "type" => "service_account",
			  "project_id" => "hyper-vision",
			  "private_key_id" => "03ba7aea662870f4c16c89c15d3562bbc1139e4a",
			  "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC/h9U/oyC2GAJx\nDtmHqViqS1oops67py5Eg1aueN67bM6K+Oy9/oo2ytGOASnLW6yZReUmxSd793dg\n5xBLnIATVAEkiaDpsksTCPVM9lIudNKLCoImtq4N14LtPVluvZEsW7Q3AgmK+cBN\nC/vmRy/dj6/ya0GeLkZPpmIdoGZcSU2S0PfPn7fFfno40H6uNiHtHEqexz6HD82V\n5AFWPmxgWJ2lubf6U2meYfXvlzC7yzdgQxh54bFiHZN2Zc3JBnDvHgrsNzfSMoXG\nTOJYSV3307qMNS6uaELCAUDkDiklTEH/M7GWs7zT8J8aPxJ6VU6JsPS43tKdXwHU\njLOxinXHAgMBAAECggEACmzrD1hifXGidbj935k45FF5tODE5rNbnLXG0B8a20+O\n5phUz/q9Ae0u/vB9RT6A5KRIrEBAZ/UOdc3eCzTs8SBkzN0DpJ+PNUK1kjI47aJv\nm3XqTmUp5ZhSM3ZgGdu5EpW4ojiQKxUb10B3E7p9loKDF0pTULhAHNx3rBkruTYu\notTv4bSw8tb4cYV4OyCpGU7iO8AgUBq5KV2VLu8wpxdKI1/XZjdv3KbHci824xW7\nPCTJjMDNf10g1+4G1FpQdUCQiC+r5VvSxH8iwllLc3LJF6jNTRF8T8XK7iW8FVvV\nFe8+/8Jgf2gQVLNXePgKCOVOhwkiLtPuUWBDSnjQUQKBgQD6Z/lVXRMeknvbpL2S\nVF/P9DmT0nAfr3K2kNYb4OK8Em6RAVop2xGailBKZcOe6GrtJEEuCVKwTbeEA+tq\nDBgmpgk4qjQmGscCRQ5jBwJC5sA3aewed24thKznU8n2rcUfw5xJLVXi2ebmwyZE\nt6laaAejgXo6g4l1A8hZDuhBKwKBgQDDzyknNZsYZUwnzOp3kyKcdrXGotX93OXE\nGqAPhZj0mhUEv5i31fOhVeabD0HEd+gW0FCKmSaYlPOoCJcWfpte1zFl8Ei4d0je\nC8CVgU6JGMMBBVlO8b3Y3nPHdbbnkHWXglNDKqSvs0mz+slHWe8NFeRMpZinabiq\n0GP0DuQ31QKBgBRebs690ndTLRYR0YXcrSqKUECxBoPdTrSBFZmQ8oCf8zSCaqYq\n9sSeaaeTnZUIjOIbZ93pzjsfJ0Vq2DR5JWHHccqVNca/uN57ZY8ym8F7ablYOWfF\nCUZUm4f5rd0OB5Hs5IKjWHAqj3jH+Q3v7qu2+KAZQSsPVhgZpatw5f2fAoGAJv5L\nKLbBIxORHd/t1VAktVniiyMEUGC8FNhkhZLKSfOhOefZxspBkSBlIv3fz/s86cYk\nM+WZzQrkltLXG7Csyi86dB1sred7SFS4zk0I0b5vnvVsuo7jipuKHO9xNB4NfNWE\nNTz5vM+MXt4TVTUwqL9yTl0v0C76inGjZgfNJN0CgYEAgy1QG7Zz5xzfYjLIVzi6\nSQBIMzAR5wJrMrKEttxWPF+lpVPkYx/PrsvwKRQTCP2D/bCcOyrTR3t+K9SKUvCX\n4cn6Anr2g1yUDDPOYB2BUWof/pC0bX0d7cmaCU8fExydx8DCcxZw8ua+VQbxxgct\nY1p+i+RLmyoMRo5SEEYzyNs=\n-----END PRIVATE KEY-----\n",
			  "client_email" => "748884811903-compute@developer.gserviceaccount.com",
			  "client_id" => "109516014158600068350",
			  "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
			  "token_uri" => "https://oauth2.googleapis.com/token",
			  "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
			  "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/748884811903-compute%40developer.gserviceaccount.com"
			]);


/**********************************************************************/
/* db STUFF */
/**********************************************************************/
include("utils.php");
define('REQIP',get_ip());
define('REQID',uniqid());
include("db.php");
include("googlevision_model.php");
//include("tessaract_model.php");
include("controller.php");
//require "../vendor/autoload.php";

#db_connect();
#Send_OCR_Details();
#Receive_OCR_Details($Json_request="");
//$result = json_decode(file_get_contents("php://input"),TRUE);
//echo OCR($result);exit;

function run_OCR($Params)
{
	LOG_MSG('INFO','### DOCTRAC Cron START ### \n');
	//return '{"sourceApp":"DOCTRAC","orderNo":"4982","orderUID":"25458","data":{"487":[{"docName":"AAA Insurance","docTypeUID":"2","pageConfidence":0.75,"pageNo":487,"extractedData":[{"InsuranceCompany":"AAA Insurance","Policy Number":" NVH3-109143693","Policy Type":" Homeowners - Heritage","Insured Location":"9132 Pine Mission Ave, Las Vegas, NV 89143 ","Policy Start Date":"12\/08\/2019 ","Policy Exp Date":"12\/08\/2020 ","Dwelling Amount":"318,571"}]}],"488":[{"docName":"AAA Insurance","docTypeUID":"2","pageConfidence":0,"pageNo":488}]}}';

	$response = OCR($Params);
	LOG_MSG('INFO','### DOCTRAC Cron END ### \n');
	return $response;
}

	/*function for Input details JSON format*/
function Send_OCR_Details(){
	LOG_MSG('INFO','Send_OCR_Details(): START \n');
	db_connect();
	//Staus image Received and is stacking --OCR Start--OCR Completed--

	$tOrders = db_get_list('ARRAY','o.ProjectUID,o.OrderUID,o.OrderNumber,td.DocumentURL','tOrders AS o JOIN tDocuments AS td ON td.OrderUID=o.OrderUID ',' o.StatusUID=100 AND td.IsStacking=1  AND o.OrderUID > 13774 ORDER BY o.OrderUID LIMIT 1');

	if(DEBUG) print_r($tOrders);echo "\n";

	/*validation for order with particular ID*/
	if($tOrders[0]['NROWS']){			
		if (file_exists(PATH.'docDef/docDef-'.$tOrders[0]['OrderUID'].'.json')) {
			$docdef=file_get_contents(PATH.'docDef/docDef-'.$tOrders[0]['OrderUID'].'.json');
		}else{
			$docdef=writeJson($tOrders[0]['OrderUID']);
			$docdef=file_get_contents(PATH.'docDef/docDef-'.$tOrders[0]['OrderUID'].'.json');
		}


			// /*get document path for an order*/
		$FilePath = PATH.$tOrders[0]['DocumentURL'];
		
		/*created JSON array values*/
		if (file_exists($FilePath)){
			$PageConfidence = array(
				"sourceApp"=>"DOCTRAC",
				"orderNo"=>$tOrders[0]['OrderNumber'],
				#"orderNo"=>'S19025455',
				"orderUID"=>$tOrders[0]['OrderUID'], 
				"docType"=> "PDF",
				"source"=> $FilePath,
				"features"=> "DOCUMENT_TEXT_DETECTION",
				"featuresInput"=> "",
				"outputFormat"=> "JSON",
				"outputLocation"=> "",
				"engine"=> "TESSERACT",
				"docDef"=> "",
			);
		}else{
			db_update('UPDATE',"StatusUID='15'",'tOrders','OrderUID='.$tOrders[0]['OrderUID']);
			Send_OCR_Details();
		}
		db_update('UPDATE',"StatusUID='15'",'tOrders','OrderUID='.$tOrders[0]['OrderUID']);
		db_close();	
		OCR($PageConfidence);
	}
	else{
		db_close();	
		echo json_encode(['Status'=>"Failed", "message"=>"No Order Found"]);
	}
	LOG_MSG('INFO','Send_OCR_Details(): END \n');
	Send_OCR_Details();
}

function Receive_OCR_Details($Json_request){
	LOG_MSG('INFO','Receive_OCR_Details(): START \n');
	db_connect();

	$Fetch_Request_Details = json_decode($Json_request);
	$data['OrderUID'] = $Fetch_Request_Details->orderUID;
	$data['orderNo'] = $Fetch_Request_Details->orderNo;
	$data['data'] = $Fetch_Request_Details->data;	
	$tOrders = db_get_list('ARRAY','ProjectUID,OrderUID,OrderNumber','tOrders','OrderNumber LIKE "'.$data['orderNo'].'" AND OrderUID='.$data['OrderUID']);	
	/*validation for order with particular ID*/
	if(empty($tOrders)){
		echo json_encode(["message"=>"Invalid Request", "Status"=>"Failed" ]); exit();
	}
	// $mProjectCategory = db_get_list('ARRAY','*','mProjectCategory ','ProjectUID='.$tOrders[0]['ProjectUID']);
	// /*fetch category details*/
	// for($i=0;$i<$mProjectCategory[0]['NROWS'];$i++){
	// 	$mDocumentType = db_get_list('ARRAY','*','mDocumentType ','CategoryUID='.$mProjectCategory[$i]['CategoryUID']);
	// 	if(!empty($mDocumentType)){
	// 		$DocTypes[$mDocumentType[0]['DocumentTypeName']] = $mDocumentType[0]['DocumentTypeUID'];
	// 	}
	// }


	$DocumentTypeObjects = new stdClass();
	$i=0;

	foreach ($data['data'] as $key => $fetchValue) {
		$object = (array)$fetchValue;
		$DocumentTypeName = key($object);
		$confidence = '';
		/*comparation of the orders details*/
		foreach ($object as $objkey => $objvalue) {
			$mDocumentType = db_get_list('ARRAY','*','mDocumentType ','DocumentTypeUID='.$objvalue->docTypeUID);

			if(!empty($mDocumentType)){
				$SubCategoryName = $mDocumentType[0]['HashCode'];
				$mCategory = db_get_list('ARRAY','*','mCategory ','CategoryUID='.$mDocumentType[0]['CategoryUID']);	
				if(!empty($mCategory)){
					$CategoryName = $mCategory[0]['HashCode'];
				}
			}
			/*Insert details in tPage Table*/
			$PageConfidenceDetails = array(														
				'OrderUID' => $data['OrderUID'],
				'DocumentUID' => $objvalue->docTypeUID,
				'CategoryName' => $CategoryName,
				'SubCategoryName' => $SubCategoryName,
				'PagePosition' => '',								
				'LogicPageNumber' => $i,
				'PageConfidence' => $objvalue->pageConfidence,					
				'PageNo' => $objvalue->pageNo,
			);		
			$PageConfidence = $objvalue->pageConfidence;					
			$PageNo = $objvalue->pageNo;

			$InsertPage = SavePage(
				$data['OrderUID'],
				$objvalue->pageNo,
				$CategoryName,
				$SubCategoryName,
				0,
				$objvalue->pageNo,
				$objvalue->pageConfidence,
				$objvalue->docTypeUID
			);
			if(DEBUG) print_arr($InsertPage);	
		}
	}
	db_update('UPDATE',"StatusUID='16'",'tOrders','OrderUID='.$data['OrderUID']);
	db_close();
	LOG_MSG('INFO','Receive_OCR_Details(): END \n');
}


function SavePage(
				  	$OrderUID,
				  	$PageNo,
				  	$CategoryName,
				  	$SubCategoryName,
				  	$PagePosition,
				  	$LogicPageNumber,
				  	$pageConfidence,
				  	$DocumentUID
				   ){
	$param_arr=_init_db_params();
	LOG_MSG('INFO',"SavePage(): START { 
										OrderUID=[$OrderUID],
										PageNo=[$PageNo],
										CategoryName=[$CategoryName],
										SubCategoryName=[$SubCategoryName],
										PagePosition=[$PagePosition],
										LogicPageNumber=[$LogicPageNumber],
										pageConfidence=[$pageConfidence],
										DocumentUID=[$DocumentUID]	\n}"); 
	// Add params to params_arr
	$param_arr=_db_prepare_param($param_arr,"i","OrderUID",$OrderUID);
	$param_arr=_db_prepare_param($param_arr,"s","PageNo",$PageNo);
	$param_arr=_db_prepare_param($param_arr,"s","CategoryName",$CategoryName);
	$param_arr=_db_prepare_param($param_arr,"s","SubCategoryName",$SubCategoryName);
	$param_arr=_db_prepare_param($param_arr,"i","PagePosition",$PagePosition);
	$param_arr=_db_prepare_param($param_arr,"i","LogicPageNumber",$LogicPageNumber);
	$param_arr=_db_prepare_param($param_arr,"d","pageConfidence",$pageConfidence);
	$param_arr=_db_prepare_param($param_arr,"i","DocumentUID",$DocumentUID);


	$resp=execSQL("INSERT INTO 
	tPage
	(".$param_arr['fields'].")
	VALUES
	(".$param_arr['placeholders'].")"
	,$param_arr['params'], 
	true);

	LOG_MSG('INFO',"SavePage(): END");
	return $resp;

}

function writeJson($ProjectUID){
	$con=new mysqli(DB_SERVER,DB_USER,DB_PASSWORD,DB_NAME) or die(mysqli_error());
	
	$docdef = mysqli_query($con,"SELECT * FROM mDocDef");
	$keywords = mysqli_query($con,"Select * from mkeywords");
	$num_rows = mysqli_num_rows($docdef);
	//$keywords = db_get_list('ARRAY','*','mkeywords','');
		$def='{"Count":"'.$num_rows.'",';
		$count=0;
		foreach ($docdef as $d) {
			$mand='';
				$normal='';
				$count_mand=0;
				$count_norm=0;
				
			foreach ($keywords as $k) {
				
				if ($d['docdef_id']==$k['docdef_id'] && $k['is_mandatory']==1) {
					$count_mand++;
					if ($count_mand>1) {
						$mand.=',';
					}else{
						$mand.='';
					}
					$explode=explode(',', $k['keywords']);
					$string='"'.implode('","',array_unique($explode)).'"';
					$mand.='{"'.$k['key_type'].'":['.$string.']}';

				}
				

				if ($d['docdef_id']==$k['docdef_id'] && $k['is_mandatory']==0) {
					$count_norm++;
					if ($count_norm>1) {
						$normal.=',';
					}else{
						$normal.='';
					}
					$explode=explode(',', $k['keywords']);
					$string='"'.implode('","',array_unique($explode)).'"';
					$normal.='{"'.$k['key_type'].'":['.$string.']}';
				}
				
			}
			$count++;
			//print_r($count."\n");
			$def.='"'.$d['docType'].'":{"Header":"'.$d['header_len'].'","Footer":"'.$d['footer_len'].'","KeyWordCutOff":"'.$d['KeyWordCutOff'].'","LowerCaseSearch":"'.$d['LowerCaseSearch'].'","Confidence":"'.$d['min_confidence'].'","docTypeUID":"'.$d['docTypeUID'].'","KeywordsCount":"'.$d['key_count'].'","MandatoryKeywordsCount":"'.$d['mand_key_count'].'","MandatoryKeywords":['.$mand.'],"Keywords":['.$normal.']}';
			if ($num_rows>1 && $num_rows!=($count)) {
				$def.=',';
			}else{
				$def.='';
			}
		}
		$def.='}';

	$fp = fopen(PATH.'docDef/docDef-'.$ProjectUID.'.json', 'w');
			chmod(PATH.'docDef/docDef-'.$ProjectUID.'.json', 0777); 
			chown(PATH.'docDef/docDef-'.$ProjectUID.'.json', 'www-data');
			fwrite($fp,$def);
			fclose($fp);
			$docdef=PATH.'docDef/docDef-'.$ProjectUID.'.json';
			return $docdef;
	}
	?>
