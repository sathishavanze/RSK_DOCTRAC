<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends MY_Controller 
{
	function __construct(){
		parent::__construct();
		$this->loggedid = $this->session->userdata('UserUID');
		$this->RoleUID = $this->session->userdata('RoleUID'); 
		$this->UserName = $this->session->userdata('UserName');
		$this->load->model('Customer_Model');
	}

	/*Listing All Customers */
	public function index()
	{	

		$data['content'] = 'index';
		$data['CustomerDetails']= $this->Customer_Model->GetCustomerDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function LoadcustomerInfo(){
		/*customer Info tab*/
		$CustomerUID = $this->input->post('CustomerUID');
		$data['Customers'] = $this->db->select("*")->from("mCustomer")->where(array('CustomerUID'=>$CustomerUID))->get()->row();

		$this->load->view('customerinfo',$data);
	}

	function LoadcustomerProduct(){
		/*customer Product tab*/
		$CustomerUID = $this->input->post('CustomerUID');
		$data['Customers'] = $this->Customer_Model->get('mCustomer', ['CustomerUID'=>$CustomerUID]);
		$data['Products'] = $this->Customer_Model->GetProductDetails();
		// $data['SubProducts'] = $this->Common_Model->get('mSubProducts', ['Active'=>1]);
		$data['JSON_WorkflowModules'] = json_encode($this->Common_Model->get('mWorkFlowModules', ['Active'=>1]));
		$Products = $data['Products'];
		$Product_Array = [];


		// foreach ($data['Products'] as $key => $value) {
		// 	$SubProducts = $this->Common_Model->get('mSubProducts', ['Active'=>1, 'ProductUID'=>$value->ProductUID]);
		// 	$data['Products'][$key]->SubProducts = $SubProducts;

		// }

		$data['JSON_Products'] = json_encode($data['Products']);
		$Prod_SubProd = $this->Customer_Model->Get_Customer_SubProduct_ById_Prod($CustomerUID);
		// print_r($Prod_SubProd);exit;
		foreach ($Prod_SubProd as $key => $value) {
			// $Prod_SubProd[$key]['Customer_Subproducts'] = $this->Customer_Model->get_customer_subproduct_details($CustomerUID,$value['ProductUID']);
			$Prod_SubProd[$key]['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_product_details($CustomerUID,$value['ProductUID']);			
		}

		$data['Prod_SubProd'] = $Prod_SubProd;
		// print_r($data['Prod_SubProd']);exit;
		$data['WorkflowDetaiils'] = $this->Common_Model->GetWorkflowDetaiils($CustomerUID);

		// echo "<pre>";
		// print_r($data);

		$this->load->view('customerproduct',$data);
		/*END OF Products TAB*/
	}
 
 	function LoadcustomerDependentWorkflow()
 	{
		$CustomerUID = $this->input->post('CustomerUID');
		$data['Customers'] = $this->Customer_Model->get('mCustomer', ['CustomerUID'=>$CustomerUID]);
		$data['Status'] = $this->Customer_Model->get('mStatus', ['Active'=>STATUS_ONE]);
		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details($CustomerUID);
		foreach ($data['Customer_Workflow'] as $key => $value) 
		{
			$test = $this->Customer_Model->get_customer_dependent_workflow_details($CustomerUID,$value['WorkflowModuleUID']);
			$data['Customer_Workflow'][$key]['DependentWorkflow'] = $this->Customer_Model->get_customer_dependent_workflow_details($CustomerUID,$value['WorkflowModuleUID']);
		}
		$data['WorkflowDetails'] = $this->Common_Model->GetcustomerWorkflowDetails($CustomerUID);
		//Customer workflow status details
		$data['mStatusDetails'] = $this->Common_Model->mStatusDetails();

		//Get category details
		$data['CategoryDetaiils'] = $this->Common_Model->GetCategory();

		//Get milestone details
		$data['MilestoneDetaiils'] = $this->Customer_Model->GetMilestoneDetaiils();

		//Get State
		$data['GetStateDetails'] = $this->Common_Model->GetStateDetails();
		//Get Loan Type
		$data['GetLoanTypeDetails'] = $this->Common_Model->GetLoanTypeDetails($CustomerUID);
		//Get Milestone
		$data['GetMilestoneDetails'] = $this->Common_Model->GetMilestoneDetails($CustomerUID);

		$this->load->view('customerworkflowdependence',$data);
	}

	function LoadcustomerMilestone() {
		$CustomerUID = $this->input->post('CustomerUID');

		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details($CustomerUID);

		$data['WorkflowDetails'] = $this->Common_Model->GetWorkflowDetaiils($CustomerUID);

		//Get Milestone
		$data['MilestoneDetaiils'] = $this->Common_Model->GetMilestoneDetails($CustomerUID);

		$product_details = $this->Customer_Model->GetCustomerProduct($CustomerUID);
		$ProductUID = $product_details['ProductUID'];

		$data['milestonematricsdetails'] = $this->Customer_Model->milestonematricsdetails($CustomerUID, $ProductUID);		
		// echo "<pre>";
		// print_r($data['milestonematricsdetails']);
		// exit();

		$this->load->view('customerMilestone',$data);
	}

	function insertcustomermilestone() {
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$OldMilestoneUID = $this->input->post('OldMilestoneUID');
		$MilestoneUID = $this->input->post('MilestoneUID');

		$data = $this->Customer_Model->GetCustomerProduct($CustomerUID);
		$ProductUID = $data['ProductUID'];

		if (!$WorkflowUID) {
			$WorkflowUID = null;
			//Check workflow id is already mapped to the client and product
			$dublicaterecordcount = $this->Customer_Model->CheckCustomerWorkflowMilestoneexist($CustomerUID, $ProductUID, $MilestoneUID);
			if ($dublicaterecordcount) {
				echo json_encode(array('error'=>1,'msg'=>'These changes are not saved. <br/>This milestone already exists. Choose another milestone!.','type'=>'danger'));
				exit();
			}
		} else {
			//Check milestone is already mapped to the client and product
			$dublicaterecordcount = $this->Customer_Model->CheckCustomerWorkflowMilestoneexists($CustomerUID, $ProductUID, $MilestoneUID);
			if ($dublicaterecordcount) {
				echo json_encode(array('error'=>1,'msg'=>'These changes are not saved. <br/>This milestone already exists. Choose another milestone!.','type'=>'danger'));
				exit();
			}
		}

		if ($OldMilestoneUID) {
			if ($MilestoneUID) {				
				//update milestone
				$result = $this->Customer_Model->NUpdateCustomerWorkflowMilestone($CustomerUID, $ProductUID, $WorkflowUID, $MilestoneUID, $OldMilestoneUID);
			} else {
				//delete milestone
				$result = $this->Customer_Model->DeleteCustomerWorkflowMilestone($CustomerUID, $ProductUID, $WorkflowUID, $MilestoneUID, $OldMilestoneUID);
			}
			
		} else {
			$result = $this->Customer_Model->InsertCustomerWorkflowMilestone($CustomerUID, $ProductUID, $WorkflowUID, $MilestoneUID);
		}
		
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Milestone Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>1,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	function LoadcustomerDependentMetricWorkflow()
 	{
		$CustomerUID = $this->input->post('CustomerUID');
		$data['Customers'] = $this->Customer_Model->get('mCustomer', ['CustomerUID'=>$CustomerUID]);
		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details($CustomerUID);
		$data['CustomerWorkflowMetricsDetails'] = $this->Customer_Model->GetCustomerWorkflowMetrics($CustomerUID);
		foreach ($data['CustomerWorkflowMetricsDetails'] as $key => $value) {
			$data['CustomerWorkflowMetricsDetails'][$key]['CompletedQueues_Arr'] = $this->Customer_Model->GetmCustomerWorkflowMetricsDependentWorkflows($value['CustomerWorkflowMetricUID']);
		}
		$this->load->view('customermetric',$data);
	}

	function LoadcustomerWorkflow(){
		/*START OF WORKFLOW TAB*/
		$CustomerUID = $this->input->post('CustomerUID');
		$details = $this->Customer_Model->Get_Customer_SubProduct_ById($CustomerUID); 
		$Prod_SubProd = $this->Customer_Model->Get_Customer_SubProduct_ById_Prod($CustomerUID); 
		//$Customer_Subproducts = [];
		foreach ($details as $key => $value) {
			$details[$key]['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_product_details($CustomerUID,$value['ProductUID'],$value['SubProductUID']);		
			$details[$key]['Customer_optionalWorkflow'] = $this->Customer_Model->get_customer_optionalworkflow_details($CustomerUID,$value['ProductUID'],$value['SubProductUID']);		
		}
		$data['details'] = $details;
		$data['WorkflowDetaiils'] = $this->Common_Model->GetWorkflowDetaiils($CustomerUID);
		$this->load->view('customerworkflowdependence', $data);
	}

	function LoadcustomerTat(){
		$CustomerUID = $this->input->post('CustomerUID');
		$data['CustomerUID']=$CustomerUID;
		$data['Prioritys'] = $this->Common_Model->get('mOrderPriority', ['Active'=>1]);
		$data['mtats'] = $this->Common_Model->get('mTAT', ['Active'=>1]);
		$details = $this->Customer_Model->Get_Customer_SubProduct_ById($CustomerUID); 
		//$Customer_Subproducts = [];
		foreach ($details as $key => $value) {

			$details[$key]['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_product_details($CustomerUID,$value['ProductUID'],$value['SubProductUID']);		
			$details[$key]['Customer_optionalWorkflow'] = $this->Customer_Model->get_customer_optionalworkflow_details($CustomerUID,$value['ProductUID'],$value['SubProductUID']);		
		}
		$data['details'] = $details;
		$this->load->view('customerpriority', $data);
	}

	function LoadcustomerAbstractor(){
		/*START OF ABSTRACTOR TAB*/
		$CustomerUID = $this->input->post('CustomerUID');
		$data['CustomerUID']=$CustomerUID;
		$data['ExcludeAbstractorDtails'] = $this->Customer_Model->GetExcludeAbstractorDtails($CustomerUID);
		$data['PrivateAbstractorDtails'] = $this->Customer_Model->GetPrivateAbstractorDtails($CustomerUID);
		/*END OF ABSTRACTOR TAB*/
		$this->load->view('customerabstractors', $data);
	}

	function LoadcustomerUploads(){
		/*START OF UPLOADS TAB*/
		$CustomerUID = $this->input->post('CustomerUID');
		$data['SubProducts'] = $this->Common_Model->GetSub_productDetails();
		$data['OrderTypes'] = $this->Customer_Model->GetOrderTypes();
		$data['OrderTypeDocuments']=$this->Customer_Model->GetCustomerOrderTypeDocs($CustomerUID);
		$data['Customers'] = $this->Customer_Model->GetCustomerDetailsByUID($CustomerUID);	
		$data['DefaultSubProduct'] = $this->Customer_Model->GetCustomerDefaultSubProduct($CustomerUID);
		$str = $data['DefaultSubProduct']->DefaultProductSubValue;	
		$arr  = explode(",",$str);
		$unSelectProduct = [];
		$Prod_SubProd = $this->Customer_Model->Get_Customer_SubProduct_ById_Prod($CustomerUID); 		
		foreach ($Prod_SubProd as $key => $value) {
			$subProductListvalue = $this->Customer_Model->get_customer_subproduct_details($CustomerUID,$value['ProductUID']);
			foreach ($subProductListvalue as $k => $v) {	
				$match = 0;
				foreach ($arr as $obj => $val) {				
					if($val == $v['SubProductUID']){
						$match = 1;
					}					
				}
				if($match != 1){
					$unSelectProduct[] = array("SubUID"=>$v['SubProductUID'] , "SubPName"=>$v['SubProductName']);
				}
			}			
		}
		$data['unSelectProducts'] = $unSelectProduct;	
		$this->load->view('customeruploads', $data);
	}



	function LoadcustomerUsers(){
		/*START OF WORKFLOW TAB*/
		$CustomerUID = $this->input->post('CustomerUID');
		$details = $this->Customer_Model->Get_Customer_SubProduct_ById($CustomerUID); 
		$Prod_SubProd = $this->Customer_Model->Get_Customer_SubProduct_ById_Prod($CustomerUID); 
		//$Customer_Subproducts = [];
		foreach ($details as $key => $value) {
			$details[$key]['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_product_details($CustomerUID,$value['ProductUID'],$value['SubProductUID']);		
			$details[$key]['CustomerProductUsers'] = $this->Customer_Model->get_mcustomerproductusers_details($CustomerUID,$value['ProductUID']);		
			$details[$key]['Customer_optionalWorkflow'] = $this->Customer_Model->get_customer_optionalworkflow_details($CustomerUID,$value['ProductUID'],$value['SubProductUID']);		
		}
		$data['details'] = $details;
		$data['WorkflowDetaiils'] = $this->Common_Model->GetWorkflowDetaiils($CustomerUID);
		$data['Users'] = $this->Common_Model->get('mUsers',['Active'=>1]);
		$this->load->view('customerusers', $data);
	}



	/*Editing Customer Page*/
	public function EditCustomer($CustomerUID)
	{
		$data['content'] = 'customer';
		$data['GetCustomer']=$this->Customer_Model->GetCustomerDet($CustomerUID);
		$data['AllProducts']=$this->Customer_Model->AllProducts();
		$data['ProductsDet']=$this->Customer_Model->ProductsDetails(); 
		$data['CustomerUID']= $CustomerUID;
		// $data['AllSubProducts'] = $this->Customer_Model->AllSubProducts();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function UpdateCustomerDetails()
	{

		$this->load->library('form_validation');
		$this->load->helper('form');
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_message('required', 'This Field is required');
			$this->form_validation->set_rules('AddressLine1','','required');
			$this->form_validation->set_rules('StateName','','required');
			$this->form_validation->set_rules('ZipCode','','required');
			$this->form_validation->set_rules('CityName','','required');
			$this->form_validation->set_rules('CustomerUID', '', 'required');
			$this->form_validation->set_rules('CustomerName','','required');
			$this->form_validation->set_rules('DefaultChecklistView','','required');
			$this->form_validation->set_rules('PreScreenChecklist','','required');
			if ($this->form_validation->run() == TRUE) 
			{
				$CustomerUID = $this->input->post('CustomerUID');		
		
				$CustomersDetails = $this->input->post();
				$CustomersDetails['Active'] = isset($CustomersDetails['Active']) ? 1 : 0;
				$CustomersDetails['EnableWorkupOption'] = isset($CustomersDetails['EnableWorkupOption']) ? 1 : 0;
				$CustomersDetails['HighlightExpiryOrders'] = isset($CustomersDetails['HighlightExpiryOrders']) ? 1 : 0;
				$CustomersDetails['HighlightLockExpiryOrdersColumn'] = isset($CustomersDetails['HighlightLockExpiryOrdersColumn']) ? 1 : 0;
				$CustomersDetails['NextPaymentDueRestriction'] = isset($CustomersDetails['NextPaymentDueRestriction']) && !empty($CustomersDetails['NextPaymentDueRestriction']) ? str_replace(' ', '', $CustomersDetails['NextPaymentDueRestriction']) : NULL;

				$this->Customer_Model->UpdateCustomerinfoDetails($CustomersDetails);
				$Msg = $this->lang->line('Customer_Update');
				$result = array("validation_error" => 0,'message'=>$Msg);
				echo json_encode($result);
			}else{
				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'CustomerName' => form_error('CustomerName'),
					'AddressLine1' => form_error('AddressLine1'),
					'StateName' => form_error('StateName'),
					'ZipCode' => form_error('ZipCode'),
					'CityName' => form_error('CityName'),
					'DefaultChecklistView' => form_error('DefaultChecklistView'),
					'PreScreenChecklist' => form_error('PreScreenChecklist'),
				);

				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')

						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}else{
			$data = array(
				'validation_error' => 1,
				'message' => $this->lang->line('Failed'),
				'CustomerName' => form_error('CustomerName'),
				'AddressLine1' => form_error('AddressLine1'),
				'StateName' => form_error('StateName'),
				'ZipCode' => form_error('ZipCode'),
				'CityName' => form_error('CityName'),
				'DefaultChecklistView' => form_error('DefaultChecklistView'),
				'PreScreenChecklist' => form_error('PreScreenChecklist'),
			);
			echo json_encode($data);
		}

	}

	function getzip()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$CustomerZipCode = $this->input->post('zipcode');
			$details = $this->Customer_Model->getzipcontents($CustomerZipCode);
			echo json_encode($details);

		}
	}

	function update_product(){

		$data['content'] = 'customer';
		$this->load->view('page', $data);
	}

	public function GetSubproducts()
	{
		$result = $this->Common_Model->GetSubproductByProduct($_POST['ProductUID']);
		$str="";
		foreach ($result as $row) 
		{
			$str.="<option value='".$row->SubProductUID."'>".$row->SubProductName."</option>";
		}
		echo $str;
	}
	function get_post_input_data(){
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = $search['value'];
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['StateUID'] = $this->input->post('StateUID');
		$post['CountyUID'] = $this->input->post('CountyUID');
		return $post;
	}

	function Update_Productsetup()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$Prod_WorkflowModule = $this->input->post('Prod_WorkflowModule');

		$CountWrokFlow = count($Prod_WorkflowModule);
		$k=0;
		for($i = 0; $i < $CountWrokFlow; $i++)
		{	
			if($Prod_WorkflowModule[$i]['WorkflowModuleUID'] == '')
			{
				$k++;
			}						
		}

		$res = FALSE;
		if($k > 0)
		{
			$res = array("validation_error" => 1,'message' => "Check the Workflow");
		} else {
			
			if($CustomerUID){
				$res = $this->Customer_Model->update_product_subproduct($CustomerUID,$Prod_WorkflowModule);
			}

			if($res){

				$res = array("validation_error" => 0,'message' => "Success");
			}else{

				$res = array("validation_error" => 1,'message' => "Failed");
			}
		}
		echo json_encode($res);
	}


	/*ADD CUSTOMER*/
	function add(){
		/*customer add Info tab*/
		$data['content'] = 'addcustomer';		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function LoadAddcustomerInfo(){
		/*Add customer Info tab*/
		$this->load->view('customeraddinfo');
	}


	/*ADD CUSTOMER*/
	function AddCustomerDetails()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_message('required', 'This Field is required');
			$this->form_validation->set_rules('CustomerName','','required|is_unique[mCustomer.CustomerName]');
			$this->form_validation->set_rules('AddressLine1','','required');
			$this->form_validation->set_rules('StateName','','required');
			$this->form_validation->set_rules('ZipCode','','required');
			$this->form_validation->set_rules('CityName','','required');
			if ($this->form_validation->run() == TRUE) 
			{
				$CustomersDetails = $this->input->post();
				$CustomerDetails['CreatedOn'] = date('Y-m-d H:i:s');
				$CustomerDetails['CreatedByUID'] = $this->loggedid;
				$CustomerDetails['Active'] = 1;


				$CustomerUID = $this->Customer_Model->AddCustomerinfoDetails($CustomersDetails);
				$Msg = $this->lang->line('Customer_Add');
				$result = array("validation_error" => 0,'message'=>$Msg,'CustomerUID'=>$CustomerUID,'Customer'=>$this->input->post());
				echo json_encode($result);
			}else{

				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'validation_error' => 1,
					'message' => $this->lang->line('Failed'),
					'CustomerName' => form_error('CustomerName'),
					'AddressLine1'=>form_error('AddressLine1'),
					'StateName'=>form_error('StateName'),
					'ZipCode'=>form_error('ZipCode'),
					'CityName'=>form_error('CityName'),
					'CustomerUID'=>''
				);
				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')

						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}else{
			$data = array(
				'validation_error' => 1,
				'message' => $this->lang->line('Failed'),
				'CustomerName' => form_error('CustomerName'),
				'AddressLine1'=>form_error('AddressLine1'),
				'StateName'=>form_error('StateName'),
				'ZipCode'=>form_error('ZipCode'),
				'CityName'=>form_error('CityName'),
				'CustomerUID'=>''
			);
			echo json_encode($data);
		}
	}

	/*CUSTOMER WORKFLOW ADD  START*/
	public function CustomerWorkflowAdd()
	{

		$this->db->trans_begin();

		$this->Customer_Model->SaveCustomerWorkflowModule($this->input->post());
		if ($this->db->trans_status()===false) {
			$this->db->trans_rollback();
			echo json_encode(array('validation_error' => 1,'message' => 'Unable to perform request!')); exit;
		}
		else{
			$this->db->trans_commit();	
			echo json_encode(array('validation_error' => 0, 'message'=>'Success'));
		}

	}

	/*CUSTOMER WORKFLOW ADD ENDS*/


	/*CUSTOMER Product Users ADD  START*/
	public function CustomerProductUsersAdd()
	{

		$this->db->trans_begin();

		$this->Customer_Model->SaveCustomerProductUsers($this->input->post());
		if ($this->db->trans_status()===false) {
			$this->db->trans_rollback();
			echo json_encode(array('validation_error' => 1,'message' => 'Unable to perform request!')); exit;
		}
		else{
			$this->db->trans_commit();	
			echo json_encode(array('validation_error' => 0, 'message'=>'Success'));
		}

	}

	/*CUSTOMER Product Users ADD ENDS*/

	/*CUSTOMERTAT TAB STARTS*/
	public function getdefaultpriorityvalues()
	{
		$priorities = $this->Common_Model->get('mOrderPriority', ['Active'=>1]);

		echo json_encode($priorities);
	}

	public function CustomeTATADD()
	{
		$post=$this->input->post();

		$this->db->trans_begin();

		$this->Customer_Model->SaveCustomerPriorities($post);
		if ($this->db->trans_status()===false) {
			$this->db->trans_rollback();
			echo json_encode(array('validation_error' => 1,'message' => 'Unable to perform request!')); exit;
		}
		else{
			$this->db->trans_commit();	
			echo json_encode(array('validation_error' => 0, 'message'=>'Success'));
		}

	}

	/*CUSTOMERTAT TAB ENDS*/

	function ajax_changestatus()
	{
		$CustomerUID = $this->input->post('custid');
		$data['Active'] = $this->input->post('status');

		if($this->Customer_Model->change_status($CustomerUID,$data))
		{
			$Msg = $this->lang->line('Search_Status');
			$res = array("validation_error" => 0,'message' => $Msg);
		} else {
			$Msg = $this->lang->line('Search_Status_Validation');
			$res = array("validation_error" => 0,'message' => $Msg);
		}
		echo json_encode($res);
	}

	function product_status()
	{
		$ProductUID = $this->input->post('ProductUID');
		$data['Active'] = $this->input->post('status');

		if($this->Customer_Model->product_change_status($ProductUID,$data))
		{
			$Msg = $this->lang->line('Search_Status');
			$res = array("validation_error" => 0,'message' => $Msg);
		} else {
			$Msg = $this->lang->line('Search_Status_Validation');
			$res = array("validation_error" => 0,'message' => $Msg);
		}
		echo json_encode($res);
	}

	function get_product_details(){
		$ProductUID = $this->input->post('ProductUID');
		$result = $this->Customer_Model->GetProductDetailsbyUID($ProductUID);
		echo json_encode($result);
	}

	function save_product_details(){
		$this->load->library('form_validation');
		$this->load->helper('form');
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_message('required', 'This Field is required');
			$this->form_validation->set_rules('ProductCode', '', 'required');
			$this->form_validation->set_rules('ProductName','','required');

			if($this->input->post('ProductCode') != $this->input->post('PreviousProductCode')) {
				$this->form_validation->set_rules('ProductCode','','required|is_unique[mproducts.ProductCode]');
			}
			if($this->input->post('ProductName') != $this->input->post('PreviousProductName')) {
				$this->form_validation->set_rules('ProductName','','required|is_unique[mproducts.ProductName]');
			}
			if ($this->form_validation->run() == TRUE) 
			{
				$data = $this->Customer_Model->saveProductsDetails($this->input->post());
				echo json_encode($data);
			}else{

				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'ProductCode' => form_error('ProductCode'),
					'ProductName' => form_error('ProductName'),
				);

				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')

						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}else{
			$data = array(
				'validation_error' => 1,
				'message' => $this->lang->line('Failed'),
				'ProductCode' => form_error('ProductCode'),
				'ProductName' => form_error('ProductName'),
			);
			echo json_encode($data);
		}

	}

	function isEmptyRow($row) {
		foreach($row as $cell){
			if (null !== $cell) return false;
		}
		return true;
	}
	function subproduct_status()
	{
		$SubProductUID = $this->input->post('SubProductUID');
		$data['Active'] = $this->input->post('status');

		if($this->Customer_Model->subproduct_change_status($SubProductUID,$data))
		{
			$Msg = $this->lang->line('Search_Status');
			$res = array("validation_error" => 0,'message' => $Msg);
		} else {
			$Msg = $this->lang->line('Search_Status_Validation');
			$res = array("validation_error" => 0,'message' => $Msg);
		}
		echo json_encode($res);
	}


	function get_subproduct_details(){
		$SubProductUID = $this->input->post('SubProductUID');
		$result = $this->Customer_Model->GetSubProductDetailsbyUID($SubProductUID);
		echo json_encode($result);
	}

	function save_subproduct_details(){
		$this->load->library('form_validation');
		$this->load->helper('form');
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_message('required', 'This Field is required');
			$this->form_validation->set_rules('ProductUID', '', 'required');
			$this->form_validation->set_rules('SubProductName','','required');

			// if($this->input->post('SubProductform_SubProductName') != $this->input->post('PreviousSubProductName')) {
			// 	$this->form_validation->set_rules('SubProductform_SubProductName','','required|is_unique[mSubProducts.SubProductName]');
			// }

			if ($this->form_validation->run() == TRUE) 
			{
				$data = $this->Customer_Model->saveSubProductsDetails($this->input->post());
				echo json_encode($data);
			}else{

				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'ProductUID' => form_error('ProductUID'),
					'SubProductName' => form_error('SubProductName'),
				);

				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')

						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}else{
			$data = array(
				'validation_error' => 1,
				'message' => $this->lang->line('Failed'),
				'ProductUID' => form_error('ProductUID'),
				'SubProductName' => form_error('SubProductName'),
			);
			echo json_encode($data);
		}

	}

	function unique_by_keys($haystack=array(),$needles=array()){
		foreach($haystack as $row){
        // declare unique key
			$key=implode('',array_intersect_key($row,array_flip($needles)));  
        // save row if non-duplicate
			if(!isset($result[$key])){$result[$key]=$row;} 
		}
		return array_values($result);
	}


	function addlogo($file , $post){
		error_reporting(E_ALL);
		$upload_url = FCPATH."uploads/customerlogo/";	
		if(!is_dir($upload_url)){		
			mkdir($upload_url, 0755, true);
		}
		if (isset($_FILES['image_upload']))
		{  
			$extension = pathinfo($_FILES['image_upload']['name'][0],PATHINFO_EXTENSION);
			$extensionArray = array('png','jpg','jpeg','gif');
			if(in_array($extension, $extensionArray))
			{
				if($_FILES['image_upload']['size'][0] < 10485760)
				{
					if(is_uploaded_file($_FILES['image_upload']['tmp_name'][0]))
					{
						$file_name = $_FILES['image_upload']['name'][0];
						$sourcePath = $_FILES['image_upload']['tmp_name'][0];
						$temp = explode(".", $_FILES["image_upload"]["name"][0]);
						$newfilename =  $post;
					///	$destination_path = $upload_url.$newfilename.'.'.$extension;  
						$destination_path = $upload_url.$newfilename.'.'.$extension;             
						try
						{
							if(move_uploaded_file($sourcePath, $destination_path))
							{
								return "uploads/customerlogo/".$newfilename.'.'.$extension;
							}                
						}
						catch (Exception $e)
						{
							return false;
						}            
					}  
				}else{
					return false;
				}
			}else{
				return false;
			}

		}
		else
		{
			return false;
		}
	}

	function sortWorkflowDependence()
	{
		$post = $this->input->post();	
		if($this->input->post('sortData'))
		{
			$file = $this->input->post('sortData');
			$CustomerUID = $this->input->post('CustomerUID');
			$Position = 1;
			foreach ($file as $value) 
			{
				$this->db->query('UPDATE mCustomerWorkflowModules SET Position = '.$Position.' WHERE WorkflowModuleUID = '.$value['ID'].' and CustomerUID = '.$CustomerUID);
				$Position++;		
			}
			echo json_encode(array('error'=>0,'msg'=>'Position Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	function UpdateDependentWorkflow()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$DependentWorkflow = $this->input->post('DependentWorkflow');

		if(!empty($DependentWorkflow))
		{
			$this->db->query('DELETE FROM mCustomerDependentWorkflowModules WHERE WorkflowModuleUID = '.$WorkflowUID.' and CustomerUID = '.$CustomerUID);
			foreach ($DependentWorkflow as $key => $value) 
			{
				$data[] =array(
					'CustomerUID' => $CustomerUID,
					'WorkflowModuleUID' => $WorkflowUID,
					'DependentWorkflowModuleUID' => $value);
			}
		}
		else
		{
			if($this->db->query('DELETE FROM mCustomerDependentWorkflowModules WHERE WorkflowModuleUID = '.$WorkflowUID.' and CustomerUID = '.$CustomerUID))
			{
				echo json_encode(array('error'=>0,'msg'=>'Dependent Workflow Updated.','type'=>'success'));
			}
			else
			{
				echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
			}
		}

		if(!empty($data))
		{
			if($this->db->insert_batch('mCustomerDependentWorkflowModules', $data))
			{
				echo json_encode(array('error'=>0,'msg'=>'Dependent Workflow Updated.','type'=>'success'));
			}
			else
			{
				echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
			}
		}
	}

	/**
		*@description Function to UpdateWorkflowStatus
		*
		* @param $CustomerUID, $WorkflowModuleUID, $StatusUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return JSON Response 
		* @since 16.3.2020 
		* @version Dynamic Workflow 
		*
	*/ 

	//update customer workflow status
	function UpdateCustomerWorkflowstatus()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$StatusUID = $this->input->post('StatusUID');

		$result = $this->Customer_Model->UpdateCustomerWorkflowstatus($CustomerUID, $WorkflowUID, $StatusUID);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Status Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//update customer workflow category
	function UpdateCustomerWorkflowCategory()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$CategoryUID = $this->input->post('CategoryUID');

		$result = $this->Customer_Model->UpdateCustomerWorkflowCategory($CustomerUID, $WorkflowUID, $CategoryUID);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Category Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//update customer workflow milestone
	function UpdateCustomerWorkflowMilestone()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$MilestoneUID = $this->input->post('MilestoneUID');

		$result = $this->Customer_Model->UpdateCustomerWorkflowMilestone($CustomerUID, $WorkflowUID, $MilestoneUID);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Milestone Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//update customer workflow ColorCode
	function UpdateCustomerWorkflowColorCode()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$ColorCode = $this->input->post('ColorCode');

		$result = $this->Customer_Model->UpdateCustomerWorkflowColorCode($CustomerUID, $WorkflowUID, $ColorCode);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'ColorCode Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}



	//Customer workflow optional update
	function UpdateCustomerWorkflowoptional()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$Optional = $this->input->post('Optional');

		$result = $this->Customer_Model->UpdateCustomerWorkflowoptional($CustomerUID, $WorkflowUID, $Optional);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Optional Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//Customer workflow IsDocChaseRequire update
	function UpdateCustomerWorkflowIsDocChaseRequire()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$IsDocChaseRequire = $this->input->post('IsDocChaseRequire');

		$result = $this->Customer_Model->UpdateCustomerWorkflowIsDocChaseRequire($CustomerUID, $WorkflowUID, $IsDocChaseRequire);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Document Chase Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//Customer workflow IsDocChaseRequire update
	function UpdateCustomerWorkflowIsEscalationRequire()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$IsEscalationRequire = $this->input->post('IsEscalationRequire');

		$result = $this->Customer_Model->UpdateCustomerWorkflowIsEscalationRequire($CustomerUID, $WorkflowUID, $IsEscalationRequire);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Escalation Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}


	//Customer workflow IsParkingRequire update
	function UpdateCustomerWorkflowIsParkingRequire()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$IsParkingRequire = $this->input->post('IsParkingRequire');
		$ParkingType = $this->input->post('ParkingType');
		$ParkingDuration = $this->input->post('ParkingDuration');
		$IsParkingCron = $this->input->post('IsParkingCron');

		$result = $this->Customer_Model->UpdateCustomerWorkflowIsParkingRequire($CustomerUID, $WorkflowUID, $IsParkingRequire, $ParkingType, $ParkingDuration,$IsParkingCron);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Parking is Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//Customer workflow SLA update
	function UpdateCustomerWorkflowSLA()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$SLA = $this->input->post('SLA');

		$result = $this->Customer_Model->UpdateCustomerWorkflowSLA($CustomerUID, $WorkflowUID, $SLA);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'SLA Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}


	function UpdateLoanPrioritizationMetrics()
	{
		// echo "<pre>";
		// print_r($_POST);
		// exit();

		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$DependentWorkflowModuleUID = $this->input->post('DependentWorkflowModuleUID');
		$CustomerWorkflowMetricUID = $this->input->post('CustomerWorkflowMetricUID');	
		$Priority = $this->input->post('Priority');		

		if(!empty($DependentWorkflowModuleUID))
		{
			$CustomerWorkflowMetricsdata = array(
				'CustomerUID' => $CustomerUID,
				'WorkflowModuleUID' => $WorkflowModuleUID,
				'Priority' => $Priority
			);
			$CustomerWorkflowMetricUID = $this->Customer_Model->InsertmCustomerWorkflowMetrics($CustomerWorkflowMetricsdata, $DependentWorkflowModuleUID, $CustomerWorkflowMetricUID);
			if($CustomerWorkflowMetricUID)
			{
				echo json_encode(array('error'=>0,'msg'=>'Loan Prioritization Metrics Updated.','type'=>'success','CustomerWorkflowMetricUID'=>$CustomerWorkflowMetricUID));
			}
			else
			{
				echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
			}
		}
		else
		{
			if($this->db->query('DELETE FROM mCustomerWorkflowMetrics WHERE WorkflowModuleUID = '.$WorkflowModuleUID.' and CustomerUID = '.$CustomerUID))
			{
				echo json_encode(array('error'=>0,'msg'=>'Dependent Metric Workflow Updated.','type'=>'success'));
			}
			else
			{
				echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
			}
		}

		if(!empty($data))
		{
			if($this->db->insert_batch('mCustomerWorkflowMetrics', $data))
			{
				echo json_encode(array('error'=>0,'msg'=>'Dependent Metric Workflow Updated.','type'=>'success'));
			}
			else
			{
				echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
			}
		}
	}

	function DeleteLoanPrioritizationMetrics()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$CustomerWorkflowMetricUID = $this->input->post('CustomerWorkflowMetricUID');

		$result = $this->Customer_Model->DeleteLoanPrioritizationMetrics($CustomerUID, $CustomerWorkflowMetricUID);

		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Loan Prioritization Metrics Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}	

	function PositionLoanPrioritizationMetrics()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$CustomerWorkflowMetricUID = $this->input->post('CustomerWorkflowMetricUID');

		$i = 1;

		foreach ($_POST['CustomerWorkflowMetricUID'] as $value) {
		    $this->db->query("UPDATE `mCustomerWorkflowMetrics` SET `Priority`=".$i." WHERE `CustomerWorkflowMetricUID`= ".$value."");
		    $i++;
		}

		echo json_encode(array('error'=>0,'msg'=>'Priority Updated.','type'=>'success'));
	}

	function UpdateWorkflowModuleUIDLoanPrioritizationMetrics()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$CustomerWorkflowMetricUID = $this->input->post('CustomerWorkflowMetricUID');

		$result = $this->Customer_Model->UpdateWorkflowModuleUIDLoanPrioritizationMetrics($CustomerUID, $WorkflowModuleUID, $CustomerWorkflowMetricUID);
		if($result)
		{
			echo json_encode(array('error'=>0,'msg'=>'Completed Queue Updated.','type'=>'success','CustomerWorkflowMetricUID'=>$CustomerWorkflowMetricUID));
		}
		else
		{
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}	

	function customerlogoUpload($file , $post)
	{	
 //echo '<pre>';print_r($post);exit;
		error_reporting(E_ALL);

		$upload_url = FCPATH."uploads/customerlogo/";
		//Check if the directory already exists.
		if(!is_dir($upload_url)){
			//Directory does not exist, so lets create it.
			mkdir($upload_url, 0755, true);
		}
		if (isset($_FILES['image_upload']))
		{  
			$extension = pathinfo($_FILES['image_upload']['name'][0],PATHINFO_EXTENSION);


			$extensionArray = array('png','jpg','jpeg','gif');
			if(in_array($extension, $extensionArray))
			{

				if($_FILES['image_upload']['size'][0] < 10485760)
				{
					if(is_uploaded_file($_FILES['image_upload']['tmp_name'][0]))
					{
						$file_name = $_FILES['image_upload']['name'][0];
						$sourcePath = $_FILES['image_upload']['tmp_name'][0];

						$temp = explode(".", $_FILES["image_upload"]["name"][0]);
						$newfilename = $this->input->post('CustomerUID');
						$destination_path = $upload_url.$newfilename.'.'.$extension;          
						try
						{
							if(move_uploaded_file($sourcePath, $destination_path))
							{
								return "uploads/customerlogo/".$newfilename.'.'.$extension;
							}                
						}
						catch (Exception $e)
						{
							return false;
						}            
					}  
				}else{
					return false;
				}
			}else{
				return false;
			}

		}
		else
		{
			return false;
		}
	}

	//update customer workflow state
	function UpdateCustomerWorkflowState()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$state = $this->input->post('state');
		$State = implode(',', $state);

		$result = $this->Customer_Model->UpdateCustomerWorkflowState($CustomerUID, $WorkflowUID, $State);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'State Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//update customer workflow LoanType
	function UpdateCustomerWorkflowLoanType()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$LoanTypeName = $this->input->post('LoanTypeName');
		$LoanTypeName = implode(',', $LoanTypeName);

		$result = $this->Customer_Model->UpdateCustomerWorkflowLoanType($CustomerUID, $WorkflowUID, $LoanTypeName);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Loan Type Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//Customer workflow PropertyType update
	function UpdateCustomerWorkflowPropertyType()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$PropertyType = $this->input->post('PropertyType');

		$result = $this->Customer_Model->UpdateCustomerWorkflowPropertyType($CustomerUID, $WorkflowUID, $PropertyType);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Property Type Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//Customer workflow ChecklistSequence update
	function UpdateCustomerWorkflowChecklistSequence()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$ChecklistSequence = $this->input->post('ChecklistSequence');
		$result = $this->Customer_Model->UpdateCustomerWorkflowChecklistSequence($CustomerUID, $WorkflowUID, $ChecklistSequence);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Checklist Sequence Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//update customer workflow MilestoneUID
	function UpdateCustomerWorkflowMilestoneUID()
	{
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$MilestoneUID = $this->input->post('MilestoneUID');
		$MilestoneUID = implode(',', $MilestoneUID);

		$result = $this->Customer_Model->UpdateCustomerWorkflowMilestoneUID($CustomerUID, $WorkflowUID, $MilestoneUID);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Milestone Updated.','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
		}
	}

	//Update order highlight duration 
	function UpdateCustomerWorkflowOrderHighlightDuration()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$CustomerUID = $this->input->post('CustomerUID');
			$WorkflowUID = $this->input->post('workflowUID');
			$OrderHighlightDuration = $this->input->post('OrderHighlightDuration');

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('OrderHighlightDuration','','numeric');

			if ($this->form_validation->run() == TRUE) 
			{
				$result = $this->Customer_Model->UpdateCustomerWorkflowOrderHighlightDuration($CustomerUID, $WorkflowUID, $OrderHighlightDuration);
				if($result) {
					echo json_encode(array('error'=>0,'msg'=>'Order highlight Duration Updated.','type'=>'success'));
				} else {
					echo json_encode(array('error'=>0,'msg'=>'Something went wrong!.','type'=>'danger'));
				}
			}else{
				$data = array(
					'error' => 1,
					'msg' => form_error('OrderHighlightDuration'),
					'type'=>'danger'
				);

				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')

						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}
		
	}

}?>
