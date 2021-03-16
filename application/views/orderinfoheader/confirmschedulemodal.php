<div class="modal fade" id="ConfirmSchedule" tabindex="-1" role="dialog" aria-labelledby="ConfirmScheduleLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 450px!important;">
		<div class="modal-content">			
			<input type="hidden" value="0" id="confirm_schedule">
			<div class="modal-header" style="padding-top: 5px!important;">
				<h5 class="modal-title" style="border-bottom: 1px solid #ddd;font-size: 20px;text-align: left;font-weight: 400;"> Confirm Schedule Update </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="padding: 0 24px;">
				<div class="row">					
					<div class="col-md-12">
						<div class="mb-20">
							<div class="ScheduleWarning">

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="padding: 0 12px;">
					<button class="btn btn-success " id="schedulereconfirm" onclick="schedule_yes();" name="submit" > Yes </button>

					<button type="button" class="btn btn-danger btn-dismiss" data-dismiss="modal"> No </button>
				</div>
			</div>
		</div>
	</div>
</div>