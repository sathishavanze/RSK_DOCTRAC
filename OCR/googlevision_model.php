<?php 


use Google\Cloud\Vision\VisionClient;

$resultData = array();
$missedPages=array();
function googlevision($allData){    
    // print_r($allData);exit;
    LOG_MSG('INFO','Google(): START \n');
    $filepath=$allData["source"];
    GLOBAL $countTime;
    $countTime=0;
    if (file_exists($filepath)){ 
   
        // Split function to extract filename
        $result_array = [];
        $result['sourceApp']=$allData['sourceApp'];
        $result['orderNo']=$allData['orderNo'];
        $result['orderUID']=$allData['orderUID'];
        $result['data'] = [];
        $url= $allData['source'];
        $parts = explode("/", $url);
        $content['source']= end($parts);
        $split_name=explode(".", $content['source']);
        // end of split;
                 
        // check if directory exists
        if (!is_dir(FILEPATH.$allData['orderNo'])) {
            mkdir(FILEPATH.$allData['orderNo'], 0777, true);
        }
        if (!is_dir(FILEPATH.$allData['orderNo']."/text_gv")) { 
            mkdir(FILEPATH.$allData['orderNo']."/text_gv", 0777, true);
        }
        
        $start_time = microtime(true); 

        if ($allData["docType"] == 'pdf' || $allData["docType"] == 'PDF') {
            if (!is_dir(FILEPATH.$allData['orderNo']."/images")) {
                mkdir(FILEPATH.$allData['orderNo']."/images", 0777,true);                       
            }
            $resultData=getSplitPdfResultGV($allData);          

        }else{
            $resultData=getImageResultGV($allData); 
        }

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time); 

        $result['data']=$resultData;    
        LOG_MSG('INFO','Google Vision(): END \n');  

        if(DEBUG) print_r("countTime : " .$countTime."\n");
        if(DEBUG) print_r("execution_time : " .$execution_time."\n");

            LOG_MSG('INFO','Google Vision(): END \n');  

        return json_encode($result);
        //Delete directory after result     
        //deleteDirectory(FILEPATH.$allData['orderNo']);
        //Receive_OCR_Details(json_encode($result));                
    } 
    else{ 
        if(DEBUG) echo "The file $filepath does not exists"; 
    LOG_MSG('INFO','Google Vision(): END \n');  
    } 
}

function getSplitPdfResultGV($allData){
    LOG_MSG('INFO','getSplitPdfResult(): START \n');

    GLOBAL $resultData;
    $filepath=$allData["source"];
    $url= $allData['source'];
    $parts = explode("/", $url);
    $content['source']= end($parts);
    $split_name=explode(".", $content['source']);
        
    // Convert pdf to image
    if(IMAGESPLIT){
        $cmd="gs -dSAFER -sDEVICE=jpeg -dINTERPOLATE -dNumRenderingThreads=8 -o ".FILEPATH.$allData['orderNo'].'/images/'.$split_name[0]."-%01d.jpg -r300 ".$filepath;
        $res = exec($cmd);
    }
    $imagePath=FILEPATH.$allData['orderNo'].'/images/'.$split_name[0]."-%01d.jpg";
    LOG_MSG('INFO','getSplitPdfResult(): Convert to image {imageFile=['.$imagePath.'],} \n');
    // Get count of PDF pages
    //scandir() to get the files and then array count to get the page count,
    // $cmd='gs -q -dNODISPLAY -c "('.$filepath.') (r) file runpdfbegin pdfpagecount = quit"';
    // $count=exec($cmd);

    $op=array_diff(scandir(FILEPATH.$allData['orderNo'].'/images/'), array('..', '.'));
    $count = count($op);

    if($count < 1){
        $op=array_diff(scandir(FILEPATH.$allData['orderNo'].'/text_gv/'), array('..', '.'));
        $count = count($op);    
    }

    // $count=2;
    $filename=FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0];
    if(OCR){

        for ($i=1; $i <=$count ; $i++) {    
            if (!file_exists(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$i.".txt")){       
            LOG_MSG('INFO','getSplittedPdfResult(): tesseractInstance START  \n');              

            $vision     = new VisionClient(['keyFile' => keyFile]);
            $myTest     = fopen(FILEPATH.$allData['orderNo'].'/images/'.$split_name[0]."-".$i.'.jpg', 'r');
            $image      = $vision->image($myTest, [ $allData['features'] ]);
            $result     = $vision->annotate($image)->text();
            $ocrResult  = (!empty($result))? $result[0]->info()['description'] : "";

            LOG_MSG('INFO','getSplittedPdfResult(): tesseractInstance END \n');         
            $fp = fopen(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$i.'.txt', 'w');

            $cmd = "sudo chown -R www-data:www-data ".FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$i.".txt";
            $res = exec($cmd);
            $cmd = "sudo chmod -R 777 ".FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$i.".txt";
            $res = exec($cmd);

            //chmod(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$i.".txt", 0777); 
            //chown(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$i.".txt", 'www-data');
            fwrite($fp,$ocrResult);
            fclose($fp);
            $path=FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$i.'.txt';
            LOG_MSG('INFO','getSplittedPdfResult(): Writting {TextFile=['.$path.'],} \n');      
            }           
        }
    }
    
    // CLASSIFICATION  is config in StacxCron
    if(CLASSIFICATION){
        for ($m=1; $m <=$count ; $m++) { 
            $results = checkConfidenceGV(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0]."-".$m.".txt",$allData, $m,$filename);
        }
    }

    if(OCR){

        $cmd = " rm -R ".FILEPATH.$allData['orderNo']."/images";
        $res = exec($cmd);
    }

    LOG_MSG('INFO','getSplitPdfResult(): END \n');
    if(DEBUG) print_arr($resultData);echo "\n";
    return $resultData;
}

function getImageResultGV($allData){
    LOG_MSG('INFO','getImageResult(): START \n');
    GLOBAL $resultData;
    $filepath=$allData["source"];
    $url= $allData['source'];
    $parts = explode("/", $url);
    $content['source']= end($parts);
    $split_name=explode(".", $content['source']);
                
    $tesseractInstance = new TesseractOCR($filepath);
    $pageNo=1;
    $result = $tesseractInstance->run();
    
    $fp = fopen(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0].'.txt', 'w');
    chmod(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0].".txt", 0777); 
    chown(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0].".txt", 'www-data');     
    fwrite($fp, json_encode($result));
    fclose($fp);
    $filename=FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0];
    checkConfidenceGV(FILEPATH.$allData['orderNo']."/text_gv/".$split_name[0].".txt",$allData['docDef'], $pageNo,$filename);                
    LOG_MSG('INFO','getImageResult(): END \n');
    return $resultData;
}

