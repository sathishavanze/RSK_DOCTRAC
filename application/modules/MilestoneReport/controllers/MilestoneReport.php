<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MilestoneReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Common_Model');
    $this->load->model('MilestoneReport_Model');
	}

	public function index()
	{
		$data['content'] = 'index';
    $data['Projects'] = $this->Common_Model->GetProjectCustomers();
    $data['Clients'] = $this->Common_Model->GetClients();
    $data['Milestone'] = $this->Common_Model->Milestone();
		$data['Users'] = $this->Common_Model->get('mUsers', ['Active'=>STATUS_ONE]);
 		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

  function MilestoneReportAjaxList() 
  {
    //Advanced Search
    $post['advancedsearch'] = $this->input->post('formData');
    $post['length'] = $this->input->post('length');
    $post['start'] = $this->input->post('start');
    $search = $this->input->post('search');
    $post['search_value'] = trim($search['value']);
    $post['order'] = $this->input->post('order');
    $post['draw'] = $this->input->post('draw');     
    $post['column_order'] = array('tOrders.OrderNumber','tOrders.LoanNumber','tOrders.MilestoneUID');
    $post['column_search'] = array('tOrders.OrderNumber','tOrders.LoanNumber','tOrders.MilestoneUID');

    $list = $this->MilestoneReport_Model->MilestoneReportOrders($post);   
    $Milestone = $this->Common_Model->Milestone();
    $no = $post['start'];
    $MilestoneReportorderslist = [];
    foreach ($list as $key => $revieworders)
    {
        $row = array();
        $row[] = $revieworders->OrderNumber;
        $row[] = $revieworders->LoanNumber;
        $row[] = $revieworders->MilestoneName;
        foreach ($Milestone as $key => $value) 
        {
         $row[] = $revieworders->{$value->MilestoneName};
        }
        $Action = '<div style="display: inline-flex;"><a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
        <i class="icon-pencil"></i></a>';
        $row[] = $Action;
      $MilestoneReportorderslist[] = $row;
    }

    $data =  array(
      'MilestoneReportorderslist' => ($MilestoneReportorderslist),
      'post' => $post
    );

    $post = $data['post'];

    $count_all = $this->MilestoneReport_Model->count_filtered($post);

    $output = array(
      "draw" => $post['draw'],
      "recordsTotal" => $this->MilestoneReport_Model->count_all(),
      "recordsFiltered" =>  $count_all,
      "data" => $data['MilestoneReportorderslist'],
    );

    unset($post);
    unset($data);
    echo json_encode($output);
  }

  function MilestoneExcelExport()
  {
    if($this->input->post('formData') == 'All')
    {
      $post['advancedsearch'] = 'false';
    }
    else
    {
      $post['advancedsearch'] = $this->input->post('formData');
    }
    $list = $this->MilestoneReport_Model->MilestoneExcelRecords($post);
    $Milestone = $this->Common_Model->Milestone();
    $data = [];
    $HeaderData = [];

      $HeaderData[] = 'Order Number';
      $HeaderData[] = 'Loan Number';
      $HeaderData[] = 'Milestone';

    foreach ($Milestone as $key => $value) 
    {
      $HeaderData[] = $value->MilestoneName;
    }

    $data[] = $HeaderData;

    for ($i=0; $i < sizeof($list); $i++){ 
      $MilestoneData = [];
        $MilestoneData[] = $list[$i]->OrderNumber;
        $MilestoneData[] = $list[$i]->LoanNumber;
        $MilestoneData[] = $list[$i]->MilestoneName;

        foreach ($Milestone as $key => $value) 
        {
          $MilestoneData[] = $list[$i]->{$value->MilestoneName};
        }
        
    $data[] = $MilestoneData;
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

