<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Products extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Products_Model');
		$this->load->model('Role/Role_Model');
		$this->load->library('form_validation');
		$this->load->config('keywords');
	}

	public function index()
	{

		$data['content'] = 'index';
		$data['Products']=$this->Products_Model->GetProductDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function AddProduct()
	{
		$data['content'] = 'addProduct';
		$data['InputDocTypes'] = $this->Common_Model->get('mInputDocType');
		$data['Rules'] = $this->Products_Model->getRules();
		$data['CustomerDetails']= $this->Role_Model->GetCustomerDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditProduct()
	{
		$ProductUID = $this->uri->segment(3);
		$data['ProductDetails'] = $this->db->select("*")->from("mProducts")->where(array('ProductUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'EditProduct';
		$data['InputDocTypes'] = $this->Common_Model->get('mInputDocType');
		$ProductDocType = $this->Common_Model->get('mProductDocType', ['ProductUID'=>$ProductUID]);

		$data['ProductRules'] = $this->Products_Model->getProductRules($ProductUID);
		$Rules = [];
		foreach ($data['ProductRules'] as $key => $value) {
			$Rules[] = $value->RuleUID;
		}
		$data['Rules'] = $this->Products_Model->getRules($Rules);
		foreach ($ProductDocType as $key => $value) {
			$data['SelectedDocTypes'][] = $value->InputDocTypeUID;
		}

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveProduct()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ProductName', '', 'required');
			$this->form_validation->set_rules('ProductCode', '', 'required');
			$this->form_validation->set_rules('productdoctype[0]', '', 'required');
			$this->form_validation->set_message('required', 'This Product is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();
				$productdoctype = $this->input->post('productdoctype');
				$CustomerUID = $this->input->post('CustomerUID');
				$addProduct = [];
				$addProduct = [
								"ProductName"=> $post['ProductName'],
								"ProductCode"=> $post['ProductCode'],
								"IsOCREnabled"=> isset($post['OcrEnable']) ? 1 : 0,
								"CreatedBy"=> $this->loggedid,
								"Active"=> 1,
							];
				if (isset($CustomerUID) && !empty($CustomerUID)) {
					$addProduct['CustomerUID'] = $CustomerUID;
				} else {
					$addProduct['CustomerUID'] = $this->parameters['DefaultClientUID'];
				}
				$ProductRules = $this->input->post('Rules');

				if($this->Products_Model->SaveProductDetails($addProduct, $productdoctype, $ProductRules) )
				{
					$res = array('validation_error' => 0,'message'=>'Product added Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Add Product.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'ProductName' => form_error('ProductName'),
					'ProductCode' => form_error('ProductCode'),
					'productdoctype' => form_error('productdoctype[0]'),
				);


				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function UpdateProduct()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ProductName', '', 'required');
			$this->form_validation->set_rules('ProductCode', '', 'required');
			$this->form_validation->set_rules('productdoctype[0]', '', 'required');

			$this->form_validation->set_message('required', 'This Product is required');
			if ($this->form_validation->run() == true) 
			{
				$post = $this->input->post();
				$productdoctype = $this->input->post('productdoctype');

				$mProducts = $this->Common_Model->get_row('mProducts', ['ProductUID'=>$post['ProductUID']]);
				if (empty($mProducts)) {
					$res = array('validation_error' => 1, 'message'=>"Invalid Product to Update.");
					echo json_encode($res);exit();
				}

				$addProduct = [];
				$addProduct = [
								"ProductName"=> $post['ProductName'],
								"ProductCode"=> $post['ProductCode'],
								"ModifiedOn"=> date('Y-m-d H:i:s'),
								"IsOCREnabled"=> isset($post['OcrEnable']) ? 1 : 0,
								"ModifiedBy"=> $this->loggedid,
								"Active"=> isset($post['Active']) ? 1 : 0,
							];

				$ProductRules = $this->input->post('Rules');
				if($this->Products_Model->UpdateProductDetails($addProduct, $productdoctype, $ProductRules, $post['ProductUID']) )
				{
					$res = array('validation_error' => 0,'message'=>'Product Updated Successsfully');
					echo json_encode($res);exit();
				}else{
					$res = array('validation_error' => 1, 'message'=>"Unable to Update Product.");
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'ProductName' => form_error('ProductName'),
					'ProductCode' => form_error('ProductCode'),
					'productdoctype' => form_error('productdoctype[0]'),

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