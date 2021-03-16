<?php 

$Queues = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID);
foreach ($Queues as $key => $queue) { ?>


	<div class="tab-pane " id="Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>">
		<div class="col-md-12 col-xs-12 pd-0">

			<div class="text-left" style="display: inline-flex;"> 
				<?php  if(!empty($queue->IsFollowup)) {  ?>
					<button class="btn btn-facebook btn-xs followupfilter"> Total Follow Up : <span class="followupcount">0</span></button>
					<button class="btn btn-facebook btn-xs followupduetodayfilter"> Follow Up Due Today : <span class="followupduetodaycount">0</span></button>
					<button class="btn btn-facebook btn-xs followupduepastfilter"> Follow Up Past Due : <span class="followupduepastcount">0</span></button>
				<?php } ?>

				<?php  if(!empty($queue->IsDocsReceived)) {  ?>
					<button class="btn btn-success btn-xs SubQueues_DocsReceive_Enabled"> Docs Received : <span class="SubQueues_IsDocsReceivedcount">0</span></button>
				<?php } ?>
				
				<?php  if(!empty($queue->IsStatus)) {  ?>
					<button class="btn btn-success btn-xs SubQueues_Status_Enabled"> Status Approved : <span class="SubQueues_IsStatuscount">0</span></button>
				<?php } ?>
			</div>

			<div class="material-datatables" id="tblcontainerQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>">
				<table class="table table-striped display nowrap abortprocesstable fetch-notescountstable" id="tblQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>"  cellspacing="0" width="100%"  style="width:100%" data-IsFollowup="<?php echo $queue->IsFollowup; ?>" data-IsDocsReceived="<?php echo $queue->IsDocsReceived; ?>" data-IsStatus="<?php echo $queue->IsStatus; ?>">
					<thead>
						<tr>
							<?php if( !empty($QueueColumns) ) { ?>

								<?php foreach ($QueueColumns as $key => $queuecolumn) { ?>
									<?php 
									if ($queuecolumn->ColumnName == "SubQueueCategories" && !empty($queuecolumn->SubQueueCategoryUID) && $queuecolumn->SubQueueUID != $queue->QueueUID) {
										continue;
									}
									if ($this->Common_Model->CheckQueueColumnIsEnabled($queuecolumn->StaticQueueUIDs, $queuecolumn->QueueUIDs, '', $queue->QueueUID)) {
										continue;
									}
									?>
									<th class="<?php echo ($queuecolumn->NoSort == 1 && empty($queuecolumn->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queuecolumn->HeaderName; ?></th>
								<?php } ?>
								<?php  if(!empty($queue->IsDocsReceived)) {  ?>
									<th>Docs Received</th>
								<?php } ?>

								<?php  if(!empty($queue->IsStatus)) {  ?>
									<th width="10%">Status</th>
								<?php } ?>

								<!-- <th>SubQueue Aging</th> -->
								<th>Initiated By</th>
								
								<?php if ($WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping']) { ?>
									
									<th>Kickback</th>

								<?php } else { ?>

									<th>Reason</th>
									
								<?php } ?>
								
								<th>Initiated DateTime</th>

								<th class="no-sort">Actions</th>

							<?php } else { ?>

								<th>Order No</th>
								<th>Client</th>
								<th>Loan No</th>									
								<th>Loan Type</th>
								<th>Milestone</th>									
								<th>Current Status</th>
								<th>Aging</th>
								<th>Due DateTime</th>
								<th>LastModified DateTime</th>
								<th>Associate</th>
								<th>Remarks</th>
								<th>Initiated DateTime</th>
								<th class="no-sort">Actions</th>

							<?php } ?>

						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>

<?php } ?>