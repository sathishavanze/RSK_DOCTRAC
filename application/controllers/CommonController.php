<?php defined('BASEPATH') or exit('No direct script access allowed');
class CommonController extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Escalation_Orders/Escalation_Orders_model');
        $this->load->library('form_validation');
        $this->load->model('User/User_Model');
        $this->load->model('Documenttype/Documenttype_model');
    }

    function Filter_from_to_date()
    {
        $filter = $this->input->post('Filter');
        $data = array();
        switch ($filter) 
        {
          case 'CYear':
          $data['From'] = date("Y-01-01");
          $data['To'] = date("Y-m-t",strtotime(date('Y-12-31')));
          break;

          case 'CMonth':
          $data['From'] = date("Y-m-01");
          $data['To'] = date("Y-m-t",strtotime(date('Y-m-d')));
          break;

          case 'LYear':
          $data['From'] = date("Y-m-d",strtotime("-1 year"));
          $data['To'] = date("Y-m-d");
          break;

          case 'LMonth': 
          $data['From'] = date("Y-m-d",strtotime("-29 days"));
          $data['To'] = date("Y-m-d");
          break;
          
          case '3Month':
          $data['From'] = date("Y-m-d",strtotime("-3 months"));
          $data['To'] = date("Y-m-d");
          break;

          case '6Month':
          $data['From'] = date("Y-m-d",strtotime("-6 months"));
          $data['To'] = date("Y-m-d");
          break;

          case 'Today':
          $data['From'] = date("Y-m-d");
          $data['To'] = date("Y-m-d");
          break;

          case 'All':
          $data['From'] = '';
          $data['To'] = '';  

      } 
      echo json_encode($data);
  }

  function SaveSpecificationInstru()
  { 
    $SpecifyInstru = $this->input->post('SpecifyInstru');
    $OrderUID = $this->input->post('OrderUID');
    $InsertFields = array(
        'Description' => $SpecifyInstru, 
        'OrderUID' => $OrderUID, 
        'Status' => 'Open', 
        'CreatedDateTime' => date('Y-m-d H:i:s'),
        'CreateByUserUID' => $this->loggedid    
    );
    $this->load->library('form_validation');


    $this->form_validation->set_error_delimiters('', '');


    $this->form_validation->set_rules('OrderUID', '', 'required');
    $this->form_validation->set_rules('SpecifyInstru', '', 'required');

    $this->form_validation->set_message('required', 'This Field is required');
    if ($this->form_validation->run() == true) {
        $this->db->insert('tOrderInstruction',$InsertFields);

        if($this->db->affected_rows() > 0)
        {
            $result = array("validation_error" => 0, 'message' => 'Specification Instruction Saved Successfully', 'type' => 'success');
            echo json_encode($result);
        }else{
         $result = array("validation_error" => 1, 'message' => 'Can not be Save', 'type' => 'warning');
         echo json_encode($result);
     }
 }else{
    $Msg = $this->lang->line('Empty_Validation');

    $formvalid = [];

    $validation_data = array(
        'validation_error' => 1,
        'message' => $Msg,
        'OrderUID' => form_error('OrderUID'),
        'SpecifyInstru' => form_error('SpecifyInstru'),
    );
    foreach ($validation_data as $key => $value) {
        if (is_null($value) || $value == '')
            unset($validation_data[$key]);
    }
    $this->output->set_content_type('application/json')
    ->set_output(json_encode($validation_data))->_display(); exit;

}

}

function DisableSpecificationInstru()
{         
    $OrderUID = $this->input->post('OrderUID');
    $InstructionUID = $this->input->post('InstructionUID');
    $UpdateFields = array(             
        'Status' => 'Closed', 
        'ClosedDateTime' => date('Y-m-d H:i:s'),
        'ClosedByUserUID' => $this->loggedid    
    );
    $UpdateInstru = $this->Common_Model->UpdateInstruction($OrderUID,$InstructionUID,$UpdateFields);

    if($UpdateInstru != 0)
    {
        $result = array("validation_error" => 0, 'message' => 'Specification Instruction Disabled Successfully', 'type' => 'success');
        echo json_encode($result);
    }else{
     $result = array("validation_error" => 1, 'message' => 'Can not be Updated', 'type' => 'warning');
     echo json_encode($result);
 }    
}

function CountInstructionSpecify()
{
    $OrderUID = $this->input->post('OrderUID');
    $status = $this->config->item('keywords')['Cancelled']; 
    $this->db->select('StatusUID')->from('tOrders');
    $this->db->where('OrderUID', $OrderUID);
    $this->db->where('StatusUID != ', $status);
    $GetStatus =  $this->db->get()->row();
    if(!empty($GetStatus))
    {
        $Query = $this->db->query("SELECT * FROM tOrderInstruction WHERE OrderUID = $OrderUID AND Status = 'Open'");
        $SQL = $Query->result(); 
        $result = array("validation_error" => 0, 'message' => 'Count', 'Count' => count($SQL), 'Data' => $SQL);
    }
    else
    {
        $result = array("validation_error" => 0, 'message' => 'Count', 'Count' => 0, 'Data' => '');
    }

    echo json_encode($result);
}

public function GetCustomerDetails()
{
    $CustomerUID = $this->input->post('CustomerUID');

    $mCustomer = $this->Common_Model->get_row('mCustomer', ['CustomerUID'=>$CustomerUID], ['CustomerUID'=>'ASC', 'Active' => 1], []);
        // $mProjectCustomer = $this->Common_Model->get('mProjectCustomer', ['CustomerUID'=>$CustomerUID, 'Active' => 1], ['ProjectUID'=>'ASC'], []);
    $mProjectCustomer = [];

    $CustomerProducts = $this->Common_Model->GetCustomerProducts($CustomerUID);   
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(['Customer'=>$mCustomer, 'ProjectCustomer'=>$mProjectCustomer, 'Products'=>$CustomerProducts]));


}

public function GetCustomerSubProducts()
{
    $CustomerUID = $this->input->post('CustomerUID');
    $ProductUID = $this->input->post('ProductUID');

    $AdditionalDocTypes = []; 
    $RULES = []; 

    $Purposes = ['sales', 'sale', 'sal', 'purchase', 'purchases', 'pur'];

    $LoanPurpose = $this->input->post('LoanPurpose');
    $MOM = $this->input->post('MOM');
    $SecondLienFlag = $this->input->post('SecondLienFlag');
    $GAPMortgageAmount = preg_replace('/[^0-9.]/', '', $this->input->post('GAPMortgageAmount'));

    $mProjectCustomer = $this->Common_Model->get('mProjectCustomer', ['CustomerUID'=>$CustomerUID,'ProductUID'=>$ProductUID,'Active'=>1]);

    $mProductTypeDoc = $this->Common_Model->getProductDocType($ProductUID);

    $mProductRules = $this->Common_Model->get('mProductRules', ['ProductUID'=>$ProductUID]);


    foreach ($mProductRules as $key => $value) {

        $RULES[] = $value->RuleUID;
    }

    /*^^^^^^^^^ Document Rules Starts^^^^^^^^^^^^^^*/

    if (in_array(STATUS_ONE, $RULES)) {

        if (in_array(strtolower(trim($LoanPurpose)), $Purposes)) {

            // Get Deed Doc Type
            $mProductTypeDoc[] = $this->Common_Model->getProductDocTypeByName($this->config->item('DocTypes')[4], $ProductUID);

        }

    }

    if (in_array(STATUS_TWO, $RULES)) {

        if (strtolower($MOM) == 'false') {
            // Get Assignment Doc Type
            $mProductTypeDoc[] = $this->Common_Model->getProductDocTypeByName($this->config->item('DocTypes')[3], $ProductUID);

        }
    }

    if (in_array(STATUS_THREE, $RULES)) {

        if (is_numeric($GAPMortgageAmount)) {
            // Check Security Title exist

            if (!$this->Common_Model->is_arrayobject_value_exist($this->config->item('DocTypes')[1], 'DocTypeName', $mProductTypeDoc)) {

                $mProductTypeDoc[] = $this->Common_Model->getProductDocTypeByName($this->config->item('DocTypes')[1], $ProductUID);
            }

        }
    }

    if (in_array(STATUS_FOUR, $RULES)) {

        if (strtolower($SecondLienFlag) == 'true') {
            // Check Security Title exist

            if (!$this->Common_Model->is_arrayobject_value_exist($this->config->item('DocTypes')[1], 'DocTypeName', $mProductTypeDoc)) {

                $mProductTypeDoc[] = $this->Common_Model->getProductDocTypeByName($this->config->item('DocTypes')[1], $ProductUID);
            }


            // Check Title Policy Exist
            $availablekey = $this->Common_Model->return_arrayobject_key($this->config->item('DocTypes')[2], 'DocTypeName', $mProductTypeDoc); 


            if ($availablekey>=0) {




                unset($mProductTypeDoc[$availablekey]);
                $mProductTypeDoc=array_values($mProductTypeDoc);

            }



        }
    }

    /*^^^^^^^^^ Document Rules Ends^^^^^^^^^^^^^^*/


    $CustomerSubProducts = [];
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(['ProjectCustomer'=>$mProjectCustomer, 'SubProducts'=>$CustomerSubProducts, 'ProductTypeDoc'=>$mProductTypeDoc]));


}

public function GetPriorityAndLender()
{
    $ProjectUID = $this->input->post('ProjectUID');

    $GetProjectLender = $this->Common_Model->GetProjectLender($ProjectUID);
    $GetProjectInvestor = $this->Common_Model->GetProjectInvestor($ProjectUID);

    $this->output->set_content_type('application/json')
    ->set_output(json_encode(['ProjectLender'=>$GetProjectLender,'ProjectInvestor'=> $GetProjectInvestor]));


}


function GetZipCodeDetails()
{
    $Zipcode = $this->input->post('Zipcode');

    if ($Zipcode) {

        $City = $this->Common_Model->getCityDetail($Zipcode);
        $State = $this->Common_Model->getStateDetail($Zipcode);
        $County = $this->Common_Model->getCountyDetail($Zipcode);
        if (!empty($State) && !empty($County) && !empty($City)) {

            echo json_encode(array('City' => $City, 'success' => 1, 'State' => $State, 'County' => $County));
        } else {
            echo json_encode(array('details' => '', 'success' => 0));

        }

    } else {
        echo json_encode(array('details' => '', 'success' => 0));
    }
}

function UpdateNullStatus()
{
    $OrderUID = $this->input->post('OrderUID');
    $result = $this->Common_Model->UpdateNullStatus($OrderUID);
    if($result && $result['status'] == 1)
    {
       $res = array('validation_error' => 0,'message'=>'Cancelled', 'URL'=>$result['URL']);
       echo json_encode($res);
       exit();
   }else{
       $res = array('validation_error' => 1,'message'=>'Unable to Cancel !!!');
       echo json_encode($res);
       exit();
   }
}

function GetCounts()
{
    $Counts = [];

    $MenuLinks = $this->Common_Model->get_definedleftDynamicMenu_options(['common','sidebar']); 
    $Total_Counts = $this->config->item('Total_Counts');

    foreach ($MenuLinks as $key => $value) {
        if (isset($Total_Counts[$value->controller])) {
            $Counts[$value->controller] = $this->{$Total_Counts[$value->controller]['model']}->{$Total_Counts[$value->controller]['function_name']}();
        }
    }

        /* $Counts['PreScreen_Orders'] = $this->PreScreen_Orders_Model->total_count();
        $Counts['WelcomeCall_Orders'] = $this->WelcomeCall_Orders_Model->total_count();
        $Counts['FHAVACaseTeam_Orders'] = $this->FHAVACaseTeam_Orders_Model->total_count();
        $Counts['TitleTeam_Orders'] = $this->TitleTeam_Orders_Model->total_count();
        $Counts['ThirdParty_Orders'] = $this->ThirdParty_Orders_Model->total_count();
        $Counts['WorkUp_Orders'] = $this->WorkUp_Orders_Model->total_count();
        $Counts['UnderWriter_Orders'] = $this->UnderWriter_Orders_Model->total_count();
        $Counts['Scheduling_Orders'] = $this->Scheduling_Orders_Model->total_count();
        $Counts['Closing_Orders'] = $this->Closing_Orders_Model->total_count();
        //$Counts['Completed'] = $this->Completedordersmodel->count_all();
        $Counts['Cancelled'] = $this->Cancelledordersmodel->count_all();
        $Counts['DocChase_Orders'] = $this->DocChase_Orders_Model->total_count();
        $Counts['DocChaseReport'] = $this->DocChaseReportmodel->count_all();
        $Counts['Escalation_Orders'] = $this->Escalation_Orders_model->count_all();
        $Counts['DocWaiting'] = $this->DocWaitingmodel->count_all();
        $Counts['Withdrawal_Orders'] = $this->Withdrawal_Orders_model->count_all();
        $Counts['HOI_Orders'] = $this->HOI_Orders_model->total_count();
        //$Counts['BorrowerDoc_Orders'] = $this->BorrowerDoc_Orders_model->total_count();
        $Counts['BorrowerDoc_Order'] = $this->BorrowerDoc_Order_Model->total_count();
        $Counts['PayOff_Orders'] = $this->PayOff_Orders_model->total_count();
        $Counts['PE_Orders'] = $this->PE_Orders_Model->total_count();
        $Counts['CD_Orders'] = $this->CD_Orders_Model->total_count();
        $Counts['FinalApproval_Orders'] = $this->FinalApproval_Orders_Model->total_count();
        $Counts['ICD_Orders'] = $this->ICD_Orders_Model->total_count();
        $Counts['Disclosures_Orders'] = $this->Disclosures_Orders_Model->total_count();
        $Counts['NTB_Orders'] = $this->NTB_Orders_Model->total_count();
        $Counts['FloodCert_Orders'] = $this->FloodCert_Orders_Model->total_count();
        $Counts['Appraisal_Orders'] = $this->Appraisal_Orders_Model->total_count();
        $Counts['Escrows_Orders'] = $this->Escrows_Orders_Model->total_count();
        $Counts['TwelveDayLetter_Orders'] = $this->TwelveDayLetter_Orders_Model->total_count();
        $Counts['MaxLoan_Orders'] = $this->MaxLoan_Orders_Model->total_count();
        $Counts['POO_Orders'] = $this->POO_Orders_Model->total_count();
        $Counts['CondoQR_Orders'] = $this->CondoQR_Orders_Model->total_count();
        $Counts['FHACaseAssignment_Orders'] = $this->FHACaseAssignment_Orders_Model->total_count();
        $Counts['VACaseAssignment_Orders'] = $this->VACaseAssignment_Orders_Model->total_count();
        $Counts['VVOE_Orders'] = $this->VVOE_Orders_Model->total_count();
        $Counts['CEMA_Orders'] = $this->CEMA_Orders_Model->total_count();
        $Counts['SCAP_Orders'] = $this->SCAP_Orders_Model->total_count();
        $Counts['NLR_Orders'] = $this->NLR_Orders_Model->total_count();
        $Counts['CTCFlipQC_Orders'] = $this->CTCFlipQC_Orders_Model->total_count();
        $Counts['PrefundAuditCorrection_Orders'] = $this->PrefundAuditCorrection_Orders_Model->total_count();
        $Counts['UWClear_Orders'] = $this->UWClear_Orders_Model->total_count();
        $Counts['AdhocTasks_Orders'] = $this->AdhocTasks_Orders_Model->total_count();
        $Counts['TitleReview_Orders'] = $this->TitleReview_Orders_Model->total_count();
        $Counts['BorrowerDocs_Orders'] = $this->BorrowerDocs_Orders_Model->total_count();
        $Counts['GateKeeping_Orders'] = $this->GateKeeping_Orders_Model->total_count();
        $Counts['Submissions_Orders'] = $this->Submissions_Orders_Model->total_count(); */

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($Counts))->_display();exit;
    }


/**
* @author Sathis Kannan.P
* @purpose Common Search Count Function
* @param return json
* @date 09-07-2020
*/
function GetCommonSearchCount(){

        // ini_set('display_errors', 1);error_reporting(E_ALL);
    $common = [];
    $test=[];
    $post = $this->input->post();

    $post['advancedsearch'] = $this->input->post('formData');

    $commoncount = $this->config->item('commoncount'); 
    $ModuleController = $this->input->post('ModuleController');

    foreach ($commoncount as $key => $value) {

    	if($key == $post['advancedsearch']['ModuleController']) {

    		$WorkflowModuleUID = $value['WorkflowUID'];
    		$post['advancedsearch']['WorkflowModuleUID'] = $WorkflowModuleUID;
    		$post['WorkflowModuleUID'] = $WorkflowModuleUID;

    		$Queuesworkflow = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID);

    		foreach ($value as $key => $val) {

    			if ($key=='NewOrders') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['NewOrders']}($post);
    			}
    			if ($key=='AssignedOrders') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['AssignedOrders']}($post);
    			}
    			if ($key=='MyOrders') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['MyOrders']}($post);
    			}
    			if ($key=='KickBackOrders') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['KickBackOrders']}($post);
    			}
    			if ($key=='parkingorders') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['parkingorders']}($post);
    			}
    			if ($key=='HOIWaiting') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['HOIWaiting']}($post);
    			}
    			if ($key=='HOIResponseReceived') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['HOIResponseReceived']}($post);
    			}
    			if ($key=='HOIDocReceived') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['HOIDocReceived']}($post);
    			}
    			if ($key=='HOIException') {
    				$common['counts'][$key] = $this->{$value['Model']}->{$value['HOIException']}($post);
    			}
                if ($key=='ExpiredOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['ExpiredOrders']}($post);
                }
                if ($key=='DocsCheckOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['DocsCheckOrders']}($post);
                }
                if ($key=='PendinguwOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['PendinguwOrders']}($post);
                }
                if ($key=='SubmittedforDocCheckOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['SubmittedforDocCheckOrders']}($post);
                }
                if ($key=='NonWorkableOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['NonWorkableOrders']}($post);
                }
                if ($key=='WorkupReworkOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['WorkupReworkOrders']}($post);
                }
                if ($key=='CDInflowOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['CDInflowOrders']}($post);
                }
                if ($key=='CDPendingOrders') {
                    $common['counts'][$key] = $this->{$value['Model']}->{$value['CDPendingOrders']}($post);
                }
                if ($key=='CDCompletedOrders') {
                    // $common['counts'][$key] = $this->{$value['Model']}->{$value['CDCompletedOrders']}($post);
                    $cd_post['advancedsearch'] = $this->input->post('formData');
                    $cd_post['advancedsearch']['WorkflowModuleUID'] = $this->config->item('Workflows')['CD'];
                    $cd_post['WorkflowModuleUID'] = $this->config->item('Workflows')['CD'];
                    $common['counts'][$key] = $this->Common_Model->completedordersBasedOnWorkflow_count_all($cd_post);
                }
                if ($key=='ExpiredCompleteOrders') {

                    $common['counts'][$key] = $this->Common_Model->ExpiredCompleteOrders($post);
                }
                $common['counts']['CompletedOrders'] = $this->Common_Model->completedordersBasedOnWorkflow_count_all($post); 

            }


            foreach ($Queuesworkflow as $key => $Queue) {
             $post['QueueUID'] = $Queue->QueueUID;
             $common['subqueues_counts'][$Queue->QueueUID]  = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post,'count_all');
         }

     }

 }



 $this->output->set_content_type('application/json');
 $this->output->set_output(json_encode($common))->_display();exit;


}

public function IsStackingDocumentAvailable()
{
    $OrderUID = $this->input->post('OrderUID');

    if (!is_numeric($OrderUID)) {
        $this->output->set_content_type('application/json')
        ->set_output(json_encode(array('validation_error'=>'1', 'message'=>'Invalid Order')))
        ->_display(); exit;
    }

    $tDocuments_available = $this->Common_Model->get('tDocuments', ['OrderUID'=>$OrderUID]);
    if (empty($tDocuments_available)) {
        $this->output->set_content_type('application/json')
        ->set_output(json_encode(array('validation_error' => '2', 'message' => 'No Document Available For Stacking !!!')))
        ->_display();
        exit;            
    }
    else{
        $stackingdocument_available = $this->Common_Model->get('tDocuments', ['OrderUID'=>$OrderUID, 'IsStacking'=>1]);
        if (empty($stackingdocument_available)) {
            $this->output->set_content_type('application/json')
            ->set_output(json_encode(array('validation_error' => '3', 'message' => 'No Stacking Document Available. Do you want to change one as Stacking.')))
            ->_display();
            exit;            

        }
        else{
            $this->output->set_content_type('application/json')
            ->set_output(json_encode(array('validation_error' => '0', 'message' => 'Document Available')))
            ->_display();
            exit;            
        }

    }
}

public function ChangeToStacking()
{
    $OrderUID = $this->input->post('OrderUID');

    if (!is_numeric($OrderUID)) {
        $this->output->set_content_type('application/json')
        ->set_output(json_encode(array('validation_error' => '1', 'message' => 'Invalid Order')))
        ->_display();
        exit;
    }

    $result = $this->Common_Model->ChangeToStacking($OrderUID);

    if (empty($result)) {
        $this->output->set_content_type('application/json')
        ->set_output(json_encode(array('validation_error' => '1', 'message' => 'Unable to Change !!!', 'title'=>'Failed', 'color'=>'danger')))
        ->_display();
        exit;

    }
    else{

        $this->output->set_content_type('application/json')
        ->set_output(json_encode(array('validation_error' => '0', 'message' => 'One Document '.$result['DocumentName'].' Changed as Stacking.', 'title'=>'Success', 'color' => 'success')))
        ->_display();
        exit;
    }
}

function GetAdvancedSearchProjects()
{
    $ProductUID = $this->input->post('ProductUID');

    $Projects = [];

    $this->db->select('*');
    $this->db->from('mProjectCustomer');

    if (in_array($this->RoleType, $this->config->item('AgentAccess'))) {
        $this->db->join('mProjectUser', 'mProjectUser.ProjectUID=mProjectCustomer.ProjectUID');
        $this->db->where('mProjectUser.UserUID', $this->loggedid);
    }

    if ($ProductUID != 'All' && $ProductUID != '') {
        $this->db->where('mProjectCustomer.ProductUID', $ProductUID);
        $this->db->where('mProjectCustomer.Active', STATUS_ONE);
    }
    $mProjectCustomers = $this->db->get()->result();




    $this->db->select('*,mInputDocType.DocTypeName');
    $this->db->from('mProductDocType');

    if (in_array($this->RoleType, $this->config->item('AgentAccess'))) {
        $this->db->join('mProductDocType', 'mProductDocType.ProductUID = tOrders.ProductUID');
        $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = mProductDocType.InputDocTypeUID');
        $this->db->where('mInputDocType.UserUID', $this->loggedid);
    }

    if ($ProductUID != 'All' && $ProductUID != '') {
        $this->db->join('mInputDocType','mInputDocType.InputDocTypeUID = mProductDocType.InputDocTypeUID');
        $this->db->where('mProductDocType.ProductUID', $ProductUID);
    }
    $mInputDocType = $this->db->get()->result(); 
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(array('validation_error'=>0, 'Projects'=>$mProjectCustomers)))
    ->_display(); exit;


}

function GetAdvancedSearchLenders()
{
    $ProjectUID = rtrim($this->input->post('ProjectUID'), ',');
    $Projects = [];


    $this->db->select('*');
    $this->db->from('mLender');

    if ($ProjectUID != 'All' && $ProjectUID != '') {
        $this->db->join('mProjectLender', 'mProjectLender.LenderUID=mLender.LenderUID');
        $this->db->where_in('mProjectLender.ProjectUID', $ProjectUID);
        $this->db->where('mLender.Active',1);
        $this->db->group_by('mLender.LenderUID');
    }
    else
    {
        $this->db->join('mProjectLender', 'mProjectLender.LenderUID=mLender.LenderUID');

        $this->db->where('mLender.Active',1);
        $this->db->group_by('mLender.LenderUID');
    }
    $mProjectLender = $this->db->get()->result();
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(array('validation_error'=>0, 'Lenders'=>$mProjectLender)))
    ->_display(); exit;


}



