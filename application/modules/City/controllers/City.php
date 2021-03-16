 <?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class City extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('City_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['getstate'] = $this->City_Model->GetState();
		//$data['GetCityDetails'] = $this->City_Model->GetCityDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}


	function city_ajax_list()
	{
		$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        $post['column_order'] = array('mCities.CityName', 'mStates.StateName','mCounties.CountyName','mCities.ZipCode');
		$post['column_search'] =  array('mCities.CityName', 'mStates.StateName','mCounties.CountyName','mCities.ZipCode');

			$list = $this->City_Model->citypagination($post);
//print_r($list);exit();
        $no = $post['start'];
        $citylist = [];
        
		foreach ($list as $city)
        {
		        $row = array();
		     
		        $row[] = $city->CityName;
		        $row[] = $city->StateName;
		        $row[] = $city->CountyName;
		        $row[] = $city->ZipCode;
 				$row[]='<a data-idcity ="'.$city->CityUID.'" class="btn btn-link btn-info btn-just-icon btn-xs updatecity" ><i class="icon-pencil"></i></a>';
		       
		        $citylist[]= $row;
		      
        }



        $data =  array(
        	'citylist' => $citylist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->City_Model->count_all(),
			"recordsFiltered" =>  $this->City_Model->count_filtered($post),
			"data" => $data['citylist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	public function Getcountys()
	{
		$stateid =$this->input->Post('stateuid');
		$getcounty = $this->City_Model->Getcounty($stateid);	
		$html = '';
		foreach ($getcounty as $key => $value) {
			$html .= '<option value = "'.$value->CountyUID.'">'.$value->CountyName . '</option>'; 
		}

		echo $html;
	}
	public function Updatecity(){
		$cityuid=$this->input->post('cityuid');
		$getupdatecity=$this->City_Model->Updatecity($cityuid);

		$getcounty = $this->City_Model->Getcounty($getupdatecity[0]->StateUID);	
		$html = '';
		foreach ($getcounty as $key => $value) {
			$html .= '<option value = "'.$value->CountyUID.'">'.$value->CountyName . '</option>'; 
		}

		$getcounty[0]->Counties = $html;
		// print_r($getcounty);
		// echo $html;

		echo json_encode (['updateresponse'=>$getupdatecity[0], 'counties'=>$html]);
	}
	
	function SaveCity()
	{

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('city_name', '', 'required');
			$this->form_validation->set_rules('StateUID', '', 'required');
			$this->form_validation->set_rules('CountyUID', '', 'required');
			$this->form_validation->set_rules('zipcode', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');

			
			$post = $this->input->post();
			$data = [];
			$Result =$this->CheckDuplicate($post);

			if ($Result == 1) {
				$res = array('Status' => 10,'message'=>'Already Exists In City Details');
					echo json_encode($res);exit();
			}
			
			$data['city_name'] = $post['city_name'];
			$data['StateUID']=$post['StateUID'];
			$data['CountyUID']=$post['CountyUID'];
			$data['zipcode']=$post['zipcode'];
			if ($this->form_validation->run() == true) 
			{
				$result=$this->City_Model->cityadd($data);
				if( $result== 1)
				{
					
					$res = array('Status' => 0,'message'=>'City added Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'Not added Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'city_name' => form_error('city_name'),
					'StateUID' => form_error('StateUID'),
					'CountyUID' => form_error('CountyUID'),
					'zipcode' => form_error('zipcode'),
					'type' => 'danger',
				);


				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
			

		}

	}

	function Updatecitysave()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('Upcity_name', '', 'required');
			$this->form_validation->set_rules('UpStateUID', '', 'required');
			$this->form_validation->set_rules('UpCountyUID', '', 'required');
			$this->form_validation->set_rules('Upzipcode', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');

			
			$post = $this->input->post();
			$data = [];
			$data['UpCityUID'] = $post['UpCityUID'];
			$data['Upcity_name'] = $post['Upcity_name'];
			$data['UpStateUID']=$post['UpStateUID'];
			$data['UpCountyUID']=$post['UpCountyUID'];
			$data['Upzipcode']=$post['Upzipcode'];
			if ($this->form_validation->run() == true) 
			{
				$result=$this->City_Model->Updatecitysave($data);
				if( $result== 1)
				{
					
					$res = array('Status' => 0,'message'=>'City added Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'Not added Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'Upcity_name' => form_error('Upcity_name'),
					'UpStateUID' => form_error('UpStateUID'),
					'UpCountyUID' => form_error('UpCountyUID'),
					'Upzipcode' => form_error('Upzipcode'),
					'type' => 'danger',
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
			

		}

	}

	public function checkloginid($LoginID){
		$UserUID = $this->UserUID;
		$query = $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID != '$UserUID'"); 
		if($query->num_rows() > 0)
		{
			$this->form_validation->set_message('checkloginid', 'Login ID Already Taken');
			return false;
			
		}
		else
		{
			return true;
		}
	}
	public function CheckDuplicate($post){
		$city_name =$this->input->post('city_name');
		$StateUID  =$this->input->post('StateUID');
		$CountyUID  =$this->input->post('CountyUID');
		$zipcode  =$this->input->post('zipcode');
		$this->db->select('*');
		$this->db->from('mCities');
		$Check = array('CityName'=>$city_name,'StateUID'=>$StateUID,'CountyUID'=>$CountyUID,'ZipCode'=>$zipcode);
		$this->db->where($Check);
		$query = $this->db->count_all_results();
		if ($query > 0) {
			return 1;
		}
		else{
			return 0;
		}

	}

} 

?>