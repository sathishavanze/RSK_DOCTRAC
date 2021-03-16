
<style type="text/css">
	
	.form-check label {
		cursor: pointer;
	}

	.MilestoneWidgetContainer {
		margin-top: -50px !important;
	}

	.SubQueueWidgets {
	    padding: 6px 14px !important;
	    background-color: #437ab7;
	}
</style>
<?php
$controller = $this->uri->segment(1);
$WorkflowModuleUID = $this->config->item('workflowcontroller')[$controller];
$WorkflowModuleName = $controller;
$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModuleUID);
$AssignToUsers = $this->Common_Model->GetAssignUserOption();
?>

<!-- <a class="excel-expo-btn" href="<?php echo base_url().'CommonController/WriteGlobalExcelSheetNew?controller='.$this->uri->segment(1) ?>"></a> -->
<!-- Widget -->
<div class="text-left" style="display: inline-flex;"> 
	<?php  
	if(in_array($WorkflowModuleUID, $this->config->item('PendingCompletedWidgetEnabledWorkflows'))) {  ?>
		<button class="btn btn-pending-info btn-xs prescreenpendingfilter" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['PreScreen'] ?>"> Prescreen - Pending : <span class="PrescreenPendingCount">0</span></button>
		<button class="btn btn-pending-info btn-xs hoipendingfilter" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['HOI'] ?>"> HOI - Pending : <span class="HOIPendingCount">0</span></button>
		<button class="btn btn-pending-info btn-xs titleteampendingfilter" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['TitleTeam'] ?>"> Title - Pending : <span class="TitleTeamPendingCount">0</span></button>
		<button class="btn btn-pending-info btn-xs fhavacaseteampendingfilter" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['FHAVACaseTeam'] ?>"> FHA/VA - Pending : <span class="FHAVACaseTeamPendingCount">0</span></button>
		<button class="btn btn-pending-info btn-xs workuppendingfilter" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['Workup'] ?>"> Workup - Pending : <span class="WorkupPendingCount">0</span></button>

		<button class="btn btn-success btn-xs completedorderfilter"> Completed - Orders : <span class="CompletedOrdersCount">0</span></button>
	<?php } ?>

	<!-- TAT missed widgets more than 4 hours of new orders -->
	<?php  
	if($WorkflowModuleUID == $this->config->item('Workflows')['DocsOut']) {  ?>
		<button class="btn DocsOutTATMissedNewOrdersFilter SubQueueWidgets" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['DocsOut'] ?>"> TAT Missed : <span class="DocsOutTATMissedNewOrdersCount">0</span></button>
		<button class="btn DocsOutTATMissedPendingFromUWFilter SubQueueWidgets" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['DocsOut'] ?>"> TAT Missed : <span class="DocsOutTATMissedPendingFromUWCount">0</span></button>
		<button class="btn DocsOutDocsCheckedConditionPendingFollowupPastDueFilter SubQueueWidgets" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['DocsOut'] ?>"> Followup Past Due : <span class="DocsOutDocsCheckedConditionPendingFollowupPastDueCount">0</span></button>
		<button class="btn DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedFilter SubQueueWidgets" data-workflowmoduleuid="<?php echo $this->config->item('Workflows')['DocsOut'] ?>"> Yet To Be Reviewed : <span class="DocsOutDocsCheckedConditionPendingFollowupYetToBeReviewedCount">0</span></button>
	<?php } ?>
	<!-- TAT missed widgets more than 4 hours of new orders end -->

</div>
<!--  Widget -->

<?php
if ( !empty($QueueColumns) ) { 
?>
<style type="text/css">
	#AssignUser .modal-content {
		width: max-content !important;
    	margin: 1px -70px 0px !important;
	}
	.ReAssignUsers, .AssignUsers {
		    margin: 0px 0px 0px 3px !important;
    		width: 30px;
	}
	table.dataTable.display tbody>tr.odd.selected, table.dataTable.display tbody>tr.odd>.selected, table.dataTable.stripe tbody>tr.odd.selected, table.dataTable.stripe tbody>tr.odd>.selected{
		background: #a5a9ad;
	}
