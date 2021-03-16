<?php 

$Queues = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID);

foreach ($Queues as $key => $queue) { ?>

	<li class="nav-item">
		<a class="nav-link exceptionqueue-navlink" data-toggle="tab" href="#Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>" data-QueueUID="<?php echo $queue->QueueUID; ?>" data-tableid="#tblQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>" data-workflowmodulename = "<?php echo $queue->WorkflowModuleName; ?>" role="tablist">
			<?php echo $queue->QueueName; ?>
			<span class="badge badge-pill badge-primary <?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName);?>" id="Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>-count" style="background-color: #fff;color: #000;"><?php echo $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders(["QueueUID"=>$queue->QueueUID], "count_all"); ?></span>
		</a>
	</li>

<?php }
?>
