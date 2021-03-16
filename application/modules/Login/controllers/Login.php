<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();	
		ob_start();
		$this->load->library('form_validation');
		$this->load->library('Aes');
		$this->load->library('AesCtr');
		$this->load->model('LoginModel');
		$this->load->helper('string');
		

	}
	public function index()
	{	
		if($this->session->userdata('UserUID'))
		{
			redirect(base_url('Dashboard'));
		}else{
		$data=$this->BrowserDetection();
		$this->load->view('index', $data);	
		}
	}

	public function forgotpassword()
	{
		$this->load->view('forgotpassword');		
	}

	public function firstloginchangepasswordpage()
	{
		$this->load->view('firstloginchangepassword');	
	}

	public function updatepasswordpage()
	{
		$this->load->view('updatepassword');		
	}

	public function changepasswordpage()
	{
		$data['content'] = 'changepassword';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);		
	}

	public function Otp()
	{
		$data['UserUID'] = $this->session->userdata('Temp_UserUID');
		//echo "otp work coorectly";
		$this->load->view('otp',$data);
	}

	function Logout()
	{ 

		$this->session->sess_destroy();
		redirect(base_url('Login')); 
	}

	 function LoginSubmit()
	{
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('Username', '', 'required');
		$this->form_validation->set_rules('Password', '', 'required');

		if($this->form_validation->run() == TRUE)
		{
			
				$Username = $this->input->post('Username');
				$Password = md5($this->input->post('Password'));
				$theUrl = $this->input->post('theUrl');
				$validate = $this->LoginModel->checkUserByCreatedDate($Username);
				$result = $this->LoginModel->CheckLoginExist($Username);
				foreach($result as $data)
				{
					$Email = $data->EmailID;
					$UserName = $data->UserName;
				}
				if (($validate)) {
					$DynamicAccessCode = random_string('numeric', 8);
						$this->LoginModel->SaveDynamicAccessCode($Username,$DynamicAccessCode);
								//Load email library
		// $this->load->library('email'); 

		// $config['protocol']    = 'smtp';
	 //    $config['smtp_host']    = 'ssl://smtp.gmail.com';
	 //    $config['smtp_port']    = '465';
	 //    $config['smtp_timeout'] = '7';
	 //    $config['smtp_user']    = 'madhuri.avanze@gmail.com';
	 //    $config['smtp_pass']    = 'avanze@123';
	 //    $config['charset']    = 'utf-8';
	 //    $config['newline']    = "\r\n";
	 //    $config['mailtype'] = 'html'; // or html
	 //    $config['validation'] = TRUE; // bool whether to validate email or not      

	 //    $this->email->initialize($config);
 		$from_email = "notifications@direct2title.com"; 
        $to_email = $Username; 
         //Load email library 
         $this->load->library('email'); 
   
         $this->email->from($from_email); 
         $this->email->to($to_email);
         $this->email->subject('Your Dynamic Access Code'); 
         $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> </head> <body> <div class="row" style="border:2px solid #ccc;width:750px;margin:0 auto;"> <div class="row" > <p style="background-color:#f5f3f3;color:#808080;text-align:center;line-height:50px;font-size:20px;margin:10px;"><img src="https://www.doctrac.direct2title.com/assets/img/doctrack_wit.png" alt="logo" class="logo-img" style="margin-top: 15px;margin-bottom: -10px;width: 21%;"></p></div><br/> <table style="max-width: 620px; margin: 0 auto;font-size:15px;line-height:22px;"> <tbody> <tr style="height: 23px;"> <td style="height: 23px;"><span style="font-weight: bold;">Hi '.$UserName.',</span></td></tr><tr style="height: 43px;"> <td style="height: 43px;">You recently requested to reset your password for your Doctrac Account- Click the button below to reset it.<strong> Your Access Code is '.$DynamicAccessCode.'.</strong></td></tr><tr style="height: 29px;"> <td style="text-align: center; height: 29px;"><a href="'.base_url().'Login/updatepasswordpage" style="background-color: red; color: #fff; display: inline-block; padding: 10px 10px 10px 10px; font-weight: bold; border-radius: 5px; text-align: center;font-size: 12px;text-decoration:none;border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 5px 10px 5px 10px;margin: 7px 0; text-decoration:none; display:inline-block; color: #FFFFFF;background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040));background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040);background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040);background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040);background-image: -o-linear-gradient(top, #ff9a9a, #ff4040);background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);" class="btn button_example"><span style="padding:10px,10px,10px,10px;">Reset Your Password</style></a></td></tr><tr style="height: 43px;"> <td style="height: 43px;">If you did not request a password reset. please ignore this email or reply to let us know. This password reset is only valid for the next 30 minutes.</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 23px;"> <td style="height: 23px;">Thanks,</td></tr><tr style="height: 23px;"> <td style="height: 23px;">Doctrac Team</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"><span style="font-weight: bold;">P.S.</span> We also love hearing from you and helping you with any issues you have. Please reply to this email if you want to ask a question or just say hi.</td></tr><tr style="height: 23px;"> <td style="height: 23px;border-bottom: 1px solid #ccc;"></td></tr><tr style="height: 23px; "> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"> If you are having trouble clicking the password reset button, copy and paste the URL below into your web browser and click forgot password link.</td></tr><tr style="height: 43px;"> <td style="height: 43px;font-size: 12px;text-decoration: underline;"> <a href="https://www.doctrac.direct2title.com"> https://www.doctrac.direct2title.com</a></td></tr></tbody> </table> <div class="row" style="margin:10px,10px,10px,10px;margin-left:10px;margin-right:10px;"> <p style="padding:10px,10px,10px,10px;background-color:#f5f3f3;color:#907f7f;text-align:center;line-height:50px"><strong> Doctrac Team. All Rights Reserved.</strong></p></div></div></body></html><style type="text/css">#main{max-width: 600px;margin: 0 auto;}.button_example{border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 10px 10px 10px 10px; text-decoration:none; display:inline-block; color: #FFFFFF;background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040));background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040);background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040);background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040);background-image: -o-linear-gradient(top, #ff9a9a, #ff4040);background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);}.button_example:hover{border:px solid #FFFFFF;background-color: #ff6767; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff6767), to(#ff0d0d));background-image: -webkit-linear-gradient(top, #ff6767, #ff0d0d);background-image: -moz-linear-gradient(top, #ff6767, #ff0d0d);background-image: -ms-linear-gradient(top, #ff6767, #ff0d0d);background-image: -o-linear-gradient(top, #ff6767, #ff0d0d);background-image: linear-gradient(to bottom, #ff6767, #ff0d0d);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff6767, endColorstr=#ff0d0d);}</style>'); 
   
         //Send mail 
         if($this->email->send()) 
         {
         	$res = array("validation_error" => 2,'message' => 'Your account has expired.Check your mail to reset your password');
						echo json_encode($res);exit;
         	
         }
         else{
         	// echo 'No';
         	echo json_encode(array('validation_error'=>2,'message'=>'Your account has expired.Check your mail to reset your password'));exit;
         }
				}else{
				$result = $this->LoginModel->CheckLogin($Username,$Password);
				 if(!empty($result['Password']))
				{

					$salt_key = $this->session->userdata('salt_key');
					$secret = 'zdfghtuOdfPKJL2551*^$#$()k';
					$encrypt = new AesCtr();
					$encrptval = $encrypt->encrypt($salt_key, $secret, 256); 
					$userpwd = md5($Password.$encrptval);
					$dbpwd = md5($result['Password'].$encrptval);		
					
				if($userpwd == $dbpwd)
					{ 
						/*AUDIT TRAIL START*/
						/*Browser Detection Start */
						//$browser = $this->load->library('Browserdetection');
						$library = $this->load->library('Browserdetection');
						$response['message'] = '';
						$response['link'] = 'javascript:void(0);';
						// echo '<pre>'; print_r($this->browserdetection); exit;
						foreach ($this->config->item('BROWSERS') as $key => $browser) {
							$browserversion = $this->browserdetection->getVersion();
							$strposition = stripos($browserversion, '.');
							$majorversion = substr($browserversion, 0, $strposition);
							$minorversions = substr($browserversion, $strposition);
							$minorversions = str_replace('.', '', $minorversions);
							$version = number_format($majorversion . '.' . $minorversions);
							// Check is Current Browser Version is Below defined.
							if ($this->browserdetection->getName() == $browser['Name']) {

								if (number_format($version) < number_format($browser['DefaultVersion'])) {
									$response['message'] = "<b style='color: red;'> ". $browser['Name'] ." - " . $this->browserdetection->getVersion() . "  </b> not supported. ";
									$response['link'] = $browser['link'];
									break;
								}
							}
						}
						$this->load->library('user_agent');

						if ($this->agent->is_browser())
						{
							$agent = $this->agent->browser();
						}
						elseif ($this->agent->is_robot())
						{
							$agent = $this->agent->robot();
						}
						elseif ($this->agent->is_mobile())
						{
							$agent = $this->agent->mobile();
						}
						else
						{
							$agent = 'Unidentified User Agent';
						}
						//echo $agent;
						$data1['Browser'] = $this->browserdetection->getName();
						$data1['BrowserVersion'] = $this->browserdetection->getVersion();
						$data1['Platform']= $agent;
						//$data1['PlatformVersion']= 'test';
						$data1['PlatformVersion']=$this->browserdetection->is64bitPlatform();
						/*Browser Detection End*/
						$data1['ModuleName']='user-login';
						$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
						$data1['DateTime']=date('y-m-d H:i:s');
						$data1['TableName']='musers';
						$data1['UserUID']=$this->session->userdata('UserUID');                
						$this->Common_Model->Audittrail_insert($data1);
						/*END AUDITRAIL*/
					 if($this->session->userdata("PasscodeVerification")==1) // Check step 2 Verification
						{
							$res = $this->Authentication_Passcode();
							echo json_encode($res);exit;
						}
						else if($result['Firstlogin'] == 1)
						{
							$res = array("validation_error" => 1,'Redirect'=>'ChangePassword');
						    echo json_encode($res);exit;
						}else{
							if(!empty($theUrl))
							{

								$res = array("validation_error" => 1,'Redirect'=>'theUrl','URL'=>$theUrl,'message' => '');
							    echo json_encode($res);exit;
							}else{
								$Redirect = isset($result['DefaultScreen']) && !empty($result['DefaultScreen']) ? $result['DefaultScreen']  : 'Profile';

								if (!empty($result)) {
									$res = array("validation_error" => 1,'Redirect'=>$Redirect,'message' => '');								
								}
								else{
									$res = array("validation_error" => 1,'Redirect'=>'Dashboard','message' => '');
								}

							    echo json_encode($res);exit;

							}
						}
					

					} 
					else 
					{
						$res = array("validation_error" => 2,'message' => 'Invalid Username or Password');
						echo json_encode($res);exit;
					}
				}
				else
				{
					$res = array("validation_error" => 2,'message' => 'Invalid Username or Password');
					echo json_encode($res);exit;
				}
			}

		}
		else
		{ 
			$data = array(
				'validation_error' => 2,
				'message' =>'Please Fill The Required Fields',
				'Username' => form_error('Username'),
				'Password' => form_error('Password'),
			); 
			foreach($data as $key=>$value)
			{
				if(is_null($value) || $value == '')
					unset($data[$key]);
			}
			echo json_encode($data);exit;
		} 
	}

	function Authentication_Passcode()
		{
			$OTP_String = 'abcdefghijklmnopqrstuvwxyz*$#@()0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$otp = str_shuffle($OTP_String);
			$Passcode = substr($otp, 1, 6);
			$UserUID = $this->session->userdata('Temp_UserUID');
			$sendcode = $this->LoginModel->PasscodeStore($UserUID,$Passcode);
			if($sendcode==1)
			{
				$UserName = $this->session->userdata('UserName');

				$from_email = "notifications@direct2title.com";
				$to_email = $this->session->userdata('Email');
		        //Load email library
		        $this->load->library('email');
		        $this->email->from($from_email);
		        $this->email->to($to_email);
		        $this->email->subject('Passcode Verify');
				$this->email->message('<p>Dear <strong>'.$UserName.'</strong><br /><br />A One Time Password has been generated to login to doctrac and the same is <b>'.$Passcode.'</b><br /><br />Please use this One Time Password to complete the doctrac Login.&nbsp;<br /><br />Please Note:&nbsp;<br /><br />One Time Password will expire&nbsp;<span class="aBn" tabindex="0" data-term="goog_102548679"><span class="aQJ">in 30 minutes</span></span>. In such a case, user needs to login again.&nbsp;<br /><br />With Regards<br /><br />Customer Service - ISGN Solutions<br /><br />*** This is an auto-generated email. Please do not reply to this email.***&nbsp;</p>
					<p><br />For any queries, please contact&nbsp;<a href="mailto:customerservice@isgnsolutions.com" target="_blank" rel="noopener">customerservice@isgnsolutions.com</a></p>');

				if($this->email->send())
				{
					$this->session->unset_userdata('Email');
					$res = array("validation_error" => 1,'message' => 'Please Check your Mail','Redirect'=>'verifyotp');
				} else {
					$res = array("validation_error" => 4,'message' => 'Oops!... Mail Error');
				}
			} else {
				$res = array("validation_error" => 5,'message' => 'Oops!... Mail Error');
			}
			return $res;
		}

	function VerifyOtp()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('passcode', '', 'required');
		if($this->form_validation->run() == TRUE)
		{
			$user = $this->input->post('userid');
			$passcode = $this->input->post('passcode');
			$otp = $this->LoginModel->VerifyPasscode($user);
			if(!empty($otp))
			{
				if($otp->Passcode == $passcode)
				{
					$valid_date = $otp->Validity;
					$now = new DateTime();
					$ref = new DateTime($valid_date);
					$valid_time = $now->diff($ref);
					$expire = $valid_time->i;
						if($expire > 30) // chk 30 min valid passcode
						{
							$data = array('validation_error'=>1,'message'=>'Passcode has Expired','type'=>'danger');
						} else {
							$this->LoginModel->DeletePasscode($user);
							$this->session->set_userdata('VerifyedPasscode',1);
							$userid=$this->session->userdata('Temp_UserUID');
							$this->session->set_userdata('UserUID',$userid);
							
							$data = array('validation_error'=>0,'message'=>'Verified Successfully','type'=>'success');
						}
					} else {
						$data = array('validation_error'=>1,'message'=>'Invalid Passcode','type'=>'danger');
					}
				} else {
					$data = array('validation_error'=>1,'message'=>'Passcode Not Generated','type'=>'danger');
				}
			} else {
				$data = array(
					'validation_error' => 2,
					'message' =>'Please Enter your Passcode',
					'passcode' => form_error('passcode'),
					'type'=>'danger'
				);
				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')
						unset($data[$key]);
				}
			}
			echo json_encode($data);
		}

	function CheckLoginExist()
	{
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('loginid', 'LoginID/Email', 'required');
		$UserName='';
		if($this->form_validation->run() == TRUE)
		{
			$loginid = $this->input->post('loginid');  
			$result = $this->LoginModel->CheckLoginExist($loginid);
			if($result)
			{
				foreach($result as $data)
				{
					$Email = $data->EmailID;
					$UserName = $result->UserName;
				}
				$DynamicAccessCode = random_string('numeric', 8);
				//$UserName = $this->session->userdata('UserName');
				$this->LoginModel->SaveDynamicAccessCode($Email,$DynamicAccessCode);
				if($this->ForgetPasswordVerification($Email,$DynamicAccessCode,$UserName))
				{
					$res = array("validation_error" => 1,'message' => 'Check Your Mail To reset Your Password');
					echo json_encode($res);exit;
				}
			}
			else
			{
				$res = array("validation_error" => 0,'message' => 'LoginID Does Not Exist');
				echo json_encode($res); 
			}
		}
		else
		{
			$data = array(
				'validation_error' => 2,
				'message' =>'Please Fill The Required Fields',
				'loginid' => form_error('loginid'),
			);

			foreach($data as $key=>$value)
			{
				if(is_null($value) || $value == '')
					unset($data[$key]);
			}
			echo json_encode($data); 
		}	
	}

	private function ForgetPasswordVerification($Email,$DynamicAccessCode,$UserName)
	{
				//Load email library
		$this->load->library('email'); 

		// $config['protocol']    = 'smtp';
	 //    $config['smtp_host']    = 'ssl://smtp.gmail.com';
	 //    $config['smtp_port']    = '465';
	 //    $config['smtp_timeout'] = '7';
	 //    $config['smtp_user']    = 'madhuri.avanze@gmail.com';
	 //    $config['smtp_pass']    = 'avanze@123';
	 //    $config['charset']    = 'utf-8';
	 //    $config['newline']    = "\r\n";
	 //    $config['mailtype'] = 'html'; // or html
	 //    $config['validation'] = TRUE; // bool whether to validate email or not      

	 //    $this->email->initialize($config);
 		$from_email = "notifications@direct2title.com"; 
        $to_email = $Email; 
         //Load email library 
         $this->load->library('email'); 
   
         $this->email->from($from_email); 
         $this->email->to($to_email);
         $this->email->subject('Your Dynamic Access Code'); 
         $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> </head> <body> <div class="row" style="border:2px solid #ccc;width:750px;margin:0 auto;"> <div class="row" > <p style="background-color:#f5f3f3;color:#808080;text-align:center;line-height:50px;font-size:20px;margin:10px;"><img src="https://www.doctrac.direct2title.com/assets/img/doctrack_wit.png" alt="logo" class="logo-img" style="margin-top: 15px;margin-bottom: -10px;width: 21%;"></p></div><br/> <table style="max-width: 620px; margin: 0 auto;font-size:15px;line-height:22px;"> <tbody> <tr style="height: 23px;"> <td style="height: 23px;"><span style="font-weight: bold;">Hi '.$UserName.',</span></td></tr><tr style="height: 43px;"> <td style="height: 43px;">You recently requested to reset your password for your Doctrac Account- Click the button below to reset it.<strong> Your Access Code is '.$DynamicAccessCode.'.</strong></td></tr><tr style="height: 29px;"> <td style="text-align: center; height: 29px;"><a href="'.base_url().'Login/updatepasswordpage" style="background-color: red; color: #fff; display: inline-block; padding: 10px 10px 10px 10px; font-weight: bold; border-radius: 5px; text-align: center;font-size: 12px;text-decoration:none;border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 5px 10px 5px 10px;margin: 7px 0; text-decoration:none; display:inline-block; color: #FFFFFF;background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040));background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040);background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040);background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040);background-image: -o-linear-gradient(top, #ff9a9a, #ff4040);background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);" class="btn button_example"><span style="padding:10px,10px,10px,10px;">Reset Your Password</style></a></td></tr><tr style="height: 43px;"> <td style="height: 43px;">If you did not request a password reset. please ignore this email or reply to let us know. This password reset is only valid for the next 30 minutes.</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 23px;"> <td style="height: 23px;">Thanks,</td></tr><tr style="height: 23px;"> <td style="height: 23px;">Doctrac Team</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"><span style="font-weight: bold;">P.S.</span> We also love hearing from you and helping you with any issues you have. Please reply to this email if you want to ask a question or just say hi.</td></tr><tr style="height: 23px;"> <td style="height: 23px;border-bottom: 1px solid #ccc;"></td></tr><tr style="height: 23px; "> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"> If you are having trouble clicking the password reset button, copy and paste the URL below into your web browser and click forgot password link.</td></tr><tr style="height: 43px;"> <td style="height: 43px;font-size: 12px;text-decoration: underline;"> <a href="https://www.doctrac.direct2title.com"> https://www.doctrac.direct2title.com</a></td></tr></tbody> </table> <div class="row" style="margin:10px,10px,10px,10px;margin-left:10px;margin-right:10px;"> <p style="padding:10px,10px,10px,10px;background-color:#f5f3f3;color:#907f7f;text-align:center;line-height:50px"><strong> Doctrac Team. All Rights Reserved.</strong></p></div></div></body></html><style type="text/css">#main{max-width: 600px;margin: 0 auto;}.button_example{border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 10px 10px 10px 10px; text-decoration:none; display:inline-block; color: #FFFFFF;background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040));background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040);background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040);background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040);background-image: -o-linear-gradient(top, #ff9a9a, #ff4040);background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);}.button_example:hover{border:px solid #FFFFFF;background-color: #ff6767; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff6767), to(#ff0d0d));background-image: -webkit-linear-gradient(top, #ff6767, #ff0d0d);background-image: -moz-linear-gradient(top, #ff6767, #ff0d0d);background-image: -ms-linear-gradient(top, #ff6767, #ff0d0d);background-image: -o-linear-gradient(top, #ff6767, #ff0d0d);background-image: linear-gradient(to bottom, #ff6767, #ff0d0d);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff6767, endColorstr=#ff0d0d);}</style>'); 
   
         //Send mail 
         if($this->email->send()) 
         {
         	// echo 'Yes';
         	echo json_encode(array('validation_error'=>1,'message' => 'Check Your Mail To reset Your Password'));
         }
         else{
         	// echo 'No';
         	echo json_encode(array('validation_error'=>0,'message'=>'Check Your Mail To reset Your Password'));
         }
	}

	function UpdatePassword()
	{
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('accesscode', 'Access Code', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required');
		if($this->form_validation->run() == TRUE)
		{
			$accesscode = $this->input->post('accesscode');
			$result = $this->LoginModel->CheckAccessCode($accesscode);
			$user = $this->LoginModel->getUserDetails($accesscode);
			if($result)
			{
				$password = $this->input->post('password');
				$cpassword = md5($this->input->post('cpassword'));

				if ($accesscode=='') {
    				$accesscode = 0;
    			}
		    	
		    	$result = $this->LoginModel->getUserDetailsByAccesscode($accesscode);
		    	$data=0;
		    	if ($result) {
		    		foreach ($result as $r) {
		    			if ($r->Password == md5($password)) {
		    				$data=1;
		    			}
		    		}
		    	}
				if (strlen($password) > 8 && preg_match('/[0-9]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password) && $data==0)
				{

				if($this->input->post('password') == $this->input->post('cpassword'))
				{
					$result = $this->LoginModel->UpdatePassword($accesscode,$cpassword);
					$this->LoginModel->PasswordStore($user['UserUID'],$cpassword);
					if($result)
					{
						$res = array('validation_error' => 1,'message'=>'Password Updated Successfully');
					}
					else{
						$res = array('validation_error' => 0,'message'=>'Error');
					}
					
				}else{
					$res = array("validation_error" => 3,'message'=>'Confirm Password field does not Match');
				}
																	 
						} 
						else {
							$res = array("validation_error" => 3,'message'=>'Must be atleast eight characters contain upercase,lowercase and numeric,Last 5 password not allowed','password' => form_error('password'));
						}
						//echo json_encode($res);exit; 
			}
			else
			{
				$res = array('validation_error' => 3,'message'=>'Entered Access Code is Wrong');
			}
			echo json_encode($res);exit;
		}
		else{
			$data = array(
				'validation_error' => 2,
				'message' =>'Please Fill The Required Fields',
				'accesscode' => form_error('accesscode'),
				'password' => form_error('password'),
				'cpassword' => form_error('cpassword'),
			);

			foreach($data as $key=>$value)
			{
				if(is_null($value) || $value == '')
					unset($data[$key]);
			}
			echo json_encode($data); 
		}
	}

 function ChangeCurrentPassword()
  {
    $this->form_validation->set_error_delimiters('', '');
    $this->form_validation->set_rules('oldpassword', 'Old Password', 'required');
    $this->form_validation->set_rules('password', 'Password', 'required');
    $this->form_validation->set_rules('cpassword', 'Confirm Password', 'required');
    if($this->form_validation->run() == TRUE)
    {
     
      $UserUID = $this->input->post('UserUID');
      $oldpassword = $this->input->post('oldpassword');
      $firstlogin = $this->input->post('Firstlogin');

      $result = $this->LoginModel->CheckOldPassword($oldpassword,$UserUID);
      if($result)
      {
        $cpassword = md5($this->input->post('cpassword'));
        $password = $this->input->post('password');

        $results = $this->LoginModel->getUserDetailsByUserID($UserUID);
    	$data=0;
    	if ($results) {
    		foreach ($results as $r) {
    			if ($r->Password == md5($password)) {
    				$data=1;
    			}
    		}
    	}
        if (strlen($password) > 8 && preg_match('/[0-9]/', $password)
          && preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password) && $data==0)
          {
          		if($this->input->post('cpassword') == $this->input->post('password'))
          		{
		            $result = $this->LoginModel->ChangePassword($UserUID,$cpassword,$firstlogin);
		            $this->LoginModel->PasswordStore($UserUID,$cpassword);
		            if($result)
		            {
		              $res = array('validation_error' => 1,'message'=>'Password Changed Successfully');
		              $this->session->sess_destroy();
		              echo json_encode($res);exit;
		            }
		            else{
		              $res = array('validation_error' => 3,'message'=>'Error');
		              echo json_encode($res);exit;
		            }                           
          		}else{
          			$res = array("validation_error" => 3,'message'=>'Confirm Password field does not Match');
          		}
          } 
          else {
            $res = array("validation_error" => 3,'message'=>'Must be atleast eight characters contain upercase,lowercase and numeric,Last 5 password not allowed','password' => form_error('password'));
          }
      }
      else
      {
        $res = array('validation_error' => 3,'message'=>'Old Password is Incorrect','oldpassword' => form_error('oldpassword'));
      }
      echo json_encode($res);

    }
    else{

      $data = array(
        'validation_error' => 2,
        'message' =>'Please Fill The Required Fields',
        'oldpassword' => form_error('oldpassword'),
        'password' => form_error('password'),
        'cpassword' => form_error('cpassword'),
      );

      foreach($data as $key=>$value)
      {
        if(is_null($value) || $value == '')
          unset($data[$key]);
      }
      echo json_encode($data); 
    }

  }

  function BrowserDetection()
  {
		$library = $this->load->library('Browserdetection');
		
		$response['message'] = '';
		$response['link'] = 'javascript:void(0);';
		// echo '<pre>'; print_r($this->browserdetection); exit;
		foreach ($this->config->item('BROWSERS') as $key => $browser) {
			$browserversion = $this->browserdetection->getVersion();
			$strposition = stripos($browserversion, '.');
			$majorversion = substr($browserversion, 0, $strposition);
			$minorversions = substr($browserversion, $strposition);

			$minorversions = str_replace('.', '', $minorversions);

			$version = number_format($majorversion . '.' . $minorversions);

			// Check is Current Browser Version is Below defined.
			if ($this->browserdetection->getName() == $browser['Name']) {
				
				if (number_format($version) < number_format($browser['DefaultVersion'])) {
					$response['message'] = "<b style='color: red;'> ". $browser['Name'] ." - " . $this->browserdetection->getVersion() . "  </b> not supported. ";
					$response['link'] = $browser['link'];
					break;
				}
			}
		}
		$response['Browser'] = $this->browserdetection->getName();
		$response['BrowserVersion'] = $this->browserdetection->getVersion();
		return $response;
  }
  public function is64bitPlatform()
    {
        return $this->_is64bit;
    }


    public function checkForLastPassword(){
    	$accesscode=$this->input->post('accesscode');
    	if ($accesscode=='') {
    		$accesscode = 0;
    	}
    	$Password=$this->input->post('Password');
    	$result = $this->LoginModel->getUserDetailsByAccesscode($accesscode);
    	$data=0;
    	if ($result) {
    		foreach ($result as $r) {
    			if ($r->Password == md5($Password)) {
    				$data=1;
    			}
    		}
    	}
    	echo json_encode($data);
    }


    public function checkForPreviousPassword(){
    	$UserUID=$this->input->post('UserUID');
    	if ($UserUID=='') {
    		$UserUID = 0;
    	}
    	$Password=$this->input->post('Password');
    	$result = $this->LoginModel->getUserDetailsByUserID($UserUID);
    	$data=0;
    	if ($result) {
    		foreach ($result as $r) {
    			if ($r->Password == md5($Password)) {
    				$data=1;
    			}
    		}
    	}
    	echo json_encode($data);
    }

}
?>
