<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerChecklistReport extends MY_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->model('CustomerChecklistReport_Model');
		$this->load->library('form_validation');
	}

	function index()
	{	
		$data['content'] = 'reports';
		//Master Setup for Reports
		$data['ReportsDetails'] = $this->CustomerChecklistReport_Model->ReportsDetails();
		// echo "<pre>"; print_r($data['ReportsDetails']); exit();
		//Customer Workflow
		$data['CustomerWorkflow'] = $this->CustomerChecklistReport_Model->GetCustomerWorkflows();

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function editreport()
	{	
		$data['content'] = 'editreport';
		//Groups Details
		$ReportUID = $this->uri->segment(3);
		$data['ReportsDetails'] = $this->CustomerChecklistReport_Model->ReportsDetails($ReportUID);
		//Groups Details
		$ReportUID = $this->uri->segment(3);
		$data['GroupsDetails'] = $this->CustomerChecklistReport_Model->GetGroupsDetails($ReportUID);
		//Customer Workflow
		$data['CustomerWorkflow'] = $this->CustomerChecklistReport_Model->GetCustomerWorkflows();
		//Standard Column
		$data['StandardColumns'] = $this->CustomerChecklistReport_Model->GetStandardColumns();
		//Checklist
		$data['Checklist'] = $this->CustomerChecklistReport_Model->Checklist();

		$data['ChecklistFields'] = $this->CustomerChecklistReport_Model->get_dynamicchecklistfields();

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function addreports() {
		$ReportUID = trim($this->input->post('ReportUID'));
		$ReportName = trim($this->input->post('ReportName'));
		$WorkflowModuleUID = trim($this->input->post('WorkflowModuleUID'));
		if (empty($ReportName)) {
			$data=array('validation_error' => 1,'message'=> 'Please Enter the Report Name!.','type'=>'danger');
		} else {
			if ($ReportUID) {
				//update report
				$data = $this->CustomerChecklistReport_Model->UpdateReports($ReportUID, $ReportName, $WorkflowModuleUID);
			} else {
				//insert report
				$data = $this->CustomerChecklistReport_Model->InsertReports($ReportName, $WorkflowModuleUID);
			}
		}		
		echo json_encode($data);
	}

	function DeleteReportDetails() {
		$ReportUID = $this->input->post('ReportUID');
		$Active = $this->input->post('Active');
		$data = $this->CustomerChecklistReport_Model->DeleteReportDetails($ReportUID,$Active);
		echo json_encode($data);
	}

	function insertreportfields() {		
		$ReportUID = $this->input->post('ReportUID');
		$GroupUID = $this->input->post('GroupUID');
		$ReportFieldUID = $this->input->post('ReportFieldUID');
		$GroupName = $this->input->post('GroupName');
		$HeaderName = $this->input->post('HeaderName');
		$IsChecklist = $this->input->post('IsChecklist');
		$ColumnName = $this->input->post('ColumnName');
		$WorkflowUID = $this->input->post('WorkflowUID');
		$OldWorkflowUID = $this->input->post('OldWorkflowUID');
		$DocumentTypeUID = $this->input->post('DocumentTypeUID');
		$OldDocumentTypeUID = $this->input->post('OldDocumentTypeUID');
		$ChecklistOption = $this->input->post('ChecklistOption');

		if ($IsChecklist == 0) {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('HeaderName', '', 'required');
			$this->form_validation->set_rules('ColumnName', '', 'required');

			if ($this->form_validation->run() == true) {
				$reportfieldsdata = array(
					'ReportUID'=>$ReportUID,
					'HeaderName'=>$HeaderName,
					'IsChecklist'=>$IsChecklist,
					'ColumnName'=>$ColumnName,
					'WorkflowUID'=>null,
					'DocumentTypeUID'=>null,
					'ChecklistOption'=>null,
					'CreatedBy'=>$this->session->userdata('UserUID')
				);
			} else{

				$Msg = $this->lang->line('Empty_Validation');

				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'HeaderName' => form_error('HeaderName'),
					'ColumnName' => form_error('ColumnName'),
					'type' => 'danger',
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data); exit();
			}			
		} else {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('HeaderName', '', 'required');
			$this->form_validation->set_rules('WorkflowUID', '', 'required');
			$this->form_validation->set_rules('DocumentTypeUID', '', 'required');
			$this->form_validation->set_rules('ChecklistOption', '', 'required');

			if ($this->form_validation->run() == true) {
				$reportfieldsdata = array(
					'ReportUID'=>$ReportUID,
					'HeaderName'=>$HeaderName,
					'IsChecklist'=>$IsChecklist,
					'ColumnName'=>null,
					'WorkflowUID'=>$WorkflowUID,
					'DocumentTypeUID'=>$DocumentTypeUID,
					'ChecklistOption'=>$ChecklistOption,
					'CreatedBy'=>$this->session->userdata('UserUID')
				);
			} else{

				$Msg = $this->lang->line('Empty_Validation');

				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'HeaderName' => form_error('HeaderName'),
					'CustomerWorkflow' => form_error('WorkflowUID'),
					'CustomerChecklist' => form_error('DocumentTypeUID'),
					'ChecklistOption' => form_error('ChecklistOption'),
					'type' => 'danger',
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data); exit();
			}
		}	

		if (!isset($GroupUID)) {
			//Insert Group and fields data
			$data = $this->CustomerChecklistReport_Model->insertGroup($GroupName,$reportfieldsdata);
			echo json_encode($data); exit();
		} elseif (isset($ReportFieldUID) && !empty($ReportFieldUID)) {					
			$reportfieldsdata['GroupUID'] = $GroupUID;
			//Update Group and fields data
			$data = $this->CustomerChecklistReport_Model->updateGroup($ReportFieldUID,$OldWorkflowUID,$OldDocumentTypeUID,$reportfieldsdata);
			echo json_encode($data); exit();
		} else {
			$reportfieldsdata['GroupUID'] = $GroupUID;
			//Update Group and fields data
			$data = $this->CustomerChecklistReport_Model->insertreportfields($reportfieldsdata);
			echo json_encode($data); exit();
		}	
	}

	function ReportFieldsPosition()
	{
		$ReportFieldUID = $this->input->post('ReportFieldUID');

		$i = 1;

		foreach ($_POST['ReportFieldUID'] as $value) {
		    $this->db->query("UPDATE `mReportFields` SET `Position`=".$i." WHERE `ReportFieldUID`= ".$value."");
		    $i++;
		}

		echo json_encode(array('error'=>0,'msg'=>'Position Updated.','type'=>'success'));
	}

	function deletereportfields() {
		$ReportFieldUID = $this->input->post('ReportFieldUID');
		$data = $this->CustomerChecklistReport_Model->deletereportfieldsdata($ReportFieldUID);
		echo json_encode($data);
	}

	function GetReportGroupDetails() {
		$ReportUID = $this->input->post('ReportUID');
		$GroupUID = $this->input->post('GroupUID');
		$data = $this->CustomerChecklistReport_Model->GetReportGroupDetails($ReportUID,$GroupUID);
		echo json_encode($data);
	}

	function UpdateGroupDetails() {
		$ReportUID = $this->input->post('ReportUID');
		$GroupUID = $this->input->post('GroupUID');
		$GroupName = $this->input->post('GroupName');
		$data = $this->CustomerChecklistReport_Model->UpdateGroupDetails($ReportUID,$GroupUID,$GroupName);
		echo json_encode($data);
	}

	function DeleteGroupDetails() {
		$ReportUID = $this->input->post('ReportUID');
		$GroupUID = $this->input->post('GroupUID');
		$this->CustomerChecklistReport_Model->DeleteGroupDetails($ReportUID,$GroupUID);
		$data=array('validation_error' => 0,'message' => 'Group and Group Details Deleted Successfully.','type'=>'success');
		echo json_encode($data);
	}
}
?>
