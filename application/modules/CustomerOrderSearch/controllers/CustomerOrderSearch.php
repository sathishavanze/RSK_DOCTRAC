<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class CustomerOrderSearch extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('CustomerOrderSearchModel');

	}	

	public function index()
	{
		
		$data['content'] = 'index';

		//$data['Customers'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID'=>'ASC'], []);

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function GetCustomerOrderSearchDetails()
	{
		$CustomerOrderSearchDetails = $this->CustomerOrderSearchModel->GetCustomerOrderSearchDetails($_POST);
		$html = '';
		$i =0;
		foreach ($CustomerOrderSearchDetails as  $row) {
			if($row->StatusUID == 100 || $row->StatusUID == 40 || $row->StatusUID == 30 || $row->StatusUID == 35 )
			{
				$ImageHtml = '<div style="display: inline-flex;"><a href="'.base_url('Indexing/DownloadZip/'.$row->OrderUID). '" class="btn btn-link btn-info btn-just-icon btn-xs ExportDocument"   data-OrderUID="' . $row->OrderUID . '"  data-OrderNumber="' . $row->OrderNumber . '" data-LoanNumber="' . $row->LoanNumber . '"><i class="icon-download"></i></a></div>';
			}
			else
			{
				/*$ImageHtml = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$row->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a></div>';*/
			}
			
				$html .= '
				<tr>
				<td><span> '.$row->OrderNumber.'</span></td>
				<td> <span> '.$row->CustomerName.'</span></td>
				<td> <span> '.$row->CustomerReferenceNumber.'</span></td>
				<td><span> '.$row->ProjectName.'</span></td>
				<td><span>'.$row->LoanNumber.'</span></td>
				<td><span> '.$row->LenderName.'</span></td>
				<td><span> '.$row->PropertyAddress1.', '.$row->PropertyCityName.', '.$row->PropertyStateCode.', '.$row->PropertyCountyName.', '.$row->PropertyZipCode.'</span></td>
				<td>'.$ImageHtml.'	</td>
				</tr>
				';
			
			
		}
		//echo '<pre>';print_r($html);exit;
		if($html == '')
		{
			$html = '<div class="col-md-12"><p style="text-align:center;">No Data Found...</p></div>';
			echo json_encode($html);
		}
		else
		{
			echo json_encode($html);
		}
	}


	


	/* ----- SUPPORTING FUNCTIONS ENDS ---- */
	
	
}?>
