<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PreScreen extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('PreScreen_Model');
		$this->load->model('Ordersummary/Ordersummarymodel');
	}

	public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		$data['Customers'] = $this->Common_Model->get('mCustomer', [], ['CustomerUID' => 'ASC'], []);
		$data['Projects'] = $this->Common_Model->get('mProjectCustomer', [], ['ProjectUID' => 'ASC'], []);
		$data['OrderSummary'] = $this->Ordersummarymodel->GettOrders($OrderUID);
		$data['OrderDetails'] = $this->Common_Model->getOrderDetails($OrderUID);
		$data['BorrowerName'] = $this->Ordersummarymodel->GetBorrowerName($OrderUID);
		$data['workflow'] = $this->PreScreen_Model->get_PrescreenWorkflowDetails($OrderUID);
		$data['Workflows'] = $this->Common_Model->GetOrderWorkflows($OrderUID);
		$data['Status'] = $this->Common_Model->GetOrderWorkflowsWithStatus($OrderUID);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
		/* 
	* Function for get checklist alert message
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 28-07-2020
	*/
	function checklistAlert()
	{
		$DocumentTypeUID = $this->PreScreen_Model->getChecklistAlert($this->input->post('DocumentTypeUID'));
		$todayDate = date('Y-m-d') . ' 00:00:00';
		$Trustee_Checklist = $this->config->item('Trustee_Checklist');
		$OrderUID = $this->input->post('OrderUID');
		foreach ($DocumentTypeUID as $key => $value) {
			$ChecklistAlertMessage = '';


			if ($value->ChecklistComment) {
				if ($value->AlertStartDate <= $todayDate && $value->AlertEndDate >= $todayDate) {
					$ChecklistAlertMessage = $value->ChecklistComment.'<br>';
				}
			}


			//check is trustee checklist
			if(in_array($value->DocumentTypeUID, $Trustee_Checklist)) {

				$Order = $this->Common_Model->get_orderinfo($OrderUID);

				if(!empty($Order)) {

					$statematrix = $this->Common_Model->get_statematrix($Order->PropertyStateCode);

					if(!empty($statematrix)) {

						//Trustee  check
						if(!empty($statematrix->Trustee) && $statematrix->Trustee != 'NA') {

							$ChecklistAlertMessage .= '<b>'.$statematrix->Trustee.'</b><br>';
						}

						if(!empty($statematrix->Trustee) && $statematrix->Trustee == 'County Name') {

							$ChecklistAlertMessage .= '<b>'.$Order->PropertyCountyName.'</b><br>';
						}

						//addtional requirement  check
						if(!empty($statematrix->AdditionalRequirements) && $statematrix->AdditionalRequirements != 'NA') {

							$ChecklistAlertMessage .= '<b>'.$statematrix->AdditionalRequirements.'</b>';
						}

						//Title Company Name
						if($statematrix->DisplayTitleCompany == 1) {

							$ChecklistAlertMessage .= '<b>'.$Order->TitleInsuranceCompanyName.'</b>';
						}

					

					}
				}
			}

			if(!empty($ChecklistAlertMessage)) {
				$data['message'][] = $ChecklistAlertMessage;
			}
		}

		if ($data['message']) {
			$res = array('Status' => 1, 'message' => $data['message']);
			echo json_encode($res);
			//exit();
		}
	}

	/* 
	* Function for get modal data
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 29-07-2020
	*/
	function getModal()
	{
		$OrderUID = ($this->input->post('OrderUID'));
		//get CategoryUID of WorkflowModuleUID
		$Modules = $this->Common_Model->GetCustomerBasedModules();
		$OrderSummary = $this->Ordersummarymodel->GettOrders($OrderUID);
		$table = '<input type="hidden" name="OrderUID" id="OrderUID" value="' . $OrderSummary->OrderUID . '">
		<input type="hidden" name="OrderNumber" id="OrderNumber" value="' . $OrderSummary->OrderNumber . '">
		<input type="hidden" name="OnHoldUID" id="OnHoldUID" value="' . $OrderSummary->OnHoldUID . '">';
		foreach ($Modules as $keyModules => $valueModules) {
			$moduleData[$valueModules->WorkflowModuleUID] = $valueModules->CategoryUID;
		}
		$workflow = $this->PreScreen_Model->get_PrescreenWorkflowDetails($OrderSummary->OrderUID);
		foreach ($workflow as $key => $workflow) {
			$data['ChecklistFields'] = $this->Common_Model->get_dynamicchecklistfields($OrderSummary->OrderUID, $workflow->WorkflowModuleUID);
			$getDocumentlistOrder = ($this->Common_Model->getDocumentlistOrder($OrderSummary->OrderUID, $moduleData[$workflow->WorkflowModuleUID]));
			$encodeGetDocumentlistOrder = json_encode($getDocumentlistOrder);
			$explodeDocument = str_replace('[', '', $encodeGetDocumentlistOrder);
			$explodeDocument = str_replace(']', '', $explodeDocument);
			if ($workflow->WorkflowModuleUID == 4) {
				$DocumentTypeNameDetails = $this->Common_Model->getfhacategorylist($OrderSummary->OrderUID, $workflow->WorkflowModuleUID, $OrderSummary->LoanType);
			} else {
				$DocumentTypeNameDetails = $this->Common_Model->getDocumentCategoryList($OrderSummary->OrderUID, $workflow->WorkflowModuleUID, $OrderSummary->LoanType, ($explodeDocument));
			}

			$question_sno = 0;
			$workflowtitle = $workflow->WorkflowModuleName;
			if ($workflow->WorkflowModuleUID == 4) {
				if ($OrderSummary->LoanType != '' && $OrderSummary->LoanType != 'FHA/VA') {
					if ($OrderSummary->LoanType == 'VA') {
						$workflowtitle = 'VA';
					} else if ($OrderSummary->LoanType == 'FHA') {
						$workflowtitle = 'FHA';
					}
				} else {
					$workflowtitle = 'FHA/VA';
				}
			}
			$table .= '<strong>
				<h6 class="pre_screen_head">' . $workflowtitle . '</h6>
			</strong>';

			$data['workflowtitle'] = $workflowtitle;
			$data['WorkflowModuleUID'] = $workflow->WorkflowModuleUID;

			$table .=$this->load->view('checklist/checklistheader', $data,TRUE);

			//$table .= '<tbody class="addChecklist' . $workflow->WorkflowModuleUID . '">';

			/* get Deleted checklist array*/
			$deletedDocument = ($this->Common_Model->getDocumentCheckList($OrderSummary->OrderUID));

			foreach ($DocumentTypeNameDetails as $key => $DocTypeName) {
				if (!in_array($DocTypeName->DocumentTypeUID, $deletedDocument)) {
					$question_sno += 1;
					$data['DocTypeName'] = $DocTypeName;
					$data['OrderUID'] = $OrderSummary->OrderUID;
					$data['question_sno'] = $question_sno;
					$table .=$this->load->view('checklist/checklist', $data,TRUE);

					/*child checklist start*/
					$childchecklists = $this->Common_Model->get_childchecklists($OrderSummary->CustomerUID, $workflow->WorkflowModuleUID, $DocTypeName->DocumentTypeUID, $OrderSummary->LoanType);
					if (!empty($childchecklists)) {
						$childquestion_sno = 0;
						foreach ($childchecklists as $childchecklistkey => $childchecklistvalue) {
							if (!in_array($childchecklistvalue->DocumentTypeUID, $deletedDocument)) {
								$childquestion_sno += 1;
								$childata['DocTypeName'] = $childchecklistvalue;
								$childata['OrderUID'] = $OrderSummary->OrderUID;
								$childata['question_sno'] = $question_sno . '.' . $childquestion_sno;
								$table .=$this->load->view('checklist/checklist', $childata,TRUE);
							}
						}
					}
					/*child checklist ends*/
				}
			}
			$data1['OrderUID'] = $OrderSummary->OrderUID;
			$data1['question_sno'] = $question_sno;
			$data1['WorkflowUID'] = $workflow->WorkflowModuleUID;
			$data1['ChecklistFields'] = $data['ChecklistFields'];
			$table .=$this->load->view('checklist/otherchecklist', $data1,TRUE);
			$table .= '</tbody></table>';
			$table .= '<table class="addnewchecklistpree"> <tbody > <tr> <td colspan="5"> <p class="custom_add_icon pull-right addchecklistrowPreScreen" data-div="addChecklist' . $question_sno . '" data-count=' . $question_sno . ' data-moduleUID=' . $workflow->WorkflowModuleUID . ' aria-hidden="true">Add New Checklist</p> </td> </tr> <tr> <td colspan="5">&nbsp;</td> </tr> </tbody> </table>';
		}
		$table .= '</div>';
		echo $table;
	}

	/* 
	* Function for get modal data
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 29-07-2020
	*/
	function getOtherModal()
	{
		$OrderUID = ($this->input->post('OrderUID'));
		$uri = explode('_', ($this->input->post('uri')))[0];
		$uri = ($uri=='WorkUp') ? 'Workup' : $uri;
/* 		echo $uri;
		exit;  */
		$Modules = $this->Common_Model->GetCustomerBasedModules();
		$WorkflowModuleUID = $this->config->item('Workflows')[$uri];
		$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID);
		$groups = $OrderDetails->LoanType;
		$OrderSummary = $this->Ordersummarymodel->GettOrders($OrderUID);
				//get CategoryUID of WorkflowModuleUID
				$table = '<input type="hidden" name="OrderUID" id="OrderUID" value="' . $OrderUID . '">
				<input type="hidden" name="OrderNumber" id="OrderNumber" value="' . $OrderSummary->OrderNumber . '">
				<input type="hidden" name="OnHoldUID" id="OnHoldUID" value="' . $OrderSummary->OnHoldUID . '">
				<input type="hidden" name="LoanNumber" id="LoanNumber" value="' . $OrderSummary->LoanNumber . '">
				<input type="hidden" name="workflowUID" id="workflowUID" value="'.$WorkflowModuleUID.'">';
		
		$this->load->view('Ordersummary/Orderdetails');
		//get CategoryUID of WorkflowModuleUID
		foreach ($Modules as $keyModules => $valueModules) {
			$moduleData[$valueModules->WorkflowModuleUID] = $valueModules->CategoryUID;
		}
		$getDocumentlistOrder = ($this->Common_Model->getDocumentlistOrder($OrderUID, $moduleData[$WorkflowModuleUID]));
		$data['ChecklistFields'] = $this->Common_Model->get_dynamicchecklistfields($OrderUID, $WorkflowModuleUID);
		$encodeGetDocumentlistOrder = json_encode($getDocumentlistOrder);
		$explodeDocument = str_replace('[', '', $encodeGetDocumentlistOrder);
		$explodeDocument = str_replace(']', '', $explodeDocument);
		$DocumentTypeNameDetails = $this->Common_Model->getcategorylist($OrderUID, $WorkflowModuleUID, $groups);

		$table .= '<strong><h6 class="pre_screen_head">' . $uri . '      Loan#' . $OrderSummary->LoanNumber . '</h6>
	</strong>';
		$table .=$this->load->view('checklist/checklistheader', $data,TRUE);


		$table .= '<tbody class="addChecklist">';
		$deletedDocument = ($this->Common_Model->getDocumentCheckList($OrderSummary->OrderUID));
		$question_sno = 0;
		foreach ($DocumentTypeNameDetails as $key => $DocTypeName) {
			if (!in_array($DocTypeName->DocumentTypeUID, $deletedDocument)) {
				$question_sno += 1;
				$data['DocTypeName'] = $DocTypeName;
				$data['OrderUID'] = $OrderSummary->OrderUID;
				$data['question_sno'] = $question_sno;
				$data['field_type'] = $DocTypeName->FieldType;
				$data['table_name'] = $DocTypeName->TableName;
				$data['table_key'] = $DocTypeName->TableKey;
				$data['ChildLabel'] = $DocTypeName->ChildLabel;
				$table .=$this->load->view('checklist/checklist', $data,TRUE);

				/*child checklist start*/
				$childchecklists = $this->Common_Model->get_childchecklists($OrderSummary->CustomerUID, $WorkflowModuleUID, $DocTypeName->DocumentTypeUID, $OrderSummary->LoanType);
				if (!empty($childchecklists)) {
					$childquestion_sno = 0;
					foreach ($childchecklists as $childchecklistkey => $childchecklistvalue) {
						if (!in_array($DocTypeName->DocumentTypeUID, $deletedDocument)) {
							$childquestion_sno += 1;
							$childata['DocTypeName'] = $childchecklistvalue;
							$childata['OrderUID'] = $OrderSummary->OrderUID;
							$childata['question_sno'] = $question_sno . '.' . $childquestion_sno;
							$table .=$this->load->view('checklist/checklist', $childata,TRUE);
						}
					}
				}
				/*child checklist ends*/
			}
		}
		$data1['OrderUID'] = $OrderSummary->OrderUID;
		$data1['question_sno'] = $question_sno;
		$data1['WorkflowUID'] = $WorkflowModuleUID;
		$table .=$this->load->view('checklist/otherchecklist', $data1,TRUE);
		$table .= '</tbody></table>';
		$table .= '<table class="addnewchecklistpree"> <tbody > <tr> <td colspan="5"> <p class="custom_add_icon pull-right addchecklistrow" data-div="addChecklist' . $question_sno . '" data-count=' . $question_sno . ' data-moduleUID=' . $WorkflowModuleUID . ' aria-hidden="true">Add New Checklist</p> </td> </tr> <tr> <td colspan="5">&nbsp;</td> </tr> </tbody> </table>';
		$table .= '</div>';
		echo $table;
	}
}
