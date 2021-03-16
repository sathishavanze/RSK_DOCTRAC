<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class ProcessorInfo extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ProcessorInfo_Model');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['ProcessorsDetails'] = $this->ProcessorInfo_Model->GetProcessorsDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	

	function addProcessorInfo()
	{
		$data['content'] = 'addProcessorInfo';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveProcessor()
	{		
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('FirstName', '', 'required');
			$this->form_validation->set_message('required', 'This Field is required');	

			$post = $this->input->post();

			if ($this->form_validation->run() == true) 
			{

				$result = $this->ProcessorInfo_Model->SaveProcessor($post);

				if($result)
				{
					if (isset($post['ProcessorUID']) && !empty($post['ProcessorUID'])) {
						
						$res = array('Status' => 0,'message'=>'Processor updated Successsfully');
					} else {

						$res = array('Status' => 0,'message'=>'Processor added Successsfully');
					}					
					
					echo json_encode($res);exit();
					
				}
				else{

					$res = array('Status' => 1,'message'=>'Processor save failed');
					echo json_encode($res);exit();
				}
			}
			else{

				$Msg = $this->lang->line('Empty_Validation');

				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'FirstName' => form_error('FirstName'),
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

	function updateProcessorInfo($ProcessorUID)
	{
		$data['ProcessorDetails'] = $this->ProcessorInfo_Model->GetProcessorsDetails($ProcessorUID);
		$data['content'] = 'addProcessorInfo';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function ExcelDownload(){

        set_include_path( get_include_path().PATH_SEPARATOR."..");

        require_once APPPATH."third_party/xlsxwriter.class.php";

        $post['advancedsearch'] = array('WorkflowModuleUID' => $this->input->post('WorkflowModuleUID'),
            'Category' => $this->input->post('Category'),
        );

        $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

        $writer = new XLSXWriter();

        $HEADER = [
        	'Name' => 'string',
        	'TeamLeader' => 'string',
        	'Manager' => 'string',
        	'VP' => 'string',
        	'PhoneNumber' => 'string',
        	'HoursofOperation' => 'string',
        	'TimeZone' => 'string',
        	'Status' => 'string'
        ];
        
        $writer->writeSheetHeader('Processors',$HEADER, $header_style);

        $ProcessorsDetails = $this->ProcessorInfo_Model->GetProcessorsDetails();
        
        foreach($ProcessorsDetails as $data) {

            $row = [];
            $row[] = $data->FirstName.', '.$data->LastName;
            $row[] = $data->TeamLeader;
            $row[] = $data->Manager;
            $row[] = $data->VP;
            $row[] = $data->PhoneNumber;
            $row[] = $data->HoursofOperation;
            $row[] = $data->TimeZone;
            $row[] = ($data->Active == 1 ) ? 'Active' : 'InActive';

            $writer->writeSheetRow('Processors', array_values($row));
        }

        $filename = 'Processors.xlsx';

        ob_clean();
        $writer->writeToFile($filename);
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename= '.$filename);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        header('Set-Cookie: fileDownload=true; path=/');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        unlink($filename);
        exit(0);

    }

} 

?>
