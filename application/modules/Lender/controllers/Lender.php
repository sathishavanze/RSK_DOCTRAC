<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Lender extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Lender_model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['DocumentDetails'] = $this->Lender_model->GetDocument();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function Addlender()
	{
		$data['content'] = 'addlender';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function Editlender()
	{
		$data['DocumentDetails'] = $this->db->select("*")->from("mLender")->where(array('LenderUID'=>$this->uri->segment(3)))->get()->row();

		$data['content'] = 'updatelender';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	function getzip()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$zipcode = $this->input->post('zipcode');
			$details = $this->Lender_model->getzipcontents($zipcode);
			echo json_encode($details);

		}
	}

	function Savelender()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('LenderName', '', 'required');
			$this->form_validation->set_rules('AddressLine1', '', 'required');
			$this->form_validation->set_rules('ZipCode', '', 'required');
			$this->form_validation->set_rules('CityUID', '', 'required');
			$this->form_validation->set_rules('StateUID', '', 'required');
			
			if ($this->form_validation->run() == true) 
			{

				if($this->Lender_model->GetDocumentType($this->input->post()) == 1)
				{

						$res = array('Status' => 0,'message'=>'Lender added Successsfully');
						echo json_encode($res);exit();
					}
				else{
					$res = array('Status' => 2,'message'=>'Lender added Successsfully');
					echo json_encode($res);exit();
				}
			}
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'LenderName' => form_error('LenderName'),
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
		function Updatelender()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('LenderName', '', 'required');
			$this->form_validation->set_rules('AddressLine1', '', 'required');
			$this->form_validation->set_rules('ZipCode', '', 'required');
			$this->form_validation->set_rules('CityUID', '', 'required');
			$this->form_validation->set_rules('StateUID', '', 'required');
			

			if ($this->form_validation->run() == true) 
			{
                $LenderUID = $this->input->post('LenderUID');
				if($this->Lender_model->UpdateDocument($this->input->post()) == 1)
				{

						$res = array('Status' => 3,'message'=>'Lender Update Successsfully');
						echo json_encode($res);exit();
					}
				else{
					$res = array('Status' => 4,'message'=>'Lender Update Successsfully');
					echo json_encode($res);exit();
				}
			}
				else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'LenderName' => form_error('LenderName'),
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