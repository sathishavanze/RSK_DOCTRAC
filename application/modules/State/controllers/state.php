<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class State extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Statemodel');
		$this->load->library('form_validation');
	}
	function index(){
		//$this->load->view('state_view');
		$data['content'] = 'state_view';
		$data['DocumentDetails']=$this->Statemodel->product_list();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	
	function AddProject()
	{
		$data['content'] = 'state_view';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function Edit()
	{

		$data['DocumentDetails']=$this->db->select("*")->from("mStates")->where(array('StateUID'=>$this->uri->segment(3)))->get()->row();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page',$data);
	}

	function Save()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{

			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('StateCode', '', 'required');
			$this->form_validation->set_rules('StateName', '', 'required');
			$this->form_validation->set_rules('FIPSCode', '', 'required');
			$this->form_validation->set_rules('State', '', 'required');
			$this->form_validation->set_rules('StateWebsite', '', 'required');
			$this->form_validation->set_rules('StatePhoneNumber', '', 'required');
			$this->form_validation->set_rules('Active', '', '');

			if ($this->form_validation->run() == true) 
			{
				$StateCode=$this->input->post('StateCode');
				$CheckIfExitStateCode = $this->Statemodel->CheckIfExitStateCode($StateCode) ;
				//echo '<pre>';print_r($CheckIfExitStateCode);exit;
				if($CheckIfExitStateCode == 0)
				{
					//echo 'hi';exit;
						$res=array('Status'=>3,'message'=>'StateCode Already Exit');
						echo json_encode($res);exit();
				}
				else
				{
					if($this->Statemodel->Savestates($this->input->post()) == 1)
					{

						$res = array('Status' => 0,'message'=>'User added Successsfully');
						echo json_encode($res);exit();
					}
					else{
						$res = array('Status' => 2,'message'=>'added failed');
						echo json_encode($res);exit();
					}
				}

				
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'StateCode' => form_error('StateCode'),
					'StateName' => form_error('StateName'),
					'FIPSCode' => form_error('FIPSCode'),
					'State' => form_error('State'),
					'StateWebsite' => form_error('StateWebsite'),
					'StatePhoneNumber' => form_error('StatePhoneNumber'),
					
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
			$this->form_validation->set_rules('StateCode','','required');
			$this->form_validation->set_rules('StateName','','required');
			$this->form_validation->set_rules('FIPSCode','','required');
			$this->form_validation->set_rules('StateEmail','','required');
			$this->form_validation->set_rules('StateWebsite','','required');
			$this->form_validation->set_rules('StatePhoneNumber','','required');
			$this->form_validation->set_rules('Active','','');
			if($this->form_validation->run()==true)
			{

				$StateUID=$this->input->post('StateUID');
					if($this->Statemodel->Update_states($this->input->post())==1)
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
					'StateCodeUpdate' => form_error('StateCode'),
					'StateNameUpdate' => form_error('StateName'),
					'FIPSCodeUpdate'  =>form_error('FIPSCode'),
					'StateWebsiteUpdate' =>form_error('StateWebsite'),
					'StateEmailUpdate' => form_error('StateEmail'),
					'StatePhoneNumberUpdate'=>form_error('StatePhoneNumber')

					);
				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}


		}
	}
	function getdatabyStateUID()
	{
		$StateUID = $this->input->post('StateUID');
		$data  = $this->Statemodel->getdatabyStateUID($StateUID);
		echo json_encode($data);
	}

}
?>
