<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Efax_model extends MY_Model {
  function __construct()
  { 
    parent::__construct();
  }

  function get_OrderDetails($OrderUID){
    $this->db->select('tOrders.OrderUID,tOrders.OrderNumber,tOrders.LoanNumber,mCustomer.CustomerEmail,mCustomer.CustomerName,tOrderImport.InsuranceCompany,tOrderImport.PolicyNumber,tDocuments.DocumentUID');
    $this->db->from('tOrders');
    $this->db->join('mCustomer','mCustomer.CustomerUID=tOrders.CustomerUID', 'left');
    $this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID');
    $this->db->join('tDocuments','tDocuments.OrderUID=tOrders.OrderUID');
    $this->db->where(['tOrders.OrderUID'=>$OrderUID]);
    $tOrders = $this->db->get();
    if($tOrders->num_rows() > 0){
      return $tOrders->row();
    }else{
      return false;
    }
  }

  function get_tDocuments($OrderUID)
  {
    $this->db->select('*');
    $this->db->from('tDocuments');
    $this->db->where('tDocuments.OrderUID', $OrderUID);
    return $this->db->get()->result();
  }

  function pluckSettingValue($FieldName)
  {
    $this->db->select('SettingValue');
    $this->db->from('mSettings');
    $this->db->where('SettingField',$FieldName);
    $query = $this->db->get();
    if($query->num_rows() > 0){
      return $query->row()->SettingValue; 
    }else{
      return '';
    }
  }

  function Func_FaxDetails($data)
  {
    $post['details'] = $data;
    $EventCode = $data['EventCode'];
    $post['EventCode'] = $data['EventCode'];
    $post['OrderUID'] = $data['OrderUID'];
    $post['OrderNumber'] = $data['OrderNumber'];
    $post['ClientAuthKey'] = $data['ClientAuthKey'];
    $post['ClientUserID'] = $data['ClientUserID'];
    $fax_crendentials = $this->ToGenerateBearerToken($post);
    $post['ClientAuthKey'] = $fax_crendentials['ClientAuthKey'];
    $post['URL'] = $fax_crendentials['URL'];
    $URL = $fax_crendentials['URL'];

    switch ($EventCode) {
      case 'SendFax':
      $json_efax = $this->GetSendFaxInformation($data);
      if($json_efax){
        $post['PostFields'] = $json_efax;
        $post['EventCode'] = 'SendFax';
        $post['URL'] = $URL;
        return $this->SendPostRequestTo_EFax($post);
      } else {
        echo json_encode(array('Status' => 1,'message'=>'Error!! No Documents Found in '.$data['OrderNumber'],'type' => 'danger','OrderUID'=>$data['OrderUID']));
      }
      break;

      case 'ListOfFaxSent':
      $params = '';
      $post['EventCode'] = 'ListOfFaxSent';
      $post['params'] = $data['params'];
      if($post['params']){
        $params = $post['params'];
      }
      $post['URL'] = $URL.'/sent'.$params;
      return $this->SendGetRequestTo_EFax($post);
      break;

      case 'SingleMetaDateRetrieve':
      $post['EventCode'] = 'SingleMetaDateRetrieve';
      if(!empty($data['FaxID'])){
        $post['URL'] = $URL.'/'.$data['FaxID'].'/metadata';
        return $this->SendGetRequestTo_EFax($post);
      }
      break;

      case 'FaxImageRetrieve':
      $post['EventCode'] = 'FaxImageRetrieve';
      $params = "?desired_format=PDF";
      if(!empty($data['FaxID'])){
        $post['URL'] = $URL.'/'.$data['FaxID'].'/image'.$params;
        return $this->SendGetRequestTo_EFax($post);
      }
      break;

      /* @Desc: Switch for List Of Fax Received @Author: Yagavi G <yagavi.g@avanzegroup.com> @Since: July 17th 2020 */
      case 'ListOfFaxReceived':
      $params = '';
      $post['EventCode'] = 'ListOfFaxReceived';
      $post['params'] = $data['params'];
      if($post['params']){
        $params = $post['params'];
      }
      $post['URL'] = $URL.'/received'.$params;
      return $this->SendGetRequestTo_EFax($post);
      break;      
    }
  }

  function GetSendFaxInformation($data)
  {
    $postdetails = $data['details'];
    $fax_destination = $data['json_data']['destination'];
    $fax_cover_page_options = $data['json_data']['cover_page_options'];
    $fax_documents = $data['json_data']['documents'];

    $destinations = array('to_name' => $fax_destination['to_name'], 'to_company' => $fax_destination['to_company'], 'fax_number' => $fax_destination['fax_number']);
    $fax_options = array('image_resolution' => 'STANDARD', 'include_cover_page' => true, 'cover_page_options' => array('from_name' => $fax_cover_page_options['from_name'], 'subject' => $fax_cover_page_options['subject'], 'message' => $fax_cover_page_options['message']), 'retry_options' => array('non_billable' => '2', 'billable' => '3', 'human_answer' => '1' ));

    $documents = [];
    foreach ($fax_documents as $key => $value) {
      $path = FCPATH.$value->DocumentURL;
      $text = file_get_contents(FCPATH.$value->DocumentURL);
      $Content = base64_encode($text);
      if(!empty($Content)){
        $documents[] = array('document_type' => 'PDF', 'document_content' => $Content);
      }
    }
    if(empty($documents)){
      return 0;
    } else {
      /*$client_data = array('client_code' => 'aliquip sit proident', 'client_id' => 'aliquip sit proident', 'client_matter' => 'aliquip sit proident', 'client_name' => 'aliquip sit proident', 'client_reference_id' => 'aliquip sit proident', 'billing_code' => 'aliquip sit proident');*/
      $efax = array('destinations' => array($destinations),'fax_options' => $fax_options,/*'client_data' => $client_data,*/'documents' => $documents);

      $json_efax = json_encode($efax);
      return $json_efax; 
    }
  }

  function SendPostRequestTo_EFax($post)
  {
    $OrderUID = $post['OrderUID'];
    $OrderNumber = $post['OrderNumber'];
    $postData = $post['PostFields'];
    $url = $post['URL'];
    $ClientAuthKey = $post['ClientAuthKey'];
    $ClientUserID = $post['ClientUserID'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_HTTPHEADER => array(
        "authorization: ".$ClientAuthKey,
        "content-type: application/json",
        "user-id: ".$ClientUserID,
        "transaction-id: ".$OrderNumber,
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    echo '<pre>';print_r($postData);
    echo '<pre>';print_r($response);

    if ($err) {
      return false;
    } else {
      $jsontoarray = json_decode($response, true);
      $fax_info['OrderUID'] = $OrderUID;
      $fax_info['OrderNumber'] = $OrderNumber;
      $fax_info['FaxDetails'] = $jsontoarray;
      $res=$this->GetFaxID($fax_info);
      return $res;
    }
  }

  function GetFaxID($fax_info)
  {
    if(isset($fax_info['FaxDetails']['errors'])){
      $transaction_id = $fax_info['transaction_id'];
      $errors = $fax_info['errors'];
      $error_code = $errors['error_code'];
      $element_name = $errors['element_name'];
      $developer_message = $errors['developer_message'];
      $user_message = $errors['user_message'];
      return false;
    } else {
      $OrderUID = $fax_info['OrderUID'];
      $OrderNumber = $fax_info['OrderNumber'];
      $FaxDetails = $fax_info['FaxDetails'];
      foreach ($FaxDetails as $key => $fax) {
        $fax_id = $fax['fax_id'];
        $destination_fax_number = $fax['destination_fax_number'];

        $tEFaxData = array(
          'OrderUID' => $OrderUID, 
          'TransactionID' => $OrderNumber, 
          'FaxID' => $fax_id, 
          'ToFaxNumber' => $destination_fax_number, 
          'Message' => '',
          'FaxType' => 'SENT',
          'CreatedBy' => $this->session->userdata('UserUID'),
          'CreatedDate' => date('Y-m-d H:i:s', strtotime('now')),
          'ModifiedBy' => $this->session->userdata('UserUID'),
          'ModifiedDate' => date('Y-m-d H:i:s', strtotime('now')),
        );
        $this->db->insert('tEFaxData', $tEFaxData);
        $EFaxUserID = $this->db->insert_id();
        if($EFaxUserID){
          return $EFaxUserID;
        }
      }
    }
  }

  function SendGetRequestTo_EFax($post)
  {
    $OrderUID = $post['OrderUID'];
    $OrderNumber = $post['OrderNumber'];
    $EventCode = $post['EventCode'];
    $url = $post['URL'];
    $ClientAuthKey = $post['ClientAuthKey'];
    $ClientUserID = $post['ClientUserID'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "user-id: ".$ClientUserID,
        "Authorization: ".$ClientAuthKey
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      return false;
    } else {

      $jsontoarray = json_decode($response, true);

      if(isset($jsontoarray['errors'])){
        $transaction_id = $jsontoarray['transaction_id'];
        $errors = $jsontoarray['errors'];
        foreach ($errors as $key => $value) {
          $error_code = $value['error_code'];
          $element_name = $value['element_name'];
          $developer_message = $value['developer_message'];
          $user_message = $value['user_message'];
          if($error_code == 'UNAUTHORIZED'){
            return $this->ToGenerateBearerToken($post);
          } else {
            return $jsontoarray;
          }
        }
      } else {

        switch ($EventCode) {
          case 'ListOfFaxSent':
          return $this->ToProcessListOfFaxSend($jsontoarray);
          break;

          case 'SingleMetaDateRetrieve':
          return $this->ToProcessSingleMetaDateRetrieve($jsontoarray);
          break;

          case 'FaxImageRetrieve':
          return $this->ToProcessFaxImageRetrieve($jsontoarray);
          break;

          case 'ListOfFaxReceived':
          return $this->ToProcessListOfFaxReceived($jsontoarray);
          break;
        }
      }
    }

  }

  function ToProcessListOfFaxSend($jsontoarray)
  {
    $Faxes = $jsontoarray['faxes'];
    $fax_list = [];
    foreach ($Faxes as $key => $fax) {
      $fax_list[] = $this->GetFaxDetails($fax,'SENT');     
    }
    return $fax_list;
  }

  function ToProcessSingleMetaDateRetrieve($jsontoarray)
  {
    $this->GetFaxDetails($jsontoarray,'SENT');
    return $jsontoarray;
  }

  function ToProcessFaxImageRetrieve($jsontoarray)
  {
    //$this->GetFaxDetails($jsontoarray,'RECEIVE');
    return $jsontoarray;
  }

  function ToProcessListOfFaxReceived($jsontoarray)
  {
    $Faxes = $jsontoarray['faxes'];
    $fax_list = [];
    foreach ($Faxes as $key => $fax) {
      $fax_list[] = $this->GetFaxDetails($fax,'RECEIVE');     
    }
    return $fax_list;
  }

  function GetFaxDetails($faxInfo,$faxType='')
  {
    if(isset($faxInfo['errors'])){

      $transaction_id = $faxInfo['transaction_id'];
      $errors = $faxInfo['errors'];
      $error_code = $errors['error_code'];
      $element_name = $errors['element_name'];
      $developer_message = $errors['developer_message'];
      $user_message = $errors['user_message'];

    } else {

      $fax_details['fax_id'] = isset($faxInfo['fax_id']) ? $faxInfo['fax_id'] : '';
      $fax_details['size'] = isset($faxInfo['size']) ? $faxInfo['size'] : '';
      $fax_details['duration'] = isset($faxInfo['duration']) ? $faxInfo['duration'] : '';
      $fax_details['pages'] = isset($faxInfo['pages']) ? $faxInfo['pages'] : '';
      $fax_details['image_downloaded'] = isset($faxInfo['image_downloaded']) ? $faxInfo['image_downloaded'] : '';
      $fax_details['fax_status'] = isset($faxInfo['fax_status']) ? $faxInfo['fax_status'] : '';
      $fax_details['completed_timestamp'] = isset($faxInfo['completed_timestamp']) ? $faxInfo['completed_timestamp'] : '';
      $fax_details['direction'] = isset($faxInfo['direction']) ? $faxInfo['direction'] : '';
      $fax_details['originating_fax_number'] = isset($faxInfo['originating_fax_number']) ? $faxInfo['originating_fax_number'] : '';
      $fax_details['destination_fax_number'] = isset($faxInfo['destination_fax_number']) ? $faxInfo['destination_fax_number'] : '';
      $fax_details['originating_fax_tsid'] = isset($faxInfo['originating_fax_tsid']) ? $faxInfo['originating_fax_tsid'] : '';
      $fax_details['destination_fax_csid'] = isset($faxInfo['destination_fax_csid']) ? $faxInfo['destination_fax_csid'] : '';

      $routing_data = isset($faxInfo['routing_data']) ? $faxInfo['routing_data'] : '';
      $fax_details['routing_data_from_name'] = isset($routing_data['from_name']) ? $routing_data['from_name'] : '';
      $fax_details['routing_data_to_name'] = isset($routing_data['to_name']) ? $routing_data['to_name'] : '';
      $fax_details['routing_data_to_company'] = isset($routing_data['to_company']) ? $routing_data['to_company'] : '';
      $fax_details['routing_data_subject'] = isset($routing_data['subject']) ? $routing_data['subject'] : '';
      $fax_details['routing_data_cover_page_tags'] = isset($routing_data['cover_page_tags']) ? $routing_data['cover_page_tags'] : '';

      $transmission_data = isset($faxInfo['transmission_data']) ? $faxInfo['transmission_data'] : '';
      $fax_details['transmission_data_transmission_status'] = isset($transmission_data['transmission_status']) ? $transmission_data['transmission_status'] : '';
      $fax_details['transmission_data_error_code'] = isset($transmission_data['error_code']) ? $transmission_data['error_code'] : '';
      $fax_details['transmission_data_error_message'] = isset($transmission_data['error_message']) ? $transmission_data['error_message'] : '';
      $fax_details['transmission_data_billable_retries'] = isset($transmission_data['billable_retries']) ? $transmission_data['billable_retries'] : '';

      $client_tracking_data = isset($faxInfo['client_tracking_data']) ? $faxInfo['client_tracking_data'] : '';
      $fax_details['client_tracking_data_client_code'] = isset($client_tracking_data['client_code']) ? $client_tracking_data['client_code'] : '';
      $fax_details['client_tracking_data_client_id'] = isset($client_tracking_data['client_id']) ? $client_tracking_data['client_id'] : '';
      $fax_details['client_tracking_data_client_matter'] = isset($client_tracking_data['client_matter']) ? $client_tracking_data['client_matter'] : '';
      $fax_details['client_tracking_data_client_name'] = isset($client_tracking_data['client_name']) ? $client_tracking_data['client_name'] : '';
      $fax_details['client_tracking_data_client_reference_id'] = isset($client_tracking_data['client_reference_id']) ? $client_tracking_data['client_reference_id'] : '';
      $fax_details['client_tracking_data_billing_code'] = isset($client_tracking_data['billing_code']) ? $client_tracking_data['billing_code'] : '';

      $query = $this->db->select("*")->from('tEFaxData')->where(array("tEFaxData.FaxID"=>$fax_details['fax_id']))->get();
      $is_eFax = $query->row();
      $AutomationType = '';
      if($faxType == 'SENT'){
        $AutomationType = 'eFax Sent';
      } else if($faxType == 'RECEIVE'){
        $AutomationType = 'eFax Receive';
      }

      if(!empty($is_eFax)){
        $OrderUID = $is_eFax->OrderUID;
        $EFaxDataUID = $is_eFax->EFaxDataUID;
        $update_tEFaxData = array(
          'FaxType' => $faxType,
          'FaxStatus' => $fax_details['fax_status'], 
          'FromFaxNumber' => $fax_details['originating_fax_number'],
          'TransmissionStatus' => $fax_details['transmission_data_transmission_status'], 
          'ModifiedBy' => $this->session->userdata('UserUID'),
          'ModifiedDate' => date('Y-m-d H:i:s', strtotime('now'))
        );

        $this->db->where('FaxID',$fax_details['fax_id']);
        $this->db->update('tEFaxData',$update_tEFaxData);
        unset($update_tEFaxData);

        /* Desc: Store response details for Automation Log @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 14th 2020 */
        $fieldArray = array('OrderUID' => $OrderUID, 'AutomationType'=> $AutomationType, 'AutomationStatus' => 'Success', 'EFaxDataUID' => $EFaxDataUID, 'CreatedDate' => date('Y-m-d H:i:s'));
        $this->Common_Model->insert_AutomationLog($fieldArray);

      } else {
        $insert_tEFaxData = array(
          'FaxType' => $faxType,
          'FaxID' => $fax_details['fax_id'], 
          'FromFaxNumber' => $fax_details['originating_fax_number'],
          'ToFaxNumber' => $fax_details['destination_fax_number'],
          'CreatedBy' => $this->session->userdata('UserUID'),
          'CreatedDate' => date('Y-m-d H:i:s', strtotime('now')),
          'ModifiedBy' => $this->session->userdata('UserUID'),
          'ModifiedDate' => date('Y-m-d H:i:s', strtotime('now')),
        );
        $this->db->insert('tEFaxData', $insert_tEFaxData);
        $EFaxDataID = $this->db->insert_id();
        unset($insert_tEFaxData);
        /* Desc: Store response details for Automation Log @author: Yagavi.g <yagavi.g@avanzegroup.com> @Since: July 14th 2020 */
        $fieldArray = array('AutomationType'=> $AutomationType, 'AutomationStatus' => 'Success', 'EFaxDataUID' => $EFaxDataID, 'CreatedDate' => date('Y-m-d H:i:s'));
        $this->Common_Model->insert_AutomationLog($fieldArray);
      }
      return $fax_details;
    }
  }

  function ToGenerateBearerToken($post)
  {
    $OrgDetails = $this->GetEFaxCredentials();
    $token_url = $OrgDetails['EFaxTokenURL'];
    $auth = $OrgDetails['EFaxAuthKey'];

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $token_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "grant_type=client_credentials",
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/x-www-form-urlencoded",
        "Authorization: ".$auth
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    if($response){
      $jsontoarray = json_decode($response, true);
      $access_token = $jsontoarray['access_token'];
      $token_type = $jsontoarray['token_type'];
      $ClientAuthKey = $token_type.' '.$access_token;
      $mSettings['SettingValue'] = $ClientAuthKey;
      $this->db->where('SettingField', 'EFaxToken');
      $res = $this->db->update('mSettings',$mSettings);
      if($res){
        $Org = $this->GetEFaxCredentials();
        $fax_crendentials['ClientAuthKey'] = $Org['EFaxToken'];
        $fax_crendentials['ClientUserID'] = $Org['EFaxUserID'];
        $fax_crendentials['URL'] = $Org['EFaxURL'];
        return $fax_crendentials;
      }
    }
  }

  function GetEFaxCredentials()
  {  
    $this->db->select("*");  
    $this->db->from('mSettings');
    $output = $this->db->get();
    $mSettings = $output->result_array();
    $arr = [];
    foreach ($mSettings as $key => $value) {
      if($value['SettingField'] == 'EFaxToken'){
        $arr['EFaxToken'] = $value['SettingValue'];
      }
      if($value['SettingField'] == 'EFaxAuthKey'){
        $arr['EFaxAuthKey'] = $value['SettingValue'];
      }
      if($value['SettingField'] == 'EFaxTokenURL'){
        $arr['EFaxTokenURL'] = $value['SettingValue'];
      }
      if($value['SettingField'] == 'EFaxUserID'){
        $arr['EFaxUserID'] = $value['SettingValue'];
      }
      if($value['SettingField'] == 'EFaxURL'){
        $arr['EFaxURL'] = $value['SettingValue'];
      }
    }
    return $arr;
  }
}?>
