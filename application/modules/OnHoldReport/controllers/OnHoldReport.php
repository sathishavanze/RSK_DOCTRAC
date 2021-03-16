<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class OnHoldReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('OnHoldReport_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function OnHold_ajax_list()
	{
		//get_post_input_data
		$post['advancedsearch'] = $this->input->post('formData');
    	$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = trim($search['value']);
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
    	//get_post_input_data
    	//column order
		$post['column_order'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mStatus.StatusName','tOrders.PropertyAddress1','tOrders.PropertyCityName','tOrders.PropertyStateCode','tOrders.PropertyZipCode','tOrders.OrderEntryDateTime',/*'tOrderOnhold.OnHoldType',*/
			'tOrderOnhold.OnHoldStatus','tOrderOnhold.Remarks','tOrderOnhold.Comments', 'mUsers.UserName', 'tOrderOnhold.OnHoldDateTime', 'tOrderOnhold.ReleaseDateTime');
		$post['column_search'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber', 'mCustomer.CustomerName','mProducts.ProductName','mProjectCustomer.ProjectName','mStatus.StatusName','tOrders.PropertyAddress1','tOrders.PropertyCityName','tOrders.PropertyStateCode','tOrders.PropertyZipCode','tOrders.OrderEntryDateTime',/*'tOrderOnhold.OnHoldType',*/
			'tOrderOnhold.OnHoldStatus','tOrderOnhold.Remarks','tOrderOnhold.Comments','mUsers.UserName', 'tOrderOnhold.OnHoldDateTime', 'tOrderOnhold.ReleaseDateTime');
        //column order
        $list = $this->OnHoldReport_Model->OnHold_ajax_list($post);
        $no = $post['start'];
        $OnHoldOrderslist = [];
		foreach ($list as $OnHoldOrders)
        {
		        $row = array();
				$row[] = $OnHoldOrders->OrderNumber;
				$row[] = $OnHoldOrders->PackageNumber;
				$row[] = $OnHoldOrders->CustomerName;
				$row[] = $OnHoldOrders->ProductName;
				$row[] = $OnHoldOrders->ProjectName;
				$row[] = '<a href="javascript:void(0)" style=" background: '.$OnHoldOrders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$OnHoldOrders->StatusName.'</a>';
				$row[] = $OnHoldOrders->PropertyAddress1;
				$row[] = $OnHoldOrders->PropertyCityName;
				$row[] = $OnHoldOrders->PropertyStateCode;
				$row[] = $OnHoldOrders->PropertyZipCode;
				$row[] = $OnHoldOrders->OrderEntryDateTime;
				// $row[] = $OnHoldOrders->OnHoldType;
				if ($OnHoldOrders->OnHoldStatus == 'Release') {
					$row[] = '<a href="javascript:void(0)" style="padding: 5px 10px;border-radius:0px;" class="btn btn-warning">'.$OnHoldOrders->OnHoldStatus.'</a>';
				}
				else{
					$row[] = '<a href="javascript:void(0)" style="padding: 5px 10px;border-radius:0px;" class="btn btn-danger">'.$OnHoldOrders->OnHoldStatus.'</a>';
				}
		        $row[] = $OnHoldOrders->Remarks;
		        $row[] = $OnHoldOrders->Comments;
		        $row[] = $OnHoldOrders->UserName;
		        if ($OnHoldOrders->OnHoldDateTime == '0000-00-00 00:00:00' ) {
		        	$OnHoldDateTime = '';
		        }
		        else{
		        	$OnHoldDateTime = $OnHoldOrders->OnHoldDateTime;
		        }
		        $row[] = $OnHoldDateTime;
		        if ($OnHoldOrders->ReleaseDateTime == '0000-00-00 00:00:00' ) {
		        	$ReleaseDateTime = '';
		        }
		        else{
		        	$ReleaseDateTime = $OnHoldOrders->ReleaseDateTime;
		        }
		        $row[] = $ReleaseDateTime;
		        $Action = '<a href="'.base_url('Ordersummary/index/'.$OnHoldOrders->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>';
		        $row[] = $Action;
		        $OnHoldOrderslist[] = $row;
        }

        $data =  array(
        	'OnHoldOrderslist' => $OnHoldOrderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->OnHoldReport_Model->count_all(),
			"recordsFiltered" =>  $this->OnHoldReport_Model->count_filtered($post),
			"data" => $data['OnHoldOrderslist'],
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
			$this->OnHoldReport_Model->GetwaitingforOnHoldExcelRecords($post);
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}
        $list = $this->OnHoldReport_Model->GetwaitingforOnHoldExcelRecords($post);
        
        $data = [];

					$data[] = array('Prop No','Pack No','Client','Product Name','Project Name','Loan No','Current Status','Property Address','Property City','Property State','Zip Code','OnHold Type','OnHoldStatus','Remarks','Comments','Assigned User','OnHold DateTime','Release DateTime');
			for ($i=0; $i < sizeof($list); $i++) { 

				
				  $data[] = array($list[$i]->OrderNumber,$list[$i]->PackageNumber,$list[$i]->CustomerName,$list[$i]->ProductName,$list[$i]->ProjectName,$list[$i]->LoanNumber,$list[$i]->StatusName,$list[$i]->PropertyAddress1.$list[$i]->PropertyAddress2,$list[$i]->PropertyCityName,$list[$i]->PropertyStateCode,$list[$i]->PropertyZipCode,$list[$i]->OnHoldType,$list[$i]->OnHoldStatus,$list[$i]->Remarks,$list[$i]->Comments,$list[$i]->UserName,$list[$i]->OnHoldDateTime,$list[$i]->ReleaseDateTime);
				
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

} 

?>