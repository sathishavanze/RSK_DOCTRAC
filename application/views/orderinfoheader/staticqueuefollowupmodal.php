<div class="modal fade" id="ClearFollowupStaticQueue" tabindex="-1" role="dialog" aria-labelledby="ClearFollowupStaticQueueLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmclearStaticQueueFollowup" action="#" method="post" novalidate>
				<div class="modal-header">
					<h5 class="modal-title" id="ClearFollowupStaticQueueLabel">Clear Followup</h5>
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
									<select class="modal-select  form-control StaticQueueFollowupclearReason" name="Reason" id="StaticQueueFollowupclearReason" style="width: 100%;height: 31px;padding: 0px;" required="">
										<option value=""> Select Reason </option>

										<?php 
										foreach ($mReasons as $key => $value) { ?>
											<option value="<?php echo $value->ReasonUID; ?>" data-staticqueueuid="<?php echo $value->StaticQueueUID; ?>"><?php echo $value->ReasonName; ?></option>

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
					<button class="btn btn-pinterest btnclearStaticQueueFollowup" name="submit" type="submit" id="btnclearStaticQueueFollowup" value="clearFollowup"> Clear Followup</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>