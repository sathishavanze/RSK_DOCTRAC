<div class="modal fade" id="FollowupQueue" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmRaiseFollowup" action="#" method="post" novalidate="">
				<div class="modal-header">
					<h5 class="modal-title" id="FollowupQueueLabel">Initiate Followup</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">

							<div class="col-md-12">
								<div class="form-group bmd-form-group is-filled">
									<label class="label-control">Remind<span class="mandatory"></span></label>
									<input type="text" class="form-control dateraideparking" value="" id="FollowupRemainder" name="FollowupRemainder" required="">
									<span class="material-input"></span>
									<span class="material-input"></span>
								</div>
							</div>

							<div class="row col-md-12">

								<div class="col-sm-6">
									<div class="">
										<label class="label-control">Reason</label>
										<select class="modal-select  form-control FollowupraiseReason" name="Reason" id="FollowupraiseReason" required="">
											<option value=""> Select Reason </option>

											<?php 
											foreach ($mReasons as $key => $value) { ?>
												<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

												<?php

											}
											?>
										</select>
									</div>
								</div>

								<div class="col-sm-6">
									<div class="form-group bmd-form-group">
										<label class="label-control">Remarks</label>
										<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" required=""></textarea>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnraiseFollowup" name="submit" type="submit" id="btnraiseFollowup" value="raiseFollowup"> Initiate Followup</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="ClearFollowupQueue" tabindex="-1" role="dialog" aria-labelledby="ClearFollowupQueueLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmclearFollowup" action="#" method="post" novalidate>
				<div class="modal-header">
					<h5 class="modal-title" id="ClearFollowupQueueLabel">Clear Followup</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">

						<div class="row col-md-12">

							<div class="col-sm-6">
								<div class="">
									<label class="label-control">Reason</label>
									<select class="modal-select  form-control FollowupclearReason" name="Reason" id="FollowupclearReason" style="width: 100%;height: 31px;padding: 0px;" required="">
										<option value=""> Select Reason </option>

										<?php 
										foreach ($mReasons as $key => $value) { ?>
											<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

											<?php

										}
										?>
									</select>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group bmd-form-group">
									<label class="label-control">Remarks</label>
									<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" required=""></textarea>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnclearFollowup" name="submit" type="submit" id="btnclearFollowup" value="clearFollowup"> Clear Followup</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>