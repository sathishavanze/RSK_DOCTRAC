<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Efax extends MY_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->model('Efax_model');
    $this->loggedid = $this->session->userdata('UserUID');
    $this->UserName = $this->session->userdata('UserName');
    $this->RoleUID = $this->session->userdata('RoleUID');
  }

  function index()
  {
    $data['EventCode'] = 'SendFax';
    $data['OrderUID'] = '194';
    $user = $this->Efax_model->pluckSettingValue('RMUser');
    $subject = 'Mortgagee Clause Change Request - Loan Number : '.'';
    $DocumentDetails = $this->Efax_model->get_tDocuments('194');
    $OrgDetails = $this->Efax_model->GetEFaxCredentials();
    $dd['destination'] = array('to_name' => '', 'to_company' => '', 'fax_number' => '13023516536');
    $dd['cover_page_options'] = array('from_name' => $user, 'subject' => $subject, 'message' => $subject);
    $dd['documents'] = $DocumentDetails;

    $fax_details['json_data'] = $dd;
    $fax_details['EventCode'] = 'SendFax';
    $fax_details['OrderUID'] = '194';
    $fax_details['OrderNumber'] = 'S20000191';
    $fax_details['ClientAuthKey'] = $OrgDetails['EFaxToken'];
    $fax_details['ClientUserID'] = $OrgDetails['EFaxUserID'];
    $fax_list = $this->Efax_model->Func_FaxDetails($fax_details);
    if($fax_list){
      echo "true";
    }

  }

  /**
  * Function to Generate Bearer Token for EFax Integration
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return NULL
  * @since July 17th 2020
  * @version E-Fax Integration
  *
  */
  function GenerateBearerToken(){
    $res = $this->Efax_model->ToGenerateBearerToken(1); /* Get Crendential of Efax from mSetting table */
    echo $res;
  }

  /**
  * Function to sent the fax details from e-Fax Integration
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return NULL
  * @since July 17th 2020
  * @version E-Fax Integration
  *
  */
  function GetListOfFaxSent(){
    $data['EventCode'] = 'ListOfFaxSent';
    $OrgDetails = $this->Efax_model->GetEFaxCredentials(); /* Get Crendential of Efax from mSetting table */
    $data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
    $data['ClientUserID'] = $OrgDetails['EFaxUserID'];
    $fax_list = $this->Efax_model->Func_FaxDetails($data); /* Posting the values and get the Fax List from Efax Intergration */
    echo "Successful";
  }

  /**
  * Function to retrive the fax details from e-Fax Integration
  *
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return NULL
  * @since July 17th 2020
  * @version E-Fax Integration
  *
  */
  function GetListOfFaxReceived(){
    $data['EventCode'] = 'ListOfFaxReceived';
    $OrgDetails = $this->Efax_model->GetEFaxCredentials(); /* Get Crendential of Efax from mSetting table */
    $data['ClientAuthKey'] = $OrgDetails['EFaxToken'];
    $data['ClientUserID'] = $OrgDetails['EFaxUserID'];
    $fax_list = $this->Efax_model->Func_FaxDetails($data); /* Posting the values and get the Fax List from Efax Intergration */
    echo "Successful";
  }
}?>
