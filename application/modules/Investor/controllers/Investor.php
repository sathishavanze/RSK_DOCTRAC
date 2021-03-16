<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Investor extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Investor_model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['InvestorList'] = $this->Investor_model->GetInvestors();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function AddInvestor()
	{
		$data['content'] = 'AddInvestor';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditInvestor($InvestorUID)
	{
		$data['InvestorDetails'] = $this->db->select("*")->from("mInvestors")->where(array('InvestorUID'=>$InvestorUID))->get()->row();

		$data['content'] = 'EditInvestor';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}

	function Saveinvestor()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('InvestorNo', '', 'required');
			$this->form_validation->set_rules('InvestorName', '', 'required');
			$this->form_validation->set_rules('AddressLine1', '', 'required');
			$this->form_validation->set_rules('ZipCode', '', 'required');
			$this->form_validation->set_rules('CityName', '', 'required');
			$this->form_validation->set_rules('StateName', '', 'required');
			
			if ($this->form_validation->run() == true) 
			{

				$Investor = $this->input->post();
				if($this->Investor_model->InsertInvestor($Investor))
				{

						$res = array('Status' => 0,'message'=>'Investor added Successsfully');
						echo json_encode($res);exit();
					}
				else{
					$res = array('Status' => 2,'message'=>'Investor added Successsfully');
					echo json_encode($res);exit();
				}
			}
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'InvestorNo' => form_error('InvestorNo'),
					'InvestorName' => form_error('InvestorName'),
					'AddressLine1' => form_error('AddressLine1'),
					'ZipCode' => form_error('ZipCode'),
					'CityName' => form_error('CityName'),
					'StateName' => form_error('StateName'),
					'type' => 'danger',
				);


				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
			
			}	
		}

		function UpdateInvestor()
		{
			ini_set("display_errors", 1);
			error_reporting(E_ALL);

			if ($this->input->server('REQUEST_METHOD') === 'POST') 
			{

			$this->form_validation->set_rules('InvestorNo', '', 'required');
			$this->form_validation->set_rules('InvestorName', '', 'required');
			$this->form_validation->set_rules('AddressLine1', '', 'required');
			$this->form_validation->set_rules('ZipCode', '', 'required');
			$this->form_validation->set_rules('CityName', '', 'required');
			$this->form_validation->set_rules('StateName', '', 'required');
			

			if ($this->form_validation->run() == true) 
			{
				$Investor = $this->input->post();
				if($this->Investor_model->UpdateInvestor($Investor))
				{

						$res = array('Status' => 3,'message'=>'Investor Update Successsfully');
						echo json_encode($res);exit();
					}
				else{
					$res = array('Status' => 4,'message'=>'Investor Update Successsfully');
					echo json_encode($res);exit();
				}
			}
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'InvestorNo' => form_error('InvestorNo'),
					'InvestorName' => form_error('InvestorName'),
					'AddressLine1' => form_error('AddressLine1'),
					'ZipCode' => form_error('ZipCode'),
					'CityName' => form_error('CityName'),
					'StateName' => form_error('StateName'),
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