function GetAdvancedSearchPackNo()
{
    $data['ProjectUID'] = rtrim($this->input->post('ProjectUID'), ',');
    $data['CustomerUID'] = rtrim($this->input->post('CustomerUID'), ',');
    $data['ProductUID'] = rtrim($this->input->post('ProductUID'), ',');
    $this->db->select('*');
    $this->db->from('tOrderPackage');
    $this->db->join('tOrders', 'tOrders.PackageUID=tOrderPackage.PackageUID');

    foreach ($data as $key => $value) 
    {
        if($value != 'All' && $value !='')
        {
            $where.= 'tOrders.'.$key.' ='.$value.' and ';
        }
    }

    if($where != '')
    {
        $where_string  = substr($where, 0, strlen($where) -4);
        $this->db->where($where_string);
    }


    $this->db->group_by('tOrderPackage.PackageUID');
    $tOrders = $this->db->get()->result();
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(array('validation_error'=>0, 'Package'=>$tOrders)))
    ->_display(); exit;
}


function Get_CustomerProducts()
{   
    $CustomerUID = $this->input->post('CustomerUID');
    $CustomerProducts = [];
    if(!empty($CustomerUID)){    
        $CustomerProducts =  $this->Common_Model->get_customerproducts($CustomerUID);
    }
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(['CustomerProducts'=>$CustomerProducts]));
}

function GetAdvancedSearchProducts()
{
    $CustomerUID = $this->input->post('CustomerUID');

    $Products = [];

    $this->db->select('*,mProducts.ProductName');
    $this->db->from('mCustomerProducts');
    $this->db->join('mProducts','mProducts.ProductUID=mCustomerProducts.ProductUID');

    if (in_array($this->RoleType, $this->config->item('AgentAccess'))) {
        $this->db->join('mCustomerProductUsers', 'mCustomerProductUsers.ProductUID=mCustomerProducts.ProductUID');
        $this->db->where('mCustomerProductUsers.UserUID', $this->loggedid);
    }

    $this->db->where('mCustomerProducts.CustomerUID', $this->parameters['DefaultClientUID']);
    $this->db->where('mProducts.Active', STATUS_ONE);

    $mProductCustomers = $this->db->get()->result();
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(array('validation_error'=>0, 'Products'=>$mProductCustomers)))
    ->_display(); exit;

}

    //for milestone
function GetAdvancedSearchMilestone(){      
    $this->db->select('mMilestone.MilestoneUID,mMilestone.MilestoneName');
    $this->db->from('mMilestone');
    $this->db->join('tOrders','mMilestone.MilestoneUID = tOrders.MilestoneUID','inner');
    $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));
    $this->db->group_by('mMilestone.MilestoneUID');
    $mMilestone = $this->db->get()->result();
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(array('validation_error'=>0, 'Milestone'=>$mMilestone)))
    ->_display(); exit;
}

    //for State
