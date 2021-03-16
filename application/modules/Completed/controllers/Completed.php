<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Completed extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Completedordersmodel');

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
    	//get_post_input_data
    	//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime');

	    //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime');
        $list = $this->Completedordersmodel->CompletedOrders($post);

        $no = $post['start'];
        $completedorderslist = [];
		foreach ($list as $revieworders)
        {
		        $row = array();
		        $row[] = '<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="ajaxload">'.$revieworders->OrderNumber.'</a>';
        		$row[] = $revieworders->CustomerName;
		        $row[] = $revieworders->LoanNumber;		        
		        $row[] = $revieworders->LoanType;	
		        $row[] = $revieworders->MilestoneName;	        
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
		        $row[] = $revieworders->PropertyStateCode;
		        $row[] = site_datetimeformat($revieworders->LastModifiedDateTime);
		        // $row[] = $revieworders->PropertyCityName;
		        // $row[] = $revieworders->PropertyStateCode;
		        // $row[] = $revieworders->PropertyZipCode;

							
		        $Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a></div>';
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
			"recordsTotal" => $this->Completedordersmodel->count_all(),
			"recordsFiltered" =>  $this->Completedordersmodel->count_filtered($post),
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
			$this->Completedordersmodel->GetCompletedOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
        $list = $this->Completedordersmodel->GetCompletedOrdersExcelRecords($post);
        
        $data = [];

					$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','State','Last Modified Date Time');
			for ($i=0; $i < sizeof($list); $i++) { 

				
				  $data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->PropertyStateCode,site_datetimeformat($list[$i]->LastModifiedDateTime));				
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
