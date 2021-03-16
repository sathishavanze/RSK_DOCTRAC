<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Documenttype extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Documenttype_model');
		$this->load->library('form_validation');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['getCategory'] = $this->Documenttype_model->getCategory();
		$data['GetLoanTypeDetails'] = $this->Common_Model->GetLoanTypeDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/* Data table */
	function GetDocument()
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['column_order'] = array('','DocumentTypeName', 'CategoryName', 'CustomerName', 'ScreenCode');
		$post['column_search'] = array('DocumentTypeName', 'CategoryName', 'CustomerName', 'ScreenCode');

		$DocumentDetails = $this->Documenttype_model->getDocumentTypeChecklists($post);
		//print_r($DocumentDetails);exit;
		if (($this->input->post('formData[WorkflowModuleUID]') == 'All' || $this->input->post('formData[WorkflowModuleUID]') == '') && ($this->input->post('formData[Category]') == 'All' || $this->input->post('formData[Category]') == '')) {
			$position = 'display:none;';
		} else {
			$workflowID = ($this->input->post('formData[WorkflowModuleUID]') != 'All') ? $this->input->post('formData[WorkflowModuleUID]') : ($this->input->post('formData[Category]'));
		}
		$i = 1;
		$wholeData = [];
		foreach ($DocumentDetails as $key => $value) {
			$row = array();
			$row[] = $i;
			$row[] = ($value->DocumentTypeName);
			$row[] = ($value->CategoryName);
			$row[] = $value->CustomerName;
			$row[] = $value->ScreenCode;
			$row[] = $value->Groups;
			$active = ($value->Active == 1) ? "checked" : '';

			$row[] = '<div class="togglebutton"><label class="label-color"><input type="checkbox" id="Active" data-DocumentTypeUID="'.$value->DocumentTypeUID.'" name="Active" class="Active" ' . $active . '><span class="toggle"></span></label></div>';
			$row[] = '<span style="text-align: left;width:100%;"><a href="' . base_url('Documenttype/EditDocument/' . $value->DocumentTypeUID) . '" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a></span>';
			
			array_push($wholeData, $row);
			$childData=$this->Documenttype_model->getDocumentPositionChildChecklist1($value->CategoryUID,$value->DocumentTypeUID);
			if($childData){
				$child=1;
				foreach($childData as $keychild=>$valueChild){
					$row = array();
					$row[] = $i.'.'.$child;
					$row[] = ($valueChild->DocumentTypeName);
					$row[] = ($valueChild->CategoryName);
					$row[] = $valueChild->CustomerName;
					$row[] = $valueChild->ScreenCode;
					$row[] = $valueChild->Groups;
					$active = ($valueChild->Active == 1) ? "checked" : '';
		
					$row[] = '<div class="togglebutton"><label class="label-color"><input type="checkbox" id="Active" name="Active" data-DocumenttypeUID="'.$valueChild->DocumentTypeUID.'" class="Active" ' . $active . '><span class="toggle"></span></label></div>';
					$row[] = '<span style="text-align: left;width:100%;"><a href="' . base_url('Documenttype/EditDocument/' . $valueChild->DocumentTypeUID) . '" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a></span>';
					$child++;
					array_push($wholeData, $row);
				}
			}
			$i++;
		}

		$data =  array(
			'MaritalTableList' => ($wholeData),
			'post' => $post
		);

		$post = $data['post'];
		$count_all = $this->Documenttype_model->count_filtered($post);
		//print_r($count_all);
		$output = array(
			"draw" => $this->input->post('draw'),
			"recordsTotal" => $this->Documenttype_model->count_all(),
			"recordsFiltered" => $count_all,
			"data" => $data['MaritalTableList'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}
	/* 
	* Function for Document Position change 
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 31-07-2020
	*/
	function DocumentPosition()
	{
		$data['content'] = 'documentposition';
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['getCategory'] = $this->Documenttype_model->getCategory();
		$data['GetLoanTypeDetails'] = $this->Common_Model->GetLoanTypeDetails();
		$data['DocumentDetails'] = $this->Documenttype_model->getDocumentPositionChecklist($this->uri->segment(3),base64_decode($this->uri->segment(4)));
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
		/* 
	* Function for Document Position change 
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 31-07-2020
	*/
	function ChecklistPosition()
	{
		$data['content'] = 'checklistposition';
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$data['getCategory'] = $this->Documenttype_model->getCategory();
		$data['GetLoanTypeDetails'] = $this->Common_Model->GetLoanTypeDetails();
		$data['DocumentDetails'] = $this->Documenttype_model->getDocumentPositionChecklist($this->uri->segment(3),base64_decode($this->uri->segment(4)));
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/* Data table */
	function GetDocumentPosition()
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['column_order'] = array('DocumentTypeName', 'Groups');
		$post['column_search'] = array('DocumentTypeName', 'Groups');

		$DocumentDetails = $this->Documenttype_model->getDocumentTypeChecklist($post);
		$position = (($this->input->post('formData[WorkflowModuleUID]') == 'All' || $this->input->post('formData[WorkflowModuleUID]') == '') && ($this->input->post('formData[Category]') == 'All' || $this->input->post('formData[Category]') == '')) ? 'display:none;' : '';

		$i = 1;
		$wholeData = [];
		foreach ($DocumentDetails as $key => $value) {
			$row = array();
			$row[] = ($value->DocumentTypeName);
			$row[] = ($value->Groups);
			$row[] = '<span title="Move" class="icon_action move-handle-icon" style="color: #000;" data-position="' . $value->DocumentTypeUID . '"><i class="fa fa-arrows" aria-hidden="true"></i></span>';
			array_push($wholeData, $row);
		}

		$data =  array(
			'MaritalTableList' => ($wholeData),
			'post' => $post
		);

		$post = $data['post'];
		$count_all = $this->Documenttype_model->count_filtered($post);
		//print_r($count_all);
		$output = array(
			"draw" => $this->input->post('draw'),
			"recordsTotal" => $this->Documenttype_model->count_all(),
			"recordsFiltered" => $count_all,
			"data" => $data['MaritalTableList'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	/* 
	* Function for change position 
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 31-07-2020
	*/
	function documentChecklistPosition()
	{
		$post = $this->input->post();
		if ($this->input->post('sortData')) {
			$file = $this->input->post('sortData');
			$CustomerUID = $this->input->post('CustomerUID');
			$OrderUID = $this->input->post('OrderUID');
			$Position = 1;
			/* foreach start for update position */
			foreach ($file as $value) {
				if ($value['ID']) {
					$this->db->query('UPDATE mDocumentType SET Position = ' . $Position . ' WHERE DocumentTypeUID = ' . $value['ID'] . ' and CustomerUID = ' . $this->parameters['DefaultClientUID']);
					$Position++;
				}
			}
			/* forach end */
			echo json_encode(array('error' => 0, 'msg' => 'Position Updated.', 'type' => 'success'));
		} else {
			echo json_encode(array('error' => 0, 'msg' => 'Something went wrong!.', 'type' => 'danger'));
		}
	}

	function Adddocument()
	{

		$data['content'] = 'adddocument';
		$data['Roles'] = $this->Common_Model->GetCategory();
		$data['EmailTemplate'] = $this->Common_Model->get('mEmailTemplate');

		$dbname = $this->db->database;
		$tables = $this->Common_Model->DbTables();
		$option = '<select name="TableName" class="select2picker form-control" id="table_name">
					<option value="">Select table name...</option>';
		$key_name = 'Tables_in_' . $dbname;
		foreach ($tables as $key => $val) {
			$option .= '<option value="' . $val[$key_name] . '">' . $val[$key_name] . '	</option>';
		}
		$option .= '</select>';
		$data['DbTables'] = $option;
		$data['Customer'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID' => 'ASC'], []);
		$data['parentchecklists'] = $this->Documenttype_model->getchild_checklists($DocumentTypeUID, 'add');
		$data['GetLoanTypeDetails'] = $this->Common_Model->GetLoanTypeDetails();

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function appendChildChecklist()
	{
		$count = $this->input->post('count');
		$parentchecklists = $this->Documenttype_model->getchild_checklists($DocumentTypeUID, 'add');

		$select = '<div class="icon_minus_td add_icon"><select name="childchecklist[' . $count . ']" class="select2picker form-control" id="ParentDocumentTypeUID">
		<option value=""> Select Child Checklist </option>';
		foreach ($parentchecklists as $parentchecklist) {
			$select .= '<option value="' . $parentchecklist->DocumentTypeUID . '">' . $parentchecklist->DocumentTypeName . '</option>';
		}
		$select .= '</select> <a style="width:8%;float:right;"><i class="fa fa-minus-circle removechecklist pull-right" aria-hidden="true" style="font-size: 20px;margin-top: 10px;"></i></a></div>';
		echo $select;
	}

	function EditDocument()
	{
		$DocumentTypeUID = $this->uri->segment(3);
		$data['DocumentDetails'] = $this->db->select("*")->from("mDocumentType")->where(array('DocumentTypeUID' => $this->uri->segment(3)))->get()->row();

		$data['content'] = 'updatedocument';
		$data['Category'] = $this->Common_Model->GetCategory();
		$data['getparent_checklists'] = $this->Documenttype_model->getparent_checklists();
		$data['Customer'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID' => 'ASC'], []);
		$data['getChildChecklist'] = $this->Documenttype_model->getChildChecklist($DocumentTypeUID);
		$data['EmailTemplate'] = $this->Common_Model->get('mEmailTemplate');
		$data['getAlertMessage'] = $this->Documenttype_model->getAlertMessage($DocumentTypeUID);
		$data['DocumentCheckList'] = $this->Documenttype_model->DocumentCheckList();

		$dbname = $this->db->database;
		$tables = $this->Common_Model->DbTables();
		$option = '<select name="TableName" class="select2picker form-control" id="table_name">
					<option value="">Select table name...</option>';
		$key_name = 'Tables_in_' . $dbname;

		foreach ($tables as $key => $val) {
			if ($data['DocumentDetails']->TableName == $val[$key_name]) {
				$option .= '<option value="' . $val[$key_name] . '" selected>' . $val[$key_name] . ' </option>';
			} else {
				$option .= '<option value="' . $val[$key_name] . '" >' . $val[$key_name] . '	</option>';
			}
		}
		$option .= '</select>';

		$data['DbTables'] = $option;

		$keyoption = '<option value="">Select table key...</option>';
		if (isset($data['DocumentDetails']->TableName)) {
			$tKeys = $this->Common_Model->TableKeys($data['DocumentDetails']->TableName);
			if ($tKeys) {
				foreach ($tKeys as $key => $val) {
					if (isset($data['DocumentDetails']->TableKey) && $data['DocumentDetails']->TableKey == $val) {
						$keyoption .= '<option value="' . $val . '" selected>' . $val . '</option>';
					} else {
						$keyoption .= '<option value="' . $val . '" >' . $val . '	</option>';
					}
				}
			}
		}

		$data['DbTableKeys'] = $keyoption;
		if ($data['DocumentDetails']->ParentDocumentTypeUID) {
			$parentId = $data['DocumentDetails']->ParentDocumentTypeUID;
			$CheckLabel = $this->Common_Model->get('mDocumentType', ['DocumentTypeUID' => $parentId])[0];
			$tn = $CheckLabel->TableName;
			$tk = $CheckLabel->TableKey;

			if ($tn) {
				$labels = $this->Common_Model->TableKeys($tn);
			}

			$childLabel = '<option value="">Select child label</option>';
			if ($labels) {
				foreach ($labels as $key => $value) {
					if ($value != $tk) {
						if ($data['DocumentDetails']->ChildLabel == $value) {
							$childLabel .= '<option value="' . $value . '" selected>' . $value . '</option>';
						} else {
							$childLabel .= '<option value="' . $value . '" >' . $value . '</option>';
						}
					}
				}
			}
			$data['childLabel'] = $childLabel;
		}
		$data['GetLoanTypeDetails'] = $this->Common_Model->GetLoanTypeDetails();

		$data['parentchecklists'] = $this->Documenttype_model->getchild_checklists($DocumentTypeUID, 'edit');

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveDocument()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('DocumentTypeName', '', 'required');
			$this->form_validation->set_rules('CategoryUID', '', 'required');
			$this->form_validation->set_rules('CustomerUID', '', 'required');
			// $this->form_validation->set_rules('FieldType', '', 'required');
			// $this->form_validation->set_rules('ToMails', '', 'required');
			// $this->form_validation->set_rules('EmailTemplate', '', 'required');
			if ($this->input->post('FieldType') == 'select') {
				$this->form_validation->set_rules('TableKey', 'Table key', 'required');
				$this->form_validation->set_rules('TableName', 'Table name', 'required');
			}
			/*$this->form_validation->set_rules('NamingConventions', '', 'required');*/

			if ($this->form_validation->run() == true) {
				// Check milestone already exist or not
				if ($this->input->post('DocumentTypeUID')) {
					$result = '';
				} else {
					$result = $this->Documenttype_model->CheckDocumentTypeExist($this->input->post());
				}
				if ($result) {
					$res = array('Status' => 1, 'message' => 'Document Type Name Already Exist!.', 'DocumentTypeName' => 'Document Type Name Already Exist.');
					echo json_encode($res);
					exit();
				}
				if ($this->Documenttype_model->GetDocumentType($this->input->post()) == 1) {
					if ($this->input->post('DocumentTypeUID')) {
						$res = array('Status' => 2, 'message' => 'Updated Successsfully');
						echo json_encode($res);
						exit();
					} else {
						$res = array('Status' => 0, 'message' => 'Added Successsfully');
						echo json_encode($res);
						exit();
					}
				} else {
					$res = array('Status' => 2, 'message' => 'Updated Successsfully');
					echo json_encode($res);
					exit();
				}
			} else {
				$Msg = $this->lang->line('Empty_Validation');
				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'DocumentTypeName' => form_error('DocumentTypeName'),
					'CategoryUID' => form_error('CategoryUID'),
					'CustomerUID' => form_error('CustomerUID'),
					'NamingConventions' => form_error('NamingConventions'),
					// 'FieldType' => form_error('FieldType'),
					// 'ToMails' => form_error('ToMails'),
					// 'EmailTemplate' => form_error('EmailTemplate'),
					'TableKey' => form_error('TableKey'),
					'TableName' => form_error('TableName'),
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
	/* 
	* Function remove document child checklist
	* @author vishnupriya<vishnupriya.a@avanzegroup.com>
	* @since Date : 27-07-2020
	 */
	function removeChildChecklist()
	{
		$DocumentTypeUID = $this->input->post('DocumentTypeUID');
		$removeData = $this->Documenttype_model->removeParent($DocumentTypeUID);
		echo $removeData;
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
	function CheckLabel()
	{
		$parentId = $this->input->post('parentId');
		$CheckLabel = $this->Common_Model->get('mDocumentType', ['DocumentTypeUID' => $parentId])[0];
		$tn = $CheckLabel->TableName;
		$tk = $CheckLabel->TableKey;

		if ($tn) {
			$labels = $this->Common_Model->TableKeys($tn);
		}

		$options = '<option value="">Select child label</option>';
		if ($labels) {
			foreach ($labels as $key => $value) {
				if ($value != $tk) {
					$options .= '<option value="' . $value . '">' . $value . '</option>';
				}
			}
		}
		$data['labels'] = $options;
		$data['Ptname'] = $tn;
		$data['Ptkey'] = $tk;

		echo json_encode($data);
	}
	function CheckChildValue()
	{
		$UID = $this->input->post('uid');
		$val = $this->input->post('val');
		$parent_table = $this->Common_Model->get('mDocumentType', ['DocumentTypeUID' => $UID])[0];
		if ($parent_table->TableName != '' && $parent_table->TableKey != '') {
			$childs = $this->Common_Model->get('mDocumentType', ['ParentDocumentTypeUID' => $UID]);
			$answers = array();
			foreach ($childs as $key => $value) {
				if ($value->ChildLabel != '') {
					$key = $value->ChildLabel;
					$ans = $this->db->select($key)->from($parent_table->TableName)->where($parent_table->TableKey, $val)->get()->row();
					$answers[$key] = $ans->$key;
				}
			}
			echo json_encode($answers);
		} else {
			return false;
		}
		// $get_values = $this->Common_Model->getChildValues($UID);


	}

		/* 
	* function for add Comments ui
	* @author vishnupriya.a<vishnupriya.a@avanzegroup.com>
	* @since Date 27-07-202
	 */
	function appendCommentsDate()
	{
		$count = $this->input->post('count');
		$select = '<div class="row removecomment">
		<div class="col-md-4">
			<div class="form-group bmd-form-group">
				<label for="comments" class="bmd-label-floating">Comments</label>
				<textarea type="text" class="form-control" id="Commands" name="Alert[' . $count . '][Comments]" rows="1"></textarea>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group bmd-form-group">
				<label for="startDate" class="bmd-label-floating">Start Date</label>
				<input type="text" title="Document Date" name="Alert[' . $count . '][startDate]" class="form-control checklistdatepicker startDate' . $count . '">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group bmd-form-group">
				<label for="endDate" class="bmd-label-floating">End Date</label>
				<input type="text" class="form-control checklistdatepicker endDate" data-date="' . $count . '" id="endDate" name="Alert[' . $count . '][endDate]" />
			</div>
		</div>
		<div class="col-md-1">
			<a style="width:8%;float:right;"><i class="fa fa-minus-circle removeComments pull-right" aria-hidden="true" style="font-size: 20px;margin-top: 10px;"></i></a>
		</div>
	</div>';
		echo $select;
	}

	/* 
	* Function for remove Alter message
	* @author vishnupriya.a<vishnupriya.a@avanzegroup.com>
	* @since Date 27-07-202
	 */
	function removeAlertMessage()
	{
		$removealert = $this->Documenttype_model->removeChecklistMessage($this->input->post('AlertUID'));
		echo $removealert;
	}

	/* 
	* Function for update alert message
	* @author vishnupriya.a<vishnupriya.a@avanzegroup.com>
	* @since Date 27-07-202
	 */
	function updateAlertMessage()
	{
		$updateAlert = $this->Documenttype_model->updateAlert($this->input->post());
		echo $updateAlert;
	}
	/* 
	* Function for update Active and inactive status
	* @author vishnupriya.a<vishnupriya.a@avanzegroup.com>
	* @since Date 15-08-202
	 */
	function UpdateStatus()
	{
		$UpdateActiveStatus = $this->Documenttype_model->UpdateActiveStatus($this->input->post());
		echo $UpdateActiveStatus;
	}

		/* 
	* Function for change position 
	* @author Vishnupriya <vishnupriya.a@avanzegroup.com> 
	* @since Date : 31-07-2020
	*/
	function documentChecklistPosition1()
	{ 
		$positionData=json_decode($_POST['data'],true);
		$Position = 1;
		foreach($positionData as $positionKey => $positionValue){
			$parentUID=explode('~',$positionValue['id']);
			$this->db->query('UPDATE mDocumentType SET Position = ' . $Position . ',ParentDocumentTypeUID = "" WHERE DocumentTypeUID = ' . $parentUID[1] . ' and CustomerUID = ' . $this->parameters['DefaultClientUID']);
			/* update chilld checklist */
			if($positionValue['children']){
				$childChecklist=$positionValue['children'];
				foreach($childChecklist as $childKey => $childValue){
					$Position++;
					$childUID=explode('~',$childValue['id']);
					$this->db->query('UPDATE mDocumentType SET Position = ' . $Position . ',ParentDocumentTypeUID = ' . $parentUID[1] . ' WHERE DocumentTypeUID = ' . $childUID[1] . ' and CustomerUID = ' . $this->parameters['DefaultClientUID']);
				}
			}
			$Position++;
		}
		echo json_encode(array('error' => 0, 'msg' => 'Position Updated.', 'type' => 'success'));
		exit;
	}
}