function GetAdvancedSearchState(){
    $this->db->select('PropertyStateCode as StateCode');
    $this->db->from('tOrders');
    $this->db->where('CustomerUID',$this->session->userdata('DefaultClientUID'));
    $this->db->where('PropertyStateCode is NOT NULL AND PropertyStateCode <> ""', NULL, FALSE);
    $this->db->group_by('PropertyStateCode');
    $mState = $this->db->get()->result();
    $this->output->set_content_type('application/json')
    ->set_output(json_encode(array('validation_error'=>0, 'States'=>$mState)))
    ->_display(); exit;
        /*$this->db->select('mStates.StateCode');
        $this->db->from('mStates');
        $this->db->join('tOrders','tOrders.PropertyStateCode = mStates.StateCode','inner');
        $this->db->where('tOrders.CustomerUID',$this->session->userdata('DefaultClientUID'));
        $this->db->group_by('mStates.StateUID');
        $mState = $this->db->get()->result();
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(array('validation_error'=>0, 'States'=>$mState)))
            ->_display(); exit;*/

        }

        function CancelOrderRevoke(){
            $OrderUID = $this->input->post('OrderUID');
            $OrderRevokeResult=$this->Common_Model->CancelOrderRevoke($OrderUID);

            if ($OrderRevokeResult == 1) {
                /*INSERT ORDER LOGS BEGIN*/
                $this->Common_Model->OrderLogsHistory($OrderUID,'Order Revoked',Date('Y-m-d H:i:s'));
                /*INSERT ORDER LOGS END*/
                $res = array('validation_error' => 1,'message'=>'Order Revoked !!!');
                echo json_encode($res);exit;
            }
            else{
                $res = array('validation_error' => 0,'message'=>'Order Failed !!!');
                echo json_encode($res);exit;
            }

        }
        function CheckOrderStatusandWorkflowShipping(){
            $OrderUID =$this->input->post('OrderUID');
            $ReturnValue =$this->Common_Model->CheckOrderStatusandWorkflowShipping($OrderUID);
            if ($ReturnValue == 1) {
                $ShippingWorkflow = array('status'=> 1);
                echo json_encode($ShippingWorkflow);exit();
            }
            else{
                $ShippingWorkflow = array('status'=> 0);
                echo json_encode($ShippingWorkflow);exit();
            }

        }


        function PickExistingOrderCheck()
        {
            $OrderUID = $this->input->post('OrderUID');
            $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
            $result = $this->Common_Model->PickExistingOrderCheck($OrderUID, $WorkflowModuleUID);
            if($result['Status'] == 1)
            {
                $res = array('validation_error' => 1,'message'=>$result['result']);
                echo json_encode($res);exit();
            }
            else
            {
                $value = $this->Common_Model->OrderAssign($OrderUID, $WorkflowModuleUID);
                if($value)
                {
                    $val = array('validation_error' => 2,'message'=>'Order Assigned','color'=>'success');
                    echo json_encode($val);exit();
                }
            }
        }

        public function ChangeOrderAssignment()
        {
            $OrderAssignmentUID = $this->input->post('OrderAssignmentUID');

            $Msg = '';
            $update = '';

            $this->load->library('form_validation');

            $this->form_validation->set_error_delimiters('', '');

            $this->form_validation->set_rules('OrderAssignmentUID', '', 'required');

            $this->form_validation->set_message('required', 'This Field is required');

            if ($this->form_validation->run() == true) {




                $this->db->select('*');
                $this->db->from('tOrderAssignments');
                $this->db->where('OrderAssignmentUID',$OrderAssignmentUID);
                $tOrderAssignmentsrow =  $this->db->get()->row();

                $workflow_names = $this->db->select('WorkflowModuleName')->from('mWorkFlowModules')->where('WorkflowModuleUID',$tOrderAssignmentsrow->WorkflowModuleUID)->get()->row();

                $user_names = $this->db->select('UserName')->from('mUsers')->where('UserUID',$this->loggedid)->get()->row();

                $Description = sprintf($this->lang->line('Log_Reassigned'),(isset($workflow_names) && !empty($workflow_names->WorkflowModuleName) ? $workflow_names->WorkflowModuleName : ''),(isset($user_names) && !empty($user_names->UserName) ? $user_names->UserName : ''));

                if(!empty($tOrderAssignmentsrow))
                {
                    $tOrderAssignmentsArray = array(
                        'AssignedToUserUID' => $this->loggedid,
                        'AssignedDatetime' => Date('Y-m-d H:i:s', strtotime("now")),
                        'AssignedByUserUID' => $this->loggedid,
                        'WorkflowStatus' => $this->config->item('WorkflowStatus')['InProgress'],
                        'OrderFlag' => 0,
                    );
                    $this->db->where('OrderAssignmentUID',$OrderAssignmentUID);
                    $update = $this->db->update('tOrderAssignments', $tOrderAssignmentsArray);


                    $this->Common_Model->OrderLogsHistory($tOrderAssignmentsrow->OrderUID,$Description,Date('Y-m-d H:i:s'));
                }

                if ($update) {
                    $Msg = 'Order Assigned';
                    $this->output->set_content_type('application/json')
                    ->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
                    exit;
                } else {
                    $Msg = $this->lang->line('Order Assigned Faild');
                    $this->output->set_content_type('application/json')
                    ->set_output(json_encode(array('validation_error' => 0, 'message' => $Msg)))->_display();
                    exit;
                }

            } else {

                $Msg = $this->lang->line('Empty_Validation');

                $formvalid = [];

                $validation_data = array(
                    'validation_error' => 1,
                    'message' => $Msg,
                    'OrderAssignmentUID' => form_error('OrderAssignmentUID')
                );
                foreach ($validation_data as $key => $value) {
                    if (is_null($value) || $value == '')
                        unset($validation_data[$key]);
                }
                $this->output->set_content_type('application/json')
                ->set_output(json_encode($validation_data))->_display();
                exit;

            }

        }

    //add all workflow commands
        function AddCommands(){
            $post = $this->input->post();
            if(!empty($post)){
                $WorkflowArrays =$this->config->item('Workflows');
                $Module = array_search($post['WorkflowModuleUID'], $WorkflowArrays);
                $data = array(
                    'OrderUID'=>$post['OrderUID'],
                    'WorkflowUID'=> $post['WorkflowModuleUID'],
                    'Description'=> nl2br($post['Commands']),
                    'Module'=> $Module,
                    'CreatedByUserUID'=> $this->loggedid,
                    'CreateDateTime'=> date('Y-m-d H:i:s'));
                $result = $this->Common_Model->sendCommands($data);
                echo json_encode($result);
            }
        }

        function SaveMeeting()
        {
            $post = $this->input->post();
            $issaved = false;
            if (empty($post['MeetingOrderUID']) || empty($post['WorkflowModuleUID']) || empty($post['PreferedTimeZone']) || !$this->validatetime($post['PreferedTime'])) {
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Invalid Request"]))->_display();exit;
            }

            /*Transaction Begin*/
            $this->db->trans_begin();


            /*Code*/
            $post['PreferedTime'] = date('H:i:s', strtotime($post['PreferedTime']));
            if (!empty($this->Common_Model->get('tOrderMeeting', ['MeetingOrderUID'=>$post['MeetingOrderUID'], 'WorkflowModuleUID'=>$post['WorkflowModuleUID']]))) {
                $issaved = $this->Common_Model->save('tOrderMeeting', $post, ['MeetingOrderUID'=>$post['MeetingOrderUID'], 'WorkflowModuleUID'=>$post['WorkflowModuleUID']]);
            }
            else{

                $issaved = $this->Common_Model->save('tOrderMeeting', $post);
            }        

            /*verify is valid transaction*/
            if ($this->db->trans_status()) {
                /*commit transaction*/
                $this->db->trans_commit();
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode(['validation_error'=>0,'message'=>"Saved Successfully"]))->_display();exit;
            }
            else{
                /*rollback transaction*/
                $this->db->trans_rollback();
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode(['validation_error'=>1,'message'=>"Unable to Save"]))->_display();exit;
            }



        }


        function validatetime($time)
        {
            if(!preg_match("((1[0-2]|0?[1-9]):([0-5][0-9]) ([AaPp][Mm]))", $time)){
                return FALSE;
            }
            else{
                return TRUE;
            }
        }

        function WriteGlobalExcel()
        {
         $controller = $this->input->get('controller');
         $controller_array = array(
            'PreScreen_Orders' => 'GetPreScreenOrders',
            'WelcomeCall_Orders'=> 'GetWelcomeCallQueue',
            'FHAVACaseTeam_Orders'=> 'GetFHAVACaseTeamQueue',
            'TitleTeam_Orders'=> 'GetTitleTeamQueue',
            'ThirdParty_Orders'=> 'GetThirdPartyQueue',
            'WorkUp_Orders'=> 'GetWorkUpQueue',
            'UnderWriter_Orders'=> 'GetUnderWriterQueue',
            'Scheduling_Orders'=> 'GetSchedulingQueue',
            'Closing_Orders' => 'GetClosingQueue',
            'HOI_Orders' => 'GetHOIQueue',
            'BorrowerDoc_Orders' => 'GetBorrowerDocQueue',
            'PayOff_Orders' => 'GetPayOffQueue'
        );
         foreach ($controller_array as $key => $value) 
         {
            if($controller == $key)
            {
               $this->db->select("tOrders.*, mProjectCustomer.ProjectName, tOrders.OrderUID,mMilestone.MilestoneName, mStatus.StatusName, mStatus.StatusColor ,mCustomer.CustomerName, mProjectCustomer.ProjectUID, mProducts.ProductName, tOrderAssignments.AssignedToUserUID, mUsers.UserName AS AssignedUserName");
               $this->db->select('tOrders.LastModifiedDateTime');
               $this->db->select('tOrderWorkflows.EntryDatetime,tOrderWorkflows.DueDateTime');
               $this->Common_Model->$value();
               $list = $this->db->get()->result();
           }
       }
       $data = [];

       $data[] = array('Order No','Loan No','Client','Loan Type','Milestone','Current Status','Aging','OrderEntryDateTime','DueDateTime','Property Address','Property City','Property State','Zip Code');
       for ($i=0; $i < sizeof($list); $i++) { 

        $data[] = array($list[$i]->OrderNumber,$list[$i]->LoanNumber,$list[$i]->CustomerName,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,site_datetimeaging($list[$i]->EntryDatetime),site_datetimeformat($list[$i]->OrderEntryDateTime),site_datetimeformat($list[$i]->DueDateTime),$list[$i]->PropertyAddress1.$list[$i]->PropertyAddress2,$list[$i]->PropertyCityName,$list[$i]->PropertyStateCode,$list[$i]->PropertyZipCode);               
    }
    $filename = $controller.'.csv';
    $this->outputCSV($data, $filename);

} 

  /**
  *Function Description: Global EXCEL SHEET for DOCchase
  *@author Shruti <shruti.vs@avanzegroup.com>
  *@since Date
	*/

  function WriteGlobalDocChaseExcel()
  {
  	set_include_path( get_include_path().PATH_SEPARATOR."..");
  	require_once APPPATH."third_party/xlsxwriter.class.php";
  	$controller = $this->input->get('controller');
  	$writer = new XLSXWriter();

  	$controller_array = array(
  		'DocChase_Orders'=> ['Model'=>'DocChase_Orders_Model',
  		'NewOrders'=>'DocChaseOrders',
  		'AssignedOrders'=>'WorkInProgressOrders',
  		'MyOrders'=>'MyOrders',
  		'parkingorders'=>'parkingorders',
  		'WorkflowUID'=>$this->config->item('Workflows')['DocChase']]
  	);      

  	$NewOrders = [];
  	$AssignedOrders = [];
  	$MyOrders = [];
  	$ParkingOrders = [];

  	$post = ['length'=>'','advancedsearch'=>[]];

  	$ExcelHeader4 = array('Order No'=>'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','Workflows'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string');
  	$ExcelHeader5 = array('Order No'=>'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','Workflows'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string');
  	$ExcelHeader6 = array('Order No'=>'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','Workflows'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string');
  	$ExcelHeader7 = array('Order No'=>'string','Client'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','Remainder On'=>'string','Raised By'=>'string','Remarks'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string');

  	$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');


  	$writer->writeSheetHeader('NewOrders',$ExcelHeader4, $header_style);
  	$writer->writeSheetHeader('AssignedOrders',$ExcelHeader5, $header_style);
  	$writer->writeSheetHeader('MyOrders',$ExcelHeader6, $header_style);
  	$writer->writeSheetHeader('ParkingOrders',$ExcelHeader7, $header_style);

  	foreach ($controller_array as $key => $value) 
  	{

  		if($controller == $key) {

  			$NewOrders = $this->{$value['Model']}->{$value['NewOrders']}($post,'');
  			$AssignedOrders = $this->{$value['Model']}->{$value['AssignedOrders']}($post,'');
  			$MyOrders = $this->{$value['Model']}->{$value['MyOrders']}($post,'');
  			$ParkingOrders = $this->{$value['Model']}->{$value['parkingorders']}($post,'');
  			

  			foreach($NewOrders as $Order){
  				$Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->Workflows,site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
  				$writer->writeSheetRow('NewOrders', array_values($Exceldataset));
  			}

  			foreach($AssignedOrders as $Order){
  				$Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->Workflows,site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
  				$writer->writeSheetRow('AssignedOrders', array_values($Exceldataset));   
  			}

  			foreach($MyOrders as $Order){
  				$Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->Workflows,site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
  				$writer->writeSheetRow('MyOrders', array_values($Exceldataset));   
  			}

  			foreach($ParkingOrders as $Order){
  				$Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,site_datetimeformat($Order->Remainder),$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
  				$writer->writeSheetRow('ParkingOrders', array_values($Exceldataset)); 

  			}

  		}
  	}	

  	
  	$filename = $controller.'.xlsx';


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


  function outputCSV($data,$filename) 
  {
  	ob_clean();
  	header("Content-Type: text/csv");
  	header("Content-Disposition: attachment; filename=".$filename);
  	$output = fopen("php://output", "w");
  	foreach ($data as $row)
  	{
  		fputcsv($output, $row); 
  	}
  	fclose($output);
  	ob_flush();


  }

  /**************************
   * /**
			*Function Description:Golbal EXCEL SHEET
			*@author Shruti <shruti.vs@avanzegroup.com>
			*@since Date
			
   * 
   * PreScreen Orders Global EXCEL SHEET**************************************** */

          function WriteGlobalExcelSheet()
          {
             set_include_path( get_include_path().PATH_SEPARATOR."..");
             require_once APPPATH."third_party/xlsxwriter.class.php";
             $controller = $this->input->get('controller');
             $activesubqueue;
             $QueueUID;
             $activesubqueue = $this->input->get('activesubqueue');
             $QueueUID = $this->input->get('QueueUID');
             $HOILoans = false;


             $writer = new XLSXWriter();

             $controller_array = $this->config->item('controllerarray');


             $NewOrders = [];
             $ExpiredOrders=[];
             $DocsCheckedConditionsPendingOrders=[];
             // $QueueclearedbyFundingOrders=[];
             // $PendingDocsReleaseOrders =[];
             $PendingfromUWOrders =[];
             $SubmittedforDocCheckOrders =[];
             $NonWorkableOrders =[];
             $WorkupReworkOrders =[];
             $AssignedOrders = [];
             $MyOrders = [];
             $ParkingOrders = [];
             $KickBackOrders = [];
             $ReWorkOrders = [];
             $HOIReworkOrders = [];
             $ThreeAConfirmationOrders = [];
             $ReWorkPendingOrders = [];

             $ExcelHeader = array('Order No'=>'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','Aging'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string');
             $ExcelHeader1 = array('Order No' => 'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','AssignedTo'=>'string','AssignedDateTime'=>'string','Aging'=>'string','DueDateTime'=>'string','LastModified Date Time'=>'string');
             $ExcelHeader2 = array('Order No'=>'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','Aging'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string');
             $ExcelHeader3 = array('Order No'=> 'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','Raised By'=>'string','Remarks'=>'string','Remainder On'=>'string','Aging'=>'string','DueDateTime'=>'string','LastModified'=>'string');

             $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

             $post = ['length'=>'','advancedsearch'=>[]];

             if(isset($controller_array[$controller]) && !empty($controller_array[$controller]))
             {   
                $value = $controller_array[$controller];

                if (!empty($activesubqueue) || !empty($QueueUID)) {
                    $selectedsubqueue;
                    switch ($activesubqueue) {
                        case 'orderslist':
                        $selectedsubqueue = 'NewOrders';
                        break;
                        case 'Expiredorderslist':
                        $selectedsubqueue = 'ExpiredOrders';/*Desc: export expired orders @author: Sathis Kannan.P <sathish.kannan@avanzegroup.com> @Since: 23/07/2020 */
                        break;
                        case 'docsorderslist':
                        $selectedsubqueue = 'DocsCheckedConditionsPendingOrders';/*Desc: export docsout docscheckorder @author: Sathis Kannan.P <sathish.kannan@avanzegroup.com> @Since: 21/07/2020 */
                        break;
                        // case 'queueorderslist':
                        // $selectedsubqueue = 'QueueclearedbyFundingOrders';/*Desc: export docsout queuecheckorder @author: Sathis Kannan.P <sathish.kannan@avanzegroup.com> @Since: 21/07/2020 */
                        // break;
                        // case 'pendingdocsoderslist':
                        // $selectedsubqueue = 'PendingDocsReleaseOrders';/*Desc: export docsout pendingdocsorder @author: Sathis Kannan.P <sathish.kannan@avanzegroup.com> @Since: 21/07/2020 */
                        // break;

                        case 'pendinguworderslist':
                        $selectedsubqueue = 'PendingfromUWOrders';/*Desc: export docsout Pendinguw orders @author: Sathis Kannan.P <sathish.kannan@avanzegroup.com> @Since: 21/07/2020 */
                        break;

                        case 'SubmittedforDocCheck_OrdersList':
                        $selectedsubqueue = 'SubmittedforDocCheckOrders';
                        break;

                        case 'NonWorkable_OrdersList':
                        $selectedsubqueue = 'NonWorkableOrders';
                        break;

                        case 'WorkupRework_OrdersList':
                        $selectedsubqueue = 'WorkupReworkOrders';
                        break;

                        case 'KickBacklist':
                        $selectedsubqueue = 'KickBackOrders';
                        break;

                        case 'ReWorkOrdersList':
                        $selectedsubqueue = 'ReWorkOrders';
                        break;

                        case 'HOIReworkOrderList':
                        $selectedsubqueue = 'HOIReworkOrders';
                        break;

                        case 'ThreeAConfirmationOrdersList':
                        $selectedsubqueue = 'ThreeAConfirmationOrders';
                        break;

                        case 'ReWorkPendingOrdersList':
                        $selectedsubqueue = 'ReWorkPendingOrders';
                        break;

                        case 'VAorderslist':
                        $selectedsubqueue = 'VANewOrders';
                        break;

                        case 'workinprogresslist':
                        $selectedsubqueue = 'AssignedOrders';
                        break;

                        case 'myorderslist':
                        $selectedsubqueue = 'MyOrders';
                        break;                

                        case 'parkingorderslist':
                        $selectedsubqueue = 'parkingorders';
                        break;                

                        case 'completedorderslist':
                        $selectedsubqueue = 'CompletedOrders';
                        break;
                        case 'hoiwaitingorderstablelist':
                        $selectedsubqueue = 'hoiwaitingorders';/*Desc: export hoi waiting orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                        break;
                        case 'hoiresponsedorderstablelist':
                        $selectedsubqueue = 'hoiresponsedorders';/*Desc: export hoi responsed orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                        break;
                        case 'hoireceivedorderstablelist':
                        $selectedsubqueue = 'hoireceivedorders';/*Desc: export hoi document received orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                        break;
                        case 'hoiexceptionorderstablelist':
                        $selectedsubqueue = 'hoiexceptionorders';/*Desc: Export HOI Expectional waiting orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                        break;

                        // CD
                        case 'CDInflowOrdersList':
                        $selectedsubqueue = 'CDInflowOrders';
                        break;

                        // CD Pending
                        case 'CDPendingOrdersList':
                        $selectedsubqueue = 'CDPendingOrders';
                        break;

                        // CD Completed
                        case 'CDCompletedOrdersList':
                        $selectedsubqueue = 'CDCompletedOrders';
                        break;

                        // Expiry Orders Complete
                        case 'ExpiredCompleteorderslist':
                        $selectedsubqueue = 'ExpiredCompleteOrders';

                        default:
                        break;
                    }
                    $finding_array = array();
                    $finding_array[] = 'Model';
                    $finding_array[] = 'WorkflowUID';
                    $finding_array[] = $selectedsubqueue;
                    foreach ($value as $k => $v) {
                        if (!in_array($k, $finding_array)) {
                            unset($value[$k]);   
                        }
                    }

            //Advanced Search
                    $post['advancedsearch'] = $this->input->post('formData');
            //Advanced Search
            //get_post_input_data
                    $search = $this->input->post('search');
                    $post['search_value'] = trim($search['value']);
            //get_post_input_data
                }

                $WorkflowModuleUID = $value['WorkflowUID'];
            // Check IsDynamic Column Available            
                $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID); 
                if (!empty($QueueColumns)) {
                    $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns);
                    $post['column_order'] = $columndetails;
                    $post['IsDynamicColumn'] = true;
                }

            //NewOrders Orders
                if (array_key_exists('NewOrders', $value)) {
                    $NewOrders = $this->{$value['Model']}->{$value['NewOrders']}($post,'');
                    $IsNewOrders = 'yes';
                }
                if($controller == 'PreScreen_Orders' || $controller == 'HOI_Orders' || $controller == 'TitleTeam_Orders' || $controller == 'FHAVACaseTeam_Orders' ){
            //Expired Orders
                    if (array_key_exists('ExpiredOrders', $value)) {
                        $ExpiredOrders = $this->{$value['Model']}->{$value['ExpiredOrders']}($post,'');
                        $IsExpiredOrders = 'yes';
                    }
                }

                if($controller == 'DocsOut_Orders' ){
            //Docschecked Orders
                    if (array_key_exists('DocsCheckedConditionsPendingOrders', $value)) {
                        $DocsCheckedConditionsPendingOrders = $this->{$value['Model']}->{$value['DocsCheckedConditionsPendingOrders']}($post,'');
                        $IsDocsCheckedConditionsPendingOrders = 'yes';
                    }
            //Queuechecked Orders
                    // if (array_key_exists('QueueclearedbyFundingOrders', $value)) {
                    //     $QueueclearedbyFundingOrders = $this->{$value['Model']}->{$value['QueueclearedbyFundingOrders']}($post,'');
                    //     $IsQueueclearedbyFundingOrders = 'yes';
                    // }
            //Pendingdocs Orders
                    // if (array_key_exists('PendingDocsReleaseOrders', $value)) {
                    //     $PendingDocsReleaseOrders = $this->{$value['Model']}->{$value['PendingDocsReleaseOrders']}($post,'');
                    //     $IsPendingDocsReleaseOrders = 'yes';
                    // }
            //Pendinguw Orders
                    if (array_key_exists('PendingfromUWOrders', $value)) {
                        $PendingfromUWOrders = $this->{$value['Model']}->{$value['PendingfromUWOrders']}($post,'');
                        $IsPendingfromUWOrders = 'yes';
                    }
                    
                    //Submitted for Doc Check
                    if (array_key_exists('SubmittedforDocCheckOrders', $value)) {
                        $SubmittedforDocCheckOrders = $this->{$value['Model']}->{$value['SubmittedforDocCheckOrders']}($post,'');
                        $IsSubmittedforDocCheckOrders = 'yes';
                    }
                    
                    //Submitted for Doc Check
                    if (array_key_exists('NonWorkableOrders', $value)) {
                        $NonWorkableOrders = $this->{$value['Model']}->{$value['NonWorkableOrders']}($post,'');
                        $IsNonWorkableOrders = 'yes';
                    }
                }
                
                //Workup Rework
                if (array_key_exists('WorkupReworkOrders', $value)) {
                    $WorkupReworkOrders = $this->{$value['Model']}->{$value['WorkupReworkOrders']}($post,'');
                    $IsWorkupReworkOrders = 'yes';
                }

             //KickBackOrders Orders
                if (array_key_exists('KickBackOrders', $value)) {
                    $KickBackOrders = $this->{$value['Model']}->{$value['KickBackOrders']}($post,'');
                    $IsKickBackOrders = 'yes';
                    $IsKickBack = $this->Common_Model->is_kickback_enabledforworkflow($value['WorkflowUID']);
                }

                //ReWork Orders
                if (array_key_exists('ReWorkOrders', $value)) {
                    $ReWorkOrders = $this->{$value['Model']}->{$value['ReWorkOrders']}($post,'');
                    $IsReWorkOrders = 'yes';
                }

                //ReWork Orders
                if (array_key_exists('HOIReworkOrders', $value)) {
                    $HOIReworkOrders = $this->{$value['Model']}->{$value['HOIReworkOrders']}($post,'');
                    $IsHOIReworkOrders = 'yes';
                }

                //3A Confirmation Orders
                if (array_key_exists('ThreeAConfirmationOrders', $value)) {
                    $ThreeAConfirmationOrders = $this->{$value['Model']}->{$value['ThreeAConfirmationOrders']}($post,'');
                    $IsThreeAConfirmationOrders = 'yes';
                }

                //ReWork Pending Orders
                if (array_key_exists('ReWorkPendingOrders', $value)) {
                    $ReWorkPendingOrders = $this->{$value['Model']}->{$value['ReWorkPendingOrders']}($post,'');
                    $IsReWorkPendingOrders = 'yes';
                }

            //VANewOrders Orders
                if (array_key_exists('VANewOrders', $value)) {
                    $VANewOrders = $this->{$value['Model']}->{$value['VANewOrders']}($post,'');
                    $IsVANewOrders = 'yes';
                }
            //AssignedOrders Orders
                if (array_key_exists('AssignedOrders', $value)) {
                    $AssignedOrders = $this->{$value['Model']}->{$value['AssignedOrders']}($post,'');
                    $IsAssignedOrders = 'yes';
                }
            //MyOrders Orders
                if (array_key_exists('MyOrders', $value)) {                
                    $MyOrders = $this->{$value['Model']}->{$value['MyOrders']}($post,'');
                    $IsMyOrders = 'yes';
                }
            //parkingorders Orders
                if (array_key_exists('parkingorders', $value)) {
                    $ParkingOrders = $this->{$value['Model']}->{$value['parkingorders']}($post,'');
                    $Isparkingorders = 'yes';
                    $IsParking = $this->Common_Model->is_parking_enabledforworkflow($value['WorkflowUID']);
                }

                if($WorkflowModuleUID == $this->config->item('Workflows')['HOI'] && $HOILoans) {
                    /*Desc: Get hoi waiting orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    if (array_key_exists('hoiwaitingorders', $value)) {
                        $post['queue_status'] = 'Waiting';
                        $hoiwaitingorders = $this->{$value['Model']}->Hoi_Loan_Process($post,'');
                    }
                    /*Desc: Get hoi responsed orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    if (array_key_exists('hoiresponsedorders', $value)) {
                        $post['queue_status'] = 'Responsed';
                        $hoiresponsedorders = $this->{$value['Model']}->Hoi_Loan_Process($post,'');
                    }
                    /*Desc: Get hoi document received orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    if (array_key_exists('hoireceivedorders', $value)) {
                        $post['queue_status'] = 'Received';
                        $hoireceivedorders = $this->{$value['Model']}->Hoi_Loan_Process($post,'');
                    }
                    /*Desc: Get hoi exceptional orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    if (array_key_exists('hoiexceptionorders', $value)) {
                        $post['queue_status'] = 'Exceptional';
                        $hoiexceptionorders = $this->{$value['Model']}->Hoi_Loan_Process($post,'');
                    }
                }

                $IsDynamicColumnsAvailable = false;

                                            /**
            *Function Description: Dynamic Columns Queues
            *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
            *@since 14.5.2020
            */

                                            /* ****** Dynamic Queues Section Starts ****** */ 

                                            if (!empty($QueueColumns)) 
                                            {
                                                $ExcelSheetOrders = [];
                                                $ExcelSheetOrders['NewOrders'] = $NewOrders;

                                                if($controller == 'PreScreen_Orders' || $controller == 'HOI_Orders' || $controller == 'TitleTeam_Orders' || $controller == 'FHAVACaseTeam_Orders' ){
            //Expired Orders
                                                    $ExcelSheetOrders['ExpiredOrders'] = $ExpiredOrders;

                                                }



                 /**
            *Function Description: Dynamic Columns Queues For Docs Out Queue
            *@author Sathis Kannan.P <sathish.kannan@avanzegroup.com>
            *@since 21.7.2020
            */
                if($controller == 'DocsOut_Orders' ){

                    $ExcelSheetOrders['DocsCheckedConditionsPendingOrders'] = $DocsCheckedConditionsPendingOrders;
                    // $ExcelSheetOrders['QueueclearedbyFundingOrders'] = $QueueclearedbyFundingOrders;
                    // $ExcelSheetOrders['PendingDocsReleaseOrders'] = $PendingDocsReleaseOrders;
                    $ExcelSheetOrders['PendingfromUWOrders'] = $PendingfromUWOrders;
                    $ExcelSheetOrders['SubmittedforDocCheckOrders'] = $SubmittedforDocCheckOrders;
                    $ExcelSheetOrders['NonWorkableOrders'] = $NonWorkableOrders;

                }
                
                

                if (!empty($IsKickBack)) 
                {
                    $ExcelSheetOrders['KickBackOrders'] = $KickBackOrders;
                }
                if ($IsReWorkOrders == 'yes') 
                {
                    $ExcelSheetOrders['ReWorkOrders'] = $ReWorkOrders;
                }
                if ($IsWorkupReworkOrders == 'yes') 
                {
                    $ExcelSheetOrders['WorkupReworkOrders'] = $WorkupReworkOrders;
                }
                if ($IsHOIReworkOrders == 'yes') 
                {
                    $ExcelSheetOrders['HOIReworkOrders'] = $HOIReworkOrders;
                }
                if ($IsThreeAConfirmationOrders == 'yes') 
                {
                    $ExcelSheetOrders['ThreeAConfirmationOrders'] = $ThreeAConfirmationOrders;
                }
                if ($IsReWorkPendingOrders == 'yes') 
                {
                    $ExcelSheetOrders['ReWorkPendingOrders'] = $ReWorkPendingOrders;
                }
                if ($IsVANewOrders == 'yes') 
                {
                    $ExcelSheetOrders['VANewOrders'] = $VANewOrders;
                }
                $ExcelSheetOrders['AssignedOrders'] = $AssignedOrders;
                $ExcelSheetOrders['MyOrders'] = $MyOrders;

                if($WorkflowModuleUID == $this->config->item('Workflows')['HOI'] && $HOILoans) {
                    $ExcelSheetOrders['hoiwaitingorders'] = $hoiwaitingorders;
                    $ExcelSheetOrders['hoiresponsedorders'] = $hoiresponsedorders;
                    $ExcelSheetOrders['hoireceivedorders'] = $hoireceivedorders;
                    $ExcelSheetOrders['hoiexceptionorders'] = $hoiexceptionorders;
                }

                if (!empty($activesubqueue) || !empty($QueueUID)) {
                    // Remove the empty key
                    foreach ($ExcelSheetOrders as $k => $v) {
                        if (!in_array($k, $finding_array)) {
                            unset($ExcelSheetOrders[$k]);
                        }
                    }
                }
                foreach ($ExcelSheetOrders as $skey => $ordersheet) 
                {

                    /* ###### New Orders Sheet ###### */
                    $Mischallenous = array();
                    $Mischallenous['PageBaseLink'] = "";
                    $Mischallenous['AssignButtonClass'] = "";
                    $Mischallenous['QueueColumns'] = $QueueColumns;

                    if($skey == 'parkingorders') {
                        $Mischallenous['IsParkingQueue'] = true;
                    }

                    if($skey == 'ThreeAConfirmationOrders') {
                        $Mischallenous['IsThreeAConfirmationQueue'] = true;
                    }

                    if($skey == 'KickBackOrders') {
                        $Mischallenous['IsKickBackQueue'] = true;
                    }

                    if($skey == 'CompletedOrders') {
                        $Mischallenous['IsCompleted'] = true;
                    }

                    if($skey == 'HOIReworkOrders') {
                        $Mischallenous['IsHOIRework'] = true;
                    }
                    
                    // $Mischallenous['SubQueueSection'] = $SubQueueSection;

                    // Subqueue Section                    
                    if($skey == 'NewOrders') {

                        $Mischallenous['SubQueueSection'] = 'tblNewOrders';

                    } elseif($skey == 'ExpiredOrders') {
                        
                        $Mischallenous['IsExpiryOrdersQueue'] = TRUE;
                        $Mischallenous['SubQueueSection'] = 'Expiredorderstable';

                    } elseif($skey == 'DocsCheckedConditionsPendingOrders') {

                        $Mischallenous['SubQueueSection'] = 'DocsCheckedorderstable';

                    } /* elseif($skey == 'QueueclearedbyFundingOrders') {

                        $Mischallenous['SubQueueSection'] = 'QueueClearedorderstable';

                    } elseif($skey == 'PendingDocsReleaseOrders') {

                        $Mischallenous['SubQueueSection'] = 'PendingDocsorderstable';

                    } */ elseif($skey == 'PendingfromUWOrders') {

                        $Mischallenous['SubQueueSection'] = 'PendiingUWorderstable';

                    } elseif($skey == 'SubmittedforDocCheckOrders') {

                        $Mischallenous['SubQueueSection'] = 'SubmittedforDocCheckOrdersTable';

                    } elseif($skey == 'NonWorkableOrders') {

                        $Mischallenous['SubQueueSection'] = 'NonWorkableOrdersTable';

                    } elseif($skey == 'WorkupReworkOrders') {

                        $Mischallenous['SubQueueSection'] = 'WorkupReworkOrdersTable';

                    } elseif($skey == 'KickBackOrders') {

                        $Mischallenous['SubQueueSection'] = 'KickBackorderstable';

                    } elseif($skey == 'ReWorkOrders') {

                        $Mischallenous['SubQueueSection'] = 'ReWorkOrdersTable';

                    } elseif($skey == 'HOIReworkOrders') {

                        $Mischallenous['SubQueueSection'] = 'HOIReworkOrdersTable';

                    } elseif($skey == 'ThreeAConfirmationOrders') {

                        $Mischallenous['SubQueueSection'] = 'ThreeAConfirmationOrdersTable';

                    } elseif($skey == 'ReWorkPendingOrders') {

                        $Mischallenous['SubQueueSection'] = 'ReWorkPendingOrdersTable';

                    } elseif($skey == 'AssignedOrders') {

                        $Mischallenous['SubQueueSection'] = 'workingprogresstable';

                    } elseif($skey == 'MyOrders') {

                        $Mischallenous['SubQueueSection'] = 'myorderstable';

                    } elseif($skey == 'hoiwaitingorders') {

                        $Mischallenous['SubQueueSection'] = 'hoiwaitingorderstable';

                    } elseif($skey == 'hoiresponsedorders') {

                        $Mischallenous['SubQueueSection'] = 'hoiresponsedorderstable';

                    } elseif($skey == 'hoireceivedorders') {

                        $Mischallenous['SubQueueSection'] = 'hoireceivedorderstable';

                    } elseif($skey == 'hoiexceptionorders') {

                        $Mischallenous['SubQueueSection'] = 'hoiexceptionorderstable';

                    } elseif($skey == 'parkingorders') {
                        $Mischallenous['SubQueueSection'] = 'parkingorderstable';
                    } elseif($skey == 'CDInflowOrders') {

                        $Mischallenous['SubQueueSection'] = 'CDInflowOrdersTable';

                    } elseif($skey == 'CDPendingOrders') {

                        $Mischallenous['SubQueueSection'] = 'CDPendingOrdersTable';

                    } elseif($skey == 'CDCompletedOrders') {

                        $Mischallenous['SubQueueSection'] = 'CDCompletedOrdersTable';

                    } elseif($skey == 'ExpiredCompleteOrders') {

                        $Mischallenous['IsExpiryOrdersQueue'] = TRUE;
                        $Mischallenous['SubQueueSection'] = 'Expiredorderstable';

                    }

                    $QueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($ordersheet, $WorkflowModuleUID, $Mischallenous);

                    if ( !empty($QueueColumnsData) ) 
                    {
                        $header = $QueueColumnsData['header'];
                        $data = $QueueColumnsData['orderslist'];

                        $HEADER = [];
                        foreach ($header as $hkey => $head) {

                            $HEADER[$head] = "string";
                        }

                        $writer->writeSheetHeader($skey, $HEADER, $header_style);

                        foreach($data as $Order) {
                            $writer->writeSheetRow($skey, $Order);
                        }


                    }
                    
                }
                $IsDynamicColumnsAvailable = true;

                $NewOrders = [];
                $ExpiredOrders =[];
                $DocsCheckedConditionsPendingOrders =[];
                // $QueueclearedbyFundingOrders =[];
                // $PendingDocsReleaseOrders =[];
                $PendingfromUWOrders =[];
                $SubmittedforDocCheckOrders =[];
                $NonWorkableOrders =[];
                $WorkupReworkOrders =[];
                $AssignedOrders = [];
                $MyOrders = [];
                $KickBackOrders = [];
                $ReWorkOrders = [];
                $HOIReworkOrders = [];
                $ThreeAConfirmationOrders = [];
                $ReWorkPendingOrders = [];
                $hoiwaitingorders = [];
                $hoiresponsedorders = [];
                $hoireceivedorders = [];
                $hoiexceptionorders = [];

            }
            /* ****** Dynamic Queues Section Ends ****** */

            /*------ skipped when top if succeded --------*/
            if ($IsDynamicColumnsAvailable == false) 
            {
                if ($IsNewOrders == 'yes') {
                    $writer->writeSheetHeader('NewOrders', $ExcelHeader, $header_style);
                } 
                if ($IsExpiredOrders == 'yes') {
                    $writer->writeSheetHeader('ExpiredOrders', $ExcelHeader, $header_style);
                }
                if ($IsDocsCheckedConditionsPendingOrders == 'yes') {
                    $writer->writeSheetHeader('DocsCheckedConditionsPendingOrders', $ExcelHeader, $header_style);
                }
                // if ($IsQueueclearedbyFundingOrders == 'yes') {
                //     $writer->writeSheetHeader('QueueclearedbyFundingOrders', $ExcelHeader, $header_style);
                // }
                // if ($IsPendingDocsReleaseOrders == 'yes') {
                //     $writer->writeSheetHeader('PendingDocsReleaseOrders', $ExcelHeader, $header_style);
                // }

                if ($IsPendingfromUWOrders == 'yes') {
                    $writer->writeSheetHeader('PendingfromUWOrders', $ExcelHeader, $header_style);
                }

                if ($IsSubmittedforDocCheckOrders == 'yes') {
                    $writer->writeSheetHeader('SubmittedforDocCheckOrders', $ExcelHeader, $header_style);
                }

                if ($IsNonWorkableOrders == 'yes') {
                    $writer->writeSheetHeader('NonWorkableOrders', $ExcelHeader, $header_style);
                }

                if ($IsWorkupReworkOrders == 'yes') {
                    $writer->writeSheetHeader('WorkupReworkOrders', $ExcelHeader, $header_style);
                }

                if (!empty($IsKickBack)) {
                    $writer->writeSheetHeader('KickBackOrders', $ExcelHeader, $header_style);
                }

                if (!empty($IsReWorkOrders)) {
                    $writer->writeSheetHeader('ReWorkOrders', $ExcelHeader, $header_style);
                }

                if (!empty($IsHOIReworkOrders)) {
                    $writer->writeSheetHeader('HOIReworkOrders', $ExcelHeader, $header_style);
                }

                if (!empty($IsThreeAConfirmationOrders)) {
                    $writer->writeSheetHeader('ThreeAConfirmationOrders', $ExcelHeader, $header_style);
                }

                if (!empty($IsReWorkPendingOrders)) {
                    $writer->writeSheetHeader('ReWorkPendingOrders', $ExcelHeader, $header_style);
                } 

                if ($IsVANewOrders == 'yes') {
                    $writer->writeSheetHeader('VANewOrders', $ExcelHeader, $header_style);
                }                
                if ($IsAssignedOrders == 'yes') {
                    $writer->writeSheetHeader('AssignedOrders', $ExcelHeader1, $header_style);
                }   
                if ($IsMyOrders == 'yes') {
                    $writer->writeSheetHeader('MyOrders', $ExcelHeader2, $header_style);    
                }  
                if ($IsMyOrders == 'yes') {
                    $writer->writeSheetHeader('MyOrders', $ExcelHeader2, $header_style);    
                }                                       
            } else {


                foreach($NewOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('NewOrders', array_values($Exceldataset));
                }

                if($controller == 'PreScreen_Orders' || $controller == 'HOI_Orders' || $controller == 'TitleTeam_Orders' || $controller == 'FHAVACaseTeam_Orders' ){
                    //Expired Orders
                    foreach($ExpiredOrders as $Order) {
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                        $writer->writeSheetRow('ExpiredOrders', array_values($Exceldataset));
                    }

                }

                if($controller == 'DocsOut_Orders' ) {
                    foreach($DocsCheckedConditionsPendingOrders as $Order) {
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                        $writer->writeSheetRow('DocsCheckedConditionsPendingOrders', array_values($Exceldataset));
                    }
                    // foreach($QueueclearedbyFundingOrders as $Order) {
                    //     $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    //     $writer->writeSheetRow('QueueclearedbyFundingOrders', array_values($Exceldataset));
                    // }
                    // foreach($PendingDocsReleaseOrders as $Order) {
                    //     $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    //     $writer->writeSheetRow('PendingDocsReleaseOrders', array_values($Exceldataset));
                    // } 
                }

                foreach($PendingfromUWOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('PendingfromUWOrders', array_values($Exceldataset));
                } 

                foreach($AssignedOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->AssignedUserName,site_datetimeformat($Order->AssignedDateTime),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('AssignedOrders', array_values($Exceldataset));
                }

                foreach($MyOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('MyOrders', array_values($Exceldataset));
                }

                if($WorkflowModuleUID == $this->config->item('Workflows')['HOI'] && $HOILoans) {
                    /*Desc: export hoi waiting orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    foreach($hoiwaitingorders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                        $writer->writeSheetRow('hoiwaitingorders', array_values($Exceldataset));
                    }
                    /*Desc: export hoi responsed orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    foreach($hoiresponsedorders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                        $writer->writeSheetRow('hoiresponsedorders', array_values($Exceldataset));
                    }
                    /*Desc: export hoi dcs received orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    foreach($hoireceivedorders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                        $writer->writeSheetRow('hoireceivedorders', array_values($Exceldataset));
                    }
                    /*Desc: export hoi responsed orders @author: Santhiya M <santhiya.m@avanzegroup.com> @Since: July 4th 2020*/
                    foreach($hoiexceptionorders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                        $writer->writeSheetRow('hoiexceptionorders', array_values($Exceldataset));
                    }

                    if(!empty($IsKickBack)) {

                        foreach($KickBackOrders as $Order){
                            $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                            $writer->writeSheetRow('KickBackOrders', array_values($Exceldataset));
                        }
                    }
                }

                if($controller == 'GateKeeping_Orders' && $IsReWorkOrders == 'yes') {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('ReWorkOrders', array_values($Exceldataset));
                }

                if($IsHOIReworkOrders == 'yes') {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('HOIReworkOrders', array_values($Exceldataset));
                }

                if($controller == 'Scheduling_Orders' && $IsThreeAConfirmationOrders == 'yes') {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('ThreeAConfirmationOrders', array_values($Exceldataset));
                }

                if($controller == 'GateKeeping_Orders' && $IsReWorkPendingOrders == 'yes') {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('ReWorkPendingOrders', array_values($Exceldataset));
                }

            }

            // Funding Conditions Exception Queues
            $FundingConditionsQueueUID = array();
            $FundingConditionsQueueUID[] = $this->config->item('FundingConditionsSubQueueIDs')['ProcessorAttention'];
            $FundingConditionsQueueUID[] = $this->config->item('FundingConditionsSubQueueIDs')['TitleNotaryAttention'];

            if (!empty($activesubqueue) || !empty($QueueUID)) {
                if (isset($QueueUID) && !empty($QueueUID)) {
                    // "Funding Conditions" Sub queue Processor attention and Title/Notary Attention should be part of Scheduling Queue
                    if (in_array($QueueUID, $FundingConditionsQueueUID) && $value['WorkflowUID'] == $this->config->item('Workflows')['Scheduling']) {
                        $Queues = $this->Common_Model->getCustomerWorkflowQueues($this->config->item('Workflows')['FundingConditions'], $QueueUID); 
                    } else {
                        $Queues = $this->Common_Model->getCustomerWorkflowQueues($value['WorkflowUID'], $QueueUID); 
                    }
                }
            } else {
                if ($value['WorkflowUID'] == $this->config->item('Workflows')['Scheduling']) {
                    $SchedulingQueues = $this->Common_Model->getCustomerWorkflowQueues($value['WorkflowUID']);
                    // "Funding Conditions" Sub queue Processor attention and Title/Notary Attention should be part of Scheduling Queue
                    $FundingConditionsQueues = $this->Common_Model->getCustomerWorkflowQueues($this->config->item('Workflows')['FundingConditions'], $FundingConditionsQueueUID);
                    $Queues = array_merge( $SchedulingQueues, $FundingConditionsQueues );
                } else {
                    $Queues = $this->Common_Model->getCustomerWorkflowQueues($value['WorkflowUID']);
                }
            }

            foreach ($Queues as $key => $queue) {

                $post['QueueUID'] = $queue->QueueUID;
                $exceptionqueueorder = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, "data");

                /**
                *Function Description: Dynamic Columns Queues
                *@author Parthasarathy <parthasarathy.m@avanzegroup.com>
                *@since 14.5.2020
                */

                /* ****** Dynamic Exception Queues Section Starts ****** */

                if ($IsDynamicColumnsAvailable == true) 
                {

                    /* ###### Dynamic Exception Columns Excel Sheet ###### */
                    $Mischallenous = array();
                    $Mischallenous['PageBaseLink'] = "";
                    $Mischallenous['AssignButtonClass'] = "";
                    $Mischallenous['QueueColumns'] = $QueueColumns;
                    $Mischallenous['SubQueueDetails'] = $queue;

                    $ExceptionQueueColumnsData = $this->Common_Model->getGlobalExceptionExcelDynamicQueueColumns($exceptionqueueorder, $WorkflowModuleUID, $Mischallenous);


                    if ( !empty($ExceptionQueueColumnsData) ) 
                    {
                        $header = $ExceptionQueueColumnsData['header'];
                        $data = $ExceptionQueueColumnsData['orderslist'];

                        $HEADER = [];
                        foreach ($header as $hkey => $head) {
                            $HEADER[$head] = "string";
                        }


                        $writer->writeSheetHeader($queue->QueueName,$HEADER, $header_style);


                        foreach($data as $Order) {
                            $writer->writeSheetRow($queue->QueueName, $Order);
                        }


                    }
                }
                /* ****** Normal Exception Queues renders ****** */
                else
                {
                    $HEADER = array('Order No'=>'string','Client'=>'string','Loan No'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','Aging'=>'string','Due DateTime'=>'string','LastModified DateTime'=>'string','Raised By'=>'string','Remarks'=>'string','Raised DateTime'=>'string');

                    $writer->writeSheetHeader($queue->QueueName,$HEADER, $header_style);
                    foreach($exceptionqueueorder as $Order) {
                        $Exceldataset_ExceptionQueue = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime),$Order->RaisedBy,$Order->RaisedRemarks, site_datetimeformat($Order->RaisedDateTime));
                        $writer->writeSheetRow($queue->QueueName, array_values($Exceldataset_ExceptionQueue));
                    }
                }
            }


            if(!empty($IsParking)) 
            {
                /* ****** Dynamic Exception Queues Section Starts ****** */

                if ($IsDynamicColumnsAvailable == true) 
                {

                    /* ###### Dynamic Exception Columns Excel Sheet ###### */
                    $Mischallenous = array();
                    $Mischallenous['PageBaseLink'] = "";
                    $Mischallenous['AssignButtonClass'] = "";
                    $Mischallenous['IsParkingQueue'] = true;
                    $Mischallenous['QueueColumns'] = $QueueColumns;
                    $Mischallenous['SubQueueSection'] = 'parkingorderstable';

                    $ParkingQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($ParkingOrders, $WorkflowModuleUID, $Mischallenous);


                    if ( !empty($ParkingQueueColumnsData) ) 
                    {
                        $header = $ParkingQueueColumnsData['header'];
                        $data = $ParkingQueueColumnsData['orderslist'];

                        $HEADER = [];
                        foreach ($header as $hkey => $head) {
                            $HEADER[$head] = "string";
                        }


                        $writer->writeSheetHeader("ParkingOrders",$HEADER, $header_style);


                        foreach($data as $Order) {
                            $writer->writeSheetRow("ParkingOrders", $Order);
                        }


                    }
                }
                /* ****** Normal Parking Queues renders ****** */
                else
                {

                  $writer->writeSheetHeader('ParkingOrders',$ExcelHeader3, $header_style);
                  foreach($ParkingOrders as $Order) {
                     $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                     $writer->writeSheetRow('ParkingOrders', array_values($Exceldataset));   
                 }
             }

         }


         if(!empty($IsKickBack)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['IsKickBackQueue'] = true;
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'KickBackorderstable';

                $KickBackQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($KickBackOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($KickBackQueueColumnsData) ) 
                {
                    $header = $KickBackQueueColumnsData['header'];
                    $data = $KickBackQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("KickBackOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("KickBackOrders", $Order);
                    }


                }
            } else {

                /* ****** Normal Kickback Queues renders ****** */
                $writer->writeSheetHeader('KickBackOrders',$ExcelHeader, $header_style);
                foreach($KickBackOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('KickBackOrders', array_values($Exceldataset));   
                }
            }

        }


         if(!empty($ReWorkOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'ReWorkOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($ReWorkOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("ReWorkOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("ReWorkOrders", $Order);
                    }


                }
            } else {

                /* ****** Normal ReWork Queues renders ****** */
                $writer->writeSheetHeader('ReWorkOrders',$ExcelHeader, $header_style);
                foreach($ReWorkOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('ReWorkOrders', array_values($Exceldataset));   
                }
            }

        }


         if(!empty($HOIReworkOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['IsHOIRework'] = true;
                $Mischallenous['SubQueueSection'] = 'HOIReworkOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($HOIReworkOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("HOIReworkOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("HOIReworkOrders", $Order);
                    }


                }
            } else {

                /* ****** Normal ReWork Queues renders ****** */
                $writer->writeSheetHeader('HOIReworkOrders',$ExcelHeader, $header_style);
                foreach($HOIReworkOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('HOIReworkOrders', array_values($Exceldataset));   
                }
            }

        }


         if(!empty($ThreeAConfirmationOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['IsThreeAConfirmationQueue'] = true;
                $Mischallenous['SubQueueSection'] = 'ThreeAConfirmationOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($ThreeAConfirmationOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("ThreeAConfirmationOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("ThreeAConfirmationOrders", $Order);
                    }


                }
            } else {

                /* ****** Normal 3A Confirmation Queues renders ****** */
                $writer->writeSheetHeader('ThreeAConfirmationOrders',$ExcelHeader, $header_style);
                foreach($ThreeAConfirmationOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('ThreeAConfirmationOrders', array_values($Exceldataset));   
                }
            }

        }


         if(!empty($ReWorkPendingOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'ReWorkPendingOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($ReWorkPendingOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("ReWorkPendingOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("ReWorkPendingOrders", $Order);
                    }


                }
            } else {

                /* ****** Normal ReWork Pending Queues renders ****** */
                $writer->writeSheetHeader('ReWorkPendingOrders',$ExcelHeader, $header_style);
                foreach($ReWorkPendingOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('ReWorkPendingOrders', array_values($Exceldataset));   
                }
            }

        }

            //Completed Orders
        if (in_array('CompletedOrders', $value)) {           
                //Get WorkflowModuleUID
            $WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CompletedOrders = [];

            if ($IsDynamicColumnsAvailable == true) 
            {
                $post['IsDynamicColumns'] = true;
                foreach ($QueueColumns as $key => $queuecolumnsvalue) {
                    if ($queuecolumnsvalue->NoSort == 1) 
                    {
                        if (!empty($queuecolumnsvalue->SortColumnName)) 
                        {
                            $post['column_order'][] = $queuecolumnsvalue->SortColumnName;
                        }
                        else
                        {
                            $post['column_order'][] = "";
                        }
                    }
                    else
                    {
                        $post['column_order'][] = $queuecolumnsvalue->ColumnName;
                    }

                }
            }

            $CompletedOrders = $this->Common_Model->CompletedOrdersBasedOnWorkflow($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Completed Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['IsCompleted'] = true;
                $Mischallenous['SubQueueSection'] = 'completedorderstable';

                $CompletedQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($CompletedOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CompletedQueueColumnsData) ) 
                {
                    $header = $CompletedQueueColumnsData['header'];
                    $data = $CompletedQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("CompletedOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("CompletedOrders", $Order);
                    }


                }
            }
            /* ****** Normal Completed Queues renders ****** */
            else
            {

                /* Completed Orders Headers*/
                $ExcelHeaderforCompletedOrders = array('Order No'=> 'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','LastModified DateTime'=>'string','Completed By'=>'string','Complted Date and Time'=>'string');
                /* Completed Orders Sheet Name and Headers*/
                $writer->writeSheetHeader('CompletedOrders', $ExcelHeaderforCompletedOrders, $header_style);
                /* print rows */
                foreach($CompletedOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime),$Order->completedby,site_datetimeformat($Order->completeddatetime));
                    $writer->writeSheetRow('CompletedOrders', array_values($Exceldataset));
                }
            }
        }

        /**
        *Function CD Inflow 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Date.
        */
        if (in_array('CDInflowOrders', $value)) {

            //Get WorkflowModuleUID
            $WorkflowModuleUID = $this->config->item('workflowcontroller')['CD_Orders'];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CDInflowOrders = [];

            $CDInflowOrders = $this->{$value['Model']}->{$value['CDInflowOrders']}($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDInflow'] = true;
                $Mischallenous['SubQueueSection'] = 'CDInflowOrdersTable';

                $CDInflowQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($CDInflowOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CDInflowQueueColumnsData) ) 
                {
                    $header = $CDInflowQueueColumnsData['header'];
                    $data = $CDInflowQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("CDInflowOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("CDInflowOrders", $Order);
                    }


                }
            }
            /* ****** Normal Queues renders ****** */
            else
            {

                /* Headers*/
                $ExcelHeaderforCDInflowOrders = array('Order No'=> 'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','LastModified DateTime'=>'string','Completed By'=>'string','Complted Date and Time'=>'string');
                /* Sheet Name and Headers*/
                $writer->writeSheetHeader('CDInflowOrders', $ExcelHeaderforCDInflowOrders, $header_style);
                /* print rows */
                foreach($CDInflowOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('CDInflowOrders', array_values($Exceldataset));
                }
            }
        }        

        /**
        *Function CD Pending 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Date.
        */
        if (in_array('CDPendingOrders', $value)) {

            //Get WorkflowModuleUID
            $WorkflowModuleUID = $this->config->item('workflowcontroller')['CD_Orders'];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CDPendingOrders = [];

            $CDPendingOrders = $this->{$value['Model']}->{$value['CDPendingOrders']}($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDPending'] = true;
                $Mischallenous['SubQueueSection'] = 'CDPendingOrdersTable';

                $CDPendingQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($CDPendingOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CDPendingQueueColumnsData) ) 
                {
                    $header = $CDPendingQueueColumnsData['header'];
                    $data = $CDPendingQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("CDPendingOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("CDPendingOrders", $Order);
                    }


                }
            }
            /* ****** Normal Queues renders ****** */
            else
            {

                /* Headers*/
                $ExcelHeaderforCDPendingOrders = array('Order No'=> 'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','LastModified DateTime'=>'string','Completed By'=>'string','Complted Date and Time'=>'string');
                /* Sheet Name and Headers*/
                $writer->writeSheetHeader('CDPendingOrders', $ExcelHeaderforCDPendingOrders, $header_style);
                /* print rows */
                foreach($CDPendingOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('CDPendingOrders', array_values($Exceldataset));
                }
            }
        }

        /**
        *Function CD Completed 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Date.
        */
        if (in_array('CDCompletedOrders', $value)) {

            //Get WorkflowModuleUID
            $WorkflowModuleUID = $this->config->item('workflowcontroller')['CD_Orders'];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CDCompletedOrders = [];

            // $CDCompletedOrders = $this->{$value['Model']}->{$value['CDCompletedOrders']}($post,'');
            $CDCompletedOrders = $this->Common_Model->CompletedOrdersBasedOnWorkflow($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDCompleted'] = true;
                $Mischallenous['IsCompleted'] = true;
                $Mischallenous['SubQueueSection'] = 'CDCompletedOrdersTable';

                $CDCompletedQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($CDCompletedOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CDCompletedQueueColumnsData) ) 
                {
                    $header = $CDCompletedQueueColumnsData['header'];
                    $data = $CDCompletedQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("CDCompletedOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("CDCompletedOrders", $Order);
                    }


                }
            }
            /* ****** Normal Queues renders ****** */
            else
            {

                /* Headers*/
                $ExcelHeaderforCDCompletedOrders = array('Order No'=> 'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','LastModified DateTime'=>'string','Completed By'=>'string','Complted Date and Time'=>'string');
                /* Sheet Name and Headers*/
                $writer->writeSheetHeader('CDCompletedOrders', $ExcelHeaderforCDCompletedOrders, $header_style);
                /* print rows */
                foreach($CDCompletedOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('CDCompletedOrders', array_values($Exceldataset));
                }
            }
        }

        /**
        *Function Expiry Orders Complete 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Monday 12 October 2020.
        */
        if (in_array('ExpiredCompleteOrders', $value)) {

            //Get WorkflowModuleUID
            $WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $ExpiredCompleteOrders = [];

            $ExpiredCompleteOrders = $this->Common_Model->ExpiredCompleteOrders($post);

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDInflow'] = true;
                $Mischallenous['SubQueueSection'] = 'ExpiredCompleteOrdersTable';

                $ExpiryCompletedOrdersData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($ExpiredCompleteOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ExpiryCompletedOrdersData) ) 
                {
                    $header = $ExpiryCompletedOrdersData['header'];
                    $data = $ExpiryCompletedOrdersData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader("ExpiredCompleteOrders",$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow("ExpiredCompleteOrders", $Order);
                    }


                }
            }
            /* ****** Normal Queues renders ****** */
            else
            {

                /* Headers*/
                $ExcelHeaderforExpiredCompleteOrders = array('Order No'=> 'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','LastModified DateTime'=>'string','Completed By'=>'string','Complted Date and Time'=>'string');
                /* Sheet Name and Headers*/
                $writer->writeSheetHeader('ExpiredCompleteOrders', $ExcelHeaderforExpiredCompleteOrders, $header_style);
                /* print rows */
                foreach($ExpiredCompleteOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime));
                    $writer->writeSheetRow('ExpiredCompleteOrders', array_values($Exceldataset));
                }
            }
        }

    }

    $filename = $controller.'.xlsx';

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

