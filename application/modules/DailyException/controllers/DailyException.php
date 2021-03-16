<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class DailyException extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DailyExceptionmodel');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function FetchDataException()
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
		$post['column_order'] = array('tOrderDocumentCheckIn.AgentNo','tOrders.OrderNumber','tOrderPropertyRole.BorrowerFirstName','mUsers.UserName','tOrders.LoanNumber','mProjectCustomer.ProjectName', 'tOrderException.ExceptionRaisedDateTime',  'tOrders.OrderUID','mStatus.StatusName','mStatus.StatusColor','mCustomer.CustomerName','mProjectCustomer.ProjectUID','mProducts.ProductName','tOrders.OrderEntryByUserUID','tOrders.PropertyStateCode','tOrders.DocType','tOrders.PropertyCountyName','mExceptions.ExceptionName','tOrderException.ExceptionRemarks','tOrderException.ExceptionRaisedDateTime');
		$post['column_search'] = array('tOrderDocumentCheckIn.AgentNo','tOrders.OrderNumber','tOrderPropertyRole.BorrowerFirstName','mUsers.UserName','tOrders.LoanNumber','mProjectCustomer.ProjectName', 'tOrderException.ExceptionRaisedDateTime',  'tOrders.OrderUID','mStatus.StatusName','mStatus.StatusColor','mCustomer.CustomerName','mProjectCustomer.ProjectUID','mProducts.ProductName','tOrders.OrderEntryByUserUID','tOrders.PropertyStateCode','tOrders.DocType','tOrders.PropertyCountyName','mExceptions.ExceptionName','tOrderException.ExceptionRemarks','tOrderException.ExceptionRaisedDateTime');
        
        $list = $this->DailyExceptionmodel->FetchDataExceptionModel($post);

        $no = $post['start'];
        $ExceptionList = [];
		foreach ($list as $value)
        {
		        $row = array();
		        $row[] = $value->OrderNumber?$value->OrderNumber:'-';
		        $row[] = $value->LoanNumber?$value->LoanNumber:'-';
		        $row[] = $value->BorrowerFirstName?$value->BorrowerFirstName:'-';
		        $row[] = $value->PropertyStateCode?$value->PropertyStateCode:'-';
		        $row[] = $value->PropertyCountyName?$value->PropertyCountyName:'-';
		        $row[] = $value->AgentNo?$value->AgentNo:'-';
		        $row[] = $value->UserName?$value->UserName:'-';
		        $row[] = $value->DocTypeName?$value->DocTypeName:'-';
		        $row[] = $value->CustomerName?$value->CustomerName:'-';
		        $row[] = $value->ExceptionName?$value->ExceptionName:'-';
		        $row[] = $value->ExceptionRemarks?$value->ExceptionRemarks:'-';
		        $row[] = $value->RisedDate?$value->RisedDate:'';
		        		        
		        $Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$value->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a>';
		        $row[] = $Action;
		        $ExceptionList[] = $row;
        }

        $data =  array(
        	'ExceptionList' => $ExceptionList,
        	'post' => $post
        );
		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->DailyExceptionmodel->count_all(),
			"recordsFiltered" =>  $this->DailyExceptionmodel->count_filtered($post),
			"data" => $data['ExceptionList'],
		);
		unset($post);
		unset($data);
		echo json_encode($output);
	}

	function WriteExcel()
	{

		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
			$this->DailyExceptionmodel->GetExceptionExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
        $list = $this->DailyExceptionmodel->GetExceptionExcelRecords($post);
        
        $data = [];
        
		$data[] = array(
				'S.No',
				'Order Number',
				'Loan Number',
				'Borrower Name',
				'Property State',
				'Property County',
				'Agent ID',
				'Agent Name',
				'Document Type',
				'Exception Owner',
				'Audit Result',
				'Exception Comment',
				'Last Update Date',
			);
			$j=1;
			for ($i=0; $i < sizeof($list); $i++) 
			{ 				
				$data[] = array(			
								$j+$i,
								$list[$i]->OrderNumber,
								$list[$i]->LoanNumber,
								$list[$i]->BorrowerFirstName,
								$list[$i]->PropertyStateCode,
								$list[$i]->PropertyCountyName,
								$list[$i]->AgentNo,
								$list[$i]->UserName,
								$list[$i]->DocType,
								$list[$i]->CustomerName,
								$list[$i]->ExceptionName,
								$list[$i]->ExceptionRemarks,
								$list[$i]->RisedDate
							);			
			}

		$this->outputCSV($data);
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
