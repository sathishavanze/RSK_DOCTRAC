<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class User extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['UsersDetails'] = $this->User_Model->GetUsersDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function AddUser()
	{
		$data['content'] = 'adduser';
		$data['getroles'] = $this->Common_Model->GetRoles();
		$data['Customer'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditUser($UserUID)
	{
		$data['UpdateUsersDetails'] = $this->db->select("*")->from("mUsers")->where(array('UserUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'updateuser';
		$data['Customer'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);
		$data['getroles'] = $this->Common_Model->GetRoles($UserType);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function SaveUser()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('username', '', 'required');
			$this->form_validation->set_rules('loginid', '', 'required|valid_email');
			$this->form_validation->set_rules('password', '', 'required');
			$this->form_validation->set_rules('emailid', '', 'required|valid_email');
			$this->form_validation->set_rules('RoleUID', '', 'required');
			$this->form_validation->set_rules('UserLocation', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			
			$post = $this->input->post();

			$post['PasscodeVerify']=isset($post['PasscodeVerify']) ? 1 : 0;
			$post['Active']=1;
			if ($this->form_validation->run() == true) 
			{
				$result=$this->User_Model->Useradding($post);
				$this->sendVerificationEmail($post['emailid'],$result);
				$this->User_Model->PasswordStore($result,$post['password']);
				if( $result!= 0)
				{
					
					$res = array('Status' => 0,'message'=>'User added Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'User added Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'username' => form_error('username'),
					'loginid' => form_error('loginid'),
					'password' => form_error('password'),
					'emailid' => form_error('emailid'),
					'RoleUID' => form_error('RoleUID'),
					'UserLocation' => form_error('UserLocation'),
					'type' => 'danger',
				);

			/*	echo '<pre>';print_r($data);exit;*/

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				//$res = array('Status' => 4,'detailes'=>$data);
				echo json_encode($data);
			}
			

		}

	}

	function SaveUpdate()
	{
		//echo '<pre>';print_r($_POST);exit;
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->CI =& $this;
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('username', '', 'required');
			$this->form_validation->set_rules('loginid', '', 'required|valid_email');
			$this->form_validation->set_rules('emailid', '', 'required|valid_email');
			$this->form_validation->set_rules('RoleUID', '', 'required');
			$this->form_validation->set_rules('UserLocation', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			
			$post = $this->input->post();

			$post['PasscodeVerify']=isset($post['PasscodeVerify']) ? 1 : 0;
			$post['Active']=isset($post['Active']) ? 1 : 0;
			$this->UserUID = $post['UserUID'];
  			//echo '<pre>';print_r($data);exit;
			if ($this->form_validation->run() == true) 
			{
				$result=$this->User_Model->UserDetailsUpdate($post);
				//$this->sendVerificationEmail($post['emailid'],$post['UserUID']);
				if( $result== 1)
				{
					$res = array('Status' => 2,'message'=>'User updated Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 2,'message'=>'User updated Successsfully');
					echo json_encode($res);exit();
				}
			}
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'username' => form_error('username'),
					'loginid' => form_error('loginid'),
					'emailid' => form_error('emailid'),
					'RoleUID' => form_error('RoleUID'),
					'RoleUID' => form_error('UserLocation'),
					'type' => 'danger',
				);

			/*	echo '<pre>';print_r($data);exit;*/

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				//$res = array('Status' => 4,'detailes'=>$data);
				echo json_encode($data);
			}
			

			

		}

	}
	public function checkloginid($LoginID){
		$UserUID = $this->UserUID;
		$query = $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID != '$UserUID'"); 
		// echo $query->num_rows();
		if($query->num_rows() > 0)
		{
			$this->form_validation->set_message('checkloginid', 'Login ID Already Taken');
			return false;
			
		}
		else
		{
			return true;
		}
	}
	function SideBarColorChanges(){
		$new_color = $this->input->post('new_color');
		$Type = $this->input->post('Type');
		$new_image = $this->input->post('new_image');
		$QueryResult = $this->User_Model->SideBarColorChanges($post);
		if ($QueryResult == 1) {
			return 1;
		}
		else{
			return 0;
		}

	}


function sendVerificationEmail($email,$UserUID){
  
// $this->load->library('email'); 

// 		$config['protocol']    = 'smtp';
// 	    $config['smtp_host']    = 'ssl://smtp.gmail.com';
// 	    $config['smtp_port']    = '465';
// 	    $config['smtp_timeout'] = '7';
// 	    $config['smtp_user']    = 'madhuri.avanze@gmail.com';
// 	    $config['smtp_pass']    = 'avanze@123';
// 	    $config['charset']    = 'utf-8';
// 	    $config['newline']    = "\r\n";
// 	    $config['mailtype'] = 'html'; // or html
// 	    $config['validation'] = TRUE; // bool whether to validate email or not      

// 	    $this->email->initialize($config);
 		$from_email = "notifications@direct2title.com"; 
        $to_email = $email; 
        
		  $this->email->from($from_email, "Admin Team");
		  $this->email->to($email);  
		  $this->email->subject("Email Verification");
		  $this->email->message("Dear User,\r\nPlease click on below URL or paste into your browser to verify your Email Address ".base_url()."User/verify/".$UserUID."\r\n"."\r\n\r\nThanks\r\nAdmin Team");


		  $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> </head> <body> <div class="row" style="border:2px solid #ccc;width:750px;margin:0 auto;"> <div class="row" > <p style="background-color:#f5f3f3;color:#808080;text-align:center;line-height:50px;font-size:20px;margin:10px;"><img src="https://www.doctrac.direct2title.com/assets/img/doctrack_wit.png" alt="logo" class="logo-img" style="margin-top: 15px;margin-bottom: -10px;width: 21%;"></p></div><br/> <table style="max-width: 620px; margin: 0 auto;font-size:15px;line-height:22px;"> <tbody> <tr style="height: 23px;"> <td style="height: 23px;"><span style="font-weight: bold;">Dear User,</span></td></tr><tr style="height: 43px;"> <td style="height: 43px;">You recently requested to reset your password for your Doctrac Account- Click the button below to reset it.<strong> Please click on below URL or paste into your browser to verify your Email Address '.base_url().'User/verify/'.$UserUID.'</strong></td></tr><tr style="height: 29px;"> <td style="text-align: center; height: 29px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;">If you did not request a password reset. please ignore this email or reply to let us know. This password reset is only valid for the next 30 minutes.</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 23px;"> <td style="height: 23px;">Thanks,</td></tr><tr style="height: 23px;"> <td style="height: 23px;">Doctrac Team</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"><span style="font-weight: bold;">P.S.</span> We also love hearing from you and helping you with any issues you have. Please reply to this email if you want to ask a question or just say hi.</td></tr><tr style="height: 23px;"> <td style="height: 23px;border-bottom: 1px solid #ccc;"></td></tr><tr style="height: 23px; "> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"> If you are having trouble clicking the password reset button, copy and paste the URL below into your web browser and click forgot password link.</td></tr><tr style="height: 43px;"> <td style="height: 43px;font-size: 12px;text-decoration: underline;"> <a href="https://www.doctrac.direct2title.com"> https://www.doctrac.direct2title.com</a></td></tr></tbody> </table> <div class="row" style="margin:10px,10px,10px,10px;margin-left:10px;margin-right:10px;"> <p style="padding:10px,10px,10px,10px;background-color:#f5f3f3;color:#907f7f;text-align:center;line-height:50px"><strong> Doctrac Team. All Rights Reserved.</strong></p></div></div></body></html><style type="text/css">#main{max-width: 600px;margin: 0 auto;}.button_example{border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 10px 10px 10px 10px; text-decoration:none; display:inline-block; color: #FFFFFF;background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040));background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040);background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040);background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040);background-image: -o-linear-gradient(top, #ff9a9a, #ff4040);background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);}.button_example:hover{border:px solid #FFFFFF;background-color: #ff6767; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff6767), to(#ff0d0d));background-image: -webkit-linear-gradient(top, #ff6767, #ff0d0d);background-image: -moz-linear-gradient(top, #ff6767, #ff0d0d);background-image: -ms-linear-gradient(top, #ff6767, #ff0d0d);background-image: -o-linear-gradient(top, #ff6767, #ff0d0d);background-image: linear-gradient(to bottom, #ff6767, #ff0d0d);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff6767, endColorstr=#ff0d0d);}</style>'); 
		  $this->email->send();
 }


 public function verify($verificationText=NULL){  
 	
	  $noRecords = $this->User_Model->verifyEmailAddress($verificationText);  
	  if ($noRecords > 0){
	   $error = array( 'message' => "Email Verified Successfully!"); 
	  }else{
	   $error = array( 'message' => "Verification Link Expired!"); 
	  }
	  $data['errormsg'] = $error; 

	 $this->load->view('verify', $data);
 }


function sendVerificationEmailLink(){
  $email=$this->input->post('email');
  $UserUID=$this->input->post('UserUID');
// $this->load->library('email'); 

// 		$config['protocol']    = 'smtp';
// 	    $config['smtp_host']    = 'ssl://smtp.gmail.com';
// 	    $config['smtp_port']    = '465';
// 	    $config['smtp_timeout'] = '7';
// 	    $config['smtp_user']    = 'madhuri.avanze@gmail.com';
// 	    $config['smtp_pass']    = 'avanze@123';
// 	    $config['charset']    = 'utf-8';
// 	    $config['newline']    = "\r\n";
// 	    $config['mailtype'] = 'html'; // or html
// 	    $config['validation'] = TRUE; // bool whether to validate email or not      

// 	    $this->email->initialize($config);
 		$from_email = "notifications@direct2title.com"; 
        $to_email = $email; 
        
		  $this->email->from($from_email, "Admin Team");
		  $this->email->to($email);  
		  $this->email->subject("Email Verification");
		  $this->email->message("Dear User,\r\nPlease click on below URL or paste into your browser to verify your Email Address ".base_url()."User/verify/".$UserUID."\r\n"."\r\n\r\nThanks\r\nAdmin Team");


		  $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> </head> <body> <div class="row" style="border:2px solid #ccc;width:750px;margin:0 auto;"> <div class="row" > <p style="background-color:#f5f3f3;color:#808080;text-align:center;line-height:50px;font-size:20px;margin:10px;"><img src="https://www.doctrac.direct2title.com/assets/img/doctrack_wit.png" alt="logo" class="logo-img" style="margin-top: 15px;margin-bottom: -10px;width: 21%;"></p></div><br/> <table style="max-width: 620px; margin: 0 auto;font-size:15px;line-height:22px;"> <tbody> <tr style="height: 23px;"> <td style="height: 23px;"><span style="font-weight: bold;">Dear User,</span></td></tr><tr style="height: 43px;"> <td style="height: 43px;">You recently requested to reset your password for your Doctrac Account- Click the button below to reset it.<strong> Please click on below URL or paste into your browser to verify your Email Address '.base_url().'User/verify/'.$UserUID.'</strong></td></tr><tr style="height: 29px;"> <td style="text-align: center; height: 29px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;">If you did not request a password reset. please ignore this email or reply to let us know. This password reset is only valid for the next 30 minutes.</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 23px;"> <td style="height: 23px;">Thanks,</td></tr><tr style="height: 23px;"> <td style="height: 23px;">Doctrac Team</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"><span style="font-weight: bold;">P.S.</span> We also love hearing from you and helping you with any issues you have. Please reply to this email if you want to ask a question or just say hi.</td></tr><tr style="height: 23px;"> <td style="height: 23px;border-bottom: 1px solid #ccc;"></td></tr><tr style="height: 23px; "> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"> If you are having trouble clicking the password reset button, copy and paste the URL below into your web browser and click forgot password link.</td></tr><tr style="height: 43px;"> <td style="height: 43px;font-size: 12px;text-decoration: underline;"> <a href="https://www.doctrac.direct2title.com"> https://www.doctrac.direct2title.com</a></td></tr></tbody> </table> <div class="row" style="margin:10px,10px,10px,10px;margin-left:10px;margin-right:10px;"> <p style="padding:10px,10px,10px,10px;background-color:#f5f3f3;color:#907f7f;text-align:center;line-height:50px"><strong> Doctrac Team. All Rights Reserved.</strong></p></div></div></body></html><style type="text/css">#main{max-width: 600px;margin: 0 auto;}.button_example{border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 10px 10px 10px 10px; text-decoration:none; display:inline-block; color: #FFFFFF;background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040));background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040);background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040);background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040);background-image: -o-linear-gradient(top, #ff9a9a, #ff4040);background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);}.button_example:hover{border:px solid #FFFFFF;background-color: #ff6767; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff6767), to(#ff0d0d));background-image: -webkit-linear-gradient(top, #ff6767, #ff0d0d);background-image: -moz-linear-gradient(top, #ff6767, #ff0d0d);background-image: -ms-linear-gradient(top, #ff6767, #ff0d0d);background-image: -o-linear-gradient(top, #ff6767, #ff0d0d);background-image: linear-gradient(to bottom, #ff6767, #ff0d0d);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff6767, endColorstr=#ff0d0d);}</style>'); 
		 //Send mail 
         if($this->email->send()) 
         {
         	// echo 'Yes';
         	echo json_encode(array('validation_error'=>1,'message' => 'Verification Link Sent Successsfully'));
         }
         else{
         	// echo 'No';
         	echo json_encode(array('validation_error'=>0,'message'=>'Verification Link Not Sent.Please Try Again'));
         }
 	}


} 

?>
