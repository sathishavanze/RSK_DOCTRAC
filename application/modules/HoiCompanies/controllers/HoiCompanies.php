<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class HoiCompanies extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('HoiCompanies_Model');
		$this->load->model('Common_Model');
		$this->load->model('Role/Role_Model');
		$this->load->library('form_validation');
		$this->load->config('keywords');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['HoiCompanies']=$this->HoiCompanies_Model->GetDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function AddCompany()
	{
		$data['content'] = 'addCompany';
		$data['InputDocTypes'] = $this->Common_Model->get('mInputDocType');
		$data['CustomerDetails']= $this->Role_Model->GetCustomerDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditCompany()
	{
		$CompanyUID = $this->uri->segment(3);
		$data['CompanyDetails'] = $this->Common_Model->get_row('mCompanyDetails', ['CompanyUID' => $CompanyUID]);
		$data['content'] = 'EditCompany';
		$data['InputDocTypes'] = $this->Common_Model->get('mInputDocType');
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveCompany()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('CompanyName', '', 'required');

			$this->form_validation->set_message('required', 'This Company Detail is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();
				$add = [];
				$add = [
						"CompanyName"=> $post['CompanyName'],
						"FolderName"=> $post['FolderName'],
						"Website"=> $post['Website'],
						"Email"=> $post['Email'],
						"FaxNo"=> $post['FaxNo'],
						"ContactNo"=> $post['ContactNo'],
						"MortgageOption"=> $post['MortgageOption'],
						"Username"=> $post['Username'],
						"Password"=> $post['Password'],
						"MortgageCode"=> $post['MortgageCode'],
						"LineOfBusiness"=> $post['LineOfBusiness'],
						"WhoPays"=> $post['WhoPays'],
						"Active"=> isset($post['Active']) ? '1' : '0',
					];

				if($this->HoiCompanies_Model->SaveDetails($add) )
				{
					$res = array('validation_error' => 0,'message'=>'Company Updated Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Company Product.");
					echo json_encode($res);exit();
				}
			}else{
				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'CompanyName' => form_error('CompanyName'),
					'ContactNo' => form_error('ContactNo'),
					'Email' => form_error('Email'),
					'FaxNo' => form_error('FaxNo'),
					'Website' => form_error('Website'),
				);
				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function UpdateCompany()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('CompanyName', '', 'required');

			$this->form_validation->set_message('required', 'This Company Detail is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();
				$add = [];
				$add = [
						"CompanyName"=> $post['CompanyName'],
						"FolderName"=> $post['FolderName'],
						"Website"=> $post['Website'],
						"Email"=> $post['Email'],
						"FaxNo"=> $post['FaxNo'],
						"ContactNo"=> $post['ContactNo'],
						"MortgageOption"=> $post['MortgageOption'],
						"Username"=> $post['Username'],
						"Password"=> $post['Password'],
						"MortgageCode"=> $post['MortgageCode'],
						"LineOfBusiness"=> $post['LineOfBusiness'],
						"WhoPays"=> $post['WhoPays'],
						"Active"=> ($post['Active'] == 'on') ? '1' : '0',
					];

				if($this->HoiCompanies_Model->UpdateDetails($add, $post['CompanyUID']) )
				{
					$res = array('validation_error' => 0,'message'=>'Company Updated Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Company Product.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'CompanyName' => form_error('CompanyName'),
					'ContactNo' => form_error('ContactNo'),
					'Email' => form_error('Email'),
					'FaxNo' => form_error('FaxNo'),
					'Website' => form_error('Website'),
				);


				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}


	}


} 

?>