function get_newchecklistrow()
{
 $OrderUID = $this->input->post('OrderUID');
 $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
 $count = $this->input->post('count');

 $returnhtml = '<tr class=" questionlist removeRow">';

 $returnhtml .= ' <td> <textarea placeholder="Checklist" class="form-control checklists" name="checklist['.$WorkflowModuleUID.'][OtherChecklist][N'.$count.'][question]" rows="1"></textarea> </td> <td> <select name="checklist['.$WorkflowModuleUID.'][OtherChecklist][N'.$count.'][Answer]" title="Findings"  class="form-control form-check-input1 checklists pre_select" > <option value="empty"></option> <option value="Completed">Completed</option> <option value="Problem Identified">Issue</option> <option value="NA">NA</option> </select> </td>';

		//CHECKLIST DYNAMIC FIELDS
 $ChecklistFields = $this->Common_Model->get_dynamicchecklistfields($OrderUID,$WorkflowModuleUID);

 if(!empty($ChecklistFields)) {
    foreach ($ChecklistFields as $key => $ChecklistField) {

       if($ChecklistField->FieldType == 'checkbox') {
           $returnhtml .= '<td class="form-check dynamicCheckedList" style="text-align: center;border: 0!important;padding-top: 7px!important;"> <label class="form-check-label Dashboardlable" title="'.$ChecklistField->FieldLabel.'" for="['.$ChecklistField->WorkflowModuleUID.'][OtherChecklist'.$count.']['.$ChecklistField->FieldName.']"  style="color: teal"> <input class="form-check-input checklists allworkflow " id = "['.$ChecklistField->WorkflowModuleUID.'][OtherChecklist'.$count.']['.$ChecklistField->FieldName.']" type="checkbox"  name="checklist['.$ChecklistField->WorkflowModuleUID.'][OtherChecklist][N'.$count.']['.$ChecklistField->FieldName.']"> <span class="form-check-sign"> <span class="check"></span> </span> </label> </td>';
       } else if($ChecklistField->FieldType == 'combobox') {

          $checklistdropdown = $this->Common_Model->get_dynamicchecklistdropdownfields($ChecklistField->FieldUID);
          $dropdownvalues = '<option value="empty"></option>';
          foreach ($checklistdropdown as $checklistdropdownkey => $checklistdropdownvalue) { 

             $dropdownvalues .= '<option value="'.$checklistdropdownvalue->DropDownName.'">'.$checklistdropdownvalue->DropDownName.'</option>';

         }
         $returnhtml .='<td> <select name="checklist['.$ChecklistField->WorkflowModuleUID.'][OtherChecklist][N'.$count.']['.$ChecklistField->FieldName.']" title="'.$ChecklistField->FieldLabel.'"  class="form-control form-check-input1 checklists pre_select"> '.$dropdownvalues.'</select>
         </td>';
     } else if($ChecklistField->FieldType == 'date') {

      $returnhtml .='<td> <span class="bmd-form-group"> <input type="text" title="'.$ChecklistField->FieldLabel.'" class="form-control checklists checklistdatepicker '.$ChecklistField->FieldName.'" name="checklist['.$ChecklistField->WorkflowModuleUID.'][OtherChecklist][N'.$count.']['.$ChecklistField->FieldName.']" value=""> </span> </td>';

  } else if($ChecklistField->FieldType == 'label') {

    $ExpirationDuration = $ChecklistField->ExpirationDuration;
    if(!empty($ChecklistField->StateCode) && in_array($OrderDetails->PropertyStateCode, explode(',', $ChecklistField->StateCode))) {
        $ExpirationDuration = $ChecklistField->StateExpirationDuration;
    }

    $returnhtml .='<td> <span class="bmd-form-group"> <input type="text" title="'.$ChecklistField->FieldLabel.'" class="form-control checklists" name="checklist['.$ChecklistField->WorkflowModuleUID.'][OtherChecklist][N'.$count.']['.$ChecklistField->FieldName.']" value="" data-expiration="'.$ExpirationDuration.'" readonly> </span> </td>';

} else {
  $returnhtml .='<td> <span class="bmd-form-group"> <input type="text" title="'.$ChecklistField->FieldLabel.'" class="form-control checklists" name="checklist['.$ChecklistField->WorkflowModuleUID.'][OtherChecklist][N'.$count.']['.$ChecklistField->FieldName.']" value=""> </span> </td>';
}

}
}
		//CHECKLIST DYNAMIC FIELDS

$returnhtml .= '<td class="icon_minus_td"> <textarea title="Comments" class="form-control checklists" name="checklist['.$WorkflowModuleUID.'][OtherChecklist][N'.$count.'][Comments]" rows="1"></textarea> <a style="width:8%;float:right;"><i class="fa fa-trash removechecklist pull-right" aria-hidden="true" style="font-size: 20px;margin-top: 10px;color:red;"></i></a> </td>';

$returnhtml .= '</tr>';

echo $returnhtml;

}

    // Get Completed Orders based workflow
