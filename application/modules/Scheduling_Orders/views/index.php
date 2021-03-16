<style>
	.DTFC_RightBodyWrapper{
		z-index: 9;
	}

	.labelinfo{
		color: #3e3e3e;
		font-weight: 600;
	}

	.notification-right{
		position: absolute;
		top: 10px;
		border: 1px solid #FFF;
		right: 46px;
		font-size: 9px;
		background: #f44336;
		color: #FFFFFF;
		min-width: 20px;
		padding: 0px 5px;
		height: 20px;
		border-radius: 10px;
		text-align: center;
		line-height: 19px;
		vertical-align: middle;
		display: block;
	}
	
	/*overall excel export*/
	.nav-pills-rose.customtab{
		position:relative;
	}
	.excel-expo-btn{
		position:absolute;
		right:13px;
	}
	.excel-expo-btn i{
		font-size:15px;
		color:#0B781C;
		cursor: pointer;
		margin-top: 13px;
	}
</style>
<?php $WorkflowModuleUID = $this->config->item("Workflows")["Scheduling"]; ?>

<div class="card mt-40">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">
			<i class="icon-file-eye"></i>
		</div>
		<?php $this->load->view('common/completed_counter', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
		<div class="row">
			<div class="col-md-10">
				<h4 class="card-title">Scheduling Orders</h4>
			</div>
		</div>

	</div>
	<div class="card-body" id="filter-bar">
		<!-- GET NEXT ORDER INCLUDED -->
		<?php $this->load->view('GetNextOrder/get_next_order'); ?>
		<!-- GET NEXT ORDER INCLUDED -->
		
		<!-- Workflow Documents View -->
		<?php $this->load->view('common/Workflow_Documents_View'); ?>
		<!-- Workflow Documents View -->
		<!-- QUEUE STATUS REPORT -->
		<div class="pull-right"> 
			<a href="javascript:;" class="viewqueuereport" title="View Status Report"><i class="fa fa-list-alt"  aria-hidden="true"></i></a>&nbsp;&nbsp;
		</div>
		<!-- Common Search -->
		<?php $this->load->view('common/commonsearch'); ?>

		<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#orderslist" role="tablist">
					New Orders
					<span class="badge badge-pill badge-primary newordercount" id="" style="background-color: #fff;color: #000;"><?php echo $this->Scheduling_Orders_Model->count_all(); ?></span>

				</a>
			</li>

			<?php
			if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {
				?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#workinprogresslist" role="tablist">
						Assigned Orders
						<span class="badge badge-pill badge-primary assignordercount" id="" style="background-color: #fff;color: #000;"><?php echo $this->Scheduling_Orders_Model->inprogress_count_all(); ?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#myorderslist" role="tablist">
						My Orders
						<span class="badge badge-pill badge-primary myordercount" id="" style="background-color: #fff;color: #000;"><?php echo $this->Scheduling_Orders_Model->myorders_count_all(); ?></span>
					</a>
				</li>

				<!-- "Funding Conditions" Sub queue Processor attention and Title/Notary Attention should be part of Scheduling Queue -->
				<?php 
				$WorkflowModuleUID = $this->config->item("Workflows")["Scheduling"];
				$this->load->view('ExceptionQueue/ExceptionQueueList', ['WorkflowModuleUID'=>$WorkflowModuleUID]); 
				?>

				<?php 
				$FundingConditionsWorkflowModuleUID = $this->config->item('Workflows')['FundingConditions'];
				$FundingConditionsQueueUID = array();
				$FundingConditionsQueueUID[] = $this->config->item('FundingConditionsSubQueueIDs')['ProcessorAttention'];
				$FundingConditionsQueueUID[] = $this->config->item('FundingConditionsSubQueueIDs')['TitleNotaryAttention'];
				$FundingConditionsQueues = $this->Common_Model->getCustomerWorkflowQueues($FundingConditionsWorkflowModuleUID, $FundingConditionsQueueUID);

				foreach ($FundingConditionsQueues as $key => $queue) { ?>

					<li class="nav-item">
						<a class="nav-link exceptionqueue-navlink" data-toggle="tab" href="#Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>" data-QueueUID="<?php echo $queue->QueueUID; ?>" data-tableid="#tblQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>" data-workflowmodulename = "<?php echo $queue->WorkflowModuleName; ?>" role="tablist">
							<?php echo $queue->QueueName; ?>
							<span class="badge badge-pill badge-primary <?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName);?>" id="Queue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>-count" style="background-color: #fff;color: #000;"><?php echo $this->ExceptionQueue_Orders_model->_getExeceptionQueueOrders(["QueueUID"=>$queue->QueueUID], "count_all"); ?></span>
						</a>
					</li>

				<?php }
				?>

				<?php 
				if(!empty($IsParking)){?>
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#parkingorderslist" role="tablist">
						Parking Orders
						<span class="badge badge-pill badge-primary parkordercount" id="" style="background-color: #fff;color: #000;"><?php echo $this->Scheduling_Orders_Model->parkingorders_count_all(); ?></span>
					</a>
				</li>

			<?php } ?> 
				<!-- Completed Orders -->
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#completedorderslist" role="tablist">
						Completed Orders
						<span class="badge badge-pill badge-primary completeordercount" id="" style="background-color: #fff;color: #000;"><?php echo $this->Common_Model->completedordersBasedOnWorkflow_count_all(); ?></span>
					</a>
				</li>

				<!-- 3A confirmation -->
				<li class="nav-item">
					<a class="nav-link " data-toggle="tab" href="#ThreeAConfirmationOrdersList" role="tablist">
						3A confirmation
						<span class="badge badge-pill badge-primary ThreeAconfirmation" id="" style="background-color: #fff;color: #000;"><?php echo $this->Scheduling_Orders_Model->ThreeAConfirmationcount_all(); ?></span>
					</a>
				</li>
			<?php } ?>

			 <a class="excel-expo-btn" href="<?php echo base_url().'CommonController/WriteGlobalExcelSheet?controller='.$this->uri->segment(1) ?>"><i class="fa fa-file-excel-o globalexceldownload " title="Overall Queue Excel Export" aria-hidden="true" style=""></i></a>
			<a class="excel-exportN-btn" > <i class="fa fa-download globalexceldownloadNew" title="Overall Queue Excel Export" aria-hidden="true" style=""></i> </a>
		
		</ul>
		
		<?php $this->load->view('common/advancesearch'); ?>
		

		<div class="tab-content tab-space customtabpane">

			<!-- "Funding Conditions" Sub queue Processor attention and Title/Notary Attention should be part of Scheduling Queue -->

			<?php 

			$FundingConditionsQueueColumns = $this->Common_Model->getWorkflowQueuesColumns($FundingConditionsWorkflowModuleUID);

			foreach ($FundingConditionsQueues as $key => $queue) { ?>

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
						</div>

						<div class="material-datatables" id="tblcontainerQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>">
							<table class="table table-striped display nowrap abortprocesstable" id="tblQueue<?php echo preg_replace("/[^a-zA-Z0-9\']/", "", $queue->QueueName); ?>"  cellspacing="0" width="100%"  style="width:100%" data-IsFollowup="<?php echo $queue->IsFollowup; ?>" data-IsDocsReceived="<?php echo $queue->IsDocsReceived; ?>">
								<thead>
									<tr>
										<?php if( !empty($FundingConditionsQueueColumns) ) { ?>

											<?php foreach ($FundingConditionsQueueColumns as $key => $queuecolumn) { ?>
												<th class="<?php echo ($queuecolumn->NoSort == 1 && empty($queuecolumn->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queuecolumn->HeaderName; ?></th>
											<?php } ?>
											<?php  if(!empty($queue->IsDocsReceived)) {  ?>
												<th>Docs Received</th>
											<?php } ?>
											<th>Initiated By</th>
											<th>Reason</th>
											<th>Raised DateTime</th>

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
											<th>Raised DateTime</th>
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

			<?php 
				$viewdata['WorkflowModuleUID'] = $WorkflowModuleUID;
				$viewdata['IsParking'] = $IsParking;
				$viewdata['IsScheduling'] = true;
				$viewdata['NewOrdersTableName'] = "tblNewOrders";
				$viewdata['WorkinProgressTableName'] = "workingprogresstable";
				$viewdata['myordertablename'] = "myorderstable";
				$viewdata['ParkingOrdersTableName'] = "parkingorderstable";
				$viewdata['CompletedOrdersTableName'] = "completedorderstable";
				$viewdata['ThreeAConfirmationOrdersTableName'] = "ThreeAConfirmationOrdersTable";
				$viewdata['IsThreeAConfirmation'] = true;
			?>
			<?php $this->load->view('DynamicColumns/DynamicColumnsView', $viewdata); ?>			

		</div>

	</div>
</div>



<script type="text/javascript">
	
	var WorkflowModuleUID = '<?php echo $WorkflowModuleUID; ?>';
	var ModuleController = '<?php echo $this->uri->segment(1); ?>';
	var HardStopSchedule = '<?php echo $this->Common_Model->pluckSettingValue('HardStop'); ?>';
</script>

<!-- common queue -->
<?php $this->load->view('orderinfoheader/commonqueue'); ?>


