<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocumentCheckList_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GettOrders($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tOrders');
		$this->db->where(array('tOrders.OrderUID'=>$OrderUID));
		$query = $this->db->get();
		return $query->row();
	}

	function GetDocuments($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tDocuments');
		$this->db->join('mUsers','mUsers.UserUID = tDocuments.UploadedByUserUID','left');
		$this->db->where(array('tDocuments.OrderUID'=>$OrderUID));
		$query = $this->db->get();
		return $query->result();
	}
	function GetDocumentCheck($post,$BorrowerNameArray)
	{
		$ClosingDateTime = (strtotime($post['ClosingDateTime'])) ? Date('Y-m-d',strtotime($post['ClosingDateTime'])) : NULL;
		$DisbursementDate = (strtotime($post['DisbursementDate'])) ? Date('Y-m-d',strtotime($post['DisbursementDate'])) : NULL;

		$document = array('FileNumber'=>$post['FileNumber'],'RecordingFeesDeed'=>$post['RecordingFeesDeed'],'RecordingFeesMortgage'=>$post['RecordingFeesMortgage'],'IncomingTrackingNumber'=>$post['IncomingTrackingNumber'],'OrderUID'=>$post['OrderUID'],'ClosingDateTime'=>$ClosingDateTime,'DisbursementDate'=>$DisbursementDate);

		$InvestorUID = $post['InvestorName'];
		if(!empty($InvestorUID)){
			$InvestorDts  = $this->db->select('*')->from('mInvestors')->where('mInvestors.InvestorUID',$InvestorUID)->get()->row();
			$document['InvestorNo'] = $InvestorDts->InvestorNo ;
			$document['InvestorName'] = $InvestorDts->InvestorName;
			$document['InvestorLoanNumber'] = $InvestorDts->InvestorLoanNumber;

		}

		$CustodianUID = $post['CustodianName'];
		if(!empty($CustodianUID)){
			$CustodianDts  = $this->db->select('*')->from('mCustodians')->where('mCustodians.CustodianUID',$CustodianUID)->get()->row();
			$document['CustodianName'] = $CustodianDts->CustodianName;
			$document['CustodianCode'] = $CustodianDts->CustodianNo;
			$document['CustodianLoanNumber'] = $CustodianDts->CustodianLoanNumber;
		}

		$SettlementAgentUID = $post['SettlementAgentName'];
		if(!empty($SettlementAgentUID)){
			$SettlementAgentDts  = $this->db->select('*')->from('mSettlementAgent')->where('mSettlementAgent.SettlementAgentUID',$SettlementAgentUID)->get()->row();
			$document['SettlementAgentName'] = $SettlementAgentDts->SettlementAgentName;
			$document['SettlementAgentPhone'] = $SettlementAgentDts->SettlementAgentPhone;
			$document['SettlementAgentFax'] = $SettlementAgentDts->SettlementAgentFax;
			$document['SettlementAgentEmail'] = $SettlementAgentDts->SettlementAgentEmail;
		}

		$BussinessChannel = $post['BussinessChannel'];
		if(!empty($BussinessChannel)){
			$BussinessChannelDts  = $this->db->select('*')->from('mBusinessChannel')->where('mBusinessChannel.BusinessChannelUID',$BussinessChannel)->get()->row();
			$document['BussinessChannel'] = $BussinessChannelDts->BusinessChannelName;
		}

		$LoanDetUpdateOrders =array('LoanAmount'=>$post['LoanAmount'],'LoanPurpose'=>$post['loanpurpose'],'LoanType'=>$post['loantype']);
// print_r($LoanDetUpdateOrders);exit;
		$CheckTorderDocCheckIn  = $this->db->select('*')->from('tOrderDocumentCheckIn')->where('tOrderDocumentCheckIn.OrderUID',$post['OrderUID'])->get()->row();
		if(!empty($CheckTorderDocCheckIn)){
			$this->db->where('OrderUID',$post['OrderUID']);
	// $this->db->delete('tOrderDocumentCheckIn');
			$this->db->update('tOrderDocumentCheckIn',$document);
			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($post['OrderUID'],'Doc Check-In Updated',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
		}else{
			$this->db->insert('tOrderDocumentCheckIn',$document);
			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($post['OrderUID'],'Doc Check-In Added',Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/
		}




		$this->db->where('OrderUID',$post['OrderUID']);
		$this->db->update('tOrders',$LoanDetUpdateOrders);

		$this->db->where('OrderUID',$post['OrderUID']);
		$this->db->delete('tOrderPropertyRole');
		for ($i=0; $i <count($BorrowerNameArray) ; $i++) {
			if(!empty($BorrowerNameArray[$i]))
			{
				$borrower=array('BorrowerFirstName'=>$BorrowerNameArray[$i],'OrderUID'=>$post['OrderUID']);
				$this->db->insert('tOrderPropertyRole',$borrower);
			}


		}
		foreach ($BorrowerNameArray as $row) {
		}

		if($this->db->affected_rows() > 0)
		{

			return 1;
		}else{
			return 0;
		}
	}

	function insert_order($data)
	{

		$date = date('Ymd');

		$this->db->trans_begin();

		$insertdata = new stdClass();

		$insertdata->AltORderNumber = $data['AltORderNumber'];
		$insertdata->LoanNumber = $data['LoanNumber'];
		// $insertdata->LoanAmount = $data['LoanAmount'];
		$insertdata->CustomerReferenceNumber = $data['CustomerRefNum'];
		$insertdata->CustomerUID = $data['Customer'];
		$insertdata->PropertyAddress1 = $data['PropertyAddress1'];
		$insertdata->PropertyAddress2 = $data['PropertyAddress2'];
		$insertdata->PropertyCityName = $data['PropertyCityName'];
		$insertdata->PropertyStateCode = $data['PropertyStateCode'];
		$insertdata->PropertyCountyName = $data['PropertyCountyName'];
		$insertdata->PropertyZipcode = $data['PropertyZipcode'];
		$insertdata->ProjectUID = $data['ProjectUID'];
		// $insertdata->StatusUID = $this->config->item('keywords')['New Order'];
		// $insertdata->OrderEntryDatetime = Date('Y-m-d H:i:s', strtotime("now"));
		$insertdata->LenderUID = isset($data['LenderUID']) && !empty($data['LenderUID']) ? $data['LenderUID'] : 0;

		// $insertdata->EmailReportTo = $data['EmailReportTo'];
		// $insertdata->AttentionName = $data['AttentionName'];
		// $insertdata->APN = $data['APN'];
		// $insertdata->IsDuplicateOrder = $IsDuplicateOrder;

		//$insertdata->OrderNumber = $this->Order_Number();

		$OrderNos = [];
		// $insertdata->OrderDueDate = date('Y-m-d H:i:s');
		// $insertdata->OrderDocsPath = 'uploads/Documents/' . $date . '/' . $OrderNo . '/';
		$this->db->where(array("tOrders.OrderUID"=>$data['OrderUID']));    
		$query = $this->db->update('tOrders', $insertdata);
		$insert_id = $data['OrderUID'];


		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			$OrderNos[] = $this->db->select('OrderNumber')->from('tOrders')->where('tOrders.OrderUID',$data['OrderUID'])->get()->row()->OrderNumber;
		}

		$OrderNumbers = implode(",", $OrderNos);
		$Msg = $this->lang->line('Order_Update');
		$Rep_msg = str_replace("<<Order Number>>", $OrderNumbers, $Msg);
		return ['message'=>$Rep_msg, 'OrderUID'=>$insert_id, 'OrderNumber'=>$OrderNumbers];

	}

	
	function UpdateStatus($OrderUID,$status)
	{
		$this->db->set('StatusUID',$status);
		$this->db->where(array('OrderUID'=>$OrderUID));
		$this->db->update('tOrders');
	}

	function DeleteExistingDocument($documentuid)
	{
		$this->db->where('DocumentUID', $documentuid);
		$this->db->delete('tDocuments');
		if($this->db->affected_rows() > 0)
		{
			return true;
		}else{
			return false;
		}
	}
	function GetBorrowerName($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tOrderPropertyRole');
		$this->db->where(array('tOrderPropertyRole.OrderUID'=>$OrderUID));
		$query = $this->db->get();
		return $query->result();
	}
	function BorrowerDocumentDetails($OrderUID)
	{
		$this->db->select('*');
		$this->db->select('DATE(ClosingDateTime) AS ClosingDateTime, DATE(DisbursementDate) AS DisbursementDate', false);
		$this->db->from('tOrderDocumentCheckIn');
		$this->db->where(array('tOrderDocumentCheckIn.OrderUID'=>$OrderUID));
		$query = $this->db->get();
		return $query->result();
	}
	function insertDocumentCheckListPDF($PDF_Data)
	{
		$this->db->select('*');
		$this->db->from('tDocuments');
		$this->db->where('OrderUID',$PDF_Data['OrderUID']);
		$this->db->where('DocumentName',$PDF_Data['DocumentName']);
		$query = $this->db->get()->num_rows();
		if($query > 0)
		{
			$this->db->where('DocumentName',$PDF_Data['DocumentName']);
			$this->db->where('OrderUID',$PDF_Data['OrderUID']);
			$this->db->update('tDocuments',$PDF_Data);
		}
		else{
			$this->db->insert('tDocuments',$PDF_Data);
		}
		
		if($this->db->affected_rows())
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	function getDocument($OrderUID)
	{
	    $this->db->select('tDocuments.DocumentName,mInputDocType.DocTypeName,tDocuments.DocumentURL');
		$this->db->from('tDocuments');
		$this->db->join('tOrders','tOrders.OrderUID=tDocuments.OrderUID','left');
		$this->db->join('mInputDocType','mInputDocType.InputDocTypeUID=tOrders.InputDocTypeUID','left');
		$this->db->where(array('tDocuments.OrderUID'=>$OrderUID));
		$query = $this->db->get();
		return $query->result();
	}

	function getFolderIDs($OrderUID)
	{
		$tDocuments = $this->Common_Model->get('tDocuments', ['OrderUID'=>$OrderUID]);
		$FolderIDs = [];
		foreach ($tDocuments as $key => $doc) {
			
			if (!empty($doc->DocumentStorage)) {
				$FolderIDs[] = $doc->DocumentStorage;
			}
		}

		return implode(", ", $FolderIDs);

	}
}
?>