function completedordersbasedonworkflow_ajax_list()
{
        //Get WorkflowModuleUID
    $controller = $this->input->get('controller');
    if (isset($this->config->item('workflowcontroller')[$controller])) {
        $WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
        $post['WorkflowModuleUID'] = $WorkflowModuleUID;
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
            //get_post_input_data
            //column order
        $post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime','mUsers.UserName','tOrderAssignments.CompleteDateTime');

            //column search
        $post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode','tOrders.LastModifiedDateTime','mUsers.UserName','tOrderAssignments.CompleteDateTime');

        /* ****** Dynamic Queues Section Starts ****** */
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
        if (!empty($QueueColumns)) 
        {
           $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
           $post['column_order'] = $columndetails;
           $post['column_search'] = array_filter($columndetails);
           array_push($post['column_order'],'mUsers.UserName','tOrderAssignments.CompleteDateTime');
           array_push($post['column_search'],'mUsers.UserName','tOrderAssignments.CompleteDateTime');
       }

       $list = $this->Common_Model->CompletedOrdersBasedOnWorkflow($post);

       $no = $post['start'];
       $completedorderslist = [];


       /* ****** Dynamic Queues Section Starts ****** */
       $Mischallenous['PageBaseLink'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['PageBaseLink'];
       $Mischallenous['AssignButtonClass'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['AssignButtonClass'];
       $Mischallenous['QueueColumns'] = $QueueColumns;
       $Mischallenous['IsCompleted'] = true;
       $Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
       $DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $WorkflowModuleUID, $Mischallenous);

       if (!empty($DynamicColumns)) 
       {
           $completedorderslist 				= 	$DynamicColumns['orderslist'];
           $post['column_order']		=	$DynamicColumns['column_order'];
           $post['column_search']		=	$DynamicColumns['column_search'];
           array_push($post['column_order'],'mUsers.UserName','tOrderAssignments.CompleteDateTime');
           array_push($post['column_search'],'mUsers.UserName','tOrderAssignments.CompleteDateTime');
           $list = [];
       }
       /* ****** Dynamic Queues Section Ends ****** */

       foreach ($list as $revieworders)
       {
        $row = array();
        $row[] = '<a href="'.base_url('Ordersummary/index/'.$revieworders->OrderUID).'" class="ajaxload">'.$revieworders->OrderNumber.'</a>';
        $row[] = $revieworders->CustomerName;
        $row[] = $revieworders->LoanNumber;             
        $row[] = $revieworders->LoanType;   
        $row[] = $revieworders->MilestoneName;          
        $row[] = '<a href="javascript:void(0)" style=" background: '.$revieworders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$revieworders->StatusName.'</a>';
        $row[] = $revieworders->PropertyStateCode;
        $row[] = site_datetimeformat($revieworders->LastModifiedDateTime);
        $row[] = $revieworders->completedby;
        $row[] = site_datetimeformat($revieworders->completeddatetime);

        $FollowUp = ''; 
        if(!empty($revieworders->FollowUpUID)) {
            $FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
        }

        $Action = '<div style="display: inline-flex;"><a href="'.base_url($Mischallenous['PageBaseLink'].$revieworders->OrderUID).'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload">
        <i class="icon-pencil"></i>'.$FollowUp.'</a></div>';
        $row[] = $Action;
        $completedorderslist[] = $row;
    }



    $data =  array(
        'completedorderslist' => $completedorderslist,
        'post' => $post
    );



    $post = $data['post'];
    $output = array(
        "draw" => $post['draw'],
        "recordsTotal" => $this->Common_Model->completedordersBasedOnWorkflow_count_all($post),
        "recordsFiltered" =>  $this->Common_Model->completedordersBasedOnWorkflow_count_filtered($post),
        "data" => $data['completedorderslist'],
    );

    unset($post);
    unset($data);

    echo json_encode($output);
} else {
    echo json_encode(["data"=>[]]);
}

}

function completedordersbasedonworkflow_ajax_listWriteExcel()
{
        //Get WorkflowModuleUID
    $controller = $this->input->get('controller');
    $WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
    $post['WorkflowModuleUID'] = $WorkflowModuleUID;

    if($this->input->post('formData') == 'All')
    {
        $post['advancedsearch'] = 'false';
    }
    else{

        $post['advancedsearch'] = $this->input->post('formData');
    }


    $list = $this->Common_Model->GetCompletedOrdersBasedOnWorkflowExcelRecords($post);

    $data = [];

    $data[] = array('Order No','Client','Loan No','Loan Type','Milestone','Current Status','State','Last Modified Date Time','Completed By','Completed Date and Time');

    /* ****** Dynamic Queues Section Starts ****** */
    $Mischallenous['PageBaseLink'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['PageBaseLink'];
    $Mischallenous['AssignButtonClass'] 		= $this->config->item('WorkflowDetails')[$WorkflowModuleUID]['AssignButtonClass'];
    $Mischallenous['IsCompleted'] = true;
    $QueueColumns = $this->Common_Model->getExcelDynamicQueueColumns($list, $WorkflowModuleUID, $Mischallenous);


    if ( !empty($QueueColumns) ) 
    {
       $data = $QueueColumns['orderslist'];
       $list = [];
   }

   /* ****** Dynamic Queues Section Ends ****** */

   /*------ Automaticalled skipped when top if succeded --------*/

   for ($i=0; $i < sizeof($list); $i++) { 


       $data[] = array($list[$i]->OrderNumber,$list[$i]->CustomerName,$list[$i]->LoanNumber,$list[$i]->LoanType,$list[$i]->MilestoneName,$list[$i]->StatusName,$list[$i]->PropertyStateCode,site_datetimeformat($list[$i]->LastModifiedDateTime),$list[$i]->completedby,site_datetimeformat($list[$i]->completeddatetime));               
   }

        // Output
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


    public function getWorkflowModuleUIDByControllerName($controller)
    {
        foreach ($this->config->item('WorkflowDetails') as $key => $detail) 
        {
            if (strripos($detail['screen'], $controller) !== false) 
            {
                return $key;
            }
        }

        return 0;
    }


	/**
	*Function Borrower Docs Global Excel
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Monday 25 May 2020  
	*/
	function WriteGlobalExcelSheet_BorrowerDocs()
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");
		require_once APPPATH."third_party/xlsxwriter.class.php";
		$controller = $this->input->get('controller');
        $QueueUID;
        $QueueUID = $this->input->post('QueueUID');

        $writer = new XLSXWriter();

        $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

        if (!empty($QueueUID)) {
            //Advanced Search
            $post['advancedsearch'] = $this->input->post('formData');
            //Advanced Search
            //get_post_input_data
            $search = $this->input->post('search');
            $post['search_value'] = trim($search['value']);
            //get_post_input_data
            $Queues = $this->Common_Model->get_borrowerdynamicworkflow_queues($QueueUID);
        } else {
            $post = ['length'=>'','advancedsearch'=>[]];
            $Queues = $this->Common_Model->get_borrowerdynamicworkflow_queues();
        }

        foreach ($Queues as $key => $queue) {

         $IsDynamicColumnsAvailable = false;

         $post['QueueUID'] = $queue->QueueUID;

      // Check IsDynamic Column Available            
         $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($queue->WorkflowModuleUID); 

         if (!empty($QueueColumns)) {
            $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns);
            $post['column_order'] = $columndetails;
            $post['IsDynamicColumn'] = true;
            $IsDynamicColumnsAvailable = true;
        }


        $exceptionqueueorder = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, "data");

			/**
			*Function Description: Dynamic Columns Queues
			*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
			*@since 14.5.2020
			*/

			/* ****** Dynamic Exception Queues Section Starts ****** */


			if ($IsDynamicColumnsAvailable == true) 
			{

				/* ###### Dynamic Exception Columns Excel Sheet ###### */
				$Mischallenous = array();
				$Mischallenous['PageBaseLink'] = "";
				$Mischallenous['AssignButtonClass'] = "";
				$Mischallenous['QueueColumns'] = $QueueColumns;

				$ExceptionQueueColumnsData = $this->Common_Model->getGlobalExceptionExcelDynamicQueueColumns($exceptionqueueorder, $queue->WorkflowModuleUID, $Mischallenous);

				if ( !empty($ExceptionQueueColumnsData) ) 
				{
					$header = $ExceptionQueueColumnsData['header'];
					$data = $ExceptionQueueColumnsData['orderslist'];

					$HEADER = [];
					foreach ($header as $hkey => $head) {
						$HEADER[$head] = "string";
					}


					$writer->writeSheetHeader($queue->QueueName,$HEADER, $header_style);


					foreach($data as $Order) {
						$writer->writeSheetRow($queue->QueueName, $Order);
					}


				}
			}
			/* ****** Normal Exception Queues renders ****** */
			else
			{
				$HEADER = array('Order No'=>'string','Client'=>'string','Loan No'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','Aging'=>'string','Due DateTime'=>'string','LastModified DateTime'=>'string','Raised By'=>'string','Remarks'=>'string','Raised DateTime'=>'string');

				$writer->writeSheetHeader($queue->QueueName,$HEADER, $header_style);
				foreach($exceptionqueueorder as $Order) {
					$Exceldataset_ExceptionQueue = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime),$Order->RaisedBy,$Order->RaisedRemarks, site_datetimeformat($Order->RaisedDateTime));
					$writer->writeSheetRow($queue->QueueName, array_values($Exceldataset_ExceptionQueue));
				}
			}

		}

		$filename = $controller.'.xlsx';

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

    /*fetch followup counts*/
    function fetch_followupcounts()
    {
     $QueueUID = $this->input->post('QueueUID');
     $data = $this->Common_Model->get_followupcounts($QueueUID); 
     echo json_encode($data);
 }

 function AssignedUsers(){

    $OrderUID = $this->input->post('OrderUID');
    $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

    // Get Order Assignment History
    $tOrderAssignmentsHistory = $this->Common_Model->GettOrderAssignmentsHistory($OrderUID, $WorkflowModuleUID);

    $tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=> $this->input->post('OrderUID')]);

    if(empty($tOrderAssignmentsHistory)){
        $row = '<td>1</td><td>'.$tOrders->LoanNumber.'</td><td>'.$this->input->post('WorkflowModuleName').'</td><td> - </td><td> - </td><td> - </td>';    
    }else{
        $row = '';
        $SNO = 1;
        foreach ($tOrderAssignmentsHistory as $key => $value) {
            $row .= '<tr><td>'.$SNO.'</td><td>'.$tOrders->LoanNumber.'</td><td>'.$this->input->post('WorkflowModuleName').'</td><td>'.$value->AssignedByUserName.'</td><td>'.$value->AssignedToUserName.'</td><td>'.site_datetimeformat($value->AssignedDatetime).'</td></tr>';
            $SNO++;
        }           
    }
    echo $row;exit;

}   
function AssignUsers(){
    $response = [];
    $OrderUID  = $this->input->post('OrderUID');
    $WorkflowModuleUID  = $this->input->post('WorkflowModuleUID');
    $tOrders = $this->Common_Model->get_row('tOrders', ['OrderUID'=>$this->input->post('OrderUID')]);
    $this->form_validation->set_rules('AssignedToUserUID', '', 'required');
    $this->form_validation->set_message('required','Please select user');
    if($this->form_validation->run()==true)
    {               
        if($this->input->post('AssignedToUserUID')){
            $assign = $this->Common_Model->OrderAssign($this->input->post('OrderUID'), $WorkflowModuleUID, $this->input->post('AssignedToUserUID'));
        }
        if($assign)
        {
            $response['validation_error'] = 0;  
            $response['color'] ='success';  
            $response['message'] = str_replace('<<Order Number>>', $tOrders->OrderNumber, $this->lang->line('Assign')) . '<br>';
        }else{
         $response['validation_error'] = 1;
         $response['color'] ='danger';
         $response['message'] = $this->lang->line('Assign_Failed');
     }


 }else{

    //Unassign user
    $Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

    // Get Order Assignment Details
    $query = $this->Common_Model->GettOrderAssignmentDetails($OrderUID, $WorkflowModuleUID);
    $is_assignment_row_available = $query->row();

    if(!empty($is_assignment_row_available)) {  
        $tOrderAssignments = [];
        $tOrderAssignments['AssignedToUserUID'] = NULL;
        $tOrderAssignments['AssignedDatetime'] = NULL;
        $tOrderAssignments['AssignedByUserUID'] = NULL;

        $assigneduser_row = $this->db->select('UserName')->from('mUsers')->where('UserUID',$is_assignment_row_available->AssignedToUserUID)->get()->row();

        $this->Common_Model->save('tOrderAssignments', $tOrderAssignments, ['OrderUID' => $OrderUID, 'WorkflowModuleUID' => $WorkflowModuleUID]);

        if(!empty($assigneduser_row)) {

            //duplicate to tOrderAssignments History 
            $this->db->insert('tOrderAssignmentsHistory', $is_assignment_row_available);
            //duplicate to tOrderAssignments History end

            /*INSERT ORDER LOGS BEGIN*/
            $this->Common_Model->OrderLogsHistory($OrderUID,$Workflow->WorkflowModuleName.' - '.$assigneduser_row->UserName.'  UnAssigned',date('Y-m-d H:i:s'));
            /*INSERT ORDER LOGS END*/
        }
    }

    $response['validation_error'] = 0;  
    $response['color'] ='success';
    $response['message'] = str_replace('<<Order Number>>', $tOrders->OrderNumber, $this->lang->line('UnAssign')) . '<br>';


}

$this->output->set_content_type('application/json')->set_output(json_encode($response));

}

// CalculatorCalculateDays
function CalculatorCalculateDays()
{

}

function ExcludeHoliday()
{
    $post = $this->input->post();
    $holidayDates = $this->Common_Model->GetHolidays();
    $datearray = [];
    foreach ($holidayDates as $key => $value) 
    {
        $datearray[] = date('m-d-Y', strtotime($value->HolidayDate));
    }   
    $countWorkingDay = 0;
    $temp = strtotime($post['StartDate']); 
    while($countWorkingDay<$post['CalculateDays']){
        $next1WD = strtotime('+1 weekday', $temp);
        $next1WDDate = date('m-d-Y', $next1WD);
        if(!in_array($next1WDDate, $datearray)){
            $countWorkingDay++;
        }
        $temp = $next1WD;
    }
    $next5WD = date("m/d/Y", $temp);
    echo $next5WD;
}

function CalculatePayOff()
{
    $post = $this->input->post();
    $PaymentDueDatePayoff = date('n',strtotime($post['PaymentDueDatePayoff'])); 
    $FundingDatePayoff = date('n',strtotime($post['FundingDatePayoff'])); 
    $result = '';

    if($post['FundingDatePayoff'] == '')
    {
        $result = '0';
    }
    else if($PaymentDueDatePayoff > $FundingDatePayoff)
    {
     $interval = $this->dateDiff($post['GoodThroughDate'], $post['GoodThroughDatePayoff']);
     $result = ($interval * $post['DailyInterestPayoff']) + $post['TotalAmountPayoff'] ;
 }
 else if (date('d',strtotime($post['FundingDatePayoff'])) > 14)
 {
    $interval = $this->dateDiff($post['GoodThroughDate'], $post['GoodThroughDatePayoff']);
    $result = ($interval * $post['DailyInterestPayoff']) + $post['TotalAmountPayoff'] + $post['LateCharge'] ;
}
else
{
    $interval = $this->dateDiff($post['GoodThroughDate'], $post['GoodThroughDatePayoff']);
    $result = ($interval * $post['DailyInterestPayoff']) + $post['TotalAmountPayoff'] ;
}
echo number_format(round($result,2),2);
}

function CalculateTotalIntrest()
{
    $post = $this->input->post();
    $PaymentDueDatePayoff = date('n',strtotime($post['PaymentDueDatePayoff'])); 
    $FundingDatePayoff = date('n',strtotime($post['FundingDatePayoff'])); 
    $result = '';

    if($post['FundingDatePayoff'] == '')
    {
        $result = '0';
    }
    else
    {
        $interval = $this->dateDiff($post['GoodThroughDate'], $post['GoodThroughDatePayoff']);
        $result = $interval * $post['DailyInterestPayoff'] ;
    }
    echo number_format(round($result,2),2);
}



function dateDiff($date1, $date2) 
{
  $date1 = date_create($date1);
  $date2 = date_create($date2);
  $diff = date_diff($date1,$date2);
  $result = $diff->format("%a");
  if ($date2 > $date1)
  {
    $result = -$result;
}
return $result;
}

function InsertCalcData()
{
    $post = $this->input->post();

    $CheckIfExsist = $this->Common_Model->get_row('`tCalculator', ['OrderUID'=>$post['OrderUID'], 'WorkflowModuleUID'=>$post['WorkflowModuleUID']]);
    if(!empty($CheckIfExsist))
    {
        $tCalculator['FundingDate'] = !empty($post['FundingDate']) ? date('Y-m-d',strtotime($post['FundingDate'])) : '';
        $tCalculator['FirstPaymentDate'] = !empty($post['FirstPaymentDate']) ? date('Y-m-d',strtotime($post['FirstPaymentDate'])) : '';
        $tCalculator['PremiumAmount'] = $post['PremiumAmount'];
        $tCalculator['HazardInsurance'] = $post['HazardInsurance'];
        $tCalculator['PolicyExpirationDate'] = !empty($post['PolicyExpiration']) ? date('Y-m-d',strtotime($post['PolicyExpiration'])) : '';
        $tCalculator['FundingDateAdd'] = !empty($post['FundingDateAdd']) ? date('Y-m-d',strtotime($post['FundingDateAdd'])) : '';
        $tCalculator['FinalOutPut'] = $post['FinalOutPut'];
        $tCalculator['RenewalPolicy'] = $post['RenewalPolicy'];
        $tCalculator['InsuranceNextDueDate'] = !empty($post['InsuranceNextDueDate']) ? date('Y-m-d',strtotime($post['InsuranceNextDueDate'])) : '';
        $this->Common_Model->save('tCalculator', $tCalculator, ['OrderUID'=>$post['OrderUID'], 'WorkflowModuleUID'=>$post['WorkflowModuleUID']]);
    }   
    else 
    {
        $tCalculator['OrderUID'] = $post['OrderUID'];
        $tCalculator['WorkflowModuleUID'] = $post['WorkflowModuleUID'];
        $tCalculator['FundingDate'] = !empty($post['FundingDate']) ? date('Y-m-d',strtotime($post['FundingDate'])) : '';
        $tCalculator['FirstPaymentDate'] = !empty($post['FirstPaymentDate']) ? date('Y-m-d',strtotime($post['FirstPaymentDate'])) : '';
        $tCalculator['PremiumAmount'] = $post['PremiumAmount'];
        $tCalculator['HazardInsurance'] = $post['HazardInsurance'];
        $tCalculator['PolicyExpirationDate'] = !empty($post['PolicyExpiration']) ? date('Y-m-d',strtotime($post['PolicyExpiration'])) : '';
        $tCalculator['FundingDateAdd'] = !empty($post['FundingDateAdd']) ? date('Y-m-d',strtotime($post['FundingDateAdd'])) : '';
        $tCalculator['FinalOutPut'] = $post['FinalOutPut'];
        $tCalculator['RenewalPolicy'] = $post['RenewalPolicy'];
        $tCalculator['InsuranceNextDueDate'] = !empty($post['InsuranceNextDueDate']) ? date('Y-m-d',strtotime($post['InsuranceNextDueDate'])) : '';
        $this->Common_Model->save('tCalculator', $tCalculator);

    }
    $this->CalculatorAuditLog($tCalculator,$CheckIfExsist,$post['OrderUID'],$post['WorkflowModuleUID'],'InsuranceCalculator');

}

function InsertPayOffCalcData()
{
    $post = $this->input->post();
    $CheckIfExsist = $this->Common_Model->get_row('`tCalculator', ['OrderUID'=>$post['OrderUID'], 'WorkflowModuleUID'=>$post['WorkflowModuleUID']]);
    if(!empty($CheckIfExsist))
    {
        $tCalculator['FundingDatePayoff'] = !empty($post['FundingDatePayoff']) ? date('Y-m-d',strtotime($post['FundingDatePayoff'])) : '';
        $tCalculator['DailyInterestPayoff'] = $post['DailyInterestPayoff'];
        $tCalculator['LateCharge'] = $post['LateCharge'];
        $tCalculator['TotalAmountPayoff'] = $post['TotalAmountPayoff'];
        $tCalculator['GoodThroughDate'] = !empty($post['GoodThroughDate']) ? date('Y-m-d',strtotime($post['GoodThroughDate'])) : '';
        $tCalculator['PaymentDueDatePayoff'] = !empty($post['PaymentDueDatePayoff']) ? date('Y-m-d',strtotime($post['PaymentDueDatePayoff'])) : '';
        $tCalculator['GoodThroughDatePayoff'] = !empty($post['GoodThroughDatePayoff']) ? date('Y-m-d',strtotime($post['GoodThroughDatePayoff'])) : '';
        $tCalculator['TotalInterest'] = trim($post['TotalInterest']);
        $tCalculator['UseThisPayoff'] = trim($post['UseThisPayoff']);
        $this->Common_Model->save('tCalculator', $tCalculator, ['OrderUID'=>$post['OrderUID'], 'WorkflowModuleUID'=>$post['WorkflowModuleUID']]);
    }   
    else 
    {
        $tCalculator['OrderUID'] = $post['OrderUID'];
        $tCalculator['WorkflowModuleUID'] = $post['WorkflowModuleUID'];
        $tCalculator['FundingDatePayoff'] = !empty($post['FundingDatePayoff']) ? date('Y-m-d',strtotime($post['FundingDatePayoff'])) : '';
        $tCalculator['DailyInterestPayoff'] = $post['DailyInterestPayoff'];
        $tCalculator['LateCharge'] = $post['LateCharge'];
        $tCalculator['TotalAmountPayoff'] = $post['TotalAmountPayoff'];
        $tCalculator['GoodThroughDate'] = !empty($post['GoodThroughDate']) ? date('Y-m-d',strtotime($post['GoodThroughDate'])) : '';
        $tCalculator['PaymentDueDatePayoff'] = !empty($post['PaymentDueDatePayoff']) ? date('Y-m-d',strtotime($post['PaymentDueDatePayoff'])) : '';
        $tCalculator['GoodThroughDatePayoff'] = !empty($post['GoodThroughDatePayoff']) ? date('Y-m-d',strtotime($post['GoodThroughDatePayoff'])) : '';
        $tCalculator['TotalInterest'] = trim($post['TotalInterest']);
        $tCalculator['UseThisPayoff'] = trim($post['UseThisPayoff']);
        $this->Common_Model->save('tCalculator', $tCalculator);

    }

    $this->CalculatorAuditLog($tCalculator,$CheckIfExsist,$post['OrderUID'],$post['WorkflowModuleUID'],'PayOffCalculator');

}

function EscrowCalculator()
{
    $post = $this->input->post();
    $resultArray = [];


        //anual tax calculation start
    if($post['EscrowTax'] == 'Annual')
    {
        if(!empty($post['EscrowFundingDate']))
        {
           $resultArray['EscrowFundingDateAdd'] = date("m/d/Y" ,strtotime("+60 days", strtotime($post['EscrowFundingDate'])));
       }
       else
       {
        $resultArray['EscrowFundingDateAdd'] = '';
    }

    $EscrowTaxDueDate1 = strtotime($post['EscrowTaxDueDate1']);
    $EscrowFundingDateAdd = strtotime($resultArray['EscrowFundingDateAdd']);
    $EscrowFirstPaymentDate = strtotime($post['EscrowFirstPaymentDate']);

    if($post['EscrowTaxDueDate1'] != '')
    {
        if($EscrowTaxDueDate1 > $EscrowFundingDateAdd)
        {
            $resultArray['EscrowFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';
            $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
            $resultArray['EscrowNextDueDate2'] = '';
            $resultArray['EscrowNextDueDate3'] = '';
            $resultArray['EscrowNextDueDate4'] = '';
        }
        else
        {
            if($post['UpdatedTaxBill'] == 'YES')
            {
                $resultArray['EscrowFinalOutPut'] = 'Collect $'.$post['EscrowTaxDueDate1Amount'].' in line 904 in 2015 itemization screen';
                if($post['EscrowFirstPaymentDate'] != '')
                {
                    $resultArray['EscrowNextDueDate1'] = date('m/d/Y',strtotime(date("Y-m-d", $EscrowFirstPaymentDate) . " +1 year -1 month last day of this month"));

                    $resultArray['EscrowNextDueDate2'] = '';
                    $resultArray['EscrowNextDueDate3'] = '';
                    $resultArray['EscrowNextDueDate4'] = '';
                }
            }
            else
            {
                $resultArray['EscrowFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';
                $resultArray['EscrowNextDueDate2'] = '';
                $resultArray['EscrowNextDueDate3'] = '';
                $resultArray['EscrowNextDueDate4'] = '';

                if ($EscrowFirstPaymentDate == '') {
                    $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
                }
                else if ($EscrowFirstPaymentDate > $EscrowTaxDueDate1) 
                {
                  $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowFirstPaymentDate);
              }
              else
              {
               $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
           }
       }
   }
}
}
        //annual tax calculation end

        //semi annual tax calculation start
elseif ($post['EscrowTax'] == 'SemiAnnual') 
{
    if(!empty($post['EscrowFundingDate']))
    {
     $resultArray['EscrowFundingDateAdd'] = date("m/d/Y" ,strtotime("+60 days", strtotime($post['EscrowFundingDate'])));
 }
 else
 {
   $resultArray['EscrowFundingDateAdd'] = '';
}

$EscrowTaxDueDate1 = strtotime($post['EscrowTaxDueDate1']);
$EscrowTaxDueDate2 = strtotime($post['EscrowTaxDueDate2']);
$EscrowFundingDateAdd = strtotime($resultArray['EscrowFundingDateAdd']);
$EscrowFirstPaymentDate = strtotime($post['EscrowFirstPaymentDate']);


if($post['EscrowTaxDueDate1'] != '')
{
    if($EscrowTaxDueDate2 != '' && $EscrowTaxDueDate2 < $EscrowTaxDueDate1)
    {
        $resultArray['EscrowTaxDueDate1'] = '';
        $resultArray['EscrowTaxDueDate2'] = '';
        $resultArray['EscrowTaxDueDate3'] = '';
        $resultArray['EscrowTaxDueDate4'] = '';
    }

    if($EscrowTaxDueDate1 > $EscrowFundingDateAdd)
    {
        $resultArray['EscrowFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';
        $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
        $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
        $resultArray['EscrowNextDueDate3'] = '';
        $resultArray['EscrowNextDueDate4'] = '';
    }
    else
    {  
        if($post['UpdatedTaxBill'] == 'YES')
        {
            $resultArray['EscrowFinalOutPut'] = 'Collect $'.$post['EscrowTaxDueDate1Amount'].' in line 904 in 2015 itemization screen';
            if($post['EscrowFirstPaymentDate'] != '')
            {
                $FirstPaymentPlus12 = strtotime(date("Y-m-d", $EscrowFirstPaymentDate) . " +1 year -1 month last day of this month");
                $EscrowNextDueDate2 = strtotime("+1 years",$EscrowTaxDueDate1);

                if($FirstPaymentPlus12 >= $EscrowNextDueDate2)
                {
                    $resultArray['EscrowNextDueDate1'] = date('m/d/Y', $EscrowTaxDueDate2);
                    $resultArray['EscrowNextDueDate2'] = date('m/d/Y', $EscrowNextDueDate2);
                    $resultArray['EscrowNextDueDate3'] = '';
                    $resultArray['EscrowNextDueDate4'] = '';
                }
                else
                {
                    $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate2);
                    $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$FirstPaymentPlus12);
                    $resultArray['EscrowNextDueDate3'] = '';
                    $resultArray['EscrowNextDueDate4'] = '';
                }      
            }
        }
        else
        {
            $resultArray['EscrowFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';
            $resultArray['EscrowNextDueDate2'] = '';
            $resultArray['EscrowNextDueDate3'] = '';
            $resultArray['EscrowNextDueDate4'] = '';

            if ($EscrowFirstPaymentDate > $EscrowTaxDueDate1) 
            {
              $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowFirstPaymentDate);
              $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
          }
          else
          {
           $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
           $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
       }
   }
}
}    
}
        //semi annual tax calculation end


        //Quarterly tax calculation start
elseif ($post['EscrowTax'] == 'Quarterly') 
{
    if(!empty($post['EscrowFundingDate']))
    {
     $resultArray['EscrowFundingDateAdd'] = date("m/d/Y" ,strtotime("+60 days", strtotime($post['EscrowFundingDate'])));
 }
 else
 {
   $resultArray['EscrowFundingDateAdd'] = '';
}

$post['EscrowTaxDueDate3'] = !empty($post['EscrowTaxDueDate3']) ? $post['EscrowTaxDueDate3'] : date("m/d/Y" ,strtotime("+90 days", strtotime($post['EscrowTaxDueDate2']))) ;
$post['EscrowTaxDueDate4'] = !empty($post['EscrowTaxDueDate4']) ? $post['EscrowTaxDueDate4'] : date("m/d/Y" ,strtotime("+90 days", strtotime($post['EscrowTaxDueDate3']))) ;

$EscrowTaxDueDate1 = strtotime($post['EscrowTaxDueDate1']);
$EscrowTaxDueDate2 = strtotime($post['EscrowTaxDueDate2']);
$EscrowTaxDueDate3 = strtotime($post['EscrowTaxDueDate3']);
$EscrowTaxDueDate4 = strtotime($post['EscrowTaxDueDate4']);
$EscrowFundingDateAdd = strtotime($resultArray['EscrowFundingDateAdd']);
$EscrowFirstPaymentDate = strtotime($post['EscrowFirstPaymentDate']);

            //result the tax duedate 3 and 4

            //$resultArray['EscrowTaxDueDate3'] = 
$resultArray['EscrowTaxDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
$resultArray['EscrowTaxDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
$resultArray['EscrowTaxDueDate3'] = date('m/d/Y',$EscrowTaxDueDate3);
$resultArray['EscrowTaxDueDate4'] = date('m/d/Y',$EscrowTaxDueDate4);

if($post['EscrowTaxDueDate1'] != '')
{
                // if($post['EscrowTaxDueDate1'] != '' && $post['EscrowTaxDueDate2'] != '' && $post['EscrowTaxDueDate3'] != '' && $post['EscrowTaxDueDate4'] != '')
                // {

                // }
    if(($EscrowTaxDueDate4 < $EscrowTaxDueDate3) || ($EscrowTaxDueDate3 < $EscrowTaxDueDate2) || ($EscrowTaxDueDate2 < $EscrowTaxDueDate1))
    {
        $resultArray['EscrowTaxDueDate1'] = '';
        $resultArray['EscrowTaxDueDate2'] = '';
        $resultArray['EscrowTaxDueDate3'] = '';
        $resultArray['EscrowTaxDueDate4'] = '';
    }

    if($EscrowTaxDueDate1 > $EscrowFundingDateAdd)
    {
        $resultArray['EscrowFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';
        $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
        $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
        $resultArray['EscrowNextDueDate3'] = date('m/d/Y',$EscrowTaxDueDate3);
        $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowTaxDueDate4);
    }
    else
    {  
        if($post['UpdatedTaxBill'] == 'YES')
        {
            if($EscrowTaxDueDate2 < $EscrowFundingDateAdd)
            {
                $AddAmount = $post['EscrowTaxDueDate1Amount'] + $post['EscrowTaxDueDate2Amount'];
                $resultArray['EscrowFinalOutPut'] = 'Collect $'.$AddAmount.' in line 904 in 2015 itemization screen';
                $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate3);
                $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate4);
                $resultArray['EscrowNextDueDate3'] = date('m/d/Y',strtotime("+1 years",$EscrowTaxDueDate1));

                $EscrowFirstPaymentDateCheck = strtotime(date("Y-m-d", $EscrowFirstPaymentDate) . " +1 year -1 month last day of this month");
                $EscrowTaxDueDate2Check = strtotime("+1 years",$EscrowTaxDueDate2);

                if($EscrowFirstPaymentDateCheck >= $EscrowTaxDueDate2Check)
                {
                    $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowTaxDueDate2Check);
                }
                else
                {
                    $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowFirstPaymentDateCheck);
                }

            }
            else
            {
                $EscrowFirstPaymentDateCheck = strtotime("+1 years",$EscrowFirstPaymentDate);
                $EscrowTaxDueDate2Check = strtotime("+1 years",$EscrowTaxDueDate2);
                $resultArray['EscrowFinalOutPut'] = 'Collect $'.$post['EscrowTaxDueDate1Amount'].' in line 904 in 2015 itemization screen';
                if($EscrowFirstPaymentDateCheck != '')
                {
                    $EscrowFirstPaymentDateCheck = strtotime(date("Y-m-d", $EscrowFirstPaymentDate) . " +1 year -1 month last day of this month");
                    $EscrowTaxDueDate2Check = strtotime("+1 years",$EscrowTaxDueDate1);
                    if($EscrowFirstPaymentDateCheck >= $EscrowTaxDueDate2Check)
                    {
                        $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate2);
                        $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate3);
                        $resultArray['EscrowNextDueDate3'] = date('m/d/Y',$EscrowTaxDueDate4);
                        $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowTaxDueDate2Check);
                    }
                    else
                    {
                        $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate2);
                        $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate3);
                        $resultArray['EscrowNextDueDate3'] = date('m/d/Y',$EscrowTaxDueDate4);
                        $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowFirstPaymentDateCheck);
                    }

                }
            }
        }
        else 
        {
            $resultArray['EscrowFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';
            $resultArray['EscrowNextDueDate2'] = '';
            $resultArray['EscrowNextDueDate3'] = '';
            $resultArray['EscrowNextDueDate4'] = '';

            if($EscrowFirstPaymentDate == '')
            {
                $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowTaxDueDate1);
                $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
                $resultArray['EscrowNextDueDate3'] = date('m/d/Y',$EscrowTaxDueDate3);
                $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowTaxDueDate4);
            }
            else if ($EscrowFirstPaymentDate > $EscrowTaxDueDate1) 
            {
              $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowFirstPaymentDate);
              $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
              $resultArray['EscrowNextDueDate3'] = date('m/d/Y',$EscrowTaxDueDate3);
              $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowTaxDueDate4);
          }
          else
          {
            $resultArray['EscrowNextDueDate1'] = date('m/d/Y',$EscrowFirstPaymentDate);
            $resultArray['EscrowNextDueDate2'] = date('m/d/Y',$EscrowTaxDueDate2);
            $resultArray['EscrowNextDueDate3'] = date('m/d/Y',$EscrowTaxDueDate3);
            $resultArray['EscrowNextDueDate4'] = date('m/d/Y',$EscrowTaxDueDate4);
        }
    }
}
}    
}

        //queterly tax calculation end
