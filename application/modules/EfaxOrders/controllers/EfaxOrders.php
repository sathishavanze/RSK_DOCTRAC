<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Listing of EFax Orders 
*@author Yagavi G <yagavi.g@avanzegroup.com>
*@since Date July 17th 2020
*/

class EfaxOrders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Efaxordersmodel');
		$this->load->model('Efax/Efax_model');

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['Projects'] = $this->Common_Model->GetProjectCustomers();
		$data['Clients'] = $this->Common_Model->GetClients();
		$data['Lenders'] = $this->Common_Model->GetLenders();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function efax_ajax_list()
	{
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['column_order'] = array('tOrders.OrderNumber', 'tEFaxData.FaxID','tEFaxData.FromFaxNumber','tEFaxData.ToFaxNumber','tEFaxData.Message', 'tEFaxData.TransmissionStatus', 'tEFaxData.FaxStatus', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime');
		$post['column_search'] = array('tOrders.OrderNumber', 'tEFaxData.FaxID','tEFaxData.FromFaxNumber','tEFaxData.ToFaxNumber','tEFaxData.Message', 'tEFaxData.TransmissionStatus', 'tEFaxData.FaxStatus', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime');
		$list = $this->Efaxordersmodel->Func_EFaxOrders($post);

		$no = $post['start'];
		$efaxorderslist = [];
		foreach ($list as $revieworders)
		{
			$row = array();
			$row[] = '<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="ajaxload">'.$revieworders->OrderNumber.'</a>';
			$row[] = $revieworders->CustomerName;
			$row[] = $revieworders->FaxID;
			//$row[] = $revieworders->FromFaxNumber;
			$row[] = $revieworders->ToFaxNumber;
			$row[] = $revieworders->TransmissionStatus;
			$row[] = $revieworders->FaxStatus;
			$row[] = $revieworders->LoanNumber;		        
			$row[] = site_datetimeformat($revieworders->ModifiedDate);
			$Action = '<div style="display: inline-flex;">
			<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_metadata" title="Get Meta Data" data-faxid="'.$revieworders->EFaxDataUID.'"><i class="icon-database-time2"></i></a>
			<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" target="_blank" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>
			</div>';
			$row[] = $Action;
			$efaxorderslist[] = $row;
		}
		$data =  array(
			'efaxorderslist' => $efaxorderslist,
			'post' => $post
		);
		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Efaxordersmodel->count_all(),
			"recordsFiltered" =>  $this->Efaxordersmodel->count_filtered($post),
			"data" => $data['efaxorderslist'],
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
			$this->Efaxordersmodel->GetEfaxOrdersExcelRecords($post);
		}
		else{
			$post['advancedsearch'] = $this->input->post('formData');
		}
		$list = $this->Efaxordersmodel->GetEfaxOrdersExcelRecords($post);
		$data = [];
		$data[] = array('OrderNo', 'Client', 'FaxID', 'ToFaxNumber', 'TransmissionStatus', 'FaxStatus', 'LoanNo', 'LastModifiedDateTime');
		for ($i=0; $i < sizeof($list); $i++) { 
			$data[] = array($list[$i]->OrderNumber, $list[$i]->CustomerName, $list[$i]->FaxID, $list[$i]->ToFaxNumber, $list[$i]->TransmissionStatus, $list[$i]->FaxStatus, $list[$i]->LoanNumber, site_datetimeformat($list[$i]->ModifiedDate));				
		}
		$this->outputCSV($data);
	}

	function FaxWriteExcel()
	{
		$data['EventCode'] = 'ListOfFaxSent';
		$OrgDetails = $this->Efax_model->GetEFaxCredentials();
		$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
		$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
		$data['advancedsearch'] = $this->input->post('formData');

		$str.='transmission_status='.$data['advancedsearch']['transmission_status'];

		if($data['advancedsearch']['transaction_id']){
			$str.='&transaction_id='.$data['advancedsearch']['transaction_id'];
		}
		if($data['advancedsearch']['fax_status']){
			$str.='&fax_status='.$data['advancedsearch']['fax_status'];
		}
		if($data['advancedsearch']['image_downloaded']){
			$str.='&image_downloaded='.$data['advancedsearch']['image_downloaded'];
		}
		if($data['advancedsearch']['originating_fax_number']){
			$str.='&originating_fax_number='.$data['advancedsearch']['originating_fax_number'];
		}
		if($data['advancedsearch']['destination_fax_number']){
			$str.='&destination_fax_number='.$data['advancedsearch']['destination_fax_number'];
		}
		if($data['advancedsearch']['error_code']){
			$str.='&error_code='.$data['advancedsearch']['error_code'];
		}
		if($data['advancedsearch']['min_pages']){
			$str.='&min_pages='.$data['advancedsearch']['min_pages'];
		}
		if($data['advancedsearch']['max_pages']){
			$str.='&max_pages='.$data['advancedsearch']['max_pages'];
		}
		if($data['advancedsearch']['search_text']){
			$str.='&search_text='.$data['advancedsearch']['search_text'];
		}
		if($data['advancedsearch']['min_completed_timestamp']){
			$str.='&min_completed_timestamp='.date(DATE_W3C,strtotime($data['advancedsearch']['min_completed_timestamp']));
		}
		if($data['advancedsearch']['max_completed_timestamp']){
			$str.='&max_completed_timestamp='.date(DATE_W3C,strtotime($data['advancedsearch']['max_completed_timestamp']));
		}

		$data['params'] = '?'.$str;
		$list = $this->Efax_model->Func_FaxDetails($data);
		$data = [];
		$data[] = array('fax_id', 'size', 'duration', 'pages', 'image_downloaded', 'fax_status', 'completed_timestamp', 'originating_fax_number', 'destination_fax_number', 'routing_data_to_name', 'routing_data_to_company', 'routing_data_subject', 'transmission_data_error_code', 'transmission_data_error_message', 'transmission_data_transmission_status');
		for ($i=0; $i < sizeof($list); $i++) { 
			$data[] = array($list[$i]['fax_id'], $list[$i]['size'], $list[$i]['duration'], $list[$i]['pages'], $list[$i]['image_downloaded'], $list[$i]['fax_status'], site_datetimeformat($list[$i]['completed_timestamp']), $list[$i]['originating_fax_number'], $list[$i]['destination_fax_number'], $list[$i]['routing_data_to_name'], $list[$i]['routing_data_to_company'], $list[$i]['routing_data_subject'], $list[$i]['transmission_data_error_code'], $list[$i]['transmission_data_error_message'], $list[$i]['transmission_data_transmission_status']);				
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

	function GetMetaDataByFaxID()
	{
		$post = $this->input->post();
		$EFaxDataUID = $post['EFaxDataUID'];
		$FaxDetails = $this->Efaxordersmodel->GetEFaxOrdersByFaxID($EFaxDataUID);
		$data['EventCode'] = 'SingleMetaDateRetrieve';
		$data['OrderUID'] = $FaxDetails->OrderUID;
		$data['OrderNumber'] = $FaxDetails->TransactionID;
		$data['FaxID'] = $FaxDetails->FaxID;
		$OrgDetails = $this->Efaxordersmodel->GetEFaxCredentials();
		$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
		$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
		$fax_list = $this->Efax_model->Func_FaxDetails($data);
		$list = $this->Efaxordersmodel->array_to_list($fax_list);
		echo json_encode($list);
	}

	function GetFaxImageByFaxID()
	{
		$post = $this->input->post();
		$EFaxDataUID = $post['EFaxDataUID'];
		$FaxDetails = $this->Efaxordersmodel->GetEFaxOrdersByFaxID($EFaxDataUID);
		$data['EventCode'] = 'FaxImageRetrieve';
		$data['OrderUID'] = $FaxDetails->OrderUID;
		$data['OrderNumber'] = $FaxDetails->TransactionID;
		$data['FaxID'] = $FaxDetails->FaxID;
		$OrgDetails = $this->Efaxordersmodel->GetEFaxCredentials();
		$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
		$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
		$fax_list = $this->Efax_model->Func_FaxDetails($data);

		if(isset($fax_list['errors'])){
			$list = $this->Efaxordersmodel->array_to_list($fax_list);
			$res = array('status' => 0, 'error' => $list);
		} else {

			$filename =  'FaxImage-'.$faxid.'.pdf';
			$basepath = FCPATH.'uploads/Efax_files/';
			$path = $basepath.$filename;
			$filepath = 'uploads/Efax_files/'.$filename;
			$this->Efaxordersmodel->CreateDirectoryToPath($basepath);
			$image = base64_decode($fax_list['image']); 
			file_put_contents($path, $image);

			$FaxID = $data['FaxID'];
			$update_tEFaxData = array(
				'IsFaxImageReceived' => 1,
				'DocumentURL' => $filepath,
			);
			$this->db->where('FaxID',$FaxID);
			$this->db->update('tEFaxData',$update_tEFaxData);
			unset($update_tEFaxData);
			$res = array('status' => 1, 'url' => $path, 'filename' => $filename, 'filepath' => $filepath, 'FaxID' => $FaxID);
		}

		echo json_encode($res);
	}

	function live_efax_ajax_list()
	{
		$data['EventCode'] = 'ListOfFaxSent';
		$OrgDetails = $this->Efax_model->GetEFaxCredentials();
		$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
		$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
		$data['advancedsearch'] = $this->input->post('formData');
		$str = '';
		$str.='?transmission_status='.$data['advancedsearch']['transmission_status'];

		if($data['advancedsearch']['transaction_id']){
			$str.='&transaction_id='.$data['advancedsearch']['transaction_id'];
		}
		if($data['advancedsearch']['fax_status']){
			$str.='&fax_status='.$data['advancedsearch']['fax_status'];
		}
		if($data['advancedsearch']['image_downloaded']){
			$str.='&image_downloaded='.$data['advancedsearch']['image_downloaded'];
		}
		if($data['advancedsearch']['originating_fax_number']){
			$str.='&originating_fax_number='.$data['advancedsearch']['originating_fax_number'];
		}
		if($data['advancedsearch']['destination_fax_number']){
			$str.='&destination_fax_number='.$data['advancedsearch']['destination_fax_number'];
		}
		if($data['advancedsearch']['error_code']){
			$str.='&error_code='.$data['advancedsearch']['error_code'];
		}
		if($data['advancedsearch']['min_pages']){
			$str.='&min_pages='.$data['advancedsearch']['min_pages'];
		}
		if($data['advancedsearch']['max_pages']){
			$str.='&max_pages='.$data['advancedsearch']['max_pages'];
		}
		if($data['advancedsearch']['search_text']){
			$str.='&search_text='.$data['advancedsearch']['search_text'];
		}
		if($data['advancedsearch']['min_completed_timestamp']){
			$str.='&min_completed_timestamp='.date(DATE_W3C,strtotime($data['advancedsearch']['min_completed_timestamp']));
		}
		if($data['advancedsearch']['max_completed_timestamp']){
			$str.='&max_completed_timestamp='.date(DATE_W3C,strtotime($data['advancedsearch']['max_completed_timestamp']));
		}

		$data['params'] = '?'.$str;
		$fax_list = $this->Efax_model->Func_FaxDetails($data);

		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');

		$no = $post['start'];
		$efaxorderslist = [];

		if(isset($fax_list['errors'])){
		} else {
			foreach ($fax_list as $fax)
			{
				$row = array();
				$row[] = $fax['fax_id'];
				$row[] = $fax['size'];
				$row[] = $fax['duration'];
				$row[] = $fax['pages'];
				$row[] = $fax['image_downloaded'];
				$row[] = $fax['fax_status'];
				$row[] = site_datetimeformat($fax['completed_timestamp']);
				$row[] = $fax['originating_fax_number'];
				$row[] = $fax['destination_fax_number'];
				$row[] = $fax['routing_data_to_name'];
				$row[] = $fax['routing_data_to_company'];
				$row[] = $fax['routing_data_subject'];
				$row[] = $fax['transmission_data_error_code'];
				$row[] = $fax['transmission_data_error_message'];
				$row[] = $fax['transmission_data_transmission_status'];
				$efaxorderslist[] = $row;
			}
		}

		$data =  array(
			'efaxorderslist' => $efaxorderslist,
			'post' => $post
		);
		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => count($fax_list),
			"data" => $data['efaxorderslist'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	function StoreDocuments(){
		$post = $this->input->post();
		$path = $post['imageURL'];
		$OrderUID = $post['OrderUID'];
		$FaxImageName = $post['FaxImageName'];
		$Doc = array(
			'DocumentName' => $FaxImageName, 
			'DocumentURL'=> $path,
			'OrderUID'=> $OrderUID,
			'IsStacking'=> 0,
			'TypeofDocument'=> 'EFaxImage',
			'DocumentStorage'=> $path,
			'UploadedDateTime'=> date('Y-m-d H:i:s'),
		);
		$this->db->insert('tDocuments',$Doc);
		$DocumentUID = $this->db->insert_id();
		if($DocumentUID){
			echo json_encode($DocumentUID);
		}
	}

	/**
	* Function to retrive the fax images from e-Fax Integration
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return List of Fax in Array
	* @since July 14th 2020
	* @version E-Fax Integration
	*
	*/

	function receive_efax_ajax_list()
	{
		$data['EventCode'] = 'ListOfFaxReceived';
		/* Get Crendential of Efax from mSetting table */
		$OrgDetails = $this->Efax_model->GetEFaxCredentials();
		$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
		$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
		$data['advancedsearch'] = $this->input->post('formData');
		/* Start - Setting Param for receiving fax list*/
		$str = '';
		$str.='?image_downloaded='.$data['advancedsearch']['image_downloaded'];
		if($data['advancedsearch']['originating_fax_number']){
			$str.='&originating_fax_number='.$data['advancedsearch']['originating_fax_number'];
		}
		if($data['advancedsearch']['destination_fax_number']){
			$str.='&destination_fax_number='.$data['advancedsearch']['destination_fax_number'];
		}
		if($data['advancedsearch']['min_pages']){
			$str.='&min_pages='.$data['advancedsearch']['min_pages'];
		}
		if($data['advancedsearch']['max_pages']){
			$str.='&max_pages='.$data['advancedsearch']['max_pages'];
		}
		if($data['advancedsearch']['search_text']){
			$str.='&search_text='.$data['advancedsearch']['search_text'];
		}
		if($data['advancedsearch']['min_completed_timestamp']){
			$str.='&min_completed_timestamp='.date(DATE_W3C,strtotime($data['advancedsearch']['min_completed_timestamp']));
		}
		if($data['advancedsearch']['max_completed_timestamp']){
			$str.='&max_completed_timestamp='.date(DATE_W3C,strtotime($data['advancedsearch']['max_completed_timestamp']));
		}
		/* Start - Setting Param for receiving fax list*/

		$data['params'] = '?'.$str;
		$fax_list = $this->Efax_model->Func_FaxDetails($data); /* Posting the values and get the Fax List from Efax Intergration */
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');

		$no = $post['start'];
		$efaxorderslist = [];

		if(isset($fax_list['errors'])){
			/* Any Error throws from EFax then the value should be empty */
		} else {
			/* If Fax List Received from EFax */
			foreach ($fax_list as $fax)
			{
				$row = array();
				$row[] = $fax['fax_id'];
				$row[] = $fax['size'];
				$row[] = $fax['duration'];
				$row[] = $fax['pages'];
				$row[] = $fax['image_downloaded'];
				$row[] = $fax['fax_status'];
				$row[] = site_datetimeformat($fax['completed_timestamp']);
				$row[] = $fax['originating_fax_number'];
				$row[] = $fax['destination_fax_number'];
				$Action = '<div style="display: inline-flex;">
				<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_receive_metadata" title="Get Meta Data" data-faxid="'.$fax['fax_id'].'"><i class="icon-database-time2"></i></a>
				<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_receive_faximage" title="Get Fax Image" data-faxid="'.$fax['fax_id'].'"> <i class="icon-file-text"></i></a>
				</div>';
				$row[] = $Action;
				$efaxorderslist[] = $row;
			}
		}

		$data =  array(
			'efaxorderslist' => $efaxorderslist,
			'post' => $post
		);
		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => count($fax_list),
			"data" => $data['efaxorderslist'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	/**
	* Function to retrive the meta data of single FaxID from e-Fax Integration
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return List of Fax in Key/Value Structure
	* @since July 17th 2020
	* @version E-Fax Integration
	*
	*/

	function GetReceiveMetaDataByFaxID(){
		$post = $this->input->post();
		$faxid = $post['faxid'];
		$data['EventCode'] = 'SingleMetaDateRetrieve';
		$data['FaxID'] = $faxid;
		$OrgDetails = $this->Efaxordersmodel->GetEFaxCredentials(); /* Get Crendential of Efax from mSetting table */
		$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
		$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
		$fax_list = $this->Efax_model->Func_FaxDetails($data); /* Posting the values and get the Fax List from Efax Intergration */
		$list = $this->Efaxordersmodel->array_to_list($fax_list); /* Converting the Array into Key/Value Structure */
		echo json_encode($list);
	}

	/**
	* Function to retrive the fax image of single FaxID from e-Fax Integration
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return JSON
	* @since July 17th 2020
	* @version E-Fax Integration
	*
	*/

	function GetReceiveFaxImageByFaxID()
	{
		$post = $this->input->post();
		$faxid = $post['faxid'];
		$FaxDetails = $this->Efaxordersmodel->GetEFaxDetailsByFaxID($faxid); 

		if(!empty($FaxDetails) && !empty($FaxDetails->DocumentURL) && file_exists(FCPATH.'uploads/Efax_files/FaxImage-'.$faxid.'.pdf')){
			$FaxID = $faxid;
			$filepath = $FaxDetails->DocumentURL;
			$filename =  'FaxImage-'.$faxid.'.pdf';
			$path = FCPATH.$filepath;
			$res = array('status' => 1, 'url' => $path, 'filename' => $filename, 'filepath' => $filepath, 'FaxID' => $faxid);
		} else {
			$OrgDetails = $this->Efaxordersmodel->GetEFaxCredentials(); /* Get Crendential of Efax from mSetting table */
			$data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
			$data['ClientUserID'] = $OrgDetails['EFaxUserID'];
			$data['EventCode'] = 'FaxImageRetrieve';
			$data['FaxID'] = $faxid;
			$fax_list = $this->Efax_model->Func_FaxDetails($data); /* Posting the values and get the Fax List from Efax Intergration */

			if(isset($fax_list['errors'])){
				/* @If EFax Failed to get Fax Images */
				$list = $this->Efaxordersmodel->array_to_list($fax_list); /* Converting the Array into Key/Value Structure */
				$res = array('status' => 0, 'error' => $list);
			} else {
				/* @If EFax Success to get Fax Images */
				$filename =  'FaxImage-'.$faxid.'.pdf';
				$basepath = FCPATH.'uploads/Efax_files/';
				$path = $basepath.$filename;
				$filepath = 'uploads/Efax_files/'.$filename;
				$this->Efaxordersmodel->CreateDirectoryToPath($basepath); /* Create Directory Path */
				$image = base64_decode($fax_list['image']); /* Decoding the Fax Encoded Image from EFax*/
				file_put_contents($path, $image);

				$FaxID = $data['FaxID'];
				$update_tEFaxData = array(
					'IsFaxImageReceived' => 1,
					'DocumentURL' => $filepath,
				);
				$this->db->where('FaxID',$FaxID);
				$this->db->update('tEFaxData',$update_tEFaxData); /*Update into table once the Fax Image is received */
				unset($update_tEFaxData);

				$res = array('status' => 1, 'url' => $path, 'filename' => $filename, 'filepath' => $filepath, 'FaxID' => $faxid);
			}
		}

		echo json_encode($res);
	}

	/**
	* Function to retrive the fax list from e-Fax Integration
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return Array
	* @since July 20th 2020
	* @version E-Fax Ithegration
	*
	*/

	function efax_receive_ajax_list()
	{
		$post['advancedsearch'] = $this->input->post('formData');
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		$post['column_order'] = array('tEFaxData.FaxID','tEFaxData.FromFaxNumber','tEFaxData.ToFaxNumber','tEFaxData.Message', 'tEFaxData.TransmissionStatus', 'tEFaxData.FaxStatus');
		$post['column_search'] = array('tEFaxData.FaxID','tEFaxData.FromFaxNumber','tEFaxData.ToFaxNumber','tEFaxData.Message', 'tEFaxData.TransmissionStatus', 'tEFaxData.FaxStatus');
		$list = $this->Efaxordersmodel->Func_ReceiveEFaxOrders($post);

		$no = $post['start'];
		$efaxorderslist = [];
		foreach ($list as $receivefaxdetails)
		{
			$row = array();
			$row[] = $receivefaxdetails->FaxID;
			$row[] = $receivefaxdetails->FromFaxNumber;
			$row[] = $receivefaxdetails->ToFaxNumber;
			$row[] = $receivefaxdetails->TransmissionStatus;
			$row[] = $receivefaxdetails->FaxStatus;
			$row[] = $receivefaxdetails->LoanNumber;		        
			$row[] = site_datetimeformat($receivefaxdetails->ModifiedDate);
			$Action = '<div style="display: inline-flex;">
			<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs btn_receive_metadata" title="Get Meta Data" data-faxid="'.$receivefaxdetails->FaxID.'"><i class="icon-database-time2"></i></a>
			<a href="javascript:void(0)" class="btn btn-link btn-info btn-just-icon btn-xs receive_btn_faximage" title="Get Fax Image" data-faxid="'.$receivefaxdetails->FaxID.'"> <i class="icon-file-text"></i></a>
			</div>';
			$row[] = $Action;
			$efaxorderslist[] = $row;
		}
		$data =  array(
			'efaxorderslist' => $efaxorderslist,
			'post' => $post
		);
		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Efaxordersmodel->receive_fax_count_all(),
			"recordsFiltered" =>  $this->Efaxordersmodel->receive_count_filtered($post),
			"data" => $data['efaxorderslist'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}

	/**
	* Function to add fax image to Order
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return JSON
	* @since July 20th 2020
	* @version E-Fax Integration
	*
	*/

	function AddFaxImageToOrder(){
		$post = $this->input->post();
		$filepath = $post['filepath'];
		$filename = $post['filename'];
		$FaxID = $post['faxID'];
		$OrderUID = $post['orderuid'][0];
		$FaxDetails = $this->Efaxordersmodel->GetEFaxDetailsByFaxID($FaxID);

		if($OrderUID){
			$update_tEFaxData = array(
				'OrderUID' => $OrderUID
			);
			$this->db->where('FaxID',$FaxID);
			$this->db->update('tEFaxData',$update_tEFaxData);
			unset($update_tEFaxData);

			$Doc = array(
				'DocumentName' => $filename, 
				'DocumentURL'=> $filepath,
				'OrderUID'=> $OrderUID,
				'IsStacking'=> 0,
				'TypeofDocument'=> 'EFaxImage',
				'DocumentStorage'=> $filepath,
				'UploadedDateTime'=> date('Y-m-d H:i:s'),
			);
			$this->db->insert('tDocuments',$Doc);
			$DocumentUID = $this->db->insert_id();
			unset($Doc);
			$res = array('status' => 1);
		} else {
			$res = array('status' => 0, 'msg' => 'OrderUID is Empty');
		}
		echo json_encode($res);
	}

	/**
	* Function to extract address
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return Array
	* @since July 20th 2020
	* @version E-Fax Integration
	*
	*/

	function ExtractInsuranceAddress(){
		$add = "9132 PINE MISSION AVE LAS VEGAS NV 89143";
		$res = $this->Common_Model->ExtractAddress($add);
		echo '<pre>';print_r($res);exit;
	}

	/**
	* Function to split PDF paged
	*
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return nothing
	* @since July 21th 2020
	* @version E-Fax Integration
	*
	*/
	function CheckSplitPDF(){
		$original_filepath = FCPATH.'uploads/Efax_files/1.pdf';
		$pages = '2-3';
		$temp_filepath = FCPATH.'uploads/Efax_files/test.pdf';
		$res = $this->Common_Model->SplitPDF($original_filepath, $pages, $temp_filepath);
		echo '<pre>';print_r($res);exit;
	}

}?>
