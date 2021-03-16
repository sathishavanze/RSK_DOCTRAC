<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* ini_set('display_errors', 1);
error_reporting(E_ALL); */
class PriorityCustomization extends MY_Controller
{

   function __construct()
   {
      parent::__construct();
      $this->load->model('PriorityCustomization_model');
      $this->load->library('form_validation');
   }

   public function index()
   {
      $data['content'] = 'index';
      $data['Modules'] = $this->Common_Model->GetCustomerBasedModules();
      $data['getWorkflowModule'] = $this->PriorityCustomization_model->getWorkflowModule();
      $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
   }


   /*
   *function for  Data table
   * *@author Vishnupriya <vishnupriya.a@avanzegroup.com>
   *@since Date : 23-07-2020
    */
   function GetPriority()
   {
      //Advanced Search
      $post['advancedsearch'] = $this->input->post('formData');
      $post['length'] = $this->input->post('length');
      $post['start'] = $this->input->post('start');
      $search = $this->input->post('search');
      $post['search_value'] = trim($search['value']);
      $post['order'] = $this->input->post('order');
      $post['draw'] = $this->input->post('draw');
      $post['column_order'] = array('','PriorityName', 'CustomerName');
      $post['column_search'] = array('PriorityName', 'CustomerName');
      //$parentchecklists = $this->PriorityCustomization_model->getParentDocumentUID();

      $getPrioritylist = $this->PriorityCustomization_model->getPrioritylist($post);
      /* foreach start for data table */
      $i = 1;
      $wholeData = [];
      foreach ($getPrioritylist as $key => $value) {
         $row = array();
         $row[] = $i;
         $row[] = $value->PriorityName;
         $row[] = $value->CustomerName;
         $active = ($value->Active == 1) ? "checked" : '';
         $row[] = '<div class="togglebutton"><label class="label-color"><input type="checkbox" id="Active" name="Active" class="Active" ' . $active . ' disabled><span class="toggle"></span></label></div>';
         $row[] = '<span style="text-align: left;width:100%;"><a href="' . base_url('PriorityCustomization/Editpriority/' . $value->PriorityUID) . '" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a></span><span title="Move" class="icon_action move-handle-icon" style="color: #000;" data-position="' . $value->PriorityUID . '"><i class="fa fa-arrows" aria-hidden="true"></i></span>';
         $i++;
         array_push($wholeData, $row);
      }
      /* foreach end */
      $data =  array(
         'MaritalTableList' => ($wholeData),
         'post' => $post
      );
      $post = $data['post'];
      //Get count for all record
      $count_all = $this->PriorityCustomization_model->count_filtered($post);
      //overall record for data table
      $output = array(
         "draw" => $this->input->post('draw'),
         "recordsTotal" => $this->PriorityCustomization_model->count_all(),
         "recordsFiltered" => $count_all,
         "data" => $data['MaritalTableList'],
      );
      unset($post);
      unset($data);
      echo json_encode($output);
   }

