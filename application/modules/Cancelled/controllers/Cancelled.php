<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Cancelled extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Cancelledordersmodel');

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

		function cancelledorders_ajax_list()
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
		$post['column_order'] = array('tOrders.OrderNumber','tOrders.OrderDueDate', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrders.LastModifiedDateTime');
    	$post['column_search'] = array('tOrders.OrderNumber','tOrders.OrderDueDate', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrders.LastModifiedDateTime');
        //column order
        $list = $this->Cancelledordersmodel->CancelledOrders($post);

        $no = $post['start'];
        $completedorderslist = [];
		foreach ($list as $revieworders)
        {
		        $row = array();
		        $row[] = '<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" class="ajaxload">'.$revieworders->OrderNumber.'</a>';
		        $row[] = $revieworders->CustomerName;
/*		        $row[] = $revieworders->ProductName;
		        $row[] = $revieworders->ProjectName;
		        $row[] = $revieworders->DocTypeName;*/
		        $row[] = $revieworders->LoanNumber;		        
		        $row[] = $revieworders->LoanType;	
		        $row[] = $revieworders->MilestoneName;	        
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
		        $row[] = $revieworders->PropertyStateCode;
		        $row[] = $revieworders->OrderDueDate;
		        $row[] = $revieworders->LastModifiedDateTime;
		        // $row[] = $revieworders->PropertyCityName;
		        // $row[] = $revieworders->PropertyStateCode;
		        // $row[] = $revieworders->PropertyZipCode;
/*				$row[] = date('m/d/Y H:i:s', strtotime($revieworders->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($revieworders->OrderDueDate));*/

		        $Action = '<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
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
			"recordsTotal" => $this->Cancelledordersmodel->count_all(),
			"recordsFiltered" =>  $this->Cancelledordersmodel->count_filtered($post),
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
			//$this->Cancelledordersmodel->GetCancelledOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
        $list = $this->Cancelledordersmodel->GetCancelledOrdersExcelRecords($post);

        $data = [];

		$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','State','OrderDueDateTime','LastModifiedDateTime');
		for ($i=0; $i < sizeof($list); $i++) 
		{
			$data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->PropertyStateCode,$list[$i]->OrderDueDateTime,$list[$i]->LastModifiedDateTime);
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
