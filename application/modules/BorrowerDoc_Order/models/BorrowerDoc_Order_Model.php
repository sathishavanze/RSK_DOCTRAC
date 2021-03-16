<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class BorrowerDoc_Order_Model extends MY_Model {

	function __construct()
	{ 
		parent::__construct();
		$this->loggedid = $this->session->userdata('UserUID');
		$this->UserName = $this->session->userdata('UserName');
		$this->RoleUID = $this->session->userdata('RoleUID');    
	}

	function total_count()
	{
		$total_count = 0;
		if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {

				$Queues = $this->Common_Model->get_borrowerdynamicworkflow_queues();

				foreach ($Queues as $key => $queue) {

					$total_count = $total_count + $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders(["QueueUID"=>$queue->QueueUID], "count_all");
					
				}
			}
		return $total_count;
	}

  
}?>
