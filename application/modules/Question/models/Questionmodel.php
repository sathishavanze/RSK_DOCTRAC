<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Questionmodel extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
	}

	function SaveQuestion($post)
	{

		$FreeQuestion = isset($post['FreeQuestion']) ? 1 : 0;
		// $Active=isset($post['Active']) ? 1 : 0;
		if($post['DocumentTypeUID'] == '')
		{
			$post['DocumentTypeUID'] ='NULL';
		}
		if($post['LenderUID'] == '')
		{
			$post['LenderUID'] ='NULL';
		}
		$Users = array('QuestionName' => $post['QuestionName'],'QuestionTypeUID' => $post['QuestionTypeUID'],'ProjectUID' => $post['ProjectUID'],'LenderUID' => $post['LenderUID'],'DocumentTypeUID' => $post['DocumentTypeUID'],'FreeQuestion' => $FreeQuestion,'Active' => 1);
		
		$this->db->insert('mQuestion',$Users);
		
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}
	function getquestion(){
		$this->db->select("*,mQuestion.Active");
		$this->db->from('mQuestion');
		$this->db->join('mQuestionType','mQuestion.QuestionTypeUID=mQuestionType.QuestionTypeUID','left');
		$this->db->join('mProjectCustomer','mQuestion.ProjectUID=mProjectCustomer.ProjectUID','left');
		$this->db->join('mLender','mQuestion.LenderUID=mLender.LenderUID','left');
		$this->db->join('mDocumentType','mQuestion.DocumentTypeUID=mDocumentType.DocumentTypeUID','left');
		return $this->db->get()->result();
	}

	function paginationquestions($post)
	{
		$this->db->select("mQuestion.QuestionUID,mQuestion.QuestionName, mQuestionType.QuestionTypeName,mProjectCustomer.ProjectName,mLender.LenderName,mDocumentType.DocumentTypeName, mQuestion.FreeQuestion,mQuestion.Active");
		$this->db->from('mQuestion');
		$this->db->join('mQuestionType','mQuestion.QuestionTypeUID=mQuestionType.QuestionTypeUID','left');
		$this->db->join('mProjectCustomer','mQuestion.ProjectUID=mProjectCustomer.ProjectUID','left');
		$this->db->join('mLender','mQuestion.LenderUID=mLender.LenderUID','left');
		$this->db->join('mDocumentType','mQuestion.DocumentTypeUID=mDocumentType.DocumentTypeUID','left');
		if (!empty($post['search_value'])) {
			$like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
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
        else{
          $this->db->order_by('mQuestion.QuestionUID', 'ASC');
        }


	    if ($post['length']!='') {
	       $this->db->limit($post['length'], $post['start']);
	    }
      
	    $query = $this->db->get();
	    return $query->result();
	}

	function count_all()
	{
		$this->db->select("mQuestion.QuestionUID,mQuestion.QuestionName, mQuestionType.QuestionTypeName,mProjectCustomer.ProjectName,mLender.LenderName,mDocumentType.DocumentTypeName, mQuestion.FreeQuestion,mQuestion.Active");
		$this->db->from('mQuestion');
		$this->db->join('mQuestionType','mQuestion.QuestionTypeUID=mQuestionType.QuestionTypeUID','left');
		$this->db->join('mProjectCustomer','mQuestion.ProjectUID=mProjectCustomer.ProjectUID','left');
		$this->db->join('mLender','mQuestion.LenderUID=mLender.LenderUID','left');
		$this->db->join('mDocumentType','mQuestion.DocumentTypeUID=mDocumentType.DocumentTypeUID','left');
		$query = $this->db->count_all_results();
  	    return $query; 
	}

	function count_filtered($post)
	{
		$this->db->select("mQuestion.QuestionUID,mQuestion.QuestionName, mQuestionType.QuestionTypeName,mProjectCustomer.ProjectName,mLender.LenderName,mDocumentType.DocumentTypeName, mQuestion.FreeQuestion,mQuestion.Active");
		$this->db->from('mQuestion');
		$this->db->join('mQuestionType','mQuestion.QuestionTypeUID=mQuestionType.QuestionTypeUID','left');
		$this->db->join('mProjectCustomer','mQuestion.ProjectUID=mProjectCustomer.ProjectUID','left');
		$this->db->join('mLender','mQuestion.LenderUID=mLender.LenderUID','left');
		$this->db->join('mDocumentType','mQuestion.DocumentTypeUID=mDocumentType.DocumentTypeUID','left');
		if (!empty($post['search_value'])) {
			$like = "";
         foreach ($post['column_search'] as $key => $item) { // loop column
            // if datatable send POST for search
            if ($key === 0) { // first loop
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
        else{
          $this->db->order_by('mQuestion.QuestionUID', 'ASC');
        }


	    
      
	    $query = $this->db->get();
  	     return $query->num_rows();
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
	function UpdateQuestion($post){
		

		$FreeQuestion = isset($post['FreeQuestion']) ? 1 : 0;
		$Active=isset($post['Active']) ? 1 : 0;
		if($post['DocumentTypeUID'] == '')
		{
			$post['DocumentTypeUID'] =NULL;
		}
		if($post['LenderUID'] == '')
		{
			$post['LenderUID'] =NULL;
		}
			if($post['InputDocTypeUID'] == '')
		{
			$post['InputDocTypeUID'] =NULL;
		}

		$Questions = array('QuestionName' => $post['QuestionName'],'QuestionTypeUID' => $post['QuestionTypeUID'],'ProjectUID' => $post['ProjectUID'],'LenderUID' => $post['LenderUID'],'DocumentTypeUID' => $post['DocumentTypeUID'],'FreeQuestion' => $FreeQuestion,'Active' => $Active, 'InputDocTypeUID'=>$post['InputDocTypeUID']);
		$this->db->where(array('QuestionUID' => $post['QuestionUID']));
		$this->db->update('mQuestion',$Questions);
		if($this->db->affected_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}


	}

	function CheckLoginUser($loginid)
	{
		$this->db->select("*");
		$this->db->from("mUsers");
		$this->db->where(array('LoginID' => $loginid));
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return 1;
		}else{
			return 0;
		}
	}

	function CheckExistUserName($UserUID, $LoginID)
	{																	
		return $this->db->query("SELECT * FROM mUsers WHERE LoginID = '$LoginID' AND UserUID <> '$UserUID'")->num_rows(); 
	}
	function DeleteCustomer($id)
	{
		$data = array(
			'Active' => 0
		);

		$this->db->where('CustomerUID', $id);
		$this->db->update('mCustomer', $data);
	}

}
?>

