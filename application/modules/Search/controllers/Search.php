<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Search extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Search_Model');
	}	

	public function index() {
		$post['start'] = 0;
		$post['length'] = 100;
		$post['search_value'] = trim($this->input->post('search_value'));

    //column search
		$post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime','tOrderImport.LoanProcessor','tOrderPropertyRole.BorrowerFirstName','mUsers.UserName');

		$data['content'] = 'index';
		if (empty($post['search_value'])) {
			$table['PreScreen Orders'] = [];
			$table['Welcome Call'] = [];
			$table['Title Team Orders'] = [];
			$table['FHA/VA Orders'] = [];
			$table['Third Party Orders'] = [];
			$table['HOI Orders'] = [];
			//$table['Borrower Doc Orders'] = [];
			$table['PayOff Orders'] = [];
			$table['PE Orders'] = [];
			$table['CD Orders'] = [];
			$table['Final Approval Orders'] = [];
			$table['Doc Waiting'] = [];
			$table['Withdrawal Orders'] = [];
			$table['Doc Chase Orders'] = [];
			$table['Escalation Orders'] = [];
			$table['UnderWriter Orders'] = [];
			$table['Scheduling Orders'] = [];
			$table['Closing Orders'] = [];
			$table['Completed Orders'] = [];
			$table['Cancelled Orders'] = [];
			$table['ICD Orders'] = [];
			$table['Disclosures Orders'] = [];
			$table['NTB Orders'] = [];
			$table['FloodCert Orders'] = [];
			$table['Appraisal Orders'] = [];
			$table['Escrows Orders'] = [];
			$table['TwelveDayLetter Orders'] = [];
			$table['MaxLoan Orders'] = [];
			$table['POO Orders'] = [];
			$table['CondoQR Orders'] = [];
			$table['FHACaseAssignment Orders'] = [];
			$table['VACaseAssignment Orders'] = [];
			$table['VVOE Orders'] = [];
			$table['CEMA Orders'] = [];
			$table['SCAP Orders'] = [];
			$table['NLR Orders'] = [];
			$table['CTCFlipQC Orders'] = [];
			$table['PrefundAuditCorrection Orders'] = [];
			$table['AdhocTasks Orders'] = [];
			$table['UWClear Orders'] = [];
			$table['TitleReview Orders'] = [];
			$table['BorrowerDocs Orders'] = [];
			$table['GateKeeping Orders'] = [];
			$table['Submissions Orders'] = [];
			$data['AllOrders'] = $table;
		} else {
			
			$data['AllOrders'] = $this->Search_Model->getsearchorders($post);
		}

		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	/**
	* Click function to search order details
	*
	* @param SearchText (String)
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return Array
	* @since July 20th 2020
	* @version E-Fax Integration
	*
	*/

	function SearchOrders(){
		$post['search_value'] = trim($this->input->post('SearchText'));
        $post['column_search'] = array('tOrders.OrderNumber','mCustomer.CustomerName','tOrders.LoanNumber','mStatus.StatusName','tOrders.PropertyAddress1','tOrders.PropertyCityName','tOrders.PropertyStateCode','tOrders.PropertyZipCode');
		$data['content'] = 'search_results';
		if (empty($post['search_value'])) {
			$table['PreScreen Orders'] = [];
			$table['Welcome Call'] = [];
			$table['Title Team Orders'] = [];
			$table['FHA/VA Orders'] = [];
			$table['Third Party Orders'] = [];
			$table['HOI Orders'] = [];
			//$table['Borrower Doc Orders'] = [];
			$table['PayOff Orders'] = [];
			$table['PE Orders'] = [];
			$table['CD Orders'] = [];
			$table['Final Approval Orders'] = [];
			$table['Doc Waiting'] = [];
			$table['Withdrawal Orders'] = [];
			$table['Doc Chase Orders'] = [];
			$table['Escalation Orders'] = [];
			$table['UnderWriter Orders'] = [];
			$table['Scheduling Orders'] = [];
			$table['Closing Orders'] = [];
			$table['Completed Orders'] = [];
			$table['Cancelled Orders'] = [];
			$table['ICD Orders'] = [];
			$table['Disclosures Orders'] = [];
			$table['NTB Orders'] = [];
			$table['FloodCert Orders'] = [];
			$table['Appraisal Orders'] = [];
			$table['Escrows Orders'] = [];
			$table['TwelveDayLetter Orders'] = [];
			$table['MaxLoan Orders'] = [];
			$table['POO Orders'] = [];
			$table['CondoQR Orders'] = [];
			$table['FHACaseAssignment Orders'] = [];
			$table['VACaseAssignment Orders'] = [];
			$table['VVOE Orders'] = [];
			$table['CEMA Orders'] = [];
			$table['SCAP Orders'] = [];
			$table['NLR Orders'] = [];
			$table['CTCFlipQC Orders'] = [];
			$table['PrefundAuditCorrection Orders'] = [];
			$table['AdhocTasks Orders'] = [];
			$table['UWClear Orders'] = [];
			$table['TitleReview Orders'] = [];
			$table['BorrowerDocs Orders'] = [];
			$table['GateKeeping Orders'] = [];
			$table['Submissions Orders'] = [];
			$data['AllOrders'] = $table;
		}else{
			$table['PreScreen Orders'] = $this->PreScreen_Orders_Model->PreScreenOrders($post,'',1);
			$table['Welcome Call'] = $this->WelcomeCall_Orders_Model->WelcomeCallOrders($post,'',1);
			$table['Title Team Orders'] = $this->TitleTeam_Orders_Model->TitleTeamOrders($post,'',1);
			$table['FHA/VA Orders']  = $this->FHAVACaseTeam_Orders_Model->FHAVACaseTeamOrders($post,'',1);
			$table['Third Party Orders']  = $this->ThirdParty_Orders_Model->ThirdPartyOrders($post,'',1);
			$table['HOI Orders']  = $this->HOI_Orders_model->HOIorders($post,1);
			//$table['Borrower Doc Orders']  = $this->BorrowerDoc_Orders_model->BorrowerDocorders($post,1);
			$table['PayOff Orders']  = $this->PayOff_Orders_model->PayOfforders($post,1);
			$table['PE Orders']  = $this->PE_Orders_Model->PEOrders($post,1);
			$table['CD Orders']  = $this->CD_Orders_Model->CDOrders($post,1);
			$table['Final Approval Orders'] = $this->FinalApproval_Orders_Model->FinalApprovalOrders($post,1);
			$table['Doc Waiting']  = $this->DocWaitingmodel->DocWaitingReportOrders($post,1);
			$table['Withdrawal Orders']  = $this->Withdrawal_Orders_model->WithdrawalOrders($post,1);
			$table['Work Up Orders']  = $this->WorkUp_Orders_Model->WorkUpOrders($post,'',1);
			$table['Doc Chase Orders']  = $this->DocChase_Orders_Model->DocChaseOrders($post,'',1);
			$table['Escalation Orders']  = $this->Escalation_Orders_model->GetEsclationQueue($post,1);
			$table['UnderWriter Orders']  = $this->UnderWriter_Orders_Model->UnderWriterOrders($post,'',1);
			$table['Scheduling Orders']  = $this->Scheduling_Orders_Model->SchedulingOrders($post,'',1);
			$table['Closing Orders']  = $this->Closing_Orders_Model->ClosingOrders($post,'',1);
			$table['Completed Orders']  = $this->Completedordersmodel->CompletedOrders($post,1);
			$table['Cancelled Orders']  = $this->Cancelledordersmodel->CancelledOrders($post,1);
			$table['ICD Orders'] = $this->ICD_Orders_Model->ICDOrders($post,'',1);
			$table['Disclosures Orders'] = $this->Disclosures_Orders_Model->DisclosuresOrders($post,'',1);
			$table['NTB Orders'] = $this->NTB_Orders_Model->NTBOrders($post,'',1);
			$table['FloodCert Orders'] = $this->FloodCert_Orders_Model->FloodCertOrders($post,'',1);
			$table['Appraisal Orders'] = $this->Appraisal_Orders_Model->AppraisalOrders($post,'',1);
			$table['Escrows Orders'] = $this->Escrows_Orders_Model->EscrowsOrders($post,'',1);
			$table['TwelveDayLetter Orders'] = $this->TwelveDayLetter_Orders_Model->TwelveDayLetterOrders($post,'',1);
			$table['MaxLoan Orders'] = $this->MaxLoan_Orders_Model->MaxLoanOrders($post,'',1);
			$table['POO Orders'] = $this->POO_Orders_Model->POOOrders($post,'',1);
			$table['CondoQR Orders'] = $this->CondoQR_Orders_Model->CondoQROrders($post,'',1);
			$table['FHACaseAssignment Orders'] = $this->FHACaseAssignment_Orders_Model->FHACaseAssignmentOrders($post,'',1);
			$table['VACaseAssignment Orders'] = $this->VACaseAssignment_Orders_Model->VACaseAssignmentOrders($post,'',1);
			$table['VVOE Orders'] = $this->VVOE_Orders_Model->VVOEOrders($post,'',1);
			$table['CEMA Orders'] = $this->CEMA_Orders_Model->CEMAOrders($post,'',1);
			$table['SCAP Orders'] = $this->SCAP_Orders_Model->SCAPOrders($post,'',1);
			$table['NLR Orders'] = $this->NLR_Orders_Model->NLROrders($post,'',1);
			$table['CTCFlipQC Orders'] = $this->CTCFlipQC_Orders_Model->CTCFlipQCOrders($post,'',1);
			$table['PrefundAuditCorrection Orders'] = $this->PrefundAuditCorrection_Orders_Model->PrefundAuditCorrectionOrders($post,'',1);
			$table['AdhocTasks Orders'] = $this->AdhocTasks_Orders_Model->AdhocTasksOrders($post,'',1);
			$table['UWClear Orders'] = $this->UWClear_Orders_Model->UWClearOrders($post,'',1);
			$table['TitleReview Orders'] = $this->TitleReview_Orders_Model->TitleReviewOrders($post,'',1);
			$table['BorrowerDocs Orders'] = $this->BorrowerDocs_Orders_Model->BorrowerDocsOrders($post,'',1);
			$table['GateKeeping Orders'] = $this->GateKeeping_Orders_Model->GateKeepingOrders($post,'',1);
			$table['Submissions Orders'] = $this->Submissions_Orders_Model->SubmissionsOrders($post,'',1);
			$data['AllOrders'] = $table;
		}
		$response = $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data, TRUE);
		echo $response;
	}
}?>
