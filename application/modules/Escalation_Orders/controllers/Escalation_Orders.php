<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Escalation_Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Escalation_Orders_model');

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

		function GetEsclationQueue_ajax_list()
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
		$post['column_order'] = array('tOrders.OrderDueDate','tOrders.OrderNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrders.LastModifiedDateTime');
		$post['column_search'] = array('tOrders.OrderDueDate','tOrders.OrderNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName', 'mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrders.LastModifiedDateTime');
        //column order
        $list = $this->Escalation_Orders_model->GetEsclationQueue($post); 
        $no = $post['start'];
        $Escalationorderslist = [];
		foreach ($list as $revieworders) {
			$url = array_search ($revieworders->WorkflowModuleUID, $this->config->item('Order_WorkflowMenu'));
			$redirect = !empty($url) ? base_url().$url.'/index/'.$revieworders->OrderUID : base_url().'DocChase/index/'.$revieworders->OrderUID;

			$row = array();
		        $row[] = '<a href="'.$redirect.'" target="_blank" class="ajaxload">'.$revieworders->OrderNumber.'</a>';
		        $row[] = $revieworders->CustomerName;
		        $row[] = $revieworders->LoanNumber;		        
		        $row[] = $revieworders->LoanType;	
		        $row[] = $revieworders->MilestoneName;	        
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
		        $row[] = $revieworders->PropertyStateCode;
		        $row[] = $revieworders->OrderDueDate;
		        $row[] = $revieworders->LastModifiedDateTime;
		        $Action = '<a href="'.$redirect.'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a>';
		        $row[] = $Action;
		        $Escalationorderslist[] = $row;
        }

        $data =  array(
        	'completedorderslist' => $Escalationorderslist,
        	'post' => $post
        );

		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Escalation_Orders_model->count_all(),
			"recordsFiltered" =>  $this->Escalation_Orders_model->count_filtered($post),
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
			//$this->Escalation_Orders_model->GetGetEsclationQueueExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
        $list = $this->Escalation_Orders_model->GetGetEsclationQueueExcelRecords($post);

        $data = [];

		// $data[] = array('Prop No','Pack No','Client','Product Name','Project Name','Doc Type','Loan No','Milestone','Current Status','Property Address','Property City','Property State','Zip Code','OrderEntryDateTime','OrderDueDateTime');
		$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','Property State','DueDateTime','LastModifiedDateTime');
		for ($i=0; $i < sizeof($list); $i++) 
		{
			$data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$List[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->PropertyStateCode,$list[$i]->OrderDueDate,$list[$i]->LastModifiedDateTime);
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
