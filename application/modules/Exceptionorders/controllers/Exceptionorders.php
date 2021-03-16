<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Exceptionorders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Exceptionordersmodel');

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function exceptionorders_ajax_list()
	{
	    //Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		//Advanced Search
		//get_post_input_data
		$Status = $this->input->post('Status');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
    	//get_post_input_data
    	//column order
		$post['column_order'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mInputDocType.DocTypeName','tOrders.LoanNumber', 'mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode',  'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
		$post['column_search'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mInputDocType.DocTypeName','tOrders.LoanNumber', 'mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode',  'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
        //column order
		$list = $this->Exceptionordersmodel->ExceptionOrders($post,$Status);

		$no = $post['start'];
		$exceptionorderslist = [];
		foreach ($list as $exceptionorders)
		{
        	// Generate Exception Remarks for Orders.
			$ExceptionRemarks = "";

			if($Status == 'indexing')
			{
				$this->db->select('ExceptionRemarks');
				$this->db->from('tOrderException');
				$this->db->where('OrderUID', $exceptionorders->OrderUID);
				$this->db->where('ExceptionTypeUID', 1);
				$exep_remarks_result =  $this->db->get()->result();
				if(!empty($exep_remarks_result)){
					foreach($exep_remarks_result as $key => $value){
						if(!empty($value->ExceptionRemarks)){
							$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
						}
					}
					$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
				}
			}
			else if($Status == 'fatal')
			{
				$this->db->select('ExceptionRemarks');
				$this->db->from('tOrderException');
				$this->db->where('OrderUID', $exceptionorders->OrderUID);
				$this->db->where('ExceptionTypeUID', 2);
				$exep_remarks_result =  $this->db->get()->result();
				if(!empty($exep_remarks_result)){
					foreach($exep_remarks_result as $key => $value){
						if(!empty($value->ExceptionRemarks)){
							$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
						}
					}
					$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
				}

			}
			else if($Status == 'nonfatal')
			{
				$this->db->select('ExceptionRemarks');
				$this->db->from('tOrderException');
				$this->db->where('OrderUID', $exceptionorders->OrderUID);
				$this->db->where('ExceptionTypeUID', 3);
				$exep_remarks_result =  $this->db->get()->result();
				if(!empty($exep_remarks_result)){
					foreach($exep_remarks_result as $key => $value){
						if(!empty($value->ExceptionRemarks)){
							$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
						}
					}
					$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
				}
			}

			$row = array();
			$row[] = $exceptionorders->OrderNumber;
			$row[] = $exceptionorders->PackageNumber;
			$row[] = $exceptionorders->CustomerName;
			$row[] = $exceptionorders->ProductName;
			$row[] = $exceptionorders->ProjectName;
			$row[] = $exceptionorders->DocTypeName;
			$row[] = $exceptionorders->LoanNumber;
			$row[] = '<a href="javascript:void(0)" style=" background: '.$exceptionorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$exceptionorders->StatusName.'</a>';
			$row[] = $exceptionorders->PropertyAddress1.' '.$exceptionorders->PropertyAddress2;
			$row[] = $exceptionorders->PropertyCityName;
			$row[] = $exceptionorders->PropertyStateCode;
			$row[] = $exceptionorders->PropertyZipCode;
			$row[] = date('m/d/Y H:i:s', strtotime($exceptionorders->OrderEntryDateTime));
			$row[] = date('m/d/Y H:i:s', strtotime($exceptionorders->OrderDueDate));
			$row[] = $ExceptionRemarks;

			if($this->loggedid == $exceptionorders->AssignedToUserUID)
			{
				$Action = '<a href="'.base_url('Ordersummary/index/'.$exceptionorders->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>';
			}else{

				$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs exceptionPickNewOrder" data-orderuid="'.$exceptionorders->OrderUID.'" data-projectuid="'.$exceptionorders->ProjectUID.'"><i class="icon-pencil"></i></button>';
			}
			$row[] = $Action;
			$exceptionorderslist[] = $row;
		}



		$data =  array(
			'exceptionorderslist' => $exceptionorderslist,
			'post' => $post
		);



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Exceptionordersmodel->count_all($Status),
			"recordsFiltered" =>  $this->Exceptionordersmodel->count_filtered($post,$Status),
			"data" => $data['exceptionorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function exceptionordersworkinprogress_ajax_list()
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		//Advanced Search

		//get_post_input_data
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
    	//get_post_input_data
		$Status = '';
    	//column order
		$post['column_order'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mInputDocType.DocTypeName', 'tOrders.LoanNumber','mUsers.UserName', 'mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode', 'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
		$post['column_search'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mInputDocType.DocTypeName','tOrders.LoanNumber', 'mUsers.UserName', 'mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode',  'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
        //column order
		$list = $this->Exceptionordersmodel->WorkinprogressExceptionOrders($post);

		$no = $post['start'];
		$exceptionorderslist = [];
		foreach ($list as $expworkinprogress)
		{


        	// Generate Exception Remarks for Orders.
			$ExceptionRemarks = "";

			$this->db->select('ExceptionRemarks');
			$this->db->from('tOrderException');
			$this->db->where('OrderUID', $expworkinprogress->OrderUID);
			$exep_remarks_result =  $this->db->get()->result();
			if(!empty($exep_remarks_result)){
				foreach($exep_remarks_result as $key => $value){
					if(!empty($value->ExceptionRemarks)){
						$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
					}
				}
				$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
			}


			$row = array();
			$row[] = $expworkinprogress->OrderNumber;
			$row[] = $expworkinprogress->PackageNumber;
			$row[] = $expworkinprogress->CustomerName;
			$row[] = $expworkinprogress->ProductName;
			$row[] = $expworkinprogress->ProjectName;
			$row[] = $expworkinprogress->DocTypeName;
			$row[] = $expworkinprogress->LoanNumber;
			$row[] = $expworkinprogress->AssignedUserName;
			$row[] = '<a href="javascript:void(0)" style=" background: '.$expworkinprogress->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$expworkinprogress->StatusName.'</a>';
			$row[] = $expworkinprogress->PropertyAddress1.' '.$expworkinprogress->PropertyAddress2;
			$row[] = $expworkinprogress->PropertyCityName;
			$row[] = $expworkinprogress->PropertyStateCode;
			$row[] = $expworkinprogress->PropertyZipCode;
			$row[] = date('m/d/Y H:i:s', strtotime($expworkinprogress->OrderEntryDateTime));
			$row[] = date('m/d/Y H:i:s', strtotime($expworkinprogress->OrderDueDate));
			$row[] = $ExceptionRemarks;

			$Action = '<a href="'.base_url('Ordersummary/index/'.$expworkinprogress->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>';
			$row[] = $Action;
			$exceptionorderslist[] = $row;
		}
		$data =  array(
			'exceptionorderslist' => $exceptionorderslist,
			'post' => $post
		);



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Exceptionordersmodel->in_progress_count_all(),
			"recordsFiltered" =>  $this->Exceptionordersmodel->in_progress_count_filtered($post),
			"data" => $data['exceptionorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function PickExistingOrderCheck()
	{
		$OrderUID = $this->input->post('OrderUID');
		$ProjectUID = $this->input->post('ProjectUID');
		$result = $this->Exceptionordersmodel->PickExistingOrderCheck($OrderUID);
		if($result['Status'] == 1)
		{
			$res = array('validation_error' => 1,'message'=>'Exception Order was picked by ' .$result['UserName'],'color'=>'danger');
			echo json_encode($res);exit();
		}
		else
		{
			$value = $this->Exceptionordersmodel->OrderAssign($OrderUID,$ProjectUID);
			if($value)
			{
				$val = array('validation_error' => 2,'message'=>'Exception Order Assigned','color'=>'success');
				echo json_encode($val);exit();
			}
		}
	}

	function WriteExcel()
	{
		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
			// $this->Exceptionordersmodel->GetExceptionOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
		$list = $this->Exceptionordersmodel->GetExceptionOrdersExcelRecords($post);
			
		if($post['advancedsearch']['Status'] == 'workinprogress')
		{
			$data = [];

			$data[] = array('Prop No','Pack No','Client','Product Name','Project Name','Doc Type','Loan No','AssignedUserName','Current Status','Property Address','Property City','Property State','Zip Code','OrderEntryDateTime','OrderDueDateTime', 'Exception Remarks');
			for ($i=0; $i < sizeof($list); $i++) { 

				// Generate Exception Remarks for Orders.
				$ExceptionRemarks = "";

				$this->db->select('ExceptionRemarks');
				$this->db->from('tOrderException');
				$this->db->where('OrderUID', $list[$i]->OrderUID);
				$exep_remarks_result =  $this->db->get()->result();
				if(!empty($exep_remarks_result)){
					foreach($exep_remarks_result as $key => $value){
						if(!empty($value->ExceptionRemarks)){
							$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
						}
					}
					$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
				}

				$data[] = array($list[$i]->OrderNumber,$list[$i]->PackageNumber,$list[$i]->CustomerName,$list[$i]->ProductName,$list[$i]->ProjectName,$list[$i]->DocTypeName,$list[$i]->LoanNumber,$list[$i]->UserName,$list[$i]->StatusName,$list[$i]->PropertyAddress1.$list[$i]->PropertyAddress2,$list[$i]->PropertyCityName,$list[$i]->PropertyStateCode,$list[$i]->PropertyZipCode,$list[$i]->OrderEntryDateTime,$list[$i]->OrderDueDate, $ExceptionRemarks);
				
			}

			$this->outputCSV($data);
		}
		else{
			$data = [];

			$data[] = array('Prop No','Pack No','Client','Product Name','Project Name','Doc Type','Loan No','Current Status','Property Address','Property City','Property State','Zip Code','OrderEntryDateTime','OrderDueDateTime','Exception Remarks');
			for ($i=0; $i < sizeof($list); $i++) { 

				// Generate Exception Remarks for Orders.
				$ExceptionRemarks = "";


				if($post['advancedsearch']['Status'] == 'indexing')
				{
					$this->db->select('ExceptionRemarks');
					$this->db->from('tOrderException');
					$this->db->where('OrderUID', $list[$i]->OrderUID);
					$this->db->where('ExceptionTypeUID', 1);
					$exep_remarks_result =  $this->db->get()->result();
					if(!empty($exep_remarks_result)){
						foreach($exep_remarks_result as $key => $value){
							if(!empty($value->ExceptionRemarks)){
								$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
							}
						}
						$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
					}
				}
				else if($post['advancedsearch']['Status'] == 'fatal')
				{
					$this->db->select('ExceptionRemarks');
					$this->db->from('tOrderException');
					$this->db->where('OrderUID', $list[$i]->OrderUID);
					$this->db->where('ExceptionTypeUID', 2);
					$exep_remarks_result =  $this->db->get()->result();
					if(!empty($exep_remarks_result)){
						foreach($exep_remarks_result as $key => $value){
							if(!empty($value->ExceptionRemarks)){
								$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
							}
						}
						$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
					}

				}
				else if($post['advancedsearch']['Status'] == 'nonfatal')
				{
					$this->db->select('ExceptionRemarks');
					$this->db->from('tOrderException');
					$this->db->where('OrderUID', $list[$i]->OrderUID);
					$this->db->where('ExceptionTypeUID', 3);
					$exep_remarks_result =  $this->db->get()->result();
					if(!empty($exep_remarks_result)){
						foreach($exep_remarks_result as $key => $value){
							if(!empty($value->ExceptionRemarks)){
								$ExceptionRemarks .= $value->ExceptionRemarks . ', ';
							}
						}
						$ExceptionRemarks = rtrim($ExceptionRemarks, ', ');
					}
				}
				$data[] = array($list[$i]->OrderNumber,$list[$i]->PackageNumber,$list[$i]->CustomerName,$list[$i]->ProductName,$list[$i]->ProjectName,$list[$i]->DocTypeName,$list[$i]->LoanNumber,$list[$i]->StatusName,$list[$i]->PropertyAddress1.$list[$i]->PropertyAddress2,$list[$i]->PropertyCityName,$list[$i]->PropertyStateCode,$list[$i]->PropertyZipCode,$list[$i]->OrderEntryDateTime,$list[$i]->OrderDueDate, $ExceptionRemarks);
				
			}

			$this->outputCSV($data);
		}
	}

	function outputCSV($data) 
	{
		ob_clean();
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=file.csv");
		$output = fopen("php://output", "w");
		foreach ($data as $row)
		{
		    fputcsv($output, $row); // here you can change delimiter/enclosure
		}
		fclose($output);
		ob_flush();
	}


}?>
