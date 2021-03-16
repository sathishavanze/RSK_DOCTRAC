<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Canceled extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Canceledordersmodel');

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

		function Canceledorders_ajax_list()
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

		/* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getSectionQueuesColumns($this->uri->segment(1));
        if (!empty($QueueColumns)) 
        {
          $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
          $post['column_order'] = $columndetails;
          $post['column_search'] = array_filter($columndetails);
          $post['IsDynamicColumns'] = true;
          $post['IsDynamicColumns_Select'] = $columndetails;

        }
        /* ****** Dynamic Queues Section Ends ****** */

        $list = $this->Canceledordersmodel->CanceledOrders($post);

        $no = $post['start'];
        $Canceledorderslist = [];

        /* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] = "Ordersummary/index/";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['IsCanceled'] = true;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $WorkflowModuleUID = '', $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$Canceledorderslist			= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $Canceledorders)
        {
		        $row = array();
		        $row[] = '<a href="'.base_url('Ordersummary/index/'.$Canceledorders->OrderUID).'" target="_blank" class="ajaxload">'.$Canceledorders->OrderNumber.'</a>';
        		$row[] = $Canceledorders->CustomerName;
		        $row[] = $Canceledorders->LoanNumber;		        
		        $row[] = $Canceledorders->LoanType;	
		        $row[] = $Canceledorders->MilestoneName;	        
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$Canceledorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$Canceledorders->StatusName.'</a>';
		        $row[] = $Canceledorders->PropertyStateCode;
		        $row[] = site_datetimeformat($Canceledorders->LastModifiedDateTime);
		        // $row[] = $Canceledorders->PropertyCityName;
		        // $row[] = $Canceledorders->PropertyStateCode;
		        // $row[] = $Canceledorders->PropertyZipCode;

							
		        $Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$Canceledorders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a></div>';
		        $row[] = $Action;
		        $Canceledorderslist[] = $row;
        }



        $data =  array(
        	'Canceledorderslist' => $Canceledorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Canceledordersmodel->count_all(),
			"recordsFiltered" =>  $this->Canceledordersmodel->count_filtered($post),
			"data" => $data['Canceledorderslist'],
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
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}


        $QueueColumns = $this->Common_Model->getSectionQueuesColumns($this->uri->segment(1));
        if (!empty($QueueColumns)) 
        {

	        $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
	        $post['IsDynamicColumns_Select'] = $columndetails;
        	$post['IsDynamicColumns'] = true;
        }

        $list = $this->Canceledordersmodel->GetCanceledOrdersExcelRecords($post);
        
        $data = [];

		$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','State','Last Modified Date Time');

		/* ****** Dynamic Queues Section Starts ****** */
        if (!empty($QueueColumns)) 
        {
          	$Mischallenous['QueueColumns'] = $QueueColumns;
			$Mischallenous['IsCanceled'] = true;
			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list, $WorkflowModuleUID = '', $Mischallenous);

			if ( !empty($QueueColumns) ) 
			{
				$data = $QueueColumns['orderslist'];
				$list = [];
			}

        }
        /* ****** Dynamic Queues Section Ends ****** */

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
