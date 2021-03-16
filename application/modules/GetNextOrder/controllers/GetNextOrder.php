<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class GetNextOrder extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('GetNextOrder_Model');
	}	

	public function index()
	{
		$data['content'] = 'index';
		$WorkflowModule=$this->input->post('WorkflowModule');
		$role_permissions=$this->Common_Model->get_rolepermissions();
		if (!in_array('GetNextOrder', $role_permissions)) {
			redirect(base_url('Profile'),'refresh');
		}
		else
		{
		$data['WorkflowModules'] = $this->Common_Model->get_customerroleworkflows();
		$GetLoanTypeDetails = $this->Common_Model->GetLoanTypeDetails();
		/*foreach ($GetLoanTypeDetails as $value) {
		    if ($value->LoanTypeName == "FHA") {
		    	$data['FHA_LockExpirationDate'] = $value->LockExpiration;
		    } elseif ($value->LoanTypeName == "VA") {
		    	$data['VA_LockExpirationDate'] = $value->LockExpiration;
		    }
		}*/
		
		//print_r($this->db)
			$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
		}	
	}
	/**
	*Get the users based on workflow for Get next order Setup
	*@author harini <harini.bnagari@avanzegroup.com>
	*@since Tuesday 18 August 2020.
	*/
	public function GetUsersByWorkflow()
	{
		$WorkflowModule = $this->input->post('WorkflowModule');	
		$LockExpirationDetails = $this->GetNextOrder_Model->GetLockExpirationDetails($WorkflowModule);
		$data['LoanTypes'] = $this->Common_Model->GetLoanTypeDetails();
		$data['userslist']=$this->GetNextOrder_Model->GetUsersByWorkflow($WorkflowModule);
		$WorkflowUserslist = $this->load->view('getusersbyworkflow', $data,true);
		echo json_encode(array('WorkflowUserslist'=>$WorkflowUserslist, 'FHALockExpirationDate'=>$LockExpirationDetails['FHALockExpirationDate'], 'VALockExpirationDate'=>$LockExpirationDetails['VALockExpirationDate']));

	}
	/**
	*Agent level Get Next Order Assigning
	*@author harini <harini.bnagari@avanzegroup.com>
	*@since Tuesday 18 August 2020.
	*/
	public function AssignGetNextOrder()
	{
		$WorkflowModule = $this->input->post('WorkflowModuleUID');	
		$assigned_UID =array();
		array_push($assigned_UID, $this->loggedid);
		$order_num = $this->GetNextOrder_Model->get_assign_nextorder($assigned_UID,$WorkflowModule,'',['loan_type'=>true]);
		//echo $order_num;exit;
		if(empty($order_num))
		{
			$Msg =  "No loans to assign";
        	echo json_encode(array('validation_error' => 1, 'message' => $Msg));
		}
		else
		{
			$Msg =  "Loan Number: ".$order_num." has been assigned";
         		echo json_encode(array('validation_error' => 0, 'message' => $Msg));	
		}
		exit;

	}
	/**
	*To check whether thw GetNextOrder button permission is enabled
	*@author harini <harini.bnagari@avanzegroup.com>
	*@since Tuesday 18 August 2020.
	*/
	public function GetNextOrderPermission()
	{
		$permissions=$this->Common_Model->get_row('mRole',['RoleTypeUID'=>$this->RoleType] );
		echo $permissions->AssignGetNextOrder;
		exit;

	}
	/**
	*Function for admin Setup
	*@author harini <harini.bnagari@avanzegroup.com>
	*@since Tuesday 18 August 2020.
	*/
	public function assign_next_order_setup()
	{
		$assigned_UID = $this->input->post('assigned_UID');	
		$WorkflowModule = $this->input->post('WorkflowModule');	
		$order_num = $this->GetNextOrder_Model->get_assign_nextorder($assigned_UID,$WorkflowModule,$this->input->post('loan_type'));
		if(empty($order_num))
		{
			$Msg =  "No loans to assign";
        	echo json_encode(array('validation_error' => 1, 'message' => $Msg));
		}
		else
		{
			$Msg =  "Loan Number: ".$order_num." has been assigned";
         		echo json_encode(array('validation_error' => 0, 'message' => $Msg));	
		}
	}
	function get_statesbygroup()
	{
		$States = [];
		$GroupUID = $this->input->post('GroupUID');
		if($GroupUID) {
			$States = $this->GetNextOrder_Model->get_groupstates($GroupUID);
		}
		$this->output->set_content_type('application/json')->set_output(json_encode(['States'=>$States]))->_display();exit;
	}

	function post_assign_next_order()
	{
		$GroupUID = $this->input->post('GroupUID');
		$StateUID = $this->input->post('StateUID');
		$Page = $this->input->post('Page');

		$WorkflowModuleUID =  isset($this->config->item('Header_WorkflowMenu')[$Page]) ? $this->config->item('Header_WorkflowMenu')[$Page] : NULL;

		$functioncall =  isset($this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call']) ? $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['function_call'] : NULL;

		if(empty($GroupUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(['status'=>false,'message'=>$this->lang->line('order_notassigned_group'),'redirecturl'=>'']))->_display();exit;
		}
		if(empty($WorkflowModuleUID)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(['status'=>false,'message'=>$this->lang->line('order_notassigned_page'),'redirecturl'=>'']))->_display();exit;
		}

		if(empty($functioncall)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(['status'=>false,'message'=>$this->lang->line('order_workflow_notconfigured'),'redirecturl'=>'']))->_display();exit;
		}


		$Order = $this->GetNextOrder_Model->assign_next_order($GroupUID,$WorkflowModuleUID,$StateUID,$functioncall);

		if(!empty($Order['OrderNumber']) && !empty($Order['OrderUID'])) {
			$this->output->set_content_type('application/json')->set_output(json_encode(['status'=>true,'message'=>sprintf($this->lang->line('order_assigned'), $Order['OrderNumber']),'redirecturl'=>base_url().'Ordersummary/index/'.$Order['OrderUID']]))->_display();exit;
		}


		$this->output->set_content_type('application/json')->set_output(json_encode(['status'=>false,'message'=>$this->lang->line('order_notassigned_noorders'),'redirecturl'=>'']))->_display();exit;

	}

	/**
	*Function update loantypes 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 08 September 2020.
	*/
	function Updateloantype() {
		$CustomerUID = $this->input->post('CustomerUID');
		$WorkflowUID = $this->input->post('workflowUID');
		$UserUID = $this->input->post('UserUID');
		$loantype = $this->input->post('loantype');

		$result = $this->GetNextOrder_Model->Updateworkflowloantype($CustomerUID, $WorkflowUID, $UserUID, $loantype);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Updated','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Failed!!!','type'=>'danger'));
		}
	}

	/**
	*Function FHA Lock Expiration Date 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 23 September 2020.
	*/
	function Update_FHA_LockExpirationDate() {
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$FHA_LockExpirationDate = !empty($this->input->post('FHA_LockExpirationDate')) ? $this->input->post('FHA_LockExpirationDate'): NULL;

		$result = $this->GetNextOrder_Model->Update_FHA_LockExpirationDate($WorkflowModuleUID, $FHA_LockExpirationDate);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Updated','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Failed!!!','type'=>'danger'));
		}
	}

	/**
	*Function VA Lock Expiration Date 
	*@author SathishKumar <sathish.kumar@avanzegroup.com>
	*@since Wednesday 23 September 2020.
	*/
	function Update_VA_LockExpirationDate() {
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$VA_LockExpirationDate = !empty($this->input->post('VA_LockExpirationDate')) ? $this->input->post('VA_LockExpirationDate'): NULL;

		$result = $this->GetNextOrder_Model->Update_VA_LockExpirationDate($WorkflowModuleUID, $VA_LockExpirationDate);
		if($result) {
			echo json_encode(array('error'=>0,'msg'=>'Updated','type'=>'success'));
		} else {
			echo json_encode(array('error'=>0,'msg'=>'Failed!!!','type'=>'danger'));
		}
	}

} 

?>