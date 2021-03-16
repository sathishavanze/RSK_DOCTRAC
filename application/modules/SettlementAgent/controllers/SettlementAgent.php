<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SettlementAgent extends MY_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('SettlementAgent_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{		
		$data['content'] = 'index';
		$data['DocumentDetails'] = $this->SettlementAgent_Model->GetSettlementAgents();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	public function AddSettlementAgent()
	{		
		$data['content'] = 'AddSettlementAgent';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function getzip()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$zipcode = $this->input->post('zipcode');
			$details = $this->SettlementAgent_Model->getzipcontents($zipcode);
			echo json_encode($details);
		}
	}

	function  EditSettlementAgent(){
		$data['DocumentDetails'] = $this->db->select("*")->from("mSettlementAgent")->where(array('SettlementAgentUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'EditSettlementAgent';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	
	function SaveSettlementAgent(){
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('SettlementAgentName', '', 'required');
			$this->form_validation->set_rules('AddressLine1', '', 'required');
			$this->form_validation->set_rules('ZipCode', '', 'required');
			$this->form_validation->set_rules('CityName', '', 'required');
			$this->form_validation->set_rules('StateName', '', 'required');			
			if ($this->form_validation->run() == true) 
			{
				if($this->SettlementAgent_Model->SaveSettlementAgent($this->input->post()) == 1)
				{

					$res = array('Status' => 0,'message'=>'Settlement Agent  added Successsfully');
					echo json_encode($res);exit();
				}
				else{
					$res = array('Status' => 2,'message'=>'Settlement Agent  added Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{
				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'SettlementAgentName' => form_error('SettlementAgentName'),
					'AddressLine1' => form_error('AddressLine1'),
					'ZipCode' => form_error('ZipCode'),
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


	function UpdateSettlement(){
	if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

		$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('SettlementAgentName', '', 'required');
			$this->form_validation->set_rules('AddressLine1', '', 'required');
			$this->form_validation->set_rules('ZipCode', '', 'required');
			$this->form_validation->set_rules('CityName', '', 'required');
			$this->form_validation->set_rules('StateName', '', 'required');		
			

			if ($this->form_validation->run() == true) 
			{
                $SettlementAgentUID = $this->input->post('SettlementAgentUID');
				if($this->SettlementAgent_Model->UpdateSettlementAgent($this->input->post()) == 1)
				{

						$res = array('Status' => 0,'message'=>'Settlement Agent Update Successsfully');
						echo json_encode($res);exit();
					}
				else{
					$res = array('Status' => 2,'message'=>'Settlement Agent Update Successsfully');
					echo json_encode($res);exit();
				}
			}
				else{


				$Msg = $this->lang->line('Empty_Validation');


			$data = array(
					'Status' => 1,
					'message' => $Msg,
					'SettlementAgentName' => form_error('SettlementAgentName'),
					'AddressLine1' => form_error('AddressLine1'),
					'ZipCode' => form_error('ZipCode'),
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