<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Profile_Model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
	}

	function GetUserDetails(){
		$this->db->select("*,mUsers.Active");
		$this->db->from('mUsers');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID','left');
		$query = $this->db->get();
		return $query->result();
	}

	function GetRoleDetails(){
		$this->db->select('*');
		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mRole');
		return $query->result();
	}

	function GetUserDetailsById($UserUID)
	{
		$this->db->select("*,mUsers.Active");
		$this->db->from('mUsers');
		$this->db->join('mRole','mUsers.RoleUID=mRole.RoleUID','left');
		$this->db->where(array("mUsers.UserUID"=>$UserUID));
		$query = $this->db->get();
		return $query->row();
	}

	function GetRoleTypeDetails($RoleUID)
	{
		$this->db->select("*");
		$this->db->from('mRole');
		$this->db->where(array("mRole.RoleUID"=>$RoleUID));  
		$query = $this->db->get();
		return $query->row();
	}

	function UpdateUserDetails($PostArray,$postfile)
	{
		
		$UserLoggin = $this->loggedid = $this->session->userdata('UserUID');
		$fieldArray = array(
			"UserUID"=>$PostArray['UserUID'],
			"UserName"=>$PostArray['UserName'],
			"LoginID"=>$PostArray['LoginID'],
			"EmailID"=>$PostArray['EmailID'],
			"ContactNo"=>$PostArray['ContactNo'],
			"FaxNo"=>$PostArray['FaxNo'], 
			"ModifiedByUserUID"=>$UserLoggin,
			"ModifiedOn"=>date('Y-m-d H:i:s')
		);

		if($postfile){
			$fieldArray['Avatar'] = $postfile; 
		}
		
		$this->db->where(array("UserUID"=>$PostArray['UserUID']));
		$res = $this->db->update('mUsers', $fieldArray);
	}

	public function insertimage($DocumentFileName,$title,$Path)
	{ 

		$useruid = $this->session->userdata('UserUID');
		$this->db->set('filename',$DocumentFileName);             
		$this->db->set('ImagePath',$Path);
		$this->db->set('title',$title);
		$this->db->where('UserUID',$useruid);
		$this->db->update('mUsers'); 
		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}

	}


	/*Abstarctor profile part*/

	function GetAbstractorDetails($AbstractorUID)
	{
		$this->db->select("*,mabstractor.Active AS MStatusActive,mabstractor.AbstractorUID AS AbstractorUIDs");
		$this->db->from('mabstractor');
		if(!empty($AbstractorUID))
		{
			$this->db->where(array("mabstractor.AbstractorUID"=>$AbstractorUID));     
			$query = $this->db->get();
			return $query->row();
		}
		$this->db->join('mcounties','mcounties.CountyUID = mabstractor.CountyUID','left');
		$this->db->join('mcities','mcities.CityUID = mabstractor.CityUID','left');
		$this->db->join('mstates','mstates.StateUID = mabstractor.StateUID','left');
		$query = $this->db->get();
		return $query->result();
	}


	function GetAbstractorDocument($AbstractorUID)
	{
		$this->db->select ( 'mabstractordoc.*, mUsers.UserUID, mUsers.UserName' ); 
		$this->db->from ( 'mabstractordoc' );
		$this->db->join ( 'mUsers', 'mabstractordoc.created_by = mUsers.UserUID' , 'left' );
		$this->db->where(array("mabstractordoc.AbstractorUID"=>$AbstractorUID));
		$query = $this->db->get();
		return $query->result();
	}

	function Getdocumenttypes() 
	{
		$query = $this->db->query("SELECT * FROM mdocumenttypes Order By FIELD(DOCUMENTTYPENAME, 'Property Info', 'Deeds', 'Mortgages', 'Judgment', 'Liens', 'Taxes', 'Others')");
		return $query->result();
	}

	function GetSubdocumenttypes() 
	{
		$query = $this->db->query("SELECT * FROM msubdocumenttypes where DocumentTypeUID = 9");
		return $query->result();
	}

	function get_abstractor_login(){
		$UserUID = $this->session->userdata('UserUID');
		$query = $this->db->query("SELECT `RoleType`, `RoleName`,`mUsers`.`AbstractorUID` AS AbstractorUID FROM (`mUsers`) INNER JOIN `mRole` ON `mUsers`.`RoleUID` = `mRole`.`RoleUID` WHERE `mUsers`.`UserUID` = $UserUID AND RoleType='15'");
		return $query->row();
	}

	function check_approval_exists($AbstractorUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mabstractorapprovals' );
		$this->db->where(array("AbstractorUID"=>$AbstractorUID,'IsApproved'=>0));
		$query = $this->db->get();
		return $query->row();
	}

	function SaveAbstractor_basicdetails($AbstractorDetails)
	{


		/*updating basic details without approval*/

		$fieldList = array(

			"AbstractorFirstName"=>$AbstractorDetails['FirstName'],
			"AbstractorLastName"=>$AbstractorDetails['LastName'],
			"Mobile"=>$AbstractorDetails['ContactNumber'],
			"Email"=>$AbstractorDetails['AbstractorEmail'],
			"PaypalEmailID"=>$AbstractorDetails['AbstractorPaypal'],

			"AddressLine1" => $AbstractorDetails['AbstractorAddress1'],
			"AddressLine2" => $AbstractorDetails['AbstractorAddress2'],
			"ZipCode"=>$AbstractorDetails['AbstractorZipCode'],
			"CityUID"=>$AbstractorDetails['AbstractorCityUID'],
			"CountyUID"=>$AbstractorDetails['AbstractorCountyUID'],
			"StateUID"=>$AbstractorDetails['AbstractorStateUID'],

			"OfficePhoneNo"=>$AbstractorDetails['OfficePhoneNo'],
			"OfficeExt"=>$AbstractorDetails['OfficeExt'],
			"BeeperPhoneNo"=>$AbstractorDetails['BeeperPhoneNo'],
			"BeeperPhoneExt"=>$AbstractorDetails['BeeperPhoneExt'],
			"CarPhoneNo"=>$AbstractorDetails['CarPhoneNo'],
			"HomePhoneNo"=>$AbstractorDetails['HomePhoneNo'],
			"CourtHousePhoneNo"=>$AbstractorDetails['CourtHousePhoneNo'],
			"CourtHouseExt"=>$AbstractorDetails['CourtHouseExt'],
			"Comments"=>$AbstractorDetails['Comments'],
			"ALTVendorNo"=>$AbstractorDetails['ALTVendorNo'],
			"Contact"=>$AbstractorDetails['Contact'],
			"BankAccountNo"=>$AbstractorDetails['BankAccountnumber'],
			"BankName"=>$AbstractorDetails['BankName'],
			"BankAddress"=>$AbstractorDetails['BankAddress'],
			"RoutingNum"=>$AbstractorDetails['RoutingNumber'],
			"BankAccountType"=>$AbstractorDetails['BankAccountType'],
		);



		$this->db->trans_begin();

		$this->db->where('AbstractorUID', $AbstractorDetails['AbstractorUID']);
		$this->db->update('mabstractor', $fieldList);

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

	function SaveAbstractor_expirydetails($AbstractorDetails,$Approvalrequired)
	{


		/*For approval*/
			$ApprovalUID = '';
		if($Approvalrequired){


			$LicenseExpiryDate = ($AbstractorDetails['LicenseExpiryDate'] == NULL) ? NULL : Date('Y-m-d',strtotime($AbstractorDetails['LicenseExpiryDate']));

			$ExpiryDate = ($AbstractorDetails['ExpiryDate'] == NULL) ? NULL : Date('Y-m-d',strtotime($AbstractorDetails['ExpiryDate']));

			if($AbstractorDetails['EOStatus'] == '2'){
				$ExpiryDate = NULL;
				$AbstractorDetails['Policy'] = NULL;
				$AbstractorDetails['Insurance'] = NULL;
			}


			$fieldArray = array(
				"AbstractorUID"=>$AbstractorDetails['AbstractorUID'],
				"LicenseNo"=>$AbstractorDetails['LicenseNo'],
				"LicenseExpiryDate"=>$LicenseExpiryDate,
				"EAndOStatus"=>$AbstractorDetails['EOStatus'],
				"EAndORecoveryAmt"=>$AbstractorDetails['EORecoveryAmount'],
				"EAndOExpiryDate"=>$ExpiryDate,
				"EAndOPolicyLimit"=>$AbstractorDetails['Policy'],
				"EAndOInsurance	"=>$AbstractorDetails['Insurance'],
				"FaxNo"=>$AbstractorDetails['FaxNo'],
				"IsApproved"=>0,
				"CreatedBy"=>$this->loggedid,
				"CreatedDate"=>date('y-m-d H:i:s'),
				"ModifiedBy"=>$this->loggedid,
				"LastModifiedDateTime"=>date('y-m-d H:i:s'),
			);

			$approvalexists = $this->check_approval_exists($AbstractorDetails['AbstractorUID']);
			if(count($approvalexists) > 0){

				$ApprovalUID = $approvalexists->ApprovalUID;
				$this->db->where('AbstractorUID', $AbstractorDetails['AbstractorUID']);
				$this->db->where('ApprovalUID', $ApprovalUID);
				$this->db->update('mabstractorapprovals', $fieldArray);

			}else{

				$this->db->insert('mabstractorapprovals', $fieldArray);
				$ApprovalUID = $this->db->insert_id();
			}

		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return $ApprovalUID;
		}
		else
		{
			$this->db->trans_commit();
			return $ApprovalUID;
		}
		
	}


	function SaveAbstractordoc($AbstractorUID,$DocumentName,$Path,$IsContractorPackage,$ApprovalUID)
	{

		$fieldArray = array(
			"AbstractorUID"=>$AbstractorUID,
			"ApprovalUID"=>$ApprovalUID,
			"document"=>$DocumentName,
			"url"=>$Path,
			"IsContractorPackage"=>$IsContractorPackage,
			"created_by"=>$this->loggedid,
			"CreatedDate"=>date('y-m-d H:i:s'),
			"modified_by"=>$this->loggedid,
			"IsApproved"=>0,
			"last_modified"=>date('y-m-d H:i:s'),
		);

		$this->db->insert('mabstractordocapprovals', $fieldArray);

		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function DeleteDocument($AbstractorUID,$DocumentName,$Path)
	{
		$this->db->where(array("mabstractordoc.AbstractorUID"=>$AbstractorUID,"document"=>$DocumentName,"url"=>$Path));    
		$this->db->delete('mabstractordoc');

	}


	function get_abstractor($AbstractorUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mabstractor' );
		$this->db->where("AbstractorUID",$AbstractorUID);
		$query = $this->db->get();
		return $query->row_array();
	}

	function GetUserProfile($UserID){
		$this->db->select("mUsers.Active , mUsers.ProfileColor , mUsers.ProfileBackground ,  mUsers.SidebarBackground , mUsers.SidebarActive , mUsers.SidebarBackgroundActive");
		$this->db->from('mUsers');
		$this->db->where("mUsers.UserUID" , $UserID);
		$query = $this->db->get();
		return $query->row();
	}

	function updateProfileSettings($data , $UserID){
		$this->db->where('UserUID',$UserID);
		$this->db->update('mUsers',$data);
		return 1;
		
	}

}
?>
