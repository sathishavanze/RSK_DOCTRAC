<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CustomerChecklistReport_Model extends MY_Model {

	function __construct()
	{ 
		parent::__construct();
	}
	
	//Customer Workflow
	function GetCustomerWorkflows(){
		$this->db->select ( 'mWorkFlowModules.WorkflowModuleUID, mWorkFlowModules.SystemName' ); 
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->join ( 'mWorkFlowModules', 'mWorkFlowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID' , 'left' );
		$this->db->where('mCustomerWorkflowModules.CustomerUID',$this->session->userdata('DefaultClientUID'));
		$this->db->group_by('mCustomerWorkflowModules.WorkflowModuleUID');
		$this->db->order_by('mCustomerWorkflowModules.Position','ASC');
		$this->db->order_by('mCustomerWorkflowModules.WorkflowModuleUID','ASC');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	//Checklist
	function Checklist(){
		$this->db->select ('DocumentTypeUID, DocumentTypeName'); 
		$this->db->from ('mDocumentType');
		$this->db->order_by('DocumentTypeUID','ASC');		
		$this->db->where(array('Active'=>'1','CustomerUID'=>$this->session->userdata('DefaultClientUID')));
		$query = $this->db->get();
		return $query->result_array();
	}

	//Reports
	function ReportsDetails($ReportUID = FALSE){
		if ($ReportUID === FALSE)
        {	
			$this->db->select('ReportUID, ReportName, WorkflowModuleUID, Active'); 
			$this->db->from('mReports');
			$this->db->order_by('ReportName','ASC');
			$this->db->where(array('ClientUID'=>$this->session->userdata('DefaultClientUID')));
			$query = $this->db->get();
			return $query->result_array();
        }	
        $this->db->select('ReportUID, ReportName'); 
		$this->db->from('mReports');
		$this->db->order_by('ReportName','ASC');
		$this->db->where(array('ReportUID'=>$ReportUID,'Active'=>'1'));
		$query = $this->db->get();
		return $query->row_array();	
	}

	//Insert Report
	function InsertReports($ReportName, $WorkflowModuleUID) {
		$data = array(
			'ReportName'=>$ReportName,
			'ClientUID'=>$this->session->userdata('DefaultClientUID')
		);
		//Check report name already exist or not
		$query = $this->db->get_where('mReports', $data);

		$count = $query->num_rows(); //counting result from query

		if ($count === 0) {
			if ($WorkflowModuleUID == 'empty') {
				$WorkflowModuleUID = null;
			}
			$data['WorkflowModuleUID'] = $WorkflowModuleUID;
            $res = $this->db->insert('mReports', $data);
            if($res){
				$data=array('validation_error' => 0,'message' => 'Report Added Successfully.','type'=>'success');
			}
			else{
				$data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
			}
			return $data;
        } else {
        	$data=array('validation_error' => 1,'message'=> 'The Report Name Already Exist!.','type'=>'danger');
        	return $data;
        }		
		
	}

	//Update Report
	function UpdateReports($ReportUID, $ReportName, $WorkflowModuleUID) {
		$where = array(
			'ReportName'=>$ReportName,
			'ClientUID'=>$this->session->userdata('DefaultClientUID')
		);
		$this->db->select('*');
		$this->db->from('mReports');
		$this->db->where($where);	
		$this->db->where_not_in('ReportUID', $ReportUID);	
		//Check report name already exist or not
		$query = $this->db->get();

		$count = $query->num_rows(); //counting result from query

		if ($count === 0) {
			if ($WorkflowModuleUID == 'empty') {
				$WorkflowModuleUID = null;
			}
			$this->db->where('ReportUID', $ReportUID);
			$this->db->update('mReports', array('ReportName'=>$ReportName,'WorkflowModuleUID'=>$WorkflowModuleUID));			
            $data=array('validation_error' => 0,'message' => 'Report Updated Successfully.','type'=>'success');
			return $data;
        } else {
        	$data=array('validation_error' => 1,'message'=> 'The Report Name Already Exist!.','type'=>'danger');
        	return $data;
        }		
		
	}

	function DeleteReportDetails($ReportUID,$Active) {
		$this->db->where('ReportUID',$ReportUID);
		$this->db->update('mReports', array('Active'=>$Active));
		if($this->db->affected_rows()){
			$data=array('validation_error' => 0,'message' => 'Report Status Changes Successfully.','type'=>'success');
		}
		else{
			$data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
		}
		return $data;
	}

	//Check group name already exist or not
	function checkgroupnameexist($GroupName,$reportfieldsdata) {		
		$this->db->select('*');
		$this->db->from('mReportFields');
		$this->db->join('mReportsGroups','mReportsGroups.GroupUID = mReportFields.GroupUID', 'left');
		$this->db->where(array('mReportsGroups.GroupName'=>$GroupName, 'mReportFields.ReportUID'=>$reportfieldsdata['ReportUID'], 'mReportFields.Active'=>'1'));
		$query = $this->db->get();

		return $count = $query->num_rows(); //counting result from query
	}

	//Insert Group
	function insertGroup($GroupName,$reportfieldsdata) {

		// check group name already exist or not
		$count = $this->checkgroupnameexist($GroupName,$reportfieldsdata);				

		if ($count === 0) {
			$this->db->trans_start(); # Starting Transaction
			$groupdata = array(
				'GroupName'=>$GroupName
			);
            $this->db->insert('mReportsGroups', $groupdata);
            //get last insert id
            $GroupUID = $this->db->insert_id();
            $reportfieldsdata['GroupUID'] = $GroupUID;

            // insert report fields data
            $this->db->insert('mReportFields', $reportfieldsdata);
        	//get last insert id
        	$ReportFieldUID = $this->db->insert_id();

            $this->db->trans_complete(); # Completing transaction

            if ($this->db->trans_status() === FALSE) {
			    # Something went wrong.
			    $this->db->trans_rollback();
			    $data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
			    return $data;
			}  else {
			    # Everything is Perfect. 
			    # Committing data to the database.
			    $this->db->trans_commit();
			    $data=array('validation_error' => 0,'message' => 'Group Details Saved Successfully.','type'=>'success','ReportFieldUID'=>$ReportFieldUID,'GroupUID'=>$GroupUID,'WorkflowUID' => $reportfieldsdata['WorkflowUID'],'DocumentTypeUID' => $reportfieldsdata['DocumentTypeUID']);
			    return $data;
			}
	            	            
        } else {
        	$data=array('validation_error' => 1,'message'=> 'The Group Name Already Exist!.','type'=>'danger');
        	return $data;
        }		
		
	}

	function insertreportfields($reportfieldsdata) {
		//Check if checklist is checked
		if ($reportfieldsdata['IsChecklist'] == 0) {
			$counts = 0;
		} else {
	        // check workflow and checklist already exist
			$counts = $this->checkworkflowchecklistexist($reportfieldsdata);
    	}

        if ($counts === 0) {
        	$res = $this->db->insert('mReportFields', $reportfieldsdata);
        	//get last insert id
            $ReportFieldUID = $this->db->insert_id();

            if($res){
				$data=array('validation_error' => 0,'message' => 'Group Details Saved Successfully.','type'=>'success','ReportFieldUID'=>$ReportFieldUID,'WorkflowUID' => $reportfieldsdata['WorkflowUID'],'DocumentTypeUID' => $reportfieldsdata['DocumentTypeUID']);
			}
			else{
				$data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
			}
			return $data;
        }else {
              $data=array('validation_error' => 1,'message'=> 'This workflow and checklist is already mapped to this group!.','type'=>'danger');
              return $data;
        }
	}

	//check workflow and checklist already mapped to this group
	function checkworkflowchecklistexist($reportfieldsdata,$ReportFieldUID = FALSE) {		
        $this->db->select('*');
        $this->db->from('mReportFields');
        $this->db->where(array(
        	'ReportUID'=>$reportfieldsdata['ReportUID'],
        	'GroupUID'=>$reportfieldsdata['GroupUID'],
        	'WorkflowUID'=>$reportfieldsdata['WorkflowUID'],
        	'DocumentTypeUID'=>$reportfieldsdata['DocumentTypeUID'],
        	'ChecklistOption'=>$reportfieldsdata['ChecklistOption'],
        	'Active'=>'1'
        ));
        if ($ReportFieldUID != FALSE) {
        	$this->db->where_not_in('ReportFieldUID',$ReportFieldUID);
        }
        $query = $this->db->get();
        return $counts = $query->num_rows(); //counting result from query
	}

	function updateGroup($ReportFieldUID,$OldWorkflowUID,$OldDocumentTypeUID,$reportfieldsdata) {
		if (!empty($reportfieldsdata['WorkflowUID']) && !empty($reportfieldsdata['DocumentTypeUID']) && $OldWorkflowUID == $reportfieldsdata['WorkflowUID'] && $OldDocumentTypeUID == $reportfieldsdata['DocumentTypeUID']) {
			// check workflow and checklist already exist
			$counts = $this->checkworkflowchecklistexist($reportfieldsdata,$ReportFieldUID);
			if($counts == 0) {
				$this->db->where(array('ReportFieldUID'=>$ReportFieldUID));
				$this->db->update('mReportFields', $reportfieldsdata);
				$data=array('validation_error' => 0,'message' => 'Group Details Saved Successfully.','type'=>'success','WorkflowUID' => $reportfieldsdata['WorkflowUID'],'DocumentTypeUID' => $reportfieldsdata['DocumentTypeUID']);
				return $data;
			} else {
				$data = array('validation_error' => 1,'message'=> 'This workflow and checklist is already mapped to this group!.','type'=>'danger');
	            return $data;
			}
		} else {
			if ($reportfieldsdata['IsChecklist'] == 0) {
				$counts = 0;
			} else {
				// check workflow and checklist already exist
				$counts = $this->checkworkflowchecklistexist($reportfieldsdata,$ReportFieldUID);
		    }

	        if ($counts === 0) {
				$this->db->where(array('ReportFieldUID'=>$ReportFieldUID));
				$this->db->update('mReportFields', $reportfieldsdata);
				$data = array('validation_error' => 0,'message' => 'Group Details Saved Successfully.','type'=>'success','WorkflowUID' => $reportfieldsdata['WorkflowUID'],'DocumentTypeUID' => $reportfieldsdata['DocumentTypeUID']);
				return $data;
	        }else {
	              $data = array('validation_error' => 1,'message'=> 'This workflow and checklist is already mapped to this group!.','type'=>'danger');
	              return $data;
	        }
		}
	}

	function deletereportfieldsdata($ReportFieldUID) {
		$this->db->where(array('ReportFieldUID'=>$ReportFieldUID));
		$this->db->update('mReportFields', array('Active'=>'0'));
		$res = $this->db->affected_rows();
		if($res){
			$data=array('validation_error' => 0,'message' => 'Group Field Deleted Successfully.','type'=>'success');
		} else{
			$data=array('validation_error' => 1,'message'=> 'Something went wrong!.','type'=>'danger');
		}
		return $data;
	}

	function GetGroupsDetails($ReportUID) {
		$this->db->select('mReportsGroups.GroupUID,mReportsGroups.GroupName');
		$this->db->from('mReportsGroups');
		$this->db->join('mReportFields','mReportFields.GroupUID = mReportsGroups.GroupUID', 'left');
		$this->db->where(array('mReportFields.ReportUID'=>$ReportUID, 'mReportFields.Active'=>'1'));
		$this->db->group_by('mReportsGroups.GroupName');
		$query = $this->db->get()->result_array();
		return $query;
		echo "<pre>";
		print_r($query);exit();
	}

	function GetReportGroupDetails($ReportUID,$GroupUID) {
		$this->db->select('mReportFields.ReportFieldUID,mReportFields.HeaderName,mReportFields.IsChecklist,mReportFields.ColumnName,mReportFields.WorkflowUID,mReportFields.DocumentTypeUID,mReportFields.ChecklistOption,mReportFields.ChecklistOption');
		$this->db->from('mReportFields');
		$this->db->join('mReportsGroups','mReportsGroups.GroupUID = mReportFields.GroupUID', 'left');
		$this->db->where(array('mReportFields.ReportUID'=>$ReportUID, 'mReportsGroups.GroupUID'=>$GroupUID, 'mReportFields.Active'=>'1'));
		$this->db->order_by('Position','ASC');
		$query = $this->db->get()->result_array();
		return $query;
	}

	function UpdateGroupDetails($ReportUID,$GroupUID,$GroupName) {
		//Check group name already exist or not
		$this->db->select('*');
		$this->db->from('mReportFields');
		$this->db->join('mReportsGroups','mReportsGroups.GroupUID = mReportFields.GroupUID', 'left');
		$this->db->where(array('mReportsGroups.GroupName'=>$GroupName, 'mReportFields.ReportUID'=>$ReportUID, 'mReportFields.Active'=>'1'));
		$this->db->where_not_in('mReportsGroups.GroupUID',$GroupUID);
		$query = $this->db->get();

		$count = $query->num_rows(); //counting result from query

		if ($count === 0) {
			$this->db->where(array('GroupUID'=>$GroupUID));
			$this->db->update('mReportsGroups', array('GroupName'=>$GroupName));
			$res = $this->db->affected_rows();
			if($res){
				$data=array('validation_error' => 0,'message' => 'Group Name Updated Successfully.','type'=>'success');
			} else{
				$data=array('validation_error' => 0,'message' => 'No changes were made to the group name.','type'=>'success');
			}
			return $data;
		} else {
        	$data=array('validation_error' => 1,'message'=> 'The Group Name Already Exist!.','type'=>'danger');
        	return $data;
        }
	}

	function DeleteGroupDetails($ReportUID,$GroupUID) {
		$this->db->where(array('ReportUID'=>$ReportUID,'GroupUID'=>$GroupUID));
		$this->db->update('mReportFields',array('Active'=>'0'));
		$this->db->where(array('GroupUID'=>$GroupUID));
		$this->db->update('mReportsGroups',array('Active'=>'0'));
	}

	function GetStandardColumns() {
	    $result = $this->db->list_fields('tOrderImport');
	    //delete first element from array
	    array_shift($result);
	    //concodinate table name and value as array key
	    $tOrderImport_fields = array();
	    foreach ($result as $key => $value) {
	    	$tOrderImport_fields['tOrderImport.'.$value] = $value;
	    }
	    //torders fields
	    $tOrders_fields = $this->config->item('tOrders_fields');
	    //merge two array
	    return $fields = array_merge($tOrders_fields,$tOrderImport_fields);
	}

	/**
	*Function fetch dynamic checklist dropdown fields
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Wednesday 29 April 2020
	*/
	function get_dynamicchecklistfields()
	{
		$this->db->select('*')->from('mCustomerField');
		$this->db->join('mFields', 'mFields.FieldUID = mCustomerField.FieldUID');
		$this->db->where('mCustomerField.CustomerUID',$this->session->userdata('DefaultClientUID'));
		return $this->db->get()->result();
	}


}
?>

