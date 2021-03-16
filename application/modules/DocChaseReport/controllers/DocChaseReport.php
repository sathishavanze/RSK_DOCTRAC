<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocChaseReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocChaseReportmodel');
	}

	/**
	*docchase index load
	*@authoralwin <alwin.l@avanzegroup.com>
	*@since Friday 31 March 2020
	*/
	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Status'] = $this->Common_Model->getStatus();
		$data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/**
	*docchase server side pagination
	*@authoralwin <alwin.l@avanzegroup.com>
	*@since Friday 31 March 2020
	*/
	function FetchDocChaseReportReport() 
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');    	
		$post['column_order'] = array('tOrders.OrderNumber','tOrders.LoanNumber','tOrders.StatusUID');
		$post['column_search'] = array('tOrders.OrderNumber','tOrders.LoanNumber','tOrders.StatusUID');

		$list = $this->DocChaseReportmodel->DocChaseReportReportOrders($post); 
		$workflows = $this->Common_Model->GetCustomerBasedModules();        
		$no = $post['start'];
		$DocChaseReportorderslist = [];
		foreach ($list as $key => $revieworders)
		{
				$row = array();
				$row[] = $revieworders->OrderNumber;
				$row[] = $revieworders->LoanNumber;
				$row[] = '<a  href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
				foreach ($workflows as $key => $value) 
				{
					if($this->config->item('Workflows')['DocChase'] != $value->WorkflowModuleUID)
					{
						$row[] = $revieworders->{$value->SystemName};
					}
				}

				$Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
				<i class="icon-pencil"></i></a>';
				$row[] = $Action;
			$DocChaseReportorderslist[] = $row;
		}

		$data =  array(
			'DocChaseReportorderslist' => ($DocChaseReportorderslist),
			'post' => $post
		);

		$post = $data['post'];

		$count_all = $this->DocChaseReportmodel->count_filtered($post);

		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->DocChaseReportmodel->count_all(),
			"recordsFiltered" =>  $count_all,
			"data" => $data['DocChaseReportorderslist'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}


	/**
	*docchase generate excel
	*@authoralwin <alwin.l@avanzegroup.com>
	*@since Friday 31 March 2020
	*/
	function WriteExcel()
	{
		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
		}
		else
		{
			$post['advancedsearch'] = $this->input->post('formData');
		}
		$list = $this->DocChaseReportmodel->GetDocChaseReportOrdersExcelRecords($post);
		$workflows = $this->Common_Model->GetCustomerBasedModules(); 
		$data = [];
		$HeaderData = [];

			$HeaderData[] = 'Order Number';
			$HeaderData[] = 'Loan Number';
			$HeaderData[] = 'StatusName';

			foreach ($workflows as $key => $value) 
			{
				if($this->config->item('Workflows')['DocChase'] != $value->WorkflowModuleUID)
				{
					$HeaderData[] = $value->SystemName;
				}
			}
		$data[] = $HeaderData;

		for ($i=0; $i < sizeof($list); $i++){ 
 			$WorkflowData = [];
				$WorkflowData[] = $list[$i]->OrderNumber;
				$WorkflowData[] = $list[$i]->LoanNumber;
				$WorkflowData[] = $list[$i]->StatusName;

				foreach ($workflows as $key => $value) 
				{
					if($this->config->item('Workflows')['DocChase'] != $value->WorkflowModuleUID)
					{
						$WorkflowData[] = $list[$i]->{$value->SystemName};
					}
				}
				
		$data[] = $WorkflowData;
		}
		$this->outputCSV($data);
	}

	/**
	*docchase download excel export
	*@authoralwin <alwin.l@avanzegroup.com>
	*@since Friday 31 March 2020
	*/
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
