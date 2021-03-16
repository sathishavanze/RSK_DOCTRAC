<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Ordersummarymodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function GettOrders($OrderUID)
	{
		$this->db->select('*,tOrders.OrderUID');
		$this->db->from('tOrders');
		$this->db->join('tOrderImport','tOrderImport.OrderUID = tOrders.OrderUID','left');
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
	function GetBorrowerName($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('tOrderPropertyRole');
		$this->db->where(array('tOrderPropertyRole.OrderUID'=>$OrderUID));
		$query = $this->db->get();
		return $query->result();
	}


	function insert_order($data)
	{

		$date = date('Ymd');

		$this->db->trans_begin();

		//Get Old Order value
		$query = $this->db->get_where('tOrders',array("tOrders.OrderUID"=>$data['OrderUID']));
		$tOrders_OldValue = $query->row_array();

		$insertdata = new stdClass();

		$insertdata->AltORderNumber = $data['AltORderNumber'];
		$insertdata->LoanNumber = $data['LoanNumber'];
		$insertdata->CustomerReferenceNumber = $data['CustomerRefNum'];
		$insertdata->CustomerUID = $data['Customer'];
		$insertdata->PropertyAddress1 = $data['PropertyAddress1'];
		$insertdata->PropertyAddress2 = $data['PropertyAddress2'];
		$insertdata->PropertyCityName = $data['PropertyCityName'];
		$insertdata->PropertyStateCode = $data['PropertyStateCode'];
		$insertdata->PropertyCountyName = $data['PropertyCountyName'];
		$insertdata->PropertyZipcode = $data['PropertyZipcode'];
		$insertdata->LoanType = $data['LoanType'];
		$insertdata->HOI = $data['HOI'];
		$insertdata->Payoff = $data['Payoff'];


		$OrderNos = [];

		$this->db->where(array("tOrders.OrderUID"=>$data['OrderUID']));    
		$query = $this->db->update('tOrders', $insertdata);
		$affected_count = $this->db->affected_rows();

		//Check any column affected in tOrders table
		if ($affected_count) {
			$query = $this->db->get_where('tOrders',array("tOrders.OrderUID"=>$data['OrderUID']));
			$tOrders_NewValue = $query->row_array();

			//Check array difference
			$Description = $this->Common_Model->arrayDifference($tOrders_NewValue, $tOrders_OldValue);

			/*INSERT ORDER LOGS BEGIN*/
			$this->Common_Model->OrderLogsHistory($data['OrderUID'],$Description,Date('Y-m-d H:i:s'));
			/*INSERT ORDER LOGS END*/			
		}

		$insert_id = $data['OrderUID'];

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
			$OrderNos[] = $this->db->select('OrderNumber')->from('tOrders')->where('tOrders.OrderUID',$data['OrderUID'])->get()->row()->OrderNumber;
		}

		if ($insert_id) {
			//Select deleted borrower details
			$this->db->select('*');
			$this->db->from('tOrderPropertyRole');
			$this->db->where('tOrderPropertyRole.OrderUID',$insert_id);
			$this->db->where_not_in('SNO',$data['SNO']);
			$query = $this->db->get();
			$result = $query->result_array();

			if (sizeof($result) != 0) {
				foreach ($result as $key => $value) {
					/*INSERT ORDER LOGS BEGIN*/
					$Description = '<span class="log_dc_col">Borrower </span><span class="log_dc_val">"'.$value['BorrowerFirstName'].' '.$value['BorrowerLastName'].'"</span> details removed.<br/>';
					$this->Common_Model->OrderLogsHistory($data['OrderUID'],$Description,Date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/
				}				
			}

			//Delete borrower details
			$this->db->where('tOrderPropertyRole.OrderUID',$insert_id);
			$this->db->where_not_in('SNO',$data['SNO']);
			$this->db->delete('tOrderPropertyRole');
			
			$entry_array = array();
			$count = count($data['BorrowerFirstName']);
			$SNO = $data['SNO'];
			$BorrowerFirstName = $data['BorrowerFirstName']; 
			$BorrowerLastName = $data['BorrowerLastName']; 
			$BorrowerMailAddress = $data['BorrowerMailAddress']; 
			$BorrowerContactNumber = $data['BorrowerContactNumber']; 
			$BorrowerSSN = $data['BorrowerSSN'];

			for($i=0; $i<$count; $i++)  
			{
				if (isset($SNO[$i])) {
					$where_array = array(
						'SNO'=>$SNO[$i],
						'OrderUID'=>$insert_id
					);
					//Select borrower old value
					$query = $this->db->get_where('tOrderPropertyRole',$where_array);
					$tOrderPropertyRole_OldValue = $query->row_array();

					$update_single_array = array(
						'BorrowerFirstName' => $BorrowerFirstName[$i],
						'BorrowerLastName' => $BorrowerLastName[$i],
						'BorrowerMailingAddress1' => $BorrowerMailAddress[$i],
						'BorrowerContactNumber' => $BorrowerContactNumber[$i],
						'BorrowerSSN' => $BorrowerSSN[$i],
					);
					//Update existing borrower
					$this->db->where($where_array);
					$this->db->update('tOrderPropertyRole', $update_single_array);

					//Select borrower New value
					$query = $this->db->get_where('tOrderPropertyRole',$where_array);
					$tOrderPropertyRole_NewValue = $query->row_array();

					//Check array difference
					$Description = $this->Common_Model->arrayDifference($tOrderPropertyRole_NewValue, $tOrderPropertyRole_OldValue);

					if (empty(!$Description)) {
						/*INSERT ORDER LOGS BEGIN*/
						$this->Common_Model->OrderLogsHistory($data['OrderUID'],$Description,Date('Y-m-d H:i:s'));
						/*INSERT ORDER LOGS END*/
					}					
				} else {
					$entry_single_array = array(
						"OrderUID"=>$insert_id,
						'BorrowerFirstName' => $BorrowerFirstName[$i],
						'BorrowerLastName' => $BorrowerLastName[$i],
						'BorrowerMailingAddress1' => $BorrowerMailAddress[$i],
						'BorrowerContactNumber' => $BorrowerContactNumber[$i],
						'BorrowerSSN' => $BorrowerSSN[$i],
					);
					$entry_array[] = $entry_single_array;

					//Check array difference
					unset($entry_single_array['OrderUID']);

					//unset empty variable
					foreach ($entry_single_array as $key => $value) {
						if (is_null($value) || $value == '') {
							unset($entry_single_array[$key]);
						}
					}

					$Description = $this->Common_Model->arrayDifference($entry_single_array, $old = array());
					/*INSERT ORDER LOGS BEGIN*/
					$this->Common_Model->OrderLogsHistory($data['OrderUID'],$Description,Date('Y-m-d H:i:s'));
					/*INSERT ORDER LOGS END*/
				}
			} 
			//Check new borrower is empty
			if (!empty($entry_array)) {
				$this->db->insert_batch('tOrderPropertyRole', $entry_array);	
			}			
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
		return true;
	}

	function checkAssignDocumentStorage($tOrders)
	{

		if ($this->Common_Model->IsWorkflowAvailableForDocUpload($tOrders->OrderUID, $this->config->item('workflows')['Doc_Check_In'])) {

			$tDocuments = $this->Common_Model->get('tDocuments', ['OrderUID'=>$tOrders->OrderUID, 'DocumentStorage'=>'']);

			foreach ($tDocuments as $key => $value) {
				$DocumentStoragevalue = $this->Common_Model->getAvailableDocumentStorage();
				$this->Common_Model->save('tDocuments', ['DocumentStorage'=>$DocumentStoragevalue],['DocumentUID'=>$value->DocumentUID]);

			}

		}
		return true;
	}

	
	function get_customerbyuid($CustomerUID){
		$this->db->select("CustomerCode,CustomerName,CustomerUID,LoanNumberValidation");
		$this->db->from('mCustomer');
		$this->db->where(array("Active"=>1,"CustomerUID"=>$CustomerUID));
		return $this->db->get()->row();
	}
}
?>