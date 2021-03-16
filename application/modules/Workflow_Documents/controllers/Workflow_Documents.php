<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Workflow_Documents extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Workflow_Documents_Model');
		$this->load->model('Customer/Customer_Model');
		$this->load->library('form_validation');
		$this->load->config('keywords');
	}

	public function index()
	{

		$data['content'] = 'index';

		$data['Customer_Workflow'] = $this->Customer_Model->get_customer_workflow_details();

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function Update_Workflow_Documents()
	{
		$data['content'] = 'Update_Workflow_Documents';

		$data['WorkFlowDetails'] = $this->db->select("*")->from("mWorkFlowModules")->where(array('WorkflowModuleUID'=>$this->uri->segment(3)))->get()->row();
		$data['WorkflowDocuments'] = $this->Common_Model->GetWorkflowDocuments($this->uri->segment(3));

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function Upload_Workflow_Document(){

		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
		$WorkflowDocumentPath = "uploads/WorkflowDocumentPath";
		$this->Common_Model->CreateDirectoryToPath($WorkflowDocumentPath);
		$config['allowed_types'] = 'pdf|docx|xls|xlsx|gif|png|jpg|jpeg';
		$config['upload_path'] = $WorkflowDocumentPath;
		$config['encrypt_name'] = true;
		$config['overwrite'] = true;
		$this->load->library('upload', $config);
		$upload = $this->upload->do_upload('file');

		if($upload){
			$data = $this->upload->data();

			$file_name = explode('.', $data['file_name']);
			$path = $WorkflowDocumentPath.'/'.$data['file_name'];

			$Doc = array(
				'WorkflowModuleUID' => $WorkflowModuleUID,
				'DocumentName' => $data['orig_name'], 
				'DocumentURL'=> ($path),
				'UploadedByUserUID' => $this->loggedid,
				'UploadedDateTime'=> date('Y-m-d H:i:s')
			);

			$this->db->insert('mWorkflowDocuments',$Doc);
			
			if ($this->db->affected_rows() == 1) {
				
				$res = array('message'=>'Updated', 'status'=>"0");
			} else {

				$res = array('message'=>'Failed to Update', 'status'=>"1");
			}

		}else{

			$res = array('message'=>'Error Uploading file', 'status'=>"1");
		}

		echo json_encode($res);exit();
	}

	function DeleteWorkflowDocument(){
		$DocumentUID = $this->input->post('DocumentUID');
		$WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

		if(!empty($DocumentUID) && !empty($WorkflowModuleUID)){

			$WorkflowDocuments = $this->Common_Model->GetWorkflowDocuments($WorkflowModuleUID, $DocumentUID);

			if (!empty($WorkflowDocuments)) {
				
				if (file_exists($WorkflowDocuments[0]->DocumentURL) && !unlink($WorkflowDocuments[0]->DocumentURL)) {  

					$res = array('message'=>$WorkflowDocuments[0]->DocumentName.'DocumentName cannot be deleted due to an error', 'status'=>"1");
				}  else {

					$this->db->where(array('mWorkflowDocuments.WorkflowModuleUID' => $WorkflowModuleUID, 'DocumentUID' => $DocumentUID));
					$this->db->delete('mWorkflowDocuments');

					$res = array('message'=>$WorkflowDocuments[0]->DocumentName.' has been deleted', 'status'=>"0");
				}
			} else {

				$res = array('message'=>'Document Details Not Found', 'status'=>"1");
			}			

		} else {

			$res = array('message'=>'Something went wrong', 'status'=>"1");
		}

		echo json_encode($res);exit();
		
	}

} 

?>