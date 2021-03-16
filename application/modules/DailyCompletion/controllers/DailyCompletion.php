<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class DailyCompletion extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DailyCompletionmodel');

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

		function completedorders_ajax_list()
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
		$post['column_order'] = array('tOrderDocumentCheckIn.AgentNo','tOrders.OrderNumber','tOrderDocumentCheckIn.SettlementAgentName','tOrderPropertyRole.BorrowerFirstName','mUsers.UserName','tOrders.LoanNumber','mProjectCustomer.ProjectName', 'tOrderAssignments.CompleteDateTime',  'tOrders.OrderUID','mStatus.StatusName','mStatus.StatusColor','mCustomer.CustomerName','mProjectCustomer.ProjectUID','mProducts.ProductName','tOrderAssignments.AssignedToUserUID','tOrderAssignments.CompleteDateTime');
		$post['column_search'] = array('tOrderDocumentCheckIn.AgentNo','tOrders.OrderNumber','tOrderDocumentCheckIn.SettlementAgentName','tOrderPropertyRole.BorrowerFirstName','mUsers.UserName','tOrders.LoanNumber','mProjectCustomer.ProjectName', 'tOrderAssignments.CompleteDateTime',  'tOrders.OrderUID','mStatus.StatusName','mStatus.StatusColor','mCustomer.CustomerName','mProjectCustomer.ProjectUID','mProducts.ProductName','tOrderAssignments.AssignedToUserUID','tOrderAssignments.CompleteDateTime');
        
        $list = $this->DailyCompletionmodel->CompletedOrders($post);

        $no = $post['start'];
        $completedorderslist = [];
		foreach ($list as $revieworders)
        {$valid ? 'yes' : 'no';
		        $row = array();
		        $row[] = $revieworders->AgentNo?$revieworders->AgentNo:'-';
		        $row[] = $revieworders->UserName?$revieworders->UserName:'-';
		        $row[] = $revieworders->OrderNumber?$revieworders->OrderNumber:'-';
		        $row[] = $revieworders->LoanNumber?$revieworders->LoanNumber:'-';
		        $row[] = $revieworders->BorrowerFirstName?$revieworders->BorrowerFirstName:'-';
		        $row[] = $revieworders->CustomerName?$revieworders->CustomerName:'-';
		        $sname=$revieworders->StatusName?$revieworders->StatusName:"-";
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$sname.'</a>';
		        $row[] = $revieworders->Date?$revieworders->Date:'-';
		        $roid=$revieworders->OrderUID?$revieworders->OrderUID:'-';
		        $Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$roid).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a>';
			   

		        $row[] = $Action;
		        $completedorderslist[] = $row;
        }

        $data =  array(
        	'completedorderslist' => $completedorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->DailyCompletionmodel->count_all(),
			"recordsFiltered" =>  $this->DailyCompletionmodel->count_filtered($post),
			"data" => $data['completedorderslist'],
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
			//$this->DailyCompletionmodel->GetCompletedOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
        $list = $this->DailyCompletionmodel->GetCompletedOrdersExcelRecords($post);
        
        $data = [];
        
		$data[] = array('Agent ID','Agent Name','Order Number','Loan Number','Borrower Name','Customer Name','Document Status','Completed Date');
			for ($i=0; $i < sizeof($list); $i++) 
			{ 				
				$data[] = array(
					$list[$i]->AgentNo,
					$list[$i]->UserName,
					$list[$i]->OrderNumber,
					$list[$i]->LoanNumber,
					$list[$i]->BorrowerFirstName,
					$list[$i]->CustomerName,
					$list[$i]->StatusName,
					$list[$i]->Date
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