   /* 
   * Function for Add priority page functionality 
   * @author Vishnupriya <vishnupriya.a@avanzegroup.com>
   * @since Date : 23-07-2020
   */
   function Addpriority()
   {
      $data['content'] = 'addpriority';
      $data['Roles'] = $this->Common_Model->GetCategory();
      $data['getWorkflowModule'] = $this->PriorityCustomization_model->getWorkflowModule();
      $data['Customer'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID' => 'ASC'], []);

      $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
   }

   /* 
   * Function for append list of workflow and status 
   *@author Vishnupriya <vishnupriya.a@avanzegroup.com>
   *@since Date : 23-07-2020
   */
   function appendWorkflowModule()
   {
      $count = $this->input->post('count');
      $getWorkflowModule = $this->PriorityCustomization_model->getWorkflowModule();;
      /* get dynamic row workflow module and status */
      $select = '<div class="row removeRow"><div class="col-md-4"><label class="bmd-label-floating" for="WorkflowModuleUID">Workflow Module</label><select name="priority[' . $count . '][WorkflowModuleUID]" class="select2picker form-control" id="WorkflowModuleUID"><option value=""> Select Workflow Module</option>';
      /* foreach start for get workflow module select */
      foreach ($getWorkflowModule as $keyWorkflow => $valueWorkflow) {
         $select .= '<option value="' . $valueWorkflow['WorkflowModuleUID'] . '">' . $valueWorkflow['WorkflowModuleName'] . '</option>';
      }
      /* foreach end */
      $select .= '</select></div><div class="col-md-4"><label class=" bmd-label-floating" for="WorkflowStatus">Workflow Status</label><select name="priority[' . $count . '][WorkflowStatus]" class="select2picker form-control" id="WorkflowStatus"><option value=""> Select Workflow Status</option><option value="Completed">Completed</option><option value="Pending">Pending</option></select></div><div class="col-md-1"  style="padding-top: 30px;"><a style="width:8%;float:right;"><i class="fa fa-minus-circle removepriority pull-right" aria-hidden="true" style="font-size: 20px;margin-top: 10px;"></i></a></div></div>';
      echo $select;
   }

   /* 
   * Function for Edit priority page functionality 
   * @author Vishnupriya <vishnupriya.a@avanzegroup.com>
   *@since Date : 23-07-2020
   */
   function Editpriority()
   {
      $PriorityUID = $this->uri->segment(3);
      $data['PriorityDetails'] = $this->db->select("*")->from("mPriorityReport")->where('mPriorityReport.PriorityUID', $this->uri->segment(3))->get()->row();
      $data['getPriorityData'] = $this->PriorityCustomization_model->getPriorityData();
      $data['content'] = 'updatepriority';
      $data['getWorkflowModuleName'] = $this->PriorityCustomization_model->getWorkflowModuleName();
      $data['getWorkflowModule'] = $this->PriorityCustomization_model->getWorkflowModule();
      $data['Customer'] = $this->Common_Model->get('mCustomer', ['Active' => 1], ['CustomerUID' => 'ASC'], []);
      $this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
   }

   /* 
   *Form submit priority 
   *@author Vishnupriya <vishnupriya.a@avanzegroup.com>
   *@since Date : 23-07-2020
   */
   function SavePriority()
   {
      if ($this->input->server('REQUEST_METHOD') === 'POST') {
         $this->form_validation->set_error_delimiters('', '');
         $this->form_validation->set_rules('PriorityName', '', 'required');
         $this->form_validation->set_rules('CustomerUID', '', 'required');
         /*validate data */
         if ($this->form_validation->run() == true) {
            // Check Priority already exist or not
            if ($this->input->post('PriorityUID')) {
               $result = '';
            } else {
               $result = $this->PriorityCustomization_model->CheckPriorityExist($this->input->post());
            }

            /* throw Warning Message for already exist Name */
            if ($result) {
               $res = array('Status' => 1, 'message' => 'Priority Name Already Exist!.', 'PriorityName' => 'Priority Name Already Exist.');
               echo json_encode($res);
               exit();
            }

            /* Add and update condition */
            if ($this->PriorityCustomization_model->GetPriorityTpe($this->input->post()) == 1) {
               if ($this->input->post('PriorityUID')) {
                  $res = array('Status' => 2, 'message' => 'Updated Successsfully');
                  echo json_encode($res);
                  exit();
               } else {
                  $res = array('Status' => 0, 'message' => 'Added Successsfully');
                  echo json_encode($res);
                  exit();
               }
            } else {
               $res = array('Status' => 2, 'message' => 'Updated Successsfully');
               echo json_encode($res);
               exit();
            }
         } else {
            $Msg = $this->lang->line('Empty_Validation');
            $data = array(
               'Status' => 1,
               'message' => $Msg,
               'PriorityName' => form_error('PriorityName'),
               'CustomerUID' => form_error('CustomerUID'),
               'type' => 'danger',
            );
            foreach ($data as $key => $value) {
               if (is_null($value) || $value == '')
                  unset($data[$key]);
            }
            //$res = array('Status' => 4,'detailes'=>$data);
            echo json_encode($data);
         }
      }
   }

   /* *
	*change the Priority position (drag and drop)
	*@author Vishnupriya <vishnupriya.a@avanzegroup.com>
	*@since Date : 23-07-2020
	 */
   function deleteWorkflow()
   {
      $post = $this->input->post();

      if ($post['workflowUID']) {
         $deleted = $this->PriorityCustomization_model->deleteWorkflowUID($post['workflowUID'], $post['PriorityUID'], $post['PriorityName']);
         echo $deleted;
      }
   }

   /* *
	*change the Priority position (drag and drop)
	*@author Vishnupriya <vishnupriya.a@avanzegroup.com>
	*@since Date : 23-07-2020
	 */
   function priorityPosition()
   {
      $post = $this->input->post();
      if ($this->input->post('sortData')) {
         $file = $this->input->post('sortData');
         $CustomerUID = $this->input->post('CustomerUID');
         $OrderUID = $this->input->post('OrderUID');
         $Position = 1;
         /* foreach start for update position */
         foreach ($file as $value) {
            if ($value['ID']) {
               $this->db->query('UPDATE mPriorityReport SET Position = ' . $Position . ' WHERE PriorityUID = ' . $value['ID'] . ' and ClientUID = ' . $this->parameters['DefaultClientUID']);
               $Position++;
            }
         }
         /* forach end */
         $this->PriorityCustomization_model->priorityLogsHistory($this->uri->segment(1), 'Position Changed', Date('Y-m-d H:i:s'));
         echo json_encode(array('error' => 0, 'msg' => 'Position Updated.', 'type' => 'success'));
      } else {
         echo json_encode(array('error' => 0, 'msg' => 'Something went wrong!.', 'type' => 'danger'));
      }
   }
}
