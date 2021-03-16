<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Waitingforimages extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Waitingforimages_Model');
	}

	public function index()
	{
		$data['content']='index';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function waitingforimages_ajax_list()
	{

		//get_post_input_data
    	$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
    	//get_post_input_data
    	//column order
		$post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName', 'mLender.LenderName', 'mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode', 'mProjectCustomer.ProjectName');
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName', 'mLender.LenderName', 'mStatus.StatusName', 'tOrders.PropertyAddress1', 'tOrders.PropertyCityName', 'tOrders.PropertyStateCode', 'tOrders.PropertyZipCode', 'mProjectCustomer.ProjectName');
        //column order
        $list = $this->Waitingforimages_Model->MyOrders($post);

        $no = $post['start'];
        $waitingforimageslist = [];
		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->CustomerName;

		        $row[] = '<a href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;" class="btn  btn-round mt-10">'.$myorders->StatusName.'</a>';
		        $row[] = $myorders->PropertyAddress1.' '.$myorders->PropertyAddress2;
		        $row[] = $myorders->PropertyCityName;
		        $row[] = $myorders->PropertyCountyName;
		        $row[] = $myorders->PropertyStateCode;
		        $row[] = $myorders->PropertyZipCode;
		        $row[] = $myorders->ProjectName;
		        $Action = '<a href="'.base_url('Ordersummary/index/'.$myorders->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
							<i class="icon-pencil"></i></a>';
		        $row[] = $Action;
		        $waitingforimageslist[] = $row;
        }



        $data =  array(
        	'waitingforimageslist' => $waitingforimageslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Waitingforimages_Model->count_all(),
			"recordsFiltered" =>  $this->Waitingforimages_Model->count_filtered($post),
			"data" => $data['waitingforimageslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}





}?>
