<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Esclation extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Esclationordersmodel');
		$this->load->model('Customer/Customer_Model');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

		function Esclationorders_ajax_list()
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

        $list = $this->Esclationordersmodel->EsclationOrders($post);

        $no = $post['start'];
        $Esclationorderslist = [];

        /* ****** Dynamic Queues Section Starts ****** */
		$Mischallenous['PageBaseLink'] = "Ordersummary/index/";
		$Mischallenous['QueueColumns'] = $QueueColumns;
		$Mischallenous['IsEsclation'] = true;
		$DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $WorkflowModuleUID = '', $Mischallenous);

		if (!empty($DynamicColumns)) 
		{
			$Esclationorderslist			= 	$DynamicColumns['orderslist'];
			$post['column_order']		=	$DynamicColumns['column_order'];
			$post['column_search']		=	$DynamicColumns['column_search'];
			$list = [];
		}
		/* ****** Dynamic Queues Section Ends ****** */

		foreach ($list as $Esclationorders)
        {
		        $row = array();
		        $row[] = '<a href="'.base_url('Ordersummary/index/'.$Esclationorders->OrderUID).'" target="_blank" class="ajaxload">'.$Esclationorders->OrderNumber.'</a>';
        		$row[] = $Esclationorders->CustomerName;
		        $row[] = $Esclationorders->LoanNumber;		        
		        $row[] = $Esclationorders->LoanType;	
		        $row[] = $Esclationorders->MilestoneName;	        
		        $row[] = '<a href="javascript:void(0)" style=" background: '.$Esclationorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$Esclationorders->StatusName.'</a>';
		        $row[] = $Esclationorders->PropertyStateCode;
		        $row[] = site_datetimeformat($Esclationorders->LastModifiedDateTime);
		        // $row[] = $Esclationorders->PropertyCityName;
		        // $row[] = $Esclationorders->PropertyStateCode;
		        // $row[] = $Esclationorders->PropertyZipCode;

							
		        $Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$Esclationorders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a></div>';
		        $row[] = $Action;
		        $Esclationorderslist[] = $row;
        }



        $data =  array(
        	'Esclationorderslist' => $Esclationorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Esclationordersmodel->count_all($post),
			"recordsFiltered" =>  $this->Esclationordersmodel->count_filtered($post),
			"data" => $data['Esclationorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function WriteExcel()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$writer = new XLSXWriter();

		$filename = 'Expiration';

		$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

		$post['advancedsearch'] = $this->input->post('formData');

		$QueueColumns = $this->Common_Model->getSectionQueuesColumns($this->uri->segment(1));

        if (!empty($QueueColumns)) 
        {

	        $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));	        
	        $post['IsDynamicColumns_Select'] = $columndetails;
        	$post['IsDynamicColumns'] = true;
        }

        $list = $this->Esclationordersmodel->EsclationOrders($post);
        
        $data = [];

		if ($post['IsDynamicColumns'] == true) 
		{                    
			$Mischallenous = array();
			$Mischallenous['QueueColumns'] = $QueueColumns;
			$Mischallenous['IsEsclation'] = true;

			$QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list, $WorkflowModuleUID = '', $Mischallenous);

			$header = $QueueColumns['header'];
			$data = $QueueColumns['orderslist'];

			$sheetheader = [];
			foreach ($header as $hkey => $head) {
				$sheetheader[$head] = "string";
			}

			$writer->writeSheetHeader($filename,$sheetheader, $header_style);  

			foreach($data as $key => $Order) {
				if ($key > 0) {
					$writer->writeSheetRow($filename, $Order);
				}					
			}
		}
		else
		{
			$HEADER = array('Order No'=>'GENERAL','Client'=>'GENERAL','Loan No'=>'GENERAL','Loan Type'=>'GENERAL','Milestone'=>'GENERAL','Current Status'=>'GENERAL','State'=>'GENERAL','Last Modified Date Time'=>'GENERAL');

			$writer->writeSheetHeader($filename,$HEADER, $header_style);
			for ($i=1; $i < sizeof($list); $i++) {
				
				  $data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->PropertyStateCode,site_datetimeformat($list[$i]->LastModifiedDateTime));				
			}
		}

		ob_clean();
		$writer->writeToFile($filename);
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename= '.$filename);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Transfer-Encoding: binary');
		header('Set-Cookie: fileDownload=true; path=/');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		readfile($filename);
		unlink($filename);
		exit(0);

	}

	public function RaiseEsclation()
	{
		$post = $this->input->post();

		// Check Escalation Raise
		$RaiseEsclationStatus = $this->Esclationordersmodel->CheckRaiseEsclation($post);

		if ($RaiseEsclationStatus == "EXISTS") {
			echo json_encode(array('Status' => 1,'message'=>'Escalation is already initiated')); exit();
		} elseif ($RaiseEsclationStatus == "NOT EXISTS") {
			echo json_encode(array('Status' => 1,'message'=>'No Escalation has been initiated')); exit();
		}

		// Update Data	
		$result = $this->Esclationordersmodel->RaiseEsclation($post);
		if( $result == true)
		{
			if (empty($post['HighlightUID'])) {
				$res = array('Status' => 0,'message'=>'Escalation Initiated Successsfully');
			} else {
				$res = array('Status' => 0,'message'=>'Escalation Cleared Successsfully');	
			}					
			echo json_encode($res);exit();

		} else {

			$res = array('Status' => 1,'message'=>'Failed');
			echo json_encode($res);exit();
		}
	}

}?>
