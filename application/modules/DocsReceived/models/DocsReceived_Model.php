<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class DocsReceived_Model extends MY_Model { 
	function __construct()
	{ 
		parent::__construct();
		$this->lang->load('keywords');
	}

	function getDocsReceived(){
		$this->db->select('*,mUsers.UserName as UploadedUser');
		$this->db->from('tDocuments');
		$this->db->join('mUsers','tDocuments.UploadedByUserUID = mUsers.UserUID','left');
		$this->db->group_by('tDocuments.DocumentUID');
		$this->db->order_by('tDocuments.DocumentUID', 'desc');
		$query = $this->db->get();
		
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}
	function getDocReceived($id){
		$this->db->select('*')->from('tDocuments')->join('tEmailImport', 'tEmailImport.DocumentUID = tDocuments.DocumentUID');
		$this->db->where('tEmailImport.DocumentUID', $id);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return false;
		}	
	}
	function UpdateDocsReceived($post){
		$log  = array('RecipientEmail' => ($post['RecipientEmail']),
			'EmailSubject'=> str_replace(',', '-', $post['EmailSubject']),
			'EmailBody'=> $post['EmailBody'],
			'IsReceived'=> 'Success',
			'OrderUID'=> '0',
			'DocumentUID' => ($post['DocumentUID'] ? $post['DocumentUID'] : 0),
			'ImportedDateTime'=> date('Y-m-d H:i:s'),
			'UploadedByUserUID'=> $this->loggedid,
		);
		if($post['EmailUID']){
			$where = array('EmailUID' => $post['EmailUID']);
			
			$this->db->update('tEmailImport', $log, $where);
		}else{
			$this->db->insert('tEmailImport', $log);
		}
		return true;
	}
	function deleteDocs($id){
		$document_details = $this->db->select('DocumentURL')->from('tDocuments')->where('DocumentUID',$id)->get();
		if($document_details->num_rows() > 0){
			$document_details = $document_details->row();
			unlink( str_replace(base_url(), '', $document_details->DocumentURL) );
		}
		
		  $this->db->where('DocumentUID',$id);
	      $this->db->delete('tDocuments');
	      if($this->db->affected_rows() > 0)
	      {
	      	$this->db->where('DocumentUID', $id);
	      	$this->db->delete('tEmailImport');
	      	return true;
	      }else{
	      	return false;
	      }
	}
}
?>
