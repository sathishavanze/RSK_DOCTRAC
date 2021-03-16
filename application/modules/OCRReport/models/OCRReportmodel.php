<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class OCRReportmodel extends MY_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	function Get_OCRReportsInfo($status) { 

		$HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];

		$this->db->select('tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,mCustomer.CustomerEmail,mCustomer.CustomerName,tOrderImport.InsuranceCompany,tOrderImport.PolicyNumber,tDocuments.DocumentUID,tDocuments.DocumentName,tDocuments.DocumentURL,tDocuments.IsStacking,tDocuments.UploadedDateTime');
		$this->db->from('tOrders');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID=tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID="'.$HoiWorkflowModuleUID.'"');
		$this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID', 'left');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID');
		$this->db->join('tDocuments','tDocuments.OrderUID=tOrders.OrderUID');
		if($status == 'all'){
			$this->db->where(['tOrders.IsOCREnabled'=>'2']);
		}else if($status == 'pending'){
			$this->db->where(['tOrders.IsOCREnabled'=>'2','tDocuments.IsStacking'=>'1']);
		}else if($status == 'success'){
			$this->db->where(['tOrders.IsOCREnabled'=>'2','tDocuments.IsStacking'=>'2']);
		}else{
			$this->db->where(['tOrders.IsOCREnabled'=>'2','tDocuments.IsStacking'=>'4']);
		}
		$this->db->group_by(['tOrders.OrderUID', 'tOrders.OrderNumber']);
				
		$tOrders = $this->db->get();
		if($tOrders->num_rows() > 0){
			$array = $tOrders->result_array();
		}else{
			$array = [];
		} 
		return $array;

	}


	function selecteOptionQuery($post)
	{
		$HoiWorkflowModuleUID = $this->config->item('Workflows')['HOI'];
		$status = $post['advancedsearch']['ReportStatus'];
		$this->db->select('tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,mCustomer.CustomerEmail,mCustomer.CustomerName,tOrderImport.InsuranceCompany,tOrderImport.PolicyNumber,tDocuments.DocumentUID,tDocuments.DocumentName,tDocuments.DocumentURL,tDocuments.IsStacking,tDocuments.UploadedDateTime');
		$this->db->from('tOrders');
		$this->db->join('tOrderWorkflows', 'tOrderWorkflows.OrderUID=tOrders.OrderUID AND tOrderWorkflows.WorkflowModuleUID="'.$HoiWorkflowModuleUID.'"');
		$this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID', 'left');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID');
		$this->db->join('tDocuments','tDocuments.OrderUID=tOrders.OrderUID');
		if($status == 'all'){
			$this->db->where(['tOrders.IsOCREnabled'=>'2']);
		}else if($status == 'pending'){
			$this->db->where(['tOrders.IsOCREnabled'=>'2','tDocuments.IsStacking'=>'1']);
		}else if($status == 'success'){
			$this->db->where(['tOrders.IsOCREnabled'=>'2','tDocuments.IsStacking'=>'2']);
		}else{
			$this->db->where(['tOrders.IsOCREnabled'=>'2','tDocuments.IsStacking'=>'4']);
		}

	}

	function count_filtered($post)
	{
		if (!empty($post['search_value']) || (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']]))) {
			$this->selecteOptionQuery($post);
		}
		
    // Datatable Search
		$this->Common_Model->Datatable_Search_having($post);
    // Datatable OrderBy
		$this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
		$query = $this->db->get();
		return $query->num_rows();
	}

	function count_all($post)
	{
		$this->db->select("1");
		$query = $this->db->count_all_results();
		return $query;
	}

	
}
?>
