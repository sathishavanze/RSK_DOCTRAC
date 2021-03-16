<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class ActivityReport extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ActivityReport_model');
	}

	public function index()
	{
		$data['content'] = 'index';
		$data['ActivityUsers'] = $this->ActivityReport_model->getActivityUsers(array());
		$data['date'] = $this->weekFirstLastDay();
		$data['WorkflowDetails'] = $this->Common_Model->GetcustomerWorkflowDetails();
		$this->load->view($this->input->is_ajax_request() ? $data['content'] : 'page', $data);
	}
	function getActiveLogs()
	{
		$data['post'] = $this->input->post();

		// From date and to date diff in days
		$WorkingDays = $this->Common_Model->TwoDatesDiffence($data['post']['FromDate'], $data['post']['ToDate'], $data['post']['IncludeWeekends']);

		$list = $this->ActivityReport_model->getActivityLogs($data['post']);

		$table = '<table class="table table-bordered table-responsive scrool processinflow"> <thead> <tr> <th>User</th> <th>Workflow</th> <th>No of Task Completed</th> <th>Duration Per Task</th> <th>Total Time Spent</th> <th>CU%</th> </tr> </thead><tbody>'; 
		if(!empty($list)){
			foreach($list as $key => $value){
			$table .= '<tr>';
			$table .= '<td>'.$value->UserName.'</td>';
			$table .= '<td>'.$value->WorkflowModuleName.'</td>';
			$table .= '<td>'.$value->WorkCount.'</td>';
			$table .= '<td>'.$value->Hours.'</td>';

			$totalLog = $value->Hours * $value->WorkCount;
			$table .= '<td>'.$totalLog.'</td>';
			$avarage = round(($totalLog/(480*$WorkingDays))*100, 2);
			$table .= '<td>'.$avarage.'%</td>';

			$table .= '</tr>';
			}	
		}else{
			$table .= '<tr><td></td><td></td><td>No data found </td><td></td><td></td><td></td></tr>';
		}
		
		$table .= '</table></table>';
		$data['table'] = $table;
		echo json_encode($data);
	}

	function weekFirstLastDay()
	{
		$date = date('m/d/Y'); // you can put any date you want
		$nbDay = date('N', strtotime($date));
		$monday = new DateTime($date);
		$sunday = new DateTime($date);
		$monday->modify('-'.($nbDay-1).' days');
		$sunday->modify('+'.(7-$nbDay).' days');
		return array('firstday'=>$monday->format('m/d/Y'),'lastday'=>$sunday->format('m/d/Y'));
	}
	
	function getFromToDate()
	{
		$period = $this->input->post('period');
		if($period == 'week')
		{
			$date = date('m/d/Y'); // you can put any date you want
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
