 <?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Permissions extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Permissions_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['PermissionsDetails'] = $this->Permissions_Model->GetPermissionsDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	
	function permission_ajax_list()
	{
		$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        $post['column_order'] = array('mResources.FieldName','mPermissions.PermissionName','mPermissions.SectionName', 'mPermissions.PermissionFieldName');
		$post['column_search'] = array( 'mResources.FieldName','mPermissions.PermissionName','mPermissions.SectionName', 'mPermissions.PermissionFieldName');

			$list = $this->Permissions_Model->paginationpermission($post);
//print_r($list);exit();
        $no = $post['start'];
        $permissionlist = [];
        
		foreach ($list as $permission)
        {
		        $row = array();
		      
		        $row[] = $permission->FieldName;
		        $row[] = $permission->PermissionName;
		        $row[] = $permission->SectionName;
		        $row[] = $permission->PermissionFieldName;
		       
				
 				$row[]=' <span style="text-align: center;width:100%;">
          <a href="'. base_url("Permissions/UpdatePermissions/".$permission->PermissionUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a> 
        </span>';
		       
		        $permissionlist[]= $row;
		       
        }



        $data =  array(
        	'permissionlist' => $permissionlist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Permissions_Model->count_all(),
			"recordsFiltered" =>  $this->Permissions_Model->count_filtered($post),
			"data" => $data['permissionlist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function AddPermissions()
	{
		$data['content'] = 'AddPermissions';
		$data['GetResources'] = $this->Permissions_Model->GetResourcesDet();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function UpdatePermissions()
	{
		$data['UpdatePermissions'] = $this->db->select("*")->from("mPermissions")->where(array('PermissionUID'=>$this->uri->segment(3)))->get()->row();
		$data['content'] = 'UpdatePermissions';
		$data['GetResources'] = $this->Permissions_Model->GetResourcesDet();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	

	function SavePermissions()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ResourceUID', '', 'required');
			$this->form_validation->set_rules('PermissionName', '', 'required');
			$this->form_validation->set_rules('PermissionFieldName', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');

			
			$post = $this->input->post();
			/*echo'<pre>';print_r($post);exit(); */
			$data = [];
			
			$data['ResourceUID'] = $post['ResourceUID'];
			$data['PermissionName']=$post['PermissionName'];
			$data['SectionName']=$post['SectionName'];
			$data['PermissionFieldName']=$post['PermissionFieldName'];

			if ($this->form_validation->run() == true) 
			{
				$result=$this->Permissions_Model->SavePermissions($data);
				if( $result== 1)
				{
					
					$res = array('Status' => 0,'message'=>'Permissions added Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'Permissions added Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'ResourceUID' => form_error('ResourceUID'),
					'PermissionName' => form_error('PermissionName'),
					'PermissionFieldName' => form_error('PermissionFieldName'),
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

	function UpdatePermissionsSave()
	{
		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('ResourceUID', '', 'required');
			$this->form_validation->set_rules('PermissionName', '', 'required');
			$this->form_validation->set_rules('PermissionFieldName', '', 'required');

			$this->form_validation->set_message('required', 'This Field is required');

			
			$post = $this->input->post();
			/*echo'<pre>';print_r($post);exit(); */
			$data = [];
			
			$data['ResourceUID'] = $post['ResourceUID'];
			$data['PermissionName']=$post['PermissionName'];
			$data['SectionName']=$post['SectionName'];
			$data['PermissionUID'] = $post['PermissionUID'];
			$data['PermissionFieldName']=$post['PermissionFieldName'];
			$data['Active']=isset($post['Active']) ? 1 : 0;

			if ($this->form_validation->run() == true) 
			{
				$result=$this->Permissions_Model->UpdatePermissionsSave($data);
				if( $result== 1)
				{
					
					$res = array('Status' => 2,'message'=>'Permissions Update Successsfully');
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 0,'message'=>'Permissions Update Successsfully');
					echo json_encode($res);exit();
				}
			}
			else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'ResourceUID' => form_error('ResourceUID'),
					'PermissionName' => form_error('PermissionName'),
					'PermissionFieldName' => form_error('PermissionFieldName'),
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

} 

?>