<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SFTP extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('SFTP_Model');
		$this->load->library('form_validation');
		$this->load->library('ftp');
	}
	public function index()
	{
		
		$data['content'] = 'index';
		$data['SFTPDetails'] = $this->SFTP_Model->GetDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function TestSFTP()
	{
		if($this->input->server("REQUEST_METHOD")=='POST')
		{
			//echo "pre";print_r($_POST);exit();
			$config['hostname'] = $this->input->post('SFTPHost');
			$config['username'] = $this->input->post('SFTPUser');
			$config['password'] = $this->input->post('SFTPPassword');
			if (!empty($this->input->post('SFTPPort'))) {
				$config['port'] = $this->input->post('SFTPPort');
			
			}
			$config['debug'] = true;
			if($this->input->post('SFTPProtocol') == 'SFTP')
			{
				require_once(APPPATH.'modules/SFTP/controllers/ConnectSFTP.php'); 
    			$exp =  new ConnectSFTP();
    			if($exp->connect($config))
    			{
    				echo "1";
    			}
    			else
				{
					echo "error";
				}
			}
			else
			{
				if($this->ftp->connect($config))
				{
				
					echo "1";
					$this->ftp->close();
				}
				else
				{
					echo "error";
				}

			}
		}
		

	}
	
	function AddSFTP()
	{
		$data['content'] = 'addsftp';
		/*$data['Roles'] = $this->Common_Model->GetCategory();*/
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditSFTP()
	{
		$data['DocumentDetails'] = $this->db->select("*")->from("mSFTP")->where(array('SFTPUID'=>$this->uri->segment(3)))->get()->row();

		$data['content'] = 'updatesftp';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	
	function SaveSFTP()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('SFTPName', '', 'required');
			$this->form_validation->set_rules('SFTPProtocol', '', 'required');
			$this->form_validation->set_rules('SFTPHost', '', 'required');
			$this->form_validation->set_rules('SFTPPassword', '', 'required');
	        $this->form_validation->set_rules('SFTPUser', '', 'required');
	        $this->form_validation->set_rules('EmailTemplateUID','','required');
	


			if ($this->form_validation->run() == true) 
			{

				if($this->SFTP_Model->GetSFTP($this->input->post()) == 1)
				{
						$res = array('Status' => 2,'message'=>'add Successsfully');
						echo json_encode($res);exit();
					}
					else
					{
						$res = array('Status' => 3,'message'=>'Failed to Add');
						echo json_encode($res);exit();
					}
			}
				
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'SFTPName' => form_error('SFTPName'),
					'SFTPProtocol' => form_error('SFTPProtocol'),
					'SFTPHost' => form_error('SFTPHost'),
					'SFTPPassword' => form_error('SFTPPassword'),
					'SFTPUser' => form_error('SFTPUser'),
					'EmailTemplateUID'=>form_error('EmailTemplateUID'),
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
	function UpdateSFTP(){
		//print_r($_POST);exit();
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('SFTPName', '', 'required');
			$this->form_validation->set_rules('SFTPProtocol', '', 'required');
			$this->form_validation->set_rules('SFTPHost', '', 'required');
			$this->form_validation->set_rules('SFTPPassword', '', 'required');
			$this->form_validation->set_rules('SFTPUser', '', 'required');
		$this->form_validation->set_rules('EmailTemplateUID','','required');
		



			if ($this->form_validation->run() == true) 
			{

				if($this->SFTP_Model->UpdateSFTP($this->input->post()) == 1)
				{
						$res = array('Status' => 2,'message'=>'UPdate Successsfully');
						echo json_encode($res);exit();
					}
					else
					{
						$res = array('Status' => 3,'message'=>'Failed to Update');
						echo json_encode($res);exit();
					}
			}
				
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'SFTPName' => form_error('SFTPName'),
					'SFTPProtocol' => form_error('SFTPProtocol'),
					'SFTPHost' => form_error('SFTPHost'),
					'SFTPPassword' => form_error('SFTPPassword'),
					'SFTPUser' => form_error('SFTPUser'),
					'EmailTemplateUID'=>form_error('EmailTemplateUID'),
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




} 

?>
