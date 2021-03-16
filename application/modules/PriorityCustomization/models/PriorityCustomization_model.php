<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class PriorityCustomization_model extends MY_Model
{
   function __construct()
   {
      parent::__construct();
   }
   /* 
   * fucntion for Insert and update Priority
   * author vishnupriya(vishnupriya.a@avanzegroup.com)
	* since Date:18-07-2020
   */
   function GetPriorityTpe($post)
   {
      $Active = $data['Active'] = isset($post['Active']) ? 1 : 0;
      /* insert array */
      $insert = array('PriorityName' => $post['PriorityName'], 'PriorityUID' => $post['PriorityUID'], 'HelpText' => $post['HelpText'], 'ClientUID' => $post['CustomerUID'], 'Active' => 1);
      /* update array */
      $update = array('PriorityName' => $post['PriorityName'], 'PriorityUID' => $post['PriorityUID'], 'HelpText' => $post['HelpText'], 'Active' => $Active);
      /* Insert */
      if (isset($post['PriorityUID'])) {
         $this->db->where(array("PriorityUID" => $post['PriorityUID'], 'ClientUID' => $post['CustomerUID']));
         $this->db->update('mPriorityReport', $update);
         $this->priorityLogsHistory($this->uri->segment(1), $post['PriorityName'] . ' Updated',  Date('Y-m-d H:i:s'));
      } else {
         $this->db->insert('mPriorityReport', $insert);
         $this->priorityLogsHistory($this->uri->segment(1), $post['PriorityName'] . ' Added',  Date('Y-m-d H:i:s'));
      }

      if ($this->db->affected_rows() > 0) {
         if ($post['priority'][0]['WorkflowModuleUID']) {
            $PriorityUID = $this->getPriorityUID($post['PriorityName'], $post['CustomerUID']);
            $this->addWorkflow($post, $PriorityUID);
         }
         return 1;
      } else {
         return 0;
      }
   }
   /*
	*Get Count of record for data table
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:18-07-2020
	 */
   function priorityLogsHistory($module, $Description, $LogsDateTime)
   {
      $LogsInsert = array('Module' => $module, 'Description' => $Description, 'UserID' => $this->loggedid, 'LogDateTime' => $LogsDateTime);
      $this->db->insert('mAuditLog', $LogsInsert);
      if ($this->db->affected_rows() > 0) {
         return 1;
      } else {
         return 0;
      }
   }
   /* 
	*Get Count of record for data table
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
   function count_all()
   {
      $this->db->select("1");
      $this->filterQuery();

      $this->db->where('mPriorityReport.ClientUID', $this->parameters['DefaultClientUID']);
      $query = $this->db->count_all_results();
      return $query;
   }
   /* 
	*Get filtered count for data table
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
   function count_filtered($post)
   {
      $this->db->select("1");
      $this->filterQuery();

      $this->db->where('mPriorityReport.ClientUID', $this->parameters['DefaultClientUID']);
      if ($post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
         $filter = $this->advanced_search($post);
      }
      // Datatable Search
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);
      
      // Datatable OrderBy
      if (!empty($post['order'])) {
         $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
      }else{
         $this->db->order_by('(mPriorityReport.Position * -1)','DESC');
      }

      $query = $this->db->get();
      return $query->num_rows();
   }
   /* 
	*Get Document checklist filtered data
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
   function getPrioritylist($post)
   {
      $this->filterQuery();
      $this->db->where('mPriorityReport.ClientUID', $this->parameters['DefaultClientUID']);

      /* Advanced Search  */
      if (!empty($post['advancedsearch']) && $post['advancedsearch'] != 'false' && sizeof(array_filter($post['advancedsearch'])) != 0) {
         $filter = $this->advanced_search($post);
      }
      /* Datatable Search */
      $this->Common_Model->WorkflowQueues_Datatable_Search($post);

      /* Datatable OrderBy */
      if (!empty($post['order'])) {
         $this->Common_Model->WorkflowQueues_Datatable_OrderBy($post);
      }else{
         $this->db->order_by('(mPriorityReport.Position * -1)','DESC');
      }

      if ($post['length'] != '') {
         $this->db->limit($post['length'], $post['start']);
      }

      $query = $this->db->get();
      return $query->result();
   }
   /* 
	*Get filtering query for document checklist data table
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:16-07-2020
	 */
   function filterQuery()
   {
      $this->db->select("mPriorityReport.PriorityUID,mPriorityReport.PriorityName,mPriorityReport.Active,mCustomer.CustomerName");
      $this->db->from('mPriorityReport');
      $this->db->join('mCustomer', 'mPriorityReport.ClientUID=mCustomer.CustomerUID', 'left');
   }
   /* 
	*Function for advanced_search
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:23-07-2020
	 */
   function advanced_search($post)
   {
      if (($post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All') || ($post['advancedsearch']['Category'] != '' && $post['advancedsearch']['Category'] != 'All')) {
         $this->db->join('mPriorityReportFields', 'mPriorityReport.PriorityUID=mPriorityReportFields.PriorityUID', 'left');
         if ($post['advancedsearch']['WorkflowModuleUID'] != '' && $post['advancedsearch']['WorkflowModuleUID'] != 'All') {
            $this->db->where('mPriorityReportFields.WorkflowModuleUID', $post['advancedsearch']['WorkflowModuleUID']);
         }
         if ($post['advancedsearch']['Category'] != '' && $post['advancedsearch']['Category'] != 'All') {
            $this->db->where('mPriorityReportFields.WorkflowStatus', $post['advancedsearch']['Category']);
         }
      }

      return true;
   }

   function CheckExistUserName($UserUID, $LoginID)
   {
      return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows();
   }

   /* 
   * Function for check existing priorityUID
   * @author vishnupriya(vishnupriya.a@avanzegroup.com)
	* @since Date:23-07-2020
   */
   function CheckPriorityExist($post)
   {
      if (isset($post['PriorityUID'])) {
         $this->db->select('*');
         $this->db->from('mPriorityReport');
         $this->db->where(array('PriorityName' => $post['PriorityName'], 'ClientUID' => $post['CustomerUID']));
         $query = $this->db->get();
      } else {
         $query = $this->db->get_where('mPriorityReport', array('PriorityName' => $post['PriorityName'], 'ClientUID' => $post['CustomerUID']));
      }
      return $query->num_rows();
   }


   /* 
   *add Workflow 
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:23-07-2020
	 */
   function addWorkflow($post, $PriorityUID)
   {
      $getWorkflowModuleName = $this->getWorkflowModuleName();
      foreach ($post['priority'] as $key => $value) {
         //check count WorkflowmoduleUID of PriorityUID
         $countWorkflow = $this->checkWorkflowField($value['WorkflowModuleUID'], $PriorityUID[0]->PriorityUID);
         //Insert and update based on count
         if ($countWorkflow > 0) {
            $this->db->where(array("PriorityUID" => $PriorityUID[0]->PriorityUID, 'WorkflowModuleUID' => $value['WorkflowModuleUID']));
            $update = array('PriorityUID' => $PriorityUID[0]->PriorityUID, 'WorkflowStatus' => $value['WorkflowStatus'], 'WorkflowModuleUID' => $value['WorkflowModuleUID']);
            $this->db->update('mPriorityReportFields', $update);
            $this->priorityLogsHistory($this->uri->segment(1), 'Updated ' . $getWorkflowModuleName[$value['WorkflowModuleUID']] . ' status is ' . $value['WorkflowStatus'] . ' in ' . $post['PriorityName'],  Date('Y-m-d H:i:s'));
         } else {
            $insert = array('PriorityUID' => $PriorityUID[0]->PriorityUID, 'WorkflowStatus' => $value['WorkflowStatus'], 'WorkflowModuleUID' => $value['WorkflowModuleUID']);
            $this->db->insert('mPriorityReportFields', $insert);
            $this->priorityLogsHistory($this->uri->segment(1), 'Added ' . $getWorkflowModuleName[$value['WorkflowModuleUID']] . ' and status is ' . $value['WorkflowStatus'] . ' in ' . $post['PriorityName'],  Date('Y-m-d H:i:s'));
         }
      }
   }

   /* 
   *check workflowUID 
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:23-07-2020
	 */
   function checkWorkflowField($WorkflowModuleUID, $PriorityUID)
   {
      $this->db->select("PriorityUID");
      $this->db->from('mPriorityReportFields');
      $this->db->where(array('WorkflowModuleUID' => $WorkflowModuleUID, 'PriorityUID' => $PriorityUID));
      return $this->db->get()->num_rows();
      //$parentChecklistsexits = array_column($deleted, 'DocumentTypeUID');
   }

   /* 
   *Get priorityUID
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:23-07-2020
	 */
   function getPriorityUID($PriorityName, $ClientUID)
   {
      $this->db->select("PriorityUID");
      $this->db->from('mPriorityReport');
      $this->db->where(array('PriorityName' => $PriorityName, 'ClientUID' => $ClientUID));
      return $this->db->get()->result();
   }


   /* 
   *get Category
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:23-07-2020
	 */
   function getWorkflowModule()
   {
      $this->db->select("mCustomerWorkflowModules.WorkflowModuleUID,mWorkFlowModules.WorkflowModuleName");
      $this->db->from('mCustomerWorkflowModules');
      $this->db->join('mWorkFlowModules', 'mCustomerWorkflowModules.WorkflowModuleUID=mWorkFlowModules.WorkflowModuleUID', 'left');
      $this->db->where('mCustomerWorkflowModules.CustomerUID', $this->parameters['DefaultClientUID']);
      return $this->db->get()->result_array();
   }

   /* 
   * Get workflow module name of workflowUID
   *author vishnupriya(vishnupriya.a@avanzegroup.com)
   *since Date:23-07-2020
   */
   function getWorkflowModuleName()
   {
      $getWorkflowModule = $this->getWorkflowModule();
      foreach ($getWorkflowModule as $key => $value) {
         $workflow[$value['WorkflowModuleUID']] = $value['WorkflowModuleName'];
      }
      return $workflow;
   }

   /* 
   *Function for get Selected workfloewUID
	*author vishnupriya(vishnupriya.a@avanzegroup.com)
	*since Date:23-07-2020
	 */
   function getPriorityWorkflowUID()
   {
      $PriorityDetails = $this->db->select("mPriorityReportFields.WorkflowModuleUID")->from("mPriorityReport")->join('mPriorityReportFields', 'mPriorityReport.PriorityUID=mPriorityReportFields.PriorityUID')->where(array('mPriorityReport.PriorityUID' => $this->uri->segment(3), 'mPriorityReport.ClientUID' => $this->parameters['DefaultClientUID']))->get()->result();

      foreach ($PriorityDetails as $key => $value) {
         $data[] = $value['WorkflowModuleUID'];
      }
      return $data;
   }

   /* 
   *Function for get data of priorityUID 
   *author vishnupriya(vishnupriya.a@avanzegroup.com)
   *since Date:23-07-2020
   */
   function getPriorityData()
   {
      $PriorityDetails = $this->db->select("mPriorityReportFields.WorkflowModuleUID,mPriorityReportFields.WorkflowStatus")->from("mPriorityReport")->join('mPriorityReportFields', 'mPriorityReport.PriorityUID=mPriorityReportFields.PriorityUID')->where('mPriorityReport.PriorityUID', $this->uri->segment(3))->get()->result();
      foreach ($PriorityDetails as $key => $value) {
         $data['WorkflowModuleUID'] = $value->WorkflowModuleUID;
         $data['WorkflowStatus'] = $value->WorkflowStatus;
         $whole[] = $data;
      }
      return $whole;
   }

   /* 
   *Function for Delete workflowUID 
   *author vishnupriya(vishnupriya.a@avanzegroup.com)
   *since Date:23-07-2020
   */
   function deleteWorkflowUID($workflowUID, $PriorityUID, $PriorityName)
   {
      $getWorkflowModuleName = $this->getWorkflowModuleName();
      $this->db->query('DELETE FROM `mPriorityReportFields` WHERE PriorityUID= ' . $PriorityUID . ' and WorkflowModuleUID =' . $workflowUID);
      $this->priorityLogsHistory($this->uri->segment(1), $getWorkflowModuleName[$workflowUID] . ' Workflow Module is Deleted',  Date('Y-m-d H:i:s'));
      return $this->db->affected_rows();
   }
}
