<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class AutomationLog extends MY_Controller {

	function __construct()
	{
		
		parent::__construct();
		$this->load->model('Automationlogmodel');
		$this->load->model('Efax/Efax_model');

	}

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function log_ajax_list()
	{
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['column_order'] = array('tOrders.OrderNumber', 'tOrders.LoanNumber', 'tAutomationLog.AutomationType','tAutomationLog.AutomationStatus', 'tAutomationLog.CreatedDate');
		$post['column_search'] = array('tOrders.OrderUID', 'tOrders.OrderNumber', 'tOrders.LoanNumber', 'tAutomationLog.AutomationType','tAutomationLog.AutomationStatus', 'tAutomationLog.CreatedDate');
		$list = $this->Automationlogmodel->Func_LogOrders($post);

		$no = $post['start'];
		$automationloglist = [];
		foreach ($list as $log)
		{
			$AutomationStatus = $log->AutomationStatus;
			if($log->AutomationStatus == 'Failure'){
				$AutomationStatus = '<span style="color:red;">'.$log->AutomationStatus.'</span><input type="hidden" name="OrderUID" value="'.$log->OrderUID.'">';
			}

			if($log->EFaxDataUID){
				$Actiondata='';
				$Actiondata.= '<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_metadata" title="Get Meta Data" data-logid="'.$log->EFaxDataUID.'" data-orderid="'.$log->OrderUID.'"><i class="icon-database-time2"></i></a>';
				/*$Actiondata.= '<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_faximage" title="Get Fax Image" data-logid="'.$log->EFaxDataUID.'"> <i class="icon-file-text"></i></a>';*/
			} else if($log->EmailUID || $log->EmailUID == '0'){
				$Actiondata = '<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_emaildata" data-logid="'.$log->EmailUID.'" data-orderid="'.$log->OrderUID.'"><i class="icon-database-time2"></i></a>';
			} else if($log->DocumentUID){
				$Actiondata = '<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_loandata" data-logid="'.$log->DocumentUID.'" data-orderid="'.$log->OrderUID.'"><i class="icon-database-time2"></i></a>';
			}

			$row = array();
			$row[] = '<a href="'.base_url('OrderLoanFile/index/'.$log->OrderUID).'" target="_blank" class="ajaxload">'.$log->OrderNumber.'</a>';
			$row[] = $log->LoanNumber;		        
			$row[] = $log->AutomationType;       
			$row[] = $AutomationStatus;
			$row[] = site_datetimeformat($log->CreatedDate);
			$Action = '<div style="display: inline-flex;">'.$Actiondata.'
			<a href="'.base_url('OrderLoanFile/index/'.$log->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>
			</div>';
			$row[] = $Action;
			$automationloglist[] = $row;
		}
		$data =  array(
			'automationloglist' => $automationloglist,
			'post' => $post
		);
		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Automationlogmodel->count_all(),
			"recordsFiltered" =>  $this->Automationlogmodel->count_filtered($post),
			"data" => $data['automationloglist'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	function WriteExcel()
	{
		if($this->input->post('formData') == 'All')
		{
			$post['advancedsearch'] = 'false';
			$this->Automationlogmodel->GetAutomationLogExcelRecords($post);
		}
		else{
			$post['advancedsearch'] = $this->input->post('formData');
		}
		$list = $this->Automationlogmodel->GetAutomationLogExcelRecords($post);
		$data = [];
		$data[] = array('Order No', 'Loan No', 'Automation Type', 'Automation Status', 'Date Time');
		for ($i=0; $i < sizeof($list); $i++) { 
			$data[] = array($list[$i]->OrderNumber, $list[$i]->LoanNumber, $list[$i]->AutomationType, $list[$i]->AutomationStatus, site_datetimeformat($list[$i]->CreatedDate));				
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
			fputcsv($output, $row);
		}
		fclose($output);
		ob_flush();
	}

	function GetOCRResponse(){
		$post = $this->input->post();
		$DocumentUID = $post['DocumentUID'];
		$OrderUID = $post['OrderUID'];
		$OCRResponse = $this->Automationlogmodel->GetOCRResponse($post);
		
		if($OCRResponse){
			$OCR = $OCRResponse->OCR;
			$response = json_decode($OCR,true);
			if($response){
				$list = $this->Common_Model->array_to_list($response);
			}else{
				$list = 'No response found';
			}
			
			$res = array('status' => 1, 'response' => $list);
		} else {
			$res = array('status' => 0);
		}

		echo json_encode($res);
	}
	/* Desc: DOC-617 Show email content for success mails in automation List @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
	function GetEmailResponse(){
		$post = $this->input->post();
		$EmailResponse = $this->Automationlogmodel->GetEmailResponse($post);
		if($EmailResponse){
			$MessageBody = $EmailResponse->EmailBody;			
			$res = array('status' => 1, 'html' => $MessageBody);
		} else {
			$res = array('status' => 0);
		}

		echo json_encode($res);
	}
	/* Desc: DOC-617 Show email content for success mails in automation List @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 7th 2020 */
}?>
