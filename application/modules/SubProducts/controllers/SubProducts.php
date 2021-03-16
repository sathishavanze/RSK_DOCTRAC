<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class SubProducts extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('SubProducts_Model');
		$this->load->library('form_validation');
		$this->load->config('keywords');
	}

	public function index()
	{

		$data['content'] = 'index';
		$data['SubProducts']=$this->SubProducts_Model->GetSubProducts();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function AddSubProduct()
	{
		$data['content'] = 'addSubProduct';
		$data['Products'] = $this->Common_Model->get('mProducts', ['Active'=>1]);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditSubProduct()
	{
		$SubProductUID = $this->uri->segment(3);
		$data['SubProductDetails'] = $this->SubProducts_Model->GetSubProduct_ByUID($SubProductUID);
		$data['Products'] = $this->Common_Model->get('mProducts', ['Active'=>1]);
		$data['content'] = 'EditSubProduct';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveSubProduct()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('SubProductName', '', 'required');
			$this->form_validation->set_rules('SubProductCode', '', 'required');
			$this->form_validation->set_rules('ProductUID', '', 'required');
			$this->form_validation->set_message('required', 'This SubProduct is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();
				$addSubProduct = [];
				$addSubProduct = [
								"SubProductName"=> $post['SubProductName'],
								"SubProductCode"=> $post['SubProductCode'],
								"ProductUID"=> $post['ProductUID'],
								"CreatedBy"=> $this->loggedid,
								"Active"=> 1,
							];
				if($this->SubProducts_Model->Save('mSubProducts', $addSubProduct))
				{
					$res = array('validation_error' => 0,'message'=>'SubProduct added Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Add SubProduct.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'SubProductName' => form_error('SubProductName'),
					'SubProductCode' => form_error('SubProductCode'),
					'ProductUID' => form_error('ProductUID'),
				);


				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function UpdateSubProduct()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('SubProductName', '', 'required');
			$this->form_validation->set_rules('SubProductCode', '', 'required');
			$this->form_validation->set_rules('ProductUID', '', 'required');
			$this->form_validation->set_message('required', 'This SubProduct is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();

				$mSubProducts = $this->Common_Model->get_row('mSubProducts', ['SubProductUID'=>$post['SubProductUID']]);
				if (empty($mSubProducts)) {
					$res = array('validation_error' => 1, 'message'=>"Invalid SubProduct to Update.");
					echo json_encode($res);exit();
				}

				$addSubProduct = [];
				$addSubProduct = [
								"SubProductName"=> $post['SubProductName'],
								"SubProductCode"=> $post['SubProductCode'],
								"ProductUID"=> $post['ProductUID'],
								"ModifiedOn"=> date('Y-m-d H:i:s'),
								"ModifiedBy"=> $this->loggedid,
								"Active"=> isset($post['Active']) ? 1 : 0,
							];
				if($this->SubProducts_Model->Save('mSubProducts', $addSubProduct, ['SubProductUID'=>$post['SubProductUID']]))
				{
					$res = array('validation_error' => 0,'message'=>'SubProduct Updated Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Update SubProduct.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'SubProductName' => form_error('SubProductName'),
					'SubProductCode' => form_error('SubProductCode'),
					'ProductUID' => form_error('ProductUID'),
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