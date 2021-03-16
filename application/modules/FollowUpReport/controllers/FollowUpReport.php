<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class FollowUpReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('FollowUpReport_model');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['ProcessUsers'] = $this->FollowUpReport_model->getProcessUsers(array());
		$data['date'] = $this->monthFirstLastDay();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function weekFirstLastDay()
	{
		$date = date('Y-m-d'); // you can put any date you want
		$nbDay = date('N', strtotime($date));
		$monday = new DateTime($date);
		$sunday = new DateTime($date);
		$monday->modify('-'.($nbDay-1).' days');
		$sunday->modify('+'.(7-$nbDay).' days');
		return array('firstday'=>$monday->format('m/d/Y'),'lastday'=>$sunday->format('m/d/Y'));
	}
	/** @author harini <harini.bangari@avanzegroup.com> **/
	/** @date  11 Aug 2020 **/
	/** @Getting month first and last date  **/
	function monthFirstLastDay()
	{
		$first_date = date('m/d/Y',strtotime('first day of this month'));
		$last_date = date('m/d/Y',strtotime('last day of this month'));
		return array('firstday'=>$first_date,'lastday'=>$last_date);
	}
	function getFollowupReport()
	{
		$data['post'] = $this->input->post();
		if($this->input->post('filter_type') == 'workflow' || $this->input->post('filter_type') == 'title'){
			$data['SubQueues'] = $this->FollowUpReport_model->getSubQueues($this->input->post('workflow'));
			$data['Followup'] = $this->FollowUpReport_model->getFollowup_workflow($this->input->post());
		}else{
			$data['ProcessUsers'] = $this->FollowUpReport_model->getProcessUsers($this->input->post('Process'));
			$data['Followup'] = $this->FollowUpReport_model->getFollowup_agent($this->input->post());
		}
		// echo '<pre>';print_r($data);exit;
		echo json_encode($this->load->view('tablepartialview',$data,'true'));
	}
	function getFromToDate()
	{
		$period = $this->input->post('period');
		if($period == 'week')
		{
			$date = date('Y-m-d'); // you can put any date you want
			$nbDay = date('N', strtotime($date));
			$monday = new DateTime($date);
			$sunday = new DateTime($date);
			$monday->modify('-'.($nbDay-1).' days');
			$sunday->modify('+'.(7-$nbDay).' days');
			echo  json_encode(array('fromDate'=>$monday->format('m/d/Y'),'toDate'=>$sunday->format('m/d/Y')));
		}
		else if($period == 'month')
		{
			$first_date = date('m/d/Y',strtotime('first day of this month'));
			$last_date = date('m/d/Y',strtotime('last day of this month'));
			echo  json_encode(array('fromDate'=>$first_date,'toDate'=>$last_date));
		}else if($period == 'year')
		{
			$first_date = date('m/d/Y',strtotime('1/1'));
			$last_date = date('m/d/Y',strtotime('12/31'));
			echo  json_encode(array('fromDate'=>$first_date,'toDate'=>$last_date));
		}
	}
}

?>
