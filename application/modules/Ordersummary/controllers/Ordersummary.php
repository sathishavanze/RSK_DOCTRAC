<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ordersummary extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Ordersummarymodel');
	}

	public function index()
	{
		$OrderUID = $this->uri->segment(3);
		$data['content'] = 'index';
		$data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID' => 'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID' => 'ASC'], []);
		$data['OrderSummary'] = $this->Ordersummarymodel->GettOrders($OrderUID);
		$data['Workflows'] = $this->Common_Model->GetOrderWorkflows($OrderUID);
		$data['Status'] = $this->Common_Model->GetOrderWorkflowsWithStatus($OrderUID);

		// Assign Document Storage value if not present
		$this->Ordersummarymodel->checkAssignDocumentStorage($data['OrderSummary']);

		$data['Documents'] = $this->Ordersummarymodel->GetDocuments($OrderUID);
		$data['OrderDetails'] = $this->Common_Model->getOrderDetails($OrderUID);
		// echo '<pre>'; print_r($data['OrderDetails']); exit;
		$data['ExceptionList'] = $this->Common_Model->GetOrderExceptions($OrderUID);
		$data['BorrowerName'] = $this->Ordersummarymodel->GetBorrowerName($OrderUID);

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}



	function insert()
	{
		$this->load->library('form_validation');
		$this->form_validation->CI = &$this;
		$data['content'] = 'index';

		if (!isset($_POST['OrderUID'])) {
			redirect("Orderentry");
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {

			$this->form_validation->set_error_delimiters('', '');


			$this->form_validation->set_rules('Customer', '', 'required');
			/*$this->form_validation->set_rules('PropertyAddress1', '', 'required');
			$this->form_validation->set_rules('PropertyCityName', '', 'required');*/
			/*$this->form_validation->set_rules('PropertyStateCode', '', 'required');*/
			/*$this->form_validation->set_rules('PropertyCountyName', '', 'required');
			$this->form_validation->set_rules('PropertyZipcode', '', 'required');*/
			$this->form_validation->set_rules('PriorityUID', '', 'required');

			/*LOOP VALIDATION*/

			$this->form_validation->set_rules('LoanNumber', 'LoanNumber', 'callback_check_loannumber');

			$this->form_validation->set_message('required', 'This Field is required');

			if ($this->form_validation->run() == true) {

				$OrderDetails = $this->input->post();


				$result = $this->Ordersummarymodel->insert_order($OrderDetails);

				$this->Common_Model->updateChecklist($this->input->post());
				//order priority raise parking
				$additionalmessage = $this->orderpriority_parking($OrderDetails);

				$result = array("validation_error" => 0, "id" => '', 'message' => $result['message'] . $additionalmessage, 'OrderUID' => $OrderDetails['OrderUID']);
				echo json_encode($result);
			} else {

				$Msg = $this->lang->line('Empty_Validation');

				$formvalid = [];

				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'Customer' => form_error('Customer'),
					'PropertyAddress1' => form_error('PropertyAddress1'),
					'PropertyCityName' => form_error('PropertyCityName'),
					/*'PropertyStateCode' => form_error('PropertyStateCode'),*/
					'PropertyCountyName' => form_error('PropertyCountyName'),
					'PropertyZipcode' => form_error('PropertyZipcode'),
					'LoanNumber' => form_error('LoanNumber'),
					'ProjectUID' => form_error('ProjectUID'),
					'PriorityUID' => form_error('PriorityUID'),
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}
	}


	function verify_password()
	{
		$password = md5($this->input->post('Password'));
		$result = $this->Common_Model->Verify_Password($this->loggedid, $password);
		if ($result) {
			$res = array('validation_error' => 1, 'message' => 'Template Changed');
		} else {
			$Msg = $this->lang->line('Error');
			$res = array("validation_error" => 0, 'message' => $Msg);
		}
		echo json_encode($res);
	}

	function GetStatusCode($OrderUID)
	{
		$this->db->select('StatusUID');
		$this->db->from('tOrders');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		return $this->db->get()->row()->StatusUID;
	}

	//raise parking with order priority
	function orderpriority_parking($post)
	{
		$message = '';
		$WorkflowModuleUID = $post['WorkflowModuleUID'];
		$OrderUID = $post['OrderUID'];
		if (!empty($OrderUID) && !empty($WorkflowModuleUID) && isset($post['orderpriority_parking']) && !empty($post['orderpriority_parking'])) {

			$ParkingTypeUID = $post['orderpriority_parking'];
			$mParkingType = $this->Common_Model->get_row('mParkingType', ['ParkingTypeUID' => $ParkingTypeUID]);
			if (!empty($mParkingType)) {
				$Remainderaddhours = $mParkingType->TAT;
				if (empty($Remainderaddhours)) {
					$Remainderaddhours = $post['orderpriority_parkinghour'];
				}

				if (!empty($Remainderaddhours)) {

					$Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID' => $WorkflowModuleUID]);
					//convert time trom hours
					$current_time = date('Y-m-d H:i:s');
					$Remainder = $current_time . "+" . abs($Remainderaddhours) . "hours";

					$this->db->trans_begin();

					$is_assignment_row_available = $this->Common_Model->get_row('tOrderAssignments', ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);

					if (empty($is_assignment_row_available)) {
						$res['WorkflowStatus'] = $this->config->item('WorkflowStatus')['Assigned'];
						$res['OrderUID'] = $OrderUID;
						$res['WorkflowModuleUID'] = $WorkflowModuleUID;
						$res['AssignedToUserUID'] = $this->loggedid;
						$res['AssignedDatetime'] = date('Y-m-d H:i:s');
						$res['AssignedByUserUID'] = $this->loggedid;
						$this->db->insert('tOrderAssignments', $res);
					}

					$is_parking_row_available = $this->Common_Model->get_row('tOrderParking', ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID, 'IsCleared' => 0]);

					if (empty($is_parking_row_available)) {
						$data['OrderUID'] = $OrderUID;
						$data['WorkflowModuleUID'] = $WorkflowModuleUID;
						$data['ParkingTypeUID'] = $ParkingTypeUID;
						$data['ReasonUID'] = '';
						$data['Remainder'] = date('Y-m-d H:i:s', strtotime($Remainder));
						$data['Remarks'] = $mParkingType->ParkingTypeName;
						$data['RaisedByUserUID'] = $this->loggedid;
						$data['RaisedDateTime'] = date('Y-m-d H:i:s');
						$this->Common_Model->save('tOrderParking', $data);
						$message = $this->lang->line('Update_raisedtoparking');
					}


					if ($this->db->trans_status() === false) {
						$this->db->trans_rollback();
						return false;
					} else {
						/*INSERT ORDER LOGS BEGIN*/
						$this->Common_Model->OrderLogsHistory($OrderUID, $Workflow->WorkflowModuleName . ' - Parking Raised', Date('Y-m-d H:i:s'));
						/*INSERT ORDER LOGS END*/
						$this->db->trans_commit();
						return $message;
					}
				}
			}
		}
	}

	/**
	 *Function particular checklist autosave 
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Monday 08 June 2020.
	 */
	function update_checklist()
	{
		$this->Common_Model->updateChecklist($this->input->post());
		echo 'success';
	}

	/**
	 *Function Duplicate Loan Number with respect to client
	 *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	 *@since Wednesday 24 June 2020
	 */
	public function check_loannumber()
	{
		$ClientUID = $this->input->post('Customer'); // get client
		$LoanNumber = $this->input->post('LoanNumber'); // get Loan cNumber
		$OrderUID = $this->input->post('OrderUID'); // get Loan cNumber
		$this->form_validation->set_message('check_loannumber', 'Duplicate {field}');

		if (empty($LoanNumber)) {
			return TRUE;
		}

		$Client = $this->Ordersummarymodel->get_customerbyuid($ClientUID);

		if (empty($Client)) {
			return TRUE;
		}

		$LoanNumberValidation = $Client->LoanNumberValidation;

		if (empty($LoanNumberValidation)) {
			return TRUE;
		}

		if (strlen($LoanNumber) == $LoanNumberValidation) {

			$this->db->select('tOrders.OrderNumber');
			$this->db->from('tOrders');
			$this->db->where('CustomerUID', $ClientUID);
			$this->db->where('LoanNumber', $LoanNumber);
			$this->db->where('OrderUID <>', $OrderUID);
			$query = $this->db->get();
			$num = $query->num_rows();
			if ($num > 0) {
				return FALSE;
			} else {
				return TRUE;
			}
		}

		$this->form_validation->set_message('check_loannumber', 'Loan Number should be exactly ' . $LoanNumberValidation . ' characters');

		return FALSE;
	}

}
