<?php

/**********************************************************************/
/*                          SEND EMAIL                                */
/**********************************************************************/
function send_email($to,$from,$cc='',$bcc='',$subject,$message,$attachments_arr='',$plain_message='',$reply_to="",$cron=0) {

	LOG_MSG('INFO',"send_email(): START EMAILER_HOST=[".EMAILER_HOST."] to=[$to] from=[$from] cc=[$cc] bcc=[$bcc] subject=[$subject] reply_to=[$reply_to],cron=[$cron]");

	// Defaults
	$EOL = PHP_EOL;
	$separator = md5(time());
	if (!$from ) $from = EMAIL_FROM;
	if (!$cc) $cc=EMAIL_CC;
	if (!$bcc) $bcc=EMAIL_BCC;

	 // Copy of the message without the attachment details
	 // required to insert into the db 
	if ( $plain_message == '' ) $plain_message=$message;
	$plain_subject=$subject;

	// The subject should have the site name defined in tConf
	if($cron!=0) $subject  = "[".SITE_NAME."] $subject";

	// common headers
	$headers = "From: $from $EOL";
	$headers .= "CC: $cc $EOL";   
	$headers .= "Bcc: $bcc $EOL";

	if ($attachments_arr) {
		// main header
		$headers .= "MIME-Version: 1.0".$EOL; 
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

		// body
		//$body = "--".$separator.$EOL;
		$body = "Content-Transfer-Encoding: 7bit".$EOL.$EOL;
		$body .= "--".$separator.$EOL;
		$body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$EOL;
		$body .= "Content-Transfer-Encoding: 8bit".$EOL.$EOL;
		$body .= $message.$EOL;

		// attachment
		$attachment = chunk_split(base64_encode($attachments_arr[0]['data']));
		$body .= "--".$separator.$EOL;
		$body .= "Content-Type: application/octet-stream; name=\"".$attachments_arr[0]['filename']."\"".$EOL; 
		$body .= "Content-Transfer-Encoding: base64".$EOL;
		$body .= "Content-Disposition: attachment".$EOL.$EOL;
		$body .= $attachment.$EOL;
		$body .= "--".$separator."--";
		$message=$body;
	} else {
		$headers.="Content-Type: text/html $EOL";
		$headers.="MIME-Version: 1.0 $EOL";
	}

	// cron email process
	if ( $cron==0 ) {
		// Don't store security emails
		$email_resp=db_emails_insert(
						$from,
						$to,
						$cc,
						$bcc,
						$subject,
						$message,
						$reply_to,
						'',
						$headers,
						'PHPMAILER');
		if ( $email_resp['STATUS'] != 'OK' ) {
			LOG_MSG('ERROR',"send_email(): Error while inserting in EMails talble from=[$from] to=[$to]");
			return false;
		}
		return true;
	}

	// Setup parameters
	$status='SUCCESS';

	if (EMAILER_HOST == 'LOCAL' ) {
		LOG_MSG("INFO","********EMAIL********     TO: ".$to."   ".$headers."    SUBJECT:".$subject."     MESSAGE=[".$message."]");

		$resp=mail($to, $subject, $message, $headers); // SEND EMAIL
		if ( $resp ) $status='SUCCESS';
		else $status='FAILED';
	} elseif (EMAILER_HOST == 'AWS' ) {
		require_once BASEDIR.'/lib/aws/Mail.php';
		require_once BASEDIR.'/lib/aws/Mail/mime.php';
		// $to - This address must be verified with Amazon SES.

		// Setup Sender's & Recipient's addresses
		$aws_bcc='';
		$aws_cc='';
 		if ($bcc) {
			$aws_bcc=$bcc;
		}
		if ($cc) {
			$aws_cc=$cc;
		}

		// Data
		$text = strip_tags($plain_message);
		$html = $plain_message;

		$crlf = "\n";

		// Header
		$header = array(
			'From' => $from,
			'To' => $to,
			'Cc' => $aws_cc,
			'Reply-To' => $reply_to,
			'Subject' => $subject,
			'MIME-Version' => '1.0');

		// Header for CC mail
		$header_cc = array(
			'From' => $from,
			'To' => $aws_cc,
			'Cc' => $to,
			'Reply-To' => $from,
			'Subject' => $subject,
			'MIME-Version' => '1.0');

		// Header for BCC mail
		$header_bcc = array(
			'From' => $from,
			'To' => $from,
			'Reply-To' => $to,
			'Subject' => $subject,
			'MIME-Version' => '1.0');

		$mime = new Mail_mime(array('eol' => $crlf));

		$mime->setTXTBody($text);
		$mime->setHTMLBody($html);

		// Attachment
		if ($attachments_arr) {
			// Attach it to the message
			$mime->addAttachment($attachments_arr[0]['data'], 'application/pdf',$attachments_arr[0]['filename'],false);
		}

		$mimeparams=array(); 
		$mimeparams['text_encoding']="8bit"; 
		$mimeparams['text_charset']="UTF-8"; 
		$mimeparams['html_charset']="UTF-8"; 
		$mimeparams['head_charset']="UTF-8"; 

		$mimeparams["debug"] = "True"; 

		$body = $mime->get($mimeparams);
		$header = $mime->headers($header);

		LOG_ARR("INFO","AWS header Info",$header);

		// Setup connection info
		$smtpParams = array (
			'host' => AWS_HOST,
			'port' => AWS_PORT,
			'auth' => true,
			'username' => AWS_USERNAME,
			'password' => AWS_PASSWORD
		);

		// Create an SMTP client.
		$mail = Mail::factory('smtp', $smtpParams);

		// Send mail
		$result = $mail->send($to, $header, $body);

		if ( $aws_cc ) {
			// Send mail to cc address
			$header_cc = $mime->headers($header_cc);
			$result_cc = $mail->send($aws_cc, $header_cc, $body);
			LOG_MSG("INFO","Sending mail to cc=$aws_cc,result=".PEAR::isError($result_cc));
		}

		if ( $aws_bcc ) {
			// Send mail to bcc address
			$header_bcc = $mime->headers($header_bcc);
			$result_bcc = $mail->send($from, $header_bcc, $body);
			LOG_MSG("INFO","Sending mail to bcc=$aws_bcc,result=".PEAR::isError($result_bcc));
		}

		// Validate acknowledgment
		if (PEAR::isError($result)) {
			$resp=false;
			LOG_MSG('ERROR',"send_email(AWS): Error sending email=[".print_r($result,true)."]");
			$status='FAILED';
		} else {
			$resp=true;
			$status='SUCCESS';
		}
	}elseif (EMAILER_HOST=='PHPMAILER') {
		require_once BASEDIR.'/lib/mailer/PHPMailerAutoload.php';
		$mail = new PHPMailer();
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPAuth = true; // enable SMTP authentication
		//$mail->SMTPSecure = "ssl"; // sets the prefix to the servier
		$mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server
		$mail->Port = 587; // set the SMTP port for the GMAIL server
		$mail->Username = USERNAME; // GMAIL username
		$mail->Password = base64_decode(PASSWORD); // GMAIL password
		if($attachments_arr != ""){
			$mail->AddStringAttachment($attachments_arr[0]['data'],
	                        $attachments_arr[0]['filename']);
		}
		//Typical mail data
		$to_array = array();
		$to_array = explode(',',$to);
		for( $j=0 ; $j<count($to_array) ; $j++ ) {
			$mail->AddAddress($to_array[$j]);
		}
		$mail->SetFrom($from);
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->IsHTML(true);

		try{
			$mail->Send();
			$resp=true;
		} catch(Exception $e){
			$resp=false;
		}
	} else {
		LOG_MSG("INFO","EMAILER_HOST is OFF. Not sending email");
		$status='NOT SENT';
		$resp=true;
	}

	LOG_MSG("INFO","
	******************************EMAIL START [$status]******************************
	TO: [$to]
	$headers
	SUBJECT:[$subject]
	$plain_message
	******************************EMAIL END******************************");

	return $resp;
}

/**********************************************************************/
/*                          SEND SMS                                  */
/**********************************************************************/
function send_sms($from, $to, $message) {

	LOG_MSG('INFO',"send_sms(): START to=[$to]");

	$plain_message=$message;
	
	$api_key=setting_get('ClickaTell');
	if ( $api_key === '' ) {
		LOG_MSG('ERROR',"send_sms(): Error loading sms gateway API Key");
	}

	$status='FAILED';
	$to=urlencode($to);
	$message=urlencode($message);
	$gateway_url="HTTPS://platform.clickatell.com/messages/http/send?apiKey=";
	$from=urlencode($from);
	
	// Generate the URL base on the provider
	//HTTPS://platform.clickatell.com/messages/http/send?apiKey=0WJptMLfTKWkJU8GkD6XWw==&to=277412345678&content=Test+message+text&from=447781234567 
	$url=$gateway_url.$api_key."&to=".$to."&content=".$message; 
	
	// Send SMS
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$response=curl_exec($ch);
	curl_close($ch);     
	if ( strpos($response,'GID') ) $status='SUCCESS';
	LOG_MSG('INFO',"send_sms(): $response");

	LOG_MSG('INFO',"send_sms(): END");
	return true;
}


/**********************************************************************/
/*                          GET ARGUMENT                              */
/**********************************************************************/
// Function to get an argument from the form (either from GET or POST)
// A wrapper helps ensure we properly check for isset() and in future
// ensure that the user parameter passed in is actually safe
function get_arg($ARR,$var) {
	if (isset($ARR[$var])) { 
		return $ARR[$var]; 
	} else {
		return "";
	}
}

function get_clean_args($ARR,$var) {
	global $PURIFIER;

	// DO NOTHING
	//return get_arg($ARR,$var);

	// HTML ENTITIES CHECK
	return htmlentities(get_arg($ARR,$var));

	// HTML PURIFIER CODE
	if (isset($ARR[$var])) {
		if (is_array($ARR[$var])) {
			return $PURIFIER->purifyArray($ARR[$var]);
		} else {
			return $PURIFIER->purify($ARR[$var]);
		}
	} else {
			return "";
	}
}



/**********************************************************************/
/*                          TODAY'S DATE                              */
/**********************************************************************/
// Function to get todays date. 
function today($time=false) {
	if ($time) return date('Y-m-d-h-i-sa');
	else return date('Y-m-d');
}


/**********************************************************************/
/*                        CURL A GET REQUEST                          */
/**********************************************************************/
// Function to make an HTTP request using the curl lib
// Errors are logged into the apache error.log
// while the user sees a standard error message (shown where the
// function is called)
function curl_get($url,$POST=false) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	if (!$response) {
		trigger_error ("ERROR:curl_get() MSG=[".curl_error($ch)."]\n URL=[".$url."] ");
		return 0;
	}
//	trigger_error("CURL: [".$url."]");
	curl_close($ch); 
	return 1;
}


