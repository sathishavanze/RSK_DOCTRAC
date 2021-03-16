<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Followup extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Followup_Model');
		$this->lang->load('keywords');
		ini_set('display_errors', 1);
		ini_set('memory_limit', -1);

	}	

	public function index()
	{
		$data['content']='index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function followup_ajax_list()
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		//Advanced Search
		//get_post_input_data
    	$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = trim($search['value']);
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        $post['filter'] = $this->input->post('filter');
    	//get_post_input_data
    	//column order
        $post['column_order'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber','tOrders.LoanNumber','FollowUpStartDateTime','FollowUpEndDateTime','RaisedUserUID','CompletedUserUID','CompletedType','Remarks','FollowUpStatus','FollowUpType');
        $post['column_search'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber','FollowUpStartDateTime','FollowUpEndDateTime','RaisedUserUID','CompletedUserUID','CompletedType','Remarks','FollowUpStatus','FollowUpType');
        //column order
        $list = $this->Followup_Model->GetFollowupOrders($post);

        $no = $post['start'];
        $myorderslist = [];
		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->PackageNumber;
		        $row[] = date('Y-m-d H:i:s', strtotime($myorders->FollowUpStartDateTime));
		        $RaisedUserUID=$this->Followup_Model->getFollowupUser($myorders->RaisedUserUID);
		        $row[] = $RaisedUserUID->UserName;
		        $row[] = $myorders->Remarks;
		        $Action = '<a href="'.base_url('Ordersummary/index/'.$myorders->OrderUID).'"class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>';
		        if($myorders->FollowUpStatus == 'Pending')
		        {
		        	$row[]='<button class="btn  btn-info">'.$myorders->FollowUpStatus.'</button>';

		        }
		        else
		        {
		        	$row[]='<button class="btn  btn-success">'.$myorders->FollowUpStatus.'</button>';
		        }
		        $row[] = $myorders->FollowUpType;
		        $row[] = $Action;
		        $myorderslist[] = $row;
        }



        $data =  array(
        	'myorderslist' => $myorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Followup_Model->Pending_count_all(),
			"recordsFiltered" =>  $this->Followup_Model->count_filtered($post),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}
	function Completed_ajax_list()
	{
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
		//Advanced Search
		//get_post_input_data
		$post['filter'] = $this->input->post('filter');
    	$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = trim($search['value']);
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
    	//get_post_input_data
    	//column order
        $post['column_order'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber','tOrders.LoanNumber','FollowUpStartDateTime','FollowUpEndDateTime','RaisedUserUID','CompletedUserUID','CompletedType','Remarks','CompletedCommand','FollowUpStatus','FollowUpType');
        $post['column_search'] = array('tOrders.OrderNumber','tOrderPackage.PackageNumber','FollowUpStartDateTime','FollowUpEndDateTime','RaisedUserUID','CompletedUserUID','CompletedType','Remarks','CompletedCommand','FollowUpStatus','FollowUpType');
        //column order
        $list = $this->Followup_Model->GetFollowupOrders($post);

        $no = $post['start'];
        $myorderslist = [];
		foreach ($list as $myorders)
        {
		        $row = array();
		        $row[] = $myorders->OrderNumber;
		        $row[] = $myorders->PackageNumber;
		        $row[] = date('Y-m-d H:i:s', strtotime($myorders->FollowUpStartDateTime));
		        if($myorders->FollowUpEndDateTime == '0000-00-00 00:00:00')
		        {
		        	 $row[] = '-';
		        }
		        else
		        {
		        	$row[] = date('Y-m-d H:i:s', strtotime($myorders->FollowUpEndDateTime));
		        }
		        $RaisedUserUID=$this->Followup_Model->getFollowupUser($myorders->RaisedUserUID);
		        $row[] = $RaisedUserUID->UserName;
		        $CompletedUserUID=$this->Followup_Model->getFollowupUser($myorders->CompletedUserUID);
		        $row[] = $CompletedUserUID->UserName;
		        if($myorders->CompletedType == 'null')
		        {
		        	$row[] = '-';
		        }
		        else
		        {
		        	$row[] = $myorders->CompletedType;
		        }
		        $row[] = $myorders->Remarks;
		        $row[] = $myorders->CompletedCommand;
		        $Action = '<a href="'.base_url('Ordersummary/index/'.$myorders->OrderUID).'"class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>';
		        if($myorders->FollowUpStatus == 'Pending')
		        {
		        	$row[]='<button class="btn  btn-info">'.$myorders->FollowUpStatus.'</button>';

		        }
		        else
		        {
		        	$row[]='<button class="btn  btn-success">'.$myorders->FollowUpStatus.'</button>';
		        }
		        $row[] = $myorders->FollowUpType;
		        $row[] = $Action;
		        $myorderslist[] = $row;
        }



        $data =  array(
        	'myorderslist' => $myorderslist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Followup_Model->Completed_count_all(),
			"recordsFiltered" =>  $this->Followup_Model->count_filtered($post),
			"data" => $data['myorderslist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function WriteCSV()
	{
		$details=$this->input->post('formData');
		if( $details['advancesearch']== 'All')
		{
			$post['advancedsearch'] = 'false';
		}
		else{

			$post['advancedsearch'] = $this->input->post('formData');
		}

		$list = $this->Followup_Model->GetMyOrdersExcelRecords($post);
		if($post['advancedsearch']['filterlist'] == 'Completed')
		{
			$data = [];

			$data[] =array('OrderNumber','PackageNumber','LoanNumber','FollowUpStartDateTime','FollowUpEndDateTime','RaisedUser','CompletedUser','CompletedType','Remarks','Commands','FollowUpStatus','FollowUpType');
			for ($i=0; $i < sizeof($list); $i++) { 

				$RaisedUser = $this->Followup_Model->getFollowupUser($list[$i]->RaisedUserUID);
				$CompletedUser = $this->Followup_Model->getFollowupUser($list[$i]->CompletedUserUID);
				$data[] = array($list[$i]->OrderNumber,$list[$i]->PackageNumber,$list[$i]->LoanNumber,$list[$i]->FollowUpStartDateTime,$list[$i]->FollowUpEndDateTime,$RaisedUser->UserName,$CompletedUser->UserName,$list[$i]->CompletedType,$list[$i]->Remarks,$list[$i]->CompletedCommand,$list[$i]->FollowUpStatus,$list[$i]->FollowUpType);

			}
		}
		else if ($post['advancedsearch']['filterlist'] == 'Pending') {
			$data = [];

			$data[] =array('OrderNumber','PackageNumber','LoanNumber','FollowUpStartDateTime','RaisedUser','Comments','FollowUpStatus','FollowUpType');
			for ($i=0; $i < sizeof($list); $i++) { 

				$RaisedUser = $this->Followup_Model->getFollowupUser($list[$i]->RaisedUserUID);
				$data[] = array($list[$i]->OrderNumber,$list[$i]->PackageNumber,$list[$i]->LoanNumber,$list[$i]->FollowUpStartDateTime,$RaisedUser->UserName,$list[$i]->Remarks,$list[$i]->FollowUpStatus,$list[$i]->FollowUpType);

			}
		}
		if($details['download_format'] == 'csv')
		{
			$this->outputCSV($data);
		}
		else if($details['download_format'] == 'excel' and $post['advancedsearch']['filterlist'] =='Completed')
		{
			$this->outputExcel($list);
		}
		else if($details['download_format'] == 'excel' and $post['advancedsearch']['filterlist'] =='Pending')
		{
			$this->outputPendingExcel($list);
		}
//$this->outputCSV($data);
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

		function outputExcel($data)
		{
			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$ColumnArray = array('A','B','C','D','E','F','G','H','I','J','K');
			$ColumnArray = array(
				'A'=>'Prop No',
				'B'=>'PackageNumber',
				'C'=>'LoanNumber',
				'D'=>'FollowUpStartDateTime',
				'E'=>'FollowUpEndDateTime',
				'F'=>'RaisedUser',
				'G'=>'CompletedUser',
				'H'=>'CompletedType',
				'I'=>'Remarks',
				'J'=>'FollowUpStatus',
				'K'=>'FollowUpType',

			);
			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$no, $value->OrderNumber);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$no, $value->PackageNumber);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$no, $value->LoanNumber);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$no, $value->FollowUpStartDateTime);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$no, $value->FollowUpEndDateTime);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$no, $value->RaisedUserUID);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$no, $value->CompletedUserUID);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$no, $value->CompletedType);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$no, $value->Remarks);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$no, $value->CompletedCommand);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$no, $value->FollowUpStatus);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$no, $value->FollowUpType);
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Followup.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}

		function outputPendingExcel($data)
		{
			$this->load->library('Excel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$ColumnArray = array('A','B','C','D','E','F','G','H');
			$ColumnArray = array(
				'A'=>'Prop No',
				'B'=>'PackageNumber',
				'C'=>'LoanNumber',
				'D'=>'FollowUpStartDateTime',
				'E'=>'RaisedUser',
				'F'=>'Remarks',
				'G'=>'FollowUpStatus',
				'H'=>'FollowUpType',

			);
			foreach ($ColumnArray as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$no=2;
			foreach ($data as $value) {

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$no, $value->OrderNumber);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$no, $value->PackageNumber);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$no, $value->LoanNumber);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$no, $value->FollowUpStartDateTime);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$no, $value->RaisedUserUID);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$no, $value->Remarks);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$no, $value->FollowUpStatus);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$no, $value->FollowUpType);
				$no++;
			}
			ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Followup.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');

		}
	function audit_faild_followup()
	{
		$comments=$this->input->post('comments');
		$OrderUID=$this->input->post('OrderUID');
		//followup update
		$Followup_Details = array('OrderUID'=>$OrderUID,
								'FollowUpStartDateTime'=>Date('Y-m-d H:i:s', strtotime("now")),
								'RaisedUserUID'=>$this->loggedid,
								'Remarks'=>$comments,
								'FollowUpStatus'=>'Pending',
								'FollowUpType'=>'Audit Failed');

		$this->load->library('form_validation');


        $this->form_validation->set_error_delimiters('', '');


        $this->form_validation->set_rules('OrderUID', '', 'required');
        $this->form_validation->set_rules('comments', '', 'required');

        $this->form_validation->set_message('required', 'This Field is required');
        if ($this->form_validation->run() == true) {
		$result=$this->Common_Model->UpdateFollowup($Followup_Details,$OrderUID);
		if($this->db->affected_rows() > 0)
        {
            $result = array("validation_error" => 0, 'message' => 'Followup Completed Successfully', 'type' => 'success');
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($result))->_display(); exit;
        }else{
           $result = array("validation_error" => 1, 'message' => 'Can not be Save', 'type' => 'warning');
           $this->output->set_content_type('application/json')
            ->set_output(json_encode($result))->_display(); exit;
        }
		}else{
            $Msg = $this->lang->line('Empty_Validation');

            $formvalid = [];

            $validation_data = array(
                'validation_error' => 1,
                'message' => $Msg,
                'OrderUID' => form_error('OrderUID'),
                'comments' => form_error('comments'),
            );
            foreach ($validation_data as $key => $value) {
                if (is_null($value) || $value == '')
                    unset($validation_data[$key]);
            }
            $this->output->set_content_type('application/json')
            ->set_output(json_encode($validation_data))->_display(); exit;

        }
	}

	function CompleteFollowup()
	{
		$CompletedType = $this->input->post('Followuptype');
		$CompleteCommand = $this->input->post('Comments');
		$OrderUID = $this->input->post('OrderUID');
		$Followup_Details = array(
								'FollowUpEndDateTime'=>Date('Y-m-d H:i:s', strtotime("now")),
								'CompletedUserUID'=>$this->loggedid,	
								'FollowUpStatus'=>'Completed',
								'CompletedType' => $CompletedType,
								'CompletedCommand' => $CompleteCommand
								);
		$result = $this->Followup_Model->Update_Complete_Followup($OrderUID,$Followup_Details);
		echo $result;
	}
}
?>