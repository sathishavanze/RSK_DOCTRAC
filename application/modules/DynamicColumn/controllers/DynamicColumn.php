<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class DynamicColumn extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('DynamicColumn_model');
		$this->load->library('form_validation');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['getCategory'] = $this->DynamicColumn_model->getCategory();
		// $data['getWorkFlowCategory'] = $this->DynamicColumn_model->getWorkFlowCategory();
		// $data['SectionDetails'] = ['Priority_Report'=>'Priority Report','PayOffOrders'=>'PayOff Orders','JuniorProcessorPriority_Report'=>'Junior Processor Priority Report','Funded'=>'Funded','Esclation'=>'Esclation','Dashboard'=>'Dashboard','Canceled'=>'Canceled'];
		$data['getWork'] = $this->DynamicColumn_model->getWorkflow();
		$data['WorkflowUID'] = $_GET['WorkflowUID'];
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/*  */
	function GetDocument()
	{
		
		$post['workflow'] = $this->input->post('workflow');
		// $workflow =$this->uri->segment(3);
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['column_order'] = array('HeaderName','DisplayQueues','IsChecklist','NoSort','SortColumnName','m1.UserName', 'CreatedDateTime');
		$post['column_search'] = array('HeaderName', 'IsChecklist','NoSort','SortColumnName','m1.UserName', 'CreatedDateTime');

		$DocumentDetails = $this->DynamicColumn_model->getWorkflowdetails($post);

		$QueueColumn = $this->QueueColumn();

		$wholeData = [];
		foreach ($DocumentDetails as $key => $value) {
			$row = array();
			
			$QueueColumnUID = $value->QueueColumnUID;
			$WorkflowUID = $value->WorkflowUID;
			$QueueColumname = array_search($value->SortColumnName, $QueueColumn);
			$row[] = $value->HeaderName;
			$row[] = $value->DisplayQueues;
			$row[] = '<div class="form-check"> <label class="form-check-label"> <input class="form-check-input" type="checkbox" name="IsChecklist" '.(($value->IsChecklist == 1) ? "checked" : "")  .' disabled > <span class="form-check-sign"> <span class="check"></span> </span> </label> </div>'; 
			$row[] = '<div class="form-check"> <label class="form-check-label"> <input class="form-check-input" type="checkbox" name="NoSort" '.(($value->NoSort == 1 && !empty($value->SortColumnName) || ($value->NoSort == 0) ) ? "checked" : "")  .' disabled > <span class="form-check-sign"> <span class="check"></span> </span> </label> </div>'; 
			$row[] = (($QueueColumname != false) ? $QueueColumname : "");
			$row[] = $value->CreateUser;

			$row[] = site_datetimeformat($value->CreatedDateTime);


			$row[] = '<span style="text-align: left;width:100%;"><a title="Edit" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload editcol" data-id="'.$QueueColumnUID.'" data-workflowmoduleuid="'.$WorkflowUID.'"><i class="icon-pencil"></i></a></span><a class="btn btn-link btn-info btn-just-icon btn-xs text-danger delete" title="delete" data-delete= '.$value->QueueColumnUID.'><i class="fa fa-trash" aria-hidden="true" style="cursor:pointer;"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a title="Move" class="btn btn-link btn-info btn-just-icon btn-xs icon_action move-handle-icon" style="color: #000;" data-position= '.$value->QueueColumnUID.'><i class="fa fa-arrows" aria-hidden="true"></i></a>';

			$i++;
			array_push($wholeData, $row);
		}

		$data =  array(
			'MaritalTableList' => ($wholeData),
			'post' => $post
		);

		$post = $data['post'];
		$count_all = $this->DynamicColumn_model->count_filtered($post);
		$workflow = $this->input->post('workflow');
		$get_dynamic_column_header = $this->DynamicColumn_model->getDynamicColumnName($workflow);
		$dynamic_header = $get_dynamic_column_header[0]['WorkflowModuleName'];

		//WorkflowModuleName
		$output = array(
			"draw" => $this->input->post('draw'),
			"recordsTotal" => $this->DynamicColumn_model->count_all($post),
			"recordsFiltered" => $count_all,
			"data" => $data['MaritalTableList']
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	function workflowlist($id='')
	{
		$data['content'] = 'workflowlist';

		$workflow1=$this->uri->segment(3);
		$get_dynamic_column_header = is_numeric($workflow1) ? $this->DynamicColumn_model->getDynamicColumnName($workflow1) : $workflow1;
		$dynamic_header = is_numeric($workflow1) ? $get_dynamic_column_header[0]['WorkflowModuleName'] : $workflow1;

		$data['dynamic_header'] = $dynamic_header;

		$data['WorkID'] = $workflow1; 

		$data['WorkflowUID'] = $_GET['WorkflowUID'];
		$data['ModuleDetails'] = $this->DynamicColumn_model->GetWorkflowModuleName();

		$data['Workflownames'] = $this->DynamicColumn_model->getWorkflow();
		$data['DocumentDetails'] = $this->DynamicColumn_model->GetDocumentTypeName();
		$data['SectionDetails'] =$this->DynamicColumn_model->GetSectionName();
		$data['CustomerUID'] =$this->parameters['DefaultClientUID'];
		$data['DynamicColumn'] =$this->config->item('DynamicColumn');
		$data['QueueColumn'] =$this->QueueColumn();
		$data['StaticQueue'] = $this->DynamicColumn_model->GetStaticQueue();
		//GetQueueIDs
		$data['SubQueue'] = $this->DynamicColumn_model->GetQueueIDs($workflow1);

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/**
	*Function fetch workflow queue add column row
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 07 December 2020.
	*/
	function fetch_addworkflowqueuecolumn()
	{
		$QueueColumnUID = $this->input->post('QueueColumnUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		$data['content'] = 'Dynamiccolumnmodal';
		$data['Heading'] = 'Add';
		$data['DocumentDetails'] = $this->DynamicColumn_model->get_checklistquestions($WorkflowModuleUID);
		$data['QueueColumn'] =$this->QueueColumn();
		//$data['ModuleDetails'] =$this->Common_Model->GetCustomerBasedModules();
		$data['SubQueues'] = $this->Common_Model->FetchWorkflowSubQueues($WorkflowModuleUID);
		$data['StaticQueues'] = $this->DynamicColumn_model->GetStaticQueue($WorkflowModuleUID);

		//$data['DynamicColumnDetails'] =  $this->DynamicColumn_model->getEditdetails($QueueColumnUID);
		$data = $this->load->view('Dynamiccolumnmodal',$data,true);
		echo json_encode(array('success' => 1,'data'=>$data));exit;

	}

	/**
	*Function fetch workflow queue column row by UID 
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 07 December 2020.
	*/
	function fetch_editworkflowqueuecolumn()
	{
		$QueueColumnUID = $this->input->post('QueueColumnUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$data['Heading'] = 'Edit';

		$data['content'] = 'Dynamiccolumnmodal';
		$data['DocumentDetails'] = $this->DynamicColumn_model->get_checklistquestions($WorkflowModuleUID);
		$data['QueueColumn'] =$this->QueueColumn();
		//$data['ModuleDetails'] =$this->Common_Model->GetCustomerBasedModules();
		$data['SubQueues'] = $this->Common_Model->FetchWorkflowSubQueues($WorkflowModuleUID);
		$data['StaticQueues'] = $this->DynamicColumn_model->GetStaticQueue($WorkflowModuleUID);

		$data['DynamicColumnDetails'] =  $this->DynamicColumn_model->getEditdetails($QueueColumnUID);
		$data = $this->load->view('Dynamiccolumnmodal',$data,true);
		echo json_encode(array('success' => 1,'data'=>$data));exit;

	}

	/* 
	*Function for Add Dynamic Queue Columns Page
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */
	function Adddynamiccolumn()
	{

		$data['content'] = 'adddynamiccolumn';
		$data['Workflownames'] = $this->DynamicColumn_model->getWorkflow();
		$data['DocumentDetails'] = $this->DynamicColumn_model->GetDocumentTypeName();
		$data['ModuleDetails'] =$this->DynamicColumn_model->GetWorkflowModuleName();
		$data['SectionDetails'] =$this->DynamicColumn_model->GetSectionName();
		$data['CustomerUID'] =$this->parameters['DefaultClientUID'];
		$data['DynamicColumn'] =$this->config->item('DynamicColumn');
		$data['QueueColumn'] =$this->QueueColumn();

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/* 
	*Function for Edit Dynamic Queue Columns Page
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:27.07.2020
	 */

	function Editdynamiccolumn()
	{   

		// $workflow=$_GET['WorkflowUID'];
		// $Headname =$_GET['HeaderName'];

		$workflow = $this->uri->segment(3);
		$Headname = urldecode($this->uri->segment(4));

		$data['DocumentDetails'] = $this->DynamicColumn_model->GetDocumentTypeName();
		$data['QueueColumn'] =$this->QueueColumn();
		$data['SectionDetails'] =$this->DynamicColumn_model->GetSectionName();
		$data['ModuleDetails'] =$this->DynamicColumn_model->GetWorkflowModuleName();

		$data['DynamicColumnDetails'] =  $this->DynamicColumn_model->getEditdetails($workflow,$Headname);
		$data['content'] = 'updatedynamiccolumn';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/* 
	*Function for Update Dynamic Queue Columns
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:28.07.2020
	 */

	function UpdateDynamicColumn()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') {

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('HeaderName', '', 'required');
			$this->form_validation->set_rules('ColumnName', '', 'required');
			$this->form_validation->set_rules('WorkflowUID', '', 'required');

			if ($this->form_validation->run() == TRUE) {

				// Check Header Name Already Exist
				if (!empty($this->input->post('QueueColumnUID')) && !empty($this->input->post('HeaderName')) && !empty($this->input->post('WorkflowUID'))) {
					$result = $this->DynamicColumn_model->CheckHeaderNameIsExist($this->input->post());
				} else {
					$result = '';
				}

				if ($result > 0) {
					$res = array('Status' => 1, 'message' => 'Header Name Already Exist!.', 'HeaderName' => 'Header Name Already Exist.');
					echo json_encode($res);
					exit();
				}

				$result = $this->DynamicColumn_model->UpdateDynamicqueue($this->input->post());

				if($result){

					$res = array('Status' => 2, 'message' => 'Updated Successfully');
					echo json_encode($res);
					exit();
				}
				else{
					$res = array('Status' => 0, 'message' => 'Update Failed');
					echo json_encode($res);
					exit();
				}

			} else {
				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'HeaderName' => form_error('HeaderName'),
					'ColumnName' => form_error('ColumnName'),
					'WorkflowUID' => form_error('WorkflowUID'),

					'type' => 'danger',
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

	/* 
	*Function for Save Dynamic Queue Columns
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:28.07.2020
	 */

	function SaveDynamicColumn()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('HeaderName', '', 'required');
			$this->form_validation->set_rules('ColumnName', '', 'required');

			if ($this->form_validation->run() == TRUE) {

				// Check Header Name Already Exist
				if (!empty($this->input->post('HeaderName')) && !empty($this->input->post('WorkflowUID'))) {
					$result = $this->DynamicColumn_model->CheckHeaderNameIsExist($this->input->post());
				} else {
					$result = '';
				}

				if ($result > 0) {
					$res = array('Status' => 1, 'message' => 'Header Name Already Exist!.', 'HeaderName' => 'Header Name Already Exist.');
					echo json_encode($res);
					exit();
				}

				$result = $this->DynamicColumn_model->AddDynamicqueue($this->input->post());

				if($result){

					$res = array('Status' => 2, 'message' => 'Added Successfully');
					echo json_encode($res);
					exit();
				}
				else{
					$res = array('Status' => 0, 'message' => 'Failed');
					echo json_encode($res);
					exit();
				}

			} else {
				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'HeaderName' => form_error('HeaderName'),
					'ColumnName' => form_error('ColumnName'),
					'WorkflowUID' => form_error('WorkflowUID'),
					'type' => 'danger',
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

	function getTableKey()
	{
		$DbTable = $this->input->post('table');
		$keys = $this->Common_Model->TableKeys($DbTable);
		if ($keys) {
			$option = '<option value="">Select table key...</option>';

			foreach ($keys as $key => $val) {

				$option .= '<option value="' . $val . '">' . $val . '	</option>';
			}


			echo $option;
		} else {
			return false;
		}
	}

	/* 
	*Function for Dynamic Queue Columns Position Change
	*author Sathis Kannan(sathish.kannan@avanzegroup.com)
	*since Date:29.07.2020
	*/

	function DynamicColumnPosition()
	{
		$post = $this->input->post();

		if ($this->input->post('sortData')) {
			$file = $this->input->post('sortData');
			$Position = 1;
			/* foreach start for update position */
			foreach ($file as $value) {
				if ($value['ID']) {
					$this->db->query('UPDATE mQueueColumns SET Position = ' . $Position . ' WHERE QueueColumnUID = ' . $value['ID'] .' AND CustomerUID='.$this->parameters['DefaultClientUID'] );
					$Position++;
				}
			}
			/* forach end */
			echo json_encode(array('error' => 0, 'msg' => 'Position Updated Successsfully.', 'type' => 'success'));
		} else {
			echo json_encode(array('error' => 0, 'msg' => 'Something went wrong!.', 'type' => 'danger'));
		}
	}

	/* 
     * Function for Delete Queue Column  
     *author sathis kannan(sathish.kannan@avanzegroup.com)
     *since Date:30-Jul-2020
     */
	function DeleteDynamicColumn()
	{
		$post = $this->input->post();
		$DeleteID = $this->input->post('DeleteID');

		if ($DeleteID) {
			$this->db->query('DELETE FROM mQueueColumns WHERE QueueColumnUID = ' . $DeleteID .' AND CustomerUID='.$this->parameters['DefaultClientUID'] );
		}
		if($this->db->affected_rows()>0){
			echo json_encode(array('error' => 1, 'msg' => 'Deleted Successsfully.', 'type' => 'success'));
		}else{

			echo json_encode(array('error' => 0, 'msg' => 'Something went wrong!.', 'type' => 'danger'));
		}

	}



	function QueueColumn()
	{

		$column["Loan Number"] = 'tOrders.LoanNumber';
		$column["Borrower Name"] = 'tOrderPropertyRole.BorrowerFirstName';
		$column["Processor"] =  'tOrderImport.LoanProcessor';
		$column["Milestone"] = 'mMilestone.MilestoneName';
		$column["SubStatus last changed date"] ='tOrderImport.SubStatusLastChangedDate';
		$column["LoanType"] ='tOrders.LoanType';
		$column["State"] ='tOrders.PropertyStateCode';
		$column["Loan Aging"]='tOrderImport.Aging';
		$column["Priority"] ='Order-Priority';
		$column["County"] ='tOrders.PropertyCountyName';
		$column["Lock Expiration"] = 'tOrderImport.LockExpiration';
		$column["Logic-Aging"]='Logic-Aging';
		$column["Earliest Closing date"] ='tOrderImport.EarliestClosingDate';
		$column["Closed date"] = 'tOrderImport.ClosedDate';
		$column["LE disclosure date"] ='tOrderImport.LEDisclosureDate';
		$column["Closing Disclosure Sent Date"] = 'tOrderImport.ClosingDisclosureSendDate';
		$column["DocsOutDate"] = 'tOrderImport.DocsOutDate';
		$column["SigningDate"] = 'tOrderImport.SigningDate';
		$column["SigningTime"] = 'tOrderImport.SigningTime';
		$column["SigningDateTime"] = 'tOrderImport.SigningDateTime';
		$column["QueueDate"] = 'tOrderImport.QueueDate';
		$column["Next Payment Due"] = 'tOrderImport.NextPaymentDue';
		$column["NSM Serviving Loan number"] = 'tOrderImport.NSMServicingLoanNumber';
		$column["Loan Amount"] = 'tOrders.LoanAmount';
		$column["DaysinStatus"] = 'tOrderImport.DaysinStatus';
		$column["ProcAssignDate"] = 'tOrderImport.ProcAssignDate';
		$column["FinalApprovalDate"] = 'tOrderImport.FinalApprovalDate';
		$column["Property Type"] = 'tOrderImport.PropertyType';
		$column["Occupancy Status"] = 'tOrderImport.OccupancyStatus';
		$column["Resubmittal Count"] = 'tOrderImport.ResubmittalCount';
		$column["TitleCompany"] = 'tOrderImport.TitleInsuranceCompanyName';
		$column["WelcomeCallDate"] = 'tOrderImport.WelcomeCallDate';
		$column["ApprovalCallDate"] = 'tOrderImport.ApprovalCallDate';
		$column["ConditionalApprovalDate"] = 'tOrderImport.ConditionalApprovalDate';
		$column["LastNotesDays"] = 'tOrderImport.LastNotesDays';
		$column["LastNotesDate"] = 'tOrderImport.LastNotesDate';
		$column["NewLoanDays"] = 'tOrderImport.NewLoanDays';
		$column["Aging"] = 'tOrderImport.Aging';
		$column["NewLoanDaysBucket"] = 'tOrderImport.NewLoanDaysBucket';
		$column["MP"] = 'tOrderImport.MP';
		$column["MPManager"] = 'tOrderImport.MPManager';
		$column["Funder"] = 'tOrderImport.Funder';
		$column["BwrEmail"] = 'tOrderImport.BwrEmail';
		$column["PriorLoanNumber"] = 'tOrderImport.PriorLoanNumber';
		$column["Borrower Cell Number"] = 'tOrderImport.BorrowerCellNumber';
		$column["Branch ID"] = 'tOrderImport.BranchID';
		$column["Funding Milestone Date"] = 'tOrderImport.FundingMilestoneDate';
		$column["Product Description"] = 'tOrderImport.ProductDescription';
		$column["Funding Date"] = 'tOrderImport.FundingDate';
		$column["First Payment Date"] = 'tOrderImport.FirstPaymentDate';
		$column["Title Docs"] = 'tOrderImport.TitleDocs';
		$column["CashFromBorrower"] = 'tOrderImport.CashFromBorrower';
		$column["Assets"] = 'tOrderImport.Assets';
		$column["Queue"] = 'tOrderImport.Queue';
		$column["Workflow Entry DateTime"] = 'tOrderWorkflows.EntryDateTime';
		$column["Processor Preferred Closing Date"] = 'tOrderImport.ProcessorChosenClosingDate';

		return $column;

	}

}