/**********************************************************************/
/*                       GET REAL IP OF USER                          */
/**********************************************************************/
function get_ip(){
	$ip='0.0.0.0';
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{
	  $ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{
	  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	elseif (isset($_SERVER['REMOTE_ADDR']))
	{
	  $ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function ajax_exit($status) {
	global $ERROR_MESSAGE,$SUCCESS_MESSAGE,$MOD,$ENTITY,$GO;

	$json_response=array();
	$json_response['go']=BASEURL."/$ENTITY/$GO";
	switch($status) {
		case 'SUCCESS':
				$json_response['status']='SUCCESS';
				$json_response['message']=$SUCCESS_MESSAGE;
				break;

		case 'ERROR':
		default:
				$json_response['status']='ERROR';
				$json_response['message']=$ERROR_MESSAGE;
				break;
	}

	LOG_ARR('INFO',"json_response",$json_response);
	echo json_encode($json_response);

	exit;
}

/**********************************************************************/
/*                              RELOAD FORM                            */
/**********************************************************************/
// Gets all POST elements and then puts them into the row array. This
// row array is then used in all forms to load in the default value of
// the fields
function reload_form() {
	global $ROW, $ERROR_MESSAGE;

	// Only reload if it hasn't been reloaded already
	if ( $ERROR_MESSAGE && ( !isset($ROW[0]['STATUS']) || $ROW[0]['STATUS'] != 'RELOAD')  ) {
		$ROW[0]=$_POST;
		$ROW[0]['STATUS']='RELOAD';
		LOG_MSG('INFO',"reload_form(): ROW=[".print_r($ROW,true)."]");
	}
}




/**********************************************************************/
/*                         VALIDATE PARAM                             */
/**********************************************************************/
// Input validation proc
function validate($name,$value,$minlen,$maxlen,$datatype="",$min_val="",$max_val="",$regexp="") {	//SAT0112:To prevent entering values which is not less than min_val and not greater than max val

	$resp=true;

	//echo "Validating: name=".$name." val=".$value." min=".$minlen." maxlen=".$maxlen." type=".$datatype." regexp=".$regexp."<br>";

	// If the value is empty and the field is not mandatory, then return
	if ( (!isset($minlen) || $minlen == 0) && $value == "" ) {
		return true;
	}

	// Empty Check
	// Changed to === to ensure 0 does not fail 
	if ( isset($minlen) && $minlen > 0 && $value === "" ) {
		add_msg("ERROR",$name." cannot be empty. <br/>"); 
		return false;
	}

	//echo "count($value)=[".preg_match("/^[0-9]+$/","12344a4")."]<br>";
	// MIN LEN check
	if ( isset($minlen) && strlen($value) < $minlen ) {
		add_msg("ERROR",$name." should be atleast ".$minlen." characters long. <br/>"); 
		return false;
	}

	// MAX LEN check
	if ( isset($maxlen) && strlen($value) > $maxlen ) {
		add_msg("ERROR",$name." cannot be longer than ".$maxlen." characters. <br/>"); 
		return false;
	}
	// ZIP Code  check for 5 or 9 digit
    if ( $datatype=='zipcode' && ((strlen($value) == $minlen)||(strlen($value) == $maxlen)) ) {
        return true;
    }elseif ($datatype=='zipcode'){
    	add_msg("ERROR",$name." should be 5 or 9 digits . <br/>");
        return false;
    }
	// CUSTOM REGEXP check
	if ( isset($regexp) && !preg_match("/$regexp/",$value) ) {
		add_msg("ERROR",$name." is not valid. <br/>"); 
		return false;
	}

	// MIN value check
	if( ($min_val !== '' && $value < $min_val) ) {
		add_msg("ERROR",$name." cannot be less than ".$min_val.". <br/>"); 
		return false;
	}

	// MAX value check
	if( ($max_val !== '' && $value > $max_val) ) {
		add_msg("ERROR",$name." cannot be greater than ".$max_val.". <br/>"); 
		return false;
	}
	// STANDARD DATATYPES check
	if ( isset($datatype) ) {
		switch ($datatype) {
			case "int":
				if ( filter_var($value, FILTER_VALIDATE_INT) === false  ) {
					add_msg("ERROR",$name." should contain only digits. <br/>"); 
					return false;
				} 
				break;
			case "decimal":
				if ( filter_var($value, FILTER_VALIDATE_FLOAT) === false ) {
					add_msg("ERROR",$name." should contain only digits. <br/>"); 
					return false;
				} 
				break;
			case "char": // anything
			case "varchar": // anything
			case "text": // anything
			case "mediumtext": // anything
				return true;
				break;
			case "bigint":
			case "tinyint":
				if (!preg_match("/^[0-9]+$/",$value)) {
					add_msg("ERROR",$name." should contain only digits. <br/>"); 
					return false;
				} 
				break;
			case "date":
				$arr=preg_split("/-/",$value); // splitting the array
				$yy=get_arg($arr,0); // first element of the array is month
				$mm=get_arg($arr,1); // second element is date
				$dd=get_arg($arr,2); // third element is year
				if( $dd == "" || $mm == "" || $yy == "" || !checkdate($mm,$dd,$yy) ){
					add_msg("ERROR",$name." is not a valid date, should be of the format YYYY-MM-DD <br/>"); 
					return false;
				}
				break;
			case "datetime":
				$arr=preg_split("/ /",$value); // splitting the array

				$date_arr=preg_split("/-/",$arr[0]);
				$time_arr=preg_split("/:/",$arr[1]);

				$yy=get_arg($date_arr,0); // first element of the array is month
				$mo=get_arg($date_arr,1); // second element is date
				$dd=get_arg($date_arr,2); // third element is year

				$hh=get_arg($time_arr,0); // first element is hour
				$mm=get_arg($time_arr,1); // second element is min
				$ss=get_arg($time_arr,2); // third element is sec
				if( $dd == "" || $mo == "" || $yy == "" || !checkdate($mo,$dd,$yy) || !checktime($hh,$mm,$ss)){
					add_msg("ERROR",$name." is not a valid date time, should be of the format YYYY-MM-DD HH:MM:SS<br/>[$yy-$mo-$dd $hh:$mm:$ss]<br/>"); 
					return false;
				}
				break;
			case "enum":
			case "PASSWORD":
				if (!preg_match("/^[a-zA-Z\-_0-9]+$/",$value)) {
					add_msg("ERROR",$name." can contain only alphabets,numbers,'-' and '_'. <br/>"); 
					return false;
				} 
				break;
			case "SIMPLE_STRING": // can only have alphabets, spaces, dots, -'s or +
				if (!preg_match("/^[a-zA-Z0-9\.\s\-\+]+$/",$value)) {
					add_msg("ERROR",$name." should contain only alphabets, numbers, spaces '.', '-' or '+'. <br/>"); 
					return false;
				} 
				break;
			case "EMAIL":
				if ( filter_var($value, FILTER_VALIDATE_EMAIL) == false ) {
					add_msg("ERROR",$name." is not valid, should be of the format abc@xyz.com. <br/>"); 
					return false;
				}
				break;
			case "MOBILE":
				if (!preg_match("/^[0-9]+$/",$value)) {
					add_msg("ERROR",$name." is not valid, should have only digits. <br/>"); 
					return false;
				} 
				break;
			case 'FILENAME':
				if ($value != basename($value) || !preg_match("/^[a-zA-Z0-9_\.]+$/",$value) || !preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $value)) {
					add_msg('ERROR', "Invalid $name");
					return false;
				}
				break;
			default:
				add_msg("ERROR",$name." is not valid. Please re enter.<br/>"); 
				return false;
		}
	}

	return true;
}




/**********************************************************************/
/*                    Generate random password                        */
/**********************************************************************/
// Mask Rules
// # - digit
// C - Caps Character (A-Z)
// c - Small Character (a-z)
// X - Mixed Case Character (a-zA-Z)
// ! - Custom Extended Characters
function gen_pass($mask) {
  $extended_chars = "!@#$%^&*()";
  $length = strlen($mask);
  $pwd = '';
  for ($c=0;$c<$length;$c++) {
    $ch = $mask[$c];
    switch ($ch) {
      case '#':
        $p_char = rand(0,9);
        break;
      case 'C':
        $p_char = chr(rand(65,90));
        break;
      case 'c':
        $p_char = chr(rand(97,122));
        break;
      case 'X':
        do {
          $p_char = rand(65,122);
        } while ($p_char > 90 && $p_char < 97);
        $p_char = chr($p_char);
        break;
      case '!':
        $p_char = $extended_chars[rand(0,strlen($extended_chars)-1)];
        break;
    }
    $pwd .= $p_char;
  }
  return $pwd; 
}

/**********************************************************************/
/*                         Check TIME                                 */
/**********************************************************************/
function checktime($hour, $min, $sec) {
	if ($hour == '' || $min == '' || $sec == '' ) return false;

	if ($hour < 0 || $hour > 23 || !is_numeric($hour)) {
		return false;
	}
	if ($min < 0 || $min > 59 || !is_numeric($min)) {
		return false;
	}
	if ($sec < 0 || $sec > 59 || !is_numeric($sec)) {
		return false;
	}

	return true;
}




/**********************************************************************/
/*                         Version info                               */
/**********************************************************************/
function ver() {

	// Makes a version number using the current dir name
	// dir name should end with YYYYMMDD eg: adfsafdsf20101214
	$dir=getcwd();
	$dir_len=strlen($dir);
	$ver=substr($dir,-6);

	
	$ver_p1=substr($ver,1,1);
	$ver_p2=substr($ver,2,2);
	$ver_p3=substr($ver,4,2);

	$ver=$ver_p1.".".$ver_p2.".".$ver_p3;

	return $ver	;

} 

function print_arr($arr) {
	echo "<pre>ARR=[".print_r($arr,true)."]</pre>";
}


/**********************************************************************/
/*                         USER MESSAGES                              */
/**********************************************************************/
function add_msg($type="SUCCESS",$msg="") {
	global $ERROR_MESSAGE, $SUCCESS_MESSAGE, $NOTICE_MESSAGE;
	global $DEBUG_MESSAGE;	

	LOG_MSG('INFO',"<<USER MESSAGE>>> $type: $msg");

	switch($type) {
		case "DEBUG": 
			if ($DEBUG_MESSAGE) $DEBUG_MESSAGE.="<br/>";
			$DEBUG_MESSAGE.=$msg;
			break;
		case "ERROR": 
			if ($ERROR_MESSAGE) $ERROR_MESSAGE.="<br/>";
			$ERROR_MESSAGE.=$msg;
			break;
		case "NOTICE": 
			if ($NOTICE_MESSAGE) $NOTICE_MESSAGE.="<br/>";
			$NOTICE_MESSAGE.=$msg;
			break;
		case "SUCCESS": 
		default:
			if ($SUCCESS_MESSAGE) $SUCCESS_MESSAGE.="<br/>";
			$SUCCESS_MESSAGE.=$msg;
			break;
	}
}


function show_msgs() {
	global $ERROR_MESSAGE, $SUCCESS_MESSAGE, $NOTICE_MESSAGE;
	include(STATIC_DIR."/html/messages.html");
	clear_msgs();
}

function clear_msgs() {
	global $ERROR_MESSAGE, $SUCCESS_MESSAGE, $NOTICE_MESSAGE;
	$ERROR_MESSAGE="";
	$SUCCESS_MESSAGE="";
	$NOTICE_MESSAGE="";
}


function get_static_page($org_page) {

	LOG_MSG('INFO',"get_static_page(): START [$org_page]");

	$page=make_clean_url(basename($org_page));
	$page_file=STATIC_DIR."/html/$page.html";


	// Validate
	if ( $page == '' || !file_exists($page_file) ) {
		add_msg("ERROR","Sorry, the page requested cannot be found!");
		LOG_MSG('ERROR',"get_static_page(): FAILED TO SHOW PAGE  [$page_file]");
		return false;
	}

	LOG_MSG('INFO',"get_static_page(): showing page [$page_file]");
	include($page_file); 
	return true;
}


/******************************************************************************/
/* Function: get_page_params($count)                                          */
/*           generates the different page params                              */
/******************************************************************************/
function get_page_params($count) {

	$page_arr=array();
	
	$ROWS_PER_PAGE=setting_get('rows_per_page');
	if($ROWS_PER_PAGE == '' || $ROWS_PER_PAGE == 0  ) $ROWS_PER_PAGE=10; 
	$firstpage = 1;
	$lastpage = intval($count / $ROWS_PER_PAGE);
	$page=(int)get_arg($_GET,"page");

	if ( $page == "" || $page < $firstpage ) { $page = 1; }	// no page no
	if ( $page > $lastpage ) {$page = $lastpage+1;}			// page greater than last page
	//echo "<pre>first=$firstpage last=$lastpage current=$page</pre>";

	if ($count % $ROWS_PER_PAGE != 0) {
		$pagecount = intval($count / $ROWS_PER_PAGE) + 1;
	} else {
		$pagecount = intval($count / $ROWS_PER_PAGE);
	}
	$startrec = $ROWS_PER_PAGE * ($page - 1);
	$reccount = min($ROWS_PER_PAGE * $page, $count);

	$currpage = ($startrec/$ROWS_PER_PAGE) + 1;


	if($lastpage==0) {
		$lastpage=null;
	} else {
		$lastpage=$lastpage+1;
	}

	if($startrec == 0) {
		$prevpage=null;
		$firstpage=null;
		if($count == 0) {$startrec=-1;}
	} else {
		$prevpage=$currpage-1;
	}
	
	if($reccount < $count) {
		$nextpage=$currpage+1;
	} else {
		$nextpage=null;
		$lastpage=null;
	}

	$appstr="&page="; 

	// Link to PREVIOUS page (and FIRST)
	if($prevpage == null) {
		$prev_href="#";
		$first_href="#";
		$prev_disabled="disabled";
	} else {
		$prev_disabled="";
		$prev_href=$appstr.$prevpage; 
		$first_href=$appstr.$firstpage; 
	}

	// Link to NEXT page
	if($nextpage == null) {
		$next_href = "#";
		$last_href = "#";
		$next_disabled="disabled";
	} else {
		$next_disabled="";
		$next_href=$appstr.$nextpage; 
		$last_href=$appstr.$lastpage; 
	}

	if ( $lastpage == null ) $lastpage=$currpage;

	$page_arr['page_start_row']=$startrec;
	$page_arr['page_row_count']=$reccount;
	$page_arr['total_rec']=$count;

	$page_arr['page']=$page;
	$page_arr['no_of_pages']=$pagecount;

	$page_arr['curr_page']=$currpage;
	$page_arr['last_page']=$lastpage;

	$page_arr['prev_disabled']=$prev_disabled;
	$page_arr['next_disabled']=$next_disabled;

	$page_arr['first_href']=$first_href;
	$page_arr['prev_href']=$prev_href;
	$page_arr['next_href']=$next_href;
	$page_arr['last_href']=$last_href;

	//LOG_MSG('INFO',"Page Array=".print_r($page_arr,true));
	return $page_arr;
}

function url($replace_key=NULL,$replace_val=NULL) {
	global $URL_BASE;

	$url=$URL_BASE;
	$replaced=0;

	foreach ($_GET as $key => $value) {
		
		if ( $key == 'mod' || $key == 'go' ) continue; 
		
		//LOG_MSG('INFO',"GOT Key: [$key]; Value: [$value] \n");
		// REPLACE
		if ( $replace_key && $replace_val && $replace_key == $key ) {
			$value=$replace_val;
			$replaced=true;
		}
		// REMOVE
		else if ( $replace_key && !$replace_val && $replace_key == $key ) {
			$replaced=true;
			continue;
		}
		
		//LOG_MSG('INFO',"SET Key: [$key]; Value: [$value] \n");
		$url.="&".$key."=".$value;
	}
	// If not already replaced then replace here
	if ( $replace_key && !$replaced ) {
		$url.="&".$replace_key."=".$replace_val;
	}
	
	return $url;
	
}



//URL: any url without the base eg: bill_id=23&val=abc. NULL will return base url
//URL_BASE: the base url incase its different from current base url. Null will use global base url
//URL_TYPE: can be 'AJAX' if you want to override the AJAX_MODE variable
function make_url($url=NULL,$url_base=NULL,$trigger=false) {
	global $URL_BASE;
	
	if ( $url_base == NULL ) { $url_base = $URL_BASE; }

	if ( AJAX_MODE ) {
		if ( $url == NULL ) {
			$url="load(".$url_base.")";
		} else {
			$url="load(".$url_base.",'&".$url."')";
		}
		if ( $trigger) { $url='href="#" onclick="'.$url.'"'; };
	} else {
		if ( $url == NULL ) {
			$url=$url_base;
			//$url="document.location.href='".$url_base."'; return false;";
		} else {
			$url=$url_base."&".$url;
			//$url="document.location.href='".$url_base."&".$url."'; return false;";
		}
		if ( $trigger) { $url='href="'.$url.'"'; };
	}
	//LOG_MSG('INFO',"make_url(): =========$url==============");
	return $url;
}

function make_base_url($mod=NULL,$ent=NULL) {

	if ( AJAX_MODE ) {
		if ( $mod == NULL || $ent == NULL ) {
			$base_url="return false;"; 	// No base url, so do nothing
		} else {
			$base_url="'".$mod."','".$ent."'";
		}
	} else {
		if ( $mod == NULL || $ent == NULL ) {
			$base_url="index.php?a=reset";
		} else {
			$base_url="index.php?mod=".$mod."&ent=".$ent;
		}
	}
	return $base_url;
}


function CATCH_ERROR($code, $message, $errFile, $errLine) {

	//Set message/log info
	$subject = "[".SITE_NAME." ERROR:".date("F j g:ia] ").": ".$message;
	$body = "
		\t\tFILE: $errFile:$errLine\n
		\t\tStack Trace:\n
		\t\t".print_r(_debug_string_backtrace(),true)."\n
		";
	//The same subject line and body of the email will get written to the error log.
	LOG_MSG('FATAL',"$subject\n $body");


	// Redirect to home
	//add_msg('ERROR',STANDARD_ERROR_MSG);
	header ("Location: ". GENERIC_ERR_PAGE );
	exit;
}


function LOG_ARR($level='INFO',$arr_name, $arr) {
	LOG_MSG($level,"===================ARRAY:$arr_name=====================\n".print_r($arr,true));
}

function LOG_MSG($level,$msg)
{
	global $MOD, $ENTITY;

	// We can't use the is_loggedin() function as that inturn uses LOG_MSG()
	$is_loggedin=false;
	if (isset($_SESSION["logged_in"]) && $_SESSION['logged_in'] ) $is_loggedin=true;
	if ( $is_loggedin ) {
		// LOGGED IN
		$id="[".REQID."]:".REQIP.":".get_clean_args($_SESSION,'user_id').":".get_clean_args($_SESSION,'email_id').":".get_clean_args($_SESSION,'role');
	} else {
		// NOT LOGGED IN
		$id="[".REQID."]:".REQIP.":GUEST";
	}

	// MOD and ENTITY
	$mod_entity="";
	if ($MOD != "") $mod_entity=$MOD;
	if ($ENTITY != "") $mod_entity.="/$ENTITY";

	$timestamp=date("h:i:sa:");
	$message="$timestamp: <$id> $level [$mod_entity] $msg\n";

	$log_message=true;
	switch ($level) {
		case 'ERROR':
				$st=_debug_string_backtrace();
				$message.="=================================STACK TRACE======================================\n".$message."=====================================================================================\n";
				break;
		case 'FATAL':
				$message.="=================================FATAL ERROR======================================\n".$message."=====================================================================================\n";
				break;
		case 'DEBUG':
				$log_message=false;
				break;
	}

	if ( $log_message ) {
		$fd = fopen(LOG_FILE, "a");
		fwrite($fd, $message);
		fclose($fd);
		//chmod(LOG_FILE,777);
	}
}


function _debug_string_backtrace() {
    ob_start();
    debug_print_backtrace();
    return ob_get_clean();
} 

function set_go($action) {
	global $GO;
	$GO=$action;
	return;
}


function is_file_uploaded($file_arr) {

	if ($file_arr['error'] == true) {
		LOG_ARR('INFO','is_file_uploaded(): FILE ARRAY',$file_arr);
		switch ($file_arr['error']) {
			case 1:
			case UPLOAD_ERR_INI_SIZE:
				add_msg('ERROR',"The uploaded file size exceeds 5MB. Please try with a smaller file.");
				LOG_MSG('ERROR','1:The uploaded file exceeds the upload_max_filesize directive in php.ini');
				break;
			case 2:
			case UPLOAD_ERR_FORM_SIZE:
				add_msg('ERROR',"The uploaded file size exceeds 5MB. Please try with a smaller file.");
				LOG_MSG('ERROR','2:The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. ');
				break;
			case 3:
			case UPLOAD_ERR_PARTIAL:
				add_msg('ERROR',"The uploaded file size exceeds 5MB. Please try with a smaller file.");
				LOG_MSG('ERROR','2:The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. ');
				break;
			case 4:
			case UPLOAD_ERR_NO_FILE:
				return false;
				add_msg('ERROR',"No file was uploaded");
				LOG_MSG('ERROR','4:No file was uploaded');
				break;
			case 6:
			case UPLOAD_ERR_NO_TMP_DIR:
				add_msg('ERROR',"There was a system error uploading your file. Please try later.");
				LOG_MSG('ERROR','6:Missing a temporary folder');
				break;
			case 7:
			case UPLOAD_ERR_CANT_WRITE:
				add_msg('ERROR',"There was a system error uploading your file. Please try later.");
				LOG_MSG('ERROR','7:Failed to write file to disk');
				break;
			case 8:
			case UPLOAD_ERR_EXTENSION:
				add_msg('ERROR',"Invalid file extension");
				LOG_MSG('ERROR','2:File upload stopped by extension');
				break;
			default:
				add_msg('ERROR',"Error uploading the file");
				LOG_MSG('ERROR',$file_arr['error'].':Unkown error');
				break;
		}
		return false;
	}
	return true;
}

function is_file_specified($file_arr) {
	LOG_ARR('INFO','is_file_specified(): file arr',$file_arr);
	if ($file_arr['error'] == '4' || $file_arr['error'] == UPLOAD_ERR_NO_FILE) {
		LOG_MSG('INFO',"is_file_specified(): returning FALSE");
		return false;
	} else {
		LOG_MSG('INFO',"is_file_specified(): returning TRUE");
		return true;
	}
}


function upload_image($html_img_name,$dest_img_file) {
	if ( isset($_FILES[$html_img_name]) && is_file_uploaded($_FILES[$html_img_name]) ) {
		if ($_FILES[$html_img_name]['type'] != 'image/jpeg' && $_FILES[$html_img_name]['type'] != 'image/pjpeg') {
			add_msg('ERROR','Sorry, you can only upload a jpg image. Please try again.');
			LOG_MSG('ERROR',"upload_image(): Got file type=[".$_FILES[$html_img_name]['type']."]");
			return false;
		}
		LOG_ARR('INFO','upload_image(): FILES',$_FILES);

		// Copy the file to the uploaded directory
		if ( !copy(get_arg($_FILES[$html_img_name],'tmp_name'),$dest_img_file) )  {
			add_msg('ERROR','There was an error uploading the image. Please try later');
			LOG_ARR('INFO','upload_image(): FILES',$_FILES);
			LOG_MSG('ERROR',"upload_image(); Error copying file to the directory: [$dest_img_file]");
			return false;
		}
		LOG_MSG('INFO',"upload_image(): New File: is [$dest_img_file]");
		return true;
	}
	return false;
}

// Generate a hash using the Global Hash key
function gen_hash($key,$number=false){
	if ($number) {
		$hash=abs(crc32(HASH_KEY.$key)) % 999999;
	} else {
		LOG_MSG('INFO','Algorithms: Blowfish['.CRYPT_BLOWFISH.'] md5['.CRYPT_MD5.'] ext_des['.CRYPT_EXT_DES.'] std_des['.CRYPT_STD_DES.']');
		$hash=crypt(HASH_KEY.$key);
	}
	LOG_MSG('INFO',"gen_hash(): KEY=[$key], NUMBER=[$number], HASH=[$hash]");

	return $hash;
}

// Converts a string to a string suitable for url format
// eg: Flipkat Store, Indiranagar => flipkart-store-indiranagar
// eg: ASF@#$%^$&^UJYTIU/..]\KL{>}<NVBDF AE#$@# => asf-ujytiu-kl-nvbdf-ae
function make_clean_url($str,$delimiter="-") {

	//LOG_MSG('INFO',"make_url_str(): Got string [$str]");
	// 4 steps to Ephipany
	// 2. Replace all special characters with a hypen
	// 3. Remove multiple hypens
	// 4. Trim the hypens at the end
	//LOG_MSG('INFO',"make_url_str(): Returned [$str]");

	return trim(preg_replace('/[-]+/',$delimiter,preg_replace('/[^0-9a-z]/',$delimiter,strtolower($str))),$delimiter);
}

// Same as above but does not convert into lower case and replaces by space instead of -
function make_clean_str($str) {
	return trim(preg_replace('/[ ]+/',' ',preg_replace('/[^0-9a-zA-Z]/',' ',ucwords(strtolower($str)))),' ');
}

function create_dir($existpath,$newdir){ 

	if(file_exists($existpath.'/'.$newdir)) return true;
	//CHECKING PATH EXISTING OR NOT
	if (file_exists($existpath)) {
		mkdir($existpath.'/'.$newdir, 0777, true);
	} else {
		if(mkdir($existpath, 0777, true)) create_dir($existpath,$newdir); else return false;
	} // IF PATH NOT EXISTING RETURNING FALSE	
 return true;	
}
function remove_dir($path){
	// checking the file is exits or not 
	if(!file_exists($path)) return true;
	chmod($path, 0777);
	//check its directory or files
	if(!is_dir($path) ){
		//if its not directory we need to unlink path
		if(unlink($path)) return true;
		else return false;
	} 
	// scandir gives list of files and subdirectory in folder
	foreach (scandir($path) as $files) {
		if($files == '.' || $files == '..')	continue;
		// cahnging the files permission mode
    chmod($path . "/" . $files, 0777);
    // using recursion method we deleting all the files in directory 
		if (!remove_dir($path . "/" . $files)) { 
			return false; 
    } 
	}
	// remove the directory
	if(!rmdir($path)) return false;

return true;	

}

function delete_files($file_path){
	// checking file exists or not
	if(!file_exists($file_path)) return true;

	// cahnging the files permission mode
	chmod($file_path, 0777);
	if(unlink($file_path)) return true;

return false;	
}

//IF money_format() function is undefined then follows this function

if (!function_exists('money_format')) { //Required for UNIX

	function money_format($format, $number)
	{
		$regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
		 '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
		if (setlocale(LC_MONETARY, 0) == 'C') {
			setlocale(LC_MONETARY, '');
		}
		$locale = localeconv();
		preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
		foreach ($matches as $fmatch) {
			$value = floatval($number);
			$flags = array(
				'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
				  $match[1] : ' ',
				'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
				'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
				  $match[0] : '+',
				'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
				'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
			);
			$width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
			$left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
			$right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
			$conversion = $fmatch[5];

			$positive = true;
			if ($value < 0) {
				$positive = false;
				$value  *= -1;
			}
			$letter = $positive ? 'p' : 'n';

			$prefix = $suffix = $cprefix = $csuffix = $signal = '';

			$signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
			switch (true) {
				case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
					$prefix = $signal;
					break;
				case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
					$suffix = $signal;
					break;
				case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
					$cprefix = $signal;
					break;
				case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
					$csuffix = $signal;
					break;
				case $flags['usesignal'] == '(':
				case $locale["{$letter}_sign_posn"] == 0:
					$prefix = '(';
					$suffix = ')';
					break;
			}
			if (!$flags['nosimbol']) {
				$currency = $cprefix .
				($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
				$csuffix;
			} else {
				$currency = '';
			}
			$space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

			$value = number_format($value, $right, $locale['mon_decimal_point'],
			$flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
			$value = @explode($locale['mon_decimal_point'], $value);

			$n = strlen($prefix) + strlen($currency) + strlen($value[0]);
			if ($left > 0 && $left > $n) {
				$value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
			}
			$value = implode($locale['mon_decimal_point'], $value);
			if ($locale["{$letter}_cs_precedes"]) {
				$value = $prefix . $currency . $space . $value . $suffix;
			} else {
				$value = $prefix . $value . $space . $currency . $suffix;
			}
			if ($width > 0) {
				$value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
				STR_PAD_RIGHT : STR_PAD_LEFT);
			}
			$format = str_replace($fmatch[0], $value, $format);
		}
		return $format;
	}
}
// Encrypt the password
function encrypt_pass($password){
	return md5(PASSWORD_SALT.$password);
}

/**********************************************************************/
/*                          IMPORT CSV                                */
/**********************************************************************/
function encode_csv_field($string) {
	if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
		$string = preg_replace('/"/', '\'', $string);
		$string = '"' . preg_replace("/\r\n/", "", $string) . '"';
	}
	return $string;
}
function clean_csv_string($str) {
	return preg_replace('/[ ]+/',' ',trim($str));
}
function floordec($number,$decimals=2){    
	return floor($number*pow(10,$decimals))/pow(10,$decimals);
}
function is_dir_empty($dir) {
	if (!is_readable($dir)) return true; 
	return (count(scandir($dir)) == 2);
}

function image_resize(	$source_image_path,
						$resize_width,
						$resize_height=0,
						$autocrop=0) {
	LOG_MSG("INFO","image_resize() :START source_image_path=[$source_image_path],
											resize_width=[$resize_width],
											resize_height=[$resize_height],
											autocrop=[$autocrop]");
	$is_streched=false;
	// As the resize dimension changes as the flow goes, store it in a variable for the padding purpose
	/******************************************************************/
	/*     STEP1:Get the image properties                             */
	/******************************************************************/
	// Get source image properties
	list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
	if($source_image_width>$resize_width){
		$ratio=$source_image_width/$resize_width;
		$resize_height=$source_image_height/$ratio;
	} else {
		$resize_width=$source_image_width;
		$resize_height=$source_image_height;
	}
	$org_resize_width=$resize_width;
	$org_resize_height=$resize_height;

	if (($source_image_width == $resize_width) && ($source_image_height == $resize_height)) { 
		LOG_MSG("INFO","image_resize() equel");
		return true; }
	/******************************************************************/
	/*              STEP2:Create image resource                       */
	/******************************************************************/
	switch ($source_image_type) {
		case IMAGETYPE_GIF:
			$source_gd_image = imagecreatefromgif($source_image_path);
			break;
		case IMAGETYPE_JPEG:
			$source_gd_image = imagecreatefromjpeg($source_image_path);
			break;
		case IMAGETYPE_PNG:
			$source_gd_image = imagecreatefrompng($source_image_path);
			break;
	}
	if ($source_gd_image == false) {
		LOG_MSG("INFO","image_resize() : Failed to create image resource source_gd_image=[$source_gd_image]");
		return false;
	}
	// Check whether the image is original image
	$is_org_image=false;
	if ( $resize_width == '' && $resize_height == '' ) $is_org_image=true;
	// Check whether the image resize is required, else skip to padding
	$is_img_resize=true;
	if ( !$is_streched && $resize_width >= $source_image_width && $resize_height >= $source_image_height ) $is_img_resize=false;
	// If original image or image resize is not required, skip all the steps till padding
	if ( !$is_org_image && $is_img_resize ) {
	// If the image dimensions are equal to the required dimensions
	if (($source_image_width == $resize_width) && ($source_image_height == $resize_height)) { return true; }
	// FIXED IMAGE - PROTRONICS
	// When the image width or height is too small, or if there is more difference in width and height
	// fixing the image will stretch or compress the image which will not look like the original image
	// To avoid this, we need to reduce the image by same percentage on all the sides. The percentage 
	// can be calculated based on the max resize width or height. 
	// If the modified width and height is greater than the resize width and height resp., then re-size with the minimum resize width or height
	// OUR LAST AIM IS EITHER WIDTH AND HEIGHT SHOULD NOT EXCEED THE ORIGINAL RESIZE WIDTH AND HEIGHT
	if ( !$is_streched ) {
		// Return if the image is smaller than the resize image
		LOG_MSG("INFO","image_resize() : ############## ORIGINAL IMAGE SIZE AND RESIZE ################
															source_image_width=[$source_image_width],
															source_image_height=[$source_image_height],
															resize_width=[$resize_width],
															resize_height=[$resize_height]");
		// Find the new resize_height
		if ( ($resize_width >= $resize_height && $resize_width <= $source_image_width) || 
			 ($resize_width < $resize_height && $resize_height > $source_image_height) ) {
				$resize_percent=($resize_width*100)/$source_image_width;
				$mod_resize_height=($resize_percent*$source_image_height)/100;
			// When mod_resize_height is > than original resize_height, then find the width based on the original resize_height
			if ( $mod_resize_height > $resize_height ) {
				LOG_MSG("INFO","image_resize() : mod_resize_height=[$mod_resize_height] > resize_height=[$resize_height]");
				$resize_percent=($resize_height*100)/$source_image_height;
				$resize_width=($resize_percent*$source_image_width)/100;
			} else {
				$resize_height=$mod_resize_height;
			}
		}
		// Find the new resize_width
		elseif ( ($resize_width >= $resize_height && $resize_width > $source_image_width) || 
				 ($resize_width < $resize_height && $resize_height <= $source_image_height) ) {
					$resize_percent=($resize_height*100)/$source_image_height;
					$mod_resize_width=($resize_percent*$source_image_width)/100;
			// When mod_resize_width is > than original resize_width, then find the height based on the original resize_width
			if ( $mod_resize_width > $resize_width ) {
			LOG_MSG("INFO","image_resize() : mod_resize_width=[$mod_resize_width] > resize_width=[$resize_width]");
				$resize_percent=($resize_width*100)/$source_image_width;
				$resize_height=($resize_percent*$source_image_height)/100;
			} else {
				$resize_width=$mod_resize_width;
			}
		}
		LOG_MSG("INFO","image_resize() : ############## MODIFIED IMAGE SIZE AND RESIZE ################
															source_image_width=[$source_image_width],
															source_image_height=[$source_image_height],
															resize_percent=[$resize_percent],
															resize_width=[$resize_width],
															resize_height=[$resize_height]");
	}
	if ( $autocrop==1 ) {
		/******************************************************************/
		/*     STEP3:Find the crop ratio                                  */
		/******************************************************************/
		$crop_ratio=($resize_width*$source_image_height)/($resize_height*$source_image_width);
		/******************************************************************/
		/*     STEP4:Calculate the cropped image width and height         */
		/******************************************************************/
		if ( $crop_ratio < 1 ){
			$new_height = $source_image_height;
			$new_width = $source_image_width*$crop_ratio;
		}else if( $crop_ratio > 1 ){
			$new_width = $source_image_width;
			$new_height = $source_image_height/$crop_ratio;
		}else if( $crop_ratio == 1 ){
			$new_width = $source_image_width;
			$new_height = $source_image_height;
		}
		LOG_MSG("INFO","image_resize() : new_width=[$new_width],
											new_height=[$new_height]");
		/******************************************************************/
		/*     STEP4:Find the crop coordinates                            */
		/******************************************************************/
		$centreX = round($source_image_width / 2);
		$centreY = round($source_image_height / 2);
		$x = max(0, $centreX - round($new_width / 2) );
		$y = max(0, $centreY - round($new_height / 2));
		LOG_MSG("INFO","image_resize() : coordinates x=[$x],
														y=[$y]");
	} else {
		$x=0;
		$y=0;
		$new_width = $source_image_width;
		$new_height = $source_image_height;
	}
	/******************************************************************/
	/*     STEP5:Crop/Resize the image                                */
	/******************************************************************/
	// Create a black image of specified crop width and size into which the area to be cropped is interpolated
	if ( !$resized_gd_image = imagecreatetruecolor ($resize_width, $resize_height) ) {
		LOG_MSG("INFO","image_resize() : Function 'imagecreatetruecolor' failed to create image identifier representing black image of the size $resize_widthX$resize_height");
		return false;
	}
	// Copy the area to be cropped onto the black image
	if ( !imagecopyresampled( $resized_gd_image, 
							 $source_gd_image, 
							 0,
							 0,
							 $x, // Center the image horizontally
							 $y, // Center the image vertically
							 $resize_width,
							 $resize_height,
							 $new_width, 
							 $new_height ) ) {
		LOG_MSG("INFO","image_resize() : Function 'imagecopyresampled' failed to create the resized/cropped image");
		return false;
	}
	// Save the image file/Create a jpeg image
	if ( !imagejpeg( $resized_gd_image, $source_image_path, 100) ) {
		LOG_MSG("INFO","image_resize() : Function 'imagejpeg' failed to create the JPEG image file");
		return false;
	}
	}
	/******************************************************************/
	/*      STEP6: Image Padding (Only for non streched images)       */
	/******************************************************************/
	if ( !$is_streched ) {
		// Take maximum image width and height for zoom in case of original image
		if ( $is_org_image ) {
			if ( $source_image_width > $source_image_height ) {
				$output_w=$output_h=$source_image_width;
			} else {
				$output_w=$output_h=$source_image_height;
			}
			// calc new image dimensions
			$new_w = $source_image_width;
			$new_h = $source_image_height;
		} else {
			$output_w=$org_resize_width;
			$output_h=$org_resize_height;
			if ( $is_img_resize ) { 
				$source_gd_image=$resized_gd_image; 
				// calc new image dimensions
				$new_w = $resize_width;
				$new_h = $resize_height;
			} else {
				$new_w = $source_image_width;
				$new_h = $source_image_height;
			}
		}
		// determine offset coords so that new image is centered
		$offest_x = ($output_w - $new_w) / 2;
		$offest_y = ($output_h - $new_h) / 2;
		// create new image and fill with background colour
		$new_img = imagecreatetruecolor($output_w, $output_h);
		$bgcolor = imagecolorallocate($new_img, 255, 255, 255); // red
		imagefill($new_img, 0, 0, $bgcolor); // fill background colour
		// copy and resize original image into center of new image
		imagecopyresampled($new_img, $source_gd_image, $offest_x, $offest_y, 0, 0, $new_w, $new_h, $new_w, $new_h);
		LOG_MSG('INFO',"image_resize(): ############ PADDING IMAGE ############ 
												is_img_resize=[$is_img_resize],
												New Image=[$new_img],
												Source image = [$source_gd_image],
												Offset X = [$offest_x],
												Offset Y = [$offest_y],
												New Width = [$new_w],
												New Height= [$new_h],
												Output Width = [$output_w],
												Output Height = [$output_h]");
		//save it
		imagejpeg($new_img, $source_image_path, 80);
	}
	// Destroy the resource image file
	if ( !imagedestroy($source_gd_image) ) {
		LOG_MSG("INFO","image_resize() : Function 'imagedestroy' failed to destroy the resource image file");
		return false;
	}
	LOG_MSG("INFO","image_resize() :END");
	return true;
}