</style>

	<!-- DYNAMIC COLUMNS -->	
	<div class="tab-pane" id="orderslist">
		<div class="col-md-12 col-xs-12 pd-0">
			<div class="material-datatables" id="tblNewTitleTeamOrder_container">
				<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $NewOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
					<thead>
						<tr>
							<?php foreach ($QueueColumns as $key => $queue) { ?>
								<?php 
								if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $NewOrdersTableName) {
									continue;
								}
								if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $NewOrdersTableName)) {
									continue;
								}
								?>
								<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
							<?php } ?>
							
							<th class="no-sort">Actions</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) {  ?>

		<div class="tab-pane" id="workinprogresslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="tblTitleTeamWorkinProgress_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $WorkinProgressTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<?php foreach ($QueueColumns as $key => $queue) { ?>
									<?php 
									if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $WorkinProgressTableName) {
										continue;
									} 
									if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $WorkinProgressTableName)) {
										continue;
									}
									?>
									<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
								<?php } ?>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="tab-pane " id="myorderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="tblTitleTeamMyOrders_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $myordertablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<?php foreach ($QueueColumns as $key => $queue) { ?>
									<?php 
									if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $myordertablename) {
										continue;
									} 
									if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $myordertablename)) {
										continue;
									}
									?>
									<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
								<?php } ?>

								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<?php $this->load->view('ExceptionQueue/ExceptionQueueView', ['WorkflowModuleUID'=>$WorkflowModuleUID, 'QueueColumns'=>$QueueColumns]); ?>


		<?php 
		if(!empty($IsParking)){?>
			<div class="tab-pane " id="parkingorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="tblParkingWelcomeCall">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ParkingOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $ParkingOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $ParkingOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th>Parking RaisedBy</th>
									<th>Parking Remarks</th>
									<th>Parking ReminderOn</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

				<?php if (isset($KickBackOrdersTableName)) { ?>
						<div class="tab-pane" id="KickBacklist">
							<div class="col-md-12 col-xs-12 pd-0">
								<div class="material-datatables" id="KickBackorderslisttblPreScreen_parent">
									<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $KickBackOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
										<thead>
											<tr>
												<?php foreach ($QueueColumns as $key => $queue) { ?>
													<?php 
													if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $KickBackOrdersTableName) {
														continue;
													} 
													if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $KickBackOrdersTableName)) {
														continue;
													}
													?>
													<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
												<?php } ?>
												<th>Kickback Associate</th>
												<th>Kickback Date</th>
												<th>Remarks</th>
												<!-- KickBack SubQueue Aging Enabled Workflows -->
												<?php if (in_array($WorkflowModuleUID, $this->config->item('KickbackAgingEnabledworkflows'))) { ?>
													<th>KickBack SubQueue Aging</th>
												<?php } ?>												
												<th class="no-sort">Actions</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
		<?php } ?>

		<!--
			* Function to Expired Order Dynamic Queue Columns
			* @throws no exception
			* @author Sathis Kannan<sathish.kannan@avanzegroup.com>
			* @since July 22 2020
		-->

		<?php if (isset($ExpiredOrdersTableName)) { ?>
			<div class="tab-pane" id="Expiredorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="ExpiredorderslisttblPreScreen_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ExpiredOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $ExpiredOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $ExpiredOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>

									<!-- Expiry SubQueue Aging Column Enabled Workflows -->
									<?php if (in_array($WorkflowModuleUID, $this->config->item('ExpiryAgingColumnEnabledworkflows'))) { ?>
										<th>Expiry SubQueue Aging</th>
									<?php } ?>	
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<!-- HOI Rework Orders Table -->
		<?php if (isset($HOIReworkOrdersTableName)) { ?>
			<div class="tab-pane" id="HOIReworkOrderList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $HOIReworkOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $HOIReworkOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $HOIReworkOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th>Raised By</th>
									<th>Raised DateTime</th>
									<th>Raised Remarks</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>
		<!-- HOI Rework Orders Table end -->
	
		<?php if (isset($CompletedOrdersTableName)) { ?>
			<div class="tab-pane " id="completedorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="completedorderslisttblPreScreen_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CompletedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $CompletedOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $CompletedOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th>Completed By</th>
									<th>Completed Date and Time</th>								
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if (isset($hoiwaitingorderstablename)) { ?>
			<div class="tab-pane " id="hoiwaitingorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoiwaitingorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoiwaitingorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $hoiwaitingorderstablename) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $hoiwaitingorderstablename)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<!-- <th>Completed By</th>
									<th>Completed Date and Time</th>	 -->							
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if (isset($hoiresponsedorderstablename)) { ?>
			<div class="tab-pane " id="hoiresponsedorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoiresponsedorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoiresponsedorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $hoiresponsedorderstablename) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $hoiresponsedorderstablename)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<!-- <th>Completed By</th>
									<th>Completed Date and Time</th>	 -->							
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if (isset($hoireceivedorderstablename)) { ?>
			<div class="tab-pane " id="hoireceivedorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoireceivedorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoireceivedorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $hoireceivedorderstablename) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $hoireceivedorderstablename)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<!-- <th>Completed By</th>
									<th>Completed Date and Time</th> -->								
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if (isset($hoiexceptionorderstablename)) { ?>
			<div class="tab-pane " id="hoiexceptionorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoiexceptionorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoiexceptionorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $hoiexceptionorderstablename) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $hoiexceptionorderstablename)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<!-- <th>Completed By</th>
									<th>Completed Date and Time</th>	 -->							
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if(isset($FHANewOrdersTableName) && !empty($FHANewOrdersTableName))
		{ ?>
			<div class="tab-pane " id="FHAorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $FHANewOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $FHANewOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $FHANewOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>



		<!--  DOCS OUT tables-->
	<?php if(isset($DocsCheckedOrdersTableName) && !empty($DocsCheckedOrdersTableName))
		{ ?>
			<div class="tab-pane " id="docsorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $DocsCheckedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $DocsCheckedOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $DocsCheckedOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

	<?php if(isset($QueueClearedOrdersTableName) && !empty($QueueClearedOrdersTableName))
		{ ?>
			<div class="tab-pane " id="queueorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $QueueClearedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $QueueClearedOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $QueueClearedOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

	<?php if(isset($PendingDocsOrdersTableName) && !empty($PendingDocsOrdersTableName))
		{ ?>
			<div class="tab-pane " id="pendingdocsoderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $PendingDocsOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $PendingDocsOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $PendingDocsOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>


		<?php if(isset($PendingUWOrdersTableName) && !empty($PendingUWOrdersTableName))
		{ ?>
			<div class="tab-pane " id="pendinguworderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $PendingUWOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $PendingUWOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $PendingUWOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>


		<?php if(isset($SubmittedforDocCheckTableName) && !empty($SubmittedforDocCheckTableName))
		{ ?>
			<div class="tab-pane " id="SubmittedforDocCheck_OrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $SubmittedforDocCheckTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $SubmittedforDocCheckTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $SubmittedforDocCheckTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if(isset($NonWorkableTableName) && !empty($NonWorkableTableName))
		{ ?>
			<div class="tab-pane " id="NonWorkable_OrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $NonWorkableTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $NonWorkableTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $NonWorkableTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if(isset($WorkupReworkTableName) && !empty($WorkupReworkTableName))
		{ ?>
			<div class="tab-pane " id="WorkupRework_OrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $WorkupReworkTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $WorkupReworkTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $WorkupReworkTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if (isset($ReWorkOrdersTableName) && !empty($ReWorkOrdersTableName)) { ?>
			<div class="tab-pane" id="ReWorkOrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ReWorkOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $ReWorkOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $ReWorkOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if (isset($ReWorkPendingOrdersTableName) && !empty($ReWorkPendingOrdersTableName)) { ?>
			<div class="tab-pane" id="ReWorkPendingOrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ReWorkPendingOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $ReWorkPendingOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $ReWorkPendingOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if (isset($ThreeAConfirmationOrdersTableName) && !empty($ThreeAConfirmationOrdersTableName)) { ?>
			<div class="tab-pane" id="ThreeAConfirmationOrdersList">

				<!-- Widget -->
				<div class="text-left MilestoneWidgetContainer"> 
					<?php 
					// Milestones enabled workflows
					$MilestoneWidgetEnabledWorkflows = $this->config->item('MilestoneWidgetEnabledWorkflows')[$WorkflowModuleUID];

					if (!empty($MilestoneWidgetEnabledWorkflows)) {
						
						foreach ($MilestoneWidgetEnabledWorkflows as $MilestoneName => $MilestoneUID) { ?>
							<button class="btn btn-pending-info btn-sm btn-success MilestoneWidget MilestoneWidgetPendingFilter" data-milestoneuid="<?php echo $MilestoneUID; ?>" title="<?php echo $MilestoneName; ?> Pending Email"> <?php echo $MilestoneName; ?> : <span class="MilestoneWidgetPendingCounts_<?php echo $MilestoneName; ?>">0</span></button>

						<?php } ?>

						<button class="btn btn-pending-info btn-sm btn-success MilestoneWidget MilestoneWidgetTotalFilter" title="Total Pending Email"> Total : <span class="MilestoneWidgetTotalCounts">0</span></button>
					<?php } ?>					
				</div>
				<!--  Widget -->
				
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ThreeAConfirmationOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $ThreeAConfirmationOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $ThreeAConfirmationOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th>Email</th>
									<th>Phone</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<!-- CD Section -->	
		<?php 
		$CD_QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($this->config->item('Workflows')['CD']);
		if (isset($CDInflowOrdersTableName)) { ?>
			<div class="tab-pane " id="CDInflowOrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CDInflowOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($CD_QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $CDInflowOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $CDInflowOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>
		
		<?php if (isset($CDPendingOrdersTableName)) { ?>
			<div class="tab-pane " id="CDPendingOrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CDPendingOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($CD_QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $CDPendingOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $CDPendingOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>
		
		<?php if (isset($CDCompletedOrdersTableName)) { ?>
			<div class="tab-pane " id="CDCompletedOrdersList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CDCompletedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($CD_QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $CDCompletedOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $CDCompletedOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th>Completed By</th>
									<th>Completed Date and Time</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>
		<!-- CD Section End -->

		<?php if (isset($ExpiredCompleteOrdersTableName)) { ?>
			<div class="tab-pane" id="ExpiredCompleteorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="ExpiredCompleteorderslisttblPreScreen_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ExpiredCompleteOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<?php foreach ($QueueColumns as $key => $queue) { ?>
										<?php 
										if ($queue->ColumnName == "SubQueueCategories" && $queue->SubQueueSection != $ExpiredCompleteOrdersTableName) {
											continue;
										} 
										if ($this->Common_Model->CheckQueueColumnIsEnabled($queue->StaticQueueUIDs, $queue->QueueUIDs, $ExpiredCompleteOrdersTableName)) {
											continue;
										}
										?>
										<th class="<?php echo ($queue->NoSort == 1 && empty($queue->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queue->HeaderName; ?></th>
									<?php } ?>
									<th>Expiry completed DateTime</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

	<?php }	?>

<?php } else { ?>
	<!-- STATIC COLUMNS -->	
	<div class="tab-pane active" id="orderslist">
		<div class="col-md-12 col-xs-12 pd-0">
			<div class="material-datatables" id="tblNewTitleTeamOrder_container">
				<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $NewOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
					<thead>
						<tr>
							<th>Order No</th>
							<th class="no-sort">Client</th>
							<th>Loan No</th>									
							<th>Loan Type</th>
							<th>Milestone</th>									
							<th>Current Status</th>
							<th>State</th>
							<th>Aging</th>
							<th>Due DateTime</th>
							<th>LastModified DateTime</th>
							
							<th class="no-sort">Actions</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php if (in_array($this->RoleType, $this->config->item('Internal Roles'))) { ?>
		<div class="tab-pane" id="workinprogresslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="tblTitleTeamWorkinProgress_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $WorkinProgressTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client</th>
								<th>Loan No</th>									
								<th>Loan Type</th>
								<th>Milestone</th>									
								<th>Current Status</th>
								<th>State</th>
								<th>Assigned To</th>
								<th>Assigned DateTime</th>
								<th>Aging</th>
								<th>Due DateTime</th>
								<th>LastModified DateTime</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="tab-pane " id="myorderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="tblTitleTeamMyOrders_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $myordertablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client</th>
								<th>Loan No</th>									
								<th>Loan Type</th>
								<th>Milestone</th>									
								<th>Current Status</th>
								<th>State</th>
								<th>Aging</th>
								<th>Due DateTime</th>
								<th>LastModified DateTime</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<?php $this->load->view('ExceptionQueue/ExceptionQueueView', ['WorkflowModuleUID'=>$WorkflowModuleUID, 'QueueColumns'=> []]); ?>


		<?php 
		if(!empty($IsParking)){?>
			<div class="tab-pane " id="parkingorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="tblParkingWelcomeCall">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ParkingOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client</th>
									<th>Loan No</th>									
									<th>Loan Type</th>
									<th>Milestone</th>									
									<th>Current Status</th>
									<th>State</th>
									<th>Raised By</th>
									<th>Remarks</th>
									<th>Remainder On</th>
									<th>Aging</th>
									<th>Due DateTime</th>
									<th>LastModified DateTime</th>
									<!-- <th>Parking RaisedBy</th>
									<th>Parking ReminderOn</th>
									<th>Parking Remarks</th> -->

									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<div class="tab-pane" id="KickBacklist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="KickBackorderslisttblPreScreen_parent">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $KickBackOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th class="no-sort">Client</th>
								<th>Loan No</th>									
								<th>Loan Type</th>
								<th>Milestone</th>									
								<th>Current Status</th>
								<th>State</th>
								<th>Aging</th>
								<th>Due DateTime</th>
								<th>LastModified DateTime</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!--
	 * Function to Expired Order Static Queue Columns
	 * @throws no exception
	 * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
	 * @since July 22 2020
	  -->
		<div class="tab-pane" id="Expiredorderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="ExpiredorderslisttblPreScreen_parent">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ExpiredOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th class="no-sort">Client</th>
								<th>Loan No</th>									
								<th>Loan Type</th>
								<th>Milestone</th>									
								<th>Current Status</th>
								<th>State</th>
								<th>Aging</th>
								<th>Due DateTime</th>
								<th>LastModified DateTime</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	
		<!-- HOI Rework Orders Table -->
		<?php if (isset($HOIReworkOrdersTableName)) { ?>
			<div class="tab-pane " id="HOIReworkOrderList">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $HOIReworkOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client </th>
									<th>Loan No</th>
									<th>Loan Type</th>
									<th>Milestone</th>
									<th>Current Status</th>
									<th>State</th>
									<th>LastModified DateTime</th>
									<th>Completed By</th>
									<th>Completed Date and Time</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>
		<!-- HOI Rework Orders Table end -->

		<?php if (isset($CompletedOrdersTableName)) { ?>
			<div class="tab-pane " id="completedorderslist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="completedorderslisttblPreScreen_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CompletedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client </th>
									<th>Loan No</th>
									<th>Loan Type</th>
									<th>Milestone</th>
									<th>Current Status</th>
									<th>State</th>
									<th>LastModified DateTime</th>
									<th>Completed By</th>
									<th>Completed Date and Time</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if (isset($hoiwaitingorderstablename)) { ?>
			<div class="tab-pane " id="hoiwaitingorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoiwaitingorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoiwaitingorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client </th>
									<th>Loan No</th>
									<th>Loan Type</th>
									<th>Milestone</th>
									<th>Current Status</th>
									<th>State</th>
									<th>LastModified DateTime</th>
									<th>Completed By</th>
									<th>Completed Date and Time</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if (isset($hoiresponsedorderstablename)) { ?>
			<div class="tab-pane " id="hoiresponsedorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoiresponsedorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoiresponsedorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client </th>
									<th>Loan No</th>
									<th>Loan Type</th>
									<th>Milestone</th>
									<th>Current Status</th>
									<th>State</th>
									<th>LastModified DateTime</th>
									<th>Completed By</th>
									<th>Completed Date and Time</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if (isset($hoireceivedorderstablename)) { ?>
			<div class="tab-pane " id="hoireceivedorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoireceivedorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoireceivedorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client </th>
									<th>Loan No</th>
									<th>Loan Type</th>
									<th>Milestone</th>
									<th>Current Status</th>
									<th>State</th>
									<th>LastModified DateTime</th>
									<th>Completed By</th>
									<th>Completed Date and Time</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		<?php if (isset($hoiexceptionorderstablename)) { ?>
			<div class="tab-pane " id="hoiexceptionorderstablelist">
				<div class="col-md-12 col-xs-12 pd-0">
					<div class="material-datatables" id="hoiexceptionorderstablelisttbl_parent">
						<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $hoiexceptionorderstablename; ?>"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th>Order No</th>
									<th>Client </th>
									<th>Loan No</th>
									<th>Loan Type</th>
									<th>Milestone</th>
									<th>Current Status</th>
									<th>State</th>
									<th>LastModified DateTime</th>
									<th>Completed By</th>
									<th>Completed Date and Time</th>
									<th class="no-sort">Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		<?php } ?>

		
	<?php }	?>

	<?php if(isset($FHANewOrdersTableName) && !empty($FHANewOrdersTableName))
	{ ?>
		<div class="tab-pane " id="docsorderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $FHANewOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if(isset($DocsCheckedOrdersTableName) && !empty($DocsCheckedOrdersTableName))
	{ ?>
		<div class="tab-pane " id="docsorderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $DocsCheckedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>


	<?php if(isset($QueueClearedOrdersTableName) && !empty($QueueClearedOrdersTableName))
	{ ?>
		<div class="tab-pane " id="queueorderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $QueueClearedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>


	<?php if(isset($PendingDocsOrdersTableName) && !empty($PendingDocsOrdersTableName))
	{ ?>
		<div class="tab-pane " id="pendingdocsoderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $PendingDocsOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if(isset($PendingUWOrdersTableName) && !empty($PendingUWOrdersTableName))
	{ ?>
		<div class="tab-pane " id="pendinguworderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="FHAtblNewTitleTeamOrder_container">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $PendingUWOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if(isset($SubmittedforDocCheckTableName) && !empty($SubmittedforDocCheckTableName))
	{ ?>
		<div class="tab-pane " id="SubmittedforDocCheck_OrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $SubmittedforDocCheckTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if(isset($NonWorkableTableName) && !empty($NonWorkableTableName))
	{ ?>
		<div class="tab-pane " id="NonWorkable_OrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $NonWorkableTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if(isset($WorkupReworkTableName) && !empty($WorkupReworkTableName))
	{ ?>
		<div class="tab-pane " id="WorkupRework_OrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $WorkupReworkTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if (isset($ReWorkOrdersTableName) && !empty($ReWorkOrdersTableName)) { ?>
		<div class="tab-pane" id="ReWorkOrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ReWorkOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if (isset($ReWorkPendingOrdersTableName) && !empty($ReWorkPendingOrdersTableName)) { ?>
		<div class="tab-pane" id="ReWorkPendingOrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ReWorkPendingOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if (isset($ThreeAConfirmationOrdersTableName) && !empty($ThreeAConfirmationOrdersTableName)) { ?>
		<div class="tab-pane" id="ThreeAConfirmationOrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ThreeAConfirmationOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<!-- CD Section -->	
	<?php if (isset($CDInflowOrdersTableName)) { ?>
		<div class="tab-pane " id="CDInflowOrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CDInflowOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>	
	<?php } ?>
	
	<?php if (isset($CDPendingOrdersTableName)) { ?>
		<div class="tab-pane " id="CDPendingOrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CDPendingOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>	
	<?php } ?>
	
	<?php if (isset($CDCompletedOrdersTableName)) { ?>
		<div class="tab-pane " id="CDCompletedOrdersList">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $CDCompletedOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th>Client </th>
								<th>Loan No</th>
								<th>Loan Type</th>
								<th>Milestone</th>
								<th>Current Status</th>
								<th>State</th>
								<th>LastModified DateTime</th>
								<th>Completed By</th>
								<th>Completed Date and Time</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>	
	<?php } ?>
	<!-- CD Section End -->

	<?php if (isset($ExpiredCompleteOrdersTableName)) { ?>
		<div class="tab-pane" id="ExpiredCompleteorderslist">
			<div class="col-md-12 col-xs-12 pd-0">
				<div class="material-datatables" id="ExpiredCompleteorderslisttblPreScreen_parent">
					<table class="table table-striped display nowrap abortprocesstable" id="<?php echo $ExpiredCompleteOrdersTableName; ?>"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
								<th>Order No</th>
								<th class="no-sort">Client</th>
								<th>Loan No</th>									
								<th>Loan Type</th>
								<th>Milestone</th>									
								<th>Current Status</th>
								<th>State</th>
								<th>Aging</th>
								<th>Due DateTime</th>
								<th>LastModified DateTime</th>
								<th class="no-sort">Actions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

<?php }

 ?>

<div class="modal fade" id="SelectQueues" tabindex="-1" role="dialog" aria-labelledby="SelectQueuesLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 450px!important;">
		<div class="modal-content">
			<form class="form-horizontal" id="formSelectQueues" action="#" method="post" style="margin-bottom: 10px;">
				<input type="hidden" name="OrderUID" value="" id="OrderUID">
				<input type="hidden" name="WorkflowModuleUID" value="<?php echo $WorkflowModuleUID; ?>" id="WorkflowModuleUID">
				<input type="hidden" name="WorkflowModuleName" value="<?php echo $WorkflowModuleName; ?>" id="WorkflowModuleName">
				<div class="modal-header" style="padding-top: 5px!important;">
					<h5 class="modal-title" style="border-bottom: 1px solid #ddd;font-size: 20px;text-align: left;font-weight: 400;"> Export </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="padding: 0 24px;">
					<div class="row">
						<div class="col-md-12">
							<div class="SelectOptions">
								<div class="form-check">									
									<label for="AllQueues" >
										<input class="form-check-input" type="checkbox"  type="checkbox" id="AllQueues" name="AllQueues" value=""> 
				                        <span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									 Select All </label>
								</div>
								<?php if(isset($IsFHANeworder)) { ?>
								<div class="form-check">
									<label for="VA New Orders" >
										<input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="VA New Orders" value="VA New Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									VA New Orders</label>
								</div>
								<?php } else { ?>
									<div class="form-check">
									<label for="New Orders" ><input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" value="New Orders" id="New Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									New Orders</label>
								</div>
								<?php } ?>
								<div class="form-check">
									<label for="Assigned Orders" >
										<input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" value="Assigned Orders" id="Assigned Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Assigned Orders</label>
								</div>
								<div class="form-check">
									<label for="My Orders" ><input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" value="My Orders" id="My Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									My Orders</label>
								</div>
												

								<?php 
								$Queues = $this->Common_Model->getCustomerWorkflowQueues($WorkflowModuleUID);

								foreach ($Queues as $key => $queue) { ?>
									<div class="form-check">
										<label for=" <?php echo $queue->QueueName; ?>" >
											<input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" value="<?php echo $queue->QueueName; ?>" id=" <?php echo $queue->QueueName; ?>">
											<span class="form-check-sign">
					                          <span class="check"></span>
					                        </span>
										 <?php echo $queue->QueueName; ?> </label>
									</div>
								<?php } ?>
								<?php if(!empty($IsParking)){?>
									<div class="form-check">
										<label for="Parking Orders" ><input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" value="Parking Orders" id="Parking Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
										Parking Orders</label>
									</div>									
								<?php } ?> 
								<?php if(!empty($IsKickBack)){ ?>
									<div class="form-check">
										<label for="KickBack Orders" ><input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" value="KickBack Orders" id="KickBack Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
										KickBack Orders</label>
									</div>
									
								<?php } ?>
								<div class="form-check">
									<label for="Completed Orders" ><input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="Completed Orders" value="Completed Orders">
									<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Completed Orders</label>
								</div>	

								<?php if(isset($WorkupReworkTableName) && !empty($WorkupReworkTableName)) { ?>
								<div class="form-check">
									<label for="WorkupRework" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="WorkupRework" value="WorkupRework">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Rework </label>
								</div>
								<?php } ?>
								
								<?php if(!empty($IsExpiryOrders)){?>
								<div class="form-check">
									<label for="Expiry Orders" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="Expiry Orders" value="Expiry Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Expiry Orders</label>
								</div>

								<div class="form-check">
									<label for="Expiry Complete Orders" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="Expiry Complete Orders" value="Expiry Complete Orders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Expiry Orders Complete</label>
								</div>
								
								<?php } ?>

								<?php if(isset($IsHOIReworkOrders)){?>
								<!-- <div class="form-check">
									<label for="HOIReworkOrders" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="HOIReworkOrders" value="HOIReworkOrders">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									HOI Rework Orders</label>
								</div> -->								
								<?php } ?>

								<?php if(isset($IsDocsOutOrder)){ ?>
								<div class="form-check">
									<label for="DocsCheckedConditionsPending" > <input type="checkbox" class="CheckOptions form-check-input" id="DocsCheckedConditionsPending" name="queueName[]" value="Docs Checked Conditions Pending">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Docs Checked Conditions Pending</label>
								</div>								
								<!-- <div class="form-check">
									<label for="QueueclearedbyFunding" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="QueueclearedbyFunding" value="QueueclearedbyFunding">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Queue cleared by Funding</label>
								</div> -->
								<!-- <div class="form-check">
									<label for="PendingDocsRelease" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="PendingDocsRelease" value="PendingDocsRelease">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Pending Docs Release</label>
								</div> -->
								<div class="form-check">
									<label for="PendingfromUW" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="PendingfromUW" value="Pending from UW">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Pending from UW </label>
								</div>
								<div class="form-check">
									<label for="SubmittedforDocCheck" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="SubmittedforDocCheck" value="SubmittedforDocCheck">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Submitted for Doc Check </label>
								</div>
								<div class="form-check">
									<label for="NonWorkable" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="NonWorkable" value="NonWorkable">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Non Workable </label>
								</div>
							<?php } 

							if(isset($IsReWork)){ ?>
								<div class="form-check">
									<label for="Re-Work" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="Re-Work" value="Re-Work">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Re-Work </label>
								</div>
							<?php }

							if(isset($IsReWorkPending)){ ?>
								<div class="form-check">
									<label for="Re-WorkPending" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="Re-WorkPending" value="Re-WorkPending">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									Re-Work Pending </label>
								</div>
							<?php }


							if(isset($IsFHANeworder)) { ?>
								<div class="form-check">
									<label for="FHA New Orders" > <input type="checkbox" class="CheckOptions form-check-input" id="FHA New Orders" name="queueName[]" value="FHA New Orders ">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									FHA New Orders </label>
								</div>
							<?php }

							if(isset($IsThreeAConfirmation)){ ?>
								<div class="form-check">
									<label for="ThreeAConfirmation" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="ThreeAConfirmation" value="ThreeAConfirmation">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									3A Confirmation </label>
								</div>
							<?php }

							// CD section
							if(isset($IsCDsection)){ ?>
								<div class="form-check">
									<label for="CDInflow" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="CDInflow" value="CD Inflow">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									CD Inflow </label>
								</div>
								
								<div class="form-check">
									<label for="CDPending" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="CDPending" value="CD Pending">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									CD Pending </label>
								</div>
								
								<div class="form-check">
									<label for="CDCompleted" > <input type="checkbox" class="CheckOptions form-check-input" name="queueName[]" id="CDCompleted" value="CD Completed">
										<span class="form-check-sign">
				                          <span class="check"></span>
				                        </span>
									CD Completed </label>
								</div>
							<?php }
							// CD section End

							?>
							</div>							
						</div>
					</div>
				</div>
				<div class="modal-footer" style="padding: 0 12px;">
					<button class="btn btn-success Reopen_workflow_submit btnSelectQueues" name="submit" type="submit" id="btnSelectQueues" value="btnSelectQueues"><i class="fa fa-download" aria-hidden="true"></i> Export </button>

					<button type="button" class="btn btn-danger btn-dismiss" data-dismiss="modal">Cancel</button>
				</div>
			</form> 
		</div>
	</div>
</div>
<div class="modal fade" id="AssignUser" tabindex="-1" role="dialog" aria-labelledby="AssignUserLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 450px!important;">
		<div class="modal-content">
			<form class="form-horizontal" id="formAssignUser" action="#" method="post" style="margin-bottom: 10px;">
				<input type="hidden" name="OrderUID" value="" id="OrderUID">
				<input type="hidden" name="WorkflowModuleUID" value="<?php echo $WorkflowModuleUID; ?>" id="WorkflowModuleUID">
				<input type="hidden" name="WorkflowModuleName" value="<?php echo $WorkflowModuleName; ?>" id="WorkflowModuleName">
				<div class="modal-header" style="padding-top: 5px!important;">
					<h5 class="modal-title" style="border-bottom: 1px solid #ddd;font-size: 20px;text-align: left;font-weight: 400;">Assign User</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="padding: 0 24px;">
					<div class="row">
						<div class="col-md-12">
							<div class="mb-20">
								<!-- <h5>Assigned User</h5> -->
								<div class="">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>SNo</th>
												<th>Loan Number</th>	
												<th>Workflow</th>
												<th>Assigned By</th>
												<th>Assigned To</th>
												<th>Assigned DateTime</th>
											</tr>
										</thead>
										<tbody id="AssignmentHistoryBody">
											<tr id="assignTr"><td>1</td>
											<td class="tdOrderUID">  </td>
											<td class="tdWorkflowModuleName"> <?php echo $WorkflowModuleName; ?></td>
											<td class="AssignedBy"> - </td>
											<td class="Assigned"> - </td>
											<td class="AssignedDateTime"> - </td>
											</tr>
										</tbody>
									</table>
								</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="mb-20">
							<div>
								<?php echo $AssignToUsers; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="padding: 0 12px;">
					<button class="btn btn-success Reopen_workflow_submit btnAssignUser" name="submit" type="submit" id="btnAssignUser" value="btnAssignUser"><i class="icon-rotate-cw2 pr-10" aria-hidden="true"></i> Assign</button>

					<button type="button" class="btn btn-danger btn-dismiss" data-dismiss="modal">Cancel</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){		
		
		$(document).off('click', '.btnSelectQueues').on('click', '.btnSelectQueues', function(e){
			e.preventDefault();
			var segment = '<?php echo $WorkflowModuleName;?>';

			 button = $(this);
		      button_val = $(this).val();
		      button_text = $(this).html();

			$.ajax({
				url: 'CommonController/WriteSelectiveGlobalExcelSheet',
				xhrFields: {
		          responseType: 'blob',
		        }, 
				data: $('#formSelectQueues').serialize(),
				beforeSend: function() {
		        	button.prop("disabled", true);
			        button.html('<i class="fa fa-spin fa-spinner"></i> Loading ...');
			        button.val('<i class="fa fa-spin fa-spinner"></i> Saving...');
		        },
		        success: function(data) {
	              var filename = segment+'.xlsx';
	              if (typeof window.chrome !== 'undefined') {
	                //Chrome version
	                var link = document.createElement('a');
	                link.href = window.URL.createObjectURL(data);
	                link.download = filename;
	                link.click();
	              } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
	                //IE version
	                var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
	                window.navigator.msSaveBlob(blob, filename);
	              } else {
	                //Firefox version
	                var file = new File([data], filename, { type: 'application/octet-stream' });
	                window.open(URL.createObjectURL(file));
	              }
	               $('.CheckOptions').prop("checked", false);
		           $('#SelectQueues').modal('hide');


		           button.html(button_text);
				   button.val(button_val);
				   button.prop('disabled',false);
	            },
	            error: function (jqXHR, textStatus, errorThrown) {

	              console.log(jqXHR);


	            },
	            failure: function (jqXHR, textStatus, errorThrown) {

	              console.log(errorThrown);

	            },
			});
		});		
	});
	$(document).ready(function(){
		$('.CheckOptions').on('click',function(){ 

	        if(this.checked){
	          this.checked = true;    
	        }else{
	          this.checked = false;
	        }
	        if($('.CheckOptions:checked').length == $('.CheckOptions').length){
	        	$('#AllQueues').prop("checked", true);
	        }else{
	        	$('#AllQueues').prop("checked", false);	        	
	        }
	      });		
		$(document).off('click','.globalexceldownloadNew').on('click','.globalexceldownloadNew',function(e){
			e.preventDefault();
			$('#AllQueues').prop('checked', true);
			$('.CheckOptions').prop("checked", true);
			$('#SelectQueues').modal('show');
		});
		
		$('#AllQueues').on('change', function() {
			if($(this).is(':checked')){
				$('.CheckOptions').prop("checked", true);
			}else {
				$('.CheckOptions').prop("checked", false);
			}
		});
	});
</script>