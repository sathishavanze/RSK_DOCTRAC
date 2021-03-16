<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class County extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Countymodel');
		$this->load->library('form_validation');
	}
	function index(){
		//$this->load->view('state_view');
		$data['content'] = 'County_view';
		$data['DocumentDetails']=$this->Countymodel->GetDocument();
		$data['County']=$this->Countymodel->GetState();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	
	function AddProject()
	{
		$data['content'] = 'County_view';
	    $data['DocumentDetails']=$this->Countymodel->GetDocument();
		$data['County']=$this->Countymodel->GetState();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function Edit()
	{

		$data['DocumentDetails']=$this->db->select("*")->from("mCounties")->where(array('CountyUID'=>$this->uri->segment(3)))->get()->row();
		$data['County']=$this->Countymodel->GetState();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page',$data);

	}

	function Save()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('CountyCode', '', 'required');
			$this->form_validation->set_rules('CountyCode', '', 'required');
			$this->form_validation->set_rules('StateUID', '', 'required');
		
			if ($this->form_validation->run() == true) 
			{

				if($this->Countymodel->SaveCounties($this->input->post()) == 1)
				{

					$res = array('Status' => 0,'message'=>'User added Successsfully');
					echo json_encode($res);exit();
				}
				else{
					$res = array('Status' => 2,'message'=>'User added Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'CountyCode' => form_error('CountyCode'),
					'CountyName' => form_error('CountyName'),
					'StateUID' => form_error('StateUID'),
					'type' => 'danger',
					);

				/*	echo '<pre>';print_r($data);exit;*/

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				//$res = array('Status' => 4,'detailes'=>$data);
				echo json_encode($data);
			}
			
		}	
	}
	function Update(){
		if($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_error_delimiters('','');
			$this->form_validation->set_rules('CountyCode','','required');
			$this->form_validation->set_rules('CountyName','','required');
			$this->form_validation->set_rules('StateUID','','required');
			if($this->form_validation->run()==true)
			{
				$CountyUID=$this->input->post('CountyUID');
				if($this->Countymodel->Update_County($this->input->post())==1)
				{
					$res=array('Status'=>2,'message'=>'user update Successsfully');
					echo json_encode($res);exit();

				}
				else{
					$res=array('Status'=>1,'message'=>'Update failed');
					echo json_encode($res);exit();
				}
			}
			else{
				$Msg=$this->lang->line('Empty_Validation');
				$data=array(
					'Status' => 1,
					'message' =>$Msg,
					'CountyCodeUpdate' => form_error('CountyCode'),
					'CountyNameUpdate' => form_error('CountyName'),
					'StateUIDUpdate'  =>form_error('StateUID'),

					);
				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);exit();
			}


		}
	}
	function getdatabyCountyUID()
	{
		$CountyUID = $this->input->post('CountyUID');
		$data  = $this->Countymodel->getdatabyCountyUID($CountyUID);
		echo json_encode($data);exit();
	}

}
?>
