<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocsReceived extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('DocsReceived_Model');
		$this->load->library('form_validation');
		$this->load->model('Ordersummary/Ordersummarymodel');
	}	

	public function index()
	{
		$data['content'] = 'index';
		$OrderUID = $this->uri->segment(3);
		
		$data['tDocuments'] = $this->DocsReceived_Model->getDocsReceived();
		// echo "<pre>";print_r($data['tDocuments']);exit();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);

	}
	function AddDocsReceived()
	{
		$data['content']='adddocsreceived';
		
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function EditDocsReceived()
	{
		$data['Docdetails'] = $this->DocsReceived_Model->getDocReceived($this->uri->segment(3));
		$data['content']="updatedocsreceived";
		$this->load->view($this->input->is_ajax_request()?$data['content'] : 'page',$data);
	}

	function UpdateDocsReceived()
	{	

		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('RecipientEmail', 'Receiver email', 'required');
			$this->form_validation->set_rules('EmailSubject', 'Subject', 'required');
			$this->form_validation->set_rules('EmailBody', 'Email Content', 'required');

			if($this->form_validation->run() == true)
			{
				$config['allowed_types'] = 'pdf|docx|xls|xlsx';
				$config['upload_path'] = 'uploads/email_extracts';
				$config['encrypt_name'] = true;
				$config['overwrite'] = true;
				$this->load->library('upload', $config);
				$upload = $this->upload->do_upload('file');
				if($upload){
					$data = $this->upload->data();
					
					$file_name = explode('.', $data['file_name']);//echo '<pre>';print_r($data);exit;
					$path = 'uploads/email_extracts/'.$data['file_name'];

					$Doc = array('DocumentName' => $data['orig_name'], 
                				'DocumentURL'=> (base_url().$path),
                				'OrderUID'=> 0,
                				'IsStacking'=> 1,
                				'TypeofDocument'=> 'Others',
                				'DocumentStorage'=> (base_url().$path),
                				'UploadedDateTime'=> date('Y-m-d H:i:s'),
                			);
					if($this->input->post('DocumentUID')){
						
						$this->db->update('tDocuments', $Doc, ['DocumentUID' => ($this->input->post('DocumentUID')) ]);
						$DocumentUID = $this->input->post('DocumentUID');
					}else{
						$this->db->insert('tDocuments',$Doc);
                		$DocumentUID = $this->db->insert_id();
					}
				}		

					$this->load->library('email');
					$this->config->load('email', FALSE, TRUE);

						$from_email = "notifications@direct2title.com"; 
         				$this->email->from($from_email);					
	        		

						$to_email = $this->input->post('RecipientEmail');
						$subject = $this->input->post('EmailSubject');
						$content = $this->input->post('EmailBody');
						
						$this->email->from($from_email);
						$this->email->to($to_email);
	         			$this->email->subject($subject); 
	         			$this->email->message($content); 
	         			if($upload){
	         				$file = base_url().$path;
							$this->email->attach($file, 'attachment',$data['orig_name'],$file_name[1]);	
						}
	         			//Send mail 
	         			if($this->email->send()){
	         				$status =  "Success";	         			
	         			}else{
	         				$status =  "Failure";
	         			}
						if($to_email){
							$log  = array('RecipientEmail' => ($this->input->post('RecipientEmail')),
								'EmailSubject'=> $this->input->post('EmailSubject'),
								'EmailBody'=> $this->input->post('EmailBody'),
								'IsReceived'=> $status,
								'OrderUID'=> '0',
								'DocumentUID' => ($DocumentUID ? $DocumentUID : ($this->input->post('DocumentUID') ? $this->input->post('DocumentUID') : 0)),
								'ImportedDateTime'=> date('Y-m-d H:i:s')
							);
							if($this->input->post('EmailUID')){
								$where = array('EmailUID' => $this->input->post('EmailUID'));
								
								$this->db->update('tEmailImport', $log, $where);
							}else{
								$this->db->insert('tEmailImport', $log);
							}
						}
						
					$res = array('Status' => 0,'message'=>'Updated Successsfully','type' => 'success');
					echo json_encode($res);exit();
				
			}
			else
			{
				$msg = 'Please fill required fields';
				$data = array(
					'Status' => 1,
					'message' => $msg			
				);

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
			
		}	
		
	}
    public function DeleteDocsReceived()
	{
		$Id = $this->input->post('dlt-id');
		$result = $this->DocsReceived_Model->deleteDocs($Id);
		if($result)
		{
			$Msg = $this->lang->line('Delete');
			$res = array("validation_error" => 1,'message' => $Msg);
		}
		else
		{
			$Msg = $this->lang->line('Error');
			$res = array("validation_error" => 0,'message' =>$Msg);
		}
		echo json_encode($res);
	}
	
	
}?>
