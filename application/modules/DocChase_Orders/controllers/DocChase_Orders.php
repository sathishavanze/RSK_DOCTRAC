<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DocChase_Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocChase_Orders_Model');
	}

	public function index()
	{
		$data['content']='index';
		$data['is_selfassign'] = 1;
		$data['IsGetNextOrder'] = $this->DocChase_Orders_Model->CheckAutoAssignEnabled($this->loggedid);
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$data['IsParking'] = $this->Common_Model->is_parking_enabledforworkflow($this->config->item('Workflows')['WelcomeCall']);
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function DocChaseorders_ajax_list()
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
		// //column order
		// $post['column_order'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
		// $post['column_search'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
		
		//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');

    //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
		
		//column order
		$list = $this->DocChase_Orders_Model->DocChaseOrders($post,'');


		$no = $post['start'];
		$myorderslist = [];
		foreach ($list as $myorders)
		{
			$row = array();
			$row[] = $myorders->OrderNumber;
			$row[] = $myorders->CustomerName;
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->MilestoneName;
			$row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';			

			$row[] = $myorders->Workflows;
			$row[] = site_datetimeformat($myorders->LastModifiedDateTime);
			$redirect = 'History';

			$Action = '<button class="btn btn-info btn-sm PickDocOrder" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'" data-redirect="'.$redirect.'"><i class="fa fa-hand-o-up" aria-hidden="true"></i></button>';
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
			"recordsTotal" => $this->DocChase_Orders_Model->count_all(),
			"recordsFiltered" =>  $this->DocChase_Orders_Model->count_filtered($post,''),
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
		// $post['column_order'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','','tOrders.LastModifiedDateTime');
		// $post['column_search'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');

		//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
		//column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
		$list = $this->DocChase_Orders_Model->WorkInProgressOrders($post,'');


		$no = $post['start'];
		$workinginprogresslist = [];
		foreach ($list as $myorders)
		{
			$row = array();
			$row[] = $myorders->OrderNumber;
			$row[] = $myorders->CustomerName;
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->MilestoneName;
			$row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->Workflows;
			$row[] = site_datetimeformat($myorders->LastModifiedDateTime);
			$redirect = 'DocChase';
			$Action = '<button class="btn btn-link btn-info btn-just-icon btn-xs PickDocOrder"  data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'" data-redirect="'.$redirect.'"><i class="icon-pencil"></i></button>';

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
			"recordsTotal" => $this->DocChase_Orders_Model->inprogress_count_all(),
			"recordsFiltered" =>  $this->DocChase_Orders_Model->inprogress_count_filtered($post),
			"data" => $data['workinginprogresslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
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
		// $post['column_order'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','','tOrders.LastModifiedDateTime');
		// $post['column_search'] = array('tOrders.OrderDueDate','tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
		//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
    //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName','tOrders.LastModifiedDateTime');
		$list = $this->DocChase_Orders_Model->MyOrders($post,'');


		$no = $post['start'];
		$workinginprogresslist = [];
		foreach ($list as $myorders)
		{
			
			$row = array();
			$row[] = $myorders->OrderNumber;
			$row[] = $myorders->CustomerName;
			$row[] = $myorders->LoanNumber;
			$row[] = $myorders->LoanType;
			$row[] = $myorders->MilestoneName;
			$row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
			$row[] = $myorders->Workflows;
			$row[] = site_datetimeformat($myorders->LastModifiedDateTime);
			$redirect = 'DocChase';


			$Action = '<a href="DocChase/index/'. $myorders->OrderUID.'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'" data-redirect="'.$redirect.'"><i class="icon-pencil"></i></a>';
			
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
			"recordsTotal" => $this->DocChase_Orders_Model->myorders_count_all(),
			"recordsFiltered" =>  $this->DocChase_Orders_Model->myorders_count_filtered($post),
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
				$ExistingOrders = $this->DocChase_Orders_Model->CheckExistingOrders($this->loggedid,$ProjectUID,$Workflow);

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
					
					$data = $this->DocChase_Orders_Model->AssignOrders($this->loggedid,$ProjectUID,$Workflow);

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

	function CheckAndPickDocChaseOrder()
	{
		$OrderUID = $this->input->post('OrderUID');
		$result = $this->DocChase_Orders_Model->CheckDocChaseAlreadyAssigned($OrderUID);
		if($result['Status'] == 1)
		{
			$res = array('validation_error' => 1,'message'=>$result['UserName'],'color'=>'danger');
			echo json_encode($res);exit();
		}
		else
		{
			$value = $this->DocChase_Orders_Model->AssignDocChaseOrder($OrderUID);
			if($value)
			{
				$val = array('validation_error' => 2,'message'=>'Order Assigned','color'=>'success','redirect'=>'DocChase');
				echo json_encode($val);exit();
			}
		}
	}

	/**
	*Function Reassign DocChase Order
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 27 April 2020
	*/
	function PickDocChaseOrder()
	{
		$OrderUID = $this->input->post('OrderUID');

		$value = $this->DocChase_Orders_Model->AssignDocChaseOrder($OrderUID);
		if($value)
		{
			$val = array('validation_error' => 2,'message'=>'Order Assigned','color'=>'success','redirect'=>'DocChase');
			echo json_encode($val);exit();
		} else {
			$val = array('validation_error' => 1,'message'=>'Unable to assign','color'=>'danger');
			echo json_encode($val);exit();
		}

	}

	function WriteExcel()
	{
		/*@Author Jainulabdeen <jainulabdeeen.b@avanzegroup.com> @Updated Mar 4 2020*/
		$post = $this->input->post();
		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}

		if($post['formData']['Status'] == 'orderslist')
		{
			$list = $this->DocChase_Orders_Model->DocChaseOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','Workflows','Last Modified Date Time');
				for ($i=0; $i < sizeof($list); $i++) { 
			$data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->Workflows,site_datetimeformat($list[$i]->LastModifiedDateTime));				
			}
			$this->outputCSV($data);
		}
		else if($post['formData']['Status'] == 'workinprogresslist')
		{
			$list1 = $this->DocChase_Orders_Model->WorkInProgressOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','Workflows','Last Modified Date Time');
				for ($i=0; $i < sizeof($list); $i++) { 
			$data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->Workflows,site_datetimeformat($list[$i]->LastModifiedDateTime));				
			}
 
			 $this->outputCSV($data);
		}
		else if($post['formData']['Status'] == 'myorderlist')
		{
			$list2 = $this->DocChase_Orders_Model->MyOrders($post,'');
			/**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			$data = [];
			$data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','Workflows','Last Modified Date Time');
				for ($i=0; $i < sizeof($list2); $i++) { 
			$data[] = array($list2[$i]->OrderNumber,$list2[$i]->CustomerName,$list2[$i]->LoanNumber,$list2[$i]->LoanType,$list2[$i]->MilestoneName,$list2[$i]->StatusName,$list2[$i]->Workflows,site_datetimeformat($list2[$i]->LastModifiedDateTime));				
			}
			$this->outputCSV($data); 
		}
		else if($post['formData']['Status'] == 'parkingorderlist')
		{
			 $list3 = $this->DocChase_Orders_Model->parkingorders($post,'');
			 /**
			*Function Description: Separate EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			*/
			 $data[] = array('Order No','Client','Loan No','Milestone','Current Status','Remainder On','Raised By','Remarks','LastModifiedDateTime');
				for ($i=0; $i < sizeof($list3); $i++) { 
				$data[] = array($list3[$i]->OrderNumber,$list3[$i]->CustomerName, $list3[$i]->LoanNumber,$list3[$i]->MilestoneName,$list3[$i]->StatusName,site_datetimeformat($list3[$i]->Remainder),$list3[$i]->RaisedBy,$list3[$i]->Remarks,site_datetimeformat($list3[$i]->LastModifiedDateTime));				
				}

			$this->outputCSV($data);
		}
		/*End*/

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

	function parkingorders_ajax_list()
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
        $post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','mMilestone.MilestoneName','mStatus.StatusName','tOrderParking.Remainder','b.UserName','tOrderParking.Remarks','tOrders.LastModifiedDateTime');
        $post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','mMilestone.MilestoneName','mStatus.StatusName','tOrderParking.Remainder','b.UserName','tOrderParking.Remarks','tOrders.LastModifiedDateTime');
        //column order
        $list = $this->DocChase_Orders_Model->parkingorders($post,'');


        $no = $post['start'];
        $myorderslist = [];
        foreach ($list as $myorders)
        {
        	$row = array();
        	$row[] = $myorders->OrderNumber;
        	$row[] = $myorders->CustomerName;
        	$row[] = $myorders->LoanNumber;
        	$row[] = $myorders->MilestoneName;
        	$row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
        	$row[] = site_datetimeformat($myorders->Remainder);
        	$row[] = $myorders->RaisedBy;
        	$row[] = $myorders->Remarks;
        	$row[] = site_datetimeformat($myorders->LastModifiedDateTime);

        	$Action = '<a href="DocChase/index/'. $myorders->OrderUID.'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i></a>';

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
			"recordsTotal" => $this->DocChase_Orders_Model->parkingorders_count_all(),
			"recordsFiltered" =>  $this->DocChase_Orders_Model->parkingorders_count_filtered($post),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

}?>

