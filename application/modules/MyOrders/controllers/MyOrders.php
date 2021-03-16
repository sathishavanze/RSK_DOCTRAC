<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MyOrders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('MyOrders_Model');
	}

	public function index()
	{
		$data['content']='index';
		$data['is_selfassign'] = 1;
		$data['IsGetNextOrder'] = $this->MyOrders_Model->CheckAutoAssignEnabled($this->loggedid);
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function myorders_ajax_list()
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
        $post['column_order'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber','mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mInputDocType.DocTypeName','tOrders.LoanNumber','mStatus.StatusName','tOrders.PropertyAddress1','tOrders.PropertyCityName','tOrders.PropertyStateCode','tOrders.PropertyZipCode', 'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
        $post['column_search'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName', 'mProjectCustomer.ProjectName','mInputDocType.DocTypeName', 'tOrders.LoanNumber','mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode', 'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
        //column order
        $list = $this->MyOrders_Model->MyOrders($post,'');


        $no = $post['start'];
        $myorderslist = [];
		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->PackageNumber;
		        $row[] = $myorders->CustomerName;
/*
		        $row[] = $myorders->ProductName;
		        $row[] = $myorders->ProjectName;
		        $row[] = $myorders->DocTypeName;
*/		        $row[] = $myorders->LoanNumber;
		        //$row[] = $myorders->LenderName;
		        $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
		        $row[] = $myorders->PropertyAddress1.' '.$myorders->PropertyAddress2;
		        $row[] = $myorders->PropertyCityName;
		        $row[] = $myorders->PropertyStateCode;
		        $row[] = $myorders->PropertyZipCode;
				
/*
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($myorders->OrderDueDate));
*/
		        if($this->loggedid == $myorders->AssignedToUserUID)
		        {
		        	$Action = '<a href="Ordersummary/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i></a>';
		        }else{

		       		$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs PickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="icon-pencil"></i></button>';
		        }
		        $row[] = $Action;
		        $myorderslist[] = $row;
        }



        $data =  array(
        	'myorderslist' => $myorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->MyOrders_Model->count_all(),
			"recordsFiltered" =>  $this->MyOrders_Model->count_filtered($post,''),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function workinginprogress_ajax_list()
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
		$post['column_order'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mInputDocType.DocTypeName','tOrders.LoanNumber','mUsers.UserName','mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode', 'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
		$post['column_search'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName', 'mProjectCustomer.ProjectName','mInputDocType.DocTypeName','tOrders.LoanNumber','mUsers.UserName','mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode', 'tOrders.OrderEntryDateTime', 'tOrders.OrderDueDate');
        //column order
        $list = $this->MyOrders_Model->WorkInProgressOrders($post);

        $no = $post['start'];
        $workinginprogresslist = [];
		foreach ($list as $workinprogress)
        {
		        $row = array();
		        $row[] = $workinprogress->OrderNumber;
		        $row[] = $workinprogress->PackageNumber;
		        $row[] = $workinprogress->CustomerName;
		        $row[] = $workinprogress->ProductName;
		        $row[] = $workinprogress->ProjectName;
		        $row[] = $workinprogress->DocTypeName;
		        $row[] = $workinprogress->LoanNumber;
		        //$row[] = $workinprogress->LenderName;
		        $row[] = $workinprogress->AssignedUserName;
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$workinprogress->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$workinprogress->StatusName.'</a>';
		        $row[] = $workinprogress->PropertyAddress1.' '.$workinprogress->PropertyAddress2;
		        $row[] = $workinprogress->PropertyCityName;
		        $row[] = $workinprogress->PropertyStateCode;
		        $row[] = $workinprogress->PropertyZipCode;
				$row[] = date('m/d/Y H:i:s', strtotime($workinprogress->OrderEntryDateTime));
				$row[] = date('m/d/Y H:i:s', strtotime($workinprogress->OrderDueDate));

		        $Action = '<button  class="btn btn-link btn-info btn-just-icon btn-xs stacking" data-orderuid = "'.$workinprogress->OrderUID.'"><i class="icon-pencil"></i></button>';
		        $row[] = $Action;
		        $workinginprogresslist[] = $row;
        }



        $data =  array(
        	'workinginprogresslist' => $workinginprogresslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->MyOrders_Model->inprogress_count_all(),
			"recordsFiltered" =>  $this->MyOrders_Model->inprogress_count_filtered($post),
			"data" => $data['workinginprogresslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function GetNextOrder()
	{
		$ProjectUID = $this->input->post('ProjectUID');
		$Workflow = $this->input->post('Workflow');
		$this->load->library('form_validation');
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ProjectUID', '', 'required');
			$this->form_validation->set_rules('Workflow', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');
			if ($this->form_validation->run() == true)
			{

					$existingarray = [];
					$ExistingOrders = $this->MyOrders_Model->CheckExistingOrders($this->loggedid,$ProjectUID,$Workflow);

					foreach ($ExistingOrders as $key => $value) {
						 if($value->StatusUID != 100)
						 {
						 	$existingarray[] = 'Not Completed';
						 }
					}
					if(sizeof($existingarray) > 0)
					{
						$result = array('validation_error' => 2,'message'=>'Existing Orders Not Completed','color'=>'danger');
						echo json_encode($result);

					}
					else
					{
						
						$data = $this->MyOrders_Model->AssignOrders($this->loggedid,$ProjectUID,$Workflow);

						if($data == 0)
						{	
							$result = array('validation_error' => 2,'message'=>'No Orders found','color'=>'danger');
							echo json_encode($result);

						}else{
							$result = array('validation_error' => 2,'message'=>'Order Assigned','color'=>'success');
							echo json_encode($result);
						}

					}
			}else{

				$Msg = $this->lang->line('Empty_Validation');
				$formvalid = [];

				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'ProjectUID' => form_error('ProjectUID'),
					'Workflow' => form_error('Workflow'),
					'color' => 'danger',
					
				);
				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
	    }
	}

	function PickExistingOrderCheck()
	{
		$OrderUID = $this->input->post('OrderUID');
		$ProjectUID = $this->input->post('ProjectUID');
		$result = $this->MyOrders_Model->PickExistingOrderCheck($OrderUID);
		if($result['Status'] == 1)
		{
			$res = array('validation_error' => 1,'message'=>'Order was picked by ' .$result['UserName'],'color'=>'danger');
			echo json_encode($res);exit();
		}
		else
		{
			$value = $this->MyOrders_Model->OrderAssign($OrderUID,$ProjectUID);
			if($value)
			{
				$val = array('validation_error' => 2,'message'=>'Order Assigned','color'=>'success');
			    echo json_encode($val);exit();
			}
		}
	}

	function WriteExcel()
	{

		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
			$this->MyOrders_Model->GetMyOrdersExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
        $list = $this->MyOrders_Model->GetMyOrdersExcelRecords($post);

        
        $data = [];

					$data[] = array('Prop No','Pack No','Client','Product Name','Project Name','Loan No','Current Status','Property Address','Property City','Property State','Zip Code','OrderEntryDateTime','OrderDueDateTime');
			for ($i=0; $i < sizeof($list); $i++) { 
				
				  $data[] = array($list[$i]->OrderNumber,$list[$i]->PackageNumber,$list[$i]->CustomerName,$list[$i]->ProductName,$list[$i]->ProjectName,$list[$i]->LoanNumber,$list[$i]->StatusName,$list[$i]->PropertyAddress1.$list[$i]->PropertyAddress2,$list[$i]->PropertyCityName,$list[$i]->PropertyStateCode,$list[$i]->PropertyZipCode,$list[$i]->OrderEntryDateTime,$list[$i]->OrderDueDate);				
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

