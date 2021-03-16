<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer_Model extends MY_Model {

	
	function __construct()
	{ 
		parent::__construct();
	}

	/*Get CustomerDetails by Customer Unique ID*/
	function GetCustomerDetailsByUID($CustomerUID)
	{
		$this->db->select('*, mCustomer.Active');	
		$this->db->from('mCustomer');
		// $this->db->join ('mstates', 'mCustomer.CustomerStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ('mcounties', 'mCustomer.CustomerCountyUID = mcounties.CountyUID' , 'left' );  
		// $this->db->join ('mcities', 'mCustomer.CustomerCityUID = mcities.CityUID' , 'left' ); 
		$this->db->join ('mgroups', 'mCustomer.GroupUID = mgroups.GroupUID' , 'left' ); 
		$this->db->join ('mpricing', 'mCustomer.PricingUID = mpricing.PricingUID' , 'left' ); 
		$this->db->where(array("mCustomer.CustomerUID"=>$CustomerUID)); 
		return $this->db->get()->row();
	}

	function GetParentCompanyDetails()
	{
		$this->db->select('*, mCustomer.Active');	
		$this->db->from('mCustomer');
		$this->db->join ('mstates', 'mCustomer.CustomerStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ('mcounties', 'mCustomer.CustomerCountyUID = mcounties.CountyUID' , 'left' );  
		$this->db->join ('mcities', 'mCustomer.CustomerCityUID = mcities.CityUID' , 'left' ); 
		$this->db->where(array("mCustomer.ParentCompany"=>1));   
		return $this->db->get()->result();
	}


	function getzipcontents($CustomerZipCode = '')
	{
		$query = $this->db->query("SELECT * FROM `mCities` 
			LEFT JOIN mStates ON mCities.StateUID = mStates.StateUID 
			LEFT JOIN mCounties ON mCities.StateUID = mCounties.StateUID 
			AND mCities.CountyUID = mCounties.CountyUID
			WHERE mCities.ZipCode = '$CustomerZipCode'");
		return $query->result();
	}
	/*updating customer basic details*/
	function UpdateCustomerinfoDetails($CustomersDetails)
	{
		$UserUID = $this->session->userdata('UserUID');

		// $data1['ModuleName']='Customer-update';
		// $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
		// $data1['DateTime']=date('y-m-d H:i:s');
		// $data1['TableName']='mCustomer';
		// $data1['UserUID']=$this->session->userdata('UserUID');                

		// $this->db->select('*');
		// $this->db->from('mCustomer');
		// $this->db->where(array("CustomerUID"=>$CustomersDetails['CustomerUID'])); 
		// $oldvalue=$this->db->get('')->row_array();

		$this->db->where(array("CustomerUID"=>$CustomersDetails['CustomerUID']));        
		$result = $this->db->update('mCustomer', $CustomersDetails);

		// $this->db->select('*');
		// $this->db->from('mCustomer');
		// $this->db->where(array("CustomerUID"=>$CustomersDetails['CustomerUID'])); 
		// $newvalue = $this->db->get('')->row_array();
		// $this->Common_Model->Audittrail_diff($newvalue,$oldvalue,$data1);
		return true;
	}



	function UpdatelogoDetails($CustomersDetails, $CustomerUID,  $logo){
		$ParentCompany = isset($CustomersDetails['ParentCompany'])? 1:0;
		$UserUID = $this->session->userdata('UserUID');
		$fieldArray = array(
			"CustomerUID"=>$CustomerUID,
			"ParentCompany"=>$ParentCompany,
			"ParentCompanyUID"=>$CustomersDetails['ParentCompanyUID'],
			"CreatedByUserUID"=>$UserUID,   
			"CreatedOn"=>date('Y-m-d h:i:s'),  
			"Active"=>1,
			"ParentCompanyCheck"=>1,
			"Avatar" => $logo
		);



		$data1['ModuleName']='Customer-update';
		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
		$data1['DateTime']=date('y-m-d H:i:s');
		$data1['TableName']='mCustomer';
		$data1['UserUID']=$this->session->userdata('UserUID');        
		$this->db->select('*');
		$this->db->from('mCustomer');
		$this->db->where(array("CustomerUID"=>$CustomerUID)); 
		$oldvalue=$this->db->get('')->row_array();
		$this->db->where(array("CustomerUID"=>$CustomerUID));        
		$result = $this->db->update('mCustomer', $fieldArray);
		return true;
	}



	function GetCustomerDetails()
	{
		$this->db->select("*,mCustomer.Active");
		$this->db->from('mCustomer');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('CustomerUID',$this->parameters['DefaultClientUID']);
		}
		// $this->db->join('mCities','mCustomer.CityUID=mCities.CityUID','left');
		// $this->db->join('mStates','mCustomer.StateUID=mStates.StateUID','left');
		return $this->db->get()->result();
	}

	function GetParentCompanyNameDetails($ParentCompanyUID)
	{
		$this->db->select('mCustomer.CustomerName,mCustomer.CustomerNumber');	
		$this->db->from('mCustomer');
		$this->db->where(array("mCustomer.CustomerUID"=>$ParentCompanyUID));   
		return $this->db->get()->row();
	}

	/*START FOR PRODUCT TAB*/

	function Get_Customer_SubProduct_ById_Prod($CustomerUID)
	{
		$this->db->select("*");
		$this->db->from('mCustomerProducts');
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID' , 'left' );
// $this->db->join('mCustomerWorkflowModules','mCustomerProducts.CustomerUID=mCustomerWorkflowModules.CustomerUID','left');
// $this->db->join('mWorkFlowModules','mCustomerWorkflowModules.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID','left');

		$this->db->where("mCustomerProducts.CustomerUID",$CustomerUID);
		$this->db->group_by("mCustomerProducts.ProductUID");
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_customer_subproduct_details($CustomerUID,$ProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mCustomerProducts' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID' , 'inner' );
		$this->db->where('mCustomerProducts.ProductUID',$ProductUID);
		$this->db->where('mCustomerProducts.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();
	}	

	/*END FOR PRODUCT TAB*/

	/*START FOR PRICING TAB*/

	function Verify_Password($userid)
	{
		$this->db->where('UserUID',$userid);
		return $this->db->get('musers')->row();
	}

	function getpricingbyUID($PricingUID)
	{
		$this->db->select('*');	
		$this->db->from('mpricingproducts');
		$this->db->where('PricingUID',$PricingUID);
		return $this->db->get()->result();
	}


	/*CUSTOMER COPY FEATURE*/
	function updatepricing($datas,$PricingUID)
	{
		$this->db->trans_begin();

		$this->db->where('PricingUID', $PricingUID);
		$this->db->delete('mpricingproducts');

		$feedata = [];
		foreach ($datas as $key => $value)
		{
			$feedata[] = array(
				'PricingUID' => $PricingUID,
				'StateUID' => $value->StateUID ? $value->StateUID : null,
				'CountyUID' => $value->CountyUID ? $value->CountyUID : null,
				'SubProductUID' => $value->SubProductUID ? $value->SubProductUID : null,
				'Pricing' => $value->Pricing,
				'CancellationFee' => $value->CancellationFee,
			);
		}

		if(!empty($feedata)){
			$this->db->insert_batch('mpricingproducts',$feedata); 
		}


		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$data = array("message"=>"error","type"=>"danger");
		}
		else
		{
			$data1['ModuleName']='Copypricing_insert';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='mpricingproducts';
			$data1['UserUID']=$this->session->userdata('UserUID');                
			$this->Common_Model->Audittrail_insert($data1);
			$this->db->trans_commit();
			$data = array("message"=>"Pricing successfully copied!","type"=>"success");
		}

		echo json_encode($data);
	}
	

	/*END FOR PRODUCT TAB*/

	/*START FOR PRICING TAB*/

	function check_pricing_name($PricingName,$pricinguid = ''){
		$WHERE = '';
		if($pricinguid){
			$WHERE = "AND PricingUID  != '".$pricinguid."' ";
		}
		$res = $this->db->query("SELECT EXISTS(SELECT * FROM mpricing WHERE PricingName = '".$PricingName."' $WHERE ) AS pricing_exists ");
		return  $res->row();
	}

	function insert_new_pricing($CustomerUID,$datas){
		$this->db->trans_begin();

		$this->db->insert('mpricing',$datas);
		$insert_id = $this->db->insert_id();
		$this->db->set('PricingUID', $insert_id);
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->update('mCustomer');

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}else{
			$data1['ModuleName']='Copypricing_insert';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='mpricingproducts';
			$data1['UserUID']=$this->session->userdata('UserUID');                
			$this->Common_Model->Audittrail_insert($data1);
			$this->db->trans_commit();
			return $insert_id;
		}

		echo json_encode($data);
	}


	function update_pricing_uid($data)
	{
		$data1['ModuleName']='pricing-update';
		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
		$data1['DateTime']=date('y-m-d H:i:s');
		$data1['TableName']='mpricing';
		$data1['UserUID']=$this->session->userdata('UserUID');                

		$this->db->select('*');
		$this->db->from('mpricing');
		$this->db->where('PricingUID',$data->PricingUID);
		$oldvalue=$this->db->get('')->row_array();


		$this->db->where('PricingUID',$data->PricingUID);
		$val = $this->db->update('mpricing',$data);
		$this->db->select('*');
		$this->db->from('mpricing');
		$this->db->where('PricingUID',$data->PricingUID);
		$newvalue = $this->db->get('')->row_array();
		$this->Common_Model->Audittrail_diff($newvalue,$oldvalue,$data1);
		return $val;
	}


	function get_Pricingproducts_byCustomerUID($post,$CustomerUID)
	{
		$this->_getcustomerpricingdetail_query($post,$CustomerUID);
		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		$query = $this->db->get();
		return $query->result();  
	}

	function _getcustomerpricingdetail_query($post,$CustomerUID)
	{

		$this->db->select('mpricingproducts.PricingUID,mpricingproducts.PricingProductUID,mpricingproducts.StateUID,mpricingproducts.CountyUID,mpricingproducts.SubProductUID,mpricingproducts.Pricing,mpricingproducts.CancellationFee,CountyName,StateCode,StateName,ProductCode,SubProductCode,ProductName,SubProductName,CustomerUID,mSubProducts.ProductUID');	
		$this->db->from('mpricingproducts');
		$this->db->join ( 'mCustomer', 'mCustomer.PricingUID = mpricingproducts.PricingUID' , 'left' );
		$this->db->join ( 'mstates', 'mstates.StateUID = mpricingproducts.StateUID' , 'left' );
		$this->db->join ( 'mcounties', 'mcounties.CountyUID = mpricingproducts.CountyUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mpricingproducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID' , 'left' );
		$this->db->where('CustomerUID',$CustomerUID);

		if(! empty($post['StateUID'])) {
			$this->db->where('`mstates.`StateUID`',$post['StateUID']);
		}

		if(! empty($post['CountyUID'])) {
			$this->db->where('`mcounties`.`CountyUID`',$post['CountyUID']);
		}

		if (!empty($post['search_value'])) {
			$like = "";
			foreach ($post['column_search'] as $key => $item) { 
				// if datatable send POST for search
				if ($key === 0) { 
				// first loop
					$like .= "( ".$item." LIKE '%".$post['search_value']."%' "; 
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";    
				}
			}
			$like .= ") ";
			$this->db->where($like, null, false);
		}

		if (!empty($post['order'])) 
		{ 
			// here order processing 
			if($post['column_order'][$post['order'][0]['column']]!='')
			{
				$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);    
			}    
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);  
		}
	}

	function count_all($post,$CustomerUID)
	{
		$this->_count_all_customerfee($post,$CustomerUID);
		$query = $this->db->count_all_results();
		return $query;
	}

	public function _count_all_customerfee($post,$CustomerUID)
	{
		$this->db->select('mpricingproducts.PricingUID,mpricingproducts.PricingProductUID,mpricingproducts.StateUID,mpricingproducts.CountyUID,mpricingproducts.SubProductUID,mpricingproducts.Pricing,mpricingproducts.CancellationFee,CountyName,StateCode,StateName,ProductCode,SubProductCode,ProductName,SubProductName,CustomerUID,mSubProducts.ProductUID');	
		$this->db->from('mpricingproducts');
		$this->db->join ( 'mCustomer', 'mCustomer.PricingUID = mpricingproducts.PricingUID' , 'left' );
		$this->db->join ( 'mstates', 'mstates.StateUID = mpricingproducts.StateUID' , 'left' );
		$this->db->join ( 'mcounties', 'mcounties.CountyUID = mpricingproducts.CountyUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mpricingproducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID' , 'left' );
		$this->db->where('CustomerUID',$CustomerUID);
	}

	function count_filtered($post,$CustomerUID)
	{
		$this->_getcustomerpricingdetail_query($post,$CustomerUID);    
		$query = $this->db->get();
		return $query->num_rows();
	}

	/*STOP FOR PRICING TAB*/


	function SaveCustomerDocsInformation($CustomerUID,$Path)
	{

		$CustomerUID = $CustomerUID;
		$this->db->set('CustomerInformation',$Path);
		$this->db->where('CustomerUID', $CustomerUID);
		$this->db->update('mCustomer'); 
		
	}

	function SaveCustomerOrderTypeDocsInformation($CustomerUID,$Path, $OrderTypeUID)
	{

		$CustomerUID = $CustomerUID;
		$data=array('DocumentName'=>$Path, 'CustomerUID'=>$CustomerUID);
		foreach ($OrderTypeUID as $key => $value) {
			$this->db->where('OrderTypeUID', $value);
			$this->db->where('CustomerUID', $CustomerUID);
			$this->db->delete('mcustomerordertypedoc');
			$data['OrderTypeUID']=$value;
			$this->db->insert('mcustomerordertypedoc', $data);
		}		
	}

	function GetAbstractorInstructionFiles($CustomerUID, $OrderTypeUIDs)
	{
		foreach ($OrderTypeUIDs as $key => $value) {
			$this->db->where('CustomerUID', $CustomerUID);
			$this->db->where('OrderTypeUID', $value);
			return $this->db->get('mcustomerordertypedoc')->row()->DocumentName;
			
		}
		return false;
	}

	function DeleteCustomerOrderTypeFiles($CustomerUID, $OrderTypeUIDs)
	{
		foreach ($OrderTypeUIDs as $key => $value) {
			$this->db->where('CustomerUID', $CustomerUID);
			$this->db->where('OrderTypeUID', $value);
			$this->db->delete('mcustomerordertypedoc');
			
		}
		return true;
	}
	function GetOrderTypes()
	{
		return $this->db->get_where('mordertypes', array('Active'=>1))->result();
	}

	function insert_CustomerFee($datas,$CustomerUID,$PricingUID){
		$data = new stdClass();
		$data->PricingUID = $datas->PricingUID;
		$data->StateUID = $datas->StateUID ? $datas->StateUID : null;
		$data->CountyUID = $datas->CountyUID ? $datas->CountyUID : null;
		$data->SubProductUID = $datas->SubProductUID ? $datas->SubProductUID : null;
		$data->Pricing = $datas->Pricing;
		$data->CancellationFee = $datas->CancellationFee;
		if(!$this->check_pricing_exists($data->PricingUID,$data->StateUID,$data->CountyUID,$data->SubProductUID)){

			$this->db->trans_start();

			$query = $this->db->insert('mpricingproducts',$data);
			$insert_id = $this->db->insert_id();
			$data1['ModuleName']='pricing_add';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='mpricingproducts';
			$data1['UserUID']=$this->session->userdata('UserUID');                
			$this->Common_Model->Audittrail_insert($data1);

			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			else{
				$this->db->trans_commit();
				return TRUE;
			}
		}
		return FALSE;

	}

	function update_CustomerFee($datas,$CustomerUID,$PricingUID){


		$this->db->trans_start();
		$data = array(
			"StateUID"=>$datas->StateUID ? $datas->StateUID : null,
			"CountyUID"=>$datas->CountyUID ? $datas->CountyUID : null,
			"SubProductUID"=>$datas->SubProductUID ? $datas->SubProductUID : null,
			"Pricing"=>$datas->Pricing,
			"CancellationFee"=>$datas->CancellationFee

		);

		if(!$this->check_pricing_exists($datas->PricingUID,$data['StateUID'],$data['CountyUID'],$data['SubProductUID'],$datas->PricingProductUID)){


			$data1['ModuleName']='pricing-update';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='mpricingproducts';
			$data1['UserUID']=$this->session->userdata('UserUID');                

			$this->db->select('*');
			$this->db->from('mpricingproducts');
			$this->db->where('PricingProductUID',$datas->PricingProductUID);
			$oldvalue=$this->db->get('')->row_array();


			$this->db->where(array("PricingProductUID"=>$datas->PricingProductUID));        
			$result = $this->db->update('mpricingproducts', $data);

			$this->db->select('*');
			$this->db->from('mpricingproducts');
			$this->db->where('PricingProductUID',$datas->PricingProductUID);
			$newvalue = $this->db->get('')->row_array();
			$this->Common_Model->Audittrail_diff($newvalue,$oldvalue,$data1);
		}

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		}else{
			$this->db->trans_commit();
			return TRUE;
		}

	}

	function getsubproduct_byuid($SubProductUID){
		$this->db->where(array("Active"=>1,"ProductUID" => $SubProductUID));
		$query = $this->db->get('mSubProducts');
		return $query->result();
	}

	function deletepricingproduct($PricingProductUID){
		$this->db->where('PricingProductUID', $PricingProductUID);
		$this->db->delete('mpricingproducts');
		return true;
	}


	function GetCustomerOrderTypeDocs($CustomerUID)
	{
		$this->db->select('mcustomerordertypedoc.*');
		$this->db->select('GROUP_CONCAT(mcustomerordertypedoc.OrderTypeUID) AS OrderTypeUID', false);
		$this->db->select('GROUP_CONCAT(mordertypes.OrderTypeName) AS OrderTypeName', false);
		$this->db->from('mcustomerordertypedoc');
		$this->db->join('mordertypes', 'mcustomerordertypedoc.OrderTypeUID=mordertypes.OrderTypeUID');
		$this->db->where('CustomerUID', $CustomerUID);
		$this->db->group_by('DocumentName');
		return $this->db->get()->result();
	}


	/*PRIVATE AND EXCLUDED ABSTRACTORS STARTS*/

	function GetExcludeAbstractorDtails(){
		$this->db->select("*");
		$this->db->from('mabstractor');
		//$this->db->join('musers','mabstractor.AbstractorUID=musers.AbstractorUID','inner');
		$this->db->where('mabstractor.IsPrivate',0);  
		$query = $this->db->get();
		return $query->result();
	}

	function GetPrivateAbstractorDtails(){
		$this->db->select("*");
		$this->db->from('mabstractor');
		//$this->db->join('musers','mabstractor.AbstractorUID=musers.AbstractorUID','inner');
		$this->db->where('mabstractor.IsPrivate',1);  
		$query = $this->db->get();
		return $query->result();
	}

	function CheckCustomerExcludeAbstractorDtails($AbstractorUID,$CustomerUID){
		$this->db->select("*");
		$this->db->from('MCustomerAbstractor');
		$this->db->where('MCustomerAbstractor.CustomerUID',$CustomerUID);  
		$this->db->where('MCustomerAbstractor.AbstractorUID',$AbstractorUID);  
		$this->db->where('MCustomerAbstractor.ExcludeAbstractor',1);  
		$query = $this->db->get();
		return $query->row();
	}

	function CheckCustomerPrivateAbstractorDtails($AbstractorUID,$CustomerUID){
		$this->db->select("*");
		$this->db->from('MCustomerAbstractor');
		$this->db->where('MCustomerAbstractor.CustomerUID',$CustomerUID);  
		$this->db->where('MCustomerAbstractor.AbstractorUID',$AbstractorUID);  
		$this->db->where('MCustomerAbstractor.ExcludeAbstractor',0);  
		$query = $this->db->get();
		return $query->row();
	}

	function SaveCustomerExcludeAbstractor($CustomerUID, $AbstractorUID, $Action)
	{
		if ($Action=='insert') {
			$this->db->insert('MCustomerAbstractor', array('ExcludeAbstractor'=>1, 'AbstractorUID'=>$AbstractorUID, 'CustomerUID'=>$CustomerUID, 'CreatedByUserUID'=>$this->loggedid, 'CreatedOn'=>date('Y-m-d H:i:s')));
		}
		else{
			$this->db->where(array('ExcludeAbstractor'=>1, 'AbstractorUID'=>$AbstractorUID, 'CustomerUID'=>$CustomerUID));
			$this->db->delete('MCustomerAbstractor');	
		}
	}

	function SaveCustomerPrivateAbstractor($CustomerUID, $AbstractorUID, $Action)
	{
		if ($Action=='insert') {
			$this->db->insert('MCustomerAbstractor', array('ExcludeAbstractor'=>0, 'AbstractorUID'=>$AbstractorUID, 'CustomerUID'=>$CustomerUID, 'CreatedByUserUID'=>$this->loggedid, 'CreatedOn'=>date('Y-m-d H:i:s')));
		}
		else{
			$this->db->where(array('ExcludeAbstractor'=>0, 'AbstractorUID'=>$AbstractorUID, 'CustomerUID'=>$CustomerUID));
			$this->db->delete('MCustomerAbstractor');	
		}
	}

	/*PRIVATE AND EXCLUDED ABSTRACTORS ENDS*/

	/*Duplicate Check*/
	function check_duplicate($StateUID,$CountyUID,$ProductUID,$SubProductUID,$PricingUID,$PricingProductUID)
	{

		$where  =' AND ';

		if($StateUID == ''){
			$where .= 'StateUID IS NULL';
		}else{
			$where .= 'StateUID = '.$StateUID;
		}

		$where .= ' AND ';

		if($CountyUID == ''){
			$where .= 'CountyUID IS NULL';
		}else{
			$where .= 'CountyUID = '.$CountyUID;
		}

		$where .= ' AND ';

		if($SubProductUID == ''){
			$where .= 'SubProductUID IS NULL';
		}else{
			$where .= 'SubProductUID = '.$SubProductUID;
		}


		if($PricingProductUID !=''){

			$query = $this->db->query("SELECT EXISTS(SELECT * FROM `mpricingproducts` WHERE `PricingProductUID` != '".$PricingProductUID."' AND `PricingUID` = '".$PricingUID."' $where) AS countexists");
		}else{
			$query = $this->db->query("SELECT EXISTS(SELECT * FROM `mpricingproducts` WHERE  `PricingUID` = '".$PricingUID."' $where) AS countexists");
			
		}
		return $query->row();

	}

	function GetCustomerApiInfoByPro($CustomerUID,$ProductUID){
		$this->db->select("*");
		$this->db->from('mCustomerApiInfo');
		$this->db->where('CustomerUID',$CustomerUID); 
		$this->db->where('ProductUID',$ProductUID); 
		$this->db->group_by('ProductUID');
		$query = $this->db->get();
		return $query->row();     	
	}

	function GetSourceApi(){
		$this->db->select("*");
		$this->db->from('mApiTitlePlatform');
		$this->db->where('mApiTitlePlatform.OrderSourceCode','Flood');  
		$query = $this->db->get();
		return $query->result();   	
	}

	function update_product_subproduct($CustomerUID,$Prod_WorkflowModule){

		$this->db->trans_start();

		// $this->db->where(array("CustomerUID"=>$CustomerUID));
		// $this->db->delete('mCustomerProducts');

		// $this->db->where(array("CustomerUID"=>$CustomerUID));
		// $this->db->delete('mCustomerWorkflowModules');

		foreach ($Prod_WorkflowModule as $key => $value) 
		{	
			if(empty($value['WorkflowModuleUID'][0])){
				return true;
			}else{
				$entry_array = array();
				$count = count($value['WorkflowModuleUID']);
				$WorkflowModuleUID = $value['WorkflowModuleUID']; 
				$ProductUID = $value['ProductUID'];

				$WorkflowModuleUIDS = implode(',', $WorkflowModuleUID);

				$this->db->query('delete from mCustomerWorkflowModules where `CustomerUID`='.$CustomerUID.' AND `ProductUID`='.$ProductUID.' AND `WorkflowModuleUID` NOT IN('.$WorkflowModuleUIDS.')');

				$this->db->query('delete from mCustomerDependentWorkflowModules where `CustomerUID`='.$CustomerUID.' AND `WorkflowModuleUID` NOT IN('.$WorkflowModuleUIDS.')');

				$this->db->query('delete from mCustomerMilestones where `CustomerUID`='.$CustomerUID.' AND `ProductUID`='.$ProductUID.' AND `WorkflowModuleUID` NOT IN('.$WorkflowModuleUIDS.')');

				$this->db->query('delete from mCustomerWorkflowMetricsDependentWorkflows where `CustomerWorkflowMetricUID` IN (select CustomerWorkflowMetricUID from mCustomerWorkflowMetrics where `CustomerUID`='.$CustomerUID.' AND `WorkflowModuleUID` NOT IN('.$WorkflowModuleUIDS.')) ');

				$this->db->query('delete from mCustomerWorkflowMetrics where `CustomerUID`='.$CustomerUID.' AND `WorkflowModuleUID` NOT IN('.$WorkflowModuleUIDS.')');

				for($i=0; $i<$count; $i++)  
				{
					if (!$this->db->query('SELECT * FROM `mCustomerWorkflowModules` WHERE `CustomerUID`='.$CustomerUID.' AND `ProductUID`='.$ProductUID.' AND `WorkflowModuleUID` = '.$WorkflowModuleUID[$i].'')->num_rows()) {

						$entry_array[] = array(
							"CustomerUID"=>$CustomerUID,
							'WorkflowModuleUID' => $WorkflowModuleUID[$i],
							'ProductUID' => $ProductUID
						);
					}


				}


				$Product_entry_array = array();
				$count = count($value['ProductUID']);
				$ProductUID = $value['ProductUID']; 
				for($i=0; $i<$count; $i++)  
				{
					$Product_entry_array[] = array('ProductUID' => $ProductUID,"CustomerUID"=>$CustomerUID,'BulkImportTemplateName' => 'DocTrac-Std-BulkFormat.xlsx','BulkImportTemplateXMLName' => 'DocTrac-Std-BulkFormat.xml');


				}

				if(!empty($entry_array)){ 

					if (!$this->db->query('SELECT * FROM `mCustomerWorkflowModules` WHERE `CustomerUID`='.$CustomerUID.' AND `ProductUID`='.$ProductUID.'')->num_rows()) {
						$this->db->where(array("CustomerUID"=>$CustomerUID));
						$this->db->delete('mCustomerWorkflowModules');
						$this->db->where(array("CustomerUID"=>$CustomerUID));
						$this->db->delete('mCustomerProducts');
						$this->db->insert_batch('mCustomerProducts', $Product_entry_array);
						$this->db->where(array("CustomerUID"=>$CustomerUID));
						$this->db->delete('mCustomerMilestones');
						$this->db->query('delete from mCustomerWorkflowMetricsDependentWorkflows where `CustomerWorkflowMetricUID` IN (select CustomerWorkflowMetricUID from mCustomerWorkflowMetrics where `CustomerUID`='.$CustomerUID.') ');
						$this->db->where(array("CustomerUID"=>$CustomerUID));
						$this->db->delete('mCustomerWorkflowMetrics');
					} 	

					$this->db->insert_batch('mCustomerWorkflowModules', $entry_array);
				}
			}
			break;
		}

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		}else{
			$this->db->trans_commit();
			return TRUE;
		}
	}

	/*Add customer basic details*/
	function AddCustomerinfoDetails($CustomersDetails)
	{
		$ParentCompany = isset($CustomersDetails['ParentCompany'])? 1:0;
		$UserUID = $this->session->userdata('UserUID');

		// $fieldArray = array(
		// 	"CustomerUID"=>$CustomersDetails['CustomerUID'],
		// 	"CustomerNumber"=>$CustomersDetails['CustomerNumber'],
		// 	"CustomerName"=>$CustomersDetails['CustomerName'],
		// 	"CustomerPContactName"=>$CustomersDetails['CustomerPContactName'],
		// 	"CustomerPContactMobileNo"=>$CustomersDetails['CustomerPContactMobileNo'],
		// 	"CustomerPContactEmailID"=>$CustomersDetails['CustomerPContactEmailID'],
		// 	"CustomerOrderAckEmailID"=>$CustomersDetails['CustomerOrderAckEmailID'],
		// 	"CustomerAddress1"=>$CustomersDetails['CustomerAddress1'],
		// 	"CustomerAddress2"=>$CustomersDetails['CustomerAddress2'],   
		// 	"CustomerZipCode"=>$CustomersDetails['CustomerZipCode'],  
		// 	"CustomerStateUID"=> isset($CustomersDetails['CustomerStateUID']) ? $CustomersDetails['CustomerStateUID'] : null,
		// 	"CustomerCityUID"=> isset($CustomersDetails['CustomerCityUID']) ? $CustomersDetails['CustomerCityUID'] : null,
		// 	"CustomerCountyUID"=> isset($CustomersDetails['CustomerCountyUID']) ? $CustomersDetails['CustomerCountyUID'] : null,
		// 	"CustomerOfficeNo"=>$CustomersDetails['CustomerOfficeNo'],
		// 	"CustomerFaxNo"=>$CustomersDetails['CustomerFaxNo'],
		// 	"CustomerWebsite"=>$CustomersDetails['CustomerWebsite'],
		// 	"PriorityUID"=>$CustomersDetails['PriorityUID'],
		// 	"DefaultTemplateUID"=>$CustomersDetails['DefaultTemplateUID'],
		// 	"TaxCertificateRequired"=>$CustomersDetails['TaxCertificateRequired'],
		// 	"ParentCompany"=>$ParentCompany,
		// 	"ParentCompanyUID"=>$CustomersDetails['ParentCompanyUID'],
		// 	"AdverseConditionsEnabled"=>isset($CustomersDetails['AdverseConditionsEnabled'])? 1:0,
		// 	"AutoBilling"=>isset($CustomersDetails['AutoBilling'])? 1:0,
		// 	"CreatedByUserUID"=>$UserUID,   
		// 	"CreatedOn"=>date('Y-m-d h:i:s'),  
		// 	"Active"=>1,
		// 	"ParentCompanyCheck"=>1
		// );	

		// $data1['ModuleName']='Customer-update';
		// $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
		// $data1['DateTime']=date('y-m-d H:i:s');
		// $data1['TableName']='mCustomer';
		// $data1['UserUID']=$this->session->userdata('UserUID');                
		
		$this->db->insert('mCustomer', $CustomersDetails);
		$CustomerUID = $this->db->insert_id();


		// $this->db->select('*');
		// $this->db->from('mCustomer');
		// $this->db->where(array("CustomerUID"=>$CustomerUID)); 
		// $oldvalue=$this->db->get('')->row_array();


		// $this->db->select('*');
		// $this->db->from('mCustomer');
		// $this->db->where(array("CustomerUID"=>$CustomerUID)); 
		// $newvalue = $this->db->get('')->row_array();

		// $this->Common_Model->Audittrail_diff($newvalue,$oldvalue,$data1);

		return $CustomerUID;

	}  

	function Get_Customer_SubProduct_ById($CustomerUID)
	{
		$this->db->select("*");
		$this->db->from('mCustomerProducts');
		// $this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mCustomerProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID' , 'left' );
		$this->db->where("CustomerUID",$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_customer_template_details($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mcustomertemplates' );
		// $this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mcustomertemplates.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mcustomertemplates.ProductUID' , 'left' );
		$this->db->join ( 'mtemplates', 'mtemplates.TemplateUID = mcustomertemplates.TemplateUID' , 'left' );
		$this->db->where('mcustomertemplates.SubProductUID',$SubProductUID);
		$this->db->where('mcustomertemplates.ProductUID',$ProductUID);
		$this->db->where('mcustomertemplates.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->row();

	}

	function get_customer_workflow_product_details($CustomerUID,$ProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->join ( 'mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID' , 'left' );
		// $this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mCustomerWorkflowModules.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerWorkflowModules.ProductUID' , 'left' );
		// $this->db->where('mCustomerWorkflowModules.SubProductUID',$SubProductUID);
		$this->db->where('mCustomerWorkflowModules.ProductUID',$ProductUID);
		$this->db->where('mCustomerWorkflowModules.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_customer_workflow_details($CustomerUID = ''){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->join ( 'mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID' , 'left' );
		$this->db->join('mProducts','mCustomerWorkflowModules.ProductUID=mProducts.ProductUID');
		if (!empty($CustomerUID)) {
			$this->db->where('mCustomerWorkflowModules.CustomerUID',$CustomerUID);
		} else {
			$this->db->where('mCustomerWorkflowModules.CustomerUID',$this->parameters['DefaultClientUID']);
		}
		$this->db->group_by('mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->order_by('mCustomerWorkflowModules.Position','ASC');
		$this->db->order_by('mCustomerWorkflowModules.WorkflowModuleUID','ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	function GetCustomerProduct($CustomerUID){
		$this->db->select ( 'ProductUID' ); 
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->where('mCustomerWorkflowModules.CustomerUID',$CustomerUID);
		$this->db->group_by('ProductUID');
		$query = $this->db->get();
		return $query->row_array();
	}

	//Customer workflow status update
	function UpdateCustomerWorkflowstatus($CustomerUID, $WorkflowUID, $StatusUID) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('StatusUID' => $StatusUID));	
	}

	//Customer workflow category update
	function UpdateCustomerWorkflowCategory($CustomerUID, $WorkflowUID, $CategoryUID) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('CategoryUID' => $CategoryUID));	
	}

	//Customer workflow ColorCode update
	function UpdateCustomerWorkflowColorCode($CustomerUID, $WorkflowUID, $ColorCode) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('ColorCode' => $ColorCode));	
	}

	//Customer workflow milestone update
	function UpdateCustomerWorkflowMilestone($CustomerUID, $WorkflowUID, $MilestoneUID) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('MilestoneUID' => $MilestoneUID));	
	}

	//Customer workflow milestone insert
	function InsertCustomerWorkflowMilestone($CustomerUID, $ProductUID, $WorkflowUID, $MileStoneUID) {
		$data = array(
			'CustomerUID'=>$CustomerUID,
			'ProductUID'=>$ProductUID,
			'WorkflowModuleUID'=>$WorkflowUID,
			'MileStoneUID'=>$MileStoneUID
		);
		return $this->db->insert('mCustomerMilestones', $data);
	}

	//Check workflow id is already mapped to the client and product
	function CheckCustomerWorkflowMilestoneexist($CustomerUID, $ProductUID, $MilestoneUID) {
		$this->db->select('*');
		$this->db->from('mCustomerMilestones');
		$data = array(
			'CustomerUID'=>$CustomerUID,
			'ProductUID'=>$ProductUID,
			'MileStoneUID'=>$MilestoneUID
		);
		$this->db->where($data);
		$query = $this->db->get();
		return $query->num_rows();
	}

	//Check milestone is already mapped to the client and product
	function CheckCustomerWorkflowMilestoneexists($CustomerUID, $ProductUID, $MilestoneUID) {
		$this->db->select('*');
		$this->db->from('mCustomerMilestones');
		$data = array(
			'CustomerUID'=>$CustomerUID,
			'ProductUID'=>$ProductUID,
			'WorkflowModuleUID'=>null,
			'MileStoneUID'=>$MilestoneUID
		);
		$this->db->where($data);
		$query = $this->db->get();
		return $query->num_rows();
	}

	//Customer workflow optional update
	function UpdateCustomerWorkflowoptional($CustomerUID, $WorkflowUID, $Optional) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('Optional' => $Optional));	
	}

	//Customer workflow IsDocChaseRequire update
	function UpdateCustomerWorkflowIsDocChaseRequire($CustomerUID, $WorkflowUID, $IsDocChaseRequire) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('IsDocChaseRequire' => $IsDocChaseRequire));	
	}

	//Customer workflow IsEscalationRequire update
	function UpdateCustomerWorkflowIsEscalationRequire($CustomerUID, $WorkflowUID, $IsEscalationRequire) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('IsEscalationRequire' => $IsEscalationRequire));	
	}


	//Customer workflow IsParkingRequire update
	function UpdateCustomerWorkflowIsParkingRequire($CustomerUID, $WorkflowUID, $IsParkingRequire, $ParkingType, $ParkingDuration,$IsParkingCron) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('IsParkingRequire' => $IsParkingRequire, 'ParkingType' => $ParkingType, 'ParkingDuration' => $ParkingDuration, 'IsParkingCron' => $IsParkingCron));	
	}

	//Customer workflow SLA update
	function UpdateCustomerWorkflowSLA($CustomerUID, $WorkflowUID, $SLA) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('SLA' => $SLA));	
	}

	//Get category details
	function GetCategoryDetaiils() {
		return $this->db->get('mCategory')->result();
	}

	//Get milestone details
	function GetMilestoneDetaiils() {
		return $this->db->get_where('mMilestone', array('Active' => 1))->result();
	}

	function get_customer_dependent_workflow_details($CustomerUID,$WorkflowModuleUID){
		$this->db->select ( 'GROUP_CONCAT(DependentWorkflowModuleUID) as DependentWorkflowModuleUID' ); 
		$this->db->from ( 'mCustomerDependentWorkflowModules' );
		$this->db->where('mCustomerDependentWorkflowModules.CustomerUID',$CustomerUID);
		$this->db->where('mCustomerDependentWorkflowModules.WorkflowModuleUID',$WorkflowModuleUID);
		$query = $this->db->get()->row();
		return $query->DependentWorkflowModuleUID;
	}

	function get_mcustomerproductusers_details($CustomerUID,$ProductUID){
		$this->db->select ( '*' ); 
		$this->db->from('mCustomerProductUsers');
		$this->db->join('mUsers', 'mUsers.UserUID = mCustomerProductUsers.UserUID' , 'left' );
		// $this->db->join ('mSubProducts', 'mSubProducts.SubProductUID = mCustomerProductUsers.SubProductUID' , 'left' );
		$this->db->join('mProducts', 'mProducts.ProductUID = mCustomerProductUsers.ProductUID' , 'left' );
		// $this->db->where('mCustomerProductUsers.SubProductUID',$SubProductUID);
		$this->db->where('mCustomerProductUsers.ProductUID',$ProductUID);
		$this->db->where('mCustomerProductUsers.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_customer_optionalworkflow_details($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mCustomerOptionalWorkflowModules' );
		$this->db->join ( 'mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerOptionalWorkflowModules.WorkflowModuleUID' , 'left' );
		// $this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mCustomerOptionalWorkflowModules.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerOptionalWorkflowModules.ProductUID' , 'left' );
		$this->db->where('mCustomerOptionalWorkflowModules.SubProductUID',$SubProductUID);
		$this->db->where('mCustomerOptionalWorkflowModules.ProductUID',$ProductUID);
		$this->db->where('mCustomerOptionalWorkflowModules.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();

	}

	function GetGroupCustomers($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mgroupcustomers' );
		$this->db->where('mgroupcustomers.GroupCustomerSubProductUID',$SubProductUID);
		$this->db->where('mgroupcustomers.GroupCustomerProductUID',$ProductUID);
		$this->db->where('mgroupcustomers.GroupCustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->row();

	}

	function GetProductByTemplate($ProductUID)
	{
		$this->db->select("*");
		$this->db->from('mtemplates');
		$this->db->join ('mProducts', 'mtemplates.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->like('mtemplates.ProductUID', $ProductUID);
		$query = $this->db->get();
		$ProductTemplate = $query->result();

		return $ProductTemplate;
	}


	function get_prioritieshours_date($CustomerUID,$PriorityUID,$ProductUID,$SubProductUID){

		$this->db->select('*');
		$this->db->from ( 'mCustomerProductTAT' );

		$this->db->where(array("CustomerUID"=>$CustomerUID,'PriorityUID'=>$PriorityUID,'ProductUID'=>$ProductUID,'SubProductUID'=>$SubProductUID));
		$result = $this->db->get();

		return $result->row();
	}

	function GetmcustomerdefaultProduct($CustomerUID)
	{
		$query = $this->db->query("SELECT DefaultProductSubCode FROM mcustomerdefaultproduct WHERE CustomerUID ='$CustomerUID' ");
		$result = $query->row();
		return $result->DefaultProductSubCode;
	}
	function getCustomerDefaultProduct($CustomerUID)
	{

		$query = $this->db->query("SELECT DefaultProductSubValue FROM mcustomerdefaultproduct WHERE CustomerUID ='$CustomerUID' ");
		if($query->num_rows()>0)
		{
			$data = $query->row();
			$SubProductUID = $data->DefaultProductSubValue;
			$query = $this->db->query("SELECT SubProductUID,SubProductName FROM mSubProducts WHERE SubProductUID IN ($SubProductUID)");
			return $query->result_array();
		}
		else{
			return false;
		}

	}


	function SaveCustomerWorkflowModule($post)
	{
		$ProductUIDs=$post['ProductUID'];
		$SubProductUIDs=$post['SubProductUID'];
		$WorkflowModuleUIDs=isset($post['WorkflowModuleUID']) ? $post['WorkflowModuleUID'] : [];
		$OptionalWorkflowModuleUIDs=isset($post['OptionalWorkflowModuleUID']) ? $post['OptionalWorkflowModuleUID'] : [];
		$CustomerUID=$post['CustomerUID'];
		// $CustomerUID=76;

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mCustomerWorkflowModules');

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mCustomerOptionalWorkflowModules');

		/*Batch Insert Array Declearation*/
		$mgroupcustomers=[];
		$mcustomertemplates=[];
		$mCustomerOptionalWorkflowModules=[];
		$mCustomerWorkflowModules=[];

		foreach ($ProductUIDs as $key => $value) {
			$ProductUID=$value;
			$SubProductUID=$SubProductUIDs[$key];
			$WorkflowModuleUID=isset($WorkflowModuleUIDs[$key]) ? $WorkflowModuleUIDs[$key] : [];
			$OptionalWorkflowModuleUID=isset($OptionalWorkflowModuleUIDs[$key]) ? $OptionalWorkflowModuleUIDs[$key] : [];
			$TemplateUID=$TemplateUIDs[$key];
			$GroupUID=$GroupUIDs[$key];


			foreach ($WorkflowModuleUID as $key => $value) {
				$customerworkflowmodules['WorkflowModuleUID']=$value;
				$customerworkflowmodules['CustomerUID']=$CustomerUID;
				$customerworkflowmodules['ProductUID']=$ProductUID;
				$customerworkflowmodules['SubProductUID']=$SubProductUID;

				$mCustomerWorkflowModules[]=$customerworkflowmodules;
			}

			foreach ($OptionalWorkflowModuleUID as $key => $value) {
				$customeroptionalworkflowmodules['WorkflowModuleUID']=$value;
				$customeroptionalworkflowmodules['CustomerUID']=$CustomerUID;
				$customeroptionalworkflowmodules['ProductUID']=$ProductUID;
				$customeroptionalworkflowmodules['SubProductUID']=$SubProductUID;

				$mCustomerOptionalWorkflowModules[]=$customeroptionalworkflowmodules;
				
			}
		}

		if (!empty($mCustomerWorkflowModules)) {
			$this->db->insert_batch('mCustomerWorkflowModules', $mCustomerWorkflowModules);				

		}
		if (!empty($mCustomerOptionalWorkflowModules)) {
			$this->db->insert_batch('mCustomerOptionalWorkflowModules', $mCustomerOptionalWorkflowModules);				
		}

	}

	function SaveCustomerProductUsers($post)
	{
		$ProductUIDs=$post['ProductUID'];
		$SubProductUIDs=$post['SubProductUID'];
		$CustomerUID=$post['CustomerUID'];
		$CustomerProductUsers=$post['CustomerProductUsers'];
		// $CustomerUID=76;
		// print_r($CustomerProductUsers);exit;

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mCustomerProductUsers');

		/*Batch Insert Array Declearation*/
		$mCustomerProductUsers=[];

		foreach ($ProductUIDs as $key => $value) {
			$ProductUID=$value;
			$SubProductUID=$SubProductUIDs[$key];
			$UsersUIDs=isset($CustomerProductUsers[$key]) ? $CustomerProductUsers[$key] : [];


			foreach ($UsersUIDs as $key => $UserUID) {
				$customerworkflowmodules['UserUID']=$UserUID;
				$customerworkflowmodules['CustomerUID']=$CustomerUID;
				$customerworkflowmodules['ProductUID']=$ProductUID;
				$customerworkflowmodules['SubProductUID']=$SubProductUID;

				$mCustomerProductUsers[]=$customerworkflowmodules;
			}

		}

		if (!empty($mCustomerProductUsers)) {
			$this->db->insert_batch('mCustomerProductUsers', $mCustomerProductUsers);				

		}

	}

	function SaveCustomerPriorities($post)
	{
		$ProductUIDs=$post['ProductUID'];
		$SubProductUIDs=$post['SubProductUID'];
		$PriorityTime=isset($post['PriorityTime']) ? $post['PriorityTime'] : [];
		$TATUID=isset($post['TATUID']) ? $post['TATUID'] : [];
		$CustomerUID=$post['CustomerUID'];

		$Priorities = $this->Common_Model->get('mOrderPriority', ['Active'=>1]);

		$mCustomerProductTAT=[];

		foreach ($ProductUIDs as $key => $value) {
			$ProductUID=$value;
			$SubProductUID=$SubProductUIDs[$key];
			$customerproducttat=[];
			foreach ($Priorities as $pkey => $priority) {
				$PriorityUID=$priority->PriorityUID;
				$vPriorityTime=isset($PriorityTime[$priority->PriorityName][$key]) ? $PriorityTime[$priority->PriorityName][$key] : 0;
				$vTATUID=isset($TATUID[$priority->PriorityName][$key]) ? $TATUID[$priority->PriorityName][$key] : 0;
				if($vPriorityTime != ''){
					$customerproducttat['CustomerUID']=$CustomerUID;
					$customerproducttat['ProductUID']=$ProductUID;
					$customerproducttat['SubProductUID']=$SubProductUID;
					$customerproducttat['PriorityUID']=$PriorityUID;
					$customerproducttat['PriorityTime']=$vPriorityTime;
					$customerproducttat['SkipOrderOpenDate']=$vTATUID;
					$mCustomerProductTAT[]=$customerproducttat;
				}

			}
		}


		if (!empty($mCustomerProductTAT)) {
			$this->db->where(array("CustomerUID"=>$CustomerUID));
			$res = $this->db->delete('mCustomerProductTAT');
			$this->db->insert_batch('mCustomerProductTAT', $mCustomerProductTAT);				
		}

		return true;
	}


	function change_status($CustomerUID,$data)
	{
		$this->db->select('*');
		$this->db->from('mCustomer');
		$this->db->where('CustomerUID',$CustomerUID);
		$Customer = $this->db->get('')->row();
		$this->db->where('CustomerUID',$CustomerUID);
		if($this->db->update('mCustomer',$data))
		{
			if($data['Active']>0){
				$data1['ModuleName'] = $Customer->CustomerName.''.'Customers status Active _add';
			} else{
				$data1['ModuleName'] = $Customer->CustomerName.''.'Customers status InActive _add';
			}
			$data1['IpAddreess'] = $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime'] = date('y-m-d H:i:s');
			$data1['TableName'] = 'mCustomer';
			$data1['UserUID'] = $this->session->userdata('UserUID');                
			$this->Common_Model->Audittrail_insert($data1);
			return true;
		}else{
			return false;
		}
	}

	function product_change_status($ProductUID,$data)
	{
		$this->db->select('*');
		$this->db->from('mProducts');
		$this->db->where('ProductUID',$ProductUID);
		$Product = $this->db->get('')->row();
		$this->db->where('ProductUID',$ProductUID);
		if($this->db->update('mProducts',$data))
		{
			if($data['Active']>0){
				$data1['ModuleName'] = $Product->ProductName.''.'Products status Active _add';
			} else{
				$data1['ModuleName'] = $Product->ProductName.''.'Products status InActive _add';
			}
			$data1['IpAddreess'] = $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime'] = date('y-m-d H:i:s');
			$data1['TableName'] = 'mProducts';
			$data1['UserUID'] = $this->session->userdata('UserUID');                
			$this->Common_Model->Audittrail_insert($data1);
			return true;
		}else{
			return false;
		}
	}

	function GetallProductDetails(){
		$query = $this->db->get('mProducts');
		return $query->result();
	}

	function GetProductDetailsbyUID($ProductUID){
		$this->db->where(array("ProductUID"=>$ProductUID));
		$query = $this->db->get('mProducts');
		return $query->row();
	}

	function saveProductsDetails($PostArray)
	{

		if($PostArray['ProductUID'] == '')
		{
			$UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

			$fieldArray = array(
				"ProductUID"=>$PostArray['ProductUID'],
				"ProductName"=>$PostArray['ProductName'],
				"ProductCode"=>$PostArray['ProductCode'],
				// "AgentPricing"=>$PostArray['AgentPricing'],
				// "UnderWritingPricing"=>$PostArray['UnderWritingPricing'],
				// "InsuranceType"=>$PostArray['InsuranceType'],
				"CreatedBy"=>$UserLoggin,
				"CreatedOn"=>date('Y-m-d H:i:s'),
				"ModifiedBy"=>$UserLoggin,
				"ModifiedOn"=>date('Y-m-d H:i:s'),
				"Active"=>$PostArray['Active']
			);

			$res = $this->db->insert('mProducts', $fieldArray);
			// $data1['ModuleName']='products_add';
			// $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			// $data1['DateTime']=date('y-m-d H:i:s');
			// $data1['TableName']='mProducts';
			// $data1['UserUID']=$this->session->userdata('UserUID');                
			// $this->Common_Model->Audittrail_insert($data1);

			if($res){
				$tabledata = $this->GetallProductDetails();
				$data=array('validation_error' => 0,"message"=> 'Product Add Successfully','tabledata'=>$tabledata);
			}
			else{
				$data=array('validation_error' => 1,"message"=> $this->lang->line('Failed'),'tabledata'=>'');
			}

			return $data;

		}else{
			$UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

			$fieldArray = array(
				"ProductName"=>$PostArray['ProductName'],
				"ProductCode"=>$PostArray['ProductCode'],
				// "AgentPricing"=>$PostArray['AgentPricing'],
				// "UnderWritingPricing"=>$PostArray['UnderWritingPricing'],
				// "InsuranceType"=>$PostArray['InsuranceType'],
				// "CreatedBy"=>$UserLoggin,
				// "CreatedOn"=>date('Y-m-d H:i:s'),
				"ModifiedBy"=>$UserLoggin,
				"ModifiedOn"=>date('Y-m-d H:i:s'),
				"Active"=>$PostArray['Active']
			);
			// $data1['ModuleName']='product-update';
			// $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			// $data1['DateTime']=date('y-m-d H:i:s');
			// $data1['TableName']='mProducts';
			// $data1['UserUID']=$this->session->userdata('UserUID');                

			// $this->db->select('*');
			// $this->db->from('mProducts');
			// $this->db->where('ProductUID',$PostArray['ProductUID']);
			// $oldvalue=$this->db->get('')->row_array();

			$this->db->where(array("ProductUID"=>$PostArray['ProductUID']));
			$res = $this->db->update('mProducts', $fieldArray);
			// $this->db->select('*');
			// $this->db->from('mProducts');
			// $this->db->where('ProductUID',$PostArray['ProductUID']);
			// $newvalue = $this->db->get('')->row_array();
			// $this->Common_Model->Audittrail_diff($newvalue,$oldvalue,$data1);
			if($res){
				$tabledata = $this->GetallProductDetails();
				$data=array('validation_error' => 0,"message"=>'Product Update Successfully','tabledata'=>$tabledata);
			}
			else{
				$data=array('validation_error' => 1,"message"=> $this->lang->line('Failed'),'tabledata'=>'');
			}

			return $data;

		}  
	}

	function getExcelRecords($PricingUID)
	{
		$this->db->select('*')->from('mpricingproducts');
		$this->db->join('mpricing', 'mpricingproducts.PricingUID=mpricing.PricingUID', 'left');
		$this->db->join('mstates', 'mpricingproducts.StateUID=mstates.StateUID', 'left');
		$this->db->join('mcounties', 'mpricingproducts.CountyUID=mcounties.CountyUID', 'left');
		// $this->db->join('mSubProducts', 'mpricingproducts.SubProductUID=mSubProducts.SubProductUID', 'left');
		$this->db->where('mpricingproducts.PricingUID', $PricingUID);
		$this->db->order_by('mpricingproducts.PricingProductUID');
		return $this->db->get()->result();
	}

	function checkPricing($id){
		$this->db->select('PricingUID');
		$this->db->from ('mpricing');
		$this->db->where('PricingUID', $id);
		$this->db->or_where('PricingName', $id);
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->PricingUID;
		}else{
			return NULL;
		}
	}

	function checkState($id){
		$this->db->select('StateUID');
		$this->db->from ('mstates');
		$this->db->where('StateUID', $id);
		$this->db->or_where('StateName', $id);
		$this->db->or_where('StateCode', $id);
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->StateUID;
		}else{
			return NULL;
		}
	}

	function checkCountybyname($State,$CountyName){
		$query = $this->db->query("SELECT CountyUID from mcounties  JOIN mstates on mstates.StateUID = mcounties.StateUID
			where (StateCode ='".$State."' OR StateName='".$State."') and CountyName='".$CountyName."'");
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->CountyUID;
		}else{
			return NULL;
		}		
	}

	function checkSubProduct($id){
		$this->db->select('SubProductUID');
		$this->db->from ('mSubProducts');
		$this->db->where('SubProductUID', $id);
		$this->db->or_where('SubProductName', $id);
		$this->db->or_where('SubProductCode', $id);
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->SubProductUID;
		}else{
			return NULL;
		}
	}

	function checkCounty($id,$StateUID){
		$query = $this->db->query("SELECT CountyUID from mcounties where StateUID ='".$StateUID."' and CountyName='".$id."'");
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->CountyUID;
		}else{
			return NULL;
		}		
	}	

	function saveexcelrecords($dataArr, $PricingUID)
	{

		$this->db->trans_start();

		$this->db->where('PricingUID', $PricingUID);
		$this->db->delete('mpricingproducts');

		$failedData = [];
		$fieldArray = [];
		foreach($dataArr as $val){


			if(!empty($val['0'])){
				if($this->checkPricing($val['0']) != $PricingUID){
					$PricingUID = NULL;
				}

			}else{
				$PricingUID = NULL;
			}


			if(!empty($val['1'])){
				$StateUID = $this->checkState($val['1']);
			}else{
				$StateUID = NULL;
			}
			if(!empty($val['2'])){
				$CountyUID = $this->checkCounty($val['2'],$StateUID);
			}else{
				$CountyUID = NULL;
			}

			if(!empty($val['3'])){
				$SubProductUID = $this->checkSubProduct($val['3']);
			}else{
				$SubProductUID = NULL;
			}	

			if(!empty($val['0']))
			{
				if($PricingUID==NULL){ 
					$failed = array('PricingUID'=>$val['0'],'StateUID'=>$val['1'],'CountyUID'=>$val['2'],'SubProductUID'=>$val['3'],'Pricing'=>$val['4'],'CancellationFee'=>$val['5']);
					array_push($failedData, $failed );

				}else{

					if($this->check_pricing_exists($PricingUID,$StateUID,$CountyUID,$SubProductUID)){
						$failed = array('PricingUID'=>$val['0'],'StateUID'=>$val['1'],'CountyUID'=>$val['2'],'SubProductUID'=>$val['3'],'Pricing'=>$val['4'],'CancellationFee'=>$val['5']);
						array_push($failedData, $failed );
					}else{
						$StateUID = $StateUID ? $StateUID : NULL;
						$CountyUID = $CountyUID ? $CountyUID : NULL;
						$SubProductUID = $SubProductUID ? $SubProductUID : NULL;

						$fieldArray[] = array(
							"PricingUID"=>$PricingUID,
							"StateUID"=>$StateUID,
							"CountyUID"=>$CountyUID,
							"SubProductUID"=>$SubProductUID,
							"Pricing"=>$val['4']=='' ? 0 : $val['4'],
							"CancellationFee"=>$val['5']=='' ? 0 : $val['5']
						);
					}

				}

			}					
		}


		if(!empty($fieldArray)){

			$this->db->insert_batch('mpricingproducts', $fieldArray);
		}


		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}else{
			$this->db->trans_commit();
		}

		$this->db->trans_complete();


		if(!empty($failedData))
		{
			return $failedData;
		}

	}

	function GetAllSubproductDetails(){

		$this->db->select("*,mSubProducts.Active");
		$this->db->from('mSubProducts');
		$this->db->join('mProducts','mSubProducts.ProductUID=mProducts.ProductUID','left');
		$query = $this->db->get();
		return $query->result();
	}

	function GetSubProductDetailsbyUID($SubProductUID){
		$this->db->select("*,mSubProducts.Active");
		$this->db->where(array("SubProductUID"=>$SubProductUID));
		$this->db->join('mProducts','mSubProducts.ProductUID=mProducts.ProductUID','left');
		$query = $this->db->get('mSubProducts');
		return $query->row();
	}

	function subproduct_change_status($SubProductUID,$data)
	{
		$this->db->select('*');
		$this->db->from('mSubProducts');
		$this->db->where('SubProductUID',$SubProductUID);
		$SubProduct = $this->db->get('')->row();
		$this->db->where('SubProductUID',$SubProductUID);
		if($this->db->update('mSubProducts',$data))
		{
			if($data['Active']>0){
				$data1['ModuleName'] = $SubProduct->SubProductName.''.'SubProduct status Active _add';
			} else{
				$data1['ModuleName'] = $SubProduct->SubProductName.''.'SubProduct status InActive _add';
			}
			$data1['IpAddreess'] = $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime'] = date('y-m-d H:i:s');
			$data1['TableName'] = 'mSubProducts';
			$data1['UserUID'] = $this->session->userdata('UserUID');                
			$this->Common_Model->Audittrail_insert($data1);
			return true;
		}else{
			return false;
		}
	}


	function saveSubProductsDetails($PostArray)
	{

		if($PostArray['SubProductUID'] == '')
		{
			$UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

			$fieldArray = array(
				"SubProductCode"=>$PostArray['SubProductCode'],
				"SubProductUID"=>$PostArray['SubProductUID'],
				"ProductUID"=>$PostArray['ProductUID'],
				// "OrderTypeUID"=>$PostArray['OrderTypeUID'],
				// "PriorityUID"=>$PostArray['PriorityUID'],
				"SubProductName"=>$PostArray['SubProductName'],
				// "ReportHeading"=>$PostArray['ReportHeading'],
				"CreatedBy"=>$UserLoggin,
				"CreatedOn"=>date('Y-m-d H:i:s'),
				"ModifiedBy"=>$UserLoggin,
				"ModifiedOn"=>date('Y-m-d H:i:s'),
				"Active"=>$PostArray['Active']
			);

			$res = $this->db->insert('mSubProducts', $fieldArray);
			// $data1['ModuleName']='subproducts_add';
			// $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			// $data1['DateTime']=date('y-m-d H:i:s');
			// $data1['TableName']='mSubProducts';
			// $data1['UserUID']=$this->session->userdata('UserUID');                
			// $this->Common_Model->Audittrail_insert($data1);
			if($res){
				$tabledata = $this->GetAllSubproductDetails();
				$data=array('validation_error' => 0,'message' => 'SubProduct Add successfully','tabledata'=>$tabledata);
			}
			else{
				$data=array('validation_error' => 1,"message"=> $this->lang->line('Failed'),'tabledata'=>'');
			}

			return $data;
		}

		else{

			$UserLoggin = $this->loggedid = $this->session->userdata('UserUID');


			$fieldArray = array(
				"SubProductCode"=>$PostArray['SubProductCode'],
				"SubProductUID"=>$PostArray['SubProductUID'],
				"ProductUID"=>$PostArray['ProductUID'],
				// "OrderTypeUID"=>$PostArray['OrderTypeUID'],
				// "PriorityUID"=>$PostArray['PriorityUID'],
				"SubProductName"=>$PostArray['SubProductName'],
				// "ReportHeading"=>$PostArray['ReportHeading'],
				"CreatedBy"=>$UserLoggin,
				"CreatedOn"=>date('Y-m-d H:i:s'),
				"ModifiedBy"=>$UserLoggin,
				"ModifiedOn"=>date('Y-m-d H:i:s'),
				"Active"=>$PostArray['Active']

			);
			// $data1['ModuleName']='subproduct-update';
			// $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			// $data1['DateTime']=date('y-m-d H:i:s');
			// $data1['TableName']='mSubProducts';
			// $data1['UserUID']=$this->session->userdata('UserUID');                

			// $this->db->select('*');
			// $this->db->from('mSubProducts');
			// $this->db->where('SubProductUID',$PostArray['SubProductUID']);
			// $oldvalue=$this->db->get('')->row_array();


			$this->db->where(array("SubProductUID"=>$PostArray['SubProductUID']));
			$res = $this->db->update('mSubProducts', $fieldArray);

			// $this->db->select('*');
			// $this->db->from('mSubProducts');
			// $this->db->where('SubProductUID',$PostArray['SubProductUID']);
			// $newvalue = $this->db->get('')->row_array();
			// $this->Common_Model->Audittrail_diff($newvalue,$oldvalue,$data1);

			if($res){
				$tabledata = $this->GetAllSubproductDetails();
				$data=array('validation_error' => 0,"message"=> 'SubProduct Update successfully','tabledata'=>$tabledata);
			}
			else{
				$data=array('validation_error' => 1,"message"=> $this->lang->line('Failed'),'tabledata'=>'');
			}
			return $data;
		}  
	}

	function get_completedpercent($CustomerUID){
		$infoquery = $this->db->query("SELECT (CASE CustomerName WHEN '' THEN 0 ELSE 1 END) * 100 / 1 as infocompleted FROM mCustomer WHERE CustomerUID = '".$CustomerUID."'");

		$productquery = $this->db->query("SELECT SUM(Completed)/count(Completed) AS productcompleted FROM (SELECT CustomerUID,(CASE ProductUID WHEN '' THEN 0 ELSE 1 END + CASE SubProductUID WHEN '' THEN 0 ELSE 1 END) * 100 / 2 AS Completed FROM mCustomerProducts WHERE CustomerUID = '".$CustomerUID."') AS productcompleted");

		$pricingquery = $this->db->query("SELECT (CASE (SELECT EXISTS (SELECT 1 FROM mCustomer JOIN mpricing ON mCustomer.PricingUID = mpricing.PricingUID WHERE CustomerUID = '".$CustomerUID."')) WHEN '1' THEN 1 ELSE 0 END + CASE (SELECT EXISTS (SELECT 1 FROM mpricingproducts JOIN mCustomer ON mCustomer.PricingUID = mpricingproducts.PricingUID WHERE CustomerUID = '".$CustomerUID."')) WHEN '1' THEN 1 ELSE 0 END) * 100 / 2 AS pricingcompleted  ");

		$totalquery = $this->db->query("SELECT  ((SELECT (CASE CustomerName WHEN '' THEN 0 ELSE 1 END) * 100 / 1 as infocompleted FROM mCustomer WHERE CustomerUID = '".$CustomerUID."')+ IFNULL((SELECT SUM(Completed)/count(Completed) AS productcompleted FROM (SELECT CustomerUID,(CASE ProductUID WHEN '' THEN 0 ELSE 1 END + CASE SubProductUID WHEN '' THEN 0 ELSE 1 END) * 100 / 2 AS Completed FROM mCustomerProducts WHERE CustomerUID = '".$CustomerUID."') AS productcompleted),0) + (SELECT (CASE (SELECT EXISTS (SELECT 1 FROM mCustomer JOIN mpricing ON mCustomer.PricingUID = mpricing.PricingUID WHERE CustomerUID = '".$CustomerUID."')) WHEN '1' THEN 1 ELSE 0 END + CASE (SELECT EXISTS (SELECT 1 FROM mpricingproducts JOIN mCustomer ON mCustomer.PricingUID = mpricingproducts.PricingUID WHERE CustomerUID = '".$CustomerUID."')) WHEN '1' THEN 1 ELSE 0 END) * 100 / 2 AS pricingcompleted))  AS totalpercent

			");

		$workflowquery = $this->db->query("SELECT * FROM mCustomerProducts WHERE CustomerUID = '".$CustomerUID."' ");
		$priorityquery = $this->db->query("SELECT * FROM morderpriority WHERE Active = '1' ");


		$workflows = $workflowquery->result_array();
		$Priorities = $priorityquery->result_array();
		$productrowcount = count($workflows);

		$workflow_percent = 0;
		$TAT_percent = 0;

		foreach ($workflows as $key => $value) {

			$check_workflowquery = $this->db->query("SELECT mCustomerWorkflowModules.ProductUID,mCustomerWorkflowModules.SubProductUID FROM mCustomerWorkflowModules WHERE CustomerUID = '".$value['CustomerUID']."' AND mCustomerWorkflowModules.ProductUID = '".$value['ProductUID']."' AND mCustomerWorkflowModules.SubProductUID = '".$value['SubProductUID']."' ");
			if($check_workflowquery->num_rows() > 0){
				$workflow_percent += 33.33333;
			}


			$check_templateworkflowquery = $this->db->query("SELECT mcustomertemplates.ProductUID,mcustomertemplates.SubProductUID FROM mcustomertemplates WHERE CustomerUID = '".$value['CustomerUID']."' AND mcustomertemplates.ProductUID = '".$value['ProductUID']."' AND mcustomertemplates.SubProductUID = '".$value['SubProductUID']."' ");
			if($check_templateworkflowquery->num_rows() > 0){
				$workflow_percent += 33.33333;
			}

			$check_templateworkflowquery = $this->db->query("SELECT mgroupcustomers.GroupCustomerProductUID,mgroupcustomers.GroupCustomerSubProductUID FROM mgroupcustomers WHERE GroupCustomerUID = '".$value['CustomerUID']."' AND mgroupcustomers.GroupCustomerProductUID = '".$value['ProductUID']."' AND mgroupcustomers.GroupCustomerSubProductUID = '".$value['SubProductUID']."' ");
			if($check_templateworkflowquery->num_rows() > 0){
				$workflow_percent += 33.33333;
			}



			foreach ($Priorities as $Prioritieskey => $Prioritiesvalue) {
				$check_TATquery = $this->db->query("SELECT mCustomerProductTAT.ProductUID,mCustomerProductTAT.SubProductUID FROM mCustomerProductTAT WHERE CustomerUID = '".$value['CustomerUID']."' AND mCustomerProductTAT.ProductUID = '".$value['ProductUID']."' AND mCustomerProductTAT.SubProductUID = '".$value['SubProductUID']."' AND PriorityUID = '".$Prioritiesvalue['PriorityUID']."' ");
				if($check_TATquery->num_rows() > 0){
					$TAT_percent += 33.33333;
				}	
			}

		}

		$Workflowpercentage = 0;
		$TATpercentage = 0;


		if($workflow_percent){
			$Workflowpercentage = round(($workflow_percent / $productrowcount));
		}

		if($TAT_percent){
			$TATpercentage = round(($TAT_percent / $productrowcount));
		}

		$result1 = $infoquery->row_array();
		$result2 = $productquery->row_array();
		$result3 = $pricingquery->row_array();
		$result4 = $totalquery->row_array();
		$totalpercent = ($result4['totalpercent'] +  $Workflowpercentage + $TATpercentage) / 5 ;

		$result =  array_merge(array('infocompleted'=>(int)$result1['infocompleted']), array('productcompleted'=>(int)$result2['productcompleted']), array('pricingcompleted'=>(int)$result3['pricingcompleted']),array('totalpercent'=>$totalpercent),array('Workflowpercentage'=>$Workflowpercentage),array('TAT_percent'=>$TATpercentage));
		return $result;
	}

	function get_total_percent($CustomerUID){
		$totalquery = $this->db->query("SELECT  ((SELECT (CASE CustomerName WHEN '' THEN 0 ELSE 1 END) * 100 / 1 as infocompleted FROM mCustomer WHERE CustomerUID = '".$CustomerUID."')+ IFNULL((SELECT SUM(Completed)/count(Completed) AS productcompleted FROM (SELECT CustomerUID,(CASE ProductUID WHEN '' THEN 0 ELSE 1 END + CASE SubProductUID WHEN '' THEN 0 ELSE 1 END) * 100 / 2 AS Completed FROM mCustomerProducts  WHERE CustomerUID = '".$CustomerUID."') AS productcompleted),0) + (SELECT (CASE (SELECT EXISTS (SELECT 1 FROM mCustomer JOIN mpricing ON mCustomer.PricingUID = mpricing.PricingUID WHERE CustomerUID = '".$CustomerUID."')) WHEN '1' THEN 1 ELSE 0 END + CASE (SELECT EXISTS (SELECT 1 FROM mpricingproducts JOIN mCustomer ON mCustomer.PricingUID = mpricingproducts.PricingUID WHERE CustomerUID = '".$CustomerUID."')) WHEN '1' THEN 1 ELSE 0 END) * 100 / 2 AS pricingcompleted))  AS totalpercent

			");
		$totalresult = $totalquery->row_array();

		$workflowquery = $this->db->query("SELECT * FROM mCustomerProducts WHERE CustomerUID = '".$CustomerUID."' ");
		$priorityquery = $this->db->query("SELECT * FROM morderpriority WHERE Active = '1' ");


		$workflows = $workflowquery->result_array();
		$Priorities = $priorityquery->result_array();
		$productrowcount = count($workflows);

		$workflow_percent = 0;
		$TAT_percent = 0;

		foreach ($workflows as $key => $value) {

			$check_workflowquery = $this->db->query("SELECT mCustomerWorkflowModules.ProductUID,mCustomerWorkflowModules.SubProductUID FROM mCustomerWorkflowModules WHERE CustomerUID = '".$value['CustomerUID']."' AND mCustomerWorkflowModules.ProductUID = '".$value['ProductUID']."' AND mCustomerWorkflowModules.SubProductUID = '".$value['SubProductUID']."' ");
			if($check_workflowquery->num_rows() > 0){
				$workflow_percent += 33.33333;
			}


			$check_templateworkflowquery = $this->db->query("SELECT mcustomertemplates.ProductUID,mcustomertemplates.SubProductUID FROM mcustomertemplates WHERE CustomerUID = '".$value['CustomerUID']."' AND mcustomertemplates.ProductUID = '".$value['ProductUID']."' AND mcustomertemplates.SubProductUID = '".$value['SubProductUID']."' ");
			if($check_templateworkflowquery->num_rows() > 0){
				$workflow_percent += 33.33333;
			}

			$check_templateworkflowquery = $this->db->query("SELECT mgroupcustomers.GroupCustomerProductUID,mgroupcustomers.GroupCustomerSubProductUID FROM mgroupcustomers WHERE GroupCustomerUID = '".$value['CustomerUID']."' AND mgroupcustomers.GroupCustomerProductUID = '".$value['ProductUID']."' AND mgroupcustomers.GroupCustomerSubProductUID = '".$value['SubProductUID']."' ");
			if($check_templateworkflowquery->num_rows() > 0){
				$workflow_percent += 33.33333;
			}



			foreach ($Priorities as $Prioritieskey => $Prioritiesvalue) {
				$check_TATquery = $this->db->query("SELECT mCustomerProductTAT.ProductUID,mCustomerProductTAT.SubProductUID FROM mCustomerProductTAT WHERE CustomerUID = '".$value['CustomerUID']."' AND mCustomerProductTAT.ProductUID = '".$value['ProductUID']."' AND mCustomerProductTAT.SubProductUID = '".$value['SubProductUID']."' AND PriorityUID = '".$Prioritiesvalue['PriorityUID']."' ");
				if($check_TATquery->num_rows() > 0){
					$TAT_percent += 33.33333;
				}	
			}

		}


		$Workflowpercentage = 0;
		$TATpercentage = 0;

		if($workflow_percent){
			$Workflowpercentage = round(($workflow_percent / $productrowcount));
		}

		if($TAT_percent){
			$TATpercentage = round(($TAT_percent / $productrowcount));
		}



		$totalpercent = ($totalresult['totalpercent'] +  $Workflowpercentage + $TATpercentage) / 5 ;

		return  $totalpercent;
	}

	function check_pricing_exists($PricingUID,$StateUID,$CountyUID,$SubProductUID){

		$conditions = array();

		if(! empty($CountyUID)) {
			$conditions[] = "CountyUID ='".$CountyUID."'";
		}

		if(! empty($SubProductUID)) {
			$conditions[] = "SubProductUID ='".$SubProductUID."'";
		}

		if(! empty($StateUID)) {
			$conditions[] = "StateUID ='".$StateUID."'";
		}


		$WHERE = '';
		if (count($conditions) > 0) {
			$WHERE .= ' AND '.implode(' AND ', $conditions);
		}

		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM mpricingproducts WHERE PricingUID = '".$PricingUID."' $WHERE LIMIT 1) AS ifpricing");

		$WHERE = '';
		if($query->row()->ifpricing == 1)
		{
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function check_updatepricing_exists($PricingUID,$StateUID,$CountyUID,$SubProductUID,$PricingProductUID){

		$conditions = array();

		if(! empty($CountyUID)) {
			$conditions[] = "CountyUID ='".$CountyUID."'";
		}

		if(! empty($SubProductUID)) {
			$conditions[] = "SubProductUID ='".$SubProductUID."'";
		}

		if(! empty($StateUID)) {
			$conditions[] = "StateUID ='".$StateUID."'";
		}


		$WHERE = '';
		if (count($conditions) > 0) {
			$WHERE .= ' AND '.implode(' AND ', $conditions);
		}

		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM mpricingproducts WHERE PricingUID = '".$PricingUID."' AND PricingProductUID != '".$PricingProductUID."'  $WHERE LIMIT 1) AS ifpricing");

		$WHERE = '';
		if($query->row()->ifpricing == 1)
		{
			return TRUE;
		}else{
			return FALSE;
		}
	}


	function update_pricing_name($data)
	{
		$data1['ModuleName']='pricing-rename';
		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
		$data1['DateTime']=date('y-m-d H:i:s');
		$data1['TableName']='mpricing';
		$data1['UserUID']=$this->session->userdata('UserUID');                

		$this->db->select('*');
		$this->db->from('mpricing');
		$this->db->where('PricingUID',$data->PricingUID);
		$oldvalue=$this->db->get('')->row_array();


		$this->db->where('PricingUID',$data->PricingUID);
		$val = $this->db->update('mpricing',$data);
		$this->db->select('*');
		$this->db->from('mpricing');
		$this->db->where('PricingUID',$data->PricingUID);
		$newvalue = $this->db->get('')->row_array();
		$this->Common_Model->Audittrail_diff($newvalue,$oldvalue,$data1);
		return $val;
	}

	function _get_customer_details($CustomerUID){
		$this->db->select('CustomerPContactEmailID,CustomerName,CustomerPContactMobileNo,CustomerWebsite,mCustomer.Active');	
		$this->db->select('CONCAT_WS(",", NULLIF(Zipcode, ""), NULLIF(StateName, ""), NULLIF(CityName, ""), NULLIF(CountyName, "") ) AS Customerlocation,CONCAT_WS(",", NULLIF(CustomerAddress1, ""), NULLIF(CustomerAddress2, "")) AS CustomerAddress',NULL);	
		$this->db->from('mCustomer');
		$this->db->join ('mstates', 'mCustomer.CustomerStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ('mcounties', 'mCustomer.CustomerCountyUID = mcounties.CountyUID' , 'left' );  
		$this->db->join ('mcities', 'mCustomer.CustomerCityUID = mcities.CityUID' , 'left' ); 
		$this->db->where(array("mCustomer.CustomerUID"=>$CustomerUID)); 
		return $this->db->get()->row();
	}

	function _get_customerproduct_details($CustomerUID){
		$query = $this->db->query("SELECT IFNULL(ProductName,'') AS ProductName,IFNULL(SubProductName,'') AS SubProductName,IFNULL(OrderSourceName,'') AS OrderSourceName FROM mCustomerProducts JOIN mProducts ON mProducts.ProductUID = mCustomerProducts.ProductUID JOIN mSubProducts ON mSubProducts.SubProductUID = mCustomerProducts.SubProductUID LEFT JOIN mCustomerApiInfo ON (mCustomerApiInfo.CustomerUID= mCustomerProducts.CustomerUID  AND   mCustomerApiInfo.ProductUID = mProducts.ProductUID AND mSubProducts.SubProductUID = mCustomerApiInfo.SubProductUID) WHERE mCustomerProducts.CustomerUID = '".$CustomerUID."' ");
		return $query->result();
	}

	function _get_customerworkflow_details($CustomerUID){
		$query = $this->db->query('SELECT CONCAT_WS("-", NULLIF(LEFT(ProductName , 1), ""), NULLIF(SubProductName, "")) AS Products,
			IFNULL((
			SELECT GROUP_CONCAT(DISTINCT WorkflowModuleName) FROM mCustomerWorkflowModules JOIN mWorkFlowModules ON mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID JOIN mCustomerProducts ON (mCustomerProducts.CustomerUID= mCustomerWorkflowModules.CustomerUID  AND   mCustomerProducts.ProductUID = mCustomerWorkflowModules.ProductUID AND mCustomerProducts.SubProductUID = mCustomerWorkflowModules.SubProductUID) AND mCustomerWorkflowModules.CustomerUID = "'.$CustomerUID.'" 
			),"") AS Workflows,

			IFNULL((
			SELECT GROUP_CONCAT(DISTINCT WorkflowModuleName) FROM mCustomerOptionalWorkflowModules JOIN mWorkFlowModules ON mWorkFlowModules.WorkflowModuleUID = mCustomerOptionalWorkflowModules.WorkflowModuleUID JOIN mCustomerProducts ON (mCustomerProducts.CustomerUID= mCustomerOptionalWorkflowModules.CustomerUID  AND   mCustomerProducts.ProductUID = mCustomerOptionalWorkflowModules.ProductUID AND mCustomerProducts.SubProductUID = mCustomerOptionalWorkflowModules.SubProductUID) AND mCustomerOptionalWorkflowModules.CustomerUID = "'.$CustomerUID.'" 

			),"") AS OptionalWorkflows, 
			IFNULL( (
			SELECT GROUP_CONCAT(DISTINCT TemplateName) FROM mcustomertemplates JOIN mtemplates ON mtemplates.TemplateUID = mcustomertemplates.TemplateUID
			JOIN mCustomerProducts ON (mCustomerProducts.CustomerUID= mcustomertemplates.CustomerUID  AND   mCustomerProducts.ProductUID = mcustomertemplates.ProductUID AND mCustomerProducts.SubProductUID = mcustomertemplates.SubProductUID) AND mcustomertemplates.CustomerUID = "'.$CustomerUID.'" 
			),"") AS Templates

			FROM mCustomerProducts JOIN mProducts ON mProducts.ProductUID = mCustomerProducts.ProductUID JOIN mSubProducts ON mSubProducts.SubProductUID = mCustomerProducts.SubProductUID WHERE mCustomerProducts.CustomerUID = "'.$CustomerUID.'" ');
		return $query->result();
	}

	function _get_customerabstractor_details($CustomerUID){
		$query = $this->db->query("SELECT ExcludeAbstractor,CONCAT_WS(' ', NULLIF(AbstractorFirstName, ''), NULLIF(AbstractorLastName, '')) AS AbstractorNames FROM MCustomerAbstractor JOIN mabstractor ON mabstractor.AbstractorUID = MCustomerAbstractor.AbstractorUID WHERE CustomerUID = '".$CustomerUID."' ");
		return $query->result();
	}


	function get_customer_workflow_details_row($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( 'IFNULL(GROUP_CONCAT(WorkflowModuleName),"") As Workflows'); 
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->join ( 'mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID' , 'left' );
		// $this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mCustomerWorkflowModules.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerWorkflowModules.ProductUID' , 'left' );
		$this->db->where('mCustomerWorkflowModules.SubProductUID',$SubProductUID);
		$this->db->where('mCustomerWorkflowModules.ProductUID',$ProductUID);
		$this->db->where('mCustomerWorkflowModules.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->row();

	}

	function get_customer_optionalworkflow_details_row($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( 'IFNULL(GROUP_CONCAT(WorkflowModuleName),"") As Workflows' ); 
		$this->db->from ( 'mCustomerOptionalWorkflowModules' );
		$this->db->join ( 'mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerOptionalWorkflowModules.WorkflowModuleUID' , 'left' );
		// $this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mCustomerOptionalWorkflowModules.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerOptionalWorkflowModules.ProductUID' , 'left' );
		$this->db->where('mCustomerOptionalWorkflowModules.SubProductUID',$SubProductUID);
		$this->db->where('mCustomerOptionalWorkflowModules.ProductUID',$ProductUID);
		$this->db->where('mCustomerOptionalWorkflowModules.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->row();
	}

	function UpdateDefaultSubProductDetails($CustomerUID,$DefaultProductSubCode,$DefaultProductSubValue){
		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mcustomerdefaultproduct');
		$data = array(
			"CustomerUID"=>$CustomerUID,
			'DefaultProductSubCode' => $DefaultProductSubCode,
			'DefaultProductSubValue' => $DefaultProductSubValue,			
		);
		$this->db->insert('mcustomerdefaultproduct', $data);
		if($this->db->affected_rows() > 0)
		{
			return true;
		}else{
			return false;
		}
	}


	function GetCustomerDefaultSubProduct($CustomerUID){
		$this->db->select("*");
		$this->db->from("mcustomerdefaultproduct");
		$this->db->where("CustomerUID" , $CustomerUID);
		$query = $this->db->get();
		return $query->row();
	}

	public function AllProducts(){
		$this->db->select('*');
		$this->db->from('mProducts');
		return $this->db->get()->result();

	}
	public function AllSubProducts()
	{
		$this->db->select('*,mProducts.ProductName');
		$this->db->from('mSubProducts');
		$this->db->join('mProducts','mProducts.ProductUID=mSubProducts.ProductUID');
		return $this->db->get()->result();
	}
	public function ProductsDetails(){
		$this->db->select('*');
		$this->db->from('mProducts');
		$this->db->where('mProducts.Active',1);
		return $this->db->get()->result();

	}
	public function GetCustomerDet($CustomerUID){
		// $CustomerUID =$this->input->post('CustomerUID');
		$this->db->select('*');
		$this->db->from('mCustomer');
		$this->db->where('mCustomer.CustomerUID',$CustomerUID);
		return $this->db->get()->row();
	}

	function InsertmCustomerWorkflowMetrics($CustomerWorkflowMetricsdata, $DependentWorkflowModuleUID, $CustomerWorkflowMetricUID)
	{
		$this->db->trans_start(); # Starting Transaction
		if (empty($CustomerWorkflowMetricUID)) {
			$this->db->insert('mCustomerWorkflowMetrics', $CustomerWorkflowMetricsdata); # Inserting data
			$CustomerWorkflowMetricUID = $this->db->insert_id();
		} else {
			$this->db->where('CustomerWorkflowMetricUID', $CustomerWorkflowMetricUID);
			$this->db->update('mCustomerWorkflowMetrics', array('WorkflowModuleUID'=>$CustomerWorkflowMetricsdata['WorkflowModuleUID']));

			$this->db->delete('mCustomerWorkflowMetricsDependentWorkflows', array('CustomerWorkflowMetricUID' => $CustomerWorkflowMetricUID));
		}	

		foreach ($DependentWorkflowModuleUID as $key => $value) 
		{
			$data[] = array(
				'CustomerWorkflowMetricUID' => $CustomerWorkflowMetricUID,
				'DependentWorkflowModuleUID' => $value
			);
		}
		$this->db->insert_batch('mCustomerWorkflowMetricsDependentWorkflows', $data);
		if ($this->db->affected_rows() < 0) {
			$this->db->delete('mCustomerWorkflowMetrics', array('CustomerWorkflowMetricUID' => $CustomerWorkflowMetricUID));
		}
		$this->db->trans_complete(); # Completing transaction

		if ($this->db->trans_status() === FALSE) {
		    # Something went wrong.
		    $this->db->trans_rollback();
		    return FALSE;
		} 
		else {
		    # Everything is Perfect. 
		    # Committing data to the database.
		    $this->db->trans_commit();
		    return $CustomerWorkflowMetricUID;
		}
	}

	function GetCustomerWorkflowMetrics($CustomerUID) {
		$this->db->select('*');
		$this->db->from('mCustomerWorkflowMetrics');
		$this->db->where('CustomerUID', $CustomerUID);
		$this->db->order_by('Priority', 'ASC');
		$query = $this->db->get()->result_array();
		return $query;
	}

	function GetmCustomerWorkflowMetricsDependentWorkflows($CustomerWorkflowMetricUID) {
		$this->db->select ( 'GROUP_CONCAT(DependentWorkflowModuleUID) as DependentWorkflowModuleUID' ); 
		$this->db->from ('mCustomerWorkflowMetricsDependentWorkflows');
		$this->db->where('CustomerWorkflowMetricUID',$CustomerWorkflowMetricUID);
		$query = $this->db->get()->row();
		return $query->DependentWorkflowModuleUID;
	}

	function DeleteLoanPrioritizationMetrics($CustomerUID, $CustomerWorkflowMetricUID) {
		$this->db->trans_start();
		$this->db->delete('mCustomerWorkflowMetrics', array('CustomerUID' => $CustomerUID,'CustomerWorkflowMetricUID' => $CustomerWorkflowMetricUID));
		$this->db->delete('mCustomerWorkflowMetricsDependentWorkflows', array('CustomerWorkflowMetricUID' => $CustomerWorkflowMetricUID));
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		        $this->db->trans_rollback();
		        return false;
		}
		else
		{
		        $this->db->trans_commit();
		        return true;
		}
	}

	function UpdateWorkflowModuleUIDLoanPrioritizationMetrics($CustomerUID, $WorkflowModuleUID, $CustomerWorkflowMetricUID) {
		$this->db->where(array('CustomerWorkflowMetricUID'=>$CustomerWorkflowMetricUID, 'CustomerUID'=>$CustomerUID));
		$this->db->update('mCustomerWorkflowMetrics', array('WorkflowModuleUID'=>$WorkflowModuleUID));
		return $this->db->affected_rows();
	}

	function milestonematricsdetails($CustomerUID, $ProductUID) {

		$this->db->select('mCustomerMilestones.*,mProducts.ProductCode');
		$this->db->from('mCustomerMilestones');
		$this->db->join('mCustomerWorkflowModules','mCustomerMilestones.CustomerUID=mCustomerWorkflowModules.CustomerUID
AND mCustomerMilestones.ProductUID=mCustomerWorkflowModules.ProductUID
AND mCustomerMilestones.WorkflowModuleUID=mCustomerMilestones.WorkflowModuleUID OR mCustomerMilestones.WorkflowModuleUID IS NULL', 'left');
		$this->db->join('mProducts','mCustomerWorkflowModules.ProductUID=mProducts.ProductUID');
		$this->db->where(array('mCustomerMilestones.CustomerUID'=>$CustomerUID,'mCustomerMilestones.ProductUID'=>$ProductUID));
		$this->db->group_by('mCustomerMilestones.CustomerUID');
		$this->db->group_by('mCustomerMilestones.ProductUID');
		$this->db->group_by('mCustomerMilestones.WorkflowModuleUID');
		$this->db->group_by('mCustomerMilestones.MileStoneUID');
		$result1 = $this->db->get()->result_array();
		$select_workfows = array_column($result1, 'WorkflowModuleUID');
		// Filtering the array
		$select_workfows_filter = array_filter($select_workfows); 
// print_r($select_workfows);exit();

		$this->db->select('mCustomerWorkflowModules.CustomerUID,mCustomerWorkflowModules.ProductUID,mCustomerWorkflowModules.WorkflowModuleUID,mProducts.ProductCode');
		$this->db->from('mCustomerWorkflowModules');
		$this->db->join('mProducts','mCustomerWorkflowModules.ProductUID=mProducts.ProductUID');
		$this->db->where(array('mCustomerWorkflowModules.CustomerUID'=>$CustomerUID,'mCustomerWorkflowModules.ProductUID'=>$ProductUID));
		if (!empty($select_workfows_filter)) {
			$this->db->where_not_in('mCustomerWorkflowModules.WorkflowModuleUID',$select_workfows_filter);
		}
		$result2 = $this->db->get()->result_array();
		// echo "<pre>";
		// print_r($result2);
		// exit();

		// echo "<pre>";
		// print_r(array_merge($result1,$result2));
		// exit();

		return array_merge($result1,$result2);
	}

	//Customer workflow milestone update
	function NUpdateCustomerWorkflowMilestone($CustomerUID, $ProductUID, $WorkflowUID, $MilestoneUID, $OldMilestoneUID) {		
		$this->db->where(array('CustomerUID' => $CustomerUID, 'ProductUID' => $ProductUID, 'WorkflowModuleUID' => $WorkflowUID, 'MileStoneUID' => $OldMilestoneUID));
		$this->db->update('mCustomerMilestones', array('MileStoneUID' => $MilestoneUID));	
		return $this->db->affected_rows();
	}

	//Customer workflow milestone update
	function DeleteCustomerWorkflowMilestone($CustomerUID, $ProductUID, $WorkflowUID, $MilestoneUID, $OldMilestoneUID) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'ProductUID' => $ProductUID, 'WorkflowModuleUID' => $WorkflowUID, 'MileStoneUID' => $OldMilestoneUID));
		$this->db->delete('mCustomerMilestones');
		return $this->db->affected_rows();
	}

	//Customer workflow state update
	function UpdateCustomerWorkflowState($CustomerUID, $WorkflowUID, $State) {		
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		$this->db->update('mCustomerWorkflowModules', array('State' => $State));	
		return $this->db->affected_rows();
	}

	//Customer workflow LoanType update
	function UpdateCustomerWorkflowLoanType($CustomerUID, $WorkflowUID, $LoanTypeName) {		
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		$this->db->update('mCustomerWorkflowModules', array('LoanTypeName' => $LoanTypeName));	
		return $this->db->affected_rows();
	}

	//Customer workflow PropertyType update
	function UpdateCustomerWorkflowPropertyType($CustomerUID, $WorkflowUID, $PropertyType) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('PropertyType' => $PropertyType));	
	}

	//Customer workflow ChecklistSequence update
	function UpdateCustomerWorkflowChecklistSequence($CustomerUID, $WorkflowUID, $ChecklistSequence) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('ChecklistSequence' => $ChecklistSequence));	
	}

	//Customer workflow MilestoneUID update
	function UpdateCustomerWorkflowMilestoneUID($CustomerUID, $WorkflowUID, $MilestoneUID) {	
		
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		$this->db->update('mCustomerWorkflowModules', array('MilestoneUID' => $MilestoneUID));	
		return $this->db->affected_rows();
	}

	public function GetProductDetails() {
		$this->db->select('*');
		$this->db->from('mProducts');
		if(!in_array($this->RoleType, $this->config->item('Super Admin'))) {
			$this->db->where('CustomerUID',$this->parameters['DefaultClientUID']);
		}
		$this->db->where('Active',STATUS_ONE);
		return $this->db->get()->result();
	}

	//Update order workflow duration
	function UpdateCustomerWorkflowOrderHighlightDuration($CustomerUID, $WorkflowUID, $OrderHighlightDuration) {
		$this->db->where(array('CustomerUID' => $CustomerUID, 'WorkflowModuleUID' => $WorkflowUID));
		return $this->db->update('mCustomerWorkflowModules', array('OrderHighlightDuration' => $OrderHighlightDuration));	
	}
	
}?>