function checkConfidenceGV($txtFile,$data,$pageNo,$filename){

    LOG_MSG('INFO','checkConfidence(): START \n');
    
    $ocrText    = file_get_contents($txtFile);      
    $fdata      = json_decode($data['docDef']);

    if(DEBUG) print_r("======================================== \n");
    if(DEBUG) print_r("textfile URL : ".$txtFile."\n");
    if(DEBUG) print_r("OcrText : ".$ocrText."\n");
    if(DEBUG) print_r("======================================== \n");



    GLOBAL $tempHeader,$tempFooter,$tempBody,$resultData,$countTime,$lastMatchDocTypeUID,$lastMatchPageNo;
    $tempHeader="";$tempFooter="";$tempBody="";$Count=0;$pageConfidence=0;$count_words = 0;
    //$lastMatchDocTypeUID="";
    //$lastMatchPageNo="";

    if(DEBUG) print_r('============ PAGENO:' .$pageNo. " ============\n");

    // Document Type Array loop
    foreach ($fdata as $key => $value){ 
        if ($key == 'Count') {
            $Count=$value;  
        }else{
            $MandatoryKeywords=0;
            $ManKeyCount =0; 
            $KeyCount =0;
            $Confidence=0; 
            $docTypeUID=0;
            $Header=0;
            $Footer=0;
            $KeyWordCutOff=0;
            $LowerCaseSearch=0;

            if(DEBUG){ print_r('COMPANY: ' .$key. "\n"); }
            
            // Document Keywords Array loop
            foreach( $value as $k => $v) { 
            
                if ($k == 'Confidence') $Confidence=$v;
                if ($k == 'KeywordsCount') $KeywordsCount=$v;
                if ($k == 'MandatoryKeywordsCount') $MandatoryKeywords=$v;
                if ($k == 'docTypeUID') $docTypeUID=$v;
                if ($k == 'Header')$Header=$v;
                if ($k == 'Footer')$Footer=$v;
                if($k=='KeyWordCutOff')$KeyWordCutOff=$v;
                if($k=='LowerCaseSearch')$LowerCaseSearch=$v;
                
                // Document Mandatory Keywords search loop
                if ($k == 'MandatoryKeywords') {

                    $getPageContent=getPageContentGV($ocrText,$Header,$Footer,$LowerCaseSearch);
                    foreach ($v as $section) {
                        foreach ($section as $keys => $words) {
                            switch ($keys) {
                                case 'Header':
                                    foreach ($words as $word) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempHeader,$word):strpos($tempHeader,$word);
                                    if($result) $ManKeyCount++;
                                }
                                break;
                                case 'Footer':
                                    foreach ($words as $word) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempFooter,$word):strpos($tempFooter,$word);
                                    if($result) $ManKeyCount++;

                                    if($key == "FAMILY SECURITY INSURANCE COMPANY"){

                                    if(DEBUG) print_r('Footer:'. "\n");
                                    if(DEBUG) print_r($tempFooter);
                                    }

                                }
                                break;
                                case 'Body':
                                    foreach ($words as $word) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempBody,$word):strpos($tempBody,$word);
                                    if($result) $ManKeyCount++;
                                }
                                break;
                                default:
                                    foreach ($words as $word) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($ocrText,$word):strpos($ocrText,$word);
                                    if($result) $ManKeyCount++;
                                }
                                break;
                            }
                        }
                    }

                    if(DEBUG) print_r('ManKeyCount:' .$ManKeyCount. "\n");
                }
                
                // Document Keywords search loop
                if($ManKeyCount && $k == 'Keywords'){

                    foreach ($v as $section) {
                        foreach ($section as $s => $keywords) {
                            switch ($s) {
                                case 'Header':
                                    foreach ($keywords as $keyword) {
                                        $countTime++;
                                        $result=($LowerCaseSearch)?stripos($tempHeader,$keyword):strpos($tempHeader,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                                case 'Footer':
                                    foreach ($keywords as $keyword) {
                                        $countTime++;
                                        $result=($LowerCaseSearch)?stripos($tempFooter,$keyword):strpos($tempFooter,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                                case 'Body':
                                    foreach ($keywords as $keyword) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempBody,$keyword):strpos($tempBody,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                                default:
                                    foreach ($keywords as $keyword) {$countTime++;
                                        $result=($LowerCaseSearch)?stripos($ocrText,$keyword):strpos($ocrText,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                            }
                        }
                    }

                    if(DEBUG) print_r('KeyCount:' .$KeyCount. "\n");
    
                    $pageConfidence =(($KeyWordCutOff != 0)? 0.5 + (($KeyCount/$KeyWordCutOff)/2):0.5 + (($KeyCount/$KeywordsCount)/2));

                    if(DEBUG) print_r('=== Satisfied Company pageConfidence:' .$pageConfidence. " ===\n");

                    if($pageConfidence >= $Confidence){

                        if(DEBUG) print_r("Inside confidence Satisfied if ===\n");

                        if($lastMatchDocTypeUID == $docTypeUID && $lastMatchPageNo<($pageNo-1)) {

                            for ($l=$lastMatchPageNo+1;$l<$pageNo;$l++){
                                $resultData[$l]=array();
                                $PageArray = [];
                                $PageArray['docName']=$key;
                                $PageArray['docTypeUID']=$docTypeUID;
                                $PageArray['pageConfidence']=0;
                                $PageArray['pageNo']=$l;

                                array_push($resultData[$l],$PageArray);
                            }  
                            $lastMatchDocTypeUID    =   $docTypeUID;
                            $lastMatchPageNo        =   $pageNo;
                        }
                        else{
                            $lastMatchDocTypeUID = $docTypeUID;
                            $lastMatchPageNo = $pageNo; 
                        }
                         
                        if(isset($resultData[$pageNo])){

                            $k = count($resultData[$pageNo]);
                            $sameDocTypeMatch="Empty";
                            $diffDocTypeMatch="Empty";
                            for($i=0;$i<$k;$i++){
                                if($pageConfidence > $resultData[$pageNo][$i]['pageConfidence'] &&$resultData[$pageNo][$i]['docTypeUID'] == $docTypeUID ){
                                    $sameDocTypeMatch=$i;
                                }else if($resultData[$pageNo][$i]['docTypeUID'] != $docTypeUID ){
                                    $diffDocTypeMatch=$i;
                                }
                                if(($sameDocTypeMatch != "Empty") && !$diffDocTypeMatch){
                                    $resultData[$pageNo][$sameDocTypeMatch]['pageConfidence']=$pageConfidence;
                                }
                                if(($diffDocTypeMatch != "Empty") && !$sameDocTypeMatch){
                                    $PageArray = [];
                                    $PageArray['docName']=$key;
                                    $PageArray['docTypeUID']=$docTypeUID;
                                    $PageArray['pageConfidence']=$pageConfidence;
                                    $PageArray['pageNo']=$pageNo;               
                                    array_push($resultData[$pageNo],$PageArray);
                                }
                            }
                        }else{

                            $resultData[$pageNo]=array();
                            $PageArray = [];
                            $PageArray['docName']=$key;
                            $PageArray['docTypeUID']=$docTypeUID;
                            $PageArray['pageConfidence']=$pageConfidence;
                            $PageArray['pageNo']=$pageNo;

                            $pageSubData = [];
                            if(SUBCLASSIFICATION){

                                $pageSubData = checkAgencyConfidenceGV($txtFile,$data['subDocDef'],$pageNo,$filename);


                                if(isset($pageSubData['subDocName']) && isset($pageSubData['subDocTypeUID']) && isset($pageSubData['pageSubConfidence'])){
                                    $PageArray['subDocName']        = $pageSubData['subDocName'];
                                    $PageArray['subDocTypeUID']     = $pageSubData['subDocTypeUID'];
                                    $PageArray['pageSubConfidence'] = $pageSubData['pageSubConfidence'];
                                }
                            }

                            $subKey = '';
                            if (EXTRACTION){
                                $PageArray['extractedData']=[];

                                $subKey = (isset($pageSubData['subDocName']))? $pageSubData['subDocName'] : '';

                                $extractedData=DataExtraction($ocrText,$key,$subKey);
                                array_push($PageArray['extractedData'],$extractedData);
                            }
                            array_push($resultData[$pageNo],$PageArray);

                            $pattern='/\bPage(\\s*).(\\d+)(\\s+)((?:[a-z][a-z]+))(\\s+)(\\d+)\b/i';
                            $pattern1='/\bPage(\\s+)(\\d+)\b/i';
                            if(preg_match($pattern, $tempHeader.$tempFooter, $matches)){
                                $string=$matches[0];

                                getUnclassifiedPagesGV($string,$filename,$pageNo,$key,$docTypeUID,$tempHeader.$tempFooter,$pattern,$Header,$Footer,$LowerCaseSearch,$subKey);
                            }elseif (preg_match($pattern1, $tempHeader.$tempFooter, $matches)) {
                                $string=$matches[0];
                                getUnclassifiedPagesGV($string,$filename,$pageNo,$key,$docTypeUID,$tempHeader.$tempFooter,$pattern1,$Header,$Footer,$LowerCaseSearch,$subKey);
                            }
                        }
                    }
                }
            } 
        }
    }

    LOG_MSG('INFO','checkConfidence(): END \n');
    return $resultData;
}

function checkAgencyConfidenceGV($txtFile,$data,$pageNo,$filename){

    LOG_MSG('INFO','checkAgencyConfidence(): START \n');
    if(DEBUG) print_r("checkAgencyConfidence(): START \n");
    
    $ocrText    = file_get_contents($txtFile);      
    $fdata      = json_decode($data);
  
    print_r("======================================== \n");
    print_r("textfile URL : ".$txtFile."\n");
    print_r("OcrText : ".$ocrText."\n");
    print_r("======================================== \n");

    GLOBAL $tempHeader,$tempFooter,$tempBody,$resultData,$countTime,$lastMatchDocTypeUID,$lastMatchPageNo;
    $tempHeader="";$tempFooter="";$tempBody="";$Count=0;$pageSubConfidence=0;$count_words = 0;
    //$lastMatchDocTypeUID="";
    //$lastMatchPageNo="";

    if(DEBUG) print_r('= Agency pageNo:' .$pageNo. " =\n");

    $returnArray = [];

    // Document Type Array loop
    foreach ($fdata as $key => $value){ 
        if ($key == 'Count') {
            $Count=$value;  
        }else{
            $MandatoryKeywords=0;
            $ManKeyCount =0; 
            $KeyCount =0;
            $subConfidence=0; 
            $subDocTypeUID=0;
            $Header=0;
            $Footer=0;
            $KeyWordCutOff=0;
            $LowerCaseSearch=0;
            
            // Document Keywords Array loop
            foreach( $value as $k => $v) { 
            
                if ($k == 'subConfidence') $subConfidence=$v;
                if ($k == 'KeywordsCount') $KeywordsCount=$v;
                if ($k == 'MandatoryKeywordsCount') $MandatoryKeywords=$v;
                if ($k == 'subDocTypeUID') $subDocTypeUID=$v;
                if ($k == 'Header')$Header=$v;
                if ($k == 'Footer')$Footer=$v;
                if($k=='KeyWordCutOff')$KeyWordCutOff=$v;
                if($k=='LowerCaseSearch')$LowerCaseSearch=$v;

                if(DEBUG) print_r($key.' default subConfidence => ' .$subConfidence. "\n");
                
                // Document Mandatory Keywords search loop
                if ($k == 'MandatoryKeywords') {

                    $getPageContent=getPageContentGV($ocrText,$Header,$Footer,$LowerCaseSearch);
                    foreach ($v as $section) {
                        foreach ($section as $keys => $words) {
                            switch ($keys) {
                                case 'Header':
                                    foreach ($words as $word) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempHeader,$word):strpos($tempHeader,$word);
                                    if($result) $ManKeyCount++;
                                }
                                break;
                                case 'Footer':
                                    foreach ($words as $word) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempFooter,$word):strpos($tempFooter,$word);
                                    if($result) $ManKeyCount++;
                                }
                                break;
                                case 'Body':
                                    foreach ($words as $word) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempBody,$word):strpos($tempBody,$word);
                                    if($result) $ManKeyCount++;
                                }
                                break;
                                default:
                                    foreach ($words as $word) {$countTime++;
                                   $result=($LowerCaseSearch)?stripos($ocrText,$word):strpos($ocrText,$word);
                                    if($result) $ManKeyCount++;
                                }
                                break;
                            }
                        }
                    }
                }
                
                // Document Keywords search loop
                if($ManKeyCount && $k == 'Keywords'){ 
                    
                    if(DEBUG) print_r('Agency ManKeyCount :' .$ManKeyCount. "\n");

                    foreach ($v as $section) {
                        foreach ($section as $s => $keywords) {
                            switch ($s) {
                                case 'Header':
                                    foreach ($keywords as $keyword) {
                                        $countTime++;
                                        $result=($LowerCaseSearch)?stripos($tempHeader,$keyword):strpos($tempHeader,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                                case 'Footer':
                                    foreach ($keywords as $keyword) {
                                        $countTime++;
                                        $result=($LowerCaseSearch)?stripos($tempFooter,$keyword):strpos($tempFooter,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                                case 'Body':
                                    foreach ($keywords as $keyword) {$countTime++;
                                    $result=($LowerCaseSearch)?stripos($tempBody,$keyword):strpos($tempBody,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                                default:
                                    foreach ($keywords as $keyword) {$countTime++;
                                        $result=($LowerCaseSearch)?stripos($ocrText,$keyword):strpos($ocrText,$keyword);
                                    if($result) $KeyCount++;
                                }
                                break;
                            }
                        }
                    }
    
                    $pageSubConfidence =(($KeyWordCutOff != 0)? 0.5 + (($KeyCount/$KeyWordCutOff)/2):0.5 + (($KeyCount/$KeywordsCount)/2));

                    if(DEBUG) print_r('=== Satisfied Agency pageSubConfidence:' .$pageSubConfidence. " ===\n");

                    if($pageSubConfidence >= $subConfidence){

                        if(DEBUG) print_r('=== Satisfied Agency : ' .$key. "=== \n");

                        $returnArray['subDocName']=$key;
                        $returnArray['subDocTypeUID']=$subDocTypeUID;
                        $returnArray['pageSubConfidence']=$pageSubConfidence;
                    }
                }
            } 
        }
    }

    LOG_MSG('INFO','checkAgencyConfidence(): END \n');
    return $returnArray;
}

function getPageContentGV($ocrText,$Header,$Footer,$LowerCaseSearch){

    LOG_MSG('INFO','getPageContent(): START \n');
    GLOBAL $tempHeader,$tempFooter,$tempBody;
    $tempHeader=" ";$tempFooter=" ";$tempBody=" ";
    $tempOcrText= preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $ocrText);
    // print_r($tempOcrText);exit;
    //$lines =($LowerCaseSearch == 1? explode("\n",strtolower($tempOcrText)):explode("\n",$tempOcrText));
    $lines=explode("\n",$tempOcrText);
    $count=count($lines);
    for($i=0; $i<$count; $i++){
        if($count==0 &&  $count<=$Header)return;
        if($count!=0 &&  $count>$Footer)$eof = ($count-$Footer);
        else return;
        if($i<$Header){
            $tempHeader.=$lines[$i];
            $tempHeader.="\n";continue;
        }
        if($i>=$eof && $i<$count){
            $tempFooter.= $lines[$i];
            $tempFooter.= "\n";continue;
        }
        if($count>($Header+$Footer) && $i>$Header && $i<$eof){
            $tempBody.= $lines[$i];
            $tempBody.= "\n";continue;
        }
    }

    // $completeText = $tempHeader.$tempFooter.$tempBody;
    // print_r($tempHeader);
    // print_r("=> \n");

    LOG_MSG('INFO','getPageContent(): END \n');
}

function getUnclassifiedPagesGV($string,$filename,$pageNo,$key,$docTypeUID,$ocrText,$pattern,$Header,$Footer,$LowerCaseSearch, $subKey){
    LOG_MSG('INFO','getUnclassifiedPages(): START \n');
    $explode=explode(' ', $string);
    $start=preg_replace("/[^0-9]/", "", $explode[1]);
    $end=(isset($explode[3]))?($explode[3]):0;
    GLOBAL $resultData;
    GLOBAL $missedPages;
    GLOBAL $tempHeader;
    GLOBAL $tempFooter;
    GLOBAL $tempBody;

    $tempHeader="";$tempFooter="";$tempBody="";
    $startPage = (($pageNo-$start)+1>0)?(($pageNo-$start)+1):1;
    $endPage = ($start && $end!=0)?$pageNo+($end - $start):$pageNo;
    //$matchedEnd=0;    
    for($i=$startPage, $n=1 ;$i<=$endPage;$i++,$n++){
        // print_r("startPage".$i);
        if (!file_exists($filename.'-'.$i.'.txt')) break;
        $ocrText=file_get_contents($filename.'-'.$i.'.txt');
        $getPageContent=getPageContentGV($ocrText,$Header,$Footer,$LowerCaseSearch);
        $pagePattern='Page '.$n;
        if(stripos($tempHeader.$tempFooter,$pagePattern)){
            //will not come to know if multiple matches are there which one is correct
            if(!isset($resultData[$i])){
                $resultData[$i]=array();
                $PageArray = [];
                $PageArray['docName']=$key;
                $PageArray['docTypeUID']=$docTypeUID;
                $PageArray['pageConfidence']=0;
                $PageArray['pageNo']=$i;
                if (EXTRACTION){
                    $PageArray['extractedData']=[];
                    $extractedData=DataExtraction($ocrText,$key,$subKey);
                    array_push($PageArray['extractedData'],$extractedData);
                }
                array_push($resultData[$i],$PageArray);
            }else{
                $m = count($resultData[$i]);
                for($j=0;$j<$m;$j++){
                     if($resultData[$i][$j]['docTypeUID'] != $docTypeUID ){
                        $PageArray = [];
                        $PageArray['docName']=$key;
                        $PageArray['docTypeUID']=$docTypeUID;
                        $PageArray['pageConfidence']=0;
                        $PageArray['pageNo']=$i;
                        if (EXTRACTION){
                            $PageArray['extractedData']=[];
                            $extractedData=DataExtraction($ocrText,$key,$subKey);
                            array_push($PageArray['extractedData'],$extractedData);
                        }
                        array_push($resultData[$i],$PageArray);
                    }   
                }
            }
            //print_r("matchDone".$i);
            //echo "\n";
            //$matchedEnd=$i;
            if(!$end)$endPage++; 
        }
    }
        // for ($m=$startPage; $m < $matchedEnd; $m++) { 
        //      print_r("matchedEnd".$matchedEnd);
        //      echo "\n";
        //      print_r("match".$m);
        //      echo "\n";
        //      //print_r("sedrtfgyhuidrtfgyhujikdrtfgyhujNo");
        //  if(!isset($resultData[$m])){
        //      $resultData[$m]=array();
        //      $PageArray = [];
        //      $PageArray['docName']=$key;
        //      $PageArray['docTypeUID']=$docTypeUID;
        //      $PageArray['pageConfidence']=0;
        //      $PageArray['pageNo']=$m;
        //      array_push($resultData[$m],$PageArray);
        //  }
        // }

    LOG_MSG('INFO','getUnclassifiedPages(): END \n');
    return ;
}

function deleteDirectoryGV($dir) {
    LOG_MSG('INFO','deleteDirectory(): START \n');

    if (is_dir($dir)) {
        exec('rm -rf ' . escapeshellarg($dir), $retval);
        return $retval == 0; 
    }else{
        echo "No such directory found";
    }

    LOG_MSG('INFO','deleteDirectory(): END \n');
}

function DataExtraction($ocrText,$InsuranceCompany,$InsuranceAgency='',$filepath='') {

    $DocData=[];
    if($ocrText=='')$ocrText = file_get_contents($filepath);
    $contents=" ".preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $ocrText);
    $DocData['InsuranceAgency'] = $InsuranceAgency;

    if($InsuranceCompany==='CSAA GENERAL INSURANCE COMPANY'){ 
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency == "AAA"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }
                    
                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Fax #:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Fax No'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Type:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)\n(.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                        $tmpData[$t]['Borrower Name'] = $matches[5];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Location of Insured Property';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[1];
                        $tmpData[$t]['Residence'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='Standard Time:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(\d{1,2}\/\d{1,2}\/\d{2,4}).(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    // $keyword='';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/\d{2}\/\d{2}\/\d{4}.(.*?)\d{2}:\d{2}/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $text = trim($matches[1]);
                        
                    // }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*\n((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Named and Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]."".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Loan Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='2. Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*) /";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Second Mortgagee'] = $matches[1];

                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern.*\n(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Second Mortgagee'].=', '.$matches[1];
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern.* (.\d*)/";
                            if(preg_match($pattern, $contents, $matches)){
                                $text = trim($matches[1]);
                                $tmpData[$t]['Second Mortgagee Loan Number']=$matches[1];
                            }
                        }
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
            endif;

        }else{
            if(DEBUG) echo "No matches found";  
        }

    }elseif($InsuranceCompany==='CSAA INSURANCE EXCHANGE'){
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency == "AAA"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (CSAAInsCompany.pdf)
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }
                
                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Insured Location';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\nNATIONSTAR/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[1]."".$matches[2];
                        $tmpData[$t]['Borrower Name'] = $matches[4];
                        $tmpData[$t]['Residence'] = $matches[1]."".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='From:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='To:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Structures';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    
                    $keyword='1. Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[2]."".$matches[3];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[4];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='2. Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*) /";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Second Mortgagee'] = $matches[1];

                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern.*\n(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Second Mortgagee'].=', '.$matches[1];
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern.* (.\d*)/";
                            if(preg_match($pattern, $contents, $matches)){
                                $text = trim($matches[1]);
                                $tmpData[$t]['Second Mortgagee Loan Number']=$matches[1];
                            }
                        }
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
            endif;


        }else{
            if(DEBUG) echo "No matches found";  
        }

    }elseif ($InsuranceCompany==="HOMESITE INSURANCE COMPANY") {
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if($InsuranceAgency == "GEICO INSURANCE AGENCY, INC."){


            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (GEICO.pdf)
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $DocData['InsuranceCompany']=$InsuranceCompany;
                    $keyword='Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Fax ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*) This/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace("/[^0-9]/", "",$matches[1]);
                        $tmpData[$t]['Fax No'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Insured Location';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $text;
                        $tmpData[$t]['Residence'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n.*?\n.((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Loan Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    // $keyword='from:';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern\n12:01 AM (.*) through/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $text = trim($matches[1]);
                    //  $tmpData[$t]['Policy Start Date'] = $matches[1];
                    // }else{if(DEBUG) echo "No matches found";}

                    $keyword = '(local time)';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/AM(.*)through\n.*?AM(.*)$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                     $text = trim($matches[1]);
                     $tmpData[$t]['Policy Start Date'] = $matches[1];
                     $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Insured Name and Mailing Address:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $matches[1]." ".$matches[2];
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='First Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nLoan Number: (.*)\n(.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        $tmpData[$t]['First Mortgagee'] = $matches[2].$matches[3].$matches[4].$matches[5];
                    }else{echo "No matches found";}

                    $keyword='Second Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nLoan Number: (.*)\n(.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Second Mortgagee Loan Number'] = $matches[1];
                        $tmpData[$t]['Second Mortgagee'] = $matches[2].$matches[3].$matches[4].$matches[5];
                    }else{ if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif ($InsuranceCompany==="LIBERTY MUTUAL") {
        
        if(DEBUG) echo 'liberty section ';
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "Liberty Mutual"){
            if(DEBUG) print_r("DataExtraction Agency ".$InsuranceAgency."\n");
            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 3;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1 (LibertyMutual.pdf)
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'POLICY NUMBER:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.*?\n((?=.*[A-Z])(?=.*\d)(?=.*[-]).*)/";

                    $keyword2 = 'POLICY NUMBER:';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n((?=.*[A-Z])(?=.*\d)(?=.*[-]).*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Provisions of a';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)under/";

                    $keyword1 ='Provisions of a';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(.*)/";

                    $keyword2 ='Provisions of a';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Type'] = trim($matches[1]);
                    }elseif(preg_match($pattern1, $contents, $matches)){

                        if(empty($matches[1])){
                            if(preg_match($pattern2, $contents, $matches)){
                                $tmpData[$t]['Policy Type'] = trim($matches[1]);
                            }
                        }else{
                            $tmpData[$t]['Policy Type'] = trim($matches[1]);
                        }

                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Type'] = trim($matches[1]);

                        if(empty($matches[1])){
                            if(preg_match($pattern1, $contents, $matches)){
                                $tmpData[$t]['Policy Type'] = trim($matches[1]);
                            }
                        }else{
                            $tmpData[$t]['Policy Type'] = trim($matches[1]);
                        }
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'RESIDENCE PREMISES INSURED';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?Same as Residence\n(.*)\n(.*)/";

                    $keyword1 = 'RESIDENCE PREMISES INSURED';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.*?Same as Residence\n(.*)\n(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='RESIDENCE PREMISES';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'POLICY PERIOD ';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[2]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'YOUR DWELLING $ ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword2 = 'YOUR DWELLING';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n.*?\n((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword3 = 'YOUR DWELLING';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3\n((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'NAME & ADDRESS';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n([A-Za-z ]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        if ($matches[2] )$tmpData[$t]['Borrower Name'].=', '.$matches[2];
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Mortgagee 1';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*).\nLoan #: (.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[3]." ".$matches[4]." ".$matches[5];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Mortgagee 2';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*).\nLoan #: (.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Second Mortgagee'] = $matches[1]." ".$matches[3]." ".$matches[4]." ".$matches[5];
                        $tmpData[$t]['Second Mortgagee Loan Number'] = $matches[2];
                    }else{ if(DEBUG) echo "No matches found";}


                    $keyword='write:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)\n([0-9,\-]+).*\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^a-z,A-Z,0-9,\s]/', '', trim($matches[3]));
                        
                        if(preg_match("/^\D+$/", $text, $matches)){

                        $tmpData[$t]['InsuranceAgency'] = $text;
                        }
                    }else{ 

                        $keyword='Liberty';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)Mutual./";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = preg_replace('/[^a-z,A-Z,0-9,\s]/', '', trim($matches[1]));
                            $tmpData[$t]['InsuranceAgency'] = $text;
                        }else{ if(DEBUG) echo "No matches found"; }
                    }
                }

                // Template 2 (libertymutual1_libertymutual_t2.pdf)
                if($t == 2){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'POLICY NUMBER:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n((?=.*[A-Z])(?=.*\d)(?=.*[-]).*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='RESIDENCE PREMISES INSURED';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?Same as Residence\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='RESIDENCE PREMISES';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'POLICY PERIOD ';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[2]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='YOUR DWELLING';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='NAME & ADDRESS';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n([A-Za-z ]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        if ($matches[2] )$tmpData[$t]['Borrower Name'].=', '.$matches[2];
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Mortgagee 1';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*).\nLoan #: (.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[3]." ".$matches[4]." ".$matches[5];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}
                }

                if($t == 3){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword = 'Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{
                        if(DEBUG) echo "No matches found";
                    }

                    $keyword = 'Mailing Address:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n(.*)\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1].", ".$matches[2];
                    }

                    $keyword = 'Lender Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n.*?\n(.*)\n.*?\n(.*)\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                    }

                    $keyword = "Loan Number: ";
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern([0-9]+)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Property Location:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nLoan/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Effective at';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?(\d{2}\/\d{2}\/\d{4}).*?(\d{2}\/\d{2}\/\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[2]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Second Mortgagee / Lender Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Second Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];

                        $keyword = $matches[4];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\nLoan Number: ([0-9]+)/";

                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Second Mortgagee Loan Number'] = $matches[1];
                        }else{if(DEBUG) echo "No matches found";}

                    }else{
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/\/ Lender Name:$pattern\n(.*)\n(.*)\n(.*)\nLoan/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Second Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3];

                            $keyword = $matches[4];
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern\nLoan Number: ([0-9]+)/";

                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Second Mortgagee Loan Number'] = $matches[1];
                            }else{if(DEBUG) echo "No matches found";}

                        }else{if(DEBUG) echo "No matches found";}
                    }
                }
            }
            if(DEBUG) print_r("DataExtraction Agency ".$tmpData."\n");
            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;

            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        if(!isset($tmpData[$i]['InsuranceAgency'])){
                            $tmpData[$i]['InsuranceAgency'] = $InsuranceAgency;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif ($InsuranceCompany==="Hartford") {
        
        // $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "HARTFORD"){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 2;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (Hartford3.pdf) DOC-1056
                if($t == 1){

                    if(DEBUG){ print_r("Inside t". $t ." \n"); }

                    $keyword='Insurer:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.([^,]+)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='RESIDENCE PREMISES:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(.*)\n(.*)\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='INSURED AND MAILING ADDRESS:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n([A-Za-z ]+)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $pattern = preg_quote($matches[1], '/');
                        $pattern = "/$pattern\n([A-Za-z ]+)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'].=', '.$matches[1];
                        }
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='RESIDENCE PREMISES:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    // $keyword='Dwelling: $';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern.((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword1 = 'LIMIT OF LIABILITY';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.*?((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword2 = 'LIMIT OF LIABILITY';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\nA. Dwelling:\n((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{2}\/\d{2}\/\d{4}).*?(\d{2}\/\d{2}\/\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='MORTGAGEE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2];
                        $keyword=$matches[2];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\nLoan number: (.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        }
                    }else{if(DEBUG) echo "No matches found";}
                }

                // Template 3 (Hartford4.pdf) DOC-1056
                if($t == 2){

                    if(DEBUG){ print_r("Inside t". $t ." \n"); }


                    $keyword='INSURER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='POLICY NO:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='RESIDENCE PREMISES';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nPolicy/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Named Insured and';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='RESIDENCE PREMISES';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nPolicy/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='DWELLING';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nB.\nOTHER STRUCTURE\n.*?\n.*?\n.*?\n.*?\n((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{2}-\d{2}-\d{2}).*?(\d{2}-\d{2}-\d{2})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='MORTGAGEE1:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.LOAN NO.(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nCOUNTERSIGNED/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5].", ".$matches[6].", ".$matches[7];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $text;
                    }else{
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern.LOAN NO.(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nCOUNTERSIGNED/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['First Mortgagee'] = $matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5].", ".$matches[6];
                            $tmpData[$t]['First Mortgagee Loan Number'] = $text;
                        }
                    }
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }//StateFarm DOC-1051 [statefarm4.pdf]
    elseif ($InsuranceCompany==="state Farm Lloyds") {
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency == "StateFarm"){

            $template   = 2;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                if($t == 1){

                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Policy Payments:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n(.*)\n.*?\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Name Insured and Address:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nPolicy Payments:\n([A-Za-z, ]+)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n(.*)\nPolicy Type\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Effective Date';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Expiration Date';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Loan Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = '1st Additional Interest';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";
                    
                    $pattern2 = preg_quote($keyword, '/');
                    $pattern2 = "/$pattern2\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4]." ".$matches[5];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Fax';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Fax No'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Email';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Email'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }

                if($t == 2){

                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];

                        $keyword = $text;
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\nPolicy Type\n.*?\n(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1]." ".$matches[2];
                            $tmpData[$t]['Residence'] = $matches[1]." ".$matches[2];
                        }else{if(DEBUG) echo "No matches found";}

                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Policy Payments:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)Premium/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword = 'Dwelling $';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{1,3},\d{1,3})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Effective Date';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Expiration Date';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Loan Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Loan Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = '1st Additional Interest';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";
                    
                    $pattern2 = preg_quote($keyword, '/');
                    $pattern2 = "/$pattern2\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Fax';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Fax No'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Email';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Email'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;
                $tmpData[$templateId]['InsuranceCompany'] = $InsuranceCompany;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }//StateFarm DOC-1051 [statefarm1.pdf, statefarm2.pdf, statefarm3.pdf, statefarm8.pdf, statefarm5.pdf, statefarm6.pdf, statefarm7.pdf]
    elseif ($InsuranceCompany==="State Farm Fire and Casualty Company") {
        $DocData['InsuranceCompany']=$InsuranceCompany;

        print_r("Inside ".$InsuranceCompany."\n");

        if ($InsuranceAgency == "StateFarm"){

            $template = 4;
            $tmpData = [];
            
            for ($t=1; $t <= $template; $t++) {

                // Template 1 (statefarm1.pdf, statefarm2.pdf, statefarm3.pdf, statefarm8.pdf)
                if($t == 1){
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(.*)\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Named Insured and Mailing Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;

                        $keyword = $matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)/";

                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                            $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                        }else{if(DEBUG) echo "No matches found";}

                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(.*).$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Expiration of Policy';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\nEffective Date\n.*?\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Limit of Liability';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nAgent/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                    $text = trim($matches[1]);
                    $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }

                // Template 2 (statefarm5.pdf)
                if($t == 2){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword = 'Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n((?=.*[A-Z])(?=.*\d)(?=.*[-]).*)/";

                    $keyword1 = 'Policy Details:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n((?=.*[A-Z])(?=.*\d)(?=.*[-]).*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Policy Payments:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\nPremium Amount\n.*?\n(.*)\nLender Pays\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                        $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                        $tmpData[$t]['Residence'] = $matches[2].", ".$matches[3];
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1='Policy Effective Date';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    $keyword2='Policy Effective Date';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern2/";

                    $keyword3='Policy Effective Date';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3.*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1='Policy Expiration Date';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    $keyword2='Policy Expiration Date';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern2/";

                    $keyword3='Policy Expiration Date';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3.*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                    

                    $keyword='Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Additional Interest';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                    $text = trim($matches[1]);
                    $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }
                // Template 3 (statefarm6.pdf, statefarm7.pdf)

                if($t == 3){

                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 ='Policy Payments:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\nPremium Amount\n.*?\n(.*)\nBalance Due Amount\n.*?\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                        $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                        $tmpData[$t]['Residence'] = $matches[2].", ".$matches[3];
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1='Policy Effective Date';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    $keyword2='Policy Effective Date';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern2/";

                    $keyword3='Policy Effective Date';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3.*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1='Policy Expiration Date';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    $keyword2='Policy Expiration Date';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern2/";

                    $keyword3='Policy Expiration Date';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3.*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                    

                    $keyword='Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Additional Interest';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                    $text = trim($matches[1]);
                        $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }

                if($t == 4){

                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 ='Policy Payments:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\nPremium Amount\n.*?\n(.*)\nLender Pays\n.*?\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                        $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                        $tmpData[$t]['Residence'] = $matches[2].", ".$matches[3];
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1='Policy Effective Date';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    $keyword2='Policy Effective Date';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern2/";

                    $keyword3='Policy Effective Date';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3.*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1='Policy Expiration Date';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    $keyword2='Policy Expiration Date';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/(\d{1,2}\/\d{1,2}\/\d{2,4})\n$pattern2/";

                    $keyword3='Policy Expiration Date';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3.*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                    

                    $keyword='Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Additional Interest';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern (.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                    $text = trim($matches[1]);
                        $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if(DEBUG){ print_r($tmpData[$i]); }

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;
                $tmpData[$templateId]['InsuranceCompany'] = $InsuranceCompany;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif ($InsuranceCompany==="ALLSTATE PROPERTY AND CASUALTY INSURANCE") {

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG) print_r('Inside ' .$InsuranceCompany. "\n");

        if ($InsuranceAgency == "Allstate"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) { 

                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }
                    
                    $keyword = 'Insurance Company';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)Last/";

                    $keyword2 = 'Insurance Company.';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Number: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern([0-9]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    
                    $keyword = 'Name of Insured: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*).Balance/";

                    $keyword2 = 'Name of Insured:';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword = 'Policy Period:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*).-.(.*) /";

                    $keyword2 = 'Policy Period: ';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[2]);
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword = 'Policy Type: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*) Due/";

                    $keyword2 = 'Policy Type: ';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword = 'Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)\n(.*)Name:/";
                    
                    $keyword2  = 'First Mortgagee';
                    $pattern2  = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2.*?\nName:(.*)\n(.*)\nAddress/";
                    
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2];
                        $keyword='Loan Number:';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern.(.*).Loan/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        }
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2];

                        $keyword = 'Loan Number:';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        }
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword = 'Mailing Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nProperty/";

                    $keyword2 = 'Mailing Address';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)\n(.*)\n(.*)\nProperty/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1]." ".$matches[2]." ".$matches[3];
                        $tmpData[$t]['Insured Location'] = $matches[1]." ".$matches[2]." ".$matches[3];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'DWELLING PROTECTION $';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{1,3},\d{1,3})/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

            
        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif ($InsuranceCompany==="ALLSTATE VEHICLE AND PROPERTY INS") {

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG) print_r('Inside ' .$InsuranceCompany. "\n");

        if ($InsuranceAgency == "Allstate"){

            $template   = 2;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) { 

                // Template 1 (AllState1.pdf,AllState2.pdf,AllState3.pdf)
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword = 'Insurance Company';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)Last/";

                    $keyword2 = 'Insurance Company.';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Number: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern([0-9]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Name of Insured: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*).Balance/";

                    $keyword2 = 'Name of Insured:';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;

                        $keyword = "Policy Number:";
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern.*?\n(.*)\n(.*)\nPolicy/";
                        if(empty($text)){
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Borrower Name'] = $matches[1]." ".$matches[2];
                            }
                        }
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword = 'Mailing Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nProperty/";

                    $keyword2 = 'Mailing Address';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)\n(.*)\n(.*)\nProperty/";

                    $keyword3 = 'Mailing Address';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3.*?\n(.*)\n(.*)\nDWELLING/";


                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $matches[1]." ".$matches[2]." ".$matches[3];
                        $tmpData[$t]['Insured Location'] = $matches[1]." ".$matches[2]." ".$matches[3];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1]." ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $matches[1]." ".$matches[2];;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Policy Period:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*).-.(.*) /";

                    $keyword2 = 'Policy Period: ';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[2]);
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword = 'Policy Type: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*) Due/";

                    $keyword2 = 'Policy Type: ';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword = 'Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)\n(.*)Name:/";
                    
                    $keyword2  = 'First Mortgagee';
                    $pattern2  = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2.*?\nName:(.*)\n(.*)\nAddress/";
                    
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2];
                        $keyword='Loan Number:';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern.(.*).Loan/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        }
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2];

                        $keyword = 'Loan Number:';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        }
                    }else{ if(DEBUG) echo "No matches found"; }


                    $keyword = 'DWELLING PROTECTION $';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{1,3},\d{1,3})/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                }

                // Template 2 (AllState5.pdf)
                if($t == 2){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }   

                    $keyword='ALLSTATE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[0]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Policy Number: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/Policy Number\: ([0-9]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[0]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='First Mortgagee Loan Number : ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[0]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = preg_replace('/[^0-9]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Interested Parties';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\nPolicy/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[0]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                    }else{

                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nPolicy/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[0]);
                            $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3];
                        }else{
                            if(DEBUG) echo "No matches found";
                        }
                    }

                    $keyword='Policy period beginning on ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{2}\/\d{2}\/\d{4}).*?(\d{2}\/\d{2}\/\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='POLICY TYPE - ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling Protection';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.[$].(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword="Insured's Information";
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;

                        $keyword=$matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\nLocation/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                        }else{
                            $keyword=$matches[1];
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nLocation/";
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2].", ".$matches[3];
                            }else{
                                if(DEBUG) echo "No matches found";
                            }
                        }
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Location of property insured';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nMortgagees/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = $matches[1].", ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $text;
                    }else{

                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nMortgagees/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = $matches[1].", ".$matches[2].", ".$matches[3];
                            $tmpData[$t]['Insured Location'] = $text;
                        }else{

                            if(DEBUG) echo "No matches found";
                        }
                    }
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

            
        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif ($InsuranceCompany==="AllState Insurance Comapny") {

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r('Inside ' .$InsuranceCompany. "\n"); }

        if ($InsuranceAgency == "Allstate"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) { 

                // Template 1 (AllState4.pdf)
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }
                    
                    $keyword='Insurance Company';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Number: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern([0-9]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Name of Insured: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*).Balance/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Policy Period:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Type: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    // 1st Mortgage
                    $keyword = 'Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)\n(.*)\nName:.*?\nAddress:(.*)\nAddress:.*?\n(.*)\nLoan/";

                    $keyword1 = 'Name:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(.*)\n(.*)\nName:.*?\n.*?\nAddress:(.*)\nAddress:.*?\n(.*)\n.*?\nLoan/";
                    if(preg_match($pattern, $contents, $matches)){

                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                        $keyword=$matches[4];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\nLoan Number:(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee Loan Number'] = trim($matches[1]);
                        }
                    }elseif(preg_match($pattern1, $contents, $matches)){

                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                        $keyword = $matches[5];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\nLoan Number:(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        }
                    }else{if(DEBUG) echo "No matches found";}


                    // 2nd Mortgage
                    $keyword = 'Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?\n.*?\nName:(.*)\n(.*)\nAddress:.*?\nAddress:(.*)\n.*?\n(.*)\nLoan/";

                    if(preg_match($pattern, $contents, $matches)){

                        $text = trim($matches[1]);
                        $tmpData[$t]['Second Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                        $keyword=$matches[4];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\nLoan Number:(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Second Mortgagee Loan Number'] = trim($matches[1]);
                        }
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='DWELLING PROTECTION ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";

                    $keyword1 = 'DWELING PROTECTION ';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1.(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Mailing Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nProperty Address\nSame as Mailing Address\n(.*)\n(.*)/";

                    $keyword1 = 'Mailing Address';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nProperty Address\n(.*)\n(.*)\n(.*)\n(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = $matches[1].", ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $text;
                        $tmpData[$t]['Residence'] = $text;
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $matches[3].", ".$matches[4];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if(DEBUG){ print_r($tmpData[$i]); }

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;
            
        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif($InsuranceCompany==='FARMERS'){
        $DocData['InsuranceCompany']=$InsuranceCompany;

        
        if ($InsuranceAgency == "Farmers Insurance"){

            $template   = 4;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (farmers3.pdf) DOC-1054
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='Company';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nNa me\n(.*)/";

                    $keyword1='Company';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nName\n(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Who Pays';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n([0-9 ]*)\n.*?\n([a-z,A-Z ]*)\n([a-z,A-Z ]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        $tmpData[$t]['First Mortgagee'] = $matches[2]." ".$matches[3];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Who Pays';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n([a-z,A-Z ]*)\n([a-z,A-Z ]*)\n(.*)\n(.*)\n(.*)\n([0-9 ]*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[6];
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4]." ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nState/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1].''.$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Fax';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Fax No'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Email Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Email'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Property';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nAddress\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Insured Location'] = $matches[1].",".$matches[2];
                        $tmpData[$t]['Residence'] = $matches[1].",".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Effective';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nDate\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Expiration/Renewal';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nDate\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Name(s) for Policy';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                }

                // Template 2 (farmers5.pdf) DOC-1054
                if($t == 2){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='Company';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nNa me\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    
                    $keyword='Who Pays';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text   = preg_replace('/[0-9]/', '', $matches[1]);
                        $loanNo = preg_replace('/[^0-9]/', '', $matches[1]);
                        $mortgagee  = str_ireplace('mortgagee', '', $text);

                        $tmpData[$t]['First Mortgagee'] = $mortgagee;


                        $keyword = $mortgagee;
                        $pattern = preg_quote($keyword, '/');
                        if(empty($loanNo)){

                            $arr = [
                                "/$pattern\n(.*)\n([^0-9]*)(.*)Mortgagee/",
                                "/$pattern(.*)([^0-9]*)(.*)Mortgagee/",
                                "/$pattern([^0-9]*)(.*)Mortgagee/",
                                "/$pattern\n(.*)\n([^0-9]*)(.*)Mortgagee/"
                            ];

                            foreach ($arr as $pattern) {
                                if(preg_match($pattern, $contents, $matches)){
                                    foreach ($matches as $val) {
                                        $text = preg_replace('/[^0-9]/', '', $val);
                                        if(!empty($text)){
                                            $loanNo = $text;
                                        }
                                    }

                                    $tmpData[$t]['First Mortgagee Loan Number'] = $loanNo;
                                }
                            }
                        }else{
                            $tmpData[$t]['First Mortgagee Loan Number'] = $text;
                        }

                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nState/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1].' '.$matches[2].' '.$matches[3];
                    }else{
                        if(DEBUG) echo "No matches found";
                    }


                    $keyword='Fax';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Fax No'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Email Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Email'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Property';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nAddress\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Insured Location'] = $matches[1].",".$matches[2];
                        $tmpData[$t]['Residence'] = $matches[1].",".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Effective';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nDate\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Expiration/Renewal';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nDate\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Name(s) for Policy';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                }

                // Template 3 (farmers3.pdf) New
                if($t == 3){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='Underwritten By:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nPolicy Type:\nPolicy Status:\nTerm Effective:\nRenewal Date:\nInsured:\n(.*)\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4}).*?\n(\d{1,2}\/\d{1,2}\/\d{2,4}).*?\n(.*)\n(.*)\n(.*)/";


                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Number'] = $matches[1];
                        $tmpData[$t]['Policy Type'] = $matches[2];
                        $tmpData[$t]['Policy Start Date'] = $matches[4];
                        $tmpData[$t]['Policy Exp Date'] = $matches[5];
                        $tmpData[$t]['Borrower Name'] = $matches[6];
                    }else{
                        if(DEBUG) echo "No matches found";
                    }


                    $keyword = 'Property Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Insured Location'] = $matches[1];
                        $tmpData[$t]['Residence'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    // $keyword='PolicyNumber:';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern.(.*)/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $text = preg_replace('/[^0-9]/', '', $matches[1]);
                    //  $tmpData[$t]['Policy Number'] = $text;
                    // }else{
                    //  $keyword='Policy Number:';
                    //  $pattern = preg_quote($keyword, '/');
                    //  $pattern = "/$pattern.(.*)/";
                    //  if(preg_match($pattern, $contents, $matches)){
                    //      $text = preg_replace('/[^0-9]/', '', $matches[1]);
                    //      $tmpData[$t]['Policy Number'] = $text;
                    //  }else{
                    //      if(DEBUG) echo "No matches found";
                    //  }
                    // }

                    // $keyword='Policy Type:';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern\n(.*)\nUnderwritten/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $text = trim($matches[1]);
                    //  $tmpData[$t]['Policy Type'] = $matches[1];
                    // }else{
                    //  $pattern = preg_quote($keyword, '/');
                    //  $pattern = "/$pattern(.*)\n([^0-9]*)Policy/";
                    //  if(preg_match($pattern, $contents, $matches)){
                    //      $text = trim($matches[1]);
                    //      $tmpData[$t]['Policy Type'] = $matches[1];
                    //  }else{if(DEBUG) echo "No matches found";}
                    // }

                    // $keyword='Insured:';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern.(.*?,)/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $text = trim($matches[1]);
                    //  $tmpData[$t]['Borrower Name'] = $text;
                    // }else{if(DEBUG) echo "No matches found";}


                    // $keyword='Property Address';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern\n(.*)\nCoverages/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $tmpData[$t]['Insured Location'] = $matches[1];
                    //  $tmpData[$t]['Residence'] = $matches[1];
                    // }else{if(DEBUG) echo "No matches found";}

                    // $keyword='Effective:';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern.(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $tmpData[$t]['Policy Start Date'] = $matches[1];
                    // }else{
                    //  if(DEBUG) echo "No matches found";
                    // }


                    // $keyword='Renewal Date:';
                    // $pattern = preg_quote($keyword, '/');
                    // $pattern = "/$pattern.(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    // if(preg_match($pattern, $contents, $matches)){
                    //  $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    // }else{
                    //  if(DEBUG) echo "No matches found";
                    // }

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)Coverage/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{

                        $keyword='Limit';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n[$](\d{1,3},\d{1,3})/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = preg_replace('/[^0-9]/', '', $matches[1]);
                            $tmpData[$t]['Dwelling Amount'] = $text;
                        }else{
                            if(DEBUG) echo "No matches found";
                        }
                    }


                    $keyword1 = '1st Mortgagee';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nLoan Number\n(.*)\n(.*)\n(.*)\n(.*)\nPremium/";

                    $keyword2 = '1st Mortgagee';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\nLoan Number\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nPremium/";

                    $keyword3 = '1 st Mortgagee';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3\nLoan Number\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nPremium/";

                    if(preg_match($pattern1, $contents, $matches)){

                        $mortgagee = preg_replace('/[0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $mortgagee.", ".$matches[2].", ".$matches[3].", ".$matches[4];

                        $loanNumber = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $loanNumber;

                    }elseif(preg_match($pattern2, $contents, $matches)){

                        $mortgagee = preg_replace('/[0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $mortgagee.", ".$matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];

                        $loanNumber = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $loanNumber;

                    }elseif(preg_match($pattern3, $contents, $matches)){

                        $mortgagee = preg_replace('/[0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $mortgagee.", ".$matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];

                        $loanNumber = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $loanNumber;

                    }else{
                        if(DEBUG) echo "No matches found";
                    }


                    $keyword='FAX:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Fax No'] = $text;

                        $keyword = $matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\nYour/";

                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Email'] = $text;
                        }else{if(DEBUG) echo "No matches found";}

                    }else{if(DEBUG) echo "No matches found";}
                }

                // Template 4 (Farmers9.pdf)
                if($t == 4){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='Company Name';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $policyNoArr = [
                        "PolicyNumber:",
                        "PolicyNumber",
                        "Policy Number:",
                        "Policy Number"
                    ];

                    foreach ($policyNoArr as $keyword) {
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = preg_replace('/[^0-9]/', '', $matches[1]);

                            if(!empty($text)){
                                $tmpData[$t]['Policy Number'] = $text;
                            }
                        }
                    }

                    $keyword = 'Policy Type';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nState/";

                    $keyword1 = 'Policy Type';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.*?\n(.*)\nState/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Type'] = $matches[1]." ".$matches[2];
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Type'] = $matches[1]." ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Primary Insured';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\nName/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Mailing Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\nPolicy/";  
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Insured Location'] = $matches[1];
                        $tmpData[$t]['Residence'] = $matches[1];
                    }else{

                        $pattern = preg_quote($keyword, '/');   
                        $pattern = "/$pattern\n(.*)\n(.*)\nPolicy/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1];
                            $tmpData[$t]['Residence'] = $matches[1];
                        }else{

                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nPolicy/";  
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Insured Location'] = $matches[1];
                                $tmpData[$t]['Residence'] = $matches[1];
                            }else{
                                if(DEBUG) echo "No matches found";
                            }
                        }
                    }

                    $keyword='Term effective Date';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Expiration/Renewal';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nDate\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    $keyword2 ='Expiration/Renewal';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2.*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Exp Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Limit/Deductible';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nDwelling\n((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword2 = 'Limit/Deductible';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n.*?((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Email Address';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = str_replace(' ', '', $matches[1]);
                        $tmpData[$t]['Email'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Fax';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Fax'] = $matches[1];
                    }else{
                        if(DEBUG) echo "No matches found";
                    }

                    $keyword1 = 'Mortgagee';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nLoan Number\nWho Pays\n(.*)\n(.*)\n(.*)\n(.*)\n([0-9]*)\n.*?\nAdditional/";

                    $keyword2 = 'Mortgagee';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\nLoan Number\nWho Pays\n(.*)\n(.*)\n(.*)\n([0-9]*)\n.*?\nAdditional/";

                    $keyword3 = 'Mortgagee';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3\nLoan Number\nWho Pays\n(.*)\n(.*)\n([0-9]*)\n.*?\nAdditional/";

                    if(preg_match($pattern1, $contents, $matches)){
                        
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[5];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[4];
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[3];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                
                if(!isset($tmpData[$templateId]['InsuranceCompany'])){
                    $tmpData[$templateId]['InsuranceCompany'] = $InsuranceCompany;
                } 
                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif($InsuranceCompany==='UNITED SERVICES AUTOMOBILE ASSOCIATION'){

        $DocData['InsuranceCompany']=$InsuranceCompany;
        
        if ($InsuranceAgency == "USAA"){

            $template   = 2;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (USAA.pdf)
                if($t == 1){

                    $keyword='RECIPIENT';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]).trim($matches[2]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='FAX NUMBER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Fax No'] = preg_replace('/[^0-9]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Location:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Insured Location'] = $matches[1].",".$matches[2];
                        $tmpData[$t]['Residence'] = $matches[1].",".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Effective Date:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1.(\d{2}\/\d{2}\/\d{2})\n.*?(\d{2}\/\d{2}\/\d{2})/";

                    $keyword2 = 'Effective Date: ';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else if(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Dwelling:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword1='Limit';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.*?((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Named Insured:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Loan Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword2 = 'FIRST MORTGAGEE';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)\nName:(.*)/";

                    if(preg_match($pattern2, $contents, $matches)) {
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2];
                    }else{
                        if(DEBUG) echo "No matches found";
                    }
                }

                // Template 2 (USAA2.pdf)
                if($t == 2){

                    $templatePattern1 = '/Named Insured\n(.*)\nPolicy\nNumber\nTerm\nType and Contract\nTotal Premium\nBalance Due/'; // USAA3_USAA_t2
                    $templatePattern2 = '/Named Insured\nPolicy\nNumber\nTerm\nType and Contract\nTotal Premium\nBalance Due\nProperty\nLocation\nCounty\nLegal Description/'; // USAA5_USAA_t2
                    $templatePattern3 = '/Named Insured\nPolicy\nNumber\nTerm\nType and Contract\nTotal Premium\nBalance Due\nProperty\nLocation\nCounty\n[^Legal]/'; // USAA6_USAA_t2

                    if(preg_match($templatePattern1, $contents, $matches)){

                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;

                        $keyword2 = 'Balance Due';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)/";
                        
                        if(preg_match($pattern2, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Policy Number'] = $text;
                        }else{if(DEBUG) echo "No matches found";}

                        $keyword2 = 'Balance Due';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                        if(preg_match($pattern2, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Policy Start Date'] = trim($matches[2]);
                            $tmpData[$t]['Policy Exy Date'] = trim($matches[4]);
                        }else{if(DEBUG) echo "No matches found";}

                        $keyword2 = 'Balance Due';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})(.*)\n(.*)/";

                        if(preg_match($pattern2, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Policy Type'] = $matches[6];
                        }else{if(DEBUG) echo "No matches found";}


                        $keyword2 = 'Legal Description';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(.*)\n(.*)/";

                        if(preg_match($pattern2, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2].", ".$matches[3];
                            $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2].", ".$matches[3];
                        }else{if(DEBUG) echo "No matches found";}                       

                    }elseif(preg_match($templatePattern2, $contents, $matches)){

                        $keyword  = 'Legal Description';
                        $pattern1 = preg_quote($keyword, '/');
                        $pattern1 = "/$pattern1\n((http|https))(.*)/";

                        if(preg_match($pattern1, $contents, $matches)){
                            $text = trim($matches[3]);
                            
                            $pattern = preg_quote($text, '/');
                            $pattern = "/$pattern\n(.*)/";

                            if(preg_match($pattern, $contents, $matches)){
                                $text = trim($matches[1]);
                                $tmpData[$t]['Borrower Name'] = $matches[1];
                            }else{if(DEBUG) echo "No matches found";}

                        }else{
                            $pattern2 = preg_quote($keyword, '/');
                            $pattern2 = "/$pattern2\n(.*)/";

                            if(preg_match($pattern2, $contents, $matches)){
                                $text = trim($matches[1]);
                                $tmpData[$t]['Borrower Name'] = $text;
                            }else{if(DEBUG) echo "No matches found";}
                        }


                        $keyword  = 'Legal Description';
                        $pattern1 = preg_quote($keyword, '/');
                        $pattern1 = "/$pattern1\n((http|https))(.*)/";
                        if(preg_match($pattern1, $contents, $matches)){
                            $text = trim($matches[3]);

                            $pattern = preg_quote($text, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)/";

                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Policy Number'] = $matches[2];
                            }else{if(DEBUG) echo "No matches found";}

                        }else{
                            $pattern2 = preg_quote($keyword, '/');
                            $pattern2 = "/$pattern1\n(.*)\n(.*)/";

                            if(preg_match($pattern2, $contents, $matches)){
                                $text = trim($matches[2]);
                                $tmpData[$t]['Policy Number'] = $text;
                            }else{if(DEBUG) echo "No matches found";}
                        }


                        $keyword2 = 'Legal Description';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})(.*)/";

                        if(preg_match($pattern2, $contents, $matches)){
                            $tmpData[$t]['Policy Start Date'] = $matches[3];
                            $tmpData[$t]['Policy Exp Date'] = $matches[5];
                        }else{if(DEBUG) echo "No matches found";}


                        $keyword  = 'Legal Description';
                        $pattern1 = preg_quote($keyword, '/');
                        $pattern1 = "/$pattern1\n((http|https))(.*)/";
                        if(preg_match($pattern1, $contents, $matches)){
                            $text = trim($matches[3]);

                            $pattern = preg_quote($text, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})(.*)/";

                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Policy Start Date'] = $matches[3];
                                $tmpData[$t]['Policy Exp Date'] = $matches[5];
                            }else{if(DEBUG) echo "No matches found";}

                        }else{
                            $pattern2 = preg_quote($keyword, '/');
                            $pattern2 = "/$pattern1\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})(.*)/";

                            if(preg_match($pattern2, $contents, $matches)){
                                $tmpData[$t]['Policy Start Date'] = $matches[3];
                                $tmpData[$t]['Policy Exp Date'] = $matches[5];
                            }else{if(DEBUG) echo "No matches found";}
                        }

                        
                        $keyword = 'Legal Description';
                        $pattern1 = preg_quote($keyword, '/');
                        $pattern1 = "/$pattern1\n((http|https))(.*)/";
                        if(preg_match($pattern1, $contents, $matches)){
                            $text = trim($matches[3]);

                            $pattern = preg_quote($text, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})(.*)\n(.*)/";

                            if(preg_match($pattern, $contents, $matches)){
                                $text = trim($matches[1]);
                                $tmpData[$t]['Policy Type'] = $matches[7];
                            }else{if(DEBUG) echo "No matches found";}

                        }else{
                            $pattern2 = preg_quote($keyword, '/');
                            $pattern2 = "/$pattern2\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})(.*)\n(.*)/";

                            if(preg_match($pattern2, $contents, $matches)){
                                $text = trim($matches[1]);
                                $tmpData[$t]['Policy Type'] = $matches[7];
                            }else{if(DEBUG) echo "No matches found";}
                        }


                        $keyword2 = 'paid by mortgagee';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(.*)\n(.*)/";
                        if(preg_match($pattern2, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2].", ".$matches[3];
                            $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2].", ".$matches[3];
                        }else{if(DEBUG) echo "No matches found";}


                    }elseif(preg_match($templatePattern3, $contents, $matches)){

                        $keyword = 'County';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Borrower Name'] = $text;
                        }else{if(DEBUG) echo "No matches found";}


                        $keyword2 = 'County';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(.*)/";
                        
                        if(preg_match($pattern2, $contents, $matches)){
                            $text = trim($matches[2]);
                            $tmpData[$t]['Policy Number'] = $text;
                        }else{if(DEBUG) echo "No matches found";}


                        $keyword2 = 'County';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                        if(preg_match($pattern2, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Policy Start Date'] = trim($matches[3]);
                            $tmpData[$t]['Policy Exy Date'] = trim($matches[5]);
                        }else{if(DEBUG) echo "No matches found";}


                        $keyword2 = 'County';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})(.*?)(\d{1,2}\/\d{1,2}\/\d{2,4})(.*)\n(.*)/";
                        if(preg_match($pattern2, $contents, $matches)){
                            $tmpData[$t]['Policy Type'] = $matches[7];
                        }else{if(DEBUG) echo "No matches found";}

                        $keyword2 = 'paid by mortgagee';
                        $pattern2 = preg_quote($keyword2, '/');
                        $pattern2 = "/$pattern2\n(.*)\n(.*)\n(.*)/";
                        if(preg_match($pattern2, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2].", ".$matches[3];
                            $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2].", ".$matches[3];
                        }else{if(DEBUG) echo "No matches found";}   

                    }else{
                        // default horizontal

                        $keyword1 = 'Named Insured';
                        $pattern1 = preg_quote($keyword1, '/');
                        $pattern1 = "/$pattern1\n(.*)/";

                        if(preg_match($pattern1, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Borrower Name'] = $text;
                        }else{if(DEBUG) echo "No matches found";}


                        $keyword1 = 'Policy';
                        $pattern1 = preg_quote($keyword1, '/');
                        $pattern1 = "/$pattern1\nNumber\n(.*)/";
                        
                        if(preg_match($pattern1, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Policy Number'] = $text;
                        }else{if(DEBUG) echo "No matches found";}

                        $keyword1 = 'Type and Contract';
                        $pattern1 = preg_quote($keyword1, '/');
                        $pattern1 = "/$pattern1\n(.*)/";

                        if(preg_match($pattern1, $contents, $matches)){
                            $text = trim($matches[1]);
                            $tmpData[$t]['Policy Type'] = $matches[1];
                        }else{if(DEBUG) echo "No matches found";}


                        $keyword1 = 'Location';
                        $pattern1 = preg_quote($keyword1, '/');
                        $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)/";

                        if(preg_match($pattern1, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1]." ".$matches[2]." ".$matches[3];
                            $tmpData[$t]['Residence'] = $matches[1]." ".$matches[2]." ".$matches[3];
                        }else{if(DEBUG) echo "No matches found";}

                        $keyword1 = 'Term';
                        $pattern1 = preg_quote($keyword1, '/');
                        $pattern1 = "/$pattern1\n(\d{2}\/\d{2}\/\d{4}).*?(\d{2}\/\d{2}\/\d{4})/";

                        if(preg_match($pattern1, $contents, $matches)){
                            $tmpData[$t]['Policy Start Date'] = $matches[1];
                            $tmpData[$t]['Policy Exp Date'] = $matches[2];
                        }else{if(DEBUG) echo "No matches found";}
                    }

                    $keyword='Loan Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Loan Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'First Mortgage';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nMortgagee Clause\n(.*)\n(.*)\n(.*)\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                    }else{
                        if(DEBUG) echo "No matches found";
                    }

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;

            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;

                            print_r($tmpData[$i]);
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;
                $tmpData[$templateId]['InsuranceCompany'] = $InsuranceCompany;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif($InsuranceCompany==='Travelers Commercial Insurance Company'){
        $DocData['InsuranceCompany']=$InsuranceCompany;
        
        if ($InsuranceAgency == "ACORD"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 2 (Travelers.pdf)
                // Travelers2.pdf DOC-1070
                if($t == 1){

                    $keyword='POLICY NUMBER';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\nLoan/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;

                        $pattern = preg_quote($keyword, '/');

                        $pattern = "/$pattern\n(.*)\nLoan:.([0-9]*)\n([0-9]*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Loan Number'] = preg_replace('/\s/', '', $matches[2]);
                            $tmpData[$t]['Policy Number'] = preg_replace('/\s/', '', $matches[3]);
                        }

                    }else{ if(DEBUG) echo "No matches found"; }
                    
                    $pattern = preg_quote('LOCATION/DESCRIPTION', '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";

                    $pattern1 = preg_quote('LOCATION DESCRIPTION', '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Insured Location']=$matches[1].", ".$matches[2];
                        $tmpData[$t]['Residence']=$matches[1].", ".$matches[2];
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Insured Location']=$matches[1].", ".$matches[2];
                        $tmpData[$t]['Residence']=$matches[1].", ".$matches[2];
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword = 'TERMINATED IF CHECKED';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(\d{1,2}\/\d{1,2}\/\d{1,2}).*?\n(\d{1,2}\/\d{1,2}\/\d{1,2}).*?\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?\n[$]((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='LOSS PAYEE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Loan:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern([0-9]+)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }
                }
            }


            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }elseif ($InsuranceAgency== "GEICO INSURANCE AGENCY, INC."){

            $keyword='S NAME AND ADDRESS:';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern\n(.*)./";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['InsuranceAgency'] = $matches[1];
            }else{if(DEBUG) echo "No matches found";}

            $keyword='INSURER:';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern.(.*)/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['InsuranceCompany'] = $matches[1];
            }else{if(DEBUG) echo "No matches found";}

            $keyword='POLICY NUMBER';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern(.*)POLICY/";
            if(preg_match($pattern, $contents, $matches)){
                $text = preg_replace('/[^0-9]/', '', $matches[1]);
                $DocData['Policy Number'] = $text;
            }else{if(DEBUG) echo "No matches found";}
            
            $keyword='MORTGAGE LOAN NUMBER: #';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern(.*)/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Loan Number'] = preg_replace('/[^0-9]/', '', $matches[1]);
            }else{if(DEBUG) echo "No matches found";}

            $keyword='PROPERTY ADDRESS:';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern\n(.*)/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Borrower Name'] = $matches[1];
                $keyword=$matches[1];
                $pattern = preg_quote($keyword, '/');
                $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nRECEIPT/";
                if(preg_match($pattern, $contents, $matches)){
                    $DocData['Borrower Name'].=', '.$matches[1];
                    $DocData['Insured Location'] = $matches[2].", ".$matches[3];
                }else{
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nRECEIPT/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Insured Location'] = $matches[1].", ".$matches[2];
                    }
                }
            }else{ if(DEBUG) echo "No matches found"; }
            
            $keyword='FROM:';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern(\d{1,2}\-\d{1,2}\-\d{4}).*?(\d{1,2}\-\d{1,2}\-\d{4})/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Policy Start Date'] = $matches[1];
                $DocData['Policy Exp Date'] = $matches[2];
            }else{if(DEBUG) echo "No matches found";}

            $keyword='DWELLING $';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern((:?\d{1,3}[,\.]?)+\d*)/";
            if(preg_match($pattern, $contents, $matches)){
                $text = trim($matches[1]);
                $DocData['Dwelling Amount'] = $matches[1];
            }else{if(DEBUG) echo "No matches found";}

            $keyword='MORTGAGEE';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern\n.*?#([0-9 ]*)\n([A-Za-z ]*).*\n(.*)\n(.*)\n(.*)/";
            if(preg_match($pattern, $contents, $matches)){
                $text = preg_replace('/[^0-9]/', '', $matches[1]);
                $DocData['First Mortgagee Loan Number'] = $text;
                $DocData['First Mortgagee'] = $matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
            }else{if(DEBUG) echo "No matches found";}
        }else{
            if(DEBUG) echo "No matches found";  
        }   

    }elseif($InsuranceCompany==='THE TRAVELERS HOME AND MARINE INSURANCE COMPANY'){
        $DocData['InsuranceCompany']=$InsuranceCompany;

        
        if ($InsuranceAgency== "GEICO INSURANCE AGENCY, INC."){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (Geico1.pdf, Geico3.pdf, Geico4.pdf) DOC-1052
                if($t == 1){

                    $keyword='S NAME AND ADDRESS:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)./";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['InsuranceAgency'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'INSURER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'INSURER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    
                    $keyword='MORTGAGE LOAN NUMBER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/(.*)\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Loan Number'] = preg_replace('/[^0-9]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='PROPERTY ADDRESS:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $keyword=$matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nRECEIPT/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'].=', '.$matches[1];
                            $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                            $tmpData[$t]['Residence'] = $matches[2].", ".$matches[3];
                        }else{
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)\nRECEIPT/";
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                                $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                            }
                        }
                    }else{ if(DEBUG) echo "No matches found"; }
                    
                    $keyword='FROM:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{1,2}\-\d{1,2}\-\d{4}).*?(\d{1,2}\-\d{1,2}\-\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='DWELLING';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n([$])((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='MORTGAGEE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?#([0-9 ]*)\n([A-Za-z ]*).*\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $text;
                        $tmpData[$t]['First Mortgagee'] = $matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";  
        }   

    }elseif($InsuranceCompany==='THE TRAVELERS INDEMNITY COMPANY'){
        $DocData['InsuranceCompany']=$InsuranceCompany;
        
        if ($InsuranceAgency== "GEICO INSURANCE AGENCY, INC."){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (geico2.pdf)
                // Geico5.pdf DOC-1052
                // Travelers1.pdf, Travelers2.pdf DOC-1052
                if($t == 1){

                    $keyword='NAME AND ADDRESS:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)./";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['InsuranceAgency'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'INSURER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'INSURER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='MORTGAGE LOAN NUMBER: #';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Loan Number'] = preg_replace('/[^0-9]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='PROPERTY ADDRESS:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $keyword=$matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nRECEIPT/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'].=', '.$matches[1];
                            $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                            $tmpData[$t]['Residence'] = $matches[2].", ".$matches[3];
                        }else{
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)\nRECEIPT/";
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                                $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                            }
                        }
                    }else{ if(DEBUG) echo "No matches found"; }
                    
                    $keyword='FROM:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{1,2}\-\d{1,2}\-\d{4}).*?(\d{1,2}\-\d{1,2}\-\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='DWELLING';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n([$])((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='MORTGAGEE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?#([0-9 ]*)\n([A-Za-z ]*).*\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $text;
                        $tmpData[$t]['First Mortgagee'] = $matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";  
        }   

    }elseif($InsuranceCompany==='TRAVELERS SPP'){
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }
        
        if ($InsuranceAgency== "GEICO INSURANCE AGENCY, INC."){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Template 1 (geico2.pdf)
                // Geico5.pdf DOC-1052
                // Travelers1.pdf, Travelers2.pdf DOC-1052
                if($t == 1){

                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='NAME AND ADDRESS:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)./";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['InsuranceAgency'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'INSURER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'INSURER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='MORTGAGE LOAN NUMBER: #';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Loan Number'] = preg_replace('/[^0-9]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='PROPERTY ADDRESS:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $keyword=$matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nRECEIPT/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'].=', '.$matches[1];
                            $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                            $tmpData[$t]['Residence'] = $matches[2].", ".$matches[3];
                        }else{
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)\nBILLING/";
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                                $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                            }
                        }
                    }else{ if(DEBUG) echo "No matches found"; }
                    
                    $keyword='FROM:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(\d{1,2}\-\d{1,2}\-\d{4}).*?(\d{1,2}\-\d{1,2}\-\d{4})/";

                    $keyword1 = 'FROM:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(\d{1,2}\-\d{1,2}\-\d{4})\n.*?(\d{1,2}\-\d{1,2}\-\d{4})/";
                    
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='DWELLING';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n([$])((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='MORTGAGEE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?#([0-9 ]*)\n([A-Za-z ]*).*\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^0-9]/', '', $matches[1]);
                        $tmpData[$t]['First Mortgagee Loan Number'] = $text;
                        $tmpData[$t]['First Mortgagee'] = $matches[2].", ".$matches[3].", ".$matches[4].", ".$matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";  
        }   

    }//DOC-1057 [american family] 
    elseif($InsuranceCompany==='American Family Insurance Company'){

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency =='American Family Ins'){

            $template   = 2;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                //AmericanFamily1 to AmericanFamily3 pdf
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }
                    $keyword='FROM: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)TO:/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $DocData['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Number: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern([0-9]*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Name of Insured: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*).Balance/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Policy Period:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*).-.(.*) /";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Type: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*) Due/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)\n(.*)Name:/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[2];
                        $keyword='Loan Number:';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern.(.*).Loan/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                        }
                    }else{if(DEBUG) echo "No matches found";}
                }
                
                // Template 2
                // AmericanFamily4
                if($t == 2){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword='PROPERTY INSURANCE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='POLICY NUMBER';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Name and Address:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $text;

                        $keyword=$matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)/";

                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                        }
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='PROPERTY LOCATION';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\nPROPERTY/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }else{
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nPROPERTY/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2].", ".$matches[3];
                        }else{
                            if(DEBUG) echo "No matches found";
                        }
                    }

                    $keyword = 'EFFECTIVE DATE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'EXPIRATION DATE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Exy Date'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='LOAN NUMBER';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Loan Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    
                    $keyword = 'NAME AND ADDRESS';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nNATURE OF INTEREST\n(.*)\n.*?\n([0-9]*)\n(.*)\n.*?\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[3]." ".$matches[4];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }
            

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif($InsuranceCompany==='Nationwide'){

        $DocData['InsuranceCompany']=$InsuranceCompany;
        
        if ($InsuranceAgency== "Nationwide"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1 (Nationwide.pdf)
                // Nationwide1.pdf, Nationwide2.pdf DOC-1069
                if($t == 1){

                    if(DEBUG) print_r("Inside t".$t."\n");

                    $keyword='Issued By:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $DocData['InsuranceCompany'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $DocData['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='go to';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)on your/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $DocData['Website'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='(Named Insured)';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\nIssued:/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Borrower Name'] = $matches[1]." ".$matches[2];
                        $DocData['Insured Location'] = $matches[3]." ".$matches[4];
                    }else{
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\nIssued:/";
                        if(preg_match($pattern, $contents, $matches)){
                            $DocData['Borrower Name'] = $matches[1];
                        }else{if(DEBUG) echo "No matches found";}
                    }


                    $keyword1 = 'Residence Premises Information:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\nPURCHASED:/";

                    $keyword2 = 'Residence Premises Information:';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)\n(.*)\nPURCHASED:/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $DocData['Residence'] = $matches[1].", ".$matches[2].", ".$matches[3];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $DocData['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Policy Period From:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*).to.(.*).but/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Policy Start Date'] = $matches[1];
                        $DocData['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='FIRST MORTGAGEE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\nLOAN.#(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['First Mortgagee'] = $matches[1].", ".$matches[2].", ".$matches[3].", ".$matches[4];
                        $DocData['First Mortgagee Loan Number']=$matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }


            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;
        }else{
            if(DEBUG) echo "No matches found";  
        }

    }//DOC-1067 [Erie.pdf]
    elseif($InsuranceCompany==='Erie'){

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency =='Erie Insurance'){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                if($t == 1){
                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='From:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Policy Start Date'] = $matches[1];
                        $DocData['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}
                    $keyword='Policy Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Policy Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Fax';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)\n([a-zA-Z@.].*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Fax No'] =  preg_replace('/[^0-9]/', '', $matches[1]);
                        $DocData['Email'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='WWW';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/\s+/', '', trim($matches[1]));
                        $DocData[$t]['Website'] = "WWW.".$text;
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Loan Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Borrower Name'] = $matches[1];
                        $DocData['Residence'] = $matches[4];
                        $DocData['Insured Location'] = $matches[4];
                        $DocData['Loan Number'] = preg_replace('/[^0-9]/', '', $matches[3]);
                    }else{if(DEBUG) echo "No matches found";}
                
                    $keyword = 'Long Name Other Interest';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)MR./";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['First Mortgagee'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif ($InsuranceCompany==="AMERICAN STRATEGIC INSURANCE CORP") {
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency == "The Progressive Corporation"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {

                // Progressive.pdf 
                // Progressive2.pdf DOC-1065

                if($t == 1){

                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Number'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Plan Type: :';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Residence Premises:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Premium';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = $matches[8];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='From:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(\d{2}\/\d{2}\/\d{4}).To:.(\d{2}\/\d{2}\/\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                     $text = trim($matches[1]);
                     $tmpData[$t]['Policy Start Date'] = $matches[1];
                     $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Named Insured:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $keyword=$matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\nEffective/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'].=', '.$matches[1];
                            $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                        }else{
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern\n(.*)\n(.*)/";
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                            }
                        }
                    }else{ if(DEBUG) echo "No matches found"; }

                    $pattern = "/.st.Mortgagee:\n([^0-9]*)\n(.*)\n(.*)\n.*#(.*).Escrow/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3];
                        $tmpData[$t]['First Mortgagee Loan Number'] = preg_replace('/[^0-9]/', '', $matches[4]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='2nd Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n([^0-9]*)\n(.*)\n(.*)\n.*#(.*).Escrow/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Second Mortgagee Loan Number'] = $matches[1];
                        $tmpData[$t]['Second Mortgagee'] = $matches[2].$matches[3].$matches[4].$matches[5];
                    }else{ if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;

        }else{
            if(DEBUG) echo "No matches found";  
        }

    }elseif ($InsuranceCompany==="Metropolitan Property and Casualty Insurance Company") {

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency == "MetLife Auto & Home"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1 (Metlife.pdf)
                // Metlife1.pdf DOC-1063

                if($t == 1){

                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Number'] = preg_replace('/[^0-9a-zA-Z]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Form:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Type'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='MetLife';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/([A-Za-z ]*)\n([A-Za-z ]*)\n(.*)\n(.*)\n$pattern/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Borrower Name']=$matches[1]==='' ? $matches[2] : $matches[1].", ".$matches[2];
                        $tmpData[$t]['Insured Location'] = $matches[3].", ".$matches[4];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='located at:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling $';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Policy Term:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(\d{2}\/\d{2}\/\d{4})\n.*(\d{2}\/\d{2}\/\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                     $tmpData[$t]['Policy Start Date'] = $matches[1];
                     $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Information:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\n3rd/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[5];
                    }else{echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;


        }else{
            if(DEBUG) echo "No matches found";  
        }

    }elseif($InsuranceCompany==='FAMILY SECURITY INSURANCE COMPANY'){

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency == "UPC Insuracnce"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) { 

                // Template 1 (UPCInsurance.pdf) DOC-1055
                // Insurance1.pdf, UPC Insurance2.pdf, UPC Insurance 4.pdf
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }
                    
                    $keyword='Policy Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Number'] = preg_replace('/[^0-9a-zA-Z]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='To: Fax:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)Page/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Fax No'] = preg_replace('/[^0-9]/', '', $matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern..((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $pattern = "/(\d{2}\/\d{2}\/\d{4}).*?(\d{2}\/\d{2}\/\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Policy Start Date'] = $matches[1];
                        $tmpData[$t]['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Premises';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)INSURANCE/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $keyword=$matches[1];
                        $pattern = preg_quote($keyword, '/');

                        $pattern = "/$pattern.*\n([A-Za-z ]*)\sST.*\n(.*)\n(.*)/";
                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'].=', '.$matches[1];
                            $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                        }else{
                            $pattern = preg_quote($keyword, '/');
                            $pattern = "/$pattern.*\n(.*)\sST.*\n(.*)/";
                            if(preg_match($pattern, $contents, $matches)){
                                $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                            }
                        }
                    }else{ if(DEBUG) echo "No matches found"; }
                    
                    $keyword='MORTGAGEE';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n.*#.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1].", ".$matches[1].", ".$matches[3].", ".$matches[4];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);


                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;
                
        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif($InsuranceCompany==="UNITED PROPERTY & CASUALTY INSURANCE COMPANY"){

        $DocData['InsuranceCompany']=$InsuranceCompany;

        if ($InsuranceAgency == "UPC Insuracnce"){

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) { 

                // Template 1 (UPCInsurance.pdf) DOC-1055
                // Insurance 3.pdf
                if($t == 1){

                    $keyword='POLICY NUMBER:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)[\r\n]+([^\r\n]+)Effective/";
                    if(preg_match($pattern, $contents, $matches)){
                        // $DocData['Policy Number'] = preg_replace('/[^0-9a-zA-Z]/', '', $matches[1]);
                        $DocData['Policy Number'] = preg_replace('/[^0-9a-zA-Z]/', '', $matches[2]);
                    }else{if(DEBUG) echo "No matches found";}

                    $pattern = "/(\d{2}\/\d{2}\/\d{4}).*?(\d{2}\/\d{2}\/\d{4})/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Policy Start Date'] = $matches[1];
                        $DocData['Policy Exp Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='INSURED:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)\n([A-Za-z, ]+)\n/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[2]);
                        $DocData['Borrower Name'] = $text;
                    }else{ if(DEBUG) echo "No matches found"; }

                    $keyword='Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern..((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Dwelling'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='1st Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)\n(.*)\n(.*)RATING/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['First Mortgagee'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        $templateCount = count($tmpData[$i]);

                        if($templateCount > $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $tmpData[$templateId]['InsuranceAgency'] = $InsuranceAgency;

                $DocData = $tmpData[$templateId];
                
            endif;
                
        }else{
            if(DEBUG) echo "No matches found";
        }

    }elseif ($InsuranceCompany==="Auto Club Family Insurance Company") {
        $DocData['InsuranceCompany']=$InsuranceCompany;
        $DocData['InsuranceAgency']="";
        if ($InsuranceAgency== "AAA Insurance"){

        }else{

            $keyword='NAMED';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/.* (.*)\n$pattern/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Policy Number'] = $matches[1];
            }else{if(DEBUG) echo "No matches found";}

            $keyword='Fax:';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern (.*)/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Fax No'] = $matches[1];
            }else{if(DEBUG) echo "No matches found";}

            $keyword='TO:';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern\n(.*) Auto/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Loan Number'] = $matches[1];
            }else{if(DEBUG) echo "No matches found";}
            
            $keyword='DWELLING';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern.* ((:?\d{1,3}[,\.]?)+\d*)/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['DWELLING'] = $matches[1];
            }else{if(DEBUG) echo "No matches found";}

            $pattern = "/(\d{2}\/\d{2}\/\d{4}).*\n.*\n.*(\d{2}\/\d{2}\/\d{4})/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Policy Start Date'] = $matches[1];
                $DocData['Policy Exp Date'] = $matches[2];
            }else{if(DEBUG) echo "No matches found";}

            $keyword='PREMISES';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern\n(.*)\(/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['Borrower Name'] = $matches[1];
                $keyword=$matches[1];
                $pattern = preg_quote($keyword, '/');

                $pattern = "/$pattern.*\n([A-Za-z ]*)\n(.*)\n(.*)/";
                if(preg_match($pattern, $contents, $matches)){
                    $DocData['Borrower Name'].=', '.$matches[1];
                    $DocData['Insured Location'] = $matches[2].", ".$matches[3];
                }else{
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*\n(.*)\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $DocData['Insured Location'] = $matches[1].", ".$matches[2];
                    }
                }
            }else{ if(DEBUG) echo "No matches found"; }
        }   

    }elseif ($InsuranceCompany==="Hippo") {
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "ACORD"){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 2;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Carrier:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    $keyword2 = 'Carrier;';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'POLICY NUMBER:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n((?=.*[A-Z])(?=.*\d)(?=.*[-]).*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;

                        $keyword = $matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\nADDITIONAL/";

                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'] = $matches[1];
                            $tmpData[$t]['Residence'] = $matches[2];
                            $tmpData[$t]['Insured Location'] = $matches[2];
                        }else{if(DEBUG) echo "No matches found";}

                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'EFFECTIVE DATE';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'EXPIRATION DATE';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?\n.((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Loan Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nBilling/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[3]." ".$matches[4]." ".$matches[5];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}
                }

                // Template 2
                if($t == 2){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'POLICY NUMBER:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.*?\n.*?\n((?=.*[A-Z])(?=.*\d)(?=.*[-]).*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;

                        $keyword = $matches[1];
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern\n(.*)\n(.*)\nADDITIONAL/";

                        $keyword1 = $matches[1];
                        $pattern1 = preg_quote($keyword1, '/');
                        $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\nADDITIONAL/";

                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'] = $matches[1];
                            $tmpData[$t]['Residence'] = $matches[2];
                            $tmpData[$t]['Insured Location'] = $matches[2];
                        }elseif(preg_match($pattern1, $contents, $matches)){
                            $tmpData[$t]['Borrower Name'] = $matches[1];
                            $tmpData[$t]['Residence'] = $matches[2]." ".$matches[3];
                            $tmpData[$t]['Insured Location'] = $matches[2]." ".$matches[3];
                        }else{if(DEBUG) echo "No matches found";}

                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'EFFECTIVE DATE EXPIRATION DATE';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[2]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Dwelling';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?\n.*?.((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Loan Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nMortgagee Information\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nBilling/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        if(!isset($tmpData[$i]['InsuranceAgency'])){
                            $tmpData[$i]['InsuranceAgency'] = $InsuranceAgency;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;

        }else{
            if(DEBUG){ print_r("DataExtraction Agency Default agency\n"); }
            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Carrier:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    $keyword2 = 'Carrier;';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Loan Number';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nBilling/";
                    
                    $keyword2 = 'Loan Number';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\nMortgagee Information\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nBilling/";
                    
                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[4]." ".$matches[5];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[3];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;
        }
    
    }elseif ($InsuranceCompany==="Country Financial") {
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "Country Financial"){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 2;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Policy Number:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Policy Type:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.*?\n.*?\n(.*)\n.*?\nEndorsements/";

                    $keyword2 = 'Policy Type:';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)\nPeril/";


                    $keyword = 'First Annual Premium: ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^a-z,A-Z,\s]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Type'] = $text;
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $text = preg_replace('/[^a-z,A-Z,\s]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Insured:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\nPolicy/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $tmpData[$t]['Insured Location'] = $matches[2]." ".$matches[3];
                        $tmpData[$t]['Residence'] = $matches[2]." ".$matches[3];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Premium Period Beginning:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Premium Period Ending:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Loan No:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = preg_replace('/[^0-9]/', '', trim($matches[1]));
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Mortgagee:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\nName:\n(.*)\n.*?\nType:/";

                    $keyword1 = 'Mortgagee:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nName:\nType:.*?\nAddress\n(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = trim($matches[1]);
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\/\/(.*)\//";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }

                // Template 2
                if($t == 2){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Policy Number:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Policy Type:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\nLimit/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^a-z,A-Z,\s]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Type'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Insured:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\nPremium/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $tmpData[$t]['Insured Location'] = $matches[2]." ".$matches[3];
                        $tmpData[$t]['Residence'] = $matches[2]." ".$matches[3];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'First Annual Premium:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.((:?\d{1,3}[,\.]?)+\d*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Dwelling Amount'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Premium Period Beginning: ';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Premium Period Ending: ';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Loan Number:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = preg_replace('/[^0-9]/', '', trim($matches[1]));
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Mortgagee Billed:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";

                    $keyword1 = 'Mortgagee Billed:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\n(.*)\nLoan/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4]." ".$matches[5];
                    }elseif(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='https:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\/\/(.*)\//";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Website'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        if(!isset($tmpData[$i]['InsuranceAgency'])){
                            $tmpData[$i]['InsuranceAgency'] = $InsuranceAgency;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;

        }else{
            if(DEBUG){ print_r("DataExtraction Agency Default agency\n"); }
            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Carrier:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    $keyword2 = 'Carrier;';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Loan Number';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nBilling/";
                    
                    $keyword2 = 'Loan Number';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\nMortgagee Information\n(.*)\n(.*)\n(.*)\n(.*)\n(.*)\nBilling/";
                    
                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[4]." ".$matches[5];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[3];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4];
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[5];
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;
        }
    
    }elseif ($InsuranceCompany==="Pemco Mutual") {
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "Pemco Mutual"){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Policy number:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Residence premises insured:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1.(.*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Evidence of property insurance for:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)\n(.*)\nThis confirms/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                        $tmpData[$t]['Insured Location'] = $matches[2].", ".$matches[3];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Policy period:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(\d{1,2}\/\d{1,2}\/\d{2,4}).*?(\d{1,2}\/\d{1,2}\/\d{2,4})/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[1]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[2]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Loan Number - ';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = preg_replace('/[^0-9]/', '', trim($matches[1]));

                        $pattern = preg_quote($matches[1], '/');
                        $pattern = "/$pattern\n(.*)/";

                        if(preg_match($pattern, $contents, $matches)){
                            $tmpData[$t]['First Mortgagee'] = trim($matches[1]);
                        }else{if(DEBUG) echo "No matches found";}

                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Dwelling';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.((:?\d{1,3}[,\.]?)+\d*)\nExtended/";

                    $keyword2 = 'Dwelling';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n.*?\n.*?\n.*?\n.*?\n.((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        if(!isset($tmpData[$i]['InsuranceAgency'])){
                            $tmpData[$i]['InsuranceAgency'] = $InsuranceAgency;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;
        }
    
    }elseif ($InsuranceCompany==="STILLWATER") {
        
        $DocData['InsuranceCompany']=$InsuranceCompany;

        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "STILLWATER INSURANCE"){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Evidence of Home Insurance';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['InsuranceCompany'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    
                    $keyword1 = 'Policy Number';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\nPolicy Eff Date\nPolicy Exp Date\nDate Printed\n.*?\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})\n(\d{1,2}\/\d{1,2}\/\d{2,4})\n.*?\nThe Companies/";

                    $keyword2 = 'Policy Number';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\nPolicy Eff Date\nPolicy Exp Date\nDate Printed\n.*?\n.*?\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})\n(\d{1,2}\/\d{1,2}\/\d{2,4})\n.*?\nThe Companies/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Number'] = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[2];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Policy Number'] = trim($matches[1]);
                        $tmpData[$t]['Policy Start Date'] = $matches[2];
                        $tmpData[$t]['Policy Exy Date'] = $matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Residence premises insured:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1.(.*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Residence premises insured:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1.(.*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Named Insured';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Property Address';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Insured Location'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Mailing Address';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(.*)/";
                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Residence'] = $matches[1].", ".$matches[2];
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = 'Loan';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.*?([0-9]+)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Name:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Dwelling Coverage A:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n.((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword2 = 'Dwelling Coverage A:';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n.*?\n.((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword3 = 'Dwelling Coverage A:';
                    $pattern3 = preg_quote($keyword3, '/');
                    $pattern3 = "/$pattern3\n.*?\n.*?\n.((:?\d{1,3}[,\.]?)+\d*)/";

                    $keyword4 = 'Dwelling Coverage A:';
                    $pattern4 = preg_quote($keyword4, '/');
                    $pattern4 = "/$pattern4\n.*?\n.*?\n.*?\n.((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }elseif(preg_match($pattern3, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }elseif(preg_match($pattern4, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        if(!isset($tmpData[$i]['InsuranceAgency'])){
                            $tmpData[$i]['InsuranceAgency'] = $InsuranceAgency;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;
        }else{

            if(DEBUG){ print_r("DataExtraction Agency : Default Agency\n"); }

            $keyword = 'Loan';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern.*?([0-9]+)/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['First Mortgagee Loan Number'] = trim($matches[1]);
            }else{if(DEBUG) echo "No matches found";}

            $keyword = 'Name:';
            $pattern = preg_quote($keyword, '/');
            $pattern = "/$pattern(.*)/";
            if(preg_match($pattern, $contents, $matches)){
                $DocData['First Mortgagee'] = trim($matches[1]);
            }else{if(DEBUG) echo "No matches found";}
        }

    }elseif ($InsuranceCompany==="Western Mutual Insurance Company") {
        $DocData['InsuranceCompany']=$InsuranceCompany;
        
        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "Western Mutual Insurance"){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Carrier:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    $keyword2 = 'Carrier;';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Policy Number';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = preg_replace('/[^A-Z,0-9,\-]/', '', trim($matches[1]));
                        $tmpData[$t]['Policy Number'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword = 'The premises covered by this policy is located at:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\nDwelling/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Residence'] = $text;
                        $tmpData[$t]['Insured Location'] = $text;
                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword='First Mortgagee';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n20514/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Borrower Name'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword1 = 'Continuous Policy Period';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)\n(\d{1,2}\/\d{1,2}\/\d{2,4})\n(\d{1,2}\/\d{1,2}\/\d{2,4})\n(.*)\n(.*)\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $text = trim($matches[2]);
                        $tmpData[$t]['Policy Start Date'] = trim($matches[2]);
                        $tmpData[$t]['Policy Exy Date'] = trim($matches[3]);
                        $tmpData[$t]['First Mortgagee'] = $matches[4]." ".$matches[5]." ".$matches[6];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='www';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)Fax/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = trim($matches[1]);
                        $tmpData[$t]['Website'] = "www.".$text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Fax Number';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Fax No'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword = 'Limit';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern\n(.*)\n(.*)\n((:?\d{1,3}[,\.]?)+\d*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['Dwelling Amount'] = trim($matches[3]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword='Loan';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)IMPOUND/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = preg_replace('/[^0-9]/', '', trim($matches[1]));
                    }else{if(DEBUG) echo "No matches found";}   
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        if(!isset($tmpData[$i]['InsuranceAgency'])){
                            $tmpData[$i]['InsuranceAgency'] = $InsuranceAgency;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;
        }

    }elseif ($InsuranceCompany==="Security First Insurance Company") {
        $DocData['InsuranceCompany']=$InsuranceCompany;
        
        if(DEBUG){ print_r("DataExtraction Company ".$InsuranceCompany."\n"); }

        if ($InsuranceAgency == "Security First Insurance Company"){

            if(DEBUG){ print_r("DataExtraction Agency ".$InsuranceAgency."\n"); }

            $template   = 1;
            $tmpData    = [];

            for ($t=1; $t <= $template; $t++) {
                
                // Template 1
                if($t == 1){
                    
                    if(DEBUG){ print_r("Inside t".$t."\n"); }

                    $keyword1 = 'Carrier:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1\n(.*)/";

                    $keyword2 = 'Carrier;';
                    $pattern2 = preg_quote($keyword2, '/');
                    $pattern2 = "/$pattern2\n(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }elseif(preg_match($pattern2, $contents, $matches)){
                        $tmpData[$t]['Company'] = trim($matches[1]);
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword1 = 'Policy Type:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1(.*)\nPolicy Number:(.*)\nPolicy Effective Date:(.*)\nPolicy Expiration Date:(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['Policy Type'] = $matches[1];
                        $tmpData[$t]['Policy Number'] = $matches[2];
                        $tmpData[$t]['Policy Start Date'] = $matches[3];
                        $tmpData[$t]['Policy Exy Date'] = $matches[4];

                        $keyword = 'Named Insured:';
                        $pattern = preg_quote($keyword, '/');
                        $pattern = "/$pattern(.*)\nMailing Address:(.*)\nEmail Address:(.*)Phone/";

                        if(preg_match($pattern, $contents, $matches)){
                            $text = preg_replace('/\s+/', '', trim($matches[3]));
                            $tmpData[$t]['Borrower Name'] = $matches[1];
                            $tmpData[$t]['Residence'] = $matches[2];
                            $tmpData[$t]['Insured Location'] = $matches[2];
                            $tmpData[$t]['Email'] = $text;
                        }else{if(DEBUG) echo "No matches found";}

                    }else{if(DEBUG) echo "No matches found";}
                    
                    $keyword1 = 'Name:';
                    $pattern1 = preg_quote($keyword1, '/');
                    $pattern1 = "/$pattern1.(.*)/";

                    if(preg_match($pattern1, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='www';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";
                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/\s+/', '', trim($matches[1]));
                        $tmpData[$t]['Website'] = "www".$text;
                    }else{if(DEBUG) echo "No matches found";}


                    $keyword = '';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(Dwelling)(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $text = preg_replace('/[^a-zA-Z0-9\']/', '', trim($matches[2]));
                        $tmpData[$t]['Dwelling Amount'] = $text;
                    }else{if(DEBUG) echo "No matches found";}

                    $keyword='Loan #:';
                    $pattern = preg_quote($keyword, '/');
                    $pattern = "/$pattern.(.*)/";

                    if(preg_match($pattern, $contents, $matches)){
                        $tmpData[$t]['First Mortgagee Loan Number'] = $matches[1];
                    }else{if(DEBUG) echo "No matches found";}
                    
                }
            }

            $tmpDataCount = count($tmpData);
            $templateData = [];

            $counted    = 0;
            $templateId = 0;
            if($tmpDataCount):
                for ($i=1; $i <= $template; $i++) {

                    if(isset($tmpData[$i])){

                        if(!isset($tmpData[$i]['InsuranceCompany'])){
                            $tmpData[$i]['InsuranceCompany'] = $InsuranceCompany;
                        }

                        if(!isset($tmpData[$i]['InsuranceAgency'])){
                            $tmpData[$i]['InsuranceAgency'] = $InsuranceAgency;
                        }

                        $templateCount = count($tmpData[$i]);

                        if($templateCount >= $counted){
                            $counted = $templateCount;
                            $templateId = $i;
                        }
                    }
                }

                if(DEBUG){ print_r("Final counted :".$counted."\n"); }
                if(DEBUG){ print_r("Final templateId :".$templateId."\n"); }

                $DocData = $tmpData[$templateId];
            endif;
        }

    }

    // if(DEBUG) print_r($DocData);
    return $DocData;
}


?>