echo json_encode($resultArray);
}

function InsertEscrowCalcData()
{
    $post = $this->input->post();
    $CheckIfExsist = $this->Common_Model->get_row('tCalculator', ['OrderUID'=>$post['CalculatorOrderUID'], 'WorkflowModuleUID'=>$post['CalculatorWorkflowModuleUID']]);
    if(!empty($CheckIfExsist))
    {
        $tCalculator['EscrowTax'] = $post['EscrowTax'];
        $tCalculator['EscrowFundingDate'] = !empty($post['EscrowFundingDate']) ? date('Y-m-d',strtotime($post['EscrowFundingDate'])) : '';
        $tCalculator['TaxAmount'] = $post['TaxAmount'];
        $tCalculator['TAXESmonthly'] = $post['TAXESmonthly'];
        $tCalculator['EscrowFundingDateAdd'] = !empty($post['EscrowFundingDateAdd']) ? date('Y-m-d',strtotime($post['EscrowFundingDateAdd'])) : '';
        $tCalculator['EscrowTaxDueDate1'] = !empty($post['EscrowTaxDueDate1']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate1'])) : '';
        $tCalculator['EscrowTaxDueDate1Amount'] = $post['EscrowTaxDueDate1Amount'];
        $tCalculator['EscrowTaxDueDate2'] = !empty($post['EscrowTaxDueDate2']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate2'])) : '';
        $tCalculator['EscrowTaxDueDate2Amount'] = $post['EscrowTaxDueDate2Amount'];
        $tCalculator['EscrowTaxDueDate3'] = !empty($post['EscrowTaxDueDate3']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate3'])) : '';
        $tCalculator['EscrowTaxDueDate3Amount'] = $post['EscrowTaxDueDate3Amount'];
        $tCalculator['EscrowTaxDueDate4'] = !empty($post['EscrowTaxDueDate4']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate4'])) : '';
        $tCalculator['EscrowTaxDueDate4Amount'] = $post['EscrowTaxDueDate4Amount'];
        $tCalculator['UpdatedTaxBill'] = trim($post['UpdatedTaxBill']);
        $tCalculator['EscrowFirstPaymentDate'] = !empty($post['EscrowFirstPaymentDate']) ? date('Y-m-d',strtotime($post['EscrowFirstPaymentDate'])) : '';
        $tCalculator['EscrowFinalOutPut'] = trim($post['EscrowFinalOutPut']);
        $tCalculator['EscrowNextDueDate1'] = !empty($post['EscrowNextDueDate1']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate1'])) : '';
        $tCalculator['EscrowNextDueDate2'] = !empty($post['EscrowNextDueDate2']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate2'])) : '';
        $tCalculator['EscrowNextDueDate3'] = !empty($post['EscrowNextDueDate3']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate3'])) : '';
        $tCalculator['EscrowNextDueDate4'] = !empty($post['EscrowNextDueDate4']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate4'])) : '';

        if ($post['EscrowTax'] == 'Annual')
        {
           $tCalculator['EscrowNextDueDate2'] = '';
           $tCalculator['EscrowNextDueDate3'] = '';
           $tCalculator['EscrowNextDueDate4'] = '';
           $tCalculator['EscrowTaxDueDate2'] = '';
           $tCalculator['EscrowTaxDueDate2Amount'] = '';
           $tCalculator['EscrowTaxDueDate3'] = '';
           $tCalculator['EscrowTaxDueDate3Amount'] = '';
           $tCalculator['EscrowTaxDueDate4'] = '';
           $tCalculator['EscrowTaxDueDate4Amount'] = '';
       }
       else if($post['EscrowTax'] == 'SemiAnnual')
       {

           $tCalculator['EscrowNextDueDate3'] = '';
           $tCalculator['EscrowNextDueDate4'] = '';
           $tCalculator['EscrowTaxDueDate3'] = '';
           $tCalculator['EscrowTaxDueDate3Amount'] = '';
           $tCalculator['EscrowTaxDueDate4'] = '';
           $tCalculator['EscrowTaxDueDate4Amount'] = '';

       }
       $this->Common_Model->save('tCalculator', $tCalculator, ['OrderUID'=>$post['CalculatorOrderUID'], 'WorkflowModuleUID'=>$post['CalculatorWorkflowModuleUID']]);
   }   
   else 
   {
    $tCalculator['EscrowTax'] = $post['EscrowTax'];
    $tCalculator['OrderUID'] = $post['CalculatorOrderUID'];
    $tCalculator['WorkflowModuleUID'] = $post['CalculatorWorkflowModuleUID'];
    $tCalculator['EscrowFundingDate'] = !empty($post['EscrowFundingDate']) ? date('Y-m-d',strtotime($post['EscrowFundingDate'])) : '';
    $tCalculator['TaxAmount'] = $post['TaxAmount'];
    $tCalculator['TAXESmonthly'] = $post['TAXESmonthly'];
    $tCalculator['EscrowFundingDateAdd'] = !empty($post['EscrowFundingDateAdd']) ? date('Y-m-d',strtotime($post['EscrowFundingDateAdd'])) : '';
    $tCalculator['EscrowTaxDueDate1'] = !empty($post['EscrowTaxDueDate1']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate1'])) : '';
    $tCalculator['EscrowTaxDueDate1Amount'] = $post['EscrowTaxDueDate1Amount'];
    $tCalculator['EscrowTaxDueDate2'] = !empty($post['EscrowTaxDueDate2']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate2'])) : '';
    $tCalculator['EscrowTaxDueDate2Amount'] = $post['EscrowTaxDueDate2Amount'];
    $tCalculator['EscrowTaxDueDate3'] = !empty($post['EscrowTaxDueDate3']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate3'])) : '';
    $tCalculator['EscrowTaxDueDate3Amount'] = $post['EscrowTaxDueDate3Amount'];
    $tCalculator['EscrowTaxDueDate4'] = !empty($post['EscrowTaxDueDate4']) ? date('Y-m-d',strtotime($post['EscrowTaxDueDate4'])) : '';
    $tCalculator['EscrowTaxDueDate4Amount'] = $post['EscrowTaxDueDate4Amount'];
    $tCalculator['UpdatedTaxBill'] = trim($post['UpdatedTaxBill']);
    $tCalculator['EscrowFirstPaymentDate'] = !empty($post['EscrowFirstPaymentDate']) ? date('Y-m-d',strtotime($post['EscrowFirstPaymentDate'])) : '';
    $tCalculator['EscrowFinalOutPut'] = trim($post['EscrowFinalOutPut']);
    $tCalculator['EscrowNextDueDate1'] = !empty($post['EscrowNextDueDate1']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate1'])) : '';
    $tCalculator['EscrowNextDueDate2'] = !empty($post['EscrowNextDueDate2']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate2'])) : '';
    $tCalculator['EscrowNextDueDate3'] = !empty($post['EscrowNextDueDate3']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate3'])) : '';
    $tCalculator['EscrowNextDueDate4'] = !empty($post['EscrowNextDueDate4']) ? date('Y-m-d',strtotime($post['EscrowNextDueDate4'])) : '';
    if ($post['EscrowTax'] == 'Annual')
    {
       $tCalculator['EscrowNextDueDate2'] = '';
       $tCalculator['EscrowNextDueDate3'] = '';
       $tCalculator['EscrowNextDueDate4'] = '';
       $tCalculator['EscrowTaxDueDate2'] = '';
       $tCalculator['EscrowTaxDueDate2Amount'] = '';
       $tCalculator['EscrowTaxDueDate3'] = '';
       $tCalculator['EscrowTaxDueDate3Amount'] = '';
       $tCalculator['EscrowTaxDueDate4'] = '';
       $tCalculator['EscrowTaxDueDate4Amount'] = '';
   }
   else if($post['EscrowTax'] == 'SemiAnnual')
   {

       $tCalculator['EscrowNextDueDate3'] = '';
       $tCalculator['EscrowNextDueDate4'] = '';
       $tCalculator['EscrowTaxDueDate3'] = '';
       $tCalculator['EscrowTaxDueDate3Amount'] = '';
       $tCalculator['EscrowTaxDueDate4'] = '';
       $tCalculator['EscrowTaxDueDate4Amount'] = '';

   }
   $this->Common_Model->save('tCalculator', $tCalculator);

}

$this->CalculatorAuditLog($tCalculator,$CheckIfExsist,$post['CalculatorOrderUID'],$post['CalculatorWorkflowModuleUID'],'EscrowCalculator');
echo 'success';      
}

function CalculatorAuditLog($tCalculator,$CheckIfExsist,$OrderUID,$WorkflowModuleUID,$CalculatorType)
{
    foreach ($tCalculator as $key => $value) 
    {
        if(strpos($key, 'Date') !== false)
        {
            $CheckIfExsist->{$key} = !empty($CheckIfExsist->{$key}) ? date('m/d/Y',strtotime($CheckIfExsist->{$key})) : '';
            $value = !empty($value) ? date('m/d/Y',strtotime($value)) : '';
        }
        if($value != $CheckIfExsist->{$key})
        {
            $tCalcAuditLog['OrderUID'] = $OrderUID;
            $tCalcAuditLog['WorkflowModuleUID'] = $WorkflowModuleUID;
            $tCalcAuditLog['CalculatorType'] = $CalculatorType;
            $tCalcAuditLog['FieldName'] = $key;
            $tCalcAuditLog['OldValue'] = $CheckIfExsist->{$key};
            $tCalcAuditLog['NewValue'] = $value;
            $tCalcAuditLog['DateTime'] = date('Y-m-d H:i:s');
            $tCalcAuditLog['UserUID'] = $this->loggedid;
            $this->Common_Model->save('tCalcAuditLog', $tCalcAuditLog);
        }
    }
}

function CalculatorAuditLogView()
{
    $post = $this->input->post();
    $this->db->select('*');
    $this->db->from('tCalcAuditLog');
    $this->db->join('mUsers','mUsers.UserUID = tCalcAuditLog.UserUID','left');
    $this->db->where(array('OrderUID'=>$post['OrderUID'],'WorkflowModuleUID'=>$post['WorkflowModuleUID'],'CalculatorType'=>$post['CalculatorType']));
    $resultArray = $this->db->get()->result();

    $i = 1;
    $result = '';
    foreach ($resultArray as $key => $value) 
    {
        $result .= '<tr>';
        $result .= '<td>'.$i.'</td>';
        $result .= '<td><strong>'.$value->FieldName.'</strong> Changed from '.$value->OldValue.' To <strong>'.$value->NewValue.'</strong></td>';
        $result .= '<td>'.date('m/d/Y h:i A',strtotime($value->DateTime)).'</td>';
        $result .= '<td>'.$value->UserName.'</td>';
        $result .= '</tr>';
        $i++;
    }
    echo $result;
}

function insurance_calculations()
{
    $post = $this->input->post();
    $date = '';
    if(!empty($post['FundingDate']))
    {
        $date = strtotime("+".$post['CalculateDays']." days", strtotime($post['FundingDate']));
    }
    $resultArray['FundingDateAdd'] =  date("m/d/Y", $date);

    $PolicyExpiration = strtotime($post['PolicyExpiration']);
    $FundingDateAdd = strtotime($resultArray['FundingDateAdd']);
    $FirstPaymentDate = strtotime($post['FirstPaymentDate']);

    if($post['PolicyExpiration'] != '')
    {
        if($PolicyExpiration > $FundingDateAdd)
        {
               // $resultArray['InsuranceFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';
            $resultArray['InsuranceNextDueDate'] = date('m/d/Y',$PolicyExpiration);
        }
        else
        {
            if($post['RenewalPolicy'] == 'YES')
            {

                    //$resultArray['InsuranceFinalOutPut'] = 'Collect $'.$post['EscrowTaxDueDate1Amount'].' in line 904 in 2015 itemization screen';
                if($post['FirstPaymentDate'] != '')
                {
                    $resultArray['InsuranceNextDueDate'] = date('m/d/Y',strtotime(date("Y-m-d", $FirstPaymentDate) . " +1 year -1 month last day of this month"));

                }
            }
            else
            {
                    //$resultArray['InsuranceFinalOutPut'] = 'Escrow it in Aggregate Escrow Account Screen';

                if ($FirstPaymentDate == '') {
                    $resultArray['InsuranceNextDueDate'] = date('m/d/Y',$PolicyExpiration);
                }
                else if ($FirstPaymentDate > $PolicyExpiration) 
                {
                    $resultArray['InsuranceNextDueDate'] = date('m/d/Y',$FirstPaymentDate);
                }
                else
                {
                    $resultArray['InsuranceNextDueDate'] = date('m/d/Y',$PolicyExpiration);
                }
            }
        }
    }

    echo json_encode($resultArray);
}
    /**
     * Funtion to get Bot response image
     * @param OrderUID
     *
     * @author Santhiya M <santhiya.m@avanzegroup.com>
     * @since July 24 2020
     *
     * @return Image html
     * 
    **/
    function GetBotResponse(){
        $OrderUID = $this->input->post('OrderUID');
        $BotResponse = $this->Common_Model->GetBotFile($OrderUID);
        if($BotResponse){
            $MessageBody = $BotResponse->DocumentURL;           
            $res = array('status' => 1, 'html' => $MessageBody);
        } else {
            $res = array('status' => 0);
        }

        echo json_encode($res);
    }
    /**
    * Function Export users list as excel file
    *
    * @param 
    *
    * @throws no exception
    * @author Santhiya M <santhiya.m@avanzegroup.com>
    * @return 
    * @since Augest 12th 2020
    *
    */
    function ExcelDownload(){
        set_include_path( get_include_path().PATH_SEPARATOR."..");
        require_once APPPATH."third_party/xlsxwriter.class.php";
        $post['advancedsearch'] = array('WorkflowModuleUID' => $this->input->post('WorkflowModuleUID'),
            'Category' => $this->input->post('Category'),
        );
        // $post['length'] = $this->input->post('formData')['length'];
        // $post['start'] = $this->input->post('formData')['start'];
        $search = $this->input->post('search');
        // $post['search_value'] = trim($search);
        // $post['order'] = $this->input->post('formData')['order'];
        // $post['draw'] = $this->input->post('formData')['draw'];
        $Module = $this->input->post('segment');
       // echo '<pre>';print_r($post);exit;
        if($Module == 'User') {
            $HEADER = array('User Name' => 'string',
                'Login ID' => 'string',
                'Contact No' => 'string',
                'Role' => 'string',
                'Active' => 'string');
            $datas = $this->User_Model->GetUsersDetails();
        }else if($Module == 'Documenttype') {
            $HEADER = array('Name' => 'string',
                'Category' => 'string',
                'Client' => 'string',
                'ScreenCode' => 'string',
                'LoanType' => 'string',
                'Active' => 'string');
            $datas = $this->Documenttype_model->getDocumentTypeChecklist($post);
        }
        // else if($Module == 'HoiCompanies'){

        // } 
        $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');
        $writer = new XLSXWriter();
        
        $writer->writeSheetHeader($Module,$HEADER, $header_style);
        
        foreach($datas as $data) {
            $active =  ($data->Active == 1 ) ? 'Active' : 'InActive';

            if($Module == 'User'){
                $mort = array($data->UserName, 
                    $data->LoginID,
                    $data->ContactNo,
                    $data->RoleName,
                    $active);
            }else if($Module == 'Documenttype'){
                $mort = array(trim($data->DocumentTypeName), 
                    $data->CategoryName,
                    $data->CustomerName,
                    $data->ScreenCode,
                    $data->Groups,
                    $active);
            }

            $writer->writeSheetRow($Module, array_values($mort));
        }
        $filename = $Module.'.xlsx';

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

    /**
    * Function to Export Selective queues in the workflow
    *
    * @param 
    *
    * @throws no exception
    * @author Santhiya M <santhiya.m@avanzegroup.com>
    * @return 
    * @since Augest 14th 2020 Modified at Augest 21 22 2020
    */

    function WriteSelectiveGlobalExcelSheet(){
       set_include_path( get_include_path().PATH_SEPARATOR."..");
       require_once APPPATH."third_party/xlsxwriter.class.php";

       $controller = $this->input->get('WorkflowModuleName');
       $activesubqueue;
       $QueueUID;
       $activesubqueue = $this->input->get('activesubqueue');
       $QueueUID = $this->input->get('QueueUID');
       $HOILoans = false;
       $SelectedQueues = $this->input->get('queueName');

       $writer = new XLSXWriter();
       $controller_array = $this->config->item('controllerarray');

       $NewOrders = [];
       $ExpiredOrders=[];
       $DocsCheckedConditionsPendingOrders=[];
       $QueueclearedbyFundingOrders=[];
       $PendingDocsReleaseOrders =[];
       $PendingfromUWOrders =[];
       $AssignedOrders = [];
       $MyOrders = [];
       $ParkingOrders = [];
       $KickBackOrders = [];
       $ReWorkOrders = [];
       $HOIReworkOrders = [];
       $ThreeAConfirmationOrders = [];
       $ReWorkPendingOrders = [];
       $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#000');

       $post = ['length'=>'','advancedsearch'=>[]];

       $ExcelHeader = array('Order No'=>'string','Client'=>'string','Loan No.'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','State'=>'string','Aging'=>'string','OrderDueDateTime'=>'string','LastModified Date Time'=>'string', 'SubQueue' => 'string');

       if(isset($controller_array[$controller]) && !empty($controller_array[$controller]))
       {   
        $value = $controller_array[$controller];
      
        $WorkflowModuleUID = $this->input->get('WorkflowModuleUID');
        $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID); 
        if (!empty($QueueColumns)) {
            $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns);
            $post['column_order'] = $columndetails;
            $post['IsDynamicColumn'] = true;
        }
        if (array_key_exists('NewOrders', $value) && in_array('New Orders',  $SelectedQueues)) {
            // $Queue = $this->Common_Model->GetQueueUID('New Orders');
            $NewOrders = $this->{$value['Model']}->{$value['NewOrders']}($post,'');
            $IsNewOrders = 'yes';
        }
       
            if (array_key_exists('ExpiredOrders', $value) && in_array('Expiry Orders',  $SelectedQueues)) {
                $ExpiredOrders = $this->{$value['Model']}->{$value['ExpiredOrders']}($post,'');
                $IsExpiredOrders = 'yes';
            }
            //Docschecked Orders
            if (array_key_exists('DocsCheckedConditionsPendingOrders', $value) && in_array('Docs Checked Conditions Pending',  $SelectedQueues)) {
                $DocsCheckedConditionsPendingOrders = $this->{$value['Model']}->{$value['DocsCheckedConditionsPendingOrders']}($post,'');
                $IsDocsCheckedConditionsPendingOrders = 'yes';
            }
            //Queuechecked Orders
            if (array_key_exists('QueueclearedbyFundingOrders', $value) && in_array('Queue cleared by Funding',  $SelectedQueues)) {
                $QueueclearedbyFundingOrders = $this->{$value['Model']}->{$value['QueueclearedbyFundingOrders']}($post,'');
                $IsQueueclearedbyFundingOrders = 'yes';
            }
            //Pendingdocs Orders
            if (array_key_exists('PendingDocsReleaseOrders', $value) && in_array('Pending Docs Release', $SelectedQueues)) {
                $PendingDocsReleaseOrders = $this->{$value['Model']}->{$value['PendingDocsReleaseOrders']}($post,'');
                $IsPendingDocsReleaseOrders = 'yes';
            }
            //Pendinguw Ordersummary
            if (array_key_exists('PendingfromUWOrders', $value) && in_array('Pending from UW', $SelectedQueues)) {
                $PendingfromUWOrders = $this->{$value['Model']}->{$value['PendingfromUWOrders']}($post,'');
                $IsPendingfromUWOrders = 'yes';
            }
            //Submitted for Doc Check
            if (array_key_exists('SubmittedforDocCheckOrders', $value) && in_array('SubmittedforDocCheck', $SelectedQueues)) {
                $SubmittedforDocCheckOrders = $this->{$value['Model']}->{$value['SubmittedforDocCheckOrders']}($post,'');
                $IsSubmittedforDocCheckOrders = 'yes';
            }
            //Submitted for Doc Check
            if (array_key_exists('NonWorkableOrders', $value) && in_array('NonWorkable', $SelectedQueues)) {
                $NonWorkableOrders = $this->{$value['Model']}->{$value['NonWorkableOrders']}($post,'');
                $IsNonWorkableOrders = 'yes';
            }
    }
    
        //Workup Rework
        if (array_key_exists('WorkupReworkOrders', $value) && in_array('WorkupRework', $SelectedQueues)) {
            $WorkupReworkOrders = $this->{$value['Model']}->{$value['WorkupReworkOrders']}($post,'');
            $IsWorkupReworkOrders = 'yes';
        }

             //KickBackOrders Orders
        if (array_key_exists('KickBackOrders', $value) && in_array('KickBack Orders',  $SelectedQueues)) {
            $KickBackOrders = $this->{$value['Model']}->{$value['KickBackOrders']}($post,'');
            $IsKickBackOrders = 'yes';
            $IsKickBack = $this->Common_Model->is_kickback_enabledforworkflow($value['WorkflowUID']);
        }
         //ReWork Orders
        if (array_key_exists('ReWorkOrders', $value) && in_array('Re-Work',  $SelectedQueues)) {
            $ReWorkOrders = $this->{$value['Model']}->{$value['ReWorkOrders']}($post,'');
            $IsReWorkOrders = 'yes';
        }
         //ReWork Orders
        if (array_key_exists('HOIReworkOrders', $value) && in_array('HOIReworkOrders',  $SelectedQueues)) {
            $HOIReworkOrders = $this->{$value['Model']}->{$value['HOIReworkOrders']}($post,'');
            $IsHOIReworkOrders = 'yes';
        }

        //3A Confirmation Orders
        if (array_key_exists('ThreeAConfirmationOrders', $value) && in_array('ThreeAConfirmation',  $SelectedQueues)) {
            $ThreeAConfirmationOrders = $this->{$value['Model']}->{$value['ThreeAConfirmationOrders']}($post,'');
            $IsThreeAConfirmationOrders = 'yes';
        }

         //ReWork Pending Orders
        if (array_key_exists('ReWorkPendingOrders', $value) && in_array('Re-WorkPending',  $SelectedQueues)) {
            $ReWorkPendingOrders = $this->{$value['Model']}->{$value['ReWorkPendingOrders']}($post,'');
            $IsReWorkPendingOrders = 'yes';
        }
            //VANewOrders Orders
        if (array_key_exists('VANewOrders', $value) && in_array('VA New Orders', $SelectedQueues)) {
            $VANewOrders = $this->{$value['Model']}->{$value['VANewOrders']}($post,'');
            $IsVANewOrders = 'yes';
        }   

            //AssignedOrders Orders
        if (array_key_exists('AssignedOrders', $value) && in_array('Assigned Orders',  $SelectedQueues)) {
            $AssignedOrders = $this->{$value['Model']}->{$value['AssignedOrders']}($post,'');
            // echo "<pre>";print_r($AssignedOrders);exit;
            $IsAssignedOrders = 'yes';
        }
            //MyOrders Orders
        if (array_key_exists('MyOrders', $value) && in_array('My Orders',  $SelectedQueues)) {                
            $MyOrders = $this->{$value['Model']}->{$value['MyOrders']}($post,'');
            $IsMyOrders = 'yes';
        }
            //parkingorders Orders
        if (array_key_exists('parkingorders', $value) && in_array('Parking Orders',  $SelectedQueues)) {
            $ParkingOrders = $this->{$value['Model']}->{$value['parkingorders']}($post,'');
            $Isparkingorders = 'yes';
            $IsParking = $this->Common_Model->is_parking_enabledforworkflow($value['WorkflowUID']);
        }
        //FHA New Orders
        if (array_key_exists('FHAneworders', $value) && in_array('FHA New Orders',  $SelectedQueues)) {
            $FHAneworders = $this->{$value['Model']}->{$value['FHAneworders']}($post,'');
            $IsFHAneworders = 'yes';
        }
                
        $IsDynamicColumnsAvailable = false;
        if (!empty($QueueColumns)) 
        {
            $ExcelSheetOrders = [];

            if (in_array('New Orders',  $SelectedQueues)) {
                $ExcelSheetOrders['NewOrders'] = $NewOrders;
            }

            if (in_array('Expiry Orders',  $SelectedQueues)) {
                $ExcelSheetOrders['ExpiredOrders'] = $ExpiredOrders;
            }

            if(isset($IsDocsCheckedConditionsPendingOrders)){
                $ExcelSheetOrders['DocsCheckedConditionsPendingOrders'] = $DocsCheckedConditionsPendingOrders;
            }
             if(isset($IsPendingDocsReleaseOrders)){
                $ExcelSheetOrders['PendingDocsReleaseOrders'] = $PendingDocsReleaseOrders;
            }
            if(isset($IsPendingfromUWOrders)){
                $ExcelSheetOrders['PendingfromUWOrders'] = $PendingfromUWOrders;
            }
            if(isset($IsSubmittedforDocCheckOrders)){
                $ExcelSheetOrders['SubmittedforDocCheckOrders'] = $SubmittedforDocCheckOrders;
            }
            if(isset($IsNonWorkableOrders)){
                $ExcelSheetOrders['NonWorkableOrders'] = $NonWorkableOrders;
            }
            if(isset($IsWorkupReworkOrders)){
                $ExcelSheetOrders['WorkupReworkOrders'] = $WorkupReworkOrders;
            }
            if(isset($IsQueueclearedbyFundingOrders)){
                $ExcelSheetOrders['QueueclearedbyFundingOrders'] = $QueueclearedbyFundingOrders;
            }
            if(isset($IsReWorkOrders)){
                $ExcelSheetOrders['ReWorkOrders'] = $ReWorkOrders;
            }
            if(isset($IsHOIReworkOrders)){
                $ExcelSheetOrders['HOIReworkOrders'] = $HOIReworkOrders;
            }
            if(isset($IsThreeAConfirmationOrders)){
                $ExcelSheetOrders['ThreeAConfirmationOrders'] = $ThreeAConfirmationOrders;
            }
            if(isset($ReWorkPendingOrders)){
                $ExcelSheetOrders['ReWorkPendingOrders'] = $ReWorkPendingOrders;
            }
                if (isset($IsKickBackOrders)) 
                {
                    $ExcelSheetOrders['KickBackOrders'] = $KickBackOrders;
                }
                if (isset($IsVANewOrders)) 
                {
                    $ExcelSheetOrders['VANewOrders'] = $VANewOrders;
                }
                if(isset($IsFHAneworders)){
                    $ExcelSheetOrders['FHAneworders'] = $FHAneworders;                    
                }

                if (in_array('Assigned Orders',  $SelectedQueues)) {

                    $ExcelSheetOrders['AssignedOrders'] = $AssignedOrders;
                }

                if (in_array('My Orders',  $SelectedQueues)) {
                    
                    $ExcelSheetOrders['MyOrders'] = $MyOrders;
                }
                // echo "<pre>";print_r($ExcelSheetOrders);exit;
                 $i = 1;
                foreach ($ExcelSheetOrders as $skey => $ordersheet) 
                {                    
                    /* ###### New Orders Sheet ###### */
                    $Mischallenous = array();
                    $Mischallenous['SubQueueName'] = $skey;
                    $Mischallenous['PageBaseLink'] = "";
                    $Mischallenous['AssignButtonClass'] = "";
                    $Mischallenous['QueueColumns'] = $QueueColumns;

                    if($skey == 'parkingorders') {
                        $Mischallenous['IsParkingQueue'] = true;
                    }

                    if($skey == 'ThreeAConfirmationOrders') {
                        $Mischallenous['IsThreeAConfirmationQueue'] = true;
                    }

                    if($skey == 'KickBackOrders') {
                        $Mischallenous['IsKickBackQueue'] = true;
                    }
                    if($skey == 'CompletedOrders') {
                        $Mischallenous['IsCompleted'] = true;
                    }
                    if($skey == 'HOIReworkOrders') {
                        $Mischallenous['IsHOIRework'] = true;
                    }

                    // Subqueue Section                    
                    if($skey == 'NewOrders') {

                        $Mischallenous['SubQueueSection'] = 'tblNewOrders';

                    } elseif($skey == 'ExpiredOrders') {

                        $Mischallenous['IsExpiryOrdersQueue'] = TRUE;
                        $Mischallenous['SubQueueSection'] = 'Expiredorderstable';

                    } elseif($skey == 'DocsCheckedConditionsPendingOrders') {

                        $Mischallenous['SubQueueSection'] = 'DocsCheckedorderstable';

                    } elseif($skey == 'QueueclearedbyFundingOrders') {

                        $Mischallenous['SubQueueSection'] = 'QueueClearedorderstable';

                    } elseif($skey == 'PendingDocsReleaseOrders') {

                        $Mischallenous['SubQueueSection'] = 'PendingDocsorderstable';

                    } elseif($skey == 'PendingfromUWOrders') {

                        $Mischallenous['SubQueueSection'] = 'PendiingUWorderstable';

                    } elseif($skey == 'SubmittedforDocCheckOrders') {

                        $Mischallenous['SubQueueSection'] = 'SubmittedforDocCheckOrdersTable';

                    } elseif($skey == 'NonWorkableOrders') {

                        $Mischallenous['SubQueueSection'] = 'NonWorkableOrdersTable';

                    } elseif($skey == 'WorkupReworkOrders') {

                        $Mischallenous['SubQueueSection'] = 'WorkupReworkOrdersTable';

                    } elseif($skey == 'KickBackOrders') {

                        $Mischallenous['SubQueueSection'] = 'KickBackorderstable';

                    } elseif($skey == 'ReWorkOrders') {

                        $Mischallenous['SubQueueSection'] = 'ReWorkOrdersTable';

                    } elseif($skey == 'HOIReworkOrders') {

                        $Mischallenous['SubQueueSection'] = 'HOIReworkOrdersTable';

                    } elseif($skey == 'ThreeAConfirmationOrders') {

                        $Mischallenous['SubQueueSection'] = 'ThreeAConfirmationOrdersTable';

                    } elseif($skey == 'ReWorkPendingOrders') {

                        $Mischallenous['SubQueueSection'] = 'ReWorkPendingOrdersTable';

                    } elseif($skey == 'AssignedOrders') {

                        $Mischallenous['SubQueueSection'] = 'workingprogresstable';

                    } elseif($skey == 'MyOrders') {

                        $Mischallenous['SubQueueSection'] = 'myorderstable';

                    } elseif($skey == 'hoiwaitingorders') {

                        $Mischallenous['SubQueueSection'] = 'hoiwaitingorderstable';

                    } elseif($skey == 'hoiresponsedorders') {

                        $Mischallenous['SubQueueSection'] = 'hoiresponsedorderstable';

                    } elseif($skey == 'hoireceivedorders') {

                        $Mischallenous['SubQueueSection'] = 'hoireceivedorderstable';

                    } elseif($skey == 'hoiexceptionorders') {

                        $Mischallenous['SubQueueSection'] = 'hoiexceptionorderstable';

                    } elseif($skey == 'parkingorders') {
                        $Mischallenous['SubQueueSection'] = 'parkingorderstable';
                    }

                    $QueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($ordersheet, $WorkflowModuleUID, $Mischallenous);
                    if ( !empty($QueueColumnsData) ) 
                    {
                        $header = $QueueColumnsData['header'];
                        $data = $QueueColumnsData['orderslist'];

                        $HEADER = [];
                        foreach ($header as $hkey => $head) {

                            $HEADER[$head] = "string";
                        }

                          $writer->writeSheetHeader($controller, $HEADER, $header_style); 
                          $i++;

                        foreach($data as $Order) {
                            $writer->writeSheetRow($controller, $Order);
                        }
                    }                    
                } 
                $IsDynamicColumnsAvailable = true;
                $NewOrders = [];
                $ExpiredOrders =[];
                $DocsCheckedConditionsPendingOrders =[];
                $QueueclearedbyFundingOrders =[];
                $PendingDocsReleaseOrders =[];
                $PendingfromUWOrders =[];
                $SubmittedforDocCheckOrders =[];
                $NonWorkableOrders =[];
                $WorkupReworkOrders =[];
                $AssignedOrders = [];
                $MyOrders = [];
                $KickBackOrders = [];
                $FHAneworders = [];
                $ReWorkOrders = [];
                $HOIReworkOrders = [];
                $ThreeAConfirmationOrders = [];
                $ReWorkPendingOrders = [];
               
            }
            /* ****** Dynamic Queues Section Ends ****** */
            
            /*------ skipped when top if succeded --------*/
            if ($IsDynamicColumnsAvailable == false) 
            {
                
                    $writer->writeSheetHeader($controller, $ExcelHeader, $header_style);
                                                    
            } else {


                foreach($NewOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'New Orders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                }
                if(!empty($ExpiredOrders)){
                    foreach($ExpiredOrders as $Order) {
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'Expired Orders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }

                }

               
                if(!empty($DocsCheckedConditionsPendingOrders)){
                    foreach($DocsCheckedConditionsPendingOrders as $Order) {
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'DocsCheckedConditionsPendingOrders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }
                }
                if(!empty($QueueclearedbyFundingOrders)){
                    foreach($QueueclearedbyFundingOrders as $Order) {
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'QueueclearedbyFundingOrders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }
                }
                if(!empty($PendingDocsReleaseOrders)){                
                    foreach($PendingDocsReleaseOrders as $Order) {
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'PendingDocsReleaseOrders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }
                } 
                

                foreach($PendingfromUWOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'PendingfromUWOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                } 
                

                foreach($SubmittedforDocCheckOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'SubmittedforDocCheckOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                } 

                foreach($NonWorkableOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'NonWorkableOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                } 

                foreach($WorkupReworkOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'WorkupReworkOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                } 

                foreach($AssignedOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->AssignedUserName,site_datetimeformat($Order->AssignedDateTime),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'AssignedOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                }

                foreach($MyOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'MyOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                }

                if($WorkflowModuleUID == $this->config->item('Workflows')['HOI']) {                   

                    if(!empty($IsKickBack)) {
                        foreach($KickBackOrders as $Order){
                            $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'KickBackOrders');
                            $writer->writeSheetRow($controller, array_values($Exceldataset));
                        }
                    }
                }
                 if(!empty($FHAneworders)) {
                        foreach($FHAneworders as $Order){
                            $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'FHAneworders');
                            $writer->writeSheetRow($controller, array_values($Exceldataset));
                        }
                }
                 if(!empty($ReWorkOrders)) {
                    foreach($ReWorkOrders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'ReWorkOrders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }
                }
                 if(!empty($HOIReworkOrders)) {
                    foreach($HOIReworkOrders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'HOIReworkOrders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }
                }
                 if(!empty($ThreeAConfirmationOrders)) {
                    foreach($ThreeAConfirmationOrders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'ThreeAConfirmationOrders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }
                }
                 if(!empty($ReWorkPendingOrders)) {
                    foreach($ReWorkPendingOrders as $Order){
                        $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'ReWorkPendingOrders');
                        $writer->writeSheetRow($controller, array_values($Exceldataset));
                    }
                }
            }

//            Funding Conditions Exception Queues
            $FundingConditionsQueueUID = array();
            $FundingConditionsQueueUID[] = $this->config->item('FundingConditionsSubQueueIDs')['ProcessorAttention'];
            $FundingConditionsQueueUID[] = $this->config->item('FundingConditionsSubQueueIDs')['TitleNotaryAttention'];
            if (!empty($QueueUID)) {
                    if (isset($QueueUID) && !empty($QueueUID)) {
                        // "Funding Conditions" Sub queue Processor attention and Title/Notary Attention should be part of Scheduling Queue
                        if (in_array($QueueUID, $FundingConditionsQueueUID) && $value['WorkflowUID'] == $this->config->item('Workflows')['Scheduling']) {
                            $Queues = $this->Common_Model->getCustomerWorkflowQueues($this->config->item('Workflows')['FundingConditions'], $QueueUID); 
                        } else {
                            $Queues = $this->Common_Model->getCustomerWorkflowQueues($value['WorkflowUID'], $QueueUID); 
                        }
                    }
                } else {
                    if ($value['WorkflowUID'] == $this->config->item('Workflows')['Scheduling']) {
                        $SchedulingQueues = $this->Common_Model->getCustomerWorkflowQueues($value['WorkflowUID']);
                        // "Funding Conditions" Sub queue Processor attention and Title/Notary Attention should be part of Scheduling Queue
                        $FundingConditionsQueues = $this->Common_Model->getCustomerWorkflowQueues($this->config->item('Workflows')['FundingConditions'], $FundingConditionsQueueUID);
                        $Queues = array_merge( $SchedulingQueues, $FundingConditionsQueues );
                    } else {
                        $Queues = $this->Common_Model->getCustomerWorkflowQueues($value['WorkflowUID']);
                    }
                }
            
            // echo '<pre>';print_r($Queues);exit;
        foreach ($Queues as $key => $queue) {
            if(in_array($queue->QueueName, $SelectedQueues)){
                $post['QueueUID'] = $queue->QueueUID;
                $QueueUID = $queue->QueueUID; 
                

                $exceptionqueueorder = $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders($post, "data");

                if ($IsDynamicColumnsAvailable == true) 
                {                    
                    $Mischallenous = array();
                    $Mischallenous['SubQueueName'] = $queue->QueueName;
                    $Mischallenous['PageBaseLink'] = "";
                    $Mischallenous['AssignButtonClass'] = "";
                    $Mischallenous['QueueColumns'] = $QueueColumns;
                    $Mischallenous['SubQueueDetails'] = $queue;

                    $ExceptionQueueColumnsData = $this->Common_Model->getSelectiveExceptionExcelDynamicQueueColumns($exceptionqueueorder, $WorkflowModuleUID, $Mischallenous);

                    if ( !empty($ExceptionQueueColumnsData) ) 
                    {
                        $header = $ExceptionQueueColumnsData['header'];
                        $data = $ExceptionQueueColumnsData['orderslist'];
                        
                        $HEADER = [];
                        foreach ($header as $hkey => $head) {
                            $HEADER[$head] = "string";
                        }
                        $HEADER['SubQueue'] = "string";
                        $writer->writeSheetHeader($controller,$HEADER, $header_style);  

                        foreach($data as $Order) {
                            $Order[] = $queue->QueueName;
                            $writer->writeSheetRow($controller, $Order);
                        }
                    }
                }
                else
                {
                    $HEADER = array('Order No'=>'string','Client'=>'string','Loan No'=>'string','Loan Type'=>'string','Milestone'=>'string','Current Status'=>'string','Aging'=>'string','Due DateTime'=>'string','LastModified DateTime'=>'string','Raised By'=>'string','Remarks'=>'string','Raised DateTime'=>'string', 'Sub Queue');

                    $writer->writeSheetHeader($controller,$HEADER, $header_style);
                    foreach($exceptionqueueorder as $Order) {
                        $Exceldataset_ExceptionQueue = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime),$Order->RaisedBy,$Order->RaisedRemarks, site_datetimeformat($Order->RaisedDateTime), $queue->QueueName);
                        $writer->writeSheetRow($controller, array_values($Exceldataset_ExceptionQueue));
                    }
                }
            }
        }

        if(!empty($IsParking)) 
        {
            if ($IsDynamicColumnsAvailable == true) 
            {
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = 'Parking Orders';
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['IsParkingQueue'] = true;
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'parkingorderstable';
                $ParkingQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($ParkingOrders, $WorkflowModuleUID, $Mischallenous);
                if ( !empty($ParkingQueueColumnsData) ) 
                {
                    $header = $ParkingQueueColumnsData['header'];
                    $data = $ParkingQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }
                    $writer->writeSheetHeader($controller,$HEADER, $header_style);
                    foreach($data as $Order) {                     
                        $writer->writeSheetRow($controller, $Order);
                    }
                }
            }
            else
            {
              $writer->writeSheetHeader($controller,$ExcelHeader3, $header_style);
              foreach($ParkingOrders as $Order) {
               $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'ParkingOrders');
               $writer->writeSheetRow($controller, array_values($Exceldataset));   
              }
            }
        }


         if(!empty($IsKickBack)) 
         {
            if ($IsDynamicColumnsAvailable == true) 
            {
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = 'Kickback Orders';
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['IsKickBackQueue'] = true;
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'KickBackorderstable';

                $KickBackQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($KickBackOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($KickBackQueueColumnsData) ) 
                {
                    $header = $KickBackQueueColumnsData['header'];
                    $data = $KickBackQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }

                    $writer->writeSheetHeader($controller,$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow($controller, $Order); //KickBackOrders
                    }


                }
            } else {

                /* ****** Normal Kickback Queues renders ****** */
                $writer->writeSheetHeader($controller,$ExcelHeader, $header_style);
                foreach($KickBackOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime), 'KickBackOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));   
                }
            }

        }
        if(!empty($ReWorkOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = 'Re-Work Orders';
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'ReWorkOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($ReWorkOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader($controller,$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow($controller, $Order);
                    }


                }
            } else {

                /* ****** Normal ReWork Queues renders ****** */
                $writer->writeSheetHeader($controller,$ExcelHeader, $header_style);
                foreach($ReWorkOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime),'ReWorkOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));   
                }
            }

        }
        if(!empty($HOIReworkOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = 'HOIReworkOrders Orders';
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['IsHOIRework'] = true;
                $Mischallenous['SubQueueSection'] = 'HOIReworkOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($HOIReworkOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader($controller,$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow($controller, $Order);
                    }


                }
            } else {

                /* ****** Normal ReWork Queues renders ****** */
                $writer->writeSheetHeader($controller,$ExcelHeader, $header_style);
                foreach($HOIReworkOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime),'HOIReworkOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));   
                }
            }

        }
        if(!empty($ThreeAConfirmationOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = '3A Confirmation Orders';
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['IsThreeAConfirmationQueue'] = true;
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'ThreeAConfirmationOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($ThreeAConfirmationOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader($controller,$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow($controller, $Order);
                    }


                }
            } else {

                /* ****** Normal 3A Confirmation Queues renders ****** */
                $writer->writeSheetHeader($controller,$ExcelHeader, $header_style);
                foreach($ThreeAConfirmationOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime),'ThreeAConfirmationOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));   
                }
            }

        }
        if(!empty($ReWorkPendingOrders)) 
         {
            /* ****** Dynamic Exception Queues Section Starts ****** */

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Exception Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = 'Re-Work Orders';
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['SubQueueSection'] = 'ReWorkPendingOrdersTable';

                $ReWorkQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($ReWorkPendingOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ReWorkQueueColumnsData) ) 
                {
                    $header = $ReWorkQueueColumnsData['header'];
                    $data = $ReWorkQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }


                    $writer->writeSheetHeader($controller,$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow($controller, $Order);
                    }


                }
            } else {

                /* ****** Normal ReWork Queues renders ****** */
                $writer->writeSheetHeader($controller,$ExcelHeader, $header_style);
                foreach($ReWorkPendingOrders as $Order) {
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,$Order->RaisedBy,$Order->Remarks,site_datetimeformat($Order->Remainder),site_datetimeaging($Order->EntryDatetime),site_datetimeformat($Order->DueDateTime),site_datetimeformat($Order->LastModifiedDateTime),'ReWorkPendingOrders');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));   
                }
            }

        }
        if (in_array('CompletedOrders', $value) && in_array('Completed Orders', $SelectedQueues)) {           
            $WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CompletedOrders = [];

            if ($IsDynamicColumnsAvailable == true) 
            {
                $post['IsDynamicColumns'] = true;
                foreach ($QueueColumns as $key => $queuecolumnsvalue) {
                    if ($queuecolumnsvalue->NoSort == 1) 
                    {
                        if (!empty($queuecolumnsvalue->SortColumnName)) 
                        {
                            $post['column_order'][] = $queuecolumnsvalue->SortColumnName;
                        }
                        else
                        {
                            $post['column_order'][] = "";
                        }
                    }
                    else
                    {
                        $post['column_order'][] = $queuecolumnsvalue->ColumnName;
                    }

                }
            }

            $CompletedOrders = $this->Common_Model->CompletedOrdersBasedOnWorkflow($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Completed Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = "Completed Orders";
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $QueueColumns;
                $Mischallenous['IsCompleted'] = true;
                $Mischallenous['SubQueueSection'] = 'completedorderstable';

                // $CompletedQueueColumnsData = $this->Common_Model->getGlobalExcelDynamicQueueColumns($CompletedOrders, $WorkflowModuleUID, $Mischallenous);
                 $CompletedQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($CompletedOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CompletedQueueColumnsData) ) 
                {
                    $header = $CompletedQueueColumnsData['header'];
                    $data = $CompletedQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }
                    
                    $writer->writeSheetHeader($controller,$HEADER, $header_style);


                    foreach($data as $Order) {
                        $writer->writeSheetRow($controller, $Order);
                    }


                }
            }
            else
            {
              
                foreach($CompletedOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime),$Order->completedby,site_datetimeformat($Order->completeddatetime), 'Completed Order');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                }
            }
        }  

        /**
        *Function CD Inflow 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Date.
        */
        if (in_array('CDInflowOrders', $value) && in_array('CD Inflow', $SelectedQueues)) {           
            $WorkflowModuleUID = $this->config->item('workflowcontroller')['CD_Orders'];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CDInflowOrders = [];

            $CDInflowOrders = $this->{$value['Model']}->{$value['CDInflowOrders']}($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Completed Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = "CD Inflow";
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDInflow'] = true;
                $Mischallenous['SubQueueSection'] = 'CDInflowOrdersTable';

                $CDInflowQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($CDInflowOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CDInflowQueueColumnsData) ) 
                {
                    $header = $CDInflowQueueColumnsData['header'];
                    $data = $CDInflowQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }
                    
                    $writer->writeSheetHeader('CD Orders',$HEADER, $header_style);

                    foreach($data as $Order) {
                        $writer->writeSheetRow('CD Orders', $Order);
                    }            
                }
            }
            else
            {

                foreach($CDInflowOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime),$Order->completedby,site_datetimeformat($Order->completeddatetime), 'CD Inflow');
                    $writer->writeSheetRow('CD Orders', array_values($Exceldataset));
                }
            }
        }

        /**
        *Function CD Pending 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Date.
        */
        if (in_array('CDPendingOrders', $value) && in_array('CD Pending', $SelectedQueues)) {           
            $WorkflowModuleUID = $this->config->item('workflowcontroller')['CD_Orders'];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CDPendingOrders = [];

            $CDPendingOrders = $this->{$value['Model']}->{$value['CDPendingOrders']}($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Completed Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = "CD Pending";
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDPending'] = true;
                $Mischallenous['SubQueueSection'] = 'CDPendingOrdersTable';

                $CDPendingQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($CDPendingOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CDPendingQueueColumnsData) ) 
                {
                    $header = $CDPendingQueueColumnsData['header'];
                    $data = $CDPendingQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }
                    
                    $writer->writeSheetHeader('CD Orders',$HEADER, $header_style);

                    foreach($data as $Order) {
                        $writer->writeSheetRow('CD Orders', $Order);
                    }            
                }
            }
            else
            {

                foreach($CDPendingOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime),$Order->completedby,site_datetimeformat($Order->completeddatetime), 'CD Pending');
                    $writer->writeSheetRow('CD Orders', array_values($Exceldataset));
                }
            }
        }

        /**
        *Function CD Completed 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Date.
        */
        if (in_array('CDCompletedOrders', $value) && in_array('CD Completed', $SelectedQueues)) {           
            $WorkflowModuleUID = $this->config->item('workflowcontroller')['CD_Orders'];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $CDCompletedOrders = [];

            // $CDCompletedOrders = $this->{$value['Model']}->{$value['CDCompletedOrders']}($post,'');
            $CDCompletedOrders = $this->Common_Model->CompletedOrdersBasedOnWorkflow($post,'');

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Completed Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = "CD Completed";
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDCompleted'] = true;
                $Mischallenous['IsCompleted'] = true;
                $Mischallenous['SubQueueSection'] = 'CDCompletedOrdersTable';

                $CDCompletedQueueColumnsData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($CDCompletedOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($CDCompletedQueueColumnsData) ) 
                {
                    $header = $CDCompletedQueueColumnsData['header'];
                    $data = $CDCompletedQueueColumnsData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }
                    
                    $writer->writeSheetHeader('CD Orders',$HEADER, $header_style);

                    foreach($data as $Order) {
                        $writer->writeSheetRow('CD Orders', $Order);
                    }            
                }
            }
            else
            {

                foreach($CDCompletedOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime),$Order->completedby,site_datetimeformat($Order->completeddatetime), 'CD Completed');
                    $writer->writeSheetRow('CD Orders', array_values($Exceldataset));
                }
            }
        }

        /**
        *Function Expiry Orders Complete 
        *@author SathishKumar <sathish.kumar@avanzegroup.com>
        *@since Monday 12 October 2020.
        */
        if (in_array('ExpiredCompleteOrders', $value) && in_array('Expiry Complete Orders', $SelectedQueues)) {           
            $WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
            $CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;
            $ExpiredCompleteOrders = [];

            $ExpiredCompleteOrders = $this->Common_Model->ExpiredCompleteOrders($post);

            if ($IsDynamicColumnsAvailable == true) 
            {

                /* ###### Dynamic Completed Columns Excel Sheet ###### */
                $Mischallenous = array();
                $Mischallenous['SubQueueName'] = "Expiry Orders Complete";
                $Mischallenous['PageBaseLink'] = "";
                $Mischallenous['AssignButtonClass'] = "";
                $Mischallenous['QueueColumns'] = $CD_QueueColumns;
                $Mischallenous['IsCDInflow'] = true;
                $Mischallenous['SubQueueSection'] = 'ExpiredCompleteOrdersTable';

                $ExpiryCompletedOrdersData = $this->Common_Model->getSelectiveExcelDynamicQueueColumns($ExpiredCompleteOrders, $WorkflowModuleUID, $Mischallenous);

                if ( !empty($ExpiryCompletedOrdersData) ) 
                {
                    $header = $ExpiryCompletedOrdersData['header'];
                    $data = $ExpiryCompletedOrdersData['orderslist'];

                    $HEADER = [];
                    foreach ($header as $hkey => $head) {
                        $HEADER[$head] = "string";
                    }
                    
                    $writer->writeSheetHeader($controller,$HEADER, $header_style);

                    foreach($data as $Order) {
                        $writer->writeSheetRow($controller, $Order);
                    }            
                }
            }
            else
            {

                foreach($ExpiredCompleteOrders as $Order){
                    $Exceldataset = array($Order->OrderNumber,$Order->CustomerName,$Order->LoanNumber,$Order->LoanType,$Order->MilestoneName,$Order->StatusName,$Order->PropertyStateCode,site_datetimeformat($Order->LastModifiedDateTime),$Order->completedby,site_datetimeformat($Order->completeddatetime), 'Expiry Orders Complete');
                    $writer->writeSheetRow($controller, array_values($Exceldataset));
                }
            }
        }

    $filename = $controller.'.xlsx';

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

    /**
    *Function Get workflow sub queue details 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Thursday 27 August 2020.
    */
    public function FetchWorkflowSubQueues()
    {
        $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

        $WorkflowQueueDetails = $this->Common_Model->FetchWorkflowSubQueues($WorkflowModuleUID);

        echo json_encode($WorkflowQueueDetails);
    }

    /**
    *Funtion Scheduling Queue 2G, 3A (Pending Email & Checkbox Checkbox not checked) and total (2G,3A 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Saturday 05 September 2020.
    */
    function GetMilestoneWidgetCounts() {
        
        // Define variables    
        $FilterCounts = [];
        $MilestoneWidgetCounts = [];

        // Post value
        $ModuleController = $this->input->post('ModuleController');
        $filterlist = $this->input->post('filterlist');
        $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

        // Get Workflow Funtions and Model details
        $WorkflowController = $this->config->item('controllerarray')[$ModuleController];

        if ($filterlist == 'ThreeAConfirmationOrdersList') {
            $FilterCounts['ModelName'] = $WorkflowController['Model'];
            $FilterCounts['FunctionName'] = 'ThreeAConfirmationcount_all';
        }

        if (!empty($FilterCounts)) {
            
            // set post value
            $post['advancedsearch']['MilestoneWidgetPendingFilter'] = 'true';                
            $post['advancedsearch']['WorkflowModuleUID'] = $WorkflowModuleUID;

            // get milestone uid
            $MilestoneWidgetEnabledWorkflows = $this->config->item('MilestoneWidgetEnabledWorkflows')[$WorkflowModuleUID];

            foreach ($MilestoneWidgetEnabledWorkflows as $MilestoneName => $MilestoneUID) {

                $post['advancedsearch']['WidgetMilestoneUID'] = $MilestoneUID;

                $MilestoneWidgetCounts['MilestoneWidgetPendingCounts_'.$MilestoneName] = $this->{$FilterCounts['ModelName']}->{$FilterCounts['FunctionName']}($post);
            }

            $post = [];
            // set post value
            $post['advancedsearch']['MilestoneWidgetTotalFilter'] = 'true';                
            $post['advancedsearch']['WorkflowModuleUID'] = $WorkflowModuleUID;
            $MilestoneWidgetCounts['MilestoneWidgetTotalCounts'] = $this->{$FilterCounts['ModelName']}->{$FilterCounts['FunctionName']}($post);

            // echo '<pre>';print_r($MilestoneWidgetCounts);exit;
        }

        echo json_encode($MilestoneWidgetCounts);   
        
    }

    /**
    *Function Update Sub Queue Category 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Wednesday 30 September 2020.
    */
    function UpdateSubQueueCategory() {
        $OrderUID = $this->input->post('OrderUID');
        $SubQueueCategoryUID = $this->input->post('SubQueueCategoryUID');
        $CategoryUID = is_array($this->input->post('CategoryUID')) ? implode(',', $this->input->post('CategoryUID')) : $this->input->post('CategoryUID');
        $StaticQueueUID = $this->input->post('StaticQueueUID');
        $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');

        if($OrderUID && $SubQueueCategoryUID && $CategoryUID) {

            $IsSubQueueuCategoryAvailable = $this->Common_Model->get_row('tSubQueueCategory', ['OrderUID'=>$OrderUID,'SubQueueCategoryUID'=>$SubQueueCategoryUID]);

            $this->db->trans_begin();

            if (empty($IsSubQueueuCategoryAvailable)) {
                $this->db->insert('tSubQueueCategory',array('OrderUID'=>$OrderUID,'SubQueueCategoryUID'=>$SubQueueCategoryUID,'CategoryUID'=>$CategoryUID, 'LastModifiedByUserUID'=>$this->loggedid, 'LastModifiedDateTime'=>date('Y-m-d H:i:s')));
            } else {
                $this->db->where(array('OrderUID'=>$OrderUID,'SubQueueCategoryUID'=>$SubQueueCategoryUID));
                $this->db->update('tSubQueueCategory', array('CategoryUID'=>$CategoryUID, 'LastModifiedByUserUID'=>$this->loggedid, 'LastModifiedDateTime'=>date('Y-m-d H:i:s')));
            }   

            if ($this->db->affected_rows() > 0) {

                $mCategories = $this->Common_Model->get_row('mCategories', ['CategoryUID'=>$CategoryUID]);

                /*INSERT ORDER LOGS BEGIN*/
                $this->Common_Model->OrderLogsHistory($OrderUID, $mCategories->CategoryName.' - category is updated', Date('Y-m-d H:i:s'));
                /*INSERT ORDER LOGS END*/    
            }            
            
            // When updating the category if follow is already initiated need to clear followup
            //clear followup
            $followup_row_available = $this->Common_Model->get('tOrderFollowUp', ['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'StaticQueueUID'=>$StaticQueueUID,'IsCleared'=>0]);

            if(!empty($followup_row_available)) {

                $Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

                $mStaticQueues = $this->Common_Model->get_row('mStaticQueues', ['StaticQueueUID'=>$StaticQueueUID]);

                foreach ($followup_row_available as $followup_row) {
                    $followpdata= [];
                    $followpdata['IsCleared'] = 1;
                    $followpdata['ClearedReasonUID'] = 3;
                    $followpdata['ClearedRemarks'] = sprintf($this->lang->line('Clear_Followup_Init'), $Workflow->WorkflowModuleName, $mStaticQueues->StaticQueueName);
                    $followpdata['ClearedByUserUID'] = $this->loggedid;
                    $followpdata['ClearedDateTime'] = date('Y-m-d H:i:s');
                    $this->db->where(['FollowUpUID'=>$followup_row->FollowUpUID]);
                    $this->db->update('tOrderFollowUp', $followpdata);
                    $this->Common_Model->OrderLogsHistory($OrderUID,$followpdata['ClearedRemarks'],Date('Y-m-d H:i:s'));
                }
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();

                $response = array('success' => 2,'message'=>'Failed to Update Category.');

            } else {
                
                $this->db->trans_commit();

                $response = array('success' => 1,'message'=>'Category Updated.');

            }

        } else {

            if (empty($CategoryUID) && !empty($OrderUID) && !empty($SubQueueCategoryUID)) {


                // When updating the category if follow is already initiated need to clear followup
                //clear followup
                $followup_row_available = $this->Common_Model->get('tOrderFollowUp', ['OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'StaticQueueUID'=>$StaticQueueUID,'IsCleared'=>0]);
                if(!empty($followup_row_available)) {

                    $Workflow = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

                    $mStaticQueues = $this->Common_Model->get_row('mStaticQueues', ['StaticQueueUID'=>$StaticQueueUID]);

                    foreach ($followup_row_available as $followup_row) {
                        $followpdata= [];
                        $followpdata['IsCleared'] = 1;
                        $followpdata['ClearedReasonUID'] = 3;
                        $followpdata['ClearedRemarks'] = sprintf($this->lang->line('Clear_Followup_Init'), $Workflow->WorkflowModuleName, $mStaticQueues->StaticQueueName);
                        $followpdata['ClearedByUserUID'] = $this->loggedid;
                        $followpdata['ClearedDateTime'] = date('Y-m-d H:i:s');
                        $this->db->where(['FollowUpUID'=>$followup_row->FollowUpUID]);
                        $this->db->update('tOrderFollowUp', $followpdata);
                        $this->Common_Model->OrderLogsHistory($OrderUID,$followpdata['ClearedRemarks'],Date('Y-m-d H:i:s'));
                    }
                }

                $this->db->where(array('OrderUID'=>$OrderUID,'SubQueueCategoryUID'=>$SubQueueCategoryUID));
                $this->db->delete('tSubQueueCategory');

                $response = array('success' => 1,'message'=>'Category Updated.');
            } else {

                $response = array('success' => 2,'message'=>'Failed');
            }          

        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response))->_display();exit;
    }

    /**
    *Function Expiry Complete Ajax 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Friday 09 October 2020.
    */    
    function ExpiredCompleteorders_ajax_list(){
        $controller = $this->input->get('controller');
        if (isset($this->config->item('workflowcontroller')[$controller])) {

            $WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
            // WorkflowModuleUID
            $post['WorkflowModuleUID'] = $WorkflowModuleUID;

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
            //get_post_input_data
            //column order
            $post['column_order'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

            //column search
            $post['column_search'] = array('tOrders.OrderNumber', 'mCustomer.CustomerName','tOrders.LoanNumber','tOrders.LoanType','mMilestone.MilestoneName','mStatus.StatusName', 'tOrders.PropertyStateCode', 'tOrderWorkflows.EntryDatetime','tOrderWorkflows.DueDateTime','tOrders.LastModifiedDateTime');

            /**
            *Function Description: Dynamic Columns Queues
            *@author Sathis Kannan <sathish.kannan@avanzegroup.com>
            *@since 23.7.2020
            */
             /* ****** Dynamic Queues Section Starts ****** */
            $QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
            if (!empty($QueueColumns)) 
            {
              $columndetails = $this->Common_Model->dynamicColumnNames($QueueColumns, $this->input->post('SubQueueSection'));
              $post['column_order'] = $columndetails;
              $post['column_search'] = array_filter($columndetails);

            }

            $list = $this->Common_Model->ExpiredCompleteOrders($post,'');

            $no = $post['start'];
            $expiredCompletelist = [];

            /**
            *Function Description: Dynamic Columns Queues
            *@author Sathis Kannan <sathish.kannan@avanzegroup.com>
            *@since 23.7.2020
            */
            /* ****** Dynamic Queues Section Starts ****** */
            $Mischallenous['PageBaseLink']      = "FHA_VA_CaseTeam/index/";
            $Mischallenous['AssignButtonClass'] = "FHAPickNewOrder";
            $Mischallenous['QueueColumns'] = $QueueColumns;
            $Mischallenous['SubQueueSection'] = $this->input->post('SubQueueSection');
            $DynamicColumns = $this->Common_Model->getDynamicQueueColumns($list, $$WorkflowModuleUID, $Mischallenous);

            if (!empty($DynamicColumns)) 
            {
                $expiredCompletelist                =   $DynamicColumns['orderslist'];
                $post['column_order']       =   $DynamicColumns['column_order'];
                $post['column_search']      =   $DynamicColumns['column_search'];
                $list = [];
            }
            /* ****** Dynamic Queues Section Ends ****** */

            foreach ($list as $myorders)
            {
                $row = array();
                $row[] = $myorders->OrderNumber;
                $row[] = $myorders->CustomerName;
                $row[] = $myorders->LoanNumber;
                $row[] = $myorders->LoanType;
                $row[] = $myorders->MilestoneName;
                $row[] = '<a  href="javascript:void(0)" style=" background: '.$myorders->StatusColor.' !important;padding: 5px 10px;border-radius:0px;" class="btn">'.$myorders->StatusName.'</a>';
                $row[] = $myorders->PropertyStateCode;
                $row[] = site_datetimeaging($myorders->EntryDatetime);
                $row[] = site_datetimeformat($myorders->DueDateTime);
                $row[] = site_datetimeformat($myorders->LastModifiedDateTime);

                $FollowUp = ''; 
                if(!empty($myorders->FollowUpUID)) {
                    $FollowUp = '<span tite="FollowUp" class="badge badge-pill followupbadge">F</span>';
                }

                if($this->loggedid == $myorders->AssignedToUserUID)
                {
                    $Action = '<a href="PreScreen/index/'. $myorders->OrderUID.'" class="btn btn-link btn-info btn-just-icon btn-xs" data-orderuid = "'.$myorders->OrderUID.'"><i class="icon-pencil"></i>'.$FollowUp.'</a>';
                }else{

                    $Action = '<button class="btn btn-info btn-sm PreScreenPickNewOrder" data-workflowmoduleuid="'.$myorders->WorkflowModuleUID.'" data-orderuid="'.$myorders->OrderUID.'" data-projectuid="'.$myorders->ProjectUID.'"><i class="fa fa-hand-o-up" aria-hidden="true"></i>'.$FollowUp.'</button>';
                }
                $row[] = $Action;
                $expiredCompletelist[] = $row;
            }

            $data =  array(
                'orderslist' => $expiredCompletelist,
                'post' => $post
            );

            $post = $data['post'];
            $output = array(
                "draw" => $post['draw'],
                "recordsTotal" => $this->Common_Model->ExpiredCompletecount_all($post),
                "recordsFiltered" =>  $this->Common_Model->ExpiredCompletecount_filtered($post,''),
                "data" => $data['orderslist'],
            );

            unset($post);
            unset($data);

            echo json_encode($output);
        } else {
            echo json_encode(["data"=>[]]);
        }
            
    }

    /**
    *Function Processor Details 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Thursday 22 October 2020.
    */
    function get_allonshoreprocessors() {
        $Processors = $this->Common_Model->get_allonshoreprocessors();
        $this->output->set_content_type('application/json')
        ->set_output(json_encode(array('validation_error'=>0, 'Processors'=>$Processors)))
        ->_display(); exit;
    }

    /**
    *Function Holidays 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Tuesday 10 November 2020.
    */
    function fetch_HolidayListPopup()
    {
        $data['HolidayDetails'] = $this->Common_Model->GetHolidays(TRUE);
        $data = $this->load->view('common/holidaylist',$data,true);
        echo json_encode(array('success' => 1,'data'=>$data));exit;

    }

    /**
    *Function Update ProcessorChosenClosingDate 
    *@author SathishKumar <sathish.kumar@avanzegroup.com>
    *@since Thursday 19 November 2020.
    */
    function Update_ProcessorChosenClosingDate() {
        $OrderUID = $this->input->post('OrderUID');
        $WorkflowModuleUID = $this->input->post('WorkflowModuleUID');
        $ProcessorChosenClosingDate = $this->input->post('ProcessorChosenClosingDate');

        if(!empty($OrderUID) && !empty($WorkflowModuleUID) && !empty($ProcessorChosenClosingDate)) {

            $this->db->where('OrderUID', $OrderUID);
            $this->db->update('tOrderImport', array('ProcessorChosenClosingDate'=>Date('m/d/Y', strtotime($ProcessorChosenClosingDate))));

            if ($this->db->affected_rows() > 0) {

                $this->db->select('*');
                $this->db->from('tOrderAssignments');
                $this->db->where(array(
                    'OrderUID' => $OrderUID,
                    'WorkflowModuleUID' => $WorkflowModuleUID
                ));
                $this->db->where('(tOrderAssignments.AssignedToUserUID <> "" OR tOrderAssignments.AssignedToUserUID IS NOT NULL) AND (tOrderAssignments.WorkflowStatus != '.$this->config->item('WorkflowStatus')['Completed'].')');
                $result = $this->db->get();
                
                $LogDetails = '';
                if ($result->num_rows()) {

                    // when processor preferred closing date changed assigned date to be updated by now
                    $this->db->where(array(
                        'OrderUID' => $OrderUID,
                        'WorkflowModuleUID' => $WorkflowModuleUID
                    ));                
                    $this->db->update('tOrderAssignments', array('AssignedDatetime'=>date('Y-m-d H:i:s')));    

                    if ($this->db->affected_rows() > 0) {

                        $tOrderAssignmentsData = $result->row();

                        $LogDetails = "<br/>Assigned date ".Date('m/d/Y', strtotime($tOrderAssignmentsData->AssignedDatetime))." changed to ".date('m/d/Y').'.';
                    }
                }

                /*INSERT ORDER LOGS BEGIN*/
                $this->Common_Model->OrderLogsHistory($OrderUID, 'Processor Preferred Closing Date <b>'.Date('m/d/Y', strtotime($ProcessorChosenClosingDate)).'</b> is updated.'.$LogDetails, Date('Y-m-d H:i:s'));
                /*INSERT ORDER LOGS END*/

                $response = array('status' => 1,'message'=>'Processor Preferred Closing Date is updated.');
            }
            
        } else {

            $response = array('status' => 0,'message'=>'Something went wrong.');
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($response))->_display();exit;
    }

}
