
<div  id="orderstablediv" style="display: none;">

	<div class="card-header card-header-icon card-header-rose">
		<div class="card-icon title-heading" style="margin-top:0px;"><i class="icon-file-check"></i><span id="orderlisttitle"></span>
		</div> 					
		<div class="text-right"> 
			<button style="display: none;" class="btn btn-default btn-xs btn-link excelorderlist" title="Excel Export" style="font-size: 13px;color:#0B781C;cursor: pointer;"><i class=" icon-file-excel"></i></button>
			<button class="btn btn-link btn-danger btn-xs orderclose" title="Close"><i class="icon-cross2
				"></i></button> 
			</div>
		</div>
		
		<div class="card-body">
			<div class="row"> 
				<div class="col-md-12 pd-0">
						<?php
						$QueueColumns = $this->Common_Model->getWorkflowQueuesColumns($WorkflowModule); 
						if(!empty($QueueColumns))
						{
						?>
						<div class="material-datatables" id="WorkinProgress_container">
						<table class="table table-striped display nowrap abortprocesstable" id="workingprogresstable"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									 <?php foreach ($QueueColumns as $key => $queuecolumn) { ?>
									<th class="<?php echo ($queuecolumn->NoSort == 1 && empty($queuecolumn->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queuecolumn->HeaderName; ?></th>
								<?php } ?>
								<!-- <th>Initiated By</th>
								<th>Reason</th>
								<th>Raised DateTime</th> -->

								</tr>
							</thead>
							<tbody id="append-data"></tbody>

						</table>
						</div>
						<div class="material-datatables" id="tblcontainerQueue">
						<table class="table table-striped display nowrap abortprocesstable" id="tblQueue"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>

								<?php foreach ($QueueColumns as $key => $queuecolumn) { ?>
									<th class="<?php echo ($queuecolumn->NoSort == 1 && empty($queuecolumn->SortColumnName)) ? "no-sort" : ""; ?>"><?php echo $queuecolumn->HeaderName; ?></th>
								<?php } ?>
								<?php  if(!empty($queue->IsDocsReceived)) {  ?>
									<th>Docs Received</th>
								<?php } ?>

								<?php  if(!empty($queue->IsStatus)) {  ?>
									<th width="10%">Status</th>
								<?php } ?>
								<th>Initiated By</th>
								<th>Reason</th>
								<th>Raised DateTime</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					<?php  
					} 
					else 
					{
					?>
						<div class="material-datatables" id="WorkinProgress_container">
						<table class="table table-striped display nowrap abortprocesstable" id="workingprogresstable"  cellspacing="0" width="100%"  style="width:100%">
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
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					</div>
					<div class="material-datatables" id="tblcontainerQueue">
						<table class="table table-striped display nowrap abortprocesstable" id="tblQueue"  cellspacing="0" width="100%"  style="width:100%">
						<thead>
							<tr>
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
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
				<?php }
				?>
				</div>
			</div>
		</div>
	</div>
	<div class="" style="display: none;">
		<input type="hidden" id="orderlist_orderuids">
		<input type="hidden" id="orderlist_workflowmoduleuid">
		<input type="hidden" id="OrderUID">
	</div>

	<div id="appendmodal" class=""  role="dialog">

	</div>

