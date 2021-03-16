<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Question extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Questionmodel');
		$this->load->library('form_validation');
	}	

	public function index()
	{
		
		$data['content'] = 'index';
		$data['question']=$this->Questionmodel->getquestion();
		$data['UserDetails'] = $this->db->select("*")->from("mQuestion")->get()->result();
		$data['Roles'] = $this->Common_Model->GetQuestion();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function question_ajax_list()
	{
		$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        $post['column_order'] = array('mQuestion.QuestionName', 'mQuestionType.QuestionTypeName','mProjectCustomer.ProjectName','mLender.LenderName','mDocumentType.DocumentTypeName', 'mQuestion.FreeQuestion');
		$post['column_search'] = array('mQuestion.QuestionName', 'mQuestionType.QuestionTypeName','mProjectCustomer.ProjectName','mLender.LenderName','mDocumentType.DocumentTypeName', 'mQuestion.FreeQuestion');

			$list = $this->Questionmodel->paginationquestions($post);
//print_r($list);exit();
        $no = $post['start'];
        $questionlist = [];
       
		foreach ($list as $question)
        {
		        $row = array();
		       
		        $row[] = $question->QuestionName;
		        $row[] = $question->QuestionTypeName;
		        $row[] = $question->ProjectName;
		        $row[] = $question->LenderName;
		        $row[] = $question->DocumentTypeName;
		       
		         if($question->FreeQuestion == 1)
		         {
		         	$row[] = '<div class="form-check"><label class="form-check-label">
            <input class="form-check-input" type="checkbox" checked name="FreeQuestion" value="FreeQuestion" disabled>
            <span class="form-check-sign">
              <span class="check"></span>
            </span>
          </label></div>';
		         }
		         else
		         {
		         	$row[] = '<div class="form-check"><label class="form-check-label"> <input class="form-check-input" type="checkbox" name="FreeQuestion" value="FreeQuestion" disabled>
            <span class="form-check-sign">
              <span class="check"></span>
            </span>
          </label></div>';
		         }
				if($question->Active == 1)
				{
					$row[]='<div class="togglebutton">
                  <label class="label-color"> 
                    <input type="checkbox" id="Active" name="Active" class="Active" checked disabled>
                    <span class="toggle"></span>
                  </label>
                </div>';
				}
				else
				{
					$row[]='<div class="togglebutton">
                  <label class="label-color"> 
                    <input type="checkbox" id="Active" name="Active" class="Active" disabled>
                    <span class="toggle"></span>
                  </label>
                </div>';
				}
				$url=base_url('Question/EditQuestion/'.$question->QuestionUID);
				
 				$row[]=' <span style="text-align: center;width:100%;">
            <a href="'.$url.'" class="btn btn-link btn-info btn-just-icon btn-xs ajaxload"><i class="icon-pencil"></i></a>';
		       
		        $questionlist[]= $row;
		       
        }



        $data =  array(
        	'questionlist' => $questionlist,
        	'post' => $post
        );



		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->Questionmodel->count_all(),
			"recordsFiltered" =>  $this->Questionmodel->count_filtered($post),
			"data" => $data['questionlist'],
		);

		unset($post);
		unset($data);

		echo json_encode($output);
	}

	function AddQuestion()
	{
		$data['content'] = 'addquestion';
		$data['UserDetails1'] = $this->db->select("*")->from("mQuestionType")->get()->result();
		$data['UserDetails2'] = $this->db->select("*")->from("mDocumentType")->get()->result();
		$data['UserDetails3'] = $this->db->select("*")->from("mProjectCustomer")->get()->result();
		$data['UserDetails4'] = $this->db->select("*")->from("mLender")->where('Active',1)->get()->result();
		$data['InputDocType'] = $this->db->select("*")->from("mInputDocType")->get()->result();

		//$data['Roles'] = $this->Common_Model->GetRoles();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function EditQuestion()
	{
		$data['UserDetails'] = $this->db->select("*")->from("mQuestion")->where(array('QuestionUID'=>$this->uri->segment(3)))->get()->row();
		$data['UserDetails1'] = $this->db->select("*")->from("mQuestionType")->get()->result();
		$data['UserDetails2'] = $this->db->select("*")->from("mDocumentType")->get()->result();
		$data['UserDetails3'] = $this->db->select("*")->from("mProjectCustomer")->get()->result();
		$data['UserDetails4'] = $this->db->select("*")->from("mLender")->where('Active',1)->get()->result();
		$data['InputDocType'] = $this->db->select("*")->from("mInputDocType")->get()->result();

         //$data['question']=$this->Questionmodel->getquestion();
		$data['content'] = 'updatequestion';
		$data['Roles'] = $this->Common_Model->GetQuestion();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}

	function SaveQuestion()
	{


		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('QuestionName', '', 'required');
			$this->form_validation->set_rules('QuestionTypeUID', '', 'required');
			$this->form_validation->set_rules('ProjectUID', '', 'required');
	
			$this->form_validation->set_message('required', 'This Field is required');

			$data['FreeQuestion']=isset($post['FreeQuestion']) ? 1 : 0;
             $data['Active']=isset($post['Active']) ? 1 : 0;

			if ($this->form_validation->run() == true) 
			{

				if($this->Questionmodel->SaveQuestion($this->input->post()) == 1)
				{
					$res = array('Status' => 1,'message'=>'Question added Successsfully');
					echo json_encode($res);exit();
				}else{
					print_r("expression");exit();
					$res = array('Status' => 0);
					echo json_encode($res);exit();
				}
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'validation_error' => 1,
					'message' => $Msg,
					'QuestionName' => form_error('QuestionName'),
					'QuestionTypeUID' => form_error('QuestionTypeUID'),
					// 'LenderUID' => form_error('LenderUID'),
					// 'DocumentTypeUID' => form_error('DocumentTypeUID'),
					
					
				);


				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function UpdateQuestion()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST') 
		{
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('QuestionName', '', 'required');
			$this->form_validation->set_rules('QuestionTypeUID', '', 'required');
			
			$this->form_validation->set_message('required', 'This Field is required');
			$post=$this->input->post();
			$data['FreeQuestion']=isset($post['FreeQuestion']) ? 1 : 0;
			 $data['Active']=isset($post['Active']) ? 1 : 0;

			if ($this->form_validation->run() == true) 
			{
				
				if($this->Questionmodel->UpdateQuestion($this->input->post()) == 1)
				{
					$res = array('Status' => 2,'message'=>'Updated Successsfully','type' => 'success');
					echo json_encode($res);exit();
				}else{
					$res = array('Status' => 0,'message'=>'Updated UNSuccesssfully');
					echo json_encode($res);exit();
				}
			
				
			}else{


				$Msg = $this->lang->line('Empty_Validation');


				$data = array(
					'Status' => 1,
					'message' => $Msg,
					'QuestionName' => form_error('QuestionName'),
					'QuestionTypeUID' => form_error('QuestionTypeUID'),
					// 'LenderUID' => form_error('LenderUID'),
					// 'DocumentTypeUID' => form_error('DocumentTypeUID'),
					
					
				);

				

				foreach ($data as $key => $value) {
					if (is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}
		}

	}

	function CheckLoginUser()
	{
		$loginid = $this->input->post('loginid');
		if($this->Usermodel->CheckLoginUser($loginid) == 1)
		{
			$res = array('Status' => 1);
			echo json_encode($res);exit();
		}else{
			$res = array('Status' => 2);
			echo json_encode($res);exit();
		}
	}


} 

?>
