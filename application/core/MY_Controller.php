<?php defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Controller extends MX_Controller {
	public $parameters = [];

	function __construct()
	{
		parent::__construct();
		$thisUrl = base_url().$this->uri->uri_string();
		$logged_in = $this->session->userdata('UserUID');
		$this->output->enable_profiler($this->config->item('profiler'));
		if ($logged_in != TRUE || empty($logged_in)){

			$this->load->model('Common_Model');

			$allowed = array('Login');
			if ( ! in_array($this->router->fetch_class(), $allowed))
			{
				if ($this->input->is_ajax_request()) {
					?>
					<script>
						window.location.href='<?php echo base_url("Login?url=$thisUrl"); ?>';
					</script>
					<?php
					exit;
				}
				else{
					redirect(base_url("Login?url=$thisUrl"));
				}
			}
		} else{

			$this->loggedid = $this->session->userdata('UserUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->RoleType = $this->session->userdata('RoleType');
			$this->parameters['DefaultClientUID'] = $this->session->userdata('DefaultClientUID');
			$this->UserPermissions = get_user_permissions();
			$this->UserName = ($this->UserPermissions != '') ? $this->UserPermissions->UserName : $this->UserName;
			$this->RoleUID = ($this->UserPermissions != '') ? $this->UserPermissions->RoleUID : $this->RoleUID;
			$this->RoleType = ($this->UserPermissions != '') ? $this->UserPermissions->RoleTypeUID : $this->RoleType;

			//load models
			$this->load->model('MY_Model');
			
			//init user params
			$this->MY_Model->init_userparams($this->UserPermissions);

			$this->load->model('Common_Model');

			if(empty($this->parameters['DefaultClientUID']) &&  ( ! in_array($this->router->fetch_class(), array('Profile')))) {
				$Activecustomer = $this->Common_Model->getActivecustomer();
				if(!empty($Activecustomer)) {
					$this->session->set_userdata(array('DefaultClientUID'=>$Activecustomer->CustomerUID));
					$this->parameters['DefaultClientUID'] = $Activecustomer->CustomerUID;
				} else {
					redirect(base_url('Profile'),'refresh');
				}
			}

			//load remaining models to fetch counts
			$this->load->model(array(
				'MyOrders/MyOrders_Model',
				'Completed/Completedordersmodel',
				'Cancelled/Cancelledordersmodel',
				'PreScreen_Orders/PreScreen_Orders_Model',
				'WelcomeCall_Orders/WelcomeCall_Orders_Model',
				'TitleTeam_Orders/TitleTeam_Orders_Model',
				'FHAVACaseTeam_Orders/FHAVACaseTeam_Orders_Model',
				'ThirdParty_Orders/ThirdParty_Orders_Model',
				'WorkUp_Orders/WorkUp_Orders_Model',
				'UnderWriter_Orders/UnderWriter_Orders_Model',
				'Scheduling_Orders/Scheduling_Orders_Model',
				'Closing_Orders/Closing_Orders_Model',
				'DocChase_Orders/DocChase_Orders_Model',
				'DocChaseReport/DocChaseReportmodel',
				'DocWaiting/DocWaitingmodel',
				'Withdrawal_Orders/Withdrawal_Orders_model',
				'HOI_Orders/HOI_Orders_model',
				'BorrowerDoc_Orders/BorrowerDoc_Orders_model',
				'BorrowerDoc_Order/BorrowerDoc_Order_Model',
				'PayOff_Orders/PayOff_Orders_model',
				'CD_Orders/CD_Orders_Model',
				'PE_Orders/PE_Orders_Model',
				'FinalApproval_Orders/FinalApproval_Orders_Model',
				'ExceptionQueue_Orders/ExceptionQueue_Orders_model',
				'ICD_Orders/ICD_Orders_Model',
				'Disclosures_Orders/Disclosures_Orders_Model',
				'NTB_Orders/NTB_Orders_Model',
				'FloodCert_Orders/FloodCert_Orders_Model',
				'Appraisal_Orders/Appraisal_Orders_Model',
				'Escrows_Orders/Escrows_Orders_Model',
				'TwelveDayLetter_Orders/TwelveDayLetter_Orders_Model',
				'MaxLoan_Orders/MaxLoan_Orders_Model',
				'POO_Orders/POO_Orders_Model',
				'CondoQR_Orders/CondoQR_Orders_Model',
				'FHACaseAssignment_Orders/FHACaseAssignment_Orders_Model',
				'VACaseAssignment_Orders/VACaseAssignment_Orders_Model',
				'VVOE_Orders/VVOE_Orders_Model',
				'CEMA_Orders/CEMA_Orders_Model',
				'SCAP_Orders/SCAP_Orders_Model',
				'NLR_Orders/NLR_Orders_Model',
				'CTCFlipQC_Orders/CTCFlipQC_Orders_Model',
				'PrefundAuditCorrection_Orders/PrefundAuditCorrection_Orders_Model',
				'AdhocTasks_Orders/AdhocTasks_Orders_Model',
				'UWClear_Orders/UWClear_Orders_Model',
				'TitleReview_Orders/TitleReview_Orders_Model',
				'BorrowerDocs_Orders/BorrowerDocs_Orders_Model',
				'GateKeeping_Orders/GateKeeping_Orders_Model',
				'Submissions_Orders/Submissions_Orders_Model',
				'DocsOut_Orders/DocsOut_Orders_Model',
				'SignedDocs_Orders/SignedDocs_Orders_Model',
				'FundingConditions_Orders/FundingConditions_Orders_Model',
				'InitialUnderWriting_Orders/InitialUnderWriting_Orders_Model',
				'ConditionwithApproval_Orders/ConditionwithApproval_Orders_Model',
				'Underwriting_Orders/Underwriting_Orders_Model'

			));
		}


	}



}?>

