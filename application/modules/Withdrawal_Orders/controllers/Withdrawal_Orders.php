<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Withdrawal_Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Withdrawal_Orders_model');

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

		function Withdrawal_Orders_ajax_list()
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
    	//column order
	        $post['column_order'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','b.UserName','tOrderWithdrawal.RaisedDateTime','tOrders.LastModifiedDateTime');
        $post['column_search'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','b.UserName','tOrderWithdrawal.RaisedDateTime','tOrders.LastModifiedDateTime');
        //column order
        $list = $this->Withdrawal_Orders_model->WithdrawalOrders($post);
        $no = $post['start'];
        $Withdrawalorderslist = [];
		foreach ($list as $revieworders)
        {
		        $row = array();
		        $Action = '<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a>';
		        $row[] = '<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="ajaxload">'.$revieworders->OrderNumber.'</a>';
		        $row[] = $revieworders->CustomerName;
		        $row[] = $revieworders->LoanNumber;		        
		        $row[] = $revieworders->LoanType;	
		        $row[] = $revieworders->MilestoneName;	        
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
		        $row[] = $revieworders->ReasonName;
		        $row[] = $revieworders->RaisedBy;
		        $row[] = $revieworders->RaisedDateTime;
		        // $row[] = $revieworders->OrderDueDate;
		        $row[] = $revieworders->LastModifiedDateTime;
		        $row[] = $Action;
		        $Withdrawalorderslist[] = $row;
        }

        $data =  array(
        	'completedorderslist' => $Withdrawalorderslist,
        	'post' => $post
        );

		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Withdrawal_Orders_model->count_all(),
			"recordsFiltered" =>  $this->Withdrawal_Orders_model->count_filtered($post),
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
			//$this->Withdrawal_Orders_model->GetCancelledOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
		$list = $this->Withdrawal_Orders_model->GetWithdrawalOrdersExcelRecords($post);
		$data = [];

		$data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','Reason','RaisedBy','RaisedOn','Last Modified Date Time');
		for ($i=0; $i < sizeof($list); $i++) { 

			$data[] = array($list[$i]->OrderNumber,$list[$i]->LoanNumber,$list[$i]->CustomerName,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->ReasonName,$list[$i]->RaisedBy,site_datetimeformat($list[$i]->RaisedDateTime),site_datetimeformat($list[$i]->LastModifiedDateTime));				
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
