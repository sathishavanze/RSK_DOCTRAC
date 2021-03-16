<!-- ORDER REVERSE POPUP CONTENT STARTS -->                

<div class="fulltopmodal modal fade" id="modal-OrderReverse" tabindex="-1" role="dialog" aria-labelledby="OrderReverseLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="<?php echo isset($Reverseform_ID) && !empty($Reverseform_ID) ? $Reverseform_ID : "frm_orderreverse"; ?>" action="#" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="OrderReverseLabel">Order Reverse</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="mb-20">
								<h5>Completed Workflows</h5>
								<div class="">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>SNo</th>
												<th>Workflow</th>
												<th>Assigned UserName</th>
												<th>Assigned DateTime</th>
												<th>Completed UserName</th>
												<th>Completed DateTime</th>
											</tr>
										</thead>
										<tbody>
											<?php $Assignment=[]; ?>
											<?php
											$tOrderAssignment = $this->Common_Model->OrderReverseWorkflow($OrderDetails->OrderUID);

											?>
											<?php foreach ($tOrderAssignment as $key => $value) { ?>
												<tr>
													<td><?php echo $key + 1; ?></td>
													<td><?php echo $value->WorkflowModuleName; ?></td>
													<td><?php echo $value->AssignedUserName; ?></td>
													<td><?php echo $value->AssignedDatetime; ?></td>
													<td><?php echo $value->CompletedUserName; ?></td>
													<td><?php echo $value->CompleteDateTime; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>

							<div class="col-md-12 mt-30">
								<label for="Customer" class="bmd-label-floating" style="position: relative;">Reverse Order<span class="mandatory"></span></label>
								<div class="form-group bmd-form-group">

									<?php 

									$tOrderAssignments = $this->Common_Model->OrderReverseWorkflowStatus($OrderDetails->OrderUID);
									?>
									<select class="form-control modal-select" name="StatusUID" id="ReverseStatusUID" style="width: 100%;height: 31px;padding: 0px;" >
										<option value="">Choose Workflow</option>

										<?php foreach ($tOrderAssignments as $key => $workflowstatus) { ?>
											<option value="<?php echo $workflowstatus->WorkflowModuleUID; ?>"><?php echo $workflowstatus->WorkflowModuleName; ?></option>
										<?php } ?>
									</select>


								</div>
							</div>

							<!-- <div class="col-md-12 mt-30" id="ReverseDependentWorkflowsDiv" style="display: none;">
								<label for="Customer" class="bmd-label-floating" style="position: relative;">Reverse Dependent Workflows</label>
								<div class="form-group bmd-form-group">

									<select class="form-control modal-select" name="ReverseDependentWorkflows" id="ReverseDependentWorkflows" multiple="true" >

									</select>


								</div>
							</div> -->

							<div class="col-md-12 mt-20" id="ReversedRemarksdiv" style="display: none;">
								<div class="form-group bmd-form-group">
									<label for="ReversedRemarks" class="bmd-label-floating">Remarks</label>
									<input type="text" class="form-control" id="ReversedRemarks" name="ReversedRemarks">
								</div> 
							</div>

							<div class="col-md-12 mt-20" id="ClearChecklistDiv" style="display: none;">
								<div class="form-check">
									<label class="form-check-label " style="color: teal">Clear Checklist Answer
										<input class="form-check-input" id="ClearChecklistData" type="checkbox" value="" name="ClearChecklistData">
										<span class="form-check-sign">
											<span class="check"></span>
										</span>
									</label>
								</div>
							</div>

							<div class="text-right  mt-20">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-twitter btnreverse" name="submit" type="submit" id="btnreverse"> Reverse </button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<!-- ORDER REVERSE POPUP CONTENT ENDS -->  